@extends('layouts.app')

@section('title', 'Job Work Order - ' . $assign->job_order_no . ' - aarambh')

@section('breadcrumb')
  <div class="breadcrumb-item"><a href="{{ route('jobwork.assign.index') }}" style="color:inherit; text-decoration:none;">Jobwork Assign</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item"><a href="{{ route('reports.job-work-register') }}" style="color:inherit; text-decoration:none;">Job Assign Register</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>{{ $assign->job_order_no }}</span></div>
@endsection

@section('content')
<div style="max-width:1050px; margin:0 auto; display:flex; flex-direction:column; gap:20px;">

  <!-- Action Header -->
  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
    <div style="display:flex; align-items:center; gap:12px;">
      <a href="{{ url()->previous() ?: route('jobwork.assign.index') }}" class="btn btn-secondary btn-sm" style="font-weight:700;">
        &larr; Back
      </a>
      <h2 style="margin:0; font-size:1.4rem; font-weight:800; color:var(--slate-900);">
        Job Work Assignment Order
      </h2>
      <span class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-weight:800; font-family:var(--font-mono, monospace); font-size:0.875rem;">
        {{ $assign->job_order_no }}
      </span>
      <span class="badge" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; font-weight:700;">
        {{ $assign->status ?: 'Issued' }}
      </span>
    </div>

    <div style="display:flex; gap:10px; align-items:center;">
      <button class="btn btn-secondary btn-sm" onclick="window.print()" style="display:inline-flex; align-items:center; gap:6px; font-weight:700;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
        Print Work Order
      </button>
      <a href="{{ route('jobwork.inward.create', ['job_order_id' => $assign->id]) }}" class="btn btn-sm" style="background:#059669; color:#fff; display:inline-flex; align-items:center; gap:6px; font-weight:700;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        Receive Inward
      </a>
      <a href="{{ route('jobwork.assign.edit', $assign->id) }}" class="btn btn-primary btn-sm" style="display:inline-flex; align-items:center; gap:6px; font-weight:700;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Edit Order
      </a>
    </div>
  </div>

  <!-- Printable Work Order Document Card -->
  <div class="card" id="printable-order" style="background:#fff; border-radius:14px; border:1px solid #cbd5e1; box-shadow:0 4px 12px rgba(15,23,42,0.06); padding:32px;">
    
    <!-- Top Order Header -->
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
        <div style="font-size:1.25rem; font-weight:900; color:#4338ca; text-transform:uppercase; letter-spacing:0.04em;">
          JOB WORK DELIVERY CHALLAN
        </div>
        <div style="font-family:var(--font-mono, monospace); font-size:1.1rem; font-weight:800; color:#0f172a; margin-top:4px;">
          {{ $assign->job_order_no }}
        </div>
        <div style="font-size:0.85rem; color:#64748b; margin-top:4px;">
          Issue Date: <strong style="color:#0f172a;">{{ date('d M Y', strtotime($assign->issue_date)) }}</strong>
        </div>
        @if($assign->due_date)
          <div style="font-size:0.85rem; color:#b45309; margin-top:2px;">
            Due Date: <strong>{{ date('d M Y', strtotime($assign->due_date)) }}</strong>
          </div>
        @endif
      </div>
    </div>

    <!-- Job Worker & Process Details Box -->
    <div style="display:grid; grid-template-columns:1.5fr 1fr; gap:20px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:18px 20px; margin-bottom:24px;">
      <div>
        <div style="font-size:0.75rem; text-transform:uppercase; font-weight:800; color:#64748b; letter-spacing:0.04em; margin-bottom:6px;">
          Contractor / Job Worker:
        </div>
        <div style="font-size:1.15rem; font-weight:800; color:#0f172a;">
          {{ $assign->job_worker_name }}
        </div>
        @if($assign->jobWorker)
          <div style="font-size:0.825rem; color:#475569; margin-top:4px;">
            {{ $assign->jobWorker->address ?: 'Address on file' }}
            @if($assign->jobWorker->phone) &bull; Ph: {{ $assign->jobWorker->phone }} @endif
            @if($assign->jobWorker->pan_number) &bull; PAN: {{ $assign->jobWorker->pan_number }} @endif
          </div>
        @endif
      </div>

      <div style="display:flex; flex-direction:column; gap:6px; font-size:0.85rem;">
        <div style="display:flex; justify-content:space-between;">
          <span style="color:#64748b;">Lot Number:</span>
          <strong style="font-family:monospace; color:#4338ca;">{{ $assign->lot_number }}</strong>
        </div>
        <div style="display:flex; justify-content:space-between;">
          <span style="color:#64748b;">Process Type:</span>
          <strong style="color:#0f172a;">{{ $assign->process_name ?: 'Stitching / Jobwork' }}</strong>
        </div>
        <div style="display:flex; justify-content:space-between;">
          <span style="color:#64748b;">Target Product:</span>
          <strong style="color:#0f172a;">{{ $assign->style_name ?: 'Garment Product' }}</strong>
        </div>
      </div>
    </div>

    <!-- Items Allocation Table -->
    <div style="overflow-x:auto; margin-bottom:24px;">
      <table style="width:100%; border-collapse:collapse; font-size:0.875rem;">
        <thead>
          <tr style="background:#1e293b; color:#ffffff;">
            <th style="padding:10px 14px; text-align:center; width:50px;">SNO.</th>
            <th style="padding:10px 14px; text-align:left;">Delivered Item (Raw Material)</th>
            <th style="padding:10px 14px; text-align:left;">Received Item (Finished Product)</th>
            <th style="padding:10px 14px; text-align:right;">Qty (Mtr)</th>
            <th style="padding:10px 14px; text-align:center;">THAN</th>
            <th style="padding:10px 14px; text-align:right;">Expected WASTE</th>
            <th style="padding:10px 14px; text-align:center;">Target Delivery</th>
          </tr>
        </thead>
        <tbody>
          @php
            $totQty = 0;
            $totThans = 0;
            $totWaste = 0;
          @endphp
          @forelse($assign->items as $idx => $it)
            @php
              $q = (float)($it->than_meters > 0 ? $it->than_meters : ($it->qty ?? 0));
              $th = (int)($it->than_count > 0 ? $it->than_count : (count($it->than_list ?? []) ?: 1));
              $w = (float)$it->wastage_meters;
              $totQty += $q;
              $totThans += $th;
              $totWaste += $w;
            @endphp
            <tr style="border-bottom:1px solid #e2e8f0;">
              <td style="padding:12px 14px; text-align:center; color:#64748b;">{{ $idx + 1 }}</td>
              <td style="padding:12px 14px; font-weight:700; color:#0f172a;">
                {{ $it->raw_item_name ?: ($it->rawItem?->name ?? 'Raw Fabric') }}
              </td>
              <td style="padding:12px 14px; font-weight:700; color:#2563eb;">
                {{ $it->finished_item_name ?: ($it->finishedItem?->name ?? $assign->style_name) }}
              </td>
              <td style="padding:12px 14px; text-align:right; font-weight:800; color:#0f172a;">{{ number_format($q, 2) }}</td>
              <td style="padding:12px 14px; text-align:center;">
                <span class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-weight:700;">
                  {{ $th }}
                </span>
              </td>
              <td style="padding:12px 14px; text-align:right; font-family:monospace; color:#d97706;">{{ number_format($w, 2) }}</td>
              <td style="padding:12px 14px; text-align:center; color:#475569;">
                {{ $it->due_date ? date('d M Y', strtotime($it->due_date)) : ($assign->due_date ? date('d M Y', strtotime($assign->due_date)) : '—') }}
              </td>
            </tr>
          @empty
            <tr style="border-bottom:1px solid #e2e8f0;">
              <td style="padding:12px 14px; text-align:center;">1</td>
              <td style="padding:12px 14px; font-weight:700;">Fabric (Total Lot Issue)</td>
              <td style="padding:12px 14px; font-weight:700; color:#2563eb;">{{ $assign->style_name }}</td>
              <td style="padding:12px 14px; text-align:right; font-weight:800;">{{ number_format($assign->total_than_meters, 2) }}</td>
              <td style="padding:12px 14px; text-align:center;">{{ $assign->total_thans ?: 1 }}</td>
              <td style="padding:12px 14px; text-align:right; color:#d97706;">{{ number_format($assign->total_wastage_meters, 2) }}</td>
              <td style="padding:12px 14px; text-align:center;">{{ $assign->due_date ? date('d M Y', strtotime($assign->due_date)) : '—' }}</td>
            </tr>
            @php
              $totQty = (float)$assign->total_than_meters;
              $totThans = (int)($assign->total_thans ?: 1);
              $totWaste = (float)$assign->total_wastage_meters;
            @endphp
          @endforelse
        </tbody>
        <tfoot style="background:#f8fafc; font-weight:800; border-top:2px solid #0f172a; border-bottom:2px solid #0f172a;">
          <tr>
            <td colspan="3" style="padding:12px 14px; text-align:right;">TOTAL:</td>
            <td style="padding:12px 14px; text-align:right; color:#2563eb; font-size:1rem;">{{ number_format($totQty, 2) }}</td>
            <td style="padding:12px 14px; text-align:center; color:#475569;">{{ $totThans }}</td>
            <td style="padding:12px 14px; text-align:right; color:#d97706;">{{ number_format($totWaste, 2) }}</td>
            <td></td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- Summary & Instructions -->
    <div style="display:grid; grid-template-columns:1.5fr 1fr; gap:24px; align-items:start;">
      <div>
        @if($assign->remarks)
          <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px 16px;">
            <div style="font-size:0.75rem; text-transform:uppercase; font-weight:800; color:#64748b; margin-bottom:4px;">Remark / Stitching Instructions</div>
            <div style="font-size:0.85rem; color:#334155;">{{ $assign->remarks }}</div>
          </div>
        @endif
      </div>

      <div style="background:#f8fafc; border:1px solid #cbd5e1; border-radius:8px; padding:16px 20px;">
        <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:0.9rem;">
          <span style="color:#64748b;">T. Raw material:</span>
          <strong style="font-family:monospace; color:#2563eb;">{{ number_format($totQty, 2) }} MTR</strong>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:0.9rem;">
          <span style="color:#64748b;">Total Than:</span>
          <strong>{{ $totThans }}</strong>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:0.9rem;">
          <span style="color:#64748b;">Total Expect waste:</span>
          <strong style="color:#d97706;">{{ number_format($totWaste, 2) }} MTR</strong>
        </div>
        <div style="display:flex; justify-content:space-between; border-top:2px solid #0f172a; padding-top:10px; font-size:1.1rem;">
          <span style="font-weight:900; color:#0f172a;">Est. Finished Goods:</span>
          <strong style="font-weight:900; color:#059669; font-family:monospace;">
            {{ $assign->issued_qty ?: max(1, floor(($totQty - $totWaste) / 1.5)) }} PCS
          </strong>
        </div>
      </div>
    </div>

    <!-- Sign-off lines -->
    <div style="display:flex; justify-content:space-between; margin-top:50px; padding-top:20px; border-top:1px dashed #cbd5e1; font-size:0.8rem; color:#64748b;">
      <div>Issued By: ___________________</div>
      <div>Contractor Receiver: ___________________</div>
      <div>Production Supervisor: ___________________</div>
    </div>

  </div>

</div>
@endsection
