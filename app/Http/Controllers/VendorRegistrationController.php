<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VendorApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class VendorRegistrationController extends Controller
{
    /**
     * Show the vendor registration form.
     * Requires a valid invite token in the query string.
     */
    public function showRegistrationForm(Request $request): View
    {
        $token = $request->query('token');

        if (!$token || !$this->isValidInviteToken($token)) {
            abort(404, 'Invalid or expired registration link.');
        }

        // Find the approved application matching the email in session (if we stored it)
        // For simplicity, we require the applicant to re-enter their email on the form
        // and cross-check against an approved application.
        return view('auth.vendor-register', ['token' => $token]);
    }

    /**
     * Handle the vendor registration submission.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!$this->isValidInviteToken($validated['token'])) {
            return back()->with('error', 'Invalid or expired registration link.');
        }

        // Verify this email belongs to an approved vendor application
        $application = VendorApplication::where('email', $validated['email'])
            ->where('status', 'approved')
            ->first();

        if (!$application) {
            return back()
                ->withInput($request->only('name', 'email'))
                ->withErrors([
                    'email' => 'No approved vendor application found for this email. Please use the email you applied with.',
                ]);
        }

        // Create the vendor user, pending final activation
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'user_type' => 'vendor',
            'is_active' => false,
            'approved_at' => null, // admin will activate after review
            'email_verified_at' => now(),

            // Copy vendor profile fields from the application
            'shop_name' => $application->shop_name,
            'shop_address' => $application->shop_address,
            'shop_latitude' => $application->shop_latitude,
            'shop_longitude' => $application->shop_longitude,
        ]);

        Log::info('Vendor registered', [
            'user_id' => $user->id,
            'email' => $user->email,
            'application_id' => $application->id,
        ]);

        // Redirect to login with a success message — admin still needs to activate
        return redirect()->route('login')
            ->with('success', 'Vendor account created! Your account is now pending final activation. You will be notified once it is active.');
    }

    /**
     * Validate the invite token against the env-configured value.
     */
    private function isValidInviteToken(string $token): bool
    {
        $validToken = env('VENDOR_REGISTRATION_TOKEN', 'vuka-vendor-2026');
        return hash_equals($validToken, $token);
    }
}
