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
use App\Http\Controllers\Admin\ReportGeneratorController as AdminReportGenerator;
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
    // Shops Management
    Route::get('/shops', [\App\Http\Controllers\Admin\AdminShopController::class, 'index'])->name('shops.index');
    Route::get('/shops/{shop}', [\App\Http\Controllers\Admin\AdminShopController::class, 'show'])->name('shops.show');
    Route::patch('/shops/{shop}/approve', [\App\Http\Controllers\Admin\AdminShopController::class, 'approve'])->name('shops.approve');
    Route::patch('/shops/{shop}/reject', [\App\Http\Controllers\Admin\AdminShopController::class, 'reject'])->name('shops.reject');
    Route::patch('/shops/{shop}/toggle-active', [\App\Http\Controllers\Admin\AdminShopController::class, 'toggleActive'])->name('shops.toggle');
    Route::delete('/shops/{shop}', [\App\Http\Controllers\Admin\AdminShopController::class, 'destroy'])->name('shops.destroy');
    Route::get('/users', [AdminUser::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUser::class, 'show'])->name('users.show');
    Route::patch('/users/{user}/approve', [AdminUser::class, 'approve'])->name('users.approve');
    Route::patch('/users/{user}/reject', [AdminUser::class, 'reject'])->name('users.reject');
    Route::patch('/users/{user}/toggle-active', [AdminUser::class, 'toggleActive'])->name('users.toggle');
    Route::delete('/users/{user}', [AdminUser::class, 'destroy'])->name('users.destroy');
    Route::patch('/users/{user}/role', [AdminUser::class, 'updateRole'])->name('users.role');
    Route::get('/vouchers', [AdminVoucher::class, 'index'])->name('vouchers.index');
    Route::get('/vouchers/create', [AdminVoucher::class, 'create'])->name('vouchers.create');
    Route::post('/vouchers', [AdminVoucher::class, 'store'])->name('vouchers.store');
    Route::get('/vouchers/{voucher}', [AdminVoucher::class, 'show'])->name('vouchers.show');
    Route::patch('/vouchers/{voucher}/cancel', [AdminVoucher::class, 'cancel'])->name('vouchers.cancel');
    Route::get('/listings', [AdminDashboard::class, 'listings'])->name('listings.index');
    Route::patch('/listings/{listing}/status', [AdminDashboard::class, 'updateListingStatus'])->name('listings.status');
    Route::delete('/listings/{listing}', [AdminDashboard::class, 'destroyListing'])->name('listings.destroy');
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
    // System Logs
    Route::get('/logs', [AdminSystemLog::class, 'index'])->name('logs.index');
    Route::get('/logs/{log}', [AdminSystemLog::class, 'show'])->name('logs.show');
    Route::get('/logs/export', [AdminSystemLog::class, 'export'])->name('logs.export');
    // Bank Deposits
    Route::get('/bank-deposits', [AdminBankDeposit::class, 'index'])->name('bank-deposits.index');
    Route::get('/bank-deposits/{deposit}', [AdminBankDeposit::class, 'show'])->name('bank-deposits.show');
    Route::patch('/bank-deposits/{deposit}/verify', [AdminBankDeposit::class, 'verify'])->name('bank-deposits.verify');
    Route::patch('/bank-deposits/{deposit}/reject', [AdminBankDeposit::class, 'reject'])->name('bank-deposits.reject');
    // Food Breakdown
    Route::get('/food-breakdown', [FoodBreakdownController::class, 'adminBreakdown'])->name('food-breakdown');
    // Reports
    Route::get('/reports/vouchers', [AdminReportGenerator::class, 'vouchersReport'])->name('reports.vouchers');
    Route::get('/reports/redemptions', [AdminReportGenerator::class, 'redemptionsReport'])->name('reports.redemptions');
    Route::get('/reports/users', [AdminReportGenerator::class, 'usersReport'])->name('reports.users');
    Route::get('/reports/food-listings', [AdminReportGenerator::class, 'foodListingsReport'])->name('reports.food-listings');
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
    Route::patch('/listings/{listing}/mark-collected', [ShopListing::class, 'markCollected'])->name('listings.collected');
    Route::get('/redemptions', [ShopDashboard::class, 'redemptions'])->name('redemptions');
    Route::get('/verify', [ShopDashboard::class, 'verifyVoucher'])->name('verify');
    Route::post('/verify/lookup', [ShopDashboard::class, 'lookupVoucher'])->name('verify.lookup');
    Route::post('/verify/accept', [ShopDashboard::class, 'acceptVoucher'])->name('verify.accept');
    Route::post('/verify/reject', [ShopDashboard::class, 'rejectVoucher'])->name('verify.reject');
    Route::post('/redemptions/{id}/confirm', [ShopDashboard::class, 'confirmRedemption'])->name('redemptions.confirm');
    Route::patch('/redemptions/{id}/confirm', [ShopDashboard::class, 'confirmRedemption']);
    Route::get('/profile', [ShopDashboard::class, 'profile'])->name('profile');
    Route::put('/profile', [ShopDashboard::class, 'updateProfile'])->name('profile.update');
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
    Route::get('/food', [RecipientDashboard::class, 'browse'])->name('food.browse');
    Route::get('/food/{listing}', [RecipientDashboard::class, 'showListing'])->name('food.show');
    Route::post('/food/{listing}/redeem', [RecipientVoucher::class, 'redeem'])->name('food.redeem');
    Route::get('/vouchers', [RecipientVoucher::class, 'index'])->name('vouchers');
    Route::get('/vouchers/{voucher}', [RecipientVoucher::class, 'show'])->name('vouchers.show');
    Route::get('/history', [RecipientDashboard::class, 'history'])->name('history');
    // Shopping Cart
    Route::get('/cart', [RecipientCart::class, 'index'])->name('cart');
    Route::post('/cart/checkout', [RecipientCart::class, 'checkout'])->name('cart.checkout');
    Route::post('/cart/{listing}', [RecipientCart::class, 'add'])->name('cart.add');
    Route::delete('/cart/{listing}', [RecipientCart::class, 'remove'])->name('cart.remove');
    Route::delete('/cart', [RecipientCart::class, 'clear'])->name('cart.clear');
    Route::get('/profile', [RecipientDashboard::class, 'profile'])->name('profile');
    Route::put('/profile', [RecipientDashboard::class, 'updateProfile'])->name('profile.update');
    // Reports
    Route::get('/reports/export-pdf', [RecipientReport::class, 'exportPDF'])->name('reports.export-pdf');
    Route::get('/reports/export-excel', [RecipientReport::class, 'exportExcel'])->name('reports.export-excel');
});

