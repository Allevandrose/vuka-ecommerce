<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Staff Invitation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1E3C2C; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 30px; }
        .button { display: inline-block; background: #1E3C2C; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Vuka Shop</h1>
            <p>Staff Registration Invitation</p>
        </div>

        <div class="content">
            <h2>You're Invited! 🎉</h2>
            
            <p>You have been invited to join Vuka Shop as a <strong>{{ ucfirst($role) }}</strong>.</p>

            <div style="background: #f0f4f8; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <p style="margin: 0; color: #1E3C2C;">
                    <strong>Role:</strong> {{ ucfirst($role) }}<br>
                    <strong>Status:</strong> Pending Approval
                </p>
            </div>

            <p style="text-align: center;">
                <a href="{{ $registerUrl }}" class="button">Complete Registration</a>
            </p>

            <p style="color: #666; font-size: 14px; margin-top: 20px;">
                This link is private and should not be shared. After registration, 
                your account will be reviewed by an administrator.
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Vuka Shop. All rights reserved.</p>
        </div>
    </div>
</body>
</html>