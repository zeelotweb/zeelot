<div id="contact" class="w-full mb-16 bg-neutral-900 text-white rounded-xl p-10 max-w-3xl mx-auto">

    <h2 class="text-3xl font-bold mb-6">Start Your Project</h2>

    <p class="text-neutral-300 mb-8">
        Fill the form below and we’ll get back to you within 24 hours.  
        Let us know which package you’re interested in.
    </p>


@if (session()->has('success'))
    <p class="text-green-400 font-semibold mb-4">
        {{ session('success') }}
    </p>
@endif



    <form wire:submit.prevent="submit" class="flex flex-col gap-6">

        {{-- Name --}}
        <flux:field>
            <flux:label class="font-semibold text-neutral-200">Full Name</flux:label>
            <flux:input wire:model.defer="name" type="text" 
                        class="w-full px-4 py-2 rounded-lg text-black bg-neutral-100 border border-neutral-700" />
            <flux:error name="name" />
        </flux:field>

        {{-- Email --}}
        <flux:field>
            <flux:label class="font-semibold text-neutral-200">Email Address</flux:label>
            <flux:input wire:model.defer="email" type="email" 
                        class="w-full px-4 py-2 rounded-lg text-black bg-neutral-100 border border-neutral-700" />
            <flux:error name="email" />
        </flux:field>

        {{-- Package --}}
        <flux:field>
            <flux:label class="font-semibold text-neutral-200">Select Package</flux:label>
            <flux:select wire:model.defer="package" 
                         class="w-full px-4 py-2 rounded-lg text-black bg-neutral-100 border border-neutral-700">
                <option value="">-- Choose a Package --</option>
                <option value="fix_stabilize">Fix & Stabilize</option>
                <option value="build_finish">Build or Finish</option>
                <option value="ongoing_care">Ongoing Care</option>
            </flux:select>
            <flux:error name="package" />
        </flux:field>

        {{-- Message --}}
        <flux:field>
            <flux:label class="font-semibold text-neutral-200">Message (Optional)</flux:label>
            <flux:textarea wire:model.defer="message" rows="4" 
                            class="w-full px-4 py-2 rounded-lg text-black bg-neutral-100 border border-neutral-700" />
        </flux:field>

        {{-- Submit --}}
        <button type="submit"
                class="bg-rose-700 hover:bg-rose-800 transition text-white px-6 py-3 rounded-lg font-semibold w-max">
            Submit Inquiry
        </button>

    </form>

</div>
