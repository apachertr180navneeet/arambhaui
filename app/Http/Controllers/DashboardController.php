<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\JobWorker;
use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\JobAssignment;
use App\Models\QrVoucher;
use App\Models\DispatchChallan;
use App\Models\ActivityLog;

class DashboardController extends Controller
{
    /**
     * Display the main GarmentERP ERP Dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        // High-level KPI metrics calculated from database
        $totalCustomers = Customer::count();
        $totalVendors = Vendor::count();
        $totalJobWorkers = JobWorker::count();
        $totalItems = Item::count();
        $totalPOs = PurchaseOrder::count();
        $totalActiveVouchers = QrVoucher::where('status', 'Active')->count();
        $totalRedeemedVouchers = QrVoucher::where('status', 'Redeemed')->count();
        $totalChallans = DispatchChallan::count();
        $lowStockItems = Item::whereRaw('current_stock <= min_stock')->get();
        $recentPOs = PurchaseOrder::with('vendor')->latest()->take(5)->get();
        $recentJobOrders = JobAssignment::latest()->take(5)->get();
        $recentVouchers = QrVoucher::latest()->take(5)->get();

        $stats = [
            'totalCustomers' => $totalCustomers,
            'totalVendors' => $totalVendors,
            'totalJobWorkers' => $totalJobWorkers,
            'totalItems' => $totalItems,
            'totalPOs' => $totalPOs,
            'totalActiveVouchers' => $totalActiveVouchers,
            'totalRedeemedVouchers' => $totalRedeemedVouchers,
            'totalChallans' => $totalChallans,
            'lowStockCount' => $lowStockItems->count()
        ];

        return view('dashboard.index', compact('user', 'stats', 'lowStockItems', 'recentPOs', 'recentJobOrders', 'recentVouchers'));
    }

    /**
     * Fallback module router for backward compatibility.
     */
    public function moduleRoute($module, $submodule = 'overview')
    {
        switch ($module) {
            case 'masters':
                if ($submodule === 'vendors') return redirect()->route('masters.vendors.index');
                if ($submodule === 'jobworkers') return redirect()->route('masters.jobworkers.index');
                if ($submodule === 'items') return redirect()->route('masters.items.index');
                if ($submodule === 'units') return redirect()->route('masters.units.index');
                return redirect()->route('masters.customers.index');
            case 'purchase':
                if ($submodule === 'create') return redirect()->route('purchase.orders.create');
                return redirect()->route('purchase.orders.index');
            case 'jobwork':
                if ($submodule === 'inward-report') return redirect()->route('jobwork.inward-report');
                return redirect()->route('jobwork.assign.index');
            case 'qr':
                if ($submodule === 'generator') return redirect()->route('qr.generator');
                if ($submodule === 'history') return redirect()->route('qr.history');
                return redirect()->route('qr.scanner');
            case 'dispatch':
                if ($submodule === 'ready') return redirect()->route('dispatch.ready');
                return redirect()->route('dispatch.dispatch');
            case 'accounts':
                if ($submodule === 'customer-outstanding') return redirect()->route('accounts.customer-outstanding');
                if ($submodule === 'vendor-outstanding') return redirect()->route('accounts.vendor-outstanding');
                if ($submodule === 'jobworker-outstanding') return redirect()->route('accounts.jobworker-outstanding');
                return redirect()->route('accounts.customer-accounts');
            case 'reports':
                if ($submodule === 'stock') return redirect()->route('reports.stock');
                if ($submodule === 'lot-purchase') return redirect()->route('reports.lot-purchase');
                if ($submodule === 'lot-sales') return redirect()->route('reports.lot-sales');
                return redirect()->route('reports.ledger');
            case 'admin':
                if ($submodule === 'roles') return redirect()->route('admin.roles');
                if ($submodule === 'activity') return redirect()->route('admin.activity');
                if ($submodule === 'settings') return redirect()->route('admin.settings');
                return redirect()->route('admin.users.index');
            default:
                return redirect()->route('dashboard');
        }
    }
}
