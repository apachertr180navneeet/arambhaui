<?php

namespace App\Http\Controllers\Dispatch;

use App\Http\Controllers\Controller;
use App\Models\DispatchChallan;
use App\Models\DispatchItem;
use App\Models\Customer;
use App\Models\Item;
use App\Models\JobInward;
use App\Models\JobAssignment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DispatchController extends Controller
{
    /**
     * Retrieve all available batches with current remaining stock.
     */
    public static function getBatchesData()
    {
        $batches = [];
        $registeredBatches = [];

        // 1. Check Job Inwards (Production Ready Lots)
        if (Schema::hasTable('job_inwards')) {
            try {
                $inwards = JobInward::select('lot_number', 'style_name')
                    ->selectRaw('SUM(received_qty) as total_inward')
                    ->groupBy('lot_number', 'style_name')
                    ->get();

                foreach ($inwards as $inw) {
                    $dispatched = (float)DispatchItem::where('batch_no', $inw->lot_number)->sum('qty');
                    $avail = max(0, (float)$inw->total_inward - $dispatched);

                    $matchedItem = Item::where('name', 'like', '%' . $inw->style_name . '%')->first();

                    $registeredBatches[$inw->lot_number] = true;
                    $batches[] = [
                        'batch_no' => $inw->lot_number,
                        'item_id' => $matchedItem?->id,
                        'item_name' => $inw->style_name,
                        'available_stock' => $avail,
                        'unit' => $matchedItem?->unit ?? 'Pcs',
                        'unit_cost' => $matchedItem?->unit_cost ?? 0,
                        'source' => 'Production Lot'
                    ];
                }
            } catch (\Throwable $e) {}
        }

        // 2. Check Item Master with available on-hand stock
        try {
            $items = Item::all();
            foreach ($items as $itm) {
                $defaultBatch = 'LOT-' . $itm->code;
                if (!isset($registeredBatches[$defaultBatch])) {
                    $batches[] = [
                        'batch_no' => $defaultBatch,
                        'item_id' => $itm->id,
                        'item_name' => $itm->name,
                        'available_stock' => (float)$itm->current_stock,
                        'unit' => $itm->unit ?? 'Pcs',
                        'unit_cost' => (float)$itm->unit_cost,
                        'source' => 'Warehouse Stock'
                    ];
                }
            }
        } catch (\Throwable $e) {}

        return $batches;
    }

    public function index(Request $request)
    {
        $challans = DispatchChallan::with(['items.item'])->latest()->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($challans);
        }

        $customers = Customer::all();
        $stats = [
            'totalChallans' => DispatchChallan::count(),
            'totalDispatchedQty' => DispatchChallan::sum('total_qty'),
            'inTransit' => DispatchChallan::where('status', 'In Transit')->count(),
            'delivered' => DispatchChallan::where('status', 'Delivered')->count()
        ];

        return view('dispatch.challans.index', compact('challans', 'customers', 'stats'));
    }

    public function readyList(Request $request)
    {
        $readyAssignments = JobAssignment::where('status', 'Completed')->orWhere('status', 'Issued')->latest()->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($readyAssignments);
        }

        return view('dispatch.ready', compact('readyAssignments'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $items = Item::orderBy('name')->get();
        $batches = self::getBatchesData();
        $count = DispatchChallan::count() + 1;
        $nextChallanNo = 'DC-2026-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        return view('dispatch.challans.create', compact('customers', 'items', 'batches', 'nextChallanNo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_no' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'customer_id' => 'nullable|integer',
            'dispatch_date' => 'required|date',
            'transporter_name' => 'required|string',
            'lr_number' => 'nullable|string',
            'bilty_no' => 'nullable|string',
            'vehicle_number' => 'nullable|string',
            'destination_city' => 'nullable|string',
            'total_cartons' => 'nullable|integer|min:0',
            'total_qty' => 'nullable|numeric|min:0',
            'total_thans' => 'nullable|integer|min:0',
            'subtotal_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.item_id' => 'nullable',
            'items.*.style_name' => 'nullable|string',
            'items.*.item_category' => 'nullable|string',
            'items.*.batch_no' => 'nullable|string',
            'items.*.size' => 'nullable|string',
            'items.*.color' => 'nullable|string',
            'items.*.qty' => 'nullable|numeric|min:0',
            'items.*.rate' => 'nullable|numeric|min:0',
            'items.*.tax_percent' => 'nullable|numeric|min:0',
            'items.*.tax_amount' => 'nullable|numeric|min:0',
            'items.*.total_amount' => 'nullable|numeric|min:0',
            'items.*.than_count' => 'nullable|integer|min:0',
            'items.*.thans_data' => 'nullable',
            'items.*.unit' => 'nullable|string',
            'items.*.cartons' => 'nullable|integer|min:0',
            'items.*.carton_barcode' => 'nullable|string'
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $count = DispatchChallan::count() + 1;
            $challanNo = 'DC-2026-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            while (DispatchChallan::where('challan_no', $challanNo)->exists()) {
                $count++;
                $challanNo = 'DC-2026-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            }

            // Filter out empty rows
            $rawItems = $validated['items'] ?? [];
            $validItems = array_filter($rawItems, function ($row) {
                return !empty($row['batch_no']) || !empty($row['style_name']) || (!empty($row['qty']) && (float)$row['qty'] > 0);
            });

            $computedQty = 0;
            $computedCartons = 0;
            $computedSubtotal = 0;
            $computedTax = 0;
            $computedTotalAmount = 0;
            $computedThans = 0;

            foreach ($validItems as $row) {
                $q = (float)($row['qty'] ?? 0);
                $r = (float)($row['rate'] ?? 0);
                $taxP = (float)($row['tax_percent'] ?? 0);
                $lineSub = round($q * $r, 2);
                $lineTax = (float)($row['tax_amount'] ?? round(($lineSub * $taxP) / 100, 2));
                $lineTot = (float)($row['total_amount'] ?? round($lineSub + $lineTax, 2));

                $computedQty += $q;
                $computedCartons += (int)($row['cartons'] ?? 1);
                $computedSubtotal += $lineSub;
                $computedTax += $lineTax;
                $computedTotalAmount += $lineTot;
                $computedThans += (int)($row['than_count'] ?? 0);
            }

            $totalQty = $computedQty > 0 ? $computedQty : (float)($validated['total_qty'] ?? 1);
            $totalCartons = $computedCartons > 0 ? $computedCartons : max(1, (int)($validated['total_cartons'] ?? 1));
            $subtotalAmt = $computedSubtotal > 0 ? $computedSubtotal : (float)($validated['subtotal_amount'] ?? 0);
            $taxAmt = $computedTax > 0 ? $computedTax : (float)($validated['tax_amount'] ?? 0);
            $totalAmt = $computedTotalAmount > 0 ? $computedTotalAmount : (float)($validated['total_amount'] ?? ($subtotalAmt + $taxAmt));
            $totalThans = $computedThans > 0 ? $computedThans : (int)($validated['total_thans'] ?? 0);

            $dcData = [
                'challan_no' => $challanNo,
                'order_no' => $validated['order_no'],
                'customer_id' => $validated['customer_id'] ?? null,
                'customer_name' => $validated['customer_name'],
                'dispatch_date' => $validated['dispatch_date'],
                'transporter_name' => $validated['transporter_name'],
                'lr_number' => $validated['lr_number'] ?? null,
                'bilty_no' => $validated['bilty_no'] ?? ($validated['lr_number'] ?? null),
                'vehicle_number' => $validated['vehicle_number'] ?? null,
                'destination_city' => $validated['destination_city'] ?? null,
                'total_cartons' => $totalCartons,
                'total_qty' => $totalQty,
                'total_thans' => $totalThans,
                'subtotal_amount' => $subtotalAmt,
                'tax_amount' => $taxAmt,
                'total_amount' => $totalAmt,
                'remarks' => $validated['remarks'] ?? null,
                'status' => 'In Transit'
            ];

            $challan = DispatchChallan::create($dcData);

            if (count($validItems) > 0) {
                foreach ($validItems as $row) {
                    $itemQty = (float)($row['qty'] ?? 0);
                    if ($itemQty <= 0) continue;

                    $batchNo = !empty($row['batch_no']) ? strtoupper(trim($row['batch_no'])) : 'SALE-LOT';
                    $styleName = !empty($row['style_name']) ? $row['style_name'] : 'Item';
                    $itemId = !empty($row['item_id']) ? (int)$row['item_id'] : null;
                    $item = $itemId ? Item::find($itemId) : null;
                    $rate = (float)($row['rate'] ?? ($item?->unit_cost ?? 0));
                    $taxPercent = (float)($row['tax_percent'] ?? 5);
                    $sub = round($itemQty * $rate, 2);
                    $lineTax = (float)($row['tax_amount'] ?? round(($sub * $taxPercent) / 100, 2));
                    $lineTotal = (float)($row['total_amount'] ?? round($sub + $lineTax, 2));
                    $thanCount = (int)($row['than_count'] ?? 0);
                    $thansData = is_array($row['thans_data'] ?? null) ? json_encode($row['thans_data']) : ($row['thans_data'] ?? null);

                    DispatchItem::create([
                        'dispatch_challan_id' => $challan->id,
                        'item_id' => $itemId,
                        'batch_no' => $batchNo,
                        'style_name' => $styleName,
                        'item_category' => $row['item_category'] ?? ($item?->category ?? 'Finished Goods'),
                        'size' => $row['size'] ?? 'All Sizes',
                        'color' => $row['color'] ?? 'Standard',
                        'qty' => $itemQty,
                        'rate' => $rate,
                        'tax_percent' => $taxPercent,
                        'tax_amount' => $lineTax,
                        'total_amount' => $lineTotal,
                        'than_count' => $thanCount,
                        'thans_data' => $thansData,
                        'unit' => $row['unit'] ?? ($item?->unit ?? 'Pcs'),
                        'carton_barcode' => $row['carton_barcode'] ?? ('CTN-' . rand(10000, 99999))
                    ]);

                    // Record stock debit in ledger (Raw or Finished alike)
                    try {
                        Transaction::create([
                            'transaction_no' => 'TXN-DC-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8)),
                            'transaction_date' => $challan->dispatch_date,
                            'ledger_type' => 'ItemStock',
                            'entity_id' => $itemId,
                            'entity_name' => $item ? $item->name : $styleName,
                            'reference_no' => $challan->challan_no,
                            'transaction_type' => 'Delivery Challan Dispatch',
                            'debit' => 0,
                            'credit' => $itemQty,
                            'running_balance' => $item ? (float)$item->current_stock : 0,
                            'created_by' => Auth::user()?->name ?? 'Admin',
                            'description' => "Dispatched {$itemQty} {$row['unit']} ({$styleName}) via {$challan->challan_no} to {$challan->customer_name}"
                        ]);

                        if ($itemId) {
                            \App\Services\StockLedgerService::recalculateItemStock($itemId);
                        }
                    } catch (\Throwable $e) {}
                }
            } else {
                // Fallback default single line item with auto-assigned lot
                $defaultBatch = 'LOT-DC-' . date('Ymd');
                DispatchItem::create([
                    'dispatch_challan_id' => $challan->id,
                    'batch_no' => $defaultBatch,
                    'style_name' => 'Finished Garment Lot Consignment',
                    'size' => 'All Sizes (M/L/XL)',
                    'color' => 'Assorted',
                    'qty' => $totalQty,
                    'unit' => 'Pcs',
                    'carton_barcode' => 'CTN-' . rand(10000, 99999)
                ]);
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Dispatch Challan {$challanNo} generated successfully with batch tracking.",
                    'challan' => $challan->load('items')
                ], 201);
            }

            return redirect()->route('dispatch.challans.index')->with('success', "Dispatch Challan {$challanNo} generated with batch stock tracking.");
        });
    }

    public function edit(DispatchChallan $challan)
    {
        $customers = Customer::orderBy('name')->get();
        $items = Item::orderBy('name')->get();
        $batches = self::getBatchesData();
        $challan->load('items.item');

        return view('dispatch.challans.edit', compact('challan', 'customers', 'items', 'batches'));
    }

    public function update(Request $request, DispatchChallan $challan)
    {
        $validated = $request->validate([
            'order_no' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'customer_id' => 'nullable|integer',
            'dispatch_date' => 'required|date',
            'transporter_name' => 'required|string',
            'lr_number' => 'nullable|string',
            'bilty_no' => 'nullable|string',
            'vehicle_number' => 'nullable|string',
            'destination_city' => 'nullable|string',
            'total_cartons' => 'nullable|integer|min:0',
            'total_qty' => 'nullable|numeric|min:0',
            'total_thans' => 'nullable|integer|min:0',
            'subtotal_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
            'status' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.item_id' => 'nullable',
            'items.*.style_name' => 'nullable|string',
            'items.*.item_category' => 'nullable|string',
            'items.*.batch_no' => 'nullable|string',
            'items.*.size' => 'nullable|string',
            'items.*.color' => 'nullable|string',
            'items.*.qty' => 'nullable|numeric|min:0',
            'items.*.rate' => 'nullable|numeric|min:0',
            'items.*.tax_percent' => 'nullable|numeric|min:0',
            'items.*.tax_amount' => 'nullable|numeric|min:0',
            'items.*.total_amount' => 'nullable|numeric|min:0',
            'items.*.than_count' => 'nullable|integer|min:0',
            'items.*.thans_data' => 'nullable',
            'items.*.unit' => 'nullable|string',
            'items.*.cartons' => 'nullable|integer|min:0',
            'items.*.carton_barcode' => 'nullable|string'
        ]);

        return DB::transaction(function () use ($validated, $challan, $request) {
            $computedQty = 0;
            $computedCartons = 0;
            $computedSubtotal = 0;
            $computedTax = 0;
            $computedTotalAmount = 0;
            $computedThans = 0;

            if (isset($validated['items']) && is_array($validated['items'])) {
                $challan->items()->delete();

                // Also delete old transaction ledger entries for this challan
                try {
                    Transaction::where('reference_no', $challan->challan_no)->delete();
                } catch (\Throwable $e) {}

                $rawItems = $validated['items'];
                $validItems = array_filter($rawItems, function ($row) {
                    return !empty($row['batch_no']) || !empty($row['style_name']) || (!empty($row['qty']) && (float)$row['qty'] > 0);
                });

                foreach ($validItems as $row) {
                    $itemQty = (float)($row['qty'] ?? 0);
                    if ($itemQty <= 0) continue;

                    $r = (float)($row['rate'] ?? 0);
                    $taxP = (float)($row['tax_percent'] ?? 0);
                    $lineSub = round($itemQty * $r, 2);
                    $lineTax = (float)($row['tax_amount'] ?? round(($lineSub * $taxP) / 100, 2));
                    $lineTot = (float)($row['total_amount'] ?? round($lineSub + $lineTax, 2));

                    $computedQty += $itemQty;
                    $computedCartons += (int)($row['cartons'] ?? 1);
                    $computedSubtotal += $lineSub;
                    $computedTax += $lineTax;
                    $computedTotalAmount += $lineTot;
                    $computedThans += (int)($row['than_count'] ?? 0);

                    $batchNo = !empty($row['batch_no']) ? strtoupper(trim($row['batch_no'])) : 'DEFAULT-LOT';
                    $styleName = !empty($row['style_name']) ? $row['style_name'] : 'Item';
                    $itemId = !empty($row['item_id']) ? (int)$row['item_id'] : null;
                    $item = $itemId ? Item::find($itemId) : null;
                    $thansData = is_array($row['thans_data'] ?? null) ? json_encode($row['thans_data']) : ($row['thans_data'] ?? null);

                    DispatchItem::create([
                        'dispatch_challan_id' => $challan->id,
                        'item_id' => $itemId,
                        'batch_no' => $batchNo,
                        'style_name' => $styleName,
                        'item_category' => $row['item_category'] ?? ($item?->category ?? 'Finished Goods'),
                        'size' => $row['size'] ?? 'All Sizes',
                        'color' => $row['color'] ?? 'Standard',
                        'qty' => $itemQty,
                        'rate' => $r,
                        'tax_percent' => $taxP,
                        'tax_amount' => $lineTax,
                        'total_amount' => $lineTot,
                        'than_count' => (int)($row['than_count'] ?? 0),
                        'thans_data' => $thansData,
                        'unit' => $row['unit'] ?? ($item?->unit ?? 'Pcs'),
                        'carton_barcode' => $row['carton_barcode'] ?? ('CTN-' . rand(10000, 99999))
                    ]);

                    // Sync Stock Transaction
                    try {
                        Transaction::create([
                            'transaction_no' => 'TXN-DC-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8)),
                            'transaction_date' => $validated['dispatch_date'],
                            'ledger_type' => 'ItemStock',
                            'entity_id' => $itemId,
                            'entity_name' => $item ? $item->name : $styleName,
                            'reference_no' => $challan->challan_no,
                            'transaction_type' => 'Delivery Challan Dispatch',
                            'debit' => 0,
                            'credit' => $itemQty,
                            'running_balance' => $item ? (float)$item->current_stock : 0,
                            'created_by' => Auth::user()?->name ?? 'Admin',
                            'description' => "Dispatched {$itemQty} {$row['unit']} ({$styleName}) via {$challan->challan_no} to {$challan->customer_name}"
                        ]);

                        if ($itemId) {
                            \App\Services\StockLedgerService::recalculateItemStock($itemId);
                        }
                    } catch (\Throwable $e) {}
                }
            }

            $totalQty = $computedQty > 0 ? $computedQty : (float)($validated['total_qty'] ?? $challan->total_qty);
            $totalCartons = $computedCartons > 0 ? $computedCartons : max(1, (int)($validated['total_cartons'] ?? $challan->total_cartons));
            $subtotalAmt = $computedSubtotal > 0 ? $computedSubtotal : (float)($validated['subtotal_amount'] ?? $challan->subtotal_amount);
            $taxAmt = $computedTax > 0 ? $computedTax : (float)($validated['tax_amount'] ?? $challan->tax_amount);
            $totalAmt = $computedTotalAmount > 0 ? $computedTotalAmount : (float)($validated['total_amount'] ?? $challan->total_amount);
            $totalThans = $computedThans > 0 ? $computedThans : (int)($validated['total_thans'] ?? $challan->total_thans);

            $challan->update([
                'order_no' => $validated['order_no'],
                'customer_id' => $validated['customer_id'] ?? $challan->customer_id,
                'customer_name' => $validated['customer_name'],
                'dispatch_date' => $validated['dispatch_date'],
                'transporter_name' => $validated['transporter_name'],
                'lr_number' => $validated['lr_number'] ?? $challan->lr_number,
                'bilty_no' => $validated['bilty_no'] ?? ($validated['lr_number'] ?? $challan->bilty_no),
                'vehicle_number' => $validated['vehicle_number'] ?? $challan->vehicle_number,
                'destination_city' => $validated['destination_city'] ?? $challan->destination_city,
                'total_cartons' => $totalCartons,
                'total_qty' => $totalQty,
                'total_thans' => $totalThans,
                'subtotal_amount' => $subtotalAmt,
                'tax_amount' => $taxAmt,
                'total_amount' => $totalAmt,
                'remarks' => $validated['remarks'] ?? $challan->remarks,
                'status' => $validated['status'] ?? $challan->status
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'challan' => $challan->load('items')]);
            }

            return redirect()->route('dispatch.challans.index')->with('success', "Dispatch Challan {$challan->challan_no} updated successfully.");
        });
    }

    public function show(Request $request, DispatchChallan $challan)
    {
        $challan->load(['items.item', 'customer']);
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($challan);
        }
        $companySettings = \App\Models\CompanySetting::first();
        return view('dispatch.challans.show', compact('challan', 'companySettings'));
    }

    public function destroy(DispatchChallan $challan)
    {
        $no = $challan->challan_no;

        try {
            Transaction::where('reference_no', $no)->delete();
        } catch (\Throwable $e) {}

        $challan->items()->delete();
        $challan->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => "Dispatch challan {$no} deleted successfully."]);
        }

        return redirect()->route('dispatch.challans.index')->with('success', "Dispatch challan {$no} deleted successfully.");
    }
}
