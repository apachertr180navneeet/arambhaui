<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Masters\CustomerController;
use App\Http\Controllers\Masters\VendorController;
use App\Http\Controllers\Masters\JobWorkerController;
use App\Http\Controllers\Masters\ItemController;
use App\Http\Controllers\Masters\SizeColorController;
use App\Http\Controllers\Purchase\PurchaseOrderController;
use App\Http\Controllers\Purchase\PurchaseInwardController;
use App\Http\Controllers\JobWork\JobAssignController;
use App\Http\Controllers\JobWork\JobInwardReportController;
use App\Http\Controllers\Production\ProductionOrderController;
use App\Http\Controllers\Production\QualityCheckController;
use App\Http\Controllers\Production\LotTrackingController;
use App\Http\Controllers\Qr\QrController;
use App\Http\Controllers\Dispatch\DispatchController;
use App\Http\Controllers\Invoices\InvoiceController;
use App\Http\Controllers\Accounts\AccountsController;
use App\Http\Controllers\Reports\ReportsController;
use App\Http\Controllers\Admin\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes - GarmentERP Enterprise Suite
|--------------------------------------------------------------------------
*/

// Guest / Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

// Authenticated ERP Protected Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ==========================================
    // 1. CORE MASTERS MANAGEMENT
    // ==========================================
    Route::prefix('masters')->name('masters.')->group(function () {
        Route::resource('customers', CustomerController::class);
        Route::resource('vendors', VendorController::class);
        Route::resource('jobworkers', JobWorkerController::class);
        Route::resource('items', ItemController::class);
        Route::get('sizes', [SizeColorController::class, 'index'])->name('sizes.index');
        Route::post('sizes', [SizeColorController::class, 'storeSize'])->name('sizes.store');
        Route::post('colors', [SizeColorController::class, 'storeColor'])->name('colors.store');
    });

    // ==========================================
    // 2. PURCHASE MANAGEMENT
    // ==========================================
    Route::prefix('purchase')->name('purchase.')->group(function () {
        Route::resource('orders', PurchaseOrderController::class);
        Route::resource('inward', PurchaseInwardController::class);
    });

    // ==========================================
    // 3. JOB WORK & ASSIGNMENT
    // ==========================================
    Route::prefix('jobwork')->name('jobwork.')->group(function () {
        Route::resource('assign', JobAssignController::class);
        Route::get('inward-report', [JobInwardReportController::class, 'index'])->name('inward-report');
    });

    // ==========================================
    // 4. MANUFACTURING & PRODUCTION
    // ==========================================
    Route::prefix('production')->name('production.')->group(function () {
        Route::resource('orders', ProductionOrderController::class);
        Route::resource('qc', QualityCheckController::class);
        Route::get('jobwork', [ProductionOrderController::class, 'index'])->name('jobwork');
        Route::get('tracking/{lot?}', [LotTrackingController::class, 'index'])->name('tracking');
    });

    // ==========================================
    // 5. QR & LOYALTY MANAGEMENT
    // ==========================================
    Route::prefix('qr')->name('qr.')->group(function () {
        Route::get('generator', [QrController::class, 'generator'])->name('generator');
        Route::post('generator', [QrController::class, 'store'])->name('generator.store');
        Route::get('scanner', [QrController::class, 'scanner'])->name('scanner');
        Route::get('history', [QrController::class, 'history'])->name('history');
        Route::post('validate', [QrController::class, 'validateVoucher'])->name('validate');
    });

    // ==========================================
    // 6. LOGISTICS & DISPATCH
    // ==========================================
    Route::prefix('dispatch')->name('dispatch.')->group(function () {
        Route::get('ready', [DispatchController::class, 'readyList'])->name('ready');
        Route::resource('challans', DispatchController::class);
        Route::get('dispatch', [DispatchController::class, 'index'])->name('dispatch');
    });

    // ==========================================
    // 7. BILLING & INVOICES
    // ==========================================
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::resource('list', InvoiceController::class);
        Route::get('create', [InvoiceController::class, 'create'])->name('create');
        Route::post('store', [InvoiceController::class, 'store'])->name('store');
    });

    // ==========================================
    // 8. ACCOUNTS & SETTLEMENTS
    // ==========================================
    Route::prefix('accounts')->name('accounts.')->group(function () {
        Route::get('customer-accounts', [AccountsController::class, 'customerAccounts'])->name('customer-accounts');
        Route::get('customer-outstanding', [AccountsController::class, 'customerOutstanding'])->name('customer-outstanding');
        Route::get('vendor-outstanding', [AccountsController::class, 'vendorOutstanding'])->name('vendor-outstanding');
        Route::get('jobworker-outstanding', [AccountsController::class, 'jobWorkerOutstanding'])->name('jobworker-outstanding');
        Route::post('receipt', [AccountsController::class, 'storeReceipt'])->name('receipt.store');
    });

    // ==========================================
    // 9. CENTRAL REPORTS HUB
    // ==========================================
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('ledger', [ReportsController::class, 'itemLedger'])->name('ledger');
        Route::get('stock', [ReportsController::class, 'stockReport'])->name('stock');
        Route::get('lot-purchase', [ReportsController::class, 'lotPurchase'])->name('lot-purchase');
        Route::get('lot-sales', [ReportsController::class, 'lotSales'])->name('lot-sales');
    });

    // ==========================================
    // 10. ADMINISTRATION
    // ==========================================
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('users', [AdminController::class, 'users'])->name('users.index');
        Route::post('users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('roles', [AdminController::class, 'roles'])->name('roles');
        Route::get('activity', [AdminController::class, 'activity'])->name('activity');
        Route::get('settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    });
});
