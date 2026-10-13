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

  // --- CSV / Excel Export Helpers ---
  exportToCSV(filename, headers, rows) {
    let cleanFilename = filename || "data_export";
    if (!cleanFilename.toLowerCase().endsWith(".csv")) {
      cleanFilename += ".csv";
    }

    let csvContent = "";
    csvContent += headers.map(h => `"${String(h || '').trim().replace(/"/g, '""')}"`).join(",") + "\r\n";
    rows.forEach(row => {
      csvContent += row.map(cell => `"${String(cell !== undefined && cell !== null ? cell : '').trim().replace(/"/g, '""')}"`).join(",") + "\r\n";
    });

    const blob = new Blob(["\uFEFF" + csvContent], { type: "text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute("download", cleanFilename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    setTimeout(() => URL.revokeObjectURL(url), 1000);

    if (window.Toast) {
      Toast.fire({ icon: "success", title: `Exported ${cleanFilename} successfully` });
    } else if (this.showToast) {
      this.showToast("Export Successful", `Downloaded ${cleanFilename}`, "success");
    }
  },

  exportTableToCSV(tableId, filename = "data_export") {
    let table = null;
    if (typeof tableId === 'string') {
      table = document.getElementById(tableId) || document.querySelector(`table.${tableId}`) || document.querySelector(tableId);
    } else if (tableId instanceof HTMLElement) {
      table = tableId;
    }

    if (!table) {
      if (window.Toast) {
        Toast.fire({ icon: "error", title: "Table not found for export" });
      } else if (this.showToast) {
        this.showToast("Export Error", "Table not found", "error");
      }
      return;
    }

    // Determine header cells and ignore action/checkbox columns
    const headerRow = table.querySelector("thead tr") || table.querySelector("tr");
    if (!headerRow) return;

    const validColIndices = [];
    const headers = [];

    const thCells = headerRow.querySelectorAll("th, td");
    thCells.forEach((th, idx) => {
      const text = th.innerText.trim();
      const lower = text.toLowerCase();
      // Skip actions, buttons, or checkboxes
      if (lower === "action" || lower === "actions" || (lower === "#" && thCells.length > 5) || th.classList.contains("no-export")) {
        return;
      }
      validColIndices.push(idx);
      headers.push(text || `Column ${idx + 1}`);
    });

    const rows = [];
    const trList = table.querySelectorAll("tbody tr");
    const targetRows = trList.length > 0 ? trList : table.querySelectorAll("tr:not(:first-child)");

    targetRows.forEach(tr => {
      // Skip hidden rows, filtered rows, or empty-state placeholder rows (with colspan)
      if (tr.classList.contains("filter-no-results") || tr.style.display === "none") return;
      if (tr.querySelector("td[colspan]")) return;

      const cells = tr.querySelectorAll("td");
      if (cells.length === 0) return;

      const rowData = [];
      validColIndices.forEach(colIdx => {
        if (colIdx < cells.length) {
          const cell = cells[colIdx];
          
          // Clone cell to remove buttons/tooltips/scripts if any
          const cellClone = cell.cloneNode(true);
          cellClone.querySelectorAll("button, .btn, script, style, .modal, .dropdown-menu").forEach(el => el.remove());
          
          let cellText = cellClone.innerText || cellClone.textContent || "";
          // Clean up newlines, multiple spaces, tabs
          cellText = cellText.replace(/\r?\n|\r/g, " ").replace(/\s+/g, " ").trim();
          rowData.push(cellText);
        } else {
          rowData.push("");
        }
      });

      if (rowData.some(val => val.length > 0)) {
        rows.push(rowData);
      }
    });

    if (rows.length === 0) {
      if (window.Toast) {
        Toast.fire({ icon: "warning", title: "No data rows available to export" });
      } else if (this.showToast) {
        this.showToast("Export Notice", "No data rows available to export", "warning");
      }
      return;
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

  // --- Print Element & Report Tool ---
  printElement(elementId, title = "Document") {
    let el = null;
    if (typeof elementId === "string") {
      el = document.getElementById(elementId) || document.querySelector(`table#${elementId}`) || document.querySelector(elementId);
    } else if (elementId instanceof HTMLElement) {
      el = elementId;
    }

    if (!el) {
      window.print();
      return;
    }

    // Clone element to sanitize for print
    const clone = el.cloneNode(true);
    // Strip action buttons, toolbars, search boxes, modals
    clone.querySelectorAll("button, .btn, .action-icon-btn, .table-action-btn, script, style, .modal, .dropdown-menu, .no-print").forEach(node => {
      node.remove();
    });

    const companyName = window.COMPANY_NAME || (window.COMPANY_SETTINGS && window.COMPANY_SETTINGS.company_name) || "aarambh";
    const gstin = (window.COMPANY_SETTINGS && window.COMPANY_SETTINGS.gstin) || "";
    const dateStr = new Date().toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });

    const htmlContent = `
      <!DOCTYPE html>
      <html>
      <head>
        <meta charset="utf-8">
        <title>${title}</title>
        <style>
          @page { size: auto; margin: 12mm 10mm 12mm 10mm; }
          * { box-sizing: border-box; margin: 0; padding: 0; }
          body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #0f172a;
            background: #ffffff;
            padding: 16px;
            font-size: 11px;
            line-height: 1.4;
          }
          .print-header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
          }
          .company-title {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
          }
          .company-meta {
            font-size: 10.5px;
            color: #475569;
            margin-top: 2px;
          }
          .report-info {
            text-align: right;
          }
          .report-title {
            font-size: 14px;
            font-weight: 800;
            color: #1e40af;
          }
          .report-date {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
          }
          table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 10.5px;
          }
          th, td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
            vertical-align: middle;
          }
          th {
            background-color: #f1f5f9 !important;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            font-size: 9.5px;
            letter-spacing: 0.03em;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
          }
          tr:nth-child(even) td {
            background-color: #f8fafc;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
          }
          .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9.5px;
            font-weight: 700;
            border: 1px solid #cbd5e1;
          }
          .print-footer {
            margin-top: 18px;
            padding-top: 8px;
            border-top: 1px dashed #cbd5e1;
            display: flex;
            justify-content: space-between;
            font-size: 9.5px;
            color: #64748b;
          }
        </style>
      </head>
      <body>
        <div class="print-header">
          <div>
            <div class="company-title">${companyName}</div>
            <div class="company-meta">${gstin ? 'GSTIN: ' + gstin + ' &bull; ' : ''}Garment Manufacturing Operations</div>
          </div>
          <div class="report-info">
            <div class="report-title">${title}</div>
            <div class="report-date">Generated on: ${dateStr}</div>
          </div>
        </div>
        ${clone.outerHTML}
        <div class="print-footer">
          <div>Official ERP Generated Report &bull; Aarambh</div>
          <div>Confidential</div>
        </div>
      </body>
      </html>
    `;

    // 1. Try hidden iframe first (Bypasses popup blockers)
    let printFrame = document.getElementById("erp-print-iframe");
    if (!printFrame) {
      printFrame = document.createElement("iframe");
      printFrame.id = "erp-print-iframe";
      printFrame.style.position = "fixed";
      printFrame.style.right = "0";
      printFrame.style.bottom = "0";
      printFrame.style.width = "0";
      printFrame.style.height = "0";
      printFrame.style.border = "0";
      document.body.appendChild(printFrame);
    }

    try {
      const frameDoc = printFrame.contentWindow.document;
      frameDoc.open();
      frameDoc.write(htmlContent);
      frameDoc.close();

      setTimeout(() => {
        try {
          printFrame.contentWindow.focus();
          printFrame.contentWindow.print();
        } catch (e) {
          fallbackWindowPrint();
        }
      }, 300);
    } catch (e) {
      fallbackWindowPrint();
    }

    function fallbackWindowPrint() {
      const printWindow = window.open('', '_blank', 'width=900,height=700');
      if (printWindow) {
        printWindow.document.write(htmlContent);
        printWindow.document.close();
        setTimeout(() => {
          printWindow.focus();
          printWindow.print();
        }, 300);
      } else {
        window.print();
      }
    }
  },

  // --- Print Section Alias ---
  printSection(elementId, title = "Document") {
    return this.printElement(elementId, title);
  }
};

// Global bindings
window.UI = UI;
window.printElement = UI.printElement.bind(UI);
window.printSection = UI.printSection.bind(UI);
