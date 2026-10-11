@extends('layouts.app')

@section('title', 'Purchase Bill & Payment Mapping - aarambh')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Purchasing</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Payment Mapping</span></div>
@endsection

@push('styles')
<style>
  .pm-container {
    display: flex;
    flex-direction: column;
    gap: 18px;
  }
  .pm-top-card {
    background: #ffffff;
    border-radius: var(--radius-xl, 16px);
    border: 1px solid var(--slate-200, #e2e8f0);
    padding: 18px 22px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
  }
  .pm-metrics-bar {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px;
    margin-top: 14px;
  }
  .pm-metric-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 16px;
    display: flex;
    flex-direction: column;
    gap: 4px;
  }
  .pm-metric-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.04em;
  }
  .pm-metric-val {
    font-size: 1.3rem;
    font-weight: 800;
    font-family: var(--font-mono, monospace);
  }

  /* Two-Column Split Layout */
  .pm-split-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    align-items: start;
  }
  @media (max-width: 992px) {
    .pm-split-grid {
      grid-template-columns: 1fr;
    }
  }

  .pm-panel {
    background: #ffffff;
    border-radius: var(--radius-xl, 16px);
    border: 1px solid var(--slate-200, #e2e8f0);
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    overflow: hidden;
    display: flex;
    flex-direction: column;
  }
  .pm-panel-header {
    background: #0f172a;
    color: #ffffff;
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
  }
  .pm-panel-title {
    font-size: 1rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
  }
  .pm-panel-toolbar {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 12px 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
  }

  /* Item cards within panels */
  .pm-scroll-list {
    padding: 14px 18px;
    overflow-y: auto;
    max-height: 580px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    background: #f8fafc;
  }
  .pm-card-item {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 16px;
    cursor: pointer;
    transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
  }
  .pm-card-item:hover {
    border-color: #cbd5e1;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.06);
  }
  .pm-card-item.selected {
    border-color: #4f46e5;
    background: #f5f3ff;
    box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.25);
  }
  .pm-card-item.selected::before {
    content: '';
    position: absolute;
    left: 0;
    top: 10px;
    bottom: 10px;
    width: 4px;
    background: #4f46e5;
    border-radius: 0 4px 4px 0;
  }

  /* Status Badges */
  .badge-unpaid {
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fecaca;
    font-weight: 800;
    font-size: 0.725rem;
    padding: 2px 8px;
    border-radius: 6px;
  }
  .badge-partially {
    background: #fef3c7;
    color: #d97706;
    border: 1px solid #fde68a;
    font-weight: 800;
    font-size: 0.725rem;
    padding: 2px 8px;
    border-radius: 6px;
  }
  .badge-paid {
    background: #dcfce7;
    color: #16a34a;
    border: 1px solid #bbf7d0;
    font-weight: 800;
    font-size: 0.725rem;
    padding: 2px 8px;
    border-radius: 6px;
  }

  /* Allocation Box */
  .pm-alloc-box {
    background: #ffffff;
    border: 2px dashed #c7d2fe;
    border-radius: 14px;
    padding: 16px;
    margin: 14px 18px 0;
  }
  .active-filter-btn {
    background-color: #0f172a !important;
    color: #ffffff !important;
    border-color: #0f172a !important;
  }
</style>
@endpush

