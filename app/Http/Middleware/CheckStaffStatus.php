<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class CheckStaffStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // Customers always have access
        if ($user->isCustomer()) {
            return $next($request);
        }

        // Check if user can access the system
        if (!$user->canAccessSystem()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $errorMessage = $this->getAccessErrorMessage($user);
            return redirect()->route('login')->with('error', $errorMessage);
        }

        // Update last activity
        $user->update(['last_activity_at' => now()]);

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
