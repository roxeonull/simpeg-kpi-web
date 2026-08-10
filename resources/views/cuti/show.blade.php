<x-app-layout title="Detail Pengajuan Cuti">
    <div x-data="{ openRejectModal: false, catatan: '', submitting: false }">
        <a href="{{ route('cuti.index') }}" class="mb-4 inline-flex items-center gap-1.5 text-sm font-medium text-kpi-gray hover:text-kpi-red">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke daftar
        </a>

        <div class="max-w-2xl space-y-5">
            <div class="card">
                <div class="mb-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-kpi-gold-soft text-sm font-semibold text-kpi-red-dark dark:bg-kpi-gold/20 dark:text-kpi-gold">
                            {{ strtoupper(substr($cuti->pegawai->nama, 0, 2)) }}
                        </div>
                        <div>
                            <h2 class="text-lg font-bold leading-tight">{{ $cuti->pegawai->nama }}</h2>
                            <p class="mono text-xs text-kpi-gray">{{ $cuti->pegawai->nip }} &middot; {{ $cuti->pegawai->unit?->nama_unit }}</p>
                        </div>
                    </div>
                    <x-badge :color="$cuti->status === 'disetujui' ? 'success' : ($cuti->status === 'ditolak' ? 'danger' : 'warning')">{{ $cuti->statusLabel() }}</x-badge>
                </div>

                <dl class="grid grid-cols-2 gap-5 border-t border-kpi-line pt-5 text-sm dark:border-white/10">
                    <div><dt class="eyebrow !normal-case !tracking-normal">Jenis Cuti</dt><dd class="mt-1 font-medium capitalize">{{ $cuti->jenis_cuti }}</dd></div>
                    <div><dt class="eyebrow !normal-case !tracking-normal">Jumlah Hari</dt><dd class="mt-1 font-medium">{{ $cuti->jumlah_hari }} hari</dd></div>
                    <div><dt class="eyebrow !normal-case !tracking-normal">Tanggal Mulai</dt><dd class="mono mt-1 font-medium">{{ $cuti->tanggal_mulai->format('d M Y') }}</dd></div>
                    <div><dt class="eyebrow !normal-case !tracking-normal">Tanggal Selesai</dt><dd class="mono mt-1 font-medium">{{ $cuti->tanggal_selesai->format('d M Y') }}</dd></div>
                    <div class="col-span-2"><dt class="eyebrow !normal-case !tracking-normal">Alasan</dt><dd class="mt-1 font-medium">{{ $cuti->alasan ?: '—' }}</dd></div>
                    <div class="col-span-2"><dt class="eyebrow !normal-case !tracking-normal">Alamat Selama Cuti</dt><dd class="mt-1 font-medium">{{ $cuti->alamat_cuti ?: '—' }}</dd></div>
                    <div class="col-span-2">
                        <dt class="eyebrow !normal-case !tracking-normal">Bukti / Lampiran</dt>
                        <dd class="mt-1 font-medium">
                            @if ($cuti->lampiran)
                                <a href="{{ asset('storage/' . $cuti->lampiran) }}" target="_blank" class="inline-flex items-center gap-1.5 text-sm font-semibold text-kpi-red hover:underline">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Lihat Lampiran Bukti
                                </a>
                            @else
                                <span class="text-kpi-gray">Tidak ada lampiran bukti</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Approval trail --}}
            <div class="card">
                <h3 class="mb-5 font-serif text-base font-semibold">Alur Persetujuan</h3>

                @php
                    $activeStep = $cuti->activeStep();
                    $canApprove = $cuti->canUserApproveActiveStep(auth()->user());
                @endphp

                <div class="space-y-6">
                    @foreach ($cuti->approvalSteps as $step)
                        @php
                            $isCurrentActive = $activeStep && $activeStep->id === $step->id;
                            $circleBg = match($step->status) {
                                'disetujui' => 'bg-emerald-500 text-white',
                                'ditolak' => 'bg-rose-500 text-white',
                                default => ($isCurrentActive ? 'bg-kpi-gold text-white shadow-md' : 'bg-stone-100 text-kpi-gray dark:bg-white/10'),
                            };
                        @endphp
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold {{ $circleBg }}">
                                    @if($step->status === 'disetujui')
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    @elseif($step->status === 'ditolak')
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                    @else
                                        {{ $step->urutan }}
                                    @endif
                                </div>
                                @if (!$loop->last)
                                    <div class="mt-1 h-full w-px flex-1 bg-kpi-line dark:bg-white/10"></div>
                                @endif
                            </div>
                            <div class="flex-1 pb-2">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p class="text-sm font-semibold">Step {{ $step->urutan }}: Persetujuan {{ $step->tipeStepLabel() }}</p>
                                    <x-badge :color="$step->status === 'disetujui' ? 'success' : ($step->status === 'ditolak' ? 'danger' : 'warning')">{{ ucfirst($step->status) }}</x-badge>
                                </div>
                                
                                @if ($step->pemrosesUser)
                                    <p class="mt-0.5 text-xs text-kpi-gray">
                                        Diproses oleh <strong class="text-stone-700 dark:text-stone-300">{{ $step->pemrosesUser->name }}</strong>
                                        @if ($step->diproses_pada)
                                            pada {{ $step->diproses_pada->format('d M Y H:i') }}
                                        @endif
                                    </p>
                                @endif

                                @if ($step->catatan)
                                    <p class="mt-2 rounded-lg bg-kpi-cream px-3 py-2 text-sm text-kpi-gray dark:bg-white/5">{{ $step->catatan }}</p>
                                @endif

                                @if ($isCurrentActive && $canApprove)
                                    @if ($step->tipe_step === 'atasan_langsung' && auth()->user()->role === 'admin')
                                        <p class="mt-2 text-[11px] text-amber-700 dark:text-amber-400 font-medium flex items-center gap-1">
                                            <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Diproses sebagai <strong>Superadmin Override</strong> (mewakili Atasan Langsung).
                                        </p>
                                    @endif
                                    <div class="mt-2 flex flex-wrap gap-3" x-data="{ submittingApprove: false }">
                                        <form method="POST" action="{{ route('cuti.approve-step', $cuti) }}" @submit="submittingApprove = true">
                                            @csrf @method('PATCH')
                                            <button class="btn-primary flex items-center gap-2" :disabled="submittingApprove">
                                                <template x-if="submittingApprove">
                                                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                </template>
                                                <span x-text="submittingApprove ? 'Memproses...' : 'Setujui Tahap Ini'">Setujui Tahap Ini</span>
                                            </button>
                                        </form>
                                        <button type="button" @click="openRejectModal = true" class="btn-danger">Tolak Tahap Ini</button>
                                    </div>
                                @elseif ($step->status === 'menunggu' && !$isCurrentActive)
                                    <p class="mt-1 text-xs text-kpi-gray">Menunggu persetujuan tahap sebelumnya.</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Modal Penolakan Modern --}}
        <div x-show="openRejectModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-stone-950/40 backdrop-blur-sm" @click="openRejectModal = false"></div>

            {{-- Dialog Box --}}
            <div class="relative w-full max-w-md rounded-2xl border border-kpi-line bg-white p-6 shadow-2xl dark:border-white/10 dark:bg-kpi-dark-surface"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                
                <div class="flex items-center gap-3 text-kpi-red mb-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-kpi-red-soft text-kpi-red dark:bg-kpi-red/20">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-serif text-lg font-bold text-kpi-black dark:text-stone-100">Penolakan Pengajuan Cuti</h3>
                        <p class="text-xs text-kpi-gray">Berikan alasan jelas mengenai penolakan pengajuan ini</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('cuti.reject-step', $cuti) }}" @submit="submitting = true" class="mt-4 space-y-4">
                    @csrf @method('PATCH')
                    <div>
                        <label class="label">Alasan Penolakan <span class="text-kpi-red">*</span></label>
                        <textarea name="catatan" x-model="catatan" required rows="3" 
                                  placeholder="Contoh: Jadwal bertabrakan dengan kegiatan operasional kritis unit..." 
                                  class="input w-full"></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-2.5 pt-2">
                        <button type="button" @click="openRejectModal = false" class="btn-secondary" :disabled="submitting">Batal</button>
                        <button type="submit" class="btn-danger flex items-center gap-2" :disabled="submitting || !catatan.trim()">
                            <template x-if="submitting">
                                <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </template>
                            <span x-text="submitting ? 'Menolak...' : 'Konfirmasi Tolak'">Konfirmasi Tolak</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
