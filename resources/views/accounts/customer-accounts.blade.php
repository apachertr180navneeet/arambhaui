@extends('layouts.app')

@section('title', 'Customer Accounts & Settlements - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Accounts & Settlements</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Customer Settlements</span></div>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <!-- KPI Metrics Row -->
  <div class="kpi-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:#fef2f2; color:var(--danger-600); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Receivable Outstanding</div>
        <div style="font-size:1.45rem; font-weight:800; color:var(--danger-600); margin-top:2px;">₹{{ number_format($stats['totalOutstanding'], 2) }}</div>
      </div>
    </div>

    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Settled Payments Received</div>
        <div style="font-size:1.45rem; font-weight:800; color:#059669; margin-top:2px;">₹{{ number_format($stats['totalCollected'], 2) }}</div>
      </div>
    </div>
  </div>

  <!-- Main Card Container -->
  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:20px;">
    
    <!-- Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:20px;">
      <div>
        <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Customer Settlement Balances</h3>
        <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">Record NEFT, Cheque, RTGS or UPI client payments and reconcile invoices</p>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('settlements-table', 'Customer_Settlements.csv')" style="display:inline-flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Export CSV
        </button>
        <button class="btn btn-primary btn-sm" onclick="openReceiptModal()" style="display:inline-flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
          + Receive Customer Payment
        </button>
      </div>
    </div>

    <!-- Quick Filter Bar -->
    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:16px;">
      <input type="text" class="form-control" style="max-width:280px; font-size:0.85rem;" placeholder="Search customer, city, balance..." onkeyup="UI.filterGenericTable('settlements-table', this.value)">
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="data-table" id="settlements-table" style="width:100%; font-size:0.85rem;">
        <thead>
          <tr>
            <th>Code</th>
            <th>Customer / Firm</th>
            <th>City / State</th>
            <th>Credit Limit</th>
            <th>Current Outstanding</th>
            <th>Status</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($customers as $c)
            <tr>
              <td style="font-family:var(--font-mono); font-weight:700; color:var(--primary-600);">{{ $c->code }}</td>
              <td style="font-weight:700; color:var(--slate-800);">{{ $c->name }}</td>
              <td>{{ $c->city ? $c->city . ($c->state ? ', ' . $c->state : '') : '—' }}</td>
              <td>₹{{ number_format($c->credit_limit, 2) }}</td>
              <td style="font-weight:800; color:{{ $c->outstanding > 0 ? 'var(--danger-600)' : 'var(--success-600)' }};">
                ₹{{ number_format($c->outstanding, 2) }}
              </td>
              <td><span class="badge badge-success">{{ $c->status }}</span></td>
              <td style="text-align:right;">
                <button class="btn btn-primary btn-xs" onclick='openReceiptModalFor("{{ $c->name }}", {{ $c->outstanding }})'>Receive Payment</button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align:center; padding:30px; color:var(--slate-400);">No customer ledger balances found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>

<!-- Receive Payment Modal -->
<div id="receipt-modal" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div class="modal-box" style="background:#fff; border-radius:16px; width:100%; max-width:550px; padding:24px; max-height:90vh; overflow-y:auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid var(--slate-200); padding-bottom:12px;">
      <h3 style="margin:0; font-size:1.15rem; font-weight:800;">Record Customer Payment Receipt</h3>
      <button onclick="closeReceiptModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--slate-400);">&times;</button>
    </div>

    <form method="POST" action="{{ route('accounts.receipt.store') }}">
      @csrf

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
        <div class="form-group" style="grid-column:1/-1;">
          <label class="form-label">Customer / Debtor <span style="color:red;">*</span></label>
          <select name="customer_name" id="rec_customer" class="form-control" required>
            <option value="">-- Select Customer --</option>
            @foreach($customers as $c)
              <option value="{{ $c->name }}">{{ $c->name }} (Due: ₹{{ number_format($c->outstanding, 2) }})</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Payment Date <span style="color:red;">*</span></label>
          <input type="date" name="payment_date" class="form-control" required value="{{ date('Y-m-d') }}">
        </div>

        <div class="form-group">
          <label class="form-label">Amount Received (₹) <span style="color:red;">*</span></label>
          <input type="number" step="0.01" name="amount" id="rec_amount" class="form-control" required min="1" placeholder="e.g. 50000.00">
        </div>

        <div class="form-group">
          <label class="form-label">Payment Mode <span style="color:red;">*</span></label>
          <select name="payment_mode" class="form-control" required>
            <option value="NEFT / RTGS">NEFT / RTGS Online</option>
            <option value="Cheque">Bank Cheque</option>
            <option value="UPI">UPI / QR Transfer</option>
            <option value="Cash">Cash Receipt</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Transaction / Cheque #</label>
          <input type="text" name="reference_no" class="form-control" placeholder="e.g. UTR / CHQ-104928">
        </div>

        <div class="form-group" style="grid-column:1/-1;">
          <label class="form-label">Deposited Bank Account</label>
          <input type="text" name="bank_name" class="form-control" placeholder="e.g. HDFC Bank - Current A/c 502000...">
        </div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px; border-top:1px solid var(--slate-200); padding-top:14px;">
        <button type="button" class="btn btn-secondary" onclick="closeReceiptModal()">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Payment Receipt</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  function openReceiptModal() {
    document.getElementById('receipt-modal').style.display = 'flex';
  }
  function openReceiptModalFor(custName, due) {
    document.getElementById('receipt-modal').style.display = 'flex';
    document.getElementById('rec_customer').value = custName;
    if (due > 0) document.getElementById('rec_amount').value = due;
  }
  function closeReceiptModal() {
    document.getElementById('receipt-modal').style.display = 'none';
  }
</script>
@endpush
@endsection
