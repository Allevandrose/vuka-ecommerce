<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();

            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            // Double-check user can access system (security check)
            if (!$user->canAccessSystem()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $errorMessage = $this->getAccessErrorMessage($user);
                return redirect()->route('login')->with('error', $errorMessage);
            }

            // Update last login timestamp
            $user->update(['last_activity_at' => now()]);

            // Get the redirect route
            $redirectTo = $user->getRedirectRoute();

            // Log successful login
            Log::info('User logged in successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'user_type' => $user->user_type,
                'redirect_to' => $redirectTo
            ]);

            // If there's an intended URL, check if it's allowed for this user
            if ($request->session()->has('url.intended')) {
                $intended = $request->session()->get('url.intended');
                if ($this->isIntendedUrlAllowed($intended, $user)) {
                    return redirect()->intended($redirectTo);
                }
                // Clear the intended URL if not allowed
                $request->session()->forget('url.intended');
            }

            return redirect($redirectTo);
        } catch (ValidationException $e) {
            // Handle validation exceptions properly - show the actual error message
            Log::warning('Login validation failed', [
                'email' => $request->email,
                'errors' => $e->errors(),
                'ip' => $request->ip()
            ]);

            return redirect()->back()
                ->withInput($request->only('email'))
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            // Handle all other exceptions
            Log::error('Login error: ' . $e->getMessage(), [
                'email' => $request->email,
                'ip' => $request->ip(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Login failed. Please try again.']);
        }
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

    /**
     * Check if the intended URL is allowed for this user
     */
    private function isIntendedUrlAllowed(string $intended, User $user): bool
    {
        // Define allowed URL patterns for each user type
        $allowedPatterns = [
            'admin' => ['/admin/*', '/dashboard', '/profile', '/admin/staff*'],
            'customer' => ['/customer/*', '/dashboard', '/profile'],
            'delivery' => ['/delivery/*', '/dashboard', '/profile'],
            'pickup' => ['/pickup/*', '/dashboard', '/profile'],
        ];

        $userType = $user->user_type;
        $allowedForUser = $allowedPatterns[$userType] ?? ['/dashboard', '/profile'];

        foreach ($allowedForUser as $pattern) {
            if (fnmatch($pattern, $intended)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
