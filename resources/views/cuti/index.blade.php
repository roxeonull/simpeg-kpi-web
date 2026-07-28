<x-app-layout title="Cuti & Izin">
    {{-- Tab Navigation --}}
    <div class="mb-6 flex gap-1 overflow-x-auto border-b border-kpi-line dark:border-white/10">
        <a href="{{ route('cuti.index') }}" 
           class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('cuti.index') && !request()->routeIs('cuti.kalender') && !request()->routeIs('cuti.analitik') && !request()->routeIs('cuti.rekomendasi') ? 'border-kpi-red text-kpi-red' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200' }}">
            Daftar Pengajuan
        </a>
        <a href="{{ route('cuti.kalender') }}" 
           class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('cuti.kalender') ? 'border-kpi-red text-kpi-red' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200' }}">
            Kalender Tim
        </a>
        <a href="{{ route('cuti.analitik') }}" 
           class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('cuti.analitik') ? 'border-kpi-red text-kpi-red' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200' }}">
            Analitik
        </a>
        <a href="{{ route('cuti.rekomendasi') }}" 
           class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('cuti.rekomendasi') ? 'border-kpi-red text-kpi-red' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200' }}">
            Rekomendasi Cerdas
        </a>
    </div>

    {{-- Status Summary Pills --}}
    <div class="mb-5 flex flex-wrap gap-2">
        @php
            $currentStatus = request('status', '');
        @endphp
        <a href="{{ route('cuti.index', array_merge(request()->except('page'), ['status' => ''])) }}" 
           class="inline-flex items-center gap-1.5 rounded-xl border px-3.5 py-1.5 text-xs font-semibold tracking-wide transition-all
                  {{ $currentStatus === '' 
                     ? 'border-kpi-red bg-kpi-red text-white' 
                     : 'border-kpi-line bg-white/40 text-kpi-gray hover:bg-stone-50 dark:border-white/10 dark:bg-white/5 dark:text-stone-300 dark:hover:bg-white/10' }}">
            Semua Pengajuan
            <span class="mono ml-1 px-1.5 py-0.5 rounded-full text-[10px] {{ $currentStatus === '' ? 'bg-white/20 text-white' : 'bg-stone-100 text-stone-600 dark:bg-white/10 dark:text-stone-300' }}">
                {{ $counts['semua'] }}
            </span>
        </a>
        
        <a href="{{ route('cuti.index', array_merge(request()->except('page'), ['status' => 'menunggu'])) }}" 
           class="inline-flex items-center gap-1.5 rounded-xl border px-3.5 py-1.5 text-xs font-semibold tracking-wide transition-all
                  {{ $currentStatus === 'menunggu' 
                     ? 'border-amber-500 bg-amber-500 text-white' 
                     : 'border-kpi-line bg-white/40 text-kpi-gray hover:bg-stone-50 dark:border-white/10 dark:bg-white/5 dark:text-stone-300 dark:hover:bg-white/10' }}">
            Menunggu
            <span class="mono ml-1 px-1.5 py-0.5 rounded-full text-[10px] {{ $currentStatus === 'menunggu' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-400' }}">
                {{ $counts['menunggu'] }}
            </span>
        </a>

        <a href="{{ route('cuti.index', array_merge(request()->except('page'), ['status' => 'disetujui'])) }}" 
           class="inline-flex items-center gap-1.5 rounded-xl border px-3.5 py-1.5 text-xs font-semibold tracking-wide transition-all
                  {{ $currentStatus === 'disetujui' 
                     ? 'border-emerald-500 bg-emerald-500 text-white' 
                     : 'border-kpi-line bg-white/40 text-kpi-gray hover:bg-stone-50 dark:border-white/10 dark:bg-white/5 dark:text-stone-300 dark:hover:bg-white/10' }}">
            Disetujui
            <span class="mono ml-1 px-1.5 py-0.5 rounded-full text-[10px] {{ $currentStatus === 'disetujui' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400' }}">
                {{ $counts['disetujui'] }}
            </span>
        </a>

        <a href="{{ route('cuti.index', array_merge(request()->except('page'), ['status' => 'ditolak'])) }}" 
           class="inline-flex items-center gap-1.5 rounded-xl border px-3.5 py-1.5 text-xs font-semibold tracking-wide transition-all
                  {{ $currentStatus === 'ditolak' 
                     ? 'border-rose-500 bg-rose-500 text-white' 
                     : 'border-kpi-line bg-white/40 text-kpi-gray hover:bg-stone-50 dark:border-white/10 dark:bg-white/5 dark:text-stone-300 dark:hover:bg-white/10' }}">
            Ditolak
            <span class="mono ml-1 px-1.5 py-0.5 rounded-full text-[10px] {{ $currentStatus === 'ditolak' ? 'bg-white/20 text-white' : 'bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-400' }}">
                {{ $counts['ditolak'] }}
            </span>
        </a>
    </div>

    <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
        <form method="GET" class="relative z-20 flex flex-wrap items-center gap-2">
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama pegawai..." class="input max-w-xs">
            <x-select name="status" :value="$filters['status'] ?? ''" :options="[
                ['value' => '', 'label' => 'Semua Status'],
                ['value' => 'menunggu', 'label' => 'Menunggu (Semua)'],
                ['value' => 'menunggu_atasan', 'label' => 'Menunggu Atasan'],
                ['value' => 'menunggu_hr', 'label' => 'Menunggu HR'],
                ['value' => 'disetujui', 'label' => 'Disetujui'],
                ['value' => 'ditolak', 'label' => 'Ditolak']
            ]" class="w-full max-w-[180px]" />
            @php
                $jcOptions = [['value' => '', 'label' => 'Semua Jenis']];
                foreach ($jenisCutis as $jc) {
                    $jcOptions[] = ['value' => (string)$jc->id, 'label' => $jc->nama];
                }
            @endphp
            <x-select name="jenis_cuti" :value="$filters['jenis_cuti'] ?? ''" :options="$jcOptions" class="w-full max-w-[180px]" />
            <button class="btn-secondary">Filter</button>
        </form>

        <a href="{{ route('cuti.create') }}" class="btn-primary flex items-center gap-1.5 whitespace-nowrap shadow-[var(--shadow-card-hover)]">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Tambah Pengajuan
        </a>
    </div>

    <div id="live-list-container" class="space-y-4">
        <div class="table-shell">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-kpi-line bg-kpi-cream/60 dark:border-white/10 dark:bg-white/[0.03]">
                    <tr>
                        <th class="th">Pegawai</th>
                        <th class="th">Jenis</th>
                        <th class="th">Periode</th>
                        <th class="th">Hari</th>
                        <th class="th">Status</th>
                        <th class="th text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-kpi-line dark:divide-white/5">
                    @forelse ($cutis as $c)
                        <tr class="tr-hover cursor-pointer" onclick="if (!event.target.closest('a, button, form')) window.location='{{ route('cuti.show', $c) }}'">
                            <td class="td">
                                <p class="font-medium">{{ $c->pegawai->nama }}</p>
                                <p class="text-xs text-kpi-gray">{{ $c->pegawai->unit?->nama_unit }}</p>
                            </td>
                            <td class="td font-medium text-stone-700 dark:text-stone-300">{{ $c->jenisCuti?->nama ?? ucfirst($c->jenis_cuti) }}</td>
                            <td class="td mono">{{ $c->tanggal_mulai->format('d M') }} – {{ $c->tanggal_selesai->format('d M Y') }}</td>
                            <td class="td">{{ $c->jumlah_hari }} hari</td>
                            <td class="td">
                                <x-badge :color="$c->status === 'disetujui' ? 'success' : ($c->status === 'ditolak' ? 'danger' : 'warning')">{{ $c->statusLabel() }}</x-badge>
                            </td>
                            <td class="td text-right">
                                <a href="{{ route('cuti.show', $c) }}" class="btn-xs-secondary">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">
                            <div class="empty-state">
                                <svg class="h-8 w-8 text-kpi-gray/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <p class="text-sm text-kpi-gray">Belum ada pengajuan cuti.</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $cutis->links() }}</div>
    </div>
</x-app-layout>
