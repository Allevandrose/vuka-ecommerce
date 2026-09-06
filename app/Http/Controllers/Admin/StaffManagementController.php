<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\StaffRegistrationInvite;
use App\Mail\StaffAccountActivated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class StaffManagementController extends Controller
{
    public function index()
    {
        $staff = User::whereIn('user_type', ['admin', 'delivery', 'pickup'])
            ->orderBy('is_active', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        return view('admin.staff.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'user_type' => ['required', Rule::in(['admin', 'delivery', 'pickup'])],
        ]);

        $token = env('STAFF_REGISTRATION_TOKEN', 'vuka-staff-2024');

        try {
            Mail::to($validated['email'])->send(new StaffRegistrationInvite($token, $validated['user_type']));

            return redirect()->route('admin.staff.index')
                ->with('success', 'Invitation sent to staff member! They will receive an email with registration instructions.');
        } catch (\Exception $e) {
            Log::error('Failed to send staff invitation: ' . $e->getMessage());

            return redirect()->route('admin.staff.index')
                ->with('error', 'Failed to send invitation. Please try again.');
        }
    }

    public function activate(User $user)
    {
        if (!$user->isStaff()) {
            return redirect()->route('admin.staff.index')
                ->with('error', 'Only staff accounts can be activated.');
        }

        // Check if already active
        if ($user->is_active && $user->approved_at !== null) {
            return redirect()->route('admin.staff.index')
                ->with('info', "{$user->name} is already activated.");
        }

        $user->activate();

        try {
            Mail::to($user->email)->send(new StaffAccountActivated($user));
        } catch (\Exception $e) {
            Log::error('Failed to send activation email: ' . $e->getMessage());
            // Continue even if email fails
        }

        return redirect()->route('admin.staff.index')
            ->with('success', "{$user->name} has been activated successfully! They can now log in.");
    }

    public function deactivate(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.staff.index')
                ->with('error', 'You cannot deactivate your own account.');
        }

        if (!$user->isStaff()) {
            return redirect()->route('admin.staff.index')
                ->with('error', 'Only staff accounts can be deactivated.');
        }

        $user->deactivate();

        return redirect()->route('admin.staff.index')
            ->with('success', "{$user->name} has been deactivated.");
    }

    public function toggleWeekendOverride(User $user)
    {
        if (!$user->isStaff()) {
            return redirect()->route('admin.staff.index')
                ->with('error', 'Only staff accounts can have weekend override.');
        }

        $user->toggleWeekendOverride();
        $status = $user->is_weekend_override ? 'enabled' : 'disabled';

        return redirect()->route('admin.staff.index')
            ->with('success', "Weekend override {$status} for {$user->name}.");
    }

    public function edit(User $user)
    {
        if (!$user->isStaff()) {
            return redirect()->route('admin.staff.index')
                ->with('error', 'Cannot edit customer accounts.');
        }

        return view('admin.staff.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if (!$user->isStaff()) {
            return redirect()->route('admin.staff.index')
                ->with('error', 'Cannot edit customer accounts.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'user_type' => ['required', Rule::in(['admin', 'delivery', 'pickup'])],
        ]);

        $user->update($validated);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member updated successfully!');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.staff.index')
                ->with('error', 'You cannot delete your own account.');
        }

        if (!$user->isStaff()) {
            return redirect()->route('admin.staff.index')
                ->with('error', 'Cannot delete customer accounts.');
        }

        $user->delete();

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member deleted successfully!');
    }
}
