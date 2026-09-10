/* ==========================================================================
   BLADE SHELL COORDINATOR & INTERACTION UTILITIES
   GarmentERP - FashionWorks Pvt. Ltd.
   ========================================================================== */

const App = {
  init() {
    this.setupEventListeners();
    this.setupGlobalSearch();
    this.setupShortcuts();
  },

  setupEventListeners() {
    // Sidebar Accordion Click Toggles
    document.addEventListener("click", (e) => {
      const groupToggle = e.target.closest(".nav-group-toggle");
      if (groupToggle) {
        e.preventDefault();
        const group = groupToggle.closest(".nav-group");
        group.classList.toggle("expanded");
      }

      // Close notification dropdown when clicked outside
      const notifMenu = document.getElementById("notification-dropdown-menu");
      const notifBtn = document.getElementById("notification-bell-btn");
      if (notifMenu && !notifMenu.contains(e.target) && !notifBtn?.contains(e.target)) {
        notifMenu.classList.remove("show");
      }
    });

    // Sidebar Collapse Toggle (Desktop & Mobile)
    const sidebarToggleBtn = document.getElementById("sidebar-toggle");
    const sidebar = document.querySelector(".app-sidebar");
    const backdrop = document.getElementById("sidebar-backdrop");
    const closeBtn = document.getElementById("sidebar-close-btn");
    const wrapper = document.querySelector(".main-wrapper");

    if (sidebarToggleBtn && sidebar) {
      sidebarToggleBtn.onclick = () => {
        if (window.innerWidth <= 1024) {
          sidebar.classList.toggle("mobile-open");
          if (backdrop) backdrop.classList.toggle("active", sidebar.classList.contains("mobile-open"));
        } else {
          sidebar.classList.toggle("collapsed");
          if (wrapper) wrapper.classList.toggle("sidebar-collapsed");
        }
      };
    }

    if (backdrop && sidebar) {
      backdrop.onclick = () => {
        sidebar.classList.remove("mobile-open");
        backdrop.classList.remove("active");
      };
    }

    if (closeBtn && sidebar) {
      closeBtn.onclick = () => {
        sidebar.classList.remove("mobile-open");
        if (backdrop) backdrop.classList.remove("active");
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

  setupGlobalSearch() {
    const searchInput = document.getElementById("global-search-input");
    if (!searchInput) return;

    searchInput.addEventListener("keydown", (e) => {
      if (e.key === "Enter") {
        const val = searchInput.value.trim().toLowerCase();
        if (!val) return;

        if (val.includes("jw-") || val.includes("job") || val.includes("assign")) window.location.href = "/jobwork/assign";
        else if (val.includes("po-") || val.includes("pur") || val.includes("order")) window.location.href = "/purchase/orders";
        else if (val.includes("lot") || val.includes("qr") || val.includes("voucher")) window.location.href = "/qr/history";
        else if (val.includes("cust") || val.includes("client")) window.location.href = "/masters/customers";
        else if (val.includes("vend") || val.includes("supplier")) window.location.href = "/masters/vendors";
        else if (val.includes("stock") || val.includes("fab") || val.includes("item")) window.location.href = "/masters/items";
        else if (val.includes("disp") || val.includes("lr") || val.includes("challan")) window.location.href = "/dispatch/dispatch";
        else if (val.includes("rep") || val.includes("ledger")) window.location.href = "/reports/ledger";
        else if (val.includes("user") || val.includes("admin")) window.location.href = "/admin/users";
        else window.location.href = `/masters/customers?search=${encodeURIComponent(val)}`;
      }
    });
  },

  setupShortcuts() {
    document.addEventListener("keydown", (e) => {
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
            <li><strong>Masters Setup:</strong> Register Customers, Vendors, Job Workers, and Raw Fabrics / Items.</li>
            <li><strong>Purchase Orders (PO):</strong> Create multi-item raw material POs with live tax & total calculation.</li>
            <li><strong>Job Work Issue:</strong> Assign processes (Cutting, Stitching, Finishing) to external Job Workers.</li>
            <li><strong>QR Code Generator & Verification:</strong> Create single-use discount vouchers and scan QR / upload vouchers.</li>
            <li><strong>Logistics & Dispatch:</strong> Generate Lorry Receipts (LR) and dispatch challans.</li>
            <li><strong>Accounts & Ledgers:</strong> Reconcile customer and vendor payment balances.</li>
          </ol>
        </div>

        <p style="color:var(--slate-500); font-size:0.8rem;">
          Production-Ready Blade UI Active • All database tables, forms, modals, print layouts, and QR barcode generators are fully server-connected.
        </p>
      </div>
    `;

    UI.openModal({ title: "GarmentERP User Guidance & Workflow", content, size: "modal-md" });
  },

  openUserModal() {
    const u = (typeof window !== 'undefined' && window.CURRENT_AUTH_USER) || {
      name: "Admin User",
      email: "admin@garmenterp.com",
      role: "Administrator",
      avatar: "AU"
    };

    const avatarText = u.avatar || (u.name ? u.name.substring(0, 2).toUpperCase() : 'AU');
    const roleText = u.role || 'Administrator';

    const content = `
      <div style="display:flex; flex-direction:column; align-items:center; text-align:center; gap:12px;">
        <div class="user-avatar" style="width:64px; height:64px; font-size:1.5rem; display:flex; align-items:center; justify-content:center; border-radius:50%; background:var(--primary-600); color:#fff; font-weight:700;">${avatarText}</div>
        <div>
          <h3 style="margin:0; font-size:1.1rem; color:var(--slate-800);">${u.name}</h3>
          <p style="color:var(--slate-500); font-size:0.825rem; margin-top:2px;">${u.email}</p>
          <span class="badge badge-purple" style="margin-top:6px; display:inline-block; padding:4px 10px; border-radius:12px; font-size:0.75rem; font-weight:600;">${roleText}</span>
        </div>

        <div style="width:100%; border-top:1px solid var(--slate-200); padding-top:14px; margin-top:10px; text-align:left; font-size:0.85rem; color:var(--slate-600);">
          <div style="margin-bottom:6px;"><strong>Company:</strong> FashionWorks Pvt. Ltd.</div>
          <div style="margin-bottom:6px;"><strong>Facility:</strong> Apparel Park MIDC Tiruppur / Mumbai</div>
          <div><strong>Active Session:</strong> Logged in (${roleText})</div>
        </div>
      </div>
    `;

    const footer = `
      <button class="btn btn-secondary btn-sm" onclick="UI.closeModal()">Close</button>
      <button class="btn btn-danger btn-sm" onclick="App.logout()" style="display:inline-flex; align-items:center; gap:6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Log Out
      </button>
    `;

    UI.openModal({ title: "User Profile", content, footer, size: "modal-sm" });
  },

  logout() {
    UI.closeModal();
    const logoutForm = document.getElementById("logout-form");
    if (logoutForm) {
      logoutForm.submit();
      return;
    }
  }
};

document.addEventListener("DOMContentLoaded", () => {
  App.init();
});
