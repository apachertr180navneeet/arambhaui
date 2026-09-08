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
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(QrVoucher::where('status', 'Active')->get());
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'qr',
            'submodule' => 'generator'
        ]);
    }

    public function scanner(Request $request)
    {
        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'qr',
            'submodule' => 'scanner'
        ]);
    }

    public function history(Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(QrVoucher::latest()->get());
        }

        return view('dashboard', [
            'user' => Auth::user(),
            'module' => 'qr',
            'submodule' => 'history'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string',
            'discount_percent' => 'required|numeric|min:1|max:100',
            'max_discount_cap' => 'nullable|numeric',
            'min_order_value' => 'nullable|numeric',
            'valid_until' => 'required|date'
        ]);

        $randomSuffix = strtoupper(substr(md5(uniqid()), 0, 5));
        $voucherCode = 'FASHION-' . (int)$validated['discount_percent'] . '-' . $randomSuffix;

        $voucher = QrVoucher::create([
            'voucher_code' => $voucherCode,
            'customer_name' => $validated['customer_name'] ?? 'General Promotion',
            'discount_percent' => $validated['discount_percent'],
            'max_discount_cap' => $validated['max_discount_cap'] ?? 5000,
            'min_order_value' => $validated['min_order_value'] ?? 25000,
            'valid_from' => now()->toDateString(),
            'valid_until' => $validated['valid_until'],
            'status' => 'Active',
            'qr_payload' => json_encode([
                'code' => $voucherCode,
                'discount' => (float)$validated['discount_percent'],
                'max' => (float)($validated['max_discount_cap'] ?? 5000),
                'issuer' => 'GarmentERP'
            ])
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'voucher' => $voucher]);
        }

        return redirect()->route('qr.generator')->with('success', "Voucher {$voucherCode} generated.");
    }

    public function validateVoucher(Request $request)
    {
        $code = $request->input('voucher_code');
        $voucher = QrVoucher::where('voucher_code', $code)->first();

        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Invalid voucher code.'], 404);
        }

        if ($voucher->is_redeemed || $voucher->status !== 'Active') {
            return response()->json(['success' => false, 'message' => 'This voucher has already been redeemed or is expired.'], 422);
        }

        return response()->json([
            'success' => true,
            'voucher' => $voucher,
            'discount_percent' => $voucher->discount_percent,
            'max_cap' => $voucher->max_discount_cap
        ]);
    }
}
