<x-layouts::auth :title="__('Reset Password')">
    <div class="flex flex-col gap-6" x-data="{ cooldown: 0, timer: null, startCooldown() { this.cooldown = 60; this.timer = setInterval(() => { if (this.cooldown > 0) { this.cooldown--; } else { clearInterval(this.timer); } }, 1000); } }">
        <x-auth-header :title="__('Set New Password')" :description="__('Enter the 6-digit code sent to your email and your new password')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.reset.otp.submit') }}" class="flex flex-col gap-4">
            @csrf

            <!-- Email Address -->
            <div class="space-y-1.5 text-left">
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-neutral-700">
                    Email address
                </label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    required
                    readonly
                    value="{{ old('email', $email) }}"
                    class="w-full py-2.5 px-3.5 rounded-xl border border-neutral-200 bg-neutral-100 text-neutral-800 text-sm cursor-not-allowed shadow-2xs"
                    style="color: #1F1A1A !important; -webkit-text-fill-color: #1F1A1A !important;"
                />
                @error('email')
                    <p class="text-xs font-medium text-danger-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 6-Digit Code -->
            <div class="space-y-1.5 text-left">
                <label for="code" class="block text-xs font-semibold uppercase tracking-wider text-neutral-700">
                    6-Digit Reset Code
                </label>
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
                    value="{{ old('code') }}"
                    class="w-full text-center tracking-[0.5em] font-mono text-xl font-bold py-2.5 px-4 rounded-xl border border-neutral-300 bg-white text-neutral-900 placeholder:text-neutral-400 focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-hidden transition-all shadow-2xs"
                    style="color: #1F1A1A !important; -webkit-text-fill-color: #1F1A1A !important;"
                />
                @error('code')
                    <p class="text-xs font-medium text-danger-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- New Password -->
            <div class="space-y-1.5 text-left">
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-neutral-700">
                    New Password
                </label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    required
                    placeholder="••••••••"
                    autocomplete="new-password"
                    class="w-full py-2.5 px-3.5 rounded-xl border border-neutral-300 bg-white text-neutral-900 placeholder:text-neutral-400 focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-hidden transition-all text-sm shadow-2xs"
                    style="color: #1F1A1A !important; -webkit-text-fill-color: #1F1A1A !important;"
                />
                @error('password')
                    <p class="text-xs font-medium text-danger-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="space-y-1.5 text-left">
                <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-neutral-700">
                    Confirm New Password
                </label>
                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    required
                    placeholder="••••••••"
                    autocomplete="new-password"
                    class="w-full py-2.5 px-3.5 rounded-xl border border-neutral-300 bg-white text-neutral-900 placeholder:text-neutral-400 focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-hidden transition-all text-sm shadow-2xs"
                    style="color: #1F1A1A !important; -webkit-text-fill-color: #1F1A1A !important;"
                />
            </div>

            <button
                type="submit"
                class="w-full inline-flex items-center justify-center font-bold rounded-xl px-4 py-2.5 text-sm transition-colors duration-150 bg-primary-600 hover:bg-primary-700 text-white shadow-xs cursor-pointer focus:outline-hidden focus-visible:ring-2 focus-visible:ring-primary-600/30 mt-2"
            >
                Reset Password &rarr;
            </button>
        </form>

        <div class="flex items-center justify-between text-xs text-neutral-500 pt-2 border-t border-neutral-200">
            <form method="POST" action="{{ route('password.reset.otp.resend') }}" x-on:submit="startCooldown()">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <button
                    type="submit"
                    x-bind:disabled="cooldown > 0"
                    class="text-xs font-semibold text-primary-700 hover:text-primary-800 underline transition-colors disabled:text-neutral-400 disabled:no-underline disabled:cursor-not-allowed cursor-pointer"
                >
                    <span x-show="cooldown === 0">Resend code</span>
                    <span x-show="cooldown > 0" x-cloak>Resend in <span x-text="cooldown"></span>s</span>
                </button>
            </form>

            <a href="{{ route('login') }}" class="font-medium text-neutral-600 hover:text-neutral-900 underline transition-colors">
                Return to log in
            </a>
        </div>
    </div>
</x-layouts::auth>
