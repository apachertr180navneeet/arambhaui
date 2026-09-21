@extends('layouts.public')

@section('title', 'UPI & QR Payment Desk - ' . ($companyName ?? 'GarmentERP'))

@section('content')
<div style="max-width:1040px; margin:0 auto; display:flex; flex-direction:column; gap:24px; padding:10px 0 40px;">

  <!-- Portal Header -->
  <div style="text-align:center;">
    <div style="display:inline-flex; align-items:center; justify-content:center; width:52px; height:52px; border-radius:14px; background:linear-gradient(135deg, #4f46e5, #7c3aed); color:#fff; margin-bottom:12px; box-shadow:0 8px 16px -4px rgba(79, 70, 229, 0.3);">
      <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/></svg>
    </div>
    <h2 style="margin:0; font-size:1.65rem; font-weight:800; color:var(--slate-900);">
      {{ $companyName ?? 'GarmentERP' }} - UPI Payment & Voucher Desk
    </h2>
    <p style="margin:6px auto 0; max-width:650px; color:var(--slate-500); font-size:0.92rem;">
      Unified payment terminal: Verify customer discount vouchers, calculate net bill, and receive instant payments directly into your merchant UPI account.
    </p>
  </div>

  <!-- SECTION 1: MERCHANT RECIPIENT PROFILE (Where money is received) -->
  <div class="card" style="background:#ffffff; border-radius:16px; border:1px solid #e0e7ff; box-shadow:0 4px 12px -2px rgba(79, 70, 229, 0.08); padding:22px; position:relative; overflow:hidden;">
    <div style="position:absolute; top:0; left:0; width:5px; height:100%; background:linear-gradient(180deg, #4f46e5, #7c3aed);"></div>
    
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
      <div style="display:flex; align-items:center; gap:10px;">
        <span style="display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:10px; background:#e0e7ff; color:#4338ca;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </span>
        <div>
          <h3 style="margin:0; font-size:1.05rem; font-weight:800; color:#1e1b4b;">1. Recipient Merchant Profile (Payment Destination)</h3>
          <span style="font-size:0.78rem; color:#6366f1; font-weight:600;">Funds will be received into this account</span>
        </div>
      </div>

      <div style="display:flex; align-items:center; gap:8px;">
        <button type="button" class="btn btn-secondary btn-sm" onclick="triggerMerchantQrInput()" style="display:inline-flex; align-items:center; gap:6px; font-weight:600; font-size:0.8rem; border-color:#c7d2fe; color:#4338ca; background:#eef2ff;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
          Upload / Scan Store QR
        </button>
        <button type="button" class="btn btn-primary btn-sm" onclick="saveRecipientAsDefault()" style="display:inline-flex; align-items:center; gap:6px; font-weight:600; font-size:0.8rem;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
          Save as Default
        </button>
      </div>
    </div>

    <!-- Hidden file input for Merchant QR upload -->
    <input type="file" id="merchant_qr_input" accept="image/*" style="display:none;" onchange="handleMerchantQrUpload(event)">

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:16px; align-items:end;">
      
      <!-- Recipient UPI ID -->
      <div class="form-group" style="margin:0;">
        <label class="form-label" style="font-weight:700; font-size:0.85rem; color:#334155;">
          Recipient UPI ID (Merchant VPA) <span style="color:red;">*</span>
        </label>
        <div style="position:relative;">
          <input type="text" id="recipient_upi_id" class="form-control" list="common_merchant_upis" placeholder="e.g. shreeshyam@icici or store@upi" value="{{ $defaultRecipientUpi ?? '' }}" style="font-weight:700; font-family:monospace; padding-left:38px; color:#1e1b4b; font-size:0.95rem;" oninput="updatePaymentPreview()">
          <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#6366f1;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><line x1="12" y1="6" x2="12" y2="8"/><line x1="12" y1="16" x2="12" y2="18"/></svg>
          </span>
        </div>
        <datalist id="common_merchant_upis">
          @if(!empty($defaultRecipientUpi))
            <option value="{{ $defaultRecipientUpi }}">{{ $defaultRecipientName ?? 'Default Store Account' }}</option>
          @endif
        </datalist>
      </div>

      <!-- Recipient Business / Payee Name -->
      <div class="form-group" style="margin:0;">
        <label class="form-label" style="font-weight:700; font-size:0.85rem; color:#334155;">
          Recipient Business Name (Payee) <span style="color:red;">*</span>
        </label>
        <input type="text" id="recipient_name" class="form-control" placeholder="e.g. Shree Shyam Welfare / Aarambh Garments" value="{{ $defaultRecipientName ?? ($companyName ?? 'Aarambh Garments') }}" style="font-weight:700; color:#334155;" oninput="updatePaymentPreview()">
      </div>

      <!-- Live Active Payee Badge -->
      <div style="background:#f8fafc; border:1px solid #cbd5e1; border-radius:10px; padding:8px 12px; display:flex; align-items:center; gap:10px;">
        <div id="merchant-qr-thumb-box" style="width:38px; height:38px; border-radius:8px; background:#e2e8f0; display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0;">
          @if(!empty($defaultRecipientQr))
            <img src="{{ $defaultRecipientQr }}" alt="Merchant QR" style="width:100%; height:100%; object-fit:cover;">
          @else
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/></svg>
          @endif
        </div>
        <div style="overflow:hidden; text-overflow:ellipsis;">
          <div style="font-size:0.72rem; text-transform:uppercase; font-weight:800; color:#64748b; letter-spacing:0.04em;">Active Destination</div>
          <div id="badge-merchant-display" style="font-weight:800; font-size:0.85rem; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
            {{ $defaultRecipientUpi ?: 'No UPI ID Set' }}
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- SECTION 2: CUSTOMER (PAYER) & VOUCHER VALIDATION -->
  <div class="card" style="background:#fff; border-radius:16px; border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:26px;">
    
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(min(100%, 340px), 1fr)); gap:24px; align-items:start;">
      
      <!-- Left Column: Customer Payer Details Form -->
      <div>
        <h3 style="font-size:1.05rem; font-weight:800; margin-top:0; margin-bottom:16px; color:var(--slate-800); display:flex; align-items:center; gap:8px;">
          <span style="display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:8px; background:#eff6ff; color:#2563eb; font-size:0.85rem;">2</span>
          <span>Customer & Order Verification</span>
        </h3>

        <form id="claim-verification-form" onsubmit="handleVerificationSubmit(event)">
          <!-- Customer Phone Number (Payer) -->
          <div class="form-group" style="margin-bottom:14px;">
            <label class="form-label" style="font-weight:700;">Customer Mobile / Phone (Payer) <span style="color:red;">*</span></label>
            <div style="position:relative;">
              <input type="tel" id="claim_phone" class="form-control" required placeholder="e.g. 9876543210" style="padding-left:42px; font-weight:700; font-size:0.95rem;">
              <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); font-weight:700; color:var(--slate-400); font-size:0.85rem;">+91</span>
            </div>
            <small style="color:var(--slate-500); font-size:0.75rem;">Single-use discount will be claimed for this customer.</small>
          </div>

          <!-- Voucher Unique Code -->
          <div class="form-group" style="margin-bottom:14px;">
            <label class="form-label" style="font-weight:700;">Voucher Unique Code <span style="color:red;">*</span></label>
            <input type="text" id="claim_code" class="form-control" required placeholder="e.g. ARM-500-X7K" value="{{ $code }}" style="font-family:monospace; text-transform:uppercase; font-size:1rem; font-weight:800; color:#4f46e5; letter-spacing:1px;" oninput="onCodeInput()">
          </div>

          <!-- Purchase Bill Amount -->
          <div class="form-group" style="margin-bottom:20px;">
            <label class="form-label" style="font-weight:700;">Order Bill Amount (₹) <span style="color:red;">*</span></label>
            <input type="number" id="claim_bill" class="form-control" placeholder="e.g. 1500.00" step="0.01" style="font-weight:800; font-size:1.1rem; color:var(--slate-900);" oninput="calculateRedemptionPreview()">
            <small id="min-bill-hint" style="color:var(--slate-500); font-size:0.75rem; display:block; margin-top:4px;">Enter gross bill to calculate discount and net payable amount.</small>
          </div>

          <button type="submit" id="verify-voucher-btn" class="btn btn-primary" style="width:100%; padding:12px; font-weight:700; font-size:0.95rem; display:inline-flex; align-items:center; justify-content:center; gap:8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            Verify Voucher & Proceed to Payment
          </button>
        </form>
      </div>

      <!-- Right Column: Customer Voucher Scanner / Upload -->
      <div>
        <h3 style="font-size:1.05rem; font-weight:800; margin-top:0; margin-bottom:16px; color:var(--slate-800); display:flex; align-items:center; gap:8px;">
          <span style="display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:8px; background:#eff6ff; color:#2563eb; font-size:0.85rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/></svg>
          </span>
          <span>Scan / Upload Customer Voucher QR</span>
        </h3>

        <!-- Drag & Drop / File Upload Box -->
        <div id="drop-zone" style="border:2px dashed #cbd5e1; border-radius:14px; padding:24px 16px; text-align:center; background:#f8fafc; cursor:pointer; transition:all 0.2s;" onclick="document.getElementById('customer-qr-file-input').click()">
          <input type="file" id="customer-qr-file-input" accept="image/*" style="display:none;" onchange="handleCustomerQrUpload(event)">
          
          <div style="width:48px; height:48px; border-radius:12px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; margin:0 auto 10px;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
          </div>
          <div style="font-weight:700; color:var(--slate-800); font-size:0.925rem;">Upload Customer QR Voucher</div>
          <div style="font-size:0.8rem; color:var(--slate-500); margin-top:4px;">Drag & drop image or browse to auto-fill code</div>
        </div>

        <!-- Camera Scanner Launcher -->
        <div style="margin-top:14px; text-align:center;">
          <button type="button" class="btn btn-secondary btn-sm" onclick="startCameraScanner()" style="width:100%; display:inline-flex; align-items:center; justify-content:center; gap:6px; font-weight:700;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
            Launch Camera Scanner
          </button>
        </div>

        <!-- Live Camera Video Element -->
        <div id="camera-container" style="display:none; margin-top:14px; position:relative; border-radius:12px; overflow:hidden; border:2px solid #4f46e5;">
          <video id="camera-preview" style="width:100%; height:200px; object-fit:cover; background:#000;"></video>
          <canvas id="qr-canvas" style="display:none;"></canvas>
          <button type="button" onclick="stopCameraScanner()" class="btn btn-danger btn-xs" style="position:absolute; top:8px; right:8px;">Close Camera</button>
        </div>

      </div>

    </div>

    <!-- Live Calculation & Voucher Status Banner -->
    <div id="redemption-result-card" style="display:none; margin-top:22px; border-radius:14px; padding:18px; transition:all 0.3s;">
      <!-- Populated via JavaScript -->
    </div>

  </div>

  <!-- SECTION 3: INTERACTIVE PAYMENT STAGE (Dynamically shown on valid voucher & bill) -->
  <div id="payment-stage-container" style="display:none;" class="card" style="background:#ffffff; border-radius:16px; border:2px solid #6366f1; box-shadow:0 10px 25px -5px rgba(79, 70, 229, 0.15); padding:28px;">
    
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px; border-bottom:1px solid #e2e8f0; padding-bottom:14px;">
      <div style="display:flex; align-items:center; gap:10px;">
        <span style="display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:10px; background:#ecfdf5; color:#059669; font-weight:800;">3</span>
        <div>
          <h3 style="margin:0; font-size:1.2rem; font-weight:800; color:#064e3b;">Payment Stage & Confirmation</h3>
          <span style="font-size:0.8rem; color:#059669; font-weight:600;">Scan & pay or record transaction to finalize discount</span>
        </div>
      </div>
      <span class="badge" style="background:#dcfce7; color:#15803d; font-weight:700; padding:6px 12px; font-size:0.85rem; border-radius:20px;">
        ● Ready for Payment
      </span>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(310px, 1fr)); gap:24px; align-items:start;">
      
      <!-- Left Column: Bill & Stakeholder Breakdown -->
      <div style="display:flex; flex-direction:column; gap:14px;">
        
        <!-- Payer & Receiver Clarity Card -->
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
          <!-- Customer Payer -->
          <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:12px;">
            <div style="font-size:0.72rem; text-transform:uppercase; font-weight:800; color:#64748b; margin-bottom:4px;">Customer (Payer)</div>
            <div id="stage-customer-phone" style="font-weight:800; color:#0f172a; font-size:0.92rem;">+91 ----------</div>
            <div id="stage-voucher-badge" style="font-size:0.75rem; color:#4f46e5; font-weight:700; margin-top:2px;">Voucher: ---</div>
          </div>
          <!-- Merchant Receiver -->
          <div style="background:#eef2ff; border:1px solid #c7d2fe; border-radius:12px; padding:12px;">
            <div style="font-size:0.72rem; text-transform:uppercase; font-weight:800; color:#4338ca; margin-bottom:4px;">Merchant (Payee)</div>
            <div id="stage-merchant-name" style="font-weight:800; color:#1e1b4b; font-size:0.92rem;">Store Name</div>
            <div id="stage-merchant-upi" style="font-size:0.75rem; color:#4f46e5; font-weight:700; margin-top:2px; font-family:monospace;">upi@id</div>
          </div>
        </div>

        <!-- Financial Summary Table -->
        <div style="background:#fafafa; border:1px solid #e5e7eb; border-radius:12px; padding:16px;">
          <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:0.9rem;">
            <span style="color:#6b7280;">Gross Order Bill:</span>
            <strong id="stage-gross-bill" style="color:#111827;">₹0.00</strong>
          </div>
          <div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:0.9rem; color:#059669;">
            <span>Voucher Discount Saved:</span>
            <strong id="stage-discount-saved">-₹0.00</strong>
          </div>
          <div style="border-top:1px dashed #d1d5db; padding-top:10px; display:flex; justify-content:space-between; align-items:baseline;">
            <span style="font-weight:800; color:#111827; font-size:1.05rem;">Net Amount Payable:</span>
            <strong id="stage-net-payable" style="font-size:1.5rem; font-weight:900; color:#4338ca;">₹0.00</strong>
          </div>
        </div>

        <!-- Payment Mode & Reference Form -->
        <div style="display:flex; flex-direction:column; gap:12px;">
          <div>
            <label class="form-label" style="font-weight:700; font-size:0.85rem;">Payment Method</label>
            <select id="stage-payment-method" class="form-control" style="font-weight:700;" onchange="onPaymentMethodChange()">
              <option value="UPI" selected>UPI Payment (Google Pay / PhonePe / Paytm / BHIM)</option>
              <option value="Cash">Cash at Counter</option>
              <option value="Card">Credit / Debit Card (POS)</option>
            </select>
          </div>

          <div id="stage-utr-group">
            <label class="form-label" style="font-weight:700; font-size:0.85rem;">UPI Transaction ID / UTR Ref</label>
            <input type="text" id="stage-utr-input" class="form-control" placeholder="e.g. 423987123456 (Optional)" style="font-family:monospace; font-weight:700;">
            <small style="color:#64748b; font-size:0.75rem;">Enter bank reference number or UTR for audit tracking.</small>
          </div>

          <button type="button" id="confirm-payment-btn" class="btn btn-success" onclick="executeFinalPaymentAndRedeem()" style="padding:14px; font-weight:800; font-size:1.05rem; display:inline-flex; align-items:center; justify-content:center; gap:8px; margin-top:6px; box-shadow:0 4px 12px -2px rgba(16, 185, 129, 0.4);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            Confirm Payment Received & Complete Claim
          </button>
        </div>

      </div>

      <!-- Right Column: Dynamic Dynamic UPI QR Code for Customer to Scan -->
      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px; padding:22px; text-align:center; display:flex; flex-direction:column; align-items:center;">
        
        <div style="font-size:0.8rem; font-weight:800; color:#475569; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:10px;">
          Scan & Pay with Any UPI App
        </div>

        <!-- Rendered QR Box -->
        <div style="background:#ffffff; padding:16px; border-radius:14px; border:2px solid #e2e8f0; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); display:inline-block; margin-bottom:14px;">
          <div id="dynamic-upi-qrcode" style="width:190px; height:190px; display:flex; align-items:center; justify-content:center;">
            <!-- Rendered by QRCode.js -->
          </div>
        </div>

        <div style="font-size:0.82rem; color:#475569; margin-bottom:12px;">
          Scan using <strong>GPay, PhonePe, Paytm, or BHIM</strong> to pay exact amount directly to merchant account.
        </div>

        <!-- Mobile Action Deep Link Button -->
        <div style="display:flex; flex-direction:column; gap:8px; width:100%;">
          <a id="stage-upi-deeplink" href="#" class="btn btn-primary btn-sm" style="display:inline-flex; align-items:center; justify-content:center; gap:6px; font-weight:700;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
            Launch UPI App on Phone
          </a>
          <button type="button" class="btn btn-secondary btn-sm" onclick="copyUpiIdToClipboard()" style="display:inline-flex; align-items:center; justify-content:center; gap:6px; font-weight:600;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
            Copy Merchant UPI ID
          </button>
        </div>

      </div>

    </div>

  </div>

  <!-- SECTION 4: REDEEMED RECEIPT CONFIRMATION (Shown after payment) -->
  <div id="receipt-modal-container" style="display:none;" class="card" style="background:#ffffff; border-radius:16px; border:2px solid #059669; box-shadow:0 12px 30px -4px rgba(5, 150, 105, 0.2); padding:30px; text-align:center;">
    <!-- Filled dynamically upon successful redemption -->
  </div>

