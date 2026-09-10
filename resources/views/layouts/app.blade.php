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

  <!-- jsQR library for browser camera/file QR decoding -->
  <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>

  <!-- SweetAlert2 library -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        <a href="{{ route('dashboard') }}" style="display:flex; align-items:center; gap:12px; text-decoration:none;">
          <div class="brand-logo-icon">G</div>
          <div class="brand-text">
            <div class="brand-title">
              GarmentERP
              <span class="brand-badge">SaaS</span>
            </div>
            <div class="brand-subtitle">Manufacturing Management</div>
          </div>
        </a>
        <!-- Mobile Sidebar Close Button -->
        <button class="sidebar-close-btn" id="sidebar-close-btn" title="Close Sidebar" aria-label="Close Sidebar">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>

      <!-- Navigation Tree -->
      <div class="sidebar-nav-container">
        <!-- 1. DASHBOARD -->
        <div class="nav-section">
          <div class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard*') && !request()->is('dashboard/*') ? 'active' : '' }}">
              <div class="nav-link-content">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                <span class="nav-title">Dashboard</span>
              </div>
            </a>
          </div>
        </div>

        <!-- 2. CORE MASTERS MANAGEMENT -->
        <div class="nav-section">
          <div class="nav-section-title">Core Masters</div>
          <div class="nav-item nav-group {{ request()->is('masters*') ? 'expanded' : '' }}">
            <a href="javascript:void(0)" class="nav-link nav-group-toggle {{ request()->is('masters*') ? 'active' : '' }}">
              <div class="nav-link-content">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h14a2 2 0 0 0 2-2V7.5L14.5 2H6a2 2 0 0 0-2 2v4"/><polyline points="14 2 14 8 20 8"/><path d="M2 15h10"/><path d="m9 18 3-3-3-3"/></svg>
                <span class="nav-title">Masters Management</span>
              </div>
              <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <div class="nav-submenu">
              <a href="{{ route('masters.customers.index') }}" class="submenu-link {{ request()->routeIs('masters.customers.*') ? 'active' : '' }}">Customer Master</a>
              <a href="{{ route('masters.vendors.index') }}" class="submenu-link {{ request()->routeIs('masters.vendors.*') ? 'active' : '' }}">Vendor Master</a>
              <a href="{{ route('masters.jobworkers.index') }}" class="submenu-link {{ request()->routeIs('masters.jobworkers.*') ? 'active' : '' }}">Job Worker Master</a>
              <a href="{{ route('masters.items.index') }}" class="submenu-link {{ request()->routeIs('masters.items.*') ? 'active' : '' }}">Item Master</a>
              <a href="{{ route('masters.units.index') }}" class="submenu-link {{ request()->routeIs('masters.units.*') ? 'active' : '' }}">Unit Master</a>
            </div>
          </div>
        </div>

        <!-- 3. QR CODE MANAGEMENT -->
        <div class="nav-section">
          <div class="nav-section-title">Barcodes & QR</div>
          <div class="nav-item nav-group {{ request()->is('qr*') ? 'expanded' : '' }}">
            <a href="javascript:void(0)" class="nav-link nav-group-toggle {{ request()->is('qr*') ? 'active' : '' }}">
              <div class="nav-link-content">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path d="M12 3h.01"/><path d="M12 16v.01"/><path d="M16 12h1"/><path d="M21 12v.01"/><path d="M12 21v-1"/></svg>
                <span class="nav-title">QR Management</span>
              </div>
              <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <div class="nav-submenu">
              <a href="{{ route('qr.history') }}" class="submenu-link {{ request()->routeIs('qr.history') ? 'active' : '' }}">Admin QR & Expiry Ledger</a>
              <a href="{{ route('qr.generator') }}" class="submenu-link {{ request()->routeIs('qr.generator') ? 'active' : '' }}">+ Generate Single-Use QR</a>
            </div>
          </div>
        </div>

        <!-- 4. PURCHASE MANAGEMENT -->
        <div class="nav-section">
          <div class="nav-section-title">Purchasing</div>
          <div class="nav-item nav-group {{ request()->is('purchase*') ? 'expanded' : '' }}">
            <a href="javascript:void(0)" class="nav-link nav-group-toggle {{ request()->is('purchase*') ? 'active' : '' }}">
              <div class="nav-link-content">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                <span class="nav-title">Purchase Mgmt</span>
              </div>
              <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <div class="nav-submenu">
              <a href="{{ route('purchase.orders.index') }}" class="submenu-link {{ request()->routeIs('purchase.orders.index') ? 'active' : '' }}">Purchase Orders (PO)</a>
              <a href="{{ route('purchase.orders.create') }}" class="submenu-link {{ request()->routeIs('purchase.orders.create') ? 'active' : '' }}">+ Create New PO</a>
            </div>
          </div>
        </div>

        <!-- 5. JOB WORK & ASSIGN -->
        <div class="nav-section">
          <div class="nav-section-title">Job Assignment</div>
          <div class="nav-item nav-group {{ request()->is('jobwork*') ? 'expanded' : '' }}">
            <a href="javascript:void(0)" class="nav-link nav-group-toggle {{ request()->is('jobwork*') ? 'active' : '' }}">
              <div class="nav-link-content">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                <span class="nav-title">Job Work & Assign</span>
              </div>
              <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <div class="nav-submenu">
              <a href="{{ route('jobwork.assign.index') }}" class="submenu-link {{ request()->routeIs('jobwork.assign.*') ? 'active' : '' }}">Job Assign Orders</a>
              <a href="{{ route('jobwork.inward-report') }}" class="submenu-link {{ request()->routeIs('jobwork.inward-report') ? 'active' : '' }}">Job Inward & Ready Report</a>
            </div>
          </div>
        </div>

        <!-- 6. DISPATCH MANAGEMENT -->
        <div class="nav-section">
          <div class="nav-section-title">Shipping</div>
          <div class="nav-item nav-group {{ request()->is('dispatch*') ? 'expanded' : '' }}">
            <a href="javascript:void(0)" class="nav-link nav-group-toggle {{ request()->is('dispatch*') ? 'active' : '' }}">
              <div class="nav-link-content">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
                <span class="nav-title">Dispatch Management</span>
              </div>
              <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <div class="nav-submenu">
              <a href="{{ route('dispatch.ready') }}" class="submenu-link {{ request()->routeIs('dispatch.ready') ? 'active' : '' }}">Ready for Dispatch</a>
              <a href="{{ route('dispatch.dispatch') }}" class="submenu-link {{ request()->routeIs('dispatch.dispatch') || request()->routeIs('dispatch.challans.*') ? 'active' : '' }}">Dispatch Challans</a>
            </div>
          </div>
        </div>

        <!-- 7. ACCOUNTS & SETTLEMENTS -->
        <div class="nav-section">
          <div class="nav-section-title">Financials</div>
          <div class="nav-item nav-group {{ request()->is('accounts*') ? 'expanded' : '' }}">
            <a href="javascript:void(0)" class="nav-link nav-group-toggle {{ request()->is('accounts*') ? 'active' : '' }}">
              <div class="nav-link-content">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                <span class="nav-title">Accounts & Settlements</span>
              </div>
              <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <div class="nav-submenu">
              <a href="{{ route('accounts.customer-accounts') }}" class="submenu-link {{ request()->routeIs('accounts.customer-accounts') ? 'active' : '' }}">Customer Settlements</a>
              <a href="{{ route('accounts.customer-outstanding') }}" class="submenu-link {{ request()->routeIs('accounts.customer-outstanding') ? 'active' : '' }}">Customer Outstanding</a>
              <a href="{{ route('accounts.vendor-outstanding') }}" class="submenu-link {{ request()->routeIs('accounts.vendor-outstanding') ? 'active' : '' }}">Vendor Outstanding</a>
              <a href="{{ route('accounts.jobworker-outstanding') }}" class="submenu-link {{ request()->routeIs('accounts.jobworker-outstanding') ? 'active' : '' }}">Job Worker Outstanding</a>
            </div>
          </div>
        </div>

        <!-- 8. REPORTS HUB -->
        <div class="nav-section">
          <div class="nav-section-title">Analytics</div>
          <div class="nav-item nav-group {{ request()->is('reports*') ? 'expanded' : '' }}">
            <a href="javascript:void(0)" class="nav-link nav-group-toggle {{ request()->is('reports*') ? 'active' : '' }}">
              <div class="nav-link-content">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
                <span class="nav-title">Reports Hub</span>
              </div>
              <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <div class="nav-submenu">
              <a href="{{ route('reports.ledger') }}" class="submenu-link {{ request()->routeIs('reports.ledger') ? 'active' : '' }}">Item Stock Ledger</a>
              <a href="{{ route('reports.stock') }}" class="submenu-link {{ request()->routeIs('reports.stock') ? 'active' : '' }}">Stock Report</a>
              <a href="{{ route('reports.lot-purchase') }}" class="submenu-link {{ request()->routeIs('reports.lot-purchase') ? 'active' : '' }}">Lot-Wise Purchase</a>
              <a href="{{ route('reports.lot-sales') }}" class="submenu-link {{ request()->routeIs('reports.lot-sales') ? 'active' : '' }}">Lot-Wise Sales</a>
            </div>
          </div>
        </div>

        <!-- 9. ADMINISTRATION -->
        <div class="nav-section">
          <div class="nav-section-title">Administration</div>
          <div class="nav-item nav-group {{ request()->is('admin*') ? 'expanded' : '' }}">
            <a href="javascript:void(0)" class="nav-link nav-group-toggle {{ request()->is('admin*') ? 'active' : '' }}">
              <div class="nav-link-content">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                <span class="nav-title">Administration</span>
              </div>
              <svg class="nav-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <div class="nav-submenu">
              <a href="{{ route('admin.users.index') }}" class="submenu-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">User Accounts</a>
              <a href="{{ route('admin.roles') }}" class="submenu-link {{ request()->routeIs('admin.roles') ? 'active' : '' }}">Roles & Permissions</a>
              <a href="{{ route('admin.activity') }}" class="submenu-link {{ request()->routeIs('admin.activity') ? 'active' : '' }}">Activity Audit Logs</a>
              <a href="{{ route('admin.settings') }}" class="submenu-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">Company Settings</a>
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

    <!-- Sidebar Backdrop for Mobile Overlay -->
    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

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
            @yield('breadcrumb')
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
              <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
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
                  <a href="{{ route('jobwork.assign.index') }}" class="notification-item unread" style="text-decoration:none; color:inherit;">
                    <div class="notification-icon-wrap info">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div class="notification-content">
                      <div class="notification-title">Job Work JW-1023 received</div>
                      <div class="notification-desc">4,850 good pcs inward from Raj Stitching</div>
                      <div class="notification-time">15 mins ago</div>
                    </div>
                  </a>

                  <a href="{{ route('masters.items.index') }}" class="notification-item unread" style="text-decoration:none; color:inherit;">
                    <div class="notification-icon-wrap warning">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </div>
                    <div class="notification-content">
                      <div class="notification-title">Low Stock Alert</div>
                      <div class="notification-desc">Polyester Pearl Buttons 18L is below reorder level</div>
                      <div class="notification-time">1 hour ago</div>
                    </div>
                  </a>

                  <a href="{{ route('dispatch.ready') }}" class="notification-item unread" style="text-decoration:none; color:inherit;">
                    <div class="notification-icon-wrap success">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/></svg>
                    </div>
                    <div class="notification-content">
                      <div class="notification-title">Order SO-2045 ready for dispatch</div>
                      <div class="notification-desc">4,550 pcs passed QC inspection</div>
                      <div class="notification-time">3 hours ago</div>
                    </div>
                  </a>
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
          <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:inline; margin-left: 6px;">
            @csrf
            <button type="submit" class="icon-btn" title="Sign Out" aria-label="Sign Out" style="color:var(--danger-600); background: #fef2f2; border:1px solid #fee2e2; cursor:pointer;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            </button>
          </form>
        </div>
      </header>

      <!-- Main Server-Rendered Blade Content Container -->
      <main class="main-content" id="main-content-render">
        @if (session('success'))
          <div style="margin-bottom: 20px; padding: 14px 18px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 10px; color: #065f46; display: flex; align-items: center; gap: 10px; font-size: 0.9rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span style="font-weight:600;">{{ session('success') }}</span>
          </div>
        @endif

        @if (session('error'))
          <div style="margin-bottom: 20px; padding: 14px 18px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; color: #991b1b; display: flex; align-items: center; gap: 10px; font-size: 0.9rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span style="font-weight:600;">{{ session('error') }}</span>
          </div>
        @endif

        @if ($errors->any())
          <div style="margin-bottom: 20px; padding: 14px 18px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; color: #92400e; font-size: 0.875rem;">
            <div style="font-weight:700; margin-bottom:6px;">Please correct the following errors:</div>
            <ul style="margin:0; padding-left:20px;">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        @yield('content')
      </main>
    </div>
  </div>

  @if(Auth::check())
  <script>
    window.CURRENT_AUTH_USER = {
      id: {{ Auth::id() }},
      name: {!! json_encode(Auth::user()->name) !!},
      email: {!! json_encode(Auth::user()->email) !!},
      role: {!! json_encode(Auth::user()->role ?? 'Administrator') !!},
      avatar: {!! json_encode(strtoupper(substr(Auth::user()->name ?? 'AU', 0, 2))) !!},
      status: {!! json_encode(Auth::user()->status ?? 'active') !!}
    };
  </script>
  @endif

  <!-- Core ERP Client Utilities (Modals, Toasts, Table Filters, CSV Export, Print, QR Helpers) -->
  <script src="{{ asset('js/data.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('js/state.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('js/components.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('js/charts.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('js/qr.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('js/blade-shell.js') }}?v={{ time() }}"></script>

  <script>
    // Global SweetAlert Toast Configuration
    window.Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
      }
    });

    @if (session('success'))
      Toast.fire({
        icon: 'success',
        title: {!! json_encode(session('success')) !!}
      });
    @endif

    @if (session('error'))
      Toast.fire({
        icon: 'error',
        title: {!! json_encode(session('error')) !!}
      });
    @endif
  </script>

  @stack('scripts')
</body>
</html>
