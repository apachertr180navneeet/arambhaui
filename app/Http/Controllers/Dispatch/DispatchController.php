<?php

namespace App\Http\Controllers\Dispatch;

use App\Http\Controllers\Controller;
use App\Models\DispatchChallan;
use App\Models\DispatchItem;
use App\Models\ProductionOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DispatchController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(DispatchChallan::with('items')->latest()->get());
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'dispatch',
            'submodule' => 'dispatch'
        ]);
    }

    public function readyList(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(ProductionOrder::where('status', 'Ready for Dispatch')->get());
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'dispatch',
            'submodule' => 'ready'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_no' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'dispatch_date' => 'required|date',
            'transporter_name' => 'required|string',
            'lr_number' => 'nullable|string',
            'vehicle_number' => 'nullable|string',
            'destination_city' => 'nullable|string',
            'total_cartons' => 'required|integer|min:1',
            'total_qty' => 'required|integer|min:1'
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $count = DispatchChallan::count() + 1;
            $challanNo = 'DC-2026-' . str_pad($count, 3, '0', STR_PAD_LEFT);

            $validated['challan_no'] = $challanNo;
            $validated['status'] = 'In Transit';

            $challan = DispatchChallan::create($validated);

            // Create sample dispatch lines
            DispatchItem::create([
                'dispatch_challan_id' => $challan->id,
                'style_name' => 'Garment Lot Line',
                'size' => 'All Sizes (M/L/XL)',
                'color' => 'Assorted',
                'qty' => $validated['total_qty'],
                'carton_barcode' => 'CTN-' . rand(10000, 99999)
            ]);

            // Update order status if exists
            ProductionOrder::where('order_no', $validated['order_no'])->update(['status' => 'Completed']);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'challan' => $challan->load('items')]);
            }

            return redirect()->route('dispatch.challans.index')->with('success', "Dispatch Challan {$challanNo} generated.");
        });
    }

    public function show(DispatchChallan $challan)
    {
        return response()->json($challan->load('items'));
    }

    public function destroy(DispatchChallan $challan)
    {
        $challan->items()->delete();
        $challan->delete();
        return response()->json(['success' => true, 'message' => 'Dispatch challan removed.']);
    }
}
