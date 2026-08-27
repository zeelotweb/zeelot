<div id="contact" class="bg-slate-50 dark:bg-white/[0.03] rounded-3xl p-8 md:p-12 border border-slate-200 dark:border-white/10 backdrop-blur">
    @if($success)
        <div class="text-center py-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-cyan-100 dark:bg-cyan-500/20 text-cyan-600 dark:text-cyan-400 rounded-full mb-6">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Message Received</h3>
            <p class="text-slate-600 dark:text-slate-400 mb-6">I'll review your project details and get back to you within 24 hours.</p>
            <flux:button wire:click="resetForm" variant="ghost">Send Another Message</flux:button>
        </div>
    @else
        <form wire:submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2 mb-4">
                @if($isProBono)
                    <span class="inline-block mb-3 text-xs font-mono font-bold tracking-wide text-cyan-600 dark:text-cyan-400 uppercase bg-cyan-100 dark:bg-cyan-500/10 border border-cyan-300 dark:border-cyan-500/30 rounded-full px-3 py-1">Pro Bono Application</span>
                @endif
                <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">{{ $isProBono ? 'Apply for a Pro Bono Slot' : 'Start a Project' }}</h2>
                <p class="text-slate-600 dark:text-slate-400">
                    @if($isProBono)
                        Tell me about your business and what you need — slots are limited to 2 a month and reviewed case by case.
                    @else
                        Tell me about your vision and let's build the engine to power it.
                    @endif
                </p>
            </div>

            <div>
                <label class="flex items-center gap-1.5 text-sm font-semibold text-slate-600 dark:text-slate-400 mb-2">
                    Full Name
                    @auth <flux:icon.lock-closed class="size-3.5 text-slate-400" /> @endauth
                </label>
                <input
                    wire:model="name"
                    type="text"
                    @auth readonly @endauth
                    class="w-full border rounded-xl px-4 py-3 outline-none transition {{ auth()->check() ? 'bg-slate-100 dark:bg-slate-800/50 border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 cursor-not-allowed' : 'bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500' }}"
                >
                @auth
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">From your account. <flux:link :href="route('profile.edit')" wire:navigate class="text-cyan-600 dark:text-cyan-400">Edit profile</flux:link> to change it.</p>
                @endauth
                @error('name') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="flex items-center gap-1.5 text-sm font-semibold text-slate-600 dark:text-slate-400 mb-2">
                    Email Address
                    @auth <flux:icon.lock-closed class="size-3.5 text-slate-400" /> @endauth
                </label>
                <input
                    wire:model="email"
                    type="email"
                    @auth readonly @endauth
                    class="w-full border rounded-xl px-4 py-3 outline-none transition {{ auth()->check() ? 'bg-slate-100 dark:bg-slate-800/50 border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 cursor-not-allowed' : 'bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500' }}"
                >
                @auth
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">From your account.</p>
                @endauth
                @error('email') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            @if($isProBono)
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-600 dark:text-slate-400 mb-2">Business Name</label>
                    <input wire:model="company" type="text" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500 outline-none transition">
                </div>
            @else
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-600 dark:text-slate-400 mb-3">Select the solutions you need — mix and match</label>
                    <div class="grid sm:grid-cols-2 gap-3">
                        @foreach($this->packages() as $package)
                            <label class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition {{ in_array($package->id, $selectedPackageIds) ? 'border-cyan-500 bg-cyan-50 dark:bg-cyan-500/10' : 'border-slate-300 dark:border-slate-700 hover:border-slate-400 dark:hover:border-slate-600' }}">
                                <input type="checkbox" wire:model.live="selectedPackageIds" value="{{ $package->id }}" class="mt-1 accent-cyan-500">
                                <div>
                                    <div class="font-semibold text-slate-900 dark:text-white">{{ $package->name }}</div>
                                    @if($package->description)
                                        <div class="text-sm text-slate-500 dark:text-slate-400">{{ $package->description }}</div>
                                    @endif
                                    <div class="text-sm font-mono text-cyan-600 dark:text-cyan-400 mt-1">${{ number_format($package->price, 0) }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('selectedPackageIds') <span class="text-red-500 dark:text-red-400 text-xs mt-2 block">{{ $message }}</span> @enderror
                </div>

                @if(count($selectedPackageIds) > 0)
                    <div class="md:col-span-2 bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl p-5 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500 dark:text-slate-400">Subtotal</span>
                            <span class="font-mono text-slate-900 dark:text-white">${{ number_format($this->subtotal(), 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-cyan-600 dark:text-cyan-400">
                            <span>Default discount ({{ config('discounts.default_percentage') }}%)</span>
                            <span class="font-mono">-${{ number_format($this->defaultDiscountAmount(), 2) }}</span>
                        </div>

                        <div class="flex gap-2 pt-1">
                            <input wire:model="discountCodeInput" type="text" placeholder="Discount code (optional)" class="flex-1 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 text-sm uppercase text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-cyan-500">
                            <flux:button size="sm" type="button" wire:click="applyDiscountCode">Apply</flux:button>
                        </div>
                        @if($discountError)
                            <p class="text-red-500 dark:text-red-400 text-xs">{{ $discountError }}</p>
                        @endif
                        @if($appliedDiscount)
                            <div class="flex justify-between text-sm text-cyan-600 dark:text-cyan-400">
                                <span>Code applied</span>
                                <span class="font-mono">-${{ number_format($this->codeDiscountAmount(), 2) }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between text-base font-bold pt-3 border-t border-slate-200 dark:border-slate-700">
                            <span class="text-slate-900 dark:text-white">Total</span>
                            <span class="font-mono text-cyan-600 dark:text-cyan-400">${{ number_format($this->total(), 2) }}</span>
                        </div>
                    </div>
                @endif
            @endif

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-600 dark:text-slate-400 mb-2">{{ $isProBono ? 'Tell me about your business and what you need' : 'Tell me about the project' }}</label>
                <textarea wire:model="message" rows="4" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500 outline-none transition"></textarea>
                @error('message') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-2 text-right">
                <button type="submit" class="w-full md:w-auto bg-cyan-500 hover:bg-cyan-400 text-white font-bold px-12 py-4 rounded-xl transition shadow-lg shadow-cyan-500/30">
                    {{ $isProBono ? 'Submit Pro Bono Application' : 'Submit Discovery Form' }}
                </button>
            </div>
        </form>
    @endif
</div>
