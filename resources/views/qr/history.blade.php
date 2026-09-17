@extends('layouts.app')

@section('title', 'Admin QR Vouchers & Expiry Ledger - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Barcodes & QR</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Admin QR & Expiry Ledger</span></div>
@endsection

@push('styles')
<style>
  /* ==========================================================================
     QR HISTORY & LEDGER - MODERN ERP STYLING
     ========================================================================== */
  .ledger-wrapper {
    display: flex;
    flex-direction: column;
    gap: 22px;
  }

  /* Stats Grid */
  .qr-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
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
    padding: 22px 24px;
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

  /* Status Filter Tabs */
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
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }

  .status-filter-btn.active {
    background: #ffffff;
    color: var(--primary-700, #4338ca);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }

  /* Code Mono Badge */
  .voucher-code-badge {
    font-family: var(--font-mono, monospace);
    font-weight: 800;
    font-size: 0.85rem;
    background: #f5f3ff;
    color: #6d28d9;
    border: 1px solid #ddd6fe;
    padding: 4px 10px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.15s ease;
  }

  .voucher-code-badge:hover {
    background: #ede9fe;
    border-color: #c4b5fd;
    transform: translateY(-1px);
  }

  /* Table Custom Row styling */
  .ledger-table th {
    background: #f8fafc;
    color: var(--slate-600, #475569);
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 12px 18px;
    border-bottom: 1px solid var(--slate-200, #e2e8f0);
  }

  .ledger-table td {
    padding: 14px 18px;
    vertical-align: middle;
    border-bottom: 1px solid var(--slate-100, #f1f5f9);
    font-size: 0.85rem;
  }

  .ledger-table tr:hover td {
    background: #fafafa;
  }

  /* Action Buttons */
  .action-icon-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
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

  /* Luxury Voucher Pass Styling for View Modal */
  .luxury-voucher-pass {
    position: relative;
    border-radius: 20px;
    padding: 24px 20px;
    color: #ffffff;
    overflow: hidden;
    box-shadow: 0 15px 35px -5px rgba(30, 27, 75, 0.35);
    transition: all 0.3s ease;
  }

  .theme-indigo { background: linear-gradient(135deg, #312e81 0%, #4338ca 50%, #6366f1 100%); }
  .theme-emerald { background: linear-gradient(135deg, #064e3b 0%, #047857 50%, #10b981 100%); }
  .theme-crimson { background: linear-gradient(135deg, #881337 0%, #be123c 50%, #f43f5e 100%); }
  .theme-onyx { background: linear-gradient(135deg, #090d16 0%, #1e293b 50%, #334155 100%); border: 1px solid rgba(255, 215, 0, 0.3); }
  .theme-amber { background: linear-gradient(135deg, #78350f 0%, #b45309 50%, #f59e0b 100%); }

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

  .copy-badge-btn {
    cursor: pointer;
    transition: background-color 0.2s;
  }

  .copy-badge-btn:hover {
    background: rgba(255, 255, 255, 0.25) !important;
  }

  /* Segmented Toggle for Edit Modal */
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
    padding: 9px 14px;
    border: none;
    background: transparent;
    border-radius: var(--radius-md, 8px);
    font-size: 0.825rem;
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
</style>
@endpush

@section('content')
<div class="ledger-wrapper">

  <!-- KPI Metrics Row -->
  <div class="qr-stats-grid">
    
    <!-- Total Vouchers -->
    <div class="qr-stat-card" style="--accent-color: #4f46e5;">
      <div class="qr-stat-icon" style="background: #eef2ff; color: #4f46e5;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path d="M12 3h.01"/></svg>
      </div>
      <div class="qr-stat-info">
        <div class="qr-stat-label">Total Vouchers Issued</div>
        <div class="qr-stat-value">
          {{ number_format($stats['total']) }}
          <span style="font-size:0.75rem; font-weight:600; color:var(--slate-500);">Tokens</span>
        </div>
      </div>
    </div>

    <!-- Active & Claimable -->
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

    <!-- Redeemed -->
    <div class="qr-stat-card" style="--accent-color: #8b5cf6;">
      <div class="qr-stat-icon" style="background: #f5f3ff; color: #7c3aed;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>
      </div>
      <div class="qr-stat-info">
        <div class="qr-stat-label">Claimed & Redeemed</div>
        <div class="qr-stat-value" style="color: #7c3aed;">
          {{ number_format($stats['redeemed']) }}
          <span style="font-size:0.75rem; font-weight:600; color:var(--slate-500);">Orders</span>
        </div>
      </div>
    </div>

    <!-- Expired -->
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
        <h3 style="margin:0; font-size:1.2rem; font-weight:800; color:var(--slate-900);">Admin QR Vouchers & Single-Use Ledger</h3>
        <p style="margin:4px 0 0; font-size:0.825rem; color:var(--slate-500);">
          Track promotional single-use security tokens, customer telephone bindings, redemption audits, and validity controls.
        </p>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('qr-table', 'QR_Vouchers_Ledger.csv')" style="display:inline-flex; align-items:center; gap:6px; font-weight:700;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Export CSV
        </button>

        <a href="{{ route('qr.generator') }}" class="btn btn-primary btn-sm" style="display:inline-flex; align-items:center; gap:6px; font-weight:700; box-shadow:0 2px 8px rgba(79, 70, 229, 0.3);">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
          Generate Single-Use QR
        </a>
      </div>
    </div>

    <!-- Quick Filter Toolbar -->
    <div class="qr-toolbar">
      <div class="search-input-wrapper">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="voucher-search-input" placeholder="Search code, phone, campaign, status..." onkeyup="filterVouchersTable()">
      </div>

      <div class="status-filter-pills">
        <button type="button" class="status-filter-btn active" onclick="setStatusFilter('all', this)">
          All ({{ $stats['total'] }})
        </button>
        <button type="button" class="status-filter-btn" onclick="setStatusFilter('active', this)">
          Active ({{ $stats['active'] }})
        </button>
        <button type="button" class="status-filter-btn" onclick="setStatusFilter('redeemed', this)">
          Redeemed ({{ $stats['redeemed'] }})
        </button>
        <button type="button" class="status-filter-btn" onclick="setStatusFilter('expired', this)">
          Expired ({{ $stats['expired'] }})
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="data-table ledger-table" id="qr-table" style="width:100%;">
        <thead>
          <tr>
            <th style="width:170px;">Voucher Code</th>
            <th>Campaign Title</th>
            <th>Audience / Locked Phone</th>
            <th>Discount Value</th>
            <th>Validity Period</th>
            <th>Status</th>
            <th>Redemption Audit</th>
            <th style="text-align:right; width:160px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($vouchers as $v)
            @php
              $isExpired = $v->valid_until && strtotime($v->valid_until) < strtotime(date('Y-m-d'));
              $statusKey = strtolower($v->status);
            @endphp
            <tr class="voucher-row" data-status="{{ $statusKey }}">
              
              <!-- Code with copy helper -->
              <td>
                <span class="voucher-code-badge" onclick="copyCode('{{ $v->voucher_code }}')" title="Click to copy code">
                  <span>{{ $v->voucher_code }}</span>
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                </span>
              </td>

              <!-- Campaign Title -->
              <td>
                <div style="font-weight:700; color:var(--slate-900);">{{ $v->title }}</div>
                <div style="font-size:0.725rem; color:var(--slate-500); margin-top:2px;">Single-Use Discount Promo</div>
              </td>

              <!-- Audience / Phone Lock -->
              <td>
                <div style="font-weight:700; color:var(--slate-800);">{{ $v->customer_name ?: 'General Promotion' }}</div>
                @if($v->customer_phone)
                  <div style="font-size:0.75rem; color:var(--primary-700); font-weight:600; display:inline-flex; align-items:center; gap:4px; margin-top:2px;">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                    +91 {{ $v->customer_phone }} (Locked)
                  </div>
                @else
                  <div style="font-size:0.75rem; color:var(--slate-400);">🌐 Open / Unrestricted</div>
                @endif
              </td>

              <!-- Discount Value & Threshold -->
              <td>
                <div style="font-weight:800; color:#059669; font-size:0.95rem;">
                  @if($v->discount_type === 'Percentage')
                    {{ $v->discount_percent }}% OFF
                  @else
                    ₹{{ number_format($v->discount_amount ?: $v->discount_percent) }} Flat
                  @endif
                </div>
                <div style="font-size:0.725rem; color:var(--slate-500); margin-top:2px;">
                  @if($v->max_discount_cap) Max Cap: ₹{{ number_format($v->max_discount_cap) }} @endif
                  @if($v->min_order_value) • Min Bill: ₹{{ number_format($v->min_order_value) }} @endif
                </div>
              </td>

              <!-- Validity Date -->
              <td>
                <div style="font-weight:600; color:var(--slate-800);">
                  {{ $v->valid_until ? date('d M Y', strtotime($v->valid_until)) : 'No Expiry' }}
                </div>
                <div style="font-size:0.725rem; color:var(--slate-500); margin-top:2px;">
                  From: {{ $v->valid_from ? date('d M Y', strtotime($v->valid_from)) : '—' }}
                </div>
              </td>

              <!-- Status Badge -->
              <td>
                @if($v->status === 'Active')
                  <span class="badge" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; font-weight:700; padding:4px 10px; border-radius:9999px;">
                    ● Active
                  </span>
                @elseif($v->status === 'Redeemed')
                  <span class="badge" style="background:#f5f3ff; color:#7c3aed; border:1px solid #ddd6fe; font-weight:700; padding:4px 10px; border-radius:9999px;">
                    ✓ Claimed
                  </span>
                @elseif($v->status === 'Expired' || $isExpired)
                  <span class="badge" style="background:#fff1f2; color:#e11d48; border:1px solid #fecdd3; font-weight:700; padding:4px 10px; border-radius:9999px;">
                    ✕ Expired
                  </span>
                @else
                  <span class="badge" style="background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; font-weight:700; padding:4px 10px; border-radius:9999px;">
                    {{ $v->status }}
                  </span>
                @endif
              </td>

              <!-- Redemption Audit Details -->
              <td>
                @if($v->is_redeemed || $v->redeemed_at)
                  <div style="font-weight:700; color:#7c3aed; font-size:0.8rem;">
                    {{ $v->redeemed_at ? (is_string($v->redeemed_at) ? $v->redeemed_at : $v->redeemed_at->format('d M Y, h:i A')) : 'Redeemed' }}
                  </div>
                  @if($v->redeemed_invoice_no)
                    <div style="font-family:var(--font-mono, monospace); font-size:0.725rem; color:var(--slate-600); margin-top:2px;">
                      Ref: {{ $v->redeemed_invoice_no }}
                    </div>
                  @endif
                @else
                  <span style="font-size:0.8rem; color:var(--slate-400);">— Awaiting Claim —</span>
                @endif
              </td>

              <!-- Action Buttons (View, Edit, Expire/Reactivate, Delete) -->
              <td style="text-align:right;">
                <div style="display:inline-flex; gap:6px; align-items:center;">
                  
                  <!-- View Voucher Modal Trigger -->
                  <button type="button" class="action-icon-btn info" title="View & Print Voucher Card" onclick='openViewModal(@json($v))'>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  </button>

                  <!-- Edit Voucher Modal Trigger -->
                  <button type="button" class="action-icon-btn primary" title="Edit Voucher" onclick='openEditModal(@json($v))'>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                  </button>

                  <!-- Expire / Reactivate Toggle -->
                  @if($v->status === 'Active')
                    <form action="{{ route('qr.expire', $v->id) }}" method="POST" style="display:inline;" onsubmit="return handleExpireVoucher(this, '{{ $v->voucher_code }}', event)">
                      @csrf
                      <button type="submit" class="action-icon-btn warning" title="Expire / Revoke Voucher">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                      </button>
                    </form>
                  @else
                    <form action="{{ route('qr.reactivate', $v->id) }}" method="POST" style="display:inline;" onsubmit="return handleReactivateVoucher(this, '{{ $v->voucher_code }}', event)">
                      @csrf
                      <button type="submit" class="action-icon-btn" title="Reactivate Voucher">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                      </button>
                    </form>
                  @endif

                  <!-- Delete Voucher -->
                  <form action="{{ route('qr.destroy', $v->id) }}" method="POST" style="display:inline;" onsubmit="return handleDeleteVoucher(this, '{{ $v->voucher_code }}', event)">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-icon-btn danger" title="Delete Voucher">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                    </button>
                  </form>

                </div>
              </td>

            </tr>
          @empty
            <tr>
              <td colspan="8" style="text-align:center; padding:48px 20px; color:var(--slate-400);">
                <div style="width:48px; height:48px; border-radius:12px; background:var(--slate-100); color:var(--slate-400); display:inline-flex; align-items:center; justify-content:center; margin-bottom:12px;">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/></svg>
                </div>
                <div style="font-weight:700; color:var(--slate-700); font-size:1rem; margin-bottom:4px;">No QR vouchers found</div>
                <p style="font-size:0.85rem; color:var(--slate-500); margin:0 0 16px;">Generate your first single-use discount QR code to begin tracking customer claims.</p>
                <a href="{{ route('qr.generator') }}" class="btn btn-primary btn-sm">+ Generate First QR Voucher</a>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>

<!-- ==========================================================================
     VIEW VOUCHER PASS MODAL
     ========================================================================== -->
<div class="modal-backdrop" id="viewVoucherModal" style="display:none;" onclick="if(event.target===this) closeViewModal()">
  <div class="modal-dialog modal-lg" style="max-width:820px;">
    
    <div class="modal-header">
      <div class="modal-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        <span>Voucher Details & Live Pass</span>
      </div>
      <button type="button" class="modal-close-btn" onclick="closeViewModal()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <div class="modal-body" style="padding:24px;">
      <div style="display:grid; grid-template-columns:360px 1fr; gap:24px; align-items:start;">
        
        <!-- LEFT: Luxury Live Voucher Pass Card -->
        <div>
          <div id="modal-voucher-card" class="luxury-voucher-pass theme-indigo">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
              <div>
                <div style="font-size:0.68rem; font-weight:800; text-transform:uppercase; letter-spacing:1.5px; opacity:0.85;">
                  GARMENT ERP • OFFICIAL PASS
                </div>
                <div style="font-size:1.05rem; font-weight:800; line-height:1.25; margin-top:2px;" id="modal-view-title">
                  Discount Voucher
                </div>
              </div>
              <div style="background:rgba(255,255,255,0.2); padding:4px 8px; border-radius:6px; font-size:0.65rem; font-weight:800; letter-spacing:0.5px; text-transform:uppercase;">
                PROMO
              </div>
            </div>

            <div style="margin-top:8px;">
              <span style="font-size:0.75rem; background:rgba(0,0,0,0.25); padding:3px 8px; border-radius:4px; font-weight:600; opacity:0.9;" id="modal-view-cust">
                Audience: General
              </span>
            </div>

            <div style="margin:14px 0 6px; text-align:center;">
              <div style="font-size:2.2rem; font-weight:900; letter-spacing:-0.03em; line-height:1;" id="modal-view-disc">
                15% OFF
              </div>
              <div style="font-size:0.75rem; opacity:0.9; margin-top:4px; font-weight:600;" id="modal-view-cap">
                Up to ₹2,500 on min. ₹1,500 bill
              </div>
            </div>

            <div class="perforation-line"></div>

            <div class="ticket-qr-container">
              <div id="modal-qr-container" style="display:flex; justify-content:center; align-items:center;"></div>
            </div>

            <div style="text-align:center; margin-top:12px;">
              <div class="copy-badge-btn" onclick="copyModalCode()" style="font-family:var(--font-mono, monospace); font-weight:800; font-size:1.05rem; letter-spacing:1px; background:rgba(0,0,0,0.3); padding:6px 14px; border-radius:8px; display:inline-flex; align-items:center; gap:6px;" title="Click to copy">
                <span id="modal-view-code">CODE-1234</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
              </div>
            </div>

            <div style="margin-top:12px; font-size:0.68rem; opacity:0.8; text-align:center; display:flex; justify-content:space-between; align-items:center;">
              <span id="modal-view-expiry">Valid until: 30 Days</span>
              <span id="modal-view-token-type">🔒 Single-Use Token</span>
            </div>
          </div>
        </div>

        <!-- RIGHT: Detailed Breakdown & Audit Logs -->
        <div style="display:flex; flex-direction:column; gap:16px;">
          
          <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px;">
            <div style="font-size:0.75rem; font-weight:800; color:var(--slate-500); text-transform:uppercase; margin-bottom:10px;">Security & Audience Locking</div>
            
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:0.85rem;">
              <div>
                <span style="color:var(--slate-500); font-size:0.75rem; display:block;">Target Customer:</span>
                <strong id="audit-cust-name" style="color:var(--slate-900);">—</strong>
              </div>
              <div>
                <span style="color:var(--slate-500); font-size:0.75rem; display:block;">Security Mobile Lock:</span>
                <strong id="audit-cust-phone" style="color:var(--primary-700); font-family:var(--font-mono, monospace);">+91 —</strong>
              </div>
              <div>
                <span style="color:var(--slate-500); font-size:0.75rem; display:block;">Current Status:</span>
                <span id="audit-status-badge">● Active</span>
              </div>
              <div>
                <span style="color:var(--slate-500); font-size:0.75rem; display:block;">Token Type:</span>
                <strong style="color:var(--slate-800);">Cryptographic Single-Use</strong>
              </div>
            </div>
          </div>

          <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px;">
            <div style="font-size:0.75rem; font-weight:800; color:var(--slate-500); text-transform:uppercase; margin-bottom:10px;">Discount Thresholds & Rules</div>
            
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:0.85rem;">
              <div>
                <span style="color:var(--slate-500); font-size:0.75rem; display:block;">Discount Mechanic:</span>
                <strong id="audit-disc-type" style="color:#059669;">—</strong>
              </div>
              <div>
                <span style="color:var(--slate-500); font-size:0.75rem; display:block;">Maximum Savings Cap:</span>
                <strong id="audit-max-cap" style="color:var(--slate-800);">₹—</strong>
              </div>
              <div>
                <span style="color:var(--slate-500); font-size:0.75rem; display:block;">Minimum Order Value:</span>
                <strong id="audit-min-bill" style="color:var(--slate-800);">₹—</strong>
              </div>
              <div>
                <span style="color:var(--slate-500); font-size:0.75rem; display:block;">Validity Window:</span>
                <strong id="audit-validity-range" style="color:var(--slate-800);">—</strong>
              </div>
            </div>
          </div>

          <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px;">
            <div style="font-size:0.75rem; font-weight:800; color:var(--slate-500); text-transform:uppercase; margin-bottom:10px;">Redemption Audit Trail</div>
            
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:0.85rem;">
              <div>
                <span style="color:var(--slate-500); font-size:0.75rem; display:block;">Redemption State:</span>
                <strong id="audit-redeemed-state" style="color:var(--slate-800);">Awaiting Claim</strong>
              </div>
              <div>
                <span style="color:var(--slate-500); font-size:0.75rem; display:block;">Claim Reference / Invoice:</span>
                <strong id="audit-claim-ref" style="font-family:var(--font-mono, monospace); color:var(--slate-800);">—</strong>
              </div>
              <div style="grid-column:span 2;">
                <span style="color:var(--slate-500); font-size:0.75rem; display:block;">Redemption Timestamp:</span>
                <span id="audit-redeemed-time" style="color:var(--slate-700); font-weight:600;">—</span>
              </div>
            </div>
          </div>

        </div>

      </div>
    </div>

    <div class="modal-footer" style="display:flex; justify-content:space-between; align-items:center;">
      <button type="button" class="btn btn-secondary" onclick="closeViewModal()">Close</button>
      
      <div style="display:flex; gap:10px; align-items:center;">
        <button type="button" class="btn btn-secondary" onclick="copyModalCode()" style="display:inline-flex; align-items:center; gap:6px;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
          Copy Code
        </button>

        <button type="button" class="btn btn-primary" onclick="printVoucherDirectly()" style="display:inline-flex; align-items:center; gap:6px;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
          Print Single-Page Voucher
        </button>

        <button type="button" class="btn btn-secondary" id="btn-edit-from-view" onclick="transitionToEditModal()" style="display:inline-flex; align-items:center; gap:6px;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          Edit Voucher
        </button>
      </div>
    </div>

  </div>
</div>

<!-- ==========================================================================
     EDIT VOUCHER MODAL
     ========================================================================== -->
<div class="modal-backdrop" id="editVoucherModal" style="display:none;" onclick="if(event.target===this) closeEditModal()">
  <div class="modal-dialog modal-lg" style="max-width:680px;">
    
    <div class="modal-header">
      <div class="modal-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        <span>Edit Discount QR Voucher: <strong id="edit-modal-code-title" style="color:var(--primary-700); font-family:var(--font-mono, monospace);"></strong></span>
      </div>
      <button type="button" class="modal-close-btn" onclick="closeEditModal()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <form id="edit-voucher-form" method="POST" action="">
      @csrf
      @method('PUT')
      <input type="hidden" id="edit_voucher_id" name="id">

      <div class="modal-body" style="padding:22px; max-height:calc(85vh - 120px); overflow-y:auto;">
        
        <div style="display:flex; flex-direction:column; gap:16px;">
          
          <!-- Campaign Title -->
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800);">Promotion Campaign Title <span style="color:#ef4444;">*</span></label>
            <input type="text" name="title" id="edit_title" class="form-control" required placeholder="e.g. VIP Summer Discount">
            <div class="preset-pills">
              <button type="button" class="preset-pill-btn" onclick="setEditCampaignTitle('Festive Garment Discount Voucher')">Festive Offer</button>
              <button type="button" class="preset-pill-btn" onclick="setEditCampaignTitle('VIP Customer Exclusive Privilege')">VIP Exclusive</button>
              <button type="button" class="preset-pill-btn" onclick="setEditCampaignTitle('First Purchase Welcome Discount')">Welcome Offer</button>
              <button type="button" class="preset-pill-btn" onclick="setEditCampaignTitle('End of Season Clearance Bonanza')">Clearance Sale</button>
            </div>
          </div>

          <!-- Customer Audience & Phone Lock -->
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800);">Select Customer Master</label>
              <select id="edit_registered_customer_select" class="form-control" onchange="onEditCustomerSelect(this)">
                <option value="">-- General / Custom Audience --</option>
                @if(isset($customers))
                  @foreach ($customers as $c)
                    <option value="{{ $c->name }}" data-phone="{{ $c->mobile ?? $c->phone ?? '' }}">{{ $c->name }} ({{ $c->mobile ?? 'No Mobile' }})</option>
                  @endforeach
                @endif
              </select>
            </div>

            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800);">Audience / Customer Name</label>
              <input type="text" name="customer_name" id="edit_cust_name" class="form-control" placeholder="Retail Customer Club">
            </div>
          </div>

          <!-- Customer Phone Security Lock -->
          <div class="form-group" style="margin-bottom:0;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800); margin:0;">
                Target Customer Mobile Phone <span style="font-size:0.75rem; font-weight:500; color:var(--slate-500);">(Security Lock)</span>
              </label>
              <span style="font-size:0.75rem; color:var(--primary-600); font-weight:600;">🔒 Locks voucher to phone</span>
            </div>
            <div style="display:flex; align-items:center; position:relative;">
              <span style="position:absolute; left:12px; font-weight:700; color:var(--slate-400); font-size:0.9rem;">+91</span>
              <input type="text" name="customer_phone" id="edit_cust_phone" class="form-control" style="padding-left:46px; font-family:var(--font-mono, monospace);" placeholder="9876543210">
            </div>
          </div>

          <!-- Discount Calculation Type -->
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800);">Discount Calculation Type <span style="color:#ef4444;">*</span></label>
            <input type="hidden" name="discount_type" id="edit_type" value="Percentage">
            <div class="discount-toggle-group">
              <button type="button" id="edit_btn_type_percent" class="discount-toggle-btn active" onclick="switchEditDiscountType('Percentage')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="5" x2="5" y2="19"/><circle cx="6.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/></svg>
                Percentage (% OFF)
              </button>
              <button type="button" id="edit_btn_type_flat" class="discount-toggle-btn" onclick="switchEditDiscountType('Flat')">
                <span style="font-size:1.1rem; font-weight:800; line-height:1;">₹</span>
                Flat Cash Discount
              </button>
            </div>
          </div>

          <!-- Dynamic Discount Value Inputs -->
          <div id="edit_group_percent">
            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800);">Discount Percentage (%) <span style="color:#ef4444;">*</span></label>
              <div style="position:relative;">
                <input type="number" step="0.5" min="1" max="100" name="discount_percent" id="edit_percent" class="form-control" value="15" style="font-weight:700; font-size:1.05rem;">
                <span style="position:absolute; right:14px; top:50%; transform:translateY(-50%); font-weight:800; color:var(--slate-400);">%</span>
              </div>
              <div class="preset-pills">
                <button type="button" class="preset-pill-btn" onclick="setEditPercent(10)">10%</button>
                <button type="button" class="preset-pill-btn" onclick="setEditPercent(15)">15%</button>
                <button type="button" class="preset-pill-btn" onclick="setEditPercent(20)">20%</button>
                <button type="button" class="preset-pill-btn" onclick="setEditPercent(25)">25%</button>
                <button type="button" class="preset-pill-btn" onclick="setEditPercent(50)">50%</button>
              </div>
            </div>
          </div>

          <div id="edit_group_amount" style="display:none;">
            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800);">Flat Discount Amount (₹) <span style="color:#ef4444;">*</span></label>
              <div style="position:relative;">
                <input type="number" step="10" min="1" name="discount_amount" id="edit_amount" class="form-control" value="500" style="font-weight:700; font-size:1.05rem;" disabled>
                <span style="position:absolute; right:14px; top:50%; transform:translateY(-50%); font-weight:800; color:var(--slate-400);">₹</span>
              </div>
              <div class="preset-pills">
                <button type="button" class="preset-pill-btn" onclick="setEditAmount(200)">₹200</button>
                <button type="button" class="preset-pill-btn" onclick="setEditAmount(500)">₹500</button>
                <button type="button" class="preset-pill-btn" onclick="setEditAmount(1000)">₹1,000</button>
                <button type="button" class="preset-pill-btn" onclick="setEditAmount(2000)">₹2,000</button>
              </div>
            </div>
          </div>

          <!-- Cap & Min Bill -->
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800);">Max Discount Cap (₹)</label>
              <input type="number" step="100" name="max_discount_cap" id="edit_cap" class="form-control" value="2500">
            </div>

            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800);">Min. Bill Value (₹)</label>
              <input type="number" step="100" name="min_order_value" id="edit_min_bill" class="form-control" value="1500">
            </div>
          </div>

          <!-- Date Ranges -->
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800);">Valid From</label>
              <input type="date" name="valid_from" id="edit_valid_from" class="form-control">
            </div>

            <div class="form-group" style="margin-bottom:0;">
              <label class="form-label" style="font-weight:700; color:var(--slate-800);">Valid Until (Expiry)</label>
              <input type="date" name="valid_until" id="edit_valid_until" class="form-control">
            </div>
          </div>

          <div class="preset-pills">
            <span style="font-size:0.75rem; color:var(--slate-500); align-self:center; margin-right:4px;">Quick Extend:</span>
            <button type="button" class="preset-pill-btn" onclick="addDaysToEditExpiry(7)">+7 Days</button>
            <button type="button" class="preset-pill-btn" onclick="addDaysToEditExpiry(15)">+15 Days</button>
            <button type="button" class="preset-pill-btn" onclick="addDaysToEditExpiry(30)">+30 Days</button>
            <button type="button" class="preset-pill-btn" onclick="addDaysToEditExpiry(60)">+60 Days</button>
          </div>

          <!-- Status Selector -->
          <div class="form-group" style="margin-bottom:0;">
            <label class="form-label" style="font-weight:700; color:var(--slate-800);">Voucher Status <span style="color:#ef4444;">*</span></label>
            <select name="status" id="edit_status" class="form-control" style="font-weight:700;">
              <option value="Active">● Active (Claimable)</option>
              <option value="Expired">✕ Expired (Revoked)</option>
              <option value="Redeemed">✓ Redeemed (Used)</option>
            </select>
          </div>

        </div>

      </div>

      <div class="modal-footer" style="display:flex; justify-content:space-between; align-items:center;">
        <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
        <button type="submit" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:8px; font-weight:800; box-shadow:0 2px 10px rgba(79, 70, 229, 0.3);">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          Save Changes
        </button>
      </div>

    </form>

  </div>
</div>

<!-- QRCode Library CDN for crisp client-side QR generation in modal -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

@push('scripts')
<script>
  let currentStatusFilter = 'all';
  let activeViewVoucher = null;

  function copyCode(code) {
    navigator.clipboard.writeText(code).then(() => {
      if (window.Toast) {
        Toast.fire({
          icon: 'success',
          title: `Voucher Code ${code} copied!`
        });
      } else if (window.Swal) {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: 'success',
          title: `Voucher Code ${code} copied!`,
          showConfirmButton: false,
          timer: 2000
        });
      }
    });
  }

  function copyModalCode() {
    if (activeViewVoucher) {
      copyCode(activeViewVoucher.voucher_code);
    }
  }

  /* ==========================================================================
     VIEW VOUCHER MODAL CONTROLLER
     ========================================================================== */
  function openViewModal(v) {
    activeViewVoucher = v;

    document.getElementById('modal-view-title').innerText = v.title || 'Special Discount Voucher';
    document.getElementById('modal-view-cust').innerText = 'Audience: ' + (v.customer_name || 'General Promotion');
    document.getElementById('modal-view-code').innerText = v.voucher_code;

    const isPercent = v.discount_type === 'Percentage';
    if (isPercent) {
      document.getElementById('modal-view-disc').innerText = (v.discount_percent || 15) + '% OFF';
      document.getElementById('modal-view-cap').innerText = `Up to ₹${Number(v.max_discount_cap || 2500).toLocaleString('en-IN')} on min. ₹${Number(v.min_order_value || 1500).toLocaleString('en-IN')} bill`;
    } else {
      const amt = v.discount_amount || v.discount_percent || 500;
      document.getElementById('modal-view-disc').innerText = '₹' + Number(amt).toLocaleString('en-IN') + ' FLAT OFF';
      document.getElementById('modal-view-cap').innerText = `Flat discount on min. ₹${Number(v.min_order_value || 1500).toLocaleString('en-IN')} bill`;
    }

    if (v.valid_until) {
      const expDate = new Date(v.valid_until);
      document.getElementById('modal-view-expiry').innerText = 'Valid until: ' + expDate.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    } else {
      document.getElementById('modal-view-expiry').innerText = 'Valid indefinitely';
    }

    // Security & Audience Audit
    document.getElementById('audit-cust-name').innerText = v.customer_name || 'General Promotion';
    document.getElementById('audit-cust-phone').innerText = v.customer_phone ? `+91 ${v.customer_phone}` : '🌐 Unlocked (Open)';
    
    // Status Badge
    const statusBadgeElem = document.getElementById('audit-status-badge');
    if (v.status === 'Active') {
      statusBadgeElem.innerHTML = '<span class="badge" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; font-weight:700; padding:3px 8px; border-radius:6px;">● Active</span>';
    } else if (v.status === 'Redeemed') {
      statusBadgeElem.innerHTML = '<span class="badge" style="background:#f5f3ff; color:#7c3aed; border:1px solid #ddd6fe; font-weight:700; padding:3px 8px; border-radius:6px;">✓ Redeemed</span>';
    } else {
      statusBadgeElem.innerHTML = '<span class="badge" style="background:#fff1f2; color:#e11d48; border:1px solid #fecdd3; font-weight:700; padding:3px 8px; border-radius:6px;">✕ ' + v.status + '</span>';
    }

    // Rules Audit
    document.getElementById('audit-disc-type').innerText = isPercent ? `${v.discount_percent}% Percentage` : `₹${v.discount_amount || v.discount_percent} Flat Cash`;
    document.getElementById('audit-max-cap').innerText = v.max_discount_cap ? `₹${Number(v.max_discount_cap).toLocaleString('en-IN')}` : 'No Limit';
    document.getElementById('audit-min-bill').innerText = v.min_order_value ? `₹${Number(v.min_order_value).toLocaleString('en-IN')}` : '₹0';
    document.getElementById('audit-validity-range').innerText = (v.valid_from ? new Date(v.valid_from).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : '—') + ' → ' + (v.valid_until ? new Date(v.valid_until).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : 'Indefinite');

    // Redemption Audit
    document.getElementById('audit-redeemed-state').innerText = v.is_redeemed || v.status === 'Redeemed' ? 'Claimed & Redeemed' : 'Awaiting Claim';
    document.getElementById('audit-claim-ref').innerText = v.redeemed_invoice_no || '—';
    document.getElementById('audit-redeemed-time').innerText = v.redeemed_at ? (typeof v.redeemed_at === 'string' ? v.redeemed_at : new Date(v.redeemed_at).toLocaleString('en-IN')) : '—';

    // Render QR Code
    renderModalQrCode(v.voucher_code);

    // Open Backdrop
    const modal = document.getElementById('viewVoucherModal');
    modal.style.display = 'flex';
    setTimeout(() => modal.classList.add('open'), 10);
  }

  function closeViewModal() {
    const modal = document.getElementById('viewVoucherModal');
    modal.classList.remove('open');
    setTimeout(() => { modal.style.display = 'none'; }, 200);
  }

  function transitionToEditModal() {
    if (activeViewVoucher) {
      const v = activeViewVoucher;
      closeViewModal();
      setTimeout(() => openEditModal(v), 250);
    }
  }

  function renderModalQrCode(code) {
    const container = document.getElementById('modal-qr-container');
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
        container.innerHTML = `<img src="https://api.qrserver.com/v1/create-qr-code/?size=130x130&data=${encodeURIComponent(code)}" alt="QR Code" style="width:130px; height:130px; border-radius:6px; display:block;">`;
      }
    } else {
      container.innerHTML = `<img src="https://api.qrserver.com/v1/create-qr-code/?size=130x130&data=${encodeURIComponent(code)}" alt="QR Code" style="width:130px; height:130px; border-radius:6px; display:block;">`;
    }
  }

  /* ==========================================================================
     SINGLE-PAGE VOUCHER PASS PRINT ENGINE
     ========================================================================== */
  function printVoucherDirectly() {
    if (!activeViewVoucher) return;
    const v = activeViewVoucher;

    let qrImgSrc = '';
    const qrCanvas = document.querySelector('#modal-qr-container canvas');
    const qrImg = document.querySelector('#modal-qr-container img');
    if (qrCanvas) {
      try { qrImgSrc = qrCanvas.toDataURL('image/png'); } catch (e) { if (qrImg) qrImgSrc = qrImg.src; }
    } else if (qrImg) {
      qrImgSrc = qrImg.src;
    }

    const title = v.title || 'Festive Garment Discount Voucher';
    const cust = 'Audience: ' + (v.customer_name || 'Retail Customer Club');
    const disc = v.discount_type === 'Percentage' ? `${v.discount_percent || 15}% OFF` : `₹${Number(v.discount_amount || v.discount_percent || 500).toLocaleString('en-IN')} FLAT OFF`;
    const cap = v.discount_type === 'Percentage' ? `Up to ₹${Number(v.max_discount_cap || 2500).toLocaleString('en-IN')} on min. ₹${Number(v.min_order_value || 1500).toLocaleString('en-IN')} bill` : `Flat discount on min. ₹${Number(v.min_order_value || 1500).toLocaleString('en-IN')} bill`;
    const code = v.voucher_code;
    const expiry = v.valid_until ? ('Valid until: ' + new Date(v.valid_until).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })) : 'Valid indefinitely';

    let printFrame = document.getElementById('history-print-iframe');
    if (printFrame) printFrame.remove();

    printFrame = document.createElement('iframe');
    printFrame.id = 'history-print-iframe';
    printFrame.style.position = 'fixed';
    printFrame.style.top = '-9999px';
    printFrame.style.left = '-9999px';
    printFrame.style.width = '0';
    printFrame.style.height = '0';
    printFrame.style.border = '0';
    document.body.appendChild(printFrame);

    const doc = printFrame.contentWindow.document;
    doc.open();
    doc.write(`
      <!DOCTYPE html>
      <html lang="en">
      <head>
        <meta charset="UTF-8">
        <title>${title} - ${code}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">
        <style>
          @page {
            size: portrait;
            margin: 15mm auto;
          }
          * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
          }
          html, body {
            margin: 0;
            padding: 0;
            background: #ffffff;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100%;
          }
          .print-wrapper {
            width: 370px;
            margin: 10px auto;
            page-break-inside: avoid;
            break-inside: avoid;
          }
          .luxury-voucher-pass {
            position: relative;
            border-radius: 20px;
            padding: 24px 20px;
            color: #ffffff;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.25);
            background: linear-gradient(135deg, #312e81 0%, #4338ca 50%, #6366f1 100%) !important;
            page-break-inside: avoid;
            break-inside: avoid;
          }
          .perforation-line {
            position: relative;
            margin: 18px -20px;
            border-top: 2px dashed rgba(255, 255, 255, 0.4);
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
            background: #ffffff !important;
            border-radius: 50%;
            top: -11px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.15);
          }
          .perforation-line::before { left: -11px; }
          .perforation-line::after { right: -11px; }

          .ticket-qr-container {
            background: #ffffff !important;
            border-radius: 14px;
            padding: 12px;
            width: 154px;
            height: 154px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
          }
          .ticket-qr-container img {
            width: 130px !important;
            height: 130px !important;
            display: block !important;
          }
        </style>
      </head>
      <body>
        <div class="print-wrapper">
          <div class="luxury-voucher-pass">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
              <div>
                <div style="font-size:0.68rem; font-weight:800; text-transform:uppercase; letter-spacing:1.5px; opacity:0.85;">
                  GARMENT ERP • OFFICIAL PASS
                </div>
                <div style="font-size:1.1rem; font-weight:800; line-height:1.25; margin-top:2px;">
                  ${title}
                </div>
              </div>
              <div style="background:rgba(255,255,255,0.2); padding:4px 8px; border-radius:6px; font-size:0.65rem; font-weight:800; letter-spacing:0.5px; text-transform:uppercase;">
                PROMO
              </div>
            </div>

            <div style="margin-top:8px;">
              <span style="font-size:0.75rem; background:rgba(0,0,0,0.25); padding:3px 8px; border-radius:4px; font-weight:600; opacity:0.9;">
                ${cust}
              </span>
            </div>

            <div style="margin:16px 0 6px; text-align:center;">
              <div style="font-size:2.4rem; font-weight:900; letter-spacing:-0.03em; line-height:1;">
                ${disc}
              </div>
              <div style="font-size:0.75rem; opacity:0.9; margin-top:4px; font-weight:600;">
                ${cap}
              </div>
            </div>

            <div class="perforation-line"></div>

            <div class="ticket-qr-container">
              ${qrImgSrc ? `<img src="${qrImgSrc}" alt="QR Code">` : ''}
            </div>

            <div style="text-align:center; margin-top:12px;">
              <div style="font-family:'JetBrains Mono', monospace; font-weight:800; font-size:1.05rem; letter-spacing:1px; background:rgba(0,0,0,0.3); padding:6px 14px; border-radius:8px; display:inline-flex; align-items:center; gap:6px;">
                <span>${code}</span>
              </div>
            </div>

            <div style="margin-top:12px; font-size:0.68rem; opacity:0.8; text-align:center; display:flex; justify-content:space-between; align-items:center;">
              <span>${expiry}</span>
              <span>🔒 Single-Use Token</span>
            </div>
          </div>
        </div>
      </body>
      </html>
    `);
    doc.close();

    setTimeout(() => {
      printFrame.contentWindow.focus();
      printFrame.contentWindow.print();
    }, 250);
  }

  /* ==========================================================================
     EDIT VOUCHER MODAL CONTROLLER
     ========================================================================== */
  function openEditModal(v) {
    document.getElementById('edit_voucher_id').value = v.id;
    document.getElementById('edit-modal-code-title').innerText = v.voucher_code;
    document.getElementById('edit_title').value = v.title || '';
    document.getElementById('edit_cust_name').value = v.customer_name || '';
    document.getElementById('edit_cust_phone').value = (v.customer_phone || '').replace('+91', '').trim();
    
    // Select registered customer if matches
    const custSelect = document.getElementById('edit_registered_customer_select');
    if (custSelect) {
      custSelect.value = v.customer_name || '';
    }

    // Discount Type & Values
    const isPercent = (v.discount_type === 'Percentage');
    switchEditDiscountType(isPercent ? 'Percentage' : 'Flat');
    if (isPercent) {
      document.getElementById('edit_percent').value = v.discount_percent || 15;
    } else {
      document.getElementById('edit_amount').value = v.discount_amount || v.discount_percent || 500;
    }

    document.getElementById('edit_cap').value = v.max_discount_cap || 2500;
    document.getElementById('edit_min_bill').value = v.min_order_value || 1500;
    
    if (v.valid_from) {
      document.getElementById('edit_valid_from').value = v.valid_from.substring(0, 10);
    }
    if (v.valid_until) {
      document.getElementById('edit_valid_until').value = v.valid_until.substring(0, 10);
    }

    document.getElementById('edit_status').value = v.status || 'Active';

    // Set Form Action Route
    const form = document.getElementById('edit-voucher-form');
    form.action = `/qr/voucher/${v.id}`;

    // Open Backdrop
    const modal = document.getElementById('editVoucherModal');
    modal.style.display = 'flex';
    setTimeout(() => modal.classList.add('open'), 10);
  }

  function closeEditModal() {
    const modal = document.getElementById('editVoucherModal');
    modal.classList.remove('open');
    setTimeout(() => { modal.style.display = 'none'; }, 200);
  }

  function switchEditDiscountType(type) {
    document.getElementById('edit_type').value = type;
    const btnPercent = document.getElementById('edit_btn_type_percent');
    const btnFlat = document.getElementById('edit_btn_type_flat');
    const groupPercent = document.getElementById('edit_group_percent');
    const groupAmount = document.getElementById('edit_group_amount');
    const inputPercent = document.getElementById('edit_percent');
    const inputAmount = document.getElementById('edit_amount');

    if (type === 'Percentage') {
      btnPercent.classList.add('active');
      btnFlat.classList.remove('active');
      groupPercent.style.display = 'block';
      groupAmount.style.display = 'none';
      if (inputPercent) inputPercent.disabled = false;
      if (inputAmount) inputAmount.disabled = true;
    } else {
      btnFlat.classList.add('active');
      btnPercent.classList.remove('active');
      groupPercent.style.display = 'none';
      groupAmount.style.display = 'block';
      if (inputPercent) inputPercent.disabled = true;
      if (inputAmount) inputAmount.disabled = false;
    }
  }

  function setEditPercent(val) {
    document.getElementById('edit_percent').value = val;
  }

  function setEditAmount(val) {
    document.getElementById('edit_amount').value = val;
  }

  function setEditCampaignTitle(val) {
    document.getElementById('edit_title').value = val;
  }

  function onEditCustomerSelect(selectElem) {
    const selected = selectElem.options[selectElem.selectedIndex];
    if (selectElem.value) {
      document.getElementById('edit_cust_name').value = selectElem.value;
      const phone = selected.getAttribute('data-phone') || '';
      document.getElementById('edit_cust_phone').value = phone.replace('+91', '').trim();
    }
  }

  function addDaysToEditExpiry(days) {
    const date = new Date();
    date.setDate(date.getDate() + days);
    const yyyy = date.getFullYear();
    const mm = String(date.getMonth() + 1).padStart(2, '0');
    const dd = String(date.getDate()).padStart(2, '0');
    document.getElementById('edit_valid_until').value = `${yyyy}-${mm}-${dd}`;
  }

  /* ==========================================================================
     EXPIRE / REACTIVATE / DELETE HANDLERS
     ========================================================================== */
  function handleExpireVoucher(form, code, e) {
    e.preventDefault();
    Swal.fire({
      title: 'Expire Voucher?',
      html: `Are you sure you want to expire single-use voucher <strong>${code}</strong>?<br><span style="font-size:0.85rem; color:#64748b;">It will be locked and can no longer be redeemed.</span>`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#f59e0b',
      cancelButtonColor: '#94a3b8',
      confirmButtonText: 'Yes, Expire Voucher',
      cancelButtonText: 'Cancel',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        form.submit();
      }
    });
    return false;
  }

  function handleReactivateVoucher(form, code, e) {
    e.preventDefault();
    Swal.fire({
      title: 'Reactivate Voucher?',
      html: `Make single-use voucher <strong>${code}</strong> active and claimable again?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#10b981',
      cancelButtonColor: '#94a3b8',
      confirmButtonText: 'Yes, Reactivate',
      cancelButtonText: 'Cancel',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        form.submit();
      }
    });
    return false;
  }

  function handleDeleteVoucher(form, code, e) {
    e.preventDefault();
    Swal.fire({
      title: 'Permanently Delete?',
      html: `Delete voucher <strong>${code}</strong> from database?<br><span style="font-size:0.85rem; color:#ef4444;">This audit record will be completely removed.</span>`,
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#94a3b8',
      confirmButtonText: 'Yes, Delete Permanently',
      cancelButtonText: 'Cancel',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        form.submit();
      }
    });
    return false;
  }

  function setStatusFilter(status, btnElem) {
    currentStatusFilter = status;
    document.querySelectorAll('.status-filter-btn').forEach(btn => btn.classList.remove('active'));
    if (btnElem) btnElem.classList.add('active');
    filterVouchersTable();
  }

  function filterVouchersTable() {
    const query = (document.getElementById('voucher-search-input').value || '').toLowerCase().trim();
    const rows = document.querySelectorAll('#qr-table tbody tr.voucher-row');

    rows.forEach(row => {
      const rowStatus = (row.getAttribute('data-status') || '').toLowerCase();
      const text = row.innerText.toLowerCase();

      const matchesStatus = (currentStatusFilter === 'all') || (rowStatus === currentStatusFilter);
      const matchesQuery = !query || text.includes(query);

      if (matchesStatus && matchesQuery) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }
</script>
@endpush
@endsection