@section('content')
<div class="pm-container">

  <!-- Top Supplier Selector & Performance Header -->
  <div class="pm-top-card">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px;">
      
      <!-- Supplier Selector -->
      <div style="display:flex; align-items:center; gap:12px; flex:1; min-width:280px; max-width:520px;">
        <span style="width:40px; height:40px; border-radius:10px; background:#eef2ff; color:#4338ca; display:inline-flex; align-items:center; justify-content:center; font-size:1.15rem; font-weight:800; flex-shrink:0;">
          🏢
        </span>
        <div style="flex:1;">
          <label style="display:block; font-size:0.75rem; font-weight:800; color:#64748b; text-transform:uppercase; margin-bottom:3px;">
            Select Supplier / Party:
          </label>
          <form method="GET" action="{{ route('purchase.payment-mapping') }}" id="vendor-select-form">
            <select name="vendor_id" class="form-control" style="font-weight:800; font-size:0.95rem; height:42px; border-radius:10px; border-color:#cbd5e1;" onchange="document.getElementById('vendor-select-form').submit()">
              @foreach($vendors as $v)
                <option value="{{ $v->id }}" {{ $selectedVendorId == $v->id ? 'selected' : '' }}>
                  {{ $v->name }} ({{ $v->code ?: 'Vendor' }}) {{ $v->city ? " - {$v->city}" : '' }}
                </option>
              @endforeach
            </select>
          </form>
        </div>
      </div>

      <!-- Quick Action Buttons -->
      <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        @if($selectedVendor)
          <a href="{{ route('purchase.ledger', ['vendor_id' => $selectedVendor->id]) }}" class="btn btn-secondary btn-sm" style="font-weight:700; display:inline-flex; align-items:center; gap:6px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            View Tally Ledger
          </a>
        @endif

        <button type="button" class="btn btn-primary btn-sm" onclick="openNewPaymentModal()" style="font-weight:800; background:#0284c7; border-color:#0284c7; display:inline-flex; align-items:center; gap:6px; box-shadow:0 2px 8px rgba(2, 132, 199, 0.3);">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
          + Record Outgoing Payment
        </button>
      </div>

    </div>

    <!-- Metrics Row -->
    <div class="pm-metrics-bar">
      <div class="pm-metric-box">
        <span class="pm-metric-label">Total Purchase Bills</span>
        <span class="pm-metric-val" style="color:#0f172a;">₹{{ number_format($metrics['totalBillsAmount'], 2) }}</span>
        <span style="font-size:0.75rem; color:#64748b;">{{ $bills->count() }} Bills recorded</span>
      </div>

      <div class="pm-metric-box">
        <span class="pm-metric-label">Total Allocated Payments</span>
        <span class="pm-metric-val" style="color:#059669;">₹{{ number_format($metrics['totalPaidAmount'], 2) }}</span>
        <span style="font-size:0.75rem; color:#059669; font-weight:700;">{{ $metrics['paidBillsCount'] }} Bills fully paid</span>
      </div>

      <div class="pm-metric-box" style="background:#fff1f2; border-color:#fecaca;">
        <span class="pm-metric-label" style="color:#e11d48;">Net Outstanding Payable</span>
        <span class="pm-metric-val" style="color:#e11d48;">₹{{ number_format($metrics['netOutstanding'], 2) }}</span>
        <span style="font-size:0.75rem; color:#e11d48; font-weight:700;">{{ $metrics['unpaidBillsCount'] + $metrics['partiallyPaidCount'] }} Bills pending balance</span>
      </div>

      <div class="pm-metric-box" style="background:#f0fdf4; border-color:#bbf7d0;">
        <span class="pm-metric-label" style="color:#16a34a;">Unallocated Payment Cash</span>
        <span class="pm-metric-val" style="color:#16a34a;">₹{{ number_format($metrics['totalUnallocatedPayments'], 2) }}</span>
        <span style="font-size:0.75rem; color:#16a34a; font-weight:700;">Available for mapping</span>
      </div>
    </div>
  </div>

  <!-- SPLIT SCREEN: Left Panel (Bills) | Right Panel (Payments & Allocation) -->
  <div class="pm-split-grid">
    
    <!-- ==========================================
         LEFT PANEL (50% WIDTH): PURCHASE BILLS
         ========================================== -->
    <div class="pm-panel">
      <!-- Header -->
      <div class="pm-panel-header">
        <div class="pm-panel-title">
          <span>📑</span>
          <span>Purchase Bills</span>
          <span style="font-size:0.775rem; background:rgba(255,255,255,0.18); padding:2px 8px; border-radius:12px; font-weight:700;">
            {{ $bills->count() }}
          </span>
        </div>
        <div style="font-size:0.78rem; color:#94a3b8;">
          Click a bill below to map payment
        </div>
      </div>

      <!-- Search & Status Filter Toolbar -->
      <div class="pm-panel-toolbar">
        <div style="position:relative; flex:1; min-width:180px;">
          <input type="text" id="bill-search-input" class="form-control" placeholder="Search Bill / Challan #..." style="height:34px; font-size:0.8rem; border-radius:8px;" oninput="filterBills()">
        </div>
        <div style="display:flex; gap:4px;">
          <button type="button" class="btn btn-secondary btn-xs active-filter-btn" id="filter-bill-all" onclick="setBillStatusFilter('all')">All</button>
          <button type="button" class="btn btn-secondary btn-xs" id="filter-bill-pending" onclick="setBillStatusFilter('pending')">Unpaid</button>
          <button type="button" class="btn btn-secondary btn-xs" id="filter-bill-partially" onclick="setBillStatusFilter('partially')">Partial</button>
          <button type="button" class="btn btn-secondary btn-xs" id="filter-bill-paid" onclick="setBillStatusFilter('paid')">Paid</button>
        </div>
      </div>

      <!-- Scrollable List of Purchase Bills -->
      <div class="pm-scroll-list" id="bills-container">
        @forelse($bills as $bill)
          @php
            $billPaid = (float) $bill->allocations->sum('allocated_amount');
            $billOutstanding = max(0, round($bill->grand_total - $billPaid, 2));
            $billStatus = 'pending';
            if ($billOutstanding <= 0.001) {
                $billStatus = 'paid';
            } elseif ($billPaid > 0.001) {
                $billStatus = 'partially';
            }
            $billNo = $bill->challan_no ?: $bill->po_number;
          @endphp
          <div class="pm-card-item bill-card" 
               id="bill-card-{{ $bill->id }}"
               data-id="{{ $bill->id }}"
               data-bill-no="{{ $billNo }}"
               data-total="{{ $bill->grand_total }}"
               data-paid="{{ $billPaid }}"
               data-outstanding="{{ $billOutstanding }}"
               data-status="{{ $billStatus }}"
               data-search="{{ strtolower($billNo . ' ' . $bill->po_date . ' ' . $bill->grand_total) }}"
               onclick="selectBill({{ $bill->id }})">
            
            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">
              <div>
                <div style="display:flex; align-items:center; gap:8px;">
                  <span style="font-weight:800; font-size:0.95rem; font-family:var(--font-mono, monospace); color:#0f172a;">
                    {{ $billNo }}
                  </span>
                  @if($billStatus === 'paid')
                    <span class="badge-paid">PAID</span>
                  @elseif($billStatus === 'partially')
                    <span class="badge-partially">PARTIAL</span>
                  @else
                    <span class="badge-unpaid">UNPAID</span>
                  @endif
                </div>
                <div style="font-size:0.75rem; color:#64748b; margin-top:2px;">
                  Date: <strong>{{ date('d-M-Y', strtotime($bill->po_date)) }}</strong>
                  @if($bill->items_summary)
                    &bull; {{ $bill->items_summary }}
                  @endif
                </div>
              </div>

              <!-- Amounts -->
              <div style="text-align:right;">
                <div style="font-size:0.75rem; color:#64748b; font-weight:600;">Bill Total: ₹{{ number_format($bill->grand_total, 2) }}</div>
                <div style="font-size:1.05rem; font-weight:800; font-family:var(--font-mono, monospace); color:{{ $billOutstanding > 0 ? '#dc2626' : '#16a34a' }};">
                  {{ $billOutstanding > 0 ? 'Due: ₹' . number_format($billOutstanding, 2) : 'Settled ✓' }}
                </div>
                @if($billPaid > 0)
                  <div style="font-size:0.725rem; color:#059669; font-weight:700;">
                    Paid: ₹{{ number_format($billPaid, 2) }}
                  </div>
                @endif
              </div>
            </div>

            <!-- Linked Payments info if any -->
            @if($bill->allocations->isNotEmpty())
              <div style="margin-top:8px; padding-top:6px; border-top:1px dashed #e2e8f0; font-size:0.725rem; color:#475569; display:flex; flex-wrap:wrap; gap:6px;">
                <span style="font-weight:700;">Mapped to:</span>
                @foreach($bill->allocations as $al)
                  <span style="background:#eef2ff; color:#4338ca; padding:1px 6px; border-radius:4px; font-weight:700; border:1px solid #c7d2fe;">
                    {{ $al->payment ? $al->payment->payment_no : 'Payment' }}: ₹{{ number_format($al->allocated_amount, 2) }}
                  </span>
                @endforeach
              </div>
            @endif

          </div>
        @empty
          <div style="text-align:center; padding:40px 16px; color:#94a3b8;">
            <div style="font-size:2rem; margin-bottom:6px;">📑</div>
            <div style="font-weight:800; font-size:0.95rem; color:#334155;">No Purchase Bills for this Supplier</div>
            <div style="font-size:0.8rem; margin-top:4px;">Create purchase orders or challans to map payments.</div>
          </div>
        @endforelse
      </div>

    </div>

    <!-- ==========================================
         RIGHT PANEL (50% WIDTH): PAYMENT DETAILS & ALLOCATION
         ========================================== -->
    <div class="pm-panel">
      <!-- Header -->
      <div class="pm-panel-header">
        <div class="pm-panel-title">
          <span>💳</span>
          <span>Payment Details & Allocation</span>
          <span style="font-size:0.775rem; background:rgba(255,255,255,0.18); padding:2px 8px; border-radius:12px; font-weight:700;">
            {{ $payments->count() }} Payments
          </span>
        </div>
        <button type="button" class="btn btn-primary btn-xs" onclick="openNewPaymentModal()" style="background:#0284c7; border-color:#0284c7; font-weight:700;">
          + New Payment
        </button>
      </div>

      <!-- Live Allocation Workbench Box -->
      <div class="pm-alloc-box" id="allocation-workbench-box">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; border-bottom:1px solid #e0e7ff; padding-bottom:8px;">
          <span style="font-weight:800; font-size:0.85rem; color:#3730a3; text-transform:uppercase; letter-spacing:0.04em;">
            ⚡ Payment Allocation Workbench
          </span>
          <span id="workbench-helper-badge" style="font-size:0.75rem; font-weight:700; color:#6366f1;">
            Select 1 Bill & 1 Payment
          </span>
        </div>

        <form method="POST" action="{{ route('purchase.payment-mapping.allocate') }}" id="allocation-form">
          @csrf
          <input type="hidden" name="vendor_payment_id" id="alloc_payment_id" value="">
          <input type="hidden" name="allocations[0][purchase_order_id]" id="alloc_bill_id" value="">

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:12px; font-size:0.8rem;">
            <!-- Selected Bill Summary -->
            <div style="background:#f8fafc; border:1px solid #cbd5e1; border-radius:8px; padding:8px 10px;">
              <div style="font-size:0.7rem; color:#64748b; font-weight:700; text-transform:uppercase;">Selected Bill:</div>
              <div style="font-weight:800; color:#0f172a;" id="wb-bill-name">None Selected</div>
              <div style="font-size:0.75rem; font-weight:700; color:#dc2626;" id="wb-bill-due">Due: ₹0.00</div>
            </div>

            <!-- Selected Payment Summary -->
            <div style="background:#f8fafc; border:1px solid #cbd5e1; border-radius:8px; padding:8px 10px;">
              <div style="font-size:0.7rem; color:#64748b; font-weight:700; text-transform:uppercase;">Selected Payment:</div>
              <div style="font-weight:800; color:#0f172a;" id="wb-pay-name">None Selected</div>
              <div style="font-size:0.75rem; font-weight:700; color:#16a34a;" id="wb-pay-avail">Avail: ₹0.00</div>
            </div>
          </div>

          <!-- Allocation Input & Button -->
          <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
            <div style="flex:1; min-width:140px;">
              <label style="display:block; font-size:0.725rem; font-weight:700; color:#475569; margin-bottom:2px;">
                Amount to Allocate (₹):
              </label>
              <input type="number" step="0.01" min="0.01" name="allocations[0][amount]" id="alloc_amount_input" class="form-control" placeholder="0.00" style="font-weight:800; font-size:0.95rem; height:38px; border-radius:8px;" oninput="validateAllocationInput()">
            </div>
            
            <div style="display:flex; gap:6px; align-items:flex-end; padding-top:16px;">
              <button type="button" class="btn btn-secondary btn-sm" onclick="autoFillMaxAllocation()" style="height:38px; font-weight:700; font-size:0.75rem;">
                Max Auto-Fill
              </button>
              <button type="submit" id="btn-confirm-allocation" class="btn btn-primary btn-sm" disabled style="height:38px; font-weight:800; background:#4338ca; border-color:#4338ca; box-shadow:0 2px 6px rgba(67, 56, 202, 0.3);">
                ✓ Confirm Allocation
              </button>
            </div>
          </div>

          <div id="alloc-feedback-msg" style="display:none; font-size:0.75rem; font-weight:700; margin-top:6px; border-radius:6px; padding:4px 8px;"></div>
        </form>
      </div>

      <!-- Scrollable List of Payments -->
      <div class="pm-scroll-list" id="payments-container" style="margin-top:10px;">
        @forelse($payments as $pay)
          @php
            $payAllocated = (float) $pay->allocations->sum('allocated_amount');
            $payUnallocated = max(0, round($pay->amount - $payAllocated, 2));
          @endphp
          <div class="pm-card-item payment-card" 
               id="pay-card-{{ $pay->id }}"
               data-id="{{ $pay->id }}"
               data-pay-no="{{ $pay->payment_no }}"
               data-total="{{ $pay->amount }}"
               data-allocated="{{ $payAllocated }}"
               data-unallocated="{{ $payUnallocated }}"
               onclick="selectPayment({{ $pay->id }})">
            
            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">
              <div>
                <div style="display:flex; align-items:center; gap:8px;">
                  <span style="font-weight:800; font-size:0.95rem; font-family:var(--font-mono, monospace); color:#0f172a;">
                    {{ $pay->payment_no }}
                  </span>
                  <span style="font-size:0.75rem; background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; font-weight:700; padding:1px 6px; border-radius:5px;">
                    {{ $pay->payment_mode }}
                  </span>
                </div>
                <div style="font-size:0.75rem; color:#64748b; margin-top:2px;">
                  Date: <strong>{{ date('d-M-Y', strtotime($pay->payment_date)) }}</strong>
                  @if($pay->reference_no)
                    &bull; Ref: <code style="font-weight:700;">{{ $pay->reference_no }}</code>
                  @endif
                  @if($pay->bank_name)
                    &bull; {{ $pay->bank_name }}
                  @endif
                </div>
              </div>

              <!-- Payment Totals -->
              <div style="text-align:right;">
                <div style="font-size:0.75rem; color:#64748b; font-weight:600;">Total: ₹{{ number_format($pay->amount, 2) }}</div>
                <div style="font-size:1.05rem; font-weight:800; font-family:var(--font-mono, monospace); color:{{ $payUnallocated > 0 ? '#16a34a' : '#64748b' }};">
                  {{ $payUnallocated > 0 ? 'Unmapped: ₹' . number_format($payUnallocated, 2) : 'Fully Mapped ✓' }}
                </div>
              </div>
            </div>

            <!-- Existing Allocations under this payment with Remove Option -->
            @if($pay->allocations->isNotEmpty())
              <div style="margin-top:10px; padding-top:8px; border-top:1px solid #f1f5f9;">
                <div style="font-size:0.725rem; font-weight:800; color:#475569; text-transform:uppercase; margin-bottom:4px;">
                  Linked Purchase Bills ({{ $pay->allocations->count() }}):
                </div>
                <div style="display:flex; flex-direction:column; gap:4px;">
                  @foreach($pay->allocations as $alloc)
                    @php
                      $linkedBillNo = $alloc->purchaseOrder ? ($alloc->purchaseOrder->challan_no ?: $alloc->purchaseOrder->po_number) : 'Bill #' . $alloc->purchase_order_id;
                    @endphp
                    <div style="display:flex; justify-content:space-between; align-items:center; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:4px 8px; font-size:0.775rem;">
                      <div>
                        <strong style="color:#1e293b;">{{ $linkedBillNo }}</strong>:
                        <span style="font-family:var(--font-mono, monospace); font-weight:800; color:#4338ca;">
                          ₹{{ number_format($alloc->allocated_amount, 2) }}
                        </span>
                      </div>
                      
                      <!-- Remove / Adjust Allocation Button -->
                      <form method="POST" action="{{ route('purchase.payment-mapping.remove-allocation', $alloc->id) }}" onsubmit="return confirm('Remove allocation of ₹{{ number_format($alloc->allocated_amount, 2) }} from {{ $linkedBillNo }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background:transparent; border:none; color:#ef4444; font-weight:800; font-size:0.75rem; cursor:pointer; padding:2px 6px;" title="Unlink allocation">
                          ✕ Remove
                        </button>
                      </form>
                    </div>
                  @endforeach
                </div>
              </div>
            @endif

          </div>
        @empty
          <div style="text-align:center; padding:40px 16px; color:#94a3b8;">
            <div style="font-size:2rem; margin-bottom:6px;">💳</div>
            <div style="font-weight:800; font-size:0.95rem; color:#334155;">No Outgoing Payments Recorded Yet</div>
            <p style="font-size:0.8rem; margin:4px 0 10px;">Click below to record a bank transfer, UPI, or cheque payment.</p>
            <button type="button" class="btn btn-primary btn-xs" onclick="openNewPaymentModal()" style="font-weight:700;">+ Record Payment</button>
          </div>
        @endforelse
      </div>

    </div>

  </div>

