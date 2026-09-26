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

            // Multi-Item Array
            'items' => 'nullable|array',
            'items.*.item_name' => 'nullable|string|max:255',
            'items.*.item_id' => 'nullable',
            'items.*.item_code' => 'nullable|string|max:100',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.rate' => 'nullable|numeric|min:0',
            'items.*.tax_percent' => 'nullable|numeric|min:0',
            'items.*.thans' => 'nullable',

            // Single Item Direct Format (Backward Compatibility)
            'item_name' => 'nullable|string|max:255',
            'item_id' => 'nullable',
            'item_code' => 'nullable|string|max:100',
            'rate' => 'nullable|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0',
            'thans' => 'nullable'
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $count = PurchaseOrder::count() + 1;
            $poNumber = 'PE-2026-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            while (PurchaseOrder::where('po_number', $poNumber)->exists()) {
                $count++;
                $poNumber = 'PE-2026-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }

            $processedItems = $this->normalizeItemsFromRequest($request);

            if (empty($processedItems)) {
                // If nothing was supplied, add a default placeholder line item
                $processedItems[] = [
                    'item_id' => null,
                    'item_name' => 'Fabric Quality',
                    'item_code' => null,
                    'ordered_qty' => 0.0,
                    'than_count' => 0,
                    'than_details' => null,
                    'clean_thans' => [],
                    'unit' => 'Meters',
                    'rate' => 0.0,
                    'tax_percent' => 5.0,
                    'tax_amount' => 0.0,
                    'total_amount' => 0.0,
                    'subtotal' => 0.0
                ];
            }

            $subtotal = 0;
            $taxTotal = 0;
            $overallTotalThans = 0;
            $overallTotalMeters = 0;
            $allThansCombined = [];

            foreach ($processedItems as $pItem) {
                $subtotal += $pItem['subtotal'];
                $taxTotal += $pItem['tax_amount'];
                $overallTotalThans += $pItem['than_count'];
                $overallTotalMeters += $pItem['ordered_qty'];
                if (!empty($pItem['clean_thans'])) {
                    $allThansCombined = array_merge($allThansCombined, $pItem['clean_thans']);
                }
            }

            $grandTotal = round($subtotal + $taxTotal, 2);
            $challanNo = $request->input('challan_no');

            $notesPayload = [
                'challan_no' => $challanNo,
                'total_items' => count($processedItems),
                'total_thans' => $overallTotalThans,
                'total_meters' => round($overallTotalMeters, 2),
                'thans' => $allThansCombined,
                'user_notes' => $request->input('notes')
            ];

            $poData = [
                'po_number' => $poNumber,
                'vendor_id' => $validated['vendor_id'] ?? null,
                'vendor_name' => $validated['vendor_name'],
                'po_date' => $validated['po_date'],
                'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                'warehouse_location' => 'Main Store',
                'subtotal' => round($subtotal, 2),
                'tax_total' => round($taxTotal, 2),
                'grand_total' => $grandTotal,
                'status' => $validated['status'] ?? 'Approved',
                'payment_status' => $validated['payment_status'] ?? 'Pending',
                'notes' => json_encode($notesPayload)
            ];

            if (Schema::hasColumn('purchase_orders', 'challan_no')) {
                $poData['challan_no'] = $challanNo;
            }
            if (Schema::hasColumn('purchase_orders', 'total_thans')) {
                $poData['total_thans'] = $overallTotalThans;
            }
            if (Schema::hasColumn('purchase_orders', 'than_details')) {
                $poData['than_details'] = !empty($allThansCombined) ? json_encode($allThansCombined) : null;
            }

            $po = PurchaseOrder::create($poData);

            // Create individual line items with their own than breakdowns
            foreach ($processedItems as $pItem) {
                $itemData = [
                    'purchase_order_id' => $po->id,
                    'item_id' => $pItem['item_id'],
                    'item_name' => $pItem['item_name'],
                    'item_code' => $pItem['item_code'],
                    'ordered_qty' => $pItem['ordered_qty'],
                    'unit' => $pItem['unit'],
                    'rate' => $pItem['rate'],
                    'tax_percent' => $pItem['tax_percent'],
                    'tax_amount' => $pItem['tax_amount'],
                    'total_amount' => $pItem['total_amount']
                ];

                if (Schema::hasColumn('purchase_order_items', 'than_count')) {
                    $itemData['than_count'] = $pItem['than_count'];
                }
                if (Schema::hasColumn('purchase_order_items', 'than_details')) {
                    $itemData['than_details'] = $pItem['than_details'];
                }

                PurchaseOrderItem::create($itemData);
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

            'items' => 'nullable|array',
            'items.*.item_name' => 'nullable|string|max:255',
            'items.*.item_id' => 'nullable',
            'items.*.item_code' => 'nullable|string|max:100',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.rate' => 'nullable|numeric|min:0',
            'items.*.tax_percent' => 'nullable|numeric|min:0',
            'items.*.thans' => 'nullable',

            'item_name' => 'nullable|string|max:255',
            'item_id' => 'nullable',
            'item_code' => 'nullable|string|max:100',
            'rate' => 'nullable|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0',
            'thans' => 'nullable'
        ]);

        return DB::transaction(function () use ($validated, $order, $request) {
            $processedItems = $this->normalizeItemsFromRequest($request);

            if (empty($processedItems)) {
                $processedItems[] = [
                    'item_id' => null,
                    'item_name' => 'Fabric Quality',
                    'item_code' => null,
                    'ordered_qty' => 0.0,
                    'than_count' => 0,
                    'than_details' => null,
                    'clean_thans' => [],
                    'unit' => 'Meters',
                    'rate' => 0.0,
                    'tax_percent' => 5.0,
                    'tax_amount' => 0.0,
                    'total_amount' => 0.0,
                    'subtotal' => 0.0
                ];
            }

            $subtotal = 0;
            $taxTotal = 0;
            $overallTotalThans = 0;
            $overallTotalMeters = 0;
            $allThansCombined = [];

            foreach ($processedItems as $pItem) {
                $subtotal += $pItem['subtotal'];
                $taxTotal += $pItem['tax_amount'];
                $overallTotalThans += $pItem['than_count'];
                $overallTotalMeters += $pItem['ordered_qty'];
                if (!empty($pItem['clean_thans'])) {
                    $allThansCombined = array_merge($allThansCombined, $pItem['clean_thans']);
                }
            }

            $grandTotal = round($subtotal + $taxTotal, 2);
            $challanNo = $request->input('challan_no', $order->challan_no);

            $notesPayload = [
                'challan_no' => $challanNo,
                'total_items' => count($processedItems),
                'total_thans' => $overallTotalThans,
                'total_meters' => round($overallTotalMeters, 2),
                'thans' => $allThansCombined,
                'user_notes' => $request->input('notes')
            ];

            $updateData = [
                'vendor_id' => $validated['vendor_id'] ?? $order->vendor_id,
                'vendor_name' => $validated['vendor_name'] ?? $order->vendor_name,
                'po_date' => $validated['po_date'] ?? $order->po_date,
                'expected_delivery_date' => $validated['expected_delivery_date'] ?? $order->expected_delivery_date,
                'subtotal' => round($subtotal, 2),
                'tax_total' => round($taxTotal, 2),
                'grand_total' => $grandTotal,
                'status' => $validated['status'] ?? $order->status,
                'payment_status' => $validated['payment_status'] ?? $order->payment_status,
                'notes' => json_encode($notesPayload)
            ];

            if (Schema::hasColumn('purchase_orders', 'challan_no')) {
                $updateData['challan_no'] = $challanNo;
            }
            if (Schema::hasColumn('purchase_orders', 'total_thans')) {
                $updateData['total_thans'] = $overallTotalThans;
            }
            if (Schema::hasColumn('purchase_orders', 'than_details')) {
                $updateData['than_details'] = !empty($allThansCombined) ? json_encode($allThansCombined) : null;
            }

            $order->update($updateData);

            // Recreate line items
            $order->items()->delete();

            foreach ($processedItems as $pItem) {
                $itemData = [
                    'purchase_order_id' => $order->id,
                    'item_id' => $pItem['item_id'],
                    'item_name' => $pItem['item_name'],
                    'item_code' => $pItem['item_code'],
                    'ordered_qty' => $pItem['ordered_qty'],
                    'unit' => $pItem['unit'],
                    'rate' => $pItem['rate'],
                    'tax_percent' => $pItem['tax_percent'],
                    'tax_amount' => $pItem['tax_amount'],
                    'total_amount' => $pItem['total_amount']
                ];

                if (Schema::hasColumn('purchase_order_items', 'than_count')) {
                    $itemData['than_count'] = $pItem['than_count'];
                }
                if (Schema::hasColumn('purchase_order_items', 'than_details')) {
                    $itemData['than_details'] = $pItem['than_details'];
                }

                PurchaseOrderItem::create($itemData);
            }

            if ($request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->isJson() || str_contains((string)$request->header('Accept'), 'application/json')) {
                return response()->json(['success' => true, 'order' => $order->load('items')]);
            }

            return redirect()->route('purchase.orders.index')->with('success', "Purchase Entry {$order->po_number} updated successfully.");
        });
    }

    /**
     * Helper to normalize multi-item and single-item requests with per-item Than meter readings
     */
    protected function normalizeItemsFromRequest(Request $request): array
    {
        $items = [];

        // 1. Check for multi-item array
        if ($request->has('items') && is_array($request->input('items'))) {
            $rawItems = $request->input('items');
            foreach ($rawItems as $raw) {
                if (empty($raw) || (empty($raw['item_name']) && empty($raw['item_id']))) {
                    continue;
                }

                // Extract Than breakdown for this item
                $rawThans = $raw['thans'] ?? [];
                if (is_string($rawThans)) {
                    if (str_starts_with(trim($rawThans), '[')) {
                        $rawThans = json_decode($rawThans, true) ?: [];
                    } else {
                        $rawThans = preg_split('/[,\s\n]+/', trim($rawThans), -1, PREG_SPLIT_NO_EMPTY);
                    }
                }
                $cleanThans = [];
                foreach ((array)$rawThans as $val) {
                    $fVal = floatval($val);
                    if ($fVal > 0) {
                        $cleanThans[] = round($fVal, 2);
                    }
                }

                $totalMeters = round(array_sum($cleanThans), 2);
                if ($totalMeters <= 0 && isset($raw['ordered_qty']) && floatval($raw['ordered_qty']) > 0) {
                    $totalMeters = round(floatval($raw['ordered_qty']), 2);
                }

                $thanCount = count($cleanThans);
                if ($thanCount === 0 && $totalMeters > 0) {
                    $thanCount = isset($raw['than_count']) ? intval($raw['than_count']) : 1;
                }

                $rate = floatval($raw['rate'] ?? 0);
                $taxPercent = isset($raw['tax_percent']) ? floatval($raw['tax_percent']) : 5.0;
                $lineSubtotal = round($totalMeters * $rate, 2);
                $taxAmount = round(($lineSubtotal * $taxPercent) / 100.0, 2);
                $lineTotal = round($lineSubtotal + $taxAmount, 2);

                $items[] = [
                    'item_id' => !empty($raw['item_id']) ? $raw['item_id'] : null,
                    'item_name' => $raw['item_name'] ?? 'Fabric Quality',
                    'item_code' => $raw['item_code'] ?? null,
                    'ordered_qty' => $totalMeters,
                    'than_count' => $thanCount,
                    'than_details' => !empty($cleanThans) ? json_encode($cleanThans) : null,
                    'clean_thans' => $cleanThans,
                    'unit' => $raw['unit'] ?? 'Meters',
                    'rate' => $rate,
                    'tax_percent' => $taxPercent,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $lineTotal,
                    'subtotal' => $lineSubtotal
                ];
            }
        }

        // 2. Fallback: If single item format was submitted directly (item_name / thans / rate / tax_percent)
        if (empty($items) && ($request->filled('item_name') || $request->has('thans'))) {
            $rawThans = $request->input('thans', []);
            if (is_string($rawThans)) {
                if (str_starts_with(trim($rawThans), '[')) {
                    $rawThans = json_decode($rawThans, true) ?: [];
                } else {
                    $rawThans = preg_split('/[,\s\n]+/', trim($rawThans), -1, PREG_SPLIT_NO_EMPTY);
                }
            }
            $cleanThans = [];
            foreach ((array)$rawThans as $val) {
                $fVal = floatval($val);
                if ($fVal > 0) {
                    $cleanThans[] = round($fVal, 2);
                }
            }

            $totalMeters = round(array_sum($cleanThans), 2);
            if ($totalMeters <= 0 && $request->filled('ordered_qty')) {
                $totalMeters = round(floatval($request->input('ordered_qty')), 2);
            }

            $thanCount = count($cleanThans);
            if ($thanCount === 0 && $totalMeters > 0) {
                $thanCount = 1;
            }

            $rate = floatval($request->input('rate', 0));
            $taxPercent = floatval($request->input('tax_percent', 5.0));
            $lineSubtotal = round($totalMeters * $rate, 2);
            $taxAmount = round(($lineSubtotal * $taxPercent) / 100.0, 2);
            $lineTotal = round($lineSubtotal + $taxAmount, 2);

            $items[] = [
                'item_id' => $request->input('item_id') ?: null,
                'item_name' => $request->input('item_name') ?: 'Fabric Quality',
                'item_code' => $request->input('item_code') ?: null,
                'ordered_qty' => $totalMeters,
                'than_count' => $thanCount,
                'than_details' => !empty($cleanThans) ? json_encode($cleanThans) : null,
                'clean_thans' => $cleanThans,
                'unit' => $request->input('unit', 'Meters'),
                'rate' => $rate,
                'tax_percent' => $taxPercent,
                'tax_amount' => $taxAmount,
                'total_amount' => $lineTotal,
                'subtotal' => $lineSubtotal
            ];
        }

        return $items;
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
