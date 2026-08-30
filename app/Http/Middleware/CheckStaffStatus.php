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
return redirect()->route('login');
}

// Customers always have access
if ($user->isCustomer()) {
return $next($request);
}

// Check if account is active and approved
if (!$user->is_active) {
Auth::logout();
return redirect()->route('login')
->with('error', 'Your account is deactivated. Please contact the administrator.');
}

if (!$user->approved_at) {
Auth::logout();
return redirect()->route('login')
->with('error', 'Your account is pending approval. Please wait for admin confirmation.');
}

// Check weekend restriction
if ($user->isWeekendDisabled()) {
Auth::logout();
return redirect()->route('login')
->with('error', 'Staff access is restricted on weekends. Please contact the administrator for override.');
}

// Update last activity
$user->update(['last_activity_at' => now()]);

return $next($request);
}
}