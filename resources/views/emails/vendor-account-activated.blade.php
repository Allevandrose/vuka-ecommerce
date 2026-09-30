<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Vendor Account Activated</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1E3C2C; color: white; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 20px; }
        .header p { margin: 4px 0 0; font-size: 13px; opacity: 0.9; }
        .content { background: #f9f9f9; padding: 30px; }
        .content h2 { margin-top: 0; color: #1E3C2C; font-size: 18px; }
        .info-box { background: #f0f4f8; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .info-box p { margin: 0; color: #1E3C2C; }
        .button { display: inline-block; background: #1E3C2C; color: #ffffff !important; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        .muted { color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Vuka Shop</h1>
            <p>Vendor Account Activated</p>
        </div>

        <div class="content">
            <h2>You're live, {{ $user->name }}! 🎉</h2>

            <p>
                Your vendor account for <strong>{{ $user->shop_name ?? 'your shop' }}</strong>
                has been activated. You can now log in to your vendor dashboard and start listing
                products.
            </p>

            <div class="info-box">
                <p>
                    <strong>Email:</strong> {{ $user->email }}<br>
                    <strong>Shop:</strong> {{ $user->shop_name ?? '—' }}<br>
                    <strong>Status:</strong> Active
                </p>
            </div>

            <p style="text-align: center; margin: 30px 0;">
                <a href="{{ $loginUrl }}" class="button">Log In to Your Dashboard</a>
            </p>

            <p class="muted">
                Once logged in, you'll land on your vendor dashboard where you can manage your
                shop profile. Product listing tools are coming soon.
            </p>

            <p class="muted">
                If you have any questions, reply to this email or contact our support team.
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Vuka Shop. All rights reserved.</p>
        </div>
    </div>
</body>
</html>