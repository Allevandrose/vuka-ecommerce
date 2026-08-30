<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to Google's OAuth page.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google after authentication.
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            // Get the user from Google
            $googleUser = Socialite::driver('google')->user();

            // Log Google user data for debugging
            Log::info('Google user data', [
                'email' => $googleUser->email,
                'name' => $googleUser->name,
                'google_id' => $googleUser->id,
            ]);

            // Find or create user in database
            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                // Create a new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'user_type' => 'customer', // Default role for new users
                    'password' => bcrypt(rand(100000, 999999)),
                    'email_verified_at' => now(),
                ]);

                Log::info('New user registered via Google', [
                    'email' => $user->email,
                    'user_id' => $user->id,
                    'user_type' => $user->user_type,
                ]);
            } else {
                // Update existing user with Google ID if not set
                if (empty($user->google_id)) {
                    $user->update(['google_id' => $googleUser->id]);
                    Log::info('Google ID added to existing user', [
                        'email' => $user->email,
                        'user_id' => $user->id,
                    ]);
                }
            }

            // Log the user in
            Auth::login($user);

            // Regenerate session to prevent session fixation
            session()->regenerate();

            // Redirect directly using the user's redirect route
            $redirectUrl = $user->getRedirectRoute();

            Log::info('Google login successful, redirecting to', [
                'user_type' => $user->user_type,
                'redirect_url' => $redirectUrl,
            ]);

            return redirect($redirectUrl)->with('success', 'Successfully logged in with Google!');
        } catch (Exception $e) {
            // Log the error for debugging
            Log::error('Google login error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Redirect back to login with error message
            return redirect()->route('login')->with('error', 'Unable to login with Google. Please try again or use email login.');
        }
    }

    /**
     * Handle Google login with custom redirect.
     */
    public function redirectToGoogleWithRedirect(string $redirectTo = 'dashboard'): RedirectResponse
    {
        session(['google_redirect' => $redirectTo]);
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback with custom redirect.
     */
    public function handleGoogleCallbackWithRedirect(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'user_type' => 'customer',
                    'password' => bcrypt(rand(100000, 999999)),
                    'email_verified_at' => now(),
                ]);
            } else {
                if (empty($user->google_id)) {
                    $user->update(['google_id' => $googleUser->id]);
                }
            }

            Auth::login($user);
            session()->regenerate();

            // Get redirect from session or default to dashboard
            $redirectTo = session('google_redirect', 'dashboard');
            session()->forget('google_redirect');

            // If it's a route name, use route()
            if ($redirectTo === 'dashboard') {
                return redirect()->route('dashboard')->with('success', 'Successfully logged in with Google!');
            }

            return redirect()->route($redirectTo)->with('success', 'Successfully logged in with Google!');
        } catch (Exception $e) {
            Log::error('Google login error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Unable to login with Google. Please try again.');
        }
    }

    /**
     * Disconnect Google account from user profile.
     */
    public function disconnectGoogle(Request $request): RedirectResponse
    {
        try {
            /** @var User|null $user */
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            $user->update(['google_id' => null]);

            Log::info('User disconnected Google account', [
                'email' => $user->email,
                'user_id' => $user->id,
            ]);

            return back()->with('success', 'Google account disconnected successfully.');
        } catch (Exception $e) {
            Log::error('Google disconnect error: ' . $e->getMessage());
            return back()->with('error', 'Failed to disconnect Google account.');
        }
    }

    /**
     * Check if a user has a Google account linked.
     */
    public function checkGoogleLinked(Request $request)
    {
        try {
            /** @var User|null $user */
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'linked' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            return response()->json([
                'linked' => !empty($user->google_id),
                'email' => $user->email,
                'name' => $user->name,
            ]);
        } catch (Exception $e) {
            Log::error('Check Google link error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to check Google link status',
            ], 500);
        }
    }
}
