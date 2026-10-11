<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Vendor;
use App\Models\VendorPayment;
use App\Models\PurchasePaymentAllocation;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PaymentMappingController extends Controller
{
    /**
     * Display Split-Screen Purchase Bill & Payment Mapping Interface
     */
    public function index(Request $request)
    {
        $vendors = Vendor::whereRaw('LOWER(status) = ?', ['active'])->orderBy('name')->get();
        if ($vendors->isEmpty()) {
            $vendors = Vendor::orderBy('name')->get();
        }

        $selectedVendorId = $request->input('vendor_id');
        $selectedVendor = null;

        if (!empty($selectedVendorId)) {
            $selectedVendor = Vendor::find($selectedVendorId);
        }

        // If no vendor is explicitly selected, default to the first vendor
        if (!$selectedVendor && $vendors->isNotEmpty()) {
            $selectedVendor = $vendors->first();
            $selectedVendorId = $selectedVendor->id;
        }

        $bills = collect();
        $payments = collect();
        $metrics = [
            'totalBillsAmount' => 0.0,
            'totalPaidAmount' => 0.0,
            'netOutstanding' => 0.0,
            'totalPaymentsRecorded' => 0.0,
            'totalUnallocatedPayments' => 0.0,
            'unpaidBillsCount' => 0,
            'partiallyPaidCount' => 0,
            'paidBillsCount' => 0,
        ];

        if ($selectedVendor) {
            // 1. Fetch Purchase Bills for Selected Vendor
            $billsQuery = PurchaseOrder::where(function ($q) use ($selectedVendor) {
                $q->where('vendor_id', $selectedVendor->id)
                  ->orWhere('vendor_name', $selectedVendor->name);
            })->where('status', '!=', 'Cancelled')
              ->with('allocations.payment')
              ->orderBy('po_date', 'asc');

            // Optional status filter
            $statusFilter = $request->input('status');
            if ($statusFilter === 'Unpaid' || $statusFilter === 'Pending') {
                $billsQuery->where('payment_status', 'Pending');
            } elseif ($statusFilter === 'Partially Paid') {
                $billsQuery->where('payment_status', 'Partially Paid');
            } elseif ($statusFilter === 'Paid') {
                $billsQuery->where('payment_status', 'Paid');
            }

            $bills = $billsQuery->get();

            // 2. Fetch Payments Recorded for Selected Vendor
            $payments = VendorPayment::where(function ($q) use ($selectedVendor) {
                $q->where('vendor_id', $selectedVendor->id)
                  ->orWhere('vendor_name', $selectedVendor->name);
            })->with('allocations.purchaseOrder')
              ->orderBy('payment_date', 'desc')
              ->orderBy('id', 'desc')
              ->get();

            // 3. Compute Metrics
            foreach ($bills as $b) {
                $bPaid = (float) $b->allocations->sum('allocated_amount');
                $bOut = max(0, round($b->grand_total - $bPaid, 2));
                $metrics['totalBillsAmount'] += $b->grand_total;
                $metrics['totalPaidAmount'] += $bPaid;
                $metrics['netOutstanding'] += $bOut;

                if ($bOut <= 0.001) {
                    $metrics['paidBillsCount']++;
                } elseif ($bPaid > 0.001) {
                    $metrics['partiallyPaidCount']++;
                } else {
                    $metrics['unpaidBillsCount']++;
                }
            }

            foreach ($payments as $p) {
                $pAlloc = (float) $p->allocations->sum('allocated_amount');
                $pUnalloc = max(0, round($p->amount - $pAlloc, 2));
                $metrics['totalPaymentsRecorded'] += $p->amount;
                $metrics['totalUnallocatedPayments'] += $pUnalloc;
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'vendor' => $selectedVendor,
                'bills' => $bills,
                'payments' => $payments,
                'metrics' => $metrics
            ]);
        }

        $companySettings = CompanySetting::first();

        return view('purchase.payment-mapping', compact(
            'vendors',
            'selectedVendor',
            'selectedVendorId',
            'bills',
            'payments',
            'metrics',
            'companySettings'
        ));
    }

    /**
     * Record a New Outgoing Vendor Payment
     */
    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|integer|exists:vendors,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'required|string|max:100',
            'reference_no' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:100',
            'notes' => 'nullable|string'
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $vendor = Vendor::findOrFail($validated['vendor_id']);

            $year = date('Y');
            $count = VendorPayment::count() + 1;
            $paymentNo = 'PAY-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            while (VendorPayment::where('payment_no', $paymentNo)->exists()) {
                $count++;
                $paymentNo = 'PAY-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }

            $payment = VendorPayment::create([
                'payment_no' => $paymentNo,
                'vendor_id' => $vendor->id,
                'vendor_name' => $vendor->name,
                'payment_date' => $validated['payment_date'],
                'amount' => round((float) $validated['amount'], 2),
                'payment_mode' => $validated['payment_mode'],
                'reference_no' => $validated['reference_no'] ?? null,
                'bank_name' => $validated['bank_name'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::user()->name ?? 'Admin'
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Payment {$paymentNo} of ₹" . number_format($payment->amount, 2) . " recorded successfully.",
                    'payment' => $payment->load('allocations')
                ]);
            }

            return redirect()->route('purchase.payment-mapping', ['vendor_id' => $vendor->id])
                ->with('success', "Payment {$paymentNo} of ₹" . number_format($payment->amount, 2) . " recorded successfully.");
        });
    }

    /**
     * Allocate Payment Amount to one or more Purchase Bills
     */
    public function allocate(Request $request)
    {
        $validated = $request->validate([
            'vendor_payment_id' => 'required|integer|exists:vendor_payments,id',
            'allocations' => 'required|array|min:1',
            'allocations.*.purchase_order_id' => 'required|integer|exists:purchase_orders,id',
            'allocations.*.amount' => 'required|numeric|min:0.01',
            'allocations.*.notes' => 'nullable|string'
        ]);

        return DB::transaction(function () use ($validated, $request) {
            // Lock payment row to prevent race conditions during concurrent requests
            $payment = VendorPayment::where('id', $validated['vendor_payment_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $incomingPoIds = collect($validated['allocations'])->pluck('purchase_order_id')->all();

            // Total amount already allocated to OTHER bills not part of this request
            $otherAllocationsSum = (float) PurchasePaymentAllocation::where('vendor_payment_id', $payment->id)
                ->whereNotIn('purchase_order_id', $incomingPoIds)
                ->sum('allocated_amount');

            $availableForTheseBills = max(0, round($payment->amount - $otherAllocationsSum, 2));

            $totalRequestedAllocation = 0.0;
            foreach ($validated['allocations'] as $item) {
                $totalRequestedAllocation += (float) $item['amount'];
            }
            $totalRequestedAllocation = round($totalRequestedAllocation, 2);

            // 1. Validation Rule: A payment must not be allocated beyond its available unallocated amount
            if ($totalRequestedAllocation > $availableForTheseBills) {
                $msg = "Cannot allocate ₹" . number_format($totalRequestedAllocation, 2) . 
                       ". Available unallocated amount on payment {$payment->payment_no} is only ₹" . 
                       number_format($availableForTheseBills, 2) . ".";
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return back()->withErrors(['amount' => $msg]);
            }

            $appliedAllocations = [];

            foreach ($validated['allocations'] as $allocItem) {
                $poId = (int) $allocItem['purchase_order_id'];
                $allocAmount = round((float) $allocItem['amount'], 2);

                if ($allocAmount <= 0) continue;

                // Lock purchase bill row
                $bill = PurchaseOrder::where('id', $poId)->lockForUpdate()->firstOrFail();

                // Sum of payments already allocated to this bill from OTHER payments
                $otherPaymentsForBill = (float) PurchasePaymentAllocation::where('purchase_order_id', $bill->id)
                    ->where('vendor_payment_id', '!=', $payment->id)
                    ->sum('allocated_amount');

                $billMaxAllowed = max(0, round($bill->grand_total - $otherPaymentsForBill, 2));

                // 2. Validation Rule: A bill must not be overpaid beyond its outstanding amount
                if ($allocAmount > $billMaxAllowed) {
                    $billNo = $bill->challan_no ?: $bill->po_number;
                    $msg = "Allocation of ₹" . number_format($allocAmount, 2) . " exceeds the outstanding amount of Bill {$billNo} (Max allowed: ₹" . number_format($billMaxAllowed, 2) . ").";
                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json(['success' => false, 'message' => $msg], 422);
                    }
                    return back()->withErrors(['amount' => $msg]);
                }

                // Upsert allocation record
                $allocation = PurchasePaymentAllocation::updateOrCreate(
                    [
                        'vendor_payment_id' => $payment->id,
                        'purchase_order_id' => $bill->id,
                    ],
                    [
                        'allocated_amount' => $allocAmount,
                        'notes' => $allocItem['notes'] ?? null
                    ]
                );

                $appliedAllocations[] = $allocation;

                // Recalculate bill payment status
                $newTotalPaid = $otherPaymentsForBill + $allocAmount;
                $newStatus = 'Pending';
                if ($newTotalPaid >= $bill->grand_total - 0.001) {
                    $newStatus = 'Paid';
                } elseif ($newTotalPaid > 0.001) {
                    $newStatus = 'Partially Paid';
                }
                $bill->update(['payment_status' => $newStatus]);
            }

            // Sync Vendor outstanding balance
            $this->syncVendorOutstanding($payment->vendor_id);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Payment of ₹" . number_format($totalRequestedAllocation, 2) . " allocated successfully.",
                    'allocations' => $appliedAllocations
                ]);
            }

            return redirect()->route('purchase.payment-mapping', ['vendor_id' => $payment->vendor_id])
                ->with('success', "Payment allocated successfully.");
        });
    }

    /**
     * Remove or Adjust an Existing Payment Allocation
     */
    public function removeAllocation(Request $request, $id)
    {
        return DB::transaction(function () use ($id, $request) {
            $allocation = PurchasePaymentAllocation::findOrFail($id);
            $paymentId = $allocation->vendor_payment_id;
            $billId = $allocation->purchase_order_id;
            $amount = $allocation->allocated_amount;

            $bill = PurchaseOrder::where('id', $billId)->lockForUpdate()->first();
            $payment = VendorPayment::where('id', $paymentId)->lockForUpdate()->first();

            $allocation->delete();

            if ($bill) {
                // Recompute bill's total paid
                $totalPaid = (float) PurchasePaymentAllocation::where('purchase_order_id', $bill->id)->sum('allocated_amount');
                $newStatus = 'Pending';
                if ($totalPaid >= $bill->grand_total - 0.001) {
                    $newStatus = 'Paid';
                } elseif ($totalPaid > 0.001) {
                    $newStatus = 'Partially Paid';
                }
                $bill->update(['payment_status' => $newStatus]);
            }

            if ($payment && $payment->vendor_id) {
                $this->syncVendorOutstanding($payment->vendor_id);
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Allocation of ₹" . number_format($amount, 2) . " removed. Balances refreshed."
                ]);
            }

            return back()->with('success', "Allocation removed. Balances refreshed.");
        });
    }

    /**
     * Helper to keep Vendor's total outstanding in sync from actual bill balances
     */
    protected function syncVendorOutstanding($vendorId)
    {
        if (!$vendorId) return;
        $vendor = Vendor::find($vendorId);
        if (!$vendor) return;

        $bills = PurchaseOrder::where('vendor_id', $vendor->id)
            ->where('status', '!=', 'Cancelled')
            ->with('allocations')
            ->get();

        $netOutstanding = 0.0;
        foreach ($bills as $b) {
            $paid = (float) $b->allocations->sum('allocated_amount');
            $netOutstanding += max(0, round($b->grand_total - $paid, 2));
        }

        $vendor->update(['outstanding' => round($netOutstanding, 2)]);
    }
}
