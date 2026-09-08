/* ==========================================================================
   JOB WORK VIEW - ASSIGNMENT, INWARD RECEIPT & READY TRACKING REPORT
   GarmentERP
   ========================================================================== */

const JobWorkView = {
  _currentJobItems: [],
  _jobItemCounter: 1,

  render(submodule = "assign") {
    if (submodule === "inward-report") {
      return this.renderInwardReport();
    } else if (submodule === "create") {
      setTimeout(() => this.openAssignJobWorkModal(), 50);
      return this.renderAssign();
    }
    return this.renderAssign();
  },

  // 1. JOB WORK ASSIGNMENT LIST
  renderAssign() {
    const jobWorks = ERPState.data.jobWorks || [];
    const totalIssued = jobWorks.reduce((acc, j) => acc + Number(j.sentQty || j.quantity || 0), 0);
    const inProgressCount = jobWorks.filter(j => j.status !== "Completed").length;
    const completedCount = jobWorks.filter(j => j.status === "Completed").length;
    const jobWorkers = ERPState.data.jobWorkers || [];

    return `
      <!-- Top Greetings & Action -->
      <div class="dashboard-top-bar" style="margin-bottom:20px;">
        <div class="dashboard-title-wrap">
          <h1>
            Job Work Assignment (Outward Operations)
            <span style="font-size:0.75rem; font-weight:600; padding:2px 8px; background:var(--primary-100); color:var(--primary-700); border-radius:var(--radius-full); vertical-align:middle;">OUTWARD ISSUE</span>
          </h1>
          <p class="dashboard-subtitle">Assign cutting, stitching, dyeing, printing, and finishing jobs to external vendors and tracking delivery timelines.</p>
        </div>

        <div class="dashboard-controls">
          <button class="btn btn-secondary btn-sm" onclick="App.navigate('jobwork', 'inward-report')">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
            Inward & Ready Report
          </button>
          <button class="btn btn-primary btn-sm" onclick="JobWorkView.openAssignJobWorkModal()">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            + Assign Job Work
          </button>
        </div>
      </div>

      <!-- 4 KPI Summary Cards -->
      <div class="kpi-grid" style="margin-bottom:24px;">
        <div class="kpi-card blue">
          <div class="kpi-top">
            <span class="kpi-title">Total Assigned Orders</span>
            <div class="kpi-icon-wrap blue">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            </div>
          </div>
          <div class="kpi-value">${jobWorks.length} Batches</div>
          <div class="kpi-bottom">
            <span class="kpi-trend up">${totalIssued.toLocaleString('en-IN')} Mtrs</span>
            <span class="kpi-period">Issued Total</span>
          </div>
        </div>

        <div class="kpi-card amber">
          <div class="kpi-top">
            <span class="kpi-title">In Progress at Vendors</span>
            <div class="kpi-icon-wrap amber">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
          </div>
          <div class="kpi-value">${inProgressCount} Active</div>
          <div class="kpi-bottom">
            <span class="kpi-trend neutral">Floor Processing</span>
            <span class="kpi-period">Awaiting Inward</span>
          </div>
        </div>

        <div class="kpi-card emerald">
          <div class="kpi-top">
            <span class="kpi-title">Ready & Inwarded</span>
            <div class="kpi-icon-wrap emerald">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
          </div>
          <div class="kpi-value">${completedCount} Batches</div>
          <div class="kpi-bottom">
            <span class="kpi-trend up">QC Received</span>
            <span class="kpi-period">Ready for Packaging</span>
          </div>
        </div>

        <div class="kpi-card purple">
          <div class="kpi-top">
            <span class="kpi-title">Job Worker Partners</span>
            <div class="kpi-icon-wrap purple">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
          </div>
          <div class="kpi-value">${jobWorkers.length} Vendors</div>
          <div class="kpi-bottom">
            <span class="kpi-trend up">Stitching, Print, Dye</span>
            <span class="kpi-period">Approved Units</span>
          </div>
        </div>
      </div>

      <!-- Main Assigned Job Works Table Card -->
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
            <div class="table-search-box" style="min-width:260px;">
              <svg class="table-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              <input type="text" class="table-search-input" placeholder="Search JW no, worker, process, item..." oninput="MastersView.filterGenericTable('jobwork-assign-table', this.value)">
            </div>

            <select class="form-control form-control-sm" style="width:190px;" onchange="JobWorkView.filterAssignByWorker(this.value)">
              <option value="">All Job Workers</option>
              ${jobWorkers.map(w => `<option value="${w.name}">${w.name}</option>`).join('')}
            </select>
          </div>

          <div class="table-toolbar-right">
            <button class="btn btn-primary btn-sm" onclick="JobWorkView.openAssignJobWorkModal()">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              + Assign Job Work
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table" id="jobwork-assign-table">
            <thead>
              <tr>
                <th>JOB WORK NO.</th>
                <th>ASSIGN DATE</th>
                <th>DELIVERY DATE</th>
                <th>JOB WORKER</th>
                <th>PROCESS</th>
                <th>ITEM & LOT</th>
                <th>ISSUED QTY (M)</th>
                <th>RATE (₹)</th>
                <th>TOTAL AMOUNT (₹)</th>
                <th>STATUS</th>
                <th style="text-align:right;">ACTIONS</th>
              </tr>
            </thead>
            <tbody>
              ${(jobWorks || []).length === 0 ? `
                <tr>
                  <td colspan="11" style="text-align:center; padding:32px 20px; color:var(--slate-400);">
                    <div style="font-size:1rem; font-weight:600; color:var(--slate-600); margin-bottom:4px;">No Job Work Assignments Found</div>
                    <div style="font-size:0.825rem;">Click <strong>+ Assign Job Work</strong> above to issue cutting bundles or garments to a vendor unit.</div>
                  </td>
                </tr>
              ` : jobWorks.map(jw => {
                const totalQty = Number(jw.netMeter || jw.sentQty || jw.quantity || 0);
                const pending = Number(jw.pendingQty !== undefined ? jw.pendingQty : totalQty);
                const isDone = jw.status === 'Completed' || pending === 0;

                return `
                  <tr data-worker="${(jw.jobWorker || '').toLowerCase()}">
                    <td class="mono-cell font-bold" style="color:var(--primary-600); font-size:0.95rem;">${jw.assignNo ? `JW-${jw.assignNo}` : jw.id}</td>
                    <td>${UI.formatDate(jw.date || jw.outwardDate)}</td>
                    <td class="font-mono font-bold" style="color:#b45309;">${UI.formatDate(jw.expectedReturnDate || jw.deliveryDate)}</td>
                    <td class="primary-cell">
                      <div class="font-bold">${jw.jobWorker}</div>
                      <span style="font-size:0.75rem; color:var(--slate-500);">${jw.transport || 'Direct Logistics'}</span>
                    </td>
                    <td><span class="badge badge-purple">${jw.process}</span></td>
                    <td>
                      <div class="font-bold">${jw.item}</div>
                      <span class="font-mono" style="font-size:0.75rem; color:var(--primary-700);">${jw.lotNo}</span>
                    </td>
                    <td class="font-mono font-bold">${totalQty.toLocaleString('en-IN')} M</td>
                    <td class="font-mono">₹${jw.rate || 25}</td>
                    <td class="font-bold font-mono" style="color:#059669;">${UI.formatCurrency(jw.totalAmount || (totalQty * (jw.rate || 25)))}</td>
                    <td>
                      <span class="badge ${isDone ? 'badge-success' : 'badge-warning'}">
                        <span class="badge-dot"></span>${isDone ? 'Ready / Completed' : 'In Progress'}
                      </span>
                    </td>
                    <td class="table-actions" style="text-align:right;">
                      ${!isDone ? `
                        <button class="table-action-btn" style="color:var(--success-700); background:#f0fdf4; font-weight:700;" onclick="JobWorkView.openReceiveInwardModal('${jw.id}')">
                          Receive Inward
                        </button>
                      ` : `
                        <span class="badge badge-slate" style="font-size:0.725rem;">Inwarded</span>
                      `}
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

  filterAssignByWorker(workerName) {
    const filter = (workerName || '').toLowerCase();
    const rows = document.querySelectorAll("#jobwork-assign-table tbody tr");
    rows.forEach(r => {
      const w = r.getAttribute("data-worker") || "";
      r.style.display = (!filter || w.includes(filter)) ? "" : "none";
    });
  },

  // 2. ASSIGN JOB WORK FORM MODAL (MATCHING EXACT VISUAL SPECIFICATION)
  openAssignJobWorkModal() {
    const jobWorkers = ERPState.data.jobWorkers || [];
    const items = ERPState.data.items || [];
    const lots = ERPState.data.lots || [];
    const nextAssignNo = String((ERPState.data.jobWorks || []).length + 8).padStart(4, '0');
    const today = new Date().toISOString().split('T')[0];
    const defaultDeliveryDate = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];

    // Reset line items
    this._currentJobItems = [];
    this._jobItemCounter = 1;

    const initialLotNo = lots[0]?.lotNo || "LOT-2026-00145";

    const content = `
      <form id="assign-job-work-form" class="dispatch-form-card" onsubmit="return false;">
        <!-- Top Bar -->
        <div class="dispatch-header-bar">
          <div class="dispatch-header-title">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle; margin-right:6px;"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            Assign Job Work
          </div>
          <button type="button" class="dispatch-btn-back" onclick="UI.closeModal()">Back</button>
        </div>

        <!-- ROW 1: DATE, JOB WORK NO., DELIVERY DATE (EXPECTED READY DATE) -->
        <div class="dispatch-header-row-3">
          <div class="dispatch-field-group">
            <label class="dispatch-field-label">DATE <span class="required-star">*</span></label>
            <input type="date" class="dispatch-input-styled" id="jw-field-date" value="${today}">
          </div>

          <div class="dispatch-field-group">
            <label class="dispatch-field-label">JOB WORK NO.</label>
            <input type="text" class="dispatch-input-styled readonly-bg" id="jw-field-no" value="${nextAssignNo}">
          </div>

          <div class="dispatch-field-group">
            <label class="dispatch-field-label">EXPECTED READY / DELIVERY DATE <span class="required-star">*</span></label>
            <input type="date" class="dispatch-input-styled font-bold" id="jw-field-deliverydate" value="${defaultDeliveryDate}">
          </div>
        </div>

        <!-- ROW 2: JOB WORKER, TRANSPORT -->
        <div class="dispatch-header-row-2">
          <div class="dispatch-field-group">
            <label class="dispatch-field-label">JOB WORKER <span class="required-star">*</span></label>
            <select class="dispatch-input-styled" id="jw-field-worker">
              <option value="">Select job worker</option>
              ${jobWorkers.map(w => `<option value="${w.name}" data-process="${w.process}">${w.name} (${w.process} - ${w.city})</option>`).join('')}
            </select>
          </div>

          <div class="dispatch-field-group">
            <label class="dispatch-field-label">TRANSPORT / LOGISTICS</label>
            <input type="text" class="dispatch-input-styled" id="jw-field-transport" placeholder="Enter transport (e.g. VRL Logistics)">
          </div>
        </div>

        <!-- ROW 3: PROCESS -->
        <div style="max-width:340px; margin-bottom:20px;">
          <div class="dispatch-field-group">
            <label class="dispatch-field-label">PROCESS / OPERATION <span class="required-star">*</span></label>
            <select class="dispatch-input-styled" id="jw-field-process">
              <option value="Stitching" selected>Stitching</option>
              <option value="Dyeing">Dyeing & Bleaching</option>
              <option value="Printing">Printing & Rotary Screen</option>
              <option value="Embroidery">Computerized Embroidery</option>
              <option value="Washing">Garment Washing & Softening</option>
              <option value="Finishing & Packing">Finishing & Ironing</option>
            </select>
          </div>
        </div>

        <!-- ITEMS DETAILS SECTION -->
        <div class="dispatch-items-section">
          <div class="dispatch-section-title">Items & Fabric Details</div>

          <!-- ROW 4: LOT NO, ITEM, AVAILABLE METER, METER, NET METER, RATE -->
          <div class="dispatch-items-grid">
            <div class="dispatch-field-group">
              <label class="dispatch-field-label">LOT NO</label>
              <select class="dispatch-input-styled" id="jw-item-lot">
                ${lots.map(l => `<option value="${l.lotNo}">${l.lotNo} (${l.product})</option>`).join('')}
              </select>
            </div>

            <div class="dispatch-field-group">
              <label class="dispatch-field-label">ITEM</label>
              <select class="dispatch-input-styled" id="jw-item-select" onchange="JobWorkView.onItemChange(this)">
                <option value="">Select item</option>
                ${items.map(i => `<option value="${i.name}" data-rate="25" data-stock="${i.currentStock || 5000}">${i.name}</option>`).join('')}
              </select>
            </div>

            <div class="dispatch-field-group">
              <label class="dispatch-field-label">AVAILABLE METER</label>
              <input type="text" class="dispatch-input-styled readonly-bg" id="jw-avail-meter" value="5000" readonly>
            </div>

            <div class="dispatch-field-group">
              <label class="dispatch-field-label">METER</label>
              <input type="number" class="dispatch-input-styled" id="jw-item-meter" placeholder="Enter meters" oninput="JobWorkView.recalcJobCalculations()">
            </div>

            <div class="dispatch-field-group">
              <label class="dispatch-field-label">NET METER</label>
              <input type="number" class="dispatch-input-styled readonly-bg" id="jw-net-meter" placeholder="Net meters" readonly>
            </div>

            <div class="dispatch-field-group">
              <label class="dispatch-field-label">RATE (₹)</label>
              <input type="number" class="dispatch-input-styled" id="jw-item-rate" placeholder="Rate ₹" value="25" oninput="JobWorkView.recalcJobCalculations()">
            </div>
          </div>

          <!-- ROW 5: AMOUNT, GST, TOTAL AMOUNT -->
          <div class="dispatch-calc-grid">
            <div class="dispatch-field-group">
              <label class="dispatch-field-label">AMOUNT</label>
              <input type="number" class="dispatch-input-styled readonly-bg" id="jw-calc-amount" placeholder="Amount" readonly>
            </div>

            <div class="dispatch-field-group">
              <label class="dispatch-field-label">GST (18%)</label>
              <input type="number" class="dispatch-input-styled readonly-bg" id="jw-calc-gst" placeholder="GST" readonly>
            </div>

            <div class="dispatch-field-group">
              <label class="dispatch-field-label">TOTAL AMOUNT</label>
              <input type="number" class="dispatch-input-styled readonly-bg" id="jw-calc-total" placeholder="Total Amount" readonly>
            </div>
          </div>

          <!-- ROW 6: ADD ITEM BUTTON -->
          <div style="margin-bottom:20px;">
            <button type="button" class="btn-add-item-purple" onclick="JobWorkView.addJobWorkItemRow()">
              + Add Item
            </button>
          </div>

          <!-- ROW 7: ITEMS TABLE -->
          <div id="jw-items-table-container">
            ${this.renderJobItemsTable()}
          </div>
        </div>

        <!-- BOTTOM ACTION: SAVE JOB WORK (GREEN BUTTON) -->
        <div style="display:flex; justify-content:flex-end; margin-top:24px; padding-top:16px; border-top:1px solid #e2e8f0;">
          <button type="button" class="btn-save-dispatch-green" onclick="JobWorkView.saveJobWorkAssignment()">
            Save Job Work
          </button>
        </div>
      </form>
    `;

    UI.openModal({ title: "Assign Job Work", content, footer: "", size: "modal-xl" });

    if (jobWorkers.length > 0) {
      document.getElementById("jw-field-worker").value = jobWorkers[0].name;
    }
  },

  onItemChange(selectEl) {
    const stock = selectEl.options[selectEl.selectedIndex].getAttribute("data-stock");
    if (stock) document.getElementById("jw-avail-meter").value = stock;
    this.recalcJobCalculations();
  },

  recalcJobCalculations() {
    const meter = Number(document.getElementById("jw-item-meter")?.value || 0);
    const rate = Number(document.getElementById("jw-item-rate")?.value || 0);

    const netMeterEl = document.getElementById("jw-net-meter");
    if (netMeterEl) netMeterEl.value = meter || "";

    const amount = meter * rate;
    const gst = amount * 0.18;
    const total = amount + gst;

    const amountEl = document.getElementById("jw-calc-amount");
    const gstEl = document.getElementById("jw-calc-gst");
    const totalEl = document.getElementById("jw-calc-total");

    if (amountEl) amountEl.value = amount ? amount.toFixed(2) : "";
    if (gstEl) gstEl.value = gst ? gst.toFixed(2) : "";
    if (totalEl) totalEl.value = total ? total.toFixed(2) : "";
  },

  addJobWorkItemRow(shouldNotify = true) {
    const itemSelect = document.getElementById("jw-item-select");
    const item = itemSelect.value;
    const lotNo = document.getElementById("jw-item-lot").value || "LOT-2026-00145";
    const meter = Number(document.getElementById("jw-item-meter").value || 0);
    const netMeter = Number(document.getElementById("jw-net-meter").value || meter);
    const rate = Number(document.getElementById("jw-item-rate").value || 0);

    if (!item) {
      if (shouldNotify) UI.showToast("Select Item", "Please select an item for this job work batch", "warning");
      return;
    }
    if (meter <= 0) {
      if (shouldNotify) UI.showToast("Enter Meters", "Please enter valid meters to issue", "warning");
      return;
    }

    const amount = netMeter * rate;
    const gst = amount * 0.18;
    const totalAmount = amount + gst;

    this._currentJobItems.push({
      lotNo,
      item,
      meter,
      netMeter,
      rate,
      amount,
      gst,
      totalAmount
    });

    this._jobItemCounter++;

    // Reset item inputs
    document.getElementById("jw-item-select").value = "";
    document.getElementById("jw-item-meter").value = "";
    document.getElementById("jw-net-meter").value = "";
    document.getElementById("jw-calc-amount").value = "";
    document.getElementById("jw-calc-gst").value = "";
    document.getElementById("jw-calc-total").value = "";

    // Refresh table
    const container = document.getElementById("jw-items-table-container");
    if (container) {
      container.innerHTML = this.renderJobItemsTable();
    }

    if (shouldNotify) {
      UI.showToast("Item Added", `${item} (${meter} M) added to job work batch`, "success");
    }
  },

  removeJobItemRow(idx) {
    this._currentJobItems.splice(idx, 1);
    const container = document.getElementById("jw-items-table-container");
    if (container) {
      container.innerHTML = this.renderJobItemsTable();
    }
  },

  renderJobItemsTable() {
    if (this._currentJobItems.length === 0) {
      return `
        <div class="dispatch-table-container">
          <table class="dispatch-items-table">
            <thead>
              <tr>
                <th>#</th>
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
                <td colspan="10" class="dispatch-empty-cell">No items added yet.</td>
              </tr>
            </tbody>
          </table>
        </div>
      `;
    }

    const totalMeters = this._currentJobItems.reduce((acc, it) => acc + Number(it.meter || 0), 0);
    const totalNetMeters = this._currentJobItems.reduce((acc, it) => acc + Number(it.netMeter || 0), 0);
    const subtotal = this._currentJobItems.reduce((acc, it) => acc + Number(it.amount || 0), 0);
    const totalGst = this._currentJobItems.reduce((acc, it) => acc + Number(it.gst || 0), 0);
    const grandTotal = this._currentJobItems.reduce((acc, it) => acc + Number(it.totalAmount || 0), 0);

    return `
      <div class="dispatch-table-container">
        <table class="dispatch-items-table">
          <thead>
            <tr>
              <th>#</th>
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
            ${this._currentJobItems.map((it, idx) => `
              <tr>
                <td class="font-mono">${idx + 1}</td>
                <td class="font-mono font-bold" style="color:var(--primary-700);">${it.lotNo}</td>
                <td class="font-bold">${it.item}</td>
                <td class="font-mono font-bold">${it.meter.toLocaleString('en-IN')}</td>
                <td class="font-mono font-bold">${it.netMeter.toLocaleString('en-IN')}</td>
                <td class="font-mono">₹${it.rate}</td>
                <td class="font-mono">₹${it.amount.toLocaleString('en-IN')}</td>
                <td class="font-mono">₹${it.gst.toLocaleString('en-IN')}</td>
                <td class="font-mono font-bold" style="color:#059669;">₹${it.totalAmount.toLocaleString('en-IN')}</td>
                <td style="text-align:center;">
                  <button type="button" class="jw-remove-item-btn" onclick="JobWorkView.removeJobItemRow(${idx})" title="Remove item">
                    ✕ Remove
                  </button>
                </td>
              </tr>
            `).join('')}
          </tbody>
        </table>

        <!-- Live Summary Bar -->
        <div class="jw-totals-pill-container">
          <div class="jw-totals-pill">Total Items: <strong>${this._currentJobItems.length}</strong></div>
          <div class="jw-totals-pill">Total Issued: <strong>${totalMeters.toLocaleString('en-IN')} M</strong></div>
          <div class="jw-totals-pill">Subtotal: <strong>₹${subtotal.toLocaleString('en-IN')}</strong></div>
          <div class="jw-totals-pill">GST (18%): <strong>₹${totalGst.toLocaleString('en-IN')}</strong></div>
          <div class="jw-totals-pill" style="margin-left:auto; background:#f0fdf4; border-color:#86efac; color:#15803d;">
            Grand Total: <strong style="color:#15803d; font-size:0.95rem;">₹${grandTotal.toLocaleString('en-IN')}</strong>
          </div>
        </div>
      </div>
    `;
  },

  saveJobWorkAssignment() {
    const date = document.getElementById("jw-field-date").value;
    const assignNo = document.getElementById("jw-field-no").value;
    const expectedReturnDate = document.getElementById("jw-field-deliverydate").value;
    const jobWorker = document.getElementById("jw-field-worker").value;
    const transport = document.getElementById("jw-field-transport").value || "VRL Logistics";
    const process = document.getElementById("jw-field-process").value;

    if (!jobWorker) {
      return UI.showToast("Select Job Worker", "Please choose a job worker vendor", "error");
    }

    // Check if user has entered an item without clicking Add Item
    const itemSelect = document.getElementById("jw-item-select");
    const currentMeter = Number(document.getElementById("jw-item-meter").value || 0);
    if (itemSelect.value && currentMeter > 0) {
      this.addJobWorkItemRow(false);
    }

    if (this._currentJobItems.length === 0) {
      return UI.showToast("No Items Added", "Please add at least one fabric item to the job assignment", "error");
    }

    const payload = {
      assignNo,
      date,
      outwardDate: date,
      expectedReturnDate,
      jobWorker,
      transport,
      process,
      items: [...this._currentJobItems]
    };

    const newJW = ERPState.assignJobWork(payload);
    UI.showToast("Job Work Assigned!", `JW #${newJW.assignNo} assigned to ${jobWorker} (Delivery: ${UI.formatDate(expectedReturnDate)})`, "success");
    UI.closeModal();
    App.refreshCurrentView();
  },

  // 3. JOB WORK INWARD & READY TRACKING REPORT
  renderInwardReport() {
    const jobWorks = ERPState.data.jobWorks || [];
    const totalIssued = jobWorks.reduce((acc, j) => acc + Number(j.sentQty || j.quantity || 0), 0);
    const totalReceived = jobWorks.reduce((acc, j) => acc + Number(j.receivedGoodQty || 0), 0);
    const totalPending = jobWorks.reduce((acc, j) => acc + Number(j.pendingQty !== undefined ? j.pendingQty : ((j.sentQty || j.quantity || 0) - (j.receivedGoodQty || 0))), 0);
    const completedCount = jobWorks.filter(j => j.status === 'Completed' || j.pendingQty === 0).length;
    const completionRate = totalIssued > 0 ? ((totalReceived / totalIssued) * 100).toFixed(1) : 0;
    const jobWorkers = ERPState.data.jobWorkers || [];

    return `
      <!-- Top Title & Controls -->
      <div class="dashboard-top-bar" style="margin-bottom:20px;">
        <div class="dashboard-title-wrap">
          <h1>
            Job Inward & Product Ready Tracking Report
            <span style="font-size:0.75rem; font-weight:600; padding:2px 8px; background:var(--success-100); color:var(--success-700); border-radius:var(--radius-full); vertical-align:middle;">DELIVERY TRACKER</span>
          </h1>
          <p class="dashboard-subtitle">Complete schedule showing when assigned jobs are ready, inwarded back to factory, and pending balances at each vendor.</p>
        </div>

        <div class="dashboard-controls">
          <button class="btn btn-primary btn-sm" onclick="JobWorkView.openAssignJobWorkModal()">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            + Assign New Job
          </button>
        </div>
      </div>

      <!-- 4 Inward KPI Cards -->
      <div class="kpi-grid" style="margin-bottom:24px;">
        <div class="kpi-card blue">
          <div class="kpi-top">
            <span class="kpi-title">Total Issued to Vendors</span>
            <div class="kpi-icon-wrap blue">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </div>
          </div>
          <div class="kpi-value">${totalIssued.toLocaleString('en-IN')} <span style="font-size:0.9rem; font-weight:normal;">Mtrs</span></div>
          <div class="kpi-bottom">
            <span class="kpi-trend up">${jobWorks.length} Batches</span>
            <span class="kpi-period">All Processes</span>
          </div>
        </div>

        <div class="kpi-card emerald">
          <div class="kpi-top">
            <span class="kpi-title">Total Ready & Inwarded</span>
            <div class="kpi-icon-wrap emerald">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
          </div>
          <div class="kpi-value" style="color:#059669;">${totalReceived.toLocaleString('en-IN')} <span style="font-size:0.9rem; font-weight:normal;">Mtrs</span></div>
          <div class="kpi-bottom">
            <span class="kpi-trend up">${completedCount} Completed</span>
            <span class="kpi-period">Received in Store</span>
          </div>
        </div>

        <div class="kpi-card rose">
          <div class="kpi-top">
            <span class="kpi-title">Pending at Vendors</span>
            <div class="kpi-icon-wrap rose">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
          </div>
          <div class="kpi-value" style="color:var(--danger-600);">${totalPending.toLocaleString('en-IN')} <span style="font-size:0.9rem; font-weight:normal;">Mtrs</span></div>
          <div class="kpi-bottom">
            <span class="kpi-trend down">Awaiting Delivery</span>
            <span class="kpi-period">On Floor</span>
          </div>
        </div>

        <div class="kpi-card purple">
          <div class="kpi-top">
            <span class="kpi-title">Inward Completion Rate</span>
            <div class="kpi-icon-wrap purple">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            </div>
          </div>
          <div class="kpi-value">${completionRate}%</div>
          <div class="kpi-bottom">
            <span class="kpi-trend up">Overall Yield</span>
            <span class="kpi-period">Factory Intake</span>
          </div>
        </div>
      </div>

      <!-- Inward Tracking Report Table Card -->
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
            <div class="table-search-box" style="min-width:240px;">
              <svg class="table-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              <input type="text" class="table-search-input" placeholder="Search JW no, worker, item, lot..." oninput="JobWorkView.filterInwardReport()">
            </div>

            <!-- Job Worker Filter -->
            <select class="form-control form-control-sm" id="inw-filter-worker" style="width:190px;" onchange="JobWorkView.filterInwardReport()">
              <option value="">All Job Workers</option>
              ${jobWorkers.map(w => `<option value="${w.name}">${w.name}</option>`).join('')}
            </select>

            <!-- Process Filter -->
            <select class="form-control form-control-sm" id="inw-filter-process" style="width:160px;" onchange="JobWorkView.filterInwardReport()">
              <option value="">All Processes</option>
              <option value="Stitching">Stitching</option>
              <option value="Dyeing">Dyeing</option>
              <option value="Printing">Printing</option>
              <option value="Finishing">Finishing</option>
            </select>

            <!-- Inward Status Filter -->
            <select class="form-control form-control-sm" id="inw-filter-status" style="width:170px;" onchange="JobWorkView.filterInwardReport()">
              <option value="">All Inward Statuses</option>
              <option value="Pending">Awaiting Inward</option>
              <option value="Partially Received">Partially Inwarded</option>
              <option value="Completed">Ready & Completed</option>
            </select>
          </div>

          <div class="table-toolbar-right">
            <button class="btn btn-secondary btn-sm" onclick="window.print()">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
              Print Report
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table" id="inward-tracking-report-table">
            <thead>
              <tr>
                <th>JW NO.</th>
                <th>JOB WORKER</th>
                <th>PROCESS</th>
                <th>ASSIGNED DATE</th>
                <th>EXPECTED READY DATE</th>
                <th>ACTUAL INWARD DATE</th>
                <th>ISSUED (M)</th>
                <th>RECEIVED (M)</th>
                <th>PENDING (M)</th>
                <th>PROGRESS</th>
                <th>STATUS</th>
                <th style="text-align:right;">ACTION</th>
              </tr>
            </thead>
            <tbody>
              ${jobWorks.map(jw => {
                const issued = Number(jw.sentQty || jw.quantity || 0);
                const received = Number(jw.receivedGoodQty || 0);
                const pending = Number(jw.pendingQty !== undefined ? jw.pendingQty : (issued - received));
                const pct = issued > 0 ? Math.min(100, Math.round((received / issued) * 100)) : 0;
                const isCompleted = jw.status === "Completed" || pending === 0;
                const isOverdue = !isCompleted && jw.expectedReturnDate && new Date(jw.expectedReturnDate) < new Date();

                return `
                  <tr data-worker="${(jw.jobWorker || '').toLowerCase()}" data-process="${(jw.process || '').toLowerCase()}" data-status="${isCompleted ? 'Completed' : (received > 0 ? 'Partially Received' : 'Pending')}">
                    <td class="mono-cell font-bold" style="color:var(--primary-600); font-size:0.95rem;">${jw.assignNo ? `JW-${jw.assignNo}` : jw.id}</td>
                    <td class="primary-cell">
                      <div class="font-bold">${jw.jobWorker}</div>
                      <span style="font-size:0.75rem; color:var(--slate-500);">${jw.lotNo}</span>
                    </td>
                    <td><span class="badge badge-purple">${jw.process}</span></td>
                    <td>${UI.formatDate(jw.date || jw.outwardDate)}</td>
                    <td class="font-mono font-bold" style="color:${isOverdue ? 'var(--danger-600)' : '#b45309'};">
                      ${UI.formatDate(jw.expectedReturnDate || jw.deliveryDate)}
                      ${isOverdue ? '<span style="font-size:0.65rem; color:var(--danger-600); display:block;">OVERDUE</span>' : ''}
                    </td>
                    <td class="font-mono font-bold" style="color:#059669;">
                      ${jw.inwardDate ? UI.formatDate(jw.inwardDate) : (isCompleted ? UI.formatDate(jw.expectedReturnDate) : '<span style="color:#94a3b8; font-weight:normal;">Pending Return</span>')}
                    </td>
                    <td class="font-mono font-bold">${issued.toLocaleString('en-IN')}</td>
                    <td class="font-mono font-bold" style="color:#059669;">${received.toLocaleString('en-IN')}</td>
                    <td class="font-mono font-bold" style="color:${pending > 0 ? 'var(--danger-600)' : 'var(--slate-400)'};">
                      ${pending.toLocaleString('en-IN')}
                    </td>
                    <td style="min-width:110px;">
                      <div style="font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:3px;">${pct}% Inwarded</div>
                      <div style="height:6px; background:#e2e8f0; border-radius:99px; overflow:hidden;">
                        <div style="width:${pct}%; height:100%; background:${pct === 100 ? '#22c55e' : (pct > 0 ? '#3b82f6' : '#94a3b8')}; border-radius:99px;"></div>
                      </div>
                    </td>
                    <td>
                      <span class="badge ${isCompleted ? 'badge-success' : (received > 0 ? 'badge-warning' : (isOverdue ? 'badge-danger' : 'badge-slate'))}">
                        <span class="badge-dot"></span>${isCompleted ? 'Ready & Inwarded' : (received > 0 ? 'Partially Inwarded' : (isOverdue ? 'Overdue' : 'Awaiting Inward'))}
                      </span>
                    </td>
                    <td class="table-actions" style="text-align:right;">
                      ${!isCompleted ? `
                        <button class="btn btn-primary btn-sm" style="padding:4px 10px; font-size:0.75rem;" onclick="JobWorkView.openReceiveInwardModal('${jw.id}')">
                          Receive Inward
                        </button>
                      ` : `
                        <span class="badge badge-success" style="font-size:0.725rem;">Received</span>
                      `}
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

  filterInwardReport() {
    const searchVal = document.querySelector(".table-search-input")?.value.toLowerCase() || "";
    const workerVal = document.getElementById("inw-filter-worker")?.value.toLowerCase() || "";
    const processVal = document.getElementById("inw-filter-process")?.value.toLowerCase() || "";
    const statusVal = document.getElementById("inw-filter-status")?.value || "";

    const rows = document.querySelectorAll("#inward-tracking-report-table tbody tr");
    rows.forEach(r => {
      const text = r.textContent.toLowerCase();
      const rowWorker = r.getAttribute("data-worker") || "";
      const rowProcess = r.getAttribute("data-process") || "";
      const rowStatus = r.getAttribute("data-status") || "";

      const matchSearch = text.includes(searchVal);
      const matchWorker = !workerVal || rowWorker.includes(workerVal);
      const matchProcess = !processVal || rowProcess.includes(processVal);
      const matchStatus = !statusVal || rowStatus === statusVal;

      r.style.display = (matchSearch && matchWorker && matchProcess && matchStatus) ? "" : "none";
    });
  },

  // 4. RECEIVE JOB WORK INWARD MODAL
  openReceiveInwardModal(jwId) {
    const jw = (ERPState.data.jobWorks || []).find(j => j.id === jwId);
    if (!jw) return;

    const issued = Number(jw.sentQty || jw.quantity || 0);
    const currentReceived = Number(jw.receivedGoodQty || 0);
    const pending = Number(jw.pendingQty !== undefined ? jw.pendingQty : (issued - currentReceived));
    const today = new Date().toISOString().split('T')[0];
    const defaultDC = `DC-INW-${Date.now().toString().slice(-6)}`;

    const content = `
      <form id="receive-jw-inward-form" onsubmit="return false;">
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Job Work Number</label>
            <input type="text" class="form-control font-mono font-bold" readonly value="${jw.assignNo ? `JW-${jw.assignNo}` : jw.id}">
          </div>

          <div class="form-group">
            <label class="form-label">Job Worker Vendor</label>
            <input type="text" class="form-control font-bold" readonly value="${jw.jobWorker}">
          </div>

          <div class="form-group">
            <label class="form-label">Process & Item</label>
            <input type="text" class="form-control font-bold" readonly value="${jw.process} - ${jw.item}">
          </div>

          <div class="form-group">
            <label class="form-label">Total Issued Fabric (M)</label>
            <input type="text" class="form-control font-mono" readonly value="${issued.toLocaleString('en-IN')} M">
          </div>

          <div class="form-group">
            <label class="form-label">Balance Pending Return (M)</label>
            <input type="text" class="form-control font-mono font-bold" style="color:var(--danger-600); background:#fef2f2;" readonly value="${pending.toLocaleString('en-IN')} M">
          </div>

          <div class="form-group">
            <label class="form-label">Actual Inward Date <span class="required-star">*</span></label>
            <input type="date" class="form-control font-bold" id="inw-date" required value="${today}">
          </div>

          <div class="form-group">
            <label class="form-label">Vendor Inward DC / Challan No <span class="required-star">*</span></label>
            <input type="text" class="form-control font-mono" id="inw-dc-no" required value="${defaultDC}">
          </div>

          <div class="form-group">
            <label class="form-label">Ready Received Quantity (Meters) <span class="required-star">*</span></label>
            <input type="number" class="form-control font-mono font-bold" id="inw-good-qty" required value="${pending}" max="${pending}">
          </div>

          <div class="form-group">
            <label class="form-label">Process Wastage / Rejected Qty (M)</label>
            <input type="number" class="form-control font-mono" id="inw-rej-qty" value="0">
          </div>

          <div class="form-group">
            <label class="form-label">Quality Inspection Status</label>
            <select class="form-control" id="inw-qc-status">
              <option value="Passed QC" selected>Passed Quality Inspection (AQL 1.5)</option>
              <option value="Minor Touchup">Passed with Minor Iron Touchup</option>
              <option value="Pending QC Check">Pending Final Lab QC</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Destination Store / Hub</label>
            <input type="text" class="form-control" id="inw-store" value="Finished Goods Hub - WH Unit 2">
          </div>

          <div class="form-group col-span-2">
            <label class="form-label">Inward Remarks</label>
            <input type="text" class="form-control" id="inw-remarks" value="Processed goods received in good condition, ready for packing and dispatch.">
          </div>
        </div>
      </form>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="UI.closeModal()">Cancel</button>
      <button class="btn btn-primary" id="btn-save-inward-action">Confirm Inward & Update Stock</button>
    `;

    UI.openModal({ title: `Receive Ready Product Inward - ${jw.assignNo ? `JW-${jw.assignNo}` : jw.id}`, content, footer, size: "modal-lg" });

    document.getElementById("btn-save-inward-action").onclick = () => {
      const goodQty = Number(document.getElementById("inw-good-qty").value);
      const rejQty = Number(document.getElementById("inw-rej-qty").value || 0);
      const inwDate = document.getElementById("inw-date").value;
      const inwDC = document.getElementById("inw-dc-no").value;

      if (goodQty <= 0) {
        return UI.showToast("Invalid Quantity", "Received quantity must be greater than 0", "error");
      }

      jw.inwardDate = inwDate;
      jw.inwardChallan = inwDC;
      jw.receivedGoodQty = (jw.receivedGoodQty || 0) + goodQty;
      jw.rejectedQty = (jw.rejectedQty || 0) + rejQty;
      jw.pendingQty = Math.max(0, issued - jw.receivedGoodQty - jw.rejectedQty);

      if (jw.pendingQty === 0) {
        jw.status = "Completed";
        jw.qcStatus = "Passed QC";
      } else {
        jw.status = "Partially Received";
        jw.qcStatus = "Partially Received";
      }

      // Add to item ledger
      const itm = ERPState.data.items.find(i => i.name.toLowerCase() === jw.item.toLowerCase());
      if (itm) {
        itm.currentStock += goodQty;
        ERPState.data.itemLedger.unshift({
          date: inwDate,
          ref: jw.id,
          item: itm.name,
          type: "Job Work Inward (Ready)",
          inward: goodQty,
          outward: 0,
          balance: itm.currentStock,
          user: ERPState.data.currentUser.name,
          lot: jw.lotNo || "LOT-READY"
        });
      }

      ERPState.logActivity(`Received Job Work Inward: ${goodQty} M from ${jw.jobWorker} under DC #${inwDC}`, "Job Work", jw.id);
      ERPState.saveState();

      UI.showToast("Product Inward Received!", `${goodQty} M inwarded from ${jw.jobWorker} on ${UI.formatDate(inwDate)}. Stock updated!`, "success");
      UI.closeModal();
      App.refreshCurrentView();
    };
  }
};
