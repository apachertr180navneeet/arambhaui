<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Job Work Register - {{ $companyName ?? 'aarambh' }}</title>
  <style>
    body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10px; color: #1e293b; margin: 0; padding: 15px; }
    .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #334155; padding-bottom: 10px; }
    .company-title { font-size: 18px; font-weight: bold; color: #0f172a; margin: 0; }
    .report-title { font-size: 13px; font-weight: bold; color: #4338ca; margin: 4px 0; text-transform: uppercase; letter-spacing: 0.05em; }
    .meta-line { font-size: 9px; color: #64748b; margin-top: 2px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th { background: #f1f5f9; color: #334155; font-size: 8.5px; font-weight: bold; text-transform: uppercase; padding: 6px 8px; border: 1px solid #cbd5e1; text-align: left; }
    td { padding: 6px 8px; border: 1px solid #e2e8f0; font-size: 9px; }
    .amt { text-align: right; font-family: 'Courier New', monospace; }
    .qty { text-align: right; font-family: 'Courier New', monospace; font-weight: bold; }
    .status-badge { padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 8px; }
    .status-pending { background: #fef3c7; color: #b45309; }
    .status-partial { background: #eff6ff; color: #1d4ed8; }
    .status-completed { background: #ecfdf5; color: #059669; }
    tfoot td { background: #f8fafc; font-weight: bold; border-top: 2px solid #94a3b8; }
  </style>
</head>
<body>

  <div class="header">
    <div class="company-title">{{ $companyName ?? 'aarambh' }}</div>
    <div class="report-title">Job Work Register Report</div>
    <div class="meta-line">
      Period: {{ $fromDate ?: 'All Dates' }} to {{ $toDate ?: 'Present' }} &bull; Generated: {{ now()->format('d-M-Y H:i') }}
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th style="width:75px;">Job Order #</th>
        <th style="width:65px;">Date</th>
        <th>Contractor</th>
        <th>Item Name</th>
        <th style="width:65px;">Item Code</th>
        <th style="width:55px; text-align:right;">Issued</th>
        <th style="width:55px; text-align:right;">Received</th>
        <th style="width:55px; text-align:right;">Pending</th>
        <th style="width:35px; text-align:center;">Unit</th>
        <th style="width:55px; text-align:right;">Rate (Rs)</th>
        <th style="width:65px; text-align:right;">Total (Rs)</th>
        <th style="width:60px; text-align:center;">Status</th>
      </tr>
    </thead>
    <tbody>
      @forelse($rows as $r)
        <tr>
          <td style="font-weight:bold; color:#4338ca;">{{ $r['job_order_no'] }}</td>
          <td>{{ $r['job_order_date'] }}</td>
          <td>{{ $r['job_worker_name'] }}</td>
          <td>{{ $r['item_name'] }}</td>
          <td>{{ $r['item_code'] }}</td>
          <td class="qty">{{ number_format($r['issued_qty']) }}</td>
          <td class="qty" style="color:#059669;">{{ number_format($r['received_qty']) }}</td>
          <td class="qty" style="color:{{ $r['remaining_qty'] > 0 ? '#b45309' : '#059669' }};">{{ number_format($r['remaining_qty']) }}</td>
          <td style="text-align:center;">{{ $r['unit'] }}</td>
          <td class="amt">{{ number_format($r['contractor_rate'], 2) }}</td>
          <td class="amt">{{ number_format($r['total_amount'], 2) }}</td>
          <td style="text-align:center;">
            <span class="status-badge {{ $r['status'] === 'Completed' ? 'status-completed' : ($r['status'] === 'Partial' ? 'status-partial' : 'status-pending') }}">
              {{ $r['status'] }}
            </span>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="12" style="text-align:center; padding:20px; color:#94a3b8;">No Job Work records found.</td>
        </tr>
      @endforelse
    </tbody>
    <tfoot>
      <tr>
        <td colspan="5" style="text-transform:uppercase;">TOTALS ({{ count($rows) }} entries)</td>
        <td class="qty">{{ number_format($totIssued) }}</td>
        <td class="qty" style="color:#059669;">{{ number_format($totRec) }}</td>
        <td class="qty" style="color:#b45309;">{{ number_format($totRem) }}</td>
        <td style="text-align:center;">PCS</td>
        <td></td>
        <td class="amt" style="color:#4338ca;">{{ number_format($totAmt, 2) }}</td>
        <td></td>
      </tr>
    </tfoot>
  </table>

</body>
</html>

