@extends('layouts.app')

@section('title', 'Purchase Ledger (Tally-Style) - aarambh')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Purchasing</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Purchase Ledger</span></div>
@endsection

@push('styles')
<style>
  .tally-card {
    background: #ffffff;
    border-radius: var(--radius-xl, 16px);
    border: 1px solid var(--slate-200, #e2e8f0);
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    overflow: hidden;
  }
  .tally-header {
    background: #0f172a;
    color: #ffffff;
    padding: 20px 24px;
    border-bottom: 2px solid #334155;
  }
  .tally-filter-bar {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 16px 24px;
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    align-items: center;
    justify-content: space-between;
  }
  .tally-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
    background: #ffffff;
  }
  .tally-table th {
    background: #f1f5f9;
    color: #334155;
    font-weight: 800;
    text-transform: uppercase;
    font-size: 0.775rem;
    letter-spacing: 0.05em;
    padding: 12px 20px;
    border-top: 1px solid #e2e8f0;
    border-bottom: 2px solid #cbd5e1;
  }
  .tally-table td {
    padding: 11px 20px;
    border-bottom: 1px solid #f1f5f9;
    color: #1e293b;
    vertical-align: middle;
  }
  .tally-table tbody tr:hover {
    background-color: #f8fafc;
  }
  .tally-table tfoot td {
    background: #f8fafc;
    border-top: 2px solid #cbd5e1;
    border-bottom: 2px solid #94a3b8;
    padding: 14px 20px;
    font-weight: 800;
  }
  .tally-amt {
    text-align: right;
    font-family: var(--font-mono, monospace);
    font-weight: 700;
  }
  .tally-pill-btn {
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #475569;
    padding: 5px 12px;
    border-radius: 6px;
    font-size: 0.775rem;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s ease;
  }
  .tally-pill-btn:hover, .tally-pill-btn.active {
    background: #4338ca;
    border-color: #4338ca;
    color: #ffffff;
  }
  .tally-hdr-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    font-size: 0.8125rem;
    font-weight: 700;
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none !important;
    transition: all 0.15s ease;
    line-height: 1.25;
  }
  .tally-hdr-btn-primary {
    background: #6366f1;
    color: #ffffff !important;
    border: 1px solid #4f46e5;
    box-shadow: 0 2px 6px rgba(99, 102, 241, 0.4);
  }
  .tally-hdr-btn-primary:hover {
    background: #4f46e5;
    color: #ffffff !important;
  }
  .tally-hdr-btn-light {
    background: #ffffff;
    color: #0f172a !important;
    border: 1px solid #cbd5e1;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }
  .tally-hdr-btn-light:hover {
    background: #f1f5f9;
    color: #0f172a !important;
  }
  @media print {
    .app-sidebar, .app-header, .tally-filter-bar, .tally-actions, .breadcrumb {
      display: none !important;
    }
    .app-content {
      padding: 0 !important;
      margin: 0 !important;
    }
    .tally-card {
      border: none !important;
      box-shadow: none !important;
    }
    .tally-header {
      background: #ffffff !important;
      color: #000000 !important;
      border-bottom: 2px solid #000000 !important;
    }
    .tally-table th {
      background: #f1f5f9 !important;
      color: #000000 !important;
      border-bottom: 2px solid #000000 !important;
    }
    .tally-table tfoot td {
      border-top: 2px solid #000000 !important;
      border-bottom: 2px solid #000000 !important;
    }
  }
</style>
@endpush

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <!-- Main Ledger Card Container -->
  <div class="tally-card">
    
    <!-- Tally-Style Top Banner: Party Name & Reporting Period -->
    <div class="tally-header">
      <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px;">
        <div>
          <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.08em; color:#94a3b8; font-weight:700; margin-bottom:4px;">
            Accounting Statement &bull; Purchase Account
          </div>
          <h2 style="margin:0; font-size:1.45rem; font-weight:800; color:#ffffff; display:flex; align-items:center; gap:10px;">
            <span>{{ $selectedVendor ? $selectedVendor->name : 'All Suppliers (Consolidated)' }}</span>
            @if($selectedVendor && $selectedVendor->code)
              <span style="font-size:0.8rem; background:rgba(255,255,255,0.15); color:#cbd5e1; padding:2px 8px; border-radius:6px; font-weight:700; font-family:monospace;">
                {{ $selectedVendor->code }}
              </span>
            @endif
          </h2>

          <div style="margin-top:8px; font-size:0.825rem; color:#cbd5e1; display:flex; flex-wrap:wrap; gap:16px; align-items:center;">
            <div>
              Reporting Period: 
              <strong style="color:#ffffff;">
                @if($fromDate && $toDate)
                  {{ date('d-M-Y', strtotime($fromDate)) }} to {{ date('d-M-Y', strtotime($toDate)) }}
                @elseif($fromDate)
                  From {{ date('d-M-Y', strtotime($fromDate)) }}
                @elseif($toDate)
                  Up to {{ date('d-M-Y', strtotime($toDate)) }}
                @else
                  All Dates (Complete History)
                @endif
              </strong>
            </div>

            @if($selectedVendor && $selectedVendor->gstin)
              <div>GSTIN: <strong style="color:#ffffff; font-family:monospace;">{{ $selectedVendor->gstin }}</strong></div>
            @endif

            @if($selectedVendor && $selectedVendor->city)
              <div>Location: <strong style="color:#ffffff;">{{ $selectedVendor->city }}{{ $selectedVendor->state ? ', ' . $selectedVendor->state : '' }}</strong></div>
            @endif
          </div>
        </div>

        <!-- Action Toolbar -->
        <div class="tally-actions" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
          @if($selectedVendor)
            <a href="{{ route('purchase.payment-mapping', ['vendor_id' => $selectedVendor->id]) }}" class="tally-hdr-btn tally-hdr-btn-primary">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
              <span>Payment Mapping</span>
            </a>
          @else
            <a href="{{ route('purchase.payment-mapping') }}" class="tally-hdr-btn tally-hdr-btn-primary">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
              <span>Open Payment Mapping</span>
            </a>
          @endif

          <button type="button" class="tally-hdr-btn tally-hdr-btn-light" onclick="window.print()">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
            <span>Print</span>
          </button>

          <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="tally-hdr-btn tally-hdr-btn-light">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            <span>Export CSV</span>
          </a>
        </div>
      </div>
    </div>

    <!-- Filter Bar: Party & Date Selection -->
    <div class="tally-filter-bar">
      <form method="GET" action="{{ route('purchase.ledger') }}" id="ledger-filter-form" style="display:flex; flex-wrap:wrap; gap:12px; align-items:center; width:100%;">
        
        <!-- Supplier / Party Filter -->
        <div style="min-width:240px; flex:1; max-width:340px;">
          <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">
            Select Party / Supplier:
          </label>
          <select name="vendor_id" class="form-control" style="font-weight:700; height:38px; border-radius:8px;" onchange="document.getElementById('ledger-filter-form').submit()">
            <option value="">-- All Suppliers / Parties --</option>
            @foreach($vendors as $v)
              <option value="{{ $v->id }}" {{ $selectedVendorId == $v->id ? 'selected' : '' }}>
                {{ $v->name }} ({{ $v->code ?: 'Vendor' }})
              </option>
            @endforeach
          </select>
        </div>

        <!-- Date Range: From Date -->
        <div style="width:150px;">
          <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">
            From Date:
          </label>
          <input type="date" name="from_date" value="{{ $fromDate }}" class="form-control" style="height:38px; font-weight:600; border-radius:8px;" onchange="document.getElementById('ledger-filter-form').submit()">
        </div>

        <!-- Date Range: To Date -->
        <div style="width:150px;">
          <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">
            To Date:
          </label>
          <input type="date" name="to_date" value="{{ $toDate }}" class="form-control" style="height:38px; font-weight:600; border-radius:8px;" onchange="document.getElementById('ledger-filter-form').submit()">
        </div>

        <!-- Preset Period Buttons -->
        <div style="display:flex; flex-direction:column; gap:4px;">
          <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569;">
            Period Presets:
          </label>
          <div style="display:flex; gap:6px; align-items:center;">
            <a href="{{ route('purchase.ledger', array_merge(request()->except(['from_date', 'to_date', 'preset']), ['preset' => 'fy', 'vendor_id' => $selectedVendorId])) }}" class="tally-pill-btn {{ $periodPreset === 'fy' ? 'active' : '' }}">Current FY</a>
            <a href="{{ route('purchase.ledger', array_merge(request()->except(['from_date', 'to_date', 'preset']), ['preset' => 'this_month', 'vendor_id' => $selectedVendorId])) }}" class="tally-pill-btn {{ $periodPreset === 'this_month' ? 'active' : '' }}">This Month</a>
            <a href="{{ route('purchase.ledger', array_merge(request()->except(['from_date', 'to_date', 'preset']), ['preset' => 'last_month', 'vendor_id' => $selectedVendorId])) }}" class="tally-pill-btn {{ $periodPreset === 'last_month' ? 'active' : '' }}">Last Month</a>
            <a href="{{ route('purchase.ledger', array_merge(request()->except(['from_date', 'to_date', 'preset']), ['preset' => 'all', 'vendor_id' => $selectedVendorId])) }}" class="tally-pill-btn {{ $periodPreset === 'all' ? 'active' : '' }}">All Time</a>
          </div>
        </div>

        <!-- Clear Filters -->
        <div style="margin-left:auto; display:flex; align-items:flex-end; padding-top:18px;">
          @if($selectedVendorId || $fromDate || $toDate)
            <a href="{{ route('purchase.ledger') }}" class="btn btn-secondary btn-sm" style="font-weight:700; height:38px; display:inline-flex; align-items:center;">
              Reset Filter
            </a>
          @endif
        </div>

      </form>
    </div>

    <!-- Tally-Style Clean 3-Column Table -->
    <div class="table-responsive">
      <table class="tally-table" id="purchase-ledger-table">
        <thead>
          <tr>
            <th style="width:200px; text-align:left;">Date</th>
            <th style="text-align:left;">Bill Number</th>
            <th style="width:240px; text-align:right;">Amount (₹)</th>
          </tr>
        </thead>
        <tbody>
          @forelse($bills as $bill)
            @php
              $billNo = $bill->challan_no ?: $bill->po_number;
              $billDateFormatted = date('d-M-Y', strtotime($bill->po_date));
            @endphp
            <tr>
              <!-- 1. Date -->
              <td style="font-weight:700; color:#334155;">
                {{ $billDateFormatted }}
              </td>

              <!-- 2. Bill Number -->
              <td>
                <span style="font-weight:800; font-family:var(--font-mono, monospace); color:#0f172a;">
                  {{ $billNo }}
                </span>
                @if(!$selectedVendor)
                  <span style="margin-left:8px; font-size:0.775rem; color:#64748b; font-weight:600;">
                    ({{ $bill->vendor_name }})
                  </span>
                @endif
              </td>

              <!-- 3. Amount (Right-Aligned) -->
              <td class="tally-amt" style="font-size:0.95rem; color:#0f172a;">
                ₹{{ number_format($bill->grand_total, 2) }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" style="text-align:center; padding:48px 20px; color:#94a3b8;">
                <div style="font-size:2rem; margin-bottom:8px;">📄</div>
                <div style="font-weight:800; font-size:1.05rem; color:#334155;">No Purchase Transactions Found</div>
                <div style="font-size:0.825rem; color:#64748b; margin-top:4px;">
                  No purchase bills match the selected party or reporting period.
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>

        <!-- Total Purchase Amount at Bottom -->
        <tfoot>
          <tr>
            <td colspan="2" style="text-transform:uppercase; letter-spacing:0.05em; font-size:0.875rem; color:#0f172a;">
              Total Purchase Amount ({{ $billCount }} {{ Str::plural('Bill', $billCount) }})
            </td>
            <td class="tally-amt" style="font-size:1.15rem; color:#4338ca;">
              ₹{{ number_format($totalAmount, 2) }}
            </td>
          </tr>
        </tfoot>
      </table>
    </div>

  </div>

</div>
@endsection
