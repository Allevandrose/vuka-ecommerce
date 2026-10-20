<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VendorProductApproved extends Mailable
{
    use Queueable, SerializesModels;

    public Product $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your product is live on VukaShop: ' . $this->product->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vendor-product-approved',
            with: [
                'product' => $this->product,
                'vendor' => $this->product->source,
                'productUrl' => route('product.show', $this->product->slug),
                'dashboardUrl' => route('vendor.products.index'),
            ],
        );
    }
}
