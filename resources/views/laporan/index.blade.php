<x-app-layout title="Laporan">
    <div x-data="{
        activeTab: 'kepegawaian',

        // Modal state
        showModal: false,
        modalTitle: 'Pilih Pegawai',
        modalSubtitle: 'Pilih item untuk mengunduh laporan',
        modalType: 'slip_rekap',
        modalSelectorType: 'pegawai', // 'pegawai' or 'unit'

        searchModal: '',
        selectedIds: [],
        pegawais: {{ Js::from($pegawais ?? []) }},
        units: {{ Js::from($units ?? []) }},

        // Card Filters
        bulanAbsensi: '{{ now()->format('Y-m') }}',
        bulanKetidakhadiran: '{{ now()->format('Y-m') }}',
        bulanDetailAbsensi: '{{ now()->format('Y-m') }}',
        bulanPengurangan: '{{ now()->format('Y-m') }}',
        bulanShift: '{{ now()->format('Y-m') }}',

        tahunCuti: '{{ now()->year }}',
        tahunCutiUnit: '{{ now()->year }}',
        tahunCutiPegawai: '{{ now()->year }}',
        tahunDiklat: '{{ now()->year }}',
        tahunTargetJp: '{{ now()->year }}',

        openModal(type, title, subtitle = '', selectorType = 'pegawai') {
            this.modalType = type;
            this.modalTitle = title;
            this.modalSubtitle = subtitle || 'Pilih item untuk mengunduh laporan';
            this.modalSelectorType = selectorType;
            this.searchModal = '';
            this.selectedIds = [];
            this.showModal = true;
        },

        get filteredItemsModal() {
            if (this.modalSelectorType === 'unit') {
                if (!this.searchModal) return this.units;
                const q = this.searchModal.toLowerCase();
                return this.units.filter(u => 
                    (u.nama_unit && u.nama_unit.toLowerCase().includes(q)) || 
                    (u.kode_unit && u.kode_unit.toLowerCase().includes(q))
                );
            } else {
                if (!this.searchModal) return this.pegawais;
                const q = this.searchModal.toLowerCase();
                return this.pegawais.filter(p => 
                    (p.nama && p.nama.toLowerCase().includes(q)) || 
                    (p.nip && p.nip.toLowerCase().includes(q))
                );
            }
        },

        get isAllSelected() {
            if (this.filteredItemsModal.length === 0) return false;
            return this.filteredItemsModal.every(item => this.selectedIds.includes(item.id));
        },

        toggleSelectAll(e) {
            if (e.target.checked) {
                const currentFilteredIds = this.filteredItemsModal.map(item => item.id);
                this.selectedIds = Array.from(new Set([...this.selectedIds, ...currentFilteredIds]));
            } else {
                const currentFilteredIds = new Set(this.filteredItemsModal.map(item => item.id));
                this.selectedIds = this.selectedIds.filter(id => !currentFilteredIds.has(id));
            }
        },

        exportReport(format) {
            if (this.selectedIds.length === 0) return;
            const ids = this.selectedIds.join(',');
            let url = '';

            if (this.modalType === 'slip_rekap') {
                url = format === 'excel' 
                    ? `{{ route('laporan.pegawai.excel') }}?pegawai_ids=${ids}&type=slip`
                    : `{{ route('laporan.pegawai.pdf') }}?pegawai_ids=${ids}&type=slip`;
            } else if (this.modalType === 'detail_absensi') {
                url = format === 'excel'
                    ? `{{ route('laporan.absensi.excel') }}?bulan=${this.bulanDetailAbsensi}&pegawai_ids=${ids}`
                    : `{{ route('laporan.absensi.pdf') }}?bulan=${this.bulanDetailAbsensi}&pegawai_ids=${ids}`;
            } else if (this.modalType === 'riwayat_cuti') {
                url = format === 'excel'
                    ? `{{ route('laporan.cuti.excel') }}?tahun=${this.tahunCutiPegawai}&pegawai_ids=${ids}`
                    : `{{ route('laporan.cuti.pdf') }}?tahun=${this.tahunCutiPegawai}&pegawai_ids=${ids}`;
            } else if (this.modalType === 'rekap_diklat') {
                url = format === 'excel'
                    ? `{{ route('laporan.pegawai.excel') }}?type=diklat&tahun=${this.tahunDiklat}&pegawai_ids=${ids}`
                    : `{{ route('laporan.pegawai.pdf') }}?type=diklat&tahun=${this.tahunDiklat}&pegawai_ids=${ids}`;
            } else if (this.modalType === 'cuti_unit') {
                url = format === 'excel'
                    ? `{{ route('laporan.cuti.excel') }}?tahun=${this.tahunCutiUnit}&by=unit&unit_ids=${ids}`
                    : `{{ route('laporan.cuti.pdf') }}?tahun=${this.tahunCutiUnit}&by=unit&unit_ids=${ids}`;
            }

            if (url) {
                window.location.href = url;
                this.showModal = false;
            }
        }
    }">
        <p class="eyebrow">Ekspor Data</p>
        <h2 class="mb-6 mt-1 font-serif text-xl font-semibold text-kpi-black dark:text-stone-100">Laporan & Rekapitulasi</h2>

        {{-- Tab Navigation Horizontal --}}
        <div class="mb-6 flex gap-1 overflow-x-auto border-b border-kpi-line dark:border-white/10">
            <button type="button" @click="activeTab = 'kepegawaian'" 
               :class="activeTab === 'kepegawaian' ? 'border-kpi-red text-kpi-red font-semibold' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200'"
               class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors">
                Kepegawaian
            </button>
            <button type="button" @click="activeTab = 'kehadiran'" 
               :class="activeTab === 'kehadiran' ? 'border-kpi-red text-kpi-red font-semibold' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200'"
               class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors">
                Kehadiran & Shift
            </button>
            <button type="button" @click="activeTab = 'cuti'" 
               :class="activeTab === 'cuti' ? 'border-kpi-red text-kpi-red font-semibold' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200'"
               class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors">
                Cuti & Izin
            </button>
            <button type="button" @click="activeTab = 'diklat'" 
               :class="activeTab === 'diklat' ? 'border-kpi-red text-kpi-red font-semibold' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200'"
               class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors">
                Diklat
            </button>
        </div>

        {{-- TAB 1: KEPEGAWAIAN --}}
        <div x-show="activeTab === 'kepegawaian'" class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
            {{-- Card 1: Data Pegawai --}}
            <div class="card card-hover flex flex-col justify-between">
                <div>
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-kpi-red-soft text-kpi-red-dark dark:bg-kpi-red/15 dark:text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 100-8 4 4 0 000 8zm6 3.13a4 4 0 00-3-3.87"/></svg>
                    </div>
                    <h3 class="mb-1 font-semibold text-kpi-black dark:text-stone-100">Data Pegawai</h3>
                    <p class="mb-5 text-sm text-kpi-gray">Ekspor seluruh data profil & status kepegawaian aktif.</p>
                </div>
                <div class="flex gap-3 pt-2">
                    <a href="{{ route('laporan.pegawai.excel') }}" class="btn-secondary flex-1">Excel</a>
                    <a href="{{ route('laporan.pegawai.pdf') }}" class="btn-secondary flex-1">PDF</a>
                </div>
            </div>

            {{-- Card 2: Demografi Pegawai --}}
            <div class="card card-hover flex flex-col justify-between">
                <div>
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-sky-100 text-sky-700 dark:bg-sky-500/15 dark:text-sky-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                    </div>
                    <h3 class="mb-1 font-semibold text-kpi-black dark:text-stone-100">Demografi Pegawai</h3>
                    <p class="mb-5 text-sm text-kpi-gray">Ringkasan statistik usia, tingkat pendidikan, rasio L/P, dan status kepegawaian.</p>
                </div>
                <div class="flex gap-3 pt-2">
                    <a href="{{ route('laporan.pegawai.excel') }}?type=demografi" class="btn-secondary flex-1">Excel</a>
                    <a href="{{ route('laporan.pegawai.pdf') }}?type=demografi" class="btn-secondary flex-1">PDF</a>
                </div>
            </div>

            {{-- Card 3: Slip Rekap Individu --}}
            <div class="card card-hover flex flex-col justify-between">
                <div>
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3 3 0 00-3 3h6a3 3 0 00-3-3z"/></svg>
                    </div>
                    <h3 class="mb-1 font-semibold text-kpi-black dark:text-stone-100">Slip Rekap Individu</h3>
                    <p class="mb-5 text-sm text-kpi-gray">Berkas komprehensif profil, rekap presensi, saldo cuti, dan diklat per pegawai terpilih.</p>
                </div>
                <div class="pt-2">
                    <button type="button" @click="openModal('slip_rekap', 'Slip Rekap Individu', 'Berkas komprehensif profil, rekap presensi, saldo cuti, dan diklat per pegawai terpilih', 'pegawai')" class="btn-primary w-full shadow-sm flex items-center justify-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Pilih Pegawai
                    </button>
                </div>
            </div>
        </div>

        {{-- TAB 2: KEHADIRAN & SHIFT --}}
        <div x-show="activeTab === 'kehadiran'" x-cloak class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
            {{-- Card 1: Rekap Absensi Bulanan --}}
            <div class="card card-hover flex flex-col justify-between">
                <div>
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="mb-1 font-semibold text-kpi-black dark:text-stone-100">Rekap Absensi Bulanan</h3>
                    <p class="mb-4 text-sm text-kpi-gray">Rekapitulasi total kehadiran, keterlambatan, izin, dan alpa seluruh pegawai per bulan.</p>
                </div>
                <div class="flex flex-col gap-3">
                    <div>
                        <label class="label !mb-1 text-xs">Pilih Bulan</label>
                        <input type="month" x-model="bulanAbsensi" class="input">
                    </div>
                    <div class="flex gap-3">
                        <a :href="`{{ route('laporan.absensi.excel') }}?bulan=${bulanAbsensi}`" class="btn-secondary flex-1">Excel</a>
                        <a :href="`{{ route('laporan.absensi.pdf') }}?bulan=${bulanAbsensi}`" class="btn-secondary flex-1">PDF</a>
                    </div>
                </div>
            </div>

            {{-- Card 2: Rekap Ketidakhadiran --}}
            <div class="card card-hover flex flex-col justify-between">
                <div>
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-purple-100 text-purple-700 dark:bg-purple-500/15 dark:text-purple-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="mb-1 font-semibold text-kpi-black dark:text-stone-100">Rekap Ketidakhadiran</h3>
                    <p class="mb-4 text-sm text-kpi-gray">Matrix rekapitulasi pengajuan izin, sakit, dan alasan non-cuti per kategori.</p>
                </div>
                <div class="flex flex-col gap-3">
                    <div>
                        <label class="label !mb-1 text-xs">Pilih Bulan</label>
                        <input type="month" x-model="bulanKetidakhadiran" class="input">
                    </div>
                    <div class="flex gap-3">
                        <a :href="`{{ route('laporan.ketidakhadiran.excel') }}?bulan=${bulanKetidakhadiran}`" class="btn-secondary flex-1">Excel</a>
                        <a :href="`{{ route('laporan.ketidakhadiran.pdf') }}?bulan=${bulanKetidakhadiran}`" class="btn-secondary flex-1">PDF</a>
                    </div>
                </div>
            </div>

            {{-- Card 3: Detail Absensi Individu --}}
            <div class="card card-hover flex flex-col justify-between">
                <div>
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-teal-100 text-teal-700 dark:bg-teal-500/15 dark:text-teal-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="mb-1 font-semibold text-kpi-black dark:text-stone-100">Detail Absensi Individu</h3>
                    <p class="mb-4 text-sm text-kpi-gray">Log timesheet presensi harian lengkap khusus untuk pegawai terpilih.</p>
                </div>
                <div class="flex flex-col gap-3">
                    <div>
                        <label class="label !mb-1 text-xs">Pilih Bulan</label>
                        <input type="month" x-model="bulanDetailAbsensi" class="input">
                    </div>
                    <div>
                        <button type="button" @click="openModal('detail_absensi', 'Detail Absensi Individu', 'Pilih pegawai untuk mengunduh log absensi bulan ' + bulanDetailAbsensi, 'pegawai')" class="btn-primary w-full shadow-sm flex items-center justify-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            Pilih Pegawai
                        </button>
                    </div>
                </div>
            </div>

            {{-- Card 4: Pengurangan Jam Kerja --}}
            <div class="card card-hover flex flex-col justify-between">
                <div>
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="mb-1 font-semibold text-kpi-black dark:text-stone-100">Pengurangan Jam Kerja</h3>
                    <p class="mb-4 text-sm text-kpi-gray">Daftar pegawai yang memiliki akumulasi potongan menit jam kerja.</p>
                </div>
                <div class="flex flex-col gap-3">
                    <div>
                        <label class="label !mb-1 text-xs">Pilih Bulan</label>
                        <input type="month" x-model="bulanPengurangan" class="input">
                    </div>
                    <div class="flex gap-3">
                        <a :href="`{{ route('laporan.absensi.excel') }}?bulan=${bulanPengurangan}&type=pengurangan`" class="btn-secondary flex-1">Excel</a>
                        <a :href="`{{ route('laporan.absensi.pdf') }}?bulan=${bulanPengurangan}&type=pengurangan`" class="btn-secondary flex-1">PDF</a>
                    </div>
                </div>
            </div>

            {{-- Card 5: Rekap Jadwal Shift Bulanan --}}
            <div class="card card-hover flex flex-col justify-between">
                <div>
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                    <h3 class="mb-1 font-semibold text-kpi-black dark:text-stone-100">Rekap Jadwal Shift Bulanan</h3>
                    <p class="mb-4 text-sm text-kpi-gray">Matriks alokasi jadwal roster shift harian (Shift 1/2/3) seluruh pegawai.</p>
                </div>
                <div class="flex flex-col gap-3">
                    <div>
                        <label class="label !mb-1 text-xs">Pilih Bulan</label>
                        <input type="month" x-model="bulanShift" class="input">
                    </div>
                    <div class="flex gap-3">
                        <a :href="`{{ route('laporan.absensi.excel') }}?bulan=${bulanShift}&type=shift`" class="btn-secondary flex-1">Excel</a>
                        <a :href="`{{ route('laporan.absensi.pdf') }}?bulan=${bulanShift}&type=shift`" class="btn-secondary flex-1">PDF</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 3: CUTI & IZIN --}}
        <div x-show="activeTab === 'cuti'" x-cloak class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
            {{-- Card 1: Rekap Cuti Tahunan --}}
            <div class="card card-hover flex flex-col justify-between">
                <div>
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-kpi-gold-soft text-kpi-gold dark:bg-kpi-gold/15">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="mb-1 font-semibold text-kpi-black dark:text-stone-100">Rekap Cuti Tahunan</h3>
                    <p class="mb-4 text-sm text-kpi-gray">Daftar seluruh riwayat pengajuan cuti pegawai dalam 1 tahun anggaran.</p>
                </div>
                <div class="flex flex-col gap-3">
                    <div>
                        <label class="label !mb-1 text-xs">Pilih Tahun</label>
                        <input type="number" x-model="tahunCuti" min="2020" max="2100" class="input">
                    </div>
                    <div class="flex gap-3">
                        <a :href="`{{ route('laporan.cuti.excel') }}?tahun=${tahunCuti}`" class="btn-secondary flex-1">Excel</a>
                        <a :href="`{{ route('laporan.cuti.pdf') }}?tahun=${tahunCuti}`" class="btn-secondary flex-1">PDF</a>
                    </div>
                </div>
            </div>

            {{-- Card 2: Laporan Cuti Per Unit Kerja --}}
            <div class="card card-hover flex flex-col justify-between">
                <div>
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V9a2 2 0 012-2h2a2 2 0 012 2v12m-6 0h6"/></svg>
                    </div>
                    <h3 class="mb-1 font-semibold text-kpi-black dark:text-stone-100">Laporan Cuti Per Unit Kerja</h3>
                    <p class="mb-4 text-sm text-kpi-gray">Daftar lengkap pengajuan cuti pegawai yang dikelompokkan per unit kerja.</p>
                </div>
                <div class="flex flex-col gap-3">
                    <div>
                        <label class="label !mb-1 text-xs">Pilih Tahun</label>
                        <input type="number" x-model="tahunCutiUnit" min="2020" max="2100" class="input">
                    </div>
                    <div>
                        <button type="button" @click="openModal('cuti_unit', 'Laporan Cuti Per Unit Kerja', 'Pilih unit kerja untuk mengunduh breakdown cuti tahun ' + tahunCutiUnit, 'unit')" class="btn-primary w-full shadow-sm flex items-center justify-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V9a2 2 0 012-2h2a2 2 0 012 2v12m-6 0h6"/></svg>
                            Pilih Unit Kerja
                        </button>
                    </div>
                </div>
            </div>

            {{-- Card 3: Riwayat Cuti Individu --}}
            <div class="card card-hover flex flex-col justify-between">
                <div>
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h3 class="mb-1 font-semibold text-kpi-black dark:text-stone-100">Riwayat Cuti Individu</h3>
                    <p class="mb-4 text-sm text-kpi-gray">Detail riwayat pengajuan cuti khusus untuk pegawai terpilih dalam 1 tahun.</p>
                </div>
                <div class="flex flex-col gap-3">
                    <div>
                        <label class="label !mb-1 text-xs">Pilih Tahun</label>
                        <input type="number" x-model="tahunCutiPegawai" min="2020" max="2100" class="input">
                    </div>
                    <div>
                        <button type="button" @click="openModal('riwayat_cuti', 'Riwayat Cuti Individu', 'Pilih pegawai untuk mengunduh riwayat cuti tahun ' + tahunCutiPegawai, 'pegawai')" class="btn-primary w-full shadow-sm flex items-center justify-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            Pilih Pegawai
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 4: DIKLAT --}}
        <div x-show="activeTab === 'diklat'" x-cloak class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
            {{-- Card 1: Rekap Diklat Individu --}}
            <div class="card card-hover flex flex-col justify-between">
                <div>
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 text-violet-700 dark:bg-violet-500/15 dark:text-violet-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                    </div>
                    <h3 class="mb-1 font-semibold text-kpi-black dark:text-stone-100">Rekap Diklat Individu</h3>
                    <p class="mb-4 text-sm text-kpi-gray">Daftar riwayat pelatihan, penyelenggara, dan perolehan JP khusus pegawai terpilih.</p>
                </div>
                <div class="flex flex-col gap-3">
                    <div>
                        <label class="label !mb-1 text-xs">Pilih Tahun</label>
                        <input type="number" x-model="tahunDiklat" min="2020" max="2100" class="input">
                    </div>
                    <div>
                        <button type="button" @click="openModal('rekap_diklat', 'Rekap Diklat Individu', 'Pilih pegawai untuk mengunduh riwayat pelatihan & total JP tahun ' + tahunDiklat, 'pegawai')" class="btn-primary w-full shadow-sm flex items-center justify-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            Pilih Pegawai
                        </button>
                    </div>
                </div>
            </div>

            {{-- Card 2: Laporan Target JP --}}
            <div class="card card-hover flex flex-col justify-between">
                <div>
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-100 text-cyan-700 dark:bg-cyan-500/15 dark:text-cyan-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </div>
                    <h3 class="mb-1 font-semibold text-kpi-black dark:text-stone-100">Laporan Target JP</h3>
                    <p class="mb-4 text-sm text-kpi-gray">Evaluasi capaian realisasi Jam Pelajaran (JP) pegawai terhadap standar target 20 JP/tahun.</p>
                </div>
                <div class="flex flex-col gap-3">
                    <div>
                        <label class="label !mb-1 text-xs">Pilih Tahun</label>
                        <input type="number" x-model="tahunTargetJp" min="2020" max="2100" class="input">
                    </div>
                    <div class="flex gap-3">
                        <a :href="`{{ route('laporan.pegawai.excel') }}?type=target_jp&tahun=${tahunTargetJp}`" class="btn-secondary flex-1">Excel</a>
                        <a :href="`{{ route('laporan.pegawai.pdf') }}?type=target_jp&tahun=${tahunTargetJp}`" class="btn-secondary flex-1">PDF</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- REUSABLE MODAL BULK SELECT (PEGAWAI / UNIT KERJA) --}}
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div x-show="showModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" @click.outside="showModal = false" class="w-full max-w-lg rounded-2xl border border-kpi-line bg-white p-6 shadow-2xl dark:border-white/10 dark:bg-kpi-dark-surface flex flex-col max-h-[85vh]">
                
                {{-- Modal Header --}}
                <div class="flex items-center justify-between border-b border-kpi-line dark:border-white/10 pb-4">
                    <div>
                        <h3 class="font-serif text-lg font-bold text-kpi-black dark:text-stone-100" x-text="modalTitle"></h3>
                        <p class="text-xs text-kpi-gray dark:text-stone-400 mt-0.5" x-text="modalSubtitle"></p>
                    </div>
                    <button type="button" @click="showModal = false" class="text-kpi-gray hover:text-kpi-black dark:text-stone-400 dark:hover:text-stone-200 transition-colors p-1 rounded-lg hover:bg-stone-100 dark:hover:bg-white/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Modal Content --}}
                <div class="py-4 space-y-3 flex-1 overflow-hidden flex flex-col">
                    {{-- Search Input --}}
                    <div class="relative">
                        <input type="text" x-model="searchModal" :placeholder="modalSelectorType === 'unit' ? 'Cari nama unit kerja...' : 'Cari nama atau NIP pegawai...'" class="input pl-9">
                        <svg class="w-4 h-4 text-kpi-gray absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    {{-- Select All Checkbox Bar --}}
                    <div class="flex items-center justify-between bg-stone-50 dark:bg-white/5 p-3 rounded-xl border border-kpi-line dark:border-white/10 text-xs">
                        <label class="flex items-center gap-2.5 font-semibold text-kpi-black dark:text-stone-200 cursor-pointer select-none">
                            <input type="checkbox" :checked="isAllSelected" @change="toggleSelectAll" class="rounded border-stone-300 text-kpi-red focus:ring-kpi-red/20 h-4 w-4">
                            <span>Pilih Semua</span>
                        </label>
                        <span class="text-kpi-gray dark:text-stone-400 font-medium">
                            Terpilih: <strong class="text-kpi-red dark:text-rose-400" x-text="selectedIds.length">0</strong> <span x-text="modalSelectorType === 'unit' ? 'unit kerja' : 'pegawai'"></span>
                        </span>
                    </div>

                    {{-- Scrollable List Items --}}
                    <div class="flex-1 overflow-y-auto pr-1 space-y-1 divide-y divide-stone-100 dark:divide-white/5 max-h-64 border border-stone-200 dark:border-white/10 rounded-xl p-2 bg-white dark:bg-kpi-dark-surface/50">
                        <template x-for="item in filteredItemsModal" :key="item.id">
                            <label class="flex items-center justify-between p-2.5 rounded-lg hover:bg-stone-50 dark:hover:bg-white/5 cursor-pointer transition-colors">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" :value="item.id" x-model="selectedIds" class="rounded border-stone-300 text-kpi-red focus:ring-kpi-red/20 h-4 w-4">
                                    <div>
                                        <p class="text-sm font-medium text-kpi-black dark:text-stone-100" x-text="modalSelectorType === 'unit' ? item.nama_unit : item.nama"></p>
                                        <p class="text-xs text-kpi-gray dark:text-stone-400">
                                            <template x-if="modalSelectorType === 'unit'">
                                                <span x-text="item.kode_unit ? 'Kode: ' + item.kode_unit : 'Unit Kerja'"></span>
                                            </template>
                                            <template x-if="modalSelectorType !== 'unit'">
                                                <span>
                                                    <span x-text="item.nip ? 'NIP: ' + item.nip : ''"></span>
                                                    <span x-show="item.unit" class="ml-1 opacity-75" x-text="'• ' + (item.unit ? item.unit.nama_unit : '')"></span>
                                                </span>
                                            </template>
                                        </p>
                                    </div>
                                </div>
                            </label>
                        </template>

                        <div x-show="filteredItemsModal.length === 0" class="py-8 text-center text-xs text-kpi-gray">
                            Data tidak ditemukan.
                        </div>
                    </div>
                </div>

                {{-- Modal Footer with Excel & PDF buttons --}}
                <div class="pt-4 border-t border-kpi-line dark:border-white/10 flex items-center justify-end gap-2.5">
                    <button type="button" @click="showModal = false" class="btn-secondary">
                        Batal
                    </button>
                    <button type="button" :disabled="selectedIds.length === 0" @click="exportReport('excel')" class="btn-secondary flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export Excel
                    </button>
                    <button type="button" :disabled="selectedIds.length === 0" @click="exportReport('pdf')" class="btn-primary flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export PDF
                    </button>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
