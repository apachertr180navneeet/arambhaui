@extends('layouts.app')

@section('title', 'Edit Job Work Inward Receipt - ' . $inward->inward_number . ' - aarambh')

@section('breadcrumb')
  <div class="breadcrumb-item"><a href="{{ route('jobwork.assign.index') }}" style="color:inherit; text-decoration:none;">Job Work & Assign</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item"><a href="{{ route('jobwork.inward.index') }}" style="color:inherit; text-decoration:none;">Job Inward Entries</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Edit {{ $inward->inward_number }}</span></div>
@endsection

@push('styles')
<style>
  .inward-item-card {
    background: #ffffff;
    border-radius: var(--radius-xl, 16px);
    border: 1px solid var(--slate-200, #e2e8f0);
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    padding: 22px;
    transition: all 0.2s ease;
  }
  .inward-item-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.07);
  }
  .inward-item-card.completed-item {
    background: #f8fafc;
    border-color: #e2e8f0;
    opacity: 0.9;
  }
  .tracking-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 9px;
    border-radius: 6px;
    font-size: 0.775rem;
    font-weight: 700;
  }
  .tracking-badge.assigned {
    background: #eef2ff;
    color: #4338ca;
    border: 1px solid #c7d2fe;
  }
  .tracking-badge.received {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
  }
  .tracking-badge.remaining {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
  }
  .tracking-badge.status-pending {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
  }
  .tracking-badge.status-partial {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
  }
  .tracking-badge.status-completed {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #6ee7b7;
  }
</style>
@endpush

