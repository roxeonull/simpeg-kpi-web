<x-app-layout title="Detail Pegawai">
    <a href="{{ route('pegawai.index') }}" class="mb-4 inline-flex items-center gap-1.5 text-sm font-medium text-kpi-gray hover:text-kpi-red">
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali ke daftar pegawai
    </a>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-kpi-line bg-white p-5 shadow-[var(--shadow-card)] dark:border-white/10 dark:bg-kpi-dark-surface">
        <div class="flex items-center gap-4">
            @if($pegawai->foto)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($pegawai->foto) }}" alt="{{ $pegawai->nama }}" class="h-16 w-16 shrink-0 rounded-full object-cover border border-kpi-line dark:border-white/10">
            @else
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-kpi-gold-soft text-xl font-serif font-semibold text-kpi-red-dark dark:bg-kpi-gold/20 dark:text-kpi-gold">
                    {{ strtoupper(substr($pegawai->nama, 0, 2)) }}
                </div>
            @endif
            <div>
                <h2 class="font-serif text-xl font-semibold">{{ $pegawai->nama }}</h2>
                <p class="mono mt-0.5 text-sm text-kpi-gray">{{ $pegawai->nip }}</p>
                <p class="mt-1 text-sm text-kpi-gray">{{ $pegawai->jabatan?->nama_jabatan ?? '—' }} &middot; {{ $pegawai->unit?->nama_unit ?? '—' }}</p>
            </div>
        </div>
        @if(auth()->user()->role === 'admin')
        <div class="flex items-center gap-2">
            @if(!$pegawai->user_id)
                <a href="{{ route('user.create', ['pegawai_id' => $pegawai->id]) }}" class="btn-secondary">
                    <svg class="h-4 w-4 text-kpi-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Buat Akun Login
                </a>
            @else
                <a href="{{ route('user.edit', $pegawai->user_id) }}" class="btn-secondary">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Kelola Akun
                </a>
            @endif
            <a href="{{ route('pegawai.edit', $pegawai) }}" class="btn-secondary">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6.586-6.586a2 2 0 112.828 2.828L11.828 13.828H9V11z"/></svg>
                Ubah Data
            </a>
        </div>
        @endif
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="card">
            <p class="text-sm text-kpi-gray">Saldo Cuti Tahunan {{ $saldoCuti->tahun }}</p>
            <p class="stat-figure mt-2 !text-2xl">{{ $saldoCuti->sisa_saldo }} <span class="text-sm font-sans font-normal text-kpi-gray">/ {{ $saldoCuti->total_saldo }} hari</span></p>
        </div>
        <div class="card">
            <p class="text-sm text-kpi-gray">Total JP Diklat Tahun Ini</p>
            <p class="stat-figure mt-2 !text-2xl">{{ $totalJp }} <span class="text-sm font-sans font-normal text-kpi-gray">JP</span></p>
        </div>
        <div class="card">
            <p class="text-sm text-kpi-gray">Status Kepegawaian</p>
            <p class="mt-2.5 flex flex-wrap gap-1.5"><x-badge color="info">{{ $pegawai->status_kepegawaian }}</x-badge> <x-badge :color="$pegawai->status_aktif === 'aktif' ? 'success' : 'default'">{{ ucfirst($pegawai->status_aktif) }}</x-badge></p>
        </div>
    </div>

    <!-- Informasi Lengkap dengan Navigasi Tabs -->
    <div x-data="{ infoTab: 'personal' }">
        <div class="mb-4 flex gap-1 overflow-x-auto border-b border-kpi-line dark:border-white/10">
            <button @click="infoTab = 'personal'" :class="infoTab === 'personal' ? 'border-kpi-red text-kpi-red font-semibold' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200'" class="whitespace-nowrap border-b-2 px-5 py-3 text-sm font-medium transition-colors">
                Data Personal
            </button>
            <button @click="infoTab = 'kepegawaian'" :class="infoTab === 'kepegawaian' ? 'border-kpi-red text-kpi-red font-semibold' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200'" class="whitespace-nowrap border-b-2 px-5 py-3 text-sm font-medium transition-colors">
                Data Kepegawaian
            </button>
            <button @click="infoTab = 'lain_lain'" :class="infoTab === 'lain_lain' ? 'border-kpi-red text-kpi-red font-semibold' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200'" class="whitespace-nowrap border-b-2 px-5 py-3 text-sm font-medium transition-colors">
                Data Lain-Lain & Dokumen
            </button>
        </div>

        <!-- Tab Content Card -->
        <div class="card mb-6">
            <!-- TAB 1: DATA PERSONAL -->
            <div x-show="infoTab === 'personal'" class="space-y-6">
                <div>
                    <h3 class="font-serif text-lg font-semibold text-kpi-black dark:text-stone-100">Data Personal</h3>
                    <p class="text-xs text-kpi-gray mt-0.5">Informasi identitas pribadi dan kontak pegawai</p>
                </div>
                <dl class="grid grid-cols-1 gap-5 text-sm sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Nama Lengkap (Tanpa Gelar)</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->nama }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Gelar Depan</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->gelar_depan ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Gelar Belakang</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->gelar_belakang ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Nama Panggilan</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->nama_panggilan ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Tempat Lahir</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->tempat_lahir ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Tanggal Lahir</dt>
                        <dd class="mono mt-1 font-medium">{{ $pegawai->tanggal_lahir ? $pegawai->tanggal_lahir->translatedFormat('d F Y') : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Jenis Kelamin</dt>
                        <dd class="mt-1 font-medium">
                            @if($pegawai->jenis_kelamin === 'L')
                                Laki-laki
                            @elseif($pegawai->jenis_kelamin === 'P')
                                Perempuan
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Golongan Darah</dt>
                        <dd class="mono mt-1 font-medium">{{ $pegawai->golongan_darah ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Agama</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->agama ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Status Pernikahan</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->status_marital ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Pendidikan Terakhir (Ringkasan)</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->pendidikan_terakhir ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Jurusan Pendidikan</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->jurusan_pendidikan ?? '—' }}</dd>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-3">
                        <dt class="eyebrow !normal-case !tracking-normal">Universitas / Lembaga Pendidikan</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->universitas ?? '—' }}</dd>
                    </div>
                    
                    <div class="border-t border-kpi-line dark:border-white/10 sm:col-span-2 lg:col-span-3 my-2"></div>

                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Email Resmi</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->email ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Email Pribadi</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->email_pribadi ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">No. HP Utama</dt>
                        <dd class="mono mt-1 font-medium">{{ $pegawai->no_hp ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">No. Telepon Tambahan/Kantor</dt>
                        <dd class="mono mt-1 font-medium">{{ $pegawai->telepon ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Fax</dt>
                        <dd class="mono mt-1 font-medium">{{ $pegawai->fax ?? '—' }}</dd>
                    </div>
                    <div>
                        <!-- empty for layout grid alignment -->
                    </div>

                    <div class="sm:col-span-2 lg:col-span-3">
                        <dt class="eyebrow !normal-case !tracking-normal">Alamat Alamat Lengkap</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->alamat ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Kelurahan</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->kelurahan ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Kecamatan</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->kecamatan ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Kota / Kabupaten</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->kota ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Provinsi</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->provinsi ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Kode Pos</dt>
                        <dd class="mono mt-1 font-medium">{{ $pegawai->kode_pos ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- TAB 2: DATA KEPEGAWAIAN -->
            <div x-show="infoTab === 'kepegawaian'" class="space-y-6" x-cloak>
                <div>
                    <h3 class="font-serif text-lg font-semibold text-kpi-black dark:text-stone-100">Data Kepegawaian</h3>
                    <p class="text-xs text-kpi-gray mt-0.5">Informasi kepangkatan, kedudukan, dan status dinas pegawai</p>
                </div>
                <dl class="grid grid-cols-1 gap-5 text-sm sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">NIP</dt>
                        <dd class="mono mt-1 font-medium">{{ $pegawai->nip }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Tipe Pegawai</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->tipe_pegawai ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Status Kepegawaian</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->status_kepegawaian }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Status Aktif</dt>
                        <dd class="mt-1">
                            <x-badge :color="$pegawai->status_aktif === 'aktif' ? 'success' : 'default'">{{ ucfirst($pegawai->status_aktif) }}</x-badge>
                        </dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Jabatan Utama</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->jabatan?->nama_jabatan ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Unit Kerja</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->unit?->nama_unit ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Atasan Langsung</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->atasan?->nama ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Jabatan PLT</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->jabatan_plt ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Jabatan PLH</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->jabatan_plh ?? '—' }}</dd>
                    </div>

                    <div class="border-t border-kpi-line dark:border-white/10 sm:col-span-2 lg:col-span-3 my-2"></div>

                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Pangkat / Golongan</dt>
                        <dd class="mono mt-1 font-medium">{{ $pegawai->pangkat_golongan ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">TMT Kepangkatan</dt>
                        <dd class="mono mt-1 font-medium">{{ $pegawai->tmt_kepangkatan ? $pegawai->tmt_kepangkatan->translatedFormat('d F Y') : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">TMT CPNS</dt>
                        <dd class="mono mt-1 font-medium">{{ $pegawai->tmt_cpns ? $pegawai->tmt_cpns->translatedFormat('d F Y') : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">TMT PNS</dt>
                        <dd class="mono mt-1 font-medium">{{ $pegawai->tmt_pns ? $pegawai->tmt_pns->translatedFormat('d F Y') : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">TMT Jabatan (TMT Sistem)</dt>
                        <dd class="mono mt-1 font-medium">{{ $pegawai->tmt ? $pegawai->tmt->translatedFormat('d F Y') : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">TMT Pangkat Berikutnya</dt>
                        <dd class="mono mt-1 font-medium">{{ $pegawai->tmt_pangkat_berikutnya ? $pegawai->tmt_pangkat_berikutnya->translatedFormat('d F Y') : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Status Portal Kepegawaian</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->portal_status ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Status SIMPATIK</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->simpatik_status ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Mendapat Tunkin</dt>
                        <dd class="mt-1 font-medium">
                            <x-badge :color="$pegawai->mendapat_tunkin ? 'success' : 'default'">{{ $pegawai->mendapat_tunkin ? 'Ya' : 'Tidak' }}</x-badge>
                        </dd>
                    </div>

                    <div class="border-t border-kpi-line dark:border-white/10 sm:col-span-2 lg:col-span-3 my-2"></div>

                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Masa Kerja Keseluruhan</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->masa_kerja_keseluruhan ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Masa Kerja Golongan</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->masa_kerja_golongan ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Pangkat Berikutnya (Estimasi)</dt>
                        <dd class="mt-1 text-kpi-gray">Data belum tersedia</dd>
                    </div>
                </dl>
            </div>

            <!-- TAB 3: DATA LAIN-LAIN -->
            <div x-show="infoTab === 'lain_lain'" class="space-y-6" x-cloak>
                <div>
                    <h3 class="font-serif text-lg font-semibold text-kpi-black dark:text-stone-100">Data Lain-Lain & Dokumen</h3>
                    <p class="text-xs text-kpi-gray mt-0.5">Informasi kependudukan, karakteristik fisik, dan berkas arsip pendukung</p>
                </div>
                <dl class="grid grid-cols-1 gap-5 text-sm sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">No. KTP (NIK)</dt>
                        <dd class="mono mt-1 font-medium">{{ $pegawai->no_ktp ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">No. Kartu Keluarga</dt>
                        <dd class="mono mt-1 font-medium">{{ $pegawai->no_kartu_keluarga ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">BKN PNS ID</dt>
                        <dd class="mono mt-1 font-medium">{{ $pegawai->bkn_pns_id ?? '—' }}</dd>
                    </div>
                    
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Tinggi Badan</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->tinggi_badan ? $pegawai->tinggi_badan . ' cm' : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Berat Badan</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->berat_badan ? $pegawai->berat_badan . ' kg' : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Jenis Rambut</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->jenis_rambut ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Bentuk Muka</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->bentuk_muka ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Warna Kulit</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->warna_kulit ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Ciri Khas</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->ciri_khas ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Cacat Tubuh</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->cacat_tubuh ?? 'Tidak ada' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="eyebrow !normal-case !tracking-normal">Hobi</dt>
                        <dd class="mt-1 font-medium">{{ $pegawai->hobi ?? '—' }}</dd>
                    </div>

                    <div class="border-t border-kpi-line dark:border-white/10 sm:col-span-2 lg:col-span-3 my-2"></div>

                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">No. Karis / Karsu</dt>
                        <dd class="mono mt-1 font-medium">{{ $pegawai->no_karis_karsu ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">No. BPJS Kesehatan</dt>
                        <dd class="mono mt-1 font-medium">{{ $pegawai->no_bpjs_kesehatan ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">No. BPJS Ketenagakerjaan</dt>
                        <dd class="mono mt-1 font-medium">{{ $pegawai->no_bpjs_ketenagakerjaan ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">No. Taspen</dt>
                        <dd class="mono mt-1 font-medium">{{ $pegawai->no_taspen ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">No. NPWP</dt>
                        <dd class="mono mt-1 font-medium">{{ $pegawai->no_npwp ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">No. Kartu ASN Virtual</dt>
                        <dd class="mono mt-1 font-medium">{{ $pegawai->no_kartu_asn_virtual ?? '—' }}</dd>
                    </div>

                    <div class="border-t border-kpi-line dark:border-white/10 sm:col-span-2 lg:col-span-3 my-2"></div>

                    <!-- Berkas Dokumen Upload -->
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Foto Profil</dt>
                        <dd class="mt-1 font-medium">
                            @if($pegawai->foto)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($pegawai->foto) }}" target="_blank" class="text-sm text-kpi-red hover:underline inline-flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    Lihat File
                                </a>
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Arsip KTP</dt>
                        <dd class="mt-1 font-medium">
                            @if($pegawai->file_ktp)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($pegawai->file_ktp) }}" target="_blank" class="text-sm text-kpi-red hover:underline inline-flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    Lihat File
                                </a>
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Arsip SK Kepegawaian</dt>
                        <dd class="mt-1 font-medium">
                            @if($pegawai->file_sk)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($pegawai->file_sk) }}" target="_blank" class="text-sm text-kpi-red hover:underline inline-flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    Lihat File
                                </a>
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Arsip Kartu Keluarga</dt>
                        <dd class="mt-1 font-medium">
                            @if($pegawai->file_kartu_keluarga)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($pegawai->file_kartu_keluarga) }}" target="_blank" class="text-sm text-kpi-red hover:underline inline-flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    Lihat File
                                </a>
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Arsip Karis / Karsu</dt>
                        <dd class="mt-1 font-medium">
                            @if($pegawai->file_karis_karsu)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($pegawai->file_karis_karsu) }}" target="_blank" class="text-sm text-kpi-red hover:underline inline-flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    Lihat File
                                </a>
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Arsip BPJS Kesehatan</dt>
                        <dd class="mt-1 font-medium">
                            @if($pegawai->file_bpjs_kesehatan)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($pegawai->file_bpjs_kesehatan) }}" target="_blank" class="text-sm text-kpi-red hover:underline inline-flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    Lihat File
                                </a>
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Arsip BPJS Ketenagakerjaan</dt>
                        <dd class="mt-1 font-medium">
                            @if($pegawai->file_bpjs_ketenagakerjaan)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($pegawai->file_bpjs_ketenagakerjaan) }}" target="_blank" class="text-sm text-kpi-red hover:underline inline-flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    Lihat File
                                </a>
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Arsip Taspen</dt>
                        <dd class="mt-1 font-medium">
                            @if($pegawai->file_taspen)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($pegawai->file_taspen) }}" target="_blank" class="text-sm text-kpi-red hover:underline inline-flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    Lihat File
                                </a>
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Arsip NPWP</dt>
                        <dd class="mt-1 font-medium">
                            @if($pegawai->file_npwp)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($pegawai->file_npwp) }}" target="_blank" class="text-sm text-kpi-red hover:underline inline-flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    Lihat File
                                </a>
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="eyebrow !normal-case !tracking-normal">Arsip Kartu ASN Virtual</dt>
                        <dd class="mt-1 font-medium">
                            @if($pegawai->file_kartu_asn_virtual)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($pegawai->file_kartu_asn_virtual) }}" target="_blank" class="text-sm text-kpi-red hover:underline inline-flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    Lihat File
                                </a>
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <div x-data="{ tab: '{{ request('tab', 'pendidikan') }}' }" class="panel">
        <div class="flex gap-1 overflow-x-auto border-b border-kpi-line px-3 dark:border-white/10">
            <button @click="tab = 'pendidikan'" :class="tab === 'pendidikan' ? 'border-kpi-red text-kpi-red' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200'" class="whitespace-nowrap border-b-2 px-4 py-3 text-sm font-medium transition-colors">Pendidikan</button>
            <button @click="tab = 'pelatihan'" :class="tab === 'pelatihan' ? 'border-kpi-red text-kpi-red' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200'" class="whitespace-nowrap border-b-2 px-4 py-3 text-sm font-medium transition-colors">Pelatihan</button>
            <button @click="tab = 'absensi'" :class="tab === 'absensi' ? 'border-kpi-red text-kpi-red' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200'" class="whitespace-nowrap border-b-2 px-4 py-3 text-sm font-medium transition-colors">Riwayat Absensi</button>
            <button @click="tab = 'cuti'" :class="tab === 'cuti' ? 'border-kpi-red text-kpi-red' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200'" class="whitespace-nowrap border-b-2 px-4 py-3 text-sm font-medium transition-colors">Riwayat Cuti</button>
            <button @click="tab = 'shift'" :class="tab === 'shift' ? 'border-kpi-red text-kpi-red' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200'" class="whitespace-nowrap border-b-2 px-4 py-3 text-sm font-medium transition-colors">Jadwal Shift</button>
        </div>

        {{-- Pendidikan --}}
        <div x-show="tab === 'pendidikan'" class="p-5">
            @if(auth()->user()->role === 'admin')
            <form method="POST" action="{{ route('pendidikan.store', $pegawai) }}" enctype="multipart/form-data"
                  class="mb-5 grid grid-cols-1 gap-3 rounded-xl border border-dashed border-stone-300 p-4 dark:border-white/15 sm:grid-cols-5">
                @csrf
                <x-select name="jenjang" value="S1" :options="[
                    ['value' => 'SD', 'label' => 'SD'],
                    ['value' => 'SMP', 'label' => 'SMP'],
                    ['value' => 'SMA/SMK', 'label' => 'SMA/SMK'],
                    ['value' => 'D3', 'label' => 'D3'],
                    ['value' => 'D4', 'label' => 'D4'],
                    ['value' => 'S1', 'label' => 'S1'],
                    ['value' => 'S2', 'label' => 'S2'],
                    ['value' => 'S3', 'label' => 'S3']
                ]" class="sm:col-span-1 w-full" />
                <input type="text" name="institusi" placeholder="Institusi" required class="input sm:col-span-1">
                <input type="text" name="jurusan" placeholder="Jurusan" class="input sm:col-span-1">
                <input type="number" name="tahun_lulus" placeholder="Tahun Lulus" required min="1950" max="{{ date('Y') + 1 }}" class="input sm:col-span-1">
                <div class="flex gap-2 sm:col-span-1">
                    <input type="file" name="file_ijazah" class="input">
                    <button class="btn-primary shrink-0 !px-3">+</button>
                </div>
            </form>
            @endif
            <div class="space-y-2">
                @forelse ($pegawai->riwayatPendidikan as $riwayat)
                    <div class="flex items-center justify-between rounded-xl border border-kpi-line px-4 py-3 dark:border-white/10">
                        <div>
                            <p class="font-medium">{{ $riwayat->jenjang }} — {{ $riwayat->institusi }}</p>
                            <p class="text-sm text-kpi-gray">{{ $riwayat->jurusan }} &middot; Lulus {{ $riwayat->tahun_lulus }}</p>
                        </div>
                        <div class="flex items-center gap-1.5">
                            @if($riwayat->file_ijazah)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($riwayat->file_ijazah) }}" target="_blank" class="btn-xs-secondary">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Lihat File
                                </a>
                            @endif
                            @if(auth()->user()->role === 'admin')
                            <form method="POST" action="{{ route('pendidikan.destroy', $riwayat) }}" class="inline" onsubmit="return confirm('Hapus riwayat ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-xs-danger">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hapus
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-kpi-gray">Belum ada riwayat pendidikan.</p>
                @endforelse
            </div>
        </div>

        {{-- Pelatihan --}}
        <div x-show="tab === 'pelatihan'" x-cloak class="p-5">
            @include('pelatihan._form-tambah')
            @include('pelatihan._list', ['showActions' => false])
        </div>

        {{-- Absensi --}}
        <div x-show="tab === 'absensi'" x-cloak class="p-5">
            {{-- Filter Bulan & Rekap Total Pengurangan Jam Kerja --}}
            <div class="mb-5 flex flex-wrap items-center justify-between gap-4 rounded-xl border border-kpi-line bg-stone-50/50 p-4 dark:border-white/10 dark:bg-white/5">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-kpi-gray">Total Pengurangan Jam Kerja</span>
                    <div class="mt-1 flex items-baseline gap-2">
                        @php
                            $formattedTotal = '0 menit';
                            if (($totalMenitPenguranganBulanIni ?? 0) > 0) {
                                $jamTotal   = floor($totalMenitPenguranganBulanIni / 60);
                                $menitTotal = $totalMenitPenguranganBulanIni % 60;
                                if ($jamTotal > 0 && $menitTotal > 0) {
                                    $formattedTotal = "{$jamTotal} jam {$menitTotal} menit";
                                } elseif ($jamTotal > 0) {
                                    $formattedTotal = "{$jamTotal} jam";
                                } else {
                                    $formattedTotal = "{$menitTotal} menit";
                                }
                            }
                        @endphp
                        <span class="text-2xl font-bold font-serif {{ ($totalMenitPenguranganBulanIni ?? 0) > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-stone-700 dark:text-stone-200' }}">
                            {{ $formattedTotal }}
                        </span>
                        <span class="text-xs text-kpi-gray">
                            ({{ \Carbon\Carbon::parse(request('bulan_absensi', now()->format('Y-m')) . '-01')->translatedFormat('F Y') }})
                        </span>
                    </div>
                </div>

                <form method="GET" action="{{ url()->current() }}" class="flex flex-wrap items-center gap-2">
                    @foreach(request()->except(['bulan_absensi', 'tab']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <input type="hidden" name="tab" value="absensi">
                    
                    <label class="text-xs text-kpi-gray font-semibold">Filter Bulan:</label>
                    <input type="month" name="bulan_absensi" value="{{ request('bulan_absensi', now()->format('Y-m')) }}" onchange="this.form.submit()" class="input max-w-[170px] !py-1.5 !text-xs cursor-pointer">
                </form>
            </div>

            <div class="space-y-2">
                @forelse ($riwayatAbsensi ?? [] as $a)
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-kpi-line px-4 py-3 dark:border-white/10">
                        <div>
                            <p class="mono text-sm font-medium">
                                {{ $a->tanggal->translatedFormat('d F Y') }}
                                &middot;
                                <span class="text-stone-600 dark:text-stone-300">Masuk: {{ $a->jam_masuk ?? '-' }}</span> /
                                <span class="text-stone-600 dark:text-stone-300">Keluar: {{ $a->jam_keluar ?? '-' }}</span>
                            </p>
                            @if($a->keterangan)
                                <p class="text-xs text-kpi-gray mt-0.5">{{ $a->keterangan }}</p>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            @if(($a->menit_pengurangan_jam_kerja ?? 0) > 0)
                                @php
                                    $mHariIni = $a->menit_pengurangan_jam_kerja;
                                    $jH       = floor($mHariIni / 60);
                                    $mH       = $mHariIni % 60;
                                    if ($jH > 0 && $mH > 0) {
                                        $formattedHariIni = "{$jH} jam {$mH}m";
                                    } elseif ($jH > 0) {
                                        $formattedHariIni = "{$jH} jam";
                                    } else {
                                        $formattedHariIni = "{$mH}m";
                                    }
                                @endphp
                                <span class="inline-flex items-center gap-1 rounded-md bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-950/40 dark:text-amber-300 dark:ring-amber-500/30" title="Pengurangan Jam Kerja Hari Ini">
                                    <svg class="h-3 w-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    -{{ $formattedHariIni }}
                                </span>
                            @endif
                            <x-badge :color="$a->statusBadgeColor()">{{ ucfirst($a->status) }}</x-badge>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-kpi-gray">Belum ada riwayat absensi pada bulan ini.</p>
                @endforelse
            </div>
        </div>

        {{-- Cuti --}}
        <div x-show="tab === 'cuti'" x-cloak class="p-5">
            <div class="space-y-2">
                @forelse ($pegawai->cuti as $c)
                    <div class="flex items-center justify-between rounded-xl border border-kpi-line px-4 py-3 dark:border-white/10">
                        <div>
                            <p class="text-sm font-medium">{{ ucfirst($c->jenis_cuti) }} &middot; {{ $c->tanggal_mulai->format('d M') }} - {{ $c->tanggal_selesai->format('d M Y') }}</p>
                            <p class="text-xs text-kpi-gray">{{ $c->jumlah_hari }} hari</p>
                        </div>
                        <x-badge :color="$c->status === 'disetujui' ? 'success' : ($c->status === 'ditolak' ? 'danger' : 'warning')">{{ $c->statusLabel() }}</x-badge>
                    </div>
                @empty
                    <p class="text-sm text-kpi-gray">Belum ada riwayat cuti.</p>
                @endforelse
            </div>
        </div>

        {{-- Jadwal Shift --}}
        <div x-show="tab === 'shift'" x-cloak class="p-5">
            <form method="GET" action="{{ url()->current() }}" class="mb-5 flex flex-wrap items-center gap-3">
                @foreach(request()->except(['bulan_shift', 'tab']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <input type="hidden" name="tab" value="shift">
                
                <label class="text-xs text-kpi-gray block font-semibold">Filter Bulan:</label>
                <input type="month" name="bulan_shift" value="{{ request('bulan_shift', now()->format('Y-m')) }}" onchange="this.form.submit()" class="input max-w-[180px] cursor-pointer">
            </form>

            <div class="space-y-2">
                @forelse ($riwayatShift ?? [] as $s)
                    <div class="flex items-center justify-between rounded-xl border border-kpi-line px-4 py-3 dark:border-white/10">
                        <div>
                            <p class="text-sm font-semibold">
                                {{ \Carbon\Carbon::parse($s->tanggal)->translatedFormat('d F Y') }}
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                    {{ $s->shift == '1' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200' : '' }}
                                    {{ $s->shift == '2' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200' : '' }}
                                    {{ $s->shift == '3' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-200' : '' }}
                                ">
                                    Shift {{ $s->shift }}
                                </span>
                            </p>
                            @if($s->stasiun_tv)
                                <p class="text-xs text-kpi-gray mt-1">Stasiun TV: <span class="font-medium text-stone-700 dark:text-stone-300">{{ $s->stasiun_tv }}</span></p>
                            @endif
                            @if($s->keterangan)
                                <p class="text-[11px] text-kpi-gray mt-0.5">Catatan: <span class="italic text-stone-600 dark:text-stone-400">{{ $s->keterangan }}</span></p>
                            @endif
                        </div>
                        <div>
                            @if($s->statusShift)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold" style="background-color: {{ $s->statusShift->warna }}; color: #1f2937">
                                    {{ $s->statusShift->nama }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/20 dark:text-emerald-400">
                                    Normal Shift
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-kpi-gray">Tidak ada riwayat jadwal shift untuk bulan/periode terpilih.</p>
                @endforelse
            </div>
        </div>

    </div>
</x-app-layout>
