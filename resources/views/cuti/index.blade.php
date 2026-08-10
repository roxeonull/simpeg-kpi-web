<x-app-layout title="Cuti & Izin">
    {{-- Tab Navigation --}}
    <div class="mb-6 flex gap-1 overflow-x-auto border-b border-kpi-line dark:border-white/10">
        <a href="{{ route('cuti.index') }}" 
           class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('cuti.index') && !request()->routeIs('cuti.kalender') && !request()->routeIs('cuti.analitik') && !request()->routeIs('cuti.rekomendasi') && !request()->routeIs('cuti.workflows') ? 'border-kpi-red text-kpi-red font-semibold' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200' }}">
            Daftar Pengajuan
        </a>
        <a href="{{ route('cuti.kalender') }}" 
           class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('cuti.kalender') ? 'border-kpi-red text-kpi-red font-semibold' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200' }}">
            Kalender Tim
        </a>
        <a href="{{ route('cuti.analitik') }}" 
           class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('cuti.analitik') ? 'border-kpi-red text-kpi-red font-semibold' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200' }}">
            Analitik
        </a>
        <a href="{{ route('cuti.rekomendasi') }}" 
           class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('cuti.rekomendasi') ? 'border-kpi-red text-kpi-red font-semibold' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200' }}">
            Rekomendasi Cerdas
        </a>
        @if(auth()->user()->role === 'admin')
        <a href="{{ route('cuti.workflows') }}" 
           class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('cuti.workflows') ? 'border-kpi-red text-kpi-red font-semibold' : 'border-transparent text-kpi-gray hover:text-kpi-black dark:hover:text-stone-200' }}">
            Workflow Approval
        </a>
        @endif
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
                  {{ in_array($currentStatus, ['menunggu', 'menunggu_atasan', 'menunggu_hr']) 
                     ? 'border-amber-500 bg-amber-500 text-white' 
                     : 'border-kpi-line bg-white/40 text-kpi-gray hover:bg-stone-50 dark:border-white/10 dark:bg-white/5 dark:text-stone-300 dark:hover:bg-white/10' }}">
            Menunggu
            <span class="mono ml-1 px-1.5 py-0.5 rounded-full text-[10px] {{ in_array($currentStatus, ['menunggu', 'menunggu_atasan', 'menunggu_hr']) ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-400' }}">
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
                     ? 'border-kpi-red bg-kpi-red text-white' 
                     : 'border-kpi-line bg-white/40 text-kpi-gray hover:bg-stone-50 dark:border-white/10 dark:bg-white/5 dark:text-stone-300 dark:hover:bg-white/10' }}">
            Ditolak
            <span class="mono ml-1 px-1.5 py-0.5 rounded-full text-[10px] {{ $currentStatus === 'ditolak' ? 'bg-white/20 text-white' : 'bg-kpi-red-soft text-kpi-red-dark dark:bg-kpi-red/20 dark:text-red-300' }}">
                {{ $counts['ditolak'] }}
            </span>
        </a>
    </div>

    <div class="relative z-20 mb-4 flex flex-wrap items-center justify-between gap-3 bg-white/40 p-4 rounded-2xl border border-kpi-line dark:border-white/10 dark:bg-kpi-dark-surface/40 backdrop-blur">
        <form method="GET" class="flex flex-1 flex-wrap items-center gap-2.5">
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama pegawai..." class="input w-full sm:w-56">
            <x-select name="status" :value="$filters['status'] ?? ''" :options="[
                ['value' => '', 'label' => 'Semua Status'],
                ['value' => 'menunggu', 'label' => 'Menunggu (Semua)'],
                ['value' => 'menunggu_atasan', 'label' => 'Menunggu Atasan'],
                ['value' => 'menunggu_hr', 'label' => 'Menunggu HR'],
                ['value' => 'disetujui', 'label' => 'Disetujui'],
                ['value' => 'ditolak', 'label' => 'Ditolak']
            ]" class="w-full sm:w-44" />
            @php
                $jcOptions = [['value' => '', 'label' => 'Semua Jenis']];
                foreach ($jenisCutis as $jc) {
                    $jcOptions[] = ['value' => (string)$jc->id, 'label' => $jc->nama];
                }
            @endphp
            <x-select name="jenis_cuti_id" :value="$filters['jenis_cuti_id'] ?? ''" :options="$jcOptions" class="w-full sm:w-44" />
            <button class="btn-secondary">Filter</button>
        </form>

        @if(!auth()->user()->isPegawaiOnly())
            <a href="{{ route('cuti.create') }}" class="btn-primary shrink-0">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Pengajuan Cuti
            </a>
        @endif
    </div>

    <div id="live-list-container">
        <div class="table-shell">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-kpi-line bg-kpi-cream/60 dark:border-white/10 dark:bg-white/[0.03]">
                    <tr>
                        <th class="th">Pegawai</th>
                        <th class="th">Jenis Cuti</th>
                        <th class="th">Tanggal</th>
                        <th class="th">Durasi</th>
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
                        <tr><td colspan="6" class="p-4">
                            <x-empty-state
                                icon="calendar"
                                title="Belum Ada Pengajuan Cuti"
                                description="Tidak ada data pengajuan cuti yang sesuai dengan filter."
                                :resetUrl="route('cuti.index')"
                                resetLabel="Reset Filter" />
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 flex flex-wrap items-center justify-between gap-4 border-t border-kpi-line pt-4 dark:border-white/10">
            <div class="flex items-center gap-2.5">
                <x-per-page :current="request('per_page', 15)" />
                <span class="text-xs text-kpi-gray dark:text-stone-400">
                    (Total <strong class="text-kpi-black dark:text-stone-200">{{ $cutis->total() }}</strong> entri)
                </span>
            </div>
            <div class="clean-pagination">
                {{ $cutis->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
