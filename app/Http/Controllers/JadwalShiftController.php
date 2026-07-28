<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\JadwalShift;
use App\Models\Pegawai;
use App\Models\StatusShift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class JadwalShiftController extends Controller
{
    public function index(Request $request, $shift)
    {
        $bulan = $request->get('bulan', now()->format('Y-m'));
        $q = $request->get('q');

        $year = substr($bulan, 0, 4);
        $month = substr($bulan, 5, 2);

        // Get number of days in selected month
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        // Find assigned pegawais for this shift and month
        $assignedPegawaiIds = JadwalShift::where('shift', $shift)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->pluck('pegawai_id')
            ->unique()
            ->toArray();
        $pegawais = Pegawai::with('unit')
            ->where('status_aktif', 'aktif')
            ->whereIn('id', $assignedPegawaiIds)
            ->when($q, function ($query) use ($q) {
                $query->where('nama', 'like', "%{$q}%");
            })
            ->get();

        // Get all entries indexed
        $entries = JadwalShift::with('statusShift')
            ->where('shift', $shift)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->get()
            ->groupBy(['pegawai_id', function ($item) {
                return $item->tanggal->toDateString();
            }]);

        // Get all active employees for selection in the modal
        $allPegawais = Pegawai::where('status_aktif', 'aktif')
            ->orderBy('nama')
            ->get();

        $statusShifts = StatusShift::orderBy('nama')->get();

        return view('jadwal-shift.index', compact(
            'shift',
            'bulan',
            'q',
            'daysInMonth',
            'year',
            'month',
            'pegawais',
            'entries',
            'allPegawais',
            'statusShifts'
        ));
    }

    public function updateCell(Request $request)
    {
        $data = $request->validate([
            'pegawai_id' => ['required', 'exists:pegawais,id'],
            'tanggal' => ['required', 'date'],
            'shift' => ['required', 'in:1,2,3'],
            'stasiun_tv' => ['nullable', 'string', 'max:100'],
            'status_shift_id' => ['nullable', 'exists:status_shifts,id'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $entry = JadwalShift::updateOrCreate(
            [
                'pegawai_id' => $data['pegawai_id'],
                'tanggal' => $data['tanggal'],
                'shift' => $data['shift']
            ],
            [
                'stasiun_tv' => $data['stasiun_tv'],
                'status_shift_id' => $data['status_shift_id'],
                'keterangan' => $data['keterangan']
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal shift berhasil diperbarui.',
            'data' => $entry->load('statusShift')
        ]);
    }

    public function tambahPegawai(Request $request)
    {
        $data = $request->validate([
            'pegawai_id' => ['required', 'exists:pegawais,id'],
            'tanggal' => ['required', 'date'],
            'shift' => ['required', 'in:1,2,3'],
            'bulan' => ['nullable', 'date_format:Y-m'],
        ]);

        // Create an empty entry on this date to register the pegawai in the calendar
        JadwalShift::updateOrCreate(
            [
                'pegawai_id' => $data['pegawai_id'],
                'tanggal' => $data['tanggal'],
                'shift' => $data['shift']
            ],
            [
                'stasiun_tv' => null,
                'status_shift_id' => null,
                'keterangan' => 'Ditambahkan secara manual ke shift'
            ]
        );

        // Log audit
        $pegawai = Pegawai::find($data['pegawai_id']);
        AuditLog::catat('menambah pegawai ke jadwal shift', 'JadwalShift', null, $pegawai->nama);

        $bulan = $request->input('bulan', \Carbon\Carbon::parse($data['tanggal'])->format('Y-m'));

        return redirect()->route('absensi.shift.index', ['shift' => $data['shift'], 'bulan' => $bulan])
            ->with('status', 'Pegawai berhasil ditambahkan ke jadwal shift.');
    }

    public function importForm()
    {
        return view('jadwal-shift.import');
    }

    public function importParse(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xls,xlsx'],
            'shift' => ['required', 'in:1,2,3'],
            'bulan' => ['required', 'date_format:Y-m'],
        ]);

        $file = $request->file('file');
        $shift = $request->input('shift');
        $bulanStr = $request->input('bulan');

        $year = substr($bulanStr, 0, 4);
        $month = substr($bulanStr, 5, 2);

        $monthsIndo = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $targetSheetName = $monthsIndo[$month] . ' ' . $year;

        // Load using PhpSpreadsheet directly to support styling/colors
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Error membaca file Excel: ' . $e->getMessage()]);
        }

        $sheetNames = $spreadsheet->getSheetNames();
        $sheetIndex = -1;
        foreach ($sheetNames as $idx => $name) {
            if (strtolower(trim($name)) === strtolower($targetSheetName)) {
                $sheetIndex = $idx;
                break;
            }
        }

        if ($sheetIndex === -1) {
            if (count($sheetNames) === 1) {
                $sheetIndex = 0;
            } else {
                return back()->withErrors(['file' => "Sheet dengan nama '{$targetSheetName}' tidak ditemukan di file Excel."]);
            }
        }

        $worksheet = $spreadsheet->getSheet($sheetIndex);
        
        // Construct 0-indexed matrix $rows
        $rows = [];
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

        for ($r = 1; $r <= $highestRow; $r++) {
            $row = [];
            for ($c = 1; $c <= $highestColumnIndex; $c++) {
                $cell = $worksheet->getCellByColumnAndRow($c, $r);
                $row[] = $cell->getValue();
            }
            $rows[] = $row;
        }

        $row3 = $rows[2] ?? [];
        $row4 = $rows[3] ?? [];

        $blocks = [];
        foreach ($row3 as $colIdx => $val) {
            if ($val === 'No') {
                $blocks[] = $colIdx;
            }
        }

        if (empty($blocks)) {
            return back()->withErrors(['file' => 'Format file Excel tidak sesuai (header "No" tidak ditemukan pada baris 3).']);
        }

        // Collect all default stasiun TVs to build the stasiun TV set
        $stasiunTvSet = [];
        foreach ($blocks as $startCol) {
            $stasiunCol = $startCol + 3;
            for ($r = 4; $r < count($rows); $r++) {
                $noVal = $rows[$r][$startCol] ?? null;
                if ($noVal === null || !is_numeric(trim($noVal))) {
                    continue;
                }
                $stasiunVal = trim($rows[$r][$stasiunCol] ?? '');
                if (!empty($stasiunVal)) {
                    $stasiunTvSet[strtolower($stasiunVal)] = $stasiunVal;
                }
            }
        }

        // Parse data
        $parsedData = [];
        $currentMonthYear = null;
        $uniqueParsedNames = [];
        $uniqueStatusCodes = [];
        $statusColors = [];
        $coloredCellCount = 0;
        $totalDateCellsParsed = 0;

        foreach ($blocks as $index => $startCol) {
            $nameCol = $startCol + 2;
            $stasiunCol = $startCol + 3;
            $nextBlockStart = $blocks[$index + 1] ?? count($row3);

            // Determine dates
            $dateCols = [];
            for ($c = $startCol + 4; $c < $nextBlockStart; $c++) {
                $mVal = $row3[$c] ?? '';
                if (!empty(trim($mVal))) {
                    $currentMonthYear = trim($mVal);
                }
                $dVal = $row4[$c] ?? '';
                if ($dVal !== null && $dVal !== '') {
                    $dateCols[$c] = [
                        'month_year' => $currentMonthYear,
                        'date' => (int)$dVal
                    ];
                }
            }

            // Read employees
            for ($r = 4; $r < count($rows); $r++) {
                $noVal = $rows[$r][$startCol] ?? null;
                if ($noVal === null || !is_numeric(trim($noVal))) {
                    continue;
                }
                $name = trim($rows[$r][$nameCol] ?? '');
                $defaultStasiun = trim($rows[$r][$stasiunCol] ?? '');
                if (empty($name)) {
                    continue;
                }

                $uniqueParsedNames[$name] = $name;

                foreach ($dateCols as $colIdx => $info) {
                    if (strtolower(trim($info['month_year'])) !== strtolower($targetSheetName)) {
                        continue;
                    }

                    $totalDateCellsParsed++;

                    $cell = $worksheet->getCellByColumnAndRow($colIdx + 1, $r + 1);
                    $isGrey = $this->isGreyCell($cell);
                    $fillColor = $this->getCellFillColor($cell);

                    $rawVal = trim($rows[$r][$colIdx] ?? '');

                    if ($isGrey) {
                        $cellVal = 'L';
                        $cellColor = $fillColor ?? '#a6a6a6';
                        $isColored = true;
                    } else {
                        $cellVal = $rawVal;
                        $cellColor = $fillColor;
                        $isColored = !empty($fillColor);
                    }

                    if ($isColored) {
                        $coloredCellCount++;
                    }

                    // Determine if status code based on cell highlight color
                    $isStatus = false;
                    $statusCode = null;
                    if ($isColored && (!empty($cellVal) || $isGrey)) {
                        $isStatus = true;
                        $statusCode = strtoupper($cellVal);
                        $uniqueStatusCodes[$statusCode] = $statusCode;
                        if (!isset($statusColors[$statusCode]) && $cellColor) {
                            $statusColors[$statusCode] = $cellColor;
                        }
                    }

                    $parsedData[] = [
                        'nama' => $name,
                        'default_stasiun' => $defaultStasiun,
                        'tanggal' => "{$year}-{$month}-" . sprintf('%02d', $info['date']),
                        'cell_value' => $cellVal,
                        'is_status' => $isStatus,
                        'status_code' => $statusCode,
                        'cell_color' => $cellColor,
                    ];
                }
            }
        }

        // Fallback: If 0 cell colors detected (e.g. unstyled/corrupt file), fallback to text matching
        $colorDetectionFailed = ($coloredCellCount === 0 && $totalDateCellsParsed > 0);

        if ($colorDetectionFailed) {
            foreach ($parsedData as &$data) {
                $cellVal = $data['cell_value'];
                if (!empty($cellVal) && strtolower($cellVal) !== 'masuk' && !isset($stasiunTvSet[strtolower($cellVal)])) {
                    $data['is_status'] = true;
                    $data['status_code'] = strtoupper($cellVal);
                    $uniqueStatusCodes[$data['status_code']] = $data['status_code'];
                }
            }
            unset($data);
        }

        // Store to session
        $sessionData = [
            'shift' => $shift,
            'bulan' => $bulanStr,
            'parsed_data' => $parsedData,
            'unique_names' => array_values($uniqueParsedNames),
            'unique_status_codes' => array_values($uniqueStatusCodes),
            'status_colors' => $statusColors,
            'color_detection_failed' => $colorDetectionFailed,
        ];
        session(['import_shift_data' => $sessionData]);

        return redirect()->route('absensi.shift.import-preview');
    }

    public function importPreview()
    {
        $sessionData = session('import_shift_data');
        if (!$sessionData) {
            return redirect()->route('absensi.shift.import-form')->withErrors(['file' => 'Session data import kedaluwarsa. Silakan upload ulang.']);
        }

        $shift = $sessionData['shift'];
        $bulan = $sessionData['bulan'];
        $uniqueNames = $sessionData['unique_names'];
        $uniqueStatusCodes = $sessionData['unique_status_codes'];
        $parsedData = $sessionData['parsed_data'];

        $statusColors = $sessionData['status_colors'] ?? [];
        $colorDetectionFailed = $sessionData['color_detection_failed'] ?? false;

        [$year, $month] = explode('-', $bulan);

        // Auto-match names
        $pegawais = Pegawai::select('id', 'nama', 'nip')->get();
        $nameMappings = [];
        // Quick lookup: parsed_name => pegawai_id (for collision detection)
        $nameToIdPreview = [];

        foreach ($uniqueNames as $name) {
            $candidate = $this->findCandidate($name, $pegawais);
            $nameMappings[] = [
                'parsed_name'        => $name,
                'matched_pegawai_id' => $candidate ? $candidate->id : null,
                'matched_nama'       => $candidate ? $candidate->nama : null,
                'matched_nip'        => $candidate ? $candidate->nip : null,
            ];
            if ($candidate) {
                $nameToIdPreview[$name] = $candidate->id;
            }
        }

        // Build collision map: which (pegawai_id, tanggal) already exist in DB for this shift+bulan
        // Key format: "{pegawai_id}_{tanggal}"
        $existingKeys = JadwalShift::where('shift', $shift)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->pluck('tanggal', \DB::raw('CONCAT(pegawai_id, "_", tanggal)'))
            ->keys()
            ->flip()
            ->toArray();

        // Build preview rows (for summary display — only rows that have a matched pegawai)
        $previewRows = [];
        $overrideCount = 0;
        $newCount = 0;
        foreach ($parsedData as $row) {
            $pegawaiId = $nameToIdPreview[$row['nama']] ?? null;
            if (!$pegawaiId) {
                continue;
            }
            $key = "{$pegawaiId}_{$row['tanggal']}";
            $isOverride = isset($existingKeys[$key]);
            if ($isOverride) {
                $overrideCount++;
            } else {
                $newCount++;
            }
            $previewRows[] = [
                'nama'          => $row['nama'],
                'tanggal'       => $row['tanggal'],
                'cell_value'    => $row['cell_value'],
                'is_status'     => $row['is_status'],
                'status_code'   => $row['status_code'],
                'is_override'   => $isOverride,
            ];
        }

        // Auto-match status codes
        $statusShifts = StatusShift::all();
        $statusMappings = [];
        foreach ($uniqueStatusCodes as $code) {
            $matched = $statusShifts->where('kode', $code)->first();
            $proposedColor = $statusColors[$code] ?? '#fca5a5';

            $statusMappings[] = [
                'code'              => $code,
                'matched_status_id' => $matched ? $matched->id : null,
                'matched_nama'      => $matched ? $matched->nama : null,
                'proposed_color'    => $proposedColor,
            ];
        }

        $allPegawais = Pegawai::where('status_aktif', 'aktif')->orderBy('nama')->get();
        $dbStatusShifts = StatusShift::orderBy('nama')->get();

        return view('jadwal-shift.preview', compact(
            'shift',
            'bulan',
            'nameMappings',
            'statusMappings',
            'allPegawais',
            'dbStatusShifts',
            'colorDetectionFailed',
            'previewRows',
            'overrideCount',
            'newCount'
        ));
    }

    public function importCommit(Request $request)
    {
        $sessionData = session('import_shift_data');
        if (!$sessionData) {
            return redirect()->route('absensi.shift.import-form')->withErrors(['file' => 'Session data import kedaluwarsa. Silakan upload ulang.']);
        }

        $shift = $sessionData['shift'];
        $parsedData = $sessionData['parsed_data'];

        // Get user mappings input
        $nameMapInput = $request->input('name_mapping', []); // parsed_name => pegawai_id (or 'new')
        $newNips = $request->input('new_nip', []); // parsed_name => NIP

        $statusMapInput = $request->input('status_mapping', []); // code => status_shift_id (or 'new')
        $newStatusNames = $request->input('new_status_nama', []); // code => Nama
        $newStatusColors = $request->input('new_status_warna', []); // code => Warna

        // 1. Process new status creation
        $statusMappingTable = [];
        foreach ($statusMapInput as $code => $statusId) {
            if ($statusId === 'new') {
                $newStatus = StatusShift::create([
                    'kode' => $code,
                    'nama' => $newStatusNames[$code] ?? $code,
                    'warna' => $newStatusColors[$code] ?? '#e5e7eb',
                ]);
                $statusMappingTable[$code] = $newStatus->id;
            } else {
                $statusMappingTable[$code] = $statusId;
            }
        }

        // 2. Process new employee creation & mappings table
        $employeeMappingTable = [];
        foreach ($nameMapInput as $parsedName => $pegawaiId) {
            if ($pegawaiId === 'new') {
                $nip = $newNips[$parsedName] ?? null;
                if (!$nip) {
                    return back()->withErrors(["nip_{$parsedName}" => 'NIP wajib diisi untuk membuat pegawai baru.']);
                }

                // Create new pegawai
                $newPegawai = Pegawai::create([
                    'nip' => $nip,
                    'nama' => $parsedName,
                    'status_aktif' => 'aktif',
                    'status_kepegawaian' => 'Non-ASN',
                ]);
                $employeeMappingTable[$parsedName] = $newPegawai->id;
            } else {
                $employeeMappingTable[$parsedName] = $pegawaiId;
            }
        }

        // 3. Commit parsed data
        \DB::beginTransaction();
        try {
            $updatedPegawais = [];
            foreach ($parsedData as $data) {
                $pegawaiId = $employeeMappingTable[$data['nama']] ?? null;
                if (!$pegawaiId) {
                    continue; // Skip if no mapping
                }

                if (!in_array($pegawaiId, $updatedPegawais)) {
                    $pegawai = Pegawai::find($pegawaiId);
                    if ($pegawai && !empty($data['default_stasiun'])) {
                        $pegawai->update(['stasiun_tv' => $data['default_stasiun']]);
                    }
                    $updatedPegawais[] = $pegawaiId;
                }

                $stasiunTv = null;
                $statusShiftId = null;

                if ($data['is_status']) {
                    $statusShiftId = $statusMappingTable[$data['status_code']] ?? null;
                } elseif (!empty($data['cell_value']) && strtolower($data['cell_value']) !== 'masuk') {
                    // Cell contains specific stasiun TV name
                    $stasiunTv = $data['cell_value'];
                } else {
                    // Normal shift, use default stasiun TV
                    $stasiunTv = !empty($data['default_stasiun']) ? $data['default_stasiun'] : null;
                }

                JadwalShift::updateOrCreate(
                    [
                        'pegawai_id' => $pegawaiId,
                        'tanggal' => $data['tanggal'],
                        'shift' => $shift
                    ],
                    [
                        'stasiun_tv' => $stasiunTv,
                        'status_shift_id' => $statusShiftId,
                        'keterangan' => 'Diimport dari file Excel'
                    ]
                );
            }
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withErrors(['file' => 'Gagal menyimpan data ke database: ' . $e->getMessage()]);
        }

        // Log audit
        AuditLog::catat('mengimport jadwal shift dari excel', 'JadwalShift');

        // Clear session
        session()->forget('import_shift_data');

        return redirect()->route('absensi.shift.index', ['shift' => $shift, 'bulan' => $sessionData['bulan']])->with('status', 'Jadwal shift berhasil diimport.');
    }

    /**
     * Kembalikan jumlah entry jadwal_shifts untuk shift+bulan tertentu (dipakai AJAX modal konfirmasi).
     */
    public function hitungEntri(Request $request, $shift)
    {
        $bulan = $request->get('bulan', now()->format('Y-m'));
        $year  = substr($bulan, 0, 4);
        $month = substr($bulan, 5, 2);

        $jumlah = JadwalShift::where('shift', $shift)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->count();

        $pegawaiCount = JadwalShift::where('shift', $shift)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->distinct('pegawai_id')
            ->count('pegawai_id');

        return response()->json([
            'jumlah_entri'   => $jumlah,
            'jumlah_pegawai' => $pegawaiCount,
        ]);
    }

    /**
     * Hapus SEMUA entry jadwal_shifts untuk shift+bulan tertentu.
     * Hanya menghapus dari tabel jadwal_shifts — data pegawai TIDAK tersentuh.
     */
    public function hapusPeriode(Request $request, $shift)
    {
        $request->validate([
            'bulan' => ['required', 'date_format:Y-m'],
        ]);

        $bulan = $request->input('bulan');
        $year  = substr($bulan, 0, 4);
        $month = substr($bulan, 5, 2);

        // Pastikan shift valid
        abort_unless(in_array((string) $shift, ['1', '2', '3']), 404);

        // Hitung dulu sebelum hapus (untuk pesan sukses & audit log)
        $jumlah = JadwalShift::where('shift', $shift)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->count();

        // Hapus — HANYA jadwal_shifts, bukan pegawais
        JadwalShift::where('shift', $shift)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->delete();

        // Format nama bulan Indonesia untuk pesan
        $monthsIndo = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',    '04' => 'April',
            '05' => 'Mei',     '06' => 'Juni',      '07' => 'Juli',     '08' => 'Agustus',
            '09' => 'September','10' => 'Oktober',  '11' => 'November', '12' => 'Desember',
        ];
        $namaBulan = ($monthsIndo[$month] ?? $month) . ' ' . $year;

        AuditLog::catat(
            "menghapus {$jumlah} entry jadwal Shift {$shift} periode {$namaBulan}",
            'JadwalShift'
        );

        return redirect()
            ->route('absensi.shift.index', ['shift' => $shift, 'bulan' => $bulan])
            ->with('status', "Berhasil menghapus {$jumlah} entry jadwal Shift {$shift} periode {$namaBulan}.");
    }

    private function findCandidate($parsedName, $pegawais)
    {
        $parsedNameClean = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $parsedName));

        // 1. Exact clean match
        foreach ($pegawais as $p) {
            $pNameClean = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $p->nama));
            if ($parsedNameClean === $pNameClean) {
                return $p;
            }
        }

        // 2. Partial clean match
        foreach ($pegawais as $p) {
            $pNameClean = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $p->nama));
            if (str_contains($pNameClean, $parsedNameClean) || str_contains($parsedNameClean, $pNameClean)) {
                return $p;
            }
        }

        // 3. Levenshtein match (max distance 3)
        $bestMatch = null;
        $shortest = -1;
        foreach ($pegawais as $p) {
            $pNameClean = strtolower($p->nama);
            $lev = levenshtein(strtolower($parsedName), $pNameClean);
            if ($lev <= 3) {
                if ($shortest < 0 || $lev < $shortest) {
                    $bestMatch = $p;
                    $shortest = $lev;
                }
            }
        }        return $bestMatch;
    }

    private function isGreyCell($cell)
    {
        $fill = $cell->getStyle()->getFill();
        $fillType = strtolower(trim($fill->getFillType()));
        if ($fillType !== 'solid') {
            return false;
        }
        $argb = $fill->getStartColor()->getARGB();
        if (empty($argb) || strlen($argb) < 6) {
            return false;
        }
        $rgb = strtoupper(substr($argb, -6));
        
        $greys = ['808080', 'A6A6A6', 'C0C0C0', 'D9D9D9', 'E0E0E0', 'BFBFBF', '7F7F7F', '595959', 'D3D3D3', 'A9A9A9'];
        if (in_array($rgb, $greys)) {
            return true;
        }
        
        $r = hexdec(substr($rgb, 0, 2));
        $g = hexdec(substr($rgb, 2, 2));
        $b = hexdec(substr($rgb, 4, 2));
        if (abs($r - $g) <= 5 && abs($g - $b) <= 5 && $r < 235 && $r > 80) {
            return true;
        }
        
        return false;
    }

    private function getCellFillColor($cell): ?string
    {
        if (!$cell) {
            return null;
        }

        try {
            $fill = $cell->getStyle()->getFill();
            $fillType = strtolower(trim((string)$fill->getFillType()));

            if ($fillType === 'none' || empty($fillType)) {
                return null;
            }

            $color = $fill->getStartColor();
            if (!$color) {
                return null;
            }

            $rgb = $color->getRGB();
            if (empty($rgb)) {
                return null;
            }

            $rgb = strtoupper(trim($rgb));

            if (strlen($rgb) < 6) {
                return null;
            }

            $r = hexdec(substr($rgb, 0, 2));
            $g = hexdec(substr($rgb, 2, 2));
            $b = hexdec(substr($rgb, 4, 2));

            // Ignore pure white, black, or default
            if ($rgb === 'FFFFFF' || $rgb === '000000') {
                return null;
            }

            // Check if off-white / light grid tint (very high brightness & low saturation)
            $max = max($r, $g, $b);
            $min = min($r, $g, $b);
            if (($max - $min) <= 15 && $min > 220) {
                return null;
            }

            return '#' . $rgb;
        } catch (\Exception $e) {
            return null;
        }
    }
}
