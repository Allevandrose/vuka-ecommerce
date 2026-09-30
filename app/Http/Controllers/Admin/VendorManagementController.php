<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\VendorAccountActivated;
use App\Mail\VendorRegistrationInvite;
use App\Models\User;
use App\Models\VendorApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class VendorManagementController extends Controller
{
    // ============================================
    // APPLICATIONS (pending review)
    // ============================================

    /**
     * List all vendor applications + existing vendors.
     */
    public function index(): View
    {
        $applications = VendorApplication::orderBy('status')
            ->orderBy('created_at', 'desc')
            ->get();

        $vendors = User::where('user_type', 'vendor')
            ->orderBy('is_active', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.vendors.index', compact('applications', 'vendors'));
    }

    /**
     * Show a single application for review.
     */
    public function show(VendorApplication $application): View
    {
        return view('admin.vendors.show', compact('application'));
    }

    /**
     * Approve an application: mark reviewed, send invite email.
     */
    public function approve(Request $request, VendorApplication $application): RedirectResponse
    {
        if (!$application->isPending()) {
            return redirect()->route('admin.vendors.index')
                ->with('error', 'This application has already been ' . $application->status . '.');
        }

        // Prevent duplicate vendor users if application email already exists
        if (User::where('email', $application->email)->exists()) {
            return redirect()->route('admin.vendors.index')
                ->with('error', 'A user with this email already exists. Cannot approve.');
        }

        $application->update([
            'status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
            'admin_notes' => $request->input('admin_notes'),
        ]);

        // Issue a vendor registration invite
        $token = env('VENDOR_REGISTRATION_TOKEN', 'vuka-vendor-2026');

        try {
            Mail::to($application->email)->send(
                new VendorRegistrationInvite($token, $application)
            );

            return redirect()->route('admin.vendors.index')
                ->with('success', "Application approved. Invitation sent to {$application->email}.");
        } catch (\Throwable $e) {
            Log::error('Failed to send vendor invite: ' . $e->getMessage(), [
                'application_id' => $application->id,
            ]);

            return redirect()->route('admin.vendors.index')
                ->with('error', 'Application approved, but the invite email failed to send. Check logs.');
        }
    }

    /**
     * Reject an application.
     */
    public function reject(Request $request, VendorApplication $application): RedirectResponse
    {
        if (!$application->isPending()) {
            return redirect()->route('admin.vendors.index')
                ->with('error', 'This application has already been ' . $application->status . '.');
        }

        $application->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
            'admin_notes' => $request->input('admin_notes'),
        ]);

        return redirect()->route('admin.vendors.index')
            ->with('success', "Application from {$application->shop_name} has been rejected.");
    }

    // ============================================
    // VENDOR USERS (already registered)
    // ============================================

    /**
     * Activate a vendor account (lets them log in) and notify them by email.
     */
    public function activate(User $user): RedirectResponse
    {
        if (!$user->isVendor()) {
            return redirect()->route('admin.vendors.index')
                ->with('error', 'Only vendor accounts can be activated here.');
        }

        if ($user->is_active && $user->approved_at !== null) {
            return redirect()->route('admin.vendors.index')
                ->with('info', "{$user->name} is already activated.");
        }

        $user->activate();

        // Notify the vendor — failure is logged but does not block activation
        try {
            Mail::to($user->email)->send(new VendorAccountActivated($user));
        } catch (\Throwable $e) {
            Log::error('Failed to send vendor activation email: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return redirect()->route('admin.vendors.index')
                ->with('success', "{$user->name} has been activated, but the notification email failed to send. Check logs.");
        }

        return redirect()->route('admin.vendors.index')
            ->with('success', "{$user->name} has been activated and notified by email.");
    }

    /**
     * Deactivate a vendor account.
     */
    public function deactivate(User $user): RedirectResponse
    {
        if (!$user->isVendor()) {
            return redirect()->route('admin.vendors.index')
                ->with('error', 'Only vendor accounts can be deactivated here.');
        }

        $user->deactivate();

        return redirect()->route('admin.vendors.index')
            ->with('success', "{$user->name} has been deactivated.");
    }

    /**
     * Delete a vendor user.
     */
    public function destroy(User $user): RedirectResponse
    {
        if (!$user->isVendor()) {
            return redirect()->route('admin.vendors.index')
                ->with('error', 'Only vendor accounts can be deleted here.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.vendors.index')
            ->with('success', "{$name} has been deleted.");
    }
}
