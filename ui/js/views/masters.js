/* ==========================================================================
   MASTERS MANAGEMENT VIEW - CUSTOMERS, VENDORS, JOB WORKERS, ITEMS & SIZES
   GarmentERP
   ========================================================================== */

const MastersView = {
  // 1. CUSTOMER MASTER
  renderCustomers() {
    const customers = ERPState.data.customers;

    return `
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left">
            <div class="table-search-box">
              <svg class="table-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              <input type="text" class="table-search-input" id="cust-search" placeholder="Search by customer name, city, GSTIN..." oninput="MastersView.filterCustomers()">
            </div>
            <select class="table-filter-select" id="cust-status-filter" onchange="MastersView.filterCustomers()">
              <option value="">All Statuses</option>
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
            </select>
          </div>

          <div class="table-toolbar-right">
            <button class="btn btn-secondary btn-sm" onclick="MastersView.exportCustomers()">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
              Export CSV
            </button>
            <button class="btn btn-primary btn-sm" onclick="MastersView.openCustomerModal()">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Add Customer
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table" id="customers-table">
            <thead>
              <tr>
                <th>Customer ID</th>
                <th>Customer Name</th>
                <th>Contact Person</th>
                <th>Mobile</th>
                <th>GSTIN</th>
                <th>City</th>
                <th>Credit Limit</th>
                <th>Outstanding</th>
                <th>Status</th>
                <th style="text-align:right;">Actions</th>
              </tr>
            </thead>
            <tbody>
              ${customers.map(c => `
                <tr id="cust-row-${c.id}">
                  <td class="mono-cell font-bold" style="color:var(--primary-600);">${c.id}</td>
                  <td class="primary-cell">
                    <a href="javascript:void(0)" onclick="MastersView.openCustomerDrawer('${c.id}')">${c.name}</a>
                    <div style="font-size:0.75rem; color:var(--slate-400);">${c.companyName}</div>
                  </td>
                  <td>${c.contactPerson}</td>
                  <td>${c.mobile}</td>
                  <td class="mono-cell">${c.gstin}</td>
                  <td>${c.city}</td>
                  <td>${UI.formatCurrency(c.creditLimit)}</td>
                  <td class="font-bold" style="color:${c.outstanding > 0 ? 'var(--danger-600)' : 'var(--success-600)'};">
                    ${UI.formatCurrency(c.outstanding)}
                  </td>
                  <td>${UI.formatStatusBadge(c.status)}</td>
                  <td class="table-actions">
                    <button class="table-action-btn view" title="View Profile" onclick="MastersView.openCustomerDrawer('${c.id}')">View</button>
                    <button class="table-action-btn edit" title="Edit Customer" onclick="MastersView.openCustomerModal('${c.id}')">Edit</button>
                    <button class="table-action-btn delete" title="Delete Customer" onclick="MastersView.confirmDeleteCustomer('${c.id}')">Delete</button>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>

        <div class="table-pagination">
          <div class="pagination-info">Showing <strong>${customers.length}</strong> of <strong>${customers.length}</strong> customers</div>
          <div class="pagination-controls">
            <button class="page-btn" disabled>Previous</button>
            <button class="page-btn active">1</button>
            <button class="page-btn" disabled>Next</button>
          </div>
        </div>
      </div>
    `;
  },

  filterCustomers() {
    const q = (document.getElementById("cust-search")?.value || "").toLowerCase();
    const st = (document.getElementById("cust-status-filter")?.value || "").toLowerCase();
    const rows = document.querySelectorAll("#customers-table tbody tr");

    rows.forEach(r => {
      const text = r.innerText.toLowerCase();
      const matchQ = !q || text.includes(q);
      const matchSt = !st || text.includes(st);
      r.style.display = matchQ && matchSt ? "" : "none";
    });
  },

  openCustomerModal(customerId = null) {
    const isEdit = !!customerId;
    const cust = isEdit ? ERPState.data.customers.find(c => c.id === customerId) : {};

    const content = `
      <form id="customer-form">
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Customer Brand / Display Name <span class="required-star">*</span></label>
            <input type="text" class="form-control" id="cust-name" required value="${cust.name || ''}" placeholder="e.g. ABC Fashion">
          </div>

          <div class="form-group">
            <label class="form-label">Registered Company Name <span class="required-star">*</span></label>
            <input type="text" class="form-control" id="cust-company" required value="${cust.companyName || ''}" placeholder="e.g. ABC Fashion Apparels Pvt Ltd">
          </div>

          <div class="form-group">
            <label class="form-label">Contact Person <span class="required-star">*</span></label>
            <input type="text" class="form-control" id="cust-contact" required value="${cust.contactPerson || ''}" placeholder="e.g. Rajesh Khanna">
          </div>

          <div class="form-group">
            <label class="form-label">Mobile Number <span class="required-star">*</span></label>
            <input type="text" class="form-control" id="cust-mobile" required value="${cust.mobile || ''}" placeholder="+91 98201 12345">
          </div>

          <div class="form-group">
            <label class="form-label">Email Address <span class="required-star">*</span></label>
            <input type="email" class="form-control" id="cust-email" required value="${cust.email || ''}" placeholder="purchase@abcfashion.com">
          </div>

          <div class="form-group">
            <label class="form-label">GSTIN <span class="required-star">*</span></label>
            <input type="text" class="form-control font-mono" id="cust-gstin" required value="${cust.gstin || ''}" placeholder="27AAACA1234A1Z5">
          </div>

          <div class="form-group col-span-2">
            <label class="form-label">Address</label>
            <textarea class="form-control" id="cust-address" placeholder="Unit / Street address">${cust.address || ''}</textarea>
          </div>

          <div class="form-group">
            <label class="form-label">City</label>
            <input type="text" class="form-control" id="cust-city" value="${cust.city || 'Mumbai'}">
          </div>

          <div class="form-group">
            <label class="form-label">State</label>
            <input type="text" class="form-control" id="cust-state" value="${cust.state || 'Maharashtra'}">
          </div>

          <div class="form-group">
            <label class="form-label">Pincode</label>
            <input type="text" class="form-control" id="cust-pincode" value="${cust.pincode || '400013'}">
          </div>

          <div class="form-group">
            <label class="form-label">Credit Limit (₹)</label>
            <input type="number" class="form-control" id="cust-credit" value="${cust.creditLimit || 2500000}">
          </div>

          <div class="form-group">
            <label class="form-label">Payment Terms</label>
            <select class="form-control" id="cust-terms">
              <option value="Immediate" ${cust.paymentTerms === 'Immediate' ? 'selected' : ''}>Immediate</option>
              <option value="15 Days" ${cust.paymentTerms === '15 Days' ? 'selected' : ''}>15 Days</option>
              <option value="30 Days" ${cust.paymentTerms === '30 Days' || !cust.paymentTerms ? 'selected' : ''}>30 Days</option>
              <option value="45 Days" ${cust.paymentTerms === '45 Days' ? 'selected' : ''}>45 Days</option>
              <option value="60 Days" ${cust.paymentTerms === '60 Days' ? 'selected' : ''}>60 Days</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Status</label>
            <select class="form-control" id="cust-status">
              <option value="Active" ${cust.status === 'Active' ? 'selected' : ''}>Active</option>
              <option value="Inactive" ${cust.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
            </select>
          </div>
        </div>
      </form>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="UI.closeModal()">Cancel</button>
      <button class="btn btn-primary" id="btn-save-cust">${isEdit ? 'Update Customer' : 'Save Customer'}</button>
    `;

    UI.openModal({ title: isEdit ? `Edit Customer - ${cust.name}` : "Add New Customer", content, footer, size: "modal-lg" });

    document.getElementById("btn-save-cust").onclick = () => {
      const name = document.getElementById("cust-name").value.trim();
      if (!name) return UI.showToast("Required Field", "Customer Name is required", "error");

      const payload = {
        name,
        companyName: document.getElementById("cust-company").value.trim(),
        contactPerson: document.getElementById("cust-contact").value.trim(),
        mobile: document.getElementById("cust-mobile").value.trim(),
        email: document.getElementById("cust-email").value.trim(),
        gstin: document.getElementById("cust-gstin").value.trim(),
        address: document.getElementById("cust-address").value.trim(),
        city: document.getElementById("cust-city").value.trim(),
        state: document.getElementById("cust-state").value.trim(),
        pincode: document.getElementById("cust-pincode").value.trim(),
        creditLimit: Number(document.getElementById("cust-credit").value),
        paymentTerms: document.getElementById("cust-terms").value,
        status: document.getElementById("cust-status").value
      };

      if (isEdit) {
        ERPState.updateCustomer(customerId, payload);
        UI.showToast("Customer Updated", `${name} was updated successfully`, "success");
      } else {
        ERPState.addCustomer(payload);
        UI.showToast("Customer Added", `${name} added to Customer Master`, "success");
      }

      UI.closeModal();
      App.refreshCurrentView();
    };
  },

  openCustomerDrawer(customerId) {
    const cust = ERPState.data.customers.find(c => c.id === customerId);
    if (!cust) return;

    const orders = ERPState.data.salesOrders.filter(o => o.customerId === customerId || o.customer === cust.name);

    const content = `
      <div style="display:flex; flex-direction:column; gap:20px;">
        <!-- KPI Row -->
        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:12px;">
          <div style="background:var(--slate-50); padding:12px; border-radius:var(--radius-md); border:1px solid var(--slate-200);">
            <div style="font-size:0.7rem; color:var(--slate-500); font-weight:700; text-transform:uppercase;">Outstanding</div>
            <div style="font-size:1.15rem; font-weight:800; color:var(--danger-600); margin-top:2px;">${UI.formatCurrency(cust.outstanding)}</div>
          </div>
          <div style="background:var(--slate-50); padding:12px; border-radius:var(--radius-md); border:1px solid var(--slate-200);">
            <div style="font-size:0.7rem; color:var(--slate-500); font-weight:700; text-transform:uppercase;">Credit Limit</div>
            <div style="font-size:1.15rem; font-weight:800; color:var(--slate-800); margin-top:2px;">${UI.formatCurrency(cust.creditLimit)}</div>
          </div>
          <div style="background:var(--slate-50); padding:12px; border-radius:var(--radius-md); border:1px solid var(--slate-200);">
            <div style="font-size:0.7rem; color:var(--slate-500); font-weight:700; text-transform:uppercase;">Payment Terms</div>
            <div style="font-size:1.15rem; font-weight:800; color:var(--primary-700); margin-top:2px;">${cust.paymentTerms}</div>
          </div>
        </div>

        <!-- Details Card -->
        <div style="border:1px solid var(--slate-200); border-radius:var(--radius-lg); padding:16px;">
          <h4 style="font-size:0.9rem; margin-bottom:12px; color:var(--slate-900);">Company Information</h4>
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:0.825rem;">
            <div><span style="color:var(--slate-500);">Contact Person:</span> <strong>${cust.contactPerson}</strong></div>
            <div><span style="color:var(--slate-500);">Mobile:</span> <strong>${cust.mobile}</strong></div>
            <div><span style="color:var(--slate-500);">Email:</span> <strong>${cust.email}</strong></div>
            <div><span style="color:var(--slate-500);">GSTIN:</span> <code class="font-mono">${cust.gstin}</code></div>
            <div style="grid-column:span 2;"><span style="color:var(--slate-500);">Billing Address:</span> ${cust.address}, ${cust.city}, ${cust.state} - ${cust.pincode}</div>
          </div>
        </div>

        <!-- Recent Sales Orders -->
        <div>
          <h4 style="font-size:0.9rem; margin-bottom:8px; color:var(--slate-900);">Active Sales Orders (${orders.length})</h4>
          ${orders.length > 0 ? `
            <table class="data-table" style="font-size:0.8rem;">
              <thead>
                <tr>
                  <th>Order No</th>
                  <th>Product</th>
                  <th>Qty</th>
                  <th>Amount</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                ${orders.map(o => `
                  <tr>
                    <td class="mono-cell font-bold">${o.id}</td>
                    <td>${o.product}</td>
                    <td class="font-mono">${o.quantity.toLocaleString('en-IN')} pcs</td>
                    <td>${UI.formatCurrency(o.amount)}</td>
                    <td>${UI.formatStatusBadge(o.status)}</td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          ` : `<p style="font-size:0.825rem; color:var(--slate-500);">No active orders for this customer.</p>`}
        </div>

        <!-- Action Shortcuts -->
        <div style="display:flex; gap:10px; justify-content:flex-end;">
          <button class="btn btn-secondary btn-sm" onclick="MastersView.openCustomerModal('${cust.id}')">Edit Profile</button>
          <button class="btn btn-primary btn-sm" onclick="App.navigate('accounts', 'receipts'); setTimeout(() => AccountsView.openReceiptModal('${cust.name}'), 100); UI.closeDrawer();">
            Receive Payment
          </button>
        </div>
      </div>
    `;

    UI.openDrawer({
      title: cust.name,
      subtitle: `${cust.id} • ${cust.companyName}`,
      tabs: [{ id: "overview", label: "Overview" }, { id: "orders", label: "Orders" }],
      content,
      size: "drawer-lg"
    });
  },

  confirmDeleteCustomer(id) {
    const cust = ERPState.data.customers.find(c => c.id === id);
    if (!cust) return;

    UI.showConfirm({
      title: "Delete Customer?",
      message: `Are you sure you want to delete <strong>${cust.name}</strong>? This action cannot be undone.`,
      confirmText: "Delete Customer",
      isDanger: true,
      onConfirm: () => {
        ERPState.deleteCustomer(id);
        UI.showToast("Customer Deleted", `${cust.name} was removed`, "warning");
        App.refreshCurrentView();
      }
    });
  },

  exportCustomers() {
    const headers = ["Customer ID", "Customer Name", "Company Name", "Contact Person", "Mobile", "Email", "GSTIN", "City", "Credit Limit", "Outstanding", "Status"];
    const rows = ERPState.data.customers.map(c => [
      c.id, c.name, c.companyName, c.contactPerson, c.mobile, c.email, c.gstin, c.city, c.creditLimit, c.outstanding, c.status
    ]);
    UI.exportToCSV("Customer_Master_Report", headers, rows);
  },

  // 2. VENDOR MASTER
  renderVendors() {
    const vendors = ERPState.data.vendors;

    return `
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left">
            <div class="table-search-box">
              <svg class="table-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              <input type="text" class="table-search-input" placeholder="Search vendors by name, category, city..." oninput="MastersView.filterGenericTable('vendors-table', this.value)">
            </div>
          </div>

          <div class="table-toolbar-right">
            <button class="btn btn-secondary btn-sm" onclick="MastersView.exportVendors()">Export CSV</button>
            <button class="btn btn-primary btn-sm" onclick="MastersView.openVendorModal()">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Add Vendor
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table" id="vendors-table">
            <thead>
              <tr>
                <th>Vendor ID</th>
                <th>Vendor Name</th>
                <th>Category</th>
                <th>Contact Person</th>
                <th>Mobile</th>
                <th>GSTIN</th>
                <th>Payment Terms</th>
                <th>Outstanding</th>
                <th>Status</th>
                <th style="text-align:right;">Actions</th>
              </tr>
            </thead>
            <tbody>
              ${vendors.map(v => `
                <tr>
                  <td class="mono-cell font-bold" style="color:var(--primary-600);">${v.id}</td>
                  <td class="primary-cell">${v.name}</td>
                  <td><span class="badge badge-slate">${v.category}</span></td>
                  <td>${v.contactPerson}</td>
                  <td>${v.mobile}</td>
                  <td class="mono-cell">${v.gstin}</td>
                  <td>${v.paymentTerms}</td>
                  <td class="font-bold" style="color:${v.outstanding > 0 ? 'var(--danger-600)' : 'var(--success-600)'};">${UI.formatCurrency(v.outstanding)}</td>
                  <td>${UI.formatStatusBadge(v.status)}</td>
                  <td class="table-actions">
                    <button class="table-action-btn edit" onclick="MastersView.openVendorModal('${v.id}')">Edit</button>
                    <button class="table-action-btn delete" onclick="MastersView.confirmDeleteVendor('${v.id}')">Delete</button>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      </div>
    `;
  },

  openVendorModal(vendorId = null) {
    const isEdit = !!vendorId;
    const vnd = isEdit ? ERPState.data.vendors.find(v => v.id === vendorId) : {};

    const content = `
      <form id="vendor-form">
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Vendor Name <span class="required-star">*</span></label>
            <input type="text" class="form-control" id="vnd-name" required value="${vnd.name || ''}" placeholder="e.g. Shree Fabrics">
          </div>

          <div class="form-group">
            <label class="form-label">Supply Category <span class="required-star">*</span></label>
            <input type="text" class="form-control" id="vnd-category" required value="${vnd.category || 'Fabric Supplier'}" placeholder="e.g. Fabric Supplier">
          </div>

          <div class="form-group">
            <label class="form-label">Contact Person</label>
            <input type="text" class="form-control" id="vnd-contact" value="${vnd.contactPerson || ''}">
          </div>

          <div class="form-group">
            <label class="form-label">Mobile</label>
            <input type="text" class="form-control" id="vnd-mobile" value="${vnd.mobile || ''}">
          </div>

          <div class="form-group">
            <label class="form-label">GSTIN</label>
            <input type="text" class="form-control font-mono" id="vnd-gstin" value="${vnd.gstin || ''}">
          </div>

          <div class="form-group">
            <label class="form-label">City</label>
            <input type="text" class="form-control" id="vnd-city" value="${vnd.city || 'Surat'}">
          </div>

          <div class="form-group">
            <label class="form-label">Payment Terms</label>
            <select class="form-control" id="vnd-terms">
              <option value="15 Days" ${vnd.paymentTerms === '15 Days' ? 'selected' : ''}>15 Days</option>
              <option value="30 Days" ${vnd.paymentTerms === '30 Days' || !vnd.paymentTerms ? 'selected' : ''}>30 Days</option>
              <option value="45 Days" ${vnd.paymentTerms === '45 Days' ? 'selected' : ''}>45 Days</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Status</label>
            <select class="form-control" id="vnd-status">
              <option value="Active" ${vnd.status === 'Active' ? 'selected' : ''}>Active</option>
              <option value="Inactive" ${vnd.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
            </select>
          </div>
        </div>
      </form>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="UI.closeModal()">Cancel</button>
      <button class="btn btn-primary" id="btn-save-vnd">${isEdit ? 'Update Vendor' : 'Save Vendor'}</button>
    `;

    UI.openModal({ title: isEdit ? `Edit Vendor - ${vnd.name}` : "Add New Vendor", content, footer, size: "modal-lg" });

    document.getElementById("btn-save-vnd").onclick = () => {
      const name = document.getElementById("vnd-name").value.trim();
      if (!name) return UI.showToast("Required Field", "Vendor Name is required", "error");

      const payload = {
        name,
        category: document.getElementById("vnd-category").value.trim(),
        contactPerson: document.getElementById("vnd-contact").value.trim(),
        mobile: document.getElementById("vnd-mobile").value.trim(),
        gstin: document.getElementById("vnd-gstin").value.trim(),
        city: document.getElementById("vnd-city").value.trim(),
        paymentTerms: document.getElementById("vnd-terms").value,
        status: document.getElementById("vnd-status").value
      };

      if (isEdit) {
        ERPState.updateVendor(vendorId, payload);
        UI.showToast("Vendor Updated", `${name} updated successfully`, "success");
      } else {
        ERPState.addVendor(payload);
        UI.showToast("Vendor Added", `${name} added to Vendor Master`, "success");
      }

      UI.closeModal();
      App.refreshCurrentView();
    };
  },

  confirmDeleteVendor(id) {
    const vnd = ERPState.data.vendors.find(v => v.id === id);
    if (!vnd) return;

    UI.showConfirm({
      title: "Delete Vendor?",
      message: `Are you sure you want to delete vendor <strong>${vnd.name}</strong>?`,
      confirmText: "Delete Vendor",
      isDanger: true,
      onConfirm: () => {
        ERPState.deleteVendor(id);
        UI.showToast("Vendor Deleted", `${vnd.name} removed`, "warning");
        App.refreshCurrentView();
      }
    });
  },

  exportVendors() {
    const headers = ["Vendor ID", "Vendor Name", "Category", "Contact Person", "Mobile", "GSTIN", "Payment Terms", "Outstanding", "Status"];
    const rows = ERPState.data.vendors.map(v => [v.id, v.name, v.category, v.contactPerson, v.mobile, v.gstin, v.paymentTerms, v.outstanding, v.status]);
    UI.exportToCSV("Vendor_Master_Report", headers, rows);
  },

  // 3. JOB WORKER MASTER
  renderJobWorkers() {
    const workers = ERPState.data.jobWorkers;

    return `
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left">
            <div class="table-search-box">
              <svg class="table-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              <input type="text" class="table-search-input" placeholder="Search job workers by process, name, city..." oninput="MastersView.filterGenericTable('jw-table', this.value)">
            </div>
          </div>

          <div class="table-toolbar-right">
            <button class="btn btn-secondary btn-sm" onclick="MastersView.exportJobWorkers()">Export CSV</button>
            <button class="btn btn-primary btn-sm" onclick="MastersView.openJobWorkerModal()">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Add Job Worker
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table" id="jw-table">
            <thead>
              <tr>
                <th>Worker ID</th>
                <th>Job Worker Name</th>
                <th>Assigned Process</th>
                <th>Standard Rate</th>
                <th>Daily Capacity</th>
                <th>City</th>
                <th>Outstanding</th>
                <th>Status</th>
                <th style="text-align:right;">Actions</th>
              </tr>
            </thead>
            <tbody>
              ${workers.map(w => `
                <tr>
                  <td class="mono-cell font-bold" style="color:var(--primary-600);">${w.id}</td>
                  <td class="primary-cell">${w.name}</td>
                  <td><span class="badge badge-purple">${w.process}</span></td>
                  <td class="font-bold">₹${w.rate} / ${w.rateUnit || 'Piece'}</td>
                  <td>${w.capacityPerDay.toLocaleString('en-IN')} pcs/day</td>
                  <td>${w.city}</td>
                  <td class="font-bold">${UI.formatCurrency(w.outstanding)}</td>
                  <td>${UI.formatStatusBadge(w.status)}</td>
                  <td class="table-actions">
                    <button class="table-action-btn edit" onclick="MastersView.openJobWorkerModal('${w.id}')">Edit</button>
                    <button class="table-action-btn delete" onclick="MastersView.confirmDeleteJobWorker('${w.id}')">Delete</button>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      </div>
    `;
  },

  openJobWorkerModal(workerId = null) {
    const isEdit = !!workerId;
    const worker = isEdit ? ERPState.data.jobWorkers.find(w => w.id === workerId) : {};

    const content = `
      <form id="jw-form">
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Job Worker / Unit Name <span class="required-star">*</span></label>
            <input type="text" class="form-control" id="jw-name" required value="${worker.name || ''}" placeholder="e.g. Raj Stitching">
          </div>

          <div class="form-group">
            <label class="form-label">Process Specialization <span class="required-star">*</span></label>
            <select class="form-control" id="jw-process">
              ${ERPState.data.processes.map(p => `
                <option value="${p}" ${worker.process === p ? 'selected' : ''}>${p}</option>
              `).join('')}
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Rate (₹) <span class="required-star">*</span></label>
            <input type="number" class="form-control" id="jw-rate" required value="${worker.rate || 25}">
          </div>

          <div class="form-group">
            <label class="form-label">Rate Unit</label>
            <input type="text" class="form-control" id="jw-unit" value="${worker.rateUnit || 'Piece'}">
          </div>

          <div class="form-group">
            <label class="form-label">Daily Capacity (Pcs)</label>
            <input type="number" class="form-control" id="jw-capacity" value="${worker.capacityPerDay || 2000}">
          </div>

          <div class="form-group">
            <label class="form-label">Contact Person</label>
            <input type="text" class="form-control" id="jw-contact" value="${worker.contactPerson || ''}">
          </div>

          <div class="form-group">
            <label class="form-label">Mobile</label>
            <input type="text" class="form-control" id="jw-mobile" value="${worker.mobile || ''}">
          </div>

          <div class="form-group">
            <label class="form-label">City</label>
            <input type="text" class="form-control" id="jw-city" value="${worker.city || 'Bhiwandi'}">
          </div>
        </div>
      </form>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="UI.closeModal()">Cancel</button>
      <button class="btn btn-primary" id="btn-save-jw">${isEdit ? 'Update Job Worker' : 'Save Job Worker'}</button>
    `;

    UI.openModal({ title: isEdit ? `Edit Job Worker - ${worker.name}` : "Add Job Worker", content, footer, size: "modal-lg" });

    document.getElementById("btn-save-jw").onclick = () => {
      const name = document.getElementById("jw-name").value.trim();
      if (!name) return UI.showToast("Required Field", "Job Worker Name is required", "error");

      const payload = {
        name,
        process: document.getElementById("jw-process").value,
        rate: Number(document.getElementById("jw-rate").value),
        rateUnit: document.getElementById("jw-unit").value,
        capacityPerDay: Number(document.getElementById("jw-capacity").value),
        contactPerson: document.getElementById("jw-contact").value.trim(),
        mobile: document.getElementById("jw-mobile").value.trim(),
        city: document.getElementById("jw-city").value.trim()
      };

      if (isEdit) {
        ERPState.updateJobWorker(workerId, payload);
        UI.showToast("Job Worker Updated", `${name} updated successfully`, "success");
      } else {
        ERPState.addJobWorker(payload);
        UI.showToast("Job Worker Added", `${name} registered for ${payload.process}`, "success");
      }

      UI.closeModal();
      App.refreshCurrentView();
    };
  },

  confirmDeleteJobWorker(id) {
    const jw = ERPState.data.jobWorkers.find(w => w.id === id);
    if (!jw) return;

    UI.showConfirm({
      title: "Delete Job Worker?",
      message: `Are you sure you want to delete <strong>${jw.name}</strong>?`,
      confirmText: "Delete",
      isDanger: true,
      onConfirm: () => {
        ERPState.deleteJobWorker(id);
        UI.showToast("Job Worker Deleted", `${jw.name} removed`, "warning");
        App.refreshCurrentView();
      }
    });
  },

  exportJobWorkers() {
    const headers = ["Worker ID", "Name", "Process", "Rate", "Daily Capacity", "Contact", "Mobile", "City", "Outstanding", "Status"];
    const rows = ERPState.data.jobWorkers.map(w => [w.id, w.name, w.process, w.rate, w.capacityPerDay, w.contactPerson, w.mobile, w.city, w.outstanding, w.status]);
    UI.exportToCSV("Job_Worker_Master", headers, rows);
  },

  // 4. ITEM MASTER
  renderItems() {
    const items = ERPState.data.items;

    return `
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left">
            <div class="table-search-box">
              <svg class="table-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              <input type="text" class="table-search-input" placeholder="Search by item name, code, category..." oninput="MastersView.filterGenericTable('items-table', this.value)">
            </div>
          </div>

          <div class="table-toolbar-right">
            <button class="btn btn-secondary btn-sm" onclick="MastersView.exportItems()">Export CSV</button>
            <button class="btn btn-primary btn-sm" onclick="MastersView.openItemModal()">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Add Item
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table" id="items-table">
            <thead>
              <tr>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Type</th>
                <th>Category</th>
                <th>Unit</th>
                <th>HSN</th>
                <th>Rate (₹)</th>
                <th>Current Stock</th>
                <th>Reorder Level</th>
                <th>Status</th>
                <th style="text-align:right;">Actions</th>
              </tr>
            </thead>
            <tbody>
              ${items.map(i => `
                <tr>
                  <td class="mono-cell font-bold" style="color:var(--primary-600);">${i.code}</td>
                  <td class="primary-cell">${i.name}</td>
                  <td><span class="badge badge-primary">${i.type}</span></td>
                  <td>${i.category}</td>
                  <td>${i.unit}</td>
                  <td class="mono-cell">${i.hsn || '6109'}</td>
                  <td class="font-bold">₹${i.rate}</td>
                  <td class="font-bold font-mono" style="color:${i.currentStock <= i.reorderLevel ? 'var(--danger-600)' : 'var(--slate-900)'};">
                    ${i.currentStock.toLocaleString('en-IN')} ${i.unit}
                    ${i.currentStock <= i.reorderLevel ? '<span class="badge badge-danger" style="margin-left:4px;">LOW</span>' : ''}
                  </td>
                  <td class="font-mono text-muted">${i.reorderLevel.toLocaleString('en-IN')}</td>
                  <td>${UI.formatStatusBadge(i.status)}</td>
                  <td class="table-actions">
                    <button class="table-action-btn edit" onclick="MastersView.openItemModal('${i.id}')">Edit</button>
                    <button class="table-action-btn delete" onclick="MastersView.confirmDeleteItem('${i.id}')">Delete</button>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      </div>
    `;
  },

  openItemModal(itemId = null) {
    const isEdit = !!itemId;
    const item = isEdit ? ERPState.data.items.find(i => i.id === itemId) : {};

    const content = `
      <form id="item-form">
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Item Code <span class="required-star">*</span></label>
            <input type="text" class="form-control font-mono" id="itm-code" required value="${item.code || `GAR-SKU-${Date.now().toString().slice(-4)}`}">
          </div>

          <div class="form-group">
            <label class="form-label">Item Name <span class="required-star">*</span></label>
            <input type="text" class="form-control" id="itm-name" required value="${item.name || ''}" placeholder="e.g. Premium Cotton Crew Neck T-Shirt">
          </div>

          <div class="form-group">
            <label class="form-label">Item Type</label>
            <select class="form-control" id="itm-type">
              <option value="Raw Material" ${item.type === 'Raw Material' ? 'selected' : ''}>Raw Material</option>
              <option value="Semi Finished" ${item.type === 'Semi Finished' ? 'selected' : ''}>Semi Finished</option>
              <option value="Finished Goods" ${item.type === 'Finished Goods' || !item.type ? 'selected' : ''}>Finished Goods</option>
              <option value="Accessories" ${item.type === 'Accessories' ? 'selected' : ''}>Accessories</option>
              <option value="Packaging" ${item.type === 'Packaging' ? 'selected' : ''}>Packaging</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Category</label>
            <input type="text" class="form-control" id="itm-cat" value="${item.category || 'T-Shirts'}">
          </div>

          <div class="form-group">
            <label class="form-label">Unit of Measure</label>
            <select class="form-control" id="itm-unit">
              <option value="Pieces" ${item.unit === 'Pieces' || !item.unit ? 'selected' : ''}>Pieces</option>
              <option value="Meters" ${item.unit === 'Meters' ? 'selected' : ''}>Meters</option>
              <option value="Gross" ${item.unit === 'Gross' ? 'selected' : ''}>Gross (144 pcs)</option>
              <option value="Kgs" ${item.unit === 'Kgs' ? 'selected' : ''}>Kgs</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Standard Rate (₹)</label>
            <input type="number" class="form-control" id="itm-rate" value="${item.rate || 350}">
          </div>

          <div class="form-group">
            <label class="form-label">Opening Stock</label>
            <input type="number" class="form-control" id="itm-opening" value="${item.openingStock || 0}">
          </div>

          <div class="form-group">
            <label class="form-label">Reorder Level Alert</label>
            <input type="number" class="form-control" id="itm-reorder" value="${item.reorderLevel || 500}">
          </div>

          <div class="form-group">
            <label class="form-label">HSN Code</label>
            <input type="text" class="form-control font-mono" id="itm-hsn" value="${item.hsn || '6109'}">
          </div>

          <div class="form-group">
            <label class="form-label">Fabric / Material Spec</label>
            <input type="text" class="form-control" id="itm-fabric" value="${item.fabric || '100% Cotton'}">
          </div>
        </div>
      </form>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="UI.closeModal()">Cancel</button>
      <button class="btn btn-primary" id="btn-save-itm">${isEdit ? 'Update Item' : 'Save Item'}</button>
    `;

    UI.openModal({ title: isEdit ? `Edit Item - ${item.name}` : "Add New Item", content, footer, size: "modal-lg" });

    document.getElementById("btn-save-itm").onclick = () => {
      const name = document.getElementById("itm-name").value.trim();
      const code = document.getElementById("itm-code").value.trim();
      if (!name || !code) return UI.showToast("Required Field", "Item Code and Name are required", "error");

      const payload = {
        code,
        name,
        type: document.getElementById("itm-type").value,
        category: document.getElementById("itm-cat").value.trim(),
        unit: document.getElementById("itm-unit").value,
        rate: Number(document.getElementById("itm-rate").value),
        openingStock: Number(document.getElementById("itm-opening").value),
        reorderLevel: Number(document.getElementById("itm-reorder").value),
        hsn: document.getElementById("itm-hsn").value.trim(),
        fabric: document.getElementById("itm-fabric").value.trim()
      };

      if (isEdit) {
        ERPState.updateItem(itemId, payload);
        UI.showToast("Item Updated", `${name} updated successfully`, "success");
      } else {
        ERPState.addItem(payload);
        UI.showToast("Item Created", `${name} added to inventory catalog`, "success");
      }

      UI.closeModal();
      App.refreshCurrentView();
    };
  },

  confirmDeleteItem(id) {
    const itm = ERPState.data.items.find(i => i.id === id);
    if (!itm) return;

    UI.showConfirm({
      title: "Delete Item?",
      message: `Are you sure you want to delete <strong>${itm.name}</strong> from catalog?`,
      confirmText: "Delete",
      isDanger: true,
      onConfirm: () => {
        ERPState.deleteItem(id);
        UI.showToast("Item Deleted", `${itm.name} deleted`, "warning");
        App.refreshCurrentView();
      }
    });
  },

  exportItems() {
    const headers = ["Item Code", "Item Name", "Type", "Category", "Unit", "HSN", "Rate", "Stock", "Reorder Level", "Status"];
    const rows = ERPState.data.items.map(i => [i.code, i.name, i.type, i.category, i.unit, i.hsn, i.rate, i.currentStock, i.reorderLevel, i.status]);
    UI.exportToCSV("Item_Master_Report", headers, rows);
  },

  // 5. SIZE & COLOR MANAGEMENT MATRIX
  renderSizesAndColors() {
    return `
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px;">
        <!-- Size Master Card -->
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">Garment Size Master</div>
              <div class="card-subtitle">Configured standard sizes for apparel cutting & bundles</div>
            </div>
            <button class="btn btn-primary btn-sm" onclick="UI.showToast('Add Size', 'New size configured', 'info')">+ Add Size</button>
          </div>
          <div style="display:flex; flex-wrap:wrap; gap:10px;">
            ${ERPState.data.sizes.map(s => `
              <div style="padding:12px 20px; background:var(--slate-100); border:1px solid var(--slate-300); border-radius:var(--radius-lg); font-weight:800; font-size:1.1rem; color:var(--slate-900);">
                ${s}
              </div>
            `).join('')}
          </div>
        </div>

        <!-- Color Master Card -->
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">Color Shade Master</div>
              <div class="card-subtitle">Approved dyeing & fabric color palettes</div>
            </div>
            <button class="btn btn-primary btn-sm" onclick="UI.showToast('Add Color', 'New color palette added', 'info')">+ Add Color</button>
          </div>
          <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:10px;">
            ${ERPState.data.colors.map(c => `
              <div style="display:flex; align-items:center; gap:10px; padding:8px 12px; border:1px solid var(--slate-200); border-radius:var(--radius-md); background:var(--slate-50);">
                <span style="width:24px; height:24px; border-radius:50%; background:${c.hex}; border:1px solid rgba(0,0,0,0.15);"></span>
                <span style="font-weight:700; font-size:0.85rem; color:var(--slate-900);">${c.name}</span>
              </div>
            `).join('')}
          </div>
        </div>
      </div>

      <!-- Size x Color Stock Matrix Breakdown -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Sample Inventory Matrix: Premium Cotton T-Shirt (GAR-TSH-001)</div>
            <div class="card-subtitle">Real-time SKU quantities by Color x Size combinations</div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table" style="text-align:center;">
            <thead>
              <tr>
                <th style="text-align:left;">Color Variant</th>
                <th>S</th>
                <th>M</th>
                <th>L</th>
                <th>XL</th>
                <th>XXL</th>
                <th>Total Color Qty</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td style="text-align:left; font-weight:700;"><span style="display:inline-block; width:10px; height:10px; background:#0f172a; border-radius:50%; margin-right:6px;"></span> Black</td>
                <td class="font-mono">150</td>
                <td class="font-mono">300</td>
                <td class="font-mono">450</td>
                <td class="font-mono">200</td>
                <td class="font-mono">100</td>
                <td class="font-bold font-mono">1,200</td>
              </tr>
              <tr>
                <td style="text-align:left; font-weight:700;"><span style="display:inline-block; width:10px; height:10px; background:#1e3a8a; border-radius:50%; margin-right:6px;"></span> Navy Blue</td>
                <td class="font-mono">400</td>
                <td class="font-mono">900</td>
                <td class="font-mono">1,200</td>
                <td class="font-mono">800</td>
                <td class="font-mono">350</td>
                <td class="font-bold font-mono">3,650</td>
              </tr>
              <tr>
                <td style="text-align:left; font-weight:700;"><span style="display:inline-block; width:10px; height:10px; background:#f8fafc; border:1px solid #cbd5e1; border-radius:50%; margin-right:6px;"></span> White</td>
                <td class="font-mono">250</td>
                <td class="font-mono">600</td>
                <td class="font-mono">850</td>
                <td class="font-mono">500</td>
                <td class="font-mono">200</td>
                <td class="font-bold font-mono">2,400</td>
              </tr>
              <tr style="background:var(--slate-100); font-weight:800;">
                <td style="text-align:left;">Total Stock Across Sizes</td>
                <td class="font-mono">800</td>
                <td class="font-mono">1,800</td>
                <td class="font-mono">2,500</td>
                <td class="font-mono">1,500</td>
                <td class="font-mono">650</td>
                <td class="font-mono" style="color:var(--primary-700);">7,250 PCS</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    `;
  },

  filterGenericTable(tableId, query) {
    const q = query.toLowerCase();
    const rows = document.querySelectorAll(`#${tableId} tbody tr`);
    rows.forEach(r => {
      r.style.display = !q || r.innerText.toLowerCase().includes(q) ? "" : "none";
    });
  }
};
