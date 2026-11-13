@extends('layouts.app')

@section('title', 'Job Work Register - aarambh')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Reports Hub</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Job Work Register</span></div>
@endsection

@push('styles')
<style>
  .register-card {
    background: #ffffff;
    border-radius: var(--radius-xl, 16px);
    border: 1px solid var(--slate-200, #e2e8f0);
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    overflow: hidden;
  }
  .register-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.85rem;
    background: #ffffff;
  }
  .register-table th {
    background: #f1f5f9;
    color: #334155;
    font-weight: 800;
    text-transform: uppercase;
    font-size: 0.725rem;
    letter-spacing: 0.05em;
    padding: 12px 14px;
    border-top: 1px solid #e2e8f0;
    border-bottom: 2px solid #cbd5e1;
    white-space: nowrap;
  }
  .register-table td {
    padding: 11px 14px;
    border-bottom: 1px solid #f1f5f9;
    color: #1e293b;
    vertical-align: middle;
  }
  .register-table tbody tr:hover {
    background-color: #f8fafc;
  }
  .register-table tfoot td {
    background: #f8fafc;
    border-top: 2px solid #cbd5e1;
    border-bottom: 2px solid #94a3b8;
    padding: 14px 16px;
    font-weight: 800;
  }
  .status-badge {
    padding: 3px 9px;
    border-radius: 9999px;
    font-size: 0.725rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }
  .status-pending {
    background: #fef3c7;
    color: #b45309;
    border: 1px solid #fde68a;
  }
  .status-partial {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
  }
  .status-completed {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
  }
  @media print {
    .app-sidebar, .app-header, .filter-section, .report-actions, .breadcrumb {
      display: none !important;
    }
    .app-content {
      padding: 0 !important;
      margin: 0 !important;
    }
    .register-card {
      border: none !important;
      box-shadow: none !important;
    }
  }
