<x-layouts::auth :title="__('Activate Your Account')">
    <div class="flex flex-col gap-6 text-center" x-data="{ cooldown: 0, timer: null, startCooldown() { this.cooldown = 60; this.timer = setInterval(() => { if (this.cooldown > 0) { this.cooldown--; } else { clearInterval(this.timer); } }, 1000); } }">
        {{-- Shield / Key Icon Header --}}
        <div class="mx-auto w-12 h-12 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>

        <div>
            <h1 class="text-2xl font-bold tracking-tight text-neutral-900">
                Activate Your Account
            </h1>
            <p class="mt-2 text-sm text-neutral-600 leading-relaxed">
                We sent a 6-digit activation code to:
            </p>
            <p class="mt-1 text-sm font-semibold text-neutral-900 bg-neutral-100 rounded-lg py-1.5 px-3 inline-block">
                {{ auth()->user()?->email }}
            </p>
            <p class="mt-3 text-xs text-neutral-500 leading-relaxed max-w-sm mx-auto">
                Please enter the code below to activate your account and start booking voluntary blood donation appointments.
            </p>
        </div>

        @if (session('status') === 'otp-sent' || session('status') === 'verification-link-sent')
            <x-alert variant="success" title="Code Sent">
                A fresh 6-digit activation code has been sent to your email.
            </x-alert>
        @endif

        {{-- OTP Verification Form --}}
        <form method="POST" action="{{ route('verification.otp.submit') }}" class="flex flex-col gap-4">
            @csrf

            <div class="space-y-1.5 text-left">
                <label for="code" class="block text-xs font-semibold uppercase tracking-wider text-neutral-700 text-center">
                    6-Digit Activation Code
                </label>
                <div class="relative max-w-xs mx-auto">
                    <input
                        type="text"
                        name="code"
                        id="code"
                        maxlength="6"
                        pattern="[0-9]{6}"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        required
                        autofocus
                        placeholder="••••••"
                        class="w-full text-center tracking-[0.5em] font-mono text-2xl font-bold py-3 px-4 rounded-xl border border-neutral-300 bg-white text-neutral-900 placeholder:text-neutral-400 placeholder:tracking-[0.2em] focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-hidden transition-all shadow-2xs"
                        style="color: #1F1A1A !important; -webkit-text-fill-color: #1F1A1A !important;"
                        value="{{ old('code') }}"
                    />
                </div>
                @error('code')
                    <p class="text-xs font-medium text-danger-600 text-center mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full max-w-xs mx-auto inline-flex items-center justify-center font-bold rounded-xl px-4 py-3 text-sm transition-colors duration-150 bg-primary-600 hover:bg-primary-700 text-white shadow-xs cursor-pointer focus:outline-hidden focus-visible:ring-2 focus-visible:ring-primary-600/30"
            >
                Activate Account &rarr;
            </button>
        </form>

        <div class="flex flex-col gap-3 pt-2 border-t border-neutral-200/80">
            <div class="text-xs text-neutral-500">
                Didn't receive the email?
            </div>
            <form method="POST" action="{{ route('verification.send') }}" x-on:submit="startCooldown()">
                @csrf
                <button
                    type="submit"
                    x-bind:disabled="cooldown > 0"
                    class="text-sm font-semibold text-primary-700 hover:text-primary-800 underline transition-colors disabled:text-neutral-400 disabled:no-underline disabled:cursor-not-allowed cursor-pointer"
                >
                    <span x-show="cooldown === 0">Resend 6-digit code</span>
                    <span x-show="cooldown > 0" x-cloak>Resend code in <span x-text="cooldown"></span>s</span>
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="pt-2">
                @csrf
                <button
                    type="submit"
                    class="text-xs font-medium text-neutral-500 hover:text-neutral-800 underline transition-colors cursor-pointer"
                    data-test="logout-button"
                >
                    Log out and continue later
                </button>
            </form>
        </div>
    </div>
</x-layouts::auth>
