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
            <th style="text-align:right; width:130px;">Actions</th>
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

              <!-- Action Buttons -->
              <td style="text-align:right;">
                <div style="display:inline-flex; gap:6px; align-items:center;">
                  
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

@push('scripts')
<script>
  let currentStatusFilter = 'all';

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
