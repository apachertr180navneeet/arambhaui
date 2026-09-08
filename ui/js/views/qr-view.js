/* ==========================================================================
   QR MANAGEMENT VIEWS - DISCOUNT QR GENERATOR, CUSTOMER SCANNER & VOUCHERS
   GarmentERP - FashionWorks Pvt. Ltd.
   ========================================================================== */

const QRView = {
  activeTab: "discount", // 'discount' | 'scanner' | 'vouchers' | 'lot-label'

  // Current working discount form state
  _currentDiscount: {
    amount: 500,
    type: "fixed", // 'fixed' | 'percent'
    code: "SAVE500-A92B",
    title: "Special Customer Discount",
    minBill: 1000,
    validTill: "2026-09-30",
    color: "#0f172a"
  },

  // 1. MAIN QR VIEWPORT (DISCOUNT QR GENERATOR AS PRIMARY)
  renderGenerator() {
    return `
      <!-- Top Mode Navigation Tabs -->
      <div class="qr-tabs-nav">
        <button class="qr-tab-btn ${this.activeTab === 'discount' ? 'active' : ''}" onclick="QRView.switchTab('discount')">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
          Discount QR Generator
        </button>
        <button class="qr-tab-btn ${this.activeTab === 'scanner' ? 'active' : ''}" onclick="QRView.switchTab('scanner')">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><circle cx="12" cy="12" r="3"/></svg>
          Customer Scan & Redeem Simulator
        </button>
        <button class="qr-tab-btn ${this.activeTab === 'vouchers' ? 'active' : ''}" onclick="QRView.switchTab('vouchers')">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
          Active Vouchers Library (${(ERPState.data.discountCoupons || []).length})
        </button>
        <button class="qr-tab-btn ${this.activeTab === 'lot-label' ? 'active' : ''}" onclick="QRView.switchTab('lot-label')">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg>
          Factory Lot Tracking Tag
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
    // Update active tab buttons
    document.querySelectorAll(".qr-tab-btn").forEach(btn => btn.classList.remove("active"));
    const activeBtn = document.querySelector(`.qr-tab-btn[onclick*="${tabName}"]`);
    if (activeBtn) activeBtn.classList.add("active");
  },

  renderActiveTabContent() {
    if (this.activeTab === "scanner") {
      return this.renderScanner();
    } else if (this.activeTab === "vouchers") {
      return this.renderVouchersLibrary();
    } else if (this.activeTab === "lot-label") {
      return this.renderIndustrialLotView();
    }
    return this.renderDiscountGeneratorView();
  },

  // 1.1 DISCOUNT QR GENERATOR VIEW (STANDALONE, WITHOUT INDUSTRIAL LABELS)
  renderDiscountGeneratorView() {
    const cpn = this._currentDiscount;

    return `
      <div class="qr-layout-grid">
        <!-- Configuration Card -->
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="M9 9h.01"/><path d="M15 15h.01"/></svg>
                Discount & Amount QR Generator
              </div>
              <div class="card-subtitle">Generate pure standalone QR codes for customer discounts and offers</div>
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

              <!-- Coupon Code with Regenerate Button -->
              <div class="form-group col-span-2">
                <label class="form-label" style="display:flex; justify-content:space-between;">
                  <span>Voucher / Coupon Code <span class="required-star">*</span></span>
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
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Save to Vouchers
              </button>
              <button type="button" class="btn btn-secondary" onclick="QRManager.downloadPNG(QRView._currentDiscount.code, QRView._currentDiscount.color)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Download PNG QR
              </button>
              <button type="button" class="btn btn-secondary" onclick="QRManager.downloadSVG(QRView._currentDiscount.code, QRView._currentDiscount.color)">
                Download SVG
              </button>
              <button type="button" class="btn btn-secondary" onclick="QRManager.openPrintDiscountVoucherModal(QRView._currentDiscount)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Print Voucher
              </button>
            </div>
          </form>
        </div>

        <!-- Live Standalone Preview Card (WITHOUT ANY LABEL) -->
        <div class="card" style="display:flex; flex-direction:column; align-items:center; justify-content:center; background:#f8fafc;">
          <div class="card-header" style="width:100%;">
            <div class="card-title">Live Standalone QR Preview</div>
            <div class="card-subtitle">Clean, pure QR ready for customer scanning</div>
          </div>
          
          <div id="standalone-qr-live-box" style="width:100%; padding:16px 0;">
            ${QRManager.renderStandaloneDiscountQR(cpn, 200)}
          </div>

          <!-- Quick Scan Test Button -->
          <div style="margin-top:8px;">
            <button type="button" class="btn btn-secondary btn-sm" style="color:var(--success-700); background:#dcfce7; border-color:#86efac;" onclick="QRView.testScanCurrentQR()">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/></svg>
              Test Scan / Redeem This QR
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
    UI.showToast("Discount QR Saved!", `Coupon ${newCoupon.code} (₹${newCoupon.amount} Off) added to Active Vouchers`, "success");
    this.switchTab("vouchers");
  },

  testScanCurrentQR() {
    this.onDiscountFormChange();
    this.switchTab("scanner");
    setTimeout(() => {
      this.simulateDiscountScan(this._currentDiscount);
    }, 150);
  },

  // 1.2 CUSTOMER SCANNER & DISCOUNT REDEMPTION SIMULATOR
  renderScanner() {
    const coupons = ERPState.data.discountCoupons || [];

    return `
      <div style="max-width:860px; margin:0 auto;">
        <!-- Scanner Viewfinder Box -->
        <div class="scanner-viewport-wrapper">
          <div style="display:flex; align-items:center; gap:8px; color:#ffffff; font-weight:700; font-size:1.1rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><circle cx="12" cy="12" r="3"/></svg>
            Customer QR Discount Scanner & Simulator
          </div>
          <p style="color:#94a3b8; font-size:0.85rem; margin-top:4px;">Position customer discount QR code in front of the camera</p>

          <div class="scanner-viewfinder">
            <div class="scanner-corners"></div>
            <div class="scanner-laser"></div>
            <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="1.5"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
          </div>

          <!-- Quick Simulate Scan Buttons -->
          <div style="display:flex; flex-direction:column; align-items:center; gap:10px; width:100%;">
            <div style="font-size:0.8rem; color:#94a3b8; font-weight:700; letter-spacing:0.05em;">SIMULATE CUSTOMER SCANNING A DISCOUNT QR:</div>
            <div style="display:flex; flex-wrap:wrap; gap:8px; justify-content:center;">
              ${coupons.map(c => `
                <button class="btn btn-primary btn-sm" style="background:${c.color || '#4f46e5'}; border-color:${c.color || '#4f46e5'};" onclick="QRView.simulateDiscountScan('${c.code}')">
                  Scan: ${c.code} (₹${c.amount} Off)
                </button>
              `).join('')}
            </div>
          </div>
        </div>

        <!-- Real-time Decoded Discount Celebration Area -->
        <div id="decoded-discount-container"></div>
      </div>
    `;
  },

  simulateDiscountScan(codeOrObject) {
    let coupon = null;
    if (typeof codeOrObject === "object") {
      coupon = codeOrObject;
    } else {
      coupon = (ERPState.data.discountCoupons || []).find(c => c.code.toLowerCase() === String(codeOrObject).toLowerCase()) || {
        code: codeOrObject,
        amount: 500,
        type: "fixed",
        title: "Customer Discount Voucher",
        minBill: 1000,
        validTill: "2026-09-30"
      };
    }

    const container = document.getElementById("decoded-discount-container");
    if (!container) return;

    const discountAmount = coupon.type === "percent" ? `${coupon.amount}%` : `₹${coupon.amount}`;
    const sampleBill = 3500;
    const discountVal = coupon.type === "percent" ? (sampleBill * coupon.amount) / 100 : coupon.amount;
    const finalPayable = Math.max(0, sampleBill - discountVal);

    UI.showToast("🎉 QR Scanned Successfully!", `Applied ${discountAmount} OFF voucher (${coupon.code})`, "success");

    container.innerHTML = `
      <div class="discount-redeemed-card">
        <div class="discount-redeemed-header">
          <div>
            <div style="font-size:0.8rem; font-weight:800; color:#15803d; text-transform:uppercase; letter-spacing:0.05em;">
              ✨ DISCOUNT CODE VERIFIED & REDEEMED
            </div>
            <div class="discount-redeemed-amount">
              FLAT ${discountAmount} OFF
            </div>
            <div style="font-size:0.875rem; color:#475569; margin-top:2px;">
              ${coupon.title} • Voucher Code: <strong class="font-mono" style="color:#0f172a;">${coupon.code}</strong>
            </div>
          </div>
          <div style="text-align:right;">
            <span class="badge badge-success" style="font-size:0.85rem; padding:6px 12px;">ACTIVE & VALID</span>
          </div>
        </div>

        <!-- Sample Customer Bill Simulation -->
        <div class="discount-redeemed-bill-box">
          <div>
            <div style="font-size:0.75rem; color:#64748b; font-weight:600;">Original Order Bill</div>
            <div style="font-size:1.15rem; font-weight:800; font-family:var(--font-mono); color:#475569; text-decoration:line-through;">₹${sampleBill.toLocaleString('en-IN')}</div>
          </div>

          <div style="color:#15803d; font-weight:800; font-size:1.1rem;">
            - ₹${discountVal.toLocaleString('en-IN')} Discount
          </div>

          <div style="text-align:right;">
            <div style="font-size:0.75rem; color:#64748b; font-weight:600;">Customer Net Payable</div>
            <div style="font-size:1.4rem; font-weight:900; font-family:var(--font-mono); color:#15803d;">₹${finalPayable.toLocaleString('en-IN')}</div>
          </div>
        </div>

        <!-- Quick Apply Actions -->
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-top:16px;">
          <div style="font-size:0.8rem; color:#475569;">
            ${coupon.minBill ? `Condition: Minimum bill spend ₹${coupon.minBill.toLocaleString('en-IN')}` : 'No minimum spend requirement'}
          </div>
          <div style="display:flex; gap:10px;">
            <button class="btn btn-secondary btn-sm" onclick="QRManager.openPrintDiscountVoucherModal(${JSON.stringify(coupon).replace(/"/g, '&quot;')})">
              Print Voucher
            </button>
            <button class="btn btn-primary btn-sm" onclick="UI.showToast('Discount Applied to Bill', 'Order total reduced by ₹${discountVal}', 'success'); App.navigate('dispatch', 'dispatch');">
              Apply to Order Dispatch
            </button>
          </div>
        </div>
      </div>
    `;

    // Smooth scroll down to celebration card
    container.scrollIntoView({ behavior: "smooth", block: "nearest" });
  },

  // 1.3 ACTIVE DISCOUNT VOUCHERS LIBRARY
  renderVouchersLibrary() {
    const coupons = ERPState.data.discountCoupons || [];

    return `
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left">
            <h3 style="font-size:1.05rem;">Active Discount QR Vouchers (${coupons.length})</h3>
          </div>
          <div class="table-toolbar-right">
            <button class="btn btn-primary btn-sm" onclick="QRView.switchTab('discount')">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Create New Discount QR
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>QR Thumbnail</th>
                <th>Coupon Code</th>
                <th>Discount Amount</th>
                <th>Campaign Title</th>
                <th>Min Bill</th>
                <th>Valid Till</th>
                <th>Times Scanned</th>
                <th>Status</th>
                <th style="text-align:right;">Actions</th>
              </tr>
            </thead>
            <tbody>
              ${coupons.map(c => `
                <tr>
                  <td>
                    <div style="background:#ffffff; border:1px solid var(--slate-200); border-radius:6px; padding:4px; display:inline-block;">
                      ${QRManager.generateQRSVG(c.code, 48, c.color || '#0f172a')}
                    </div>
                  </td>
                  <td class="mono-cell font-bold font-mono">
                    <span onclick="QRManager.copyCouponCode('${c.code}')" style="cursor:pointer;" title="Click to copy">${c.code}</span>
                  </td>
                  <td>
                    <span class="badge ${c.amount >= 500 ? 'badge-success' : 'badge-primary'}" style="font-size:0.85rem; font-weight:800;">
                      ${c.type === 'percent' ? `${c.amount}% OFF` : `₹${c.amount} OFF`}
                    </span>
                  </td>
                  <td class="font-bold">${c.title}</td>
                  <td>${c.minBill ? `₹${c.minBill.toLocaleString('en-IN')}` : '<span style="color:var(--slate-400);">None</span>'}</td>
                  <td>${UI.formatDate(c.validTill)}</td>
                  <td class="font-mono font-bold">${c.timesScanned || 0} scans</td>
                  <td>${UI.formatStatusBadge(c.status || 'Active')}</td>
                  <td class="table-actions">
                    <button class="table-action-btn view" title="Test Scan" onclick="QRView.simulateDiscountScan('${c.code}')">Scan Test</button>
                    <button class="table-action-btn edit" title="Print Clean Voucher" onclick="QRManager.openPrintDiscountVoucherModal(${JSON.stringify(c).replace(/"/g, '&quot;')})">Print</button>
                    <button class="table-action-btn delete" title="Delete" onclick="QRView.deleteCoupon('${c.id}')">Delete</button>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      </div>
    `;
  },

  deleteCoupon(id) {
    if (confirm("Are you sure you want to delete this discount QR voucher?")) {
      ERPState.deleteDiscountCoupon(id);
      UI.showToast("Deleted", "Discount QR voucher removed", "info");
      this.switchTab("vouchers");
    }
  },

  // 1.4 INDUSTRIAL LOT VIEW (PRESERVED FOR FACTORY LOT TRACKING)
  renderIndustrialLotView() {
    const lots = ERPState.data.lots;
    const currentLot = lots[0];

    return `
      <div class="qr-layout-grid">
        <!-- Configuration Card -->
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">Industrial QR Lot Label Generator</div>
              <div class="card-subtitle">Factory floor tag for manufacturing bundles with barcode and lot details</div>
            </div>
          </div>

          <form id="qr-lot-generator-form">
            <div class="form-grid">
              <div class="form-group col-span-2">
                <label class="form-label">Select Production Lot to Encode <span class="required-star">*</span></label>
                <select class="form-control" id="qr-lot-select" onchange="QRView.updateLotPreview(this.value)">
                  ${lots.map(l => `<option value="${l.lotNo}">${l.lotNo} - ${l.product} (${l.customer})</option>`).join('')}
                </select>
              </div>

              <div class="form-group">
                <label class="form-label">Label Format</label>
                <select class="form-control">
                  <option>Industrial 2D QR + Barcode Tag</option>
                  <option>Micro 2D QR Only</option>
                </select>
              </div>

              <div class="form-group">
                <label class="form-label">Print Thermal Tag Size</label>
                <select class="form-control">
                  <option>4" x 3" Thermal Transfer Tag</option>
                  <option>2" x 1" Bundle Tag</option>
                </select>
              </div>
            </div>

            <div style="display:flex; gap:12px; margin-top:24px;">
              <button type="button" class="btn btn-primary" onclick="QRManager.openPrintLabelModal(document.getElementById('qr-lot-select').value)">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Print Industrial Tag
              </button>
            </div>
          </form>
        </div>

        <!-- Live Preview Card -->
        <div class="card" style="text-align:center;">
          <div class="card-header">
            <div class="card-title">Industrial Tag Preview</div>
          </div>
          <div id="qr-lot-preview-box">
            ${QRManager.renderIndustrialLabel(currentLot)}
          </div>
        </div>
      </div>
    `;
  },

  updateLotPreview(lotNo) {
    const lot = ERPState.data.lots.find(l => l.lotNo === lotNo);
    const box = document.getElementById("qr-lot-preview-box");
    if (lot && box) {
      box.innerHTML = QRManager.renderIndustrialLabel(lot);
    }
  },

  // QR History route handler
  renderHistory() {
    return this.renderVouchersLibrary();
  }
};

