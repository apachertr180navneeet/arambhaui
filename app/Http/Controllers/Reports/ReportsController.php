<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\DispatchChallan;
use App\Models\JobAssignment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    public function itemLedger(Request $request)
    {
        $transactions = Transaction::where('ledger_type', 'ItemStock')->latest()->get();
        $items = Item::all();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($transactions);
        }

        return view('reports.ledger', compact('transactions', 'items'));
    }

    public function stockReport(Request $request)
    {
        $items = Item::latest()->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($items);
        }

        $totalValuation = $items->sum(function($itm) {
            return $itm->current_stock * $itm->unit_cost;
        });

        $stats = [
            'totalItems' => $items->count(),
            'totalValuation' => $totalValuation,
            'lowStockCount' => $items->where('current_stock', '<=', 'min_stock')->count()
        ];

        return view('reports.stock', compact('items', 'stats'));
    }

    public function lotPurchase(Request $request)
    {
        $orders = PurchaseOrder::with(['items', 'vendor'])->latest()->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($orders);
        }

        return view('reports.lot-purchase', compact('orders'));
    }

    public function lotSales(Request $request)
    {
        $challans = DispatchChallan::with('items')->latest()->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($challans);
        }

        return view('reports.lot-sales', compact('challans'));
    }
}
