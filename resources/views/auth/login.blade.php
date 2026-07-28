<!DOCTYPE html>
<html lang="id" x-data="{ dark: localStorage.getItem('simpeg_dark') === 'true' }"
      x-init="$watch('dark', v => localStorage.setItem('simpeg_dark', v))"
      :class="{ 'dark': dark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — SIMPEG-KPI</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('logo-kpi-head.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|source-serif-4:500,600,700|ibm-plex-mono:400,500" rel="stylesheet" />
    <style>
        /* Animated Gradient Mesh */
        .bg-gradient-mesh {
            background-color: #0f0c0a !important;
            background-image: 
                radial-gradient(at 0% 0%, rgba(193, 39, 45, 0.38) 0px, transparent 65%),
                radial-gradient(at 100% 0%, rgba(212, 175, 91, 0.28) 0px, transparent 65%),
                radial-gradient(at 100% 100%, rgba(193, 39, 45, 0.3) 0px, transparent 65%),
                radial-gradient(at 0% 100%, rgba(28, 23, 18, 0.98) 0px, transparent 65%) !important;
            background-size: 240% 240% !important;
            animation: mesh-drift 12s ease infinite alternate !important;
        }

        @keyframes mesh-drift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Floating Animation */
        .animate-float {
            animation: float 7s ease-in-out infinite !important;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(0.5deg); }
        }

        /* Entrance Animations */
        .animate-fade-in-up {
            animation: fadeInUp 0.65s cubic-bezier(0.16, 1, 0.3, 1) both !important;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Glassmorphic Card slab */
        .glass-card-premium {
            background-color: rgba(255, 255, 255, 0.6) !important;
            backdrop-filter: blur(28px) !important;
            -webkit-backdrop-filter: blur(28px) !important;
            border: 1px solid rgba(231, 224, 210, 0.6) !important;
            box-shadow: 0 24px 70px -15px rgba(28, 23, 18, 0.12) !important;
        }
        .dark .glass-card-premium {
            background-color: rgba(28, 24, 18, 0.7) !important;
            border-color: rgba(255, 255, 255, 0.04) !important;
            box-shadow: 0 24px 70px -15px rgba(0, 0, 0, 0.5) !important;
        }

        /* Premium Inputs with translate effect on focus */
        .input-premium {
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .input-premium:focus {
            border-color: #C1272D !important;
            box-shadow: 0 8px 20px -6px rgba(193,39,45,0.25), 0 0 0 2px rgba(193,39,45,0.15) !important;
            transform: translateY(-2px) !important;
        }

        /* Premium submit button with sweeping gradient and hover glow */
        .btn-premium {
            background: linear-gradient(135deg, #C1272D 0%, #e03238 50%, #C1272D 100%) !important;
            background-size: 200% auto !important;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
            box-shadow: 0 4px 14px rgba(193, 39, 45, 0.35) !important;
            border: none !important;
            cursor: pointer !important;
        }
        .btn-premium:hover {
            background-position: right center !important;
            box-shadow: 0 8px 24px rgba(193, 39, 45, 0.5) !important;
            transform: translateY(-2px) scale(1.01) !important;
        }
        .btn-premium:active {
            transform: translateY(0) scale(0.98) !important;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-kpi-cream font-sans text-kpi-black transition-colors duration-300 dark:bg-kpi-dark-bg dark:text-stone-100 overflow-x-hidden">
<div class="relative flex min-h-screen overflow-hidden">

    {{-- Floating theme toggle --}}
    <button @click="dark = !dark" 
            class="absolute top-6 right-6 z-50 rounded-xl p-2.5 text-kpi-gray hover:bg-stone-200/50 dark:hover:bg-white/5 border border-kpi-line dark:border-white/10 backdrop-blur-sm shadow-sm transition-all hover:scale-105 active:scale-95" 
            title="Ganti mode gelap/terang">
        <svg x-show="!dark" class="h-5 w-5 transition-transform hover:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        <svg x-show="dark" x-cloak class="h-5 w-5 transition-transform hover:-rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
    </button>

    {{-- Brand panel --}}
    <div class="relative hidden w-[46%] shrink-0 overflow-hidden lg:flex lg:flex-col lg:justify-between border-r border-kpi-line dark:border-white/10 bg-kpi-black">
        
        {{-- Layer 1: Background Image with Blur --}}
        <div class="absolute inset-0 z-0 overflow-hidden bg-kpi-black">
            <div class="absolute inset-0 bg-cover scale-110 blur-[2px] opacity-100 transition-all duration-500"
                 style="background-image: url('{{ asset('images/login-bg.jpg') }}'); background-position: center 80%; left 30%">
            </div>
        </div>

        {{-- Layer 2: Institutional Color Overlay (Gradient from --color-kpi-black to --color-kpi-red-dark at 70% opacity) --}}
        <div class="absolute inset-0 z-10" style="background: linear-gradient(135deg, rgba(28, 23, 18, 0.70) 0%, rgba(132, 24, 28, 0.70) 100%);"></div>

        {{-- Layer 3: Texture & Glow overlay (Pola titik-titik dan radial glow merah) --}}
        <div class="absolute inset-0 z-20 pointer-events-none">
            {{-- Radial glow --}}
            <div class="absolute -left-16 -top-16 h-80 w-80 rounded-full bg-kpi-red/20 blur-3xl animate-float" style="animation-duration: 8s;"></div>
            <div class="absolute -right-16 -bottom-16 h-80 w-80 rounded-full bg-kpi-gold/15 blur-3xl animate-float" style="animation-duration: 10s; animation-delay: 1s;"></div>
            <div class="absolute top-1/3 left-1/4 h-72 w-72 rounded-full bg-kpi-red-dark/15 blur-3xl animate-float" style="animation-duration: 12s; animation-delay: 2s;"></div>
            
            {{-- Dot pattern --}}
            <div class="absolute inset-0 opacity-[0.04]"
                 style="background-image: radial-gradient(circle, #D4AF5B 1px, transparent 1px); background-size: 24px 24px;"></div>
        </div>

        {{-- Layer 4: Content (z-30) --}}
        {{-- Header branding --}}
        <div class="relative z-30 flex items-center gap-3 px-12 pt-12 animate-fade-in-up" style="animation-delay: 100ms;">
            <img src="{{ asset('logo-kpi.jpg') }}" alt="Logo KPI" class="h-11 w-11 shrink-0 rounded-full object-cover transition-transform hover:scale-105 duration-500">
            <div class="leading-tight text-stone-200">
                <p class="text-sm font-bold tracking-tight text-white">SIMPEG-KPI</p>
                <p class="text-[11px] text-stone-400">Sistem Informasi Kepegawaian</p>
            </div>
        </div>

        {{-- Center glassmorphic info box --}}
        <div class="relative z-30 mx-10 my-auto rounded-2xl border border-white/10 bg-white/[0.04] p-8 backdrop-blur-md shadow-2xl animate-fade-in-up" style="animation-delay: 250ms;">
            <p class="eyebrow !text-kpi-gold-soft/80">Komisi Penyiaran Indonesia Pusat</p>
            <h1 class="mt-3.5 font-serif text-3xl font-semibold leading-[1.2] text-white">
                Satu Pintu untuk Seluruh Data Kepegawaian.
            </h1>
            <p class="mt-4 text-sm leading-relaxed text-stone-300">
                Kelola presensi harian, pengajuan cuti, riwayat diklat, serta data induk pegawai KPI Pusat secara terintegrasi, akurat, dan transparan.
            </p>

            <dl class="mt-8 grid grid-cols-3 gap-4 border-t border-white/10 pt-6">
                <div class="transition-all hover:translate-y-[-2px] duration-300">
                    <dt class="text-[10px] font-semibold uppercase tracking-wider text-stone-400">Presensi</dt>
                    <dd class="mt-1 font-serif text-lg text-kpi-gold-soft font-medium">Digital</dd>
                </div>
                <div class="transition-all hover:translate-y-[-2px] duration-300">
                    <dt class="text-[10px] font-semibold uppercase tracking-wider text-stone-400">Persetujuan</dt>
                    <dd class="mt-1 font-serif text-lg text-kpi-gold-soft font-medium">Berjenjang</dd>
                </div>
                <div class="transition-all hover:translate-y-[-2px] duration-300">
                    <dt class="text-[10px] font-semibold uppercase tracking-wider text-stone-400">Arsip</dt>
                    <dd class="mt-1 font-serif text-lg text-kpi-gold-soft font-medium">Terpusat</dd>
                </div>
            </dl>
        </div>

        <p class="relative z-30 px-12 pb-10 text-xs text-stone-500 animate-fade-in-up" style="animation-delay: 400ms;">
            &copy; {{ date('Y') }} Komisi Penyiaran Indonesia Pusat. Internal Use Only.
        </p>
    </div>

    {{-- Form panel --}}
    <div class="relative flex flex-1 flex-col items-center justify-center px-6 py-12 bg-stone-50/50 dark:bg-kpi-dark-bg/20 sm:px-10">
        {{-- Faint Grid Background --}}
        <div class="pointer-events-none absolute inset-0 opacity-[0.02] dark:opacity-[0.03]"
             style="background-image: linear-gradient(to right, #6B6459 1px, transparent 1px), linear-gradient(to bottom, #6B6459 1px, transparent 1px); background-size: 32px 32px;"></div>

        <div class="w-full max-w-sm relative">
            {{-- Mobile branding --}}
            <div class="mb-8 flex flex-col items-center text-center lg:hidden animate-fade-in-up" style="animation-delay: 100ms;">
                <img src="{{ asset('logo-kpi.jpg') }}" alt="Logo KPI" class="mb-3 h-14 w-14 shrink-0 rounded-full object-cover">
                <h1 class="text-xl font-bold tracking-tight">SIMPEG-KPI</h1>
                <p class="text-xs text-kpi-gray">Sistem Informasi Kepegawaian — KPI Pusat</p>
            </div>

            {{-- Form container card --}}
            <div class="rounded-2xl glass-card-premium p-8 animate-fade-in-up"
                 style="animation-delay: 150ms;">
                 
                <div class="mb-6 hidden lg:block">
                    <p class="eyebrow">Selamat datang kembali</p>
                    <h2 class="mt-1 font-serif text-2xl font-semibold tracking-tight">Masuk ke akun Anda</h2>
                </div>

                @if (session('status'))
                    <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-400 animate-pulse-glow">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-400">
                        @foreach ($errors->all() as $error)
                            <p class="flex items-center gap-1.5"><span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ showPassword: false }">
                    @csrf
                    <div class="animate-fade-in-up" style="animation-delay: 200ms;">
                        <label class="label">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="input input-premium" placeholder="nama@kpi.go.id">
                    </div>
                    
                    <div class="animate-fade-in-up" style="animation-delay: 300ms;">
                        <label class="label">Kata Sandi</label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" name="password" required 
                                   class="input input-premium pr-10" placeholder="••••••••">
                            <button type="button" @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-stone-400 hover:text-kpi-red dark:hover:text-kpi-gold transition-colors focus:outline-none"
                                    aria-label="Tampilkan sandi">
                                {{-- Eye Open icon --}}
                                <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                {{-- Eye Closed icon --}}
                                <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between animate-fade-in-up" style="animation-delay: 400ms;">
                        <label class="flex items-center gap-2 text-sm text-kpi-gray cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="rounded border-stone-300 text-kpi-red focus:ring-kpi-red/40 dark:border-white/10 dark:bg-white/5">
                            Ingat saya di perangkat ini
                        </label>
                    </div>

                    <button type="submit" 
                            class="btn-premium w-full !py-3.5 tracking-wide text-white font-semibold active:scale-98 animate-fade-in-up"
                            style="animation-delay: 500ms;">
                        Masuk ke Aplikasi
                    </button>
                </form>
            </div>

            <p class="mt-8 text-center text-xs text-kpi-gray lg:hidden animate-fade-in-up" style="animation-delay: 600ms;">
                &copy; {{ date('Y') }} Komisi Penyiaran Indonesia Pusat. Internal Use Only.
            </p>
        </div>
    </div>
</div>
</body>
</html>