// VCFSE
Route::prefix('vcfse')->name('vcfse.')->middleware(['auth', 'approved', 'role:vcfse'])->group(function () {
    Route::get('/dashboard', [OrgDashboard::class, 'vcfseDashboard'])->name('dashboard');
    Route::get('/food', [OrgDashboard::class, 'browseFood'])->name('food');
    Route::post('/food/{foodListingId}/claim', [SurplusClaimController::class, 'claim'])->name('food.claim');
    Route::get('/donate', [DonationController::class, 'showDonateForm'])->name('donate');
    Route::post('/donate', [DonationController::class, 'storeDonation'])->name('donate.store');
    Route::get('/donations', [OrgDashboard::class, 'donations'])->name('donations');
    Route::get('/fund-load', [OrgFundLoad::class, 'showLoadForm'])->name('fund-load');
    Route::post('/fund-load/create-intent', [OrgFundLoad::class, 'createPaymentIntent'])->name('fund-load.create-intent');
    Route::post('/fund-load/confirm', [OrgFundLoad::class, 'confirmPayment'])->name('fund-load.confirm');
    Route::get('/fund-load/history', [OrgFundLoad::class, 'loadHistory'])->name('fund-load.history');
    Route::get('/food-breakdown', [FoodBreakdownController::class, 'vcfseBreakdown'])->name('food-breakdown');
    Route::get('/reports', [\App\Http\Controllers\Organisation\ReportsController::class, 'index'])->name('reports');
    Route::get('/reports/fund-loads/excel', [\App\Http\Controllers\Organisation\ReportsController::class, 'exportFundLoadsExcel'])->name('reports.fund-loads-excel');
    Route::get('/reports/fund-loads/pdf', [\App\Http\Controllers\Organisation\ReportsController::class, 'exportFundLoadsPdf'])->name('reports.fund-loads-pdf');
    Route::get('/reports/bank-deposits/excel', [\App\Http\Controllers\Organisation\ReportsController::class, 'exportBankDepositsExcel'])->name('reports.bank-deposits-excel');
    Route::get('/reports/bank-deposits/pdf', [\App\Http\Controllers\Organisation\ReportsController::class, 'exportBankDepositsPdf'])->name('reports.bank-deposits-pdf');
    Route::get('/profile', [OrgDashboard::class, 'profile'])->name('profile');
    Route::put('/profile', [OrgDashboard::class, 'updateProfile'])->name('profile.update');
    Route::get('/bank-deposit-notification', [\App\Http\Controllers\Organisation\BankDepositNotificationController::class, 'create'])->name('bank-deposit-notification.create');
    Route::post('/bank-deposit-notification', [\App\Http\Controllers\Organisation\BankDepositNotificationController::class, 'store'])->name('bank-deposit-notification.store');
    Route::get('/bank-deposit-notification/list', [\App\Http\Controllers\Organisation\BankDepositNotificationController::class, 'index'])->name('bank-deposit-notification.index');
    Route::get('/bank-deposit-notification/{bankDeposit}', [\App\Http\Controllers\Organisation\BankDepositNotificationController::class, 'show'])->name('bank-deposit-notification.show');
    // Vouchers
    Route::get('/vouchers/create', [OrgVoucher::class, 'create'])->name('vouchers.create');
    Route::post('/vouchers', [OrgVoucher::class, 'store'])->name('vouchers.store');
    Route::get('/vouchers', [OrgVoucher::class, 'index'])->name('vouchers.index');
    Route::get('/vouchers/{voucher}', [OrgVoucher::class, 'show'])->name('vouchers.show');
    Route::patch('/vouchers/{voucher}/cancel', [OrgVoucher::class, 'cancel'])->name('vouchers.cancel');
    Route::get('/reports/vouchers/excel', [\App\Http\Controllers\Organisation\ReportsController::class, 'exportVouchersExcel'])->name('reports.vouchers-excel');
    Route::get('/reports/vouchers/pdf', [\App\Http\Controllers\Organisation\ReportsController::class, 'exportVouchersPdf'])->name('reports.vouchers-pdf');
});

