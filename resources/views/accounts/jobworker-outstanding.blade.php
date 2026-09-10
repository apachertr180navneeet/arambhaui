@extends('layouts.app')

@section('title', 'Job Worker Outstanding - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Accounts & Settlements</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Job Worker Outstanding</span></div>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <!-- Main Card Container -->
  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:20px;">
    
    <!-- Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:20px;">
      <div>
        <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Job Worker Labor Dues & Settlement</h3>
        <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">Piece-rate stitching contractors, cutting masters & finishing labor settlements</p>
      </div>

      <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('jw-due-table', 'JobWorker_Payables.csv')">
        Export CSV
      </button>
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="data-table" id="jw-due-table" style="width:100%; font-size:0.85rem;">
        <thead>
          <tr>
            <th>Worker Code</th>
            <th>Job Worker Name</th>
            <th>Process Skill</th>
            <th>Rate / Pc (₹)</th>
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
              <td>{{ $jw->phone }}</td>
              <td><span class="badge badge-success">{{ $jw->status }}</span></td>
              <td style="text-align:right;">
                <a href="{{ route('jobwork.assign.index') }}" class="btn btn-secondary btn-xs">View Orders</a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align:center; padding:30px; color:var(--slate-400);">No job worker accounts found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>
@endsection
