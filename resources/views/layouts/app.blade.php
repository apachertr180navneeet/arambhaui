<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'GarmentERP - Manufacturing & Supply Chain Management')</title>
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
            <span class="brand-badge">PRO</span>
          </div>
          <div class="brand-subtitle">FashionWorks Pvt. Ltd.</div>
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
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="m9 12 2 2 4-4"/></svg>
                <span class="nav-title">Job Work & Assign</span>
              </div>
              <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <div class="nav-submenu">
              <a class="submenu-link" data-route="jobwork/assign" data-module="jobwork" data-submodule="assign">Job Assign Orders</a>
              <a class="submenu-link" data-route="jobwork/inward-report" data-module="jobwork" data-submodule="inward-report">Inward & Ready Report</a>
            </div>
          </div>
        </div>

        <!-- 5. PRODUCTION MANAGEMENT -->
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

        <!-- 6. QR MANAGEMENT -->
        <div class="nav-section">
          <div class="nav-section-title">QR & Barcode</div>
          <div class="nav-item nav-group">
            <a class="nav-link nav-group-toggle" data-module="qr">
              <div class="nav-link-content">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path d="M12 3h.01"/><path d="M12 16v.01"/><path d="M16 12h1"/><path d="M21 12v.01"/><path d="M12 21v-1"/></svg>
                <span class="nav-title">QR Management</span>
              </div>
              <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <div class="nav-submenu">
              <a class="submenu-link" data-route="qr/generator" data-module="qr" data-submodule="generator">Discount QR Generator</a>
              <a class="submenu-link" data-route="qr/scanner" data-module="qr" data-submodule="scanner">Customer Scanner</a>
              <a class="submenu-link" data-route="qr/history" data-module="qr" data-submodule="history">Active Vouchers Library</a>
            </div>
          </div>
        </div>

        <!-- 7. DISPATCH MANAGEMENT -->
        <div class="nav-section">
          <div class="nav-section-title">Logistics & Dispatch</div>
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
          <div class="nav-section-title">Billing & Sales</div>
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
              <a class="submenu-link" data-route="invoices/create" data-module="invoices" data-submodule="create">Create Sales Invoice</a>
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
          <div class="nav-section-title">System</div>
          <div class="nav-item nav-group">
            <a class="nav-link nav-group-toggle" data-module="admin">
              <div class="nav-link-content">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                <span class="nav-title">Administration</span>
              </div>
              <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <div class="nav-submenu">
              <a class="submenu-link" data-route="admin/users" data-module="admin" data-submodule="users">User Management</a>
              <a class="submenu-link" data-route="admin/roles" data-module="admin" data-submodule="roles">Roles & Permissions</a>
              <a class="submenu-link" data-route="admin/activity" data-module="admin" data-submodule="activity">Audit Logs</a>
              <a class="submenu-link" data-route="admin/settings" data-module="admin" data-submodule="settings">Company Settings</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar Footer (System Status & Quick Session Info) -->
      <div class="sidebar-footer">
        <div class="system-status">
          <span class="status-indicator online"></span>
          <span class="status-label">ERP Production Node (Laravel / MySQL)</span>
        </div>
        <div style="font-size:0.75rem; color:var(--slate-400); margin-top:4px;">
          Logged in as: <strong style="color:var(--slate-200);">{{ Auth::user()->name }}</strong> ({{ ucfirst(Auth::user()->role) }})
        </div>
      </div>
    </aside>

    <!-- ==========================================
         MAIN CONTENT AREA & TOP HEADER NAVBAR
         ========================================== -->
    <div class="app-main-wrapper">
      <header class="app-header">
        <div class="header-left">
          <!-- Mobile Sidebar Toggle -->
          <button class="header-icon-btn mobile-menu-toggle" id="sidebar-toggle-btn" title="Toggle Navigation">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
          </button>

          <!-- Dynamic Breadcrumb Navigation -->
          <nav class="header-breadcrumb" id="breadcrumb-container" aria-label="Breadcrumb">
            <div class="breadcrumb-item">
              <span>Dashboard</span>
            </div>
          </nav>
        </div>

        <div class="header-right">
          <!-- Global Command / Search Trigger -->
          <div class="global-search-wrapper">
            <div class="search-input-box">
              <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              <input type="text" id="global-search-input" placeholder="Quick Search orders, lots, invoices (Ctrl+K)..." autocomplete="off">
              <kbd class="search-kbd">⌘K</kbd>
            </div>
            <!-- Live Search Suggestions Container -->
            <div id="global-search-results" class="search-dropdown-menu" style="display:none;"></div>
          </div>

          <!-- Quick Action Dropdown Trigger -->
          <div class="header-action-group">
            <button class="btn btn-outline btn-sm" id="btn-quick-create" title="Quick Actions">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              <span>Quick Create</span>
            </button>
          </div>

          <!-- Notification Center Popover Trigger -->
          <div class="header-icon-group">
            <button class="header-icon-btn" id="btn-notifications" title="Notifications">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
              <span class="badge-count" id="header-notif-badge">3</span>
            </button>
          </div>

          <!-- User Profile & Session Logout Form -->
          <div class="user-profile-menu">
            <div class="avatar-badge">{{ substr(Auth::user()->name, 0, 1) }}</div>
            <div class="user-meta hide-mobile">
              <span class="user-name">{{ Auth::user()->name }}</span>
              <span class="user-role">{{ ucfirst(Auth::user()->role) }}</span>
            </div>

            <!-- Logout Form with CSRF Protection -->
            <form method="POST" action="{{ route('logout') }}" style="display:inline; margin-left: 8px;">
              @csrf
              <button type="submit" class="btn btn-outline btn-sm" title="Sign Out" style="padding: 6px 10px; font-size: 0.75rem; color: #ef4444; border-color: #fecaca; background: #fff5f5; cursor: pointer;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                <span>Logout</span>
              </button>
            </form>
          </div>
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