// School/Care
Route::prefix('school')->name('school.')->middleware(['auth', 'approved', 'role:school_care'])->group(function () {
    Route::get('/dashboard', [OrgDashboard::class, 'schoolDashboard'])->name('dashboard');
    Route::get('/food', [OrgDashboard::class, 'browseFood'])->name('food');
    Route::post('/food/{foodListingId}/claim', [SurplusClaimController::class, 'claim'])->name('food.claim');
    Route::post('/food/{listing}/redeem', [RecipientVoucher::class, 'redeem'])->name('food.redeem');
    Route::get('/donate', [DonationController::class, 'showDonateForm'])->name('donate');
    Route::post('/donate', [DonationController::class, 'storeDonation'])->name('donate.store');
    Route::get('/donations', [OrgDashboard::class, 'donations'])->name('donations');
    Route::get('/fund-load', [OrgFundLoad::class, 'showLoadForm'])->name('fund-load');
    Route::post('/fund-load/create-intent', [OrgFundLoad::class, 'createPaymentIntent'])->name('fund-load.create-intent');
    Route::post('/fund-load/confirm', [OrgFundLoad::class, 'confirmPayment'])->name('fund-load.confirm');
    Route::get('/fund-load/history', [OrgFundLoad::class, 'loadHistory'])->name('fund-load.history');
    Route::get('/food-breakdown', [FoodBreakdownController::class, 'schoolBreakdown'])->name('food-breakdown');
    Route::get('/reports', [\App\Http\Controllers\Organisation\ReportsController::class, 'index'])->name('reports');
    Route::get('/reports/fund-loads/excel', [\App\Http\Controllers\Organisation\ReportsController::class, 'exportFundLoadsExcel'])->name('reports.fund-loads-excel');
    Route::get('/reports/fund-loads/pdf', [\App\Http\Controllers\Organisation\ReportsController::class, 'exportFundLoadsPdf'])->name('reports.fund-loads-pdf');
    Route::get('/reports/bank-deposits/excel', [\App\Http\Controllers\Organisation\ReportsController::class, 'exportBankDepositsExcel'])->name('reports.bank-deposits-excel');
    Route::get('/reports/bank-deposits/pdf', [\App\Http\Controllers\Organisation\ReportsController::class, 'exportBankDepositsPdf'])->name('reports.bank-deposits-pdf');
    Route::get('/profile', [OrgDashboard::class, 'profile'])->name('profile');
    Route::put('/profile', [OrgDashboard::class, 'updateProfile'])->name('profile.update');
    Route::get('/bank-deposit-notification', [\App\Http\Controllers\Organisation\BankDepositNotificationController::class, 'create'])->name('bank-deposit-notification.create');
    Route::post('/bank-deposit-notification', [\App\Http\Controllers\Organisation\BankDepositNotificationController::class, 'store'])->name('bank-deposit-notification.store');
    Route::get('/bank-deposit-notification/list', [\App\Http\Controllers\Organisation\BankDepositNotificationController::class, 'index'])->name('bank-deposit-notification.index');
    Route::get('/bank-deposit-notification/{bankDeposit}', [\App\Http\Controllers\Organisation\BankDepositNotificationController::class, 'show'])->name('bank-deposit-notification.show');
    // Vouchers
    Route::get('/vouchers/create', [OrgVoucher::class, 'create'])->name('vouchers.create');
    Route::post('/vouchers', [OrgVoucher::class, 'store'])->name('vouchers.store');
    Route::get('/vouchers', [OrgVoucher::class, 'index'])->name('vouchers.index');
    Route::get('/vouchers/{voucher}', [OrgVoucher::class, 'show'])->name('vouchers.show');
    Route::patch('/vouchers/{voucher}/cancel', [OrgVoucher::class, 'cancel'])->name('vouchers.cancel');
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
