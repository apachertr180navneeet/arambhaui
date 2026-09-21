@extends('layouts.app')

@section('title', 'Generate QR Vouchers (Color Master A4 Setup) - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Barcodes & QR</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Generate QR Batch</span></div>
@endsection

@push('styles')
<style>
  /* ==========================================================================
     AARAMBH QR GENERATOR STUDIO - 4 ESSENTIAL FIELDS
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
    background: radial-gradient(circle, rgba(99, 102, 241, 0.2) 0%, rgba(99, 102, 241, 0) 70%);
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

  /* 2-Column Layout */
  .qr-generator-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 24px;
    align-items: start;
  }

  @media (max-width: 980px) {
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
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--slate-100, #f1f5f9);
  }

  .input-label-primary {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 700;
    font-size: 0.9rem;
    color: var(--slate-800, #1e293b);
    margin-bottom: 6px;
  }

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

  /* Live 1" x 1" Aarambh Preview Card */
  .preview-sticky-card {
    background: #ffffff;
    border: 1px solid var(--slate-200, #e2e8f0);
    border-radius: var(--radius-xl, 16px);
    box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
    padding: 24px;
    position: sticky;
    top: 24px;
    text-align: center;
  }

  /* Aarambh 1 inch by 1 inch Sticker Frame */
  .majasol-sticker-box {
    width: 1.25in;
    height: 1.25in;
    margin: 14px auto;
    background: #ffffff;
    border: 2px solid #0f172a;
    border-radius: 6px;
    padding: 4px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
    box-sizing: border-box;
    position: relative;
    background-color: #fff;
  }

  .majasol-sticker-box .sticker-brand {
    font-size: 8px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #000;
    line-height: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
  }

  .majasol-sticker-box .sticker-qr {
    width: 62px;
    height: 62px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .majasol-sticker-box .sticker-qr img,
  .majasol-sticker-box .sticker-qr canvas {
    width: 62px !important;
    height: 62px !important;
  }

  .majasol-sticker-box .sticker-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    padding: 0 2px;
    line-height: 1;
  }

  .majasol-sticker-box .sticker-amt {
    font-size: 9px;
    font-weight: 900;
    color: #000;
  }

  .majasol-sticker-box .sticker-code {
    font-family: var(--font-mono, monospace);
    font-size: 6.5px;
    font-weight: 800;
    color: #333;
    letter-spacing: 0.2px;
  }

  /* A4 Print CSS - Maximum QRs on Single A4 Sheet */
  @media print {
    @page {
      size: A4 portrait;
      margin: 5mm;
    }

    html, body {
      background: #ffffff !important;
      margin: 0 !important;
      padding: 0 !important;
    }

    body * {
      visibility: hidden !important;
    }

    #single-print-wrapper,
    #single-print-wrapper * {
      visibility: visible !important;
    }

    #single-print-wrapper {
      position: absolute !important;
      left: 10mm !important;
      top: 10mm !important;
      width: 25mm !important;
      height: 25mm !important;
      margin: 0 !important;
      padding: 1.5mm !important;
      box-sizing: border-box !important;
      display: flex !important;
      flex-direction: column !important;
      align-items: center !important;
      justify-content: space-between !important;
      text-align: center !important;
      background: #fff !important;
      border: 0.8px dashed #000 !important;
    }

    #single-print-wrapper .sticker-brand {
      font-size: 6pt !important;
      font-weight: 900 !important;
      color: #000 !important;
      line-height: 1 !important;
    }

    #single-print-wrapper .sticker-qr img,
    #single-print-wrapper .sticker-qr canvas {
      width: 44px !important;
      height: 44px !important;
    }

    #single-print-wrapper .sticker-amt {
      font-size: 7pt !important;
      font-weight: 900 !important;
      color: #000 !important;
    }

    #single-print-wrapper .sticker-code {
      font-size: 5pt !important;
      font-weight: 800 !important;
      color: #000 !important;
    }
  }
</style>
@endpush

