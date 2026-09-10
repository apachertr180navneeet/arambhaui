@extends('layouts.app')

@section('title', 'Customer Master - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Masters Management</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Customer Master</span></div>
@endsection

@push('styles')
<style>
  /* Customer Master Modern UI Styles */
  .cm-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 16px;
  }

  .cm-stat-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid var(--slate-200);
    padding: 20px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    display: flex;
    align-items: center;
    gap: 16px;
    position: relative;
    overflow: hidden;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .cm-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.08);
    border-color: var(--slate-300);
  }

  .cm-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--stat-accent, var(--primary-500));
  }

  .cm-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .cm-stat-info {
    flex: 1;
    min-width: 0;
  }

  .cm-stat-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--slate-500);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 4px;
  }

  .cm-stat-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--slate-900);
    line-height: 1.2;
    display: flex;
    align-items: baseline;
    gap: 8px;
    flex-wrap: wrap;
  }

  .cm-main-card {
    background: #ffffff;
    border-radius: 18px;
    border: 1px solid var(--slate-200);
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    overflow: hidden;
  }

  .cm-card-header {
    padding: 22px 24px;
    border-bottom: 1px solid var(--slate-100);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    background: #ffffff;
  }

  .cm-toolbar {
    padding: 16px 24px;
    background: #f8fafc;
    border-bottom: 1px solid var(--slate-200);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    flex-wrap: wrap;
  }

  .cm-search-wrap {
    position: relative;
    flex: 1;
    min-width: 260px;
    max-width: 400px;
  }

  .cm-search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--slate-400);
    pointer-events: none;
  }

  .cm-search-input {
    width: 100%;
    padding: 9px 14px 9px 38px;
    border: 1px solid var(--slate-300);
    border-radius: 10px;
    font-size: 0.85rem;
    background: #ffffff;
    color: var(--slate-800);
    transition: all 0.2s ease;
  }

  .cm-search-input:focus {
    outline: none;
    border-color: var(--primary-500);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
  }

  .cm-filter-pills {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
  }

  .cm-filter-pill {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.775rem;
    font-weight: 600;
    color: var(--slate-600);
    background: #ffffff;
    border: 1px solid var(--slate-200);
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;
  }

  .cm-filter-pill:hover {
    border-color: var(--slate-400);
    color: var(--slate-900);
  }

  .cm-filter-pill.active {
    background: var(--primary-600);
    color: #ffffff;
    border-color: var(--primary-600);
    box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
  }

  /* Empty State Modern Box */
  .cm-empty-box {
    padding: 60px 24px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
  }

  .cm-empty-icon-ring {
    width: 72px;
    height: 72px;
    border-radius: 24px;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border: 1px solid #bfdbfe;
    color: var(--primary-600);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    box-shadow: 0 8px 16px -4px rgba(37, 99, 235, 0.15);
  }

  .cm-empty-features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    max-width: 780px;
    width: 100%;
    margin-top: 32px;
    text-align: left;
  }

  .cm-feature-card {
    background: #ffffff;
    border: 1px solid var(--slate-200);
    border-radius: 12px;
    padding: 16px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
  }

  .cm-avatar {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: #ffffff;
    font-weight: 700;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    letter-spacing: 0.5px;
  }

  /* Interactive Status Select Dropdown inside table */
  .cm-status-select {
    appearance: none;
    -webkit-appearance: none;
    padding: 4px 22px 4px 10px;
    border-radius: 14px;
    font-size: 0.75rem;
    font-weight: 700;
    border: 1px solid transparent;
    cursor: pointer;
    outline: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    background-repeat: no-repeat;
    background-position: right 6px center;
    background-size: 11px;
    display: inline-block;
  }

  .cm-status-select:focus {
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
  }

  .cm-status-select.active {
    background-color: #ecfdf5;
    color: #059669;
    border-color: #a7f3d0;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23059669' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  }

  .cm-status-select.blocked {
    background-color: #fef2f2;
    color: #dc2626;
    border-color: #fecaca;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23dc2626' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  }

  .cm-status-select.inactive {
    background-color: #f1f5f9;
    color: #64748b;
    border-color: #cbd5e1;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  }

  /* Modal Styling */
  #customer-modal, #delete-customer-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    z-index: 99999;
    align-items: center;
    justify-content: center;
    padding: 16px;
  }

  .cm-modal-box {
    background: #ffffff;
    border-radius: 20px;
    width: 100%;
    max-width: 680px;
    padding: 26px;
    max-height: 92vh;
    overflow-y: auto;
    box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.3);
    border: 1px solid var(--slate-200);
    position: relative;
    animation: cmModalFade 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  }

  @keyframes cmModalFade {
    from { opacity: 0; transform: scale(0.96) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
  }

  .cm-modal-section-title {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--slate-400);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin: 16px 0 10px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .cm-modal-section-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--slate-200);
  }

  .cm-row-transition {
    transition: all 0.3s ease;
  }
