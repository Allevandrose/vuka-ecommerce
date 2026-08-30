<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Account Activated</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1E3C2C; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 30px; }
        .button { display: inline-block; background: #1E3C2C; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        .success { background: #d1fae5; border-left: 4px solid #10b981; padding: 15px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Vuka Shop</h1>
            <p>Account Activated</p>
        </div>

        <div class="content">
            <h2>Congratulations {{ $user->name }}! 🎉</h2>
            
            <p>Your staff account has been activated!</p>

            <div class="success">
                <p style="margin: 0; color: #065f46;">
                    <strong>✅ Account Status: Active</strong><br>
                    You can now access the Vuka Shop system.
                </p>
            </div>

            <div style="background: #f0f4f8; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <p style="margin: 0; color: #1E3C2C;">
                    <strong>Role:</strong> {{ ucfirst($user->user_type) }}<br>
                    <strong>Email:</strong> {{ $user->email }}
                </p>
            </div>

            <p style="text-align: center;">
                <a href="{{ $loginUrl }}" class="button">Login to Your Account</a>
            </p>

            <p style="color: #666; font-size: 14px; margin-top: 20px;">
                If you have any questions, please contact your administrator.
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Vuka Shop. All rights reserved.</p>
        </div>
    </div>
</body>
</html>