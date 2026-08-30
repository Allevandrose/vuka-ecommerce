<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Staff Registration</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1E3C2C; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 30px; }
        .button { display: inline-block; background: #1E3C2C; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        .alert { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Vuka Shop</h1>
            <p>New Staff Registration</p>
        </div>

        <div class="content">
            <h2>New Staff Registration! 🔔</h2>
            
            <p>Hello {{ $admin->name }},</p>
            
            <p>A new staff member has registered and is waiting for your approval.</p>

            <div style="background: #f0f4f8; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <p style="margin: 0; color: #1E3C2C;">
                    <strong>Name:</strong> {{ $newUser->name }}<br>
                    <strong>Email:</strong> {{ $newUser->email }}<br>
                    <strong>Role:</strong> {{ ucfirst($newUser->user_type) }}<br>
                    <strong>Registered:</strong> {{ $newUser->created_at->format('M d, Y H:i A') }}
                </p>
            </div>

            <div class="alert">
                <p style="margin: 0; color: #92400e;">
                    <strong>⚠️ Action Required:</strong><br>
                    Please review and activate this staff member.
                </p>
            </div>

            <p style="text-align: center;">
                <a href="{{ $manageUrl }}" class="button">Manage Staff</a>
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Vuka Shop. All rights reserved.</p>
        </div>
    </div>
</body>
</html>