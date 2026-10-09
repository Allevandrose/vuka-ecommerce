<?php

namespace App\View\Components;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductCard extends Component
{
    public Product $product;
    public bool $showCategory;

    /**
     * Create a new component instance.
     *
     * @param  Product  $product       The product to render.
     * @param  bool     $showCategory  Whether to show the category as a tag pill.
     *                                  If false, brand is used (when present).
     */
    public function __construct(Product $product, bool $showCategory = true)
    {
        $this->product = $product;
        $this->showCategory = $showCategory;
    }

    /**
     * Compute the tag pill text.
     * Priority: category name (if showCategory) → brand name → 'Product'
     */
    public function tag(): string
    {
        if ($this->showCategory && $this->product->category) {
            return $this->product->category->name;
        }

        if ($this->product->brand) {
            return $this->product->brand->name;
        }

        if ($this->product->category) {
            return $this->product->category->name;
        }

        return 'Product';
    }

    /**
     * The primary image URL, or null if no image.
     */
    public function imageUrl(): ?string
    {
        $path = $this->product->thumbnail
            ?? $this->product->primary_image
            ?? null;

        if (!$path) {
            return null;
        }

        return asset('storage/' . $path);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.product-card');
    }
}
