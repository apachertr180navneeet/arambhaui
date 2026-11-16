@extends('layouts.app')

@section('title', 'Job Inward Register - aarambh')

@section('breadcrumb')
  <div class="breadcrumb-item"><a href="{{ route('jobwork.inward.index') }}" style="color:inherit; text-decoration:none;">Jobwork Inward</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Job Inward Register</span></div>
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
  .status-badge {
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 700;
    display: inline-block;
  }
  .status-badge.passed {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
  }
  .status-badge.pending {
    background: #fffbeb;
    color: #d97706;
    border: 1px solid #fde68a;
  }
  .status-badge.rejected {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
  }
</style>
@endpush

@section('content')
<div class="register-container">

  <!-- Top Action Header -->
  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
    <div>
      <h2 style="margin:0; font-size:1.4rem; font-weight:800; color:var(--slate-900);">
        Job Inward Register
      </h2>
      <p style="margin:4px 0 0; font-size:0.85rem; color:var(--slate-500);">
        Complete chronological audit ledger of garment bundles and stitched garments received from job workers.
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

  <!-- KPI Aggregation Banner -->
  <div class="kpi-grid">
    <div class="kpi-card">
      <div class="kpi-label">Total Inward Receipts</div>
      <div class="kpi-value" style="color:#0284c7;">{{ $inwards->count() }}</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Total Qty Received</div>
      <div class="kpi-value" style="color:#059669;">{{ number_format($totalReceived) }} PCS</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Total Thans</div>
      <div class="kpi-value" style="color:#7c3aed;">{{ $totalThans }}</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Total Wastage Returned</div>
      <div class="kpi-value" style="color:#d97706;">{{ number_format($totalWastage, 2) }} MTR</div>
    </div>
  </div>

  <!-- Filter Toolbar (Note 3: Comprehensive filters) -->
  <div class="register-card" style="padding:16px 20px;">
    <form method="GET" action="{{ route('jobwork.inward-report') }}" id="inward-report-filter-form" style="display:flex; flex-wrap:wrap; gap:12px; align-items:center;">
      
      <!-- Search Input -->
      <div style="min-width:180px; flex:1; max-width:240px;">
        <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">Search:</label>
        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search Inward #, Order #, Worker..." class="form-control" style="height:38px; border-radius:8px;">
      </div>

      <!-- Contractor / Worker -->
      <div style="min-width:200px; flex:1; max-width:260px;">
        <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">Job Worker:</label>
        <select name="worker_id" class="form-control" style="font-weight:700; height:38px; border-radius:8px;" onchange="document.getElementById('inward-report-filter-form').submit()">
          <option value="">-- All Job Workers --</option>
          @foreach($jobworkers as $w)
            <option value="{{ $w->id }}" {{ ($workerId == $w->id) ? 'selected' : '' }}>
              {{ $w->name }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- QC Status -->
      <div style="width:130px;">
        <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">QC Status:</label>
        <select name="qc_status" class="form-control" style="font-weight:600; height:38px; border-radius:8px;" onchange="document.getElementById('inward-report-filter-form').submit()">
          <option value="all">All QC</option>
          <option value="Passed" {{ ($qcStatus === 'Passed') ? 'selected' : '' }}>Passed</option>
          <option value="Pending" {{ ($qcStatus === 'Pending') ? 'selected' : '' }}>Pending</option>
          <option value="Rejected" {{ ($qcStatus === 'Rejected') ? 'selected' : '' }}>Rejected</option>
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
        @if($workerId || $qcStatus || $fromDate || $toDate || !empty($search) || !empty($preset))
          <a href="{{ route('jobwork.inward-report') }}" class="btn btn-secondary btn-sm" style="font-weight:700; height:38px; display:inline-flex; align-items:center;">Reset</a>
        @endif
      </div>

      <!-- Period Presets -->
      <div style="display:flex; align-items:center; gap:6px; margin-left:auto;">
        <a href="{{ route('jobwork.inward-report', array_merge(request()->except(['from_date', 'to_date', 'preset']), ['preset' => 'today'])) }}" class="btn btn-sm {{ ($preset ?? '') === 'today' ? 'btn-primary' : 'btn-secondary' }}" style="font-weight:700; font-size:0.75rem;">Today</a>
        <a href="{{ route('jobwork.inward-report', array_merge(request()->except(['from_date', 'to_date', 'preset']), ['preset' => 'this_month'])) }}" class="btn btn-sm {{ ($preset ?? '') === 'this_month' ? 'btn-primary' : 'btn-secondary' }}" style="font-weight:700; font-size:0.75rem;">This Month</a>
        <a href="{{ route('jobwork.inward-report', array_merge(request()->except(['from_date', 'to_date', 'preset']), ['preset' => 'last_month'])) }}" class="btn btn-sm {{ ($preset ?? '') === 'last_month' ? 'btn-primary' : 'btn-secondary' }}" style="font-weight:700; font-size:0.75rem;">Last Month</a>
        <a href="{{ route('jobwork.inward-report', array_merge(request()->except(['from_date', 'to_date', 'preset']), ['preset' => 'fy'])) }}" class="btn btn-sm {{ ($preset ?? '') === 'fy' ? 'btn-primary' : 'btn-secondary' }}" style="font-weight:700; font-size:0.75rem;">Current FY</a>
        <a href="{{ route('jobwork.inward-report', array_merge(request()->except(['from_date', 'to_date', 'preset']), ['preset' => 'all'])) }}" class="btn btn-sm {{ ($preset ?? '') === 'all' ? 'btn-primary' : 'btn-secondary' }}" style="font-weight:700; font-size:0.75rem;">All Time</a>
      </div>

    </form>
  </div>

  <!-- Register Listing Table (Note 2: Clickable links to drill down) -->
  <div class="register-card" style="padding:0; overflow:hidden;">
    <div class="table-responsive">
      <table class="register-table" id="job-inward-register-table">
        <thead>
          <tr>
            <th style="width:50px; text-align:center;">S.No.</th>
            <th style="width:130px;">Inward #</th>
            <th style="width:105px;">Receipt Date</th>
            <th style="width:130px;">Job Order #</th>
            <th>Contractor / Job Worker</th>
            <th style="width:110px;">Challan #</th>
            <th>Product / Style</th>
            <th style="width:95px; text-align:right;">Qty Received</th>
            <th style="width:70px; text-align:center;">THAN</th>
            <th style="width:105px; text-align:right;">Wastage (Mtr)</th>
            <th style="width:95px; text-align:center;">QC Status</th>
            <th style="width:90px; text-align:center;">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($inwards as $idx => $inw)
            @php
              $thCount = 0;
              if (!empty($inw->items_list) && is_array($inw->items_list)) {
                foreach ($inw->items_list as $it) {
                  $thCount += (int)($it['than_count'] ?? (count($it['assigned_thans'] ?? []) ?: 1));
                }
              } else {
                $thCount = (int)$inw->total_thans ?: 1;
              }

              $qc = strtolower($inw->qc_status ?? 'passed');
              $badgeClass = 'passed';
              if (strpos($qc, 'pend') !== false) $badgeClass = 'pending';
              elseif (strpos($qc, 'reject') !== false || strpos($qc, 'fail') !== false) $badgeClass = 'rejected';
            @endphp
            <tr>
              <td style="text-align:center; color:#64748b; font-weight:700;">{{ $idx + 1 }}</td>

              <!-- 1. Clickable Inward Link -->
              <td>
                <a href="{{ route('jobwork.inward.show', $inw->id) }}" style="font-weight:800; font-family:var(--font-mono, monospace); color:#0284c7; text-decoration:none; display:inline-flex; align-items:center; gap:4px;" title="View Inward Slip">
                  <span>{{ $inw->inward_number }}</span>
                  <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                </a>
              </td>

              <!-- 2. Date -->
              <td style="color:#475569; font-weight:600;">
                {{ date('d-M-Y', strtotime($inw->inward_date)) }}
              </td>

              <!-- 3. Job Order # Link -->
              <td>
                @if($inw->job_assignment_id)
                  <a href="{{ route('jobwork.assign.show', $inw->job_assignment_id) }}" style="font-weight:700; font-family:var(--font-mono, monospace); color:#4338ca; text-decoration:none;">
                    {{ $inw->job_order_no }}
                  </a>
                @else
                  <span style="font-family:monospace; color:#475569;">{{ $inw->job_order_no ?: '—' }}</span>
                @endif
              </td>

              <!-- 4. Job Worker -->
              <td style="font-weight:700; color:#0f172a;">
                {{ $inw->job_worker_name }}
              </td>

              <!-- 5. Challan No -->
              <td style="font-family:monospace; color:#475569;">
                {{ $inw->challan_no ?: '—' }}
              </td>

              <!-- 6. Style Name -->
              <td style="font-weight:700; color:#1e293b;">
                {{ $inw->style_name ?: 'Garment Product' }}
              </td>

              <!-- 7. Received Qty -->
              <td style="text-align:right; font-weight:800; font-family:var(--font-mono, monospace); color:#059669;">
                {{ number_format($inw->received_qty) }}
              </td>

              <!-- 8. Than Count -->
              <td style="text-align:center;">
                <span class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-weight:700;">
                  {{ $thCount }}
                </span>
              </td>

              <!-- 9. Wastage Returned -->
              <td style="text-align:right; font-family:var(--font-mono, monospace); color:#d97706; font-weight:700;">
                {{ number_format($inw->wastage_returned_meters, 2) }}
              </td>

              <!-- 10. QC Status -->
              <td style="text-align:center;">
                <span class="status-badge {{ $badgeClass }}">
                  ● {{ $inw->qc_status ?: 'Passed' }}
                </span>
              </td>

              <!-- 11. Action: Drill Down Transaction -->
              <td style="text-align:center;">
                <a href="{{ route('jobwork.inward.show', $inw->id) }}" class="btn btn-secondary btn-sm" style="padding:4px 8px; font-weight:700; font-size:0.75rem; display:inline-flex; align-items:center; gap:4px;" title="View Inward Slip">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  View
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="12" style="text-align:center; padding:48px 20px; color:#94a3b8;">
                <div style="font-size:2rem; margin-bottom:8px;">📦</div>
                <div style="font-weight:800; font-size:1.05rem; color:#334155;">No Job Inward Receipts Found</div>
                <div style="font-size:0.825rem; color:#64748b; margin-top:4px;">
                  No receipts match the selected filters or search criteria.
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>

        <tfoot>
          <tr>
            <td colspan="7" style="text-transform:uppercase; letter-spacing:0.05em; font-size:0.825rem; color:#0f172a;">
              Total Summary ({{ $inwards->count() }} Inward {{ Str::plural('Receipt', $inwards->count()) }})
            </td>
            <td style="text-align:right; font-weight:800; font-family:var(--font-mono, monospace); color:#059669; font-size:0.95rem;">
              {{ number_format($totalReceived) }}
            </td>
            <td style="text-align:center; color:#7c3aed; font-weight:800;">
              {{ $totalThans }}
            </td>
            <td style="text-align:right; font-family:var(--font-mono, monospace); color:#d97706; font-weight:800; font-size:0.95rem;">
              {{ number_format($totalWastage, 2) }}
            </td>
            <td colspan="2"></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

</div>
@endsection
