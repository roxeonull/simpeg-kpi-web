<x-app-layout title="Detail Pengajuan Cuti">
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
                                <div class="mt-3 flex flex-wrap gap-3">
                                    <form method="POST" action="{{ route('cuti.approve-step', $cuti) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn-primary">Setujui Tahap Ini</button>
                                    </form>
                                    <form method="POST" action="{{ route('cuti.reject-step', $cuti) }}" x-data
                                          @submit.prevent="let c = prompt('Alasan penolakan:'); if(c){ $el.querySelector('[name=catatan]').value = c; $el.submit(); }">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="catatan">
                                        <button type="submit" class="btn-danger">Tolak Tahap Ini</button>
                                    </form>
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
</x-app-layout>
