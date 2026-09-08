<?php

namespace App\Http\Controllers\Invoices;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\QrVoucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(Invoice::with('items')->latest()->get());
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'invoices',
            'submodule' => 'list'
        ]);
    }

    public function create()
    {
        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'invoices',
            'submodule' => 'create'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'order_no' => 'nullable|string',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date',
            'voucher_code' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_description' => 'required|string',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.rate' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $count = Invoice::count() + 1;
            $invoiceNo = 'INV-2026-' . str_pad($count, 3, '0', STR_PAD_LEFT);

            $subtotal = 0;
            foreach ($validated['items'] as $it) {
                $subtotal += ($it['qty'] * $it['rate']);
            }

            // Discount calculation if voucher supplied
            $discountAmount = 0;
            if (!empty($validated['voucher_code'])) {
                $voucher = QrVoucher::where('voucher_code', $validated['voucher_code'])->first();
                if ($voucher && !$voucher->is_redeemed) {
                    $calc = ($subtotal * $voucher->discount_percent) / 100;
                    $discountAmount = min($calc, $voucher->max_discount_cap);
                    $voucher->update([
                        'is_redeemed' => true,
                        'redeemed_at' => now(),
                        'redeemed_invoice_no' => $invoiceNo,
                        'status' => 'Redeemed'
                    ]);
                }
            }

            $taxable = max(0, $subtotal - $discountAmount);
            $cgst = ($taxable * 2.5) / 100;
            $sgst = ($taxable * 2.5) / 100;
            $grandTotal = $taxable + $cgst + $sgst;

            $invoice = Invoice::create([
                'invoice_no' => $invoiceNo,
                'order_no' => $validated['order_no'] ?? null,
                'customer_name' => $validated['customer_name'],
                'company_name' => $validated['company_name'] ?? null,
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'voucher_code' => $validated['voucher_code'] ?? null,
                'taxable_amount' => $taxable,
                'cgst_rate' => 2.5,
                'cgst_amount' => $cgst,
                'sgst_rate' => 2.5,
                'sgst_amount' => $sgst,
                'grand_total' => $grandTotal,
                'paid_amount' => 0.00,
                'balance_due' => $grandTotal,
                'status' => 'Sent',
                'notes' => $validated['notes'] ?? null
            ]);

            foreach ($validated['items'] as $it) {
                $amount = $it['qty'] * $it['rate'];
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_description' => $it['item_description'],
                    'qty' => $it['qty'],
                    'rate' => $it['rate'],
                    'amount' => $amount
                ]);
            }

            // Update customer outstanding
            $cust = Customer::where('name', $validated['customer_name'])->first();
            if ($cust) {
                $cust->increment('outstanding', $grandTotal);
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'invoice' => $invoice->load('items')]);
            }

            return redirect()->route('invoices.list.index')->with('success', "Invoice {$invoiceNo} created.");
        });
    }

    public function show(Invoice $invoice)
    {
        return response()->json($invoice->load('items'));
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->items()->delete();
        $invoice->delete();
        return response()->json(['success' => true, 'message' => 'Invoice removed.']);
    }
}
