/* ==========================================================================
   REUSABLE UI COMPONENTS - TOASTS, MODALS, DRAWERS, TABLES & FORMATTERS
   GarmentERP
   ========================================================================== */

const UI = {
  // --- Toast Notifications ---
  showToast(title, message = "", type = "success") {
    let container = document.getElementById("toast-container");
    if (!container) {
      container = document.createElement("div");
      container.id = "toast-container";
      document.body.appendChild(container);
    }

    const toast = document.createElement("div");
    toast.className = `toast ${type}`;

    let iconSvg = ``;
    if (type === "success") {
      iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>`;
    } else if (type === "error") {
      iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>`;
    } else if (type === "warning") {
      iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>`;
    } else {
      iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`;
    }

    toast.innerHTML = `
      <div class="toast-icon">${iconSvg}</div>
      <div class="toast-content">
        <div class="toast-title">${title}</div>
        ${message ? `<div class="toast-msg">${message}</div>` : ""}
      </div>
      <button class="toast-close" onclick="this.parentElement.remove()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
      </button>
    `;

    container.appendChild(toast);

    setTimeout(() => {
      toast.classList.add("removing");
      setTimeout(() => toast.remove(), 250);
    }, 4000);
  },

  // --- Modal Dialog System ---
  openModal({ title, content, footer = "", size = "modal-lg" }) {
    let backdrop = document.getElementById("global-modal-backdrop");
    if (!backdrop) {
      backdrop = document.createElement("div");
      backdrop.id = "global-modal-backdrop";
      backdrop.className = "modal-backdrop";
      document.body.appendChild(backdrop);
    }

    backdrop.innerHTML = `
      <div class="modal-dialog ${size}">
        <div class="modal-header">
          <div class="modal-title">${title}</div>
          <button class="modal-close-btn" onclick="UI.closeModal()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
          </button>
        </div>
        <div class="modal-body">${content}</div>
        ${footer ? `<div class="modal-footer">${footer}</div>` : ""}
      </div>
    `;

    // Click outside to close
    backdrop.onclick = (e) => {
      if (e.target === backdrop) UI.closeModal();
    };

    setTimeout(() => backdrop.classList.add("open"), 10);
  },

  closeModal() {
    const backdrop = document.getElementById("global-modal-backdrop");
    if (backdrop) {
      backdrop.classList.remove("open");
      setTimeout(() => {
        backdrop.innerHTML = "";
      }, 250);
    }
  },

  // --- Detail Drawer System ---
  openDrawer({ title, subtitle = "", tabs = [], content = "", size = "" }) {
    let backdrop = document.getElementById("global-drawer-backdrop");
    if (!backdrop) {
      backdrop = document.createElement("div");
      backdrop.id = "global-drawer-backdrop";
      backdrop.className = "drawer-backdrop";
      document.body.appendChild(backdrop);
    }

    let tabsHtml = "";
    if (tabs && tabs.length > 0) {
      tabsHtml = `
        <div class="drawer-tabs-bar">
          ${tabs.map((t, idx) => `
            <button class="drawer-tab-btn ${idx === 0 ? 'active' : ''}" data-tab="${t.id}">
              ${t.label}
            </button>
          `).join('')}
        </div>
      `;
    }

    backdrop.innerHTML = `
      <div class="drawer-panel ${size}">
        <div class="drawer-header">
          <div>
            <h3 class="modal-title" style="margin:0">${title}</h3>
            ${subtitle ? `<p style="font-size:0.75rem; color:var(--slate-500); margin-top:2px;">${subtitle}</p>` : ''}
          </div>
          <button class="modal-close-btn" onclick="UI.closeDrawer()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
          </button>
        </div>
        ${tabsHtml}
        <div class="drawer-body" id="drawer-body-container">${content}</div>
      </div>
    `;

    backdrop.onclick = (e) => {
      if (e.target === backdrop) UI.closeDrawer();
    };

    setTimeout(() => backdrop.classList.add("open"), 10);
  },

  closeDrawer() {
    const backdrop = document.getElementById("global-drawer-backdrop");
    if (backdrop) {
      backdrop.classList.remove("open");
    }
  },

  // --- Confirmation Dialog ---
  showConfirm({ title, message, confirmText = "Confirm", isDanger = false, onConfirm }) {
    const footer = `
      <button class="btn btn-secondary" onclick="UI.closeModal()">Cancel</button>
      <button class="btn ${isDanger ? 'btn-danger' : 'btn-primary'}" id="btn-confirm-action">${confirmText}</button>
    `;

    const content = `
      <div style="display:flex; gap:16px; align-items:flex-start;">
        <div style="width:40px; height:40px; border-radius:50%; background:${isDanger ? 'var(--danger-50)' : 'var(--primary-50)'}; color:${isDanger ? 'var(--danger-600)' : 'var(--primary-600)'}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
        </div>
        <div>
          <p style="color:var(--slate-700); font-size:0.9rem; margin-top:4px;">${message}</p>
        </div>
      </div>
    `;

    UI.openModal({ title, content, footer, size: "modal-sm" });

    document.getElementById("btn-confirm-action").onclick = () => {
      UI.closeModal();
      if (typeof onConfirm === "function") onConfirm();
    };
  },

  // --- Formatters ---
  formatCurrency(num) {
    if (num === null || num === undefined) return "₹0";
    return "₹" + Number(num).toLocaleString('en-IN');
  },

  formatDate(dateStr) {
    if (!dateStr) return "-";
    try {
      const d = new Date(dateStr);
      if (isNaN(d.getTime())) return dateStr;
      return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    } catch (e) {
      return dateStr;
    }
  },

  formatStatusBadge(status) {
    const s = String(status).toLowerCase();
    if (s.includes("active") || s.includes("received") || s.includes("completed") || s.includes("paid") || s.includes("passed") || s.includes("approved")) {
      return `<span class="badge badge-success"><span class="badge-dot"></span>${status}</span>`;
    }
    if (s.includes("in production") || s.includes("in progress") || s.includes("pending") || s.includes("partially") || s.includes("in transit") || s.includes("material")) {
      return `<span class="badge badge-warning"><span class="badge-dot"></span>${status}</span>`;
    }
    if (s.includes("cancel") || s.includes("reject") || s.includes("unpaid") || s.includes("failed") || s.includes("overdue") || s.includes("damage")) {
      return `<span class="badge badge-danger"><span class="badge-dot"></span>${status}</span>`;
    }
    if (s.includes("ready") || s.includes("dispatched") || s.includes("assigned")) {
      return `<span class="badge badge-primary"><span class="badge-dot"></span>${status}</span>`;
    }
    return `<span class="badge badge-slate"><span class="badge-dot"></span>${status}</span>`;
  },

  // --- CSV Export Helper ---
  exportToCSV(filename, headers, rows) {
    let csvContent = "data:text/csv;charset=utf-8,";
    csvContent += headers.map(h => `"${h}"`).join(",") + "\r\n";
    rows.forEach(row => {
      csvContent += row.map(cell => `"${String(cell || '').replace(/"/g, '""')}"`).join(",") + "\r\n";
    });

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `${filename}.csv`);
    document.body.appendChild(link);
    link.click();
    link.remove();
    UI.showToast("Export Successful", `Downloaded ${filename}.csv`, "success");
  }
};
