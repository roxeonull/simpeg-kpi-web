<div class="space-y-2">
    @forelse ($pegawai->riwayatPelatihan as $p)
        <div onclick="if (!event.target.closest('a, button, form')) window.location='{{ route('pelatihan.show', $p) }}'" 
             class="flex items-center justify-between rounded-xl border border-kpi-line px-4 py-3 dark:border-white/10 hover:bg-kpi-cream/40 transition-colors cursor-pointer">
            <div>
                <p class="font-medium text-kpi-black dark:text-stone-100">{{ $p->nama_pelatihan }}</p>
                <p class="text-sm text-kpi-gray">
                    {{ $p->bentukPelatihan?->nama_bentuk ?? '—' }} &middot; {{ $p->tipeKursus?->nama_tipe ?? '—' }} &middot;
                    {{ $p->penyelenggara }} &middot;
                    {{ $p->tanggal->format('d M Y') }}{{ $p->tanggal_akhir ? ' s.d. ' . $p->tanggal_akhir->format('d M Y') : '' }} &middot;
                    {{ $p->durasi_jp }} JP
                </p>
            </div>
            <div class="flex items-center gap-3">
                <x-badge :color="$p->status_verifikasi === 'terverifikasi' ? 'success' : ($p->status_verifikasi === 'ditolak' ? 'danger' : 'warning')">
                    {{ ucfirst($p->status_verifikasi) }}
                </x-badge>
                
                @if(auth()->user()->role === 'admin' && isset($showActions) && $showActions)
                    <div class="flex items-center gap-1.5">
                        @if($p->status_verifikasi === 'menunggu')
                            <form method="POST" action="{{ route('pelatihan.verifikasi', $p) }}" class="inline">
                                @csrf @method('PATCH')
                                <input type="hidden" name="keputusan" value="terverifikasi">
                                <button type="submit" class="btn-xs-success">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Verifikasi
                                </button>
                            </form>
                            <button type="button" @click="rejectActionUrl = '{{ route('pelatihan.verifikasi', $p) }}'; showRejectModal = true" class="btn-xs-danger">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Tolak
                            </button>
                        @endif
                        <form method="POST" action="{{ route('pelatihan.destroy', $p) }}" class="inline" onsubmit="return confirm('Hapus data ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-xs-danger">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <p class="text-sm text-kpi-gray">Belum ada riwayat pelatihan.</p>
    @endforelse
</div>
