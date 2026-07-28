<x-app-layout title="Data Pegawai">
    {{-- Alpine View State Wrapper --}}
    <div x-data="{ viewMode: localStorage.getItem('pegawai_view_mode') || 'table' }"
         x-init="$watch('viewMode', val => localStorage.setItem('pegawai_view_mode', val))"
         class="space-y-6">

        {{-- 1. Stat Cards Grid --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Total Pegawai --}}
            <div class="card card-glow-sky card-hover group transition-all duration-300 hover:-translate-y-1.5 animate-fade-in-up" style="animation-delay: 50ms;">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400">Total Pegawai</p>
                        <p class="stat-figure mt-2.5 transition-transform duration-300 group-hover:scale-[1.03]">{{ $totalPegawai }}</p>
                    </div>
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 text-white shadow-[0_4px_14px_rgba(14,165,233,0.3)] dark:from-sky-600 dark:to-blue-700 transition-all duration-300 group-hover:scale-110">
                        <svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 100-8 4 4 0 000 8zm6 3.13a4 4 0 00-3-3.87"/></svg>
                    </div>
                </div>
                <p class="mt-4 text-xs text-stone-500 dark:text-stone-400 flex items-center gap-1.5">
                    <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                    Pegawai aktif & nonaktif terdaftar
                </p>
            </div>

            {{-- Pegawai Keaktifan --}}
            <div class="card card-glow-emerald card-hover group transition-all duration-300 hover:-translate-y-1.5 animate-fade-in-up" style="animation-delay: 100ms;">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400">Keaktifan Pegawai</p>
                        <p class="stat-figure mt-2.5 transition-transform duration-300 group-hover:scale-[1.03]">{{ $aktifCount }}<span class="text-xs font-sans font-normal text-kpi-gray">/ {{ $totalPegawai }}</span></p>
                    </div>
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-[0_4px_14px_rgba(16,185,129,0.3)] dark:from-emerald-600 dark:to-teal-700 transition-all duration-300 group-hover:scale-110">
                        <svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-stone-100 dark:bg-white/10">
                        <div class="h-full rounded-full bg-emerald-500 transition-all duration-1000 ease-out" style="width: {{ $totalPegawai > 0 ? min(100, round($aktifCount / $totalPegawai * 100)) : 0 }}%"></div>
                    </div>
                    <div class="mt-2 flex items-center justify-between text-[11px] text-stone-500 dark:text-stone-400">
                        <span>Proporsi Pegawai Aktif</span>
                        <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $nonaktifCount }} Nonaktif</span>
                    </div>
                </div>
            </div>

            {{-- Breakdown Status Kepegawaian --}}
            <div class="card card-glow-red card-hover group transition-all duration-300 hover:-translate-y-1.5 animate-fade-in-up" style="animation-delay: 150ms;">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400">Status Kepegawaian</p>
                    </div>
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-rose-500 to-red-600 text-white shadow-[0_4px_14px_rgba(244,63,94,0.3)] dark:from-rose-600 dark:to-red-700 transition-all duration-300 group-hover:scale-110">
                        <svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                </div>
                <div class="flex flex-col gap-1.5 mt-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-kpi-gray flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-sky-500"></span> PNS</span>
                        <strong class="font-semibold text-kpi-black dark:text-stone-100">{{ $pnsCount }}</strong>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-kpi-gray flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-kpi-gold"></span> PPPK</span>
                        <strong class="font-semibold text-kpi-black dark:text-stone-100">{{ $pppkCount }}</strong>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-kpi-gray flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-rose-500"></span> Non-ASN</span>
                        <strong class="font-semibold text-kpi-black dark:text-stone-100">{{ $nonAsnCount }}</strong>
                    </div>
                </div>
            </div>

            {{-- Butuh Tindakan --}}
            <a href="{{ route('cuti.index', ['status' => 'menunggu']) }}"
               class="card card-glow-amber card-hover group transition-all duration-300 hover:-translate-y-1.5 animate-fade-in-up"
               style="animation-delay: 200ms;">

                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400">Butuh Tindakan</p>
                        <div class="mt-2.5 flex items-center gap-2">
                            <p class="stat-figure transition-transform duration-300 group-hover:scale-[1.03]">{{ $totalButuhTindakan }}</p>
                            @if($totalButuhTindakan > 0)
                                <span class="inline-flex items-center rounded-full bg-orange-100 px-2 py-0.5 text-[10px] font-bold text-orange-700 ring-1 ring-orange-200 dark:bg-orange-500/20 dark:text-orange-300 dark:ring-orange-500/30 animate-pulse">
                                    Perlu Aksi
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-orange-500 to-rose-600 text-white shadow-[0_4px_14px_rgba(249,115,22,0.35)] dark:from-orange-600 dark:to-rose-700 transition-all duration-300 group-hover:scale-110">
                        <svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                </div>

                <div class="mt-3 flex flex-col gap-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-kpi-gray flex items-center gap-1.5">
                            <span class="h-2 w-2 rounded-full bg-orange-500"></span>
                            Pengajuan Cuti
                        </span>
                        <strong class="font-semibold {{ $pendingCuti > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-kpi-black dark:text-stone-100' }}">{{ $pendingCuti }}</strong>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-kpi-gray flex items-center gap-1.5">
                            <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                            Verifikasi Pelatihan
                        </span>
                        <strong class="font-semibold {{ $pendingPelatihan > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-kpi-black dark:text-stone-100' }}">{{ $pendingPelatihan }}</strong>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-kpi-gray flex items-center gap-1.5">
                            <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                            Perubahan Data
                        </span>
                        <strong class="font-semibold {{ $pendingPerubahan > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-kpi-black dark:text-stone-100' }}">{{ $pendingPerubahan }}</strong>
                    </div>
                </div>
            </a>
        </div>

        {{-- 2. Grafik Distribusi & Widget Pegawai Baru --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in-up" style="animation-delay: 250ms;">
            {{-- Horizontal Bar Chart --}}
            <div class="card lg:col-span-2 shadow-[var(--shadow-card)]">
                <div class="mb-4">
                    <h2 class="font-serif text-lg font-semibold">Distribusi Pegawai per Unit Kerja</h2>
                    <p class="text-xs text-kpi-gray mt-0.5">Jumlah pegawai aktif & nonaktif pada masing-masing unit kerja</p>
                </div>
                <div class="h-80">
                    <canvas id="unitChartCanvas"></canvas>
                </div>
            </div>

            {{-- Pegawai Baru Bergabung Widget --}}
            <div class="card shadow-[var(--shadow-card)]">
                <div>
                    <h2 class="font-serif text-lg font-semibold">Pegawai Baru Bergabung</h2>
                    <p class="text-xs text-kpi-gray mt-0.5">Daftar pegawai dengan TMT terbaru</p>
                </div>
                <ul class="mt-4 divide-y divide-kpi-line dark:divide-white/5">
                    @forelse($pegawaiBaru as $pb)
                        <li class="py-3 flex items-center justify-between gap-3 transition-colors hover:bg-stone-50/50 dark:hover:bg-white/[0.01] rounded-lg px-2 -mx-2">
                            <div class="flex items-center gap-3 min-w-0">
                                @if($pb->foto)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($pb->foto) }}" alt="{{ $pb->nama }}" class="h-9 w-9 shrink-0 rounded-full object-cover">
                                @else
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-kpi-gold-soft text-xs font-semibold text-kpi-red-dark dark:bg-kpi-gold/20 dark:text-kpi-gold">
                                        {{ strtoupper(substr($pb->nama, 0, 2)) }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <a href="{{ route('pegawai.show', $pb) }}" class="text-sm font-bold text-kpi-black hover:text-kpi-red hover:underline dark:text-stone-100 truncate block">
                                        {{ $pb->nama }}
                                    </a>
                                    <p class="text-xs text-kpi-gray truncate">{{ $pb->jabatan?->nama_jabatan ?? '—' }}</p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="mono text-[10.5px] font-semibold text-kpi-gold bg-kpi-gold-soft/30 px-2 py-0.5 rounded dark:bg-kpi-gold/10 whitespace-nowrap">
                                    {{ $pb->tmt->translatedFormat('d M Y') }}
                                </span>
                            </div>
                        </li>
                    @empty
                        <li class="empty-state !py-10">
                            <p class="text-sm text-kpi-gray">Belum ada data TMT pegawai.</p>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- 3. Filter Bar & View Toggle --}}
        @php
            $unitOptions = [['value' => '', 'label' => 'Semua Unit']];
            foreach ($units as $unit) {
                $unitOptions[] = ['value' => (string)$unit->id, 'label' => $unit->nama_unit];
            }
        @endphp
        <div class="relative z-20 mb-4 flex flex-wrap items-center justify-between gap-3 bg-white/40 p-4 rounded-2xl border border-kpi-line dark:border-white/10 dark:bg-kpi-dark-surface/40 backdrop-blur">
            <form method="GET" class="flex flex-1 flex-wrap items-center gap-2">
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama atau NIP..."
                       class="input max-w-xs">
                <x-select name="unit_id" :value="$filters['unit_id'] ?? ''" :options="$unitOptions" class="w-full max-w-[180px]" />
                <x-select name="status_kepegawaian" :value="$filters['status_kepegawaian'] ?? ''" :options="[
                    ['value' => '', 'label' => 'Semua Status'],
                    ['value' => 'PNS', 'label' => 'PNS'],
                    ['value' => 'PPPK', 'label' => 'PPPK'],
                    ['value' => 'Non-ASN', 'label' => 'Non-ASN']
                ]" class="w-full max-w-[150px]" />
                <x-select name="status_aktif" :value="$filters['status_aktif'] ?? ''" :options="[
                    ['value' => '', 'label' => 'Aktif & Nonaktif'],
                    ['value' => 'aktif', 'label' => 'Aktif'],
                    ['value' => 'nonaktif', 'label' => 'Nonaktif']
                ]" class="w-full max-w-[140px]" />
                <button class="btn-secondary">Filter</button>
            </form>
            
            <div class="flex items-center gap-3 shrink-0">
                {{-- View Mode Toggle Buttons --}}
                <div class="flex items-center gap-1 rounded-lg border border-stone-300 bg-white p-0.5 dark:border-white/10 dark:bg-white/5">
                    <button @click="viewMode = 'table'"
                            :class="viewMode === 'table' ? 'bg-kpi-red text-white' : 'text-kpi-gray hover:bg-stone-100 dark:hover:bg-white/5'"
                            class="rounded px-2 py-1.5 transition-colors"
                            title="Tampilan Tabel">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <button @click="viewMode = 'grid'"
                            :class="viewMode === 'grid' ? 'bg-kpi-red text-white' : 'text-kpi-gray hover:bg-stone-100 dark:hover:bg-white/5'"
                            class="rounded px-2 py-1.5 transition-colors"
                            title="Tampilan Grid/Kartu">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    </button>
                </div>

                <a href="{{ route('pegawai.create') }}" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Pegawai
                </a>
            </div>
        </div>

        <div id="live-list-container" class="space-y-6">
            {{-- 4. View Mode: Table --}}
            <div x-show="viewMode === 'table'" class="table-shell">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-kpi-line bg-kpi-cream/60 dark:border-white/10 dark:bg-white/[0.03]">
                        <tr>
                            <th class="th">Pegawai</th>
                            <th class="th">NIP</th>
                            <th class="th">Jabatan</th>
                            <th class="th">Unit</th>
                            <th class="th">Status</th>
                            <th class="th">Aktif</th>
                            <th class="th text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-kpi-line dark:divide-white/5">
                        @forelse ($pegawais as $pegawai)
                            <tr class="tr-hover cursor-pointer" onclick="if (!event.target.closest('a, button, form')) window.location='{{ route('pegawai.show', $pegawai) }}'">
                                <td class="flex items-center gap-3 px-4 py-3.5">
                                    @if($pegawai->foto)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($pegawai->foto) }}" alt="{{ $pegawai->nama }}" class="h-9 w-9 shrink-0 rounded-full object-cover">
                                    @else
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-kpi-gold-soft text-xs font-semibold text-kpi-red-dark dark:bg-kpi-gold/20 dark:text-kpi-gold">
                                            {{ strtoupper(substr($pegawai->nama, 0, 2)) }}
                                        </div>
                                    @endif
                                    <a href="{{ route('pegawai.show', $pegawai) }}" class="font-medium hover:text-kpi-red">{{ $pegawai->nama }}</a>
                                </td>
                                <td class="td mono text-kpi-gray">{{ $pegawai->nip }}</td>
                                <td class="td">{{ $pegawai->jabatan?->nama_jabatan ?? '—' }}</td>
                                <td class="td">{{ $pegawai->unit?->nama_unit ?? '—' }}</td>
                                <td class="td"><x-badge color="info">{{ $pegawai->status_kepegawaian }}</x-badge></td>
                                <td class="td">
                                    <x-badge :color="$pegawai->status_aktif === 'aktif' ? 'success' : 'default'">{{ ucfirst($pegawai->status_aktif) }}</x-badge>
                                </td>
                                <td class="td text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('pegawai.edit', $pegawai) }}" class="btn-xs-secondary">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6.586-6.586a2 2 0 112.828 2.828L11.828 13.828H9V11z"/></svg>
                                            Ubah
                                        </a>
                                        <form method="POST" action="{{ route('pegawai.destroy', $pegawai) }}" class="inline"
                                              onsubmit="return confirm('Hapus data pegawai {{ $pegawai->nama }}?')">
                                            @csrf @method('DELETE')
                                            <button class="btn-xs-danger">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7">
                                <div class="empty-state">
                                    <svg class="h-8 w-8 text-kpi-gray/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 100-8 4 4 0 000 8zm6 3.13a4 4 0 00-3-3.87"/></svg>
                                    <p class="text-sm text-kpi-gray">Belum ada data pegawai.</p>
                                </div>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 5. View Mode: Grid/Card --}}
            <div x-show="viewMode === 'grid'" x-cloak class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 animate-fade-in-up">
                @forelse ($pegawais as $pegawai)
                    <a href="{{ route('pegawai.show', $pegawai) }}" class="card card-hover group flex flex-col justify-between p-5 transition-all duration-300 hover:-translate-y-1 block cursor-pointer">
                        <div class="flex items-center gap-4">
                            @if($pegawai->foto)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($pegawai->foto) }}" alt="{{ $pegawai->nama }}" class="h-12 w-12 shrink-0 rounded-full object-cover border border-kpi-line dark:border-white/10">
                            @else
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-kpi-gold-soft text-sm font-semibold text-kpi-red-dark dark:bg-kpi-gold/20 dark:text-kpi-gold">
                                    {{ strtoupper(substr($pegawai->nama, 0, 2)) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <h3 class="font-serif font-bold text-kpi-black dark:text-stone-100 group-hover:text-kpi-red transition-colors truncate">{{ $pegawai->nama }}</h3>
                                <p class="mono text-xs text-kpi-gray truncate mt-0.5">{{ $pegawai->nip }}</p>
                            </div>
                        </div>
                        <div class="mt-4 border-t border-kpi-line pt-3 dark:border-white/10 text-xs text-kpi-gray leading-relaxed">
                            <p class="truncate"><strong class="text-kpi-black dark:text-stone-200">Jabatan:</strong> {{ $pegawai->jabatan?->nama_jabatan ?? '—' }}</p>
                            <p class="truncate mt-1"><strong class="text-kpi-black dark:text-stone-200">Unit:</strong> {{ $pegawai->unit?->nama_unit ?? '—' }}</p>
                        </div>
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-2 border-t border-kpi-line pt-3 dark:border-white/10">
                            <x-badge color="info">{{ $pegawai->status_kepegawaian }}</x-badge>
                            <x-badge :color="$pegawai->status_aktif === 'aktif' ? 'success' : 'default'">{{ ucfirst($pegawai->status_aktif) }}</x-badge>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full card py-12 flex flex-col items-center justify-center gap-2">
                        <svg class="h-8 w-8 text-kpi-gray/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 100-8 4 4 0 000 8zm6 3.13a4 4 0 00-3-3.87"/></svg>
                        <p class="text-sm text-kpi-gray text-center">Belum ada data pegawai.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-4">{{ $pegawais->links() }}</div>
        </div>

    </div>

    {{-- Chart.js Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartCanvas = document.getElementById('unitChartCanvas');
            if (chartCanvas) {
                const ctx = chartCanvas.getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($distribusiUnit->pluck('nama_unit')) !!},
                        datasets: [{
                            label: 'Jumlah Pegawai',
                            data: {!! json_encode($distribusiUnit->pluck('pegawais_count')) !!},
                            backgroundColor: 'rgba(193, 39, 45, 0.75)',
                            borderColor: '#C1272D',
                            borderWidth: 1.5,
                            borderRadius: 6,
                            borderSkipped: false,
                            maxBarThickness: 24,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(28, 23, 18, 0.95)',
                                padding: 10,
                                titleFont: { family: 'Instrument Sans', size: 11, weight: 'bold' },
                                bodyFont: { family: 'Instrument Sans', size: 12 },
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return context.parsed.x + ' Pegawai';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    color: '#8C857B',
                                    font: { family: 'IBM Plex Mono', size: 10 }
                                },
                                grid: { color: 'rgba(130, 130, 130, 0.08)' }
                            },
                            y: {
                                ticks: {
                                    color: '#8C857B',
                                    font: { family: 'Instrument Sans', size: 11, weight: 'bold' }
                                },
                                grid: { display: false }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
