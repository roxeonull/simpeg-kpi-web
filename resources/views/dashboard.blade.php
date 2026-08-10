<x-app-layout title="Dashboard">
    @php
        $jam = now()->hour;
        $sapaan = $jam < 11 ? 'Selamat pagi' : ($jam < 15 ? 'Selamat siang' : ($jam < 18 ? 'Selamat sore' : 'Selamat malam'));
    @endphp

    {{-- Greeting Section --}}
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4 animate-fade-in-up" style="animation-delay: 50ms;">
        <div>
            <p class="eyebrow flex items-center gap-1.5">
                <span class="h-1.5 w-1.5 rounded-full bg-kpi-gold animate-pulse"></span>
                {{ now()->translatedFormat('l, d F Y') }}
            </p>
            <h2 class="mt-2 font-serif text-3xl font-semibold tracking-tight sm:text-[34px]">{{ $sapaan }}, {{ explode(' ', auth()->user()->name)[0] }}.</h2>
        </div>
        <div class="flex items-center gap-2 rounded-xl border border-kpi-line bg-white/40 px-4 py-2.5 backdrop-blur dark:border-white/10 dark:bg-kpi-dark-surface/40">
            <div class="h-2 w-2 rounded-full bg-emerald-500 animate-ping"></div>
            <p class="text-xs font-medium text-kpi-gray dark:text-stone-300">Sistem Berjalan Normal</p>
        </div>
    </div>

    {{-- 1. HEADER HERO WIDGET: "BUTUH TINDAKAN SEGERA" (Urgent Red Accent) --}}
    @if($totalButuhTindakan > 0)
        <div class="card border-l-4 border-l-kpi-red mb-6 animate-fade-in-up" style="animation-delay: 80ms;">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-kpi-red text-white shadow-sm">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-serif text-lg font-bold text-kpi-black dark:text-stone-100">Butuh Tindakan Segera</h3>
                            <span class="inline-flex items-center rounded-md bg-kpi-red/10 px-2.5 py-0.5 text-xs font-bold text-kpi-red dark:bg-rose-500/20 dark:text-rose-400">
                                {{ $totalButuhTindakan }} Menunggu
                            </span>
                        </div>
                        <p class="text-xs text-kpi-gray mt-0.5">Terdapat pengajuan pegawai yang memerlukan persetujuan atau verifikasi Anda.</p>
                    </div>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3 border-t border-kpi-line pt-4 dark:border-white/10">
                {{-- Cuti Menunggu --}}
                <a href="{{ route('cuti.index') }}" class="group flex items-center justify-between rounded-xl border border-kpi-line bg-stone-50/50 p-3 hover:border-kpi-red/40 hover:bg-white dark:border-white/5 dark:bg-white/5 dark:hover:bg-white/10 transition-all">
                    <div>
                        <p class="text-xs font-bold text-kpi-black dark:text-stone-200">Persetujuan Cuti</p>
                        <p class="text-[11px] text-kpi-gray">{{ $cutiMenunggu }} pengajuan</p>
                    </div>
                    <span class="btn-xs-secondary group-hover:border-kpi-red group-hover:text-kpi-red">Tinjau &rarr;</span>
                </a>

                {{-- Verifikasi Diklat --}}
                <a href="{{ route('pelatihan.index', ['status_verifikasi' => 'menunggu']) }}" class="group flex items-center justify-between rounded-xl border border-kpi-line bg-stone-50/50 p-3 hover:border-kpi-red/40 hover:bg-white dark:border-white/5 dark:bg-white/5 dark:hover:bg-white/10 transition-all">
                    <div>
                        <p class="text-xs font-bold text-kpi-black dark:text-stone-200">Verifikasi Diklat</p>
                        <p class="text-[11px] text-kpi-gray">{{ $pelatihanMenunggu }} usulan</p>
                    </div>
                    <span class="btn-xs-secondary group-hover:border-kpi-red group-hover:text-kpi-red">Verifikasi &rarr;</span>
                </a>

                {{-- Perubahan Data --}}
                <a href="{{ route('pengajuan-perubahan.index', ['status' => 'menunggu']) }}" class="group flex items-center justify-between rounded-xl border border-kpi-line bg-stone-50/50 p-3 hover:border-kpi-red/40 hover:bg-white dark:border-white/5 dark:bg-white/5 dark:hover:bg-white/10 transition-all">
                    <div>
                        <p class="text-xs font-bold text-kpi-black dark:text-stone-200">Perubahan Data</p>
                        <p class="text-[11px] text-kpi-gray">{{ $perubahanDataMenunggu }} usulan</p>
                    </div>
                    <span class="btn-xs-secondary group-hover:border-kpi-red group-hover:text-kpi-red">Tinjau &rarr;</span>
                </a>
            </div>
        </div>
    @else
        <div class="card border-l-4 border-l-stone-300 dark:border-l-stone-600 mb-6 animate-fade-in-up" style="animation-delay: 80ms;">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <h3 class="font-serif text-sm font-bold text-kpi-black dark:text-stone-100">Semua Pengajuan Selesai</h3>
                        <p class="text-xs text-kpi-gray mt-0.5">Tidak ada persetujuan cuti, verifikasi diklat, atau perubahan data yang tertunda saat ini.</p>
                    </div>
                </div>
                <span class="badge badge-success shrink-0">Status Bersih</span>
            </div>
        </div>
    @endif

    {{-- 2. CORE STAT CARDS GRID (2 Cards - Subtle Emerald & Sky Accents) --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        
        {{-- Total Pegawai --}}
        <div class="card card-hover group transition-all duration-300 hover:-translate-y-1 animate-fade-in-up" style="animation-delay: 100ms;">
            <div class="flex items-start justify-between">
                <div>
                    <p class="eyebrow">Total Pegawai Aktif</p>
                    <p class="stat-figure mt-2 transition-transform duration-300 group-hover:scale-[1.02]">{{ $totalPegawai }}</p>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400 transition-all duration-300 group-hover:scale-105">
                    <svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 100-8 4 4 0 000 8zm6 3.13a4 4 0 00-3-3.87"/></svg>
                </div>
            </div>
            <p class="mt-3.5 text-xs text-kpi-gray flex items-center gap-1.5">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                Seluruh unit kerja aktif
            </p>
        </div>

        {{-- Kehadiran Hari Ini --}}
        <div class="card card-hover group transition-all duration-300 hover:-translate-y-1 animate-fade-in-up" style="animation-delay: 150ms;">
            <div class="flex items-start justify-between">
                <div>
                    <p class="eyebrow">Kehadiran Hari Ini</p>
                    <p class="stat-figure mt-2 transition-transform duration-300 group-hover:scale-[1.02]">{{ $tingkatKehadiran }}<span class="text-lg font-medium text-stone-400">%</span></p>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-600 dark:bg-sky-500/15 dark:text-sky-400 transition-all duration-300 group-hover:scale-105">
                    <svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-3.5">
                <div class="h-1.5 w-full overflow-hidden rounded-full bg-stone-100 dark:bg-white/10">
                    <div class="h-full rounded-full bg-sky-500 transition-all duration-1000 ease-out" style="width: {{ min(100, $tingkatKehadiran) }}%"></div>
                </div>
                <div class="mt-2 flex items-center justify-between text-[11px] text-kpi-gray">
                    <span>Tingkat Kehadiran Kerja</span>
                    <span class="font-semibold text-sky-600 dark:text-sky-400">{{ $tingkatKehadiran }}%</span>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. OPERATIONAL ACTIONABLE WIDGETS (3 COLUMNS - Subtle Harmonized Accents) --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3 animate-fade-in-up" style="animation-delay: 200ms;">
        
        {{-- WIDGET CUTI HARI INI (Rose Accent) --}}
        <div class="card shadow-[var(--shadow-card)] flex flex-col justify-between">
            <div>
                <div class="mb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-rose-50 text-rose-600 dark:bg-rose-500/15 dark:text-rose-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </span>
                        <h3 class="font-serif text-base font-bold text-kpi-black dark:text-stone-100">Cuti Hari Ini</h3>
                    </div>
                    <span class="badge badge-danger">{{ $totalCutiHariIniCount }} Pegawai</span>
                </div>

                @if($cutiHariIniList->isNotEmpty())
                    <ul class="divide-y divide-kpi-line dark:divide-white/5 space-y-0 text-xs">
                        @foreach($cutiHariIniList as $c)
                            <li class="py-2.5 flex items-center justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <p class="font-semibold text-kpi-black dark:text-stone-200 truncate">{{ $c->pegawai->nama }}</p>
                                    <p class="text-[10px] text-kpi-gray truncate">{{ $c->pegawai->unit?->nama_unit ?? '—' }}</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <x-badge color="danger" class="!text-[10px] !px-1.5 !py-0.5">
                                        {{ $c->jenisCuti?->nama ?? ucfirst($c->jenis_cuti) }}
                                    </x-badge>
                                    <p class="text-[10px] font-mono text-kpi-gray mt-0.5">s.d. {{ $c->tanggal_selesai->format('d M Y') }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="py-6 text-center">
                        <svg class="mx-auto h-8 w-8 text-kpi-gray/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="mt-2 text-xs font-semibold text-kpi-gray">Tidak ada pegawai cuti hari ini</p>
                    </div>
                @endif
            </div>
            <div class="mt-3 pt-3 border-t border-kpi-line dark:border-white/10">
                <a href="{{ route('cuti.index') }}" class="text-xs font-bold text-kpi-black dark:text-stone-200 hover:text-kpi-red flex items-center gap-1">
                    Kelola Data Cuti &rarr;
                </a>
            </div>
        </div>

        {{-- WIDGET JADWAL SHIFT HARI INI (Sky/Info Accent & Shift Box Colors) --}}
        <div class="card shadow-[var(--shadow-card)] flex flex-col justify-between">
            <div>
                <div class="mb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-sky-50 text-sky-600 dark:bg-sky-500/15 dark:text-sky-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <h3 class="font-serif text-base font-bold text-kpi-black dark:text-stone-100">Jadwal Shift Hari Ini</h3>
                    </div>
                    <span class="badge badge-info">{{ $shiftCounts['total'] }} Terjadwal</span>
                </div>

                <div class="grid grid-cols-3 gap-2 py-2">
                    {{-- Shift 1 (Blue) --}}
                    <div class="rounded-xl border border-sky-100 bg-sky-50/40 p-2.5 text-center dark:border-sky-500/10 dark:bg-sky-500/5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-600 dark:text-sky-400">Shift 1</span>
                        <p class="text-lg font-bold font-serif text-kpi-black dark:text-stone-100 mt-0.5">{{ $shiftCounts['shift_1'] }}</p>
                        <p class="text-[9px] text-kpi-gray">06:00-14:00</p>
                    </div>
                    {{-- Shift 2 (Orange/Amber) --}}
                    <div class="rounded-xl border border-amber-100 bg-amber-50/40 p-2.5 text-center dark:border-amber-500/10 dark:bg-amber-500/5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Shift 2</span>
                        <p class="text-lg font-bold font-serif text-kpi-black dark:text-stone-100 mt-0.5">{{ $shiftCounts['shift_2'] }}</p>
                        <p class="text-[9px] text-kpi-gray">14:00-22:00</p>
                    </div>
                    {{-- Shift 3 (Purple) --}}
                    <div class="rounded-xl border border-purple-100 bg-purple-50/40 p-2.5 text-center dark:border-purple-500/10 dark:bg-purple-500/5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400">Shift 3</span>
                        <p class="text-lg font-bold font-serif text-kpi-black dark:text-stone-100 mt-0.5">{{ $shiftCounts['shift_3'] }}</p>
                        <p class="text-[9px] text-kpi-gray">22:00-06:00</p>
                    </div>
                </div>

                <p class="text-[11px] text-kpi-gray mt-2 flex items-center gap-1.5">
                    <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                    Petugas penyiaran & operasional terjadwal hari ini
                </p>
            </div>
            <div class="mt-3 pt-3 border-t border-kpi-line dark:border-white/10">
                <a href="{{ route('absensi.shift.index', 1) }}" class="text-xs font-bold text-kpi-black dark:text-stone-200 hover:text-kpi-red flex items-center gap-1">
                    Buka Grid Shift &rarr;
                </a>
            </div>
        </div>

        {{-- WIDGET CAPAIAN JP DIKLAT (Gold Accent) --}}
        <div class="card shadow-[var(--shadow-card)] flex flex-col justify-between">
            <div>
                <div class="mb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-kpi-gold-soft text-kpi-gold dark:bg-kpi-gold/25">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0118 20.5H6a12.083 12.083 0 01.84-9.922L12 14z"/></svg>
                        </span>
                        <h3 class="font-serif text-base font-bold text-kpi-black dark:text-stone-100">Capaian JP Diklat</h3>
                    </div>
                    <span class="badge badge-warning">Target {{ $targetJp }} JP</span>
                </div>

                @php
                    $persenCapaian = $totalPegawai > 0 ? round(($pegawaiCapaianDiklat / $totalPegawai) * 100) : 0;
                    $belumMemenuhi = max(0, $totalPegawai - $pegawaiCapaianDiklat);
                @endphp

                <div class="py-2">
                    <div class="flex items-baseline justify-between">
                        <span class="text-2xl font-bold font-serif text-kpi-black dark:text-stone-100">{{ $pegawaiCapaianDiklat }} <span class="text-xs font-sans font-normal text-kpi-gray">/ {{ $totalPegawai }} pegawai</span></span>
                        <span class="font-semibold text-xs text-kpi-gold">{{ $persenCapaian }}% Tuntas</span>
                    </div>
                    <div class="mt-2.5 h-2 w-full overflow-hidden rounded-full bg-stone-100 dark:bg-white/10">
                        <div class="h-full rounded-full bg-kpi-gold transition-all duration-1000 ease-out" style="width: {{ min(100, $persenCapaian) }}%"></div>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs">
                        <span class="text-stone-700 dark:text-stone-300 font-medium">✓ {{ $pegawaiCapaianDiklat }} Memenuhi</span>
                        <span class="text-kpi-gray font-medium">! {{ $belumMemenuhi }} Belum Memenuhi</span>
                    </div>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-kpi-line dark:border-white/10">
                <a href="{{ route('pelatihan.index', ['tab' => 'rekap']) }}" class="text-xs font-bold text-kpi-gold hover:underline flex items-center gap-1">
                    Rekap Capaian Diklat &rarr;
                </a>
            </div>
        </div>
    </div>

    {{-- 4. CHARTS SECTION (Preserved) --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        
        {{-- Weekly Line Chart Area --}}
        <div class="card lg:col-span-2 shadow-[var(--shadow-card)] animate-fade-in-up" style="animation-delay: 250ms;">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="font-serif text-lg font-semibold" id="chartTitle">Grafik Kehadiran</h2>
                    <p class="text-xs text-kpi-gray mt-0.5">Tren harian tingkat kehadiran pegawai aktif</p>
                </div>
                <div class="flex items-center gap-2" id="chartPeriodeContainer"
                     @change="fetchChartData($event.target.value)">
                    <x-select name="periode" :value="$selectedPeriode ?? '7'" :options="[
                        ['value' => '7', 'label' => '7 Hari Terakhir'],
                        ['value' => '30', 'label' => '30 Hari Terakhir'],
                        ['value' => 'bulan_ini', 'label' => 'Bulan Ini']
                    ]" size="sm" class="min-w-[155px]" />
                </div>
            </div>
            <div class="h-68 relative">
                <div id="chartLoadingOverlay" class="absolute inset-0 z-10 flex items-center justify-center bg-white/50 dark:bg-kpi-dark-surface/50 backdrop-blur-[1px] opacity-0 pointer-events-none transition-opacity duration-200">
                    <span class="btn-loading-spinner btn-loading-spinner-dark"></span>
                </div>
                <canvas id="lineChartCanvas"></canvas>
            </div>
        </div>

        {{-- Today Attendance Doughnut --}}
        <div class="card shadow-[var(--shadow-card)] animate-fade-in-up" style="animation-delay: 300ms;">
            <div class="mb-4">
                <h2 class="font-serif text-lg font-semibold">Komposisi Presensi</h2>
                <p class="text-xs text-kpi-gray mt-0.5">Rincian status pegawai hari ini</p>
            </div>
            
            <div class="relative h-52 flex items-center justify-center">
                <canvas id="todayDoughnutCanvas"></canvas>
                <div class="absolute flex flex-col items-center justify-center text-center">
                    <span class="text-3xl font-bold font-serif leading-none text-kpi-black dark:text-stone-100">{{ $tingkatKehadiran }}%</span>
                    <span class="text-[10px] font-semibold uppercase tracking-wider text-kpi-gray mt-1">Hadir Kerja</span>
                </div>
            </div>

            <script>
                let lineChartInstance = null;

                function fetchChartData(periode) {
                    const overlay = document.getElementById('chartLoadingOverlay');
                    if (overlay) overlay.style.opacity = '1';

                    fetch(`{{ route('dashboard') }}?periode=${periode}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (lineChartInstance && data.labels && data.totals) {
                            lineChartInstance.data.labels = data.labels;
                            lineChartInstance.data.datasets[0].data = data.totals;
                            lineChartInstance.update('active');
                        }
                    })
                    .catch(err => console.error('Chart fetch error:', err))
                    .finally(() => {
                        if (overlay) overlay.style.opacity = '0';
                    });
                }

                document.addEventListener('DOMContentLoaded', function() {
                    // 1. Weekly Line Chart
                    const lineCanvas = document.getElementById('lineChartCanvas');
                    if (lineCanvas) {
                        const ctxLine = lineCanvas.getContext('2d');
                        const gradient = ctxLine.createLinearGradient(0, 0, 0, 240);
                        gradient.addColorStop(0, 'rgba(193, 39, 45, 0.22)');
                        gradient.addColorStop(1, 'rgba(193, 39, 45, 0.00)');

                        lineChartInstance = new Chart(ctxLine, {
                            type: 'line',
                            data: {
                                labels: {!! json_encode($grafikMingguan->pluck('label')) !!},
                                datasets: [{
                                    label: 'Pegawai Hadir',
                                    data: {!! json_encode($grafikMingguan->pluck('total')) !!},
                                    borderColor: '#C1272D',
                                    backgroundColor: gradient,
                                    borderWidth: 3.5,
                                    pointRadius: 4.5,
                                    pointBackgroundColor: '#C1272D',
                                    pointBorderColor: document.documentElement.classList.contains('dark') ? '#1C1812' : '#fff',
                                    pointBorderWidth: 2.5,
                                    pointHoverRadius: 7,
                                    pointHoverBackgroundColor: '#C1272D',
                                    pointHoverBorderColor: '#fff',
                                    pointHoverBorderWidth: 3,
                                    tension: 0.38,
                                    fill: true,
                                }],
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { 
                                    legend: { display: false },
                                    tooltip: {
                                        backgroundColor: 'rgba(28, 23, 18, 0.95)',
                                        padding: 12,
                                        titleFont: { family: 'Instrument Sans', size: 12, weight: 'bold' },
                                        bodyFont: { family: 'Instrument Sans', size: 13 },
                                        displayColors: false,
                                        callbacks: {
                                            label: function(context) {
                                                return context.parsed.y + ' Pegawai Hadir';
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: { 
                                        beginAtZero: true, 
                                        ticks: { 
                                            precision: 0,
                                            color: '#8C857B',
                                            font: { family: 'IBM Plex Mono', size: 10 }
                                        }, 
                                        grid: { color: 'rgba(130, 130, 130, 0.08)' } 
                                    },
                                    x: { 
                                        ticks: {
                                            color: '#8C857B',
                                            font: { family: 'Instrument Sans', size: 11, weight: 'bold' }
                                        },
                                        grid: { display: false } 
                                    },
                                },
                            },
                        });
                    }

                    // 2. Today Doughnut Chart
                    const doughnutCanvas = document.getElementById('todayDoughnutCanvas');
                    if (doughnutCanvas) {
                        const ctxDoughnut = doughnutCanvas.getContext('2d');
                        new Chart(ctxDoughnut, {
                            type: 'doughnut',
                            data: {
                                labels: ['Hadir', 'Telat', 'Izin/Sakit', 'Alpa', 'Belum Presensi'],
                                datasets: [{
                                    data: [
                                        {{ $rincianKehadiran['hadir'] }},
                                        {{ $rincianKehadiran['telat'] }},
                                        {{ $rincianKehadiran['izin_sakit'] }},
                                        {{ $rincianKehadiran['alpa'] }},
                                        {{ $rincianKehadiran['belum_presensi'] }}
                                    ],
                                    backgroundColor: ['#10B981', '#F59E0B', '#0EA5E9', '#EF4444', '#a8a29e'],
                                    borderWidth: 2,
                                    borderColor: document.documentElement.classList.contains('dark') ? '#1c1812' : '#fff',
                                    hoverOffset: 4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '75%',
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        backgroundColor: 'rgba(28, 23, 18, 0.95)',
                                        padding: 10,
                                        titleFont: { family: 'Instrument Sans', size: 11, weight: 'bold' },
                                        bodyFont: { family: 'Instrument Sans', size: 12 },
                                        displayColors: true,
                                    }
                                }
                            }
                        });
                    }
                });
            </script>

            <ul class="mt-5 grid grid-cols-2 gap-x-4 gap-y-2 border-t border-kpi-line pt-4 dark:border-white/10 text-xs">
                <li class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-[#10B981]"></span>
                    <span class="text-kpi-gray">Hadir: <strong class="text-kpi-black dark:text-stone-100">{{ $rincianKehadiran['hadir'] }}</strong></span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-[#F59E0B]"></span>
                    <span class="text-kpi-gray">Telat: <strong class="text-kpi-black dark:text-stone-100">{{ $rincianKehadiran['telat'] }}</strong></span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-[#0EA5E9]"></span>
                    <span class="text-kpi-gray">Izin/Sakit: <strong class="text-kpi-black dark:text-stone-100">{{ $rincianKehadiran['izin_sakit'] }}</strong></span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-[#EF4444]"></span>
                    <span class="text-kpi-gray">Alpa: <strong class="text-kpi-black dark:text-stone-100">{{ $rincianKehadiran['alpa'] }}</strong></span>
                </li>
                <li class="flex items-center gap-2 col-span-2">
                    <span class="h-2 w-2 rounded-full bg-[#a8a29e]"></span>
                    <span class="text-kpi-gray">Belum Presensi: <strong class="text-kpi-black dark:text-stone-100">{{ $rincianKehadiran['belum_presensi'] }}</strong></span>
                </li>
            </ul>
        </div>
    </div>

    {{-- 5. BOTTOM SECTION: RECENT ACTIVITY & QUICK LINKS (Preserved) --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3 animate-fade-in-up" style="animation-delay: 350ms;">
        
        {{-- Recent Activity timeline --}}
        <div class="card lg:col-span-2 shadow-[var(--shadow-card)]">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="font-serif text-lg font-semibold">Aktivitas Sistem Terbaru</h2>
                    <p class="text-xs text-kpi-gray mt-0.5">Catatan riwayat perubahan data dan sistem</p>
                </div>
            </div>
            
            <ul class="mt-6 space-y-1 max-h-76 overflow-y-auto pr-1">
                @forelse ($aktivitasTerbaru as $log)
                    <li class="timeline-item transition-all hover:bg-stone-50/50 dark:hover:bg-white/[0.02] p-2 rounded-lg">
                        <p class="text-sm leading-snug">
                            <span class="font-semibold text-kpi-black dark:text-stone-100">{{ $log->user?->name ?? 'Sistem' }}</span>
                            <span class="text-stone-600 dark:text-stone-300">{{ $log->aksi }}</span>
                            @if($log->keterangan) 
                                <span class="text-kpi-gray italic font-mono text-[11px] block mt-0.5">&mdash; {{ $log->keterangan }}</span> 
                            @endif
                        </p>
                        <p class="mono mt-1.5 text-[10px] text-stone-400 flex items-center gap-1">
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $log->created_at?->diffForHumans() }}
                        </p>
                    </li>
                @empty
                    <li class="empty-state !py-10">
                        <svg class="h-9 w-9 text-kpi-gray/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-sm font-semibold text-kpi-gray">Belum ada aktivitas.</p>
                    </li>
                @endforelse
            </ul>
        </div>

        {{-- Quick Links Card --}}
        <div class="card shadow-[var(--shadow-card)] flex flex-col justify-between">
            <div>
                <div class="mb-4">
                    <h2 class="font-serif text-lg font-semibold">Tautan Cepat</h2>
                    <p class="text-xs text-kpi-gray mt-0.5">Akses langsung ke menu operasional utama</p>
                </div>

                <div class="space-y-3">
                    <a href="{{ route('absensi.index') }}" class="flex items-center gap-3 p-3 rounded-xl border border-kpi-line hover:border-kpi-red/40 bg-stone-50/50 hover:bg-white dark:border-white/5 dark:bg-white/5 dark:hover:bg-white/10 transition-all duration-200">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-50 text-sky-600 dark:bg-sky-500/20 dark:text-sky-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <div>
                            <p class="text-xs font-semibold">Kelola Absensi</p>
                            <p class="text-[10px] text-kpi-gray mt-0.5">Tinjau & rekap kehadiran pegawai</p>
                        </div>
                    </a>

                    <a href="{{ route('cuti.index') }}" class="flex items-center gap-3 p-3 rounded-xl border border-kpi-line hover:border-kpi-red/40 bg-stone-50/50 hover:bg-white dark:border-white/5 dark:bg-white/5 dark:hover:bg-white/10 transition-all duration-200">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-rose-50 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </span>
                        <div>
                            <p class="text-xs font-semibold">Persetujuan Cuti</p>
                            <p class="text-[10px] text-kpi-gray mt-0.5">Proses cuti berjenjang pegawai</p>
                        </div>
                    </a>

                    <a href="{{ route('pelatihan.index') }}" class="flex items-center gap-3 p-3 rounded-xl border border-kpi-line hover:border-kpi-red/40 bg-stone-50/50 hover:bg-white dark:border-white/5 dark:bg-white/5 dark:hover:bg-white/10 transition-all duration-200">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-kpi-gold-soft text-kpi-gold dark:bg-kpi-gold/25">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0118 20.5H6a12.083 12.083 0 01.84-9.922L12 14z"/></svg>
                        </span>
                        <div>
                            <p class="text-xs font-semibold">Pendidikan & Diklat</p>
                            <p class="text-[10px] text-kpi-gray mt-0.5">Pantau capaian pengembangan diri</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-kpi-line dark:border-white/10 text-[11px] text-kpi-gray dark:text-stone-400">
                <p>SIMPEG-KPI v1.0 &mdash; Komisi Penyiaran Indonesia Pusat</p>
            </div>
        </div>
    </div>
</x-app-layout>
