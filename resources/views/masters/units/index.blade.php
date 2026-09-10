@extends('layouts.app')

@section('title', 'Unit Master - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Masters Management</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Unit Master</span></div>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <!-- KPI Metrics Row -->
  <div class="kpi-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:var(--primary-50); color:var(--primary-600); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Total Units Defined</div>
        <div style="font-size:1.45rem; font-weight:800; color:var(--slate-900); margin-top:2px;">{{ $stats['total'] }}</div>
      </div>
    </div>

    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Base Base Units</div>
        <div style="font-size:1.45rem; font-weight:800; color:var(--slate-900); margin-top:2px;">{{ $stats['baseUnits'] }} Base</div>
      </div>
    </div>

    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:#faf5ff; color:#9333ea; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 3h5v5"/><path d="M8 21H3v-5"/><path d="M21 3l-7 7"/><path d="M3 21l7-7"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Derived Sub-Units</div>
        <div style="font-size:1.45rem; font-weight:800; color:var(--slate-900); margin-top:2px;">{{ $stats['derivedUnits'] }} Converted</div>
      </div>
    </div>
  </div>

  <!-- Main Card Container -->
  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:20px;">
    
    <!-- Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:20px;">
      <div>
        <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Unit of Measurement (UOM) Master</h3>
        <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">Meters, Rolls, Kilograms, Pieces, Cones, Dozen & Gross conversion rates</p>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('units-table', 'Unit_Master.csv')" style="display:inline-flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Export CSV
        </button>
        <button class="btn btn-primary btn-sm" onclick="openUnitModal()" style="display:inline-flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
          + Add Unit
        </button>
      </div>
    </div>

    <!-- Quick Filter Bar -->
    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:16px;">
      <input type="text" class="form-control" style="max-width:280px; font-size:0.85rem;" placeholder="Search unit name, code, symbol..." onkeyup="UI.filterGenericTable('units-table', this.value)">
    </div>

    <!-- Unit Table -->
    <div class="table-responsive">
      <table class="data-table" id="units-table" style="width:100%; font-size:0.85rem;">
        <thead>
          <tr>
            <th>Code</th>
            <th>Unit Name</th>
            <th>Symbol</th>
            <th>Type</th>
            <th>Parent Base Unit</th>
            <th>Conversion Factor</th>
            <th>Decimals</th>
            <th>Status</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($units as $u)
            <tr>
              <td style="font-family:var(--font-mono); font-weight:700; color:var(--primary-600);">{{ $u->code }}</td>
              <td style="font-weight:700; color:var(--slate-800);">{{ $u->name }}</td>
              <td style="font-family:var(--font-mono); font-weight:600;">{{ $u->symbol ?: '—' }}</td>
              <td>
                @if($u->parent_id)
                  <span class="badge badge-purple">Derived Unit</span>
                @else
                  <span class="badge badge-primary">Base Unit</span>
                @endif
              </td>
              <td>{{ $u->parent ? $u->parent->name : '— (Self Base)' }}</td>
              <td style="font-weight:600;">
                @if($u->parent_id && $u->conversion_factor)
                  1 {{ $u->code }} = {{ $u->conversion_factor }} {{ $u->parent->symbol ?: $u->parent->code }}
                @else
                  1.0000
                @endif
              </td>
              <td>{{ $u->decimal_places ?? 2 }}</td>
              <td><span class="badge badge-success">{{ $u->status }}</span></td>
              <td style="text-align:right;">
                <div style="display:inline-flex; gap:6px;">
                  <button class="btn btn-secondary btn-xs" onclick='openUnitModal(@json($u))'>Edit</button>
                  <form action="{{ route('masters.units.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Delete unit {{ $u->name }}?')" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-xs">&times;</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" style="text-align:center; padding:30px; color:var(--slate-400);">
                No unit records found. Click "+ Add Unit" to define measurement units.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>

<!-- Add / Edit Unit Modal -->
<div id="unit-modal" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div class="modal-box" style="background:#fff; border-radius:16px; width:100%; max-width:550px; padding:24px; max-height:90vh; overflow-y:auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid var(--slate-200); padding-bottom:12px;">
      <h3 id="umodal-title" style="margin:0; font-size:1.15rem; font-weight:800;">Add Unit</h3>
      <button onclick="closeUnitModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--slate-400);">&times;</button>
    </div>

    <form id="unit-form" method="POST" action="{{ route('masters.units.store') }}">
      @csrf
      <input type="hidden" name="_method" id="uform-method" value="POST">

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
        <div class="form-group">
          <label class="form-label">Unit Name <span style="color:red;">*</span></label>
          <input type="text" name="name" id="unit_name" class="form-control" required placeholder="e.g. Meters, Rolls, Kg">
        </div>
        <div class="form-group">
          <label class="form-label">Unit Code / Abbr</label>
          <input type="text" name="code" id="unit_code" class="form-control" placeholder="e.g. MTR, KG, PCS">
        </div>
        <div class="form-group">
          <label class="form-label">Symbol</label>
          <input type="text" name="symbol" id="unit_symbol" class="form-control" placeholder="e.g. m, kg, pcs">
        </div>
        <div class="form-group">
          <label class="form-label">Base Parent Unit</label>
          <select name="parent_id" id="unit_parent" class="form-control">
            <option value="">None (This is a Base Unit)</option>
            @foreach($parentUnits as $pu)
              <option value="{{ $pu->id }}">{{ $pu->name }} ({{ $pu->code }})</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Conversion Factor</label>
          <input type="number" step="0.0001" name="conversion_factor" id="unit_factor" class="form-control" placeholder="e.g. 1 Roll = 100 Meters">
        </div>
        <div class="form-group">
          <label class="form-label">Decimal Places</label>
          <input type="number" name="decimal_places" id="unit_decimals" class="form-control" value="2" min="0" max="4">
        </div>
        <div class="form-group" style="grid-column:1/-1;">
          <label class="form-label">Status</label>
          <select name="status" id="unit_status" class="form-control">
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px; border-top:1px solid var(--slate-200); padding-top:14px;">
        <button type="button" class="btn btn-secondary" onclick="closeUnitModal()">Cancel</button>
        <button type="submit" class="btn btn-primary" id="usubmit-btn">Save Unit</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  function openUnitModal(u = null) {
    const modal = document.getElementById('unit-modal');
    const form = document.getElementById('unit-form');
    const methodInput = document.getElementById('uform-method');
    const title = document.getElementById('umodal-title');

    if (u) {
      title.innerText = 'Edit Unit: ' + (u.code || u.name);
      form.action = `/masters/units/${u.id}`;
      methodInput.value = 'PUT';

      document.getElementById('unit_name').value = u.name || '';
      document.getElementById('unit_code').value = u.code || '';
      document.getElementById('unit_symbol').value = u.symbol || '';
      document.getElementById('unit_parent').value = u.parent_id || '';
      document.getElementById('unit_factor').value = u.conversion_factor || '';
      document.getElementById('unit_decimals').value = u.decimal_places ?? 2;
      document.getElementById('unit_status').value = u.status || 'Active';
      document.getElementById('usubmit-btn').innerText = 'Update Unit';
    } else {
      title.innerText = 'Add Unit';
      form.action = '{{ route('masters.units.store') }}';
      methodInput.value = 'POST';
      form.reset();
      document.getElementById('unit_decimals').value = '2';
      document.getElementById('unit_status').value = 'Active';
      document.getElementById('usubmit-btn').innerText = 'Save Unit';
    }

    modal.style.display = 'flex';
  }

  function closeUnitModal() {
    document.getElementById('unit-modal').style.display = 'none';
  }
</script>
@endpush
@endsection
