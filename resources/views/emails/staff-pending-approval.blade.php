<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Registration Pending</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1E3C2C; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 30px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Vuka Shop</h1>
            <p>Registration Submitted</p>
        </div>

        <div class="content">
            <h2>Hello {{ $user->name }}! 👋</h2>
            
            <p>Your staff registration has been submitted successfully.</p>

            <div style="background: #f0f4f8; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <p style="margin: 0; color: #1E3C2C;">
                    <strong>Email:</strong> {{ $user->email }}<br>
                    <strong>Role:</strong> {{ ucfirst($user->user_type) }}<br>
                    <strong>Status:</strong> <span style="color: #f59e0b;">Pending Approval</span>
                </p>
            </div>

            <p>An administrator will review your application and activate your account.</p>
            <p>You will receive a confirmation email once your account has been approved.</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Vuka Shop. All rights reserved.</p>
        </div>
    </div>
</body>
</html>