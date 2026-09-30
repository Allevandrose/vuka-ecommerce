<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\StaffManagementController;
use App\Http\Controllers\Admin\StaffRegistrationController;
use App\Http\Controllers\Admin\VendorManagementController;
use App\Http\Controllers\VendorApplicationController;
use App\Http\Controllers\VendorRegistrationController;
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
// PUBLIC VENDOR APPLICATION
// ============================================
Route::prefix('vendor')->name('vendor.')->group(function () {
    // Public application
    Route::get('/apply', [VendorApplicationController::class, 'create'])->name('apply');
    Route::post('/apply', [VendorApplicationController::class, 'store'])->name('apply.store');
    Route::get('/apply/pending', [VendorApplicationController::class, 'pending'])->name('apply.pending');

    // Invite-only registration (token in query string)
    Route::get('/register', [VendorRegistrationController::class, 'showRegistrationForm'])->name('register.form');
    Route::post('/register', [VendorRegistrationController::class, 'register'])->name('register');
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
// ADMIN VENDOR MANAGEMENT
// ============================================
Route::middleware(['auth', 'admin'])->prefix('admin/vendors')->name('admin.vendors.')->group(function () {
    Route::get('/', [VendorManagementController::class, 'index'])->name('index');
    Route::get('/{application}', [VendorManagementController::class, 'show'])->name('show');
    Route::post('/{application}/approve', [VendorManagementController::class, 'approve'])->name('approve');
    Route::post('/{application}/reject', [VendorManagementController::class, 'reject'])->name('reject');

    // Vendor user actions (uses user id, not application)
    Route::post('/user/{user}/activate', [VendorManagementController::class, 'activate'])->name('user.activate');
    Route::post('/user/{user}/deactivate', [VendorManagementController::class, 'deactivate'])->name('user.deactivate');
    Route::delete('/user/{user}', [VendorManagementController::class, 'destroy'])->name('user.destroy');
});

// ============================================
// AUTHENTICATED ROUTES
// ============================================
Route::middleware(['auth', 'staff.status'])->group(function () {
    Route::get('/dashboard', function () {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->canAccessSystem()) {
            Auth::logout();
            return redirect()->route('login')->with('error', $user->getAccessErrorMessage());
        }

        return redirect()->route($user->getDashboardRouteName());
    })->name('dashboard');

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

    Route::get('/vendor/dashboard', function () {
        return view('dashboards.vendor');
    })->middleware('vendor')->name('vendor.dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/auth/google/disconnect', [GoogleAuthController::class, 'disconnectGoogle'])->name('auth.google.disconnect');
    Route::get('/auth/google/check', [GoogleAuthController::class, 'checkGoogleLinked'])->name('auth.google.check');
});

// ============================================
// AUTH ROUTES (Laravel Breeze)
// ============================================
require __DIR__ . '/auth.php';
