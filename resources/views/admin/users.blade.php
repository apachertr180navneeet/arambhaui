@extends('layouts.app')

@section('title', 'User Accounts - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Administration</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>User Accounts</span></div>
@endsection

@push('styles')
<style>
  .user-status-select {
    appearance: none;
    -webkit-appearance: none;
    padding: 4px 22px 4px 10px;
    border-radius: 14px;
    font-size: 0.75rem;
    font-weight: 700;
    border: 1px solid transparent;
    cursor: pointer;
    outline: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    background-repeat: no-repeat;
    background-position: right 6px center;
    background-size: 11px;
    display: inline-block;
  }
  .user-status-select.active {
    background-color: #ecfdf5;
    color: #059669;
    border-color: #a7f3d0;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23059669' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  }
  .user-status-select.inactive {
    background-color: #f1f5f9;
    color: #64748b;
    border-color: #cbd5e1;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  }
</style>
@endpush

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <!-- KPI Metrics Row -->
  <div class="kpi-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:var(--primary-50); color:var(--primary-600); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Registered Staff Accounts</div>
        <div style="font-size:1.45rem; font-weight:800; color:var(--slate-900); margin-top:2px;">{{ $stats['totalUsers'] }} Users</div>
      </div>
    </div>

    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Active Logins</div>
        <div style="font-size:1.45rem; font-weight:800; color:#059669; margin-top:2px;">{{ $stats['activeUsers'] }} Active</div>
      </div>
    </div>
  </div>

  <!-- Main Card Container -->
  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:20px;">
    
    <!-- Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:20px;">
      <div>
        <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900);">User Accounts & Access Control</h3>
        <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">Administrators, production floor managers, accountants & logistics operators</p>
      </div>

      <button class="btn btn-primary btn-sm" onclick="openUserCreateModal()" style="display:inline-flex; align-items:center; gap:6px; font-weight:700;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
        + Add User Account
      </button>
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="data-table" style="width:100%; font-size:0.85rem;" id="users-table">
        <thead>
          <tr>
            <th>User</th>
            <th>Email Address</th>
            <th>Assigned Role</th>
            <th style="text-align:center; width:130px;">Account Status</th>
            <th>Created Date</th>
            <th style="text-align:center; width:100px;">Actions</th>
          </tr>
        </thead>
        <tbody id="users-table-body">
          @forelse ($users as $u)
            <tr id="user-row-{{ $u->id }}">
              <td>
                <div style="display:flex; align-items:center; gap:10px;">
                  <div class="user-avatar" style="width:32px; height:32px; font-size:0.8rem; display:flex; align-items:center; justify-content:center; border-radius:50%; background:var(--primary-600); color:#fff; font-weight:700;">
                    {{ strtoupper(substr($u->name, 0, 2)) }}
                  </div>
                  <div>
                    <strong style="color:var(--slate-800); display:block;">{{ $u->name }}</strong>
                    @if(auth()->id() === $u->id)
                      <span style="font-size:0.7rem; color:#2563eb; font-weight:700;">(You)</span>
                    @endif
                  </div>
                </div>
              </td>
              <td>{{ $u->email }}</td>
              <td><span class="badge badge-purple">{{ $u->role ?: 'Administrator' }}</span></td>
              <td style="text-align:center;">
                <select class="user-status-select {{ strtolower($u->status ?? 'active') }}" onchange="changeUserStatus({{ $u->id }}, this.value, this)" title="Click to change status">
                  <option value="Active" {{ strtolower($u->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                  <option value="Inactive" {{ strtolower($u->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
              </td>
              <td>{{ $u->created_at ? $u->created_at->format('d M Y') : '—' }}</td>
              <td style="text-align:center;">
                <div style="display:inline-flex; align-items:center; gap:6px;">
                  <button type="button" class="btn btn-secondary btn-icon" onclick='openUserEditModal(@json($u))' title="Edit User" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                  </button>
                  @if(auth()->id() !== $u->id)
                    <button type="button" class="btn btn-danger btn-icon" onclick="confirmDeleteUser({{ $u->id }}, '{{ addslashes($u->name) }}')" title="Delete User" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                      </svg>
                    </button>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align:center; padding:30px; color:var(--slate-400);">No user accounts found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>

<!-- Create / Edit User Modal -->
<div id="user-modal" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div class="modal-box" style="background:#fff; border-radius:16px; width:100%; max-width:500px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid var(--slate-200); padding-bottom:12px;">
      <h3 id="user-modal-title" style="margin:0; font-size:1.15rem; font-weight:800;">Create New User Account</h3>
      <button onclick="closeUserModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--slate-400);">&times;</button>
    </div>

    <form id="user-form" method="POST" action="{{ route('admin.users.store') }}">
      @csrf
      <input type="hidden" name="_method" id="user-form-method" value="POST">
      <input type="hidden" name="user_id" id="user-form-id" value="">

      <div style="display:flex; flex-direction:column; gap:14px;">
        <div class="form-group">
          <label class="form-label" style="font-weight:700;">Full Name <span style="color:red;">*</span></label>
          <input type="text" name="name" id="user-name" class="form-control" required placeholder="e.g. Ramesh Sharma">
        </div>

        <div class="form-group">
          <label class="form-label" style="font-weight:700;">Email Address <span style="color:red;">*</span></label>
          <input type="email" name="email" id="user-email" class="form-control" required placeholder="e.g. ramesh@fashionworks.com">
        </div>

        <div class="form-group">
          <label class="form-label" style="font-weight:700;">System Role <span style="color:red;">*</span></label>
          <select name="role" id="user-role" class="form-control" required>
            <option value="Administrator">Administrator (Full Access)</option>
            <option value="Production Manager">Production Manager</option>
            <option value="Purchase Officer">Purchase Officer</option>
            <option value="Accountant">Accountant / Cashier</option>
            <option value="Dispatch Clerk">Dispatch Clerk</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" style="font-weight:700;">Account Status</label>
          <select name="status" id="user-status" class="form-control">
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" id="user-password-label" style="font-weight:700;">Password <span style="color:red;">*</span></label>
          <input type="password" name="password" id="user-password" class="form-control" placeholder="Min 6 characters">
          <small id="user-password-help" style="color:var(--slate-500); font-size:0.75rem; display:none;">Leave blank to keep existing password.</small>
        </div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px; border-top:1px solid var(--slate-200); padding-top:14px;">
        <button type="button" class="btn btn-secondary" onclick="closeUserModal()">Cancel</button>
        <button type="submit" id="user-save-btn" class="btn btn-primary" style="font-weight:700;">Create User</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete User Modal -->
<div id="delete-user-modal" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div class="modal-box" style="background:#fff; border-radius:16px; width:100%; max-width:420px; padding:24px; text-align:center;">
    <div style="width:48px; height:48px; border-radius:50%; background:#fee2e2; color:#dc2626; display:flex; align-items:center; justify-content:center; margin:0 auto 14px;">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
    </div>
    <h3 style="margin:0 0 6px; font-size:1.15rem; font-weight:800;">Delete User Account</h3>
    <p style="font-size:0.875rem; color:var(--slate-500); margin:0 0 20px;">
      Are you sure you want to delete <strong id="delete-user-name" style="color:var(--slate-800);">this user</strong>? They will no longer be able to log in.
    </p>
    <form id="delete-user-form" method="POST" action="">
      @csrf
      @method('DELETE')
      <div style="display:flex; justify-content:center; gap:10px;">
        <button type="button" class="btn btn-secondary" onclick="closeDeleteUserModal()">Cancel</button>
        <button type="submit" class="btn btn-danger" style="font-weight:700;">Yes, Delete User</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  const CSRF_TOKEN = '{{ csrf_token() }}';

  function openUserCreateModal() {
    const form = document.getElementById('user-form');
    form.reset();
    form.action = "{{ route('admin.users.store') }}";
    document.getElementById('user-form-method').value = 'POST';
    document.getElementById('user-form-id').value = '';
    document.getElementById('user-modal-title').textContent = 'Create New User Account';
    document.getElementById('user-save-btn').textContent = 'Create User';
    document.getElementById('user-password').required = true;
    document.getElementById('user-password-label').innerHTML = 'Temporary Password <span style="color:red;">*</span>';
    document.getElementById('user-password-help').style.display = 'none';
    document.getElementById('user-modal').style.display = 'flex';
  }

  function openUserEditModal(user) {
    const form = document.getElementById('user-form');
    form.reset();
    form.action = `/admin/users/${user.id}`;
    document.getElementById('user-form-method').value = 'PUT';
    document.getElementById('user-form-id').value = user.id;
    document.getElementById('user-modal-title').textContent = 'Edit User Account';
    document.getElementById('user-save-btn').textContent = 'Update User';

    document.getElementById('user-name').value = user.name || '';
    document.getElementById('user-email').value = user.email || '';
    document.getElementById('user-role').value = user.role || 'Administrator';
    document.getElementById('user-status').value = user.status || 'Active';

    document.getElementById('user-password').required = false;
    document.getElementById('user-password-label').innerHTML = 'New Password';
    document.getElementById('user-password-help').style.display = 'block';

    document.getElementById('user-modal').style.display = 'flex';
  }

  function closeUserModal() {
    document.getElementById('user-modal').style.display = 'none';
  }

  function confirmDeleteUser(id, name) {
    document.getElementById('delete-user-name').textContent = name;
    document.getElementById('delete-user-form').action = `/admin/users/${id}`;
    document.getElementById('delete-user-modal').style.display = 'flex';
  }

  function closeDeleteUserModal() {
    document.getElementById('delete-user-modal').style.display = 'none';
  }

  function changeUserStatus(id, newStatus, selectEl) {
    selectEl.style.opacity = '0.5';

    fetch(`/admin/users/${id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': CSRF_TOKEN
      },
      body: JSON.stringify({
        status: newStatus,
        name: selectEl.closest('tr').querySelector('strong').textContent.trim(),
        email: selectEl.closest('tr').children[1].textContent.trim(),
        role: selectEl.closest('tr').children[2].textContent.trim()
      })
    })
    .then(res => res.json())
    .then(data => {
      selectEl.style.opacity = '1';
      if (data.success) {
        selectEl.className = 'user-status-select ' + newStatus.toLowerCase();
        if (window.Toast) {
          Toast.fire({ icon: 'success', title: `User status changed to ${newStatus}` });
        }
      } else {
        if (window.Toast) {
          Toast.fire({ icon: 'error', title: data.message || 'Could not update status' });
        }
      }
    })
    .catch(err => {
      selectEl.style.opacity = '1';
      if (window.Toast) {
        Toast.fire({ icon: 'error', title: 'Network error updating user status' });
      }
    });
  }
</script>
@endpush
@endsection
