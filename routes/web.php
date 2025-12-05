<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ShopDashboardController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CommissionLevelController;
use App\Http\Controllers\UserKycController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\ActivationKeyController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\IncomeSettingsController;



Route::get('/', function () {
    return redirect()->route('login');
});



Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/resend-password', [AuthController::class, 'resendPasswordform'])->name('resendPasswordform');
Route::post('/resend-password', [AuthController::class, 'resendPassword'])->name('resend.password');

Route::get('management/shop/create', [ShopController::class, 'create'])->name('shop.create');
Route::post('management/shop/store', [ShopController::class, 'store'])->name('shop.store');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

use App\Http\Controllers\ManagementLoginController;

// Management Login Routes
Route::prefix('management')->group(function () {
    
    // Show login form
    Route::get('/login', [ManagementLoginController::class, 'showLoginForm'])->name('management.login');

    // Handle login submission
    Route::post('/login', [ManagementLoginController::class, 'login'])->name('management.login.submit');

    // Logout
    Route::post('/logout', [ManagementLoginController::class, 'logout'])->name('management.logout');

    // Dashboard (Protected Route)
    Route::middleware('auth:management')->group(function () {
        Route::get('/dashboard', [ManagementLoginController::class, 'index'])->name('management.dashboard');
        Route::get('create', [ManagementLoginController::class, 'create'])->name('management.create');
        Route::post('store', [ManagementLoginController::class, 'store'])->name('management.store');

        Route::get('/shops', [ShopController::class, 'index'])->name('management.shops.index');
        Route::post('/shops/{shopId}/deduct-commission', [ShopController::class, 'deductCommission'])->name('shops.deductCommission');

        
        
        Route::get('/shops/{shop}/edit', [ShopController::class, 'edit'])->name('shop.edit');
        Route::put('/shops/{shop}', [ShopController::class, 'update'])->name('shop.update');
        Route::delete('/shops/{shop}', [ShopController::class, 'destroy'])->name('shop.destroy');
        Route::post('/shops/{shop}/change-password', [ShopController::class, 'changePassword'])->name('shop.changePassword');
        Route::prefix('commission-levels')->name('commission-levels.')->group(function () {
        Route::get('/', [CommissionLevelController::class, 'index'])->name('index');
        Route::post('/', [CommissionLevelController::class, 'store'])->name('store');
        Route::put('/{commissionLevel}', [CommissionLevelController::class, 'update'])->name('update');
        Route::delete('/{commissionLevel}', [CommissionLevelController::class, 'destroy'])->name('destroy');
        

    });
        Route::prefix('income-settings')->name('income-settings.')->group(function () {
            Route::get('/', [IncomeSettingsController::class, 'index'])->name('index');
            Route::put('/', [IncomeSettingsController::class, 'update'])->name('update');
            Route::post('/reset', [IncomeSettingsController::class, 'reset'])->name('reset');
        });
    });

});


// Grouped under middleware for admin (management)
Route::middleware(['auth:management'])->group(function () {
    // Show form to assign keys
    Route::get('/activation-keys/assign', [ActivationKeyController::class, 'create'])->name('activation-keys.assign.form');
    // Handle assignment POST
    Route::post('/activation-keys/assign', [ActivationKeyController::class, 'assignToUser'])->name('activation-keys.assign');
    // Optional: View assigned keys
    Route::get('/activation-keys', [ActivationKeyController::class, 'index'])->name('activation-keys.index');
    Route::resource('products', ProductController::class)->except(['create','edit','show']);
});


Route::middleware(['auth'])->group(function () {
    Route::get('/kyc/create', [UserKycController::class, 'create'])->name('kyc.create');
    Route::post('/kyc/store', [UserKycController::class, 'store'])->name('kyc.store');
    Route::get('/kyc/edit', [UserKycController::class, 'edit'])->name('kyc.edit');
    Route::post('/kyc/update', [UserKycController::class, 'update'])->name('kyc.update');
});



