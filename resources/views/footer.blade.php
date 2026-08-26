<!-- Footer Section -->
<footer class="bg-slate-50 text-slate-600 dark:bg-black dark:text-slate-400 py-16 w-screen border-t border-slate-200 dark:border-white/10">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">

            <!-- Brand Column -->
            <div class="col-span-1 md:col-span-2">
                <div class="text-2xl font-black tracking-tighter text-slate-900 dark:text-white mb-6">Zeelot<span class="text-cyan-500 dark:text-cyan-400">Web</span></div>
                <p class="text-slate-500 max-w-sm mb-6 leading-relaxed">
                    High-performance Laravel engineering for businesses that outgrew "off-the-shelf" solutions. We build scalable digital infrastructure.
                </p>
                <div class="flex items-center space-x-2 text-sm text-slate-500">
                    <svg xmlns="http://w3.org" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Based in Columbus, OH — Serving Clients Globally</span>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="text-slate-900 dark:text-white font-bold mb-6 uppercase tracking-wider text-sm">Capabilities</h4>
                <ul class="space-y-4 text-sm">
                    <li><a href="#" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition">Custom SaaS Dev</a></li>
                    <li><a href="#" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition">API Integrations</a></li>
                    <li><a href="#" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition">Database Optimization</a></li>
                    <li><a href="#" class="hover:text-cyan-600 dark:hover:text-cyan-400 transition">Legacy Migration</a></li>
                </ul>
            </div>

            <!-- Contact/Social -->
            <div>
                <h4 class="text-slate-900 dark:text-white font-bold mb-6 uppercase tracking-wider text-sm">Get in Touch</h4>
                <ul class="space-y-4 text-sm">
                    <li class="flex items-center space-x-3">
                        <span class="text-cyan-600 dark:text-cyan-400 font-semibold">Ready?</span>
                        <a href="#contact-wrap" class="hover:text-slate-900 dark:hover:text-white transition underline decoration-cyan-500 underline-offset-4">Start a Project</a>
                    </li>
                    <li><a href="mailto:hello@zeelotweb.com" class="hover:text-slate-900 dark:hover:text-white transition">hello@zeelotweb.com</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-slate-900 dark:hover:text-white transition">Customer Login</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="pt-8 border-t border-slate-200 dark:border-white/10 flex flex-col md:flex-row justify-between items-center text-xs text-slate-500">
            <p>&copy; {{ date('Y') }} ZeelotWeb. All rights reserved.</p>
            <div class="mt-4 md:mt-0 space-x-6 italic">
                Built with Laravel, Livewire, and Grit.
            </div>
        </div>
    </div>
</footer>
