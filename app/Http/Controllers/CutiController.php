<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Cuti;
use Illuminate\Http\Request;

class CutiController extends Controller
{
    public function index(Request $request)
    {
        $query = Cuti::with(['pegawai.unit', 'jenisCuti']);
        $user = $request->user();

        if ($user->role === 'atasan' && $user->pegawai) {
            $ids = $user->pegawai->anggotaTim()->pluck('id')->toArray();
            $query->whereIn('pegawai_id', $ids);
        }

        // Hitung total status aktual dari DB sebelum memfilter pencarian / jenis
        $countQuery = clone $query;
        $counts = [
            'semua' => (clone $countQuery)->count(),
            'menunggu' => (clone $countQuery)->whereIn('status', ['menunggu_atasan', 'menunggu_hr'])->count(),
            'disetujui' => (clone $countQuery)->where('status', 'disetujui')->count(),
            'ditolak' => (clone $countQuery)->where('status', 'ditolak')->count(),
        ];

        if ($status = $request->get('status')) {
            if ($status === 'menunggu') {
                $query->whereIn('status', ['menunggu_atasan', 'menunggu_hr']);
            } else {
                $query->where('status', $status);
            }
        }

        if ($jenis = $request->get('jenis_cuti')) {
            $jenisCutiObj = \App\Models\JenisCuti::find($jenis);
            $legacyKeys = [];
            if ($jenisCutiObj) {
                $nameLower = strtolower($jenisCutiObj->nama);
                if (str_contains($nameLower, 'sakit')) $legacyKeys[] = 'sakit';
                if (str_contains($nameLower, 'tahunan')) $legacyKeys[] = 'tahunan';
                if (str_contains($nameLower, 'bersalin') || str_contains($nameLower, 'melahirkan')) $legacyKeys[] = 'melahirkan';
                if (str_contains($nameLower, 'alasan penting') || str_contains($nameLower, 'lain')) $legacyKeys[] = 'lainnya';
            }
            $query->where(function ($q) use ($jenis, $legacyKeys) {
                $q->where('jenis_cuti_id', $jenis);
                if (!empty($legacyKeys)) {
                    $q->orWhereIn('jenis_cuti', $legacyKeys);
                } else {
                    $q->orWhere('jenis_cuti', $jenis);
                }
            });
        }

        if ($search = $request->get('q')) {
            $query->whereHas('pegawai', fn ($q) => $q->where('nama', 'like', "%{$search}%"));
        }

        $cutis = $query->latest('tanggal_mulai')->paginate(15)->withQueryString();
        $jenisCutis = \App\Models\JenisCuti::orderBy('nama')->get();

        return view('cuti.index', [
            'cutis' => $cutis,
            'counts' => $counts,
            'filters' => $request->only(['status', 'jenis_cuti', 'q']),
            'isAtasan' => $user->role === 'atasan',
            'jenisCutis' => $jenisCutis,
        ]);
    }

    public function show(Cuti $cuti)
    {
        $cuti->load(['pegawai.unit', 'atasanPemroses', 'hrPemroses', 'approvalSteps.pemrosesUser']);

        return view('cuti.show', ['cuti' => $cuti]);
    }

    public function approveStep(Request $request, Cuti $cuti)
    {
        abort_unless($cuti->canUserApproveActiveStep($request->user()), 403, 'Anda tidak berhak memproses persetujuan tahap ini.');

        $data = $request->validate(['catatan' => ['nullable', 'string']]);
        $activeStep = $cuti->activeStep();
        $cuti->prosesActiveStep($request->user(), 'disetujui', $data['catatan'] ?? null);

        $tahapLabel = $activeStep ? $activeStep->tipeStepLabel() : 'tahap ini';
        AuditLog::catat("menyetujui cuti ({$tahapLabel})", 'Cuti', $cuti->id, $cuti->pegawai->nama);

        return back()->with('status', "Pengajuan cuti berhasil disetujui pada tahap {$tahapLabel}.");
    }

    public function rejectStep(Request $request, Cuti $cuti)
    {
        abort_unless($cuti->canUserApproveActiveStep($request->user()), 403, 'Anda tidak berhak memproses persetujuan tahap ini.');

        $data = $request->validate(['catatan' => ['required', 'string']]);
        $activeStep = $cuti->activeStep();
        $cuti->prosesActiveStep($request->user(), 'ditolak', $data['catatan']);

        $tahapLabel = $activeStep ? $activeStep->tipeStepLabel() : 'tahap ini';
        AuditLog::catat("menolak cuti ({$tahapLabel})", 'Cuti', $cuti->id, $cuti->pegawai->nama);

        return back()->with('status', "Pengajuan cuti ditolak pada tahap {$tahapLabel}.");
    }

