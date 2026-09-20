@extends('layouts.app')

@section('title', 'Job Work & Assignment - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Job Work & Assign</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Job Assign Orders</span></div>
@endsection

@push('styles')
<style>
  /* ==========================================================================
     JOB WORK ASSIGNMENT - MODERN ERP STYLING
     ========================================================================== */
  .jw-wrapper {
    display: flex;
    flex-direction: column;
    gap: 22px;
  }

  /* Stats Grid */
  .jw-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
  }

  .jw-stat-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid var(--slate-200, #e2e8f0);
    padding: 18px 20px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    display: flex;
    align-items: center;
    gap: 16px;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .jw-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.08);
  }

  .jw-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--accent-color, #4f46e5);
  }

  .jw-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .jw-stat-info {
    flex: 1;
    min-width: 0;
  }

  .jw-stat-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--slate-500, #64748b);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 2px;
  }

  .jw-stat-value {
    font-size: 1.55rem;
    font-weight: 800;
    color: var(--slate-900, #0f172a);
    line-height: 1.2;
    display: flex;
    align-items: baseline;
    gap: 8px;
  }

  /* Main Card */
  .jw-ledger-card {
    background: #ffffff;
    border-radius: 18px;
    border: 1px solid var(--slate-200, #e2e8f0);
    box-shadow: 0 2px 12px -2px rgba(15, 23, 42, 0.06);
    overflow: hidden;
  }

  .jw-card-header {
    padding: 22px 24px;
    border-bottom: 1px solid var(--slate-100, #f1f5f9);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
  }

  /* Filter Toolbar */
  .jw-toolbar {
    padding: 14px 24px;
    background: #fafafa;
    border-bottom: 1px solid var(--slate-200, #e2e8f0);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 14px;
  }

  .search-input-wrapper {
    position: relative;
    max-width: 320px;
    width: 100%;
  }

  .search-input-wrapper svg {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--slate-400, #94a3b8);
    pointer-events: none;
  }

  .search-input-wrapper input {
    padding-left: 36px;
    font-size: 0.85rem;
    border-radius: 10px;
    border: 1px solid var(--slate-300, #cbd5e1);
    background: #ffffff;
    width: 100%;
    height: 38px;
  }

  /* Process Filter Tabs */
  .process-filter-pills {
    display: flex;
    gap: 6px;
    background: var(--slate-200, #e2e8f0);
    padding: 3px;
    border-radius: 10px;
  }

  .process-filter-btn {
    border: none;
    background: transparent;
    padding: 6px 12px;
    font-size: 0.775rem;
    font-weight: 700;
    color: var(--slate-600, #475569);
    border-radius: 7px;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }

  .process-filter-btn.active {
    background: #ffffff;
    color: var(--primary-700, #4338ca);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }

  /* Code Mono Badge */
  .jw-code-badge {
    font-family: var(--font-mono, monospace);
    font-weight: 800;
    font-size: 0.85rem;
    background: #eef2ff;
    color: #4338ca;
    border: 1px solid #c7d2fe;
    padding: 4px 10px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.15s ease;
  }

  .jw-code-badge:hover {
    background: #e0e7ff;
    border-color: #a5b4fd;
    transform: translateY(-1px);
  }

  .lot-badge {
    font-family: var(--font-mono, monospace);
    font-weight: 800;
    font-size: 0.8rem;
    background: #fdf4ff;
    color: #a21caf;
    border: 1px solid #f0abfc;
    padding: 3px 8px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }

  .jw-table th {
    background: #f8fafc;
    color: var(--slate-600, #475569);
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 12px 14px;
    border-bottom: 1px solid var(--slate-200, #e2e8f0);
  }

  .jw-table td {
    padding: 12px 14px;
    vertical-align: middle;
    border-bottom: 1px solid var(--slate-100, #f1f5f9);
    font-size: 0.85rem;
  }

  .jw-table tr:hover td {
    background: #fafafa;
  }

  .action-icon-btn {
    width: 30px;
    height: 30px;
    border-radius: 6px;
    border: 1px solid var(--slate-200, #e2e8f0);
    background: #ffffff;
    color: var(--slate-600, #475569);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
  }

  .action-icon-btn:hover {
    background: var(--slate-100, #f1f5f9);
    color: var(--slate-900, #0f172a);
    border-color: var(--slate-300, #cbd5e1);
  }

  .action-icon-btn.info:hover {
    background: #eff6ff;
    color: #2563eb;
    border-color: #bfdbfe;
  }

  .action-icon-btn.danger:hover {
    background: #fef2f2;
    color: #dc2626;
    border-color: #fca5a5;
  }
</style>
@endpush

@section('content')
<div class="jw-wrapper">

  <!-- KPI Metrics Row -->
  <div class="jw-stats-grid">
    
    <!-- Total Job Orders -->
    <div class="jw-stat-card" style="--accent-color: #4f46e5;">
      <div class="jw-stat-icon" style="background: #eef2ff; color: #4f46e5;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
      </div>
      <div class="jw-stat-info">
        <div class="jw-stat-label">Job Assignment Orders</div>
        <div class="jw-stat-value">
          {{ number_format($stats['totalOrders']) }}
          <span style="font-size:0.75rem; font-weight:600; color:var(--slate-500);">Orders</span>
        </div>
      </div>
    </div>

    <!-- Total Than Fabric Issued -->
    <div class="jw-stat-card" style="--accent-color: #059669;">
      <div class="jw-stat-icon" style="background: #ecfdf5; color: #059669;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
      </div>
      <div class="jw-stat-info">
        <div class="jw-stat-label">Total Fabric Issued</div>
        <div class="jw-stat-value" style="color:#059669;">
          {{ number_format($stats['totalThanMeters'], 1) }}
          <span style="font-size:0.75rem; font-weight:600; color:var(--slate-500);">Mtrs</span>
        </div>
      </div>
    </div>

    <!-- Total Production Pieces -->
    <div class="jw-stat-card" style="--accent-color: #2563eb;">
      <div class="jw-stat-icon" style="background: #eff6ff; color: #2563eb;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
      </div>
      <div class="jw-stat-info">
        <div class="jw-stat-label">Target Production</div>
        <div class="jw-stat-value" style="color:#2563eb;">
          {{ number_format($stats['totalIssuedQty']) }}
          <span style="font-size:0.75rem; font-weight:600; color:var(--slate-500);">Pcs</span>
        </div>
      </div>
    </div>

    <!-- Contract Labor Value -->
    <div class="jw-stat-card" style="--accent-color: #7c3aed;">
      <div class="jw-stat-icon" style="background: #f5f3ff; color: #7c3aed;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      </div>
      <div class="jw-stat-info">
        <div class="jw-stat-label">Total Contract Value</div>
        <div class="jw-stat-value" style="color: #7c3aed;">
          ₹{{ number_format($stats['totalProcessValue'], 2) }}
        </div>
      </div>
    </div>

  </div>

  <!-- Main Card Container -->
  <div class="jw-ledger-card">
    
    <!-- Action Header -->
    <div class="jw-card-header">
      <div>
        <h3 style="margin:0; font-size:1.2rem; font-weight:800; color:var(--slate-900);">Job Work Assignment Registry</h3>
        <p style="margin:4px 0 0; font-size:0.825rem; color:var(--slate-500);">
          Outward job orders with auto-assigned Lot References and multi-item Than fabric-to-pieces yield tracking.
        </p>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <a href="{{ route('jobwork.inward.index') }}" class="btn btn-secondary btn-sm" style="display:inline-flex; align-items:center; gap:6px; font-weight:700;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Job Inwards
        </a>
        <a href="{{ route('jobwork.inward.create') }}" class="btn btn-sm" style="background:#059669; color:#fff; display:inline-flex; align-items:center; gap:6px; font-weight:700;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
          + Receive Inward
        </a>
        <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('job-assign-table', 'Job_Assignments.csv')" style="display:inline-flex; align-items:center; gap:6px; font-weight:700;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Export CSV
        </button>
        <a href="{{ route('jobwork.assign.create') }}" class="btn btn-primary btn-sm" style="display:inline-flex; align-items:center; gap:6px; font-weight:700; box-shadow:0 2px 8px rgba(79, 70, 229, 0.3);">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
          + Issue New Job Order
        </a>
      </div>
    </div>

    <!-- Quick Filter Bar -->
    <div class="jw-toolbar">
      <div class="search-input-wrapper">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="jw-search-input" placeholder="Search order#, lot#, worker, style..." onkeyup="filterJWTable()">
      </div>

      <div class="process-filter-pills">
        <button type="button" class="process-filter-btn active" onclick="setProcessFilter('all', this)">
          All ({{ $stats['totalOrders'] }})
        </button>
        <button type="button" class="process-filter-btn" onclick="setProcessFilter('stitching', this)">
          Stitching
        </button>
        <button type="button" class="process-filter-btn" onclick="setProcessFilter('cutting', this)">
          Cutting
        </button>
        <button type="button" class="process-filter-btn" onclick="setProcessFilter('embroidery', this)">
          Embroidery
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="data-table jw-table" id="job-assign-table" style="width:100%;">
        <thead>
          <tr>
            <th style="width:140px;">Order #</th>
            <th style="width:130px;">Lot #</th>
            <th>Job Worker</th>
            <th>Process</th>
            <th>Items / Styles</th>
            <th>Than (Mtr)</th>
            <th>Target Pcs</th>
            <th>Wastage</th>
            <th>Avg Cons</th>
            <th>Total Amount</th>
            <th>Status</th>
            <th style="text-align:right; width:120px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($assignments as $ja)
            @php
              $procKey = strtolower($ja->process_name);
              $than = (float)($ja->total_than_meters ?: 0);
              $pcs = (int)($ja->issued_qty ?: 0);
              $wastage = (float)($ja->total_wastage_meters ?: 0);
              $avgCons = $pcs > 0 ? max(0, ($than - $wastage) / $pcs) : 0;
            @endphp
            <tr class="jw-row" data-process="{{ $procKey }}">
              
              <!-- Order No Badge -->
              <td>
                <span class="jw-code-badge" onclick="copyJWCode('{{ $ja->job_order_no }}')" title="Click to copy Order #">
                  <span>{{ $ja->job_order_no }}</span>
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                </span>
                <div style="font-size:0.72rem; color:var(--slate-500); margin-top:2px;">
                  {{ date('d M Y', strtotime($ja->issue_date)) }}
                </div>
              </td>

              <!-- Lot Number Badge -->
              <td>
                <span class="lot-badge" title="Auto-Assigned Lot #">
                  <span>{{ $ja->lot_number }}</span>
                </span>
              </td>

              <!-- Job Worker -->
              <td>
                <div style="font-weight:700; color:var(--slate-900);">{{ $ja->job_worker_name }}</div>
                <div style="font-size:0.725rem; color:var(--slate-500);">Contractor Unit</div>
              </td>

              <!-- Process -->
              <td>
                <span class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-weight:700; padding:3px 8px; border-radius:6px;">
                  {{ $ja->process_name }}
                </span>
              </td>

              <!-- Items / Style Summary -->
              <td>
                <div style="font-weight:600; color:var(--slate-800); max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $ja->style_name }}">
                  {{ $ja->style_name }}
                </div>
                @if($ja->items && count($ja->items) > 1)
                  <span style="font-size:0.72rem; color:var(--primary-700); font-weight:700;">+{{ count($ja->items) }} items</span>
                @endif
              </td>

              <!-- Than Fabric Meters -->
              <td>
                <div style="font-weight:700; color:var(--slate-900);">
                  {{ $than > 0 ? number_format($than, 1) . ' Mtr' : '—' }}
                </div>
              </td>

              <!-- Target Output Pcs -->
              <td>
                <div style="font-weight:800; color:var(--primary-700); font-size:0.95rem;">
                  {{ number_format($pcs) }} pcs
                </div>
              </td>

              <!-- Wastage -->
              <td>
                <span style="font-weight:700; color:{{ $wastage > 0 ? '#dc2626' : 'var(--slate-400)' }};">
                  {{ $wastage > 0 ? number_format($wastage, 1) . ' Mtr' : '0 Mtr' }}
                </span>
              </td>

              <!-- Avg Consumption -->
              <td>
                <span style="font-size:0.775rem; font-weight:700; background:#f1f5f9; padding:2px 6px; border-radius:4px; color:var(--slate-700);">
                  {{ $avgCons > 0 ? number_format($avgCons, 2) . ' M/Pc' : '—' }}
                </span>
              </td>

              <!-- Total Amount -->
              <td>
                <div style="font-weight:800; color:#059669;">₹{{ number_format($ja->total_amount, 2) }}</div>
                <div style="font-size:0.7rem; color:var(--slate-500);">₹{{ number_format($ja->rate_per_piece, 1) }}/pc avg</div>
              </td>

              <!-- Status -->
              <td>
                <span class="badge" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; font-weight:700; padding:3px 8px; border-radius:9999px;">
                  ● {{ $ja->status }}
                </span>
              </td>

              <!-- Actions -->
              <td style="text-align:right;">
                <div style="display:inline-flex; gap:6px; align-items:center;">
                  
                  <!-- Receive Inward -->
                  <a href="{{ route('jobwork.inward.create', ['job_order_id' => $ja->id]) }}" class="action-icon-btn" style="color:#059669; background:#ecfdf5; border-color:#a7f3d0;" title="Receive Inward (Batch-wise Lot {{ $ja->lot_number }})">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                  </a>

                  <!-- View Breakdown Modal -->
                  <button type="button" class="action-icon-btn info" title="View Order & Fabric Breakdown" onclick='openJobOrderModal(@json($ja))'>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  </button>

                  <!-- Edit -->
                  <a href="{{ route('jobwork.assign.edit', $ja->id) }}" class="action-icon-btn" title="Edit Job Order">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                  </a>

                  <!-- Delete -->
                  <form action="{{ route('jobwork.assign.destroy', $ja->id) }}" method="POST" style="display:inline;" onsubmit="return handleDeleteJW(this, '{{ $ja->job_order_no }}', event)">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-icon-btn danger" title="Delete Job Order">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                    </button>
                  </form>
                </div>
              </td>

            </tr>
          @empty
            <tr>
              <td colspan="12" style="text-align:center; padding:48px 20px; color:var(--slate-400);">
                <div style="width:48px; height:48px; border-radius:12px; background:var(--slate-100); color:var(--slate-400); display:inline-flex; align-items:center; justify-content:center; margin-bottom:12px;">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                </div>
                <div style="font-weight:700; color:var(--slate-700); font-size:1rem; margin-bottom:4px;">No job work assignments yet</div>
                <p style="font-size:0.85rem; color:var(--slate-500); margin:0 0 16px;">Issue your first outward job work order with auto-lot reference and fabric yield tracking.</p>
                <a href="{{ route('jobwork.assign.create') }}" class="btn btn-primary btn-sm">+ Issue Job Order</a>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>

<!-- ==========================================================================
     JOB ORDER DETAILS & MULTI-ITEM BREAKDOWN MODAL
     ========================================================================== -->
<div class="modal-backdrop" id="jobOrderModal" style="display:none;" onclick="if(event.target===this) closeJobOrderModal()">
  <div class="modal-dialog modal-lg" style="max-width:760px;">
    
    <div class="modal-header">
      <div class="modal-title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        <span>Job Work Order Details: <span id="m-order-no" style="color:#4f46e5; font-family:var(--font-mono, monospace);">JA-2026-001</span></span>
      </div>
      <button type="button" class="modal-close-btn" onclick="closeJobOrderModal()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <div class="modal-body" style="padding:22px; display:flex; flex-direction:column; gap:18px;">
      
      <!-- Top Overview Grid -->
      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px; display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:14px; font-size:0.85rem;">
        <div>
          <span style="color:var(--slate-500); font-size:0.75rem; display:block;">Lot Reference #:</span>
          <strong id="m-lot-no" class="lot-badge" style="font-size:0.9rem; margin-top:2px;">LOT-2026-001</strong>
        </div>
        <div>
          <span style="color:var(--slate-500); font-size:0.75rem; display:block;">Job Worker / Contractor:</span>
          <strong id="m-worker-name" style="color:var(--slate-900);">—</strong>
        </div>
        <div>
          <span style="color:var(--slate-500); font-size:0.75rem; display:block;">Process / Operation:</span>
          <strong id="m-process-name" style="color:#1d4ed8;">—</strong>
        </div>
        <div>
          <span style="color:var(--slate-500); font-size:0.75rem; display:block;">Issue & Due Date:</span>
          <strong id="m-dates" style="color:var(--slate-800);">—</strong>
        </div>
      </div>

      <!-- Line Items Breakdown Table -->
      <div>
        <h4 style="font-size:0.85rem; font-weight:800; text-transform:uppercase; color:var(--slate-700); margin:0 0 8px;">
          Product Items & Fabric Yield Breakdown
        </h4>
        <div class="table-responsive">
          <table class="data-table" style="width:100%; font-size:0.825rem;">
            <thead>
              <tr style="background:#f1f5f9;">
                <th>Item / Style</th>
                <th>Than (Mtr)</th>
                <th>Production (Pcs)</th>
                <th>Wastage (Mtr)</th>
                <th>Avg Cons</th>
                <th>Rate (₹)</th>
                <th style="text-align:right;">Line Total</th>
              </tr>
            </thead>
            <tbody id="m-items-tbody">
              <!-- Populated dynamically via JS -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- Order Yield & Financial Footer -->
      <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:16px; display:grid; grid-template-columns:repeat(auto-fit, minmax(140px, 1fr)); gap:10px; font-size:0.85rem;">
        <div>
          <span style="color:#166534; font-size:0.75rem; display:block;">Total Than Fabric:</span>
          <strong id="m-total-than" style="color:#14532d; font-size:1rem;">—</strong>
        </div>
        <div>
          <span style="color:#166534; font-size:0.75rem; display:block;">Total Target Output:</span>
          <strong id="m-total-pcs" style="color:#14532d; font-size:1rem;">—</strong>
        </div>
        <div>
          <span style="color:#166534; font-size:0.75rem; display:block;">Total Wastage:</span>
          <strong id="m-total-wastage" style="color:#dc2626; font-size:1rem;">—</strong>
        </div>
        <div>
          <span style="color:#166534; font-size:0.75rem; display:block;">Overall Avg Yield:</span>
          <strong id="m-overall-avg" style="color:#14532d; font-size:1rem;">—</strong>
        </div>
        <div>
          <span style="color:#166534; font-size:0.75rem; display:block;">Contract Amount:</span>
          <strong id="m-grand-amount" style="color:#059669; font-size:1.15rem;">₹0.00</strong>
        </div>
      </div>

    </div>

    <div class="modal-footer" style="padding:14px 22px; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end;">
      <button type="button" class="btn btn-secondary" onclick="closeJobOrderModal()">Close</button>
    </div>

  </div>
</div>

@push('scripts')
<script>
  let currentProcessFilter = 'all';

  function copyJWCode(code) {
    navigator.clipboard.writeText(code);
    if (window.UI && UI.showToast) {
      UI.showToast('Copied', `Job Order ${code} copied!`, 'info');
    } else {
      alert(`Copied: ${code}`);
    }
  }

  function handleDeleteJW(form, orderNo, e) {
    e.preventDefault();
    if (confirm(`Are you sure you want to delete Job Order ${orderNo}?`)) {
      form.submit();
    }
    return false;
  }

  function setProcessFilter(proc, btnElem) {
    currentProcessFilter = proc;
    document.querySelectorAll('.process-filter-btn').forEach(btn => btn.classList.remove('active'));
    if (btnElem) btnElem.classList.add('active');
    filterJWTable();
  }

  function filterJWTable() {
    const query = (document.getElementById('jw-search-input').value || '').toLowerCase().trim();
    const rows = document.querySelectorAll('#job-assign-table tbody tr.jw-row');

    rows.forEach(row => {
      const rowProc = (row.getAttribute('data-process') || '').toLowerCase();
      const text = row.innerText.toLowerCase();

      const matchesProc = (currentProcessFilter === 'all') || rowProc.includes(currentProcessFilter);
      const matchesQuery = !query || text.includes(query);

      if (matchesProc && matchesQuery) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }

  function openJobOrderModal(ja) {
    document.getElementById('m-order-no').innerText = ja.job_order_no;
    document.getElementById('m-lot-no').innerText = ja.lot_number;
    document.getElementById('m-worker-name').innerText = ja.job_worker_name;
    document.getElementById('m-process-name').innerText = ja.process_name;
    document.getElementById('m-dates').innerText = `${ja.issue_date} &rarr; ${ja.due_date || 'N/A'}`;

    const tbody = document.getElementById('m-items-tbody');
    tbody.innerHTML = '';

    const than = parseFloat(ja.total_than_meters || 0);
    const pcs = parseInt(ja.issued_qty || 0);
    const wastage = parseFloat(ja.total_wastage_meters || 0);
    const net = Math.max(0, than - wastage);
    const avg = pcs > 0 ? (net / pcs) : 0;

    document.getElementById('m-total-than').innerText = than.toFixed(1) + ' Mtr';
    document.getElementById('m-total-pcs').innerText = pcs + ' Pcs';
    document.getElementById('m-total-wastage').innerText = wastage.toFixed(1) + ' Mtr';
    document.getElementById('m-overall-avg').innerText = avg.toFixed(2) + ' Mtr/Pc';
    document.getElementById('m-grand-amount').innerText = '₹' + Number(ja.total_amount || 0).toLocaleString('en-IN', { minimumFractionDigits: 2 });

    const items = ja.items && ja.items.length > 0 ? ja.items : [{
      item_name: ja.style_name,
      than_meters: than,
      production_pcs: pcs,
      wastage_meters: wastage,
      avg_consumption: avg,
      rate_per_piece: ja.rate_per_piece,
      total_amount: ja.total_amount
    }];

    items.forEach(it => {
      const itThan = parseFloat(it.than_meters || 0);
      const itPcs = parseInt(it.production_pcs || it.qty || 0);
      const itWastage = parseFloat(it.wastage_meters || 0);
      const itAvg = parseFloat(it.avg_consumption || (itPcs > 0 ? (itThan - itWastage) / itPcs : 0));
      const itRate = parseFloat(it.rate_per_piece || 0);
      const itTotal = parseFloat(it.total_amount || (itPcs * itRate));

      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td><strong>${it.item_name || ja.style_name}</strong></td>
        <td>${itThan.toFixed(1)} Mtr</td>
        <td><strong>${itPcs} Pcs</strong></td>
        <td style="color:#dc2626;">${itWastage.toFixed(1)} Mtr</td>
        <td><span class="badge" style="background:#f1f5f9; color:#334155;">${itAvg.toFixed(2)} M/Pc</span></td>
        <td>₹${itRate.toFixed(2)}</td>
        <td style="text-align:right; font-weight:700; color:#059669;">₹${itTotal.toLocaleString('en-IN', { minimumFractionDigits: 2 })}</td>
      `;
      tbody.appendChild(tr);
    });

    document.getElementById('jobOrderModal').style.display = 'flex';
  }

  function closeJobOrderModal() {
    document.getElementById('jobOrderModal').style.display = 'none';
  }
</script>
@endpush
@endsection
