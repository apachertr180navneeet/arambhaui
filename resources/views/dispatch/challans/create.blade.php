@extends('layouts.app')

@section('title', 'Generate Dispatch Challan - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><a href="{{ route('dispatch.challans.index') }}" style="color:inherit; text-decoration:none;">Logistics & Dispatch</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Generate Delivery Challan</span></div>
@endsection

@section('content')
<form action="{{ route('dispatch.challans.store') }}" method="POST" id="dispatch-form">
  @csrf

  <div style="display:flex; flex-direction:column; gap:20px;">
    
    <!-- Top Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
      <div>
        <h2 style="margin:0; font-size:1.45rem; font-weight:800; color:var(--slate-900); letter-spacing:-0.02em;">Generate Delivery Challan</h2>
        <p style="margin:3px 0 0; font-size:0.85rem; color:var(--slate-500);">Outbound freight document for finished garment carton consignments.</p>
      </div>

      <div style="display:flex; gap:10px; align-items:center;">
        <a href="{{ route('dispatch.challans.index') }}" class="btn btn-secondary" style="font-weight:700;">Cancel</a>
        <button type="submit" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:6px; font-weight:700; box-shadow:0 2px 8px rgba(37, 99, 235, 0.3);">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
          Save & Issue Challan
        </button>
      </div>
    </div>

    <!-- Step 1: Consignee & Order Reference -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:24px;">
      <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin-top:0; margin-bottom:18px; color:var(--slate-800); border-bottom:1px solid var(--slate-100); padding-bottom:10px;">
        1. Consignee & Order Information
      </h3>

      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:18px;">
        
        <!-- Dynamic Customer Select -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Customer / Consignee <span style="color:#ef4444;">*</span></label>
          <select name="customer_name" id="dc_customer" class="form-control" required onchange="onCustomerSelect(this)">
            <option value="">-- Select Customer --</option>
            @forelse($customers as $c)
              <option value="{{ $c->name }}" data-city="{{ $c->city }}">{{ $c->name }} ({{ $c->city ?? 'No City' }})</option>
            @empty
              <option value="" disabled>No registered Customers found in Master</option>
            @endforelse
          </select>
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Sales Order Ref # <span style="color:#ef4444;">*</span></label>
          <input type="text" name="order_no" class="form-control" required placeholder="e.g. ORD-2026-101" style="font-family:var(--font-mono, monospace); font-weight:700;">
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Dispatch Date <span style="color:#ef4444;">*</span></label>
          <input type="date" name="dispatch_date" class="form-control" required value="{{ date('Y-m-d') }}">
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Destination City</label>
          <input type="text" name="destination_city" id="dc_city" class="form-control" placeholder="e.g. Mumbai / Tiruppur / Bengaluru">
        </div>

      </div>
    </div>

    <!-- Step 2: Logistics & Transportation -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:24px;">
      <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin-top:0; margin-bottom:18px; color:var(--slate-800); border-bottom:1px solid var(--slate-100); padding-bottom:10px;">
        2. Freight, Carrier & Vehicle Information
      </h3>

      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:18px;">
        
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Transporter / Carrier <span style="color:#ef4444;">*</span></label>
          <input type="text" name="transporter_name" class="form-control" required placeholder="e.g. V-Trans Logistics Ltd. / SafeXpress">
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">LR (Lorry Receipt) #</label>
          <input type="text" name="lr_number" class="form-control" placeholder="e.g. LR-987654" style="font-family:var(--font-mono, monospace); font-weight:700;">
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Vehicle / Truck Number</label>
          <input type="text" name="vehicle_number" class="form-control" placeholder="e.g. TN-38-AB-1234" style="font-family:var(--font-mono, monospace); text-transform:uppercase;">
        </div>

      </div>
    </div>

    <!-- Step 3: Packaging & Quantities -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:24px;">
      <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin-top:0; margin-bottom:18px; color:var(--slate-800); border-bottom:1px solid var(--slate-100); padding-bottom:10px;">
        3. Packaging, Cartons & Quantity Breakdown
      </h3>

      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:18px;">
        
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Total Cartons / Boxes <span style="color:#ef4444;">*</span></label>
          <input type="number" min="0" name="total_cartons" id="dc_cartons" class="form-control" required value="0" placeholder="0" oninput="updateSummary()" style="font-weight:700; font-size:1.05rem;">
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Total Finished Garment Pieces (Pcs) <span style="color:#ef4444;">*</span></label>
          <input type="number" min="0" name="total_qty" id="dc_qty" class="form-control" required value="0" placeholder="0" oninput="updateSummary()" style="font-weight:700; font-size:1.05rem; color:#059669;">
        </div>

      </div>

      <!-- Live Summary Footer -->
      <div style="display:flex; justify-content:flex-end; margin-top:20px;">
        <div style="width:340px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:18px; display:flex; flex-direction:column; gap:10px; font-size:0.875rem;">
          <div style="display:flex; justify-content:space-between;">
            <span style="color:var(--slate-600);">Total Cartons:</span>
            <strong id="summary-cartons" style="color:var(--slate-900);">0 Boxes</strong>
          </div>
          <div style="border-top:2px dashed #cbd5e1; padding-top:10px; display:flex; justify-content:space-between; font-size:1.1rem;">
            <span style="font-weight:800; color:var(--slate-900);">Dispatched Units:</span>
            <span style="font-weight:800; color:#059669;" id="summary-qty">0 pcs</span>
          </div>
        </div>
      </div>
    </div>

  </div>
</form>

@push('scripts')
<script>
  function onCustomerSelect(select) {
    const opt = select.options[select.selectedIndex];
    const city = opt.getAttribute('data-city');
    if (city) {
      document.getElementById('dc_city').value = city;
    }
  }

  function updateSummary() {
    const cartons = parseInt(document.getElementById('dc_cartons')?.value) || 0;
    const qty = parseInt(document.getElementById('dc_qty')?.value) || 0;

    document.getElementById('summary-cartons').innerText = cartons.toLocaleString('en-IN') + ' Boxes';
    document.getElementById('summary-qty').innerText = qty.toLocaleString('en-IN') + ' pcs';
  }

  document.addEventListener('DOMContentLoaded', () => {
    updateSummary();
  });
</script>
@endpush
@endsection
