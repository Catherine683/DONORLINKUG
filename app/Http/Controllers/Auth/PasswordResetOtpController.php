<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetOtpController extends Controller
{
    public function __construct(
        private readonly OtpService $otpService,
    ) {}

    /**
     * Send a 6-digit password reset OTP to the user's email.
     */
    public function sendOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower(trim((string) $request->input('email')));
        $user = User::where('email', $email)->first();

        if ($user && $user->is_active) {
            $this->otpService->sendPasswordResetOtp($user);
        }

        return redirect()->route('password.reset.otp.form', ['email' => $email])
            ->with('status', 'A 6-digit password reset code has been sent to your email.');
    }

    /**
     * Display the OTP entry and password reset form.
     */
    public function showResetForm(Request $request): View
    {
        $email = (string) $request->query('email', session('password_reset_email', ''));

        return view('pages.auth.reset-password-otp', [
            'email' => $email,
        ]);
    }

    /**
     * Verify OTP and reset the user's password.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string', 'size:6'],
            'password' => ['required', 'string', 'confirmed', Password::default()],
        ], [
            'code.required' => 'Please enter the 6-digit reset code sent to your email.',
            'code.size' => 'The reset code must be exactly 6 digits.',
        ]);

        $email = strtolower(trim((string) $request->input('email')));
        $code = trim((string) $request->input('code'));

        $isValid = $this->otpService->verify(
            $email,
            $code,
            OtpService::PURPOSE_PASSWORD_RESET
        );

        if (! $isValid) {
            throw ValidationException::withMessages([
                'code' => ['The password reset code is invalid or has expired. Please request a new code.'],
            ]);
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['No account found with this email address.'],
            ]);
        }

        $user->forceFill([
            'password' => Hash::make((string) $request->input('password')),
            'remember_token' => Str::random(60),
        ])->save();

        return redirect()->route('login')
            ->with('status', 'Your password has been reset successfully. Please log in with your new password.');
    }

    /**
     * Resend a fresh 6-digit password reset OTP.
     */
    public function resendOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower(trim((string) $request->input('email')));
        $user = User::where('email', $email)->first();

        if ($user && $user->is_active) {
            $this->otpService->sendPasswordResetOtp($user);
        }

        return back()->with('status', 'A new 6-digit reset code has been sent to your email.');
    }
}
