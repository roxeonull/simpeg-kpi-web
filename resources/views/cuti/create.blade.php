<x-app-layout title="Tambah Pengajuan Cuti">
    <form method="POST" action="{{ route('cuti.store') }}" enctype="multipart/form-data"
          x-data="{ 
              tanggalMulai: '{{ old('tanggal_mulai', '') }}', 
              tanggalSelesai: '{{ old('tanggal_selesai', '') }}',
              get jumlahHari() {
                  if (!this.tanggalMulai || !this.tanggalSelesai) return 0;
                  const start = new Date(this.tanggalMulai);
                  const end = new Date(this.tanggalSelesai);
                  if (isNaN(start) || isNaN(end) || end < start) return 0;
                  const diffTime = Math.abs(end - start);
                  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                  return diffDays;
              }
          }" 
          class="card max-w-lg space-y-5">
        @csrf
        
        <div>
            <p class="eyebrow">Pengajuan Baru</p>
            <h2 class="mt-1 font-serif text-lg font-semibold">Form Pengajuan Cuti & Izin</h2>
        </div>

        <div>
            <label class="label">Pegawai *</label>
            @php
                $pegawaiOptions = [['value' => '', 'label' => '— Pilih Pegawai —']];
                foreach ($pegawais as $p) {
                    $pegawaiOptions[] = ['value' => (string)$p->id, 'label' => $p->nama . ' (' . $p->nip . ')'];
                }
            @endphp
            <x-select-search name="pegawai_id" :value="old('pegawai_id') ?? ''" :options="$pegawaiOptions" class="w-full" />
        </div>

        <div>
            <label class="label">Jenis Cuti *</label>
            @php
                $jcOptions = [['value' => '', 'label' => '— Pilih Jenis Cuti —']];
                foreach ($jenisCutis as $jc) {
                    $jcOptions[] = ['value' => (string)$jc->id, 'label' => $jc->nama];
                }
            @endphp
            <x-select name="jenis_cuti_id" :value="old('jenis_cuti_id') ?? ''" :options="$jcOptions" class="w-full" />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">Tanggal Mulai *</label>
                <input type="date" name="tanggal_mulai" x-model="tanggalMulai" required class="input">
            </div>
            <div>
                <label class="label">Tanggal Akhir *</label>
                <input type="date" name="tanggal_selesai" x-model="tanggalSelesai" required class="input">
            </div>
        </div>

        <div>
            <label class="label">Jumlah Hari</label>
            <div class="input bg-stone-50 dark:bg-white/5 py-2.5 font-semibold text-kpi-black dark:text-stone-300">
                <span x-text="jumlahHari">0</span> Hari
            </div>
            <p class="field-hint">Dihitung otomatis dari tanggal mulai dan akhir.</p>
        </div>

        <div>
            <label class="label">Alasan Cuti *</label>
            <textarea name="alasan" rows="3" required placeholder="Tuliskan alasan pengajuan cuti secara lengkap..." class="input">{{ old('alasan') }}</textarea>
        </div>

        <div>
            <label class="label">Alamat Selama Cuti</label>
            <textarea name="alamat_cuti" rows="3" placeholder="Tuliskan alamat lengkap tempat tinggal selama cuti..." class="input">{{ old('alamat_cuti') }}</textarea>
            <p class="field-hint">Opsional. Alamat yang dapat dihubungi selama masa cuti berlangsung.</p>
        </div>


        <div>
            <label class="label">Bukti / Lampiran (PDF/Gambar)</label>
            <input type="file" name="lampiran" class="input">
            <p class="field-hint">Opsional. Unggah berkas bukti pendukung pengajuan (maksimal 2MB).</p>
        </div>

        <div class="flex gap-3 border-t border-kpi-line pt-5 dark:border-white/10">
            <button type="submit" class="btn-primary">Kirim Pengajuan</button>
            <a href="{{ route('cuti.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</x-app-layout>
