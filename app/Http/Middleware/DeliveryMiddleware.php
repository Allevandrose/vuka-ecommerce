<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DeliveryMiddleware
{
    /**
     * Handle an incoming request.
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

        // Check if user is delivery staff
        if (!$user->isDelivery()) {
            abort(403, 'Access denied. Delivery staff area only.');
        }

        // Check if user can access system
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
     * Get the appropriate error message based on user status
     */
    private function getAccessErrorMessage(User $user): string
    {
        if (!$user->is_active) {
            return 'Your account is deactivated. Please contact the administrator.';
        }

        if ($user->isStaff() && !$user->approved_at) {
            return 'Your account is pending approval. Please wait for admin confirmation.';
        }

        if ($user->isWeekendDisabled()) {
            return 'Staff access is restricted on weekends. Please contact the administrator for override.';
        }

        return 'Access denied. Please contact the administrator.';
    }
}
