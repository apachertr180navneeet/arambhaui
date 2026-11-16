@extends('layouts.app')

@section('title', 'Aarambh - Edit JOB Work Order (' . $assign->job_order_no . ')')

@section('breadcrumb')
  <div class="breadcrumb-item"><a href="{{ route('jobwork.assign.index') }}" style="color:inherit; text-decoration:none;">Job Work & Assign</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Edit Order {{ $assign->job_order_no }}</span></div>
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

  $preloadedItems = [];
  if ($assign->items && $assign->items->count() > 0) {
    foreach ($assign->items as $ai) {
      $preloadedItems[] = [
        'raw_item_id' => $ai->raw_item_id,
        'raw_item_name' => $ai->raw_item_name ?: ($ai->rawItem?->name ?? 'Raw Fabric'),
        'finished_item_id' => $ai->finished_item_id,
        'finished_item_name' => $ai->finished_item_name ?: ($ai->finishedItem?->name ?? 'Finished Good'),
        'qty' => (float)($ai->than_meters > 0 ? $ai->than_meters : ($ai->qty ?? 0)),
        'than_count' => (int)($ai->than_count > 0 ? $ai->than_count : (count($ai->than_list ?? []) ?: 1)),
        'expected_waste' => (float)$ai->wastage_meters,
        'delivery_date' => $ai->due_date ? date('Y-m-d', strtotime($ai->due_date)) : date('Y-m-d', strtotime($assign->due_date ?: '+7 days')),
        'production_pcs' => (int)($ai->production_pcs > 0 ? $ai->production_pcs : 1),
        'thans' => $ai->than_list ?? []
      ];
    }
  } else {
    $preloadedItems[] = [
      'raw_item_id' => null,
      'raw_item_name' => 'Fabric',
      'finished_item_id' => null,
      'finished_item_name' => $assign->style_name ?: 'Finished Good',
      'qty' => (float)$assign->total_than_meters,
      'than_count' => (int)$assign->total_thans ?: 1,
      'expected_waste' => (float)$assign->total_wastage_meters,
      'delivery_date' => date('Y-m-d', strtotime($assign->due_date ?: '+7 days')),
      'production_pcs' => (int)$assign->issued_qty ?: 1,
      'thans' => $assign->than_list ?? []
    ];
  }
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
    background: #2563eb;
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 800;
  }
  .jwo-header-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
  }
  .jwo-item-entry-panel {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 16px 18px;
  }
  .jwo-item-entry-grid {
    display: grid;
    grid-template-columns: 1.8fr 1.8fr 1fr 44px 1fr 1fr 1.1fr auto;
    gap: 10px;
    align-items: flex-end;
  }
  .jwo-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-top: 14px;
    font-size: 0.875rem;
  }
  .jwo-table th {
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
  .jwo-table td {
    padding: 12px 14px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    color: #1e293b;
  }
  .jwo-table tbody tr:hover {
    background: #f8fafc;
  }
  .jwo-table tfoot td {
    background: #f8fafc;
    font-weight: 800;
    border-top: 2px solid #cbd5e1;
    border-bottom: 2px solid #cbd5e1;
    padding: 12px 14px;
    color: #0f172a;
  }
  .jwo-bottom-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 24px;
  }
  .jwo-summary-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 16px 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  .jwo-summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.95rem;
    color: #475569;
    padding-bottom: 8px;
    border-bottom: 1px dashed #e2e8f0;
  }
  .jwo-summary-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
  }
  .jwo-summary-row.total-row {
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
    .jwo-header-grid {
      grid-template-columns: 1fr;
    }
    .jwo-item-entry-grid {
      grid-template-columns: 1fr 1fr;
    }
    .jwo-bottom-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
@endpush

@section('content')
<div class="jwo-container">

  <!-- Top Title Bar -->
  <div class="jwo-card" style="padding-bottom: 16px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
      <div style="font-size:1.35rem; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:10px;">
        <span style="color:#2563eb;">☆</span>
        <span>Aarambh - Edit JOB Work Order ({{ $assign->job_order_no }})</span>
      </div>
      <div style="display:flex; gap:10px; align-items:center;">
        <a href="{{ route('jobwork.assign.index') }}" class="btn btn-secondary" style="font-weight:600;">
          Back to List
        </a>
      </div>
    </div>

    <!-- Main Order Form -->
    <form action="{{ route('jobwork.assign.update', $assign->id) }}" method="POST" id="job-work-order-form" enctype="multipart/form-data" onsubmit="return validateBeforeSubmit(event)">
      @csrf
      @method('PUT')

      <!-- 1. FORM HEADER FIELDS (Handwritten Sketch 1) -->
      <div class="jwo-section-title">
        <span class="badge-num">1</span>
        <span>Header Information</span>
      </div>

      <div class="jwo-header-grid">
        
        <!-- Jobworker (Select Job worker) -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.825rem; color:#334155; margin-bottom:5px;">
            Jobworker [MASTER] <span style="color:#ef4444;">*</span>
          </label>
          <select name="job_worker_id" id="jwo-jobworker-select" class="form-control" required onchange="onJobWorkerChanged()">
            <option value="">-- Select Job worker --</option>
            @foreach($jobworkers as $jw)
              <option value="{{ $jw->id }}" 
                      data-name="{{ $jw->name }}" 
                      {{ ($assign->job_worker_id == $jw->id) ? 'selected' : '' }}>
                {{ $jw->name }} {{ $jw->phone ? '('.$jw->phone.')' : '' }}
              </option>
            @endforeach
          </select>
          <input type="hidden" name="job_worker_name" id="jwo-jobworker-name" value="{{ $assign->job_worker_name }}">
        </div>

        <!-- Job work order NO. (Input) -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.825rem; color:#334155; margin-bottom:5px;">
            Job work order NO. <span style="color:#ef4444;">*</span>
          </label>
          <input type="text" 
                 name="job_order_no" 
                 id="jwo-order-no" 
                 class="form-control" 
                 required 
                 value="{{ $assign->job_order_no }}" 
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
                 value="{{ date('Y-m-d', strtotime($assign->issue_date)) }}" 
                 style="font-weight:600;">
        </div>

        <!-- Hidden / Supporting Process & Lot fields -->
        <input type="hidden" name="lot_number" id="jwo-lot-no" value="{{ $assign->lot_number }}">
        <input type="hidden" name="process_name" id="jwo-process-name" value="{{ $assign->process_name ?: 'Stitching / Jobwork' }}">
        <input type="hidden" name="maintain_stock" value="1">

      </div>
  </div>

  <!-- 2. INLINE ITEM ENTRY BAR (Handwritten Sketch 1) -->
  <div class="jwo-card" style="margin-bottom:20px;">
    <div class="jwo-section-title">
      <span class="badge-num">2</span>
      <span>Item Entry (Delivered Raw Fabric &rarr; Received Finished Goods)</span>
    </div>

    <div class="jwo-item-entry-panel">
      <div class="jwo-item-entry-grid">
        
        <!-- Delivered item (Item Master filtered by Raw) -->
        <div class="form-group" style="margin-bottom:0;">
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

        <!-- Recd item (Item Master filtered by Finished) -->
        <div class="form-group" style="margin-bottom:0;">
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

        <!-- Qty (Number input) -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569; margin-bottom:4px;">
            Qty (Mtr) <span style="color:#ef4444;">*</span>
          </label>
          <input type="number" 
                 step="0.01" 
                 min="0.01" 
                 id="entry-raw-qty" 
                 class="form-control" 
                 placeholder="0.00" 
                 style="font-weight:700; text-align:right;"
                 oninput="calculateEntryWaste()">
        </div>

        <!-- + Button (supports adding multiple / than modal) -->
        <div class="form-group" style="margin-bottom:0; display:flex; align-items:center;">
          <button type="button" 
                  class="btn-quick-add" 
                  title="Add Multiple Than Breakdown" 
                  onclick="openThanModalForEntry()">
            +
          </button>
        </div>

        <!-- Than (Number input) -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569; margin-bottom:4px;">
            Than <span style="color:#ef4444;">*</span>
          </label>
          <input type="number" 
                 step="1" 
                 min="1" 
                 id="entry-than-count" 
                 class="form-control" 
                 placeholder="0" 
                 style="font-weight:700; text-align:center;">
        </div>

        <!-- Expected waste (Auto-calculated read-only) -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569; margin-bottom:4px;">
            Expected waste (Auto)
          </label>
          <input type="number" 
                 step="0.01" 
                 min="0" 
                 id="entry-expected-waste" 
                 class="form-control" 
                 readonly 
                 placeholder="0.00" 
                 value="0.00"
                 style="background:#f1f5f9; font-weight:700; text-align:right; color:#d97706;">
        </div>

        <!-- Delivery Date -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569; margin-bottom:4px;">
            Delivery Date
          </label>
          <input type="date" 
                 id="entry-delivery-date" 
                 class="form-control" 
                 value="{{ date('Y-m-d', strtotime('+7 days')) }}" 
                 style="font-weight:600;">
        </div>

        <!-- Add Item Button -->
        <div class="form-group" style="margin-bottom:0;">
          <button type="button" 
                  class="btn btn-primary" 
                  onclick="addItemToJobOrderTable()" 
                  style="font-weight:700; padding:9px 18px; display:inline-flex; align-items:center; gap:6px;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add Item
          </button>
        </div>

      </div>

      <!-- Live Thans preview chip line -->
      <div id="entry-than-chips-line" style="display:none; align-items:center; gap:8px; margin-top:10px; padding:6px 12px; background:#eff6ff; border-radius:6px; font-size:0.8rem; color:#1e40af;">
        <strong>Entered Thans Breakdown:</strong>
        <span id="entry-than-chips-text"></span>
        <span style="font-weight:700; color:#059669;" id="entry-than-meters-text"></span>
        <span style="margin-left:auto; cursor:pointer; color:#ef4444; font-weight:700;" onclick="clearEntryThans()">Clear</span>
      </div>

    </div>

    <!-- 3. LINE ITEMS TABLE (Handwritten Sketch 1) -->
    <div style="overflow-x:auto;">
      <table class="jwo-table" id="jwo-items-table">
        <thead>
          <tr>
            <th style="width:50px; text-align:center;">SNO.</th>
            <th style="width:22%;">ITEM (Delivered Raw)</th>
            <th style="width:22%;">Recd item (Finished)</th>
            <th style="width:13%; text-align:right;">Qty (Mtr)</th>
            <th style="width:10%; text-align:center;">THAN</th>
            <th style="width:12%; text-align:right;">Expected WASTE</th>
            <th style="width:13%; text-align:center;">Delivery Date</th>
            <th style="width:8%; text-align:center;">Actions</th>
          </tr>
        </thead>
        <tbody id="jwo-items-tbody">
          <!-- Populated dynamically by JS -->
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3" style="text-align:right; font-weight:800; text-transform:uppercase; color:#475569;">
              Total:
            </td>
            <td id="tfoot-total-qty" style="text-align:right; color:#2563eb; font-size:1rem;">0.00</td>
            <td id="tfoot-total-thans" style="text-align:center; color:#475569; font-size:0.95rem;">0</td>
            <td id="tfoot-total-waste" style="text-align:right; color:#d97706; font-size:0.95rem;">0.00</td>
            <td colspan="2"></td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- Empty Table State -->
    <div id="jwo-empty-banner" style="text-align:center; padding:30px 20px; color:#94a3b8; font-size:0.9rem; display:none;">
      No items added yet. Use the panel above to add delivered raw fabrics & received finished items.
    </div>

  </div>

  <!-- 4. FOOTER & SUMMARY (Handwritten Sketch 1) -->
  <div class="jwo-card">
    <div class="jwo-bottom-grid">
      
      <!-- Remarks & Sample Photo (Left) -->
      <div>
        <label class="form-label" style="font-weight:700; font-size:0.85rem; color:#334155; margin-bottom:6px;">
          Remark.
        </label>
        <textarea name="remarks" 
                  id="jwo-remarks" 
                  rows="4" 
                  class="form-control" 
                  placeholder="Enter order remarks, cutting/stitching instructions, specifications...">{{ old('remarks', $assign->remarks) }}</textarea>
      </div>

      <!-- Right Summary Card (Sketch 1) -->
      <div class="jwo-summary-card">
        <div style="font-weight:800; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.04em; color:#64748b; border-bottom:1px solid #f1f5f9; padding-bottom:6px;">
          Order Summary
        </div>

        <div class="jwo-summary-row">
          <span>T. Raw material</span>
          <span style="font-weight:800; color:#2563eb; font-size:1.05rem;" id="summary-raw-material">0.00 MTR</span>
        </div>

        <div class="jwo-summary-row">
          <span>Total Than</span>
          <span style="font-weight:800; color:#475569; font-size:1.05rem;" id="summary-total-thans">0</span>
        </div>

        <div class="jwo-summary-row">
          <span>Total Expect waste</span>
          <span style="font-weight:800; color:#d97706; font-size:1.05rem;" id="summary-total-waste">0.00 MTR</span>
        </div>

        <div class="jwo-summary-row total-row">
          <span>Total of Finished Goods</span>
          <span style="font-weight:800; color:#059669; font-size:1.25rem;" id="summary-finished-goods">0 PCS</span>
        </div>

        <div style="margin-top:14px; display:flex; justify-content:flex-end; gap:10px;">
          <button type="submit" 
                  id="btn-submit-order" 
                  class="btn btn-primary" 
                  style="width:100%; font-weight:800; padding:12px; font-size:1rem; box-shadow:0 4px 12px rgba(37,99,235,0.25);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Update Job Work Order
          </button>
        </div>

      </div>

    </div>
  </div>

  </form>
</div>

<!-- MULTIPLE THAN POPUP MODAL -->
<div class="modal fade" id="thanBreakdownModal" tabindex="-1" style="display:none; background:rgba(15,23,42,0.6); z-index:9999;" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:520px; margin: 1.75rem auto;">
    <div class="modal-content" style="border-radius:14px; border:none; box-shadow:0 20px 25px -5px rgba(0,0,0,0.2);">
      <div class="modal-header" style="background:#f8fafc; border-bottom:1px solid #e2e8f0; padding:16px 20px; border-radius:14px 14px 0 0;">
        <h5 class="modal-title" style="font-weight:800; color:#0f172a; font-size:1.1rem; display:flex; align-items:center; gap:8px;">
          Multiple Than Breakdown
        </h5>
        <button type="button" class="btn-close" onclick="closeThanModal()" style="border:none; background:transparent; font-size:1.2rem; cursor:pointer;">&times;</button>
      </div>
      <div class="modal-body" style="padding:20px;">
        <p style="font-size:0.85rem; color:#64748b; margin-top:0; margin-bottom:12px;">
          Enter individual Than meter values (separated by comma, space or newline). Example: <code>100, 105, 95.5, 109.5</code>
        </p>

        <div class="form-group" style="margin-bottom:14px;">
          <label class="form-label" style="font-weight:700; font-size:0.825rem; color:#334155;">Than Meter Values</label>
          <textarea id="than-modal-textarea" 
                    rows="5" 
                    class="form-control" 
                    placeholder="100&#10;105&#10;95.5&#10;109.5" 
                    oninput="recalculateThanModalPreview()" 
                    style="font-family:var(--font-mono, monospace); font-weight:600;"></textarea>
        </div>

        <div style="background:#f1f5f9; border-radius:8px; padding:12px 14px; display:flex; justify-content:space-between; align-items:center;">
          <div>
            <div style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:#64748b;">Total Thans</div>
            <div style="font-weight:800; font-size:1.15rem; color:#0f172a;" id="modal-thans-count">0</div>
          </div>
          <div style="text-align:right;">
            <div style="font-size:0.75rem; text-transform:uppercase; font-weight:700; color:#64748b;">Total Meters</div>
            <div style="font-weight:800; font-size:1.15rem; color:#2563eb;" id="modal-thans-meters">0.00 MTR</div>
          </div>
        </div>

      </div>
      <div class="modal-footer" style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:12px 20px; border-radius:0 0 14px 14px; display:flex; justify-content:flex-end; gap:10px;">
        <button type="button" class="btn btn-secondary" onclick="closeThanModal()" style="font-weight:700;">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="confirmThanModal()" style="font-weight:700;">
          Apply Thans
        </button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  let orderItems = @json($preloadedItems);
  let modalTargetContext = 'entry';
  let currentModalThans = [];

  document.addEventListener('DOMContentLoaded', () => {
    onJobWorkerChanged();
    renderTable();
    calculateOrderTotals();
  });

  function onJobWorkerChanged() {
    const select = document.getElementById('jwo-jobworker-select');
    const opt = select.options[select.selectedIndex];
    if (opt && opt.value) {
      document.getElementById('jwo-jobworker-name').value = opt.getAttribute('data-name') || opt.text;
    }
  }

  function calculateEntryWaste() {
    const rawQty = parseFloat(document.getElementById('entry-raw-qty').value || 0);
    const wasteMtr = rawQty * 0.015;
    document.getElementById('entry-expected-waste').value = wasteMtr.toFixed(2);
  }

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
    document.getElementById('modal-thans-meters').textContent = `${sum.toFixed(2)} MTR`;
  }

  function confirmThanModal() {
    const raw = document.getElementById('than-modal-textarea').value;
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
    }
    closeThanModal();
  }

  function clearEntryThans() {
    currentModalThans = [];
    document.getElementById('entry-than-chips-line').style.display = 'none';
  }

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

    orderItems.forEach((itm, idx) => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td style="text-align:center; font-weight:700; color:#64748b;">${idx + 1}</td>
        <td>
          <div style="font-weight:700; color:#0f172a;">${escapeHtml(itm.raw_item_name)}</div>
          <input type="hidden" name="items[${idx}][raw_item_id]" value="${itm.raw_item_id || ''}">
          <input type="hidden" name="items[${idx}][raw_item_name]" value="${escapeHtml(itm.raw_item_name)}">
          <input type="hidden" name="items[${idx}][item_name]" value="${escapeHtml(itm.finished_item_name || itm.raw_item_name)}">
          <input type="hidden" name="items[${idx}][production_pcs]" value="${itm.production_pcs || 1}">
        </td>
        <td>
          <div style="font-weight:700; color:#2563eb;">${escapeHtml(itm.finished_item_name)}</div>
          <input type="hidden" name="items[${idx}][finished_item_id]" value="${itm.finished_item_id || ''}">
          <input type="hidden" name="items[${idx}][finished_item_name]" value="${escapeHtml(itm.finished_item_name)}">
        </td>
        <td style="text-align:right;">
          <input type="number" 
                 step="0.01" 
                 class="form-control" 
                 style="text-align:right; font-weight:700; padding:4px 8px; width:100px; display:inline-block;" 
                 value="${itm.qty}" 
                 name="items[${idx}][than_meters]"
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
                 value="${itm.expected_waste}" 
                 name="items[${idx}][wastage_meters]"
                 onchange="updateRowWaste(${idx}, this.value)">
        </td>
        <td style="text-align:center;">
          <input type="date" 
                 class="form-control" 
                 style="font-size:0.8rem; padding:4px 6px; width:130px; display:inline-block;" 
                 value="${itm.delivery_date}" 
                 name="items[${idx}][delivery_date]">
        </td>
        <td style="text-align:center;">
          <button type="button" class="btn-remove-row" onclick="removeRow(${idx})" title="Remove Item">
            &times;
          </button>
        </td>
      `;
      tbody.appendChild(tr);
    });
  }

  function updateRowQty(idx, val) {
    const q = parseFloat(val) || 0;
    orderItems[idx].qty = q;
    orderItems[idx].expected_waste = parseFloat((q * 0.015).toFixed(2));
    const usableMtr = Math.max(0, q - orderItems[idx].expected_waste);
    orderItems[idx].production_pcs = Math.max(1, Math.floor(usableMtr / 1.5));
    renderTable();
    calculateOrderTotals();
  }

  function updateRowThans(idx, val) {
    orderItems[idx].than_count = parseInt(val) || 0;
    calculateOrderTotals();
  }

  function updateRowWaste(idx, val) {
    orderItems[idx].expected_waste = parseFloat(val) || 0;
    calculateOrderTotals();
  }

  function removeRow(idx) {
    orderItems.splice(idx, 1);
    renderTable();
    calculateOrderTotals();
  }

  function calculateOrderTotals() {
    let sumQty = 0;
    let sumThans = 0;
    let sumWaste = 0;
    let sumPcs = 0;

    orderItems.forEach(i => {
      sumQty += (i.qty || 0);
      sumThans += (i.than_count || 0);
      sumWaste += (i.expected_waste || 0);
      sumPcs += (i.production_pcs || 0);
    });

    document.getElementById('tfoot-total-qty').textContent = sumQty.toFixed(2);
    document.getElementById('tfoot-total-thans').textContent = sumThans;
    document.getElementById('tfoot-total-waste').textContent = sumWaste.toFixed(2);

    document.getElementById('summary-raw-material').textContent = `${sumQty.toFixed(2)} MTR`;
    document.getElementById('summary-total-thans').textContent = sumThans;
    document.getElementById('summary-total-waste').textContent = `${sumWaste.toFixed(2)} MTR`;
    document.getElementById('summary-finished-goods').textContent = `${sumPcs} PCS`;
  }

  function validateBeforeSubmit(e) {
    const worker = document.getElementById('jwo-jobworker-select').value;
    if (!worker) {
      alert('Please select a Jobworker.');
      document.getElementById('jwo-jobworker-select').focus();
      e.preventDefault();
      return false;
    }

    if (orderItems.length === 0) {
      alert('Please add at least one line item to the Order Table.');
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
  window.openThanModalForEntry = typeof openThanModalForEntry !== 'undefined' ? openThanModalForEntry : null;
  window.openEntryThanModal = typeof openThanModalForEntry !== 'undefined' ? openThanModalForEntry : null;
  window.openThanModalForRow = typeof openThanModalForRow !== 'undefined' ? openThanModalForRow : null;
  window.closeThanModal = typeof closeThanModal !== 'undefined' ? closeThanModal : null;
  window.applyModalThans = typeof applyModalThans !== 'undefined' ? applyModalThans : null;
  window.addOrderLineItem = typeof addOrderLineItem !== 'undefined' ? addOrderLineItem : null;
  window.removeOrderRow = typeof removeOrderRow !== 'undefined' ? removeOrderRow : null;
</script>
@endpush
@endsection
