<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the main GarmentERP ERP Dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $module = 'dashboard';
        $submodule = 'overview';
        return view('dashboard', compact('user', 'module', 'submodule'));
    }

    /**
     * Generic module router.
     */
    public function moduleRoute($module, $submodule = 'overview')
    {
        $user = Auth::user();
        return view('dashboard', compact('user', 'module', 'submodule'));
    }

    public function masters($submodule = 'customers')
    {
        return view('dashboard', ['user' => Auth::user(), 'module' => 'masters', 'submodule' => $submodule]);
    }

    public function jobwork($submodule = 'assign')
    {
        return view('dashboard', ['user' => Auth::user(), 'module' => 'jobwork', 'submodule' => $submodule]);
    }

    public function production($submodule = 'orders')
    {
        return view('dashboard', ['user' => Auth::user(), 'module' => 'production', 'submodule' => $submodule]);
    }

    public function qr($submodule = 'generator')
    {
        return view('dashboard', ['user' => Auth::user(), 'module' => 'qr', 'submodule' => $submodule]);
    }

    public function dispatch($submodule = 'ready')
    {
        return view('dashboard', ['user' => Auth::user(), 'module' => 'dispatch', 'submodule' => $submodule]);
    }

    public function invoices($submodule = 'list')
    {
        return view('dashboard', ['user' => Auth::user(), 'module' => 'invoices', 'submodule' => $submodule]);
    }

    public function accounts($submodule = 'customer-accounts')
    {
        return view('dashboard', ['user' => Auth::user(), 'module' => 'accounts', 'submodule' => $submodule]);
    }

    public function reports($submodule = 'ledger')
    {
        return view('dashboard', ['user' => Auth::user(), 'module' => 'reports', 'submodule' => $submodule]);
    }

    public function admin($submodule = 'users')
    {
        return view('dashboard', ['user' => Auth::user(), 'module' => 'admin', 'submodule' => $submodule]);
    }
}

