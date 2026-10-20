<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Product Approved</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1E3C2C; color: white; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 20px; }
        .header p { margin: 4px 0 0; font-size: 13px; opacity: 0.9; }
        .content { background: #f9f9f9; padding: 30px; }
        .content h2 { margin-top: 0; color: #1E3C2C; font-size: 18px; }
        .card { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin: 20px 0; display: table; width: 100%; }
        .card-img { width: 100px; display: table-cell; vertical-align: middle; padding-right: 16px; }
        .card-img img { width: 100px; height: 100px; object-fit: cover; border-radius: 6px; }
        .card-body { display: table-cell; vertical-align: middle; }
        .card-body p { margin: 2px 0; }
        .card-title { font-weight: bold; color: #14231C; font-size: 15px; }
        .card-meta { color: #6C7268; font-size: 13px; }
        .button { display: inline-block; background: #1E3C2C; color: #ffffff !important; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .button-secondary { display: inline-block; background: #ffffff; color: #1E3C2C !important; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; border: 1px solid #1E3C2C; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        .muted { color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Vuka Shop</h1>
            <p>Product Approved</p>
        </div>

        <div class="content">
            <h2>Great news, {{ $vendor->name ?? 'there' }}! 🎉</h2>

            <p>
                Your product has been reviewed and approved. It's now live on VukaShop and visible to customers.
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
                    <p class="card-meta">Status: <strong style="color: #16a34a;">Active</strong></p>
                </div>
            </div>

            <p style="text-align: center; margin: 30px 0;">
                <a href="{{ $productUrl }}" class="button">View on Site</a>
            </p>

            <p style="text-align: center; margin: 20px 0;">
                <a href="{{ $dashboardUrl }}" class="button-secondary">Manage in Dashboard</a>
            </p>

            <p class="muted">
                You can edit the product anytime from your dashboard. Note: significant changes
                to price, images, or description will send the product back through review
                to keep the catalog consistent.
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Vuka Shop. All rights reserved.</p>
        </div>
    </div>
</body>
</html>