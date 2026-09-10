@extends('layouts.app')

@section('title', 'Generate Single-Use Discount QR - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Barcodes & QR</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Generate Single-Use QR</span></div>
@endsection

@push('styles')
<style>
  /* ==========================================================================
     QR GENERATOR STUDIO - MODERN ENTERPRISE STYLES
     ========================================================================== */
  .qr-studio-wrapper {
    display: flex;
    flex-direction: column;
    gap: 24px;
  }

  /* Header banner */
  .qr-page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    color: #ffffff;
    border-radius: var(--radius-xl, 16px);
    padding: 24px 28px;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15);
    position: relative;
    overflow: hidden;
  }

  .qr-page-header::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 320px;
    height: 320px;
    background: radial-gradient(circle, rgba(99, 102, 241, 0.18) 0%, rgba(99, 102, 241, 0) 70%);
    pointer-events: none;
  }

  .qr-badge-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(99, 102, 241, 0.2);
    border: 1px solid rgba(129, 140, 248, 0.35);
    color: #a5b4fc;
    font-size: 0.725rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    padding: 4px 10px;
    border-radius: 9999px;
    margin-bottom: 8px;
  }

  /* Main 2-Column Grid */
  .qr-generator-grid {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 24px;
    align-items: start;
  }

  @media (max-width: 1040px) {
    .qr-generator-grid {
      grid-template-columns: 1fr;
    }
  }

  /* Form Container */
  .studio-form-card {
    background: #ffffff;
    border: 1px solid var(--slate-200, #e2e8f0);
    border-radius: var(--radius-xl, 16px);
    box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
    padding: 28px;
  }

  .form-section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.95rem;
    font-weight: 800;
    color: var(--slate-900, #0f172a);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 16px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--slate-100, #f1f5f9);
  }

  .form-section-title .section-step {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: var(--primary-50, #eef2ff);
    color: var(--primary-600, #4f46e5);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 800;
  }

  /* Segmented Toggle */
  .discount-toggle-group {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    background: var(--slate-100, #f1f5f9);
    padding: 4px;
    border-radius: var(--radius-lg, 12px);
  }

  .discount-toggle-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 14px;
    border: none;
    background: transparent;
    border-radius: var(--radius-md, 8px);
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--slate-600, #475569);
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .discount-toggle-btn.active {
    background: #ffffff;
    color: var(--primary-700, #4338ca);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  }

  /* Preset Pills */
  .preset-pills {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    margin-top: 8px;
  }

  .preset-pill-btn {
    background: var(--slate-100, #f1f5f9);
    border: 1px solid var(--slate-200, #e2e8f0);
    color: var(--slate-700, #334155);
    font-size: 0.75rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.15s ease;
  }

  .preset-pill-btn:hover {
    background: var(--primary-50, #eef2ff);
    border-color: var(--primary-300, #a5b4fc);
    color: var(--primary-700, #4338ca);
  }

  /* Live Preview Sidebar */
  .preview-sticky-card {
    background: #ffffff;
    border: 1px solid var(--slate-200, #e2e8f0);
    border-radius: var(--radius-xl, 16px);
    box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
    padding: 24px;
    position: sticky;
    top: 24px;
  }

  /* Luxury Voucher Ticket Card */
  .luxury-voucher-pass {
    position: relative;
    border-radius: 20px;
    padding: 24px 20px;
    color: #ffffff;
    overflow: hidden;
    box-shadow: 0 15px 35px -5px rgba(30, 27, 75, 0.35);
    transition: all 0.3s ease;
  }

  /* Gradient Color Themes */
  .theme-indigo { background: linear-gradient(135deg, #312e81 0%, #4338ca 50%, #6366f1 100%); }
  .theme-emerald { background: linear-gradient(135deg, #064e3b 0%, #047857 50%, #10b981 100%); }
  .theme-crimson { background: linear-gradient(135deg, #881337 0%, #be123c 50%, #f43f5e 100%); }
  .theme-onyx { background: linear-gradient(135deg, #090d16 0%, #1e293b 50%, #334155 100%); border: 1px solid rgba(255, 215, 0, 0.3); }
  .theme-amber { background: linear-gradient(135deg, #78350f 0%, #b45309 50%, #f59e0b 100%); }

  /* Perforation Edge Notches */
  .perforation-line {
    position: relative;
    margin: 18px -20px;
    border-top: 2px dashed rgba(255, 255, 255, 0.35);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .perforation-line::before,
  .perforation-line::after {
    content: '';
    position: absolute;
    width: 22px;
    height: 22px;
    background: #ffffff;
    border-radius: 50%;
    top: -11px;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15);
  }

  .perforation-line::before { left: -11px; }
  .perforation-line::after { right: -11px; }

  /* QR Box Container */
  .ticket-qr-container {
    background: #ffffff;
    border-radius: 14px;
    padding: 12px;
    width: 154px;
    height: 154px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
  }

  /* Color Theme Switcher dots */
  .color-dots-bar {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-bottom: 16px;
  }

  .color-dot {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid transparent;
    transition: transform 0.2s, border-color 0.2s;
  }

  .color-dot:hover {
    transform: scale(1.15);
  }

  .color-dot.active {
    border-color: var(--slate-900, #0f172a);
    box-shadow: 0 0 0 2px #ffffff;
    transform: scale(1.15);
  }

  /* Quick Actions inside Live Preview */
  .preview-actions-bar {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-top: 18px;
  }

  /* Micro copy popover */
  .copy-badge-btn {
    cursor: pointer;
    transition: background-color 0.2s;
  }

  .copy-badge-btn:hover {
    background: rgba(255, 255, 255, 0.25) !important;
  }

  @media print {
    body * {
      visibility: hidden;
    }
    #voucher-print-area, #voucher-print-area * {
      visibility: visible;
    }
    #voucher-print-area {
      position: absolute;
      left: 50%;
      top: 50px;
      transform: translateX(-50%);
      width: 380px;
    }
  }
</style>
@endpush

@section('content')
<div class="qr-studio-wrapper">

  <!-- Action Header Banner -->
  <div class="qr-page-header">
    <div>
      <div class="qr-badge-pill">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        Single-Use Security Engine
      </div>
      <h2 style="margin:0; font-size:1.5rem; font-weight:800; letter-spacing:-0.02em;">Discount QR Voucher Generator</h2>
      <p style="margin:4px 0 0; font-size:0.85rem; color:#cbd5e1; max-width:640px;">
        Issue cryptographically unique single-use vouchers with real-time dynamic card rendering, optional mobile phone lock, and automated expiry policies.
      </p>
    </div>

    <div style="display:flex; gap:10px; align-items:center;">
      <a href="{{ route('qr.history') }}" class="btn btn-secondary" style="background:rgba(255,255,255,0.12); color:#fff; border-color:rgba(255,255,255,0.25); display:inline-flex; align-items:center; gap:8px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        Admin Expiry Ledger
      </a>
    </div>
  </div>

  <!-- Studio Main Work Area (Form + Live Card Preview) -->
  <div class="qr-generator-grid">
    
    <!-- LEFT: Interactive Generator Configurator -->
    <div class="studio-form-card">
      <form action="{{ route('qr.generator.store') }}" method="POST" id="qr-gen-form">
        @csrf

        <!-- SECTION 1: Campaign & Customer Target -->
        <div class="form-section-title">
          <span class="section-step">1</span>
          <span>Campaign & Customer Audience</span>
        </div>

        <div style="display:flex; flex-direction:column; gap:16px; margin-bottom:24px;">
          <!-- Campaign Title -->
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800);">
              Promotion Campaign Title <span style="color:#ef4444;">*</span>
            </label>
            <input type="text" name="title" id="gen_title" class="form-control" required value="Festive Garment Discount Voucher" placeholder="e.g. VIP Summer Gala Privilege" oninput="updateLivePreview()">
            
            <!-- Quick Title Suggestions -->
            <div class="preset-pills">
              <span style="font-size:0.75rem; color:var(--slate-500); align-self:center; margin-right:4px;">Quick:</span>
              <button type="button" class="preset-pill-btn" onclick="setCampaignTitle('Festive Garment Discount Voucher')">Festive Offer</button>
              <button type="button" class="preset-pill-btn" onclick="setCampaignTitle('VIP Customer Exclusive Privilege')">VIP Exclusive</button>
              <button type="button" class="preset-pill-btn" onclick="setCampaignTitle('First Purchase Welcome Discount')">Welcome Offer</button>
              <button type="button" class="preset-pill-btn" onclick="setCampaignTitle('End of Season Clearance Bonanza')">Clearance Sale</button>
            </div>
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <!-- Customer Select / Custom -->
            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800);">Select Registered Customer</label>
              <select id="registered_customer_select" class="form-control" onchange="onCustomerSelect(this)">
                <option value="">-- General / Custom Audience --</option>
                @foreach ($customers as $c)
                  <option value="{{ $c->name }}" data-phone="{{ $c->mobile ?? $c->phone ?? '' }}">{{ $c->name }} ({{ $c->mobile ?? 'No Mobile' }})</option>
                @endforeach
              </select>
            </div>

            <!-- Customer Display Name -->
            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800);">Audience / Customer Name</label>
              <input type="text" name="customer_name" id="gen_cust" class="form-control" placeholder="Retail Customer Club" value="Retail Customer Club" oninput="updateLivePreview()">
            </div>
          </div>

          <!-- Customer Phone (Security Lock) -->
          <div class="form-group" style="margin-bottom:0;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800); margin:0;">
                Target Customer Mobile Phone <span style="font-size:0.75rem; font-weight:500; color:var(--slate-500);">(Optional Security Lock)</span>
              </label>
              <span style="font-size:0.75rem; color:var(--primary-600); font-weight:600;">🔒 Locks voucher to phone</span>
            </div>
            <div style="display:flex; align-items:center; position:relative;">
              <span style="position:absolute; left:12px; font-weight:700; color:var(--slate-400); font-size:0.9rem;">+91</span>
              <input type="text" name="customer_phone" id="gen_phone" class="form-control" style="padding-left:46px; font-family:var(--font-mono, monospace);" placeholder="9876543210" oninput="updateLivePreview()">
            </div>
          </div>
        </div>

        <!-- SECTION 2: Discount & Pricing Mechanics -->
        <div class="form-section-title">
          <span class="section-step">2</span>
          <span>Discount Mechanics & Value</span>
        </div>

        <div style="display:flex; flex-direction:column; gap:16px; margin-bottom:24px;">
          <!-- Discount Type Selector Tabs -->
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800);">Discount Calculation Type <span style="color:#ef4444;">*</span></label>
            <input type="hidden" name="discount_type" id="gen_type" value="Percentage">
            <div class="discount-toggle-group">
              <button type="button" id="btn_type_percent" class="discount-toggle-btn active" onclick="switchDiscountType('Percentage')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="5" x2="5" y2="19"/><circle cx="6.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/></svg>
                Percentage (% OFF)
              </button>
              <button type="button" id="btn_type_flat" class="discount-toggle-btn" onclick="switchDiscountType('Flat')">
                <span style="font-size:1.1rem; font-weight:800; line-height:1;">₹</span>
                Flat Cash Discount
              </button>
            </div>
          </div>

          <!-- Dynamic Discount Value Input -->
          <div id="group_percent">
            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800);">Discount Percentage (%) <span style="color:#ef4444;">*</span></label>
              <div style="position:relative;">
                <input type="number" step="0.5" min="1" max="100" name="discount_percent" id="gen_percent" class="form-control" value="15" style="font-weight:700; font-size:1.05rem;" oninput="updateLivePreview()">
                <span style="position:absolute; right:14px; top:50%; transform:translateY(-50%); font-weight:800; color:var(--slate-400);">%</span>
              </div>
              <div class="preset-pills">
                <button type="button" class="preset-pill-btn" onclick="setPercent(10)">10%</button>
                <button type="button" class="preset-pill-btn" onclick="setPercent(15)">15%</button>
                <button type="button" class="preset-pill-btn" onclick="setPercent(20)">20%</button>
                <button type="button" class="preset-pill-btn" onclick="setPercent(25)">25%</button>
                <button type="button" class="preset-pill-btn" onclick="setPercent(50)">50% (BOGO)</button>
              </div>
            </div>
          </div>

          <div id="group_amount" style="display:none;">
            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800);">Flat Discount Amount (₹) <span style="color:#ef4444;">*</span></label>
              <div style="position:relative;">
                <input type="number" step="10" min="1" name="discount_amount" id="gen_amount" class="form-control" value="500" style="font-weight:700; font-size:1.05rem;" oninput="updateLivePreview()">
                <span style="position:absolute; right:14px; top:50%; transform:translateY(-50%); font-weight:800; color:var(--slate-400);">₹</span>
              </div>
              <div class="preset-pills">
                <button type="button" class="preset-pill-btn" onclick="setAmount(200)">₹200</button>
                <button type="button" class="preset-pill-btn" onclick="setAmount(500)">₹500</button>
                <button type="button" class="preset-pill-btn" onclick="setAmount(1000)">₹1,000</button>
                <button type="button" class="preset-pill-btn" onclick="setAmount(2000)">₹2,000</button>
              </div>
            </div>
          </div>

          <!-- Cap and Min Bill Threshold -->
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800);">Max Discount Cap (₹)</label>
              <input type="number" step="100" name="max_discount_cap" id="gen_cap" class="form-control" value="2500" oninput="updateLivePreview()">
              <small style="color:var(--slate-500); font-size:0.725rem;">Maximum savings allowed</small>
            </div>

            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800);">Min. Bill Value (₹)</label>
              <input type="number" step="100" name="min_order_value" id="gen_min_bill" class="form-control" value="1500" oninput="updateLivePreview()">
              <small style="color:var(--slate-500); font-size:0.725rem;">Minimum cart total required</small>
            </div>
          </div>
        </div>

        <!-- SECTION 3: Voucher Code & Validity Schedule -->
        <div class="form-section-title">
          <span class="section-step">3</span>
          <span>Security Token & Expiry Schedule</span>
        </div>

        <div style="display:flex; flex-direction:column; gap:16px;">
          <!-- Custom Code Input + Generator Button -->
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800);">Voucher Code <span style="font-weight:400; color:var(--slate-500);">(Auto-assigned if left blank)</span></label>
            <div style="display:flex; gap:8px;">
              <input type="text" name="voucher_code" id="gen_code" class="form-control" placeholder="e.g. FESTIVE-15-X7K" style="font-family:var(--font-mono, monospace); font-weight:700; text-transform:uppercase; letter-spacing:0.05em;" oninput="updateLivePreview()">
              <button type="button" class="btn btn-secondary" onclick="generateRandomCode()" style="white-space:nowrap; display:inline-flex; align-items:center; gap:6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                🎲 Randomize
              </button>
            </div>
          </div>

          <!-- Date Ranges -->
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800);">Valid From</label>
              <input type="date" name="valid_from" id="gen_from" class="form-control" value="{{ date('Y-m-d') }}">
            </div>

            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800);">Valid Until (Expiry)</label>
              <input type="date" name="valid_until" id="gen_until" class="form-control" value="{{ date('Y-m-d', strtotime('+30 days')) }}" oninput="updateLivePreview()">
            </div>
          </div>

          <!-- Quick Expiry Buttons -->
          <div class="preset-pills">
            <span style="font-size:0.75rem; color:var(--slate-500); align-self:center; margin-right:4px;">Expiry:</span>
            <button type="button" class="preset-pill-btn" onclick="addDaysToExpiry(7)">+7 Days</button>
            <button type="button" class="preset-pill-btn" onclick="addDaysToExpiry(15)">+15 Days</button>
            <button type="button" class="preset-pill-btn" onclick="addDaysToExpiry(30)">+30 Days</button>
            <button type="button" class="preset-pill-btn" onclick="addDaysToExpiry(60)">+60 Days</button>
            <button type="button" class="preset-pill-btn" onclick="addDaysToExpiry(90)">+90 Days</button>
          </div>
        </div>

        <!-- Form Submit Bar -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:28px; padding-top:20px; border-top:1px solid var(--slate-200, #e2e8f0);">
          <button type="reset" class="btn btn-secondary" onclick="setTimeout(updateLivePreview, 50)">
            Reset Form
          </button>
          <button type="submit" class="btn btn-primary" style="padding:10px 24px; font-size:0.95rem; font-weight:800; display:inline-flex; align-items:center; gap:8px; box-shadow:0 4px 14px rgba(79, 70, 229, 0.35);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
            Generate & Register Voucher
          </button>
        </div>

      </form>
    </div>

    <!-- RIGHT: Live Luxury Card Pass Studio & Actions -->
    <div class="preview-sticky-card">
      
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <span style="font-size:0.8rem; font-weight:800; color:var(--slate-700); text-transform:uppercase; letter-spacing:0.06em;">
          Live Card Preview
        </span>
        <span style="font-size:0.75rem; background:#ecfdf5; color:#059669; font-weight:700; padding:2px 8px; border-radius:9999px; border:1px solid #a7f3d0;">
          Realtime Sync
        </span>
      </div>

      <!-- Color Theme Chooser -->
      <div class="color-dots-bar">
        <div class="color-dot theme-indigo active" title="Royal Indigo" onclick="setCardTheme('theme-indigo', this)"></div>
        <div class="color-dot theme-emerald" title="Emerald Velvet" onclick="setCardTheme('theme-emerald', this)"></div>
        <div class="color-dot theme-crimson" title="Crimson Ruby" onclick="setCardTheme('theme-crimson', this)"></div>
        <div class="color-dot theme-onyx" title="Midnight Onyx & Gold" onclick="setCardTheme('theme-onyx', this)"></div>
        <div class="color-dot theme-amber" title="Sunset Amber" onclick="setCardTheme('theme-amber', this)"></div>
      </div>

      <!-- VISUAL VOUCHER PASS (PRINTABLE SECTION) -->
      <div id="voucher-print-area">
        <div id="voucher-preview-card" class="luxury-voucher-pass theme-indigo">
          
          <!-- Card Header / Brand -->
          <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
              <div style="font-size:0.68rem; font-weight:800; text-transform:uppercase; letter-spacing:1.5px; opacity:0.85;">
                GARMENT ERP • OFFICIAL PASS
              </div>
              <div style="font-size:1.1rem; font-weight:800; line-height:1.25; margin-top:2px;" id="prev-title">
                Festive Garment Discount Voucher
              </div>
            </div>
            <div style="background:rgba(255,255,255,0.2); padding:4px 8px; border-radius:6px; font-size:0.65rem; font-weight:800; letter-spacing:0.5px; text-transform:uppercase; backdrop-filter:blur(4px);">
              PROMO
            </div>
          </div>

          <!-- Customer segment pill -->
          <div style="margin-top:8px;">
            <span style="font-size:0.75rem; background:rgba(0,0,0,0.25); padding:3px 8px; border-radius:4px; font-weight:600; opacity:0.9;" id="prev-cust">
              Audience: Retail Customer Club
            </span>
          </div>

          <!-- Big Typographic Discount -->
          <div style="margin:16px 0 6px; text-align:center;">
            <div style="font-size:2.4rem; font-weight:900; letter-spacing:-0.03em; line-height:1;" id="prev-disc">
              15% OFF
            </div>
            <div style="font-size:0.75rem; opacity:0.9; margin-top:4px; font-weight:600;" id="prev-cap">
              Up to ₹2,500 on min. ₹1,500 bill
            </div>
          </div>

          <!-- Perforation divider line with cutout circles -->
          <div class="perforation-line"></div>

          <!-- QR Code Canvas Frame -->
          <div class="ticket-qr-container">
            <div id="qrcode-canvas-container" style="display:flex; justify-content:center; align-items:center;"></div>
          </div>

          <!-- Code Display Box with Copy Tool -->
          <div style="text-align:center; margin-top:12px;">
            <div class="copy-badge-btn" onclick="copyVoucherCode()" style="font-family:var(--font-mono, monospace); font-weight:800; font-size:1.05rem; letter-spacing:1px; background:rgba(0,0,0,0.3); padding:6px 14px; border-radius:8px; display:inline-flex; align-items:center; gap:6px;" title="Click to copy">
              <span id="prev-code">FASHION-15-AUTO</span>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
            </div>
          </div>

          <!-- Security footer inside card -->
          <div style="margin-top:12px; font-size:0.68rem; opacity:0.8; text-align:center; display:flex; justify-content:space-between; align-items:center;">
            <span id="prev-expiry">Valid until: 30 Days</span>
            <span>🔒 Single-Use Token</span>
          </div>

        </div>
      </div>

      <!-- Quick Action Buttons -->
      <div class="preview-actions-bar">
        <button type="button" class="btn btn-secondary btn-sm" onclick="printVoucherCard()" style="display:inline-flex; align-items:center; justify-content:center; gap:6px; font-weight:700;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
          Print Voucher
        </button>

        <button type="button" class="btn btn-secondary btn-sm" onclick="copyVoucherCode()" style="display:inline-flex; align-items:center; justify-content:center; gap:6px; font-weight:700;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
          Copy Code
        </button>
      </div>

    </div>

  </div>

  <!-- RECENTLY GENERATED VOUCHERS (BOTTOM SHELF) -->
  @if(isset($vouchers) && count($vouchers) > 0)
    <div class="card" style="background:#fff; border-radius:var(--radius-xl, 16px); border:1px solid var(--slate-200, #e2e8f0); box-shadow:var(--shadow-sm); padding:22px;">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <div>
          <h3 style="margin:0; font-size:1.1rem; font-weight:800; color:var(--slate-900);">Active Vouchers in Circulation</h3>
          <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">Instant glance at recently registered single-use promotional vouchers.</p>
        </div>
        <a href="{{ route('qr.history') }}" class="btn btn-secondary btn-sm" style="font-weight:700;">View Full Ledger →</a>
      </div>

      <div class="table-responsive">
        <table class="data-table" style="width:100%; font-size:0.85rem;">
          <thead>
            <tr>
              <th>Voucher Code</th>
              <th>Campaign</th>
              <th>Audience</th>
              <th>Discount Value</th>
              <th>Valid Until</th>
              <th>Status</th>
              <th style="text-align:right;">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($vouchers->take(5) as $v)
              <tr>
                <td style="font-family:var(--font-mono, monospace); font-weight:800; color:var(--primary-700);">
                  {{ $v->voucher_code }}
                </td>
                <td style="font-weight:600; color:var(--slate-800);">{{ $v->title }}</td>
                <td style="color:var(--slate-600);">{{ $v->customer_name ?: 'General' }}</td>
                <td style="font-weight:800; color:#059669;">
                  {{ $v->discount_type === 'Percentage' ? ($v->discount_percent . '% OFF') : ('₹' . number_format($v->discount_amount ?: $v->discount_percent) . ' Flat') }}
                </td>
                <td style="color:var(--slate-600);">{{ $v->valid_until ? date('d M Y', strtotime($v->valid_until)) : 'No Expiry' }}</td>
                <td>
                  <span class="badge" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; font-weight:700; padding:3px 8px; border-radius:6px;">
                    ● Active
                  </span>
                </td>
                <td style="text-align:right;">
                  <button type="button" class="btn btn-secondary btn-sm" style="padding:2px 8px; font-size:0.75rem;" onclick="navigator.clipboard.writeText('{{ $v->voucher_code }}'); UI.notify('Copied {{ $v->voucher_code }}', 'success');">
                    Copy
                  </button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @endif

</div>

<!-- QRCode Library CDN for crisp, instant client-side QR generation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

@push('scripts')
<script>
  let currentCardTheme = 'theme-indigo';

  function switchDiscountType(type) {
    document.getElementById('gen_type').value = type;
    const btnPercent = document.getElementById('btn_type_percent');
    const btnFlat = document.getElementById('btn_type_flat');
    const groupPercent = document.getElementById('group_percent');
    const groupAmount = document.getElementById('group_amount');

    if (type === 'Percentage') {
      btnPercent.classList.add('active');
      btnFlat.classList.remove('active');
      groupPercent.style.display = 'block';
      groupAmount.style.display = 'none';
    } else {
      btnFlat.classList.add('active');
      btnPercent.classList.remove('active');
      groupPercent.style.display = 'none';
      groupAmount.style.display = 'block';
    }
    updateLivePreview();
  }

  function setPercent(val) {
    document.getElementById('gen_percent').value = val;
    updateLivePreview();
  }

  function setAmount(val) {
    document.getElementById('gen_amount').value = val;
    updateLivePreview();
  }

  function setCampaignTitle(text) {
    document.getElementById('gen_title').value = text;
    updateLivePreview();
  }

  function onCustomerSelect(selectElem) {
    const selected = selectElem.options[selectElem.selectedIndex];
    if (selectElem.value) {
      document.getElementById('gen_cust').value = selectElem.value;
      const phone = selected.getAttribute('data-phone') || '';
      document.getElementById('gen_phone').value = phone.replace('+91', '').trim();
    }
    updateLivePreview();
  }

  function generateRandomCode() {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    const type = document.getElementById('gen_type').value;
    const percent = document.getElementById('gen_percent').value || '15';
    const amount = document.getElementById('gen_amount').value || '500';

    let prefix = (type === 'Percentage') ? `FASHION-${percent}` : `SAVE-${amount}`;
    let suffix = '';
    for (let i = 0; i < 4; i++) {
      suffix += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('gen_code').value = `${prefix}-${suffix}`;
    updateLivePreview();
  }

  function addDaysToExpiry(days) {
    const date = new Date();
    date.setDate(date.getDate() + days);
    const yyyy = date.getFullYear();
    const mm = String(date.getMonth() + 1).padStart(2, '0');
    const dd = String(date.getDate()).padStart(2, '0');
    document.getElementById('gen_until').value = `${yyyy}-${mm}-${dd}`;
    updateLivePreview();
  }

  function setCardTheme(themeClass, dotElem) {
    currentCardTheme = themeClass;
    const card = document.getElementById('voucher-preview-card');
    card.className = `luxury-voucher-pass ${themeClass}`;
    
    document.querySelectorAll('.color-dot').forEach(d => d.classList.remove('active'));
    if (dotElem) dotElem.classList.add('active');
  }

  function updateLivePreview() {
    const title = document.getElementById('gen_title').value || 'Special Discount Voucher';
    const cust = document.getElementById('gen_cust').value || 'Retail Customer';
    const type = document.getElementById('gen_type').value;
    const percent = document.getElementById('gen_percent').value || '15';
    const amount = document.getElementById('gen_amount').value || '500';
    const cap = document.getElementById('gen_cap').value || '2500';
    const minBill = document.getElementById('gen_min_bill').value || '1500';
    const expiry = document.getElementById('gen_until').value;

    let code = document.getElementById('gen_code').value.toUpperCase().trim();
    if (!code) {
      code = (type === 'Percentage') ? `FASHION-${percent}-AUTO` : `SAVE-${amount}-AUTO`;
    }

    document.getElementById('prev-title').innerText = title;
    document.getElementById('prev-cust').innerText = 'Audience: ' + cust;
    document.getElementById('prev-code').innerText = code;

    if (type === 'Percentage') {
      document.getElementById('prev-disc').innerText = percent + '% OFF';
      document.getElementById('prev-cap').innerText = `Up to ₹${Number(cap).toLocaleString('en-IN')} on min. ₹${Number(minBill).toLocaleString('en-IN')} bill`;
    } else {
      document.getElementById('prev-disc').innerText = '₹' + Number(amount).toLocaleString('en-IN') + ' FLAT OFF';
      document.getElementById('prev-cap').innerText = `Flat discount on min. ₹${Number(minBill).toLocaleString('en-IN')} bill`;
    }

    if (expiry) {
      const expDate = new Date(expiry);
      if (!isNaN(expDate)) {
        document.getElementById('prev-expiry').innerText = 'Valid until: ' + expDate.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
      }
    }

    renderQrCode(code);
  }

  function renderQrCode(code) {
    const container = document.getElementById('qrcode-canvas-container');
    if (!container) return;

    container.innerHTML = '';

    if (typeof QRCode !== 'undefined') {
      try {
        new QRCode(container, {
          text: code,
          width: 130,
          height: 130,
          colorDark: "#0f172a",
          colorLight: "#ffffff",
          correctLevel: QRCode.CorrectLevel.H
        });
      } catch (e) {
        fallbackQR(container, code);
      }
    } else {
      fallbackQR(container, code);
    }
  }

  function fallbackQR(container, code) {
    container.innerHTML = `<img src="https://api.qrserver.com/v1/create-qr-code/?size=130x130&data=${encodeURIComponent(code)}" alt="QR Code" style="width:130px; height:130px; border-radius:6px; display:block;">`;
  }

  function copyVoucherCode() {
    const code = document.getElementById('prev-code').innerText;
    navigator.clipboard.writeText(code).then(() => {
      if (window.Toast) {
        Toast.fire({
          icon: 'success',
          title: `Voucher Code ${code} copied to clipboard!`
        });
      } else if (window.Swal) {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: `Voucher Code ${code} copied to clipboard!`,
          showConfirmButton: false,
          timer: 2000
        });
      } else {
        alert(`Voucher Code ${code} copied!`);
      }
    });
  }

  function printVoucherCard() {
    window.print();
  }

  document.addEventListener('DOMContentLoaded', () => {
    updateLivePreview();
  });
</script>
@endpush
@endsection
