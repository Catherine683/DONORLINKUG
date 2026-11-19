<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class VerifyOtpController extends Controller
{
    public function __construct(
        private readonly OtpService $otpService,
    ) {}

    /**
     * Verify the donor's 6-digit activation OTP.
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ], [
            'code.required' => 'Please enter the 6-digit activation code sent to your email.',
            'code.size' => 'The activation code must be exactly 6 digits.',
        ]);

        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('donor.home'));
        }

        $isValid = $this->otpService->verify(
            $user->email,
            (string) $request->input('code'),
            OtpService::PURPOSE_ACTIVATION
        );

        if (! $isValid) {
            throw ValidationException::withMessages([
                'code' => ['The activation code is invalid or has expired. Please check your email or request a new code.'],
            ]);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->intended(route('donor.home'))
            ->with('status', 'Your account has been successfully activated! Welcome to DonorLink UG.');
    }

    /**
     * Resend a fresh 6-digit activation code.
     */
    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('donor.home'));
        }

        $this->otpService->sendActivationOtp($user);

        return back()->with('status', 'otp-sent');
    }
}
