@extends('layouts.app')

@section('title', 'Generate Dispatch Challan - ' . ($companyName ?? 'GarmentERP'))

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

    <!-- Step 3: Product Items & Batch Number Selection -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:24px;">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:16px; border-bottom:1px solid var(--slate-100); padding-bottom:12px;">
        <div>
          <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin:0; color:var(--slate-800); display:flex; align-items:center; gap:8px;">
            <span>3. Product Items & Batch Number Stock Selection</span>
            <span class="badge badge-info" style="font-size:0.75rem;">Batch Stock Linked</span>
          </h3>
          <p style="margin:4px 0 0; font-size:0.8rem; color:var(--slate-500);">
            Select items and their corresponding batch numbers. Dispatch quantity will be deducted directly from the selected batch's inventory balance.
          </p>
        </div>

        <button type="button" class="btn btn-secondary btn-sm" onclick="addNewDispatchRow()" style="font-weight:700; display:inline-flex; align-items:center; gap:6px; background:#f0fdf4; border-color:#86efac; color:#166534;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          + Add Product / Batch Row
        </button>
      </div>

      <!-- Line Items Repeater Table -->
      <div class="table-responsive">
        <table class="data-table dc-items-table" id="dc-items-table" style="width:100%;">
          <thead>
            <tr>
              <th style="width:26%;">Product / Style Name <span style="color:red;">*</span></th>
              <th style="width:26%;">Batch Number <span style="color:red;">*</span></th>
              <th style="width:12%;">Size</th>
              <th style="width:12%;">Color</th>
              <th style="width:12%;">Dispatch Qty (Pcs) <span style="color:red;">*</span></th>
              <th style="width:10%;">Cartons</th>
              <th style="width:40px; text-align:center;"></th>
            </tr>
          </thead>
          <tbody id="dc-items-tbody">
            <!-- Row 0 Pre-created for convenience -->
            <tr class="dc-item-row" data-index="0">
              <td>
                <select name="items[0][item_id]" class="form-control item-select" onchange="onItemChange(this, 0)" required>
                  <option value="">-- Select Product / Item --</option>
                  @foreach($items as $itm)
                    <option value="{{ $itm->id }}" data-name="{{ $itm->name }}" data-code="{{ $itm->code }}" data-stock="{{ $itm->current_stock }}" data-unit="{{ $itm->unit ?? 'Pcs' }}">
                      {{ $itm->name }} ({{ $itm->code }}) — Stock: {{ number_format($itm->current_stock) }} {{ $itm->unit ?? 'Pcs' }}
                    </option>
                  @endforeach
                </select>
                <input type="hidden" name="items[0][style_name]" class="style-name-input" value="">
                <input type="hidden" name="items[0][unit]" class="unit-input" value="Pcs">
              </td>
              <td>
                <div style="display:flex; flex-direction:column; gap:4px;">
                  <div style="display:flex; gap:6px;">
                    <select class="form-control batch-select" onchange="onBatchSelect(this, 0)" style="flex:1;">
                      <option value="">-- Select Available Batch --</option>
                      @foreach($batches as $b)
                        <option value="{{ $b['batch_no'] }}" data-avail="{{ $b['available_stock'] }}" data-item-id="{{ $b['item_id'] ?? '' }}" data-item-name="{{ $b['item_name'] }}">
                          {{ $b['batch_no'] }} (Avail: {{ number_format($b['available_stock']) }} {{ $b['unit'] }})
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <input type="text" name="items[0][batch_no]" class="form-control batch-input" required placeholder="or enter Batch #" style="font-family:monospace; font-weight:700; text-transform:uppercase;" oninput="onCustomBatchInput(0)">
                  <div class="batch-stock-hint" id="stock-hint-0" style="font-size:0.72rem; color:var(--slate-500); font-weight:600;"></div>
                </div>
              </td>
              <td>
                <input type="text" name="items[0][size]" class="form-control" value="All Sizes" placeholder="e.g. M/L/XL">
              </td>
              <td>
                <input type="text" name="items[0][color]" class="form-control" value="Assorted" placeholder="e.g. Navy / Assorted">
              </td>
              <td>
                <input type="number" min="1" step="1" name="items[0][qty]" class="form-control input-qty" required value="" placeholder="0" oninput="calculateDcTotals()" style="font-weight:800; color:#059669; font-size:0.95rem;">
              </td>
              <td>
                <input type="number" min="0" step="1" name="items[0][cartons]" class="form-control input-cartons" value="1" placeholder="1" oninput="calculateDcTotals()" style="font-weight:700;">
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

      <!-- Live Summary Footer -->
      <div style="display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:16px; margin-top:20px; border-top:1px solid #e2e8f0; padding-top:16px;">
        <div>
          <span style="font-size:0.8rem; color:var(--slate-500);">
            💡 <strong>Stock Traceability:</strong> All dispatched line items will be recorded under the selected batch and deducted from the live inventory ledger.
          </span>
        </div>

        <div style="width:360px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:18px; display:flex; flex-direction:column; gap:10px; font-size:0.875rem;">
          <div style="display:flex; justify-content:space-between;">
            <span style="color:var(--slate-600);">Total Carton Boxes:</span>
            <strong id="summary-cartons" style="color:var(--slate-900);">1 Boxes</strong>
          </div>
          <div style="border-top:2px dashed #cbd5e1; padding-top:10px; display:flex; justify-content:space-between; font-size:1.15rem;">
            <span style="font-weight:800; color:var(--slate-900);">Total Dispatched Units:</span>
            <span style="font-weight:800; color:#059669;" id="summary-qty">0 pcs</span>
          </div>
        </div>
      </div>

      <!-- Hidden inputs for backward-compatible total fields -->
      <input type="hidden" name="total_cartons" id="dc_total_cartons" value="1">
      <input type="hidden" name="total_qty" id="dc_total_qty" value="0">
    </div>

  </div>
