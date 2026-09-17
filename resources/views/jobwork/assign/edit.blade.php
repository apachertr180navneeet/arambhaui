@extends('layouts.app')

@section('title', 'Edit Job Work Order - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><a href="{{ route('jobwork.assign.index') }}" style="color:inherit; text-decoration:none;">Job Work & Assign</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Edit Order {{ $assign->job_order_no }}</span></div>
@endsection

@push('styles')
<style>
  .items-table th {
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

  .items-table td {
    padding: 8px 10px;
    vertical-align: middle;
    border-bottom: 1px solid var(--slate-100, #f1f5f9);
  }

  .items-table input.form-control,
  .items-table select.form-control {
    font-size: 0.85rem;
    padding: 6px 10px;
    height: 36px;
  }

  .calc-badge {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
  }

  .summary-card {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1px solid var(--slate-200, #e2e8f0);
    border-radius: 14px;
    padding: 20px;
  }
</style>
@endpush

@section('content')
<form action="{{ route('jobwork.assign.update', $assign->id) }}" method="POST" id="jw-form">
  @csrf
  @method('PUT')

  <div style="display:flex; flex-direction:column; gap:20px;">
    
    <!-- Top Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
      <div>
        <div style="display:flex; align-items:center; gap:8px;">
          <h2 style="margin:0; font-size:1.45rem; font-weight:800; color:var(--slate-900); letter-spacing:-0.02em;">Edit Job Work Order</h2>
          <span style="font-family:var(--font-mono, monospace); font-weight:800; font-size:0.95rem; background:#eef2ff; color:#4338ca; padding:3px 10px; border-radius:8px; border:1px solid #c7d2fe;">
            {{ $assign->job_order_no }}
          </span>
        </div>
        <p style="margin:4px 0 0; font-size:0.85rem; color:var(--slate-500);">Modify assigned contractor, process, lot number, items, than fabric lengths, piece rates, or order status.</p>
      </div>

      <div style="display:flex; gap:10px; align-items:center;">
        <a href="{{ route('jobwork.assign.index') }}" class="btn btn-secondary" style="font-weight:700;">Cancel</a>
        <button type="submit" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:6px; font-weight:700; box-shadow:0 2px 8px rgba(79, 70, 229, 0.3);">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
          Update Job Order
        </button>
      </div>
    </div>

    <!-- Step 1: Contractor & Manufacturing Process -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:24px;">
      <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin-top:0; margin-bottom:18px; color:var(--slate-800); border-bottom:1px solid var(--slate-100); padding-bottom:10px;">
        1. Contractor & Manufacturing Schedule
      </h3>

      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:18px;">
        
        <!-- Job Worker Select -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Job Worker Contractor <span style="color:#ef4444;">*</span></label>
          <select name="job_worker_name" id="ja_worker" class="form-control" required onchange="onWorkerSelect(this)">
            <option value="{{ $assign->job_worker_name }}" selected>{{ $assign->job_worker_name }}</option>
            @foreach($jobworkers as $jw)
              @if($jw->name !== $assign->job_worker_name)
                <option value="{{ $jw->name }}" data-id="{{ $jw->id }}" data-rate="{{ $jw->rate_per_piece }}" data-process="{{ $jw->skill_type }}">{{ $jw->name }} ({{ $jw->skill_type ?? 'Contractor' }})</option>
              @endif
            @endforeach
          </select>
          <input type="hidden" name="job_worker_id" id="ja_worker_id" value="{{ $assign->job_worker_id }}">
        </div>

        <!-- Process / Operation -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Process / Operation <span style="color:#ef4444;">*</span></label>
          <select name="process_name" id="ja_process" class="form-control" required>
            <option value="Cutting" {{ $assign->process_name === 'Cutting' ? 'selected' : '' }}>Fabric Cutting</option>
            <option value="Stitching" {{ $assign->process_name === 'Stitching' ? 'selected' : '' }}>Stitching</option>
            <option value="Embroidery" {{ $assign->process_name === 'Embroidery' ? 'selected' : '' }}>Embroidery</option>
            <option value="Washing & Finishing" {{ $assign->process_name === 'Washing & Finishing' ? 'selected' : '' }}>Washing & Finishing</option>
            <option value="Ironing & Packing" {{ $assign->process_name === 'Ironing & Packing' ? 'selected' : '' }}>Ironing & Packing</option>
          </select>
        </div>

        <!-- Lot Reference Number -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Lot Reference # <span style="color:#ef4444;">*</span></label>
          <input type="text" name="lot_number" class="form-control" required value="{{ $assign->lot_number }}" style="font-family:var(--font-mono, monospace); font-weight:800; color:var(--primary-800); background:#f8fafc;">
        </div>

        <!-- Issue Date -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Issue Date <span style="color:#ef4444;">*</span></label>
          <input type="date" name="issue_date" class="form-control" required value="{{ $assign->issue_date }}">
        </div>

        <!-- Target Due Date -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Target Due Date</label>
          <input type="date" name="due_date" class="form-control" value="{{ $assign->due_date }}">
        </div>

        <!-- Status -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Order Status</label>
          <select name="status" class="form-control" style="font-weight:700;">
            <option value="Issued" {{ $assign->status === 'Issued' ? 'selected' : '' }}>Issued</option>
            <option value="In Progress" {{ $assign->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
            <option value="Partial Ready" {{ $assign->status === 'Partial Ready' ? 'selected' : '' }}>Partial Ready</option>
            <option value="Completed" {{ $assign->status === 'Completed' ? 'selected' : '' }}>Completed</option>
            <option value="Cancelled" {{ $assign->status === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
          </select>
        </div>

      </div>
    </div>

    <!-- Step 2: Multi-Item Than Fabric, Production Pcs & Wastage Calculator -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:24px;">
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:16px; border-bottom:1px solid var(--slate-100); padding-bottom:10px;">
        <div>
          <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin:0; color:var(--slate-800);">
            2. Product Items, Than Fabric, Pcs & Wastage Calculator
          </h3>
          <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">
            Manage multi-item lines, fabric rolls, finished pieces yield and rates.
          </p>
        </div>

        <button type="button" class="btn btn-secondary btn-sm" onclick="addNewItemRow()" style="font-weight:700; display:inline-flex; align-items:center; gap:6px; background:#f0fdf4; border-color:#86efac; color:#166534;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          + Add Item Row
        </button>
      </div>

      <!-- Repeater Table -->
      <div class="table-responsive">
        <table class="data-table items-table" id="items-table" style="width:100%;">
          <thead>
            <tr>
              <th style="width:28%;">Item / Fabric Description <span style="color:red;">*</span></th>
              <th style="width:13%;">Than / Roll (Mtr)</th>
              <th style="width:13%;">Production (Pcs) <span style="color:red;">*</span></th>
              <th style="width:12%;">Wastage (Mtr)</th>
              <th style="width:14%;">Avg Cons. / Pc</th>
              <th style="width:12%;">Rate / Pc (₹) <span style="color:red;">*</span></th>
              <th style="width:14%; text-align:right;">Line Total (₹)</th>
              <th style="width:40px; text-align:center;"></th>
            </tr>
          </thead>
          <tbody id="items-tbody">
            @php
              $existingItems = $assign->items && count($assign->items) > 0 ? $assign->items : [null];
            @endphp
            @foreach($existingItems as $idx => $line)
              @php
                $itemName = $line ? ($line->item_name ?: $assign->style_name) : $assign->style_name;
                $than = $line ? (float)($line->than_meters ?: ($assign->total_than_meters ?: 100)) : 100;
                $pcs = $line ? (int)($line->production_pcs ?: ($line->qty ?: $assign->issued_qty)) : $assign->issued_qty;
                $wastage = $line ? (float)($line->wastage_meters ?: ($assign->total_wastage_meters ?: 2)) : 2;
                $rate = $line ? (float)($line->rate_per_piece ?: $assign->rate_per_piece) : $assign->rate_per_piece;
                $lineTotal = $pcs * $rate;
                $avgCons = $pcs > 0 ? max(0, ($than - $wastage) / $pcs) : 0;
              @endphp
              <tr class="item-row" data-index="{{ $idx }}">
                <td>
                  <div style="display:flex; flex-direction:column; gap:4px;">
                    <select name="items[{{ $idx }}][item_id]" class="form-control item-select" onchange="onItemDropdownChange(this, {{ $idx }})">
                      <option value="">-- Choose from Master / Custom --</option>
                      @foreach($items as $it)
                        <option value="{{ $it->id }}" {{ ($line && $line->item_id == $it->id) ? 'selected' : '' }} data-name="{{ $it->name }}">{{ $it->name }} ({{ $it->item_code ?? 'Item' }})</option>
                      @endforeach
                    </select>
                    <input type="text" name="items[{{ $idx }}][item_name]" class="form-control item-name-input" required value="{{ $itemName }}" placeholder="Style/Fabric name">
                  </div>
                </td>
                <td>
                  <div style="position:relative;">
                    <input type="number" step="0.01" min="0" name="items[{{ $idx }}][than_meters]" class="form-control input-than" value="{{ $than }}" oninput="calculateRow({{ $idx }})" style="font-weight:700;">
                    <span style="position:absolute; right:8px; top:50%; transform:translateY(-50%); font-size:0.7rem; color:var(--slate-400);">Mtr</span>
                  </div>
                </td>
                <td>
                  <div style="position:relative;">
                    <input type="number" step="1" min="1" name="items[{{ $idx }}][production_pcs]" class="form-control input-pcs" required value="{{ $pcs }}" oninput="calculateRow({{ $idx }})" style="font-weight:800; color:var(--primary-700);">
                    <span style="position:absolute; right:8px; top:50%; transform:translateY(-50%); font-size:0.7rem; color:var(--slate-400);">Pcs</span>
                  </div>
                </td>
                <td>
                  <div style="position:relative;">
                    <input type="number" step="0.01" min="0" name="items[{{ $idx }}][wastage_meters]" class="form-control input-wastage" value="{{ $wastage }}" oninput="calculateRow({{ $idx }})" style="font-weight:700; color:#dc2626;">
                    <span style="position:absolute; right:8px; top:50%; transform:translateY(-50%); font-size:0.7rem; color:var(--slate-400);">Mtr</span>
                  </div>
                </td>
                <td style="text-align:center;">
                  <span class="calc-badge row-avg-badge" id="badge-avg-{{ $idx }}">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span class="val">{{ number_format($avgCons, 2) }} Mtr/Pc</span>
                  </span>
                </td>
                <td>
                  <div style="position:relative;">
                    <span style="position:absolute; left:8px; top:50%; transform:translateY(-50%); font-size:0.75rem; font-weight:700; color:var(--slate-500);">₹</span>
                    <input type="number" step="0.5" min="0" name="items[{{ $idx }}][rate_per_piece]" class="form-control input-rate" required value="{{ $rate }}" oninput="calculateRow({{ $idx }})" style="padding-left:20px; font-weight:700;">
                  </div>
                </td>
                <td style="text-align:right;">
                  <div style="font-weight:800; font-size:0.95rem; color:#059669;" class="row-total" id="total-val-{{ $idx }}">
                    ₹{{ number_format($lineTotal, 2) }}
                  </div>
                </td>
                <td style="text-align:center;">
                  <button type="button" class="btn btn-secondary btn-xs" onclick="removeRow(this)" title="Delete Row" style="color:#ef4444; padding:4px 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                  </button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <!-- Grand Calculation Summary Card -->
      <div style="display:flex; justify-content:flex-end; margin-top:20px;">
        <div class="summary-card" style="width:420px; display:flex; flex-direction:column; gap:10px;">
          
          <div style="font-size:0.8rem; font-weight:800; color:var(--slate-700); text-transform:uppercase; letter-spacing:0.05em; border-bottom:1px solid #cbd5e1; padding-bottom:6px;">
            Production Yield & Contract Summary
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
            <span style="color:var(--slate-600);">Total Than Fabric Issued:</span>
            <strong id="grand-than" style="color:var(--slate-900);">0.00 Mtr</strong>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
            <span style="color:var(--slate-600);">Expected Wastage:</span>
            <strong id="grand-wastage" style="color:#dc2626;">0.00 Mtr</strong>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
            <span style="color:var(--slate-600);">Net Fabric Consumption:</span>
            <strong id="grand-net-fabric" style="color:#059669;">0.00 Mtr</strong>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
            <span style="color:var(--slate-600);">Total Finished Pieces:</span>
            <strong id="grand-pcs" style="color:var(--primary-700); font-size:0.95rem;">0 Pcs</strong>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
            <span style="color:var(--slate-600);">Overall Avg Consumption:</span>
            <strong id="grand-avg-cons" style="color:var(--slate-800);">0.00 Mtr / Pc</strong>
          </div>

          <div style="border-top:2px dashed #cbd5e1; padding-top:10px; display:flex; justify-content:space-between; align-items:baseline; font-size:1.15rem;">
            <span style="font-weight:800; color:var(--slate-900);">Contract Total Amount:</span>
            <span style="font-weight:800; color:#059669;" id="grand-amount">₹0.00</span>
          </div>

        </div>
      </div>

    </div>

    <!-- Step 3: Instructions -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:24px;">
      <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin-top:0; margin-bottom:12px; color:var(--slate-800);">
        3. Job Work Instructions & Quality Standards
      </h3>
      <div class="form-group" style="margin-bottom:0;">
        <textarea name="instructions" class="form-control" rows="3" placeholder="Specify cutting patterns, seam stitch density (SPI), packaging requirements...">{{ $assign->instructions }}</textarea>
      </div>
    </div>

  </div>
</form>

@push('scripts')
<script>
  let rowCount = {{ count($existingItems) }};
  const itemsMasterList = @json($items);

  function onWorkerSelect(select) {
    const opt = select.options[select.selectedIndex];
    const rate = opt.getAttribute('data-rate');
    const process = opt.getAttribute('data-process');
    const workerId = opt.getAttribute('data-id');

    if (workerId) {
      document.getElementById('ja_worker_id').value = workerId;
    }

    if (rate && parseFloat(rate) > 0) {
      document.querySelectorAll('.input-rate').forEach(inp => {
        if (!inp.value || parseFloat(inp.value) === 0) {
          inp.value = parseFloat(rate).toFixed(2);
        }
      });
      calculateAll();
    }

    if (process) {
      const procSelect = document.getElementById('ja_process');
      for (let i = 0; i < procSelect.options.length; i++) {
        if (procSelect.options[i].value.toLowerCase().includes(process.toLowerCase())) {
          procSelect.selectedIndex = i;
          break;
        }
      }
    }
  }

  function onItemDropdownChange(select, idx) {
    const opt = select.options[select.selectedIndex];
    const name = opt.getAttribute('data-name');
    const row = select.closest('tr');
    const nameInput = row.querySelector('.item-name-input');

    if (name && nameInput) {
      nameInput.value = name;
    }
  }

  function addNewItemRow() {
    const tbody = document.getElementById('items-tbody');
    const idx = rowCount++;

    let itemsOptions = '<option value="">-- Choose from Master / Custom --</option>';
    itemsMasterList.forEach(it => {
      itemsOptions += `<option value="${it.id}" data-name="${it.name}">${it.name} (${it.item_code || 'Item'})</option>`;
    });

    const tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.setAttribute('data-index', idx);
    tr.innerHTML = `
      <td>
        <div style="display:flex; flex-direction:column; gap:4px;">
          <select name="items[${idx}][item_id]" class="form-control item-select" onchange="onItemDropdownChange(this, ${idx})">
            ${itemsOptions}
          </select>
          <input type="text" name="items[${idx}][item_name]" class="form-control item-name-input" required value="Cotton Than Roll #${idx + 1}" placeholder="Style/Fabric name">
        </div>
      </td>
      <td>
        <div style="position:relative;">
          <input type="number" step="0.01" min="0" name="items[${idx}][than_meters]" class="form-control input-than" value="50" placeholder="50.00" oninput="calculateRow(${idx})" style="font-weight:700;">
          <span style="position:absolute; right:8px; top:50%; transform:translateY(-50%); font-size:0.7rem; color:var(--slate-400);">Mtr</span>
        </div>
      </td>
      <td>
        <div style="position:relative;">
          <input type="number" step="1" min="1" name="items[${idx}][production_pcs]" class="form-control input-pcs" required value="16" placeholder="16" oninput="calculateRow(${idx})" style="font-weight:800; color:var(--primary-700);">
          <span style="position:absolute; right:8px; top:50%; transform:translateY(-50%); font-size:0.7rem; color:var(--slate-400);">Pcs</span>
        </div>
      </td>
      <td>
        <div style="position:relative;">
          <input type="number" step="0.01" min="0" name="items[${idx}][wastage_meters]" class="form-control input-wastage" value="1" placeholder="1.00" oninput="calculateRow(${idx})" style="font-weight:700; color:#dc2626;">
          <span style="position:absolute; right:8px; top:50%; transform:translateY(-50%); font-size:0.7rem; color:var(--slate-400);">Mtr</span>
        </div>
      </td>
      <td style="text-align:center;">
        <span class="calc-badge row-avg-badge" id="badge-avg-${idx}">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <span class="val">3.06 Mtr/Pc</span>
        </span>
      </td>
      <td>
        <div style="position:relative;">
          <span style="position:absolute; left:8px; top:50%; transform:translateY(-50%); font-size:0.75rem; font-weight:700; color:var(--slate-500);">₹</span>
          <input type="number" step="0.5" min="0" name="items[${idx}][rate_per_piece]" class="form-control input-rate" required value="50.00" placeholder="50.00" oninput="calculateRow(${idx})" style="padding-left:20px; font-weight:700;">
        </div>
      </td>
      <td style="text-align:right;">
        <div style="font-weight:800; font-size:0.95rem; color:#059669;" class="row-total" id="total-val-${idx}">
          ₹800.00
        </div>
      </td>
      <td style="text-align:center;">
        <button type="button" class="btn btn-secondary btn-xs" onclick="removeRow(this)" title="Delete Row" style="color:#ef4444; padding:4px 6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
        </button>
      </td>
    `;

    tbody.appendChild(tr);
    calculateRow(idx);
  }

  function removeRow(btn) {
    const row = btn.closest('tr');
    const tbody = document.getElementById('items-tbody');
    if (tbody.querySelectorAll('tr').length > 1) {
      row.remove();
      calculateAll();
    } else {
      alert('At least one item is required in the Job Work Order.');
    }
  }

  function calculateRow(idx) {
    const row = document.querySelector(`tr[data-index="${idx}"]`);
    if (!row) return;

    const than = parseFloat(row.querySelector('.input-than')?.value) || 0;
    const pcs = parseFloat(row.querySelector('.input-pcs')?.value) || 0;
    const wastage = parseFloat(row.querySelector('.input-wastage')?.value) || 0;
    const rate = parseFloat(row.querySelector('.input-rate')?.value) || 0;

    let avg = 0;
    if (pcs > 0) {
      avg = Math.max(0, (than - wastage) / pcs);
    }

    const badge = document.getElementById(`badge-avg-${idx}`);
    if (badge) {
      badge.querySelector('.val').innerText = avg.toFixed(2) + ' Mtr/Pc';
    }

    const lineTotal = pcs * rate;
    const totalElem = document.getElementById(`total-val-${idx}`);
    if (totalElem) {
      totalElem.innerText = '₹' + lineTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    calculateAll();
  }

  function calculateAll() {
    let grandThan = 0;
    let grandWastage = 0;
    let grandPcs = 0;
    let grandAmount = 0;

    document.querySelectorAll('#items-tbody tr').forEach(row => {
      const than = parseFloat(row.querySelector('.input-than')?.value) || 0;
      const pcs = parseFloat(row.querySelector('.input-pcs')?.value) || 0;
      const wastage = parseFloat(row.querySelector('.input-wastage')?.value) || 0;
      const rate = parseFloat(row.querySelector('.input-rate')?.value) || 0;

      grandThan += than;
      grandWastage += wastage;
      grandPcs += pcs;
      grandAmount += (pcs * rate);
    });

    const netFabric = Math.max(0, grandThan - grandWastage);
    const overallAvg = grandPcs > 0 ? (netFabric / grandPcs) : 0;

    document.getElementById('grand-than').innerText = grandThan.toFixed(2) + ' Mtr';
    document.getElementById('grand-wastage').innerText = grandWastage.toFixed(2) + ' Mtr';
    document.getElementById('grand-net-fabric').innerText = netFabric.toFixed(2) + ' Mtr';
    document.getElementById('grand-pcs').innerText = Math.round(grandPcs) + ' Pcs';
    document.getElementById('grand-avg-cons').innerText = overallAvg.toFixed(2) + ' Mtr / Pc';
    document.getElementById('grand-amount').innerText = '₹' + grandAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  document.addEventListener('DOMContentLoaded', () => {
    calculateAll();
  });
</script>
@endpush
@endsection
