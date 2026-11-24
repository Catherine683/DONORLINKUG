<?php

namespace Tests\Feature\Mail;

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Email;
use Tests\TestCase;

class BrevoTransportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'mail.mailers.brevo' => ['transport' => 'brevo'],
            'mail.from.address' => 'no-reply@example.com',
            'mail.from.name' => 'DonorLink UG',
            'services.brevo.api_key' => 'test-brevo-api-key',
        ]);
    }

    public function test_it_sends_mail_through_the_brevo_https_api(): void
    {
        Http::fake();

        Mail::mailer('brevo')->raw('Your reset code is 123456.', function (Message $message): void {
            $message->to('person@example.com')
                ->subject('Password reset');
        });

        Http::assertSent(function (Request $request): bool {
            return $request->url() === 'https://api.brevo.com/v3/smtp/email'
                && $request->hasHeader('api-key', 'test-brevo-api-key')
                && $request['sender'] === [
                    'email' => 'no-reply@example.com',
                    'name' => 'DonorLink UG',
                ]
                && $request['to'] === [['email' => 'person@example.com']]
                && $request['subject'] === 'Password reset'
                && $request['textContent'] === 'Your reset code is 123456.';
        });
    }

    public function test_it_preserves_calendar_attachments(): void
    {
        Http::fake();

        $email = (new Email)
            ->from('no-reply@example.com')
            ->to('person@example.com')
            ->subject('Appointment')
            ->text('Your appointment details are attached.')
            ->attach('BEGIN:VCALENDAR', 'appointment.ics', 'text/calendar');

        Mail::mailer('brevo')->getSymfonyTransport()->send($email);

        Http::assertSent(fn (Request $request): bool => $request['attachment'] === [[
            'name' => 'appointment.ics',
            'content' => base64_encode('BEGIN:VCALENDAR'),
        ]]);
    }

    public function test_it_surfaces_brevo_api_errors(): void
    {
        Http::fake([
            'api.brevo.com/*' => Http::response(['message' => 'Invalid API key'], 401),
        ]);

        $this->expectException(RequestException::class);

        Mail::mailer('brevo')->raw('Reset code', function (Message $message): void {
            $message->to('person@example.com')
                ->subject('Password reset');
        });
    }
}
