@extends('layouts.app')

@section('title', 'New Purchase Entry - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><a href="{{ route('purchase.orders.index') }}" style="color:inherit; text-decoration:none;">Purchase Entries</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>New Purchase Entry</span></div>
@endsection

@section('content')
<form action="{{ route('purchase.orders.store') }}" method="POST" id="pe-form">
  @csrf

  <div style="display:flex; flex-direction:column; gap:22px;">
    
    <!-- Top Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
      <div>
        <h2 style="margin:0; font-size:1.45rem; font-weight:800; color:var(--slate-900); letter-spacing:-0.02em;">
          New Purchase Entry
        </h2>
        <p style="margin:3px 0 0; font-size:0.85rem; color:var(--slate-500);">
          Record fabric inward challan, than-wise meter breakdown, supplier rate & procurement billing.
        </p>
      </div>

      <div style="display:flex; gap:10px; align-items:center;">
        <a href="{{ route('purchase.orders.index') }}" class="btn btn-secondary" style="font-weight:700;">Cancel</a>
        <button type="submit" id="save-pe-btn" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:8px; font-weight:700; box-shadow:0 2px 8px rgba(37, 99, 235, 0.3); padding:10px 22px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
          Save Purchase Entry
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
        
        <!-- Vendor / Supplier Select with Live Refresh & Add Link -->
        <div class="form-group" style="margin-bottom:0;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800); margin:0;">
              Vendor / Supplier <span style="color:#ef4444;">*</span>
            </label>
            <a href="{{ route('masters.vendors.index') }}" target="_blank" style="font-size:0.75rem; color:#2563eb; text-decoration:none; font-weight:600;" title="Open Vendor Master to register suppliers">+ Manage Vendors</a>
          </div>
          <select name="vendor_name" id="vendor-select" class="form-control" required onchange="onVendorChange(this)">
            <option value="">-- Select Vendor / Supplier --</option>
            @forelse($vendors as $v)
              <option value="{{ $v->name }}" data-id="{{ $v->id }}" data-code="{{ $v->code }}" data-city="{{ $v->city ?? '' }}">
                {{ $v->name }} ({{ $v->code }}){{ !empty($v->city) ? ' - '.$v->city : '' }}
              </option>
            @empty
              <option value="" disabled>No vendors found in Vendor Master</option>
            @endforelse
          </select>
          <input type="hidden" name="vendor_id" id="vendor_id">
        </div>

        <!-- Supplier Challan / Bill No -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            Supplier Challan / Bill No. <span style="color:#ef4444;">*</span>
          </label>
          <input type="text" name="challan_no" id="challan_no" class="form-control" required placeholder="e.g. 1076 / CH-9821" style="font-weight:700; letter-spacing:0.5px;">
        </div>

        <!-- Challan / Entry Date -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            Challan / Entry Date <span style="color:#ef4444;">*</span>
          </label>
          <input type="date" name="po_date" class="form-control" required value="{{ date('Y-m-d') }}">
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
            <label class="form-label" style="font-weight:700; color:var(--slate-800); margin:0;">
              Fabric Quality / Item SKU <span style="color:#ef4444;">*</span>
            </label>
            <a href="{{ route('masters.items.index') }}" target="_blank" style="font-size:0.75rem; color:#2563eb; text-decoration:none; font-weight:600;" title="Manage Item Master">+ Manage Items</a>
          </div>
          <select name="item_name" id="item-select" class="form-control" required onchange="onItemChange(this)">
            <option value="">-- Select Fabric / Raw Material SKU --</option>
            @forelse($items as $itm)
              <option value="{{ $itm->name }}" data-id="{{ $itm->id }}" data-code="{{ $itm->code }}" data-rate="{{ $itm->unit_cost ?? 0 }}" data-unit="{{ $itm->unit ?? 'Meters' }}">
                {{ $itm->name }} [{{ $itm->code }}] ({{ $itm->category ?? 'Fabric' }})
              </option>
            @empty
              <option value="" disabled>No items found in Item Master</option>
            @endforelse
          </select>
          <input type="hidden" name="item_id" id="item_id">
          <input type="hidden" name="item_code" id="item_code">
        </div>

        <!-- Rate Per Meter -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            Rate Per Meter (₹) <span style="color:#ef4444;">*</span>
          </label>
          <div style="position:relative;">
            <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); font-weight:700; color:#64748b;">₹</span>
            <input type="number" step="0.01" min="0" name="rate" id="item_rate" class="form-control" required placeholder="0.00" value="35.00" style="padding-left:28px; font-weight:700; font-size:1rem;" oninput="calculateTotals()">
          </div>
        </div>

        <!-- GST Tax % -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            GST Tax Rate
          </label>
          <select name="tax_percent" id="tax_percent" class="form-control" onchange="calculateTotals()">
            <option value="5" selected>5% GST (Standard Fabric)</option>
            <option value="0">0% Excluded / Exempted</option>
            <option value="12">12% GST</option>
            <option value="18">18% GST</option>
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

      <!-- Dual Column Challan Visual Sheet (Matches the physical paper slip) -->
      <div id="than-container-empty" style="text-align:center; padding:36px 16px; color:#94a3b8; border:2px dashed #e2e8f0; border-radius:12px; display:none;">
        <div style="font-weight:700; font-size:0.95rem; color:#64748b; margin-bottom:4px;">No Thans added yet</div>
        <p style="font-size:0.8rem; margin:0 0 12px;">Type meter above and press Enter, or paste multiple than readings from WhatsApp/challan.</p>
        <button type="button" class="btn btn-secondary btn-sm" onclick="loadSampleChallanThans()">Load Challan Sample (14 Thans)</button>
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
          <textarea name="notes" class="form-control" rows="4" placeholder="e.g. Transport: Shree Balaji Roadlines, Shade tolerance acceptable, Haste: Gaysubhai..."></textarea>
          
          <div style="display:flex; gap:16px; margin-top:14px;">
            <div style="flex:1;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800); font-size:0.8rem; margin-bottom:4px;">Payment Status</label>
              <select name="payment_status" class="form-control">
                <option value="Pending" selected>Pending / Credit</option>
                <option value="Partially Paid">Partially Paid</option>
                <option value="Paid">Paid Fully</option>
              </select>
            </div>
            <div style="flex:1;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800); font-size:0.8rem; margin-bottom:4px;">Status</label>
              <select name="status" class="form-control">
                <option value="Approved" selected>Approved</option>
                <option value="Draft">Draft</option>
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
            <span style="font-weight:700; color:#0f172a;" id="summary-rate">₹35.00</span>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#64748b; border-top:1px dashed #cbd5e1; padding-top:10px;">
            <span>Taxable Subtotal:</span>
            <span style="font-weight:700; color:#0f172a;" id="summary-subtotal">₹0.00</span>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#64748b;">
            <span id="summary-tax-label">GST Tax (5%):</span>
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
      Paste numbers separated by space, comma, or new line (e.g. from WhatsApp or Challan sheet):
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
  // In-memory array of Than meter readings
  let thanList = [];

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

  // Add a single than meter reading
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

  // Handle Enter key in quick-than-input
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

  function loadSampleChallanThans() {
    // Sample from the challan slip image
    thanList = [148, 72.50, 112.50, 103.50, 110.50, 105, 99.50, 116, 126.50, 89.50, 117.50, 98, 117, 99.50];
    document.getElementById('challan_no').value = '1076';
    document.getElementById('item_rate').value = '35.00';
    renderThanSheet();
    calculateTotals();
  }

  // Modal Paste Support
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
    // Match all integer/decimal numbers
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

  // Render Dual-Column Visual Sheet
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

    // Split evenly between Col 1 and Col 2
    const midpoint = Math.ceil(thanList.length / 2);

    thanList.forEach((meter, i) => {
      const row = document.createElement('div');
      row.style.cssText = 'display:flex; align-items:center; gap:8px; background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:6px 10px; transition:border-color 0.2s ease;';
      row.innerHTML = `
        <span style="font-weight:700; font-size:0.75rem; color:#64748b; width:64px;">Than #${i + 1}</span>
        <div style="flex:1; position:relative;">
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

  // Calculate overall metrics
  function calculateTotals() {
    const totalMeters = thanList.reduce((acc, v) => acc + (parseFloat(v) || 0), 0);
    const totalThans = thanList.length;
    const rate = parseFloat(document.getElementById('item_rate')?.value) || 0;
    const taxPct = parseFloat(document.getElementById('tax_percent')?.value) || 0;

    const subtotal = Math.round(totalMeters * rate * 100) / 100;
    const taxAmount = Math.round((subtotal * taxPct / 100.0) * 100) / 100;
    const grandTotal = Math.round((subtotal + taxAmount) * 100) / 100;

    // Badges
    document.getElementById('badge-total-thans').textContent = totalThans + ' Thans';
    document.getElementById('badge-total-meters').textContent = totalMeters.toFixed(2) + ' Mtr';

    // Summary Box
    document.getElementById('summary-than-count').textContent = totalThans + ' Thans';
    document.getElementById('summary-total-meters').textContent = totalMeters.toFixed(2) + ' Mtr';
    document.getElementById('summary-rate').textContent = '₹' + rate.toFixed(2) + ' / Mtr';
    document.getElementById('summary-subtotal').textContent = '₹' + subtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('summary-tax-label').textContent = `GST Tax (${taxPct}%):`;
    document.getElementById('summary-tax').textContent = '₹' + taxAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('summary-grand').textContent = '₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  // Live fetch active vendors from Vendor Master API to guarantee fresh list
  function loadFreshVendors() {
    fetch("{{ route('masters.vendors.index') }}", {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(res => res.json())
    .then(vendors => {
      if (Array.isArray(vendors) && vendors.length > 0) {
        const select = document.getElementById('vendor-select');
        if (!select) return;
        const currentVal = select.value;
        select.innerHTML = '<option value="">-- Select Vendor / Supplier --</option>';
        vendors.forEach(v => {
          const opt = document.createElement('option');
          opt.value = v.name;
          opt.setAttribute('data-id', v.id);
          opt.setAttribute('data-code', v.code || '');
          opt.textContent = v.name + (v.code ? ' (' + v.code + ')' : '') + (v.city ? ' - ' + v.city : '');
          if (currentVal && (v.name === currentVal || v.id == currentVal)) {
            opt.selected = true;
          }
          select.appendChild(opt);
        });
      }
    })
    .catch(() => {});
  }

  // Validate on submit
  document.getElementById('pe-form')?.addEventListener('submit', function(e) {
    if (thanList.length === 0) {
      e.preventDefault();
      alert('Please add at least one Than / Roll meter reading before saving the purchase entry.');
      document.getElementById('quick-than-input')?.focus();
      return false;
    }
  });

  document.addEventListener('DOMContentLoaded', () => {
    loadFreshVendors();
    renderThanSheet();
    calculateTotals();
  });
</script>
@endpush