</style>
@endpush

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <!-- Company Report Header Banner -->
  <div style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:16px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; box-shadow:var(--shadow-sm);">
    <div style="display:flex; align-items:center; gap:12px;">
      <div style="width:42px; height:42px; border-radius:10px; background:var(--primary-50); color:var(--primary-700); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.2rem;">
        🧵
      </div>
      <div>
        <div style="font-weight:800; font-size:1.15rem; color:var(--slate-900);">{{ $companyName ?? 'aarambh' }}</div>
        <div style="font-size:0.775rem; color:var(--slate-500);">
          <strong>Job Work Register Report</strong> &bull; Complete Outward Issues, Confirmed Inward Receipts & Outstanding Balances
        </div>
      </div>
    </div>
    <div class="report-actions" style="display:flex; gap:8px;">
      <button class="btn btn-secondary btn-sm" onclick="window.print()" style="display:inline-flex; align-items:center; gap:6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
        Print
      </button>
      <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="btn btn-secondary btn-sm" style="display:inline-flex; align-items:center; gap:6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        Export PDF
      </a>
      <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-secondary btn-sm" style="display:inline-flex; align-items:center; gap:6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Export Excel (CSV)
      </a>
    </div>
  </div>

  <!-- KPI Summary Row: Issued, Received, Pending, Contract Amount -->
  <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap:16px;">
    
    <!-- Total Issued Quantity -->
    <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:16px 20px; box-shadow:var(--shadow-sm); display:flex; justify-content:space-between; align-items:center;">
      <div>
        <div style="font-size:0.75rem; text-transform:uppercase; color:#64748b; font-weight:700; letter-spacing:0.04em;">Total Issued Quantity</div>
        <div style="font-size:1.6rem; font-weight:800; color:#1e293b; margin-top:2px;">{{ number_format($totalIssuedAll) }} <span style="font-size:0.8rem; color:#64748b; font-weight:600;">PCS</span></div>
      </div>
      <div style="width:44px; height:44px; border-radius:10px; background:#f1f5f9; color:#475569; display:flex; align-items:center; justify-content:center; font-size:1.3rem;">📋</div>
    </div>

    <!-- Total Received Quantity -->
    <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:16px 20px; box-shadow:var(--shadow-sm); display:flex; justify-content:space-between; align-items:center;">
      <div>
        <div style="font-size:0.75rem; text-transform:uppercase; color:#64748b; font-weight:700; letter-spacing:0.04em;">Total Received Quantity</div>
        <div style="font-size:1.6rem; font-weight:800; color:#059669; margin-top:2px;">{{ number_format($totalReceivedAll) }} <span style="font-size:0.8rem; color:#059669; font-weight:600;">PCS</span></div>
      </div>
      <div style="width:44px; height:44px; border-radius:10px; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center; font-size:1.3rem;">✅</div>
    </div>

    <!-- Total Pending Quantity (Remaining) -->
    <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:16px 20px; box-shadow:var(--shadow-sm); display:flex; justify-content:space-between; align-items:center;">
      <div>
        <div style="font-size:0.75rem; text-transform:uppercase; color:#64748b; font-weight:700; letter-spacing:0.04em;">Total Remaining Pending</div>
        <div style="font-size:1.6rem; font-weight:800; color:#b45309; margin-top:2px;">{{ number_format($totalRemainingAll) }} <span style="font-size:0.8rem; color:#b45309; font-weight:600;">PCS</span></div>
      </div>
      <div style="width:44px; height:44px; border-radius:10px; background:#fef3c7; color:#b45309; display:flex; align-items:center; justify-content:center; font-size:1.3rem;">⏳</div>
    </div>

    <!-- Total Job Work Amount -->
    <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:16px 20px; box-shadow:var(--shadow-sm); display:flex; justify-content:space-between; align-items:center;">
      <div>
        <div style="font-size:0.75rem; text-transform:uppercase; color:#64748b; font-weight:700; letter-spacing:0.04em;">Total Job Work Value</div>
        <div style="font-size:1.6rem; font-weight:800; color:#4338ca; margin-top:2px;">₹{{ number_format($totalAmountAll, 2) }}</div>
      </div>
      <div style="width:44px; height:44px; border-radius:10px; background:#eef2ff; color:#4338ca; display:flex; align-items:center; justify-content:center; font-size:1.3rem;">💰</div>
    </div>

  </div>

  <!-- Main Register Table Card -->
  <div class="register-card">

    <!-- Filters Bar -->
    <div class="filter-section" style="background:#f8fafc; border-bottom:1px solid #e2e8f0; padding:16px 20px;">
      <form method="GET" action="{{ route('reports.job-work-register') }}" style="display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end;">
        
        <!-- Search Keyword -->
        <div style="min-width:180px; flex:1; max-width:240px;">
          <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">Search Keyword:</label>
          <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Order#, worker, style..." class="form-control" style="height:38px; border-radius:8px;">
        </div>

        <!-- Job Worker Filter -->
        <div style="min-width:180px; flex:1; max-width:240px;">
          <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">Job Worker / Contractor:</label>
          <select name="worker_id" class="form-control" style="height:38px; border-radius:8px; font-weight:600;">
            <option value="">-- All Contractors --</option>
            @foreach($jobworkers as $jw)
              <option value="{{ $jw->id }}" {{ $workerId == $jw->id ? 'selected' : '' }}>{{ $jw->name }} ({{ $jw->code }})</option>
            @endforeach
          </select>
        </div>

        <!-- Item Filter -->
        <div style="min-width:180px; flex:1; max-width:240px;">
          <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">Item / Material:</label>
          <select name="item_id" class="form-control" style="height:38px; border-radius:8px; font-weight:600;">
            <option value="">-- All Items --</option>
            @foreach($items as $itm)
              <option value="{{ $itm->id }}" {{ $itemId == $itm->id ? 'selected' : '' }}>{{ $itm->name }} ({{ $itm->code }})</option>
            @endforeach
          </select>
        </div>

        <!-- Status Filter: Pending, Partial, Completed -->
        <div style="width:140px;">
          <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">Status Filter:</label>
          <select name="status" class="form-control" style="height:38px; border-radius:8px; font-weight:700;">
            <option value="">-- All Statuses --</option>
            <option value="Pending" {{ $status === 'Pending' ? 'selected' : '' }}>Pending</option>
            <option value="Partial" {{ $status === 'Partial' ? 'selected' : '' }}>Partial</option>
            <option value="Completed" {{ $status === 'Completed' ? 'selected' : '' }}>Completed</option>
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
          @if($workerId || $itemId || $status || $fromDate || $toDate || !empty($search))
            <a href="{{ route('reports.job-work-register') }}" class="btn btn-secondary btn-sm" style="font-weight:700; height:38px; display:inline-flex; align-items:center;">Reset</a>
          @endif
        </div>

      </form>
    </div>

    <!-- Register Table -->
    <div class="table-responsive">
      <table class="register-table" id="job-work-register-table">
        <thead>
          <tr>
            <th style="width:130px;">Job Order #</th>
            <th style="width:105px;">Order Date</th>
            <th>Contractor / Worker</th>
            <th>Item Name</th>
            <th style="width:90px;">Item Code</th>
            <th style="width:95px; text-align:right;">Issued Qty</th>
            <th style="width:95px; text-align:right;">Received Qty</th>
            <th style="width:95px; text-align:right;">Remaining Qty</th>
            <th style="width:65px; text-align:center;">Unit</th>
            <th style="width:100px; text-align:right;">Rate (₹)</th>
            <th style="width:120px; text-align:right;">Total Amount (₹)</th>
            <th style="width:105px; text-align:center;">Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($registerRows as $r)
            @php
              $statusClass = 'status-pending';
              if ($r['status'] === 'Completed') {
                $statusClass = 'status-completed';
              } elseif ($r['status'] === 'Partial') {
                $statusClass = 'status-partial';
              }
            @endphp
            <tr>
              <!-- 1. Job Order Number with Direct Drill-Down Link -->
              <td>
                <a href="{{ route('jobwork.assign.edit', $r['order_id']) }}" target="_blank" style="font-weight:800; font-family:var(--font-mono, monospace); color:#4338ca; text-decoration:none; display:inline-flex; align-items:center; gap:4px;" title="View & Edit Job Order">
                  <span>{{ $r['job_order_no'] }}</span>
                  <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                </a>
                @if(!empty($r['lot_number']))
                  <div style="font-size:0.7rem; color:#64748b; font-family:monospace;">Lot: {{ $r['lot_number'] }}</div>
                @endif
              </td>

              <!-- 2. Job Order Date -->
              <td style="color:#475569; font-weight:600;">
                {{ $r['job_order_date'] }}
              </td>

              <!-- 3. Job Worker / Contractor Name -->
              <td>
                <div style="font-weight:700; color:#0f172a;">{{ $r['job_worker_name'] }}</div>
                @if(!empty($r['worker_code']))
                  <div style="font-size:0.7rem; color:#64748b; font-family:monospace;">{{ $r['worker_code'] }}</div>
                @endif
              </td>

              <!-- 4. Item Name -->
              <td style="font-weight:700; color:#1e293b;">
                {{ $r['item_name'] }}
              </td>

              <!-- 5. Item Code -->
              <td style="font-family:var(--font-mono, monospace); font-size:0.775rem; color:#64748b; font-weight:600;">
                {{ $r['item_code'] }}
              </td>

              <!-- 6. Issued Quantity -->
              <td style="text-align:right; font-weight:800; font-family:var(--font-mono, monospace); color:#1e293b;">
                {{ number_format($r['issued_qty']) }}
              </td>

              <!-- 7. Received Quantity -->
              <td style="text-align:right; font-weight:800; font-family:var(--font-mono, monospace); color:#059669;">
                {{ number_format($r['received_qty']) }}
              </td>

              <!-- 8. Remaining Quantity (Issued - Total Received) -->
              <td style="text-align:right; font-weight:800; font-family:var(--font-mono, monospace); color:{{ $r['remaining_qty'] > 0 ? '#b45309' : '#059669' }};">
                {{ number_format($r['remaining_qty']) }}
              </td>

              <!-- 9. Unit -->
              <td style="text-align:center;">
                <span style="font-size:0.75rem; font-weight:700; background:#f1f5f9; color:#475569; padding:2px 6px; border-radius:4px;">
                  {{ $r['unit'] }}
                </span>
              </td>

              <!-- 10. Contractor Rate -->
              <td style="text-align:right; font-family:var(--font-mono, monospace); font-weight:600; color:#334155;">
                ₹{{ number_format($r['contractor_rate'], 2) }}
              </td>

              <!-- 11. Total Job Work Amount -->
              <td style="text-align:right; font-weight:800; font-family:var(--font-mono, monospace); color:#4338ca;">
                ₹{{ number_format($r['total_amount'], 2) }}
              </td>

              <!-- 12. Job Work Status -->
              <td style="text-align:center;">
                <span class="status-badge {{ $statusClass }}">
                  ● {{ $r['status'] }}
                </span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="12" style="text-align:center; padding:48px 20px; color:#94a3b8;">
                <div style="font-size:2rem; margin-bottom:8px;">🧵</div>
                <div style="font-weight:800; font-size:1.05rem; color:#334155;">No Job Work Orders Found</div>
                <div style="font-size:0.825rem; color:#64748b; margin-top:4px;">
                  No orders match the selected filters or search criteria.
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>

        <!-- Totals Footer -->
        <tfoot>
          <tr>
            <td colspan="5" style="text-transform:uppercase; letter-spacing:0.05em; font-size:0.825rem; color:#0f172a;">
              Total Summary ({{ count($registerRows) }} Line {{ Str::plural('Entry', count($registerRows)) }})
            </td>
            <td style="text-align:right; font-weight:800; font-family:var(--font-mono, monospace); color:#1e293b; font-size:0.95rem;">
              {{ number_format($totalIssuedAll) }}
            </td>
            <td style="text-align:right; font-weight:800; font-family:var(--font-mono, monospace); color:#059669; font-size:0.95rem;">
              {{ number_format($totalReceivedAll) }}
            </td>
            <td style="text-align:right; font-weight:800; font-family:var(--font-mono, monospace); color:#b45309; font-size:0.95rem;">
              {{ number_format($totalRemainingAll) }}
            </td>
            <td style="text-align:center; font-size:0.8rem; font-weight:700; color:#64748b;">PCS</td>
            <td></td>
            <td style="text-align:right; font-weight:800; font-family:var(--font-mono, monospace); color:#4338ca; font-size:1.05rem;">
              ₹{{ number_format($totalAmountAll, 2) }}
            </td>
            <td></td>
          </tr>
        </tfoot>
      </table>
    </div>

  </div>

</div>
@endsection

