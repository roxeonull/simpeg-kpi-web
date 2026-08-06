<x-app-layout :title="$pegawai->exists ? 'Ubah Pegawai' : 'Tambah Pegawai'">
    @php
        $jabatanOptions = [['value' => '', 'label' => '— Pilih Jabatan —']];
        foreach ($jabatans as $j) {
            $jabatanOptions[] = ['value' => (string)$j->id, 'label' => $j->nama_jabatan];
        }

        $unitOptions = [['value' => '', 'label' => '— Pilih Unit —']];
        foreach ($units as $u) {
            $unitOptions[] = ['value' => (string)$u->id, 'label' => $u->nama_unit];
        }

        $atasanOptions = [['value' => '', 'label' => '— Tidak Ada —']];
        foreach ($atasanList as $a) {
            $atasanOptions[] = ['value' => (string)$a->id, 'label' => $a->nama];
        }

        $genderOptions = [
            ['value' => '', 'label' => '— Pilih Jenis Kelamin —'],
            ['value' => 'L', 'label' => 'Laki-laki (L)'],
            ['value' => 'P', 'label' => 'Perempuan (P)'],
        ];

        $tipeOptions = [
            ['value' => '', 'label' => '— Pilih Tipe Pegawai —'],
            ['value' => 'Struktural', 'label' => 'Struktural'],
            ['value' => 'Fungsional', 'label' => 'Fungsional'],
        ];

        $portalOptions = [
            ['value' => '', 'label' => '— Pilih Status Portal —'],
            ['value' => 'Aktif', 'label' => 'Aktif'],
            ['value' => 'Nonaktif', 'label' => 'Nonaktif'],
        ];

        $simpatikOptions = [
            ['value' => '', 'label' => '— Pilih Status Simpatik —'],
            ['value' => 'Aktif', 'label' => 'Aktif'],
            ['value' => 'Nonaktif', 'label' => 'Nonaktif'],
        ];

        // Deteksi Tab mana yang memiliki error validasi
        $personalFields = [
            'nama', 'nama_panggilan', 'gelar_depan', 'gelar_belakang', 'tempat_lahir', 'tanggal_lahir',
            'jenis_kelamin', 'golongan_darah', 'agama', 'status_marital', 'email', 'email_pribadi', 'no_hp', 'telepon', 'fax',
            'alamat', 'kelurahan', 'kecamatan', 'kota', 'provinsi', 'kode_pos'
        ];

        $kepegawaianFields = [
            'nip', 'jabatan_id', 'unit_id', 'atasan_id', 'status_kepegawaian', 'tmt', 'status_aktif',
            'tipe_pegawai', 'jabatan_plt', 'jabatan_plh', 'tmt_cpns', 'tmt_pns', 'pangkat_golongan',
            'tmt_kepangkatan', 'tmt_pangkat_berikutnya', 'portal_status', 'simpatik_status', 'mendapat_tunkin'
        ];

        $lainLainFields = [
            'no_ktp', 'file_ktp', 'file_sk', 'foto', 'no_karis_karsu', 'file_karis_karsu',
            'no_bpjs_kesehatan', 'file_bpjs_kesehatan', 'no_taspen', 'file_taspen', 'no_npwp', 'file_npwp',
            'no_kartu_asn_virtual', 'file_kartu_asn_virtual', 'bkn_pns_id', 'no_bpjs_ketenagakerjaan',
            'file_bpjs_ketenagakerjaan', 'no_kartu_keluarga', 'file_kartu_keluarga', 'tinggi_badan',
            'berat_badan', 'jenis_rambut', 'bentuk_muka', 'warna_kulit', 'ciri_khas', 'cacat_tubuh', 'hobi'
        ];

        $initialTab = 'personal';
        foreach ($errors->keys() as $errorKey) {
            if (in_array($errorKey, $kepegawaianFields)) {
                $initialTab = 'kepegawaian';
                break;
            } elseif (in_array($errorKey, $lainLainFields)) {
                $initialTab = 'lain_lain';
                break;
            }
        }
    @endphp

    <form method="POST"
          action="{{ $pegawai->exists ? route('pegawai.update', $pegawai) : route('pegawai.store') }}"
          enctype="multipart/form-data" 
          class="max-w-4xl space-y-6"
          x-data="{ activeTab: '{{ $initialTab }}' }">
        @csrf
        @if ($pegawai->exists) @method('PUT') @endif

        <!-- Banner Petunjuk Field Wajib -->
        <div class="flex items-center gap-2.5 rounded-xl border border-amber-200 bg-amber-50/80 p-3.5 text-xs text-amber-900 shadow-xs dark:border-amber-900/40 dark:bg-amber-950/20 dark:text-amber-300">
            <svg class="h-4 w-4 shrink-0 text-amber-600 dark:text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
            </svg>
            <div>
                Field dengan tanda bintang merah (<span class="font-bold text-kpi-red">*</span>) <strong>wajib diisi</strong>:
                <span class="font-medium">Nama Lengkap</span> (Tab Data Personal),
                <span class="font-medium">NIP</span>,
                <span class="font-medium">Status Kepegawaian</span>, dan
                <span class="font-medium">Status Aktif</span> (Tab Data Kepegawaian).
            </div>
        </div>

        <!-- Tab Horizontal Navigation -->
        <div class="flex gap-1 overflow-x-auto border-b border-kpi-line px-1 dark:border-white/10">
            <button type="button" @click="activeTab = 'personal'" :class="activeTab === 'personal' ? 'border-kpi-red text-kpi-red font-semibold' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200'" class="whitespace-nowrap border-b-2 px-6 py-3 text-sm font-medium transition-colors">
                Data Personal <span class="text-kpi-red font-bold">*</span>
            </button>
            <button type="button" @click="activeTab = 'kepegawaian'" :class="activeTab === 'kepegawaian' ? 'border-kpi-red text-kpi-red font-semibold' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200'" class="whitespace-nowrap border-b-2 px-6 py-3 text-sm font-medium transition-colors">
                Data Kepegawaian <span class="text-kpi-red font-bold">*</span>
            </button>
            <button type="button" @click="activeTab = 'lain_lain'" :class="activeTab === 'lain_lain' ? 'border-kpi-red text-kpi-red font-semibold' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200'" class="whitespace-nowrap border-b-2 px-6 py-3 text-sm font-medium transition-colors">
                Data Lain-Lain
            </button>
        </div>

        <!-- ==================== TAB 1: DATA PERSONAL ==================== -->
        <div x-show="activeTab === 'personal'" class="space-y-6">
            <!-- Identitas Pribadi -->
            <div class="card relative z-20">
                <p class="eyebrow">Identitas Pribadi</p>
                <h2 class="mt-1 mb-5 font-serif text-lg font-semibold">Nama & Data Kelahiran</h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label class="label">Gelar Depan</label>
                        <input type="text" name="gelar_depan" value="{{ old('gelar_depan', $pegawai->gelar_depan) }}" placeholder="Contoh: Dr., Ir." class="input">
                    </div>
                    <div>
                        <label class="label">Nama Lengkap <span class="text-kpi-red font-bold">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama', $pegawai->nama) }}" required placeholder="Nama lengkap sesuai KTP" class="input">
                    </div>
                    <div>
                        <label class="label">Gelar Belakang</label>
                        <input type="text" name="gelar_belakang" value="{{ old('gelar_belakang', $pegawai->gelar_belakang) }}" placeholder="Contoh: S.Kom, M.T." class="input">
                    </div>
                    <div>
                        <label class="label">Nama Panggilan</label>
                        <input type="text" name="nama_panggilan" value="{{ old('nama_panggilan', $pegawai->nama_panggilan) }}" class="input">
                    </div>
                    <div>
                        <label class="label">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $pegawai->tempat_lahir) }}" class="input">
                    </div>
                    <div>
                        <label class="label">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', optional($pegawai->tanggal_lahir)->format('Y-m-d')) }}" class="input">
                    </div>
                    <div>
                        <label class="label">Jenis Kelamin</label>
                        <x-select name="jenis_kelamin" :value="old('jenis_kelamin', $pegawai->jenis_kelamin) ?? ''" :options="$genderOptions" class="w-full" />
                    </div>
                    <div>
                        <label class="label">Golongan Darah</label>
                        <input type="text" name="golongan_darah" value="{{ old('golongan_darah', $pegawai->golongan_darah) }}" placeholder="A / B / AB / O" class="input mono">
                    </div>
                    <div>
                        <label class="label">Agama</label>
                        <input type="text" name="agama" value="{{ old('agama', $pegawai->agama) }}" class="input">
                    </div>
                    <div>
                        <label class="label">Status Marital</label>
                        <input type="text" name="status_marital" value="{{ old('status_marital', $pegawai->status_marital) }}" placeholder="Belum Kawin / Kawin / Cerai" class="input">
                    </div>
                </div>
            </div>

            <!-- Kontak & Alamat -->
            <div class="card relative z-10">
                <p class="eyebrow">Kontak & Alamat</p>
                <h2 class="mt-1 mb-5 font-serif text-lg font-semibold">Alamat Rumah & Informasi Kontak</h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label">Email Resmi (Kantor)</label>
                        <input type="email" name="email" value="{{ old('email', $pegawai->email) }}" placeholder="email@instansi.go.id" class="input">
                    </div>
                    <div>
                        <label class="label">Email Pribadi</label>
                        <input type="email" name="email_pribadi" value="{{ old('email_pribadi', $pegawai->email_pribadi) }}" placeholder="email@gmail.com" class="input">
                    </div>
                    <div>
                        <label class="label">No. HP Utama</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $pegawai->no_hp) }}" placeholder="08xxxxxxxxxx" class="input mono">
                    </div>
                    <div>
                        <label class="label">No. Telepon Tambahan/Kantor</label>
                        <input type="text" name="telepon" value="{{ old('telepon', $pegawai->telepon) }}" class="input mono">
                    </div>
                    <div>
                        <label class="label">No. Fax</label>
                        <input type="text" name="fax" value="{{ old('fax', $pegawai->fax) }}" class="input mono">
                    </div>
                    <div>
                        <!-- Spacer for grid alignment -->
                    </div>
                    
                    <div class="sm:col-span-2">
                        <label class="label">Alamat Lengkap (Sesuai KTP)</label>
                        <textarea name="alamat" rows="2" class="input">{{ old('alamat', $pegawai->alamat) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4 sm:col-span-2">
                        <div>
                            <label class="label">Kelurahan</label>
                            <input type="text" name="kelurahan" value="{{ old('kelurahan', $pegawai->kelurahan) }}" class="input">
                        </div>
                        <div>
                            <label class="label">Kecamatan</label>
                            <input type="text" name="kecamatan" value="{{ old('kecamatan', $pegawai->kecamatan) }}" class="input">
                        </div>
                        <div>
                            <label class="label">Kota/Kabupaten</label>
                            <input type="text" name="kota" value="{{ old('kota', $pegawai->kota) }}" class="input">
                        </div>
                        <div>
                            <label class="label">Provinsi</label>
                            <input type="text" name="provinsi" value="{{ old('provinsi', $pegawai->provinsi) }}" class="input">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="label">Kode Pos</label>
                            <input type="text" name="kode_pos" value="{{ old('kode_pos', $pegawai->kode_pos) }}" class="input mono">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== TAB 2: DATA KEPEGAWAIAN ==================== -->
        <div x-show="activeTab === 'kepegawaian'" class="space-y-6" x-cloak>
            <!-- Data Pokok Kepegawaian -->
            <div class="card relative z-20">
                <p class="eyebrow">Data Pokok</p>
                <h2 class="mt-1 mb-5 font-serif text-lg font-semibold">Struktur & Status Organisasi</h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label">NIP (Nomor Induk Pegawai) <span class="text-kpi-red font-bold">*</span></label>
                        <input type="text" name="nip" value="{{ old('nip', $pegawai->nip) }}" required class="input mono">
                    </div>
                    <div>
                        <label class="label">Tipe Pegawai</label>
                        <x-select name="tipe_pegawai" :value="old('tipe_pegawai', $pegawai->tipe_pegawai) ?? ''" :options="$tipeOptions" class="w-full" />
                    </div>
                    <div>
                        <label class="label">Status Kepegawaian <span class="text-kpi-red font-bold">*</span></label>
                        <x-select name="status_kepegawaian" :value="old('status_kepegawaian', $pegawai->status_kepegawaian) ?? ''" :options="[
                            ['value' => 'PNS', 'label' => 'PNS'],
                            ['value' => 'PPPK', 'label' => 'PPPK'],
                            ['value' => 'Non-ASN', 'label' => 'Non-ASN']
                        ]" class="w-full" />
                    </div>
                    <div>
                        <label class="label">Status Aktif <span class="text-kpi-red font-bold">*</span></label>
                        <x-select name="status_aktif" :value="old('status_aktif', $pegawai->status_aktif ?: 'aktif') ?? ''" :options="[
                            ['value' => 'aktif', 'label' => 'Aktif'],
                            ['value' => 'nonaktif', 'label' => 'Nonaktif']
                        ]" class="w-full" />
                    </div>
                    <div>
                        <label class="label">Jabatan Utama</label>
                        <x-select name="jabatan_id" :value="old('jabatan_id', $pegawai->jabatan_id) ?? ''" :options="$jabatanOptions" class="w-full" />
                    </div>
                    <div>
                        <label class="label">Unit Kerja</label>
                        <x-select name="unit_id" :value="old('unit_id', $pegawai->unit_id) ?? ''" :options="$unitOptions" class="w-full" />
                    </div>
                    <div>
                        <label class="label">Atasan Langsung</label>
                        @php
                            // Build options without the empty "Tidak Ada" entry —
                            // clearLabel prop handles that row natively in x-select-search.
                            $atasanSearchOptions = array_values(
                                array_filter($atasanOptions, fn($o) => $o['value'] !== '')
                            );
                        @endphp
                        <x-select-search
                            name="atasan_id"
                            :value="old('atasan_id', $pegawai->atasan_id) ?? ''"
                            :options="$atasanSearchOptions"
                            placeholder="Ketik nama atasan..."
                            clearLabel="— Tidak Ada —"
                            class="w-full" />
                    </div>
                    <div>
                        <!-- Spacer -->
                    </div>
                    <div>
                        <label class="label">Jabatan PLT</label>
                        <input type="text" name="jabatan_plt" value="{{ old('jabatan_plt', $pegawai->jabatan_plt) }}" placeholder="Diisi jika merangkap PLT" class="input">
                    </div>
                    <div>
                        <label class="label">Jabatan PLH</label>
                        <input type="text" name="jabatan_plh" value="{{ old('jabatan_plh', $pegawai->jabatan_plh) }}" placeholder="Diisi jika merangkap PLH" class="input">
                    </div>
                </div>
            </div>

            <!-- Kepangkatan & TMT -->
            <div class="card relative z-10">
                <p class="eyebrow">Kepangkatan & TMT</p>
                <h2 class="mt-1 mb-5 font-serif text-lg font-semibold">TMT & Informasi Golongan/Tunkin</h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label">Pangkat / Golongan</label>
                        <input type="text" name="pangkat_golongan" value="{{ old('pangkat_golongan', $pegawai->pangkat_golongan) }}" placeholder="Contoh: Pembina - IV/A" class="input mono">
                    </div>
                    <div>
                        <label class="label">TMT Kepangkatan</label>
                        <input type="date" name="tmt_kepangkatan" value="{{ old('tmt_kepangkatan', optional($pegawai->tmt_kepangkatan)->format('Y-m-d')) }}" class="input">
                    </div>
                    <div>
                        <label class="label">TMT CPNS</label>
                        <input type="date" name="tmt_cpns" value="{{ old('tmt_cpns', optional($pegawai->tmt_cpns)->format('Y-m-d')) }}" class="input">
                    </div>
                    <div>
                        <label class="label">TMT PNS</label>
                        <input type="date" name="tmt_pns" value="{{ old('tmt_pns', optional($pegawai->tmt_pns)->format('Y-m-d')) }}" class="input">
                    </div>
                    <div>
                        <label class="label">TMT Jabatan (TMT Sistem)</label>
                        <input type="date" name="tmt" value="{{ old('tmt', optional($pegawai->tmt)->format('Y-m-d')) }}" class="input">
                    </div>
                    <div>
                        <label class="label">TMT Pangkat Berikutnya</label>
                        <input type="date" name="tmt_pangkat_berikutnya" value="{{ old('tmt_pangkat_berikutnya', optional($pegawai->tmt_pangkat_berikutnya)->format('Y-m-d')) }}" class="input">
                    </div>
                    <div>
                        <label class="label">Status Portal Kepegawaian</label>
                        <x-select name="portal_status" :value="old('portal_status', $pegawai->portal_status) ?? ''" :options="$portalOptions" class="w-full" />
                    </div>
                    <div>
                        <label class="label">Status SIMPATIK</label>
                        <x-select name="simpatik_status" :value="old('simpatik_status', $pegawai->simpatik_status) ?? ''" :options="$simpatikOptions" class="w-full" />
                    </div>
                    <div class="sm:col-span-2 flex items-center gap-3 py-2">
                        <input type="checkbox" name="mendapat_tunkin" id="mendapat_tunkin" value="1" @checked(old('mendapat_tunkin', $pegawai->mendapat_tunkin)) class="h-4 w-4 rounded border-stone-300 text-kpi-red focus:ring-kpi-red/20 dark:border-white/10 dark:bg-kpi-dark-surface">
                        <label for="mendapat_tunkin" class="text-sm font-medium text-kpi-black dark:text-stone-200 select-none">Mendapat Tunjangan Kinerja (Tunkin)</label>
                    </div>
                </div>
            </div>

            <!-- computed read-only info (hanya jika edit) -->
            @if($pegawai->exists)
            <div class="card bg-kpi-cream-soft/40 dark:bg-white/[0.01] border-dashed">
                <p class="eyebrow text-kpi-gray">Kalkulasi Masa Kerja (Computed)</p>
                <h2 class="mt-1 mb-5 font-serif text-lg font-semibold text-kpi-gray">Informasi Masa Kerja Otomatis</h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label class="label text-kpi-gray">Masa Kerja Keseluruhan</label>
                        <input type="text" readonly value="{{ $pegawai->masa_kerja_keseluruhan ?? '—' }}" class="input bg-stone-100 dark:bg-stone-800 text-kpi-gray cursor-not-allowed">
                    </div>
                    <div>
                        <label class="label text-kpi-gray">Masa Kerja Golongan</label>
                        <input type="text" readonly value="{{ $pegawai->masa_kerja_golongan ?? '—' }}" class="input bg-stone-100 dark:bg-stone-800 text-kpi-gray cursor-not-allowed">
                    </div>
                    <div>
                        <label class="label text-kpi-gray">Pangkat Berikutnya (Estimasi)</label>
                        <input type="text" readonly value="Data belum tersedia" class="input bg-stone-100 dark:bg-stone-800 text-kpi-gray cursor-not-allowed">
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- ==================== TAB 3: DATA LAIN-LAIN ==================== -->
        <div x-show="activeTab === 'lain_lain'" class="space-y-6" x-cloak>
            <!-- Data Kependudukan & Fisik -->
            <div class="card relative z-10">
                <p class="eyebrow">Kependudukan & Fisik</p>
                <h2 class="mt-1 mb-5 font-serif text-lg font-semibold">Identitas Negara & Deskripsi Fisik</h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label class="label">No. KTP (NIK)</label>
                        <input type="text" name="no_ktp" value="{{ old('no_ktp', $pegawai->no_ktp) }}" placeholder="16 digit NIK" class="input mono">
                    </div>
                    <div>
                        <label class="label">No. Kartu Keluarga</label>
                        <input type="text" name="no_kartu_keluarga" value="{{ old('no_kartu_keluarga', $pegawai->no_kartu_keluarga) }}" placeholder="16 digit No. KK" class="input mono">
                    </div>
                    <div>
                        <label class="label">BKN PNS ID</label>
                        <input type="text" name="bkn_pns_id" value="{{ old('bkn_pns_id', $pegawai->bkn_pns_id) }}" class="input mono">
                    </div>
                    <div>
                        <label class="label">Tinggi Badan (cm)</label>
                        <input type="number" name="tinggi_badan" value="{{ old('tinggi_badan', $pegawai->tinggi_badan) }}" placeholder="cm" class="input">
                    </div>
                    <div>
                        <label class="label">Berat Badan (kg)</label>
                        <input type="number" name="berat_badan" value="{{ old('berat_badan', $pegawai->berat_badan) }}" placeholder="kg" class="input">
                    </div>
                    <div>
                        <label class="label">Jenis Rambut</label>
                        <input type="text" name="jenis_rambut" value="{{ old('jenis_rambut', $pegawai->jenis_rambut) }}" placeholder="Lurus, keriting, dll." class="input">
                    </div>
                    <div>
                        <label class="label">Bentuk Muka</label>
                        <input type="text" name="bentuk_muka" value="{{ old('bentuk_muka', $pegawai->bentuk_muka) }}" placeholder="Oval, bulat, dll." class="input">
                    </div>
                    <div>
                        <label class="label">Warna Kulit</label>
                        <input type="text" name="warna_kulit" value="{{ old('warna_kulit', $pegawai->warna_kulit) }}" placeholder="Sawo matang, dll." class="input">
                    </div>
                    <div>
                        <label class="label">Ciri Khas</label>
                        <input type="text" name="ciri_khas" value="{{ old('ciri_khas', $pegawai->ciri_khas) }}" placeholder="Tanda lahir, dll." class="input">
                    </div>
                    <div>
                        <label class="label">Cacat Tubuh (Tinggalkan kosong jika Tidak Ada)</label>
                        <input type="text" name="cacat_tubuh" value="{{ old('cacat_tubuh', $pegawai->cacat_tubuh) }}" placeholder="Tidak ada" class="input">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label">Hobi</label>
                        <input type="text" name="hobi" value="{{ old('hobi', $pegawai->hobi) }}" placeholder="Membaca, olahraga, dll." class="input">
                    </div>
                </div>
            </div>

            <!-- Keanggotaan & Berkas Dokumen -->
            <div class="card">
                <p class="eyebrow">Dokumen & Arsip</p>
                <h2 class="mt-1 mb-5 font-serif text-lg font-semibold">Nomor Keanggotaan & Berkas Digital</h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <!-- Foto Profil -->
                    <div class="p-4 border rounded-xl border-stone-200 dark:border-white/10 space-y-2">
                        <label class="label font-semibold">Foto Profil</label>
                        <input type="file" name="foto" accept="image/*" class="input file:mr-3 file:rounded-md file:border-0 file:bg-kpi-cream-soft file:px-3 file:py-1.5 file:text-xs file:font-medium dark:file:bg-white/10">
                        @if($pegawai->foto)
                            <p class="text-xs text-kpi-gray mt-1">Berkas saat ini: <a href="{{ \Illuminate\Support\Facades\Storage::url($pegawai->foto) }}" target="_blank" class="text-kpi-red font-medium hover:underline">Lihat Foto</a></p>
                        @endif
                    </div>

                    <!-- SK Kepegawaian -->
                    <div class="p-4 border rounded-xl border-stone-200 dark:border-white/10 space-y-2">
                        <label class="label font-semibold">File SK Kepegawaian (PDF/Gambar)</label>
                        <input type="file" name="file_sk" class="input file:mr-3 file:rounded-md file:border-0 file:bg-kpi-cream-soft file:px-3 file:py-1.5 file:text-xs file:font-medium dark:file:bg-white/10">
                        @if($pegawai->file_sk)
                            <p class="text-xs text-kpi-gray mt-1">Berkas saat ini: <a href="{{ \Illuminate\Support\Facades\Storage::url($pegawai->file_sk) }}" target="_blank" class="text-kpi-red font-medium hover:underline">Lihat SK</a></p>
                        @endif
                    </div>

                    <!-- KTP -->
                    <div class="p-4 border rounded-xl border-stone-200 dark:border-white/10 space-y-2 sm:col-span-2">
                        <label class="label font-semibold">File KTP (PDF/Gambar)</label>
                        <input type="file" name="file_ktp" class="input file:mr-3 file:rounded-md file:border-0 file:bg-kpi-cream-soft file:px-3 file:py-1.5 file:text-xs file:font-medium dark:file:bg-white/10">
                        @if($pegawai->file_ktp)
                            <p class="text-xs text-kpi-gray mt-1">Berkas saat ini: <a href="{{ \Illuminate\Support\Facades\Storage::url($pegawai->file_ktp) }}" target="_blank" class="text-kpi-red font-medium hover:underline">Lihat KTP</a></p>
                        @endif
                    </div>

                    <!-- Kartu Keluarga -->
                    <div class="p-4 border rounded-xl border-stone-200 dark:border-white/10 space-y-3">
                        <label class="label font-semibold">File Kartu Keluarga (PDF/Gambar)</label>
                        <input type="file" name="file_kartu_keluarga" class="input file:mr-3 file:rounded-md file:border-0 file:bg-kpi-cream-soft file:px-3 file:py-1.5 file:text-xs file:font-medium dark:file:bg-white/10">
                        @if($pegawai->file_kartu_keluarga)
                            <p class="text-xs text-kpi-gray mt-1">Berkas saat ini: <a href="{{ \Illuminate\Support\Facades\Storage::url($pegawai->file_kartu_keluarga) }}" target="_blank" class="text-kpi-red font-medium hover:underline">Lihat Kartu Keluarga</a></p>
                        @endif
                    </div>

                    <!-- Karis Karsu -->
                    <div class="p-4 border rounded-xl border-stone-200 dark:border-white/10 space-y-2">
                        <label class="label font-semibold">No. Karis / Karsu</label>
                        <input type="text" name="no_karis_karsu" value="{{ old('no_karis_karsu', $pegawai->no_karis_karsu) }}" class="input mono">
                        <label class="label mt-2">File Kartu Karis/Karsu</label>
                        <input type="file" name="file_karis_karsu" class="input file:mr-3 file:rounded-md file:border-0 file:bg-kpi-cream-soft file:px-3 file:py-1.5 file:text-xs file:font-medium dark:file:bg-white/10">
                        @if($pegawai->file_karis_karsu)
                            <p class="text-xs text-kpi-gray mt-1">Berkas saat ini: <a href="{{ \Illuminate\Support\Facades\Storage::url($pegawai->file_karis_karsu) }}" target="_blank" class="text-kpi-red font-medium hover:underline">Lihat Kartu</a></p>
                        @endif
                    </div>

                    <!-- BPJS Kesehatan -->
                    <div class="p-4 border rounded-xl border-stone-200 dark:border-white/10 space-y-2">
                        <label class="label font-semibold">No. BPJS Kesehatan</label>
                        <input type="text" name="no_bpjs_kesehatan" value="{{ old('no_bpjs_kesehatan', $pegawai->no_bpjs_kesehatan) }}" class="input mono">
                        <label class="label mt-2">File Kartu BPJS Kesehatan</label>
                        <input type="file" name="file_bpjs_kesehatan" class="input file:mr-3 file:rounded-md file:border-0 file:bg-kpi-cream-soft file:px-3 file:py-1.5 file:text-xs file:font-medium dark:file:bg-white/10">
                        @if($pegawai->file_bpjs_kesehatan)
                            <p class="text-xs text-kpi-gray mt-1">Berkas saat ini: <a href="{{ \Illuminate\Support\Facades\Storage::url($pegawai->file_bpjs_kesehatan) }}" target="_blank" class="text-kpi-red font-medium hover:underline">Lihat Kartu</a></p>
                        @endif
                    </div>

                    <!-- BPJS Ketenagakerjaan -->
                    <div class="p-4 border rounded-xl border-stone-200 dark:border-white/10 space-y-2">
                        <label class="label font-semibold">No. BPJS Ketenagakerjaan</label>
                        <input type="text" name="no_bpjs_ketenagakerjaan" value="{{ old('no_bpjs_ketenagakerjaan', $pegawai->no_bpjs_ketenagakerjaan) }}" class="input mono">
                        <label class="label mt-2">File Kartu BPJS Ketenagakerjaan</label>
                        <input type="file" name="file_bpjs_ketenagakerjaan" class="input file:mr-3 file:rounded-md file:border-0 file:bg-kpi-cream-soft file:px-3 file:py-1.5 file:text-xs file:font-medium dark:file:bg-white/10">
                        @if($pegawai->file_bpjs_ketenagakerjaan)
                            <p class="text-xs text-kpi-gray mt-1">Berkas saat ini: <a href="{{ \Illuminate\Support\Facades\Storage::url($pegawai->file_bpjs_ketenagakerjaan) }}" target="_blank" class="text-kpi-red font-medium hover:underline">Lihat Kartu</a></p>
                        @endif
                    </div>

                    <!-- Taspen -->
                    <div class="p-4 border rounded-xl border-stone-200 dark:border-white/10 space-y-2">
                        <label class="label font-semibold">No. Taspen</label>
                        <input type="text" name="no_taspen" value="{{ old('no_taspen', $pegawai->no_taspen) }}" class="input mono">
                        <label class="label mt-2">File Kartu Taspen</label>
                        <input type="file" name="file_taspen" class="input file:mr-3 file:rounded-md file:border-0 file:bg-kpi-cream-soft file:px-3 file:py-1.5 file:text-xs file:font-medium dark:file:bg-white/10">
                        @if($pegawai->file_taspen)
                            <p class="text-xs text-kpi-gray mt-1">Berkas saat ini: <a href="{{ \Illuminate\Support\Facades\Storage::url($pegawai->file_taspen) }}" target="_blank" class="text-kpi-red font-medium hover:underline">Lihat Kartu</a></p>
                        @endif
                    </div>

                    <!-- NPWP -->
                    <div class="p-4 border rounded-xl border-stone-200 dark:border-white/10 space-y-2">
                        <label class="label font-semibold">No. NPWP</label>
                        <input type="text" name="no_npwp" value="{{ old('no_npwp', $pegawai->no_npwp) }}" class="input mono">
                        <label class="label mt-2">File Kartu NPWP</label>
                        <input type="file" name="file_npwp" class="input file:mr-3 file:rounded-md file:border-0 file:bg-kpi-cream-soft file:px-3 file:py-1.5 file:text-xs file:font-medium dark:file:bg-white/10">
                        @if($pegawai->file_npwp)
                            <p class="text-xs text-kpi-gray mt-1">Berkas saat ini: <a href="{{ \Illuminate\Support\Facades\Storage::url($pegawai->file_npwp) }}" target="_blank" class="text-kpi-red font-medium hover:underline">Lihat NPWP</a></p>
                        @endif
                    </div>

                    <!-- Kartu ASN Virtual -->
                    <div class="p-4 border rounded-xl border-stone-200 dark:border-white/10 space-y-2">
                        <label class="label font-semibold">No. Kartu ASN Virtual</label>
                        <input type="text" name="no_kartu_asn_virtual" value="{{ old('no_kartu_asn_virtual', $pegawai->no_kartu_asn_virtual) }}" class="input mono">
                        <label class="label mt-2">File Kartu ASN Virtual</label>
                        <input type="file" name="file_kartu_asn_virtual" class="input file:mr-3 file:rounded-md file:border-0 file:bg-kpi-cream-soft file:px-3 file:py-1.5 file:text-xs file:font-medium dark:file:bg-white/10">
                        @if($pegawai->file_kartu_asn_virtual)
                            <p class="text-xs text-kpi-gray mt-1">Berkas saat ini: <a href="{{ \Illuminate\Support\Facades\Storage::url($pegawai->file_kartu_asn_virtual) }}" target="_blank" class="text-kpi-red font-medium hover:underline">Lihat Kartu</a></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Submit & Cancel Buttons -->
        <div class="flex gap-3 pt-4">
            <button type="submit" class="btn-primary">Simpan</button>
            <a href="{{ route('pegawai.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</x-app-layout>
