@extends('layouts.public')

@section('title', 'Claim Voucher & Transfer Amount - ' . ($companyName ?? 'GarmentERP'))

@section('content')
<div style="max-width:760px; margin:0 auto; padding:15px 12px 50px;">

  <!-- Main Card Container -->
  <div class="card" style="background:#ffffff; border-radius:20px; border:1px solid #e2e8f0; box-shadow:0 12px 36px -4px rgba(15, 23, 42, 0.08); overflow:hidden;">
    
    <!-- Top Header Ribbon -->
    <div style="background:linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding:24px 28px; color:#ffffff; position:relative; overflow:hidden;">
      <div style="position:absolute; top:-20px; right:-20px; width:130px; height:130px; background:radial-gradient(circle, rgba(99,102,241,0.25) 0%, rgba(99,102,241,0) 70%); border-radius:50%; pointer-events:none;"></div>
      
      <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <div style="display:flex; align-items:center; gap:14px;">
          <div style="width:48px; height:48px; border-radius:14px; background:linear-gradient(135deg, #2563eb, #4f46e5); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:1.3rem; box-shadow:0 6px 16px -2px rgba(37,99,235,0.4); flex-shrink:0;">
            G
          </div>
          <div>
            <h1 style="margin:0; font-size:1.35rem; font-weight:800; letter-spacing:-0.02em; color:#ffffff;">
              {{ $companyName ?? 'GarmentERP' }}
            </h1>
            <p style="margin:3px 0 0; font-size:0.85rem; color:#94a3b8;">
              Customer Reward & Voucher Claim Portal
            </p>
          </div>
        </div>

        <div style="background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.15); padding:6px 14px; border-radius:20px; font-size:0.75rem; font-weight:700; color:#e2e8f0; backdrop-filter:blur(4px); display:inline-flex; align-items:center; gap:6px;">
          <span style="width:8px; height:8px; border-radius:50%; background:#22c55e; display:inline-block; box-shadow:0 0 8px #22c55e;"></span>
          Instant Payout Active
        </div>
      </div>
    </div>

    <!-- Claim Form Body -->
    <div style="padding:28px 26px;">
      
      <form id="voucher-claim-form" onsubmit="event.preventDefault(); submitClaimTransfer();">
        @csrf

        <!-- =========================================================================
             SECTION 1: CUSTOMER PHONE & BENEFICIARY PAYMENT DETAILS (TOP SECTION)
             ========================================================================= -->
        <div style="display:flex; flex-direction:column; gap:16px;">
          
          <!-- 1. Customer Phone No. -->
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" for="customer_phone" style="font-weight:700; font-size:0.92rem; color:#1e293b; margin-bottom:8px; display:flex; align-items:center; justify-content:space-between;">
              <span style="display:inline-flex; align-items:center; gap:6px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                Customer Phone No. <span style="color:#ef4444;">*</span>
              </span>
              <span style="font-size:0.75rem; color:#64748b; font-weight:600;">Linked Mobile</span>
            </label>
            
            <div style="position:relative; display:flex; align-items:center;">
              <span style="position:absolute; left:14px; font-weight:700; color:#64748b; font-size:0.9rem; pointer-events:none; border-right:1px solid #cbd5e1; padding-right:10px; line-height:1.2;">
                +91
              </span>
              <input 
                type="tel" 
                id="customer_phone" 
                name="customer_phone" 
                class="form-control" 
                placeholder="Enter 10-digit phone number" 
                required 
                maxlength="10" 
                pattern="[0-9]{10}"
                value="{{ $voucher->customer_phone ?? '' }}" 
                style="width:100%; padding:12px 14px 12px 64px; border:1.5px solid #cbd5e1; border-radius:10px; font-size:0.98rem; font-weight:700; color:#0f172a; transition:all 0.2s;"
                oninput="onPhoneInputChange(this)"
              >
            </div>
          </div>

          <!-- 2. Your UPI ID -->
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" for="customer_upi" style="font-weight:700; font-size:0.92rem; color:#1e293b; margin-bottom:8px; display:flex; align-items:center; justify-content:space-between;">
              <span style="display:inline-flex; align-items:center; gap:6px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><line x1="12" y1="6" x2="12" y2="8"/><line x1="12" y1="16" x2="12" y2="18"/></svg>
                Your UPI ID
              </span>
              <span style="font-size:0.75rem; color:#64748b; font-weight:600;">For Payout</span>
            </label>

            <div style="position:relative; display:flex; align-items:center;">
              <input 
                type="text" 
                id="customer_upi" 
                name="recipient_upi_id" 
                class="form-control font-mono" 
                placeholder="e.g. yourname@okhdfcbank or 9876543210@paytm" 
                value="{{ $voucher->recipient_upi_id ?? '' }}"
                style="width:100%; padding:12px 14px; border:1.5px solid #cbd5e1; border-radius:10px; font-size:0.95rem; font-weight:700; color:#0f172a; letter-spacing:0.02em;"
                oninput="onUpiInputChange(this)"
              >
            </div>
          </div>

          <!-- Stylized OR Divider -->
          <div style="display:flex; align-items:center; gap:14px; margin:2px 0;">
            <div style="flex:1; height:1px; background:#e2e8f0;"></div>
            <span style="font-size:0.8rem; font-weight:800; color:#64748b; text-transform:lowercase; background:#f8fafc; padding:2px 12px; border-radius:20px; border:1px solid #e2e8f0; font-style:italic;">or.</span>
            <div style="flex:1; height:1px; background:#e2e8f0;"></div>
          </div>

          <!-- 3. Enter your QR For Payment (Upload Payment QR) -->
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; font-size:0.92rem; color:#1e293b; margin-bottom:8px; display:flex; align-items:center; justify-content:space-between;">
              <span style="display:inline-flex; align-items:center; gap:6px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/></svg>
                Enter your QR For Payment
              </span>
              <span style="font-size:0.75rem; color:#64748b; font-weight:600;">Image / Screenshot</span>
            </label>

            <!-- Hidden File Input for Customer QR -->
            <input type="file" id="customer_qr_file" accept="image/*" style="display:none;" onchange="handleCustomerPaymentQrUpload(event)">
            
            <div 
              id="payment_qr_dropzone" 
              onclick="document.getElementById('customer_qr_file').click()" 
              style="border:2px dashed #cbd5e1; background:#f8fafc; border-radius:12px; padding:14px 18px; cursor:pointer; display:flex; align-items:center; justify-content:space-between; transition:all 0.2s; gap:12px;"
              onmouseover="this.style.borderColor='#2563eb'; this.style.background='#eff6ff';"
              onmouseout="this.style.borderColor='#cbd5e1'; this.style.background='#f8fafc';"
            >
              <div style="display:flex; align-items:center; gap:12px;">
                <div id="payment_qr_preview_box" style="width:42px; height:42px; border-radius:10px; background:#e2e8f0; display:flex; align-items:center; justify-content:center; color:#64748b; overflow:hidden; flex-shrink:0;">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                </div>
                <div>
                  <div id="payment_qr_label" style="font-weight:700; font-size:0.9rem; color:#1e293b;">Click to upload your QR for payment</div>
                  <div id="payment_qr_sub" style="font-size:0.75rem; color:#64748b;">Supports Paytm, PhonePe, Google Pay, BHIM QR</div>
                </div>
              </div>

              <button type="button" class="btn btn-secondary btn-sm" style="font-weight:700; font-size:0.8rem; pointer-events:none; padding:6px 14px; border-color:#cbd5e1; background:#ffffff; color:#1e293b; border-radius:8px;">
                Upload
              </button>
            </div>
            
            <input type="hidden" id="uploaded_customer_qr_url" name="recipient_qr_image" value="{{ $voucher->recipient_qr_image ?? '' }}">
          </div>

        </div>

        <!-- =========================================================================
             DIVIDER 1
             ========================================================================= -->
        <div style="height:2px; background:linear-gradient(90deg, transparent 0%, #cbd5e1 50%, transparent 100%); margin:26px 0;"></div>

        <!-- =========================================================================
             SECTION 2: ENTER OUR UNIQUE NO OR OUR QR IMAGE (MIDDLE SECTION)
             ========================================================================= -->
        <div style="display:flex; flex-direction:column; gap:16px;">
          
          <!-- 4. ENTER OUR UNIQUE NO -->
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" for="voucher_code" style="font-weight:800; font-size:0.92rem; color:#1e293b; margin-bottom:8px; display:flex; align-items:center; justify-content:space-between;">
              <span style="display:inline-flex; align-items:center; gap:6px; letter-spacing:0.02em;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2.2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 8h10"/><path d="M7 12h10"/><path d="M7 16h6"/></svg>
                ENTER OUR UNIQUE NO <span style="color:#ef4444;">*</span>
              </span>
              <span id="code_validation_pill" style="font-size:0.72rem; padding:2px 8px; border-radius:12px; font-weight:700; background:#e0e7ff; color:#4338ca;">
                {{ !empty($code) ? 'Detected' : 'Required' }}
              </span>
            </label>

            <div style="position:relative; display:flex; align-items:center;">
              <input 
                type="text" 
                id="voucher_code" 
                name="voucher_code" 
                class="form-control font-mono" 
                placeholder="e.g. ARM-500-0799A" 
                required 
                value="{{ $code ?? ($voucher->voucher_code ?? '') }}"
                style="width:100%; padding:13px 14px; border:2px solid #6366f1; border-radius:10px; font-size:1.15rem; font-weight:800; color:#312e81; text-transform:uppercase; letter-spacing:1.5px; background:#faf5ff;"
                oninput="onVoucherCodeInput(this.value)"
              >
            </div>
          </div>

          <!-- Stylized OR Divider -->
          <div style="display:flex; align-items:center; gap:14px; margin:2px 0;">
            <div style="flex:1; height:1px; background:#e2e8f0;"></div>
            <span style="font-size:0.8rem; font-weight:800; color:#64748b; text-transform:lowercase; background:#f8fafc; padding:2px 12px; border-radius:20px; border:1px solid #e2e8f0; font-style:italic;">or.</span>
            <div style="flex:1; height:1px; background:#e2e8f0;"></div>
          </div>

          <!-- 5. OUR QR Image (Upload QR / Scan Camera) -->
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; font-size:0.92rem; color:#1e293b; margin-bottom:8px; display:flex; align-items:center; justify-content:space-between;">
              <span style="display:inline-flex; align-items:center; gap:6px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2.2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                OUR QR Image
              </span>
              <span style="font-size:0.75rem; color:#64748b; font-weight:600;">Scan / Upload Sticker</span>
            </label>

            <!-- Hidden File Input for Voucher QR -->
            <input type="file" id="voucher_qr_file" accept="image/*" style="display:none;" onchange="handleVoucherQrImageUpload(event)">

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
              <!-- Upload Button -->
              <button 
                type="button" 
                class="btn" 
                onclick="document.getElementById('voucher_qr_file').click()" 
                style="padding:12px 14px; background:#f8fafc; border:1.5px solid #cbd5e1; border-radius:10px; font-weight:700; font-size:0.9rem; color:#1e293b; display:flex; align-items:center; justify-content:center; gap:8px; transition:all 0.2s;"
                onmouseover="this.style.borderColor='#4f46e5'; this.style.color='#4f46e5';"
                onmouseout="this.style.borderColor='#cbd5e1'; this.style.color='#1e293b';"
              >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                <span>Upload QR</span>
              </button>

              <!-- Scan Camera Button -->
              <button 
                type="button" 
                class="btn" 
                onclick="toggleCameraScannerModal()" 
                style="padding:12px 14px; background:#eef2ff; border:1.5px solid #c7d2fe; border-radius:10px; font-weight:700; font-size:0.9rem; color:#4338ca; display:flex; align-items:center; justify-content:center; gap:8px; transition:all 0.2s;"
                onmouseover="this.style.background='#e0e7ff';"
                onmouseout="this.style.background='#eef2ff';"
              >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                <span>Scan Camera</span>
              </button>
            </div>

            <!-- Inline Camera Viewport (if active) -->
            <div id="camera_scanner_container" style="display:none; margin-top:14px; position:relative; border-radius:12px; overflow:hidden; border:2px solid #4f46e5; background:#000000;">
              <video id="camera_video" playsinline style="width:100%; height:220px; object-fit:cover;"></video>
              <canvas id="camera_canvas" style="display:none;"></canvas>
              <div style="position:absolute; inset:0; border:2px solid rgba(255,255,255,0.4); margin:20px; border-radius:12px; pointer-events:none; box-shadow:0 0 0 2000px rgba(0,0,0,0.4);"></div>
              <div style="position:absolute; bottom:10px; left:0; right:0; text-align:center; color:#fff; font-size:0.8rem; font-weight:600; text-shadow:0 1px 3px rgba(0,0,0,0.8);">
                Align QR sticker inside frame
              </div>
              <button type="button" onclick="stopCameraScanner()" class="btn btn-sm btn-danger" style="position:absolute; top:8px; right:8px; font-weight:700; font-size:0.75rem;">
                ✕ Close Camera
              </button>
            </div>

          </div>

        </div>

        <!-- =========================================================================
             DIVIDER 2
             ========================================================================= -->
        <div style="height:2px; background:linear-gradient(90deg, transparent 0%, #cbd5e1 50%, transparent 100%); margin:26px 0;"></div>

        <!-- =========================================================================
             SECTION 3: TRANSFER AMOUNT DISPLAY & CLAIM SUBMIT (BOTTOM SECTION)
             ("Neeche transfer amount show krwana")
             ========================================================================= -->
        <div style="display:flex; flex-direction:column; gap:16px;">
          
          <!-- Transfer Amount Hero Card -->
          <div style="background:linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border:2px solid #86efac; border-radius:16px; padding:22px 24px; position:relative; overflow:hidden; box-shadow:0 4px 14px -2px rgba(16, 185, 129, 0.15);">
            <div style="position:absolute; top:-10px; right:-10px; width:90px; height:90px; background:radial-gradient(circle, rgba(34,197,94,0.2) 0%, transparent 70%); border-radius:50%; pointer-events:none;"></div>

            <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:10px;">
              <div>
                <div style="font-size:0.82rem; font-weight:800; text-transform:uppercase; letter-spacing:0.06em; color:#15803d; margin-bottom:4px; display:flex; align-items:center; gap:6px;">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                  <span>TRANSFER AMOUNT</span>
                </div>
                <div style="font-size:0.82rem; color:#166534; font-weight:600;">
                  Instant cashback reward credited directly to you
                </div>
              </div>

              <div id="transfer_badge" style="background:#bbf7d0; color:#14532d; font-weight:800; font-size:0.8rem; padding:5px 12px; border-radius:20px; border:1px solid #86efac; display:inline-flex; align-items:center; gap:5px;">
                <span style="width:6px; height:6px; border-radius:50%; background:#16a34a;"></span>
                <span id="transfer_badge_text">Active & Claimable</span>
              </div>
            </div>

            <!-- Big Transfer Amount Value -->
            <div style="margin:16px 0 12px; display:flex; align-items:baseline; gap:6px;">
              <span style="font-size:1.6rem; font-weight:900; color:#14532d;">₹</span>
              <span id="transfer_amount_display" style="font-size:2.6rem; font-weight:900; color:#14532d; letter-spacing:-0.03em; line-height:1;">
                {{ isset($voucher) ? number_format($voucher->amount ?: ($voucher->discount_amount ?: 500), 2) : '500.00' }}
              </span>
              <span style="font-size:0.9rem; font-weight:700; color:#166534; margin-left:4px;">INR</span>
            </div>

            <!-- Live Details Summary Row -->
            <div style="background:rgba(255,255,255,0.7); border:1px solid rgba(22, 101, 52, 0.15); border-radius:10px; padding:10px 14px; font-size:0.82rem; color:#166534; display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:8px;">
              <div>
                <span style="color:#15803d; font-weight:600;">Voucher Code:</span>
                <strong id="summary_voucher_code" class="font-mono" style="color:#14532d; margin-left:4px;">{{ $code ?: ($voucher->voucher_code ?? 'ARM-500-0799A') }}</strong>
              </div>
              <div>
                <span style="color:#15803d; font-weight:600;">Payout Mode:</span>
                <strong id="summary_payout_mode" style="color:#14532d; margin-left:4px;">Direct UPI / QR</strong>
              </div>
            </div>

          </div>

          <!-- Status Alert Banner (Validation Feedback) -->
          <div id="validation_alert_box" style="display:none; border-radius:10px; padding:12px 16px; font-size:0.88rem; font-weight:600; align-items:center; gap:10px;"></div>

          <!-- Claim & Transfer Submit Button -->
          <button 
            type="submit" 
            id="claim_submit_btn" 
            class="btn btn-primary btn-lg" 
            style="width:100%; padding:16px 20px; background:linear-gradient(135deg, #16a34a 0%, #15803d 100%); color:#ffffff; border:none; border-radius:12px; font-size:1.1rem; font-weight:800; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:10px; box-shadow:0 8px 20px -3px rgba(22, 163, 74, 0.4); transition:all 0.2s;"
            onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 10px 24px -3px rgba(22, 163, 74, 0.5)';"
            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 20px -3px rgba(22, 163, 74, 0.4)';"
          >
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            <span id="claim_btn_text">⚡ Claim & Transfer Amount Now</span>
          </button>

          <p style="text-align:center; font-size:0.75rem; color:#64748b; margin:4px 0 0;">
            🔒 Safe & Secure single-use verification. Amount credited instantly upon confirmation.
          </p>

        </div>

      </form>

    </div>

  </div>

  <!-- =========================================================================
       SUCCESS CONFIRMATION RECEIPT (MODAL / OVERLAY)
       ========================================================================= -->
  <div id="success_receipt_modal" style="display:none; position:fixed; inset:0; background:rgba(15, 23, 42, 0.7); backdrop-filter:blur(6px); z-index:9999; align-items:center; justify-content:center; padding:16px;">
    <div style="background:#ffffff; border-radius:20px; max-width:520px; width:100%; padding:30px 26px; text-align:center; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); position:relative; animation:popIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
      
      <!-- Green Success Icon -->
      <div style="width:68px; height:68px; border-radius:50%; background:#dcfce7; color:#16a34a; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; border:4px solid #f0fdf4;">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
      </div>

      <h2 style="margin:0; font-size:1.45rem; font-weight:800; color:#0f172a;">
        Transfer Successful & Claimed!
      </h2>
      <p style="margin:6px 0 20px; font-size:0.88rem; color:#64748b;">
        Your single-use voucher has been verified and amount transferred.
      </p>

      <!-- Receipt Breakdown Box -->
      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px; padding:18px; text-align:left; font-size:0.88rem; margin-bottom:22px;">
        
        <div style="display:flex; justify-content:space-between; margin-bottom:10px; border-bottom:1px dashed #cbd5e1; padding-bottom:10px;">
          <span style="color:#64748b;">Transfer Amount:</span>
          <strong id="receipt_amount" style="font-size:1.25rem; font-weight:900; color:#15803d;">₹0.00</strong>
        </div>

        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
          <span style="color:#64748b;">Claim Reference ID:</span>
          <strong id="receipt_claim_id" class="font-mono" style="color:#2563eb;">CLM-000000</strong>
        </div>

        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
          <span style="color:#64748b;">Voucher Code:</span>
          <strong id="receipt_code" class="font-mono" style="color:#0f172a;">---</strong>
        </div>

        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
          <span style="color:#64748b;">Beneficiary Phone:</span>
          <strong id="receipt_phone" style="color:#0f172a;">---</strong>
        </div>

        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
          <span style="color:#64748b;">Payout UPI / QR:</span>
          <strong id="receipt_payout_dest" class="font-mono" style="color:#0f172a; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">---</strong>
        </div>

        <div style="display:flex; justify-content:space-between;">
          <span style="color:#64748b;">Date & Time:</span>
          <span id="receipt_timestamp" style="color:#0f172a; font-weight:600;">---</span>
        </div>

      </div>

      <!-- Action Buttons -->
      <div style="display:flex; flex-direction:column; gap:10px;">
        <button type="button" class="btn btn-secondary w-full" onclick="window.print()" style="padding:12px; font-weight:700; border-radius:10px; display:flex; align-items:center; justify-content:center; gap:8px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
          Print Official Receipt Pass
        </button>

        <button type="button" class="btn btn-primary w-full" onclick="location.reload()" style="padding:12px; font-weight:700; border-radius:10px;">
          Claim Another Voucher
        </button>
      </div>

    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
  let activeVoucher = @json($voucher ?? null);
  let activeTransferAmount = {{ isset($voucher) ? (float)($voucher->amount ?: ($voucher->discount_amount ?: 500)) : 500 }};
  let cameraStream = null;
  let cameraAnimationId = null;

  document.addEventListener('DOMContentLoaded', function() {
    const codeInput = document.getElementById('voucher_code');
    const initialCode = codeInput ? codeInput.value.trim() : '';
    if (initialCode) {
      validateVoucherLive(initialCode);
    }
  });

  // 1. Phone input formatter
  function onPhoneInputChange(input) {
    input.value = input.value.replace(/\D/g, '').slice(0, 10);
  }

  // 2. UPI input listener
  function onUpiInputChange(input) {
    const upiVal = input.value.trim();
    const modeSpan = document.getElementById('summary_payout_mode');
    if (modeSpan) {
      modeSpan.innerText = upiVal ? upiVal : 'Direct UPI / QR';
    }
  }

  // 3. Customer Payment QR Upload handler
  function handleCustomerPaymentQrUpload(e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function() {
      const img = new Image();
      img.onload = function() {
        // Decode UPI details via jsQR
        const canvas = document.createElement('canvas');
        canvas.width = img.width;
        canvas.height = img.height;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0);
        const imgData = ctx.getImageData(0, 0, img.width, img.height);

        let parsedUpi = '';
        if (typeof jsQR !== 'undefined') {
          const qr = jsQR(imgData.data, imgData.width, imgData.height);
          if (qr && qr.data) {
            const dataStr = qr.data.trim();
            if (dataStr.startsWith('upi://') || dataStr.includes('pa=')) {
              try {
                const url = new URL(dataStr.startsWith('upi://') ? dataStr.replace('upi://pay', 'http://upi') : dataStr);
                parsedUpi = url.searchParams.get('pa') || '';
              } catch(err) {
                const paMatch = dataStr.match(/[?&]pa=([^&#\s]+)/i);
                if (paMatch) parsedUpi = decodeURIComponent(paMatch[1]);
              }
            } else if (dataStr.includes('@')) {
              parsedUpi = dataStr;
            }
          }
        }

        if (parsedUpi) {
          document.getElementById('customer_upi').value = parsedUpi;
          onUpiInputChange(document.getElementById('customer_upi'));
        }

        // Update preview UI
        const previewBox = document.getElementById('payment_qr_preview_box');
        if (previewBox) {
          previewBox.innerHTML = `<img src="${reader.result}" alt="Payment QR" style="width:100%; height:100%; object-fit:cover;">`;
        }
        document.getElementById('payment_qr_label').innerText = file.name;
        document.getElementById('payment_qr_sub').innerHTML = `<span style="color:#16a34a; font-weight:700;">✓ Payment QR Attached ${parsedUpi ? '(' + parsedUpi + ')' : ''}</span>`;

        // Upload to server
        const formData = new FormData();
        formData.append('qr_image', file);
        if (parsedUpi) formData.append('recipient_upi_id', parsedUpi);

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        fetch('{{ route('qr.uploadRecipient') }}', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          },
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          if (data.success && data.image_url) {
            document.getElementById('uploaded_customer_qr_url').value = data.image_url;
          }
        })
        .catch(err => console.error('QR upload notice:', err));
      };
      img.src = reader.result;
    };
    reader.readAsDataURL(file);
  }

  // 4. Clean Voucher Code Helper
  function cleanVoucherInput(raw) {
    if (!raw) return '';
    raw = raw.toString().trim();

    try {
      const parsed = JSON.parse(raw);
      if (parsed && parsed.code) return parsed.code.toString().toUpperCase().trim();
    } catch(e) {}

    const queryMatch = raw.match(/[?&](?:code|voucher|c|v)=([^&#\s]+)/i);
    if (queryMatch && queryMatch[1]) return decodeURIComponent(queryMatch[1]).toUpperCase().trim();

    const pathMatch = raw.match(/(?:\/claim\/|\/voucher\/|\/qr\/scanner\/)([^\/?&#\s]+)/i);
    if (pathMatch && pathMatch[1]) {
      const seg = decodeURIComponent(pathMatch[1]).toUpperCase().trim();
      if (!['SCANNER', 'CLAIM', 'QR', 'PUBLIC', 'GENERATOR'].includes(seg)) return seg;
    }

    return raw.replace(/^["'\s]+|["'\s/]+$/g, '').toUpperCase().trim();
  }

  function onVoucherCodeInput(val) {
    const clean = cleanVoucherInput(val);
    const inputElem = document.getElementById('voucher_code');
    if (clean && clean !== val && (val.includes('/') || val.includes('?'))) {
      inputElem.value = clean;
    }
    const targetCode = clean || val.toUpperCase().trim();
    if (targetCode.length >= 3) {
      validateVoucherLive(targetCode);
    }
  }

  // 5. Live Voucher Validation with Backend
  async function validateVoucherLive(rawCode) {
    const code = cleanVoucherInput(rawCode);
    if (!code) return;

    const summaryCode = document.getElementById('summary_voucher_code');
    if (summaryCode) summaryCode.innerText = code;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    try {
      const res = await fetch('{{ route('qr.validate') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: JSON.stringify({ voucher_code: code })
      });

      const data = await res.json();
      const alertBox = document.getElementById('validation_alert_box');
      const pill = document.getElementById('code_validation_pill');
      const submitBtn = document.getElementById('claim_submit_btn');

      if (data.success) {
        activeVoucher = data.voucher;
        activeTransferAmount = parseFloat(data.amount || data.voucher.amount || data.voucher.discount_amount || 500);

        // Update Transfer Amount Display
        document.getElementById('transfer_amount_display').innerText = activeTransferAmount.toFixed(2);
        document.getElementById('transfer_badge_text').innerText = 'Active & Claimable';
        document.getElementById('transfer_badge').style.background = '#bbf7d0';
        document.getElementById('transfer_badge').style.color = '#14532d';

        if (pill) {
          pill.innerText = 'Valid Code ✓';
          pill.style.background = '#dcfce7';
          pill.style.color = '#15803d';
        }

        if (data.voucher.customer_phone) {
          const phoneInput = document.getElementById('customer_phone');
          if (!phoneInput.value) phoneInput.value = data.voucher.customer_phone;
        }

        if (alertBox) alertBox.style.display = 'none';
        if (submitBtn) submitBtn.disabled = false;

      } else {
        activeVoucher = null;
        if (pill) {
          pill.innerText = 'Invalid';
          pill.style.background = '#fee2e2';
          pill.style.color = '#b91c1c';
        }

        document.getElementById('transfer_badge_text').innerText = data.already_redeemed ? 'Already Redeemed' : (data.expired ? 'Expired' : 'Invalid');
        document.getElementById('transfer_badge').style.background = '#fee2e2';
        document.getElementById('transfer_badge').style.color = '#b91c1c';

        if (alertBox) {
          alertBox.style.display = 'flex';
          alertBox.style.background = '#fef2f2';
          alertBox.style.border = '1px solid #fecaca';
          alertBox.style.color = '#991b1b';
          alertBox.innerHTML = `<span>⚠️ ${data.message || 'Voucher cannot be claimed.'}</span>`;
        }
      }
    } catch(err) {
      console.error('Validation error:', err);
    }
  }

  // 6. Voucher QR Image Upload & jsQR Decode
  function handleVoucherQrImageUpload(e) {
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
          const qr = jsQR(imgData.data, imgData.width, imgData.height);
          if (qr && qr.data) {
            const extractedCode = cleanVoucherInput(qr.data);
            document.getElementById('voucher_code').value = extractedCode;
            validateVoucherLive(extractedCode);
            if (typeof Swal !== 'undefined') {
              Swal.fire({
                icon: 'success',
                title: 'QR Code Scanned!',
                text: `Unique Code: ${extractedCode}`,
                timer: 2000,
                showConfirmButton: false
              });
            }
            return;
          }
        }
        alert('Could not decode QR code from the uploaded image. Please ensure image is clear.');
      };
      img.src = reader.result;
    };
    reader.readAsDataURL(file);
  }

  // 7. Live Camera Scanner
  function toggleCameraScannerModal() {
    const container = document.getElementById('camera_scanner_container');
    if (container.style.display === 'none') {
      startCameraScanner();
    } else {
      stopCameraScanner();
    }
  }

  function startCameraScanner() {
    const container = document.getElementById('camera_scanner_container');
    const video = document.getElementById('camera_video');
    const canvas = document.getElementById('camera_canvas');
    container.style.display = 'block';

    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
      .then(stream => {
        cameraStream = stream;
        video.srcObject = stream;
        video.setAttribute('playsinline', true);
        video.play();
        cameraAnimationId = requestAnimationFrame(scanCameraFrame);
      })
      .catch(err => {
        alert('Camera access denied or unavailable: ' + err.message);
        container.style.display = 'none';
      });
  }

  function scanCameraFrame() {
    const video = document.getElementById('camera_video');
    const canvas = document.getElementById('camera_canvas');
    if (video.readyState === video.HAVE_ENOUGH_DATA) {
      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      const ctx = canvas.getContext('2d');
      ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
      const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);

      if (typeof jsQR !== 'undefined') {
        const qr = jsQR(imgData.data, imgData.width, imgData.height, {
          inversionAttempts: 'dontInvert'
        });
        if (qr && qr.data) {
          const code = cleanVoucherInput(qr.data);
          document.getElementById('voucher_code').value = code;
          stopCameraScanner();
          validateVoucherLive(code);
          return;
        }
      }
    }
    cameraAnimationId = requestAnimationFrame(scanCameraFrame);
  }

  function stopCameraScanner() {
    if (cameraAnimationId) cancelAnimationFrame(cameraAnimationId);
    if (cameraStream) {
      cameraStream.getTracks().forEach(track => track.stop());
      cameraStream = null;
    }
    const container = document.getElementById('camera_scanner_container');
    if (container) container.style.display = 'none';
  }

  // 8. Submit Claim & Instant Transfer
  async function submitClaimTransfer() {
    const phone = document.getElementById('customer_phone').value.trim();
    const upi = document.getElementById('customer_upi').value.trim();
    const qrUrl = document.getElementById('uploaded_customer_qr_url').value.trim();
    const rawCode = document.getElementById('voucher_code').value.trim();
    const code = cleanVoucherInput(rawCode);

    if (!phone || phone.length < 10) {
      alert('Please enter a valid 10-digit Customer Phone Number.');
      document.getElementById('customer_phone').focus();
      return;
    }

    if (!code) {
      alert('Please enter or scan our Unique Voucher Code.');
      document.getElementById('voucher_code').focus();
      return;
    }

    if (!upi && !qrUrl) {
      const confirmProceed = confirm('No UPI ID or Payment QR was entered. Would you like to proceed with claim registration linked to mobile ' + phone + '?');
      if (!confirmProceed) return;
    }

    const btn = document.getElementById('claim_submit_btn');
    const btnText = document.getElementById('claim_btn_text');
    btn.disabled = true;
    btnText.innerText = 'Processing Transfer & Verifying...';

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    try {
      const res = await fetch('{{ route('qr.redeem') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          voucher_code: code,
          customer_phone: phone,
          recipient_upi_id: upi || 'PAYOUT-' + phone,
          recipient_qr_image: qrUrl,
          order_bill: activeTransferAmount,
          payment_method: upi ? 'UPI' : (qrUrl ? 'QR' : 'Direct')
        })
      });

      const data = await res.json();
      btn.disabled = false;
      btnText.innerText = '⚡ Claim & Transfer Amount Now';

      if (data.success) {
        // Show Receipt Modal
        document.getElementById('receipt_amount').innerText = '₹' + parseFloat(data.discount_value || activeTransferAmount).toFixed(2);
        document.getElementById('receipt_claim_id').innerText = data.claim_id || ('CLM-' + Math.random().toString(36).substring(2, 8).toUpperCase());
        document.getElementById('receipt_code').innerText = code;
        document.getElementById('receipt_phone').innerText = '+91 ' + phone;
        document.getElementById('receipt_payout_dest').innerText = upi || (qrUrl ? 'Uploaded QR' : 'Mobile Linked');
        document.getElementById('receipt_timestamp').innerText = data.redeemed_at || new Date().toLocaleString();

        const modal = document.getElementById('success_receipt_modal');
        modal.style.display = 'flex';

      } else {
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'error',
            title: 'Claim Failed',
            text: data.message || 'Unable to redeem voucher.'
          });
        } else {
          alert('Claim Failed: ' + (data.message || 'Unable to redeem voucher.'));
        }
      }
    } catch(err) {
      btn.disabled = false;
      btnText.innerText = '⚡ Claim & Transfer Amount Now';
      console.error(err);
      alert('Network or Server error: ' + err.message);
    }
  }
</script>

<style>
@keyframes popIn {
  0% { transform: scale(0.92); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}

@media (max-width: 640px) {
  .card {
    border-radius: 14px !important;
  }
  #transfer_amount_display {
    font-size: 2.1rem !important;
  }
}
</style>
@endpush
