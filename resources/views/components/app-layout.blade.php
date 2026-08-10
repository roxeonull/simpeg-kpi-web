@props(['title' => 'Dashboard'])
<!DOCTYPE html>
<html lang="id" x-data="{ dark: localStorage.getItem('simpeg_dark') === 'true' }"
      x-init="$watch('dark', v => localStorage.setItem('simpeg_dark', v))"
      :class="{ 'dark': dark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} — SIMPEG-KPI</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('logo-kpi-head.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|source-serif-4:500,600,700|ibm-plex-mono:400,500,600" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function() {
            const cdnChart = window.Chart;
            Object.defineProperty(window, 'Chart', {
                get: function() { return cdnChart; },
                set: function(val) { /* ignore overwriting */ },
                configurable: true
            });
        })();
    </script>
    <style>
        /* Modern Gradient Mesh Background */
        body {
            background-attachment: fixed !important;
            background-image: linear-gradient(135deg, #fcfbfa 0%, #f7f3ea 40%, #e8e2d4 100%) !important;
        }
        .dark body {
            background-color: #141210 !important;
            background-image: linear-gradient(135deg, #12100e 0%, #171411 50%, #12100e 100%) !important;
        }

        /* Glassmorphic Translucent Sidebar */
        aside {
            background-color: rgba(255, 255, 255, 0.45) !important;
            backdrop-filter: blur(28px) !important;
            -webkit-backdrop-filter: blur(28px) !important;
            box-shadow: 4px 0 24px -12px rgba(28, 23, 18, 0.06) !important;
            transition: width 300ms cubic-bezier(0.16, 1, 0.3, 1), transform 300ms cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .dark aside {
            background-color: rgba(37, 33, 27, 0.85) !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
        }

        /* Glowing capsule background for active sidebar link */
        aside a.bg-kpi-red-soft {
            background: linear-gradient(to right, rgba(193, 39, 45, 0.08), rgba(180, 135, 42, 0.04)) !important;
            border-left: 3px solid #C1272D !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6), 0 2px 8px rgba(193, 39, 45, 0.03) !important;
            border-radius: 8px !important;
        }
        .dark aside a[class*="dark:bg-kpi-red/15"] {
            background: linear-gradient(to right, rgba(193, 39, 45, 0.16), rgba(180, 135, 42, 0.06)) !important;
            border-left: 3px solid #C1272D !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.05) !important;
            border-radius: 8px !important;
            color: #fff !important;
        }

        /* Glassmorphic Translucent Header */
        header {
            background-color: rgba(255, 255, 255, 0.5) !important;
            backdrop-filter: blur(20px) !important;
            -webkit-backdrop-filter: blur(20px) !important;
        }
        .dark header {
            background-color: rgba(30, 26, 21, 0.8) !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
        }

        /* Glassmorphic Translucent Cards & Panels */
        .card, .panel {
            background-color: rgba(255, 255, 255, 0.55) !important;
            backdrop-filter: blur(20px) !important;
            -webkit-backdrop-filter: blur(20px) !important;
            border-color: rgba(231, 224, 210, 0.4) !important;
            box-shadow: 0 10px 30px -15px rgba(28, 23, 18, 0.05), var(--shadow-card) !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .dark .card, .dark .panel {
            background-color: #25211B !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.35) !important;
        }

        /* Premium Shadow Glows on Hover */
        .card-glow-emerald:hover {
            box-shadow: 0 16px 36px -8px rgba(16, 185, 129, 0.18), 0 4px 12px rgba(16, 185, 129, 0.06) !important;
            transform: translateY(-4px) !important;
        }
        .card-glow-sky:hover {
            box-shadow: 0 16px 36px -8px rgba(14, 165, 233, 0.18), 0 4px 12px rgba(14, 165, 233, 0.06) !important;
            transform: translateY(-4px) !important;
        }
        .card-glow-red:hover {
            box-shadow: 0 16px 36px -8px rgba(193, 39, 45, 0.18), 0 4px 12px rgba(193, 39, 45, 0.06) !important;
            transform: translateY(-4px) !important;
        }
        .card-glow-amber:hover {
            box-shadow: 0 16px 36px -8px rgba(245, 158, 11, 0.18), 0 4px 12px rgba(245, 158, 11, 0.06) !important;
            transform: translateY(-4px) !important;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-stone-50 via-kpi-cream/15 to-stone-100 font-sans text-kpi-black transition-colors dark:from-kpi-dark-bg dark:via-stone-900 dark:to-kpi-dark-bg dark:text-stone-100 overflow-x-hidden">
    {{-- Floating orbs for glassmorphism backdrop contrast --}}
    <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden opacity-45 dark:opacity-20">
        <div class="absolute -top-40 -left-40 h-[600px] w-[600px] rounded-full bg-[#C1272D]/6 blur-[130px] animate-float" style="animation-duration: 15s;"></div>
        <div class="absolute top-1/2 right-1/4 h-[500px] w-[500px] rounded-full bg-[#B4872A]/8 blur-[120px] animate-float" style="animation-duration: 18s; animation-delay: 2s;"></div>
        <div class="absolute -bottom-20 left-1/3 h-[550px] w-[550px] rounded-full bg-emerald-500/4 blur-[110px] animate-float" style="animation-duration: 22s; animation-delay: 4s;"></div>
    </div>
    
<div class="flex min-h-screen relative z-10"
     x-data="{ sidebarOpen: false, sidebarCollapsed: localStorage.getItem('simpeg_sidebar_collapsed') === 'true' }"
     x-init="$watch('sidebarCollapsed', val => localStorage.setItem('simpeg_sidebar_collapsed', val))">

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-50 flex -translate-x-full flex-col border-r border-kpi-line bg-white/75 backdrop-blur-xl transition-all duration-300 ease-in-out dark:border-white/10 dark:bg-kpi-dark-surface/80 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0"
           :class="{
               '!translate-x-0': sidebarOpen,
               'w-72 lg:w-72': !sidebarCollapsed,
               'w-72 lg:w-20': sidebarCollapsed
           }">

        {{-- Single Floating Glass Circular Toggle Button --}}
        <button @click="sidebarCollapsed = !sidebarCollapsed"
                class="hidden lg:flex absolute -right-3.5 top-6.5 z-50 h-7 w-7 items-center justify-center rounded-full bg-white dark:bg-kpi-dark-surface border border-kpi-line dark:border-white/15 text-stone-500 hover:text-kpi-red dark:hover:text-kpi-gold shadow-md hover:shadow-lg transition-all duration-300 hover:scale-110 active:scale-95 cursor-pointer"
                :title="sidebarCollapsed ? 'Perluas Sidebar' : 'Ciutkan Sidebar'"
                aria-label="Toggle sidebar collapse">
            <svg class="h-3.5 w-3.5 transition-transform duration-300" :class="{ 'rotate-180': sidebarCollapsed }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        {{-- Sidebar Header --}}
        <div class="flex h-20 items-center border-b border-kpi-line transition-all duration-300 dark:border-white/10"
             :class="sidebarCollapsed ? 'justify-center px-2' : 'justify-between px-5'">
            <div class="flex items-center gap-3 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
                <img src="{{ asset('logo-kpi.jpg') }}" alt="Logo KPI"
                     class="h-10 w-10 shrink-0 rounded-full object-cover transition-transform hover:scale-105">
                <div class="min-w-0 leading-tight transition-all duration-200"
                     :class="sidebarCollapsed ? 'hidden lg:hidden' : 'block'">
                    <p class="truncate text-sm font-bold tracking-tight text-kpi-black dark:text-stone-100">SIMPEG-KPI</p>
                    <p class="truncate text-[11px] text-kpi-gray dark:text-stone-400">Sistem Informasi Kepegawaian</p>
                </div>
            </div>

            {{-- Close button for mobile drawer --}}
            <button class="ml-auto rounded-lg p-1.5 text-kpi-gray hover:bg-stone-100 dark:hover:bg-white/10 lg:hidden" @click="sidebarOpen = false" aria-label="Tutup menu">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Nav Area --}}
        <nav class="flex-1 space-y-5 overflow-y-auto overflow-x-hidden py-5"
             :class="sidebarCollapsed ? 'px-2' : 'px-3'">
            <div>
                <p class="eyebrow px-2.5 pb-2 transition-all duration-200"
                   :class="sidebarCollapsed ? 'hidden lg:hidden' : 'block'">Utama</p>
                <div :class="sidebarCollapsed ? 'hidden lg:block my-2 border-t border-kpi-line/40 dark:border-white/10 opacity-0' : 'hidden'"></div>
                <x-nav-link route="dashboard" icon="home" activePattern="dashboard*">Dashboard</x-nav-link>
            </div>

            @if(auth()->user()->role === 'admin')
            <div>
                <p class="eyebrow px-2.5 pb-2 transition-all duration-200"
                   :class="sidebarCollapsed ? 'hidden lg:hidden' : 'block'">Kepegawaian</p>
                <div :class="sidebarCollapsed ? 'block my-2 border-t border-kpi-line/40 dark:border-white/10' : 'hidden'"></div>
                <x-nav-link route="pegawai.index" icon="users" activePattern="pegawai.*">Data Pegawai</x-nav-link>
                <x-nav-link route="pengajuan-perubahan.index" icon="pencil" activePattern="pengajuan-perubahan.*">Pengajuan Perubahan</x-nav-link>
            </div>
            @endif

            @if(in_array(auth()->user()->role, ['admin', 'atasan']))
            <div>
                <p class="eyebrow px-2.5 pb-2 transition-all duration-200"
                   :class="sidebarCollapsed ? 'hidden lg:hidden' : 'block'">Operasional</p>
                <div :class="sidebarCollapsed ? 'block my-2 border-t border-kpi-line/40 dark:border-white/10' : 'hidden'"></div>
                <x-nav-link route="pelatihan.index" icon="academic-cap" activePattern="pelatihan.*">Pelatihan</x-nav-link>
                <x-nav-link route="absensi.index" icon="clock" activePattern="absensi.*,dinas-luar.*,jadwal-shift.*">Absensi</x-nav-link>
                <x-nav-link route="cuti.index" icon="calendar" activePattern="cuti.*">Cuti & Izin</x-nav-link>
            </div>
            @endif

            @if(auth()->user()->role === 'admin')
            <div>
                <p class="eyebrow px-2.5 pb-2 transition-all duration-200"
                   :class="sidebarCollapsed ? 'hidden lg:hidden' : 'block'">Sistem</p>
                <div :class="sidebarCollapsed ? 'block my-2 border-t border-kpi-line/40 dark:border-white/10' : 'hidden'"></div>
                <x-nav-link route="laporan.index" icon="document-download" activePattern="laporan.*">Laporan</x-nav-link>
                <x-nav-link route="user.index" icon="user-cog" activePattern="user.*">Kelola User</x-nav-link>
                <x-nav-link route="pengaturan.index" icon="cog" activePattern="pengaturan.*">Pengaturan</x-nav-link>
            </div>
            @endif
        </nav>

        {{-- Profile Footer --}}
        <div class="border-t border-kpi-line transition-all duration-300 dark:border-white/10"
             :class="sidebarCollapsed ? 'flex justify-center p-3' : 'p-4'">
            <div class="group relative flex items-center rounded-xl bg-kpi-cream transition-all duration-300 dark:bg-white/5"
                 :class="sidebarCollapsed ? 'justify-center p-2' : 'gap-3 px-3 py-2.5 w-full'">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-kpi-gold-soft text-xs font-semibold text-kpi-red-dark dark:bg-kpi-gold/20 dark:text-kpi-gold shadow-sm">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0 leading-tight transition-all duration-200"
                     :class="sidebarCollapsed ? 'hidden lg:hidden' : 'block'">
                    <p class="truncate text-sm font-semibold text-kpi-black dark:text-stone-100">{{ auth()->user()->name }}</p>
                    <p class="truncate text-[11px] capitalize text-kpi-gray dark:text-stone-400">{{ auth()->user()->role }}</p>
                </div>

                {{-- User Profile Floating Tooltip when collapsed --}}
                <div x-show="sidebarCollapsed"
                     x-cloak
                     class="pointer-events-none absolute left-full top-1/2 ml-3.5 -translate-y-1/2 z-50 whitespace-nowrap rounded-lg bg-stone-900/90 text-stone-100 px-3 py-1.5 text-xs font-semibold shadow-lg backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-all duration-200 group-hover:translate-x-0.5 dark:bg-stone-800 dark:text-stone-100 dark:border dark:border-white/10 hidden lg:block">
                    <p class="font-semibold">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] capitalize text-stone-300">{{ auth()->user()->role }}</p>
                    <div class="absolute -left-1 top-1/2 h-2 w-2 -translate-y-1/2 rotate-45 bg-stone-900/90 dark:bg-stone-800"></div>
                </div>
            </div>
        </div>
    </aside>

    <div class="fixed inset-0 z-40 bg-kpi-black/40 backdrop-blur-[2px] lg:hidden" x-show="sidebarOpen" x-cloak x-transition.opacity @click="sidebarOpen = false"></div>

    {{-- Main --}}
    <div class="flex min-h-screen flex-1 flex-col min-w-0">
        <header class="sticky top-0 z-30 flex h-16 items-center justify-between gap-3 border-b border-kpi-line bg-white px-4 dark:border-white/10 dark:bg-kpi-dark-bg sm:px-6">
            <div class="flex min-w-0 items-center gap-3">
                <button class="shrink-0 rounded-lg p-2 text-kpi-gray hover:bg-stone-200/60 dark:hover:bg-white/10 lg:hidden" @click="sidebarOpen = true" aria-label="Buka menu">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="min-w-0">
                    <p class="eyebrow hidden sm:block">{{ auth()->user()->role === 'admin' ? 'Panel Administrator' : (auth()->user()->role === 'atasan' ? 'Panel Atasan' : 'Panel Pegawai') }}</p>
                    <h1 class="truncate font-serif text-lg font-semibold tracking-tight">{{ $title ?? 'Dashboard' }}</h1>
                </div>
            </div>

            <div class="flex shrink-0 items-center gap-1.5 sm:gap-3">
                <div class="mono hidden items-center gap-2 rounded-lg border border-kpi-line px-3 py-1.5 text-xs text-kpi-gray dark:border-white/10 md:flex"
                     x-data="{ now: '' }" x-init="setInterval(() => now = new Date().toLocaleString('id-ID', { weekday: 'short', day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' }), 1000); now = new Date().toLocaleString('id-ID', { weekday: 'short', day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' })">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-text="now"></span>
                </div>

                <button @click="dark = !dark" class="rounded-lg p-2 text-kpi-gray hover:bg-stone-200/60 dark:hover:bg-white/10 transition-all duration-200 hover:scale-105 active:scale-95" title="Ganti mode gelap/terang">
                    <svg x-show="!dark" class="h-5 w-5 transition-transform hover:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <svg x-show="dark" x-cloak class="h-5 w-5 transition-transform hover:-rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </button>

                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 rounded-lg px-1.5 py-1.5 hover:bg-stone-200/60 dark:hover:bg-white/10 sm:px-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-kpi-gold-soft text-sm font-semibold text-kpi-red-dark dark:bg-kpi-gold/20 dark:text-kpi-gold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden text-left sm:block">
                            <p class="text-sm font-semibold leading-tight">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] capitalize leading-tight text-kpi-gray">{{ auth()->user()->role }}</p>
                        </div>
                        <svg class="hidden h-4 w-4 text-kpi-gray sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-cloak @click.outside="open = false" x-transition.origin.top.right
                         class="absolute right-0 z-30 mt-2 w-48 overflow-hidden rounded-xl border border-kpi-line bg-white py-1 shadow-[var(--shadow-card-hover)] dark:border-white/10 dark:bg-kpi-dark-surface">
                        <div class="border-b border-kpi-line px-4 py-2.5 dark:border-white/10 sm:hidden">
                            <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                            <p class="text-xs capitalize text-kpi-gray">{{ auth()->user()->role }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2 px-4 py-2.5 text-left text-sm font-medium text-kpi-red hover:bg-kpi-red-soft dark:hover:bg-kpi-red/10">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Floating Toast Notifications Container --}}
        <div x-data="{ 
                showSuccess: {{ (session('status') || session('success')) ? 'true' : 'false' }}, 
                showError: {{ (session('error') || $errors->any()) ? 'true' : 'false' }},
                init() {
                    if (this.showSuccess) setTimeout(() => this.showSuccess = false, 4500);
                    if (this.showError) setTimeout(() => this.showError = false, 6500);
                }
             }" 
             class="fixed top-5 right-5 z-50 flex flex-col gap-2.5 max-w-sm w-full pointer-events-none">
             
            <template x-if="showSuccess">
                <div x-show="showSuccess" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                     class="pointer-events-auto flex items-center gap-3 rounded-2xl border border-emerald-300 bg-white/95 p-4 shadow-xl backdrop-blur-md dark:border-emerald-500/30 dark:bg-kpi-dark-surface/95">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div class="flex-1 text-xs font-medium text-stone-800 dark:text-stone-200">
                        <p class="font-bold text-sm text-emerald-700 dark:text-emerald-400">Berhasil</p>
                        <p class="mt-0.5">{{ session('status') ?? session('success') }}</p>
                    </div>
                    <button @click="showSuccess = false" class="text-stone-400 hover:text-stone-600 dark:hover:text-stone-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </template>

            <template x-if="showError">
                <div x-show="showError" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-2 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                     class="pointer-events-auto flex items-start gap-3 rounded-2xl border border-red-300 bg-white/95 p-4 shadow-xl backdrop-blur-md dark:border-red-500/30 dark:bg-kpi-dark-surface/95">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-kpi-red-soft text-kpi-red dark:bg-kpi-red/20 dark:text-red-300">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    </div>
                    <div class="flex-1 text-xs text-stone-800 dark:text-stone-200">
                        <p class="font-bold text-sm text-kpi-red dark:text-red-400">Terjadi Kesalahan</p>
                        @if(session('error'))
                            <p class="mt-0.5">{{ session('error') }}</p>
                        @endif
                        @if($errors->any())
                            <ul class="mt-1 list-inside list-disc space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <button @click="showError = false" class="text-stone-400 hover:text-stone-600 dark:hover:text-stone-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </template>
        </div>

        <main class="flex-1 px-4 py-6 sm:px-6 lg:py-8 min-w-0">
            {{ $slot }}
        </main>
    </div>
