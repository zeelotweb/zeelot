<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>ZeelotWeb | High-Performance Laravel Development</title>

        <link rel="icon" href="/favicon.png" type="image/png">
        <link rel="apple-touch-icon" href="/favicon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800,900|jetbrains-mono:400,500,600" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance

        <style>
            .grid-pattern {
                background-image:
                    linear-gradient(to right, rgba(0,0,0,0.04) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(0,0,0,0.04) 1px, transparent 1px);
                background-size: 48px 48px;
            }
            .dark .grid-pattern {
                background-image:
                    linear-gradient(to right, rgba(255,255,255,0.05) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(255,255,255,0.05) 1px, transparent 1px);
            }
        </style>
    </head>
    <body class="bg-white text-slate-900 dark:bg-slate-950 dark:text-white antialiased font-sans">

<!-- Navigation -->
<nav class="sticky top-0 z-50 backdrop-blur-md bg-white/80 border-b border-slate-200 dark:bg-slate-950/80 dark:border-white/10">
    <div class="container mx-auto px-4 sm:px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-2xl font-black tracking-tighter text-slate-900 dark:text-white">
                <span class="flex rounded-md overflow-hidden ring-1 ring-slate-200 dark:ring-white/10 p-1 bg-white"><x-app-logo-icon /></span>
                <span>Zeelot<span class="text-cyan-500 dark:text-cyan-400">Web</span></span>
            </div>
            <div class="hidden md:flex items-center gap-8 font-medium text-slate-600 dark:text-slate-400 text-sm">
                <a href="#work" class="hover:text-slate-900 dark:hover:text-white transition">Our Work</a>
                <a href="#stack" class="hover:text-slate-900 dark:hover:text-white transition">Stack</a>
                <a href="#process" class="hover:text-slate-900 dark:hover:text-white transition">Process</a>
            </div>
            <div class="flex items-center gap-3">
                <button
                    type="button"
                    x-data
                    x-on:click="$flux.appearance = document.documentElement.classList.contains('dark') ? 'light' : 'dark'"
                    class="flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 text-slate-600 hover:text-slate-900 hover:border-slate-300 dark:border-white/10 dark:text-slate-400 dark:hover:text-white dark:hover:border-white/20 transition"
                    aria-label="Toggle dark mode"
                >
                    <flux:icon.sun variant="micro" class="size-4 dark:hidden" />
                    <flux:icon.moon variant="micro" class="size-4 hidden dark:block" />
                </button>
                <div class="hidden md:flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition">
                            Customer Login
                        </a>
                    @endauth
                    <a href="#contact-wrap" x-data x-on:click="Livewire.dispatch('resetDiscoveryForm')" class="bg-cyan-500 text-white px-6 py-2.5 rounded-lg font-semibold text-sm hover:bg-cyan-400 hover:shadow-[0_0_25px_rgba(6,182,212,0.5)] transition-all">
                        Start a Project
                    </a>
                </div>
            </div>
        </div>

        <!-- Mobile-only actions row -->
        <div class="flex md:hidden items-center gap-3 mt-3 pt-3 border-t border-slate-100 dark:border-white/5">
            @auth
                <a href="{{ route('dashboard') }}" class="flex-1 text-center text-sm font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition py-2">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="flex-1 text-center text-sm font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition py-2">
                    Customer Login
                </a>
            @endauth
            <a href="#contact-wrap" x-data x-on:click="Livewire.dispatch('resetDiscoveryForm')" class="flex-1 text-center bg-cyan-500 text-white px-4 py-2.5 rounded-lg font-semibold text-sm hover:bg-cyan-400 transition-all">
                Start a Project
            </a>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<header class="relative overflow-hidden bg-white dark:bg-slate-950 pt-24 pb-32 grid-pattern">
    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-white/40 to-white dark:via-slate-950/40 dark:to-slate-950"></div>
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[40rem] h-[40rem] rounded-full bg-cyan-400/20 dark:bg-cyan-600/20 blur-[100px]"></div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-4xl">
            <span class="inline-flex items-center gap-2 py-1.5 px-4 mb-8 text-xs font-mono font-medium tracking-wide text-cyan-700 dark:text-cyan-300 uppercase bg-cyan-50 dark:bg-cyan-500/10 border border-cyan-200 dark:border-cyan-500/30 rounded-full">
                &lt;/&gt; Custom Laravel Solutions
            </span>
            <h1 class="text-5xl md:text-7xl font-black leading-[1.05] mb-8 tracking-tight text-slate-900 dark:text-white">
                We build the <span class="text-cyan-600 dark:text-cyan-400">engines</span> that power modern business.
            </h1>
            <p class="text-xl text-slate-600 dark:text-slate-400 mb-10 max-w-2xl leading-relaxed">
                Stop fighting with off-the-shelf software. ZeelotWeb engineers custom, scalable web applications designed to solve your specific bottlenecks and drive revenue.
            </p>

            <div class="flex flex-col sm:flex-row gap-4">
                <a href="#work" class="bg-cyan-500 text-white px-10 py-4 rounded-xl font-bold text-center hover:bg-cyan-400 hover:shadow-[0_0_30px_rgba(6,182,212,0.4)] transition-all">
                    View Case Studies
                </a>
                <a href="#stack" class="border border-slate-300 dark:border-white/15 px-10 py-4 rounded-xl font-bold text-center text-slate-700 dark:text-slate-300 hover:border-slate-400 hover:text-slate-900 dark:hover:border-white/30 dark:hover:text-white transition-all">
                    Our Tech Stack
                </a>
            </div>

        </div>
    </div>
