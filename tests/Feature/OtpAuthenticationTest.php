<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\EmailOtp;
use App\Models\User;
use App\Notifications\AccountActivationOtp;
use App\Notifications\PasswordResetOtp;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OtpAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_donor_registration_sends_activation_otp(): void
    {
        Notification::fake();

        $response = $this->post(route('register.store'), [
            'name' => 'Sarah Namubiru',
            'email' => 'sarah@example.com',
            'phone' => '+256772123456',
            'date_of_birth' => '1998-05-12',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'consent' => '1',
        ]);

        $response->assertRedirect(route('verification.notice'));

        $user = User::where('email', 'sarah@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);

        Notification::assertSentTo($user, AccountActivationOtp::class);

        $otp = EmailOtp::where('email', 'sarah@example.com')
            ->where('purpose', OtpService::PURPOSE_ACTIVATION)
            ->first();

        $this->assertNotNull($otp);
        $this->assertEquals(6, strlen($otp->code));
    }

    public function test_donor_can_activate_account_with_valid_otp(): void
    {
        $user = User::factory()->create([
            'role' => Role::Donor,
            'email_verified_at' => null,
        ]);

        $otpService = app(OtpService::class);
        $code = $otpService->generate($user->email, OtpService::PURPOSE_ACTIVATION);

        $response = $this->actingAs($user)->post(route('verification.otp.submit'), [
            'code' => $code,
        ]);

        $response->assertRedirect(route('donor.home'));

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);

        $otp = EmailOtp::where('email', $user->email)->where('code', $code)->first();
        $this->assertNotNull($otp->used_at);
    }

    public function test_donor_cannot_activate_account_with_invalid_otp(): void
    {
        $user = User::factory()->create([
            'role' => Role::Donor,
            'email_verified_at' => null,
        ]);

        $otpService = app(OtpService::class);
        $otpService->generate($user->email, OtpService::PURPOSE_ACTIVATION);

        $response = $this->actingAs($user)->from(route('verification.notice'))->post(route('verification.otp.submit'), [
            'code' => '000000',
        ]);

        $response->assertRedirect(route('verification.notice'));
        $response->assertSessionHasErrors('code');

        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }

    public function test_donor_cannot_activate_account_with_expired_otp(): void
    {
        $user = User::factory()->create([
            'role' => Role::Donor,
            'email_verified_at' => null,
        ]);

        $otpService = app(OtpService::class);
        $code = $otpService->generate($user->email, OtpService::PURPOSE_ACTIVATION);

        // Simulate OTP expiration
        EmailOtp::where('code', $code)->update([
            'expires_at' => Carbon::now()->subMinute(),
        ]);

        $response = $this->actingAs($user)->from(route('verification.notice'))->post(route('verification.otp.submit'), [
            'code' => $code,
        ]);

        $response->assertRedirect(route('verification.notice'));
        $response->assertSessionHasErrors('code');

        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }

    public function test_donor_can_resend_activation_otp(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'role' => Role::Donor,
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->post(route('verification.send'));

        $response->assertRedirect();
        $response->assertSessionHas('status', 'otp-sent');

        Notification::assertSentTo($user, AccountActivationOtp::class);
    }

    public function test_forgot_password_sends_password_reset_otp(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'donor@ubts.gov.ug',
            'is_active' => true,
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'donor@ubts.gov.ug',
        ]);

        $response->assertRedirect(route('password.reset.otp.form', ['email' => 'donor@ubts.gov.ug']));

        Notification::assertSentTo($user, PasswordResetOtp::class);

        $otp = EmailOtp::where('email', 'donor@ubts.gov.ug')
            ->where('purpose', OtpService::PURPOSE_PASSWORD_RESET)
            ->first();

        $this->assertNotNull($otp);
        $this->assertEquals(6, strlen($otp->code));
    }

    public function test_user_can_reset_password_using_valid_otp(): void
    {
        $user = User::factory()->create([
            'email' => 'donor2@ubts.gov.ug',
            'password' => Hash::make('OldPassword123!'),
        ]);

        $otpService = app(OtpService::class);
        $code = $otpService->generate($user->email, OtpService::PURPOSE_PASSWORD_RESET);

        $response = $this->post(route('password.reset.otp.submit'), [
            'email' => 'donor2@ubts.gov.ug',
            'code' => $code,
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');

        $user->refresh();
        $this->assertTrue(Hash::check('NewSecurePassword123!', $user->password));

        $otp = EmailOtp::where('code', $code)->first();
        $this->assertNotNull($otp->used_at);
    }

    public function test_user_cannot_reset_password_with_invalid_otp(): void
    {
        $user = User::factory()->create([
            'email' => 'donor3@ubts.gov.ug',
            'password' => Hash::make('OldPassword123!'),
        ]);

        $otpService = app(OtpService::class);
        $otpService->generate($user->email, OtpService::PURPOSE_PASSWORD_RESET);

        $response = $this->post(route('password.reset.otp.submit'), [
            'email' => 'donor3@ubts.gov.ug',
            'code' => '999999',
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ]);

        $response->assertSessionHasErrors('code');

        $user->refresh();
        $this->assertTrue(Hash::check('OldPassword123!', $user->password));
    }
}
