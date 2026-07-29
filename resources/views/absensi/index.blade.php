<x-app-layout title="Absensi">
    {{-- Tab Navigation --}}
    <div class="mb-6 flex gap-1 overflow-x-auto border-b border-kpi-line dark:border-white/10 no-scrollbar">
        <a href="{{ route('absensi.index') }}"
           class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-semibold transition-colors border-kpi-red text-kpi-red">
            Absensi Harian
        </a>
        @if(auth()->user()->role === 'admin')
        <a href="{{ route('absensi.shift.index', 1) }}"
           class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200">
            Jadwal Shift
        </a>
        @endif
    </div>

    {{-- Top Stat Cards Grid --}}
    <div class="mb-6 grid grid-cols-2 gap-3.5 sm:grid-cols-3 lg:grid-cols-5">
        {{-- Hadir --}}
        <div class="card !p-4 card-glow-emerald card-hover transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400">Hadir</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-600 dark:bg-emerald-500/25 dark:text-emerald-400">
                    <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
            <p class="stat-figure mt-2.5 !text-2xl font-bold text-emerald-700 dark:text-emerald-400">{{ $rekapHariIni['hadir'] }}</p>
        </div>

        {{-- Telat --}}
        <div class="card !p-4 card-glow-amber card-hover transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400">Telat</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-500/15 text-amber-600 dark:bg-amber-500/25 dark:text-amber-400">
                    <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="stat-figure mt-2.5 !text-2xl font-bold text-amber-700 dark:text-amber-400">{{ $rekapHariIni['telat'] }}</p>
        </div>

        {{-- Izin/Sakit --}}
        <div class="card !p-4 card-glow-sky card-hover transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400">Izin / Sakit</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-500/15 text-sky-600 dark:bg-sky-500/25 dark:text-sky-400">
                    <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="stat-figure mt-2.5 !text-2xl font-bold text-sky-700 dark:text-sky-400">{{ $rekapHariIni['izin_sakit'] }}</p>
        </div>

        {{-- Alpa --}}
        <div class="card !p-4 card-glow-red card-hover transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400">Alpa</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-500/15 text-rose-600 dark:bg-rose-500/25 dark:text-rose-400">
                    <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
            </div>
            <p class="stat-figure mt-2.5 !text-2xl font-bold text-rose-700 dark:text-rose-400">{{ $rekapHariIni['alpa'] }}</p>
        </div>

        {{-- Belum Presensi --}}
        <div class="card !p-4 card-hover transition-all duration-300 group col-span-2 sm:col-span-1">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400">Belum Presensi</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-stone-500/15 text-stone-600 dark:bg-white/10 dark:text-stone-300">
                    <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="stat-figure mt-2.5 !text-2xl font-bold text-stone-600 dark:text-stone-400">{{ $rekapHariIni['belum_presensi'] }}</p>
        </div>
    </div>

    @php
        $unitOptions = [['value' => '', 'label' => 'Semua Unit']];
        foreach ($units as $unit) {
            $unitOptions[] = ['value' => (string)$unit->id, 'label' => $unit->nama_unit];
        }
        $jkOptions = [['value' => '', 'label' => 'Semua Ketidakhadiran']];
        foreach ($jenisKetidakhadirans as $jk) {
            $jkOptions[] = ['value' => (string)$jk->id, 'label' => $jk->nama];
        }
    @endphp

    @php
        $wfhEnabledWeb = (bool) \App\Models\Pengaturan::get('wfh_enabled', '1');
        $wfhDaysWeb = json_decode(\App\Models\Pengaturan::get('wfh_days', '["friday"]'), true) ?? ['friday'];
        $selectedDayName = strtolower(\Carbon\Carbon::parse($tanggal)->format('l'));
        $isHariWfhWeb = $wfhEnabledWeb && in_array($selectedDayName, $wfhDaysWeb);
    @endphp

    @if($isHariWfhWeb)
        <div class="mb-5 flex items-center justify-between rounded-2xl border border-emerald-200 bg-emerald-50/80 p-4 dark:border-emerald-500/20 dark:bg-emerald-500/[0.04]">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-sm">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
                <div>
                    <h4 class="font-serif text-sm font-bold text-emerald-900 dark:text-emerald-300">Mode WFH (Work From Home) Aktif Hari Ini</h4>
                    <p class="text-xs text-emerald-700 dark:text-emerald-400">Presensi pegawai pada hari {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }} dipandu oleh sistem radius koordinat domisili pegawai.</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3.5 py-1 text-xs font-bold text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300 shrink-0">
                🏡 Presensi Domisili
            </span>
        </div>
    @endif

    {{-- Filter & Live Search Toolbar Container --}}
    <div class="relative z-40 mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between bg-white/40 p-4 rounded-2xl border border-kpi-line dark:border-white/10 dark:bg-kpi-dark-surface/40 backdrop-blur">
        <form method="GET" x-data="{ status: '{{ $filters['status'] ?? '' }}' }" class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
            {{-- Datepicker --}}
            <div class="w-full sm:w-auto">
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="input text-xs w-full sm:w-40 shadow-[var(--shadow-card)]">
            </div>

            {{-- Unit Filter --}}
            <div class="w-full sm:w-auto min-w-[170px]">
                <x-select name="unit_id" :value="$filters['unit_id'] ?? ''" :options="$unitOptions" class="w-full" />
            </div>

            {{-- Status Filter --}}
            <div class="w-full sm:w-auto min-w-[150px]" @change="status = $event.target.value">
                <x-select name="status" :value="$filters['status'] ?? ''" :options="[
                    ['value' => '', 'label' => 'Semua Status'],
                    ['value' => 'hadir', 'label' => 'Hadir'],
                    ['value' => 'telat', 'label' => 'Telat'],
                    ['value' => 'izin', 'label' => 'Izin'],
                    ['value' => 'sakit', 'label' => 'Sakit'],
                    ['value' => 'alpa', 'label' => 'Alpa'],
                    ['value' => 'belum_presensi', 'label' => 'Belum Presensi']
                ]" class="w-full" />
            </div>

            {{-- Ketidakhadiran Filter --}}
            <div x-show="status !== 'hadir' && status !== 'telat' && status !== 'belum_presensi' && status !== 'alpa'" x-cloak class="w-full sm:w-auto min-w-[190px]">
                <x-select name="jenis_ketidakhadiran_id" :value="$filters['jenis_ketidakhadiran_id'] ?? ''" :options="$jkOptions" class="w-full" />
            </div>

            {{-- Amber Toggle Pill: Perlu Ditinjau --}}
            <label class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-amber-300 bg-amber-50/80 px-3.5 py-2 text-xs font-semibold text-amber-800 transition hover:bg-amber-100 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300 dark:hover:bg-amber-500/20 shadow-sm"
                   title="Tampilkan hanya presensi yang memiliki indikasi lokasi perlu ditinjau">
                <input type="checkbox" name="perlu_ditinjau" value="1"
                       {{ !empty($filters['perlu_ditinjau']) ? 'checked' : '' }}
                       class="h-3.5 w-3.5 rounded accent-amber-600">
                <svg class="h-3.5 w-3.5 text-amber-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                Perlu Ditinjau
            </label>

            <button type="submit" class="btn-secondary text-xs">Filter</button>
        </form>

        @if(auth()->user()->role === 'admin')
        <a href="{{ route('absensi.create') }}" class="btn-primary shrink-0 w-full lg:w-auto justify-center">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Catat Manual
        </a>
        @endif
    </div>

    {{-- Tabel Absensi --}}
    <div id="live-list-container" class="space-y-4" x-data="absensiDetailModal()">

        <div class="table-shell">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-kpi-line bg-kpi-cream/60 dark:border-white/10 dark:bg-white/[0.03]">
                    <tr>
                        <th class="th">Pegawai</th>
                        <th class="th">Unit</th>
                        <th class="th">Jam Masuk</th>
                        <th class="th">Jam Keluar</th>
                        <th class="th">Status</th>
                        <th class="th">Pengurangan</th>
                        <th class="th">Keterangan</th>
                        <th class="th">Tinjauan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-kpi-line dark:divide-white/5">
                    @forelse ($absensis as $a)
                        @php
                            $rowData = [
                                'nama'                 => $a->pegawai->nama,
                                'nip'                  => $a->pegawai->nip,
                                'unit'                 => $a->pegawai->unit?->nama_unit ?? '—',
                                'tanggal'              => $a->tanggal->translatedFormat('d F Y'),
                                'jam_masuk'            => $a->jam_masuk ? substr($a->jam_masuk, 0, 5) : null,
                                'jam_keluar'           => $a->jam_keluar ? substr($a->jam_keluar, 0, 5) : null,
                                'jam_pulang_diizinkan' => $a->jam_pulang_diizinkan ? substr($a->jam_pulang_diizinkan, 0, 5) : null,
                                'status'               => $a->status,
                                'keterangan'           => $a->keterangan,
                                'jenis_ketidakhadiran' => $a->jenisKetidakhadiran?->nama,
                                'foto_url'             => $a->getFotoMasukUrl(),
                                'lat'                  => $a->latitude_masuk ? (float)$a->latitude_masuk : null,
                                'lng'                  => $a->longitude_masuk ? (float)$a->longitude_masuk : null,
                                'lat_keluar'           => $a->latitude_keluar ? (float)$a->latitude_keluar : null,
                                'lng_keluar'           => $a->longitude_keluar ? (float)$a->longitude_keluar : null,
                                'flag_review'          => (bool) $a->flag_review,
                                'is_mock'              => (bool) $a->is_mock_location,
                                'gps_accuracy'         => $a->gps_accuracy,
                                'catatan_flag'         => $a->catatan_flag,
                                'menit_pengurangan'    => $a->menit_pengurangan_jam_kerja,
                            ];
                        @endphp
                        <tr class="tr-hover cursor-pointer"
                            @click="openDetail({{ Js::from($rowData) }})"
                            title="Klik untuk lihat detail presensi">
                            <td class="td">
                                <div class="flex items-center gap-3">
                                    @if($a->pegawai->foto)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($a->pegawai->foto) }}" alt="{{ $a->pegawai->nama }}" class="h-9 w-9 shrink-0 rounded-xl object-cover shadow-sm">
                                    @else
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-kpi-gold-soft text-xs font-bold text-kpi-red-dark dark:bg-kpi-gold/20 dark:text-kpi-gold">
                                            {{ strtoupper(substr($a->pegawai->nama, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-serif text-sm font-bold text-kpi-black dark:text-stone-100 truncate">{{ $a->pegawai->nama }}</p>
                                        <p class="text-[11px] text-kpi-gray truncate">NIP: {{ $a->pegawai->nip }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="td text-kpi-gray text-xs">
                                <span class="rounded-md bg-stone-100 px-2 py-0.5 font-medium text-stone-700 dark:bg-white/10 dark:text-stone-300 truncate inline-block max-w-[180px]">
                                    {{ $a->pegawai->unit?->nama_unit ?? '—' }}
                                </span>
                            </td>
                            <td class="td mono text-xs font-semibold">
                                {{ $a->jam_masuk ? substr($a->jam_masuk, 0, 5) : '—' }}
                                @if($a->jam_pulang_diizinkan)
                                    <span class="block text-[10px] text-kpi-gray font-sans font-normal">Pulang ≥ {{ substr($a->jam_pulang_diizinkan, 0, 5) }}</span>
                                @endif
                            </td>
                            <td class="td mono text-xs font-semibold">{{ $a->jam_keluar ? substr($a->jam_keluar, 0, 5) : '—' }}</td>
                            <td class="td"><x-badge :color="$a->statusBadgeColor()">{{ ucfirst($a->status) }}</x-badge></td>
                            <td class="td">
                                @if($a->menit_pengurangan_jam_kerja)
                                    <x-badge color="warning">Pulang cepat &minus;{{ $a->menit_pengurangan_jam_kerja }} mnt</x-badge>
                                @else
                                    <span class="text-kpi-gray text-xs">—</span>
                                @endif
                            </td>
                            <td class="td text-kpi-gray">
                                @if($a->jenisKetidakhadiran)
                                    <span class="inline-flex items-center rounded-lg bg-stone-100 dark:bg-white/5 px-2 py-0.5 text-xs font-semibold text-kpi-black dark:text-stone-300 mr-1 shadow-sm">{{ $a->jenisKetidakhadiran->nama }}</span>
                                @endif
                                @if(str_contains($a->keterangan ?? '', 'WFH') || $isHariWfhWeb)
                                    <span class="inline-flex items-center gap-1 rounded-md bg-stone-100 dark:bg-white/10 px-2 py-0.5 text-xs font-bold text-stone-800 dark:text-stone-200 mr-1 border border-stone-200 dark:border-white/10">
                                        🏡 WFH
                                    </span>
                                @endif
                                <span class="text-xs">{{ $a->keterangan ?? '—' }}</span>
                            </td>
                            {{-- Kolom Tinjauan GPS --}}
                            <td class="td">
                                @if($a->flag_review)
                                    <span
                                        title="{{ $a->catatan_flag ?? 'Indikasi memerlukan peninjauan' }}"
                                        class="inline-flex cursor-help items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-[11px] font-semibold text-amber-800 ring-1 ring-amber-300 dark:bg-amber-500/15 dark:text-amber-300 dark:ring-amber-500/30 shadow-sm"
                                    >
                                        <svg class="h-3 w-3 shrink-0 text-amber-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                                        Perlu Ditinjau
                                    </span>
                                @else
                                    <span class="text-kpi-gray text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8">
                            <div class="empty-state">
                                <svg class="h-8 w-8 text-kpi-gray/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-sm text-kpi-gray">Belum ada data absensi pada tanggal ini.</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $absensis->links() }}</div>

        {{-- Modal Detail Presensi --}}
        <div x-show="showModal" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-kpi-black/50 p-4 backdrop-blur-[2px] pt-12 sm:pt-16"
             @keydown.escape.window="close()">

            <div @click.away="close()"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="card w-full max-w-xl shadow-2xl dark:bg-kpi-dark-surface dark:border-white/10 mb-8 max-h-[90vh] flex flex-col">

                {{-- Modal Header --}}
                <div class="flex items-start justify-between border-b border-kpi-line pb-4 dark:border-white/10 shrink-0">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-kpi-red-soft text-sm font-bold uppercase text-kpi-red dark:bg-kpi-red/20 dark:text-rose-300"
                             x-text="detail.nama ? detail.nama.substring(0,2) : ''"></div>
                        <div class="min-w-0">
                            <h3 class="font-serif text-base font-bold text-kpi-black dark:text-stone-50 truncate" x-text="detail.nama"></h3>
                            <p class="text-xs text-kpi-gray truncate" x-text="detail.unit"></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize"
                              :class="{
                                  'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300': detail.status === 'hadir',
                                  'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300': detail.status === 'telat',
                                  'bg-sky-100 text-sky-800 dark:bg-sky-500/20 dark:text-sky-300': detail.status === 'izin' || detail.status === 'sakit',
                                  'bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-300': detail.status === 'alpa',
                                  'bg-stone-100 text-stone-700 dark:bg-white/10 dark:text-stone-300': !['hadir','telat','izin','sakit','alpa'].includes(detail.status)
                              }"
                              x-text="detail.status ? detail.status.charAt(0).toUpperCase() + detail.status.slice(1) : ''">
                        </span>
                        <span x-show="detail.flag_review"
                              class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-semibold text-amber-800 ring-1 ring-amber-300 dark:bg-amber-500/15 dark:text-amber-300 dark:ring-amber-500/30">
                            <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                            Perlu Ditinjau
                        </span>
                        <button @click="close()" class="ml-1 rounded-lg p-1 text-kpi-gray transition hover:bg-stone-100 hover:text-kpi-black dark:hover:bg-white/10 dark:hover:text-stone-200">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Modal Body (Scrollable) --}}
                <div class="space-y-5 py-4 overflow-y-auto max-h-[calc(90vh-140px)] pr-1">
                    {{-- Banner Alasan Perlu Ditinjau --}}
                    <div x-show="detail.flag_review || detail.is_mock"
                         class="rounded-xl border border-amber-300/80 bg-amber-50/90 p-4 dark:border-amber-500/30 dark:bg-amber-500/10">
                        <div class="flex items-start gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-200/80 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-amber-900 dark:text-amber-200">Indikasi Presensi Perlu Ditinjau</h4>
                                <div class="mt-1.5 space-y-1.5 text-xs text-amber-900 dark:text-amber-200">
                                    <template x-if="detail.is_mock">
                                        <div class="flex items-center gap-1.5 font-bold text-rose-700 dark:text-rose-400">
                                            <span class="inline-block h-2 w-2 rounded-full bg-rose-600 animate-pulse"></span>
                                            Mock Location / Fake GPS Terdeteksi di Perangkat
                                        </div>
                                    </template>
                                    <template x-if="detail.catatan_flag">
                                        <div class="flex items-start gap-1.5 font-medium">
                                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-amber-700 mt-1 shrink-0"></span>
                                            <span x-text="detail.catatan_flag"></span>
                                        </div>
                                    </template>
                                    <template x-if="!detail.catatan_flag && !detail.is_mock">
                                        <div class="flex items-start gap-1.5 font-medium">
                                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-amber-700 mt-1 shrink-0"></span>
                                            <span>Lokasi atau akurasi GPS presensi memerlukan verifikasi manual admin/atasan.</span>
                                        </div>
                                    </template>
                                    <template x-if="detail.gps_accuracy">
                                        <div class="text-[11px] font-mono text-amber-800/80 dark:text-amber-300/80 pt-0.5">
                                            Akurasi Sinyal GPS Device: &plusmn;<span x-text="detail.gps_accuracy"></span> meter
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-x-6 gap-y-3.5 text-sm">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-kpi-gray">Tanggal</p>
                            <p class="mt-0.5 font-medium text-kpi-black dark:text-stone-100" x-text="detail.tanggal || '—'"></p>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-kpi-gray">Jenis Ketidakhadiran</p>
                            <p class="mt-0.5 font-medium text-kpi-black dark:text-stone-100" x-text="detail.jenis_ketidakhadiran || '—'"></p>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-kpi-gray">Jam Masuk</p>
                            <p class="mt-0.5 font-mono font-semibold text-kpi-black dark:text-stone-100" x-text="detail.jam_masuk || '—'"></p>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-kpi-gray">Jam Keluar</p>
                            <p class="mt-0.5 font-mono font-semibold text-kpi-black dark:text-stone-100" x-text="detail.jam_keluar || '—'"></p>
                        </div>
                        <template x-if="detail.menit_pengurangan">
                            <div class="col-span-2">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-amber-700 dark:text-amber-400">Pengurangan Jam Kerja</p>
                                <p class="mt-0.5 text-xs font-semibold text-amber-800 dark:text-amber-300">
                                    Pulang Cepat &minus;<span x-text="detail.menit_pengurangan"></span> Menit
                                </p>
                            </div>
                        </template>
                        <template x-if="detail.keterangan">
                            <div class="col-span-2">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-kpi-gray">Keterangan / Catatan</p>
                                <p class="mt-0.5 text-xs text-kpi-black dark:text-stone-200" x-text="detail.keterangan"></p>
                            </div>
                        </template>
                    </div>

                    {{-- Lokasi Presensi Masuk --}}
                    <div x-show="detail.lat && detail.lng"
                         class="rounded-xl border border-kpi-line bg-stone-50/60 p-3.5 dark:border-white/10 dark:bg-white/[0.03]">
                        <p class="mb-2 text-[11px] font-bold uppercase tracking-wide text-kpi-gray">Lokasi Presensi Masuk</p>
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-1.5 text-xs text-kpi-gray">
                                <svg class="h-3.5 w-3.5 shrink-0 text-kpi-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="font-mono text-[11px]" x-text="detail.lat && detail.lng ? detail.lat.toFixed(6) + ', ' + detail.lng.toFixed(6) : ''"></span>
                            </div>
                            <a :href="'https://www.google.com/maps?q=' + detail.lat + ',' + detail.lng"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-700 ring-1 ring-sky-200 transition hover:bg-sky-100 dark:bg-sky-500/10 dark:text-sky-300 dark:ring-sky-500/30 dark:hover:bg-sky-500/20"
                               @click.stop>
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                Buka di Maps
                            </a>
                        </div>
                    </div>

                    {{-- Lokasi Presensi Keluar --}}
                    <div x-show="detail.lat_keluar && detail.lng_keluar"
                         class="rounded-xl border border-kpi-line bg-stone-50/60 p-3.5 dark:border-white/10 dark:bg-white/[0.03]">
                        <p class="mb-2 text-[11px] font-bold uppercase tracking-wide text-kpi-gray">Lokasi Presensi Pulang/Keluar</p>
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-1.5 text-xs text-kpi-gray">
                                <svg class="h-3.5 w-3.5 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="font-mono text-[11px]" x-text="detail.lat_keluar && detail.lng_keluar ? detail.lat_keluar.toFixed(6) + ', ' + detail.lng_keluar.toFixed(6) : ''"></span>
                            </div>
                            <a :href="'https://www.google.com/maps?q=' + detail.lat_keluar + ',' + detail.lng_keluar"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-700 ring-1 ring-sky-200 transition hover:bg-sky-100 dark:bg-sky-500/10 dark:text-sky-300 dark:ring-sky-500/30 dark:hover:bg-sky-500/20"
                               @click.stop>
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                Buka di Maps
                            </a>
                        </div>
                    </div>

                    {{-- Foto Selfie Presensi (Tanpa terpotong / object-contain) --}}
                    <div x-show="detail.foto_url" class="space-y-2">
                        <div class="flex items-center justify-between">
                            <p class="text-[11px] font-bold uppercase tracking-wide text-kpi-gray">Foto Selfie Presensi</p>
                            <a :href="detail.foto_url"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="inline-flex items-center gap-1 text-xs font-semibold text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300 transition"
                               @click.stop>
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                Lihat Ukuran Penuh
                            </a>
                        </div>
                        <a :href="detail.foto_url"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="group relative block overflow-hidden rounded-xl border border-kpi-line bg-stone-950/90 dark:border-white/10 dark:bg-black/90 p-2"
                           @click.stop>
                            <img :src="detail.foto_url"
                                 alt="Foto selfie presensi"
                                 class="max-h-[380px] w-auto max-w-full mx-auto object-contain rounded-lg transition duration-300 group-hover:scale-[1.01]"
                                 loading="lazy">
                        </a>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="flex justify-end border-t border-kpi-line pt-4 dark:border-white/10 shrink-0">
                    <button @click="close()" class="btn-secondary">Tutup</button>
                </div>
            </div>
        </div>

    </div>

    {{-- Bottom Layout: Unrecorded Employees & Kategori Ketidakhadiran Breakdown --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3 items-start">
        <div class="lg:col-span-2">
            @if ($belumPresensiPegawais->isNotEmpty())
                <div class="card">
                    <div class="flex items-center justify-between border-b border-kpi-line pb-4 dark:border-white/10">
                        <div>
                            <h3 class="font-serif text-base font-semibold">
                                {{ $isPastJamBatasAlpa ? 'Pegawai Alpa' : 'Pegawai Belum Presensi' }}
                            </h3>
                            <p class="text-xs text-kpi-gray mt-0.5">
                                {{ $isPastJamBatasAlpa ? 'Belum mencatat absensi hingga jam batas alpa (' . $jamBatasAlpa . ')' : 'Belum mencatat kehadiran pada tanggal terpilih' }}
                            </p>
                        </div>
                        <span class="badge {{ $isPastJamBatasAlpa ? 'bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-400' : 'bg-stone-100 text-stone-700 dark:bg-white/10 dark:text-stone-300' }} font-bold">
                            {{ $belumPresensiPegawais->count() }} Orang
                        </span>
                    </div>

                    <div class="mt-4 max-h-[380px] overflow-y-auto pr-1.5 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        @foreach ($belumPresensiPegawais as $p)
                            <div class="flex items-center gap-3 rounded-xl border border-kpi-line p-3 dark:border-white/5 bg-white/50 dark:bg-white/[0.01]">
                                <div class="h-8 w-8 rounded-full bg-stone-100 dark:bg-white/10 flex items-center justify-center font-bold text-xs uppercase text-kpi-gray">
                                    {{ substr($p->nama, 0, 2) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-kpi-black dark:text-stone-100 truncate">{{ $p->nama }}</p>
                                    <p class="text-[11px] text-kpi-gray truncate">{{ $p->unit?->nama_unit ?? '—' }}</p>
                                </div>
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium {{ $isPastJamBatasAlpa ? 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300' : 'bg-stone-50 text-stone-600 dark:bg-white/5 dark:text-stone-400' }}">
                                    {{ $isPastJamBatasAlpa ? 'Alpa' : 'Belum' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="card flex flex-col items-center justify-center text-center py-8">
                    <svg class="h-8 w-8 text-emerald-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm font-semibold text-stone-700 dark:text-stone-300">Semua Pegawai Terdata</p>
                    <p class="text-xs text-kpi-gray mt-0.5">Seluruh pegawai aktif telah mencatat kehadiran atau keterangan.</p>
                </div>
            @endif
        </div>

        <div class="lg:col-span-1">
            <div class="card">
                <div class="border-b border-kpi-line pb-4 dark:border-white/10">
                    <h3 class="font-serif text-base font-semibold">Keterangan Ketidakhadiran</h3>
                    <p class="text-xs text-kpi-gray mt-0.5">Jumlah pegawai absen/tugas luar berdasarkan jenis hari ini</p>
                </div>
                <div class="mt-4 space-y-3">
                    @forelse ($breakdownKetidakhadiran as $item)
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-purple-500 shadow-sm"></span>
                                <span class="font-medium text-stone-700 dark:text-stone-300">{{ $item['nama'] }}</span>
                            </div>
                            <span class="mono font-semibold text-stone-800 dark:text-stone-200 bg-stone-100 dark:bg-white/5 px-2.5 py-0.5 rounded-full text-xs">
                                {{ $item['total'] }} Orang
                            </span>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center text-center py-8 text-kpi-gray">
                            <svg class="h-8 w-8 text-stone-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <p class="text-xs">Tidak ada dinas luar/izin tercatat hari ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        function absensiDetailModal() {
            return {
                showModal: false,
                detail: {},

                openDetail(data) {
                    this.detail = data;
                    this.showModal = true;
                },

                close() {
                    this.showModal = false;
                }
            };
        }
    </script>
</x-app-layout>
