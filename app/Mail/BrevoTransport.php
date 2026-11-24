<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Http\Client\PendingRequest;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

class BrevoTransport extends AbstractTransport
{
    public function __construct(private readonly PendingRequest $http)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $sentMessage): void
    {
        $email = $sentMessage->getOriginalMessage();

        if (! $email instanceof Email) {
            throw new TransportException('Brevo transport only supports email messages.');
        }

        $from = $email->getFrom();

        if (count($from) !== 1) {
            throw new TransportException('Brevo requires exactly one sender address.');
        }

        $payload = [
            'sender' => $this->formatAddress($from[0]),
            'to' => array_map($this->formatAddress(...), $email->getTo()),
            'subject' => $email->getSubject() ?? '',
        ];

        $html = $this->bodyToString($email->getHtmlBody());
        $text = $this->bodyToString($email->getTextBody());

        if ($html !== null) {
            $payload['htmlContent'] = $html;
        }

        if ($text !== null) {
            $payload['textContent'] = $text;
        }

        if ($email->getCc() !== []) {
            $payload['cc'] = array_map($this->formatAddress(...), $email->getCc());
        }

        if ($email->getBcc() !== []) {
            $payload['bcc'] = array_map($this->formatAddress(...), $email->getBcc());
        }

        if ($email->getReplyTo() !== []) {
            $payload['replyTo'] = $this->formatAddress($email->getReplyTo()[0]);
        }

        if ($email->getAttachments() !== []) {
            $payload['attachment'] = array_map(
                fn (DataPart $attachment): array => [
                    'name' => $attachment->getFilename() ?? 'attachment',
                    'content' => base64_encode($attachment->getBody()),
                ],
                $email->getAttachments(),
            );
        }

        $this->http
            ->post('https://api.brevo.com/v3/smtp/email', $payload)
            ->throw();
    }

    public function __toString(): string
    {
        return 'brevo+https://api.brevo.com/v3/smtp/email';
    }

    private function formatAddress(Address $address): array
    {
        $formatted = ['email' => $address->getAddress()];

        if ($address->getName() !== '') {
            $formatted['name'] = $address->getName();
        }

        return $formatted;
    }

    private function bodyToString(mixed $body): ?string
    {
        if ($body === null) {
            return null;
        }

        if (is_resource($body)) {
            $metadata = stream_get_meta_data($body);

            if ($metadata['seekable']) {
                rewind($body);
            }

            $body = stream_get_contents($body);
        }

        if (! is_string($body)) {
            throw new TransportException('Email body must be a string or readable stream.');
        }

        return $body;
    }
}
