<?php

namespace App\Http\Controllers;

use App\Mail\VendorApplicationReceived;
use App\Models\User;
use App\Models\VendorApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class VendorApplicationController extends Controller
{
    /**
     * Show the public vendor application form.
     */
    public function create(): View
    {
        return view('auth.vendor-apply');
    }

    /**
     * Handle a submitted vendor application.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'max:255', 'unique:vendor_applications,email'],
            'phone'            => ['nullable', 'string', 'max:30'],
            'shop_name'        => ['required', 'string', 'max:255'],
            'shop_address'     => ['required', 'string', 'max:500'],
            'shop_latitude'    => ['nullable', 'numeric', 'between:-90,90'],
            'shop_longitude'   => ['nullable', 'numeric', 'between:-180,180'],
            'product_categories' => ['nullable', 'string', 'max:2000'],
            'message'          => ['nullable', 'string', 'max:2000'],
        ]);

        $application = VendorApplication::create([
            ...$validated,
            'status' => 'pending',
        ]);

        $this->notifyAdmins($application);

        return redirect()
            ->route('vendor.apply.pending')
            ->with('success', 'Your application has been received. Our team will review it shortly.');
    }

    /**
     * Show the "application received" page.
     */
    public function pending(): View
    {
        return view('auth.vendor-pending');
    }

    /**
     * Notify all admins of a new vendor application.
     */
    private function notifyAdmins(VendorApplication $application): void
    {
        $admins = User::where('user_type', 'admin')->get();

        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(new VendorApplicationReceived($application, $admin));
            } catch (\Throwable $e) {
                Log::error('Failed to send vendor application alert to admin: ' . $e->getMessage(), [
                    'admin_id' => $admin->id,
                    'application_id' => $application->id,
                ]);
                // Continue — one failed email shouldn't block the application
            }
        }
    }
}
