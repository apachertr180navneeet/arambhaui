@extends('layouts.app')

@section('title', 'Vendor Master - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Masters Management</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Vendor Master</span></div>
@endsection

@push('styles')
<style>
  /* Vendor Master Modern UI Styles */
  .vm-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 16px;
  }

  .vm-stat-card {
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

  .vm-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.08);
    border-color: var(--slate-300);
  }

  .vm-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--stat-accent, var(--primary-500));
  }

  .vm-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .vm-stat-info {
    flex: 1;
    min-width: 0;
  }

  .vm-stat-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--slate-500);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 4px;
  }

  .vm-stat-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--slate-900);
    line-height: 1.2;
    display: flex;
    align-items: baseline;
    gap: 8px;
    flex-wrap: wrap;
  }

  .vm-main-card {
    background: #ffffff;
    border-radius: 18px;
    border: 1px solid var(--slate-200);
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    overflow: hidden;
  }

  .vm-card-header {
    padding: 22px 24px;
    border-bottom: 1px solid var(--slate-100);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    background: #ffffff;
  }

  .vm-toolbar {
    padding: 16px 24px;
    background: #f8fafc;
    border-bottom: 1px solid var(--slate-200);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    flex-wrap: wrap;
  }

  .vm-search-wrap {
    position: relative;
    flex: 1;
    min-width: 260px;
    max-width: 400px;
  }

  .vm-search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--slate-400);
    pointer-events: none;
  }

  .vm-search-input {
    width: 100%;
    padding: 9px 14px 9px 38px;
    border: 1px solid var(--slate-300);
    border-radius: 10px;
    font-size: 0.85rem;
    background: #ffffff;
    color: var(--slate-800);
    transition: all 0.2s ease;
  }

  .vm-search-input:focus {
    outline: none;
    border-color: var(--primary-500);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
  }

  .vm-filter-pills {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
  }

  .vm-filter-pill {
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

  .vm-filter-pill:hover {
    border-color: var(--slate-400);
    color: var(--slate-900);
  }

  .vm-filter-pill.active {
    background: var(--primary-600);
    color: #ffffff;
    border-color: var(--primary-600);
    box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
  }

  /* Empty State Modern Box */
  .vm-empty-box {
    padding: 60px 24px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
  }

  .vm-empty-icon-ring {
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

  .vm-empty-features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    max-width: 780px;
    width: 100%;
    margin-top: 32px;
    text-align: left;
  }

  .vm-feature-card {
    background: #ffffff;
    border: 1px solid var(--slate-200);
    border-radius: 12px;
    padding: 16px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
  }

  .vm-avatar {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, #0284c7, #0369a1);
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
  .vm-status-select {
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

  .vm-status-select:focus {
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
  }

  .vm-status-select.active {
    background-color: #ecfdf5;
    color: #059669;
    border-color: #a7f3d0;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23059669' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  }

  .vm-status-select.blocked {
    background-color: #fef2f2;
    color: #dc2626;
    border-color: #fecaca;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23dc2626' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  }

  .vm-status-select.inactive {
    background-color: #f1f5f9;
    color: #64748b;
    border-color: #cbd5e1;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  }

  /* Modal Styling */
  #vendor-modal, #delete-vendor-modal {
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

  .vm-modal-box {
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
    animation: vmModalFade 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  }

  @keyframes vmModalFade {
    from { opacity: 0; transform: scale(0.96) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
  }

  .vm-modal-section-title {
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

  .vm-modal-section-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--slate-200);
  }

  .vm-row-transition {
    transition: all 0.3s ease;
  }
</style>
@endpush

@section('content')
<div style="display:flex; flex-direction:column; gap:22px;">

  <!-- Top KPI Dynamic Metrics Row -->
  <div class="vm-stats-grid">
    
    <!-- 1. Total Suppliers -->
    <div class="vm-stat-card" style="--stat-accent: #2563eb;">
      <div class="vm-stat-icon" style="background:#eff6ff; color:#2563eb;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>
        </svg>
      </div>
      <div class="vm-stat-info">
        <div class="vm-stat-label">Total Suppliers</div>
        <div class="vm-stat-value">
          <span id="stat-total-count">{{ $stats['total'] }}</span>
          <span id="stat-active-badge" style="font-size:0.75rem; font-weight:700; color:#059669; background:#ecfdf5; padding:2px 8px; border-radius:12px; border:1px solid #a7f3d0;">
            <span id="stat-active-count">{{ $stats['active'] }}</span> Active
          </span>
        </div>
      </div>
    </div>

    <!-- 2. Fabric Mills & Yarn -->
    <div class="vm-stat-card" style="--stat-accent: #059669;">
      <div class="vm-stat-icon" style="background:#ecfdf5; color:#059669;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <rect width="20" height="14" x="2" y="7" rx="2" ry="2"/>
          <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
        </svg>
      </div>
      <div class="vm-stat-info">
        <div class="vm-stat-label">Fabric Mills & Yarn</div>
        <div class="vm-stat-value">
          <span id="stat-fabric-count">{{ $stats['fabricVendors'] }}</span>
          <span style="font-size:0.75rem; font-weight:600; color:var(--slate-500);">Primary Material</span>
        </div>
      </div>
    </div>

    <!-- 3. Trims & Accessories -->
    <div class="vm-stat-card" style="--stat-accent: #8b5cf6;">
      <div class="vm-stat-icon" style="background:#faf5ff; color:#7c3aed;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/>
          <line x1="20" y1="4" x2="8.12" y2="15.88"/>
          <line x1="14.47" y1="14.48" x2="20" y2="20"/>
          <line x1="8.12" y1="8.12" x2="12" y2="12"/>
        </svg>
      </div>
      <div class="vm-stat-info">
        <div class="vm-stat-label">Trims & Accessories</div>
        <div class="vm-stat-value">
          <span id="stat-trim-count">{{ $stats['trimVendors'] }}</span>
          <span style="font-size:0.75rem; font-weight:600; color:var(--slate-500);">Components</span>
        </div>
      </div>
    </div>

    <!-- 4. Total Outstanding Payables -->
    <div class="vm-stat-card" style="--stat-accent: #ef4444;">
      <div class="vm-stat-icon" style="background:#fef2f2; color:#dc2626;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="1" x2="12" y2="23"/>
          <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
        </svg>
      </div>
      <div class="vm-stat-info">
        <div class="vm-stat-label">Total Payables Outstanding</div>
        <div class="vm-stat-value" id="stat-total-outstanding" style="color:{{ $stats['totalOutstanding'] > 0 ? '#dc2626' : '#059669' }};">
          ₹{{ number_format($stats['totalOutstanding'], 2) }}
        </div>
      </div>
    </div>

  </div>

  <!-- Main Card Container -->
  <div class="vm-main-card">
    
    <!-- Action Header -->
    <div class="vm-card-header">
      <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:40px; height:40px; border-radius:10px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>
          </svg>
        </div>
        <div>
          <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900); display:flex; align-items:center; gap:8px;">
            Vendor Directory
            <span class="badge badge-primary" id="vendor-badge-count" style="font-size:0.75rem; padding:2px 8px; border-radius:12px;">{{ count($vendors) }} Records</span>
          </h3>
          <p style="margin:3px 0 0; font-size:0.8rem; color:var(--slate-500);">Raw fabric mills, yarn suppliers, thread, button vendors & credit terms</p>
        </div>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('vendors-table', 'Vendor_Master.csv')" style="display:inline-flex; align-items:center; gap:6px; font-weight:600;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
          </svg>
          Export CSV
        </button>

        <button class="btn btn-primary btn-sm" onclick="openVendorModal()" style="display:inline-flex; align-items:center; gap:6px; font-weight:700; background:linear-gradient(135deg, #2563eb, #1d4ed8); box-shadow:0 4px 10px rgba(37,99,235,0.25); cursor:pointer;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
          </svg>
          Add Vendor
        </button>
      </div>
    </div>

    <!-- Quick Filter Bar -->
    <div class="vm-toolbar">
      <div class="vm-search-wrap">
        <svg class="vm-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"/>
          <line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" id="vend-filter-input" class="vm-search-input" placeholder="Search name, phone, GSTIN, category, city..." onkeyup="filterVendorTable(this.value)">
      </div>

      <div class="vm-filter-pills">
        <button type="button" class="vm-filter-pill active" id="pill-all" onclick="applyVendorFilter('all', this)">All (<span id="pill-count-all">{{ $stats['total'] }}</span>)</button>
        <button type="button" class="vm-filter-pill" id="pill-active" onclick="applyVendorFilter('active', this)">Active (<span id="pill-count-active">{{ $stats['active'] }}</span>)</button>
        <button type="button" class="vm-filter-pill" onclick="applyVendorFilter('fabric', this)">Fabric Mills</button>
        <button type="button" class="vm-filter-pill" onclick="applyVendorFilter('trims', this)">Trims & Accessories</button>
        <button type="button" class="vm-filter-pill" onclick="applyVendorFilter('blocked', this)">Blocked / Inactive</button>
        <button type="button" class="vm-filter-pill" onclick="applyVendorFilter('balance', this)">With Balance</button>
      </div>
    </div>

    <!-- Empty State Box (Toggled if 0 records) -->
    <div id="vendors-empty-state" class="vm-empty-box" style="{{ $vendors->isEmpty() ? 'display:flex;' : 'display:none;' }}">
      <div class="vm-empty-icon-ring">
        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>
        </svg>
      </div>

      <h3 style="margin:0 0 8px; font-size:1.25rem; font-weight:800; color:var(--slate-900);">No Vendors in Directory Yet</h3>
      <p style="margin:0 0 20px; font-size:0.875rem; color:var(--slate-500); max-width:480px; line-height:1.5;">
        Register raw material suppliers, yarn mills, and trim vendors to manage procurement, purchase orders, and payment terms seamlessly.
      </p>

      <button type="button" class="btn btn-primary" onclick="openVendorModal()" style="display:inline-flex; align-items:center; gap:8px; padding:10px 22px; font-size:0.9rem; font-weight:700; border-radius:10px; background:linear-gradient(135deg, #2563eb, #1d4ed8); box-shadow:0 6px 16px rgba(37,99,235,0.25); cursor:pointer;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"/>
          <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Add Your First Vendor
      </button>

      <!-- Feature cards -->
      <div class="vm-empty-features">
        <div class="vm-feature-card">
          <div style="width:34px; height:34px; border-radius:8px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          </div>
          <div>
            <div style="font-weight:700; font-size:0.825rem; color:var(--slate-800);">GSTIN & State Compliance</div>
            <div style="font-size:0.75rem; color:var(--slate-500); margin-top:2px;">Automated input tax credit and GST validation</div>
          </div>
        </div>

        <div class="vm-feature-card">
          <div style="width:34px; height:34px; border-radius:8px; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
          </div>
          <div>
            <div style="font-weight:700; font-size:0.825rem; color:var(--slate-800);">Credit Terms & Payables</div>
            <div style="font-size:0.75rem; color:var(--slate-500); margin-top:2px;">Track payment deadlines, credit days, and dues</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Data Table Container -->
    <div class="table-responsive" id="vendors-table-container" style="{{ $vendors->isEmpty() ? 'display:none;' : '' }}">
      <table class="data-table" id="vendors-table">
        <thead>
          <tr>
            <th style="width: 50px; text-align: center;">#</th>
            <th>Vendor Name & Code</th>
            <th>Category</th>
            <th>Contact & Phone</th>
            <th>Location & State</th>
            <th>Tax Info (GSTIN)</th>
            <th style="text-align: center;">Credit Terms</th>
            <th style="text-align: right;">Outstanding Payables</th>
            <th style="text-align: center; width: 130px;">Status</th>
            <th style="text-align: center; width: 100px;">Actions</th>
          </tr>
        </thead>
        <tbody id="vendors-table-body">
          @foreach($vendors as $index => $vendor)
            @php
              $categoryLower = strtolower($vendor->category ?? '');
              $categoryBadgeClass = 'badge-info';
              if (str_contains($categoryLower, 'fabric') || str_contains($categoryLower, 'mill')) {
                $categoryBadgeClass = 'badge-primary';
              } elseif (str_contains($categoryLower, 'trim') || str_contains($categoryLower, 'accessory')) {
                $categoryBadgeClass = 'badge-warning';
              } elseif (str_contains($categoryLower, 'yarn') || str_contains($categoryLower, 'thread')) {
                $categoryBadgeClass = 'badge-purple';
              }
            @endphp
            <tr class="vm-row vm-row-transition" id="vendor-row-{{ $vendor->id }}" data-id="{{ $vendor->id }}" data-category="{{ $categoryLower }}" data-status="{{ strtolower($vendor->status ?? 'active') }}" data-balance="{{ $vendor->outstanding ?? 0 }}">
              <td class="row-index" style="text-align: center; font-weight: 600; color: var(--slate-400);">{{ $index + 1 }}</td>
              <td>
                <div style="display:flex; align-items:center; gap:10px;">
                  <div class="vm-avatar" id="avatar-{{ $vendor->id }}">
                    {{ strtoupper(substr($vendor->name, 0, 2)) }}
                  </div>
                  <div>
                    <div class="vend-name-val" style="font-weight:700; color:var(--slate-900); font-size:0.9rem;">{{ $vendor->name }}</div>
                    <div class="vend-code-val" style="font-size:0.75rem; color:var(--slate-500); font-family:monospace;">{{ $vendor->code ?? ('VEND-'.str_pad($vendor->id, 3, '0', STR_PAD_LEFT)) }}</div>
                  </div>
                </div>
              </td>
              <td class="vend-cat-cell">
                <span class="badge {{ $categoryBadgeClass }}" style="font-size:0.75rem; font-weight:600; padding:4px 8px; border-radius:8px;">
                  {{ $vendor->category ?: 'General Supplier' }}
                </span>
              </td>
              <td>
                <div class="vend-contact-val" style="font-weight:600; color:var(--slate-800); font-size:0.85rem;">{{ $vendor->contact_person ?: ($vendor->company_name ?: '—') }}</div>
                <div class="vend-phone-val" style="font-size:0.75rem; color:var(--slate-500);">{{ $vendor->phone ?? '—' }}</div>
              </td>
              <td>
                <div class="vend-city-val" style="font-weight:600; color:var(--slate-700); font-size:0.85rem;">{{ $vendor->city ?? '—' }}</div>
                <div class="vend-state-val" style="font-size:0.75rem; color:var(--slate-500);">{{ $vendor->state ?? '—' }}</div>
              </td>
              <td class="vend-gst-cell">
                @if(!empty($vendor->gst_number ?? $vendor->gstin))
                  <span class="badge" style="background:#f1f5f9; color:#1e293b; font-family:monospace; font-size:0.75rem; font-weight:600; border:1px solid #e2e8f0;">
                    {{ $vendor->gst_number ?? $vendor->gstin }}
                  </span>
                @else
                  <span style="font-size:0.75rem; color:var(--slate-400);">Unregistered</span>
                @endif
              </td>
              <td class="vend-credit-cell" style="text-align: center; font-weight: 600; color: var(--slate-700); font-size:0.85rem;">
                {{ $vendor->credit_days ? ($vendor->credit_days . ' Days') : 'Immediate / Cash' }}
              </td>
              <td class="vend-outstanding-val" style="text-align: right; font-weight: 700; color: {{ ($vendor->outstanding ?? 0) > 0 ? '#dc2626' : '#059669' }};">
                ₹{{ number_format($vendor->outstanding ?? 0, 2) }}
              </td>
              <td style="text-align: center;">
                <!-- Direct Table Status Changer -->
                <select class="vm-status-select {{ strtolower($vendor->status ?? 'active') }}" onchange="changeVendorStatus({{ $vendor->id }}, this.value, this)" title="Click to change status">
                  <option value="Active" {{ strtolower($vendor->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                  <option value="Inactive" {{ strtolower($vendor->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                  <option value="Blocked" {{ strtolower($vendor->status ?? '') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                </select>
              </td>
              <td style="text-align: center;">
                <div style="display:inline-flex; align-items:center; gap:6px;">
                  <button type="button" class="btn btn-secondary btn-icon edit-btn-{{ $vendor->id }}" onclick='editVendor(@json($vendor))' title="Edit Vendor" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                  </button>
                  <button type="button" class="btn btn-danger btn-icon" onclick="confirmDeleteVendor({{ $vendor->id }}, '{{ addslashes($vendor->name) }}')" title="Delete Vendor" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
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

<!-- Vendor Modal (Add & Edit Form via AJAX) -->
<div id="vendor-modal" style="display:none;">
  <div class="vm-modal-box">
    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--slate-100); padding-bottom:16px; margin-bottom:18px;">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:38px; height:38px; border-radius:10px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/>
          </svg>
        </div>
        <div>
          <h3 id="modal-title" style="margin:0; font-size:1.1rem; font-weight:800; color:var(--slate-900);">Add New Vendor</h3>
          <p style="margin:2px 0 0; font-size:0.75rem; color:var(--slate-500);">Complete supplier & mill profile for procurement and purchase orders</p>
        </div>
      </div>
      <button type="button" onclick="closeVendorModal()" style="background:none; border:none; color:var(--slate-400); cursor:pointer; font-size:1.4rem; line-height:1; padding:4px;">
        &times;
      </button>
    </div>

    <form id="vendor-form" onsubmit="saveVendorAjax(event)">
      @csrf
      <input type="hidden" name="id" id="vendor-id" value="">

      <!-- Basic Details -->
      <div class="vm-modal-section-title">General Information</div>
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:14px; margin-bottom:12px;">
        <div class="form-group" style="margin:0;">
          <label class="form-label required">Vendor / Supplier Name</label>
          <input type="text" name="name" id="vend-name" class="form-control" required placeholder="e.g. Vardhman Textiles Ltd">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Vendor Code</label>
          <input type="text" name="code" id="vend-code" class="form-control" placeholder="Auto-generated if empty (e.g. VEND-001)">
        </div>
      </div>

      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:14px; margin-bottom:12px;">
        <div class="form-group" style="margin:0;">
          <label class="form-label">Category</label>
          <select name="category" id="vend-category" class="form-control">
            <option value="Fabric Mill">Fabric Mill</option>
            <option value="Yarn & Thread">Yarn & Thread</option>
            <option value="Trims & Accessories">Trims & Accessories (Buttons/Zippers)</option>
            <option value="Packaging Materials">Packaging Materials</option>
            <option value="Dyes & Chemicals">Dyes & Chemicals</option>
            <option value="Jobwork / Services">Jobwork / Services</option>
            <option value="General">General Supplier</option>
          </select>
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Company / Mill Name</label>
          <input type="text" name="company_name" id="vend-company" class="form-control" placeholder="e.g. Vardhman Spinning Mills Unit 2">
        </div>
      </div>

      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:14px; margin-bottom:12px;">
        <div class="form-group" style="margin:0;">
          <label class="form-label">Contact Person</label>
          <input type="text" name="contact_person" id="vend-contact-person" class="form-control" placeholder="e.g. Rajesh Singhal">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label required">Phone / Mobile</label>
          <input type="text" name="phone" id="vend-phone" class="form-control" required placeholder="e.g. 9820011223">
        </div>
      </div>

      <div class="form-group" style="margin-bottom:12px;">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" id="vend-email" class="form-control" placeholder="e.g. sales@vardhmanmills.com">
      </div>

      <!-- Financial & Tax Details -->
      <div class="vm-modal-section-title">Tax & Financial Credentials</div>
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:14px; margin-bottom:12px;">
        <div class="form-group" style="margin:0;">
          <label class="form-label">GSTIN Number</label>
          <input type="text" name="gst_number" id="vend-gst" class="form-control" placeholder="27AAAAA0000A1Z5" style="text-transform:uppercase;">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">PAN Number</label>
          <input type="text" name="pan_number" id="vend-pan" class="form-control" placeholder="AAAAA0000A" style="text-transform:uppercase;">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Credit Days</label>
          <input type="number" name="credit_days" id="vend-credit-days" class="form-control" placeholder="30" value="30">
        </div>
      </div>

      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:14px; margin-bottom:12px;">
        <div class="form-group" style="margin:0;">
          <label class="form-label">Current Outstanding Payables (₹)</label>
          <input type="number" step="0.01" name="outstanding" id="vend-outstanding" class="form-control" placeholder="0.00" value="0.00">
        </div>
      </div>

      <!-- Address Details -->
      <div class="vm-modal-section-title">Address & Mill Location</div>
      <div class="form-group" style="margin-bottom:12px;">
        <label class="form-label">Address</label>
        <textarea name="address" id="vend-address" class="form-control" rows="2" placeholder="Mill address, industrial area, street, landmark..."></textarea>
      </div>

      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap:14px; margin-bottom:20px;">
        <div class="form-group" style="margin:0;">
          <label class="form-label">City</label>
          <input type="text" name="city" id="vend-city" class="form-control" placeholder="e.g. Surat">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">State</label>
          <input type="text" name="state" id="vend-state" class="form-control" placeholder="e.g. Gujarat">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Pincode</label>
          <input type="text" name="pincode" id="vend-pincode" class="form-control" placeholder="e.g. 395002">
        </div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid var(--slate-100); padding-top:16px;">
        <button type="button" class="btn btn-secondary" onclick="closeVendorModal()">Cancel</button>
        <button type="submit" id="save-vend-btn" class="btn btn-primary" style="background:linear-gradient(135deg, #2563eb, #1d4ed8); font-weight:700;">Save Vendor</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Confirmation Modal (via AJAX) -->
<div id="delete-vendor-modal" style="display:none;">
  <div class="vm-modal-box" style="max-width:440px; text-align:center;">
    <div style="width:52px; height:52px; border-radius:50%; background:#fef2f2; color:#dc2626; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
      <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
    </div>
    <h3 style="margin:0 0 8px; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Delete Vendor</h3>
    <p style="margin:0 0 20px; font-size:0.875rem; color:var(--slate-500); line-height:1.4;">
      Are you sure you want to delete <strong id="delete-vend-name" style="color:var(--slate-800);">this vendor</strong>? This action cannot be undone.
    </p>

    <input type="hidden" id="delete-vend-id" value="">
    <div style="display:flex; justify-content:center; gap:10px;">
      <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
      <button type="button" id="confirm-delete-btn" class="btn btn-danger" onclick="deleteVendorAjax()" style="font-weight:700;">Yes, Delete Vendor</button>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  const CSRF_TOKEN = '{{ csrf_token() }}';

  function getCategoryBadgeClass(category) {
    const c = (category || '').toLowerCase();
    if (c.includes('fabric') || c.includes('mill')) return 'badge-primary';
    if (c.includes('trim') || c.includes('accessory')) return 'badge-warning';
    if (c.includes('yarn') || c.includes('thread')) return 'badge-purple';
    return 'badge-info';
  }

  // --- Modal Open / Close ---
  function openVendorModal() {
    const modal = document.getElementById('vendor-modal');
    if (!modal) return;
    
    document.getElementById('vendor-form').reset();
    document.getElementById('vendor-id').value = '';
    document.getElementById('modal-title').textContent = 'Add New Vendor';
    document.getElementById('save-vend-btn').textContent = 'Save Vendor';
    document.getElementById('save-vend-btn').disabled = false;
    document.getElementById('vend-credit-days').value = '30';
    document.getElementById('vend-outstanding').value = '0.00';
    
    modal.style.display = 'flex';
  }

  function closeVendorModal() {
    const modal = document.getElementById('vendor-modal');
    if (modal) modal.style.display = 'none';
  }

  function editVendor(vendor) {
    const modal = document.getElementById('vendor-modal');
    if (!modal) return;

    document.getElementById('modal-title').textContent = 'Edit Vendor';
    document.getElementById('save-vend-btn').textContent = 'Update Vendor';
    document.getElementById('save-vend-btn').disabled = false;
    document.getElementById('vendor-id').value = vendor.id;

    document.getElementById('vend-name').value = vendor.name || '';
    document.getElementById('vend-code').value = vendor.code || '';
    document.getElementById('vend-category').value = vendor.category || 'Fabric Mill';
    document.getElementById('vend-company').value = vendor.company_name || '';
    document.getElementById('vend-contact-person').value = vendor.contact_person || '';
    document.getElementById('vend-phone').value = vendor.phone || '';
    document.getElementById('vend-email').value = vendor.email || '';
    document.getElementById('vend-gst').value = vendor.gst_number || vendor.gstin || '';
    document.getElementById('vend-pan').value = vendor.pan_number || '';
    document.getElementById('vend-credit-days').value = vendor.credit_days !== undefined ? vendor.credit_days : 30;
    document.getElementById('vend-outstanding').value = vendor.outstanding || '0.00';
    document.getElementById('vend-address').value = vendor.address || vendor.billing_address || '';
    document.getElementById('vend-city').value = vendor.city || '';
    document.getElementById('vend-state').value = vendor.state || '';
    document.getElementById('vend-pincode').value = vendor.pincode || '';

    modal.style.display = 'flex';
  }

  function confirmDeleteVendor(id, name) {
    const modal = document.getElementById('delete-vendor-modal');
    if (!modal) return;
    
    document.getElementById('delete-vend-name').textContent = name;
    document.getElementById('delete-vend-id').value = id;
    document.getElementById('confirm-delete-btn').textContent = 'Yes, Delete Vendor';
    document.getElementById('confirm-delete-btn').disabled = false;
    modal.style.display = 'flex';
  }

  function closeDeleteModal() {
    const modal = document.getElementById('delete-vendor-modal');
    if (modal) modal.style.display = 'none';
  }

  // --- 1. STATUS CHANGE THROUGH TABLE VIA AJAX ---
  function changeVendorStatus(id, newStatus, selectEl) {
    const prevClass = selectEl.className;
    selectEl.style.opacity = '0.5';

    fetch('/masters/vendors/' + id, {
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
        selectEl.className = 'vm-status-select ' + newStatus.toLowerCase();
        
        // Update row data-status
        const row = document.getElementById('vendor-row-' + id);
        if (row) row.setAttribute('data-status', newStatus.toLowerCase());

        // Update stats if returned
        if (data.stats) updateKpiStats(data.stats);

        UI.showToast('Status Updated', 'Vendor status set to ' + newStatus, 'success');
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
  function saveVendorAjax(e) {
    e.preventDefault();

    const saveBtn = document.getElementById('save-vend-btn');
    const vendId = document.getElementById('vendor-id').value;
    const isEdit = Boolean(vendId);

    const payload = {
      name: document.getElementById('vend-name').value.trim(),
      code: document.getElementById('vend-code').value.trim(),
      category: document.getElementById('vend-category').value,
      company_name: document.getElementById('vend-company').value.trim(),
      contact_person: document.getElementById('vend-contact-person').value.trim(),
      phone: document.getElementById('vend-phone').value.trim(),
      email: document.getElementById('vend-email').value.trim(),
      gstin: document.getElementById('vend-gst').value.trim(),
      gst_number: document.getElementById('vend-gst').value.trim(),
      pan_number: document.getElementById('vend-pan').value.trim(),
      credit_days: parseInt(document.getElementById('vend-credit-days').value) || 0,
      outstanding: parseFloat(document.getElementById('vend-outstanding').value) || 0,
      address: document.getElementById('vend-address').value.trim(),
      city: document.getElementById('vend-city').value.trim(),
      state: document.getElementById('vend-state').value.trim(),
      pincode: document.getElementById('vend-pincode').value.trim()
    };

    if (!payload.name) {
      UI.showToast('Validation Error', 'Vendor Name is required', 'error');
      return;
    }
    if (!payload.phone) {
      UI.showToast('Validation Error', 'Phone Number is required', 'error');
      return;
    }

    saveBtn.disabled = true;
    saveBtn.textContent = isEdit ? 'Updating...' : 'Saving...';

    const url = isEdit ? ('/masters/vendors/' + vendId) : "{{ route('masters.vendors.store') }}";
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
      saveBtn.textContent = isEdit ? 'Update Vendor' : 'Save Vendor';

      if (data.success && data.vendor) {
        closeVendorModal();
        UI.showToast(isEdit ? 'Vendor Updated' : 'Vendor Created', data.message, 'success');

        if (isEdit) {
          updateTableRow(data.vendor);
        } else {
          prependTableRow(data.vendor);
        }

        if (data.stats) updateKpiStats(data.stats);
      } else {
        const msg = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message || 'Validation error');
        UI.showToast('Error', msg, 'error');
      }
    })
    .catch(err => {
      saveBtn.disabled = false;
      saveBtn.textContent = isEdit ? 'Update Vendor' : 'Save Vendor';
      UI.showToast('Error', 'Failed to save vendor details', 'error');
    });
  }

  // --- 3. DELETE USING AJAX ---
  function deleteVendorAjax() {
    const id = document.getElementById('delete-vend-id').value;
    if (!id) return;

    const delBtn = document.getElementById('confirm-delete-btn');
    delBtn.disabled = true;
    delBtn.textContent = 'Deleting...';

    fetch('/masters/vendors/' + id, {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': CSRF_TOKEN
      }
    })
    .then(res => res.json())
    .then(data => {
      delBtn.disabled = false;
      delBtn.textContent = 'Yes, Delete Vendor';
      closeDeleteModal();

      if (data.success) {
        UI.showToast('Vendor Deleted', data.message || 'Vendor removed successfully', 'warning');
        
        // Animate row removal
        const row = document.getElementById('vendor-row-' + id);
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
        UI.showToast('Error', data.message || 'Could not delete vendor', 'error');
      }
    })
    .catch(err => {
      delBtn.disabled = false;
      delBtn.textContent = 'Yes, Delete Vendor';
      UI.showToast('Error', 'Failed to delete vendor', 'error');
    });
  }

  // --- Dynamic Table DOM Manipulation ---
  function prependTableRow(v) {
    const tbody = document.getElementById('vendors-table-body');
    const tableContainer = document.getElementById('vendors-table-container');
    const emptyState = document.getElementById('vendors-empty-state');

    if (emptyState) emptyState.style.display = 'none';
    if (tableContainer) tableContainer.style.display = '';

    const tr = document.createElement('tr');
    tr.className = 'vm-row vm-row-transition';
    tr.id = 'vendor-row-' + v.id;
    tr.setAttribute('data-id', v.id);
    tr.setAttribute('data-category', (v.category || '').toLowerCase());
    tr.setAttribute('data-status', (v.status || 'active').toLowerCase());
    tr.setAttribute('data-balance', v.outstanding || 0);

    const initials = (v.name || 'VE').substring(0, 2).toUpperCase();
    const gstinBadge = (v.gstin || v.gst_number)
      ? `<span class="badge" style="background:#f1f5f9; color:#1e293b; font-family:monospace; font-size:0.75rem; font-weight:600; border:1px solid #e2e8f0;">${v.gstin || v.gst_number}</span>`
      : `<span style="font-size:0.75rem; color:var(--slate-400);">Unregistered</span>`;

    const badgeClass = getCategoryBadgeClass(v.category);
    const statusVal = (v.status || 'Active');
    const statusLower = statusVal.toLowerCase();

    tr.innerHTML = `
      <td class="row-index" style="text-align: center; font-weight: 600; color: var(--slate-400);">1</td>
      <td>
        <div style="display:flex; align-items:center; gap:10px;">
          <div class="vm-avatar" id="avatar-${v.id}">${initials}</div>
          <div>
            <div class="vend-name-val" style="font-weight:700; color:var(--slate-900); font-size:0.9rem;">${v.name}</div>
            <div class="vend-code-val" style="font-size:0.75rem; color:var(--slate-500); font-family:monospace;">${v.code || ('VEND-' + String(v.id).padStart(3, '0'))}</div>
          </div>
        </div>
      </td>
      <td class="vend-cat-cell">
        <span class="badge ${badgeClass}" style="font-size:0.75rem; font-weight:600; padding:4px 8px; border-radius:8px;">
          ${v.category || 'General Supplier'}
        </span>
      </td>
      <td>
        <div class="vend-contact-val" style="font-weight:600; color:var(--slate-800); font-size:0.85rem;">${v.contact_person || (v.company_name || '—')}</div>
        <div class="vend-phone-val" style="font-size:0.75rem; color:var(--slate-500);">${v.phone || '—'}</div>
      </td>
      <td>
        <div class="vend-city-val" style="font-weight:600; color:var(--slate-700); font-size:0.85rem;">${v.city || '—'}</div>
        <div class="vend-state-val" style="font-size:0.75rem; color:var(--slate-500);">${v.state || '—'}</div>
      </td>
      <td class="vend-gst-cell">${gstinBadge}</td>
      <td class="vend-credit-cell" style="text-align: center; font-weight: 600; color: var(--slate-700); font-size:0.85rem;">${v.credit_days ? (v.credit_days + ' Days') : 'Immediate / Cash'}</td>
      <td class="vend-outstanding-val" style="text-align: right; font-weight: 700; color: ${Number(v.outstanding || 0) > 0 ? '#dc2626' : '#059669'};">₹${Number(v.outstanding || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
      <td style="text-align: center;">
        <select class="vm-status-select ${statusLower}" onchange="changeVendorStatus(${v.id}, this.value, this)" title="Click to change status">
          <option value="Active" ${statusLower === 'active' ? 'selected' : ''}>Active</option>
          <option value="Inactive" ${statusLower === 'inactive' ? 'selected' : ''}>Inactive</option>
          <option value="Blocked" ${statusLower === 'blocked' ? 'selected' : ''}>Blocked</option>
        </select>
      </td>
      <td style="text-align: center;">
        <div style="display:inline-flex; align-items:center; gap:6px;">
          <button type="button" class="btn btn-secondary btn-icon edit-btn-${v.id}" title="Edit Vendor" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
              <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
          </button>
          <button type="button" class="btn btn-danger btn-icon" onclick="confirmDeleteVendor(${v.id}, '${(v.name || '').replace(/'/g, "\\'")}')" title="Delete Vendor" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="3 6 5 6 21 6"/>
              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
            </svg>
          </button>
        </div>
      </td>
    `;

    // Attach edit onclick handler
    const editBtn = tr.querySelector(`.edit-btn-${v.id}`);
    if (editBtn) {
      editBtn.onclick = () => editVendor(v);
    }

    tbody.insertBefore(tr, tbody.firstChild);
    reindexRows();
  }

  function updateTableRow(v) {
    const row = document.getElementById('vendor-row-' + v.id);
    if (!row) return;

    row.querySelector('.vend-name-val').textContent = v.name;
    row.querySelector('.vend-code-val').textContent = v.code || ('VEND-' + String(v.id).padStart(3, '0'));
    row.querySelector('.vend-contact-val').textContent = v.contact_person || (v.company_name || '—');
    row.querySelector('.vend-phone-val').textContent = v.phone || '—';
    row.querySelector('.vend-city-val').textContent = v.city || '—';
    row.querySelector('.vend-state-val').textContent = v.state || '—';
    
    const initials = (v.name || 'VE').substring(0, 2).toUpperCase();
    const avatar = document.getElementById('avatar-' + v.id);
    if (avatar) avatar.textContent = initials;

    const catCell = row.querySelector('.vend-cat-cell');
    if (catCell) {
      const badgeClass = getCategoryBadgeClass(v.category);
      catCell.innerHTML = `<span class="badge ${badgeClass}" style="font-size:0.75rem; font-weight:600; padding:4px 8px; border-radius:8px;">${v.category || 'General Supplier'}</span>`;
    }

    const gstinCell = row.querySelector('.vend-gst-cell');
    if (gstinCell) {
      gstinCell.innerHTML = (v.gstin || v.gst_number)
        ? `<span class="badge" style="background:#f1f5f9; color:#1e293b; font-family:monospace; font-size:0.75rem; font-weight:600; border:1px solid #e2e8f0;">${v.gstin || v.gst_number}</span>`
        : `<span style="font-size:0.75rem; color:var(--slate-400);">Unregistered</span>`;
    }

    const creditCell = row.querySelector('.vend-credit-cell');
    if (creditCell) {
      creditCell.textContent = v.credit_days ? (v.credit_days + ' Days') : 'Immediate / Cash';
    }

    const outstandingCell = row.querySelector('.vend-outstanding-val');
    if (outstandingCell) {
      const outVal = Number(v.outstanding || 0);
      outstandingCell.textContent = '₹' + outVal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
      outstandingCell.style.color = outVal > 0 ? '#dc2626' : '#059669';
    }

    // Update edit button handler with fresh data
    const editBtn = row.querySelector('.btn-secondary');
    if (editBtn) {
      editBtn.onclick = () => editVendor(v);
    }
  }

  function reindexRows() {
    const rows = document.querySelectorAll('#vendors-table-body tr');
    rows.forEach((row, i) => {
      const idxCell = row.querySelector('.row-index');
      if (idxCell) idxCell.textContent = i + 1;
    });
  }

  function checkEmptyState() {
    const rows = document.querySelectorAll('#vendors-table-body tr');
    const emptyState = document.getElementById('vendors-empty-state');
    const tableContainer = document.getElementById('vendors-table-container');

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
    if (document.getElementById('vendor-badge-count')) document.getElementById('vendor-badge-count').textContent = stats.total + ' Records';

    if (document.getElementById('stat-fabric-count') && stats.fabricVendors !== undefined) {
      document.getElementById('stat-fabric-count').textContent = stats.fabricVendors;
    }

    if (document.getElementById('stat-trim-count') && stats.trimVendors !== undefined) {
      document.getElementById('stat-trim-count').textContent = stats.trimVendors;
    }

    if (document.getElementById('stat-total-outstanding') && stats.totalOutstanding !== undefined) {
      const val = Number(stats.totalOutstanding);
      document.getElementById('stat-total-outstanding').textContent = '₹' + val.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
      document.getElementById('stat-total-outstanding').style.color = val > 0 ? '#dc2626' : '#059669';
    }
  }

  // --- Live Table Filter & Pills ---
  function filterVendorTable(query) {
    query = (query || '').toLowerCase().trim();
    const rows = document.querySelectorAll('#vendors-table-body tr');
    rows.forEach(row => {
      const text = row.innerText.toLowerCase();
      row.style.display = text.includes(query) ? '' : 'none';
    });
  }

  function applyVendorFilter(type, btn) {
    document.querySelectorAll('.vm-filter-pill').forEach(el => el.classList.remove('active'));
    if (btn) btn.classList.add('active');

    const rows = document.querySelectorAll('#vendors-table-body tr.vm-row');
    rows.forEach(row => {
      const status = row.getAttribute('data-status');
      const category = (row.getAttribute('data-category') || '').toLowerCase();
      const balance = parseFloat(row.getAttribute('data-balance') || 0);

      if (type === 'all') {
        row.style.display = '';
      } else if (type === 'active') {
        row.style.display = (status === 'active') ? '' : 'none';
      } else if (type === 'fabric') {
        row.style.display = (category.includes('fabric') || category.includes('mill') || category.includes('yarn')) ? '' : 'none';
      } else if (type === 'trims') {
        row.style.display = (category.includes('trim') || category.includes('accessory') || category.includes('packaging')) ? '' : 'none';
      } else if (type === 'blocked') {
        row.style.display = (status === 'blocked' || status === 'inactive') ? '' : 'none';
      } else if (type === 'balance') {
        row.style.display = (balance > 0) ? '' : 'none';
      }
    });
  }

  // Close modals on clicking backdrop
  window.addEventListener('click', function(e) {
    const vendModal = document.getElementById('vendor-modal');
    const delModal = document.getElementById('delete-vendor-modal');
    if (e.target === vendModal) closeVendorModal();
    if (e.target === delModal) closeDeleteModal();
  });
</script>
@endpush
