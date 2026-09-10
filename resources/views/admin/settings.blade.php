@extends('layouts.app')

@section('title', 'Company Settings - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Administration</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Company Settings</span></div>
@endsection

@section('content')
<div style="max-width:850px; display:flex; flex-direction:column; gap:20px;">

  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:24px;">
    
    <div style="margin-bottom:20px; border-bottom:1px solid var(--slate-200); padding-bottom:12px;">
      <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900);">GarmentERP Enterprise Company Profile</h3>
      <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">Configure organization profile, GSTIN tax registration, invoice numbering prefixes, and currency</p>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}">
      @csrf

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
        <div class="form-group" style="grid-column:1/-1;">
          <label class="form-label">Company / Legal Entity Name <span style="color:red;">*</span></label>
          <input type="text" name="company_name" class="form-control" required value="{{ $settings['company_name'] ?? 'FashionWorks Pvt. Ltd.' }}">
        </div>

        <div class="form-group">
          <label class="form-label">GSTIN / Tax Identification <span style="color:red;">*</span></label>
          <input type="text" name="gstin" class="form-control" required value="{{ $settings['gstin'] ?? '27AABCF1234F1Z1' }}">
        </div>

        <div class="form-group">
          <label class="form-label">PAN Number</label>
          <input type="text" name="pan_number" class="form-control" value="{{ $settings['pan_number'] ?? 'AABCF1234F' }}">
        </div>

        <div class="form-group">
          <label class="form-label">Official Phone</label>
          <input type="text" name="company_phone" class="form-control" value="{{ $settings['company_phone'] ?? '+91 421 2478900' }}">
        </div>

        <div class="form-group">
          <label class="form-label">Official Billing Email</label>
          <input type="email" name="company_email" class="form-control" value="{{ $settings['company_email'] ?? 'accounts@fashionworks.com' }}">
        </div>

        <div class="form-group">
          <label class="form-label">Currency Symbol</label>
          <input type="text" name="currency_symbol" class="form-control" value="{{ $settings['currency_symbol'] ?? '₹' }}">
        </div>

        <div class="form-group">
          <label class="form-label">Purchase Order Prefix</label>
          <input type="text" name="po_prefix" class="form-control" value="{{ $settings['po_prefix'] ?? 'PO-2026-' }}">
        </div>

        <div class="form-group" style="grid-column:1/-1;">
          <label class="form-label">Factory & Registered Address</label>
          <textarea name="company_address" class="form-control" rows="2">{{ $settings['company_address'] ?? 'Plot No. 45-B, Apparel Park MIDC, Tiruppur / Mumbai, Maharashtra 400093' }}</textarea>
        </div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:24px; border-top:1px solid var(--slate-200); padding-top:16px;">
        <button type="submit" class="btn btn-primary">Save Company Settings</button>
      </div>
    </form>

  </div>

</div>
@endsection
