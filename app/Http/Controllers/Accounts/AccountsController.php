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
        $customers = Customer::with('payments')->latest()->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($customers);
        }

        $recentPayments = CustomerPayment::latest()->take(10)->get();
        $totalOutstanding = Customer::sum('outstanding');
        $totalCollected = CustomerPayment::sum('amount');

        $stats = [
            'totalCustomers' => $customers->count(),
            'totalOutstanding' => $totalOutstanding,
            'totalCollected' => $totalCollected
        ];

        return view('accounts.customer-accounts', compact('customers', 'recentPayments', 'stats'));
    }

    public function customerOutstanding(Request $request)
    {
        $customers = Customer::where('outstanding', '>', 0)->latest()->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($customers);
        }

        $totalOutstanding = $customers->sum('outstanding');
        $highRiskCount = $customers->filter(function($c) {
            return $c->credit_limit > 0 && ($c->outstanding / $c->credit_limit) >= 0.8;
        })->count();

        $stats = [
            'dueAccounts' => $customers->count(),
            'totalOutstanding' => $totalOutstanding,
            'highRiskCount' => $highRiskCount
        ];

        return view('accounts.customer-outstanding', compact('customers', 'stats'));
    }

    public function vendorOutstanding(Request $request)
    {
        $vendors = Vendor::latest()->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($vendors);
        }

        return view('accounts.vendor-outstanding', compact('vendors'));
    }

    public function jobWorkerOutstanding(Request $request)
    {
        $jobworkers = JobWorker::latest()->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($jobworkers);
        }

        return view('accounts.jobworker-outstanding', compact('jobworkers'));
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

            $cust = Customer::where('name', $validated['customer_name'])->first();
            if ($cust) {
                $validated['customer_id'] = $cust->id;
            }

            $payment = CustomerPayment::create($validated);

            // Deduct outstanding
            if ($cust) {
                $cust->decrement('outstanding', min($cust->outstanding, $validated['amount']));
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'payment' => $payment]);
            }

            return redirect()->route('accounts.customer-accounts')->with('success', "Payment receipt {$receiptNo} recorded successfully.");
        });
    }
}