Route::middleware(['auth', 'check.kyc'])->group(function () {
    Route::get('/showEarnings', [DashboardController::class, 'showEarnings'])->name('showEarnings');
    Route::get('/income-report', [DashboardController::class, 'incomeReport'])->name('income.report');
    Route::get('/tree', [DashboardController::class, 'tree'])->name('tree');
    Route::get('/table', [DashboardController::class, 'table'])->name('table');
    Route::get('/cashback', [DashboardController::class, 'cashbackOverview'])->name('cashback');
    Route::get('/my-activation-keys', [ActivationKeyController::class, 'userIndex'])->name('activation-keys.user.index');
    Route::post('/activation-keys/use', [ActivationKeyController::class, 'useKey'])->name('activation-keys.use');
    Route::post('/activation-keys/transfer', [ActivationKeyController::class, 'transferKey'])->name('activation-keys.transfer');

});

Route::prefix('shop')->group(function () {
    Route::get('/login', [ShopController::class, 'showLoginForm'])->name('shop.login');
    Route::post('/login', [ShopController::class, 'login'])->name('shop.login.submit');

    Route::middleware('auth:shop')->group(function () {
        Route::get('/dashboard', [ShopDashboardController::class, 'index'])->name('shop.dashboard');
        Route::post('/transactions', [ShopDashboardController::class, 'storeTransaction'])->name('shop.transaction.store');
        Route::post('/logout', [ShopController::class, 'logout'])->name('shop.logout');
        
        // Shop notification routes
        Route::get('/notifications', [NotificationController::class, 'getNotifications'])->name('shop.notifications.get');
        Route::get('/notifications-page', function() { return view('notifications.index'); })->name('shop.notifications.index');
        Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('shop.notifications.mark-read');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('shop.notifications.mark-all-read');

    });
});

Route::middleware(['auth:management'])->group(function () {
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/user/{user}', [AdminUserController::class, 'show'])->name('admin.users.show');
    Route::get('/admin/users/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::get('/admin/toggle-kyc/{id}', [AdminUserController::class, 'toggleKyc'])->name('admin.toggle.kyc');
    Route::get('/admin/toggle-user/{id}', [AdminUserController::class, 'toggleActive'])->name('admin.toggle.user');
    // routes/web.php (add this route)
    Route::get('/admin/users/{userId}/downline', [AdminUserController::class, 'getDownlineInfo'])->name('admin.users.downline');
    Route::patch('/shops/{shop}/toggle-status', [ShopController::class, 'toggleStatus'])->name('shop.toggle-status');


    
});


Route::middleware(['auth'])->group(function () {
    Route::post('/withdrawal/request', [WithdrawalController::class, 'request'])->name('withdrawal.request');
    
    // User notification routes
    Route::get('/notifications', [NotificationController::class, 'getNotifications'])->name('notifications.get');
    Route::get('/notifications-page', function() { return view('notifications.index'); })->name('notifications.index');
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

Route::middleware(['auth:management'])->group(function () {
    Route::get('/management/withdrawals', [WithdrawalController::class, 'index'])->name('management.withdrawals');
    Route::post('/management/withdrawals/{id}/approve', [WithdrawalController::class, 'approve'])->name('management.withdrawals.approve');
    Route::post('/management/withdrawals/{id}/reject', [WithdrawalController::class, 'reject'])->name('management.withdrawals.reject');
    
    // Admin notification management routes
    Route::prefix('admin/notifications')->name('admin.notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/create', [NotificationController::class, 'create'])->name('create');
        Route::post('/', [NotificationController::class, 'store'])->name('store');
        Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::get('/recipients', [NotificationController::class, 'getRecipients'])->name('recipients');
    });
});
Route::get('/referral-user/{code}', [App\Http\Controllers\ActivationKeyController::class, 'getUserByReferral']);

// Test route for debugging notifications
Route::get('/test-notifications', function() {
    $user = Auth::user();
    $notifications = App\Models\Notification::where('recipient_type', 'user')
        ->where('recipient_id', $user->id)
        ->latest()
        ->limit(10)
        ->get();
    
    return response()->json([
        'user_id' => $user ? $user->id : null,
        'user_name' => $user ? $user->name : null,
        'notifications' => $notifications,
        'unread_count' => $notifications->where('is_read', false)->count()
    ]);
})->middleware('auth');
