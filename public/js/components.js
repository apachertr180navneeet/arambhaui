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
    // Automatically close any open drawer to avoid overlay collisions
    this.closeDrawer();

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
      setTimeout(() => {
        if (!backdrop.classList.contains("open")) {
          backdrop.innerHTML = "";
        }
      }, 300);
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
    if (num === null || num === undefined || isNaN(Number(num))) return "₹0";
    return "₹" + Number(num).toLocaleString('en-IN', { maximumFractionDigits: 2 });
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
    if (!status) return `<span class="badge badge-slate"><span class="badge-dot"></span>-</span>`;
    const s = String(status).toLowerCase();
    if (s.includes("active") || s.includes("received") || s.includes("completed") || s.includes("paid") || s.includes("passed") || s.includes("approved") || s.includes("settled")) {
      return `<span class="badge badge-success"><span class="badge-dot"></span>${status}</span>`;
    }
    if (s.includes("in production") || s.includes("in progress") || s.includes("pending") || s.includes("partially") || s.includes("in transit") || s.includes("material") || s.includes("due")) {
      return `<span class="badge badge-warning"><span class="badge-dot"></span>${status}</span>`;
    }
    if (s.includes("cancel") || s.includes("reject") || s.includes("unpaid") || s.includes("failed") || s.includes("overdue") || s.includes("damage") || s.includes("inactive")) {
      return `<span class="badge badge-danger"><span class="badge-dot"></span>${status}</span>`;
    }
    if (s.includes("ready") || s.includes("dispatched") || s.includes("assigned")) {
      return `<span class="badge badge-primary"><span class="badge-dot"></span>${status}</span>`;
    }
    return `<span class="badge badge-slate"><span class="badge-dot"></span>${status}</span>`;
  },

  // --- Universal Table Filter Tool ---
  filterGenericTable(tableId, query) {
    const q = (query || "").toLowerCase().trim();
    const table = document.getElementById(tableId) || document.querySelector(`table.${tableId}`);
    if (!table) return;
    const tbody = table.querySelector("tbody");
    if (!tbody) return;
    const rows = tbody.querySelectorAll("tr");
    let visibleCount = 0;
    
    rows.forEach(r => {
      if (r.classList.contains("filter-no-results")) return;
      if (r.classList.contains("empty-state-row")) return;
      
      const match = !q || r.innerText.toLowerCase().includes(q);
      r.style.display = match ? "" : "none";
      if (match) visibleCount++;
    });

    let noResultsRow = tbody.querySelector(".filter-no-results");
    if (visibleCount === 0 && q) {
      if (!noResultsRow) {
        noResultsRow = document.createElement("tr");
        noResultsRow.className = "filter-no-results";
        const thCount = table.querySelectorAll("thead th").length || 8;
        noResultsRow.innerHTML = `<td colspan="${thCount}" style="text-align:center; padding:28px 16px; color:var(--slate-500); font-style:italic;">No matching records found for "<strong>${escapeHtml(query)}</strong>"</td>`;
        tbody.appendChild(noResultsRow);
      } else {
        noResultsRow.style.display = "";
        noResultsRow.querySelector("td").innerHTML = `No matching records found for "<strong>${escapeHtml(query)}</strong>"`;
      }
    } else if (noResultsRow) {
      noResultsRow.style.display = "none";
    }

    function escapeHtml(text) {
      return String(text).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
    }
  },

  // --- CSV Export Helpers ---
  exportToCSV(filename, headers, rows) {
    let csvContent = "data:text/csv;charset=utf-8,\uFEFF";
    csvContent += headers.map(h => `"${String(h).replace(/"/g, '""')}"`).join(",") + "\r\n";
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
  },

  exportTableToCSV(tableId, filename = "data_export") {
    const table = document.getElementById(tableId) || document.querySelector(`table.${tableId}`);
    if (!table) return UI.showToast("Export Error", "Table not found", "error");

    const headers = [];
    table.querySelectorAll("thead th").forEach(th => {
      // Exclude action column headers
      const txt = th.innerText.trim();
      if (txt && !txt.toLowerCase().includes("action")) {
        headers.push(txt);
      }
    });

    const rows = [];
    table.querySelectorAll("tbody tr").forEach(tr => {
      if (tr.classList.contains("filter-no-results") || tr.style.display === "none") return;
      const row = [];
      const cells = tr.querySelectorAll("td");
      if (cells.length === 0) return;
      // Skip if action row
      cells.forEach((td, idx) => {
        if (idx < headers.length) {
          row.push(td.innerText.trim().replace(/\n+/g, ' '));
        }
      });
      if (row.length > 0) rows.push(row);
    });

    if (rows.length === 0) {
      return UI.showToast("Export Notice", "No data rows available to export", "warning");
    }

    this.exportToCSV(filename, headers, rows);
  },

  // --- Copy to Clipboard Tool ---
  copyToClipboard(text, successMessage = "Copied to clipboard") {
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(text).then(() => {
        UI.showToast("Copied", successMessage, "info");
      }).catch(() => {
        fallbackCopy(text, successMessage);
      });
    } else {
      fallbackCopy(text, successMessage);
    }

    function fallbackCopy(val, msg) {
      const ta = document.createElement("textarea");
      ta.value = val;
      ta.style.position = "fixed";
      ta.style.opacity = "0";
      document.body.appendChild(ta);
      ta.focus();
      ta.select();
      try {
        document.execCommand("copy");
        UI.showToast("Copied", msg, "info");
      } catch (e) {
        UI.showToast("Copy Failed", "Please manually copy the text", "error");
      }
      ta.remove();
    }
  },

  // --- Form Button Loading Helper ---
  setButtonLoading(button, isLoading, loadingText = "Saving...") {
    if (!button) return;
    if (isLoading) {
      button.dataset.originalHtml = button.innerHTML;
      button.disabled = true;
      button.innerHTML = `
        <svg class="animate-spin" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation:spin 1s linear infinite; display:inline-block; vertical-align:middle; margin-right:6px;">
          <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
          <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"></path>
        </svg>
        <span>${loadingText}</span>
      `;
    } else {
      button.disabled = false;
      if (button.dataset.originalHtml) {
        button.innerHTML = button.dataset.originalHtml;
        delete button.dataset.originalHtml;
      }
    }
  },

  // --- Print Section Tool ---
  printSection(elementId, title = "GarmentERP Document") {
    const el = document.getElementById(elementId);
    if (!el) {
      window.print();
      return;
    }
    const printWindow = window.open('', '_blank', 'width=900,height=700');
    if (!printWindow) {
      window.print();
      return;
    }
    printWindow.document.write(`
      <!DOCTYPE html>
      <html>
      <head>
        <title>${title}</title>
        <link rel="stylesheet" href="/css/main.css">
        <link rel="stylesheet" href="/css/print.css">
        <style>
          body { padding: 24px; font-family: 'Plus Jakarta Sans', -apple-system, sans-serif; background: #fff; }
          .no-print, .table-actions, .table-toolbar { display: none !important; }
          table { width: 100%; border-collapse: collapse; margin-top: 16px; }
          th, td { border: 1px solid #cbd5e1; padding: 8px 12px; text-align: left; font-size: 12px; }
          th { background: #f8fafc; font-weight: 700; }
        </style>
      </head>
      <body>
        <div style="margin-bottom:20px; border-bottom:2px solid #0f172a; padding-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
          <div>
            <h2 style="margin:0; font-size:18px; color:#0f172a;">FashionWorks Pvt. Ltd. - GarmentERP</h2>
            <p style="margin:4px 0 0 0; font-size:12px; color:#64748b;">Generated on ${new Date().toLocaleString()}</p>
          </div>
          <div style="font-size:14px; font-weight:700; color:#2563eb;">${title}</div>
        </div>
        ${el.innerHTML}
        <script>
          window.onload = function() {
            window.print();
            window.onafterprint = function() { window.close(); };
          };
        </script>
      </body>
      </html>
    `);
    printWindow.document.close();
  }
};
