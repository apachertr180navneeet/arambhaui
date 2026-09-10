@extends('layouts.app')

@section('title', 'Item Master & Inventory - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Masters Management</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Item Master</span></div>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <!-- KPI Metrics Row -->
  <div class="kpi-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:var(--primary-50); color:var(--primary-600); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.29 7 12 12 20.71 7"/><line x1="12" y1="22" x2="12" y2="12"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Total Item SKUs</div>
        <div style="font-size:1.45rem; font-weight:800; color:var(--slate-900); margin-top:2px;">{{ $stats['total'] }}</div>
      </div>
    </div>

    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4 22h14a2 2 0 0 0 2-2V7.5L14.5 2H6a2 2 0 0 0-2 2v4"/><polyline points="14 2 14 8 20 8"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Fabric Rolls & Yarn</div>
        <div style="font-size:1.45rem; font-weight:800; color:var(--slate-900); margin-top:2px;">{{ $stats['fabricCount'] }} SKUs</div>
      </div>
    </div>

    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:{{ $stats['lowStockCount'] > 0 ? '#fef2f2' : '#f0fdf4' }}; color:{{ $stats['lowStockCount'] > 0 ? '#dc2626' : '#16a34a' }}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Low Stock Reorder Alert</div>
        <div style="font-size:1.45rem; font-weight:800; color:{{ $stats['lowStockCount'] > 0 ? '#dc2626' : '#16a34a' }}; margin-top:2px;">{{ $stats['lowStockCount'] }} Low Stock</div>
      </div>
    </div>
  </div>

  <!-- Main Card Container -->
  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:20px;">
    
    <!-- Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:20px;">
      <div>
        <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Item & Fabric Inventory</h3>
        <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">Raw materials, cotton weaves, polyester, buttons, zippers, thread & packaging</p>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('items-table', 'Item_Master.csv')" style="display:inline-flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Export CSV
        </button>
        <button class="btn btn-primary btn-sm" onclick="openItemModal()" style="display:inline-flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
          + Add Item SKU
        </button>
      </div>
    </div>

    <!-- Quick Filter Bar -->
    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:16px;">
      <input type="text" class="form-control" style="max-width:280px; font-size:0.85rem;" placeholder="Search item name, code, category, HSN..." onkeyup="UI.filterGenericTable('items-table', this.value)">
    </div>

    <!-- Item Table -->
    <div class="table-responsive">
      <table class="data-table" id="items-table" style="width:100%; font-size:0.85rem;">
        <thead>
          <tr>
            <th>Code</th>
            <th>Item Name & Specifications</th>
            <th>Category</th>
            <th>Unit</th>
            <th>Unit Cost (₹)</th>
            <th>Current Stock</th>
            <th>Reorder Min</th>
            <th>HSN Code</th>
            <th>Status</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($items as $itm)
            <tr>
              <td style="font-family:var(--font-mono); font-weight:700; color:var(--primary-600);">{{ $itm->code }}</td>
              <td>
                <div style="font-weight:700; color:var(--slate-800);">{{ $itm->name }}</div>
                @if($itm->fabric || $itm->color || $itm->size)
                  <div style="font-size:0.75rem; color:var(--slate-500);">
                    {{ implode(' • ', array_filter([$itm->fabric, $itm->color, $itm->size])) }}
                  </div>
                @endif
              </td>
              <td><span class="badge badge-info">{{ $itm->category }}</span></td>
              <td style="font-weight:600;">{{ $itm->unit }}</td>
              <td style="font-weight:600;">₹{{ number_format($itm->unit_cost, 2) }}</td>
              <td style="font-weight:800; color:{{ $itm->current_stock <= $itm->min_stock ? 'var(--danger-600)' : 'var(--slate-800)' }};">
                {{ number_format($itm->current_stock, 2) }}
              </td>
              <td style="font-size:0.8rem; color:var(--slate-500);">{{ number_format($itm->min_stock, 2) }}</td>
              <td style="font-family:var(--font-mono); font-size:0.8rem;">{{ $itm->hsn_code ?: '—' }}</td>
              <td>
                @if($itm->current_stock <= $itm->min_stock)
                  <span class="badge badge-danger">Low Stock</span>
                @else
                  <span class="badge badge-success">In Stock</span>
                @endif
              </td>
              <td style="text-align:right;">
                <div style="display:inline-flex; gap:6px;">
                  <button class="btn btn-secondary btn-xs" onclick='openItemModal(@json($itm))'>Edit</button>
                  <form action="{{ route('masters.items.destroy', $itm->id) }}" method="POST" onsubmit="return confirm('Delete item {{ $itm->name }}?')" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-xs">&times;</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="10" style="text-align:center; padding:30px; color:var(--slate-400);">
                No item records found. Click "+ Add Item SKU" to register raw materials.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>

<!-- Add / Edit Item Modal -->
<div id="item-modal" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div class="modal-box" style="background:#fff; border-radius:16px; width:100%; max-width:650px; padding:24px; max-height:90vh; overflow-y:auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid var(--slate-200); padding-bottom:12px;">
      <h3 id="imodual-title" style="margin:0; font-size:1.15rem; font-weight:800;">Add Item SKU</h3>
      <button onclick="closeItemModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--slate-400);">&times;</button>
    </div>

    <form id="item-form" method="POST" action="{{ route('masters.items.store') }}">
      @csrf
      <input type="hidden" name="_method" id="iform-method" value="POST">

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
        <div class="form-group">
          <label class="form-label">Item / Fabric Name <span style="color:red;">*</span></label>
          <input type="text" name="name" id="itm_name" class="form-control" required placeholder="e.g. 100% Cotton Single Jersey 180 GSM">
        </div>

        <div class="form-group">
          <label class="form-label">Category <span style="color:red;">*</span></label>
          <select name="category" id="itm_category" class="form-control" required>
            <option value="Fabric">Fabric (Knit/Woven)</option>
            <option value="Yarn">Yarn / Thread</option>
            <option value="Trims">Trims (Buttons, Zippers, Rib)</option>
            <option value="Packaging">Packaging (Polybag, Tag, Box)</option>
            <option value="Finished Goods">Finished Garments</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Unit of Measure (UOM)</label>
          <select name="unit" id="itm_unit" class="form-control">
            <option value="Meters">Meters (m)</option>
            <option value="Kilograms">Kilograms (kg)</option>
            <option value="Pieces">Pieces (pcs)</option>
            <option value="Yards">Yards</option>
            <option value="Rolls">Rolls</option>
            <option value="Gross">Gross (144 pcs)</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Standard Unit Cost (₹)</label>
          <input type="number" step="0.01" name="unit_cost" id="itm_cost" class="form-control" value="280.00">
        </div>

        <div class="form-group">
          <label class="form-label">Opening / Current Stock</label>
          <input type="number" step="0.01" name="current_stock" id="itm_stock" class="form-control" value="500.00">
        </div>

        <div class="form-group">
          <label class="form-label">Minimum Reorder Level</label>
          <input type="number" step="0.01" name="min_stock" id="itm_min_stock" class="form-control" value="100.00">
        </div>

        <div class="form-group">
          <label class="form-label">HSN Code</label>
          <input type="text" name="hsn_code" id="itm_hsn" class="form-control" placeholder="e.g. 5208">
        </div>

        <div class="form-group">
          <label class="form-label">Storage Location / Rack</label>
          <input type="text" name="location" id="itm_location" class="form-control" placeholder="e.g. Warehouse A - Rack 04">
        </div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px; border-top:1px solid var(--slate-200); padding-top:14px;">
        <button type="button" class="btn btn-secondary" onclick="closeItemModal()">Cancel</button>
        <button type="submit" class="btn btn-primary" id="isubmit-btn">Save Item</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  function openItemModal(itm = null) {
    const modal = document.getElementById('item-modal');
    const form = document.getElementById('item-form');
    const methodInput = document.getElementById('iform-method');
    const title = document.getElementById('imodual-title');

    if (itm) {
      title.innerText = 'Edit Item: ' + (itm.code || itm.name);
      form.action = `/masters/items/${itm.id}`;
      methodInput.value = 'PUT';

      document.getElementById('itm_name').value = itm.name || '';
      document.getElementById('itm_category').value = itm.category || 'Fabric';
      document.getElementById('itm_unit').value = itm.unit || 'Meters';
      document.getElementById('itm_cost').value = itm.unit_cost || 0;
      document.getElementById('itm_stock').value = itm.current_stock || 0;
      document.getElementById('itm_min_stock').value = itm.min_stock || 100;
      document.getElementById('itm_hsn').value = itm.hsn_code || '';
      document.getElementById('itm_location').value = itm.location || '';
      document.getElementById('isubmit-btn').innerText = 'Update Item';
    } else {
      title.innerText = 'Add Item SKU';
      form.action = '{{ route('masters.items.store') }}';
      methodInput.value = 'POST';
      form.reset();
      document.getElementById('itm_cost').value = '280.00';
      document.getElementById('itm_stock').value = '500.00';
      document.getElementById('itm_min_stock').value = '100.00';
      document.getElementById('isubmit-btn').innerText = 'Save Item';
    }

    modal.style.display = 'flex';
  }

  function closeItemModal() {
    document.getElementById('item-modal').style.display = 'none';
  }
</script>
@endpush
@endsection
