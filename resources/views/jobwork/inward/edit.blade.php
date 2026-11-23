@extends('layouts.app')

@section('title', 'Aarambh - Edit Job Work Inward (' . $inward->inward_number . ')')

@section('breadcrumb')
  <div class="breadcrumb-item"><a href="{{ route('jobwork.assign.index') }}" style="color:inherit; text-decoration:none;">Job Work & Assign</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item"><a href="{{ route('jobwork.inward.index') }}" style="color:inherit; text-decoration:none;">Job Inward Entries</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Edit {{ $inward->inward_number }}</span></div>
@endsection

@php
  $finishedGoods = $items->filter(function($i) {
    return stripos($i->category ?? '', 'finish') !== false 
        || stripos($i->type ?? '', 'finish') !== false 
        || in_array(strtoupper($i->unit ?? ''), ['PCS', 'PIECES', 'SETS']);
  });
  if ($finishedGoods->isEmpty()) $finishedGoods = $items;

  $preloadedItems = [];
  if (!empty($inward->items_list) && is_array($inward->items_list)) {
    foreach ($inward->items_list as $itm) {
      $preloadedItems[] = [
        'item_id' => $itm['item_id'] ?? ($itm['finished_item_id'] ?? null),
        'item_name' => $itm['item_name'] ?? ($itm['finished_item_name'] ?? 'Garment Product'),
        'raw_item_name' => $itm['raw_item_name'] ?? 'Fabric',
        'job_assignment_item_id' => $itm['job_assignment_item_id'] ?? null,
        'assigned_qty' => (float)($itm['assigned_qty'] ?? 0),
        'previously_received_qty' => (float)($itm['previously_received_qty'] ?? 0),
        'received_qty' => (float)($itm['received_qty'] ?? 0),
        'than_count' => (int)($itm['than_count'] ?? (count($itm['assigned_thans'] ?? []) ?: 1)),
        'wastage_returned_meters' => (float)($itm['wastage_returned_meters'] ?? 0),
        'pending_qty' => max(0, (float)($itm['assigned_qty'] ?? 0) - ((float)($itm['previously_received_qty'] ?? 0) + (float)($itm['received_qty'] ?? 0))),
        'thans' => []
      ];
    }
  } else {
    $preloadedItems[] = [
      'item_id' => null,
      'item_name' => $inward->style_name ?: 'Garment Product',
      'raw_item_name' => 'Fabric',
      'job_assignment_item_id' => null,
      'assigned_qty' => (float)$inward->received_qty,
      'previously_received_qty' => 0,
      'received_qty' => (float)$inward->received_qty,
      'than_count' => 1,
      'wastage_returned_meters' => (float)$inward->wastage_returned_meters,
      'pending_qty' => 0,
      'thans' => []
    ];
  }
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
    grid-template-columns: repeat(12, 1fr);
    gap: 12px 14px;
    align-items: flex-end;
  }
  .jwi-col-item  { grid-column: span 7; }
  .jwi-col-waste { grid-column: span 5; }
  .jwi-col-qty   { grid-column: span 4; }
  .jwi-col-than  { grid-column: span 4; }
  .jwi-col-btn   { grid-column: span 4; }

  @media (max-width: 992px) {
    .jwi-item-entry-grid {
      grid-template-columns: repeat(6, 1fr);
    }
    .jwi-col-item  { grid-column: span 6 !important; }
    .jwi-col-waste { grid-column: span 6 !important; }
    .jwi-col-qty   { grid-column: span 3 !important; }
    .jwi-col-than  { grid-column: span 3 !important; }
    .jwi-col-btn   { grid-column: span 6 !important; }
  }
  @media (max-width: 640px) {
    .jwi-item-entry-grid {
      grid-template-columns: 1fr;
    }
    .jwi-col-item, .jwi-col-waste, .jwi-col-qty, .jwi-col-than, .jwi-col-btn {
      grid-column: span 1 !important;
    }
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
        <span>Aarambh - Edit JOB WORK INWARD ({{ $inward->inward_number }})</span>
      </div>
      <div style="display:flex; gap:10px; align-items:center;">
        <a href="{{ route('jobwork.inward.index') }}" class="btn btn-secondary" style="font-weight:600;">
          Back to List
        </a>
      </div>
    </div>

    <!-- Main Inward Form -->
    <form action="{{ route('jobwork.inward.update', $inward->id) }}" method="POST" id="job-inward-form" onsubmit="return validateBeforeSubmit(event)">
      @csrf
      @method('PUT')

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
          <select name="job_worker_id" id="jwi-jobworker-select" class="form-control" required>
            <option value="">-- Select Job worker --</option>
            @foreach($jobworkers as $jw)
              <option value="{{ $jw->id }}" 
                      data-name="{{ $jw->name }}"
                      {{ ($inward->job_worker_id == $jw->id) ? 'selected' : '' }}>
                {{ $jw->name }} {{ $jw->phone ? '('.$jw->phone.')' : '' }}
              </option>
            @endforeach
          </select>
          <input type="hidden" name="job_worker_name" id="jwi-jobworker-name" value="{{ $inward->job_worker_name }}">
        </div>

        <!-- Job Inward NO. -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.825rem; color:#334155; margin-bottom:5px;">
            Job Inward NO. <span style="color:#ef4444;">*</span>
          </label>
          <input type="text" 
                 name="inward_number" 
                 id="jwi-inward-no" 
                 class="form-control" 
                 readonly 
                 value="{{ $inward->inward_number }}" 
                 style="font-family:var(--font-mono, monospace); font-weight:700; background:#f8fafc;">
        </div>

        <!-- Job work order NO. -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.825rem; color:#334155; margin-bottom:5px;">
            Job work order NO. <span style="color:#ef4444;">*</span>
          </label>
          <input type="text" 
                 class="form-control" 
                 readonly 
                 value="{{ $inward->job_order_no ?: ($inward->jobAssignment?->job_order_no ?? 'N/A') }}" 
                 style="font-family:var(--font-mono, monospace); font-weight:700; background:#f8fafc;">
          <input type="hidden" name="job_assignment_id" value="{{ $inward->job_assignment_id }}">
          <input type="hidden" name="job_order_no" value="{{ $inward->job_order_no }}">
          <input type="hidden" name="lot_number" value="{{ $inward->lot_number }}">
          <input type="hidden" name="process_name" value="{{ $inward->process_name ?: 'Stitching / Jobwork' }}">
          <input type="hidden" name="challan_no" value="{{ $inward->challan_no }}">
          <input type="hidden" name="qc_status" value="{{ $inward->qc_status ?: 'Passed' }}">
          <input type="hidden" name="storage_location" value="{{ $inward->storage_location ?: 'Main Warehouse' }}">
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
                 value="{{ date('Y-m-d', strtotime($inward->inward_date)) }}" 
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
        
        <!-- LINE 1: ITEM recd (Finished Goods Dropdown) -->
        <div class="form-group jwi-col-item" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569; margin-bottom:4px;">
            ITEM recd (Finished Goods) <span style="color:#ef4444;">*</span>
          </label>
          <select id="entry-item-recd" class="form-control" style="font-weight:600;">
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
        </div>

        <!-- LINE 1: Actual waste (Manual number input) -->
        <div class="form-group jwi-col-waste" style="margin-bottom:0;">
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

        <!-- LINE 2: Qty with + Button -->
        <div class="form-group jwi-col-qty" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569; margin-bottom:4px; display:flex; justify-content:space-between;">
            <span>Qty <span style="color:#ef4444;">*</span></span>
            <a href="javascript:void(0)" onclick="openThanModalForEntry()" style="font-size:0.75rem; color:#2563eb; text-decoration:none;" title="Add multiple than roll readings">+ Than Modal</a>
          </label>
          <div style="display:flex; gap:6px; align-items:center;">
            <input type="number" 
                   step="0.01" 
                   min="0.01" 
                   id="entry-inward-qty" 
                   class="form-control" 
                   placeholder="0.00" 
                   style="font-weight:700; text-align:right;">
            <button type="button" 
                    class="btn-quick-add" 
                    title="Add Multiple Than Breakdown" 
                    onclick="openThanModalForEntry()"
                    style="flex-shrink:0;">
              +
            </button>
          </div>
        </div>

        <!-- LINE 2: THAN (Number input) -->
        <div class="form-group jwi-col-than" style="margin-bottom:0;">
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

        <!-- LINE 2: Add Item Button -->
        <div class="jwi-col-btn">
          <button type="button" 
                  class="btn btn-primary" 
                  onclick="addItemToInwardTable()" 
                  style="width:100%; height:38px; font-weight:700; padding:8px 18px; display:inline-flex; align-items:center; justify-content:center; gap:6px; white-space:nowrap;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add Item
          </button>
        </div>

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
    <div id="jwi-empty-banner" style="text-align:center; padding:30px 20px; color:#94a3b8; font-size:0.9rem; display:none;">
      No items in this inward entry. Select Finished Item from above and click <strong>Add Item</strong>.
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
                  placeholder="Enter inward remarks, QC notes, delivery details, etc.">{{ old('remarks', $inward->remarks) }}</textarea>
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
            Update Job Work Inward
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
  let inwardItems = @json($preloadedItems);
  let currentModalThans = [];

  document.addEventListener('DOMContentLoaded', () => {
    renderInwardTable();
    calculateInwardTotals();
  });

  function openThanModalForEntry() {
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

    currentModalThans = thans;
    if (thans.length > 0) {
      document.getElementById('entry-inward-qty').value = sum.toFixed(2);
      document.getElementById('entry-than-count').value = thans.length;
    }
    closeThanModal();
  }

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
    const qty = parseFloat(document.getElementById('entry-inward-qty').value || 0);
    const thanCount = parseInt(document.getElementById('entry-than-count').value || currentModalThans.length || 0);
    const actualWaste = parseFloat(document.getElementById('entry-actual-waste').value || 0);

    if (qty <= 0) {
      alert('Please enter a valid inward Quantity greater than 0.');
      document.getElementById('entry-inward-qty').focus();
      return;
    }

    inwardItems.push({
      item_id: recdId,
      item_name: recdName,
      raw_item_name: 'Fabric',
      job_assignment_item_id: null,
      assigned_qty: qty,
      previously_received_qty: 0,
      received_qty: qty,
      than_count: thanCount,
      wastage_returned_meters: actualWaste,
      pending_qty: 0,
      thans: [...currentModalThans]
    });

    recdSelect.value = '';
    document.getElementById('entry-inward-qty').value = '';
    document.getElementById('entry-than-count').value = '';
    document.getElementById('entry-actual-waste').value = '0.00';
    currentModalThans = [];

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
    inwardItems[idx].received_qty = parseFloat(val) || 0;
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

    document.getElementById('summary-total-recd').textContent = sumQty.toFixed(2);
    document.getElementById('summary-total-wastage').textContent = sumWaste.toFixed(2);
    const net = Math.max(0, sumQty - sumWaste);
    document.getElementById('summary-net-inward').textContent = net.toFixed(2);
  }

  function validateBeforeSubmit(e) {
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
  window.openThanModalForEntry = typeof openThanModalForEntry !== 'undefined' ? openThanModalForEntry : null;
  window.openEntryThanModal = typeof openThanModalForEntry !== 'undefined' ? openThanModalForEntry : null;
  window.openThanModalForRow = typeof openThanModalForRow !== 'undefined' ? openThanModalForRow : null;
  window.closeThanModal = typeof closeThanModal !== 'undefined' ? closeThanModal : null;
  window.applyModalThans = typeof applyModalThans !== 'undefined' ? applyModalThans : null;
  window.addInwardLineItem = typeof addInwardLineItem !== 'undefined' ? addInwardLineItem : null;
  window.removeInwardRow = typeof removeInwardRow !== 'undefined' ? removeInwardRow : null;
</script>
@endpush
@endsection
