@extends('layouts.app')

@section('title', 'Dispatch Register (Sales & Delivery) - aarambh')

@section('breadcrumb')
  <div class="breadcrumb-item"><a href="{{ route('dispatch.challans.index') }}" style="color:inherit; text-decoration:none;">Dispatch Challan</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Dispatch Register</span></div>
@endsection

@push('styles')
<style>
  .register-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
  }
  .register-card {
    background: #ffffff;
    border-radius: var(--radius-xl, 16px);
    border: 1px solid var(--slate-200, #e2e8f0);
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    padding: 22px;
  }
  .kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: 16px;
  }
  .kpi-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 18px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
  }
  .kpi-label {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
    letter-spacing: 0.04em;
    margin-bottom: 4px;
  }
  .kpi-value {
    font-size: 1.4rem;
    font-weight: 800;
    color: #0f172a;
    font-family: var(--font-mono, monospace);
  }
  .register-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.85rem;
  }
  .register-table th {
    background: #f8fafc;
    color: #334155;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.725rem;
    letter-spacing: 0.04em;
    padding: 10px 12px;
    border-bottom: 2px solid #cbd5e1;
  }
  .register-table td {
    padding: 11px 12px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }
  .register-table tbody tr:hover {
    background: #f8fafc;
  }
  .register-table tfoot td {
    background: #f8fafc;
    font-weight: 800;
    border-top: 2px solid #cbd5e1;
    border-bottom: 2px solid #cbd5e1;
    padding: 11px 12px;
  }
</style>
@endpush

