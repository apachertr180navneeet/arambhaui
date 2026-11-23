@extends('layouts.app')

@section('title', 'Aarambh - JOB Work Order (Job Assignment)')

@section('breadcrumb')
  <div class="breadcrumb-item"><a href="{{ route('jobwork.assign.index') }}" style="color:inherit; text-decoration:none;">Job Work & Assign</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Job Work Order</span></div>
@endsection

@php
  $rawItems = $items->filter(function($i) {
    return stripos($i->category ?? '', 'raw') !== false 
        || stripos($i->type ?? '', 'raw') !== false 
        || stripos($i->category ?? '', 'fabric') !== false 
        || in_array(strtoupper($i->unit ?? ''), ['MTR', 'METERS', 'METER', 'KG', 'YDS']);
  });
  if ($rawItems->isEmpty()) $rawItems = $items;

  $finishedItems = $items->filter(function($i) {
    return stripos($i->category ?? '', 'finish') !== false 
        || stripos($i->type ?? '', 'finish') !== false 
        || in_array(strtoupper($i->unit ?? ''), ['PCS', 'PIECES', 'SETS', 'PCS']);
  });
  if ($finishedItems->isEmpty()) $finishedItems = $items;
@endphp

@push('styles')
<style>
  .jwo-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
    max-width: 1380px;
    margin: 0 auto;
  }
  .jwo-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    padding: 20px 24px;
  }
  .jwo-section-title {
    font-size: 0.95rem;
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
  .jwo-section-title .badge-num {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    background: #eff6ff;
    color: #2563eb;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 800;
  }
  .jwo-grid-header {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
  }
  .jwo-item-entry-panel {
    background: #f8fafc;
    border: 1.5px solid #cbd5e1;
    border-radius: 12px;
    padding: 16px 18px;
    margin-bottom: 18px;
  }
  .jwo-item-entry-grid {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 12px 14px;
    align-items: flex-end;
  }
  .jwo-col-raw      { grid-column: span 6; }
  .jwo-col-finished { grid-column: span 6; }
  .jwo-col-qty      { grid-column: span 3; }
  .jwo-col-than     { grid-column: span 2; }
  .jwo-col-waste    { grid-column: span 2; }
  .jwo-col-date     { grid-column: span 3; }
  .jwo-col-btn      { grid-column: span 2; }

  @media (max-width: 992px) {
    .jwo-item-entry-grid {
      grid-template-columns: repeat(6, 1fr);
    }
    .jwo-col-raw      { grid-column: span 6 !important; }
    .jwo-col-finished { grid-column: span 6 !important; }
    .jwo-col-qty      { grid-column: span 3 !important; }
    .jwo-col-than     { grid-column: span 3 !important; }
    .jwo-col-waste    { grid-column: span 2 !important; }
    .jwo-col-date     { grid-column: span 2 !important; }
    .jwo-col-btn      { grid-column: span 2 !important; }
  }
  @media (max-width: 640px) {
    .jwo-item-entry-grid {
      grid-template-columns: 1fr;
    }
    .jwo-col-raw, .jwo-col-finished, .jwo-col-qty, .jwo-col-than, .jwo-col-waste, .jwo-col-date, .jwo-col-btn {
      grid-column: span 1 !important;
    }
  }
  .jwo-table th {
    background: #f1f5f9 !important;
    color: #475569 !important;
    font-weight: 700 !important;
    font-size: 0.8rem !important;
    text-transform: uppercase !important;
    letter-spacing: 0.03em !important;
    padding: 10px 12px !important;
    border-bottom: 2px solid #cbd5e1 !important;
  }
  .jwo-table td {
    padding: 10px 12px !important;
    font-size: 0.85rem !important;
    vertical-align: middle !important;
    border-bottom: 1px solid #e2e8f0 !important;
  }
  .jwo-table tbody tr:hover {
    background-color: #f8fafc !important;
  }
  .jwo-summary-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 18px 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .jwo-summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.875rem;
    color: #475569;
  }
  .jwo-summary-row.total {
    border-top: 2px dashed #cbd5e1;
    padding-top: 10px;
    margin-top: 4px;
    font-size: 1.1rem;
    font-weight: 800;
    color: #0f172a;
  }
  .modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(15, 23, 42, 0.6);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(2px);
    padding: 16px;
  }
  .modal-box {
    background: #ffffff;
    border-radius: 14px;
    width: 100%;
    max-width: 600px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
    overflow: hidden;
    animation: popIn 0.18s ease-out;
  }
  @keyframes popIn {
    from { transform: scale(0.96); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
  }
</style>
@endpush

@section('content')
<div class="jwo-container">

  <form id="job-order-form" action="{{ route('jobwork.assign.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- Top Action Bar -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:18px;">
      <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:44px; height:44px; border-radius:10px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; box-shadow:0 1px 3px rgba(37,99,235,0.15);">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        </div>
        <div>
          <h1 style="font-size:1.35rem; font-weight:800; color:#0f172a; margin:0; letter-spacing:-0.02em;">
            Aarambh - JOB Work Order (Job Assignment)
          </h1>
          <p style="font-size:0.825rem; color:#64748b; margin:2px 0 0;">
            Handwritten format: Jobworker, Order No., Date, Delivered Raw Item, Recd Finished Item, Than breakdown & waste calculation.
          </p>
        </div>
      </div>

      <div style="display:flex; gap:10px; align-items:center;">
        <a href="{{ route('jobwork.assign.index') }}" class="btn btn-secondary" style="font-weight:700; padding:8px 16px;">
          Cancel
        </a>
        <button type="submit" class="btn btn-primary" id="save-jwo-btn" style="background:#2563eb; font-weight:700; padding:8px 22px; display:inline-flex; align-items:center; gap:8px; box-shadow:0 2px 6px rgba(37,99,235,0.3);">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          Save Job Work Order
        </button>
      </div>
    </div>

    <!-- 1. FORM HEADER FIELDS (Handwritten Sketch 1) -->
    <div class="jwo-card" style="margin-bottom:20px;">
      <div class="jwo-section-title">
        <span class="badge-num">1</span>
        <span>Order Header Information</span>
      </div>

      <div class="jwo-grid-header">
        
        <!-- Jobworker Dropdown -->
        <div class="form-group" style="margin-bottom:0;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:5px;">
            <label class="form-label" style="font-weight:700; font-size:0.825rem; color:#334155; margin:0;">
              Jobworker <span style="color:#ef4444;">*</span>
            </label>
            <a href="{{ route('masters.jobworkers.index') }}" target="_blank" style="font-size:0.75rem; color:#2563eb; text-decoration:none; font-weight:600;">
              + Add New
            </a>
          </div>
          <select name="job_worker_name" id="jwo-worker-select" class="form-control" required style="font-weight:600;" onchange="handleWorkerSelect(this)">
            <option value="">Select Job worker</option>
            @foreach($jobworkers as $jw)
              <option value="{{ $jw->name }}" 
                      data-id="{{ $jw->id }}" 
                      data-phone="{{ $jw->phone ?? '' }}" 
                      data-process="{{ $jw->process_type ?? 'Stitching' }}">
                {{ $jw->name }} ({{ $jw->process_type ?? 'General Jobwork' }})
              </option>
            @endforeach
          </select>
          <input type="hidden" name="job_worker_id" id="jwo-worker-id" value="">
        </div>

        <!-- Job work order NO. -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.825rem; color:#334155; margin-bottom:5px;">
            Job work order NO. <span style="color:#ef4444;">*</span>
          </label>
          <input type="text" 
                 name="job_order_no" 
                 id="jwo-order-no" 
                 class="form-control" 
                 required 
                 value="{{ old('job_order_no', $nextJobOrderNo) }}" 
                 placeholder="e.g. JA-2026-001" 
                 style="font-family:var(--font-mono, monospace); font-weight:700;">
        </div>

        <!-- Date (Date Picker) -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.825rem; color:#334155; margin-bottom:5px;">
            Date <span style="color:#ef4444;">*</span>
          </label>
          <input type="date" 
                 name="issue_date" 
                 id="jwo-date" 
                 class="form-control" 
                 required 
                 value="{{ date('Y-m-d') }}" 
                 style="font-weight:600;">
        </div>

        <!-- Hidden / Supporting Process & Lot fields -->
        <input type="hidden" name="lot_number" id="jwo-lot-no" value="{{ $nextLotNumber }}">
        <input type="hidden" name="process_name" id="jwo-process-name" value="Stitching / Jobwork">
        <input type="hidden" name="maintain_stock" value="1">

      </div>
    </div>

    <!-- 2. INLINE ITEM ENTRY BAR (Handwritten Sketch 1) -->
    <div class="jwo-card" style="margin-bottom:20px;">
      <div class="jwo-section-title">
        <span class="badge-num">2</span>
        <span>Item Entry (Delivered Raw Fabric $\rightarrow$ Received Finished Goods)</span>
      </div>

      <div class="jwo-item-entry-panel">
        <div class="jwo-item-entry-grid">
          
          <!-- LINE 1: Delivered item (Item Master filtered by Raw) -->
          <div class="form-group jwo-col-raw" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569; margin-bottom:4px;">
              Delivered item (Raw) <span style="color:#ef4444;">*</span>
            </label>
            <select id="entry-delivered-raw" class="form-control" style="font-weight:600;" onchange="calculateEntryWaste()">
              <option value="">-- Select Raw Fabric --</option>
              @foreach($rawItems as $rItm)
                <option value="{{ $rItm->id }}" 
                        data-name="{{ $rItm->name }}" 
                        data-code="{{ $rItm->code }}" 
                        data-stock="{{ $rItm->current_stock ?? 0 }}"
                        data-unit="{{ $rItm->unit ?? 'MTR' }}">
                  [Raw] {{ $rItm->name }} ({{ $rItm->code }}) • Stock: {{ (float)($rItm->current_stock ?? 0) }} {{ $rItm->unit ?? 'MTR' }}
                </option>
              @endforeach
            </select>
          </div>

          <!-- LINE 1: Recd item (Finished Goods) -->
          <div class="form-group jwo-col-finished" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569; margin-bottom:4px;">
              Recd item (Finished) <span style="color:#ef4444;">*</span>
            </label>
            <select id="entry-recd-finished" class="form-control" style="font-weight:600;" onchange="calculateEntryWaste()">
              <option value="">-- Select Finished Good --</option>
              @foreach($finishedItems as $fItm)
                <option value="{{ $fItm->id }}" 
                        data-name="{{ $fItm->name }}" 
                        data-code="{{ $fItm->code }}"
                        data-unit="{{ $fItm->unit ?? 'PCS' }}">
                  [Finished] {{ $fItm->name }} ({{ $fItm->code }})
                </option>
              @endforeach
            </select>
          </div>

          <!-- LINE 2: Qty (Raw Delivered Quantity / Meters) -->
          <div class="form-group jwo-col-qty" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569; margin-bottom:4px;">
              Qty (Raw Mtr)
            </label>
            <input type="number" 
                   id="entry-raw-qty" 
                   class="form-control" 
                   step="0.01" 
                   min="0" 
                   placeholder="0.00" 
                   style="font-weight:800; color:#059669;" 
                   oninput="onEntryQtyInput()">
          </div>

          <!-- LINE 2: Multiple Than Modal Button (+) & Than Input -->
          <div class="form-group jwo-col-than" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569; margin-bottom:4px; display:flex; justify-content:space-between;">
              <span>Than</span>
              <a href="javascript:void(0)" onclick="openEntryThanModal()" style="font-size:0.75rem; color:#2563eb; text-decoration:none;" title="Add multiple than roll readings">+ Breakdown</a>
            </label>
            <div style="display:flex; gap:4px; align-items:center;">
              <input type="number" 
                     id="entry-than-count" 
                     class="form-control" 
                     min="0" 
                     step="1" 
                     placeholder="0" 
                     style="font-weight:700;" 
                     oninput="calculateEntryWaste()">
              <button type="button" 
                      class="btn btn-secondary btn-sm" 
                      onclick="openEntryThanModal()" 
                      style="padding:7px 10px; background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; font-weight:800;" 
                      title="Add multiple than meter breakdown">
                +
              </button>
            </div>
          </div>

          <!-- LINE 2: Expected waste (Auto-calculated / percentage) -->
          <div class="form-group jwo-col-waste" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569; margin-bottom:4px;">
              Expected waste
            </label>
            <div style="position:relative; display:flex; align-items:center;">
              <input type="number" 
                     id="entry-expected-waste" 
                     class="form-control" 
                     step="0.01" 
                     min="0" 
                     placeholder="0.00" 
                     value="0.00"
                     style="font-weight:700; color:#dc2626; background:#f8fafc;">
              <span style="position:absolute; right:8px; font-size:0.65rem; font-weight:800; background:#fee2e2; color:#dc2626; padding:2px 5px; border-radius:4px;">
                Auto
              </span>
            </div>
          </div>

          <!-- LINE 2: Delivery Date -->
          <div class="form-group jwo-col-date" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569; margin-bottom:4px;">
              Delivery Date
            </label>
            <input type="date" 
                   id="entry-delivery-date" 
                   class="form-control" 
                   value="{{ date('Y-m-d', strtotime('+7 days')) }}" 
                   style="font-weight:600;">
          </div>

          <!-- LINE 2: Add Item Button -->
          <div class="jwo-col-btn">
            <button type="button" 
                    id="btn-add-jwo-item" 
                    class="btn btn-primary" 
                    onclick="addItemToJobOrderTable()" 
                    style="width:100%; height:38px; font-weight:700; padding:8px 18px; background:#2563eb; display:inline-flex; align-items:center; justify-content:center; gap:6px; white-space:nowrap;">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Add Item
            </button>
          </div>

        </div>

        <!-- Than Breakdown Banner -->
        <div id="entry-than-chips-line" style="display:none; margin-top:10px; padding:6px 12px; background:#eff6ff; border-radius:6px; font-size:0.8rem; color:#1e40af; align-items:center; justify-content:space-between;">
          <div>
            <span style="font-weight:700;">Roll Readings:</span>
            <span id="entry-than-chips-text">0 Thans</span> • Total Qty: <span id="entry-than-meters-text" style="font-weight:800;">0.00 MTR</span>
          </div>
          <button type="button" onclick="clearEntryThans()" style="background:none; border:none; color:#ef4444; font-size:0.75rem; font-weight:700; cursor:pointer;">
            Clear Than
          </button>
        </div>
      </div>

      <!-- 3. LINE ITEMS TABLE (Handwritten Sketch 1) -->
      <div class="table-responsive" style="border:1px solid #e2e8f0; border-radius:10px; overflow:hidden;">
        <table class="table jwo-table" id="jwo-items-table" style="width:100%; margin-bottom:0;">
          <thead>
            <tr>
              <th style="width:50px; text-align:center;">SNO.</th>
              <th style="width:28%;">ITEM (Delivered Raw)</th>
              <th style="width:24%;">Recd item (Finished)</th>
              <th style="width:13%; text-align:right;">Qty (MTR)</th>
              <th style="width:11%; text-align:center;">THAN</th>
              <th style="width:13%; text-align:right;">Expected WASTE</th>
              <th style="width:13%; text-align:center;">Delivery Date</th>
              <th style="width:60px; text-align:center;">Actions</th>
            </tr>
          </thead>
          <tbody id="jwo-items-tbody">
            <!-- Dynamic items rendered via JavaScript -->
          </tbody>
          <tfoot id="jwo-table-footer" style="background:#f8fafc; font-weight:800; border-top:2px solid #cbd5e1;">
            <tr>
              <td colspan="3" style="text-align:right; font-size:0.875rem; color:#334155;">Total:</td>
              <td id="footer-total-qty" style="text-align:right; color:#059669; font-size:0.95rem;">0.00</td>
              <td id="footer-total-than" style="text-align:center; color:#2563eb; font-size:0.95rem;">0</td>
              <td id="footer-total-waste" style="text-align:right; color:#dc2626; font-size:0.95rem;">0.00</td>
              <td colspan="2"></td>
            </tr>
          </tfoot>
        </table>
      </div>

      <!-- Empty state banner -->
      <div id="jwo-empty-banner" style="text-align:center; padding:32px 16px; color:#94a3b8; background:#fff;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom:8px; opacity:0.6;"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/><path d="M12 8v8"/></svg>
        <div style="font-weight:700; font-size:0.95rem; color:#64748b;">No Items Added to Job Work Order Yet</div>
        <div style="font-size:0.8rem; margin-top:4px;">Select Delivered Raw Item and Recd Finished Item above, then click "Add Item".</div>
      </div>

    </div>

    <!-- 4. FOOTER & SUMMARY (Handwritten Sketch 1) -->
    <div style="display:grid; grid-template-columns:1.3fr 1fr; gap:20px; align-items:flex-start;">
      
      <!-- Remarks & Photo Attachment -->
      <div class="jwo-card">
        <div class="jwo-section-title">
          <span class="badge-num">3</span>
          <span>Remarks & Supporting Photo</span>
        </div>

        <div class="form-group" style="margin-bottom:14px;">
          <label class="form-label" style="font-weight:700; font-size:0.825rem; color:#334155;">Remark.</label>
          <textarea name="instructions" 
                    id="jwo-remarks" 
                    class="form-control" 
                    rows="3" 
                    placeholder="Enter process instructions, shrinkage allowances, stitching specifications or delivery terms..."></textarea>
        </div>

        <!-- Sample Photo Upload / Camera fallback -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.825rem; color:#334155; display:flex; justify-content:space-between;">
            <span>Job Sample Photo (Optional)</span>
            <span style="font-size:0.75rem; color:#64748b;">Camera / Device Photo</span>
          </label>
          <input type="file" name="photos[]" class="form-control" accept="image/*" capture="environment">
        </div>
      </div>

      <!-- Right Summary (Handwritten Sketch 1) -->
      <div class="jwo-card">
        <div class="jwo-section-title">
          <span class="badge-num">4</span>
          <span>Order Summary</span>
        </div>

        <div class="jwo-summary-box">
          <div class="jwo-summary-row">
            <span>T. Raw material:</span>
            <span id="summary-total-raw" style="font-weight:800; color:#059669; font-size:1rem;">0.00 MTR</span>
          </div>

          <div class="jwo-summary-row">
            <span>Total Than:</span>
            <span id="summary-total-than" style="font-weight:800; color:#2563eb; font-size:1rem;">0 Thans</span>
          </div>

          <div class="jwo-summary-row">
            <span>Total Expect waste:</span>
            <span id="summary-total-waste" style="font-weight:800; color:#dc2626; font-size:1rem;">0.00 MTR</span>
          </div>

          <div class="jwo-summary-row total">
            <span>Total of Finished Goods:</span>
            <span id="summary-total-finished" style="color:#0f172a; font-size:1.25rem;">0 PCS</span>
          </div>
        </div>

        <!-- Hidden aggregate fields -->
        <input type="hidden" name="total_than_meters" id="hidden-total-than-meters" value="0">
        <input type="hidden" name="total_thans" id="hidden-total-thans" value="0">
        <input type="hidden" name="total_wastage_meters" id="hidden-total-wastage-meters" value="0">
        <input type="hidden" name="issued_qty" id="hidden-issued-qty" value="0">
      </div>

    </div>

  </form>

</div>

<!-- Multiple Than Entry Modal -->
<div class="modal-overlay" id="than-modal" style="display:none;">
  <div class="modal-box">
    <div style="padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; background:#f8fafc;">
      <h4 style="margin:0; font-size:1rem; font-weight:800; color:#0f172a;" id="than-modal-title">
        📦 Multiple Than / Roll Meter Readings
      </h4>
      <button type="button" onclick="closeThanModal()" style="background:none; border:none; color:#64748b; font-size:1.4rem; cursor:pointer;">
        &times;
      </button>
    </div>

    <div style="padding:18px 20px;">
      <p style="font-size:0.8rem; color:#64748b; margin-top:0; margin-bottom:10px;">
        Enter meters for each than separated by commas or spaces (e.g. <code>100, 110, 95.5, 95.5</code>).
      </p>

      <textarea id="modal-than-textarea" 
                class="form-control" 
                rows="4" 
                placeholder="100, 110, 95, 105..." 
                oninput="updateModalThanPreview()" 
                style="font-family:var(--font-mono, monospace); font-size:0.9rem; font-weight:600;"></textarea>

      <div style="margin-top:14px; background:#eff6ff; border-radius:8px; padding:12px; display:flex; justify-content:space-around; align-items:center;">
        <div style="text-align:center;">
          <div style="font-size:0.75rem; color:#64748b; font-weight:700;">TOTAL THANS</div>
          <div id="modal-preview-count" style="font-size:1.25rem; font-weight:800; color:#2563eb;">0 Thans</div>
        </div>
        <div style="width:1px; height:30px; background:#bfdbfe;"></div>
        <div style="text-align:center;">
          <div style="font-size:0.75rem; color:#64748b; font-weight:700;">TOTAL METERS</div>
          <div id="modal-preview-meters" style="font-size:1.25rem; font-weight:800; color:#059669;">0.00 MTR</div>
        </div>
      </div>
    </div>

    <div style="padding:14px 20px; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:10px; background:#f8fafc;">
      <button type="button" class="btn btn-secondary" onclick="closeThanModal()" style="font-weight:700;">
        Cancel
      </button>
      <button type="button" class="btn btn-primary" onclick="applyModalThans()" style="background:#2563eb; font-weight:700;">
        Apply to Qty & Than
      </button>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  let orderItems = [];
  let currentModalThans = [];
  let modalTargetContext = 'entry';

  function handleWorkerSelect(select) {
    const opt = select.options[select.selectedIndex];
    const workerId = opt ? opt.getAttribute('data-id') : '';
    const process = opt ? opt.getAttribute('data-process') : 'Stitching';
    document.getElementById('jwo-worker-id').value = workerId || '';
    if (process) {
      document.getElementById('jwo-process-name').value = process;
    }
  }

  function calculateEntryWaste() {
    const qty = parseFloat(document.getElementById('entry-raw-qty').value || 0);
    // Typical wastage standard: ~1% to 2% of fabric or 0.02 * qty
    const waste = Math.round((qty * 0.015) * 100) / 100;
    const wasteInput = document.getElementById('entry-expected-waste');
    if (wasteInput && (!wasteInput.value || parseFloat(wasteInput.value) === 0 || wasteInput.dataset.auto === 'true')) {
      wasteInput.value = waste > 0 ? waste.toFixed(2) : '0.00';
      wasteInput.dataset.auto = 'true';
    }
  }

  function onEntryQtyInput() {
    document.getElementById('entry-expected-waste').dataset.auto = 'true';
    calculateEntryWaste();
  }

  // Than Modal
  function openEntryThanModal() {
    modalTargetContext = 'entry';
    document.getElementById('than-modal-title').textContent = '📦 Entry Than / Roll Meter Readings';
    document.getElementById('modal-than-textarea').value = currentModalThans.length > 0 ? currentModalThans.join(', ') : (document.getElementById('entry-raw-qty').value || '');
    updateModalThanPreview();
    document.getElementById('than-modal').style.display = 'flex';
  }

  function openRowThanModal(index) {
    modalTargetContext = index;
    const item = orderItems[index];
    if (!item) return;

    document.getElementById('than-modal-title').textContent = `📦 Than Readings for: ${item.raw_item_name}`;
    document.getElementById('modal-than-textarea').value = (item.thans && item.thans.length > 0) ? item.thans.join(', ') : '';
    updateModalThanPreview();
    document.getElementById('than-modal').style.display = 'flex';
  }

  function closeThanModal() {
    document.getElementById('than-modal').style.display = 'none';
  }

  function parseThansFromText(text) {
    if (!text) return [];
    const parts = text.split(/[,\s\n\t]+/);
    const res = [];
    parts.forEach(p => {
      const v = parseFloat(p.trim());
      if (!isNaN(v) && v > 0) {
        res.push(Math.round(v * 100) / 100);
      }
    });
    return res;
  }

  function updateModalThanPreview() {
    const raw = document.getElementById('modal-than-textarea').value;
    const thans = parseThansFromText(raw);
    const sum = thans.reduce((a, b) => a + b, 0);

    document.getElementById('modal-preview-count').textContent = thans.length + ' Thans';
    document.getElementById('modal-preview-meters').textContent = sum.toFixed(2) + ' MTR';
  }

  function applyModalThans() {
    const raw = document.getElementById('modal-than-textarea').value;
    const thans = parseThansFromText(raw);
    const sum = thans.reduce((a, b) => a + b, 0);

    if (modalTargetContext === 'entry') {
      currentModalThans = thans;
      if (thans.length > 0) {
        document.getElementById('entry-raw-qty').value = sum.toFixed(2);
        document.getElementById('entry-than-count').value = thans.length;
        
        const banner = document.getElementById('entry-than-chips-line');
        if (banner) {
          banner.style.display = 'flex';
          document.getElementById('entry-than-chips-text').textContent = `${thans.length} Thans`;
          document.getElementById('entry-than-meters-text').textContent = `${sum.toFixed(2)} MTR`;
        }
        calculateEntryWaste();
      }
    } else {
      const idx = modalTargetContext;
      if (orderItems[idx]) {
        orderItems[idx].thans = thans;
        orderItems[idx].than_count = thans.length;
        if (thans.length > 0) {
          orderItems[idx].qty = sum;
        }
        renderTable();
        calculateOrderTotals();
      }
    }

    closeThanModal();
  }

  function clearEntryThans() {
    currentModalThans = [];
    document.getElementById('entry-than-chips-line').style.display = 'none';
    document.getElementById('entry-than-count').value = '';
  }

  // Adding item to line items table
  function addItemToJobOrderTable() {
    const rawSelect = document.getElementById('entry-delivered-raw');
    const recdSelect = document.getElementById('entry-recd-finished');

    const rawId = rawSelect.value;
    const recdId = recdSelect.value;

    if (!rawId) {
      alert('Please select the Delivered (Raw) Item.');
      rawSelect.focus();
      return;
    }
    if (!recdId) {
      alert('Please select the Received (Finished) Item.');
      recdSelect.focus();
      return;
    }

    const rawOpt = rawSelect.options[rawSelect.selectedIndex];
    const recdOpt = recdSelect.options[recdSelect.selectedIndex];

    const rawName = rawOpt.getAttribute('data-name') || rawOpt.text;
    const recdName = recdOpt.getAttribute('data-name') || recdOpt.text;

    const qty = parseFloat(document.getElementById('entry-raw-qty').value || 0);
    const thanCount = parseInt(document.getElementById('entry-than-count').value || currentModalThans.length || 0);
    const expectedWaste = parseFloat(document.getElementById('entry-expected-waste').value || 0);
    const deliveryDate = document.getElementById('entry-delivery-date').value || '{{ date("Y-m-d", strtotime("+7 days")) }}';

    if (qty <= 0) {
      alert('Please enter a valid Raw Material Quantity greater than 0.');
      document.getElementById('entry-raw-qty').focus();
      return;
    }

    // Finished pieces estimate: (Qty - Waste) / 1.5 consumption
    const usableMtr = Math.max(0, qty - expectedWaste);
    const estPcs = Math.max(1, Math.floor(usableMtr / 1.5));

    orderItems.push({
      raw_item_id: rawId,
      raw_item_name: rawName,
      finished_item_id: recdId,
      finished_item_name: recdName,
      qty: qty,
      than_count: thanCount,
      expected_waste: expectedWaste,
      delivery_date: deliveryDate,
      thans: [...currentModalThans],
      production_pcs: estPcs
    });

    // Reset inputs
    rawSelect.value = '';
    recdSelect.value = '';
    document.getElementById('entry-raw-qty').value = '';
    document.getElementById('entry-than-count').value = '';
    document.getElementById('entry-expected-waste').value = '0.00';
    clearEntryThans();

    renderTable();
    calculateOrderTotals();
  }

  function renderTable() {
    const tbody = document.getElementById('jwo-items-tbody');
    const emptyBanner = document.getElementById('jwo-empty-banner');
    tbody.innerHTML = '';

    if (orderItems.length === 0) {
      emptyBanner.style.display = 'block';
      return;
    }

    emptyBanner.style.display = 'none';

    orderItems.forEach((item, index) => {
      const tr = document.createElement('tr');

      let thanHtml = item.than_count;
      if (item.thans && item.thans.length > 0) {
        thanHtml = `<button type="button" class="btn btn-secondary btn-xs" onclick="openRowThanModal(${index})" style="background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; font-weight:700; font-size:0.75rem; padding:2px 6px;">
          ${item.thans.length} Thans 📝
        </button>`;
      }

      tr.innerHTML = `
        <td style="text-align:center; font-weight:700; color:#64748b;">${index + 1}</td>
        <td>
          <div style="font-weight:700; color:#0f172a;">${item.raw_item_name}</div>
          <span style="font-size:0.7rem; font-weight:700; background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; padding:1px 5px; border-radius:4px;">Raw</span>
          <!-- Hidden inputs for form submit -->
          <input type="hidden" name="items[${index}][raw_item_id]" value="${item.raw_item_id}">
          <input type="hidden" name="items[${index}][raw_item_name]" value="${item.raw_item_name}">
          <input type="hidden" name="items[${index}][finished_item_id]" value="${item.finished_item_id}">
          <input type="hidden" name="items[${index}][finished_item_name]" value="${item.finished_item_name}">
          <input type="hidden" name="items[${index}][than_meters]" value="${item.qty}">
          <input type="hidden" name="items[${index}][production_pcs]" value="${item.production_pcs}">
          <input type="hidden" name="items[${index}][wastage_meters]" value="${item.expected_waste}">
          <input type="hidden" name="items[${index}][than_count]" value="${item.than_count}">
          <input type="hidden" name="items[${index}][thans]" value='${JSON.stringify(item.thans || [])}'>
        </td>
        <td>
          <div style="font-weight:700; color:#0f172a;">${item.finished_item_name}</div>
          <span style="font-size:0.7rem; font-weight:700; background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; padding:1px 5px; border-radius:4px;">Finished</span>
        </td>
        <td style="text-align:right;">
          <input type="number" step="0.01" min="0" 
                 class="form-control" 
                 value="${item.qty}" 
                 oninput="handleRowQtyChange(${index}, this.value)" 
                 style="text-align:right; font-weight:800; color:#059669; width:95px; display:inline-block; padding:4px 6px;">
        </td>
        <td style="text-align:center; font-weight:700; color:#2563eb;">
          ${thanHtml}
        </td>
        <td style="text-align:right;">
          <input type="number" step="0.01" min="0" 
                 class="form-control" 
                 value="${item.expected_waste}" 
                 oninput="handleRowWasteChange(${index}, this.value)" 
                 style="text-align:right; font-weight:700; color:#dc2626; width:85px; display:inline-block; padding:4px 6px;">
        </td>
        <td style="text-align:center; font-weight:600; color:#475569;">
          ${item.delivery_date}
        </td>
        <td style="text-align:center;">
          <button type="button" 
                  onclick="deleteRow(${index})" 
                  class="btn btn-secondary btn-xs" 
                  style="color:#ef4444; border:1px solid #fecdd3; background:#fff1f2; padding:4px 8px; border-radius:6px; cursor:pointer;" 
                  title="Delete Row">
            &times;
          </button>
        </td>
      `;

      tbody.appendChild(tr);
    });
  }

  function handleRowQtyChange(index, val) {
    orderItems[index].qty = parseFloat(val) || 0;
    calculateOrderTotals();
  }

  function handleRowWasteChange(index, val) {
    orderItems[index].expected_waste = parseFloat(val) || 0;
    calculateOrderTotals();
  }

  function deleteRow(index) {
    orderItems.splice(index, 1);
    renderTable();
    calculateOrderTotals();
  }

  function calculateOrderTotals() {
    let totalQty = 0;
    let totalThan = 0;
    let totalWaste = 0;
    let totalFinishedPcs = 0;

    orderItems.forEach(it => {
      totalQty += it.qty;
      totalThan += it.than_count;
      totalWaste += it.expected_waste;
      totalFinishedPcs += (it.production_pcs || Math.max(1, Math.floor(Math.max(0, it.qty - it.expected_waste) / 1.5)));
    });

    // Update table footer auto-sums
    document.getElementById('footer-total-qty').textContent = totalQty.toFixed(2);
    document.getElementById('footer-total-than').textContent = totalThan;
    document.getElementById('footer-total-waste').textContent = totalWaste.toFixed(2);

    // Update summary card
    document.getElementById('summary-total-raw').textContent = `${totalQty.toFixed(2)} MTR`;
    document.getElementById('summary-total-than').textContent = `${totalThan} Thans`;
    document.getElementById('summary-total-waste').textContent = `${totalWaste.toFixed(2)} MTR`;
    document.getElementById('summary-total-finished').textContent = `${totalFinishedPcs} PCS`;

    // Hidden inputs
    document.getElementById('hidden-total-than-meters').value = totalQty.toFixed(2);
    document.getElementById('hidden-total-thans').value = totalThan;
    document.getElementById('hidden-total-wastage-meters').value = totalWaste.toFixed(2);
    document.getElementById('hidden-issued-qty').value = totalFinishedPcs;
  }

  // Form submit validation
  document.getElementById('job-order-form')?.addEventListener('submit', function(e) {
    if (orderItems.length === 0) {
      e.preventDefault();
      alert('Please add at least one line item to the Job Work Order.');
      return false;
    }
    const worker = document.getElementById('jwo-worker-select').value;
    if (!worker) {
      e.preventDefault();
      alert('Please select a Job worker.');
      document.getElementById('jwo-worker-select').focus();
      return false;
    }
  });

  // Ensure modal and action handlers are globally accessible on window
  window.openEntryThanModal = openEntryThanModal;
  window.openRowThanModal = openRowThanModal;
  window.closeThanModal = closeThanModal;
  window.applyModalThans = applyModalThans;
  window.updateModalThanPreview = updateModalThanPreview;
  window.addEntryLineItem = addEntryLineItem;
  window.deleteItem = deleteItem;
  window.editItem = editItem;

  document.addEventListener('DOMContentLoaded', function() {
    renderTable();
    calculateOrderTotals();
  });
</script>
@endpush
