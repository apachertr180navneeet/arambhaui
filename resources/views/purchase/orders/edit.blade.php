@extends('layouts.app')

@section('title', 'Edit Purchase Order - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><a href="{{ route('purchase.orders.index') }}" style="color:inherit; text-decoration:none;">Purchase Orders</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Edit PO {{ $order->po_number }}</span></div>
@endsection

@php
  $unitList = [];
  if(isset($units) && count($units) > 0) {
    foreach($units as $u) {
      $unitList[] = ['name' => $u->name, 'label' => $u->name . ' (' . ($u->symbol ?: $u->code) . ')'];
    }
  } else {
    $unitList = [
      ['name' => 'Meters', 'label' => 'Meters (m)'],
      ['name' => 'Kg', 'label' => 'Kg (kg)'],
      ['name' => 'Pieces', 'label' => 'Pieces (pcs)'],
      ['name' => 'Rolls', 'label' => 'Rolls (rl)'],
      ['name' => 'Gross', 'label' => 'Gross (grs)'],
      ['name' => 'Cones', 'label' => 'Cones (cne)']
    ];
  }

  $defaultUnitOptionsHtml = '';
  foreach($unitList as $u) {
    $defaultUnitOptionsHtml .= '<option value="'.e($u['name']).'">'.e($u['label']).'</option>';
  }
@endphp

@section('content')
<form action="{{ route('purchase.orders.update', $order->id) }}" method="POST" id="po-form">
  @csrf
  @method('PUT')

  <div style="display:flex; flex-direction:column; gap:20px;">
    
    <!-- Top Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
      <div>
        <div style="display:flex; align-items:center; gap:8px;">
          <h2 style="margin:0; font-size:1.45rem; font-weight:800; color:var(--slate-900); letter-spacing:-0.02em;">Edit Purchase Order</h2>
          <span style="font-family:var(--font-mono, monospace); font-weight:800; font-size:1rem; background:#eff6ff; color:#1d4ed8; padding:3px 10px; border-radius:8px; border:1px solid #bfdbfe;">
            {{ $order->po_number }}
          </span>
        </div>
        <p style="margin:4px 0 0; font-size:0.85rem; color:var(--slate-500);">Modify line items, quantities, delivery dates, or fulfillment terms.</p>
      </div>

      <div style="display:flex; gap:10px; align-items:center;">
        <a href="{{ route('purchase.orders.index') }}" class="btn btn-secondary" style="font-weight:700;">Cancel</a>
        <button type="submit" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:6px; font-weight:700; box-shadow:0 2px 8px rgba(37, 99, 235, 0.3);">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
          Update Purchase Order
        </button>
      </div>
    </div>

    <!-- PO Details Header Card -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:24px;">
      <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin-top:0; margin-bottom:18px; color:var(--slate-800); border-bottom:1px solid var(--slate-100); padding-bottom:10px;">
        1. Order Header Information
      </h3>

      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:18px;">
        
        <!-- Vendor Select -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Vendor / Supplier <span style="color:#ef4444;">*</span></label>
          <select name="vendor_name" id="vendor-select" class="form-control" required onchange="onVendorChange(this)">
            <option value="{{ $order->vendor_name }}" data-id="{{ $order->vendor_id }}" selected>{{ $order->vendor_name }}</option>
            @foreach($vendors as $v)
              @if($v->name !== $order->vendor_name)
                <option value="{{ $v->name }}" data-id="{{ $v->id }}">{{ $v->name }} ({{ $v->code }})</option>
              @endif
            @endforeach
          </select>
          <input type="hidden" name="vendor_id" id="vendor_id" value="{{ $order->vendor_id }}">
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">PO Date <span style="color:#ef4444;">*</span></label>
          <input type="date" name="po_date" class="form-control" required value="{{ $order->po_date }}">
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Expected Delivery Date</label>
          <input type="date" name="expected_delivery_date" class="form-control" value="{{ $order->expected_delivery_date }}">
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Receiving Warehouse Location</label>
          <input type="text" name="warehouse_location" class="form-control" value="{{ $order->warehouse_location ?: 'Main Raw Material Store' }}">
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Order Status</label>
          <select name="status" class="form-control">
            <option value="Draft" {{ $order->status === 'Draft' ? 'selected' : '' }}>Draft</option>
            <option value="Approved" {{ $order->status === 'Approved' ? 'selected' : '' }}>Approved</option>
            <option value="Partially Received" {{ $order->status === 'Partially Received' ? 'selected' : '' }}>Partially Received</option>
            <option value="Received" {{ $order->status === 'Received' ? 'selected' : '' }}>Received</option>
            <option value="Cancelled" {{ $order->status === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
          </select>
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Payment Status</label>
          <select name="payment_status" class="form-control">
            <option value="Pending" {{ $order->payment_status === 'Pending' ? 'selected' : '' }}>Pending</option>
            <option value="Partially Paid" {{ $order->payment_status === 'Partially Paid' ? 'selected' : '' }}>Partially Paid</option>
            <option value="Paid" {{ $order->payment_status === 'Paid' ? 'selected' : '' }}>Paid</option>
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

      <!-- Item Datalist for autocomplete -->
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
            @forelse ($order->items as $idx => $itm)
              <tr class="line-row">
                <td>
                  <input type="text" name="items[{{ $idx }}][item_name]" list="registered-items-list" class="form-control item-name" required value="{{ $itm->item_name }}" onchange="onItemSelect(this)">
                </td>
                <td>
                  <input type="number" step="0.01" min="0" name="items[{{ $idx }}][ordered_qty]" class="form-control item-qty" required value="{{ $itm->ordered_qty }}" oninput="calculateTotals()">
                </td>
                <td>
                  <select name="items[{{ $idx }}][unit]" class="form-control item-unit">
                    @foreach($unitList as $u)
                      <option value="{{ $u['name'] }}" {{ (strtolower($itm->unit) === strtolower($u['name']) || strtolower($itm->unit) === strtolower($u['label'])) ? 'selected' : '' }}>
                        {{ $u['label'] }}
                      </option>
                    @endforeach
                  </select>
                </td>
                <td>
                  <input type="number" step="0.01" min="0" name="items[{{ $idx }}][rate]" class="form-control item-rate" required value="{{ $itm->rate }}" oninput="calculateTotals()">
                </td>
                <td>
                  <select name="items[{{ $idx }}][tax_percent]" class="form-control item-tax" onchange="calculateTotals()">
                    <option value="5" {{ $itm->tax_percent == 5 ? 'selected' : '' }}>5% GST</option>
                    <option value="12" {{ $itm->tax_percent == 12 ? 'selected' : '' }}>12% GST</option>
                    <option value="18" {{ $itm->tax_percent == 18 ? 'selected' : '' }}>18% GST</option>
                    <option value="0" {{ $itm->tax_percent == 0 ? 'selected' : '' }}>0% Excluded</option>
                  </select>
                </td>
                <td style="text-align:right; font-weight:800; color:#059669;" class="line-total-cell">
                  ₹{{ number_format($itm->total_amount, 2) }}
                </td>
                <td style="text-align:center;">
                  <button type="button" class="btn btn-danger btn-xs" onclick="removeLineItem(this)" style="padding:4px 8px;">&times;</button>
                </td>
              </tr>
            @empty
              <tr class="line-row">
                <td>
                  <input type="text" name="items[0][item_name]" list="registered-items-list" class="form-control item-name" required placeholder="Type or select raw material..." onchange="onItemSelect(this)">
                </td>
                <td>
                  <input type="number" step="0.01" min="0" name="items[0][ordered_qty]" class="form-control item-qty" required value="0" oninput="calculateTotals()">
                </td>
                <td>
                  <select name="items[0][unit]" class="form-control item-unit">
                    {!! $defaultUnitOptionsHtml !!}
                  </select>
                </td>
                <td>
                  <input type="number" step="0.01" min="0" name="items[0][rate]" class="form-control item-rate" required value="0.00" oninput="calculateTotals()">
                </td>
                <td>
                  <select name="items[0][tax_percent]" class="form-control item-tax" onchange="calculateTotals()">
                    <option value="5">5% GST</option>
                    <option value="12">12% GST</option>
                    <option value="18">18% GST</option>
                    <option value="0">0% Excluded</option>
                  </select>
                </td>
                <td style="text-align:right; font-weight:800; color:#059669;" class="line-total-cell">₹0.00</td>
                <td style="text-align:center;">
                  <button type="button" class="btn btn-danger btn-xs" onclick="removeLineItem(this)" style="padding:4px 8px;">&times;</button>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Financial Calculation Summary Footers -->
      <div style="display:flex; justify-content:flex-end; margin-top:20px;">
        <div style="width:340px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:18px; display:flex; flex-direction:column; gap:10px; font-size:0.875rem;">
          <div style="display:flex; justify-content:space-between;">
            <span style="color:var(--slate-600);">Taxable Subtotal:</span>
            <strong id="summary-subtotal" style="color:var(--slate-900);">₹{{ number_format($order->subtotal, 2) }}</strong>
          </div>
          <div style="display:flex; justify-content:space-between;">
            <span style="color:var(--slate-600);">GST Tax Total:</span>
            <strong id="summary-tax" style="color:var(--slate-900);">₹{{ number_format($order->tax_total, 2) }}</strong>
          </div>
          <div style="border-top:2px dashed #cbd5e1; padding-top:10px; display:flex; justify-content:space-between; font-size:1.1rem;">
            <span style="font-weight:800; color:var(--slate-900);">Grand Total:</span>
            <span style="font-weight:800; color:#059669;" id="summary-grand">₹{{ number_format($order->grand_total, 2) }}</span>
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
        <textarea name="notes" class="form-control" rows="3" placeholder="Specify fabric shade tolerance, laboratory test certificate requirement, delivery gate timings, packaging instructions...">{{ $order->notes }}</textarea>
      </div>
    </div>

  </div>
</form>

@push('scripts')
<script>
  let lineCount = {{ count($order->items) + 1 }};
  const UNIT_OPTIONS_HTML = `{!! $defaultUnitOptionsHtml !!}`;

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
