<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Vendor;
use App\Models\Item;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = PurchaseOrder::with(['items', 'vendor'])->latest()->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($orders);
        }

        $stats = [
            'totalOrders' => PurchaseOrder::count(),
            'totalAmount' => PurchaseOrder::sum('grand_total'),
            'approvedCount' => PurchaseOrder::where('status', 'Approved')->count(),
            'pendingPayment' => PurchaseOrder::where('payment_status', 'Pending')->count()
        ];

        return view('purchase.orders.index', compact('orders', 'stats'));
    }

    public function create()
    {
        $vendors = Vendor::all();
        $items = Item::all();
        $units = Unit::all();
        $count = PurchaseOrder::count() + 1;
        $nextPoNumber = 'PO-2026-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        return view('purchase.orders.create', compact('vendors', 'items', 'units', 'nextPoNumber'));
    }

    public function edit(PurchaseOrder $order)
    {
        $vendors = Vendor::all();
        $items = Item::all();
        $units = Unit::all();
        $order->load(['items', 'vendor']);

        return view('purchase.orders.edit', compact('order', 'vendors', 'items', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_name' => 'required|string|max:255',
            'vendor_id' => 'nullable|integer',
            'po_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date',
            'warehouse_location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'nullable|string|in:Draft,Approved,Partially Received,Received,Cancelled',
            'payment_status' => 'nullable|string|in:Pending,Partially Paid,Paid',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.item_code' => 'nullable|string',
            'items.*.ordered_qty' => 'required|numeric|min:0.01',
            'items.*.unit' => 'nullable|string',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.tax_percent' => 'nullable|numeric'
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $count = PurchaseOrder::count() + 1;
            $poNumber = 'PO-2026-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            $subtotal = 0;
            $taxTotal = 0;

            foreach ($validated['items'] as $item) {
                $lineSub = $item['ordered_qty'] * $item['rate'];
                $taxRate = isset($item['tax_percent']) ? (float)$item['tax_percent'] : 5.0;
                $taxLine = ($lineSub * $taxRate) / 100.0;
                $subtotal += $lineSub;
                $taxTotal += $taxLine;
            }

            $grandTotal = $subtotal + $taxTotal;

            $po = PurchaseOrder::create([
                'po_number' => $poNumber,
                'vendor_id' => $validated['vendor_id'] ?? null,
                'vendor_name' => $validated['vendor_name'],
                'po_date' => $validated['po_date'],
                'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                'warehouse_location' => $validated['warehouse_location'] ?? 'Main Store - Unit 1',
                'subtotal' => $subtotal,
                'tax_total' => $taxTotal,
                'grand_total' => $grandTotal,
                'status' => $validated['status'] ?? 'Approved',
                'payment_status' => $validated['payment_status'] ?? 'Pending',
                'notes' => $validated['notes'] ?? null
            ]);

            foreach ($validated['items'] as $item) {
                $lineSub = $item['ordered_qty'] * $item['rate'];
                $taxRate = isset($item['tax_percent']) ? (float)$item['tax_percent'] : 5.0;
                $taxLine = ($lineSub * $taxRate) / 100.0;
                $lineTotal = $lineSub + $taxLine;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'item_id' => $item['item_id'] ?? null,
                    'item_name' => $item['item_name'],
                    'item_code' => $item['item_code'] ?? null,
                    'ordered_qty' => $item['ordered_qty'],
                    'unit' => $item['unit'] ?? 'Meters',
                    'rate' => $item['rate'],
                    'tax_percent' => $taxRate,
                    'tax_amount' => $taxLine,
                    'total_amount' => $lineTotal
                ]);
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'order' => $po->load('items')]);
            }

            return redirect()->route('purchase.orders.index')->with('success', "Purchase Order {$poNumber} created successfully.");
        });
    }

    public function show(PurchaseOrder $order)
    {
        return response()->json($order->load(['items', 'vendor', 'inwards']));
    }

    public function update(Request $request, PurchaseOrder $order)
    {
        $validated = $request->validate([
            'vendor_name' => 'sometimes|required|string|max:255',
            'vendor_id' => 'nullable|integer',
            'po_date' => 'sometimes|required|date',
            'expected_delivery_date' => 'nullable|date',
            'warehouse_location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'nullable|string|in:Draft,Approved,Partially Received,Received,Cancelled',
            'payment_status' => 'nullable|string|in:Pending,Partially Paid,Paid',
            'items' => 'sometimes|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.item_code' => 'nullable|string',
            'items.*.ordered_qty' => 'required|numeric|min:0.01',
            'items.*.unit' => 'nullable|string',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.tax_percent' => 'nullable|numeric'
        ]);

        return DB::transaction(function () use ($validated, $order, $request) {
            if (isset($validated['items'])) {
                $subtotal = 0;
                $taxTotal = 0;

                foreach ($validated['items'] as $item) {
                    $lineSub = $item['ordered_qty'] * $item['rate'];
                    $taxRate = isset($item['tax_percent']) ? (float)$item['tax_percent'] : 5.0;
                    $taxLine = ($lineSub * $taxRate) / 100.0;
                    $subtotal += $lineSub;
                    $taxTotal += $taxLine;
                }

                $validated['subtotal'] = $subtotal;
                $validated['tax_total'] = $taxTotal;
                $validated['grand_total'] = $subtotal + $taxTotal;

                // Sync line items
                $order->items()->delete();
                foreach ($validated['items'] as $item) {
                    $lineSub = $item['ordered_qty'] * $item['rate'];
                    $taxRate = isset($item['tax_percent']) ? (float)$item['tax_percent'] : 5.0;
                    $taxLine = ($lineSub * $taxRate) / 100.0;
                    $lineTotal = $lineSub + $taxLine;

                    PurchaseOrderItem::create([
                        'purchase_order_id' => $order->id,
                        'item_id' => $item['item_id'] ?? null,
                        'item_name' => $item['item_name'],
                        'item_code' => $item['item_code'] ?? null,
                        'ordered_qty' => $item['ordered_qty'],
                        'unit' => $item['unit'] ?? 'Meters',
                        'rate' => $item['rate'],
                        'tax_percent' => $taxRate,
                        'tax_amount' => $taxLine,
                        'total_amount' => $lineTotal
                    ]);
                }
            }

            $order->update($validated);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'order' => $order->load('items')]);
            }

            return redirect()->route('purchase.orders.index')->with('success', "Purchase Order {$order->po_number} updated successfully.");
        });
    }

    public function destroy(PurchaseOrder $order)
    {
        $poNumber = $order->po_number;
        $order->items()->delete();
        $order->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => "Purchase Order {$poNumber} deleted successfully."]);
        }

        return redirect()->route('purchase.orders.index')->with('success', "Purchase Order {$poNumber} deleted successfully.");
    }
}
