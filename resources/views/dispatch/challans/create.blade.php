@extends('layouts.app')

@section('title', 'Sales Entry - Aarambh ERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><a href="{{ route('dispatch.challans.index') }}" style="color:inherit; text-decoration:none;">Logistics & Sales</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Sales Entry</span></div>
@endsection

@push('styles')
<style>
  .se-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
    max-width: 1380px;
    margin: 0 auto;
  }
  .se-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    padding: 20px 24px;
  }
  .se-section-title {
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
  .se-section-title .badge-num {
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
  .se-grid-header {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
  }
  .se-item-entry-panel {
    background: #f8fafc;
    border: 1.5px solid #cbd5e1;
    border-radius: 12px;
    padding: 16px 18px;
    margin-bottom: 18px;
  }
  .se-item-entry-grid {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 12px 14px;
    align-items: flex-end;
  }
  .se-col-item { grid-column: span 6; }
  .se-col-rate { grid-column: span 3; }
  .se-col-unit { grid-column: span 3; }
  .se-col-qty  { grid-column: span 3; }
  .se-col-than { grid-column: span 2; }
  .se-col-tax  { grid-column: span 2; }
  .se-col-btn  { grid-column: span 3; }
  .se-col-more { grid-column: span 2; }

  @media (max-width: 992px) {
    .se-item-entry-grid {
      grid-template-columns: repeat(6, 1fr);
    }
    .se-col-item { grid-column: span 6 !important; }
    .se-col-rate { grid-column: span 3 !important; }
    .se-col-unit { grid-column: span 3 !important; }
    .se-col-qty  { grid-column: span 3 !important; }
    .se-col-than { grid-column: span 3 !important; }
    .se-col-tax  { grid-column: span 2 !important; }
    .se-col-btn  { grid-column: span 2 !important; }
    .se-col-more { grid-column: span 2 !important; }
  }
  @media (max-width: 640px) {
    .se-item-entry-grid {
      grid-template-columns: 1fr;
    }
    .se-col-item, .se-col-rate, .se-col-unit, .se-col-qty, .se-col-than, .se-col-tax, .se-col-btn, .se-col-more {
      grid-column: span 1 !important;
    }
  }
  .se-table th {
    background: #f1f5f9 !important;
    color: #475569 !important;
    font-weight: 700 !important;
    font-size: 0.8rem !important;
    text-transform: uppercase !important;
    letter-spacing: 0.03em !important;
    padding: 10px 12px !important;
    border-bottom: 2px solid #cbd5e1 !important;
  }
  .se-table td {
    padding: 10px 12px !important;
    font-size: 0.85rem !important;
    vertical-align: middle !important;
    border-bottom: 1px solid #e2e8f0 !important;
  }
  .se-table tbody tr:hover {
    background-color: #f8fafc !important;
  }
  .se-summary-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 18px 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .se-summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.875rem;
    color: #475569;
  }
  .se-summary-row.total {
    border-top: 2px dashed #cbd5e1;
    padding-top: 10px;
    margin-top: 4px;
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
  }
  .badge-category-raw {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
  }
  .badge-category-finished {
    background: #eff6ff;
    color: #2563eb;
    border: 1px solid #bfdbfe;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
  }
  .unit-tag {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
  }
  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
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
    max-width: 820px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
    overflow: hidden;
    animation: popIn 0.18s ease-out;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
  }
  @keyframes popIn {
    from { transform: scale(0.96); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
  }
</style>
@endpush

@section('content')
<div class="se-container">

  <!-- Main Sales Form -->
  <form id="sales-form" action="{{ route('dispatch.challans.store') }}" method="POST">
    @csrf

    <!-- Page Header Bar -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:18px;">
      <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:44px; height:44px; border-radius:10px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; box-shadow:0 1px 3px rgba(37,99,235,0.15);">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
        </div>
        <div>
          <div style="display:flex; align-items:center; gap:8px;">
            <h1 style="font-size:1.35rem; font-weight:800; color:#0f172a; margin:0; letter-spacing:-0.02em;">Sales Entry</h1>
            <span style="font-size:0.75rem; font-weight:700; background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; padding:2px 8px; border-radius:20px;">
              Sales = Raw + Finished
            </span>
          </div>
          <p style="font-size:0.825rem; color:#64748b; margin:2px 0 0;">
            Handwritten format: Customer, Bill, Date, Transport, Bilty, Multiple Thans & Bulk Item selection.
          </p>
        </div>
      </div>

      <div style="display:flex; gap:10px; align-items:center;">
        <a href="{{ route('dispatch.challans.index') }}" class="btn btn-secondary" style="font-weight:700; padding:8px 16px;">
          Cancel
        </a>
        <button type="submit" class="btn btn-primary" id="save-sales-btn" style="background:#2563eb; font-weight:700; padding:8px 22px; display:inline-flex; align-items:center; gap:8px; box-shadow:0 2px 6px rgba(37,99,235,0.3);">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          Save Sales Entry
        </button>
      </div>
    </div>

    <!-- 1. SALES HEADER SECTION (Handwritten Layout) -->
    <div class="se-card" style="margin-bottom:20px;">
      <div class="se-section-title">
        <span class="badge-num">1</span>
        <span>Sales Header</span>
      </div>

      <div class="se-grid-header">
        
        <!-- Customer Master Dropdown -->
        <div class="form-group" style="margin-bottom:0;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:5px;">
            <label class="form-label" style="font-weight:700; font-size:0.825rem; color:#334155; margin:0;">
              Customer (Master) <span style="color:#ef4444;">*</span>
            </label>
            <a href="{{ route('masters.customers.index') }}" target="_blank" style="font-size:0.75rem; color:#2563eb; text-decoration:none; font-weight:600;">
              + Add New
            </a>
          </div>
          <select name="customer_name" id="sales-customer-select" class="form-control" required style="font-weight:600;" onchange="handleCustomerSelect(this)">
            <option value="">-- Select Customer (Master) --</option>
            @foreach($customers as $c)
              <option value="{{ $c->name }}" 
                      data-id="{{ $c->id }}" 
                      data-phone="{{ $c->phone ?? '' }}" 
                      data-gstin="{{ $c->gst_number ?? '' }}" 
                      data-city="{{ $c->city ?? '' }}"
                      data-address="{{ $c->address ?? '' }}">
                {{ $c->name }} {{ $c->city ? '('.$c->city.')' : '' }}
              </option>
            @endforeach
          </select>
          <input type="hidden" name="customer_id" id="sales-customer-id" value="">
        </div>

        <!-- Bill / Order Ref # -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.825rem; color:#334155; margin-bottom:5px;">
            Bill # (Auto) <span style="color:#ef4444;">*</span>
          </label>
          <div style="position:relative; display:flex; align-items:center;">
            <input type="text" 
                   name="order_no" 
                   id="sales-order-no" 
                   class="form-control" 
                   required 
                   value="{{ old('order_no', $nextChallanNo ?? ('DC-' . date('Y') . '-' . str_pad(rand(10, 999), 3, '0', STR_PAD_LEFT))) }}" 
                   placeholder="e.g. DC-2026-001" 
                   style="font-family:var(--font-mono, monospace); font-weight:700; padding-right:55px;">
            <span style="position:absolute; right:8px; font-size:0.65rem; font-weight:800; background:#e2e8f0; color:#475569; padding:2px 6px; border-radius:4px; text-transform:uppercase;">
              Auto
            </span>
          </div>
        </div>

        <!-- Sales Date -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.825rem; color:#334155; margin-bottom:5px;">
            Date <span style="color:#ef4444;">*</span>
          </label>
          <input type="date" 
                 name="dispatch_date" 
                 id="sales-dispatch-date" 
                 class="form-control" 
                 required 
                 value="{{ date('Y-m-d') }}" 
                 style="font-weight:600;">
        </div>

        <!-- Transport Name -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.825rem; color:#334155; margin-bottom:5px;">
            Transport
          </label>
          <input type="text" 
                 name="transporter_name" 
                 id="sales-transport-name" 
                 class="form-control" 
                 required
                 placeholder="Transport / Carrier Name" 
                 value="V-Trans Logistics Ltd.">
        </div>

        <!-- Bilty Number -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.825rem; color:#334155; margin-bottom:5px;">
            Bilty #
          </label>
          <input type="text" 
                 name="bilty_no" 
                 id="sales-bilty-no" 
                 class="form-control" 
                 placeholder="Bilty / LR Number" 
                 style="font-family:var(--font-mono, monospace); font-weight:700;">
        </div>

      </div>
    </div>

    <!-- 2. INLINE ITEM ENTRY BAR & BULK ADD BUTTON -->
    <div class="se-card" style="margin-bottom:20px;">
      <div class="se-section-title" style="justify-content:space-between;">
        <div style="display:flex; align-items:center; gap:8px;">
          <span class="badge-num">2</span>
          <span>Item Entry (Raw Fabric + Finished Garments)</span>
        </div>
        <div>
          <button type="button" class="btn btn-sm" onclick="openBulkItemsModal()" style="background:#f0fdf4; color:#166534; border:1.5px solid #86efac; font-weight:700; display:inline-flex; align-items:center; gap:6px;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M8 12h8"/><path d="M12 8v8"/></svg>
            Add Multiple Items Modal
          </button>
        </div>
      </div>

      <!-- Quick Inline Entry Panel -->
      <div class="se-item-entry-panel">
        <div class="se-item-entry-grid">
          
          <!-- LINE 1: Item Master Dropdown (Raw + Finished) -->
          <div class="form-group se-col-item" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569; margin-bottom:4px;">
              Item (Item Master: Raw + Finished)
            </label>
            <select id="entry-item-select" class="form-control" style="font-weight:600;" onchange="handleEntryItemSelect(this)">
              <option value="">-- Select Item (Raw / Finished) --</option>
              @foreach($items as $itm)
                @php
                  $isRaw = stripos($itm->category ?? '', 'raw') !== false 
                        || stripos($itm->type ?? '', 'raw') !== false 
                        || stripos($itm->category ?? '', 'fabric') !== false 
                        || in_array(strtoupper($itm->unit ?? ''), ['MTR', 'METERS', 'METER', 'KG', 'YDS']);
                  $catBadge = $isRaw ? '[Raw]' : '[Finished]';
                @endphp
                <option value="{{ $itm->id }}" 
                        data-name="{{ $itm->name }}" 
                        data-code="{{ $itm->code }}" 
                        data-category="{{ $itm->category ?? ($isRaw ? 'Raw Material' : 'Finished Goods') }}"
                        data-type="{{ $catBadge }}"
                        data-unit="{{ $itm->unit ?? ($isRaw ? 'MTR' : 'PCS') }}" 
                        data-cost="{{ $itm->unit_cost ?? 0 }}"
                        data-stock="{{ $itm->current_stock ?? 0 }}">
                  {{ $catBadge }} {{ $itm->name }} ({{ $itm->code }}) • Stock: {{ (float)($itm->current_stock ?? 0) }} {{ $itm->unit ?? 'MTR' }}
                </option>
              @endforeach
            </select>
          </div>

          <!-- LINE 1: Rate -->
          <div class="form-group se-col-rate" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569; margin-bottom:4px;">
              Rate (₹)
            </label>
            <input type="number" 
                   id="entry-rate" 
                   class="form-control" 
                   step="0.01" 
                   min="0" 
                   placeholder="0.00" 
                   style="font-weight:700;" 
                   oninput="calculateEntryPreview()">
          </div>

          <!-- LINE 1: Unit Dropdown -->
          <div class="form-group se-col-unit" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569; margin-bottom:4px;">
              Unit
            </label>
            <select id="entry-unit" class="form-control" style="font-weight:600;">
              <option value="MTR" selected>MTR</option>
              <option value="PCS">PCS</option>
              <option value="KG">KG</option>
              <option value="YDS">YDS</option>
              <option value="SET">SET</option>
            </select>
          </div>

          <!-- LINE 2: Qty -->
          <div class="form-group se-col-qty" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569; margin-bottom:4px;">
              Qty
            </label>
            <input type="number" 
                   id="entry-qty" 
                   class="form-control" 
                   step="0.01" 
                   min="0" 
                   placeholder="0" 
                   style="font-weight:800; color:#059669;" 
                   oninput="calculateEntryPreview()">
          </div>

          <!-- LINE 2: Than Modal Button (+) -->
          <div class="form-group se-col-than" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569; margin-bottom:4px; display:flex; justify-content:space-between;">
              <span>Multiple Than</span>
            </label>
            <button type="button" 
                    id="entry-than-btn" 
                    class="btn btn-secondary w-full" 
                    onclick="openEntryThanModal()" 
                    style="width:100%; height:38px; font-weight:700; padding:8px 10px; background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; display:inline-flex; align-items:center; justify-content:center; gap:6px;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              <span id="entry-than-btn-text">Than</span>
            </button>
          </div>

          <!-- LINE 2: Tax % -->
          <div class="form-group se-col-tax" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; font-size:0.8rem; color:#475569; margin-bottom:4px;">
              Tax %
            </label>
            <select id="entry-tax" class="form-control" style="font-weight:700;" onchange="calculateEntryPreview()">
              <option value="5" selected>5%</option>
              <option value="12">12%</option>
              <option value="18">18%</option>
              <option value="0">0%</option>
            </select>
          </div>

          <!-- LINE 2: Add Item Button -->
          <div class="se-col-btn">
            <button type="button" 
                    id="btn-add-item" 
                    class="btn btn-primary" 
                    onclick="addItemToSalesTable()" 
                    style="width:100%; height:38px; font-weight:700; padding:8px 16px; background:#2563eb; display:inline-flex; align-items:center; justify-content:center; gap:6px; white-space:nowrap;">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Add Item
            </button>
          </div>

          <!-- LINE 2: Add More Button (Opens Modal) -->
          <div class="se-col-more">
            <button type="button" 
                    class="btn btn-secondary" 
                    onclick="openBulkItemsModal()" 
                    style="width:100%; height:38px; font-weight:700; padding:8px 14px; background:#f8fafc; color:#0f172a; border:1px solid #cbd5e1; display:inline-flex; align-items:center; justify-content:center; gap:6px; white-space:nowrap;"
                    title="Add multiple items together via popup modal">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="8" height="8" x="3" y="3" rx="1"/><rect width="8" height="8" x="13" y="3" rx="1"/><rect width="8" height="8" x="3" y="13" rx="1"/><rect width="8" height="8" x="13" y="13" rx="1"/></svg>
              Add more
            </button>
          </div>

        </div>

        <!-- Live Row Preview calculation banner -->
        <div id="entry-thans-summary-line" style="display:none; margin-top:10px; padding:6px 12px; background:#eff6ff; border-radius:6px; font-size:0.8rem; color:#1e40af; align-items:center; justify-content:space-between;">
          <div>
            <span style="font-weight:700;">Than Breakdown:</span>
            <span id="entry-thans-count-display">0 Thans</span> • Total: <span id="entry-thans-meters-display" style="font-weight:800;">0.00 MTR</span>
          </div>
          <button type="button" onclick="clearEntryThans()" style="background:none; border:none; color:#ef4444; font-size:0.75rem; font-weight:700; cursor:pointer;">
            Clear Than
          </button>
        </div>
      </div>

      <!-- 3. LINE ITEMS TABLE (Handwritten Format) -->
      <div class="table-responsive" style="border:1px solid #e2e8f0; border-radius:10px; overflow:hidden;">
        <table class="table se-table" id="sales-items-table" style="width:100%; margin-bottom:0;">
          <thead>
            <tr>
              <th style="width:50px; text-align:center;">S.No.</th>
              <th style="width:28%;">Item Name</th>
              <th style="width:11%; text-align:right;">Rate (₹)</th>
              <th style="width:11%; text-align:right;">Qty</th>
              <th style="width:8%; text-align:center;">Unit</th>
              <th style="width:11%; text-align:center;">Than Detail</th>
              <th style="width:8%; text-align:center;">Tax %</th>
              <th style="width:11%; text-align:right;">Amt (₹)</th>
              <th style="width:11%; text-align:right;">Tax Amt (₹)</th>
              <th style="width:12%; text-align:right;">Total (₹)</th>
              <th style="width:60px; text-align:center;">Actions</th>
            </tr>
          </thead>
          <tbody id="sales-items-tbody">
            <!-- Dynamic items rendered via JavaScript -->
          </tbody>
          <tfoot id="sales-table-footer" style="background:#f8fafc; font-weight:800; border-top:2px solid #cbd5e1;">
            <tr>
              <td colspan="3" style="text-align:right; font-size:0.875rem; color:#334155;">Total:</td>
              <td id="footer-total-qty" style="text-align:right; color:#059669; font-size:0.95rem;">0.00</td>
              <td colspan="3"></td>
              <td id="footer-total-amt" style="text-align:right; color:#1e293b; font-size:0.9rem;">₹0.00</td>
              <td id="footer-total-tax" style="text-align:right; color:#dc2626; font-size:0.9rem;">₹0.00</td>
              <td id="footer-grand-total" style="text-align:right; color:#059669; font-size:1.05rem;">₹0.00</td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>

      <!-- Empty state banner -->
      <div id="table-empty-banner" style="text-align:center; padding:32px 16px; color:#94a3b8; background:#fff;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom:8px; opacity:0.6;"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/><path d="M12 8v8"/></svg>
        <div style="font-weight:700; font-size:0.95rem; color:#64748b;">No Sales Items Added Yet</div>
        <div style="font-size:0.8rem; margin-top:4px;">Select an item above and click "Add Item", or click "Add more" to select multiple items from catalog.</div>
      </div>

    </div>

    <!-- 4. BOTTOM SECTION: Remarks & Summary Calculations -->
    <div style="display:grid; grid-template-columns:1.3fr 1fr; gap:20px; align-items:flex-start;">
      
      <!-- Remarks & Logistic Details -->
      <div class="se-card">
        <div class="se-section-title">
          <span class="badge-num">3</span>
          <span>Remarks & Dispatch Notes</span>
        </div>

        <div class="form-group" style="margin-bottom:14px;">
          <label class="form-label" style="font-weight:700; font-size:0.825rem; color:#334155;">Remarks</label>
          <textarea name="remarks" 
                    id="sales-remarks" 
                    class="form-control" 
                    rows="3" 
                    placeholder="Enter sales terms, delivery instructions, packaging notes or bilty remarks..."></textarea>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; font-size:0.825rem; color:#334155;">Vehicle #</label>
            <input type="text" name="vehicle_number" class="form-control" placeholder="e.g. MH-04-AB-1234">
          </div>
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; font-size:0.825rem; color:#334155;">Challan Status</label>
            <select name="status" class="form-control" style="font-weight:600;">
              <option value="In Transit" selected>In Transit</option>
              <option value="Delivered">Delivered</option>
              <option value="Pending">Pending</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Live Totals & Unit Summary Box -->
      <div class="se-card">
        <div class="se-section-title">
          <span class="badge-num">4</span>
          <span>Sales Total Summary</span>
        </div>

        <div class="se-summary-box">
          <!-- Unit Breakdown line -->
          <div style="padding-bottom:8px; border-bottom:1px solid #e2e8f0;">
            <div style="font-size:0.75rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:0.04em; margin-bottom:6px;">
              Quantity by Unit
            </div>
            <div id="unit-summary-tags" style="display:flex; flex-wrap:wrap; gap:8px;">
              <span class="unit-tag">MTR: 0.00</span>
            </div>
          </div>

          <div class="se-summary-row">
            <span>Subtotal (Amount):</span>
            <span id="summary-subtotal" style="font-weight:700; color:#1e293b;">₹0.00</span>
          </div>

          <div class="se-summary-row">
            <span>Total Tax (GST):</span>
            <span id="summary-tax" style="font-weight:700; color:#dc2626;">₹0.00</span>
          </div>

          <div class="se-summary-row total">
            <span>Total Amount:</span>
            <span id="summary-grand-total" style="color:#059669; font-size:1.35rem;">₹0.00</span>
          </div>
        </div>

        <!-- Hidden aggregate fields submitted to controller -->
        <input type="hidden" name="total_qty" id="hidden-total-qty" value="0">
        <input type="hidden" name="total_thans" id="hidden-total-thans" value="0">
        <input type="hidden" name="subtotal_amount" id="hidden-subtotal-amount" value="0">
        <input type="hidden" name="tax_amount" id="hidden-tax-amount" value="0">
        <input type="hidden" name="total_amount" id="hidden-total-amount" value="0">

      </div>

    </div>

  </form>

</div>

<!-- =========================================================================
     MODAL 1: ADD MULTIPLE ITEMS POPUP MODAL ("Add multiple then pop up")
     ========================================================================= -->
<div class="modal-overlay" id="bulk-items-modal" style="display:none;">
  <div class="modal-box" style="max-width:920px;">
    
    <!-- Modal Header -->
    <div style="padding:18px 24px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; background:#f8fafc;">
      <div>
        <h3 style="margin:0; font-size:1.1rem; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:8px;">
          <span>📦 Add Multiple Items (Sales Catalog: Raw + Finished)</span>
        </h3>
        <p style="margin:4px 0 0; font-size:0.8rem; color:#64748b;">
          Select multiple items from Raw Materials and Finished Goods, specify rate/quantity, and add all into the sales bill at once.
        </p>
      </div>
      <button type="button" onclick="closeBulkItemsModal()" style="background:none; border:none; color:#64748b; font-size:1.5rem; cursor:pointer; line-height:1;">
        &times;
      </button>
    </div>

    <!-- Modal Controls: Search & Category Filter -->
    <div style="padding:14px 24px; background:#fff; border-bottom:1px solid #f1f5f9; display:flex; gap:12px; flex-wrap:wrap; align-items:center; justify-content:space-between;">
      <div style="position:relative; flex:1; min-width:240px;">
        <input type="text" 
               id="bulk-search-input" 
               class="form-control" 
               placeholder="Search items by name, code or fabric..." 
               oninput="filterBulkModalItems()" 
               style="padding-left:36px; font-size:0.85rem;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" style="position:absolute; left:12px; top:50%; transform:translateY(-50%);">
          <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
      </div>

      <div style="display:flex; gap:6px;">
        <button type="button" class="btn btn-sm btn-filter active" id="filter-btn-all" onclick="setBulkFilter('all')" style="font-size:0.75rem; font-weight:700; background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe;">
          All Items
        </button>
        <button type="button" class="btn btn-sm btn-filter" id="filter-btn-raw" onclick="setBulkFilter('raw')" style="font-size:0.75rem; font-weight:700; background:#f8fafc; color:#475569; border:1px solid #e2e8f0;">
          Raw Material
        </button>
        <button type="button" class="btn btn-sm btn-filter" id="filter-btn-finished" onclick="setBulkFilter('finished')" style="font-size:0.75rem; font-weight:700; background:#f8fafc; color:#475569; border:1px solid #e2e8f0;">
          Finished Goods
        </button>
      </div>
    </div>

    <!-- Modal Items Table -->
    <div style="padding:0; overflow-y:auto; flex:1; max-height:450px;">
      <table class="table se-table" style="width:100%; margin:0; font-size:0.825rem;">
        <thead style="position:sticky; top:0; z-index:10;">
          <tr>
            <th style="width:40px; text-align:center;">
              <input type="checkbox" id="bulk-select-all-cb" onchange="toggleSelectAllBulk(this)" style="cursor:pointer; width:16px; height:16px;">
            </th>
            <th style="width:34%;">Item Name & Code</th>
            <th style="width:12%; text-align:center;">Type</th>
            <th style="width:12%; text-align:center;">Stock</th>
            <th style="width:14%;">Rate (₹)</th>
            <th style="width:14%;">Qty</th>
            <th style="width:14%;">Tax %</th>
          </tr>
        </thead>
        <tbody id="bulk-modal-tbody">
          @foreach($items as $idx => $itm)
            @php
              $isRaw = stripos($itm->category ?? '', 'raw') !== false 
                    || stripos($itm->type ?? '', 'raw') !== false 
                    || stripos($itm->category ?? '', 'fabric') !== false 
                    || in_array(strtoupper($itm->unit ?? ''), ['MTR', 'METERS', 'METER', 'KG', 'YDS']);
              $itemCatType = $isRaw ? 'raw' : 'finished';
              $badgeClass = $isRaw ? 'badge-category-raw' : 'badge-category-finished';
              $badgeText = $isRaw ? 'Raw' : 'Finished';
            @endphp
            <tr class="bulk-item-row" data-type="{{ $itemCatType }}" data-search="{{ strtolower($itm->name . ' ' . $itm->code . ' ' . ($itm->category ?? '')) }}">
              <td style="text-align:center;">
                <input type="checkbox" 
                       class="bulk-row-cb" 
                       id="bulk-cb-{{ $itm->id }}" 
                       data-id="{{ $itm->id }}" 
                       data-name="{{ $itm->name }}" 
                       data-code="{{ $itm->code }}" 
                       data-category="{{ $itm->category ?? ($isRaw ? 'Raw Material' : 'Finished Goods') }}"
                       data-unit="{{ $itm->unit ?? ($isRaw ? 'MTR' : 'PCS') }}" 
                       data-type-label="{{ $badgeText }}"
                       onchange="onBulkRowCheckChange(this)">
              </td>
              <td>
                <label for="bulk-cb-{{ $itm->id }}" style="font-weight:700; color:#0f172a; margin:0; cursor:pointer; display:block;">
                  {{ $itm->name }}
                </label>
                <span style="font-family:var(--font-mono, monospace); font-size:0.75rem; color:#64748b;">
                  {{ $itm->code }}
                </span>
              </td>
              <td style="text-align:center;">
                <span class="{{ $badgeClass }}">{{ $badgeText }}</span>
              </td>
              <td style="text-align:center; font-weight:700; color:#334155;">
                {{ (float)($itm->current_stock ?? 0) }} <span style="font-size:0.7rem; color:#64748b;">{{ $itm->unit ?? 'MTR' }}</span>
              </td>
              <td>
                <input type="number" 
                       step="0.01" 
                       min="0" 
                       class="form-control bulk-input-rate" 
                       id="bulk-rate-{{ $itm->id }}" 
                       value="{{ (float)($itm->unit_cost ?? 0) }}" 
                       style="padding:4px 8px; font-size:0.8rem; font-weight:700;">
              </td>
              <td>
                <input type="number" 
                       step="0.01" 
                       min="0" 
                       class="form-control bulk-input-qty" 
                       id="bulk-qty-{{ $itm->id }}" 
                       placeholder="Qty" 
                       value="" 
                       style="padding:4px 8px; font-size:0.8rem; font-weight:700; color:#059669;">
              </td>
              <td>
                <select class="form-control bulk-input-tax" id="bulk-tax-{{ $itm->id }}" style="padding:4px 6px; font-size:0.8rem; font-weight:600;">
                  <option value="5" selected>5%</option>
                  <option value="12">12%</option>
                  <option value="18">18%</option>
                  <option value="0">0%</option>
                </select>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <!-- Modal Footer -->
    <div style="padding:14px 24px; border-top:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; background:#f8fafc;">
      <div>
        <span id="bulk-selected-count" style="font-weight:700; font-size:0.85rem; color:#2563eb;">0 items selected</span>
      </div>
      <div style="display:flex; gap:10px;">
        <button type="button" class="btn btn-secondary" onclick="closeBulkItemsModal()" style="font-weight:700;">
          Cancel
        </button>
        <button type="button" class="btn btn-primary" onclick="confirmBulkAdd()" style="font-weight:700; background:#2563eb; display:inline-flex; align-items:center; gap:6px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          Confirm / Add Selected Items
        </button>
      </div>
    </div>

  </div>
</div>

<!-- =========================================================================
     MODAL 2: THAN / ROLL BREAKDOWN MODAL (Multiple Than / Roll Entry)
     ========================================================================= -->
<div class="modal-overlay" id="than-modal" style="display:none;">
  <div class="modal-box" style="max-width:540px;">
    
    <div style="padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; background:#f8fafc;">
      <h4 style="margin:0; font-size:1rem; font-weight:800; color:#0f172a;" id="than-modal-title">
        📦 Multiple Than / Roll Meter Breakdown
      </h4>
      <button type="button" onclick="closeThanModal()" style="background:none; border:none; color:#64748b; font-size:1.4rem; cursor:pointer;">
        &times;
      </button>
    </div>

    <div style="padding:18px 20px;">
      <p style="font-size:0.8rem; color:#64748b; margin-top:0; margin-bottom:10px;">
        Enter meters for each than separated by commas, spaces or newlines (e.g. <code>100, 110.5, 95</code>).
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
        Apply to Qty
      </button>
    </div>

  </div>
</div>

@endsection

@push('scripts')
<script>
  // State: items in current sales entry
  let billItems = [];
  let currentModalThans = [];
  let modalTargetContext = 'entry'; // 'entry' or row index
  let activeBulkCategory = 'all';

  // Customer selection auto-fill
  function handleCustomerSelect(select) {
    const opt = select.options[select.selectedIndex];
    const customerId = opt ? opt.getAttribute('data-id') : '';
    document.getElementById('sales-customer-id').value = customerId || '';
  }

  // Quick item select auto-populate
  function handleEntryItemSelect(select) {
    const opt = select.options[select.selectedIndex];
    if (!opt || !select.value) return;

    const cost = parseFloat(opt.getAttribute('data-cost') || 0);
    const unit = opt.getAttribute('data-unit') || 'MTR';
    
    if (cost > 0) {
      document.getElementById('entry-rate').value = cost.toFixed(2);
    }
    document.getElementById('entry-unit').value = unit;
    calculateEntryPreview();
  }

  function calculateEntryPreview() {
    // Optional live hook
  }

  // Multiple Than Modal Handlers
  function openEntryThanModal() {
    modalTargetContext = 'entry';
    document.getElementById('than-modal-title').textContent = '📦 Entry Than / Roll Meter Readings';
    document.getElementById('modal-than-textarea').value = currentModalThans.length > 0 ? currentModalThans.join(', ') : '';
    updateModalThanPreview();
    document.getElementById('than-modal').style.display = 'flex';
  }

  function openRowThanModal(index) {
    modalTargetContext = index;
    const item = billItems[index];
    if (!item) return;

    document.getElementById('than-modal-title').textContent = `📦 Than Readings for: ${item.item_name}`;
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
        document.getElementById('entry-qty').value = sum.toFixed(2);
        document.getElementById('entry-than-btn-text').textContent = `${thans.length} Thans`;
        
        const summaryLine = document.getElementById('entry-thans-summary-line');
        if (summaryLine) {
          summaryLine.style.display = 'flex';
          document.getElementById('entry-thans-count-display').textContent = `${thans.length} Thans`;
          document.getElementById('entry-thans-meters-display').textContent = `${sum.toFixed(2)} MTR`;
        }
      }
    } else {
      const idx = modalTargetContext;
      if (billItems[idx]) {
        billItems[idx].thans = thans;
        billItems[idx].than_count = thans.length;
        if (thans.length > 0) {
          billItems[idx].qty = sum;
        }
        recalculateRow(idx);
        renderTable();
        calculateBillTotals();
      }
    }

    closeThanModal();
  }

  function clearEntryThans() {
    currentModalThans = [];
    document.getElementById('entry-than-btn-text').textContent = 'Than';
    const summaryLine = document.getElementById('entry-thans-summary-line');
    if (summaryLine) summaryLine.style.display = 'none';
  }

  // Adding single inline item to table
  function addItemToSalesTable() {
    const select = document.getElementById('entry-item-select');
    const itemId = select.value;
    const opt = select.options[select.selectedIndex];

    if (!itemId) {
      alert('Please select an item from the Item Master.');
      select.focus();
      return;
    }

    const itemName = opt.getAttribute('data-name') || opt.text;
    const itemCode = opt.getAttribute('data-code') || '';
    const category = opt.getAttribute('data-category') || 'Finished Goods';
    const typeLabel = opt.getAttribute('data-type') || '';
    const rate = parseFloat(document.getElementById('entry-rate').value || 0);
    const qty = parseFloat(document.getElementById('entry-qty').value || 0);
    const unit = document.getElementById('entry-unit').value || 'MTR';
    const taxPct = parseFloat(document.getElementById('entry-tax').value || 0);

    if (qty <= 0) {
      alert('Please enter a valid quantity greater than 0.');
      document.getElementById('entry-qty').focus();
      return;
    }

    if (rate < 0) {
      alert('Please enter a valid rate.');
      document.getElementById('entry-rate').focus();
      return;
    }

    const amount = Math.round(rate * qty * 100) / 100;
    const taxAmount = Math.round((amount * taxPct / 100.0) * 100) / 100;
    const total = Math.round((amount + taxAmount) * 100) / 100;

    billItems.push({
      item_id: itemId,
      item_name: itemName,
      item_code: itemCode,
      category: category,
      type_label: typeLabel,
      rate: rate,
      qty: qty,
      unit: unit,
      tax_percent: taxPct,
      amount: amount,
      tax_amount: taxAmount,
      total: total,
      thans: [...currentModalThans],
      than_count: currentModalThans.length
    });

    // Reset entry inputs
    select.value = '';
    document.getElementById('entry-rate').value = '';
    document.getElementById('entry-qty').value = '';
    clearEntryThans();

    renderTable();
    calculateBillTotals();
  }

  // =========================================================================
  // BULK MODAL FUNCTIONALITY ("Add multiple then pop up")
  // =========================================================================
  function openBulkItemsModal() {
    document.getElementById('bulk-search-input').value = '';
    filterBulkModalItems();
    updateBulkSelectedCount();
    document.getElementById('bulk-items-modal').style.display = 'flex';
  }

  function closeBulkItemsModal() {
    document.getElementById('bulk-items-modal').style.display = 'none';
  }

  function setBulkFilter(cat) {
    activeBulkCategory = cat;
    document.querySelectorAll('.btn-filter').forEach(b => {
      b.style.background = '#f8fafc';
      b.style.color = '#475569';
      b.style.borderColor = '#e2e8f0';
    });
    const activeBtn = document.getElementById('filter-btn-' + cat);
    if (activeBtn) {
      activeBtn.style.background = '#eff6ff';
      activeBtn.style.color = '#2563eb';
      activeBtn.style.borderColor = '#bfdbfe';
    }
    filterBulkModalItems();
  }

  function filterBulkModalItems() {
    const q = (document.getElementById('bulk-search-input').value || '').toLowerCase().trim();
    const rows = document.querySelectorAll('.bulk-item-row');

    rows.forEach(r => {
      const type = r.getAttribute('data-type');
      const searchTxt = r.getAttribute('data-search') || '';
      
      const matchesCat = (activeBulkCategory === 'all') || (type === activeBulkCategory);
      const matchesSearch = !q || searchTxt.includes(q);

      r.style.display = (matchesCat && matchesSearch) ? '' : 'none';
    });
  }

  function toggleSelectAllBulk(masterCb) {
    const checked = masterCb.checked;
    const visibleRows = document.querySelectorAll('.bulk-item-row:not([style*="display: none"])');

    visibleRows.forEach(r => {
      const cb = r.querySelector('.bulk-row-cb');
      if (cb) {
        cb.checked = checked;
        const id = cb.getAttribute('data-id');
        const qtyInput = document.getElementById('bulk-qty-' + id);
        if (checked && (!qtyInput.value || parseFloat(qtyInput.value) <= 0)) {
          qtyInput.value = '1';
        }
      }
    });

    updateBulkSelectedCount();
  }

  function onBulkRowCheckChange(cb) {
    const id = cb.getAttribute('data-id');
    const qtyInput = document.getElementById('bulk-qty-' + id);
    if (cb.checked && (!qtyInput.value || parseFloat(qtyInput.value) <= 0)) {
      qtyInput.value = '1';
    }
    updateBulkSelectedCount();
  }

  function updateBulkSelectedCount() {
    const checkedCount = document.querySelectorAll('.bulk-row-cb:checked').length;
    document.getElementById('bulk-selected-count').textContent = `${checkedCount} items selected`;
  }

  function confirmBulkAdd() {
    const checkedBoxes = document.querySelectorAll('.bulk-row-cb:checked');
    if (checkedBoxes.length === 0) {
      alert('Please check at least one item to add.');
      return;
    }

    let addedCount = 0;

    checkedBoxes.forEach(cb => {
      const id = cb.getAttribute('data-id');
      const name = cb.getAttribute('data-name');
      const code = cb.getAttribute('data-code');
      const category = cb.getAttribute('data-category');
      const unit = cb.getAttribute('data-unit') || 'MTR';
      const typeLabel = cb.getAttribute('data-type-label') || '';

      const rate = parseFloat(document.getElementById('bulk-rate-' + id)?.value || 0);
      const qty = parseFloat(document.getElementById('bulk-qty-' + id)?.value || 0);
      const taxPct = parseFloat(document.getElementById('bulk-tax-' + id)?.value || 5);

      if (qty > 0) {
        const amount = Math.round(rate * qty * 100) / 100;
        const taxAmount = Math.round((amount * taxPct / 100.0) * 100) / 100;
        const total = Math.round((amount + taxAmount) * 100) / 100;

        billItems.push({
          item_id: id,
          item_name: name,
          item_code: code,
          category: category,
          type_label: typeLabel,
          rate: rate,
          qty: qty,
          unit: unit,
          tax_percent: taxPct,
          amount: amount,
          tax_amount: taxAmount,
          total: total,
          thans: [],
          than_count: 0
        });

        addedCount++;
        // uncheck
        cb.checked = false;
      }
    });

    if (addedCount === 0) {
      alert('Please specify a Quantity greater than 0 for selected items.');
      return;
    }

    closeBulkItemsModal();
    renderTable();
    calculateBillTotals();
  }

  // =========================================================================
  // TABLE RENDERING & TOTALS
  // =========================================================================
  function renderTable() {
    const tbody = document.getElementById('sales-items-tbody');
    const emptyBanner = document.getElementById('table-empty-banner');
    tbody.innerHTML = '';

    if (billItems.length === 0) {
      emptyBanner.style.display = 'block';
      return;
    }

    emptyBanner.style.display = 'none';

    billItems.forEach((item, index) => {
      const tr = document.createElement('tr');
      const isRaw = item.type_label?.includes('Raw') || item.category?.toLowerCase().includes('raw') || item.category?.toLowerCase().includes('fabric');
      const badgeCls = isRaw ? 'badge-category-raw' : 'badge-category-finished';
      const badgeTxt = isRaw ? 'Raw' : 'Finished';

      let thanBtnHtml = '-';
      if (item.thans && item.thans.length > 0) {
        thanBtnHtml = `<button type="button" class="btn btn-secondary btn-xs" onclick="openRowThanModal(${index})" style="font-weight:700; background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; font-size:0.75rem; padding:3px 8px;">
          ${item.thans.length} Thans 📝
        </button>`;
      } else {
        thanBtnHtml = `<button type="button" class="btn btn-secondary btn-xs" onclick="openRowThanModal(${index})" style="font-size:0.75rem; padding:3px 8px; color:#64748b;">
          + Than
        </button>`;
      }

      tr.innerHTML = `
        <td style="text-align:center; font-weight:700; color:#64748b;">${index + 1}</td>
        <td>
          <div style="font-weight:700; color:#0f172a; display:flex; align-items:center; gap:6px;">
            <span>${item.item_name}</span>
            <span class="${badgeCls}">${badgeTxt}</span>
          </div>
          <div style="font-family:var(--font-mono, monospace); font-size:0.75rem; color:#64748b;">
            ${item.item_code || ''}
          </div>
          <!-- Hidden inputs for form submit -->
          <input type="hidden" name="items[${index}][item_id]" value="${item.item_id}">
          <input type="hidden" name="items[${index}][style_name]" value="${item.item_name}">
          <input type="hidden" name="items[${index}][item_category]" value="${item.category || (isRaw ? 'Raw Material' : 'Finished Goods')}">
          <input type="hidden" name="items[${index}][unit]" value="${item.unit}">
          <input type="hidden" name="items[${index}][than_count]" value="${item.thans?.length || 0}">
          <input type="hidden" name="items[${index}][thans_data]" value='${JSON.stringify(item.thans || [])}'>
          <input type="hidden" name="items[${index}][batch_no]" value="SALE-LOT">
        </td>
        <td style="text-align:right;">
          <input type="number" step="0.01" min="0" 
                 name="items[${index}][rate]" 
                 class="form-control" 
                 value="${item.rate}" 
                 oninput="handleRowRateChange(${index}, this.value)" 
                 style="text-align:right; font-weight:700; width:95px; display:inline-block; padding:4px 6px;">
        </td>
        <td style="text-align:right;">
          <input type="number" step="0.01" min="0.01" 
                 name="items[${index}][qty]" 
                 class="form-control" 
                 value="${item.qty}" 
                 oninput="handleRowQtyChange(${index}, this.value)" 
                 style="text-align:right; font-weight:800; color:#059669; width:95px; display:inline-block; padding:4px 6px;">
        </td>
        <td style="text-align:center;">
          <span class="unit-tag">${item.unit}</span>
        </td>
        <td style="text-align:center;">
          ${thanBtnHtml}
        </td>
        <td style="text-align:center;">
          <select name="items[${index}][tax_percent]" 
                  class="form-control" 
                  onchange="handleRowTaxChange(${index}, this.value)" 
                  style="font-weight:700; width:72px; display:inline-block; padding:4px 6px; font-size:0.8rem;">
            <option value="5" ${item.tax_percent == 5 ? 'selected' : ''}>5%</option>
            <option value="12" ${item.tax_percent == 12 ? 'selected' : ''}>12%</option>
            <option value="18" ${item.tax_percent == 18 ? 'selected' : ''}>18%</option>
            <option value="0" ${item.tax_percent == 0 ? 'selected' : ''}>0%</option>
          </select>
        </td>
        <td style="text-align:right; font-weight:700; color:#1e293b;">
          ₹${item.amount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
          <input type="hidden" name="items[${index}][subtotal]" value="${item.amount}">
        </td>
        <td style="text-align:right; font-weight:700; color:#dc2626;">
          ₹${item.tax_amount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
          <input type="hidden" name="items[${index}][tax_amount]" value="${item.tax_amount}">
        </td>
        <td style="text-align:right; font-weight:800; color:#059669;">
          ₹${item.total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
          <input type="hidden" name="items[${index}][total_amount]" value="${item.total}">
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

  function handleRowRateChange(index, val) {
    const rate = parseFloat(val) || 0;
    billItems[index].rate = rate;
    recalculateRow(index);
    updateRowDisplay(index);
    calculateBillTotals();
  }

  function handleRowQtyChange(index, val) {
    const qty = parseFloat(val) || 0;
    billItems[index].qty = qty;
    recalculateRow(index);
    updateRowDisplay(index);
    calculateBillTotals();
  }

  function handleRowTaxChange(index, val) {
    const tax = parseFloat(val) || 0;
    billItems[index].tax_percent = tax;
    recalculateRow(index);
    updateRowDisplay(index);
    calculateBillTotals();
  }

  function recalculateRow(index) {
    const item = billItems[index];
    const amount = Math.round(item.rate * item.qty * 100) / 100;
    const taxAmount = Math.round((amount * item.tax_percent / 100.0) * 100) / 100;
    const total = Math.round((amount + taxAmount) * 100) / 100;

    item.amount = amount;
    item.tax_amount = taxAmount;
    item.total = total;
  }

  function updateRowDisplay(index) {
    // Quick re-render to update amounts
    renderTable();
  }

  function deleteRow(index) {
    billItems.splice(index, 1);
    renderTable();
    calculateBillTotals();
  }

  function calculateBillTotals() {
    let totalQty = 0;
    let totalSubtotal = 0;
    let totalTax = 0;
    let grandTotal = 0;
    let totalThans = 0;
    const unitMap = {};

    billItems.forEach(item => {
      totalQty += item.qty;
      totalSubtotal += item.amount;
      totalTax += item.tax_amount;
      grandTotal += item.total;
      totalThans += (item.thans ? item.thans.length : 0);

      const u = item.unit || 'MTR';
      unitMap[u] = (unitMap[u] || 0) + item.qty;
    });

    // Update footer row
    document.getElementById('footer-total-qty').textContent = totalQty.toFixed(2);
    document.getElementById('footer-total-amt').textContent = '₹' + totalSubtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('footer-total-tax').textContent = '₹' + totalTax.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('footer-grand-total').textContent = '₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    // Update summary card
    document.getElementById('summary-subtotal').textContent = '₹' + totalSubtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('summary-tax').textContent = '₹' + totalTax.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('summary-grand-total').textContent = '₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    // Unit breakdown tags
    const unitContainer = document.getElementById('unit-summary-tags');
    unitContainer.innerHTML = '';
    const unitKeys = Object.keys(unitMap);
    if (unitKeys.length === 0) {
      unitContainer.innerHTML = '<span class="unit-tag">MTR: 0.00</span>';
    } else {
      unitKeys.forEach(k => {
        const span = document.createElement('span');
        span.className = 'unit-tag';
        span.textContent = `${k}: ${unitMap[k].toFixed(2)}`;
        unitContainer.appendChild(span);
      });
    }

    // Hidden inputs
    document.getElementById('hidden-total-qty').value = totalQty.toFixed(2);
    document.getElementById('hidden-total-thans').value = totalThans;
    document.getElementById('hidden-subtotal-amount').value = totalSubtotal.toFixed(2);
    document.getElementById('hidden-tax-amount').value = totalTax.toFixed(2);
    document.getElementById('hidden-total-amount').value = grandTotal.toFixed(2);
  }

  // Form submit validation
  document.getElementById('sales-form')?.addEventListener('submit', function(e) {
    if (billItems.length === 0) {
      e.preventDefault();
      alert('Please add at least one line item to the Sales Entry.');
      return false;
    }
    const customer = document.getElementById('sales-customer-select').value;
    if (!customer) {
      e.preventDefault();
      alert('Please select a Customer.');
      document.getElementById('sales-customer-select').focus();
      return false;
    }
  });

  // Ensure modal and action handlers are globally accessible on window
  window.openEntryThanModal = openEntryThanModal;
  window.openRowThanModal = openRowThanModal;
  window.closeThanModal = closeThanModal;
  window.applyModalThans = applyModalThans;
  window.updateModalThanPreview = updateModalThanPreview;
  window.openBulkItemModal = openBulkItemModal;
  window.closeBulkItemModal = closeBulkItemModal;
  window.applyBulkSelectedItems = applyBulkSelectedItems;
  window.addEntryLineItem = addEntryLineItem;
  window.deleteItem = deleteItem;
  window.editItem = editItem;

  // Initialize
  document.addEventListener('DOMContentLoaded', function() {
    renderTable();
    calculateBillTotals();
  });
</script>
@endpush
