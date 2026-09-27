@extends('layouts.app')

@section('title', 'New Job Inward Entry (Multi-Item & Than Breakdown) - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><a href="{{ route('jobwork.assign.index') }}" style="color:inherit; text-decoration:none;">Job Work & Assign</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item"><a href="{{ route('jobwork.inward.index') }}" style="color:inherit; text-decoration:none;">Job Inward Entries</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>New Inward Receipt</span></div>
@endsection

@section('content')
<form action="{{ route('jobwork.inward.store') }}" method="POST" id="inward-form">
  @csrf

  <div style="display:flex; flex-direction:column; gap:22px;">

    <!-- Top Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
      <div>
        <div style="display:flex; align-items:center; gap:10px;">
          <h2 style="margin:0; font-size:1.45rem; font-weight:800; color:var(--slate-900); letter-spacing:-0.02em;">
            Record Job Work Inward Receipt
          </h2>
          <span style="font-family:var(--font-mono, monospace); font-weight:800; font-size:0.9rem; background:#ecfdf5; color:#059669; padding:3px 10px; border-radius:8px; border:1px solid #a7f3d0;">
            {{ $nextInwardNo }}
          </span>
        </div>
        <p style="margin:4px 0 0; font-size:0.85rem; color:var(--slate-500);">
          Receive multi-item finished garments or processed fabric with Than-wise meter breakdown, contractor billing & QC inspection.
        </p>
      </div>

      <div style="display:flex; gap:10px; align-items:center;">
        <a href="{{ route('jobwork.inward.index') }}" class="btn btn-secondary" style="font-weight:700;">Cancel</a>
        <button type="submit" id="save-inward-btn" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:8px; font-weight:700; box-shadow:0 2px 8px rgba(5, 150, 105, 0.3); background:#059669; border-color:#059669; padding:10px 22px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
          Confirm Inward & Update Stock
        </button>
      </div>
    </div>

    <!-- 1. Header Information (Job Order, DC, Date, QC, Location) -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:24px;">
      <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--slate-100); padding-bottom:12px; margin-bottom:18px;">
        <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin:0; color:var(--slate-800); display:flex; align-items:center; gap:8px;">
          <span style="width:24px; height:24px; border-radius:6px; background:#eff6ff; color:#2563eb; display:inline-flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:800;">1</span>
          Job Order & Delivery Challan Information
        </h3>
      </div>

      <!-- Job Order Dropdown -->
      <div class="form-group" style="margin-bottom:18px;">
        <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
          Active Job Work Order (Awaiting Return) <span style="color:#ef4444;">*</span>
        </label>
        <select name="job_assignment_id" id="ja_select" class="form-control" required onchange="onJobOrderChange(this)" style="font-size:0.95rem; font-weight:700;">
          <option value="">-- Select Active Job Order / Lot --</option>
          @foreach($activeAssignments as $ja)
            @php
              $pend = max(0, $ja->issued_qty - $ja->received_qty - $ja->rejected_qty);
              $isSel = ($preselected && $preselected->id == $ja->id);
              $assignmentItems = $ja->items ? $ja->items->toArray() : [];
            @endphp
            <option value="{{ $ja->id }}" {{ $isSel ? 'selected' : '' }}
              data-order="{{ $ja->job_order_no }}"
              data-lot="{{ $ja->lot_number }}"
              data-worker="{{ $ja->job_worker_name }}"
              data-worker-id="{{ $ja->job_worker_id }}"
              data-process="{{ $ja->process_name }}"
              data-style="{{ $ja->style_name }}"
              data-issued="{{ $ja->issued_qty }}"
              data-received="{{ $ja->received_qty }}"
              data-rejected="{{ $ja->rejected_qty }}"
              data-pending="{{ $pend }}"
              data-rate="{{ $ja->rate_per_piece }}"
              data-than="{{ $ja->total_than_meters }}"
              data-wastage="{{ $ja->total_wastage_meters }}"
              data-items='@json($assignmentItems)'
            >
              Lot: {{ $ja->lot_number }} | {{ $ja->job_order_no }} — {{ $ja->job_worker_name }} ({{ $ja->process_name }}) — {{ $pend }} Pcs Pending (Issued: {{ $ja->issued_qty }})
            </option>
          @endforeach
        </select>
      </div>

      <!-- Live Job Order Details Preview Box -->
      <div id="job-order-preview-box" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px; display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:14px; margin-bottom:18px;">
        <div>
          <span style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">Contractor:</span>
          <div id="pv-worker" style="font-weight:800; color:var(--slate-800); font-size:0.95rem;">—</div>
        </div>
        <div>
          <span style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">Process:</span>
          <div id="pv-process" style="font-weight:700; color:var(--slate-700);">—</div>
        </div>
        <div>
          <span style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">Lot / Batch #:</span>
          <div id="pv-lot" style="font-family:var(--font-mono, monospace); font-weight:800; color:#4338ca;">—</div>
        </div>
        <div>
          <span style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">Style / Fabric:</span>
          <div id="pv-style" style="font-weight:700; color:var(--slate-700);">—</div>
        </div>
        <div>
          <span style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">Total Issued:</span>
          <div id="pv-issued" style="font-family:var(--font-mono, monospace); font-weight:800; color:var(--slate-900);">0 Pcs</div>
        </div>
        <div>
          <span style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">Already Received:</span>
          <div id="pv-received" style="font-family:var(--font-mono, monospace); font-weight:800; color:#059669;">0 Pcs</div>
        </div>
        <div>
          <span style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">Balance Pending:</span>
          <div id="pv-pending" style="font-family:var(--font-mono, monospace); font-weight:800; color:#dc2626; font-size:1.05rem;">0 Pcs</div>
        </div>
      </div>

      <!-- Inward Date, Challan No with Auto-Generate, QC Status, Stock Location -->
      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:18px;">
        
        <!-- Inward Date -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            Inward Date <span style="color:#ef4444;">*</span>
          </label>
          <input type="date" name="inward_date" class="form-control" required value="{{ date('Y-m-d') }}">
        </div>

        <!-- Contractor Return DC / Challan No with Auto Generate -->
        <div class="form-group" style="margin-bottom:0;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800); margin:0;">
              Contractor Return DC / Challan No. <span style="color:#ef4444;">*</span>
            </label>
            <button type="button" onclick="autoGenerateChallanNo()" style="font-size:0.75rem; color:#2563eb; background:#eff6ff; border:1px solid #bfdbfe; border-radius:6px; padding:2px 8px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:4px; transition:all 0.2s ease;" title="Auto generate unique challan number">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
              Auto Generate
            </button>
          </div>
          <div style="position:relative; display:flex; align-items:center;">
            <input type="text" name="challan_no" id="challan_no" class="form-control" required value="{{ $nextChallanNo ?? '' }}" placeholder="e.g. DC-984 / CH-104" style="font-weight:700; letter-spacing:0.5px; padding-right:75px;">
            <button type="button" onclick="autoGenerateChallanNo()" class="btn btn-secondary btn-sm" style="position:absolute; right:4px; height:calc(100% - 8px); padding:0 10px; font-size:0.75rem; font-weight:700; display:inline-flex; align-items:center; gap:4px; border-radius:6px; background:#f8fafc; color:#334155; border:1px solid #cbd5e1;" title="Regenerate Challan Number">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
              Auto
            </button>
          </div>
          <small style="font-size:0.72rem; color:var(--slate-500); margin-top:4px; display:block;">Auto-assigned or contractor's physical DC / Challan #</small>
        </div>

        <!-- QC Inspection Status -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            Quality Inspection (QC) Status <span style="color:#ef4444;">*</span>
          </label>
          <select name="qc_status" id="inw_qc_status" class="form-control" required>
            <option value="Passed QC" selected>Passed Quality Inspection (Ready for Dispatch)</option>
            <option value="Minor Touchup">Passed with Minor Iron/Thread Touchup</option>
            <option value="Under Lab QC">Under Lab QC Review</option>
            <option value="Rejected">Full Batch Rejected</option>
          </select>
        </div>

        <!-- Destination Stock Location -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            Destination Stock Location
          </label>
          <input type="text" name="storage_location" class="form-control" value="Finished Goods Stock" placeholder="e.g. Finished Goods Stock / Packing Hub">
        </div>

      </div>
    </div>

    <!-- 2. Multi-Item & Than-Wise Breakdown Section -->
    <div style="display:flex; flex-direction:column; gap:16px;">
      
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div>
          <h3 style="font-size:1.05rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin:0; color:var(--slate-900); display:flex; align-items:center; gap:8px;">
            <span style="width:26px; height:26px; border-radius:6px; background:#ecfdf5; color:#059669; display:inline-flex; align-items:center; justify-content:center; font-size:0.8rem; font-weight:800;">2</span>
            Product Items & Than Breakdown Inward
          </h3>
          <p style="margin:3px 0 0; font-size:0.825rem; color:var(--slate-500);">
            Receive finished goods or processed fabric with Than/Roll meter readings, good pieces, QC defect rejections & contractor rates.
          </p>
        </div>

        <button type="button" class="btn btn-primary btn-sm" onclick="addNewItem()" style="display:inline-flex; align-items:center; gap:6px; font-weight:700; box-shadow:0 2px 6px rgba(5, 150, 105, 0.25); background:#059669; border-color:#059669;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          + Add Another Item
        </button>
      </div>

      <!-- Container where Item Cards are dynamically rendered -->
      <div id="items-container" style="display:flex; flex-direction:column; gap:20px;"></div>

      <!-- Add Item Large Footer Trigger Button -->
      <div style="text-align:center; padding:6px 0;">
        <button type="button" class="btn btn-secondary" onclick="addNewItem()" style="border:2px dashed #cbd5e1; background:#f8fafc; color:#334155; font-weight:700; width:100%; padding:12px; display:inline-flex; align-items:center; justify-content:center; gap:8px; border-radius:12px; transition:all 0.2s ease;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Add Another Product / Fabric Quality (Multi-Item Inward)
        </button>
      </div>

    </div>

    <!-- 3. Calculation Summary Card & Remarks -->
    <div style="display:grid; grid-template-columns:1fr 420px; gap:20px; align-items:start;">
      
      <!-- Remarks -->
      <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:22px;">
        <h4 style="font-size:0.9rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin-top:0; margin-bottom:10px; color:var(--slate-800);">
          Inward Remarks & Inspection Notes
        </h4>
        <textarea name="remarks" id="inw-remarks" class="form-control" rows="4" placeholder="Finished goods received in good condition, buttons and stitching checked, ready for ironing & packing..."></textarea>
      </div>

      <!-- Live Calculation Card: Yield & Contractor Payable -->
      <div class="card" style="background:linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border:1px solid #cbd5e1; border-radius:var(--radius-xl); box-shadow:var(--shadow-sm); padding:22px; display:flex; flex-direction:column; gap:10px;">
        
        <div style="font-size:0.85rem; font-weight:800; color:var(--slate-800); text-transform:uppercase; letter-spacing:0.05em; border-bottom:1px solid #cbd5e1; padding-bottom:8px; display:flex; justify-content:space-between; align-items:center;">
          <span>Inward Yield & Billing Summary</span>
          <span class="badge" id="summary-items-count" style="background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; font-size:0.75rem;">1 Item</span>
        </div>

        <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
          <span style="color:var(--slate-600);">Total Thans Inwarded:</span>
          <strong id="summary-than-count" style="color:var(--slate-800);">0 Thans</strong>
        </div>

        <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
          <span style="color:var(--slate-600);">Total Than Meters:</span>
          <strong id="summary-total-meters" style="color:#059669; font-weight:800;">0.00 Mtr</strong>
        </div>

        <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
          <span style="color:var(--slate-600);">Good Pcs Inwarded Now:</span>
          <strong id="summary-good-inward" style="color:#059669; font-size:0.95rem;">0 Pcs</strong>
        </div>

        <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
          <span style="color:var(--slate-600);">QC Defect / Rejected:</span>
          <strong id="summary-defect-inward" style="color:#dc2626;">0 Pcs</strong>
        </div>

        <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
          <span style="color:var(--slate-600);">Wastage Returned:</span>
          <strong id="summary-wastage-returned" style="color:#475569;">0.00 Mtr</strong>
        </div>

        <div style="display:flex; justify-content:space-between; font-size:0.85rem; border-top:1px dashed #cbd5e1; padding-top:6px;">
          <span style="color:var(--slate-600); font-weight:700;">Remaining Balance Pending:</span>
          <strong id="summary-remaining-pending" style="color:#dc2626; font-size:1rem;">0 Pcs</strong>
        </div>

        <div style="border-top:2px dashed #cbd5e1; padding-top:10px; display:flex; justify-content:space-between; align-items:baseline; font-size:1.15rem;">
          <span style="font-weight:800; color:var(--slate-900);">Contractor Payable:</span>
          <span style="font-weight:800; color:#059669;" id="summary-grand">₹0.00</span>
        </div>

      </div>

    </div>

  </div>
