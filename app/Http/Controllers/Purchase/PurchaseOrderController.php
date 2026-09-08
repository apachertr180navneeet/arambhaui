<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(PurchaseOrder::with('items')->latest()->get());
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'purchase',
            'submodule' => 'orders'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'nullable|exists:vendors,id',
            'vendor_name' => 'required|string|max:255',
            'po_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_percent' => 'nullable|numeric',
            'notes' => 'nullable|string'
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $count = PurchaseOrder::count() + 1;
            $poNumber = 'PO-2026-' . str_pad($count, 3, '0', STR_PAD_LEFT);

            $subtotal = 0;
            $taxTotal = 0;

            foreach ($validated['items'] as $it) {
                $lineSub = $it['qty'] * $it['unit_price'];
                $taxRate = $it['tax_percent'] ?? 5.00;
                $lineTax = ($lineSub * $taxRate) / 100;
                $subtotal += $lineSub;
                $taxTotal += $lineTax;
            }

            $po = PurchaseOrder::create([
                'po_number' => $poNumber,
                'vendor_id' => $validated['vendor_id'] ?? null,
                'vendor_name' => $validated['vendor_name'],
                'po_date' => $validated['po_date'],
                'expected_date' => $validated['expected_date'] ?? null,
                'subtotal' => $subtotal,
                'tax_amount' => $taxTotal,
                'grand_total' => $subtotal + $taxTotal,
                'status' => 'Approved',
                'notes' => $validated['notes'] ?? null
            ]);

            foreach ($validated['items'] as $it) {
                $lineSub = $it['qty'] * $it['unit_price'];
                $taxRate = $it['tax_percent'] ?? 5.00;
                $lineTax = ($lineSub * $taxRate) / 100;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'item_name' => $it['item_name'],
                    'qty' => $it['qty'],
                    'unit' => $it['unit'] ?? 'Meters',
                    'unit_price' => $it['unit_price'],
                    'tax_percent' => $taxRate,
                    'total_amount' => $lineSub + $lineTax
                ]);
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'purchase_order' => $po->load('items')]);
            }

            return redirect()->route('purchase.orders.index')->with('success', "PO {$poNumber} generated successfully.");
        });
    }

    public function show(PurchaseOrder $order)
    {
        return response()->json($order->load('items'));
    }

    public function destroy(PurchaseOrder $order)
    {
        $order->items()->delete();
        $order->delete();
        return response()->json(['success' => true, 'message' => 'Purchase order removed.']);
    }
}
