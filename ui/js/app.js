/* ==========================================================================
   APP ROUTER, GLOBAL SEARCH, NOTIFICATIONS & SHELL COORDINATOR
   GarmentERP - FashionWorks Pvt. Ltd.
   ========================================================================== */

const App = {
  currentModule: "dashboard",
  currentSubmodule: "overview",
  isLoggedIn: true,

  init() {
    this.setupEventListeners();
    this.setupGlobalSearch();
    this.setupShortcuts();

    // Subscribe to state updates to refresh active view automatically
    ERPState.subscribe(() => {
      this.updateHeaderBadges();
    });

    // Initial navigation
    const hash = window.location.hash.replace("#/", "");
    if (hash) {
      const parts = hash.split("/");
      this.navigate(parts[0], parts[1] || "overview", false);
    } else {
      this.navigate("dashboard", "overview", false);
    }
  },

  navigate(module, submodule = "overview", updateHash = true) {
    this.currentModule = module;
    this.currentSubmodule = submodule;

    if (updateHash) {
      window.location.hash = `#/${module}/${submodule}`;
    }

    this.updateSidebarUI(module, submodule);
    this.updateBreadcrumb(module, submodule);
    this.renderContent();

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });

    // Close mobile sidebar if open
    document.querySelector(".app-sidebar")?.classList.remove("mobile-open");
  },

  updateSidebarUI(module, submodule) {
    // Reset all active states
    document.querySelectorAll(".nav-link").forEach(l => l.classList.remove("active"));
    document.querySelectorAll(".submenu-link").forEach(l => l.classList.remove("active"));

    // Find active links
    const targetNavLink = document.querySelector(`.nav-link[data-module="${module}"]`);
    if (targetNavLink) {
      targetNavLink.classList.add("active");
      const parentGroup = targetNavLink.closest(".nav-group");
      if (parentGroup) {
        parentGroup.classList.add("expanded");
      }
    }

    const targetSubLink = document.querySelector(`.submenu-link[data-submodule="${submodule}"][data-module="${module}"]`);
    if (targetSubLink) {
      targetSubLink.classList.add("active");
      const parentGroup = targetSubLink.closest(".nav-group");
      if (parentGroup) {
        parentGroup.classList.add("expanded");
      }
    }
  },

  updateBreadcrumb(module, submodule) {
    const nav = document.getElementById("breadcrumb-container");
    if (!nav) return;

    const moduleTitles = {
      dashboard: "Dashboard",
      masters: "Masters Management",
      purchase: "Purchase Management",
      jobwork: "Job Work & Assign",
      production: "Production Management",
      qr: "QR Management",
      dispatch: "Dispatch Management",
      invoices: "Sales Invoices",
      accounts: "Accounts & Settlements",
      reports: "Reports Hub",
      admin: "Administration"
    };

    const subTitles = {
      overview: "Overview",
      customers: "Customer Master",
      vendors: "Vendor Master",
      jobworkers: "Job Worker Master",
      items: "Item Master",
      sizes: "Size & Color Master",
      assign: "Job Assign Orders",
      "inward-report": "Job Inward & Ready Report",
      orders: "Sales Orders",
      jobwork: "Job Work Assignment",
      qc: "Quality Check (QC)",
      tracking: "Lot Tracking",
      inward: "Purchase Inward (GRN)",
      ready: "Ready for Dispatch",
      dispatch: "Dispatch Challans",
      list: "Customer Invoices",
      create: "Create Sales Invoice",
      "customer-accounts": "Customer Settlements",
      "customer-outstanding": "Customer Outstanding",
      "vendor-outstanding": "Vendor Outstanding",
      "jobworker-outstanding": "Job Worker Outstanding",
      generator: "Discount QR Generator",
      scanner: "Customer QR Scanner",
      history: "Active Vouchers Library",
      ledger: "Item Stock Ledger",
      stock: "Stock Report",
      "lot-purchase": "Lot-Wise Purchase",
      "lot-sales": "Lot-Wise Sales",
      users: "User Management",
      roles: "Roles & Permissions",
      activity: "Audit Logs",
      settings: "Company Settings"
    };

    const mTitle = moduleTitles[module] || module;
    const sTitle = subTitles[submodule] || submodule;

    nav.innerHTML = `
      <div class="breadcrumb-item">
        <span>${mTitle}</span>
      </div>
      ${submodule && submodule !== "overview" ? `
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        <div class="breadcrumb-item active">
          <span>${sTitle}</span>
        </div>
      ` : ''}
    `;
  },

  renderContent() {
    const main = document.getElementById("main-content-render");
    if (!main) return;

    let html = "";
    let postRenderFn = null;

    const m = this.currentModule;
    const s = this.currentSubmodule;

    if (m === "dashboard") {
      html = DashboardView.render();
      postRenderFn = () => DashboardView.postRender();
    } else if (m === "masters") {
      if (s === "customers") html = MastersView.renderCustomers();
      else if (s === "vendors") html = MastersView.renderVendors();
      else if (s === "jobworkers") html = MastersView.renderJobWorkers();
      else if (s === "items") html = MastersView.renderItems();
      else if (s === "sizes") html = MastersView.renderSizesAndColors();
      else html = MastersView.renderCustomers();
    } else if (m === "purchase") {
      if (s === "inward") html = PurchaseView.renderInwards();
      else html = PurchaseView.renderOrders();
    } else if (m === "jobwork") {
      html = JobWorkView.render(s);
    } else if (m === "production") {
      if (s === "orders") html = ProductionView.renderOrders();
      else if (s === "jobwork") html = ProductionView.renderJobWork();
      else if (s === "qc") html = ProductionView.renderQC();
      else if (s === "tracking") html = ProductionView.renderTracking();
      else html = ProductionView.renderOrders();
    } else if (m === "qr") {
      if (s === "generator") html = QRView.renderGenerator();
      else if (s === "scanner") html = QRView.renderScanner();
      else if (s === "history") html = QRView.renderHistory();
      else html = QRView.renderGenerator();
    } else if (m === "dispatch") {
      if (s === "ready") html = DispatchView.renderReady();
      else html = DispatchView.renderDispatch();
    } else if (m === "invoices") {
      html = InvoicesView.render(s);
    } else if (m === "accounts") {
      if (s === "customer-outstanding") html = AccountsView.renderCustomerOutstanding();
      else if (s === "vendor-outstanding") html = AccountsView.renderVendorOutstanding();
      else if (s === "jobworker-outstanding") html = AccountsView.renderJobWorkerOutstanding();
      else html = AccountsView.renderCustomerSettlement();
    } else if (m === "reports") {
      if (s === "ledger") html = ReportsView.renderItemLedger();
      else if (s === "stock") html = ReportsView.renderStockReport();
      else if (s === "lot-purchase") html = ReportsView.renderLotPurchase();
      else if (s === "lot-sales") html = ReportsView.renderLotSales();
      else html = ReportsView.renderDashboard();
    } else if (m === "admin") {
      if (s === "roles") html = AdminView.renderRoles();
      else if (s === "activity") html = AdminView.renderActivity();
      else if (s === "settings") html = AdminView.renderSettings();
      else html = AdminView.renderUsers();
    } else {
      html = DashboardView.render();
      postRenderFn = () => DashboardView.postRender();
    }

    main.innerHTML = html;

    if (postRenderFn) {
      setTimeout(postRenderFn, 20);
    }
  },

  refreshCurrentView() {
    this.renderContent();
  },

  setupEventListeners() {
    // Sidebar Accordion Click Toggles
    document.addEventListener("click", (e) => {
      const groupToggle = e.target.closest(".nav-group-toggle");
      if (groupToggle) {
        const group = groupToggle.closest(".nav-group");
        group.classList.toggle("expanded");
      }

      const navLink = e.target.closest("[data-route]");
      if (navLink) {
        e.preventDefault();
        const route = navLink.getAttribute("data-route");
        const parts = route.split("/");
        App.navigate(parts[0], parts[1] || "overview");
      }

      // Close notification dropdown when clicked outside
      const notifMenu = document.getElementById("notification-dropdown-menu");
      const notifBtn = document.getElementById("notification-bell-btn");
      if (notifMenu && !notifMenu.contains(e.target) && !notifBtn.contains(e.target)) {
        notifMenu.classList.remove("show");
      }
    });

    // Sidebar Collapse Toggle
    const sidebarToggleBtn = document.getElementById("sidebar-toggle");
    if (sidebarToggleBtn) {
      sidebarToggleBtn.onclick = () => {
        const sidebar = document.querySelector(".app-sidebar");
        const wrapper = document.querySelector(".main-wrapper");
        if (window.innerWidth <= 1024) {
          sidebar.classList.toggle("mobile-open");
        } else {
          sidebar.classList.toggle("collapsed");
          wrapper.classList.toggle("sidebar-collapsed");
        }
      };
    }

    // Notification Dropdown Toggle
    const notifBtn = document.getElementById("notification-bell-btn");
    if (notifBtn) {
      notifBtn.onclick = () => {
        const menu = document.getElementById("notification-dropdown-menu");
        menu.classList.toggle("show");
      };
    }
  },

  updateHeaderBadges() {
    const notifDot = document.querySelector(".notification-badge-dot");
    const unreadCount = ERPState.data.notifications.filter(n => n.unread).length;
    if (notifDot) {
      notifDot.style.display = unreadCount > 0 ? "block" : "none";
    }
  },

  setupGlobalSearch() {
    const searchInput = document.getElementById("global-search-input");
    if (!searchInput) return;

    searchInput.addEventListener("keydown", (e) => {
      if (e.key === "Enter") {
        const val = searchInput.value.trim().toLowerCase();
        if (!val) return;

        if (val.includes("so-") || val.includes("order")) App.navigate("production", "orders");
        else if (val.includes("po-") || val.includes("purchase")) App.navigate("purchase", "orders");
        else if (val.includes("jw-") || val.includes("job")) App.navigate("production", "jobwork");
        else if (val.includes("lot") || val.includes("qr")) App.navigate("qr", "scanner");
        else if (val.includes("cust") || val.includes("client")) App.navigate("masters", "customers");
        else if (val.includes("stock") || val.includes("fab") || val.includes("item")) App.navigate("masters", "items");
        else if (val.includes("disp") || val.includes("lr")) App.navigate("dispatch", "dispatch");
        else if (val.includes("rep") || val.includes("ledger")) App.navigate("reports", "ledger");
        else App.navigate("masters", "customers");

        searchInput.value = "";
        UI.showToast("Search Navigated", `Filtered views matching: "${val}"`, "info");
      }
    });
  },

  setupShortcuts() {
    document.addEventListener("keydown", (e) => {
      // Ctrl+K for Global Search Focus
      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === "k") {
        e.preventDefault();
        document.getElementById("global-search-input")?.focus();
      }
    });
  },

  openHelpModal() {
    const content = `
      <div style="display:flex; flex-direction:column; gap:16px; font-size:0.875rem;">
        <p>Welcome to <strong>GarmentERP</strong> — Enterprise Manufacturing & Supply Chain System for FashionWorks Pvt. Ltd.</p>
        
        <div style="background:var(--slate-50); border:1px solid var(--slate-200); border-radius:var(--radius-lg); padding:16px;">
          <h4 style="font-size:0.95rem; margin-bottom:8px;">Manufacturing Core Lifecycle:</h4>
          <ol style="padding-left:20px; line-height:1.7;">
            <li><strong>Customer Order:</strong> Book sales order & auto-generate QR Lot tag.</li>
            <li><strong>Purchase & Inward (GRN):</strong> Receive raw fabrics from vendors & increment stock.</li>
            <li><strong>Job Work Issue:</strong> Dispatch cutting bundles to job workers (Challan Rule 55).</li>
            <li><strong>Job Work Inward & QC:</strong> Reconcile good vs defective pieces & pass into finished goods.</li>
            <li><strong>Dispatch & Settlement:</strong> Generate Lorry Receipt, create Tax Invoice, and record payment.</li>
          </ol>
        </div>

        <p style="color:var(--slate-500); font-size:0.8rem;">
          Demo System Active • All buttons, modals, print layouts, and QR barcode generators are fully functional.
        </p>
      </div>
    `;

    UI.openModal({ title: "GarmentERP User Guidance & Workflow", content, size: "modal-md" });
  },

  openUserModal() {
    const u = ERPState.data.currentUser;
    const content = `
      <div style="display:flex; flex-direction:column; align-items:center; text-align:center; gap:12px;">
        <div class="user-avatar" style="width:64px; height:64px; font-size:1.5rem;">${u.avatar}</div>
        <div>
          <h3 style="margin:0;">${u.name}</h3>
          <p style="color:var(--slate-500); font-size:0.825rem;">${u.email}</p>
          <span class="badge badge-purple" style="margin-top:6px;">${u.role}</span>
        </div>

        <div style="width:100%; border-top:1px solid var(--slate-200); padding-top:14px; margin-top:10px; text-align:left; font-size:0.85rem;">
          <div style="margin-bottom:6px;"><strong>Company:</strong> FashionWorks Pvt. Ltd.</div>
          <div style="margin-bottom:6px;"><strong>Facility:</strong> Apparel Park MIDC Tiruppur / Mumbai</div>
          <div><strong>Active Session:</strong> Logged in via SSO (Administrator)</div>
        </div>
      </div>
    `;

    const footer = `
      <button class="btn btn-secondary btn-sm" onclick="UI.closeModal()">Close</button>
      <button class="btn btn-danger btn-sm" onclick="App.logout()">Log Out</button>
    `;

    UI.openModal({ title: "User Profile", content, footer, size: "modal-sm" });
  },

  logout() {
    UI.closeModal();
    const loginOverlay = document.getElementById("login-modal-overlay");
    if (loginOverlay) {
      loginOverlay.style.display = "flex";
    }
  },

  loginSubmit(e) {
    if (e) e.preventDefault();
    const loginOverlay = document.getElementById("login-modal-overlay");
    if (loginOverlay) {
      loginOverlay.style.display = "none";
    }
    UI.showToast("Welcome Back", "Logged in as Admin User (FashionWorks Pvt. Ltd.)", "success");
  }
};
