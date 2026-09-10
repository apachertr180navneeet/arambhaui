@extends('layouts.app')

@section('title', 'Dispatch Challans - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Shipping</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Dispatch Challans</span></div>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <!-- KPI Metrics Row -->
  <div class="kpi-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:var(--primary-50); color:var(--primary-600); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Delivery Challans Issued</div>
        <div style="font-size:1.45rem; font-weight:800; color:var(--slate-900); margin-top:2px;">{{ $stats['totalChallans'] }}</div>
      </div>
    </div>

    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Total Dispatched Units</div>
        <div style="font-size:1.45rem; font-weight:800; color:var(--slate-900); margin-top:2px;">{{ number_format($stats['totalDispatchedQty']) }} pcs</div>
      </div>
    </div>

    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:#fffbeb; color:#d97706; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">In-Transit Shipments</div>
        <div style="font-size:1.45rem; font-weight:800; color:#d97706; margin-top:2px;">{{ $stats['inTransit'] }} On Road</div>
      </div>
    </div>
  </div>

  <!-- Main Card Container -->
  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:20px;">
    
    <!-- Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:20px;">
      <div>
        <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Dispatch Challans Registry</h3>
        <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">Lorry receipt numbers (LR), transporter names, vehicle records, and tracking</p>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('challans-table', 'Dispatch_Challans.csv')" style="display:inline-flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Export CSV
        </button>
        <button class="btn btn-primary btn-sm" onclick="openChallanModal()" style="display:inline-flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
          + Generate Delivery Challan
        </button>
      </div>
    </div>

    <!-- Quick Filter Bar -->
    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:16px;">
      <input type="text" class="form-control" style="max-width:280px; font-size:0.85rem;" placeholder="Search DC#, customer, LR#, transporter..." onkeyup="UI.filterGenericTable('challans-table', this.value)">
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="data-table" id="challans-table" style="width:100%; font-size:0.85rem;">
        <thead>
          <tr>
            <th>Challan #</th>
            <th>Customer Name</th>
            <th>Order Ref</th>
            <th>Dispatch Date</th>
            <th>Transporter</th>
            <th>LR Number</th>
            <th>Vehicle #</th>
            <th>Cartons</th>
            <th>Total Qty</th>
            <th>Status</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($challans as $dc)
            <tr>
              <td style="font-family:var(--font-mono); font-weight:700; color:var(--primary-600);">{{ $dc->challan_no }}</td>
              <td style="font-weight:700; color:var(--slate-800);">{{ $dc->customer_name }}</td>
              <td style="font-family:var(--font-mono); font-size:0.8rem;">{{ $dc->order_no }}</td>
              <td>{{ date('d M Y', strtotime($dc->dispatch_date)) }}</td>
              <td style="font-weight:600;">{{ $dc->transporter_name }}</td>
              <td style="font-family:var(--font-mono); font-size:0.8rem;">{{ $dc->lr_number ?: '—' }}</td>
              <td>{{ $dc->vehicle_number ?: '—' }}</td>
              <td>{{ $dc->total_cartons }} CTN</td>
              <td style="font-weight:800; color:#059669;">{{ number_format($dc->total_qty) }} pcs</td>
              <td><span class="badge badge-info">{{ $dc->status }}</span></td>
              <td style="text-align:right;">
                <form action="{{ route('dispatch.challans.destroy', $dc->id) }}" method="POST" onsubmit="return confirm('Delete challan {{ $dc->challan_no }}?')" style="display:inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-xs">&times;</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="11" style="text-align:center; padding:30px; color:var(--slate-400);">
                No delivery challans generated yet. Click "+ Generate Delivery Challan" to issue logistics dispatch paperwork.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>

<!-- Generate Challan Modal -->
<div id="challan-modal" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div class="modal-box" style="background:#fff; border-radius:16px; width:100%; max-width:650px; padding:24px; max-height:90vh; overflow-y:auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid var(--slate-200); padding-bottom:12px;">
      <h3 style="margin:0; font-size:1.15rem; font-weight:800;">Generate Delivery & Dispatch Challan</h3>
      <button onclick="closeChallanModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--slate-400);">&times;</button>
    </div>

    <form method="POST" action="{{ route('dispatch.challans.store') }}">
      @csrf

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
        <div class="form-group">
          <label class="form-label">Order Reference # <span style="color:red;">*</span></label>
          <input type="text" name="order_no" class="form-control" required value="ORD-2026-{{ rand(100, 999) }}">
        </div>

        <div class="form-group">
          <label class="form-label">Customer / Consignee <span style="color:red;">*</span></label>
          <select name="customer_name" class="form-control" required>
            <option value="">-- Select Customer --</option>
            @foreach($customers as $c)
              <option value="{{ $c->name }}">{{ $c->name }} ({{ $c->city }})</option>
            @endforeach
            <option value="Zara Retail India Ltd.">Zara Retail India Ltd. (Mumbai)</option>
            <option value="H&M Hennes & Mauritz Pvt. Ltd.">H&M Hennes & Mauritz Pvt. Ltd. (Bengaluru)</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Dispatch Date <span style="color:red;">*</span></label>
          <input type="date" name="dispatch_date" class="form-control" required value="{{ date('Y-m-d') }}">
        </div>

        <div class="form-group">
          <label class="form-label">Transporter / Logistics Carrier <span style="color:red;">*</span></label>
          <select name="transporter_name" class="form-control" required>
            <option value="VRL Logistics Ltd.">VRL Logistics Ltd.</option>
            <option value="TCI Express Ltd.">TCI Express Ltd.</option>
            <option value="Gati KWE Logistics">Gati KWE Logistics</option>
            <option value="Blue Dart Express">Blue Dart Express</option>
            <option value="Self Fleet Truck">Self Fleet Truck</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Lorry Receipt (LR) #</label>
          <input type="text" name="lr_number" class="form-control" placeholder="e.g. LR-98765432">
        </div>

        <div class="form-group">
          <label class="form-label">Vehicle Registration #</label>
          <input type="text" name="vehicle_number" class="form-control" placeholder="e.g. TN-39-AX-1234">
        </div>

        <div class="form-group">
          <label class="form-label">Destination City</label>
          <input type="text" name="destination_city" class="form-control" placeholder="e.g. Mumbai / Delhi">
        </div>

        <div class="form-group">
          <label class="form-label">Total Cartons <span style="color:red;">*</span></label>
          <input type="number" name="total_cartons" class="form-control" required value="20" min="1">
        </div>

        <div class="form-group" style="grid-column:1/-1;">
          <label class="form-label">Total Dispatch Quantity (Pieces) <span style="color:red;">*</span></label>
          <input type="number" name="total_qty" class="form-control" required value="1000" min="1">
        </div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px; border-top:1px solid var(--slate-200); padding-top:14px;">
        <button type="button" class="btn btn-secondary" onclick="closeChallanModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">Create Dispatch Challan</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  function openChallanModal() {
    document.getElementById('challan-modal').style.display = 'flex';
  }
  function closeChallanModal() {
    document.getElementById('challan-modal').style.display = 'none';
  }
</script>
@endpush
@endsection
