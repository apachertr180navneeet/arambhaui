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
        $vendors = Vendor::latest()->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($vendors);
        }

        $stats = [
            'total' => Vendor::count(),
            'fabricVendors' => Vendor::where('category', 'Fabric')->count(),
            'trimVendors' => Vendor::where('category', 'Trims & Accessories')->count()
        ];

        return view('masters.vendors.index', compact('vendors', 'stats'));
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

        return redirect()->route('masters.vendors.index')->with('success', "Vendor {$vendor->name} created successfully.");
    }

    public function show(Vendor $vendor)
    {
        return response()->json($vendor);
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'category' => 'nullable|string',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'gstin' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'credit_days' => 'nullable|integer',
            'status' => 'nullable|string'
        ]);

        $vendor->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'vendor' => $vendor]);
        }

        return redirect()->route('masters.vendors.index')->with('success', "Vendor {$vendor->name} updated successfully.");
    }

    public function destroy(Vendor $vendor)
    {
        $name = $vendor->name;
        $vendor->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => "Vendor {$name} deleted."]);
        }

        return redirect()->route('masters.vendors.index')->with('success', "Vendor {$name} deleted successfully.");
    }
}
