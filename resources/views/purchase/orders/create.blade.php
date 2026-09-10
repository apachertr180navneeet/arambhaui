@extends('layouts.app')

@section('title', 'Create Purchase Order - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><a href="{{ route('purchase.orders.index') }}" style="color:inherit; text-decoration:none;">Purchase Orders</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Create New PO</span></div>
@endsection

@php
  $unitOptionsHtml = '';
  if(isset($units) && count($units) > 0) {
    foreach($units as $u) {
      $unitOptionsHtml .= '<option value="'.e($u->name).'">'.e($u->name).' ('.e($u->symbol ?: $u->code).')</option>';
    }
  } else {
    $unitOptionsHtml = '<option value="Meters">Meters (m)</option><option value="Kg">Kg (kg)</option><option value="Pieces">Pieces (pcs)</option><option value="Rolls">Rolls (rl)</option><option value="Gross">Gross (grs)</option><option value="Cones">Cones (cne)</option>';
  }
@endphp

@section('content')
<form action="{{ route('purchase.orders.store') }}" method="POST" id="po-form">
  @csrf

  <div style="display:flex; flex-direction:column; gap:20px;">
    
    <!-- Top Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
      <div>
        <h2 style="margin:0; font-size:1.45rem; font-weight:800; color:var(--slate-900); letter-spacing:-0.02em;">Generate Purchase Order</h2>
        <p style="margin:3px 0 0; font-size:0.85rem; color:var(--slate-500);">Procurement order with live item rate, GST tax calculation, and inventory routing.</p>
      </div>

      <div style="display:flex; gap:10px; align-items:center;">
        <a href="{{ route('purchase.orders.index') }}" class="btn btn-secondary" style="font-weight:700;">Cancel</a>
        <button type="submit" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:6px; font-weight:700; box-shadow:0 2px 8px rgba(37, 99, 235, 0.3);">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
          Save & Issue PO
        </button>
      </div>
    </div>

    <!-- PO Details Header Card -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:24px;">
      <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin-top:0; margin-bottom:18px; color:var(--slate-800); border-bottom:1px solid var(--slate-100); padding-bottom:10px;">
        1. Order Header Information
      </h3>

      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:18px;">
        
        <!-- Dynamic Vendor Select -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Vendor / Supplier <span style="color:#ef4444;">*</span></label>
          <select name="vendor_name" id="vendor-select" class="form-control" required onchange="onVendorChange(this)">
            <option value="">-- Select Vendor --</option>
            @forelse($vendors as $v)
              <option value="{{ $v->name }}" data-id="{{ $v->id }}">{{ $v->name }} ({{ $v->code }})</option>
            @empty
              <option value="" disabled>No vendors found in Vendor Master</option>
            @endforelse
          </select>
          <input type="hidden" name="vendor_id" id="vendor_id">
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">PO Date <span style="color:#ef4444;">*</span></label>
          <input type="date" name="po_date" class="form-control" required value="{{ date('Y-m-d') }}">
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Expected Delivery Date</label>
          <input type="date" name="expected_delivery_date" class="form-control" value="{{ date('Y-m-d', strtotime('+14 days')) }}">
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Receiving Warehouse Location</label>
          <select name="warehouse_location" class="form-control">
            <option value="Main Raw Material Store">Main Raw Material Store</option>
            <option value="Dyeing & Weaving Store">Dyeing & Weaving Store</option>
            <option value="Trims & Accessories Store">Trims & Accessories Store</option>
            <option value="Finished Goods Warehouse">Finished Goods Warehouse</option>
          </select>
        </div>

      </div>
    </div>

    <!-- Line Items Table Card -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:24px;">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid var(--slate-100); padding-bottom:10px;">
        <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin:0; color:var(--slate-800);">
          2. Order Line Items
        </h3>
        <button type="button" class="btn btn-secondary btn-sm" onclick="addLineItem()" style="font-weight:700; display:inline-flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Add Line Item
        </button>
      </div>

      <!-- Item Datalist for fast auto-completion from Item Master -->
      <datalist id="registered-items-list">
        @if(isset($items))
          @foreach($items as $itm)
            <option value="{{ $itm->name }}" data-rate="{{ $itm->rate ?? $itm->purchase_rate ?? 0 }}" data-unit="{{ $itm->unit ?? 'Meters' }}">[{{ $itm->code }}] {{ $itm->category ?? 'Item' }}</option>
          @endforeach
        @endif
      </datalist>

      <div class="table-responsive">
        <table class="data-table" id="items-table" style="width:100%;">
          <thead>
            <tr>
              <th style="width:34%;">Item Name & Description <span style="color:#ef4444;">*</span></th>
              <th style="width:13%;">Quantity <span style="color:#ef4444;">*</span></th>
              <th style="width:13%;">Unit</th>
              <th style="width:15%;">Unit Rate (₹) <span style="color:#ef4444;">*</span></th>
              <th style="width:12%;">GST Tax %</th>
              <th style="width:13%; text-align:right;">Line Total (₹)</th>
              <th style="width:5%; text-align:center;"></th>
            </tr>
          </thead>
          <tbody id="line-items-body">
            <!-- Row 1 Initial Starting with 0 -->
            <tr class="line-row">
              <td>
                <input type="text" name="items[0][item_name]" list="registered-items-list" class="form-control item-name" required placeholder="Type or select raw fabric, trim, yarn..." onchange="onItemSelect(this)">
              </td>
              <td>
                <input type="number" step="0.01" min="0" name="items[0][ordered_qty]" class="form-control item-qty" required value="0" placeholder="0" oninput="calculateTotals()">
              </td>
              <td>
                <select name="items[0][unit]" class="form-control item-unit">
                  {!! $unitOptionsHtml !!}
                </select>
              </td>
              <td>
                <input type="number" step="0.01" min="0" name="items[0][rate]" class="form-control item-rate" required placeholder="0.00" value="0.00" oninput="calculateTotals()">
              </td>
              <td>
                <select name="items[0][tax_percent]" class="form-control item-tax" onchange="calculateTotals()">
                  <option value="5" selected>5% GST</option>
                  <option value="12">12% GST</option>
                  <option value="18">18% GST</option>
                  <option value="0">0% Excluded</option>
                </select>
              </td>
              <td style="text-align:right; font-weight:800; color:#059669;" class="line-total-cell">
                ₹0.00
              </td>
              <td style="text-align:center;">
                <button type="button" class="btn btn-danger btn-xs" onclick="removeLineItem(this)" style="padding:4px 8px;">&times;</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Financial Calculation Summary Footers -->
      <div style="display:flex; justify-content:flex-end; margin-top:20px;">
        <div style="width:340px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:18px; display:flex; flex-direction:column; gap:10px; font-size:0.875rem;">
          <div style="display:flex; justify-content:space-between;">
            <span style="color:var(--slate-600);">Taxable Subtotal:</span>
            <strong id="summary-subtotal" style="color:var(--slate-900);">₹0.00</strong>
          </div>
          <div style="display:flex; justify-content:space-between;">
            <span style="color:var(--slate-600);">GST Tax Total:</span>
            <strong id="summary-tax" style="color:var(--slate-900);">₹0.00</strong>
          </div>
          <div style="border-top:2px dashed #cbd5e1; padding-top:10px; display:flex; justify-content:space-between; font-size:1.1rem;">
            <span style="font-weight:800; color:var(--slate-900);">Grand Total:</span>
            <span style="font-weight:800; color:#059669;" id="summary-grand">₹0.00</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Notes & Remarks Card -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:24px;">
      <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin-top:0; margin-bottom:12px; color:var(--slate-800);">
        3. Purchase Order Terms & Instructions
      </h3>
      <div class="form-group" style="margin-bottom:0;">
        <textarea name="notes" class="form-control" rows="3" placeholder="Specify fabric shade tolerance, laboratory test certificate requirement, delivery gate timings, packaging instructions..."></textarea>
      </div>
    </div>

  </div>
</form>

@push('scripts')
<script>
  let lineCount = 1;
  const UNIT_OPTIONS_HTML = `{!! $unitOptionsHtml !!}`;

  function onVendorChange(select) {
    const opt = select.options[select.selectedIndex];
    document.getElementById('vendor_id').value = opt.getAttribute('data-id') || '';
  }

  function onItemSelect(input) {
    const val = input.value;
    const datalist = document.getElementById('registered-items-list');
    if (!datalist) return;

    for (let opt of datalist.options) {
      if (opt.value === val) {
        const row = input.closest('tr');
        const rate = opt.getAttribute('data-rate');
        const unit = opt.getAttribute('data-unit');
        if (rate && row.querySelector('.item-rate')) {
          row.querySelector('.item-rate').value = parseFloat(rate).toFixed(2);
        }
        if (unit && row.querySelector('.item-unit')) {
          row.querySelector('.item-unit').value = unit;
        }
        calculateTotals();
        break;
      }
    }
  }

  function addLineItem() {
    const tbody = document.getElementById('line-items-body');
    const idx = lineCount++;
    const tr = document.createElement('tr');
    tr.className = 'line-row';
    tr.innerHTML = `
      <td>
        <input type="text" name="items[${idx}][item_name]" list="registered-items-list" class="form-control item-name" required placeholder="Type or select item description..." onchange="onItemSelect(this)">
      </td>
      <td>
        <input type="number" step="0.01" min="0" name="items[${idx}][ordered_qty]" class="form-control item-qty" required value="0" placeholder="0" oninput="calculateTotals()">
      </td>
      <td>
        <select name="items[${idx}][unit]" class="form-control item-unit">
          ${UNIT_OPTIONS_HTML}
        </select>
      </td>
      <td>
        <input type="number" step="0.01" min="0" name="items[${idx}][rate]" class="form-control item-rate" required placeholder="0.00" value="0.00" oninput="calculateTotals()">
      </td>
      <td>
        <select name="items[${idx}][tax_percent]" class="form-control item-tax" onchange="calculateTotals()">
          <option value="5" selected>5% GST</option>
          <option value="12">12% GST</option>
          <option value="18">18% GST</option>
          <option value="0">0% Excluded</option>
        </select>
      </td>
      <td style="text-align:right; font-weight:800; color:#059669;" class="line-total-cell">
        ₹0.00
      </td>
      <td style="text-align:center;">
        <button type="button" class="btn btn-danger btn-xs" onclick="removeLineItem(this)" style="padding:4px 8px;">&times;</button>
      </td>
    `;
    tbody.appendChild(tr);
    calculateTotals();
  }

  function removeLineItem(btn) {
    const tbody = document.getElementById('line-items-body');
    if (tbody.querySelectorAll('.line-row').length <= 1) {
      if (window.Toast) {
        Toast.fire({
          icon: 'warning',
          title: 'PO must have at least one line item'
        });
      } else {
        alert('PO must have at least one line item.');
      }
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

      const totalCell = row.querySelector('.line-total-cell');
      if (totalCell) {
        totalCell.innerText = '₹' + lineTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      }

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
