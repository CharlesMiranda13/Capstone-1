<!DOCTYPE html>
<html>
<head>
    <title>Account Declined</title>
</head>
<body>
    <h2>Hello {{ $user->name }},</h2>

    <p>Thank you for registering with HealConnect.</p>

    <p>After reviewing your information, we regret to inform you that your account has been <strong>declined</strong>.</p>

    @if(!empty($reason))
        <p><strong>Reason:</strong></p>
        <p>{{ $reason }}</p>
    @endif

    <p>You may update your details and reapply anytime.</p>

    <br><br>
    <p>— The HealConnect Team</p>
</body>
</html>
