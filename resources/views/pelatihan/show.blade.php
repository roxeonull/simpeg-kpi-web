<x-app-layout title="Detail Riwayat Pelatihan">
    <a href="{{ $backUrl }}" class="mb-4 inline-flex items-center gap-1.5 text-sm font-medium text-kpi-gray hover:text-kpi-red">
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>

    <div class="max-w-2xl space-y-5">
        <div class="card">
            {{-- Compact Profile Header --}}
            <div class="mb-5 flex flex-wrap items-center justify-between gap-3 pb-5 border-b border-kpi-line dark:border-white/10">
                <div class="flex items-center gap-3">
                    @if($riwayatPelatihan->pegawai->foto)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($riwayatPelatihan->pegawai->foto) }}" alt="{{ $riwayatPelatihan->pegawai->nama }}" class="h-11 w-11 shrink-0 rounded-full object-cover border border-kpi-line dark:border-white/10">
                    @else
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-kpi-gold-soft text-sm font-semibold text-kpi-red-dark dark:bg-kpi-gold/20 dark:text-kpi-gold">
                            {{ strtoupper(substr($riwayatPelatihan->pegawai->nama, 0, 2)) }}
                        </div>
                    @endif
                    <div>
                        <h2 class="text-base font-bold leading-tight">
                            <a href="{{ route('pegawai.show', $riwayatPelatihan->pegawai) }}" class="hover:text-kpi-red hover:underline">
                                {{ $riwayatPelatihan->pegawai->nama }}
                            </a>
                        </h2>
                        <p class="mono text-xs text-kpi-gray">
                            {{ $riwayatPelatihan->pegawai->nip }} &middot; {{ $riwayatPelatihan->pegawai->jabatan?->nama_jabatan ?? '—' }} &middot; {{ $riwayatPelatihan->pegawai->unit?->nama_unit ?? '—' }}
                        </p>
                    </div>
                </div>
                <x-badge :color="$riwayatPelatihan->status_verifikasi === 'terverifikasi' ? 'success' : ($riwayatPelatihan->status_verifikasi === 'ditolak' ? 'danger' : 'warning')">
                    {{ ucfirst($riwayatPelatihan->status_verifikasi) }}
                </x-badge>
            </div>

            {{-- Training details definition list --}}
            <dl class="grid grid-cols-1 gap-5 text-sm sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <dt class="eyebrow !normal-case !tracking-normal">Nama Kursus / Pelatihan</dt>
                    <dd class="mt-1 font-medium text-base text-kpi-black dark:text-stone-50">{{ $riwayatPelatihan->nama_pelatihan }}</dd>
                </div>
                <div>
                    <dt class="eyebrow !normal-case !tracking-normal">Bentuk Pelatihan</dt>
                    <dd class="mt-1 font-medium">{{ $riwayatPelatihan->bentukPelatihan?->nama_bentuk ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="eyebrow !normal-case !tracking-normal">Tipe Kursus</dt>
                    <dd class="mt-1 font-medium">{{ $riwayatPelatihan->tipeKursus?->nama_tipe ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="eyebrow !normal-case !tracking-normal">Jenis Kursus</dt>
                    <dd class="mt-1 font-medium">{{ $riwayatPelatihan->jenisKursus?->nama_jenis ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="eyebrow !normal-case !tracking-normal">Lama Kursus (Jam Pelajaran / JP)</dt>
                    <dd class="mono mt-1 font-medium">{{ $riwayatPelatihan->durasi_jp }} JP</dd>
                </div>
                <div>
                    <dt class="eyebrow !normal-case !tracking-normal">Institusi Penyelenggara</dt>
                    <dd class="mt-1 font-medium">{{ $riwayatPelatihan->penyelenggara }}</dd>
                </div>
                <div>
                    <dt class="eyebrow !normal-case !tracking-normal">Instansi Pengirim</dt>
                    <dd class="mt-1 font-medium">{{ $riwayatPelatihan->instansi?->nama_instansi ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="eyebrow !normal-case !tracking-normal">Tanggal Mulai</dt>
                    <dd class="mono mt-1 font-medium">{{ $riwayatPelatihan->tanggal->translatedFormat('d F Y') }}</dd>
                </div>
                <div>
                    <dt class="eyebrow !normal-case !tracking-normal">Tanggal Akhir</dt>
                    <dd class="mono mt-1 font-medium">{{ $riwayatPelatihan->tanggal_akhir ? $riwayatPelatihan->tanggal_akhir->translatedFormat('d F Y') : '—' }}</dd>
                </div>
                <div>
                    <dt class="eyebrow !normal-case !tracking-normal">Tahun Diklat</dt>
                    <dd class="mono mt-1 font-medium">{{ $riwayatPelatihan->tahun_diklat ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="eyebrow !normal-case !tracking-normal">Bidang SDM SPBE</dt>
                    <dd class="mt-1 font-medium">{{ $riwayatPelatihan->bidang_sdm_spbe ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="eyebrow !normal-case !tracking-normal">Nomor Sertifikat</dt>
                    <dd class="mono mt-1 font-medium">{{ $riwayatPelatihan->no_sertifikat ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="eyebrow !normal-case !tracking-normal">Tanggal Sertifikat</dt>
                    <dd class="mono mt-1 font-medium">{{ $riwayatPelatihan->tanggal_sertifikat ? $riwayatPelatihan->tanggal_sertifikat->translatedFormat('d F Y') : '—' }}</dd>
                </div>

                @if($riwayatPelatihan->status_verifikasi !== 'menunggu')
                <div>
                    <dt class="eyebrow !normal-case !tracking-normal">Diverifikasi Oleh</dt>
                    <dd class="mt-1 font-medium">{{ $riwayatPelatihan->verifikator?->name ?? '—' }}</dd>
                </div>
                @endif
                @if($riwayatPelatihan->catatan)
                <div class="sm:col-span-2">
                    <dt class="eyebrow !normal-case !tracking-normal">Catatan Verifikasi</dt>
                    <dd class="mt-1 rounded-lg bg-kpi-cream px-3 py-2 text-sm text-kpi-gray dark:bg-white/5">
                        {{ $riwayatPelatihan->catatan }}
                    </dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- Certificate preview and download section --}}
        <div class="card">
            <h3 class="mb-4 font-serif text-base font-semibold">Berkas Sertifikat</h3>
            
            @if($riwayatPelatihan->sertifikat)
                @php
                    $extension = strtolower(pathinfo($riwayatPelatihan->sertifikat, PATHINFO_EXTENSION));
                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    $fileUrl = \Illuminate\Support\Facades\Storage::url($riwayatPelatihan->sertifikat);
                @endphp

                @if($isImage)
                    <div class="mb-3 overflow-hidden rounded-xl border border-kpi-line dark:border-white/10 max-w-full bg-stone-50 dark:bg-stone-900/50 p-2">
                        <img src="{{ $fileUrl }}" alt="Sertifikat" class="w-full h-auto rounded-lg object-contain max-h-[450px]">
                    </div>
                    <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center gap-1.5 text-sm font-semibold text-kpi-red hover:underline">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Buka Gambar di Tab Baru
                    </a>
                @else
                    <div class="flex items-center gap-3 rounded-xl border border-kpi-line p-4 dark:border-white/10 max-w-md bg-stone-50/50 dark:bg-white/[0.02]">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-kpi-red-soft text-kpi-red-dark dark:bg-kpi-red/20 dark:text-kpi-red">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">Sertifikat_{{ Str::slug($riwayatPelatihan->nama_pelatihan) }}.{{ $extension }}</p>
                            <p class="text-xs text-kpi-gray uppercase">{{ $extension }} File</p>
                        </div>
                        <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center gap-1 text-sm font-semibold text-kpi-red hover:underline shrink-0">
                            Lihat File
                        </a>
                    </div>
                @endif
            @else
                <div class="flex flex-col items-center justify-center gap-2 rounded-xl border border-dashed border-stone-300 py-8 text-center dark:border-white/15">
                    <svg class="h-8 w-8 text-kpi-gray/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="text-sm text-kpi-gray">Belum ada berkas sertifikat.</p>
                </div>
            @endif
        </div>

        {{-- Verification Actions for Admin --}}
        @if(auth()->user()->role === 'admin' && $riwayatPelatihan->status_verifikasi === 'menunggu')
            <div class="card border-l-4 border-l-kpi-gold bg-kpi-gold-soft/10" x-data="{ showRejectModal: false }">
                <h3 class="font-serif text-base font-semibold">Tindakan Verifikasi</h3>
                <p class="mt-1 text-sm text-kpi-gray">Sebagai admin, silakan verifikasi keabsahan data pelatihan/diklat ini.</p>
                
                <div class="mt-4 flex flex-wrap gap-3">
                    <form method="POST" action="{{ route('pelatihan.verifikasi', $riwayatPelatihan) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="keputusan" value="terverifikasi">
                        <button class="btn-primary">Setujui Verifikasi</button>
                    </form>
                    <button type="button" @click="showRejectModal = true" class="btn-danger">Tolak</button>
                </div>

                {{-- Modal Alasan Penolakan --}}
                <template x-teleport="body">
                    <div x-show="showRejectModal" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                        <div x-show="showRejectModal" x-transition.scale.95 @click.outside="showRejectModal = false" class="w-full max-w-md rounded-2xl border border-kpi-line bg-white p-6 shadow-2xl dark:border-white/10 dark:bg-kpi-dark-surface">
                            <h3 class="font-serif text-lg font-bold text-kpi-black dark:text-stone-50">Tolak Verifikasi Pelatihan</h3>
                            <p class="mt-2 text-sm text-kpi-gray">Silakan masukkan alasan penolakan untuk riwayat pelatihan ini. Alasan ini akan ditampilkan kepada pegawai.</p>
                            
                            <form method="POST" action="{{ route('pelatihan.verifikasi', $riwayatPelatihan) }}" class="mt-4 space-y-4">
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
        @endif
    </div>
</x-app-layout>
