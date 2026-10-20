<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VendorProductRejected extends Mailable
{
    use Queueable, SerializesModels;

    public Product $product;
    public string $reason;

    public function __construct(Product $product, string $reason)
    {
        $this->product = $product;
        $this->reason = $reason;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your VukaShop product needs changes: ' . $this->product->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vendor-product-rejected',
            with: [
                'product' => $this->product,
                'vendor' => $this->product->source,
                'reason' => $this->reason,
                'editUrl' => route('vendor.products.edit', $this->product),
                'dashboardUrl' => route('vendor.products.index'),
            ],
        );
    }
}
