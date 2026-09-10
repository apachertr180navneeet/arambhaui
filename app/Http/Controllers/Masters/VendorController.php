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
        $query = Vendor::withCount(['purchaseOrders']);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('gstin', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }

        $vendors = $query->latest()->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($vendors);
        }

        $stats = $this->getStats();
        return view('masters.vendors.index', compact('vendors', 'stats'));
    }

    private function getStats()
    {
        $total = Vendor::count();
        $active = Vendor::whereRaw('LOWER(status) = ?', ['active'])->count();
        $fabricVendors = Vendor::where(function ($q) {
            $q->where('category', 'like', '%fabric%')
              ->orWhere('category', 'like', '%mill%')
              ->orWhere('category', 'like', '%yarn%');
        })->count();
        $trimVendors = Vendor::where(function ($q) {
            $q->where('category', 'like', '%trim%')
              ->orWhere('category', 'like', '%accessories%')
              ->orWhere('category', 'like', '%packaging%');
        })->count();

        return [
            'total' => $total,
            'active' => $active,
            'fabricVendors' => $fabricVendors,
            'trimVendors' => $trimVendors,
            'totalOutstanding' => (float) Vendor::sum('outstanding')
        ];
    }

    public function store(Request $request)
    {
        // Support alias fields
        if ($request->filled('gst_number') && !$request->filled('gstin')) {
            $request->merge(['gstin' => $request->input('gst_number')]);
        }
        if ($request->filled('billing_address') && !$request->filled('address')) {
            $request->merge(['address' => $request->input('billing_address')]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'code' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'gstin' => 'nullable|string|max:50',
            'pan_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'credit_days' => 'nullable|integer',
            'outstanding' => 'nullable|numeric',
            'status' => 'nullable|string'
        ]);

        if (empty($validated['status'])) {
            $validated['status'] = 'Active';
        } else {
            $validated['status'] = ucfirst(strtolower($validated['status']));
        }

        if (empty($validated['code'])) {
            $count = Vendor::count() + 1;
            $validated['code'] = 'VEND-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        if (empty($validated['category'])) {
            $validated['category'] = 'Fabric Mill';
        }

        $vendor = Vendor::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'vendor' => $vendor,
                'stats' => $this->getStats(),
                'message' => "Vendor {$vendor->name} created successfully."
            ]);
        }

        return redirect()->route('masters.vendors.index')->with('success', "Vendor {$vendor->name} created successfully.");
    }

    public function show(Vendor $vendor)
    {
        return response()->json([
            'success' => true,
            'vendor' => $vendor->load(['purchaseOrders'])
        ]);
    }

    public function update(Request $request, Vendor $vendor)
    {
        // Support alias fields
        if ($request->filled('gst_number') && !$request->filled('gstin')) {
            $request->merge(['gstin' => $request->input('gst_number')]);
        }
        if ($request->filled('billing_address') && !$request->filled('address')) {
            $request->merge(['address' => $request->input('billing_address')]);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:30',
            'code' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'gstin' => 'nullable|string|max:50',
            'pan_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'credit_days' => 'nullable|integer',
            'outstanding' => 'nullable|numeric',
            'status' => 'nullable|string'
        ]);

        if (isset($validated['status'])) {
            $validated['status'] = ucfirst(strtolower($validated['status']));
        }

        $vendor->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'vendor' => $vendor,
                'stats' => $this->getStats(),
                'message' => "Vendor {$vendor->name} updated successfully."
            ]);
        }

        return redirect()->route('masters.vendors.index')->with('success', "Vendor {$vendor->name} updated successfully.");
    }

    public function destroy(Vendor $vendor)
    {
        $name = $vendor->name;
        $vendor->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'stats' => $this->getStats(),
                'message' => "Vendor {$name} deleted successfully."
            ]);
        }

        return redirect()->route('masters.vendors.index')->with('success', "Vendor {$name} deleted successfully.");
    }

    public function statement(Vendor $vendor)
    {
        $orders = $vendor->purchaseOrders()->latest()->get();

        return response()->json([
            'success' => true,
            'vendor' => $vendor,
            'purchase_orders' => $orders,
            'current_outstanding' => $vendor->outstanding
        ]);
    }
}
