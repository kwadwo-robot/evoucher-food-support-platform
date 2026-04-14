<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\Admin\VoucherController as AdminVoucher;
use App\Http\Controllers\Admin\ReportController as AdminReport;
use App\Http\Controllers\Admin\FundLoadController as AdminFundLoad;
use App\Http\Controllers\Admin\PayoutController as AdminPayout;
use App\Http\Controllers\Admin\BroadcastController as AdminBroadcast;
use App\Http\Controllers\Admin\SystemLogController as AdminSystemLog;
use App\Http\Controllers\Admin\BankDepositController as AdminBankDeposit;
use App\Http\Controllers\Admin\ShopController as AdminShop;
use App\Http\Controllers\Admin\ReportGeneratorController as AdminReportGenerator;
use App\Http\Controllers\Admin\ServiceFeeController as AdminServiceFee;
use App\Http\Controllers\Organisation\FundLoadController as OrgFundLoad;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\FoodListingController;
use App\Http\Controllers\Organisation\DashboardController as OrgDashboard;
use App\Http\Controllers\Organisation\DonationController;
use App\Http\Controllers\Organisation\VoucherController as OrgVoucher;
use App\Http\Controllers\DonationController as PublicDonationController;
use App\Http\Controllers\Recipient\DashboardController as RecipientDashboard;
use App\Http\Controllers\Recipient\VoucherController as RecipientVoucher;
use App\Http\Controllers\Recipient\CartController as RecipientCart;
use App\Http\Controllers\Recipient\ReportController as RecipientReport;
use App\Http\Controllers\Shop\DashboardController as ShopDashboard;
use App\Http\Controllers\Shop\FoodListingController as ShopListing;
use App\Http\Controllers\Shop\PayoutController as ShopPayout;
use App\Http\Controllers\Shop\ReportController as ShopReport;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SurplusClaimController;
use App\Http\Controllers\FoodBreakdownController;
use RecipientBroadcast as RecipientBroadcast;
use Illuminate\Support\Facades\Route;

// Language Switcher
Route::post('/lang/{locale}', function ($locale) {
    $supported = ['en', 'ar', 'ro', 'pl'];
    if (in_array($locale, $supported)) {
        session(['locale' => $locale]);
        return response()->json(['success' => true, 'locale' => $locale]);
    }
    return response()->json(['success' => false], 400);
})->name('lang.switch');

// Also support GET for backward compatibility
Route::get('/lang/{locale}', function ($locale) {
    $supported = ['en', 'ar', 'ro', 'pl'];
    if (in_array($locale, $supported)) {
        session(['locale' => $locale]);
        return redirect()->back();
    }
    return redirect()->back();
})->name('lang.switch.get');

Route::get("/privacy-policy", function () { return view("privacy-policy"); })->name("privacy");
Route::get("/terms-of-use", function () { return view("terms-of-use"); })->name("terms");
// Public
Route::get('/', function () { return view('welcome'); })->name('home');
Route::get('/food', [FoodListingController::class, 'index'])->name('food.index');
Route::get('/food/{id}', [FoodListingController::class, 'show'])->name('food.show');
Route::get('/shops', [ShopController::class, 'index'])->name('shops.index');
Route::get('/shops/{shop}', [ShopController::class, 'show'])->name('shops.show');

// Donations
Route::post('/api/donations/create-payment-intent', [\App\Http\Controllers\PublicDonationController::class, 'createPaymentIntent'])->name('donations.create-intent');
Route::post('/api/donations/process', [\App\Http\Controllers\PublicDonationController::class, 'confirm'])->name('donations.confirm');

// Auth
require __DIR__.'/auth.php';
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Change Password (all authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/password/change', [ChangePasswordController::class, 'show'])->name('password.change');
    Route::put('/password/change', [ChangePasswordController::class, 'update'])->name('password.change.update');
});

