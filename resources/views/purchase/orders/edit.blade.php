@extends('layouts.app')

@section('title', 'Edit Purchase Entry - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><a href="{{ route('purchase.orders.index') }}" style="color:inherit; text-decoration:none;">Purchase Entries</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Edit Entry {{ $order->po_number }}</span></div>
@endsection

@php
  $firstItem = $order->items->first();
  $existingThans = $order->than_list;
  if (empty($existingThans) && $firstItem && $firstItem->ordered_qty > 0) {
    $existingThans = [$firstItem->ordered_qty];
  }
  $challanNumber = $order->challan_number;
@endphp

@section('content')
<form action="{{ route('purchase.orders.update', $order->id) }}" method="POST" id="pe-form">
  @csrf
  @method('PUT')

  <div style="display:flex; flex-direction:column; gap:22px;">
    
    <!-- Top Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
      <div>
        <div style="display:flex; align-items:center; gap:8px;">
          <h2 style="margin:0; font-size:1.45rem; font-weight:800; color:var(--slate-900); letter-spacing:-0.02em;">Edit Purchase Entry</h2>
          <span style="font-family:var(--font-mono, monospace); font-weight:800; font-size:1rem; background:#eff6ff; color:#1d4ed8; padding:3px 10px; border-radius:8px; border:1px solid #bfdbfe;">
            {{ $order->po_number }}
          </span>
        </div>
        <p style="margin:4px 0 0; font-size:0.85rem; color:var(--slate-500);">Modify supplier challan details, than-wise meters, or procurement billing terms.</p>
      </div>

      <div style="display:flex; gap:10px; align-items:center;">
        <a href="{{ route('purchase.orders.index') }}" class="btn btn-secondary" style="font-weight:700;">Cancel</a>
        <button type="submit" id="update-pe-btn" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:8px; font-weight:700; box-shadow:0 2px 8px rgba(37, 99, 235, 0.3); padding:10px 22px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
          Update Purchase Entry
        </button>
      </div>
    </div>

    <!-- 1. Header Information (Vendor, Challan, Date) -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:24px;">
      <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--slate-100); padding-bottom:12px; margin-bottom:18px;">
        <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin:0; color:var(--slate-800); display:flex; align-items:center; gap:8px;">
          <span style="width:24px; height:24px; border-radius:6px; background:#eff6ff; color:#2563eb; display:inline-flex; align-items:center; justify-content:center; font-size:0.75rem;">1</span>
          Challan & Supplier Information
        </h3>
      </div>

      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:18px;">
        
        <!-- Vendor Select with Add Link -->
        <div class="form-group" style="margin-bottom:0;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800); margin:0;">Vendor / Supplier <span style="color:#ef4444;">*</span></label>
            <a href="{{ route('masters.vendors.index') }}" target="_blank" style="font-size:0.75rem; color:#2563eb; text-decoration:none; font-weight:600;" title="Open Vendor Master">+ Manage Vendors</a>
          </div>
          <select name="vendor_name" id="vendor-select" class="form-control" required onchange="onVendorChange(this)">
            <option value="{{ $order->vendor_name }}" data-id="{{ $order->vendor_id }}" selected>{{ $order->vendor_name }}</option>
            @foreach($vendors as $v)
              @if($v->name !== $order->vendor_name)
                <option value="{{ $v->name }}" data-id="{{ $v->id }}">{{ $v->name }} ({{ $v->code }}){{ !empty($v->city) ? ' - '.$v->city : '' }}</option>
              @endif
            @endforeach
          </select>
          <input type="hidden" name="vendor_id" id="vendor_id" value="{{ $order->vendor_id }}">
        </div>

        <!-- Supplier Challan / Bill No -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">Supplier Challan / Bill No. <span style="color:#ef4444;">*</span></label>
          <input type="text" name="challan_no" id="challan_no" class="form-control" required value="{{ $challanNumber ?: '' }}" placeholder="e.g. 1076 / CH-9821" style="font-weight:700; letter-spacing:0.5px;">
        </div>

        <!-- Challan / Entry Date -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">Challan / Entry Date <span style="color:#ef4444;">*</span></label>
          <input type="date" name="po_date" class="form-control" required value="{{ is_string($order->po_date) ? substr($order->po_date, 0, 10) : $order->po_date->format('Y-m-d') }}">
        </div>

      </div>
    </div>

    <!-- 2. Product / Fabric Quality Selection (Select ONCE) -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:24px;">
      <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--slate-100); padding-bottom:12px; margin-bottom:18px;">
        <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin:0; color:var(--slate-800); display:flex; align-items:center; gap:8px;">
          <span style="width:24px; height:24px; border-radius:6px; background:#ecfdf5; color:#059669; display:inline-flex; align-items:center; justify-content:center; font-size:0.75rem;">2</span>
          Product / Fabric Quality & Rate (Single Selection)
        </h3>
        <span style="font-size:0.75rem; color:#64748b; font-weight:600; background:#f1f5f9; padding:3px 10px; border-radius:8px;">
          Product selected once • Multiple Than meters below
        </span>
      </div>

      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:18px;">
        
        <!-- Fabric / Product Selector -->
        <div class="form-group" style="margin-bottom:0; grid-column: span 2;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800); margin:0;">Fabric Quality / Item SKU <span style="color:#ef4444;">*</span></label>
            <a href="{{ route('masters.items.index') }}" target="_blank" style="font-size:0.75rem; color:#2563eb; text-decoration:none; font-weight:600;" title="Manage Item Master">+ Manage Items</a>
          </div>
          <select name="item_name" id="item-select" class="form-control" required onchange="onItemChange(this)">
            @if($firstItem)
              <option value="{{ $firstItem->item_name }}" data-id="{{ $firstItem->item_id }}" data-code="{{ $firstItem->item_code }}" selected>
                {{ $firstItem->item_name }} [{{ $firstItem->item_code ?: 'ITEM' }}]
              </option>
            @else
              <option value="">-- Select Fabric / Raw Material SKU --</option>
            @endif
            @foreach($items as $itm)
              @if(!$firstItem || $itm->name !== $firstItem->item_name)
                <option value="{{ $itm->name }}" data-id="{{ $itm->id }}" data-code="{{ $itm->code }}" data-rate="{{ $itm->unit_cost ?? 0 }}" data-unit="{{ $itm->unit ?? 'Meters' }}">
                  {{ $itm->name }} [{{ $itm->code }}] ({{ $itm->category ?? 'Fabric' }})
                </option>
              @endif
            @endforeach
          </select>
          <input type="hidden" name="item_id" id="item_id" value="{{ $firstItem ? $firstItem->item_id : '' }}">
          <input type="hidden" name="item_code" id="item_code" value="{{ $firstItem ? $firstItem->item_code : '' }}">
        </div>

        <!-- Rate Per Meter -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">Rate Per Meter (₹) <span style="color:#ef4444;">*</span></label>
          <div style="position:relative;">
            <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); font-weight:700; color:#64748b;">₹</span>
            <input type="number" step="0.01" min="0" name="rate" id="item_rate" class="form-control" required value="{{ $firstItem ? number_format($firstItem->rate, 2, '.', '') : '35.00' }}" style="padding-left:28px; font-weight:700; font-size:1rem;" oninput="calculateTotals()">
          </div>
        </div>

        <!-- GST Tax % -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">GST Tax Rate</label>
          @php $currTax = $firstItem ? (int)$firstItem->tax_percent : 5; @endphp
          <select name="tax_percent" id="tax_percent" class="form-control" onchange="calculateTotals()">
            <option value="5" {{ $currTax === 5 ? 'selected' : '' }}>5% GST (Standard Fabric)</option>
            <option value="0" {{ $currTax === 0 ? 'selected' : '' }}>0% Excluded / Exempted</option>
            <option value="12" {{ $currTax === 12 ? 'selected' : '' }}>12% GST</option>
            <option value="18" {{ $currTax === 18 ? 'selected' : '' }}>18% GST</option>
          </select>
        </div>

        <input type="hidden" name="unit" id="item_unit" value="Meters">
      </div>
    </div>

    <!-- 3. Than / Roll Meter Entry (Challan Format as per image) -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:24px;">
      
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; border-bottom:1px solid var(--slate-100); padding-bottom:14px; margin-bottom:18px;">
        <div>
          <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin:0; color:var(--slate-800); display:flex; align-items:center; gap:8px;">
            <span style="width:24px; height:24px; border-radius:6px; background:#faf5ff; color:#7c3aed; display:inline-flex; align-items:center; justify-content:center; font-size:0.75rem;">3</span>
            Than-wise Meter Breakdown (Challan Format)
          </h3>
          <p style="margin:3px 0 0; font-size:0.8rem; color:var(--slate-500);">
            Enter individual roll / than meter values. Meters are summed together for overall calculation.
          </p>
        </div>

        <!-- Quick Live Stats Badges -->
        <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
          <div style="background:#f1f5f9; border:1px solid #cbd5e1; border-radius:10px; padding:6px 14px; display:flex; align-items:baseline; gap:6px;">
            <span style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">Total Thans:</span>
            <span id="badge-total-thans" style="font-size:1rem; font-weight:800; color:#0f172a;">0 Thans</span>
          </div>

          <div style="background:#ecfdf5; border:1px solid #a7f3d0; border-radius:10px; padding:6px 14px; display:flex; align-items:baseline; gap:6px;">
            <span style="font-size:0.75rem; color:#065f46; font-weight:700; text-transform:uppercase;">Total Meters:</span>
            <span id="badge-total-meters" style="font-size:1.15rem; font-weight:800; color:#059669;">0.00 Mtr</span>
          </div>
        </div>
      </div>

      <!-- Quick Fast Entry Toolbar -->
      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px; padding:14px 18px; margin-bottom:18px; display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
        <div style="flex:1; min-width:240px; display:flex; gap:8px;">
          <input type="number" step="0.01" min="0" id="quick-than-input" class="form-control" placeholder="Type meter reading (e.g. 148) and press Enter" style="font-size:0.95rem; font-weight:600;">
          <button type="button" class="btn btn-primary" onclick="addSingleThanFromInput()" style="white-space:nowrap; font-weight:700; display:inline-flex; align-items:center; gap:6px;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Than
          </button>
        </div>

        <div style="display:flex; gap:8px; align-items:center;">
          <button type="button" class="btn btn-secondary btn-sm" onclick="openPasteModal()" style="font-weight:700; display:inline-flex; align-items:center; gap:6px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
            Paste Multi-Thans
          </button>
          <button type="button" class="btn btn-secondary btn-sm" onclick="clearAllThans()" style="font-weight:700; color:#dc2626;" title="Remove all than entries">
            Clear All
          </button>
        </div>
      </div>

      <!-- Dual Column Challan Visual Sheet -->
      <div id="than-container-empty" style="text-align:center; padding:36px 16px; color:#94a3b8; border:2px dashed #e2e8f0; border-radius:12px; display:none;">
        <div style="font-weight:700; font-size:0.95rem; color:#64748b; margin-bottom:4px;">No Thans added yet</div>
        <p style="font-size:0.8rem; margin:0 0 12px;">Type meter above and press Enter, or paste multiple than readings from WhatsApp/challan.</p>
      </div>

      <div id="than-container-sheet" style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
        
        <!-- Column 1 (Left Sheet) -->
        <div style="background:#fbfcfe; border:1px solid #e2e8f0; border-radius:14px; padding:16px;">
          <div style="font-weight:700; font-size:0.8rem; color:#64748b; text-transform:uppercase; letter-spacing:0.05em; border-bottom:1px solid #e2e8f0; padding-bottom:8px; margin-bottom:12px; display:flex; justify-content:space-between;">
            <span>Than Column 1</span>
            <span id="col1-subtotal-badge" style="color:#2563eb; font-weight:800;">0.00 Mtr</span>
          </div>
          <div id="col1-thans-list" style="display:flex; flex-direction:column; gap:8px;"></div>
        </div>

        <!-- Column 2 (Right Sheet) -->
        <div style="background:#fbfcfe; border:1px solid #e2e8f0; border-radius:14px; padding:16px;">
          <div style="font-weight:700; font-size:0.8rem; color:#64748b; text-transform:uppercase; letter-spacing:0.05em; border-bottom:1px solid #e2e8f0; padding-bottom:8px; margin-bottom:12px; display:flex; justify-content:space-between;">
            <span>Than Column 2</span>
            <span id="col2-subtotal-badge" style="color:#2563eb; font-weight:800;">0.00 Mtr</span>
          </div>
          <div id="col2-thans-list" style="display:flex; flex-direction:column; gap:8px;"></div>
        </div>

      </div>

    </div>

    <!-- 4. Financial Calculation Summary & Notes Card -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:24px;">
      <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin-top:0; margin-bottom:18px; color:var(--slate-800); border-bottom:1px solid var(--slate-100); padding-bottom:10px;">
        4. Overall Calculation & Terms
      </h3>

      <div style="display:grid; grid-template-columns: 1.2fr 1fr; gap:24px;">
        
        <!-- Notes & Terms -->
        <div>
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            Notes / Transport / Delivery Remarks
          </label>
          @php
            $rawNotes = $order->notes;
            $displayNotes = '';
            if (!empty($rawNotes)) {
              if (str_starts_with(trim($rawNotes), '{')) {
                $decoded = json_decode($rawNotes, true);
                $displayNotes = $decoded['user_notes'] ?? '';
              } else {
                $displayNotes = $rawNotes;
              }
            }
          @endphp
          <textarea name="notes" class="form-control" rows="4" placeholder="e.g. Transport: Shree Balaji Roadlines, Shade tolerance acceptable, Haste: Gaysubhai...">{{ $displayNotes }}</textarea>
          
          <div style="display:flex; gap:16px; margin-top:14px;">
            <div style="flex:1;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800); font-size:0.8rem; margin-bottom:4px;">Payment Status</label>
              <select name="payment_status" class="form-control">
                <option value="Pending" {{ strtolower($order->payment_status) === 'pending' ? 'selected' : '' }}>Pending / Credit</option>
                <option value="Partially Paid" {{ strtolower($order->payment_status) === 'partially paid' ? 'selected' : '' }}>Partially Paid</option>
                <option value="Paid" {{ strtolower($order->payment_status) === 'paid' ? 'selected' : '' }}>Paid Fully</option>
              </select>
            </div>
            <div style="flex:1;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800); font-size:0.8rem; margin-bottom:4px;">Status</label>
              <select name="status" class="form-control">
                <option value="Approved" {{ strtolower($order->status) === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="Draft" {{ strtolower($order->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="Cancelled" {{ strtolower($order->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Calculated Summary Box -->
        <div style="background:#f8fafc; border:1px solid var(--slate-200); border-radius:14px; padding:20px; display:flex; flex-direction:column; gap:12px;">
          <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#64748b;">
            <span>Total Than Count:</span>
            <span style="font-weight:700; color:#0f172a;" id="summary-than-count">0 Thans</span>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#64748b;">
            <span>Total Meter Quantity:</span>
            <span style="font-weight:700; color:#0f172a;" id="summary-total-meters">0.00 Mtr</span>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#64748b;">
            <span>Rate Per Meter:</span>
            <span style="font-weight:700; color:#0f172a;" id="summary-rate">₹0.00</span>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#64748b; border-top:1px dashed #cbd5e1; padding-top:10px;">
            <span>Taxable Subtotal:</span>
            <span style="font-weight:700; color:#0f172a;" id="summary-subtotal">₹0.00</span>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#64748b;">
            <span id="summary-tax-label">GST Tax:</span>
            <span style="font-weight:700; color:#0f172a;" id="summary-tax">₹0.00</span>
          </div>

          <div style="border-top:2px solid #cbd5e1; padding-top:12px; margin-top:4px; display:flex; justify-content:space-between; align-items:baseline;">
            <span style="font-weight:800; font-size:1.1rem; color:var(--slate-900);">Grand Total:</span>
            <span style="font-weight:800; font-size:1.4rem; color:#059669;" id="summary-grand">₹0.00</span>
          </div>
        </div>

      </div>

    </div>

  </div>
</form>

<!-- Modal: Quick Paste Multiple Thans -->
<div id="paste-modal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(15,23,42,0.6); z-index:99999; align-items:center; justify-content:center; padding:16px;">
  <div style="background:#fff; border-radius:16px; max-width:480px; width:100%; padding:24px; box-shadow:0 20px 40px rgba(0,0,0,0.2);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
      <h4 style="margin:0; font-weight:800; font-size:1.1rem; color:#0f172a;">Paste Than Meter Readings</h4>
      <button type="button" onclick="closePasteModal()" style="background:none; border:none; font-size:1.4rem; cursor:pointer; color:#94a3b8;">&times;</button>
    </div>
    <p style="font-size:0.8rem; color:#64748b; margin:0 0 12px;">
      Paste numbers separated by space, comma, or new line:
    </p>
    <textarea id="paste-textarea" class="form-control" rows="6" placeholder="148, 72.50, 112.50, 103.50, 110.50, 105&#10;99.50, 116.00, 126.50, 89.50, 117.50, 98, 117, 99.50" style="font-family:monospace; font-size:0.9rem;"></textarea>
    <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:16px;">
      <button type="button" class="btn btn-secondary" onclick="closePasteModal()">Cancel</button>
      <button type="button" class="btn btn-primary" onclick="applyPastedThans()" style="font-weight:700;">Add to Challan</button>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  // Pre-load existing Than list from database
  let thanList = @json($existingThans);

  function onVendorChange(select) {
    const opt = select.options[select.selectedIndex];
    document.getElementById('vendor_id').value = opt ? (opt.getAttribute('data-id') || '') : '';
  }

  function onItemChange(select) {
    const opt = select.options[select.selectedIndex];
    if (!opt || !opt.value) {
      document.getElementById('item_id').value = '';
      document.getElementById('item_code').value = '';
      return;
    }
    document.getElementById('item_id').value = opt.getAttribute('data-id') || '';
    document.getElementById('item_code').value = opt.getAttribute('data-code') || '';
    
    const rate = parseFloat(opt.getAttribute('data-rate'));
    if (rate > 0) {
      document.getElementById('item_rate').value = rate.toFixed(2);
    }
    calculateTotals();
  }

  function addThanMeter(meterValue) {
    const val = parseFloat(meterValue);
    if (isNaN(val) || val <= 0) return;
    thanList.push(Math.round(val * 100) / 100);
    renderThanSheet();
    calculateTotals();
  }

  function addSingleThanFromInput() {
    const input = document.getElementById('quick-than-input');
    if (!input) return;
    const val = input.value.trim();
    if (val) {
      addThanMeter(val);
      input.value = '';
      input.focus();
    }
  }

  document.getElementById('quick-than-input')?.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      addSingleThanFromInput();
    }
  });

  function removeThan(index) {
    thanList.splice(index, 1);
    renderThanSheet();
    calculateTotals();
  }

  function updateThanMeter(index, newMeter) {
    const val = parseFloat(newMeter);
    if (!isNaN(val) && val >= 0) {
      thanList[index] = Math.round(val * 100) / 100;
      calculateTotals();
      updateColumnSubtotals();
    }
  }

  function clearAllThans() {
    if (thanList.length === 0) return;
    if (confirm('Are you sure you want to clear all than entries?')) {
      thanList = [];
      renderThanSheet();
      calculateTotals();
    }
  }

  function openPasteModal() {
    document.getElementById('paste-modal').style.display = 'flex';
    document.getElementById('paste-textarea').focus();
  }

  function closePasteModal() {
    document.getElementById('paste-modal').style.display = 'none';
    document.getElementById('paste-textarea').value = '';
  }

  function applyPastedThans() {
    const text = document.getElementById('paste-textarea').value;
    if (!text) {
      closePasteModal();
      return;
    }
    const matches = text.match(/\d+(?:\.\d+)?/g);
    if (matches && matches.length > 0) {
      matches.forEach(m => {
        const val = parseFloat(m);
        if (val > 0) thanList.push(Math.round(val * 100) / 100);
      });
      renderThanSheet();
      calculateTotals();
    }
    closePasteModal();
  }

  function renderThanSheet() {
    const emptyBox = document.getElementById('than-container-empty');
    const sheetBox = document.getElementById('than-container-sheet');
    const col1 = document.getElementById('col1-thans-list');
    const col2 = document.getElementById('col2-thans-list');

    col1.innerHTML = '';
    col2.innerHTML = '';

    if (thanList.length === 0) {
      emptyBox.style.display = 'block';
      sheetBox.style.display = 'none';
      return;
    }

    emptyBox.style.display = 'none';
    sheetBox.style.display = 'grid';

    const midpoint = Math.ceil(thanList.length / 2);

    thanList.forEach((meter, i) => {
      const row = document.createElement('div');
      row.style.cssText = 'display:flex; align-items:center; gap:8px; background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:6px 10px;';
      row.innerHTML = `
        <span style="font-weight:700; font-size:0.75rem; color:#64748b; width:64px;">Than #${i + 1}</span>
        <div style="flex:1;">
          <input type="number" step="0.01" min="0" value="${meter}" name="thans[]" 
            class="form-control form-control-sm than-input" 
            style="font-weight:700; font-size:0.9rem; padding:4px 8px;"
            oninput="updateThanMeter(${i}, this.value)"
            onfocus="this.select()">
        </div>
        <span style="font-size:0.75rem; color:#94a3b8; font-weight:600;">Mtr</span>
        <button type="button" onclick="removeThan(${i})" title="Remove this than" style="background:none; border:none; color:#ef4444; font-size:1.1rem; cursor:pointer; line-height:1; padding:2px 4px;">&times;</button>
      `;

      if (i < midpoint) {
        col1.appendChild(row);
      } else {
        col2.appendChild(row);
      }
    });

    updateColumnSubtotals();
  }

  function updateColumnSubtotals() {
    const midpoint = Math.ceil(thanList.length / 2);
    let c1 = 0;
    let c2 = 0;
    thanList.forEach((m, i) => {
      if (i < midpoint) c1 += (m || 0);
      else c2 += (m || 0);
    });

    document.getElementById('col1-subtotal-badge').textContent = c1.toFixed(2) + ' Mtr (' + Math.min(midpoint, thanList.length) + ' Thans)';
    document.getElementById('col2-subtotal-badge').textContent = c2.toFixed(2) + ' Mtr (' + Math.max(0, thanList.length - midpoint) + ' Thans)';
  }

  function calculateTotals() {
    const totalMeters = thanList.reduce((acc, v) => acc + (parseFloat(v) || 0), 0);
    const totalThans = thanList.length;
    const rate = parseFloat(document.getElementById('item_rate')?.value) || 0;
    const taxPct = parseFloat(document.getElementById('tax_percent')?.value) || 0;

    const subtotal = Math.round(totalMeters * rate * 100) / 100;
    const taxAmount = Math.round((subtotal * taxPct / 100.0) * 100) / 100;
    const grandTotal = Math.round((subtotal + taxAmount) * 100) / 100;

    document.getElementById('badge-total-thans').textContent = totalThans + ' Thans';
    document.getElementById('badge-total-meters').textContent = totalMeters.toFixed(2) + ' Mtr';

    document.getElementById('summary-than-count').textContent = totalThans + ' Thans';
    document.getElementById('summary-total-meters').textContent = totalMeters.toFixed(2) + ' Mtr';
    document.getElementById('summary-rate').textContent = '₹' + rate.toFixed(2) + ' / Mtr';
    document.getElementById('summary-subtotal').textContent = '₹' + subtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('summary-tax-label').textContent = `GST Tax (${taxPct}%):`;
    document.getElementById('summary-tax').textContent = '₹' + taxAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('summary-grand').textContent = '₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  document.getElementById('pe-form')?.addEventListener('submit', function(e) {
    if (thanList.length === 0) {
      e.preventDefault();
      alert('Please add at least one Than / Roll meter reading before updating the purchase entry.');
      document.getElementById('quick-than-input')?.focus();
      return false;
    }
  });

  document.addEventListener('DOMContentLoaded', () => {
    renderThanSheet();
    calculateTotals();
  });
</script>
@endpush
