/* ==========================================================================
   MASTERS MANAGEMENT VIEW - CUSTOMERS, VENDORS, JOB WORKERS, ITEMS & SIZES
   GarmentERP
   ========================================================================== */

const MastersView = {
  // 1. CUSTOMER MASTER STATE & CONTROLLER
  _custState: {
    search: "",
    status: "",
    city: "",
    balanceFilter: "",
    sortBy: "name_asc",
    page: 1,
    pageSize: 10,
    activeDrawerTab: "overview",
    activeCustomerId: null
  },

  renderCustomers() {
    const allCustomers = ERPState.data.customers || [];
    const stats = ERPState.getCustomerStats();

    // 1. Filter customers
    let filtered = allCustomers.filter(c => {
      const q = (this._custState.search || "").toLowerCase().trim();
      if (q) {
        const matchName = (c.name || "").toLowerCase().includes(q);
        const matchComp = (c.companyName || "").toLowerCase().includes(q);
        const matchCode = (c.id || c.code || "").toLowerCase().includes(q);
        const matchPhone = (c.mobile || c.phone || "").toLowerCase().includes(q);
        const matchEmail = (c.email || "").toLowerCase().includes(q);
        const matchGst = (c.gstin || "").toLowerCase().includes(q);
        const matchCity = (c.city || "").toLowerCase().includes(q);
        const matchContact = (c.contactPerson || "").toLowerCase().includes(q);
        if (!matchName && !matchComp && !matchCode && !matchPhone && !matchEmail && !matchGst && !matchCity && !matchContact) {
          return false;
        }
      }

      if (this._custState.status && c.status !== this._custState.status) {
        return false;
      }

      if (this._custState.city && c.city !== this._custState.city) {
        return false;
      }

      if (this._custState.balanceFilter === "due" && !(Number(c.outstanding) > 0)) {
        return false;
      }
      if (this._custState.balanceFilter === "zero" && Number(c.outstanding) > 0) {
        return false;
      }
      if (this._custState.balanceFilter === "risk" && !((Number(c.outstanding) || 0) >= (Number(c.creditLimit) || 0) * 0.9 && (Number(c.outstanding) || 0) > 0)) {
        return false;
      }

      return true;
    });

    // 2. Sort customers
    filtered.sort((a, b) => {
      switch (this._custState.sortBy) {
        case "name_asc":
          return (a.name || "").localeCompare(b.name || "");
        case "name_desc":
          return (b.name || "").localeCompare(a.name || "");
        case "due_desc":
          return (Number(b.outstanding) || 0) - (Number(a.outstanding) || 0);
        case "due_asc":
          return (Number(a.outstanding) || 0) - (Number(b.outstanding) || 0);
        case "credit_desc":
          return (Number(b.creditLimit) || 0) - (Number(a.creditLimit) || 0);
        case "recent":
          return (b.id || "").localeCompare(a.id || "");
        default:
          return (a.name || "").localeCompare(b.name || "");
      }
    });

    // 3. Paginate
    const totalRecords = filtered.length;
    const pageSize = this._custState.pageSize === "all" ? totalRecords : Number(this._custState.pageSize || 10);
    const totalPages = Math.max(1, Math.ceil(totalRecords / (pageSize || 1)));
    
    if (this._custState.page > totalPages) this._custState.page = totalPages;
    if (this._custState.page < 1) this._custState.page = 1;
    
    const startIndex = (this._custState.page - 1) * pageSize;
    const paginated = pageSize === totalRecords ? filtered : filtered.slice(startIndex, startIndex + pageSize);

    return `
      <div style="display:flex; flex-direction:column; gap:20px;">
        
        <!-- Top KPI Dynamic Metrics Row -->
        <div class="kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
          
          <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
            <div style="width:46px; height:46px; border-radius:12px; background:var(--primary-50); color:var(--primary-600); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
              <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Total Customers</div>
              <div style="font-size:1.45rem; font-weight:800; color:var(--slate-900); margin-top:2px;">${stats.total} <span style="font-size:0.8rem; font-weight:600; color:var(--success-600); background:#ecfdf5; padding:2px 8px; border-radius:12px; border:1px solid #a7f3d0;">${stats.active} Active</span></div>
            </div>
          </div>

          <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
            <div style="width:46px; height:46px; border-radius:12px; background:#fef2f2; color:var(--danger-600); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div>
              <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Total Outstanding</div>
              <div style="font-size:1.45rem; font-weight:800; color:var(--danger-600); margin-top:2px;">${UI.formatCurrency(stats.totalOutstanding)}</div>
            </div>
          </div>

          <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
            <div style="width:46px; height:46px; border-radius:12px; background:#f0fdf4; color:var(--success-600); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
            </div>
            <div>
              <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Approved Credit Limit</div>
              <div style="font-size:1.45rem; font-weight:800; color:var(--slate-800); margin-top:2px;">${UI.formatCurrency(stats.totalCreditLimit)}</div>
            </div>
          </div>

          <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
            <div style="width:46px; height:46px; border-radius:12px; background:${stats.highRisk > 0 ? '#fffbeb' : '#f8fafc'}; color:${stats.highRisk > 0 ? '#d97706' : '#64748b'}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/></svg>
            </div>
            <div>
              <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Credit Utilization</div>
              <div style="font-size:1.45rem; font-weight:800; color:${Number(stats.creditUtilization) > 75 ? 'var(--danger-600)' : 'var(--primary-700)'}; margin-top:2px;">
                ${stats.creditUtilization}%
                ${stats.highRisk > 0 ? `<span style="font-size:0.75rem; font-weight:700; color:#b45309; background:#fef3c7; padding:2px 6px; border-radius:10px; margin-left:4px;">${stats.highRisk} High Risk</span>` : ''}
              </div>
            </div>
          </div>

        </div>

        <!-- Main Table & Filter Container -->
        <div class="table-card">
          
          <!-- Advanced Multi-Filter Toolbar -->
          <div class="table-toolbar" style="gap:12px; flex-wrap:wrap;">
            <div class="table-toolbar-left" style="display:flex; gap:10px; flex-wrap:wrap; flex:1; min-width:320px;">
              
              <!-- Search Box -->
              <div class="table-search-box" style="min-width:240px; flex:1.2;">
                <svg class="table-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" class="table-search-input" id="cust-search" value="${this._custState.search}" placeholder="Search name, phone, GSTIN, city..." oninput="MastersView.onCustomerSearch(this.value)">
                ${this._custState.search ? `
                  <button onclick="MastersView.onCustomerSearch(''); document.getElementById('cust-search').value='';" style="position:absolute; right:8px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:var(--slate-400);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                  </button>
                ` : ''}
              </div>

              <!-- Status Filter -->
              <select class="table-filter-select" onchange="MastersView.onCustomerFilter('status', this.value)" style="min-width:125px;">
                <option value="">All Statuses</option>
                <option value="Active" ${this._custState.status === 'Active' ? 'selected' : ''}>Active</option>
                <option value="Inactive" ${this._custState.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
                <option value="Blocked" ${this._custState.status === 'Blocked' ? 'selected' : ''}>Blocked</option>
              </select>

              <!-- City Filter -->
              <select class="table-filter-select" onchange="MastersView.onCustomerFilter('city', this.value)" style="min-width:125px;">
                <option value="">All Cities</option>
                ${(stats.cities || []).map(ct => `<option value="${ct}" ${this._custState.city === ct ? 'selected' : ''}>${ct}</option>`).join('')}
              </select>

              <!-- Balance Filter -->
              <select class="table-filter-select" onchange="MastersView.onCustomerFilter('balanceFilter', this.value)" style="min-width:140px;">
                <option value="">All Balances</option>
                <option value="due" ${this._custState.balanceFilter === 'due' ? 'selected' : ''}>Payment Due (>0)</option>
                <option value="zero" ${this._custState.balanceFilter === 'zero' ? 'selected' : ''}>Zero Balance (₹0)</option>
                <option value="risk" ${this._custState.balanceFilter === 'risk' ? 'selected' : ''}>High Risk (>90%)</option>
              </select>

              <!-- Sort Order -->
              <select class="table-filter-select" onchange="MastersView.onCustomerFilter('sortBy', this.value)" style="min-width:140px;">
                <option value="name_asc" ${this._custState.sortBy === 'name_asc' ? 'selected' : ''}>Name (A → Z)</option>
                <option value="name_desc" ${this._custState.sortBy === 'name_desc' ? 'selected' : ''}>Name (Z → A)</option>
                <option value="due_desc" ${this._custState.sortBy === 'due_desc' ? 'selected' : ''}>Highest Balance</option>
                <option value="due_asc" ${this._custState.sortBy === 'due_asc' ? 'selected' : ''}>Lowest Balance</option>
                <option value="credit_desc" ${this._custState.sortBy === 'credit_desc' ? 'selected' : ''}>Credit Limit</option>
                <option value="recent" ${this._custState.sortBy === 'recent' ? 'selected' : ''}>Newest First</option>
              </select>

            </div>

            <div class="table-toolbar-right" style="display:flex; gap:8px;">
              <button class="btn btn-secondary btn-sm" onclick="MastersView.exportCustomers()" title="Export CSV file">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export CSV
              </button>
              
              <button class="btn btn-secondary btn-sm" onclick="MastersView.printCustomerDirectory()" title="Print Customer List">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                Print
              </button>

              <button class="btn btn-primary btn-sm" onclick="MastersView.openCustomerModal()">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Customer
              </button>
            </div>
          </div>

          <!-- Responsive Data Table -->
          <div class="table-responsive">
            <table class="data-table" id="customers-table">
              <thead>
                <tr>
                  <th style="width:110px;">Customer ID</th>
                  <th>Customer / Entity</th>
                  <th>Contact Person & Phone</th>
                  <th>GSTIN</th>
                  <th>City & State</th>
                  <th>Credit Limit</th>
                  <th>Outstanding Balance</th>
                  <th>Status</th>
                  <th style="text-align:right; min-width:210px;">Actions</th>
                </tr>
              </thead>
              <tbody>
                ${paginated.length > 0 ? paginated.map(c => {
                  const outAmt = Number(c.outstanding) || 0;
                  const credLim = Number(c.creditLimit) || 1;
                  const utilPercent = Math.min(100, Math.round((outAmt / credLim) * 100));
                  const isHighRisk = outAmt >= credLim * 0.9 && outAmt > 0;
                  const initials = (c.name || "CU").split(" ").map(w => w[0]).slice(0, 2).join("").toUpperCase();

                  return `
                    <tr id="cust-row-${c.id}" class="${isHighRisk ? 'row-highlight-warning' : ''}">
                      <td class="mono-cell font-bold" style="color:var(--primary-600);">
                        <span style="background:var(--primary-50); padding:3px 7px; border-radius:6px; border:1px solid var(--primary-100);">${c.id || c.code}</span>
                      </td>
                      
                      <td class="primary-cell">
                        <div style="display:flex; align-items:center; gap:10px;">
                          <div style="width:34px; height:34px; border-radius:8px; background:linear-gradient(135deg, var(--primary-600), var(--primary-800)); color:#ffffff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.75rem; flex-shrink:0;">
                            ${initials}
                          </div>
                          <div>
                            <a href="javascript:void(0)" onclick="MastersView.openCustomerDrawer('${c.id}')" style="font-weight:700; color:var(--slate-900); text-decoration:none;" onmouseover="this.style.color='var(--primary-600)'" onmouseout="this.style.color='var(--slate-900)'">
                              ${c.name}
                            </a>
                            <div style="font-size:0.725rem; color:var(--slate-500); margin-top:1px;">${c.companyName || c.name}</div>
                          </div>
                        </div>
                      </td>

                      <td>
                        <div style="font-weight:600; color:var(--slate-800); font-size:0.825rem;">${c.contactPerson || '-'}</div>
                        <div style="font-size:0.75rem; color:var(--slate-500); margin-top:2px;">
                          <a href="tel:${c.mobile || c.phone}" style="color:inherit; text-decoration:none;" title="Click to call">
                            ${c.mobile || c.phone || '-'}
                          </a>
                        </div>
                      </td>

                      <td class="mono-cell">
                        ${c.gstin ? `
                          <div style="display:inline-flex; align-items:center; gap:4px; background:var(--slate-100); padding:2px 6px; border-radius:4px; font-size:0.775rem;">
                            <span>${c.gstin}</span>
                            <button onclick="navigator.clipboard.writeText('${c.gstin}'); UI.showToast('Copied', 'GSTIN copied to clipboard', 'info');" style="background:none; border:none; cursor:pointer; padding:0; color:var(--slate-400);" title="Copy GSTIN">
                              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                            </button>
                          </div>
                        ` : `<span style="color:var(--slate-400); font-size:0.75rem;">Unregistered</span>`}
                      </td>

                      <td>
                        <div style="font-size:0.825rem; font-weight:600; color:var(--slate-800);">${c.city || 'Mumbai'}</div>
                        <div style="font-size:0.725rem; color:var(--slate-400);">${c.state || 'Maharashtra'}</div>
                      </td>

                      <td>
                        <div style="font-weight:700; color:var(--slate-800); font-size:0.825rem;">${UI.formatCurrency(credLim)}</div>
                        <div style="font-size:0.7rem; color:var(--slate-500);">${c.paymentTerms || '30 Days Net'}</div>
                      </td>

                      <td>
                        <div style="display:flex; align-items:baseline; justify-content:space-between; gap:6px;">
                          <span style="font-size:0.875rem; font-weight:800; color:${outAmt > 0 ? (isHighRisk ? 'var(--danger-700)' : 'var(--danger-600)') : 'var(--success-600)'};">
                            ${UI.formatCurrency(outAmt)}
                          </span>
                          ${outAmt > 0 ? `<span style="font-size:0.7rem; color:var(--slate-500);">${utilPercent}%</span>` : ''}
                        </div>
                        ${outAmt > 0 ? `
                          <div style="width:100%; height:4px; background:var(--slate-200); border-radius:2px; margin-top:4px; overflow:hidden;">
                            <div style="width:${utilPercent}%; height:100%; background:${isHighRisk ? 'var(--danger-500)' : (utilPercent > 50 ? '#f59e0b' : 'var(--primary-500)')}; border-radius:2px;"></div>
                          </div>
                        ` : ''}
                      </td>

                      <td>
                        ${UI.formatStatusBadge(c.status || 'Active')}
                      </td>

                      <td class="table-actions" style="text-align:right;">
                        <button class="table-action-btn view" title="View Profile & History" onclick="MastersView.openCustomerDrawer('${c.id}')">View</button>
                        <button class="table-action-btn ledger" title="View Statement Ledger" onclick="MastersView.openCustomerStatementModal('${c.id}')">
                          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                          Ledger
                        </button>
                        <button class="table-action-btn receipt" title="Record Receipt" onclick="MastersView.openCustomerReceiptModal('${c.id}')">
                          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                          Receipt
                        </button>
                        <button class="table-action-btn edit" title="Edit Customer" onclick="MastersView.openCustomerModal('${c.id}')">Edit</button>
                        <button class="table-action-btn delete" title="Delete Customer" onclick="MastersView.confirmDeleteCustomer('${c.id}')">
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                        </button>
                      </td>
                    </tr>
                  `;
                }).join('') : `
                  <tr>
                    <td colspan="9" style="text-align:center; padding:40px 20px;">
                      <div style="width:50px; height:50px; border-radius:50%; background:var(--slate-100); color:var(--slate-400); display:flex; align-items:center; justify-content:center; margin:0 auto 12px;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                      </div>
                      <h4 style="color:var(--slate-800); font-size:0.95rem; margin-bottom:4px;">No matching customers found</h4>
                      <p style="color:var(--slate-500); font-size:0.8rem; margin-bottom:16px;">Try adjusting your search query, status, city or balance filter.</p>
                      <button class="btn btn-secondary btn-sm" onclick="MastersView.resetCustomerFilters()">Reset All Filters</button>
                    </td>
                  </tr>
                `}
              </tbody>
            </table>
          </div>

          <!-- Dynamic Pagination Controls -->
          <div class="table-pagination" style="padding:14px 20px; border-top:1px solid var(--slate-200); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
            <div class="pagination-info" style="font-size:0.825rem; color:var(--slate-600);">
              Showing <strong>${totalRecords > 0 ? startIndex + 1 : 0}</strong> to <strong>${Math.min(startIndex + pageSize, totalRecords)}</strong> of <strong>${totalRecords}</strong> customer accounts
            </div>

            <div style="display:flex; align-items:center; gap:12px;">
              <div style="display:flex; align-items:center; gap:6px; font-size:0.8rem; color:var(--slate-500);">
                <span>Per Page:</span>
                <select class="table-filter-select" style="padding:4px 24px 4px 8px; font-size:0.8rem;" onchange="MastersView.onCustomerPageSize(this.value)">
                  <option value="5" ${this._custState.pageSize === 5 || this._custState.pageSize === '5' ? 'selected' : ''}>5</option>
                  <option value="10" ${this._custState.pageSize === 10 || this._custState.pageSize === '10' ? 'selected' : ''}>10</option>
                  <option value="25" ${this._custState.pageSize === 25 || this._custState.pageSize === '25' ? 'selected' : ''}>25</option>
                  <option value="50" ${this._custState.pageSize === 50 || this._custState.pageSize === '50' ? 'selected' : ''}>50</option>
                  <option value="all" ${this._custState.pageSize === 'all' ? 'selected' : ''}>All</option>
                </select>
              </div>

              <div class="pagination-controls" style="display:flex; gap:4px;">
                <button class="page-btn" ${this._custState.page <= 1 ? 'disabled' : ''} onclick="MastersView.onCustomerPageChange(${this._custState.page - 1})">Previous</button>
                
                ${Array.from({ length: totalPages }, (_, i) => i + 1).map(p => {
                  if (totalPages > 6 && Math.abs(p - this._custState.page) > 2 && p !== 1 && p !== totalPages) {
                    return p === 2 || p === totalPages - 1 ? `<span style="padding:4px 6px; color:var(--slate-400);">...</span>` : '';
                  }
                  return `<button class="page-btn ${this._custState.page === p ? 'active' : ''}" onclick="MastersView.onCustomerPageChange(${p})">${p}</button>`;
                }).join('')}
                
                <button class="page-btn" ${this._custState.page >= totalPages ? 'disabled' : ''} onclick="MastersView.onCustomerPageChange(${this._custState.page + 1})">Next</button>
              </div>
            </div>
          </div>

        </div>
      </div>
    `;
  },

  onCustomerSearch(val) {
    this._custState.search = val;
    this._custState.page = 1;
    App.refreshCurrentView();
  },

  onCustomerFilter(key, val) {
    this._custState[key] = val;
    this._custState.page = 1;
    App.refreshCurrentView();
  },

  onCustomerPageChange(page) {
    this._custState.page = page;
    App.refreshCurrentView();
  },

  onCustomerPageSize(size) {
    this._custState.pageSize = size;
    this._custState.page = 1;
    App.refreshCurrentView();
  },

  resetCustomerFilters() {
    this._custState.search = "";
    this._custState.status = "";
    this._custState.city = "";
    this._custState.balanceFilter = "";
    this._custState.sortBy = "name_asc";
    this._custState.page = 1;
    App.refreshCurrentView();
  },

  openCustomerModal(customerId = null) {
    const isEdit = !!customerId;
    const cust = isEdit ? ERPState.getCustomerById(customerId) || {} : {};

    const content = `
      <form id="customer-form" onsubmit="event.preventDefault(); document.getElementById('btn-save-cust').click();">
        <div style="background:var(--slate-50); padding:12px 16px; border-radius:var(--radius-md); margin-bottom:16px; border:1px solid var(--slate-200); font-size:0.825rem; color:var(--slate-600); display:flex; align-items:center; gap:8px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--primary-600); flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
          <span>${isEdit ? `Editing Master Record for <strong>${cust.name}</strong> (${cust.id})` : 'New Customer account will be auto-assigned a unique code and added to live ERP Database.'}</span>
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Customer / Brand Display Name <span class="required-star">*</span></label>
            <input type="text" class="form-control" id="cust-name" required value="${cust.name || ''}" placeholder="e.g. Vogue Fashions Mumbai">
          </div>

          <div class="form-group">
            <label class="form-label">Registered Legal Entity Name</label>
            <input type="text" class="form-control" id="cust-company" value="${cust.companyName || ''}" placeholder="e.g. Vogue Retail Ltd.">
          </div>

          <div class="form-group">
            <label class="form-label">Primary Contact Person <span class="required-star">*</span></label>
            <input type="text" class="form-control" id="cust-contact" required value="${cust.contactPerson || ''}" placeholder="e.g. Rajesh Sharma">
          </div>

          <div class="form-group">
            <label class="form-label">Mobile Number <span class="required-star">*</span></label>
            <input type="tel" class="form-control font-mono" id="cust-mobile" required value="${cust.mobile || cust.phone || ''}" placeholder="+91 98201 54321">
          </div>

          <div class="form-group">
            <label class="form-label">Email Address <span class="required-star">*</span></label>
            <input type="email" class="form-control" id="cust-email" required value="${cust.email || ''}" placeholder="rajesh@voguefashions.in">
          </div>

          <div class="form-group">
            <label class="form-label">GSTIN (15 Digits)</label>
            <input type="text" class="form-control font-mono" id="cust-gstin" maxlength="15" style="text-transform:uppercase;" value="${cust.gstin || ''}" placeholder="27AABCV1234F1Z8">
          </div>

          <div class="form-group col-span-2">
            <label class="form-label">Billing & Dispatch Address</label>
            <textarea class="form-control" id="cust-address" rows="2" placeholder="Factory / Office unit, Road, Industrial Area">${cust.address || ''}</textarea>
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
            <input type="text" class="form-control font-mono" id="cust-pincode" maxlength="6" value="${cust.pincode || '400093'}">
          </div>

          <div class="form-group">
            <label class="form-label">Approved Credit Limit (₹)</label>
            <input type="number" class="form-control font-mono" id="cust-credit" min="0" step="50000" value="${cust.creditLimit !== undefined ? cust.creditLimit : 1500000}">
          </div>

          <div class="form-group">
            <label class="form-label">${isEdit ? 'Current Outstanding Balance (₹)' : 'Opening Outstanding Balance (₹)'}</label>
            <input type="number" class="form-control font-mono" id="cust-outstanding" min="0" step="1000" value="${cust.outstanding || 0}">
          </div>

          <div class="form-group">
            <label class="form-label">Payment Terms</label>
            <select class="form-control" id="cust-terms">
              <option value="Immediate" ${cust.paymentTerms === 'Immediate' ? 'selected' : ''}>Immediate (Advance)</option>
              <option value="7 Days" ${cust.paymentTerms === '7 Days' ? 'selected' : ''}>Net 7 Days</option>
              <option value="15 Days" ${cust.paymentTerms === '15 Days' ? 'selected' : ''}>Net 15 Days</option>
              <option value="30 Days" ${cust.paymentTerms === '30 Days' || !cust.paymentTerms || cust.paymentTerms === 'Net 30 Days' ? 'selected' : ''}>Net 30 Days</option>
              <option value="45 Days" ${cust.paymentTerms === '45 Days' ? 'selected' : ''}>Net 45 Days</option>
              <option value="60 Days" ${cust.paymentTerms === '60 Days' ? 'selected' : ''}>Net 60 Days</option>
              <option value="90 Days" ${cust.paymentTerms === '90 Days' ? 'selected' : ''}>Net 90 Days</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Account Status</label>
            <select class="form-control" id="cust-status">
              <option value="Active" ${cust.status === 'Active' || !cust.status ? 'selected' : ''}>Active</option>
              <option value="Inactive" ${cust.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
              <option value="Blocked" ${cust.status === 'Blocked' ? 'selected' : ''}>Blocked (Credit Hold)</option>
            </select>
          </div>
        </div>
      </form>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="UI.closeModal()">Cancel</button>
      <button class="btn btn-primary" id="btn-save-cust">${isEdit ? 'Update Customer' : 'Save Customer'}</button>
    `;

    UI.openModal({ title: isEdit ? `Edit Customer - ${cust.name}` : "Add New Customer Account", content, footer, size: "modal-lg" });

    document.getElementById("btn-save-cust").onclick = () => {
      const name = document.getElementById("cust-name").value.trim();
      const mobile = document.getElementById("cust-mobile").value.trim();
      const email = document.getElementById("cust-email").value.trim();
      const contactPerson = document.getElementById("cust-contact").value.trim();

      if (!name) return UI.showToast("Required Field", "Customer / Brand Name is required", "error");
      if (!mobile) return UI.showToast("Required Field", "Mobile Number is required", "error");
      if (!email) return UI.showToast("Required Field", "Email Address is required", "error");

      const payload = {
        name,
        companyName: document.getElementById("cust-company").value.trim() || name,
        contactPerson,
        mobile,
        phone: mobile,
        email,
        gstin: document.getElementById("cust-gstin").value.trim().toUpperCase(),
        address: document.getElementById("cust-address").value.trim(),
        city: document.getElementById("cust-city").value.trim(),
        state: document.getElementById("cust-state").value.trim(),
        pincode: document.getElementById("cust-pincode").value.trim(),
        creditLimit: Number(document.getElementById("cust-credit").value) || 0,
        outstanding: Number(document.getElementById("cust-outstanding").value) || 0,
        paymentTerms: document.getElementById("cust-terms").value,
        status: document.getElementById("cust-status").value
      };

      if (isEdit) {
        ERPState.updateCustomer(customerId, payload);
        UI.showToast("Customer Updated", `${name} updated successfully in ERP database`, "success");
      } else {
        const created = ERPState.addCustomer(payload);
        UI.showToast("Customer Created", `${name} (${created.id}) registered successfully`, "success");
      }

      UI.closeModal();
      App.refreshCurrentView();
    };
  },

  openCustomerDrawer(customerId, initialTab = "overview") {
    const cust = ERPState.getCustomerById(customerId);
    if (!cust) return;

    this._custState.activeCustomerId = customerId;
    this._custState.activeDrawerTab = initialTab;

    const stmt = ERPState.getCustomerStatement(customerId);
    const orders = stmt ? stmt.orders : [];
    const invoices = stmt ? stmt.invoices : [];
    const payments = stmt ? stmt.payments : [];

    const outAmt = Number(cust.outstanding) || 0;
    const credLim = Number(cust.creditLimit) || 1;
    const utilPercent = Math.min(100, Math.round((outAmt / credLim) * 100));
    const availableCredit = Math.max(0, credLim - outAmt);
    const isHighRisk = outAmt >= credLim * 0.9 && outAmt > 0;

    const renderOverviewTab = () => `
      <div style="display:flex; flex-direction:column; gap:20px;">
        
        <!-- Financial Health Progress & KPIs -->
        <div style="background:var(--slate-50); border:1px solid var(--slate-200); border-radius:var(--radius-lg); padding:16px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
            <span style="font-size:0.8rem; font-weight:700; color:var(--slate-700); text-transform:uppercase;">Credit Utilization</span>
            <span style="font-size:0.85rem; font-weight:800; color:${isHighRisk ? 'var(--danger-600)' : 'var(--primary-600)'};">${utilPercent}% Used</span>
          </div>
          <div style="width:100%; height:8px; background:var(--slate-200); border-radius:4px; overflow:hidden;">
            <div style="width:${utilPercent}%; height:100%; background:${isHighRisk ? 'var(--danger-500)' : (utilPercent > 60 ? '#f59e0b' : 'var(--primary-500)')}; border-radius:4px; transition:width 0.4s ease;"></div>
          </div>
          <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:10px; margin-top:14px; text-align:center;">
            <div style="background:#ffffff; padding:10px; border-radius:8px; border:1px solid var(--slate-200);">
              <div style="font-size:0.7rem; color:var(--slate-500); font-weight:600;">Outstanding</div>
              <div style="font-size:1.1rem; font-weight:800; color:${outAmt > 0 ? 'var(--danger-600)' : 'var(--success-600)'}; margin-top:2px;">${UI.formatCurrency(outAmt)}</div>
            </div>
            <div style="background:#ffffff; padding:10px; border-radius:8px; border:1px solid var(--slate-200);">
              <div style="font-size:0.7rem; color:var(--slate-500); font-weight:600;">Available Credit</div>
              <div style="font-size:1.1rem; font-weight:800; color:var(--success-600); margin-top:2px;">${UI.formatCurrency(availableCredit)}</div>
            </div>
            <div style="background:#ffffff; padding:10px; border-radius:8px; border:1px solid var(--slate-200);">
              <div style="font-size:0.7rem; color:var(--slate-500); font-weight:600;">Credit Limit</div>
              <div style="font-size:1.1rem; font-weight:800; color:var(--slate-800); margin-top:2px;">${UI.formatCurrency(credLim)}</div>
            </div>
          </div>
        </div>

        <!-- Company & Contact Profile -->
        <div style="border:1px solid var(--slate-200); border-radius:var(--radius-lg); padding:16px;">
          <h4 style="font-size:0.9rem; margin-bottom:12px; color:var(--slate-900); display:flex; align-items:center; gap:8px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Company & Contact Information
          </h4>
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; font-size:0.825rem;">
            <div><span style="color:var(--slate-500);">Legal Name:</span> <strong style="color:var(--slate-800);">${cust.companyName || cust.name}</strong></div>
            <div><span style="color:var(--slate-500);">Contact Person:</span> <strong style="color:var(--slate-800);">${cust.contactPerson || '-'}</strong></div>
            <div><span style="color:var(--slate-500);">Mobile Phone:</span> <a href="tel:${cust.mobile}" style="color:var(--primary-600); font-weight:600; text-decoration:none;">${cust.mobile || '-'}</a></div>
            <div><span style="color:var(--slate-500);">Email:</span> <a href="mailto:${cust.email}" style="color:var(--primary-600); font-weight:600; text-decoration:none;">${cust.email || '-'}</a></div>
            <div><span style="color:var(--slate-500);">GSTIN:</span> <code class="font-mono font-bold">${cust.gstin || 'Unregistered'}</code></div>
            <div><span style="color:var(--slate-500);">Payment Terms:</span> <strong>${cust.paymentTerms || '30 Days Net'}</strong></div>
            <div style="grid-column:span 2;"><span style="color:var(--slate-500);">Address:</span> ${cust.address ? `${cust.address}, ${cust.city}, ${cust.state} - ${cust.pincode}` : `${cust.city}, ${cust.state}`}</div>
          </div>
        </div>

        <!-- Quick Summary Metrics -->
        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:10px;">
          <div style="background:#ffffff; border:1px solid var(--slate-200); border-radius:var(--radius-md); padding:12px; text-align:center;">
            <div style="font-size:0.75rem; color:var(--slate-500);">Sales Orders</div>
            <div style="font-size:1.25rem; font-weight:800; color:var(--slate-900); margin-top:2px;">${orders.length}</div>
          </div>
          <div style="background:#ffffff; border:1px solid var(--slate-200); border-radius:var(--radius-md); padding:12px; text-align:center;">
            <div style="font-size:0.75rem; color:var(--slate-500);">Tax Invoices</div>
            <div style="font-size:1.25rem; font-weight:800; color:var(--slate-900); margin-top:2px;">${invoices.length}</div>
          </div>
          <div style="background:#ffffff; border:1px solid var(--slate-200); border-radius:var(--radius-md); padding:12px; text-align:center;">
            <div style="font-size:0.75rem; color:var(--slate-500);">Payment Receipts</div>
            <div style="font-size:1.25rem; font-weight:800; color:var(--slate-900); margin-top:2px;">${payments.length}</div>
          </div>
        </div>

        <!-- Action Shortcuts -->
        <div style="display:flex; gap:10px; justify-content:flex-end; flex-wrap:wrap; margin-top:8px;">
          <button class="btn btn-secondary btn-sm" onclick="MastersView.openCustomerModal('${cust.id}')">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
            Edit Profile
          </button>
          <button class="btn btn-primary btn-sm" style="background:#2563eb; color:#ffffff;" onclick="MastersView.openCustomerStatementModal('${cust.id}')">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            Account Ledger
          </button>
          <button class="btn btn-success btn-sm" onclick="MastersView.openCustomerReceiptModal('${cust.id}')">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            Record Payment
          </button>
          <button class="btn btn-secondary btn-sm" onclick="UI.closeDrawer(); App.navigate('invoices', 'create'); setTimeout(() => { if(window.InvoicesView) InvoicesView.onCustomerSelect('${cust.name}'); }, 150);">
            + Create Invoice
          </button>
        </div>
      </div>
    `;

    const renderOrdersTab = () => `
      <div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
          <h4 style="font-size:0.9rem; color:var(--slate-900); margin:0;">Active & Historical Customer Orders (${orders.length})</h4>
          <button class="btn btn-primary btn-sm" onclick="UI.closeDrawer(); App.navigate('production', 'orders');">
            + New Production Order
          </button>
        </div>
        ${orders.length > 0 ? `
          <div class="table-responsive">
            <table class="data-table" style="font-size:0.8rem;">
              <thead>
                <tr>
                  <th>Order No</th>
                  <th>Style / Product</th>
                  <th>Quantity</th>
                  <th>Total Amount</th>
                  <th>Delivery</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                ${orders.map(o => `
                  <tr>
                    <td class="mono-cell font-bold" style="color:var(--primary-600);">${o.id}</td>
                    <td><strong>${o.product || o.style || '-'}</strong></td>
                    <td class="font-mono">${(o.quantity || 0).toLocaleString('en-IN')} pcs</td>
                    <td class="font-bold font-mono">${UI.formatCurrency(o.amount || 0)}</td>
                    <td style="font-size:0.75rem;">${o.deliveryDate || o.targetDate || '-'}</td>
                    <td>${UI.formatStatusBadge(o.status || 'Active')}</td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>
        ` : `<div style="text-align:center; padding:30px; color:var(--slate-400); font-size:0.85rem;">No sales orders recorded for this customer yet.</div>`}
      </div>
    `;

    const renderInvoicesTab = () => `
      <div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
          <h4 style="font-size:0.9rem; color:var(--slate-900); margin:0;">GST Tax Invoices (${invoices.length})</h4>
          <button class="btn btn-primary btn-sm" onclick="UI.closeDrawer(); App.navigate('invoices', 'create');">
            + New Sales Invoice
          </button>
        </div>
        ${invoices.length > 0 ? `
          <div class="table-responsive">
            <table class="data-table" style="font-size:0.8rem;">
              <thead>
                <tr>
                  <th>Invoice No</th>
                  <th>Date</th>
                  <th>Amount</th>
                  <th>Paid</th>
                  <th>Balance</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                ${invoices.map(i => `
                  <tr>
                    <td class="mono-cell font-bold" style="color:var(--primary-600);">${i.invoiceNo}</td>
                    <td>${i.date}</td>
                    <td class="font-bold font-mono">${UI.formatCurrency(i.amount)}</td>
                    <td class="font-mono" style="color:var(--success-700);">${UI.formatCurrency(i.paidAmount || 0)}</td>
                    <td class="font-bold font-mono" style="color:${(i.balanceAmount || 0) > 0 ? 'var(--danger-600)' : 'var(--success-600)'};">
                      ${UI.formatCurrency(i.balanceAmount !== undefined ? i.balanceAmount : (i.amount - (i.paidAmount || 0)))}
                    </td>
                    <td>${UI.formatStatusBadge(i.status)}</td>
                    <td>
                      <button class="table-action-btn view" onclick="UI.closeDrawer(); App.navigate('invoices', 'list'); setTimeout(() => { if(window.InvoicesView) InvoicesView.openInvoiceModal('${i.invoiceNo}'); }, 150);">View</button>
                    </td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>
        ` : `<div style="text-align:center; padding:30px; color:var(--slate-400); font-size:0.85rem;">No invoices generated for this customer yet.</div>`}
      </div>
    `;

    const renderPaymentsTab = () => `
      <div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
          <h4 style="font-size:0.9rem; color:var(--slate-900); margin:0;">Payment Receipts & Settlements (${payments.length})</h4>
          <button class="btn btn-success btn-sm" onclick="MastersView.openCustomerReceiptModal('${cust.id}')">
            + Record Receipt
          </button>
        </div>
        ${payments.length > 0 ? `
          <div class="table-responsive">
            <table class="data-table" style="font-size:0.8rem;">
              <thead>
                <tr>
                  <th>Receipt ID</th>
                  <th>Date</th>
                  <th>Payment Mode</th>
                  <th>UTR / Reference</th>
                  <th>Settled Amount</th>
                  <th>Remarks</th>
                </tr>
              </thead>
              <tbody>
                ${payments.map(p => `
                  <tr>
                    <td class="mono-cell font-bold" style="color:var(--success-700);">${p.id}</td>
                    <td>${p.date}</td>
                    <td>${p.mode}</td>
                    <td class="mono-cell" style="font-size:0.75rem;">${p.refNo}</td>
                    <td class="font-bold font-mono" style="color:var(--success-700);">${UI.formatCurrency(p.amount)}</td>
                    <td style="font-size:0.75rem; color:var(--slate-500);">${p.remarks || '-'}</td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>
        ` : `<div style="text-align:center; padding:30px; color:var(--slate-400); font-size:0.85rem;">No payment receipts recorded for this customer yet.</div>`}
      </div>
    `;

    const drawerBodyContent = `
      <div id="cust-drawer-tab-content">
        ${initialTab === 'overview' ? renderOverviewTab() : 
          initialTab === 'orders' ? renderOrdersTab() : 
          initialTab === 'invoices' ? renderInvoicesTab() : 
          renderPaymentsTab()}
      </div>
    `;

    UI.openDrawer({
      title: cust.name,
      subtitle: `${cust.id} • ${cust.companyName || cust.name} • ${cust.city || 'Mumbai'}`,
      tabs: [
        { id: "overview", label: "Overview & Profile" },
        { id: "orders", label: `Orders (${orders.length})` },
        { id: "invoices", label: `Invoices (${invoices.length})` },
        { id: "payments", label: `Receipts (${payments.length})` }
      ],
      content: drawerBodyContent,
      size: "drawer-lg"
    });

    // Add click listeners to drawer tab buttons
    setTimeout(() => {
      document.querySelectorAll(".drawer-tab-btn").forEach(btn => {
        btn.onclick = () => {
          document.querySelectorAll(".drawer-tab-btn").forEach(b => b.classList.remove("active"));
          btn.classList.add("active");
          const tabId = btn.getAttribute("data-tab");
          const tabContentContainer = document.getElementById("cust-drawer-tab-content");
          if (tabContentContainer) {
            if (tabId === "overview") tabContentContainer.innerHTML = renderOverviewTab();
            else if (tabId === "orders") tabContentContainer.innerHTML = renderOrdersTab();
            else if (tabId === "invoices") tabContentContainer.innerHTML = renderInvoicesTab();
            else if (tabId === "payments") tabContentContainer.innerHTML = renderPaymentsTab();
          }
        };
      });
    }, 50);
  },

  openCustomerStatementModal(customerId) {
    const cust = ERPState.getCustomerById(customerId);
    if (!cust) return;

    const stmt = ERPState.getCustomerStatement(customerId);
    if (!stmt) return;

    const content = `
      <div id="customer-statement-print-area">
        <!-- Statement Header -->
        <div style="display:flex; justify-content:space-between; align-items:flex-start; border-bottom:2px solid var(--slate-900); padding-bottom:16px; margin-bottom:16px;">
          <div>
            <h2 style="font-size:1.25rem; font-weight:800; color:var(--slate-900); margin:0;">STATEMENT OF ACCOUNT</h2>
            <div style="font-size:0.8rem; color:var(--slate-500); margin-top:2px;">FashionWorks Pvt. Ltd. • GarmentERP</div>
          </div>
          <div style="text-align:right;">
            <div style="font-size:0.75rem; color:var(--slate-500);">Statement Date</div>
            <div style="font-size:0.9rem; font-weight:700; color:var(--slate-900);">${new Date().toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' })}</div>
          </div>
        </div>

        <!-- Customer Summary Card -->
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; background:var(--slate-50); border:1px solid var(--slate-200); border-radius:var(--radius-md); padding:14px; margin-bottom:18px; font-size:0.825rem;">
          <div>
            <div style="font-size:0.7rem; color:var(--slate-500); text-transform:uppercase; font-weight:700;">Account Details</div>
            <div style="font-size:1rem; font-weight:800; color:var(--slate-900); margin-top:2px;">${cust.name}</div>
            <div style="color:var(--slate-600);">${cust.companyName || ''}</div>
            <div style="color:var(--slate-500); margin-top:4px;">GSTIN: <span class="font-mono font-bold">${cust.gstin || 'Unregistered'}</span></div>
            <div style="color:var(--slate-500);">${cust.address || ''}, ${cust.city}</div>
          </div>
          <div style="text-align:right;">
            <div style="font-size:0.7rem; color:var(--slate-500); text-transform:uppercase; font-weight:700;">Financial Summary</div>
            <div style="margin-top:4px;"><span style="color:var(--slate-500);">Total Invoiced:</span> <strong class="font-mono">${UI.formatCurrency(stmt.totalInvoiced)}</strong></div>
            <div><span style="color:var(--slate-500);">Total Received:</span> <strong class="font-mono" style="color:var(--success-700);">${UI.formatCurrency(stmt.totalReceived)}</strong></div>
            <div style="margin-top:6px; font-size:1.05rem; font-weight:800; color:${(cust.outstanding || 0) > 0 ? 'var(--danger-600)' : 'var(--success-600)'};">
              Net Balance Due: ${UI.formatCurrency(cust.outstanding || 0)}
            </div>
          </div>
        </div>

        <!-- Statement Ledger Table -->
        <div class="table-responsive">
          <table class="data-table" style="font-size:0.8rem;">
            <thead>
              <tr>
                <th>Date</th>
                <th>Transaction Details</th>
                <th>Reference #</th>
                <th style="text-align:right;">Debit (₹)</th>
                <th style="text-align:right;">Credit (₹)</th>
                <th style="text-align:right;">Running Balance (₹)</th>
              </tr>
            </thead>
            <tbody>
              ${stmt.ledger.length > 0 ? stmt.ledger.map(row => `
                <tr>
                  <td>${row.date}</td>
                  <td><strong>${row.type}</strong> <div style="font-size:0.725rem; color:var(--slate-500);">${row.description}</div></td>
                  <td class="mono-cell font-bold">${row.ref}</td>
                  <td style="text-align:right; font-mono; font-weight:600; color:${row.debit > 0 ? 'var(--danger-600)' : 'inherit'};">
                    ${row.debit > 0 ? UI.formatCurrency(row.debit) : '-'}
                  </td>
                  <td style="text-align:right; font-mono; font-weight:600; color:${row.credit > 0 ? 'var(--success-700)' : 'inherit'};">
                    ${row.credit > 0 ? UI.formatCurrency(row.credit) : '-'}
                  </td>
                  <td style="text-align:right; font-mono; font-weight:700; color:${row.balance > 0 ? 'var(--danger-700)' : 'var(--success-700)'};">
                    ${UI.formatCurrency(row.balance)}
                  </td>
                </tr>
              `).join('') : `
                <tr>
                  <td colspan="6" style="text-align:center; padding:20px; color:var(--slate-400);">No transactions found for this statement period.</td>
                </tr>
              `}
            </tbody>
            <tfoot>
              <tr style="background:var(--slate-50); font-weight:800;">
                <td colspan="3" style="text-align:right;">Total Summary:</td>
                <td style="text-align:right; color:var(--danger-600);">${UI.formatCurrency(stmt.totalInvoiced)}</td>
                <td style="text-align:right; color:var(--success-700);">${UI.formatCurrency(stmt.totalReceived)}</td>
                <td style="text-align:right; color:${(cust.outstanding || 0) > 0 ? 'var(--danger-700)' : 'var(--success-700)'};">${UI.formatCurrency(cust.outstanding || 0)}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="UI.closeModal()">Close</button>
      <button class="btn btn-primary" onclick="MastersView.printCustomerStatement('${cust.id}')">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
        Print / Save PDF
      </button>
    `;

    UI.openModal({ title: `Account Statement - ${cust.name}`, content, footer, size: "modal-lg" });
  },

  printCustomerStatement(customerId) {
    const printContent = document.getElementById("customer-statement-print-area");
    if (!printContent) return;

    const printWin = window.open('', '', 'width=900,height=700');
    printWin.document.write(`
      <html>
        <head>
          <title>Account Statement - ${customerId}</title>
          <style>
            body { font-family: 'Plus Jakarta Sans', Arial, sans-serif; padding: 25px; color: #0f172a; }
            table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 13px; }
            th, td { border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; }
            th { background: #f1f5f9; }
            .font-mono { font-family: monospace; }
            .font-bold { font-weight: bold; }
          </style>
        </head>
        <body>
          ${printContent.innerHTML}
        </body>
      </html>
    `);
    printWin.document.close();
    printWin.focus();
    setTimeout(() => {
      printWin.print();
      printWin.close();
    }, 300);
  },

  printCustomerDirectory() {
    const customers = ERPState.data.customers || [];
    const printWin = window.open('', '', 'width=1000,height=700');
    printWin.document.write(`
      <html>
        <head>
          <title>Customer Master Directory - GarmentERP</title>
          <style>
            body { font-family: Arial, sans-serif; padding: 25px; font-size: 12px; }
            h2 { margin-bottom: 4px; }
            table { width: 100%; border-collapse: collapse; margin-top: 15px; }
            th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
            th { background: #f1f5f9; font-weight: bold; }
          </style>
        </head>
        <body>
          <h2>FashionWorks Pvt. Ltd. - Customer Master Directory</h2>
          <p>Generated on ${new Date().toLocaleString()}</p>
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Company</th>
                <th>Contact</th>
                <th>Mobile</th>
                <th>GSTIN</th>
                <th>City</th>
                <th>Credit Limit</th>
                <th>Outstanding</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              ${customers.map(c => `
                <tr>
                  <td>${c.id}</td>
                  <td><strong>${c.name}</strong></td>
                  <td>${c.companyName || ''}</td>
                  <td>${c.contactPerson || ''}</td>
                  <td>${c.mobile || c.phone || ''}</td>
                  <td>${c.gstin || '-'}</td>
                  <td>${c.city || ''}</td>
                  <td>₹${(c.creditLimit || 0).toLocaleString('en-IN')}</td>
                  <td>₹${(c.outstanding || 0).toLocaleString('en-IN')}</td>
                  <td>${c.status || 'Active'}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </body>
      </html>
    `);
    printWin.document.close();
    printWin.focus();
    setTimeout(() => {
      printWin.print();
      printWin.close();
    }, 300);
  },

  openCustomerReceiptModal(customerId) {
    const cust = ERPState.getCustomerById(customerId);
    if (!cust) return;

    const content = `
      <form id="cust-quick-receipt-form">
        <div style="background:var(--primary-50); padding:12px; border-radius:var(--radius-md); margin-bottom:16px; border:1px solid var(--primary-100);">
          <div style="font-size:0.8rem; color:var(--primary-700); font-weight:700;">Customer Account</div>
          <div style="font-size:1.05rem; font-weight:800; color:var(--slate-900);">${cust.name}</div>
          <div style="font-size:0.825rem; color:var(--slate-600); margin-top:2px;">Current Outstanding Balance: <strong class="font-mono" style="color:var(--danger-600);">${UI.formatCurrency(cust.outstanding || 0)}</strong></div>
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Payment Date <span class="required-star">*</span></label>
            <input type="date" class="form-control" id="qrec-date" value="${new Date().toISOString().split('T')[0]}">
          </div>

          <div class="form-group">
            <label class="form-label">Received Amount (₹) <span class="required-star">*</span></label>
            <input type="number" class="form-control font-mono font-bold" id="qrec-amount" min="1" max="${cust.outstanding || 99999999}" value="${cust.outstanding > 0 ? cust.outstanding : 50000}" required>
          </div>

          <div class="form-group">
            <label class="form-label">Payment Mode <span class="required-star">*</span></label>
            <select class="form-control" id="qrec-mode">
              <option value="Bank Transfer (NEFT)">Bank Transfer (NEFT)</option>
              <option value="RTGS">RTGS</option>
              <option value="UPI / QR Payment">UPI / QR Payment</option>
              <option value="Cheque Deposit">Cheque Deposit</option>
              <option value="Cash Receipt">Cash Receipt</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Bank Reference / UTR Number <span class="required-star">*</span></label>
            <input type="text" class="form-control font-mono" id="qrec-ref" required value="UTR${Date.now().toString().slice(-8)}" placeholder="e.g. UTR / Cheque No">
          </div>

          <div class="form-group col-span-2">
            <label class="form-label">Notes & Remarks</label>
            <textarea class="form-control" id="qrec-remarks" rows="2" placeholder="Payment towards outstanding balance"></textarea>
          </div>
        </div>
      </form>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="UI.closeModal()">Cancel</button>
      <button class="btn btn-success" id="btn-save-qrec">Confirm & Settle Balance</button>
    `;

    UI.openModal({ title: `Receive Payment - ${cust.name}`, content, footer, size: "modal-md" });

    document.getElementById("btn-save-qrec").onclick = () => {
      const amount = Number(document.getElementById("qrec-amount").value);
      const date = document.getElementById("qrec-date").value;
      const mode = document.getElementById("qrec-mode").value;
      const refNo = document.getElementById("qrec-ref").value.trim();
      const remarks = document.getElementById("qrec-remarks").value.trim();

      if (amount <= 0) return UI.showToast("Invalid Amount", "Payment amount must be greater than 0", "error");
      if (!refNo) return UI.showToast("Required", "Bank Reference / UTR number is required", "error");

      ERPState.recordPayment({
        customer: cust.name,
        amount,
        date,
        mode,
        refNo,
        remarks
      });

      UI.showToast("Payment Recorded", `₹${amount.toLocaleString('en-IN')} credited to ${cust.name}`, "success");
      UI.closeModal();
      UI.closeDrawer();
      App.refreshCurrentView();
    };
  },

  confirmDeleteCustomer(id) {
    const cust = ERPState.getCustomerById(id);
    if (!cust) return;

    UI.showConfirm({
      title: "Delete Customer Account?",
      message: `Are you sure you want to permanently delete <strong>${cust.name}</strong> (${cust.id})? This will remove their master records.`,
      confirmText: "Yes, Delete Customer",
      isDanger: true,
      onConfirm: () => {
        ERPState.deleteCustomer(id);
        UI.showToast("Customer Deleted", `${cust.name} removed from master records`, "warning");
        App.refreshCurrentView();
      }
    });
  },

  exportCustomers() {
    const headers = ["Customer ID", "Customer Name", "Company Name", "Contact Person", "Mobile", "Email", "GSTIN", "City", "State", "Credit Limit", "Outstanding", "Payment Terms", "Status"];
    const rows = (ERPState.data.customers || []).map(c => [
      c.id || c.code,
      c.name,
      c.companyName || '',
      c.contactPerson || '',
      c.mobile || c.phone || '',
      c.email || '',
      c.gstin || '',
      c.city || '',
      c.state || '',
      c.creditLimit || 0,
      c.outstanding || 0,
      c.paymentTerms || '30 Days',
      c.status || 'Active'
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
              ${(vendors || []).length === 0 ? `
                <tr>
                  <td colspan="10" style="text-align:center; padding:32px 20px; color:var(--slate-400);">
                    <div style="font-size:1rem; font-weight:600; color:var(--slate-600); margin-bottom:4px;">No Vendors Registered</div>
                    <div style="font-size:0.825rem;">Click the <strong>+ Add Vendor</strong> button above to register your first supplier.</div>
                  </td>
                </tr>
              ` : vendors.map(v => `
                <tr>
                  <td class="mono-cell font-bold" style="color:var(--primary-600);">${v.id || v.code}</td>
                  <td class="primary-cell">${v.name}</td>
                  <td><span class="badge badge-slate">${v.category || 'Supplier'}</span></td>
                  <td>${v.contactPerson || '-'}</td>
                  <td>${v.mobile || v.phone || '-'}</td>
                  <td class="mono-cell">${v.gstin || '-'}</td>
                  <td>${v.paymentTerms || '30 Days'}</td>
                  <td class="font-bold" style="color:${(v.outstanding || 0) > 0 ? 'var(--danger-600)' : 'var(--success-600)'};">${UI.formatCurrency(v.outstanding || 0)}</td>
                  <td>${UI.formatStatusBadge(v.status || 'Active')}</td>
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
              ${(workers || []).length === 0 ? `
                <tr>
                  <td colspan="9" style="text-align:center; padding:32px 20px; color:var(--slate-400);">
                    <div style="font-size:1rem; font-weight:600; color:var(--slate-600); margin-bottom:4px;">No Job Workers Registered</div>
                    <div style="font-size:0.825rem;">Click the <strong>+ Add Job Worker</strong> button above to register a stitching/cutting/printing partner.</div>
                  </td>
                </tr>
              ` : workers.map(w => `
                <tr>
                  <td class="mono-cell font-bold" style="color:var(--primary-600);">${w.id || w.code}</td>
                  <td class="primary-cell">${w.name}</td>
                  <td><span class="badge badge-purple">${w.process || 'Stitching'}</span></td>
                  <td class="font-bold">₹${w.rate || 20} / ${w.rateUnit || 'Piece'}</td>
                  <td>${(w.capacityPerDay || 1000).toLocaleString('en-IN')} pcs/day</td>
                  <td>${w.city || '-'}</td>
                  <td class="font-bold">${UI.formatCurrency(w.outstanding || 0)}</td>
                  <td>${UI.formatStatusBadge(w.status || 'Active')}</td>
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
              ${(items || []).length === 0 ? `
                <tr>
                  <td colspan="11" style="text-align:center; padding:32px 20px; color:var(--slate-400);">
                    <div style="font-size:1rem; font-weight:600; color:var(--slate-600); margin-bottom:4px;">No Items in Catalog</div>
                    <div style="font-size:0.825rem;">Click the <strong>+ Add Item</strong> button above to register fabrics, trims, or garments.</div>
                  </td>
                </tr>
              ` : items.map(i => `
                <tr>
                  <td class="mono-cell font-bold" style="color:var(--primary-600);">${i.code || i.id}</td>
                  <td class="primary-cell">${i.name}</td>
                  <td><span class="badge badge-primary">${i.type || 'Raw Material'}</span></td>
                  <td>${i.category || 'Fabric'}</td>
                  <td>${i.unit || 'Meters'}</td>
                  <td class="mono-cell">${i.hsn || '5208'}</td>
                  <td class="font-bold">₹${i.rate || i.unitCost || 0}</td>
                  <td class="font-bold font-mono" style="color:${(i.currentStock || 0) <= (i.reorderLevel || 100) ? 'var(--danger-600)' : 'var(--slate-900)'};">
                    ${(i.currentStock || 0).toLocaleString('en-IN')} ${i.unit || 'Meters'}
                    ${(i.currentStock || 0) <= (i.reorderLevel || 100) ? '<span class="badge badge-danger" style="margin-left:4px;">LOW</span>' : ''}
                  </td>
                  <td class="font-mono text-muted">${(i.reorderLevel || 100).toLocaleString('en-IN')}</td>
                  <td>${UI.formatStatusBadge(i.status || 'Active')}</td>
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

  // 5. UNIT MASTER (UOM) MANAGEMENT - HIERARCHICAL CONVERSION LOGIC
  renderUnits() {
    const rawUnits = ERPState.data.units || [];
    
    // Group base units and their respective sub-units
    const baseUnits = rawUnits.filter(u => !u.parentId);
    const subUnits = rawUnits.filter(u => !!u.parentId);

    // Build ordered list where each base unit is immediately followed by its sub-units
    const orderedList = [];
    baseUnits.forEach((base, index) => {
      orderedList.push({ ...base, rowIndex: index + 1, isChild: false });
      const children = subUnits.filter(sub => sub.parentId == base.id || sub.parentId == base.dbId || (base.code && sub.parentName === base.code) || (base.name && sub.parentName === base.name));
      children.forEach(child => {
        orderedList.push({
          ...child,
          rowIndex: null,
          isChild: true,
          parentObj: base,
          conversionDisplay: child.conversionFactor ? `1 ${base.name} = ${Number(child.conversionFactor).toFixed(2)} ${child.name}` : (child.conversionText || "—")
        });
      });
    });

    // Also include any orphan sub-units whose parent might not be in base list
    subUnits.forEach(sub => {
      const alreadyAdded = orderedList.some(item => item.id == sub.id || (item.dbId && item.dbId == sub.dbId));
      if (!alreadyAdded) {
        orderedList.push({
          ...sub,
          rowIndex: null,
          isChild: true,
          parentObj: { name: sub.parentName || "Parent" },
          conversionDisplay: sub.conversionFactor ? `1 ${sub.parentName || 'Unit'} = ${Number(sub.conversionFactor).toFixed(2)} ${sub.name}` : (sub.conversionText || "—")
        });
      }
    });

    const totalCount = orderedList.length;

    return `
      <!-- Main Unit Master Container matching screenshot -->
      <div style="background:#ffffff; border-radius:16px; border:1px solid #e5e7eb; box-shadow:0 1px 3px rgba(0,0,0,0.04); padding:28px; margin-bottom:30px;">
        
        <!-- Top Toolbar: Search Bar (Left) & Add Unit Button (Right) -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:28px; flex-wrap:wrap; gap:16px;">
          <!-- Search Bar -->
          <div style="display:flex; align-items:center; gap:10px; width:100%; max-width:440px;">
            <div style="position:relative; flex:1;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" style="position:absolute; left:16px; top:50%; transform:translateY(-50%);"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              <input 
                type="text" 
                id="input-search-unit"
                placeholder="Search unit..." 
                oninput="MastersView.filterGenericTable('table-unit-master-custom', this.value)"
                style="width:100%; height:44px; padding:8px 16px 8px 44px; border:1px solid #e5e7eb; border-radius:9999px; font-size:0.95rem; color:#111827; outline:none; background:#ffffff; transition:all 0.2s ease;"
                onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.1)';"
                onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';"
              >
            </div>
            <button 
              class="btn" 
              onclick="MastersView.filterGenericTable('table-unit-master-custom', document.getElementById('input-search-unit').value)"
              style="height:44px; padding:0 24px; background:#6366f1; color:#ffffff; font-weight:700; border-radius:9999px; border:none; cursor:pointer; font-size:0.95rem; display:inline-flex; align-items:center; justify-content:center; transition:background 0.2s ease;"
              onmouseover="this.style.background='#4f46e5'"
              onmouseout="this.style.background='#6366f1'"
            >
              Search
            </button>
          </div>

          <!-- Add Unit Button (Forest Green Pill) -->
          <div>
            <button 
              class="btn" 
              onclick="MastersView.openUnitModal()"
              style="height:44px; padding:0 24px; background:#059669; color:#ffffff; font-weight:700; border-radius:9999px; border:none; cursor:pointer; font-size:0.95rem; display:inline-flex; align-items:center; gap:8px; box-shadow:0 2px 4px rgba(5,150,105,0.2); transition:all 0.2s ease;"
              onmouseover="this.style.background='#047857'; this.style.transform='translateY(-1px)';"
              onmouseout="this.style.background='#059669'; this.style.transform='translateY(0)';"
            >
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              + Add Unit
            </button>
          </div>
        </div>

        <!-- Custom Unit Table matching screenshot -->
        <div style="overflow-x:auto; border-radius:12px; border:1px solid #e5e7eb;">
          <table class="data-table" id="table-unit-master-custom" style="width:100%; border-collapse:collapse; text-align:left;">
            <thead>
              <tr style="background:#ffffff; border-bottom:1px solid #e5e7eb;">
                <th style="padding:16px 20px; font-size:0.75rem; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; width:60px;">#</th>
                <th style="padding:16px 20px; font-size:0.75rem; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; width:25%;">UNIT NAME</th>
                <th style="padding:16px 20px; font-size:0.75rem; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; width:22%;">PARENT UNIT</th>
                <th style="padding:16px 20px; font-size:0.75rem; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; width:25%;">CONVERSION</th>
                <th style="padding:16px 20px; font-size:0.75rem; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; width:14%;">STATUS</th>
                <th style="padding:16px 20px; font-size:0.75rem; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; width:14%; text-align:right;">ACTION</th>
              </tr>
            </thead>
            <tbody>
              ${orderedList.length === 0 ? `
                <tr>
                  <td colspan="6" style="text-align:center; padding:40px; color:#9ca3af;">
                    <div style="font-weight:700; font-size:1rem; margin-bottom:4px;">No Units Registered</div>
                    <div style="font-size:0.85rem;">Click "+ Add Unit" above to configure your base and sub-units.</div>
                  </td>
                </tr>
              ` : orderedList.map(item => `
                <tr style="background:${item.isChild ? '#ffffff' : '#ede9fe'}; border-bottom:1px solid ${item.isChild ? '#f3f4f6' : '#ddd6fe'}; transition:background 0.15s ease;">
                  
                  <!-- Column #: Number for Base, empty/indent for Child -->
                  <td style="padding:18px 20px; font-size:0.95rem; font-weight:600; color:#4b5563;">
                    ${item.isChild ? '' : item.rowIndex}
                  </td>

                  <!-- Column UNIT NAME -->
                  <td style="padding:18px 20px;">
                    ${item.isChild ? `
                      <div style="display:flex; align-items:center; gap:8px; padding-left:24px; color:#111827; font-size:0.925rem;">
                        <span style="color:#6366f1; font-weight:700; font-size:1.1rem; line-height:1;">↳</span>
                        <span style="font-weight:600; color:#1f2937;">${item.name}</span>
                      </div>
                    ` : `
                      <span style="font-weight:800; font-size:0.95rem; color:#111827; letter-spacing:0.02em;">
                        ${item.name}
                      </span>
                    `}
                  </td>

                  <!-- Column PARENT UNIT -->
                  <td style="padding:18px 20px; font-size:0.925rem; font-weight:600; color:#374151;">
                    ${item.isChild ? (item.parentName || (item.parentObj ? item.parentObj.name : '—')) : '—'}
                  </td>

                  <!-- Column CONVERSION -->
                  <td style="padding:18px 20px; font-size:0.925rem; font-weight:700; color:#111827;">
                    ${item.isChild ? item.conversionDisplay : '—'}
                  </td>

                  <!-- Column STATUS -->
                  <td style="padding:18px 20px;">
                    <span style="display:inline-flex; align-items:center; justify-content:center; padding:4px 14px; background:#dcfce7; color:#15803d; border-radius:9999px; font-size:0.8rem; font-weight:700; letter-spacing:0.02em;">
                      ${item.status || 'Active'}
                    </span>
                  </td>

                  <!-- Column ACTION (Edit & Delete Icon Buttons in white rounded boxes) -->
                  <td style="padding:18px 20px; text-align:right;">
                    <div style="display:flex; justify-content:flex-end; gap:8px;">
                      <!-- Edit Button -->
                      <button 
                        title="Edit Unit" 
                        onclick="MastersView.openUnitModal(${item.id || `'${item.code}'`})"
                        style="width:36px; height:36px; border-radius:8px; border:1px solid #d1d5db; background:#ffffff; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; color:#6b7280; box-shadow:0 1px 2px rgba(0,0,0,0.05); transition:all 0.15s ease;"
                        onmouseover="this.style.borderColor='#6366f1'; this.style.color='#4f46e5'; this.style.background='#f5f3ff';"
                        onmouseout="this.style.borderColor='#d1d5db'; this.style.color='#6b7280'; this.style.background='#ffffff';"
                      >
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                      </button>

                      <!-- Delete Button -->
                      <button 
                        title="Delete Unit" 
                        onclick="MastersView.deleteUnit(${item.id || `'${item.code}'`})"
                        style="width:36px; height:36px; border-radius:8px; border:1px solid #d1d5db; background:#ffffff; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; color:#ef4444; box-shadow:0 1px 2px rgba(0,0,0,0.05); transition:all 0.15s ease;"
                        onmouseover="this.style.borderColor='#ef4444'; this.style.color='#b91c1c'; this.style.background='#fef2f2';"
                        onmouseout="this.style.borderColor='#d1d5db'; this.style.color='#ef4444'; this.style.background='#ffffff';"
                      >
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                      </button>
                    </div>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>

        <!-- Bottom Pagination Row matching screenshot -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:24px; flex-wrap:wrap; gap:12px; font-size:0.9rem; color:#6b7280;">
          <div>
            Showing <strong style="color:#111827;">1</strong> to <strong style="color:#111827;">${totalCount}</strong> of <strong style="color:#111827;">${totalCount}</strong> units
          </div>
          <div style="display:flex; align-items:center; gap:8px;">
            <button 
              disabled 
              style="padding:6px 14px; border-radius:8px; border:1px solid #e5e7eb; background:#f9fafb; color:#9ca3af; font-weight:600; font-size:0.85rem; cursor:not-allowed;"
            >
              &laquo; Prev
            </button>
            <span style="padding:6px 12px; font-weight:700; color:#111827; font-size:0.9rem;">1/1</span>
            <button 
              disabled 
              style="padding:6px 14px; border-radius:8px; border:1px solid #e5e7eb; background:#f9fafb; color:#9ca3af; font-weight:600; font-size:0.85rem; cursor:not-allowed;"
            >
              Next &raquo;
            </button>
          </div>
        </div>

      </div>
    `;
  },

  // Modal: Add or Edit Unit with Parent Unit & Conversion Logic
  openUnitModal(unitId = null) {
    const isEdit = unitId !== null && unitId !== undefined;
    const unit = isEdit ? ERPState.getUnitById(unitId) : {
      name: "",
      code: "",
      parentId: null,
      parentName: "",
      conversionFactor: "",
      status: "Active"
    };

    if (isEdit && !unit) return;

    // Get all available parent units (excluding this unit if editing to prevent cycle)
    const availableParents = (ERPState.data.units || []).filter(u => {
      if (isEdit && (u.id == unitId || (u.dbId && u.dbId == unitId))) return false;
      return !u.parentId; // Only base units can be parent
    });

    const content = `
      <form id="form-unit-modal" style="display:flex; flex-direction:column; gap:18px;">
        
        <!-- Unit Name -->
        <div class="form-group">
          <label class="form-label required" style="font-weight:700; color:#374151;">Unit Name</label>
          <input 
            type="text" 
            id="unit-name-input" 
            class="form-control" 
            placeholder="e.g. KG, gm, PCS, PAIR, MTR, cm" 
            value="${unit.name || ''}" 
            required 
            style="height:44px; border-radius:8px; font-size:0.95rem; font-weight:600;"
            oninput="MastersView.updateConversionPreview()"
          >
          <span class="form-hint" style="color:#6b7280; font-size:0.8rem; margin-top:4px;">Enter unit name or abbreviation (e.g. PAIR, PCS, KG, gm, MTR)</span>
        </div>

        <!-- Parent Unit Selection -->
        <div class="form-group">
          <label class="form-label" style="font-weight:700; color:#374151;">Parent Unit (Optional)</label>
          <select 
            id="unit-parent-select" 
            class="form-control" 
            style="height:44px; border-radius:8px; font-size:0.95rem; font-weight:600;"
            onchange="MastersView.toggleConversionFields(this.value)"
          >
            <option value="">— None (This is a Base Unit) —</option>
            ${availableParents.map(p => `
              <option value="${p.id || p.dbId}" ${((unit.parentId == p.id || unit.parentId == p.dbId) || (unit.parentName && (unit.parentName === p.name || unit.parentName === p.code))) ? 'selected' : ''}>
                ${p.name}
              </option>
            `).join('')}
          </select>
          <span class="form-hint" style="color:#6b7280; font-size:0.8rem; margin-top:4px;">Select a parent unit if this unit is a sub-unit (e.g. gm under KG, cm under MTR)</span>
        </div>

        <!-- Conversion Factor Field (Shown if Parent is selected) -->
        <div id="conversion-fields-container" style="display:${unit.parentId ? 'block' : 'none'}; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:16px;">
          <div class="form-group" style="margin-bottom:10px;">
            <label class="form-label required" style="font-weight:700; color:#374151;">Conversion Factor</label>
            <div style="display:flex; align-items:center; gap:10px;">
              <input 
                type="number" 
                step="any"
                id="unit-conversion-factor" 
                class="form-control" 
                placeholder="e.g. 1000 for gm under KG (1 KG = 1000 gm)" 
                value="${unit.conversionFactor || ''}" 
                style="height:44px; border-radius:8px; font-size:0.95rem; font-weight:700;"
                oninput="MastersView.updateConversionPreview()"
              >
            </div>
          </div>
          <div id="conversion-live-preview" style="font-size:0.9rem; font-weight:800; color:#4f46e5; background:#ede9fe; padding:10px 14px; border-radius:8px; border:1px solid #c7d2fe;">
            Conversion: 1 Parent = ? Unit
          </div>
        </div>

        <!-- Status -->
        <div class="form-group">
          <label class="form-label required" style="font-weight:700; color:#374151;">Status</label>
          <select id="unit-status-select" class="form-control" style="height:44px; border-radius:8px; font-size:0.95rem;">
            <option value="Active" ${unit.status === 'Active' ? 'selected' : ''}>Active</option>
            <option value="Inactive" ${unit.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
          </select>
        </div>

      </form>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="UI.closeModal()" style="border-radius:8px; padding:8px 18px;">Cancel</button>
      <button class="btn btn-primary" id="btn-save-unit-custom" style="background:#059669; border:none; border-radius:8px; padding:8px 22px; font-weight:700;">${isEdit ? 'Update Unit' : 'Save Unit'}</button>
    `;

    UI.openModal({
      title: isEdit ? `Edit Unit: ${unit.name}` : "Add New Unit",
      content,
      footer,
      size: "modal-md"
    });

    // Initialize preview immediately if editing
    setTimeout(() => {
      this.updateConversionPreview();
    }, 50);

    document.getElementById("btn-save-unit-custom").onclick = () => {
      const name = document.getElementById("unit-name-input").value.trim();
      const parentSelect = document.getElementById("unit-parent-select");
      const parentId = parentSelect.value ? Number(parentSelect.value) : null;
      const parentName = parentId && parentSelect.selectedIndex >= 0 ? parentSelect.options[parentSelect.selectedIndex].text.trim() : null;
      const conversionFactor = parentId ? Number(document.getElementById("unit-conversion-factor").value) : null;
      const status = document.getElementById("unit-status-select").value;

      if (!name) return UI.showToast("Required Field", "Please enter Unit Name (e.g. PAIR, PCS, KG, gm)", "error");
      if (parentId && (!conversionFactor || conversionFactor <= 0)) {
        return UI.showToast("Required Field", "Please specify a valid conversion factor (e.g. 1000 for gm under KG)", "error");
      }

      const payload = {
        name,
        code: name,
        parentId,
        parentName,
        conversionFactor,
        status
      };

      if (isEdit) {
        ERPState.updateUnit(unitId, payload);
        UI.showToast("Unit Updated", `${name} updated successfully`, "success");
      } else {
        const created = ERPState.addUnit(payload);
        UI.showToast("Unit Added", `${name} registered successfully`, "success");
      }

      UI.closeModal();
      App.refreshCurrentView();
    };
  },

  toggleConversionFields(parentId) {
    const container = document.getElementById("conversion-fields-container");
    if (!container) return;
    if (parentId) {
      container.style.display = "block";
      this.updateConversionPreview();
    } else {
      container.style.display = "none";
    }
  },

  updateConversionPreview() {
    const preview = document.getElementById("conversion-live-preview");
    const nameInput = document.getElementById("unit-name-input");
    const parentSelect = document.getElementById("unit-parent-select");
    const factorInput = document.getElementById("unit-conversion-factor");
    if (!preview || !nameInput || !parentSelect || !factorInput) return;

    const name = nameInput.value.trim() || "Unit";
    const parentName = parentSelect.value && parentSelect.selectedIndex >= 0 ? parentSelect.options[parentSelect.selectedIndex].text.trim() : "Parent";
    const factor = Number(factorInput.value) || 1;

    preview.innerText = `1 ${parentName} = ${factor.toFixed(2)} ${name}`;
  },

  deleteUnit(unitId) {
    const unit = ERPState.getUnitById(unitId);
    if (!unit) return;

    UI.showConfirm({
      title: "Delete Unit?",
      message: `Are you sure you want to delete <strong>${unit.name}</strong>?`,
      confirmText: "Delete Unit",
      isDanger: true,
      onConfirm: () => {
        ERPState.deleteUnit(unitId);
        UI.showToast("Unit Deleted", `${unit.name} deleted successfully`, "warning");
        App.refreshCurrentView();
      }
    });
  },

  exportUnits() {
    const headers = ["Unit Name", "Parent Unit", "Conversion Rate", "Status"];
    const rows = (ERPState.data.units || []).map(u => [
      u.name,
      u.parentName || "—",
      u.conversionFactor ? `1 ${u.parentName} = ${Number(u.conversionFactor).toFixed(2)} ${u.name}` : "—",
      u.status || "Active"
    ]);
    UI.exportToCSV("Unit_Master_Report", headers, rows);
  },

  // Backward compatibility aliases
  renderSizesAndColors() {
    return this.renderUnits();
  },

  filterGenericTable(tableId, query) {
    const q = query.toLowerCase();
    const rows = document.querySelectorAll(`#${tableId} tbody tr`);
    rows.forEach(r => {
      r.style.display = !q || r.innerText.toLowerCase().includes(q) ? "" : "none";
    });
  }
};
