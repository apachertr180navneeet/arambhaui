@extends('layouts.app')

@section('title', 'Lot-Wise Sales & Dispatch Analysis - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Reports Hub</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Lot-Wise Sales</span></div>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <!-- Company Report Header Banner -->
  <div style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:16px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; box-shadow:var(--shadow-sm);">
    <div style="display:flex; align-items:center; gap:12px;">
      <div style="width:42px; height:42px; border-radius:10px; background:var(--primary-50); color:var(--primary-700); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.2rem;">
        🏢
      </div>
      <div>
        <div style="font-weight:800; font-size:1.15rem; color:var(--slate-900);">{{ $companyName ?? 'GarmentERP' }}</div>
        <div style="font-size:0.775rem; color:var(--slate-500);">
          @if(!empty($companySettings['gstin'])) GSTIN: <strong style="color:var(--slate-700);">{{ $companySettings['gstin'] }}</strong> &bull; @endif
          Financial Year: <strong style="color:var(--slate-700);">{{ $companySettings['financial_year'] ?? date('Y') . '-' . (date('Y')+1) }}</strong>
          @if(!empty($companySettings['company_address'])) &bull; {{ Str::limit($companySettings['company_address'], 60) }} @endif
        </div>
      </div>
    </div>
    <div style="display:flex; gap:8px;">
      <button class="btn btn-secondary btn-sm" onclick="UI.printElement('lot-sales-table', 'Lot-Wise Finished Goods Sales & Dispatch Report - ' + {!! json_encode($companyName ?? 'GarmentERP') !!})" style="display:inline-flex; align-items:center; gap:6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
        Print Report
      </button>
      <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('lot-sales-table', '{{ Str::slug($companyName ?? 'company') }}_Lot_Sales_Report.csv')">
        Export CSV
      </button>
    </div>
  </div>

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
