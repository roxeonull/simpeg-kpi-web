@if(auth()->user()->role === 'admin')
@php
    $bentukOptions = [['value' => '', 'label' => '— Pilih Bentuk Pelatihan —']];
    foreach($bentukPelatihans as $b) {
        $bentukOptions[] = ['value' => (string)$b->id, 'label' => $b->nama_bentuk];
    }

    $jenisOptions = [['value' => '', 'label' => '— Pilih Jenis —']];
    foreach($jenisKursuses as $jk) {
        $jenisOptions[] = ['value' => (string)$jk->id, 'label' => $jk->nama_jenis];
    }

    $instansiOptions = [['value' => '', 'label' => '— Pilih Instansi —']];
    foreach($instansis as $in) {
        $instansiOptions[] = ['value' => (string)$in->id, 'label' => $in->nama_instansi];
    }

    $hasTrainingErrors = $errors->hasAny([
        'nama_pelatihan', 'penyelenggara', 'tanggal', 'tanggal_akhir', 'durasi_jp',
        'bentuk_pelatihan_id', 'tipe_kursus_id', 'jenis_kursus_id', 'instansi_id',
        'no_sertifikat', 'tanggal_sertifikat', 'bidang_sdm_spbe', 'sertifikat'
    ]);
    $initialShowForm = $hasTrainingErrors ? 'true' : 'false';
@endphp

