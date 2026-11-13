@extends('layouts.app')

@section('title', 'Item Stock Movement Ledger - aarambh')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Reports Hub</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Item Stock Ledger</span></div>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <!-- Company Report Header Banner -->
  <div style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:16px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; box-shadow:var(--shadow-sm);">
    <div style="display:flex; align-items:center; gap:12px;">
      <div style="width:42px; height:42px; border-radius:10px; background:var(--primary-50); color:var(--primary-700); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.2rem;">
        🏢
      </div>
      <div>
        <div style="font-weight:800; font-size:1.15rem; color:var(--slate-900);">{{ $companyName ?? 'aarambh' }}</div>
        <div style="font-size:0.775rem; color:var(--slate-500);">
          @if(!empty($companySettings['gstin'])) GSTIN: <strong style="color:var(--slate-700);">{{ $companySettings['gstin'] }}</strong> &bull; @endif
          Financial Year: <strong style="color:var(--slate-700);">{{ $companySettings['financial_year'] ?? date('Y') . '-' . (date('Y')+1) }}</strong>
          @if(!empty($companySettings['company_address'])) &bull; {{ Str::limit($companySettings['company_address'], 60) }} @endif
        </div>
      </div>
    </div>
    <div style="display:flex; gap:8px;">
      <button class="btn btn-secondary btn-sm" onclick="window.print()" style="display:inline-flex; align-items:center; gap:6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
        Print Ledger
      </button>
      <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-secondary btn-sm" style="display:inline-flex; align-items:center; gap:6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Export CSV
      </a>
    </div>
  </div>

  <!-- Summary KPI Cards -->
  <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
    <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:16px 20px; box-shadow:var(--shadow-sm); display:flex; justify-content:space-between; align-items:center;">
      <div>
        <div style="font-size:0.75rem; text-transform:uppercase; color:#64748b; font-weight:700; letter-spacing:0.04em;">Total Material In (+)</div>
        <div style="font-size:1.6rem; font-weight:800; color:#059669; margin-top:2px;">+{{ format_quantity($totalIn ?? 0) }}</div>
      </div>
      <div style="width:44px; height:44px; border-radius:10px; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center; font-size:1.3rem;">📥</div>
    </div>
    <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:16px 20px; box-shadow:var(--shadow-sm); display:flex; justify-content:space-between; align-items:center;">
      <div>
        <div style="font-size:0.75rem; text-transform:uppercase; color:#64748b; font-weight:700; letter-spacing:0.04em;">Total Material Out (-)</div>
        <div style="font-size:1.6rem; font-weight:800; color:#dc2626; margin-top:2px;">-{{ format_quantity($totalOut ?? 0) }}</div>
      </div>
      <div style="width:44px; height:44px; border-radius:10px; background:#fef2f2; color:#dc2626; display:flex; align-items:center; justify-content:center; font-size:1.3rem;">📤</div>
    </div>
    <div style="background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:16px 20px; box-shadow:var(--shadow-sm); display:flex; justify-content:space-between; align-items:center;">
      <div>
        <div style="font-size:0.75rem; text-transform:uppercase; color:#64748b; font-weight:700; letter-spacing:0.04em;">Current Stock Balance</div>
        <div style="font-size:1.6rem; font-weight:800; color:#4338ca; margin-top:2px;">{{ format_quantity($running ?? 0) }} {{ $selectedItem ? $selectedItem->unit : '' }}</div>
      </div>
      <div style="width:44px; height:44px; border-radius:10px; background:#eef2ff; color:#4338ca; display:flex; align-items:center; justify-content:center; font-size:1.3rem;">📊</div>
    </div>
  </div>

  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:20px;">
    
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:18px;">
      <div>
        <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900);">
          {{ $selectedItem ? "{$selectedItem->name} Stock Movement Ledger" : "Item Stock Movement & Transaction Ledger" }}
        </h3>
        <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">Material IN (Purchases & Job Inward), Material OUT (Job Work Issue & Dispatch) with chronological running balance.</p>
      </div>
    </div>

    <!-- Filter Bar -->
    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:14px 16px; margin-bottom:18px;">
      <form method="GET" action="{{ route('reports.ledger') }}" style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
        
        <!-- Item Filter -->
        <div style="min-width:220px; flex:1; max-width:320px;">
          <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">Filter by Item / SKU:</label>
          <select name="item_id" class="form-control" style="font-weight:700; height:38px; border-radius:8px;">
            <option value="">-- All Items / Materials --</option>
            @foreach($items as $itm)
              <option value="{{ $itm->id }}" {{ $selectedItemId == $itm->id ? 'selected' : '' }}>
                {{ $itm->name }} ({{ $itm->code ?: 'SKU' }}) - Stock: {{ format_quantity($itm->current_stock) }} {{ $itm->unit }}
              </option>
            @endforeach
          </select>
        </div>

        <!-- Search -->
        <div style="min-width:180px; flex:1; max-width:260px;">
          <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">Search Keyword:</label>
          <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control" style="height:38px; border-radius:8px;" placeholder="Search doc#, item, type...">
        </div>

        <!-- From Date -->
        <div style="width:140px;">
          <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">From Date:</label>
          <input type="date" name="from_date" value="{{ $fromDate ?? '' }}" class="form-control" style="height:38px; border-radius:8px;">
        </div>

        <!-- To Date -->
        <div style="width:140px;">
          <label style="display:block; font-size:0.75rem; font-weight:700; color:#475569; margin-bottom:4px;">To Date:</label>
          <input type="date" name="to_date" value="{{ $toDate ?? '' }}" class="form-control" style="height:38px; border-radius:8px;">
        </div>

        <!-- Submit & Reset -->
        <div style="display:flex; gap:8px;">
          <button type="submit" class="btn btn-primary btn-sm" style="font-weight:700; height:38px;">Filter Ledger</button>
          @if($selectedItemId || $fromDate || $toDate || !empty($search))
            <a href="{{ route('reports.ledger') }}" class="btn btn-secondary btn-sm" style="font-weight:700; height:38px; display:inline-flex; align-items:center;">Reset</a>
          @endif
        </div>
      </form>
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="data-table" id="ledger-table" style="width:100%; font-size:0.85rem;">
        <thead>
          <tr>
            <th style="width:130px;">Date & Time</th>
            <th>Item / Material</th>
            <th style="width:140px;">Transaction Type</th>
            <th style="width:150px;">Reference Doc #</th>
            <th style="width:120px; text-align:right;">Quantity In (+)</th>
            <th style="width:120px; text-align:right;">Quantity Out (-)</th>
            <th style="width:120px; text-align:right;">Running Stock</th>
            <th style="width:70px;">Unit</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($transactions as $t)
            @php
              $isInward = $t->qty_in > 0;
            @endphp
            <tr>
              <td>{{ date('d M Y, h:i A', strtotime($t->created_at)) }}</td>
              <td style="font-weight:700; color:#0f172a;">{{ $t->item_name }}</td>
              <td>
                <span class="badge" style="{{ $isInward ? 'background:#ecfdf5; color:#059669; border:1px solid #a7f3d0;' : 'background:#fef2f2; color:#dc2626; border:1px solid #fecaca;' }} font-weight:700;">
                  {{ $t->type }}
                </span>
              </td>
              <td style="font-family:var(--font-mono); font-weight:700; color:#4338ca;">
                @php
                  $ref = $t->reference_no;
                  $targetUrl = null;
                  if (str_starts_with($ref, 'PE-') || str_starts_with($ref, 'CH-')) {
                    $targetUrl = route('purchase.orders.index') . '?search=' . urlencode($ref);
                  } elseif (str_starts_with($ref, 'JA-')) {
                    $targetUrl = route('jobwork.assign.index') . '?search=' . urlencode($ref);
                  } elseif (str_starts_with($ref, 'INW-') || str_starts_with($ref, 'JINW-')) {
                    $targetUrl = route('jobwork.inward.index') . '?search=' . urlencode($ref);
                  } elseif (str_starts_with($ref, 'DC-')) {
                    $targetUrl = route('dispatch.dispatch') . '?search=' . urlencode($ref);
                  }
                @endphp
                @if($targetUrl)
                  <a href="{{ $targetUrl }}" target="_blank" style="color:#4338ca; text-decoration:none; display:inline-flex; align-items:center; gap:4px;" title="View Voucher / Transaction">
                    <span>{{ $ref }}</span>
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                  </a>
                @else
                  {{ $ref }}
                @endif
              </td>
              <td style="font-weight:800; color:#059669; text-align:right; font-family:var(--font-mono);">{{ $t->qty_in ? '+' . format_quantity($t->qty_in) : '—' }}</td>
              <td style="font-weight:800; color:var(--danger-600); text-align:right; font-family:var(--font-mono);">{{ $t->qty_out ? '-' . format_quantity($t->qty_out) : '—' }}</td>
              <td style="font-weight:800; color:var(--slate-800); text-align:right; font-family:var(--font-mono);">{{ format_quantity($t->running_balance) }}</td>
              <td><span style="font-size:0.75rem; font-weight:700; background:#f1f5f9; color:#475569; padding:2px 6px; border-radius:4px;">{{ $t->unit ?: 'MTR' }}</span></td>
            </tr>
          @empty
            @forelse ($items as $itm)
              <tr>
                <td>{{ date('d M Y') }}</td>
                <td style="font-weight:700;">{{ $itm->name }} ({{ $itm->code }})</td>
                <td><span class="badge badge-primary">Current / Opening Stock</span></td>
                <td style="font-family:var(--font-mono); font-weight:600;">SYS-OP-{{ $itm->code }}</td>
                <td style="font-weight:700; color:#059669; text-align:right;">+{{ format_quantity($itm->current_stock) }}</td>
                <td style="text-align:right;">—</td>
                <td style="font-weight:800; color:var(--slate-800); text-align:right;">{{ format_quantity($itm->current_stock) }}</td>
                <td><span style="font-size:0.75rem; font-weight:700; background:#f1f5f9; color:#475569; padding:2px 6px; border-radius:4px;">{{ $itm->unit ?: 'MTR' }}</span></td>
              </tr>
            @empty
              <tr>
                <td colspan="8" style="text-align:center; padding:30px; color:var(--slate-400);">No stock movements recorded yet.</td>
              </tr>
            @endforelse
          @endforelse
        </tbody>
        @if($transactions->isNotEmpty())
          <tfoot>
            <tr style="background:#f8fafc; font-weight:800; border-top:2px solid #cbd5e1;">
              <td colspan="4" style="text-transform:uppercase; letter-spacing:0.04em;">Closing Stock Position</td>
              <td style="text-align:right; color:#059669; font-family:var(--font-mono); font-size:0.95rem;">+{{ format_quantity($totalIn ?? 0) }}</td>
              <td style="text-align:right; color:#dc2626; font-family:var(--font-mono); font-size:0.95rem;">-{{ format_quantity($totalOut ?? 0) }}</td>
              <td style="text-align:right; color:#4338ca; font-family:var(--font-mono); font-size:1.05rem;">{{ format_quantity($running ?? 0) }}</td>
              <td></td>
            </tr>
          </tfoot>
        @endif
      </table>
    </div>

  </div>

</div>
@endsection
