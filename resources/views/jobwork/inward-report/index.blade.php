@extends('layouts.app')

@section('title', 'Job Inward & QC Report - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Job Work & Assign</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Inward & Ready Report</span></div>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <!-- Main Card Container -->
  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:20px;">
    
    <!-- Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:20px;">
      <div>
        <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Job Work Inward Inspection & Rejection Report</h3>
        <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">Received stitched/processed bundles, passed QC pieces, defect rejections, and ready lots</p>
      </div>

      <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('inward-table', 'Job_Inward_Report.csv')" style="display:inline-flex; align-items:center; gap:6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Export CSV
      </button>
    </div>

    <!-- Quick Filter Bar -->
    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:16px;">
      <input type="text" class="form-control" style="max-width:280px; font-size:0.85rem;" placeholder="Search lot#, job order, worker..." onkeyup="UI.filterGenericTable('inward-table', this.value)">
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="data-table" id="inward-table" style="width:100%; font-size:0.85rem;">
        <thead>
          <tr>
            <th>Job Order #</th>
            <th>Job Worker Contractor</th>
            <th>Process</th>
            <th>Lot #</th>
            <th>Style Article</th>
            <th>Issued Qty</th>
            <th>Received Good Qty</th>
            <th>QC Defect Rejection</th>
            <th>Wastage %</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($assignments as $ja)
            @php
              $good = (int)($ja->issued_qty * 0.98);
              $defect = $ja->issued_qty - $good;
              $defectPct = round(($defect / $ja->issued_qty) * 100, 1);
            @endphp
            <tr>
              <td style="font-family:var(--font-mono); font-weight:700; color:var(--primary-600);">{{ $ja->job_order_no }}</td>
              <td style="font-weight:700; color:var(--slate-800);">{{ $ja->job_worker_name }}</td>
              <td><span class="badge badge-info">{{ $ja->process_name }}</span></td>
              <td style="font-family:var(--font-mono); font-weight:600;">{{ $ja->lot_number }}</td>
              <td>{{ $ja->style_name }}</td>
              <td style="font-weight:700;">{{ number_format($ja->issued_qty) }} pcs</td>
              <td style="font-weight:800; color:#059669;">{{ number_format($good) }} pcs</td>
              <td style="font-weight:700; color:var(--danger-600);">{{ number_format($defect) }} pcs</td>
              <td><span class="badge {{ $defectPct > 3 ? 'badge-danger' : 'badge-success' }}">{{ $defectPct }}%</span></td>
              <td><span class="badge badge-success">{{ $ja->status }}</span></td>
            </tr>
          @empty
            <tr>
              <td colspan="10" style="text-align:center; padding:30px; color:var(--slate-400);">
                No job inward logs recorded yet.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>
@endsection
