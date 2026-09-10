/* ==========================================================================
   PURCHASE ORDERS MANAGEMENT VIEW
   GarmentERP
   ========================================================================== */

const PurchaseView = {
  // Temporary line items in create/edit PO modal
  tempPoItems: [],

  // 1. PURCHASE ORDERS LIST & VIEW
  renderOrders() {
    const orders = ERPState.data.purchaseOrders || [];
    const vendors = ERPState.data.vendors || [];
    const totalOrdered = orders.reduce((sum, o) => sum + Number(o.grand_total || o.grandTotal || 0), 0);
    const pendingCount = orders.filter(o => o.status !== 'Received' && o.status !== 'Cancelled').length;
    const receivedCount = orders.filter(o => o.status === 'Received').length;

    return `
      <!-- Top Title & Action Controls -->
      <div class="dashboard-top-bar" style="margin-bottom:20px;">
        <div class="dashboard-title-wrap">
          <h1>
            Purchase Orders (PO)
            <span style="font-size:0.75rem; font-weight:600; padding:2px 8px; background:var(--primary-100); color:var(--primary-700); border-radius:var(--radius-full); vertical-align:middle;">PROCUREMENT</span>
          </h1>
          <p class="dashboard-subtitle">Manage raw fabric procurement, vendor purchasing contracts, order line items, and delivery status.</p>
        </div>

        <div class="dashboard-controls">
          <button class="btn btn-primary btn-sm" onclick="PurchaseView.openCreatePOModal()">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            + Create Purchase Order
          </button>
        </div>
      </div>

      <!-- 4 KPI Summary Cards -->
      <div class="kpi-grid" style="margin-bottom:24px;">
        <div class="kpi-card blue">
          <div class="kpi-top">
            <span class="kpi-title">Total PO Value</span>
            <div class="kpi-icon-wrap blue">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            </div>
          </div>
          <div class="kpi-value">${UI.formatCurrency(totalOrdered)}</div>
          <div class="kpi-bottom">
            <span class="kpi-trend up">${orders.length} Total POs</span>
            <span class="kpi-period">All Suppliers</span>
          </div>
        </div>

        <div class="kpi-card amber">
          <div class="kpi-top">
            <span class="kpi-title">Pending Orders</span>
            <div class="kpi-icon-wrap amber">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
          </div>
          <div class="kpi-value">${pendingCount} Orders</div>
          <div class="kpi-bottom">
            <span class="kpi-trend down">Awaiting Delivery</span>
            <span class="kpi-period">In Procurement</span>
          </div>
        </div>

        <div class="kpi-card emerald">
          <div class="kpi-top">
            <span class="kpi-title">Fulfilled Orders</span>
            <div class="kpi-icon-wrap emerald">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
          </div>
          <div class="kpi-value">${receivedCount} Received</div>
          <div class="kpi-bottom">
            <span class="kpi-trend up">Store Stock Added</span>
            <span class="kpi-period">Completed</span>
          </div>
        </div>

        <div class="kpi-card purple">
          <div class="kpi-top">
            <span class="kpi-title">Active Suppliers</span>
            <div class="kpi-icon-wrap purple">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
          </div>
          <div class="kpi-value">${vendors.length} Vendors</div>
          <div class="kpi-bottom">
            <span class="kpi-trend up">Fabric & Trims</span>
            <span class="kpi-period">Registered Mills</span>
          </div>
        </div>
      </div>

      <!-- Main Purchase Orders Table Card -->
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
            <div class="table-search-box" style="min-width:280px;">
              <svg class="table-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              <input type="text" class="table-search-input" id="po-search" placeholder="Search PO #, vendor, warehouse..." oninput="PurchaseView.filterPOTable()">
            </div>

            <select class="form-control form-control-sm" id="po-status-filter" style="width:170px;" onchange="PurchaseView.filterPOTable()">
              <option value="">All Statuses</option>
              <option value="Draft">Draft</option>
              <option value="Approved">Approved</option>
              <option value="Partially Received">Partially Received</option>
              <option value="Received">Received</option>
              <option value="Cancelled">Cancelled</option>
            </select>
          </div>

          <div class="table-toolbar-right">
            <button class="btn btn-primary btn-sm" onclick="PurchaseView.openCreatePOModal()">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              + Create Purchase Order
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table" id="po-table">
            <thead>
              <tr>
                <th>PO NUMBER</th>
                <th>VENDOR / SUPPLIER</th>
                <th>PO DATE</th>
                <th>DELIVERY DATE</th>
                <th>ORDERED ITEMS</th>
                <th>GRAND TOTAL (₹)</th>
                <th>PAYMENT</th>
                <th>STATUS</th>
                <th style="text-align:right;">ACTIONS</th>
              </tr>
            </thead>
            <tbody>
              ${orders.length === 0 ? `
                <tr>
                  <td colspan="9" style="text-align:center; padding:48px 20px; color:var(--slate-400);">
                    <div style="display:inline-flex; align-items:center; justify-content:center; width:52px; height:52px; background:var(--primary-50); color:var(--primary-600); border-radius:var(--radius-full); margin-bottom:12px;">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    </div>
                    <div style="font-size:1.05rem; font-weight:700; color:var(--slate-700); margin-bottom:4px;">No Purchase Orders Found</div>
                    <div style="font-size:0.85rem; color:var(--slate-500); margin-bottom:16px;">Raise your first purchase order to procure fabric rolls, threads, zippers, and trims from mill suppliers.</div>
                    <button class="btn btn-primary btn-sm" onclick="PurchaseView.openCreatePOModal()">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                      + Create First Purchase Order
                    </button>
                  </td>
                </tr>
              ` : orders.map(po => {
                const poId = po.po_number || po.poNumber || po.id;
                const poDbId = po.dbId || po.id;
                const vendorName = po.vendor_name || po.vendorName || (po.vendor ? po.vendor.name : 'N/A');
                const itemsCount = (po.items && Array.isArray(po.items)) ? po.items.length : 1;
                const total = Number(po.grand_total || po.grandTotal || 0);
                const poDate = po.po_date || po.poDate;
                const expDate = po.expected_delivery_date || po.delivery_date || po.deliveryDate || po.expectedDate;

                return `
                  <tr>
                    <td class="mono-cell font-bold" style="color:var(--primary-600); cursor:pointer;" onclick="PurchaseView.printPO('${poId}')">${poId}</td>
                    <td class="primary-cell">
                      <div class="fw-bold">${vendorName}</div>
                      <div class="text-xs text-muted">${po.warehouse_location || po.warehouse || 'Main Store - Unit 1'}</div>
                    </td>
                    <td>${UI.formatDate(poDate)}</td>
                    <td style="font-size:0.825rem; color:var(--slate-600);">${expDate ? UI.formatDate(expDate) : '-'}</td>
                    <td><span class="count-badge">${itemsCount} item(s)</span></td>
                    <td class="font-bold font-mono">${UI.formatCurrency(total)}</td>
                    <td>
                      <span class="badge ${po.payment_status === 'Paid' ? 'badge-success' : (po.payment_status === 'Partially Paid' ? 'badge-warning' : 'badge-neutral')}">
                        ${po.payment_status || 'Pending'}
                      </span>
                    </td>
                    <td>${UI.formatStatusBadge(po.status || 'Approved')}</td>
                    <td style="text-align:right;">
                      <div class="table-actions" style="justify-content:flex-end;">
                        <button class="table-action-btn view" title="View & Print PO" onclick="PurchaseView.printPO('${poId}')">
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                        </button>
                        <button class="table-action-btn edit" title="Edit PO" onclick="PurchaseView.openEditPOModal('${poId}')">
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                        </button>
                        <button class="table-action-btn delete" title="Delete PO" onclick="PurchaseView.deletePO('${poId}', ${poDbId})">
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
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

  // 2. CREATE PURCHASE ORDER (FULL PAGE VIEW)
  renderCreatePage() {
    const vendors = ERPState.data.vendors || [];
    const items = ERPState.data.items || [];
    const todayStr = new Date().toISOString().split("T")[0];

    this.tempPoItems = [
      {
        item_name: items[0] ? items[0].name : "100% Combed Cotton Fabric 180 GSM",
        item_code: items[0] ? (items[0].code || items[0].id) : "FAB-COT-180",
        ordered_qty: 1000,
        unit: items[0] ? (items[0].unit || "Meters") : "Meters",
        rate: items[0] ? (items[0].rate || 145) : 145,
        tax_percent: 5,
        total_amount: 152250
      }
    ];

    return `
      <!-- Top Navigation & Action Header -->
      <div class="dashboard-top-bar" style="margin-bottom:20px;">
        <div class="dashboard-title-wrap">
          <div style="display:flex; align-items:center; gap:12px; margin-bottom:6px;">
            <button class="btn btn-secondary btn-sm" onclick="App.navigate('purchase', 'orders')" title="Back to PO List">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
              Back to PO List
            </button>
            <h1 style="margin:0;">
              Create Purchase Order
              <span style="font-size:0.75rem; font-weight:600; padding:2px 8px; background:var(--primary-100); color:var(--primary-700); border-radius:var(--radius-full); vertical-align:middle;">NEW PO</span>
            </h1>
          </div>
          <p class="dashboard-subtitle">Fill in the supplier procurement contract, select fabrics/trims, and issue a formal purchase order.</p>
        </div>

        <div class="dashboard-controls">
          <button type="button" class="btn btn-secondary btn-sm" onclick="App.navigate('purchase', 'orders')">Cancel</button>
          <button type="button" class="btn btn-primary btn-sm" onclick="PurchaseView.submitPOForm(null, null)">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            Generate & Issue PO
          </button>
        </div>
      </div>

      <form id="po-full-form" onsubmit="event.preventDefault();">
        <!-- 1. General PO Information Card -->
        <div class="card mb-6" style="padding:24px;">
          <h3 style="font-size:1.05rem; font-weight:700; color:var(--slate-800); margin-bottom:16px; border-bottom:1px solid var(--slate-100); padding-bottom:10px;">
            1. Supplier & Procurement Terms
          </h3>

          <div class="form-grid-3">
            <div class="form-group">
              <label class="form-label">Vendor / Mill Supplier <span class="required-star">*</span></label>
              <select class="form-control" id="po-vendor" required>
                ${vendors.length === 0 ? '<option value="">No vendors found (Add in Masters)</option>' : vendors.map(v => `<option value="${v.name}">${v.name} (${v.code || v.id})</option>`).join('')}
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">PO Issue Date <span class="required-star">*</span></label>
              <input type="date" class="form-control" id="po-date" required value="${todayStr}">
            </div>

            <div class="form-group">
              <label class="form-label">Expected Delivery Date</label>
              <input type="date" class="form-control" id="po-exp-date">
            </div>
          </div>

          <div class="form-grid-3" style="margin-top:16px;">
            <div class="form-group">
              <label class="form-label">Warehouse Receiving Location</label>
              <select class="form-control" id="po-warehouse">
                <option value="Main Store - Unit 1">Main Store - Unit 1</option>
                <option value="Dyeing & Trims Store - Unit 2">Dyeing & Trims Store - Unit 2</option>
                <option value="Secondary Processing Hub">Secondary Processing Hub</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">Payment Terms</label>
              <select class="form-control" id="po-pay-terms">
                <option value="Pending">Payment on Delivery (Net 30)</option>
                <option value="Advance">100% Advance Payment</option>
                <option value="Partially Paid">50% Advance, 50% on Delivery</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">PO Status</label>
              <select class="form-control" id="po-status">
                <option value="Draft">Draft (Internal Review)</option>
                <option value="Approved" selected>Approved & Issued</option>
              </select>
            </div>
          </div>
        </div>

        <!-- 2. Ordered Line Items Card -->
        <div class="card mb-6" style="padding:24px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid var(--slate-100); padding-bottom:10px;">
            <h3 style="font-size:1.05rem; font-weight:700; color:var(--slate-800); margin:0;">
              2. Ordered Raw Materials & Line Items
            </h3>
            <button type="button" class="btn btn-secondary btn-sm" onclick="PurchaseView.addTempItem()">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              + Add Material Row
            </button>
          </div>

          <div class="table-responsive">
            <table class="data-table" id="po-items-table">
              <thead>
                <tr>
                  <th style="width:36%;">RAW MATERIAL / ITEM</th>
                  <th style="width:14%;">QUANTITY</th>
                  <th style="width:12%;">UNIT</th>
                  <th style="width:14%;">RATE (₹)</th>
                  <th style="width:10%;">GST %</th>
                  <th style="width:14%;">TOTAL (₹)</th>
                  <th style="width:4%;"></th>
                </tr>
              </thead>
              <tbody id="po-items-body">
                ${this.renderTempPoRows(items)}
              </tbody>
            </table>
          </div>

          <!-- Total Calculation Box -->
          <div style="display:flex; justify-content:flex-end; margin-top:20px;">
            <div style="width:320px; background:var(--slate-50); border:1px solid var(--slate-200); border-radius:var(--radius-lg); padding:16px; font-size:0.9rem; line-height:2;">
              <div style="display:flex; justify-content:space-between; color:var(--slate-600);">
                <span>Taxable Subtotal:</span> <strong class="font-mono" id="po-subtotal-val">₹0</strong>
              </div>
              <div style="display:flex; justify-content:space-between; color:var(--slate-600);">
                <span>GST Tax Total:</span> <strong class="font-mono" id="po-tax-val">₹0</strong>
              </div>
              <div style="display:flex; justify-content:space-between; font-size:1.15rem; font-weight:800; color:var(--primary-700); border-top:2px solid var(--slate-300); padding-top:6px; margin-top:6px;">
                <span>Grand Total:</span> <strong class="font-mono" id="po-grand-val">₹0</strong>
              </div>
            </div>
          </div>
        </div>

        <!-- 3. Instructions & Notes Card -->
        <div class="card mb-6" style="padding:24px;">
          <h3 style="font-size:1.05rem; font-weight:700; color:var(--slate-800); margin-bottom:16px; border-bottom:1px solid var(--slate-100); padding-bottom:10px;">
            3. Purchase Order Notes & Delivery Guidelines
          </h3>
          <div class="form-group">
            <textarea class="form-control" id="po-notes" rows="3" placeholder="Specify roll width (e.g. 58 inches), lab dip color shade approvals, test certificates required with delivery..."></textarea>
          </div>
        </div>

        <!-- Bottom Action Bar -->
        <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:24px; padding-bottom:32px;">
          <button type="button" class="btn btn-secondary btn-lg" onclick="App.navigate('purchase', 'orders')">Cancel & Discard</button>
          <button type="button" class="btn btn-primary btn-lg" onclick="PurchaseView.submitPOForm(null, null)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            Generate & Issue Purchase Order
          </button>
        </div>
      </form>
    `;
  },

  postRenderCreate() {
    this.recalcPOTotals();
  },

  // 3. EDIT PURCHASE ORDER (FULL PAGE VIEW)
  renderEditPage(poId) {
    const orders = ERPState.data.purchaseOrders || [];
    const targetId = poId || PurchaseView.currentEditPoId || window.INITIAL_ROUTE?.targetId;
    const po = orders.find(o => (o.po_number || o.poNumber || o.id) === targetId || String(o.dbId || o.id) === String(targetId));

    if (!po) {
      return `
        <div class="card" style="padding:48px; text-align:center;">
          <h3>Purchase Order Not Found</h3>
          <p class="text-muted">The requested purchase order could not be located in state.</p>
          <button class="btn btn-primary btn-sm mt-4" onclick="App.navigate('purchase', 'orders')">Return to Purchase Orders</button>
        </div>
      `;
    }

    const vendors = ERPState.data.vendors || [];
    const items = ERPState.data.items || [];
    const poNumber = po.po_number || po.poNumber || po.id;
    const poDbId = po.dbId || po.id;
    const poDateVal = po.po_date || po.poDate || '';
    const expDateVal = po.expected_delivery_date || po.delivery_date || po.deliveryDate || po.expectedDate || '';
    const vendorVal = po.vendor_name || po.vendorName || (po.vendor ? po.vendor.name : '');
    const warehouseVal = po.warehouse_location || po.warehouse || 'Main Store - Unit 1';
    const payTermsVal = po.payment_status || 'Pending';
    const statusVal = po.status || 'Approved';
    const notesVal = po.notes || '';

    if (po.items && Array.isArray(po.items) && po.items.length > 0) {
      this.tempPoItems = JSON.parse(JSON.stringify(po.items)).map(it => ({
        item_name: it.item_name || it.item || (items[0] ? items[0].name : "Cotton Fabric"),
        item_code: it.item_code || it.code || "FAB-01",
        ordered_qty: Number(it.ordered_qty || it.qty || 1),
        unit: it.unit || "Meters",
        rate: Number(it.rate || it.unit_price || 100),
        tax_percent: Number(it.tax_percent !== undefined ? it.tax_percent : (it.tax || 5)),
        total_amount: Number(it.total_amount || it.total_price || 0)
      }));
    } else {
      this.tempPoItems = [
        {
          item_name: items[0] ? items[0].name : "100% Combed Cotton Fabric 180 GSM",
          item_code: items[0] ? (items[0].code || items[0].id) : "FAB-COT-180",
          ordered_qty: 1000,
          unit: "Meters",
          rate: 145,
          tax_percent: 5,
          total_amount: 152250
        }
      ];
    }

    return `
      <!-- Top Navigation & Action Header -->
      <div class="dashboard-top-bar" style="margin-bottom:20px;">
        <div class="dashboard-title-wrap">
          <div style="display:flex; align-items:center; gap:12px; margin-bottom:6px;">
            <button class="btn btn-secondary btn-sm" onclick="App.navigate('purchase', 'orders')" title="Back to PO List">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
              Back to PO List
            </button>
            <h1 style="margin:0;">
              Edit Purchase Order (${poNumber})
              <span style="font-size:0.75rem; font-weight:600; padding:2px 8px; background:var(--primary-100); color:var(--primary-700); border-radius:var(--radius-full); vertical-align:middle;">EDITING</span>
            </h1>
          </div>
          <p class="dashboard-subtitle">Update vendor order specifications, delivery date, quantities, or rate terms.</p>
        </div>

        <div class="dashboard-controls">
          <button type="button" class="btn btn-secondary btn-sm" onclick="App.navigate('purchase', 'orders')">Cancel</button>
          <button type="button" class="btn btn-primary btn-sm" onclick="PurchaseView.submitPOForm('${poNumber}', ${poDbId})">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            Save Changes
          </button>
        </div>
      </div>

      <form id="po-full-form" onsubmit="event.preventDefault();">
        <!-- 1. General PO Information Card -->
        <div class="card mb-6" style="padding:24px;">
          <h3 style="font-size:1.05rem; font-weight:700; color:var(--slate-800); margin-bottom:16px; border-bottom:1px solid var(--slate-100); padding-bottom:10px;">
            1. Supplier & Procurement Terms
          </h3>

          <div class="form-grid-3">
            <div class="form-group">
              <label class="form-label">Vendor / Mill Supplier <span class="required-star">*</span></label>
              <select class="form-control" id="po-vendor" required>
                ${vendors.map(v => `<option value="${v.name}" ${v.name === vendorVal ? 'selected' : ''}>${v.name} (${v.code || v.id})</option>`).join('')}
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">PO Issue Date <span class="required-star">*</span></label>
              <input type="date" class="form-control" id="po-date" required value="${poDateVal}">
            </div>

            <div class="form-group">
              <label class="form-label">Expected Delivery Date</label>
              <input type="date" class="form-control" id="po-exp-date" value="${expDateVal}">
            </div>
          </div>

          <div class="form-grid-3" style="margin-top:16px;">
            <div class="form-group">
              <label class="form-label">Warehouse Receiving Location</label>
              <select class="form-control" id="po-warehouse">
                <option value="Main Store - Unit 1" ${warehouseVal.includes('Unit 1') ? 'selected' : ''}>Main Store - Unit 1</option>
                <option value="Dyeing & Trims Store - Unit 2" ${warehouseVal.includes('Unit 2') ? 'selected' : ''}>Dyeing & Trims Store - Unit 2</option>
                <option value="Secondary Processing Hub" ${warehouseVal.includes('Secondary') ? 'selected' : ''}>Secondary Processing Hub</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">Payment Terms</label>
              <select class="form-control" id="po-pay-terms">
                <option value="Pending" ${payTermsVal === 'Pending' ? 'selected' : ''}>Payment on Delivery (Net 30)</option>
                <option value="Advance" ${payTermsVal === 'Advance' ? 'selected' : ''}>100% Advance Payment</option>
                <option value="Partially Paid" ${payTermsVal === 'Partially Paid' ? 'selected' : ''}>50% Advance, 50% on Delivery</option>
                <option value="Paid" ${payTermsVal === 'Paid' ? 'selected' : ''}>Paid in Full</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">PO Status</label>
              <select class="form-control" id="po-status">
                <option value="Draft" ${statusVal === 'Draft' ? 'selected' : ''}>Draft (Internal Review)</option>
                <option value="Approved" ${statusVal === 'Approved' ? 'selected' : ''}>Approved & Issued</option>
                <option value="Partially Received" ${statusVal === 'Partially Received' ? 'selected' : ''}>Partially Received</option>
                <option value="Received" ${statusVal === 'Received' ? 'selected' : ''}>Received / Fulfilled</option>
                <option value="Cancelled" ${statusVal === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
              </select>
            </div>
          </div>
        </div>

        <!-- 2. Ordered Line Items Card -->
        <div class="card mb-6" style="padding:24px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid var(--slate-100); padding-bottom:10px;">
            <h3 style="font-size:1.05rem; font-weight:700; color:var(--slate-800); margin:0;">
              2. Ordered Raw Materials & Line Items
            </h3>
            <button type="button" class="btn btn-secondary btn-sm" onclick="PurchaseView.addTempItem()">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              + Add Material Row
            </button>
          </div>

          <div class="table-responsive">
            <table class="data-table" id="po-items-table">
              <thead>
                <tr>
                  <th style="width:36%;">RAW MATERIAL / ITEM</th>
                  <th style="width:14%;">QUANTITY</th>
                  <th style="width:12%;">UNIT</th>
                  <th style="width:14%;">RATE (₹)</th>
                  <th style="width:10%;">GST %</th>
                  <th style="width:14%;">TOTAL (₹)</th>
                  <th style="width:4%;"></th>
                </tr>
              </thead>
              <tbody id="po-items-body">
                ${this.renderTempPoRows(items)}
              </tbody>
            </table>
          </div>

          <!-- Total Calculation Box -->
          <div style="display:flex; justify-content:flex-end; margin-top:20px;">
            <div style="width:320px; background:var(--slate-50); border:1px solid var(--slate-200); border-radius:var(--radius-lg); padding:16px; font-size:0.9rem; line-height:2;">
              <div style="display:flex; justify-content:space-between; color:var(--slate-600);">
                <span>Taxable Subtotal:</span> <strong class="font-mono" id="po-subtotal-val">₹0</strong>
              </div>
              <div style="display:flex; justify-content:space-between; color:var(--slate-600);">
                <span>GST Tax Total:</span> <strong class="font-mono" id="po-tax-val">₹0</strong>
              </div>
              <div style="display:flex; justify-content:space-between; font-size:1.15rem; font-weight:800; color:var(--primary-700); border-top:2px solid var(--slate-300); padding-top:6px; margin-top:6px;">
                <span>Grand Total:</span> <strong class="font-mono" id="po-grand-val">₹0</strong>
              </div>
            </div>
          </div>
        </div>

        <!-- 3. Instructions & Notes Card -->
        <div class="card mb-6" style="padding:24px;">
          <h3 style="font-size:1.05rem; font-weight:700; color:var(--slate-800); margin-bottom:16px; border-bottom:1px solid var(--slate-100); padding-bottom:10px;">
            3. Purchase Order Notes & Delivery Guidelines
          </h3>
          <div class="form-group">
            <textarea class="form-control" id="po-notes" rows="3" placeholder="Specify roll width (e.g. 58 inches), lab dip color shade approvals, test certificates required with delivery...">${notesVal}</textarea>
          </div>
        </div>

        <!-- Bottom Action Bar -->
        <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:24px; padding-bottom:32px;">
          <button type="button" class="btn btn-secondary btn-lg" onclick="App.navigate('purchase', 'orders')">Cancel & Discard</button>
          <button type="button" class="btn btn-primary btn-lg" onclick="PurchaseView.submitPOForm('${poNumber}', ${poDbId})">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            Save Purchase Order Changes
          </button>
        </div>
      </form>
    `;
  },

  postRenderEdit() {
    this.recalcPOTotals();
  },

  openCreatePOModal() {
    App.navigate("purchase", "create");
  },

  openEditPOModal(poId) {
    const po = (ERPState.data.purchaseOrders || []).find(o => (o.po_number || o.poNumber || o.id) === poId);
    PurchaseView.currentEditPoId = poId;
    PurchaseView.currentEditDbId = po ? (po.dbId || po.id) : null;
    App.navigate("purchase", "edit");
  },

  renderTempPoRows(masterItems) {
    const items = masterItems || (ERPState.data.items || []);
    return this.tempPoItems.map((item, idx) => {
      return `
        <tr data-idx="${idx}">
          <td>
            <select class="form-control form-control-sm" onchange="PurchaseView.onItemSelect(${idx}, this.value)">
              ${items.map(i => `<option value="${i.name}" ${(item.item_name || item.item) === i.name ? 'selected' : ''}>${i.name} (${i.code || i.id})</option>`).join('')}
            </select>
          </td>
          <td>
            <input type="number" class="form-control form-control-sm font-mono" min="1" value="${item.ordered_qty || item.qty || 1}" oninput="PurchaseView.updateItemField(${idx}, 'qty', this.value)">
          </td>
          <td>
            <input type="text" class="form-control form-control-sm" value="${item.unit || 'Meters'}" oninput="PurchaseView.updateItemField(${idx}, 'unit', this.value)">
          </td>
          <td>
            <input type="number" class="form-control form-control-sm font-mono" min="0" step="0.5" value="${item.rate || 100}" oninput="PurchaseView.updateItemField(${idx}, 'rate', this.value)">
          </td>
          <td>
            <select class="form-control form-control-sm" onchange="PurchaseView.updateItemField(${idx}, 'tax', this.value)">
              <option value="5" ${Number(item.tax_percent !== undefined ? item.tax_percent : item.tax) === 5 ? 'selected' : ''}>5%</option>
              <option value="12" ${Number(item.tax_percent !== undefined ? item.tax_percent : item.tax) === 12 ? 'selected' : ''}>12%</option>
              <option value="18" ${Number(item.tax_percent !== undefined ? item.tax_percent : item.tax) === 18 ? 'selected' : ''}>18%</option>
              <option value="0" ${Number(item.tax_percent !== undefined ? item.tax_percent : item.tax) === 0 ? 'selected' : ''}>0%</option>
            </select>
          </td>
          <td class="font-bold font-mono" id="po-line-total-${idx}">
            ${UI.formatCurrency(item.total_amount || item.amount || 0)}
          </td>
          <td style="text-align:center;">
            ${this.tempPoItems.length > 1 ? `
              <button type="button" class="table-action-btn delete" onclick="PurchaseView.removeTempItem(${idx})" title="Remove">✕</button>
            ` : ''}
          </td>
        </tr>
      `;
    }).join('');
  },

  addTempItem() {
    const items = ERPState.data.items || [];
    const first = items[0] || { name: "100% Combed Cotton Fabric 180 GSM", code: "FAB-COT-180", rate: 145, unit: "Meters" };
    this.tempPoItems.push({
      item_name: first.name,
      item_code: first.code || first.id || "FAB-01",
      ordered_qty: 500,
      unit: first.unit || "Meters",
      rate: first.rate || 145,
      tax_percent: 5,
      total_amount: (500 * (first.rate || 145)) * 1.05
    });
    const body = document.getElementById("po-items-body");
    if (body) body.innerHTML = this.renderTempPoRows();
    this.recalcPOTotals();
  },

  removeTempItem(idx) {
    if (this.tempPoItems.length > 1) {
      this.tempPoItems.splice(idx, 1);
      const body = document.getElementById("po-items-body");
      if (body) body.innerHTML = this.renderTempPoRows();
      this.recalcPOTotals();
    }
  },

  onItemSelect(idx, itemName) {
    const items = ERPState.data.items || [];
    const found = items.find(i => i.name === itemName);
    if (found && this.tempPoItems[idx]) {
      this.tempPoItems[idx].item_name = found.name;
      this.tempPoItems[idx].item_code = found.code || found.id;
      if (found.rate) this.tempPoItems[idx].rate = found.rate;
      if (found.unit) this.tempPoItems[idx].unit = found.unit;
    }
    this.recalcPOTotals();
  },

  updateItemField(idx, field, val) {
    if (!this.tempPoItems[idx]) return;
    if (field === 'qty') this.tempPoItems[idx].ordered_qty = Number(val) || 0;
    if (field === 'rate') this.tempPoItems[idx].rate = Number(val) || 0;
    if (field === 'unit') this.tempPoItems[idx].unit = val;
    if (field === 'tax') this.tempPoItems[idx].tax_percent = Number(val) || 0;
    this.recalcPOTotals();
  },

  recalcPOTotals() {
    let subtotal = 0;
    let taxTotal = 0;

    this.tempPoItems.forEach((item, idx) => {
      const q = Number(item.ordered_qty || item.qty || 0);
      const r = Number(item.rate || 0);
      const taxRate = Number(item.tax_percent !== undefined ? item.tax_percent : (item.tax || 5));
      const lineSub = q * r;
      const lineTax = (lineSub * taxRate) / 100;
      const lineTotal = lineSub + lineTax;

      item.total_amount = lineTotal;
      item.tax_amount = lineTax;
      subtotal += lineSub;
      taxTotal += lineTax;

      const el = document.getElementById(`po-line-total-${idx}`);
      if (el) el.textContent = UI.formatCurrency(lineTotal);
    });

    const grand = subtotal + taxTotal;
    const subEl = document.getElementById("po-subtotal-val");
    const taxEl = document.getElementById("po-tax-val");
    const grandEl = document.getElementById("po-grand-val");

    if (subEl) subEl.textContent = UI.formatCurrency(subtotal);
    if (taxEl) taxEl.textContent = UI.formatCurrency(taxTotal);
    if (grandEl) grandEl.textContent = UI.formatCurrency(grand);
  },

  submitPOForm(poId, dbId) {
    const vendorName = document.getElementById("po-vendor").value;
    const poDate = document.getElementById("po-date").value;
    const expectedDate = document.getElementById("po-exp-date").value;
    const warehouse = document.getElementById("po-warehouse").value;
    const payTerms = document.getElementById("po-pay-terms") ? document.getElementById("po-pay-terms").value : "Pending";
    const status = document.getElementById("po-status") ? document.getElementById("po-status").value : "Approved";
    const notes = document.getElementById("po-notes").value;

    if (!vendorName) return UI.showToast("Required Field", "Please select a vendor supplier", "error");
    if (!poDate) return UI.showToast("Required Field", "Please select the PO date", "error");

    const payload = {
      vendor_name: vendorName,
      po_date: poDate,
      expected_delivery_date: expectedDate || null,
      warehouse_location: warehouse,
      payment_status: payTerms,
      status: status,
      notes,
      items: this.tempPoItems.map(item => ({
        item_name: item.item_name || item.item,
        item_code: item.item_code || item.code || 'RAW-01',
        ordered_qty: Number(item.ordered_qty || item.qty || 1),
        unit: item.unit || 'Meters',
        rate: Number(item.rate || 0),
        tax_percent: Number(item.tax_percent !== undefined ? item.tax_percent : (item.tax || 5))
      }))
    };

    if (poId && dbId) {
      // Update PO
      fetch(`/purchase/orders/${dbId}`, {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || ""
        },
        body: JSON.stringify(payload)
      })
      .then(res => res.json())
      .then(data => {
        UI.showToast("PO Updated", `Purchase Order ${poId} successfully updated!`, "success");
        ERPState.syncWithBackend().then(() => {
          App.navigate("purchase", "orders");
        });
      })
      .catch(err => {
        ERPState.updatePurchaseOrder(poId, payload);
        UI.showToast("PO Updated", `Purchase Order ${poId} updated.`, "success");
        App.navigate("purchase", "orders");
      });
    } else {
      // Create PO
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
        UI.showToast("PO Issued", `Purchase Order created successfully!`, "success");
        ERPState.syncWithBackend().then(() => {
          App.navigate("purchase", "orders");
        });
      })
      .catch(err => {
        ERPState.createPurchaseOrder(payload);
        UI.showToast("PO Issued", "Purchase order created locally.", "success");
        App.navigate("purchase", "orders");
      });
    }
  },

  deletePO(poId, dbId) {
    UI.showConfirm({
      title: "Delete Purchase Order?",
      message: `Are you sure you want to delete ${poId}? This action cannot be undone.`,
      confirmText: "Yes, Delete PO",
      confirmVariant: "danger",
      onConfirm: () => {
        fetch(`/purchase/orders/${dbId || poId}`, {
          method: "DELETE",
          headers: {
            "Accept": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || ""
          }
        })
        .then(() => {
          UI.showToast("PO Deleted", `${poId} removed.`, "info");
          ERPState.syncWithBackend().then(() => App.refreshCurrentView());
        })
        .catch(() => {
          ERPState.deletePurchaseOrder(poId);
          UI.showToast("PO Deleted", `${poId} removed locally.`, "info");
          App.refreshCurrentView();
        });
      }
    });
  },

  printPO(poId) {
    const po = (ERPState.data.purchaseOrders || []).find(o => (o.po_number || o.poNumber || o.id) === poId);
    if (!po) return;

    const items = po.items || [
      { item_name: "Raw Material Fabric", ordered_qty: 1000, unit: "Meters", rate: 145, tax_percent: 5, total_amount: 152250 }
    ];

    const printHtml = `
      <div class="print-container" style="padding:24px; font-family:sans-serif; color:#0f172a;">
        <div style="display:flex; justify-content:space-between; border-bottom:2px solid #2563eb; padding-bottom:16px; margin-bottom:20px;">
          <div>
            <h2 style="color:#2563eb; margin:0 0 4px 0; font-size:1.5rem;">FASHIONWORKS GARMENTS PVT. LTD.</h2>
            <p style="margin:0; font-size:0.85rem; color:#475569;">Plot 101, Textile Technology Park, Bhiwandi, Thane 421302</p>
            <p style="margin:0; font-size:0.85rem; color:#475569;">GSTIN: 27AABCF9876K1Z2 • Phone: +91 22 6789 0000</p>
          </div>
          <div style="text-align:right;">
            <h3 style="margin:0; font-size:1.3rem; color:#0f172a;">PURCHASE ORDER</h3>
            <p style="margin:4px 0 0 0; font-weight:700; font-size:1.1rem; color:#2563eb;">${po.po_number || po.poNumber || po.id}</p>
            <p style="margin:0; font-size:0.85rem; color:#64748b;">Date: ${UI.formatDate(po.po_date || po.poDate)}</p>
          </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px; font-size:0.875rem;">
          <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px;">
            <strong style="color:#64748b; font-size:0.75rem; text-transform:uppercase;">Vendor Details:</strong>
            <h4 style="margin:6px 0 4px 0; color:#0f172a;">${po.vendor_name || po.vendorName || (po.vendor ? po.vendor.name : 'Vendor')}</h4>
            <p style="margin:0; color:#475569;">Delivery Location: ${po.warehouse_location || po.warehouse || 'Main Store - Unit 1'}</p>
          </div>
          <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px;">
            <strong style="color:#64748b; font-size:0.75rem; text-transform:uppercase;">Delivery Terms:</strong>
            <p style="margin:6px 0 2px 0;">Expected Date: <strong>${UI.formatDate(po.expected_delivery_date || po.delivery_date || po.expectedDate)}</strong></p>
            <p style="margin:0; color:#475569;">Payment: <strong>${po.payment_status || 'Pending'}</strong></p>
          </div>
        </div>

        <table style="width:100%; border-collapse:collapse; margin-bottom:20px; font-size:0.875rem;">
          <thead>
            <tr style="background:#f1f5f9; border-bottom:2px solid #cbd5e1; text-align:left;">
              <th style="padding:10px;">#</th>
              <th style="padding:10px;">Item Description</th>
              <th style="padding:10px; text-align:right;">Ordered Qty</th>
              <th style="padding:10px; text-align:right;">Unit Rate (₹)</th>
              <th style="padding:10px; text-align:right;">GST %</th>
              <th style="padding:10px; text-align:right;">Total (₹)</th>
            </tr>
          </thead>
          <tbody>
            ${items.map((it, i) => `
              <tr style="border-bottom:1px solid #e2e8f0;">
                <td style="padding:10px;">${i + 1}</td>
                <td style="padding:10px;"><strong>${it.item_name || it.item}</strong> (${it.item_code || it.code || 'N/A'})</td>
                <td style="padding:10px; text-align:right; font-family:monospace;">${Number(it.ordered_qty || it.qty || 0).toLocaleString('en-IN')} ${it.unit || 'M'}</td>
                <td style="padding:10px; text-align:right; font-family:monospace;">₹${Number(it.rate || 0).toFixed(2)}</td>
                <td style="padding:10px; text-align:right;">${it.tax_percent !== undefined ? it.tax_percent : (it.tax || 5)}%</td>
                <td style="padding:10px; text-align:right; font-family:monospace; font-weight:700;">₹${Number(it.total_amount || it.amount || 0).toLocaleString('en-IN', {minimumFractionDigits:2})}</td>
              </tr>
            `).join('')}
          </tbody>
        </table>

        <div style="display:flex; justify-content:flex-end; margin-bottom:30px;">
          <div style="width:280px; font-size:0.9rem; line-height:1.8;">
            <div style="display:flex; justify-content:space-between;"><span>Subtotal:</span> <strong style="font-family:monospace;">₹${Number(po.subtotal || 0).toLocaleString('en-IN', {minimumFractionDigits:2})}</strong></div>
            <div style="display:flex; justify-content:space-between;"><span>Total Tax:</span> <strong style="font-family:monospace;">₹${Number(po.tax_total || po.tax_amount || 0).toLocaleString('en-IN', {minimumFractionDigits:2})}</strong></div>
            <div style="display:flex; justify-content:space-between; font-size:1.15rem; font-weight:800; color:#2563eb; border-top:2px solid #cbd5e1; padding-top:6px; margin-top:6px;">
              <span>Grand Total:</span> <strong style="font-family:monospace;">₹${Number(po.grand_total || po.grandTotal || 0).toLocaleString('en-IN', {minimumFractionDigits:2})}</strong>
            </div>
          </div>
        </div>

        <div style="display:flex; justify-content:space-between; border-top:1px solid #cbd5e1; padding-top:24px; margin-top:40px; font-size:0.8rem; color:#64748b;">
          <div>Prepared By: <strong>Store Purchase Officer</strong></div>
          <div>Authorized Signatory: ________________________</div>
        </div>
      </div>
    `;

    UI.openModal({
      title: `Purchase Order Print Document (${poId})`,
      content: printHtml,
      footer: `
        <button class="btn btn-secondary" onclick="UI.closeModal()">Close</button>
        <button class="btn btn-primary" onclick="window.print()">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
          Print Purchase Order
        </button>
      `,
      size: "modal-lg"
    });
  },

  // 4. SEARCH / FILTERS
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
