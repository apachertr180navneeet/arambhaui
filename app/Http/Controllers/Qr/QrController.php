<?php

namespace App\Http\Controllers\Qr;

use App\Http\Controllers\Controller;
use App\Models\QrVoucher;
use App\Models\Customer;
use Illuminate\Http\Request;

class QrController extends Controller
{
    public function generator(Request $request)
    {
        $vouchers = QrVoucher::where('status', 'Active')->latest()->take(10)->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($vouchers);
        }

        $customers = Customer::all();

        return view('qr.generator', compact('vouchers', 'customers'));
    }

    public static function cleanVoucherCode($input)
    {
        if (empty($input)) {
            return '';
        }

        $input = trim((string)$input);

        // If JSON payload was passed (e.g. {"code":"MAJ-500-X7K"})
        if (str_starts_with($input, '{') && str_ends_with($input, '}')) {
            $json = json_decode($input, true);
            if (is_array($json) && !empty($json['code'])) {
                return strtoupper(trim($json['code']));
            }
        }

        // Check query parameters (case-insensitive) like ?code=, &code=, ?voucher=, &c=, &v=
        if (preg_match('/[?&](?:code|voucher|c|v)=([^&#\s]+)/i', $input, $matches)) {
            return strtoupper(trim(urldecode($matches[1])));
        }

        // Check path routes like /claim/CODE or /voucher/CODE
        if (preg_match('/(?:\/claim\/|\/voucher\/|\/qr\/scanner\/)([^\/?&#\s]+)/i', $input, $matches)) {
            $seg = strtoupper(trim(urldecode($matches[1])));
            if (!in_array($seg, ['SCANNER', 'CLAIM', 'QR', 'PUBLIC', 'GENERATOR'])) {
                return $seg;
            }
        }

        // If full URL was passed without standard code query param
        if (preg_match('/^https?:\/\//i', $input)) {
            $parsed = parse_url($input);
            if (!empty($parsed['query'])) {
                parse_str($parsed['query'], $queryParams);
                foreach ($queryParams as $key => $val) {
                    if (in_array(strtolower($key), ['code', 'voucher', 'c', 'v']) && !empty($val)) {
                        return strtoupper(trim($val));
                    }
                }
            }
            if (!empty($parsed['path'])) {
                $segments = array_filter(explode('/', trim($parsed['path'], '/')));
                $lastSegment = end($segments);
                if ($lastSegment && !in_array(strtolower($lastSegment), ['scanner', 'claim', 'qr', 'public', 'generator'])) {
                    return strtoupper(trim(urldecode($lastSegment)));
                }
            }
        }

        // Clean up quotes, whitespace, trailing slash
        $input = trim($input, " \t\n\r\0\x0B\"'\\/");

        return strtoupper($input);
    }

    public function scanner(Request $request, $code = null)
    {
        $code = $code ?: $request->query('code', '');
        $code = self::cleanVoucherCode($code);
        return view('qr.scanner', compact('code'));
    }

    public function history(Request $request)
    {
        $vouchers = QrVoucher::latest()->get()->map(function ($v) {
            $v->claim_url = url('/claim/' . $v->voucher_code);
            return $v;
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($vouchers);
        }

        $stats = [
            'total' => QrVoucher::count(),
            'active' => QrVoucher::where('status', 'Active')->count(),
            'redeemed' => QrVoucher::where('status', 'Redeemed')->count(),
            'expired' => QrVoucher::where('status', 'Expired')->count()
        ];

        $customers = Customer::all();

        return view('qr.history', compact('vouchers', 'stats', 'customers'));
    }

    public function show($id)
    {
        $voucher = QrVoucher::findOrFail($id);
        $voucherData = $voucher->toArray();
        $voucherData['claim_url'] = url('/claim/' . $voucher->voucher_code);
        return response()->json([
            'success' => true,
            'voucher' => $voucherData
        ]);
    }

    public function update(Request $request, $id)
    {
        $voucher = QrVoucher::findOrFail($id);

        $validated = $request->validate([
            'batch_name' => 'nullable|string|max:255',
            'qr_date' => 'nullable|date',
            'amount' => 'nullable|numeric|min:0',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:25',
            'valid_until' => 'nullable|date',
            'status' => 'nullable|in:Active,Expired,Redeemed'
        ]);

        if (isset($validated['batch_name'])) {
            $voucher->batch_name = $validated['batch_name'];
            $voucher->title = $validated['batch_name'];
        }
        if (isset($validated['qr_date'])) {
            $voucher->qr_date = $validated['qr_date'];
            $voucher->valid_from = $validated['qr_date'];
        }
        if (isset($validated['amount'])) {
            $amt = (float)$validated['amount'];
            $voucher->amount = $amt;
            $voucher->discount_amount = $amt;
            $voucher->discount_percent = $amt;
            $voucher->max_discount_cap = $amt;
        }
        if (isset($validated['customer_name'])) $voucher->customer_name = $validated['customer_name'];
        if (isset($validated['customer_phone'])) $voucher->customer_phone = $validated['customer_phone'];
        if (isset($validated['valid_until'])) $voucher->valid_until = $validated['valid_until'];
        if (isset($validated['status'])) {
            $voucher->status = $validated['status'];
            if ($voucher->status === 'Active') {
                $voucher->is_redeemed = false;
            }
        }

        $voucher->qr_payload = url('/claim/' . $voucher->voucher_code);
        $voucher->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Voucher {$voucher->voucher_code} updated successfully.",
                'voucher' => $voucher,
                'claim_url' => url('/claim/' . $voucher->voucher_code)
            ]);
        }

        return redirect()->route('qr.history')->with('success', "Voucher {$voucher->voucher_code} updated successfully.");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'qr_date' => 'required|date',
            'batch_name' => 'required|string|max:255',
            'count' => 'required|integer|min:1|max:1000',
            'amount' => 'required|numeric|min:1',
            'voucher_code' => 'nullable|string|max:50'
        ]);

        $qrDate = $validated['qr_date'];
        $batchName = trim($validated['batch_name']);
        $count = (int)$validated['count'];
        $amount = (float)$validated['amount'];

        $createdVouchers = [];

        for ($i = 1; $i <= $count; $i++) {
            $randomSuffix = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));
            $prefix = "MAJ-" . intval($amount);
            $voucherCode = ($count === 1 && !empty($validated['voucher_code']))
                ? strtoupper(trim($validated['voucher_code']))
                : "{$prefix}-{$randomSuffix}";

            // Ensure uniqueness
            while (QrVoucher::where('voucher_code', $voucherCode)->exists()) {
                $randomSuffix = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
                $voucherCode = "{$prefix}-{$randomSuffix}";
            }

            $claimUrl = url('/claim/' . $voucherCode);

            $voucher = QrVoucher::create([
                'voucher_code' => $voucherCode,
                'batch_name' => $batchName,
                'qr_date' => $qrDate,
                'amount' => $amount,
                'customer_name' => 'General Promotion',
                'customer_phone' => null,
                'title' => $batchName,
                'discount_type' => 'Flat',
                'discount_percent' => $amount,
                'discount_amount' => $amount,
                'max_discount_cap' => $amount,
                'min_order_value' => 0,
                'valid_from' => $qrDate,
                'valid_until' => date('Y-m-d', strtotime('+365 days', strtotime($qrDate))),
                'status' => 'Active',
                'is_redeemed' => false,
                'qr_payload' => $claimUrl
            ]);

            $createdVouchers[] = $voucher;
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Successfully generated {$count} QR Voucher(s) for batch '{$batchName}' with ₹{$amount} value.",
                'vouchers' => $createdVouchers,
                'count' => $count
            ], 201);
        }

        return redirect()->route('qr.history')->with('success', "Batch '{$batchName}' created ({$count} QRs of ₹{$amount} generated successfully).");
    }

    public function validateVoucher(Request $request)
    {
        $raw = $request->input('voucher_code', '');
        $code = self::cleanVoucherCode($raw);

        $voucher = !empty($code) ? QrVoucher::where('voucher_code', $code)->first() : null;
        if (!$voucher && !empty($code)) {
            $voucher = QrVoucher::whereRaw('UPPER(voucher_code) = ?', [$code])->first();
        }

        if (!$voucher) {
            $displayCode = $code ?: htmlspecialchars(substr($raw, 0, 30));
            return response()->json(['success' => false, 'message' => "Invalid voucher code '{$displayCode}'. Code not found in database."], 404);
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

        $amt = (float)($voucher->amount ?: $voucher->discount_amount ?: $voucher->discount_percent ?: 0);

        return response()->json([
            'success' => true,
            'voucher' => $voucher,
            'batch_name' => $voucher->batch_name ?: $voucher->title,
            'amount' => $amt,
            'discount_type' => $voucher->discount_type,
            'discount_percent' => $voucher->discount_percent,
            'discount_amount' => $amt,
            'max_cap' => $voucher->max_discount_cap ?: $amt,
            'min_bill' => $voucher->min_order_value ?: 0
        ]);
    }

    public function redeemVoucher(Request $request)
    {
        $validated = $request->validate([
            'voucher_code' => 'required|string',
            'customer_phone' => 'required|string',
            'order_bill' => 'nullable|numeric|min:0'
        ]);

        $raw = $validated['voucher_code'];
        $code = self::cleanVoucherCode($raw);
        $phone = trim($validated['customer_phone']);

        $voucher = !empty($code) ? QrVoucher::where('voucher_code', $code)->first() : null;
        if (!$voucher && !empty($code)) {
            $voucher = QrVoucher::whereRaw('UPPER(voucher_code) = ?', [$code])->first();
        }

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

        $discountValue = (float)($voucher->amount ?: $voucher->discount_amount ?: $voucher->discount_percent ?: 0);
        $bill = (float)($validated['order_bill'] ?? $discountValue);
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
            'message' => "Discount of ₹{$discountValue} successfully claimed for {$phone}! Voucher is now redeemed."
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
