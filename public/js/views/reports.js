/* ==========================================================================
   REPORTS & LEDGERS MODULE - ITEM LEDGER, LOT-WISE REPORTS & STOCK SUMMARY
   GarmentERP
   ========================================================================== */

const ReportsView = {
  // 1. CENTRAL REPORTS DASHBOARD
  renderDashboard() {
    const reportList = [
      { id: "ledger", title: "Item Stock Ledger", desc: "Complete chronological running balance of inventory transactions", icon: "book-open", category: "Inventory" },
      { id: "stock", title: "Stock & Inventory Report", desc: "Warehouse-wise stock levels, valuation and reorder alerts", icon: "box", category: "Inventory" },
      { id: "lot-purchase", title: "Lot-Wise Purchase Report", desc: "Trace raw materials, vendor invoice lots and yields", icon: "layers", category: "Purchase" },
      { id: "lot-sales", title: "Lot-Wise Sales Report", desc: "Finished garment lot deliveries, invoices and margins", icon: "trending-up", category: "Sales" },
      { id: "jobwork", title: "Job Work Efficiency Report", desc: "Worker turnaround times, rejection rates and labor cost", icon: "scissors", category: "Production" },
      { id: "dispatch", title: "Dispatch & Logistics Report", desc: "Transporter tracking, vehicle numbers and delivery status", icon: "truck", category: "Dispatch" },
      { id: "cust-out", title: "Customer Outstanding Report", desc: "Accounts receivable aging and overdue credit analysis", icon: "dollar-sign", category: "Accounts" },
      { id: "vend-out", title: "Vendor Outstanding Report", desc: "Fabric and trim supplier payables and due dates", icon: "credit-card", category: "Accounts" },
      { id: "jw-out", title: "Job Worker Outstanding", desc: "Contractor piece-rate labor ledger and settlement status", icon: "users", category: "Accounts" }
    ];

    return `
      <div style="margin-bottom:24px;">
        <h2 style="font-size:1.35rem; color:var(--slate-900);">Central Management Reports Hub</h2>
        <p style="color:var(--slate-500); font-size:0.875rem;">Access audit-ready statutory reports, lot traceability, and financial ledgers</p>
      </div>

      <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:18px;">
        ${reportList.map(r => `
          <div class="card" style="cursor:pointer; transition:transform 0.15s ease;" onclick="ReportsView.openReport('${r.id}')">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
              <span class="badge badge-primary">${r.category}</span>
              <div class="kpi-icon-wrap blue">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
              </div>
            </div>
            <h4 style="font-size:1rem; color:var(--slate-900); margin-bottom:4px;">${r.title}</h4>
            <p style="font-size:0.8rem; color:var(--slate-500); line-height:1.4;">${r.desc}</p>
            <div style="margin-top:16px; font-size:0.8rem; font-weight:700; color:var(--primary-600); display:flex; align-items:center; gap:4px;">
              View Report →
            </div>
          </div>
        `).join('')}
      </div>
    `;
  },

  openReport(reportId) {
    if (reportId === "ledger") App.navigate("reports", "ledger");
    else if (reportId === "stock") App.navigate("reports", "stock");
    else if (reportId === "lot-purchase") App.navigate("reports", "lot-purchase");
    else if (reportId === "lot-sales") App.navigate("reports", "lot-sales");
    else if (reportId === "cust-out") App.navigate("accounts", "customer-outstanding");
    else if (reportId === "vend-out") App.navigate("accounts", "vendor-outstanding");
    else if (reportId === "jw-out") App.navigate("accounts", "jobworker-outstanding");
    else if (reportId === "dispatch") App.navigate("dispatch", "dispatch");
    else App.navigate("reports", "ledger");
  },

  // 2. ITEM STOCK LEDGER (RUNNING BALANCE)
  renderItemLedger() {
    const ledger = ERPState.data.itemLedger || [];

    return `
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left">
            <div class="table-search-box">
              <svg class="table-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              <input type="text" class="table-search-input" placeholder="Search by item, transaction type, ref..." oninput="MastersView.filterGenericTable('ledger-table', this.value)">
            </div>
          </div>

          <div class="table-toolbar-right">
            <button class="btn btn-secondary btn-sm" onclick="ReportsView.exportLedger()">Export CSV</button>
            <button class="btn btn-primary btn-sm" onclick="window.print(); UI.showToast('Printing Ledger', 'Sent to printer', 'info');">Print Ledger</button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table" id="ledger-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Reference No</th>
                <th>Item / SKU</th>
                <th>Transaction Type</th>
                <th>Lot Ref</th>
                <th style="color:var(--success-700);">Inward (+)</th>
                <th style="color:var(--danger-700);">Outward (-)</th>
                <th>Running Balance</th>
                <th>Logged By</th>
              </tr>
            </thead>
            <tbody>
              ${ledger.length === 0 ? `
                <tr>
                  <td colspan="9" style="text-align:center; padding:32px 20px; color:var(--slate-400);">No stock ledger entries recorded yet.</td>
                </tr>
              ` : ledger.map(l => `
                <tr>
                  <td>${UI.formatDate(l.date)}</td>
                  <td class="mono-cell font-bold">${l.ref}</td>
                  <td class="primary-cell">${l.item}</td>
                  <td><span class="badge ${l.inward > 0 ? 'badge-success' : 'badge-danger'}">${l.type}</span></td>
                  <td class="mono-cell">${l.lot || '-'}</td>
                  <td class="font-bold font-mono" style="color:var(--success-600);">${l.inward > 0 ? '+' + Number(l.inward).toLocaleString('en-IN') : '-'}</td>
                  <td class="font-bold font-mono" style="color:var(--danger-600);">${l.outward > 0 ? '-' + Number(l.outward).toLocaleString('en-IN') : '-'}</td>
                  <td class="font-bold font-mono" style="color:var(--primary-800);">${Number(l.balance).toLocaleString('en-IN')}</td>
                  <td>${l.user || 'System'}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      </div>
    `;
  },

  exportLedger() {
    const headers = ["Date", "Reference", "Item", "Transaction Type", "Lot", "Inward", "Outward", "Balance", "User"];
    const rows = (ERPState.data.itemLedger || []).map(l => [l.date, l.ref, l.item, l.type, l.lot, l.inward, l.outward, l.balance, l.user]);
    UI.exportToCSV("Item_Stock_Ledger", headers, rows);
  },

  // 3. STOCK REPORT
  renderStockReport() {
    const items = ERPState.data.items || [];

    return `
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left">
            <h3 style="font-size:1.05rem;">Complete Warehouse Stock Position & Valuation</h3>
          </div>
          <div class="table-toolbar-right">
            <button class="btn btn-secondary btn-sm" onclick="MastersView.exportItems()">Export Stock</button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>Item Code</th>
                <th>Item Description</th>
                <th>Type</th>
                <th>Unit</th>
                <th>Current Stock</th>
                <th>Reorder Level</th>
                <th>Unit Cost (₹)</th>
                <th>Stock Valuation (₹)</th>
              </tr>
            </thead>
            <tbody>
              ${items.length === 0 ? `
                <tr>
                  <td colspan="8" style="text-align:center; padding:32px 20px; color:var(--slate-400);">No inventory items recorded in catalog.</td>
                </tr>
              ` : items.map(i => {
                const stock = Number(i.currentStock || 0);
                const rate = Number(i.rate || i.unitCost || 0);
                const val = stock * rate;
                return `
                  <tr>
                    <td class="mono-cell font-bold">${i.code || i.id}</td>
                    <td class="primary-cell">${i.name}</td>
                    <td><span class="badge badge-primary">${i.type || 'Raw Material'}</span></td>
                    <td>${i.unit || 'Meters'}</td>
                    <td class="font-bold font-mono" style="color:${stock <= (i.reorderLevel || 100) ? 'var(--danger-600)' : 'var(--slate-900)'};">
                      ${stock.toLocaleString('en-IN')}
                    </td>
                    <td class="font-mono text-muted">${(i.reorderLevel || 100).toLocaleString('en-IN')}</td>
                    <td class="font-mono">₹${rate.toLocaleString('en-IN')}</td>
                    <td class="font-bold font-mono" style="color:#059669;">${UI.formatCurrency(val)}</td>
                  </tr>
                `;
              }).join('')}
            </tbody>
          </table>
        </div>
      </div>
    `;
  },

  // 4. LOT-WISE PURCHASE REPORT
  renderLotPurchase() {
    const inwards = ERPState.data.purchaseInwards || [];

    return `
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left">
            <h3 style="font-size:1.05rem;">Lot-Wise Purchase Traceability Report</h3>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>Lot Number</th>
                <th>Material / Fabric</th>
                <th>Vendor</th>
                <th>Purchase Date</th>
                <th>Purchased Qty</th>
                <th>Warehouse</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              ${inwards.length === 0 ? `
                <tr>
                  <td colspan="7" style="text-align:center; padding:32px 20px; color:var(--slate-400);">No lot purchase records found.</td>
                </tr>
              ` : inwards.map(inw => `
                <tr>
                  <td class="mono-cell font-bold" style="color:var(--primary-600);">${inw.lotNumber || inw.id}</td>
                  <td class="primary-cell">${inw.item}</td>
                  <td>${inw.vendor}</td>
                  <td>${UI.formatDate(inw.receivedDate)}</td>
                  <td class="font-mono">${Number(inw.acceptedQty || inw.receivedQty || 0).toLocaleString('en-IN')}</td>
                  <td>${inw.warehouse || 'Main Raw Material Store'}</td>
                  <td>${UI.formatStatusBadge(inw.status || 'Approved')}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      </div>
    `;
  },

  // 5. LOT-WISE SALES REPORT
  renderLotSales() {
    const invoices = ERPState.data.invoices || [];

    return `
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left">
            <h3 style="font-size:1.05rem;">Lot-Wise Sales Fulfillment Report</h3>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>Invoice / Lot</th>
                <th>Customer</th>
                <th>Sales Order Ref</th>
                <th>Invoice Date</th>
                <th>Invoice Amount</th>
                <th>Paid Amount</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              ${invoices.length === 0 ? `
                <tr>
                  <td colspan="7" style="text-align:center; padding:32px 20px; color:var(--slate-400);">No sales delivery fulfillment records found.</td>
                </tr>
              ` : invoices.map(inv => `
                <tr>
                  <td class="mono-cell font-bold" style="color:var(--primary-600);">${inv.invoiceNo || inv.id}</td>
                  <td class="primary-cell">${inv.customer}</td>
                  <td class="mono-cell">${inv.orderNo || '-'}</td>
                  <td>${UI.formatDate(inv.invoiceDate || inv.date)}</td>
                  <td class="font-bold font-mono">${UI.formatCurrency(inv.amount || inv.grandTotal || 0)}</td>
                  <td class="font-bold font-mono" style="color:var(--success-700);">${UI.formatCurrency(inv.paidAmount || 0)}</td>
                  <td>${UI.formatStatusBadge(inv.status || 'Sent')}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      </div>
    `;
  }
};
