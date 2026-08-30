<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\StaffPendingApproval;
use App\Mail\NewStaffRegistrationAdminAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class StaffRegistrationController extends Controller
{
    public function showRegistrationForm(Request $request)
    {
        $token = $request->query('token');
        if (!$token || !$this->isValidInviteToken($token)) {
            abort(404, 'Invalid or expired registration link.');
        }

        return view('auth.staff-register', ['token' => $token]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => ['required', 'in:admin,delivery,pickup'],
        ]);

        if (!$this->isValidInviteToken($validated['token'])) {
            return back()->with('error', 'Invalid or expired registration link.');
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'user_type' => $validated['user_type'],
            'is_active' => false,
            'approved_at' => null,
        ]);

        $this->notifyAdminsAboutRegistration($user);

        Mail::to($user->email)->send(new StaffPendingApproval($user));

        return redirect()->route('staff.registration.pending')
            ->with('success', 'Registration submitted! Please wait for admin approval.');
    }

    public function pending()
    {
        return view('auth.staff-pending');
    }

    public function inactive()
    {
        return view('auth.account-inactive');
    }

    private function isValidInviteToken($token): bool
    {
        $validToken = env('STAFF_REGISTRATION_TOKEN', 'vuka-staff-2024');
        return hash_equals($validToken, $token);
    }

    private function notifyAdminsAboutRegistration(User $newUser): void
    {
        $admins = User::where('user_type', 'admin')->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new NewStaffRegistrationAdminAlert($newUser, $admin));
        }
    }
}
