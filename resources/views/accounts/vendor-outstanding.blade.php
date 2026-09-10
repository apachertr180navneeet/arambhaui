@extends('layouts.app')

@section('title', 'Vendor Outstanding & Payables - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Accounts & Settlements</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Vendor Outstanding</span></div>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <!-- Main Card Container -->
  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:20px;">
    
    <!-- Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:20px;">
      <div>
        <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Vendor Accounts & Raw Material Payables</h3>
        <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">Fabric mills, yarn suppliers, trim vendors payment credit tracking</p>
      </div>

      <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('vendor-due-table', 'Vendor_Payables.csv')">
        Export CSV
      </button>
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="data-table" id="vendor-due-table" style="width:100%; font-size:0.85rem;">
        <thead>
          <tr>
            <th>Vendor Code</th>
            <th>Vendor Name</th>
            <th>Category</th>
            <th>Contact Phone</th>
            <th>Credit Days Allowed</th>
            <th>Payment Status</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($vendors as $v)
            <tr>
              <td style="font-family:var(--font-mono); font-weight:700; color:var(--primary-600);">{{ $v->code }}</td>
              <td style="font-weight:700; color:var(--slate-800);">{{ $v->name }}</td>
              <td><span class="badge badge-info">{{ $v->category ?: 'General' }}</span></td>
              <td>{{ $v->phone }}</td>
              <td>{{ $v->credit_days ? $v->credit_days . ' Days' : 'Immediate' }}</td>
              <td><span class="badge badge-success">Current / Reconciled</span></td>
              <td style="text-align:right;">
                <a href="{{ route('masters.vendors.index') }}" class="btn btn-secondary btn-xs">View Ledger</a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align:center; padding:30px; color:var(--slate-400);">No vendor records found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>
@endsection
