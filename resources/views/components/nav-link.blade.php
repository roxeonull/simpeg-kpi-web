@props(['route', 'icon', 'activePattern' => null])
@php
    // activePattern allows passing a custom wildcard prefix (e.g. 'pegawai.*')
    // independently of the href route (e.g. 'pegawai.index').
    // If not set, falls back to the route name + '*' (e.g. 'pegawai.index*').
    $active = request()->routeIs($activePattern ?? ($route . '*'));
    $icons = [
        'home' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        'users' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 100-8 4 4 0 000 8zm6 3.13a4 4 0 00-3-3.87',
        'pencil' => 'M15.232 5.232l3.536 3.536M9 11l6.586-6.586a2 2 0 112.828 2.828L11.828 13.828H9V11z',
        'academic-cap' => 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0118 20.5H6a12.083 12.083 0 01.84-9.922L12 14z',
        'clock' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'calendar' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'document-download' => 'M12 10v6m0 0l-3-3m3 3l3-3M4 6a2 2 0 012-2h8l6 6v10a2 2 0 01-2 2H6a2 2 0 01-2-2V6z',
        'cog' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
        'user-cog' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z M3 20a6 6 0 0112 0v1H3v-1z',
    ];
    $path = $icons[$icon] ?? $icons['home'];
@endphp
<a href="{{ route($route) }}"
   class="group relative mb-1 flex items-center rounded-xl py-2.5 text-sm font-medium transition-all duration-200
   {{ $active ? 'bg-kpi-red-soft text-kpi-red-dark dark:bg-kpi-red/15 dark:text-white font-semibold' : 'text-stone-600 dark:text-stone-300' }}"
   :class="sidebarCollapsed ? 'justify-center px-0 h-10 w-10 mx-auto hover:bg-stone-200/60 hover:text-kpi-red dark:hover:bg-white/10 dark:hover:text-kpi-gold' : 'gap-3 pl-3.5 pr-3 hover:translate-x-0.5 hover:bg-stone-200/50 hover:text-kpi-red dark:hover:bg-white/10 dark:hover:text-stone-100'">
    <span class="absolute left-0 top-1/2 h-4 w-[3px] -translate-y-1/2 rounded-full bg-kpi-red transition-opacity {{ $active ? 'opacity-100' : 'opacity-0' }}"
          :class="sidebarCollapsed ? 'left-[-4px]' : 'left-0'"></span>
    <svg class="h-[18px] w-[18px] shrink-0 transition-transform duration-200 group-hover:scale-110 {{ $active ? 'text-kpi-red dark:text-white' : 'text-kpi-gray group-hover:text-kpi-red dark:group-hover:text-kpi-gold' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $path }}"/>
    </svg>
    <span class="truncate transition-all duration-200"
          :class="sidebarCollapsed ? 'hidden lg:hidden' : 'inline'">{{ $slot }}</span>

    {{-- Floating Tooltip when Collapsed --}}
    <div x-show="sidebarCollapsed"
         x-cloak
         class="pointer-events-none absolute left-full top-1/2 ml-3.5 -translate-y-1/2 z-50 whitespace-nowrap rounded-lg bg-stone-900/90 text-stone-100 px-3 py-1.5 text-xs font-medium shadow-xl backdrop-blur-md opacity-0 group-hover:opacity-100 transition-all duration-200 group-hover:translate-x-1 dark:bg-stone-800 dark:text-stone-100 dark:border dark:border-white/10 hidden lg:block">
        {{ $slot }}
        <div class="absolute -left-1 top-1/2 h-2 w-2 -translate-y-1/2 rotate-45 bg-stone-900/90 dark:bg-stone-800"></div>
    </div>
</a>
