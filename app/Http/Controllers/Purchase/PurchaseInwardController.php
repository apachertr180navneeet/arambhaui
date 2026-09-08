<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\PurchaseInward;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseInwardController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(PurchaseInward::latest()->get());
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
            'vendor_name' => 'required|string|max:255',
            'item_name' => 'required|string|max:255',
            'received_qty' => 'required|numeric|min:1',
            'rejected_qty' => 'nullable|numeric|min:0',
            'inward_date' => 'required|date',
            'warehouse_location' => 'nullable|string',
            'vehicle_number' => 'nullable|string',
            'vendor_invoice_no' => 'nullable|string'
        ]);

        $count = PurchaseInward::count() + 1;
        $grnNumber = 'GRN-2026-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        $lotNumber = 'LOT-RAW-' . rand(1000, 9999);

        $validated['grn_number'] = $grnNumber;
        $validated['lot_number'] = $lotNumber;
        $validated['status'] = ($validated['rejected_qty'] ?? 0) > 0 ? 'Partial Acceptance' : 'Accepted';

        $inward = PurchaseInward::create($validated);

        // Update item stock if found
        $item = Item::where('name', 'like', '%' . $validated['item_name'] . '%')->first();
        if ($item) {
            $item->increment('current_stock', $validated['received_qty']);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'inward' => $inward]);
        }

        return redirect()->route('purchase.inward.index')->with('success', "GRN {$grnNumber} generated with Lot {$lotNumber}.");
    }

    public function show(PurchaseInward $inward)
    {
        return response()->json($inward);
    }
}
