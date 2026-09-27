@extends('layouts.app')

@section('title', 'Edit Job Work Order - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><a href="{{ route('jobwork.assign.index') }}" style="color:inherit; text-decoration:none;">Job Work & Assign</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Edit Order {{ $assign->job_order_no }}</span></div>
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
  .than-item-card {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 14px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    transition: all 0.15s ease;
  }
  .than-item-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
  }
  .ton-row {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    padding: 8px 12px;
    transition: all 0.15s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
  }
  .ton-row:hover {
    border-color: #4338ca;
    box-shadow: 0 2px 8px rgba(67, 56, 202, 0.1);
  }
</style>
@endpush

@section('content')
<form action="{{ route('jobwork.assign.update', $assign->id) }}" method="POST" id="jw-form">
  @csrf
  @method('PUT')

  <div style="display:flex; flex-direction:column; gap:22px;">
    
    <!-- Top Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
      <div>
        <div style="display:flex; align-items:center; gap:10px;">
          <h2 style="margin:0; font-size:1.45rem; font-weight:800; color:var(--slate-900); letter-spacing:-0.02em;">
            Edit Job Work Order
          </h2>
          <span style="font-family:var(--font-mono, monospace); font-weight:800; font-size:0.95rem; background:#eef2ff; color:#4338ca; padding:3px 10px; border-radius:8px; border:1px solid #c7d2fe;">
            {{ $assign->job_order_no }}
          </span>
        </div>
        <p style="margin:4px 0 0; font-size:0.85rem; color:var(--slate-500);">
          Modify Raw Materials, purchase thans, target Finished Items, pieces yield, wastage and contract rates.
        </p>
      </div>

      <div style="display:flex; gap:10px; align-items:center;">
        <a href="{{ route('jobwork.assign.index') }}" class="btn btn-secondary" style="font-weight:700;">Cancel</a>
        <button type="submit" id="save-jw-btn" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:8px; font-weight:700; box-shadow:0 2px 8px rgba(79, 70, 229, 0.3); padding:10px 22px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
          Update Job Order
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
        <span class="badge" style="background:#f1f5f9; color:#334155; border:1px solid #cbd5e1; font-weight:700;">
          Status: {{ $assign->status }}
        </span>
      </div>

      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:18px;">
        
        <!-- Job Worker Contractor -->
        <div class="form-group" style="margin-bottom:0;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800); margin:0;">
              Job Worker Contractor <span style="color:#ef4444;">*</span>
            </label>
            <a href="{{ route('masters.jobworkers.index') }}" target="_blank" style="font-size:0.75rem; color:#4338ca; text-decoration:none; font-weight:600;">+ Manage Workers</a>
          </div>
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
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            Process / Operation <span style="color:#ef4444;">*</span>
          </label>
          <select name="process_name" id="ja_process" class="form-control" required>
            <option value="Cutting" {{ $assign->process_name === 'Cutting' ? 'selected' : '' }}>Fabric Cutting</option>
            <option value="Stitching" {{ $assign->process_name === 'Stitching' ? 'selected' : '' }}>Stitching & Assembly</option>
            <option value="Embroidery" {{ $assign->process_name === 'Embroidery' ? 'selected' : '' }}>Embroidery & Design</option>
            <option value="Washing & Finishing" {{ $assign->process_name === 'Washing & Finishing' ? 'selected' : '' }}>Washing & Finishing</option>
            <option value="Ironing & Packing" {{ $assign->process_name === 'Ironing & Packing' ? 'selected' : '' }}>Ironing & Packing</option>
          </select>
        </div>

        <!-- Lot Reference Number -->
        <div class="form-group" style="margin-bottom:0;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800); margin:0;">
              Lot Reference # <span style="color:#ef4444;">*</span>
            </label>
            <button type="button" onclick="autoGenerateLotNo()" style="font-size:0.75rem; color:#4338ca; background:#eef2ff; border:1px solid #c7d2fe; border-radius:6px; padding:2px 8px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:4px;" title="Regenerate unique lot number">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
              Auto Gen
            </button>
          </div>
          <input type="text" name="lot_number" id="ja_lot_number" class="form-control" required value="{{ $assign->lot_number }}" style="font-family:var(--font-mono, monospace); font-weight:800; color:#4338ca; background:#f8fafc; border-color:#c7d2fe;">
        </div>

        <!-- Issue Date -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            Issue Date <span style="color:#ef4444;">*</span>
          </label>
          <input type="date" name="issue_date" class="form-control" required value="{{ $assign->issue_date }}">
        </div>

        <!-- Target Due Date -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            Target Due Date
          </label>
          <input type="date" name="due_date" class="form-control" value="{{ $assign->due_date }}">
        </div>

        <!-- Order Status -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            Order Status
          </label>
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

    <!-- 2. Raw Items, Purchase Thans & Finished Output Section -->
    <div style="display:flex; flex-direction:column; gap:16px;">
      
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div>
          <h3 style="font-size:1.05rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin:0; color:var(--slate-900); display:flex; align-items:center; gap:8px;">
            <span style="width:26px; height:26px; border-radius:6px; background:#ecfdf5; color:#059669; display:inline-flex; align-items:center; justify-content:center; font-size:0.8rem; font-weight:800;">2</span>
            Raw Materials, Purchase Thans & Finished Product Yield
          </h3>
          <p style="margin:3px 0 0; font-size:0.825rem; color:var(--slate-500);">
            Modify Raw Material & selected purchase thans, change target Finished Item, and view automated wastage & finished pieces calculation.
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
          <textarea name="instructions" class="form-control" rows="5" placeholder="Specify cutting patterns, seam stitch density (SPI), thread color matching, packaging details and return delivery deadline...">{{ $assign->instructions }}</textarea>
        </div>

        <!-- Calculated Summary Box -->
        <div style="background:#f8fafc; border:1px solid var(--slate-200, #e2e8f0); border-radius:14px; padding:20px; display:flex; flex-direction:column; gap:12px;">
          <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#64748b;">
            <span>Total Raw Materials:</span>
            <span style="font-weight:700; color:#0f172a;" id="summary-items-count">1 Item</span>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#64748b;">
            <span>Total Purchase Thans:</span>
            <span style="font-weight:700; color:#0f172a;" id="summary-than-count">0 Thans</span>
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

    <!-- Bottom Action Bar -->
    <div style="display:flex; justify-content:flex-end; align-items:center; gap:12px; padding:16px 24px; background:#ffffff; border:1px solid var(--slate-200, #e2e8f0); border-radius:var(--radius-xl, 16px); box-shadow:var(--shadow-sm); margin-top:4px;">
      <a href="{{ route('jobwork.assign.index') }}" class="btn btn-secondary" style="font-weight:700; padding:10px 20px;">Cancel</a>
      <button type="submit" id="update-jw-btn-bottom" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:8px; font-weight:700; box-shadow:0 2px 8px rgba(79, 70, 229, 0.3); padding:10px 24px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Update Job Order
      </button>
    </div>

  </div>
