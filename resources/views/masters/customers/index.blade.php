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

  .cm-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 700;
  }

  .cm-status-pill.active {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
  }

  .cm-status-pill.blocked {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
  }

  .cm-status-pill.inactive {
    background: #f1f5f9;
    color: #64748b;
    border: 1px solid #cbd5e1;
  }

  .cm-status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
  }

  .cm-status-pill.active .cm-status-dot {
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.3);
    animation: cm-pulse 2s infinite;
  }

  @keyframes cm-pulse {
    0% { transform: scale(0.95); opacity: 0.8; }
    50% { transform: scale(1.2); opacity: 1; }
    100% { transform: scale(0.95); opacity: 0.8; }
  }

  /* Modal Styling */
  #customer-modal {
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
          <span>{{ $stats['total'] }}</span>
          <span style="font-size:0.75rem; font-weight:700; color:#059669; background:#ecfdf5; padding:2px 8px; border-radius:12px; border:1px solid #a7f3d0;">
            {{ $stats['active'] }} Active
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
          <span>{{ $stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100) : 100 }}%</span>
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
        <div class="cm-stat-value" style="color:{{ $stats['totalOutstanding'] > 0 ? '#dc2626' : '#059669' }};">
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
        <div class="cm-stat-value" style="color:var(--slate-800);">
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
            <span class="badge badge-primary" style="font-size:0.75rem; padding:2px 8px; border-radius:12px;">{{ count($customers) }} Records</span>
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
        <button type="button" class="cm-filter-pill active" onclick="applyCustomerFilter('all', this)">All ({{ $stats['total'] }})</button>
        <button type="button" class="cm-filter-pill" onclick="applyCustomerFilter('active', this)">Active ({{ $stats['active'] }})</button>
        <button type="button" class="cm-filter-pill" onclick="applyCustomerFilter('blocked', this)">Blocked</button>
        <button type="button" class="cm-filter-pill" onclick="applyCustomerFilter('balance', this)">With Balance</button>
      </div>
    </div>

    <!-- Empty State or Data Table -->
    @if($customers->isEmpty())
      <!-- Rich Empty State -->
      <div class="cm-empty-box">
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

          <div class="cm-feature-card">
            <div style="width:34px; height:34px; border-radius:8px; background:#faf5ff; color:#7c3aed; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
            </div>
            <div>
              <div style="font-weight:700; font-size:0.825rem; color:var(--slate-800);">Direct Order & Dispatch Link</div>
              <div style="font-size:0.75rem; color:var(--slate-500); margin-top:2px;">Generate delivery challans, finished goods invoices & settlements</div>
            </div>
          </div>
        </div>
      </div>
    @else
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
            @foreach ($customers as $c)
              @php
                $initials = strtoupper(substr($c->name, 0, 2));
              @endphp
              <tr data-status="{{ strtolower($c->status) }}" data-outstanding="{{ $c->outstanding }}">
                <td style="font-family:var(--font-mono); font-weight:700;">
                  <span style="background:var(--primary-50); color:var(--primary-700); padding:3px 8px; border-radius:6px; border:1px solid var(--primary-200); font-size:0.8rem;">
                    {{ $c->code }}
                  </span>
                </td>
                <td>
                  <div style="display:flex; align-items:center; gap:10px;">
                    <div class="cm-avatar">{{ $initials }}</div>
                    <div>
                      <div style="font-weight:700; color:var(--slate-900);">{{ $c->name }}</div>
                      @if($c->company_name)
                        <div style="font-size:0.75rem; color:var(--slate-500);">{{ $c->company_name }}</div>
                      @endif
                    </div>
                  </div>
                </td>
                <td>
                  @if($c->contact_person)
                    <div style="display:flex; align-items:center; gap:6px; color:var(--slate-700);">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--slate-400);"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                      <span>{{ $c->contact_person }}</span>
                    </div>
                  @else
                    <span style="color:var(--slate-400);">—</span>
                  @endif
                </td>
                <td>
                  <div style="font-weight:600; font-family:var(--font-mono); font-size:0.8rem;">
                    <a href="tel:{{ $c->phone }}" style="color:inherit; text-decoration:none;">{{ $c->phone }}</a>
                  </div>
                  @if($c->email)
                    <div style="font-size:0.75rem; color:var(--slate-500); margin-top:2px;">
                      <a href="mailto:{{ $c->email }}" style="color:var(--primary-600); text-decoration:none;">{{ $c->email }}</a>
                    </div>
                  @endif
                </td>
                <td style="font-family:var(--font-mono); font-size:0.8rem;">
                  @if($c->gstin)
                    <span style="background:#f1f5f9; padding:2px 6px; border-radius:4px; border:1px solid #e2e8f0; color:var(--slate-800);">{{ $c->gstin }}</span>
                  @else
                    <span style="color:var(--slate-400);">—</span>
                  @endif
                </td>
                <td>
                  @if($c->city || $c->state)
                    <div style="font-weight:600; color:var(--slate-800);">{{ $c->city ?: '—' }}</div>
                    @if($c->state)
                      <div style="font-size:0.75rem; color:var(--slate-500);">{{ $c->state }}</div>
                    @endif
                  @else
                    <span style="color:var(--slate-400);">—</span>
                  @endif
                </td>
                <td style="font-weight:600; font-family:var(--font-mono); color:var(--slate-800);">
                  ₹{{ number_format($c->credit_limit, 2) }}
                </td>
                <td>
                  <span style="font-weight:700; font-family:var(--font-mono); color:{{ $c->outstanding > 0 ? '#dc2626' : '#059669' }}; background:{{ $c->outstanding > 0 ? '#fef2f2' : '#ecfdf5' }}; padding:3px 8px; border-radius:6px; border:1px solid {{ $c->outstanding > 0 ? '#fecaca' : '#a7f3d0' }}; display:inline-block;">
                    ₹{{ number_format($c->outstanding, 2) }}
                  </span>
                </td>
                <td>
                  @if($c->status === 'Active')
                    <span class="cm-status-pill active"><span class="cm-status-dot"></span> Active</span>
                  @elseif($c->status === 'Blocked')
                    <span class="cm-status-pill blocked"><span class="cm-status-dot"></span> Blocked</span>
                  @else
                    <span class="cm-status-pill inactive"><span class="cm-status-dot"></span> {{ $c->status }}</span>
                  @endif
                </td>
                <td style="text-align:right;">
                  <div style="display:inline-flex; gap:6px;">
                    <button class="btn btn-secondary btn-xs" onclick='openCustomerModal(@json($c))' title="Edit Customer" style="display:inline-flex; align-items:center; gap:4px; font-weight:600; cursor:pointer;">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                      Edit
                    </button>
                    <form action="{{ route('masters.customers.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete customer {{ $c->name }}?')" style="display:inline;">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger btn-xs" title="Delete" style="padding:4px 8px; cursor:pointer;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

  </div>

