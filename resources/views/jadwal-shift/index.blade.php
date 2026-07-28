<x-app-layout title="Absensi">
    @php
        $initialCellsData = [];
        foreach ($pegawais as $p) {
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dateStr = "{$year}-{$month}-" . sprintf('%02d', $d);
                $entry = isset($entries[$p->id][$dateStr]) ? $entries[$p->id][$dateStr]->first() : null;
                $key = "{$p->id}-{$dateStr}";
                $initialCellsData[$key] = [
                    'stasiun_tv' => $entry ? $entry->stasiun_tv : '',
                    'status_shift_id' => $entry ? $entry->status_shift_id : '',
                    'status_name' => ($entry && $entry->statusShift) ? $entry->statusShift->nama : '',
                    'status_warna' => ($entry && $entry->statusShift) ? $entry->statusShift->warna : '',
                    'keterangan' => $entry ? $entry->keterangan : '',
                ];
            }
        }
    @endphp
    {{-- Tab Navigation --}}
    <div class="mb-6 flex gap-1 overflow-x-auto border-b border-kpi-line dark:border-white/10">
        <a href="{{ route('absensi.index') }}" 
           class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200">
            Absensi Harian
        </a>
        <a href="{{ route('absensi.shift.index', ['shift' => 1, 'bulan' => $bulan]) }}" 
           class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors border-kpi-red text-kpi-red font-semibold">
            Jadwal Shift
        </a>
    </div>

    {{-- Sub-tab Pills Navigation --}}
    <div class="mb-5 flex flex-wrap gap-2">
        <a href="{{ route('absensi.shift.index', ['shift' => 1, 'bulan' => $bulan]) }}" 
           class="inline-flex items-center gap-1.5 rounded-xl border px-3.5 py-1.5 text-xs font-semibold tracking-wide transition-all
                  {{ $shift == 1 
                     ? 'border-kpi-red bg-kpi-red text-white' 
                     : 'border-kpi-line bg-white/40 text-kpi-gray hover:bg-stone-50 dark:border-white/10 dark:bg-white/5 dark:text-stone-300 dark:hover:bg-white/10' }}">
            Shift 1 (06.00-14.00)
        </a>
        <a href="{{ route('absensi.shift.index', ['shift' => 2, 'bulan' => $bulan]) }}" 
           class="inline-flex items-center gap-1.5 rounded-xl border px-3.5 py-1.5 text-xs font-semibold tracking-wide transition-all
                  {{ $shift == 2 
                     ? 'border-kpi-red bg-kpi-red text-white' 
                     : 'border-kpi-line bg-white/40 text-kpi-gray hover:bg-stone-50 dark:border-white/10 dark:bg-white/5 dark:text-stone-300 dark:hover:bg-white/10' }}">
            Shift 2 (14.00-22.00)
        </a>
        <a href="{{ route('absensi.shift.index', ['shift' => 3, 'bulan' => $bulan]) }}" 
           class="inline-flex items-center gap-1.5 rounded-xl border px-3.5 py-1.5 text-xs font-semibold tracking-wide transition-all
                  {{ $shift == 3 
                     ? 'border-kpi-red bg-kpi-red text-white' 
                     : 'border-kpi-line bg-white/40 text-kpi-gray hover:bg-stone-50 dark:border-white/10 dark:bg-white/5 dark:text-stone-300 dark:hover:bg-white/10' }}">
            Shift 3 (22.00-06.00)
        </a>
    </div>

    <div x-data="{
        ...shiftCalendar(),
        showHapusModal: false,
        hapusLoading: false,
        hapusJumlahEntri: 0,
        hapusJumlahPegawai: 0,
        hapusError: '',
        fetchHapusCount() {
            this.hapusLoading = true;
            this.hapusError = '';
            fetch(`{{ route('absensi.shift.hitung-entri', $shift) }}?bulan={{ $bulan }}`)
                .then(r => r.json())
                .then(data => {
                    this.hapusJumlahEntri   = data.jumlah_entri;
                    this.hapusJumlahPegawai = data.jumlah_pegawai;
                    this.hapusLoading = false;
                    this.showHapusModal = true;
                })
                .catch(() => {
                    this.hapusLoading = false;
                    this.hapusError = 'Gagal memuat data. Silakan coba lagi.';
                });
        }
    }" class="relative">

        {{-- Page Header --}}
        <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-serif text-xl font-bold">Jadwal Shift {{ $shift }}</h2>
                <p class="text-xs text-kpi-gray">
                    Shift {{ $shift }} ({{ $shift == '1' ? '06.00-14.00' : ($shift == '2' ? '14.00-22.00' : '22.00-06.00') }}). Klik pada sel kalender untuk mengedit TV pantauan atau status hari libur pegawai.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('absensi.shift.import-form', ['shift' => $shift, 'bulan' => $bulan]) }}" class="btn-secondary">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Import Excel
                </a>
                <button
                    type="button"
                    @click="fetchHapusCount()"
                    :disabled="hapusLoading"
                    class="btn-danger disabled:opacity-50 disabled:cursor-not-allowed">
                    <template x-if="!hapusLoading">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </template>
                    <template x-if="hapusLoading">
                        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                    </template>
                    <span x-text="hapusLoading ? 'Memuat...' : 'Hapus Data Bulan Ini'"></span>
                </button>
                <button @click="showAddModal = true" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Pegawai
                </button>
            </div>
        </div>

        {{-- Filter Month & Search Bar --}}
        <div class="card mb-5 !p-4">
            <form method="GET" action="{{ route('absensi.shift.index', $shift) }}" class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <input type="month" name="bulan" value="{{ $bulan }}" @change="$el.form.submit()" class="input max-w-[180px]">
                    <button class="btn-primary">Filter</button>
                </div>
                <div class="flex items-center gap-2">
                    <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama pegawai..." class="input max-w-[200px]">
                    <button class="btn-secondary">Cari</button>
                </div>
            </form>
        </div>

        @if(session('status'))
            <div class="mb-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800/30 p-4 text-sm text-emerald-800 dark:text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        {{-- Grid Kalender --}}
        <div class="overflow-x-auto w-full border border-kpi-line rounded-2xl bg-white shadow-[var(--shadow-card)] dark:border-white/10 dark:bg-kpi-dark-surface">
            <table class="w-full text-left text-xs border-collapse min-w-[1600px]" style="min-width: 1600px;">
                <thead class="border-b border-kpi-line bg-kpi-cream/60 dark:border-white/10 dark:bg-white/[0.03]">
                    <tr>
                        <th class="th p-3 font-semibold sticky left-0 bg-kpi-cream dark:bg-stone-900 z-20 min-w-[200px] border-r border-kpi-line dark:border-white/10">Pegawai</th>
                        <th class="th p-3 font-semibold sticky left-[200px] bg-kpi-cream dark:bg-stone-900 z-20 min-w-[120px] border-r border-kpi-line dark:border-white/10 shadow-[2px_0_5px_rgba(0,0,0,0.05)]">Stasiun TV</th>
                        @for($d = 1; $d <= $daysInMonth; $d++)
                            <th class="th p-2 text-center font-semibold min-w-[45px] border-r border-kpi-line dark:border-white/10">
                                {{ sprintf('%02d', $d) }}
                            </th>
                        @endfor
                    </tr>
                </thead>
                <tbody class="divide-y divide-kpi-line dark:divide-white/5">
                    @forelse ($pegawais as $p)
                        <tr class="tr-hover">
                            <td class="td p-3 font-medium sticky left-0 bg-white dark:bg-kpi-dark-surface z-10 border-r border-kpi-line dark:border-white/10 shadow-[2px_0_5px_rgba(0,0,0,0.05)]">
                                <div>
                                    <p class="font-semibold">{{ $p->nama }}</p>
                                    <p class="text-[10px] text-kpi-gray">{{ $p->unit?->nama_unit ?? '—' }}</p>
                                </div>
                            </td>
                            <td class="td p-3 font-medium sticky left-[200px] bg-white dark:bg-kpi-dark-surface z-10 border-r border-kpi-line dark:border-white/10 shadow-[2px_0_5px_rgba(0,0,0,0.05)] text-stone-700 dark:text-stone-300">
                                {{ $p->stasiun_tv ?? '—' }}
                            </td>
                            @for($d = 1; $d <= $daysInMonth; $d++)
                                @php
                                    $dateStr = "{$year}-{$month}-" . sprintf('%02d', $d);
                                    $entry = isset($entries[$p->id][$dateStr]) ? $entries[$p->id][$dateStr]->first() : null;
                                    $key = "{$p->id}-{$dateStr}";
                                    
                                    $carbonDate = \Carbon\Carbon::createFromDate($year, $month, $d);
                                    $isWeekend = $carbonDate->isWeekend();
                                    $dateFormatted = $carbonDate->translatedFormat('d F Y');
                                @endphp
                                <td @click="openCell('{{ $p->id }}', '{{ addslashes($p->nama) }}', '{{ $dateStr }}', '{{ $dateFormatted }}')"
                                    class="p-2 border-r border-kpi-line dark:border-white/10 text-center cursor-pointer hover:bg-stone-100 dark:hover:bg-white/5 transition-colors select-none min-h-[45px] {{ $isWeekend ? 'bg-amber-500/5 dark:bg-amber-500/[0.02]' : '' }}">
                                    
                                    {{-- Reactive cell rendering --}}
                                    <template x-if="cells['{{ $key }}'] && cells['{{ $key }}'].status_name">
                                        <span class="inline-block px-1.5 py-0.5 rounded-[4px] text-[9px] font-bold text-gray-800 tracking-tight shadow-sm"
                                              :style="{ backgroundColor: cells['{{ $key }}'].status_warna || '#e5e7eb' }"
                                              :title="cells['{{ $key }}'].keterangan"
                                              x-text="cells['{{ $key }}'].status_name">
                                        </span>
                                    </template>
                                    <template x-if="cells['{{ $key }}'] && cells['{{ $key }}'].stasiun_tv && !cells['{{ $key }}'].status_name">
                                        <span class="text-[10px] font-medium text-stone-700 dark:text-stone-300 block truncate max-w-[45px]"
                                              :title="cells['{{ $key }}'].stasiun_tv"
                                              x-text="cells['{{ $key }}'].stasiun_tv">
                                        </span>
                                    </template>
                                    <template x-if="(!cells['{{ $key }}'] || (!cells['{{ $key }}'].stasiun_tv && !cells['{{ $key }}'].status_name))">
                                        <span class="text-[10px] text-stone-300 dark:text-stone-700 font-normal select-none">
                                            Masuk
                                        </span>
                                    </template>
                                </td>
                            @endfor
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $daysInMonth + 2 }}" class="p-6 text-center text-kpi-gray">
                                Belum ada pegawai yang dijadwalkan pada shift ini untuk periode terpilih. Klik 'Tambah Pegawai' atau 'Import Excel' untuk memulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Alpine JS Modal Edit Cell --}}
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-kpi-black/40 backdrop-blur-[2px] transition-opacity">
            <div @click.away="showModal = false" class="card w-full max-w-md shadow-2xl dark:bg-kpi-dark-surface dark:border-white/10 animate-[fadeIn_0.2s_ease-out]">
                <div class="flex items-center justify-between border-b border-kpi-line pb-3 dark:border-white/10">
                    <div>
                        <h3 class="font-serif text-base font-semibold">Ubah Jadwal Shift</h3>
                        <p class="text-xs text-kpi-gray mt-0.5" x-text="selectedPegawaiNama"></p>
                    </div>
                    <button @click="showModal = false" class="text-kpi-gray hover:text-kpi-black dark:hover:text-stone-300">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="py-4 space-y-3.5">
                    <div>
                        <label class="text-xs text-kpi-gray block mb-1">Tanggal</label>
                        <p class="text-sm font-semibold" x-text="selectedDateFormatted"></p>
                    </div>
                    <div>
                        <label class="text-xs text-kpi-gray block mb-1">Status Shift (opsional)</label>
                        <select x-model="statusShiftId" class="input w-full">
                            <option value="">-- Bekerja Normal (Normal Shift) --</option>
                            @foreach ($statusShifts as $ss)
                                <option value="{{ $ss->id }}">{{ $ss->nama }} ({{ $ss->kode }})</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-kpi-gray mt-1">Jika memilih Status Shift (misal: Cuti Bersama), data Stasiun TV di bawah akan diabaikan.</p>
                    </div>
                    <div x-show="!statusShiftId">
                        <label class="text-xs text-kpi-gray block mb-1">Stasiun TV Yang Dipantau (opsional)</label>
                        <input type="text" x-model="stasiunTv" placeholder="Contoh: TRANS TV" class="input w-full">
                    </div>
                    <div>
                        <label class="text-xs text-kpi-gray block mb-1">Keterangan / Catatan</label>
                        <textarea x-model="keterangan" rows="2" placeholder="Catatan opsional..." class="input w-full"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 border-t border-kpi-line pt-3 dark:border-white/10">
                    <button @click="showModal = false" class="btn-secondary">Batal</button>
                    <button @click="saveCell" class="btn-primary">Simpan</button>
                </div>
            </div>
        </div>

        {{-- Alpine JS Modal Tambah Pegawai --}}
        <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-kpi-black/40 backdrop-blur-[2px] transition-opacity">
            <div @click.away="showAddModal = false" class="card w-full max-w-md shadow-2xl dark:bg-kpi-dark-surface dark:border-white/10 animate-[fadeIn_0.2s_ease-out]">
                <form method="POST" action="{{ route('absensi.shift.tambah-pegawai') }}">
                    @csrf
                    <input type="hidden" name="shift" value="{{ $shift }}">
                    <input type="hidden" name="bulan" value="{{ $bulan }}">
                    <div class="flex items-center justify-between border-b border-kpi-line pb-3 dark:border-white/10">
                        <div>
                            <h3 class="font-serif text-base font-semibold">Tambah Pegawai ke Shift {{ $shift }}</h3>
                            <p class="text-xs text-kpi-gray mt-0.5">Daftarkan pegawai ke grid kalender periode ini</p>
                        </div>
                        <button type="button" @click="showAddModal = false" class="text-kpi-gray hover:text-kpi-black dark:hover:text-stone-300">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="py-4 space-y-4">
                        <div>
                            <label class="text-xs text-kpi-gray block mb-1">Pilih Pegawai</label>
                            <select name="pegawai_id" required class="input w-full">
                                <option value="">-- Pilih Pegawai --</option>
                                @foreach ($allPegawais as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama }} (NIP: {{ $p->nip }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-kpi-gray block mb-1">Mulai Tanggal Penugasan</label>
                            <input type="date" name="tanggal" value="{{ $year }}-{{ $month }}-01" required class="input w-full">
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 border-t border-kpi-line pt-3 dark:border-white/10">
                        <button type="button" @click="showAddModal = false" class="btn-secondary">Batal</button>
                        <button type="submit" class="btn-primary">Tambah ke Grid</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Konfirmasi Hapus Data Periode --}}
        <div x-show="showHapusModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-kpi-black/40 backdrop-blur-[2px] transition-opacity"
             @keydown.escape.window="showHapusModal = false">
            <div @click.away="showHapusModal = false"
                 class="card w-full max-w-lg shadow-2xl dark:bg-kpi-dark-surface dark:border-white/10 animate-[fadeIn_0.2s_ease-out]">

                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-kpi-line pb-3 dark:border-white/10">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-rose-100 dark:bg-rose-500/15">
                            <svg class="h-4 w-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </span>
                        <div>
                            <h3 class="font-serif text-base font-semibold text-rose-700 dark:text-rose-400">Konfirmasi Hapus Data Jadwal</h3>
                            <p class="text-xs text-kpi-gray mt-0.5">Tindakan ini tidak dapat dibatalkan</p>
                        </div>
                    </div>
                    <button type="button" @click="showHapusModal = false"
                            class="text-kpi-gray hover:text-kpi-black dark:hover:text-stone-300">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="py-5 space-y-4">
                    <p class="text-sm text-stone-700 dark:text-stone-300 leading-relaxed">
                        Anda akan menghapus <strong>seluruh data jadwal</strong> berikut:
                    </p>

                    <div class="rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 p-4 space-y-2.5">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-kpi-gray font-medium">Shift</span>
                            <span class="font-bold text-rose-700 dark:text-rose-400">
                                Shift {{ $shift }} ({{ $shift == '1' ? '06.00–14.00' : ($shift == '2' ? '14.00–22.00' : '22.00–06.00') }})
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-kpi-gray font-medium">Periode</span>
                            <span class="font-bold text-rose-700 dark:text-rose-400">
                                @php
                                    $monthsIndo = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
                                @endphp
                                {{ ($monthsIndo[$month] ?? $month) . ' ' . $year }}
                            </span>
                        </div>
                        <div class="border-t border-rose-200 dark:border-rose-500/20 pt-2.5 grid grid-cols-2 gap-2">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-rose-700 dark:text-rose-400" x-text="hapusJumlahEntri"></p>
                                <p class="text-[11px] text-kpi-gray mt-0.5">Total entry yang dihapus</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-rose-700 dark:text-rose-400" x-text="hapusJumlahPegawai"></p>
                                <p class="text-[11px] text-kpi-gray mt-0.5">Pegawai terdampak</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 p-3">
                        <p class="text-xs text-amber-800 dark:text-amber-300 leading-relaxed">
                            <strong>ℹ️ Data pegawai tetap aman.</strong> Hanya entri jadwal yang dihapus — profil pegawai
                            di sistem tidak ikut terhapus.
                        </p>
                    </div>

                    <template x-if="hapusJumlahEntri === 0">
                        <div class="rounded-xl bg-stone-50 dark:bg-white/5 border border-kpi-line dark:border-white/10 p-3 text-center">
                            <p class="text-sm text-kpi-gray">Tidak ada data jadwal untuk periode ini. Tidak ada yang perlu dihapus.</p>
                        </div>
                    </template>
                </div>

                {{-- Footer --}}
                <div class="flex justify-end gap-2 border-t border-kpi-line pt-3 dark:border-white/10">
                    <button type="button" @click="showHapusModal = false" class="btn-secondary">
                        Batal
                    </button>

                    {{-- Form DELETE tersembunyi — hanya submit jika ada data --}}
                    <template x-if="hapusJumlahEntri > 0">
                        <form method="POST"
                              action="{{ route('absensi.shift.hapus-periode', $shift) }}"
                              id="form-hapus-periode">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="bulan" value="{{ $bulan }}">
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-rose-600 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 dark:focus:ring-offset-kpi-dark-surface">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Ya, Hapus <span x-text="hapusJumlahEntri"></span> Entry
                            </button>
                        </form>
                    </template>
                </div>
            </div>
        </div>
    </div>


    <script>
        function shiftCalendar() {
            return {
                showModal: false,
                showAddModal: false,
                selectedPegawaiId: null,
                selectedPegawaiNama: '',
                selectedDate: '',
                selectedDateFormatted: '',
                stasiunTv: '',
                statusShiftId: '',
                keterangan: '',
                cells: @json($initialCellsData ?? (object)[]),

                init() {
                    // Pre-populated cells
                },

                openCell(pegawaiId, pegawaiNama, dateStr, dateFormatted) {
                    this.selectedPegawaiId = pegawaiId;
                    this.selectedPegawaiNama = pegawaiNama;
                    this.selectedDate = dateStr;
                    this.selectedDateFormatted = dateFormatted;

                    let key = pegawaiId + '-' + dateStr;
                    let data = this.cells[key] || { stasiun_tv: '', status_shift_id: '', status_name: '', status_warna: '', keterangan: '' };

                    this.stasiunTv = data.stasiun_tv || '';
                    this.statusShiftId = data.status_shift_id || '';
                    this.keterangan = data.keterangan || '';

                    this.showModal = true;
                },

                saveCell() {
                    let payload = {
                        pegawai_id: this.selectedPegawaiId,
                        tanggal: this.selectedDate,
                        shift: '{{ $shift }}',
                        stasiun_tv: this.stasiunTv,
                        status_shift_id: this.statusShiftId,
                        keterangan: this.keterangan
                    };

                    fetch('{{ route('absensi.shift.update-cell') }}', {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success') {
                            let key = this.selectedPegawaiId + '-' + this.selectedDate;
                            let statusName = '';
                            let statusWarna = '';
                            if (res.data.status_shift) {
                                statusName = res.data.status_shift.nama;
                                statusWarna = res.data.status_shift.warna;
                            }
                            this.cells[key] = {
                                stasiun_tv: res.data.stasiun_tv,
                                status_shift_id: res.data.status_shift_id,
                                status_name: statusName,
                                status_warna: statusWarna,
                                keterangan: res.data.keterangan
                            };
                            this.showModal = false;
                        } else {
                            alert('Gagal memperbarui jadwal.');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Terjadi kesalahan koneksi.');
                    });
                }
            };
        }
    </script>
</x-app-layout>
