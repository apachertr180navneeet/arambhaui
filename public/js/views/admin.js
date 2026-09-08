/* ==========================================================================
   ADMINISTRATION VIEW - USERS, ROLES MATRIX, AUDIT LOGS & COMPANY SETTINGS
   GarmentERP
   ========================================================================== */

const AdminView = {
  // 1. USER MANAGEMENT
  renderUsers() {
    const users = ERPState.data.users;

    return `
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left">
            <h3 style="font-size:1.05rem;">User Accounts & System Access</h3>
          </div>
          <div class="table-toolbar-right">
            <button class="btn btn-primary btn-sm" onclick="AdminView.openAddUserModal()">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Add New User
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>User ID</th>
                <th>Full Name</th>
                <th>Email Address</th>
                <th>Assigned Role</th>
                <th>Status</th>
                <th>Last Active</th>
                <th style="text-align:right;">Actions</th>
              </tr>
            </thead>
            <tbody>
              ${users.map(u => `
                <tr>
                  <td class="mono-cell font-bold" style="color:var(--primary-600);">${u.id}</td>
                  <td class="primary-cell">${u.name}</td>
                  <td>${u.email}</td>
                  <td><span class="badge badge-purple">${u.role}</span></td>
                  <td>${UI.formatStatusBadge(u.status)}</td>
                  <td class="text-muted">${u.lastLogin}</td>
                  <td class="table-actions">
                    <button class="table-action-btn edit" onclick="UI.showToast('Edit User', 'User permissions dialog opened', 'info')">Edit</button>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      </div>
    `;
  },

  openAddUserModal() {
    const content = `
      <form id="new-user-form">
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Full Name <span class="required-star">*</span></label>
            <input type="text" class="form-control" id="usr-name" required placeholder="e.g. Ramesh Thorat">
          </div>

          <div class="form-group">
            <label class="form-label">Email Address <span class="required-star">*</span></label>
            <input type="email" class="form-control" id="usr-email" required placeholder="store@fashionworks.co.in">
          </div>

          <div class="form-group">
            <label class="form-label">Role Designation</label>
            <select class="form-control" id="usr-role">
              <option value="Store Manager">Store Manager</option>
              <option value="Production Manager">Production Manager</option>
              <option value="Accounts Manager">Accounts Manager</option>
              <option value="Dispatch Manager">Dispatch Manager</option>
              <option value="Administrator">Administrator</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Temporary Password</label>
            <input type="text" class="form-control font-mono" value="Garment@2026">
          </div>
        </div>
      </form>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="UI.closeModal()">Cancel</button>
      <button class="btn btn-primary" id="btn-save-user">Create User Account</button>
    `;

    UI.openModal({ title: "Add System User", content, footer, size: "modal-md" });

    document.getElementById("btn-save-user").onclick = () => {
      const name = document.getElementById("usr-name").value.trim();
      const email = document.getElementById("usr-email").value.trim();
      const role = document.getElementById("usr-role").value;

      if (!name || !email) return UI.showToast("Required Fields", "Name and email are required", "error");

      ERPState.data.users.push({
        id: `USR-00${ERPState.data.users.length + 1}`,
        name,
        email,
        role,
        status: "Active",
        lastLogin: "Just now"
      });

      ERPState.logActivity(`Created user account for ${name} (${role})`, "Users", name);
      ERPState.saveState();

      UI.showToast("User Created", `Access granted for ${name}`, "success");
      UI.closeModal();
      App.refreshCurrentView();
    };
  },

  // 2. ROLES & PERMISSIONS MATRIX
  renderRoles() {
    const roles = ERPState.data.roles;
    const permissions = ["view", "create", "edit", "delete", "approve", "export"];

    return `
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left">
            <h3 style="font-size:1.05rem;">Role-Based Access Control (RBAC) Permissions Matrix</h3>
          </div>
          <div class="table-toolbar-right">
            <button class="btn btn-primary btn-sm" onclick="UI.showToast('Permissions Saved', 'RBAC matrix successfully updated across all modules', 'success')">
              Save Permissions Matrix
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table" style="text-align:center;">
            <thead>
              <tr>
                <th style="text-align:left;">Role Designation</th>
                <th>View Records</th>
                <th>Create Entries</th>
                <th>Edit / Update</th>
                <th>Delete Records</th>
                <th>Approve (PO / QC)</th>
                <th>Export Data</th>
              </tr>
            </thead>
            <tbody>
              ${roles.map(r => `
                <tr>
                  <td style="text-align:left;" class="primary-cell">
                    <span class="badge badge-purple">${r.role}</span>
                  </td>
                  ${permissions.map(p => `
                    <td>
                      <input type="checkbox" style="width:16px; height:16px; cursor:pointer;" ${r.permissions[p] ? 'checked' : ''}>
                    </td>
                  `).join('')}
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      </div>
    `;
  },

  // 3. ACTIVITY AUDIT LOGS
  renderActivity() {
    const logs = ERPState.data.activityLogs;

    return `
      <div class="table-card">
        <div class="table-toolbar">
          <div class="table-toolbar-left">
            <div class="table-search-box">
              <svg class="table-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              <input type="text" class="table-search-input" placeholder="Search audit trail by user, action, module..." oninput="MastersView.filterGenericTable('logs-table', this.value)">
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="data-table" id="logs-table">
            <thead>
              <tr>
                <th>Log ID</th>
                <th>User / Operator</th>
                <th>Role</th>
                <th>Action Performed</th>
                <th>Module</th>
                <th>Affected Record</th>
                <th>Timestamp</th>
                <th>IP Address</th>
              </tr>
            </thead>
            <tbody>
              ${logs.map(l => `
                <tr>
                  <td class="mono-cell font-bold">${l.id}</td>
                  <td class="primary-cell">${l.user}</td>
                  <td><span class="badge badge-slate">${l.role}</span></td>
                  <td><strong>${l.action}</strong></td>
                  <td><span class="badge badge-primary">${l.module}</span></td>
                  <td class="mono-cell">${l.record}</td>
                  <td class="text-muted">${l.time}</td>
                  <td class="mono-cell font-mono text-muted">${l.ip}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      </div>
    `;
  },

  // 4. COMPANY SETTINGS
  renderSettings() {
    const c = ERPState.data.company;

    return `
      <div style="max-width:880px; margin:0 auto;">
        <div class="card" style="margin-bottom:24px;">
          <div class="card-header">
            <div>
              <div class="card-title">Company Profile & Statutory Identifiers</div>
              <div class="card-subtitle">Default print header and invoicing entity parameters</div>
            </div>
            <button class="btn btn-primary btn-sm" onclick="UI.showToast('Settings Saved', 'Company profile details updated', 'success')">Save Changes</button>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Legal Company Name</label>
              <input type="text" class="form-control" value="${c.name}">
            </div>

            <div class="form-group">
              <label class="form-label">Brand / App Title</label>
              <input type="text" class="form-control" value="${c.brand}">
            </div>

            <div class="form-group">
              <label class="form-label">GSTIN</label>
              <input type="text" class="form-control font-mono" value="${c.gstin}">
            </div>

            <div class="form-group">
              <label class="form-label">Corporate CIN</label>
              <input type="text" class="form-control font-mono" value="${c.cin}">
            </div>

            <div class="form-group col-span-2">
              <label class="form-label">Manufacturing Plant & Registered Address</label>
              <textarea class="form-control">${c.address}</textarea>
            </div>

            <div class="form-group">
              <label class="form-label">Official Email</label>
              <input type="email" class="form-control" value="${c.email}">
            </div>

            <div class="form-group">
              <label class="form-label">Support & Orders Phone</label>
              <input type="text" class="form-control" value="${c.phone}">
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">Bank Account Details (Printed on Invoices & Challans)</div>
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Bank Name</label>
              <input type="text" class="form-control" value="${c.bank.name}">
            </div>

            <div class="form-group">
              <label class="form-label">Branch Name</label>
              <input type="text" class="form-control" value="${c.bank.branch}">
            </div>

            <div class="form-group">
              <label class="form-label">Current Account Number</label>
              <input type="text" class="form-control font-mono" value="${c.bank.accountNo}">
            </div>

            <div class="form-group">
              <label class="form-label">IFSC Code</label>
              <input type="text" class="form-control font-mono" value="${c.bank.ifsc}">
            </div>
          </div>
        </div>
      </div>
    `;
  }
};
