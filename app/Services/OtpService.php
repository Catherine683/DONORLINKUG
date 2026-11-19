<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EmailOtp;
use App\Models\User;
use App\Notifications\AccountActivationOtp;
use App\Notifications\PasswordResetOtp;
use Illuminate\Support\Carbon;

class OtpService
{
    public const PURPOSE_ACTIVATION = 'activation';

    public const PURPOSE_PASSWORD_RESET = 'password_reset';

    public const CODE_EXPIRY_MINUTES = 15;

    public const MAX_ATTEMPTS = 5;

    /**
     * Generate and store a new 6-digit OTP for the given email and purpose.
     */
    public function generate(string $email, string $purpose): string
    {
        $normalizedEmail = strtolower(trim($email));

        // Invalidate any existing unused OTPs for this email & purpose
        EmailOtp::where('email', $normalizedEmail)
            ->where('purpose', $purpose)
            ->whereNull('used_at')
            ->delete();

        // Secure 6-digit numeric code (100000 to 999999)
        $code = (string) random_int(100000, 999999);

        EmailOtp::create([
            'email' => $normalizedEmail,
            'code' => $code,
            'purpose' => $purpose,
            'expires_at' => Carbon::now()->addMinutes(self::CODE_EXPIRY_MINUTES),
            'attempts' => 0,
        ]);

        return $code;
    }

    /**
     * Verify a 6-digit OTP for an email and purpose.
     */
    public function verify(string $email, string $code, string $purpose): bool
    {
        $normalizedEmail = strtolower(trim($email));
        $cleanCode = trim($code);

        $otp = EmailOtp::where('email', $normalizedEmail)
            ->where('purpose', $purpose)
            ->whereNull('used_at')
            ->where('expires_at', '>', Carbon::now())
            ->latest('id')
            ->first();

        if (! $otp) {
            return false;
        }

        // If exceeded max attempts, expire the OTP
        if ($otp->attempts >= self::MAX_ATTEMPTS) {
            $otp->update(['expires_at' => Carbon::now()->subMinute()]);

            return false;
        }

        if ($otp->code !== $cleanCode) {
            $otp->increment('attempts');

            return false;
        }

        $otp->update(['used_at' => Carbon::now()]);

        return true;
    }

    /**
     * Send an account activation OTP email to a user.
     */
    public function sendActivationOtp(User $user): void
    {
        $code = $this->generate($user->email, self::PURPOSE_ACTIVATION);
        $user->notify(new AccountActivationOtp($user, $code));
    }

    /**
     * Send a password reset OTP email to a user.
     */
    public function sendPasswordResetOtp(User $user): void
    {
        $code = $this->generate($user->email, self::PURPOSE_PASSWORD_RESET);
        $user->notify(new PasswordResetOtp($user, $code));
    }
}
