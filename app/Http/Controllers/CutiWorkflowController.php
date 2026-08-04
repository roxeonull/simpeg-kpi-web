<?php

namespace App\Http\Controllers;

use App\Models\ApprovalWorkflow;
use App\Models\ApprovalWorkflowStep;
use App\Models\AuditLog;
use App\Models\Cuti;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CutiWorkflowController extends Controller
{
    public function index()
    {
        $workflows = ApprovalWorkflow::with(['unit', 'steps'])->get();
        $units = UnitKerja::orderBy('nama_unit')->get();

        return view('cuti.workflows', [
            'workflows' => $workflows,
            'units' => $units,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'unit_id' => ['required', 'exists:unit_kerjas,id', 'unique:approval_workflows,unit_id'],
            'steps' => ['required', 'array', 'min:1'],
            'steps.*' => ['required', 'in:atasan_langsung,hr_admin'],
        ], [
            'unit_id.unique' => 'Unit kerja ini sudah memiliki workflow approval aktif.',
            'steps.required' => 'Wajib menambahkan minimal 1 tahapan approval.',
            'steps.min' => 'Wajib menambahkan minimal 1 tahapan approval.',
        ]);

        $workflow = ApprovalWorkflow::create([
            'nama' => $data['nama'],
            'unit_id' => $data['unit_id'],
        ]);

        foreach ($data['steps'] as $index => $tipeStep) {
            $workflow->steps()->create([
                'urutan' => $index + 1,
                'tipe_step' => $tipeStep,
            ]);
        }

        AuditLog::catat('membuat workflow approval cuti', 'ApprovalWorkflow', $workflow->id, $workflow->nama);

        return back()->with('status', "Workflow approval '{$workflow->nama}' berhasil dibuat.");
    }

    public function update(Request $request, ApprovalWorkflow $workflow)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'unit_id' => ['required', 'exists:unit_kerjas,id', Rule::unique('approval_workflows', 'unit_id')->ignore($workflow->id)],
            'steps' => ['required', 'array', 'min:1'],
            'steps.*' => ['required', 'in:atasan_langsung,hr_admin'],
        ], [
            'unit_id.unique' => 'Unit kerja ini sudah memiliki workflow approval aktif.',
            'steps.required' => 'Wajib menambahkan minimal 1 tahapan approval.',
        ]);

        $workflow->update([
            'nama' => $data['nama'],
            'unit_id' => $data['unit_id'],
        ]);

        $workflow->steps()->delete();

        foreach ($data['steps'] as $index => $tipeStep) {
            $workflow->steps()->create([
                'urutan' => $index + 1,
                'tipe_step' => $tipeStep,
            ]);
        }

        AuditLog::catat('memperbarui workflow approval cuti', 'ApprovalWorkflow', $workflow->id, $workflow->nama);

        return back()->with('status', "Workflow approval '{$workflow->nama}' berhasil diperbarui.");
    }

    public function destroy(ApprovalWorkflow $workflow)
    {
        if ($workflow->unit_id) {
            $hasActiveCutis = Cuti::whereHas('pegawai', fn ($q) => $q->where('unit_id', $workflow->unit_id))
                ->whereIn('status', ['menunggu_atasan', 'menunggu_hr'])
                ->exists();

            if ($hasActiveCutis) {
                return back()->withErrors([
                    'error' => "Workflow '{$workflow->nama}' tidak dapat dihapus karena terdapat pengajuan cuti yang sedang berjalan di unit kerja ini.",
                ]);
            }
        }

        $nama = $workflow->nama;
        $workflow->delete();

        AuditLog::catat('menghapus workflow approval cuti', 'ApprovalWorkflow', $workflow->id, $nama);

        return back()->with('status', "Workflow approval '{$nama}' berhasil dihapus.");
    }
}
