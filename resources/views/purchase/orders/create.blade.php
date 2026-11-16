@extends('layouts.app')

@section('title', 'New Purchase Entry - Aarambh ERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><a href="{{ route('purchase.orders.index') }}" style="color:inherit; text-decoration:none;">Purchase Entries</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>New Purchase Entry</span></div>
@endsection

@push('styles')
<style>
  .pe-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
    max-width: 1380px;
    margin: 0 auto;
  }
  .pe-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    padding: 20px 24px;
  }
  .pe-section-title {
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
  .pe-section-title .badge-num {
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
  .pe-grid-header {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
  }
  .pe-item-entry-panel {
    background: #f8fafc;
    border: 1.5px solid #cbd5e1;
    border-radius: 12px;
    padding: 16px 18px;
    margin-bottom: 18px;
  }
  .pe-item-entry-grid {
    display: grid;
    grid-template-columns: 2.2fr 1fr 1.1fr 1fr 0.9fr 1.1fr auto;
    gap: 12px;
    align-items: flex-end;
  }
  @media (max-width: 1100px) {
    .pe-item-entry-grid {
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    }
  }
  .pe-table th {
    background: #f1f5f9 !important;
    color: #475569 !important;
    font-weight: 700 !important;
    font-size: 0.8rem !important;
    text-transform: uppercase !important;
    letter-spacing: 0.03em !important;
    padding: 10px 12px !important;
    border-bottom: 2px solid #cbd5e1 !important;
  }
  .pe-table td {
    padding: 10px 12px !important;
    font-size: 0.85rem !important;
    vertical-align: middle !important;
    border-bottom: 1px solid #e2e8f0 !important;
  }
  .pe-table tbody tr:hover {
    background-color: #f8fafc !important;
  }
  .pe-summary-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 18px 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .unit-summary-pill {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 6px 12px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.8rem;
    font-weight: 700;
    color: #334155;
  }
  .than-tag {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1e40af;
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
  }
</style>
@endpush

@section('content')
<form action="{{ route('purchase.orders.store') }}" method="POST" id="pe-form">
  @csrf

  <div class="pe-container">
    
    <!-- Top Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
      <div>
        <h2 style="margin:0; font-size:1.45rem; font-weight:800; color:var(--slate-900); letter-spacing:-0.02em;">
          Purchase Entry
        </h2>
        <p style="margin:3px 0 0; font-size:0.85rem; color:var(--slate-500);">
          Tally format purchase: select vendor, bill details, fabric rolls (thans), rates and multi-unit auto-totals.
        </p>
      </div>

      <div style="display:flex; gap:10px; align-items:center;">
        <a href="{{ route('purchase.orders.index') }}" class="btn btn-secondary" style="font-weight:700;">Cancel</a>
        <button type="submit" id="save-pe-btn" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:8px; font-weight:700; box-shadow:0 2px 8px rgba(37, 99, 235, 0.3); padding:10px 22px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
          Save Purchase Entry
        </button>
      </div>
    </div>

    <!-- 1. PURCHASE HEADER (Matching Reference Sketch: Vendor, Bill, Type, Date, Transport, Bilty No) -->
    <div class="pe-card">
      <div class="pe-section-title">
        <span class="badge-num">1</span>
        <span>Purchase Header & Transport Details</span>
      </div>

      <div class="pe-grid-header">
        
        <!-- Vendor Master Dropdown with Add Option -->
        <div class="form-group" style="margin-bottom:0;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800); margin:0;">
              Vendor [Master] <span style="color:#ef4444;">*</span>
            </label>
            <a href="{{ route('masters.vendors.index') }}" target="_blank" style="font-size:0.75rem; color:#2563eb; text-decoration:none; font-weight:700;" title="Open Vendor Master to register new supplier">+ Add New</a>
          </div>
          <select name="vendor_name" id="vendor-select" class="form-control" required onchange="onVendorChange(this)" style="font-weight:600; font-size:0.875rem;">
            <option value="">-- Select Vendor / Supplier --</option>
            @forelse($vendors as $v)
              <option value="{{ $v->name }}" data-id="{{ $v->id }}" data-code="{{ $v->code }}" data-city="{{ $v->city ?? '' }}" {{ (request('vendor_id') == $v->id) ? 'selected' : '' }}>
                {{ $v->name }} ({{ $v->code ?: 'Vendor' }}){{ !empty($v->city) ? ' - '.$v->city : '' }}
              </option>
            @empty
              <option value="" disabled>No vendors found in Vendor Master</option>
            @endforelse
          </select>
          <input type="hidden" name="vendor_id" id="vendor_id" value="{{ request('vendor_id') }}">
        </div>

        <!-- Purchase Bill Number (Challan No) -->
        <div class="form-group" style="margin-bottom:0;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800); margin:0;">
              Purchase Bill / Challan No. <span style="color:#ef4444;">*</span>
            </label>
            <button type="button" onclick="autoGenerateChallanNo()" style="font-size:0.75rem; color:#2563eb; background:#eff6ff; border:1px solid #bfdbfe; border-radius:6px; padding:2px 8px; font-weight:700; cursor:pointer;" title="Generate unique bill number">
              Auto
            </button>
          </div>
          <input type="text" name="challan_no" id="challan_no" class="form-control" required value="{{ old('challan_no', $nextChallanNo ?? '') }}" placeholder="e.g. 1076 / BILL-9821" style="font-weight:700; letter-spacing:0.5px;">
        </div>

        <!-- Purchase Type -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            Purchase Type <span style="color:#ef4444;">*</span>
          </label>
          <select name="purchase_type" id="purchase_type" class="form-control" style="font-weight:600; font-size:0.875rem;">
            <option value="Fabric / Raw Material" selected>Fabric / Raw Material</option>
            <option value="Grey Fabric">Grey Fabric</option>
            <option value="Finished Goods">Finished Goods</option>
            <option value="Trims & Accessories">Trims & Accessories</option>
            <option value="Packaging Material">Packaging Material</option>
            <option value="Job Work Return">Job Work Return</option>
          </select>
        </div>

        <!-- Purchase Date -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            Purchase Date <span style="color:#ef4444;">*</span>
          </label>
          <input type="date" name="po_date" id="po_date" class="form-control" required value="{{ date('Y-m-d') }}" style="font-weight:600;">
        </div>

        <!-- Transport Name -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            Transport Name
          </label>
          <input type="text" name="transport_name" id="transport_name" class="form-control" placeholder="e.g. VRL Logistics, SafeExpress" value="{{ old('transport_name') }}" style="font-weight:600;">
        </div>

        <!-- Bilty Number -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            Bilty No. / LR No.
          </label>
          <input type="text" name="bilty_no" id="bilty_no" class="form-control" placeholder="e.g. BLTY-88412" value="{{ old('bilty_no') }}" style="font-weight:600;">
        </div>

      </div>
    </div>

    <!-- 2. ITEMS SECTION: ITEM ENTRY PANEL + PURCHASE ITEMS TABLE -->
    <div class="pe-card">
      <div class="pe-section-title">
        <span class="badge-num">2</span>
        <span>Items & Multi-Than Breakdown (Tally Format)</span>
      </div>

      <!-- Item Entry Input Row (Matching Handwritten Reference Sketch) -->
      <div class="pe-item-entry-panel">
        <div style="font-size:0.8rem; font-weight:800; color:#475569; text-transform:uppercase; letter-spacing:0.04em; margin-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
          <span>⚡ Quick Item Entry</span>
          <span style="font-size:0.75rem; color:#64748b; font-weight:600;">Select item, enter rate & quantity (or click <strong>[+] Add multiple than</strong> for roll meter entry)</span>
        </div>

        <div class="pe-item-entry-grid">
          
          <!-- Fabric / Item Dropdown -->
          <div>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
              <label class="form-label" style="font-size:0.75rem; font-weight:700; margin:0; color:#334155;">Fabric / Item (Item Master) <span style="color:#ef4444;">*</span></label>
              <a href="{{ route('masters.items.index') }}" target="_blank" style="font-size:0.7rem; color:#2563eb; text-decoration:none; font-weight:600;">+ New Item</a>
            </div>
            <select id="entry-item-select" class="form-control" onchange="onEntryItemSelect(this)" style="font-weight:700; font-size:0.85rem;">
              <option value="">-- Select Fabric / Item --</option>
              @foreach($items as $itm)
                <option value="{{ $itm->id }}" data-name="{{ $itm->name }}" data-code="{{ $itm->code }}" data-unit="{{ $itm->unit ?? 'MTR' }}" data-cost="{{ $itm->unit_cost ?? 0 }}" data-stock="{{ $itm->current_stock ?? 0 }}">
                  {{ $itm->name }} ({{ $itm->code ?: 'SKU' }}) [{{ $itm->unit ?? 'MTR' }}]
                </option>
              @endforeach
            </select>
          </div>

          <!-- Rate Input -->
          <div>
            <label class="form-label" style="font-size:0.75rem; font-weight:700; margin-bottom:4px; color:#334155;">Rate (₹) <span style="color:#ef4444;">*</span></label>
            <div style="position:relative;">
              <span style="position:absolute; left:10px; top:50%; transform:translateY(-50%); font-weight:700; color:#64748b; font-size:0.8rem;">₹</span>
              <input type="number" id="entry-rate" class="form-control" step="0.01" min="0" placeholder="0.00" oninput="calculateEntryPreview()" style="padding-left:24px; font-weight:700; font-size:0.9rem;">
            </div>
          </div>

          <!-- Quantity Input with Than Modal Trigger -->
          <div>
            <label class="form-label" style="font-size:0.75rem; font-weight:700; margin-bottom:4px; color:#334155;">Qty <span style="color:#ef4444;">*</span></label>
            <div style="display:flex; align-items:center; gap:4px;">
              <input type="number" id="entry-qty" class="form-control" step="0.01" min="0.01" placeholder="0.00" oninput="onEntryQtyManualInput()" style="font-weight:800; color:#059669; font-size:0.95rem;">
              <!-- Add Multiple Than Button (Matching Image 3) -->
              <button type="button" class="btn btn-secondary btn-sm" onclick="openEntryThanModal()" title="Add / Paste multiple than meter readings" style="padding:6px 10px; font-size:0.75rem; font-weight:800; background:#eff6ff; color:#2563eb; border-color:#bfdbfe; white-space:nowrap; display:inline-flex; align-items:center; gap:4px;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span id="entry-than-btn-text">Than</span>
              </button>
            </div>
          </div>

          <!-- Unit Dropdown -->
          <div>
            <label class="form-label" style="font-size:0.75rem; font-weight:700; margin-bottom:4px; color:#334155;">Unit</label>
            <select id="entry-unit" class="form-control" style="font-weight:700; font-size:0.85rem;">
              <option value="MTR" selected>MTR (Meters)</option>
              <option value="PCS">PCS (Pieces)</option>
              <option value="KG">KG (Kilograms)</option>
              <option value="THN">THN (Than)</option>
              <option value="ROL">ROL (Rolls)</option>
              <option value="YD">YD (Yards)</option>
            </select>
          </div>

          <!-- Tax % Dropdown -->
          <div>
            <label class="form-label" style="font-size:0.75rem; font-weight:700; margin-bottom:4px; color:#334155;">Tax %</label>
            <select id="entry-tax" class="form-control" onchange="calculateEntryPreview()" style="font-weight:700; font-size:0.85rem;">
              <option value="5" selected>5%</option>
              <option value="12">12%</option>
              <option value="18">18%</option>
              <option value="0">0%</option>
            </select>
          </div>

          <!-- Line Preview Info (Amt + Tax) -->
          <div>
            <label class="form-label" style="font-size:0.75rem; font-weight:700; margin-bottom:4px; color:#334155;">Line Total</label>
            <div id="entry-preview-total" style="height:38px; display:flex; align-items:center; font-weight:800; font-size:0.95rem; color:#1e293b; background:#ffffff; border:1px solid #cbd5e1; border-radius:6px; padding:0 10px;">
              ₹0.00
            </div>
          </div>

          <!-- Add Item / Add More Button -->
          <div>
            <button type="button" id="btn-add-item-to-table" class="btn btn-primary" onclick="addItemToTable()" style="height:38px; font-weight:700; display:inline-flex; align-items:center; gap:6px; white-space:nowrap; padding:0 18px; box-shadow:0 2px 6px rgba(37, 99, 235, 0.25);">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              <span>Add Item</span>
            </button>
          </div>

        </div>

        <!-- Temporary Thans Badge preview if entered -->
        <div id="entry-thans-summary-line" style="display:none; margin-top:10px; font-size:0.75rem; color:#2563eb; font-weight:700; background:#eff6ff; padding:4px 10px; border-radius:6px; align-items:center; gap:8px;">
          <span>📦 Thans Added: <strong id="entry-thans-count-display">0 Thans</strong> (<span id="entry-thans-meters-display">0.00 MTR</span>)</span>
          <button type="button" onclick="clearEntryThans()" style="border:none; background:none; color:#ef4444; font-size:0.75rem; font-weight:700; cursor:pointer;">Clear Thans</button>
        </div>
      </div>

      <!-- 3. PURCHASE ITEMS TABLE (Matching Reference Image Table Columns) -->
      <div class="table-responsive">
        <table class="data-table pe-table" id="purchase-items-table" style="width:100%;">
          <thead>
            <tr>
              <th style="width:50px; text-align:center;">S.No.</th>
              <th style="width:28%;">Item Name</th>
              <th style="width:10%; text-align:right;">Rate (₹)</th>
              <th style="width:11%; text-align:right;">Qty</th>
              <th style="width:7%; text-align:center;">Unit</th>
              <th style="width:10%; text-align:center;">Than Detail</th>
              <th style="width:7%; text-align:center;">Tax %</th>
              <th style="width:11%; text-align:right;">Amt (₹)</th>
              <th style="width:10%; text-align:right;">Tax (₹)</th>
              <th style="width:12%; text-align:right;">Total (₹)</th>
              <th style="width:70px; text-align:center;">Actions</th>
            </tr>
          </thead>
          <tbody id="purchase-items-tbody">
            <!-- Dynamic Item Rows Injected Here -->
          </tbody>
          <tfoot id="purchase-items-tfoot" style="background:#f8fafc; font-weight:800; border-top:2px solid #cbd5e1;">
            <tr>
              <td colspan="3" style="text-align:right; font-size:0.9rem; color:#334155;">Total:</td>
              <td id="tbl-total-qty" style="text-align:right; color:#059669; font-size:0.95rem;">0.00</td>
              <td colspan="3"></td>
              <td id="tbl-total-amt" style="text-align:right; font-size:0.95rem; color:#1e293b;">₹0.00</td>
              <td id="tbl-total-tax" style="text-align:right; font-size:0.95rem; color:#dc2626;">₹0.00</td>
              <td id="tbl-total-grand" style="text-align:right; font-size:1.05rem; color:#059669;">₹0.00</td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>

      <!-- Empty State if no items added -->
      <div id="no-items-empty-state" style="text-align:center; padding:36px 16px; color:#94a3b8; border:2px dashed #cbd5e1; border-radius:10px; margin-top:12px; display:block;">
        <div style="font-weight:700; font-size:0.95rem; color:#64748b; margin-bottom:4px;">No items added to this purchase bill yet</div>
        <p style="font-size:0.8rem; margin:0 0 10px;">Select an item from the quick entry panel above and click <strong>Add Item</strong>.</p>
      </div>

      <!-- Hidden container holding normalized form inputs for Laravel backend -->
      <div id="hidden-inputs-container"></div>

    </div>

    <!-- 4. BOTTOM SECTION: REMARKS, UNIT-WISE SUMMARY & GRAND TOTALS -->
    <div class="pe-card">
      <div class="pe-section-title">
        <span class="badge-num">3</span>
        <span>Summary & Billing Terms</span>
      </div>

      <div style="display:grid; grid-template-columns: 1.2fr 1fr; gap:24px; align-items:start;">
        
        <!-- Left: Remarks & Status -->
        <div>
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            Remarks / Delivery Notes
          </label>
          <textarea name="notes" class="form-control" rows="4" placeholder="Enter delivery terms, shade remarks, transport instructions, or bill references..."></textarea>
          
          <div style="display:flex; gap:16px; margin-top:14px;">
            <div style="flex:1;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800); font-size:0.8rem; margin-bottom:4px;">Payment Status</label>
              <select name="payment_status" class="form-control" style="font-weight:600;">
                <option value="Pending" selected>Pending / Credit</option>
                <option value="Partially Paid">Partially Paid</option>
                <option value="Paid">Paid Fully</option>
              </select>
            </div>
            <div style="flex:1;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800); font-size:0.8rem; margin-bottom:4px;">Order Status</label>
              <select name="status" class="form-control" style="font-weight:600;">
                <option value="Approved" selected>Approved</option>
                <option value="Draft">Draft</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Right: Unit-Wise Quantity Summary & Overall Financials (Matching Image 3) -->
        <div class="pe-summary-box">
          
          <!-- Unit Wise Summary Pills (MTR, PCS, KG) -->
          <div style="border-bottom:1px solid #e2e8f0; padding-bottom:12px;">
            <div style="font-size:0.75rem; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:0.04em; margin-bottom:8px;">
              Unit-Wise Quantity Breakdown:
            </div>
            <div id="unit-summary-container" style="display:flex; gap:8px; flex-wrap:wrap;">
              <div class="unit-summary-pill">
                <span>MTR:</span>
                <strong id="unit-sum-mtr" style="color:#059669;">0.00 MTR</strong>
              </div>
              <div class="unit-summary-pill">
                <span>PCS:</span>
                <strong id="unit-sum-pcs" style="color:#2563eb;">0 PCS</strong>
              </div>
              <div class="unit-summary-pill">
                <span>KG:</span>
                <strong id="unit-sum-kg" style="color:#7c3aed;">0.00 KG</strong>
              </div>
            </div>
          </div>

          <!-- Total Amount Calculation Breakdown -->
          <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#475569;">
            <span>Total Items:</span>
            <strong id="sum-items-count" style="color:#0f172a;">0 Items</strong>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#475569;">
            <span>Total Than Count:</span>
            <strong id="sum-thans-count" style="color:#0f172a;">0 Thans</strong>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#475569; border-top:1px dashed #cbd5e1; padding-top:8px;">
            <span>Taxable Subtotal (Amt):</span>
            <strong id="sum-subtotal" style="color:#0f172a;">₹0.00</strong>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#475569;">
            <span>Total GST Tax:</span>
            <strong id="sum-tax" style="color:#dc2626;">₹0.00</strong>
          </div>

          <div style="border-top:2px solid #cbd5e1; padding-top:12px; margin-top:4px; display:flex; justify-content:space-between; align-items:baseline;">
            <span style="font-weight:800; font-size:1.1rem; color:var(--slate-900);">Total Amount:</span>
            <span style="font-weight:800; font-size:1.45rem; color:#059669;" id="sum-grand-total">₹0.00</span>
          </div>

        </div>

      </div>
    </div>

  </div>
</form>

<!-- Modal: Multi-Than Meter Entry & Paste -->
<div id="than-modal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.65); z-index:99999; align-items:center; justify-content:center; padding:16px;">
  <div style="background:#fff; border-radius:16px; max-width:540px; width:100%; padding:24px; box-shadow:0 20px 40px rgba(0,0,0,0.25);">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; border-bottom:1px solid #e2e8f0; padding-bottom:10px;">
      <h4 style="margin:0; font-weight:800; font-size:1.1rem; color:#0f172a;" id="than-modal-title">
        📦 Roll / Than Meter Readings
      </h4>
      <button type="button" onclick="closeThanModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:#94a3b8; line-height:1;">&times;</button>
    </div>

    <p style="font-size:0.8rem; color:#64748b; margin:0 0 14px;">
      Type or paste roll meters separated by commas, spaces, or new lines (e.g. from WhatsApp, packing slip, or challan):
    </p>

    <!-- Paste Textarea -->
    <textarea id="modal-than-textarea" class="form-control" rows="5" placeholder="e.g. 100, 105, 102.50, 102.50" oninput="updateModalThanPreview()" style="font-family:monospace; font-size:0.9rem; font-weight:600;"></textarea>

    <!-- Live Than Preview Info -->
    <div style="display:flex; justify-content:space-between; align-items:center; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px 14px; margin-top:12px;">
      <div>
        <span style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">Count:</span>
        <strong id="modal-preview-count" style="font-size:0.95rem; color:#2563eb; margin-left:4px;">0 Thans</strong>
      </div>
      <div>
        <span style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">Total Meters:</span>
        <strong id="modal-preview-meters" style="font-size:1.05rem; color:#059669; margin-left:4px;">0.00 MTR</strong>
      </div>
    </div>

    <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:18px;">
      <button type="button" class="btn btn-secondary" onclick="closeThanModal()">Cancel</button>
      <button type="button" class="btn btn-primary" onclick="applyModalThans()" style="font-weight:700;">Apply to Quantity</button>
    </div>

  </div>
</div>

@endsection

@push('scripts')
<script>
  const masterItems = @json($items);
  const latestRates = @json($latestRates ?? []);

  // Items State in Table
  let billItems = [];
  
  // Active editing index (-1 when adding new)
  let editingRowIndex = -1;

  // Active Than modal buffer
  let currentModalThans = [];
  let modalTargetContext = 'entry'; // 'entry' or row index number

  function autoGenerateChallanNo() {
    const input = document.getElementById('challan_no');
    if (!input) return;

    const now = new Date();
    const year = now.getFullYear();
    const rand = Math.floor(1000 + Math.random() * 9000);
    input.value = 'CH-' + year + '-' + rand;

    input.style.transition = 'all 0.25s ease';
    input.style.borderColor = '#2563eb';
    input.style.backgroundColor = '#eff6ff';
    setTimeout(() => {
      input.style.borderColor = '';
      input.style.backgroundColor = '';
    }, 400);
  }

  function onVendorChange(select) {
    const opt = select.options[select.selectedIndex];
    document.getElementById('vendor_id').value = opt ? (opt.getAttribute('data-id') || '') : '';
  }

  function onEntryItemSelect(select) {
    const opt = select.options[select.selectedIndex];
    if (!opt || !opt.value) {
      calculateEntryPreview();
      return;
    }

    const itemId = opt.value;
    const itemUnit = opt.getAttribute('data-unit') || 'MTR';
    const itemCost = parseFloat(opt.getAttribute('data-cost') || 0);

    // Auto-select unit
    const unitSelect = document.getElementById('entry-unit');
    if (unitSelect) {
      const match = Array.from(unitSelect.options).find(o => o.value.toUpperCase() === itemUnit.toUpperCase());
      if (match) unitSelect.value = match.value;
      else unitSelect.value = 'MTR';
    }

    // Auto-fill rate if available
    const rateInput = document.getElementById('entry-rate');
    if (rateInput && (!rateInput.value || parseFloat(rateInput.value) === 0)) {
      if (latestRates[itemId]) {
        rateInput.value = latestRates[itemId];
      } else if (itemCost > 0) {
        rateInput.value = itemCost;
      }
    }

    calculateEntryPreview();
  }

  function onEntryQtyManualInput() {
    // If user types directly, keep whatever is typed
    calculateEntryPreview();
  }

  function calculateEntryPreview() {
    const rate = parseFloat(document.getElementById('entry-rate')?.value || 0);
    const qty = parseFloat(document.getElementById('entry-qty')?.value || 0);
    const taxPct = parseFloat(document.getElementById('entry-tax')?.value || 0);

    const amt = Math.round(rate * qty * 100) / 100;
    const taxAmt = Math.round((amt * taxPct / 100.0) * 100) / 100;
    const total = Math.round((amt + taxAmt) * 100) / 100;

    const previewEl = document.getElementById('entry-preview-total');
    if (previewEl) {
      previewEl.textContent = '₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
  }

  // Than Modal Handlers
  function openEntryThanModal() {
    modalTargetContext = 'entry';
    document.getElementById('than-modal-title').textContent = '📦 Entry Roll / Than Meter Readings';
    const currentM = currentModalThans.length > 0 ? currentModalThans.join(', ') : (document.getElementById('entry-qty')?.value || '');
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
      calculateEntryPreview();
    } else {
      // Editing an existing row
      const idx = modalTargetContext;
      if (billItems[idx]) {
        billItems[idx].thans = thans;
        billItems[idx].than_count = thans.length;
        if (thans.length > 0) {
          billItems[idx].qty = sum;
          billItems[idx].ordered_qty = sum;
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

  // Adding Item to Table
  function addItemToTable() {
    const itemSelect = document.getElementById('entry-item-select');
    const itemId = itemSelect.value;
    const opt = itemSelect.options[itemSelect.selectedIndex];
    
    if (!itemId) {
      alert('Please select an item from the Item Master.');
      itemSelect.focus();
      return;
    }

    const itemName = opt.getAttribute('data-name') || opt.text;
    const itemCode = opt.getAttribute('data-code') || '';
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

    const itemObj = {
      item_id: itemId,
      item_name: itemName,
      item_code: itemCode,
      rate: rate,
      qty: qty,
      ordered_qty: qty,
      unit: unit,
      tax_percent: taxPct,
      amount: amount,
      subtotal: amount,
      tax_amount: taxAmount,
      total_amount: total,
      thans: [...currentModalThans],
      than_count: currentModalThans.length
    };

    if (editingRowIndex >= 0) {
      // Update existing row
      billItems[editingRowIndex] = itemObj;
      editingRowIndex = -1;
      document.getElementById('btn-add-item-to-table').innerHTML = `
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        <span>Add Item</span>
      `;
    } else {
      // Add new row
      billItems.push(itemObj);
    }

    // Reset input fields
    itemSelect.value = '';
    document.getElementById('entry-rate').value = '';
    document.getElementById('entry-qty').value = '';
    clearEntryThans();
    calculateEntryPreview();

    renderTable();
    calculateBillTotals();
  }

  function editItem(index) {
    const item = billItems[index];
    if (!item) return;

    editingRowIndex = index;

    // Load back into input row
    const itemSelect = document.getElementById('entry-item-select');
    itemSelect.value = item.item_id;

    document.getElementById('entry-rate').value = item.rate;
    document.getElementById('entry-qty').value = item.qty;
    document.getElementById('entry-unit').value = item.unit;
    document.getElementById('entry-tax').value = item.tax_percent;

    currentModalThans = item.thans ? [...item.thans] : [];
    if (currentModalThans.length > 0) {
      document.getElementById('entry-than-btn-text').textContent = `${currentModalThans.length} Thans`;
      const summaryLine = document.getElementById('entry-thans-summary-line');
      if (summaryLine) {
        summaryLine.style.display = 'flex';
        document.getElementById('entry-thans-count-display').textContent = `${currentModalThans.length} Thans`;
        document.getElementById('entry-thans-meters-display').textContent = `${item.qty.toFixed(2)} MTR`;
      }
    } else {
      clearEntryThans();
    }

    calculateEntryPreview();

    document.getElementById('btn-add-item-to-table').innerHTML = `
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
      <span>Update Row #${index + 1}</span>
    `;

    document.getElementById('entry-rate').focus();
  }

  function deleteItem(index) {
    if (confirm(`Remove item #${index + 1} (${billItems[index].item_name}) from this purchase?`)) {
      billItems.splice(index, 1);
      if (editingRowIndex === index) {
        editingRowIndex = -1;
        document.getElementById('btn-add-item-to-table').innerHTML = `
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          <span>Add Item</span>
        `;
      }
      renderTable();
      calculateBillTotals();
    }
  }

  function recalculateRow(index) {
    const item = billItems[index];
    if (!item) return;

    item.amount = Math.round(item.rate * item.qty * 100) / 100;
    item.subtotal = item.amount;
    item.tax_amount = Math.round((item.amount * item.tax_percent / 100.0) * 100) / 100;
    item.total_amount = Math.round((item.amount + item.tax_amount) * 100) / 100;
  }

  function onInlineRowChange(index, field, value) {
    const item = billItems[index];
    if (!item) return;

    if (field === 'rate') item.rate = parseFloat(value) || 0;
    if (field === 'qty') {
      item.qty = parseFloat(value) || 0;
      item.ordered_qty = item.qty;
    }
    if (field === 'tax_percent') item.tax_percent = parseFloat(value) || 0;

    recalculateRow(index);
    renderTable();
    calculateBillTotals();
  }

  // Render Table
  function renderTable() {
    const tbody = document.getElementById('purchase-items-tbody');
    const emptyState = document.getElementById('no-items-empty-state');
    const tfoot = document.getElementById('purchase-items-tfoot');
    const hiddenContainer = document.getElementById('hidden-inputs-container');

    tbody.innerHTML = '';
    hiddenContainer.innerHTML = '';

    if (billItems.length === 0) {
      emptyState.style.display = 'block';
      tfoot.style.display = 'none';
      return;
    }

    emptyState.style.display = 'none';
    tfoot.style.display = 'table-footer-group';

    billItems.forEach((item, idx) => {
      const tr = document.createElement('tr');
      const hasThans = item.thans && item.thans.length > 0;
      const thanText = hasThans ? `${item.thans.length} Thans` : '-';

      tr.innerHTML = `
        <td style="text-align:center; font-weight:700; color:#64748b;">${idx + 1}</td>
        <td>
          <div style="font-weight:700; color:#0f172a;">${item.item_name}</div>
          ${item.item_code ? `<span style="font-size:0.75rem; color:#64748b; font-family:monospace;">${item.item_code}</span>` : ''}
        </td>
        <td style="text-align:right;">
          <input type="number" step="0.01" min="0" value="${item.rate}" 
            class="form-control form-control-sm text-end" 
            style="width:90px; display:inline-block; font-weight:700;"
            onchange="onInlineRowChange(${idx}, 'rate', this.value)">
        </td>
        <td style="text-align:right;">
          <input type="number" step="0.01" min="0.01" value="${item.qty}" 
            class="form-control form-control-sm text-end" 
            style="width:90px; display:inline-block; font-weight:800; color:#059669;"
            onchange="onInlineRowChange(${idx}, 'qty', this.value)">
        </td>
        <td style="text-align:center;">
          <span class="badge" style="background:#f1f5f9; color:#475569; font-weight:700; border:1px solid #cbd5e1;">${item.unit}</span>
        </td>
        <td style="text-align:center;">
          <span class="than-tag" onclick="openRowThanModal(${idx})" title="Click to view or edit roll readings">
            <span>${thanText}</span>
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          </span>
        </td>
        <td style="text-align:center;">
          <select class="form-control form-control-sm" style="width:75px; display:inline-block; font-weight:700; padding:2px 4px;" onchange="onInlineRowChange(${idx}, 'tax_percent', this.value)">
            <option value="5" ${item.tax_percent == 5 ? 'selected' : ''}>5%</option>
            <option value="12" ${item.tax_percent == 12 ? 'selected' : ''}>12%</option>
            <option value="18" ${item.tax_percent == 18 ? 'selected' : ''}>18%</option>
            <option value="0" ${item.tax_percent == 0 ? 'selected' : ''}>0%</option>
          </select>
        </td>
        <td style="text-align:right; font-weight:700; color:#1e293b;">
          ₹${item.amount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
        </td>
        <td style="text-align:right; font-weight:700; color:#dc2626;">
          ₹${item.tax_amount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
        </td>
        <td style="text-align:right; font-weight:800; color:#059669; font-size:0.95rem;">
          ₹${item.total_amount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
        </td>
        <td style="text-align:center;">
          <div style="display:inline-flex; align-items:center; gap:4px;">
            <button type="button" class="btn btn-secondary btn-xs" onclick="editItem(${idx})" title="Edit in panel" style="padding:4px 8px; font-size:0.75rem;">
              ✏️
            </button>
            <button type="button" class="btn btn-danger btn-xs" onclick="deleteItem(${idx})" title="Delete row" style="padding:4px 8px; font-size:0.75rem; background:#fee2e2; border-color:#fca5a5; color:#dc2626;">
              &times;
            </button>
          </div>
        </td>
      `;

      tbody.appendChild(tr);

      // Generate hidden form inputs for Laravel backend
      const thansJson = (item.thans && item.thans.length > 0) ? JSON.stringify(item.thans) : '';
      hiddenContainer.innerHTML += `
        <input type="hidden" name="items[${idx}][item_id]" value="${item.item_id}">
        <input type="hidden" name="items[${idx}][item_name]" value="${item.item_name}">
        <input type="hidden" name="items[${idx}][item_code]" value="${item.item_code}">
        <input type="hidden" name="items[${idx}][rate]" value="${item.rate}">
        <input type="hidden" name="items[${idx}][ordered_qty]" value="${item.qty}">
        <input type="hidden" name="items[${idx}][unit]" value="${item.unit}">
        <input type="hidden" name="items[${idx}][tax_percent]" value="${item.tax_percent}">
        <input type="hidden" name="items[${idx}][tax_amount]" value="${item.tax_amount}">
        <input type="hidden" name="items[${idx}][total_amount]" value="${item.total_amount}">
        <input type="hidden" name="items[${idx}][than_count]" value="${item.than_count || 0}">
        <input type="hidden" name="items[${idx}][thans]" value='${thansJson}'>
      `;
    });
  }

  // Calculate Overall Totals & Unit-Wise Breakdowns
  function calculateBillTotals() {
    let grandQty = 0;
    let grandAmt = 0;
    let grandTax = 0;
    let grandTotal = 0;
    let grandThans = 0;

    const unitTotals = {};

    billItems.forEach(item => {
      grandQty += item.qty;
      grandAmt += item.amount;
      grandTax += item.tax_amount;
      grandTotal += item.total_amount;
      grandThans += (item.than_count || 0);

      const u = (item.unit || 'MTR').toUpperCase();
      unitTotals[u] = (unitTotals[u] || 0) + item.qty;
    });

    grandAmt = Math.round(grandAmt * 100) / 100;
    grandTax = Math.round(grandTax * 100) / 100;
    grandTotal = Math.round(grandTotal * 100) / 100;

    // Table Footer
    document.getElementById('tbl-total-qty').textContent = grandQty.toFixed(2);
    document.getElementById('tbl-total-amt').textContent = '₹' + grandAmt.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('tbl-total-tax').textContent = '₹' + grandTax.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('tbl-total-grand').textContent = '₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    // Summary Box
    document.getElementById('sum-items-count').textContent = billItems.length + (billItems.length === 1 ? ' Item' : ' Items');
    document.getElementById('sum-thans-count').textContent = grandThans + ' Thans';
    document.getElementById('sum-subtotal').textContent = '₹' + grandAmt.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('sum-tax').textContent = '₹' + grandTax.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('sum-grand-total').textContent = '₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    // Unit-Wise Breakdown Box (Matching Image: MTR, PCS, KG)
    const mtrVal = (unitTotals['MTR'] || unitTotals['METERS'] || 0);
    const pcsVal = (unitTotals['PCS'] || unitTotals['PIECES'] || 0);
    const kgVal  = (unitTotals['KG']  || unitTotals['KGS'] || 0);

    document.getElementById('unit-sum-mtr').textContent = mtrVal > 0 ? `${mtrVal.toFixed(2)} MTR` : '-';
    document.getElementById('unit-sum-pcs').textContent = pcsVal > 0 ? `${Math.round(pcsVal)} PCS` : '-';
    document.getElementById('unit-sum-kg').textContent  = kgVal > 0  ? `${kgVal.toFixed(2)} KG`  : '-';

    // Also support any custom units
    const container = document.getElementById('unit-summary-container');
    // Ensure base 3 are always rendered
    container.innerHTML = `
      <div class="unit-summary-pill">
        <span>MTR:</span>
        <strong id="unit-sum-mtr" style="color:#059669;">${mtrVal > 0 ? mtrVal.toFixed(2) + ' MTR' : '-'}</strong>
      </div>
      <div class="unit-summary-pill">
        <span>PCS:</span>
        <strong id="unit-sum-pcs" style="color:#2563eb;">${pcsVal > 0 ? Math.round(pcsVal) + ' PCS' : '-'}</strong>
      </div>
      <div class="unit-summary-pill">
        <span>KG:</span>
        <strong id="unit-sum-kg" style="color:#7c3aed;">${kgVal > 0 ? kgVal.toFixed(2) + ' KG' : '-'}</strong>
      </div>
    `;

    Object.keys(unitTotals).forEach(u => {
      if (!['MTR', 'METERS', 'PCS', 'PIECES', 'KG', 'KGS'].includes(u)) {
        const val = unitTotals[u];
        container.innerHTML += `
          <div class="unit-summary-pill">
            <span>${u}:</span>
            <strong style="color:#0f172a;">${val.toFixed(2)} ${u}</strong>
          </div>
        `;
      }
    });
  }

  // Form Validation on Submit
  document.getElementById('pe-form')?.addEventListener('submit', function(e) {
    const vSel = document.getElementById('vendor-select');
    if (!vSel.value) {
      e.preventDefault();
      alert('Please select a Vendor / Supplier.');
      vSel.focus();
      return false;
    }

    const challanNo = document.getElementById('challan_no');
    if (!challanNo.value.trim()) {
      e.preventDefault();
      alert('Please enter a Supplier Challan / Bill Number.');
      challanNo.focus();
      return false;
    }

    if (billItems.length === 0) {
      e.preventDefault();
      alert('Please add at least one item to the purchase items table.');
      document.getElementById('entry-item-select').focus();
      return false;
    }
  });

  // Ensure global modal & action accessibility on window
  window.openEntryThanModal = openEntryThanModal;
  window.openRowThanModal = openRowThanModal;
  window.closeThanModal = closeThanModal;
  window.applyModalThans = applyModalThans;
  window.updateModalThanPreview = updateModalThanPreview;
  window.addEntryLineItem = addEntryLineItem;
  window.deleteItem = deleteItem;
  window.editItem = editItem;
  window.autoGenerateChallanNo = autoGenerateChallanNo;

  document.addEventListener('DOMContentLoaded', () => {
    const vSel = document.getElementById('vendor-select');
    if (vSel && vSel.value) {
      onVendorChange(vSel);
    }

    renderTable();
    calculateBillTotals();
  });
</script>
@endpush
