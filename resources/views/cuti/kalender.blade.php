@php
    $prevMonth = $month == 1 ? 12 : $month - 1;
    $prevYear = $month == 1 ? $year - 1 : $year;
    
    $nextMonth = $month == 12 ? 1 : $month + 1;
    $nextYear = $month == 12 ? $year + 1 : $year;

    $unitOptions = [['value' => '', 'label' => 'Semua Unit Kerja']];
    foreach ($units as $u) {
        $unitOptions[] = ['value' => (string)$u->id, 'label' => $u->nama_unit];
    }
@endphp
<x-app-layout title="Cuti & Izin — Kalender Tim">
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

    {{-- Legend --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-kpi-line bg-white/40 p-4 dark:border-white/10 dark:bg-white/[0.02]">
        <div class="flex flex-wrap items-center gap-4 text-xs">
            <span class="font-medium text-kpi-gray">Legenda Jenis Cuti:</span>
            <span class="flex items-center gap-1.5"><span class="h-3 w-3 rounded bg-blue-500"></span> Tahunan</span>
            <span class="flex items-center gap-1.5"><span class="h-3 w-3 rounded bg-red-500"></span> Sakit</span>
            <span class="flex items-center gap-1.5"><span class="h-3 w-3 rounded bg-emerald-500"></span> Melahirkan</span>
            <span class="flex items-center gap-1.5"><span class="h-3 w-3 rounded bg-amber-500"></span> Lainnya</span>
            <span class="flex items-center gap-1.5 border-l border-kpi-line pl-4 dark:border-white/10"><span class="h-3 w-3 rounded border border-dashed border-stone-400"></span> Menunggu Approval (Faded/Dashed)</span>
        </div>
    </div>

    {{-- Filter & Month Navigation Controls --}}
    <div class="relative z-20 mb-5 flex flex-wrap items-center justify-between gap-4">
        {{-- Filter Form --}}
        <form method="GET" class="flex flex-wrap items-center gap-2">
            <input type="hidden" name="year" value="{{ $year }}">
            <input type="hidden" name="month" value="{{ $month }}">
            <x-select name="unit_id" :value="$filters['unit_id'] ?? ''" :options="$unitOptions" class="w-full max-w-[200px]" />
            @php
                $jcOptions = [['value' => '', 'label' => 'Semua Jenis']];
                foreach ($jenisCutis as $jc) {
                    $jcOptions[] = ['value' => (string)$jc->id, 'label' => $jc->nama];
                }
            @endphp
            <x-select name="jenis_cuti" :value="$filters['jenis_cuti'] ?? ''" :options="$jcOptions" class="w-full max-w-[180px]" />
            <button class="btn-secondary">Filter</button>
        </form>

        {{-- Month Navigator --}}
        <div class="flex items-center gap-1">
            <a href="{{ route('cuti.kalender', array_merge(request()->query(), ['month' => $prevMonth, 'year' => $prevYear])) }}" 
               class="btn-secondary !p-2" title="Bulan Sebelumnya">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <a href="{{ route('cuti.kalender', request()->except(['month', 'year'])) }}" 
               class="btn-secondary text-xs py-2 px-3">
                Hari Ini
            </a>
            <h2 class="text-sm font-bold font-serif mx-2 text-kpi-black dark:text-stone-100">{{ $currentMonthLabel }}</h2>
            <a href="{{ route('cuti.kalender', array_merge(request()->query(), ['month' => $nextMonth, 'year' => $nextYear])) }}" 
               class="btn-secondary !p-2" title="Bulan Berikutnya">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>

    {{-- Calendar Grid --}}
    <div class="card p-4 shadow-[var(--shadow-card)] overflow-x-auto">
        <div class="min-w-[700px]">
            {{-- Day names header --}}
            <div class="grid grid-cols-7 gap-1.5 mb-2">
                @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $dayName)
                    <div class="text-center font-semibold text-xs py-2 bg-kpi-cream/60 dark:bg-white/[0.03] text-kpi-gray rounded-lg">
                        {{ $dayName }}
                    </div>
                @endforeach
            </div>

            {{-- Grid cells --}}
            <div class="grid grid-cols-7 gap-1.5">
                @foreach ($days as $day)
                    @php
                        $isCurrentMonth = $day->month == $month;
                        $isToday = $day->isToday();
                        $dayCutis = $cutis->filter(function($c) use ($day) {
                            return $day->between($c->tanggal_mulai, $c->tanggal_selesai);
                        });
                    @endphp
                    <div class="min-h-[110px] border border-kpi-line dark:border-white/5 p-2 flex flex-col rounded-xl transition-colors
                                {{ !$isCurrentMonth ? 'bg-stone-50/30 dark:bg-stone-900/10 opacity-40' : 'bg-white/50 dark:bg-kpi-dark-surface/30' }} 
                                {{ $isToday ? 'ring-2 ring-kpi-red ring-inset bg-kpi-red-soft/20 dark:bg-kpi-red/5' : '' }}">
                        
                        <div class="flex items-center justify-between mb-1.5">
                            @if ($isToday)
                                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-kpi-red text-[10px] font-bold text-white shadow-sm">
                                    {{ $day->day }}
                                </span>
                            @else
                                <span class="text-xs font-mono font-medium text-kpi-gray dark:text-stone-400">
                                    {{ $day->day }}
                                </span>
                            @endif
                        </div>

                        <div class="flex-1 space-y-1.5 overflow-y-auto max-h-[80px]">
                            @foreach ($dayCutis as $c)
                                @php
                                    $jenisVal = $c->jenisCuti ? $c->jenisCuti->nama : $c->jenis_cuti;
                                    $colorClass = match(strtolower($jenisVal)) {
                                        'cuti tahunan', 'tahunan' => 'bg-blue-500/10 text-blue-700 border-blue-500/30 dark:bg-blue-500/20 dark:text-blue-400',
                                        'sakit/cuti sakit', 'sakit' => 'bg-red-500/10 text-red-700 border-red-500/30 dark:bg-red-500/20 dark:text-red-400',
                                        'cuti bersalin anak ke-1 s.d 2', 'cuti bersalin anak ke-3 dst.', 'melahirkan' => 'bg-emerald-500/10 text-emerald-700 border-emerald-500/30 dark:bg-emerald-500/20 dark:text-emerald-400',
                                        default => 'bg-amber-500/10 text-amber-700 border-amber-500/30 dark:bg-amber-500/20 dark:text-amber-400',
                                    };
                                    $isPending = in_array($c->status, ['menunggu_atasan', 'menunggu_hr']);
                                    $pendingStyle = $isPending ? 'border-dashed opacity-75' : 'border-solid';
                                @endphp
                                <a href="{{ route('cuti.show', $c) }}" 
                                   class="block text-[10px] leading-snug p-1 rounded-lg border {{ $colorClass }} {{ $pendingStyle }} truncate transition-all hover:scale-[1.02] hover:shadow-sm"
                                   title="{{ $c->pegawai->nama }} ({{ $c->jenis_cuti }}): {{ $c->alasan }}">
                                    @if ($isPending)
                                        <span class="inline-block w-1 h-1 rounded-full bg-current animate-ping mr-0.5"></span>
                                    @endif
                                    <span class="font-medium">{{ $c->pegawai->nama }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
