<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        @if (! config('app.registration_open') && ! $invitation)
            <flux:callout variant="warning" icon="lock-closed" heading="{{ __('Invite-only') }}">
                {{ __('Registration currently requires an invitation link. Ask your ZeelotWeb contact to send you one.') }}
            </flux:callout>
        @else
            @if ($invitation)
                <flux:callout variant="secondary" icon="envelope" heading="{{ __('You\'re invited') }}">
                    {{ __('Joining as :role.', ['role' => ucfirst(str_replace('_', ' ', $invitation->role))]) }}
                </flux:callout>
            @endif

            <a
                href="{{ route('auth.google.redirect') }}"
                class="inline-flex w-full items-center justify-center gap-3 rounded-lg border border-zinc-200 dark:border-white/10 px-4 py-2.5 text-sm font-semibold text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-white/5 transition"
            >
                <x-google-icon class="size-5" />
                {{ __('Continue with Google') }}
            </a>

            <flux:separator text="{{ __('or') }}" />

            <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
                @csrf

                @if ($invitation)
                    <input type="hidden" name="invitation" value="{{ $invitation->token }}">
                @endif

                <!-- Name -->
                <flux:input
                    name="name"
                    :label="__('Name')"
                    :value="old('name')"
                    type="text"
                    required
                    autofocus
                    autocomplete="name"
                    :placeholder="__('Full name')"
                />

                <!-- Email Address -->
                <flux:input
                    name="email"
                    :label="__('Email address')"
                    :value="old('email', $invitation->email ?? '')"
                    type="email"
                    required
                    :readonly="(bool) $invitation"
                    autocomplete="email"
                    placeholder="email@example.com"
                />

                <!-- Password -->
                <flux:input
                    name="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="new-password"
                    :placeholder="__('Password')"
                    viewable
                />

                <!-- Confirm Password -->
                <flux:input
                    name="password_confirmation"
                    :label="__('Confirm password')"
                    type="password"
                    required
                    autocomplete="new-password"
                    :placeholder="__('Confirm password')"
                    viewable
                />

                <div class="flex items-center justify-end">
                    <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                        {{ __('Create account') }}
                    </flux:button>
                </div>
            </form>

            <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
                <span>{{ __('Already have an account?') }}</span>
                <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
            </div>
        @endif
    </div>
</x-layouts.auth>