</form>

@endsection

@php
  $formattedExistingItems = [];
  if ($assign->items && count($assign->items) > 0) {
      foreach ($assign->items as $it) {
          $rawThans = $it->than_list;
          $cons = (float)($it->avg_consumption ?: 1.5);
          $thans = [];
          if (!empty($rawThans) && is_array($rawThans)) {
              foreach ($rawThans as $t) {
                  if (is_array($t)) {
                      $m = (float)($t['meter'] ?? $t['meters'] ?? 0);
                      $w = isset($t['wastage']) ? (float)$t['wastage'] : (isset($t['wastage_meters']) ? (float)$t['wastage_meters'] : null);
                      if ($w !== null && $w > 0) {
                          $avail = max(0, $m - $w);
                          $p = isset($t['pieces']) ? (int)$t['pieces'] : ($cons > 0 ? (int)floor($avail / $cons) : 0);
                          $u = isset($t['usable']) ? (float)$t['usable'] : ($p * $cons);
                          $w = max(0, $m - $u);
                      } else {
                          $p = isset($t['pieces']) ? (int)$t['pieces'] : ($cons > 0 ? (int)floor($m / $cons) : 0);
                          $u = isset($t['usable']) ? (float)$t['usable'] : ($p * $cons);
                          $w = max(0, $m - $u);
                      }
                      $uid = $t['unique_id'] ?? (!empty($t['po_id']) && !empty($t['po_item_id']) && !empty($t['purchase_than_no']) ? "po_{$t['po_id']}_item_{$t['po_item_id']}_than_" . ($t['purchase_than_no'] - 1) : null);
                      $thans[] = [
                          'meter' => $m,
                          'wastage' => round($w, 2),
                          'usable' => round($u, 2),
                          'pieces' => $p,
                          'unique_id' => $uid,
                          'po_id' => $t['po_id'] ?? null,
                          'po_item_id' => $t['po_item_id'] ?? null,
                          'po_number' => $t['po_number'] ?? null,
                          'challan_no' => $t['challan_no'] ?? null,
                          'purchase_than_no' => $t['purchase_than_no'] ?? ($t['than_no'] ?? null),
                      ];
                  } else {
                      $m = (float)$t;
                      $p = $cons > 0 ? (int)floor($m / $cons) : 0;
                      $u = $p * $cons;
                      $w = max(0, $m - $u);
                      $thans[] = [
                          'meter' => $m,
                          'wastage' => round($w, 2),
                          'usable' => round($u, 2),
                          'pieces' => $p
                      ];
                  }
              }
          }

          $rawName = $it->raw_item_name ?: ($it->rawItem ? $it->rawItem->name : ($it->item_name ?: ''));
          $rawId = $it->raw_item_id ?: ($it->rawItem ? $it->rawItem->id : ($it->item_id ?: ''));
          $finName = $it->finished_item_name ?: ($it->finishedItem ? $it->finishedItem->name : ($it->item_name ?: ''));
          $finId = $it->finished_item_id ?: ($it->finishedItem ? $it->finishedItem->id : '');

          $formattedExistingItems[] = [
              'raw_item_id' => $rawId ? (string)$rawId : '',
              'raw_item_name' => $rawName,
              'finished_item_id' => $finId ? (string)$finId : '',
              'finished_item_name' => $finName,
              'consumption_per_pc' => $cons,
              'rate_per_piece' => (float)($it->rate_per_piece ?: ($assign->rate_per_piece ?: 25.0)),
              'thans' => $thans
          ];
      }
  } else {
      $rawThans = $assign->than_list;
      if (empty($rawThans) && $assign->total_than_meters > 0) {
          $rawThans = [(float)$assign->total_than_meters];
      }
      $cons = 1.5;
      $thans = [];
      if (!empty($rawThans) && is_array($rawThans)) {
          foreach ($rawThans as $t) {
              if (is_array($t)) {
                  $m = (float)($t['meter'] ?? $t['meters'] ?? 0);
                  $w = isset($t['wastage']) ? (float)$t['wastage'] : (isset($t['wastage_meters']) ? (float)$t['wastage_meters'] : null);
                  if ($w !== null && $w > 0) {
                      $avail = max(0, $m - $w);
                      $p = isset($t['pieces']) ? (int)$t['pieces'] : ($cons > 0 ? (int)floor($avail / $cons) : 0);
                      $u = isset($t['usable']) ? (float)$t['usable'] : ($p * $cons);
                      $w = max(0, $m - $u);
                  } else {
                      $p = isset($t['pieces']) ? (int)$t['pieces'] : ($cons > 0 ? (int)floor($m / $cons) : 0);
                      $u = isset($t['usable']) ? (float)$t['usable'] : ($p * $cons);
                      $w = max(0, $m - $u);
                  }
                  $uid = $t['unique_id'] ?? (!empty($t['po_id']) && !empty($t['po_item_id']) && !empty($t['purchase_than_no']) ? "po_{$t['po_id']}_item_{$t['po_item_id']}_than_" . ($t['purchase_than_no'] - 1) : null);
                  $thans[] = [
                      'meter' => $m,
                      'wastage' => round($w, 2),
                      'usable' => round($u, 2),
                      'pieces' => $p,
                      'unique_id' => $uid,
                      'po_id' => $t['po_id'] ?? null,
                      'po_item_id' => $t['po_item_id'] ?? null,
                      'po_number' => $t['po_number'] ?? null,
                      'challan_no' => $t['challan_no'] ?? null,
                      'purchase_than_no' => $t['purchase_than_no'] ?? ($t['than_no'] ?? null),
                  ];
              } else {
                  $m = (float)$t;
                  $p = $cons > 0 ? (int)floor($m / $cons) : 0;
                  $u = $p * $cons;
                  $w = max(0, $m - $u);
                  $thans[] = [
                      'meter' => $m,
                      'wastage' => round($w, 2),
                      'usable' => round($u, 2),
                      'pieces' => $p
                  ];
              }
          }
      }
      $formattedExistingItems[] = [
          'raw_item_id' => '',
          'raw_item_name' => '',
          'finished_item_id' => '',
          'finished_item_name' => $assign->style_name ?: '',
          'consumption_per_pc' => 1.5,
          'rate_per_piece' => (float)($assign->rate_per_piece ?: 25.0),
          'thans' => $thans
      ];
  }
