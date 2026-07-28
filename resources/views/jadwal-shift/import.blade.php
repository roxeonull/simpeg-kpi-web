<x-app-layout title="Import Jadwal Shift">
    <div class="max-w-2xl mx-auto">
        <a href="{{ route('absensi.shift.index', ['shift' => request('shift', 1), 'bulan' => request('bulan', now()->format('Y-m'))]) }}" class="mb-4 inline-flex items-center gap-1.5 text-sm font-medium text-kpi-gray hover:text-kpi-red">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke kalender shift
        </a>

        <div class="card">
            <div class="border-b border-kpi-line pb-4 mb-5 dark:border-white/10">
                <h2 class="font-serif text-xl font-bold">Import Jadwal Shift dari Excel</h2>
                <p class="text-xs text-kpi-gray mt-1">Langkah 1: Upload file Excel (.xls atau .xlsx) berisi jadwal shift bulanan.</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 rounded-xl bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-800/30 p-4 text-sm text-rose-800 dark:text-rose-300">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('absensi.shift.import-parse') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs text-kpi-gray block mb-1">Target Shift Kerja</label>
                    <select name="shift" required class="input w-full">
                        <option value="1" {{ request('shift') == '1' ? 'selected' : '' }}>Shift 1 (06.00-14.00)</option>
                        <option value="2" {{ request('shift') == '2' ? 'selected' : '' }}>Shift 2 (14.00-22.00)</option>
                        <option value="3" {{ request('shift') == '3' ? 'selected' : '' }}>Shift 3 (22.00-06.00)</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs text-kpi-gray block mb-1">Target Bulan & Tahun</label>
                    <input type="month" name="bulan" value="{{ request('bulan', now()->format('Y-m')) }}" required class="input w-full">
                    <p class="text-[10px] text-kpi-gray mt-1">
                        Sistem akan memproses sheet dengan nama bulan bahasa Indonesia, contoh: <strong>Juli 2026</strong>.
                    </p>
                </div>

                <div>
                    <label class="text-xs text-kpi-gray block mb-1">Pilih File Excel (.xls / .xlsx)</label>
                    <input type="file" name="file" accept=".xls,.xlsx" required class="input w-full py-2">
                </div>

                <div class="flex justify-end gap-2 border-t border-kpi-line pt-4 dark:border-white/10 mt-6">
                    <button type="submit" class="btn-primary flex items-center gap-2">
                        Next: Preview & Map Pegawai
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
