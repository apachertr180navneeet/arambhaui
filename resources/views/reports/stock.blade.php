@extends('layouts.app')

@section('title', 'Warehouse Stock Valuation - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Reports Hub</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Stock Report</span></div>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <!-- KPI Metrics Row -->
  <div class="kpi-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:var(--primary-50); color:var(--primary-600); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Warehouse Asset Valuation</div>
        <div style="font-size:1.45rem; font-weight:800; color:#059669; margin-top:2px;">₹{{ number_format($stats['totalValuation'], 2) }}</div>
      </div>
    </div>

    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="18" height="18" x="3" y="3" rx="2"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Total Tracked Items</div>
        <div style="font-size:1.45rem; font-weight:800; color:var(--slate-900); margin-top:2px;">{{ $stats['totalItems'] }} Items</div>
      </div>
    </div>
  </div>

  <!-- Main Card Container -->
  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:20px;">
    
    <!-- Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:20px;">
      <div>
        <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Warehouse Stock Valuation & Balance Summary</h3>
        <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">Real-time on-hand quantities, weighted average unit costs, and inventory asset valuation</p>
      </div>

      <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('stock-val-table', 'Stock_Valuation_Report.csv')">
        Export CSV
      </button>
    </div>

    <!-- Quick Filter Bar -->
    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:16px;">
      <input type="text" class="form-control" style="max-width:280px; font-size:0.85rem;" placeholder="Search item, category, location..." onkeyup="UI.filterGenericTable('stock-val-table', this.value)">
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="data-table" id="stock-val-table" style="width:100%; font-size:0.85rem;">
        <thead>
          <tr>
            <th>Item Code</th>
            <th>Item Name & Description</th>
            <th>Category</th>
            <th>UOM</th>
            <th>Current Stock</th>
            <th>Avg Unit Cost (₹)</th>
            <th>Stock Asset Valuation (₹)</th>
            <th>Reorder Level</th>
            <th>Location</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($items as $itm)
            @php
              $val = $itm->current_stock * $itm->unit_cost;
            @endphp
            <tr>
              <td style="font-family:var(--font-mono); font-weight:700; color:var(--primary-600);">{{ $itm->code }}</td>
              <td style="font-weight:700; color:var(--slate-800);">{{ $itm->name }}</td>
              <td><span class="badge badge-info">{{ $itm->category }}</span></td>
              <td>{{ $itm->unit }}</td>
              <td style="font-weight:800; color:{{ $itm->current_stock <= $itm->min_stock ? 'var(--danger-600)' : 'var(--slate-800)' }};">
                {{ number_format($itm->current_stock, 2) }}
              </td>
              <td>₹{{ number_format($itm->unit_cost, 2) }}</td>
              <td style="font-weight:800; color:#059669;">₹{{ number_format($val, 2) }}</td>
              <td>{{ number_format($itm->min_stock, 2) }}</td>
              <td>{{ $itm->location ?: 'Warehouse A' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="9" style="text-align:center; padding:30px; color:var(--slate-400);">No item inventory balances found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>
@endsection
