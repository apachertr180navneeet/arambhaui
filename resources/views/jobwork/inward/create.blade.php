@extends('layouts.app')

@section('title', 'New Job Inward Entry - GarmentERP')

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

  <div style="display:flex; flex-direction:column; gap:20px;">

    <!-- Top Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
      <div>
        <div style="display:flex; align-items:center; gap:10px;">
          <h2 style="margin:0; font-size:1.45rem; font-weight:800; color:var(--slate-900); letter-spacing:-0.02em;">Record Job Work Inward Receipt</h2>
          <span style="font-family:var(--font-mono, monospace); font-weight:800; font-size:0.9rem; background:#ecfdf5; color:#059669; padding:3px 10px; border-radius:8px; border:1px solid #a7f3d0;">
            {{ $nextInwardNo }}
          </span>
        </div>
        <p style="margin:4px 0 0; font-size:0.85rem; color:var(--slate-500);">
          Receive finished stitched/processed garments from contractor, record batch QC inspection, defect rejections, and update stock.
        </p>
      </div>

      <div style="display:flex; gap:10px; align-items:center;">
        <a href="{{ route('jobwork.inward.index') }}" class="btn btn-secondary" style="font-weight:700;">Cancel</a>
        <button type="submit" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:6px; font-weight:700; box-shadow:0 2px 8px rgba(5, 150, 105, 0.3); background:#059669; border-color:#059669;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
          Confirm Inward & Update Stock
        </button>
      </div>
    </div>

    <!-- Step 1: Select Active Batch / Lot / Job Order -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:24px;">
      <div style="border-bottom:1px solid var(--slate-100); padding-bottom:12px; margin-bottom:18px;">
        <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin:0; color:var(--slate-800); display:flex; align-items:center; gap:8px;">
          <span style="width:24px; height:24px; border-radius:6px; background:#eff6ff; color:#2563eb; display:inline-flex; align-items:center; justify-content:center; font-size:0.75rem;">1</span>
          Select Active Job Order / Lot / Batch <span style="color:#ef4444;">*</span>
        </h3>
      </div>

      <div class="form-group" style="margin-bottom:16px;">
        <label class="form-label" style="font-weight:700; color:var(--slate-800);">Active Job Work Order (Awaiting Return) <span style="color:#ef4444;">*</span></label>
        <select name="job_assignment_id" id="ja_select" class="form-control" required onchange="onJobOrderChange(this)" style="font-size:0.95rem; font-weight:700;">
          <option value="">-- Select Active Job Order / Lot --</option>
          @foreach($activeAssignments as $ja)
            @php
              $pend = max(0, $ja->issued_qty - $ja->received_qty - $ja->rejected_qty);
              $isSel = ($preselected && $preselected->id == $ja->id);
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
            >
              Lot: {{ $ja->lot_number }} | {{ $ja->job_order_no }} — {{ $ja->job_worker_name }} ({{ $ja->process_name }}) — {{ $pend }} Pcs Pending (Issued: {{ $ja->issued_qty }})
            </option>
          @endforeach
        </select>
      </div>

      <!-- Live Job Order Details Preview Box -->
      <div id="job-order-preview-box" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px; display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px;">
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
    </div>

    <!-- Step 2: Inward Receipt & QC Inspection Form -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:24px;">
      <div style="border-bottom:1px solid var(--slate-100); padding-bottom:12px; margin-bottom:18px;">
        <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin:0; color:var(--slate-800); display:flex; align-items:center; gap:8px;">
          <span style="width:24px; height:24px; border-radius:6px; background:#ecfdf5; color:#059669; display:inline-flex; align-items:center; justify-content:center; font-size:0.75rem;">2</span>
          Inward Receipt Quantities & Quality Check
        </h3>
      </div>

      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:18px;">
        
        <!-- Inward Date -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Inward Date <span style="color:#ef4444;">*</span></label>
          <input type="date" name="inward_date" class="form-control" required value="{{ date('Y-m-d') }}">
        </div>

        <!-- Contractor DC / Challan No -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Contractor Return DC / Challan No.</label>
          <input type="text" name="challan_no" class="form-control" placeholder="e.g. DC-984 / CH-104" style="font-weight:700;">
        </div>

        <!-- Received Good Pieces -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:#059669;">Received Good Finished Pieces <span style="color:#ef4444;">*</span></label>
          <div style="position:relative;">
            <input type="number" step="1" min="1" name="received_qty" id="inw_good_qty" class="form-control" required placeholder="0" oninput="calculateInwardBalance()" style="font-weight:800; font-size:1.05rem; color:#059669;">
            <span style="position:absolute; right:10px; top:50%; transform:translateY(-50%); font-size:0.75rem; color:#64748b; font-weight:700;">Pcs</span>
          </div>
          <small style="font-size:0.72rem; color:var(--slate-500);">Passed finished garments received into stock.</small>
        </div>

        <!-- QC Defect Rejection Pieces -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:#dc2626;">Defect / Rejected Pieces (Pcs)</label>
          <div style="position:relative;">
            <input type="number" step="1" min="0" name="defect_qty" id="inw_defect_qty" class="form-control" value="0" placeholder="0" oninput="calculateInwardBalance()" style="font-weight:700; color:#dc2626;">
            <span style="position:absolute; right:10px; top:50%; transform:translateY(-50%); font-size:0.75rem; color:#64748b; font-weight:700;">Pcs</span>
          </div>
          <small style="font-size:0.72rem; color:var(--slate-500);">Stitching defects, oil spots or damaged pieces.</small>
        </div>

        <!-- Actual Wastage Returned Meters -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Fabric Wastage Returned (Mtr)</label>
          <div style="position:relative;">
            <input type="number" step="0.01" min="0" name="wastage_returned_meters" id="inw_wastage_qty" class="form-control" value="0" placeholder="0.00" style="font-weight:700;">
            <span style="position:absolute; right:10px; top:50%; transform:translateY(-50%); font-size:0.75rem; color:#64748b; font-weight:700;">Mtr</span>
          </div>
          <small style="font-size:0.72rem; color:var(--slate-500);">Actual fabric scraps/leftover returned by contractor.</small>
        </div>

        <!-- QC Inspection Status -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Quality Inspection (QC) Status <span style="color:#ef4444;">*</span></label>
          <select name="qc_status" id="inw_qc_status" class="form-control" required>
            <option value="Passed QC" selected>Passed Quality Inspection (Ready for Dispatch)</option>
            <option value="Minor Touchup">Passed with Minor Iron/Thread Touchup</option>
            <option value="Under Lab QC">Under Lab QC Review</option>
            <option value="Rejected">Full Batch Rejected</option>
          </select>
        </div>

        <!-- Storage Location -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Destination Stock Location</label>
          <input type="text" name="storage_location" class="form-control" value="Finished Goods Stock" placeholder="e.g. Packing Hub / Store A">
        </div>

      </div>

      <!-- Live Calculation Card: Pending Balance & Contractor Payable -->
      <div style="display:flex; justify-content:flex-end; margin-top:22px;">
        <div style="width:400px; background:linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border:1px solid #cbd5e1; border-radius:14px; padding:18px; display:flex; flex-direction:column; gap:10px;">
          
          <div style="font-size:0.8rem; font-weight:800; color:var(--slate-700); text-transform:uppercase; border-bottom:1px solid #cbd5e1; padding-bottom:6px;">
            Inward Batch Yield & Contractor Billing
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
            <span style="color:var(--slate-600);">Total Job Issued:</span>
            <strong id="card-issued" style="color:var(--slate-900);">0 Pcs</strong>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
            <span style="color:var(--slate-600);">Good Pcs Inwarded Now:</span>
            <strong id="card-good-inward" style="color:#059669; font-size:0.95rem;">0 Pcs</strong>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
            <span style="color:var(--slate-600);">Defect / Rejected:</span>
            <strong id="card-defect-inward" style="color:#dc2626;">0 Pcs</strong>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.85rem; border-top:1px dashed #cbd5e1; padding-top:6px;">
            <span style="color:var(--slate-600); font-weight:700;">Remaining Balance Pending:</span>
            <strong id="card-remaining-pending" style="color:#dc2626; font-size:1rem;">0 Pcs</strong>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.85rem;">
            <span style="color:var(--slate-600);">Contractor Stitching Rate:</span>
            <strong id="card-rate" style="color:var(--slate-800);">₹0.00 / Pc</strong>
          </div>

          <div style="border-top:2px dashed #cbd5e1; padding-top:8px; display:flex; justify-content:space-between; align-items:baseline; font-size:1.15rem;">
            <span style="font-weight:800; color:var(--slate-900);">Batch Payable Amount:</span>
            <span style="font-weight:800; color:#059669;" id="card-batch-amount">₹0.00</span>
          </div>

        </div>
      </div>

    </div>

    <!-- Step 3: Remarks -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:24px;">
      <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin-top:0; margin-bottom:12px; color:var(--slate-800);">
        3. Inward Remarks & Inspection Notes
      </h3>
      <div class="form-group" style="margin-bottom:0;">
        <textarea name="remarks" class="form-control" rows="2" placeholder="Finished goods received in good condition, buttons and stitching checked, ready for ironing & packing..."></textarea>
      </div>
    </div>

  </div>
