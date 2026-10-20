<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Product Needs Changes</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #7f1d1d; color: white; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 20px; }
        .header p { margin: 4px 0 0; font-size: 13px; opacity: 0.9; }
        .content { background: #f9f9f9; padding: 30px; }
        .content h2 { margin-top: 0; color: #7f1d1d; font-size: 18px; }
        .card { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin: 20px 0; display: table; width: 100%; }
        .card-img { width: 100px; display: table-cell; vertical-align: middle; padding-right: 16px; }
        .card-img img { width: 100px; height: 100px; object-fit: cover; border-radius: 6px; }
        .card-body { display: table-cell; vertical-align: middle; }
        .card-body p { margin: 2px 0; }
        .card-title { font-weight: bold; color: #14231C; font-size: 15px; }
        .card-meta { color: #6C7268; font-size: 13px; }
        .reason-box { background: #fef2f2; border-left: 4px solid #dc2626; padding: 14px 18px; margin: 20px 0; border-radius: 4px; }
        .reason-box p { margin: 0; color: #7f1d1d; }
        .reason-label { font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #dc2626; font-weight: bold; margin-bottom: 6px; }
        .button { display: inline-block; background: #1E3C2C; color: #ffffff !important; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .button-secondary { display: inline-block; background: #ffffff; color: #1E3C2C !important; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; border: 1px solid #1E3C2C; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        .muted { color: #666; font-size: 14px; }
        .tips { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 14px 18px; margin: 20px 0; }
        .tips p { margin: 0 0 6px; color: #92400e; font-weight: bold; font-size: 14px; }
        .tips ul { margin: 0; padding-left: 20px; color: #78350f; font-size: 13px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Vuka Shop</h1>
            <p>Product Needs Changes</p>
        </div>

        <div class="content">
            <h2>Hi {{ $vendor->name ?? 'there' }},</h2>

            <p>
                Our team reviewed your product submission and it needs a few changes before
                it can go live. Fix the items below and save — your product will be
                resubmitted for review automatically.
            </p>

            <div class="card">
                @if ($product->thumbnail)
                    <div class="card-img">
                        <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}">
                    </div>
                @endif
                <div class="card-body">
                    <p class="card-title">{{ $product->name }}</p>
                    <p class="card-meta">SKU: {{ $product->sku }}</p>
                    <p class="card-meta">Price: {{ $product->formattedPrice() }}</p>
                    <p class="card-meta">Status: <strong style="color: #dc2626;">Rejected</strong></p>
                </div>
            </div>

            <div class="reason-box">
                <p class="reason-label">Reason from admin</p>
                <p>{{ $reason }}</p>
            </div>

            <p style="text-align: center; margin: 30px 0;">
                <a href="{{ $editUrl }}" class="button">Edit Product</a>
            </p>

            <p style="text-align: center; margin: 20px 0;">
                <a href="{{ $dashboardUrl }}" class="button-secondary">Go to My Products</a>
            </p>

            <div class="tips">
                <p>💡 Common reasons for rejection</p>
                <ul>
                    <li>Blurry or low-quality product images</li>
                    <li>Description doesn't match the product</li>
                    <li>Price significantly above market rate</li>
                    <li>Missing or incorrect category/brand</li>
                    <li>Restricted or prohibited item</li>
                </ul>
            </div>

            <p class="muted">
                If you believe this rejection is an error, reply to this email or contact
                support and we'll take another look.
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Vuka Shop. All rights reserved.</p>
        </div>
    </div>
</body>
</html>