<div x-data="{
         showForm: {{ $initialShowForm }},
         bentukId: '',
         tanggalMulai: '',
         tahunDiklat: '—',
         tipeKursusList: {{ json_encode($tipeKursuses) }},
         getFilteredTipe() {
             if (!this.bentukId) return [];
             return this.tipeKursusList.filter(t => t.bentuk_pelatihan_id == this.bentukId);
         },
         updateTahun() {
             if (this.tanggalMulai) {
                 this.tahunDiklat = new Date(this.tanggalMulai).getFullYear();
             } else {
                 this.tahunDiklat = '—';
             }
         }
     }" class="mb-6">
     
    <!-- Toggle Button -->
    <div class="mb-4">
        <button type="button" @click="showForm = !showForm" class="btn-secondary">
            <svg class="h-4 w-4 text-kpi-red transition-transform duration-200" :class="{ 'rotate-45': showForm }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span x-text="showForm ? 'Batal Tambah Pelatihan' : 'Tambah Pelatihan Baru'"></span>
        </button>
    </div>

    <!-- Toggled Form Card -->
    <div x-show="showForm" x-transition 
         class="card border-dashed bg-kpi-cream-soft/10 dark:bg-white/[0.01]" x-cloak>
        <p class="eyebrow">Input Riwayat</p>
        <h3 class="mt-1 mb-5 font-serif text-base font-semibold text-kpi-black dark:text-stone-100">Tambah Pengembangan Kompetensi (SIMPATIK)</h3>
        
        <form method="POST" action="{{ route('pelatihan.store', $pegawai) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-6">
                <!-- Row 1 -->
                <div class="sm:col-span-3">
                    <label class="label">Nama Kursus / Pelatihan *</label>
                    <input type="text" name="nama_pelatihan" required placeholder="Contoh: Pelatihan Kearsipan Nasional" class="input">
                </div>
                <div class="sm:col-span-3">
                    <label class="label">Bidang SDM SPBE (Opsional)</label>
                    <input type="text" name="bidang_sdm_spbe" placeholder="Contoh: Tata Kelola / Infrastruktur" class="input">
                </div>

                <!-- Row 2 -->
                <div class="sm:col-span-2" @change="bentukId = $event.target.value">
                    <label class="label">Bentuk Pelatihan *</label>
                    <x-select name="bentuk_pelatihan_id" value="" :options="$bentukOptions" class="w-full" />
                </div>
                <div class="sm:col-span-2" x-data="{
                         open: false,
                         selectedId: '',
                         selectedLabel: '— Pilih Tipe Kursus —',
                         init() {
                             this.$watch('bentukId', () => {
                                 this.selectedId = '';
                                 this.selectedLabel = '— Pilih Tipe Kursus —';
                                 if (this.$refs.hiddenInput) this.$refs.hiddenInput.value = '';
                             });
                         },
                         selectTipe(tipe) {
                             this.selectedId = tipe ? tipe.id : '';
                             this.selectedLabel = tipe ? tipe.nama_tipe : '— Pilih Tipe Kursus —';
                             this.open = false;
                             this.$refs.hiddenInput.value = this.selectedId;
                             this.$refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                         }
                     }" @click.outside="open = false" class="relative">
                    <label class="label">Tipe Kursus *</label>
                    <input type="hidden" name="tipe_kursus_id" x-ref="hiddenInput" :value="selectedId" required>
                    
                    <button type="button" @click="if (bentukId) open = !open"
                            :disabled="!bentukId"
                            class="flex w-full items-center justify-between gap-2.5 rounded-xl border border-stone-200 bg-white/90 px-3.5 py-2.5 text-sm font-medium text-kpi-black shadow-sm transition-all duration-200 hover:border-stone-300 hover:bg-stone-50 focus:border-kpi-red focus:outline-none focus:ring-2 focus:ring-kpi-red/15 dark:border-white/10 dark:bg-kpi-dark-surface dark:text-stone-100 dark:hover:border-white/20 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-text="!bentukId ? '— Pilih Bentuk Dulu —' : selectedLabel" class="truncate"></span>
                        <svg class="h-4 w-4 shrink-0 text-kpi-gray transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open && bentukId"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute left-0 z-[100] mt-1.5 max-h-60 w-full min-w-full overflow-y-auto rounded-2xl border border-stone-200 bg-white py-1.5 shadow-2xl dark:border-white/15 dark:bg-[#25211B]"
                         x-cloak>
                        <div class="px-1 space-y-0.5">
                            <template x-for="tipe in getFilteredTipe()" :key="tipe.id">
                                <button type="button" @click="selectTipe(tipe)"
                                        class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm text-kpi-black transition-colors hover:bg-kpi-cream/60 dark:text-stone-200 dark:hover:bg-white/[0.03]"
                                        :class="{ 'bg-kpi-cream/40 font-semibold text-kpi-red dark:text-kpi-gold': selectedId == tipe.id }">
                                    <span x-text="tipe.nama_tipe" class="truncate"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label class="label">Jenis Kursus *</label>
                    <x-select name="jenis_kursus_id" value="" :options="$jenisOptions" class="w-full" />
                </div>

                <!-- Row 3 -->
                <div class="sm:col-span-2">
                    <label class="label">Institusi Penyelenggara *</label>
                    <input type="text" name="penyelenggara" required placeholder="Lembaga Penyelenggara" class="input">
                </div>
                <div class="sm:col-span-2">
                    <label class="label">Instansi Pengirim *</label>
                    <x-select name="instansi_id" value="" :options="$instansiOptions" class="w-full" />
                </div>
                <div class="sm:col-span-2">
                    <label class="label">Lama Kursus (Jam Pelajaran / JP) *</label>
                    <input type="number" name="durasi_jp" required min="1" placeholder="Durasi JP" class="input mono">
                </div>

                <!-- Row 4 -->
                <div class="sm:col-span-2">
                    <label class="label">Tanggal Mulai *</label>
                    <input type="date" name="tanggal" required x-model="tanggalMulai" @change="updateTahun()" class="input">
                </div>
                <div class="sm:col-span-2">
                    <label class="label">Tanggal Akhir *</label>
                    <input type="date" name="tanggal_akhir" required class="input">
                </div>
                <div class="sm:col-span-2">
                    <label class="label">Tahun Diklat (Otomatis)</label>
                    <input type="text" disabled x-text="tahunDiklat" :value="tahunDiklat" class="input bg-stone-100 dark:bg-stone-800 cursor-not-allowed select-none font-semibold">
                </div>

                <!-- Row 5 -->
                <div class="sm:col-span-2">
                    <label class="label">Nomor Sertifikat *</label>
                    <input type="text" name="no_sertifikat" required placeholder="No. Sertifikat" class="input mono">
                </div>
                <div class="sm:col-span-2">
                    <label class="label">Tanggal Sertifikat *</label>
                    <input type="date" name="tanggal_sertifikat" required class="input">
                </div>
                <div class="sm:col-span-2">
                    <label class="label">Arsip Sertifikat (PDF/Gambar) *</label>
                    <input type="file" name="sertifikat" required class="input file:mr-3 file:rounded-md file:border-0 file:bg-kpi-cream-soft file:px-3 file:py-1.5 file:text-xs file:font-medium dark:file:bg-white/10">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button class="btn-primary shrink-0">+ Simpan Riwayat</button>
            </div>
        </form>
    </div>
</div>
@endif
