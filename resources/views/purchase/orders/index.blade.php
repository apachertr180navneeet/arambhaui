@extends('layouts.app')

@section('title', 'Purchase Entry - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Purchase Management</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Purchase Entry</span></div>
@endsection

@push('styles')
<style>
  /* ==========================================================================
     PURCHASE ENTRY REGISTRY - MODERN ERP STYLING
     ========================================================================== */
  .po-wrapper {
    display: flex;
    flex-direction: column;
    gap: 22px;
  }

  /* Stats Grid */
  .po-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 16px;
  }

  .po-stat-card {
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

  .po-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.08);
  }

  .po-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--accent-color, #2563eb);
  }

  .po-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .po-stat-info {
    flex: 1;
    min-width: 0;
  }

  .po-stat-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--slate-500, #64748b);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 2px;
  }

  .po-stat-value {
    font-size: 1.55rem;
    font-weight: 800;
    color: var(--slate-900, #0f172a);
    line-height: 1.2;
    display: flex;
    align-items: baseline;
    gap: 8px;
  }

  /* Main Card */
  .po-ledger-card {
    background: #ffffff;
    border-radius: 18px;
    border: 1px solid var(--slate-200, #e2e8f0);
    box-shadow: 0 2px 12px -2px rgba(15, 23, 42, 0.06);
    overflow: hidden;
  }

  .po-card-header {
    padding: 22px 24px;
    border-bottom: 1px solid var(--slate-100, #f1f5f9);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
  }

  /* Filter Toolbar */
  .po-toolbar {
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
    max-width: 340px;
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
  .po-code-badge {
    font-family: var(--font-mono, monospace);
    font-weight: 800;
    font-size: 0.85rem;
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    padding: 4px 10px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
  }

  .po-code-badge:hover {
    background: #dbeafe;
    border-color: #93c5fd;
    transform: translateY(-1px);
  }

  /* Table Custom Row styling */
  .po-table th {
    background: #f8fafc;
    color: var(--slate-600, #475569);
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 12px 18px;
    border-bottom: 1px solid var(--slate-200, #e2e8f0);
  }

  .po-table td {
    padding: 14px 18px;
    vertical-align: middle;
    border-bottom: 1px solid var(--slate-100, #f1f5f9);
    font-size: 0.85rem;
  }

  .po-table tr:hover td {
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
    text-decoration: none;
  }

  .action-icon-btn:hover {
    background: var(--slate-100, #f1f5f9);
    color: var(--slate-900, #0f172a);
    border-color: var(--slate-300, #cbd5e1);
  }

  .action-icon-btn.primary:hover {
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
<div class="po-wrapper">

  <!-- KPI Metrics Row -->
  <div class="po-stats-grid">
    
    <!-- Total Entries Recorded -->
    <div class="po-stat-card" style="--accent-color: #2563eb;">
      <div class="po-stat-icon" style="background: #eff6ff; color: #2563eb;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
      </div>
      <div class="po-stat-info">
        <div class="po-stat-label">Total Purchase Entries</div>
        <div class="po-stat-value">
          {{ number_format($stats['totalOrders']) }}
          <span style="font-size:0.75rem; font-weight:600; color:var(--slate-500);">Entries</span>
        </div>
      </div>
    </div>

    <!-- Cumulative Value -->
    <div class="po-stat-card" style="--accent-color: #10b981;">
      <div class="po-stat-icon" style="background: #ecfdf5; color: #059669;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      </div>
      <div class="po-stat-info">
        <div class="po-stat-label">Cumulative Purchase Value</div>
        <div class="po-stat-value" style="color: #059669;">
          ₹{{ number_format($stats['totalAmount'], 2) }}
        </div>
      </div>
    </div>

    <!-- Approved Entries -->
    <div class="po-stat-card" style="--accent-color: #8b5cf6;">
      <div class="po-stat-icon" style="background: #f5f3ff; color: #7c3aed;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      </div>
      <div class="po-stat-info">
        <div class="po-stat-label">Approved & Active</div>
        <div class="po-stat-value" style="color: #7c3aed;">
          {{ number_format($stats['approvedCount']) }}
          <span style="font-size:0.75rem; background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; padding:2px 8px; border-radius:9999px; font-weight:700;">Approved</span>
        </div>
      </div>
    </div>

  </div>

  <!-- Main Purchase Entry Ledger Card Container -->
  <div class="po-ledger-card">
    
    <!-- Action Header -->
    <div class="po-card-header">
      <div>
        <h3 style="margin:0; font-size:1.2rem; font-weight:800; color:var(--slate-900);">Purchase Entry Registry</h3>
        <p style="margin:4px 0 0; font-size:0.825rem; color:var(--slate-500);">
          Track supplier fabric inward challans, Than-wise meters breakdown & procurement bills.
        </p>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('po-table', 'Purchase_Entries.csv')" style="display:inline-flex; align-items:center; gap:6px; font-weight:700;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Export CSV
        </button>

        <a href="{{ route('purchase.orders.create') }}" class="btn btn-primary btn-sm" style="display:inline-flex; align-items:center; gap:6px; font-weight:700; box-shadow:0 2px 8px rgba(37, 99, 235, 0.3);">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
          + New Purchase Entry
        </a>
      </div>
    </div>

    <!-- Quick Filter Toolbar -->
    <div class="po-toolbar">
      <div class="search-input-wrapper">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="po-search-input" placeholder="Search Entry #, Challan #, Vendor..." onkeyup="filterPOTable()">
      </div>

      <div class="status-filter-pills">
        <button type="button" class="status-filter-btn active" onclick="setStatusFilter('all', this)">
          All ({{ $stats['totalOrders'] }})
        </button>
        <button type="button" class="status-filter-btn" onclick="setStatusFilter('approved', this)">
          Approved ({{ $stats['approvedCount'] }})
        </button>
        <button type="button" class="status-filter-btn" onclick="setStatusFilter('pending', this)">
          Pending
        </button>
      </div>
    </div>

    <!-- Purchase Entries Table -->
    <div class="table-responsive">
      <table class="data-table po-table" id="po-table" style="width:100%;">
        <thead>
          <tr>
            <th style="width:145px;">Entry #</th>
            <th style="width:125px;">Challan #</th>
            <th>Vendor / Supplier</th>
            <th>Entry Date</th>
            <th>Product / Fabric</th>
            <th>Thans & Meters</th>
            <th>Grand Total</th>
            <th>Status</th>
            <th>Payment</th>
            <th style="text-align:right; width:120px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($orders as $po)
            @php
              $statusKey = strtolower($po->status);
              $firstItem = $po->items->first();
              $totalMeters = $firstItem ? $firstItem->ordered_qty : 0;
              $thansCount = $po->total_thans_count ?: ($po->than_list ? count($po->than_list) : 0);
              $challanNo = $po->challan_number ?: '—';
            @endphp
            <tr class="po-row" data-status="{{ $statusKey }}">
              
              <!-- Entry Number -->
              <td>
                <span class="po-code-badge" onclick="copyCode('{{ $po->po_number }}')" title="Click to copy Entry Number">
                  <span>{{ $po->po_number }}</span>
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                </span>
              </td>

              <!-- Challan # -->
              <td>
                <span class="badge" style="background:#fef3c7; color:#92400e; border:1px solid #fde68a; font-weight:800; font-family:var(--font-mono, monospace);">
                  {{ $challanNo }}
                </span>
              </td>

              <!-- Vendor Name -->
              <td>
                <div style="font-weight:700; color:var(--slate-900);">{{ $po->vendor_name }}</div>
                <div style="font-size:0.725rem; color:var(--slate-500); margin-top:2px;">Supplier / Mill</div>
              </td>

              <!-- PO Date -->
              <td>
                <div style="font-weight:600; color:var(--slate-800);">{{ date('d M Y', strtotime($po->po_date)) }}</div>
              </td>

              <!-- Product / Fabric -->
              <td>
                <div style="font-weight:700; color:var(--slate-800);">{{ $po->items_summary }}</div>
                @if($po->items->count() > 1)
                  <div style="font-size:0.725rem; color:#2563eb; font-weight:600; margin-top:2px;">
                    {{ $po->items->count() }} Fabric Items
                  </div>
                @elseif($firstItem && $firstItem->item_code)
                  <div style="font-size:0.725rem; color:var(--slate-500); font-family:monospace;">{{ $firstItem->item_code }}</div>
                @endif
              </td>

              <!-- Thans & Total Meters -->
              <td>
                <div>
                  <span class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-weight:700;">
                    {{ $thansCount }} {{ Str::plural('Than', $thansCount) }}
                  </span>
                  <div style="font-weight:800; color:var(--slate-900); margin-top:4px; font-size:0.85rem;">
                    {{ number_format($po->total_meters_sum ?: $totalMeters, 2) }} Mtr
                  </div>
                </div>
              </td>

              <!-- Grand Total -->
              <td>
                <div style="font-weight:800; color:#059669; font-size:0.95rem;">
                  ₹{{ number_format($po->grand_total, 2) }}
                </div>
              </td>

              <!-- Status -->
              <td>
                @if(strtolower($po->status) === 'approved')
                  <span class="badge" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; font-weight:700; padding:4px 10px; border-radius:9999px;">
                    ● Approved
                  </span>
                @elseif(strtolower($po->status) === 'pending')
                  <span class="badge" style="background:#fffbeb; color:#d97706; border:1px solid #fde68a; font-weight:700; padding:4px 10px; border-radius:9999px;">
                    ● Pending
                  </span>
                @else
                  <span class="badge" style="background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; font-weight:700; padding:4px 10px; border-radius:9999px;">
                    {{ $po->status }}
                  </span>
                @endif
              </td>

              <!-- Payment Status -->
              <td>
                @if(strtolower($po->payment_status) === 'paid')
                  <span class="badge" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; font-weight:700;">Paid</span>
                @else
                  <span class="badge" style="background:#fffbeb; color:#d97706; border:1px solid #fde68a; font-weight:700;">{{ $po->payment_status ?: 'Unpaid' }}</span>
                @endif
              </td>

              <!-- Actions -->
              <td style="text-align:right;">
                <div style="display:inline-flex; gap:6px; align-items:center;">
                  
                  <a href="{{ route('purchase.orders.edit', $po->id) }}" class="action-icon-btn primary" title="Edit Purchase Entry">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                  </a>

                  <button type="button" class="action-icon-btn" onclick='printPurchaseOrder(@json($po))' title="Print Challan Slip">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                  </button>

                  <form action="{{ route('purchase.orders.destroy', $po->id) }}" method="POST" style="display:inline;" onsubmit="return handleDeletePO(this, '{{ $po->po_number }}', event)">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-icon-btn danger" title="Delete Purchase Entry">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                    </button>
                  </form>

                </div>
              </td>

            </tr>
          @empty
            <tr>
              <td colspan="10" style="text-align:center; padding:48px 20px; color:var(--slate-400);">
                <div style="width:48px; height:48px; border-radius:12px; background:var(--slate-100); color:var(--slate-400); display:inline-flex; align-items:center; justify-content:center; margin-bottom:12px;">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                </div>
                <div style="font-weight:700; color:var(--slate-700); font-size:1rem; margin-bottom:4px;">No purchase entries found</div>
                <p style="font-size:0.85rem; color:var(--slate-500); margin:0 0 16px;">Record fabric than-wise challan details and inward bills.</p>
                <a href="{{ route('purchase.orders.create') }}" class="btn btn-primary btn-sm">+ New Purchase Entry</a>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>

<!-- Printable Section Hidden Container -->
<div id="print-po-area" style="display:none;"></div>

@push('scripts')
<script>
  let currentStatusFilter = 'all';

  function copyCode(code) {
    navigator.clipboard.writeText(code).then(() => {
      if (window.Toast) {
        Toast.fire({
          icon: 'success',
          title: `Entry Number ${code} copied!`
        });
      } else if (window.Swal) {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: `Entry Number ${code} copied!`,
          showConfirmButton: false,
          timer: 2000
        });
      }
    });
  }

  function handleDeletePO(form, poNumber, e) {
    e.preventDefault();
    Swal.fire({
      title: 'Delete Purchase Entry?',
      html: `Are you sure you want to delete entry <strong>${poNumber}</strong>?<br><span style="font-size:0.85rem; color:#ef4444;">This record and all Than meter entries will be removed.</span>`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#94a3b8',
      confirmButtonText: 'Yes, Delete Entry',
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
    filterPOTable();
  }

  function filterPOTable() {
    const query = (document.getElementById('po-search-input').value || '').toLowerCase().trim();
    const rows = document.querySelectorAll('#po-table tbody tr.po-row');

    rows.forEach(row => {
      const rowStatus = (row.getAttribute('data-status') || '').toLowerCase();
      const text = row.innerText.toLowerCase();

      const matchesStatus = (currentStatusFilter === 'all') || (rowStatus === currentStatusFilter);
      const matchesQuery = !query || text.includes(query);

      if (matchesStatus && matchesQuery) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }

  function printPurchaseOrder(po) {
    const challanNo = po.challan_number || po.challan_no || '—';
    const items = (po.items && po.items.length) ? po.items : [];

    let overallThansCount = 0;
    let overallMetersTotal = 0;
    let itemsHtml = '';

    if (items.length > 0) {
      items.forEach((item, itemIdx) => {
        let thans = [];
        if (item.than_details) {
          try {
            thans = typeof item.than_details === 'string' ? JSON.parse(item.than_details) : item.than_details;
          } catch(e) {}
        }
        if (!thans.length && item.than_list) {
          thans = item.than_list;
        }
        if (!thans.length && item.ordered_qty > 0) {
          thans = [parseFloat(item.ordered_qty)];
        }

        const half = Math.ceil(thans.length / 2);
        const col1 = thans.slice(0, half);
        const col2 = thans.slice(half);

        const sumCol1 = col1.reduce((acc, val) => acc + (parseFloat(val) || 0), 0);
        const sumCol2 = col2.reduce((acc, val) => acc + (parseFloat(val) || 0), 0);
        const itemMeters = sumCol1 + sumCol2;
        const itemRate = Number(item.rate || 0);
        const itemSubtotal = itemMeters * itemRate;
        const itemTaxPct = Number(item.tax_percent || 5);
        const itemTax = (itemSubtotal * itemTaxPct) / 100;
        const itemTotal = itemSubtotal + itemTax;

        overallThansCount += thans.length;
        overallMetersTotal += itemMeters;

        const maxRows = Math.max(col1.length, col2.length, 1);
        let rowsHtml = '';
        for (let i = 0; i < maxRows; i++) {
          const t1 = col1[i] !== undefined ? parseFloat(col1[i]).toFixed(2) : '';
          const t2 = col2[i] !== undefined ? parseFloat(col2[i]).toFixed(2) : '';
          rowsHtml += `
            <tr>
              <td style="padding:5px 8px; border:1px solid #cbd5e1; text-align:center; font-weight:600; color:#64748b; font-size:12px;">${col1[i] !== undefined ? (i + 1) : ''}</td>
              <td style="padding:5px 8px; border:1px solid #cbd5e1; text-align:right; font-weight:700; font-size:13px; font-family:monospace;">${t1}</td>
              <td style="padding:5px 8px; border:1px solid #cbd5e1; text-align:center; font-weight:600; color:#64748b; font-size:12px;">${col2[i] !== undefined ? (half + i + 1) : ''}</td>
              <td style="padding:5px 8px; border:1px solid #cbd5e1; text-align:right; font-weight:700; font-size:13px; font-family:monospace;">${t2}</td>
            </tr>
          `;
        }

        itemsHtml += `
          <div style="margin-bottom:18px; border:1px solid #cbd5e1; border-radius:8px; overflow:hidden;">
            <!-- Product Banner -->
            <div style="background:#eff6ff; border-bottom:1px solid #bfdbfe; padding:8px 12px; display:flex; justify-content:space-between; align-items:center;">
              <div>
                <span style="font-size:11px; font-weight:700; color:#1d4ed8; text-transform:uppercase;">Item #${itemIdx + 1}: ${item.item_name} ${item.item_code ? `[${item.item_code}]` : ''}</span>
              </div>
              <div style="text-align:right; font-size:12px; font-weight:700; color:#1e3a8a;">
                Rate: ₹${itemRate.toFixed(2)}/Mtr | GST: ${itemTaxPct}% | Total: ₹${itemTotal.toFixed(2)}
              </div>
            </div>

            <!-- Than List in 2 Columns -->
            <table style="width:100%; border-collapse:collapse; font-size:12px;">
              <thead>
                <tr style="background:#f1f5f9;">
                  <th style="padding:5px; border:1px solid #cbd5e1; width:12%;">Than #</th>
                  <th style="padding:5px; border:1px solid #cbd5e1; width:38%; text-align:right;">Meters</th>
                  <th style="padding:5px; border:1px solid #cbd5e1; width:12%;">Than #</th>
                  <th style="padding:5px; border:1px solid #cbd5e1; width:38%; text-align:right;">Meters</th>
                </tr>
              </thead>
              <tbody>
                ${rowsHtml}
              </tbody>
              <tfoot>
                <tr style="background:#f8fafc; font-weight:700; font-size:12px;">
                  <td style="padding:5px; border:1px solid #cbd5e1; text-align:center;">Sub 1:</td>
                  <td style="padding:5px; border:1px solid #cbd5e1; text-align:right; font-family:monospace; color:#2563eb;">${sumCol1.toFixed(2)} m</td>
                  <td style="padding:5px; border:1px solid #cbd5e1; text-align:center;">Sub 2:</td>
                  <td style="padding:5px; border:1px solid #cbd5e1; text-align:right; font-family:monospace; color:#2563eb;">${sumCol2.toFixed(2)} m</td>
                </tr>
                <tr style="background:#f1f5f9; font-weight:800; font-size:12px;">
                  <td colspan="2" style="padding:6px 10px; border:1px solid #cbd5e1;">Item Total Thans: ${thans.length} Thans</td>
                  <td colspan="2" style="padding:6px 10px; border:1px solid #cbd5e1; text-align:right;">Total Item Meters: ${itemMeters.toFixed(2)} Mtr</td>
                </tr>
              </tfoot>
            </table>
          </div>
        `;
      });
    } else {
      let thans = po.than_list || [];
      const half = Math.ceil(thans.length / 2);
      const col1 = thans.slice(0, half);
      const col2 = thans.slice(half);
      const sumCol1 = col1.reduce((a, b) => a + (parseFloat(b) || 0), 0);
      const sumCol2 = col2.reduce((a, b) => a + (parseFloat(b) || 0), 0);
      overallThansCount = thans.length;
      overallMetersTotal = sumCol1 + sumCol2;
    }

    const html = `
      <div style="font-family:'Plus Jakarta Sans', Arial, sans-serif; padding:24px; color:#1e293b; max-width:760px; margin:auto; border:2px solid #334155; border-radius:12px; background:#fff;">
        
        <!-- Challan Header (Textile Format) -->
        <div style="text-align:center; border-bottom:2px solid #0f172a; padding-bottom:12px; margin-bottom:16px;">
          <div style="font-size:11px; font-weight:700; letter-spacing:2px; color:#64748b; text-transform:uppercase;">॥ Shree Ganeshay Namah ॥</div>
          <h1 style="margin:4px 0 0; font-size:24px; font-weight:900; letter-spacing:1px; color:#0f172a; text-transform:uppercase;">PURCHASE CHALLAN ENTRY</h1>
          <div style="font-size:12px; color:#475569; margin-top:2px;">Textile Weaving, Dyeing & Processing House Inward Slip</div>
        </div>

        <!-- Meta Details Grid -->
        <table style="width:100%; border-collapse:collapse; margin-bottom:16px; font-size:13px;">
          <tr>
            <td style="width:60%; vertical-align:top; padding:8px 12px; background:#f8fafc; border:1px solid #cbd5e1; border-radius:6px;">
              <span style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase;">M/s (Vendor / Supplier):</span>
              <div style="font-size:16px; font-weight:800; color:#0f172a; margin-top:4px;">${po.vendor_name || 'Direct Supplier'}</div>
              <div style="font-size:12px; color:#64748b; margin-top:2px;">Entry Reference: <strong>${po.po_number}</strong></div>
            </td>
            <td style="width:40%; vertical-align:top; padding:8px 12px; background:#f8fafc; border:1px solid #cbd5e1; border-radius:6px;">
              <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                <span style="font-weight:700; color:#475569;">Challan No.:</span>
                <strong style="font-size:15px; color:#b45309; font-family:monospace;">${challanNo}</strong>
              </div>
              <div style="display:flex; justify-content:space-between;">
                <span style="font-weight:700; color:#475569;">Date:</span>
                <strong>${po.po_date}</strong>
              </div>
            </td>
          </tr>
        </table>

        <!-- Items & Than Breakdown -->
        ${itemsHtml}

        <!-- Summary & Financials Calculation -->
        <table style="width:100%; border-collapse:collapse; margin-bottom:20px; font-size:13px;">
          <tr>
            <td style="width:55%; vertical-align:top; padding:10px 14px; border:1px solid #cbd5e1; background:#f8fafc; border-radius:6px;">
              <div style="font-weight:700; color:#334155; margin-bottom:6px;">Than Summary (Overall):</div>
              <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                <span>Total Items:</span>
                <strong style="font-size:14px;">${items.length || 1} Items</strong>
              </div>
              <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                <span>Total Thans / Rolls:</span>
                <strong style="font-size:14px;">${overallThansCount} Thans</strong>
              </div>
              <div style="display:flex; justify-content:space-between;">
                <span>Total Meters Aggregated:</span>
                <strong style="font-size:15px; color:#0f172a;">${overallMetersTotal.toFixed(2)} Mtr</strong>
              </div>
            </td>
            <td style="width:45%; vertical-align:top; padding:10px 14px; border:1px solid #cbd5e1; background:#ffffff; border-radius:6px;">
              <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                <span style="color:#64748b;">Subtotal:</span>
                <strong style="font-family:monospace;">₹${Number(po.subtotal).toFixed(2)}</strong>
              </div>
              <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                <span style="color:#64748b;">GST / Taxes:</span>
                <strong style="font-family:monospace;">₹${Number(po.tax_total).toFixed(2)}</strong>
              </div>
              <div style="border-top:2px solid #0f172a; padding-top:6px; display:flex; justify-content:space-between; font-size:16px;">
                <strong style="color:#0f172a;">Grand Total:</strong>
                <strong style="color:#059669; font-size:17px; font-family:monospace;">₹${Number(po.grand_total).toFixed(2)}</strong>
              </div>
            </td>
          </tr>
        </table>

        <!-- Signatures and Stamp Section -->
        <div style="display:flex; justify-content:space-between; padding-top:28px; border-top:1px dashed #94a3b8; font-size:12px; color:#64748b;">
          <div>
            <div>Prepared By: _________________</div>
          </div>
          <div>
            <div>Checked By: _________________</div>
          </div>
          <div style="text-align:right;">
            <div>Authorised Signatory</div>
          </div>
        </div>

      </div>
    `;

    const printArea = document.getElementById('print-po-area');
    printArea.innerHTML = html;
    UI.printSection('print-po-area');
  }
</script>
@endpush
@endsection

