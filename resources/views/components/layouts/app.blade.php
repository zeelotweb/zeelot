@if(request()->routeIs('admin.*'))
    <x-layouts.admin.sidebar :title="$title ?? null">
        <flux:main>
            {{ $slot }}
        </flux:main>
    </x-layouts.admin.sidebar>
@elseif(request()->routeIs('quotes.*'))
    <x-layouts.public :title="$title ?? null">
        {{ $slot }}
    </x-layouts.public>
@else
    <x-layouts.app.sidebar :title="$title ?? null">
        <flux:main>
            {{ $slot }}
        </flux:main>
    </x-layouts.app.sidebar>
@endif
