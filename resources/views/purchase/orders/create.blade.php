@extends('layouts.app')

@section('title', 'Create Purchase Order - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><a href="{{ route('purchase.orders.index') }}" style="color:inherit; text-decoration:none;">Purchase Orders</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Create New PO</span></div>
@endsection

@section('content')
<form action="{{ route('purchase.orders.store') }}" method="POST" id="po-form">
  @csrf

  <div style="display:flex; flex-direction:column; gap:20px;">
    
    <!-- Top Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
      <div>
        <h2 style="margin:0; font-size:1.4rem; font-weight:800; color:var(--slate-900);">Generate Purchase Order</h2>
        <p style="margin:2px 0 0; font-size:0.85rem; color:var(--slate-500);">Auto-calculates item amounts, GST taxes, and lines</p>
      </div>

      <div style="display:flex; gap:10px;">
        <a href="{{ route('purchase.orders.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:6px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
          Save & Issue PO
        </button>
      </div>
    </div>

    <!-- PO Details Header Card -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:22px;">
      <h3 style="font-size:1rem; font-weight:700; margin-top:0; margin-bottom:16px; color:var(--slate-800);">1. Order Header Information</h3>

      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
        <div class="form-group">
          <label class="form-label">Vendor / Supplier <span style="color:red;">*</span></label>
          <select name="vendor_name" id="vendor-select" class="form-control" required onchange="onVendorChange(this)">
            <option value="">-- Select Vendor --</option>
            @foreach($vendors as $v)
              <option value="{{ $v->name }}" data-id="{{ $v->id }}">{{ $v->name }} ({{ $v->code }})</option>
            @endforeach
            <option value="Vardhman Textiles Ltd.">Vardhman Textiles Ltd.</option>
            <option value="Arvind Mills Ltd.">Arvind Mills Ltd.</option>
            <option value="YKK India Pvt. Ltd.">YKK India Pvt. Ltd.</option>
          </select>
          <input type="hidden" name="vendor_id" id="vendor_id">
        </div>

        <div class="form-group">
          <label class="form-label">PO Date <span style="color:red;">*</span></label>
          <input type="date" name="po_date" class="form-control" required value="{{ date('Y-m-d') }}">
        </div>

        <div class="form-group">
          <label class="form-label">Expected Delivery Date</label>
          <input type="date" name="expected_delivery_date" class="form-control" value="{{ date('Y-m-d', strtotime('+14 days')) }}">
        </div>

        <div class="form-group">
          <label class="form-label">Receiving Warehouse Location</label>
          <select name="warehouse_location" class="form-control">
            <option value="Main Store - Unit 1 (Tiruppur)">Main Store - Unit 1 (Tiruppur)</option>
            <option value="Dyeing & Weaving Store (Unit 2)">Dyeing & Weaving Store (Unit 2)</option>
            <option value="Finished Goods Warehouse (Mumbai)">Finished Goods Warehouse (Mumbai)</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Line Items Table Card -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:22px;">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <h3 style="font-size:1rem; font-weight:700; margin:0; color:var(--slate-800);">2. Order Line Items</h3>
        <button type="button" class="btn btn-secondary btn-sm" onclick="addLineItem()">+ Add Another Line Item</button>
      </div>

      <div class="table-responsive">
        <table class="data-table" id="items-table" style="width:100%;">
          <thead>
            <tr>
              <th style="width:30%;">Item Name & Description <span style="color:red;">*</span></th>
              <th style="width:12%;">Quantity <span style="color:red;">*</span></th>
              <th style="width:12%;">Unit</th>
              <th style="width:15%;">Unit Rate (₹) <span style="color:red;">*</span></th>
              <th style="width:12%;">GST Tax %</th>
              <th style="width:14%; text-align:right;">Line Total (₹)</th>
              <th style="width:5%;"></th>
            </tr>
          </thead>
          <tbody id="line-items-body">
            <!-- Row 1 Default -->
            <tr class="line-row">
              <td>
                <input type="text" name="items[0][item_name]" class="form-control item-name" required placeholder="e.g. 100% Cotton Combed Fabric 180 GSM" value="100% Cotton Single Jersey Fabric 180 GSM">
              </td>
              <td>
                <input type="number" step="0.01" min="0.01" name="items[0][ordered_qty]" class="form-control item-qty" required value="500" oninput="calculateTotals()">
              </td>
              <td>
                <select name="items[0][unit]" class="form-control">
                  <option value="Meters">Meters</option>
                  <option value="Kg">Kg</option>
                  <option value="Pieces">Pieces</option>
                  <option value="Rolls">Rolls</option>
                </select>
              </td>
              <td>
                <input type="number" step="0.01" min="0" name="items[0][rate]" class="form-control item-rate" required value="280.00" oninput="calculateTotals()">
              </td>
              <td>
                <select name="items[0][tax_percent]" class="form-control item-tax" onchange="calculateTotals()">
                  <option value="5">5% GST</option>
                  <option value="12">12% GST</option>
                  <option value="18">18% GST</option>
                  <option value="0">0% Excluded</option>
                </select>
              </td>
              <td style="text-align:right; font-weight:700;" class="line-total-cell">
                ₹147,000.00
              </td>
              <td style="text-align:center;">
                <button type="button" class="btn btn-danger btn-xs" onclick="removeLineItem(this)">&times;</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Financial Calculation Summary Footers -->
      <div style="display:flex; justify-content:flex-end; margin-top:20px;">
        <div style="width:340px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px; display:flex; flex-direction:column; gap:10px; font-size:0.875rem;">
          <div style="display:flex; justify-content:space-between;">
            <span style="color:var(--slate-600);">Taxable Subtotal:</span>
            <strong id="summary-subtotal">₹140,000.00</strong>
          </div>
          <div style="display:flex; justify-content:space-between;">
            <span style="color:var(--slate-600);">GST Tax Total:</span>
            <strong id="summary-tax">₹7,000.00</strong>
          </div>
          <div style="border-top:2px dashed #cbd5e1; padding-top:10px; display:flex; justify-content:space-between; font-size:1.1rem;">
            <span style="font-weight:800; color:var(--slate-900);">Grand Total:</span>
            <span style="font-weight:800; color:#059669;" id="summary-grand">₹147,000.00</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Notes & Remarks Card -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:22px;">
      <h3 style="font-size:1rem; font-weight:700; margin-top:0; margin-bottom:12px; color:var(--slate-800);">3. Purchase Order Terms & Instructions</h3>
      <div class="form-group">
        <textarea name="notes" class="form-control" rows="3" placeholder="Specify fabric shade tolerance, test certificate requirement, delivery timings, terms..."></textarea>
      </div>
    </div>

  </div>
</form>

@push('scripts')
<script>
  let lineCount = 1;

  function onVendorChange(select) {
    const opt = select.options[select.selectedIndex];
    document.getElementById('vendor_id').value = opt.getAttribute('data-id') || '';
  }

  function addLineItem() {
    const tbody = document.getElementById('line-items-body');
    const idx = lineCount++;
    const tr = document.createElement('tr');
    tr.className = 'line-row';
    tr.innerHTML = `
      <td>
        <input type="text" name="items[${idx}][item_name]" class="form-control item-name" required placeholder="e.g. Polyester Buttons 18L / Polybag">
      </td>
      <td>
        <input type="number" step="0.01" min="0.01" name="items[${idx}][ordered_qty]" class="form-control item-qty" required value="100" oninput="calculateTotals()">
      </td>
      <td>
        <select name="items[${idx}][unit]" class="form-control">
          <option value="Meters">Meters</option>
          <option value="Kg">Kg</option>
          <option value="Pieces">Pieces</option>
          <option value="Rolls">Rolls</option>
          <option value="Gross">Gross</option>
        </select>
      </td>
      <td>
        <input type="number" step="0.01" min="0" name="items[${idx}][rate]" class="form-control item-rate" required value="50.00" oninput="calculateTotals()">
      </td>
      <td>
        <select name="items[${idx}][tax_percent]" class="form-control item-tax" onchange="calculateTotals()">
          <option value="5">5% GST</option>
          <option value="12">12% GST</option>
          <option value="18">18% GST</option>
          <option value="0">0% Excluded</option>
        </select>
      </td>
      <td style="text-align:right; font-weight:700;" class="line-total-cell">
        ₹5,250.00
      </td>
      <td style="text-align:center;">
        <button type="button" class="btn btn-danger btn-xs" onclick="removeLineItem(this)">&times;</button>
      </td>
    `;
    tbody.appendChild(tr);
    calculateTotals();
  }

  function removeLineItem(btn) {
    const tbody = document.getElementById('line-items-body');
    if (tbody.querySelectorAll('.line-row').length <= 1) {
      alert('Purchase Order must have at least one line item.');
      return;
    }
    btn.closest('tr').remove();
    calculateTotals();
  }

  function calculateTotals() {
    let subtotal = 0;
    let taxTotal = 0;

    document.querySelectorAll('.line-row').forEach(row => {
      const qty = parseFloat(row.querySelector('.item-qty')?.value) || 0;
      const rate = parseFloat(row.querySelector('.item-rate')?.value) || 0;
      const taxPct = parseFloat(row.querySelector('.item-tax')?.value) || 0;

      const lineSub = qty * rate;
      const lineTax = (lineSub * taxPct) / 100;
      const lineTotal = lineSub + lineTax;

      row.querySelector('.line-total-cell').innerText = '₹' + lineTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

      subtotal += lineSub;
      taxTotal += lineTax;
    });

    const grandTotal = subtotal + taxTotal;

    document.getElementById('summary-subtotal').innerText = '₹' + subtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('summary-tax').innerText = '₹' + taxTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('summary-grand').innerText = '₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  document.addEventListener('DOMContentLoaded', () => {
    calculateTotals();
  });
</script>
@endpush
@endsection
