@extends('layouts.app')

@section('title', 'Dispatch Challans - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Shipping</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Dispatch Challans</span></div>
@endsection

@push('styles')
<style>
  /* ==========================================================================
     DISPATCH CHALLANS - MODERN ERP STYLING
     ========================================================================== */
  .dispatch-wrapper {
    display: flex;
    flex-direction: column;
    gap: 22px;
  }

  /* Stats Grid */
  .dispatch-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 16px;
  }

  .dispatch-stat-card {
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

  .dispatch-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.08);
  }

  .dispatch-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--accent-color, #2563eb);
  }

  .dispatch-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .dispatch-stat-info {
    flex: 1;
    min-width: 0;
  }

  .dispatch-stat-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--slate-500, #64748b);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 2px;
  }

  .dispatch-stat-value {
    font-size: 1.55rem;
    font-weight: 800;
    color: var(--slate-900, #0f172a);
    line-height: 1.2;
    display: flex;
    align-items: baseline;
    gap: 8px;
  }

  /* Main Card */
  .dispatch-ledger-card {
    background: #ffffff;
    border-radius: 18px;
    border: 1px solid var(--slate-200, #e2e8f0);
    box-shadow: 0 2px 12px -2px rgba(15, 23, 42, 0.06);
    overflow: hidden;
  }

  .dispatch-card-header {
    padding: 22px 24px;
    border-bottom: 1px solid var(--slate-100, #f1f5f9);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
  }

  /* Filter Toolbar */
  .dispatch-toolbar {
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

  /* Status Filter Tabs */
  .status-filter-pills {
    display: flex;
    gap: 6px;
    background: var(--slate-200, #e2e8f0);
    padding: 3px;
    border-radius: 10px;
  }

  .status-filter-btn {
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

  .status-filter-btn.active {
    background: #ffffff;
    color: var(--primary-700, #4338ca);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }

  /* Code Mono Badge */
  .dc-code-badge {
    font-family: var(--font-mono, monospace);
    font-weight: 800;
    font-size: 0.85rem;
    background: #f0fdf4;
    color: #15803d;
    border: 1px solid #bbf7d0;
    padding: 4px 10px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.15s ease;
  }

  .dc-code-badge:hover {
    background: #dcfce7;
    border-color: #86efac;
    transform: translateY(-1px);
  }

  /* Table Custom Row styling */
  .dispatch-table th {
    background: #f8fafc;
    color: var(--slate-600, #475569);
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 12px 18px;
    border-bottom: 1px solid var(--slate-200, #e2e8f0);
  }

  .dispatch-table td {
    padding: 14px 18px;
    vertical-align: middle;
    border-bottom: 1px solid var(--slate-100, #f1f5f9);
    font-size: 0.85rem;
  }

  .dispatch-table tr:hover td {
    background: #fafafa;
  }

  /* Action Buttons */
  .action-icon-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
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

  .action-icon-btn.danger:hover {
    background: #fef2f2;
    color: #dc2626;
    border-color: #fca5a5;
  }
</style>
@endpush

@section('content')
<div class="dispatch-wrapper">

  <!-- KPI Metrics Row -->
  <div class="dispatch-stats-grid">
    
    <!-- Total Challans -->
    <div class="dispatch-stat-card" style="--accent-color: #2563eb;">
      <div class="dispatch-stat-icon" style="background: #eff6ff; color: #2563eb;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
      </div>
      <div class="dispatch-stat-info">
        <div class="dispatch-stat-label">Delivery Challans Issued</div>
        <div class="dispatch-stat-value">
          {{ number_format($stats['totalChallans']) }}
          <span style="font-size:0.75rem; font-weight:600; color:var(--slate-500);">Challans</span>
        </div>
      </div>
    </div>

    <!-- Total Dispatched Pcs -->
    <div class="dispatch-stat-card" style="--accent-color: #10b981;">
      <div class="dispatch-stat-icon" style="background: #ecfdf5; color: #059669;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
      </div>
      <div class="dispatch-stat-info">
        <div class="dispatch-stat-label">Total Dispatched Units</div>
        <div class="dispatch-stat-value" style="color: #059669;">
          {{ number_format($stats['totalDispatchedQty']) }}
          <span style="font-size:0.75rem; font-weight:600; color:var(--slate-500);">Pcs</span>
        </div>
      </div>
    </div>

    <!-- In Transit -->
    <div class="dispatch-stat-card" style="--accent-color: #f59e0b;">
      <div class="dispatch-stat-icon" style="background: #fffbeb; color: #d97706;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      </div>
      <div class="dispatch-stat-info">
        <div class="dispatch-stat-label">In-Transit Shipments</div>
        <div class="dispatch-stat-value" style="color: #d97706;">
          {{ number_format($stats['inTransit']) }}
          <span style="font-size:0.75rem; background:#fffbeb; color:#d97706; border:1px solid #fde68a; padding:2px 8px; border-radius:9999px; font-weight:700;">On Road</span>
        </div>
      </div>
    </div>

    <!-- Delivered -->
    <div class="dispatch-stat-card" style="--accent-color: #8b5cf6;">
      <div class="dispatch-stat-icon" style="background: #f5f3ff; color: #7c3aed;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      </div>
      <div class="dispatch-stat-info">
        <div class="dispatch-stat-label">Delivered Shipments</div>
        <div class="dispatch-stat-value" style="color: #7c3aed;">
          {{ number_format($stats['delivered'] ?? 0) }}
          <span style="font-size:0.75rem; font-weight:600; color:var(--slate-500);">Closed</span>
        </div>
      </div>
    </div>

  </div>

  <!-- Main Ledger Card Container -->
  <div class="dispatch-ledger-card">
    
    <!-- Action Header -->
    <div class="dispatch-card-header">
      <div>
        <h3 style="margin:0; font-size:1.2rem; font-weight:800; color:var(--slate-900);">Dispatch Challans Registry</h3>
        <p style="margin:4px 0 0; font-size:0.825rem; color:var(--slate-500);">
          Lorry receipt numbers (LR), transporter logistics, vehicle numbers, and consignee delivery paperwork.
        </p>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('challans-table', 'Dispatch_Challans.csv')" style="display:inline-flex; align-items:center; gap:6px; font-weight:700;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Export CSV
        </button>
        <a href="{{ route('dispatch.challans.create') }}" class="btn btn-primary btn-sm" style="display:inline-flex; align-items:center; gap:6px; font-weight:700; box-shadow:0 2px 8px rgba(37, 99, 235, 0.3);">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
          Generate Delivery Challan
        </a>
      </div>
    </div>

    <!-- Quick Filter Bar -->
    <div class="dispatch-toolbar">
      <div class="search-input-wrapper">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="dc-search-input" placeholder="Search DC#, customer, LR#, transporter..." onkeyup="filterDCTable()">
      </div>

      <div class="status-filter-pills">
        <button type="button" class="status-filter-btn active" onclick="setStatusFilter('all', this)">
          All ({{ $stats['totalChallans'] }})
        </button>
        <button type="button" class="status-filter-btn" onclick="setStatusFilter('in transit', this)">
          In Transit ({{ $stats['inTransit'] }})
        </button>
        <button type="button" class="status-filter-btn" onclick="setStatusFilter('delivered', this)">
          Delivered
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="data-table dispatch-table" id="challans-table" style="width:100%;">
        <thead>
          <tr>
            <th style="width:160px;">Challan #</th>
            <th>Customer / Consignee</th>
            <th>Order Ref</th>
            <th>Dispatch Date</th>
            <th>Transporter</th>
            <th>LR Number</th>
            <th>Vehicle #</th>
            <th>Cartons</th>
            <th>Total Qty</th>
            <th>Status</th>
            <th style="text-align:right; width:110px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($challans as $dc)
            @php
              $statusKey = strtolower($dc->status);
            @endphp
            <tr class="dc-row" data-status="{{ $statusKey }}">
              
              <!-- Challan Badge -->
              <td>
                <span class="dc-code-badge" onclick="copyDCCode('{{ $dc->challan_no }}')" title="Click to copy Challan #">
                  <span>{{ $dc->challan_no }}</span>
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                </span>
              </td>

              <!-- Customer -->
              <td>
                <div style="font-weight:700; color:var(--slate-900);">{{ $dc->customer_name }}</div>
                @if($dc->destination_city)
                  <div style="font-size:0.725rem; color:var(--slate-500); margin-top:2px;">📍 {{ $dc->destination_city }}</div>
                @endif
              </td>

              <!-- Order Ref -->
              <td>
                <span style="font-family:var(--font-mono, monospace); font-weight:600; background:#f1f5f9; padding:2px 6px; border-radius:4px; font-size:0.75rem;">
                  {{ $dc->order_no }}
                </span>
              </td>

              <!-- Date -->
              <td>{{ date('d M Y', strtotime($dc->dispatch_date)) }}</td>

              <!-- Transporter -->
              <td>
                <div style="font-weight:600; color:var(--slate-800);">{{ $dc->transporter_name }}</div>
              </td>

              <!-- LR Number -->
              <td>
                <span style="font-family:var(--font-mono, monospace); font-weight:700; color:var(--primary-700);">
                  {{ $dc->lr_number ?: '—' }}
                </span>
              </td>

              <!-- Vehicle Number -->
              <td>
                <span style="font-family:var(--font-mono, monospace); font-size:0.75rem; background:#f8fafc; padding:2px 6px; border:1px solid #e2e8f0; border-radius:4px;">
                  {{ $dc->vehicle_number ?: '—' }}
                </span>
              </td>

              <!-- Cartons -->
              <td>
                <span class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-weight:700;">
                  {{ $dc->total_cartons }} CTN
                </span>
              </td>

              <!-- Total Qty -->
              <td>
                <strong style="color:#059669; font-size:0.95rem;">{{ number_format($dc->total_qty) }} pcs</strong>
              </td>

              <!-- Status -->
              <td>
                @if(strtolower($dc->status) === 'in transit')
                  <span class="badge" style="background:#fffbeb; color:#d97706; border:1px solid #fde68a; font-weight:700; padding:4px 10px; border-radius:9999px;">
                    ● In Transit
                  </span>
                @elseif(strtolower($dc->status) === 'delivered')
                  <span class="badge" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; font-weight:700; padding:4px 10px; border-radius:9999px;">
                    ✓ Delivered
                  </span>
                @else
                  <span class="badge" style="background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; font-weight:700; padding:4px 10px; border-radius:9999px;">
                    {{ $dc->status }}
                  </span>
                @endif
              </td>

              <!-- Actions -->
              <td style="text-align:right;">
                <div style="display:inline-flex; gap:6px; align-items:center;">
                  <a href="{{ route('dispatch.challans.edit', $dc->id) }}" class="action-icon-btn" title="Edit Challan">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                  </a>
                  <form action="{{ route('dispatch.challans.destroy', $dc->id) }}" method="POST" style="display:inline;" onsubmit="return handleDeleteDC(this, '{{ $dc->challan_no }}', event)">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-icon-btn danger" title="Delete Challan">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                    </button>
                  </form>
                </div>
              </td>

            </tr>
          @empty
            <tr>
              <td colspan="11" style="text-align:center; padding:48px 20px; color:var(--slate-400);">
                <div style="width:48px; height:48px; border-radius:12px; background:var(--slate-100); color:var(--slate-400); display:inline-flex; align-items:center; justify-content:center; margin-bottom:12px;">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
                </div>
                <div style="font-weight:700; color:var(--slate-700); font-size:1rem; margin-bottom:4px;">No delivery challans issued yet</div>
                <p style="font-size:0.85rem; color:var(--slate-500); margin:0 0 16px;">Generate your first dispatch challan to record outbound finished goods.</p>
                <a href="{{ route('dispatch.challans.create') }}" class="btn btn-primary btn-sm">+ Generate Delivery Challan</a>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>

@push('scripts')
<script>
  let currentStatusFilter = 'all';

  function copyDCCode(code) {
    navigator.clipboard.writeText(code).then(() => {
      if (window.Toast) {
        Toast.fire({
          icon: 'success',
          title: `Challan ${code} copied!`
        });
      }
    });
  }

  function handleDeleteDC(form, challanNo, e) {
    e.preventDefault();
    Swal.fire({
      title: 'Delete Dispatch Challan?',
      html: `Are you sure you want to delete Challan <strong>${challanNo}</strong>?`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#94a3b8',
      confirmButtonText: 'Yes, Delete Challan',
      cancelButtonText: 'Cancel',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        form.submit();
      }
    });
    return false;
  }

  function setStatusFilter(status, btnElem) {
    currentStatusFilter = status;
    document.querySelectorAll('.status-filter-btn').forEach(btn => btn.classList.remove('active'));
    if (btnElem) btnElem.classList.add('active');
    filterDCTable();
  }

  function filterDCTable() {
    const query = (document.getElementById('dc-search-input').value || '').toLowerCase().trim();
    const rows = document.querySelectorAll('#challans-table tbody tr.dc-row');

    rows.forEach(row => {
      const rowStatus = (row.getAttribute('data-status') || '').toLowerCase();
      const text = row.innerText.toLowerCase();

      const matchesStatus = (currentStatusFilter === 'all') || rowStatus.includes(currentStatusFilter);
      const matchesQuery = !query || text.includes(query);

      if (matchesStatus && matchesQuery) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }
</script>
@endpush
@endsection
