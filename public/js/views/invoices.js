/* ==========================================================================
   CUSTOMER SALES INVOICES VIEW - BILLING, GST TAX INVOICES & PAYMENTS
   GarmentERP
   ========================================================================== */

const InvoicesView = {
  _currentInvoiceItems: [],
  _itemCounter: 1,

  render(submodule = "list") {
    if (submodule === "create") {
      setTimeout(() => this.openCreateInvoiceModal(), 50);
    }
    return this.renderList();
  },

  renderList() {
    const invoices = ERPState.data.invoices || [];
    const totalInvoiced = invoices.reduce((acc, i) => acc + Number(i.amount || 0), 0);
    const totalCollected = invoices.reduce((acc, i) => acc + Number(i.paidAmount || 0), 0);
    const totalOutstanding = invoices.reduce((acc, i) => acc + Number(i.balanceAmount !== undefined ? i.balanceAmount : (i.amount - (i.paidAmount || 0))), 0);
    const unpaidCount = invoices.filter(i => i.status === "Unpaid" || i.status === "Partially Paid").length;
    const customers = ERPState.data.customers || [];

    return `
      <!-- Top Greetings & Quick Create -->
      <div class="dashboard-top-bar" style="margin-bottom:20px;">
        <div class="dashboard-title-wrap">
          <h1>
            Customer Sales Invoices
            <span style="font-size:0.75rem; font-weight:600; padding:2px 8px; background:var(--primary-100); color:var(--primary-700); border-radius:var(--radius-full); vertical-align:middle;">GST BILLING</span>
          </h1>
          <p class="dashboard-subtitle">Generate, track, settle, and print professional GST Tax Invoices for your customers.</p>
        </div>

        <div class="dashboard-controls">
          <button class="btn btn-primary btn-sm" onclick="InvoicesView.openCreateInvoiceModal()">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Create Sales Invoice
          </button>
        </div>
      </div>

      <!-- 4 Financial Summary KPI Cards -->
      <div class="kpi-grid" style="margin-bottom:24px;">
        <div class="kpi-card blue">
          <div class="kpi-top">
            <span class="kpi-title">Total Invoiced</span>
            <div class="kpi-icon-wrap blue">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
          </div>
          <div class="kpi-value">${UI.formatCurrency(totalInvoiced)}</div>
          <div class="kpi-bottom">
            <span class="kpi-trend up">${invoices.length} Total Invoices</span>
            <span class="kpi-period">All Time</span>
          </div>
        </div>

        <div class="kpi-card emerald">
          <div class="kpi-top">
            <span class="kpi-title">Total Collected</span>
            <div class="kpi-icon-wrap emerald">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
          </div>
          <div class="kpi-value">${UI.formatCurrency(totalCollected)}</div>
          <div class="kpi-bottom">
            <span class="kpi-trend up">Bank & UPI</span>
            <span class="kpi-period">Settled</span>
          </div>
        </div>

        <div class="kpi-card rose">
          <div class="kpi-top">
            <span class="kpi-title">Total Outstanding Due</span>
            <div class="kpi-icon-wrap rose">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
          </div>
          <div class="kpi-value" style="color:var(--danger-600);">${UI.formatCurrency(totalOutstanding)}</div>
          <div class="kpi-bottom">
            <span class="kpi-trend down">${unpaidCount} Pending Invoices</span>
            <span class="kpi-period">Receivables</span>
          </div>
        </div>

        <div class="kpi-card purple">
          <div class="kpi-top">
            <span class="kpi-title">Active Customers</span>
            <div class="kpi-icon-wrap purple">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
          </div>
          <div class="kpi-value">${customers.length} Accounts</div>
          <div class="kpi-bottom">
            <span class="kpi-trend up">GST Registered</span>
            <span class="kpi-period">Verified</span>
          </div>
        </div>
      </div>

      <!-- Main Invoices Table Card -->
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
            <!-- Live Search -->
            <div class="table-search-box" style="min-width:260px;">
              <svg class="table-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              <input type="text" class="table-search-input" placeholder="Search invoice no, customer, ref..." oninput="InvoicesView.filterInvoicesTable()">
            </div>

            <!-- Customer Filter Dropdown -->
            <select class="form-control form-control-sm" id="invoice-filter-customer" style="width:200px;" onchange="InvoicesView.filterInvoicesTable()">
              <option value="">All Customers</option>
              ${customers.map(c => `<option value="${c.name}">${c.name}</option>`).join('')}
            </select>

            <!-- Status Filter -->
            <select class="form-control form-control-sm" id="invoice-filter-status" style="width:150px;" onchange="InvoicesView.filterInvoicesTable()">
              <option value="">All Statuses</option>
              <option value="Unpaid">Unpaid</option>
              <option value="Partially Paid">Partially Paid</option>
              <option value="Paid">Paid</option>
            </select>
          </div>

          <div class="table-toolbar-right">
            <button class="btn btn-primary btn-sm" onclick="InvoicesView.openCreateInvoiceModal()">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              + Create Sales Invoice
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table" id="invoices-main-table">
            <thead>
              <tr>
                <th>INVOICE NO.</th>
                <th>CUSTOMER NAME</th>
                <th>INVOICE DATE</th>
                <th>DUE DATE</th>
                <th>ORDER / DISPATCH REF</th>
                <th>INVOICE AMOUNT (₹)</th>
                <th>PAID (₹)</th>
                <th>BALANCE DUE (₹)</th>
                <th>STATUS</th>
                <th style="text-align:right;">ACTIONS</th>
              </tr>
            </thead>
            <tbody>
              ${invoices.length === 0 ? `
                <tr>
                  <td colspan="10" style="text-align:center; padding:32px 20px; color:var(--slate-400);">
                    <div style="font-size:1rem; font-weight:600; color:var(--slate-600); margin-bottom:4px;">No Sales Invoices Generated</div>
                    <div style="font-size:0.825rem;">Click <strong>+ Create Sales Invoice</strong> above to raise a GST invoice for completed orders.</div>
                  </td>
                </tr>
              ` : invoices.map(inv => {
                const bal = Number(inv.balanceAmount !== undefined ? inv.balanceAmount : (Number(inv.amount || inv.grandTotal || 0) - (inv.paidAmount || 0)));
                const paid = Number(inv.paidAmount || 0);
                const statusClass = inv.status === 'Paid' ? 'badge-success' : (inv.status === 'Partially Paid' ? 'badge-warning' : 'badge-danger');

                return `
                  <tr data-customer="${(inv.customer || '').toLowerCase()}" data-status="${inv.status}">
                    <td class="mono-cell font-bold" style="color:var(--primary-600); font-size:0.95rem;">${inv.invoiceNo || inv.id}</td>
                    <td class="primary-cell">
                      <div class="font-bold">${inv.customer}</div>
                      <span style="font-size:0.75rem; color:var(--slate-500);">${inv.companyName || ''}</span>
                    </td>
                    <td>${UI.formatDate(inv.invoiceDate || inv.date)}</td>
                    <td class="font-mono">${UI.formatDate(inv.dueDate)}</td>
                    <td class="mono-cell font-bold" style="color:var(--slate-600);">${inv.dispatchRef || inv.orderNo || '-'}</td>
                    <td class="font-bold font-mono">${UI.formatCurrency(inv.amount || inv.grandTotal || 0)}</td>
                    <td class="font-mono" style="color:var(--success-700); font-weight:600;">${UI.formatCurrency(paid)}</td>
                    <td class="font-bold font-mono" style="color:${bal > 0 ? 'var(--danger-600)' : 'var(--success-700)'};">
                      ${UI.formatCurrency(bal)}
                    </td>
                    <td>
                      <span class="badge ${statusClass}">
                        <span class="badge-dot"></span>${inv.status || 'Sent'}
                      </span>
                    </td>
                    <td class="table-actions" style="text-align:right;">
                      <button class="table-action-btn view" title="Print GST Tax Invoice" onclick="InvoicesView.openPrintInvoiceModal('${inv.invoiceNo || inv.id}')">
                        Print Invoice
                      </button>
                      ${bal > 0 ? `
                        <button class="table-action-btn" style="color:var(--success-700); background:#f0fdf4;" title="Record Customer Payment" onclick="InvoicesView.openRecordPaymentModal('${inv.invoiceNo || inv.id}')">
                          Pay ₹
                        </button>
                      ` : ''}
                      <button class="table-action-btn delete" title="Delete Invoice" onclick="InvoicesView.deleteInvoice('${inv.invoiceNo || inv.id}')">
                        ✕
                      </button>
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

  filterInvoicesTable() {
    const searchVal = document.querySelector(".table-search-input")?.value.toLowerCase() || "";
    const customerVal = document.getElementById("invoice-filter-customer")?.value.toLowerCase() || "";
    const statusVal = document.getElementById("invoice-filter-status")?.value || "";

    const rows = document.querySelectorAll("#invoices-main-table tbody tr");
    rows.forEach(r => {
      const text = r.textContent.toLowerCase();
      const rowCust = r.getAttribute("data-customer") || "";
      const rowStatus = r.getAttribute("data-status") || "";

      const matchSearch = text.includes(searchVal);
      const matchCust = !customerVal || rowCust.includes(customerVal);
      const matchStatus = !statusVal || rowStatus === statusVal;

      r.style.display = (matchSearch && matchCust && matchStatus) ? "" : "none";
    });
  },

  // --- CREATE CUSTOMER SALES INVOICE MODAL ---
  openCreateInvoiceModal(defaultCustomerName = null) {
    const customers = ERPState.data.customers || [];
    const items = ERPState.data.items || [];
    const lots = ERPState.data.lots || [];
    const dispatches = ERPState.data.dispatches || [];
    const nextInvNo = `INV-2026-${String((ERPState.data.invoices || []).length + 925).padStart(4, '0')}`;
    const today = new Date().toISOString().split('T')[0];
    const dueDateDefault = new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];

    // Reset line items
    this._currentInvoiceItems = [];
    this._itemCounter = 1;

    // Prefill one default sample item for fast creation
    this._currentInvoiceItems.push({
      item: items[0]?.name || "100% Combed Cotton Fabric 180 GSM",
      hsn: "5208",
      lotNo: lots[0]?.lotNo || "LOT-2026-00145",
      baleNo: "BALE-01",
      qty: 2500,
      unit: "Meters",
      rate: items[0]?.rate || 145,
      discount: 0,
      taxableAmount: 362500,
      gstRate: 18,
      gstAmount: 65250,
      totalAmount: 427750
    });

    const content = `
      <form id="create-customer-invoice-form" class="dispatch-form-card" onsubmit="return false;">
        <!-- Top Title Bar -->
        <div class="dispatch-header-bar">
          <div class="dispatch-header-title">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle; margin-right:6px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            Create Customer Sales Tax Invoice
          </div>
          <button type="button" class="dispatch-btn-back" onclick="UI.closeModal()">Back</button>
        </div>

        <!-- ROW 1: INVOICE NO, DATE, DUE DATE, PAYMENT TERMS -->
        <div class="dispatch-header-row-3" style="grid-template-columns: 1fr 1fr 1fr 1fr; gap:14px;">
          <div class="dispatch-field-group">
            <label class="dispatch-field-label">TAX INVOICE NO.</label>
            <input type="text" class="dispatch-input-styled readonly-bg" id="inv-field-no" value="${nextInvNo}">
          </div>

          <div class="dispatch-field-group">
            <label class="dispatch-field-label">INVOICE DATE <span class="required-star">*</span></label>
            <input type="date" class="dispatch-input-styled" id="inv-field-date" value="${today}">
          </div>

          <div class="dispatch-field-group">
            <label class="dispatch-field-label">DUE DATE</label>
            <input type="date" class="dispatch-input-styled" id="inv-field-duedate" value="${dueDateDefault}">
          </div>

          <div class="dispatch-field-group">
            <label class="dispatch-field-label">PAYMENT TERMS</label>
            <select class="dispatch-input-styled" id="inv-field-terms">
              <option value="Immediate">Immediate / Advance</option>
              <option value="15 Days">15 Days Net</option>
              <option value="30 Days" selected>30 Days Net</option>
              <option value="45 Days">45 Days Net</option>
              <option value="60 Days">60 Days Net</option>
            </select>
          </div>
        </div>

        <!-- ROW 2: CUSTOMER SELECTION & DISPATCH LINK -->
        <div class="dispatch-header-row-2" style="margin-top:14px; grid-template-columns: 1.5fr 1fr;">
          <div class="dispatch-field-group">
            <label class="dispatch-field-label">CUSTOMER (CONSIGNEE) <span class="required-star">*</span></label>
            <select class="dispatch-input-styled" id="inv-field-customer" onchange="InvoicesView.onCustomerSelect(this.value)">
              <option value="">Select customer</option>
              ${customers.map(c => `<option value="${c.name}" ${defaultCustomerName && defaultCustomerName === c.name ? 'selected' : ''}>${c.name} (${c.city}, ${c.state})</option>`).join('')}
            </select>
          </div>

          <div class="dispatch-field-group">
            <label class="dispatch-field-label">DISPATCH / ORDER REF NO.</label>
            <select class="dispatch-input-styled" id="inv-field-dispatch-ref">
              <option value="N/A">Direct Billing / No Dispatch Ref</option>
              ${dispatches.map(d => `<option value="${d.orderDispatchNo || d.id}">Dispatch #${d.orderDispatchNo || d.id} (${d.customer})</option>`).join('')}
            </select>
          </div>
        </div>

        <!-- DYNAMIC CUSTOMER DETAILS PREVIEW CARD -->
        <div id="inv-customer-details-card" style="margin:14px 0 20px 0; padding:12px 16px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; font-size:0.825rem;">
          <div>
            <div style="font-weight:700; color:#475569; font-size:0.725rem; text-transform:uppercase;">Billing Address</div>
            <div id="inv-preview-address" style="color:#0f172a; margin-top:2px;">Select a customer to view address</div>
          </div>
          <div>
            <div style="font-weight:700; color:#475569; font-size:0.725rem; text-transform:uppercase;">GSTIN / PAN</div>
            <div id="inv-preview-gstin" class="font-mono" style="color:#2563eb; font-weight:700; margin-top:2px;">-</div>
          </div>
          <div>
            <div style="font-weight:700; color:#475569; font-size:0.725rem; text-transform:uppercase;">Place of Supply / State</div>
            <div id="inv-preview-state" style="color:#0f172a; font-weight:600; margin-top:2px;">-</div>
          </div>
        </div>

        <!-- LINE ITEMS DETAILS SECTION -->
        <div class="dispatch-items-section">
          <div class="dispatch-section-title">Invoice Line Items & GST Rates</div>

          <!-- INPUT ROW -->
          <div style="display:grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr 1fr; gap:10px; margin-bottom:12px;">
            <div class="dispatch-field-group">
              <label class="dispatch-field-label">ITEM / FABRIC DESCRIPTION</label>
              <select class="dispatch-input-styled" id="inv-line-item" onchange="InvoicesView.onItemChange(this)">
                <option value="">Select Item</option>
                ${items.map(i => `<option value="${i.name}" data-rate="${i.rate || 145}" data-hsn="5208">${i.name}</option>`).join('')}
              </select>
            </div>

            <div class="dispatch-field-group">
              <label class="dispatch-field-label">HSN CODE</label>
              <input type="text" class="dispatch-input-styled font-mono" id="inv-line-hsn" placeholder="5208" value="5208">
            </div>

            <div class="dispatch-field-group">
              <label class="dispatch-field-label">LOT / BALE NO</label>
              <input type="text" class="dispatch-input-styled font-mono" id="inv-line-lot" placeholder="LOT-001" value="LOT-2026-00145">
            </div>

            <div class="dispatch-field-group">
              <label class="dispatch-field-label">QTY (METERS)</label>
              <input type="number" class="dispatch-input-styled font-mono" id="inv-line-qty" placeholder="Qty" value="1000" oninput="InvoicesView.recalcLineItem()">
            </div>

            <div class="dispatch-field-group">
              <label class="dispatch-field-label">RATE (₹)</label>
              <input type="number" class="dispatch-input-styled font-mono" id="inv-line-rate" placeholder="Rate" value="145" oninput="InvoicesView.recalcLineItem()">
            </div>

            <div class="dispatch-field-group">
              <label class="dispatch-field-label">GST RATE</label>
              <select class="dispatch-input-styled" id="inv-line-gstrate" onchange="InvoicesView.recalcLineItem()">
                <option value="5">5% GST</option>
                <option value="12">12% GST</option>
                <option value="18" selected>18% GST</option>
                <option value="28">28% GST</option>
              </select>
            </div>

            <div class="dispatch-field-group">
              <label class="dispatch-field-label">TOTAL (₹)</label>
              <input type="number" class="dispatch-input-styled readonly-bg font-mono" id="inv-line-calc-total" placeholder="Total" readonly>
            </div>
          </div>

          <!-- Add Item Button -->
          <div style="margin-bottom:18px;">
            <button type="button" class="btn-add-item-purple" onclick="InvoicesView.addLineItem()">
              + Add Item to Invoice
            </button>
          </div>

          <!-- Items Table Container -->
          <div id="inv-items-table-container">
            ${this.renderInvoiceItemsTable()}
          </div>
        </div>

        <!-- INVOICE NOTES & TERMS -->
        <div style="margin-top:16px;">
          <label class="dispatch-field-label">INVOICE REMARKS / TERMS</label>
          <input type="text" class="dispatch-input-styled" id="inv-field-notes" value="Subject to Mumbai jurisdiction. Goods once sold will not be taken back. Interest @ 18% p.a. charged after due date.">
        </div>

        <!-- BOTTOM ACTION BUTTONS -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:24px; padding-top:16px; border-top:1px solid #e2e8f0;">
          <button type="button" class="btn btn-secondary" onclick="UI.closeModal()">Cancel</button>

          <div style="display:flex; gap:12px;">
            <button type="button" class="btn-save-dispatch-green" onclick="InvoicesView.saveInvoice(false)">
              Save Sales Invoice
            </button>
            <button type="button" class="btn btn-primary font-bold" style="padding:10px 22px; font-size:0.95rem; border-radius:8px;" onclick="InvoicesView.saveInvoice(true)">
              Save & Print Tax Invoice
            </button>
          </div>
        </div>
      </form>
    `;

    UI.openModal({ title: "Customer Sales Invoice", content, footer: "", size: "modal-xl" });

    // Initial trigger for customer prefill if passed
    if (defaultCustomerName) {
      this.onCustomerSelect(defaultCustomerName);
    } else if (customers.length > 0) {
      document.getElementById("inv-field-customer").value = customers[0].name;
      this.onCustomerSelect(customers[0].name);
    }

    this.recalcLineItem();
  },

  onCustomerSelect(customerName) {
    const cust = (ERPState.data.customers || []).find(c => c.name.toLowerCase() === (customerName || '').toLowerCase());
    const addressEl = document.getElementById("inv-preview-address");
    const gstinEl = document.getElementById("inv-preview-gstin");
    const stateEl = document.getElementById("inv-preview-state");

    if (cust) {
      if (addressEl) addressEl.textContent = `${cust.address}, ${cust.city}, ${cust.state} - ${cust.pincode}`;
      if (gstinEl) gstinEl.textContent = cust.gstin || "27AABCF1234F1Z5";
      if (stateEl) stateEl.textContent = `${cust.state} (State Code: 27)`;
    } else {
      if (addressEl) addressEl.textContent = "Select a customer to view address";
      if (gstinEl) gstinEl.textContent = "-";
      if (stateEl) stateEl.textContent = "-";
    }
  },

  onItemChange(selectEl) {
    const rate = selectEl.options[selectEl.selectedIndex].getAttribute("data-rate");
    const hsn = selectEl.options[selectEl.selectedIndex].getAttribute("data-hsn");
    if (rate) document.getElementById("inv-line-rate").value = rate;
    if (hsn) document.getElementById("inv-line-hsn").value = hsn;
    this.recalcLineItem();
  },

  recalcLineItem() {
    const qty = Number(document.getElementById("inv-line-qty")?.value || 0);
    const rate = Number(document.getElementById("inv-line-rate")?.value || 0);
    const gstRate = Number(document.getElementById("inv-line-gstrate")?.value || 18);

    const taxable = qty * rate;
    const gst = taxable * (gstRate / 100);
    const total = taxable + gst;

    const calcTotalEl = document.getElementById("inv-line-calc-total");
    if (calcTotalEl) calcTotalEl.value = total ? total.toFixed(2) : "";
  },

  addLineItem() {
    const itemSelect = document.getElementById("inv-line-item");
    const item = itemSelect.value;
    const hsn = document.getElementById("inv-line-hsn").value || "5208";
    const lotNo = document.getElementById("inv-line-lot").value || `LOT-2026-00145`;
    const baleNo = `BALE-${String(this._itemCounter).padStart(2, '0')}`;
    const qty = Number(document.getElementById("inv-line-qty").value || 0);
    const rate = Number(document.getElementById("inv-line-rate").value || 0);
    const gstRate = Number(document.getElementById("inv-line-gstrate").value || 18);

    if (!item) return UI.showToast("Select Item", "Please choose an item/fabric for this line", "warning");
    if (qty <= 0) return UI.showToast("Enter Quantity", "Quantity must be greater than 0", "warning");

    const taxableAmount = qty * rate;
    const gstAmount = taxableAmount * (gstRate / 100);
    const totalAmount = taxableAmount + gstAmount;

    this._currentInvoiceItems.push({
      item,
      hsn,
      lotNo,
      baleNo,
      qty,
      unit: "Meters",
      rate,
      discount: 0,
      taxableAmount,
      gstRate,
      gstAmount,
      totalAmount
    });

    this._itemCounter++;

    // Refresh table
    const container = document.getElementById("inv-items-table-container");
    if (container) {
      container.innerHTML = this.renderInvoiceItemsTable();
    }

    UI.showToast("Line Item Added", `${item} (${qty} M) added to invoice`, "success");
  },

  removeLineItem(idx) {
    this._currentInvoiceItems.splice(idx, 1);
    const container = document.getElementById("inv-items-table-container");
    if (container) {
      container.innerHTML = this.renderInvoiceItemsTable();
    }
  },

  renderInvoiceItemsTable() {
    if (this._currentInvoiceItems.length === 0) {
      return `
        <div class="dispatch-table-container">
          <table class="dispatch-items-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Item Description</th>
                <th>HSN</th>
                <th>Lot No</th>
                <th>Qty (M)</th>
                <th>Rate (₹)</th>
                <th>Taxable Value (₹)</th>
                <th>GST Rate</th>
                <th>GST Amount (₹)</th>
                <th>Total (₹)</th>
                <th style="text-align:center;">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="11" class="dispatch-empty-cell">No line items added yet.</td>
              </tr>
            </tbody>
          </table>
        </div>
      `;
    }

    const totalQty = this._currentInvoiceItems.reduce((acc, it) => acc + Number(it.qty || 0), 0);
    const subtotal = this._currentInvoiceItems.reduce((acc, it) => acc + Number(it.taxableAmount || 0), 0);
    const totalGst = this._currentInvoiceItems.reduce((acc, it) => acc + Number(it.gstAmount || 0), 0);
    const grandTotal = this._currentInvoiceItems.reduce((acc, it) => acc + Number(it.totalAmount || 0), 0);

    return `
      <div class="dispatch-table-container">
        <table class="dispatch-items-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Item Description</th>
              <th>HSN</th>
              <th>Lot No</th>
              <th>Qty (M)</th>
              <th>Rate (₹)</th>
              <th>Taxable Value (₹)</th>
              <th>GST Rate</th>
              <th>GST Amount (₹)</th>
              <th>Total (₹)</th>
              <th style="text-align:center;">Action</th>
            </tr>
          </thead>
          <tbody>
            ${this._currentInvoiceItems.map((it, idx) => `
              <tr>
                <td class="font-mono">${idx + 1}</td>
                <td class="font-bold">${it.item}</td>
                <td class="font-mono">${it.hsn}</td>
                <td class="font-mono" style="color:var(--primary-700);">${it.lotNo}</td>
                <td class="font-mono font-bold">${Number(it.qty).toLocaleString('en-IN')}</td>
                <td class="font-mono">₹${Number(it.rate).toLocaleString('en-IN')}</td>
                <td class="font-mono font-bold">₹${Number(it.taxableAmount).toLocaleString('en-IN')}</td>
                <td><span class="badge badge-slate">${it.gstRate}%</span></td>
                <td class="font-mono">₹${Number(it.gstAmount).toLocaleString('en-IN')}</td>
                <td class="font-mono font-bold" style="color:#059669;">₹${Number(it.totalAmount).toLocaleString('en-IN')}</td>
                <td style="text-align:center;">
                  <button type="button" class="jw-remove-item-btn" onclick="InvoicesView.removeLineItem(${idx})" title="Remove item">
                    ✕
                  </button>
                </td>
              </tr>
            `).join('')}
          </tbody>
        </table>

        <!-- Live Totals Summary Bar -->
        <div class="jw-totals-pill-container" style="background:#f8fafc; border:1px solid #e2e8f0; margin-top:10px;">
          <div class="jw-totals-pill">Total Items: <strong>${this._currentInvoiceItems.length}</strong></div>
          <div class="jw-totals-pill">Total Qty: <strong>${totalQty.toLocaleString('en-IN')} M</strong></div>
          <div class="jw-totals-pill">Taxable Amount: <strong>₹${subtotal.toLocaleString('en-IN')}</strong></div>
          <div class="jw-totals-pill">Total GST (CGST+SGST): <strong>₹${totalGst.toLocaleString('en-IN')}</strong></div>
          <div class="jw-totals-pill" style="margin-left:auto; background:#f0fdf4; border-color:#86efac; color:#15803d;">
            Grand Total: <strong style="color:#15803d; font-size:1.05rem;">₹${grandTotal.toLocaleString('en-IN')}</strong>
          </div>
        </div>
      </div>
    `;
  },

  saveInvoice(andPrint = false) {
    const customer = document.getElementById("inv-field-customer").value;
    const invoiceNo = document.getElementById("inv-field-no").value;
    const date = document.getElementById("inv-field-date").value;
    const dueDate = document.getElementById("inv-field-duedate").value;
    const paymentTerms = document.getElementById("inv-field-terms").value;
    const dispatchRef = document.getElementById("inv-field-dispatch-ref").value;
    const notes = document.getElementById("inv-field-notes").value;

    if (!customer) {
      return UI.showToast("Select Customer", "Please choose a customer for this sales invoice", "error");
    }

    if (this._currentInvoiceItems.length === 0) {
      return UI.showToast("No Line Items", "Please add at least one item to the invoice", "error");
    }

    const payload = {
      invoiceNo,
      customer,
      date,
      dueDate,
      paymentTerms,
      dispatchRef,
      notes,
      items: [...this._currentInvoiceItems]
    };

    const newInv = ERPState.createCustomerSalesInvoice(payload);
    UI.showToast("Sales Invoice Created!", `Invoice ${newInv.invoiceNo} saved for ${customer}`, "success");
    UI.closeModal();

    if (andPrint) {
      setTimeout(() => this.openPrintInvoiceModal(newInv.invoiceNo), 150);
    } else {
      App.refreshCurrentView();
    }
  },

  deleteInvoice(invoiceNo) {
    if (confirm(`Are you sure you want to delete invoice ${invoiceNo}?`)) {
      ERPState.deleteCustomerInvoice(invoiceNo);
      UI.showToast("Invoice Deleted", `Invoice ${invoiceNo} removed`, "info");
      App.refreshCurrentView();
    }
  },

  // --- RECORD CUSTOMER PAYMENT MODAL ---
  openRecordPaymentModal(invoiceNo) {
    const inv = (ERPState.data.invoices || []).find(i => i.invoiceNo === invoiceNo);
    if (!inv) return;

    const balance = inv.balanceAmount !== undefined ? inv.balanceAmount : (inv.amount - (inv.paidAmount || 0));

    const content = `
      <form id="record-payment-form" onsubmit="return false;">
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Invoice Number</label>
            <input type="text" class="form-control font-mono font-bold" readonly value="${inv.invoiceNo}">
          </div>

          <div class="form-group">
            <label class="form-label">Customer Name</label>
            <input type="text" class="form-control font-bold" readonly value="${inv.customer}">
          </div>

          <div class="form-group">
            <label class="form-label">Total Invoice Amount</label>
            <input type="text" class="form-control font-mono" readonly value="${UI.formatCurrency(inv.amount)}">
          </div>

          <div class="form-group">
            <label class="form-label">Current Balance Due</label>
            <input type="text" class="form-control font-mono font-bold" style="color:var(--danger-600); background:#fef2f2;" readonly value="${UI.formatCurrency(balance)}">
          </div>

          <div class="form-group">
            <label class="form-label">Payment Amount Received (₹) <span class="required-star">*</span></label>
            <input type="number" class="form-control font-mono font-bold" id="pay-amount" required value="${balance}" max="${balance}">
          </div>

          <div class="form-group">
            <label class="form-label">Payment Mode <span class="required-star">*</span></label>
            <select class="form-control" id="pay-mode">
              <option value="Bank Transfer (NEFT)">Bank Transfer (NEFT)</option>
              <option value="RTGS">RTGS</option>
              <option value="UPI / QR Payment">UPI / QR Payment</option>
              <option value="Cheque">Cheque</option>
              <option value="Cash">Cash</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Transaction / UTR / Cheque Ref</label>
            <input type="text" class="form-control font-mono" id="pay-ref" placeholder="e.g. HDFC982347101" value="TXN-${Date.now().toString().slice(-6)}">
          </div>

          <div class="form-group">
            <label class="form-label">Payment Date</label>
            <input type="date" class="form-control" id="pay-date" value="${new Date().toISOString().split('T')[0]}">
          </div>

          <div class="form-group col-span-2">
            <label class="form-label">Remarks / Settlement Notes</label>
            <input type="text" class="form-control" id="pay-remarks" value="Payment received against invoice ${inv.invoiceNo}">
          </div>
        </div>
      </form>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="UI.closeModal()">Cancel</button>
      <button class="btn btn-primary" id="btn-save-payment-action">Confirm Payment Receipt</button>
    `;

    UI.openModal({ title: `Record Payment for ${inv.invoiceNo}`, content, footer, size: "modal-md" });

    document.getElementById("btn-save-payment-action").onclick = () => {
      const payAmount = Number(document.getElementById("pay-amount").value);
      if (payAmount <= 0) return UI.showToast("Invalid Amount", "Please enter valid payment amount", "error");

      const payload = {
        invoiceNo: inv.invoiceNo,
        customer: inv.customer,
        amount: payAmount,
        mode: document.getElementById("pay-mode").value,
        refNo: document.getElementById("pay-ref").value,
        date: document.getElementById("pay-date").value,
        remarks: document.getElementById("pay-remarks").value
      };

      ERPState.recordPayment(payload);
      UI.showToast("Payment Recorded!", `₹${payAmount.toLocaleString('en-IN')} credited against ${inv.invoiceNo}`, "success");
      UI.closeModal();
      App.refreshCurrentView();
    };
  },

  // --- PRINT-READY PROFESSIONAL GST TAX INVOICE ---
  openPrintInvoiceModal(invoiceNo) {
    const inv = (ERPState.data.invoices || []).find(i => i.invoiceNo === invoiceNo);
    if (!inv) return;

    const company = ERPState.data.company;
    const cust = (ERPState.data.customers || []).find(c => c.name.toLowerCase() === inv.customer.toLowerCase());

    const items = inv.items && inv.items.length > 0 ? inv.items : [{
      item: "Premium Cotton Fabric Batch",
      hsn: "5208",
      lotNo: "LOT-2026-00145",
      qty: 2500,
      unit: "M",
      rate: 145,
      taxableAmount: inv.amount / 1.18,
      gstRate: 18,
      gstAmount: inv.amount - (inv.amount / 1.18),
      totalAmount: inv.amount
    }];

    const taxableTotal = items.reduce((acc, it) => acc + Number(it.taxableAmount || 0), 0);
    const cgst = taxableTotal * 0.09;
    const sgst = taxableTotal * 0.09;
    const grandTotal = inv.amount;

    const content = `
      <div class="print-document-container" id="printable-tax-invoice" style="background:#ffffff; color:#0f172a; padding:24px; font-family:'Inter', sans-serif;">
        <!-- Header Strip -->
        <div style="display:flex; justify-content:space-between; align-items:flex-start; border-bottom:2px solid #0f172a; padding-bottom:14px; margin-bottom:16px;">
          <div>
            <div style="font-size:1.35rem; font-weight:900; color:#1e293b; letter-spacing:-0.02em;">TAX INVOICE</div>
            <div style="font-size:0.75rem; color:#64748b; margin-top:2px;">(Issued under Section 31 of Central Goods and Services Tax Act, 2017)</div>
            <div style="margin-top:8px; font-family:var(--font-mono); font-size:1.05rem; font-weight:800; color:#2563eb;">
              INVOICE NO: ${inv.invoiceNo}
            </div>
            <div style="font-size:0.8rem; color:#475569; margin-top:2px;">
              Date: <strong>${UI.formatDate(inv.date)}</strong> | Due: <strong>${UI.formatDate(inv.dueDate)}</strong>
            </div>
          </div>

          <div style="text-align:right;">
            <div style="font-size:1.25rem; font-weight:900; color:#0f172a;">${company.name}</div>
            <div style="font-size:0.775rem; color:#475569; max-width:300px; margin-top:4px; line-height:1.4;">
              ${company.address}<br>
              <strong>GSTIN:</strong> ${company.gstin} | <strong>PAN:</strong> AABCF8821P<br>
              Email: billing@fashionworks.co.in | State: Maharashtra (27)
            </div>
          </div>
        </div>

        <!-- Consignee Details Grid -->
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px; font-size:0.825rem;">
          <div style="padding:12px; border:1px solid #cbd5e1; border-radius:6px; background:#f8fafc;">
            <div style="font-size:0.7rem; font-weight:800; color:#475569; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Bill To / Consignee:</div>
            <div style="font-size:1rem; font-weight:800; color:#0f172a;">${inv.customer}</div>
            <div style="color:#475569; margin-top:3px; line-height:1.35;">
              ${cust ? `${cust.address}, ${cust.city}, ${cust.state} - ${cust.pincode}` : (inv.customerAddress || 'Mumbai, Maharashtra')}<br>
              <strong>GSTIN:</strong> ${cust ? cust.gstin : (inv.customerGstin || '27AABCF1234F1Z5')}<br>
              <strong>State:</strong> ${cust ? cust.state : 'Maharashtra'} (State Code: 27)
            </div>
          </div>

          <div style="padding:12px; border:1px solid #cbd5e1; border-radius:6px; background:#f8fafc;">
            <div style="font-size:0.7rem; font-weight:800; color:#475569; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Dispatch & Payment Details:</div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; color:#334155; margin-top:4px;">
              <div><strong>Dispatch Ref:</strong> ${inv.dispatchRef || inv.orderNo || '-'}</div>
              <div><strong>Payment Terms:</strong> ${inv.paymentTerms || '30 Days'}</div>
              <div><strong>Place of Supply:</strong> ${cust ? cust.state : 'Maharashtra'}</div>
              <div><strong>Payment Status:</strong> <span style="font-weight:700; color:${inv.status === 'Paid' ? '#15803d' : '#b91c1c'};">${inv.status}</span></div>
            </div>
          </div>
        </div>

        <!-- Items Table -->
        <table style="width:100%; border-collapse:collapse; font-size:0.8rem; margin-bottom:16px;">
          <thead>
            <tr style="background:#0f172a; color:#ffffff; text-align:left;">
              <th style="padding:8px 10px; border:1px solid #0f172a;">#</th>
              <th style="padding:8px 10px; border:1px solid #0f172a;">Item & Quality Description</th>
              <th style="padding:8px 10px; border:1px solid #0f172a;">HSN</th>
              <th style="padding:8px 10px; border:1px solid #0f172a; text-align:right;">Qty (M)</th>
              <th style="padding:8px 10px; border:1px solid #0f172a; text-align:right;">Rate (₹)</th>
              <th style="padding:8px 10px; border:1px solid #0f172a; text-align:right;">Taxable (₹)</th>
              <th style="padding:8px 10px; border:1px solid #0f172a; text-align:center;">GST %</th>
              <th style="padding:8px 10px; border:1px solid #0f172a; text-align:right;">Total (₹)</th>
            </tr>
          </thead>
          <tbody>
            ${items.map((it, idx) => `
              <tr style="border-bottom:1px solid #e2e8f0;">
                <td style="padding:8px 10px; border:1px solid #cbd5e1; font-family:var(--font-mono);">${idx + 1}</td>
                <td style="padding:8px 10px; border:1px solid #cbd5e1; font-weight:700;">
                  ${it.item}
                  ${it.lotNo ? `<div style="font-size:0.7rem; color:#64748b; font-weight:normal;">Lot: ${it.lotNo}</div>` : ''}
                </td>
                <td style="padding:8px 10px; border:1px solid #cbd5e1; font-family:var(--font-mono);">${it.hsn || '5208'}</td>
                <td style="padding:8px 10px; border:1px solid #cbd5e1; text-align:right; font-family:var(--font-mono); font-weight:700;">${Number(it.qty).toLocaleString('en-IN')}</td>
                <td style="padding:8px 10px; border:1px solid #cbd5e1; text-align:right; font-family:var(--font-mono);">₹${Number(it.rate).toLocaleString('en-IN')}</td>
                <td style="padding:8px 10px; border:1px solid #cbd5e1; text-align:right; font-family:var(--font-mono); font-weight:700;">₹${Number(it.taxableAmount || (it.qty * it.rate)).toLocaleString('en-IN')}</td>
                <td style="padding:8px 10px; border:1px solid #cbd5e1; text-align:center;">${it.gstRate || 18}%</td>
                <td style="padding:8px 10px; border:1px solid #cbd5e1; text-align:right; font-family:var(--font-mono); font-weight:800; color:#0f172a;">₹${Number(it.totalAmount).toLocaleString('en-IN')}</td>
              </tr>
            `).join('')}
          </tbody>
        </table>

        <!-- Tax Breakdown & Bank Details Grid -->
        <div style="display:grid; grid-template-columns: 1.2fr 1fr; gap:16px; font-size:0.8rem; margin-bottom:16px;">
          <!-- Bank Details & Payment QR -->
          <div style="padding:12px; border:1px solid #cbd5e1; border-radius:6px; background:#f8fafc; display:flex; gap:14px; align-items:center;">
            <div>
              <div style="font-size:0.7rem; font-weight:800; color:#475569; text-transform:uppercase; margin-bottom:4px;">Bank Settlement Details:</div>
              <div style="color:#334155; line-height:1.4;">
                <strong>Bank:</strong> HDFC Bank Ltd.<br>
                <strong>A/C Name:</strong> FashionWorks Pvt. Ltd.<br>
                <strong>A/C No:</strong> <span class="font-mono font-bold">50200084920194</span><br>
                <strong>IFSC Code:</strong> <span class="font-mono font-bold">HDFC0000240</span><br>
                <strong>Branch:</strong> Lower Parel, Mumbai
              </div>
            </div>
            <div style="text-align:center; padding-left:10px; border-left:1px dashed #cbd5e1;">
              <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="1.5"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path d="M12 3h.01"/><path d="M12 16v.01"/><path d="M16 12h1"/><path d="M21 12v.01"/><path d="M12 21v-1"/></svg>
              <div style="font-size:0.65rem; color:#64748b; font-weight:700;">UPI QR</div>
            </div>
          </div>

          <!-- Total Calculation Table -->
          <div style="padding:10px 14px; border:1px solid #cbd5e1; border-radius:6px; background:#f8fafc;">
            <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
              <span>Total Taxable Value:</span>
              <strong class="font-mono">₹${taxableTotal.toLocaleString('en-IN')}</strong>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:4px; color:#475569;">
              <span>CGST (9%):</span>
              <span class="font-mono">₹${cgst.toLocaleString('en-IN')}</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:6px; color:#475569;">
              <span>SGST (9%):</span>
              <span class="font-mono">₹${sgst.toLocaleString('en-IN')}</span>
            </div>
            <div style="display:flex; justify-content:space-between; padding-top:8px; border-top:2px solid #0f172a; font-size:1.05rem;">
              <span style="font-weight:900;">Grand Total (₹):</span>
              <strong class="font-mono" style="color:#15803d; font-size:1.15rem;">₹${grandTotal.toLocaleString('en-IN')}</strong>
            </div>
          </div>
        </div>

        <!-- Signatures -->
        <div style="display:flex; justify-content:space-between; align-items:flex-end; padding-top:20px; border-top:1px solid #cbd5e1; font-size:0.775rem; color:#475569;">
          <div>
            <strong>Terms:</strong> ${inv.notes || 'Subject to Mumbai jurisdiction. 18% p.a. interest after due date.'}
          </div>
          <div style="text-align:right;">
            <div style="font-weight:800; color:#0f172a;">For FashionWorks Pvt. Ltd.</div>
            <div style="margin-top:35px; border-top:1px solid #94a3b8; padding-top:3px;">Authorized Signatory</div>
          </div>
        </div>
      </div>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="UI.closeModal()">Close</button>
      <button class="btn btn-primary" onclick="UI.printElement('printable-tax-invoice')">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Print GST Tax Invoice
      </button>
    `;

    UI.openModal({ title: `GST Tax Invoice - ${inv.invoiceNo}`, content, footer, size: "modal-lg" });
  }
};
