<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'GarmentERP - Manufacturing & Supply Chain Management | FashionWorks Pvt. Ltd.')</title>
  <meta name="description" content="Production-ready Garment Manufacturing ERP for customer orders, raw materials, job worker outward/inward, QR lot tracking, quality check, finished goods dispatch, and accounts settlement.">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Application Stylesheets -->
  <link rel="stylesheet" href="{{ asset('css/main.css') }}">
  <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
  <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
  <link rel="stylesheet" href="{{ asset('css/tables.css') }}">
  <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
  <link rel="stylesheet" href="{{ asset('css/qr.css') }}">
  <link rel="stylesheet" href="{{ asset('css/print.css') }}">

  @stack('styles')
</head>
<body>

  <div id="app">
    <!-- ==========================================
         LEFT SIDEBAR NAVIGATION (ENTERPRISE SLATE)
         ========================================== -->
    <aside class="app-sidebar">
      <!-- Sidebar Header & Brand -->
      <div class="sidebar-header">
        <div class="brand-logo-icon">G</div>
        <div class="brand-text">
          <div class="brand-title">
            GarmentERP
            <span class="brand-badge">SaaS</span>
          </div>
          <div class="brand-subtitle">Manufacturing Management</div>
        </div>
      </div>

      <!-- Navigation Tree -->
      <div class="sidebar-nav-container">
        <!-- 1. DASHBOARD -->
        <div class="nav-section">
          <div class="nav-item">
            <a class="nav-link active" data-route="dashboard/overview" data-module="dashboard">
              <div class="nav-link-content">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                <span class="nav-title">Dashboard</span>
              </div>
            </a>
          </div>
        </div>

        <!-- 2. MASTERS MANAGEMENT -->
        <div class="nav-section">
          <div class="nav-section-title">Core Masters</div>
          <div class="nav-item nav-group">
            <a class="nav-link nav-group-toggle" data-module="masters">
              <div class="nav-link-content">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h14a2 2 0 0 0 2-2V7.5L14.5 2H6a2 2 0 0 0-2 2v4"/><polyline points="14 2 14 8 20 8"/><path d="M2 15h10"/><path d="m9 18 3-3-3-3"/></svg>
                <span class="nav-title">Masters Management</span>
              </div>
              <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <div class="nav-submenu">
              <a class="submenu-link" data-route="masters/customers" data-module="masters" data-submodule="customers">Customer Master</a>
              <a class="submenu-link" data-route="masters/vendors" data-module="masters" data-submodule="vendors">Vendor Master</a>
              <a class="submenu-link" data-route="masters/jobworkers" data-module="masters" data-submodule="jobworkers">Job Worker Master</a>
              <a class="submenu-link" data-route="masters/items" data-module="masters" data-submodule="items">Item Master</a>
              <a class="submenu-link" data-route="masters/sizes" data-module="masters" data-submodule="sizes">Size & Color Master</a>
            </div>
          </div>
        </div>

        <!-- 3. PURCHASE MANAGEMENT -->
        <div class="nav-section">
          <div class="nav-section-title">Procurement</div>
          <div class="nav-item nav-group">
            <a class="nav-link nav-group-toggle" data-module="purchase">
              <div class="nav-link-content">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                <span class="nav-title">Purchase Management</span>
              </div>
              <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <div class="nav-submenu">
              <a class="submenu-link" data-route="purchase/orders" data-module="purchase" data-submodule="orders">Purchase Orders</a>
              <a class="submenu-link" data-route="purchase/inward" data-module="purchase" data-submodule="inward">Purchase Inward (GRN)</a>
            </div>
          </div>
        </div>

        <!-- 4. JOB WORK & ASSIGN -->
        <div class="nav-section">
          <div class="nav-section-title">Job Assignment</div>
          <div class="nav-item nav-group">
            <a class="nav-link nav-group-toggle" data-module="jobwork">
              <div class="nav-link-content">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                <span class="nav-title">Job Work & Assign</span>
              </div>
              <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <div class="nav-submenu">
              <a class="submenu-link" data-route="jobwork/assign" data-module="jobwork" data-submodule="assign">Job Assign Orders</a>
              <a class="submenu-link" data-route="jobwork/inward-report" data-module="jobwork" data-submodule="inward-report">Job Inward & Ready Report</a>
            </div>
          </div>
        </div>

        <!-- 5. MANUFACTURING & PRODUCTION -->
        <div class="nav-section">
          <div class="nav-section-title">Manufacturing</div>
          <div class="nav-item nav-group">
            <a class="nav-link nav-group-toggle" data-module="production">
              <div class="nav-link-content">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                <span class="nav-title">Production Mgmt</span>
              </div>
              <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <div class="nav-submenu">
              <a class="submenu-link" data-route="production/orders" data-module="production" data-submodule="orders">Customer Orders</a>
              <a class="submenu-link" data-route="production/jobwork" data-module="production" data-submodule="jobwork">Job Worker Outward/Inward</a>
              <a class="submenu-link" data-route="production/qc" data-module="production" data-submodule="qc">Quality Check (QC)</a>
              <a class="submenu-link" data-route="production/tracking" data-module="production" data-submodule="tracking">Lot Tracking</a>
            </div>
          </div>
        </div>

        <!-- 6. QR CODE MANAGEMENT -->
        <div class="nav-section">
          <div class="nav-section-title">Barcodes & QR</div>
          <div class="nav-item nav-group">
            <a class="nav-link nav-group-toggle" data-module="qr">
              <div class="nav-link-content">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path d="M12 3h.01"/><path d="M12 16v.01"/><path d="M16 12h1"/><path d="M21 12v.01"/><path d="M12 21v-1"/></svg>
                <span class="nav-title">QR Management</span>
              </div>
              <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <div class="nav-submenu">
              <a class="submenu-link" data-route="qr/scanner" data-module="qr" data-submodule="scanner">Customer Claim & Upload Portal</a>
              <a class="submenu-link" data-route="qr/history" data-module="qr" data-submodule="history">Admin QR & Expiry Ledger</a>
              <a class="submenu-link" data-route="qr/generator" data-module="qr" data-submodule="generator">+ Generate Single-Use QR</a>
            </div>
          </div>
        </div>

        <!-- 7. DISPATCH MANAGEMENT -->
        <div class="nav-section">
          <div class="nav-section-title">Shipping</div>
          <div class="nav-item nav-group">
            <a class="nav-link nav-group-toggle" data-module="dispatch">
              <div class="nav-link-content">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
                <span class="nav-title">Dispatch Management</span>
              </div>
              <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <div class="nav-submenu">
              <a class="submenu-link" data-route="dispatch/ready" data-module="dispatch" data-submodule="ready">Ready for Dispatch</a>
              <a class="submenu-link" data-route="dispatch/dispatch" data-module="dispatch" data-submodule="dispatch">Dispatch Challans</a>
            </div>
          </div>
        </div>

        <!-- 8. SALES INVOICES -->
        <div class="nav-section">
          <div class="nav-section-title">Sales & Billing</div>
          <div class="nav-item nav-group">
            <a class="nav-link nav-group-toggle" data-module="invoices">
              <div class="nav-link-content">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                <span class="nav-title">Sales Invoices</span>
              </div>
              <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <div class="nav-submenu">
              <a class="submenu-link" data-route="invoices/list" data-module="invoices" data-submodule="list">Customer Invoices</a>
              <a class="submenu-link" data-route="invoices/create" data-module="invoices" data-submodule="create">+ Create Sales Invoice</a>
            </div>
          </div>
        </div>

        <!-- 9. ACCOUNTS & SETTLEMENTS -->
        <div class="nav-section">
          <div class="nav-section-title">Financials</div>
          <div class="nav-item nav-group">
            <a class="nav-link nav-group-toggle" data-module="accounts">
              <div class="nav-link-content">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                <span class="nav-title">Accounts & Settlements</span>
              </div>
              <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <div class="nav-submenu">
              <a class="submenu-link" data-route="accounts/customer-accounts" data-module="accounts" data-submodule="customer-accounts">Customer Settlements</a>
              <a class="submenu-link" data-route="accounts/customer-outstanding" data-module="accounts" data-submodule="customer-outstanding">Customer Outstanding</a>
              <a class="submenu-link" data-route="accounts/vendor-outstanding" data-module="accounts" data-submodule="vendor-outstanding">Vendor Outstanding</a>
              <a class="submenu-link" data-route="accounts/jobworker-outstanding" data-module="accounts" data-submodule="jobworker-outstanding">Job Worker Outstanding</a>
            </div>
          </div>
        </div>

        <!-- 10. REPORTS HUB -->
        <div class="nav-section">
          <div class="nav-section-title">Analytics</div>
          <div class="nav-item nav-group">
            <a class="nav-link nav-group-toggle" data-module="reports">
              <div class="nav-link-content">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
                <span class="nav-title">Reports Hub</span>
              </div>
              <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <div class="nav-submenu">
              <a class="submenu-link" data-route="reports/ledger" data-module="reports" data-submodule="ledger">Item Stock Ledger</a>
              <a class="submenu-link" data-route="reports/stock" data-module="reports" data-submodule="stock">Stock Report</a>
              <a class="submenu-link" data-route="reports/lot-purchase" data-module="reports" data-submodule="lot-purchase">Lot-Wise Purchase</a>
              <a class="submenu-link" data-route="reports/lot-sales" data-module="reports" data-submodule="lot-sales">Lot-Wise Sales</a>
            </div>
          </div>
        </div>

        <!-- 11. ADMINISTRATION -->
        <div class="nav-section">
          <div class="nav-section-title">Administration</div>
          <div class="nav-item nav-group">
            <a class="nav-link nav-group-toggle" data-module="admin">
              <div class="nav-link-content">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                <span class="nav-title">Administration</span>
              </div>
              <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <div class="nav-submenu">
              <a class="submenu-link" data-route="admin/users" data-module="admin" data-submodule="users">User Accounts</a>
              <a class="submenu-link" data-route="admin/roles" data-module="admin" data-submodule="roles">Roles & Permissions</a>
              <a class="submenu-link" data-route="admin/activity" data-module="admin" data-submodule="activity">Activity Audit Logs</a>
              <a class="submenu-link" data-route="admin/settings" data-module="admin" data-submodule="settings">Company Settings</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar Status Footer -->
      <div class="sidebar-footer">
        <div class="system-status-pill">
          <div class="status-indicator-dot"></div>
          <span>FashionWorks Cloud Active</span>
        </div>
      </div>
    </aside>

    <!-- ==========================================
         MAIN VIEWPORT WRAPPER
         ========================================== -->
    <div class="main-wrapper">
      <!-- Sticky Top Header -->
      <header class="app-header">
        <div class="header-left">
          <button class="sidebar-toggle-btn" id="sidebar-toggle" title="Toggle Sidebar">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
          </button>
          <div class="breadcrumb-nav" id="breadcrumb-container">
            <div class="breadcrumb-item"><span>Dashboard</span></div>
          </div>
        </div>

        <!-- Global Quick Search -->
        <div class="header-center">
          <div class="global-search-wrapper">
            <svg class="global-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" class="global-search-input" id="global-search-input" placeholder="Search orders, lots, job work, items, customers...">
            <span class="search-shortcut-badge">Ctrl+K</span>
          </div>
        </div>

        <!-- Header Right Profile & Alerts -->
        <div class="header-right">
          <div class="header-company-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg>
            FashionWorks Pvt. Ltd.
          </div>

          <div class="header-actions">
            <!-- Help Button -->
            <button class="icon-btn" onclick="App.openHelpModal()" title="Help & Workflow Guide">
              <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="12 17h.01"/></svg>
            </button>

            <!-- Notifications Bell -->
            <div style="position:relative;">
              <button class="icon-btn" id="notification-bell-btn" title="Notifications">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                <div class="notification-badge-dot"></div>
              </button>

              <!-- Notifications Dropdown Menu -->
              <div class="dropdown-menu" id="notification-dropdown-menu">
                <div class="dropdown-header">
                  <h4>Notifications</h4>
                  <span class="badge badge-primary">3 New</span>
                </div>
                <div class="dropdown-list">
                  <div class="notification-item unread" onclick="App.navigate('production', 'jobwork')">
                    <div class="notification-icon-wrap info">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div class="notification-content">
                      <div class="notification-title">Job Work JW-1023 received</div>
                      <div class="notification-desc">4,850 good pcs inward from Raj Stitching</div>
                      <div class="notification-time">15 mins ago</div>
                    </div>
                  </div>

                  <div class="notification-item unread" onclick="App.navigate('masters', 'items')">
                    <div class="notification-icon-wrap warning">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </div>
                    <div class="notification-content">
                      <div class="notification-title">Low Stock Alert</div>
                      <div class="notification-desc">Polyester Pearl Buttons 18L is below reorder level</div>
                      <div class="notification-time">1 hour ago</div>
                    </div>
                  </div>

                  <div class="notification-item unread" onclick="App.navigate('dispatch', 'ready')">
                    <div class="notification-icon-wrap success">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/></svg>
                    </div>
                    <div class="notification-content">
                      <div class="notification-title">Order SO-2045 ready for dispatch</div>
                      <div class="notification-desc">4,550 pcs passed QC inspection</div>
                      <div class="notification-time">3 hours ago</div>
                    </div>
                  </div>
                </div>
                <div class="dropdown-footer">
                  <a href="javascript:void(0)" onclick="UI.showToast('All Caught Up', 'All notifications marked as read', 'success')">Mark all as read</a>
                </div>
              </div>
            </div>
          </div>

          <!-- User Profile Dropdown Trigger -->
          <div class="user-profile-btn" onclick="App.openUserModal()">
            <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'AU', 0, 2)) }}</div>
            <div class="user-info">
              <div class="user-name">{{ Auth::user()->name ?? 'Admin User' }}</div>
              <div class="user-role">{{ ucfirst(Auth::user()->role ?? 'Administrator') }}</div>
            </div>
          </div>

          <!-- Logout Form with CSRF Protection -->
          <form method="POST" action="{{ route('logout') }}" style="display:inline; margin-left: 6px;">
            @csrf
            <button type="submit" class="icon-btn" title="Sign Out" style="color:var(--danger-600); background: #fef2f2; border:1px solid #fee2e2;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            </button>
          </form>
        </div>
      </header>

      <!-- Dynamic Content Injection Container -->
      <main class="main-content" id="main-content-render">
        @yield('content')
      </main>
    </div>
  </div>

  <!-- Application Logic Scripts -->
  <script src="{{ asset('js/data.js') }}"></script>
  <script src="{{ asset('js/state.js') }}"></script>
  <script src="{{ asset('js/components.js') }}"></script>
  <script src="{{ asset('js/charts.js') }}"></script>
  <script src="{{ asset('js/qr.js') }}"></script>
  <script src="{{ asset('js/views/dashboard.js') }}"></script>
  <script src="{{ asset('js/views/masters.js') }}"></script>
  <script src="{{ asset('js/views/purchase.js') }}"></script>
  <script src="{{ asset('js/views/jobwork-view.js') }}"></script>
  <script src="{{ asset('js/views/production.js') }}"></script>
  <script src="{{ asset('js/views/qr-view.js') }}"></script>
  <script src="{{ asset('js/views/dispatch.js') }}"></script>
  <script src="{{ asset('js/views/invoices.js') }}"></script>
  <script src="{{ asset('js/views/accounts.js') }}"></script>
  <script src="{{ asset('js/views/reports.js') }}"></script>
  <script src="{{ asset('js/views/admin.js') }}"></script>
  <script src="{{ asset('js/app.js') }}"></script>

  <script>
    window.INITIAL_ROUTE = {
      module: "{{ $module ?? '' }}",
      submodule: "{{ $submodule ?? '' }}"
    };

    // Initialize Application on DOM Ready
    document.addEventListener("DOMContentLoaded", () => {
      App.init();
    });
  </script>

  @stack('scripts')
</body>
</html>
