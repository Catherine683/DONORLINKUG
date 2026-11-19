@component('emails.layout', [
    'subject' => $subject,
    'heading' => $heading,
    'actionText' => $actionText,
    'actionUrl' => $actionUrl,
])
    <p style="margin: 0 0 16px;">Hello {{ $user->name }},</p>
    <p style="margin: 0 0 16px;">Thank you for registering as a voluntary blood donor with DonorLink UG. Use the 6-digit activation code below to verify your email address and activate your account:</p>

    <div style="background-color: #FDF2F2; border: 2px dashed #FACDD0; border-radius: 12px; padding: 24px; text-align: center; margin: 24px 0;">
        <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #961D23; display: block; margin-bottom: 8px;">Your Activation Code</span>
        <span style="font-size: 38px; font-weight: 800; letter-spacing: 10px; color: #B3262E; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; display: block;">{{ $code }}</span>
        <span style="font-size: 12px; color: #7F7368; display: block; margin-top: 10px;">This code will expire in 15 minutes.</span>
    </div>

    <p style="margin: 0 0 16px;">Enter this code on the activation screen to complete your registration and begin booking voluntary blood donation appointments with UBTS.</p>
    <p style="margin: 0; font-size: 13px; color: #7F7368;">If you did not create an account on DonorLink UG, please ignore this email.</p>
@endcomponent