</div>

{{-- Global Live Search & Filter Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const listContainer = document.getElementById('live-list-container');
        const filterForms = document.querySelectorAll('form[method="get"], form[method="GET"]');

        // Fallback to standard submit if listContainer is not present
        if (!listContainer || filterForms.length === 0) {
            // 1. Restore focus and cursor selection if saved
            const focusedName = sessionStorage.getItem('live_filter_focus_name');
            const focusedCursor = sessionStorage.getItem('live_filter_focus_cursor');
            if (focusedName) {
                const el = document.querySelector(`[name="${focusedName}"]`);
                if (el) {
                    el.focus();
                    const len = el.value.length;
                    const cursor = focusedCursor !== null ? parseInt(focusedCursor, 10) : len;
                    el.setSelectionRange(cursor, cursor);
                }
                sessionStorage.removeItem('live_filter_focus_name');
                sessionStorage.removeItem('live_filter_focus_cursor');
            }

            // 2. Standard live submit (fallback if page doesn't have live-list-container)
            filterForms.forEach(function(form) {
                const submitBtn = form.querySelector('button:not([type="button"]), input[type="submit"]');
                if (submitBtn && (submitBtn.textContent.trim().toLowerCase() === 'filter' || submitBtn.value === 'Filter')) {
                    submitBtn.style.display = 'none';
                }

                let debounceTimeout;

                const triggerSubmit = function(inputEl) {
                    if (inputEl && inputEl.name) {
                        sessionStorage.setItem('live_filter_focus_name', inputEl.name);
                        sessionStorage.setItem('live_filter_focus_cursor', inputEl.selectionStart || 0);
                    }
                    form.submit();
                };

                form.querySelectorAll('select, input[type="date"]').forEach(function(el) {
                    el.addEventListener('change', function() {
                        triggerSubmit(el);
                    });
                });

                form.querySelectorAll('input[type="text"], input[type="search"]').forEach(function(input) {
                    input.addEventListener('input', function() {
                        clearTimeout(debounceTimeout);
                        debounceTimeout = setTimeout(function() {
                            triggerSubmit(input);
                        }, 500);
                    });
                    input.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            clearTimeout(debounceTimeout);
                            triggerSubmit(input);
                        }
                    });
                });
            });
            return;
        }

        // --- AJAX Live Search & Filter ---
        const performAjaxSearch = function(url) {
            listContainer.style.opacity = '0.5';
            listContainer.style.transition = 'opacity 0.15s ease-in-out';

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(response) {
                return response.text();
            })
            .then(function(htmlText) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(htmlText, 'text/html');
                const newContent = doc.getElementById('live-list-container');
                if (newContent) {
                    listContainer.innerHTML = newContent.innerHTML;
                    bindPaginationLinks();
                }
                listContainer.style.opacity = '1';
            })
            .catch(function(err) {
                console.error('Ajax search error:', err);
                listContainer.style.opacity = '1';
            });
        };

        const bindPaginationLinks = function() {
            listContainer.querySelectorAll('.pagination a, a[href*="page="]').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = link.getAttribute('href');
                    if (url) {
                        history.pushState(null, '', url);
                        performAjaxSearch(url);
                    }
                });
            });
        };

        filterForms.forEach(function(form) {
            const submitBtn = form.querySelector('button:not([type="button"]), input[type="submit"]');
            if (submitBtn && (submitBtn.textContent.trim().toLowerCase() === 'filter' || submitBtn.value === 'Filter')) {
                submitBtn.style.display = 'none';
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(form);
                const params = new URLSearchParams(formData).toString();
                const action = form.getAttribute('action') || window.location.pathname;
                const separator = action.includes('?') ? '&' : '?';
                const url = action + separator + params;

                history.pushState(null, '', url);
                performAjaxSearch(url);
            });

            let debounceTimeout;

            form.querySelectorAll('select, input[type="date"]').forEach(function(el) {
                el.addEventListener('change', function() {
                    form.dispatchEvent(new Event('submit'));
                });
            });

            form.querySelectorAll('input[type="text"], input[type="search"]').forEach(function(input) {
                input.addEventListener('input', function() {
                    clearTimeout(debounceTimeout);
                    debounceTimeout = setTimeout(function() {
                        form.dispatchEvent(new Event('submit'));
                    }, 500);
                });

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        clearTimeout(debounceTimeout);
                        form.dispatchEvent(new Event('submit'));
                    }
                });
            });
        });

        window.addEventListener('popstate', function() {
            performAjaxSearch(window.location.href);
        });

        // Initial binding
        bindPaginationLinks();
    });
