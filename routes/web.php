<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\Admin\VoucherController as AdminVoucher;
use App\Http\Controllers\Admin\ReportController as AdminReport;
use App\Http\Controllers\Admin\FundLoadController as AdminFundLoad;
use App\Http\Controllers\Admin\PayoutController as AdminPayout;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FoodListingController;
use App\Http\Controllers\Organisation\DashboardController as OrgDashboard;
use App\Http\Controllers\Organisation\DonationController;
use App\Http\Controllers\Recipient\DashboardController as RecipientDashboard;
use App\Http\Controllers\Recipient\VoucherController as RecipientVoucher;
use App\Http\Controllers\Shop\DashboardController as ShopDashboard;
use App\Http\Controllers\Shop\FoodListingController as ShopListing;
use App\Http\Controllers\Shop\PayoutController as ShopPayout;
use Illuminate\Support\Facades\Route;

// Language Switcher
Route::get('/lang/{locale}', function ($locale) {
    $supported = ['en', 'ar', 'ro', 'pl'];
    if (in_array($locale, $supported)) {
        session(['locale' => $locale]);
    }
    return redirect()->back()->withInput();
})->name('lang.switch');

// Public
Route::get('/', function () { return view('welcome'); })->name('home');
Route::get('/food', [FoodListingController::class, 'index'])->name('food.index');
Route::get('/food/{id}', [FoodListingController::class, 'show'])->name('food.show');

// Auth
require __DIR__.'/auth.php';
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Admin
Route::prefix('admin')->name('admin.')->middleware(['auth', 'approved', 'role:admin,super_admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
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
    Route::get('/donations', [AdminDashboard::class, 'donations'])->name('donations.index');
    Route::get('/reports', [AdminReport::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [AdminReport::class, 'export'])->name('reports.export');
    Route::get('/settings', [AdminDashboard::class, 'settings'])->name('settings')->middleware('role:super_admin');
    Route::post('/settings', [AdminDashboard::class, 'saveSettings'])->name('settings.save')->middleware('role:super_admin');
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
    Route::patch('/redemptions/{id}/confirm', [ShopDashboard::class, 'confirmRedemption'])->name('redemptions.confirm');
    Route::get('/profile', [ShopDashboard::class, 'profile'])->name('profile');
    Route::put('/profile', [ShopDashboard::class, 'updateProfile'])->name('profile.update');
    // Payouts
    Route::get('/payouts', [ShopPayout::class, 'index'])->name('payouts.index');
    Route::post('/payouts/bank-details', [ShopPayout::class, 'saveBankDetails'])->name('payouts.bank-details');
    Route::post('/payouts/request', [ShopPayout::class, 'requestPayout'])->name('payouts.request');
    Route::get('/payouts/{id}', [ShopPayout::class, 'show'])->name('payouts.show');
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
    Route::get('/profile', [RecipientDashboard::class, 'profile'])->name('profile');
    Route::put('/profile', [RecipientDashboard::class, 'updateProfile'])->name('profile.update');
});

// VCFSE
Route::prefix('vcfse')->name('vcfse.')->middleware(['auth', 'approved', 'role:vcfse'])->group(function () {
    Route::get('/dashboard', [OrgDashboard::class, 'vcfseDashboard'])->name('dashboard');
    Route::get('/food', [OrgDashboard::class, 'browseFood'])->name('food');
    Route::get('/donate', [DonationController::class, 'showForm'])->name('donate');
    Route::post('/donate', [DonationController::class, 'initiate'])->name('donate.initiate');
    Route::get('/donate/success', [DonationController::class, 'success'])->name('donate.success');
    Route::get('/donate/cancel', [DonationController::class, 'cancel'])->name('donate.cancel');
    Route::get('/donations', [OrgDashboard::class, 'donations'])->name('donations');
    Route::get('/profile', [OrgDashboard::class, 'profile'])->name('profile');
    Route::put('/profile', [OrgDashboard::class, 'updateProfile'])->name('profile.update');
});

// School/Care
Route::prefix('school')->name('school.')->middleware(['auth', 'approved', 'role:school_care'])->group(function () {
    Route::get('/dashboard', [OrgDashboard::class, 'schoolDashboard'])->name('dashboard');
    Route::get('/food', [OrgDashboard::class, 'browseFood'])->name('food');
    Route::get('/donate', [DonationController::class, 'showForm'])->name('donate');
    Route::post('/donate', [DonationController::class, 'initiate'])->name('donate.initiate');
    Route::get('/donate/success', [DonationController::class, 'success'])->name('donate.success');
    Route::get('/donate/cancel', [DonationController::class, 'cancel'])->name('donate.cancel');
    Route::get('/donations', [OrgDashboard::class, 'donations'])->name('donations');
    Route::get('/profile', [OrgDashboard::class, 'profile'])->name('profile');
    Route::put('/profile', [OrgDashboard::class, 'updateProfile'])->name('profile.update');
});

// Stripe Webhook
Route::post('/stripe/webhook', [DonationController::class, 'webhook'])->name('stripe.webhook');
