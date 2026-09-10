@extends('layouts.app')

@section('title', 'Admin QR Vouchers & Expiry Ledger - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Barcodes & QR</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Admin QR & Expiry Ledger</span></div>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <!-- KPI Metrics Row -->
  <div class="kpi-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:var(--primary-50); color:var(--primary-600); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Total Vouchers Issued</div>
        <div style="font-size:1.45rem; font-weight:800; color:var(--slate-900); margin-top:2px;">{{ $stats['total'] }}</div>
      </div>
    </div>

    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Active & Claimable</div>
        <div style="font-size:1.45rem; font-weight:800; color:#059669; margin-top:2px;">{{ $stats['active'] }} Active</div>
      </div>
    </div>

    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:#fef2f2; color:#dc2626; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Redeemed / Expired</div>
        <div style="font-size:1.45rem; font-weight:800; color:#dc2626; margin-top:2px;">{{ $stats['redeemed'] + $stats['expired'] }}</div>
      </div>
    </div>
  </div>

  <!-- Main Card Container -->
  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:20px;">
    
    <!-- Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:20px;">
      <div>
        <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Admin QR Vouchers & Single-Use Ledger</h3>
        <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">Live status tracking, customer phone numbers, redemption audit timestamps, and expiry control</p>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('qr-table', 'QR_Vouchers_Ledger.csv')" style="display:inline-flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Export CSV
        </button>
        <a href="{{ route('qr.generator') }}" class="btn btn-primary btn-sm" style="display:inline-flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
          + Generate Single-Use QR
        </a>
      </div>
    </div>

    <!-- Quick Filter Bar -->
    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:16px;">
      <input type="text" class="form-control" style="max-width:280px; font-size:0.85rem;" placeholder="Search voucher code, phone, campaign..." onkeyup="UI.filterGenericTable('qr-table', this.value)">
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="data-table" id="qr-table" style="width:100%; font-size:0.85rem;">
        <thead>
          <tr>
            <th>Voucher Code</th>
            <th>Campaign Title</th>
            <th>Customer Phone</th>
            <th>Discount Value</th>
            <th>Valid Until</th>
            <th>Status</th>
            <th>Redeemed At</th>
            <th>Claim / Receipt #</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($vouchers as $v)
            <tr>
              <td style="font-family:var(--font-mono); font-weight:700; color:#7c3aed;">
                {{ $v->voucher_code }}
              </td>
              <td style="font-weight:600; color:var(--slate-800);">{{ $v->title }}</td>
              <td style="font-weight:600; color:var(--slate-700);">{{ $v->customer_phone ?: '— (Open/Unclaimed)' }}</td>
              <td style="font-weight:800; color:#059669;">
                {{ $v->discount_type === 'Percentage' ? ($v->discount_percent . '% OFF (Max ₹' . number_format($v->max_discount_cap) . ')') : ('₹' . number_format($v->discount_amount ?: $v->discount_percent) . ' Flat') }}
              </td>
              <td>{{ $v->valid_until ? date('d M Y', strtotime($v->valid_until)) : 'Never' }}</td>
              <td>
                @if($v->status === 'Active')
                  <span class="badge badge-success">Single-Use Active</span>
                @elseif($v->status === 'Redeemed')
                  <span class="badge badge-danger">Redeemed & Expired</span>
                @else
                  <span class="badge badge-secondary">{{ $v->status }}</span>
                @endif
              </td>
              <td style="font-size:0.8rem; color:var(--slate-500);">
                {{ $v->redeemed_at ? (is_string($v->redeemed_at) ? $v->redeemed_at : $v->redeemed_at->format('d M Y, h:i A')) : '—' }}
              </td>
              <td style="font-family:var(--font-mono); font-size:0.8rem; font-weight:600;">
                {{ $v->redeemed_invoice_no ?: '—' }}
              </td>
              <td style="text-align:right;">
                <div style="display:inline-flex; gap:6px;">
                  @if($v->status === 'Active')
                    <form action="{{ route('qr.expire', $v->id) }}" method="POST" style="display:inline;">
                      @csrf
                      <button type="submit" class="btn btn-warning btn-xs">Expire</button>
                    </form>
                  @else
                    <form action="{{ route('qr.reactivate', $v->id) }}" method="POST" style="display:inline;">
                      @csrf
                      <button type="submit" class="btn btn-secondary btn-xs">Reactivate</button>
                    </form>
                  @endif

                  <form action="{{ route('qr.destroy', $v->id) }}" method="POST" onsubmit="return confirm('Delete voucher {{ $v->voucher_code }}?')" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-xs">&times;</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" style="text-align:center; padding:30px; color:var(--slate-400);">
                No QR vouchers registered yet. Click "+ Generate Single-Use QR" to create promotion codes.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>
@endsection
