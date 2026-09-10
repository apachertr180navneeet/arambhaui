@extends('layouts.app')

@section('title', 'Ready for Dispatch - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Shipping</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Ready for Dispatch</span></div>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <!-- Main Card Container -->
  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:20px;">
    
    <!-- Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:20px;">
      <div>
        <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Finished Goods Ready for Dispatch</h3>
        <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">Packed garment lots, carton barcode labels, and awaiting delivery challan generation</p>
      </div>

      <a href="{{ route('dispatch.dispatch') }}" class="btn btn-primary btn-sm">View Dispatch Challans</a>
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="data-table" style="width:100%; font-size:0.85rem;">
        <thead>
          <tr>
            <th>Lot #</th>
            <th>Style Article Description</th>
            <th>Contractor / Unit</th>
            <th>Finished Quantity</th>
            <th>Inspection Status</th>
            <th style="text-align:right;">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($readyAssignments as $ja)
            <tr>
              <td style="font-family:var(--font-mono); font-weight:700; color:var(--primary-600);">{{ $ja->lot_number }}</td>
              <td style="font-weight:700; color:var(--slate-800);">{{ $ja->style_name }}</td>
              <td>{{ $ja->job_worker_name }}</td>
              <td style="font-weight:800; color:#059669;">{{ number_format($ja->issued_qty) }} pcs</td>
              <td><span class="badge badge-success">QC Passed & Packed</span></td>
              <td style="text-align:right;">
                <a href="{{ route('dispatch.dispatch') }}" class="btn btn-primary btn-xs">Generate Challan</a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align:center; padding:30px; color:var(--slate-400);">
                No finished goods currently in ready dispatch stage.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>
@endsection
