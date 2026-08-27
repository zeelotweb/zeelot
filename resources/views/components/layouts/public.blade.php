<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-slate-950 text-white antialiased">
        <header class="border-b border-white/10">
            <div class="max-w-3xl mx-auto px-6 py-5">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2">
                    <x-app-logo-icon class="size-7 fill-current text-cyan-400" />
                    <span class="font-bold tracking-tight">{{ config('app.name', 'ZeelotWeb') }}</span>
                </a>
            </div>
        </header>

        <main class="max-w-3xl mx-auto px-6 py-12">
            {{ $slot }}
        </main>

        @fluxScripts
    </body>
</html>
