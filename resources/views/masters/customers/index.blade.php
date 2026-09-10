@extends('layouts.app')

@section('title', 'Customer Master - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Masters Management</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Customer Master</span></div>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:20px;">

  <!-- Top KPI Dynamic Metrics Row -->
  <div class="kpi-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
    
    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:var(--primary-50); color:var(--primary-600); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Total Customers</div>
        <div style="font-size:1.45rem; font-weight:800; color:var(--slate-900); margin-top:2px;">
          {{ $stats['total'] }} <span style="font-size:0.8rem; font-weight:600; color:var(--success-600); background:#ecfdf5; padding:2px 8px; border-radius:12px; border:1px solid #a7f3d0;">{{ $stats['active'] }} Active</span>
        </div>
      </div>
    </div>

    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:#fef2f2; color:var(--danger-600); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Total Outstanding</div>
        <div style="font-size:1.45rem; font-weight:800; color:var(--danger-600); margin-top:2px;">₹{{ number_format($stats['totalOutstanding'], 2) }}</div>
      </div>
    </div>

    <div class="kpi-card" style="background:#ffffff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); padding:18px; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:16px;">
      <div style="width:46px; height:46px; border-radius:12px; background:#f0fdf4; color:var(--success-600); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
      </div>
      <div>
        <div style="font-size:0.75rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Approved Credit Limit</div>
        <div style="font-size:1.45rem; font-weight:800; color:var(--slate-800); margin-top:2px;">₹{{ number_format($stats['totalCreditLimit'], 2) }}</div>
      </div>
    </div>

  </div>

  <!-- Main Card Container -->
  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:20px;">
    
    <!-- Action Header & Search Toolbar -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:14px; margin-bottom:20px;">
      <div>
        <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Customer Directory</h3>
        <p style="margin:2px 0 0; font-size:0.8rem; color:var(--slate-500);">Commercial buyers, retail distributors, export clients & billing terms</p>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('customers-table', 'Customer_Master.csv')" style="display:inline-flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Export CSV
        </button>
        <button class="btn btn-primary btn-sm" onclick="openCustomerModal()" style="display:inline-flex; align-items:center; gap:6px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
          + Add Customer
        </button>
      </div>
    </div>

    <!-- Quick Filter Bar -->
    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:16px;">
      <input type="text" id="cust-filter-input" class="form-control" style="max-width:280px; font-size:0.85rem;" placeholder="Search name, phone, GSTIN, city..." onkeyup="UI.filterGenericTable('customers-table', this.value)">
    </div>

    <!-- Customer Data Table -->
    <div class="table-responsive">
      <table class="data-table" id="customers-table" style="width:100%; font-size:0.85rem;">
        <thead>
          <tr>
            <th>Code</th>
            <th>Customer / Firm</th>
            <th>Contact Person</th>
            <th>Phone / Email</th>
            <th>GSTIN</th>
            <th>City / State</th>
            <th>Credit Limit</th>
            <th>Outstanding</th>
            <th>Status</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($customers as $c)
            <tr>
              <td style="font-family:var(--font-mono); font-weight:700; color:var(--primary-600);">{{ $c->code }}</td>
              <td>
                <div style="font-weight:700; color:var(--slate-800);">{{ $c->name }}</div>
                @if($c->company_name)
                  <div style="font-size:0.75rem; color:var(--slate-500);">{{ $c->company_name }}</div>
                @endif
              </td>
              <td>{{ $c->contact_person ?: '—' }}</td>
              <td>
                <div style="font-weight:600;">{{ $c->phone }}</div>
                @if($c->email)
                  <div style="font-size:0.75rem; color:var(--slate-500);">{{ $c->email }}</div>
                @endif
              </td>
              <td style="font-family:var(--font-mono); font-size:0.8rem;">{{ $c->gstin ?: '—' }}</td>
              <td>{{ $c->city ? $c->city . ($c->state ? ', ' . $c->state : '') : '—' }}</td>
              <td style="font-weight:600;">₹{{ number_format($c->credit_limit, 2) }}</td>
              <td style="font-weight:700; color:{{ $c->outstanding > 0 ? 'var(--danger-600)' : 'var(--success-600)' }};">
                ₹{{ number_format($c->outstanding, 2) }}
              </td>
              <td>
                @if($c->status === 'Active')
                  <span class="badge badge-success">Active</span>
                @elseif($c->status === 'Blocked')
                  <span class="badge badge-danger">Blocked</span>
                @else
                  <span class="badge badge-secondary">{{ $c->status }}</span>
                @endif
              </td>
              <td style="text-align:right;">
                <div style="display:inline-flex; gap:6px;">
                  <button class="btn btn-secondary btn-xs" onclick='openCustomerModal(@json($c))' title="Edit Customer">
                    Edit
                  </button>
                  <form action="{{ route('masters.customers.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Delete customer {{ $c->name }}?')" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-xs" title="Delete">
                      &times;
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="10" style="text-align:center; padding:30px; color:var(--slate-400);">
                No customer records found. Click "+ Add Customer" to create the first customer.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

</div>

<!-- Add / Edit Customer Modal -->
<div id="customer-modal" class="modal-backdrop" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
  <div class="modal-box" style="background:#fff; border-radius:16px; width:100%; max-width:650px; padding:24px; max-height:90vh; overflow-y:auto; box-shadow:0 20px 25px -5px rgba(0,0,0,0.2);">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid var(--slate-200); padding-bottom:12px;">
      <h3 id="modal-title" style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Add New Customer</h3>
      <button onclick="closeCustomerModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--slate-400);">&times;</button>
    </div>

    <form id="customer-form" method="POST" action="{{ route('masters.customers.store') }}">
      @csrf
      <input type="hidden" name="_method" id="form-method" value="POST">

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
        <div class="form-group">
          <label class="form-label">Customer / Trading Name <span style="color:red;">*</span></label>
          <input type="text" name="name" id="cust_name" class="form-control" required placeholder="e.g. Zara Apparels Ltd.">
        </div>

        <div class="form-group">
          <label class="form-label">Phone / Mobile <span style="color:red;">*</span></label>
          <input type="text" name="phone" id="cust_phone" class="form-control" required placeholder="e.g. +91 98765 43210">
        </div>

        <div class="form-group">
          <label class="form-label">Company / Legal Name</label>
          <input type="text" name="company_name" id="cust_company" class="form-control" placeholder="e.g. Zara Retail Pvt. Ltd.">
        </div>

        <div class="form-group">
          <label class="form-label">Contact Person</label>
          <input type="text" name="contact_person" id="cust_contact" class="form-control" placeholder="e.g. Rajesh Mehta">
        </div>

        <div class="form-group">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" id="cust_email" class="form-control" placeholder="e.g. contact@zara.com">
        </div>

        <div class="form-group">
          <label class="form-label">GSTIN / Tax ID</label>
          <input type="text" name="gstin" id="cust_gstin" class="form-control" placeholder="e.g. 27AAAAA0000A1Z5">
        </div>

        <div class="form-group">
          <label class="form-label">City</label>
          <input type="text" name="city" id="cust_city" class="form-control" placeholder="e.g. Mumbai">
        </div>

        <div class="form-group">
          <label class="form-label">State</label>
          <input type="text" name="state" id="cust_state" class="form-control" placeholder="e.g. Maharashtra">
        </div>

        <div class="form-group">
          <label class="form-label">Credit Limit (₹)</label>
          <input type="number" step="0.01" name="credit_limit" id="cust_credit" class="form-control" value="100000.00">
        </div>

        <div class="form-group">
          <label class="form-label">Status</label>
          <select name="status" id="cust_status" class="form-control">
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
            <option value="Blocked">Blocked</option>
          </select>
        </div>
      </div>

      <div class="form-group" style="margin-top:14px;">
        <label class="form-label">Billing & Shipping Address</label>
        <textarea name="address" id="cust_address" class="form-control" rows="2" placeholder="Street address, industrial area, PIN..."></textarea>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px; border-top:1px solid var(--slate-200); padding-top:14px;">
        <button type="button" class="btn btn-secondary" onclick="closeCustomerModal()">Cancel</button>
        <button type="submit" class="btn btn-primary" id="submit-btn">Save Customer</button>
      </div>
    </form>

  </div>
</div>

@push('scripts')
<script>
  function openCustomerModal(c = null) {
    const modal = document.getElementById('customer-modal');
    const form = document.getElementById('customer-form');
    const methodInput = document.getElementById('form-method');
    const title = document.getElementById('modal-title');

    if (c) {
      title.innerText = 'Edit Customer: ' + (c.code || c.name);
      form.action = `/masters/customers/${c.id}`;
      methodInput.value = 'PUT';

      document.getElementById('cust_name').value = c.name || '';
      document.getElementById('cust_phone').value = c.phone || '';
      document.getElementById('cust_company').value = c.company_name || '';
      document.getElementById('cust_contact').value = c.contact_person || '';
      document.getElementById('cust_email').value = c.email || '';
      document.getElementById('cust_gstin').value = c.gstin || '';
      document.getElementById('cust_city').value = c.city || '';
      document.getElementById('cust_state').value = c.state || '';
      document.getElementById('cust_credit').value = c.credit_limit || 0;
      document.getElementById('cust_status').value = c.status || 'Active';
      document.getElementById('cust_address').value = c.address || '';
      document.getElementById('submit-btn').innerText = 'Update Customer';
    } else {
      title.innerText = 'Add New Customer';
      form.action = '{{ route('masters.customers.store') }}';
      methodInput.value = 'POST';
      form.reset();
      document.getElementById('cust_credit').value = '100000.00';
      document.getElementById('cust_status').value = 'Active';
      document.getElementById('submit-btn').innerText = 'Save Customer';
    }

    modal.style.display = 'flex';
  }

  function closeCustomerModal() {
    document.getElementById('customer-modal').style.display = 'none';
  }
</script>
@endpush
@endsection
