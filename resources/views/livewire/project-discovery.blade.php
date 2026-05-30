<div id="contact" class="bg-slate-900 rounded-3xl p-8 md:p-12 shadow-2xl border border-slate-800">
    @if($success)
        <div class="text-center py-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-500/20 text-emerald-500 rounded-full mb-6">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Message Received</h3>
            <p class="text-slate-400">I'll review your project details and get back to you within 24 hours.</p>
        </div>
    @else
        <form wire:submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2 mb-4">
                <h2 class="text-3xl font-bold text-white mb-2">Start a Project</h2>
                <p class="text-slate-400">Tell me about your vision and let's build the engine to power it.</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-400 mb-2">Full Name</label>
                <input wire:model="name" type="text" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 outline-none transition">
                @error('name') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-400 mb-2">Email Address</label>
                <input wire:model="email" type="email" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 outline-none transition">
                @error('email') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-400 mb-2">Project Budget (Starting at)</label>
                <select wire:model="budget" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 outline-none transition">
                    <option value="1500">$1,500 - $3,000</option>
                    <option value="5000">$5,000 - $10,000</option>
                    <option value="10000">$10,000+</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-400 mb-2">Tell me about the project</label>
                <textarea wire:model="message" rows="4" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 outline-none transition"></textarea>
                @error('message') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-2 text-right">
                <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold px-12 py-4 rounded-xl transition shadow-lg shadow-blue-500/20">
                    Submit Discovery Form
                </button>
            </div>
        </form>
    @endif
</div>
