<div class="w-full mb-10">

    <div class="relative h-fit rounded-xl overflow-hidden">

        {{-- Background Image --}}
        <img 
            src="{{ asset('storage/siteicons/zltcc.jpg') }}" 
            alt="ZeelotWeb background"
            class="absolute inset-0 w-full h-full object-cover opacity-50"
        />

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-black/40"></div>

        {{-- Content --}}
        <div class="relative p-10 flex flex-col justify-center max-w-2xl">

            <h1 class="text-4xl font-bold mb-4 text-white">
                Stay Informed
            </h1>

            <p class="text-stone-200 mb-6 leading-relaxed">
                Practical updates on web platforms, fixes, launches, and real-world lessons.
                No hype. No spam. Just things worth knowing.
            </p>

            {{-- Email Field --}}
            <flux:field class="mb-6">
                <flux:label class="text-stone-100 font-semibold">
                    Email address
                </flux:label>

                <flux:input 
                    wire:model.defer="email" 
                    type="email"
                    placeholder="you@domain.com"
                    class="text-black bg-stone-100 border border-stone-300 rounded-lg"
                />

                <flux:error name="email" />
            </flux:field>

            {{-- CTA --}}
            <button
                wire:click="subscribe"
                class="bg-rose-700 hover:bg-rose-800 transition text-white px-6 py-3 rounded-lg max-w-max font-semibold flex items-center gap-2"
            >
                <flux:icon.envelope variant="mini" />
                Get Updates
            </button>

            <p class="text-xs text-stone-300 mt-4">
                Unsubscribe anytime. Zero nonsense.
            </p>

        </div>

    </div>

</div>
