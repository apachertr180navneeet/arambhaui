/* ==========================================================================
   PURCHASE ENTRY MANAGEMENT VIEW (CHALLAN FORMAT)
   GarmentERP - Single Product Fabric Selection & Than-Wise Meter Breakdown
   ========================================================================== */

const PurchaseView = {
  // Current Than list for active create/edit session (array of meter floats)
  currentThans: [],
  currentEditPoId: null,
  currentEditDbId: null,

  // 1. Live Master Sync - Fetch real vendors & items from backend API
  fetchFreshMasters() {
    fetch('/masters/vendors', {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(vendors => {
      if (Array.isArray(vendors) && vendors.length > 0) {
        ERPState.data.vendors = vendors;
        ERPState.saveState();
        this.populateVendorDropdown();
      }
    })
    .catch(() => {});

    fetch('/masters/items', {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(items => {
      if (Array.isArray(items) && items.length > 0) {
        ERPState.data.items = items;
        ERPState.saveState();
        this.populateItemDropdown();
      }
    })
    .catch(() => {});
  },

  populateVendorDropdown(selectedVendorName) {
    const sel = document.getElementById('pe-vendor');
    if (!sel) return;
    const currentVal = selectedVendorName || sel.value;
    const vendors = (ERPState.data.vendors || []).filter(v => (v.status || '').toLowerCase() !== 'inactive');

    if (vendors.length === 0) {
      sel.innerHTML = '<option value="">No vendors found (Add in Vendor Master)</option>';
      return;
    }

    sel.innerHTML = '<option value="">-- Select Vendor / Supplier --</option>' +
      vendors.map(v => {
        const isSel = (v.name === currentVal || String(v.id) === String(currentVal)) ? 'selected' : '';
        const codeText = v.code ? ` (${v.code})` : '';
        const cityText = v.city ? ` - ${v.city}` : '';
        return `<option value="${v.name}" data-id="${v.id}" data-code="${v.code || ''}" ${isSel}>${v.name}${codeText}${cityText}</option>`;
      }).join('');
  },

  populateItemDropdown(selectedItemName) {
    const sel = document.getElementById('pe-item');
    if (!sel) return;
    const currentVal = selectedItemName || sel.value;
    const items = (ERPState.data.items || []).filter(i => (i.status || '').toLowerCase() !== 'inactive');

    if (items.length === 0) {
      sel.innerHTML = '<option value="">No fabric items found (Add in Item Master)</option>';
      return;
    }

    sel.innerHTML = '<option value="">-- Select Fabric / Raw Material SKU --</option>' +
      items.map(i => {
        const isSel = (i.name === currentVal || String(i.id) === String(currentVal)) ? 'selected' : '';
        const codeText = i.code ? ` [${i.code}]` : '';
        const catText = i.category ? ` (${i.category})` : '';
        const rate = i.unit_cost || i.rate || 0;
        return `<option value="${i.name}" data-id="${i.id}" data-code="${i.code || ''}" data-rate="${rate}" ${isSel}>${i.name}${codeText}${catText}</option>`;
      }).join('');
  },

  onItemChange(selectEl) {
    const opt = selectEl.options[selectEl.selectedIndex];
    if (!opt) return;
    const rate = opt.getAttribute('data-rate');
    const rateInput = document.getElementById('pe-rate');
    if (rate && rateInput && (!rateInput.value || parseFloat(rateInput.value) === 0)) {
      rateInput.value = parseFloat(rate).toFixed(2);
    }
    this.calculateTotals();
  },

  // 2. MAIN REGISTRY LIST: PURCHASE ENTRIES
  renderOrders() {
    const orders = ERPState.data.purchaseOrders || [];
    const vendors = ERPState.data.vendors || [];
    const totalPurchased = orders.reduce((sum, o) => sum + Number(o.grand_total || o.grandTotal || 0), 0);
    const totalThansAll = orders.reduce((sum, o) => {
      const thansCount = o.total_thans || (o.thans && o.thans.length) || (o.notes_parsed && o.notes_parsed.total_thans) || 0;
      return sum + Number(thansCount);
    }, 0);

    return `
      <!-- Top Title & Action Controls -->
      <div class="dashboard-top-bar" style="margin-bottom:20px;">
        <div class="dashboard-title-wrap">
          <h1>
            Purchase Entry
            <span style="font-size:0.75rem; font-weight:700; padding:2px 8px; background:#eff6ff; color:#2563eb; border-radius:var(--radius-full); vertical-align:middle; border:1px solid #bfdbfe;">
              INWARD / PROCUREMENT
            </span>
          </h1>
          <p class="dashboard-subtitle">Record fabric inward challan, than-wise meter breakdown, supplier rate & procurement billing.</p>
        </div>

        <div class="dashboard-controls">
          <button class="btn btn-primary btn-sm" onclick="PurchaseView.openCreatePOModal()">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            + New Purchase Entry
          </button>
        </div>
      </div>

      <!-- 4 KPI Summary Cards -->
      <div class="kpi-grid" style="margin-bottom:24px;">
        <div class="kpi-card blue">
          <div class="kpi-top">
            <span class="kpi-title">Total Procurement Value</span>
            <div class="kpi-icon-wrap blue">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            </div>
          </div>
          <div class="kpi-value">${UI.formatCurrency(totalPurchased)}</div>
          <div class="kpi-bottom">
            <span class="kpi-trend up">${orders.length} Total Entries</span>
            <span class="kpi-period">All Inward Bills</span>
          </div>
        </div>

        <div class="kpi-card amber">
          <div class="kpi-top">
            <span class="kpi-title">Total Challan Entries</span>
            <div class="kpi-icon-wrap amber">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
          </div>
          <div class="kpi-value">${orders.length} Challans</div>
          <div class="kpi-bottom">
            <span class="kpi-trend up">Supplier Inward</span>
            <span class="kpi-period">Procurement</span>
          </div>
        </div>

        <div class="kpi-card emerald">
          <div class="kpi-top">
            <span class="kpi-title">Total Fabric Thans Inward</span>
            <div class="kpi-icon-wrap emerald">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
          </div>
          <div class="kpi-value">${totalThansAll > 0 ? totalThansAll + ' Thans' : orders.length + ' Lots'}</div>
          <div class="kpi-bottom">
            <span class="kpi-trend up">Inventory Stocked</span>
            <span class="kpi-period">Measured Rolls</span>
          </div>
        </div>

        <div class="kpi-card purple">
          <div class="kpi-top">
            <span class="kpi-title">Registered Suppliers</span>
            <div class="kpi-icon-wrap purple">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
          </div>
          <div class="kpi-value">${vendors.length} Vendors</div>
          <div class="kpi-bottom">
            <span class="kpi-trend up">Active Mills</span>
            <span class="kpi-period">Vendor Master</span>
          </div>
        </div>
      </div>

      <!-- Main Purchase Entries Table Card -->
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
            <div class="table-search-box" style="min-width:280px;">
              <svg class="table-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              <input type="text" class="table-search-input" id="po-search" placeholder="Search Challan #, vendor, fabric..." oninput="PurchaseView.filterPOTable()">
            </div>

            <select class="form-control form-control-sm" id="po-status-filter" style="width:170px;" onchange="PurchaseView.filterPOTable()">
              <option value="">All Statuses</option>
              <option value="Approved">Approved</option>
              <option value="Received">Received</option>
              <option value="Draft">Draft</option>
              <option value="Cancelled">Cancelled</option>
            </select>
          </div>

          <div class="table-toolbar-right">
            <button class="btn btn-primary btn-sm" onclick="PurchaseView.openCreatePOModal()">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              + New Purchase Entry
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table" id="po-table">
            <thead>
              <tr>
                <th>ENTRY / BILL NO</th>
                <th>SUPPLIER / VENDOR</th>
                <th>ENTRY DATE</th>
                <th>PRODUCT / FABRIC</th>
                <th style="text-align:center;">THANS</th>
                <th style="text-align:right;">TOTAL METERS</th>
                <th style="text-align:right;">GRAND TOTAL (₹)</th>
                <th>STATUS</th>
                <th style="text-align:right;">ACTIONS</th>
              </tr>
            </thead>
            <tbody>
              ${orders.length === 0 ? `
                <tr>
                  <td colspan="9" style="text-align:center; padding:48px 20px; color:var(--slate-400);">
                    <div style="display:inline-flex; align-items:center; justify-content:center; width:52px; height:52px; background:var(--primary-50); color:var(--primary-600); border-radius:var(--radius-full); margin-bottom:12px;">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    </div>
                    <div style="font-size:1.05rem; font-weight:700; color:var(--slate-700); margin-bottom:4px;">No Purchase Entries Found</div>
                    <div style="font-size:0.85rem; color:var(--slate-500); margin-bottom:16px;">Record your first fabric inward challan with than-wise meter breakdown.</div>
                    <button class="btn btn-primary btn-sm" onclick="PurchaseView.openCreatePOModal()">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                      + Create First Purchase Entry
                    </button>
                  </td>
                </tr>
              ` : orders.map(o => {
                const poId = o.po_number || o.poNumber || o.id;
                const dbId = o.dbId || o.id;
                const challanDisplay = o.challan_no ? `CH-${o.challan_no}` : poId;
                const vendorName = o.vendor_name || o.vendorName || (o.vendor ? o.vendor.name : 'Unknown Vendor');
                const dateStr = UI.formatDate(o.po_date || o.poDate || o.created_at);
                const fabricName = o.item_name || (o.items && o.items[0] ? (o.items[0].item_name || o.items[0].name) : 'Fabric Inward');
                
                let thansCount = o.total_thans;
                let metersTotal = o.total_meters;
                if (!thansCount && o.notes) {
                  try {
                    const parsed = typeof o.notes === 'string' ? JSON.parse(o.notes) : o.notes;
                    if (parsed.total_thans) thansCount = parsed.total_thans;
                    if (parsed.total_meters) metersTotal = parsed.total_meters;
                  } catch(e){}
                }
                if (!thansCount && o.thans && Array.isArray(o.thans)) thansCount = o.thans.length;
                if (!metersTotal && o.items && o.items[0]) metersTotal = o.items[0].ordered_qty || o.items[0].qty;

                const grandTotal = Number(o.grand_total || o.grandTotal || 0);
                const status = o.status || 'Approved';
                const statusColor = status === 'Received' ? 'emerald' : (status === 'Draft' ? 'slate' : 'blue');

                return `
                  <tr>
                    <td>
                      <span style="font-family:var(--font-mono, monospace); font-weight:800; color:var(--primary-700);">${challanDisplay}</span>
                      ${o.challan_no ? `<div style="font-size:0.75rem; color:var(--slate-400);">${poId}</div>` : ''}
                    </td>
                    <td>
                      <div style="font-weight:700; color:var(--slate-800);">${vendorName}</div>
                    </td>
                    <td>${dateStr}</td>
                    <td>
                      <span style="font-weight:600; color:var(--slate-700);">${fabricName}</span>
                    </td>
                    <td style="text-align:center;">
                      <span style="background:#f1f5f9; padding:3px 8px; border-radius:6px; font-weight:800; font-size:0.8rem; color:#334155;">
                        ${thansCount || 1} Thans
                      </span>
                    </td>
                    <td style="text-align:right; font-family:var(--font-mono, monospace); font-weight:800; color:#059669;">
                      ${metersTotal ? Number(metersTotal).toFixed(2) + ' Mtr' : '—'}
                    </td>
                    <td style="text-align:right; font-family:var(--font-mono, monospace); font-weight:800; color:var(--slate-900);">
                      ${UI.formatCurrency(grandTotal)}
                    </td>
                    <td>
                      <span class="badge badge-${statusColor}">${status}</span>
                    </td>
                    <td style="text-align:right;">
                      <div style="display:flex; justify-content:flex-end; gap:6px;">
                        <button type="button" class="btn btn-secondary btn-xs" onclick="PurchaseView.printPO('${poId}')" title="Print Inward Challan Slip">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                        </button>
                        <button type="button" class="btn btn-secondary btn-xs" onclick="PurchaseView.openEditPOModal('${poId}')" title="Edit Purchase Entry">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </button>
                        <button type="button" class="btn btn-secondary btn-xs" onclick="PurchaseView.deletePO('${poId}', ${dbId})" title="Delete Entry" style="color:#ef4444;">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        </button>
                      </div>
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

  postRenderOrders() {
    this.fetchFreshMasters();
  },

  // 3. CREATE PURCHASE ENTRY (FULL PAGE CHALLAN FORMAT)
  renderCreatePage() {
    const todayStr = new Date().toISOString().split("T")[0];
    this.currentThans = [];
    this.currentEditPoId = null;
    this.currentEditDbId = null;

    return `
      <div class="dashboard-top-bar" style="margin-bottom:20px;">
        <div class="dashboard-title-wrap">
          <div style="display:flex; align-items:center; gap:12px; margin-bottom:4px;">
            <button class="btn btn-secondary btn-sm" onclick="App.navigate('purchase', 'orders')" title="Back to Registry">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
              Back
            </button>
            <h1 style="margin:0;">
              New Purchase Entry
              <span style="font-size:0.75rem; font-weight:700; padding:2px 8px; background:#eff6ff; color:#2563eb; border-radius:var(--radius-full); vertical-align:middle; border:1px solid #bfdbfe;">
                CHALLAN ENTRY
              </span>
            </h1>
          </div>
          <p class="dashboard-subtitle">Record fabric inward challan, than-wise meter breakdown, supplier rate & procurement billing.</p>
        </div>

        <div class="dashboard-controls">
          <button type="button" class="btn btn-secondary" onclick="App.navigate('purchase', 'orders')">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="PurchaseView.submitPurchaseEntry()" style="box-shadow:0 2px 8px rgba(37, 99, 235, 0.3);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Save Purchase Entry
          </button>
        </div>
      </div>

      <form id="pe-form" onsubmit="event.preventDefault(); PurchaseView.submitPurchaseEntry();">
        <div style="display:flex; flex-direction:column; gap:20px;">

          <!-- 1. Supplier & Challan Information -->
          <div class="card" style="padding:22px;">
            <div style="border-bottom:1px solid var(--slate-100); padding-bottom:10px; margin-bottom:16px;">
              <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin:0; color:var(--slate-800); display:flex; align-items:center; gap:8px;">
                <span style="width:24px; height:24px; border-radius:6px; background:#eff6ff; color:#2563eb; display:inline-flex; align-items:center; justify-content:center; font-size:0.75rem;">1</span>
                Challan & Supplier Information
              </h3>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:18px;">
              <!-- Vendor Select -->
              <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-weight:700; color:var(--slate-800);">Vendor / Supplier <span style="color:#ef4444;">*</span></label>
                <select class="form-control" id="pe-vendor" required>
                  <option value="">Loading active vendors...</option>
                </select>
              </div>

              <!-- Supplier Challan / Bill No -->
              <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-weight:700; color:var(--slate-800);">Supplier Challan / Bill No. <span style="color:#ef4444;">*</span></label>
                <input type="text" class="form-control" id="pe-challan-no" required placeholder="e.g. 1076 / CH-9821" style="font-weight:700;">
              </div>

              <!-- Entry Date -->
              <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-weight:700; color:var(--slate-800);">Challan / Entry Date <span style="color:#ef4444;">*</span></label>
                <input type="date" class="form-control" id="pe-date" required value="${todayStr}">
              </div>
            </div>
          </div>

          <!-- 2. Product / Fabric Quality (Single Selection) -->
          <div class="card" style="padding:22px;">
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--slate-100); padding-bottom:10px; margin-bottom:16px;">
              <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin:0; color:var(--slate-800); display:flex; align-items:center; gap:8px;">
                <span style="width:24px; height:24px; border-radius:6px; background:#ecfdf5; color:#059669; display:inline-flex; align-items:center; justify-content:center; font-size:0.75rem;">2</span>
                Product / Fabric Quality & Rate (Single Selection)
              </h3>
              <span style="font-size:0.75rem; color:#64748b; font-weight:600; background:#f1f5f9; padding:3px 10px; border-radius:8px;">
                Product selected once • Multiple Than meters below
              </span>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:18px;">
              <!-- Item Select -->
              <div class="form-group" style="margin-bottom:0; grid-column:span 2;">
                <label class="form-label" style="font-weight:700; color:var(--slate-800);">Fabric Quality / Item SKU <span style="color:#ef4444;">*</span></label>
                <select class="form-control" id="pe-item" required onchange="PurchaseView.onItemChange(this)">
                  <option value="">Loading fabric items...</option>
                </select>
              </div>

              <!-- Rate Per Meter -->
              <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-weight:700; color:var(--slate-800);">Rate Per Meter (₹) <span style="color:#ef4444;">*</span></label>
                <div style="position:relative;">
                  <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); font-weight:700; color:#64748b;">₹</span>
                  <input type="number" step="0.01" min="0" id="pe-rate" class="form-control" required placeholder="0.00" value="35.00" style="padding-left:28px; font-weight:700; font-size:1rem;" oninput="PurchaseView.calculateTotals()">
                </div>
              </div>

              <!-- GST Rate -->
              <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-weight:700; color:var(--slate-800);">GST Tax Rate</label>
                <select id="pe-tax" class="form-control" onchange="PurchaseView.calculateTotals()">
                  <option value="5" selected>5% GST (Standard Fabric)</option>
                  <option value="0">0% Excluded / Exempted</option>
                  <option value="12">12% GST</option>
                  <option value="18">18% GST</option>
                </select>
              </div>
            </div>
          </div>

          <!-- 3. Than-Wise Meter Breakdown (Challan Format) -->
          <div class="card" style="padding:22px;">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; border-bottom:1px solid var(--slate-100); padding-bottom:12px; margin-bottom:16px;">
              <div>
                <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin:0; color:var(--slate-800); display:flex; align-items:center; gap:8px;">
                  <span style="width:24px; height:24px; border-radius:6px; background:#faf5ff; color:#7c3aed; display:inline-flex; align-items:center; justify-content:center; font-size:0.75rem;">3</span>
                  Than-wise Meter Breakdown (Challan Format)
                </h3>
                <p style="margin:3px 0 0; font-size:0.8rem; color:var(--slate-500);">Enter individual roll / than meter values. Meters are summed together for overall calculation.</p>
              </div>

              <!-- Badges -->
              <div style="display:flex; gap:10px; align-items:center;">
                <div style="background:#f1f5f9; border:1px solid #cbd5e1; border-radius:10px; padding:6px 14px;">
                  <span style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">Total Thans: </span>
                  <strong id="badge-total-thans" style="color:#0f172a; font-size:0.95rem;">0 Thans</strong>
                </div>
                <div style="background:#ecfdf5; border:1px solid #a7f3d0; border-radius:10px; padding:6px 14px;">
                  <span style="font-size:0.75rem; color:#065f46; font-weight:700; text-transform:uppercase;">Total Meters: </span>
                  <strong id="badge-total-meters" style="color:#059669; font-size:1.05rem;">0.00 Mtr</strong>
                </div>
              </div>
            </div>

            <!-- Fast Entry Toolbar -->
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:12px 16px; margin-bottom:16px; display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
              <div style="flex:1; min-width:240px; display:flex; gap:8px;">
                <input type="number" step="0.01" min="0" id="quick-than-input" class="form-control" placeholder="Type meter reading (e.g. 148) and press Enter" onkeydown="if(event.key==='Enter'){event.preventDefault();PurchaseView.addSingleThan();}">
                <button type="button" class="btn btn-primary" onclick="PurchaseView.addSingleThan()" style="white-space:nowrap; font-weight:700;">
                  + Add Than
                </button>
              </div>

              <div style="display:flex; gap:8px;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="PurchaseView.openPasteModal()" style="font-weight:700;">
                  Paste Multi-Thans
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="PurchaseView.clearAllThans()" style="font-weight:700; color:#dc2626;">
                  Clear All
                </button>
              </div>
            </div>

            <!-- Empty Box -->
            <div id="than-container-empty" style="text-align:center; padding:32px 16px; color:#94a3b8; border:2px dashed #e2e8f0; border-radius:12px;">
              <div style="font-weight:700; font-size:0.95rem; color:#64748b; margin-bottom:4px;">No Thans added yet</div>
              <p style="font-size:0.8rem; margin:0 0 10px;">Type meter reading above and press Enter, or click Paste Multi-Thans.</p>
              <button type="button" class="btn btn-secondary btn-xs" onclick="PurchaseView.loadSampleChallan()">Load Sample Slip (14 Thans)</button>
            </div>

            <!-- Dual Column Visual Sheet -->
            <div id="than-container-sheet" style="display:none; grid-template-columns:1fr 1fr; gap:18px;">
              <!-- Col 1 -->
              <div style="background:#fbfcfe; border:1px solid #e2e8f0; border-radius:12px; padding:14px;">
                <div style="font-weight:700; font-size:0.8rem; color:#64748b; text-transform:uppercase; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-bottom:10px; display:flex; justify-content:space-between;">
                  <span>Than Column 1</span>
                  <span id="col1-subtotal-badge" style="color:#2563eb; font-weight:800;">0.00 Mtr</span>
                </div>
                <div id="col1-thans-list" style="display:flex; flex-direction:column; gap:8px;"></div>
              </div>

              <!-- Col 2 -->
              <div style="background:#fbfcfe; border:1px solid #e2e8f0; border-radius:12px; padding:14px;">
                <div style="font-weight:700; font-size:0.8rem; color:#64748b; text-transform:uppercase; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-bottom:10px; display:flex; justify-content:space-between;">
                  <span>Than Column 2</span>
                  <span id="col2-subtotal-badge" style="color:#2563eb; font-weight:800;">0.00 Mtr</span>
                </div>
                <div id="col2-thans-list" style="display:flex; flex-direction:column; gap:8px;"></div>
              </div>
            </div>

            <!-- Grand Calculation Summary Card -->
            <div style="display:flex; justify-content:flex-end; margin-top:20px;">
              <div style="width:380px; background:linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border:1px solid #e2e8f0; border-radius:14px; padding:16px; display:flex; flex-direction:column; gap:8px;">
                <div style="font-size:0.8rem; font-weight:800; color:var(--slate-700); text-transform:uppercase; border-bottom:1px solid #cbd5e1; padding-bottom:6px;">
                  Challan Inward Billing Summary
                </div>
                <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
                  <span style="color:var(--slate-600);">Total Thans:</span>
                  <strong id="summary-than-count" style="color:var(--slate-900);">0 Thans</strong>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
                  <span style="color:var(--slate-600);">Total Inward Meters:</span>
                  <strong id="summary-total-meters" style="color:#059669; font-weight:800;">0.00 Mtr</strong>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
                  <span style="color:var(--slate-600);">Rate Per Meter:</span>
                  <span id="summary-rate" style="font-weight:700; color:var(--slate-800);">₹0.00 / Mtr</span>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
                  <span style="color:var(--slate-600);">Taxable Subtotal:</span>
                  <strong id="summary-subtotal" style="font-family:var(--font-mono, monospace);">₹0.00</strong>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
                  <span id="summary-tax-label" style="color:var(--slate-600);">GST Tax (5%):</span>
                  <strong id="summary-tax" style="font-family:var(--font-mono, monospace);">₹0.00</strong>
                </div>
                <div style="border-top:2px dashed #cbd5e1; padding-top:8px; display:flex; justify-content:space-between; align-items:baseline; font-size:1.15rem;">
                  <span style="font-weight:800; color:var(--slate-900);">Grand Total:</span>
                  <span id="summary-grand" style="font-weight:800; color:#2563eb; font-family:var(--font-mono, monospace);">₹0.00</span>
                </div>
              </div>
            </div>
          </div>

          <!-- 4. Notes / Vehicle -->
          <div class="card" style="padding:20px;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800);">Challan Inward Remarks / Vehicle / Transport Details</label>
            <textarea id="pe-notes" class="form-control" rows="2" placeholder="Specify transport carrier, LR number, roll inspection remarks..."></textarea>
          </div>

          <!-- Bottom Action Buttons -->
          <div style="display:flex; justify-content:flex-end; gap:12px; margin-bottom:30px;">
            <button type="button" class="btn btn-secondary" onclick="App.navigate('purchase', 'orders')">Cancel & Discard</button>
            <button type="button" class="btn btn-primary" onclick="PurchaseView.submitPurchaseEntry()" style="box-shadow:0 2px 8px rgba(37, 99, 235, 0.3);">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
              Save Purchase Entry
            </button>
          </div>
        </div>
      </form>
    `;
  },

  postRenderCreate() {
    this.populateVendorDropdown();
    this.populateItemDropdown();
    this.fetchFreshMasters();
    this.renderThanSheet();
    this.calculateTotals();
  },

  // 4. EDIT PURCHASE ENTRY
  renderEditPage(poId) {
    const orders = ERPState.data.purchaseOrders || [];
    const targetId = poId || PurchaseView.currentEditPoId || window.INITIAL_ROUTE?.targetId;
    const po = orders.find(o => (o.po_number || o.poNumber || o.id) === targetId || String(o.dbId || o.id) === String(targetId));

    if (!po) {
      return `
        <div class="card" style="padding:48px; text-align:center;">
          <h3>Purchase Entry Not Found</h3>
          <p class="text-muted">The requested purchase inward entry could not be located.</p>
          <button class="btn btn-primary btn-sm mt-4" onclick="App.navigate('purchase', 'orders')">Return to Purchase Registry</button>
        </div>
      `;
    }

    PurchaseView.currentEditPoId = po.po_number || po.poNumber || po.id;
    PurchaseView.currentEditDbId = po.dbId || po.id;

    // Extract Thans list
    this.currentThans = [];
    if (po.thans && Array.isArray(po.thans)) {
      this.currentThans = [...po.thans];
    } else if (po.notes) {
      try {
        const parsed = typeof po.notes === 'string' ? JSON.parse(po.notes) : po.notes;
        if (parsed.thans && Array.isArray(parsed.thans)) {
          this.currentThans = [...parsed.thans];
        }
      } catch (e) {}
    }
    if (this.currentThans.length === 0 && po.items && po.items.length > 0) {
      const q = parseFloat(po.items[0].ordered_qty || po.items[0].qty || 0);
      if (q > 0) this.currentThans = [q];
    }

    const challanNo = po.challan_no || '';
    const dateVal = po.po_date || po.poDate || new Date().toISOString().split("T")[0];
    const rateVal = po.rate || (po.items && po.items[0] ? (po.items[0].rate || 35.00) : 35.00);
    const taxVal = po.tax_percent !== undefined ? po.tax_percent : (po.items && po.items[0] ? po.items[0].tax_percent : 5);
    const notesVal = typeof po.notes === 'string' && po.notes.startsWith('{') ? (JSON.parse(po.notes).user_notes || '') : (po.notes || '');

    return `
      <div class="dashboard-top-bar" style="margin-bottom:20px;">
        <div class="dashboard-title-wrap">
          <div style="display:flex; align-items:center; gap:12px; margin-bottom:4px;">
            <button class="btn btn-secondary btn-sm" onclick="App.navigate('purchase', 'orders')" title="Back to Registry">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
              Back
            </button>
            <h1 style="margin:0;">
              Edit Purchase Entry (${PurchaseView.currentEditPoId})
              <span style="font-size:0.75rem; font-weight:700; padding:2px 8px; background:#eff6ff; color:#2563eb; border-radius:var(--radius-full); vertical-align:middle; border:1px solid #bfdbfe;">
                EDITING
              </span>
            </h1>
          </div>
          <p class="dashboard-subtitle">Update supplier inward challan number, meter readings, or fabric rate.</p>
        </div>

        <div class="dashboard-controls">
          <button type="button" class="btn btn-secondary" onclick="App.navigate('purchase', 'orders')">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="PurchaseView.submitPurchaseEntry()" style="box-shadow:0 2px 8px rgba(37, 99, 235, 0.3);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Save Changes
          </button>
        </div>
      </div>

      <form id="pe-form" onsubmit="event.preventDefault(); PurchaseView.submitPurchaseEntry();">
        <div style="display:flex; flex-direction:column; gap:20px;">

          <!-- 1. Supplier & Challan -->
          <div class="card" style="padding:22px;">
            <div style="border-bottom:1px solid var(--slate-100); padding-bottom:10px; margin-bottom:16px;">
              <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin:0; color:var(--slate-800); display:flex; align-items:center; gap:8px;">
                <span style="width:24px; height:24px; border-radius:6px; background:#eff6ff; color:#2563eb; display:inline-flex; align-items:center; justify-content:center; font-size:0.75rem;">1</span>
                Challan & Supplier Information
              </h3>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:18px;">
              <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-weight:700; color:var(--slate-800);">Vendor / Supplier <span style="color:#ef4444;">*</span></label>
                <select class="form-control" id="pe-vendor" required>
                  <option value="${po.vendor_name || ''}" selected>${po.vendor_name || '-- Select Vendor --'}</option>
                </select>
              </div>

              <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-weight:700; color:var(--slate-800);">Supplier Challan / Bill No. <span style="color:#ef4444;">*</span></label>
                <input type="text" class="form-control" id="pe-challan-no" required value="${challanNo}" style="font-weight:700;">
              </div>

              <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-weight:700; color:var(--slate-800);">Challan / Entry Date <span style="color:#ef4444;">*</span></label>
                <input type="date" class="form-control" id="pe-date" required value="${dateVal}">
              </div>
            </div>
          </div>

          <!-- 2. Product / Fabric Quality (Single Selection) -->
          <div class="card" style="padding:22px;">
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--slate-100); padding-bottom:10px; margin-bottom:16px;">
              <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin:0; color:var(--slate-800); display:flex; align-items:center; gap:8px;">
                <span style="width:24px; height:24px; border-radius:6px; background:#ecfdf5; color:#059669; display:inline-flex; align-items:center; justify-content:center; font-size:0.75rem;">2</span>
                Product / Fabric Quality & Rate (Single Selection)
              </h3>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:18px;">
              <div class="form-group" style="margin-bottom:0; grid-column:span 2;">
                <label class="form-label" style="font-weight:700; color:var(--slate-800);">Fabric Quality / Item SKU <span style="color:#ef4444;">*</span></label>
                <select class="form-control" id="pe-item" required onchange="PurchaseView.onItemChange(this)">
                  <option value="${po.item_name || ''}" selected>${po.item_name || '-- Select Item --'}</option>
                </select>
              </div>

              <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-weight:700; color:var(--slate-800);">Rate Per Meter (₹) <span style="color:#ef4444;">*</span></label>
                <div style="position:relative;">
                  <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); font-weight:700; color:#64748b;">₹</span>
                  <input type="number" step="0.01" min="0" id="pe-rate" class="form-control" required value="${rateVal}" style="padding-left:28px; font-weight:700;" oninput="PurchaseView.calculateTotals()">
                </div>
              </div>

              <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-weight:700; color:var(--slate-800);">GST Tax Rate</label>
                <select id="pe-tax" class="form-control" onchange="PurchaseView.calculateTotals()">
                  <option value="5" ${Number(taxVal) === 5 ? 'selected' : ''}>5% GST (Standard Fabric)</option>
                  <option value="0" ${Number(taxVal) === 0 ? 'selected' : ''}>0% Excluded / Exempted</option>
                  <option value="12" ${Number(taxVal) === 12 ? 'selected' : ''}>12% GST</option>
                  <option value="18" ${Number(taxVal) === 18 ? 'selected' : ''}>18% GST</option>
                </select>
              </div>
            </div>
          </div>

          <!-- 3. Than-wise Meter Breakdown -->
          <div class="card" style="padding:22px;">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; border-bottom:1px solid var(--slate-100); padding-bottom:12px; margin-bottom:16px;">
              <div>
                <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin:0; color:var(--slate-800);">
                  3. Than-wise Meter Breakdown (Challan Format)
                </h3>
              </div>

              <div style="display:flex; gap:10px; align-items:center;">
                <div style="background:#f1f5f9; border:1px solid #cbd5e1; border-radius:10px; padding:6px 14px;">
                  <span style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">Total Thans: </span>
                  <strong id="badge-total-thans" style="color:#0f172a; font-size:0.95rem;">0 Thans</strong>
                </div>
                <div style="background:#ecfdf5; border:1px solid #a7f3d0; border-radius:10px; padding:6px 14px;">
                  <span style="font-size:0.75rem; color:#065f46; font-weight:700; text-transform:uppercase;">Total Meters: </span>
                  <strong id="badge-total-meters" style="color:#059669; font-size:1.05rem;">0.00 Mtr</strong>
                </div>
              </div>
            </div>

            <!-- Fast Entry Toolbar -->
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:12px 16px; margin-bottom:16px; display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
              <div style="flex:1; min-width:240px; display:flex; gap:8px;">
                <input type="number" step="0.01" min="0" id="quick-than-input" class="form-control" placeholder="Type meter reading (e.g. 148) and press Enter" onkeydown="if(event.key==='Enter'){event.preventDefault();PurchaseView.addSingleThan();}">
                <button type="button" class="btn btn-primary" onclick="PurchaseView.addSingleThan()" style="white-space:nowrap; font-weight:700;">
                  + Add Than
                </button>
              </div>

              <div style="display:flex; gap:8px;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="PurchaseView.openPasteModal()" style="font-weight:700;">
                  Paste Multi-Thans
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="PurchaseView.clearAllThans()" style="font-weight:700; color:#dc2626;">
                  Clear All
                </button>
              </div>
            </div>

            <!-- Empty Box -->
            <div id="than-container-empty" style="text-align:center; padding:32px 16px; color:#94a3b8; border:2px dashed #e2e8f0; border-radius:12px; display:none;">
              <div style="font-weight:700; font-size:0.95rem; color:#64748b; margin-bottom:4px;">No Thans recorded</div>
              <p style="font-size:0.8rem; margin:0 0 10px;">Type meter reading above and press Enter, or click Paste Multi-Thans.</p>
            </div>

            <!-- Dual Column Visual Sheet -->
            <div id="than-container-sheet" style="display:grid; grid-template-columns:1fr 1fr; gap:18px;">
              <div style="background:#fbfcfe; border:1px solid #e2e8f0; border-radius:12px; padding:14px;">
                <div style="font-weight:700; font-size:0.8rem; color:#64748b; text-transform:uppercase; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-bottom:10px; display:flex; justify-content:space-between;">
                  <span>Than Column 1</span>
                  <span id="col1-subtotal-badge" style="color:#2563eb; font-weight:800;">0.00 Mtr</span>
                </div>
                <div id="col1-thans-list" style="display:flex; flex-direction:column; gap:8px;"></div>
              </div>

              <div style="background:#fbfcfe; border:1px solid #e2e8f0; border-radius:12px; padding:14px;">
                <div style="font-weight:700; font-size:0.8rem; color:#64748b; text-transform:uppercase; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-bottom:10px; display:flex; justify-content:space-between;">
                  <span>Than Column 2</span>
                  <span id="col2-subtotal-badge" style="color:#2563eb; font-weight:800;">0.00 Mtr</span>
                </div>
                <div id="col2-thans-list" style="display:flex; flex-direction:column; gap:8px;"></div>
              </div>
            </div>

            <!-- Summary Card -->
            <div style="display:flex; justify-content:flex-end; margin-top:20px;">
              <div style="width:380px; background:linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border:1px solid #e2e8f0; border-radius:14px; padding:16px; display:flex; flex-direction:column; gap:8px;">
                <div style="font-size:0.8rem; font-weight:800; color:var(--slate-700); text-transform:uppercase; border-bottom:1px solid #cbd5e1; padding-bottom:6px;">
                  Challan Inward Billing Summary
                </div>
                <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
                  <span style="color:var(--slate-600);">Total Thans:</span>
                  <strong id="summary-than-count" style="color:var(--slate-900);">0 Thans</strong>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
                  <span style="color:var(--slate-600);">Total Inward Meters:</span>
                  <strong id="summary-total-meters" style="color:#059669; font-weight:800;">0.00 Mtr</strong>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
                  <span style="color:var(--slate-600);">Rate Per Meter:</span>
                  <span id="summary-rate" style="font-weight:700; color:var(--slate-800);">₹0.00 / Mtr</span>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
                  <span style="color:var(--slate-600);">Taxable Subtotal:</span>
                  <strong id="summary-subtotal" style="font-family:var(--font-mono, monospace);">₹0.00</strong>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
                  <span id="summary-tax-label" style="color:var(--slate-600);">GST Tax:</span>
                  <strong id="summary-tax" style="font-family:var(--font-mono, monospace);">₹0.00</strong>
                </div>
                <div style="border-top:2px dashed #cbd5e1; padding-top:8px; display:flex; justify-content:space-between; align-items:baseline; font-size:1.15rem;">
                  <span style="font-weight:800; color:var(--slate-900);">Grand Total:</span>
                  <span id="summary-grand" style="font-weight:800; color:#2563eb; font-family:var(--font-mono, monospace);">₹0.00</span>
                </div>
              </div>
            </div>
          </div>

          <!-- 4. Notes -->
          <div class="card" style="padding:20px;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800);">Challan Inward Remarks / Vehicle / Transport Details</label>
            <textarea id="pe-notes" class="form-control" rows="2">${notesVal}</textarea>
          </div>

          <!-- Bottom Action Buttons -->
          <div style="display:flex; justify-content:flex-end; gap:12px; margin-bottom:30px;">
            <button type="button" class="btn btn-secondary" onclick="App.navigate('purchase', 'orders')">Cancel & Discard</button>
            <button type="button" class="btn btn-primary" onclick="PurchaseView.submitPurchaseEntry()" style="box-shadow:0 2px 8px rgba(37, 99, 235, 0.3);">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              Save Changes
            </button>
          </div>
        </div>
      </form>
    `;
  },

  postRenderEdit() {
    const orders = ERPState.data.purchaseOrders || [];
    const targetId = PurchaseView.currentEditPoId || window.INITIAL_ROUTE?.targetId;
    const po = orders.find(o => (o.po_number || o.poNumber || o.id) === targetId || String(o.dbId || o.id) === String(targetId));

    this.populateVendorDropdown(po ? po.vendor_name : null);
    this.populateItemDropdown(po ? po.item_name : null);
    this.fetchFreshMasters();
    this.renderThanSheet();
    this.calculateTotals();
  },

  // 5. THAN-WISE VISUAL SHEET CONTROLLER & CALCULATOR
  addSingleThan() {
    const input = document.getElementById('quick-than-input');
    if (!input) return;
    const val = parseFloat(input.value);
    if (!isNaN(val) && val > 0) {
      this.currentThans.push(Math.round(val * 100) / 100);
      input.value = '';
      input.focus();
      this.renderThanSheet();
      this.calculateTotals();
    } else {
      input.focus();
    }
  },

  updateThanMeter(index, value) {
    const val = parseFloat(value);
    if (!isNaN(val) && val >= 0) {
      this.currentThans[index] = Math.round(val * 100) / 100;
    } else {
      this.currentThans[index] = 0;
    }
    this.updateColumnSubtotals();
    this.calculateTotals();
  },

  removeThan(index) {
    this.currentThans.splice(index, 1);
    this.renderThanSheet();
    this.calculateTotals();
  },

  clearAllThans() {
    if (this.currentThans.length === 0) return;
    UI.showConfirm({
      title: "Clear All Thans?",
      message: "Are you sure you want to remove all recorded than meter readings?",
      confirmText: "Yes, Clear All",
      isDanger: true,
      onConfirm: () => {
        PurchaseView.currentThans = [];
        PurchaseView.renderThanSheet();
        PurchaseView.calculateTotals();
      }
    });
  },

  loadSampleChallan() {
    this.currentThans = [148, 72.50, 112.50, 103.50, 110.50, 105, 99.50, 116, 126.50, 89.50, 117.50, 98, 117, 99.50];
    const challanInput = document.getElementById('pe-challan-no');
    if (challanInput && !challanInput.value) challanInput.value = '1076';
    this.renderThanSheet();
    this.calculateTotals();
  },

  // Paste Multi-Thans Modal
  openPasteModal() {
    const modalHtml = `
      <div>
        <p style="font-size:0.85rem; color:#64748b; margin-top:0; margin-bottom:12px;">
          Copy and paste raw meter readings from WhatsApp, supplier paper challan slip, or Excel sheet. Any space, comma, or newline separated numbers will be recognized automatically.
        </p>
        <textarea id="paste-than-textarea" class="form-control" rows="8" placeholder="Example:
148  72.50  112.50  103.50
110.50  105  99.50  116
126.50  89.50  117.50  98" style="font-family:var(--font-mono, monospace); font-size:0.9rem;"></textarea>
      </div>
    `;

    UI.openModal({
      title: "Paste Multi-Thans Meter Readings",
      content: modalHtml,
      footer: `
        <button class="btn btn-secondary" onclick="UI.closeModal()">Cancel</button>
        <button class="btn btn-primary" onclick="PurchaseView.applyPastedThans()">Import Thans</button>
      `,
      size: "modal-md"
    });

    setTimeout(() => {
      document.getElementById('paste-than-textarea')?.focus();
    }, 100);
  },

  applyPastedThans() {
    const textarea = document.getElementById('paste-than-textarea');
    if (!textarea) return;
    const text = textarea.value;
    if (text) {
      const matches = text.match(/\d+(?:\.\d+)?/g);
      if (matches && matches.length > 0) {
        matches.forEach(m => {
          const val = parseFloat(m);
          if (val > 0) {
            this.currentThans.push(Math.round(val * 100) / 100);
          }
        });
        this.renderThanSheet();
        this.calculateTotals();
        UI.showToast("Thans Added", `Successfully imported ${matches.length} than meters.`, "success");
      }
    }
    UI.closeModal();
  },

  renderThanSheet() {
    const emptyBox = document.getElementById('than-container-empty');
    const sheetBox = document.getElementById('than-container-sheet');
    const col1 = document.getElementById('col1-thans-list');
    const col2 = document.getElementById('col2-thans-list');

    if (!emptyBox || !sheetBox || !col1 || !col2) return;

    col1.innerHTML = '';
    col2.innerHTML = '';

    if (this.currentThans.length === 0) {
      emptyBox.style.display = 'block';
      sheetBox.style.display = 'none';
      return;
    }

    emptyBox.style.display = 'none';
    sheetBox.style.display = 'grid';

    const midpoint = Math.ceil(this.currentThans.length / 2);

    this.currentThans.forEach((meter, i) => {
      const row = document.createElement('div');
      row.style.cssText = 'display:flex; align-items:center; gap:8px; background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:6px 10px;';
      row.innerHTML = `
        <span style="font-weight:700; font-size:0.75rem; color:#64748b; width:64px;">Than #${i + 1}</span>
        <div style="flex:1; position:relative;">
          <input type="number" step="0.01" min="0" value="${meter || ''}" 
            class="form-control form-control-sm" 
            style="font-weight:700; font-size:0.9rem; padding:4px 8px;"
            oninput="PurchaseView.updateThanMeter(${i}, this.value)"
            onfocus="this.select()">
        </div>
        <span style="font-size:0.75rem; color:#94a3b8; font-weight:600;">Mtr</span>
        <button type="button" onclick="PurchaseView.removeThan(${i})" title="Remove than" style="background:none; border:none; color:#ef4444; font-size:1.15rem; cursor:pointer; line-height:1; padding:2px 4px;">&times;</button>
      `;

      if (i < midpoint) {
        col1.appendChild(row);
      } else {
        col2.appendChild(row);
      }
    });

    this.updateColumnSubtotals();
  },

  updateColumnSubtotals() {
    const midpoint = Math.ceil(this.currentThans.length / 2);
    let c1 = 0;
    let c2 = 0;
    this.currentThans.forEach((m, i) => {
      if (i < midpoint) c1 += (m || 0);
      else c2 += (m || 0);
    });

    const c1Badge = document.getElementById('col1-subtotal-badge');
    const c2Badge = document.getElementById('col2-subtotal-badge');
    if (c1Badge) c1Badge.textContent = c1.toFixed(2) + ' Mtr (' + Math.min(midpoint, this.currentThans.length) + ' Thans)';
    if (c2Badge) c2Badge.textContent = c2.toFixed(2) + ' Mtr (' + Math.max(0, this.currentThans.length - midpoint) + ' Thans)';
  },

  calculateTotals() {
    const totalMeters = this.currentThans.reduce((acc, v) => acc + (parseFloat(v) || 0), 0);
    const totalThans = this.currentThans.length;
    const rate = parseFloat(document.getElementById('pe-rate')?.value) || 0;
    const taxPct = parseFloat(document.getElementById('pe-tax')?.value) || 0;

    const subtotal = Math.round(totalMeters * rate * 100) / 100;
    const taxAmount = Math.round((subtotal * taxPct / 100.0) * 100) / 100;
    const grandTotal = Math.round((subtotal + taxAmount) * 100) / 100;

    // Badges
    const badgeThans = document.getElementById('badge-total-thans');
    const badgeMeters = document.getElementById('badge-total-meters');
    if (badgeThans) badgeThans.textContent = totalThans + ' Thans';
    if (badgeMeters) badgeMeters.textContent = totalMeters.toFixed(2) + ' Mtr';

    // Summary Card
    const sThan = document.getElementById('summary-than-count');
    const sMtr = document.getElementById('summary-total-meters');
    const sRate = document.getElementById('summary-rate');
    const sSub = document.getElementById('summary-subtotal');
    const sTaxLabel = document.getElementById('summary-tax-label');
    const sTax = document.getElementById('summary-tax');
    const sGrand = document.getElementById('summary-grand');

    if (sThan) sThan.textContent = totalThans + ' Thans';
    if (sMtr) sMtr.textContent = totalMeters.toFixed(2) + ' Mtr';
    if (sRate) sRate.textContent = '₹' + rate.toFixed(2) + ' / Mtr';
    if (sSub) sSub.textContent = UI.formatCurrency(subtotal);
    if (sTaxLabel) sTaxLabel.textContent = `GST Tax (${taxPct}%):`;
    if (sTax) sTax.textContent = UI.formatCurrency(taxAmount);
    if (sGrand) sGrand.textContent = UI.formatCurrency(grandTotal);
  },

  // 6. SUBMIT PURCHASE ENTRY
  submitPurchaseEntry() {
    const vendorSelect = document.getElementById('pe-vendor');
    const vendorName = vendorSelect ? vendorSelect.value : '';
    const vendorOpt = vendorSelect ? vendorSelect.options[vendorSelect.selectedIndex] : null;
    const vendorId = vendorOpt ? vendorOpt.getAttribute('data-id') : null;

    const challanNo = document.getElementById('pe-challan-no')?.value.trim();
    const poDate = document.getElementById('pe-date')?.value;

    const itemSelect = document.getElementById('pe-item');
    const itemName = itemSelect ? itemSelect.value : '';
    const itemOpt = itemSelect ? itemSelect.options[itemSelect.selectedIndex] : null;
    const itemId = itemOpt ? itemOpt.getAttribute('data-id') : null;
    const itemCode = itemOpt ? itemOpt.getAttribute('data-code') : '';

    const rate = parseFloat(document.getElementById('pe-rate')?.value) || 0;
    const taxPercent = parseFloat(document.getElementById('pe-tax')?.value) || 0;
    const notes = document.getElementById('pe-notes')?.value || '';

    if (!vendorName) {
      return UI.showToast("Required Field", "Please select a vendor / supplier", "error");
    }
    if (!challanNo) {
      return UI.showToast("Required Field", "Please enter the Supplier Challan / Bill No", "error");
    }
    if (!poDate) {
      return UI.showToast("Required Field", "Please enter the Challan date", "error");
    }
    if (!itemName) {
      return UI.showToast("Required Field", "Please select the Fabric Quality / Item SKU", "error");
    }
    if (this.currentThans.length === 0) {
      return UI.showToast("Thans Required", "Please record at least one Than meter reading", "warning");
    }

    const cleanThans = this.currentThans.filter(m => parseFloat(m) > 0);
    const totalMeters = Math.round(cleanThans.reduce((a, b) => a + b, 0) * 100) / 100;
    const totalThans = cleanThans.length;
    const subtotal = Math.round(totalMeters * rate * 100) / 100;
    const taxTotal = Math.round((subtotal * taxPercent / 100.0) * 100) / 100;
    const grandTotal = Math.round((subtotal + taxTotal) * 100) / 100;

    const payload = {
      vendor_name: vendorName,
      vendor_id: vendorId,
      challan_no: challanNo,
      po_date: poDate,
      item_name: itemName,
      item_id: itemId,
      item_code: itemCode,
      rate: rate,
      tax_percent: taxPercent,
      thans: cleanThans,
      total_thans: totalThans,
      total_meters: totalMeters,
      subtotal: subtotal,
      tax_total: taxTotal,
      grand_total: grandTotal,
      status: 'Approved',
      payment_status: 'Pending',
      notes: notes,
      items: [
        {
          item_name: itemName,
          item_code: itemCode,
          ordered_qty: totalMeters,
          unit: 'Meters',
          rate: rate,
          tax_percent: taxPercent,
          total_amount: grandTotal
        }
      ]
    };

    const isEdit = Boolean(PurchaseView.currentEditPoId);
    const targetDbId = PurchaseView.currentEditDbId;
    const targetPoId = PurchaseView.currentEditPoId;

    if (isEdit) {
      fetch(`/purchase/orders/${targetDbId || targetPoId}`, {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || ""
        },
        body: JSON.stringify(payload)
      })
      .then(res => res.json())
      .then(() => {
        UI.showToast("Purchase Entry Updated", `Challan ${challanNo} (${targetPoId}) updated successfully.`, "success");
        this.updateLocalState(targetPoId, payload, true);
        App.navigate("purchase", "orders");
      })
      .catch(() => {
        this.updateLocalState(targetPoId, payload, true);
        UI.showToast("Updated Locally", `Purchase Entry ${challanNo} updated.`, "success");
        App.navigate("purchase", "orders");
      });
    } else {
      fetch("/purchase/orders", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || ""
        },
        body: JSON.stringify(payload)
      })
      .then(res => res.json())
      .then(data => {
        const newPoId = data.po_number || data.poNumber || `PE-2026-${String(Date.now()).slice(-4)}`;
        UI.showToast("Purchase Entry Saved", `Challan ${challanNo} (${newPoId}) saved successfully!`, "success");
        this.updateLocalState(newPoId, payload, false);
        App.navigate("purchase", "orders");
      })
      .catch(() => {
        const fallbackPoId = `PE-2026-${String(Date.now()).slice(-4)}`;
        this.updateLocalState(fallbackPoId, payload, false);
        UI.showToast("Saved Locally", `Purchase Entry ${challanNo} saved locally.`, "success");
        App.navigate("purchase", "orders");
      });
    }
  },

  updateLocalState(poId, payload, isEdit) {
    if (!ERPState.data.purchaseOrders) ERPState.data.purchaseOrders = [];
    if (isEdit) {
      const idx = ERPState.data.purchaseOrders.findIndex(o => (o.po_number || o.id) === poId);
      if (idx !== -1) {
        ERPState.data.purchaseOrders[idx] = { ...ERPState.data.purchaseOrders[idx], ...payload, po_number: poId };
      }
    } else {
      ERPState.data.purchaseOrders.unshift({
        id: poId,
        po_number: poId,
        ...payload,
        created_at: new Date().toISOString()
      });
    }
    ERPState.saveState();
  },

  deletePO(poId, dbId) {
    UI.showConfirm({
      title: "Delete Purchase Entry?",
      message: `Are you sure you want to delete purchase entry <strong>${poId}</strong>? This action cannot be undone.`,
      confirmText: "Yes, Delete Entry",
      isDanger: true,
      onConfirm: () => {
        fetch(`/purchase/orders/${dbId || poId}`, {
          method: "DELETE",
          headers: {
            "Accept": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || ""
          }
        })
        .finally(() => {
          ERPState.data.purchaseOrders = (ERPState.data.purchaseOrders || []).filter(o => (o.po_number || o.id) !== poId && String(o.dbId || o.id) !== String(dbId));
          ERPState.saveState();
          UI.showToast("Entry Deleted", `${poId} removed.`, "info");
          App.refreshCurrentView();
        });
      }
    });
  },

  // 7. PRINT CHALLAN SLIP MODAL
  printPO(poId) {
    const po = (ERPState.data.purchaseOrders || []).find(o => (o.po_number || o.poNumber || o.id) === poId);
    if (!po) return;

    let thans = po.thans || [];
    let challanNotes = po.notes || '';
    if (thans.length === 0 && po.notes) {
      try {
        const parsed = typeof po.notes === 'string' ? JSON.parse(po.notes) : po.notes;
        if (parsed.thans) thans = parsed.thans;
        if (parsed.user_notes) challanNotes = parsed.user_notes;
      } catch(e){}
    }

    const challanNo = po.challan_no ? `CH-${po.challan_no}` : poId;
    const vendorName = po.vendor_name || po.vendorName || (po.vendor ? po.vendor.name : 'Supplier');
    const fabricName = po.item_name || (po.items && po.items[0] ? po.items[0].item_name : 'Fabric Inward');
    const totalMtr = po.total_meters || (thans.length > 0 ? thans.reduce((a,b)=>a+b,0) : (po.items && po.items[0] ? po.items[0].ordered_qty : 0));
    const rate = po.rate || (po.items && po.items[0] ? po.items[0].rate : 0);
    const subtotal = po.subtotal || (totalMtr * rate);
    const taxTotal = po.tax_total || (po.grand_total ? po.grand_total - subtotal : 0);
    const grandTotal = po.grand_total || (subtotal + taxTotal);

    const midpoint = Math.ceil(thans.length / 2);
    const col1 = thans.slice(0, midpoint);
    const col2 = thans.slice(midpoint);

    const printHtml = `
      <div class="print-container" style="padding:24px; font-family:sans-serif; color:#0f172a;">
        <div style="display:flex; justify-content:space-between; border-bottom:2px solid #2563eb; padding-bottom:14px; margin-bottom:18px;">
          <div>
            <h2 style="color:#2563eb; margin:0 0 4px 0; font-size:1.4rem;">FABRIC INWARD CHALLAN ENTRY</h2>
            <p style="margin:0; font-size:0.85rem; color:#475569;">FASHIONWORKS GARMENTS PVT. LTD. • Bhiwandi, Thane</p>
          </div>
          <div style="text-align:right;">
            <p style="margin:0; font-weight:800; font-size:1.1rem; color:#2563eb;">${challanNo}</p>
            <p style="margin:2px 0 0; font-size:0.8rem; color:#64748b;">Date: ${UI.formatDate(po.po_date || po.poDate)}</p>
          </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:18px; font-size:0.875rem;">
          <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px;">
            <span style="color:#64748b; font-size:0.75rem; text-transform:uppercase; font-weight:700;">Vendor / Supplier:</span>
            <div style="font-weight:800; font-size:1rem; color:#0f172a; margin-top:2px;">${vendorName}</div>
          </div>
          <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px;">
            <span style="color:#64748b; font-size:0.75rem; text-transform:uppercase; font-weight:700;">Fabric Quality:</span>
            <div style="font-weight:800; font-size:1rem; color:#059669; margin-top:2px;">${fabricName}</div>
          </div>
        </div>

        ${thans.length > 0 ? `
          <div style="border:1px solid #e2e8f0; border-radius:10px; padding:14px; margin-bottom:18px;">
            <div style="font-size:0.8rem; font-weight:800; text-transform:uppercase; color:#64748b; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-bottom:10px;">
              Than-wise Meter Breakdown (${thans.length} Thans)
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; font-size:0.85rem;">
              <div>
                ${col1.map((m, i) => `
                  <div style="display:flex; justify-content:space-between; padding:4px 0; border-bottom:1px dashed #e2e8f0;">
                    <span style="color:#64748b;">Than #${i + 1}:</span>
                    <strong style="font-family:monospace;">${Number(m).toFixed(2)} Mtr</strong>
                  </div>
                `).join('')}
              </div>
              <div>
                ${col2.map((m, i) => `
                  <div style="display:flex; justify-content:space-between; padding:4px 0; border-bottom:1px dashed #e2e8f0;">
                    <span style="color:#64748b;">Than #${midpoint + i + 1}:</span>
                    <strong style="font-family:monospace;">${Number(m).toFixed(2)} Mtr</strong>
                  </div>
                `).join('')}
              </div>
            </div>
          </div>
        ` : ''}

        <div style="display:flex; justify-content:flex-end; margin-bottom:24px;">
          <div style="width:300px; font-size:0.9rem; line-height:1.9;">
            <div style="display:flex; justify-content:space-between;"><span>Total Meters:</span> <strong style="font-family:monospace; color:#059669;">${Number(totalMtr).toFixed(2)} Mtr</strong></div>
            <div style="display:flex; justify-content:space-between;"><span>Rate / Mtr:</span> <strong style="font-family:monospace;">₹${Number(rate).toFixed(2)}</strong></div>
            <div style="display:flex; justify-content:space-between;"><span>Subtotal:</span> <strong style="font-family:monospace;">${UI.formatCurrency(subtotal)}</strong></div>
            <div style="display:flex; justify-content:space-between;"><span>GST Tax:</span> <strong style="font-family:monospace;">${UI.formatCurrency(taxTotal)}</strong></div>
            <div style="display:flex; justify-content:space-between; font-size:1.15rem; font-weight:800; color:#2563eb; border-top:2px solid #cbd5e1; padding-top:6px; margin-top:6px;">
              <span>Grand Total:</span> <strong style="font-family:monospace;">${UI.formatCurrency(grandTotal)}</strong>
            </div>
          </div>
        </div>

        <div style="display:flex; justify-content:space-between; border-top:1px solid #cbd5e1; padding-top:20px; font-size:0.8rem; color:#64748b;">
          <div>Received By (Store Keeper): ____________________</div>
          <div>Supplier Signature: ____________________</div>
        </div>
      </div>
    `;

    UI.openModal({
      title: `Challan Inward Slip (${challanNo})`,
      content: printHtml,
      footer: `
        <button class="btn btn-secondary" onclick="UI.closeModal()">Close</button>
        <button class="btn btn-primary" onclick="window.print()">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
          Print Challan Slip
        </button>
      `,
      size: "modal-lg"
    });
  },

  openCreatePOModal() {
    App.navigate("purchase", "create");
  },

  openEditPOModal(poId) {
    PurchaseView.currentEditPoId = poId;
    App.navigate("purchase", "edit");
  },

  filterPOTable() {
    const q = (document.getElementById("po-search")?.value || "").toLowerCase();
    const st = document.getElementById("po-status-filter")?.value || "";
    const rows = document.querySelectorAll("#po-table tbody tr");

    rows.forEach(r => {
      const text = r.textContent.toLowerCase();
      const matchesSearch = !q || text.includes(q);
      const matchesStatus = !st || text.includes(st.toLowerCase());
      r.style.display = (matchesSearch && matchesStatus) ? "" : "none";
    });
  }
};
