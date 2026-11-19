<x-layouts::auth :title="__('Forgot password')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Forgot password')" :description="__('Enter your email to receive a 6-digit password reset code')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-5">
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
                    autofocus
                    placeholder="email@example.com"
                    value="{{ old('email') }}"
                    class="w-full py-2.5 px-3.5 rounded-xl border border-neutral-300 bg-white text-neutral-900 placeholder:text-neutral-400 focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 focus:outline-hidden transition-all text-sm shadow-2xs"
                    style="color: #1F1A1A !important; -webkit-text-fill-color: #1F1A1A !important;"
                />
                @error('email')
                    <p class="text-xs font-medium text-danger-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full inline-flex items-center justify-center font-bold rounded-xl px-4 py-2.5 text-sm transition-colors duration-150 bg-primary-600 hover:bg-primary-700 text-white shadow-xs cursor-pointer focus:outline-hidden focus-visible:ring-2 focus-visible:ring-primary-600/30"
                data-test="email-password-reset-link-button"
            >
                Send Password Reset Code &rarr;
            </button>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-neutral-500">
            <span>{{ __('Or, return to') }}</span>
            <a href="{{ route('login') }}" class="font-semibold text-primary-700 hover:text-primary-800 underline transition-colors">
                {{ __('log in') }}
            </a>
        </div>
    </div>
</x-layouts::auth>
