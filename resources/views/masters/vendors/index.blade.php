@extends('layouts.app')

@section('title', 'Vendor Master - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Masters Management</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Vendor Master</span></div>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <!-- KPI Metrics Row -->
  <div class="kpi-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Total Suppliers</div>
        <div style="font-size:1.45rem; font-weight:800; color:var(--slate-900); margin-top:2px;">{{ $stats['total'] }}</div>
      </div>
    </div>

    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Fabric Mills & Mills</div>
        <div style="font-size:1.45rem; font-weight:800; color:var(--slate-900); margin-top:2px;">{{ $stats['fabricVendors'] }}</div>
      </div>
    </div>

    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:#faf5ff; color:#9333ea; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Trims & Accessories</div>
        <div style="font-size:1.45rem; font-weight:800; color:var(--slate-900); margin-top:2px;">{{ $stats['trimVendors'] }}</div>
      </div>
    </div>
  </div>

  <!-- Main Card Container -->
  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:20px;">
    
    <!-- Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:20px;">
      <div>
        <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Vendor Directory</h3>
        <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">Raw fabric mills, yarn suppliers, thread & button vendors</p>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('vendors-table', 'Vendor_Master.csv')" style="display:inline-flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Export CSV
        </button>
        <button class="btn btn-primary btn-sm" onclick="openVendorModal()" style="display:inline-flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
          + Add Vendor
        </button>
      </div>
    </div>

    <!-- Quick Filter Bar -->
    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:16px;">
      <input type="text" class="form-control" style="max-width:280px; font-size:0.85rem;" placeholder="Search vendor name, category, phone..." onkeyup="UI.filterGenericTable('vendors-table', this.value)">
    </div>

    <!-- Vendor Table -->
    <div class="table-responsive">
      <table class="data-table" id="vendors-table" style="width:100%; font-size:0.85rem;">
        <thead>
          <tr>
            <th>Code</th>
            <th>Vendor / Firm Name</th>
            <th>Category</th>
            <th>Phone / Contact</th>
            <th>GSTIN</th>
            <th>Credit Days</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($vendors as $v)
            <tr>
              <td style="font-family:var(--font-mono); font-weight:700; color:var(--primary-600);">{{ $v->code }}</td>
              <td>
                <div style="font-weight:700; color:var(--slate-800);">{{ $v->name }}</div>
                @if($v->company_name)
                  <div style="font-size:0.75rem; color:var(--slate-500);">{{ $v->company_name }}</div>
                @endif
              </td>
              <td><span class="badge badge-info">{{ $v->category ?: 'General' }}</span></td>
              <td>
                <div style="font-weight:600;">{{ $v->phone }}</div>
                @if($v->email)
                  <div style="font-size:0.75rem; color:var(--slate-500);">{{ $v->email }}</div>
                @endif
              </td>
              <td style="font-family:var(--font-mono); font-size:0.8rem;">{{ $v->gstin ?: '—' }}</td>
              <td style="font-weight:600;">{{ $v->credit_days ? $v->credit_days . ' Days' : 'Immediate' }}</td>
              <td style="text-align:right;">
                <div style="display:inline-flex; gap:6px;">
                  <button class="btn btn-secondary btn-xs" onclick='openVendorModal(@json($v))'>Edit</button>
                  <form action="{{ route('masters.vendors.destroy', $v->id) }}" method="POST" onsubmit="return confirm('Delete vendor {{ $v->name }}?')" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-xs">&times;</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align:center; padding:30px; color:var(--slate-400);">
                No vendor records found. Click "+ Add Vendor" to register raw material suppliers.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>

<!-- Add / Edit Vendor Modal -->
<div id="vendor-modal" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div class="modal-box" style="background:#fff; border-radius:16px; width:100%; max-width:600px; padding:24px; max-height:90vh; overflow-y:auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid var(--slate-200); padding-bottom:12px;">
      <h3 id="vmodal-title" style="margin:0; font-size:1.15rem; font-weight:800;">Add New Vendor</h3>
      <button onclick="closeVendorModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--slate-400);">&times;</button>
    </div>

    <form id="vendor-form" method="POST" action="{{ route('masters.vendors.store') }}">
      @csrf
      <input type="hidden" name="_method" id="vform-method" value="POST">

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
        <div class="form-group">
          <label class="form-label">Vendor Name <span style="color:red;">*</span></label>
          <input type="text" name="name" id="vend_name" class="form-control" required placeholder="e.g. Vardhman Textiles">
        </div>
        <div class="form-group">
          <label class="form-label">Phone / Mobile <span style="color:red;">*</span></label>
          <input type="text" name="phone" id="vend_phone" class="form-control" required placeholder="e.g. +91 98200 11223">
        </div>
        <div class="form-group">
          <label class="form-label">Category</label>
          <select name="category" id="vend_category" class="form-control">
            <option value="Fabric">Fabric Mill</option>
            <option value="Yarn">Yarn & Thread</option>
            <option value="Trims & Accessories">Trims & Accessories (Buttons/Zippers)</option>
            <option value="Packaging">Packaging Materials</option>
            <option value="Dyes & Chemicals">Dyes & Chemicals</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Credit Days</label>
          <input type="number" name="credit_days" id="vend_credit_days" class="form-control" value="30">
        </div>
        <div class="form-group">
          <label class="form-label">Email</label>
          <input type="email" name="email" id="vend_email" class="form-control" placeholder="sales@mill.com">
        </div>
        <div class="form-group">
          <label class="form-label">GSTIN</label>
          <input type="text" name="gstin" id="vend_gstin" class="form-control" placeholder="27AAAAA0000A1Z5">
        </div>
      </div>

      <div class="form-group" style="margin-top:14px;">
        <label class="form-label">Address</label>
        <textarea name="address" id="vend_address" class="form-control" rows="2" placeholder="Mill address, city..."></textarea>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px; border-top:1px solid var(--slate-200); padding-top:14px;">
        <button type="button" class="btn btn-secondary" onclick="closeVendorModal()">Cancel</button>
        <button type="submit" class="btn btn-primary" id="vsubmit-btn">Save Vendor</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  function openVendorModal(v = null) {
    const modal = document.getElementById('vendor-modal');
    const form = document.getElementById('vendor-form');
    const methodInput = document.getElementById('vform-method');
    const title = document.getElementById('vmodal-title');

    if (v) {
      title.innerText = 'Edit Vendor: ' + (v.code || v.name);
      form.action = `/masters/vendors/${v.id}`;
      methodInput.value = 'PUT';

      document.getElementById('vend_name').value = v.name || '';
      document.getElementById('vend_phone').value = v.phone || '';
      document.getElementById('vend_category').value = v.category || 'Fabric';
      document.getElementById('vend_credit_days').value = v.credit_days || 30;
      document.getElementById('vend_email').value = v.email || '';
      document.getElementById('vend_gstin').value = v.gstin || '';
      document.getElementById('vend_address').value = v.address || '';
      document.getElementById('vsubmit-btn').innerText = 'Update Vendor';
    } else {
      title.innerText = 'Add New Vendor';
      form.action = '{{ route('masters.vendors.store') }}';
      methodInput.value = 'POST';
      form.reset();
      document.getElementById('vend_credit_days').value = '30';
      document.getElementById('vsubmit-btn').innerText = 'Save Vendor';
    }

    modal.style.display = 'flex';
  }

  function closeVendorModal() {
    document.getElementById('vendor-modal').style.display = 'none';
  }
</script>
@endpush
@endsection