</div>

<!-- Add / Edit Customer Modal -->
<div id="customer-modal" style="display:none;">
  <div class="cm-modal-box">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:1px solid var(--slate-200); padding-bottom:14px;">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:36px; height:36px; border-radius:10px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div>
          <h3 id="modal-title" style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Add New Customer</h3>
          <p style="margin:2px 0 0; font-size:0.775rem; color:var(--slate-500);">Configure client commercial credentials and limits</p>
        </div>
      </div>
      <button type="button" onclick="closeCustomerModal()" style="background:none; border:none; width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--slate-400); transition:all 0.15s ease;" onmouseover="this.style.background='#f1f5f9'; this.style.color='#0f172a'" onmouseout="this.style.background='none'; this.style.color='var(--slate-400)'">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <form id="customer-form" method="POST" action="{{ route('masters.customers.store') }}">
      @csrf
      <input type="hidden" name="_method" id="form-method" value="POST">

      <div class="cm-modal-section-title">1. Identity & Business Details</div>
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
        <div class="form-group">
          <label class="form-label" style="font-weight:700;">Customer / Trading Name <span style="color:red;">*</span></label>
          <input type="text" name="name" id="cust_name" class="form-control" required placeholder="e.g. Zara Apparels Ltd.">
        </div>

        <div class="form-group">
          <label class="form-label" style="font-weight:700;">Company / Legal Name</label>
          <input type="text" name="company_name" id="cust_company" class="form-control" placeholder="e.g. Zara Retail Pvt. Ltd.">
        </div>

        <div class="form-group">
          <label class="form-label" style="font-weight:700;">Contact Person</label>
          <input type="text" name="contact_person" id="cust_contact" class="form-control" placeholder="e.g. Rajesh Mehta">
        </div>

        <div class="form-group">
          <label class="form-label" style="font-weight:700;">Phone / Mobile <span style="color:red;">*</span></label>
          <input type="text" name="phone" id="cust_phone" class="form-control" required placeholder="e.g. +91 98765 43210">
        </div>
      </div>

      <div class="cm-modal-section-title">2. Tax & Location</div>
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
        <div class="form-group">
          <label class="form-label" style="font-weight:700;">Email Address</label>
          <input type="email" name="email" id="cust_email" class="form-control" placeholder="e.g. contact@zara.com">
        </div>

        <div class="form-group">
          <label class="form-label" style="font-weight:700;">GSTIN / Tax ID</label>
          <input type="text" name="gstin" id="cust_gstin" class="form-control" placeholder="e.g. 27AAAAA0000A1Z5" style="text-transform:uppercase;">
        </div>

        <div class="form-group">
          <label class="form-label" style="font-weight:700;">City</label>
          <input type="text" name="city" id="cust_city" class="form-control" placeholder="e.g. Mumbai">
        </div>

        <div class="form-group">
          <label class="form-label" style="font-weight:700;">State</label>
          <input type="text" name="state" id="cust_state" class="form-control" placeholder="e.g. Maharashtra">
        </div>
      </div>

      <div class="cm-modal-section-title">3. Commercial Terms & Address</div>
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
        <div class="form-group">
          <label class="form-label" style="font-weight:700;">Approved Credit Limit (₹)</label>
          <input type="number" step="0.01" name="credit_limit" id="cust_credit" class="form-control" value="100000.00">
        </div>

        <div class="form-group">
          <label class="form-label" style="font-weight:700;">Status</label>
          <select name="status" id="cust_status" class="form-control">
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
            <option value="Blocked">Blocked</option>
          </select>
        </div>
      </div>

      <div class="form-group" style="margin-top:14px;">
        <label class="form-label" style="font-weight:700;">Billing & Shipping Address</label>
        <textarea name="address" id="cust_address" class="form-control" rows="2" placeholder="Street address, industrial area, PIN..."></textarea>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:24px; border-top:1px solid var(--slate-200); padding-top:16px;">
        <button type="button" class="btn btn-secondary" onclick="closeCustomerModal()" style="font-weight:600; cursor:pointer;">Cancel</button>
        <button type="submit" class="btn btn-primary" id="submit-btn" style="display:inline-flex; align-items:center; gap:6px; font-weight:700; background:linear-gradient(135deg, #2563eb, #1d4ed8); cursor:pointer;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          Save Customer
        </button>
      </div>
    </form>

  </div>