</div>

@push('scripts')
<script>
  let validatedVoucher = null;
  let activeFinalPayable = 0;
  let activeGrossBill = 0;
  let activeDiscount = 0;
  let videoStream = null;
  let animationFrameId = null;
  let qrCodeInstance = null;

  // 1. Recipient Merchant QR Upload & Decoding
  function triggerMerchantQrInput() {
    document.getElementById('merchant_qr_input').click();
  }

  function handleMerchantQrUpload(e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function() {
      const img = new Image();
      img.onload = function() {
        const canvas = document.createElement('canvas');
        canvas.width = img.width;
        canvas.height = img.height;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0);
        const imgData = ctx.getImageData(0, 0, img.width, img.height);

        let parsedUpi = '';
        let parsedName = '';

        if (typeof jsQR !== 'undefined') {
          const qr = jsQR(imgData.data, imgData.width, imgData.height);
          if (qr && qr.data) {
            const dataStr = qr.data.trim();
            // Check for upi://pay URI
            if (dataStr.startsWith('upi://') || dataStr.includes('pa=')) {
              try {
                const url = new URL(dataStr.startsWith('upi://') ? dataStr.replace('upi://pay', 'http://upi') : dataStr);
                parsedUpi = url.searchParams.get('pa') || '';
                parsedName = url.searchParams.get('pn') || '';
              } catch(err) {
                const paMatch = dataStr.match(/[?&]pa=([^&#\s]+)/i);
                if (paMatch) parsedUpi = decodeURIComponent(paMatch[1]);
                const pnMatch = dataStr.match(/[?&]pn=([^&#\s]+)/i);
                if (pnMatch) parsedName = decodeURIComponent(pnMatch[1]);
              }
            } else if (dataStr.includes('@')) {
              parsedUpi = dataStr;
            }
          }
        }

        // Upload to server
        const formData = new FormData();
        formData.append('qr_image', file);
        if (parsedUpi) formData.append('recipient_upi_id', parsedUpi);
        if (parsedName) formData.append('recipient_name', parsedName);
        formData.append('save_as_default', '1');

        fetch('{{ route('qr.uploadRecipient') }}', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
          },
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            if (parsedUpi) {
              document.getElementById('recipient_upi_id').value = parsedUpi;
            }
            if (parsedName) {
              document.getElementById('recipient_name').value = parsedName;
            }

            // Update badge thumbnail
            const thumbBox = document.getElementById('merchant-qr-thumb-box');
            if (thumbBox && data.image_url) {
              thumbBox.innerHTML = `<img src="${data.image_url}" alt="Store QR" style="width:100%; height:100%; object-fit:cover;">`;
            }

            updatePaymentPreview();

            if (typeof Swal !== 'undefined') {
              Swal.fire({
                icon: 'success',
                title: 'Merchant QR Scanner Saved',
                text: parsedUpi ? `Recipient VPA set to ${parsedUpi}` : 'QR image uploaded successfully',
                timer: 2500,
                showConfirmButton: false
              });
            } else {
              alert('Merchant QR uploaded successfully!');
            }
          }
        })
        .catch(err => {
          console.error(err);
          alert('Failed to upload merchant QR: ' + err.message);
        });

      };
      img.src = reader.result;
    };
    reader.readAsDataURL(file);
  }

  // 2. Save Recipient UPI as Default
  async function saveRecipientAsDefault() {
    const upi = document.getElementById('recipient_upi_id').value.trim();
    const name = document.getElementById('recipient_name').value.trim();

    if (!upi) {
      alert('Please enter a recipient UPI ID first.');
      return;
    }

    try {
      const res = await fetch('{{ route('qr.saveRecipientSettings') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          recipient_upi_id: upi,
          recipient_name: name
        })
      });
      const data = await res.json();
      if (data.success) {
        updatePaymentPreview();
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'success',
            title: 'Default Saved',
            text: `Recipient ${upi} is now configured as default for all payments.`,
            timer: 2000,
            showConfirmButton: false
          });
        } else {
          alert('Default recipient details saved!');
        }
      }
    } catch(err) {
      console.error(err);
    }
  }

  // 3. Extract & Clean Customer Voucher Codes
  function extractVoucherCode(input) {
    if (!input) return '';
    input = input.toString().trim();

    try {
      const parsed = JSON.parse(input);
      if (parsed && typeof parsed === 'object' && parsed.code) {
        return parsed.code.toString().toUpperCase().trim();
      }
    } catch(ex) {}

    const queryMatch = input.match(/[?&](?:code|voucher|c|v)=([^&#\s]+)/i);
    if (queryMatch && queryMatch[1]) {
      return decodeURIComponent(queryMatch[1]).toUpperCase().trim();
    }

    const pathMatch = input.match(/(?:\/claim\/|\/voucher\/|\/qr\/scanner\/)([^\/?&#\s]+)/i);
    if (pathMatch && pathMatch[1]) {
      const seg = decodeURIComponent(pathMatch[1]).toUpperCase().trim();
      if (!['SCANNER', 'CLAIM', 'QR', 'PUBLIC', 'GENERATOR'].includes(seg)) {
        return seg;
      }
    }

    if (/^https?:\/\//i.test(input) || input.includes('://')) {
      try {
        const url = new URL(input, window.location.origin);
        for (const [key, val] of url.searchParams.entries()) {
          if (['code', 'voucher', 'c', 'v'].includes(key.toLowerCase()) && val) {
            return decodeURIComponent(val).toUpperCase().trim();
          }
        }
        const segments = url.pathname.split('/').filter(Boolean);
        const last = segments[segments.length - 1];
        if (last && !['scanner', 'claim', 'qr', 'public', 'generator', 'garment'].includes(last.toLowerCase())) {
          return decodeURIComponent(last).toUpperCase().trim();
        }
      } catch(ex) {}
    }

    return input.replace(/^["'\s]+|["'\s/]+$/g, '').toUpperCase().trim();
  }

  function onCodeInput() {
    const inputElem = document.getElementById('claim_code');
    const raw = inputElem.value;
    const clean = extractVoucherCode(raw);
    
    if (clean && clean !== raw && (raw.includes('/') || raw.includes('?') || raw.includes('{'))) {
      inputElem.value = clean;
    }
    
    const activeCode = clean || raw.toUpperCase().trim();
    if (activeCode && activeCode.length >= 3) {
      checkVoucherStatus(activeCode);
    }
  }

  // 4. Validate Voucher Status with Backend
  async function checkVoucherStatus(rawCode) {
    const code = extractVoucherCode(rawCode);
    if (!code) return;
    
    const inputElem = document.getElementById('claim_code');
    if (inputElem.value !== code && (inputElem.value.includes('/') || inputElem.value.includes('?'))) {
      inputElem.value = code;
    }

    try {
      const res = await fetch('{{ route('qr.validate') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        },
        body: JSON.stringify({ voucher_code: code })
      });

      const data = await res.json();
      if (data.success) {
        validatedVoucher = data.voucher;

        const billInput = document.getElementById('claim_bill');
        const minOrder = parseFloat(data.voucher.min_order_value || data.min_bill || 0);
        const discAmount = parseFloat(data.voucher.discount_amount || 0);
        const discPercent = parseFloat(data.voucher.discount_percent || 0);

        if (!billInput.value || parseFloat(billInput.value) === 0) {
          if (minOrder > 0) {
            billInput.value = minOrder.toFixed(2);
          } else if (discAmount > 0) {
            billInput.value = (discAmount * 2).toFixed(2);
          } else if (discPercent > 0) {
            billInput.value = "1000.00";
          }
        }

        const hint = document.getElementById('min-bill-hint');
        if (hint) {
          if (minOrder > 0) {
            hint.innerHTML = `Minimum qualifying order amount: <strong>₹${minOrder.toFixed(2)}</strong>`;
          } else {
            hint.innerHTML = `No minimum spend required for this voucher.`;
          }
        }

        const phoneInput = document.getElementById('claim_phone');
        if (data.voucher.customer_phone && (!phoneInput.value || phoneInput.value.trim() === '')) {
          phoneInput.value = data.voucher.customer_phone;
        }

        calculateRedemptionPreview();
      } else {
        validatedVoucher = null;
        hidePaymentStage();
        showResultBanner(false, data.message || 'Voucher cannot be redeemed.');
      }
    } catch(err) {
      console.error(err);
    }
  }

  // 5. Calculate Discount and Render Payment Stage
  function calculateRedemptionPreview() {
    if (!validatedVoucher) return;
    const bill = parseFloat(document.getElementById('claim_bill').value) || 0;
    const minOrder = parseFloat(validatedVoucher.min_order_value || 0);

    if (minOrder > 0 && bill < minOrder) {
      hidePaymentStage();
      showResultBanner(false, `⚠️ Order amount ₹${bill.toFixed(2)} is below minimum required order value of ₹${minOrder.toFixed(2)} for this voucher.`);
      return;
    }

    let disc = 0;
    if (validatedVoucher.discount_type === 'Percentage') {
      disc = (bill * parseFloat(validatedVoucher.discount_percent)) / 100;
      if (validatedVoucher.max_discount_cap && disc > validatedVoucher.max_discount_cap) {
        disc = parseFloat(validatedVoucher.max_discount_cap);
      }
    } else {
      disc = parseFloat(validatedVoucher.discount_amount || validatedVoucher.discount_percent || 500);
      if (disc > bill) disc = bill;
    }

    const finalPay = Math.max(0, bill - disc);

    activeGrossBill = bill;
    activeDiscount = disc;
    activeFinalPayable = finalPay;

    showResultBanner(true, `
      <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:38px; height:38px; border-radius:50%; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div>
          <div style="font-weight:800; font-size:1.05rem; color:#065f46;">Valid Single-Use Voucher: ${validatedVoucher.voucher_code}</div>
          <div style="font-size:0.85rem; color:#047857;">Original Bill: ₹${bill.toFixed(2)} | Discount Applied: -₹${disc.toFixed(2)} | <strong>Net Payable: ₹${finalPay.toFixed(2)}</strong></div>
        </div>
      </div>
    `);

    renderPaymentStage();
  }

  function handleVerificationSubmit(e) {
    e.preventDefault();
    const phone = document.getElementById('claim_phone').value.trim();
    const rawCode = document.getElementById('claim_code').value.trim();
    const code = extractVoucherCode(rawCode);

    if (!phone || !code) {
      alert('Please provide both phone number and voucher code.');
      return;
    }

    checkVoucherStatus(code);
    renderPaymentStage();
    document.getElementById('payment-stage-container').scrollIntoView({ behavior: 'smooth' });
  }

  // 6. Interactive Payment Stage Renderer
  function renderPaymentStage() {
    const stage = document.getElementById('payment-stage-container');
    const phone = document.getElementById('claim_phone').value.trim() || '---';
    const recipientUpi = document.getElementById('recipient_upi_id').value.trim() || '---';
    const recipientName = document.getElementById('recipient_name').value.trim() || 'Aarambh Garments';

    // Populate stage fields
    document.getElementById('stage-customer-phone').innerText = '+91 ' + phone;
    document.getElementById('stage-voucher-badge').innerText = 'Voucher: ' + (validatedVoucher ? validatedVoucher.voucher_code : '---');
    document.getElementById('stage-merchant-name').innerText = recipientName;
    document.getElementById('stage-merchant-upi').innerText = recipientUpi;
    document.getElementById('stage-gross-bill').innerText = '₹' + activeGrossBill.toFixed(2);
    document.getElementById('stage-discount-saved').innerText = '-₹' + activeDiscount.toFixed(2);
    document.getElementById('stage-net-payable').innerText = '₹' + activeFinalPayable.toFixed(2);

    // Build standard UPI URI for Dynamic QR & Deep Link
    // format: upi://pay?pa=VPA&pn=NAME&am=AMOUNT&cu=INR&tn=NOTE
    const voucherCode = validatedVoucher ? validatedVoucher.voucher_code : 'VOUCHER';
    const txnNote = encodeURIComponent(`Bill payment ${voucherCode}`);
    const encodedName = encodeURIComponent(recipientName);
    const upiUri = `upi://pay?pa=${encodeURIComponent(recipientUpi)}&pn=${encodedName}&am=${activeFinalPayable.toFixed(2)}&cu=INR&tn=${txnNote}`;

    // Deep link for mobile button
    const deeplink = document.getElementById('stage-upi-deeplink');
    if (deeplink) {
      deeplink.href = upiUri;
    }

    // Render Dynamic QR code into #dynamic-upi-qrcode using QRCode.js
    const qrContainer = document.getElementById('dynamic-upi-qrcode');
    if (qrContainer) {
      qrContainer.innerHTML = '';
      if (typeof QRCode !== 'undefined') {
        qrCodeInstance = new QRCode(qrContainer, {
          text: upiUri,
          width: 190,
          height: 190,
          colorDark: "#0f172a",
          colorLight: "#ffffff",
          correctLevel: QRCode.CorrectLevel.M
        });
      } else {
        qrContainer.innerHTML = `<div style="font-size:0.75rem; color:#dc2626;">QRCode generator unavailable. Please pay to UPI ID directly.</div>`;
      }
    }

    stage.style.display = 'block';
  }

  function hidePaymentStage() {
    const stage = document.getElementById('payment-stage-container');
    if (stage) stage.style.display = 'none';
  }

  function updatePaymentPreview() {
    const upi = document.getElementById('recipient_upi_id').value.trim();
    const displayBadge = document.getElementById('badge-merchant-display');
    if (displayBadge) {
      displayBadge.innerText = upi || 'No UPI ID Set';
    }
    if (document.getElementById('payment-stage-container').style.display === 'block') {
      renderPaymentStage();
    }
  }

  function onPaymentMethodChange() {
    const method = document.getElementById('stage-payment-method').value;
    const utrGroup = document.getElementById('stage-utr-group');
    if (method === 'Cash') {
      utrGroup.style.display = 'none';
    } else {
      utrGroup.style.display = 'block';
    }
  }

  function copyUpiIdToClipboard() {
    const upi = document.getElementById('recipient_upi_id').value.trim();
    if (!upi) return;
    navigator.clipboard.writeText(upi).then(() => {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'success',
          title: 'Copied!',
          text: `UPI ID ${upi} copied to clipboard.`,
          timer: 1500,
          showConfirmButton: false
        });
      } else {
        alert('UPI ID copied to clipboard!');
      }
    });
  }

  // 7. Execute Final Payment and Redemption
  async function executeFinalPaymentAndRedeem() {
    const phone = document.getElementById('claim_phone').value.trim();
    const rawCode = document.getElementById('claim_code').value.trim();
    const code = extractVoucherCode(rawCode);
    const bill = activeGrossBill;
    const recipientUpi = document.getElementById('recipient_upi_id').value.trim();
    const recipientName = document.getElementById('recipient_name').value.trim();
    const paymentMethod = document.getElementById('stage-payment-method').value;
    const utrRef = document.getElementById('stage-utr-input').value.trim();

    if (!phone || !code) {
      alert('Missing customer phone or voucher code.');
      return;
    }

    const btn = document.getElementById('confirm-payment-btn');
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing & Confirming...`;

    try {
      const res = await fetch('{{ route('qr.redeem') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          voucher_code: code,
          customer_phone: phone,
          order_bill: bill,
          recipient_upi_id: recipientUpi,
          recipient_name: recipientName,
          payment_method: paymentMethod,
          payment_status: 'Completed',
          upi_txn_ref: utrRef
        })
      });

      const data = await res.json();
      btn.disabled = false;
      btn.innerText = 'Confirm Payment Received & Complete Claim';

      if (data.success) {
        hidePaymentStage();
        showReceiptModal(data);
      } else {
        alert(data.message || 'Payment processing failed.');
      }
    } catch(err) {
      btn.disabled = false;
      btn.innerText = 'Confirm Payment Received & Complete Claim';
      alert('Network error connecting to redemption server.');
    }
  }

  // 8. Render Receipt Modal
  function showReceiptModal(data) {
    const container = document.getElementById('receipt-modal-container');
    container.style.display = 'block';

    const voucherCode = data.voucher ? data.voucher.voucher_code : '';
    const claimId = data.claim_id || 'CLM-XXXX';
    const finalAmt = Number(data.final_payable || 0).toFixed(2);
    const discAmt = Number(data.discount_value || 0).toFixed(2);
    const grossAmt = Number(data.original_bill || 0).toFixed(2);
    const time = data.redeemed_at || new Date().toLocaleString();

    container.innerHTML = `
      <div style="max-width:550px; margin:0 auto; text-align:left;">
        <div style="text-align:center; margin-bottom:20px;">
          <div style="width:56px; height:56px; border-radius:50%; background:#dcfce7; color:#15803d; display:inline-flex; align-items:center; justify-content:center; margin-bottom:12px;">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          </div>
          <h3 style="margin:0; font-size:1.4rem; font-weight:800; color:#064e3b;">Payment & Redemption Successful!</h3>
          <p style="margin:4px 0 0; color:#059669; font-size:0.88rem;">Receipt #${claimId} generated successfully.</p>
        </div>

        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:18px; margin-bottom:20px;">
          <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:0.85rem;">
            <span style="color:#64748b;">Customer (Payer):</span>
            <strong style="color:#0f172a;">+91 ${data.phone}</strong>
          </div>
          <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:0.85rem;">
            <span style="color:#64748b;">Payee (Merchant):</span>
            <strong style="color:#0f172a;">${data.recipient_name || 'Merchant'} (${data.recipient_upi_id || 'UPI'})</strong>
          </div>
          <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:0.85rem;">
            <span style="color:#64748b;">Voucher Code Applied:</span>
            <strong style="color:#4f46e5; font-family:monospace;">${voucherCode}</strong>
          </div>
          <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:0.85rem;">
            <span style="color:#64748b;">Payment Method:</span>
            <strong style="color:#0f172a;">${data.payment_method || 'UPI'}${data.upi_txn_ref ? ' (Ref: ' + data.upi_txn_ref + ')' : ''}</strong>
          </div>
          <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:0.85rem;">
            <span style="color:#64748b;">Timestamp:</span>
            <span style="color:#334155; font-size:0.8rem;">${time}</span>
          </div>

          <div style="border-top:1px dashed #cbd5e1; margin-top:12px; padding-top:12px;">
            <div style="display:flex; justify-content:space-between; font-size:0.9rem; margin-bottom:4px;">
              <span>Gross Bill:</span>
              <span>₹${grossAmt}</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#059669; margin-bottom:6px;">
              <span>Voucher Discount:</span>
              <span>-₹${discAmt}</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:1.15rem; font-weight:800; color:#111827;">
              <span>Total Paid:</span>
              <span style="color:#059669;">₹${finalAmt}</span>
            </div>
          </div>
        </div>

        <div style="display:flex; gap:12px; justify-content:center;">
          <button type="button" class="btn btn-secondary" onclick="window.print()" style="display:inline-flex; align-items:center; gap:6px; font-weight:700;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Print Receipt
          </button>
          <button type="button" class="btn btn-primary" onclick="window.location.reload()" style="font-weight:700;">
            Process Next Customer
          </button>
        </div>
      </div>
    `;

    container.scrollIntoView({ behavior: 'smooth' });
  }

  function showResultBanner(isSuccess, htmlContent) {
    const card = document.getElementById('redemption-result-card');
    card.style.display = 'block';
    if (isSuccess) {
      card.style.background = '#ecfdf5';
      card.style.border = '1px solid #a7f3d0';
      card.style.color = '#065f46';
    } else {
      card.style.background = '#fef2f2';
      card.style.border = '1px solid #fecaca';
      card.style.color = '#991b1b';
    }
    card.innerHTML = typeof htmlContent === 'string' && htmlContent.startsWith('<') ? htmlContent : `<div style="font-weight:700;">${htmlContent}</div>`;
  }

  // 9. Customer QR Image Upload Handler
  function handleCustomerQrUpload(e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function() {
      const img = new Image();
      img.onload = function() {
        const canvas = document.createElement('canvas');
        canvas.width = img.width;
        canvas.height = img.height;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0);
        const imgData = ctx.getImageData(0, 0, img.width, img.height);

        if (typeof jsQR !== 'undefined') {
          const qrCode = jsQR(imgData.data, imgData.width, imgData.height);
          if (qrCode && qrCode.data) {
            let extractedCode = extractVoucherCode(qrCode.data);
            document.getElementById('claim_code').value = extractedCode;
            checkVoucherStatus(extractedCode);
            if (window.UI && UI.showToast) {
              UI.showToast('Voucher Decoded', extractedCode, 'success');
            }
          } else {
            alert('Could not decode a valid QR from this image. Please ensure the QR is clear and well-lit.');
          }
        }
      };
      img.src = reader.result;
    };
    reader.readAsDataURL(file);
  }

  // 10. Web Camera Live Scanner
  function startCameraScanner() {
    const container = document.getElementById('camera-container');
    const video = document.getElementById('camera-preview');
    container.style.display = 'block';

    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
      .then(stream => {
        videoStream = stream;
        video.srcObject = stream;
        video.setAttribute('playsinline', true);
        video.play();
        animationFrameId = requestAnimationFrame(scanCameraFrame);
      })
      .catch(err => {
        alert('Camera access denied or unavailable: ' + err.message);
        container.style.display = 'none';
      });
  }

  function stopCameraScanner() {
    if (videoStream) {
      videoStream.getTracks().forEach(t => t.stop());
      videoStream = null;
    }
    if (animationFrameId) cancelAnimationFrame(animationFrameId);
    document.getElementById('camera-container').style.display = 'none';
  }

  function scanCameraFrame() {
    const video = document.getElementById('camera-preview');
    const canvas = document.getElementById('qr-canvas');
    if (video.readyState === video.HAVE_ENOUGH_DATA) {
      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      const ctx = canvas.getContext('2d');
      ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
      const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);

      if (typeof jsQR !== 'undefined') {
        const qrCode = jsQR(imgData.data, imgData.width, imgData.height);
        if (qrCode && qrCode.data) {
          stopCameraScanner();
          let code = extractVoucherCode(qrCode.data);
          document.getElementById('claim_code').value = code;
          checkVoucherStatus(code);
          if (window.UI && UI.showToast) {
            UI.showToast('QR Code Scanned', code, 'success');
          }
          return;
        }
      }
    }
    animationFrameId = requestAnimationFrame(scanCameraFrame);
  }

  document.addEventListener('DOMContentLoaded', () => {
    const inputElem = document.getElementById('claim_code');
    const raw = inputElem.value;
    if (raw) {
      const code = extractVoucherCode(raw);
      inputElem.value = code;
      checkVoucherStatus(code);
    }

    inputElem.addEventListener('paste', () => {
      setTimeout(onCodeInput, 30);
    });
    inputElem.addEventListener('change', onCodeInput);
    updatePaymentPreview();
  });
</script>
@endpush
@endsection
