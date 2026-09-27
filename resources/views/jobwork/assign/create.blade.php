@extends('layouts.app')

@section('title', 'Issue Outward Job Work Order - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><a href="{{ route('jobwork.assign.index') }}" style="color:inherit; text-decoration:none;">Job Work & Assign</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Issue Outward Job Order</span></div>
@endsection

@push('styles')
<style>
  .raw-item-card {
    background: #ffffff;
    border-radius: var(--radius-xl, 16px);
    border: 1px solid var(--slate-200, #e2e8f0);
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    padding: 22px;
    transition: all 0.2s ease;
  }
  .raw-item-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.07);
  }
  .tons-container {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
  }
  .purchased-ton-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    padding: 5px 10px;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 700;
    color: #334155;
    cursor: pointer;
    transition: all 0.15s ease;
  }
  .purchased-ton-chip:hover {
    border-color: #4338ca;
    background: #eef2ff;
    color: #4338ca;
    transform: translateY(-1px);
  }
  .purchased-ton-chip.added {
    background: #ecfdf5;
    border-color: #a7f3d0;
    color: #065f46;
  }
  .calc-badge {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
  }
  .ton-row {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 6px 10px;
    transition: all 0.15s ease;
  }
  .ton-row:hover {
    border-color: #cbd5e1;
    background: #fafafa;
  }
</style>
@endpush

