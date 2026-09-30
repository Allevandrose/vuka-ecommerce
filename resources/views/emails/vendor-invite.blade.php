<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Vendor Application Approved</title>
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
            <p>Vendor Application Approved</p>
        </div>

        <div class="content">
            <h2>Congratulations, {{ $application->name }}! 🎉</h2>

            <p>
                Your application to become a Vuka Shop vendor for
                <strong>{{ $application->shop_name }}</strong> has been approved.
            </p>

            <div class="info-box">
                <p>
                    <strong>Shop:</strong> {{ $application->shop_name }}<br>
                    <strong>Location:</strong> {{ $application->shop_address }}<br>
                    <strong>Status:</strong> Approved
                </p>
            </div>

            <p>
                The next step is to create your vendor account. Click the button below
                to set up your login credentials.
            </p>

            <p style="text-align: center; margin: 30px 0;">
                <a href="{{ $registerUrl }}" class="button">Create Vendor Account</a>
            </p>

            <p class="muted">
                This link is private and should not be shared. After creating your
                account, you will be able to log in and access your vendor dashboard.
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Vuka Shop. All rights reserved.</p>
        </div>
    </div>
</body>
</html>