@extends('layouts.app')

@section('title', 'Job Inward Entries - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><a href="{{ route('jobwork.assign.index') }}" style="color:inherit; text-decoration:none;">Job Work & Assign</a></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Job Inward Entries</span></div>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <!-- Top Action Header -->
  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
    <div>
      <div style="display:flex; align-items:center; gap:10px;">
        <h2 style="margin:0; font-size:1.45rem; font-weight:800; color:var(--slate-900); letter-spacing:-0.02em;">Job Inward Entries</h2>
        <span style="font-size:0.75rem; font-weight:800; background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; padding:3px 10px; border-radius:9999px;">
          FINISHED GOODS & FABRIC INWARD
        </span>
      </div>
      <p style="margin:4px 0 0; font-size:0.85rem; color:var(--slate-500);">
        Receive multi-item finished garments and processed fabric with Than-wise breakdown, QC inspection, defect rejections, and inventory stock-in.
      </p>
    </div>

    <div style="display:flex; gap:10px; align-items:center;">
      <a href="{{ route('jobwork.inward-report') }}" class="btn btn-secondary" style="font-weight:700; display:inline-flex; align-items:center; gap:6px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
        Inward & QC Report
      </a>
      <a href="{{ route('jobwork.inward.create') }}" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:6px; font-weight:700; box-shadow:0 2px 8px rgba(5, 150, 105, 0.3); background:#059669; border-color:#059669;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        + New Inward Entry
      </a>
    </div>
  </div>

  @if(session('success'))
    <div style="background:#f0fdf4; border:1px solid #86efac; border-radius:12px; padding:12px 18px; color:#166534; font-weight:700; font-size:0.9rem; display:flex; align-items:center; gap:8px;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      {{ session('success') }}
    </div>
  @endif

  <!-- KPI Summary Cards -->
  <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
    
    <div class="card" style="padding:18px 20px; background:#fff; border-radius:14px; border:1px solid var(--slate-200); box-shadow:var(--shadow-sm);">
      <span style="font-size:0.75rem; font-weight:700; color:var(--slate-500); text-transform:uppercase;">Total Finished Inward</span>
      <div style="font-size:1.6rem; font-weight:800; color:#059669; margin:4px 0 2px;">{{ number_format($stats['totalInwardQty']) }} Pcs</div>
      <span style="font-size:0.75rem; color:#16a34a; font-weight:600;">Ready Garments Received</span>
    </div>

    <div class="card" style="padding:18px 20px; background:#fff; border-radius:14px; border:1px solid var(--slate-200); box-shadow:var(--shadow-sm);">
      <span style="font-size:0.75rem; font-weight:700; color:var(--slate-500); text-transform:uppercase;">QC Rejections</span>
      <div style="font-size:1.6rem; font-weight:800; color:#dc2626; margin:4px 0 2px;">{{ number_format($stats['totalDefectQty']) }} Pcs</div>
      <span style="font-size:0.75rem; color:#ef4444; font-weight:600;">Defect / Rework Pieces</span>
    </div>

    <div class="card" style="padding:18px 20px; background:#fff; border-radius:14px; border:1px solid var(--slate-200); box-shadow:var(--shadow-sm);">
      <span style="font-size:0.75rem; font-weight:700; color:var(--slate-500); text-transform:uppercase;">Total Inward Receipts</span>
      <div style="font-size:1.6rem; font-weight:800; color:var(--primary-700); margin:4px 0 2px;">{{ number_format($stats['totalEntries']) }} Receipts</div>
      <span style="font-size:0.75rem; color:var(--primary-600); font-weight:600;">Batch Deliveries</span>
    </div>

    <div class="card" style="padding:18px 20px; background:#fff; border-radius:14px; border:1px solid var(--slate-200); box-shadow:var(--shadow-sm);">
      <span style="font-size:0.75rem; font-weight:700; color:var(--slate-500); text-transform:uppercase;">Fabric Wastage Returned</span>
      <div style="font-size:1.6rem; font-weight:800; color:#475569; margin:4px 0 2px;">{{ number_format($stats['totalWastageReturned'], 2) }} Mtr</div>
      <span style="font-size:0.75rem; color:#64748b; font-weight:600;">Challan Returns</span>
    </div>

  </div>

  <!-- Inward Receipts Table Card -->
  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:20px;">
    
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
      <input type="text" class="form-control" style="max-width:320px; font-size:0.85rem;" placeholder="Search inward#, lot#, worker, challan..." onkeyup="UI.filterGenericTable('inward-entries-table', this.value)">
    </div>

    <div class="table-responsive">
      <table class="data-table" id="inward-entries-table" style="width:100%; font-size:0.85rem;">
        <thead>
          <tr>
            <th>Inward Receipt #</th>
            <th>Inward Date</th>
            <th>Job Worker Contractor</th>
            <th>Lot / Batch #</th>
            <th>Job Order #</th>
            <th>Style / Fabric</th>
            <th style="text-align:center;">Thans / Meters</th>
            <th style="text-align:right;">Good Pcs</th>
            <th style="text-align:right;">Defect Pcs</th>
            <th style="text-align:right;">Wastage (Mtr)</th>
            <th>QC Status</th>
            <th style="text-align:right;">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($inwards as $inw)
            @php
              $thanList = $inw->than_list;
              $thanCount = count($thanList);
              $totalMeters = $thanCount > 0 ? array_sum($thanList) : 0;
            @endphp
            <tr>
              <td style="font-family:var(--font-mono, monospace); font-weight:800; color:var(--primary-700);">
                {{ $inw->inward_number }}
                @if($inw->challan_no)
                  <div style="font-size:0.72rem; color:var(--slate-400);">DC: {{ $inw->challan_no }}</div>
                @endif
              </td>
              <td>{{ date('d M Y', strtotime($inw->inward_date)) }}</td>
              <td style="font-weight:700; color:var(--slate-800);">
                {{ $inw->job_worker_name }}
                <div style="font-size:0.72rem; color:var(--slate-400);">{{ $inw->process_name }}</div>
              </td>
              <td style="font-family:var(--font-mono, monospace); font-weight:700; color:#4338ca;">
                {{ $inw->lot_number }}
              </td>
              <td style="font-family:var(--font-mono, monospace); color:var(--slate-600);">
                {{ $inw->job_order_no }}
              </td>
              <td style="max-width:160px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                {{ $inw->style_name ?: 'Garment Item' }}
              </td>
              <td style="text-align:center;">
                @if($thanCount > 0)
                  <span class="badge" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; font-weight:800;">
                    {{ $thanCount }} Thans ({{ number_format($totalMeters, 2) }} M)
                  </span>
                @else
                  <span style="color:var(--slate-400);">—</span>
                @endif
              </td>
              <td style="text-align:right; font-family:var(--font-mono, monospace); font-weight:800; color:#059669; font-size:0.95rem;">
                +{{ number_format($inw->received_qty) }} Pcs
              </td>
              <td style="text-align:right; font-family:var(--font-mono, monospace); font-weight:700; color:{{ $inw->defect_qty > 0 ? '#dc2626' : 'var(--slate-400)' }};">
                {{ $inw->defect_qty > 0 ? $inw->defect_qty . ' Pcs' : '0' }}
              </td>
              <td style="text-align:right; font-family:var(--font-mono, monospace); color:var(--slate-700);">
                {{ number_format($inw->wastage_returned_meters, 2) }} Mtr
              </td>
              <td>
                <span class="badge" style="background:{{ $inw->qc_status === 'Passed QC' ? '#ecfdf5' : '#fef2f2' }}; color:{{ $inw->qc_status === 'Passed QC' ? '#059669' : '#dc2626' }}; border:1px solid {{ $inw->qc_status === 'Passed QC' ? '#a7f3d0' : '#fecaca' }}; font-weight:700; padding:3px 8px; border-radius:9999px;">
                  ● {{ $inw->qc_status }}
                </span>
              </td>
              <td style="text-align:right;">
                <div style="display:inline-flex; align-items:center; gap:4px;">
                  <button type="button" class="action-icon-btn" onclick='printInwardSlip(@json($inw), @json($thanList), @json($inw->items_list))' title="Print Inward Slip & Than Matrix">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                  </button>
                  <form action="{{ route('jobwork.inward.destroy', $inw->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this inward entry? Pending quantities on the Job Order will be restored.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-icon-btn danger" title="Delete Inward Entry">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="12" style="text-align:center; padding:40px 20px; color:var(--slate-400);">
                <div style="font-size:1rem; font-weight:700; color:var(--slate-600); margin-bottom:4px;">No Job Inwards Recorded Yet</div>
                <p style="font-size:0.85rem; margin:0 0 14px;">When contractors finish stitching garments, click "+ New Inward Entry" to receive them into stock.</p>
                <a href="{{ route('jobwork.inward.create') }}" class="btn btn-primary btn-sm">+ New Inward Entry</a>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($inwards->hasPages())
      <div style="margin-top:16px;">
        {{ $inwards->links() }}
      </div>
    @endif

  </div>

</div>

<!-- Inward Print Slip Modal -->
<div id="print-slip-modal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(15, 23, 42, 0.6); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
  <div style="background:#fff; border-radius:16px; padding:24px; width:90%; max-width:680px; max-height:90vh; overflow-y:auto; box-shadow:0 20px 25px -5px rgba(0,0,0,0.2);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid #e2e8f0; padding-bottom:12px;">
      <h3 style="margin:0; font-weight:800; font-size:1.15rem; color:#0f172a;" id="print-modal-title">Job Inward Delivery Slip</h3>
      <div style="display:flex; gap:8px;">
        <button type="button" class="btn btn-primary btn-sm" onclick="triggerSlipPrint()" style="background:#059669; border-color:#059669; font-weight:700;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
          Print Slip
        </button>
        <button type="button" onclick="closePrintSlipModal()" class="btn btn-secondary btn-sm">Close</button>
      </div>
    </div>

    <div id="printable-slip-content" style="padding:10px; font-family:var(--font-sans, sans-serif); color:#0f172a;"></div>
  </div>
</div>

@push('scripts')
<script>
  function printInwardSlip(inw, thans, items) {
    const modal = document.getElementById('print-slip-modal');
    const container = document.getElementById('printable-slip-content');
    if (!modal || !container) return;

    let itemsRows = '';
    if (items && items.length > 0) {
      items.forEach((itm, idx) => {
        itemsRows += `
          <tr>
            <td style="padding:8px 10px; border:1px solid #cbd5e1;">#${idx + 1} ${itm.item_name}</td>
            <td style="padding:8px 10px; border:1px solid #cbd5e1; text-align:center;">${(itm.thans && itm.thans.length) || 0} Thans</td>
            <td style="padding:8px 10px; border:1px solid #cbd5e1; text-align:right; font-weight:700;">${(itm.total_meters || 0).toFixed(2)} Mtr</td>
            <td style="padding:8px 10px; border:1px solid #cbd5e1; text-align:right; color:#059669; font-weight:800;">${itm.received_qty || 0} Pcs</td>
            <td style="padding:8px 10px; border:1px solid #cbd5e1; text-align:right; color:#dc2626;">${itm.defect_qty || 0} Pcs</td>
            <td style="padding:8px 10px; border:1px solid #cbd5e1; text-align:right;">₹${(itm.rate || 0).toFixed(2)}</td>
            <td style="padding:8px 10px; border:1px solid #cbd5e1; text-align:right; font-weight:800;">₹${(itm.line_total || 0).toFixed(2)}</td>
          </tr>
        `;
      });
    }

    let thanMatrix = '';
    if (thans && thans.length > 0) {
      let pills = '';
      thans.forEach((t, i) => {
        pills += `<span style="display:inline-block; margin:3px; padding:3px 8px; background:#f1f5f9; border:1px solid #cbd5e1; border-radius:6px; font-family:monospace; font-size:0.8rem; font-weight:700;"><span style="color:#64748b; font-size:0.7rem;">#${i+1}:</span> ${t} M</span>`;
      });
      thanMatrix = `
        <div style="margin-top:16px; border:1px solid #cbd5e1; border-radius:10px; padding:12px; background:#fafafa;">
          <div style="font-weight:800; font-size:0.8rem; text-transform:uppercase; color:#475569; margin-bottom:8px;">Than / Roll Meter Readings Breakdown (${thans.length} Thans)</div>
          <div>${pills}</div>
        </div>
      `;
    }

    container.innerHTML = `
      <div style="border:2px solid #0f172a; padding:18px; border-radius:12px;">
        <div style="text-align:center; border-bottom:2px solid #0f172a; padding-bottom:12px; margin-bottom:14px;">
          <h2 style="margin:0; font-size:1.4rem; font-weight:900; letter-spacing:0.05em; text-transform:uppercase;">JOB WORK INWARD RECEIPT</h2>
          <p style="margin:3px 0 0; font-size:0.85rem; color:#475569;">Garment Manufacturing & Processing Inward Slip</p>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; font-size:0.85rem; margin-bottom:14px;">
          <div>
            <div><strong>Inward Receipt #:</strong> <span style="font-family:monospace; font-weight:800; color:#059669;">${inw.inward_number}</span></div>
            <div><strong>Contractor DC #:</strong> ${inw.challan_no || '—'}</div>
            <div><strong>Inward Date:</strong> ${inw.inward_date}</div>
            <div><strong>QC Status:</strong> <span style="font-weight:800; color:#059669;">${inw.qc_status}</span></div>
          </div>
          <div>
            <div><strong>Contractor:</strong> <strong>${inw.job_worker_name}</strong></div>
            <div><strong>Process:</strong> ${inw.process_name || '—'}</div>
            <div><strong>Lot / Batch #:</strong> <span style="font-family:monospace; font-weight:800; color:#4338ca;">${inw.lot_number}</span></div>
            <div><strong>Job Order #:</strong> ${inw.job_order_no}</div>
          </div>
        </div>

        ${itemsRows ? `
          <table style="width:100%; border-collapse:collapse; font-size:0.85rem; margin-bottom:12px;">
            <thead>
              <tr style="background:#f1f5f9;">
                <th style="padding:6px 10px; border:1px solid #cbd5e1; text-align:left;">Item / Fabric</th>
                <th style="padding:6px 10px; border:1px solid #cbd5e1;">Thans</th>
                <th style="padding:6px 10px; border:1px solid #cbd5e1; text-align:right;">Meters</th>
                <th style="padding:6px 10px; border:1px solid #cbd5e1; text-align:right;">Good Pcs</th>
                <th style="padding:6px 10px; border:1px solid #cbd5e1; text-align:right;">Defect</th>
                <th style="padding:6px 10px; border:1px solid #cbd5e1; text-align:right;">Rate</th>
                <th style="padding:6px 10px; border:1px solid #cbd5e1; text-align:right;">Total</th>
              </tr>
            </thead>
            <tbody>${itemsRows}</tbody>
          </table>
        ` : ''}

        ${thanMatrix}

        <div style="display:flex; justify-content:space-between; align-items:center; border-top:2px solid #0f172a; margin-top:16px; padding-top:10px; font-size:0.95rem;">
          <div>
            <div><strong>Total Good Finished Inward:</strong> ${inw.received_qty} Pcs</div>
            <div><strong>QC Defect / Rejected:</strong> ${inw.defect_qty} Pcs</div>
          </div>
          <div style="text-align:right;">
            <div style="font-size:1.15rem; font-weight:900; color:#059669;">Contractor Total: ₹${parseFloat(inw.total_amount || 0).toLocaleString('en-IN', {minimumFractionDigits: 2})}</div>
          </div>
        </div>
      </div>
    `;

    modal.style.display = 'flex';
  }

  function closePrintSlipModal() {
    document.getElementById('print-slip-modal').style.display = 'none';
  }

  function triggerSlipPrint() {
    const printContent = document.getElementById('printable-slip-content').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
      <html>
        <head>
          <title>Job Inward Slip</title>
          <style>
            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; padding: 20px; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #cbd5e1; padding: 6px 10px; }
          </style>
        </head>
        <body onload="window.print(); window.close();">
          ${printContent}
        </body>
      </html>
    `);
    printWindow.document.close();
  }
</script>
@endpush
@endsection
