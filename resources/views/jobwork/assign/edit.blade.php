@extends('layouts.app')

@section('title', 'Edit Job Work Order - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><a href="{{ route('jobwork.assign.index') }}" style="color:inherit; text-decoration:none;">Job Work & Assign</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Edit Order {{ $assign->job_order_no }}</span></div>
@endsection

@section('content')
<form action="{{ route('jobwork.assign.update', $assign->id) }}" method="POST" id="jw-form">
  @csrf
  @method('PUT')

  <div style="display:flex; flex-direction:column; gap:20px;">
    
    <!-- Top Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
      <div>
        <div style="display:flex; align-items:center; gap:8px;">
          <h2 style="margin:0; font-size:1.45rem; font-weight:800; color:var(--slate-900); letter-spacing:-0.02em;">Edit Job Work Order</h2>
          <span style="font-family:var(--font-mono, monospace); font-weight:800; font-size:1rem; background:#eef2ff; color:#4338ca; padding:3px 10px; border-radius:8px; border:1px solid #c7d2fe;">
            {{ $assign->job_order_no }}
          </span>
        </div>
        <p style="margin:4px 0 0; font-size:0.85rem; color:var(--slate-500);">Modify assigned contractor, process, quantity, rate, or status.</p>
      </div>

      <div style="display:flex; gap:10px; align-items:center;">
        <a href="{{ route('jobwork.assign.index') }}" class="btn btn-secondary" style="font-weight:700;">Cancel</a>
        <button type="submit" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:6px; font-weight:700; box-shadow:0 2px 8px rgba(79, 70, 229, 0.3);">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
          Update Job Order
        </button>
      </div>
    </div>

    <!-- Step 1: Contractor & Manufacturing Process -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:24px;">
      <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin-top:0; margin-bottom:18px; color:var(--slate-800); border-bottom:1px solid var(--slate-100); padding-bottom:10px;">
        1. Contractor & Process Specification
      </h3>

      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:18px;">
        
        <!-- Dynamic Job Worker Select -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Job Worker Contractor <span style="color:#ef4444;">*</span></label>
          <select name="job_worker_name" id="ja_worker" class="form-control" required onchange="onWorkerSelect(this)">
            <option value="{{ $assign->job_worker_name }}" selected>{{ $assign->job_worker_name }}</option>
            @foreach($jobworkers as $jw)
              @if($jw->name !== $assign->job_worker_name)
                <option value="{{ $jw->name }}" data-rate="{{ $jw->rate_per_piece }}" data-process="{{ $jw->skill_type }}">{{ $jw->name }} ({{ $jw->skill_type ?? 'Contractor' }})</option>
              @endif
            @endforeach
          </select>
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Process / Operation <span style="color:#ef4444;">*</span></label>
          <select name="process_name" id="ja_process" class="form-control" required>
            <option value="Stitching" {{ $assign->process_name === 'Stitching' ? 'selected' : '' }}>Stitching</option>
            <option value="Cutting" {{ $assign->process_name === 'Cutting' ? 'selected' : '' }}>Fabric Cutting</option>
            <option value="Embroidery" {{ $assign->process_name === 'Embroidery' ? 'selected' : '' }}>Embroidery</option>
            <option value="Washing & Finishing" {{ $assign->process_name === 'Washing & Finishing' ? 'selected' : '' }}>Washing & Finishing</option>
            <option value="Ironing & Packing" {{ $assign->process_name === 'Ironing & Packing' ? 'selected' : '' }}>Ironing & Packing</option>
          </select>
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Lot Reference # <span style="color:#ef4444;">*</span></label>
          <input type="text" name="lot_number" class="form-control" required value="{{ $assign->lot_number }}" style="font-family:var(--font-mono, monospace); font-weight:700;">
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Style / Item Description <span style="color:#ef4444;">*</span></label>
          <input type="text" name="style_name" class="form-control" required value="{{ $assign->style_name }}">
        </div>

      </div>
    </div>

    <!-- Step 2: Quantities, Rates & Schedule -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:24px;">
      <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin-top:0; margin-bottom:18px; color:var(--slate-800); border-bottom:1px solid var(--slate-100); padding-bottom:10px;">
        2. Production Quantity, Rates & Timeline
      </h3>

      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:18px;">
        
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Issued Quantity (Pcs) <span style="color:#ef4444;">*</span></label>
          <input type="number" step="1" min="0" name="issued_qty" id="ja_qty" class="form-control" required value="{{ $assign->issued_qty }}" oninput="calculateJWTotal()" style="font-weight:700; font-size:1.05rem;">
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Contract Rate per Pc (₹) <span style="color:#ef4444;">*</span></label>
          <input type="number" step="0.5" min="0" name="rate_per_piece" id="ja_rate" class="form-control" required value="{{ $assign->rate_per_piece }}" oninput="calculateJWTotal()" style="font-weight:700;">
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Issue Date <span style="color:#ef4444;">*</span></label>
          <input type="date" name="issue_date" class="form-control" required value="{{ $assign->issue_date }}">
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Target Due Date</label>
          <input type="date" name="due_date" class="form-control" value="{{ $assign->due_date }}">
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; color:var(--slate-800);">Order Status</label>
          <select name="status" class="form-control">
            <option value="Issued" {{ $assign->status === 'Issued' ? 'selected' : '' }}>Issued</option>
            <option value="In Progress" {{ $assign->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
            <option value="Completed" {{ $assign->status === 'Completed' ? 'selected' : '' }}>Completed</option>
            <option value="Cancelled" {{ $assign->status === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
          </select>
        </div>

      </div>

      <!-- Financial Calculation Summary Footers -->
      <div style="display:flex; justify-content:flex-end; margin-top:20px;">
        <div style="width:340px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:18px; display:flex; flex-direction:column; gap:10px; font-size:0.875rem;">
          <div style="display:flex; justify-content:space-between;">
            <span style="color:var(--slate-600);">Total Issued Pieces:</span>
            <strong id="summary-pcs" style="color:var(--slate-900);">{{ number_format($assign->issued_qty) }} pcs</strong>
          </div>
          <div style="border-top:2px dashed #cbd5e1; padding-top:10px; display:flex; justify-content:space-between; font-size:1.1rem;">
            <span style="font-weight:800; color:var(--slate-900);">Contract Total:</span>
            <span style="font-weight:800; color:#059669;" id="summary-total">₹{{ number_format($assign->total_amount, 2) }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Step 3: Instructions -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:24px;">
      <h3 style="font-size:0.95rem; font-weight:800; text-transform:uppercase; letter-spacing:0.05em; margin-top:0; margin-bottom:12px; color:var(--slate-800);">
        3. Job Work Instructions & Quality Standards
      </h3>
      <div class="form-group" style="margin-bottom:0;">
        <textarea name="instructions" class="form-control" rows="3" placeholder="Specify seam stitch density (SPI), thread color matching, shrinkage allowance, packaging and delivery requirements...">{{ $assign->instructions }}</textarea>
      </div>
    </div>

  </div>
</form>

@push('scripts')
<script>
  function onWorkerSelect(select) {
    const opt = select.options[select.selectedIndex];
    const rate = opt.getAttribute('data-rate');
    const process = opt.getAttribute('data-process');

    if (rate) document.getElementById('ja_rate').value = parseFloat(rate).toFixed(2);
    if (process) {
      const procSelect = document.getElementById('ja_process');
      for (let i = 0; i < procSelect.options.length; i++) {
        if (procSelect.options[i].value.toLowerCase().includes(process.toLowerCase())) {
          procSelect.selectedIndex = i;
          break;
        }
      }
    }
    calculateJWTotal();
  }

  function calculateJWTotal() {
    const qty = parseFloat(document.getElementById('ja_qty')?.value) || 0;
    const rate = parseFloat(document.getElementById('ja_rate')?.value) || 0;
    const total = qty * rate;

    document.getElementById('summary-pcs').innerText = Number(qty).toLocaleString('en-IN') + ' pcs';
    document.getElementById('summary-total').innerText = '₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  document.addEventListener('DOMContentLoaded', () => {
    calculateJWTotal();
  });
</script>
@endpush
@endsection
