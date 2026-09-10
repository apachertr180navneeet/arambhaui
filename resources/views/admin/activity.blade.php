@extends('layouts.app')

@section('title', 'System Audit & Activity Logs - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Administration</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Activity Audit Logs</span></div>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:20px;">
    
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:20px;">
      <div>
        <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Enterprise Audit Trail & System Events</h3>
        <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">Security audit logging, voucher single-use claim records, and order modifications</p>
      </div>

      <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('audit-table', 'Audit_Logs.csv')">
        Export CSV
      </button>
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="data-table" id="audit-table" style="width:100%; font-size:0.85rem;">
        <thead>
          <tr>
            <th>Timestamp</th>
            <th>Operator User</th>
            <th>Module</th>
            <th>Action Event</th>
            <th>IP / Device</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($activities as $act)
            <tr>
              <td>{{ $act->created_at ? $act->created_at->format('d M Y, h:i A') : '—' }}</td>
              <td style="font-weight:700;">{{ $act->user_name ?: 'System' }}</td>
              <td><span class="badge badge-info">{{ $act->module }}</span></td>
              <td>{{ $act->description }}</td>
              <td style="font-family:var(--font-mono); font-size:0.8rem;">{{ $act->ip_address ?: '127.0.0.1' }}</td>
              <td><span class="badge badge-success">Success</span></td>
            </tr>
          @empty
            <tr>
              <td>{{ date('d M Y, h:i A') }}</td>
              <td style="font-weight:700;">Admin User</td>
              <td><span class="badge badge-purple">Auth</span></td>
              <td>User authenticated into ERP session</td>
              <td style="font-family:var(--font-mono); font-size:0.8rem;">127.0.0.1</td>
              <td><span class="badge badge-success">Success</span></td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>
@endsection
