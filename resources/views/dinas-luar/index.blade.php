<x-app-layout title="Absensi & Dinas Luar">
    <style>[x-cloak] { display: none !important; }</style>

    {{-- Tab Navigation --}}
    <div class="mb-6 flex gap-1 overflow-x-auto border-b border-kpi-line dark:border-white/10 no-scrollbar">
        <a href="{{ route('absensi.index') }}"
           class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200">
            Absensi Harian
        </a>
        @if(auth()->user()->role === 'admin')
        <a href="{{ route('absensi.shift.index', 1) }}"
           class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200">
            Jadwal Shift
        </a>
        @endif
        <a href="{{ route('dinas-luar.index') }}"
           class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-semibold transition-colors border-kpi-red text-kpi-red">
            Dinas Luar & WFA
        </a>
    </div>

    <div class="space-y-6">

        {{-- Stat Cards --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="card p-5">
                <p class="eyebrow">Total Pengajuan</p>
                <p class="mt-2 text-2xl font-bold tracking-tight text-kpi-black dark:text-stone-100">{{ $stats['total'] }}</p>
            </div>
            <div class="card p-5 border-amber-200/80 bg-amber-50/40 dark:bg-amber-500/10">
                <p class="eyebrow text-amber-700 dark:text-amber-400">Menunggu Persetujuan</p>
                <p class="mt-2 text-2xl font-bold tracking-tight text-amber-600 dark:text-amber-400">{{ $stats['pending'] }}</p>
            </div>
            <div class="card p-5 border-emerald-200/80 bg-emerald-50/40 dark:bg-emerald-500/10">
                <p class="eyebrow text-emerald-700 dark:text-emerald-400">Disetujui</p>
                <p class="mt-2 text-2xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400">{{ $stats['disetujui'] }}</p>
            </div>
            <div class="card p-5 border-rose-200/80 bg-rose-50/40 dark:bg-rose-500/10">
                <p class="eyebrow text-rose-700 dark:text-rose-400">Ditolak</p>
                <p class="mt-2 text-2xl font-bold tracking-tight text-rose-600 dark:text-rose-400">{{ $stats['ditolak'] }}</p>
            </div>
        </div>

        {{-- Filter & Action Bar --}}
        <div class="card relative z-40 p-4 sm:p-5">
            <form method="GET" action="{{ route('dinas-luar.index') }}" x-data="{ submit() { $el.submit(); } }" @change="submit()" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
                    {{-- Search Input --}}
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ request('search') }}"
                               @input.debounce.400ms="submit()"
                               placeholder="Cari nama pegawai atau NIP..."
                               class="w-full rounded-xl border border-kpi-line bg-white/80 py-2.5 pl-10 pr-4 text-sm focus:border-kpi-red focus:ring-1 focus:ring-kpi-red dark:border-white/10 dark:bg-kpi-dark-surface dark:text-stone-100">
                        <svg class="absolute left-3.5 top-3 h-4 w-4 text-kpi-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                    </div>

                    {{-- Custom Design Status Dropdown --}}
                    <div class="w-full sm:w-52">
                        <x-select name="status" :value="request('status')" :options="[
                            ['value' => '', 'label' => 'Semua Status'],
                            ['value' => 'pending', 'label' => 'Menunggu (Pending)'],
                            ['value' => 'disetujui', 'label' => 'Disetujui'],
                            ['value' => 'ditolak', 'label' => 'Ditolak']
                        ]" class="w-full" />
                    </div>

                    @if(request()->anyFilled(['search', 'status']))
                        <a href="{{ route('dinas-luar.index') }}" class="inline-flex items-center justify-center px-3.5 py-2.5 text-xs font-semibold text-kpi-gray hover:text-kpi-red">
                            Reset Filter
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Data Table --}}
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-kpi-line bg-stone-50/80 dark:border-white/10 dark:bg-white/5">
                        <tr>
                            <th class="px-5 py-3.5 font-semibold text-kpi-gray">Pegawai</th>
                            <th class="px-5 py-3.5 font-semibold text-kpi-gray">Jenis Tugas</th>
                            <th class="px-5 py-3.5 font-semibold text-kpi-gray">Tanggal Pelaksanaan</th>
                            <th class="px-5 py-3.5 font-semibold text-kpi-gray">Lokasi & Uraian</th>
                            <th class="px-5 py-3.5 font-semibold text-kpi-gray">Berkas SPT</th>
                            <th class="px-5 py-3.5 font-semibold text-kpi-gray">Status</th>
                            <th class="px-5 py-3.5 text-right font-semibold text-kpi-gray">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-kpi-line dark:divide-white/10">
                        @forelse($dinasLuars as $dl)
                            <tr class="hover:bg-stone-50/50 dark:hover:bg-white/5 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-kpi-black dark:text-stone-100">{{ $dl->pegawai?->nama ?? '—' }}</div>
                                    <div class="text-xs text-kpi-gray">NIP: {{ $dl->pegawai?->nip ?? '—' }}</div>
                                    <div class="text-[11px] text-stone-400">{{ $dl->pegawai?->unit?->nama }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center rounded-lg bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700 dark:bg-sky-500/10 dark:text-sky-400">
                                        {{ $dl->jenisKetidakhadiran?->nama ?? 'Dinas Luar' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-stone-800 dark:text-stone-200">
                                        {{ \Carbon\Carbon::parse($dl->tanggal_mulai)->format('d M Y') }}
                                    </div>
                                    <div class="text-xs text-kpi-gray">
                                        s/d {{ \Carbon\Carbon::parse($dl->tanggal_selesai)->format('d M Y') }}
                                    </div>
                                </td>
                                <td class="px-5 py-4 max-w-xs">
                                    <div class="font-medium text-kpi-black dark:text-stone-100 truncate" title="{{ $dl->lokasi_tugas }}">
                                        📍 {{ $dl->lokasi_tugas }}
                                    </div>
                                    <div class="text-xs text-kpi-gray line-clamp-2 mt-0.5" title="{{ $dl->alasan }}">
                                        {{ $dl->alasan }}
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    @if($dl->file_spt)
                                        <a href="{{ asset('storage/' . $dl->file_spt) }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50/80 px-2.5 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-100 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-400">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            SPT PDF
                                        </a>
                                    @else
                                        <span class="text-xs text-stone-400 font-italic">Tidak ada berkas</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    @if($dl->status === 'disetujui')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300">
                                            ✓ Disetujui
                                        </span>
                                    @elseif($dl->status === 'ditolak')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-semibold text-rose-800 dark:bg-rose-500/20 dark:text-rose-300">
                                            ✕ Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800 dark:bg-amber-500/20 dark:text-amber-300">
                                            ⏱ Pending
                                        </span>
                                    @endif
                                    @if($dl->catatan_atasan)
                                        <p class="mt-1 text-[11px] text-stone-500 italic">Catatan: {{ $dl->catatan_atasan }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    @if($dl->status === 'pending')
                                        <div class="flex items-center justify-end gap-1.5" x-data="{ openApprove: false, openReject: false }">
                                            {{-- Button Setujui --}}
                                            <button @click="openApprove = true" class="rounded-lg bg-emerald-600 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700 shadow-sm">
                                                Setujui
                                            </button>
                                            {{-- Button Tolak --}}
                                            <button @click="openReject = true" class="rounded-lg bg-rose-600 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-rose-700 shadow-sm">
                                                Tolak
                                            </button>

                                            {{-- Modal Setujui --}}
                                            <template x-teleport="body">
                                                <div x-show="openApprove" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
                                                    <div @click.outside="openApprove = false" class="w-full max-w-md rounded-2xl bg-white p-6 text-left shadow-xl dark:bg-kpi-dark-surface">
                                                        <h3 class="text-lg font-bold text-kpi-black dark:text-stone-100">Setujui Pengajuan Dinas Luar?</h3>
                                                        <p class="mt-1 text-xs text-kpi-gray">Persetujuan ini akan melonggarkan batas geofence presensi pegawai pada tanggal tugas.</p>
                                                        <form method="POST" action="{{ route('dinas-luar.setujui', $dl->id) }}" class="mt-4 space-y-3">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div>
                                                                <label class="block text-xs font-semibold text-kpi-black dark:text-stone-200">Catatan (Opsional)</label>
                                                                <input type="text" name="catatan_atasan" placeholder="Catatan persetujuan..." class="mt-1 w-full rounded-xl border border-kpi-line p-2.5 text-sm dark:border-white/10 dark:bg-stone-800">
                                                            </div>
                                                            <div class="flex justify-end gap-2 pt-2">
                                                                <button type="button" @click="openApprove = false" class="rounded-xl px-4 py-2 text-xs font-semibold text-kpi-gray hover:bg-stone-100">Batal</button>
                                                                <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-xs font-semibold text-white hover:bg-emerald-700">Ya, Setujui</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </template>

                                            {{-- Modal Tolak --}}
                                            <template x-teleport="body">
                                                <div x-show="openReject" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
                                                    <div @click.outside="openReject = false" class="w-full max-w-md rounded-2xl bg-white p-6 text-left shadow-xl dark:bg-kpi-dark-surface">
                                                        <h3 class="text-lg font-bold text-kpi-black dark:text-stone-100">Tolak Pengajuan Dinas Luar?</h3>
                                                        <p class="mt-1 text-xs text-kpi-gray">Berikan alasan penolakan untuk pegawai yang bersangkutan.</p>
                                                        <form method="POST" action="{{ route('dinas-luar.tolak', $dl->id) }}" class="mt-4 space-y-3">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div>
                                                                <label class="block text-xs font-semibold text-kpi-black dark:text-stone-200">Alasan Penolakan</label>
                                                                <input type="text" name="catatan_atasan" placeholder="Alasan penolakan..." required class="mt-1 w-full rounded-xl border border-kpi-line p-2.5 text-sm dark:border-white/10 dark:bg-stone-800">
                                                            </div>
                                                            <div class="flex justify-end gap-2 pt-2">
                                                                <button type="button" @click="openReject = false" class="rounded-xl px-4 py-2 text-xs font-semibold text-kpi-gray hover:bg-stone-100">Batal</button>
                                                                <button type="submit" class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-700">Ya, Tolak</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    @else
                                        <span class="text-xs text-stone-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-kpi-gray">
                                    Belum ada data pengajuan Dinas Luar / WFA.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-kpi-line p-4 dark:border-white/10 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-2.5">
                    <x-per-page :current="request('per_page', 15)" />
                    <span class="text-xs text-kpi-gray dark:text-stone-400">
                        (Total <strong class="text-kpi-black dark:text-stone-200">{{ $dinasLuars->total() }}</strong> entri)
                    </span>
                </div>
                <div class="clean-pagination">
                    {{ $dinasLuars->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
