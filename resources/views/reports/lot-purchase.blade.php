@extends('layouts.app')

@section('title', 'Lot-Wise Purchase Analysis - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Reports Hub</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Lot-Wise Purchase</span></div>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:20px;">
    
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:20px;">
      <div>
        <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Lot-Wise Raw Material Purchase Report</h3>
        <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">Procurement breakdown by purchase orders, supplier bills, taxes, and warehouse lots</p>
      </div>

      <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('lot-pur-table', 'Lot_Purchase_Report.csv')">
        Export CSV
      </button>
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="data-table" id="lot-pur-table" style="width:100%; font-size:0.85rem;">
        <thead>
          <tr>
            <th>PO Number</th>
            <th>Vendor / Mill</th>
            <th>Order Date</th>
            <th>Material Line Items</th>
            <th>Subtotal (₹)</th>
            <th>GST Tax (₹)</th>
            <th>Grand Total (₹)</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($orders as $po)
            <tr>
              <td style="font-family:var(--font-mono); font-weight:700; color:var(--primary-600);">{{ $po->po_number }}</td>
              <td style="font-weight:700; color:var(--slate-800);">{{ $po->vendor_name }}</td>
              <td>{{ date('d M Y', strtotime($po->po_date)) }}</td>
              <td>{{ $po->items->count() }} Line Items</td>
              <td>₹{{ number_format($po->subtotal, 2) }}</td>
              <td>₹{{ number_format($po->tax_total, 2) }}</td>
              <td style="font-weight:800; color:#059669;">₹{{ number_format($po->grand_total, 2) }}</td>
              <td><span class="badge badge-success">{{ $po->status }}</span></td>
            </tr>
          @empty
            <tr>
              <td colspan="8" style="text-align:center; padding:30px; color:var(--slate-400);">No purchase order lot records found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>
@endsection
