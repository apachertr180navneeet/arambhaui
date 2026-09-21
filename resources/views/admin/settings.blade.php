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
          <input type="text" name="company_name" class="form-control" required value="{{ $settings['company_name'] ?? config('app.name', 'GarmentERP') }}">
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
          <input type="email" name="company_email" class="form-control" value="{{ $settings['company_email'] ?? 'accounts@garmenterp.com' }}">
        </div>

        <div class="form-group">
          <label class="form-label">Financial Year</label>
          <input type="text" name="financial_year" class="form-control" value="{{ $settings['financial_year'] ?? date('Y') . '-' . (date('Y')+1) }}" placeholder="e.g. 2026-2027">
        </div>

        <div class="form-group">
          <label class="form-label">Currency Symbol</label>
          <input type="text" name="currency_symbol" class="form-control" value="{{ $settings['currency_symbol'] ?? '₹' }}">
        </div>

        <div class="form-group">
          <label class="form-label">Purchase Order Prefix</label>
          <input type="text" name="po_prefix" class="form-control" value="{{ $settings['po_prefix'] ?? 'PO-2026-' }}">
        </div>

        <!-- UPI & QR Payment Recipient Settings Section -->
        <div style="grid-column:1/-1; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-top:8px;">
          <h4 style="margin:0 0 12px 0; font-size:0.95rem; font-weight:800; color:var(--slate-800); display:flex; align-items:center; gap:8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/></svg>
            UPI & QR Payment Recipient Configuration
          </h4>
          <p style="margin:0 0 14px 0; font-size:0.775rem; color:var(--slate-500);">
            These details are automatically presented to customers on the <strong>/qr/scanner</strong> payment portal to receive digital payments directly.
          </p>
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700;">Default Recipient UPI ID</label>
              <input type="text" name="recipient_upi_id" class="form-control" placeholder="e.g. shreeshyam@okaxis / payments@upi" value="{{ $settings['recipient_upi_id'] ?? '' }}" style="font-family:monospace; font-weight:700;">
              <small style="font-size:0.72rem; color:var(--slate-500);">The merchant UPI ID where customer payments will be directed.</small>
            </div>
            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700;">Recipient / Payee Display Name</label>
              <input type="text" name="recipient_name" class="form-control" placeholder="e.g. Shree Shyam Welfare / Garments Store" value="{{ $settings['recipient_name'] ?? ($settings['company_name'] ?? '') }}">
              <small style="font-size:0.72rem; color:var(--slate-500);">The legal or business name shown in UPI apps during payment.</small>
            </div>
          </div>
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
