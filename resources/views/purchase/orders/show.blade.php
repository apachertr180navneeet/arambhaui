@extends('layouts.app')

@section('title', 'Purchase Bill - ' . $order->po_number . ' - aarambh')

@section('breadcrumb')
  <div class="breadcrumb-item"><a href="{{ route('purchase.orders.index') }}" style="color:inherit; text-decoration:none;">Purchase Book</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item"><a href="{{ route('purchase.ledger') }}" style="color:inherit; text-decoration:none;">Purchase Register</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>{{ $order->po_number }}</span></div>
@endsection

@section('content')
<div style="max-width:1050px; margin:0 auto; display:flex; flex-direction:column; gap:20px;">

  <!-- Action Header -->
  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
    <div style="display:flex; align-items:center; gap:12px;">
      <a href="{{ url()->previous() ?: route('purchase.orders.index') }}" class="btn btn-secondary btn-sm" style="font-weight:700;">
        &larr; Back
      </a>
      <h2 style="margin:0; font-size:1.4rem; font-weight:800; color:var(--slate-900);">
        Purchase Bill Details
      </h2>
      <span class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-weight:800; font-family:var(--font-mono, monospace); font-size:0.875rem;">
        {{ $order->po_number }}
      </span>
      <span class="badge" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; font-weight:700;">
        {{ $order->status ?: 'Approved' }}
      </span>
    </div>

    <div style="display:flex; gap:10px; align-items:center;">
      <button class="btn btn-secondary btn-sm" onclick="window.print()" style="display:inline-flex; align-items:center; gap:6px; font-weight:700;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
        Print Bill
      </button>
      <a href="{{ route('purchase.orders.edit', $order->id) }}" class="btn btn-primary btn-sm" style="display:inline-flex; align-items:center; gap:6px; font-weight:700;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Edit Entry
      </a>
    </div>
  </div>

  <!-- Printable Invoice Document Card -->
  <div class="card" id="printable-bill" style="background:#fff; border-radius:14px; border:1px solid #cbd5e1; box-shadow:0 4px 12px rgba(15,23,42,0.06); padding:32px;">
    
    <!-- Top Invoice Header -->
    <div style="display:flex; justify-content:space-between; align-items:flex-start; border-bottom:2px solid #0f172a; padding-bottom:20px; margin-bottom:24px;">
      <div>
        <h1 style="margin:0; font-size:1.6rem; font-weight:900; color:#0f172a; letter-spacing:-0.02em;">
          {{ $companySettings['company_name'] ?? 'Aarambh ERP' }}
        </h1>
        <div style="font-size:0.85rem; color:#475569; margin-top:4px; max-width:400px;">
          {{ $companySettings['company_address'] ?? 'Textile & Garment Manufacturing Unit' }}
        </div>
        <div style="font-size:0.825rem; color:#64748b; margin-top:4px;">
          @if(!empty($companySettings['gstin'])) GSTIN: <strong style="color:#0f172a;">{{ $companySettings['gstin'] }}</strong> &bull; @endif
          @if(!empty($companySettings['email'])) Email: {{ $companySettings['email'] }} @endif
        </div>
      </div>

      <div style="text-align:right;">
        <div style="font-size:1.25rem; font-weight:900; color:#1e3a8a; text-transform:uppercase; letter-spacing:0.04em;">
          PURCHASE INWARD VOUCHER
        </div>
        <div style="font-family:var(--font-mono, monospace); font-size:1.1rem; font-weight:800; color:#0f172a; margin-top:4px;">
          {{ $order->po_number }}
        </div>
        <div style="font-size:0.85rem; color:#64748b; margin-top:4px;">
          Date: <strong style="color:#0f172a;">{{ date('d M Y', strtotime($order->po_date)) }}</strong>
        </div>
      </div>
    </div>

    <!-- Vendor & Logistics Metadata Box -->
    <div style="display:grid; grid-template-columns:1.5fr 1fr; gap:20px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:18px 20px; margin-bottom:24px;">
      <div>
        <div style="font-size:0.75rem; text-transform:uppercase; font-weight:800; color:#64748b; letter-spacing:0.04em; margin-bottom:6px;">
          Supplier / Vendor:
        </div>
        <div style="font-size:1.15rem; font-weight:800; color:#0f172a;">
          {{ $order->vendor_name }}
        </div>
        @if($order->vendor)
          <div style="font-size:0.825rem; color:#475569; margin-top:4px;">
            {{ $order->vendor->address ?: 'Address on file' }}
            @if($order->vendor->gst_number) &bull; GSTIN: <strong>{{ $order->vendor->gst_number }}</strong> @endif
            @if($order->vendor->phone) &bull; Ph: {{ $order->vendor->phone }} @endif
          </div>
        @endif
      </div>

      <div style="display:flex; flex-direction:column; gap:6px; font-size:0.85rem;">
        <div style="display:flex; justify-content:space-between;">
          <span style="color:#64748b;">Challan / Bill No:</span>
          <strong style="font-family:monospace; color:#b45309;">{{ $order->challan_no ?: '—' }}</strong>
        </div>
        <div style="display:flex; justify-content:space-between;">
          <span style="color:#64748b;">Purchase Type:</span>
          <strong style="color:#0f172a;">{{ $order->purchase_type ?: 'Fabric (Raw Material)' }}</strong>
        </div>
        <div style="display:flex; justify-content:space-between;">
          <span style="color:#64748b;">Transport Name:</span>
          <strong style="color:#0f172a;">{{ $order->transport_name ?: '—' }}</strong>
        </div>
        <div style="display:flex; justify-content:space-between;">
          <span style="color:#64748b;">Bilty / LR No:</span>
          <strong style="font-family:monospace; color:#0f172a;">{{ $order->bilty_no ?: '—' }}</strong>
        </div>
      </div>
    </div>

    <!-- Items Table -->
    <div style="overflow-x:auto; margin-bottom:24px;">
      <table style="width:100%; border-collapse:collapse; font-size:0.875rem;">
        <thead>
          <tr style="background:#0f172a; color:#ffffff;">
            <th style="padding:10px 14px; text-align:center; width:50px;">S.No.</th>
            <th style="padding:10px 14px; text-align:left;">Item Name & Fabric</th>
            <th style="padding:10px 14px; text-align:right;">Rate (₹)</th>
            <th style="padding:10px 14px; text-align:right;">Qty</th>
            <th style="padding:10px 14px; text-align:center;">Unit</th>
            <th style="padding:10px 14px; text-align:center;">Than Detail</th>
            <th style="padding:10px 14px; text-align:center;">Tax %</th>
            <th style="padding:10px 14px; text-align:right;">Amount (₹)</th>
            <th style="padding:10px 14px; text-align:right;">Tax Amt (₹)</th>
            <th style="padding:10px 14px; text-align:right;">Total (₹)</th>
          </tr>
        </thead>
        <tbody>
          @php
            $totQty = 0;
            $totThans = 0;
            $totTaxable = 0;
            $totTax = 0;
          @endphp
          @foreach($order->items as $idx => $it)
            @php
              $q = (float)($it->quantity > 0 ? $it->quantity : ($it->than_meters ?? 0));
              $r = (float)$it->rate;
              $amt = (float)($it->amount ?: ($q * $r));
              $tPct = (float)($it->tax_percentage ?? 5);
              $tAmt = (float)($it->tax_amount ?: ($amt * $tPct / 100));
              $itemTotal = (float)($it->total ?: ($amt + $tAmt));
              $thCount = (int)($it->than_count > 0 ? $it->than_count : (count($it->than_list ?? []) ?: 1));

              $totQty += $q;
              $totThans += $thCount;
              $totTaxable += $amt;
              $totTax += $tAmt;
            @endphp
            <tr style="border-bottom:1px solid #e2e8f0;">
              <td style="padding:12px 14px; text-align:center; color:#64748b;">{{ $idx + 1 }}</td>
              <td style="padding:12px 14px;">
                <div style="font-weight:700; color:#0f172a;">{{ $it->item_name }}</div>
                @if($it->item_code)
                  <div style="font-size:0.75rem; color:#64748b; font-family:monospace;">{{ $it->item_code }}</div>
                @endif
              </td>
              <td style="padding:12px 14px; text-align:right; font-family:monospace;">₹{{ number_format($r, 2) }}</td>
              <td style="padding:12px 14px; text-align:right; font-weight:800; color:#0f172a;">{{ number_format($q, 2) }}</td>
              <td style="padding:12px 14px; text-align:center;">{{ $it->unit ?? 'MTR' }}</td>
              <td style="padding:12px 14px; text-align:center;">
                <span class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-weight:700;">
                  {{ $thCount }} Than(s)
                </span>
                @if(!empty($it->than_list) && is_array($it->than_list))
                  <div style="font-size:0.725rem; color:#475569; margin-top:2px; font-family:monospace;">
                    ({{ implode(', ', array_map(function($t){ return is_array($t) ? ($t['meter']??$t['meters']??0) : $t; }, array_slice($it->than_list, 0, 5))) }}{{ count($it->than_list) > 5 ? '...' : '' }})
                  </div>
                @endif
              </td>
              <td style="padding:12px 14px; text-align:center;">{{ $tPct }}%</td>
              <td style="padding:12px 14px; text-align:right; font-family:monospace;">₹{{ number_format($amt, 2) }}</td>
              <td style="padding:12px 14px; text-align:right; font-family:monospace; color:#d97706;">₹{{ number_format($tAmt, 2) }}</td>
              <td style="padding:12px 14px; text-align:right; font-weight:800; font-family:monospace; color:#059669;">₹{{ number_format($itemTotal, 2) }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot style="background:#f8fafc; font-weight:800; border-top:2px solid #0f172a; border-bottom:2px solid #0f172a;">
          <tr>
            <td colspan="3" style="padding:12px 14px; text-align:right;">TOTALS:</td>
            <td style="padding:12px 14px; text-align:right; color:#0f172a;">{{ number_format($totQty, 2) }}</td>
            <td></td>
            <td style="padding:12px 14px; text-align:center; color:#1d4ed8;">{{ $totThans }} Thans</td>
            <td></td>
            <td style="padding:12px 14px; text-align:right; font-family:monospace;">₹{{ number_format($totTaxable, 2) }}</td>
            <td style="padding:12px 14px; text-align:right; font-family:monospace; color:#d97706;">₹{{ number_format($totTax, 2) }}</td>
            <td style="padding:12px 14px; text-align:right; font-family:monospace; color:#059669; font-size:1.05rem;">
              ₹{{ number_format($order->grand_total ?: ($totTaxable + $totTax), 2) }}
            </td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- Bottom Totals & Signatures -->
    <div style="display:grid; grid-template-columns:1.5fr 1fr; gap:24px; align-items:start;">
      <div>
        @if($order->remarks || $order->notes)
          <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px 16px;">
            <div style="font-size:0.75rem; text-transform:uppercase; font-weight:800; color:#64748b; margin-bottom:4px;">Remarks / Notes</div>
            <div style="font-size:0.85rem; color:#334155;">{{ $order->remarks ?: $order->notes }}</div>
          </div>
        @endif
      </div>

      <div style="background:#f8fafc; border:1px solid #cbd5e1; border-radius:8px; padding:16px 20px;">
        <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:0.9rem;">
          <span style="color:#64748b;">Subtotal (Taxable):</span>
          <strong style="font-family:monospace;">₹{{ number_format($totTaxable, 2) }}</strong>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:0.9rem;">
          <span style="color:#64748b;">Total GST Tax:</span>
          <strong style="font-family:monospace; color:#d97706;">₹{{ number_format($totTax, 2) }}</strong>
        </div>
        <div style="display:flex; justify-content:space-between; border-top:2px solid #0f172a; padding-top:10px; font-size:1.15rem;">
          <span style="font-weight:900; color:#0f172a;">Grand Total:</span>
          <strong style="font-weight:900; color:#059669; font-family:monospace;">
            ₹{{ number_format($order->grand_total ?: ($totTaxable + $totTax), 2) }}
          </strong>
        </div>
      </div>
    </div>

    <!-- Sign-off lines -->
    <div style="display:flex; justify-content:space-between; margin-top:50px; padding-top:20px; border-top:1px dashed #cbd5e1; font-size:0.8rem; color:#64748b;">
      <div>Received & Checked By: ___________________</div>
      <div>Store In-Charge: ___________________</div>
      <div>Authorized Signatory: ___________________</div>
    </div>

  </div>

</div>
@endsection
