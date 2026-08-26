<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        @if(auth()->user()?->isStaff())
            <a
                href="{{ route('admin.dashboard') }}"
                wire:navigate
                class="flex items-center justify-between gap-4 rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 px-6 py-4 hover:border-cyan-300 dark:hover:border-cyan-500/40 transition"
            >
                <div class="flex items-center gap-3">
                    <flux:icon.shield-check class="size-6 text-cyan-500" />
                    <div>
                        <div class="font-semibold text-neutral-900 dark:text-white">Admin Panel</div>
                        <div class="text-sm text-neutral-500 dark:text-neutral-400">Leads, projects, and team management.</div>
                    </div>
                </div>
                <flux:icon.arrow-right class="size-5 text-neutral-400" />
            </a>
        @endif

        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
    </div>
</x-layouts.app>