@section('content')
<div class="qr-studio-wrapper">

  <!-- Header Banner -->
  <div class="qr-page-header">
    <div>
      <div class="qr-badge-pill">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path d="M12 3h.01"/></svg>
        Aarambh QR System (1" × 1" Thermal Sticker)
      </div>
      <h2 style="margin:0; font-size:1.5rem; font-weight:800; letter-spacing:-0.02em;">Generate QR Vouchers</h2>
      <p style="margin:4px 0 0; font-size:0.85rem; color:#cbd5e1; max-width:640px;">
        Create bulk QR batches with 4 simple inputs: <strong>QR Date</strong>, <strong>Batch Name</strong>, <strong>Count</strong>, and <strong>QR Amount</strong>. Generates high-density A4 PDF sheets (up to 100–120 QRs per page) matching the Color Master module.
      </p>
    </div>

    <div style="display:flex; gap:10px; align-items:center;">
      <a href="{{ route('qr.history') }}" class="btn btn-secondary" style="background:rgba(255,255,255,0.12); color:#fff; border-color:rgba(255,255,255,0.25); display:inline-flex; align-items:center; gap:8px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/></svg>
        QR Listing & Ledger
      </a>
    </div>
  </div>

  <!-- 4-FIELD GENERATOR FORM + LIVE 1"x1" STICKER PREVIEW -->
  <div class="qr-generator-grid">

    <!-- LEFT: 4-Field Form -->
    <div class="studio-form-card">
      <form action="{{ route('qr.generator.store') }}" method="POST" id="qr-gen-form" onsubmit="handleBatchGenerate(event)">
        @csrf

        <div class="form-section-title">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color:var(--primary-600);"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
          <span>QR Batch Specifications (4 Essential Fields)</span>
        </div>

        <div style="display:flex; flex-direction:column; gap:20px;">
          
          <!-- FIELD 1: QR Date -->
          <div class="form-group" style="margin-bottom:0;">
            <label class="input-label-primary">
              <span>1. QR Date <span style="color:#ef4444;">*</span></span>
              <span style="font-size:0.75rem; font-weight:500; color:var(--slate-500);">Issue / Manufacturing Date</span>
            </label>
            <input type="date" name="qr_date" id="input_qr_date" class="form-control" required value="{{ date('Y-m-d') }}" oninput="updateLivePreview()" style="font-weight:700; font-size:1rem;">
          </div>

          <!-- FIELD 2: Batch Name -->
          <div class="form-group" style="margin-bottom:0;">
            <label class="input-label-primary">
              <span>2. Batch Name <span style="color:#ef4444;">*</span></span>
              <span style="font-size:0.75rem; font-weight:500; color:var(--slate-500);">Aarambh Batch Tag</span>
            </label>
            <input type="text" name="batch_name" id="input_batch_name" class="form-control" required value="AARAMBH BATCH #1" placeholder="e.g. AARAMBH-LOT-2026 or Festive Offer" oninput="updateLivePreview()" style="font-weight:700; font-size:1rem; text-transform:uppercase;">
            
            <div class="preset-pills">
              <span style="font-size:0.75rem; color:var(--slate-500); align-self:center; margin-right:4px;">Quick:</span>
              <button type="button" class="preset-pill-btn" onclick="setBatch('AARAMBH BATCH #1')">AARAMBH #1</button>
              <button type="button" class="preset-pill-btn" onclick="setBatch('AARAMBH SUMMER 2026')">Summer 2026</button>
              <button type="button" class="preset-pill-btn" onclick="setBatch('AARAMBH FESTIVE VIP')">Festive VIP</button>
              <button type="button" class="preset-pill-btn" onclick="setBatch('AARAMBH LOT-A')">LOT-A</button>
            </div>
          </div>

          <!-- FIELD 3 & 4 (Grid): Count & QR Amount -->
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
            
            <!-- FIELD 3: Count -->
            <div class="form-group" style="margin-bottom:0;">
              <label class="input-label-primary">
                <span>3. Count (Quantity) <span style="color:#ef4444;">*</span></span>
              </label>
              <div style="position:relative;">
                <input type="number" name="count" id="input_count" class="form-control" required min="1" max="1000" value="10" placeholder="e.g. 50" oninput="updateLivePreview()" style="font-weight:800; font-size:1.1rem; color:var(--primary-700);">
                <span style="position:absolute; right:12px; top:50%; transform:translateY(-50%); font-weight:700; color:var(--slate-400); font-size:0.8rem;">QRs</span>
              </div>
              <div class="preset-pills">
                <button type="button" class="preset-pill-btn" onclick="setCount(1)">1</button>
                <button type="button" class="preset-pill-btn" onclick="setCount(10)">10</button>
                <button type="button" class="preset-pill-btn" onclick="setCount(50)">50</button>
                <button type="button" class="preset-pill-btn" onclick="setCount(100)">100</button>
                <button type="button" class="preset-pill-btn" onclick="setCount(500)">500</button>
              </div>
            </div>

            <!-- FIELD 4: QR Amount -->
            <div class="form-group" style="margin-bottom:0;">
              <label class="input-label-primary">
                <span>4. QR Amount (₹) <span style="color:#ef4444;">*</span></span>
              </label>
              <div style="position:relative;">
                <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); font-weight:800; color:var(--slate-500); font-size:1.1rem;">₹</span>
                <input type="number" name="amount" id="input_amount" class="form-control" required min="1" step="1" value="500" placeholder="500" oninput="updateLivePreview()" style="padding-left:30px; font-weight:800; font-size:1.1rem; color:#059669;">
              </div>
              <div class="preset-pills">
                <button type="button" class="preset-pill-btn" onclick="setAmount(50)">₹50</button>
                <button type="button" class="preset-pill-btn" onclick="setAmount(100)">₹100</button>
                <button type="button" class="preset-pill-btn" onclick="setAmount(200)">₹200</button>
                <button type="button" class="preset-pill-btn" onclick="setAmount(500)">₹500</button>
                <button type="button" class="preset-pill-btn" onclick="setAmount(1000)">₹1,000</button>
              </div>
            </div>

          </div>

          <!-- Color Master PDF Generation Option -->
          <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px 16px; margin-top:4px;">
            <label style="display:flex; align-items:center; gap:10px; font-weight:700; color:var(--slate-800); cursor:pointer; font-size:0.9rem; margin-bottom:0;">
              <input type="checkbox" name="download_pdf" value="1" checked style="width:18px; height:18px; accent-color:#4f46e5;">
              <span>Download A4 Multi-QR PDF Sheet immediately (Color Master Module Mode)</span>
            </label>
            <div style="font-size:0.75rem; color:var(--slate-500); margin-left:28px; margin-top:4px;">
              Arranges up to 100–120 QR codes tightly onto an A4 paper in a 10-column table grid with dashed cutting borders.
            </div>
          </div>

        </div>

        <!-- Form Submit Bar -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:28px; padding-top:20px; border-top:1px solid var(--slate-200, #e2e8f0); flex-wrap:wrap; gap:12px;">
          <a href="{{ route('qr.history') }}" class="btn btn-secondary">
            View All QRs
          </a>
          <div style="display:inline-flex; gap:10px;">
            <a href="{{ route('qr.pdfPreview') }}" target="_blank" class="btn btn-secondary" style="font-weight:700; display:inline-flex; align-items:center; gap:6px;" title="View HTML layout of A4 PDF sheet">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              Preview A4 Sheet
            </a>
            <button type="submit" id="btn-submit-gen" class="btn btn-primary" style="padding:12px 28px; font-size:1rem; font-weight:800; display:inline-flex; align-items:center; gap:8px; box-shadow:0 4px 14px rgba(79, 70, 229, 0.35);">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/></svg>
              Generate & Export A4 Sheet (<span id="btn-count-label">10</span> QRs)
            </button>
          </div>
        </div>

      </form>
    </div>

    <!-- RIGHT: 1" x 1" Aarambh Live Sticker Preview & Quick Print -->
    <div class="preview-sticky-card">
      
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
        <span style="font-size:0.8rem; font-weight:800; color:var(--slate-700); text-transform:uppercase; letter-spacing:0.06em;">
          1" × 1" Aarambh Sticker
        </span>
        <span style="font-size:0.75rem; background:#ecfdf5; color:#059669; font-weight:700; padding:2px 8px; border-radius:9999px; border:1px solid #a7f3d0;">
          Exact 1"x1" Size
        </span>
      </div>

      <p style="font-size:0.75rem; color:var(--slate-500); margin:0 0 12px;">
        Physical thermal label preview (TSC, Zebra, TVS, 25.4mm × 25.4mm)
      </p>

      <!-- 1" x 1" STICKER BOX (PRINTABLE) -->
      <div id="single-print-wrapper" class="majasol-sticker-box">
        <div class="sticker-brand" id="prev-brand">AARAMBH BATCH #1</div>
        <div class="sticker-qr">
          <div id="preview-qrcode-target"></div>
        </div>
        <div class="sticker-footer">
          <span class="sticker-amt" id="prev-amt">₹500</span>
          <span class="sticker-code" id="prev-code">ARM-500-PREV</span>
        </div>
      </div>

      <!-- Frontend Scan URL Card -->
      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:12px; margin-top:16px; text-align:left;">
        <div style="font-size:0.75rem; font-weight:800; color:var(--slate-600); text-transform:uppercase; margin-bottom:4px; display:flex; align-items:center; gap:6px;">
          <span>🔗 Frontend Scan URL</span>
        </div>
        <div style="font-size:0.75rem; font-family:var(--font-mono, monospace); color:var(--primary-700); word-break:break-all; background:#fff; padding:6px 8px; border-radius:6px; border:1px solid #cbd5e1;" id="prev-scan-url">
          {{ url('/claim/ARM-500-PREV') }}
        </div>
        <div style="font-size:0.7rem; color:var(--slate-500); margin-top:6px;">
          📱 When scanned by phone camera, customer opens this URL directly to redeem their ₹ discount.
        </div>
      </div>

      <!-- Quick Action Buttons -->
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-top:14px;">
        <a href="{{ route('qr.pdfPreview') }}" target="_blank" class="btn btn-secondary btn-sm" style="font-weight:700; display:inline-flex; align-items:center; justify-content:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          A4 Preview
        </a>

        <a href="{{ route('qr.exportPdf') }}?count=50" class="btn btn-secondary btn-sm" style="font-weight:700; display:inline-flex; align-items:center; justify-content:center; gap:6px; color:#4f46e5;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Export 50 A4
        </a>
      </div>

    </div>

  </div>

  <!-- RECENTLY GENERATED ACTIVE QRS -->
  @if(isset($vouchers) && count($vouchers) > 0)
    <div class="card" style="background:#fff; border-radius:var(--radius-xl, 16px); border:1px solid var(--slate-200, #e2e8f0); box-shadow:var(--shadow-sm); padding:22px;">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <div>
          <h3 style="margin:0; font-size:1.1rem; font-weight:800; color:var(--slate-900);">Recent Active QR Vouchers</h3>
          <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">Recently created Aarambh QR vouchers in circulation.</p>
        </div>
        <a href="{{ route('qr.history') }}" class="btn btn-secondary btn-sm" style="font-weight:700;">Open QR Listing Table →</a>
      </div>

      <div class="table-responsive">
        <table class="data-table" style="width:100%; font-size:0.85rem;">
          <thead>
            <tr>
              <th style="width:70px;">QR</th>
              <th>Voucher Code</th>
              <th>Batch Name</th>
              <th>Date</th>
              <th>Amount</th>
              <th>Status</th>
              <th style="text-align:right;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($vouchers->take(6) as $v)
              <tr>
                <td>
                  <div class="mini-table-qr" data-code="{{ $v->voucher_code }}" style="width:38px; height:38px; background:#fff; border:1px solid #e2e8f0; border-radius:4px; padding:2px; display:flex; align-items:center; justify-content:center;"></div>
                </td>
                <td style="font-family:var(--font-mono, monospace); font-weight:800; color:var(--primary-700);">
                  {{ $v->voucher_code }}
                </td>
                <td style="font-weight:700; color:var(--slate-800);">
                  {{ $v->batch_name ?: $v->title }}
                </td>
                <td style="color:var(--slate-600);">
                  {{ $v->qr_date ? date('d M Y', strtotime($v->qr_date)) : ($v->valid_from ? date('d M Y', strtotime($v->valid_from)) : '—') }}
                </td>
                <td style="font-weight:800; color:#059669;">
                  ₹{{ number_format($v->amount ?: $v->discount_amount ?: $v->discount_percent) }}
                </td>
                <td>
                  <span class="badge" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; font-weight:700; padding:2px 8px; border-radius:6px;">
                    ● Active
                  </span>
                </td>
                <td style="text-align:right;">
                  <div style="display:inline-flex; gap:6px;">
                    <a href="{{ route('qr.claim', ['code' => $v->voucher_code]) }}" target="_blank" class="btn btn-secondary btn-sm" style="padding:2px 8px; font-size:0.75rem;" title="Test Customer Frontend Scan URL">
                      Open Claim
                    </a>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @endif

</div>

<!-- QRCode Library CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

@push('scripts')
<script>
  let qrCodeGenerator = null;
  const baseUrl = "{{ url('/claim') }}";

  function setBatch(name) {
    document.getElementById('input_batch_name').value = name;
    updateLivePreview();
  }

  function setCount(n) {
    document.getElementById('input_count').value = n;
    updateLivePreview();
  }

  function setAmount(a) {
    document.getElementById('input_amount').value = a;
    updateLivePreview();
  }

  function updateLivePreview() {
    const batchName = document.getElementById('input_batch_name').value.trim() || 'AARAMBH BATCH #1';
    const count = parseInt(document.getElementById('input_count').value) || 1;
    const amount = parseFloat(document.getElementById('input_amount').value) || 500;
    
    // Update labels
    document.getElementById('prev-brand').innerText = batchName;
    document.getElementById('prev-amt').innerText = '₹' + amount;
    
    const sampleCode = `ARM-${Math.round(amount)}-SAMPLE`;
    document.getElementById('prev-code').innerText = sampleCode;
    
    const scanUrl = `${baseUrl}/${sampleCode}`;
    document.getElementById('prev-scan-url').innerText = scanUrl;
    
    const countLabel = document.getElementById('btn-count-label');
    if (countLabel) countLabel.innerText = count;

    // Render 1" x 1" QR Canvas
    const qrContainer = document.getElementById('preview-qrcode-target');
    if (qrContainer) {
      qrContainer.innerHTML = '';
      qrCodeGenerator = new QRCode(qrContainer, {
        text: scanUrl,
        width: 60,
        height: 60,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.M
      });
    }
  }

  function printSingleSticker() {
    window.print();
  }

  function testScanUrl() {
    const amount = parseFloat(document.getElementById('input_amount').value) || 500;
    const sampleCode = `ARM-${Math.round(amount)}-SAMPLE`;
    window.open(`${baseUrl}/${sampleCode}`, '_blank');
  }

  async function handleBatchGenerate(e) {
    // Form submits normally to Laravel backend or via AJAX if preferred
  }

  document.addEventListener('DOMContentLoaded', () => {
    updateLivePreview();

    // Render table mini QRs
    document.querySelectorAll('.mini-table-qr').forEach(el => {
      const code = el.getAttribute('data-code');
      if (code) {
        new QRCode(el, {
          text: `${baseUrl}/${code}`,
          width: 34,
          height: 34,
          colorDark: "#000000",
          colorLight: "#ffffff",
          correctLevel: QRCode.CorrectLevel.L
        });
      }
    });
  });
</script>
@endpush
@endsection
