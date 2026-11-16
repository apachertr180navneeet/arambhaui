@extends('layouts.app')

@section('title', 'Aarambh - JOB WORK INWARD')

@section('breadcrumb')
  <div class="breadcrumb-item"><a href="{{ route('jobwork.assign.index') }}" style="color:inherit; text-decoration:none;">Job Work & Assign</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item"><a href="{{ route('jobwork.inward.index') }}" style="color:inherit; text-decoration:none;">Job Inward Entries</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Job Work Inward</span></div>
@endsection

@php
  $finishedGoods = $items->filter(function($i) {
    return stripos($i->category ?? '', 'finish') !== false 
        || stripos($i->type ?? '', 'finish') !== false 
        || in_array(strtoupper($i->unit ?? ''), ['PCS', 'PIECES', 'SETS']);
  });
  if ($finishedGoods->isEmpty()) $finishedGoods = $items;

  $rawGoods = $items->filter(function($i) {
    return stripos($i->category ?? '', 'raw') !== false 
        || stripos($i->type ?? '', 'raw') !== false 
        || stripos($i->category ?? '', 'fabric') !== false 
        || in_array(strtoupper($i->unit ?? ''), ['MTR', 'METERS', 'METER', 'KG', 'YDS']);
  });
  if ($rawGoods->isEmpty()) $rawGoods = $items;
@endphp

