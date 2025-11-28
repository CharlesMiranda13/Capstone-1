<div style="font-family: Arial, sans-serif; font-size: 16px; color: #333; line-height: 1.6;">
    <p>Hi {{ $name }},</p>

    <p>We received your concern:</p>
    <blockquote style="background:#f7f7f7; padding:10px; border-left:4px solid #1a73e8; font-size:15px;">
        {{ $originalMessage }}
    </blockquote>

    <p>Here is our reply:</p>
    <div style="background:#eef5ff; padding:15px; border-radius:6px; font-size:16px;">
        {{ $reply }}
    </div>

    <p>Best regards,<br>HealConnect Team</p>
</div>
