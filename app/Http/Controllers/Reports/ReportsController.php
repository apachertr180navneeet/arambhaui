<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    public function itemLedger(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(Transaction::where('ledger_type', 'ItemStock')->latest()->get());
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'reports',
            'submodule' => 'ledger'
        ]);
    }

    public function stockReport(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(Item::all());
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'reports',
            'submodule' => 'stock'
        ]);
    }

    public function lotPurchase(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([]);
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'reports',
            'submodule' => 'lot-purchase'
        ]);
    }

    public function lotSales(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([]);
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'reports',
            'submodule' => 'lot-sales'
        ]);
    }
}
