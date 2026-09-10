<?php

namespace App\Http\Controllers\Dispatch;

use App\Http\Controllers\Controller;
use App\Models\DispatchChallan;
use App\Models\DispatchItem;
use App\Models\Customer;
use App\Models\JobAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DispatchController extends Controller
{
    public function index(Request $request)
    {
        $challans = DispatchChallan::with('items')->latest()->get();

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

            DispatchItem::create([
                'dispatch_challan_id' => $challan->id,
                'style_name' => 'Finished Garment Lot Line',
                'size' => 'All Sizes (M/L/XL)',
                'color' => 'Assorted',
                'qty' => $validated['total_qty'],
                'carton_barcode' => 'CTN-' . rand(10000, 99999)
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'challan' => $challan->load('items')]);
            }

            return redirect()->route('dispatch.dispatch')->with('success', "Dispatch Challan {$challanNo} generated.");
        });
    }

    public function create()
    {
        $customers = Customer::all();
        $count = DispatchChallan::count() + 1;
        $nextChallanNo = 'DC-2026-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        return view('dispatch.challans.create', compact('customers', 'nextChallanNo'));
    }

    public function edit(DispatchChallan $challan)
    {
        $customers = Customer::all();
        $challan->load('items');

        return view('dispatch.challans.edit', compact('challan', 'customers'));
    }

    public function update(Request $request, DispatchChallan $challan)
    {
        $validated = $request->validate([
            'order_no' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'dispatch_date' => 'required|date',
            'transporter_name' => 'required|string',
            'lr_number' => 'nullable|string',
            'vehicle_number' => 'nullable|string',
            'destination_city' => 'nullable|string',
            'total_cartons' => 'required|integer|min:0',
            'total_qty' => 'required|integer|min:0',
            'status' => 'nullable|string'
        ]);

        $challan->update($validated);

        if ($challan->items()->exists()) {
            $challan->items()->first()->update([
                'qty' => $validated['total_qty']
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'challan' => $challan->load('items')]);
        }

        return redirect()->route('dispatch.challans.index')->with('success', "Dispatch Challan {$challan->challan_no} updated successfully.");
    }

    public function show(DispatchChallan $challan)
    {
        return response()->json($challan->load('items'));
    }

    public function destroy(DispatchChallan $challan)
    {
        $no = $challan->challan_no;
        $challan->items()->delete();
        $challan->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => "Dispatch challan {$no} removed."]);
        }

        return redirect()->route('dispatch.challans.index')->with('success', "Dispatch challan {$no} removed.");
    }
}
