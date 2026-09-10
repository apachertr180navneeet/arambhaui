@extends('layouts.app')

@section('title', 'Purchase Orders - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Purchase Management</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Purchase Orders</span></div>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <!-- KPI Metrics Row -->
  <div class="kpi-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Total Orders Issued</div>
        <div style="font-size:1.45rem; font-weight:800; color:var(--slate-900); margin-top:2px;">{{ $stats['totalOrders'] }}</div>
      </div>
    </div>

    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Cumulative PO Value</div>
        <div style="font-size:1.45rem; font-weight:800; color:#059669; margin-top:2px;">₹{{ number_format($stats['totalAmount'], 2) }}</div>
      </div>
    </div>

    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:#faf5ff; color:#9333ea; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Approved POs</div>
        <div style="font-size:1.45rem; font-weight:800; color:var(--slate-900); margin-top:2px;">{{ $stats['approvedCount'] }} Approved</div>
      </div>
    </div>
  </div>

  <!-- Main Card Container -->
  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:20px;">
    
    <!-- Action Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:20px;">
      <div>
        <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Purchase Orders Registry</h3>
        <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">Procurement orders for greige fabric, finished rolls, trims & accessories</p>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('po-table', 'Purchase_Orders.csv')" style="display:inline-flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Export CSV
        </button>
        <a href="{{ route('purchase.orders.create') }}" class="btn btn-primary btn-sm" style="display:inline-flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
          + Create Purchase Order
        </a>
      </div>
    </div>

    <!-- Quick Filter Bar -->
    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:16px;">
      <input type="text" class="form-control" style="max-width:280px; font-size:0.85rem;" placeholder="Search PO#, vendor name, warehouse..." onkeyup="UI.filterGenericTable('po-table', this.value)">
    </div>

    <!-- Purchase Orders Table -->
    <div class="table-responsive">
      <table class="data-table" id="po-table" style="width:100%; font-size:0.85rem;">
        <thead>
          <tr>
            <th>PO Number</th>
            <th>Vendor / Supplier</th>
            <th>PO Date</th>
            <th>Expected Date</th>
            <th>Warehouse</th>
            <th>Line Items</th>
            <th>Grand Total</th>
            <th>Status</th>
            <th>Payment</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($orders as $po)
            <tr>
              <td style="font-family:var(--font-mono); font-weight:700; color:var(--primary-600);">{{ $po->po_number }}</td>
              <td style="font-weight:700; color:var(--slate-800);">{{ $po->vendor_name }}</td>
              <td>{{ date('d M Y', strtotime($po->po_date)) }}</td>
              <td>{{ $po->expected_delivery_date ? date('d M Y', strtotime($po->expected_delivery_date)) : '—' }}</td>
              <td style="font-size:0.8rem; color:var(--slate-600);">{{ $po->warehouse_location }}</td>
              <td><span class="badge badge-info">{{ $po->items->count() }} Items</span></td>
              <td style="font-weight:800; color:#059669;">₹{{ number_format($po->grand_total, 2) }}</td>
              <td><span class="badge badge-success">{{ $po->status }}</span></td>
              <td>
                <span class="badge {{ $po->payment_status === 'Paid' ? 'badge-success' : 'badge-warning' }}">
                  {{ $po->payment_status }}
                </span>
              </td>
              <td style="text-align:right;">
                <div style="display:inline-flex; gap:6px;">
                  <a href="{{ route('purchase.orders.edit', $po->id) }}" class="btn btn-secondary btn-xs">Edit</a>
                  <button class="btn btn-secondary btn-xs" onclick='printPurchaseOrder(@json($po))'>Print</button>
                  <form action="{{ route('purchase.orders.destroy', $po->id) }}" method="POST" onsubmit="return confirm('Delete PO {{ $po->po_number }}?')" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-xs">&times;</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="10" style="text-align:center; padding:30px; color:var(--slate-400);">
                No purchase orders generated yet. Click "+ Create Purchase Order" to issue a procurement order.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>

<!-- Printable Section Hidden Container -->
<div id="print-po-area" style="display:none;"></div>

@push('scripts')
<script>
  function printPurchaseOrder(po) {
    let linesHtml = (po.items || []).map((itm, idx) => `
      <tr>
        <td style="padding:8px; border:1px solid #e2e8f0; text-align:center;">${idx + 1}</td>
        <td style="padding:8px; border:1px solid #e2e8f0; font-weight:600;">${itm.item_name}</td>
        <td style="padding:8px; border:1px solid #e2e8f0; text-align:center;">${itm.ordered_qty} ${itm.unit || 'm'}</td>
        <td style="padding:8px; border:1px solid #e2e8f0; text-align:right;">₹${Number(itm.rate).toFixed(2)}</td>
        <td style="padding:8px; border:1px solid #e2e8f0; text-align:right;">${itm.tax_percent}%</td>
        <td style="padding:8px; border:1px solid #e2e8f0; text-align:right; font-weight:700;">₹${Number(itm.total_amount).toFixed(2)}</td>
      </tr>
    `).join('');

    const html = `
      <div style="font-family:'Plus Jakarta Sans', sans-serif; padding:30px; color:#1e293b; max-width:800px; margin:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:2px solid #0f172a; padding-bottom:16px; margin-bottom:20px;">
          <div>
            <h2 style="margin:0; font-size:1.5rem; color:#0f172a; font-weight:800;">FashionWorks Pvt. Ltd.</h2>
            <p style="margin:2px 0 0; font-size:0.85rem; color:#64748b;">Apparel Park MIDC, Tiruppur / Mumbai | GSTIN: 27AABCF1234F1Z1</p>
          </div>
          <div style="text-align:right;">
            <h3 style="margin:0; font-size:1.25rem; color:#4f46e5; font-weight:800;">PURCHASE ORDER</h3>
            <p style="margin:2px 0 0; font-size:0.85rem; font-family:monospace; font-weight:700;">${po.po_number}</p>
          </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px; font-size:0.85rem;">
          <div style="padding:12px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px;">
            <div style="font-size:0.75rem; color:#64748b; font-weight:700; text-transform:uppercase;">Vendor / Supplier:</div>
            <div style="font-weight:700; font-size:1rem; margin-top:4px;">${po.vendor_name}</div>
          </div>
          <div style="padding:12px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px;">
            <div><strong>PO Date:</strong> ${po.po_date}</div>
            <div><strong>Delivery Location:</strong> ${po.warehouse_location || 'Main Store'}</div>
          </div>
        </div>

        <table style="width:100%; border-collapse:collapse; font-size:0.85rem; margin-bottom:20px;">
          <thead>
            <tr style="background:#f1f5f9;">
              <th style="padding:8px; border:1px solid #e2e8f0;">#</th>
              <th style="padding:8px; border:1px solid #e2e8f0; text-align:left;">Item Description</th>
              <th style="padding:8px; border:1px solid #e2e8f0;">Qty</th>
              <th style="padding:8px; border:1px solid #e2e8f0; text-align:right;">Rate</th>
              <th style="padding:8px; border:1px solid #e2e8f0; text-align:right;">Tax %</th>
              <th style="padding:8px; border:1px solid #e2e8f0; text-align:right;">Total Amount</th>
            </tr>
          </thead>
          <tbody>${linesHtml}</tbody>
          <tfoot>
            <tr>
              <td colspan="5" style="padding:8px; text-align:right; font-weight:700; border:1px solid #e2e8f0;">Subtotal:</td>
              <td style="padding:8px; text-align:right; font-weight:700; border:1px solid #e2e8f0;">₹${Number(po.subtotal).toFixed(2)}</td>
            </tr>
            <tr>
              <td colspan="5" style="padding:8px; text-align:right; font-weight:700; border:1px solid #e2e8f0;">GST / Tax:</td>
              <td style="padding:8px; text-align:right; font-weight:700; border:1px solid #e2e8f0;">₹${Number(po.tax_total).toFixed(2)}</td>
            </tr>
            <tr style="background:#f8fafc;">
              <td colspan="5" style="padding:10px; text-align:right; font-weight:800; font-size:1rem; border:1px solid #e2e8f0;">Grand Total:</td>
              <td style="padding:10px; text-align:right; font-weight:800; font-size:1rem; color:#059669; border:1px solid #e2e8f0;">₹${Number(po.grand_total).toFixed(2)}</td>
            </tr>
          </tfoot>
        </table>
      </div>
    `;

    const printArea = document.getElementById('print-po-area');
    printArea.innerHTML = html;
    UI.printSection('print-po-area');
  }
</script>
@endpush
@endsection