</header>

<!-- Trusted By Section -->
<section class="py-16 bg-slate-50 dark:bg-slate-900/30 border-y border-slate-100 dark:border-white/5 overflow-hidden">
    <div class="container mx-auto px-6">
        <p class="text-center text-xs font-mono font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-10">
            Trusted by teams building the next big thing
        </p>

        <div class="flex flex-wrap items-center justify-center gap-6">
            <a href="https://ValueAfrik.com" target="_blank" class="group flex items-center gap-4 px-6 py-5 bg-white dark:bg-white/[0.04] rounded-2xl border border-slate-200 dark:border-white/10 shadow-sm hover:shadow-xl hover:shadow-cyan-100 dark:hover:shadow-cyan-500/10 hover:-translate-y-1.5 hover:rotate-1 hover:border-cyan-300 dark:hover:border-cyan-500/40 transition-all duration-300">
                <span class="flex items-center justify-center w-16 h-16 rounded-xl bg-white shrink-0 border border-slate-100 overflow-hidden group-hover:scale-105 transition-transform duration-300">
                    <img src="{{ asset('valueafrik.jpeg') }}" alt="ValueAfrik" class="h-12 w-12 object-contain">
                </span>
                <span class="text-left">
                    <span class="block font-black text-lg text-slate-900 dark:text-white group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition">ValueAfrik</span>
                    <span class="block text-xs font-mono uppercase tracking-wide text-slate-400 dark:text-slate-500">Social Ecosystem</span>
                </span>
            </a>

            <a href="https://Tiecnoc.com" target="_blank" class="group flex items-center gap-4 px-6 py-5 bg-white dark:bg-white/[0.04] rounded-2xl border border-slate-200 dark:border-white/10 shadow-sm hover:shadow-xl hover:shadow-cyan-100 dark:hover:shadow-cyan-500/10 hover:-translate-y-1.5 hover:-rotate-1 hover:border-cyan-300 dark:hover:border-cyan-500/40 transition-all duration-300">
                <span class="flex items-center justify-center w-16 h-16 rounded-xl bg-white shrink-0 border border-slate-100 overflow-hidden group-hover:scale-105 transition-transform duration-300">
                    <img src="{{ asset('tiecnoc.png') }}" alt="Tiecnoc" class="h-12 w-12 object-contain">
                </span>
                <span class="text-left">
                    <span class="block font-black text-lg text-slate-900 dark:text-white group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition">Tiecnoc</span>
                    <span class="block text-xs font-mono uppercase tracking-wide text-slate-400 dark:text-slate-500">E-Commerce</span>
                </span>
            </a>

            <a href="https://Taongaf.com" target="_blank" class="group flex items-center gap-4 px-6 py-5 bg-white dark:bg-white/[0.04] rounded-2xl border border-slate-200 dark:border-white/10 shadow-sm hover:shadow-xl hover:shadow-cyan-100 dark:hover:shadow-cyan-500/10 hover:-translate-y-1.5 hover:rotate-1 hover:border-cyan-300 dark:hover:border-cyan-500/40 transition-all duration-300">
                <span class="flex items-center justify-center w-16 h-16 rounded-xl bg-white shrink-0 border border-slate-100 overflow-hidden group-hover:scale-105 transition-transform duration-300">
                    <img src="{{ asset('taongaf.png') }}" alt="Taongaf" class="h-16 w-16 object-contain">
                </span>
                <span class="text-left">
                    <span class="block font-black text-lg text-slate-900 dark:text-white group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition">Taongaf</span>
                    <span class="block text-xs font-mono uppercase tracking-wide text-slate-400 dark:text-slate-500">SaaS / Marketplace</span>
                </span>
            </a>
        </div>
    </div>
