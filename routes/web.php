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
Route::post('/qr/upload-recipient', [QrController::class, 'uploadRecipientQr'])->name('qr.uploadRecipient');
Route::post('/qr/save-recipient-settings', [QrController::class, 'saveRecipientSettings'])->name('qr.saveRecipientSettings');

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

// Composer Update / Install route for live servers
Route::match(['get', 'post'], '/composer-update', function (\Illuminate\Http\Request $request) {
    @ini_set('max_execution_time', 600);
    @set_time_limit(600);
    @ini_set('memory_limit', '1024M');

    $action = $request->query('action', 'update'); // 'update', 'install', 'dump', 'require'
    $package = $request->query('package', '');

    $basePath = base_path();
    putenv("COMPOSER_HOME={$basePath}/storage/framework/cache");

    // Check if execution functions are allowed
    $disabledFunctions = explode(',', ini_get('disable_functions') ?: '');
    $disabledFunctions = array_map('trim', $disabledFunctions);

    $canExec = function_exists('shell_exec') && !in_array('shell_exec', $disabledFunctions);

    if (!$canExec) {
        $msg = "Warning: shell_exec() is disabled in php.ini on this hosting server.\n\n"
             . "Because shell execution is disabled by your host, Composer CLI commands cannot be run directly via browser.\n\n"
             . "ALTERNATIVE SOLUTION (Recommended for cPanel / Shared Hosting):\n"
             . "1. Run 'composer update' or 'composer require barryvdh/laravel-dompdf simplesoftwareio/simple-qrcode' on your local computer.\n"
             . "2. Upload your local 'vendor/' directory to the server via cPanel File Manager (zip vendor, upload, and extract) or FTP.";
        return response("<!DOCTYPE html><html><body style='font-family:sans-serif;background:#0f172a;color:#f8fafc;padding:40px 20px;'><div style='max-width:800px;margin:0 auto;background:#1e293b;border:1px solid #ef4444;border-radius:12px;padding:24px;'><h3 style='color:#f87171;'>Command Execution Not Allowed</h3><pre style='background:#090d16;color:#fca5a5;padding:20px;border-radius:8px;white-space:pre-wrap;line-height:1.6;'>{$msg}</pre><a href='" . url('/clear-cache') . "' style='display:inline-block;margin-top:14px;background:#2563eb;color:white;padding:8px 16px;border-radius:6px;text-decoration:none;'>Clear Cache</a></div></body></html>", 200);
    }

    // Find PHP binary
    $php = defined('PHP_BINARY') && PHP_BINARY ? PHP_BINARY : 'php';

    // Find Composer
    $composer = null;
    $possibleComposers = [
        $basePath . '/composer.phar',
        'composer.phar',
        'composer',
        '/usr/local/bin/composer',
        '/usr/bin/composer',
        'C:\\ProgramData\\ComposerSetup\\bin\\composer.bat',
        'composer.bat'
    ];

    foreach ($possibleComposers as $cmd) {
        if (file_exists($cmd)) {
            $composer = (str_ends_with($cmd, '.phar')) ? "{$php} -d memory_limit=-1 {$cmd}" : $cmd;
            break;
        }
    }

    if (!$composer) {
        $test = @shell_exec('composer --version 2>&1');
        if ($test && stripos($test, 'Composer') !== false) {
            $composer = 'composer';
        } else {
            // Try to download composer.phar if missing
            $pharPath = $basePath . '/composer.phar';
            if (!file_exists($pharPath)) {
                $pharContent = @file_get_contents('https://getcomposer.org/composer-stable.phar');
                if ($pharContent && strlen($pharContent) > 500000) {
                    @file_put_contents($pharPath, $pharContent);
                    @chmod($pharPath, 0755);
                    $composer = "{$php} -d memory_limit=-1 {$pharPath}";
                }
            } else {
                $composer = "{$php} -d memory_limit=-1 {$pharPath}";
            }
        }
    }

    if (!$composer) {
        $composer = 'composer';
    }

    // Build the specific command
    if ($action === 'dump') {
        $cmd = "{$composer} dump-autoload -o 2>&1";
    } elseif ($action === 'install') {
        $cmd = "{$composer} install --no-dev --optimize-autoloader --no-interaction 2>&1";
    } elseif ($action === 'require' && !empty($package)) {
        $safePkg = escapeshellcmd($package);
        $cmd = "{$composer} require {$safePkg} --no-interaction 2>&1";
    } else {
        $cmd = "{$composer} update --no-dev --optimize-autoloader --no-interaction 2>&1";
    }

    // Execute in project base directory
    $fullCmd = "cd " . escapeshellarg($basePath) . " && " . $cmd;
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $fullCmd = "cd /d " . escapeshellarg($basePath) . " && " . $cmd;
    }

    $output = @shell_exec($fullCmd);

    // Refresh artisan framework optimization
    try {
        Artisan::call('optimize:clear');
        $artisanOut = Artisan::output();
    } catch (\Throwable $e) {
        $artisanOut = $e->getMessage();
    }

    $escapedCmd = htmlspecialchars($cmd);
    $escapedOutput = htmlspecialchars(trim((string)$output) ?: 'Command executed (No console output returned).');
    $escapedArtisan = htmlspecialchars(trim((string)$artisanOut));

    return "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <title>Composer Executed - GarmentERP</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #0f172a; color: #f8fafc; padding: 40px 20px; }
        .container { max-width: 880px; margin: 0 auto; background: #1e293b; border-radius: 16px; border: 1px solid #334155; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.4); }
        .badge { background: #059669; color: white; padding: 4px 12px; border-radius: 20px; font-weight: 700; font-size: 12px; }
        .badge-cmd { background: #3b82f6; color: white; padding: 4px 10px; border-radius: 6px; font-family: monospace; font-size: 13px; display: inline-block; margin: 10px 0; }
        pre { background: #090d16; color: #34d399; padding: 20px; border-radius: 10px; font-family: 'Consolas', 'Courier New', monospace; font-size: 13px; overflow-x: auto; line-height: 1.5; border: 1px solid #1e293b; max-height: 450px; }
        .btn-group { display: flex; gap: 10px; margin-top: 24px; flex-wrap: wrap; }
        .btn { padding: 9px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-block; background: #2563eb; color: white; font-size: 13px; }
        .btn-sub { background: #334155; color: #e2e8f0; }
        .btn:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class='container'>
        <span class='badge'>COMPOSER EXECUTION</span>
        <h2 style='margin: 12px 0 6px;'>Server Composer Task Finished</h2>
        <div>Command: <span class='badge-cmd'>{$escapedCmd}</span></div>
        <h4 style='margin: 16px 0 6px; color: #94a3b8;'>Composer Output:</h4>
        <pre>{$escapedOutput}</pre>

        <h4 style='margin: 16px 0 6px; color: #94a3b8;'>Artisan Cache Discovery:</h4>
        <pre style='color: #60a5fa;'>{$escapedArtisan}</pre>

        <div class='btn-group'>
            <a href='" . url('/composer-update') . "' class='btn'>Run Composer Update</a>
            <a href='" . url('/composer-update?action=install') . "' class='btn btn-sub'>Run Composer Install</a>
            <a href='" . url('/composer-update?action=dump') . "' class='btn btn-sub'>Dump Autoload</a>
            <a href='" . url('/clear-cache') . "' class='btn btn-sub'>Clear Cache</a>
            <a href='" . url('/dashboard') . "' class='btn btn-sub'>Back to Dashboard &rarr;</a>
        </div>
    </div>
</body>
</html>";
})->name('server.composer-update');

Route::get('/composer/update', function () {
    return redirect()->to('/composer-update');
});
Route::get('/composer-install', function () {
    return redirect()->to('/composer-update?action=install');
});