@section('content')
<form action="{{ route('jobwork.inward.update', $inward->id) }}" method="POST" id="inward-form">
  @csrf
  @method('PUT')

  <div style="display:flex; flex-direction:column; gap:22px;">

    <!-- Top Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
      <div>
        <div style="display:flex; align-items:center; gap:10px;">
          <h2 style="margin:0; font-size:1.45rem; font-weight:800; color:var(--slate-900); letter-spacing:-0.02em;">
            Edit Job Work Inward Receipt
          </h2>
          <span style="font-family:var(--font-mono, monospace); font-weight:800; font-size:0.9rem; background:#eff6ff; color:#2563eb; padding:3px 10px; border-radius:8px; border:1px solid #bfdbfe;">
            {{ $inward->inward_number }}
          </span>
        </div>
        <p style="margin:4px 0 0; font-size:0.85rem; color:var(--slate-500);">
          Update received quantities, DC / challan info, inspection status, and piece tracking for this inward entry.
        </p>
      </div>

      <div style="display:flex; gap:10px; align-items:center;">
        <a href="{{ route('jobwork.inward.index') }}" class="btn btn-secondary" style="font-weight:700;">Cancel</a>
        <button type="submit" id="save-inward-btn-top" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:8px; font-weight:700; box-shadow:0 2px 8px rgba(5, 150, 105, 0.3); background:#059669; border-color:#059669; padding:10px 22px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
          Update Inward & Stock
        </button>
      </div>
    </div>

    <!-- 1. Selection Card: Job Worker & Job Assignment -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl, 16px); border:1px solid var(--slate-200, #e2e8f0); box-shadow:var(--shadow-sm); padding:24px;">
      <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--slate-100, #f1f5f9); padding-bottom:12px; margin-bottom:18px;">
        <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin:0; color:var(--slate-800); display:flex; align-items:center; gap:8px;">
          <span style="width:24px; height:24px; border-radius:6px; background:#eff6ff; color:#2563eb; display:inline-flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:800;">1</span>
          Contractor & Job Assignment
        </h3>
        <span style="font-size:0.75rem; background:#eff6ff; color:#1d4ed8; font-weight:700; padding:3px 10px; border-radius:9999px; border:1px solid #bfdbfe;">
          Active Order: Lot {{ $inward->lot_number ?? '—' }} ({{ $inward->job_order_no }})
        </span>
      </div>

      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:18px; margin-bottom:18px;">
        
        <!-- Step 1: Select Job Worker -->
        <div class="form-group" style="margin-bottom:0;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800); margin:0;">
              1. Job Worker / Contractor <span style="color:#ef4444;">*</span>
            </label>
            <span style="font-size:0.75rem; color:var(--slate-500);">Assigned contractor</span>
          </div>
          <select id="worker_select" name="job_worker_id" class="form-control" required onchange="onWorkerSelectChange(this)" style="font-size:0.95rem; font-weight:700;">
            <option value="">-- Select Job Worker / Contractor --</option>
            @foreach($jobworkers as $jw)
              @php
                $wKey = (string)$jw->id;
                $activeCount = isset($assignmentsByWorker[$wKey]) ? count($assignmentsByWorker[$wKey]) : 0;
                $isWorkerSel = ($inward->job_worker_id == $jw->id);
              @endphp
              <option value="{{ $jw->id }}" data-name="{{ $jw->name }}" data-process="{{ $jw->process_type }}" {{ $isWorkerSel ? 'selected' : '' }}>
                {{ $jw->name }} [{{ $jw->process_type ?? 'Contractor' }}] — {{ $activeCount }} Active Job(s)
              </option>
            @endforeach
          </select>
        </div>

        <!-- Step 2: Select Job Assignment -->
        <div class="form-group" style="margin-bottom:0;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800); margin:0;">
              2. Job Assignment (Order / Lot) <span style="color:#ef4444;">*</span>
            </label>
            <span style="font-size:0.75rem; color:var(--slate-500);">Assigned lot order</span>
          </div>
          <select name="job_assignment_id" id="ja_select" class="form-control" required onchange="onJobOrderChange(this)" style="font-size:0.95rem; font-weight:700;">
            <option value="">-- Select Job Assignment --</option>
          </select>
        </div>

      </div>

      <!-- Live Job Order Details Preview Banner -->
      <div id="job-order-preview-box" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px; display:grid; grid-template-columns:repeat(auto-fit, minmax(160px, 1fr)); gap:14px;">
        <div>
          <span style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">Contractor:</span>
          <div id="pv-worker" style="font-weight:800; color:var(--slate-800); font-size:0.95rem;">{{ $inward->job_worker_name }}</div>
        </div>
        <div>
          <span style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">Process:</span>
          <div id="pv-process" style="font-weight:700; color:var(--slate-700);">{{ $inward->process_name }}</div>
        </div>
        <div>
          <span style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">Lot / Batch #:</span>
          <div id="pv-lot" style="font-family:var(--font-mono, monospace); font-weight:800; color:#4338ca;">{{ $inward->lot_number }}</div>
        </div>
        <div>
          <span style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">Style / Primary Item:</span>
          <div id="pv-style" style="font-weight:700; color:var(--slate-700);">{{ $inward->style_name }}</div>
        </div>
        <div>
          <span style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">Total Assigned:</span>
          <div id="pv-issued" style="font-family:var(--font-mono, monospace); font-weight:800; color:var(--slate-900);">0 Pcs</div>
        </div>
        <div>
          <span style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">Previously Received:</span>
          <div id="pv-received" style="font-family:var(--font-mono, monospace); font-weight:800; color:#059669;">0 Pcs</div>
        </div>
        <div>
          <span style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">Remaining Balance:</span>
          <div id="pv-pending" style="font-family:var(--font-mono, monospace); font-weight:800; color:#dc2626; font-size:1.05rem;">0 Pcs</div>
        </div>
      </div>

    </div>

    <!-- 2. Step 2: Assigned Items & Inward Quantity Tracking -->
    <div style="display:flex; flex-direction:column; gap:16px;">
      
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div>
          <h3 style="font-size:1.05rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin:0; color:var(--slate-900); display:flex; align-items:center; gap:8px;">
            <span style="width:26px; height:26px; border-radius:6px; background:#ecfdf5; color:#059669; display:inline-flex; align-items:center; justify-content:center; font-size:0.8rem; font-weight:800;">2</span>
            Assigned Items & Inward Quantities
          </h3>
          <p style="margin:3px 0 0; font-size:0.825rem; color:var(--slate-500);">
            Live tracking of Assigned Quantity, Other Receipts, and Current Inward Pieces.
          </p>
        </div>

        <div id="header-items-summary-badge" style="display:flex; gap:8px; align-items:center;">
          <!-- Dynamically filled -->
        </div>
      </div>

      <!-- Container where Item Cards are dynamically rendered -->
      <div id="items-container" style="display:flex; flex-direction:column; gap:18px;">
        <div style="text-align:center; padding:32px 18px; color:#64748b; border:2px dashed #cbd5e1; border-radius:16px; background:#ffffff;">
          <div style="font-weight:700; font-size:0.95rem; color:#475569; margin-bottom:4px;">Loading Assigned Items...</div>
        </div>
      </div>

    </div>

    <!-- 3. Delivery Challan, QC Status & Storage Location -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl, 16px); border:1px solid var(--slate-200, #e2e8f0); box-shadow:var(--shadow-sm); padding:24px;">
      <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin:0; color:var(--slate-800); border-bottom:1px solid var(--slate-100, #f1f5f9); padding-bottom:12px; margin-bottom:18px; display:flex; align-items:center; gap:8px;">
        <span style="width:24px; height:24px; border-radius:6px; background:#f5f3ff; color:#7c3aed; display:inline-flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:800;">3</span>
        Delivery Challan & Quality Control (QC)
      </h3>

      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:18px;">
        
        <!-- Inward Date -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            Inward Receipt Date <span style="color:#ef4444;">*</span>
          </label>
          <input type="date" name="inward_date" class="form-control" required value="{{ old('inward_date', $inward->inward_date) }}">
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
            <input type="text" name="challan_no" id="challan_no" class="form-control" required value="{{ old('challan_no', $inward->challan_no) }}" placeholder="e.g. DC-984 / CH-104" style="font-weight:700; letter-spacing:0.5px; padding-right:75px;">
            <button type="button" onclick="autoGenerateChallanNo()" class="btn btn-secondary btn-sm" style="position:absolute; right:4px; height:calc(100% - 8px); padding:0 10px; font-size:0.75rem; font-weight:700; display:inline-flex; align-items:center; gap:4px; border-radius:6px; background:#f8fafc; color:#334155; border:1px solid #cbd5e1;" title="Regenerate Challan Number">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
              Auto
            </button>
          </div>
          <small style="font-size:0.72rem; color:var(--slate-500); margin-top:4px; display:block;">Contractor's delivery challan reference</small>
        </div>

        <!-- QC Inspection Status -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            Quality Inspection (QC) Status <span style="color:#ef4444;">*</span>
          </label>
          <select name="qc_status" id="inw_qc_status" class="form-control" required>
            <option value="Passed QC" {{ $inward->qc_status === 'Passed QC' ? 'selected' : '' }}>Passed Quality Inspection (Ready for Stock)</option>
            <option value="Minor Touchup" {{ $inward->qc_status === 'Minor Touchup' ? 'selected' : '' }}>Passed with Minor Iron/Thread Touchup</option>
            <option value="Under Lab QC" {{ $inward->qc_status === 'Under Lab QC' ? 'selected' : '' }}>Under Lab QC Review</option>
            <option value="Rejected" {{ $inward->qc_status === 'Rejected' ? 'selected' : '' }}>Batch Rejected / Sent for Rework</option>
          </select>
        </div>

        <!-- Destination Stock Location -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            Destination Stock Location
          </label>
          <input type="text" name="storage_location" class="form-control" value="{{ old('storage_location', $inward->storage_location ?? 'Finished Goods Stock') }}" placeholder="e.g. Finished Goods Stock / Packing Hub">
        </div>

      </div>
    </div>

    <!-- 4. Calculation Summary Card & Remarks -->
    <div style="display:grid; grid-template-columns:1fr 420px; gap:20px; align-items:start;">
      
      <!-- Remarks -->
      <div class="card" style="background:#fff; border-radius:var(--radius-xl, 16px); border:1px solid var(--slate-200, #e2e8f0); box-shadow:var(--shadow-sm); padding:22px;">
        <h4 style="font-size:0.9rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin-top:0; margin-bottom:10px; color:var(--slate-800);">
          Inward Remarks & Inspection Notes
        </h4>
        <textarea name="remarks" id="inw-remarks" class="form-control" rows="5" placeholder="Finished goods received in good condition, stitching and finishing verified, ready for ironing & dispatch...">{{ old('remarks', $inward->user_remarks) }}</textarea>
      </div>

      <!-- Live Calculation Card: Yield & Contractor Payable -->
      <div class="card" style="background:linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border:1px solid #cbd5e1; border-radius:var(--radius-xl, 16px); box-shadow:var(--shadow-sm); padding:22px; display:flex; flex-direction:column; gap:10px;">
        
        <div style="font-size:0.85rem; font-weight:800; color:var(--slate-800); text-transform:uppercase; letter-spacing:0.05em; border-bottom:1px solid #cbd5e1; padding-bottom:8px; display:flex; justify-content:space-between; align-items:center;">
          <span>Inward Yield & Billing Summary</span>
          <span class="badge" id="summary-items-count" style="background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; font-size:0.75rem;">0 Items</span>
        </div>

        <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
          <span style="color:var(--slate-600);">Total Assigned in Order:</span>
          <strong id="summary-total-assigned" style="color:var(--slate-800);">0 Pcs</strong>
        </div>

        <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
          <span style="color:var(--slate-600);">Other Receipts (Excl. this):</span>
          <strong id="summary-prev-received" style="color:#059669;">0 Pcs</strong>
        </div>

        <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
          <span style="color:var(--slate-600); font-weight:700;">Good Pcs Inwarded Now:</span>
          <strong id="summary-good-inward" style="color:#059669; font-size:0.95rem; font-weight:800;">0 Pcs</strong>
        </div>

        <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
          <span style="color:var(--slate-600);">QC Defect / Rejected:</span>
          <strong id="summary-defect-inward" style="color:#dc2626;">0 Pcs</strong>
        </div>

        <div style="display:flex; justify-content:space-between; font-size:0.85rem; border-top:1px dashed #cbd5e1; padding-top:6px;">
          <span style="color:var(--slate-600); font-weight:700;">Net Balance Remaining After Inward:</span>
          <strong id="summary-remaining-pending" style="color:#dc2626; font-size:1.05rem; font-weight:800;">0 Pcs</strong>
        </div>

        <div style="border-top:2px dashed #cbd5e1; padding-top:10px; display:flex; justify-content:space-between; align-items:baseline; font-size:1.15rem;">
          <span style="font-weight:800; color:var(--slate-900);">Contractor Payable:</span>
          <span style="font-weight:800; color:#059669;" id="summary-grand">₹{{ number_format($inward->total_amount, 2) }}</span>
        </div>

      </div>

    </div>

    <!-- Bottom Action Bar -->
    <div style="display:flex; justify-content:flex-end; align-items:center; gap:12px; padding:16px 24px; background:#ffffff; border:1px solid var(--slate-200, #e2e8f0); border-radius:var(--radius-xl, 16px); box-shadow:var(--shadow-sm); margin-top:4px;">
      <a href="{{ route('jobwork.inward.index') }}" class="btn btn-secondary" style="font-weight:700; padding:10px 20px;">Cancel</a>
      <button type="submit" id="save-inward-btn-bottom" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:8px; font-weight:700; box-shadow:0 2px 8px rgba(5, 150, 105, 0.3); background:#059669; border-color:#059669; padding:10px 24px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Update Inward & Stock
      </button>
    </div>

  </div>
</form>

@endsection

@push('scripts')
<script>
  const masterItems = @json($items);
  const formattedAssignmentsMap = @json($formattedAssignments);
  const assignmentsByWorkerMap = @json($assignmentsByWorker);
  const existingInwardData = @json($inward);
  const existingInwardItems = @json($inward->items_list);
  const initialAssignmentId = @json($inward->job_assignment_id);
  const initialWorkerId = @json($inward->job_worker_id);

  let currentAssignmentData = null;
  let itemsData = [];

  function autoGenerateChallanNo() {
    const input = document.getElementById('challan_no');
    if (!input) return;

    const now = new Date();
    const year = now.getFullYear();
    const rand = Math.floor(1000 + Math.random() * 9000);

    const genNo = 'JDC-' + year + '-' + rand;
    input.value = genNo;

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

  // 1. When Job Worker is selected
  function onWorkerSelectChange(workerSelect) {
    const workerId = workerSelect.value;
    const jaSelect = document.getElementById('ja_select');
    if (!jaSelect) return;

    jaSelect.innerHTML = '';

    if (!workerId) {
      jaSelect.innerHTML = '<option value="">-- First select a Job Worker above --</option>';
      resetJobOrderView();
      return;
    }

    const wKey = String(workerId);
    const workerAssignments = assignmentsByWorkerMap[wKey] || [];

    if (workerAssignments.length === 0) {
      jaSelect.innerHTML = '<option value="">-- No active job orders found for this contractor --</option>';
      resetJobOrderView();
      return;
    }

    jaSelect.innerHTML = '<option value="">-- Select Active Job Order / Lot --</option>';
    workerAssignments.forEach(ja => {
      const opt = document.createElement('option');
      opt.value = ja.id;
      const isSel = (ja.id == initialAssignmentId);
      opt.textContent = `Lot: ${ja.lot_number} | ${ja.job_order_no} — ${ja.style_name} (${ja.total_remaining_qty} Pcs Pending / Assigned: ${ja.total_assigned_qty})`;
      if (isSel) opt.selected = true;
      jaSelect.appendChild(opt);
    });

    if (jaSelect.value) {
      onJobOrderChange(jaSelect);
    } else if (workerAssignments.length === 1) {
      jaSelect.selectedIndex = 1;
      onJobOrderChange(jaSelect);
    } else {
      resetJobOrderView();
    }
  }

  function resetJobOrderView() {
    currentAssignmentData = null;
    document.getElementById('pv-worker').innerText = '—';
    document.getElementById('pv-process').innerText = '—';
    document.getElementById('pv-lot').innerText = '—';
    document.getElementById('pv-style').innerText = '—';
    document.getElementById('pv-issued').innerText = '0 Pcs';
    document.getElementById('pv-received').innerText = '0 Pcs';
    document.getElementById('pv-pending').innerText = '0 Pcs';

    itemsData = [];
    renderAllItems();
    calculateOverallTotals();
  }

  // 2. When Job Assignment is selected
  function onJobOrderChange(jaSelect) {
    const jaId = jaSelect.value;
    if (!jaId || !formattedAssignmentsMap[jaId]) {
      resetJobOrderView();
      return;
    }

    currentAssignmentData = formattedAssignmentsMap[jaId];

    // Populate Banner Preview
    document.getElementById('pv-worker').innerText = currentAssignmentData.job_worker_name || '—';
    document.getElementById('pv-process').innerText = currentAssignmentData.process_name || '—';
    document.getElementById('pv-lot').innerText = currentAssignmentData.lot_number || '—';
    document.getElementById('pv-style').innerText = currentAssignmentData.style_name || '—';
    document.getElementById('pv-issued').innerText = currentAssignmentData.total_assigned_qty + ' Pcs';
    document.getElementById('pv-received').innerText = currentAssignmentData.total_previously_received_qty + ' Pcs';
    document.getElementById('pv-pending').innerText = currentAssignmentData.total_remaining_qty + ' Pcs';

    // Populate items Data from Job Assignment & existing Inward values
    itemsData = [];
    (currentAssignmentData.items || []).forEach(it => {
      // Find matching saved item from this existing inward if matching assignment
      let matchedSavedItem = null;
      if (jaId == initialAssignmentId && Array.isArray(existingInwardItems)) {
        matchedSavedItem = existingInwardItems.find(ei => {
          if (it.job_assignment_item_id && ei.job_assignment_item_id && ei.job_assignment_item_id == it.job_assignment_item_id) return true;
          if (it.item_id && ei.item_id && ei.item_id == it.item_id) return true;
          if (it.item_name && ei.item_name && ei.item_name.toLowerCase().trim() === it.item_name.toLowerCase().trim()) return true;
          return false;
        });
      }

      let savedInwardQty = matchedSavedItem ? (parseInt(matchedSavedItem.received_qty) || 0) : (it.remaining_qty > 0 ? it.remaining_qty : 0);
      let savedDefectQty = matchedSavedItem ? (parseInt(matchedSavedItem.defect_qty) || 0) : 0;
      let savedRate = matchedSavedItem && matchedSavedItem.rate ? parseFloat(matchedSavedItem.rate) : (it.rate || currentAssignmentData.rate_per_piece || 0);

      let remToDistribute = savedInwardQty;
      let thansList = [];

      (it.assigned_thans || []).forEach((at, atIdx) => {
        let thInward = 0;
        const thRem = parseInt(at.remaining_pcs) || 0;

        // Check if there's a saved than in existing inward item
        let savedThanPcs = null;
        if (matchedSavedItem && Array.isArray(matchedSavedItem.assigned_thans)) {
          const matchedThan = matchedSavedItem.assigned_thans.find(st => {
            if (at.unique_id && st.unique_id && st.unique_id == at.unique_id) return true;
            if (at.than_key && st.than_key && st.than_key == at.than_key) return true;
            if (at.than_no && st.than_no && st.than_no == at.than_no) return true;
            return false;
          });
          if (matchedThan) {
            savedThanPcs = parseInt(matchedThan.received_pcs) || 0;
          }
        }

        if (savedThanPcs !== null) {
          thInward = savedThanPcs;
        } else if (remToDistribute > 0 && thRem > 0) {
          thInward = Math.min(remToDistribute, thRem);
          remToDistribute -= thInward;
        }

        thansList.push({
          than_key: at.than_key || ('jai_' + it.job_assignment_item_id + '_than_' + (atIdx + 1)),
          than_no: at.than_no || (atIdx + 1),
          unique_id: at.unique_id || '',
          challan_no: at.challan_no || '',
          po_number: at.po_number || '',
          purchase_than_no: at.purchase_than_no || '',
          meter: parseFloat(at.meter) || 0,
          usable: parseFloat(at.usable) || parseFloat(at.meter) || 0,
          assigned_pcs: parseInt(at.assigned_pcs) || 0,
          previously_received_pcs: parseInt(at.previously_received_pcs) || 0,
          remaining_pcs: thRem,
          current_inward_pcs: thInward,
          defect_pcs: 0,
          status: at.status || 'Pending'
        });
      });

      itemsData.push({
        job_assignment_item_id: it.job_assignment_item_id || '',
        raw_item_id: it.raw_item_id || '',
        raw_item_name: it.raw_item_name || '',
        finished_item_id: it.finished_item_id || '',
        finished_item_name: it.finished_item_name || '',
        item_id: it.item_id || '',
        item_name: it.item_name || currentAssignmentData.style_name,
        item_code: it.item_code || '',
        unit: 'Pcs',
        fabric_unit: 'Meter',
        rate: savedRate,
        assigned_qty: it.assigned_qty || 0,
        previously_received_qty: it.previously_received_qty || 0,
        remaining_qty: it.remaining_qty || 0,
        current_inward_qty: savedInwardQty,
        defect_qty: savedDefectQty,
        status: it.status || 'Pending',
        assigned_thans: thansList,
        thans: [],
        showThans: false
      });
    });

    renderAllItems();
    calculateOverallTotals();
  }

  // 3. Render Assigned Items
  function renderAllItems() {
    const container = document.getElementById('items-container');
    if (!container) return;

    if (!currentAssignmentData || itemsData.length === 0) {
      container.innerHTML = `
        <div style="text-align:center; padding:32px 18px; color:#64748b; border:2px dashed #cbd5e1; border-radius:16px; background:#ffffff;">
          <div style="font-weight:700; font-size:0.95rem; color:#475569; margin-bottom:4px;">No Job Assignment Selected</div>
          <p style="font-size:0.8rem; color:#94a3b8; margin:0;">Please select a <strong>Job Worker</strong> and <strong>Job Assignment</strong> above to load assigned items and quantities.</p>
        </div>
      `;
      return;
    }

    container.innerHTML = '';

    itemsData.forEach((item, itemIdx) => {
      const isCompleted = item.remaining_qty <= 0 && item.previously_received_qty > 0 && (parseFloat(item.current_inward_qty) || 0) <= 0;
      const isPartial = item.previously_received_qty > 0 && item.remaining_qty > 0;

      let statusBadge = '';
      if (isCompleted) {
        statusBadge = `<span class="tracking-badge status-completed">✓ Completed (${item.previously_received_qty} Pcs Received)</span>`;
      } else if (isPartial) {
        statusBadge = `<span class="tracking-badge status-partial">⚡ Partially Received (${item.previously_received_qty} Rec / ${item.remaining_qty} Pending)</span>`;
      } else {
        statusBadge = `<span class="tracking-badge status-pending">⏳ Pending (${item.assigned_qty} Pcs Assigned)</span>`;
      }

      const newRemaining = Math.max(0, item.remaining_qty - (parseFloat(item.current_inward_qty) || 0));
      const calcQty = (parseFloat(item.current_inward_qty) || 0);
      const lineTotal = Math.round(calcQty * (item.rate || 0) * 100) / 100;
      const totalFabricMeters = (item.assigned_thans || []).reduce((a, b) => a + (parseFloat(b.meter) || 0), 0);
      const totalThanCount = (item.assigned_thans || []).length;

      const card = document.createElement('div');
      card.className = `inward-item-card ${isCompleted ? 'completed-item' : ''}`;
      card.id = `inward-item-card-${itemIdx}`;

      // Build Than-wise Table Rows
      let thansTableRowsHtml = '';
      (item.assigned_thans || []).forEach((th, tIdx) => {
        const isThanCompleted = th.remaining_pcs <= 0 && th.previously_received_pcs > 0 && (parseInt(th.current_inward_pcs) || 0) <= 0;
        const isThanPartial = th.previously_received_pcs > 0 && th.remaining_pcs > 0;
        const thNewRemaining = Math.max(0, th.remaining_pcs - (parseInt(th.current_inward_pcs) || 0));

        let thBadge = '';
        if (isThanCompleted) {
          thBadge = `<span style="font-size:0.7rem; font-weight:800; background:#ecfdf5; color:#059669; padding:2px 7px; border-radius:5px; border:1px solid #a7f3d0;">✓ Completed</span>`;
        } else if (isThanPartial) {
          thBadge = `<span style="font-size:0.7rem; font-weight:800; background:#eff6ff; color:#1d4ed8; padding:2px 7px; border-radius:5px; border:1px solid #bfdbfe;">⚡ Partial</span>`;
        } else {
          thBadge = `<span style="font-size:0.7rem; font-weight:800; background:#f1f5f9; color:#475569; padding:2px 7px; border-radius:5px; border:1px solid #cbd5e1;">⏳ Pending</span>`;
        }

        thansTableRowsHtml += `
          <tr id="than-row-${itemIdx}-${tIdx}" style="border-bottom:1px solid #f1f5f9; ${isThanCompleted ? 'background:#f8fafc;' : ''}">
            <td style="padding:10px 12px; font-weight:700; color:var(--slate-800);">
              <div style="font-weight:800; color:#1e293b;">Than #${th.than_no}</div>
              ${th.challan_no ? `<div style="font-size:0.72rem; color:#64748b; font-family:monospace;">Challan: ${th.challan_no}</div>` : ''}
              ${th.purchase_than_no ? `<div style="font-size:0.7rem; color:#94a3b8;">Ref Than #${th.purchase_than_no}</div>` : ''}
              <input type="hidden" name="items[${itemIdx}][assigned_thans][${tIdx}][than_key]" value="${th.than_key}">
              <input type="hidden" name="items[${itemIdx}][assigned_thans][${tIdx}][than_no]" value="${th.than_no}">
              <input type="hidden" name="items[${itemIdx}][assigned_thans][${tIdx}][unique_id]" value="${th.unique_id || ''}">
              <input type="hidden" name="items[${itemIdx}][assigned_thans][${tIdx}][challan_no]" value="${th.challan_no || ''}">
              <input type="hidden" name="items[${itemIdx}][assigned_thans][${tIdx}][po_number]" value="${th.po_number || ''}">
              <input type="hidden" name="items[${itemIdx}][assigned_thans][${tIdx}][purchase_than_no]" value="${th.purchase_than_no || ''}">
              <input type="hidden" name="items[${itemIdx}][assigned_thans][${tIdx}][meter]" value="${th.meter}">
              <input type="hidden" name="items[${itemIdx}][assigned_thans][${tIdx}][usable]" value="${th.usable}">
              <input type="hidden" name="items[${itemIdx}][assigned_thans][${tIdx}][assigned_pcs]" value="${th.assigned_pcs}">
              <input type="hidden" name="items[${itemIdx}][assigned_thans][${tIdx}][previously_received_pcs]" value="${th.previously_received_pcs}">
            </td>
            <td style="padding:10px 12px; text-align:right; font-family:var(--font-mono, monospace); font-weight:700; color:#475569;">
              <div>${th.meter.toFixed(2)} Mtr</div>
              <div style="font-size:0.7rem; color:#475569;">Usable: <strong>${th.usable.toFixed(2)}m</strong></div>
            </td>
            <td style="padding:10px 12px; text-align:right; font-family:var(--font-mono, monospace); font-weight:800; color:#4338ca;">
              ${th.assigned_pcs} Pcs
            </td>
            <td style="padding:10px 12px; text-align:right; font-family:var(--font-mono, monospace); font-weight:800; color:#059669;">
              ${th.previously_received_pcs} Pcs
            </td>
            <td style="padding:10px 12px; text-align:right; font-family:var(--font-mono, monospace); font-weight:800; color:#dc2626;">
              ${th.remaining_pcs} Pcs
            </td>
            <td style="padding:8px 12px; text-align:center;">
              <div style="position:relative; max-width:130px; margin:0 auto;">
                <input type="number" step="1" min="0" max="${th.remaining_pcs}" 
                  id="than-inward-input-${itemIdx}-${tIdx}"
                  name="items[${itemIdx}][assigned_thans][${tIdx}][received_pcs]" 
                  class="form-control" 
                  value="${th.current_inward_pcs}" 
                  style="font-weight:800; color:#059669; font-size:1rem; text-align:center; height:36px; border-color:#6ee7b7; background:#f0fdf4;"
                  oninput="onThanInwardPcsChange(${itemIdx}, ${tIdx}, this.value)"
                  onfocus="this.select()">
              </div>
            </td>
            <td style="padding:10px 12px; text-align:right; font-family:var(--font-mono, monospace); font-weight:800;" id="than-new-remaining-${itemIdx}-${tIdx}">
              <span style="color:${thNewRemaining === 0 ? '#059669' : '#b45309'};">${thNewRemaining} Pcs</span>
              <div style="font-size:0.7rem; font-weight:700; color:${thNewRemaining === 0 ? '#059669' : '#64748b'};">
                ${thNewRemaining === 0 ? '✓ Cleared' : 'Pending'}
              </div>
            </td>
            <td style="padding:10px 12px; text-align:center;">
              ${thBadge}
            </td>
          </tr>
        `;
      });

      card.innerHTML = `
        <!-- Item Header -->
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--slate-100, #f1f5f9); padding-bottom:12px; margin-bottom:16px; flex-wrap:wrap; gap:10px;">
          <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
            <span style="width:28px; height:28px; border-radius:8px; background:#ecfdf5; color:#059669; display:inline-flex; align-items:center; justify-content:center; font-size:0.85rem; font-weight:800;">
              #${itemIdx + 1}
            </span>
            <div>
              <div style="font-weight:800; font-size:1.05rem; color:var(--slate-900);">
                ${item.item_name} ${item.item_code ? `<span style="font-family:monospace; font-size:0.8rem; font-weight:600; color:#64748b;">[${item.item_code}]</span>` : ''}
              </div>
              ${item.raw_item_name ? `<div style="font-size:0.75rem; color:#64748b;">Raw Material Source: <strong>${item.raw_item_name}</strong></div>` : ''}
            </div>
            ${statusBadge}
          </div>

          <!-- Quantity Overview Pills -->
          <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <span class="tracking-badge assigned" title="Total pieces and fabric assigned in Job Order">
              Assigned: <strong>${item.assigned_qty} Pcs</strong> (${totalFabricMeters.toFixed(1)} Mtr Fabric)
            </span>
            <span class="tracking-badge received" title="Total pieces previously received across other receipts">
              Other Rec: <strong>${item.previously_received_qty} Pcs</strong>
            </span>
            <span class="tracking-badge remaining" title="Pending pieces available to receive">
              Pending: <strong>${item.remaining_qty} Pcs</strong>
            </span>
            <span class="tracking-badge" style="background:#f1f5f9; color:#1e293b; border:1px solid #cbd5e1;" id="item-line-total-badge-${itemIdx}">
              ₹${lineTotal.toFixed(2)}
            </span>
          </div>
        </div>

        <input type="hidden" name="items[${itemIdx}][job_assignment_item_id]" value="${item.job_assignment_item_id || ''}">
        <input type="hidden" name="items[${itemIdx}][item_id]" value="${item.item_id || ''}">
        <input type="hidden" name="items[${itemIdx}][item_name]" value="${item.item_name || ''}">
        <input type="hidden" name="items[${itemIdx}][item_code]" value="${item.item_code || ''}">
        <input type="hidden" name="items[${itemIdx}][unit]" value="Pcs">
        <input type="hidden" name="items[${itemIdx}][assigned_qty]" value="${item.assigned_qty}">
        <input type="hidden" name="items[${itemIdx}][previously_received_qty]" value="${item.previously_received_qty}">

        <!-- Than-wise Piece Tracking Breakdown Table -->
        <div style="margin-bottom:18px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:14px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; flex-wrap:wrap; gap:8px;">
            <span style="font-size:0.8rem; font-weight:800; color:#1e293b; text-transform:uppercase; letter-spacing:0.04em; display:flex; align-items:center; gap:6px;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
              Assigned Than Breakdown & Piece Verification (${totalThanCount} ${totalThanCount === 1 ? 'Than' : 'Thans'})
            </span>
            <div style="display:flex; gap:6px;">
              <button type="button" class="btn btn-secondary btn-xs" onclick="receiveAllThansForItem(${itemIdx})" style="background:#fff; font-weight:700; color:#059669; border-color:#a7f3d0;" title="Receive all remaining pieces for all thans">
                ✓ Receive All Pending
              </button>
              <button type="button" class="btn btn-secondary btn-xs" onclick="clearAllThansForItem(${itemIdx})" style="background:#fff; font-weight:700; color:#ef4444; border-color:#fecaca;" title="Set current inward to zero">
                ✕ Clear
              </button>
            </div>
          </div>

          <div class="table-responsive" style="margin:0; background:#fff; border-radius:8px; border:1px solid #e2e8f0; overflow-x:auto;">
            <table class="data-table" style="width:100%; margin:0; font-size:0.825rem;">
              <thead style="background:#f1f5f9;">
                <tr>
                  <th style="padding:8px 12px; font-size:0.75rem; text-transform:uppercase;">Than # / Challan</th>
                  <th style="padding:8px 12px; font-size:0.75rem; text-transform:uppercase; text-align:right;">Than Fabric</th>
                  <th style="padding:8px 12px; font-size:0.75rem; text-transform:uppercase; text-align:right;">Assigned Pcs</th>
                  <th style="padding:8px 12px; font-size:0.75rem; text-transform:uppercase; text-align:right;">Other Receipts</th>
                  <th style="padding:8px 12px; font-size:0.75rem; text-transform:uppercase; text-align:right;">Pending Pcs</th>
                  <th style="padding:8px 12px; font-size:0.75rem; text-transform:uppercase; text-align:center; min-width:130px;">Current Inward (Pcs)</th>
                  <th style="padding:8px 12px; font-size:0.75rem; text-transform:uppercase; text-align:right;">Balance After Inward</th>
                  <th style="padding:8px 12px; font-size:0.75rem; text-transform:uppercase; text-align:center;">Than Status</th>
                </tr>
              </thead>
              <tbody>
                ${thansTableRowsHtml}
              </tbody>
            </table>
          </div>
        </div>

        <!-- Overall Item Quantity Inputs Row -->
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(190px, 1fr)); gap:16px; align-items:flex-end;">
          
          <!-- Total Current Inward Qty (Pcs) -->
          <div class="form-group" style="margin-bottom:0;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
              <label class="form-label" style="font-weight:800; color:#059669; font-size:0.825rem; margin:0;">
                Total Inward (Pcs) <span style="color:#ef4444;">*</span>
              </label>
              <span style="font-size:0.7rem; color:#64748b;">Max: <strong>${item.remaining_qty} Pcs</strong></span>
            </div>
            <div style="position:relative;">
              <input type="number" step="1" min="0" max="${item.remaining_qty}" 
                id="inward-qty-input-${itemIdx}"
                name="items[${itemIdx}][received_qty]" 
                class="form-control" 
                value="${item.current_inward_qty}" 
                style="font-weight:800; color:#059669; font-size:1.05rem; height:42px; border-color:#6ee7b7; background:#f0fdf4;"
                oninput="onItemInwardQtyChange(${itemIdx}, this.value)"
                onfocus="this.select()">
              <span style="position:absolute; right:10px; top:50%; transform:translateY(-50%); font-size:0.75rem; font-weight:700; color:#059669;">
                Pcs
              </span>
            </div>
            <div id="inward-qty-error-${itemIdx}" style="display:none; color:#dc2626; font-size:0.72rem; font-weight:700; margin-top:3px;"></div>
          </div>

          <!-- New Remaining Balance Live Display -->
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; color:#475569; font-size:0.825rem; margin-bottom:4px;">
              Balance After Inward
            </label>
            <div id="new-remaining-display-${itemIdx}" style="height:42px; background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:8px; display:flex; align-items:center; justify-content:space-between; padding:0 12px; font-weight:800; font-size:0.95rem; color:${newRemaining === 0 ? '#059669' : '#b45309'};">
              <span>${newRemaining} Pcs</span>
              <span style="font-size:0.725rem; font-weight:700; color:${newRemaining === 0 ? '#059669' : '#64748b'};">
                ${newRemaining === 0 ? '✓ Fully Cleared' : 'Pending'}
              </span>
            </div>
          </div>

          <!-- QC Defect / Rejection (Pcs) -->
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; color:#dc2626; font-size:0.825rem; margin-bottom:4px;">
              QC Defect (Pcs)
            </label>
            <input type="number" step="1" min="0" name="items[${itemIdx}][defect_qty]" 
              class="form-control" 
              value="${item.defect_qty || 0}" 
              style="font-weight:700; color:#dc2626; height:42px;" 
              oninput="onItemDefectQtyChange(${itemIdx}, this.value)">
          </div>

          <!-- Contractor Rate (₹/Pc) -->
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800); font-size:0.825rem; margin-bottom:4px;">
              Contractor Rate (₹) <span style="color:#ef4444;">*</span>
            </label>
            <div style="position:relative;">
              <span style="position:absolute; left:10px; top:50%; transform:translateY(-50%); font-weight:700; color:#64748b;">₹</span>
              <input type="number" step="0.5" min="0" name="items[${itemIdx}][rate]" 
                class="form-control" 
                required 
                value="${parseFloat(item.rate || 0).toFixed(2)}" 
                style="padding-left:24px; font-weight:700; height:42px;" 
                oninput="onItemRateChange(${itemIdx}, this.value)">
            </div>
          </div>

        </div>
      `;

      container.appendChild(card);
    });
  }

  // When an individual Than's inward pieces are modified
  function onThanInwardPcsChange(itemIdx, tIdx, val) {
    const item = itemsData[itemIdx];
    if (!item || !item.assigned_thans || !item.assigned_thans[tIdx]) return;

    const th = item.assigned_thans[tIdx];
    let num = parseInt(val);
    if (isNaN(num) || num < 0) num = 0;

    // Clamping to than remaining
    if (num > th.remaining_pcs) {
      num = th.remaining_pcs;
      const input = document.getElementById(`than-inward-input-${itemIdx}-${tIdx}`);
      if (input) input.value = num;
    }

    th.current_inward_pcs = num;

    // Update Than's live balance display
    const thNewRem = Math.max(0, th.remaining_pcs - num);
    const thRemDisp = document.getElementById(`than-new-remaining-${itemIdx}-${tIdx}`);
    if (thRemDisp) {
      thRemDisp.innerHTML = `
        <span style="color:${thNewRem === 0 ? '#059669' : '#b45309'};">${thNewRem} Pcs</span>
        <div style="font-size:0.7rem; font-weight:700; color:${thNewRem === 0 ? '#059669' : '#64748b'};">
          ${thNewRem === 0 ? '✓ Cleared' : 'Pending'}
        </div>
      `;
    }

    // Recompute Item Overall Current Inward Qty as sum of Thans
    let totalThanInward = 0;
    item.assigned_thans.forEach(t => {
      totalThanInward += (parseInt(t.current_inward_pcs) || 0);
    });

    item.current_inward_qty = totalThanInward;

    // Update Item input
    const itemInp = document.getElementById(`inward-qty-input-${itemIdx}`);
    if (itemInp) itemInp.value = totalThanInward;

    // Update Item New Remaining Display
    const itemNewRem = Math.max(0, item.remaining_qty - totalThanInward);
    const itemRemDisp = document.getElementById(`new-remaining-display-${itemIdx}`);
    if (itemRemDisp) {
      itemRemDisp.innerHTML = `
        <span>${itemNewRem} Pcs</span>
        <span style="font-size:0.725rem; font-weight:700; color:${itemNewRem === 0 ? '#059669' : '#64748b'};">
          ${itemNewRem === 0 ? '✓ Fully Cleared' : 'Pending'}
        </span>
      `;
      itemRemDisp.style.color = itemNewRem === 0 ? '#059669' : '#b45309';
    }

    // Update Line Total
    const lineTotal = Math.round(totalThanInward * (item.rate || 0) * 100) / 100;
    const badge = document.getElementById(`item-line-total-badge-${itemIdx}`);
    if (badge) badge.textContent = `₹${lineTotal.toFixed(2)}`;

    calculateOverallTotals();
  }

  // When Item-level total inward input is modified directly
  function onItemInwardQtyChange(itemIdx, val) {
    const item = itemsData[itemIdx];
    if (!item) return;

    let num = parseFloat(val);
    if (isNaN(num) || num < 0) num = 0;

    const errorDisp = document.getElementById(`inward-qty-error-${itemIdx}`);

    // Validation: Cannot exceed remaining
    if (num > item.remaining_qty) {
      if (errorDisp) {
        errorDisp.textContent = `Cannot exceed pending balance of ${item.remaining_qty} Pcs!`;
        errorDisp.style.display = 'block';
      }
      num = item.remaining_qty;
      const input = document.getElementById(`inward-qty-input-${itemIdx}`);
      if (input) input.value = num;
    } else {
      if (errorDisp) errorDisp.style.display = 'none';
    }

    item.current_inward_qty = num;

    // Distribute across assigned thans sequentially
    let remToDistribute = num;
    (item.assigned_thans || []).forEach((th, tIdx) => {
      let thInward = 0;
      if (remToDistribute > 0 && th.remaining_pcs > 0) {
        thInward = Math.min(remToDistribute, th.remaining_pcs);
        remToDistribute -= thInward;
      }
      th.current_inward_pcs = thInward;

      const thInput = document.getElementById(`than-inward-input-${itemIdx}-${tIdx}`);
      if (thInput) thInput.value = thInward;

      const thNewRem = Math.max(0, th.remaining_pcs - thInward);
      const thRemDisp = document.getElementById(`than-new-remaining-${itemIdx}-${tIdx}`);
      if (thRemDisp) {
        thRemDisp.innerHTML = `
          <span style="color:${thNewRem === 0 ? '#059669' : '#b45309'};">${thNewRem} Pcs</span>
          <div style="font-size:0.7rem; font-weight:700; color:${thNewRem === 0 ? '#059669' : '#64748b'};">
            ${thNewRem === 0 ? '✓ Cleared' : 'Pending'}
          </div>
        `;
      }
    });

    // Update Live New Remaining Display
    const newRemaining = Math.max(0, item.remaining_qty - num);
    const remDisp = document.getElementById(`new-remaining-display-${itemIdx}`);
    if (remDisp) {
      remDisp.innerHTML = `
        <span>${newRemaining} Pcs</span>
        <span style="font-size:0.725rem; font-weight:700; color:${newRemaining === 0 ? '#059669' : '#64748b'};">
          ${newRemaining === 0 ? '✓ Fully Cleared' : 'Pending'}
        </span>
      `;
      remDisp.style.color = newRemaining === 0 ? '#059669' : '#b45309';
    }

    // Update Line Total
    const lineTotal = Math.round(num * (item.rate || 0) * 100) / 100;
    const badge = document.getElementById(`item-line-total-badge-${itemIdx}`);
    if (badge) badge.textContent = `₹${lineTotal.toFixed(2)}`;

    calculateOverallTotals();
  }

  function receiveAllThansForItem(itemIdx) {
    const item = itemsData[itemIdx];
    if (!item) return;

    (item.assigned_thans || []).forEach((th, tIdx) => {
      th.current_inward_pcs = th.remaining_pcs;

      const thInput = document.getElementById(`than-inward-input-${itemIdx}-${tIdx}`);
      if (thInput) thInput.value = th.remaining_pcs;

      const thRemDisp = document.getElementById(`than-new-remaining-${itemIdx}-${tIdx}`);
      if (thRemDisp) {
        thRemDisp.innerHTML = `
          <span style="color:#059669;">0 Pcs</span>
          <div style="font-size:0.7rem; font-weight:700; color:#059669;">✓ Cleared</div>
        `;
      }
    });

    item.current_inward_qty = item.remaining_qty;

    const itemInp = document.getElementById(`inward-qty-input-${itemIdx}`);
    if (itemInp) itemInp.value = item.remaining_qty;

    const remDisp = document.getElementById(`new-remaining-display-${itemIdx}`);
    if (remDisp) {
      remDisp.innerHTML = `
        <span>0 Pcs</span>
        <span style="font-size:0.725rem; font-weight:700; color:#059669;">✓ Fully Cleared</span>
      `;
      remDisp.style.color = '#059669';
    }

    const lineTotal = Math.round(item.remaining_qty * (item.rate || 0) * 100) / 100;
    const badge = document.getElementById(`item-line-total-badge-${itemIdx}`);
    if (badge) badge.textContent = `₹${lineTotal.toFixed(2)}`;

    calculateOverallTotals();
  }

  function clearAllThansForItem(itemIdx) {
    const item = itemsData[itemIdx];
    if (!item) return;

    (item.assigned_thans || []).forEach((th, tIdx) => {
      th.current_inward_pcs = 0;

      const thInput = document.getElementById(`than-inward-input-${itemIdx}-${tIdx}`);
      if (thInput) thInput.value = 0;

      const thRemDisp = document.getElementById(`than-new-remaining-${itemIdx}-${tIdx}`);
      if (thRemDisp) {
        thRemDisp.innerHTML = `
          <span style="color:#b45309;">${th.remaining_pcs} Pcs</span>
          <div style="font-size:0.7rem; font-weight:700; color:#64748b;">Pending</div>
        `;
      }
    });

    item.current_inward_qty = 0;

    const itemInp = document.getElementById(`inward-qty-input-${itemIdx}`);
    if (itemInp) itemInp.value = 0;

    const remDisp = document.getElementById(`new-remaining-display-${itemIdx}`);
    if (remDisp) {
      remDisp.innerHTML = `
        <span>${item.remaining_qty} Pcs</span>
        <span style="font-size:0.725rem; font-weight:700; color:#64748b;">Pending</span>
      `;
      remDisp.style.color = '#b45309';
    }

    const badge = document.getElementById(`item-line-total-badge-${itemIdx}`);
    if (badge) badge.textContent = `₹0.00`;

    calculateOverallTotals();
  }

  function onItemDefectQtyChange(itemIdx, val) {
    const item = itemsData[itemIdx];
    if (!item) return;
    const num = parseInt(val);
    item.defect_qty = !isNaN(num) && num >= 0 ? num : 0;
    calculateOverallTotals();
  }

  function onItemRateChange(itemIdx, val) {
    const item = itemsData[itemIdx];
    if (!item) return;
    const num = parseFloat(val);
    item.rate = !isNaN(num) && num >= 0 ? num : 0;

    const calcQty = (parseFloat(item.current_inward_qty) || 0);
    const lineTotal = Math.round(calcQty * item.rate * 100) / 100;
    const badge = document.getElementById(`item-line-total-badge-${itemIdx}`);
    if (badge) badge.textContent = `₹${lineTotal.toFixed(2)}`;

    calculateOverallTotals();
  }

  // 4. Overall Totals Calculation
  function calculateOverallTotals() {
    let grandAssigned = 0;
    let grandPrevReceived = 0;
    let grandGoodQty = 0;
    let grandDefectQty = 0;
    let grandContractorAmount = 0;

    itemsData.forEach(item => {
      const inwardQty = parseFloat(item.current_inward_qty) || 0;
      const defectQty = parseInt(item.defect_qty) || 0;
      const rate = parseFloat(item.rate) || 0;
      const itemTotal = Math.round(inwardQty * rate * 100) / 100;

      grandAssigned += (parseInt(item.assigned_qty) || 0);
      grandPrevReceived += (parseInt(item.previously_received_qty) || 0);
      grandGoodQty += inwardQty;
      grandDefectQty += defectQty;
      grandContractorAmount += itemTotal;
    });

    const totalRemainingPending = Math.max(0, grandAssigned - (grandPrevReceived + grandGoodQty));

    const sItemsCount = document.getElementById('summary-items-count');
    const sAssigned = document.getElementById('summary-total-assigned');
    const sPrevReceived = document.getElementById('summary-prev-received');
    const sGood = document.getElementById('summary-good-inward');
    const sDefect = document.getElementById('summary-defect-inward');
    const sPending = document.getElementById('summary-remaining-pending');
    const sGrand = document.getElementById('summary-grand');

    if (sItemsCount) sItemsCount.textContent = `${itemsData.length} ${itemsData.length === 1 ? 'Item' : 'Items'}`;
    if (sAssigned) sAssigned.textContent = `${grandAssigned} Pcs`;
    if (sPrevReceived) sPrevReceived.textContent = `${grandPrevReceived} Pcs`;
    if (sGood) sGood.textContent = `${grandGoodQty} Pcs`;
    if (sDefect) sDefect.textContent = `${grandDefectQty} Pcs`;
    if (sPending) sPending.textContent = `${totalRemainingPending} Pcs`;
    if (sGrand) sGrand.textContent = '₹' + grandContractorAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  // 5. Submit validation
  document.getElementById('inward-form')?.addEventListener('submit', function(e) {
    if (!document.getElementById('worker_select').value) {
      e.preventDefault();
      alert('Please select a Job Worker / Contractor.');
      return false;
    }

    if (!document.getElementById('ja_select').value) {
      e.preventDefault();
      alert('Please select an Active Job Assignment.');
      return false;
    }

    if (itemsData.length === 0) {
      e.preventDefault();
      alert('No items found to inward.');
      return false;
    }

    let totalInwarding = 0;
    itemsData.forEach((item, idx) => {
      const q = parseFloat(item.current_inward_qty) || 0;
      totalInwarding += q;
    });

    if (totalInwarding <= 0) {
      e.preventDefault();
      alert('Please enter at least 1 piece in Current Inward Quantity before saving.');
      return false;
    }
  });

  // Initial Load Trigger
  document.addEventListener('DOMContentLoaded', () => {
    const workerSelect = document.getElementById('worker_select');
    if (workerSelect && workerSelect.value) {
      onWorkerSelectChange(workerSelect);
    }
  });
</script>
@endpush