</form>

@push('scripts')
<script>
  let currentOrderData = null;

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
      document.getElementById('inw_good_qty').value = '';
      calculateInwardBalance();
      return;
    }

    currentOrderData = {
      order: opt.getAttribute('data-order'),
      lot: opt.getAttribute('data-lot'),
      worker: opt.getAttribute('data-worker'),
      process: opt.getAttribute('data-process'),
      style: opt.getAttribute('data-style'),
      issued: parseInt(opt.getAttribute('data-issued') || 0),
      received: parseInt(opt.getAttribute('data-received') || 0),
      rejected: parseInt(opt.getAttribute('data-rejected') || 0),
      pending: parseInt(opt.getAttribute('data-pending') || 0),
      rate: parseFloat(opt.getAttribute('data-rate') || 0),
      than: parseFloat(opt.getAttribute('data-than') || 0),
      wastage: parseFloat(opt.getAttribute('data-wastage') || 0),
    };

    document.getElementById('pv-worker').innerText = currentOrderData.worker;
    document.getElementById('pv-process').innerText = currentOrderData.process;
    document.getElementById('pv-lot').innerText = currentOrderData.lot;
    document.getElementById('pv-style').innerText = currentOrderData.style || 'Garment Item';
    document.getElementById('pv-issued').innerText = currentOrderData.issued + ' Pcs';
    document.getElementById('pv-received').innerText = currentOrderData.received + ' Pcs';
    document.getElementById('pv-pending').innerText = currentOrderData.pending + ' Pcs';

    const goodInput = document.getElementById('inw_good_qty');
    goodInput.max = currentOrderData.pending;
    if (!goodInput.value || parseFloat(goodInput.value) === 0) {
      goodInput.value = currentOrderData.pending;
    }

    calculateInwardBalance();
  }

  function calculateInwardBalance() {
    const goodQty = parseInt(document.getElementById('inw_good_qty').value || 0);
    const defectQty = parseInt(document.getElementById('inw_defect_qty').value || 0);

    const issued = currentOrderData ? currentOrderData.issued : 0;
    const prevReceived = currentOrderData ? currentOrderData.received : 0;
    const prevRejected = currentOrderData ? currentOrderData.rejected : 0;
    const currentPending = currentOrderData ? currentOrderData.pending : 0;
    const rate = currentOrderData ? currentOrderData.rate : 0;

    const remainingPending = Math.max(0, currentPending - goodQty - defectQty);
    const batchAmount = goodQty * rate;

    document.getElementById('card-issued').innerText = issued + ' Pcs';
    document.getElementById('card-good-inward').innerText = goodQty + ' Pcs';
    document.getElementById('card-defect-inward').innerText = defectQty + ' Pcs';
    document.getElementById('card-remaining-pending').innerText = remainingPending + ' Pcs';
    document.getElementById('card-rate').innerText = '₹' + rate.toFixed(2) + ' / Pc';
    document.getElementById('card-batch-amount').innerText = '₹' + batchAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  document.addEventListener('DOMContentLoaded', () => {
    const sel = document.getElementById('ja_select');
    if (sel && sel.value) {
      onJobOrderChange(sel);
    }
  });
</script>
@endpush
@endsection
