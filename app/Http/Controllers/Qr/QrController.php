<?php

namespace App\Http\Controllers\Qr;

use App\Http\Controllers\Controller;
use App\Models\QrVoucher;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QrController extends Controller
{
    public function generator(Request $request)
    {
        $vouchers = QrVoucher::where('status', 'Active')->latest()->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($vouchers);
        }

        $customers = Customer::all();

        return view('qr.generator', compact('vouchers', 'customers'));
    }

    public function scanner(Request $request)
    {
        $code = $request->query('code', '');
        return view('qr.scanner', compact('code'));
    }

    public function history(Request $request)
    {
        $vouchers = QrVoucher::latest()->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($vouchers);
        }

        $stats = [
            'total' => QrVoucher::count(),
            'active' => QrVoucher::where('status', 'Active')->count(),
            'redeemed' => QrVoucher::where('status', 'Redeemed')->count(),
            'expired' => QrVoucher::where('status', 'Expired')->count()
        ];

        return view('qr.history', compact('vouchers', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'voucher_code' => 'nullable|string|max:50|unique:qr_vouchers,voucher_code',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:25',
            'title' => 'nullable|string|max:255',
            'discount_type' => 'nullable|in:Percentage,Flat,percent,fixed',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'max_discount_cap' => 'nullable|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date'
        ]);

        $discType = in_array(strtolower($validated['discount_type'] ?? ''), ['flat', 'fixed']) ? 'Flat' : 'Percentage';
        $percent = $discType === 'Percentage' ? (float)($validated['discount_percent'] ?? 10) : 0;
        $amount = $discType === 'Flat' ? (float)($validated['discount_amount'] ?? 500) : null;

        if (!empty($validated['voucher_code'])) {
            $voucherCode = strtoupper(trim($validated['voucher_code']));
        } else {
            $randomSuffix = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));
            $prefix = $discType === 'Percentage' ? "FASHION-{$percent}" : "SAVE-{$amount}";
            $voucherCode = "{$prefix}-{$randomSuffix}";
        }

        $voucher = QrVoucher::create([
            'voucher_code' => $voucherCode,
            'customer_name' => $validated['customer_name'] ?? 'General Promotion',
            'customer_phone' => $validated['customer_phone'] ?? null,
            'title' => $validated['title'] ?? 'Special Customer Discount',
            'discount_type' => $discType,
            'discount_percent' => $percent,
            'discount_amount' => $amount,
            'max_discount_cap' => $validated['max_discount_cap'] ?? 5000,
            'min_order_value' => $validated['min_order_value'] ?? 1000,
            'valid_from' => $validated['valid_from'] ?? now()->toDateString(),
            'valid_until' => $validated['valid_until'] ?? now()->addDays(30)->toDateString(),
            'status' => 'Active',
            'is_redeemed' => false,
            'qr_payload' => json_encode([
                'code' => $voucherCode,
                'type' => $discType,
                'discount' => $discType === 'Percentage' ? $percent : $amount,
                'max' => (float)($validated['max_discount_cap'] ?? 5000),
                'issuer' => 'GarmentERP'
            ])
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'voucher' => $voucher], 201);
        }

        return redirect()->route('qr.history')->with('success', "Single-use discount QR Voucher {$voucherCode} generated successfully.");
    }

    public function validateVoucher(Request $request)
    {
        $code = strtoupper(trim($request->input('voucher_code', '')));
        $voucher = QrVoucher::where('voucher_code', $code)->first();

        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Invalid voucher code. Code not found in database.'], 404);
        }

        if ($voucher->is_redeemed || $voucher->status === 'Redeemed') {
            $redeemedAtText = $voucher->redeemed_at instanceof \DateTimeInterface ? $voucher->redeemed_at->format('d M Y, h:i A') : (string)($voucher->redeemed_at ?: 'earlier session');
            return response()->json([
                'success' => false,
                'already_redeemed' => true,
                'message' => "This single-use voucher has already been redeemed on {$redeemedAtText}.",
                'voucher' => $voucher
            ], 422);
        }

        if ($voucher->status === 'Expired' || ($voucher->valid_until && strtotime($voucher->valid_until) < strtotime(date('Y-m-d')))) {
            return response()->json([
                'success' => false,
                'expired' => true,
                'message' => "This voucher has expired on " . date('d M Y', strtotime($voucher->valid_until)) . ".",
                'voucher' => $voucher
            ], 422);
        }

        return response()->json([
            'success' => true,
            'voucher' => $voucher,
            'discount_type' => $voucher->discount_type,
            'discount_percent' => $voucher->discount_percent,
            'discount_amount' => $voucher->discount_amount,
            'max_cap' => $voucher->max_discount_cap,
            'min_bill' => $voucher->min_order_value
        ]);
    }

    public function redeemVoucher(Request $request)
    {
        $validated = $request->validate([
            'voucher_code' => 'required|string',
            'customer_phone' => 'required|string',
            'order_bill' => 'nullable|numeric|min:0'
        ]);

        $code = strtoupper(trim($validated['voucher_code']));
        $phone = trim($validated['customer_phone']);

        $voucher = QrVoucher::where('voucher_code', $code)->first();

        if (!$voucher) {
            return response()->json(['success' => false, 'message' => "Voucher code '{$code}' not found."], 404);
        }

        if ($voucher->is_redeemed || $voucher->status === 'Redeemed') {
            $redeemedAtText = $voucher->redeemed_at instanceof \DateTimeInterface ? $voucher->redeemed_at->format('d M Y, h:i A') : (string)($voucher->redeemed_at ?: 'previous transaction');
            return response()->json([
                'success' => false,
                'already_redeemed' => true,
                'message' => "Single-use security lock: This QR voucher has already been redeemed by " . ($voucher->customer_phone ?: 'customer') . " on {$redeemedAtText}.",
                'voucher' => $voucher
            ], 422);
        }

        if ($voucher->status === 'Expired' || ($voucher->valid_until && strtotime($voucher->valid_until) < strtotime(date('Y-m-d')))) {
            return response()->json([
                'success' => false,
                'expired' => true,
                'message' => "This voucher expired on " . date('d M Y', strtotime($voucher->valid_until)) . " and can no longer be claimed.",
                'voucher' => $voucher
            ], 422);
        }

        // Apply single-use redemption and permanently mark expired
        $claimId = 'CLM-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        $voucher->is_redeemed = true;
        $voucher->status = 'Redeemed';
        $voucher->redeemed_at = now();
        $voucher->customer_phone = $phone;
        $voucher->redeemed_invoice_no = $claimId;
        $voucher->save();

        $bill = (float)($validated['order_bill'] ?? 3500);
        $discountValue = 0;
        if ($voucher->discount_type === 'Percentage') {
            $discountValue = ($bill * (float)$voucher->discount_percent) / 100;
            if ($voucher->max_discount_cap && $discountValue > $voucher->max_discount_cap) {
                $discountValue = (float)$voucher->max_discount_cap;
            }
        } else {
            $discountValue = (float)($voucher->discount_amount ?: $voucher->discount_percent ?: 500);
        }

        $finalPayable = max(0, $bill - $discountValue);
        $redeemedAtFormatted = $voucher->redeemed_at instanceof \DateTimeInterface ? $voucher->redeemed_at->format('d M Y, h:i A') : (string)$voucher->redeemed_at;

        return response()->json([
            'success' => true,
            'claim_id' => $claimId,
            'voucher' => $voucher,
            'phone' => $phone,
            'discount_value' => $discountValue,
            'original_bill' => $bill,
            'final_payable' => $finalPayable,
            'redeemed_at' => $redeemedAtFormatted,
            'message' => "Discount successfully claimed for {$phone}! Voucher is now single-use expired."
        ]);
    }

    public function expireVoucher($id)
    {
        $voucher = QrVoucher::findOrFail($id);
        $voucher->status = 'Expired';
        $voucher->save();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => "Voucher {$voucher->voucher_code} marked as Expired."]);
        }

        return redirect()->route('qr.history')->with('success', "Voucher {$voucher->voucher_code} marked as Expired.");
    }

    public function reactivateVoucher($id)
    {
        $voucher = QrVoucher::findOrFail($id);
        $voucher->status = 'Active';
        $voucher->is_redeemed = false;
        $voucher->redeemed_at = null;
        $voucher->save();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => "Voucher {$voucher->voucher_code} reactivated."]);
        }

        return redirect()->route('qr.history')->with('success', "Voucher {$voucher->voucher_code} reactivated.");
    }

    public function destroy($id)
    {
        $voucher = QrVoucher::findOrFail($id);
        $code = $voucher->voucher_code;
        $voucher->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => "Voucher {$code} deleted successfully."]);
        }

        return redirect()->route('qr.history')->with('success', "Voucher {$code} deleted successfully.");
    }
}
