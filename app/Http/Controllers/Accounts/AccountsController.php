<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\CustomerPayment;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\JobWorker;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountsController extends Controller
{
    public function customerAccounts(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(Customer::all());
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'accounts',
            'submodule' => 'customer-accounts'
        ]);
    }

    public function customerOutstanding(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(Customer::where('outstanding', '>', 0)->get());
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'accounts',
            'submodule' => 'customer-outstanding'
        ]);
    }

    public function vendorOutstanding(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(Vendor::all());
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'accounts',
            'submodule' => 'vendor-outstanding'
        ]);
    }

    public function jobWorkerOutstanding(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(JobWorker::all());
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'accounts',
            'submodule' => 'jobworker-outstanding'
        ]);
    }

    public function storeReceipt(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'payment_mode' => 'required|string',
            'reference_no' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $count = CustomerPayment::count() + 1;
            $receiptNo = 'REC-2026-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            $validated['receipt_no'] = $receiptNo;

            $payment = CustomerPayment::create($validated);

            // Deduct outstanding
            $cust = Customer::where('name', $validated['customer_name'])->first();
            if ($cust) {
                $cust->decrement('outstanding', min($cust->outstanding, $validated['amount']));
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'payment' => $payment]);
            }

            return redirect()->route('accounts.customer-accounts')->with('success', "Payment receipt {$receiptNo} recorded.");
        });
    }
}
