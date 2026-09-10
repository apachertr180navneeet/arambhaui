@extends('layouts.app')

@section('title', 'Lot-Wise Sales & Dispatch Analysis - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Reports Hub</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Lot-Wise Sales</span></div>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:20px;">
    
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:20px;">
      <div>
        <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Lot-Wise Finished Goods Sales & Dispatch Report</h3>
        <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">Outward shipping records, consignee customers, cartons, and delivered quantities</p>
      </div>

      <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('lot-sales-table', 'Lot_Sales_Report.csv')">
        Export CSV
      </button>
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="data-table" id="lot-sales-table" style="width:100%; font-size:0.85rem;">
        <thead>
          <tr>
            <th>Challan #</th>
            <th>Customer Consignee</th>
            <th>Dispatch Date</th>
            <th>Transporter</th>
            <th>LR Number</th>
            <th>Cartons</th>
            <th>Dispatched Quantity</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($challans as $dc)
            <tr>
              <td style="font-family:var(--font-mono); font-weight:700; color:var(--primary-600);">{{ $dc->challan_no }}</td>
              <td style="font-weight:700; color:var(--slate-800);">{{ $dc->customer_name }}</td>
              <td>{{ date('d M Y', strtotime($dc->dispatch_date)) }}</td>
              <td>{{ $dc->transporter_name }}</td>
              <td style="font-family:var(--font-mono); font-size:0.8rem;">{{ $dc->lr_number ?: '—' }}</td>
              <td>{{ $dc->total_cartons }} CTN</td>
              <td style="font-weight:800; color:#059669;">{{ number_format($dc->total_qty) }} pcs</td>
              <td><span class="badge badge-info">{{ $dc->status }}</span></td>
            </tr>
          @empty
            <tr>
              <td colspan="8" style="text-align:center; padding:30px; color:var(--slate-400);">No dispatched sales lots found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>
@endsection
