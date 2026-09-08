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
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(Customer::latest()->get());
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'masters',
            'submodule' => 'customers'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'gstin' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'credit_limit' => 'nullable|numeric',
            'payment_terms' => 'nullable|string'
        ]);

        $count = Customer::count() + 1;
        $validated['code'] = 'CUST-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        $customer = Customer::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'customer' => $customer]);
        }

        return redirect()->route('masters.customers.index')->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        return response()->json($customer->load(['invoices', 'payments']));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'gstin' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'credit_limit' => 'nullable|numeric',
            'status' => 'nullable|string'
        ]);

        $customer->update($validated);
        return response()->json(['success' => true, 'customer' => $customer]);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(['success' => true, 'message' => 'Customer deleted.']);
    }
}
