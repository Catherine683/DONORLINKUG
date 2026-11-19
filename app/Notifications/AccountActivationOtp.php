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

class AccountActivationOtp extends Notification
{
    use LogsNotification, Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $code,
    ) {
        $this->dedupeKey = "account_activation_otp:user:{$user->id}:".time().':'.rand(100, 999);
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = 'Your DonorLink UG Account Activation Code';

        $this->recordSent($this->user->id, 'account_activation_otp', User::class, $this->user->id);

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.account-activation-otp', [
                'subject' => $subject,
                'heading' => 'Activate Your DonorLink Account',
                'user' => $this->user,
                'code' => $this->code,
                'actionText' => 'Enter Activation Code',
                'actionUrl' => route('verification.notice'),
            ])
            ->text('emails.account-activation-otp-text', [
                'subject' => $subject,
                'heading' => 'Activate Your DonorLink Account',
                'user' => $this->user,
                'code' => $this->code,
                'actionUrl' => route('verification.notice'),
            ]);
    }

    public function failed(Throwable $e): void
    {
        $this->recordFailed($e, $this->user->id, 'account_activation_otp', User::class, $this->user->id);
    }
}