</style>
@endpush

@section('content')
<div style="display:flex; flex-direction:column; gap:22px;">

  <!-- Top KPI Dynamic Metrics Row -->
  <div class="cm-stats-grid">
    
    <!-- 1. Total Customers -->
    <div class="cm-stat-card" style="--stat-accent: #2563eb;">
      <div class="cm-stat-icon" style="background:#eff6ff; color:#2563eb;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
      </div>
      <div class="cm-stat-info">
        <div class="cm-stat-label">Total Customers</div>
        <div class="cm-stat-value">
          <span id="stat-total-count">{{ $stats['total'] }}</span>
          <span id="stat-active-badge" style="font-size:0.75rem; font-weight:700; color:#059669; background:#ecfdf5; padding:2px 8px; border-radius:12px; border:1px solid #a7f3d0;">
            <span id="stat-active-count">{{ $stats['active'] }}</span> Active
          </span>
        </div>
      </div>
    </div>

    <!-- 2. Operational Standing Ratio -->
    <div class="cm-stat-card" style="--stat-accent: #8b5cf6;">
      <div class="cm-stat-icon" style="background:#faf5ff; color:#7c3aed;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
          <polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
      </div>
      <div class="cm-stat-info">
        <div class="cm-stat-label">Account Health</div>
        <div class="cm-stat-value">
          <span id="stat-health-percent">{{ $stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100) : 100 }}%</span>
          <span style="font-size:0.75rem; font-weight:600; color:var(--slate-500);">Operational</span>
        </div>
      </div>
    </div>

    <!-- 3. Total Outstanding -->
    <div class="cm-stat-card" style="--stat-accent: #ef4444;">
      <div class="cm-stat-icon" style="background:#fef2f2; color:#dc2626;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="1" x2="12" y2="23"/>
          <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
        </svg>
      </div>
      <div class="cm-stat-info">
        <div class="cm-stat-label">Total Outstanding</div>
        <div class="cm-stat-value" id="stat-total-outstanding" style="color:{{ $stats['totalOutstanding'] > 0 ? '#dc2626' : '#059669' }};">
          ₹{{ number_format($stats['totalOutstanding'], 2) }}
        </div>
      </div>
    </div>

    <!-- 4. Approved Credit Limit -->
    <div class="cm-stat-card" style="--stat-accent: #10b981;">
      <div class="cm-stat-icon" style="background:#ecfdf5; color:#059669;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <rect width="20" height="14" x="2" y="5" rx="2"/>
          <line x1="2" y1="10" x2="22" y2="10"/>
        </svg>
      </div>
      <div class="cm-stat-info">
        <div class="cm-stat-label">Approved Credit Facility</div>
        <div class="cm-stat-value" id="stat-total-credit" style="color:var(--slate-800);">
          ₹{{ number_format($stats['totalCreditLimit'], 2) }}
        </div>
      </div>
    </div>

  </div>

  <!-- Main Card Container -->
  <div class="cm-main-card">
    
    <!-- Action Header -->
    <div class="cm-card-header">
      <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:40px; height:40px; border-radius:10px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </div>
        <div>
          <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900); display:flex; align-items:center; gap:8px;">
            Customer Directory
            <span class="badge badge-primary" id="customer-badge-count" style="font-size:0.75rem; padding:2px 8px; border-radius:12px;">{{ count($customers) }} Records</span>
          </h3>
          <p style="margin:3px 0 0; font-size:0.8rem; color:var(--slate-500);">Commercial buyers, retail distributors, export clients & billing terms</p>
        </div>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('customers-table', 'Customer_Master.csv')" style="display:inline-flex; align-items:center; gap:6px; font-weight:600;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
          </svg>
          Export CSV
        </button>

        <button class="btn btn-primary btn-sm" onclick="openCustomerModal()" style="display:inline-flex; align-items:center; gap:6px; font-weight:700; background:linear-gradient(135deg, #2563eb, #1d4ed8); box-shadow:0 4px 10px rgba(37,99,235,0.25); cursor:pointer;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
          </svg>
          Add Customer
        </button>
      </div>
    </div>

    <!-- Quick Filter Bar -->
    <div class="cm-toolbar">
      <div class="cm-search-wrap">
        <svg class="cm-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"/>
          <line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" id="cust-filter-input" class="cm-search-input" placeholder="Search name, phone, GSTIN, city..." onkeyup="filterCustomerTable(this.value)">
      </div>

      <div class="cm-filter-pills">
        <button type="button" class="cm-filter-pill active" id="pill-all" onclick="applyCustomerFilter('all', this)">All (<span id="pill-count-all">{{ $stats['total'] }}</span>)</button>
        <button type="button" class="cm-filter-pill" id="pill-active" onclick="applyCustomerFilter('active', this)">Active (<span id="pill-count-active">{{ $stats['active'] }}</span>)</button>
        <button type="button" class="cm-filter-pill" onclick="applyCustomerFilter('blocked', this)">Blocked</button>
        <button type="button" class="cm-filter-pill" onclick="applyCustomerFilter('balance', this)">With Balance</button>
      </div>
    </div>

    <!-- Empty State Box (Toggled if 0 records) -->
    <div id="customers-empty-state" class="cm-empty-box" style="{{ $customers->isEmpty() ? 'display:flex;' : 'display:none;' }}">
      <div class="cm-empty-icon-ring">
        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <line x1="19" y1="8" x2="19" y2="14"/>
          <line x1="22" y1="11" x2="16" y2="11"/>
        </svg>
      </div>

      <h3 style="margin:0 0 8px; font-size:1.25rem; font-weight:800; color:var(--slate-900);">No Customers in Directory Yet</h3>
      <p style="margin:0 0 20px; font-size:0.875rem; color:var(--slate-500); max-width:480px; line-height:1.5;">
        Create customer profiles to manage billing addresses, GSTIN tax credentials, credit ceilings, and issue sales invoices seamlessly.
      </p>

      <button type="button" class="btn btn-primary" onclick="openCustomerModal()" style="display:inline-flex; align-items:center; gap:8px; padding:10px 22px; font-size:0.9rem; font-weight:700; border-radius:10px; background:linear-gradient(135deg, #2563eb, #1d4ed8); box-shadow:0 6px 16px rgba(37,99,235,0.25); cursor:pointer;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"/>
          <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Add Your First Customer
      </button>

      <!-- Feature cards -->
      <div class="cm-empty-features">
        <div class="cm-feature-card">
          <div style="width:34px; height:34px; border-radius:8px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          </div>
          <div>
            <div style="font-weight:700; font-size:0.825rem; color:var(--slate-800);">GSTIN & State Compliance</div>
            <div style="font-size:0.75rem; color:var(--slate-500); margin-top:2px;">Multi-state tax filing and GSTIN structure verification</div>
          </div>
        </div>

        <div class="cm-feature-card">
          <div style="width:34px; height:34px; border-radius:8px; background:#fef2f2; color:#dc2626; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
          </div>
          <div>
            <div style="font-weight:700; font-size:0.825rem; color:var(--slate-800);">Credit Limits & Balance Alerts</div>
            <div style="font-size:0.75rem; color:var(--slate-500); margin-top:2px;">Automated block rules when outstanding exceeds credit limits</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Data Table Container -->
    <div class="table-responsive" id="customers-table-container" style="{{ $customers->isEmpty() ? 'display:none;' : '' }}">
      <table class="data-table" id="customers-table">
        <thead>
          <tr>
            <th style="width: 50px; text-align: center;">#</th>
            <th>Customer Name & Code</th>
            <th>Contact Person</th>
            <th>Location & State</th>
            <th>Tax Info (GSTIN)</th>
            <th style="text-align: right;">Credit Limit</th>
            <th style="text-align: right;">Outstanding</th>
            <th style="text-align: center; width: 130px;">Status</th>
            <th style="text-align: center; width: 100px;">Actions</th>
          </tr>
        </thead>
        <tbody id="customers-table-body">
          @foreach($customers as $index => $customer)
            <tr class="cm-row cm-row-transition" id="customer-row-{{ $customer->id }}" data-id="{{ $customer->id }}" data-status="{{ strtolower($customer->status ?? 'active') }}" data-balance="{{ $customer->outstanding ?? $customer->outstanding_balance ?? 0 }}">
              <td class="row-index" style="text-align: center; font-weight: 600; color: var(--slate-400);">{{ $index + 1 }}</td>
              <td>
                <div style="display:flex; align-items:center; gap:10px;">
                  <div class="cm-avatar" id="avatar-{{ $customer->id }}">
                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                  </div>
                  <div>
                    <div class="cust-name-val" style="font-weight:700; color:var(--slate-900); font-size:0.9rem;">{{ $customer->name }}</div>
                    <div class="cust-code-val" style="font-size:0.75rem; color:var(--slate-500); font-family:monospace;">{{ $customer->code ?? ('CUST-'.str_pad($customer->id, 4, '0', STR_PAD_LEFT)) }}</div>
                  </div>
                </div>
              </td>
              <td>
                <div class="cust-contact-val" style="font-weight:600; color:var(--slate-800); font-size:0.85rem;">{{ $customer->contact_person ?? '—' }}</div>
                <div class="cust-phone-val" style="font-size:0.75rem; color:var(--slate-500);">{{ $customer->phone ?? $customer->mobile ?? '—' }}</div>
              </td>
              <td>
                <div class="cust-city-val" style="font-weight:600; color:var(--slate-700); font-size:0.85rem;">{{ $customer->city ?? '—' }}</div>
                <div class="cust-state-val" style="font-size:0.75rem; color:var(--slate-500);">{{ $customer->state ?? '—' }}</div>
              </td>
              <td class="cust-gst-cell">
                @if(!empty($customer->gst_number ?? $customer->gstin))
                  <span class="badge" style="background:#f1f5f9; color:#1e293b; font-family:monospace; font-size:0.75rem; font-weight:600; border:1px solid #e2e8f0;">
                    {{ $customer->gst_number ?? $customer->gstin }}
                  </span>
                @else
                  <span style="font-size:0.75rem; color:var(--slate-400);">Unregistered</span>
                @endif
              </td>
              <td class="cust-credit-val" style="text-align: right; font-weight: 600; color: var(--slate-700);">
                ₹{{ number_format($customer->credit_limit ?? 0, 2) }}
              </td>
              <td class="cust-outstanding-val" style="text-align: right; font-weight: 700; color: {{ ($customer->outstanding ?? $customer->outstanding_balance ?? 0) > 0 ? '#dc2626' : '#059669' }};">
                ₹{{ number_format($customer->outstanding ?? $customer->outstanding_balance ?? 0, 2) }}
              </td>
              <td style="text-align: center;">
                <!-- Direct Table Status Changer -->
                <select class="cm-status-select {{ strtolower($customer->status ?? 'active') }}" onchange="changeCustomerStatus({{ $customer->id }}, this.value, this)" title="Click to change status">
                  <option value="Active" {{ strtolower($customer->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                  <option value="Inactive" {{ strtolower($customer->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                  <option value="Blocked" {{ strtolower($customer->status ?? '') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                </select>
              </td>
              <td style="text-align: center;">
                <div style="display:inline-flex; align-items:center; gap:6px;">
                  <button type="button" class="btn btn-secondary btn-icon" onclick='editCustomer(@json($customer))' title="Edit Customer" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                  </button>
                  <button type="button" class="btn btn-danger btn-icon" onclick="confirmDeleteCustomer({{ $customer->id }}, '{{ addslashes($customer->name) }}')" title="Delete Customer" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <polyline points="3 6 5 6 21 6"/>
                      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

  </div>

</div>

<!-- Customer Modal (Add & Edit Form via AJAX) -->
<div id="customer-modal" style="display:none;">
  <div class="cm-modal-box">
    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--slate-100); padding-bottom:16px; margin-bottom:18px;">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:38px; height:38px; border-radius:10px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
          </svg>
        </div>
        <div>
          <h3 id="modal-title" style="margin:0; font-size:1.1rem; font-weight:800; color:var(--slate-900);">Add New Customer</h3>
          <p style="margin:2px 0 0; font-size:0.75rem; color:var(--slate-500);">Complete customer master profile for invoicing & billing</p>
        </div>
      </div>
      <button type="button" onclick="closeCustomerModal()" style="background:none; border:none; color:var(--slate-400); cursor:pointer; font-size:1.4rem; line-height:1; padding:4px;">
        &times;
      </button>
    </div>

    <form id="customer-form" onsubmit="saveCustomerAjax(event)">
      @csrf
      <input type="hidden" name="id" id="customer-id" value="">

      <!-- Basic Details -->
      <div class="cm-modal-section-title">General Information</div>
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:14px; margin-bottom:12px;">
        <div class="form-group" style="margin:0;">
          <label class="form-label required">Customer Name</label>
          <input type="text" name="name" id="cust-name" class="form-control" required placeholder="e.g. Royal Apparels Ltd">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Customer Code</label>
          <input type="text" name="code" id="cust-code" class="form-control" placeholder="Auto-generated if empty">
        </div>
      </div>

      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:14px; margin-bottom:12px;">
        <div class="form-group" style="margin:0;">
          <label class="form-label">Contact Person</label>
          <input type="text" name="contact_person" id="cust-contact-person" class="form-control" placeholder="e.g. Rahul Sharma">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label required">Phone / Mobile</label>
          <input type="text" name="phone" id="cust-phone" class="form-control" required placeholder="e.g. 9876543210">
        </div>
      </div>

      <div class="form-group" style="margin-bottom:12px;">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" id="cust-email" class="form-control" placeholder="e.g. accounts@royalapparel.com">
      </div>

      <!-- Financial & Tax Details -->
      <div class="cm-modal-section-title">Tax & Financial Credentials</div>
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:14px; margin-bottom:12px;">
        <div class="form-group" style="margin:0;">
          <label class="form-label">GSTIN Number</label>
          <input type="text" name="gst_number" id="cust-gst" class="form-control" placeholder="22AAAAA0000A1Z5" style="text-transform:uppercase;">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">PAN Number</label>
          <input type="text" name="pan_number" id="cust-pan" class="form-control" placeholder="AAAAA0000A" style="text-transform:uppercase;">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Credit Limit (₹)</label>
          <input type="number" step="0.01" name="credit_limit" id="cust-credit-limit" class="form-control" placeholder="0.00">
        </div>
      </div>

      <!-- Address Details -->
      <div class="cm-modal-section-title">Address & Billing Details</div>
      <div class="form-group" style="margin-bottom:12px;">
        <label class="form-label">Billing Address</label>
        <textarea name="billing_address" id="cust-address" class="form-control" rows="2" placeholder="Street name, industrial area, landmark..."></textarea>
      </div>

      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap:14px; margin-bottom:20px;">
        <div class="form-group" style="margin:0;">
          <label class="form-label">City</label>
          <input type="text" name="city" id="cust-city" class="form-control" placeholder="e.g. Mumbai">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">State</label>
          <input type="text" name="state" id="cust-state" class="form-control" placeholder="e.g. Maharashtra">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Pincode</label>
          <input type="text" name="pincode" id="cust-pincode" class="form-control" placeholder="e.g. 400001">
        </div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid var(--slate-100); padding-top:16px;">
        <button type="button" class="btn btn-secondary" onclick="closeCustomerModal()">Cancel</button>
        <button type="submit" id="save-cust-btn" class="btn btn-primary" style="background:linear-gradient(135deg, #2563eb, #1d4ed8); font-weight:700;">Save Customer</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Confirmation Modal (via AJAX) -->
<div id="delete-customer-modal" style="display:none;">
  <div class="cm-modal-box" style="max-width:440px; text-align:center;">
    <div style="width:52px; height:52px; border-radius:50%; background:#fef2f2; color:#dc2626; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
      <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
    </div>
    <h3 style="margin:0 0 8px; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Delete Customer</h3>
    <p style="margin:0 0 20px; font-size:0.875rem; color:var(--slate-500); line-height:1.4;">
      Are you sure you want to delete <strong id="delete-cust-name" style="color:var(--slate-800);">this customer</strong>? This action cannot be undone.
    </p>

    <input type="hidden" id="delete-cust-id" value="">
    <div style="display:flex; justify-content:center; gap:10px;">
      <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
      <button type="button" id="confirm-delete-btn" class="btn btn-danger" onclick="deleteCustomerAjax()" style="font-weight:700;">Yes, Delete Customer</button>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  const CSRF_TOKEN = '{{ csrf_token() }}';

  // --- Modal Open / Close ---
  function openCustomerModal() {
    const modal = document.getElementById('customer-modal');
    if (!modal) return;
    
    document.getElementById('customer-form').reset();
    document.getElementById('customer-id').value = '';
    document.getElementById('modal-title').textContent = 'Add New Customer';
    document.getElementById('save-cust-btn').textContent = 'Save Customer';
    document.getElementById('save-cust-btn').disabled = false;
    
    modal.style.display = 'flex';
  }

  function closeCustomerModal() {
    const modal = document.getElementById('customer-modal');
    if (modal) modal.style.display = 'none';
  }

  function editCustomer(customer) {
    const modal = document.getElementById('customer-modal');
    if (!modal) return;

    document.getElementById('modal-title').textContent = 'Edit Customer';
    document.getElementById('save-cust-btn').textContent = 'Update Customer';
    document.getElementById('save-cust-btn').disabled = false;
    document.getElementById('customer-id').value = customer.id;

    document.getElementById('cust-name').value = customer.name || '';
    document.getElementById('cust-code').value = customer.code || '';
    document.getElementById('cust-contact-person').value = customer.contact_person || '';
    document.getElementById('cust-phone').value = customer.phone || customer.mobile || '';
    document.getElementById('cust-email').value = customer.email || '';
    document.getElementById('cust-gst').value = customer.gst_number || customer.gstin || '';
    document.getElementById('cust-pan').value = customer.pan_number || '';
    document.getElementById('cust-credit-limit').value = customer.credit_limit || '';
    document.getElementById('cust-address').value = customer.billing_address || customer.address || '';
    document.getElementById('cust-city').value = customer.city || '';
    document.getElementById('cust-state').value = customer.state || '';
    document.getElementById('cust-pincode').value = customer.pincode || '';

    modal.style.display = 'flex';
  }

  function confirmDeleteCustomer(id, name) {
    const modal = document.getElementById('delete-customer-modal');
    if (!modal) return;
    
    document.getElementById('delete-cust-name').textContent = name;
    document.getElementById('delete-cust-id').value = id;
    document.getElementById('confirm-delete-btn').textContent = 'Yes, Delete Customer';
    document.getElementById('confirm-delete-btn').disabled = false;
    modal.style.display = 'flex';
  }

  function closeDeleteModal() {
    const modal = document.getElementById('delete-customer-modal');
    if (modal) modal.style.display = 'none';
  }

  // --- 1. STATUS CHANGE THROUGH TABLE VIA AJAX ---
  function changeCustomerStatus(id, newStatus, selectEl) {
    const prevClass = selectEl.className;
    selectEl.style.opacity = '0.5';

    fetch('/masters/customers/' + id, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': CSRF_TOKEN
      },
      body: JSON.stringify({ status: newStatus })
    })
    .then(res => res.json())
    .then(data => {
      selectEl.style.opacity = '1';
      if (data.success) {
        // Update select styles
        selectEl.className = 'cm-status-select ' + newStatus.toLowerCase();
        
        // Update row data-status
        const row = document.getElementById('customer-row-' + id);
        if (row) row.setAttribute('data-status', newStatus.toLowerCase());

        // Update stats if returned
        if (data.stats) updateKpiStats(data.stats);

        UI.showToast('Status Updated', 'Customer status set to ' + newStatus, 'success');
      } else {
        selectEl.className = prevClass;
        UI.showToast('Error', data.message || 'Could not update status', 'error');
      }
    })
    .catch(err => {
      selectEl.style.opacity = '1';
      selectEl.className = prevClass;
      UI.showToast('Error', 'Network error occurred while updating status', 'error');
    });
  }

  // --- 2. FORM SAVE USING AJAX (ADD & EDIT) ---
  function saveCustomerAjax(e) {
    e.preventDefault();

    const saveBtn = document.getElementById('save-cust-btn');
    const custId = document.getElementById('customer-id').value;
    const isEdit = Boolean(custId);

    const payload = {
      name: document.getElementById('cust-name').value.trim(),
      code: document.getElementById('cust-code').value.trim(),
      contact_person: document.getElementById('cust-contact-person').value.trim(),
      phone: document.getElementById('cust-phone').value.trim(),
      email: document.getElementById('cust-email').value.trim(),
      gstin: document.getElementById('cust-gst').value.trim(),
      gst_number: document.getElementById('cust-gst').value.trim(),
      pan_number: document.getElementById('cust-pan').value.trim(),
      credit_limit: parseFloat(document.getElementById('cust-credit-limit').value) || 0,
      address: document.getElementById('cust-address').value.trim(),
      billing_address: document.getElementById('cust-address').value.trim(),
      city: document.getElementById('cust-city').value.trim(),
      state: document.getElementById('cust-state').value.trim(),
      pincode: document.getElementById('cust-pincode').value.trim()
    };

    if (!payload.name) {
      UI.showToast('Validation Error', 'Customer Name is required', 'error');
      return;
    }
    if (!payload.phone) {
      UI.showToast('Validation Error', 'Phone Number is required', 'error');
      return;
    }

    saveBtn.disabled = true;
    saveBtn.textContent = isEdit ? 'Updating...' : 'Saving...';

    const url = isEdit ? ('/masters/customers/' + custId) : "{{ route('masters.customers.store') }}";
    const method = isEdit ? 'PUT' : 'POST';

    fetch(url, {
      method: method,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': CSRF_TOKEN
      },
      body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
      saveBtn.disabled = false;
      saveBtn.textContent = isEdit ? 'Update Customer' : 'Save Customer';

      if (data.success && data.customer) {
        closeCustomerModal();
        UI.showToast(isEdit ? 'Customer Updated' : 'Customer Created', data.message, 'success');

        if (isEdit) {
          updateTableRow(data.customer);
        } else {
          prependTableRow(data.customer);
        }

        if (data.stats) updateKpiStats(data.stats);
      } else {
        const msg = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message || 'Validation error');
        UI.showToast('Error', msg, 'error');
      }
    })
    .catch(err => {
      saveBtn.disabled = false;
      saveBtn.textContent = isEdit ? 'Update Customer' : 'Save Customer';
      UI.showToast('Error', 'Failed to save customer details', 'error');
    });
  }

  // --- 3. DELETE USING AJAX ---
  function deleteCustomerAjax() {
    const id = document.getElementById('delete-cust-id').value;
    if (!id) return;

    const delBtn = document.getElementById('confirm-delete-btn');
    delBtn.disabled = true;
    delBtn.textContent = 'Deleting...';

    fetch('/masters/customers/' + id, {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': CSRF_TOKEN
      }
    })
    .then(res => res.json())
    .then(data => {
      delBtn.disabled = false;
      delBtn.textContent = 'Yes, Delete Customer';
      closeDeleteModal();

      if (data.success) {
        UI.showToast('Customer Deleted', data.message || 'Customer removed successfully', 'warning');
        
        // Animate row removal
        const row = document.getElementById('customer-row-' + id);
        if (row) {
          row.style.opacity = '0';
          row.style.transform = 'scale(0.95)';
          setTimeout(() => {
            row.remove();
            reindexRows();
            checkEmptyState();
          }, 250);
        }

        if (data.stats) updateKpiStats(data.stats);
      } else {
        UI.showToast('Error', data.message || 'Could not delete customer', 'error');
      }
    })
    .catch(err => {
      delBtn.disabled = false;
      delBtn.textContent = 'Yes, Delete Customer';
      UI.showToast('Error', 'Failed to delete customer', 'error');
    });
  }

  // --- Dynamic Table DOM Manipulation ---
  function prependTableRow(c) {
    const tbody = document.getElementById('customers-table-body');
    const tableContainer = document.getElementById('customers-table-container');
    const emptyState = document.getElementById('customers-empty-state');

    if (emptyState) emptyState.style.display = 'none';
    if (tableContainer) tableContainer.style.display = '';

    const tr = document.createElement('tr');
    tr.className = 'cm-row cm-row-transition';
    tr.id = 'customer-row-' + c.id;
    tr.setAttribute('data-id', c.id);
    tr.setAttribute('data-status', (c.status || 'active').toLowerCase());
    tr.setAttribute('data-balance', c.outstanding || 0);

    const initials = (c.name || 'CU').substring(0, 2).toUpperCase();
    const gstinBadge = (c.gstin || c.gst_number)
      ? `<span class="badge" style="background:#f1f5f9; color:#1e293b; font-family:monospace; font-size:0.75rem; font-weight:600; border:1px solid #e2e8f0;">${c.gstin || c.gst_number}</span>`
      : `<span style="font-size:0.75rem; color:var(--slate-400);">Unregistered</span>`;

    const statusVal = (c.status || 'Active');
    const statusLower = statusVal.toLowerCase();

    tr.innerHTML = `
      <td class="row-index" style="text-align: center; font-weight: 600; color: var(--slate-400);">1</td>
      <td>
        <div style="display:flex; align-items:center; gap:10px;">
          <div class="cm-avatar" id="avatar-${c.id}">${initials}</div>
          <div>
            <div class="cust-name-val" style="font-weight:700; color:var(--slate-900); font-size:0.9rem;">${c.name}</div>
            <div class="cust-code-val" style="font-size:0.75rem; color:var(--slate-500); font-family:monospace;">${c.code || ('CUST-' + String(c.id).padStart(4, '0'))}</div>
          </div>
        </div>
      </td>
      <td>
        <div class="cust-contact-val" style="font-weight:600; color:var(--slate-800); font-size:0.85rem;">${c.contact_person || '—'}</div>
        <div class="cust-phone-val" style="font-size:0.75rem; color:var(--slate-500);">${c.phone || c.mobile || '—'}</div>
      </td>
      <td>
        <div class="cust-city-val" style="font-weight:600; color:var(--slate-700); font-size:0.85rem;">${c.city || '—'}</div>
        <div class="cust-state-val" style="font-size:0.75rem; color:var(--slate-500);">${c.state || '—'}</div>
      </td>
      <td class="cust-gst-cell">${gstinBadge}</td>
      <td class="cust-credit-val" style="text-align: right; font-weight: 600; color: var(--slate-700);">₹${Number(c.credit_limit || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
      <td class="cust-outstanding-val" style="text-align: right; font-weight: 700; color: #059669;">₹0.00</td>
      <td style="text-align: center;">
        <select class="cm-status-select ${statusLower}" onchange="changeCustomerStatus(${c.id}, this.value, this)" title="Click to change status">
          <option value="Active" ${statusLower === 'active' ? 'selected' : ''}>Active</option>
          <option value="Inactive" ${statusLower === 'inactive' ? 'selected' : ''}>Inactive</option>
          <option value="Blocked" ${statusLower === 'blocked' ? 'selected' : ''}>Blocked</option>
        </select>
      </td>
      <td style="text-align: center;">
        <div style="display:inline-flex; align-items:center; gap:6px;">
          <button type="button" class="btn btn-secondary btn-icon edit-btn-${c.id}" title="Edit Customer" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
              <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
          </button>
          <button type="button" class="btn btn-danger btn-icon" onclick="confirmDeleteCustomer(${c.id}, '${c.name.replace(/'/g, "\\'")}')" title="Delete Customer" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="3 6 5 6 21 6"/>
              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
            </svg>
          </button>
        </div>
      </td>
    `;

    // Attach edit onclick handler
    const editBtn = tr.querySelector(`.edit-btn-${c.id}`);
    if (editBtn) {
      editBtn.onclick = () => editCustomer(c);
    }

    tbody.insertBefore(tr, tbody.firstChild);
    reindexRows();
  }

  function updateTableRow(c) {
    const row = document.getElementById('customer-row-' + c.id);
    if (!row) return;

    row.querySelector('.cust-name-val').textContent = c.name;
    row.querySelector('.cust-code-val').textContent = c.code || ('CUST-' + String(c.id).padStart(4, '0'));
    row.querySelector('.cust-contact-val').textContent = c.contact_person || '—';
    row.querySelector('.cust-phone-val').textContent = c.phone || c.mobile || '—';
    row.querySelector('.cust-city-val').textContent = c.city || '—';
    row.querySelector('.cust-state-val').textContent = c.state || '—';
    
    const initials = (c.name || 'CU').substring(0, 2).toUpperCase();
    const avatar = document.getElementById('avatar-' + c.id);
    if (avatar) avatar.textContent = initials;

    const gstinCell = row.querySelector('.cust-gst-cell');
    if (gstinCell) {
      gstinCell.innerHTML = (c.gstin || c.gst_number)
        ? `<span class="badge" style="background:#f1f5f9; color:#1e293b; font-family:monospace; font-size:0.75rem; font-weight:600; border:1px solid #e2e8f0;">${c.gstin || c.gst_number}</span>`
        : `<span style="font-size:0.75rem; color:var(--slate-400);">Unregistered</span>`;
    }

    const creditCell = row.querySelector('.cust-credit-val');
    if (creditCell) {
      creditCell.textContent = '₹' + Number(c.credit_limit || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    // Update edit button handler with fresh data
    const editBtn = row.querySelector('.btn-secondary');
    if (editBtn) {
      editBtn.onclick = () => editCustomer(c);
    }
  }

  function reindexRows() {
    const rows = document.querySelectorAll('#customers-table-body tr');
    rows.forEach((row, i) => {
      const idxCell = row.querySelector('.row-index');
      if (idxCell) idxCell.textContent = i + 1;
    });
  }

  function checkEmptyState() {
    const rows = document.querySelectorAll('#customers-table-body tr');
    const emptyState = document.getElementById('customers-empty-state');
    const tableContainer = document.getElementById('customers-table-container');

    if (rows.length === 0) {
      if (emptyState) emptyState.style.display = 'flex';
      if (tableContainer) tableContainer.style.display = 'none';
    } else {
      if (emptyState) emptyState.style.display = 'none';
      if (tableContainer) tableContainer.style.display = '';
    }
  }

  function updateKpiStats(stats) {
    if (!stats) return;

    if (document.getElementById('stat-total-count')) document.getElementById('stat-total-count').textContent = stats.total;
    if (document.getElementById('stat-active-count')) document.getElementById('stat-active-count').textContent = stats.active;
    if (document.getElementById('pill-count-all')) document.getElementById('pill-count-all').textContent = stats.total;
    if (document.getElementById('pill-count-active')) document.getElementById('pill-count-active').textContent = stats.active;
    if (document.getElementById('customer-badge-count')) document.getElementById('customer-badge-count').textContent = stats.total + ' Records';

    if (document.getElementById('stat-health-percent')) {
      const pct = stats.total > 0 ? Math.round((stats.active / stats.total) * 100) : 100;
      document.getElementById('stat-health-percent').textContent = pct + '%';
    }

    if (document.getElementById('stat-total-outstanding') && stats.totalOutstanding !== undefined) {
      const val = Number(stats.totalOutstanding);
      document.getElementById('stat-total-outstanding').textContent = '₹' + val.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
      document.getElementById('stat-total-outstanding').style.color = val > 0 ? '#dc2626' : '#059669';
    }

    if (document.getElementById('stat-total-credit') && stats.totalCreditLimit !== undefined) {
      document.getElementById('stat-total-credit').textContent = '₹' + Number(stats.totalCreditLimit).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
  }

  // --- Live Table Filter & Pills ---
  function filterCustomerTable(query) {
    query = (query || '').toLowerCase().trim();
    const rows = document.querySelectorAll('#customers-table-body tr');
    rows.forEach(row => {
      const text = row.innerText.toLowerCase();
      row.style.display = text.includes(query) ? '' : 'none';
    });
  }

  function applyCustomerFilter(type, btn) {
    document.querySelectorAll('.cm-filter-pill').forEach(el => el.classList.remove('active'));
    if (btn) btn.classList.add('active');

    const rows = document.querySelectorAll('#customers-table-body tr.cm-row');
    rows.forEach(row => {
      const status = row.getAttribute('data-status');
      const balance = parseFloat(row.getAttribute('data-balance') || 0);

      if (type === 'all') {
        row.style.display = '';
      } else if (type === 'active') {
        row.style.display = (status === 'active') ? '' : 'none';
      } else if (type === 'blocked') {
        row.style.display = (status === 'blocked') ? '' : 'none';
      } else if (type === 'balance') {
        row.style.display = (balance > 0) ? '' : 'none';
      }
    });
  }

  // Close modals on clicking backdrop
  window.addEventListener('click', function(e) {
    const custModal = document.getElementById('customer-modal');
    const delModal = document.getElementById('delete-customer-modal');
    if (e.target === custModal) closeCustomerModal();
    if (e.target === delModal) closeDeleteModal();
  });
</script>
@endpush