@extends('layouts.app')

@section('title', 'Purchase Ledger & Item Details - aarambh')

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
    font-size: 0.85rem;
    background: #ffffff;
  }
  .tally-table th {
    background: #f1f5f9;
    color: #334155;
    font-weight: 800;
    text-transform: uppercase;
    font-size: 0.725rem;
    letter-spacing: 0.05em;
    padding: 11px 14px;
    border-top: 1px solid #e2e8f0;
    border-bottom: 2px solid #cbd5e1;
    white-space: nowrap;
  }
  .tally-table td {
    padding: 10px 14px;
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
    padding: 14px 16px;
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
  .bill-header-row {
    background: #f8fafc;
    border-top: 1.5px solid #e2e8f0;
  }
  .bill-header-cell {
    font-weight: 800;
    color: #0f172a;
    font-size: 0.825rem;
    padding: 8px 14px !important;
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

  <!-- Summary KPI Cards -->
  <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
    <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:16px 20px; box-shadow:var(--shadow-sm); display:flex; justify-content:space-between; align-items:center;">
      <div>
        <div style="font-size:0.75rem; text-transform:uppercase; color:#64748b; font-weight:700; letter-spacing:0.04em;">Total Bills</div>
        <div style="font-size:1.6rem; font-weight:800; color:#0f172a; margin-top:2px;">{{ $billCount }}</div>
      </div>
      <div style="width:44px; height:44px; border-radius:10px; background:#eef2ff; color:#4338ca; display:flex; align-items:center; justify-content:center; font-size:1.3rem;">🧾</div>
    </div>
    <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:16px 20px; box-shadow:var(--shadow-sm); display:flex; justify-content:space-between; align-items:center;">
      <div>
        <div style="font-size:0.75rem; text-transform:uppercase; color:#64748b; font-weight:700; letter-spacing:0.04em;">Total Items Purchased</div>
        <div style="font-size:1.6rem; font-weight:800; color:#059669; margin-top:2px;">{{ format_quantity($totalItemsQty ?? 0) }}</div>
      </div>
      <div style="width:44px; height:44px; border-radius:10px; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center; font-size:1.3rem;">📦</div>
    </div>
    <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:16px 20px; box-shadow:var(--shadow-sm); display:flex; justify-content:space-between; align-items:center;">
      <div>
        <div style="font-size:0.75rem; text-transform:uppercase; color:#64748b; font-weight:700; letter-spacing:0.04em;">Total Purchase Value</div>
        <div style="font-size:1.6rem; font-weight:800; color:#4338ca; margin-top:2px;">₹{{ number_format($totalAmount, 2) }}</div>
      </div>
      <div style="width:44px; height:44px; border-radius:10px; background:#eef2ff; color:#4338ca; display:flex; align-items:center; justify-content:center; font-size:1.3rem;">💳</div>
    </div>
  </div>

  <!-- Main Ledger Card Container -->
  <div class="tally-card">
    
    <!-- Tally-Style Top Banner: Party Name & Reporting Period -->
    <div class="tally-header">
      <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px;">
        <div>
          <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.08em; color:#94a3b8; font-weight:700; margin-bottom:4px;">
            Accounting Statement &bull; Detailed Purchase Register
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
          <a href="{{ route('purchase.orders.create') }}" class="tally-hdr-btn tally-hdr-btn-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>New Purchase</span>
          </a>

          <button type="button" class="tally-hdr-btn tally-hdr-btn-light" onclick="window.print()">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
            <span>Print</span>
          </button>

          <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="tally-hdr-btn tally-hdr-btn-light">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            <span>Export CSV / Excel</span>
          </a>
        </div>
      </div>
    </div>

    <!-- Filter Bar: Party & Date Selection -->
    <div class="tally-filter-bar">
      <form method="GET" action="{{ route('purchase.ledger') }}" id="ledger-filter-form" style="display:flex; flex-wrap:wrap; gap:12px; align-items:center; width:100%;">
        
        <!-- Search Input -->
        <div style="min-width:200px; flex:1; max-width:280px;">
          <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">
            Search Item / Bill:
          </label>
          <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search item, code, bill..." class="form-control" style="font-weight:600; height:38px; border-radius:8px;">
        </div>

        <!-- Supplier / Party Filter -->
        <div style="min-width:220px; flex:1; max-width:300px;">
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
        <div style="width:140px;">
          <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">
            From Date:
          </label>
          <input type="date" name="from_date" value="{{ $fromDate }}" class="form-control" style="height:38px; font-weight:600; border-radius:8px;">
        </div>

        <!-- Date Range: To Date -->
        <div style="width:140px;">
          <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">
            To Date:
          </label>
          <input type="date" name="to_date" value="{{ $toDate }}" class="form-control" style="height:38px; font-weight:600; border-radius:8px;">
        </div>

        <!-- Filter Action Buttons -->
        <div style="display:flex; align-items:flex-end; gap:8px; padding-top:18px;">
          <button type="submit" class="btn btn-primary btn-sm" style="font-weight:700; height:38px;">Filter</button>
          @if($selectedVendorId || $fromDate || $toDate || !empty($search))
            <a href="{{ route('purchase.ledger') }}" class="btn btn-secondary btn-sm" style="font-weight:700; height:38px; display:inline-flex; align-items:center;">Reset</a>
          @endif
        </div>

        <!-- Preset Period Buttons -->
        <div style="display:flex; flex-direction:column; gap:4px; margin-left:auto;">
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

      </form>
    </div>

    <!-- Detailed Purchase Ledger Table with Item Details -->
    <div class="table-responsive">
      <table class="tally-table" id="purchase-ledger-table">
        <thead>
          <tr>
            <th style="width:110px;">Date</th>
            <th style="width:140px;">Bill / Doc #</th>
            <th>Supplier</th>
            <th>Item Description / Quality</th>
            <th style="width:90px;">Item Code</th>
            <th style="width:90px; text-align:right;">Quantity</th>
            <th style="width:70px;">Unit</th>
            <th style="width:95px; text-align:right;">Rate (₹)</th>
            <th style="width:80px; text-align:right;">Tax</th>
            <th style="width:110px; text-align:right;">Item Total (₹)</th>
            <th style="width:120px; text-align:right;">Bill Total (₹)</th>
          </tr>
        </thead>
        <tbody>
          @forelse($bills as $bill)
            @php
              $billNo = $bill->challan_no ?: $bill->po_number;
              $billDateFormatted = date('d-M-Y', strtotime($bill->po_date));
              $items = $bill->items;
              $itemCount = $items->count();
            @endphp

            @if($itemCount > 0)
              @foreach($items as $idx => $item)
                <tr>
                  @if($idx === 0)
                    <!-- Date -->
                    <td rowspan="{{ $itemCount }}" style="font-weight:700; color:#334155; vertical-align:top; border-right:1px solid #f1f5f9; background:#fff;">
                      {{ $billDateFormatted }}
                    </td>

                    <!-- Bill Number & PO Reference -->
                    <td rowspan="{{ $itemCount }}" style="vertical-align:top; border-right:1px solid #f1f5f9; background:#fff;">
                      <a href="{{ route('purchase.orders.show', $bill->id) }}" style="font-weight:800; font-family:var(--font-mono, monospace); color:#4338ca; text-decoration:none;">
                        {{ $billNo }}
                      </a>
                      @if($bill->challan_no && $bill->challan_no !== $bill->po_number)
                        <div style="font-size:0.7rem; color:#64748b; font-family:monospace;">PO: {{ $bill->po_number }}</div>
                      @endif
                    </td>

                    <!-- Supplier -->
                    <td rowspan="{{ $itemCount }}" style="vertical-align:top; border-right:1px solid #f1f5f9; background:#fff;">
                      <div style="font-weight:700; color:#0f172a;">{{ $bill->vendor_name }}</div>
                      @if($bill->vendor && $bill->vendor->code)
                        <div style="font-size:0.7rem; color:#64748b; font-family:monospace;">{{ $bill->vendor->code }}</div>
                      @endif
                    </td>
                  @endif

                  <!-- Item Name -->
                  <td style="font-weight:700; color:#1e293b;">
                    {{ $item->item_name }}
                  </td>

                  <!-- Item Code / SKU -->
                  <td style="font-family:var(--font-mono, monospace); font-size:0.775rem; color:#475569; font-weight:600;">
                    {{ $item->item_code ?: '—' }}
                  </td>

                  <!-- Quantity -->
                  <td style="text-align:right; font-weight:800; color:#059669; font-family:var(--font-mono, monospace);">
                    {{ format_quantity($item->ordered_qty) }}
                  </td>

                  <!-- Unit -->
                  <td>
                    <span style="font-size:0.75rem; font-weight:700; background:#f1f5f9; color:#475569; padding:2px 6px; border-radius:4px;">
                      {{ $item->unit ?: 'MTR' }}
                    </span>
                  </td>

                  <!-- Rate -->
                  <td class="tally-amt" style="color:#334155;">
                    ₹{{ number_format($item->rate, 2) }}
                  </td>

                  <!-- Tax -->
                  <td class="tally-amt" style="font-size:0.775rem; color:#64748b;">
                    {{ number_format($item->tax_percent, 1) }}%
                    <div style="font-size:0.7rem; color:#94a3b8;">₹{{ number_format($item->tax_amount, 2) }}</div>
                  </td>

                  <!-- Item Total -->
                  <td class="tally-amt" style="font-weight:700; color:#0f172a;">
                    ₹{{ number_format($item->total_amount, 2) }}
                  </td>

                  @if($idx === 0)
                    <!-- Bill Grand Total -->
                    <td rowspan="{{ $itemCount }}" class="tally-amt" style="vertical-align:middle; font-size:0.95rem; font-weight:800; color:#4338ca; border-left:1px solid #f1f5f9; background:#fcfdff;">
                      ₹{{ number_format($bill->grand_total, 2) }}
                    </td>
                  @endif
                </tr>
              @endforeach
            @else
              <!-- Fallback if no items array saved -->
              <tr>
                <td style="font-weight:700; color:#334155;">{{ $billDateFormatted }}</td>
                <td>
                  <a href="{{ route('purchase.orders.show', $bill->id) }}" style="font-weight:800; font-family:monospace; color:#4338ca; text-decoration:none;">
                    {{ $billNo }}
                  </a>
                </td>
                <td style="font-weight:700; color:#0f172a;">{{ $bill->vendor_name }}</td>
                <td style="font-weight:700; color:#1e293b;">Fabric Material</td>
                <td style="color:#64748b;">—</td>
                <td style="text-align:right; font-weight:800; color:#059669;">1</td>
                <td><span style="font-size:0.75rem; font-weight:700; background:#f1f5f9; color:#475569; padding:2px 6px; border-radius:4px;">MTR</span></td>
                <td class="tally-amt">₹{{ number_format($bill->subtotal, 2) }}</td>
                <td class="tally-amt" style="font-size:0.775rem;">₹{{ number_format($bill->tax_total, 2) }}</td>
                <td class="tally-amt" style="font-weight:700;">₹{{ number_format($bill->grand_total, 2) }}</td>
                <td class="tally-amt" style="font-size:0.95rem; font-weight:800; color:#4338ca;">₹{{ number_format($bill->grand_total, 2) }}</td>
              </tr>
            @endif
          @empty
            <tr>
              <td colspan="11" style="text-align:center; padding:48px 20px; color:#94a3b8;">
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
            <td colspan="5" style="text-transform:uppercase; letter-spacing:0.05em; font-size:0.825rem; color:#0f172a;">
              Total ({{ $billCount }} {{ Str::plural('Bill', $billCount) }})
            </td>
            <td style="text-align:right; font-weight:800; font-family:var(--font-mono, monospace); font-size:0.95rem; color:#059669;">
              {{ format_quantity($totalItemsQty ?? 0) }}
            </td>
            <td colspan="4"></td>
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
