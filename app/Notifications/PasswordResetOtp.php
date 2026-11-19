<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use App\Notifications\Concerns\LogsNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Throwable;

class PasswordResetOtp extends Notification
{
    use LogsNotification, Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $code,
    ) {
        $this->dedupeKey = "password_reset_otp:user:{$user->id}:".time().':'.rand(100, 999);
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = 'Your DonorLink UG Password Reset Code';

        $this->recordSent($this->user->id, 'password_reset_otp', User::class, $this->user->id);

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.password-reset-otp', [
                'subject' => $subject,
                'heading' => 'Reset Your Password',
                'user' => $this->user,
                'code' => $this->code,
                'actionText' => 'Reset Password',
                'actionUrl' => route('password.reset.otp.form', ['email' => $this->user->email]),
            ])
            ->text('emails.password-reset-otp-text', [
                'subject' => $subject,
                'heading' => 'Reset Your Password',
                'user' => $this->user,
                'code' => $this->code,
                'actionUrl' => route('password.reset.otp.form', ['email' => $this->user->email]),
            ]);
    }

    public function failed(Throwable $e): void
    {
        $this->recordFailed($e, $this->user->id, 'password_reset_otp', User::class, $this->user->id);
    }
}