</form>

@push('scripts')
<script>
  const BATCH_DATA = {!! json_encode($batches) !!};
  const ITEM_DATA = {!! json_encode($items) !!};
  let rowIndexCounter = 1;

  function onCustomerSelect(select) {
    const opt = select.options[select.selectedIndex];
    const city = opt.getAttribute('data-city');
    if (city) {
      document.getElementById('dc_city').value = city;
    }
  }

  function onItemChange(select, idx) {
    const opt = select.options[select.selectedIndex];
    const name = opt.getAttribute('data-name') || '';
    const code = opt.getAttribute('data-code') || '';
    const unit = opt.getAttribute('data-unit') || 'Pcs';
    const itemId = opt.value;

    const row = select.closest('tr');
    if (!row) return;

    row.querySelector('.style-name-input').value = name;
    row.querySelector('.unit-input').value = unit;

    // Filter or highlight batches for this item
    const batchSelect = row.querySelector('.batch-select');
    const batchInput = row.querySelector('.batch-input');

    if (itemId) {
      // Find matching batch
      const matched = BATCH_DATA.find(b => b.item_id == itemId || b.batch_no === ('LOT-' + code));
      if (matched) {
        batchSelect.value = matched.batch_no;
        batchInput.value = matched.batch_no;
        updateStockHint(idx, matched.available_stock, unit);
      } else {
        batchInput.value = 'LOT-' + code;
        updateStockHint(idx, opt.getAttribute('data-stock') || 0, unit);
      }
    }
    calculateDcTotals();
  }

  function onBatchSelect(select, idx) {
    const opt = select.options[select.selectedIndex];
    const val = opt.value;
    const avail = parseFloat(opt.getAttribute('data-avail') || 0);
    const itemId = opt.getAttribute('data-item-id');
    const itemName = opt.getAttribute('data-item-name');

    const row = select.closest('tr');
    if (!row) return;

    const batchInput = row.querySelector('.batch-input');
    batchInput.value = val;

    if (val) {
      updateStockHint(idx, avail, 'Pcs');
      // If item is not selected, auto-select corresponding item
      const itemSelect = row.querySelector('.item-select');
      if (!itemSelect.value && itemId) {
        itemSelect.value = itemId;
        row.querySelector('.style-name-input').value = itemName || '';
      }
    } else {
      document.getElementById('stock-hint-' + idx).innerText = '';
    }
    calculateDcTotals();
  }

  function onCustomBatchInput(idx) {
    const row = document.querySelector(`.dc-item-row[data-index="${idx}"]`);
    if (!row) return;
    const inputVal = row.querySelector('.batch-input').value.toUpperCase();
    const matched = BATCH_DATA.find(b => b.batch_no.toUpperCase() === inputVal);
    if (matched) {
      row.querySelector('.batch-select').value = matched.batch_no;
      updateStockHint(idx, matched.available_stock, matched.unit);
    } else {
      document.getElementById('stock-hint-' + idx).innerHTML = '<span style="color:#64748b;">Custom Batch (Stock not linked)</span>';
    }
  }

  function updateStockHint(idx, avail, unit) {
    const hint = document.getElementById('stock-hint-' + idx);
    if (!hint) return;
    const availNum = parseFloat(avail) || 0;
    if (availNum > 0) {
      hint.innerHTML = `<span class="stock-badge">✓ Available: ${availNum.toLocaleString()} ${unit}</span>`;
    } else {
      hint.innerHTML = `<span class="stock-badge stock-warn">⚠ Batch Empty / 0 ${unit} Stock</span>`;
    }
  }

  function addNewDispatchRow() {
    const idx = rowIndexCounter++;
    const tbody = document.getElementById('dc-items-tbody');

    let itemOptions = '<option value="">-- Select Product / Item --</option>';
    ITEM_DATA.forEach(itm => {
      itemOptions += `<option value="${itm.id}" data-name="${itm.name}" data-code="${itm.code}" data-stock="${itm.current_stock}" data-unit="${itm.unit || 'Pcs'}">${itm.name} (${itm.code}) — Stock: ${Number(itm.current_stock).toLocaleString()} ${itm.unit || 'Pcs'}</option>`;
    });

    let batchOptions = '<option value="">-- Select Available Batch --</option>';
    BATCH_DATA.forEach(b => {
      batchOptions += `<option value="${b.batch_no}" data-avail="${b.available_stock}" data-item-id="${b.item_id || ''}" data-item-name="${b.item_name}">${b.batch_no} (Avail: ${Number(b.available_stock).toLocaleString()} ${b.unit || 'Pcs'})</option>`;
    });

    const tr = document.createElement('tr');
    tr.className = 'dc-item-row';
    tr.setAttribute('data-index', idx);
    tr.innerHTML = `
      <td>
        <select name="items[${idx}][item_id]" class="form-control item-select" onchange="onItemChange(this, ${idx})" required>
          ${itemOptions}
        </select>
        <input type="hidden" name="items[${idx}][style_name]" class="style-name-input" value="">
        <input type="hidden" name="items[${idx}][unit]" class="unit-input" value="Pcs">
      </td>
      <td>
        <div style="display:flex; flex-direction:column; gap:4px;">
          <select class="form-control batch-select" onchange="onBatchSelect(this, ${idx})">
            ${batchOptions}
          </select>
          <input type="text" name="items[${idx}][batch_no]" class="form-control batch-input" required placeholder="or enter Batch #" style="font-family:monospace; font-weight:700; text-transform:uppercase;" oninput="onCustomBatchInput(${idx})">
          <div class="batch-stock-hint" id="stock-hint-${idx}" style="font-size:0.72rem; color:var(--slate-500); font-weight:600;"></div>
        </div>
      </td>
      <td>
        <input type="text" name="items[${idx}][size]" class="form-control" value="All Sizes" placeholder="e.g. M/L/XL">
      </td>
      <td>
        <input type="text" name="items[${idx}][color]" class="form-control" value="Assorted" placeholder="e.g. Navy / Assorted">
      </td>
      <td>
        <input type="number" min="1" step="1" name="items[${idx}][qty]" class="form-control input-qty" required value="" placeholder="0" oninput="calculateDcTotals()" style="font-weight:800; color:#059669; font-size:0.95rem;">
      </td>
      <td>
        <input type="number" min="0" step="1" name="items[${idx}][cartons]" class="form-control input-cartons" value="1" placeholder="1" oninput="calculateDcTotals()" style="font-weight:700;">
      </td>
      <td style="text-align:center;">
        <button type="button" class="btn btn-secondary btn-xs" onclick="removeDcRow(this)" title="Delete Row" style="color:#ef4444; padding:4px 6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
        </button>
      </td>
    `;
    tbody.appendChild(tr);
    calculateDcTotals();
  }

  function removeDcRow(btn) {
    const rows = document.querySelectorAll('.dc-item-row');
    if (rows.length <= 1) {
      alert('At least one product line item is required for the delivery challan.');
      return;
    }
    btn.closest('tr').remove();
    calculateDcTotals();
  }

  function calculateDcTotals() {
    let totalQty = 0;
    let totalCartons = 0;

    document.querySelectorAll('.dc-item-row').forEach(row => {
      const qty = parseFloat(row.querySelector('.input-qty')?.value) || 0;
      const ctn = parseFloat(row.querySelector('.input-cartons')?.value) || 0;
      totalQty += qty;
      totalCartons += ctn;
    });

    document.getElementById('summary-cartons').innerText = totalCartons.toLocaleString('en-IN') + ' Boxes';
    document.getElementById('summary-qty').innerText = totalQty.toLocaleString('en-IN') + ' pcs';

    document.getElementById('dc_total_cartons').value = totalCartons;
    document.getElementById('dc_total_qty').value = totalQty;
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
    calculateDcTotals();
  });
</script>
@endpush
@endsection