// Admin
Route::prefix('admin')->name('admin.')->middleware(['auth', 'approved', 'role:admin,super_admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminUser::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUser::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [AdminUser::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUser::class, 'update'])->name('users.update');
    Route::post('/users/{user}/reset-password', [AdminUser::class, 'resetPassword'])->name('users.reset-password');
    Route::patch('/users/{user}/approve', [AdminUser::class, 'approve'])->name('users.approve');
    Route::patch('/users/{user}/reject', [AdminUser::class, 'reject'])->name('users.reject');
    Route::patch('/users/{user}/toggle-active', [AdminUser::class, 'toggleActive'])->name('users.toggle');
    Route::delete('/users/{user}', [AdminUser::class, 'destroy'])->name('users.destroy');
    Route::patch('/users/{user}/role', [AdminUser::class, 'updateRole'])->name('users.role');
    Route::get('/vouchers', [AdminVoucher::class, 'index'])->name('vouchers.index');
    Route::get('/vouchers/create', [AdminVoucher::class, 'create'])->name('vouchers.create');
    Route::post('/vouchers', [AdminVoucher::class, 'store'])->name('vouchers.store');
    Route::get('/vouchers/{voucher}', [AdminVoucher::class, 'show'])->name('vouchers.show');
    Route::patch('/vouchers/{voucher}/cancel', [AdminVoucher::class, 'revoke'])->name('vouchers.revoke');
    Route::get('/listings', [AdminDashboard::class, 'listings'])->name('listings.index');
    Route::patch('/listings/{listing}/status', [AdminDashboard::class, 'updateListingStatus'])->name('listings.status');
    Route::delete('/listings/{listing}', [AdminDashboard::class, 'destroyListing'])->name('listings.destroy');
    Route::get('/redemptions', [AdminDashboard::class, 'redemptions'])->name('redemptions.index');
    Route::get('/donations', [\App\Http\Controllers\Admin\DonationController::class, 'index'])->name('donations.index');
    Route::post('/donations/sync-stripe', [\App\Http\Controllers\Admin\DonationController::class, 'syncFromStripe'])->name('donations.sync-stripe');
    Route::get('/donations/{donation}', [\App\Http\Controllers\Admin\DonationController::class, 'show'])->name('donations.show');
    Route::get('/reports', [AdminReport::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [AdminReport::class, 'export'])->name('reports.export');
    Route::get('/settings', [AdminDashboard::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminDashboard::class, 'saveSettings'])->name('settings.save');
    // Fund Loads
    Route::get('/fund-loads', [AdminFundLoad::class, 'index'])->name('fund-loads.index');
    Route::post('/fund-loads', [AdminFundLoad::class, 'store'])->name('fund-loads.store');
    Route::delete('/fund-loads/{fundLoad}', [AdminFundLoad::class, 'destroy'])->name('fund-loads.destroy');
    // Shop Payouts
    Route::get('/payouts', [AdminPayout::class, 'index'])->name('payouts.index');
    Route::get('/payouts/{id}', [AdminPayout::class, 'show'])->name('payouts.show');
    Route::patch('/payouts/{id}/approve', [AdminPayout::class, 'approve'])->name('payouts.approve');
    Route::post('/payouts/{id}/mark-paid', [AdminPayout::class, 'markPaid'])->name('payouts.mark-paid');
    Route::post('/payouts/{id}/reject', [AdminPayout::class, 'reject'])->name('payouts.reject');
    // Broadcasts
    Route::get('/broadcasts', [AdminBroadcast::class, 'index'])->name('broadcasts.index');
    Route::get('/broadcasts/create', [AdminBroadcast::class, 'create'])->name('broadcasts.create');
    Route::post('/broadcasts', [AdminBroadcast::class, 'store'])->name('broadcasts.store');
    Route::get('/broadcasts/{broadcast}', [AdminBroadcast::class, 'show'])->name('broadcasts.show');
    Route::post('/broadcasts/{broadcast}/send', [AdminBroadcast::class, 'send'])->name('broadcasts.send');
    Route::delete('/broadcasts/{broadcast}', [AdminBroadcast::class, 'destroy'])->name('broadcasts.destroy');
    Route::get('/api/users', [AdminBroadcast::class, 'getAllUsers'])->name('api.users.all');
    // System Logs
    Route::get('/logs', [AdminSystemLog::class, 'index'])->name('logs.index');
    Route::get('/logs/{log}', [AdminSystemLog::class, 'show'])->name('logs.show');
    Route::get('/logs/export', [AdminSystemLog::class, 'export'])->name('logs.export');
    // Bank Deposits
    Route::get('/bank-deposits', [AdminBankDeposit::class, 'index'])->name('bank-deposits.index');
    Route::get('/bank-deposits/{deposit}', [AdminBankDeposit::class, 'show'])->name('bank-deposits.show');
    Route::patch('/bank-deposits/{deposit}/verify', [AdminBankDeposit::class, 'verify'])->name('bank-deposits.verify');
    Route::patch('/bank-deposits/{deposit}/reject', [AdminBankDeposit::class, 'reject'])->name('bank-deposits.reject');
    // Shops
    Route::get('/shops', [AdminShop::class, 'index'])->name('shops.index');
    Route::get('/shops/{shop}', [AdminShop::class, 'show'])->name('shops.show');
    Route::get('/shops/{shop}/edit', [AdminShop::class, 'edit'])->name('shops.edit');
    Route::put('/shops/{shop}', [AdminShop::class, 'update'])->name('shops.update');
    Route::patch('/shops/{shop}/suspend', [AdminShop::class, 'suspend'])->name('shops.suspend');
    Route::patch('/shops/{shop}/reactivate', [AdminShop::class, 'reactivate'])->name('shops.reactivate');
    Route::delete('/shops/{shop}', [AdminShop::class, 'destroy'])->name('shops.destroy');
    // Food Breakdown
    Route::get('/food-breakdown', [FoodBreakdownController::class, 'adminBreakdown'])->name('food-breakdown');
    // Reports
    Route::get('/reports/vouchers', [AdminReportGenerator::class, 'vouchersReport'])->name('reports.vouchers');
    Route::get('/reports/redemptions', [AdminReportGenerator::class, 'redemptionsReport'])->name('reports.redemptions');
    Route::get('/reports/users', [AdminReportGenerator::class, 'usersReport'])->name('reports.users');
    Route::get('/reports/food-listings', [AdminReportGenerator::class, 'foodListingsReport'])->name('reports.food-listings');
    // Service Fees
    Route::get('/service-fees', [AdminServiceFee::class, 'index'])->name('service-fees.index');
    Route::get('/service-fees/{id}', [AdminServiceFee::class, 'show'])->name('service-fees.show');
    Route::get('/service-fees/settings', [AdminServiceFee::class, 'settings'])->name('service-fees.settings');
    Route::post('/service-fees/settings', [AdminServiceFee::class, 'updatePercentage'])->name('service-fees.update-percentage');
    Route::get('/service-fees/export', [AdminServiceFee::class, 'export'])->name('service-fees.export');
});

// Shop
Route::prefix('shop')->name('shop.')->middleware(['auth', 'approved', 'role:local_shop'])->group(function () {
    Route::get('/dashboard', [ShopDashboard::class, 'index'])->name('dashboard');
    Route::get('/listings', [ShopListing::class, 'index'])->name('listings.index');
    Route::get('/listings/create', [ShopListing::class, 'create'])->name('listings.create');
    Route::post('/listings', [ShopListing::class, 'store'])->name('listings.store');
    Route::get('/listings/{listing}/edit', [ShopListing::class, 'edit'])->name('listings.edit');
    Route::put('/listings/{listing}', [ShopListing::class, 'update'])->name('listings.update');
    Route::delete('/listings/{listing}', [ShopListing::class, 'destroy'])->name('listings.destroy');
    // Voucher Verification
    Route::get('/verify', [ShopDashboard::class, 'verifyVoucher'])->name('verify');
    Route::post('/verify/lookup', [ShopDashboard::class, 'lookupVoucher'])->name('verify.lookup');
    Route::post('/verify/accept', [ShopDashboard::class, 'acceptVoucher'])->name('verify.accept');
    Route::post('/verify/accept-direct', [ShopDashboard::class, 'acceptVoucherDirect'])->name('verify.accept-direct');
    Route::get('/redemptions', [ShopDashboard::class, 'redemptions'])->name('redemptions');
    // Payouts
    Route::get('/payouts', [ShopPayout::class, 'index'])->name('payouts.index');
    Route::post('/payouts/bank-details', [ShopPayout::class, 'saveBankDetails'])->name('payouts.bank-details');
    Route::post('/payouts/request', [ShopPayout::class, 'requestPayout'])->name('payouts.request');
    Route::get('/payouts/{id}', [ShopPayout::class, 'show'])->name('payouts.show');
    // Reports
    Route::get('/reports', [ShopReport::class, 'index'])->name('reports.index');
});

// Recipient
Route::prefix('recipient')->name('recipient.')->middleware(['auth', 'role:recipient'])->group(function () {
    Route::get('/dashboard', [RecipientDashboard::class, 'index'])->name('dashboard');
    Route::get('/vouchers', [RecipientVoucher::class, 'index'])->name('vouchers.index');
    Route::get('/vouchers/{voucher}', [RecipientVoucher::class, 'show'])->name('vouchers.show');
    Route::get('/cart', [RecipientCart::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{listing}', [RecipientCart::class, 'add'])->name('cart.add');
    Route::post('/cart/remove/{listing}', [RecipientCart::class, 'remove'])->name('cart.remove');
    Route::post('/cart/checkout', [RecipientCart::class, 'checkout'])->name('cart.checkout');
    Route::post('/cart/clear', [RecipientCart::class, 'clear'])->name('cart.clear');
    // Reports
    Route::get('/reports', [RecipientReport::class, 'index'])->name('reports.index');
    Route::get('/reports/export-excel', [RecipientReport::class, 'exportExcel'])->name('reports.export-excel');
    // Broadcasts
    Route::get("/broadcasts/{broadcast}", [RecipientBroadcast::class, "show"])->name("broadcasts.show");
    Route::post("/broadcasts/{broadcast}/mark-read", [RecipientBroadcast::class, "markRead"])->name("broadcasts.mark-read");
});

// VCFSE
Route::prefix('vcfse')->name('vcfse.')->middleware(['auth', 'approved', 'role:vcfse'])->group(function () {
    Route::get('/dashboard', [OrgDashboard::class, 'vcfseDashboard'])->name('dashboard');
    Route::get('/fund-loads', [OrgFundLoad::class, 'index'])->name('fund-loads.index');
    Route::post('/fund-loads', [OrgFundLoad::class, 'store'])->name('fund-loads.store');
    Route::delete('/fund-loads/{fundLoad}', [OrgFundLoad::class, 'destroy'])->name('fund-loads.destroy');
    Route::get('/vouchers', [OrgVoucher::class, 'index'])->name('vouchers.index');
    Route::get('/vouchers/create', [OrgVoucher::class, 'create'])->name('vouchers.create');
    Route::post('/vouchers', [OrgVoucher::class, 'store'])->name('vouchers.store');
    Route::get('/vouchers/{voucher}', [OrgVoucher::class, 'show'])->name('vouchers.show');
    Route::patch('/vouchers/{voucher}/cancel', [OrgVoucher::class, 'revoke'])->name('vouchers.revoke');
    Route::get('/reports/vouchers/excel', [\App\Http\Controllers\Organisation\ReportsController::class, 'exportVouchersExcel'])->name('reports.vouchers-excel');
    Route::get('/reports/vouchers/pdf', [\App\Http\Controllers\Organisation\ReportsController::class, 'exportVouchersPdf'])->name('reports.vouchers-pdf');
});

// School/Care
Route::prefix('school')->name('school.')->middleware(['auth', 'approved', 'role:school_care'])->group(function () {
    Route::get('/dashboard', [OrgDashboard::class, 'schoolDashboard'])->name('dashboard');
    Route::get('/fund-loads', [OrgFundLoad::class, 'index'])->name('fund-loads.index');
    Route::post('/fund-loads', [OrgFundLoad::class, 'store'])->name('fund-loads.store');
    Route::delete('/fund-loads/{fundLoad}', [OrgFundLoad::class, 'destroy'])->name('fund-loads.destroy');
    Route::get('/vouchers', [OrgVoucher::class, 'index'])->name('vouchers.index');
    Route::get('/vouchers/create', [OrgVoucher::class, 'create'])->name('vouchers.create');
    Route::post('/vouchers', [OrgVoucher::class, 'store'])->name('vouchers.store');
    Route::get('/vouchers/{voucher}', [OrgVoucher::class, 'show'])->name('vouchers.show');
    Route::patch('/vouchers/{voucher}/cancel', [OrgVoucher::class, 'revoke'])->name('vouchers.revoke');
    Route::get('/reports/vouchers/excel', [\App\Http\Controllers\Organisation\ReportsController::class, 'exportVouchersExcel'])->name('reports.vouchers-excel');
    Route::get('/reports/vouchers/pdf', [\App\Http\Controllers\Organisation\ReportsController::class, 'exportVouchersPdf'])->name('reports.vouchers-pdf');
});

// Notifications
Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::get('/unread', [NotificationController::class, 'getUnread'])->name('unread');
    Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
    Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
    Route::delete('/{notification}', [NotificationController::class, 'delete'])->name('delete');
    Route::delete('/delete-all', [NotificationController::class, 'deleteAll'])->name('delete-all');
});

// Stripe Webhook
Route::post('/stripe/webhook', [DonationController::class, 'webhook'])->name('stripe.webhook');
