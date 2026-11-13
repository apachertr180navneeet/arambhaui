@extends('layouts.app')

@section('title', 'Generate Dispatch Challan - ' . ($companyName ?? 'aarambh'))

@section('breadcrumb')
  <div class="breadcrumb-item"><a href="{{ route('dispatch.challans.index') }}" style="color:inherit; text-decoration:none;">Logistics & Dispatch</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Generate Delivery Challan</span></div>
@endsection

@push('styles')
<style>
  .dc-items-table th {
    background: #f8fafc;
    color: var(--slate-700, #334155);
    font-size: 0.775rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 10px 12px;
    border-bottom: 2px solid var(--slate-200, #e2e8f0);
    white-space: nowrap;
  }
  .dc-items-table td {
    padding: 8px 10px;
    vertical-align: middle;
    border-bottom: 1px solid var(--slate-100, #f1f5f9);
  }
  .batch-badge {
    background: #eef2ff;
    color: #4338ca;
    border: 1px solid #c7d2fe;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
  }
  .stock-badge {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
    white-space: nowrap;
  }
  .stock-warn {
    background: #fff1f2;
    color: #be123c;
    border: 1px solid #fecdd3;
  }
</style>
@endpush

@section('content')
<form action="{{ route('dispatch.challans.store') }}" method="POST" id="dispatch-form" onsubmit="return validateDispatchSubmit(event)">
  @csrf

  <div style="display:flex; flex-direction:column; gap:20px;">
    
    <!-- Top Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
      <div>
        <div style="display:flex; align-items:center; gap:8px;">
          <h2 style="margin:0; font-size:1.45rem; font-weight:800; color:var(--slate-900); letter-spacing:-0.02em;">Generate Delivery Challan</h2>
          <span style="font-family:var(--font-mono, monospace); font-weight:800; font-size:0.9rem; background:#e0f2fe; color:#0369a1; padding:3px 10px; border-radius:8px; border:1px solid #bae6fd;">
            {{ $nextChallanNo }}
          </span>
        </div>
        <p style="margin:3px 0 0; font-size:0.85rem; color:var(--slate-500);">Outbound freight delivery challan with batch-wise stock verification and automatic inventory deduction.</p>
      </div>

      <div style="display:flex; gap:10px; align-items:center;">
        <a href="{{ route('dispatch.challans.index') }}" class="btn btn-secondary" style="font-weight:700;">Cancel</a>
        <button type="submit" class="btn btn-primary" id="save-challan-btn" style="display:inline-flex; align-items:center; gap:6px; font-weight:700; box-shadow:0 2px 8px rgba(37, 99, 235, 0.3);">
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
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800); margin:0;">
              Customer / Consignee <span style="color:#ef4444;">*</span>
            </label>
            <a href="{{ route('masters.customers.index') }}" target="_blank" style="font-size:0.75rem; color:#2563eb; text-decoration:none; font-weight:600;" title="Manage Customer Master">+ Manage Customers</a>
          </div>
          <select name="customer_name" id="dc_customer" class="form-control" required onchange="onCustomerSelect(this)">
            <option value="">-- Select Customer (Master) --</option>
            @forelse($customers as $c)
              <option value="{{ $c->name }}" data-id="{{ $c->id }}" data-city="{{ $c->city }}" {{ (request('customer_id') == $c->id || request('customer') == $c->name) ? 'selected' : '' }}>
                {{ $c->name }} ({{ $c->city ?? 'No City' }})
              </option>
            @empty
              <option value="" disabled>No registered Customers found in Master</option>
            @endforelse
          </select>
          <input type="hidden" name="customer_id" id="dc_customer_id" value="{{ request('customer_id') }}">
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Bill / Order Ref # <span style="color:#ef4444;">*</span></label>
          <div style="position:relative; display:flex; align-items:center;">
            <input type="text" name="order_no" id="dc_order_no" class="form-control" required value="{{ old('order_no', 'ORD-' . date('Y') . '-' . str_pad(rand(100, 999), 4, '0', STR_PAD_LEFT)) }}" placeholder="e.g. ORD-2026-101" style="font-family:var(--font-mono, monospace); font-weight:700;">
          </div>
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Sales / Dispatch Date <span style="color:#ef4444;">*</span></label>
          <input type="date" name="dispatch_date" class="form-control" required value="{{ date('Y-m-d') }}">
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Destination City</label>
          <input type="text" name="destination_city" id="dc_city" class="form-control" placeholder="e.g. Mumbai / Tiruppur / Surat">
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

    <!-- Step 3: Product Items (Tally Format: Raw & Finished Goods) -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:24px;">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:16px; border-bottom:1px solid var(--slate-100); padding-bottom:12px;">
        <div>
          <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin:0; color:var(--slate-800); display:flex; align-items:center; gap:8px;">
            <span>3. Items & Pricing (Raw Fabric & Finished Goods)</span>
            <span class="badge" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; font-size:0.75rem; font-weight:700;">Sales = Raw + Finished</span>
          </h3>
          <p style="margin:4px 0 0; font-size:0.8rem; color:var(--slate-500);">
            Tally format sales entry: add fabric or finished garments, rates, than meter breakdowns, taxes and auto-totals.
          </p>
        </div>

        <button type="button" class="btn btn-secondary btn-sm" onclick="addNewDispatchRow()" style="font-weight:700; display:inline-flex; align-items:center; gap:6px; background:#f0fdf4; border-color:#86efac; color:#166534;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Add Item Row
        </button>
      </div>

      <!-- Line Items Repeater Table matching Image 3 -->
      <div class="table-responsive">
        <table class="data-table dc-items-table" id="dc-items-table" style="width:100%;">
          <thead>
            <tr>
              <th style="width:40px; text-align:center;">#</th>
              <th style="width:26%;">Item Name (Raw / Finished) <span style="color:red;">*</span></th>
              <th style="width:11%;">Rate (₹) <span style="color:red;">*</span></th>
              <th style="width:12%;">Qty (Mtr/Pcs) <span style="color:red;">*</span></th>
              <th style="width:13%;">Than Count</th>
              <th style="width:9%;">Tax %</th>
              <th style="width:11%;">Amount (₹)</th>
              <th style="width:11%;">Tax Amt (₹)</th>
              <th style="width:12%;">Total (₹)</th>
              <th style="width:36px; text-align:center;"></th>
            </tr>
          </thead>
          <tbody id="dc-items-tbody">
            <!-- Row 0 Pre-created for convenience -->
            <tr class="dc-item-row" data-index="0">
              <td style="text-align:center; font-weight:700; color:var(--slate-400);" class="row-num">1</td>
              <td>
                <select name="items[0][item_id]" class="form-control item-select" onchange="onItemChange(this, 0)" required style="font-size:0.85rem; font-weight:600;">
                  <option value="">-- Select Item (Item Master) --</option>
                  @foreach($items as $itm)
                    <option value="{{ $itm->id }}" data-name="{{ $itm->name }}" data-code="{{ $itm->code }}" data-category="{{ $itm->category ?? 'Finished Goods' }}" data-stock="{{ $itm->current_stock }}" data-unit="{{ $itm->unit ?? 'Pcs' }}" data-cost="{{ $itm->unit_cost ?? 0 }}">
                      {{ $itm->name }} ({{ $itm->code }}) [{{ $itm->unit ?? 'Pcs' }}]
                    </option>
                  @endforeach
                </select>
                <input type="hidden" name="items[0][style_name]" class="style-name-input" value="">
                <input type="hidden" name="items[0][item_category]" class="item-cat-input" value="">
                <input type="hidden" name="items[0][unit]" class="unit-input" value="Pcs">
                <input type="hidden" name="items[0][batch_no]" class="batch-input" value="SALE-LOT">
                <input type="hidden" name="items[0][thans_data]" class="thans-data-input" id="thans-data-0" value="">
              </td>
              <td>
                <input type="number" min="0" step="0.01" name="items[0][rate]" class="form-control input-rate" value="0" placeholder="0.00" oninput="calculateDcRow(0)" style="font-weight:700;">
              </td>
              <td>
                <input type="number" min="0.01" step="0.01" name="items[0][qty]" class="form-control input-qty" required value="" placeholder="0" oninput="calculateDcRow(0)" style="font-weight:800; color:#059669; font-size:0.95rem;">
              </td>
              <td>
                <div style="display:flex; align-items:center; gap:4px;">
                  <input type="number" min="0" step="1" name="items[0][than_count]" class="form-control input-than-count" id="than-count-0" value="0" placeholder="0" oninput="calculateDcTotals()" style="width:60px; font-weight:700;">
                  <button type="button" class="btn btn-secondary btn-sm" onclick="openThanModal(0)" title="Add multiple than meter breakdown" style="padding:4px 8px; font-size:0.75rem; font-weight:800; background:#eff6ff; color:#2563eb; border-color:#bfdbfe;">
                    + Than
                  </button>
                </div>
              </td>
              <td>
                <select name="items[0][tax_percent]" class="form-control input-tax-percent" onchange="calculateDcRow(0)" style="font-weight:600; font-size:0.85rem; padding:4px 6px;">
                  <option value="5" selected>5%</option>
                  <option value="12">12%</option>
                  <option value="18">18%</option>
                  <option value="0">0%</option>
                </select>
              </td>
              <td>
                <input type="number" readonly step="0.01" name="items[0][subtotal]" class="form-control input-subtotal" value="0.00" style="background:#f8fafc; font-weight:700; color:var(--slate-800);" tabindex="-1">
              </td>
              <td>
                <input type="number" readonly step="0.01" name="items[0][tax_amount]" class="form-control input-tax-amount" value="0.00" style="background:#f8fafc; font-weight:700; color:var(--slate-600);" tabindex="-1">
              </td>
              <td>
                <input type="number" readonly step="0.01" name="items[0][total_amount]" class="form-control input-total-amount" value="0.00" style="background:#f8fafc; font-weight:800; color:#059669;" tabindex="-1">
              </td>
              <td style="text-align:center;">
                <button type="button" class="btn btn-secondary btn-xs" onclick="removeDcRow(this)" title="Delete Row" style="color:#ef4444; padding:4px 6px;">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Live Summary & Remarks (Image 3) -->
      <div style="display:grid; grid-template-columns:1fr 380px; gap:20px; margin-top:20px; border-top:1px solid #e2e8f0; padding-top:16px;">
        <div>
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Remarks / Notes</label>
          <textarea name="remarks" class="form-control" rows="3" placeholder="Enter delivery terms, packaging instructions or notes..."></textarea>
          <div style="font-size:0.75rem; color:var(--slate-500); margin-top:8px;">
            💡 <strong>Tally Format Sales:</strong> Flexible entry supports both Raw Fabric and Finished Goods. Use "+ Than" popup for roll-wise meter entry.
          </div>
        </div>

        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:18px; display:flex; flex-direction:column; gap:8px; font-size:0.875rem;">
          <div style="display:flex; justify-content:space-between;">
            <span style="color:var(--slate-600);">Total Thans:</span>
            <strong id="summary-thans" style="color:var(--slate-900);">0 Thans</strong>
          </div>
          <div style="display:flex; justify-content:space-between;">
            <span style="color:var(--slate-600);">Total Quantity:</span>
            <strong id="summary-qty" style="color:var(--slate-900);">0.00 Mtr/Pcs</strong>
          </div>
          <div style="display:flex; justify-content:space-between;">
            <span style="color:var(--slate-600);">Taxable Subtotal:</span>
            <span id="summary-subtotal" style="font-weight:700; color:var(--slate-800);">₹0.00</span>
          </div>
          <div style="display:flex; justify-content:space-between;">
            <span style="color:var(--slate-600);">Total Tax:</span>
            <span id="summary-tax" style="font-weight:700; color:var(--slate-800);">₹0.00</span>
          </div>
          <div style="border-top:2px dashed #cbd5e1; padding-top:8px; display:flex; justify-content:space-between; font-size:1.15rem;">
            <span style="font-weight:800; color:var(--slate-900);">Grand Total:</span>
            <span style="font-weight:800; color:#059669;" id="summary-total">₹0.00</span>
          </div>
        </div>
      </div>

      <!-- Hidden inputs for backend form processing -->
      <input type="hidden" name="total_cartons" id="dc_total_cartons" value="1">
      <input type="hidden" name="total_qty" id="dc_total_qty" value="0">
      <input type="hidden" name="total_thans" id="dc_total_thans" value="0">
      <input type="hidden" name="subtotal_amount" id="dc_subtotal_amount" value="0">
      <input type="hidden" name="tax_amount" id="dc_tax_amount" value="0">
      <input type="hidden" name="total_amount" id="dc_total_amount" value="0">
    </div>

  </div>
</form>

<!-- Modal: Multiple Than Entry Popup (Image 3: * Add multiple than pop up) -->
<div id="multiple-than-modal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(15,23,42,0.65); backdrop-filter:blur(4px); z-index:99999; align-items:center; justify-content:center; padding:16px;">
  <div style="background:#fff; border-radius:16px; border:1px solid #cbd5e1; box-shadow:0 20px 25px -5px rgba(0,0,0,0.2); width:100%; max-width:480px; overflow:hidden;">
    <div style="display:flex; justify-content:space-between; align-items:center; padding:16px 20px; border-bottom:1px solid #e2e8f0; background:#f8fafc;">
      <div style="display:flex; align-items:center; gap:8px;">
        <span style="width:28px; height:28px; border-radius:8px; background:#eff6ff; color:#2563eb; display:inline-flex; align-items:center; justify-content:center; font-weight:800; font-size:0.85rem;">+</span>
        <h3 style="margin:0; font-size:1rem; font-weight:800; color:var(--slate-900);">Add Multiple Than Meter Readings</h3>
      </div>
      <button type="button" onclick="closeThanModal()" style="background:none; border:none; font-size:1.5rem; line-height:1; color:#94a3b8; cursor:pointer;">&times;</button>
    </div>

    <div style="padding:20px;">
      <p style="font-size:0.8rem; color:var(--slate-500); margin:0 0 10px;">
        Enter Than/Roll meters separated by commas, spaces, or newlines (e.g. <code>50.5, 48.0, 52, 60.5</code>):
      </p>

      <textarea id="than-modal-input" class="form-control" rows="5" placeholder="52.0&#10;48.5&#10;50.0&#10;55.2" oninput="updateThanModalStats()" style="font-family:monospace; font-weight:700; font-size:0.95rem;"></textarea>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:14px; background:#f1f5f9; padding:12px; border-radius:10px;">
        <div>
          <span style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">Than Count:</span>
          <div id="than-modal-count" style="font-size:1.25rem; font-weight:800; color:#1e293b;">0 Thans</div>
        </div>
        <div>
          <span style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">Total Meters:</span>
          <div id="than-modal-meters" style="font-size:1.25rem; font-weight:800; color:#059669;">0.00 Mtr</div>
        </div>
      </div>
    </div>

    <div style="display:flex; justify-content:flex-end; gap:10px; padding:14px 20px; border-top:1px solid #e2e8f0; background:#f8fafc;">
      <button type="button" class="btn btn-secondary" onclick="closeThanModal()">Cancel</button>
      <button type="button" class="btn btn-primary" onclick="applyThanModal()" style="font-weight:700;">Apply Than Readings</button>
    </div>
  </div>
</div>

@push('scripts')
<script>
  const ITEM_DATA = {!! json_encode($items) !!};
  let rowIndexCounter = 1;
  let activeThanRowIndex = null;

  function onCustomerSelect(select) {
    const opt = select.options[select.selectedIndex];
    const city = opt.getAttribute('data-city');
    const custId = opt.getAttribute('data-id');
    if (city) {
      document.getElementById('dc_city').value = city;
    }
    if (custId) {
      document.getElementById('dc_customer_id').value = custId;
    }
  }

  function onItemChange(select, idx) {
    const opt = select.options[select.selectedIndex];
    const name = opt.getAttribute('data-name') || '';
    const code = opt.getAttribute('data-code') || '';
    const unit = opt.getAttribute('data-unit') || 'Pcs';
    const category = opt.getAttribute('data-category') || 'Finished Goods';
    const cost = parseFloat(opt.getAttribute('data-cost') || 0);

    const row = select.closest('tr');
    if (!row) return;

    row.querySelector('.style-name-input').value = name;
    row.querySelector('.item-cat-input').value = category;
    row.querySelector('.unit-input').value = unit;
    row.querySelector('.batch-input').value = 'LOT-' + (code || 'SALES');

    const rateInput = row.querySelector('.input-rate');
    if (cost > 0 && (!rateInput.value || parseFloat(rateInput.value) === 0)) {
      rateInput.value = cost;
    }

    calculateDcRow(idx);
  }

  function calculateDcRow(idx) {
    const row = document.querySelector(`.dc-item-row[data-index="${idx}"]`);
    if (!row) return;

    const qty = parseFloat(row.querySelector('.input-qty')?.value) || 0;
    const rate = parseFloat(row.querySelector('.input-rate')?.value) || 0;
    const taxPercent = parseFloat(row.querySelector('.input-tax-percent')?.value) || 0;

    const subtotal = Math.round(qty * rate * 100) / 100;
    const taxAmount = Math.round((subtotal * taxPercent / 100.0) * 100) / 100;
    const total = Math.round((subtotal + taxAmount) * 100) / 100;

    const subInput = row.querySelector('.input-subtotal');
    const taxInput = row.querySelector('.input-tax-amount');
    const totInput = row.querySelector('.input-total-amount');

    if (subInput) subInput.value = subtotal.toFixed(2);
    if (taxInput) taxInput.value = taxAmount.toFixed(2);
    if (totInput) totInput.value = total.toFixed(2);

    calculateDcTotals();
  }

  function addNewDispatchRow() {
    const idx = rowIndexCounter++;
    const tbody = document.getElementById('dc-items-tbody');

    let itemOptions = '<option value="">-- Select Item (Item Master) --</option>';
    ITEM_DATA.forEach(itm => {
      itemOptions += `<option value="${itm.id}" data-name="${itm.name}" data-code="${itm.code}" data-category="${itm.category || 'Finished Goods'}" data-stock="${itm.current_stock}" data-unit="${itm.unit || 'Pcs'}" data-cost="${itm.unit_cost || 0}">${itm.name} (${itm.code}) [${itm.unit || 'Pcs'}]</option>`;
    });

    const tr = document.createElement('tr');
    tr.className = 'dc-item-row';
    tr.setAttribute('data-index', idx);
    tr.innerHTML = `
      <td style="text-align:center; font-weight:700; color:var(--slate-400);" class="row-num">${tbody.children.length + 1}</td>
      <td>
        <select name="items[${idx}][item_id]" class="form-control item-select" onchange="onItemChange(this, ${idx})" required style="font-size:0.85rem; font-weight:600;">
          ${itemOptions}
        </select>
        <input type="hidden" name="items[${idx}][style_name]" class="style-name-input" value="">
        <input type="hidden" name="items[${idx}][item_category]" class="item-cat-input" value="">
        <input type="hidden" name="items[${idx}][unit]" class="unit-input" value="Pcs">
        <input type="hidden" name="items[${idx}][batch_no]" class="batch-input" value="SALE-LOT">
        <input type="hidden" name="items[${idx}][thans_data]" class="thans-data-input" id="thans-data-${idx}" value="">
      </td>
      <td>
        <input type="number" min="0" step="0.01" name="items[${idx}][rate]" class="form-control input-rate" value="0" placeholder="0.00" oninput="calculateDcRow(${idx})" style="font-weight:700;">
      </td>
      <td>
        <input type="number" min="0.01" step="0.01" name="items[${idx}][qty]" class="form-control input-qty" required value="" placeholder="0" oninput="calculateDcRow(${idx})" style="font-weight:800; color:#059669; font-size:0.95rem;">
      </td>
      <td>
        <div style="display:flex; align-items:center; gap:4px;">
          <input type="number" min="0" step="1" name="items[${idx}][than_count]" class="form-control input-than-count" id="than-count-${idx}" value="0" placeholder="0" oninput="calculateDcTotals()" style="width:60px; font-weight:700;">
          <button type="button" class="btn btn-secondary btn-sm" onclick="openThanModal(${idx})" title="Add multiple than meter breakdown" style="padding:4px 8px; font-size:0.75rem; font-weight:800; background:#eff6ff; color:#2563eb; border-color:#bfdbfe;">
            + Than
          </button>
        </div>
      </td>
      <td>
        <select name="items[${idx}][tax_percent]" class="form-control input-tax-percent" onchange="calculateDcRow(${idx})" style="font-weight:600; font-size:0.85rem; padding:4px 6px;">
          <option value="5" selected>5%</option>
          <option value="12">12%</option>
          <option value="18">18%</option>
          <option value="0">0%</option>
        </select>
      </td>
      <td>
        <input type="number" readonly step="0.01" name="items[${idx}][subtotal]" class="form-control input-subtotal" value="0.00" style="background:#f8fafc; font-weight:700; color:var(--slate-800);" tabindex="-1">
      </td>
      <td>
        <input type="number" readonly step="0.01" name="items[${idx}][tax_amount]" class="form-control input-tax-amount" value="0.00" style="background:#f8fafc; font-weight:700; color:var(--slate-600);" tabindex="-1">
      </td>
      <td>
        <input type="number" readonly step="0.01" name="items[${idx}][total_amount]" class="form-control input-total-amount" value="0.00" style="background:#f8fafc; font-weight:800; color:#059669;" tabindex="-1">
      </td>
      <td style="text-align:center;">
        <button type="button" class="btn btn-secondary btn-xs" onclick="removeDcRow(this)" title="Delete Row" style="color:#ef4444; padding:4px 6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
        </button>
      </td>
    `;
    tbody.appendChild(tr);
    renumberRows();
    calculateDcTotals();
  }

  function removeDcRow(btn) {
    const rows = document.querySelectorAll('.dc-item-row');
    if (rows.length <= 1) {
      alert('At least one product line item is required.');
      return;
    }
    btn.closest('tr').remove();
    renumberRows();
    calculateDcTotals();
  }

  function renumberRows() {
    document.querySelectorAll('.dc-item-row').forEach((row, i) => {
      const numCell = row.querySelector('.row-num');
      if (numCell) numCell.innerText = i + 1;
    });
  }

  function calculateDcTotals() {
    let totalQty = 0;
    let totalThans = 0;
    let totalSubtotal = 0;
    let totalTax = 0;
    let grandTotal = 0;

    document.querySelectorAll('.dc-item-row').forEach(row => {
      const qty = parseFloat(row.querySelector('.input-qty')?.value) || 0;
      const thans = parseInt(row.querySelector('.input-than-count')?.value) || 0;
      const sub = parseFloat(row.querySelector('.input-subtotal')?.value) || 0;
      const tax = parseFloat(row.querySelector('.input-tax-amount')?.value) || 0;
      const tot = parseFloat(row.querySelector('.input-total-amount')?.value) || 0;

      totalQty += qty;
      totalThans += thans;
      totalSubtotal += sub;
      totalTax += tax;
      grandTotal += tot;
    });

    document.getElementById('summary-thans').innerText = totalThans + ' Thans';
    document.getElementById('summary-qty').innerText = totalQty.toFixed(2) + ' Mtr/Pcs';
    document.getElementById('summary-subtotal').innerText = '₹' + totalSubtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('summary-tax').innerText = '₹' + totalTax.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('summary-total').innerText = '₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    document.getElementById('dc_total_qty').value = totalQty;
    document.getElementById('dc_total_thans').value = totalThans;
    document.getElementById('dc_subtotal_amount').value = totalSubtotal.toFixed(2);
    document.getElementById('dc_tax_amount').value = totalTax.toFixed(2);
    document.getElementById('dc_total_amount').value = grandTotal.toFixed(2);
  }

  // --- Than Modal Logic ---
  function openThanModal(idx) {
    activeThanRowIndex = idx;
    const existing = document.getElementById(`thans-data-${idx}`)?.value || '';
    let valStr = '';
    try {
      const arr = JSON.parse(existing);
      if (Array.isArray(arr)) valStr = arr.join('\n');
    } catch(e) {
      valStr = existing;
    }
    document.getElementById('than-modal-input').value = valStr;
    updateThanModalStats();
    document.getElementById('multiple-than-modal').style.display = 'flex';
  }

  function closeThanModal() {
    document.getElementById('multiple-than-modal').style.display = 'none';
    activeThanRowIndex = null;
  }

  function parseThanInput(raw) {
    return raw.split(/[\n,; ]+/)
      .map(s => parseFloat(s.trim()))
      .filter(n => !isNaN(n) && n > 0);
  }

  function updateThanModalStats() {
    const raw = document.getElementById('than-modal-input').value;
    const values = parseThanInput(raw);
    const count = values.length;
    const sum = values.reduce((a, b) => a + b, 0);

    document.getElementById('than-modal-count').innerText = count + ' Thans';
    document.getElementById('than-modal-meters').innerText = sum.toFixed(2) + ' Mtr';
  }

  function applyThanModal() {
    if (activeThanRowIndex === null) return;
    const raw = document.getElementById('than-modal-input').value;
    const values = parseThanInput(raw);
    const count = values.length;
    const sum = values.reduce((a, b) => a + b, 0);

    const row = document.querySelector(`.dc-item-row[data-index="${activeThanRowIndex}"]`);
    if (row) {
      if (count > 0) {
        row.querySelector('.input-qty').value = sum.toFixed(2);
        row.querySelector('.input-than-count').value = count;
        document.getElementById(`thans-data-${activeThanRowIndex}`).value = JSON.stringify(values);
      }
      calculateDcRow(activeThanRowIndex);
    }
    closeThanModal();
  }

  function validateDispatchSubmit(e) {
    calculateDcTotals();
    const totalQty = parseFloat(document.getElementById('dc_total_qty').value) || 0;
    if (totalQty <= 0) {
      alert('Please specify a valid dispatch quantity greater than 0.');
      e.preventDefault();
      return false;
    }
    return true;
  }

  document.addEventListener('DOMContentLoaded', () => {
    const custSel = document.getElementById('dc_customer');
    if (custSel && custSel.value) {
      onCustomerSelect(custSel);
    }
    calculateDcRow(0);
    calculateDcTotals();
  });
</script>
@endpush
@endsection