    public function approveAtasan(Request $request, Cuti $cuti)
    {
        return $this->approveStep($request, $cuti);
    }

    public function rejectAtasan(Request $request, Cuti $cuti)
    {
        return $this->rejectStep($request, $cuti);
    }

    public function approveHr(Request $request, Cuti $cuti)
    {
        return $this->approveStep($request, $cuti);
    }

    public function rejectHr(Request $request, Cuti $cuti)
    {
        return $this->rejectStep($request, $cuti);
    }

    public function create(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'atasan') {
            $pegawais = $user->pegawai ? $user->pegawai->anggotaTim()->where('status_aktif', 'aktif')->orderBy('nama')->get() : collect();
        } else {
            $pegawais = \App\Models\Pegawai::where('status_aktif', 'aktif')->orderBy('nama')->get();
        }

        $jenisCutis = \App\Models\JenisCuti::orderBy('nama')->get();

        return view('cuti.create', [
            'pegawais' => $pegawais,
            'jenisCutis' => $jenisCutis,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'pegawai_id' => ['required', 'exists:pegawais,id'],
            'jenis_cuti_id' => ['required', 'exists:jenis_cutis,id'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'alasan' => ['required', 'string'],
            'alamat_cuti' => ['nullable', 'string'],
            'lampiran' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        if ($user->role === 'atasan' && $user->pegawai) {
            $timIds = $user->pegawai->anggotaTim()->pluck('id')->toArray();
            abort_unless(in_array($data['pegawai_id'], $timIds), 403, 'Pegawai ini bukan anggota tim Anda.');
        }

        $start = \Carbon\Carbon::parse($data['tanggal_mulai']);
        $end = \Carbon\Carbon::parse($data['tanggal_selesai']);
        $jumlahHari = $start->diffInDays($end) + 1;

        // Legacy mapping for jenis_cuti string column
        $jenisCuti = \App\Models\JenisCuti::find($data['jenis_cuti_id']);
        $legacyJenis = 'lainnya';
        if ($jenisCuti->nama === 'Cuti Tahunan') $legacyJenis = 'tahunan';
        elseif ($jenisCuti->nama === 'Sakit/Cuti Sakit') $legacyJenis = 'sakit';
        elseif ($jenisCuti->nama === 'Cuti Bersalin Anak Ke-1 s.d 2') $legacyJenis = 'melahirkan';

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('cuti', 'public');
        }

        $cuti = Cuti::create([
            'pegawai_id' => $data['pegawai_id'],
            'jenis_cuti_id' => $data['jenis_cuti_id'],
            'jenis_cuti' => $legacyJenis,
            'tanggal_mulai' => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'],
            'jumlah_hari' => $jumlahHari,
            'alasan' => $data['alasan'],
            'alamat_cuti' => $data['alamat_cuti'] ?? null,
            'lampiran' => $lampiranPath,
            'status' => 'menunggu_atasan', // default status
        ]);

        // Audit log
        \App\Models\AuditLog::catat('membuat pengajuan cuti', 'Cuti', $cuti->id, $cuti->pegawai->nama ?? null);

        // Send FCM Notification to Atasan if exists
        $pegawaiPemohon = $cuti->pegawai;
        if ($pegawaiPemohon && $pegawaiPemohon->atasan && $pegawaiPemohon->atasan->user) {
            $jenisStr = ($cuti->jenisCuti && $cuti->jenisCuti->nama) ? $cuti->jenisCuti->nama : ucfirst($cuti->jenis_cuti);
            $tglStr = $cuti->tanggal_mulai->format('d M') . ' s.d. ' . $cuti->tanggal_selesai->format('d M Y');
            \App\Services\NotificationService::sendToUser(
                $pegawaiPemohon->atasan->user,
                'Pengajuan Cuti Tim Baru',
                "{$pegawaiPemohon->nama} mengajukan cuti {$jenisStr} ({$tglStr}) yang memerlukan persetujuan Anda.",
                ['type' => 'cuti', 'id' => (string) $cuti->id]
            );
        }

        return redirect()->route('cuti.index')
            ->with('status', 'Pengajuan cuti berhasil ditambahkan.');
    }

    private function authorizeAtasan(Request $request, Cuti $cuti): void
    {
        $user = $request->user();
        if ($user->role !== 'atasan') {
            return;
        }

        $timIds = $user->pegawai?->anggotaTim()->pluck('id')->toArray() ?? [];
        abort_unless(in_array($cuti->pegawai_id, $timIds, true), 403, 'Pengajuan ini bukan anggota tim Anda.');
    }
}
