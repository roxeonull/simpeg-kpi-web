<x-app-layout title="Cuti & Izin — Analitik">
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

    {{-- Filter Form --}}
    <div class="relative z-20 mb-6 flex justify-between items-center flex-wrap gap-4">
        <div>
            <h2 class="font-serif text-xl font-semibold">Dashboard Analitik Cuti</h2>
            <p class="text-xs text-kpi-gray mt-0.5">Statistik tren dan pemanfaatan cuti pegawai</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <label class="text-xs font-semibold text-kpi-gray">Tahun Periode:</label>
            @php
                $yearOptions = [];
                foreach (range(now()->year - 2, now()->year + 1) as $y) {
                    $yearOptions[] = ['value' => (string)$y, 'label' => (string)$y];
                }
            @endphp
            <x-select name="year" :value="$year" :options="$yearOptions" class="w-full max-w-[120px]" />
        </form>
    </div>

    {{-- Stats Cards Grid --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        {{-- Card 1: Total Pengajuan --}}
        <div class="card card-glow-amber card-hover group transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400">Total Pengajuan</p>
                    <p class="stat-figure mt-2.5 transition-transform duration-300 group-hover:scale-[1.03]">{{ $totalPengajuan }}</p>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-yellow-600 text-white shadow-[0_4px_14px_rgba(245,158,11,0.3)] dark:from-amber-600 dark:to-yellow-700 transition-all duration-300 group-hover:scale-110">
                    <svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
            <p class="mt-4 text-xs flex items-center gap-1.5">
                @if($changeTotal !== null)
                    <span class="inline-flex items-center font-semibold {{ $changeTotal >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                        {{ $changeTotal >= 0 ? '↑' : '↓' }} {{ abs($changeTotal) }}%
                    </span>
                    <span class="text-kpi-gray">dari bulan lalu</span>
                @else
                    <span class="text-stone-400">&mdash;</span>
                    <span class="text-kpi-gray">Belum ada pembanding</span>
                @endif
            </p>
        </div>

        {{-- Card 2: Tingkat Persetujuan --}}
        <div class="card card-glow-emerald card-hover group transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400">Tingkat Persetujuan</p>
                    <p class="stat-figure mt-2.5 transition-transform duration-300 group-hover:scale-[1.03]">{{ $tingkatPersetujuan }}<span class="text-lg font-medium text-stone-400">%</span></p>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-[0_4px_14px_rgba(16,185,129,0.3)] dark:from-emerald-600 dark:to-teal-700 transition-all duration-300 group-hover:scale-110">
                    <svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="mt-4 text-xs text-stone-500 dark:text-stone-400 flex items-center gap-1.5">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                Dari pengajuan yang diputuskan
            </p>
        </div>

        {{-- Card 3: Rata-rata Hari --}}
        <div class="card card-glow-sky card-hover group transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400">Rata-rata Hari</p>
                    <p class="stat-figure mt-2.5 transition-transform duration-300 group-hover:scale-[1.03]">{{ $rataRataHari }}<span class="text-xs font-sans font-normal text-kpi-gray"> hari/cuti</span></p>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 text-white shadow-[0_4px_14px_rgba(14,165,233,0.3)] dark:from-sky-600 dark:to-blue-700 transition-all duration-300 group-hover:scale-110">
                    <svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="mt-4 text-xs text-stone-500 dark:text-stone-400 flex items-center gap-1.5">
                <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                Durasi rata-rata per pengajuan
            </p>
        </div>

        {{-- Card 4: Total Hari Terpakai --}}
        <div class="card card-glow-red card-hover group transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400">Total Hari Terpakai</p>
                    <p class="stat-figure mt-2.5 transition-transform duration-300 group-hover:scale-[1.03]">{{ $totalHariTerpakai }}<span class="text-xs font-sans font-normal text-kpi-gray"> hari</span></p>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-rose-500 to-red-600 text-white shadow-[0_4px_14px_rgba(244,63,94,0.3)] dark:from-rose-600 dark:to-red-700 transition-all duration-300 group-hover:scale-110">
                    <svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <p class="mt-4 text-xs flex items-center gap-1.5">
                @if($changeHari !== null)
                    <span class="inline-flex items-center font-semibold {{ $changeHari >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                        {{ $changeHari >= 0 ? '↑' : '↓' }} {{ abs($changeHari) }}%
                    </span>
                    <span class="text-kpi-gray">dari bulan lalu</span>
                @else
                    <span class="text-stone-400">&mdash;</span>
                    <span class="text-kpi-gray">Belum ada pembanding</span>
                @endif
            </p>
        </div>
    </div>

    {{-- Main Premium Visualisation Section --}}
    <div class="card shadow-[var(--shadow-card)]" 
         x-data="{
             activeTab: 'tren',
             charts: {},
             init() {
                 const data = JSON.parse(this.$refs.dataPayload.textContent);
                 const isDark = document.documentElement.classList.contains('dark');
                 const textColor = isDark ? '#8C857B' : '#6B6459';
                 const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(130,130,130,0.08)';
                 const borderThemeColor = isDark ? '#1C1812' : '#fff';

                 // Initialize Line Chart (Tren Bulanan)
                 const ctxLine = document.getElementById('lineChartCanvas').getContext('2d');
                 const gradientLine = ctxLine.createLinearGradient(0, 0, 0, 240);
                 gradientLine.addColorStop(0, 'rgba(180, 135, 42, 0.15)');
                 gradientLine.addColorStop(1, 'rgba(180, 135, 42, 0.00)');

                 this.charts.tren = new Chart(ctxLine, {
                     type: 'line',
                     data: {
                         labels: data.labels,
                         datasets: [
                             {
                                 label: 'Total Pengajuan',
                                 data: data.total,
                                 borderColor: '#B4872A',
                                 backgroundColor: gradientLine,
                                 borderWidth: 3,
                                 pointRadius: 4,
                                 pointBackgroundColor: '#B4872A',
                                 pointBorderColor: borderThemeColor,
                                 pointBorderWidth: 2,
                                 tension: 0.38,
                                 fill: true
                             },
                             {
                                 label: 'Disetujui',
                                 data: data.disetujui,
                                 borderColor: '#10B981',
                                 backgroundColor: 'transparent',
                                 borderWidth: 2.5,
                                 pointRadius: 3.5,
                                 pointBackgroundColor: '#10B981',
                                 pointBorderColor: borderThemeColor,
                                 pointBorderWidth: 1.5,
                                 tension: 0.38
                             },
                             {
                                 label: 'Ditolak',
                                 data: data.ditolak,
                                 borderColor: '#EF4444',
                                 backgroundColor: 'transparent',
                                 borderWidth: 2.5,
                                 pointRadius: 3.5,
                                 pointBackgroundColor: '#EF4444',
                                 pointBorderColor: borderThemeColor,
                                 pointBorderWidth: 1.5,
                                 tension: 0.38
                             }
                         ]
                     },
                     options: {
                         responsive: true,
                         maintainAspectRatio: false,
                         plugins: {
                             legend: { labels: { color: textColor, font: { family: 'Instrument Sans', weight: 'bold', size: 11 } } },
                             tooltip: { backgroundColor: 'rgba(28, 23, 18, 0.95)', padding: 12 }
                         },
                         scales: {
                             x: { ticks: { color: textColor, font: { family: 'Instrument Sans', size: 11 } }, grid: { display: false } },
                             y: { ticks: { color: textColor, font: { family: 'IBM Plex Mono', size: 10 }, precision: 0 }, grid: { color: gridColor }, beginAtZero: true }
                         }
                     }
                 });

                 // Initialize Bar Chart (Perbandingan Unit Kerja)
                 const ctxBar = document.getElementById('barChartCanvas').getContext('2d');
                 this.charts.unit = new Chart(ctxBar, {
                     type: 'bar',
                     data: {
                         labels: data.unitLabels,
                         datasets: [
                             {
                                 label: 'Hari Cuti Terpakai',
                                 data: data.unitHari,
                                 backgroundColor: 'rgba(193, 39, 45, 0.85)',
                                 borderColor: '#C1272D',
                                 borderWidth: 1,
                                 borderRadius: 4
                             },
                             {
                                 label: 'Total Pengajuan',
                                 data: data.unitPengajuan,
                                 backgroundColor: 'rgba(180, 135, 42, 0.85)',
                                 borderColor: '#B4872A',
                                 borderWidth: 1,
                                 borderRadius: 4
                             }
                         ]
                     },
                     options: {
                         responsive: true,
                         maintainAspectRatio: false,
                         plugins: {
                             legend: { labels: { color: textColor, font: { family: 'Instrument Sans', weight: 'bold', size: 11 } } },
                             tooltip: { backgroundColor: 'rgba(28, 23, 18, 0.95)', padding: 12 }
                         },
                         scales: {
                             x: { ticks: { color: textColor, font: { family: 'Instrument Sans', size: 11 } }, grid: { display: false } },
                             y: { ticks: { color: textColor, font: { family: 'IBM Plex Mono', size: 10 }, precision: 0 }, grid: { color: gridColor }, beginAtZero: true }
                         }
                     }
                 });

                 // Initialize Pie/Donut Chart (Breakdown Jenis Cuti)
                 const ctxDoughnut = document.getElementById('doughnutChartCanvas').getContext('2d');
                 this.charts.jenis = new Chart(ctxDoughnut, {
                     type: 'doughnut',
                     data: {
                         labels: data.jenisLabels,
                         datasets: [{
                             data: data.jenisHari,
                             backgroundColor: ['#0EA5E9', '#EF4444', '#10B981', '#F59E0B'],
                             borderWidth: 2,
                             borderColor: borderThemeColor,
                             hoverOffset: 6
                         }]
                     },
                     options: {
                         responsive: true,
                         maintainAspectRatio: false,
                         cutout: '60%',
                         plugins: {
                             legend: { position: 'right', labels: { color: textColor, font: { family: 'Instrument Sans', weight: 'bold', size: 11 } } },
                             tooltip: {
                                 backgroundColor: 'rgba(28, 23, 18, 0.95)',
                                 padding: 12,
                                 callbacks: {
                                     label: function(context) {
                                         return ' ' + context.label + ': ' + context.parsed + ' hari terpakai';
                                     }
                                 }
                             }
                         }
                     }
                 });
             }
         }">
        
        <script type="application/json" x-ref="dataPayload">
            {!! json_encode($chartData) !!}
        </script>

        {{-- Dynamic Chart Container --}}
        <div class="h-80 w-full relative">
            <div x-show="activeTab === 'tren'" class="h-full w-full">
                <canvas id="lineChartCanvas"></canvas>
            </div>
            <div x-show="activeTab === 'unit'" class="h-full w-full" x-cloak>
                <canvas id="barChartCanvas"></canvas>
            </div>
            <div x-show="activeTab === 'jenis'" class="h-full w-full" x-cloak>
                <canvas id="doughnutChartCanvas"></canvas>
            </div>
        </div>

        {{-- Internal Tab Controls --}}
        <div class="mt-6 flex flex-wrap justify-center gap-2 border-t border-kpi-line pt-4 dark:border-white/10">
            <button @click="activeTab = 'tren'" 
                    :class="activeTab === 'tren' ? 'bg-kpi-red text-white' : 'btn-secondary'" 
                    class="text-xs font-semibold py-2 px-4 rounded-xl transition-all active:scale-[0.98]">
                Tren Bulanan
            </button>
            <button @click="activeTab = 'unit'" 
                    :class="activeTab === 'unit' ? 'bg-kpi-red text-white' : 'btn-secondary'" 
                    class="text-xs font-semibold py-2 px-4 rounded-xl transition-all active:scale-[0.98]">
                Perbandingan Unit Kerja
            </button>
            <button @click="activeTab = 'jenis'" 
                    :class="activeTab === 'jenis' ? 'bg-kpi-red text-white' : 'btn-secondary'" 
                    class="text-xs font-semibold py-2 px-4 rounded-xl transition-all active:scale-[0.98]">
                Breakdown Jenis Cuti
            </button>
        </div>
    </div>
</x-app-layout>
