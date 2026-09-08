/* ==========================================================================
   DISPATCH MANAGEMENT VIEW - READY FOR DISPATCH, CHALLANS & LOGISTICS
   GarmentERP
   ========================================================================== */

const DispatchView = {
  _currentDispatchBales: [],
  _dispatchBaleCounter: 1,

  // 1. READY FOR DISPATCH LIST
  renderReady() {
    const readyOrders = ERPState.data.salesOrders.filter(o => o.status === "Ready for Dispatch" || o.stage === "Ready");

    return `
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left">
            <h3 style="font-size:1.05rem;">Orders Ready for Packaging & Dispatch (Finished Goods Hub)</h3>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>Order No</th>
                <th>Customer</th>
                <th>Product Description</th>
                <th>Ready Qty</th>
                <th>Color</th>
                <th>Order Date</th>
                <th>Delivery Deadline</th>
                <th>Status</th>
                <th style="text-align:right;">Actions</th>
              </tr>
            </thead>
            <tbody>
              ${readyOrders.length > 0 ? readyOrders.map(o => `
                <tr>
                  <td class="mono-cell font-bold" style="color:var(--primary-600);">${o.pchNo || o.id}</td>
                  <td class="primary-cell">${o.customer}</td>
                  <td>${o.product}</td>
                  <td class="font-bold font-mono" style="color:var(--success-700);">${o.quantity.toLocaleString('en-IN')} M</td>
                  <td><span class="badge badge-slate">${o.stage || 'Ready'}</span></td>
                  <td>${UI.formatDate(o.orderDate || o.date)}</td>
                  <td>${UI.formatDate(o.deliveryDate)}</td>
                  <td><span class="badge badge-success"><span class="badge-dot"></span>Ready for Dispatch</span></td>
                  <td class="table-actions">
                    <button class="btn btn-primary btn-sm" onclick="DispatchView.openCreateDispatchModal('${o.id}')">
                      Create Dispatch
                    </button>
                  </td>
                </tr>
              `).join('') : `
                <tr>
                  <td colspan="9" style="text-align:center; padding:32px; color:var(--slate-500);">
                    All completed orders have been dispatched. No pending batches in Ready queue.
                  </td>
                </tr>
              `}
            </tbody>
          </table>
        </div>
      </div>
    `;
  },

  // 2. DISPATCH LIST (CHALLANS & SHIPMENTS)
  renderDispatch() {
    const dispatches = ERPState.data.dispatchChallans || ERPState.data.dispatches || [];

    return `
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left">
            <div class="table-search-box">
              <svg class="table-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              <input type="text" class="table-search-input" placeholder="Search dispatches by Dispatch No, Bill No, customer, transport..." oninput="MastersView.filterGenericTable('dispatch-table', this.value)">
            </div>
          </div>

          <div class="table-toolbar-right">
            <button class="btn btn-primary btn-sm" onclick="DispatchView.openCreateDispatchModal()">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Add Order Dispatch
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table" id="dispatch-table">
            <thead>
              <tr>
                <th>ORDER DISPATCH NO.</th>
                <th>BILL NO.</th>
                <th>Customer</th>
                <th>Transport</th>
                <th>Items & Bales</th>
                <th>Total Meters</th>
                <th>Invoice Amount (₹)</th>
                <th>Dispatch Date</th>
                <th>Status</th>
                <th style="text-align:right;">Actions</th>
              </tr>
            </thead>
            <tbody>
              ${dispatches.length === 0 ? `
                <tr>
                  <td colspan="10" style="text-align:center; padding:32px 20px; color:var(--slate-400);">
                    <div style="font-size:1rem; font-weight:600; color:var(--slate-600); margin-bottom:4px;">No Dispatch Challans Found</div>
                    <div style="font-size:0.825rem;">Click <strong>Add Order Dispatch</strong> to generate a delivery challan & Lorry Receipt.</div>
                  </td>
                </tr>
              ` : dispatches.map(d => {
                const baleCount = d.items ? d.items.length : (d.totalCartons || 1);
                const totalMtr = d.items ? d.items.reduce((acc, it) => acc + Number(it.netMeter || it.meter || 0), 0) : (d.totalQty || d.quantity || 0);
                const grandTotal = d.invoiceAmount || 0;
                const firstItem = d.items && d.items.length > 0 ? d.items[0].item : (d.item || "Finished Goods");

                return `
                  <tr>
                    <td class="mono-cell font-bold" style="color:var(--primary-600); font-size:0.95rem;">${d.orderDispatchNo || d.challanNo || d.id}</td>
                    <td class="mono-cell font-bold" style="color:var(--slate-700);">${d.billNo || d.invoiceNo || d.lrNumber || '-'}</td>
                    <td class="primary-cell">${d.customer}</td>
                    <td>${d.transport || d.transporter || '-'}</td>
                    <td>
                      <div class="font-bold">${firstItem}</div>
                      <span class="badge badge-slate" style="font-size:0.7rem; margin-top:2px;">${baleCount} Carton(s)</span>
                    </td>
                    <td class="font-bold font-mono">${Number(totalMtr).toLocaleString('en-IN')} M</td>
                    <td class="font-bold font-mono" style="color:#059669;">${UI.formatCurrency(grandTotal)}</td>
                    <td>${UI.formatDate(d.dispatchDate || d.date)}</td>
                    <td>${UI.formatStatusBadge(d.status || 'In Transit')}</td>
                    <td class="table-actions">
                      <button class="table-action-btn view" onclick="DispatchView.openPrintDispatchChallanModal('${d.id}')">Print LR</button>
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

  // 2.1 ADD ORDER DISPATCH MODAL (PIXEL-PERFECT FROM USER SCREENSHOTS)
  openCreateDispatchModal(orderId = null) {
    const customers = ERPState.data.customers;
    const items = ERPState.data.items;
    const lots = ERPState.data.lots;
    const defaultDispatchNo = String(ERPState.data.dispatches.length + 4).padStart(4, '0');
    const todayFormatted = new Date().toISOString().split('T')[0];

    // Reset bales array
    this._currentDispatchBales = [];
    this._dispatchBaleCounter = 1;

    const initialBaleNo = "BALE-01";

    const content = `
      <form id="add-order-dispatch-form" class="dispatch-form-card" onsubmit="return false;">
        <!-- TOP HEADER BAR -->
        <div class="dispatch-header-bar">
          <div class="dispatch-header-title">Add Order Dispatch</div>
          <button type="button" class="dispatch-btn-back" onclick="UI.closeModal()">Back</button>
        </div>

        <!-- ROW 1: DATE, ORDER DISPATCH NO., BILL NO. -->
        <div class="dispatch-header-row-3">
          <div class="dispatch-field-group">
            <label class="dispatch-field-label">DATE</label>
            <input type="date" class="dispatch-input-styled" id="disp-field-date" value="${todayFormatted}">
          </div>

          <div class="dispatch-field-group">
            <label class="dispatch-field-label">ORDER DISPATCH NO.</label>
            <input type="text" class="dispatch-input-styled readonly-bg" id="disp-field-no" value="${defaultDispatchNo}">
          </div>

          <div class="dispatch-field-group">
            <label class="dispatch-field-label">BILL NO.</label>
            <input type="text" class="dispatch-input-styled" id="disp-field-billno" placeholder="Enter Bill No. (e.g. BILL-908)">
          </div>
        </div>

        <!-- ROW 2: CUSTOMER, TRANSPORT -->
        <div class="dispatch-header-row-2">
          <div class="dispatch-field-group">
            <label class="dispatch-field-label">CUSTOMER</label>
            <select class="dispatch-input-styled" id="disp-field-customer">
              <option value="">Select customer</option>
              ${customers.map(c => `<option value="${c.name}" data-id="${c.id}">${c.name}</option>`).join('')}
            </select>
          </div>

          <div class="dispatch-field-group">
            <label class="dispatch-field-label">TRANSPORT</label>
            <input type="text" class="dispatch-input-styled" id="disp-field-transport" placeholder="Enter transport name">
          </div>
        </div>

        <!-- ROW 3: STATUS -->
        <div style="max-width:320px; margin-bottom:20px;">
          <div class="dispatch-field-group">
            <label class="dispatch-field-label">STATUS</label>
            <select class="dispatch-input-styled" id="disp-field-status">
              <option value="Pending" selected>Pending</option>
              <option value="Dispatched">Dispatched</option>
              <option value="In Transit">In Transit</option>
              <option value="Delivered">Delivered</option>
            </select>
          </div>
        </div>

        <!-- ITEMS DETAILS SECTION -->
        <div class="dispatch-items-section">
          <div class="dispatch-section-title">Items Details</div>

          <!-- ROW 4: BALE NO, ITEM, LOT NO, AVAILABLE METER, METER, NET METER, RATE -->
          <div class="dispatch-items-grid">
            <div class="dispatch-field-group">
              <label class="dispatch-field-label">BALE NO.</label>
              <input type="text" class="dispatch-input-styled" id="disp-bale-no" placeholder="Bale No" value="${initialBaleNo}">
            </div>

            <div class="dispatch-field-group">
              <label class="dispatch-field-label">ITEM</label>
              <select class="dispatch-input-styled" id="disp-item-select" onchange="DispatchView.onDispatchItemChange(this)">
                <option value="">Select item</option>
                ${items.map(i => `<option value="${i.name}" data-rate="${i.rate || 145}" data-stock="${i.currentStock || 5000}">${i.name}</option>`).join('')}
              </select>
            </div>

            <div class="dispatch-field-group">
              <label class="dispatch-field-label">LOT NO</label>
              <select class="dispatch-input-styled" id="disp-lot-select">
                <option value="">Select lot</option>
                ${lots.map(l => `<option value="${l.lotNo}">${l.lotNo} (${l.product})</option>`).join('')}
              </select>
            </div>

            <div class="dispatch-field-group">
              <label class="dispatch-field-label">AVAILABLE METER</label>
              <input type="text" class="dispatch-input-styled readonly-bg" id="disp-avail-meter" placeholder="Available" value="5000">
            </div>

            <div class="dispatch-field-group">
              <label class="dispatch-field-label">METER</label>
              <input type="number" class="dispatch-input-styled" id="disp-item-meter" placeholder="Enter meters" oninput="DispatchView.recalcDispatchRowCalculations()">
            </div>

            <div class="dispatch-field-group">
              <label class="dispatch-field-label">NET METER</label>
              <input type="number" class="dispatch-input-styled readonly-bg" id="disp-net-meter" placeholder="Net meters" oninput="DispatchView.recalcDispatchRowCalculations()">
            </div>

            <div class="dispatch-field-group">
              <label class="dispatch-field-label">RATE</label>
              <input type="number" class="dispatch-input-styled" id="disp-item-rate" placeholder="Rate ₹" value="145" oninput="DispatchView.recalcDispatchRowCalculations()">
            </div>
          </div>

          <!-- ROW 5: AMOUNT, GST (18%), TOTAL AMOUNT -->
          <div class="dispatch-calc-grid">
            <div class="dispatch-field-group">
              <label class="dispatch-field-label">AMOUNT</label>
              <input type="number" class="dispatch-input-styled readonly-bg" id="disp-calc-amount" placeholder="Amount">
            </div>

            <div class="dispatch-field-group">
              <label class="dispatch-field-label">GST (18%)</label>
              <input type="number" class="dispatch-input-styled readonly-bg" id="disp-calc-gst" placeholder="GST">
            </div>

            <div class="dispatch-field-group">
              <label class="dispatch-field-label">TOTAL AMOUNT</label>
              <input type="number" class="dispatch-input-styled readonly-bg" id="disp-calc-total" placeholder="Total Amount">
            </div>
          </div>

          <!-- ROW 6: ADD ITEM BUTTON -->
          <div style="margin-bottom:20px;">
            <button type="button" class="btn-add-item-purple" onclick="DispatchView.addDispatchBaleRow()">
              Add Item
            </button>
          </div>

          <!-- ROW 7: BALES TABLE -->
          <div id="dispatch-bales-table-container">
            ${this.renderDispatchBalesTable()}
          </div>
        </div>

        <!-- BOTTOM ACTION: SAVE DISPATCH (GREEN BUTTON) -->
        <div style="display:flex; justify-content:flex-end; margin-top:24px; padding-top:16px; border-top:1px solid #e2e8f0;">
          <button type="button" class="btn-save-dispatch-green" id="btn-save-dispatch-action">
            Save Dispatch
          </button>
        </div>
      </form>
    `;

    UI.openModal({ title: "Order Dispatch Management", content, footer: "", size: "modal-xl" });

    // Handle Save Dispatch click
    document.getElementById("btn-save-dispatch-action").onclick = () => {
      const date = document.getElementById("disp-field-date").value;
      const orderDispatchNo = document.getElementById("disp-field-no").value;
      const billNo = document.getElementById("disp-field-billno").value || `BILL-${orderDispatchNo}`;
      const custSelect = document.getElementById("disp-field-customer");
      const customer = custSelect.value;
      const customerId = custSelect.selectedIndex >= 0 ? custSelect.options[custSelect.selectedIndex].getAttribute("data-id") : "CUST-001";
      const transport = document.getElementById("disp-field-transport").value || "V-Trans Logistics India";
      const status = document.getElementById("disp-field-status").value || "Pending";

      if (!customer) {
        return UI.showToast("Select Customer", "Please select a customer from the dropdown", "error");
      }

      // Check if user has entered an item in the fields without clicking Add Item
      const itemSelect = document.getElementById("disp-item-select");
      const currentItemName = itemSelect.value;
      const currentMeter = Number(document.getElementById("disp-item-meter").value || 0);

      if (currentItemName && currentMeter > 0) {
        this.addDispatchBaleRow(false);
      }

      if (this._currentDispatchBales.length === 0) {
        return UI.showToast("No Bales Added", "Please add at least one bale item using 'Add Item'", "error");
      }

      const payload = {
        orderDispatchNo,
        billNo,
        date,
        customer,
        customerId,
        transport,
        status,
        items: [...this._currentDispatchBales]
      };

      const newDO = ERPState.createDispatch(payload);
      UI.showToast("Order Dispatch Saved!", `Dispatch #${orderDispatchNo} with ${this._currentDispatchBales.length} bales created for ${customer}`, "success");
      UI.closeModal();
      App.refreshCurrentView();
    };
  },

  onDispatchItemChange(selectEl) {
    const rate = selectEl.options[selectEl.selectedIndex].getAttribute("data-rate");
    const stock = selectEl.options[selectEl.selectedIndex].getAttribute("data-stock");
    const itemName = selectEl.value;

    if (rate) document.getElementById("disp-item-rate").value = rate;
    if (stock) document.getElementById("disp-avail-meter").value = stock;

    // Find linked lot if available
    const linkedLot = ERPState.data.lots.find(l => l.product.toLowerCase() === itemName.toLowerCase());
    if (linkedLot) {
      document.getElementById("disp-lot-select").value = linkedLot.lotNo;
    }

    this.recalcDispatchRowCalculations();
  },

  recalcDispatchRowCalculations() {
    const meter = Number(document.getElementById("disp-item-meter")?.value || 0);
    const rate = Number(document.getElementById("disp-item-rate")?.value || 0);

    const netMeterEl = document.getElementById("disp-net-meter");
    if (netMeterEl) netMeterEl.value = meter || "";

    const amount = meter * rate;
    const gst = amount * 0.18;
    const total = amount + gst;

    const amountEl = document.getElementById("disp-calc-amount");
    const gstEl = document.getElementById("disp-calc-gst");
    const totalEl = document.getElementById("disp-calc-total");

    if (amountEl) amountEl.value = amount ? amount.toFixed(2) : "";
    if (gstEl) gstEl.value = gst ? gst.toFixed(2) : "";
    if (totalEl) totalEl.value = total ? total.toFixed(2) : "";
  },

  addDispatchBaleRow(shouldNotify = true) {
    const baleNo = document.getElementById("disp-bale-no").value || `BALE-${String(this._dispatchBaleCounter).padStart(2, '0')}`;
    const itemSelect = document.getElementById("disp-item-select");
    const item = itemSelect.value;
    const lotNo = document.getElementById("disp-lot-select").value || `dh/0000/0008/${String(this._dispatchBaleCounter).padStart(2, '0')}`;
    const availableMeter = Number(document.getElementById("disp-avail-meter").value || 5000);
    const meter = Number(document.getElementById("disp-item-meter").value || 0);
    const netMeter = Number(document.getElementById("disp-net-meter").value || meter);
    const rate = Number(document.getElementById("disp-item-rate").value || 0);

    if (!item) {
      if (shouldNotify) UI.showToast("Select Item", "Please select an item for this bale", "warning");
      return;
    }
    if (meter <= 0) {
      if (shouldNotify) UI.showToast("Enter Meters", "Please enter valid dispatch meters", "warning");
      return;
    }

    const amount = netMeter * rate;
    const gst = amount * 0.18;
    const totalAmount = amount + gst;

    this._currentDispatchBales.push({
      baleNo,
      lotNo,
      item,
      availableMeter,
      meter,
      netMeter,
      rate,
      amount,
      gst,
      totalAmount
    });

    this._dispatchBaleCounter++;

    // Increment Bale No for next row
    const nextBaleNo = `BALE-${String(this._dispatchBaleCounter).padStart(2, '0')}`;
    document.getElementById("disp-bale-no").value = nextBaleNo;

    // Reset item input fields
    document.getElementById("disp-item-select").value = "";
    document.getElementById("disp-lot-select").value = "";
    document.getElementById("disp-item-meter").value = "";
    document.getElementById("disp-net-meter").value = "";
    document.getElementById("disp-calc-amount").value = "";
    document.getElementById("disp-calc-gst").value = "";
    document.getElementById("disp-calc-total").value = "";

    // Refresh table
    const container = document.getElementById("dispatch-bales-table-container");
    if (container) {
      container.innerHTML = this.renderDispatchBalesTable();
    }

    if (shouldNotify) {
      UI.showToast("Bale Added", `${baleNo} (${meter} M of ${item}) added to dispatch`, "success");
    }
  },

  removeDispatchBaleRow(index) {
    this._currentDispatchBales.splice(index, 1);
    const container = document.getElementById("dispatch-bales-table-container");
    if (container) {
      container.innerHTML = this.renderDispatchBalesTable();
    }
  },

  renderDispatchBalesTable() {
    if (this._currentDispatchBales.length === 0) {
      return `
        <div class="dispatch-table-container">
          <table class="dispatch-items-table">
            <thead>
              <tr>
                <th>Bale No.</th>
                <th>Lot No</th>
                <th>Item</th>
                <th>Meter</th>
                <th>Net Meter</th>
                <th>Rate</th>
                <th>Amount</th>
                <th>GST (18%)</th>
                <th>Total Amount</th>
                <th style="text-align:center;">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="10" class="dispatch-empty-cell">
                  No items added yet.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      `;
    }

    const totalMeters = this._currentDispatchBales.reduce((acc, it) => acc + Number(it.meter || 0), 0);
    const totalNetMeters = this._currentDispatchBales.reduce((acc, it) => acc + Number(it.netMeter || 0), 0);
    const subtotal = this._currentDispatchBales.reduce((acc, it) => acc + Number(it.amount || 0), 0);
    const totalGst = this._currentDispatchBales.reduce((acc, it) => acc + Number(it.gst || 0), 0);
    const grandTotal = this._currentDispatchBales.reduce((acc, it) => acc + Number(it.totalAmount || 0), 0);

    return `
      <div class="dispatch-table-container">
        <table class="dispatch-items-table">
          <thead>
            <tr>
              <th>Bale No.</th>
              <th>Lot No</th>
              <th>Item</th>
              <th>Meter</th>
              <th>Net Meter</th>
              <th>Rate</th>
              <th>Amount</th>
              <th>GST (18%)</th>
              <th>Total Amount</th>
              <th style="text-align:center;">Action</th>
            </tr>
          </thead>
          <tbody>
            ${this._currentDispatchBales.map((bale, idx) => `
              <tr>
                <td class="font-bold font-mono">${bale.baleNo}</td>
                <td class="font-mono font-bold" style="color:var(--primary-700);">${bale.lotNo}</td>
                <td class="font-bold">${bale.item}</td>
                <td class="font-mono font-bold">${bale.meter.toLocaleString('en-IN')}</td>
                <td class="font-mono font-bold">${bale.netMeter.toLocaleString('en-IN')}</td>
                <td class="font-mono">₹${bale.rate}</td>
                <td class="font-mono">₹${bale.amount.toLocaleString('en-IN')}</td>
                <td class="font-mono">₹${bale.gst.toLocaleString('en-IN')}</td>
                <td class="font-mono font-bold" style="color:#059669;">₹${bale.totalAmount.toLocaleString('en-IN')}</td>
                <td style="text-align:center;">
                  <button type="button" class="jw-remove-item-btn" onclick="DispatchView.removeDispatchBaleRow(${idx})" title="Remove bale">
                    ✕ Remove
                  </button>
                </td>
              </tr>
            `).join('')}
          </tbody>
        </table>

        <!-- Live Totals Summary Bar -->
        <div class="jw-totals-pill-container">
          <div class="jw-totals-pill">Total Bales: <strong>${this._currentDispatchBales.length}</strong></div>
          <div class="jw-totals-pill">Total Meters: <strong>${totalMeters.toLocaleString('en-IN')} M</strong></div>
          <div class="jw-totals-pill">Subtotal: <strong>₹${subtotal.toLocaleString('en-IN')}</strong></div>
          <div class="jw-totals-pill">GST (18%): <strong>₹${totalGst.toLocaleString('en-IN')}</strong></div>
          <div class="jw-totals-pill" style="margin-left:auto; background:#f0fdf4; border-color:#86efac; color:#15803d;">
            Grand Total: <strong style="color:#15803d; font-size:0.95rem;">₹${grandTotal.toLocaleString('en-IN')}</strong>
          </div>
        </div>
      </div>
    `;
  },


  syncDispatchModal(orderId) {
    const so = ERPState.data.salesOrders.find(o => o.id === orderId);
    if (so) {
      document.getElementById("do-customer").value = so.customer;
      document.getElementById("do-item").value = so.product;
      document.getElementById("do-qty").value = so.quantity;
      const cust = ERPState.data.customers.find(c => c.name === so.customer);
      if (cust) {
        document.getElementById("do-address").value = `${cust.address}, ${cust.city}, ${cust.state} - ${cust.pincode}`;
      }
    }
  },

  openPrintDispatchChallanModal(doId) {
    const d = ERPState.data.dispatches.find(disp => disp.id === doId);
    if (!d) return;

    const content = `
      <div class="print-document-container" id="printable-dispatch">
        <div class="print-header-grid">
          <div>
            <h2 class="print-doc-title">DISPATCH DELIVERY CHALLAN / LR</h2>
            <div style="font-weight:700; color:var(--primary-700); font-family:var(--font-mono); font-size:1.1rem;">${d.id}</div>
            <div style="font-size:0.85rem; color:var(--slate-500); margin-top:4px;">Date: ${UI.formatDate(d.dispatchDate)} | LR No: <strong>${d.lrNumber}</strong></div>
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
            <div style="font-size:0.7rem; font-weight:700; color:var(--slate-500); text-transform:uppercase;">Consignee / Customer</div>
            <div style="font-weight:800; font-size:1rem; color:var(--slate-900); margin-top:2px;">${d.customer}</div>
            <div style="color:var(--slate-600); margin-top:4px;">Delivery Address: ${d.deliveryAddress}</div>
            <div style="color:var(--slate-600);">Tax Invoice Ref: <strong>${d.invoiceNo}</strong></div>
          </div>

          <div style="padding:12px; border:1px solid var(--slate-200); border-radius:var(--radius-md);">
            <div style="font-size:0.7rem; font-weight:700; color:var(--slate-500); text-transform:uppercase;">Transport Details</div>
            <div style="color:var(--slate-600); margin-top:4px;">Transporter: <strong>${d.transporter}</strong></div>
            <div style="color:var(--slate-600);">Vehicle Number: <strong class="font-mono">${d.vehicleNo}</strong></div>
            <div style="color:var(--slate-600);">Sales Order: <strong>${d.orderNo}</strong></div>
          </div>
        </div>

        <table class="print-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Description of Finished Garment Goods</th>
              <th>Quantity (Pcs)</th>
              <th>Invoice Amount (₹)</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td><strong>${d.item}</strong></td>
              <td class="font-mono font-bold">${d.quantity.toLocaleString('en-IN')} Pcs</td>
              <td class="font-mono font-bold">${UI.formatCurrency(d.invoiceAmount)}</td>
            </tr>
          </tbody>
        </table>

        <div class="print-footer-grid">
          <div class="print-sign-box">Dispatch In-charge</div>
          <div class="print-sign-box">Transporter / Driver Sign</div>
          <div class="print-sign-box">Receiver Sign & Stamp</div>
        </div>
      </div>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="UI.closeModal()">Close</button>
      <button class="btn btn-primary" onclick="window.print(); UI.showToast('Printing Challan', 'Sent to printer', 'info');">
        Print Dispatch Challan
      </button>
    `;

    UI.openModal({ title: `Dispatch Challan - ${d.id}`, content, footer, size: "modal-xl" });
  }
};
