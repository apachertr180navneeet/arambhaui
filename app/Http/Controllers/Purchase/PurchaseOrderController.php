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
use Illuminate\Support\Facades\Schema;

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
            'totalAmount' => (float) PurchaseOrder::sum('grand_total'),
            'approvedCount' => PurchaseOrder::where('status', 'Approved')->count(),
            'pendingPayment' => PurchaseOrder::where('payment_status', 'Pending')->count()
        ];

        return view('purchase.orders.index', compact('orders', 'stats'));
    }

    public function create()
    {
        $vendors = Vendor::whereRaw('LOWER(status) = ?', ['active'])->orderBy('name')->get();
        if ($vendors->isEmpty()) {
            $vendors = Vendor::orderBy('name')->get();
        }

        $items = Item::whereRaw('LOWER(status) = ?', ['active'])->orderBy('name')->get();
        if ($items->isEmpty()) {
            $items = Item::orderBy('name')->get();
        }

        $units = Unit::all();
        $count = PurchaseOrder::count() + 1;
        $nextPoNumber = 'PE-2026-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        return view('purchase.orders.create', compact('vendors', 'items', 'units', 'nextPoNumber'));
    }

    public function edit(PurchaseOrder $order)
    {
        $vendors = Vendor::whereRaw('LOWER(status) = ?', ['active'])->orderBy('name')->get();
        if ($vendors->isEmpty()) {
            $vendors = Vendor::orderBy('name')->get();
        }

        $items = Item::whereRaw('LOWER(status) = ?', ['active'])->orderBy('name')->get();
        if ($items->isEmpty()) {
            $items = Item::orderBy('name')->get();
        }

        $units = Unit::all();
        $order->load(['items', 'vendor']);

        return view('purchase.orders.edit', compact('order', 'vendors', 'items', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_name' => 'required|string|max:255',
            'vendor_id' => 'nullable|integer',
            'challan_no' => 'nullable|string|max:100',
            'po_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'status' => 'nullable|string|in:Draft,Approved,Partially Received,Received,Cancelled',
            'payment_status' => 'nullable|string|in:Pending,Partially Paid,Paid',

            // Textile Than-Wise format (Product selected once, multiple Than meters)
            'item_name' => 'nullable|string|max:255',
            'item_id' => 'nullable',
            'item_code' => 'nullable|string|max:100',
            'rate' => 'nullable|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0',
            'thans' => 'nullable|array',
            'thans.*' => 'nullable|numeric|min:0',

            // Multi-item fallback
            'items' => 'nullable|array'
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $count = PurchaseOrder::count() + 1;
            $poNumber = 'PE-2026-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            while (PurchaseOrder::where('po_number', $poNumber)->exists()) {
                $count++;
                $poNumber = 'PE-2026-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }

            // 1. If Textile Than-wise input format
            if (!empty($request->item_name) || $request->has('thans')) {
                $rawThans = $request->input('thans', []);
                $cleanThans = [];
                foreach ((array)$rawThans as $val) {
                    $fVal = floatval($val);
                    if ($fVal > 0) {
                        $cleanThans[] = round($fVal, 2);
                    }
                }

                $totalMeters = round(array_sum($cleanThans), 2);
                if ($totalMeters <= 0 && $request->filled('ordered_qty')) {
                    $totalMeters = floatval($request->input('ordered_qty'));
                }

                $totalThans = count($cleanThans);
                $rate = floatval($request->input('rate', 0));
                $taxPercent = floatval($request->input('tax_percent', 5.0));

                $subtotal = round($totalMeters * $rate, 2);
                $taxTotal = round(($subtotal * $taxPercent) / 100.0, 2);
                $grandTotal = round($subtotal + $taxTotal, 2);

                $challanNo = $request->input('challan_no');

                $notesPayload = [
                    'challan_no' => $challanNo,
                    'total_thans' => $totalThans,
                    'total_meters' => $totalMeters,
                    'thans' => $cleanThans,
                    'user_notes' => $request->input('notes')
                ];

                $poData = [
                    'po_number' => $poNumber,
                    'vendor_id' => $validated['vendor_id'] ?? null,
                    'vendor_name' => $validated['vendor_name'],
                    'po_date' => $validated['po_date'],
                    'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                    'warehouse_location' => 'Main Store',
                    'subtotal' => $subtotal,
                    'tax_total' => $taxTotal,
                    'grand_total' => $grandTotal,
                    'status' => $validated['status'] ?? 'Approved',
                    'payment_status' => $validated['payment_status'] ?? 'Pending',
                    'notes' => json_encode($notesPayload)
                ];

                if (Schema::hasColumn('purchase_orders', 'challan_no')) {
                    $poData['challan_no'] = $challanNo;
                }
                if (Schema::hasColumn('purchase_orders', 'total_thans')) {
                    $poData['total_thans'] = $totalThans;
                }
                if (Schema::hasColumn('purchase_orders', 'than_details')) {
                    $poData['than_details'] = json_encode($cleanThans);
                }

                $po = PurchaseOrder::create($poData);

                // Create the single line item representing this fabric challan
                $itemData = [
                    'purchase_order_id' => $po->id,
                    'item_id' => $request->input('item_id') ?: null,
                    'item_name' => $request->input('item_name') ?: 'Fabric',
                    'item_code' => $request->input('item_code') ?: null,
                    'ordered_qty' => $totalMeters,
                    'unit' => $request->input('unit', 'Meters'),
                    'rate' => $rate,
                    'tax_percent' => $taxPercent,
                    'tax_amount' => $taxTotal,
                    'total_amount' => $grandTotal
                ];

                if (Schema::hasColumn('purchase_order_items', 'than_count')) {
                    $itemData['than_count'] = $totalThans;
                }
                if (Schema::hasColumn('purchase_order_items', 'than_details')) {
                    $itemData['than_details'] = json_encode($cleanThans);
                }

                PurchaseOrderItem::create($itemData);

            } else {
                // 2. Standard multi-item fallback
                $items = $request->input('items', []);
                $subtotal = 0;
                $taxTotal = 0;

                foreach ($items as $item) {
                    $qty = floatval($item['ordered_qty'] ?? 0);
                    $rate = floatval($item['rate'] ?? 0);
                    $lineSub = $qty * $rate;
                    $taxRate = isset($item['tax_percent']) ? floatval($item['tax_percent']) : 5.0;
                    $taxLine = ($lineSub * $taxRate) / 100.0;
                    $subtotal += $lineSub;
                    $taxTotal += $taxLine;
                }

                $grandTotal = round($subtotal + $taxTotal, 2);

                $po = PurchaseOrder::create([
                    'po_number' => $poNumber,
                    'vendor_id' => $validated['vendor_id'] ?? null,
                    'vendor_name' => $validated['vendor_name'],
                    'po_date' => $validated['po_date'],
                    'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                    'warehouse_location' => 'Main Store',
                    'subtotal' => $subtotal,
                    'tax_total' => $taxTotal,
                    'grand_total' => $grandTotal,
                    'status' => $validated['status'] ?? 'Approved',
                    'payment_status' => $validated['payment_status'] ?? 'Pending',
                    'notes' => $validated['notes'] ?? null
                ]);

                foreach ($items as $item) {
                    $qty = floatval($item['ordered_qty'] ?? 0);
                    $rate = floatval($item['rate'] ?? 0);
                    $lineSub = $qty * $rate;
                    $taxRate = isset($item['tax_percent']) ? floatval($item['tax_percent']) : 5.0;
                    $taxLine = ($lineSub * $taxRate) / 100.0;

                    PurchaseOrderItem::create([
                        'purchase_order_id' => $po->id,
                        'item_id' => $item['item_id'] ?? null,
                        'item_name' => $item['item_name'],
                        'item_code' => $item['item_code'] ?? null,
                        'ordered_qty' => $qty,
                        'unit' => $item['unit'] ?? 'Meters',
                        'rate' => $rate,
                        'tax_percent' => $taxRate,
                        'tax_amount' => $taxLine,
                        'total_amount' => $lineSub + $taxLine
                    ]);
                }
            }

            if ($request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->isJson() || str_contains((string)$request->header('Accept'), 'application/json')) {
                return response()->json(['success' => true, 'order' => $po->load('items')]);
            }

            return redirect()->route('purchase.orders.index')->with('success', "Purchase Entry {$poNumber} recorded successfully.");
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
            'challan_no' => 'nullable|string|max:100',
            'po_date' => 'sometimes|required|date',
            'expected_delivery_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'status' => 'nullable|string|in:Draft,Approved,Partially Received,Received,Cancelled',
            'payment_status' => 'nullable|string|in:Pending,Partially Paid,Paid',

            'item_name' => 'nullable|string|max:255',
            'item_id' => 'nullable',
            'item_code' => 'nullable|string|max:100',
            'rate' => 'nullable|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0',
            'thans' => 'nullable|array',
            'thans.*' => 'nullable|numeric|min:0',
            'items' => 'nullable|array'
        ]);

        return DB::transaction(function () use ($validated, $order, $request) {
            if (!empty($request->item_name) || $request->has('thans')) {
                $rawThans = $request->input('thans', []);
                $cleanThans = [];
                foreach ((array)$rawThans as $val) {
                    $fVal = floatval($val);
                    if ($fVal > 0) {
                        $cleanThans[] = round($fVal, 2);
                    }
                }

                $totalMeters = round(array_sum($cleanThans), 2);
                if ($totalMeters <= 0 && $request->filled('ordered_qty')) {
                    $totalMeters = floatval($request->input('ordered_qty'));
                }

                $totalThans = count($cleanThans);
                $rate = floatval($request->input('rate', 0));
                $taxPercent = floatval($request->input('tax_percent', 5.0));

                $subtotal = round($totalMeters * $rate, 2);
                $taxTotal = round(($subtotal * $taxPercent) / 100.0, 2);
                $grandTotal = round($subtotal + $taxTotal, 2);
                $challanNo = $request->input('challan_no');

                $notesPayload = [
                    'challan_no' => $challanNo,
                    'total_thans' => $totalThans,
                    'total_meters' => $totalMeters,
                    'thans' => $cleanThans,
                    'user_notes' => $request->input('notes')
                ];

                $updateData = [
                    'vendor_id' => $validated['vendor_id'] ?? $order->vendor_id,
                    'vendor_name' => $validated['vendor_name'] ?? $order->vendor_name,
                    'po_date' => $validated['po_date'] ?? $order->po_date,
                    'expected_delivery_date' => $validated['expected_delivery_date'] ?? $order->expected_delivery_date,
                    'subtotal' => $subtotal,
                    'tax_total' => $taxTotal,
                    'grand_total' => $grandTotal,
                    'status' => $validated['status'] ?? $order->status,
                    'payment_status' => $validated['payment_status'] ?? $order->payment_status,
                    'notes' => json_encode($notesPayload)
                ];

                if (Schema::hasColumn('purchase_orders', 'challan_no')) {
                    $updateData['challan_no'] = $challanNo;
                }
                if (Schema::hasColumn('purchase_orders', 'total_thans')) {
                    $updateData['total_thans'] = $totalThans;
                }
                if (Schema::hasColumn('purchase_orders', 'than_details')) {
                    $updateData['than_details'] = json_encode($cleanThans);
                }

                $order->update($updateData);

                $order->items()->delete();
                $itemData = [
                    'purchase_order_id' => $order->id,
                    'item_id' => $request->input('item_id') ?: null,
                    'item_name' => $request->input('item_name') ?: 'Fabric',
                    'item_code' => $request->input('item_code') ?: null,
                    'ordered_qty' => $totalMeters,
                    'unit' => $request->input('unit', 'Meters'),
                    'rate' => $rate,
                    'tax_percent' => $taxPercent,
                    'tax_amount' => $taxTotal,
                    'total_amount' => $grandTotal
                ];

                if (Schema::hasColumn('purchase_order_items', 'than_count')) {
                    $itemData['than_count'] = $totalThans;
                }
                if (Schema::hasColumn('purchase_order_items', 'than_details')) {
                    $itemData['than_details'] = json_encode($cleanThans);
                }

                PurchaseOrderItem::create($itemData);

            } elseif (isset($validated['items'])) {
                $subtotal = 0;
                $taxTotal = 0;

                foreach ($validated['items'] as $item) {
                    $qty = floatval($item['ordered_qty'] ?? 0);
                    $rate = floatval($item['rate'] ?? 0);
                    $lineSub = $qty * $rate;
                    $taxRate = isset($item['tax_percent']) ? floatval($item['tax_percent']) : 5.0;
                    $taxLine = ($lineSub * $taxRate) / 100.0;
                    $subtotal += $lineSub;
                    $taxTotal += $taxLine;
                }

                $validated['subtotal'] = $subtotal;
                $validated['tax_total'] = $taxTotal;
                $validated['grand_total'] = $subtotal + $taxTotal;

                $order->items()->delete();
                foreach ($validated['items'] as $item) {
                    $qty = floatval($item['ordered_qty'] ?? 0);
                    $rate = floatval($item['rate'] ?? 0);
                    $lineSub = $qty * $rate;
                    $taxRate = isset($item['tax_percent']) ? floatval($item['tax_percent']) : 5.0;
                    $taxLine = ($lineSub * $taxRate) / 100.0;

                    PurchaseOrderItem::create([
                        'purchase_order_id' => $order->id,
                        'item_id' => $item['item_id'] ?? null,
                        'item_name' => $item['item_name'],
                        'item_code' => $item['item_code'] ?? null,
                        'ordered_qty' => $qty,
                        'unit' => $item['unit'] ?? 'Meters',
                        'rate' => $rate,
                        'tax_percent' => $taxRate,
                        'tax_amount' => $taxLine,
                        'total_amount' => $lineSub + $taxLine
                    ]);
                }

                $order->update($validated);
            }

            if ($request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->isJson() || str_contains((string)$request->header('Accept'), 'application/json')) {
                return response()->json(['success' => true, 'order' => $order->load('items')]);
            }

            return redirect()->route('purchase.orders.index')->with('success', "Purchase Entry {$order->po_number} updated successfully.");
        });
    }

    public function destroy(PurchaseOrder $order)
    {
        $poNumber = $order->po_number;
        $order->items()->delete();
        $order->delete();

        if (request()->expectsJson() || request()->wantsJson() || request()->ajax() || request()->isJson() || str_contains((string)request()->header('Accept'), 'application/json')) {
            return response()->json(['success' => true, 'message' => "Purchase Entry {$poNumber} deleted successfully."]);
        }

        return redirect()->route('purchase.orders.index')->with('success', "Purchase Entry {$poNumber} deleted successfully.");
    }
}
