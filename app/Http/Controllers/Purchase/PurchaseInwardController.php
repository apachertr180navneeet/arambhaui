<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\PurchaseInward;
use App\Models\PurchaseOrder;
use App\Models\Item;
use App\Models\Vendor;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseInwardController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(PurchaseInward::with(['purchaseOrder', 'vendor'])->latest()->get());
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'purchase',
            'submodule' => 'inward'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'po_number' => 'nullable|string',
            'purchase_order_id' => 'nullable|integer',
            'vendor_name' => 'required|string|max:255',
            'vendor_id' => 'nullable|integer',
            'received_date' => 'required|date',
            'warehouse_location' => 'nullable|string|max:255',
            'item_name' => 'required|string|max:255',
            'item_code' => 'nullable|string|max:100',
            'ordered_qty' => 'nullable|numeric',
            'received_qty' => 'required|numeric|min:0.01',
            'accepted_qty' => 'required|numeric|min:0',
            'rejected_qty' => 'nullable|numeric|min:0',
            'lot_number' => 'nullable|string|max:100',
            'supplier_invoice_no' => 'nullable|string|max:100',
            'inspected_by' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:Pending,Approved,Rejected',
            'notes' => 'nullable|string'
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $count = PurchaseInward::count() + 1;
            $grnNumber = 'GRN-2026-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            $validated['grn_number'] = $grnNumber;

            if (empty($validated['lot_number'])) {
                $validated['lot_number'] = 'RAW-LOT-' . date('ymd') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            }

            if (!isset($validated['rejected_qty'])) {
                $validated['rejected_qty'] = max(0, $validated['received_qty'] - $validated['accepted_qty']);
            }

            $inward = PurchaseInward::create($validated);

            // If item exists in Item Master, increment current_stock
            if (!empty($validated['item_name']) || !empty($validated['item_code'])) {
                $item = Item::where('name', $validated['item_name'])
                    ->orWhere('code', $validated['item_code'])
                    ->first();
                if ($item && $validated['accepted_qty'] > 0) {
                    $item->increment('current_stock', (float)$validated['accepted_qty']);

                    // Create stock ledger transaction
                    Transaction::create([
                        'transaction_type' => 'Purchase GRN',
                        'party_type' => 'Vendor',
                        'party_name' => $validated['vendor_name'],
                        'reference_no' => $grnNumber,
                        'date' => $validated['received_date'],
                        'amount' => 0.00,
                        'payment_mode' => 'N/A',
                        'ledger_type' => 'ItemStock',
                        'remarks' => "Stock incremented by {$validated['accepted_qty']} via GRN {$grnNumber}"
                    ]);
                }
            }

            // If linked to PO, update PO status if fully received
            if (!empty($validated['purchase_order_id'])) {
                $po = PurchaseOrder::find($validated['purchase_order_id']);
                if ($po) {
                    $po->update(['status' => 'Received']);
                }
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'inward' => $inward]);
            }

            return redirect()->route('purchase.inward.index')->with('success', "GRN {$grnNumber} created successfully.");
        });
    }

    public function show(PurchaseInward $inward)
    {
        return response()->json($inward->load(['purchaseOrder', 'vendor']));
    }

    public function update(Request $request, PurchaseInward $inward)
    {
        $validated = $request->validate([
            'vendor_name' => 'sometimes|required|string|max:255',
            'received_date' => 'sometimes|required|date',
            'warehouse_location' => 'nullable|string',
            'received_qty' => 'sometimes|required|numeric',
            'accepted_qty' => 'sometimes|required|numeric',
            'rejected_qty' => 'nullable|numeric',
            'lot_number' => 'nullable|string',
            'status' => 'nullable|string|in:Pending,Approved,Rejected',
            'notes' => 'nullable|string'
        ]);

        $inward->update($validated);
        return response()->json(['success' => true, 'inward' => $inward]);
    }

    public function destroy(PurchaseInward $inward)
    {
        $grn = $inward->grn_number;
        $inward->delete();

        return response()->json(['success' => true, 'message' => "GRN {$grn} deleted successfully."]);
    }
}