</section>

<!-- Pro Bono Banner -->
<section class="bg-cyan-50 dark:bg-cyan-500/10 border-y border-cyan-200 dark:border-cyan-500/20">
    <div class="container mx-auto px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left">
        <p class="text-sm font-medium text-cyan-900 dark:text-cyan-200">
            <span class="font-bold">2 pro bono builds a month</span> — for small business owners just starting out. You only cover hosting.
        </p>
        <a href="#pro-bono" class="shrink-0 text-sm font-bold text-cyan-700 dark:text-cyan-300 hover:text-cyan-900 dark:hover:text-white underline underline-offset-4">
            See if you qualify →
        </a>
    </div>
</section>

<!-- Portfolio Section -->
<section id="work" class="py-24 bg-white dark:bg-slate-950 border-t border-slate-100 dark:border-white/5">
    <div class="container mx-auto px-6 text-center mb-16">
        <span class="inline-block py-1.5 px-4 mb-4 text-xs font-mono font-medium tracking-wide text-cyan-700 dark:text-cyan-300 uppercase bg-cyan-50 dark:bg-cyan-500/10 border border-cyan-200 dark:border-cyan-500/30 rounded-full">Selected Work</span>
        <h2 class="text-3xl md:text-5xl font-black text-slate-900 dark:text-white mb-4 tracking-tight">Shipped. Scaled. Proven.</h2>
        <p class="text-slate-600 dark:text-slate-400 max-w-2xl mx-auto text-lg">
            High-performance web applications built from the ground up to solve complex business logic.
        </p>
    </div>

    <div class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

        <!-- Case Study 1: ValueAfrik -->
        <div class="group relative bg-slate-50 dark:bg-white/[0.03] rounded-2xl p-8 border border-slate-200 dark:border-white/10 hover:border-cyan-400 dark:hover:border-cyan-500/40 hover:bg-white dark:hover:bg-white/[0.05] hover:-translate-y-1 hover:shadow-xl hover:shadow-cyan-100 dark:hover:shadow-[0_0_40px_rgba(6,182,212,0.15)] transition-all duration-300">
            <div class="font-mono text-cyan-600 dark:text-cyan-400 text-xs uppercase tracking-widest mb-4">Social Ecosystem</div>
            <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white mb-3">ValueAfrik.com</h3>
            <p class="text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">
                A custom social media engine designed for high-concurrency and global connectivity. Built for scale.
            </p>
            <ul class="flex flex-wrap gap-2 mb-8">
                <li class="px-3 py-1 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded text-xs font-mono text-slate-600 dark:text-slate-400">Laravel</li>
                <li class="px-3 py-1 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded text-xs font-mono text-slate-600 dark:text-slate-400">MySQL</li>
                <li class="px-3 py-1 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded text-xs font-mono text-slate-600 dark:text-slate-400">Real-time Data</li>
            </ul>
            <a href="https://ValueAfrik.com" target="_blank" class="inline-flex items-center font-bold text-cyan-600 dark:text-cyan-400 hover:text-cyan-700 dark:hover:text-cyan-300">
                View Live Site
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>

        <!-- Case Study 2: Tiecnoc -->
        <div class="group relative bg-slate-50 dark:bg-white/[0.03] rounded-2xl p-8 border border-slate-200 dark:border-white/10 hover:border-cyan-400 dark:hover:border-cyan-500/40 hover:bg-white dark:hover:bg-white/[0.05] hover:-translate-y-1 hover:shadow-xl hover:shadow-cyan-100 dark:hover:shadow-[0_0_40px_rgba(6,182,212,0.15)] transition-all duration-300">
            <div class="font-mono text-cyan-600 dark:text-cyan-400 text-xs uppercase tracking-widest mb-4">E-Commerce</div>
            <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white mb-3">Tiecnoc.com</h3>
            <p class="text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">
                A professional clothing line marketplace. Streamlined checkout and custom inventory management.
            </p>
            <ul class="flex flex-wrap gap-2 mb-8">
                <li class="px-3 py-1 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded text-xs font-mono text-slate-600 dark:text-slate-400">Stripe Integration</li>
                <li class="px-3 py-1 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded text-xs font-mono text-slate-600 dark:text-slate-400">Custom CMS</li>
            </ul>
            <a href="https://Tiecnoc.com" target="_blank" class="inline-flex items-center font-bold text-cyan-600 dark:text-cyan-400 hover:text-cyan-700 dark:hover:text-cyan-300">
                View Live Site
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>

        <!-- Case Study 3: Taongaf -->
        <div class="group relative bg-slate-50 dark:bg-white/[0.03] rounded-2xl p-8 border border-slate-200 dark:border-white/10 hover:border-cyan-400 dark:hover:border-cyan-500/40 hover:bg-white dark:hover:bg-white/[0.05] hover:-translate-y-1 hover:shadow-xl hover:shadow-cyan-100 dark:hover:shadow-[0_0_40px_rgba(6,182,212,0.15)] transition-all duration-300">
            <div class="font-mono text-cyan-600 dark:text-cyan-400 text-xs uppercase tracking-widest mb-4">SaaS / Marketplace</div>
            <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white mb-3">Taongaf.com</h3>
            <p class="text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">
                A writer's marketplace facilitating content creation and publishing workflows.
            </p>
            <ul class="flex flex-wrap gap-2 mb-8">
                <li class="px-3 py-1 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded text-xs font-mono text-slate-600 dark:text-slate-400">User Auth</li>
                <li class="px-3 py-1 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded text-xs font-mono text-slate-600 dark:text-slate-400">Asset Management</li>
            </ul>
            <a href="https://Taongaf.com" target="_blank" class="inline-flex items-center font-bold text-cyan-600 dark:text-cyan-400 hover:text-cyan-700 dark:hover:text-cyan-300">
                View Live Site
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>

    </div>