@section('content')
<div class="register-container">

  <!-- Top Action Header -->
  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
    <div>
      <h2 style="margin:0; font-size:1.4rem; font-weight:800; color:var(--slate-900);">
        Dispatch Register (Sales & Shipping)
      </h2>
      <p style="margin:4px 0 0; font-size:0.85rem; color:var(--slate-500);">
        Outward sales delivery ledger, customer consignees, transporter LR details, cartons, and finished goods revenue.
      </p>
    </div>

    <div style="display:flex; gap:10px; align-items:center;">
      <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-secondary btn-sm" style="display:inline-flex; align-items:center; gap:6px; font-weight:700;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Export Excel (CSV)
      </a>
      <button class="btn btn-secondary btn-sm" onclick="window.print()" style="display:inline-flex; align-items:center; gap:6px; font-weight:700;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
        Print Register
      </button>
    </div>
  </div>

  <!-- KPI Aggregations -->
  <div class="kpi-grid">
    <div class="kpi-card">
      <div class="kpi-label">Total Challans</div>
      <div class="kpi-value" style="color:#2563eb;">{{ $challans->count() }}</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Total Qty Dispatched</div>
      <div class="kpi-value" style="color:#059669;">{{ number_format($totalQty) }} PCS</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Total Thans</div>
      <div class="kpi-value" style="color:#7c3aed;">{{ $totalThans }}</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Total Cartons</div>
      <div class="kpi-value" style="color:#d97706;">{{ $totalCartons }} CTN</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Total Revenue (₹)</div>
      <div class="kpi-value" style="color:#0f172a;">₹{{ number_format($totalAmount, 2) }}</div>
    </div>
  </div>

  <!-- Filter Toolbar (Note 3: Comprehensive filters) -->
  <div class="register-card" style="padding:16px 20px;">
    <form method="GET" action="{{ route('reports.lot-sales') }}" id="dispatch-report-filter-form" style="display:flex; flex-wrap:wrap; gap:12px; align-items:center;">
      
      <!-- Search Input -->
      <div style="min-width:180px; flex:1; max-width:240px;">
        <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">Search:</label>
        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search Challan #, Customer, LR..." class="form-control" style="height:38px; border-radius:8px;">
      </div>

      <!-- Customer / Consignee -->
      <div style="min-width:200px; flex:1; max-width:260px;">
        <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">Customer / Consignee:</label>
        <select name="customer_id" class="form-control" style="font-weight:700; height:38px; border-radius:8px;" onchange="document.getElementById('dispatch-report-filter-form').submit()">
          <option value="">-- All Customers --</option>
          @foreach($customers as $c)
            <option value="{{ $c->id }}" {{ ($customerId == $c->id) ? 'selected' : '' }}>
              {{ $c->name }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- Item Filter -->
      <div style="min-width:180px; flex:1; max-width:240px;">
        <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">Filter by Item:</label>
        <select name="item_id" class="form-control" style="font-weight:600; height:38px; border-radius:8px;" onchange="document.getElementById('dispatch-report-filter-form').submit()">
          <option value="">-- All Items --</option>
          @foreach($items as $itm)
            <option value="{{ $itm->id }}" {{ ($itemId == $itm->id) ? 'selected' : '' }}>
              {{ $itm->name }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- Status -->
      <div style="width:130px;">
        <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">Status:</label>
        <select name="status" class="form-control" style="font-weight:600; height:38px; border-radius:8px;" onchange="document.getElementById('dispatch-report-filter-form').submit()">
          <option value="all">All Status</option>
          <option value="Dispatched" {{ ($status === 'Dispatched') ? 'selected' : '' }}>Dispatched</option>
          <option value="Ready" {{ ($status === 'Ready') ? 'selected' : '' }}>Ready</option>
          <option value="Delivered" {{ ($status === 'Delivered') ? 'selected' : '' }}>Delivered</option>
        </select>
      </div>

      <!-- From Date -->
      <div style="width:130px;">
        <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">From Date:</label>
        <input type="date" name="from_date" value="{{ $fromDate ?? '' }}" class="form-control" style="height:38px; border-radius:8px;">
      </div>

      <!-- To Date -->
      <div style="width:130px;">
        <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">To Date:</label>
        <input type="date" name="to_date" value="{{ $toDate ?? '' }}" class="form-control" style="height:38px; border-radius:8px;">
      </div>

      <!-- Action Buttons -->
      <div style="display:flex; gap:8px;">
        <button type="submit" class="btn btn-primary btn-sm" style="font-weight:700; height:38px;">Apply Filter</button>
        @if($customerId || $itemId || $status || $fromDate || $toDate || !empty($search) || !empty($preset))
          <a href="{{ route('reports.lot-sales') }}" class="btn btn-secondary btn-sm" style="font-weight:700; height:38px; display:inline-flex; align-items:center;">Reset</a>
        @endif
      </div>

      <!-- Period Presets -->
      <div style="display:flex; align-items:center; gap:6px; margin-left:auto;">
        <a href="{{ route('reports.lot-sales', array_merge(request()->except(['from_date', 'to_date', 'preset']), ['preset' => 'today'])) }}" class="btn btn-sm {{ ($preset ?? '') === 'today' ? 'btn-primary' : 'btn-secondary' }}" style="font-weight:700; font-size:0.75rem;">Today</a>
        <a href="{{ route('reports.lot-sales', array_merge(request()->except(['from_date', 'to_date', 'preset']), ['preset' => 'this_month'])) }}" class="btn btn-sm {{ ($preset ?? '') === 'this_month' ? 'btn-primary' : 'btn-secondary' }}" style="font-weight:700; font-size:0.75rem;">This Month</a>
        <a href="{{ route('reports.lot-sales', array_merge(request()->except(['from_date', 'to_date', 'preset']), ['preset' => 'last_month'])) }}" class="btn btn-sm {{ ($preset ?? '') === 'last_month' ? 'btn-primary' : 'btn-secondary' }}" style="font-weight:700; font-size:0.75rem;">Last Month</a>
        <a href="{{ route('reports.lot-sales', array_merge(request()->except(['from_date', 'to_date', 'preset']), ['preset' => 'fy'])) }}" class="btn btn-sm {{ ($preset ?? '') === 'fy' ? 'btn-primary' : 'btn-secondary' }}" style="font-weight:700; font-size:0.75rem;">Current FY</a>
        <a href="{{ route('reports.lot-sales', array_merge(request()->except(['from_date', 'to_date', 'preset']), ['preset' => 'all'])) }}" class="btn btn-sm {{ ($preset ?? '') === 'all' ? 'btn-primary' : 'btn-secondary' }}" style="font-weight:700; font-size:0.75rem;">All Time</a>
      </div>

    </form>
  </div>

  <!-- Register Listing Table (Note 2: Clickable links to drill down) -->
  <div class="register-card" style="padding:0; overflow:hidden;">
    <div class="table-responsive">
      <table class="register-table" id="dispatch-register-table">
        <thead>
          <tr>
            <th style="width:50px; text-align:center;">S.No.</th>
            <th style="width:130px;">Challan #</th>
            <th style="width:105px;">Dispatch Date</th>
            <th>Customer / Consignee</th>
            <th style="width:130px;">Transporter</th>
            <th style="width:110px;">Bilty / LR #</th>
            <th style="width:75px; text-align:center;">Cartons</th>
            <th style="width:75px; text-align:center;">Thans</th>
            <th style="width:110px; text-align:right;">Dispatched Qty</th>
            <th style="width:120px; text-align:right;">Total Amount (₹)</th>
            <th style="width:95px; text-align:center;">Status</th>
            <th style="width:90px; text-align:center;">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($challans as $idx => $dc)
            @php
              $thCount = $dc->total_thans ?: $dc->items->sum('than_count');
            @endphp
            <tr>
              <td style="text-align:center; color:#64748b; font-weight:700;">{{ $idx + 1 }}</td>

              <!-- 1. Clickable Challan Link -->
              <td>
                <a href="{{ route('dispatch.challans.show', $dc->id) }}" style="font-weight:800; font-family:var(--font-mono, monospace); color:#1e40af; text-decoration:none; display:inline-flex; align-items:center; gap:4px;" title="View Delivery Challan">
                  <span>{{ $dc->challan_no }}</span>
                  <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                </a>
              </td>

              <!-- 2. Date -->
              <td style="color:#475569; font-weight:600;">
                {{ date('d-M-Y', strtotime($dc->dispatch_date)) }}
              </td>

              <!-- 3. Customer -->
              <td style="font-weight:700; color:#0f172a;">
                {{ $dc->customer_name }}
              </td>

              <!-- 4. Transporter -->
              <td style="color:#334155;">
                {{ $dc->transporter_name ?: '—' }}
              </td>

              <!-- 5. Bilty / LR # -->
              <td style="font-family:monospace; color:#475569;">
                {{ $dc->bilty_no ?: ($dc->lr_number ?: '—') }}
              </td>

              <!-- 6. Cartons -->
              <td style="text-align:center;">
                <span class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-weight:700;">
                  {{ $dc->total_cartons }} CTN
                </span>
              </td>

              <!-- 7. Thans -->
              <td style="text-align:center;">
                <span class="badge" style="background:#f5f3ff; color:#7c3aed; border:1px solid #ddd6fe; font-weight:700;">
                  {{ $thCount }}
                </span>
              </td>

              <!-- 8. Dispatched Qty -->
              <td style="text-align:right; font-weight:800; font-family:var(--font-mono, monospace); color:#059669;">
                {{ number_format($dc->total_qty) }}
              </td>

              <!-- 9. Total Amount -->
              <td style="text-align:right; font-weight:800; font-family:var(--font-mono, monospace); color:#0f172a;">
                ₹{{ number_format($dc->total_amount, 2) }}
              </td>

              <!-- 10. Status -->
              <td style="text-align:center;">
                <span class="badge" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; font-weight:700;">
                  ● {{ $dc->status ?: 'Dispatched' }}
                </span>
              </td>

              <!-- 11. Action: Drill Down Transaction -->
              <td style="text-align:center;">
                <a href="{{ route('dispatch.challans.show', $dc->id) }}" class="btn btn-secondary btn-sm" style="padding:4px 8px; font-weight:700; font-size:0.75rem; display:inline-flex; align-items:center; gap:4px;" title="View Challan Details">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  View
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="12" style="text-align:center; padding:48px 20px; color:#94a3b8;">
                <div style="font-size:2rem; margin-bottom:8px;">🚚</div>
                <div style="font-weight:800; font-size:1.05rem; color:#334155;">No Dispatch Records Found</div>
                <div style="font-size:0.825rem; color:#64748b; margin-top:4px;">
                  No sales delivery challans match the selected filters or search criteria.
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>

        <tfoot>
          <tr>
            <td colspan="6" style="text-transform:uppercase; letter-spacing:0.05em; font-size:0.825rem; color:#0f172a;">
              Total Summary ({{ $challans->count() }} {{ Str::plural('Challan', $challans->count()) }})
            </td>
            <td style="text-align:center; font-weight:800; color:#d97706;">
              {{ $totalCartons }} CTN
            </td>
            <td style="text-align:center; font-weight:800; color:#7c3aed;">
              {{ $totalThans }}
            </td>
            <td style="text-align:right; font-weight:800; font-family:var(--font-mono, monospace); color:#059669; font-size:0.95rem;">
              {{ number_format($totalQty) }}
            </td>
            <td style="text-align:right; font-weight:800; font-family:var(--font-mono, monospace); color:#0f172a; font-size:1.05rem;">
              ₹{{ number_format($totalAmount, 2) }}
            </td>
            <td colspan="2"></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

</div>
@endsection