@endphp

@push('scripts')
<script>
  const masterItems = @json($items);
  const purchasedTonsMap = @json($purchasedTonsByItem);

  // Multi-Item State Preloaded from Database
  let itemsData = @json($formattedExistingItems);

  function calculateThanValues(meter, manualWastage = null, consumption = 1.5) {
    const m = Math.max(0, parseFloat(meter) || 0);
    const c = Math.max(0.0001, parseFloat(consumption) || 1.5);
    
    let p = 0;
    let u = 0;
    let w = 0;

    if (manualWastage !== null && manualWastage !== undefined && manualWastage !== '' && !isNaN(parseFloat(manualWastage))) {
      const mw = Math.max(0, parseFloat(manualWastage) || 0);
      const avail = Math.max(0, m - mw);
      p = c > 0 ? Math.floor(avail / c) : 0;
      u = p * c;
      w = Math.max(0, m - u);
    } else {
      p = c > 0 ? Math.floor(m / c) : 0;
      u = p * c;
      w = Math.max(0, m - u);
    }

    return {
      meter: Math.round(m * 100) / 100,
      wastage: Math.round(w * 100) / 100,
      usable: Math.round(u * 100) / 100,
      pieces: p
    };
  }

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
        if (!item.rate_per_piece || parseFloat(item.rate_per_piece) === 0) {
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
    if (workerSelect && workerSelect.selectedIndex >= 0) {
      const opt = workerSelect.options[workerSelect.selectedIndex];
      const r = parseFloat(opt?.getAttribute('data-rate'));
      if (r > 0) defaultRate = r;
    }

    itemsData.push({
      raw_item_id: '',
      raw_item_name: '',
      finished_item_id: '',
      finished_item_name: '',
      consumption_per_pc: 1.50,
      rate_per_piece: defaultRate,
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
      if (!confirm(`Remove Raw Item #${idx + 1} (${item.raw_item_name || 'Item'}) and its ${item.thans.length} thans?`)) {
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

    // Recalculate all Thans with new consumption ratio while preserving metadata
    const cons = itemsData[idx].consumption_per_pc;
    itemsData[idx].thans.forEach(t => {
      const calcs = calculateThanValues(t.meter, null, cons);
      Object.assign(t, calcs);
    });

    renderAllRawItemCards();
    calculateOverallTotals();
  }

  // Add a purchased ton from the available list
  function addPurchasedTon(itemIdx, tonData) {
    let meterVal = 0;
    let tonMeta = {};
    if (typeof tonData === 'object' && tonData !== null) {
      meterVal = parseFloat(tonData.meter) || 0;
      tonMeta = tonData;
    } else {
      meterVal = parseFloat(tonData) || 0;
    }
    if (isNaN(meterVal) || meterVal <= 0) return;

    // Check if this than is already selected in this raw item card
    if (tonMeta.unique_id) {
      const alreadyExists = itemsData[itemIdx].thans.some(t => t.unique_id === tonMeta.unique_id);
      if (alreadyExists) {
        return;
      }
    }

    const cons = parseFloat(itemsData[itemIdx].consumption_per_pc) || 1.5;
    const thanObj = calculateThanValues(meterVal, null, cons);
    
    if (tonMeta.unique_id) thanObj.unique_id = tonMeta.unique_id;
    if (tonMeta.po_id) thanObj.po_id = tonMeta.po_id;
    if (tonMeta.po_item_id) thanObj.po_item_id = tonMeta.po_item_id;
    if (tonMeta.po_number) thanObj.po_number = tonMeta.po_number;
    if (tonMeta.challan_no) thanObj.challan_no = tonMeta.challan_no;
    if (tonMeta.than_no) thanObj.purchase_than_no = tonMeta.than_no;
    if (tonMeta.unit) thanObj.unit = tonMeta.unit;

    itemsData[itemIdx].thans.push(thanObj);
    renderAllRawItemCards();
    calculateOverallTotals();
  }

  // Add all available purchased tons for this raw item
  function addAllPurchasedTons(itemIdx) {
    const rawId = itemsData[itemIdx].raw_item_id;
    const rawName = (itemsData[itemIdx].raw_item_name || '').toLowerCase().trim();
    const available = (rawId && purchasedTonsMap[rawId]) ? purchasedTonsMap[rawId] : (rawName && purchasedTonsMap[rawName] ? purchasedTonsMap[rawName] : []);
    
    if (available.length === 0) return;
    const cons = parseFloat(itemsData[itemIdx].consumption_per_pc) || 1.5;
    const currentUniqueIds = new Set(itemsData[itemIdx].thans.map(t => t.unique_id).filter(Boolean));

    available.forEach(t => {
      if (t.unique_id && currentUniqueIds.has(t.unique_id)) {
        return;
      }
      const thanObj = calculateThanValues(t.meter, null, cons);
      if (t.unique_id) thanObj.unique_id = t.unique_id;
      if (t.po_id) thanObj.po_id = t.po_id;
      if (t.po_item_id) thanObj.po_item_id = t.po_item_id;
      if (t.po_number) thanObj.po_number = t.po_number;
      if (t.challan_no) thanObj.challan_no = t.challan_no;
      if (t.than_no) thanObj.purchase_than_no = t.than_no;
      if (t.unit) thanObj.unit = t.unit;

      itemsData[itemIdx].thans.push(thanObj);
    });

    renderAllRawItemCards();
    calculateOverallTotals();
  }

  function addManualThan(itemIdx) {
    const cons = parseFloat(itemsData[itemIdx].consumption_per_pc) || 1.5;
    const thanObj = calculateThanValues(0, null, cons);
    itemsData[itemIdx].thans.push(thanObj);
    renderAllRawItemCards();
    calculateOverallTotals();
  }

  function removeTon(itemIdx, tonIdx) {
    itemsData[itemIdx].thans.splice(tonIdx, 1);
    renderAllRawItemCards();
    calculateOverallTotals();
  }

  function updateThanMeter(itemIdx, tonIdx, newMeter) {
    const val = parseFloat(newMeter);
    const m = (!isNaN(val) && val >= 0) ? val : 0;
    const item = itemsData[itemIdx];
    const than = item.thans[tonIdx];
    if (!than) return;

    const cons = parseFloat(item.consumption_per_pc) || 1.5;
    const updated = calculateThanValues(m, null, cons);
    Object.assign(than, updated);

    const wastageInput = document.getElementById(`than-wastage-input-${itemIdx}-${tonIdx}`);
    const usableDisp = document.getElementById(`than-usable-display-${itemIdx}-${tonIdx}`);
    const pcsDisp = document.getElementById(`than-pcs-display-${itemIdx}-${tonIdx}`);
    const usableHidden = document.getElementById(`than-usable-hidden-${itemIdx}-${tonIdx}`);
    const pcsHidden = document.getElementById(`than-pieces-hidden-${itemIdx}-${tonIdx}`);

    if (wastageInput) wastageInput.value = updated.wastage;
    if (usableDisp) usableDisp.textContent = `${updated.usable.toFixed(2)} Mtr`;
    if (pcsDisp) pcsDisp.textContent = `${updated.pieces} Pcs`;
    if (usableHidden) usableHidden.value = updated.usable;
    if (pcsHidden) pcsHidden.value = updated.pieces;

    recalcRawItemRow(itemIdx);
    calculateOverallTotals();
  }

  function updateThanWastage(itemIdx, tonIdx, newWastage) {
    const val = parseFloat(newWastage);
    const w = (!isNaN(val) && val >= 0) ? val : 0;
    const item = itemsData[itemIdx];
    const than = item.thans[tonIdx];
    if (!than) return;

    const cons = parseFloat(item.consumption_per_pc) || 1.5;
    const updated = calculateThanValues(than.meter, w, cons);
    Object.assign(than, updated);

    const usableDisp = document.getElementById(`than-usable-display-${itemIdx}-${tonIdx}`);
    const pcsDisp = document.getElementById(`than-pcs-display-${itemIdx}-${tonIdx}`);
    const usableHidden = document.getElementById(`than-usable-hidden-${itemIdx}-${tonIdx}`);
    const pcsHidden = document.getElementById(`than-pieces-hidden-${itemIdx}-${tonIdx}`);

    if (usableDisp) usableDisp.textContent = `${updated.usable.toFixed(2)} Mtr`;
    if (pcsDisp) pcsDisp.textContent = `${updated.pieces} Pcs`;
    if (usableHidden) usableHidden.value = updated.usable;
    if (pcsHidden) pcsHidden.value = updated.pieces;

    recalcRawItemRow(itemIdx);
    calculateOverallTotals();
  }

  function clearAllTons(itemIdx) {
    if (itemsData[itemIdx].thans.length === 0) return;
    if (confirm(`Clear all selected thans for Raw Item #${itemIdx + 1}?`)) {
      itemsData[itemIdx].thans = [];
      renderAllRawItemCards();
      calculateOverallTotals();
    }
  }

  function onConsumptionChange(itemIdx, val) {
    const c = parseFloat(val);
    itemsData[itemIdx].consumption_per_pc = !isNaN(c) && c > 0 ? c : 1.5;
    
    // Recalculate each Than's pieces and scrap under this new consumption
    const cons = itemsData[itemIdx].consumption_per_pc;
    itemsData[itemIdx].thans.forEach(t => {
      const calcs = calculateThanValues(t.meter, null, cons);
      Object.assign(t, calcs);
    });
    
    renderTonsForCard(itemIdx);
    recalcRawItemRow(itemIdx);
    calculateOverallTotals();
  }

  function onRateChange(itemIdx, val) {
    const r = parseFloat(val);
    itemsData[itemIdx].rate_per_piece = !isNaN(r) && r >= 0 ? r : 0;
    recalcRawItemRow(itemIdx);
    calculateOverallTotals();
  }

  // Main Yield and Quantity Recalculation for Card
  function recalcRawItemRow(itemIdx) {
    const item = itemsData[itemIdx];
    if (!item) return;

    let totalRaw = 0;
    let totalWastage = 0;
    let totalUsable = 0;
    let totalPcs = 0;

    item.thans.forEach(t => {
      totalRaw += (parseFloat(t.meter) || 0);
      totalWastage += (parseFloat(t.wastage) || 0);
      totalUsable += (parseFloat(t.usable) || 0);
      totalPcs += (parseInt(t.pieces) || 0);
    });

    const rate = parseFloat(item.rate_per_piece) || 0;
    const lineTotal = Math.round(totalPcs * rate * 100) / 100;

    // Update form elements
    const pcsInput = document.getElementById(`pieces-input-${itemIdx}`);
    if (pcsInput) pcsInput.value = totalPcs;

    const wastageInput = document.getElementById(`wastage-input-${itemIdx}`);
    if (wastageInput) wastageInput.value = totalWastage.toFixed(2);

    const hiddenRawQty = document.getElementById(`hidden-raw-qty-${itemIdx}`);
    if (hiddenRawQty) hiddenRawQty.value = totalRaw.toFixed(2);

    // Update Display Badges
    const bRawQty = document.getElementById(`card-badge-raw-qty-${itemIdx}`);
    const bThansCount = document.getElementById(`card-badge-tons-count-${itemIdx}`);
    const bPcs = document.getElementById(`card-badge-pcs-${itemIdx}`);
    const bTotal = document.getElementById(`card-badge-total-${itemIdx}`);
    const titleDisp = document.getElementById(`card-title-display-${itemIdx}`);
    const countBadge = document.getElementById(`card-step3-count-badge-${itemIdx}`);

    if (bRawQty) bRawQty.textContent = `${totalRaw.toFixed(2)} Mtr/KG`;
    if (bThansCount) bThansCount.textContent = `${item.thans.length} Thans`;
    if (bPcs) bPcs.textContent = `${totalPcs} Pcs`;
    if (bTotal) bTotal.textContent = `₹${lineTotal.toFixed(2)}`;
    if (titleDisp) titleDisp.textContent = item.raw_item_name || `Raw Material #${itemIdx + 1}`;
    if (countBadge) countBadge.textContent = `${item.thans.length} Selected`;

    const footerRawTotal = document.getElementById(`tons-total-display-${itemIdx}`);
    if (footerRawTotal) {
      footerRawTotal.innerHTML = `
        <span style="font-weight:800; color:#0f172a;">${totalRaw.toFixed(2)} Mtr/KG <span style="font-size:0.8rem; color:#64748b; font-weight:600;">(${item.thans.length} Thans)</span></span>
        <span style="color:#cbd5e1;">•</span>
        <span style="font-weight:700; color:#dc2626; font-size:0.85rem;">Wastage: ${totalWastage.toFixed(2)} Mtr</span>
        <span style="color:#cbd5e1;">•</span>
        <span style="font-weight:700; color:#059669; font-size:0.85rem;">Usable: ${totalUsable.toFixed(2)} Mtr</span>
        <span style="color:#cbd5e1;">•</span>
        <span style="font-weight:800; color:#4338ca; font-size:0.95rem;">Yield: ${totalPcs} Pcs</span>
      `;
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

      let rawOptions = `<option value="">-- Select Raw Item / Fabric --</option>`;
      masterItems.forEach(itm => {
        const isSel = (item.raw_item_name && (itm.name === item.raw_item_name || itm.id == item.raw_item_id)) ? 'selected' : '';
        rawOptions += `<option value="${itm.name}" data-id="${itm.id}" data-code="${itm.code || ''}" ${isSel}>${itm.name} [${itm.code || 'ITEM'}] (${itm.category || 'Raw Material'})</option>`;
      });

      let finishedOptions = `<option value="">-- Select Target Finished Product --</option>`;
      masterItems.forEach(itm => {
        const isSel = (item.finished_item_name && (itm.name === item.finished_item_name || itm.id == item.finished_item_id)) ? 'selected' : '';
        const rawMtr = itm.raw_meter_per_piece || 0;
        finishedOptions += `<option value="${itm.name}" data-id="${itm.id}" data-code="${itm.code || ''}" data-raw-meter="${rawMtr}" ${isSel}>${itm.name} [${itm.code || 'STYLE'}] ${rawMtr > 0 ? `(${rawMtr} Mtr/Pc)` : ''}</option>`;
      });

      const rawId = item.raw_item_id;
      const rawName = (item.raw_item_name || '').toLowerCase().trim();
      const availablePurchases = (rawId && purchasedTonsMap[rawId]) ? purchasedTonsMap[rawId] : (rawName && purchasedTonsMap[rawName] ? purchasedTonsMap[rawName] : []);
      
      const allSelectedUniqueIds = new Set();
      itemsData.forEach(itm => {
        (itm.thans || []).forEach(t => {
          if (t.unique_id) allSelectedUniqueIds.add(t.unique_id);
        });
      });

      const remainingAvailable = availablePurchases.filter(p => !p.unique_id || !allSelectedUniqueIds.has(p.unique_id));

      let purchasedTonsChipsHtml = '';
      if (!item.raw_item_name) {
        purchasedTonsChipsHtml = `
          <div style="margin-top:10px; background:#f8fafc; border:1px dashed #cbd5e1; border-radius:10px; padding:12px; text-align:center; color:#64748b; font-size:0.8rem;">
            Please select a <strong>Raw Item</strong> above to load available Purchase Thans from stock.
          </div>
        `;
      } else if (availablePurchases.length > 0) {
        purchasedTonsChipsHtml = `
          <div style="margin-top:10px; background:#ffffff; border:1px solid #cbd5e1; border-radius:10px; padding:12px 14px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; flex-wrap:wrap; gap:6px;">
              <span style="font-size:0.75rem; font-weight:800; color:#1e293b; text-transform:uppercase; display:inline-flex; align-items:center; gap:5px;">
                📦 Available Purchase Thans in Stock (${remainingAvailable.length} of ${availablePurchases.length} available):
              </span>
              ${remainingAvailable.length > 0 ? `
                <button type="button" class="btn btn-secondary btn-xs" onclick="addAllPurchasedTons(${itemIdx})" style="font-size:0.725rem; font-weight:700;">+ Select Remaining (${remainingAvailable.length})</button>
              ` : `
                <span style="font-size:0.725rem; font-weight:700; color:#059669; background:#ecfdf5; border:1px solid #a7f3d0; padding:2px 8px; border-radius:6px;">✓ All Stock Thans Selected</span>
              `}
            </div>
            <div style="display:flex; flex-wrap:wrap; gap:8px;">
              ${availablePurchases.map(p => {
                const isAdded = p.unique_id && allSelectedUniqueIds.has(p.unique_id);
                const pJson = JSON.stringify(p).replace(/"/g, '&quot;');
                if (isAdded) {
                  return `
                    <button type="button" class="purchased-ton-chip added" style="background:#ecfdf5; border-color:#6ee7b7; color:#065f46; cursor:default; font-weight:700;" title="Already added to this order" disabled>
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="color:#059669;"><polyline points="20 6 9 17 4 12"/></svg>
                      <span>${p.label} (Added)</span>
                    </button>
                  `;
                } else {
                  return `
                    <button type="button" class="purchased-ton-chip" onclick="addPurchasedTon(${itemIdx}, ${pJson})" title="Click to select ${p.meter} ${p.unit} from Challan #${p.challan_no}">
                      <span>+ ${p.label}</span>
                    </button>
                  `;
                }
              }).join('')}
            </div>
          </div>
        `;
      } else {
        purchasedTonsChipsHtml = `
          <div style="margin-top:10px; background:#fffbeb; border:1px solid #fde68a; border-radius:10px; padding:10px 14px; color:#92400e; font-size:0.8rem;">
            No purchase thans in stock found for <strong>${item.raw_item_name}</strong>. You can click <strong>+ Add Custom Than</strong> to enter thans manually.
          </div>
        `;
      }

      let totalRaw = 0;
      let totalWastage = 0;
      let totalPcs = 0;
      item.thans.forEach(t => {
        totalRaw += (parseFloat(t.meter) || 0);
        totalWastage += (parseFloat(t.wastage) || 0);
        totalPcs += (parseInt(t.pieces) || 0);
      });
      const rate = parseFloat(item.rate_per_piece) || 0;
      const lineTotal = Math.round(totalPcs * rate * 100) / 100;

      card.innerHTML = `
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
              ${item.thans.length} Thans
            </span>
            <span class="badge" style="background:#eef2ff; color:#4338ca; border:1px solid #c7d2fe; font-weight:800; font-size:0.85rem;" id="card-badge-raw-qty-${itemIdx}">
              ${totalRaw.toFixed(2)} Mtr/KG
            </span>
            <span class="badge" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; font-weight:800; font-size:0.85rem;" id="card-badge-pcs-${itemIdx}">
              ${totalPcs} Pcs
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
        <input type="hidden" id="hidden-raw-qty-${itemIdx}" name="items[${itemIdx}][than_meters]" value="${totalRaw.toFixed(2)}">

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:14px;">
          
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

        <div class="tons-container" style="margin-top:14px;">
          
          <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:10px;">
            <div style="font-weight:800; font-size:0.85rem; color:#334155; text-transform:uppercase; letter-spacing:0.04em; display:flex; align-items:center; gap:6px;">
              <span>3. Select Purchase Thans for ${item.raw_item_name || 'Raw Material'}</span>
              <span class="calc-badge" style="font-size:0.75rem;" id="card-step3-count-badge-${itemIdx}">${item.thans.length} Selected</span>
            </div>
            
            <div style="display:flex; gap:8px; align-items:center;">
              <button type="button" class="btn btn-secondary btn-xs" onclick="addManualThan(${itemIdx})" style="font-weight:700; font-size:0.75rem; color:#4338ca; border-color:#c7d2fe; background:#eef2ff;">
                + Add Custom Than
              </button>
              ${item.thans.length > 0 ? `
                <button type="button" class="btn btn-secondary btn-xs" onclick="clearAllTons(${itemIdx})" style="font-weight:700; font-size:0.75rem; color:#dc2626;">
                  Clear All Thans
                </button>
              ` : ''}
            </div>
          </div>

          <!-- Available Purchases Thans Chip List -->
          ${purchasedTonsChipsHtml}

          <!-- Empty State Box -->
          <div id="tons-empty-box-${itemIdx}" style="text-align:center; padding:18px 14px; color:#64748b; border:2px dashed #cbd5e1; border-radius:10px; background:#ffffff; margin-top:10px; ${item.thans.length > 0 ? 'display:none;' : 'display:block;'}">
            <div style="font-weight:700; font-size:0.85rem; color:#475569; margin-bottom:2px;">No Purchase Thans selected yet</div>
            <p style="font-size:0.75rem; color:#94a3b8; margin:0;">Click on the available purchase thans in stock above or click <strong>+ Add Custom Than</strong> to enter thans.</p>
          </div>

          <!-- Selected Thans Grid List -->
          <div id="tons-list-box-${itemIdx}" style="${item.thans.length > 0 ? 'display:grid;' : 'display:none;'} grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:14px; margin-top:14px; margin-bottom:14px;"></div>

          <!-- Total Raw Quantity Footer under this Raw Item -->
          <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px dashed #cbd5e1; padding-top:12px; margin-top:8px; font-size:0.875rem; flex-wrap:wrap; gap:8px;">
            <span style="font-weight:700; color:#475569;">Raw Material Summary (${item.raw_item_name || 'Raw Item'}):</span>
            <div id="tons-total-display-${itemIdx}" style="display:flex; align-items:center; flex-wrap:wrap; gap:8px;">
              <!-- Handled by recalcRawItemRow -->
            </div>
          </div>

        </div>

        <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-top:16px;">
          <div style="font-weight:800; font-size:0.85rem; color:#334155; text-transform:uppercase; letter-spacing:0.04em; margin-bottom:12px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:6px;">
            <span>4. Output Specifications & Contract Labor Rate</span>
            <span style="font-size:0.75rem; color:#64748b; font-weight:600; text-transform:none;">Formula: FLOOR(Usable / Requirement) per Than</span>
          </div>

          <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(170px, 1fr)); gap:14px;">
            
            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800); font-size:0.8rem; margin-bottom:4px;">
                Finished Product Req. (Mtr/Pc) <span style="color:#ef4444;">*</span>
              </label>
              <input type="number" step="0.001" min="0.001" id="cons-input-${itemIdx}" name="items[${itemIdx}][avg_consumption]" class="form-control" value="${item.consumption_per_pc}" style="font-weight:700; height:40px;" oninput="onConsumptionChange(${itemIdx}, this.value)">
              <small style="font-size:0.7rem; color:#64748b; margin-top:2px; display:block;">Meters required per 1 finished item</small>
            </div>

            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700; color:#dc2626; font-size:0.8rem; margin-bottom:4px;">
                Total Expected Wastage (Mtr)
              </label>
              <input type="number" step="0.01" min="0" id="wastage-input-${itemIdx}" name="items[${itemIdx}][wastage_meters]" class="form-control" readonly value="${totalWastage.toFixed(2)}" style="font-weight:800; color:#dc2626; height:40px; background:#fff1f2; border-color:#fecaca;" title="Sum of all individual than wastages">
              <small style="font-size:0.7rem; color:#64748b; margin-top:2px; display:block;">Auto-summed from all thans</small>
            </div>

            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700; color:#4338ca; font-size:0.8rem; margin-bottom:4px;">
                Total Expected Finished Pieces <span style="color:#ef4444;">*</span>
              </label>
              <div style="position:relative;">
                <input type="number" step="1" min="0" id="pieces-input-${itemIdx}" name="items[${itemIdx}][production_pcs]" class="form-control" readonly value="${totalPcs}" style="font-weight:800; color:#4338ca; padding-right:38px; background:#eef2ff; border-color:#c7d2fe; height:40px;" title="Sum of finished products from all thans">
                <span style="position:absolute; right:10px; top:50%; transform:translateY(-50%); font-size:0.75rem; color:#4338ca; font-weight:800;">Pcs</span>
              </div>
              <small style="font-size:0.7rem; color:#64748b; margin-top:2px; display:block;">Auto-summed from all thans</small>
            </div>

            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800); font-size:0.8rem; margin-bottom:4px;">
                Contractor Rate / Pc (₹) <span style="color:#ef4444;">*</span>
              </label>
              <div style="position:relative;">
                <span style="position:absolute; left:10px; top:50%; transform:translateY(-50%); font-weight:700; color:#64748b;">₹</span>
                <input type="number" step="0.5" min="0" name="items[${itemIdx}][rate_per_piece]" class="form-control" required value="${parseFloat(item.rate_per_piece).toFixed(2)}" style="padding-left:24px; font-weight:700; height:40px;" oninput="onRateChange(${itemIdx}, this.value)">
              </div>
              <small style="font-size:0.7rem; color:#64748b; margin-top:2px; display:block;">Rate per finished piece</small>
            </div>

          </div>
        </div>
      `;

      container.appendChild(card);
      renderTonsForCard(itemIdx);
      recalcRawItemRow(itemIdx);
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

    item.thans.forEach((than, tIdx) => {
      const row = document.createElement('div');
      row.className = 'than-item-card';
      const challanBadge = than.challan_no ? `
        <span style="background:#f1f5f9; color:#475569; font-size:0.75rem; font-weight:700; padding:2px 7px; border-radius:4px; border:1px solid #cbd5e1;" title="Purchase Challan Reference">
          Challan #${than.challan_no}${than.purchase_than_no ? ` (Than #${than.purchase_than_no})` : ''}
        </span>
      ` : (than.po_number ? `
        <span style="background:#f1f5f9; color:#475569; font-size:0.75rem; font-weight:700; padding:2px 7px; border-radius:4px; border:1px solid #cbd5e1;">
          PO #${than.po_number}
        </span>
      ` : '');

      row.innerHTML = `
        <input type="hidden" id="than-no-hidden-${itemIdx}-${tIdx}" name="items[${itemIdx}][thans][${tIdx}][than_no]" value="${tIdx + 1}">
        <input type="hidden" id="than-usable-hidden-${itemIdx}-${tIdx}" name="items[${itemIdx}][thans][${tIdx}][usable]" value="${than.usable}">
        <input type="hidden" id="than-pieces-hidden-${itemIdx}-${tIdx}" name="items[${itemIdx}][thans][${tIdx}][pieces]" value="${than.pieces}">
        <input type="hidden" name="items[${itemIdx}][thans][${tIdx}][unique_id]" value="${than.unique_id || ''}">
        <input type="hidden" name="items[${itemIdx}][thans][${tIdx}][po_id]" value="${than.po_id || ''}">
        <input type="hidden" name="items[${itemIdx}][thans][${tIdx}][po_item_id]" value="${than.po_item_id || ''}">
        <input type="hidden" name="items[${itemIdx}][thans][${tIdx}][po_number]" value="${than.po_number || ''}">
        <input type="hidden" name="items[${itemIdx}][thans][${tIdx}][challan_no]" value="${than.challan_no || ''}">
        <input type="hidden" name="items[${itemIdx}][thans][${tIdx}][purchase_than_no]" value="${than.purchase_than_no || (than.than_no || '')}">
        
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; flex-wrap:wrap; gap:6px;">
          <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <span style="background:#eef2ff; color:#4338ca; font-weight:800; font-size:0.85rem; padding:3px 9px; border-radius:6px; border:1px solid #c7d2fe;">
              Than #${tIdx + 1}
            </span>
            ${challanBadge}
          </div>
          <div style="display:flex; align-items:center; gap:8px;">
            <div style="display:inline-flex; align-items:center; gap:5px; background:#ecfdf5; border:1px solid #a7f3d0; padding:3px 9px; border-radius:6px;">
              <span style="font-size:0.75rem; color:#065f46; font-weight:700;">Output:</span>
              <strong style="font-size:0.9rem; color:#047857;" id="than-pcs-display-${itemIdx}-${tIdx}">${than.pieces} Pcs</strong>
            </div>
            <button type="button" onclick="removeTon(${itemIdx}, ${tIdx})" title="Remove than" style="background:#fee2e2; border:none; color:#dc2626; width:26px; height:26px; border-radius:6px; display:inline-flex; align-items:center; justify-content:center; font-size:1.15rem; font-weight:700; cursor:pointer; line-height:1; padding:0; flex-shrink:0;">&times;</button>
          </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:8px; align-items:flex-end;">
          <div>
            <label style="font-size:0.725rem; font-weight:700; color:#334155; margin-bottom:3px; display:block;">Than Qty (Mtr)</label>
            <input type="number" step="0.01" min="0" value="${than.meter}" name="items[${itemIdx}][thans][${tIdx}][meter]" 
              id="than-meter-input-${itemIdx}-${tIdx}"
              class="form-control" 
              style="font-weight:800; font-size:0.95rem; padding:5px 8px; height:38px; border-radius:6px; border:1.5px solid #cbd5e1; text-align:right; width:100%; color:#0f172a;"
              oninput="updateThanMeter(${itemIdx}, ${tIdx}, this.value)"
              onfocus="this.select()">
          </div>
          <div>
            <label style="font-size:0.725rem; font-weight:700; color:#dc2626; margin-bottom:3px; display:block;">Wastage (Mtr)</label>
            <input type="number" step="0.01" min="0" value="${than.wastage}" name="items[${itemIdx}][thans][${tIdx}][wastage]" 
              id="than-wastage-input-${itemIdx}-${tIdx}"
              class="form-control" 
              style="font-weight:800; font-size:0.95rem; padding:5px 8px; height:38px; border-radius:6px; border:1.5px solid #fecaca; background:#fffbfb; text-align:right; width:100%; color:#dc2626;"
              oninput="updateThanWastage(${itemIdx}, ${tIdx}, this.value)"
              onfocus="this.select()">
          </div>
          <div>
            <label style="font-size:0.725rem; font-weight:700; color:#059669; margin-bottom:3px; display:block;">Usable Qty</label>
            <div id="than-usable-display-${itemIdx}-${tIdx}" style="height:38px; background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:6px; display:flex; align-items:center; justify-content:flex-end; padding:0 8px; font-weight:800; color:#065f46; font-size:0.875rem;">
              ${than.usable.toFixed(2)} Mtr
            </div>
          </div>
        </div>
      `;

      listBox.appendChild(row);
    });
  }

  function calculateOverallTotals() {
    let grandTotalRaw = 0;
    let grandTotalThans = 0;
    let grandTotalWastage = 0;
    let grandTotalPcs = 0;
    let grandTotalAmount = 0;

    itemsData.forEach(item => {
      let itemRaw = 0;
      let itemWastage = 0;
      let itemPcs = 0;

      item.thans.forEach(t => {
        itemRaw += (parseFloat(t.meter) || 0);
        itemWastage += (parseFloat(t.wastage) || 0);
        itemPcs += (parseInt(t.pieces) || 0);
      });

      const itemRate = parseFloat(item.rate_per_piece) || 0;

      grandTotalRaw += itemRaw;
      grandTotalThans += item.thans.length;
      grandTotalWastage += itemWastage;
      grandTotalPcs += itemPcs;
      grandTotalAmount += (itemPcs * itemRate);
    });

    const netRaw = Math.max(0, grandTotalRaw - grandTotalWastage);

    const elItems = document.getElementById('summary-items-count');
    const elThans = document.getElementById('summary-than-count');
    const elRaw = document.getElementById('summary-total-raw-qty');
    const elWastage = document.getElementById('summary-total-wastage');
    const elNet = document.getElementById('summary-net-raw');
    const elPcs = document.getElementById('summary-total-pcs');
    const elAmount = document.getElementById('summary-grand-amount');

    if (elItems) elItems.textContent = itemsData.length + (itemsData.length === 1 ? ' Item' : ' Items');
    if (elThans) elThans.textContent = grandTotalThans + ' Thans';
    if (elRaw) elRaw.textContent = grandTotalRaw.toFixed(2) + ' Mtr/KG';
    if (elWastage) elWastage.textContent = grandTotalWastage.toFixed(2) + ' Mtr/KG';
    if (elNet) elNet.textContent = netRaw.toFixed(2) + ' Mtr/KG';
    if (elPcs) elPcs.textContent = grandTotalPcs.toLocaleString('en-IN') + ' Pcs';
    if (elAmount) elAmount.textContent = '₹' + grandTotalAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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
      
      let itemTotalPcs = 0;
      item.thans.forEach(t => {
        itemTotalPcs += (parseInt(t.pieces) || 0);
      });
      if (itemTotalPcs <= 0 && item.thans.length > 0) {
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
      alert('Please select or add at least one Than for each raw item.');
      return false;
    }

    if (hasZeroPcs) {
      e.preventDefault();
      alert('Please verify expected finished pieces quantity (Yield Pcs > 0) for each Than.');
      return false;
    }
  });

  document.addEventListener('DOMContentLoaded', () => {
    renderAllRawItemCards();
    calculateOverallTotals();
  });
</script>
@endpush
