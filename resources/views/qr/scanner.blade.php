@extends('layouts.public')

@section('title', 'Claim Voucher & Transfer Amount - ' . ($companyName ?? 'aarambh'))

@section('content')
<div style="max-width:760px; margin:0 auto; padding:15px 12px 50px;">

  <!-- Main Card Container -->
  <div class="card" style="background:#ffffff; border-radius:20px; border:1px solid #e2e8f0; box-shadow:0 12px 36px -4px rgba(15, 23, 42, 0.08); overflow:hidden;">
    
    <!-- Top Header Ribbon -->
    <div style="background:linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding:24px 28px; color:#ffffff; position:relative; overflow:hidden;">
      <div style="position:absolute; top:-20px; right:-20px; width:130px; height:130px; background:radial-gradient(circle, rgba(99,102,241,0.25) 0%, rgba(99,102,241,0) 70%); border-radius:50%; pointer-events:none;"></div>
      
      @php
        $cName = $companyName ?? 'aarambh';
        $words = preg_split('/\s+/', trim($cName));
        $brandInitials = '';
        foreach ($words as $w) {
          if (!empty($w)) $brandInitials .= mb_strtoupper(mb_substr($w, 0, 1));
          if (strlen($brandInitials) >= 2) break;
        }
        if (empty($brandInitials)) $brandInitials = 'NA';
      @endphp
      <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <div style="display:flex; align-items:center; gap:14px;">
          <div style="width:48px; height:48px; border-radius:14px; background:linear-gradient(135deg, #2563eb, #4f46e5); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:1.15rem; box-shadow:0 6px 16px -2px rgba(37,99,235,0.4); flex-shrink:0;">
            {{ $brandInitials }}
          </div>
          <div>
            <h1 style="margin:0; font-size:1.35rem; font-weight:800; letter-spacing:-0.02em; color:#ffffff;">
              {{ $cName }}
            </h1>
            <p style="margin:3px 0 0; font-size:0.85rem; color:#94a3b8;">
              Customer Reward & Voucher Claim Portal
            </p>
          </div>
        </div>

        <div style="background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.15); padding:6px 14px; border-radius:20px; font-size:0.75rem; font-weight:700; color:#e2e8f0; backdrop-filter:blur(4px); display:inline-flex; align-items:center; gap:6px;">
          <span style="width:8px; height:8px; border-radius:50%; background:#22c55e; display:inline-block; box-shadow:0 0 8px #22c55e;"></span>
          Claim Portal Active
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

          <!-- Stylized Section: Full Address & PIN Code for Sending Gifts -->
          <div style="background:#f0fdf4; border:1.5px solid #86efac; border-radius:14px; padding:16px 18px; margin-top:4px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
              <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-size:1.15rem;">🎁</span>
                <div>
                  <h4 style="margin:0; font-size:0.95rem; font-weight:800; color:#14532d;">
                    Gift Delivery Address Details
                  </h4>
                  <p style="margin:2px 0 0; font-size:0.75rem; color:#15803d;">
                    Full shipping address & PIN code are required to deliver your gifts
                  </p>
                </div>
              </div>
              <span style="font-size:0.7rem; background:#bbf7d0; color:#14532d; font-weight:800; padding:2px 8px; border-radius:12px; border:1px solid #86efac;">
                Mandatory
              </span>
            </div>

            <!-- Full Address -->
            <div class="form-group" style="margin-bottom:12px;">
              <label class="form-label" for="recipient_address" style="font-weight:700; font-size:0.88rem; color:#14532d; margin-bottom:6px; display:flex; align-items:center; justify-content:space-between;">
                <span style="display:inline-flex; align-items:center; gap:6px;">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                  Full Address for Sending Gifts <span style="color:#ef4444;">*</span>
                </span>
                <span style="font-size:0.72rem; color:#166534; font-weight:600;">Doorstep Delivery</span>
              </label>
              <textarea 
                id="recipient_address" 
                name="recipient_address" 
                class="form-control" 
                rows="3" 
                required 
                placeholder="House / Flat No., Building Name, Street / Road, Area, Landmark, City..." 
                style="width:100%; padding:10px 14px; border:1.5px solid #86efac; border-radius:10px; font-size:0.9rem; font-weight:600; color:#0f172a; background:#ffffff; resize:vertical;"
              >{{ $voucher->recipient_address ?? '' }}</textarea>
            </div>

            <!-- PIN Code and City/State Grid -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
              <!-- PIN Code (Required) -->
              <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" for="recipient_pincode" style="font-weight:700; font-size:0.88rem; color:#14532d; margin-bottom:6px; display:flex; align-items:center; justify-content:space-between;">
                  <span style="display:inline-flex; align-items:center; gap:5px;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18"/><path d="M15 3v18"/></svg>
                    PIN Code <span style="color:#ef4444;">*</span>
                  </span>
                  <span style="font-size:0.72rem; color:#dc2626; font-weight:700;">6 Digits</span>
                </label>
                <input 
                  type="text" 
                  id="recipient_pincode" 
                  name="recipient_pincode" 
                  class="form-control font-mono" 
                  required 
                  maxlength="6" 
                  pattern="[0-9]{6}" 
                  placeholder="e.g. 302001" 
                  value="{{ $voucher->recipient_pincode ?? '' }}" 
                  oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                  style="width:100%; padding:10px 14px; border:1.5px solid #86efac; border-radius:10px; font-size:1rem; font-weight:800; color:#14532d; background:#ffffff; letter-spacing:2px;"
                >
              </div>

              <!-- City / State (Optional Helper) -->
              <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" for="recipient_city" style="font-weight:700; font-size:0.88rem; color:#14532d; margin-bottom:6px;">
                  City / State
                </label>
                <input 
                  type="text" 
                  id="recipient_city" 
                  name="recipient_city" 
                  class="form-control" 
                  placeholder="e.g. Jaipur, Rajasthan" 
                  value="{{ $voucher->recipient_city ?? '' }}" 
                  style="width:100%; padding:10px 14px; border:1.5px solid #86efac; border-radius:10px; font-size:0.9rem; font-weight:600; color:#0f172a; background:#ffffff;"
                >
              </div>
            </div>

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
            
            <!-- Hidden File Input for Voucher QR (Upload from gallery/files) -->
            <input type="file" id="voucher_qr_file" accept="image/*" style="display:none;" onchange="handleVoucherQrImageUpload(event)">
            
            <div style="margin-top:8px; display:flex; align-items:center; justify-content:space-between; font-size:0.8rem; color:#64748b;">
              <span>Unique code is printed on your voucher sticker</span>
              <button 
                type="button" 
                onclick="document.getElementById('voucher_qr_file').click()" 
                style="background:none; border:none; padding:0; color:#4f46e5; font-weight:700; cursor:pointer; text-decoration:underline; font-size:0.82rem; display:inline-flex; align-items:center; gap:4px;"
              >
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Upload QR Image
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
          
          @php
            $hasValidInitialVoucher = isset($voucher) && ($voucher->status === 'Active' || !$voucher->is_redeemed) && ((float)($voucher->amount ?: ($voucher->discount_amount ?: 0)) > 0);
            $initialAmount = $hasValidInitialVoucher ? (float)($voucher->amount ?: ($voucher->discount_amount ?: 0)) : 0;
            $initialCode = $hasValidInitialVoucher ? ($code ?: $voucher->voucher_code) : '';
          @endphp

          <!-- Transfer Amount Pending Placeholder (Shown until voucher code is entered/scanned) -->
          <div id="voucher_waiting_placeholder" style="{{ $hasValidInitialVoucher ? 'display:none;' : 'display:flex;' }} align-items:center; gap:14px; background:#f8fafc; border:2px dashed #cbd5e1; border-radius:16px; padding:20px 22px; color:#64748b;">
            <div style="width:48px; height:48px; border-radius:12px; background:#e2e8f0; display:flex; align-items:center; justify-content:center; flex-shrink:0; color:#475569;">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="16" rx="2"/>
                <path d="M7 8h10M7 12h10M7 16h6"/>
              </svg>
            </div>
            <div>
              <div style="font-weight:800; font-size:0.95rem; color:#1e293b; margin-bottom:2px;">
                Enter Voucher Code to Calculate Transfer Amount
              </div>
              <div style="font-size:0.82rem; color:#64748b; line-height:1.4;">
                Transfer amount will appear here once your unique voucher code is entered or scanned.
              </div>
            </div>
          </div>

          <!-- Transfer Amount Hero Card (Visible ONLY when valid voucher code & amount exist) -->
          <div id="transfer_amount_card" style="{{ $hasValidInitialVoucher ? 'display:block;' : 'display:none;' }} background:linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border:2px solid #86efac; border-radius:16px; padding:22px 24px; position:relative; overflow:hidden; box-shadow:0 4px 14px -2px rgba(16, 185, 129, 0.15);">
            <div style="position:absolute; top:-10px; right:-10px; width:90px; height:90px; background:radial-gradient(circle, rgba(34,197,94,0.2) 0%, transparent 70%); border-radius:50%; pointer-events:none;"></div>

            <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:10px;">
              <div>
                <div style="font-size:0.82rem; font-weight:800; text-transform:uppercase; letter-spacing:0.06em; color:#15803d; margin-bottom:4px; display:flex; align-items:center; gap:6px;">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                  <span>TRANSFER AMOUNT</span>
                </div>
                <div style="font-size:0.82rem; color:#166534; font-weight:600;">
                  Cashback reward verified and processed after claim review
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
                {{ $hasValidInitialVoucher ? number_format($initialAmount, 2) : '0.00' }}
              </span>
              <span style="font-size:0.9rem; font-weight:700; color:#166534; margin-left:4px;">INR</span>
            </div>

            <!-- Live Details Summary Row -->
            <div style="background:rgba(255,255,255,0.7); border:1px solid rgba(22, 101, 52, 0.15); border-radius:10px; padding:10px 14px; font-size:0.82rem; color:#166534; display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:8px;">
              <div>
                <span style="color:#15803d; font-weight:600;">Voucher Code:</span>
                <strong id="summary_voucher_code" class="font-mono" style="color:#14532d; margin-left:4px;">{{ $initialCode ?: '---' }}</strong>
              </div>
              <div>
                <span style="color:#15803d; font-weight:600;">Payout Mode:</span>
                <strong id="summary_payout_mode" style="color:#14532d; margin-left:4px;">Direct UPI / QR</strong>
              </div>
            </div>

          </div>

          <!-- Status Alert Banner (Validation Feedback) -->
          <div id="validation_alert_box" style="display:none; border-radius:10px; padding:12px 16px; font-size:0.88rem; font-weight:600; align-items:center; gap:10px;"></div>

          <!-- Claim Submit Button -->
          <button 
            type="submit" 
            id="claim_submit_btn" 
            class="btn btn-primary btn-lg" 
            {{ $hasValidInitialVoucher ? '' : 'disabled' }}
            style="width:100%; padding:16px 20px; background:linear-gradient(135deg, #16a34a 0%, #15803d 100%); color:#ffffff; border:none; border-radius:12px; font-size:1.1rem; font-weight:800; cursor:{{ $hasValidInitialVoucher ? 'pointer' : 'not-allowed' }}; opacity:{{ $hasValidInitialVoucher ? '1' : '0.55' }}; display:flex; align-items:center; justify-content:center; gap:10px; box-shadow:0 8px 20px -3px rgba(22, 163, 74, 0.4); transition:all 0.2s;"
            onmouseover="if(!this.disabled){this.style.transform='translateY(-1px)'; this.style.boxShadow='0 10px 24px -3px rgba(22, 163, 74, 0.5)';}"
            onmouseout="if(!this.disabled){this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 20px -3px rgba(22, 163, 74, 0.4)';}"
          >
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            <span id="claim_btn_text">{{ $hasValidInitialVoucher ? '⚡ Submit Voucher Claim Now' : 'Enter Voucher Code to Claim' }}</span>
          </button>

          <p style="text-align:center; font-size:0.75rem; color:#64748b; margin:4px 0 0;">
            🔒 Safe & Secure single-use verification. Payment will be checked and processed after claim verification.
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
        Claim Received Successfully!
      </h2>
      <p style="margin:8px 0 20px; font-size:0.92rem; color:#475569; font-weight:500; line-height:1.5;">
        We received your Claim, We will check and make payment soon if your claim is correct.
      </p>

      <!-- Receipt Breakdown Box -->
      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px; padding:18px; text-align:left; font-size:0.88rem; margin-bottom:22px;">
        
        <div style="display:flex; justify-content:space-between; margin-bottom:10px; border-bottom:1px dashed #cbd5e1; padding-bottom:10px;">
          <span style="color:#64748b;">Claim Amount:</span>
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

        <div style="display:flex; justify-content:space-between; margin-bottom:8px; border-top:1px dashed #cbd5e1; padding-top:8px;">
          <span style="color:#64748b; display:inline-flex; align-items:center; gap:4px;">
            <span>🎁</span> Gift Address:
          </span>
          <strong id="receipt_address" style="color:#0f172a; max-width:220px; text-align:right; font-size:0.82rem; line-height:1.3; word-break:break-word;">---</strong>
        </div>

        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
          <span style="color:#64748b;">Delivery PIN Code:</span>
          <strong id="receipt_pincode" class="font-mono" style="color:#15803d; font-weight:800; font-size:0.95rem;">---</strong>
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
  var activeVoucher = @json($hasValidInitialVoucher ? $voucher : null);
  var activeTransferAmount = {{ $hasValidInitialVoucher ? (float)$initialAmount : 0 }};

  document.addEventListener('DOMContentLoaded', function() {
    const codeInput = document.getElementById('voucher_code');
    const initialCode = codeInput ? codeInput.value.trim() : '';
    if (initialCode) {
      if (activeTransferAmount > 0) {
        updateTransferCardVisibility(true, activeTransferAmount, initialCode);
      }
      validateVoucherLive(initialCode);
    } else {
      updateTransferCardVisibility(false);
    }
  });

  // UI state switcher helper: Shows Transfer Amount card only when voucher has valid amount
  function updateTransferCardVisibility(isValid, amount = 0, voucherCode = '') {
    const card = document.getElementById('transfer_amount_card');
    const placeholder = document.getElementById('voucher_waiting_placeholder');
    const submitBtn = document.getElementById('claim_submit_btn');
    const submitBtnText = document.getElementById('claim_btn_text');
    const amountDisplay = document.getElementById('transfer_amount_display');
    const summaryCode = document.getElementById('summary_voucher_code');

    if (isValid && amount > 0) {
      if (card) card.style.display = 'block';
      if (placeholder) placeholder.style.display = 'none';
      if (amountDisplay) amountDisplay.innerText = parseFloat(amount).toFixed(2);
      if (summaryCode && voucherCode) summaryCode.innerText = voucherCode;

      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.style.cursor = 'pointer';
        submitBtn.style.opacity = '1';
      }
      if (submitBtnText) submitBtnText.innerText = '⚡ Submit Voucher Claim Now';
    } else {
      if (card) card.style.display = 'none';
      if (placeholder) placeholder.style.display = 'flex';
      if (amountDisplay) amountDisplay.innerText = '0.00';
      if (summaryCode) summaryCode.innerText = voucherCode || '---';

      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.style.cursor = 'not-allowed';
        submitBtn.style.opacity = '0.55';
      }
      if (submitBtnText) submitBtnText.innerText = 'Enter Voucher Code to Claim';
    }
  }

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

  // =========================================================================
  // UNIVERSAL MULTI-TIER QR DECODER (Image / File / Screenshot)
  // Powered by ZXing BrowserMultiFormatReader, BarcodeDetector & jsQR
  // =========================================================================
  async function decodeQrFromImage(file) {
    if (!file) return null;

    // Load file into HTMLImageElement
    let img = null;
    try {
      img = await new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => {
          const image = new Image();
          image.onload = () => resolve(image);
          image.onerror = () => reject(new Error('Image failed to load'));
          image.src = reader.result;
        };
        reader.onerror = () => reject(new Error('File read failed'));
        reader.readAsDataURL(file);
      });
    } catch(loadErr) {
      console.warn('Image load failed:', loadErr);
      return null;
    }

    const naturalWidth = img.naturalWidth || img.width || 800;
    const naturalHeight = img.naturalHeight || img.height || 600;

    // Helper: offscreen canvas scaled
    function createScaledCanvas(maxDim) {
      let w = naturalWidth;
      let h = naturalHeight;
      if (w > maxDim || h > maxDim) {
        const scale = maxDim / Math.max(w, h);
        w = Math.round(w * scale);
        h = Math.round(h * scale);
      }
      const c = document.createElement('canvas');
      c.width = w;
      c.height = h;
      const ctx = c.getContext('2d', { willReadFrequently: true });
      ctx.drawImage(img, 0, 0, w, h);
      return c;
    }

    // Helper: center crop canvas
    function createCenterCropCanvas(cropRatio = 0.75, targetSize = 600) {
      const cropSize = Math.floor(Math.min(naturalWidth, naturalHeight) * cropRatio);
      const sx = Math.floor((naturalWidth - cropSize) / 2);
      const sy = Math.floor((naturalHeight - cropSize) / 2);
      const c = document.createElement('canvas');
      c.width = targetSize;
      c.height = targetSize;
      const ctx = c.getContext('2d', { willReadFrequently: true });
      ctx.drawImage(img, sx, sy, cropSize, cropSize, 0, 0, targetSize, targetSize);
      return c;
    }

    // =======================================================================
    // TIER 1: ZXing BrowserMultiFormatReader (Industry Gold Standard)
    // Works on all phone photos (rotated, tilted, curved, shadowed)
    // =======================================================================
    if (typeof ZXing !== 'undefined' && ZXing.BrowserMultiFormatReader) {
      try {
        const zxingReader = new ZXing.BrowserMultiFormatReader();

        // 1A. Direct image decode
        try {
          const res = await zxingReader.decodeFromImageElement(img);
          if (res && res.getText()) return res.getText().trim();
        } catch(e) {}

        // 1B. Center crop at 800px (most phone users center the sticker)
        try {
          const cropC = createCenterCropCanvas(0.8, 800);
          const res = await zxingReader.decodeFromCanvas(cropC);
          if (res && res.getText()) return res.getText().trim();
        } catch(e) {}

        // 1C. Scaled to 1200px
        try {
          const scaledC = createScaledCanvas(1200);
          const res = await zxingReader.decodeFromCanvas(scaledC);
          if (res && res.getText()) return res.getText().trim();
        } catch(e) {}

        // 1D. Scaled to 700px
        try {
          const scaledC2 = createScaledCanvas(700);
          const res = await zxingReader.decodeFromCanvas(scaledC2);
          if (res && res.getText()) return res.getText().trim();
        } catch(e) {}
      } catch(zxingErr) {
        console.warn('ZXing pass notice:', zxingErr);
      }
    }

    // =======================================================================
    // TIER 2: Native BarcodeDetector (GPU/Hardware Accelerated on Android/Chrome)
    // =======================================================================
    if ('BarcodeDetector' in window) {
      try {
        const detector = new BarcodeDetector({ formats: ['qr_code'] });

        // Direct img element
        let barcodes = await detector.detect(img);
        if (barcodes && barcodes.length > 0 && barcodes[0].rawValue) {
          return barcodes[0].rawValue.trim();
        }

        // Scaled canvas
        const scaledCanvas = createScaledCanvas(1000);
        barcodes = await detector.detect(scaledCanvas);
        if (barcodes && barcodes.length > 0 && barcodes[0].rawValue) {
          return barcodes[0].rawValue.trim();
        }
      } catch(bErr) {}
    }

    // =======================================================================
    // TIER 3: Multi-Scale jsQR with Contrast / Inversion Passes
    // =======================================================================
    if (typeof jsQR !== 'undefined') {
      try {
        // 3A. Center Crop
        const cropC = createCenterCropCanvas(0.8, 600);
        const cropCtx = cropC.getContext('2d', { willReadFrequently: true });
        let imgData = cropCtx.getImageData(0, 0, 600, 600);
        let qr = jsQR(imgData.data, 600, 600, { inversionAttempts: 'attemptBoth' });
        if (qr && qr.data) return qr.data.trim();

        // 3B. Multi-scale passes
        const sizes = [1000, 600, 1400];
        for (const s of sizes) {
          const c = createScaledCanvas(s);
          const ctx = c.getContext('2d', { willReadFrequently: true });
          imgData = ctx.getImageData(0, 0, c.width, c.height);
          qr = jsQR(imgData.data, c.width, c.height, { inversionAttempts: 'attemptBoth' });
          if (qr && qr.data) return qr.data.trim();
        }
      } catch(jsqrErr) {}
    }

    // =======================================================================
    // TIER 4: Html5Qrcode.scanFile with real-dimension offscreen DOM element
    // =======================================================================
    if (typeof Html5Qrcode !== 'undefined') {
      try {
        let tempDiv = document.getElementById('temp_html5qr_file_scan');
        if (!tempDiv) {
          tempDiv = document.createElement('div');
          tempDiv.id = 'temp_html5qr_file_scan';
          tempDiv.style.cssText = 'position:fixed; top:-9999px; left:-9999px; width:500px; height:500px; opacity:0; pointer-events:none; z-index:-1;';
          document.body.appendChild(tempDiv);
        }
        const fileScanner = new Html5Qrcode('temp_html5qr_file_scan');
        const text = await fileScanner.scanFile(file, false);
        try { await fileScanner.clear(); } catch(e) {}
        if (text && text.trim()) return text.trim();
      } catch(e) {}
    }

    return null;
  }

  // 3. Customer Payment QR Upload handler
  async function handleCustomerPaymentQrUpload(e) {
    const file = e.target.files[0];
    if (!file) return;

    // Show instant preview
    const reader = new FileReader();
    reader.onload = function() {
      const previewBox = document.getElementById('payment_qr_preview_box');
      if (previewBox) {
        previewBox.innerHTML = `<img src="${reader.result}" alt="Payment QR" style="width:100%; height:100%; object-fit:cover;">`;
      }
      document.getElementById('payment_qr_label').innerText = file.name;
    };
    reader.readAsDataURL(file);

    try {
      const decoded = await decodeQrFromImage(file);
      let parsedUpi = '';
      if (decoded) {
        const dataStr = decoded.trim();
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

      if (parsedUpi) {
        document.getElementById('customer_upi').value = parsedUpi;
        onUpiInputChange(document.getElementById('customer_upi'));
        document.getElementById('payment_qr_sub').innerHTML = `<span style="color:#16a34a; font-weight:700;">✓ Payment QR Attached (${parsedUpi})</span>`;
      } else {
        document.getElementById('payment_qr_sub').innerHTML = `<span style="color:#16a34a; font-weight:700;">✓ Payment QR Attached</span>`;
      }

      // Upload file to server
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
        if (data && data.success && data.image_url) {
          document.getElementById('uploaded_customer_qr_url').value = data.image_url;
        }
      })
      .catch(err => console.error('QR upload notice:', err));

    } catch(err) {
      console.error('Customer payment QR scan notice:', err);
    }
  }

  // 4. Clean Voucher Code Helper (Accepts URLs, query strings, JSON, paths, plain codes)
  function cleanVoucherInput(raw) {
    if (!raw) return '';
    raw = raw.toString().trim();

    // 1. JSON payload parse
    if ((raw.startsWith('{') && raw.endsWith('}')) || (raw.startsWith('[') && raw.endsWith(']'))) {
      try {
        const parsed = JSON.parse(raw);
        if (parsed) {
          if (parsed.code) return String(parsed.code).toUpperCase().trim();
          if (parsed.voucher_code) return String(parsed.voucher_code).toUpperCase().trim();
          if (parsed.voucher) return String(parsed.voucher).toUpperCase().trim();
        }
      } catch(e) {}
    }

    // 2. Query parameter match: ?voucher_code=, ?code=, ?voucher=, ?c=, ?v=
    const queryMatch = raw.match(/[?&](?:voucher_code|code|voucher|c|v)=([^&#\s]+)/i);
    if (queryMatch && queryMatch[1]) {
      return decodeURIComponent(queryMatch[1]).toUpperCase().trim();
    }

    // 3. Path route match: /claim/CODE or /voucher/CODE or /qr/scanner/CODE
    const pathMatch = raw.match(/(?:^|\/)(?:claim|voucher|qr\/scanner)\/([^\/?&#\s]+)/i);
    if (pathMatch && pathMatch[1]) {
      const seg = decodeURIComponent(pathMatch[1]).toUpperCase().trim();
      if (!['SCANNER', 'CLAIM', 'QR', 'PUBLIC', 'GENERATOR', 'HISTORY', 'INDEX'].includes(seg)) {
        return seg;
      }
    }

    // 4. URL path segment parse
    if (/^https?:\/\//i.test(raw)) {
      try {
        const urlObj = new URL(raw);
        const parts = urlObj.pathname.split('/').filter(Boolean);
        const last = parts[parts.length - 1];
        if (last && !['SCANNER', 'CLAIM', 'QR', 'PUBLIC', 'GENERATOR', 'GARMENT'].includes(last.toUpperCase())) {
          return decodeURIComponent(last).toUpperCase().trim();
        }
      } catch(e) {}
    }

    // 5. Clean text voucher code
    return raw.replace(/^["'\s/]+|["'\s/]+$/g, '').toUpperCase().trim();
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
    } else {
      activeVoucher = null;
      activeTransferAmount = 0;
      updateTransferCardVisibility(false, 0, targetCode);
      const pill = document.getElementById('code_validation_pill');
      if (pill) {
        pill.innerText = 'Required';
        pill.style.background = '#e0e7ff';
        pill.style.color = '#4338ca';
      }
      const alertBox = document.getElementById('validation_alert_box');
      if (alertBox) alertBox.style.display = 'none';
    }
  }

  // 5. Live Voucher Validation with Backend
  async function validateVoucherLive(rawCode) {
    const code = cleanVoucherInput(rawCode);
    if (!code) {
      activeVoucher = null;
      activeTransferAmount = 0;
      updateTransferCardVisibility(false);
      return;
    }

    const summaryCode = document.getElementById('summary_voucher_code');
    if (summaryCode) summaryCode.innerText = code;

    const pill = document.getElementById('code_validation_pill');
    const alertBox = document.getElementById('validation_alert_box');

    if (pill) {
      pill.innerText = 'Checking...';
      pill.style.background = '#fef3c7';
      pill.style.color = '#92400e';
    }

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

      let data = null;
      try {
        data = await res.json();
      } catch(parseErr) {
        data = { success: false, message: 'Server response error: invalid JSON.' };
      }

      if (data && data.success) {
        activeVoucher = data.voucher;
        activeTransferAmount = parseFloat(data.amount || data.voucher.amount || data.voucher.discount_amount || 0);

        if (activeTransferAmount > 0) {
          updateTransferCardVisibility(true, activeTransferAmount, data.voucher.voucher_code || code);

          const badgeText = document.getElementById('transfer_badge_text');
          const badge = document.getElementById('transfer_badge');
          if (badgeText) badgeText.innerText = 'Active & Claimable';
          if (badge) {
            badge.style.background = '#bbf7d0';
            badge.style.color = '#14532d';
          }

          if (pill) {
            pill.innerText = 'Valid Code ✓';
            pill.style.background = '#dcfce7';
            pill.style.color = '#15803d';
          }

          if (data.voucher.customer_phone) {
            const phoneInput = document.getElementById('customer_phone');
            if (phoneInput && !phoneInput.value) phoneInput.value = data.voucher.customer_phone;
          }

          if (alertBox) alertBox.style.display = 'none';
        } else {
          activeVoucher = null;
          activeTransferAmount = 0;
          updateTransferCardVisibility(false, 0, code);

          if (pill) {
            pill.innerText = 'No Amount';
            pill.style.background = '#fee2e2';
            pill.style.color = '#b91c1c';
          }

          if (alertBox) {
            alertBox.style.display = 'flex';
            alertBox.style.background = '#fef2f2';
            alertBox.style.border = '1px solid #fecaca';
            alertBox.style.color = '#991b1b';
            alertBox.innerHTML = `<span>⚠️ This voucher has zero or unconfigured transfer amount.</span>`;
          }
        }

      } else {
        activeVoucher = null;
        activeTransferAmount = 0;
        updateTransferCardVisibility(false, 0, code);

        if (pill) {
          pill.innerText = (data && data.already_redeemed) ? 'Redeemed' : ((data && data.expired) ? 'Expired' : 'Invalid');
          pill.style.background = '#fee2e2';
          pill.style.color = '#b91c1c';
        }

        if (alertBox) {
          alertBox.style.display = 'flex';
          alertBox.style.background = '#fef2f2';
          alertBox.style.border = '1px solid #fecaca';
          alertBox.style.color = '#991b1b';
          alertBox.innerHTML = `<span>⚠️ ${(data && data.message) ? data.message : 'Voucher cannot be claimed.'}</span>`;
        }
      }
    } catch(err) {
      console.error('Validation error:', err);
      activeVoucher = null;
      activeTransferAmount = 0;
      updateTransferCardVisibility(false, 0, code);
      if (pill) {
        pill.innerText = 'Network Error';
        pill.style.background = '#fee2e2';
        pill.style.color = '#b91c1c';
      }
    }
  }

  // 6. Voucher QR Image Upload Handler
  async function handleVoucherQrImageUpload(e) {
    const file = e.target.files[0];
    if (!file) return;

    const pill = document.getElementById('code_validation_pill');
    if (pill) {
      pill.innerText = 'Decoding QR...';
      pill.style.background = '#fef3c7';
      pill.style.color = '#92400e';
    }

    try {
      const decodedText = await decodeQrFromImage(file);
      if (decodedText) {
        const extractedCode = cleanVoucherInput(decodedText);
        if (extractedCode) {
          document.getElementById('voucher_code').value = extractedCode;
          validateVoucherLive(extractedCode);
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'success',
              title: 'QR Code Scanned!',
              text: `Voucher Code: ${extractedCode}`,
              timer: 2000,
              showConfirmButton: false
            });
          }
          return;
        }
      }

      alert('Could not decode QR code from the uploaded image. Please ensure the image is clear and well-lit, or manually enter the code.');
      if (pill) {
        pill.innerText = 'Not Found';
        pill.style.background = '#fee2e2';
        pill.style.color = '#b91c1c';
      }
    } catch(err) {
      console.error('File scan error:', err);
      alert('Error reading image file: ' + err.message);
    } finally {
      e.target.value = '';
    }
  }

  // 7. Voucher QR Scanner Helper
  function stopCameraScanner() {}

  function onSuccessfulQrScan(rawText) {
    if (navigator.vibrate) {
      try { navigator.vibrate([80]); } catch(e) {}
    }

    stopCameraScanner(true);

    const code = cleanVoucherInput(rawText);
    if (code) {
      const input = document.getElementById('voucher_code');
      if (input) input.value = code;
      validateVoucherLive(code);
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'success',
          title: 'QR Code Detected!',
          text: `Voucher Code: ${code}`,
          timer: 1800,
          showConfirmButton: false
        });
      }
    } else {
      alert('Scanned code could not be resolved: ' + rawText);
    }
  }

  // 8. Submit Claim & Instant Transfer
  async function submitClaimTransfer() {
    const phone = document.getElementById('customer_phone').value.trim();
    const upi = document.getElementById('customer_upi').value.trim();
    const qrUrl = document.getElementById('uploaded_customer_qr_url').value.trim();
    const rawCode = document.getElementById('voucher_code').value.trim();
    const code = cleanVoucherInput(rawCode);
    const address = (document.getElementById('recipient_address')?.value || '').trim();
    const pincode = (document.getElementById('recipient_pincode')?.value || '').trim();
    const city = (document.getElementById('recipient_city')?.value || '').trim();

    if (!phone || phone.length < 10) {
      alert('Please enter a valid 10-digit Customer Phone Number.');
      document.getElementById('customer_phone').focus();
      return;
    }

    if (!address || address.length < 5) {
      alert('Please enter your complete Full Delivery Address [for sending gifts].');
      document.getElementById('recipient_address')?.focus();
      return;
    }

    if (!pincode || !/^\d{6}$/.test(pincode)) {
      alert('A valid 6-digit PIN code is necessary for gift delivery.');
      document.getElementById('recipient_pincode')?.focus();
      return;
    }

    if (!code) {
      alert('Please enter or scan our Unique Voucher Code.');
      document.getElementById('voucher_code').focus();
      return;
    }

    if (!activeVoucher || !activeTransferAmount || activeTransferAmount <= 0) {
      alert('Valid voucher code and transfer amount are required before claim can be submitted.');
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
    btnText.innerText = 'Submitting Claim...';

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
          recipient_address: address,
          recipient_pincode: pincode,
          recipient_city: city,
          recipient_upi_id: upi || 'PAYOUT-' + phone,
          recipient_qr_image: qrUrl,
          order_bill: activeTransferAmount,
          payment_method: upi ? 'UPI' : (qrUrl ? 'QR' : 'Direct')
        })
      });

      const data = await res.json();
      btn.disabled = false;
      btnText.innerText = '⚡ Submit Voucher Claim Now';

      if (data.success) {
        // Show Receipt Modal
        document.getElementById('receipt_amount').innerText = '₹' + parseFloat(data.discount_value || activeTransferAmount).toFixed(2);
        document.getElementById('receipt_claim_id').innerText = data.claim_id || ('CLM-' + Math.random().toString(36).substring(2, 8).toUpperCase());
        document.getElementById('receipt_code').innerText = code;
        document.getElementById('receipt_phone').innerText = '+91 ' + phone;
        document.getElementById('receipt_payout_dest').innerText = upi || (qrUrl ? 'Uploaded QR' : 'Mobile Linked');
        if (document.getElementById('receipt_address')) {
          document.getElementById('receipt_address').innerText = data.recipient_address || address;
        }
        if (document.getElementById('receipt_pincode')) {
          document.getElementById('receipt_pincode').innerText = data.recipient_pincode || pincode;
        }
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
      btnText.innerText = '⚡ Submit Voucher Claim Now';
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

@keyframes qrScanLaser {
  0% { top: 8px; opacity: 0.85; }
  50% { top: 185px; opacity: 1; }
  100% { top: 8px; opacity: 0.85; }
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
