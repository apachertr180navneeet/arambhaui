<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Masters\CustomerController;
use App\Http\Controllers\Masters\VendorController;
use App\Http\Controllers\Masters\JobWorkerController;
use App\Http\Controllers\Masters\ItemController;
use App\Http\Controllers\Masters\UnitController;
use App\Http\Controllers\JobWork\JobAssignController;
use App\Http\Controllers\JobWork\JobInwardController;
use App\Http\Controllers\JobWork\JobInwardReportController;
use App\Http\Controllers\Purchase\PurchaseOrderController;
use App\Http\Controllers\Qr\QrController;
use App\Http\Controllers\Dispatch\DispatchController;
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

// Public Customer QR Claim & Scanning Routes
Route::get('/claim/{code?}', [QrController::class, 'scanner'])->name('qr.claim');
Route::get('/qr/scanner', [QrController::class, 'scanner'])->name('qr.scanner');
Route::post('/qr/validate', [QrController::class, 'validateVoucher'])->name('qr.validate');
Route::post('/qr/redeem', [QrController::class, 'redeemVoucher'])->name('qr.redeem');

// Authenticated ERP Protected Routes
Route::middleware('auth')->group(function () {
    Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/home', function () {
        return redirect()->route('dashboard');
    })->name('home');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/{module}/{submodule?}', [DashboardController::class, 'moduleRoute'])->name('dashboard.module');

    // ==========================================
    // 1. CORE MASTERS MANAGEMENT
    // ==========================================
    Route::prefix('masters')->name('masters.')->group(function () {
        Route::get('customers/{customer}/statement', [CustomerController::class, 'statement'])->name('customers.statement');
        Route::resource('customers', CustomerController::class);
        Route::get('vendors/{vendor}/statement', [VendorController::class, 'statement'])->name('vendors.statement');
        Route::resource('vendors', VendorController::class);
        Route::resource('jobworkers', JobWorkerController::class);
        Route::resource('items', ItemController::class);
        Route::resource('units', UnitController::class);
        // Fallback for previous sizes route
        Route::get('sizes', function () {
            return redirect()->route('masters.units.index');
        })->name('sizes.index');
    });

    // ==========================================
    // 2. JOB WORK & ASSIGNMENT
    // ==========================================
    Route::prefix('jobwork')->name('jobwork.')->group(function () {
        Route::resource('assign', JobAssignController::class);
        Route::resource('inward', JobInwardController::class);
        Route::get('inward-report', [JobInwardReportController::class, 'index'])->name('inward-report');
    });

    // ==========================================
    // 3. PURCHASE ORDERS
    // ==========================================
    Route::prefix('purchase')->name('purchase.')->group(function () {
        Route::resource('orders', PurchaseOrderController::class);
    });

    // ==========================================
    // 5. QR & LOYALTY MANAGEMENT (ADMIN)
    // ==========================================
    Route::prefix('qr')->name('qr.')->group(function () {
        Route::get('generator', [QrController::class, 'generator'])->name('generator');
        Route::post('generator', [QrController::class, 'store'])->name('generator.store');
        Route::post('store-batch', [QrController::class, 'storeBatch'])->name('storeBatch');
        Route::get('export-pdf', [QrController::class, 'exportPdf'])->name('exportPdf');
        Route::get('pdf-preview', [QrController::class, 'previewPdf'])->name('pdfPreview');
        Route::get('history', [QrController::class, 'history'])->name('history');
        Route::get('voucher/{id}', [QrController::class, 'show'])->name('show');
        Route::put('voucher/{id}', [QrController::class, 'update'])->name('update');
        Route::post('voucher/{id}/update', [QrController::class, 'update'])->name('update.post');
        Route::post('expire/{id}', [QrController::class, 'expireVoucher'])->name('expire');
        Route::post('reactivate/{id}', [QrController::class, 'reactivateVoucher'])->name('reactivate');
        Route::delete('voucher/{id}', [QrController::class, 'destroy'])->name('destroy');
    });

    // Color Master module compatibility aliases
    Route::get('/admin/qr/export-pdf', [QrController::class, 'exportPdf'])->name('admin.qr.exportPdf');
    Route::post('/admin/qr/storeBatch', [QrController::class, 'storeBatch'])->name('admin.qr.storeBatch');

    // ==========================================
    // 6. LOGISTICS & DISPATCH
    // ==========================================
    Route::prefix('dispatch')->name('dispatch.')->group(function () {
        Route::get('ready', [DispatchController::class, 'readyList'])->name('ready');
        Route::resource('challans', DispatchController::class);
        Route::get('dispatch', [DispatchController::class, 'index'])->name('dispatch');
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
        Route::put('users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('users/{user}', [AdminController::class, 'deleteUser'])->name('users.destroy');
        Route::get('roles', [AdminController::class, 'roles'])->name('roles');
        Route::get('activity', [AdminController::class, 'activity'])->name('activity');
        Route::get('settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    });
});

/*
|--------------------------------------------------------------------------
| Server Maintenance & Migration Execution Routes
|--------------------------------------------------------------------------
| Use these routes in browser or web hooks on live servers (e.g. cPanel / VPS)
| to run migrations and cache clearing without terminal access.
*/

Route::get('/run-migration', function (\Illuminate\Http\Request $request) {
    try {
        // Run migration with --force flag for production environments
        Artisan::call('migrate', ['--force' => true]);
        $output = Artisan::output();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Migrations executed successfully.',
                'output' => $output
            ]);
        }

        return "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Migration Status - GarmentERP</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #0f172a; color: #f8fafc; padding: 40px 20px; margin: 0; }
        .container { max-width: 760px; margin: 0 auto; background: #1e293b; border-radius: 16px; border: 1px solid #334155; padding: 30px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5); }
        .header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
        .badge { background: #059669; color: white; padding: 4px 12px; border-radius: 20px; font-weight: 700; font-size: 0.85rem; }
        h2 { margin: 0; font-size: 1.4rem; color: #ffffff; }
        pre { background: #090d16; color: #34d399; padding: 20px; border-radius: 10px; font-family: 'Fira Code', Consolas, monospace; font-size: 0.9rem; overflow-x: auto; border: 1px solid #1e293b; line-height: 1.5; }
        .actions { margin-top: 24px; display: flex; gap: 12px; flex-wrap: wrap; }
        .btn { padding: 10px 18px; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 0.875rem; transition: all 0.2s; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-secondary { background: #334155; color: #cbd5e1; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-secondary:hover { background: #475569; color: white; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <span class='badge'>SUCCESS</span>
            <h2>Database Migrations Executed</h2>
        </div>
        <p style='color:#94a3b8; margin: 0 0 16px; font-size: 0.95rem;'>Command: <code style='color:#60a5fa;'>php artisan migrate --force</code></p>
        <pre>" . (trim($output) ?: 'Nothing to migrate. All migrations are already up to date.') . "</pre>
        <div class='actions'>
            <a href='" . url('/clear-cache') . "' class='btn btn-secondary'>Clear Cache / Optimize</a>
            <a href='" . url('/dashboard') . "' class='btn btn-primary'>Go to Dashboard &rarr;</a>
        </div>
    </div>
</body>
</html>";

    } catch (\Throwable $e) {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }

        return "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <title>Migration Error - GarmentERP</title>
    <style>
        body { font-family: sans-serif; background: #0f172a; color: #f8fafc; padding: 40px 20px; }
        .container { max-width: 760px; margin: 0 auto; background: #1e293b; border-radius: 16px; border: 1px solid #ef4444; padding: 30px; }
        .badge { background: #dc2626; color: white; padding: 4px 12px; border-radius: 20px; font-weight: 700; }
        pre { background: #090d16; color: #f87171; padding: 20px; border-radius: 10px; font-family: monospace; overflow-x: auto; line-height: 1.5; }
        .btn { padding: 10px 18px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-block; margin-top: 16px; background: #2563eb; color: white; }
    </style>
</head>
<body>
    <div class='container'>
        <span class='badge'>MIGRATION ERROR</span>
        <h2 style='margin-top:12px;'>Database Migration Failed</h2>
        <pre>" . htmlspecialchars($e->getMessage()) . "\n\nFile: " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</pre>
        <a href='" . url('/run-migration') . "' class='btn'>Retry Migration</a>
    </div>
</body>
</html>";
    }
})->name('server.migrate');

// Alias for convenience: /migrate
Route::get('/migrate', function (\Illuminate\Http\Request $request) {
    return redirect()->to('/run-migration');
});

// Clear cache & optimization route for live servers
Route::get('/clear-cache', function (\Illuminate\Http\Request $request) {
    try {
        Artisan::call('optimize:clear');
        $output = Artisan::output();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Cache cleared and optimized successfully.',
                'output' => $output
            ]);
        }

        return "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <title>Cache Cleared - GarmentERP</title>
    <style>
        body { font-family: sans-serif; background: #0f172a; color: #f8fafc; padding: 40px 20px; }
        .container { max-width: 760px; margin: 0 auto; background: #1e293b; border-radius: 16px; border: 1px solid #334155; padding: 30px; }
        .badge { background: #059669; color: white; padding: 4px 12px; border-radius: 20px; font-weight: 700; }
        pre { background: #090d16; color: #34d399; padding: 20px; border-radius: 10px; font-family: monospace; overflow-x: auto; line-height: 1.5; }
        .btn { padding: 10px 18px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-block; margin-top: 16px; background: #2563eb; color: white; }
    </style>
</head>
<body>
    <div class='container'>
        <span class='badge'>OPTIMIZED</span>
        <h2 style='margin-top:12px;'>Framework Cache Cleared</h2>
        <pre>" . (trim($output) ?: 'All caches cleared successfully (config, routes, views, compiled).') . "</pre>
        <a href='" . url('/dashboard') . "' class='btn'>Back to Dashboard &rarr;</a>
    </div>
</body>
</html>";
    } catch (\Throwable $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
})->name('server.clear-cache');

// Storage Link helper route for live servers
Route::get('/storage-link', function () {
    try {
        Artisan::call('storage:link');
        $output = Artisan::output();
        return "<body style='background:#0f172a;color:#34d399;font-family:monospace;padding:40px;'><h3>Storage Link:</h3><pre>" . ($output ?: 'Symlink created / already exists.') . "</pre><a href='/dashboard' style='color:#60a5fa;'>Dashboard</a></body>";
    } catch (\Throwable $e) {
        return "<body style='background:#0f172a;color:#ef4444;font-family:monospace;padding:40px;'><h3>Storage Link Error:</h3><pre>" . $e->getMessage() . "</pre></body>";
    }
})->name('server.storage-link');

