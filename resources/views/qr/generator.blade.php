@extends('layouts.app')

@section('title', 'Generate Single-Use Discount QR - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Barcodes & QR</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Generate Single-Use QR</span></div>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <!-- Action Header -->
  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
    <div>
      <h2 style="margin:0; font-size:1.4rem; font-weight:800; color:var(--slate-900);">Discount QR Voucher Generator</h2>
      <p style="margin:2px 0 0; font-size:0.85rem; color:var(--slate-500);">Issues high-security single-use discount QR vouchers that lock to phone numbers and expire upon scan.</p>
    </div>

    <div style="display:flex; gap:10px;">
      <a href="{{ route('qr.history') }}" class="btn btn-secondary">View Vouchers Library</a>
      <a href="{{ route('qr.scanner') }}" class="btn btn-primary">Open Scanner Portal</a>
    </div>
  </div>

  <div style="display:grid; grid-template-columns:1fr 380px; gap:20px; align-items:start;">
    
    <!-- Generator Form Card -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:22px;">
      <h3 style="font-size:1.05rem; font-weight:700; margin-top:0; margin-bottom:16px; color:var(--slate-800);">Voucher Parameters & Policy</h3>

      <form action="{{ route('qr.generator.store') }}" method="POST" id="qr-gen-form">
        @csrf

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
          <div class="form-group" style="grid-column:1/-1;">
            <label class="form-label">Promotion Campaign Title <span style="color:red;">*</span></label>
            <input type="text" name="title" id="gen_title" class="form-control" required value="Festive Garment Discount Voucher" oninput="updateLivePreview()">
          </div>

          <div class="form-group">
            <label class="form-label">Voucher Code (Auto-Generated if blank)</label>
            <div style="display:flex; gap:8px;">
              <input type="text" name="voucher_code" id="gen_code" class="form-control" placeholder="e.g. FESTIVE-15-X7K" style="font-family:monospace; text-transform:uppercase;" oninput="updateLivePreview()">
              <button type="button" class="btn btn-secondary btn-sm" onclick="generateRandomCode()">Random</button>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Customer Name / Segment</label>
            <input type="text" name="customer_name" id="gen_cust" class="form-control" placeholder="General Promotion or Client Name" value="Retail Customer Club" oninput="updateLivePreview()">
          </div>

          <div class="form-group">
            <label class="form-label">Target Customer Phone (Optional Lock)</label>
            <input type="text" name="customer_phone" id="gen_phone" class="form-control" placeholder="e.g. +91 98765 43210">
          </div>

          <div class="form-group">
            <label class="form-label">Discount Type <span style="color:red;">*</span></label>
            <select name="discount_type" id="gen_type" class="form-control" required onchange="onTypeChange(this.value)">
              <option value="Percentage">Percentage (% OFF)</option>
              <option value="Flat">Flat Cash Off (₹)</option>
            </select>
          </div>

          <div class="form-group" id="group_percent">
            <label class="form-label">Discount Percentage (%) <span style="color:red;">*</span></label>
            <input type="number" step="0.1" min="1" max="100" name="discount_percent" id="gen_percent" class="form-control" value="15" oninput="updateLivePreview()">
          </div>

          <div class="form-group" id="group_amount" style="display:none;">
            <label class="form-label">Flat Discount Amount (₹) <span style="color:red;">*</span></label>
            <input type="number" step="1" min="1" name="discount_amount" id="gen_amount" class="form-control" value="500" oninput="updateLivePreview()">
          </div>

          <div class="form-group">
            <label class="form-label">Max Discount Cap (₹)</label>
            <input type="number" step="1" name="max_discount_cap" id="gen_cap" class="form-control" value="2500" oninput="updateLivePreview()">
          </div>

          <div class="form-group">
            <label class="form-label">Minimum Order Bill Value (₹)</label>
            <input type="number" step="1" name="min_order_value" id="gen_min_bill" class="form-control" value="1500">
          </div>

          <div class="form-group">
            <label class="form-label">Valid From</label>
            <input type="date" name="valid_from" class="form-control" value="{{ date('Y-m-d') }}">
          </div>

          <div class="form-group">
            <label class="form-label">Valid Until (Expiry)</label>
            <input type="date" name="valid_until" class="form-control" value="{{ date('Y-m-d', strtotime('+30 days')) }}">
          </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px; border-top:1px solid var(--slate-200); padding-top:16px;">
          <button type="submit" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:6px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            Generate & Register Voucher
          </button>
        </div>
      </form>
    </div>

    <!-- Live QR Card Preview -->
    <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:22px; text-align:center;">
      <h3 style="font-size:0.95rem; font-weight:700; margin-top:0; margin-bottom:14px; color:var(--slate-800); text-transform:uppercase; letter-spacing:0.5px;">Live Voucher Preview</h3>

      <!-- Visual Voucher Pass -->
      <div id="voucher-preview-card" style="background:linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color:#fff; border-radius:16px; padding:20px; box-shadow:0 10px 20px -5px rgba(79, 70, 229, 0.3); text-align:center;">
        <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:1px; opacity:0.85;">FashionWorks Pvt. Ltd.</div>
        <div style="font-size:1.15rem; font-weight:800; margin-top:4px;" id="prev-title">Festive Garment Discount</div>

        <!-- Big Discount Display -->
        <div style="font-size:2.4rem; font-weight:900; margin:14px 0 4px; letter-spacing:-0.5px;" id="prev-disc">15% OFF</div>
        <div style="font-size:0.75rem; opacity:0.85;" id="prev-cap">Up to ₹2,500 on min. ₹1,500 bill</div>

        <!-- Generated QR Code Canvas Container -->
        <div style="background:#fff; border-radius:12px; padding:12px; margin:16px auto 10px; width:170px; height:170px; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 6px rgba(0,0,0,0.1);">
          <div id="qrcode-canvas-container" style="display:flex; justify-content:center;"></div>
        </div>

        <div style="font-family:monospace; font-weight:700; font-size:1rem; letter-spacing:1px; background:rgba(0,0,0,0.2); padding:6px 12px; border-radius:8px; display:inline-block;" id="prev-code">
          FASHION-15-AUTO
        </div>

        <div style="font-size:0.7rem; opacity:0.75; margin-top:8px;">
          🔒 Single-Use Security Locked • Auto-expires upon first scan
        </div>
      </div>

      <div style="margin-top:16px;">
        <button type="button" class="btn btn-secondary btn-sm" onclick="printVoucherCard()" style="width:100%;">
          🖨️ Print Voucher Pass
        </button>
      </div>
    </div>

  </div>

</div>

@push('scripts')
<script>
  function generateRandomCode() {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    let code = 'FASHION-';
    for (let i = 0; i < 6; i++) {
      code += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('gen_code').value = code;
    updateLivePreview();
  }

  function onTypeChange(type) {
    if (type === 'Percentage') {
      document.getElementById('group_percent').style.display = 'block';
      document.getElementById('group_amount').style.display = 'none';
    } else {
      document.getElementById('group_percent').style.display = 'none';
      document.getElementById('group_amount').style.display = 'block';
    }
    updateLivePreview();
  }

  function updateLivePreview() {
    const title = document.getElementById('gen_title').value || 'Special Discount Voucher';
    const type = document.getElementById('gen_type').value;
    const percent = document.getElementById('gen_percent').value || '10';
    const amount = document.getElementById('gen_amount').value || '500';
    const cap = document.getElementById('gen_cap').value || '2500';
    const code = document.getElementById('gen_code').value.toUpperCase().trim() || (type === 'Percentage' ? `FASHION-${percent}-PROMO` : `SAVE-${amount}-PROMO`);

    document.getElementById('prev-title').innerText = title;
    document.getElementById('prev-code').innerText = code;

    if (type === 'Percentage') {
      document.getElementById('prev-disc').innerText = percent + '% OFF';
      document.getElementById('prev-cap').innerText = `Up to ₹${cap} max discount`;
    } else {
      document.getElementById('prev-disc').innerText = '₹' + amount + ' OFF';
      document.getElementById('prev-cap').innerText = `Flat discount on purchase`;
    }

    renderQrCode(code);
  }

  function renderQrCode(code) {
    const container = document.getElementById('qrcode-canvas-container');
    if (!container) return;

    // Use QR library if available, otherwise draw SVG fallback
    if (typeof QRCode !== 'undefined') {
      container.innerHTML = '';
      new QRCode(container, {
        text: code,
        width: 140,
        height: 140,
        colorDark: "#1e1b4b",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
      });
    } else {
      container.innerHTML = `
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=${encodeURIComponent(code)}" alt="QR Code" style="width:140px; height:140px; border-radius:6px;">
      `;
    }
  }

  function printVoucherCard() {
    UI.printSection('voucher-preview-card');
  }

  document.addEventListener('DOMContentLoaded', () => {
    updateLivePreview();
  });
</script>
@endpush
@endsection
