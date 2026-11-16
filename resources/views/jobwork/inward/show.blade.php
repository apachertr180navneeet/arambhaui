@extends('layouts.app')

@section('title', 'Job Inward Receipt - ' . $inward->inward_number . ' - aarambh')

@section('breadcrumb')
  <div class="breadcrumb-item"><a href="{{ route('jobwork.inward.index') }}" style="color:inherit; text-decoration:none;">Jobwork Inward</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item"><a href="{{ route('jobwork.inward-report') }}" style="color:inherit; text-decoration:none;">Job Inward Register</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>{{ $inward->inward_number }}</span></div>
@endsection

@section('content')
<div style="max-width:1050px; margin:0 auto; display:flex; flex-direction:column; gap:20px;">

  <!-- Action Header -->
  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
    <div style="display:flex; align-items:center; gap:12px;">
      <a href="{{ url()->previous() ?: route('jobwork.inward.index') }}" class="btn btn-secondary btn-sm" style="font-weight:700;">
        &larr; Back
      </a>
      <h2 style="margin:0; font-size:1.4rem; font-weight:800; color:var(--slate-900);">
        Job Work Inward Receipt
      </h2>
      <span class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-weight:800; font-family:var(--font-mono, monospace); font-size:0.875rem;">
        {{ $inward->inward_number }}
      </span>
      <span class="badge" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; font-weight:700;">
        QC: {{ $inward->qc_status ?: 'Passed' }}
      </span>
    </div>

    <div style="display:flex; gap:10px; align-items:center;">
      <button class="btn btn-secondary btn-sm" onclick="window.print()" style="display:inline-flex; align-items:center; gap:6px; font-weight:700;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
        Print Receipt
      </button>
      <a href="{{ route('jobwork.inward.edit', $inward->id) }}" class="btn btn-primary btn-sm" style="display:inline-flex; align-items:center; gap:6px; font-weight:700;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Edit Inward
      </a>
    </div>
  </div>

  <!-- Printable Inward Slip Document Card -->
  <div class="card" id="printable-inward" style="background:#fff; border-radius:14px; border:1px solid #cbd5e1; box-shadow:0 4px 12px rgba(15,23,42,0.06); padding:32px;">
    
    <!-- Top Inward Header -->
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
          @if(!empty($companySettings['phone'])) Ph: {{ $companySettings['phone'] }} @endif
        </div>
      </div>

      <div style="text-align:right;">
        <div style="font-size:1.25rem; font-weight:900; color:#059669; text-transform:uppercase; letter-spacing:0.04em;">
          JOB WORK INWARD RECEIPT
        </div>
        <div style="font-family:var(--font-mono, monospace); font-size:1.1rem; font-weight:800; color:#0f172a; margin-top:4px;">
          {{ $inward->inward_number }}
        </div>
        <div style="font-size:0.85rem; color:#64748b; margin-top:4px;">
          Receipt Date: <strong style="color:#0f172a;">{{ date('d M Y', strtotime($inward->inward_date)) }}</strong>
        </div>
      </div>
    </div>

    <!-- Job Worker & Order Reference Box -->
    <div style="display:grid; grid-template-columns:1.5fr 1fr; gap:20px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:18px 20px; margin-bottom:24px;">
      <div>
        <div style="font-size:0.75rem; text-transform:uppercase; font-weight:800; color:#64748b; letter-spacing:0.04em; margin-bottom:6px;">
          Received From Job Worker:
        </div>
        <div style="font-size:1.15rem; font-weight:800; color:#0f172a;">
          {{ $inward->job_worker_name }}
        </div>
        @if($inward->jobWorker)
          <div style="font-size:0.825rem; color:#475569; margin-top:4px;">
            {{ $inward->jobWorker->address ?: 'Address on file' }}
            @if($inward->jobWorker->phone) &bull; Ph: {{ $inward->jobWorker->phone }} @endif
          </div>
        @endif
      </div>

      <div style="display:flex; flex-direction:column; gap:6px; font-size:0.85rem;">
        <div style="display:flex; justify-content:space-between;">
          <span style="color:#64748b;">Job Order Ref:</span>
          @if($inward->job_assignment_id)
            <a href="{{ route('jobwork.assign.show', $inward->job_assignment_id) }}" style="font-family:monospace; font-weight:700; color:#2563eb; text-decoration:none;">
              {{ $inward->job_order_no }}
            </a>
          @else
            <strong style="font-family:monospace; color:#2563eb;">{{ $inward->job_order_no ?: '—' }}</strong>
          @endif
        </div>
        <div style="display:flex; justify-content:space-between;">
          <span style="color:#64748b;">Delivery Challan #:</span>
          <strong style="font-family:monospace; color:#0f172a;">{{ $inward->challan_no ?: '—' }}</strong>
        </div>
        <div style="display:flex; justify-content:space-between;">
          <span style="color:#64748b;">Storage Warehouse:</span>
          <strong style="color:#0f172a;">{{ $inward->storage_location ?: 'Main Warehouse' }}</strong>
        </div>
        <div style="display:flex; justify-content:space-between;">
          <span style="color:#64748b;">QC Inspection:</span>
          <strong style="color:#059669;">{{ $inward->qc_status ?: 'Passed' }}</strong>
        </div>
      </div>
    </div>

    <!-- Items Received Table -->
    <div style="overflow-x:auto; margin-bottom:24px;">
      <table style="width:100%; border-collapse:collapse; font-size:0.875rem;">
        <thead>
          <tr style="background:#065f46; color:#ffffff;">
            <th style="padding:10px 14px; text-align:center; width:50px;">SNO.</th>
            <th style="padding:10px 14px; text-align:left;">ITEM (Recd Finished Product)</th>
            <th style="padding:10px 14px; text-align:left;">NAME used item (Raw Fabric)</th>
            <th style="padding:10px 14px; text-align:right;">Qty Received</th>
            <th style="padding:10px 14px; text-align:center;">Pcs / THAN</th>
            <th style="padding:10px 14px; text-align:right;">WASTAGE (Mtr)</th>
            <th style="padding:10px 14px; text-align:right;">Pending Qty</th>
          </tr>
        </thead>
        <tbody>
          @php
            $totRecd = 0;
            $totThans = 0;
            $totWaste = 0;
            $totPending = 0;
          @endphp
          @if(!empty($inward->items_list) && is_array($inward->items_list))
            @foreach($inward->items_list as $idx => $it)
              @php
                $q = (float)($it['received_qty'] ?? 0);
                $th = (int)($it['than_count'] ?? (count($it['assigned_thans'] ?? []) ?: 1));
                $w = (float)($it['wastage_returned_meters'] ?? 0);
                $p = (float)($it['pending_qty'] ?? max(0, ($it['assigned_qty'] ?? 0) - (($it['previously_received_qty'] ?? 0) + $q)));
                
                $totRecd += $q;
                $totThans += $th;
                $totWaste += $w;
                $totPending += $p;
              @endphp
              <tr style="border-bottom:1px solid #e2e8f0;">
                <td style="padding:12px 14px; text-align:center; color:#64748b;">{{ $idx + 1 }}</td>
                <td style="padding:12px 14px; font-weight:700; color:#0f172a;">
                  {{ $it['item_name'] ?? ($it['finished_item_name'] ?? 'Garment Product') }}
                </td>
                <td style="padding:12px 14px; color:#475569;">
                  {{ $it['raw_item_name'] ?? 'Fabric' }}
                </td>
                <td style="padding:12px 14px; text-align:right; font-weight:800; color:#059669;">{{ number_format($q, 2) }}</td>
                <td style="padding:12px 14px; text-align:center;">
                  <span class="badge" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; font-weight:700;">
                    {{ $th }}
                  </span>
                </td>
                <td style="padding:12px 14px; text-align:right; font-family:monospace; color:#d97706;">{{ number_format($w, 2) }}</td>
                <td style="padding:12px 14px; text-align:right; color:#64748b;">{{ number_format($p, 2) }}</td>
              </tr>
            @endforeach
          @else
            @php
              $totRecd = (float)$inward->received_qty;
              $totThans = 1;
              $totWaste = (float)$inward->wastage_returned_meters;
            @endphp
            <tr style="border-bottom:1px solid #e2e8f0;">
              <td style="padding:12px 14px; text-align:center;">1</td>
              <td style="padding:12px 14px; font-weight:700; color:#0f172a;">{{ $inward->style_name ?: 'Garment Product' }}</td>
              <td style="padding:12px 14px; color:#475569;">Fabric</td>
              <td style="padding:12px 14px; text-align:right; font-weight:800; color:#059669;">{{ number_format($inward->received_qty, 2) }}</td>
              <td style="padding:12px 14px; text-align:center;">1</td>
              <td style="padding:12px 14px; text-align:right; color:#d97706;">{{ number_format($inward->wastage_returned_meters, 2) }}</td>
              <td style="padding:12px 14px; text-align:right; color:#64748b;">0.00</td>
            </tr>
          @endif
        </tbody>
        <tfoot style="background:#f8fafc; font-weight:800; border-top:2px solid #0f172a; border-bottom:2px solid #0f172a;">
          <tr>
            <td colspan="3" style="padding:12px 14px; text-align:right;">TOTAL:</td>
            <td style="padding:12px 14px; text-align:right; color:#059669; font-size:1.05rem;">{{ number_format($totRecd, 2) }}</td>
            <td style="padding:12px 14px; text-align:center; color:#475569;">{{ $totThans }}</td>
            <td style="padding:12px 14px; text-align:right; color:#d97706;">{{ number_format($totWaste, 2) }}</td>
            <td style="padding:12px 14px; text-align:right; color:#64748b;">{{ number_format($totPending, 2) }}</td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- Summary Details -->
    <div style="display:grid; grid-template-columns:1.5fr 1fr; gap:24px; align-items:start;">
      <div>
        @if($inward->remarks)
          <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px 16px;">
            <div style="font-size:0.75rem; text-transform:uppercase; font-weight:800; color:#64748b; margin-bottom:4px;">Remarks</div>
            <div style="font-size:0.85rem; color:#334155;">{{ $inward->remarks }}</div>
          </div>
        @endif
      </div>

      <div style="background:#f8fafc; border:1px solid #cbd5e1; border-radius:8px; padding:16px 20px;">
        <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:0.9rem;">
          <span style="color:#64748b;">- T Recd Item:</span>
          <strong style="color:#059669; font-size:1.05rem;">{{ number_format($totRecd, 2) }} PCS</strong>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:0.9rem;">
          <span style="color:#64748b;">- Total Wastage:</span>
          <strong style="color:#d97706; font-size:1.05rem;">{{ number_format($totWaste, 2) }} MTR</strong>
        </div>
        <div style="display:flex; justify-content:space-between; border-top:2px solid #0f172a; padding-top:10px; font-size:1.15rem;">
          <span style="font-weight:900; color:#0f172a;">- Net Inward:</span>
          <strong style="font-weight:900; color:#059669; font-family:monospace;">
            {{ number_format(max(0, $totRecd - $totWaste), 2) }} PCS
          </strong>
        </div>
      </div>
    </div>

    <!-- Sign-off lines -->
    <div style="display:flex; justify-content:space-between; margin-top:50px; padding-top:20px; border-top:1px dashed #cbd5e1; font-size:0.8rem; color:#64748b;">
      <div>QC Checked By: ___________________</div>
      <div>Store Receiving: ___________________</div>
      <div>Authorized Signatory: ___________________</div>
    </div>

  </div>

</div>
@endsection
