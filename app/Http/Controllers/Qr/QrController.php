<?php

namespace App\Http\Controllers\Qr;

use App\Http\Controllers\Controller;
use App\Models\QrVoucher;
use App\Models\Customer;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

        $companySettings = CompanySetting::all()->pluck('value', 'key')->toArray();
        $defaultRecipientUpi = $companySettings['recipient_upi_id'] ?? '';
        $defaultRecipientName = $companySettings['recipient_name'] ?? ($companySettings['company_name'] ?? 'Aarambh Garments');
        $defaultRecipientQr = $companySettings['recipient_qr_image'] ?? '';

        return view('qr.scanner', compact('code', 'defaultRecipientUpi', 'defaultRecipientName', 'defaultRecipientQr', 'companySettings'));
    }

    public function uploadRecipientQr(Request $request)
    {
        $request->validate([
            'qr_image' => 'required|image|max:5120', // 5MB max
            'recipient_upi_id' => 'nullable|string|max:100',
            'recipient_name' => 'nullable|string|max:100',
            'save_as_default' => 'nullable'
        ]);

        $file = $request->file('qr_image');
        $fileName = 'recipient_qr_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $destinationPath = public_path('uploads/upi_qr');
        if (!file_exists($destinationPath)) {
            @mkdir($destinationPath, 0777, true);
        }
        $file->move($destinationPath, $fileName);
        $imageUrl = asset('uploads/upi_qr/' . $fileName);

        $upiId = trim($request->input('recipient_upi_id', ''));
        $recipientName = trim($request->input('recipient_name', ''));

        if ($request->boolean('save_as_default') || $request->input('save_as_default') === '1' || $request->input('save_as_default') === 'true') {
            if ($upiId) {
                CompanySetting::updateOrCreate(['key' => 'recipient_upi_id'], ['value' => $upiId, 'group' => 'general']);
            }
            if ($recipientName) {
                CompanySetting::updateOrCreate(['key' => 'recipient_name'], ['value' => $recipientName, 'group' => 'general']);
            }
            CompanySetting::updateOrCreate(['key' => 'recipient_qr_image'], ['value' => $imageUrl, 'group' => 'general']);
            Artisan::call('view:clear');
        }

        return response()->json([
            'success' => true,
            'image_url' => $imageUrl,
            'recipient_upi_id' => $upiId,
            'recipient_name' => $recipientName,
            'message' => 'Recipient UPI QR Scanner uploaded successfully.'
        ]);
    }

    public function saveRecipientSettings(Request $request)
    {
        $validated = $request->validate([
            'recipient_upi_id' => 'required|string|max:100',
            'recipient_name' => 'nullable|string|max:100',
            'recipient_qr_image' => 'nullable|string'
        ]);

        CompanySetting::updateOrCreate(
            ['key' => 'recipient_upi_id'],
            ['value' => trim($validated['recipient_upi_id']), 'group' => 'general']
        );

        if (!empty($validated['recipient_name'])) {
            CompanySetting::updateOrCreate(
                ['key' => 'recipient_name'],
                ['value' => trim($validated['recipient_name']), 'group' => 'general']
            );
        }

        if (!empty($validated['recipient_qr_image'])) {
            CompanySetting::updateOrCreate(
                ['key' => 'recipient_qr_image'],
                ['value' => trim($validated['recipient_qr_image']), 'group' => 'general']
            );
        }

        Artisan::call('view:clear');

        return response()->json([
            'success' => true,
            'message' => 'Default recipient UPI details saved successfully.'
        ]);
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
            'qr_date' => 'nullable|date',
            'batch_name' => 'nullable|string|max:255',
            'count' => 'nullable|integer|min:1|max:2000',
            'amount' => 'nullable|numeric|min:1',
            'voucher_code' => 'nullable|string|max:50',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:25',
            'discount_type' => 'nullable|in:Flat,Percentage',
            'discount_amount' => 'nullable|numeric',
            'discount_percent' => 'nullable|numeric',
            'max_discount_cap' => 'nullable|numeric',
            'min_order_value' => 'nullable|numeric',
            'valid_until' => 'nullable|date',
        ]);

        $qrDate = $validated['qr_date'] ?? date('Y-m-d');
        $batchName = trim($validated['batch_name'] ?? $request->input('title', 'AARAMBH BATCH'));
        $count = (int)($validated['count'] ?? 1);
        $amount = (float)($validated['amount'] ?? $validated['discount_amount'] ?? $validated['discount_percent'] ?? 500);
        $customerName = $validated['customer_name'] ?? 'General Promotion';
        $customerPhone = $validated['customer_phone'] ?? null;
        $discountType = $validated['discount_type'] ?? 'Flat';
        $maxCap = (float)($validated['max_discount_cap'] ?? $amount);
        $minOrder = (float)($validated['min_order_value'] ?? 0);
        $validUntil = $validated['valid_until'] ?? date('Y-m-d', strtotime('+365 days', strtotime($qrDate)));

        $dirPath = public_path('images/qrcodes');
        if (!file_exists($dirPath)) {
            @mkdir($dirPath, 0777, true);
        }

        $createdVouchers = [];

        for ($i = 1; $i <= $count; $i++) {
            $randomSuffix = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));
            $prefix = "ARM-" . intval($amount);
            $voucherCode = ($count === 1 && !empty($validated['voucher_code']))
                ? strtoupper(trim($validated['voucher_code']))
                : "{$prefix}-{$randomSuffix}";

            // Ensure uniqueness
            while (QrVoucher::where('voucher_code', $voucherCode)->exists()) {
                $randomSuffix = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
                $voucherCode = "{$prefix}-{$randomSuffix}";
            }

            $claimUrl = url('/claim/' . $voucherCode);

            // Generate crisp vector SVG for the QR code
            try {
                $svg = (string)QrCode::size(120)->margin(0)->generate($claimUrl);
                @file_put_contents($dirPath . "/qr_{$voucherCode}.svg", $svg);
            } catch (\Throwable $e) {
                // If offline or GD fallback
            }

            $voucher = QrVoucher::create([
                'voucher_code' => $voucherCode,
                'batch_name' => $batchName,
                'qr_date' => $qrDate,
                'amount' => $amount,
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'title' => $batchName,
                'discount_type' => $discountType,
                'discount_percent' => $amount,
                'discount_amount' => $amount,
                'max_discount_cap' => $maxCap,
                'min_order_value' => $minOrder,
                'valid_from' => $qrDate,
                'valid_until' => $validUntil,
                'status' => 'Active',
                'is_redeemed' => false,
                'qr_payload' => $claimUrl
            ]);

            $createdVouchers[] = $voucher;
        }

        // Return JSON if requested
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Successfully generated {$count} QR Voucher(s) for batch '{$batchName}' with ₹{$amount} value.",
                'voucher' => $createdVouchers[0] ?? null,
                'vouchers' => $createdVouchers,
                'count' => $count,
                'pdf_url' => route('qr.exportPdf', ['batch' => $batchName, 'count' => $count])
            ], 201);
        }

        // Color Master behavior: If requested or user checked download_pdf
        if ($request->boolean('download_pdf', true)) {
            $cols = (int)$request->input('cols', 10);
            return $this->generatePdfFromVouchers($createdVouchers, $batchName, $cols);
        }

        return redirect()->route('qr.history')->with('success', "Batch '{$batchName}' created ({$count} QRs of ₹{$amount} generated successfully).");
    }

    /**
     * Color Master Batch Store & Direct A4 PDF Download
     */
    public function storeBatch(Request $request)
    {
        $request->merge(['download_pdf' => true]);
        return $this->store($request);
    }

    /**
     * Export A4 Sheet PDF (Color Master Module equivalent)
     */
    public function exportPdf(Request $request)
    {
        $countInput = $request->input('count', 50);
        $batch = $request->input('batch_name') ?: $request->input('batch');
        $status = $request->input('status', 'Active');
        $ids = $request->input('ids');
        $cols = (int)$request->input('cols', 10);
        if ($cols < 4 || $cols > 15) {
            $cols = 10;
        }

        $query = QrVoucher::query();

        if (!empty($ids)) {
            $idList = is_array($ids) ? $ids : explode(',', $ids);
            $query->whereIn('id', array_filter(array_map('intval', $idList)));
        } else {
            if ($status && $status !== 'all') {
                $query->where('status', $status);
            }
            if ($batch) {
                $query->where('batch_name', $batch);
            }
            if ($countInput !== 'all') {
                $count = max(1, min(2000, (int)$countInput));
                $query->take($count);
            }
        }

        $vouchers = $query->latest()->get();

        if ($vouchers->isEmpty()) {
            return redirect()->route('qr.history')->with('error', 'No QR vouchers found matching criteria to export.');
        }

        $batchName = $batch ?: ($vouchers->first()->batch_name ?? 'Aarambh Vouchers');

        if ($request->boolean('preview')) {
            return $this->previewPdfFromVouchers($vouchers, $batchName, $cols);
        }

        return $this->generatePdfFromVouchers($vouchers, $batchName, $cols);
    }

    /**
     * HTML Preview of A4 Sheet
     */
    public function previewPdf(Request $request)
    {
        $request->merge(['preview' => true]);
        return $this->exportPdf($request);
    }

    /**
     * Helper to render and download DomPDF for vouchers
     */
    private function generatePdfFromVouchers($vouchers, $batchName = 'Aarambh Vouchers', $cols = 10)
    {
        $dirPath = public_path('images/qrcodes');
        if (!file_exists($dirPath)) {
            @mkdir($dirPath, 0777, true);
        }

        $qrs = [];
        foreach ($vouchers as $v) {
            $code = $v->voucher_code;
            $claimUrl = url('/claim/' . $code);
            $svgPath = $dirPath . "/qr_{$code}.svg";

            if (file_exists($svgPath)) {
                $svg = file_get_contents($svgPath);
            } else {
                $svg = (string)QrCode::size(120)->margin(0)->generate($claimUrl);
                @file_put_contents($svgPath, $svg);
            }

            $qrs[] = [
                'id' => $v->id,
                'voucher_code' => $code,
                'batch_name' => $v->batch_name ?: $v->title ?: 'AARAMBH',
                'amount' => $v->amount ?: $v->discount_amount ?: $v->discount_percent ?: 500,
                'qr_base64' => base64_encode($svg),
                'claim_url' => $claimUrl,
            ];
        }

        $generatedAt = now()->format('d M Y, h:i A');
        $pdf = Pdf::loadView('qr.pdf', compact('qrs', 'cols', 'batchName', 'generatedAt'));
        $pdf->setPaper('a4', 'portrait');

        $filename = 'qr_records_' . count($qrs) . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Helper to preview HTML in browser
     */
    private function previewPdfFromVouchers($vouchers, $batchName = 'Aarambh Vouchers', $cols = 10)
    {
        $dirPath = public_path('images/qrcodes');
        if (!file_exists($dirPath)) {
            @mkdir($dirPath, 0777, true);
        }

        $qrs = [];
        foreach ($vouchers as $v) {
            $code = $v->voucher_code;
            $claimUrl = url('/claim/' . $code);
            $svgPath = $dirPath . "/qr_{$code}.svg";

            if (file_exists($svgPath)) {
                $svg = file_get_contents($svgPath);
            } else {
                $svg = (string)QrCode::size(120)->margin(0)->generate($claimUrl);
                @file_put_contents($svgPath, $svg);
            }

            $qrs[] = [
                'id' => $v->id,
                'voucher_code' => $code,
                'batch_name' => $v->batch_name ?: $v->title ?: 'AARAMBH',
                'amount' => $v->amount ?: $v->discount_amount ?: $v->discount_percent ?: 500,
                'qr_base64' => base64_encode($svg),
                'claim_url' => $claimUrl,
            ];
        }

        $generatedAt = now()->format('d M Y, h:i A');
        return view('qr.pdf', compact('qrs', 'cols', 'batchName', 'generatedAt'));
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
            'order_bill' => 'nullable|numeric|min:0',
            'recipient_upi_id' => 'nullable|string|max:100',
            'recipient_name' => 'nullable|string|max:100',
            'payment_status' => 'nullable|string|max:50',
            'payment_method' => 'nullable|string|max:50',
            'upi_txn_ref' => 'nullable|string|max:100',
            'recipient_qr_image' => 'nullable|string'
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

        $discountValue = (float)($voucher->amount ?: $voucher->discount_amount ?: $voucher->discount_percent ?: 0);
        $bill = (float)($validated['order_bill'] ?? $discountValue);

        // Handle Percentage discount calculation
        if ($voucher->discount_type === 'Percentage') {
            $pct = (float)($voucher->discount_percent ?: 10);
            $calcDisc = ($bill * $pct) / 100;
            if ($voucher->max_discount_cap && $calcDisc > (float)$voucher->max_discount_cap) {
                $calcDisc = (float)$voucher->max_discount_cap;
            }
            $discountValue = $calcDisc;
        }

        if ($discountValue > $bill && $bill > 0) {
            $discountValue = $bill;
        }

        $finalPayable = max(0, $bill - $discountValue);

        // Apply single-use redemption and permanently mark redeemed with full payment tracking
        $claimId = 'CLM-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        $voucher->is_redeemed = true;
        $voucher->status = 'Redeemed';
        $voucher->redeemed_at = now();
        $voucher->customer_phone = $phone;
        $voucher->redeemed_invoice_no = $claimId;

        $voucher->recipient_upi_id = !empty($validated['recipient_upi_id']) ? trim($validated['recipient_upi_id']) : null;
        $voucher->recipient_name = !empty($validated['recipient_name']) ? trim($validated['recipient_name']) : null;
        $voucher->original_bill = $bill;
        $voucher->discount_claimed = $discountValue;
        $voucher->final_payable = $finalPayable;
        $voucher->payment_status = !empty($validated['payment_status']) ? $validated['payment_status'] : 'Completed';
        $voucher->payment_method = !empty($validated['payment_method']) ? $validated['payment_method'] : 'UPI';
        $voucher->upi_txn_ref = !empty($validated['upi_txn_ref']) ? trim($validated['upi_txn_ref']) : null;
        if (!empty($validated['recipient_qr_image'])) {
            $voucher->recipient_qr_image = $validated['recipient_qr_image'];
        }

        $voucher->save();

        $redeemedAtFormatted = $voucher->redeemed_at instanceof \DateTimeInterface ? $voucher->redeemed_at->format('d M Y, h:i A') : (string)$voucher->redeemed_at;

        return response()->json([
            'success' => true,
            'claim_id' => $claimId,
            'voucher' => $voucher,
            'phone' => $phone,
            'discount_value' => $discountValue,
            'original_bill' => $bill,
            'final_payable' => $finalPayable,
            'recipient_upi_id' => $voucher->recipient_upi_id,
            'recipient_name' => $voucher->recipient_name,
            'payment_status' => $voucher->payment_status,
            'payment_method' => $voucher->payment_method,
            'upi_txn_ref' => $voucher->upi_txn_ref,
            'redeemed_at' => $redeemedAtFormatted,
            'message' => "Payment of ₹" . number_format($finalPayable, 2) . " confirmed and recorded! Voucher {$voucher->voucher_code} (Discount: ₹" . number_format($discountValue, 2) . ") successfully redeemed."
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
