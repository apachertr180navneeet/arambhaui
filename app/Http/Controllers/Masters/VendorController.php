<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(Vendor::latest()->get());
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'masters',
            'submodule' => 'vendors'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'category' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'gstin' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'credit_days' => 'nullable|integer'
        ]);

        $count = Vendor::count() + 1;
        $validated['code'] = 'VEND-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        $vendor = Vendor::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'vendor' => $vendor]);
        }

        return redirect()->route('masters.vendors.index')->with('success', 'Vendor created successfully.');
    }

    public function show(Vendor $vendor)
    {
        return response()->json($vendor->load(['purchaseOrders', 'purchaseInwards']));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'category' => 'nullable|string',
            'credit_days' => 'nullable|integer',
            'status' => 'nullable|string'
        ]);

        $vendor->update($validated);
        return response()->json(['success' => true, 'vendor' => $vendor]);
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
        return response()->json(['success' => true, 'message' => 'Vendor deleted.']);
    }
}
