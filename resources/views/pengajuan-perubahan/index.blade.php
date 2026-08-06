@php
    $fieldLabels = [
        'no_hp' => 'Nomor HP Utama',
        'email' => 'Email Resmi',
        'email_pribadi' => 'Email Pribadi',
        'alamat' => 'Alamat Lengkap',
        'nama_panggilan' => 'Nama Panggilan',
        'status_marital' => 'Status Pernikahan',
        'golongan_darah' => 'Golongan Darah',
        'agama' => 'Agama',
        'hobi' => 'Hobi',
        'koordinat_domisili' => 'Koordinat Titik Domisili WFH',
    ];

    $fieldOptions = [['value' => '', 'label' => 'Semua Field']];
    foreach ($fieldLabels as $k => $lbl) {
        $fieldOptions[] = ['value' => $k, 'label' => $lbl];
    }

    $firstItem = $pengajuans->first();
@endphp

<x-app-layout title="Pengajuan Perubahan Data">
    <div x-data="{ 
        selectedId: {{ $firstItem ? $firstItem->id : 'null' }},
        statusFilter: '',
        fieldFilter: '',
        searchQuery: '',
        showTolakForm: false,

        selectItem(id) {
            this.selectedId = id;
            this.showTolakForm = false;
            if (window.innerWidth < 1024 && this.$refs.detailPanel) {
                this.$nextTick(() => {
                    this.$refs.detailPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            }
        },

        matchesFilter(status, field, nama, nip) {
            const matchStatus = !this.statusFilter || status === this.statusFilter;
            const matchField = !this.fieldFilter || field === this.fieldFilter;
            const q = this.searchQuery.toLowerCase().trim();
            const matchSearch = !q || nama.toLowerCase().includes(q) || (nip && nip.toLowerCase().includes(q));
            return matchStatus && matchField && matchSearch;
        }
    }" class="space-y-6">

        {{-- Banner Edukasi Top --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-2xl border border-amber-200/80 bg-amber-50/60 p-4 dark:border-amber-500/20 dark:bg-amber-500/[0.04]">
            <div class="flex items-center gap-3.5">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 text-white shadow-sm dark:from-amber-600 dark:to-amber-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <h3 class="font-serif text-sm font-bold text-stone-800 dark:text-stone-100">Verifikasi Perubahan Data Pegawai</h3>
                    <p class="text-xs text-stone-600 dark:text-stone-400">Pengajuan dari SIMPEG Mobile perlu ditinjau sebelum otomatis memperbarui profil resmi pegawai.</p>
                </div>
            </div>
            @if($counts['menunggu'] > 0)
                <span class="inline-flex items-center gap-1.5 shrink-0 rounded-full bg-amber-500/15 px-3.5 py-1.5 text-xs font-semibold text-amber-800 dark:bg-amber-500/25 dark:text-amber-300">
                    <span class="h-2 w-2 animate-ping rounded-full bg-amber-500"></span>
                    {{ $counts['menunggu'] }} Perlu Diperiksa
                </span>
            @endif
        </div>

        {{-- Filter & Live Search Toolbar --}}
        <div class="relative z-40 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between bg-white/40 p-4 rounded-2xl border border-kpi-line dark:border-white/10 dark:bg-kpi-dark-surface/40 backdrop-blur">
            {{-- Status Tabs Live Filter --}}
            <div class="flex items-center gap-2 overflow-x-auto pb-1 lg:pb-0 no-scrollbar">
                <button type="button" @click="statusFilter = ''" 
                        :class="statusFilter === '' 
                           ? 'border-kpi-red bg-kpi-red text-white shadow-sm' 
                           : 'border-kpi-line bg-white text-kpi-gray hover:bg-stone-50 dark:border-white/10 dark:bg-kpi-dark-surface dark:text-stone-300 dark:hover:bg-white/5'"
                        class="inline-flex shrink-0 items-center gap-1.5 rounded-xl border px-3.5 py-2 text-xs font-semibold transition-all">
                    Semua
                    <span class="mono rounded-full px-1.5 py-0.5 text-[10px]" :class="statusFilter === '' ? 'bg-white/20 text-white' : 'bg-stone-100 text-stone-600 dark:bg-white/10 dark:text-stone-300'">
                        {{ $counts['semua'] }}
                    </span>
                </button>

                <button type="button" @click="statusFilter = 'menunggu'" 
                        :class="statusFilter === 'menunggu' 
                           ? 'border-amber-500 bg-amber-500 text-white shadow-sm' 
                           : 'border-kpi-line bg-white text-kpi-gray hover:bg-stone-50 dark:border-white/10 dark:bg-kpi-dark-surface dark:text-stone-300 dark:hover:bg-white/5'"
                        class="inline-flex shrink-0 items-center gap-1.5 rounded-xl border px-3.5 py-2 text-xs font-semibold transition-all">
                    Menunggu
                    <span class="mono rounded-full px-1.5 py-0.5 text-[10px]" :class="statusFilter === 'menunggu' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300'">
                        {{ $counts['menunggu'] }}
                    </span>
                </button>

                <button type="button" @click="statusFilter = 'disetujui'" 
                        :class="statusFilter === 'disetujui' 
                           ? 'border-emerald-600 bg-emerald-600 text-white shadow-sm' 
                           : 'border-kpi-line bg-white text-kpi-gray hover:bg-stone-50 dark:border-white/10 dark:bg-kpi-dark-surface dark:text-stone-300 dark:hover:bg-white/5'"
                        class="inline-flex shrink-0 items-center gap-1.5 rounded-xl border px-3.5 py-2 text-xs font-semibold transition-all">
                    Disetujui
                    <span class="mono rounded-full px-1.5 py-0.5 text-[10px]" :class="statusFilter === 'disetujui' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300'">
                        {{ $counts['disetujui'] }}
                    </span>
                </button>

                <button type="button" @click="statusFilter = 'ditolak'" 
                        :class="statusFilter === 'ditolak' 
                           ? 'border-kpi-red bg-kpi-red text-white shadow-sm' 
                           : 'border-kpi-line bg-white text-kpi-gray hover:bg-stone-50 dark:border-white/10 dark:bg-kpi-dark-surface dark:text-stone-300 dark:hover:bg-white/5'"
                        class="inline-flex shrink-0 items-center gap-1.5 rounded-xl border px-3.5 py-2 text-xs font-semibold transition-all">
                    Ditolak
                    <span class="mono rounded-full px-1.5 py-0.5 text-[10px]" :class="statusFilter === 'ditolak' ? 'bg-white/20 text-white' : 'bg-kpi-red-soft text-kpi-red-dark dark:bg-kpi-red/20 dark:text-red-300'">
                        {{ $counts['ditolak'] }}
                    </span>
                </button>
            </div>

            {{-- Controls: Live Search + Custom Dropdown --}}
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
                {{-- Live Search Input --}}
                <div class="relative w-full sm:w-64">
                    <svg class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-kpi-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" x-model="searchQuery" placeholder="Live search pegawai / NIP..." class="input pl-10 text-xs w-full shadow-[var(--shadow-card)]">
                </div>

                {{-- Custom Select Dropdown for Field Filter --}}
                <div @change="fieldFilter = $event.target.value" class="w-full sm:w-48">
                    <x-select name="field_filter" value="" :options="$fieldOptions" class="w-full" />
                </div>

                {{-- Reset Button --}}
                <button type="button" x-show="statusFilter || fieldFilter || searchQuery" @click="statusFilter = ''; fieldFilter = ''; searchQuery = '';" class="btn-xs-secondary text-xs w-full sm:w-auto justify-center">
                    Reset Filter
                </button>
            </div>
        </div>

        @if($pengajuans->isEmpty())
            {{-- Empty State --}}
            <div class="rounded-2xl border border-kpi-line bg-white p-12 text-center dark:border-white/10 dark:bg-kpi-dark-surface">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-stone-100 text-kpi-gray dark:bg-white/5">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536M9 11l6.586-6.586a2 2 0 112.828 2.828L11.828 13.828H9V11z"/></svg>
                </div>
                <h3 class="mt-4 font-serif text-base font-bold text-kpi-black dark:text-stone-100">Belum ada pengajuan perubahan data</h3>
                <p class="mt-1 text-xs text-kpi-gray">Pengajuan data dari SIMPEG Mobile akan muncul di sini.</p>
            </div>
        @else
            {{-- Split-Pane Master-Detail Container --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 relative z-10">
                
                {{-- Left Pane: Request Queue List (5 Cols) --}}
                <div class="lg:col-span-5 space-y-3">
                    <div class="flex items-center justify-between px-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-kpi-gray">DAFTAR PENGAJUAN ({{ $counts['semua'] }})</span>
                        <span class="text-[11px] text-kpi-gray">Ketuk kartu untuk inspeksi</span>
                    </div>

                    <div class="space-y-2.5 max-h-[660px] overflow-y-auto pr-1">
                        @foreach($pengajuans as $p)
                            <div x-show="matchesFilter('{{ $p->status }}', '{{ $p->field }}', '{{ addslashes($p->pegawai->nama) }}', '{{ $p->pegawai->nip }}')"
                                 @click="selectItem({{ $p->id }})"
                                 :class="selectedId == {{ $p->id }} 
                                     ? 'border-kpi-red bg-white shadow-md ring-1 ring-kpi-red/30 dark:bg-white/[0.06] dark:border-kpi-red' 
                                     : 'border-kpi-line bg-white/80 hover:bg-white hover:border-stone-300 dark:border-white/10 dark:bg-kpi-dark-surface dark:hover:bg-white/5'"
                                 class="relative cursor-pointer rounded-2xl border p-4 transition-all duration-200 group overflow-hidden">
                                
                                {{-- Active Accent Bar --}}
                                <div x-show="selectedId == {{ $p->id }}" class="absolute left-0 top-3 bottom-3 w-1.5 rounded-r-full bg-kpi-red"></div>

                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0 flex-1">
                                        @if($p->pegawai->foto)
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($p->pegawai->foto) }}" alt="{{ $p->pegawai->nama }}" class="h-11 w-11 shrink-0 rounded-xl object-cover shadow-sm">
                                        @else
                                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-kpi-gold-soft text-xs font-bold text-kpi-red-dark dark:bg-kpi-gold/20 dark:text-kpi-gold">
                                                {{ strtoupper(substr($p->pegawai->nama, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div class="min-w-0 flex-1">
                                            <h4 class="truncate font-serif text-sm font-bold text-kpi-black dark:text-stone-100 group-hover:text-kpi-red transition-colors">{{ $p->pegawai->nama }}</h4>
                                            <p class="truncate text-[11.5px] text-kpi-gray">NIP: {{ $p->pegawai->nip }} &middot; {{ $p->pegawai->unit?->nama_unit ?? '—' }}</p>
                                        </div>
                                    </div>
                                    <div class="shrink-0">
                                        <x-badge :color="$p->status === 'disetujui' ? 'success' : ($p->status === 'ditolak' ? 'danger' : 'warning')">
                                            {{ ucfirst($p->status) }}
                                        </x-badge>
                                    </div>
                                </div>

                                <div class="mt-3 flex items-center justify-between border-t border-kpi-line/60 pt-2.5 dark:border-white/5">
                                    <span class="rounded-md bg-stone-100 px-2.5 py-0.5 text-[11px] font-semibold text-stone-700 dark:bg-white/10 dark:text-stone-300 truncate max-w-[180px]">
                                        {{ $fieldLabels[$p->field] ?? str_replace('_', ' ', $p->field) }}
                                    </span>
                                    <span class="text-[11px] text-kpi-gray flex items-center gap-1 font-mono shrink-0">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $p->created_at->format('d M H:i') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Right Pane: Inspection & Approval Detail Panel (7 Cols) --}}
                <div class="lg:col-span-7" x-ref="detailPanel">
                    @foreach($pengajuans as $p)
                        <div x-show="selectedId == {{ $p->id }}" 
                             class="sticky top-6 rounded-2xl border border-kpi-line bg-white p-5 sm:p-6 shadow-[var(--shadow-card)] dark:border-white/10 dark:bg-kpi-dark-surface space-y-6">
                            
                            {{-- Header Inspector --}}
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 border-b border-kpi-line pb-5 dark:border-white/10">
                                <div class="flex items-center gap-4">
                                    @if($p->pegawai->foto)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($p->pegawai->foto) }}" alt="{{ $p->pegawai->nama }}" class="h-14 w-14 shrink-0 rounded-2xl object-cover shadow-sm">
                                    @else
                                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-kpi-gold-soft text-base font-bold text-kpi-red-dark dark:bg-kpi-gold/20 dark:text-kpi-gold">
                                            {{ strtoupper(substr($p->pegawai->nama, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <h3 class="font-serif text-lg font-bold text-kpi-black dark:text-stone-50">{{ $p->pegawai->nama }}</h3>
                                        <p class="text-xs text-kpi-gray">NIP: {{ $p->pegawai->nip }} &middot; {{ $p->pegawai->unit?->nama_unit ?? '—' }}</p>
                                        <div class="mt-1.5 flex items-center gap-2">
                                            <span class="inline-flex items-center gap-1 rounded-md bg-stone-100 px-2.5 py-0.5 text-[11px] font-semibold text-stone-700 dark:bg-white/10 dark:text-stone-300">
                                                Field: {{ $fieldLabels[$p->field] ?? str_replace('_', ' ', $p->field) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="shrink-0 self-start">
                                    <x-badge :color="$p->status === 'disetujui' ? 'success' : ($p->status === 'ditolak' ? 'danger' : 'warning')">
                                        {{ ucfirst($p->status) }}
                                    </x-badge>
                                </div>
                            </div>

                            {{-- Tanggal Pengajuan Detail Banner --}}
                            <div class="rounded-xl border border-kpi-line/80 bg-stone-50/70 p-3.5 dark:border-white/5 dark:bg-white/[0.02] flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-xs">
                                <div class="flex items-center gap-2 text-kpi-gray">
                                    <svg class="h-4 w-4 text-kpi-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>Tanggal Pengajuan:</span>
                                    <strong class="text-kpi-black dark:text-stone-200">{{ $p->created_at->translatedFormat('d F Y \j\a\m H:i') }} WIB</strong>
                                </div>
                                <span class="text-kpi-gray">({{ $p->created_at->diffForHumans() }})</span>
                            </div>

                            {{-- Side-by-Side Comparison Box --}}
                            <div class="space-y-2.5">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-kpi-gray">PERBANDINGAN PERUBAHAN DATA</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    {{-- Nilai Lama --}}
                                    <div class="rounded-2xl border border-stone-200 bg-stone-50 p-4 dark:border-white/5 dark:bg-white/[0.01]">
                                        <span class="text-xs font-semibold text-stone-500">Nilai Lama (Saat Ini)</span>
                                        <p class="mt-2 font-mono text-base font-medium text-stone-500 line-through decoration-stone-400/60 break-words">
                                            {{ $p->nilai_lama ?: '— (Kosong)' }}
                                        </p>
                                    </div>
                                    {{-- Nilai Baru --}}
                                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4 dark:border-emerald-500/20 dark:bg-emerald-500/[0.04]">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">Nilai Baru (Diajukan)</span>
                                            <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <p class="mt-2 font-mono text-base font-bold text-emerald-800 dark:text-emerald-300 break-words">
                                            {{ $p->nilai_baru }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Footprint / Penolakan Admin --}}
                            @if($p->status !== 'menunggu')
                                <div class="rounded-xl border border-kpi-line bg-stone-50/50 p-4 dark:border-white/5 dark:bg-white/[0.01] space-y-2 text-xs">
                                    <div class="flex items-center justify-between">
                                        <span class="text-kpi-gray">Diproses Oleh:</span>
                                        <span class="font-semibold text-kpi-black dark:text-stone-200">{{ $p->diprosesOleh?->name ?? 'Admin HR' }}</span>
                                    </div>
                                    @if($p->catatan_admin)
                                        <div class="mt-2 border-t border-kpi-line pt-2 text-kpi-red dark:text-red-300">
                                            <strong>Catatan Penolakan:</strong>
                                            <p class="mt-1 font-serif text-stone-700 dark:text-stone-300">{{ $p->catatan_admin }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            {{-- Action Bar --}}
                            @if($p->status === 'menunggu')
                                <div class="border-t border-kpi-line pt-4 dark:border-white/10">
                                    <div x-show="!showTolakForm" class="flex flex-col sm:flex-row items-center justify-end gap-3">
                                        <button type="button" @click="showTolakForm = true" class="btn-danger w-full sm:w-auto">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Tolak Pengajuan
                                        </button>
                                        
                                        <form method="POST" action="{{ route('pengajuan-perubahan.setujui', $p) }}" class="w-full sm:w-auto">
                                            @csrf @method('PATCH')
                                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menyetujui dan langsung menerapkan perubahan data ini ke profil resmi pegawai?')" class="btn-primary w-full sm:w-auto">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Setujui & Terapkan Profil
                                            </button>
                                        </form>
                                    </div>

                                    {{-- Form In-Place Penolakan --}}
                                    <div x-show="showTolakForm" x-cloak class="rounded-xl border border-red-200 bg-kpi-red-soft/60 p-4 dark:border-red-500/20 dark:bg-kpi-red/[0.08]">
                                        <form method="POST" action="{{ route('pengajuan-perubahan.tolak', $p) }}" class="space-y-3">
                                            @csrf @method('PATCH')
                                            <div>
                                                <label class="label text-xs font-bold text-kpi-red-dark dark:text-red-300">Alasan / Catatan Penolakan (Opsional)</label>
                                                <textarea name="catatan_admin" rows="3" class="input mt-1 w-full text-xs" placeholder="Tuliskan alasan mengapa perubahan ini ditolak..."></textarea>
                                            </div>
                                            <div class="flex justify-end gap-2">
                                                <button type="button" @click="showTolakForm = false" class="btn-secondary !text-xs py-1.5 px-3">Batal</button>
                                                <button type="submit" class="btn-danger !text-xs py-1.5 px-3">Konfirmasi Penolakan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
