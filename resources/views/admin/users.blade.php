@extends('layouts.app')

@section('title', 'User Accounts - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Administration</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>User Accounts</span></div>
@endsection

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
        <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">Administrators, production floor managers, accountants & logistics dispatch operators</p>
      </div>

      <button class="btn btn-primary btn-sm" onclick="openUserCreateModal()" style="display:inline-flex; align-items:center; gap:6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        + Add User Account
      </button>
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="data-table" style="width:100%; font-size:0.85rem;">
        <thead>
          <tr>
            <th>User</th>
            <th>Email Address</th>
            <th>Assigned Role</th>
            <th>Account Status</th>
            <th>Created Date</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($users as $u)
            <tr>
              <td>
                <div style="display:flex; align-items:center; gap:10px;">
                  <div class="user-avatar" style="width:32px; height:32px; font-size:0.8rem; display:flex; align-items:center; justify-content:center; border-radius:50%; background:var(--primary-600); color:#fff; font-weight:700;">
                    {{ strtoupper(substr($u->name, 0, 2)) }}
                  </div>
                  <strong style="color:var(--slate-800);">{{ $u->name }}</strong>
                </div>
              </td>
              <td>{{ $u->email }}</td>
              <td><span class="badge badge-purple">{{ $u->role ?: 'Administrator' }}</span></td>
              <td><span class="badge badge-success">{{ $u->status ?: 'Active' }}</span></td>
              <td>{{ $u->created_at ? $u->created_at->format('d M Y') : '—' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="5" style="text-align:center; padding:30px; color:var(--slate-400);">No user accounts found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>

<!-- Add User Modal -->
<div id="user-modal" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div class="modal-box" style="background:#fff; border-radius:16px; width:100%; max-width:500px; padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid var(--slate-200); padding-bottom:12px;">
      <h3 style="margin:0; font-size:1.15rem; font-weight:800;">Create New User Account</h3>
      <button onclick="closeUserCreateModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--slate-400);">&times;</button>
    </div>

    <form method="POST" action="{{ route('admin.users.store') }}">
      @csrf

      <div style="display:flex; flex-direction:column; gap:14px;">
        <div class="form-group">
          <label class="form-label">Full Name <span style="color:red;">*</span></label>
          <input type="text" name="name" class="form-control" required placeholder="e.g. Ramesh Sharma">
        </div>

        <div class="form-group">
          <label class="form-label">Email Address <span style="color:red;">*</span></label>
          <input type="email" name="email" class="form-control" required placeholder="e.g. ramesh@fashionworks.com">
        </div>

        <div class="form-group">
          <label class="form-label">System Role <span style="color:red;">*</span></label>
          <select name="role" class="form-control" required>
            <option value="Administrator">Administrator (Full Access)</option>
            <option value="Production Manager">Production Manager</option>
            <option value="Purchase Officer">Purchase Officer</option>
            <option value="Accountant">Accountant / Cashier</option>
            <option value="Dispatch Clerk">Dispatch Clerk</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Temporary Password <span style="color:red;">*</span></label>
          <input type="password" name="password" class="form-control" required placeholder="Min 6 characters">
        </div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px; border-top:1px solid var(--slate-200); padding-top:14px;">
        <button type="button" class="btn btn-secondary" onclick="closeUserCreateModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">Create User</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  function openUserCreateModal() {
    document.getElementById('user-modal').style.display = 'flex';
  }
  function closeUserCreateModal() {
    document.getElementById('user-modal').style.display = 'none';
  }
</script>
@endpush
@endsection