</div>

@push('scripts')
<script>
  let activeFilterTab = 'all';

  function filterCustomerTable(query) {
    const q = (query || '').toLowerCase().trim();
    const table = document.getElementById('customers-table');
    if (!table) return;

    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
      if (row.classList.contains('filter-no-results')) return;
      
      const textMatch = !q || row.innerText.toLowerCase().includes(q);
      const statusMatch = checkStatusFilter(row);

      row.style.display = (textMatch && statusMatch) ? '' : 'none';
    });
  }

  function checkStatusFilter(row) {
    if (activeFilterTab === 'all') return true;
    const rowStatus = row.getAttribute('data-status') || '';
    const rowOutstanding = parseFloat(row.getAttribute('data-outstanding') || 0);

    if (activeFilterTab === 'active') return rowStatus === 'active';
    if (activeFilterTab === 'blocked') return rowStatus === 'blocked';
    if (activeFilterTab === 'balance') return rowOutstanding > 0;
    return true;
  }

  function applyCustomerFilter(tab, btn) {
    activeFilterTab = tab;
    document.querySelectorAll('.cm-filter-pill').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');

    const searchInput = document.getElementById('cust-filter-input');
    filterCustomerTable(searchInput ? searchInput.value : '');
  }

  function openCustomerModal(c = null) {
    const modal = document.getElementById('customer-modal');
    const form = document.getElementById('customer-form');
    const methodInput = document.getElementById('form-method');
    const title = document.getElementById('modal-title');

    if (!modal) {
      console.error("Modal element #customer-modal not found");
      return;
    }

    if (c && typeof c === 'object' && c.id) {
      if (title) title.innerText = 'Edit Customer: ' + (c.code || c.name);
      if (form) form.action = `/masters/customers/${c.id}`;
      if (methodInput) methodInput.value = 'PUT';

      const setVal = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.value = val !== undefined && val !== null ? val : '';
      };

      setVal('cust_name', c.name);
      setVal('cust_phone', c.phone);
      setVal('cust_company', c.company_name);
      setVal('cust_contact', c.contact_person);
      setVal('cust_email', c.email);
      setVal('cust_gstin', c.gstin);
      setVal('cust_city', c.city);
      setVal('cust_state', c.state);
      setVal('cust_credit', c.credit_limit || 0);
      setVal('cust_status', c.status || 'Active');
      setVal('cust_address', c.address);
      
      const submitBtn = document.getElementById('submit-btn');
      if (submitBtn) {
        submitBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Update Customer';
      }
    } else {
      if (title) title.innerText = 'Add New Customer';
      if (form) {
        form.action = '{{ route('masters.customers.store') }}';
        form.reset();
      }
      if (methodInput) methodInput.value = 'POST';
      
      const creditEl = document.getElementById('cust_credit');
      if (creditEl) creditEl.value = '100000.00';
      const statusEl = document.getElementById('cust_status');
      if (statusEl) statusEl.value = 'Active';
      
      const submitBtn = document.getElementById('submit-btn');
      if (submitBtn) {
        submitBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Save Customer';
      }
    }

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }

  function closeCustomerModal() {
    const modal = document.getElementById('customer-modal');
    if (modal) {
      modal.style.display = 'none';
      document.body.style.overflow = '';
    }
  }

  // Close modal when clicking outside box
  document.getElementById('customer-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeCustomerModal();
  });

  // Close modal on Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeCustomerModal();
  });
</script>
@endpush
@endsection