@push('styles')
<style>
  .jwi-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
    max-width: 1380px;
    margin: 0 auto;
  }
  .jwi-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    padding: 20px 24px;
  }
  .jwi-header-title {
    font-size: 1.35rem;
    font-weight: 800;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0 0 16px 0;
    letter-spacing: -0.01em;
  }
  .jwi-section-title {
    font-size: 0.925rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #1e293b;
    margin: 0 0 16px 0;
    display: flex;
    align-items: center;
    gap: 8px;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 10px;
  }
  .jwi-section-title .badge-num {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    background: #0284c7;
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 800;
  }
  .jwi-header-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
  }
  .jwi-item-entry-panel {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 16px 18px;
  }
  .jwi-item-entry-grid {
    display: grid;
    grid-template-columns: 2.2fr 1.1fr 44px 1fr 1.1fr auto;
    gap: 12px;
    align-items: flex-end;
  }
  .jwi-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-top: 14px;
    font-size: 0.875rem;
  }
  .jwi-table th {
    background: #f1f5f9;
    color: #334155;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.04em;
    padding: 10px 14px;
    border-top: 1px solid #e2e8f0;
    border-bottom: 1px solid #cbd5e1;
  }
  .jwi-table td {
    padding: 12px 14px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    color: #1e293b;
  }
  .jwi-table tbody tr:hover {
    background: #f8fafc;
  }
  .jwi-table tfoot td {
    background: #f8fafc;
    font-weight: 800;
    border-top: 2px solid #cbd5e1;
    border-bottom: 2px solid #cbd5e1;
    padding: 12px 14px;
    color: #0f172a;
  }
  .jwi-bottom-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 24px;
  }
  .jwi-summary-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 16px 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  .jwi-summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.95rem;
    color: #475569;
    padding-bottom: 8px;
    border-bottom: 1px dashed #e2e8f0;
  }
  .jwi-summary-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
  }
  .jwi-summary-row.total-row {
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
    border-top: 2px solid #e2e8f0;
    padding-top: 10px;
    border-bottom: none;
  }
  .btn-remove-row {
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fecaca;
    border-radius: 6px;
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-weight: 800;
    transition: all 0.15s ease;
  }
  .btn-remove-row:hover {
    background: #dc2626;
    color: #ffffff;
  }
  .btn-quick-add {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    border-radius: 6px;
    width: 38px;
    height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-weight: 800;
    font-size: 1.1rem;
    transition: all 0.15s ease;
  }
  .btn-quick-add:hover {
    background: #2563eb;
    color: #ffffff;
  }
  @media (max-width: 992px) {
    .jwi-header-grid {
      grid-template-columns: 1fr 1fr;
    }
    .jwi-item-entry-grid {
      grid-template-columns: 1fr 1fr;
    }
    .jwi-bottom-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
@endpush

@section('content')
<div class="jwi-container">

  <!-- Header Card -->
  <div class="jwi-card" style="padding-bottom: 16px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
      <div class="jwi-header-title" style="margin-bottom:0;">
        <span style="color:#0284c7;">☆</span>
        <span>Aarambh - JOB WORK INWARD</span>
      </div>
      <div style="display:flex; gap:10px; align-items:center;">
        <a href="{{ route('jobwork.inward.index') }}" class="btn btn-secondary" style="font-weight:600;">
          View All Inwards
        </a>
      </div>
    </div>

    <!-- Main Inward Form -->
    <form action="{{ route('jobwork.inward.store') }}" method="POST" id="job-inward-form" onsubmit="return validateBeforeSubmit(event)">
      @csrf

      <!-- 1. FORM HEADER FIELDS (Handwritten Sketch 2) -->
      <div class="jwi-section-title">
        <span class="badge-num">1</span>
        <span>Header Information</span>
      </div>

      <div class="jwi-header-grid">
        
        <!-- Jobworker (Master Dropdown) -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.825rem; color:#334155; margin-bottom:5px;">
            Jobworker [MASTER] <span style="color:#ef4444;">*</span>
          </label>
          <select name="job_worker_id" id="jwi-jobworker-select" class="form-control" required onchange="onJobWorkerChanged()">
            <option value="">-- Select Job worker --</option>
            @foreach($jobworkers as $jw)
              <option value="{{ $jw->id }}" 
                      data-name="{{ $jw->name }}"
                      {{ (old('job_worker_id', $preselectedWorkerId) == $jw->id) ? 'selected' : '' }}>
                {{ $jw->name }} {{ $jw->phone ? '('.$jw->phone.')' : '' }}
              </option>
            @endforeach
          </select>
          <input type="hidden" name="job_worker_name" id="jwi-jobworker-name" value="">
        </div>

        <!-- Job Inward NO. (Dropdown / Input defaulting to Auto) -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.825rem; color:#334155; margin-bottom:5px;">
            Job Inward NO. [Auto] <span style="color:#ef4444;">*</span>
          </label>
          <div style="display:flex; gap:6px;">
            <input type="text" 
                   name="inward_number" 
                   id="jwi-inward-no" 
                   class="form-control" 
                   required 
                   value="{{ old('inward_number', $nextInwardNo) }}" 
                   style="font-family:var(--font-mono, monospace); font-weight:700;">
          </div>
        </div>

        <!-- Job work order NO. (Select dropdown linked to open orders) -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.825rem; color:#334155; margin-bottom:5px;">
            Job work order NO. <span style="color:#ef4444;">*</span>
          </label>
          <select name="job_assignment_id" id="jwi-order-select" class="form-control" onchange="onJobOrderChanged()">
            <option value="">-- Select Job work order NO. --</option>
            @foreach($formattedAssignments as $jaId => $ja)
              <option value="{{ $jaId }}" 
                      data-worker-id="{{ $ja['job_worker_id'] }}"
                      data-worker-name="{{ $ja['job_worker_name'] }}"
                      data-order-no="{{ $ja['job_order_no'] }}"
                      data-rem-qty="{{ $ja['total_remaining_qty'] }}"
                      {{ (old('job_assignment_id', $preselectedId) == $jaId) ? 'selected' : '' }}>
                {{ $ja['job_order_no'] }} — {{ $ja['job_worker_name'] }} (Bal: {{ $ja['total_remaining_qty'] }} Pcs)
              </option>
            @endforeach
          </select>
          <input type="hidden" name="job_order_no" id="jwi-order-no" value="">
          <input type="hidden" name="lot_number" id="jwi-lot-number" value="">
          <input type="hidden" name="process_name" id="jwi-process-name" value="Stitching / Jobwork">
          <input type="hidden" name="challan_no" value="{{ $nextChallanNo }}">
          <input type="hidden" name="qc_status" value="Passed">
          <input type="hidden" name="storage_location" value="Main Warehouse">
        </div>

        <!-- Date (Date Picker) -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.825rem; color:#334155; margin-bottom:5px;">
            Date <span style="color:#ef4444;">*</span>
          </label>
          <input type="date" 
                 name="inward_date" 
                 id="jwi-date" 
                 class="form-control" 
                 required 
                 value="{{ old('inward_date', date('Y-m-d')) }}" 
                 style="font-weight:600;">
        </div>

      </div>
  </div>

  <!-- 2. INLINE ITEM ENTRY BAR (Handwritten Sketch 2) -->
  <div class="jwi-card" style="margin-bottom: 20px;">
    <div class="jwi-section-title">
      <span class="badge-num">2</span>
      <span>Item Inward Entry</span>
    </div>

    <div class="jwi-item-entry-panel">
      <div class="jwi-item-entry-grid">
        
        <!-- ITEM recd (Finished Goods Dropdown) -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569; margin-bottom:4px;">
            ITEM recd (Finished Goods) <span style="color:#ef4444;">*</span>
          </label>
          <select id="entry-item-recd" class="form-control" style="font-weight:600;" onchange="onRecdItemSelectChanged()">
            <option value="">-- Select Finished Goods --</option>
            @foreach($finishedGoods as $fg)
              <option value="{{ $fg->id }}" 
                      data-name="{{ $fg->name }}" 
                      data-code="{{ $fg->code }}" 
                      data-unit="{{ $fg->unit ?? 'Pcs' }}">
                {{ $fg->name }} ({{ $fg->code ?? 'N/A' }})
              </option>
            @endforeach
          </select>
          <input type="hidden" id="entry-used-raw-name" value="">
          <input type="hidden" id="entry-jai-id" value="">
          <input type="hidden" id="entry-assigned-qty" value="0">
          <input type="hidden" id="entry-prev-qty" value="0">
          <input type="hidden" id="entry-rem-balance" value="0">
        </div>

        <!-- Qty (Number input) -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569; margin-bottom:4px;">
            Qty <span style="color:#ef4444;">*</span>
          </label>
          <input type="number" 
                 step="0.01" 
                 min="0.01" 
                 id="entry-inward-qty" 
                 class="form-control" 
                 placeholder="0.00" 
                 style="font-weight:700; text-align:right;">
        </div>

        <!-- + Button (Multiple Than modal / Quick Add) -->
        <div class="form-group" style="margin-bottom:0; display:flex; align-items:center;">
          <button type="button" 
                  class="btn-quick-add" 
                  title="Add Multiple Than Breakdown" 
                  onclick="openThanModalForEntry()">
            +
          </button>
        </div>

        <!-- THAN (Number input) -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569; margin-bottom:4px;">
            THAN <span style="color:#ef4444;">*</span>
          </label>
          <input type="number" 
                 step="1" 
                 min="0" 
                 id="entry-than-count" 
                 class="form-control" 
                 placeholder="0" 
                 style="font-weight:700; text-align:center;">
        </div>

        <!-- Actual waste (Manual number input) -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569; margin-bottom:4px;">
            Actual waste
          </label>
          <input type="number" 
                 step="0.01" 
                 min="0" 
                 id="entry-actual-waste" 
                 class="form-control" 
                 placeholder="0.00" 
                 value="0.00"
                 style="font-weight:700; text-align:right;">
        </div>

        <!-- Add Item Button -->
        <div class="form-group" style="margin-bottom:0;">
          <button type="button" 
                  class="btn btn-primary" 
                  onclick="addItemToInwardTable()" 
                  style="font-weight:700; padding:9px 18px; display:inline-flex; align-items:center; gap:6px;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add Item
          </button>
        </div>

      </div>

      <!-- Live Thans preview chip line -->
      <div id="entry-than-chips-line" style="display:none; align-items:center; gap:8px; margin-top:10px; padding:6px 12px; background:#e0f2fe; border-radius:6px; font-size:0.8rem; color:#0369a1;">
        <strong>Than Breakdown:</strong>
        <span id="entry-than-chips-text"></span>
        <span style="margin-left:auto; cursor:pointer; color:#ef4444; font-weight:700;" onclick="clearEntryThans()">Clear</span>
      </div>

    </div>

    <!-- 3. LINE ITEMS TABLE (Handwritten Sketch 2) -->
    <div style="overflow-x:auto;">
      <table class="jwi-table" id="jwi-items-table">
        <thead>
          <tr>
            <th style="width:50px; text-align:center;">SNO.</th>
            <th style="width:24%;">ITEM (Recd)</th>
            <th style="width:22%;">NAME used item</th>
            <th style="width:12%; text-align:right;">Qty</th>
            <th style="width:11%; text-align:center;">Pcs / THAN</th>
            <th style="width:12%; text-align:right;">WASTAGE</th>
            <th style="width:12%; text-align:right;">Pending Qty</th>
            <th style="width:7%; text-align:center;">Actions</th>
          </tr>
        </thead>
        <tbody id="jwi-items-tbody">
          <!-- Populated dynamically by JS -->
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3" style="text-align:right; font-weight:800; text-transform:uppercase; color:#475569;">
              Total:
            </td>
            <td id="tfoot-sum-qty" style="text-align:right; color:#0284c7; font-size:1rem;">0.00</td>
            <td id="tfoot-sum-thans" style="text-align:center; color:#475569; font-size:0.95rem;">0</td>
            <td id="tfoot-sum-waste" style="text-align:right; color:#d97706; font-size:0.95rem;">0.00</td>
            <td id="tfoot-sum-pending" style="text-align:right; color:#64748b; font-size:0.95rem;">0.00</td>
            <td></td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- Empty Table State -->
    <div id="jwi-empty-banner" style="text-align:center; padding:30px 20px; color:#94a3b8; font-size:0.9rem;">
      No items added yet. Select Finished Item from above or Job Order and click <strong>Add Item</strong>.
    </div>

  </div>

  <!-- 4. BOTTOM SECTION: REMARKS & SUMMARY (Handwritten Sketch 2) -->
  <div class="jwi-card">
    <div class="jwi-bottom-grid">
      
      <!-- Remarks (Left) -->
      <div>
        <label class="form-label" style="font-weight:700; font-size:0.85rem; color:#334155; margin-bottom:6px;">
          Remarks
        </label>
        <textarea name="remarks" 
                  id="jwi-remarks" 
                  rows="4" 
                  class="form-control" 
                  placeholder="Enter inward remarks, QC notes, delivery details, etc."></textarea>
      </div>

      <!-- Right Summary Card (Sketch 2) -->
      <div class="jwi-summary-card">
        <div style="font-weight:800; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.04em; color:#64748b; border-bottom:1px solid #f1f5f9; padding-bottom:6px;">
          Inward Summary
        </div>

        <div class="jwi-summary-row">
          <span>- T Recd Item</span>
          <span style="font-weight:800; color:#0284c7; font-size:1.05rem;" id="summary-total-recd">0.00</span>
        </div>

        <div class="jwi-summary-row">
          <span>- Total Wastage</span>
          <span style="font-weight:800; color:#d97706; font-size:1.05rem;" id="summary-total-wastage">0.00</span>
        </div>

        <div class="jwi-summary-row total-row">
          <span>- Net Inward</span>
          <span style="font-weight:800; color:#059669; font-size:1.25rem;" id="summary-net-inward">0.00</span>
        </div>

        <div style="margin-top:14px; display:flex; justify-content:flex-end; gap:10px;">
          <button type="submit" 
                  id="btn-submit-inward" 
                  class="btn btn-primary" 
                  style="width:100%; font-weight:800; padding:12px; font-size:1rem; background:#0284c7; border-color:#0284c7; display:inline-flex; align-items:center; justify-content:center; gap:8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Save Job Work Inward
          </button>
        </div>

      </div>

    </div>
  </div>

  </form>
</div>

<!-- MULTIPLE THAN POPUP MODAL (Sketch requirement: Add multiple than pop up) -->
<div class="modal fade" id="thanBreakdownModal" tabindex="-1" style="display:none; background:rgba(15,23,42,0.6); z-index:9999;" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:520px; margin: 1.75rem auto;">
    <div class="modal-content" style="border-radius:14px; border:none; box-shadow:0 20px 25px -5px rgba(0,0,0,0.2);">
      <div class="modal-header" style="background:#f8fafc; border-bottom:1px solid #e2e8f0; padding:16px 20px; border-radius:14px 14px 0 0;">
        <h5 class="modal-title" style="font-weight:800; color:#0f172a; font-size:1.1rem; display:flex; align-items:center; gap:8px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
          Multiple Than Breakdown
        </h5>
        <button type="button" class="btn-close" onclick="closeThanModal()" style="border:none; background:transparent; font-size:1.2rem; cursor:pointer;">&times;</button>
      </div>
      <div class="modal-body" style="padding:20px;">
        <p style="font-size:0.85rem; color:#64748b; margin-top:0; margin-bottom:12px;">
          Enter individual Than meter/pieces values (separated by comma, space or newline). Example: <code>100, 105, 95.5, 109.5</code>
        </p>

        <div class="form-group" style="margin-bottom:14px;">
          <label class="form-label" style="font-weight:700; font-size:0.825rem; color:#334155;">Than Values (Pcs/Meters)</label>
          <textarea id="than-modal-textarea" 
                    rows="5" 
                    class="form-control" 
                    placeholder="100&#10;105&#10;95.5&#10;109.5" 
                    oninput="recalculateThanModalPreview()" 
                    style="font-family:var(--font-mono, monospace); font-weight:600;"></textarea>
        </div>

        <!-- Live Modal Calculation -->
        <div style="background:#f1f5f9; border-radius:8px; padding:12px 14px; display:flex; justify-content:space-between; align-items:center;">
          <div>
            <div style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:#64748b;">Total Thans</div>
            <div style="font-weight:800; font-size:1.15rem; color:#0f172a;" id="modal-thans-count">0</div>
          </div>
          <div style="text-align:right;">
            <div style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:#64748b;">Total Sum</div>
            <div style="font-weight:800; font-size:1.15rem; color:#0284c7;" id="modal-thans-sum">0.00</div>
          </div>
        </div>

      </div>
      <div class="modal-footer" style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:12px 20px; border-radius:0 0 14px 14px; display:flex; justify-content:flex-end; gap:10px;">
        <button type="button" class="btn btn-secondary" onclick="closeThanModal()" style="font-weight:700;">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="confirmThanModal()" style="font-weight:700; background:#0284c7; border-color:#0284c7;">
          Apply Thans
        </button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  // Global formatted assignments from Controller
  const assignmentData = @json($formattedAssignments);
  
  // Line items state
  let inwardItems = [];
  let modalTargetContext = 'entry'; // 'entry' or row index
  let currentModalThans = [];

  document.addEventListener('DOMContentLoaded', () => {
    // If worker or order was preselected, trigger change
    const workerSelect = document.getElementById('jwi-jobworker-select');
    if (workerSelect.value) {
      onJobWorkerChanged();
    }
    const orderSelect = document.getElementById('jwi-order-select');
    if (orderSelect.value) {
      onJobOrderChanged();
    }
  });

  // When Jobworker changes, filter open Job Work Orders
  function onJobWorkerChanged() {
    const workerSelect = document.getElementById('jwi-jobworker-select');
    const workerId = workerSelect.value;
    const workerOpt = workerSelect.options[workerSelect.selectedIndex];
    const workerName = workerOpt ? (workerOpt.getAttribute('data-name') || workerOpt.text) : '';
    document.getElementById('jwi-jobworker-name').value = workerName;

    const orderSelect = document.getElementById('jwi-order-select');
    const currentVal = orderSelect.value;
    let foundCurrent = false;

    for (let i = 0; i < orderSelect.options.length; i++) {
      const opt = orderSelect.options[i];
      if (!opt.value) continue;
      const optWorkerId = opt.getAttribute('data-worker-id');

      if (!workerId || optWorkerId == workerId) {
        opt.style.display = '';
        if (opt.value === currentVal) foundCurrent = true;
      } else {
        opt.style.display = 'none';
      }
    }

    if (!foundCurrent && workerId) {
      // Auto-select first matching order
      for (let i = 0; i < orderSelect.options.length; i++) {
        const opt = orderSelect.options[i];
        if (opt.value && opt.style.display !== 'none') {
          orderSelect.value = opt.value;
          onJobOrderChanged();
          break;
        }
      }
    }
  }

  // When Job Work Order is selected, auto-populate item options & details
  function onJobOrderChanged() {
    const orderSelect = document.getElementById('jwi-order-select');
    const orderId = orderSelect.value;
    if (!orderId || !assignmentData[orderId]) {
      return;
    }

    const ja = assignmentData[orderId];
    document.getElementById('jwi-order-no').value = ja.job_order_no || '';
    document.getElementById('jwi-lot-number').value = ja.lot_number || '';
    document.getElementById('jwi-process-name').value = ja.process_name || 'Stitching / Jobwork';

    // Auto-select worker if not already matching
    const workerSelect = document.getElementById('jwi-jobworker-select');
    if (ja.job_worker_id && workerSelect.value != ja.job_worker_id) {
      workerSelect.value = ja.job_worker_id;
      const wOpt = workerSelect.options[workerSelect.selectedIndex];
      if (wOpt) {
        document.getElementById('jwi-jobworker-name').value = wOpt.getAttribute('data-name') || wOpt.text;
      }
    }

    // Refresh entry item dropdown with items from this job assignment
    const recdSelect = document.getElementById('entry-item-recd');
    recdSelect.innerHTML = '<option value="">-- Select Finished Goods (From Order) --</option>';

    if (ja.items && ja.items.length > 0) {
      ja.items.forEach(itm => {
        const opt = document.createElement('option');
        opt.value = itm.finished_item_id || itm.item_id || itm.job_assignment_item_id;
        opt.setAttribute('data-jai-id', itm.job_assignment_item_id || '');
        opt.setAttribute('data-name', itm.finished_item_name || itm.item_name);
        opt.setAttribute('data-raw-name', itm.raw_item_name || '');
        opt.setAttribute('data-assigned-qty', itm.assigned_qty || 0);
        opt.setAttribute('data-prev-qty', itm.previously_received_qty || 0);
        opt.setAttribute('data-rem-qty', itm.remaining_qty || 0);
        opt.setAttribute('data-thans', itm.than_count || 1);
        opt.setAttribute('data-rate', itm.rate || 0);
        opt.textContent = `${itm.finished_item_name || itm.item_name} (Raw: ${itm.raw_item_name || 'N/A'}, Bal: ${itm.remaining_qty} Pcs)`;
        recdSelect.appendChild(opt);
      });

      // Auto-select first item
      if (ja.items.length === 1) {
        recdSelect.selectedIndex = 1;
        onRecdItemSelectChanged();
      }
    }
  }

  function onRecdItemSelectChanged() {
    const recdSelect = document.getElementById('entry-item-recd');
    const opt = recdSelect.options[recdSelect.selectedIndex];
    if (!opt || !opt.value) {
      document.getElementById('entry-used-raw-name').value = '';
      document.getElementById('entry-jai-id').value = '';
      document.getElementById('entry-assigned-qty').value = '0';
      document.getElementById('entry-prev-qty').value = '0';
      document.getElementById('entry-rem-balance').value = '0';
      return;
    }

    const rawName = opt.getAttribute('data-raw-name') || '';
    const jaiId = opt.getAttribute('data-jai-id') || '';
    const assignedQty = parseFloat(opt.getAttribute('data-assigned-qty') || 0);
    const prevQty = parseFloat(opt.getAttribute('data-prev-qty') || 0);
    const remQty = parseFloat(opt.getAttribute('data-rem-qty') || 0);
    const thans = parseInt(opt.getAttribute('data-thans') || 1);

    document.getElementById('entry-used-raw-name').value = rawName;
    document.getElementById('entry-jai-id').value = jaiId;
    document.getElementById('entry-assigned-qty').value = assignedQty;
    document.getElementById('entry-prev-qty').value = prevQty;
    document.getElementById('entry-rem-balance').value = remQty;

    // Suggest remaining quantity if available
    if (remQty > 0) {
      document.getElementById('entry-inward-qty').value = remQty;
      document.getElementById('entry-than-count').value = thans;
    }
  }

  // Multiple Than Modal Functions
  function openThanModalForEntry() {
    modalTargetContext = 'entry';
    document.getElementById('than-modal-textarea').value = currentModalThans.join(', ');
    recalculateThanModalPreview();
    const modalEl = document.getElementById('thanBreakdownModal');
    modalEl.classList.add('show');
    modalEl.style.display = 'block';
  }

  function closeThanModal() {
    const modalEl = document.getElementById('thanBreakdownModal');
    modalEl.classList.remove('show');
    modalEl.style.display = 'none';
  }

  function parseThansFromText(text) {
    if (!text) return [];
    return text.split(/[\n,\s]+/)
      .map(v => parseFloat(v.trim()))
      .filter(v => !isNaN(v) && v > 0);
  }

  function recalculateThanModalPreview() {
    const raw = document.getElementById('than-modal-textarea').value;
    const thans = parseThansFromText(raw);
    const sum = thans.reduce((a, b) => a + b, 0);

    document.getElementById('modal-thans-count').textContent = thans.length;
    document.getElementById('modal-thans-sum').textContent = sum.toFixed(2);
  }

  function confirmThanModal() {
    const raw = document.getElementById('than-modal-textarea').value;
    const thans = parseThansFromText(raw);
    const sum = thans.reduce((a, b) => a + b, 0);

    if (modalTargetContext === 'entry') {
      currentModalThans = thans;
      if (thans.length > 0) {
        document.getElementById('entry-inward-qty').value = sum.toFixed(2);
        document.getElementById('entry-than-count').value = thans.length;
        
        const banner = document.getElementById('entry-than-chips-line');
        banner.style.display = 'flex';
        document.getElementById('entry-than-chips-text').textContent = `${thans.length} Thans = ${sum.toFixed(2)} Pcs/Mtr`;
      }
    }
    closeThanModal();
  }

  function clearEntryThans() {
    currentModalThans = [];
    document.getElementById('entry-than-chips-line').style.display = 'none';
  }

  // Adding item to Inward Table
  function addItemToInwardTable() {
    const recdSelect = document.getElementById('entry-item-recd');
    const recdId = recdSelect.value;
    if (!recdId) {
      alert('Please select the Received (Finished) Item.');
      recdSelect.focus();
      return;
    }

    const opt = recdSelect.options[recdSelect.selectedIndex];
    const recdName = opt.getAttribute('data-name') || opt.text;
    const rawName = document.getElementById('entry-used-raw-name').value || 'Fabric';
    const jaiId = document.getElementById('entry-jai-id').value || null;
    const assignedQty = parseFloat(document.getElementById('entry-assigned-qty').value || 0);
    const prevQty = parseFloat(document.getElementById('entry-prev-qty').value || 0);
    const remBalance = parseFloat(document.getElementById('entry-rem-balance').value || 0);

    const qty = parseFloat(document.getElementById('entry-inward-qty').value || 0);
    const thanCount = parseInt(document.getElementById('entry-than-count').value || currentModalThans.length || 0);
    const actualWaste = parseFloat(document.getElementById('entry-actual-waste').value || 0);

    if (qty <= 0) {
      alert('Please enter a valid inward Quantity greater than 0.');
      document.getElementById('entry-inward-qty').focus();
      return;
    }

    // Pending Qty = Remaining Balance - current inward Qty
    const pendingQty = Math.max(0, remBalance > 0 ? (remBalance - qty) : 0);

    inwardItems.push({
      item_id: recdId,
      item_name: recdName,
      raw_item_name: rawName,
      job_assignment_item_id: jaiId,
      assigned_qty: assignedQty,
      previously_received_qty: prevQty,
      received_qty: qty,
      than_count: thanCount,
      wastage_returned_meters: actualWaste,
      pending_qty: pendingQty,
      thans: [...currentModalThans]
    });

    // Reset entry inputs
    recdSelect.value = '';
    document.getElementById('entry-inward-qty').value = '';
    document.getElementById('entry-than-count').value = '';
    document.getElementById('entry-actual-waste').value = '0.00';
    clearEntryThans();

    renderInwardTable();
    calculateInwardTotals();
  }

  function renderInwardTable() {
    const tbody = document.getElementById('jwi-items-tbody');
    const emptyBanner = document.getElementById('jwi-empty-banner');
    tbody.innerHTML = '';

    if (inwardItems.length === 0) {
      emptyBanner.style.display = 'block';
      return;
    }
    emptyBanner.style.display = 'none';

    inwardItems.forEach((itm, idx) => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td style="text-align:center; font-weight:700; color:#64748b;">${idx + 1}</td>
        <td>
          <div style="font-weight:700; color:#0f172a;">${escapeHtml(itm.item_name)}</div>
          <input type="hidden" name="items[${idx}][item_name]" value="${escapeHtml(itm.item_name)}">
          <input type="hidden" name="items[${idx}][item_id]" value="${itm.item_id || ''}">
          <input type="hidden" name="items[${idx}][job_assignment_item_id]" value="${itm.job_assignment_item_id || ''}">
          <input type="hidden" name="items[${idx}][assigned_qty]" value="${itm.assigned_qty || 0}">
          <input type="hidden" name="items[${idx}][previously_received_qty]" value="${itm.previously_received_qty || 0}">
        </td>
        <td>
          <div style="font-weight:600; color:#475569;">${escapeHtml(itm.raw_item_name)}</div>
          <input type="hidden" name="items[${idx}][raw_item_name]" value="${escapeHtml(itm.raw_item_name)}">
        </td>
        <td style="text-align:right;">
          <input type="number" 
                 step="0.01" 
                 class="form-control" 
                 style="text-align:right; font-weight:700; padding:4px 8px; width:100px; display:inline-block;" 
                 value="${itm.received_qty}" 
                 name="items[${idx}][received_qty]"
                 onchange="updateRowQty(${idx}, this.value)">
        </td>
        <td style="text-align:center;">
          <input type="number" 
                 step="1" 
                 class="form-control" 
                 style="text-align:center; font-weight:700; padding:4px 8px; width:70px; display:inline-block;" 
                 value="${itm.than_count}" 
                 name="items[${idx}][than_count]"
                 onchange="updateRowThans(${idx}, this.value)">
        </td>
        <td style="text-align:right;">
          <input type="number" 
                 step="0.01" 
                 class="form-control" 
                 style="text-align:right; font-weight:700; padding:4px 8px; width:90px; display:inline-block; color:#d97706;" 
                 value="${itm.wastage_returned_meters}" 
                 name="items[${idx}][wastage_returned_meters]"
                 onchange="updateRowWaste(${idx}, this.value)">
        </td>
        <td style="text-align:right; font-weight:700; color:#64748b;">
          ${(itm.pending_qty || 0).toFixed(2)}
          <input type="hidden" name="items[${idx}][pending_qty]" value="${itm.pending_qty || 0}">
        </td>
        <td style="text-align:center;">
          <button type="button" class="btn-remove-row" onclick="removeInwardRow(${idx})" title="Delete Item">
            &times;
          </button>
        </td>
      `;
      tbody.appendChild(tr);
    });
  }

  function updateRowQty(idx, val) {
    const q = parseFloat(val) || 0;
    inwardItems[idx].received_qty = q;
    if (inwardItems[idx].assigned_qty > 0) {
      const rem = Math.max(0, inwardItems[idx].assigned_qty - (inwardItems[idx].previously_received_qty + q));
      inwardItems[idx].pending_qty = rem;
    }
    renderInwardTable();
    calculateInwardTotals();
  }

  function updateRowThans(idx, val) {
    inwardItems[idx].than_count = parseInt(val) || 0;
    calculateInwardTotals();
  }

  function updateRowWaste(idx, val) {
    inwardItems[idx].wastage_returned_meters = parseFloat(val) || 0;
    calculateInwardTotals();
  }

  function removeInwardRow(idx) {
    inwardItems.splice(idx, 1);
    renderInwardTable();
    calculateInwardTotals();
  }

  function calculateInwardTotals() {
    let sumQty = 0;
    let sumThans = 0;
    let sumWaste = 0;
    let sumPending = 0;

    inwardItems.forEach(i => {
      sumQty += (i.received_qty || 0);
      sumThans += (i.than_count || 0);
      sumWaste += (i.wastage_returned_meters || 0);
      sumPending += (i.pending_qty || 0);
    });

    document.getElementById('tfoot-sum-qty').textContent = sumQty.toFixed(2);
    document.getElementById('tfoot-sum-thans').textContent = sumThans;
    document.getElementById('tfoot-sum-waste').textContent = sumWaste.toFixed(2);
    document.getElementById('tfoot-sum-pending').textContent = sumPending.toFixed(2);

    // Summary Card
    document.getElementById('summary-total-recd').textContent = sumQty.toFixed(2);
    document.getElementById('summary-total-wastage').textContent = sumWaste.toFixed(2);
    const net = Math.max(0, sumQty - sumWaste);
    document.getElementById('summary-net-inward').textContent = net.toFixed(2);
  }

  function validateBeforeSubmit(e) {
    const worker = document.getElementById('jwi-jobworker-select').value;
    if (!worker) {
      alert('Please select a Jobworker.');
      document.getElementById('jwi-jobworker-select').focus();
      e.preventDefault();
      return false;
    }

    if (inwardItems.length === 0) {
      alert('Please add at least one line item to the Inward Table.');
      e.preventDefault();
      return false;
    }
    return true;
  }

  function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  // Window global bindings
  window.openThanModalForEntry = openThanModalForEntry;
  window.openEntryThanModal = openThanModalForEntry;
  window.openThanModalForRow = openThanModalForRow;
  window.closeThanModal = closeThanModal;
  window.applyModalThans = applyModalThans;
  window.addInwardLineItem = addInwardLineItem;
  window.removeInwardRow = removeInwardRow;
</script>
@endpush
@endsection