@section('content')
<form action="{{ route('jobwork.assign.store') }}" method="POST" id="jw-form">
  @csrf

  <div style="display:flex; flex-direction:column; gap:22px;">
    
    <!-- Top Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
      <div>
        <div style="display:flex; align-items:center; gap:10px;">
          <h2 style="margin:0; font-size:1.45rem; font-weight:800; color:var(--slate-900); letter-spacing:-0.02em;">
            Issue Outward Job Work Order
          </h2>
          <span style="font-family:var(--font-mono, monospace); font-weight:800; font-size:0.9rem; background:#eef2ff; color:#4338ca; padding:3px 10px; border-radius:8px; border:1px solid #c7d2fe;">
            {{ $nextJobOrderNo }}
          </span>
        </div>
        <p style="margin:4px 0 0; font-size:0.85rem; color:var(--slate-500);">
          Select Raw Material & Purchase Tons/Thans, assign Finished Items, and auto-calculate expected finished pieces and wastage.
        </p>
      </div>

      <div style="display:flex; gap:10px; align-items:center;">
        <a href="{{ route('jobwork.assign.index') }}" class="btn btn-secondary" style="font-weight:700;">Cancel</a>
        <button type="submit" id="save-jw-btn" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:8px; font-weight:700; box-shadow:0 2px 8px rgba(79, 70, 229, 0.3); padding:10px 22px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
          Save & Issue Job Order
        </button>
      </div>
    </div>

    <!-- 1. Contractor & Manufacturing Schedule -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl, 16px); border:1px solid var(--slate-200, #e2e8f0); box-shadow:var(--shadow-sm); padding:24px;">
      <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--slate-100, #f1f5f9); padding-bottom:12px; margin-bottom:18px;">
        <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin:0; color:var(--slate-800); display:flex; align-items:center; gap:8px;">
          <span style="width:24px; height:24px; border-radius:6px; background:#eef2ff; color:#4338ca; display:inline-flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:800;">1</span>
          Contractor & Manufacturing Schedule
        </h3>
        <span style="font-size:0.75rem; background:#ecfdf5; color:#059669; font-weight:700; padding:3px 10px; border-radius:9999px; border:1px solid #a7f3d0;">
          ⚡ Auto Lot Generator Active
        </span>
      </div>

      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:18px;">
        
        <!-- Job Worker Contractor -->
        <div class="form-group" style="margin-bottom:0;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800); margin:0;">
              Job Worker Contractor <span style="color:#ef4444;">*</span>
            </label>
            <a href="{{ route('masters.jobworkers.index') }}" target="_blank" style="font-size:0.75rem; color:#4338ca; text-decoration:none; font-weight:600;" title="Open Job Worker Master">+ Manage Workers</a>
          </div>
          <select name="job_worker_name" id="ja_worker" class="form-control" required onchange="onWorkerSelect(this)">
            <option value="">-- Select Job Worker --</option>
            @forelse($jobworkers as $jw)
              <option value="{{ $jw->name }}" data-id="{{ $jw->id }}" data-rate="{{ $jw->rate_per_piece }}" data-process="{{ $jw->skill_type }}">{{ $jw->name }} ({{ $jw->skill_type ?? 'Contractor' }})</option>
            @empty
              <option value="" disabled>No registered Job Workers found in Master</option>
            @endforelse
          </select>
          <input type="hidden" name="job_worker_id" id="ja_worker_id">
        </div>

        <!-- Process / Operation -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            Process / Operation <span style="color:#ef4444;">*</span>
          </label>
          <select name="process_name" id="ja_process" class="form-control" required>
            <option value="Cutting">Fabric Cutting</option>
            <option value="Stitching" selected>Stitching & Assembly</option>
            <option value="Embroidery">Embroidery & Design</option>
            <option value="Washing & Finishing">Washing & Finishing</option>
            <option value="Ironing & Packing">Ironing & Packing</option>
          </select>
        </div>

        <!-- Auto-Assigned Lot Reference Number -->
        <div class="form-group" style="margin-bottom:0;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800); margin:0;">
              Lot Reference # <span style="color:#ef4444;">*</span>
            </label>
            <button type="button" onclick="autoGenerateLotNo()" style="font-size:0.75rem; color:#4338ca; background:#eef2ff; border:1px solid #c7d2fe; border-radius:6px; padding:2px 8px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:4px; transition:all 0.2s ease;" title="Auto generate unique lot number">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
              Auto Generate
            </button>
          </div>
          <div style="position:relative; display:flex; align-items:center;">
            <input type="text" name="lot_number" id="ja_lot_number" class="form-control" required value="{{ old('lot_number', $nextLotNumber ?? '') }}" placeholder="e.g. LOT-2026-001" style="font-family:var(--font-mono, monospace); font-weight:800; color:#4338ca; background:#f8fafc; border-color:#c7d2fe; padding-right:75px;">
            <button type="button" onclick="autoGenerateLotNo()" class="btn btn-secondary btn-sm" style="position:absolute; right:4px; height:calc(100% - 8px); padding:0 10px; font-size:0.75rem; font-weight:700; display:inline-flex; align-items:center; gap:4px; border-radius:6px; background:#f8fafc; color:#334155; border:1px solid #cbd5e1;" title="Regenerate Lot Number">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
              Auto
            </button>
          </div>
          <small style="font-size:0.72rem; color:var(--slate-500); margin-top:4px; display:block;">Unique lot identifier tagged through all production & inward batches.</small>
        </div>

        <!-- Issue Date -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            Issue Date <span style="color:#ef4444;">*</span>
          </label>
          <input type="date" name="issue_date" class="form-control" required value="{{ date('Y-m-d') }}">
        </div>

        <!-- Target Due Date -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            Target Due Date
          </label>
          <input type="date" name="due_date" class="form-control" value="{{ date('Y-m-d', strtotime('+7 days')) }}">
        </div>

      </div>
    </div>

    <!-- 2. Raw Items, Purchase Tons & Finished Output Section -->
    <div style="display:flex; flex-direction:column; gap:16px;">
      
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div>
          <h3 style="font-size:1.05rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin:0; color:var(--slate-900); display:flex; align-items:center; gap:8px;">
            <span style="width:26px; height:26px; border-radius:6px; background:#ecfdf5; color:#059669; display:inline-flex; align-items:center; justify-content:center; font-size:0.8rem; font-weight:800;">2</span>
            Raw Materials, Purchase Tons & Finished Product Yield
          </h3>
          <p style="margin:3px 0 0; font-size:0.825rem; color:var(--slate-500);">
            Select Raw Material & choose from purchased tons/rolls, pick the target Finished Item, and view automated wastage & finished pieces calculation.
          </p>
        </div>

        <button type="button" class="btn btn-primary btn-sm" onclick="addNewRawItemCard()" style="display:inline-flex; align-items:center; gap:6px; font-weight:700; box-shadow:0 2px 6px rgba(79, 70, 229, 0.25);">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          + Add Another Raw Material
        </button>
      </div>

      <!-- Dynamic Raw Items Container -->
      <div id="raw-items-container" style="display:flex; flex-direction:column; gap:22px;"></div>

      <!-- Add Raw Item Button -->
      <div style="text-align:center; padding:8px 0;">
        <button type="button" class="btn btn-secondary" onclick="addNewRawItemCard()" style="border:2px dashed #cbd5e1; background:#f8fafc; color:#334155; font-weight:700; width:100%; padding:14px; display:inline-flex; align-items:center; justify-content:center; gap:8px; border-radius:12px; transition:all 0.2s ease;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Add Another Raw Item (Multi-Material Job Order)
        </button>
      </div>

    </div>

    <!-- 3. Instructions & Overall Order Summary -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl, 16px); border:1px solid var(--slate-200, #e2e8f0); box-shadow:var(--shadow-sm); padding:24px;">
      <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin-top:0; margin-bottom:18px; color:var(--slate-800); border-bottom:1px solid var(--slate-100, #f1f5f9); padding-bottom:10px; display:flex; align-items:center; gap:8px;">
        <span style="width:24px; height:24px; border-radius:6px; background:#f5f3ff; color:#7c3aed; display:inline-flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:800;">3</span>
        Production Yield & Contract Terms Summary
      </h3>

      <div style="display:grid; grid-template-columns: 1.2fr 1fr; gap:24px;">
        
        <!-- Instructions -->
        <div>
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            Job Work Instructions & Quality Standards
          </label>
          <textarea name="instructions" class="form-control" rows="5" placeholder="Specify cutting patterns, seam stitch density (SPI), thread color matching, packaging details and return delivery deadline..."></textarea>
        </div>

        <!-- Calculated Summary Box -->
        <div style="background:#f8fafc; border:1px solid var(--slate-200, #e2e8f0); border-radius:14px; padding:20px; display:flex; flex-direction:column; gap:12px;">
          <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#64748b;">
            <span>Total Raw Materials:</span>
            <span style="font-weight:700; color:#0f172a;" id="summary-items-count">1 Item</span>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#64748b;">
            <span>Total Purchase Tons/Thans:</span>
            <span style="font-weight:700; color:#0f172a;" id="summary-than-count">0 Tons</span>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#64748b;">
            <span>Total Raw Quantity Issued:</span>
            <span style="font-weight:700; color:#0f172a; font-size:0.95rem;" id="summary-total-raw-qty">0.00 Mtr/KG</span>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#64748b;">
            <span>Total Expected Wastage:</span>
            <span style="font-weight:700; color:#dc2626;" id="summary-total-wastage">0.00 Mtr/KG</span>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#64748b;">
            <span>Net Raw Material Used:</span>
            <span style="font-weight:700; color:#059669;" id="summary-net-raw">0.00 Mtr/KG</span>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#64748b; border-top:1px dashed #cbd5e1; padding-top:10px;">
            <span>Total Expected Finished Pieces:</span>
            <span style="font-weight:800; color:#4338ca; font-size:1.15rem;" id="summary-total-pcs">0 Pcs</span>
          </div>

          <div style="border-top:2px solid #cbd5e1; padding-top:12px; margin-top:4px; display:flex; justify-content:space-between; align-items:baseline;">
            <span style="font-weight:800; font-size:1.05rem; color:var(--slate-900);">Contract Labor Total Amount:</span>
            <span style="font-weight:800; font-size:1.4rem; color:#059669;" id="summary-grand-amount">₹0.00</span>
          </div>
        </div>

      </div>

    </div>


  </div>
</form>

@endsection

@push('scripts')
<script>
  const masterItems = @json($items);
  const purchasedTonsMap = @json($purchasedTonsByItem);

  // Multi-Item State
  let itemsData = [
    {
      raw_item_id: '',
      raw_item_name: '',
      finished_item_id: '',
      finished_item_name: '',
      consumption_per_pc: 1.50,
      wastage_meters: 0,
      rate_per_piece: 25.00,
      expected_pieces: 0,
      thans: [] // Array of tons/thans (e.g. [500, 750, 600])
    }
  ];

  function autoGenerateLotNo() {
    const input = document.getElementById('ja_lot_number');
    if (!input) return;

    const year = new Date().getFullYear();
    const rand = Math.floor(100 + Math.random() * 900);
    input.value = `LOT-${year}-${rand}`;

    input.style.transition = 'all 0.25s ease';
    input.style.borderColor = '#4338ca';
    input.style.backgroundColor = '#eef2ff';
    input.style.boxShadow = '0 0 0 3px rgba(79, 70, 229, 0.2)';
    setTimeout(() => {
      input.style.borderColor = '';
      input.style.backgroundColor = '';
      input.style.boxShadow = '';
    }, 500);
  }

  function onWorkerSelect(select) {
    const opt = select.options[select.selectedIndex];
    const rate = opt ? opt.getAttribute('data-rate') : null;
    const process = opt ? opt.getAttribute('data-process') : null;
    const workerId = opt ? opt.getAttribute('data-id') : null;

    if (workerId) {
      document.getElementById('ja_worker_id').value = workerId;
    }

    if (rate && parseFloat(rate) > 0) {
      itemsData.forEach((item) => {
        if (!item.rate_per_piece || parseFloat(item.rate_per_piece) === 0 || item.rate_per_piece === 25.00) {
          item.rate_per_piece = parseFloat(rate);
        }
      });
      renderAllRawItemCards();
      calculateOverallTotals();
    }

    if (process) {
      const procSelect = document.getElementById('ja_process');
      if (procSelect) {
        for (let i = 0; i < procSelect.options.length; i++) {
          if (procSelect.options[i].value.toLowerCase().includes(process.toLowerCase())) {
            procSelect.selectedIndex = i;
            break;
          }
        }
      }
    }
  }

  function addNewRawItemCard() {
    const workerSelect = document.getElementById('ja_worker');
    let defaultRate = 25.00;
    if (workerSelect && workerSelect.selectedIndex > 0) {
      const opt = workerSelect.options[workerSelect.selectedIndex];
      const r = parseFloat(opt.getAttribute('data-rate'));
      if (r > 0) defaultRate = r;
    }

    itemsData.push({
      raw_item_id: '',
      raw_item_name: '',
      finished_item_id: '',
      finished_item_name: '',
      consumption_per_pc: 1.50,
      wastage_meters: 0,
      rate_per_piece: defaultRate,
      expected_pieces: 0,
      thans: []
    });

    renderAllRawItemCards();
    calculateOverallTotals();
  }

  function removeRawItemCard(idx) {
    if (itemsData.length <= 1) {
      alert('A job work order must contain at least one raw item.');
      return;
    }
    const item = itemsData[idx];
    if (item.thans.length > 0) {
      if (!confirm(`Remove Raw Item #${idx + 1} (${item.raw_item_name || 'Item'}) and its ${item.thans.length} tons/thans?`)) {
        return;
      }
    }
    itemsData.splice(idx, 1);
    renderAllRawItemCards();
    calculateOverallTotals();
  }

  // 1. Raw Item Selection Change
  function onRawItemChange(idx, select) {
    const opt = select.options[select.selectedIndex];
    if (!opt || !opt.value) {
      itemsData[idx].raw_item_id = '';
      itemsData[idx].raw_item_name = '';
    } else {
      itemsData[idx].raw_item_id = opt.getAttribute('data-id') || '';
      itemsData[idx].raw_item_name = opt.value;
    }

    renderAllRawItemCards();
    calculateOverallTotals();
  }

  // 2. Finished Item Selection Change
  function onFinishedItemChange(idx, select) {
    const opt = select.options[select.selectedIndex];
    if (!opt || !opt.value) {
      itemsData[idx].finished_item_id = '';
      itemsData[idx].finished_item_name = '';
    } else {
      itemsData[idx].finished_item_id = opt.getAttribute('data-id') || '';
      itemsData[idx].finished_item_name = opt.value;
      const rawMtr = parseFloat(opt.getAttribute('data-raw-meter'));
      if (rawMtr && rawMtr > 0) {
        itemsData[idx].consumption_per_pc = rawMtr;
      }
    }

    recalcRawItemRow(idx);
    calculateOverallTotals();
  }

  // Add a purchased ton from the available list
  function addPurchasedTon(itemIdx, meterVal) {
    const val = parseFloat(meterVal);
    if (isNaN(val) || val <= 0) return;
    itemsData[itemIdx].thans.push(Math.round(val * 100) / 100);
    renderTonsForCard(itemIdx);
    recalcRawItemRow(itemIdx);
    calculateOverallTotals();
  }

  // Add all available purchased tons for this raw item
  function addAllPurchasedTons(itemIdx) {
    const rawId = itemsData[itemIdx].raw_item_id;
    const rawName = (itemsData[itemIdx].raw_item_name || '').toLowerCase().trim();
    const available = (rawId && purchasedTonsMap[rawId]) ? purchasedTonsMap[rawId] : (rawName && purchasedTonsMap[rawName] ? purchasedTonsMap[rawName] : []);
    
    if (available.length === 0) return;

    available.forEach(t => {
      itemsData[itemIdx].thans.push(Math.round(t.meter * 100) / 100);
    });

    renderTonsForCard(itemIdx);
    recalcRawItemRow(itemIdx);
    calculateOverallTotals();
  }

  function removeTon(itemIdx, tonIdx) {
    itemsData[itemIdx].thans.splice(tonIdx, 1);
    renderTonsForCard(itemIdx);
    recalcRawItemRow(itemIdx);
    calculateOverallTotals();
  }

  function updateTonValue(itemIdx, tonIdx, newMeter) {
    const val = parseFloat(newMeter);
    if (!isNaN(val) && val >= 0) {
      itemsData[itemIdx].thans[tonIdx] = Math.round(val * 100) / 100;
      recalcRawItemRow(itemIdx);
      calculateOverallTotals();
    }
  }

  function clearAllTons(itemIdx) {
    if (itemsData[itemIdx].thans.length === 0) return;
    if (confirm(`Clear all selected tons for Raw Item #${itemIdx + 1}?`)) {
      itemsData[itemIdx].thans = [];
      renderTonsForCard(itemIdx);
      recalcRawItemRow(itemIdx);
      calculateOverallTotals();
    }
  }

  function onConsumptionChange(itemIdx, val) {
    const c = parseFloat(val);
    itemsData[itemIdx].consumption_per_pc = !isNaN(c) && c > 0 ? c : 1.5;
    recalcRawItemRow(itemIdx, 'from_consumption');
    calculateOverallTotals();
  }

  function onWastageChange(itemIdx, val) {
    const w = parseFloat(val);
    itemsData[itemIdx].wastage_meters = !isNaN(w) && w >= 0 ? w : 0;
    recalcRawItemRow(itemIdx, 'from_wastage');
    calculateOverallTotals();
  }

  function onExpectedPiecesChange(itemIdx, val) {
    const p = parseInt(val);
    itemsData[itemIdx].expected_pieces = !isNaN(p) && p >= 0 ? p : 0;
    
    // If pieces manually changed, recompute consumption ratio
    const totalRaw = itemsData[itemIdx].thans.reduce((a, b) => a + (parseFloat(b) || 0), 0);
    const netRaw = Math.max(0, totalRaw - itemsData[itemIdx].wastage_meters);
    if (p > 0) {
      itemsData[itemIdx].consumption_per_pc = Math.round((netRaw / p) * 1000) / 1000;
      const consInput = document.getElementById(`cons-input-${itemIdx}`);
      if (consInput) consInput.value = itemsData[itemIdx].consumption_per_pc;
    }

    recalcRawItemRow(itemIdx, 'from_pieces');
    calculateOverallTotals();
  }

  function onRateChange(itemIdx, val) {
    const r = parseFloat(val);
    itemsData[itemIdx].rate_per_piece = !isNaN(r) && r >= 0 ? r : 0;
    recalcRawItemRow(itemIdx, 'from_rate');
    calculateOverallTotals();
  }

  // Main Yield and Quantity Recalculation for Card
  function recalcRawItemRow(itemIdx, trigger = 'general') {
    const item = itemsData[itemIdx];
    if (!item) return;

    const totalRaw = item.thans.reduce((a, b) => a + (parseFloat(b) || 0), 0);
    const wastage = parseFloat(item.wastage_meters) || 0;
    const netRaw = Math.max(0, totalRaw - wastage);
    const cons = parseFloat(item.consumption_per_pc) || 1.5;

    // Auto-calculate expected pieces if not manually forced
    if (trigger !== 'from_pieces') {
      if (cons > 0 && netRaw > 0) {
        item.expected_pieces = Math.floor(netRaw / cons);
      } else if (netRaw === 0) {
        item.expected_pieces = 0;
      }
    }

    const pcs = item.expected_pieces || 0;
    const rate = parseFloat(item.rate_per_piece) || 0;
    const lineTotal = Math.round(pcs * rate * 100) / 100;

    // Update form input values
    const pcsInput = document.getElementById(`pieces-input-${itemIdx}`);
    if (pcsInput && trigger !== 'from_pieces') {
      pcsInput.value = pcs;
    }

    // Update Display Badges
    const bRawQty = document.getElementById(`card-badge-raw-qty-${itemIdx}`);
    const bTonsCount = document.getElementById(`card-badge-tons-count-${itemIdx}`);
    const bPcs = document.getElementById(`card-badge-pcs-${itemIdx}`);
    const bNet = document.getElementById(`card-badge-net-${itemIdx}`);
    const bTotal = document.getElementById(`card-badge-total-${itemIdx}`);
    const titleDisp = document.getElementById(`card-title-display-${itemIdx}`);

    if (bRawQty) bRawQty.textContent = `${totalRaw.toFixed(2)} Mtr/KG`;
    if (bTonsCount) bTonsCount.textContent = `${item.thans.length} Tons/Thans`;
    if (bPcs) bPcs.textContent = `${pcs} Pcs`;
    if (bNet) bNet.textContent = `${netRaw.toFixed(2)} Net Mtr/KG`;
    if (bTotal) bTotal.textContent = `₹${lineTotal.toFixed(2)}`;
    if (titleDisp) {
      titleDisp.textContent = item.raw_item_name || `Raw Material #${itemIdx + 1}`;
    }

    const footerRawTotal = document.getElementById(`tons-total-display-${itemIdx}`);
    if (footerRawTotal) {
      footerRawTotal.textContent = `${totalRaw.toFixed(2)} Mtr/KG (${item.thans.length} Tons)`;
    }
  }

  function renderAllRawItemCards() {
    const container = document.getElementById('raw-items-container');
    if (!container) return;

    container.innerHTML = '';

    itemsData.forEach((item, itemIdx) => {
      const card = document.createElement('div');
      card.className = 'card raw-item-card';
      card.id = `raw-card-${itemIdx}`;

      // Options for Raw Items
      let rawOptions = `<option value="">-- Select Raw Item / Fabric --</option>`;
      masterItems.forEach(itm => {
        const isSel = (item.raw_item_name && (itm.name === item.raw_item_name || itm.id == item.raw_item_id)) ? 'selected' : '';
        rawOptions += `<option value="${itm.name}" data-id="${itm.id}" data-code="${itm.code || ''}" ${isSel}>${itm.name} [${itm.code || 'ITEM'}] (${itm.category || 'Raw Material'})</option>`;
      });

      // Options for Finished Items
      let finishedOptions = `<option value="">-- Select Target Finished Product --</option>`;
      masterItems.forEach(itm => {
        const isSel = (item.finished_item_name && (itm.name === item.finished_item_name || itm.id == item.finished_item_id)) ? 'selected' : '';
        const rawMtr = itm.raw_meter_per_piece || 0;
        finishedOptions += `<option value="${itm.name}" data-id="${itm.id}" data-code="${itm.code || ''}" data-raw-meter="${rawMtr}" ${isSel}>${itm.name} [${itm.code || 'STYLE'}] ${rawMtr > 0 ? `(${rawMtr} Mtr/Pc)` : ''}</option>`;
      });

      // Available Purchase Tons for this raw item
      const rawId = item.raw_item_id;
      const rawName = (item.raw_item_name || '').toLowerCase().trim();
      const availablePurchases = (rawId && purchasedTonsMap[rawId]) ? purchasedTonsMap[rawId] : (rawName && purchasedTonsMap[rawName] ? purchasedTonsMap[rawName] : []);

      let purchasedTonsChipsHtml = '';
      if (!item.raw_item_name) {
        purchasedTonsChipsHtml = `
          <div style="margin-top:10px; background:#f8fafc; border:1px dashed #cbd5e1; border-radius:10px; padding:12px; text-align:center; color:#64748b; font-size:0.8rem;">
            Please select a <strong>Raw Item</strong> above to load available Purchase Tons from stock.
          </div>
        `;
      } else if (availablePurchases.length > 0) {
        purchasedTonsChipsHtml = `
          <div style="margin-top:10px; background:#ffffff; border:1px solid #cbd5e1; border-radius:10px; padding:12px 14px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; flex-wrap:wrap; gap:6px;">
              <span style="font-size:0.75rem; font-weight:800; color:#1e293b; text-transform:uppercase; display:inline-flex; align-items:center; gap:5px;">
                📦 Available Purchase Tons in Stock (${availablePurchases.length} available):
              </span>
              <button type="button" class="btn btn-secondary btn-xs" onclick="addAllPurchasedTons(${itemIdx})" style="font-size:0.725rem; font-weight:700;">+ Select All Available (${availablePurchases.length})</button>
            </div>
            <div style="display:flex; flex-wrap:wrap; gap:8px;">
              ${availablePurchases.map(p => `
                <button type="button" class="purchased-ton-chip" onclick="addPurchasedTon(${itemIdx}, ${p.meter})" title="Click to select ${p.meter} ${p.unit} from Challan #${p.challan_no}">
                  <span>+ ${p.label}</span>
                </button>
              `).join('')}
            </div>
          </div>
        `;
      } else {
        purchasedTonsChipsHtml = `
          <div style="margin-top:10px; background:#fffbeb; border:1px solid #fde68a; border-radius:10px; padding:10px 14px; color:#92400e; font-size:0.8rem;">
            No purchase tons in stock found for <strong>${item.raw_item_name}</strong>.
          </div>
        `;
      }

      const totalRaw = item.thans.reduce((a, b) => a + (parseFloat(b) || 0), 0);
      const wastage = parseFloat(item.wastage_meters) || 0;
      const netRaw = Math.max(0, totalRaw - wastage);
      const pcs = item.expected_pieces || 0;
      const rate = parseFloat(item.rate_per_piece) || 0;
      const lineTotal = Math.round(pcs * rate * 100) / 100;

      card.innerHTML = `
        <!-- Card Header -->
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--slate-100, #f1f5f9); padding-bottom:12px; margin-bottom:16px; flex-wrap:wrap; gap:10px;">
          <div style="display:flex; align-items:center; gap:10px;">
            <span style="width:28px; height:28px; border-radius:8px; background:#eef2ff; color:#4338ca; display:inline-flex; align-items:center; justify-content:center; font-size:0.85rem; font-weight:800;">
              #${itemIdx + 1}
            </span>
            <div>
              <div style="font-weight:800; font-size:1.05rem; color:var(--slate-900);" id="card-title-display-${itemIdx}">
                ${item.raw_item_name || 'Raw Material #' + (itemIdx + 1)}
              </div>
              <div style="font-size:0.75rem; color:var(--slate-500);">
                Output: <strong style="color:#4338ca;">${item.finished_item_name || 'Select Finished Product'}</strong>
              </div>
            </div>
          </div>

          <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <span class="badge" style="background:#f1f5f9; color:#334155; border:1px solid #cbd5e1; font-weight:700; font-size:0.8rem;" id="card-badge-tons-count-${itemIdx}">
              ${item.thans.length} Tons/Thans
            </span>
            <span class="badge" style="background:#eef2ff; color:#4338ca; border:1px solid #c7d2fe; font-weight:800; font-size:0.85rem;" id="card-badge-raw-qty-${itemIdx}">
              ${totalRaw.toFixed(2)} Mtr/KG
            </span>
            <span class="badge" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; font-weight:800; font-size:0.85rem;" id="card-badge-pcs-${itemIdx}">
              ${pcs} Pcs
            </span>
            <span class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-weight:800; font-size:0.85rem;" id="card-badge-total-${itemIdx}">
              ₹${lineTotal.toFixed(2)}
            </span>

            ${itemsData.length > 1 ? `
              <button type="button" class="btn btn-secondary btn-sm" onclick="removeRawItemCard(${itemIdx})" style="color:#ef4444; font-weight:700; padding:4px 8px; border-color:#fecaca;" title="Remove this entire raw material block">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                Remove
              </button>
            ` : ''}
          </div>
        </div>

        <input type="hidden" name="items[${itemIdx}][raw_item_id]" value="${item.raw_item_id}">
        <input type="hidden" name="items[${itemIdx}][finished_item_id]" value="${item.finished_item_id}">

        <!-- Row 1: Raw Item Select & Finished Item Select -->
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:14px;">
          
          <!-- Step 1: Select Raw Item -->
          <div class="form-group" style="margin-bottom:0;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800); margin:0;">
                1. Select Raw Item <span style="color:#ef4444;">*</span>
              </label>
              <a href="{{ route('masters.items.index') }}" target="_blank" style="font-size:0.75rem; color:#4338ca; text-decoration:none; font-weight:600;">+ Items Master</a>
            </div>
            <select name="items[${itemIdx}][raw_item_name]" class="form-control" required onchange="onRawItemChange(${itemIdx}, this)">
              ${rawOptions}
            </select>
          </div>

          <!-- Step 2: Select Finished Item -->
          <div class="form-group" style="margin-bottom:0;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800); margin:0;">
                2. Select Target Finished Item <span style="color:#ef4444;">*</span>
              </label>
              <span style="font-size:0.725rem; color:var(--slate-500);">Auto loads consumption ratio</span>
            </div>
            <select name="items[${itemIdx}][finished_item_name]" class="form-control" required onchange="onFinishedItemChange(${itemIdx}, this)">
              ${finishedOptions}
            </select>
          </div>

        </div>

        <!-- Section A: Multiple Purchase Tons / Thans Container -->
        <div class="tons-container" style="margin-top:14px;">
          
          <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:10px;">
            <div style="font-weight:800; font-size:0.85rem; color:#334155; text-transform:uppercase; letter-spacing:0.04em; display:flex; align-items:center; gap:6px;">
              <span>3. Select Purchase Tons for ${item.raw_item_name || 'Raw Material'}</span>
              <span class="calc-badge" style="font-size:0.75rem;">${item.thans.length} Selected</span>
            </div>
            
            ${item.thans.length > 0 ? `
              <button type="button" class="btn btn-secondary btn-xs" onclick="clearAllTons(${itemIdx})" style="font-weight:700; font-size:0.75rem; color:#dc2626;">
                Clear All Tons
              </button>
            ` : ''}
          </div>

          <!-- Available Purchases Tons Chip List -->
          ${purchasedTonsChipsHtml}

          <!-- Empty State Box -->
          <div id="tons-empty-box-${itemIdx}" style="text-align:center; padding:18px 14px; color:#64748b; border:2px dashed #cbd5e1; border-radius:10px; background:#ffffff; margin-top:10px; ${item.thans.length > 0 ? 'display:none;' : 'display:block;'}">
            <div style="font-weight:700; font-size:0.85rem; color:#475569; margin-bottom:2px;">No Purchase Tons selected yet</div>
            <p style="font-size:0.75rem; color:#94a3b8; margin:0;">Click on the available purchase tons in stock above to select tons for this order.</p>
          </div>

          <!-- Selected Tons Grid List -->
          <div id="tons-list-box-${itemIdx}" style="${item.thans.length > 0 ? 'display:grid;' : 'display:none;'} grid-template-columns:repeat(auto-fill, minmax(200px, 1fr)); gap:10px; margin-top:12px; margin-bottom:12px;"></div>

          <!-- Total Raw Quantity Footer under this Raw Item -->
          <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px dashed #cbd5e1; padding-top:10px; margin-top:8px; font-size:0.875rem;">
            <span style="font-weight:700; color:#475569;">Total Raw Quantity (${item.raw_item_name || 'Raw Item'}):</span>
            <strong id="tons-total-display-${itemIdx}" style="color:#0f172a; font-size:1.05rem;">
              ${totalRaw.toFixed(2)} Mtr/KG (${item.thans.length} Tons)
            </strong>
          </div>

        </div>

        <!-- Section B: Output Specification, Wastage & Expected Pieces Calculation -->
        <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-top:16px;">
          <div style="font-weight:800; font-size:0.85rem; color:#334155; text-transform:uppercase; letter-spacing:0.04em; margin-bottom:12px;">
            4. Output Specifications, Expected Finished Pieces & Wastage
          </div>

          <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(170px, 1fr)); gap:14px;">
            
            <!-- Consumption per Piece -->
            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800); font-size:0.8rem; margin-bottom:4px;">
                Consumption / Ratio (Mtr/KG per Pc)
              </label>
              <input type="number" step="0.001" min="0.001" id="cons-input-${itemIdx}" name="items[${itemIdx}][avg_consumption]" class="form-control" value="${item.consumption_per_pc}" style="font-weight:700;" oninput="onConsumptionChange(${itemIdx}, this.value)">
            </div>

            <!-- Expected Wastage -->
            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800); font-size:0.8rem; margin-bottom:4px;">
                Expected Wastage (Mtr / KG)
              </label>
              <input type="number" step="0.01" min="0" name="items[${itemIdx}][wastage_meters]" class="form-control" value="${item.wastage_meters}" style="font-weight:700; color:#dc2626;" oninput="onWastageChange(${itemIdx}, this.value)">
            </div>

            <!-- Expected Finished Pieces -->
            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700; color:#4338ca; font-size:0.8rem; margin-bottom:4px;">
                Expected Finished Pieces <span style="color:#ef4444;">*</span>
              </label>
              <div style="position:relative;">
                <input type="number" step="1" min="1" id="pieces-input-${itemIdx}" name="items[${itemIdx}][production_pcs]" class="form-control" required value="${pcs}" style="font-weight:800; color:#4338ca; padding-right:38px; background:#f8fafc;" oninput="onExpectedPiecesChange(${itemIdx}, this.value)">
                <span style="position:absolute; right:10px; top:50%; transform:translateY(-50%); font-size:0.75rem; color:#64748b; font-weight:700;">Pcs</span>
              </div>
            </div>

            <!-- Contractor Rate -->
            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800); font-size:0.8rem; margin-bottom:4px;">
                Contractor Rate / Pc (₹) <span style="color:#ef4444;">*</span>
              </label>
              <div style="position:relative;">
                <span style="position:absolute; left:10px; top:50%; transform:translateY(-50%); font-weight:700; color:#64748b;">₹</span>
                <input type="number" step="0.5" min="0" name="items[${itemIdx}][rate_per_piece]" class="form-control" required value="${parseFloat(item.rate_per_piece).toFixed(2)}" style="padding-left:24px; font-weight:700;" oninput="onRateChange(${itemIdx}, this.value)">
              </div>
            </div>

          </div>
        </div>
      `;

      container.appendChild(card);
      renderTonsForCard(itemIdx);
    });
  }

  function renderTonsForCard(itemIdx) {
    const item = itemsData[itemIdx];
    if (!item) return;

    const emptyBox = document.getElementById(`tons-empty-box-${itemIdx}`);
    const listBox = document.getElementById(`tons-list-box-${itemIdx}`);

    if (!emptyBox || !listBox) return;

    listBox.innerHTML = '';

    if (item.thans.length === 0) {
      emptyBox.style.display = 'block';
      listBox.style.display = 'none';
      return;
    }

    emptyBox.style.display = 'none';
    listBox.style.display = 'grid';

    item.thans.forEach((meter, tIdx) => {
      const row = document.createElement('div');
      row.className = 'ton-row';
      row.innerHTML = `
        <span style="font-weight:700; font-size:0.75rem; color:#4338ca; width:64px;">Ton #${tIdx + 1}</span>
        <div style="flex:1;">
          <input type="number" step="0.01" min="0" value="${meter}" name="items[${itemIdx}][thans][]" 
            class="form-control form-control-sm" 
            style="font-weight:700; font-size:0.85rem; padding:3px 6px; height:28px;"
            oninput="updateTonValue(${itemIdx}, ${tIdx}, this.value)"
            onfocus="this.select()">
        </div>
        <span style="font-size:0.7rem; color:#64748b; font-weight:600;">Mtr/KG</span>
        <button type="button" onclick="removeTon(${itemIdx}, ${tIdx})" title="Remove ton" style="background:none; border:none; color:#ef4444; font-size:1.15rem; cursor:pointer; line-height:1; padding:0 3px;">&times;</button>
      `;

      listBox.appendChild(row);
    });
  }

  function calculateOverallTotals() {
    let grandTotalRaw = 0;
    let grandTotalTons = 0;
    let grandTotalWastage = 0;
    let grandTotalPcs = 0;
    let grandTotalAmount = 0;

    itemsData.forEach(item => {
      const itemRaw = item.thans.reduce((a, b) => a + (parseFloat(b) || 0), 0);
      const itemWastage = parseFloat(item.wastage_meters) || 0;
      const itemPcs = parseInt(item.expected_pieces) || 0;
      const itemRate = parseFloat(item.rate_per_piece) || 0;

      grandTotalRaw += itemRaw;
      grandTotalTons += item.thans.length;
      grandTotalWastage += itemWastage;
      grandTotalPcs += itemPcs;
      grandTotalAmount += (itemPcs * itemRate);
    });

    const netRaw = Math.max(0, grandTotalRaw - grandTotalWastage);

    document.getElementById('summary-items-count').textContent = itemsData.length + (itemsData.length === 1 ? ' Item' : ' Items');
    document.getElementById('summary-than-count').textContent = grandTotalTons + ' Tons/Thans';
    document.getElementById('summary-total-raw-qty').textContent = grandTotalRaw.toFixed(2) + ' Mtr/KG';
    document.getElementById('summary-total-wastage').textContent = grandTotalWastage.toFixed(2) + ' Mtr/KG';
    document.getElementById('summary-net-raw').textContent = netRaw.toFixed(2) + ' Mtr/KG';
    document.getElementById('summary-total-pcs').textContent = grandTotalPcs.toLocaleString('en-IN') + ' Pcs';
    document.getElementById('summary-grand-amount').textContent = '₹' + grandTotalAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  document.getElementById('jw-form')?.addEventListener('submit', function(e) {
    if (itemsData.length === 0) {
      e.preventDefault();
      alert('Please add at least one raw item to this job work order.');
      return false;
    }

    let hasMissingRaw = false;
    let hasMissingFinished = false;
    let hasZeroTons = false;
    let hasZeroPcs = false;

    itemsData.forEach((item, idx) => {
      if (!item.raw_item_name) {
        hasMissingRaw = true;
      }
      if (!item.finished_item_name) {
        hasMissingFinished = true;
      }
      if (item.thans.length === 0) {
        hasZeroTons = true;
      }
      if (!item.expected_pieces || parseInt(item.expected_pieces) <= 0) {
        hasZeroPcs = true;
      }
    });

    if (hasMissingRaw) {
      e.preventDefault();
      alert('Please select a Raw Item for all entries.');
      return false;
    }

    if (hasMissingFinished) {
      e.preventDefault();
      alert('Please select a Target Finished Item for all entries.');
      return false;
    }

    if (hasZeroTons) {
      e.preventDefault();
      alert('Please select or add at least one purchase Ton/Than for each raw item.');
      return false;
    }

    if (hasZeroPcs) {
      e.preventDefault();
      alert('Please verify expected finished pieces quantity (Pcs > 0) for each item.');
      return false;
    }
  });

  document.addEventListener('DOMContentLoaded', () => {
    renderAllRawItemCards();
    calculateOverallTotals();
  });
</script>
@endpush
