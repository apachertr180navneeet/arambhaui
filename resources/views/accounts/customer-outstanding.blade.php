@extends('layouts.app')

@section('title', 'Customer Outstanding Aging - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Accounts & Settlements</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Customer Outstanding</span></div>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <!-- KPI Metrics Row -->
  <div class="kpi-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:#fef2f2; color:var(--danger-600); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Total Overdue Outstanding</div>
        <div style="font-size:1.45rem; font-weight:800; color:var(--danger-600); margin-top:2px;">₹{{ number_format($stats['totalOutstanding'], 2) }}</div>
      </div>
    </div>

    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:#fffbeb; color:#d97706; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Accounts with Dues</div>
        <div style="font-size:1.45rem; font-weight:800; color:var(--slate-900); margin-top:2px;">{{ $stats['dueAccounts'] }} Customers</div>
      </div>
    </div>
  </div>

  <!-- Main Card Container -->
  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:20px;">
    
    <!-- Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:20px;">
      <div>
        <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Customer Aging & Outstanding Ledger</h3>
        <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">Unsettled receivables, credit limit utilization, and collection tracking</p>
      </div>

      <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('cust-due-table', 'Customer_Outstanding.csv')">
        Export CSV
      </button>
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="data-table" id="cust-due-table" style="width:100%; font-size:0.85rem;">
        <thead>
          <tr>
            <th>Customer Code</th>
            <th>Customer Name</th>
            <th>Contact Phone</th>
            <th>City</th>
            <th>Credit Limit</th>
            <th>Current Outstanding</th>
            <th>Credit Used %</th>
            <th style="text-align:right;">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($customers as $c)
            @php
              $pct = $c->credit_limit > 0 ? round(($c->outstanding / $c->credit_limit) * 100, 1) : 0;
            @endphp
            <tr>
              <td style="font-family:var(--font-mono); font-weight:700; color:var(--primary-600);">{{ $c->code }}</td>
              <td style="font-weight:700; color:var(--slate-800);">{{ $c->name }}</td>
              <td>{{ $c->phone }}</td>
              <td>{{ $c->city ?: '—' }}</td>
              <td>₹{{ number_format($c->credit_limit, 2) }}</td>
              <td style="font-weight:800; color:var(--danger-600);">₹{{ number_format($c->outstanding, 2) }}</td>
              <td>
                <span class="badge {{ $pct >= 80 ? 'badge-danger' : ($pct >= 50 ? 'badge-warning' : 'badge-info') }}">
                  {{ $pct }}%
                </span>
              </td>
              <td style="text-align:right;">
                <a href="{{ route('accounts.customer-accounts') }}" class="btn btn-primary btn-xs">Settle</a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" style="text-align:center; padding:30px; color:var(--slate-400);">No overdue customer receivables at this time.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>
@endsection
