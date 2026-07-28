<x-app-layout title="Detail Pelatihan Pegawai">
    <a href="{{ route('pelatihan.index', ['tab' => 'rekap']) }}" class="mb-4 inline-flex items-center gap-1.5 text-sm font-medium text-kpi-gray hover:text-kpi-red">
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali ke Rekap Capaian JP
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
    </div>

    <div x-data="{ showRejectModal: false, rejectActionUrl: '' }" class="panel p-5">
        <h3 class="eyebrow mb-4">Daftar Pelatihan</h3>
        
        {{-- Form Tambah Pelatihan --}}
        @include('pelatihan._form-tambah')

        {{-- Daftar Pelatihan --}}
        @include('pelatihan._list', ['showActions' => true])

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