</form>

<!-- Modal: Paste Than Meter Readings (Bulk) -->
<div id="paste-modal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(15, 23, 42, 0.6); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
  <div style="background:#fff; border-radius:16px; padding:24px; width:90%; max-width:520px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.2);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
      <h4 style="margin:0; font-weight:800; font-size:1.1rem; color:#0f172a;" id="paste-modal-title">Paste Than Meter Readings</h4>
      <button type="button" onclick="closePasteModal()" style="background:none; border:none; font-size:1.4rem; cursor:pointer; color:#94a3b8;">&times;</button>
    </div>
    <p style="font-size:0.8rem; color:#64748b; margin:0 0 12px;">
      Paste numbers separated by space, comma, or new line (e.g. from WhatsApp or Challan sheet):
    </p>
    <textarea id="paste-textarea" class="form-control" rows="6" placeholder="148, 72.50, 112.50, 103.50, 110.50, 105&#10;99.50, 116.00, 126.50, 89.50, 117.50, 98, 117, 99.50" style="font-family:monospace; font-size:0.9rem;"></textarea>
    <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:16px;">
      <button type="button" class="btn btn-secondary" onclick="closePasteModal()">Cancel</button>
      <button type="button" class="btn btn-primary" onclick="applyPastedThans()" style="font-weight:700; background:#059669; border-color:#059669;">Add to Item Thans</button>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  const masterItems = @json($items);
  let activePasteItemIndex = 0;
  let currentOrderData = null;

  // Multi-item array state for Job Inward
  let itemsData = [
    {
      item_id: '',
      item_name: 'Finished Garment',
      item_code: '',
      received_qty: 0,
      defect_qty: 0,
      wastage_returned_meters: 0,
      rate: 35.00,
      unit: 'Pcs',
      thans: []
    }
  ];

  function autoGenerateChallanNo() {
    const input = document.getElementById('challan_no');
    if (!input) return;

    const now = new Date();
    const year = now.getFullYear();
    const rand = Math.floor(1000 + Math.random() * 9000);

    const genNo = 'JDC-' + year + '-' + rand;
    input.value = genNo;

    // Visual pulse feedback
    input.style.transition = 'all 0.25s ease';
    input.style.borderColor = '#059669';
    input.style.backgroundColor = '#ecfdf5';
    input.style.boxShadow = '0 0 0 3px rgba(5, 150, 105, 0.2)';
    setTimeout(() => {
      input.style.borderColor = '';
      input.style.backgroundColor = '';
      input.style.boxShadow = '';
    }, 500);
  }

  function onJobOrderChange(select) {
    const opt = select.options[select.selectedIndex];
    if (!opt || !opt.value) {
      currentOrderData = null;
      document.getElementById('pv-worker').innerText = '—';
      document.getElementById('pv-process').innerText = '—';
      document.getElementById('pv-lot').innerText = '—';
      document.getElementById('pv-style').innerText = '—';
      document.getElementById('pv-issued').innerText = '0 Pcs';
      document.getElementById('pv-received').innerText = '0 Pcs';
      document.getElementById('pv-pending').innerText = '0 Pcs';
      itemsData = [
        {
          item_id: '',
          item_name: 'Finished Garment',
          item_code: '',
          received_qty: 0,
          defect_qty: 0,
          wastage_returned_meters: 0,
          rate: 35.00,
          unit: 'Pcs',
          thans: []
        }
      ];
      renderAllItems();
      calculateOverallTotals();
      return;
    }

    let parsedItems = [];
    try {
      const raw = opt.getAttribute('data-items');
      if (raw) parsedItems = JSON.parse(raw);
    } catch(e) {}

    currentOrderData = {
      order: opt.getAttribute('data-order'),
      lot: opt.getAttribute('data-lot'),
      worker: opt.getAttribute('data-worker'),
      process: opt.getAttribute('data-process'),
      style: opt.getAttribute('data-style') || 'Garment Item',
      issued: parseInt(opt.getAttribute('data-issued') || 0),
      received: parseInt(opt.getAttribute('data-received') || 0),
      rejected: parseInt(opt.getAttribute('data-rejected') || 0),
      pending: parseInt(opt.getAttribute('data-pending') || 0),
      rate: parseFloat(opt.getAttribute('data-rate') || 0),
      than: parseFloat(opt.getAttribute('data-than') || 0),
      wastage: parseFloat(opt.getAttribute('data-wastage') || 0),
      items: parsedItems
    };

    document.getElementById('pv-worker').innerText = currentOrderData.worker;
    document.getElementById('pv-process').innerText = currentOrderData.process;
    document.getElementById('pv-lot').innerText = currentOrderData.lot;
    document.getElementById('pv-style').innerText = currentOrderData.style;
    document.getElementById('pv-issued').innerText = currentOrderData.issued + ' Pcs';
    document.getElementById('pv-received').innerText = currentOrderData.received + ' Pcs';
    document.getElementById('pv-pending').innerText = currentOrderData.pending + ' Pcs';

    // Populate items Data from Job Order if available
    if (parsedItems && parsedItems.length > 0) {
      itemsData = [];
      parsedItems.forEach(it => {
        itemsData.push({
          item_id: it.item_id || '',
          item_name: it.item_name || currentOrderData.style,
          item_code: it.item_code || '',
          received_qty: parseInt(it.expected_pieces || it.issued_qty || 0),
          defect_qty: 0,
          wastage_returned_meters: parseFloat(it.wastage_meters || 0),
          rate: parseFloat(it.rate_per_piece || currentOrderData.rate || 0),
          unit: 'Pcs',
          thans: (it.than_meters > 0) ? [parseFloat(it.than_meters)] : []
        });
      });
    } else {
      itemsData = [
        {
          item_id: '',
          item_name: currentOrderData.style,
          item_code: '',
          received_qty: currentOrderData.pending,
          defect_qty: 0,
          wastage_returned_meters: currentOrderData.wastage,
          rate: currentOrderData.rate,
          unit: 'Pcs',
          thans: []
        }
      ];
    }

    renderAllItems();
    calculateOverallTotals();
  }

  function addNewItem() {
    const defRate = currentOrderData ? currentOrderData.rate : 35.00;
    itemsData.push({
      item_id: '',
      item_name: '',
      item_code: '',
      received_qty: 0,
      defect_qty: 0,
      wastage_returned_meters: 0,
      rate: defRate,
      unit: 'Pcs',
      thans: []
    });
    renderAllItems();
    calculateOverallTotals();
  }

  function removeItem(itemIdx) {
    if (itemsData.length <= 1) {
      alert('A job inward receipt must have at least one item.');
      return;
    }
    const item = itemsData[itemIdx];
    if (item.thans.length > 0 || item.received_qty > 0) {
      if (!confirm(`Remove Item #${itemIdx + 1} (${item.item_name || 'Item'}) and its readings?`)) {
        return;
      }
    }
    itemsData.splice(itemIdx, 1);
    renderAllItems();
    calculateOverallTotals();
  }

  function onItemSelectChange(itemIdx, select) {
    const opt = select.options[select.selectedIndex];
    if (!opt || !opt.value) {
      itemsData[itemIdx].item_id = '';
      itemsData[itemIdx].item_name = '';
      itemsData[itemIdx].item_code = '';
    } else {
      itemsData[itemIdx].item_id = opt.getAttribute('data-id') || '';
      itemsData[itemIdx].item_name = opt.value;
      itemsData[itemIdx].item_code = opt.getAttribute('data-code') || '';
      const r = parseFloat(opt.getAttribute('data-rate'));
      if (r > 0 && itemsData[itemIdx].rate === 0) {
        itemsData[itemIdx].rate = r;
      }
    }
    renderAllItems();
    calculateOverallTotals();
  }

  function onItemFieldChange(itemIdx, field, val) {
    const num = parseFloat(val);
    itemsData[itemIdx][field] = !isNaN(num) && num >= 0 ? num : 0;
    updateItemSummaryHeader(itemIdx);
    calculateOverallTotals();
  }

  function addThanToItem(itemIdx, meterValue) {
    const val = parseFloat(meterValue);
    if (isNaN(val) || val <= 0) return;
    itemsData[itemIdx].thans.push(Math.round(val * 100) / 100);
    renderItemThans(itemIdx);
    calculateOverallTotals();
  }

  function addSingleThanFromInput(itemIdx) {
    const input = document.getElementById(`quick-than-input-${itemIdx}`);
    if (!input) return;
    const val = input.value.trim();
    if (val) {
      addThanToItem(itemIdx, val);
      input.value = '';
      input.focus();
    }
  }

  function removeThan(itemIdx, thanIdx) {
    itemsData[itemIdx].thans.splice(thanIdx, 1);
    renderItemThans(itemIdx);
    calculateOverallTotals();
  }

  function updateThanMeter(itemIdx, thanIdx, newMeter) {
    const val = parseFloat(newMeter);
    if (!isNaN(val) && val >= 0) {
      itemsData[itemIdx].thans[thanIdx] = Math.round(val * 100) / 100;
      updateItemThansSubtotals(itemIdx);
      calculateOverallTotals();
    }
  }

  function clearItemThans(itemIdx) {
    if (itemsData[itemIdx].thans.length === 0) return;
    if (confirm(`Clear all thans for Item #${itemIdx + 1}?`)) {
      itemsData[itemIdx].thans = [];
      renderItemThans(itemIdx);
      calculateOverallTotals();
    }
  }

  function loadSampleChallanForItem(itemIdx) {
    itemsData[itemIdx].thans = [148, 72.50, 112.50, 103.50, 110.50, 105, 99.50, 116, 126.50, 89.50, 117.50, 98, 117, 99.50];
    renderItemThans(itemIdx);
    calculateOverallTotals();
  }

  function openPasteModalForItem(itemIdx) {
    activePasteItemIndex = itemIdx;
    document.getElementById('paste-modal-title').textContent = `Paste Thans for Item #${itemIdx + 1} (${itemsData[itemIdx].item_name || 'Item'})`;
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
        if (val > 0) itemsData[activePasteItemIndex].thans.push(Math.round(val * 100) / 100);
      });
      renderItemThans(activePasteItemIndex);
      calculateOverallTotals();
    }
    closePasteModal();
  }

  function renderAllItems() {
    const container = document.getElementById('items-container');
    if (!container) return;

    container.innerHTML = '';

    itemsData.forEach((item, itemIdx) => {
      const itemCard = document.createElement('div');
      itemCard.className = 'card item-card-box';
      itemCard.id = `item-card-${itemIdx}`;
      itemCard.style.cssText = 'background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:22px; transition:border-color 0.2s ease;';

      let itemOptions = `<option value="">-- Select Fabric / Garment SKU --</option>`;
      masterItems.forEach(itm => {
        const isSel = (item.item_name && (itm.name === item.item_name || itm.id == item.item_id)) ? 'selected' : '';
        itemOptions += `<option value="${itm.name}" data-id="${itm.id}" data-code="${itm.code || ''}" data-rate="${itm.unit_cost || 0}" data-unit="${itm.unit || 'Pcs'}" ${isSel}>${itm.name} [${itm.code || 'ITEM'}] (${itm.category || 'Garment'})</option>`;
      });

      const totalMeters = item.thans.reduce((a, b) => a + (parseFloat(b) || 0), 0);
      const calcQty = item.received_qty > 0 ? item.received_qty : totalMeters;
      const lineTotal = Math.round(calcQty * (item.rate || 0) * 100) / 100;

      itemCard.innerHTML = `
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--slate-100); padding-bottom:12px; margin-bottom:16px; flex-wrap:wrap; gap:10px;">
          <div style="display:flex; align-items:center; gap:10px;">
            <span style="width:26px; height:26px; border-radius:8px; background:#ecfdf5; color:#059669; display:inline-flex; align-items:center; justify-content:center; font-size:0.85rem; font-weight:800;">
              #${itemIdx + 1}
            </span>
            <span style="font-weight:800; font-size:1rem; color:var(--slate-900);" id="item-title-display-${itemIdx}">
              ${item.item_name || 'Product / Fabric Quality ' + (itemIdx + 1)}
            </span>
            ${item.item_code ? `<span style="font-family:monospace; font-size:0.75rem; background:#f1f5f9; padding:2px 6px; border-radius:6px; color:#475569;">${item.item_code}</span>` : ''}
          </div>

          <div style="display:flex; align-items:center; gap:10px;">
            <div style="display:flex; gap:8px; align-items:center;">
              <span class="badge" style="background:#f1f5f9; color:#334155; border:1px solid #cbd5e1; font-weight:700; font-size:0.8rem;" id="item-badge-thans-${itemIdx}">
                ${item.thans.length} Thans
              </span>
              <span class="badge" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; font-weight:800; font-size:0.85rem;" id="item-badge-meters-${itemIdx}">
                ${totalMeters.toFixed(2)} Mtr
              </span>
              <span class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-weight:800; font-size:0.85rem;" id="item-badge-total-${itemIdx}">
                ₹${lineTotal.toFixed(2)}
              </span>
            </div>

            ${itemsData.length > 1 ? `
              <button type="button" class="btn btn-secondary btn-xs" onclick="removeItem(${itemIdx})" style="color:#ef4444; border-color:#fecaca; background:#fef2f2; font-weight:700;" title="Remove this item">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                Remove
              </button>
            ` : ''}
          </div>
        </div>

        <input type="hidden" name="items[${itemIdx}][item_id]" value="${item.item_id || ''}">
        <input type="hidden" name="items[${itemIdx}][item_code]" value="${item.item_code || ''}">
        <input type="hidden" name="items[${itemIdx}][unit]" value="${item.unit || 'Pcs'}">

        <!-- Item Controls Grid -->
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:14px; margin-bottom:18px;">
          
          <!-- Item SKU / Description -->
          <div class="form-group" style="margin-bottom:0; grid-column:span 2;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:4px; font-size:0.85rem;">
              Product Item / Fabric Quality <span style="color:#ef4444;">*</span>
            </label>
            <div style="display:flex; gap:6px;">
              <select class="form-control" name="items[${itemIdx}][item_name_select]" onchange="onItemSelectChange(${itemIdx}, this)" style="font-size:0.85rem; font-weight:600;">
                ${itemOptions}
              </select>
              <input type="text" name="items[${itemIdx}][item_name]" class="form-control" value="${item.item_name || ''}" placeholder="Or type custom item name" required style="font-size:0.85rem; font-weight:700;" oninput="itemsData[${itemIdx}].item_name = this.value; document.getElementById('item-title-display-${itemIdx}').textContent = this.value || 'Item ${itemIdx+1}';">
            </div>
          </div>

          <!-- Received Good Pieces -->
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; color:#059669; margin-bottom:4px; font-size:0.85rem;">
              Good Received (Pcs) <span style="color:#ef4444;">*</span>
            </label>
            <input type="number" step="1" min="0" name="items[${itemIdx}][received_qty]" class="form-control" value="${item.received_qty || 0}" required style="font-weight:800; color:#059669; font-size:0.95rem;" oninput="onItemFieldChange(${itemIdx}, 'received_qty', this.value)">
          </div>

          <!-- Defect / Rejections (Pcs) -->
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; color:#dc2626; margin-bottom:4px; font-size:0.85rem;">
              QC Defect (Pcs)
            </label>
            <input type="number" step="1" min="0" name="items[${itemIdx}][defect_qty]" class="form-control" value="${item.defect_qty || 0}" style="font-weight:700; color:#dc2626; font-size:0.95rem;" oninput="onItemFieldChange(${itemIdx}, 'defect_qty', this.value)">
          </div>

          <!-- Wastage Returned Meters -->
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:4px; font-size:0.85rem;">
              Wastage (Mtr)
            </label>
            <input type="number" step="0.01" min="0" name="items[${itemIdx}][wastage_returned_meters]" class="form-control" value="${item.wastage_returned_meters || 0}" style="font-weight:700; font-size:0.95rem;" oninput="onItemFieldChange(${itemIdx}, 'wastage_returned_meters', this.value)">
          </div>

          <!-- Contractor Rate / Pc -->
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:4px; font-size:0.85rem;">
              Contractor Rate (₹) <span style="color:#ef4444;">*</span>
            </label>
            <input type="number" step="0.01" min="0" name="items[${itemIdx}][rate]" class="form-control" value="${item.rate || 0}" required style="font-weight:700; font-size:0.95rem;" oninput="onItemFieldChange(${itemIdx}, 'rate', this.value)">
          </div>

        </div>

        <!-- Hidden input storing serialized clean thans array -->
        <input type="hidden" name="items[${itemIdx}][thans]" id="item-thans-input-${itemIdx}" value="${item.thans.join(',')}">

        <!-- Than / Roll Breakdown Sub-Box -->
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px;">
          
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; flex-wrap:wrap; gap:8px;">
            <div style="display:flex; align-items:center; gap:8px;">
              <span style="font-weight:800; font-size:0.85rem; color:#1e293b; text-transform:uppercase; letter-spacing:0.04em;">
                Than / Roll Meter Readings (${item.item_name || 'Fabric'})
              </span>
              <span class="badge" style="background:#ecfdf5; color:#059669; font-weight:800;" id="than-count-pill-${itemIdx}">
                ${item.thans.length} Thans (${totalMeters.toFixed(2)} Mtr)
              </span>
            </div>

            <div style="display:flex; gap:6px; align-items:center;">
              <button type="button" class="btn btn-secondary btn-xs" onclick="openPasteModalForItem(${itemIdx})" style="font-weight:700; display:inline-flex; align-items:center; gap:4px; background:#fff;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
                📋 Paste Multi-Thans
              </button>
              <button type="button" class="btn btn-secondary btn-xs" onclick="clearItemThans(${itemIdx})" style="color:#ef4444; background:#fff; font-weight:700;">
                Clear Thans
              </button>
            </div>
          </div>

          <div style="display:flex; gap:8px; margin-bottom:14px;">
            <input type="number" step="0.01" min="0" id="quick-than-input-${itemIdx}" class="form-control" placeholder="Type meter reading (e.g. 148) and press Enter" style="font-size:0.9rem; font-weight:600;" onkeydown="if(event.key==='Enter'){event.preventDefault();addSingleThanFromInput(${itemIdx});}">
            <button type="button" class="btn btn-primary btn-sm" onclick="addSingleThanFromInput(${itemIdx})" style="font-weight:700; white-space:nowrap; display:inline-flex; align-items:center; gap:6px; background:#059669; border-color:#059669;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Add Than
            </button>
          </div>

          <div id="than-empty-box-${itemIdx}" style="text-align:center; padding:22px 14px; color:#94a3b8; border:2px dashed #cbd5e1; border-radius:10px; background:#ffffff; ${item.thans.length > 0 ? 'display:none;' : 'display:block;'}">
            <div style="font-weight:700; font-size:0.9rem; color:#64748b; margin-bottom:3px;">No Thans recorded for this item yet</div>
            <p style="font-size:0.775rem; margin:0 0 10px;">Type meter reading above or paste multiple readings from contractor challan.</p>
            <button type="button" class="btn btn-secondary btn-xs" onclick="loadSampleChallanForItem(${itemIdx})">Load Sample Challan (14 Thans)</button>
          </div>

          <div id="than-sheet-box-${itemIdx}" style="${item.thans.length > 0 ? 'display:grid;' : 'display:none;'} grid-template-columns:1fr 1fr; gap:14px;">
            <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:12px;">
              <div style="font-weight:700; font-size:0.75rem; color:#64748b; text-transform:uppercase; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-bottom:8px; display:flex; justify-content:space-between;">
                <span>Column 1</span>
                <span id="col1-badge-${itemIdx}" style="color:#059669; font-weight:800;">0.00 Mtr</span>
              </div>
              <div id="col1-thans-${itemIdx}" style="display:flex; flex-direction:column; gap:6px;"></div>
            </div>

            <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:12px;">
              <div style="font-weight:700; font-size:0.75rem; color:#64748b; text-transform:uppercase; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-bottom:8px; display:flex; justify-content:space-between;">
                <span>Column 2</span>
                <span id="col2-badge-${itemIdx}" style="color:#059669; font-weight:800;">0.00 Mtr</span>
              </div>
              <div id="col2-thans-${itemIdx}" style="display:flex; flex-direction:column; gap:6px;"></div>
            </div>
          </div>

        </div>
      `;

      container.appendChild(itemCard);
      renderItemThans(itemIdx);
    });
  }

  function renderItemThans(itemIdx) {
    const item = itemsData[itemIdx];
    if (!item) return;

    const emptyBox = document.getElementById(`than-empty-box-${itemIdx}`);
    const sheetBox = document.getElementById(`than-sheet-box-${itemIdx}`);
    const col1 = document.getElementById(`col1-thans-${itemIdx}`);
    const col2 = document.getElementById(`col2-thans-${itemIdx}`);

    if (!emptyBox || !sheetBox || !col1 || !col2) return;

    col1.innerHTML = '';
    col2.innerHTML = '';

    if (item.thans.length === 0) {
      emptyBox.style.display = 'block';
      sheetBox.style.display = 'none';
      updateItemSummaryHeader(itemIdx);
      return;
    }

    emptyBox.style.display = 'none';
    sheetBox.style.display = 'grid';

    const half = Math.ceil(item.thans.length / 2);
    let col1Sum = 0;
    let col2Sum = 0;

    item.thans.forEach((meter, tIdx) => {
      const row = document.createElement('div');
      row.style.cssText = 'display:flex; justify-content:space-between; align-items:center; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:5px 8px; font-size:0.85rem;';
      row.innerHTML = `
        <span style="font-weight:700; color:#64748b; font-size:0.75rem; width:22px;">#${tIdx + 1}</span>
        <input type="number" step="0.01" min="0" value="${meter}" style="width:90px; text-align:right; font-weight:800; border:1px solid #cbd5e1; border-radius:4px; padding:2px 6px; font-size:0.85rem;" onchange="updateThanMeter(${itemIdx}, ${tIdx}, this.value)">
        <span style="font-size:0.75rem; color:#64748b; margin-left:4px;">Mtr</span>
        <button type="button" onclick="removeThan(${itemIdx}, ${tIdx})" style="background:none; border:none; color:#ef4444; font-weight:800; cursor:pointer; font-size:1.1rem; line-height:1; padding:0 4px;" title="Remove Than">&times;</button>
      `;

      if (tIdx < half) {
        col1.appendChild(row);
        col1Sum += meter;
      } else {
        col2.appendChild(row);
        col2Sum += meter;
      }
    });

    document.getElementById(`col1-badge-${itemIdx}`).textContent = col1Sum.toFixed(2) + ' Mtr';
    document.getElementById(`col2-badge-${itemIdx}`).textContent = col2Sum.toFixed(2) + ' Mtr';

    // Synchronize serialized input
    const serializedInput = document.getElementById(`item-thans-input-${itemIdx}`);
    if (serializedInput) serializedInput.value = item.thans.join(',');

    updateItemSummaryHeader(itemIdx);
  }

  function updateItemThansSubtotals(itemIdx) {
    const item = itemsData[itemIdx];
    if (!item) return;

    const half = Math.ceil(item.thans.length / 2);
    let col1Sum = 0;
    let col2Sum = 0;

    item.thans.forEach((meter, tIdx) => {
      if (tIdx < half) col1Sum += meter;
      else col2Sum += meter;
    });

    const b1 = document.getElementById(`col1-badge-${itemIdx}`);
    const b2 = document.getElementById(`col2-badge-${itemIdx}`);
    if (b1) b1.textContent = col1Sum.toFixed(2) + ' Mtr';
    if (b2) b2.textContent = col2Sum.toFixed(2) + ' Mtr';

    const serializedInput = document.getElementById(`item-thans-input-${itemIdx}`);
    if (serializedInput) serializedInput.value = item.thans.join(',');

    updateItemSummaryHeader(itemIdx);
  }

  function updateItemSummaryHeader(itemIdx) {
    const item = itemsData[itemIdx];
    if (!item) return;

    const totalMeters = item.thans.reduce((a, b) => a + (parseFloat(b) || 0), 0);
    const calcQty = item.received_qty > 0 ? item.received_qty : totalMeters;
    const lineTotal = Math.round(calcQty * (item.rate || 0) * 100) / 100;

    const pill = document.getElementById(`than-count-pill-${itemIdx}`);
    if (pill) pill.textContent = `${item.thans.length} Thans (${totalMeters.toFixed(2)} Mtr)`;

    const thansBadge = document.getElementById(`item-badge-thans-${itemIdx}`);
    const metersBadge = document.getElementById(`item-badge-meters-${itemIdx}`);
    const totalBadge = document.getElementById(`item-badge-total-${itemIdx}`);
    const titleDisplay = document.getElementById(`item-title-display-${itemIdx}`);

    if (thansBadge) thansBadge.textContent = `${item.thans.length} Thans`;
    if (metersBadge) metersBadge.textContent = `${totalMeters.toFixed(2)} Mtr`;
    if (totalBadge) totalBadge.textContent = `₹${lineTotal.toFixed(2)}`;
    if (titleDisplay && item.item_name) titleDisplay.textContent = item.item_name;
  }

  function calculateOverallTotals() {
    let grandTotalMeters = 0;
    let grandTotalThans = 0;
    let grandGoodQty = 0;
    let grandDefectQty = 0;
    let grandWastage = 0;
    let grandContractorAmount = 0;

    itemsData.forEach(item => {
      const itemMeters = item.thans.reduce((a, b) => a + (parseFloat(b) || 0), 0);
      const calcQty = item.received_qty > 0 ? item.received_qty : itemMeters;
      const itemTotal = Math.round(calcQty * (item.rate || 0) * 100) / 100;

      grandTotalMeters += itemMeters;
      grandTotalThans += item.thans.length;
      grandGoodQty += (parseInt(item.received_qty) || 0);
      grandDefectQty += (parseInt(item.defect_qty) || 0);
      grandWastage += (parseFloat(item.wastage_returned_meters) || 0);
      grandContractorAmount += itemTotal;
    });

    const pending = currentOrderData ? currentOrderData.pending : 0;
    const remainingPending = Math.max(0, pending - grandGoodQty - grandDefectQty);

    document.getElementById('summary-items-count').textContent = itemsData.length + (itemsData.length === 1 ? ' Item' : ' Items');
    document.getElementById('summary-than-count').textContent = grandTotalThans + ' Thans';
    document.getElementById('summary-total-meters').textContent = grandTotalMeters.toFixed(2) + ' Mtr';
    document.getElementById('summary-good-inward').textContent = grandGoodQty + ' Pcs';
    document.getElementById('summary-defect-inward').textContent = grandDefectQty + ' Pcs';
    document.getElementById('summary-wastage-returned').textContent = grandWastage.toFixed(2) + ' Mtr';
    document.getElementById('summary-remaining-pending').textContent = remainingPending + ' Pcs';
    document.getElementById('summary-grand').textContent = '₹' + grandContractorAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  document.getElementById('inward-form')?.addEventListener('submit', function(e) {
    if (!document.getElementById('ja_select').value) {
      e.preventDefault();
      alert('Please select an Active Job Work Order.');
      return false;
    }

    if (itemsData.length === 0) {
      e.preventDefault();
      alert('Please add at least one item to inward.');
      return false;
    }

    let hasMissingSku = false;
    let hasZeroQty = false;

    itemsData.forEach((item, idx) => {
      if (!item.item_name) {
        hasMissingSku = true;
      }
      if (item.received_qty <= 0 && item.thans.length === 0) {
        hasZeroQty = true;
      }
    });

    if (hasMissingSku) {
      e.preventDefault();
      alert('Please enter or select an Item Name / Fabric SKU for all item rows.');
      return false;
    }

    if (hasZeroQty) {
      e.preventDefault();
      alert('Please enter Received Good Qty (Pcs) or Than meter readings for each item before saving.');
      return false;
    }
  });

  document.addEventListener('DOMContentLoaded', () => {
    const sel = document.getElementById('ja_select');
    if (sel && sel.value) {
      onJobOrderChange(sel);
    } else {
      renderAllItems();
      calculateOverallTotals();
    }
  });
</script>
@endpush
