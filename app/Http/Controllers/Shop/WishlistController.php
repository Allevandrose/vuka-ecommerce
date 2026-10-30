<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\WishlistItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class WishlistController extends Controller
{
    // ============================================
    // INDEX — show the wishlist page
    // ============================================

    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $items = $user->wishlistItems()
            ->with(['product.category', 'product.brand'])
            ->latest()
            ->get()
            ->filter(fn(WishlistItem $item) => $item->product !== null)
            ->values();

        $itemCount = $items->count();

        return view('shop.wishlist', compact('items', 'itemCount'));
    }

    // ============================================
    // STORE — add a product to the wishlist
    // ============================================

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $wantsJson = $request->expectsJson() || $request->ajax();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Only visible products can be wishlisted — a soft-deleted or
        // disabled product must 404, matching the cart's policy.
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

        try {
            $wishlistItem = $user->wishlistItems()->firstOrCreate([
                'product_id' => $product->id,
            ]);

            $wishlistCount = $user->wishlistCount();
            $alreadyThere = !$wishlistItem->wasRecentlyCreated;

            if ($wantsJson) {
                return response()->json([
                    'success' => true,
                    'message' => $alreadyThere
                        ? "\"{$product->name}\" is already in your wishlist."
                        : "\"{$product->name}\" added to your wishlist.",
                    'wishlist_count' => $wishlistCount,
                    'wishlisted' => true,
                    'already_there' => $alreadyThere,
                ]);
            }

            return back()->with('success', $alreadyThere
                ? "\"{$product->name}\" is already in your wishlist."
                : "\"{$product->name}\" added to your wishlist.");
        } catch (\Throwable $e) {
            Log::error('Wishlist add failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'product_id' => $product->id,
                'trace' => $e->getTraceAsString(),
            ]);

            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not add to wishlist. Please try again.',
                ], 500);
            }
            return back()->with('error', 'Could not add to wishlist. Please try again.');
        }
    }

    // ============================================
    // DESTROY — remove a single item from the wishlist
    // ============================================

    public function destroy(Request $request, WishlistItem $wishlistItem): RedirectResponse|JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ((int) $wishlistItem->user_id !== (int) $user->id) {
            abort(403, 'You do not have permission to modify this wishlist item.');
        }

        $wantsJson = $request->expectsJson() || $request->ajax();

        $productName = $wishlistItem->product?->name ?? 'Item';

        try {
            $wishlistItem->delete();

            $wishlistCount = $user->wishlistCount();

            if ($wantsJson) {
                return response()->json([
                    'success' => true,
                    'message' => "\"{$productName}\" removed from your wishlist.",
                    'wishlist_count' => $wishlistCount,
                    'wishlisted' => false,
                ]);
            }

            return back()->with('success', "\"{$productName}\" removed from your wishlist.");
        } catch (\Throwable $e) {
            Log::error('Wishlist remove failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'wishlist_item_id' => $wishlistItem->id,
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
    // TOGGLE — add if absent, remove if present
    // ============================================

    /**
     * The heart button on product cards and the product page calls
     * this endpoint. Returns the new state plus the count. Idempotent
     * in the sense that calling it flips the state deterministically:
     * wishlisted → not wishlisted, not wishlisted → wishlisted.
     */
    public function toggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $product = Product::visible()->find($validated['product_id']);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'This product is not available.',
            ], 404);
        }

        try {
            $existing = $user->wishlistItems()
                ->where('product_id', $product->id)
                ->first();

            if ($existing) {
                $existing->delete();
                $wishlisted = false;
                $message = "\"{$product->name}\" removed from your wishlist.";
            } else {
                $user->wishlistItems()->create([
                    'product_id' => $product->id,
                ]);
                $wishlisted = true;
                $message = "\"{$product->name}\" added to your wishlist.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'wishlist_count' => $user->wishlistCount(),
                'wishlisted' => $wishlisted,
            ]);
        } catch (\Throwable $e) {
            Log::error('Wishlist toggle failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'product_id' => $product->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Could not update wishlist. Please try again.',
            ], 500);
        }
    }

    // ============================================
    // COUNT — lightweight endpoint for badges
    // ============================================

    public function count(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'count' => $user->wishlistCount(),
        ]);
    }

    // ============================================
    // MOVE TO CART — wishlist → cart in one action
    // ============================================

    /**
     * Removes the product from the wishlist and adds it to the cart
     * in a single transaction. If the cart add fails (out of stock,
     * product became unavailable), the wishlist row is left intact
     * so the user doesn't lose their saved item.
     */
    public function moveToCart(Request $request, WishlistItem $wishlistItem): RedirectResponse|JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ((int) $wishlistItem->user_id !== (int) $user->id) {
            abort(403, 'You do not have permission to modify this wishlist item.');
        }

        $wantsJson = $request->expectsJson() || $request->ajax();

        $product = $wishlistItem->product;

        if (!$product) {
            $wishlistItem->delete();

            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'This product is no longer available.',
                    'wishlist_count' => $user->wishlistCount(),
                ], 404);
            }
            return back()->with('error', 'This product is no longer available.');
        }

        // Re-check the product is still visible and in stock. The product
        // may have been disabled since it was wishlisted.
        if (!$product->isVisible()) {
            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'This product is no longer available.',
                ], 422);
            }
            return back()->with('error', 'This product is no longer available.');
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

        try {
            $result = DB::transaction(function () use ($user, $product, $wishlistItem) {
                // Lock the existing cart row (if any) to avoid racing a
                // simultaneous "Add to cart" from another tab.
                $existingCart = $user->cartItems()
                    ->where('product_id', $product->id)
                    ->lockForUpdate()
                    ->first();

                $currentQty = $existingCart?->quantity ?? 0;
                $newQty = $currentQty + 1;

                // Stock cap check — same rule as CartController::store.
                if ($product->track_inventory && $newQty > $product->stock_quantity) {
                    throw new \RuntimeException("Only {$product->stock_quantity} available.");
                }

                if ($existingCart) {
                    $existingCart->update(['quantity' => $newQty]);
                } else {
                    $user->cartItems()->create([
                        'product_id' => $product->id,
                        'quantity' => 1,
                    ]);
                }

                $wishlistItem->delete();

                return [
                    'cart_count' => $user->cartCount(),
                    'wishlist_count' => $user->wishlistCount(),
                ];
            });
        } catch (\RuntimeException $e) {
            // Domain error (stock cap) — not a 500.
            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Wishlist moveToCart failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'wishlist_item_id' => $wishlistItem->id,
                'product_id' => $product->id,
                'trace' => $e->getTraceAsString(),
            ]);

            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not move item to cart. Please try again.',
                ], 500);
            }
            return back()->with('error', 'Could not move item to cart. Please try again.');
        }

        $message = "\"{$product->name}\" moved to your cart.";

        if ($wantsJson) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'cart_count' => $result['cart_count'],
                'wishlist_count' => $result['wishlist_count'],
                'wishlisted' => false,
            ]);
        }

        return back()->with('success', $message);
    }
}
