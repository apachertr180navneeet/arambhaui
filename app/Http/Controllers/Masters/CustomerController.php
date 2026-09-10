<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount(['payments']);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('gstin', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        $customers = $query->latest()->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($customers);
        }

        $totalCount = Customer::count();
        $activeCount = Customer::where('status', 'Active')->count();
        $totalOutstanding = Customer::sum('outstanding');
        $totalCreditLimit = Customer::sum('credit_limit');

        $stats = [
            'total' => $totalCount,
            'active' => $activeCount,
            'totalOutstanding' => $totalOutstanding,
            'totalCreditLimit' => $totalCreditLimit
        ];

        $stats = $this->getStats();
        return view('masters.customers.index', compact('customers', 'stats'));
    }

    private function getStats()
    {
        return [
            'total' => Customer::count(),
            'active' => Customer::whereRaw('LOWER(status) = ?', ['active'])->count(),
            'totalOutstanding' => (float) Customer::sum('outstanding'),
            'totalCreditLimit' => (float) Customer::sum('credit_limit')
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
            'company_name' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'gstin' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'credit_limit' => 'nullable|numeric',
            'outstanding' => 'nullable|numeric',
            'payment_terms' => 'nullable|string|max:100',
            'status' => 'nullable|string'
        ]);

        if (empty($validated['status'])) {
            $validated['status'] = 'Active';
        } else {
            $validated['status'] = ucfirst(strtolower($validated['status']));
        }

        if (empty($validated['code'])) {
            $count = Customer::count() + 1;
            $validated['code'] = 'CUST-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        $customer = Customer::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'customer' => $customer,
                'stats' => $this->getStats(),
                'message' => "Customer {$customer->name} created successfully."
            ]);
        }

        return redirect()->route('masters.customers.index')->with('success', "Customer {$customer->name} created successfully.");
    }

    public function show(Customer $customer)
    {
        return response()->json([
            'success' => true,
            'customer' => $customer->load(['payments'])
        ]);
    }

    public function update(Request $request, Customer $customer)
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
            'company_name' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'gstin' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'credit_limit' => 'nullable|numeric',
            'outstanding' => 'nullable|numeric',
            'payment_terms' => 'nullable|string|max:100',
            'status' => 'nullable|string'
        ]);

        if (isset($validated['status'])) {
            $validated['status'] = ucfirst(strtolower($validated['status']));
        }

        $customer->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'customer' => $customer,
                'stats' => $this->getStats(),
                'message' => "Customer {$customer->name} updated successfully."
            ]);
        }

        return redirect()->route('masters.customers.index')->with('success', "Customer {$customer->name} updated successfully.");
    }

    public function destroy(Customer $customer)
    {
        $name = $customer->name;
        $customer->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'stats' => $this->getStats(),
                'message' => "Customer {$name} deleted successfully."
            ]);
        }

        return redirect()->route('masters.customers.index')->with('success', "Customer {$name} deleted successfully.");
    }

    
    public function statement(Customer $customer)
    {
        $payments = $customer->payments()->latest()->get();

        return response()->json([
            'success' => true,
            'customer' => $customer,
            'invoices' => [],
            'payments' => $payments,
            'current_outstanding' => $customer->outstanding
        ]);
    }
}
