/* ==========================================================================
   DASHBOARD VIEW - 8 KPIS, 9-STAGE PIPELINE, CHARTS & TIMELINE
   GarmentERP
   ========================================================================== */

const DashboardView = {
  render() {
    const stats = ERPState.getDashboardStats();
    const activity = ERPState.data.activityLogs.slice(0, 6);
    const productionOrders = ERPState.data.productionOrders || [];
    const items = ERPState.data.items || [];
    const customers = ERPState.data.customers || [];
    const vendors = ERPState.data.vendors || [];
    const userName = ERPState.data.currentUser ? ERPState.data.currentUser.name : "Admin";

    return `
      <!-- Top Greetings & Date Filters -->
      <div class="dashboard-top-bar">
        <div class="dashboard-title-wrap">
          <h1>
            Good Morning, ${userName}
            <span style="font-size:0.75rem; font-weight:600; padding:2px 8px; background:var(--primary-100); color:var(--primary-700); border-radius:var(--radius-full); vertical-align:middle;">LIVE OPS</span>
          </h1>
          <p class="dashboard-subtitle">Here's what's happening with your manufacturing operations today at FashionWorks Pvt. Ltd.</p>
        </div>

        <div class="dashboard-controls">
          <div class="date-filter-group">
            <button class="date-filter-btn active" onclick="DashboardView.switchDateFilter(this, 'today')">Today</button>
            <button class="date-filter-btn" onclick="DashboardView.switchDateFilter(this, 'week')">This Week</button>
            <button class="date-filter-btn" onclick="DashboardView.switchDateFilter(this, 'month')">This Month</button>
            <button class="date-filter-btn" onclick="DashboardView.switchDateFilter(this, 'custom')">Custom</button>
          </div>

          <button class="btn btn-primary btn-sm" onclick="App.navigate('dispatch', 'dispatch'); setTimeout(() => DispatchView.openCreateDispatchModal(), 100);">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add Order Dispatch
          </button>
        </div>
      </div>

      <!-- 8 Key Performance Indicator Cards -->
      <div class="kpi-grid">
        <!-- 1. Total Dispatches -->
        <div class="kpi-card blue" onclick="App.navigate('dispatch', 'dispatch')">
          <div class="kpi-top">
            <span class="kpi-title">Total Dispatches</span>
            <div class="kpi-icon-wrap blue">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
            </div>
          </div>
          <div class="kpi-value">${ERPState.data.dispatches.length} Notes</div>
          <div class="kpi-bottom">
            <span class="kpi-trend up">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
              Active Shipments
            </span>
            <span class="kpi-period">All Units</span>
          </div>
        </div>

        <!-- 2. Active Discount Vouchers -->
        <div class="kpi-card purple" onclick="App.navigate('qr', 'history')">
          <div class="kpi-top">
            <span class="kpi-title">Discount QR Vouchers</span>
            <div class="kpi-icon-wrap purple">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="M9 9h.01"/><path d="M15 15h.01"/></svg>
            </div>
          </div>
          <div class="kpi-value">${(ERPState.data.discountCoupons || []).length} Active</div>
          <div class="kpi-bottom">
            <span class="kpi-trend up">Instant Scan</span>
            <span class="kpi-period">Customer Loyalty</span>
          </div>
        </div>

        <!-- 3. Production Orders -->
        <div class="kpi-card amber" onclick="App.navigate('production', 'orders')">
          <div class="kpi-top">
            <span class="kpi-title">Production Orders</span>
            <div class="kpi-icon-wrap amber">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            </div>
          </div>
          <div class="kpi-value">${productionOrders.length} Orders</div>
          <div class="kpi-bottom">
            <span class="kpi-trend up">Live Queue</span>
            <span class="kpi-period">Floor Progress</span>
          </div>
        </div>

        <!-- 4. Ready for Dispatch -->
        <div class="kpi-card emerald" onclick="App.navigate('dispatch', 'ready')">
          <div class="kpi-top">
            <span class="kpi-title">Ready for Dispatch</span>
            <div class="kpi-icon-wrap emerald">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
            </div>
          </div>
          <div class="kpi-value">${stats.readyForDispatch} Orders</div>
          <div class="kpi-bottom">
            <span class="kpi-trend up">QC Passed</span>
            <span class="kpi-period">WH Unit 2</span>
          </div>
        </div>

        <!-- 5. Total Items Master -->
        <div class="kpi-card indigo" onclick="App.navigate('masters', 'items')">
          <div class="kpi-top">
            <span class="kpi-title">Items Master</span>
            <div class="kpi-icon-wrap blue">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
            </div>
          </div>
          <div class="kpi-value">${ERPState.data.items.length} Items</div>
          <div class="kpi-bottom">
            <span class="kpi-trend up">Fabrics & Qualities</span>
            <span class="kpi-period">Active Master</span>
          </div>
        </div>

        <!-- 6. Customers Master -->
        <div class="kpi-card rose" onclick="App.navigate('masters', 'customers')">
          <div class="kpi-top">
            <span class="kpi-title">Active Customers</span>
            <div class="kpi-icon-wrap rose">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
          </div>
          <div class="kpi-value">${ERPState.data.customers.length} Accounts</div>
          <div class="kpi-bottom">
            <span class="kpi-trend up">Verified Parties</span>
            <span class="kpi-period">Direct Clients</span>
          </div>
        </div>

        <!-- 7. Registered Vendors -->
        <div class="kpi-card amber" onclick="App.navigate('masters', 'vendors')">
          <div class="kpi-top">
            <span class="kpi-title">Registered Vendors</span>
            <div class="kpi-icon-wrap amber">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
            </div>
          </div>
          <div class="kpi-value">${ERPState.data.vendors.length} Vendors</div>
          <div class="kpi-bottom">
            <span class="kpi-trend up">Yarn & Mills</span>
            <span class="kpi-period">Active Supply</span>
          </div>
        </div>

        <!-- 8. Today's Dispatch -->
        <div class="kpi-card emerald" onclick="App.navigate('dispatch', 'dispatch')">
          <div class="kpi-top">
            <span class="kpi-title">Dispatch Volume</span>
            <div class="kpi-icon-wrap emerald">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
          </div>
          <div class="kpi-value">${ERPState.data.dispatches.reduce((acc, d) => acc + Number(d.quantity || 0), 0).toLocaleString('en-IN')} M</div>
          <div class="kpi-bottom">
            <span class="kpi-trend up">Outward Shipments</span>
            <span class="kpi-period">All Transports</span>
          </div>
        </div>
      </div>

      <!-- 9-Stage Visual Production Pipeline Workflow -->
      <div class="pipeline-section">
        <div class="pipeline-header">
          <div>
            <div class="pipeline-header-title">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
              Interactive Production Pipeline Tracking
            </div>
            <p style="font-size:0.775rem; color:var(--slate-500); margin-top:2px;">
              Live stage-by-stage quantity reconciliation for Order SO-2026-1045 (LOT-2026-00145)
            </p>
          </div>
          <button class="btn btn-secondary btn-sm" onclick="ProductionView.openLotDetailDrawer('LOT-2026-00145')">
            View Lot Timeline
          </button>
        </div>

        <div class="pipeline-track">
          <!-- Stage 1 -->
          <div class="pipeline-step active" onclick="DashboardView.showStageDetails('Order Booked', 5000)">
            <div class="pipeline-step-number">1</div>
            <div class="pipeline-step-name">Customer Order</div>
            <div class="pipeline-step-qty">5,000</div>
            <div class="pipeline-step-badge">100% Target</div>
          </div>

          <!-- Stage 2 -->
          <div class="pipeline-step" onclick="DashboardView.showStageDetails('Material Available', 5000)">
            <div class="pipeline-step-number">2</div>
            <div class="pipeline-step-name">Material Ready</div>
            <div class="pipeline-step-qty">5,000</div>
            <div class="pipeline-step-badge">WH-01 Store</div>
          </div>

          <!-- Stage 3 -->
          <div class="pipeline-step" onclick="DashboardView.showStageDetails('Cutting Completed', 5000)">
            <div class="pipeline-step-number">3</div>
            <div class="pipeline-step-name">Cutting</div>
            <div class="pipeline-step-qty">5,000</div>
            <div class="pipeline-step-badge">CNC Lay Done</div>
          </div>

          <!-- Stage 4 -->
          <div class="pipeline-step" onclick="DashboardView.showStageDetails('Stitching Done', 4850)">
            <div class="pipeline-step-number">4</div>
            <div class="pipeline-step-name">Stitching</div>
            <div class="pipeline-step-qty">4,850</div>
            <div class="pipeline-step-badge">Raj Stitching</div>
          </div>

          <!-- Stage 5 -->
          <div class="pipeline-step" onclick="DashboardView.showStageDetails('Printing Done', 4700)">
            <div class="pipeline-step-number">5</div>
            <div class="pipeline-step-name">Printing</div>
            <div class="pipeline-step-qty">4,700</div>
            <div class="pipeline-step-badge">ColorPrint</div>
          </div>

          <!-- Stage 6 -->
          <div class="pipeline-step" onclick="DashboardView.showStageDetails('Finishing Completed', 4650)">
            <div class="pipeline-step-number">6</div>
            <div class="pipeline-step-name">Finishing</div>
            <div class="pipeline-step-qty">4,650</div>
            <div class="pipeline-step-badge">Ironed & Trimmed</div>
          </div>

          <!-- Stage 7 -->
          <div class="pipeline-step" onclick="DashboardView.showStageDetails('Quality Check', 4600)">
            <div class="pipeline-step-number">7</div>
            <div class="pipeline-step-name">Quality Check</div>
            <div class="pipeline-step-qty">4,600</div>
            <div class="pipeline-step-badge">AQL 1.5 Passed</div>
          </div>

          <!-- Stage 8 -->
          <div class="pipeline-step" onclick="DashboardView.showStageDetails('Ready for Dispatch', 4550)">
            <div class="pipeline-step-number">8</div>
            <div class="pipeline-step-name">Finished Boxed</div>
            <div class="pipeline-step-qty">4,550</div>
            <div class="pipeline-step-badge">WH-02 Hub</div>
          </div>

          <!-- Stage 9 -->
          <div class="pipeline-step" onclick="DashboardView.showStageDetails('Dispatched Goods', 4200)">
            <div class="pipeline-step-number">9</div>
            <div class="pipeline-step-name">Dispatched</div>
            <div class="pipeline-step-qty">4,200</div>
            <div class="pipeline-step-badge">In Transit</div>
          </div>
        </div>
      </div>

      <!-- Charts Row -->
      <div class="charts-grid">
        <!-- Production Chart -->
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
                Production & Dispatch Overview
              </div>
              <div class="card-subtitle">Daily output vs target for the last 7 manufacturing shifts</div>
            </div>
            <span class="badge badge-primary">Units in Thousands</span>
          </div>
          <div id="dashboard-production-chart" class="chart-container"></div>
        </div>

        <!-- Order Status Donut Chart -->
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">Order Status</div>
              <div class="card-subtitle">Distribution by stage</div>
            </div>
          </div>
          <div id="dashboard-donut-chart" class="chart-container"></div>
        </div>
      </div>

      <!-- Bottom Row: Stock Overview & Activity Log -->
      <div style="display:grid; grid-template-columns:1.3fr 1fr; gap:20px;">
        <!-- Stock Overview -->
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg>
                Inventory Health & Job Work Status
              </div>
              <div class="card-subtitle">Category breakdown and job worker queue</div>
            </div>
            <button class="btn btn-secondary btn-sm" onclick="App.navigate('reports', 'stock')">Full Stock Report</button>
          </div>

          <div id="dashboard-stock-breakdown"></div>

          <!-- Job Work Summary Cards -->
          <div class="jobwork-status-grid">
            <div class="jw-status-card">
              <div class="jw-status-label">Assigned</div>
              <div class="jw-status-count" style="color:var(--primary-600);">8 Orders</div>
            </div>
            <div class="jw-status-card">
              <div class="jw-status-label">In Progress</div>
              <div class="jw-status-count" style="color:var(--warning-600);">10 Orders</div>
            </div>
            <div class="jw-status-card">
              <div class="jw-status-label">Partially Received</div>
              <div class="jw-status-count" style="color:var(--purple-600);">4 Orders</div>
            </div>
            <div class="jw-status-card">
              <div class="jw-status-label">Completed</div>
              <div class="jw-status-count" style="color:var(--success-600);">18 Orders</div>
            </div>
            <div class="jw-status-card">
              <div class="jw-status-label">Overdue</div>
              <div class="jw-status-count" style="color:var(--danger-600);">1 Order</div>
            </div>
          </div>
        </div>

        <!-- Recent Activity Feed -->
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
                Recent Activity
              </div>
              <div class="card-subtitle">Real-time manufacturing audit trail</div>
            </div>
            <button class="btn btn-ghost btn-sm" onclick="App.navigate('admin', 'activity')">View All</button>
          </div>

          <div class="timeline-list">
            ${activity.map(item => `
              <div class="timeline-item">
                <div class="timeline-icon blue">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/></svg>
                </div>
                <div class="timeline-content">
                  <div class="timeline-title">${item.action}</div>
                  <div class="timeline-desc"><strong>${item.user}</strong> • ${item.record}</div>
                  <div class="timeline-time">${item.time}</div>
                </div>
              </div>
            `).join('')}
          </div>
        </div>
      </div>
    `;
  },

  postRender() {
    ERPCharts.renderProductionChart("dashboard-production-chart");
    ERPCharts.renderOrderStatusDonut("dashboard-donut-chart");
    ERPCharts.renderStockOverview("dashboard-stock-breakdown");
  },

  switchDateFilter(btn, filterType) {
    document.querySelectorAll(".date-filter-btn").forEach(b => b.classList.remove("active"));
    btn.classList.add("active");
    UI.showToast("Filter Applied", `Dashboard metrics recalculated for: ${filterType.toUpperCase()}`, "info");
  },

  showStageDetails(stageName, qty) {
    const content = `
      <div style="display:flex; flex-direction:column; gap:16px;">
        <div style="padding:14px; background:var(--primary-50); border:1px solid var(--primary-200); border-radius:var(--radius-lg); display:flex; justify-content:space-between; align-items:center;">
          <div>
            <div style="font-size:0.75rem; font-weight:700; color:var(--primary-700); text-transform:uppercase;">Selected Stage</div>
            <div style="font-size:1.15rem; font-weight:800; color:var(--primary-900);">${stageName}</div>
          </div>
          <div style="text-align:right;">
            <div style="font-size:0.75rem; font-weight:700; color:var(--slate-500);">Current Quantity</div>
            <div style="font-size:1.35rem; font-weight:800; color:var(--slate-900); font-family:var(--font-mono);">${qty.toLocaleString('en-IN')} pcs</div>
          </div>
        </div>

        <p style="font-size:0.85rem; color:var(--slate-600);">
          This batch belongs to <strong>SO-2026-1045</strong> (ABC Fashion - Premium Cotton Crew Neck T-Shirt). All items in this stage are tracked with QR code <code>GARMENT-LOT-2026-000145</code>.
        </p>

        <div style="display:flex; gap:10px; justify-content:flex-end;">
          <button class="btn btn-secondary btn-sm" onclick="UI.closeModal(); QRManager.openPrintLabelModal('LOT-2026-00145');">Print QR Tag</button>
          <button class="btn btn-primary btn-sm" onclick="UI.closeModal(); App.navigate('production', 'tracking');">Open Lot Tracker</button>
        </div>
      </div>
    `;

    UI.openModal({ title: `Production Stage Inspection: ${stageName}`, content, size: "modal-md" });
  }
};
