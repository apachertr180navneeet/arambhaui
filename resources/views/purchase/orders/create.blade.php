@extends('layouts.app')

@section('title', 'New Purchase Entry - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><a href="{{ route('purchase.orders.index') }}" style="color:inherit; text-decoration:none;">Purchase Entries</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>New Purchase Entry</span></div>
@endsection

@section('content')
<form action="{{ route('purchase.orders.store') }}" method="POST" id="pe-form">
  @csrf

  <div style="display:flex; flex-direction:column; gap:22px;">
    
    <!-- Top Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
      <div>
        <h2 style="margin:0; font-size:1.45rem; font-weight:800; color:var(--slate-900); letter-spacing:-0.02em;">
          New Purchase Entry
        </h2>
        <p style="margin:3px 0 0; font-size:0.85rem; color:var(--slate-500);">
          Record multi-item fabric inward challan, than-wise meter breakdown, supplier rate & procurement billing.
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

    <!-- 1. Header Information (Vendor, Challan, Date) -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:24px;">
      <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--slate-100); padding-bottom:12px; margin-bottom:18px;">
        <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin:0; color:var(--slate-800); display:flex; align-items:center; gap:8px;">
          <span style="width:24px; height:24px; border-radius:6px; background:#eff6ff; color:#2563eb; display:inline-flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:800;">1</span>
          Challan & Supplier Information
        </h3>
      </div>

      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:18px;">
        
        <!-- Vendor / Supplier Select with Live Refresh & Add Link -->
        <div class="form-group" style="margin-bottom:0;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800); margin:0;">
              Vendor / Supplier <span style="color:#ef4444;">*</span>
            </label>
            <a href="{{ route('masters.vendors.index') }}" target="_blank" style="font-size:0.75rem; color:#2563eb; text-decoration:none; font-weight:600;" title="Open Vendor Master to register suppliers">+ Manage Vendors</a>
          </div>
          <select name="vendor_name" id="vendor-select" class="form-control" required onchange="onVendorChange(this)">
            <option value="">-- Select Vendor / Supplier --</option>
            @forelse($vendors as $v)
              <option value="{{ $v->name }}" data-id="{{ $v->id }}" data-code="{{ $v->code }}" data-city="{{ $v->city ?? '' }}">
                {{ $v->name }} ({{ $v->code }}){{ !empty($v->city) ? ' - '.$v->city : '' }}
              </option>
            @empty
              <option value="" disabled>No vendors found in Vendor Master</option>
            @endforelse
          </select>
          <input type="hidden" name="vendor_id" id="vendor_id">
        </div>

        <!-- Supplier Challan / Bill No -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            Supplier Challan / Bill No. <span style="color:#ef4444;">*</span>
          </label>
          <input type="text" name="challan_no" id="challan_no" class="form-control" required placeholder="e.g. 1076 / CH-9821" style="font-weight:700; letter-spacing:0.5px;">
        </div>

        <!-- Challan / Entry Date -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            Challan / Entry Date <span style="color:#ef4444;">*</span>
          </label>
          <input type="date" name="po_date" class="form-control" required value="{{ date('Y-m-d') }}">
        </div>

      </div>
    </div>

    <!-- 2. Multi-Item & Than-Wise Breakdown Section -->
    <div style="display:flex; flex-direction:column; gap:16px;">
      
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div>
          <h3 style="font-size:1.05rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin:0; color:var(--slate-900); display:flex; align-items:center; gap:8px;">
            <span style="width:26px; height:26px; border-radius:6px; background:#ecfdf5; color:#059669; display:inline-flex; align-items:center; justify-content:center; font-size:0.8rem; font-weight:800;">2</span>
            Fabric Items & Than Breakdown
          </h3>
          <p style="margin:3px 0 0; font-size:0.825rem; color:var(--slate-500);">
            Add one or more fabric qualities with independent than meter readings, rate & taxes.
          </p>
        </div>

        <button type="button" class="btn btn-primary btn-sm" onclick="addNewItem()" style="display:inline-flex; align-items:center; gap:6px; font-weight:700; box-shadow:0 2px 6px rgba(37, 99, 235, 0.25);">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          + Add Another Item
        </button>
      </div>

      <!-- Container where Item Cards are dynamically rendered -->
      <div id="items-container" style="display:flex; flex-direction:column; gap:20px;"></div>

      <!-- Add Item Large Footer Trigger Button -->
      <div style="text-align:center; padding:10px 0;">
        <button type="button" class="btn btn-secondary" onclick="addNewItem()" style="border:2px dashed #cbd5e1; background:#f8fafc; color:#334155; font-weight:700; width:100%; padding:12px; display:inline-flex; align-items:center; justify-content:center; gap:8px; border-radius:12px; transition:all 0.2s ease;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Add Another Product / Fabric Quality (Multi-Item Inward)
        </button>
      </div>

    </div>

    <!-- 3. Overall Financial Calculation Summary & Notes Card -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:24px;">
      <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin-top:0; margin-bottom:18px; color:var(--slate-800); border-bottom:1px solid var(--slate-100); padding-bottom:10px; display:flex; align-items:center; gap:8px;">
        <span style="width:24px; height:24px; border-radius:6px; background:#f5f3ff; color:#7c3aed; display:inline-flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:800;">3</span>
        Overall Calculation & Procurement Terms
      </h3>

      <div style="display:grid; grid-template-columns: 1.2fr 1fr; gap:24px;">
        
        <!-- Notes & Terms -->
        <div>
          <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
            Notes / Transport / Delivery Remarks
          </label>
          <textarea name="notes" class="form-control" rows="4" placeholder="e.g. Transport: Shree Balaji Roadlines, Shade tolerance acceptable, Haste: Gaysubhai..."></textarea>
          
          <div style="display:flex; gap:16px; margin-top:14px;">
            <div style="flex:1;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800); font-size:0.8rem; margin-bottom:4px;">Payment Status</label>
              <select name="payment_status" class="form-control">
                <option value="Pending" selected>Pending / Credit</option>
                <option value="Partially Paid">Partially Paid</option>
                <option value="Paid">Paid Fully</option>
              </select>
            </div>
            <div style="flex:1;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800); font-size:0.8rem; margin-bottom:4px;">Status</label>
              <select name="status" class="form-control">
                <option value="Approved" selected>Approved</option>
                <option value="Draft">Draft</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Calculated Summary Box -->
        <div style="background:#f8fafc; border:1px solid var(--slate-200); border-radius:14px; padding:20px; display:flex; flex-direction:column; gap:12px;">
          <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#64748b;">
            <span>Total Fabric Items:</span>
            <span style="font-weight:700; color:#0f172a;" id="summary-items-count">1 Item</span>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#64748b;">
            <span>Total Than Count:</span>
            <span style="font-weight:700; color:#0f172a;" id="summary-than-count">0 Thans</span>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#64748b;">
            <span>Total Inward Quantity:</span>
            <span style="font-weight:700; color:#059669; font-size:1rem;" id="summary-total-meters">0.00 Mtr</span>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#64748b; border-top:1px dashed #cbd5e1; padding-top:10px;">
            <span>Taxable Subtotal:</span>
            <span style="font-weight:700; color:#0f172a;" id="summary-subtotal">₹0.00</span>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#64748b;">
            <span>Total GST Taxes:</span>
            <span style="font-weight:700; color:#0f172a;" id="summary-tax">₹0.00</span>
          </div>

          <div style="border-top:2px solid #cbd5e1; padding-top:12px; margin-top:4px; display:flex; justify-content:space-between; align-items:baseline;">
            <span style="font-weight:800; font-size:1.1rem; color:var(--slate-900);">Grand Total:</span>
            <span style="font-weight:800; font-size:1.4rem; color:#059669;" id="summary-grand">₹0.00</span>
          </div>
        </div>

      </div>

    </div>

  </div>
