@extends('layouts.app')

@section('title', 'Job Worker Master - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Masters Management</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Job Worker Master</span></div>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <!-- KPI Metrics Row -->
  <div class="kpi-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:var(--primary-50); color:var(--primary-600); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Registered Job Workers</div>
        <div style="font-size:1.45rem; font-weight:800; color:var(--slate-900); margin-top:2px;">{{ $stats['total'] }}</div>
      </div>
    </div>

    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Active Status</div>
        <div style="font-size:1.45rem; font-weight:800; color:var(--slate-900); margin-top:2px;">{{ $stats['active'] }} Active</div>
      </div>
    </div>

    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:#faf5ff; color:#9333ea; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Total Daily Capacity</div>
        <div style="font-size:1.45rem; font-weight:800; color:var(--slate-900); margin-top:2px;">{{ number_format($stats['totalCapacity']) }} Pcs/Day</div>
      </div>
    </div>
  </div>

  <!-- Main Card Container -->
  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:20px;">
    
    <!-- Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:20px;">
      <div>
        <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Job Worker Directory</h3>
        <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">Stitching units, cutting masters, embroidery & washing contractors</p>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('jobworkers-table', 'JobWorker_Master.csv')" style="display:inline-flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Export CSV
        </button>
        <button class="btn btn-primary btn-sm" onclick="openJobWorkerModal()" style="display:inline-flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
          + Add Job Worker
        </button>
      </div>
    </div>

    <!-- Quick Filter Bar -->
    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:16px;">
      <input type="text" class="form-control" style="max-width:280px; font-size:0.85rem;" placeholder="Search worker, skill, phone..." onkeyup="UI.filterGenericTable('jobworkers-table', this.value)">
    </div>

    <!-- Job Worker Table -->
    <div class="table-responsive">
      <table class="data-table" id="jobworkers-table" style="width:100%; font-size:0.85rem;">
        <thead>
          <tr>
            <th>Code</th>
            <th>Worker / Unit Name</th>
            <th>Skill Process</th>
            <th>Rate / Pc (₹)</th>
            <th>Daily Capacity</th>
            <th>Phone</th>
            <th>Status</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($jobworkers as $jw)
            <tr>
              <td style="font-family:var(--font-mono); font-weight:700; color:var(--primary-600);">{{ $jw->code }}</td>
              <td style="font-weight:700; color:var(--slate-800);">{{ $jw->name }}</td>
              <td><span class="badge badge-info">{{ $jw->skill_type }}</span></td>
              <td style="font-weight:700; color:#059669;">₹{{ number_format($jw->rate_per_piece, 2) }}</td>
              <td style="font-weight:600;">{{ number_format($jw->daily_capacity) }} pcs</td>
              <td>{{ $jw->phone }}</td>
              <td><span class="badge badge-success">{{ $jw->status }}</span></td>
              <td style="text-align:right;">
                <div style="display:inline-flex; gap:6px;">
                  <button class="btn btn-secondary btn-xs" onclick='openJobWorkerModal(@json($jw))'>Edit</button>
                  <form action="{{ route('masters.jobworkers.destroy', $jw->id) }}" method="POST" onsubmit="return confirm('Delete job worker {{ $jw->name }}?')" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-xs">&times;</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" style="text-align:center; padding:30px; color:var(--slate-400);">
                No job workers found. Click "+ Add Job Worker" to register manufacturing contractors.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>

<!-- Add / Edit Job Worker Modal -->
<div id="jobworker-modal" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div class="modal-box" style="background:#fff; border-radius:16px; width:100%; max-width:600px; padding:24px; max-height:90vh; overflow-y:auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid var(--slate-200); padding-bottom:12px;">
      <h3 id="jwmodal-title" style="margin:0; font-size:1.15rem; font-weight:800;">Add New Job Worker</h3>
      <button onclick="closeJobWorkerModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--slate-400);">&times;</button>
    </div>

    <form id="jobworker-form" method="POST" action="{{ route('masters.jobworkers.store') }}">
      @csrf
      <input type="hidden" name="_method" id="jwform-method" value="POST">

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
        <div class="form-group">
          <label class="form-label">Worker / Unit Name <span style="color:red;">*</span></label>
          <input type="text" name="name" id="jw_name" class="form-control" required placeholder="e.g. Raj Stitching Works">
        </div>
        <div class="form-group">
          <label class="form-label">Phone / Mobile <span style="color:red;">*</span></label>
          <input type="text" name="phone" id="jw_phone" class="form-control" required placeholder="e.g. +91 98450 67890">
        </div>
        <div class="form-group">
          <label class="form-label">Specialization / Process <span style="color:red;">*</span></label>
          <select name="skill_type" id="jw_skill" class="form-control" required>
            <option value="Stitching">Stitching / Sewing</option>
            <option value="Cutting">Fabric Cutting</option>
            <option value="Embroidery">Computer Embroidery</option>
            <option value="Printing">Screen / Digital Printing</option>
            <option value="Washing & Finishing">Washing & Finishing</option>
            <option value="Ironing & Packing">Ironing & Packing</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Default Rate / Piece (₹) <span style="color:red;">*</span></label>
          <input type="number" step="0.01" name="rate_per_piece" id="jw_rate" class="form-control" required value="45.00">
        </div>
        <div class="form-group">
          <label class="form-label">Daily Capacity (Pcs)</label>
          <input type="number" name="daily_capacity" id="jw_capacity" class="form-control" value="500">
        </div>
        <div class="form-group">
          <label class="form-label">Status</label>
          <select name="status" id="jw_status" class="form-control">
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>
      </div>

      <div class="form-group" style="margin-top:14px;">
        <label class="form-label">Unit Workshop Address</label>
        <textarea name="address" id="jw_address" class="form-control" rows="2" placeholder="Workshop address, industrial gala..."></textarea>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px; border-top:1px solid var(--slate-200); padding-top:14px;">
        <button type="button" class="btn btn-secondary" onclick="closeJobWorkerModal()">Cancel</button>
        <button type="submit" class="btn btn-primary" id="jwsubmit-btn">Save Job Worker</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  function openJobWorkerModal(jw = null) {
    const modal = document.getElementById('jobworker-modal');
    const form = document.getElementById('jobworker-form');
    const methodInput = document.getElementById('jwform-method');
    const title = document.getElementById('jwmodal-title');

    if (jw) {
      title.innerText = 'Edit Job Worker: ' + (jw.code || jw.name);
      form.action = `/masters/jobworkers/${jw.id}`;
      methodInput.value = 'PUT';

      document.getElementById('jw_name').value = jw.name || '';
      document.getElementById('jw_phone').value = jw.phone || '';
      document.getElementById('jw_skill').value = jw.skill_type || 'Stitching';
      document.getElementById('jw_rate').value = jw.rate_per_piece || 45;
      document.getElementById('jw_capacity').value = jw.daily_capacity || 500;
      document.getElementById('jw_status').value = jw.status || 'Active';
      document.getElementById('jw_address').value = jw.address || '';
      document.getElementById('jwsubmit-btn').innerText = 'Update Job Worker';
    } else {
      title.innerText = 'Add New Job Worker';
      form.action = '{{ route('masters.jobworkers.store') }}';
      methodInput.value = 'POST';
      form.reset();
      document.getElementById('jw_rate').value = '45.00';
      document.getElementById('jw_capacity').value = '500';
      document.getElementById('jw_status').value = 'Active';
      document.getElementById('jwsubmit-btn').innerText = 'Save Job Worker';
    }

    modal.style.display = 'flex';
  }

  function closeJobWorkerModal() {
    document.getElementById('jobworker-modal').style.display = 'none';
  }
</script>
@endpush
@endsection