</section>

<!-- Tech Stack Section -->
<section id="stack" class="py-24 bg-slate-50 dark:bg-slate-900/40 border-y border-slate-100 dark:border-white/5 relative overflow-hidden">
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[36rem] h-[36rem] rounded-full bg-cyan-300/10 dark:bg-cyan-600/10 blur-[120px]"></div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex flex-col md:flex-row items-center gap-16">

            <!-- Text Content -->
            <div class="md:w-1/2">
                <h2 class="text-4xl font-black text-slate-900 dark:text-white mb-6 leading-tight tracking-tight">
                    The modern stack for <span class="text-cyan-600 dark:text-cyan-400">ambitious</span> applications.
                </h2>
                <p class="text-lg text-slate-600 dark:text-slate-400 mb-8 leading-relaxed">
                    We don't just "build websites." We engineer resilient digital products using the same tools used by industry leaders like Twitch, Disney, and The New York Times.
                </p>

                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-lg flex items-center justify-center font-mono font-bold text-cyan-600 dark:text-cyan-400">PHP</div>
                        <div>
                            <h4 class="font-bold text-slate-900 dark:text-white">Laravel Framework</h4>
                            <p class="text-sm text-slate-500">The most powerful PHP ecosystem for secure, rapid development.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-lg flex items-center justify-center font-mono font-bold text-cyan-600 dark:text-cyan-400">LW</div>
                        <div>
                            <h4 class="font-bold text-slate-900 dark:text-white">Livewire & Flux</h4>
                            <p class="text-sm text-slate-500">Interactive, reactive UIs without the complexity of heavy JavaScript frameworks.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-lg flex items-center justify-center font-mono font-bold text-cyan-600 dark:text-cyan-400">SQL</div>
                        <div>
                            <h4 class="font-bold text-slate-900 dark:text-white">MySQL & Eloquent</h4>
                            <p class="text-sm text-slate-500">Relational data structures optimized for speed and strict integrity.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visual Graphic / Badge Cloud -->
            <div class="md:w-1/2 grid grid-cols-2 gap-4">
                <div class="bg-white dark:bg-white/[0.03] p-8 rounded-2xl border border-slate-200 dark:border-white/10 text-center">
                    <div class="text-3xl font-black text-slate-900 dark:text-white mb-1 leading-none">99.9%</div>
                    <div class="text-xs uppercase tracking-widest text-slate-500 font-mono">Uptime Focused</div>
                </div>
                <div class="bg-cyan-500 p-8 rounded-2xl shadow-[0_0_40px_rgba(6,182,212,0.35)] text-center text-white">
                    <div class="text-3xl font-black mb-1 leading-none">10x</div>
                    <div class="text-xs uppercase tracking-widest text-cyan-100 font-mono">Faster MVP</div>
                </div>
                <div class="bg-white dark:bg-white/[0.03] border border-slate-200 dark:border-white/10 p-8 rounded-2xl text-center text-slate-900 dark:text-white col-span-2">
                    <div class="text-xl font-bold mb-1">Tailwind CSS</div>
                    <div class="text-xs uppercase tracking-widest text-slate-500 font-mono">Utility-First Responsive Design</div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Process Section -->
