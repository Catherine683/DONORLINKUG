{{ $subject }}
========================================

Hello {{ $user->name }},

We received a request to reset the password for your DonorLink UG account. Use the 6-digit code below to set a new password:

YOUR PASSWORD RESET CODE: {{ $code }}

(This code is valid for 15 minutes. Do not share it with anyone.)

Visit the link below to enter your code and new password:
{{ $actionUrl }}

If you did not request a password reset, you can safely ignore this email. Your current password remains secure.

--
Uganda Blood Transfusion Services (UBTS)
DonorLink UG
