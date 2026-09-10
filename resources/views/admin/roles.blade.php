@extends('layouts.app')

@section('title', 'Roles & Permissions - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Administration</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Roles & Permissions</span></div>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:20px;">
    <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Enterprise Roles & Permission Matrix</h3>
    <p style="margin:2px 0 16px; font-size:0.8rem; color:var(--slate-500);">Module security boundaries, read/write authorizations, and audit compliance</p>

    <div class="table-responsive">
      <table class="data-table" style="width:100%; font-size:0.85rem;">
        <thead>
          <tr>
            <th>Module Feature</th>
            <th>Administrator</th>
            <th>Production Manager</th>
            <th>Purchase Officer</th>
            <th>Accountant</th>
            <th>Dispatch Clerk</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="font-weight:700;">Customer & Vendor Masters</td>
            <td><span class="badge badge-success">Full Access</span></td>
            <td><span class="badge badge-info">Read Only</span></td>
            <td><span class="badge badge-success">Full Access</span></td>
            <td><span class="badge badge-info">Read Only</span></td>
            <td><span class="badge badge-secondary">Hidden</span></td>
          </tr>
          <tr>
            <td style="font-weight:700;">Purchase Orders (PO)</td>
            <td><span class="badge badge-success">Full Access</span></td>
            <td><span class="badge badge-info">Read Only</span></td>
            <td><span class="badge badge-success">Create / Edit</span></td>
            <td><span class="badge badge-info">Read Only</span></td>
            <td><span class="badge badge-secondary">Hidden</span></td>
          </tr>
          <tr>
            <td style="font-weight:700;">Job Work Outward / Inward</td>
            <td><span class="badge badge-success">Full Access</span></td>
            <td><span class="badge badge-success">Issue & QC Inward</span></td>
            <td><span class="badge badge-secondary">Hidden</span></td>
            <td><span class="badge badge-info">Labor Dues</span></td>
            <td><span class="badge badge-info">Ready Lots</span></td>
          </tr>
          <tr>
            <td style="font-weight:700;">QR Voucher Generation & Expiry</td>
            <td><span class="badge badge-success">Full Control</span></td>
            <td><span class="badge badge-info">Lot Labels</span></td>
            <td><span class="badge badge-secondary">Hidden</span></td>
            <td><span class="badge badge-success">Redeem / Claim</span></td>
            <td><span class="badge badge-secondary">Hidden</span></td>
          </tr>
          <tr>
            <td style="font-weight:700;">Logistics & Dispatch Challans</td>
            <td><span class="badge badge-success">Full Access</span></td>
            <td><span class="badge badge-info">Read Only</span></td>
            <td><span class="badge badge-secondary">Hidden</span></td>
            <td><span class="badge badge-info">Billing</span></td>
            <td><span class="badge badge-success">Generate DC</span></td>
          </tr>
          <tr>
            <td style="font-weight:700;">Accounts & Settlements</td>
            <td><span class="badge badge-success">Full Access</span></td>
            <td><span class="badge badge-secondary">Hidden</span></td>
            <td><span class="badge badge-secondary">Hidden</span></td>
            <td><span class="badge badge-success">Receive Payment</span></td>
            <td><span class="badge badge-secondary">Hidden</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