</script>

{{-- Global Custom Confirmation Modal --}}
<div id="global-confirm-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 opacity-0 transition-opacity duration-200">
    <div class="w-full max-w-md transform scale-95 rounded-2xl border border-kpi-line bg-white p-6 shadow-2xl transition-transform duration-200 dark:border-white/10 dark:bg-kpi-dark-surface">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="font-serif text-lg font-bold text-kpi-black dark:text-stone-50">Konfirmasi Tindakan</h3>
        </div>
        <p id="global-confirm-message" class="mt-3 text-sm text-kpi-gray leading-relaxed">Apakah Anda yakin ingin menghapus data ini?</p>
        <div class="flex justify-end gap-3 pt-4 border-t border-kpi-line mt-4 dark:border-white/10">
            <button type="button" id="global-confirm-cancel" class="btn-secondary">Batal</button>
            <button type="button" id="global-confirm-ok" class="btn-danger">Ya, Hapus</button>
        </div>
    </div>
</div>

<script>
    // Silence browser native confirm dialog and let global submit interceptor handle it
    window.confirm = function() { return true; };

    document.addEventListener('DOMContentLoaded', function() {
        const confirmModal = document.getElementById('global-confirm-modal');
        const confirmMessage = document.getElementById('global-confirm-message');
        const confirmCancelBtn = document.getElementById('global-confirm-cancel');
        const confirmOkBtn = document.getElementById('global-confirm-ok');
        let currentConfirmCallback = null;

        const showCustomConfirmModal = function(message, callback) {
            confirmMessage.textContent = message;
            currentConfirmCallback = callback;

            confirmModal.classList.remove('hidden');
            // Force reflow
            confirmModal.offsetHeight;
            confirmModal.classList.add('opacity-100');
            confirmModal.querySelector('div').classList.remove('scale-95');
            confirmModal.querySelector('div').classList.add('scale-100');
        };

        const hideCustomConfirmModal = function() {
            confirmModal.classList.remove('opacity-100');
            confirmModal.querySelector('div').classList.remove('scale-100');
            confirmModal.querySelector('div').classList.add('scale-95');
            setTimeout(function() {
                confirmModal.classList.add('hidden');
                currentConfirmCallback = null;
            }, 200);
        };

        confirmCancelBtn.addEventListener('click', hideCustomConfirmModal);
        confirmModal.addEventListener('click', function(e) {
            if (e.target === confirmModal) {
                hideCustomConfirmModal();
            }
        });

        confirmOkBtn.addEventListener('click', function() {
            if (currentConfirmCallback) {
                currentConfirmCallback();
            }
            hideCustomConfirmModal();
        });

        // Intercept form submissions globally
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.dataset.confirmed === 'true') {
                return;
            }

            const onsubmitAttr = form.getAttribute('onsubmit');
            if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
                e.preventDefault();

                let message = 'Apakah Anda yakin ingin melakukan tindakan ini?';
                const match = onsubmitAttr.match(/confirm\(['"](.*?)['"]\)/);
                if (match && match[1]) {
                    message = match[1];
                }

                showCustomConfirmModal(message, function() {
                    form.dataset.confirmed = 'true';
                    form.submit();
                });
            }
        });
    });
</script>
</body>
</html>
