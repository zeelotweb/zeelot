<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <div>
            <flux:heading size="xl">Welcome back, {{ explode(' ', auth()->user()->name)[0] }}.</flux:heading>
            <flux:subheading>Here's what's happening with your account.</flux:subheading>
        </div>

        @if(auth()->user()?->isStaff())
            <a
                href="{{ route('admin.dashboard') }}"
                wire:navigate
                class="group flex items-center justify-between gap-4 rounded-2xl border border-cyan-200 dark:border-cyan-500/30 bg-cyan-50 dark:bg-cyan-500/10 px-6 py-5 hover:border-cyan-400 dark:hover:border-cyan-400/60 hover:shadow-lg hover:shadow-cyan-100 dark:hover:shadow-cyan-500/10 transition-all"
            >
                <div class="flex items-center gap-4">
                    <span class="flex items-center justify-center w-11 h-11 rounded-xl bg-cyan-500 text-white shadow-lg shadow-cyan-500/30 group-hover:scale-105 transition-transform">
                        <flux:icon.shield-check class="size-6" />
                    </span>
                    <div>
                        <div class="font-black text-slate-900 dark:text-white">Admin Panel</div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">Leads, projects, and team management.</div>
                    </div>
                </div>
                <flux:icon.arrow-right class="size-5 text-cyan-600 dark:text-cyan-400 group-hover:translate-x-1 transition-transform" />
            </a>
        @endif

        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <flux:card class="space-y-1">
                <span class="text-xs font-mono uppercase tracking-widest text-slate-400 dark:text-slate-500">Projects</span>
                <div class="text-3xl font-black text-slate-900 dark:text-white">{{ auth()->user()->projects()->count() }}</div>
                <div class="flex items-center gap-3">
                    <flux:link :href="route('portal.projects.index')" wire:navigate class="text-sm">View all &rarr;</flux:link>
                    <flux:modal.trigger name="start-project">
                        <button type="button" x-data x-on:click="Livewire.dispatch('resetDiscoveryForm')" class="text-sm text-cyan-600 dark:text-cyan-400 hover:underline">+ New</button>
                    </flux:modal.trigger>
                </div>
            </flux:card>

            <flux:card class="space-y-1">
                <span class="text-xs font-mono uppercase tracking-widest text-slate-400 dark:text-slate-500">Account</span>
                <div class="text-3xl font-black text-slate-900 dark:text-white">{{ ucwords(str_replace('_', ' ', auth()->user()->role)) }}</div>
                <flux:link :href="route('profile.edit')" wire:navigate class="text-sm">Edit profile &rarr;</flux:link>
            </flux:card>

            <flux:card class="space-y-1">
                <span class="text-xs font-mono uppercase tracking-widest text-slate-400 dark:text-slate-500">Support</span>
                <div class="text-lg font-bold text-slate-900 dark:text-white">Need help?</div>
                <a href="mailto:hello@zeelotweb.com" class="text-sm text-cyan-600 dark:text-cyan-400 hover:underline">hello@zeelotweb.com &rarr;</a>
            </flux:card>
        </div>

        <div class="relative flex-1 rounded-2xl border border-slate-200 dark:border-neutral-700 overflow-hidden">
            @php $recentProjects = auth()->user()->projects()->latest()->limit(5)->get(); @endphp

            @if($recentProjects->isEmpty())
                <div class="flex flex-col items-center justify-center h-full py-16 px-6 text-center">
                    <span class="flex items-center justify-center w-14 h-14 rounded-2xl bg-cyan-50 dark:bg-cyan-500/10 text-cyan-500 mb-4">
                        <flux:icon.folder class="size-7" />
                    </span>
                    <flux:heading size="lg">No projects yet</flux:heading>
                    <flux:subheading class="max-w-sm mt-1">
                        Once your project kicks off, you'll track milestones and payments right here.
                    </flux:subheading>
                    <flux:modal.trigger name="start-project">
                        <flux:button variant="primary" x-data x-on:click="Livewire.dispatch('resetDiscoveryForm')" class="mt-6">Start a New Project</flux:button>
                    </flux:modal.trigger>
                </div>
            @else
                <div class="p-6 space-y-3">
                    <flux:heading size="lg" class="mb-2">Recent Projects</flux:heading>
                    @foreach($recentProjects as $project)
                        <a
                            href="{{ route('portal.projects.show', $project) }}"
                            wire:navigate
                            class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 dark:border-white/10 px-5 py-4 hover:border-cyan-300 dark:hover:border-cyan-500/40 transition"
                        >
                            <div>
                                <div class="font-bold text-slate-900 dark:text-white">{{ $project->name }}</div>
                                <div class="text-sm text-slate-500 dark:text-slate-400">${{ number_format($project->paidAmount(), 2) }} paid of ${{ number_format($project->totalAmount(), 2) }}</div>
                            </div>
                            <flux:badge :color="match($project->status) {
                                'active' => 'green',
                                'on_hold' => 'amber',
                                'completed' => 'blue',
                                'cancelled' => 'red',
                                default => 'zinc',
                            }">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</flux:badge>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <flux:modal name="start-project" class="md:w-[40rem]">
            <div class="mb-4">
                <flux:heading size="lg">Start a New Project</flux:heading>
                <flux:subheading>Tell us what you have in mind and we'll follow up within 24 hours.</flux:subheading>
            </div>
            <livewire:project-discovery />
        </flux:modal>
    </div>
</x-layouts.app>
