/* ==========================================================================
   PRODUCTION & JOB WORK MANAGEMENT VIEW
   Sales Orders, Job Work Outward/Inward, QC Inspection, & Lot Tracking
   GarmentERP
   ========================================================================== */

const ProductionView = {
  _addedSOItems: [],
  _soItemCounter: 1,

  // 1. CUSTOMER SALES ORDERS (SO)
  renderOrders() {
    const orders = ERPState.data.salesOrders;

    return `
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left">
            <div class="table-search-box">
              <svg class="table-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              <input type="text" class="table-search-input" placeholder="Search orders by PCH No, BNO, customer, item..." oninput="MastersView.filterGenericTable('so-table', this.value)">
            </div>
          </div>

          <div class="table-toolbar-right">
            <button class="btn btn-secondary btn-sm" onclick="ProductionView.exportOrders()">Export CSV</button>
            <button class="btn btn-primary btn-sm" onclick="ProductionView.openNewOrderModal()">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Book Sales Order
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table" id="so-table">
            <thead>
              <tr>
                <th>PCH NO.</th>
                <th>BNO</th>
                <th>Customer / Vendor</th>
                <th>Order Date</th>
                <th>Items / Quality</th>
                <th>Stage</th>
                <th>Total Qty (M)</th>
                <th>Order Value</th>
                <th>Freight</th>
                <th>Status</th>
                <th style="text-align:right;">Actions</th>
              </tr>
            </thead>
            <tbody>
              ${(!orders || orders.length === 0) ? `
                <tr>
                  <td colspan="11" style="text-align:center; padding:32px 20px; color:var(--slate-400);">
                    <div style="font-size:1rem; font-weight:600; color:var(--slate-600); margin-bottom:4px;">No Sales / Production Orders Found</div>
                    <div style="font-size:0.825rem;">Click the <strong>Book Sales Order</strong> button above to schedule a new garment manufacturing batch.</div>
                  </td>
                </tr>
              ` : orders.map(so => {
                const itemCount = so.items ? so.items.length : 1;
                const displayItem = so.items && so.items.length > 0 ? (so.items[0].item || so.items[0].name) : (so.product || "Garment Order");
                const totalMeters = so.items ? so.items.reduce((acc, it) => acc + Number(it.qty || it.netMeter || 0), 0) : (so.quantity || 0);
                const displayStage = so.items && so.items.length > 0 ? so.items[0].stage : (so.stage || "Cutting");

                return `
                  <tr>
                    <td class="mono-cell font-bold" style="color:var(--primary-600); font-size:0.95rem;">${so.pchNo || so.id || so.orderNo}</td>
                    <td class="mono-cell font-bold" style="color:var(--slate-700);">${so.bno || '-'}</td>
                    <td class="primary-cell">
                      <div class="font-bold">${so.customer || 'Customer'}</div>
                      ${so.remark ? `<div style="font-size:0.75rem; color:var(--slate-500); max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${so.remark}</div>` : ''}
                    </td>
                    <td>${UI.formatDate(so.orderDate || so.date)}</td>
                    <td>
                      <div class="font-bold">${displayItem}</div>
                      ${itemCount > 1 ? `<span class="badge badge-primary" style="font-size:0.7rem; margin-top:2px;">+ ${itemCount - 1} more items</span>` : ''}
                    </td>
                    <td><span class="badge badge-slate">${displayStage}</span></td>
                    <td class="font-bold font-mono">${Number(totalMeters).toLocaleString('en-IN')} M</td>
                    <td class="font-bold font-mono" style="color:#059669;">${UI.formatCurrency(so.amount || (totalMeters * (so.rate || 100)))}</td>
                    <td><span class="badge ${so.freight === 'Paid' ? 'badge-success' : 'badge-slate'}">${so.freight || 'To Pay'}</span></td>
                    <td>${UI.formatStatusBadge(so.status || 'Scheduled')}</td>
                    <td class="table-actions">
                      <button class="table-action-btn qr" title="Print QR Lot Tag" onclick="QRManager.openPrintLabelModal('${so.items && so.items[0] ? so.items[0].lotNo : (so.lotNo || so.id)}')">QR Tag</button>
                      <button class="table-action-btn edit" title="Assign Job Work" onclick="ProductionView.openAssignJobWorkModal('${so.id}')">Assign JW</button>
                      <button class="table-action-btn delete" onclick="ProductionView.deleteOrder('${so.id}')">Delete</button>
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

  // 1.1 BOOK CUSTOMER SALES ORDER MODAL (EXACT SPECIFICATION & MULTI-ITEM SUPPORT)
  openNewOrderModal() {
    const customers = ERPState.data.customers;
    const items = ERPState.data.items;
    const defaultPchNo = String(ERPState.data.salesOrders.length + 8).padStart(4, '0');
    const todayFormatted = new Date().toISOString().split('T')[0];

    // Reset working added items array
    this._addedSOItems = [];
    this._soItemCounter = 1;

    const initialLotNo = `dh/0000/${defaultPchNo}/01`;

    const content = `
      <form id="book-so-form" class="so-booking-card" onsubmit="return false;">
        <!-- TOP HEADER SECTION (2 COLUMNS) -->
        <div class="so-header-grid">
          <!-- Column 1: Date, BNO, Remark -->
          <div style="display:flex; flex-direction:column; gap:14px;">
            <div class="so-field-row">
              <label class="so-field-label">Date</label>
              <input type="date" class="so-input-styled" id="so-field-date" value="${todayFormatted}">
            </div>

            <div class="so-field-row">
              <label class="so-field-label">BNO</label>
              <input type="text" class="so-input-styled" id="so-field-bno" placeholder="Enter BNO (e.g. BNO-0081)">
            </div>

            <div class="so-field-row top-align">
              <label class="so-field-label">Remark</label>
              <textarea class="so-textarea-styled" id="so-field-remark" placeholder="Enter order remarks / delivery terms..."></textarea>
            </div>
          </div>

          <!-- Column 2: PCH. NO., Vendor Name / Customer, Freight -->
          <div style="display:flex; flex-direction:column; gap:14px;">
            <div class="so-field-row">
              <label class="so-field-label">PCH. NO.</label>
              <input type="text" class="so-input-styled readonly-bg" id="so-field-pchno" value="${defaultPchNo}">
            </div>

            <div class="so-field-row">
              <label class="so-field-label">Vendor Name</label>
              <select class="so-input-styled" id="so-field-customer">
                <option value="">Select Vendor</option>
                ${customers.map(c => `<option value="${c.name}" data-id="${c.id}">${c.name}</option>`).join('')}
              </select>
            </div>

            <div class="so-field-row">
              <label class="so-field-label">Freight</label>
              <select class="so-input-styled" id="so-field-freight">
                <option value="">Select Freight</option>
                <option value="To Pay" selected>To Pay</option>
                <option value="Paid">Paid</option>
                <option value="To Be Billed">To Be Billed</option>
                <option value="Customer Scope">Customer Scope</option>
                <option value="Self / Included">Self / Included</option>
                <option value="By Transporter">By Transporter</option>
              </select>
            </div>
          </div>
        </div>

        <!-- ITEM LINE DETAILS SECTION (3 COLUMNS) -->
        <div class="so-item-section">
          <div class="so-item-grid-3">
            <!-- Row 1: LOT NO., Item/Quality, Stage -->
            <div class="so-item-col">
              <label class="so-field-label">LOT NO.</label>
              <input type="text" class="so-input-styled readonly-bg" id="so-item-lotno" value="${initialLotNo}">
            </div>

            <div class="so-item-col">
              <label class="so-field-label">Item/Quality</label>
              <select class="so-input-styled" id="so-item-select" onchange="ProductionView.syncSOItemRate(this)">
                <option value="">Select Item</option>
                ${items.map(i => `<option value="${i.name}" data-rate="${i.rate || 145}">${i.name}</option>`).join('')}
              </select>
            </div>

            <div class="so-item-col">
              <label class="so-field-label">Stage</label>
              <select class="so-input-styled" id="so-item-stage">
                <option value="">Select Stage</option>
                <option value="Grey" selected>Grey</option>
                <option value="Bleached">Bleached</option>
                <option value="Dyed">Dyed</option>
                <option value="Printing">Printing</option>
                <option value="Stitching">Stitching</option>
                <option value="Finishing">Finishing</option>
                <option value="Ready for Dispatch">Ready for Dispatch</option>
              </select>
            </div>

            <!-- Row 2: Qty. (M), Rate -->
            <div class="so-item-col">
              <label class="so-field-label">Qty. (M)</label>
              <input type="number" class="so-input-styled" id="so-item-qty" placeholder="e.g. 3000" oninput="ProductionView.recalcSOItemNetMeter()">
            </div>

            <div class="so-item-col">
              <label class="so-field-label">Rate</label>
              <input type="number" class="so-input-styled" id="so-item-rate" placeholder="Rate ₹" value="145">
            </div>

            <!-- Row 3: Transport, LR NO., Net Meter -->
            <div class="so-item-col">
              <label class="so-field-label">Transport</label>
              <input type="text" class="so-input-styled" id="so-item-transport" placeholder="Transport Name">
            </div>

            <div class="so-item-col">
              <label class="so-field-label">LR NO.</label>
              <input type="text" class="so-input-styled" id="so-item-lrno" placeholder="LR / Bilty No.">
            </div>

            <div class="so-item-col">
              <label class="so-field-label">Net Meter</label>
              <input type="number" class="so-input-styled readonly-bg" id="so-item-netmeter" placeholder="Net Meters">
            </div>
          </div>

          <!-- Add More Item Button -->
          <div class="so-add-item-action-bar">
            <button type="button" class="btn-add-more-item-purple" onclick="ProductionView.addSOItemRow()">
              Add More Item
            </button>
          </div>
        </div>

        <!-- Added Multi-Items Table Area -->
        <div id="so-added-items-container-box">
          ${this.renderAddedSOItemsTable()}
        </div>
      </form>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="UI.closeModal()">Cancel</button>
      <button class="btn btn-primary" id="btn-save-so-modal">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        Book Customer Sales Order
      </button>
    `;

    UI.openModal({ title: "Book Customer Sales Order", content, footer, size: "modal-xl" });

    // Handle Save Order click
    document.getElementById("btn-save-so-modal").onclick = () => {
      const date = document.getElementById("so-field-date").value;
      const pchNo = document.getElementById("so-field-pchno").value;
      const bno = document.getElementById("so-field-bno").value || `BNO-${pchNo}`;
      const custSelect = document.getElementById("so-field-customer");
      const customer = custSelect.value;
      const customerId = custSelect.selectedIndex >= 0 ? custSelect.options[custSelect.selectedIndex].getAttribute("data-id") : "CUST-001";
      const freight = document.getElementById("so-field-freight").value || "To Pay";
      const remark = document.getElementById("so-field-remark").value;

      if (!customer) {
        return UI.showToast("Missing Customer / Vendor", "Please select a customer / vendor name", "error");
      }

      // Check if user entered an item in fields without clicking 'Add More Item'
      const itemSelect = document.getElementById("so-item-select");
      const currentItemName = itemSelect.value;
      const currentQty = Number(document.getElementById("so-item-qty").value || 0);

      if (currentItemName && currentQty > 0) {
        this.addSOItemRow(false);
      }

      if (this._addedSOItems.length === 0) {
        return UI.showToast("No Items Added", "Please add at least one item using 'Add More Item'", "error");
      }

      const payload = {
        pchNo,
        bno,
        date,
        customer,
        customerId,
        freight,
        remark,
        items: [...this._addedSOItems]
      };

      const newSO = ERPState.createSalesOrder(payload);
      UI.showToast("Sales Order Booked!", `Order PCH #${pchNo} with ${this._addedSOItems.length} items booked for ${customer}`, "success");
      UI.closeModal();
      App.refreshCurrentView();
    };
  },

  syncSOItemRate(selectEl) {
    const rate = selectEl.options[selectEl.selectedIndex].getAttribute("data-rate");
    if (rate) {
      document.getElementById("so-item-rate").value = rate;
    }
  },

  recalcSOItemNetMeter() {
    const qty = Number(document.getElementById("so-item-qty")?.value || 0);
    const netMeterEl = document.getElementById("so-item-netmeter");
    if (netMeterEl) {
      netMeterEl.value = qty || "";
    }
  },

  addSOItemRow(shouldNotify = true) {
    const lotNo = document.getElementById("so-item-lotno").value;
    const itemSelect = document.getElementById("so-item-select");
    const item = itemSelect.value;
    const stage = document.getElementById("so-item-stage").value || "Grey";
    const qty = Number(document.getElementById("so-item-qty").value || 0);
    const rate = Number(document.getElementById("so-item-rate").value || 0);
    const transport = document.getElementById("so-item-transport").value || "";
    const lrNo = document.getElementById("so-item-lrno").value || "";
    const netMeter = Number(document.getElementById("so-item-netmeter").value || qty);

    if (!item) {
      if (shouldNotify) UI.showToast("Select Item", "Please select an Item/Quality", "warning");
      return;
    }
    if (qty <= 0) {
      if (shouldNotify) UI.showToast("Invalid Quantity", "Please enter a valid Quantity (M)", "warning");
      return;
    }

    const amount = qty * rate;

    this._addedSOItems.push({
      lotNo: lotNo || `dh/0000/0008/${String(this._soItemCounter).padStart(2, '0')}`,
      item,
      stage,
      qty,
      rate,
      amount,
      transport,
      lrNo,
      netMeter
    });

    this._soItemCounter++;

    // Increment LOT NO for next item
    const pchNo = document.getElementById("so-field-pchno")?.value || "0008";
    const nextLotNo = `dh/0000/${pchNo}/${String(this._soItemCounter).padStart(2, '0')}`;
    document.getElementById("so-item-lotno").value = nextLotNo;

    // Reset item input fields
    document.getElementById("so-item-select").value = "";
    document.getElementById("so-item-qty").value = "";
    document.getElementById("so-item-netmeter").value = "";

    // Refresh added items table
    const container = document.getElementById("so-added-items-container-box");
    if (container) {
      container.innerHTML = this.renderAddedSOItemsTable();
    }

    if (shouldNotify) {
      UI.showToast("Item Added", `${item} (${qty} M) added to Sales Order list`, "success");
    }
  },

  removeSOItemRow(index) {
    this._addedSOItems.splice(index, 1);
    const container = document.getElementById("so-added-items-container-box");
    if (container) {
      container.innerHTML = this.renderAddedSOItemsTable();
    }
  },

  renderAddedSOItemsTable() {
    if (this._addedSOItems.length === 0) {
      return `
        <div style="margin-top:16px; padding:16px; background:#f8fafc; border:1px dashed #cbd5e1; border-radius:8px; text-align:center; color:#64748b; font-size:0.85rem;">
          No items added to this order yet. Fill in the item details above and click <strong>"Add More Item"</strong>.
        </div>
      `;
    }

    const totalQty = this._addedSOItems.reduce((acc, it) => acc + Number(it.qty || 0), 0);
    const totalNetMeter = this._addedSOItems.reduce((acc, it) => acc + Number(it.netMeter || 0), 0);
    const totalAmount = this._addedSOItems.reduce((acc, it) => acc + Number(it.amount || 0), 0);

    return `
      <div class="so-added-items-container">
        <div class="so-added-items-header">
          <div class="so-added-items-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            Added Order Items (${this._addedSOItems.length})
          </div>
        </div>

        <table class="so-added-items-table">
          <thead>
            <tr>
              <th>#</th>
              <th>LOT NO.</th>
              <th>Item / Quality</th>
              <th>Stage</th>
              <th>Qty (M)</th>
              <th>Rate</th>
              <th>Transport</th>
              <th>LR NO.</th>
              <th>Net Meter</th>
              <th>Amount (₹)</th>
              <th style="text-align:center;">Action</th>
            </tr>
          </thead>
          <tbody>
            ${this._addedSOItems.map((item, idx) => `
              <tr>
                <td style="font-weight:700; color:#64748b;">${idx + 1}</td>
                <td class="font-mono font-bold" style="color:var(--primary-700);">${item.lotNo}</td>
                <td class="font-bold">${item.item}</td>
                <td><span class="badge badge-slate" style="font-size:0.75rem;">${item.stage}</span></td>
                <td class="font-mono font-bold">${item.qty.toLocaleString('en-IN')} M</td>
                <td class="font-mono">₹${item.rate}</td>
                <td>${item.transport || '-'}</td>
                <td class="font-mono">${item.lrNo || '-'}</td>
                <td class="font-mono font-bold">${item.netMeter.toLocaleString('en-IN')} M</td>
                <td class="font-mono font-bold" style="color:#059669;">₹${item.amount.toLocaleString('en-IN')}</td>
                <td style="text-align:center;">
                  <button type="button" class="jw-remove-item-btn" onclick="ProductionView.removeSOItemRow(${idx})" title="Remove item">
                    ✕ Remove
                  </button>
                </td>
              </tr>
            `).join('')}
          </tbody>
        </table>

        <!-- Live Totals Summary Bar -->
        <div class="jw-totals-pill-container">
          <div class="jw-totals-pill">Total Items: <strong>${this._addedSOItems.length}</strong></div>
          <div class="jw-totals-pill">Total Qty: <strong>${totalQty.toLocaleString('en-IN')} Meters</strong></div>
          <div class="jw-totals-pill">Total Net Meters: <strong>${totalNetMeter.toLocaleString('en-IN')} M</strong></div>
          <div class="jw-totals-pill" style="margin-left:auto; background:#f0fdf4; border-color:#86efac; color:#15803d;">
            Total Order Value: <strong style="color:#15803d; font-size:0.95rem;">₹${totalAmount.toLocaleString('en-IN')}</strong>
          </div>
        </div>
      </div>
    `;
  },

  exportOrders() {
    const headers = ["PCH No", "BNO", "Customer", "Order Date", "Delivery Date", "Product", "Stage", "Quantity (M)", "Amount", "Freight", "Status"];
    const rows = ERPState.data.salesOrders.map(o => [o.pchNo || o.id, o.bno || '', o.customer, o.orderDate || o.date, o.deliveryDate, o.product, o.stage, o.quantity, o.amount, o.freight, o.status]);
    UI.exportToCSV("Customer_Sales_Orders_Report", headers, rows);
  },

  // 2. JOB WORK ASSIGNMENT & OUTWARD

  _currentJWItems: [],

  renderJobWork() {
    const jobworks = ERPState.data.jobWorks;

    return `
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left">
            <div class="table-search-box">
              <svg class="table-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              <input type="text" class="table-search-input" placeholder="Search Job Work by Assign No, worker, process, lot..." oninput="MastersView.filterGenericTable('jw-mgmt-table', this.value)">
            </div>
          </div>

          <div class="table-toolbar-right">
            <button class="btn btn-secondary btn-sm" onclick="ProductionView.exportJobWork()">Export CSV</button>
            <button class="btn btn-primary btn-sm" onclick="ProductionView.openAssignJobWorkModal()">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Assign Job Work
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table" id="jw-mgmt-table">
            <thead>
              <tr>
                <th>Assign No</th>
                <th>Date</th>
                <th>Job Worker</th>
                <th>Process</th>
                <th>Lot Number</th>
                <th>Meters / Qty</th>
                <th>Factory Challan</th>
                <th>Freight</th>
                <th>LR No & Transport</th>
                <th>Status</th>
                <th style="text-align:right;">Actions</th>
              </tr>
            </thead>
            <tbody>
              ${jobworks.map(jw => `
                <tr>
                  <td class="mono-cell font-bold" style="color:var(--primary-600);">
                    #${jw.assignNo || jw.id.replace('JW-2026-', '')}
                    <div style="font-size:0.7rem; color:var(--slate-400); font-weight:normal;">${jw.id}</div>
                  </td>
                  <td>${UI.formatDate(jw.date || jw.outwardDate)}</td>
                  <td class="primary-cell font-bold">${jw.jobWorker}</td>
                  <td><span class="badge badge-purple">${jw.process}</span></td>
                  <td class="mono-cell">${jw.lotNo || 'N/A'}</td>
                  <td class="font-bold font-mono">
                    ${(jw.meter || jw.sentQty || jw.quantity || 0).toLocaleString('en-IN')} mtr
                  </td>
                  <td class="mono-cell font-bold">${jw.factoryChallan || '<span style="color:var(--slate-400);">-</span>'}</td>
                  <td>
                    <span class="badge ${jw.freight === 'Paid' ? 'badge-success' : jw.freight === 'To Pay' ? 'badge-warning' : 'badge-slate'}">
                      ${jw.freight || 'To Pay'}
                    </span>
                  </td>
                  <td style="font-size:0.8rem;">
                    ${jw.lrNo ? `<strong>${jw.lrNo}</strong>` : ''}
                    ${jw.transport ? `<div style="color:var(--slate-500);">${jw.transport}</div>` : (jw.lrNo ? '' : '<span style="color:var(--slate-400);">-</span>')}
                  </td>
                  <td>${UI.formatStatusBadge(jw.status)}</td>
                  <td class="table-actions">
                    <button class="table-action-btn view" title="Print Outward Challan" onclick="ProductionView.openPrintJWChallanModal('${jw.id}')">Challan</button>
                    ${jw.status !== 'Completed' ? `
                      <button class="table-action-btn edit" style="color:var(--success-600);" onclick="ProductionView.openJobWorkInwardModal('${jw.id}')">Receive</button>
                    ` : ''}
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      </div>
    `;
  },

  exportJobWork() {
    const headers = ["Assign No", "Job Work ID", "Date", "Job Worker", "Process", "Lot No", "Meters", "Factory Challan", "Freight", "LR No", "Transport", "Status"];
    const rows = ERPState.data.jobWorks.map(jw => [
      jw.assignNo || "",
      jw.id,
      jw.date || jw.outwardDate,
      jw.jobWorker,
      jw.process,
      jw.lotNo,
      jw.meter || jw.sentQty || 0,
      jw.factoryChallan || "",
      jw.freight || "",
      jw.lrNo || "",
      jw.transport || "",
      jw.status
    ]);
    UI.exportToCSV("Job_Work_Assignments", headers, rows);
  },

  // --- JOB WORK ASSIGNMENT FORM (MATCHING USER SCREENSHOT EXACTLY) ---
  openAssignJobWorkModal(prefillOrderId = null) {
    const workers = ERPState.data.jobWorkers || [];
    const items = ERPState.data.items || [];
    const lots = ERPState.data.lots || [];
    const processes = ERPState.data.processes || [
      "Stitching", "Printing", "Dyeing", "Embroidery", "Washing", "Bleaching", "Finishing", "Cutting", "Packing"
    ];

    const selectedSO = prefillOrderId ? ERPState.data.salesOrders.find(o => o.id === prefillOrderId) : null;

    // Next Assign Number formatted e.g. "0008"
    const nextAssignNum = String(ERPState.data.jobWorks.length + 8).padStart(4, '0');
    const todayStr = new Date().toISOString().split('T')[0];

    // Reset current items array
    this._currentJWItems = [];

    // If prefill order exists, seed one default item
    if (selectedSO) {
      this._currentJWItems.push({
        item: selectedSO.product,
        lotNo: selectedSO.lotNo || "LOT-2026-00145",
        stage: selectedSO.stage || "Stitching Stage",
        meter: selectedSO.quantity || 5000,
        netMeter: selectedSO.quantity || 5000,
        process: "Stitching",
        lrNo: "",
        transport: "",
        rate: 25,
        amount: (selectedSO.quantity || 5000) * 25
      });
    }

    const content = `
      <form id="assign-jw-form" class="jw-assign-card" onsubmit="return false;">
        <!-- Top Section Header -->
        <div class="jw-header-grid">
          <!-- Date -->
          <div class="jw-field-row">
            <label class="jw-field-label">Date</label>
            <input type="date" class="jw-input-styled" id="jw-date-input" value="${todayStr}">
          </div>

          <!-- Assign No -->
          <div class="jw-field-row">
            <label class="jw-field-label">Assign No.</label>
            <input type="text" class="jw-input-styled readonly-bg font-mono font-bold" id="jw-assign-no" value="${nextAssignNum}">
          </div>

          <!-- Job Worker -->
          <div class="jw-field-row">
            <label class="jw-field-label">Job Worker</label>
            <select class="jw-input-styled" id="jw-worker-select" onchange="ProductionView.onJWWorkerChange(this.value)">
              <option value="">Select Job Worker</option>
              ${workers.map(w => `<option value="${w.name}" data-process="${w.process}" data-rate="${w.rate}">${w.name}</option>`).join('')}
            </select>
          </div>

          <!-- Freight -->
          <div class="jw-field-row">
            <label class="jw-field-label">Freight</label>
            <select class="jw-input-styled" id="jw-freight-select">
              <option value="">Select Freight</option>
              <option value="To Pay" selected>To Pay</option>
              <option value="Paid">Paid</option>
              <option value="To Be Billed">To Be Billed</option>
              <option value="Customer Scope">Customer Scope</option>
              <option value="Self / Included">Self / Included</option>
              <option value="By Transporter">By Transporter</option>
            </select>
          </div>

          <!-- Remark (Left) & Factory Challan (Right) -->
          <div class="jw-field-row top-align" style="grid-column: 1 / 2; grid-row: 3 / 5;">
            <label class="jw-field-label">Remark</label>
            <textarea class="jw-textarea-styled" id="jw-remark-input" placeholder=""></textarea>
          </div>

          <div class="jw-field-row" style="grid-column: 2 / 3; grid-row: 3 / 4;">
            <label class="jw-field-label">Factory Challan</label>
            <input type="text" class="jw-input-styled font-mono" id="jw-factory-challan-input" placeholder="">
          </div>
        </div>

        <!-- Line Item Detail Section (3-Column Grid) -->
        <div class="jw-item-section">
          <!-- Row 1: Item/Quality, LOT NO., Stage -->
          <div class="jw-item-grid-3" style="margin-bottom: 16px;">
            <div class="jw-item-col">
              <label class="jw-field-label">Item/Quality</label>
              <select class="jw-input-styled" id="jw-item-select" onchange="ProductionView.onJWItemChange(this.value)">
                <option value="">Select Item</option>
                ${items.map(i => `<option value="${i.name}" data-code="${i.code}" ${selectedSO && selectedSO.product === i.name ? 'selected' : ''}>${i.name}</option>`).join('')}
              </select>
            </div>

            <div class="jw-item-col">
              <label class="jw-field-label">LOT NO.</label>
              <select class="jw-input-styled font-mono" id="jw-lot-select" onchange="ProductionView.onJWLotChange(this.value)">
                <option value="">Select Lot No.</option>
                ${lots.map(l => `<option value="${l.lotNo}" data-qty="${l.currentQty}" data-process="${l.currentProcess}" ${selectedSO && selectedSO.lotNo === l.lotNo ? 'selected' : ''}>${l.lotNo} (${l.product})</option>`).join('')}
              </select>
            </div>

            <div class="jw-item-col">
              <label class="jw-field-label">Stage</label>
              <input type="text" class="jw-input-styled readonly-bg" id="jw-stage-input" value="${selectedSO ? 'Stitching Stage' : ''}" placeholder="">
            </div>
          </div>

          <!-- Row 2: Meter, Net Meter -->
          <div class="jw-item-grid-2" style="margin-bottom: 16px;">
            <div class="jw-item-col">
              <label class="jw-field-label">Meter</label>
              <input type="number" step="any" class="jw-input-styled font-mono" id="jw-meter-input" placeholder="" value="${selectedSO ? selectedSO.quantity : ''}" oninput="ProductionView.recalcJWItemNetMeter()">
            </div>

            <div class="jw-item-col">
              <label class="jw-field-label">Net Meter</label>
              <input type="number" step="any" class="jw-input-styled readonly-bg font-mono font-bold" id="jw-netmeter-input" placeholder="" value="${selectedSO ? selectedSO.quantity : ''}">
            </div>
          </div>

          <!-- Row 3: Process, LR NO., Transport -->
          <div class="jw-item-grid-3">
            <div class="jw-item-col">
              <label class="jw-field-label">Process</label>
              <select class="jw-input-styled" id="jw-process-select">
                <option value="">Select Process</option>
                ${processes.map(p => `<option value="${p}">${p}</option>`).join('')}
              </select>
            </div>

            <div class="jw-item-col">
              <label class="jw-field-label">LR NO.</label>
              <input type="text" class="jw-input-styled font-mono" id="jw-lrno-input" placeholder="">
            </div>

            <div class="jw-item-col">
              <label class="jw-field-label">Transport</label>
              <input type="text" class="jw-input-styled" id="jw-transport-input" placeholder="">
            </div>
          </div>

          <!-- Add Item Action Button -->
          <div class="jw-add-item-action-bar">
            <button type="button" class="btn-add-item-purple" onclick="ProductionView.addJWItemRow()">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Add Item
            </button>
          </div>
        </div>

        <!-- Dynamic Added Items Table Container -->
        <div id="jw-added-items-wrapper">
          ${this.renderAddedJWItemsTable()}
        </div>
      </form>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="UI.closeModal()">Cancel</button>
      <button class="btn btn-primary" id="btn-save-jw-assignment">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
        Save & Assign Job Work
      </button>
    `;

    UI.openModal({ title: `Job Work Assignment (Assign No: ${nextAssignNum})`, content, footer, size: "modal-xl" });

    // Handle Save Job Work Assignment
    document.getElementById("btn-save-jw-assignment").onclick = () => {
      const date = document.getElementById("jw-date-input").value;
      const assignNo = document.getElementById("jw-assign-no").value;
      const workerSelect = document.getElementById("jw-worker-select");
      const jobWorker = workerSelect.value;
      const freight = document.getElementById("jw-freight-select").value || "To Pay";
      const factoryChallan = document.getElementById("jw-factory-challan-input").value.trim();
      const remark = document.getElementById("jw-remark-input").value.trim();

      if (!jobWorker) {
        return UI.showToast("Required Field", "Please select a Job Worker", "warning");
      }

      // Check if current inputs have an unsaved item row and auto-add it if needed
      const itemVal = document.getElementById("jw-item-select").value;
      const meterVal = Number(document.getElementById("jw-meter-input").value);
      if (this._currentJWItems.length === 0 && (!itemVal || !meterVal)) {
        return UI.showToast("Item Required", "Please add at least one Item / Quality with Meters", "warning");
      }

      if (itemVal && meterVal) {
        const lotVal = document.getElementById("jw-lot-select").value || "LOT-2026-00145";
        const stageVal = document.getElementById("jw-stage-input").value || "Stitching Stage";
        const netMeterVal = Number(document.getElementById("jw-netmeter-input").value) || meterVal;
        const processVal = document.getElementById("jw-process-select").value || "Stitching";
        const lrNoVal = document.getElementById("jw-lrno-input").value.trim();
        const transportVal = document.getElementById("jw-transport-input").value.trim();

        this._currentJWItems.push({
          item: itemVal,
          lotNo: lotVal,
          stage: stageVal,
          meter: meterVal,
          netMeter: netMeterVal,
          process: processVal,
          lrNo: lrNoVal,
          transport: transportVal,
          rate: 20,
          amount: netMeterVal * 20
        });
      }

      const payload = {
        assignNo,
        date,
        outwardDate: date,
        jobWorker,
        freight,
        factoryChallan,
        remark,
        items: [...this._currentJWItems]
      };

      const newJW = ERPState.assignJobWork(payload);
      UI.showToast("Job Work Assigned", `Assign No. ${assignNo} (${newJW.id}) successfully issued to ${jobWorker}`, "success");
      UI.closeModal();
      App.refreshCurrentView();
    };
  },

  onJWWorkerChange(workerName) {
    const worker = ERPState.data.jobWorkers.find(w => w.name === workerName);
    if (worker) {
      const processSelect = document.getElementById("jw-process-select");
      if (processSelect) {
        for (let opt of processSelect.options) {
          if (opt.value.toLowerCase().includes(worker.process.toLowerCase()) || worker.process.toLowerCase().includes(opt.value.toLowerCase())) {
            opt.selected = true;
            break;
          }
        }
      }
      const stageInput = document.getElementById("jw-stage-input");
      if (stageInput && !stageInput.value) {
        stageInput.value = `${worker.process} Stage`;
      }
    }
  },

  onJWItemChange(itemName) {
    const item = ERPState.data.items.find(i => i.name === itemName);
    const stageInput = document.getElementById("jw-stage-input");
    if (item && stageInput && !stageInput.value) {
      stageInput.value = item.type === "Raw Material" ? "Grey / Fabric Stage" : "Production Stage";
    }
  },

  onJWLotChange(lotNo) {
    const lot = ERPState.data.lots.find(l => l.lotNo === lotNo);
    if (lot) {
      const stageInput = document.getElementById("jw-stage-input");
      const meterInput = document.getElementById("jw-meter-input");
      const netMeterInput = document.getElementById("jw-netmeter-input");
      
      if (stageInput) stageInput.value = lot.currentProcess ? `${lot.currentProcess} Stage` : "In-Process";
      if (meterInput && !meterInput.value) {
        meterInput.value = lot.currentQty || 5000;
        if (netMeterInput) netMeterInput.value = lot.currentQty || 5000;
      }
    }
  },

  recalcJWItemNetMeter() {
    const meter = Number(document.getElementById("jw-meter-input")?.value || 0);
    const netMeterEl = document.getElementById("jw-netmeter-input");
    if (netMeterEl) {
      netMeterEl.value = meter > 0 ? meter : "";
    }
  },

  addJWItemRow() {
    const item = document.getElementById("jw-item-select").value;
    const lotNo = document.getElementById("jw-lot-select").value;
    const stage = document.getElementById("jw-stage-input").value.trim() || "Production Stage";
    const meter = Number(document.getElementById("jw-meter-input").value);
    const netMeter = Number(document.getElementById("jw-netmeter-input").value) || meter;
    const process = document.getElementById("jw-process-select").value || "Stitching";
    const lrNo = document.getElementById("jw-lrno-input").value.trim();
    const transport = document.getElementById("jw-transport-input").value.trim();

    if (!item) {
      return UI.showToast("Validation Error", "Please select an Item/Quality", "warning");
    }
    if (!lotNo) {
      return UI.showToast("Validation Error", "Please select a LOT NO.", "warning");
    }
    if (!meter || meter <= 0) {
      return UI.showToast("Validation Error", "Please enter valid Meter quantity", "warning");
    }

    // Add to items list
    this._currentJWItems.push({
      item,
      lotNo,
      stage,
      meter,
      netMeter,
      process,
      lrNo,
      transport,
      rate: 20,
      amount: netMeter * 20
    });

    // Refresh added items container in modal
    const wrapper = document.getElementById("jw-added-items-wrapper");
    if (wrapper) {
      wrapper.innerHTML = this.renderAddedJWItemsTable();
    }

    // Clear item inputs for next row
    document.getElementById("jw-item-select").value = "";
    document.getElementById("jw-lot-select").value = "";
    document.getElementById("jw-stage-input").value = "";
    document.getElementById("jw-meter-input").value = "";
    document.getElementById("jw-netmeter-input").value = "";
    document.getElementById("jw-lrno-input").value = "";
    document.getElementById("jw-transport-input").value = "";

    UI.showToast("Item Added", `${item} (${meter} mtr) added to assignment`, "success");
  },

  removeJWItemRow(index) {
    if (this._currentJWItems[index]) {
      this._currentJWItems.splice(index, 1);
      const wrapper = document.getElementById("jw-added-items-wrapper");
      if (wrapper) {
        wrapper.innerHTML = this.renderAddedJWItemsTable();
      }
    }
  },

  renderAddedJWItemsTable() {
    if (!this._currentJWItems || this._currentJWItems.length === 0) {
      return `
        <div style="margin-top:16px; padding:12px; border:1px dashed #cbd5e1; border-radius:8px; text-align:center; color:#64748b; font-size:0.825rem;">
          No item added yet. Fill the details above and click <strong>Add Item</strong>.
        </div>
      `;
    }

    const totalMeters = this._currentJWItems.reduce((acc, it) => acc + Number(it.meter || 0), 0);
    const totalNetMeters = this._currentJWItems.reduce((acc, it) => acc + Number(it.netMeter || 0), 0);

    return `
      <div class="jw-added-items-container">
        <div class="jw-added-items-header">
          <div class="jw-added-items-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            Assigned Line Items (${this._currentJWItems.length})
          </div>
        </div>

        <div class="table-responsive">
          <table class="jw-added-items-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Item / Quality</th>
                <th>LOT NO.</th>
                <th>Stage</th>
                <th>Meter</th>
                <th>Net Meter</th>
                <th>Process</th>
                <th>LR NO.</th>
                <th>Transport</th>
                <th style="text-align:center;">Action</th>
              </tr>
            </thead>
            <tbody>
              ${this._currentJWItems.map((it, idx) => `
                <tr>
                  <td class="font-bold" style="color:var(--slate-500);">${idx + 1}</td>
                  <td class="font-bold">${it.item}</td>
                  <td class="font-mono">${it.lotNo}</td>
                  <td><span class="badge badge-slate">${it.stage || '-'}</span></td>
                  <td class="font-mono font-bold">${Number(it.meter || 0).toLocaleString('en-IN')}</td>
                  <td class="font-mono font-bold" style="color:var(--primary-700);">${Number(it.netMeter || 0).toLocaleString('en-IN')}</td>
                  <td><span class="badge badge-purple">${it.process}</span></td>
                  <td class="font-mono">${it.lrNo || '-'}</td>
                  <td>${it.transport || '-'}</td>
                  <td style="text-align:center;">
                    <button type="button" class="jw-remove-item-btn" onclick="ProductionView.removeJWItemRow(${idx})" title="Remove item">
                      ✕
                    </button>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>

        <div class="jw-totals-pill-container">
          <div class="jw-totals-pill">Total Items: <strong>${this._currentJWItems.length}</strong></div>
          <div class="jw-totals-pill">Total Meters: <strong>${totalMeters.toLocaleString('en-IN')} mtr</strong></div>
          <div class="jw-totals-pill">Total Net Meters: <strong>${totalNetMeters.toLocaleString('en-IN')} mtr</strong></div>
        </div>
      </div>
    `;
  },

  // 3. JOB WORK INWARD MODAL
  openJobWorkInwardModal(jwId) {
    const jw = ERPState.data.jobWorks.find(j => j.id === jwId);
    if (!jw) return;

    const content = `
      <form id="receive-jw-form">
        <div style="background:var(--slate-50); border:1px solid var(--slate-200); border-radius:var(--radius-lg); padding:16px; margin-bottom:20px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <div style="font-size:0.75rem; font-weight:700; color:var(--primary-700); text-transform:uppercase;">Job Work Job Card</div>
            <span class="badge badge-purple">${jw.process}</span>
          </div>
          <div style="font-size:1.1rem; font-weight:800; color:var(--slate-900);">${jw.jobWorker}</div>
          <div style="font-size:0.85rem; color:var(--slate-600); margin-top:4px;">
            Assign No: <strong>#${jw.assignNo || jw.id.replace('JW-2026-', '')}</strong> | Item: <strong>${jw.item}</strong> | Lot: <strong class="font-mono">${jw.lotNo}</strong>
          </div>
          <div style="font-size:0.85rem; color:var(--slate-600); margin-top:2px;">
            Dispatched Meters/Qty: <strong>${(jw.meter || jw.sentQty).toLocaleString('en-IN')}</strong> | Previously Received: <strong>${jw.receivedGoodQty.toLocaleString('en-IN')}</strong>
          </div>
        </div>

        <div class="form-grid grid-3">
          <div class="form-group">
            <label class="form-label">Good Quality Received (Mtrs/Pcs) <span class="required-star">*</span></label>
            <input type="number" class="form-control" id="jwi-good-qty" required value="${jw.pendingQty || (jw.meter || jw.sentQty) - jw.receivedGoodQty}" oninput="ProductionView.recalcJWIPending('${jw.id}')">
          </div>

          <div class="form-group">
            <label class="form-label">Rejected / Defective Qty</label>
            <input type="number" class="form-control" id="jwi-reject-qty" value="0" oninput="ProductionView.recalcJWIPending('${jw.id}')">
          </div>

          <div class="form-group">
            <label class="form-label">Damaged / Scrap Qty</label>
            <input type="number" class="form-control" id="jwi-damaged-qty" value="0" oninput="ProductionView.recalcJWIPending('${jw.id}')">
          </div>

          <div class="form-group">
            <label class="form-label">Inward Date</label>
            <input type="date" class="form-control" id="jwi-date" value="${new Date().toISOString().split('T')[0]}">
          </div>

          <div class="form-group col-span-2">
            <label class="form-label">Job Worker Delivery Challan / Invoice No</label>
            <input type="text" class="form-control" id="jwi-challan" placeholder="e.g. JW-INV-9941">
          </div>

          <div class="form-group col-span-full">
            <label class="form-label">Inspection Remarks</label>
            <textarea class="form-control" id="jwi-remarks" placeholder="Quality comments, measurement check, thread trimming status..."></textarea>
          </div>
        </div>

        <div class="order-summary-box" style="margin-top:16px;">
          <div class="summary-row">
            <span>Pending Balance at Job Worker:</span>
            <strong id="jwi-pending-qty" style="color:var(--slate-800); font-family:var(--font-mono);">0</strong>
          </div>
        </div>
      </form>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="UI.closeModal()">Cancel</button>
      <button class="btn btn-primary" id="btn-save-jw-inward">Accept Inward & Update Lot</button>
    `;

    UI.openModal({ title: `Receive Job Work Inward - #${jw.assignNo || jw.id}`, content, footer, size: "modal-lg" });

    document.getElementById("btn-save-jw-inward").onclick = () => {
      const goodQty = Number(document.getElementById("jwi-good-qty").value);
      const rejectedQty = Number(document.getElementById("jwi-reject-qty").value || 0);
      const damagedQty = Number(document.getElementById("jwi-damaged-qty").value || 0);
      const inwardDate = document.getElementById("jwi-date").value;
      const challanNo = document.getElementById("jwi-challan").value;
      const remarks = document.getElementById("jwi-remarks").value;

      if (!goodQty && goodQty !== 0) return UI.showToast("Invalid Input", "Please enter received quantity", "error");

      const inwardPayload = {
        goodQty,
        rejectedQty,
        damagedQty,
        inwardDate,
        challanNo,
        remarks
      };

      ERPState.receiveJobWorkInward(jwId, inwardPayload);
      UI.showToast("Job Work Inward Recorded", `Received ${goodQty} units from ${jw.jobWorker}`, "success");
      UI.closeModal();
      App.refreshCurrentView();
    };
  },

  recalcJWIPending(jwId) {
    const jw = ERPState.data.jobWorks.find(j => j.id === jwId);
    if (!jw) return;

    const sentQty = Number(jw.meter || jw.sentQty || jw.quantity || 0);
    const good = Number(document.getElementById("jwi-good-qty")?.value || 0);
    const rej = Number(document.getElementById("jwi-reject-qty")?.value || 0);
    const dam = Number(document.getElementById("jwi-damaged-qty")?.value || 0);

    const pending = Math.max(0, sentQty - jw.receivedGoodQty - good - rej - dam);
    const el = document.getElementById("jwi-pending-qty");
    if (el) el.innerText = `${pending.toLocaleString('en-IN')} units`;
  },

  // 4. PRINTABLE JOB WORK OUTWARD CHALLAN
  openPrintJWChallanModal(jwId) {
    const jw = ERPState.data.jobWorks.find(j => j.id === jwId);
    if (!jw) return;

    const items = jw.items && jw.items.length > 0 ? jw.items : [{
      item: jw.item,
      lotNo: jw.lotNo,
      stage: jw.stage || "Stage 1",
      meter: jw.meter || jw.sentQty || jw.quantity,
      netMeter: jw.netMeter || jw.meter || jw.sentQty,
      process: jw.process,
      lrNo: jw.lrNo || "",
      transport: jw.transport || "",
      rate: jw.rate,
      amount: jw.totalAmount
    }];

    const totalMeters = items.reduce((sum, it) => sum + Number(it.meter || 0), 0);
    const totalNetMeters = items.reduce((sum, it) => sum + Number(it.netMeter || 0), 0);

    const content = `
      <div class="print-document-container" id="printable-jw-challan">
        <div class="print-header-grid">
          <div>
            <h2 class="print-doc-title">JOB WORK DELIVERY CHALLAN</h2>
            <div style="font-weight:800; color:var(--primary-700); font-family:var(--font-mono); font-size:1.25rem;">
              Assign No: #${jw.assignNo || jw.id.replace('JW-2026-', '')} 
              <span style="font-size:0.9rem; color:var(--slate-500); font-weight:normal;">(${jw.id})</span>
            </div>
            <div style="font-size:0.85rem; color:var(--slate-600); margin-top:4px;">
              Date: <strong>${UI.formatDate(jw.date || jw.outwardDate)}</strong>
            </div>
          </div>
          <div style="text-align:right;">
            <h3 style="font-size:1.1rem; color:var(--slate-900);">${ERPState.data.company.name}</h3>
            <p style="font-size:0.775rem; color:var(--slate-600); max-width:280px; margin-top:2px;">
              ${ERPState.data.company.address}<br>
              GSTIN: ${ERPState.data.company.gstin}
            </p>
          </div>
        </div>

        <div style="display:grid; grid-template-columns:1.2fr 1fr; gap:16px; margin-bottom:20px; font-size:0.85rem;">
          <div style="padding:12px 16px; border:1px solid var(--slate-200); border-radius:var(--radius-md); background:#fafafa;">
            <div style="font-size:0.7rem; font-weight:700; color:var(--slate-500); text-transform:uppercase; letter-spacing:0.5px;">Consigned To (Job Worker)</div>
            <div style="font-weight:800; font-size:1.05rem; color:var(--slate-900); margin-top:2px;">${jw.jobWorker}</div>
            <div style="color:var(--slate-600); margin-top:4px;">Process: <strong>${jw.process}</strong></div>
            <div style="color:var(--slate-600);">Freight: <span class="badge badge-slate font-bold">${jw.freight || 'To Pay'}</span></div>
            ${jw.remark ? `<div style="color:var(--slate-600); margin-top:4px;">Remark: <em>${jw.remark}</em></div>` : ''}
          </div>

          <div style="padding:12px 16px; border:1px solid var(--slate-200); border-radius:var(--radius-md); background:#fafafa;">
            <div style="font-size:0.7rem; font-weight:700; color:var(--slate-500); text-transform:uppercase; letter-spacing:0.5px;">Dispatch & Transport Details</div>
            <div style="color:var(--slate-700); margin-top:4px;">Factory Challan: <strong class="font-mono">${jw.factoryChallan || 'N/A'}</strong></div>
            <div style="color:var(--slate-700);">LR / Bilty No: <strong class="font-mono">${jw.lrNo || 'N/A'}</strong></div>
            <div style="color:var(--slate-700);">Transport: <strong>${jw.transport || 'Self / Direct'}</strong></div>
            <div style="color:var(--slate-700);">Expected Return: <strong>${UI.formatDate(jw.expectedReturnDate)}</strong></div>
          </div>
        </div>

        <table class="print-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Item / Quality</th>
              <th>LOT NO.</th>
              <th>Stage</th>
              <th style="text-align:right;">Meter</th>
              <th style="text-align:right;">Net Meter</th>
              <th>Process</th>
              <th>LR NO.</th>
              <th>Transport</th>
            </tr>
          </thead>
          <tbody>
            ${items.map((it, idx) => `
              <tr>
                <td>${idx + 1}</td>
                <td><strong>${it.item}</strong></td>
                <td class="font-mono">${it.lotNo}</td>
                <td>${it.stage || '-'}</td>
                <td class="font-mono font-bold" style="text-align:right;">${Number(it.meter || 0).toLocaleString('en-IN')}</td>
                <td class="font-mono font-bold" style="text-align:right;">${Number(it.netMeter || 0).toLocaleString('en-IN')}</td>
                <td><span class="badge badge-purple">${it.process}</span></td>
                <td class="font-mono">${it.lrNo || '-'}</td>
                <td>${it.transport || '-'}</td>
              </tr>
            `).join('')}
          </tbody>
          <tfoot>
            <tr style="font-weight:bold; background:#f8fafc; border-top:2px solid #cbd5e1;">
              <td colspan="4" style="text-align:right;">Total:</td>
              <td class="font-mono font-bold" style="text-align:right;">${totalMeters.toLocaleString('en-IN')} mtr</td>
              <td class="font-mono font-bold" style="text-align:right;">${totalNetMeters.toLocaleString('en-IN')} mtr</td>
              <td colspan="3"></td>
            </tr>
          </tfoot>
        </table>

        <div style="margin-top:20px; font-size:0.775rem; color:var(--slate-600); line-height:1.5;">
          <strong>Statutory Terms:</strong> 1. Goods dispatched under Rule 55 of CGST Rules 2017 for job work manufacturing. 2. Return within 180 days. 3. Rejections and processing waste to be accounted and returned along with the finished lot.
        </div>

        <div class="print-footer-grid" style="margin-top:30px;">
          <div class="print-sign-box">Prepared By (Dispatch)</div>
          <div class="print-sign-box">Transporter / LR Carrier</div>
          <div class="print-sign-box">Job Worker Signature</div>
        </div>
      </div>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="UI.closeModal()">Close</button>
      <button class="btn btn-primary" onclick="window.print(); UI.showToast('Printing Challan', 'Sent to printer', 'info');">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
        Print Delivery Challan
      </button>
    `;

    UI.openModal({ title: `Job Work Delivery Challan - #${jw.assignNo || jw.id}`, content, footer, size: "modal-xl" });
  },

  // 5. QUALITY CHECK (QC) MODULE
  renderQC() {

    return `
      <!-- Visual Inspection Progress Banner -->
      <div class="card" style="margin-bottom:24px; background:linear-gradient(135deg, #ffffff, #f0fdf4); border-color:#bbf7d0;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
          <div>
            <div style="font-size:0.75rem; font-weight:800; color:var(--success-700); text-transform:uppercase; letter-spacing:0.05em;">AQL 1.5 Quality Inspection Progress</div>
            <h3 style="margin-top:2px; color:var(--slate-900);">Active Quality Reconciliation for LOT-2026-00145</h3>
          </div>
          <button class="btn btn-success btn-sm" onclick="ProductionView.openNewQCModal()">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            Record Inspection
          </button>
        </div>

        <div style="display:grid; grid-template-columns:repeat(5, 1fr); gap:12px; margin-top:16px; text-align:center;">
          <div style="background:#ffffff; padding:12px; border-radius:var(--radius-md); border:1px solid #e2e8f0;">
            <div style="font-size:0.7rem; color:var(--slate-500); font-weight:700;">RECEIVED</div>
            <div style="font-size:1.35rem; font-weight:800; color:var(--slate-900); font-family:var(--font-mono); margin-top:2px;">5,000</div>
          </div>
          <div style="background:#ffffff; padding:12px; border-radius:var(--radius-md); border:1px solid #e2e8f0;">
            <div style="font-size:0.7rem; color:var(--slate-500); font-weight:700;">QC CHECKED</div>
            <div style="font-size:1.35rem; font-weight:800; color:var(--primary-600); font-family:var(--font-mono); margin-top:2px;">4,900</div>
          </div>
          <div style="background:#ffffff; padding:12px; border-radius:var(--radius-md); border:1px solid #bbf7d0;">
            <div style="font-size:0.7rem; color:var(--success-700); font-weight:700;">PASSED (FINISHED)</div>
            <div style="font-size:1.35rem; font-weight:800; color:var(--success-700); font-family:var(--font-mono); margin-top:2px;">4,700</div>
          </div>
          <div style="background:#ffffff; padding:12px; border-radius:var(--radius-md); border:1px solid #fde68a;">
            <div style="font-size:0.7rem; color:var(--warning-700); font-weight:700;">REWORK</div>
            <div style="font-size:1.35rem; font-weight:800; color:var(--warning-700); font-family:var(--font-mono); margin-top:2px;">150</div>
          </div>
          <div style="background:#ffffff; padding:12px; border-radius:var(--radius-md); border:1px solid #fecaca;">
            <div style="font-size:0.7rem; color:var(--danger-700); font-weight:700;">REJECTED</div>
            <div style="font-size:1.35rem; font-weight:800; color:var(--danger-700); font-family:var(--font-mono); margin-top:2px;">50</div>
          </div>
        </div>
      </div>

      <!-- QC Table -->
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left">
            <div class="table-search-box">
              <svg class="table-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              <input type="text" class="table-search-input" placeholder="Search QC by inspection no, lot, item..." oninput="MastersView.filterGenericTable('qc-table', this.value)">
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table" id="qc-table">
            <thead>
              <tr>
                <th>QC Number</th>
                <th>Job Work Ref</th>
                <th>Lot Number</th>
                <th>Item Checked</th>
                <th>Received</th>
                <th>Passed</th>
                <th>Rework</th>
                <th>Rejected</th>
                <th>QC Person</th>
                <th>Inspection Date</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              ${qcs.map(qc => `
                <tr>
                  <td class="mono-cell font-bold" style="color:var(--primary-600);">${qc.id}</td>
                  <td class="mono-cell">${qc.jobWorkNo}</td>
                  <td class="mono-cell font-bold">${qc.lotNo}</td>
                  <td class="primary-cell">${qc.item}</td>
                  <td class="font-mono">${qc.receivedQty.toLocaleString('en-IN')}</td>
                  <td class="font-mono font-bold" style="color:var(--success-600);">${qc.passedQty.toLocaleString('en-IN')}</td>
                  <td class="font-mono" style="color:var(--warning-600);">${qc.reworkQty}</td>
                  <td class="font-mono" style="color:var(--danger-600);">${qc.rejectedQty}</td>
                  <td>${qc.qcPerson}</td>
                  <td>${UI.formatDate(qc.qcDate)}</td>
                  <td>${UI.formatStatusBadge(qc.status)}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      </div>
    `;
  },

  openNewQCModal() {
    const jws = ERPState.data.jobWorks;
    const selectedJW = jws[0];

    const content = `
      <form id="new-qc-form">
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Job Work Lot <span class="required-star">*</span></label>
            <select class="form-control" id="qc-jw-select" onchange="ProductionView.syncQCModal(this.value)">
              ${jws.map(j => `<option value="${j.id}">${j.id} - ${j.jobWorker} (${j.lotNo})</option>`).join('')}
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Lot Number</label>
            <input type="text" class="form-control font-mono" id="qc-lot-input" readonly value="${selectedJW.lotNo}">
          </div>

          <div class="form-group col-span-2">
            <label class="form-label">Garment Product</label>
            <input type="text" class="form-control" id="qc-item-input" readonly value="${selectedJW.item}">
          </div>

          <div class="form-group">
            <label class="form-label">Received Quantity (Pcs)</label>
            <input type="number" class="form-control" id="qc-received-input" readonly value="${selectedJW.receivedGoodQty || 5000}">
          </div>

          <div class="form-group">
            <label class="form-label">Passed Quantity (A-Grade) <span class="required-star">*</span></label>
            <input type="number" class="form-control" id="qc-passed-input" required value="4700">
          </div>

          <div class="form-group">
            <label class="form-label">Rework Quantity (Minor Flaws)</label>
            <input type="number" class="form-control" id="qc-rework-input" value="150">
          </div>

          <div class="form-group">
            <label class="form-label">Rejected Quantity (Scrap)</label>
            <input type="number" class="form-control" id="qc-reject-input" value="50">
          </div>

          <div class="form-group col-span-2">
            <label class="form-label">Inspection Remarks</label>
            <textarea class="form-control" id="qc-remarks-input">50 pcs neck rib loose stitching, 150 sent back for thread trimming and steam iron touchup.</textarea>
          </div>
        </div>
      </form>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="UI.closeModal()">Cancel</button>
      <button class="btn btn-success" id="btn-save-qc">Approve QC & Move to Finished Goods</button>
    `;

    UI.openModal({ title: "Record Quality Check (QC) Inspection", content, footer, size: "modal-lg" });

    document.getElementById("btn-save-qc").onclick = () => {
      const jwSelect = document.getElementById("qc-jw-select");
      const jw = ERPState.data.jobWorks.find(j => j.id === jwSelect.value);
      const passedQty = Number(document.getElementById("qc-passed-input").value);
      const reworkQty = Number(document.getElementById("qc-rework-input").value);
      const rejectedQty = Number(document.getElementById("qc-reject-input").value);

      if (passedQty <= 0) return UI.showToast("Invalid Quantity", "Passed quantity must be greater than 0", "error");

      const payload = {
        jobWorkNo: jw.id,
        lotNo: jw.lotNo,
        item: jw.item,
        receivedQty: Number(document.getElementById("qc-received-input").value),
        passedQty,
        reworkQty,
        rejectedQty,
        remarks: document.getElementById("qc-remarks-input").value
      };

      const newQC = ERPState.recordQualityCheck(payload);
      UI.showToast("QC Completed", `QC ${newQC.id} passed ${passedQty} pcs into Finished Goods Hub`, "success");
      UI.closeModal();
      App.refreshCurrentView();
    };
  },

  syncQCModal(jwId) {
    const jw = ERPState.data.jobWorks.find(j => j.id === jwId);
    if (jw) {
      document.getElementById("qc-lot-input").value = jw.lotNo;
      document.getElementById("qc-item-input").value = jw.item;
      document.getElementById("qc-received-input").value = jw.receivedGoodQty || jw.sentQty;
    }
  },

  // 6. PRODUCTION & LOT TRACKING TIMELINE
  renderTracking() {
    const lots = ERPState.data.lots;

    return `
      <div class="card" style="margin-bottom:24px;">
        <div class="card-header">
          <div>
            <div class="card-title">Active Manufacturing Lots Traceability</div>
            <div class="card-subtitle">End-to-end QR lot history across fabric cutting, job work, QC, and dispatch</div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>Lot Number</th>
                <th>Order Ref</th>
                <th>Customer</th>
                <th>Product</th>
                <th>Current Quantity</th>
                <th>Current Process</th>
                <th>Status</th>
                <th style="text-align:right;">Actions</th>
              </tr>
            </thead>
            <tbody>
              ${lots.map(lot => `
                <tr>
                  <td class="mono-cell font-bold" style="color:var(--primary-600);">${lot.lotNo}</td>
                  <td class="mono-cell">${lot.orderNo}</td>
                  <td class="primary-cell">${lot.customer}</td>
                  <td>${lot.product}</td>
                  <td class="font-bold font-mono">${(lot.currentQty || lot.targetQty).toLocaleString('en-IN')} pcs</td>
                  <td><span class="badge badge-purple">${lot.currentProcess}</span></td>
                  <td>${UI.formatStatusBadge(lot.status)}</td>
                  <td class="table-actions">
                    <button class="table-action-btn qr" onclick="QRManager.openPrintLabelModal('${lot.lotNo}')">Print QR</button>
                    <button class="table-action-btn view" onclick="ProductionView.openLotDetailDrawer('${lot.lotNo}')">View History</button>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      </div>
    `;
  },

  openLotDetailDrawer(lotNo) {
    const lot = ERPState.data.lots.find(l => l.lotNo === lotNo) || ERPState.data.lots[0];
    const qrSvg = QRManager.generateQRSVG(lot.qrCodeString, 110);

    const content = `
      <div style="display:flex; flex-direction:column; gap:20px;">
        <!-- Lot Summary Card with QR -->
        <div style="background:var(--slate-50); border:1px solid var(--slate-200); border-radius:var(--radius-lg); padding:18px; display:flex; justify-content:space-between; align-items:center; gap:16px;">
          <div>
            <div style="font-size:0.7rem; font-weight:700; color:var(--slate-500); text-transform:uppercase;">Tracked Production Lot</div>
            <div style="font-size:1.35rem; font-weight:800; color:var(--slate-900); font-family:var(--font-mono); margin-top:2px;">${lot.lotNo}</div>
            <div style="font-size:0.85rem; color:var(--slate-700); margin-top:4px;"><strong>${lot.product}</strong> for <strong>${lot.customer}</strong></div>
            <div style="margin-top:6px;">Status: ${UI.formatStatusBadge(lot.status)}</div>
          </div>
          <div style="background:#ffffff; padding:6px; border:1px solid var(--slate-200); border-radius:8px; text-align:center;">
            ${qrSvg}
            <div style="font-size:0.65rem; font-family:var(--font-mono); color:var(--slate-500); margin-top:2px;">SCAN TO TRACK</div>
          </div>
        </div>

        <!-- Lot Milestone Timeline -->
        <div>
          <h4 style="font-size:0.95rem; font-weight:700; color:var(--slate-900); margin-bottom:14px;">Manufacturing Process Trail</h4>
          <div class="timeline-list">
            ${lot.timeline.map(t => `
              <div class="timeline-item">
                <div class="timeline-icon emerald">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div class="timeline-content">
                  <div class="timeline-title">${t.stage} <span style="font-weight:normal; font-family:var(--font-mono); color:var(--primary-700);">(${t.qty.toLocaleString('en-IN')} pcs)</span></div>
                  <div class="timeline-desc">${t.note}</div>
                  <div class="timeline-time">${t.date} at ${t.time} • Operator: <strong>${t.operator}</strong></div>
                </div>
              </div>
            `).join('')}
          </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px;">
          <button class="btn btn-secondary btn-sm" onclick="QRManager.openPrintLabelModal('${lot.lotNo}')">Print Industrial Label</button>
        </div>
      </div>
    `;

    UI.openDrawer({
      title: `Lot Traceability - ${lot.lotNo}`,
      subtitle: `${lot.product} • ${lot.customer}`,
      content,
      size: "drawer-lg"
    });
  },

  deleteOrder(id) {
    UI.showConfirm({
      title: "Delete Production Order?",
      message: `Are you sure you want to delete order <strong>${id}</strong>?`,
      confirmText: "Delete Order",
      isDanger: true,
      onConfirm: () => {
        ERPState.deleteProductionOrder(id);
        UI.showToast("Order Deleted", `${id} removed from schedule`, "warning");
        App.refreshCurrentView();
      }
    });
  }
};