</div>

<!-- Modal: Record New Outgoing Payment -->
<div id="new-payment-modal" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.7); backdrop-filter:blur(3px); z-index:9999; align-items:center; justify-content:center;">
  <div style="background:#ffffff; border-radius:18px; width:92%; max-width:540px; padding:24px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); border:1px solid #cbd5e1; max-height:90vh; overflow-y:auto;">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid #e2e8f0; padding-bottom:12px;">
      <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:#0f172a; display:flex; align-items:center; gap:8px;">
        <span>💳</span> Record Outgoing Payment to Supplier
      </h3>
      <button type="button" onclick="closeNewPaymentModal()" style="background:none; border:none; font-size:1.5rem; color:#94a3b8; cursor:pointer; line-height:1;">&times;</button>
    </div>

    <form method="POST" action="{{ route('purchase.payment-mapping.store-payment') }}">
      @csrf

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
        
        <!-- Vendor Selection -->
        <div class="form-group" style="grid-column:1/-1; margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.8rem; margin-bottom:4px;">
            Supplier / Vendor <span style="color:#ef4444;">*</span>
          </label>
          <select name="vendor_id" class="form-control" required style="font-weight:700; height:40px;">
            @foreach($vendors as $v)
              <option value="{{ $v->id }}" {{ $selectedVendorId == $v->id ? 'selected' : '' }}>
                {{ $v->name }} ({{ $v->code ?: 'Vendor' }})
              </option>
            @endforeach
          </select>
        </div>

        <!-- Payment Date -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.8rem; margin-bottom:4px;">
            Payment Date <span style="color:#ef4444;">*</span>
          </label>
          <input type="date" name="payment_date" class="form-control" required value="{{ date('Y-m-d') }}" style="font-weight:700; height:40px;">
        </div>

        <!-- Payment Amount -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.8rem; margin-bottom:4px;">
            Payment Amount (₹) <span style="color:#ef4444;">*</span>
          </label>
          <input type="number" step="0.01" min="0.01" name="amount" class="form-control" required placeholder="e.g. 50000.00" style="font-weight:800; height:40px;">
        </div>

        <!-- Payment Mode -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.8rem; margin-bottom:4px;">
            Payment Method <span style="color:#ef4444;">*</span>
          </label>
          <select name="payment_mode" class="form-control" required style="font-weight:700; height:40px;">
            <option value="Bank Transfer (NEFT)">Bank Transfer (NEFT)</option>
            <option value="RTGS">RTGS</option>
            <option value="UPI">UPI / QR Code</option>
            <option value="Cheque">Cheque</option>
            <option value="Cash">Cash</option>
          </select>
        </div>

        <!-- Reference / UTR No -->
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.8rem; margin-bottom:4px;">
            Bank Ref / UTR / Cheque #
          </label>
          <input type="text" name="reference_no" class="form-control font-mono" placeholder="e.g. UTR12345678" style="font-weight:700; height:40px;">
        </div>

        <!-- Bank Name -->
        <div class="form-group" style="grid-column:1/-1; margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.8rem; margin-bottom:4px;">
            Drawn On Bank / Account
          </label>
          <input type="text" name="bank_name" class="form-control" placeholder="e.g. HDFC Bank Ltd - Current A/c" style="font-weight:600; height:40px;">
        </div>

        <!-- Notes -->
        <div class="form-group" style="grid-column:1/-1; margin-bottom:0;">
          <label class="form-label" style="font-weight:700; font-size:0.8rem; margin-bottom:4px;">
            Notes & Remarks
          </label>
          <textarea name="notes" class="form-control" rows="2" placeholder="Payment towards purchase invoice settlement..."></textarea>
        </div>

      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px; border-top:1px solid #e2e8f0; padding-top:14px;">
        <button type="button" class="btn btn-secondary" onclick="closeNewPaymentModal()" style="font-weight:700;">Cancel</button>
        <button type="submit" class="btn btn-primary" style="font-weight:800; background:#0284c7; border-color:#0284c7; padding:8px 22px;">
          ✓ Save Payment Record
        </button>
      </div>

    </form>

  </div>
