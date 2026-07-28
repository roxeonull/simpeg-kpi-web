<x-app-layout title="Laporan">
    <p class="eyebrow">Ekspor Data</p>
    <h2 class="mb-6 mt-1 font-serif text-xl font-semibold">Laporan & Rekapitulasi</h2>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4">
        <div class="card card-hover">
            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-kpi-red-soft text-kpi-red-dark dark:bg-kpi-red/15 dark:text-white">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 100-8 4 4 0 000 8zm6 3.13a4 4 0 00-3-3.87"/></svg>
            </div>
            <h3 class="mb-1 font-semibold">Data Pegawai</h3>
            <p class="mb-5 text-sm text-kpi-gray">Ekspor seluruh data induk pegawai.</p>
            <div class="flex gap-3">
                <a href="{{ route('laporan.pegawai.excel') }}" class="btn-secondary flex-1">Excel</a>
                <a href="{{ route('laporan.pegawai.pdf') }}" class="btn-secondary flex-1">PDF</a>
            </div>
        </div>

        <div class="card card-hover">
            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="mb-1 font-semibold">Rekap Absensi</h3>
            <div class="flex flex-col gap-3" x-data="{ bulan: '{{ now()->format('Y-m') }}' }">
                <label class="label !mb-0">Pilih Bulan</label>
                <input type="month" x-model="bulan" class="input">
                <div class="flex gap-3">
                    <a :href="`{{ route('laporan.absensi.excel') }}?bulan=${bulan}`" class="btn-secondary flex-1">Excel</a>
                    <a :href="`{{ route('laporan.absensi.pdf') }}?bulan=${bulan}`" class="btn-secondary flex-1">PDF</a>
                </div>
            </div>
        </div>

        <div class="card card-hover">
            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-kpi-gold-soft text-kpi-gold dark:bg-kpi-gold/15">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="mb-1 font-semibold">Rekap Cuti</h3>
            <div class="flex flex-col gap-3" x-data="{ tahun: '{{ now()->year }}' }">
                <label class="label !mb-0">Tahun</label>
                <input type="number" x-model="tahun" class="input" min="2020" max="2100">
                <div class="flex gap-3">
                    <a :href="`{{ route('laporan.cuti.excel') }}?tahun=${tahun}`" class="btn-secondary flex-1">Excel</a>
                    <a :href="`{{ route('laporan.cuti.pdf') }}?tahun=${tahun}`" class="btn-secondary flex-1">PDF</a>
                </div>
            </div>
        </div>

        <div class="card card-hover">
            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-purple-100 text-purple-700 dark:bg-purple-500/15 dark:text-purple-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 17v-2a4 4 0 00-4-4H3m2 6h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="mb-1 font-semibold">Rekap Ketidakhadiran</h3>
            <div class="flex flex-col gap-3" x-data="{ bulan: '{{ now()->format('Y-m') }}' }">
                <label class="label !mb-0">Pilih Bulan</label>
                <input type="month" x-model="bulan" class="input">
                <div class="flex gap-3">
                    <a :href="`{{ route('laporan.ketidakhadiran.excel') }}?bulan=${bulan}`" class="btn-secondary flex-1">Excel</a>
                    <a :href="`{{ route('laporan.ketidakhadiran.pdf') }}?bulan=${bulan}`" class="btn-secondary flex-1">PDF</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
