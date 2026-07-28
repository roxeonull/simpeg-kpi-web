<x-app-layout title="Catat Absensi Manual">
    <form method="POST" action="{{ route('absensi.store') }}" x-data="{ status: '{{ old('status', '') }}' }" class="card max-w-lg space-y-5">
        @csrf
        @php
            $pegawaiOptions = [['value' => '', 'label' => '— Pilih Pegawai —']];
            foreach ($pegawais as $p) {
                $pegawaiOptions[] = ['value' => (string)$p->id, 'label' => $p->nama . ' (' . $p->nip . ')'];
            }
        @endphp
        <div>
            <p class="eyebrow">Jalur Cadangan</p>
            <h2 class="mt-1 font-serif text-lg font-semibold">Catat Absensi Manual</h2>
        </div>

        <div>
            <label class="label">Pegawai</label>
            <x-select-search name="pegawai_id" :value="old('pegawai_id') ?? ''" :options="$pegawaiOptions" class="w-full" />
        </div>
        <div>
            <label class="label">Tanggal</label>
            <input type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" required class="input">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">Jam Masuk</label>
                <input type="time" name="jam_masuk" value="{{ old('jam_masuk') }}" class="input">
            </div>
            <div>
                <label class="label">Jam Keluar</label>
                <input type="time" name="jam_keluar" value="{{ old('jam_keluar') }}" class="input">
            </div>
        </div>
        <div @change="status = $event.target.value">
            <label class="label">Status</label>
            <x-select name="status" :value="old('status') ?? ''" :options="[
                ['value' => 'hadir', 'label' => 'Hadir'],
                ['value' => 'telat', 'label' => 'Telat'],
                ['value' => 'izin', 'label' => 'Izin'],
                ['value' => 'sakit', 'label' => 'Sakit'],
                ['value' => 'alpa', 'label' => 'Alpa']
            ]" class="w-full" />
        </div>
        <div x-show="status === 'izin' || status === 'sakit'" x-cloak
             x-effect="
                const sel = $el.querySelector('select[name=jenis_ketidakhadiran_id]');
                if (sel) sel.required = (status === 'izin' || status === 'sakit');
             "
             class="space-y-1">
            <label class="label">Jenis Ketidakhadiran <span class="text-kpi-red">*</span></label>
            @php
                $jkOptions = [['value' => '', 'label' => '— Pilih Jenis Ketidakhadiran —']];
                foreach ($jenisKetidakhadirans as $jk) {
                    $jkOptions[] = ['value' => (string)$jk->id, 'label' => $jk->nama];
                }
            @endphp
            <x-select name="jenis_ketidakhadiran_id" :value="old('jenis_ketidakhadiran_id') ?? ''" :options="$jkOptions" class="w-full" />
            <p class="field-hint">Wajib diisi untuk status Izin atau Sakit.</p>
        </div>

        <div>
            <label class="label">Keterangan</label>
            <textarea name="keterangan" rows="2" class="input">{{ old('keterangan') }}</textarea>
        </div>
        <p class="field-hint rounded-lg bg-kpi-cream px-3 py-2.5 dark:bg-white/5">Gunakan ini sebagai jalur cadangan bila absensi mobile (GPS/selfie) tidak dapat digunakan.</p>
        <div class="flex gap-3 border-t border-kpi-line pt-5 dark:border-white/10">
            <button type="submit" class="btn-primary">Simpan</button>
            <a href="{{ route('absensi.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</x-app-layout>
