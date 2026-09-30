<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VendorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Mirrors AdminMiddleware but gates on the vendor user type.
     * Vendors are NOT subject to weekend restrictions (that rule is staff-only).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // Must be a vendor
        if (!$user->isVendor()) {
            abort(403, 'Access denied. Vendor area only.');
        }

        // Must have an active, approved account
        if (!$user->canAccessSystem()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', $this->getAccessErrorMessage($user));
        }

        return $next($request);
    }

    /**
     * Get the appropriate error message based on user status.
     */
    private function getAccessErrorMessage(User $user): string
    {
        if (!$user->is_active) {
            return 'Your vendor account is deactivated. Please contact the administrator.';
        }

        if (!$user->approved_at) {
            return 'Your vendor account is pending approval. Please wait for admin confirmation.';
        }

        return 'Access denied. Please contact the administrator.';
    }
}
