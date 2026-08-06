<x-app-layout title="Cuti & Izin — Rekomendasi Cerdas">
    {{-- Tab Navigation --}}
    <div class="mb-6 flex gap-1 overflow-x-auto border-b border-kpi-line dark:border-white/10">
        <a href="{{ route('cuti.index') }}" 
           class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('cuti.index') && !request()->routeIs('cuti.kalender') && !request()->routeIs('cuti.analitik') && !request()->routeIs('cuti.rekomendasi') ? 'border-kpi-red text-kpi-red' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200' }}">
            Daftar Pengajuan
        </a>
        <a href="{{ route('cuti.kalender') }}" 
           class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('cuti.kalender') ? 'border-kpi-red text-kpi-red' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200' }}">
            Kalender Tim
        </a>
        <a href="{{ route('cuti.analitik') }}" 
           class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('cuti.analitik') ? 'border-kpi-red text-kpi-red' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200' }}">
            Analitik
        </a>
        <a href="{{ route('cuti.rekomendasi') }}" 
           class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('cuti.rekomendasi') ? 'border-kpi-red text-kpi-red' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200' }}">
            Rekomendasi Cerdas
        </a>
    </div>

    {{-- Title Section --}}
    <div class="mb-6">
        <h2 class="font-serif text-xl font-semibold">Rekomendasi Cerdas Penjadwalan</h2>
        <p class="text-xs text-kpi-gray mt-0.5">Analisis heuristik dan pola historis untuk mendukung keputusan approval</p>
    </div>

    {{-- Stats Cards Grid --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 mb-6">
        {{-- Card 1: Unit Berisiko Tinggi --}}
        <div class="card card-glow-red card-hover group transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400">Unit Berisiko Tinggi</p>
                    <p class="text-lg font-serif font-bold text-kpi-black dark:text-stone-50 mt-3 truncate max-w-[150px]" title="{{ $highRiskUnit }}">{{ $highRiskUnit }}</p>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-kpi-red to-kpi-red-dark text-white shadow-[0_4px_14px_rgba(193,39,45,0.3)] dark:from-kpi-red dark:to-kpi-red-dark transition-all duration-300 group-hover:scale-110">
                    <svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <p class="mt-4 text-[10px] text-kpi-gray">Unit dengan rasio cuti tertinggi saat ini</p>
        </div>

        {{-- Card 2: Konflik Aktif --}}
        <div class="card card-glow-amber card-hover group transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400">Konflik Jadwal Cuti</p>
                    <p class="stat-figure text-amber-600 dark:text-amber-400 mt-3">{{ $konflikAktifCount ?? 0 }}</p>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 text-white shadow-[0_4px_14px_rgba(245,158,11,0.3)] dark:from-amber-600 dark:to-amber-700 transition-all duration-300 group-hover:scale-110">
                    <svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="mt-4 text-[10px] text-kpi-gray">Pegawai cuti bersamaan dalam 1 unit</p>
        </div>

        {{-- Card 3: Rekomendasi Sistem --}}
        <div class="card card-glow-sky card-hover group transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400">Total Rekomendasi AI</p>
                    <p class="stat-figure text-sky-600 dark:text-sky-400 mt-3">{{ count($recommendations) }}</p>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 text-white shadow-[0_4px_14px_rgba(14,165,233,0.3)] dark:from-sky-600 dark:to-blue-700 transition-all duration-300 group-hover:scale-110">
                    <svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </div>
            </div>
            <p class="mt-4 text-[10px] text-kpi-gray">Analisis beban kerja & kapasitas SDM</p>
        </div>
    </div>

    {{-- Daftar Rekomendasi --}}
    <div class="space-y-4">
        <h2 class="font-serif text-base font-bold text-kpi-black dark:text-stone-50">Daftar Rekomendasi & Catatan Sistem</h2>
        @forelse($recommendations as $rec)
            @php
                $badgeColor = match($rec['level']) {
                    'Tinggi' => 'danger',
                    'Sedang' => 'warning',
                    default => 'info',
                };
            @endphp
            <div class="card card-hover flex flex-col md:flex-row md:items-center justify-between gap-4 border-l-4 
                        {{ $rec['level'] === 'Tinggi' ? 'border-l-kpi-red' : ($rec['level'] === 'Sedang' ? 'border-l-amber-500' : 'border-l-sky-500') }}
                        bg-white/95 dark:bg-kpi-dark-surface/90">
                <div class="space-y-2 max-w-3xl">
                    <div class="flex flex-wrap items-center gap-2">
                        <x-badge :color="$badgeColor">{{ $rec['level'] }}</x-badge>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-kpi-gray">{{ $rec['kategori'] }}</span>
                    </div>
                    <h3 class="font-serif text-base font-bold text-kpi-black dark:text-stone-50">{{ $rec['judul'] }}</h3>
                    <p class="text-sm text-kpi-gray leading-relaxed">{!! $rec['deskripsi'] !!}</p>
                </div>
                <div class="flex items-center shrink-0">
                    <a href="{{ $rec['aksi_link'] }}" class="btn-secondary !text-xs py-2 px-4 rounded-xl font-semibold active:scale-[0.98]">
                        {{ $rec['aksi_nama'] }}
                    </a>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="empty-state">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 mb-2">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="font-serif text-base font-semibold text-kpi-black dark:text-stone-50">Kondisi Jadwal Optimal</p>
                    <p class="text-xs text-kpi-gray max-w-md">Tidak ada konflik jadwal atau penumpukan antrean approval yang kritis saat ini. Operasional unit berjalan aman.</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- System Notes Notice --}}
    <div class="mt-8 p-4 rounded-2xl border border-dashed border-kpi-line bg-kpi-cream/30 text-xs text-kpi-gray dark:border-white/10 dark:bg-white/[0.01] leading-relaxed">
        <p class="font-bold mb-1">Catatan Logika & Batasan Sistem:</p>
        <p>Fitur Rekomendasi Cerdas ini menggunakan modul <strong>rule-based berbasis analisis statistik data historis</strong> internal SIMPEG-KPI Pusat. Sistem mendeteksi bentrok jadwal secara langsung menggunakan rentang irisan tanggal pegawai di unit kerja yang sama, menghitung tingkat antrean terhadap batas kapasitas, dan mengevaluasi tren musiman berdasarkan frekuensi cuti tahun sebelumnya. Logika ini bersifat deterministik (bukan Machine Learning / AI model eksternal) untuk mengoptimalkan performa server tanpa biaya API eksternal, dan dapat ditingkatkan di masa depan dengan model regresi/prediksi tingkat lanjut.</p>
    </div>
</x-app-layout>
