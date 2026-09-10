@extends('layouts.app')

@section('title', 'Manufacturing Dashboard - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Dashboard</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Overview</span></div>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:24px;">

  <!-- Page Header Title & Actions -->
  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
    <div>
      <h2 style="margin:0; font-size:1.45rem; font-weight:800; color:var(--slate-900); letter-spacing:-0.4px;">
        Manufacturing & Supply Chain Overview
      </h2>
      <p style="margin:4px 0 0; color:var(--slate-500); font-size:0.875rem;">
        Real-time visibility into customer orders, job assignments, inventory, single-use QR vouchers, and dispatches.
      </p>
    </div>

    <div style="display:flex; gap:10px; flex-wrap:wrap;">
      <a href="{{ route('purchase.orders.create') }}" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:6px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        + New Purchase Order
      </a>
      <a href="{{ route('qr.scanner') }}" class="btn btn-secondary" style="display:inline-flex; align-items:center; gap:6px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/></svg>
        QR Scanner Portal
      </a>
    </div>
  </div>

  <!-- KPI Summary Cards Grid -->
  <div class="kpi-grid" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(230px, 1fr)); gap:16px;">
    <!-- Customers KPI -->
    <div class="kpi-card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:20px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:48px; height:48px; border-radius:12px; background:var(--primary-50); color:var(--primary-600); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Active Customers</div>
        <div style="font-size:1.5rem; font-weight:800; color:var(--slate-900); margin-top:2px;">{{ $stats['totalCustomers'] }}</div>
        <div style="font-size:0.75rem; color:var(--primary-600); margin-top:2px;"><a href="{{ route('masters.customers.index') }}" style="color:inherit; font-weight:600; text-decoration:none;">View Directory &rarr;</a></div>
      </div>
    </div>

    <!-- Purchase Orders KPI -->
    <div class="kpi-card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:20px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:48px; height:48px; border-radius:12px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Purchase Orders</div>
        <div style="font-size:1.5rem; font-weight:800; color:var(--slate-900); margin-top:2px;">{{ $stats['totalPOs'] }}</div>
        <div style="font-size:0.75rem; color:#2563eb; margin-top:2px;"><a href="{{ route('purchase.orders.index') }}" style="color:inherit; font-weight:600; text-decoration:none;">Manage Orders &rarr;</a></div>
      </div>
    </div>

    <!-- Active Single-Use QR Vouchers KPI -->
    <div class="kpi-card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:20px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:48px; height:48px; border-radius:12px; background:#f5f3ff; color:#7c3aed; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Single-Use QR Codes</div>
        <div style="font-size:1.5rem; font-weight:800; color:var(--slate-900); margin-top:2px;">{{ $stats['totalActiveVouchers'] }} <span style="font-size:0.75rem; font-weight:600; color:#059669; background:#ecfdf5; padding:2px 8px; border-radius:10px;">{{ $stats['totalRedeemedVouchers'] }} Claimed</span></div>
        <div style="font-size:0.75rem; color:#7c3aed; margin-top:2px;"><a href="{{ route('qr.history') }}" style="color:inherit; font-weight:600; text-decoration:none;">Voucher Ledger &rarr;</a></div>
      </div>
    </div>

    <!-- Items & Inventory KPI -->
    <div class="kpi-card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:20px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:48px; height:48px; border-radius:12px; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.29 7 12 12 20.71 7"/><line x1="12" y1="22" x2="12" y2="12"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Item SKUs Registered</div>
        <div style="font-size:1.5rem; font-weight:800; color:var(--slate-900); margin-top:2px;">{{ $stats['totalItems'] }}</div>
        <div style="font-size:0.75rem; color:#059669; margin-top:2px;"><a href="{{ route('masters.items.index') }}" style="color:inherit; font-weight:600; text-decoration:none;">Inventory Master &rarr;</a></div>
      </div>
    </div>
  </div>

  <!-- Quick Workflow Modules Hub -->
  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:22px; box-shadow:var(--shadow-sm);">
    <h3 style="font-size:1.05rem; font-weight:700; color:var(--slate-800); margin-top:0; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
      Quick Navigation Modules
    </h3>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:14px;">
      <a href="{{ route('masters.customers.index') }}" class="btn-card" style="text-decoration:none; padding:16px; border:1px solid var(--slate-200); border-radius:12px; background:#f8fafc; display:flex; flex-direction:column; gap:6px; transition:all 0.2s ease;">
        <div style="font-weight:700; color:var(--slate-800); font-size:0.925rem;">👥 Customer Master</div>
        <div style="font-size:0.8rem; color:var(--slate-500);">Clients, credit limits & statements</div>
      </a>

      <a href="{{ route('purchase.orders.index') }}" class="btn-card" style="text-decoration:none; padding:16px; border:1px solid var(--slate-200); border-radius:12px; background:#f8fafc; display:flex; flex-direction:column; gap:6px; transition:all 0.2s ease;">
        <div style="font-weight:700; color:var(--slate-800); font-size:0.925rem;">📦 Purchase Orders</div>
        <div style="font-size:0.8rem; color:var(--slate-500);">Raw materials & vendor procurement</div>
      </a>

      <a href="{{ route('jobwork.assign.index') }}" class="btn-card" style="text-decoration:none; padding:16px; border:1px solid var(--slate-200); border-radius:12px; background:#f8fafc; display:flex; flex-direction:column; gap:6px; transition:all 0.2s ease;">
        <div style="font-weight:700; color:var(--slate-800); font-size:0.925rem;">✂️ Job Work Assign</div>
        <div style="font-size:0.8rem; color:var(--slate-500);">Outward stitching & cutting issues</div>
      </a>

      <a href="{{ route('qr.scanner') }}" class="btn-card" style="text-decoration:none; padding:16px; border:1px solid var(--slate-200); border-radius:12px; background:#f8fafc; display:flex; flex-direction:column; gap:6px; transition:all 0.2s ease;">
        <div style="font-weight:700; color:var(--slate-800); font-size:0.925rem;">📱 QR Scanner & Claims</div>
        <div style="font-size:0.8rem; color:var(--slate-500);">Phone verification & single-use expiry</div>
      </a>

      <a href="{{ route('dispatch.dispatch') }}" class="btn-card" style="text-decoration:none; padding:16px; border:1px solid var(--slate-200); border-radius:12px; background:#f8fafc; display:flex; flex-direction:column; gap:6px; transition:all 0.2s ease;">
        <div style="font-weight:700; color:var(--slate-800); font-size:0.925rem;">🚚 Dispatch Challans</div>
        <div style="font-size:0.8rem; color:var(--slate-500);">Lorry receipts & shipping tracking</div>
      </a>

      <a href="{{ route('accounts.customer-accounts') }}" class="btn-card" style="text-decoration:none; padding:16px; border:1px solid var(--slate-200); border-radius:12px; background:#f8fafc; display:flex; flex-direction:column; gap:6px; transition:all 0.2s ease;">
        <div style="font-weight:700; color:var(--slate-800); font-size:0.925rem;">💳 Accounts Ledger</div>
        <div style="font-size:0.8rem; color:var(--slate-500);">Customer & vendor settlements</div>
      </a>
    </div>
  </div>

  <!-- Split Data Grid: Recent Job Work & Active QR Vouchers -->
  <div class="dashboard-split-grid" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(min(100%, 400px), 1fr)); gap:20px;">
    
    <!-- Recent Job Work Orders -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:20px; box-shadow:var(--shadow-sm);">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
        <h3 style="font-size:1rem; font-weight:700; color:var(--slate-800); margin:0;">Recent Job Assignments</h3>
        <a href="{{ route('jobwork.assign.index') }}" style="font-size:0.8rem; font-weight:600; color:var(--primary-600); text-decoration:none;">View All &rarr;</a>
      </div>

      <div class="table-responsive">
        <table class="data-table" style="width:100%; font-size:0.825rem;">
          <thead>
            <tr>
              <th>Order #</th>
              <th>Job Worker</th>
              <th>Process</th>
              <th>Qty (Pcs)</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($recentJobOrders as $jo)
              <tr>
                <td style="font-family:var(--font-mono); font-weight:600; color:var(--primary-600);">{{ $jo->job_order_no }}</td>
                <td style="font-weight:600;">{{ $jo->job_worker_name }}</td>
                <td><span class="badge badge-info">{{ $jo->process_name }}</span></td>
                <td style="font-weight:700;">{{ number_format($jo->issued_qty) }}</td>
                <td><span class="badge badge-success">{{ $jo->status }}</span></td>
              </tr>
            @empty
              <tr>
                <td colspan="5" style="text-align:center; padding:20px; color:var(--slate-400);">No job assignments recorded yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- Active Single-Use QR Vouchers -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:20px; box-shadow:var(--shadow-sm);">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
        <h3 style="font-size:1rem; font-weight:700; color:var(--slate-800); margin:0;">Single-Use Discount QR Vouchers</h3>
        <a href="{{ route('qr.history') }}" style="font-size:0.8rem; font-weight:600; color:var(--primary-600); text-decoration:none;">View Ledger &rarr;</a>
      </div>

      <div class="table-responsive">
        <table class="data-table" style="width:100%; font-size:0.825rem;">
          <thead>
            <tr>
              <th>Voucher Code</th>
              <th>Discount</th>
              <th>Phone</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($recentVouchers as $v)
              <tr>
                <td style="font-family:var(--font-mono); font-weight:700; color:#7c3aed;">{{ $v->voucher_code }}</td>
                <td style="font-weight:700; color:#059669;">
                  {{ $v->discount_type === 'Percentage' ? ($v->discount_percent . '% OFF') : ('₹' . number_format($v->discount_amount ?: $v->discount_percent)) }}
                </td>
                <td style="font-size:0.8rem; color:var(--slate-600);">{{ $v->customer_phone ?: '—' }}</td>
                <td>
                  @if($v->status === 'Active')
                    <span class="badge badge-success">Single-Use Active</span>
                  @elseif($v->status === 'Redeemed')
                    <span class="badge badge-danger">Redeemed & Expired</span>
                  @else
                    <span class="badge badge-secondary">{{ $v->status }}</span>
                  @endif
                </td>
                <td>
                  <a href="{{ route('qr.scanner') }}?code={{ $v->voucher_code }}" class="btn btn-secondary btn-xs" style="padding:4px 8px; font-size:0.75rem;">Scan & Claim</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" style="text-align:center; padding:20px; color:var(--slate-400);">No QR vouchers created yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </div>

</div>
@endsection
