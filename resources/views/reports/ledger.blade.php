@extends('layouts.app')

@section('title', 'Item Stock Movement Ledger - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Reports Hub</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Item Stock Ledger</span></div>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:20px;">
    
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:20px;">
      <div>
        <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Item Stock Movement & Transaction Ledger</h3>
        <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">Material IN (PO Inward), Material OUT (Job Work Issue), QC Return & Finished Goods Dispatch</p>
      </div>

      <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('ledger-table', 'Stock_Ledger.csv')">
        Export CSV
      </button>
    </div>

    <!-- Filter -->
    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:16px;">
      <input type="text" class="form-control" style="max-width:280px; font-size:0.85rem;" placeholder="Search item, transaction type, reference..." onkeyup="UI.filterGenericTable('ledger-table', this.value)">
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="data-table" id="ledger-table" style="width:100%; font-size:0.85rem;">
        <thead>
          <tr>
            <th>Date & Time</th>
            <th>Item / Material</th>
            <th>Transaction Type</th>
            <th>Reference Doc #</th>
            <th>Quantity In (+)</th>
            <th>Quantity Out (-)</th>
            <th>Running Stock</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($transactions as $t)
            <tr>
              <td>{{ date('d M Y, h:i A', strtotime($t->created_at)) }}</td>
              <td style="font-weight:700;">{{ $t->item_name }}</td>
              <td><span class="badge badge-info">{{ $t->type }}</span></td>
              <td style="font-family:var(--font-mono); font-weight:600;">{{ $t->reference_no }}</td>
              <td style="font-weight:700; color:#059669;">{{ $t->qty_in ? '+' . number_format($t->qty_in, 2) : '—' }}</td>
              <td style="font-weight:700; color:var(--danger-600);">{{ $t->qty_out ? '-' . number_format($t->qty_out, 2) : '—' }}</td>
              <td style="font-weight:800; color:var(--slate-800);">{{ number_format($t->running_balance, 2) }}</td>
            </tr>
          @empty
            @forelse ($items as $itm)
              <tr>
                <td>{{ date('d M Y') }}</td>
                <td style="font-weight:700;">{{ $itm->name }} ({{ $itm->code }})</td>
                <td><span class="badge badge-primary">Opening / Stock</span></td>
                <td style="font-family:var(--font-mono); font-weight:600;">SYS-OP-{{ $itm->code }}</td>
                <td style="font-weight:700; color:#059669;">+{{ number_format($itm->current_stock, 2) }}</td>
                <td>—</td>
                <td style="font-weight:800; color:var(--slate-800);">{{ number_format($itm->current_stock, 2) }} {{ $itm->unit }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="7" style="text-align:center; padding:30px; color:var(--slate-400);">No stock movements recorded yet.</td>
              </tr>
            @endforelse
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>
@endsection
