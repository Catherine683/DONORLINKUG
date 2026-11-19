@component('emails.layout', [
    'subject' => $subject,
    'heading' => $heading,
    'actionText' => $actionText,
    'actionUrl' => $actionUrl,
])
    <p style="margin: 0 0 16px;">Hello {{ $user->name }},</p>
    <p style="margin: 0 0 16px;">We received a request to reset your password for your DonorLink UG account. Use the 6-digit code below to set a new password:</p>

    <div style="background-color: #FDF2F2; border: 2px dashed #FACDD0; border-radius: 12px; padding: 24px; text-align: center; margin: 24px 0;">
        <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #961D23; display: block; margin-bottom: 8px;">Password Reset Code</span>
        <span style="font-size: 38px; font-weight: 800; letter-spacing: 10px; color: #B3262E; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; display: block;">{{ $code }}</span>
        <span style="font-size: 12px; color: #7F7368; display: block; margin-top: 10px;">This code will expire in 15 minutes.</span>
    </div>

    <p style="margin: 0 0 16px;">Enter this code on the password reset page along with your new password to regain access to your account.</p>
    <p style="margin: 0; font-size: 13px; color: #7F7368;">If you did not request a password reset, you can safely ignore this email. Your current password remains secure.</p>
@endcomponent
