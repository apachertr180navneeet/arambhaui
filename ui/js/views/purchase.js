/* ==========================================================================
   PURCHASE MANAGEMENT VIEW - PO GENERATOR, INWARDS (GRN) & RETURNS
   GarmentERP
   ========================================================================== */

const PurchaseView = {
  // 1. PURCHASE ORDERS LIST
  renderOrders() {
    const orders = ERPState.data.purchaseOrders;

    return `
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left">
            <div class="table-search-box">
              <svg class="table-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              <input type="text" class="table-search-input" placeholder="Search POs by number, vendor..." oninput="MastersView.filterGenericTable('po-table', this.value)">
            </div>
          </div>

          <div class="table-toolbar-right">
            <button class="btn btn-secondary btn-sm" onclick="PurchaseView.exportPOs()">Export CSV</button>
            <button class="btn btn-primary btn-sm" onclick="PurchaseView.openNewPOModal()">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Create Purchase Order
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table" id="po-table">
            <thead>
              <tr>
                <th>PO Number</th>
                <th>Vendor</th>
                <th>PO Date</th>
                <th>Items Ordered</th>
                <th>Total Qty</th>
                <th>Grand Total</th>
                <th>Expected Date</th>
                <th>Status</th>
                <th style="text-align:right;">Actions</th>
              </tr>
            </thead>
            <tbody>
              ${orders.map(po => {
                const totalQty = po.items.reduce((acc, it) => acc + it.qty, 0);
                return `
                  <tr>
                    <td class="mono-cell font-bold" style="color:var(--primary-600);">${po.id}</td>
                    <td class="primary-cell">${po.vendor}</td>
                    <td>${UI.formatDate(po.poDate)}</td>
                    <td>${po.items.map(i => i.item).join(', ')}</td>
                    <td class="font-mono">${totalQty.toLocaleString('en-IN')}</td>
                    <td class="font-bold font-mono">${UI.formatCurrency(po.grandTotal)}</td>
                    <td>${UI.formatDate(po.expectedDate)}</td>
                    <td>${UI.formatStatusBadge(po.status)}</td>
                    <td class="table-actions">
                      <button class="table-action-btn view" onclick="PurchaseView.openPrintPOModal('${po.id}')">Print PO</button>
                      ${po.status === 'Approved' ? `
                        <button class="table-action-btn edit" style="color:var(--success-600);" onclick="PurchaseView.openInwardFromPO('${po.id}')">Inward (GRN)</button>
                      ` : ''}
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

  // 2. PURCHASE INWARD (GRN) LIST
  renderInwards() {
    const inwards = ERPState.data.purchaseInwards;

    return `
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left">
            <div class="table-search-box">
              <svg class="table-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              <input type="text" class="table-search-input" placeholder="Search GRN by lot, vendor, item..." oninput="MastersView.filterGenericTable('grn-table', this.value)">
            </div>
          </div>

          <div class="table-toolbar-right">
            <button class="btn btn-primary btn-sm" onclick="PurchaseView.openCreateGRNModal()">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              New Goods Receipt (GRN)
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table" id="grn-table">
            <thead>
              <tr>
                <th>GRN Number</th>
                <th>PO Reference</th>
                <th>Vendor</th>
                <th>Received Date</th>
                <th>Item Received</th>
                <th>Ordered</th>
                <th>Received</th>
                <th>Accepted Qty</th>
                <th>Generated Lot No</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              ${inwards.map(grn => `
                <tr>
                  <td class="mono-cell font-bold" style="color:var(--primary-600);">${grn.id}</td>
                  <td class="mono-cell">${grn.poNumber}</td>
                  <td class="primary-cell">${grn.vendor}</td>
                  <td>${UI.formatDate(grn.receivedDate)}</td>
                  <td>${grn.item}</td>
                  <td class="font-mono">${grn.orderedQty.toLocaleString('en-IN')}</td>
                  <td class="font-mono">${grn.receivedQty.toLocaleString('en-IN')}</td>
                  <td class="font-mono font-bold" style="color:var(--success-700);">${grn.acceptedQty.toLocaleString('en-IN')}</td>
                  <td class="mono-cell font-bold">${grn.lotNumber}</td>
                  <td>${UI.formatStatusBadge(grn.status)}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      </div>
    `;
  },

  openNewPOModal() {
    const vendors = ERPState.data.vendors;
    const items = ERPState.data.items;

    const content = `
      <form id="new-po-form">
        <div class="form-grid" style="margin-bottom:16px;">
          <div class="form-group">
            <label class="form-label">Vendor Supplier <span class="required-star">*</span></label>
            <select class="form-control" id="po-vendor" required>
              ${vendors.map(v => `<option value="${v.name}" data-id="${v.id}">${v.name} (${v.category})</option>`).join('')}
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">PO Date</label>
            <input type="date" class="form-control" id="po-date" value="${new Date().toISOString().split('T')[0]}">
          </div>

          <div class="form-group">
            <label class="form-label">Expected Delivery Date <span class="required-star">*</span></label>
            <input type="date" class="form-control" id="po-expected" required value="${new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0]}">
          </div>

          <div class="form-group">
            <label class="form-label">Destination Warehouse</label>
            <select class="form-control" id="po-warehouse">
              ${ERPState.data.warehouses.map(w => `<option value="${w.name}">${w.name}</option>`).join('')}
            </select>
          </div>
        </div>

        <div class="card-title" style="font-size:0.95rem; margin-bottom:8px;">Order Line Items</div>
        <div class="line-items-wrapper">
          <table class="line-items-table" id="po-items-table">
            <thead>
              <tr>
                <th>Item / Material</th>
                <th style="width:100px;">Quantity</th>
                <th style="width:90px;">Unit</th>
                <th style="width:110px;">Rate (₹)</th>
                <th style="width:80px;">GST %</th>
                <th style="width:120px;">Amount</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <select class="form-control po-item-select" onchange="PurchaseView.recalcPOModal()">
                    ${items.map(i => `<option value="${i.name}" data-rate="${i.rate}" data-unit="${i.unit}" data-code="${i.code}">${i.name}</option>`).join('')}
                  </select>
                </td>
                <td><input type="number" class="form-control po-qty-input" value="3000" min="1" oninput="PurchaseView.recalcPOModal()"></td>
                <td><input type="text" class="form-control po-unit-input" value="Meters" readonly></td>
                <td><input type="number" class="form-control po-rate-input" value="145" oninput="PurchaseView.recalcPOModal()"></td>
                <td><input type="number" class="form-control po-tax-input" value="5" oninput="PurchaseView.recalcPOModal()"></td>
                <td class="font-mono font-bold po-row-total" style="vertical-align:middle;">₹4,56,750</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="order-summary-box">
          <div class="summary-row">
            <span>Items Subtotal:</span>
            <strong id="po-summary-subtotal">₹4,35,000</strong>
          </div>
          <div class="summary-row">
            <span>Estimated GST Tax (5%):</span>
            <strong id="po-summary-tax">₹21,750</strong>
          </div>
          <div class="summary-row total">
            <span>PO Grand Total:</span>
            <strong id="po-summary-grand" style="color:var(--primary-700);">₹4,56,750</strong>
          </div>
        </div>
      </form>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="UI.closeModal()">Cancel</button>
      <button class="btn btn-primary" id="btn-save-po">Create & Approve PO</button>
    `;

    UI.openModal({ title: "Create Purchase Order", content, footer, size: "modal-xl" });

    document.getElementById("btn-save-po").onclick = () => {
      const vendorSelect = document.getElementById("po-vendor");
      const vendor = vendorSelect.value;
      const vendorId = vendorSelect.options[vendorSelect.selectedIndex].getAttribute("data-id");

      const itemSelect = document.querySelector(".po-item-select");
      const item = itemSelect.value;
      const code = itemSelect.options[itemSelect.selectedIndex].getAttribute("data-code");
      const qty = Number(document.querySelector(".po-qty-input").value);
      const unit = document.querySelector(".po-unit-input").value;
      const rate = Number(document.querySelector(".po-rate-input").value);
      const tax = Number(document.querySelector(".po-tax-input").value);

      const payload = {
        vendor,
        vendorId,
        poDate: document.getElementById("po-date").value,
        expectedDate: document.getElementById("po-expected").value,
        warehouse: document.getElementById("po-warehouse").value,
        items: [{ item, code, qty, unit, rate, tax }]
      };

      const newPO = ERPState.createPurchaseOrder(payload);
      UI.showToast("Purchase Order Created", `PO ${newPO.id} generated for ${vendor}`, "success");
      UI.closeModal();
      App.refreshCurrentView();
    };
  },

  recalcPOModal() {
    const itemSelect = document.querySelector(".po-item-select");
    const opt = itemSelect.options[itemSelect.selectedIndex];
    const unitInput = document.querySelector(".po-unit-input");
    const rateInput = document.querySelector(".po-rate-input");
    const qtyInput = document.querySelector(".po-qty-input");
    const taxInput = document.querySelector(".po-tax-input");

    if (unitInput && opt.getAttribute("data-unit")) unitInput.value = opt.getAttribute("data-unit");
    if (rateInput && opt.getAttribute("data-rate") && rateInput.value === "145") rateInput.value = opt.getAttribute("data-rate");

    const qty = Number(qtyInput.value || 0);
    const rate = Number(rateInput.value || 0);
    const tax = Number(taxInput.value || 5);

    const subtotal = qty * rate;
    const taxAmount = (subtotal * tax) / 100;
    const total = subtotal + taxAmount;

    const rowTotal = document.querySelector(".po-row-total");
    if (rowTotal) rowTotal.innerText = UI.formatCurrency(total);

    const subEl = document.getElementById("po-summary-subtotal");
    const taxEl = document.getElementById("po-summary-tax");
    const grandEl = document.getElementById("po-summary-grand");

    if (subEl) subEl.innerText = UI.formatCurrency(subtotal);
    if (taxEl) taxEl.innerText = UI.formatCurrency(taxAmount);
    if (grandEl) grandEl.innerText = UI.formatCurrency(total);
  },

  openPrintPOModal(poId) {
    const po = ERPState.data.purchaseOrders.find(p => p.id === poId);
    if (!po) return;

    const content = `
      <div class="print-document-container" id="printable-po">
        <div class="print-header-grid">
          <div>
            <h2 class="print-doc-title">PURCHASE ORDER</h2>
            <div style="font-weight:700; color:var(--primary-700); font-family:var(--font-mono); font-size:1.1rem;">${po.id}</div>
            <div style="font-size:0.85rem; color:var(--slate-500); margin-top:4px;">Date: ${UI.formatDate(po.poDate)}</div>
          </div>
          <div style="text-align:right;">
            <h3 style="font-size:1.1rem; color:var(--slate-900);">FashionWorks Pvt. Ltd.</h3>
            <p style="font-size:0.775rem; color:var(--slate-600); max-width:280px; margin-top:2px;">
              ${ERPState.data.company.address}<br>
              GSTIN: ${ERPState.data.company.gstin}
            </p>
          </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px; font-size:0.85rem;">
          <div style="padding:12px; border:1px solid var(--slate-200); border-radius:var(--radius-md);">
            <div style="font-size:0.7rem; font-weight:700; color:var(--slate-500); text-transform:uppercase;">Vendor / Supplier</div>
            <div style="font-weight:800; font-size:1rem; color:var(--slate-900); margin-top:2px;">${po.vendor}</div>
            <div style="color:var(--slate-600); margin-top:4px;">Delivery Warehouse: ${po.warehouse}</div>
            <div style="color:var(--slate-600);">Expected Delivery: ${UI.formatDate(po.expectedDate)}</div>
          </div>

          <div style="padding:12px; border:1px solid var(--slate-200); border-radius:var(--radius-md);">
            <div style="font-size:0.7rem; font-weight:700; color:var(--slate-500); text-transform:uppercase;">Status & Terms</div>
            <div style="margin-top:4px;">PO Status: ${UI.formatStatusBadge(po.status)}</div>
            <div style="color:var(--slate-600); margin-top:4px;">Payment Status: ${po.paymentStatus}</div>
          </div>
        </div>

        <table class="print-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Item Description</th>
              <th>Item Code</th>
              <th>Quantity</th>
              <th>Rate (₹)</th>
              <th>GST %</th>
              <th style="text-align:right;">Amount (₹)</th>
            </tr>
          </thead>
          <tbody>
            ${po.items.map((it, idx) => `
              <tr>
                <td>${idx + 1}</td>
                <td><strong>${it.item}</strong></td>
                <td class="font-mono">${it.code}</td>
                <td class="font-mono">${it.qty.toLocaleString('en-IN')} ${it.unit}</td>
                <td class="font-mono">₹${it.rate}</td>
                <td class="font-mono">${it.tax}%</td>
                <td class="font-mono font-bold" style="text-align:right;">${UI.formatCurrency(it.amount)}</td>
              </tr>
            `).join('')}
          </tbody>
        </table>

        <div style="display:flex; justify-content:flex-end; margin-top:16px;">
          <div style="width:300px; display:flex; flex-direction:column; gap:6px; font-size:0.9rem;">
            <div style="display:flex; justify-content:space-between;"><span>Subtotal:</span> <strong>${UI.formatCurrency(po.subtotal)}</strong></div>
            <div style="display:flex; justify-content:space-between;"><span>Total Tax:</span> <strong>${UI.formatCurrency(po.taxTotal)}</strong></div>
            <div style="display:flex; justify-content:space-between; border-top:2px solid var(--slate-900); padding-top:6px; font-size:1.1rem; font-weight:800;">
              <span>Grand Total:</span>
              <span style="color:var(--primary-700);">${UI.formatCurrency(po.grandTotal)}</span>
            </div>
          </div>
        </div>
      </div>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="UI.closeModal()">Close</button>
      <button class="btn btn-primary" onclick="window.print(); UI.showToast('Printing PO', 'Document sent to printer', 'info');">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
        Print Purchase Order
      </button>
    `;

    UI.openModal({ title: `Purchase Order - ${po.id}`, content, footer, size: "modal-xl" });
  },

  openInwardFromPO(poId) {
    const po = ERPState.data.purchaseOrders.find(p => p.id === poId);
    if (!po) return;
    this.openCreateGRNModal(po);
  },

  openCreateGRNModal(prefilledPO = null) {
    const pos = ERPState.data.purchaseOrders;
    const selectedPO = prefilledPO || pos[0];
    const defaultItem = selectedPO ? selectedPO.items[0] : ERPState.data.items[0];

    const content = `
      <form id="grn-form">
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Linked Purchase Order</label>
            <select class="form-control" id="grn-po-select" onchange="PurchaseView.syncGRNFields(this.value)">
              ${pos.map(p => `<option value="${p.id}" ${selectedPO && selectedPO.id === p.id ? 'selected' : ''}>${p.id} - ${p.vendor}</option>`).join('')}
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Vendor</label>
            <input type="text" class="form-control" id="grn-vendor" readonly value="${selectedPO ? selectedPO.vendor : 'Shree Fabrics'}">
          </div>

          <div class="form-group">
            <label class="form-label">Received Date</label>
            <input type="date" class="form-control" id="grn-date" value="${new Date().toISOString().split('T')[0]}">
          </div>

          <div class="form-group">
            <label class="form-label">Receiving Warehouse</label>
            <select class="form-control" id="grn-wh">
              ${ERPState.data.warehouses.map(w => `<option value="${w.name}">${w.name}</option>`).join('')}
            </select>
          </div>

          <div class="form-group col-span-2">
            <label class="form-label">Item / Fabric Received</label>
            <input type="text" class="form-control" id="grn-item" readonly value="${defaultItem ? defaultItem.item : '100% Combed Cotton Fabric 180 GSM'}">
          </div>

          <div class="form-group">
            <label class="form-label">Ordered Qty</label>
            <input type="number" class="form-control" id="grn-ordered-qty" readonly value="${defaultItem ? defaultItem.qty : 5000}">
          </div>

          <div class="form-group">
            <label class="form-label">Physically Received Qty <span class="required-star">*</span></label>
            <input type="number" class="form-control" id="grn-received-qty" required value="${defaultItem ? defaultItem.qty : 5000}">
          </div>

          <div class="form-group">
            <label class="form-label">Rejected / Defective Qty</label>
            <input type="number" class="form-control" id="grn-rejected-qty" value="0">
          </div>

          <div class="form-group">
            <label class="form-label">Generated Lot Tag</label>
            <input type="text" class="form-control font-mono" id="grn-lot" readonly value="RAW-LOT-${Date.now().toString().slice(-6)}">
          </div>
        </div>
      </form>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="UI.closeModal()">Cancel</button>
      <button class="btn btn-primary" id="btn-save-grn">Accept & Update Raw Stock</button>
    `;

    UI.openModal({ title: "Generate Goods Receipt Note (GRN)", content, footer, size: "modal-lg" });

    document.getElementById("btn-save-grn").onclick = () => {
      const poNum = document.getElementById("grn-po-select").value;
      const vendor = document.getElementById("grn-vendor").value;
      const item = document.getElementById("grn-item").value;
      const orderedQty = Number(document.getElementById("grn-ordered-qty").value);
      const receivedQty = Number(document.getElementById("grn-received-qty").value);
      const rejectedQty = Number(document.getElementById("grn-rejected-qty").value);

      if (receivedQty <= 0) return UI.showToast("Invalid Quantity", "Received quantity must be greater than 0", "error");

      const payload = {
        poNumber: poNum,
        vendor,
        item,
        orderedQty,
        receivedQty,
        rejectedQty,
        warehouse: document.getElementById("grn-wh").value,
        receivedDate: document.getElementById("grn-date").value
      };

      const newGRN = ERPState.createPurchaseInward(payload);
      UI.showToast("Stock Inward Completed", `GRN ${newGRN.id} accepted. Raw material stock increased!`, "success");
      UI.closeModal();
      App.refreshCurrentView();
    };
  },

  syncGRNFields(poId) {
    const po = ERPState.data.purchaseOrders.find(p => p.id === poId);
    if (po && po.items.length > 0) {
      document.getElementById("grn-vendor").value = po.vendor;
      document.getElementById("grn-item").value = po.items[0].item;
      document.getElementById("grn-ordered-qty").value = po.items[0].qty;
      document.getElementById("grn-received-qty").value = po.items[0].qty;
    }
  },

  exportPOs() {
    const headers = ["PO Number", "Vendor", "PO Date", "Expected Date", "Subtotal", "Tax Total", "Grand Total", "Status"];
    const rows = ERPState.data.purchaseOrders.map(p => [p.id, p.vendor, p.poDate, p.expectedDate, p.subtotal, p.taxTotal, p.grandTotal, p.status]);
    UI.exportToCSV("Purchase_Orders_Report", headers, rows);
  }
};