<section id="process" class="py-24 bg-white dark:bg-slate-950 overflow-hidden">
    <div class="container mx-auto px-6">
        <div class="max-w-3xl mb-16">
            <h2 class="text-4xl font-black text-slate-900 dark:text-white mb-6 tracking-tight">How We <span class="text-cyan-600 dark:text-cyan-400">Build</span>.</h2>
            <p class="text-lg text-slate-600 dark:text-slate-400">
                A high-performance application requires a high-performance process. We don't guess; we engineer.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 relative">
            <!-- Connecting Line (Desktop Only) -->
            <div class="hidden md:block absolute top-8 left-0 w-full h-px bg-slate-200 dark:bg-white/10 z-0"></div>

            <!-- Step 1 -->
            <div class="relative z-10 group">
                <div class="w-16 h-16 bg-cyan-500 text-white rounded-2xl flex items-center justify-center text-2xl font-black mb-8 shadow-[0_0_30px_rgba(6,182,212,0.4)] group-hover:scale-110 transition duration-300">
                    01
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Discovery & Logic</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    We map out every user flow and database relationship before writing a single line of code. No surprises, just clarity.
                </p>
            </div>

            <!-- Step 2 -->
            <div class="relative z-10 group">
                <div class="w-16 h-16 bg-slate-100 border border-slate-200 dark:bg-white/10 dark:border-white/15 text-slate-900 dark:text-white rounded-2xl flex items-center justify-center text-2xl font-black mb-8 group-hover:scale-110 transition duration-300">
                    02
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Rapid Development</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Using the Laravel/TALL stack, we build reactive, secure, and scalable MVPs in weeks, not months.
                </p>
            </div>

            <!-- Step 3 -->
            <div class="relative z-10 group">
                <div class="w-16 h-16 bg-slate-100 border border-slate-200 dark:bg-white/10 dark:border-white/15 text-slate-900 dark:text-white rounded-2xl flex items-center justify-center text-2xl font-black mb-8 group-hover:scale-110 transition duration-300">
                    03
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Deployment & Scale</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    We launch on production-grade infrastructure with automated backups and sub-second load times.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Pro Bono Section -->
<section id="pro-bono" class="py-24 bg-slate-50 dark:bg-slate-900/40 border-y border-slate-100 dark:border-white/5">
    <div class="container mx-auto px-6">
        <div class="max-w-3xl mx-auto text-center mb-14">
            <span class="inline-block py-1.5 px-4 mb-4 text-xs font-mono font-medium tracking-wide text-cyan-700 dark:text-cyan-300 uppercase bg-cyan-50 dark:bg-cyan-500/10 border border-cyan-200 dark:border-cyan-500/30 rounded-full">Community Slots</span>
            <h2 class="text-4xl font-black text-slate-900 dark:text-white mb-6 tracking-tight">2 pro bono builds, every month.</h2>
            <p class="text-lg text-slate-600 dark:text-slate-400">
                Reserved for small business owners just starting out who can't yet afford a custom build. You cover hosting — we cover the engineering.
            </p>
        </div>

        <div class="max-w-2xl mx-auto bg-white dark:bg-white/[0.03] rounded-2xl border border-slate-200 dark:border-white/10 p-8 md:p-10">
            <h3 class="font-bold text-slate-900 dark:text-white mb-4">Who qualifies</h3>
            <ul class="space-y-3 mb-8 text-slate-600 dark:text-slate-400">
                <li class="flex gap-3"><span class="text-cyan-500 font-bold">✓</span> Early-stage or pre-revenue small business</li>
                <li class="flex gap-3"><span class="text-cyan-500 font-bold">✓</span> Needs a simple, focused website or web app</li>
                <li class="flex gap-3"><span class="text-cyan-500 font-bold">✓</span> Can commit to covering hosting costs only</li>
            </ul>
            <a href="?intent=probono#contact-wrap" class="inline-flex w-full sm:w-auto justify-center bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-8 py-3.5 rounded-xl font-bold hover:bg-slate-800 dark:hover:bg-slate-100 transition">
                Apply for a Pro Bono Slot
            </a>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section id="contact-wrap" class="py-24 bg-white dark:bg-slate-950 border-t border-slate-100 dark:border-white/5 relative overflow-hidden">
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[36rem] h-[36rem] rounded-full bg-cyan-300/10 dark:bg-cyan-600/15 blur-[120px]"></div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="text-center mb-12">
            <span class="inline-block py-1.5 px-4 mb-4 text-xs font-mono font-medium tracking-wide text-cyan-700 dark:text-cyan-300 uppercase bg-cyan-50 dark:bg-cyan-500/10 border border-cyan-200 dark:border-cyan-500/30 rounded-full">Let's Talk</span>
            <h2 class="text-4xl md:text-5xl font-black text-slate-900 dark:text-white mb-4 tracking-tight">Let's build your engine.</h2>
            <p class="text-slate-600 dark:text-slate-400 max-w-xl mx-auto text-lg">Tell us about your project and we'll get back to you within 24 hours.</p>
        </div>

        <div class="max-w-3xl mx-auto">
            <livewire:project-discovery />
        </div>
    </div>
</section>
    </body>

    @include('footer')

@fluxScripts
</html>
