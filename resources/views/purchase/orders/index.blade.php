@extends('layouts.app')

@section('title', 'Purchase Orders - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Purchase Management</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Purchase Orders</span></div>
@endsection

@push('styles')
<style>
  /* ==========================================================================
     PURCHASE ORDERS REGISTRY - MODERN ERP STYLING
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
    
    <!-- Total Orders Issued -->
    <div class="po-stat-card" style="--accent-color: #2563eb;">
      <div class="po-stat-icon" style="background: #eff6ff; color: #2563eb;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
      </div>
      <div class="po-stat-info">
        <div class="po-stat-label">Total Orders Issued</div>
        <div class="po-stat-value">
          {{ number_format($stats['totalOrders']) }}
          <span style="font-size:0.75rem; font-weight:600; color:var(--slate-500);">Orders</span>
        </div>
      </div>
    </div>

    <!-- Cumulative Value -->
    <div class="po-stat-card" style="--accent-color: #10b981;">
      <div class="po-stat-icon" style="background: #ecfdf5; color: #059669;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      </div>
      <div class="po-stat-info">
        <div class="po-stat-label">Cumulative PO Value</div>
        <div class="po-stat-value" style="color: #059669;">
          ₹{{ number_format($stats['totalAmount'], 2) }}
        </div>
      </div>
    </div>

    <!-- Approved Orders -->
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

  <!-- Main PO Ledger Card Container -->
  <div class="po-ledger-card">
    
    <!-- Action Header -->
    <div class="po-card-header">
      <div>
        <h3 style="margin:0; font-size:1.2rem; font-weight:800; color:var(--slate-900);">Purchase Orders Registry</h3>
        <p style="margin:4px 0 0; font-size:0.825rem; color:var(--slate-500);">
          Procurement orders for greige fabric, finished rolls, trims & manufacturing accessories.
        </p>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('po-table', 'Purchase_Orders.csv')" style="display:inline-flex; align-items:center; gap:6px; font-weight:700;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Export CSV
        </button>

        <a href="{{ route('purchase.orders.create') }}" class="btn btn-primary btn-sm" style="display:inline-flex; align-items:center; gap:6px; font-weight:700; box-shadow:0 2px 8px rgba(37, 99, 235, 0.3);">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
          Create Purchase Order
        </a>
      </div>
    </div>

    <!-- Quick Filter Toolbar -->
    <div class="po-toolbar">
      <div class="search-input-wrapper">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="po-search-input" placeholder="Search PO#, vendor name, warehouse..." onkeyup="filterPOTable()">
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

    <!-- Purchase Orders Table -->
    <div class="table-responsive">
      <table class="data-table po-table" id="po-table" style="width:100%;">
        <thead>
          <tr>
            <th style="width:170px;">PO Number</th>
            <th>Vendor / Supplier</th>
            <th>PO Date</th>
            <th>Expected Date</th>
            <th>Warehouse</th>
            <th>Line Items</th>
            <th>Grand Total</th>
            <th>Status</th>
            <th>Payment</th>
            <th style="text-align:right; width:130px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($orders as $po)
            @php
              $statusKey = strtolower($po->status);
            @endphp
            <tr class="po-row" data-status="{{ $statusKey }}">
              
              <!-- PO Number -->
              <td>
                <span class="po-code-badge" onclick="copyCode('{{ $po->po_number }}')" title="Click to copy PO Number">
                  <span>{{ $po->po_number }}</span>
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
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

              <!-- Expected Date -->
              <td>
                <div style="color:var(--slate-700);">
                  {{ $po->expected_delivery_date ? date('d M Y', strtotime($po->expected_delivery_date)) : '—' }}
                </div>
              </td>

              <!-- Warehouse -->
              <td>
                <span class="badge" style="background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; font-weight:600; font-size:0.75rem;">
                  {{ $po->warehouse_location ?: 'Main Store' }}
                </span>
              </td>

              <!-- Line Items Count -->
              <td>
                <span class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-weight:700;">
                  {{ $po->items ? $po->items->count() : 0 }} Items
                </span>
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
                  
                  <a href="{{ route('purchase.orders.edit', $po->id) }}" class="action-icon-btn primary" title="Edit Purchase Order">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                  </a>

                  <button type="button" class="action-icon-btn" onclick='printPurchaseOrder(@json($po))' title="Print Purchase Order">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                  </button>

                  <form action="{{ route('purchase.orders.destroy', $po->id) }}" method="POST" style="display:inline;" onsubmit="return handleDeletePO(this, '{{ $po->po_number }}', event)">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-icon-btn danger" title="Delete Purchase Order">
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
                <div style="font-weight:700; color:var(--slate-700); font-size:1rem; margin-bottom:4px;">No purchase orders found</div>
                <p style="font-size:0.85rem; color:var(--slate-500); margin:0 0 16px;">Create your first procurement order to start tracking material supply.</p>
                <a href="{{ route('purchase.orders.create') }}" class="btn btn-primary btn-sm">+ Create Purchase Order</a>
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
          title: `PO Number ${code} copied!`
        });
      } else if (window.Swal) {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: `PO Number ${code} copied!`,
          showConfirmButton: false,
          timer: 2000
        });
      }
    });
  }

  function handleDeletePO(form, poNumber, e) {
    e.preventDefault();
    Swal.fire({
      title: 'Delete Purchase Order?',
      html: `Are you sure you want to delete PO <strong>${poNumber}</strong>?<br><span style="font-size:0.85rem; color:#ef4444;">This order and its line items will be removed.</span>`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#94a3b8',
      confirmButtonText: 'Yes, Delete PO',
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
    let linesHtml = (po.items || []).map((itm, idx) => `
      <tr>
        <td style="padding:8px; border:1px solid #e2e8f0; text-align:center;">${idx + 1}</td>
        <td style="padding:8px; border:1px solid #e2e8f0; font-weight:600;">${itm.item_name}</td>
        <td style="padding:8px; border:1px solid #e2e8f0; text-align:center;">${itm.ordered_qty} ${itm.unit || 'm'}</td>
        <td style="padding:8px; border:1px solid #e2e8f0; text-align:right;">₹${Number(itm.rate).toFixed(2)}</td>
        <td style="padding:8px; border:1px solid #e2e8f0; text-align:right;">${itm.tax_percent}%</td>
        <td style="padding:8px; border:1px solid #e2e8f0; text-align:right; font-weight:700;">₹${Number(itm.total_amount).toFixed(2)}</td>
      </tr>
    `).join('');

    const html = `
      <div style="font-family:'Plus Jakarta Sans', sans-serif; padding:30px; color:#1e293b; max-width:800px; margin:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:2px solid #0f172a; padding-bottom:16px; margin-bottom:20px;">
          <div>
            <h2 style="margin:0; font-size:1.5rem; color:#0f172a; font-weight:800;">FashionWorks Pvt. Ltd.</h2>
            <p style="margin:2px 0 0; font-size:0.85rem; color:#64748b;">Apparel Park MIDC, Tiruppur / Mumbai | GSTIN: 27AABCF1234F1Z1</p>
          </div>
          <div style="text-align:right;">
            <h3 style="margin:0; font-size:1.25rem; color:#4f46e5; font-weight:800;">PURCHASE ORDER</h3>
            <p style="margin:2px 0 0; font-size:0.85rem; font-family:monospace; font-weight:700;">${po.po_number}</p>
          </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px; font-size:0.85rem;">
          <div style="padding:12px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px;">
            <div style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">Vendor / Supplier:</div>
            <div style="font-weight:700; font-size:1rem; margin-top:4px;">${po.vendor_name}</div>
          </div>
          <div style="padding:12px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px;">
            <div><strong>PO Date:</strong> ${po.po_date}</div>
            <div><strong>Delivery Location:</strong> ${po.warehouse_location || 'Main Store'}</div>
          </div>
        </div>

        <table style="width:100%; border-collapse:collapse; font-size:0.85rem; margin-bottom:20px;">
          <thead>
            <tr style="background:#f1f5f9;">
              <th style="padding:8px; border:1px solid #e2e8f0;">#</th>
              <th style="padding:8px; border:1px solid #e2e8f0; text-align:left;">Item Description</th>
              <th style="padding:8px; border:1px solid #e2e8f0;">Qty</th>
              <th style="padding:8px; border:1px solid #e2e8f0; text-align:right;">Rate</th>
              <th style="padding:8px; border:1px solid #e2e8f0; text-align:right;">Tax %</th>
              <th style="padding:8px; border:1px solid #e2e8f0; text-align:right;">Total Amount</th>
            </tr>
          </thead>
          <tbody>${linesHtml}</tbody>
          <tfoot>
            <tr>
              <td colspan="5" style="padding:8px; text-align:right; font-weight:700; border:1px solid #e2e8f0;">Subtotal:</td>
              <td style="padding:8px; text-align:right; font-weight:700; border:1px solid #e2e8f0;">₹${Number(po.subtotal).toFixed(2)}</td>
            </tr>
            <tr>
              <td colspan="5" style="padding:8px; text-align:right; font-weight:700; border:1px solid #e2e8f0;">GST / Tax:</td>
              <td style="padding:8px; text-align:right; font-weight:700; border:1px solid #e2e8f0;">₹${Number(po.tax_total).toFixed(2)}</td>
            </tr>
            <tr style="background:#f8fafc;">
              <td colspan="5" style="padding:10px; text-align:right; font-weight:800; font-size:1rem; border:1px solid #e2e8f0;">Grand Total:</td>
              <td style="padding:10px; text-align:right; font-weight:800; font-size:1rem; color:#059669; border:1px solid #e2e8f0;">₹${Number(po.grand_total).toFixed(2)}</td>
            </tr>
          </tfoot>
        </table>
      </div>
    `;

    const printArea = document.getElementById('print-po-area');
    printArea.innerHTML = html;
    UI.printSection('print-po-area');
  }
</script>
@endpush
@endsection
