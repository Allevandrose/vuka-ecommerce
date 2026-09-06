<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\StaffManagementController;
use App\Http\Controllers\Admin\StaffRegistrationController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ============================================
// PUBLIC ROUTES
// ============================================
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ============================================
// GOOGLE AUTH ROUTES
// ============================================
Route::prefix('auth/google')->name('auth.google.')->group(function () {
    Route::get('/', [GoogleAuthController::class, 'redirectToGoogle'])->name('redirect');
    Route::get('/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('callback');
    Route::get('/redirect/{redirectTo?}', [GoogleAuthController::class, 'redirectToGoogleWithRedirect'])->name('redirect.custom');
    Route::get('/callback/custom', [GoogleAuthController::class, 'handleGoogleCallbackWithRedirect'])->name('callback.custom');
});

// ============================================
// STAFF REGISTRATION (Private URL)
// ============================================
Route::get('/staff/register', [StaffRegistrationController::class, 'showRegistrationForm'])
    ->name('staff.register.form');
Route::post('/staff/register', [StaffRegistrationController::class, 'register'])
    ->name('staff.register');
Route::get('/staff/pending', [StaffRegistrationController::class, 'pending'])
    ->name('staff.registration.pending');
Route::get('/account/inactive', [StaffRegistrationController::class, 'inactive'])
    ->name('account.inactive');

// ============================================
// ADMIN STAFF MANAGEMENT
// ============================================
Route::middleware(['auth', 'admin'])->prefix('admin/staff')->name('admin.staff.')->group(function () {
    Route::get('/', [StaffManagementController::class, 'index'])->name('index');
    Route::get('/create', [StaffManagementController::class, 'create'])->name('create');
    Route::post('/store', [StaffManagementController::class, 'store'])->name('store');
    Route::get('/{user}/edit', [StaffManagementController::class, 'edit'])->name('edit');
    Route::put('/{user}', [StaffManagementController::class, 'update'])->name('update');
    Route::delete('/{user}', [StaffManagementController::class, 'destroy'])->name('destroy');
    Route::post('/{user}/activate', [StaffManagementController::class, 'activate'])->name('activate');
    Route::post('/{user}/deactivate', [StaffManagementController::class, 'deactivate'])->name('deactivate');
    Route::post('/{user}/toggle-weekend-override', [StaffManagementController::class, 'toggleWeekendOverride'])->name('toggle-weekend-override');
});

// ============================================
// AUTHENTICATED ROUTES
// ============================================
Route::middleware(['auth', 'staff.status'])->group(function () {
    // Dashboard route - now properly redirects
    Route::get('/dashboard', function () {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user can access the system
        if (!$user->canAccessSystem()) {
            Auth::logout();
            return redirect()->route('login')->with('error', $user->getAccessErrorMessage());
        }

        // Redirect to the appropriate dashboard using route name
        return redirect()->route($user->getDashboardRouteName());
    })->name('dashboard');

    // User type specific dashboards
    Route::get('/admin/dashboard', function () {
        return view('dashboards.admin');
    })->middleware('admin')->name('admin.dashboard');

    Route::get('/customer/dashboard', function () {
        return view('dashboards.customer');
    })->middleware('customer')->name('customer.dashboard');

    Route::get('/delivery/dashboard', function () {
        return view('dashboards.delivery');
    })->middleware('delivery')->name('delivery.dashboard');

    Route::get('/pickup/dashboard', function () {
        return view('dashboards.pickup');
    })->middleware('pickup')->name('pickup.dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Google account management
    Route::post('/auth/google/disconnect', [GoogleAuthController::class, 'disconnectGoogle'])->name('auth.google.disconnect');
    Route::get('/auth/google/check', [GoogleAuthController::class, 'checkGoogleLinked'])->name('auth.google.check');
});

// ============================================
// AUTH ROUTES (Laravel Breeze)
// ============================================
require __DIR__ . '/auth.php';
