<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $items = $user->cartItems()
            ->with(['product.category', 'product.brand'])
            ->latest()
            ->get()
            ->filter(fn(CartItem $item) => $item->product !== null)
            ->values();

        $subtotal = $items->sum(fn(CartItem $item) => $item->subtotal());

        $currency = $items->first()?->currency() ?? 'KES';
        $formattedSubtotal = $currency . ' ' . number_format($subtotal, 2);

        $itemCount = $items->sum('quantity');

        return view('shop.cart', compact(
            'items',
            'subtotal',
            'currency',
            'formattedSubtotal',
            'itemCount'
        ));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:999'],
        ]);

        $wantsJson = $request->expectsJson() || $request->ajax();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $product = Product::visible()->find($validated['product_id']);

        if (!$product) {
            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'This product is not available.',
                ], 404);
            }
            return back()->with('error', 'This product is not available.');
        }

        if (!$product->inStock()) {
            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'This product is out of stock.',
                ], 422);
            }
            return back()->with('error', 'This product is out of stock.');
        }

        $requestedQty = (int) ($validated['quantity'] ?? 1);

        try {
            DB::beginTransaction();

            $existing = $user->cartItems()
                ->where('product_id', $product->id)
                ->lockForUpdate()
                ->first();

            $currentQty = $existing?->quantity ?? 0;
            $newQty = $currentQty + $requestedQty;

            if ($product->track_inventory && $newQty > $product->stock_quantity) {
                DB::rollBack();

                $maxAddable = max(0, $product->stock_quantity - $currentQty);
                $message = $maxAddable === 0
                    ? 'You already have the maximum available quantity in your cart.'
                    : "Only {$product->stock_quantity} available — you already have {$currentQty} in your cart.";

                if ($wantsJson) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                        'available' => (int) $product->stock_quantity,
                        'in_cart' => (int) $currentQty,
                    ], 422);
                }
                return back()->with('error', $message);
            }

            if ($existing) {
                $existing->update(['quantity' => $newQty]);
                $cartItem = $existing;
            } else {
                $cartItem = $user->cartItems()->create([
                    'product_id' => $product->id,
                    'quantity' => $requestedQty,
                ]);
            }

            DB::commit();

            $cartCount = $user->cartCount();
            $cartSubtotal = $this->cartSubtotal($user);
            $cartCurrency = $this->cartCurrency($user);

            if ($wantsJson) {
                return response()->json([
                    'success' => true,
                    'message' => "\"{$product->name}\" added to cart.",
                    'cart_count' => $cartCount,
                    'cart_subtotal' => $cartSubtotal,
                    'cart_currency' => $cartCurrency,
                    'line_quantity' => (int) $cartItem->quantity,
                ]);
            }

            return back()->with('success', "\"{$product->name}\" added to cart.");
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Cart add failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'product_id' => $product->id,
                'quantity' => $requestedQty,
                'trace' => $e->getTraceAsString(),
            ]);

            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not add to cart. Please try again.',
                ], 500);
            }
            return back()->with('error', 'Could not add to cart. Please try again.');
        }
    }

    public function update(Request $request, CartItem $cartItem): RedirectResponse|JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ((int) $cartItem->user_id !== (int) $user->id) {
            abort(403, 'You do not have permission to modify this cart item.');
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        $wantsJson = $request->expectsJson() || $request->ajax();

        $product = $cartItem->product;
        $newQty = (int) $validated['quantity'];

        if (!$product) {
            $cartItem->delete();

            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'This product is no longer available.',
                    'cart_count' => $user->cartCount(),
                    'cart_subtotal' => $this->cartSubtotal($user),
                    'cart_currency' => $this->cartCurrency($user),
                ], 404);
            }
            return back()->with('error', 'This product is no longer available.');
        }

        if ($product->track_inventory && $newQty > $product->stock_quantity) {
            $message = "Only {$product->stock_quantity} available.";

            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'available' => (int) $product->stock_quantity,
                ], 422);
            }
            return back()->with('error', $message);
        }

        try {
            $cartItem->update(['quantity' => $newQty]);

            $cartCount = $user->cartCount();
            $cartSubtotal = $this->cartSubtotal($user);
            $cartCurrency = $this->cartCurrency($user);

            if ($wantsJson) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cart updated.',
                    'cart_count' => $cartCount,
                    'cart_subtotal' => $cartSubtotal,
                    'cart_currency' => $cartCurrency,
                    'line_quantity' => (int) $cartItem->quantity,
                    'line_subtotal' => $cartItem->formattedSubtotal(),
                ]);
            }

            return back()->with('success', 'Cart updated.');
        } catch (\Throwable $e) {
            Log::error('Cart update failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'cart_item_id' => $cartItem->id,
                'trace' => $e->getTraceAsString(),
            ]);

            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not update cart. Please try again.',
                ], 500);
            }
            return back()->with('error', 'Could not update cart. Please try again.');
        }
    }

    public function destroy(Request $request, CartItem $cartItem): RedirectResponse|JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ((int) $cartItem->user_id !== (int) $user->id) {
            abort(403, 'You do not have permission to modify this cart item.');
        }

        $wantsJson = $request->expectsJson() || $request->ajax();

        $productName = $cartItem->product?->name ?? 'Item';

        try {
            $cartItem->delete();

            $cartCount = $user->cartCount();
            $cartSubtotal = $this->cartSubtotal($user);
            $cartCurrency = $this->cartCurrency($user);

            if ($wantsJson) {
                return response()->json([
                    'success' => true,
                    'message' => "\"{$productName}\" removed from cart.",
                    'cart_count' => $cartCount,
                    'cart_subtotal' => $cartSubtotal,
                    'cart_currency' => $cartCurrency,
                ]);
            }

            return back()->with('success', "\"{$productName}\" removed from cart.");
        } catch (\Throwable $e) {
            Log::error('Cart remove failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'cart_item_id' => $cartItem->id,
                'trace' => $e->getTraceAsString(),
            ]);

            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not remove item. Please try again.',
                ], 500);
            }
            return back()->with('error', 'Could not remove item. Please try again.');
        }
    }

    // ============================================
    // COUNT — lightweight endpoint for live counts
    // ============================================

    /**
     * Returns the current user's cart summary as JSON.
     * Used by the customer dashboard to refresh the count
     * without a full page reload.
     */
    public function count(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'count' => $user->cartCount(),
            'subtotal' => $this->cartSubtotal($user),
            'currency' => $this->cartCurrency($user),
        ]);
    }

    // ============================================
    // HELPERS
    // ============================================

    protected function cartSubtotal(\App\Models\User $user): float
    {
        return (float) $user->cartItems()
            ->with('product')
            ->get()
            ->filter(fn(CartItem $item) => $item->product !== null)
            ->sum(fn(CartItem $item) => $item->subtotal());
    }

    protected function cartCurrency(\App\Models\User $user): string
    {
        return $user->cartItems()
            ->with('product')
            ->first()?->currency() ?? 'KES';
    }
}
