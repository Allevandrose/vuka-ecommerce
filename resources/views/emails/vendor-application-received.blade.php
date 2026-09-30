<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Vendor Application</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1E3C2C; color: white; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 20px; }
        .header p { margin: 4px 0 0; font-size: 13px; opacity: 0.9; }
        .content { background: #f9f9f9; padding: 30px; }
        .content h2 { margin-top: 0; color: #1E3C2C; font-size: 18px; }
        .info-box { background: #ffffff; border-left: 4px solid #1E3C2C; padding: 15px 20px; margin: 20px 0; border-radius: 4px; }
        .info-row { margin: 6px 0; }
        .info-label { font-weight: bold; color: #1E3C2C; display: inline-block; min-width: 130px; }
        .button { display: inline-block; background: #1E3C2C; color: #ffffff !important; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        .muted { color: #666; font-size: 13px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Vuka Shop</h1>
            <p>New Vendor Application</p>
        </div>

        <div class="content">
            <h2>Hi {{ $admin->name }},</h2>

            <p>A new vendor has applied to join Vuka Shop. Please review their application.</p>

            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Applicant:</span>
                    {{ $application->name }}
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    {{ $application->email }}
                </div>
                @if ($application->phone)
                    <div class="info-row">
                        <span class="info-label">Phone:</span>
                        {{ $application->phone }}
                    </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Shop Name:</span>
                    {{ $application->shop_name }}
                </div>
                <div class="info-row">
                    <span class="info-label">Shop Address:</span>
                    {{ $application->shop_address }}
                </div>
                @if ($application->shop_latitude && $application->shop_longitude)
                    <div class="info-row">
                        <span class="info-label">Coordinates:</span>
                        {{ $application->shop_latitude }}, {{ $application->shop_longitude }}
                    </div>
                @endif
                @if ($application->product_categories)
                    <div class="info-row">
                        <span class="info-label">Sells:</span>
                        {{ $application->product_categories }}
                    </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Applied:</span>
                    {{ $application->created_at->format('M d, Y H:i') }}
                </div>
            </div>

            @if ($application->message)
                <p class="muted"><strong>Message from applicant:</strong></p>
                <p style="background: #fff; padding: 12px 16px; border-radius: 4px; border: 1px solid #e5e7eb;">
                    {{ $application->message }}
                </p>
            @endif

            <p style="text-align: center; margin-top: 30px;">
                <a href="{{ $reviewUrl }}" class="button">Review Application</a>
            </p>

            <p class="muted" style="margin-top: 30px;">
                You are receiving this because you are an administrator on Vuka Shop.
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Vuka Shop. All rights reserved.</p>
        </div>
    </div>
</body>
</html>