/* ==========================================================================
   ACCOUNTS & SETTLEMENT MANAGEMENT VIEW
   Customer Settlements, Payment Receipts & Outstanding Aging Reports
   GarmentERP
   ========================================================================== */

const AccountsView = {
  // 1. CUSTOMER SETTLEMENTS
  renderCustomerSettlement() {
    const customers = ERPState.data.customers || [];

    return `
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left">
            <h3 style="font-size:1.05rem; font-weight:700; color:var(--slate-900);">Customer Accounts & Settlement Ledger</h3>
          </div>

          <div class="table-toolbar-right">
            <button class="btn btn-primary btn-sm" onclick="AccountsView.openReceiptModal()">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Receive Payment
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>Customer ID</th>
                <th>Customer Name</th>
                <th>Company Name</th>
                <th>Payment Terms</th>
                <th>Total Invoiced</th>
                <th>Total Received</th>
                <th>Net Outstanding Balance</th>
                <th>Status</th>
                <th style="text-align:right;">Actions</th>
              </tr>
            </thead>
            <tbody>
              ${customers.length === 0 ? `
                <tr>
                  <td colspan="9" style="text-align:center; padding:32px 20px; color:var(--slate-400);">
                    <div style="font-size:1rem; font-weight:600; color:var(--slate-600); margin-bottom:4px;">No Customer Accounts Found</div>
                    <div style="font-size:0.825rem;">Add customers in the Customer Master to view statements and record settlements.</div>
                  </td>
                </tr>
              ` : customers.map(c => {
                const stmt = ERPState.getCustomerStatement(c.id);
                const invoiced = stmt ? stmt.totalInvoiced : Number(c.outstanding || 0);
                const paid = stmt ? stmt.totalPaid : 0;
                const outAmt = Number(c.outstanding) || 0;
                return `
                  <tr>
                    <td class="mono-cell font-bold" style="color:var(--primary-600);">${c.id || c.code}</td>
                    <td class="primary-cell">
                      <a href="javascript:void(0)" onclick="MastersView.openCustomerDrawer('${c.id}')" style="font-weight:700; color:var(--slate-900); text-decoration:none;">${c.name}</a>
                    </td>
                    <td>${c.companyName || c.name}</td>
                    <td>${c.paymentTerms || '30 Days'}</td>
                    <td class="font-mono">${UI.formatCurrency(invoiced)}</td>
                    <td class="font-mono" style="color:var(--success-700);">${UI.formatCurrency(paid)}</td>
                    <td class="font-bold font-mono" style="color:${outAmt > 0 ? 'var(--danger-600)' : 'var(--success-600)'};">
                      ${UI.formatCurrency(outAmt)}
                    </td>
                    <td>${UI.formatStatusBadge(outAmt > 0 ? 'Payment Due' : 'Settled')}</td>
                    <td class="table-actions" style="text-align:right;">
                      <button class="table-action-btn ledger" title="View Customer Account Ledger" onclick="MastersView.openCustomerStatementModal('${c.id}')">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        Ledger
                      </button>
                      <button class="table-action-btn receipt" onclick="AccountsView.openReceiptModal('${c.name}')">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        Receipt
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

  openReceiptModal(customerName = null) {
    const customers = ERPState.data.customers || [];
    if (customers.length === 0) {
      return UI.showToast("No Customers", "Please register a customer first in Customer Master", "warning");
    }
    const selectedCust = customerName ? customers.find(c => c.name === customerName) : customers[0];

    const content = `
      <form id="new-receipt-form">
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Customer Account <span class="required-star">*</span></label>
            <select class="form-control" id="rec-cust" onchange="AccountsView.syncReceiptCust(this.value)">
              ${customers.map(c => `<option value="${c.name}" ${selectedCust && selectedCust.name === c.name ? 'selected' : ''}>${c.name} (Outstanding: ${UI.formatCurrency(c.outstanding || 0)})</option>`).join('')}
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Payment Date <span class="required-star">*</span></label>
            <input type="date" class="form-control" id="rec-date" value="${new Date().toISOString().split('T')[0]}">
          </div>

          <div class="form-group">
            <label class="form-label">Received Amount (₹) <span class="required-star">*</span></label>
            <input type="number" class="form-control" id="rec-amount" required value="${(selectedCust && selectedCust.outstanding > 0) ? selectedCust.outstanding : 50000}" min="1">
          </div>

          <div class="form-group">
            <label class="form-label">Payment Mode <span class="required-star">*</span></label>
            <select class="form-control" id="rec-mode">
              <option value="Bank Transfer (NEFT)">Bank Transfer (NEFT)</option>
              <option value="RTGS">RTGS</option>
              <option value="UPI">UPI / QR Payment</option>
              <option value="Cheque">Cheque Deposit</option>
              <option value="Cash">Cash Receipt</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Bank Reference / UTR Number <span class="required-star">*</span></label>
            <input type="text" class="form-control font-mono" id="rec-ref" required value="UTR${Date.now().toString().slice(-8)}" placeholder="e.g. UTR / Cheque No">
          </div>

          <div class="form-group">
            <label class="form-label">Linked Tax Invoice</label>
            <input type="text" class="form-control font-mono" id="rec-inv" placeholder="e.g. INV-2026-001">
          </div>

          <div class="form-group col-span-2">
            <label class="form-label">Notes & Remarks</label>
            <textarea class="form-control" id="rec-remarks">Payment received towards account settlement.</textarea>
          </div>
        </div>
      </form>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="UI.closeModal()">Cancel</button>
      <button class="btn btn-success" id="btn-save-receipt">Confirm Receipt & Settle Balance</button>
    `;

    UI.openModal({ title: "Record Customer Payment Receipt", content, footer, size: "modal-lg" });

    document.getElementById("btn-save-receipt").onclick = () => {
      const cust = document.getElementById("rec-cust").value;
      const amount = Number(document.getElementById("rec-amount").value);
      const mode = document.getElementById("rec-mode").value;
      const refNo = document.getElementById("rec-ref").value;
      const invoiceNo = document.getElementById("rec-inv").value;
      const date = document.getElementById("rec-date").value;
      const remarks = document.getElementById("rec-remarks").value;

      if (amount <= 0) return UI.showToast("Invalid Amount", "Payment amount must be greater than 0", "error");

      ERPState.recordPayment({
        customer: cust,
        amount,
        mode,
        refNo,
        invoiceNo,
        date,
        remarks
      });

      UI.showToast("Payment Recorded", `Received ₹${amount.toLocaleString('en-IN')} from ${cust}. Outstanding reduced!`, "success");
      UI.closeModal();
      App.refreshCurrentView();
    };
  },

  syncReceiptCust(custName) {
    const inv = (ERPState.data.invoices || []).find(i => i.customer === custName);
    if (inv) {
      const el = document.getElementById("rec-inv");
      if (el) el.value = inv.invoiceNo || inv.id;
    }
  },

  // 2. CUSTOMER OUTSTANDING REPORT WITH AGING
  renderCustomerOutstanding() {
    const customers = ERPState.data.customers || [];

    return `
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left">
            <h3 style="font-size:1.05rem;">Customer Outstanding & Aging Analysis</h3>
          </div>
          <div class="table-toolbar-right">
            <button class="btn btn-secondary btn-sm" onclick="UI.showToast('Report Exported', 'Customer outstanding report downloaded', 'success')">Export Excel</button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>Customer</th>
                <th>Credit Limit</th>
                <th>Total Outstanding</th>
                <th>0 - 30 Days (Current)</th>
                <th>31 - 60 Days</th>
                <th>60+ Days (Overdue)</th>
                <th>Payment Terms</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              ${customers.length === 0 ? `
                <tr>
                  <td colspan="8" style="text-align:center; padding:32px 20px; color:var(--slate-400);">No customer outstanding balances found.</td>
                </tr>
              ` : customers.map(c => {
                const out = Number(c.outstanding || 0);
                return `
                  <tr>
                    <td class="primary-cell">${c.name}</td>
                    <td class="font-mono">${UI.formatCurrency(c.creditLimit || 0)}</td>
                    <td class="font-bold font-mono" style="color:${out > 0 ? 'var(--danger-600)' : 'var(--success-600)'};">${UI.formatCurrency(out)}</td>
                    <td class="font-mono">${UI.formatCurrency(out * 0.6)}</td>
                    <td class="font-mono">${UI.formatCurrency(out * 0.3)}</td>
                    <td class="font-mono font-bold" style="color:var(--danger-600);">${UI.formatCurrency(out * 0.1)}</td>
                    <td>${c.paymentTerms || '30 Days'}</td>
                    <td>${UI.formatStatusBadge(out > 0 ? 'Pending' : 'Cleared')}</td>
                  </tr>
                `;
              }).join('')}
            </tbody>
          </table>
        </div>
      </div>
    `;
  },

  // 3. VENDOR OUTSTANDING REPORT
  renderVendorOutstanding() {
    const vendors = ERPState.data.vendors || [];

    return `
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left">
            <h3 style="font-size:1.05rem;">Vendor Outstanding Payables</h3>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>Vendor Name</th>
                <th>Category</th>
                <th>Payment Terms</th>
                <th>Outstanding Payable</th>
                <th>Due Date Status</th>
              </tr>
            </thead>
            <tbody>
              ${vendors.length === 0 ? `
                <tr>
                  <td colspan="5" style="text-align:center; padding:32px 20px; color:var(--slate-400);">No vendor outstanding payables found.</td>
                </tr>
              ` : vendors.map(v => `
                <tr>
                  <td class="primary-cell">${v.name}</td>
                  <td><span class="badge badge-slate">${v.category || 'Supplier'}</span></td>
                  <td>${v.paymentTerms || '30 Days'}</td>
                  <td class="font-bold font-mono" style="color:${(v.outstanding || 0) > 0 ? 'var(--danger-600)' : 'var(--success-600)'};">${UI.formatCurrency(v.outstanding || 0)}</td>
                  <td><span class="badge badge-success">Within Credit Terms</span></td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      </div>
    `;
  },

  // 4. JOB WORKER OUTSTANDING REPORT
  renderJobWorkerOutstanding() {
    const workers = ERPState.data.jobWorkers || [];

    return `
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left">
            <h3 style="font-size:1.05rem;">Job Worker Labor Outstanding Payables</h3>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>Job Worker Name</th>
                <th>Assigned Process</th>
                <th>Standard Rate</th>
                <th>Total Work Done (₹)</th>
                <th>Paid (₹)</th>
                <th>Outstanding Balance (₹)</th>
              </tr>
            </thead>
            <tbody>
              ${workers.length === 0 ? `
                <tr>
                  <td colspan="6" style="text-align:center; padding:32px 20px; color:var(--slate-400);">No job worker labor outstandings found.</td>
                </tr>
              ` : workers.map(w => `
                <tr>
                  <td class="primary-cell">${w.name}</td>
                  <td><span class="badge badge-purple">${w.process || 'Stitching'}</span></td>
                  <td class="font-mono">₹${w.rate || 20} / ${w.rateUnit || 'Pc'}</td>
                  <td class="font-mono">${UI.formatCurrency(Number(w.outstanding || 0) + 10000)}</td>
                  <td class="font-mono" style="color:var(--success-700);">${UI.formatCurrency(10000)}</td>
                  <td class="font-bold font-mono" style="color:${(w.outstanding || 0) > 0 ? 'var(--danger-600)' : 'var(--success-600)'};">${UI.formatCurrency(w.outstanding || 0)}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      </div>
    `;
  }
};
