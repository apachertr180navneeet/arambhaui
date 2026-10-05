@extends('layouts.app')

@section('title', 'QR Vouchers Listing & A4 Sheet Printing - aarambh')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Barcodes & QR</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>QR Listing & Ledger</span></div>
@endsection

@push('styles')
<style>
  /* ==========================================================================
     AARAMBH QR LISTING & 1" x 1" THERMAL STICKER PRINT SUITE
     ========================================================================== */
  .ledger-wrapper {
    display: flex;
    flex-direction: column;
    gap: 22px;
  }

  /* Stats Grid */
  .qr-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
  }

  .qr-stat-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid var(--slate-200, #e2e8f0);
    padding: 18px 20px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    display: flex;
    align-items: center;
    gap: 16px;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .qr-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.08);
  }

  .qr-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--accent-color, #4f46e5);
  }

  .qr-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .qr-stat-info {
    flex: 1;
    min-width: 0;
  }

  .qr-stat-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--slate-500, #64748b);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 2px;
  }

  .qr-stat-value {
    font-size: 1.55rem;
    font-weight: 800;
    color: var(--slate-900, #0f172a);
    line-height: 1.2;
    display: flex;
    align-items: baseline;
    gap: 8px;
  }

  /* Main Card */
  .qr-ledger-card {
    background: #ffffff;
    border-radius: 18px;
    border: 1px solid var(--slate-200, #e2e8f0);
    box-shadow: 0 2px 12px -2px rgba(15, 23, 42, 0.06);
    overflow: hidden;
  }

  .qr-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--slate-100, #f1f5f9);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
  }

  /* Filter Toolbar */
  .qr-toolbar {
    padding: 14px 24px;
    background: #fafafa;
    border-bottom: 1px solid var(--slate-200, #e2e8f0);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 14px;
  }

  .search-input-wrapper {
    position: relative;
    max-width: 320px;
    width: 100%;
  }

  .search-input-wrapper svg {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--slate-400, #94a3b8);
    pointer-events: none;
  }

  .search-input-wrapper input {
    padding-left: 36px;
    font-size: 0.85rem;
    border-radius: 10px;
    border: 1px solid var(--slate-300, #cbd5e1);
    background: #ffffff;
    width: 100%;
    height: 38px;
  }

  .status-filter-pills {
    display: flex;
    gap: 6px;
    background: var(--slate-200, #e2e8f0);
    padding: 3px;
    border-radius: 10px;
  }

  .status-filter-btn {
    border: none;
    background: transparent;
    padding: 6px 12px;
    font-size: 0.775rem;
    font-weight: 700;
    color: var(--slate-600, #475569);
    border-radius: 7px;
    cursor: pointer;
    transition: all 0.15s ease;
  }

  .status-filter-btn.active {
    background: #ffffff;
    color: var(--primary-700, #4338ca);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }

  /* QR Thumbnail inside Table */
  .table-qr-thumb {
    width: 44px;
    height: 44px;
    background: #ffffff;
    border: 1px solid var(--slate-300, #cbd5e1);
    border-radius: 6px;
    padding: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform 0.15s, box-shadow 0.15s;
  }

  .table-qr-thumb:hover {
    transform: scale(1.15);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border-color: var(--primary-500, #6366f1);
  }

  .voucher-code-badge {
    font-family: var(--font-mono, monospace);
    font-weight: 800;
    font-size: 0.85rem;
    background: #f5f3ff;
    color: #6d28d9;
    border: 1px solid #ddd6fe;
    padding: 4px 8px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
    transition: all 0.15s ease;
  }

  .voucher-code-badge:hover {
    background: #ede9fe;
    border-color: #c4b5fd;
    transform: translateY(-1px);
  }

  .ledger-table th {
    background: #f8fafc;
    color: var(--slate-600, #475569);
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 12px 14px;
    border-bottom: 1px solid var(--slate-200, #e2e8f0);
  }

  .ledger-table td {
    padding: 12px 14px;
    vertical-align: middle;
    border-bottom: 1px solid var(--slate-100, #f1f5f9);
    font-size: 0.85rem;
  }

  .ledger-table tr:hover td {
    background: #fafafa;
  }

  /* Action Buttons */
  .action-icon-btn {
    width: 30px;
    height: 30px;
    border-radius: 6px;
    border: 1px solid var(--slate-200, #e2e8f0);
    background: #ffffff;
    color: var(--slate-600, #475569);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
  }

  .action-icon-btn:hover {
    background: var(--slate-100, #f1f5f9);
    color: var(--slate-900, #0f172a);
    border-color: var(--slate-300, #cbd5e1);
  }

  .action-icon-btn.print-btn {
    color: #0f172a;
    font-weight: 700;
  }

  .action-icon-btn.print-btn:hover {
    background: #f8fafc;
    border-color: #0f172a;
  }

  .action-icon-btn.info:hover {
    background: #eff6ff;
    color: #2563eb;
    border-color: #bfdbfe;
  }

  .action-icon-btn.primary:hover {
    background: #eef2ff;
    color: #4f46e5;
    border-color: #c7d2fe;
  }

  .action-icon-btn.danger:hover {
    background: #fef2f2;
    color: #dc2626;
    border-color: #fca5a5;
  }

  .action-icon-btn.warning:hover {
    background: #fffbeb;
    color: #d97706;
    border-color: #fde68a;
  }

  /* Aarambh 1" x 1" Sticker Modal Design */
  .majasol-sticker-card {
    width: 1.25in;
    height: 1.25in;
    margin: 0 auto;
    background: #ffffff;
    border: 2px solid #000;
    border-radius: 4px;
    padding: 3px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    box-sizing: border-box;
    text-align: center;
    box-shadow: 0 6px 16px rgba(0,0,0,0.15);
  }

  .majasol-sticker-card .sticker-brand {
    font-size: 7.5pt;
    font-weight: 900;
    text-transform: uppercase;
    color: #000;
    line-height: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
  }

  .majasol-sticker-card .sticker-qr {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .majasol-sticker-card .sticker-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    padding: 0 1px;
    line-height: 1;
  }

  .majasol-sticker-card .sticker-amt {
    font-size: 8.5pt;
    font-weight: 900;
    color: #000;
  }

  .majasol-sticker-card .sticker-code {
    font-family: var(--font-mono, monospace);
    font-size: 6pt;
    font-weight: 800;
    color: #000;
  }

  /* Hide print area on screen so it never causes page distortion */
  #print-container-area {
    display: none !important;
  }

  /* ==========================================================================
     AARAMBH A4 MULTI-QR SHEET PRINT STYLES (5 COLUMNS - 1.5" THERMAL/A4 STICKERS)
     ========================================================================== */
  @media print {
    @page {
      size: A4 portrait;
      margin: 6mm 5mm 6mm 5mm;
    }

    html, body {
      background: #ffffff !important;
      margin: 0 !important;
      padding: 0 !important;
      width: 100% !important;
      height: auto !important;
      overflow: visible !important;
      font-family: 'Helvetica', 'Arial', sans-serif !important;
    }

    body * {
      visibility: hidden !important;
    }

    #print-container-area,
    #print-container-area * {
      visibility: visible !important;
    }

    #print-container-area {
      display: block !important;
      position: absolute !important;
      left: 0 !important;
      top: 0 !important;
      width: 100% !important;
      margin: 0 !important;
      padding: 0 !important;
      background: #ffffff !important;
    }

    .print-a4-header {
      text-align: center !important;
      margin-bottom: 5px !important;
      padding-bottom: 3px !important;
      border-bottom: 0.8px solid #0f172a !important;
    }

    .print-a4-header h2 {
      margin: 0 !important;
      font-size: 11pt !important;
      font-weight: 900 !important;
      text-transform: uppercase !important;
      letter-spacing: 0.5px !important;
      color: #000000 !important;
    }

    .print-a4-header p {
      margin: 1px 0 0 !important;
      font-size: 7.5pt !important;
      color: #334155 !important;
    }

    /* Fixed Table Layout for 100% reliable print alignment without stretching */
    .print-a4-table {
      width: 100% !important;
      border-collapse: collapse !important;
      table-layout: fixed !important;
      margin: 0 auto !important;
      background: #ffffff !important;
    }

    .print-a4-table tr {
      page-break-inside: avoid !important;
      break-inside: avoid !important;
    }

    .print-a4-table td {
      width: 20% !important;
      height: 38mm !important;
      max-height: 38mm !important;
      padding: 2.5mm 1.5mm !important;
      text-align: center !important;
      vertical-align: middle !important;
      border: 0.7px dashed #94a3b8 !important;
      box-sizing: border-box !important;
      overflow: hidden !important;
    }

    .print-qr-card {
      display: block !important;
      width: 100% !important;
      text-align: center !important;
      margin: 0 auto !important;
    }

    .print-qr-card .cell-brand {
      font-size: 7.5pt !important;
      font-weight: 800 !important;
      color: #0f172a !important;
      text-transform: uppercase !important;
      line-height: 1.1 !important;
      margin-bottom: 1.5mm !important;
      white-space: nowrap !important;
      overflow: hidden !important;
      text-overflow: ellipsis !important;
      max-width: 95% !important;
      margin-left: auto !important;
      margin-right: auto !important;
    }

    .print-qr-card .cell-qr-wrap {
      width: 23mm !important;
      height: 23mm !important;
      margin: 0 auto !important;
      display: block !important;
      overflow: hidden !important;
    }

    .print-qr-card .cell-qr,
    .print-qr-card .cell-qr img,
    .print-qr-card .cell-qr canvas {
      width: 23mm !important;
      height: 23mm !important;
      max-width: 23mm !important;
      max-height: 23mm !important;
      display: block !important;
      margin: 0 auto !important;
      aspect-ratio: 1 / 1 !important;
      object-fit: contain !important;
    }

    .print-qr-card .cell-amt {
      font-size: 9.5pt !important;
      font-weight: 900 !important;
      color: #047857 !important;
      line-height: 1 !important;
      margin-top: 1.5mm !important;
      margin-bottom: 1mm !important;
    }

    .print-qr-card .cell-code {
      font-size: 6.5pt !important;
      font-weight: 700 !important;
      color: #1e293b !important;
      font-family: monospace !important;
      line-height: 1 !important;
      letter-spacing: 0.2px !important;
      white-space: nowrap !important;
      overflow: hidden !important;
      text-overflow: ellipsis !important;
      max-width: 95% !important;
      margin-left: auto !important;
      margin-right: auto !important;
    }
  }
</style>
@endpush

@section('content')
<div class="ledger-wrapper">

  <!-- KPI Metrics Row -->
  <div class="qr-stats-grid">
    
    <div class="qr-stat-card" style="--accent-color: #4f46e5;">
      <div class="qr-stat-icon" style="background: #eef2ff; color: #4f46e5;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path d="M12 3h.01"/></svg>
      </div>
      <div class="qr-stat-info">
        <div class="qr-stat-label">Total QRs Generated</div>
        <div class="qr-stat-value">
          {{ number_format($stats['total']) }}
          <span style="font-size:0.75rem; font-weight:600; color:var(--slate-500);">Tokens</span>
        </div>
      </div>
    </div>

    <div class="qr-stat-card" style="--accent-color: #10b981;">
      <div class="qr-stat-icon" style="background: #ecfdf5; color: #059669;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      </div>
      <div class="qr-stat-info">
        <div class="qr-stat-label">Active & Claimable</div>
        <div class="qr-stat-value" style="color: #059669;">
          {{ number_format($stats['active']) }}
          <span style="font-size:0.75rem; background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; padding:2px 8px; border-radius:9999px; font-weight:700;">Live</span>
        </div>
      </div>
    </div>

    <div class="qr-stat-card" style="--accent-color: #8b5cf6;">
      <div class="qr-stat-icon" style="background: #f5f3ff; color: #7c3aed;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>
      </div>
      <div class="qr-stat-info">
        <div class="qr-stat-label">Claimed (Pending)</div>
        <div class="qr-stat-value" style="color: #7c3aed;">
          {{ number_format($stats['claimed'] ?? $stats['redeemed']) }}
          <span style="font-size:0.75rem; font-weight:600; color:var(--slate-500);">Claims</span>
        </div>
      </div>
    </div>

    <div class="qr-stat-card" style="--accent-color: #059669;">
      <div class="qr-stat-icon" style="background: #ecfdf5; color: #059669;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/><path d="m9 12 2 2 4-4"/></svg>
      </div>
      <div class="qr-stat-info">
        <div class="qr-stat-label">Paid Claims</div>
        <div class="qr-stat-value" style="color: #059669;">
          {{ number_format($stats['paid'] ?? 0) }}
          <span style="font-size:0.75rem; background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; padding:2px 8px; border-radius:9999px; font-weight:700;">Paid</span>
        </div>
      </div>
    </div>

    <div class="qr-stat-card" style="--accent-color: #f43f5e;">
      <div class="qr-stat-icon" style="background: #fff1f2; color: #e11d48;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      </div>
      <div class="qr-stat-info">
        <div class="qr-stat-label">Expired / Revoked</div>
        <div class="qr-stat-value" style="color: #e11d48;">
          {{ number_format($stats['expired']) }}
          <span style="font-size:0.75rem; font-weight:600; color:var(--slate-500);">Closed</span>
        </div>
      </div>
    </div>

  </div>

  <!-- Main Ledger Card Container -->
  <div class="qr-ledger-card">
    
    <!-- Action Header -->
    <div class="qr-card-header">
      <div>
        <h3 style="margin:0; font-size:1.2rem; font-weight:800; color:var(--slate-900);">Aarambh QR Vouchers Ledger & A4 Sheet Printing</h3>
        <p style="margin:4px 0 0; font-size:0.825rem; color:var(--slate-500);">
          Displaying QR codes directly in table. Print A4 multi-QR sheets (Color Master style) or download PDF.
        </p>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <!-- Color Master Export PDF Form with Column Selector -->
        <form action="{{ route('qr.exportPdf') }}" method="GET" style="display:inline-flex; align-items:center; gap:6px;">
          <input type="number" name="count" value="50" min="1" max="1000" class="form-control form-control-sm" placeholder="No. of Records" style="width:90px; height:34px; font-weight:700;" title="Number of QR records to export on A4 sheet">
          <select name="cols" class="form-select form-select-sm" style="height:34px; font-weight:700; width:125px; font-size:11px;" title="Sticker Columns per Row on A4">
            <option value="auto" selected>Auto-Fit (Smart)</option>
            <option value="4">4 Cols (2" Large)</option>
            <option value="5">5 Cols (1.5" Standard)</option>
            <option value="6">6 Cols (1.25" Compact)</option>
          </select>
          <button type="submit" class="btn btn-secondary btn-sm" style="height:34px; font-weight:700; display:inline-flex; align-items:center; gap:5px; background:#f1f5f9; color:#0f172a; border-color:#cbd5e1;" title="Download A4 Multi-QR PDF">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Export PDF
          </button>
        </form>

        <button type="button" class="btn btn-secondary btn-sm" onclick="printVisibleA4Grid()" style="height:34px; display:inline-flex; align-items:center; gap:6px; font-weight:700;" title="Print All Visible QR Codes onto Single A4 Grid">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
          Print A4 Sheet
        </button>

        <a href="{{ route('qr.generator') }}" class="btn btn-primary btn-sm" style="height:34px; display:inline-flex; align-items:center; gap:6px; font-weight:700; box-shadow:0 2px 8px rgba(79, 70, 229, 0.3);">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
          + Generate New Batch
        </a>
      </div>
    </div>

    <!-- Quick Filter Toolbar -->
    <div class="qr-toolbar">
      <div class="search-input-wrapper">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="voucher-search-input" placeholder="Search batch, code, date, amount..." onkeyup="filterVouchersTable()">
      </div>

      <div class="status-filter-pills">
        <button type="button" class="status-filter-btn active" onclick="setStatusFilter('all', this)">
          All ({{ $stats['total'] }})
        </button>
        <button type="button" class="status-filter-btn" onclick="setStatusFilter('active', this)">
          Active ({{ $stats['active'] }})
        </button>
        <button type="button" class="status-filter-btn" onclick="setStatusFilter('claimed', this)">
          Claimed ({{ $stats['claimed'] ?? $stats['redeemed'] }})
        </button>
        <button type="button" class="status-filter-btn" onclick="setStatusFilter('paid', this)">
          Paid ({{ $stats['paid'] ?? 0 }})
        </button>
        <button type="button" class="status-filter-btn" onclick="setStatusFilter('expired', this)">
          Expired ({{ $stats['expired'] }})
        </button>
      </div>
    </div>

    <!-- Multi-Select Action Bar -->
    <div id="selection-action-bar" style="display:none; background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:10px 16px; margin:0 24px 16px 24px; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
      <div style="font-weight:700; color:#1e40af; font-size:0.875rem; display:inline-flex; align-items:center; gap:8px;">
        <span class="badge" style="background:#2563eb; color:#fff; font-size:0.8rem; padding:3px 8px; border-radius:9999px;" id="selected-count-badge">0</span>
        <span>QR Voucher(s) selected</span>
      </div>
      <div style="display:inline-flex; gap:8px;">
        <button type="button" class="btn btn-primary btn-sm" onclick="printSelectedA4()" style="background:#2563eb; border-color:#2563eb; font-weight:700; display:inline-flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
          Print Selected (A4 Grid)
        </button>
        <button type="button" class="btn btn-secondary btn-sm" onclick="exportSelectedPdf()" style="font-weight:700; display:inline-flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Export Selected to PDF
        </button>
        <button type="button" class="btn btn-secondary btn-sm" onclick="clearSelectedVouchers()" style="font-weight:600; color:#64748b;">
          Clear
        </button>
      </div>
    </div>

    <!-- Table With Direct QR Preview Column -->
    <div class="table-responsive">
      <table class="data-table ledger-table" id="qr-table" style="width:100%;">
        <thead>
          <tr>
            <th style="width:36px; text-align:center;">
              <input type="checkbox" id="check-all-vouchers" onchange="toggleSelectAllVouchers(this)" title="Select All Visible">
            </th>
            <th style="width:60px; text-align:center;">QR</th>
            <th style="width:160px;">Voucher Code</th>
            <th>Batch Name</th>
            <th>QR Date</th>
            <th>Amount (₹)</th>
            <th>Status</th>
            <th>Redemption Audit</th>
            <th style="text-align:right; width:150px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($vouchers as $v)
            @php
              $amt = (float)($v->amount ?: $v->discount_amount ?: $v->discount_percent ?: 0);
              $batch = $v->batch_name ?: $v->title ?: 'AARAMBH';
              $isExpired = $v->status === 'Expired' || ($v->valid_until && strtotime($v->valid_until) < strtotime(date('Y-m-d')));
              $isPaid = ($v->status === 'Paid' || strtolower((string)$v->payment_status) === 'paid');
              $isClaimed = !$isPaid && ($v->status === 'Redeemed' || (bool)$v->is_redeemed);

              if ($isPaid) {
                $statusKey = 'paid';
              } elseif ($isClaimed) {
                $statusKey = 'claimed';
              } elseif ($isExpired) {
                $statusKey = 'expired';
              } else {
                $statusKey = 'active';
              }
              $claimUrl = url('/claim/' . $v->voucher_code);
            @endphp
            <tr class="voucher-row" data-id="{{ $v->id }}" data-status="{{ $statusKey }}" data-code="{{ $v->voucher_code }}" data-batch="{{ $batch }}" data-amt="{{ $amt }}" data-url="{{ $claimUrl }}">
              
              <!-- 0. SELECT CHECKBOX -->
              <td style="text-align:center;">
                <input type="checkbox" class="voucher-select-box" value="{{ $v->id }}" data-code="{{ $v->voucher_code }}" data-batch="{{ $batch }}" data-amt="{{ $amt }}" data-url="{{ $claimUrl }}" onchange="handleRowSelect()">
              </td>

              <!-- 1. LIVE QR CODE PREVIEW COLUMN -->
              <td style="text-align:center;">
                <div class="table-qr-thumb list-qr-canvas" data-url="{{ $claimUrl }}" data-code="{{ $v->voucher_code }}" onclick='openStickerModal(@json($v))' title="Click to view Sticker & Scan URL">
                </div>
              </td>

              <!-- 2. VOUCHER CODE -->
              <td>
                <span class="voucher-code-badge" onclick="copyCode('{{ $v->voucher_code }}')" title="Click to copy code">
                  <span>{{ $v->voucher_code }}</span>
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                </span>
                <div style="margin-top:3px;">
                  <a href="{{ $claimUrl }}" target="_blank" style="font-size:0.72rem; color:var(--primary-600); text-decoration:none; display:inline-flex; align-items:center; gap:3px;">
                    <span>🔗 Claim URL</span>
                  </a>
                </div>
              </td>

              <!-- 3. BATCH NAME -->
              <td>
                <div style="font-weight:700; color:var(--slate-900);">{{ $batch }}</div>
                @if($v->customer_phone)
                  <div style="font-size:0.725rem; color:var(--primary-700); font-weight:600;">
                    🔒 Lock: +91 {{ $v->customer_phone }}
                  </div>
                @endif
              </td>

              <!-- 4. QR DATE -->
              <td>
                <div style="font-weight:600; color:var(--slate-800);">
                  {{ $v->qr_date ? date('d M Y', strtotime($v->qr_date)) : ($v->valid_from ? date('d M Y', strtotime($v->valid_from)) : '—') }}
                </div>
              </td>

              <!-- 5. AMOUNT (₹) -->
              <td>
                <div style="font-weight:800; color:#059669; font-size:1rem;">
                  ₹{{ number_format($amt) }}
                </div>
              </td>

              <!-- 6. STATUS -->
              <td>
                @if($isPaid)
                  <span class="badge" style="background:#ecfdf5; color:#059669; border:1px solid #10b981; font-weight:800; padding:4px 11px; border-radius:9999px; display:inline-flex; align-items:center; gap:4px; box-shadow:0 1px 2px rgba(16,185,129,0.15);">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                    Paid
                  </span>
                @elseif($isClaimed)
                  <span class="badge" style="background:#f5f3ff; color:#7c3aed; border:1px solid #ddd6fe; font-weight:700; padding:4px 10px; border-radius:9999px; display:inline-flex; align-items:center; gap:4px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Claimed
                  </span>
                @elseif($isExpired)
                  <span class="badge" style="background:#fff1f2; color:#e11d48; border:1px solid #fecdd3; font-weight:700; padding:4px 10px; border-radius:9999px;">
                    ✕ Expired
                  </span>
                @else
                  <span class="badge" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; font-weight:700; padding:4px 10px; border-radius:9999px;">
                    ● Active
                  </span>
                @endif
              </td>

              <!-- 7. REDEMPTION AUDIT -->
              <td>
                @if($v->is_redeemed || $v->redeemed_at || $isPaid)
                  <div style="font-weight:700; color:#475569; font-size:0.8rem;">
                    {{ $v->redeemed_at ? (is_string($v->redeemed_at) ? $v->redeemed_at : $v->redeemed_at->format('d M Y, h:i A')) : 'Claimed' }}
                  </div>
                  @if($v->customer_phone)
                    <div style="font-size:0.75rem; color:var(--slate-600);">Payer: {{ $v->customer_phone }}</div>
                  @endif
                  @if($v->recipient_upi_id)
                    <div style="font-size:0.72rem; color:#4338ca; font-weight:700;" title="Receiver: {{ $v->recipient_name }}">
                      Payee: {{ $v->recipient_upi_id }}
                    </div>
                  @endif
                  @if($v->recipient_address || $v->recipient_pincode)
                    <div style="font-size:0.73rem; color:#15803d; background:#f0fdf4; border:1px solid #86efac; border-radius:6px; padding:3px 7px; margin-top:3px; line-height:1.3;" title="Gift Delivery Address: {{ $v->recipient_address }}">
                      🎁 <strong>Gift Address:</strong> {{ Str::limit($v->recipient_address, 35) }}
                      @if($v->recipient_pincode)
                        <br><span style="color:#166534; font-weight:700;">PIN: {{ $v->recipient_pincode }}</span>
                      @endif
                      @if($v->recipient_city)
                        <span style="color:#64748b;">({{ $v->recipient_city }})</span>
                      @endif
                    </div>
                  @endif
                  @if($isPaid)
                    <div style="font-size:0.72rem; color:#059669; font-weight:800; margin-top:3px;">
                      <span style="background:#dcfce7; color:#15803d; padding:2px 7px; border-radius:4px; border:1px solid #86efac; display:inline-flex; align-items:center; gap:3px;">
                        ✓ Paid: ₹{{ number_format($amt, 2) }} <span style="font-weight:600; color:#166534;">({{ $v->payment_method ?: 'UPI' }})</span>
                      </span>
                    </div>
                    @if($v->upi_txn_ref)
                      <div style="font-size:0.68rem; color:#64748b; font-family:monospace; margin-top:1px;">Ref: {{ $v->upi_txn_ref }}</div>
                    @endif
                  @else
                    <div style="font-size:0.72rem; color:#b45309; font-weight:700; margin-top:3px;">
                      <span style="background:#fef3c7; color:#b45309; padding:2px 7px; border-radius:4px; border:1px solid #fde68a; display:inline-flex; align-items:center; gap:3px;">
                        ⏳ Payment Pending
                      </span>
                    </div>
                  @endif
                @else
                  <span style="font-size:0.8rem; color:var(--slate-400);">— Not Claimed —</span>
                @endif
              </td>

              <!-- 8. ACTIONS & PRINT -->
              <td style="text-align:right;">
                <div style="display:inline-flex; gap:6px; align-items:center;">
                  
                  <!-- Mark as Paid Button (For Claimed / Unpaid Vouchers) -->
                  @if($isClaimed)
                    <button type="button" class="btn btn-sm btn-mark-paid" onclick='openMarkPaidModal(@json($v))' style="font-size:0.75rem; font-weight:800; padding:4px 9px; display:inline-flex; align-items:center; gap:4px; background:#16a34a; border-color:#16a34a; color:#ffffff; border-radius:6px; cursor:pointer; box-shadow:0 1px 3px rgba(22,163,74,0.3);" title="Mark this claim as Paid">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                      Mark Paid
                    </button>
                  @endif

                  <!-- Direct Print Button -->
                  <button type="button" class="btn btn-secondary btn-sm" title="Print this QR Code" onclick='printSingleRowSticker(@json($v))' style="font-size:0.75rem; font-weight:800; padding:3px 8px; display:inline-flex; align-items:center; gap:4px; background:#f8fafc; border-color:#0f172a; color:#0f172a;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                    Print
                  </button>

                  <!-- View Modal Trigger -->
                  <button type="button" class="action-icon-btn info" title="View Sticker & Details" onclick='openStickerModal(@json($v))'>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  </button>

                  <!-- Edit Modal Trigger -->
                  <button type="button" class="action-icon-btn primary" title="Edit Voucher" onclick='openEditModal(@json($v))'>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                  </button>

                  <!-- Expire / Reactivate Toggle -->
                  @if($v->status === 'Active')
                    <form action="{{ route('qr.expire', $v->id) }}" method="POST" style="display:inline;" onsubmit="return handleExpireVoucher(this, '{{ $v->voucher_code }}', event)">
                      @csrf
                      <button type="submit" class="action-icon-btn warning" title="Expire / Deactivate Voucher">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                      </button>
                    </form>
                  @else
                    <form action="{{ route('qr.reactivate', $v->id) }}" method="POST" style="display:inline;" onsubmit="return handleReactivateVoucher(this, '{{ $v->voucher_code }}', event)">
                      @csrf
                      <button type="submit" class="action-icon-btn" title="Reactivate Voucher">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                      </button>
                    </form>
                  @endif

                  <!-- Delete Voucher -->
                  <form action="{{ route('qr.destroy', $v->id) }}" method="POST" style="display:inline;" onsubmit="return handleDeleteVoucher(this, '{{ $v->voucher_code }}', event)">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-icon-btn danger" title="Delete Voucher">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                    </button>
                  </form>

                </div>
              </td>

            </tr>
          @empty
            <tr>
              <td colspan="9" style="text-align:center; padding:48px 20px; color:var(--slate-400);">
                <div style="width:48px; height:48px; border-radius:12px; background:var(--slate-100); color:var(--slate-400); display:inline-flex; align-items:center; justify-content:center; margin-bottom:12px;">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/></svg>
                </div>
                <div style="font-weight:700; color:var(--slate-700); font-size:1rem; margin-bottom:4px;">No QR vouchers found</div>
                <p style="font-size:0.85rem; color:var(--slate-500); margin:0 0 16px;">Generate your first QR batch to print 1"x1" stickers.</p>
                <a href="{{ route('qr.generator') }}" class="btn btn-primary btn-sm">+ Generate First QR Batch</a>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>

<!-- ==========================================================================
     STICKER VIEW & PRINT MODAL
     ========================================================================== -->
<div class="modal-backdrop" id="stickerModal" style="display:none;" onclick="if(event.target===this) closeStickerModal()">
  <div class="modal-dialog" style="max-width:560px;">
    
    <div class="modal-header">
      <div class="modal-title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/></svg>
        <span>1" × 1" Aarambh Sticker & Scan URL</span>
      </div>
      <button type="button" class="modal-close-btn" onclick="closeStickerModal()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <div class="modal-body" style="padding:24px; text-align:center;">
      
      <!-- Exact 1"x1" Aarambh Sticker Card -->
      <div class="majasol-sticker-card" id="modal-sticker-card">
        <div class="sticker-brand" id="m-batch-name">AARAMBH BATCH</div>
        <div class="sticker-qr">
          <div id="modal-qr-target"></div>
        </div>
        <div class="sticker-footer">
          <span class="sticker-amt" id="m-amt">₹500</span>
          <span class="sticker-code" id="m-code">ARM-500-1234</span>
        </div>
      </div>

      <!-- Frontend Scan URL breakdown -->
      <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px; margin-top:20px; text-align:left;">
        <div style="font-size:0.75rem; font-weight:800; color:var(--slate-600); text-transform:uppercase; margin-bottom:4px; display:flex; justify-content:space-between;">
          <span>🔗 Frontend Customer Scan URL</span>
          <span style="color:#059669; font-weight:700;">After Scanning QR</span>
        </div>
        <div style="font-size:0.8rem; font-family:var(--font-mono, monospace); color:var(--primary-700); word-break:break-all; background:#fff; padding:8px 10px; border-radius:6px; border:1px solid #cbd5e1;" id="m-scan-url">
          https://...
        </div>
        <div style="font-size:0.75rem; color:var(--slate-500); margin-top:6px;">
          When customer scans the 1"x1" sticker with any phone camera, it opens this claim portal to redeem discount.
        </div>
      </div>

      <!-- Gift Delivery Address Info in Modal -->
      <div id="m-gift-delivery-box" style="display:none; background:#f0fdf4; border:1.5px solid #86efac; border-radius:10px; padding:14px; margin-top:14px; text-align:left;">
        <div style="font-size:0.78rem; font-weight:800; color:#15803d; text-transform:uppercase; margin-bottom:6px; display:flex; align-items:center; gap:6px;">
          <span>🎁</span> Gift Delivery Address & PIN
        </div>
        <div style="font-size:0.85rem; color:#0f172a; font-weight:600; line-height:1.4;" id="m-gift-address">
          ---
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px; border-top:1px dashed #bbf7d0; padding-top:6px; font-size:0.8rem;">
          <span style="color:#166534; font-weight:700;">PIN Code: <span id="m-gift-pincode" class="font-mono" style="font-size:0.95rem; color:#14532d;">---</span></span>
          <span style="color:#64748b;" id="m-gift-city"></span>
        </div>
      </div>

      <!-- Modal Action Buttons -->
      <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; margin-top:20px;">
        <button type="button" class="btn btn-secondary" onclick="copyModalClaimUrl()">
          Copy Scan Link
        </button>
        <div style="display:flex; gap:8px;">
          <a href="#" id="m-open-claim-btn" target="_blank" class="btn btn-secondary">
            Open Claim Page
          </a>
          <button type="button" class="btn btn-primary" onclick="printModalSticker()" style="background:#0f172a; border-color:#0f172a; color:#fff; font-weight:800;">
            Print 1"×1" Sticker
          </button>
        </div>
      </div>

    </div>

  </div>
</div>

<!-- ==========================================================================
     EDIT VOUCHER MODAL
     ========================================================================== -->
<div class="modal-backdrop" id="editVoucherModal" style="display:none;" onclick="if(event.target===this) closeEditModal()">
  <div class="modal-dialog" style="max-width:540px;">
    
    <div class="modal-header">
      <div class="modal-title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        <span>Edit QR Voucher</span>
      </div>
      <button type="button" class="modal-close-btn" onclick="closeEditModal()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <form id="edit-voucher-form" onsubmit="handleEditSubmit(event)">
      @csrf
      <input type="hidden" id="edit_voucher_id" name="id">

      <div class="modal-body" style="padding:22px; display:flex; flex-direction:column; gap:16px;">
        
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700;">Voucher Code</label>
          <input type="text" id="edit_voucher_code" class="form-control" readonly style="background:#f1f5f9; font-family:var(--font-mono, monospace); font-weight:800;">
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700;">Batch Name <span style="color:red;">*</span></label>
          <input type="text" id="edit_batch_name" name="batch_name" class="form-control" required style="font-weight:700;">
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700;">QR Date</label>
            <input type="date" id="edit_qr_date" name="qr_date" class="form-control">
          </div>

          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700;">Amount (₹) <span style="color:red;">*</span></label>
            <input type="number" id="edit_amount" name="amount" class="form-control" required min="1" step="1" style="font-weight:800; color:#059669;">
          </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700;">Status</label>
            <select id="edit_status" name="status" class="form-control" style="font-weight:700;">
              <option value="Active">Active</option>
              <option value="Redeemed">Claimed</option>
              <option value="Paid">Paid</option>
              <option value="Expired">Expired</option>
            </select>
          </div>

          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700;">Payment Status</label>
            <select id="edit_payment_status" name="payment_status" class="form-control" style="font-weight:700;">
              <option value="Pending">Pending</option>
              <option value="Paid">Paid</option>
            </select>
          </div>
        </div>

      </div>

      <div class="modal-footer" style="padding:16px 22px; border-top:1px solid #e2e8f0; display:flex; justify-content:space-between;">
        <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
        <button type="submit" id="btn-save-edit" class="btn btn-primary" style="font-weight:800;">Save Changes</button>
      </div>

    </form>
  </div>
</div>

<!-- ==========================================================================
     MARK CLAIM AS PAID MODAL
     ========================================================================== -->
<div class="modal-backdrop" id="markPaidModal" style="display:none;" onclick="if(event.target===this) closeMarkPaidModal()">
  <div class="modal-dialog" style="max-width:480px;">
    <form id="markPaidForm" onsubmit="event.preventDefault(); submitMarkPaid();">
      @csrf
      <input type="hidden" id="pay_voucher_id" name="voucher_id">

      <div class="modal-header" style="background:#f0fdf4; border-bottom:1px solid #bbf7d0; padding:16px 20px;">
        <div class="modal-title" style="color:#15803d; font-size:1.1rem; font-weight:800; display:flex; align-items:center; gap:8px;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/><polyline points="20 6 9 17 4 12"/></svg>
          Confirm Payment Settlement
        </div>
        <button type="button" class="modal-close-btn" onclick="closeMarkPaidModal()" style="border:none; background:transparent;">✕</button>
      </div>

      <div class="modal-body" style="padding:20px; display:flex; flex-direction:column; gap:16px;">
        
        <!-- Summary Box -->
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:14px; font-size:0.88rem;">
          <div style="display:flex; justify-content:space-between; margin-bottom:8px; border-bottom:1px dashed #cbd5e1; padding-bottom:8px;">
            <span style="color:#64748b;">Voucher Code:</span>
            <strong id="pay_code" class="font-mono" style="color:#0f172a;">---</strong>
          </div>
          <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
            <span style="color:#64748b;">Payout Amount:</span>
            <strong id="pay_amount" style="color:#15803d; font-size:1.15rem; font-weight:900;">₹0.00</strong>
          </div>
          <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
            <span style="color:#64748b;">Customer Mobile:</span>
            <strong id="pay_phone" style="color:#0f172a;">---</strong>
          </div>
          <div style="display:flex; justify-content:space-between;">
            <span style="color:#64748b;">Recipient UPI / QR:</span>
            <strong id="pay_dest" class="font-mono" style="color:#2563eb; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">---</strong>
          </div>
        </div>

        <!-- Payment Method -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.85rem; margin-bottom:6px;">Payment Method <span style="color:red;">*</span></label>
          <select id="pay_method" name="payment_method" class="form-control" style="font-weight:700;">
            <option value="UPI" selected>UPI Transfer (PhonePe / GPay / Paytm)</option>
            <option value="Direct QR">Direct QR Scan Payment</option>
            <option value="Bank Transfer">Bank Transfer (IMPS / NEFT)</option>
            <option value="Cash">Cash Handover</option>
          </select>
        </div>

        <!-- UPI / UTR Transaction Reference ID -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.85rem; margin-bottom:6px;">Bank / UPI Transaction Ref ID (UTR)</label>
          <input type="text" id="pay_txn_ref" name="upi_txn_ref" class="form-control" placeholder="e.g. 425619284729 or UTR No. (Optional)" style="font-family:monospace;">
          <span style="font-size:0.75rem; color:#64748b; margin-top:4px; display:block;">Saving reference number helps in bank reconciliation.</span>
        </div>

      </div>

      <div class="modal-footer" style="padding:16px 20px; border-top:1px solid #e2e8f0; display:flex; justify-content:space-between;">
        <button type="button" class="btn btn-secondary" onclick="closeMarkPaidModal()">Cancel</button>
        <button type="submit" id="btn-confirm-paid" class="btn btn-primary" style="background:#16a34a; border-color:#16a34a; font-weight:800; display:inline-flex; align-items:center; gap:6px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
          Confirm Paid
        </button>
      </div>

    </form>
  </div>
</div>

<!-- ==========================================================================
     HIDDEN PRINT CONTAINER FOR BATCH & SINGLE 1"x1" STICKERS
     ========================================================================== -->
<div id="print-container-area" style="display:none;"></div>

<!-- QRCode Library CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

@push('scripts')
<script>
  let activeModalVoucher = null;
  const baseUrl = "{{ url('/claim') }}";

  function copyCode(code) {
    navigator.clipboard.writeText(code);
    if (window.UI && UI.showToast) {
      UI.showToast('Copied', `Voucher Code ${code} copied to clipboard`, 'info');
    } else {
      alert(`Copied Code: ${code}`);
    }
  }

  function setStatusFilter(status, btn) {
    document.querySelectorAll('.status-filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const rows = document.querySelectorAll('.voucher-row');
    rows.forEach(r => {
      const rowStatus = r.getAttribute('data-status');
      if (status === 'all') {
        r.style.display = '';
      } else if (status === 'claimed' || status === 'redeemed') {
        r.style.display = (rowStatus === 'claimed' || rowStatus === 'redeemed') ? '' : 'none';
      } else if (status === 'paid') {
        r.style.display = (rowStatus === 'paid') ? '' : 'none';
      } else {
        r.style.display = (rowStatus === status) ? '' : 'none';
      }
    });
  }

  function filterVouchersTable() {
    const q = document.getElementById('voucher-search-input').value.toLowerCase().trim();
    const rows = document.querySelectorAll('.voucher-row');
    rows.forEach(r => {
      const text = r.innerText.toLowerCase();
      if (!q || text.includes(q)) {
        r.style.display = '';
      } else {
        r.style.display = 'none';
      }
    });
  }

  // Open 1"x1" Sticker View Modal
  function openStickerModal(v) {
    activeModalVoucher = v;
    const batch = v.batch_name || v.title || 'AARAMBH';
    const amt = v.amount || v.discount_amount || v.discount_percent || 0;
    const code = v.voucher_code;
    const claimUrl = `${baseUrl}/${code}`;

    document.getElementById('m-batch-name').innerText = batch;
    document.getElementById('m-amt').innerText = '₹' + amt;
    document.getElementById('m-code').innerText = code;
    document.getElementById('m-scan-url').innerText = claimUrl;
    document.getElementById('m-open-claim-btn').href = claimUrl;

    const qrTarget = document.getElementById('modal-qr-target');
    qrTarget.innerHTML = '';
    new QRCode(qrTarget, {
      text: claimUrl,
      width: 58,
      height: 58,
      colorDark: "#000000",
      colorLight: "#ffffff",
      correctLevel: QRCode.CorrectLevel.M
    });

    const giftBox = document.getElementById('m-gift-delivery-box');
    if (giftBox) {
      if (v.recipient_address || v.recipient_pincode) {
        giftBox.style.display = 'block';
        document.getElementById('m-gift-address').innerText = v.recipient_address || 'Address not specified';
        document.getElementById('m-gift-pincode').innerText = v.recipient_pincode || '---';
        document.getElementById('m-gift-city').innerText = v.recipient_city ? `${v.recipient_city}${v.recipient_state ? ', ' + v.recipient_state : ''}` : '';
      } else {
        giftBox.style.display = 'none';
      }
    }

    document.getElementById('stickerModal').style.display = 'flex';
  }

  function closeStickerModal() {
    document.getElementById('stickerModal').style.display = 'none';
  }

  function copyModalClaimUrl() {
    if (!activeModalVoucher) return;
    const url = `${baseUrl}/${activeModalVoucher.voucher_code}`;
    navigator.clipboard.writeText(url);
    if (window.UI && UI.showToast) {
      UI.showToast('Copied Claim URL', url, 'success');
    } else {
      alert('Copied Scan URL: ' + url);
    }
  }

  // Print Single Sticker from Row
  function printSingleRowSticker(v) {
    renderAndPrintStickers([v]);
  }

  function printModalSticker() {
    if (activeModalVoucher) {
      renderAndPrintStickers([activeModalVoucher]);
    }
  }

  // Print All Visible QR Codes onto single A4 Grid Sheet
  function printVisibleA4Grid() {
    const visibleRows = Array.from(document.querySelectorAll('.voucher-row')).filter(r => r.style.display !== 'none');
    if (visibleRows.length === 0) {
      alert('No vouchers visible to print.');
      return;
    }

    const ids = visibleRows.map(r => r.getAttribute('data-id')).filter(Boolean).join(',');
    const count = visibleRows.length;
    const cols = count <= 12 ? 4 : 5;
    // Open high-fidelity vector print preview with optimal columns
    const url = `{{ route('qr.exportPdf') }}?ids=${ids}&cols=${cols}&preview=1`;
    const win = window.open(url, '_blank');
    if (!win) {
      // If popup blocker intervened, render in-page table
      const vouchersList = visibleRows.map(r => ({
        id: r.getAttribute('data-id'),
        voucher_code: r.getAttribute('data-code'),
        batch_name: r.getAttribute('data-batch'),
        amount: r.getAttribute('data-amt'),
        claim_url: r.getAttribute('data-url')
      }));
      renderAndPrintStickers(vouchersList, cols);
    }
  }

  // Legacy alias for batchPrintAllStickers
  function batchPrintAllStickers() {
    printVisibleA4Grid();
  }

  // Multi-Select Checkboxes Functions
  function toggleSelectAllVouchers(master) {
    const boxes = document.querySelectorAll('.voucher-select-box');
    boxes.forEach(b => {
      const row = b.closest('.voucher-row');
      if (row && row.style.display !== 'none') {
        b.checked = master.checked;
      }
    });
    handleRowSelect();
  }

  function handleRowSelect() {
    const checkedBoxes = Array.from(document.querySelectorAll('.voucher-select-box:checked'));
    const bar = document.getElementById('selection-action-bar');
    const badge = document.getElementById('selected-count-badge');
    
    if (checkedBoxes.length > 0) {
      bar.style.display = 'flex';
      badge.innerText = checkedBoxes.length;
    } else {
      bar.style.display = 'none';
      const master = document.getElementById('check-all-vouchers');
      if (master) master.checked = false;
    }
  }

  function clearSelectedVouchers() {
    document.querySelectorAll('.voucher-select-box').forEach(b => b.checked = false);
    const master = document.getElementById('check-all-vouchers');
    if (master) master.checked = false;
    handleRowSelect();
  }

  function printSelectedA4() {
    const checkedBoxes = Array.from(document.querySelectorAll('.voucher-select-box:checked'));
    if (checkedBoxes.length === 0) {
      alert('Please select at least one QR voucher.');
      return;
    }

    const ids = checkedBoxes.map(b => b.value).join(',');
    const count = checkedBoxes.length;
    const cols = count <= 12 ? 4 : 5;
    // Open high-fidelity vector print preview in a clean new tab
    const url = `{{ route('qr.exportPdf') }}?ids=${ids}&cols=${cols}&preview=1`;
    const win = window.open(url, '_blank');
    if (!win) {
      // If popup blocker intervened, render in-page table
      const vouchersList = checkedBoxes.map(b => ({
        id: b.value,
        voucher_code: b.getAttribute('data-code'),
        batch_name: b.getAttribute('data-batch'),
        amount: b.getAttribute('data-amt'),
        claim_url: b.getAttribute('data-url')
      }));
      renderAndPrintStickers(vouchersList, cols);
    }
  }

  function exportSelectedPdf() {
    const checkedBoxes = Array.from(document.querySelectorAll('.voucher-select-box:checked'));
    if (checkedBoxes.length === 0) {
      alert('Please select at least one QR voucher to export.');
      return;
    }

    const ids = checkedBoxes.map(b => b.value).join(',');
    const count = checkedBoxes.length;
    const cols = count <= 12 ? 4 : 5;
    window.location.href = `{{ route('qr.exportPdf') }}?ids=${ids}&cols=${cols}`;
  }

  // Render High Density Multi-QR Grid on A4 Sheet (Auto-Columns - 4 for <=12, 5 for >12)
  function renderAndPrintStickers(vouchers, targetCols) {
    const printArea = document.getElementById('print-container-area');
    printArea.innerHTML = '';
    printArea.style.display = 'block';

    const totalCols = targetCols || (vouchers.length <= 12 ? 4 : 5);
    const cellH = totalCols <= 4 ? '44mm' : '38mm';
    const qrSize = totalCols <= 4 ? 26 : 23;

    const header = document.createElement('div');
    header.className = 'print-a4-header';
    header.innerHTML = `
      <h2>Aarambh Garments - QR Voucher Sheet</h2>
      <p>Total QRs: <strong>${vouchers.length}</strong> | Layout: <strong>${totalCols} Columns</strong> | Printed: ${new Date().toLocaleString('en-IN')}</p>
    `;
    printArea.appendChild(header);

    const totalRows = Math.ceil(vouchers.length / totalCols);

    const table = document.createElement('table');
    table.className = 'print-a4-table';
    printArea.appendChild(table);

    let vIdx = 0;
    for (let r = 0; r < totalRows; r++) {
      const tr = document.createElement('tr');
      for (let c = 0; c < totalCols; c++) {
        const td = document.createElement('td');
        td.style.width = (100 / totalCols) + '%';
        td.style.height = cellH;
        td.style.maxHeight = cellH;

        if (vIdx < vouchers.length) {
          const v = vouchers[vIdx];
          const batch = v.batch_name || v.title || 'AARAMBH';
          const amt = v.amount || v.discount_amount || v.discount_percent || 500;
          const code = v.voucher_code || '';
          const claimUrl = v.claim_url || `${baseUrl}/${code}`;
          const slotId = `print-qr-slot-${vIdx}`;

          td.innerHTML = `
            <div class="print-qr-card">
              <div class="cell-brand">${batch}</div>
              <div class="cell-qr-wrap" style="width:${qrSize}mm; height:${qrSize}mm;"><div class="cell-qr" id="${slotId}"></div></div>
              <div class="cell-amt">₹${Number(amt).toLocaleString('en-IN')}</div>
              <div class="cell-code">${code}</div>
            </div>
          `;
          tr.appendChild(td);

          setTimeout(((sId, url, qS) => () => {
            const slot = document.getElementById(sId);
            if (slot) {
              slot.innerHTML = '';
              new QRCode(slot, {
                text: url,
                width: 100,
                height: 100,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.M
              });
            }
          })(slotId, claimUrl, qrSize), 0);

          vIdx++;
        } else {
          td.innerHTML = '&nbsp;';
          td.style.border = 'none';
          tr.appendChild(td);
        }
      }
      table.appendChild(tr);
    }

    setTimeout(() => {
      window.print();
    }, 600);

    window.onafterprint = () => {
      printArea.style.display = 'none';
      printArea.innerHTML = '';
    };
  }

  // Open Edit Modal
  function openEditModal(v) {
    document.getElementById('edit_voucher_id').value = v.id;
    document.getElementById('edit_voucher_code').value = v.voucher_code;
    document.getElementById('edit_batch_name').value = v.batch_name || v.title || '';
    document.getElementById('edit_qr_date').value = v.qr_date ? v.qr_date.substring(0, 10) : (v.valid_from ? v.valid_from.substring(0, 10) : '');
    document.getElementById('edit_amount').value = v.amount || v.discount_amount || v.discount_percent || 500;
    
    const isPaid = (v.status === 'Paid' || (v.payment_status && v.payment_status.toLowerCase() === 'paid'));
    if (isPaid) {
      document.getElementById('edit_status').value = 'Paid';
      document.getElementById('edit_payment_status').value = 'Paid';
    } else {
      document.getElementById('edit_status').value = v.status || 'Active';
      document.getElementById('edit_payment_status').value = v.payment_status || 'Pending';
    }

    document.getElementById('editVoucherModal').style.display = 'flex';
  }

  function closeEditModal() {
    document.getElementById('editVoucherModal').style.display = 'none';
  }

  // ==========================================================================
  // MARK AS PAID HANDLERS
  // ==========================================================================
  let activePaidVoucher = null;

  function openMarkPaidModal(v) {
    activePaidVoucher = v;
    const amt = v.amount || v.discount_amount || v.discount_percent || 0;
    document.getElementById('pay_voucher_id').value = v.id;
    document.getElementById('pay_code').innerText = v.voucher_code;
    document.getElementById('pay_amount').innerText = '₹' + parseFloat(amt).toFixed(2);
    document.getElementById('pay_phone').innerText = v.customer_phone ? '+91 ' + v.customer_phone : '—';
    document.getElementById('pay_dest').innerText = v.recipient_upi_id || 'Direct / Cash';
    document.getElementById('pay_method').value = v.payment_method || 'UPI';
    document.getElementById('pay_txn_ref').value = v.upi_txn_ref || '';

    document.getElementById('markPaidModal').style.display = 'flex';
  }

  function closeMarkPaidModal() {
    document.getElementById('markPaidModal').style.display = 'none';
    activePaidVoucher = null;
  }

  async function submitMarkPaid() {
    if (!activePaidVoucher) return;
    const btn = document.getElementById('btn-confirm-paid');
    btn.disabled = true;
    btn.innerText = 'Updating...';

    const voucherId = document.getElementById('pay_voucher_id').value;
    const paymentMethod = document.getElementById('pay_method').value;
    const upiTxnRef = document.getElementById('pay_txn_ref').value.trim();
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    try {
      const res = await fetch(`{{ url('/qr/mark-paid') }}/${voucherId}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          payment_method: paymentMethod,
          upi_txn_ref: upiTxnRef
        })
      });

      const data = await res.json();
      btn.disabled = false;
      btn.innerText = 'Confirm Paid';

      if (data.success) {
        closeMarkPaidModal();
        if (window.UI && UI.showToast) {
          UI.showToast('Payment Marked Paid', data.message, 'success');
        }
        window.location.reload();
      } else {
        alert(data.message || 'Error updating payment status.');
      }
    } catch (err) {
      btn.disabled = false;
      btn.innerText = 'Confirm Paid';
      alert('Error updating payment status: ' + err.message);
    }
  }

  async function handleEditSubmit(e) {
    e.preventDefault();
    const id = document.getElementById('edit_voucher_id').value;
    const btn = document.getElementById('btn-save-edit');
    btn.disabled = true;
    btn.innerText = 'Saving...';

    const formData = new FormData(document.getElementById('edit-voucher-form'));
    
    try {
      const res = await fetch(`{{ url('/qr/voucher') }}/${id}/update`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        },
        body: formData
      });

      const data = await res.json();
      if (data.success) {
        if (window.UI && UI.showToast) {
          UI.showToast('Saved', data.message, 'success');
        } else {
          alert(data.message);
        }
        window.location.reload();
      } else {
        alert(data.message || 'Update failed');
        btn.disabled = false;
        btn.innerText = 'Save Changes';
      }
    } catch(err) {
      alert('Error updating voucher');
      btn.disabled = false;
      btn.innerText = 'Save Changes';
    }
  }

  async function handleExpireVoucher(form, code, e) {
    e.preventDefault();
    if (!confirm(`Are you sure you want to expire/revoke voucher ${code}?`)) return false;
    
    try {
      const res = await fetch(form.action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        }
      });
      const data = await res.json();
      if (data.success) {
        window.location.reload();
      }
    } catch(err) {
      form.submit();
    }
    return false;
  }

  async function handleReactivateVoucher(form, code, e) {
    e.preventDefault();
    try {
      const res = await fetch(form.action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        }
      });
      const data = await res.json();
      if (data.success) {
        window.location.reload();
      }
    } catch(err) {
      form.submit();
    }
    return false;
  }

  async function handleDeleteVoucher(form, code, e) {
    e.preventDefault();
    if (!confirm(`Permanently delete QR Voucher ${code}? This action cannot be undone.`)) return false;

    try {
      const res = await fetch(form.action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        },
        body: new FormData(form)
      });
      const data = await res.json();
      if (data.success) {
        window.location.reload();
      }
    } catch(err) {
      form.submit();
    }
    return false;
  }

  // Render QR Codes in Table Rows on load
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.list-qr-canvas').forEach(el => {
      const url = el.getAttribute('data-url');
      if (url) {
        new QRCode(el, {
          text: url,
          width: 38,
          height: 38,
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
