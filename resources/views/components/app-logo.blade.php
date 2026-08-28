<div class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
    {{-- This badge's background is the inverse of the page theme (dark square in
         light mode, white square in dark mode), so the mark needs the opposite
         swap from everywhere else it appears. --}}
    <img src="{{ asset('logo/mark-dark-mode.png') }}" alt="ZeelotWeb" class="size-5 dark:hidden">
    <img src="{{ asset('logo/mark.png') }}" alt="ZeelotWeb" class="size-5 hidden dark:block">
</div>
<div class="ms-1 grid flex-1 text-start text-sm">
    <span class="mb-0.5 truncate leading-tight font-semibold">ZeelotWeb</span>
</div>
