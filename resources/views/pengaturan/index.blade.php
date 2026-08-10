<x-app-layout title="Pengaturan">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Kolom Kiri -->
        <div class="space-y-6">

            {{-- ═══════════════════════════════════════════════════════ --}}
            {{-- CARD: GPS & Lokasi Kantor                               --}}
            {{-- ═══════════════════════════════════════════════════════ --}}
            <div class="card">
                <p class="eyebrow">Konfigurasi</p>
                <h3 class="mt-1 mb-5 font-serif text-lg font-semibold">GPS &amp; Lokasi Kantor</h3>
                <form method="POST" action="{{ route('pengaturan.update') }}" class="space-y-4">
                    @csrf @method('PUT')
                    {{-- ── Semua field pengaturan dikirim dalam SATU form besar ── --}}
                    {{-- GPS --}}
                    <div>
                        <label class="label">Radius Toleransi GPS Absensi (meter)</label>
                        <input type="number" name="radius_gps" value="{{ $radiusGps }}" min="10" class="input">
                    </div>
                    <div>
                        <label class="label">Koordinat Kantor</label>
                        <p class="text-[11px] text-kpi-gray mb-1.5">Salin dari Google Maps. Format: latitude, longitude</p>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[11px] text-kpi-gray block mb-1">Latitude</label>
                                <input type="text" name="kantor_lat" value="{{ $kantorLat }}" placeholder="Contoh: -6.167055" class="input font-mono text-sm">
                            </div>
                            <div>
                                <label class="text-[11px] text-kpi-gray block mb-1">Longitude</label>
                                <input type="text" name="kantor_lng" value="{{ $kantorLng }}" placeholder="Contoh: 106.822400" class="input font-mono text-sm">
                            </div>
                        </div>
                    </div>

                    {{-- ── Jam Kerja Normal ── --}}
                    <div class="border-t border-kpi-line dark:border-white/10 pt-5 mt-5">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="h-4 w-4 text-kpi-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <h4 class="font-semibold text-sm text-kpi-black dark:text-stone-100">Jam Kerja Normal (Non-Shift)</h4>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="label">Jendela Buka Absen (paling awal)</label>
                                <input type="time" id="jam_awal_absen" name="jam_awal_absen" value="{{ $jamAwalAbsen }}" class="input font-mono">
                                <p class="text-[11px] text-kpi-gray mt-1">Pegawai tidak bisa presensi sebelum jam ini</p>
                            </div>
                            <div>
                                <label class="label">Jam Masuk Standar (batas hadir)</label>
                                <input type="time" id="jam_masuk_standar" name="jam_masuk_standar" value="{{ $jamMasukStandar }}" class="input font-mono">
                                <p class="text-[11px] text-kpi-gray mt-1">Presensi setelah jam ini = Telat</p>
                            </div>
                            <div>
                                <label class="label">Jam Batas Telat (setelahnya = Alpa)</label>
                                <input type="time" id="jam_batas_telat" name="jam_batas_telat" value="{{ $jamBatasTelat }}" class="input font-mono">
                                <p class="text-[11px] text-kpi-gray mt-1">Presensi masuk setelah jam ini tidak diterima</p>
                            </div>
                            <div>
                                <label class="label">Jam Batas Alpa (penanda otomatis)</label>
                                <input type="time" id="jam_batas_alpa" name="jam_batas_alpa" value="{{ $jamBatasAlpa }}" class="input font-mono">
                                <p class="text-[11px] text-kpi-gray mt-1">Jika belum presensi masuk sampai jam ini → Alpa</p>
                            </div>
                            <div>
                                <label class="label">Jam Pulang Standar</label>
                                <input type="time" id="jam_pulang_standar" name="jam_pulang_standar" value="{{ $jamPulangStandar }}" class="input font-mono">
                            </div>
                            <div>
                                <label class="label">Jam Pulang Minimal (Flexible)</label>
                                <input type="time" id="jam_pulang_minimal_flexibel" name="jam_pulang_minimal_flexibel" value="{{ $jamPulangMinimalFleks }}" class="input font-mono">
                                <p class="text-[11px] text-kpi-gray mt-1">Batas paling awal boleh pulang (walau masuk sangat awal)</p>
                            </div>
                        </div>

                        {{-- Toggle Flexible Work Hours --}}
                        <div class="mt-4 rounded-xl border border-kpi-line dark:border-white/10 p-4 bg-stone-50/50 dark:bg-white/[0.02]">
                            <label class="flex items-start gap-3 cursor-pointer" for="flexible_work_hours_enabled">
                                <div class="mt-0.5 relative">
                                    <input type="hidden" name="flexible_work_hours_enabled" value="0">
                                    <input type="checkbox" id="flexible_work_hours_enabled" name="flexible_work_hours_enabled"
                                        value="1" {{ $flexibleWorkHoursEnabled ? 'checked' : '' }}
                                        class="sr-only peer">
                                    <div class="w-10 h-5 bg-stone-300 dark:bg-white/20 rounded-full peer-checked:bg-kpi-red transition-colors duration-200"></div>
                                    <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-kpi-black dark:text-stone-100">Flexible Work Hours</p>
                                    <p class="text-[11px] text-kpi-gray mt-0.5 leading-relaxed">
                                        Jika aktif: pegawai yang masuk lebih awal dari jam standar boleh pulang lebih awal
                                        (maks selisih 30 menit / sesuai "Jam Pulang Minimal" di atas).
                                        Jika nonaktif: semua pegawai wajib pulang tepat jam standar.
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- ── Mode WFH & Presensi Domisili ── --}}
                    <div class="border-t border-kpi-line dark:border-white/10 pt-5 mt-5">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <h4 class="font-semibold text-sm text-kpi-black dark:text-stone-100">Sistem Work From Home (WFH) &amp; Domisili</h4>
                        </div>

                        {{-- Toggle WFH System --}}
                        <div class="rounded-xl border border-kpi-line dark:border-white/10 p-4 bg-stone-50/50 dark:bg-white/[0.02] mb-4">
                            <label class="flex items-start gap-3 cursor-pointer" for="wfh_enabled">
                                <div class="mt-0.5 relative">
                                    <input type="hidden" name="wfh_enabled" value="0">
                                    <input type="checkbox" id="wfh_enabled" name="wfh_enabled"
                                        value="1" {{ $wfhEnabled ? 'checked' : '' }}
                                        class="sr-only peer">
                                    <div class="w-10 h-5 bg-stone-300 dark:bg-white/20 rounded-full peer-checked:bg-emerald-600 transition-colors duration-200"></div>
                                    <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-kpi-black dark:text-stone-100">Aktifkan Mode WFH Berbasis Domisili</p>
                                    <p class="text-[11px] text-kpi-gray mt-0.5 leading-relaxed">
                                        Jika aktif: pada hari-hari WFH yang dipilih di bawah, acuan radius GPS absensi pegawai otomatis berpindah ke titik koordinat domisili pegawai yang telah disetujui.
                                    </p>
                                </div>
                            </label>
                        </div>

                        {{-- Simple & Clean Checkbox Selector for WFH Days --}}
                        <div>
                            <label class="label mb-2">Pilih Hari Pelaksanaan WFH</label>
                            @php
                                $daysList = [
                                    'monday' => 'Senin',
                                    'tuesday' => 'Selasa',
                                    'wednesday' => 'Rabu',
                                    'thursday' => 'Kamis',
                                    'friday' => 'Jumat',
                                    'saturday' => 'Sabtu',
                                    'sunday' => 'Minggu',
                                ];
                            @endphp
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @foreach($daysList as $dayKey => $dayName)
                                    @php $isChecked = in_array($dayKey, $wfhDays); @endphp
                                    <label class="flex items-center gap-3 rounded-2xl border border-kpi-line bg-white dark:bg-kpi-dark-surface dark:border-white/10 p-3.5 cursor-pointer hover:bg-stone-50 dark:hover:bg-white/5 transition-all">
                                        <input type="checkbox" name="wfh_days[]" value="{{ $dayKey }}" {{ $isChecked ? 'checked' : '' }} class="h-4.5 w-4.5 rounded border-stone-300 text-kpi-red focus:ring-kpi-red cursor-pointer shrink-0">
                                        <span class="text-xs font-bold text-kpi-black dark:text-stone-100">{{ $dayName }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- ── Jam Kerja Shift ── --}}
                    <div class="border-t border-kpi-line dark:border-white/10 pt-5 mt-1">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="h-4 w-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <h4 class="font-semibold text-sm text-kpi-black dark:text-stone-100">Toleransi Jam Kerja Shift</h4>
                        </div>
                        <p class="text-[11px] text-kpi-gray mb-3 leading-relaxed">
                            Berlaku untuk <strong>semua shift</strong> (1/2/3). Jam mulai shift sudah ditetapkan (Shift 1 = 06:00, Shift 2 = 14:00, Shift 3 = 22:00).
                        </p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="label">Toleransi Awal Shift (menit)</label>
                                <input type="number" id="toleransi_awal_shift_menit" name="toleransi_awal_shift_menit"
                                    value="{{ $toleransiAwalShiftMenit }}" min="0" max="180" class="input">
                                <p class="text-[11px] text-kpi-gray mt-1">Berapa menit sebelum jam mulai shift boleh presensi masuk</p>
                            </div>
                            <div>
                                <label class="label">Toleransi Telat Shift (menit)</label>
                                <input type="number" id="toleransi_telat_shift_menit" name="toleransi_telat_shift_menit"
                                    value="{{ $toleransiTelatShiftMenit }}" min="0" max="120" class="input">
                                <p class="text-[11px] text-kpi-gray mt-1">Batas toleransi masuk masih dihitung Hadir (setelahnya = Telat)</p>
                            </div>
                        </div>
                    </div>

                    {{-- ── Cuti & Diklat ── --}}
                    <div class="border-t border-kpi-line dark:border-white/10 pt-5 mt-1">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <h4 class="font-semibold text-sm text-kpi-black dark:text-stone-100">Cuti &amp; Diklat</h4>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="label">Kuota Cuti Tahunan Default (hari)</label>
                                <input type="number" name="kuota_cuti_tahunan" value="{{ $kuotaCuti }}" min="1" class="input">
                            </div>
                            <div>
                                <label class="label">Target JP Diklat per Tahun</label>
                                <input type="number" name="target_jp_tahunan" value="{{ $targetJp }}" min="1" class="input">
                            </div>
                        </div>
                    </div>

                    <button class="btn-primary">Simpan Semua Pengaturan</button>
                </form>
            </div>

            <div class="card">
                <p class="eyebrow">Master Data</p>
                <h3 class="mt-1 mb-4 font-serif text-lg font-semibold">Unit Kerja</h3>
                <form method="POST" action="{{ route('pengaturan.unit.store') }}" class="mb-4 flex gap-2">
                    @csrf
                    <input type="text" name="nama_unit" placeholder="Nama unit kerja" required class="input">
                    <input type="text" name="kode_unit" placeholder="Kode" class="input max-w-[90px]">
                    <button class="btn-primary shrink-0 !px-3">+</button>
                </form>
                <ul class="divide-y divide-kpi-line dark:divide-white/5 max-h-48 overflow-y-auto pr-3 sm:pr-4">
                    @forelse ($units as $u)
                        <li class="flex items-center justify-between gap-2 py-2.5 text-sm">
                            <span class="truncate">{{ $u->nama_unit }} @if($u->kode_unit)<span class="mono text-xs text-kpi-gray">({{ $u->kode_unit }})</span>@endif</span>
                            <form method="POST" action="{{ route('pengaturan.unit.destroy', $u) }}" onsubmit="return confirm('Hapus unit ini?')" class="shrink-0 ml-2">
                                @csrf @method('DELETE')
                                <button class="btn-xs-danger">Hapus</button>
                            </form>
                        </li>
                    @empty
                        <li class="py-2.5 text-sm text-kpi-gray">Belum ada unit kerja.</li>
                    @endforelse
                </ul>
            </div>

            <div class="card">
                <p class="eyebrow">Master Data</p>
                <h3 class="mt-1 mb-4 font-serif text-lg font-semibold">Jabatan</h3>
                <form method="POST" action="{{ route('pengaturan.jabatan.store') }}" class="mb-4 flex gap-2">
                    @csrf
                    <input type="text" name="nama_jabatan" placeholder="Nama jabatan" required class="input">
                    <button class="btn-primary shrink-0 !px-3">+</button>
                </form>
                <ul class="divide-y divide-kpi-line dark:divide-white/5 max-h-48 overflow-y-auto pr-3 sm:pr-4">
                    @forelse ($jabatans as $j)
                        <li class="flex items-center justify-between gap-2 py-2.5 text-sm">
                            <span class="truncate">{{ $j->nama_jabatan }}</span>
                            <form method="POST" action="{{ route('pengaturan.jabatan.destroy', $j) }}" onsubmit="return confirm('Hapus jabatan ini?')" class="shrink-0 ml-2">
                                @csrf @method('DELETE')
                                <button class="btn-xs-danger">Hapus</button>
                            </form>
                        </li>
                    @empty
                        <li class="py-2.5 text-sm text-kpi-gray">Belum ada jabatan.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Jenis Cuti -->
            <div class="card">
                <p class="eyebrow">Master Data Cuti</p>
                <h3 class="mt-1 mb-4 font-serif text-lg font-semibold">Jenis Cuti & Izin</h3>
                <form method="POST" action="{{ route('pengaturan.jenis-cuti.store') }}" class="mb-4 space-y-3">
                    @csrf
                    <div class="flex gap-2">
                        <input type="text" name="nama" placeholder="Jenis cuti baru" required class="input">
                        <button class="btn-primary shrink-0 !px-3">+</button>
                    </div>
                    <label class="flex items-center gap-2 text-xs font-semibold text-kpi-gray">
                        <input type="checkbox" name="potong_saldo_cuti" value="1" class="rounded border-stone-300 text-kpi-red focus:ring-kpi-red/20">
                        Memotong Saldo Cuti Tahunan
                    </label>
                </form>
                <ul class="divide-y divide-kpi-line dark:divide-white/5 max-h-48 overflow-y-auto pr-3 sm:pr-4">
                    @forelse ($jenisCutis as $jc)
                        <li class="flex items-center justify-between gap-2 py-2.5 text-sm">
                            <div class="flex flex-col min-w-0 flex-1">
                                <span class="font-medium text-stone-700 dark:text-stone-300 truncate">{{ $jc->nama }}</span>
                                @if($jc->potong_saldo_cuti)
                                    <span class="text-[10px] font-semibold uppercase tracking-wider text-rose-600 dark:text-rose-400">Memotong Saldo Cuti</span>
                                @else
                                    <span class="text-[10px] font-medium text-kpi-gray">Tidak Memotong Saldo</span>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('pengaturan.jenis-cuti.destroy', $jc) }}" onsubmit="return confirm('Hapus jenis cuti ini?')" class="shrink-0 ml-2">
                                @csrf @method('DELETE')
                                <button class="btn-xs-danger">Hapus</button>
                            </form>
                        </li>
                    @empty
                        <li class="py-2.5 text-sm text-kpi-gray">Belum ada jenis cuti.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Kolom Kanan (Master Data Pelatihan SIMPATIK) -->
        <div class="space-y-6">
            <!-- Bentuk Pelatihan -->
            <div class="card">
                <p class="eyebrow">Master Data Diklat</p>
                <h3 class="mt-1 mb-4 font-serif text-lg font-semibold">Bentuk Pelatihan</h3>
                <form method="POST" action="{{ route('pengaturan.bentuk-pelatihan.store') }}" class="mb-4 flex gap-2">
                    @csrf
                    <input type="text" name="nama_bentuk" placeholder="Bentuk pelatihan baru" required class="input">
                    <button class="btn-primary shrink-0 !px-3">+</button>
                </form>
                <ul class="divide-y divide-kpi-line dark:divide-white/5 max-h-40 overflow-y-auto pr-3 sm:pr-4">
                    @forelse ($bentukPelatihans as $b)
                        <li class="flex items-center justify-between gap-2 py-2 text-sm">
                            <span class="truncate">{{ $b->nama_bentuk }}</span>
                            <form method="POST" action="{{ route('pengaturan.bentuk-pelatihan.destroy', $b) }}" onsubmit="return confirm('Hapus bentuk pelatihan ini?')" class="shrink-0 ml-2">
                                @csrf @method('DELETE')
                                <button class="btn-xs-danger">Hapus</button>
                            </form>
                        </li>
                    @empty
                        <li class="py-2 text-sm text-kpi-gray">Belum ada data bentuk pelatihan.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Tipe Kursus -->
            <div class="card">
                <p class="eyebrow">Master Data Diklat</p>
                <h3 class="mt-1 mb-4 font-serif text-lg font-semibold">Tipe Kursus</h3>
                <form method="POST" action="{{ route('pengaturan.tipe-kursus.store') }}" class="mb-4 space-y-2">
                    @csrf
                    @php
                        $bpOptions = [['value' => '', 'label' => '— Pilih Bentuk —']];
                        foreach ($bentukPelatihans as $b) {
                            $bpOptions[] = ['value' => (string)$b->id, 'label' => $b->nama_bentuk];
                        }
                    @endphp
                    <div class="flex gap-2 items-center">
                        <div class="w-1/2">
                            <x-select name="bentuk_pelatihan_id" value="" :options="$bpOptions" class="w-full text-xs" />
                        </div>
                        <input type="text" name="nama_tipe" placeholder="Tipe kursus baru" required class="input w-1/2">
                        <button class="btn-primary shrink-0 !px-3">+</button>
                    </div>
                </form>
                <ul class="divide-y divide-kpi-line dark:divide-white/5 max-h-40 overflow-y-auto pr-3 sm:pr-4">
                    @forelse ($tipeKursuses as $tk)
                        <li class="flex items-center justify-between gap-2 py-2 text-sm">
                            <div class="min-w-0 flex-1">
                                <span class="truncate block">{{ $tk->nama_tipe }}</span>
                                <p class="text-xs text-kpi-gray font-semibold truncate">{{ $tk->bentukPelatihan?->nama_bentuk }}</p>
                            </div>
                            <form method="POST" action="{{ route('pengaturan.tipe-kursus.destroy', $tk) }}" onsubmit="return confirm('Hapus tipe kursus ini?')" class="shrink-0 ml-2">
                                @csrf @method('DELETE')
                                <button class="btn-xs-danger">Hapus</button>
                            </form>
                        </li>
                    @empty
                        <li class="py-2 text-sm text-kpi-gray">Belum ada data tipe kursus.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Jenis Kursus -->
            <div class="card">
                <p class="eyebrow">Master Data Diklat</p>
                <h3 class="mt-1 mb-4 font-serif text-lg font-semibold">Jenis Kursus</h3>
                <form method="POST" action="{{ route('pengaturan.jenis-kursus.store') }}" class="mb-4 flex gap-2">
                    @csrf
                    <input type="text" name="nama_jenis" placeholder="Jenis kursus baru" required class="input">
                    <button class="btn-primary shrink-0 !px-3">+</button>
                </form>
                <ul class="divide-y divide-kpi-line dark:divide-white/5 max-h-40 overflow-y-auto pr-3 sm:pr-4">
                    @forelse ($jenisKursuses as $jk)
                        <li class="flex items-center justify-between gap-2 py-2 text-sm">
                            <span class="truncate">{{ $jk->nama_jenis }}</span>
                            <form method="POST" action="{{ route('pengaturan.jenis-kursus.destroy', $jk) }}" onsubmit="return confirm('Hapus jenis kursus ini?')" class="shrink-0 ml-2">
                                @csrf @method('DELETE')
                                <button class="btn-xs-danger">Hapus</button>
                            </form>
                        </li>
                    @empty
                        <li class="py-2 text-sm text-kpi-gray">Belum ada data jenis kursus.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Instansi -->
            <div class="card">
                <p class="eyebrow">Master Data Diklat</p>
                <h3 class="mt-1 mb-4 font-serif text-lg font-semibold">Daftar Instansi Penyelenggara</h3>
                <form method="POST" action="{{ route('pengaturan.instansi.store') }}" class="mb-4 flex gap-2">
                    @csrf
                    <input type="text" name="nama_instansi" placeholder="Nama instansi baru" required class="input">
                    <button class="btn-primary shrink-0 !px-3">+</button>
                </form>
                <ul class="divide-y divide-kpi-line dark:divide-white/5 max-h-40 overflow-y-auto pr-3 sm:pr-4">
                    @forelse ($instansis as $in)
                        <li class="flex items-center justify-between gap-2 py-2 text-sm">
                            <span class="truncate">{{ $in->nama_instansi }}</span>
                            <form method="POST" action="{{ route('pengaturan.instansi.destroy', $in) }}" onsubmit="return confirm('Hapus instansi ini?')" class="shrink-0 ml-2">
                                @csrf @method('DELETE')
                                <button class="btn-xs-danger">Hapus</button>
                            </form>
                        </li>
                    @empty
                        <li class="py-2 text-sm text-kpi-gray">Belum ada data instansi.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Jenis Ketidakhadiran -->
            <div class="card">
                <p class="eyebrow">Master Data Absensi</p>
                <h3 class="mt-1 mb-4 font-serif text-lg font-semibold">Jenis Ketidakhadiran</h3>
                <form method="POST" action="{{ route('pengaturan.jenis-ketidakhadiran.store') }}" class="mb-4 flex gap-2">
                    @csrf
                    <input type="text" name="nama" placeholder="Jenis ketidakhadiran baru" required class="input">
                    <button class="btn-primary shrink-0 !px-3">+</button>
                </form>
                <ul class="divide-y divide-kpi-line dark:divide-white/5 max-h-48 overflow-y-auto pr-3 sm:pr-4">
                    @forelse ($jenisKetidakhadirans as $jk)
                        <li class="flex items-center justify-between gap-2 py-2 text-sm">
                            <span class="truncate">{{ $jk->nama }}</span>
                            <form method="POST" action="{{ route('pengaturan.jenis-ketidakhadiran.destroy', $jk) }}" onsubmit="return confirm('Hapus jenis ketidakhadiran ini?')" class="shrink-0 ml-2">
                                @csrf @method('DELETE')
                                <button class="btn-xs-danger">Hapus</button>
                            </form>
                        </li>
                    @empty
                        <li class="py-2 text-sm text-kpi-gray">Belum ada jenis ketidakhadiran.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Status Shift -->
            <div class="card">
                <p class="eyebrow">Master Data Shift</p>
                <h3 class="mt-1 mb-4 font-serif text-lg font-semibold">Status Jadwal Shift</h3>
                <form method="POST" action="{{ route('pengaturan.status-shift.store') }}" class="mb-4 flex flex-col gap-2">
                    @csrf
                    <div class="flex gap-2">
                        <input type="text" name="kode" placeholder="Kode (misal: CB)" required class="input max-w-[120px] uppercase">
                        <input type="text" name="nama" placeholder="Nama Status (Cuti Bersama)" required class="input">
                    </div>
                    <div class="flex gap-2 items-center">
                        <label class="text-xs text-kpi-gray">Warna Label:</label>
                        <input type="color" name="warna" value="#e5e7eb" class="h-9 w-12 rounded border border-kpi-line bg-transparent p-0.5 cursor-pointer">
                        <button class="btn-primary shrink-0 ml-auto !px-3">+</button>
                    </div>
                </form>
                <ul class="divide-y divide-kpi-line dark:divide-white/5 max-h-48 overflow-y-auto pr-3 sm:pr-4">
                    @forelse ($statusShifts as $ss)
                        <li class="flex items-center justify-between gap-2 py-2 text-sm">
                            <div class="flex items-center gap-2 min-w-0 flex-1">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold shrink-0" style="background-color: {{ $ss->warna }}; color: #1f2937">
                                    {{ $ss->kode }}
                                </span>
                                <span class="truncate">{{ $ss->nama }}</span>
                            </div>
                            <form method="POST" action="{{ route('pengaturan.status-shift.destroy', $ss) }}" onsubmit="return confirm('Hapus status shift ini?')" class="shrink-0 ml-2">
                                @csrf @method('DELETE')
                                <button class="btn-xs-danger">Hapus</button>
                            </form>
                        </li>
                    @empty
                        <li class="py-2 text-sm text-kpi-gray">Belum ada status shift.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
