<x-app-layout title="Pendidikan & Pelatihan">
    @php
        $units = \App\Models\UnitKerja::orderBy('nama_unit')->get();
        $unitOptions = [['value' => '', 'label' => 'Semua Unit Kerja']];
        foreach ($units as $u) {
            $unitOptions[] = ['value' => (string)$u->id, 'label' => $u->nama_unit];
        }
    @endphp
    {{-- Stat Cards Grid --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        
        {{-- Capaian Diklat --}}
        <div class="card card-glow-amber card-hover group transition-all duration-300 hover:-translate-y-1.5 animate-fade-in-up" style="animation-delay: 50ms;">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400">Capaian Diklat &ge;{{ $targetJp }} JP</p>
                    <p class="stat-figure mt-2.5 transition-transform duration-300 group-hover:scale-[1.03]">{{ $capaianDiklatCount }}<span class="text-xs font-sans font-normal text-kpi-gray">/ {{ $totalPegawai }} pegawai</span></p>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-yellow-600 text-white shadow-[0_4px_14px_rgba(245,158,11,0.3)] dark:from-amber-600 dark:to-yellow-700 transition-all duration-300 group-hover:scale-110">
                    <svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0118 20.5H6a12.083 12.083 0 01.84-9.922L12 14z"/></svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="h-1.5 w-full overflow-hidden rounded-full bg-stone-100 dark:bg-white/10">
                    <div class="h-full rounded-full bg-kpi-gold transition-all duration-1000 ease-out" style="width: {{ $totalPegawai > 0 ? min(100, round($capaianDiklatCount / $totalPegawai * 100)) : 0 }}%"></div>
                </div>
                <div class="mt-2 flex items-center justify-between text-[11px] text-stone-500 dark:text-stone-400">
                    <span>Target Capaian</span>
                    <span class="font-semibold text-kpi-gold">{{ $totalPegawai > 0 ? round($capaianDiklatCount / $totalPegawai * 100) : 0 }}% Pegawai</span>
                </div>
            </div>
        </div>

        {{-- Menunggu Verifikasi --}}
        <a href="{{ route('pelatihan.index', ['status_verifikasi' => 'menunggu']) }}" class="card card-glow-red card-hover group transition-all duration-300 hover:-translate-y-1.5 animate-fade-in-up block" style="animation-delay: 100ms;">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400">Menunggu Verifikasi</p>
                    <p class="stat-figure mt-2.5 transition-transform duration-300 group-hover:scale-[1.03]">{{ $menungguVerifikasiCount }}</p>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-kpi-red to-kpi-red-dark text-white shadow-[0_4px_14px_rgba(193,39,45,0.3)] dark:from-kpi-red dark:to-kpi-red-dark transition-all duration-300 group-hover:scale-110">
                    <svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
            <p class="mt-4 inline-flex items-center gap-1 text-xs font-bold text-kpi-red hover:text-kpi-red-dark transition-colors">
                Tinjau Pengajuan
                <svg class="h-3 w-3 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </p>
        </a>

        {{-- Rata-rata JP per Pegawai --}}
        <div class="card card-glow-sky card-hover group transition-all duration-300 hover:-translate-y-1.5 animate-fade-in-up" style="animation-delay: 150ms;">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400">Rata-rata JP per Pegawai</p>
                    <p class="stat-figure mt-2.5 transition-transform duration-300 group-hover:scale-[1.03]">{{ $rataRataJp }} <span class="text-sm font-sans font-normal text-kpi-gray">JP</span></p>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 text-white shadow-[0_4px_14px_rgba(14,165,233,0.3)] dark:from-sky-600 dark:to-blue-700 transition-all duration-300 group-hover:scale-110">
                    <svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
            </div>
            <p class="mt-4 text-xs text-stone-500 dark:text-stone-400 flex items-center gap-1.5">
                <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                Rata-rata akumulasi tahun ini
            </p>
        </div>

        {{-- Total Pelatihan Tercatat --}}
        <div class="card card-glow-emerald card-hover group transition-all duration-300 hover:-translate-y-1.5 animate-fade-in-up" style="animation-delay: 200ms;">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400">Total Pelatihan Tercatat</p>
                    <p class="stat-figure mt-2.5 transition-transform duration-300 group-hover:scale-[1.03]">{{ $totalPelatihanTahunIni }} <span class="text-xs font-sans font-normal text-kpi-gray">diklat</span></p>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-[0_4px_14px_rgba(16,185,129,0.3)] dark:from-emerald-600 dark:to-teal-700 transition-all duration-300 group-hover:scale-110">
                    <svg class="h-5.5 w-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <p class="mt-4 text-xs text-stone-500 dark:text-stone-400 flex items-center gap-1.5">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                Tercatat pada tahun {{ now()->year }}
            </p>
        </div>

    </div>

    <div x-data="{ 
        tab: '{{ request()->get('tab') }}' || localStorage.getItem('pelatihan_active_tab') || 'daftar', 
        showRejectModal: false, 
        rejectActionUrl: '' 
    }" x-init="$watch('tab', val => localStorage.setItem('pelatihan_active_tab', val))">
        <div class="mb-5 flex gap-1 overflow-x-auto border-b border-kpi-line dark:border-white/10">
            <button @click="tab = 'daftar'; const u = new URL(window.location.href); u.searchParams.set('tab', 'daftar'); window.history.replaceState({}, '', u.toString());" :class="tab === 'daftar' ? 'border-kpi-red text-kpi-red' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200'" class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors">Daftar Pelatihan</button>
            <button @click="tab = 'rekap'; const u = new URL(window.location.href); u.searchParams.set('tab', 'rekap'); window.history.replaceState({}, '', u.toString());" :class="tab === 'rekap' ? 'border-kpi-red text-kpi-red' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200'" class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors">Rekap Capaian JP</button>
        </div>

        <div x-show="tab === 'daftar'">
            <div class="relative z-20 mb-4 flex flex-wrap items-center justify-between gap-3 bg-white/40 p-4 rounded-2xl border border-kpi-line dark:border-white/10 dark:bg-kpi-dark-surface/40 backdrop-blur">
                <form method="GET" class="flex flex-1 flex-wrap items-center gap-2.5">
                    <input type="hidden" name="tab" value="daftar">
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama pegawai..." class="input w-full sm:w-56">
                    <x-select name="status_verifikasi" :value="$filters['status_verifikasi'] ?? ''" :options="[
                        ['value' => '', 'label' => 'Semua Status Verifikasi'],
                        ['value' => 'menunggu', 'label' => 'Menunggu'],
                        ['value' => 'terverifikasi', 'label' => 'Terverifikasi'],
                        ['value' => 'ditolak', 'label' => 'Ditolak']
                    ]" class="w-full sm:w-52" />
                    <x-select name="kategori" :value="$filters['kategori'] ?? ''" :options="[
                        ['value' => '', 'label' => 'Semua Kategori'],
                        ['value' => 'struktural', 'label' => 'Struktural'],
                        ['value' => 'fungsional', 'label' => 'Fungsional'],
                        ['value' => 'teknis', 'label' => 'Teknis'],
                        ['value' => 'latsar', 'label' => 'Latsar'],
                        ['value' => 'lainnya', 'label' => 'Lainnya']
                    ]" class="w-full sm:w-44" />
                    <button class="btn-secondary">Filter</button>
                </form>
            </div>

            <div id="live-list-container" class="space-y-4">
                <div class="table-shell">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-kpi-line bg-kpi-cream/60 dark:border-white/10 dark:bg-white/[0.03]">
                            <tr>
                                <th class="th">Pegawai</th>
                                <th class="th">Nama Kursus</th>
                                <th class="th">Bentuk / Tipe</th>
                                <th class="th">Tanggal</th>
                                <th class="th">JP</th>
                                <th class="th">Status</th>
                                @if(auth()->user()->role === 'admin')<th class="th text-right">Aksi</th>@endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-kpi-line dark:divide-white/5">
                            @forelse ($pelatihans as $p)
                                <tr class="tr-hover cursor-pointer" onclick="if (!event.target.closest('a, button, form')) window.location='{{ route('pelatihan.show', $p) }}'">
                                    <td class="td">
                                        <a href="{{ route('pegawai.show', $p->pegawai) }}" class="font-medium hover:text-kpi-red">{{ $p->pegawai->nama }}</a>
                                        <p class="text-xs text-kpi-gray">{{ $p->pegawai->unit?->nama_unit }}</p>
                                    </td>
                                    <td class="td">{{ $p->nama_pelatihan }}</td>
                                    <td class="td">
                                        <span class="font-medium text-stone-700 dark:text-stone-300">{{ $p->bentukPelatihan?->nama_bentuk ?? '—' }}</span>
                                        <p class="text-xs text-kpi-gray">{{ $p->tipeKursus?->nama_tipe ?? '—' }}</p>
                                    </td>
                                    <td class="td mono text-xs">
                                        {{ $p->tanggal->format('d M Y') }}
                                        @if($p->tanggal_akhir)
                                            <p class="text-[10px] text-kpi-gray mt-0.5">s.d. {{ $p->tanggal_akhir->format('d M Y') }}</p>
                                        @endif
                                    </td>
                                    <td class="td">{{ $p->durasi_jp }}</td>
                                    <td class="td">
                                        <x-badge :color="$p->status_verifikasi === 'terverifikasi' ? 'success' : ($p->status_verifikasi === 'ditolak' ? 'danger' : 'warning')">
                                            {{ ucfirst($p->status_verifikasi) }}
                                        </x-badge>
                                    </td>
                                    @if(auth()->user()->role === 'admin')
                                    <td class="td text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if($p->status_verifikasi === 'menunggu')
                                                <form method="POST" action="{{ route('pelatihan.verifikasi', $p) }}" class="inline">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="keputusan" value="terverifikasi">
                                                    <button type="submit" class="btn-xs-success">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                        Verifikasi
                                                    </button>
                                                </form>
                                                <button type="button" @click="rejectActionUrl = '{{ route('pelatihan.verifikasi', $p) }}'; showRejectModal = true" class="btn-xs-danger">
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    Tolak
                                                </button>
                                            @endif
                                            <form method="POST" action="{{ route('pelatihan.destroy', $p) }}" class="inline" onsubmit="return confirm('Hapus data ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-xs-danger">
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                            @empty
                                <tr><td colspan="7">
                                    <div class="empty-state">
                                        <svg class="h-8 w-8 text-kpi-gray/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0118 20.5H6a12.083 12.083 0 01.84-9.922L12 14z"/></svg>
                                        <p class="text-sm text-kpi-gray">Belum ada data pelatihan.</p>
                                    </div>
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 flex flex-wrap items-center justify-between gap-4 border-t border-kpi-line pt-4 dark:border-white/10">
                    <div class="flex items-center gap-2.5">
                        <x-per-page :current="request('per_page', 15)" />
                        <span class="text-xs text-kpi-gray dark:text-stone-400">
                            (Total <strong class="text-kpi-black dark:text-stone-200">{{ $pelatihans->total() }}</strong> entri)
                        </span>
                    </div>
                    <div class="clean-pagination">
                        {{ $pelatihans->links() }}
                    </div>
                </div>
            </div>
        </div>

        <div x-show="tab === 'rekap'" x-cloak
             x-data="{
                 searchQuery: '',
                 unitId: '',
                 statusCapaian: '',
                 currentPage: 1,
                 perPage: 15,
                 rekapData: [
                     @foreach ($rekapPegawai as $p)
                     {
                         id: {{ $p->id }},
                         nama: {{ json_encode($p->nama) }},
                         nip: {{ json_encode($p->nip) }},
                         foto: {{ json_encode($p->foto ? \Illuminate\Support\Facades\Storage::url($p->foto) : null) }},
                         unit_id: '{{ $p->unit_id }}',
                         unit_nama: {{ json_encode($p->unit?->nama_unit ?? '') }},
                         total_pelatihan: {{ (int) $p->total_pelatihan }},
                         pelatihan_terakhir: '{{ $p->pelatihan_terakhir ? \Carbon\Carbon::parse($p->pelatihan_terakhir)->translatedFormat('d M Y') : 'Belum ada' }}',
                         jp: {{ (int) ($p->jp_tahun_ini ?? 0) }},
                         memenuhi: {{ (int) ($p->jp_tahun_ini ?? 0) >= $targetJp ? 1 : 0 }},
                         detail_url: '{{ route('pelatihan.pegawai', $p) }}'
                     },
                     @endforeach
                 ],
                 init() {
                     const urlParams = new URLSearchParams(window.location.search);
                     this.searchQuery = urlParams.get('rekap_q') || '';
                     this.unitId = urlParams.get('rekap_unit_id') || '';
                     this.statusCapaian = urlParams.get('rekap_status') || '';
                     
                     this.$watch('searchQuery', () => this.currentPage = 1);
                     this.$watch('unitId', () => this.currentPage = 1);
                     this.$watch('statusCapaian', () => this.currentPage = 1);
                 },
                 get filteredData() {
                     return this.rekapData.filter(item => {
                         const matchQuery = !this.searchQuery || item.nama.toLowerCase().includes(this.searchQuery.toLowerCase()) || item.nip.toLowerCase().includes(this.searchQuery.toLowerCase());
                         const matchUnit = !this.unitId || String(item.unit_id) === String(this.unitId);
                         const matchStatus = !this.statusCapaian || 
                                             (this.statusCapaian === 'memenuhi' && item.memenuhi === 1) || 
                                             (this.statusCapaian === 'belum_memenuhi' && item.memenuhi === 0);
                         return matchQuery && matchUnit && matchStatus;
                     });
                 },
                 get totalPages() {
                     return Math.ceil(this.filteredData.length / this.perPage) || 1;
                 },
                 get paginatedData() {
                     const start = (this.currentPage - 1) * this.perPage;
                     return this.filteredData.slice(start, start + this.perPage);
                 }
             }">

            {{-- Filter Bar untuk Rekap --}}
            <div class="relative z-40 mb-4 flex flex-wrap items-center gap-3 bg-white/40 p-4 rounded-2xl border border-kpi-line dark:border-white/10 dark:bg-kpi-dark-surface/40 backdrop-blur">
                <div class="relative w-full sm:w-64">
                    <svg class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-kpi-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" x-model="searchQuery" placeholder="Cari nama pegawai / NIP..." class="input pl-10 text-xs w-full shadow-[var(--shadow-card)]">
                </div>
                
                <div @change="unitId = $event.target.value" class="w-full sm:w-52">
                    <x-select name="rekap_unit" 
                              :value="request()->get('rekap_unit_id', '')" 
                              :options="$unitOptions"
                              class="w-full" />
                </div>

                <div @change="statusCapaian = $event.target.value" class="w-full sm:w-52">
                    <x-select name="rekap_status" 
                              :value="request()->get('rekap_status', '')" 
                              :options="[
                                  ['value' => '', 'label' => 'Semua Status Capaian'],
                                  ['value' => 'memenuhi', 'label' => 'Memenuhi'],
                                  ['value' => 'belum_memenuhi', 'label' => 'Belum Memenuhi']
                              ]"
                              class="w-full" />
                </div>

                <button type="button" x-show="searchQuery || unitId || statusCapaian" @click="searchQuery = ''; unitId = ''; statusCapaian = '';" class="btn-xs-secondary text-xs">
                    Reset Filter
                </button>
            </div>

            <div class="table-shell">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-kpi-line bg-kpi-cream/60 dark:border-white/10 dark:bg-white/[0.03]">
                        <tr>
                            <th class="th">Pegawai</th>
                            <th class="th">Unit</th>
                            <th class="th">Total Pelatihan</th>
                            <th class="th">Pelatihan Terakhir Diikuti</th>
                            <th class="th">Progres JP Tahun Ini</th>
                            <th class="th">Status</th>
                            <th class="th text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-kpi-line dark:divide-white/5">
                        <template x-for="row in paginatedData" :key="row.id">
                            <tr class="tr-hover cursor-pointer" @click="if (!event.target.closest('a')) window.location = row.detail_url">
                                <td class="td">
                                    <div class="flex items-center gap-3">
                                        <template x-if="row.foto">
                                            <img :src="row.foto" :alt="row.nama" class="h-9 w-9 shrink-0 rounded-xl object-cover shadow-sm">
                                        </template>
                                        <template x-if="!row.foto">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-kpi-gold-soft text-xs font-bold text-kpi-red-dark dark:bg-kpi-gold/20 dark:text-kpi-gold"
                                                 x-text="row.nama.substring(0, 2).toUpperCase()"></div>
                                        </template>
                                        <div class="min-w-0">
                                            <a :href="row.detail_url" class="font-serif text-sm font-bold text-kpi-black dark:text-stone-100 hover:text-kpi-red transition-colors block truncate" x-text="row.nama"></a>
                                            <p class="text-[11px] text-kpi-gray truncate" x-text="'NIP: ' + row.nip"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="td text-kpi-gray text-xs">
                                    <span class="rounded-md bg-stone-100 px-2 py-0.5 font-medium text-stone-700 dark:bg-white/10 dark:text-stone-300 truncate inline-block max-w-[180px]" x-text="row.unit_nama || '—'"></span>
                                </td>
                                <td class="td font-medium text-stone-700 dark:text-stone-300">
                                    <span x-text="row.total_pelatihan"></span> tercatat
                                </td>
                                <td class="td text-kpi-gray mono text-xs" x-text="row.pelatihan_terakhir"></td>
                                <td class="td">
                                    <div class="flex items-center gap-3">
                                        <div class="h-1.5 w-32 overflow-hidden rounded-full bg-stone-100 dark:bg-white/10">
                                            <div class="h-full rounded-full transition-all duration-500" 
                                                 :class="row.memenuhi === 1 ? 'bg-emerald-500' : 'bg-kpi-gold'" 
                                                 :style="'width: ' + ({{ $targetJp }} > 0 ? Math.min(100, Math.round(row.jp / {{ $targetJp }} * 100)) : 0) + '%'"></div>
                                        </div>
                                        <span class="mono text-xs text-kpi-gray">
                                            <span x-text="row.jp"></span> / {{ $targetJp }} JP
                                        </span>
                                    </div>
                                </td>
                                <td class="td">
                                    <template x-if="row.memenuhi === 1">
                                        <x-badge color="success">Memenuhi</x-badge>
                                    </template>
                                    <template x-if="row.memenuhi !== 1">
                                        <x-badge color="danger">Belum Memenuhi</x-badge>
                                    </template>
                                </td>
                                <td class="td text-right">
                                    <a :href="row.detail_url" class="btn-xs-secondary">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredData.length === 0">
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <svg class="h-8 w-8 text-kpi-gray/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0118 20.5H6a12.083 12.083 0 01.84-9.922L12 14z"/></svg>
                                        <p class="text-sm text-kpi-gray">Tidak ada data rekap pegawai yang cocok.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            {{-- Alpine Client-side Instant Pagination Footer (No Page Reload!) --}}
            <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-stone-600 dark:text-stone-400">
                <div>
                    Showing <span class="font-semibold text-kpi-black dark:text-stone-200" x-text="filteredData.length > 0 ? (currentPage - 1) * perPage + 1 : 0"></span>
                    to <span class="font-semibold text-kpi-black dark:text-stone-200" x-text="Math.min(currentPage * perPage, filteredData.length)"></span>
                    of <span class="font-semibold text-kpi-black dark:text-stone-200" x-text="filteredData.length"></span> results
                </div>
                
                <div class="inline-flex items-center rounded-xl border border-stone-200 bg-white shadow-sm dark:border-white/10 dark:bg-kpi-dark-surface overflow-hidden" x-show="totalPages > 1">
                    {{-- Previous Button --}}
                    <button type="button" 
                            @click="if (currentPage > 1) currentPage--" 
                            :disabled="currentPage === 1" 
                            class="flex h-9 w-9 items-center justify-center border-r border-stone-200 text-stone-500 hover:bg-stone-50 disabled:opacity-40 disabled:hover:bg-transparent dark:border-white/10 dark:text-stone-400 dark:hover:bg-white/5 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    
                    {{-- Page Numbers (1, 2, 3, 4, 5...) --}}
                    <template x-for="p in totalPages" :key="p">
                        <button type="button" 
                                @click="currentPage = p" 
                                :class="currentPage === p 
                                   ? 'bg-stone-200/90 font-bold text-stone-900 dark:bg-white/20 dark:text-stone-100' 
                                   : 'bg-white text-stone-600 hover:bg-stone-50 dark:bg-transparent dark:text-stone-300 dark:hover:bg-white/5'" 
                                class="flex h-9 min-w-[36px] items-center justify-center border-r border-stone-200 px-3 text-xs transition-colors dark:border-white/10" 
                                x-text="p"></button>
                    </template>

                    {{-- Next Button --}}
                    <button type="button" 
                            @click="if (currentPage < totalPages) currentPage++" 
                            :disabled="currentPage === totalPages" 
                            class="flex h-9 w-9 items-center justify-center text-stone-500 hover:bg-stone-50 disabled:opacity-40 disabled:hover:bg-transparent dark:text-stone-400 dark:hover:bg-white/5 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Modal Alasan Penolakan --}}
        <template x-teleport="body">
            <div x-show="showRejectModal" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                <div x-show="showRejectModal" x-transition.scale.95 @click.outside="showRejectModal = false" class="w-full max-w-md rounded-2xl border border-kpi-line bg-white p-6 shadow-2xl dark:border-white/10 dark:bg-kpi-dark-surface">
                    <h3 class="font-serif text-lg font-bold text-kpi-black dark:text-stone-50">Tolak Verifikasi Pelatihan</h3>
                    <p class="mt-2 text-sm text-kpi-gray">Silakan masukkan alasan penolakan untuk riwayat pelatihan ini. Alasan ini akan ditampilkan kepada pegawai.</p>
                    
                    <form method="POST" :action="rejectActionUrl" class="mt-4 space-y-4">
                        @csrf @method('PATCH')
                        <input type="hidden" name="keputusan" value="ditolak">
                        <div>
                            <label for="catatan" class="eyebrow block mb-1.5">Alasan Penolakan</label>
                            <textarea id="catatan" name="catatan" rows="3" required class="input w-full resize-none" placeholder="Contoh: Berkas sertifikat tidak valid atau tidak terbaca..."></textarea>
                        </div>
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="showRejectModal = false" class="btn-secondary">Batal</button>
                            <button type="submit" class="btn-danger">Ya, Tolak</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>
