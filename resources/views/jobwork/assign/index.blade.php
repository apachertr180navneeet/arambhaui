@extends('layouts.app')

@section('title', 'Job Work & Assignment - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Job Work & Assign</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Job Assign Orders</span></div>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <!-- KPI Metrics Row -->
  <div class="kpi-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:var(--primary-50); color:var(--primary-600); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Job Assignment Orders</div>
        <div style="font-size:1.45rem; font-weight:800; color:var(--slate-900); margin-top:2px;">{{ $stats['totalOrders'] }}</div>
      </div>
    </div>

    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Total Issued Pieces</div>
        <div style="font-size:1.45rem; font-weight:800; color:var(--slate-900); margin-top:2px;">{{ number_format($stats['totalIssuedQty']) }} pcs</div>
      </div>
    </div>

    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Contract Labor Value</div>
        <div style="font-size:1.45rem; font-weight:800; color:#059669; margin-top:2px;">₹{{ number_format($stats['totalProcessValue'], 2) }}</div>
      </div>
    </div>
  </div>

  <!-- Main Card Container -->
  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:20px;">
    
    <!-- Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:20px;">
      <div>
        <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Job Work Assignment Registry</h3>
        <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">Outward job orders for Stitching, Fabric Cutting, Embroidery & Washing</p>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('job-assign-table', 'Job_Assignments.csv')" style="display:inline-flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Export CSV
        </button>
        <button class="btn btn-primary btn-sm" onclick="openJobAssignModal()" style="display:inline-flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
          + Issue Job Order
        </button>
      </div>
    </div>

    <!-- Quick Filter Bar -->
    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:16px;">
      <input type="text" class="form-control" style="max-width:280px; font-size:0.85rem;" placeholder="Search JA#, worker name, lot#, style..." onkeyup="UI.filterGenericTable('job-assign-table', this.value)">
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="data-table" id="job-assign-table" style="width:100%; font-size:0.85rem;">
        <thead>
          <tr>
            <th>Order #</th>
            <th>Job Worker</th>
            <th>Process</th>
            <th>Lot #</th>
            <th>Style Description</th>
            <th>Issue Date</th>
            <th>Due Date</th>
            <th>Issued Qty</th>
            <th>Rate / Pc (₹)</th>
            <th>Total Amount</th>
            <th>Status</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($assignments as $ja)
            <tr>
              <td style="font-family:var(--font-mono); font-weight:700; color:var(--primary-600);">{{ $ja->job_order_no }}</td>
              <td style="font-weight:700; color:var(--slate-800);">{{ $ja->job_worker_name }}</td>
              <td><span class="badge badge-info">{{ $ja->process_name }}</span></td>
              <td style="font-family:var(--font-mono); font-weight:600;">{{ $ja->lot_number }}</td>
              <td>{{ $ja->style_name }}</td>
              <td>{{ date('d M Y', strtotime($ja->issue_date)) }}</td>
              <td>{{ $ja->due_date ? date('d M Y', strtotime($ja->due_date)) : '—' }}</td>
              <td style="font-weight:700;">{{ number_format($ja->issued_qty) }} pcs</td>
              <td>₹{{ number_format($ja->rate_per_piece, 2) }}</td>
              <td style="font-weight:800; color:#059669;">₹{{ number_format($ja->total_amount, 2) }}</td>
              <td><span class="badge badge-success">{{ $ja->status }}</span></td>
              <td style="text-align:right;">
                <form action="{{ route('jobwork.assign.destroy', $ja->id) }}" method="POST" onsubmit="return confirm('Delete Job Order {{ $ja->job_order_no }}?')" style="display:inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-xs">&times;</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="12" style="text-align:center; padding:30px; color:var(--slate-400);">
                No job work orders assigned yet. Click "+ Issue Job Order" to dispatch outward cutting or stitching.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>

<!-- Issue Job Work Order Modal -->
<div id="ja-modal" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div class="modal-box" style="background:#fff; border-radius:16px; width:100%; max-width:650px; padding:24px; max-height:90vh; overflow-y:auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid var(--slate-200); padding-bottom:12px;">
      <h3 style="margin:0; font-size:1.15rem; font-weight:800;">Issue Outward Job Work Order</h3>
      <button onclick="closeJobAssignModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--slate-400);">&times;</button>
    </div>

    <form method="POST" action="{{ route('jobwork.assign.store') }}">
      @csrf

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
        <div class="form-group">
          <label class="form-label">Job Worker Contractor <span style="color:red;">*</span></label>
          <select name="job_worker_name" id="ja_worker" class="form-control" required onchange="onWorkerSelect(this)">
            <option value="">-- Select Job Worker --</option>
            @foreach($jobworkers as $jw)
              <option value="{{ $jw->name }}" data-rate="{{ $jw->rate_per_piece }}" data-process="{{ $jw->skill_type }}">{{ $jw->name }} ({{ $jw->skill_type }})</option>
            @endforeach
            <option value="Raj Stitching Works" data-rate="45.00" data-process="Stitching">Raj Stitching Works (Stitching)</option>
            <option value="Modern Cutters & Co." data-rate="12.00" data-process="Cutting">Modern Cutters & Co. (Cutting)</option>
            <option value="Fine Finish Embroidery" data-rate="35.00" data-process="Embroidery">Fine Finish Embroidery (Embroidery)</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Process / Operation <span style="color:red;">*</span></label>
          <select name="process_name" id="ja_process" class="form-control" required>
            <option value="Stitching">Stitching</option>
            <option value="Cutting">Fabric Cutting</option>
            <option value="Embroidery">Embroidery</option>
            <option value="Printing">Printing</option>
            <option value="Washing & Finishing">Washing & Finishing</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Lot Number <span style="color:red;">*</span></label>
          <input type="text" name="lot_number" class="form-control" required placeholder="e.g. LOT-2026-089" value="LOT-2026-{{ rand(100, 999) }}">
        </div>

        <div class="form-group">
          <label class="form-label">Style / Article Description <span style="color:red;">*</span></label>
          <input type="text" name="style_name" class="form-control" required placeholder="e.g. Men's Polo T-Shirt Navy" value="Men's Premium Cotton Polo T-Shirt">
        </div>

        <div class="form-group">
          <label class="form-label">Issue Date <span style="color:red;">*</span></label>
          <input type="date" name="issue_date" class="form-control" required value="{{ date('Y-m-d') }}">
        </div>

        <div class="form-group">
          <label class="form-label">Expected Due Date</label>
          <input type="date" name="due_date" class="form-control" value="{{ date('Y-m-d', strtotime('+7 days')) }}">
        </div>

        <div class="form-group">
          <label class="form-label">Issued Quantity (Pcs) <span style="color:red;">*</span></label>
          <input type="number" name="issued_qty" id="ja_qty" class="form-control" required value="1000" min="1" oninput="calcJaTotal()">
        </div>

        <div class="form-group">
          <label class="form-label">Agreed Rate / Piece (₹) <span style="color:red;">*</span></label>
          <input type="number" step="0.01" name="rate_per_piece" id="ja_rate" class="form-control" required value="45.00" oninput="calcJaTotal()">
        </div>
      </div>

      <div class="form-group" style="margin-top:14px;">
        <label class="form-label">Cutting / Stitching Technical Instructions</label>
        <textarea name="instructions" class="form-control" rows="2" placeholder="Seam allowance 1.2cm, 100% cotton thread matching tone..."></textarea>
      </div>

      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:12px; margin-top:14px; display:flex; justify-content:space-between; align-items:center;">
        <span style="font-weight:600; color:var(--slate-700);">Estimated Job Work Value:</span>
        <strong style="font-size:1.15rem; color:#059669;" id="ja_total_preview">₹45,000.00</strong>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px; border-top:1px solid var(--slate-200); padding-top:14px;">
        <button type="button" class="btn btn-secondary" onclick="closeJobAssignModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">Create Job Order</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  function openJobAssignModal() {
    document.getElementById('ja-modal').style.display = 'flex';
  }
  function closeJobAssignModal() {
    document.getElementById('ja-modal').style.display = 'none';
  }

  function onWorkerSelect(select) {
    const opt = select.options[select.selectedIndex];
    const rate = opt.getAttribute('data-rate');
    const proc = opt.getAttribute('data-process');
    if (rate) document.getElementById('ja_rate').value = rate;
    if (proc) document.getElementById('ja_process').value = proc;
    calcJaTotal();
  }

  function calcJaTotal() {
    const qty = parseFloat(document.getElementById('ja_qty').value) || 0;
    const rate = parseFloat(document.getElementById('ja_rate').value) || 0;
    const total = qty * rate;
    document.getElementById('ja_total_preview').innerText = '₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }
</script>
@endpush
@endsection