</div>
@endsection

@push('scripts')
<script>
  let activeBillId = null;
  let activePaymentId = null;

  let activeBillData = null;
  let activePaymentData = null;

  function selectBill(billId) {
    activeBillId = billId;
    document.querySelectorAll('.bill-card').forEach(el => el.classList.remove('selected'));
    const card = document.getElementById(`bill-card-${billId}`);
    if (card) {
      card.classList.add('selected');
      activeBillData = {
        id: billId,
        billNo: card.getAttribute('data-bill-no'),
        total: parseFloat(card.getAttribute('data-total')) || 0,
        paid: parseFloat(card.getAttribute('data-paid')) || 0,
        outstanding: parseFloat(card.getAttribute('data-outstanding')) || 0
      };
    }
    updateWorkbenchState();
  }

  function selectPayment(paymentId) {
    activePaymentId = paymentId;
    document.querySelectorAll('.payment-card').forEach(el => el.classList.remove('selected'));
    const card = document.getElementById(`pay-card-${paymentId}`);
    if (card) {
      card.classList.add('selected');
      activePaymentData = {
        id: paymentId,
        payNo: card.getAttribute('data-pay-no'),
        total: parseFloat(card.getAttribute('data-total')) || 0,
        allocated: parseFloat(card.getAttribute('data-allocated')) || 0,
        unallocated: parseFloat(card.getAttribute('data-unallocated')) || 0
      };
    }
    updateWorkbenchState();
  }

  function updateWorkbenchState() {
    const wbBillName = document.getElementById('wb-bill-name');
    const wbBillDue = document.getElementById('wb-bill-due');
    const wbPayName = document.getElementById('wb-pay-name');
    const wbPayAvail = document.getElementById('wb-pay-avail');
    const helperBadge = document.getElementById('workbench-helper-badge');
    const confirmBtn = document.getElementById('btn-confirm-allocation');
    const amountInput = document.getElementById('alloc_amount_input');
    const inputBillId = document.getElementById('alloc_bill_id');
    const inputPayId = document.getElementById('alloc_payment_id');

    if (activeBillData) {
      wbBillName.textContent = activeBillData.billNo;
      wbBillDue.textContent = `Due: ₹${activeBillData.outstanding.toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2})}`;
      inputBillId.value = activeBillData.id;
    } else {
      wbBillName.textContent = 'None Selected';
      wbBillDue.textContent = 'Due: ₹0.00';
      inputBillId.value = '';
    }

    if (activePaymentData) {
      wbPayName.textContent = activePaymentData.payNo;
      wbPayAvail.textContent = `Avail: ₹${activePaymentData.unallocated.toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2})}`;
      inputPayId.value = activePaymentData.id;
    } else {
      wbPayName.textContent = 'None Selected';
      wbPayAvail.textContent = 'Avail: ₹0.00';
      inputPayId.value = '';
    }

    if (activeBillData && activePaymentData) {
      helperBadge.textContent = 'Ready to Allocate';
      helperBadge.style.color = '#16a34a';

      // Auto compute recommended allocation: min(bill.outstanding, payment.unallocated)
      const maxAlloc = Math.min(activeBillData.outstanding, activePaymentData.unallocated);
      if (maxAlloc > 0 && (!amountInput.value || parseFloat(amountInput.value) <= 0)) {
        amountInput.value = maxAlloc.toFixed(2);
      }
      validateAllocationInput();
    } else {
      helperBadge.textContent = 'Select 1 Bill & 1 Payment';
      helperBadge.style.color = '#6366f1';
      confirmBtn.disabled = true;
    }
  }

  function autoFillMaxAllocation() {
    if (!activeBillData || !activePaymentData) {
      alert('Please select a purchase bill on the left and a payment on the right first.');
      return;
    }
    const maxAlloc = Math.min(activeBillData.outstanding, activePaymentData.unallocated);
    const amountInput = document.getElementById('alloc_amount_input');
    amountInput.value = maxAlloc.toFixed(2);
    validateAllocationInput();
  }

  function validateAllocationInput() {
    const amountInput = document.getElementById('alloc_amount_input');
    const confirmBtn = document.getElementById('btn-confirm-allocation');
    const feedback = document.getElementById('alloc-feedback-msg');

    if (!activeBillData || !activePaymentData) {
      confirmBtn.disabled = true;
      feedback.style.display = 'none';
      return;
    }

    const val = parseFloat(amountInput.value) || 0;

    if (val <= 0) {
      confirmBtn.disabled = true;
      feedback.style.display = 'none';
      return;
    }

    if (val > activePaymentData.unallocated + 0.001) {
      confirmBtn.disabled = true;
      feedback.style.display = 'block';
      feedback.style.background = '#fef2f2';
      feedback.style.color = '#dc2626';
      feedback.textContent = `❌ Amount (₹${val.toFixed(2)}) exceeds unallocated payment amount (₹${activePaymentData.unallocated.toFixed(2)}).`;
      return;
    }

    if (val > activeBillData.outstanding + 0.001) {
      confirmBtn.disabled = true;
      feedback.style.display = 'block';
      feedback.style.background = '#fef2f2';
      feedback.style.color = '#dc2626';
      feedback.textContent = `❌ Amount (₹${val.toFixed(2)}) exceeds outstanding bill amount (₹${activeBillData.outstanding.toFixed(2)}).`;
      return;
    }

    confirmBtn.disabled = false;
    feedback.style.display = 'block';
    feedback.style.background = '#ecfdf5';
    feedback.style.color = '#047857';
    feedback.textContent = `✓ Valid allocation: ₹${val.toFixed(2)} will be applied from ${activePaymentData.payNo} to ${activeBillData.billNo}.`;
  }

  // Bill Filter Functions
  let currentBillStatusFilter = 'all';

  function setBillStatusFilter(status) {
    currentBillStatusFilter = status;
    document.querySelectorAll('.active-filter-btn').forEach(btn => btn.classList.remove('active-filter-btn'));
    const btn = document.getElementById(`filter-bill-${status}`);
    if (btn) btn.classList.add('active-filter-btn');
    filterBills();
  }

  function filterBills() {
    const query = (document.getElementById('bill-search-input')?.value || '').toLowerCase().trim();
    document.querySelectorAll('.bill-card').forEach(card => {
      const cardStatus = card.getAttribute('data-status');
      const searchData = card.getAttribute('data-search') || '';

      const matchStatus = (currentBillStatusFilter === 'all') || (cardStatus === currentBillStatusFilter);
      const matchSearch = !query || searchData.includes(query);

      card.style.display = (matchStatus && matchSearch) ? 'block' : 'none';
    });
  }

  // Modal handlers
  function openNewPaymentModal() {
    const modal = document.getElementById('new-payment-modal');
    if (modal) modal.style.display = 'flex';
  }

  function closeNewPaymentModal() {
    const modal = document.getElementById('new-payment-modal');
    if (modal) modal.style.display = 'none';
  }

  document.addEventListener('DOMContentLoaded', () => {
    // If bills are present and not selected, auto-select first pending bill
    const firstPendingBill = document.querySelector('.bill-card[data-status="pending"], .bill-card[data-status="partially"]');
    if (firstPendingBill) {
      firstPendingBill.click();
    } else {
      const anyBill = document.querySelector('.bill-card');
      if (anyBill) anyBill.click();
    }

    // Auto-select first payment with unallocated balance
    const firstAvailPay = Array.from(document.querySelectorAll('.payment-card')).find(c => {
      return (parseFloat(c.getAttribute('data-unallocated')) || 0) > 0;
    });
    if (firstAvailPay) {
      firstAvailPay.click();
    } else {
      const anyPay = document.querySelector('.payment-card');
      if (anyPay) anyPay.click();
    }
  });
</script>
@endpush
