/* ==========================================================================
   QR MANAGEMENT VIEWS - CUSTOMER FRONT-END SCANNER/UPLOAD & ADMIN LIFECYCLE
   GarmentERP - FashionWorks Pvt. Ltd.
   ========================================================================== */

const QRView = {
  activeTab: "customer-portal", // 'customer-portal' | 'discount' | 'vouchers' | 'lot-label'
  customerInputMode: "upload",  // 'upload' | 'camera' | 'code'

  // Current working discount generator form state
  _currentDiscount: {
    amount: 500,
    type: "fixed", // 'fixed' | 'percent'
    code: "SAVE500-A92B",
    title: "Special Customer Discount",
    minBill: 1000,
    validTill: "2026-09-30",
    color: "#0f172a",
    usageType: "single" // 'single' | 'multi'
  },

  // State for Customer Front-End Portal
  _customerState: {
    phone: "+91 98201 12345",
    selectedCode: "SAVE500-A92B",
    uploadedFile: null,
    uploadedPreviewUrl: null,
    lastResult: null
  },

  // 1. MAIN QR VIEWPORT ROUTER
  renderGenerator() {
    return `
      <!-- Top Mode Navigation Tabs -->
      <div class="qr-tabs-nav">
        <button class="qr-tab-btn ${this.activeTab === 'customer-portal' ? 'active' : ''}" onclick="QRView.switchTab('customer-portal')">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/><circle cx="18" cy="6" r="3"/></svg>
          Customer QR Scan & Claim Portal (Front-End)
        </button>

        <button class="qr-tab-btn ${this.activeTab === 'vouchers' ? 'active' : ''}" onclick="QRView.switchTab('vouchers')">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
          Admin QR Management & Expiry Ledger (${(ERPState.data.discountCoupons || []).length})
        </button>

        <button class="qr-tab-btn ${this.activeTab === 'discount' ? 'active' : ''}" onclick="QRView.switchTab('discount')">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
          + Generate New QR Voucher
        </button>

        <button class="qr-tab-btn ${this.activeTab === 'lot-label' ? 'active' : ''}" onclick="QRView.switchTab('lot-label')">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg>
          Industrial Factory Lot Tag
        </button>
      </div>

      <div id="qr-tab-content-area">
        ${this.renderActiveTabContent()}
      </div>
    `;
  },

  switchTab(tabName) {
    this.activeTab = tabName;
    const contentArea = document.getElementById("qr-tab-content-area");
    if (contentArea) {
      contentArea.innerHTML = this.renderActiveTabContent();
    }
    document.querySelectorAll(".qr-tab-btn").forEach(btn => btn.classList.remove("active"));
    const activeBtn = document.querySelector(`.qr-tab-btn[onclick*="${tabName}"]`);
    if (activeBtn) activeBtn.classList.add("active");
  },

  renderActiveTabContent() {
    if (this.activeTab === "customer-portal") {
      return this.renderCustomerFrontEndPortal();
    } else if (this.activeTab === "vouchers") {
      return this.renderAdminVouchersManagement();
    } else if (this.activeTab === "lot-label") {
      return this.renderIndustrialLotView();
    }
    return this.renderDiscountGeneratorView();
  },

  // =========================================================================
  // SECTION A: CUSTOMER FRONT-END SETUP (PHONE + UNIQUE CODE + QR UPLOAD/SCAN)
  // =========================================================================
  renderCustomerFrontEndPortal() {
    const coupons = ERPState.data.discountCoupons || [];
    const cust = this._customerState;

    return `
      <div class="customer-portal-container">
        <!-- Branded Header Banner -->
        <div class="customer-portal-hero">
          <div class="customer-portal-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            OFFICIAL CUSTOMER DISCOUNT & REWARD PORTAL
          </div>
          <h2 class="customer-portal-title">Redeem Your Exclusive Discount Voucher</h2>
          <p class="customer-portal-subtitle">
            Enter your mobile number and upload or scan your unique QR voucher code to claim instant savings. Each QR voucher is single-use and automatically expires once redeemed.
          </p>
        </div>

        <div class="customer-portal-card">
          <!-- Step 1: Customer Phone Number Input -->
          <div class="portal-step-section">
            <div class="portal-step-header">
              <div class="portal-step-number">1</div>
              <div>
                <h4 class="portal-step-title">Enter Customer Mobile Number <span class="required-star">*</span></h4>
                <p class="portal-step-desc">Your 10-digit mobile number will be securely linked to this single-use voucher</p>
              </div>
            </div>

            <div class="portal-phone-input-row">
              <div class="portal-country-code">+91</div>
              <input type="tel" class="form-control portal-phone-input" id="cust-phone-input" 
                value="${cust.phone.replace('+91 ', '')}" 
                placeholder="98201 12345" 
                maxlength="14"
                oninput="QRView.onPhoneChange(this.value)">
              
              <button type="button" class="btn btn-secondary btn-sm" onclick="QRView.setSamplePhone('+91 98201 12345')">Demo Mobile 1</button>
              <button type="button" class="btn btn-secondary btn-sm" onclick="QRView.setSamplePhone('+91 98112 55678')">Demo Mobile 2</button>
            </div>
          </div>

          <!-- Step 2: Choose QR Input Mode -->
          <div class="portal-step-section" style="margin-top:24px;">
            <div class="portal-step-header">
              <div class="portal-step-number">2</div>
              <div>
                <h4 class="portal-step-title">Provide Your QR Voucher Code <span class="required-star">*</span></h4>
                <p class="portal-step-desc">Choose whether to upload a QR image, scan with camera, or enter the unique code</p>
              </div>
            </div>

            <!-- Mode Selector Tabs -->
            <div class="portal-mode-pills">
              <button type="button" class="portal-mode-pill ${this.customerInputMode === 'upload' ? 'active' : ''}" onclick="QRView.setCustomerInputMode('upload')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Upload QR Image File
              </button>
              <button type="button" class="portal-mode-pill ${this.customerInputMode === 'camera' ? 'active' : ''}" onclick="QRView.setCustomerInputMode('camera')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                Live Camera Scanner
              </button>
              <button type="button" class="portal-mode-pill ${this.customerInputMode === 'code' ? 'active' : ''}" onclick="QRView.setCustomerInputMode('code')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                Enter Unique Voucher Code
              </button>
            </div>

            <!-- Mode Content Viewport -->
            <div class="portal-mode-content">
              ${this.renderCustomerModeContent()}
            </div>
          </div>

          <!-- Quick Test Vouchers Selection Bar -->
          <div style="margin-top:20px; padding:14px; background:#f1f5f9; border-radius:var(--radius-lg); border:1px dashed var(--slate-300);">
            <div style="font-size:0.75rem; font-weight:800; color:var(--slate-600); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:8px;">
              💡 QUICK TEST CODES (CLICK TO LOAD ACTIVE OR EXPIRED CODES):
            </div>
            <div style="display:flex; flex-wrap:wrap; gap:8px;">
              ${coupons.map(c => `
                <button type="button" class="quick-test-code-btn ${c.status === 'Active' ? 'active-code' : 'expired-code'}" onclick="QRView.selectQuickCoupon('${c.code}')">
                  <span class="font-mono font-bold">${c.code}</span>
                  <span class="quick-code-badge ${c.status === 'Active' ? 'badge-active' : 'badge-used'}">
                    ${c.status === 'Active' ? `₹${c.amount} OFF (Active)` : (c.status === 'Redeemed / Expired' ? 'Used / Expired' : 'Expired Date')}
                  </span>
                </button>
              `).join('')}
            </div>
          </div>

          <!-- Action Submit Button -->
          <div style="margin-top:28px;">
            <button type="button" class="btn btn-primary w-full btn-lg" style="height:52px; font-size:1.05rem; font-weight:800;" onclick="QRView.submitCustomerRedemption()">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              Verify Code & Redeem Discount Now
            </button>
          </div>
        </div>

        <!-- Live Dynamic Redemption Result Box (Success Celebration OR Single-Use Expired Warning) -->
        <div id="customer-redemption-result-box" style="margin-top:28px;"></div>
      </div>
    `;
  },

  setCustomerInputMode(mode) {
    this.customerInputMode = mode;
    const container = document.querySelector(".portal-mode-content");
    if (container) {
      container.innerHTML = this.renderCustomerModeContent();
    }
    document.querySelectorAll(".portal-mode-pill").forEach(p => p.classList.remove("active"));
    const activePill = document.querySelector(`.portal-mode-pill[onclick*="${mode}"]`);
    if (activePill) activePill.classList.add("active");
  },

  renderCustomerModeContent() {
    const cust = this._customerState;

    if (this.customerInputMode === "upload") {
      return `
        <!-- Drag & Drop Upload Zone -->
        <div class="qr-upload-dropzone" id="qr-file-dropzone" onclick="document.getElementById('qr-file-input').click()">
          <input type="file" id="qr-file-input" accept="image/png, image/jpeg, image/webp, image/svg+xml" style="display:none;" onchange="QRView.handleFileUpload(event)">
          
          <div class="upload-icon-circle">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
          </div>
          
          <div style="font-size:1.05rem; font-weight:700; color:var(--slate-900); margin-top:12px;">
            Click to Browse or Drag & Drop QR Image
          </div>
          <div style="font-size:0.825rem; color:var(--slate-500); margin-top:4px;">
            Supported formats: PNG, JPG, JPEG, SVG, WebP (Max 10MB)
          </div>

          ${cust.uploadedPreviewUrl ? `
            <div class="upload-preview-card" onclick="event.stopPropagation()">
              <img src="${cust.uploadedPreviewUrl}" class="upload-preview-img" alt="QR Preview">
              <div>
                <div style="font-size:0.85rem; font-weight:700; color:var(--slate-900);">${cust.uploadedFile ? cust.uploadedFile.name : 'Voucher-QR.png'}</div>
                <div style="font-size:0.75rem; color:var(--success-700); font-weight:600; margin-top:2px;">
                  ✓ Decoded Unique Code: <strong class="font-mono">${cust.selectedCode}</strong>
                </div>
              </div>
            </div>
          ` : `
            <div style="margin-top:14px;">
              <span class="badge badge-primary">Target Code: <strong class="font-mono">${cust.selectedCode || 'SAVE500-A92B'}</strong></span>
            </div>
          `}
        </div>
      `;
    } else if (this.customerInputMode === "camera") {
      return `
        <!-- Live Camera Viewfinder Simulation -->
        <div class="customer-camera-viewport">
          <div style="font-size:0.85rem; color:#94a3b8; margin-bottom:12px; font-weight:600;">
            Center the voucher QR code in the active viewfinder:
          </div>

          <div class="scanner-viewfinder" style="width:240px; height:240px; margin:0 auto;">
            <div class="scanner-corners"></div>
            <div class="scanner-laser"></div>
            <div style="text-align:center;">
              <div style="background:#ffffff; padding:10px; border-radius:10px; display:inline-block; opacity:0.85;">
                ${QRManager.generateQRSVG(cust.selectedCode || 'SAVE500-A92B', 120)}
              </div>
            </div>
          </div>

          <div style="margin-top:16px; font-size:0.85rem; color:#38bdf8; font-weight:700;">
            Target Code Detected: <span class="font-mono" style="color:#ffffff;">${cust.selectedCode || 'SAVE500-A92B'}</span>
          </div>
        </div>
      `;
    }

    // Manual Code Input
    return `
      <div style="background:#ffffff; border:1px solid var(--slate-200); border-radius:var(--radius-lg); padding:20px;">
        <label class="form-label" style="font-size:0.9rem;">Unique Alphanumeric Voucher Code</label>
        <div style="display:flex; gap:10px; margin-top:8px;">
          <input type="text" class="form-control font-mono font-bold" style="font-size:1.15rem; text-transform:uppercase; letter-spacing:0.08em;" id="cust-manual-code" value="${cust.selectedCode}" placeholder="e.g. SAVE500-A92B" oninput="QRView.onManualCodeChange(this.value)">
          <button type="button" class="btn btn-secondary" onclick="document.getElementById('cust-manual-code').value='SAVE500-A92B'; QRView.onManualCodeChange('SAVE500-A92B');">Reset</button>
        </div>
        <div style="font-size:0.75rem; color:var(--slate-500); margin-top:8px;">
          Enter the exact unique code printed below the QR code on your voucher certificate.
        </div>
      </div>
    `;
  },

  onPhoneChange(val) {
    this._customerState.phone = val.startsWith("+91 ") ? val : "+91 " + val.replace("+91 ", "").trim();
  },

  setSamplePhone(phone) {
    this._customerState.phone = phone;
    const input = document.getElementById("cust-phone-input");
    if (input) input.value = phone.replace("+91 ", "");
    UI.showToast("Phone Number Set", `Using ${phone} for verification`, "info");
  },

  onManualCodeChange(val) {
    this._customerState.selectedCode = (val || "").toUpperCase().trim();
  },

  selectQuickCoupon(code) {
    this._customerState.selectedCode = code;
    const manualInput = document.getElementById("cust-manual-code");
    if (manualInput) manualInput.value = code;

    const modeContainer = document.querySelector(".portal-mode-content");
    if (modeContainer) modeContainer.innerHTML = this.renderCustomerModeContent();

    UI.showToast("Selected Code", `Testing code: ${code}`, "info");
  },

  handleFileUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    this._customerState.uploadedFile = file;

    QRManager.decodeImageFile(file, (res) => {
      if (res.success) {
        this._customerState.selectedCode = res.code;
        this._customerState.uploadedPreviewUrl = res.dataUrl;

        const container = document.querySelector(".portal-mode-content");
        if (container) container.innerHTML = this.renderCustomerModeContent();

        UI.showToast("QR Image Decoded!", `Extracted code: ${res.code} from ${file.name}`, "success");
      } else {
        UI.showToast("Upload Error", res.message, "danger");
      }
    });
  },

  // SUBMIT & EXECUTE VERIFICATION WITH SINGLE-USE AUTO-EXPIRATION
  submitCustomerRedemption() {
    const phoneInput = document.getElementById("cust-phone-input")?.value || this._customerState.phone;
    const cleanPhone = phoneInput.startsWith("+91 ") ? phoneInput : "+91 " + phoneInput.replace("+91 ", "").trim();

    if (!cleanPhone || cleanPhone.replace(/\D/g, '').length < 10) {
      UI.showToast("Invalid Mobile Number", "Please enter a valid 10-digit customer phone number", "warning");
      document.getElementById("cust-phone-input")?.focus();
      return;
    }

    const code = this._customerState.selectedCode || document.getElementById("cust-manual-code")?.value || "SAVE500-A92B";
    const resultBox = document.getElementById("customer-redemption-result-box");
    if (!resultBox) return;

    // Execute state verification & expiry
    const result = ERPState.redeemDiscountCoupon(code, cleanPhone);

    if (result.success) {
      // 1. SUCCESS: Single-Use Redemption Confirmed & Expired
      const cpn = result.coupon;
      const discountAmount = cpn.type === "percent" ? `${cpn.amount}%` : `₹${cpn.amount}`;
      const sampleBill = 3500;
      const discountVal = cpn.type === "percent" ? (sampleBill * cpn.amount) / 100 : cpn.amount;
      const finalPayable = Math.max(0, sampleBill - discountVal);

      UI.showToast("🎉 Discount Redeemed & Claimed!", `Applied ${discountAmount} OFF voucher (${cpn.code}) for ${cleanPhone}`, "success");

      resultBox.innerHTML = `
        <div class="redemption-success-card">
          <div class="success-top-banner">
            <div style="display:flex; align-items:center; gap:10px;">
              <div class="success-check-circle">✓</div>
              <div>
                <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.08em; font-weight:800; color:#15803d;">
                  VERIFICATION SUCCESSFUL • SINGLE-USE APPLIED
                </div>
                <div style="font-size:1.6rem; font-weight:900; color:#0f172a; margin-top:2px;">
                  FLAT ${discountAmount} OFF DISCOUNT CLAIMED!
                </div>
              </div>
            </div>

            <div style="text-align:right;">
              <span class="badge badge-danger font-bold" style="font-size:0.8rem; padding:6px 12px;">
                STATUS: REDEEMED / EXPIRED
              </span>
            </div>
          </div>

          <!-- Claim Meta Grid -->
          <div class="redemption-meta-grid">
            <div class="meta-item-box">
              <span class="meta-label">Claim Reference ID</span>
              <strong class="meta-val font-mono" style="color:var(--primary-600);">${result.claimId}</strong>
            </div>

            <div class="meta-item-box">
              <span class="meta-label">Registered Customer Mobile</span>
              <strong class="meta-val font-mono">${cleanPhone}</strong>
            </div>

            <div class="meta-item-box">
              <span class="meta-label">Voucher Code</span>
              <strong class="meta-val font-mono">${cpn.code}</strong>
            </div>

            <div class="meta-item-box">
              <span class="meta-label">Redemption Timestamp</span>
              <strong class="meta-val">${result.timestamp}</strong>
            </div>
          </div>

          <!-- Simulated Savings Bill Calculation -->
          <div class="discount-redeemed-bill-box" style="margin-top:16px;">
            <div>
              <div style="font-size:0.75rem; color:#64748b; font-weight:600;">Original Order Bill</div>
              <div style="font-size:1.15rem; font-weight:800; font-family:var(--font-mono); color:#475569; text-decoration:line-through;">₹${sampleBill.toLocaleString('en-IN')}</div>
            </div>

            <div style="color:#15803d; font-weight:800; font-size:1.15rem;">
              - ₹${discountVal.toLocaleString('en-IN')} Instant Discount
            </div>

            <div style="text-align:right;">
              <div style="font-size:0.75rem; color:#64748b; font-weight:600;">Customer Net Payable</div>
              <div style="font-size:1.45rem; font-weight:900; font-family:var(--font-mono); color:#15803d;">₹${finalPayable.toLocaleString('en-IN')}</div>
            </div>
          </div>

          <!-- Single-Use Notice Alert -->
          <div style="margin-top:16px; background:#fef2f2; border:1px solid #fecaca; border-radius:var(--radius-md); padding:12px 16px; font-size:0.825rem; color:#991b1b; display:flex; align-items:center; gap:8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span><strong>Single-Use Security Lock:</strong> This QR voucher has been permanently locked to mobile <strong>${cleanPhone}</strong> and marked <strong>Expired</strong> in the admin panel. Any subsequent scan attempt will be rejected.</span>
          </div>

          <!-- Action Buttons -->
          <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-top:20px;">
            <button type="button" class="btn btn-secondary btn-sm" onclick="QRView.switchTab('vouchers')">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
              View in Admin Ledger
            </button>

            <div style="display:flex; gap:10px;">
              <button type="button" class="btn btn-secondary" onclick="QRManager.openRedemptionReceiptModal(${JSON.stringify(result).replace(/"/g, '&quot;')})">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Print Official Claim Pass
              </button>
              <button type="button" class="btn btn-primary" onclick="UI.showToast('Discount Applied', 'Discount of ₹${discountVal} added to active dispatch', 'success'); App.navigate('dispatch', 'dispatch');">
                Apply to Order Dispatch →
              </button>
            </div>
          </div>
        </div>
      `;
    } else if (result.reason === "already_redeemed") {
      // 2. ERROR: ALREADY REDEEMED / EXPIRED SINGLE-USE VOUCHER
      const cpn = result.coupon;
      UI.showToast("Voucher Already Expired", "This single-use QR code has already been redeemed", "danger");

      resultBox.innerHTML = `
        <div class="redemption-error-card">
          <div style="display:flex; align-items:flex-start; gap:14px;">
            <div class="error-circle-icon">✕</div>
            <div style="flex:1;">
              <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.08em; font-weight:800; color:#dc2626;">
                SECURITY ALERT • REDEMPTION BLOCKED
              </div>
              <h3 style="font-size:1.3rem; font-weight:800; color:#991b1b; margin:4px 0;">
                This QR Voucher is Already Redeemed & Expired
              </h3>
              <p style="font-size:0.875rem; color:#7f1d1d; line-height:1.5;">
                ${result.message}
              </p>

              <div style="background:#ffffff; border:1px solid #fecaca; border-radius:var(--radius-md); padding:14px; margin-top:14px; font-size:0.825rem; line-height:1.7;">
                <div><strong>Voucher Code:</strong> <span class="font-mono">${cpn.code}</span> (${cpn.title})</div>
                <div><strong>Original Claimant Mobile:</strong> <span class="font-mono font-bold">${cpn.redeemedByPhone || 'Recorded in Admin Log'}</span></div>
                <div><strong>Redeemed At:</strong> <span>${cpn.redeemedAt || 'Earlier date'}</span></div>
                <div><strong>Current Status:</strong> <span class="badge badge-danger">EXPIRED (SINGLE-USE ONLY)</span></div>
              </div>

              <div style="margin-top:16px; display:flex; gap:10px;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="QRView.switchTab('vouchers')">Check Admin Listing</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="QRView.selectQuickCoupon('SAVE500-A92B')">Try Another Active Code</button>
              </div>
            </div>
          </div>
        </div>
      `;
    } else {
      // 3. OTHER ERROR (NOT FOUND / EXPIRED DATE)
      UI.showToast("Redemption Failed", result.message, "danger");

      resultBox.innerHTML = `
        <div class="redemption-error-card" style="background:#fffbeb; border-color:#fde68a;">
          <div style="display:flex; align-items:flex-start; gap:14px;">
            <div class="error-circle-icon" style="background:#f59e0b;">!</div>
            <div style="flex:1;">
              <h3 style="font-size:1.15rem; font-weight:800; color:#92400e; margin:0 0 4px 0;">
                Invalid or Expired Voucher Code
              </h3>
              <p style="font-size:0.875rem; color:#b45309; line-height:1.5;">
                ${result.message}
              </p>
              <div style="margin-top:12px;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="QRView.selectQuickCoupon('SAVE500-A92B')">Select Valid Test Code (SAVE500-A92B)</button>
              </div>
            </div>
          </div>
        </div>
      `;
    }

    resultBox.scrollIntoView({ behavior: "smooth", block: "nearest" });
  },

  // =========================================================================
  // SECTION B: ADMIN-END SETUP (CONNECTED QR MANAGEMENT & EXPIRE ON SCAN)
  // =========================================================================
  renderAdminVouchersManagement() {
    const coupons = ERPState.data.discountCoupons || [];
    const analytics = ERPState.getCouponAnalytics ? ERPState.getCouponAnalytics() : { totalIssued: coupons.length, active: 3, redeemed: 2, expired: 0, totalSavingsDisbursed: 1200 };

    return `
      <!-- Admin Analytics Summary KPI Cards -->
      <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:16px; margin-bottom:20px;">
        <div class="card" style="padding:16px;">
          <div style="font-size:0.75rem; color:var(--slate-500); font-weight:700; text-transform:uppercase;">Total QR Issued</div>
          <div style="font-size:1.6rem; font-weight:900; color:var(--slate-900); margin-top:4px;">${analytics.totalIssued}</div>
          <div style="font-size:0.75rem; color:var(--slate-400); margin-top:2px;">Standalone & single-use codes</div>
        </div>

        <div class="card" style="padding:16px; border-left:4px solid var(--success-500);">
          <div style="font-size:0.75rem; color:var(--success-700); font-weight:700; text-transform:uppercase;">Active Unscanned</div>
          <div style="font-size:1.6rem; font-weight:900; color:var(--success-700); margin-top:4px;">${analytics.active}</div>
          <div style="font-size:0.75rem; color:var(--slate-500); margin-top:2px;">Ready for customer scanning</div>
        </div>

        <div class="card" style="padding:16px; border-left:4px solid var(--danger-500);">
          <div style="font-size:0.75rem; color:var(--danger-700); font-weight:700; text-transform:uppercase;">Redeemed & Expired</div>
          <div style="font-size:1.6rem; font-weight:900; color:var(--danger-700); margin-top:4px;">${analytics.redeemed}</div>
          <div style="font-size:0.75rem; color:var(--slate-500); margin-top:2px;">Auto-expired upon scan</div>
        </div>

        <div class="card" style="padding:16px; border-left:4px solid var(--primary-500);">
          <div style="font-size:0.75rem; color:var(--primary-700); font-weight:700; text-transform:uppercase;">Disbursed Savings</div>
          <div style="font-size:1.6rem; font-weight:900; color:var(--primary-700); margin-top:4px;">₹${analytics.totalSavingsDisbursed.toLocaleString('en-IN')}</div>
          <div style="font-size:0.75rem; color:var(--slate-500); margin-top:2px;">Total discounts claimed</div>
        </div>
      </div>

      <!-- Main Admin Data Table Card -->
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left">
            <div class="table-search-box">
              <svg class="table-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              <input type="text" class="table-search-input" placeholder="Search by unique code, phone number, title..." oninput="MastersView.filterGenericTable('admin-vouchers-table', this.value)">
            </div>
            <select class="table-filter-select" id="admin-qr-status-filter" onchange="QRView.filterAdminTable(this.value)">
              <option value="">All Statuses</option>
              <option value="Active">Active Only</option>
              <option value="Redeemed / Expired">Redeemed / Expired</option>
              <option value="Expired">Expired by Date</option>
            </select>
          </div>

          <div class="table-toolbar-right">
            <button class="btn btn-secondary btn-sm" onclick="QRView.switchTab('customer-portal')">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
              Open Customer Portal
            </button>
            <button class="btn btn-primary btn-sm" onclick="QRView.switchTab('discount')">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              + Create Single-Use QR
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table" id="admin-vouchers-table">
            <thead>
              <tr>
                <th>QR Thumbnail</th>
                <th>Unique Code</th>
                <th>Discount Value</th>
                <th>Campaign Title</th>
                <th>Usage Rule</th>
                <th>Live Status</th>
                <th>Redeemed By (Customer Phone)</th>
                <th>Scanned / Redeemed At</th>
                <th style="text-align:right;">Admin Actions</th>
              </tr>
            </thead>
            <tbody>
              ${coupons.map(c => {
                const isRedeemed = c.status === "Redeemed / Expired" || (c.timesScanned > 0);
                const isActive = c.status === "Active";

                return `
                  <tr id="voucher-row-${c.id}">
                    <td>
                      <div style="background:#ffffff; border:1px solid var(--slate-200); border-radius:6px; padding:3px; display:inline-block;">
                        ${QRManager.generateQRSVG(c.code, 44, c.color || '#0f172a')}
                      </div>
                    </td>
                    <td class="mono-cell font-bold font-mono">
                      <span onclick="QRManager.copyCouponCode('${c.code}')" style="cursor:pointer;" title="Click to copy code">
                        ${c.code}
                      </span>
                    </td>
                    <td>
                      <span class="badge ${c.amount >= 500 ? 'badge-success' : 'badge-primary'}" style="font-size:0.825rem; font-weight:800;">
                        ${c.type === 'percent' ? `${c.amount}% OFF` : `₹${c.amount} OFF`}
                      </span>
                    </td>
                    <td class="font-bold">${c.title}</td>
                    <td>
                      <span style="font-size:0.75rem; color:var(--slate-600); font-weight:600;">
                        ${c.usageType === 'multi' ? 'Multi-Use' : '⚡ Single-Use (Auto-Expire)'}
                      </span>
                    </td>
                    <td>
                      ${isActive ? `
                        <span class="badge badge-success" style="font-weight:700;">● Active (Unclaimed)</span>
                      ` : (isRedeemed ? `
                        <span class="badge badge-danger" style="font-weight:700;">● Redeemed / Expired</span>
                      ` : `
                        <span class="badge badge-secondary" style="font-weight:700;">● Expired (Date)</span>
                      `)}
                    </td>
                    <td>
                      ${c.redeemedByPhone ? `
                        <div style="font-weight:700; font-family:var(--font-mono); color:var(--slate-900);">
                          ${c.redeemedByPhone}
                        </div>
                        ${c.claimId ? `<div style="font-size:0.7rem; color:var(--primary-600);">${c.claimId}</div>` : ''}
                      ` : `
                        <span style="color:var(--slate-400); font-style:italic;">Not scanned yet</span>
                      `}
                    </td>
                    <td>
                      ${c.redeemedAt ? `
                        <div style="font-size:0.8rem; font-weight:600; color:var(--slate-700);">${c.redeemedAt}</div>
                      ` : `
                        <span style="color:var(--slate-400); font-size:0.75rem;">Valid till: ${c.validTill}</span>
                      `}
                    </td>
                    <td class="table-actions">
                      ${isActive ? `
                        <button class="table-action-btn view" title="Test Customer Scan" onclick="QRView.testCustomerScan('${c.code}')">Test Claim</button>
                        <button class="table-action-btn delete" title="Manually Expire" onclick="QRView.adminExpireCoupon('${c.id}')">Expire Now</button>
                      ` : `
                        <button class="table-action-btn edit" title="Reactivate Voucher" onclick="QRView.adminReactivateCoupon('${c.id}')">Reactivate</button>
                      `}
                      <button class="table-action-btn edit" title="Print Clean Voucher" onclick="QRManager.openPrintDiscountVoucherModal(${JSON.stringify(c).replace(/"/g, '&quot;')})">Print</button>
                      <button class="table-action-btn delete" title="Delete Voucher" onclick="QRView.deleteCoupon('${c.id}')">Delete</button>
                    </td>
                  </tr>
                `;
              }).join('')}
            </tbody>
          </table>
        </div>
      </div>
    `;
  },

  testCustomerScan(code) {
    this._customerState.selectedCode = code;
    this.switchTab("customer-portal");
  },

  adminExpireCoupon(id) {
    ERPState.expireCoupon(id);
    UI.showToast("Voucher Expired", "QR Code marked as Expired in admin ledger", "warning");
    this.switchTab("vouchers");
  },

  adminReactivateCoupon(id) {
    ERPState.reactivateCoupon(id);
    UI.showToast("Voucher Reactivated", "QR Code reset to Active status and ready for redemption", "success");
    this.switchTab("vouchers");
  },

  deleteCoupon(id) {
    ERPState.deleteDiscountCoupon(id);
    UI.showToast("Voucher Deleted", "QR Code removed from system", "info");
    this.switchTab("vouchers");
  },

  filterAdminTable(status) {
    const rows = document.querySelectorAll("#admin-vouchers-table tbody tr");
    rows.forEach(r => {
      if (!status) {
        r.style.display = "";
      } else {
        const text = r.innerText.toLowerCase();
        r.style.display = text.includes(status.toLowerCase()) ? "" : "none";
      }
    });
  },

  // =========================================================================
  // SECTION C: STANDALONE DISCOUNT GENERATOR VIEW
  // =========================================================================
  renderDiscountGeneratorView() {
    const cpn = this._currentDiscount;

    return `
      <div class="qr-layout-grid">
        <!-- Configuration Card -->
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="M9 9h.01"/><path d="M15 15h.01"/></svg>
                Discount QR Voucher Generator
              </div>
              <div class="card-subtitle">Generate standalone single-use QR vouchers that expire once scanned</div>
            </div>
          </div>

          <form id="discount-qr-form" onsubmit="return false;">
            <!-- Quick Preset Amount Buttons -->
            <div class="form-group" style="margin-bottom:16px;">
              <label class="form-label">Quick Amount Presets</label>
              <div class="quick-amount-container">
                <button type="button" class="amount-chip-btn ${cpn.amount === 50 && cpn.type === 'fixed' ? 'active' : ''}" onclick="QRView.setPresetDiscount(50, 'fixed')">₹50 OFF</button>
                <button type="button" class="amount-chip-btn ${cpn.amount === 100 && cpn.type === 'fixed' ? 'active' : ''}" onclick="QRView.setPresetDiscount(100, 'fixed')">₹100 OFF</button>
                <button type="button" class="amount-chip-btn ${cpn.amount === 200 && cpn.type === 'fixed' ? 'active' : ''}" onclick="QRView.setPresetDiscount(200, 'fixed')">₹200 OFF</button>
                <button type="button" class="amount-chip-btn ${cpn.amount === 500 && cpn.type === 'fixed' ? 'active' : ''}" onclick="QRView.setPresetDiscount(500, 'fixed')">₹500 OFF</button>
                <button type="button" class="amount-chip-btn ${cpn.amount === 1000 && cpn.type === 'fixed' ? 'active' : ''}" onclick="QRView.setPresetDiscount(1000, 'fixed')">₹1,000 OFF</button>
                <button type="button" class="amount-chip-btn ${cpn.amount === 2000 && cpn.type === 'fixed' ? 'active' : ''}" onclick="QRView.setPresetDiscount(2000, 'fixed')">₹2,000 OFF</button>
                <button type="button" class="amount-chip-btn ${cpn.amount === 10 && cpn.type === 'percent' ? 'active' : ''}" onclick="QRView.setPresetDiscount(10, 'percent')">10% OFF</button>
                <button type="button" class="amount-chip-btn ${cpn.amount === 20 && cpn.type === 'percent' ? 'active' : ''}" onclick="QRView.setPresetDiscount(20, 'percent')">20% OFF</button>
              </div>
            </div>

            <div class="form-grid">
              <!-- Discount Amount & Type -->
              <div class="form-group">
                <label class="form-label">Discount Amount (₹ / %) <span class="required-star">*</span></label>
                <input type="number" class="form-control font-bold" id="d-amount-input" value="${cpn.amount}" oninput="QRView.onDiscountFormChange()" placeholder="e.g. 500">
              </div>

              <div class="form-group">
                <label class="form-label">Discount Type</label>
                <select class="form-control" id="d-type-select" onchange="QRView.onDiscountFormChange()">
                  <option value="fixed" ${cpn.type === 'fixed' ? 'selected' : ''}>Flat Rupee (₹ Off)</option>
                  <option value="percent" ${cpn.type === 'percent' ? 'selected' : ''}>Percentage (% Off)</option>
                </select>
              </div>

              <!-- Single-Use Expiry Rule -->
              <div class="form-group col-span-2">
                <label class="form-label">Redemption & Expiry Policy <span class="required-star">*</span></label>
                <select class="form-control" id="d-usage-select" onchange="QRView.onDiscountFormChange()">
                  <option value="single" ${cpn.usageType === 'single' ? 'selected' : ''}>⚡ Single-Use (Auto-Expires Immediately Upon Customer Scan)</option>
                  <option value="multi" ${cpn.usageType === 'multi' ? 'selected' : ''}>Multi-Use (Expires only on validity date)</option>
                </select>
              </div>

              <!-- Coupon Code with Regenerate Button -->
              <div class="form-group col-span-2">
                <label class="form-label" style="display:flex; justify-content:space-between;">
                  <span>Unique Voucher / Coupon Code <span class="required-star">*</span></span>
                  <a href="javascript:void(0)" style="font-size:0.75rem; color:var(--primary-600); font-weight:700;" onclick="QRView.regenerateCouponCode()">🎲 Generate New Code</a>
                </label>
                <input type="text" class="form-control font-mono font-bold" id="d-code-input" value="${cpn.code}" oninput="QRView.onDiscountFormChange()" placeholder="e.g. SAVE500-A92B">
              </div>

              <!-- Offer Title -->
              <div class="form-group col-span-2">
                <label class="form-label">Offer / Campaign Title</label>
                <input type="text" class="form-control" id="d-title-input" value="${cpn.title}" oninput="QRView.onDiscountFormChange()" placeholder="e.g. Festival Special Discount">
              </div>

              <!-- Min Spend -->
              <div class="form-group">
                <label class="form-label">Min Bill Spend (₹)</label>
                <input type="number" class="form-control" id="d-minbill-input" value="${cpn.minBill}" oninput="QRView.onDiscountFormChange()" placeholder="0 for no minimum">
              </div>

              <!-- Valid Till -->
              <div class="form-group">
                <label class="form-label">Valid Till</label>
                <input type="date" class="form-control" id="d-valid-input" value="${cpn.validTill}" onchange="QRView.onDiscountFormChange()">
              </div>

              <!-- QR Color Theme Picker -->
              <div class="form-group col-span-2">
                <label class="form-label">QR Code Color Theme</label>
                <div class="qr-color-picker-grid">
                  <div class="qr-color-circle ${cpn.color === '#0f172a' ? 'active' : ''}" style="background:#0f172a;" onclick="QRView.setQRColor('#0f172a')" title="Jet Black"></div>
                  <div class="qr-color-circle ${cpn.color === '#4f46e5' ? 'active' : ''}" style="background:#4f46e5;" onclick="QRView.setQRColor('#4f46e5')" title="Royal Indigo"></div>
                  <div class="qr-color-circle ${cpn.color === '#059669' ? 'active' : ''}" style="background:#059669;" onclick="QRView.setQRColor('#059669')" title="Emerald Green"></div>
                  <div class="qr-color-circle ${cpn.color === '#7c3aed' ? 'active' : ''}" style="background:#7c3aed;" onclick="QRView.setQRColor('#7c3aed')" title="Cyber Violet"></div>
                  <div class="qr-color-circle ${cpn.color === '#e11d48' ? 'active' : ''}" style="background:#e11d48;" onclick="QRView.setQRColor('#e11d48')" title="Crimson Rose"></div>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div style="display:flex; flex-wrap:wrap; gap:10px; margin-top:24px;">
              <button type="button" class="btn btn-primary" onclick="QRView.saveDiscountCoupon()">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Save Single-Use Voucher
              </button>
              <button type="button" class="btn btn-secondary" onclick="QRManager.downloadPNG(QRView._currentDiscount.code, QRView._currentDiscount.color)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="15"/></svg>
                Download PNG QR
              </button>
              <button type="button" class="btn btn-secondary" onclick="QRManager.downloadSVG(QRView._currentDiscount.code, QRView._currentDiscount.color)">
                Download SVG
              </button>
              <button type="button" class="btn btn-secondary" onclick="QRManager.openPrintDiscountVoucherModal(QRView._currentDiscount)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Print Voucher Sheet
              </button>
            </div>
          </form>
        </div>

        <!-- Live Standalone Preview Card -->
        <div class="card" style="display:flex; flex-direction:column; align-items:center; justify-content:center; background:#f8fafc;">
          <div class="card-header" style="width:100%;">
            <div class="card-title">Live Standalone QR Preview</div>
            <div class="card-subtitle">Clean, pure single-use QR ready for customer scanning</div>
          </div>
          
          <div id="standalone-qr-live-box" style="width:100%; padding:16px 0;">
            ${QRManager.renderStandaloneDiscountQR(cpn, 200)}
          </div>

          <!-- Quick Test Button -->
          <div style="margin-top:8px;">
            <button type="button" class="btn btn-secondary btn-sm" style="color:var(--success-700); background:#dcfce7; border-color:#86efac;" onclick="QRView.testClaimGeneratedQR()">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
              Test Customer Claim with Phone
            </button>
          </div>
        </div>
      </div>
    `;
  },

  setPresetDiscount(amount, type) {
    this._currentDiscount.amount = amount;
    this._currentDiscount.type = type;
    this._currentDiscount.code = type === "percent" ? `SAVE${amount}PCT-${Math.random().toString(36).substring(2, 6).toUpperCase()}` : `SAVE${amount}-${Math.random().toString(36).substring(2, 6).toUpperCase()}`;
    this._currentDiscount.title = type === "percent" ? `Special ${amount}% Off Mega Sale` : `Special ₹${amount} Off Discount`;

    const contentArea = document.getElementById("qr-tab-content-area");
    if (contentArea) contentArea.innerHTML = this.renderDiscountGeneratorView();
  },

  regenerateCouponCode() {
    const amt = this._currentDiscount.amount || 500;
    const prefix = this._currentDiscount.type === "percent" ? `SAVE${amt}PCT` : `SAVE${amt}`;
    const code = `${prefix}-${Math.random().toString(36).substring(2, 6).toUpperCase()}`;
    this._currentDiscount.code = code;
    const input = document.getElementById("d-code-input");
    if (input) input.value = code;
    this.updateStandalonePreview();
  },

  setQRColor(hex) {
    this._currentDiscount.color = hex;
    document.querySelectorAll(".qr-color-circle").forEach(c => c.classList.remove("active"));
    const activeCircle = document.querySelector(`.qr-color-circle[style*="${hex}"]`);
    if (activeCircle) activeCircle.classList.add("active");
    this.updateStandalonePreview();
  },

  onDiscountFormChange() {
    this._currentDiscount.amount = Number(document.getElementById("d-amount-input")?.value || 100);
    this._currentDiscount.type = document.getElementById("d-type-select")?.value || "fixed";
    this._currentDiscount.usageType = document.getElementById("d-usage-select")?.value || "single";
    this._currentDiscount.code = (document.getElementById("d-code-input")?.value || "SAVE100").toUpperCase().trim();
    this._currentDiscount.title = document.getElementById("d-title-input")?.value || "Customer Discount";
    this._currentDiscount.minBill = Number(document.getElementById("d-minbill-input")?.value || 0);
    this._currentDiscount.validTill = document.getElementById("d-valid-input")?.value || "2026-09-30";
    this.updateStandalonePreview();
  },

  updateStandalonePreview() {
    const box = document.getElementById("standalone-qr-live-box");
    if (box) {
      box.innerHTML = QRManager.renderStandaloneDiscountQR(this._currentDiscount, 200);
    }
  },

  saveDiscountCoupon() {
    this.onDiscountFormChange();
    const newCoupon = ERPState.addDiscountCoupon(this._currentDiscount);
    UI.showToast("Discount QR Saved!", `Single-use code ${newCoupon.code} (₹${newCoupon.amount} Off) listed in Admin Ledger`, "success");
    this.switchTab("vouchers");
  },

  testClaimGeneratedQR() {
    this.onDiscountFormChange();
    this._customerState.selectedCode = this._currentDiscount.code;
    this.switchTab("customer-portal");
  },

  // =========================================================================
  // SECTION D: INDUSTRIAL LOT TRACKING VIEW (FACTORY BUNDLE TAGS)
  // =========================================================================
  renderIndustrialLotView() {
    const lots = ERPState.data.lots || [];
    const activeLot = lots[0] || {
      lotNo: "LOT-2026-00145",
      orderNo: "SO-2026-1045",
      customer: "ABC Fashion",
      product: "Men's Crew Neck T-Shirt",
      targetQty: 5000,
      currentProcess: "Stitching",
      qrCodeString: "GARMENT-LOT-2026-00145"
    };

    return `
      <div class="qr-layout-grid">
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">Industrial Factory QR Lot Tag</div>
              <div class="card-subtitle">Generate 4" x 3" bundle barcode labels for assembly line tracking</div>
            </div>
          </div>

          <form id="industrial-lot-form" onsubmit="return false;">
            <div class="form-group">
              <label class="form-label">Select Production Lot</label>
              <select class="form-control" id="lot-select-input" onchange="QRView.onLotSelect(this.value)">
                ${lots.map(l => `<option value="${l.lotNo}">${l.lotNo} - ${l.product} (${l.customer})</option>`).join('')}
              </select>
            </div>

            <div style="margin-top:20px; display:flex; gap:10px;">
              <button type="button" class="btn btn-primary" onclick="QRManager.openPrintLabelModal(document.getElementById('lot-select-input')?.value || '${activeLot.lotNo}')">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Print Thermal Lot Tag
              </button>
            </div>
          </form>
        </div>

        <div class="card" style="display:flex; justify-content:center; align-items:center; background:#f8fafc;">
          <div id="industrial-preview-box">
            ${QRManager.renderIndustrialLabel(activeLot)}
          </div>
        </div>
      </div>
    `;
  },

  onLotSelect(lotNo) {
    const lot = (ERPState.data.lots || []).find(l => l.lotNo === lotNo);
    const box = document.getElementById("industrial-preview-box");
    if (box && lot) {
      box.innerHTML = QRManager.renderIndustrialLabel(lot);
    }
  },

  // QR History route handler
  renderHistory() {
    return this.renderAdminVouchersManagement();
  }
};
