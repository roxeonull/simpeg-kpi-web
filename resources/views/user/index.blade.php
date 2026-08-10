<x-app-layout title="Kelola User">
    <div class="space-y-6">

        {{-- 1. Stat Cards --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 lg:grid-cols-4">
            <div class="card card-glow-sky group transition-all duration-300 hover:-translate-y-1 animate-fade-in-up" style="animation-delay:50ms">
                <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400">Total Akun</p>
                <p class="stat-figure mt-2">{{ $totalUsers }}</p>
                <p class="mt-3 text-xs text-stone-500 dark:text-stone-400 flex items-center gap-1.5">
                    <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>Semua role terdaftar
                </p>
            </div>
            <div class="card card-glow-emerald group transition-all duration-300 hover:-translate-y-1 animate-fade-in-up" style="animation-delay:100ms">
                <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400">Akun Aktif</p>
                <p class="stat-figure mt-2 text-emerald-600 dark:text-emerald-400">{{ $aktifCount }}</p>
                <p class="mt-3 text-xs text-stone-500 dark:text-stone-400 flex items-center gap-1.5">
                    <span class="h-1.5 w-1.5 rounded-full bg-kpi-red"></span>{{ $nonaktifCount }} nonaktif
                </p>
            </div>
            <div class="card card-glow-red group transition-all duration-300 hover:-translate-y-1 animate-fade-in-up" style="animation-delay:150ms">
                <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400">Role</p>
                <div class="mt-2 flex flex-col gap-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-kpi-gray flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-kpi-red"></span>Admin</span>
                        <strong>{{ $adminCount }}</strong>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-kpi-gray flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-kpi-gold"></span>Atasan</span>
                        <strong>{{ $atasanCount }}</strong>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-kpi-gray flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-sky-500"></span>Pegawai</span>
                        <strong>{{ $pegawaiCount }}</strong>
                    </div>
                </div>
            </div>
            <div class="card card-glow-amber group transition-all duration-300 hover:-translate-y-1 animate-fade-in-up" style="animation-delay:200ms">
                <p class="text-[11px] font-bold uppercase tracking-wider text-stone-500 dark:text-stone-400">Belum Punya Akun</p>
                <p class="stat-figure mt-2 text-kpi-gold">{{ $tanpaAkunCount }}</p>
                <a href="{{ route('user.create') }}" class="mt-3 text-xs text-kpi-red hover:underline flex items-center gap-1">
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Buat Akun
                </a>
            </div>
        </div>

        {{-- 2. Error session --}}
        @if(session('error'))
            <div class="flex items-start gap-2.5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-400">
                <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- 3. Filter Bar --}}
        <div class="relative z-20 mb-4 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-kpi-line bg-white/40 p-4 backdrop-blur dark:border-white/10 dark:bg-kpi-dark-surface/40">
            <form method="GET" class="flex flex-1 flex-wrap items-center gap-2">
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama atau email..."
                       class="input max-w-xs">
                <x-select name="role" :value="$filters['role'] ?? ''" :options="[
                    ['value' => '', 'label' => 'Semua Role'],
                    ['value' => 'admin', 'label' => 'Admin'],
                    ['value' => 'atasan', 'label' => 'Atasan'],
                    ['value' => 'pegawai', 'label' => 'Pegawai'],
                ]" class="w-full max-w-[150px]" />
                <x-select name="is_active" :value="$filters['is_active'] ?? ''" :options="[
                    ['value' => '', 'label' => 'Aktif & Nonaktif'],
                    ['value' => '1', 'label' => 'Aktif'],
                    ['value' => '0', 'label' => 'Nonaktif'],
                ]" class="w-full max-w-[150px]" />
                <button class="btn-secondary">Filter</button>
            </form>
            <a href="{{ route('user.create') }}" class="btn-primary shrink-0">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah User
            </a>
        </div>

        {{-- 4. Tabel --}}
        <div id="live-list-container">
            <div class="table-shell">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-kpi-line bg-kpi-cream/60 dark:border-white/10 dark:bg-white/[0.03]">
                        <tr>
                            <th class="th">User</th>
                            <th class="th">Email</th>
                            <th class="th">Role</th>
                            <th class="th">Status</th>
                            <th class="th">Pegawai Tertaut</th>
                            <th class="th text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-kpi-line dark:divide-white/5">
                        @forelse($users as $u)
                            <tr class="tr-hover" x-data="{ showResetModal: false }">
                                {{-- Avatar + Nama --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-xs font-semibold
                                            {{ $u->role === 'admin' ? 'bg-kpi-red-soft text-kpi-red-dark dark:bg-kpi-red/20 dark:text-rose-300' : ($u->role === 'atasan' ? 'bg-kpi-gold-soft text-kpi-red-dark dark:bg-kpi-gold/20 dark:text-kpi-gold' : 'bg-sky-100 text-sky-700 dark:bg-sky-500/20 dark:text-sky-300') }}">
                                            {{ strtoupper(substr($u->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-kpi-black dark:text-stone-100">{{ $u->name }}</p>
                                            @if($u->id === auth()->id())
                                                <span class="text-[10px] font-semibold text-kpi-gold">(Anda)</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                {{-- Email --}}
                                <td class="px-4 py-3.5 text-kpi-gray">{{ $u->email }}</td>
                                {{-- Role Badge --}}
                                {{-- Role Badge --}}
                                <td class="px-4 py-3.5">
                                    @if($u->role === 'admin')
                                        <x-badge color="danger">Admin</x-badge>
                                    @elseif($u->role === 'atasan')
                                        <x-badge color="warning">Atasan</x-badge>
                                    @else
                                        <x-badge color="info">Pegawai</x-badge>
                                    @endif
                                </td>
                                {{-- Status Aktif --}}
                                <td class="px-4 py-3.5">
                                    @if($u->is_active)
                                        <x-badge color="success">Aktif</x-badge>
                                    @else
                                        <x-badge color="default">Nonaktif</x-badge>
                                    @endif
                                </td>
                                {{-- Pegawai Tertaut --}}
                                <td class="px-4 py-3.5">
                                    @if($u->pegawai)
                                        <a href="{{ route('pegawai.show', $u->pegawai) }}"
                                           class="text-sm font-medium text-kpi-black hover:text-kpi-red hover:underline dark:text-stone-100">
                                            {{ $u->pegawai->nama }}
                                        </a>
                                        <p class="text-xs text-kpi-gray">{{ $u->pegawai->nip }}</p>
                                    @else
                                        <span class="text-xs italic text-stone-400 dark:text-stone-500">— Tidak tertaut —</span>
                                    @endif
                                </td>
                                {{-- Aksi --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-end gap-1.5">
                                        {{-- Edit --}}
                                        <a href="{{ route('user.edit', $u) }}" class="btn-xs-secondary">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6.586-6.586a2 2 0 112.828 2.828L11.828 13.828H9V11z"/></svg>
                                            Edit
                                        </a>

                                        {{-- Reset Password (trigger Alpine modal) --}}
                                        <button @click="showResetModal = true" class="btn-xs-warning">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                            Reset
                                        </button>

                                        {{-- Toggle Aktif --}}
                                        <form method="POST" action="{{ route('user.toggle-active', $u) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    title="{{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                                    class="{{ $u->is_active ? 'btn-xs-warning' : 'btn-xs-success' }}">
                                                @if($u->is_active)
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                    Nonaktifkan
                                                @else
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Aktifkan
                                                @endif
                                            </button>
                                        </form>

                                        {{-- Hapus --}}
                                        <form method="POST" action="{{ route('user.destroy', $u) }}"
                                              onsubmit="return confirm('Hapus akun {{ addslashes($u->name) }}? Tindakan ini tidak dapat dibatalkan.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-xs-danger">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>

                                    {{-- Modal Reset Password (Alpine) --}}
                                    <div x-show="showResetModal" x-cloak
                                         x-transition.opacity
                                         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
                                         @keydown.escape.window="showResetModal = false">
                                        <div @click.outside="showResetModal = false"
                                             class="w-full max-w-sm rounded-2xl border border-kpi-line bg-white p-6 shadow-2xl dark:border-white/10 dark:bg-kpi-dark-surface">
                                            <h3 class="font-serif text-base font-bold text-kpi-black dark:text-stone-50">Reset Password</h3>
                                            <p class="mt-1 text-sm text-kpi-gray">Akun: <strong class="text-kpi-black dark:text-stone-100">{{ $u->name }}</strong></p>
                                            <form method="POST" action="{{ route('user.reset-password', $u) }}" class="mt-4 space-y-3">
                                                @csrf
                                                <div>
                                                    <label class="label">Password Baru</label>
                                                    <input type="password" name="password" required minlength="8"
                                                           class="input mt-1 w-full"
                                                           placeholder="Minimal 8 karakter">
                                                </div>
                                                <div class="flex justify-end gap-2 pt-2">
                                                    <button type="button" @click="showResetModal = false" class="btn-secondary">Batal</button>
                                                    <button type="submit" class="btn-primary">Reset Password</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-4">
                                    <x-empty-state
                                        icon="users"
                                        title="Tidak Ada Akun User"
                                        description="Tidak ada data akun user yang sesuai dengan kata kunci atau filter yang Anda pilih."
                                        :resetUrl="route('user.index')"
                                        resetLabel="Reset Filter"
                                        :actionUrl="route('user.create')"
                                        actionLabel="Tambah User Baru" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Footer --}}
            <div class="mt-4 flex flex-wrap items-center justify-between gap-4 border-t border-kpi-line pt-4 dark:border-white/10 px-1">
                <div class="flex items-center gap-2.5">
                    <x-per-page :current="request('per_page', 15)" />
                    <span class="text-xs text-kpi-gray dark:text-stone-400">
                        (Total <strong class="text-kpi-black dark:text-stone-200">{{ $users->total() }}</strong> akun)
                    </span>
                </div>
                <div class="clean-pagination">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
