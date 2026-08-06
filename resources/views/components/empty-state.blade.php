@props([
    'title' => 'Belum Ada Data',
    'description' => 'Belum ada data yang tersedia atau sesuai dengan filter pencarian Anda.',
    'icon' => 'folder-open',
    'actionUrl' => null,
    'actionLabel' => 'Tambah Data',
    'resetUrl' => null,
    'resetLabel' => 'Reset Filter',
])

<div class="empty-state py-12 px-4 flex flex-col items-center justify-center text-center">
    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-kpi-cream-soft text-kpi-gray dark:bg-white/10 dark:text-stone-300 shadow-sm border border-kpi-line dark:border-white/10">
        @if($icon === 'search' || $icon === 'magnifying-glass')
            <svg class="h-8 w-8 stroke-[1.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
        @elseif($icon === 'users' || $icon === 'user-group')
            <svg class="h-8 w-8 stroke-[1.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
            </svg>
        @elseif($icon === 'calendar' || $icon === 'clock')
            <svg class="h-8 w-8 stroke-[1.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
            </svg>
        @elseif($icon === 'document' || $icon === 'clipboard')
            <svg class="h-8 w-8 stroke-[1.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
        @else
            <svg class="h-8 w-8 stroke-[1.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A7.5 7.5 0 0 1 9.75 4.5h4.5A7.5 7.5 0 0 1 21.75 12v.75m-8.625 4.5h4.5a3 3 0 0 0 3-3V12M3.75 12v3a3 3 0 0 0 3 3h1.5m0-6.75A2.25 2.25 0 0 1 10.5 6h3a2.25 2.25 0 0 1 2.25 2.25v.75m-6 3a2.25 2.25 0 0 1 2.25 2.25v3" />
            </svg>
        @endif
    </div>

    <h3 class="text-base font-bold text-kpi-black dark:text-stone-100">{{ $title }}</h3>
    @if($description)
        <p class="mt-1 max-w-sm text-xs text-kpi-gray dark:text-stone-400 leading-relaxed">{{ $description }}</p>
    @endif

    <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
        @if($resetUrl)
            <a href="{{ $resetUrl }}" class="btn-secondary text-xs px-3.5 py-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>{{ $resetLabel }}</span>
            </a>
        @endif

        @if($actionUrl)
            <a href="{{ $actionUrl }}" class="btn-primary text-xs px-3.5 py-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>{{ $actionLabel }}</span>
            </a>
        @endif
    </div>
</div>
