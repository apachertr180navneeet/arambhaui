<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\ProductionOrder;
use App\Models\LotTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductionOrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(ProductionOrder::latest()->get());
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'production',
            'submodule' => 'orders'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'style_name' => 'required|string|max:255',
            'order_qty' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'order_date' => 'required|date',
            'delivery_date' => 'required|date',
            'item_description' => 'nullable|string'
        ]);

        $count = ProductionOrder::count() + 1;
        $orderNo = 'ORD-2026-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        $totalAmount = $validated['order_qty'] * $validated['unit_price'];

        $validated['order_no'] = $orderNo;
        $validated['total_amount'] = $totalAmount;
        $validated['current_stage'] = 'Cutting';
        $validated['progress_percent'] = 15;
        $validated['status'] = 'Scheduled';

        $order = ProductionOrder::create($validated);

        // Auto-create initial WIP Lot
        $lotNo = 'LOT-' . strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $validated['style_name']), 0, 4)) . '-' . rand(1000, 9999);
        LotTracking::create([
            'lot_number' => $lotNo,
            'order_no' => $orderNo,
            'style_name' => $validated['style_name'],
            'initial_qty' => $validated['order_qty'],
            'current_qty' => $validated['order_qty'],
            'current_stage' => 'Cutting Dept',
            'current_location' => 'Main Cutting Hall - Table #2',
            'status' => 'Active WIP'
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'order' => $order]);
        }

        return redirect()->route('production.orders.index')->with('success', "Production Order {$orderNo} initialized.");
    }

    public function show(ProductionOrder $order)
    {
        return response()->json($order);
    }
}