</form>

<!-- Modal: Quick Paste Multiple Thans -->
<div id="paste-modal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(15,23,42,0.6); z-index:99999; align-items:center; justify-content:center; padding:16px;">
  <div style="background:#fff; border-radius:16px; max-width:480px; width:100%; padding:24px; box-shadow:0 20px 40px rgba(0,0,0,0.2);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
      <h4 style="margin:0; font-weight:800; font-size:1.1rem; color:#0f172a;" id="paste-modal-title">Paste Than Meter Readings</h4>
      <button type="button" onclick="closePasteModal()" style="background:none; border:none; font-size:1.4rem; cursor:pointer; color:#94a3b8;">&times;</button>
    </div>
    <p style="font-size:0.8rem; color:#64748b; margin:0 0 12px;">
      Paste numbers separated by space, comma, or new line (e.g. from WhatsApp or Challan sheet):
    </p>
    <textarea id="paste-textarea" class="form-control" rows="6" placeholder="148, 72.50, 112.50, 103.50, 110.50, 105&#10;99.50, 116.00, 126.50, 89.50, 117.50, 98, 117, 99.50" style="font-family:monospace; font-size:0.9rem;"></textarea>
    <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:16px;">
      <button type="button" class="btn btn-secondary" onclick="closePasteModal()">Cancel</button>
      <button type="button" class="btn btn-primary" onclick="applyPastedThans()" style="font-weight:700;">Add to Fabric Item</button>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  const masterItems = @json($items);
  let activePasteItemIndex = 0;

  // Multi-item array state
  let itemsData = [
    {
      item_id: '',
      item_name: '',
      item_code: '',
      rate: 35.00,
      tax_percent: 5,
      unit: 'Meters',
      thans: []
    }
  ];

  function onVendorChange(select) {
    const opt = select.options[select.selectedIndex];
    document.getElementById('vendor_id').value = opt ? (opt.getAttribute('data-id') || '') : '';
  }

  function addNewItem() {
    itemsData.push({
      item_id: '',
      item_name: '',
      item_code: '',
      rate: 35.00,
      tax_percent: 5,
      unit: 'Meters',
      thans: []
    });
    renderAllItems();
    calculateOverallTotals();
  }

  function removeItem(itemIdx) {
    if (itemsData.length <= 1) {
      alert('A purchase entry must have at least one fabric item.');
      return;
    }
    const item = itemsData[itemIdx];
    if (item.thans.length > 0) {
      if (!confirm(`Remove Item #${itemIdx + 1} (${item.item_name || 'Fabric'}) and its ${item.thans.length} than readings?`)) {
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
      if (r > 0) {
        itemsData[itemIdx].rate = r;
      }
      itemsData[itemIdx].unit = opt.getAttribute('data-unit') || 'Meters';
    }
    renderAllItems();
    calculateOverallTotals();
  }

  function onItemRateChange(itemIdx, val) {
    const r = parseFloat(val);
    itemsData[itemIdx].rate = !isNaN(r) && r >= 0 ? r : 0;
    updateItemSummaryHeader(itemIdx);
    calculateOverallTotals();
  }

  function onItemTaxChange(itemIdx, val) {
    const t = parseFloat(val);
    itemsData[itemIdx].tax_percent = !isNaN(t) && t >= 0 ? t : 0;
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
    if (!document.getElementById('challan_no').value) {
      document.getElementById('challan_no').value = '1076';
    }
    renderItemThans(itemIdx);
    calculateOverallTotals();
  }

  function openPasteModalForItem(itemIdx) {
    activePasteItemIndex = itemIdx;
    document.getElementById('paste-modal-title').textContent = `Paste Thans for Item #${itemIdx + 1} (${itemsData[itemIdx].item_name || 'Fabric'})`;
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

      let itemOptions = `<option value="">-- Select Fabric / Raw Material SKU --</option>`;
      masterItems.forEach(itm => {
        const isSel = (item.item_name && (itm.name === item.item_name || itm.id == item.item_id)) ? 'selected' : '';
        itemOptions += `<option value="${itm.name}" data-id="${itm.id}" data-code="${itm.code || ''}" data-rate="${itm.unit_cost || 0}" data-unit="${itm.unit || 'Meters'}" ${isSel}>${itm.name} [${itm.code || 'ITEM'}] (${itm.category || 'Fabric'})</option>`;
      });

      const totalMeters = item.thans.reduce((a, b) => a + (parseFloat(b) || 0), 0);
      const subtotal = Math.round(totalMeters * (item.rate || 0) * 100) / 100;
      const taxAmount = Math.round((subtotal * (item.tax_percent || 0) / 100.0) * 100) / 100;
      const lineTotal = Math.round((subtotal + taxAmount) * 100) / 100;

      itemCard.innerHTML = `
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--slate-100); padding-bottom:12px; margin-bottom:16px; flex-wrap:wrap; gap:10px;">
          <div style="display:flex; align-items:center; gap:10px;">
            <span style="width:26px; height:26px; border-radius:8px; background:#eff6ff; color:#2563eb; display:inline-flex; align-items:center; justify-content:center; font-size:0.85rem; font-weight:800;">
              #${itemIdx + 1}
            </span>
            <span style="font-weight:800; font-size:1rem; color:var(--slate-900);" id="item-title-display-${itemIdx}">
              ${item.item_name || 'Fabric Item ' + (itemIdx + 1)}
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
              <button type="button" class="btn btn-secondary btn-sm" onclick="removeItem(${itemIdx})" style="color:#ef4444; font-weight:700; padding:4px 8px; border-color:#fecaca;" title="Remove this entire fabric item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                Remove Item
              </button>
            ` : ''}
          </div>
        </div>

        <input type="hidden" name="items[${itemIdx}][item_id]" id="item-id-input-${itemIdx}" value="${item.item_id}">
        <input type="hidden" name="items[${itemIdx}][item_code]" id="item-code-input-${itemIdx}" value="${item.item_code}">
        <input type="hidden" name="items[${itemIdx}][unit]" value="${item.unit || 'Meters'}">

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px; margin-bottom:18px;">
          
          <div class="form-group" style="margin-bottom:0; grid-column:span 2;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800); margin:0;">
                Fabric Quality / Item SKU <span style="color:#ef4444;">*</span>
              </label>
              <a href="{{ route('masters.items.index') }}" target="_blank" style="font-size:0.75rem; color:#2563eb; text-decoration:none; font-weight:600;">+ Manage Items</a>
            </div>
            <select name="items[${itemIdx}][item_name]" class="form-control item-sku-select" required onchange="onItemSelectChange(${itemIdx}, this)">
              ${itemOptions}
            </select>
          </div>

          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
              Rate Per Meter (₹) <span style="color:#ef4444;">*</span>
            </label>
            <div style="position:relative;">
              <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); font-weight:700; color:#64748b;">₹</span>
              <input type="number" step="0.01" min="0" name="items[${itemIdx}][rate]" class="form-control" required value="${parseFloat(item.rate).toFixed(2)}" style="padding-left:28px; font-weight:700; font-size:0.95rem;" oninput="onItemRateChange(${itemIdx}, this.value)">
            </div>
          </div>

          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800); margin-bottom:6px;">
              GST Tax Rate
            </label>
            <select name="items[${itemIdx}][tax_percent]" class="form-control" onchange="onItemTaxChange(${itemIdx}, this.value)">
              <option value="5" ${item.tax_percent == 5 ? 'selected' : ''}>5% GST (Standard Fabric)</option>
              <option value="0" ${item.tax_percent == 0 ? 'selected' : ''}>0% Excluded / Exempted</option>
              <option value="12" ${item.tax_percent == 12 ? 'selected' : ''}>12% GST</option>
              <option value="18" ${item.tax_percent == 18 ? 'selected' : ''}>18% GST</option>
            </select>
          </div>

        </div>

        <div style="background:#fafafa; border:1px solid #e2e8f0; border-radius:14px; padding:16px;">
          
          <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:12px;">
            <div style="font-weight:800; font-size:0.85rem; color:#475569; text-transform:uppercase; letter-spacing:0.04em;">
              Than Meter Breakdown for Item #${itemIdx + 1}
            </div>
            
            <div style="display:flex; gap:8px;">
              <button type="button" class="btn btn-secondary btn-sm" onclick="openPasteModalForItem(${itemIdx})" style="font-weight:700; font-size:0.8rem; display:inline-flex; align-items:center; gap:5px;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
                Paste Multi-Thans
              </button>
              <button type="button" class="btn btn-secondary btn-sm" onclick="clearItemThans(${itemIdx})" style="font-weight:700; font-size:0.8rem; color:#dc2626;" title="Clear thans for this item">
                Clear Thans
              </button>
            </div>
          </div>

          <div style="display:flex; gap:8px; margin-bottom:14px;">
            <input type="number" step="0.01" min="0" id="quick-than-input-${itemIdx}" class="form-control" placeholder="Type meter reading (e.g. 148) and press Enter" style="font-size:0.9rem; font-weight:600;" onkeydown="if(event.key==='Enter'){event.preventDefault();addSingleThanFromInput(${itemIdx});}">
            <button type="button" class="btn btn-primary btn-sm" onclick="addSingleThanFromInput(${itemIdx})" style="font-weight:700; white-space:nowrap; display:inline-flex; align-items:center; gap:6px;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Add Than
            </button>
          </div>

          <div id="than-empty-box-${itemIdx}" style="text-align:center; padding:24px 14px; color:#94a3b8; border:2px dashed #cbd5e1; border-radius:10px; background:#ffffff; ${item.thans.length > 0 ? 'display:none;' : 'display:block;'}">
            <div style="font-weight:700; font-size:0.9rem; color:#64748b; margin-bottom:3px;">No Thans added for this item yet</div>
            <p style="font-size:0.775rem; margin:0 0 10px;">Type meter reading above or paste multiple readings.</p>
            <button type="button" class="btn btn-secondary btn-xs" onclick="loadSampleChallanForItem(${itemIdx})">Load Challan Sample (14 Thans)</button>
          </div>

          <div id="than-sheet-box-${itemIdx}" style="${item.thans.length > 0 ? 'display:grid;' : 'display:none;'} grid-template-columns:1fr 1fr; gap:14px;">
            <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:12px;">
              <div style="font-weight:700; font-size:0.75rem; color:#64748b; text-transform:uppercase; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-bottom:8px; display:flex; justify-content:space-between;">
                <span>Column 1</span>
                <span id="col1-badge-${itemIdx}" style="color:#2563eb; font-weight:800;">0.00 Mtr</span>
              </div>
              <div id="col1-thans-${itemIdx}" style="display:flex; flex-direction:column; gap:6px;"></div>
            </div>

            <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:12px;">
              <div style="font-weight:700; font-size:0.75rem; color:#64748b; text-transform:uppercase; border-bottom:1px solid #e2e8f0; padding-bottom:6px; margin-bottom:8px; display:flex; justify-content:space-between;">
                <span>Column 2</span>
                <span id="col2-badge-${itemIdx}" style="color:#2563eb; font-weight:800;">0.00 Mtr</span>
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

    const midpoint = Math.ceil(item.thans.length / 2);

    item.thans.forEach((meter, tIdx) => {
      const row = document.createElement('div');
      row.style.cssText = 'display:flex; align-items:center; gap:6px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:4px 8px;';
      row.innerHTML = `
        <span style="font-weight:700; font-size:0.725rem; color:#64748b; width:54px;">Than #${tIdx + 1}</span>
        <div style="flex:1;">
          <input type="number" step="0.01" min="0" value="${meter}" name="items[${itemIdx}][thans][]" 
            class="form-control form-control-sm than-input" 
            style="font-weight:700; font-size:0.85rem; padding:3px 6px; height:28px;"
            oninput="updateThanMeter(${itemIdx}, ${tIdx}, this.value)"
            onfocus="this.select()">
        </div>
        <span style="font-size:0.7rem; color:#94a3b8; font-weight:600;">Mtr</span>
        <button type="button" onclick="removeThan(${itemIdx}, ${tIdx})" title="Remove than" style="background:none; border:none; color:#ef4444; font-size:1.1rem; cursor:pointer; line-height:1; padding:0 3px;">&times;</button>
      `;

      if (tIdx < midpoint) {
        col1.appendChild(row);
      } else {
        col2.appendChild(row);
      }
    });

    updateItemThansSubtotals(itemIdx);
    updateItemSummaryHeader(itemIdx);
  }

  function updateItemThansSubtotals(itemIdx) {
    const item = itemsData[itemIdx];
    if (!item) return;

    const midpoint = Math.ceil(item.thans.length / 2);
    let c1 = 0;
    let c2 = 0;
    item.thans.forEach((m, i) => {
      if (i < midpoint) c1 += (parseFloat(m) || 0);
      else c2 += (parseFloat(m) || 0);
    });

    const b1 = document.getElementById(`col1-badge-${itemIdx}`);
    const b2 = document.getElementById(`col2-badge-${itemIdx}`);
    if (b1) b1.textContent = c1.toFixed(2) + ' Mtr (' + Math.min(midpoint, item.thans.length) + ')';
    if (b2) b2.textContent = c2.toFixed(2) + ' Mtr (' + Math.max(0, item.thans.length - midpoint) + ')';
  }

  function updateItemSummaryHeader(itemIdx) {
    const item = itemsData[itemIdx];
    if (!item) return;

    const totalMeters = item.thans.reduce((a, b) => a + (parseFloat(b) || 0), 0);
    const subtotal = Math.round(totalMeters * (item.rate || 0) * 100) / 100;
    const taxAmount = Math.round((subtotal * (item.tax_percent || 0) / 100.0) * 100) / 100;
    const lineTotal = Math.round((subtotal + taxAmount) * 100) / 100;

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
    let grandSubtotal = 0;
    let grandTax = 0;

    itemsData.forEach(item => {
      const itemMeters = item.thans.reduce((a, b) => a + (parseFloat(b) || 0), 0);
      const itemSub = Math.round(itemMeters * (item.rate || 0) * 100) / 100;
      const itemTax = Math.round((itemSub * (item.tax_percent || 0) / 100.0) * 100) / 100;

      grandTotalMeters += itemMeters;
      grandTotalThans += item.thans.length;
      grandSubtotal += itemSub;
      grandTax += itemTax;
    });

    const grandFinal = Math.round((grandSubtotal + grandTax) * 100) / 100;

    document.getElementById('summary-items-count').textContent = itemsData.length + (itemsData.length === 1 ? ' Item' : ' Items');
    document.getElementById('summary-than-count').textContent = grandTotalThans + ' Thans';
    document.getElementById('summary-total-meters').textContent = grandTotalMeters.toFixed(2) + ' Mtr';
    document.getElementById('summary-subtotal').textContent = '₹' + grandSubtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('summary-tax').textContent = '₹' + grandTax.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('summary-grand').textContent = '₹' + grandFinal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  document.getElementById('pe-form')?.addEventListener('submit', function(e) {
    if (itemsData.length === 0) {
      e.preventDefault();
      alert('Please add at least one fabric item.');
      return false;
    }

    let hasMissingSku = false;
    let hasZeroThans = false;

    itemsData.forEach((item, idx) => {
      if (!item.item_name) {
        hasMissingSku = true;
      }
      if (item.thans.length === 0) {
        hasZeroThans = true;
      }
    });

    if (hasMissingSku) {
      e.preventDefault();
      alert('Please select a Fabric Quality / Item SKU for all item entries.');
      return false;
    }

    if (hasZeroThans) {
      e.preventDefault();
      alert('Please enter at least one Than / Roll meter reading for each fabric item before saving.');
      return false;
    }
  });

  document.addEventListener('DOMContentLoaded', () => {
    renderAllItems();
    calculateOverallTotals();
  });
</script>
@endpush
