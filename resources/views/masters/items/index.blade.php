@extends('layouts.app')

@section('title', 'Item Master & Inventory - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Masters Management</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Item Master</span></div>
@endsection

@push('styles')
<style>
  /* Item Master Modern UI Styles */
  .itm-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 16px;
  }

  .itm-stat-card {
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

  .itm-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.08);
    border-color: var(--slate-300);
  }

  .itm-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--stat-accent, var(--primary-500));
  }

  .itm-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .itm-stat-info {
    flex: 1;
    min-width: 0;
  }

  .itm-stat-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--slate-500);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 4px;
  }

  .itm-stat-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--slate-900);
    line-height: 1.2;
    display: flex;
    align-items: baseline;
    gap: 8px;
    flex-wrap: wrap;
  }

  .itm-main-card {
    background: #ffffff;
    border-radius: 18px;
    border: 1px solid var(--slate-200);
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    overflow: hidden;
  }

  .itm-card-header {
    padding: 22px 24px;
    border-bottom: 1px solid var(--slate-100);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    background: #ffffff;
  }

  .itm-toolbar {
    padding: 16px 24px;
    background: #f8fafc;
    border-bottom: 1px solid var(--slate-200);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    flex-wrap: wrap;
  }

  .itm-search-wrap {
    position: relative;
    flex: 1;
    min-width: 260px;
    max-width: 400px;
  }

  .itm-search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--slate-400);
    pointer-events: none;
  }

  .itm-search-input {
    width: 100%;
    padding: 9px 14px 9px 38px;
    border: 1px solid var(--slate-300);
    border-radius: 10px;
    font-size: 0.85rem;
    background: #ffffff;
    color: var(--slate-800);
    transition: all 0.2s ease;
  }

  .itm-search-input:focus {
    outline: none;
    border-color: var(--primary-500);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
  }

  .itm-filter-pills {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
  }

  .itm-filter-pill {
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

  .itm-filter-pill:hover {
    border-color: var(--slate-400);
    color: var(--slate-900);
  }

  .itm-filter-pill.active {
    background: var(--primary-600);
    color: #ffffff;
    border-color: var(--primary-600);
    box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
  }

  /* Empty State Modern Box */
  .itm-empty-box {
    padding: 60px 24px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
  }

  .itm-empty-icon-ring {
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

  .itm-empty-features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    max-width: 780px;
    width: 100%;
    margin-top: 32px;
    text-align: left;
  }

  .itm-feature-card {
    background: #ffffff;
    border: 1px solid var(--slate-200);
    border-radius: 12px;
    padding: 16px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
  }

  .itm-avatar {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, #4f46e5, #4338ca);
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
  .itm-status-select {
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

  .itm-status-select:focus {
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
  }

  .itm-status-select.active {
    background-color: #ecfdf5;
    color: #059669;
    border-color: #a7f3d0;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23059669' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  }

  .itm-status-select.blocked {
    background-color: #fef2f2;
    color: #dc2626;
    border-color: #fecaca;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23dc2626' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  }

  .itm-status-select.inactive {
    background-color: #f1f5f9;
    color: #64748b;
    border-color: #cbd5e1;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  }

  /* Modal Styling */
  #item-modal, #delete-item-modal {
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

  .itm-modal-box {
    background: #ffffff;
    border-radius: 20px;
    width: 100%;
    max-width: 720px;
    padding: 26px;
    max-height: 92vh;
    overflow-y: auto;
    box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.3);
    border: 1px solid var(--slate-200);
    position: relative;
    animation: itmModalFade 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  }

  @keyframes itmModalFade {
    from { opacity: 0; transform: scale(0.96) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
  }

  .itm-modal-section-title {
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

  .itm-modal-section-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--slate-200);
  }

  .itm-row-transition {
    transition: all 0.3s ease;
  }
</style>
@endpush

@section('content')
<div style="display:flex; flex-direction:column; gap:22px;">

  <!-- Top KPI Dynamic Metrics Row -->
  <div class="itm-stats-grid">
    
    <!-- 1. Total Item SKUs -->
    <div class="itm-stat-card" style="--stat-accent: #2563eb;">
      <div class="itm-stat-icon" style="background:#eff6ff; color:#2563eb;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
          <polyline points="3.29 7 12 12 20.71 7"/>
          <line x1="12" y1="22" x2="12" y2="12"/>
        </svg>
      </div>
      <div class="itm-stat-info">
        <div class="itm-stat-label">Total Item SKUs</div>
        <div class="itm-stat-value">
          <span id="stat-total-count">{{ $stats['total'] }}</span>
          <span id="stat-active-badge" style="font-size:0.75rem; font-weight:700; color:#059669; background:#ecfdf5; padding:2px 8px; border-radius:12px; border:1px solid #a7f3d0;">
            <span id="stat-active-count">{{ $stats['active'] }}</span> Active
          </span>
        </div>
      </div>
    </div>

    <!-- 2. Fabric Rolls & Yarn -->
    <div class="itm-stat-card" style="--stat-accent: #059669;">
      <div class="itm-stat-icon" style="background:#ecfdf5; color:#059669;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 22h14a2 2 0 0 0 2-2V7.5L14.5 2H6a2 2 0 0 0-2 2v4"/>
          <polyline points="14 2 14 8 20 8"/>
        </svg>
      </div>
      <div class="itm-stat-info">
        <div class="itm-stat-label">Fabric Rolls & Yarn</div>
        <div class="itm-stat-value">
          <span id="stat-fabric-count">{{ $stats['fabricCount'] }}</span>
          <span style="font-size:0.75rem; font-weight:600; color:var(--slate-500);">Raw Materials</span>
        </div>
      </div>
    </div>

    <!-- 3. Trims & Accessories -->
    <div class="itm-stat-card" style="--stat-accent: #8b5cf6;">
      <div class="itm-stat-icon" style="background:#faf5ff; color:#7c3aed;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/>
          <line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/>
          <line x1="8.12" y1="8.12" x2="12" y2="12"/>
        </svg>
      </div>
      <div class="itm-stat-info">
        <div class="itm-stat-label">Trims & Packaging</div>
        <div class="itm-stat-value">
          <span id="stat-trim-count">{{ $stats['trimsCount'] }}</span>
          <span style="font-size:0.75rem; font-weight:600; color:var(--slate-500);">Components</span>
        </div>
      </div>
    </div>

    <!-- 4. Low Stock Alerts -->
    <div class="itm-stat-card" style="--stat-accent: {{ $stats['lowStockCount'] > 0 ? '#ef4444' : '#10b981' }};">
      <div class="itm-stat-icon" style="background:{{ $stats['lowStockCount'] > 0 ? '#fef2f2' : '#ecfdf5' }}; color:{{ $stats['lowStockCount'] > 0 ? '#dc2626' : '#059669' }};">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
          <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
      </div>
      <div class="itm-stat-info">
        <div class="itm-stat-label">Stock Reorder Alerts</div>
        <div class="itm-stat-value" id="stat-low-stock" style="color:{{ $stats['lowStockCount'] > 0 ? '#dc2626' : '#059669' }};">
          {{ $stats['lowStockCount'] }} Low Stock
        </div>
      </div>
    </div>

  </div>

  <!-- Main Card Container -->
  <div class="itm-main-card">
    
    <!-- Action Header -->
    <div class="itm-card-header">
      <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:40px; height:40px; border-radius:10px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
            <polyline points="3.29 7 12 12 20.71 7"/>
          </svg>
        </div>
        <div>
          <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900); display:flex; align-items:center; gap:8px;">
            Item & Fabric Inventory
            <span class="badge badge-primary" id="item-badge-count" style="font-size:0.75rem; padding:2px 8px; border-radius:12px;">{{ count($items) }} Records</span>
          </h3>
          <p style="margin:3px 0 0; font-size:0.8rem; color:var(--slate-500);">Raw materials, cotton weaves, polyester, buttons, zippers, thread & packaging</p>
        </div>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('items-table', 'Item_Master.csv')" style="display:inline-flex; align-items:center; gap:6px; font-weight:600;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
          </svg>
          Export CSV
        </button>

        <button class="btn btn-primary btn-sm" onclick="openItemModal()" style="display:inline-flex; align-items:center; gap:6px; font-weight:700; background:linear-gradient(135deg, #2563eb, #1d4ed8); box-shadow:0 4px 10px rgba(37,99,235,0.25); cursor:pointer;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
          </svg>
          Add Item SKU
        </button>
      </div>
    </div>

    <!-- Quick Filter Bar -->
    <div class="itm-toolbar">
      <div class="itm-search-wrap">
        <svg class="itm-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"/>
          <line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" id="itm-filter-input" class="itm-search-input" placeholder="Search item name, code, category, HSN, location..." onkeyup="filterItemTable(this.value)">
      </div>

      <div class="itm-filter-pills">
        <button type="button" class="itm-filter-pill active" id="pill-all" onclick="applyItemFilter('all', this)">All (<span id="pill-count-all">{{ $stats['total'] }}</span>)</button>
        <button type="button" class="itm-filter-pill" id="pill-active" onclick="applyItemFilter('active', this)">Active (<span id="pill-count-active">{{ $stats['active'] }}</span>)</button>
        <button type="button" class="itm-filter-pill" onclick="applyItemFilter('fabric', this)">Fabric</button>
        <button type="button" class="itm-filter-pill" onclick="applyItemFilter('yarn', this)">Yarn & Thread</button>
        <button type="button" class="itm-filter-pill" onclick="applyItemFilter('trims', this)">Trims</button>
        <button type="button" class="itm-filter-pill" onclick="applyItemFilter('lowstock', this)">Low Stock</button>
      </div>
    </div>

    <!-- Empty State Box (Toggled if 0 records) -->
    <div id="items-empty-state" class="itm-empty-box" style="{{ $items->isEmpty() ? 'display:flex;' : 'display:none;' }}">
      <div class="itm-empty-icon-ring">
        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
          <polyline points="3.29 7 12 12 20.71 7"/>
        </svg>
      </div>

      <h3 style="margin:0 0 8px; font-size:1.25rem; font-weight:800; color:var(--slate-900);">No Items in Inventory Yet</h3>
      <p style="margin:0 0 20px; font-size:0.875rem; color:var(--slate-500); max-width:480px; line-height:1.5;">
        Add raw materials, fabrics, trims, buttons, and finished goods to manage purchase orders, cutting consumption, and inventory stock balances.
      </p>

      <button type="button" class="btn btn-primary" onclick="openItemModal()" style="display:inline-flex; align-items:center; gap:8px; padding:10px 22px; font-size:0.9rem; font-weight:700; border-radius:10px; background:linear-gradient(135deg, #2563eb, #1d4ed8); box-shadow:0 6px 16px rgba(37,99,235,0.25); cursor:pointer;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"/>
          <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Add Your First Item SKU
      </button>

      <!-- Feature cards -->
      <div class="itm-empty-features">
        <div class="itm-feature-card">
          <div style="width:34px; height:34px; border-radius:8px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h14a2 2 0 0 0 2-2V7.5L14.5 2H6a2 2 0 0 0-2 2v4"/><polyline points="14 2 14 8 20 8"/></svg>
          </div>
          <div>
            <div style="font-weight:700; font-size:0.825rem; color:var(--slate-800);">Fabric & Roll Specifications</div>
            <div style="font-size:0.75rem; color:var(--slate-500); margin-top:2px;">Track weaves, GSM, fabric blends, color shades & sizes</div>
          </div>
        </div>

        <div class="itm-feature-card">
          <div style="width:34px; height:34px; border-radius:8px; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/></svg>
          </div>
          <div>
            <div style="font-weight:700; font-size:0.825rem; color:var(--slate-800);">Automated Reorder Alerts</div>
            <div style="font-size:0.75rem; color:var(--slate-500); margin-top:2px;">Set min safety thresholds and prevent fabric shortage in production</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Data Table Container -->
    <div class="table-responsive" id="items-table-container" style="{{ $items->isEmpty() ? 'display:none;' : '' }}">
      <table class="data-table" id="items-table">
        <thead>
          <tr>
            <th style="width: 50px; text-align: center;">#</th>
            <th>Item SKU & Name</th>
            <th>Category</th>
            <th>UOM</th>
            <th style="text-align: right;">Unit Cost</th>
            <th style="text-align: right;">Current Stock</th>
            <th style="text-align: right;">Reorder Min</th>
            <th style="text-align: center;">Stock Status</th>
            <th>HSN / Location</th>
            <th style="text-align: center; width: 130px;">Status</th>
            <th style="text-align: center; width: 100px;">Actions</th>
          </tr>
        </thead>
        <tbody id="items-table-body">
          @foreach($items as $index => $itm)
            @php
              $categoryLower = strtolower($itm->category ?? '');
              $catBadge = 'badge-info';
              if (str_contains($categoryLower, 'fabric')) $catBadge = 'badge-primary';
              elseif (str_contains($categoryLower, 'yarn')) $catBadge = 'badge-purple';
              elseif (str_contains($categoryLower, 'trim')) $catBadge = 'badge-warning';
              elseif (str_contains($categoryLower, 'pack')) $catBadge = 'badge-cyan';
              elseif (str_contains($categoryLower, 'finish')) $catBadge = 'badge-success';

              $isLowStock = ($itm->current_stock <= $itm->min_stock);
            @endphp
            <tr class="itm-row itm-row-transition" id="item-row-{{ $itm->id }}" data-id="{{ $itm->id }}" data-category="{{ $categoryLower }}" data-lowstock="{{ $isLowStock ? '1' : '0' }}" data-status="{{ strtolower($itm->status ?? 'active') }}">
              <td class="row-index" style="text-align: center; font-weight: 600; color: var(--slate-400);">{{ $index + 1 }}</td>
              <td>
                <div style="display:flex; align-items:center; gap:10px;">
                  <div class="itm-avatar" id="avatar-{{ $itm->id }}">
                    {{ strtoupper(substr($itm->name, 0, 2)) }}
                  </div>
                  <div>
                    <div class="itm-name-val" style="font-weight:700; color:var(--slate-900); font-size:0.9rem;">{{ $itm->name }}</div>
                    <div style="display:flex; align-items:center; gap:6px;">
                      <span class="itm-code-val" style="font-size:0.75rem; color:var(--slate-500); font-family:monospace;">{{ $itm->code ?? ('ITM-'.str_pad($itm->id, 3, '0', STR_PAD_LEFT)) }}</span>
                      @if($itm->fabric || $itm->color || $itm->size)
                        <span class="itm-spec-val" style="font-size:0.75rem; color:var(--slate-400);">• {{ implode(' • ', array_filter([$itm->fabric, $itm->color, $itm->size])) }}</span>
                      @endif
                    </div>
                  </div>
                </div>
              </td>
              <td class="itm-cat-cell">
                <span class="badge {{ $catBadge }}" style="font-size:0.75rem; font-weight:600; padding:4px 8px; border-radius:8px;">
                  {{ $itm->category }}
                </span>
              </td>
              <td class="itm-unit-val" style="font-weight:600; color:var(--slate-700);">
                {{ $itm->unit ?: 'Meters' }}
              </td>
              <td class="itm-cost-val" style="text-align: right; font-weight: 700; color: var(--slate-800);">
                ₹{{ number_format($itm->unit_cost, 2) }}
              </td>
              <td class="itm-stock-val" style="text-align: right; font-weight: 800; color: {{ $isLowStock ? '#dc2626' : '#059669' }};">
                {{ number_format($itm->current_stock, 2) }}
              </td>
              <td class="itm-minstock-val" style="text-align: right; font-size:0.8rem; color:var(--slate-500); font-weight:600;">
                {{ number_format($itm->min_stock, 2) }}
              </td>
              <td class="itm-stock-status-cell" style="text-align: center;">
                @if($isLowStock)
                  <span class="badge badge-danger" style="font-size:0.7rem; font-weight:700;">Low Stock</span>
                @else
                  <span class="badge badge-success" style="font-size:0.7rem; font-weight:700;">In Stock</span>
                @endif
              </td>
              <td>
                <div class="itm-hsn-val" style="font-family:monospace; font-size:0.75rem; font-weight:600; color:var(--slate-700);">{{ $itm->hsn_code ?: '—' }}</div>
                <div class="itm-location-val" style="font-size:0.75rem; color:var(--slate-400);">{{ $itm->location ?: '—' }}</div>
              </td>
              <td style="text-align: center;">
                <!-- Direct Table Status Changer -->
                <select class="itm-status-select {{ strtolower($itm->status ?? 'active') }}" onchange="changeItemStatus({{ $itm->id }}, this.value, this)" title="Click to change status">
                  <option value="Active" {{ strtolower($itm->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                  <option value="Inactive" {{ strtolower($itm->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                  <option value="Blocked" {{ strtolower($itm->status ?? '') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                </select>
              </td>
              <td style="text-align: center;">
                <div style="display:inline-flex; align-items:center; gap:6px;">
                  <button type="button" class="btn btn-secondary btn-icon edit-btn-{{ $itm->id }}" onclick='editItem(@json($itm))' title="Edit Item SKU" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                  </button>
                  <button type="button" class="btn btn-danger btn-icon" onclick="confirmDeleteItem({{ $itm->id }}, '{{ addslashes($itm->name) }}')" title="Delete Item SKU" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
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

<!-- Item Modal (Add & Edit Form via AJAX) -->
<div id="item-modal" style="display:none;">
  <div class="itm-modal-box">
    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--slate-100); padding-bottom:16px; margin-bottom:18px;">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:38px; height:38px; border-radius:10px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
            <polyline points="3.29 7 12 12 20.71 7"/>
          </svg>
        </div>
        <div>
          <h3 id="imodual-title" style="margin:0; font-size:1.1rem; font-weight:800; color:var(--slate-900);">Add Item SKU</h3>
          <p style="margin:2px 0 0; font-size:0.75rem; color:var(--slate-500);">Complete raw material, fabric roll & accessories profile</p>
        </div>
      </div>
      <button type="button" onclick="closeItemModal()" style="background:none; border:none; color:var(--slate-400); cursor:pointer; font-size:1.4rem; line-height:1; padding:4px;">
        &times;
      </button>
    </div>

    <form id="item-form" onsubmit="saveItemAjax(event)">
      @csrf
      <input type="hidden" name="id" id="item-id" value="">

      <!-- Basic Identification -->
      <div class="itm-modal-section-title">General Identification</div>
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:14px; margin-bottom:12px;">
        <div class="form-group" style="margin:0;">
          <label class="form-label required">Item / Fabric SKU Name</label>
          <input type="text" name="name" id="itm_name" class="form-control" required placeholder="e.g. 100% Cotton Single Jersey 180 GSM">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Item SKU Code</label>
          <input type="text" name="code" id="itm_code" class="form-control" placeholder="Auto-generated if empty (e.g. FAB-001)">
        </div>
      </div>

      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:14px; margin-bottom:12px;">
        <div class="form-group" style="margin:0;">
          <label class="form-label required">Category</label>
          <select name="category" id="itm_category" class="form-control" required>
            <option value="Fabric">Fabric (Knit/Woven Rolls)</option>
            <option value="Yarn">Yarn & Thread</option>
            <option value="Trims">Trims & Accessories (Buttons/Zippers)</option>
            <option value="Packaging">Packaging (Polybag, Tag, Box)</option>
            <option value="Finished Goods">Finished Garments</option>
          </select>
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Brand / Mill Source</label>
          <input type="text" name="brand" id="itm_brand" class="form-control" placeholder="e.g. Arvind / Vardhman">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Unit of Measure (UOM)</label>
          <select name="unit" id="itm_unit" class="form-control">
            <option value="Meters">Meters (m)</option>
            <option value="Kilograms">Kilograms (kg)</option>
            <option value="Pieces">Pieces (pcs)</option>
            <option value="Yards">Yards (yd)</option>
            <option value="Rolls">Rolls</option>
            <option value="Gross">Gross (144 pcs)</option>
            <option value="Cones">Cones</option>
          </select>
        </div>
      </div>

      <!-- Specifications -->
      <div class="itm-modal-section-title">Specifications & Variants</div>
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:14px; margin-bottom:12px;">
        <div class="form-group" style="margin:0;">
          <label class="form-label">Fabric Blend / Weave</label>
          <input type="text" name="fabric" id="itm_fabric" class="form-control" placeholder="e.g. 100% Cotton Bio-wash">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Color / Shade</label>
          <input type="text" name="color" id="itm_color" class="form-control" placeholder="e.g. Navy Blue #001">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Size / Width</label>
          <input type="text" name="size" id="itm_size" class="form-control" placeholder="e.g. 60 Inch Dia / M">
        </div>
      </div>

      <!-- Pricing & Inventory Stock -->
      <div class="itm-modal-section-title">Pricing & Stock Thresholds</div>
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:14px; margin-bottom:12px;">
        <div class="form-group" style="margin:0;">
          <label class="form-label">Standard Unit Cost (₹)</label>
          <input type="number" step="0.01" name="unit_cost" id="itm_cost" class="form-control" placeholder="0.00" value="280.00">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Opening / Current Stock</label>
          <input type="number" step="0.01" name="current_stock" id="itm_stock" class="form-control" placeholder="0.00" value="500.00">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Min Reorder Level</label>
          <input type="number" step="0.01" name="min_stock" id="itm_min_stock" class="form-control" placeholder="0.00" value="100.00">
        </div>
      </div>

      <!-- Tax & Storage -->
      <div class="itm-modal-section-title">Compliance & Storage Location</div>
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:14px; margin-bottom:20px;">
        <div class="form-group" style="margin:0;">
          <label class="form-label">HSN Code</label>
          <input type="text" name="hsn_code" id="itm_hsn" class="form-control" placeholder="e.g. 5208">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Storage Location / Rack</label>
          <input type="text" name="location" id="itm_location" class="form-control" placeholder="e.g. Warehouse A - Rack 04">
        </div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid var(--slate-100); padding-top:16px;">
        <button type="button" class="btn btn-secondary" onclick="closeItemModal()">Cancel</button>
        <button type="submit" id="save-itm-btn" class="btn btn-primary" style="background:linear-gradient(135deg, #2563eb, #1d4ed8); font-weight:700;">Save Item SKU</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Confirmation Modal (via AJAX) -->
<div id="delete-item-modal" style="display:none;">
  <div class="itm-modal-box" style="max-width:440px; text-align:center;">
    <div style="width:52px; height:52px; border-radius:50%; background:#fef2f2; color:#dc2626; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
      <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
    </div>
    <h3 style="margin:0 0 8px; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Delete Item SKU</h3>
    <p style="margin:0 0 20px; font-size:0.875rem; color:var(--slate-500); line-height:1.4;">
      Are you sure you want to delete <strong id="delete-itm-name" style="color:var(--slate-800);">this item</strong>? This action cannot be undone.
    </p>

    <input type="hidden" id="delete-itm-id" value="">
    <div style="display:flex; justify-content:center; gap:10px;">
      <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
      <button type="button" id="confirm-delete-btn" class="btn btn-danger" onclick="deleteItemAjax()" style="font-weight:700;">Yes, Delete Item</button>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  const CSRF_TOKEN = '{{ csrf_token() }}';

  function getItemCategoryBadgeClass(category) {
    const c = (category || '').toLowerCase();
    if (c.includes('fabric')) return 'badge-primary';
    if (c.includes('yarn') || c.includes('thread')) return 'badge-purple';
    if (c.includes('trim')) return 'badge-warning';
    if (c.includes('pack')) return 'badge-cyan';
    if (c.includes('finish')) return 'badge-success';
    return 'badge-info';
  }

  // --- Modal Open / Close ---
  function openItemModal() {
    const modal = document.getElementById('item-modal');
    if (!modal) return;
    
    document.getElementById('item-form').reset();
    document.getElementById('item-id').value = '';
    document.getElementById('imodual-title').textContent = 'Add Item SKU';
    document.getElementById('save-itm-btn').textContent = 'Save Item SKU';
    document.getElementById('save-itm-btn').disabled = false;
    document.getElementById('itm_cost').value = '280.00';
    document.getElementById('itm_stock').value = '500.00';
    document.getElementById('itm_min_stock').value = '100.00';
    
    modal.style.display = 'flex';
  }

  function closeItemModal() {
    const modal = document.getElementById('item-modal');
    if (modal) modal.style.display = 'none';
  }

  function editItem(itm) {
    const modal = document.getElementById('item-modal');
    if (!modal) return;

    document.getElementById('imodual-title').textContent = 'Edit Item: ' + (itm.code || itm.name);
    document.getElementById('save-itm-btn').textContent = 'Update Item SKU';
    document.getElementById('save-itm-btn').disabled = false;
    document.getElementById('item-id').value = itm.id;

    document.getElementById('itm_name').value = itm.name || '';
    document.getElementById('itm_code').value = itm.code || '';
    document.getElementById('itm_category').value = itm.category || 'Fabric';
    document.getElementById('itm_brand').value = itm.brand || '';
    document.getElementById('itm_unit').value = itm.unit || 'Meters';
    document.getElementById('itm_fabric').value = itm.fabric || '';
    document.getElementById('itm_color').value = itm.color || '';
    document.getElementById('itm_size').value = itm.size || '';
    document.getElementById('itm_cost').value = itm.unit_cost !== undefined ? itm.unit_cost : 280;
    document.getElementById('itm_stock').value = itm.current_stock !== undefined ? itm.current_stock : 500;
    document.getElementById('itm_min_stock').value = itm.min_stock !== undefined ? itm.min_stock : 100;
    document.getElementById('itm_hsn').value = itm.hsn_code || '';
    document.getElementById('itm_location').value = itm.location || '';

    modal.style.display = 'flex';
  }

  function confirmDeleteItem(id, name) {
    const modal = document.getElementById('delete-item-modal');
    if (!modal) return;
    
    document.getElementById('delete-itm-name').textContent = name;
    document.getElementById('delete-itm-id').value = id;
    document.getElementById('confirm-delete-btn').textContent = 'Yes, Delete Item';
    document.getElementById('confirm-delete-btn').disabled = false;
    modal.style.display = 'flex';
  }

  function closeDeleteModal() {
    const modal = document.getElementById('delete-item-modal');
    if (modal) modal.style.display = 'none';
  }

  // --- 1. STATUS CHANGE THROUGH TABLE VIA AJAX ---
  function changeItemStatus(id, newStatus, selectEl) {
    const prevClass = selectEl.className;
    selectEl.style.opacity = '0.5';

    fetch('/masters/items/' + id, {
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
        selectEl.className = 'itm-status-select ' + newStatus.toLowerCase();
        
        const row = document.getElementById('item-row-' + id);
        if (row) row.setAttribute('data-status', newStatus.toLowerCase());

        if (data.stats) updateKpiStats(data.stats);

        UI.showToast('Status Updated', 'Item SKU status set to ' + newStatus, 'success');
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
  function saveItemAjax(e) {
    e.preventDefault();

    const saveBtn = document.getElementById('save-itm-btn');
    const itmId = document.getElementById('item-id').value;
    const isEdit = Boolean(itmId);

    const payload = {
      name: document.getElementById('itm_name').value.trim(),
      code: document.getElementById('itm_code').value.trim(),
      category: document.getElementById('itm_category').value,
      brand: document.getElementById('itm_brand').value.trim(),
      unit: document.getElementById('itm_unit').value,
      fabric: document.getElementById('itm_fabric').value.trim(),
      color: document.getElementById('itm_color').value.trim(),
      size: document.getElementById('itm_size').value.trim(),
      unit_cost: parseFloat(document.getElementById('itm_cost').value) || 0,
      current_stock: parseFloat(document.getElementById('itm_stock').value) || 0,
      min_stock: parseFloat(document.getElementById('itm_min_stock').value) || 0,
      hsn_code: document.getElementById('itm_hsn').value.trim(),
      location: document.getElementById('itm_location').value.trim()
    };

    if (!payload.name) {
      UI.showToast('Validation Error', 'Item Name is required', 'error');
      return;
    }

    saveBtn.disabled = true;
    saveBtn.textContent = isEdit ? 'Updating...' : 'Saving...';

    const url = isEdit ? ('/masters/items/' + itmId) : "{{ route('masters.items.store') }}";
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
      saveBtn.textContent = isEdit ? 'Update Item SKU' : 'Save Item SKU';

      if (data.success && data.item) {
        closeItemModal();
        UI.showToast(isEdit ? 'Item Updated' : 'Item Created', data.message, 'success');

        if (isEdit) {
          updateTableRow(data.item);
        } else {
          prependTableRow(data.item);
        }

        if (data.stats) updateKpiStats(data.stats);
      } else {
        const msg = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message || 'Validation error');
        UI.showToast('Error', msg, 'error');
      }
    })
    .catch(err => {
      saveBtn.disabled = false;
      saveBtn.textContent = isEdit ? 'Update Item SKU' : 'Save Item SKU';
      UI.showToast('Error', 'Failed to save item details', 'error');
    });
  }

  // --- 3. DELETE USING AJAX ---
  function deleteItemAjax() {
    const id = document.getElementById('delete-itm-id').value;
    if (!id) return;

    const delBtn = document.getElementById('confirm-delete-btn');
    delBtn.disabled = true;
    delBtn.textContent = 'Deleting...';

    fetch('/masters/items/' + id, {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': CSRF_TOKEN
      }
    })
    .then(res => res.json())
    .then(data => {
      delBtn.disabled = false;
      delBtn.textContent = 'Yes, Delete Item';
      closeDeleteModal();

      if (data.success) {
        UI.showToast('Item Deleted', data.message || 'Item removed successfully', 'warning');
        
        const row = document.getElementById('item-row-' + id);
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
        UI.showToast('Error', data.message || 'Could not delete item', 'error');
      }
    })
    .catch(err => {
      delBtn.disabled = false;
      delBtn.textContent = 'Yes, Delete Item';
      UI.showToast('Error', 'Failed to delete item', 'error');
    });
  }

  // --- Dynamic Table DOM Manipulation ---
  function prependTableRow(itm) {
    const tbody = document.getElementById('items-table-body');
    const tableContainer = document.getElementById('items-table-container');
    const emptyState = document.getElementById('items-empty-state');

    if (emptyState) emptyState.style.display = 'none';
    if (tableContainer) tableContainer.style.display = '';

    const tr = document.createElement('tr');
    tr.className = 'itm-row itm-row-transition';
    tr.id = 'item-row-' + itm.id;
    tr.setAttribute('data-id', itm.id);
    tr.setAttribute('data-category', (itm.category || '').toLowerCase());
    const isLow = (parseFloat(itm.current_stock || 0) <= parseFloat(itm.min_stock || 0));
    tr.setAttribute('data-lowstock', isLow ? '1' : '0');
    tr.setAttribute('data-status', (itm.status || 'active').toLowerCase());

    const initials = (itm.name || 'IT').substring(0, 2).toUpperCase();
    const badgeClass = getItemCategoryBadgeClass(itm.category);
    const statusVal = (itm.status || 'Active');
    const statusLower = statusVal.toLowerCase();

    const specText = [itm.fabric, itm.color, itm.size].filter(Boolean).join(' • ');

    tr.innerHTML = `
      <td class="row-index" style="text-align: center; font-weight: 600; color: var(--slate-400);">1</td>
      <td>
        <div style="display:flex; align-items:center; gap:10px;">
          <div class="itm-avatar" id="avatar-${itm.id}">${initials}</div>
          <div>
            <div class="itm-name-val" style="font-weight:700; color:var(--slate-900); font-size:0.9rem;">${itm.name}</div>
            <div style="display:flex; align-items:center; gap:6px;">
              <span class="itm-code-val" style="font-size:0.75rem; color:var(--slate-500); font-family:monospace;">${itm.code || ('ITM-' + String(itm.id).padStart(3, '0'))}</span>
              ${specText ? `<span class="itm-spec-val" style="font-size:0.75rem; color:var(--slate-400);">• ${specText}</span>` : ''}
            </div>
          </div>
        </div>
      </td>
      <td class="itm-cat-cell">
        <span class="badge ${badgeClass}" style="font-size:0.75rem; font-weight:600; padding:4px 8px; border-radius:8px;">
          ${itm.category || 'Fabric'}
        </span>
      </td>
      <td class="itm-unit-val" style="font-weight:600; color:var(--slate-700);">
        ${itm.unit || 'Meters'}
      </td>
      <td class="itm-cost-val" style="text-align: right; font-weight: 700; color: var(--slate-800);">
        ₹${Number(itm.unit_cost || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
      </td>
      <td class="itm-stock-val" style="text-align: right; font-weight: 800; color: ${isLow ? '#dc2626' : '#059669'};">
        ${Number(itm.current_stock || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
      </td>
      <td class="itm-minstock-val" style="text-align: right; font-size:0.8rem; color:var(--slate-500); font-weight:600;">
        ${Number(itm.min_stock || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
      </td>
      <td class="itm-stock-status-cell" style="text-align: center;">
        ${isLow ? '<span class="badge badge-danger" style="font-size:0.7rem; font-weight:700;">Low Stock</span>' : '<span class="badge badge-success" style="font-size:0.7rem; font-weight:700;">In Stock</span>'}
      </td>
      <td>
        <div class="itm-hsn-val" style="font-family:monospace; font-size:0.75rem; font-weight:600; color:var(--slate-700);">${itm.hsn_code || '—'}</div>
        <div class="itm-location-val" style="font-size:0.75rem; color:var(--slate-400);">${itm.location || '—'}</div>
      </td>
      <td style="text-align: center;">
        <select class="itm-status-select ${statusLower}" onchange="changeItemStatus(${itm.id}, this.value, this)" title="Click to change status">
          <option value="Active" ${statusLower === 'active' ? 'selected' : ''}>Active</option>
          <option value="Inactive" ${statusLower === 'inactive' ? 'selected' : ''}>Inactive</option>
          <option value="Blocked" ${statusLower === 'blocked' ? 'selected' : ''}>Blocked</option>
        </select>
      </td>
      <td style="text-align: center;">
        <div style="display:inline-flex; align-items:center; gap:6px;">
          <button type="button" class="btn btn-secondary btn-icon edit-btn-${itm.id}" title="Edit Item SKU" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
              <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
          </button>
          <button type="button" class="btn btn-danger btn-icon" onclick="confirmDeleteItem(${itm.id}, '${(itm.name || '').replace(/'/g, "\\'")}')" title="Delete Item SKU" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="3 6 5 6 21 6"/>
              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
            </svg>
          </button>
        </div>
      </td>
    `;

    const editBtn = tr.querySelector(`.edit-btn-${itm.id}`);
    if (editBtn) {
      editBtn.onclick = () => editItem(itm);
    }

    tbody.insertBefore(tr, tbody.firstChild);
    reindexRows();
  }

  function updateTableRow(itm) {
    const row = document.getElementById('item-row-' + itm.id);
    if (!row) return;

    row.querySelector('.itm-name-val').textContent = itm.name;
    row.querySelector('.itm-code-val').textContent = itm.code || ('ITM-' + String(itm.id).padStart(3, '0'));
    
    const specText = [itm.fabric, itm.color, itm.size].filter(Boolean).join(' • ');
    const specEl = row.querySelector('.itm-spec-val');
    if (specEl) {
      specEl.textContent = specText ? ('• ' + specText) : '';
    }

    const initials = (itm.name || 'IT').substring(0, 2).toUpperCase();
    const avatar = document.getElementById('avatar-' + itm.id);
    if (avatar) avatar.textContent = initials;

    const catCell = row.querySelector('.itm-cat-cell');
    if (catCell) {
      const badgeClass = getItemCategoryBadgeClass(itm.category);
      catCell.innerHTML = `<span class="badge ${badgeClass}" style="font-size:0.75rem; font-weight:600; padding:4px 8px; border-radius:8px;">${itm.category || 'Fabric'}</span>`;
    }

    const unitCell = row.querySelector('.itm-unit-val');
    if (unitCell) unitCell.textContent = itm.unit || 'Meters';

    const costCell = row.querySelector('.itm-cost-val');
    if (costCell) {
      costCell.textContent = '₹' + Number(itm.unit_cost || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    const isLow = (parseFloat(itm.current_stock || 0) <= parseFloat(itm.min_stock || 0));
    row.setAttribute('data-lowstock', isLow ? '1' : '0');

    const stockCell = row.querySelector('.itm-stock-val');
    if (stockCell) {
      stockCell.textContent = Number(itm.current_stock || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
      stockCell.style.color = isLow ? '#dc2626' : '#059669';
    }

    const minCell = row.querySelector('.itm-minstock-val');
    if (minCell) {
      minCell.textContent = Number(itm.min_stock || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    const statusBadgeCell = row.querySelector('.itm-stock-status-cell');
    if (statusBadgeCell) {
      statusBadgeCell.innerHTML = isLow 
        ? '<span class="badge badge-danger" style="font-size:0.7rem; font-weight:700;">Low Stock</span>' 
        : '<span class="badge badge-success" style="font-size:0.7rem; font-weight:700;">In Stock</span>';
    }

    const hsnCell = row.querySelector('.itm-hsn-val');
    if (hsnCell) hsnCell.textContent = itm.hsn_code || '—';

    const locCell = row.querySelector('.itm-location-val');
    if (locCell) locCell.textContent = itm.location || '—';

    const editBtn = row.querySelector('.btn-secondary');
    if (editBtn) {
      editBtn.onclick = () => editItem(itm);
    }
  }

  function reindexRows() {
    const rows = document.querySelectorAll('#items-table-body tr');
    rows.forEach((row, i) => {
      const idxCell = row.querySelector('.row-index');
      if (idxCell) idxCell.textContent = i + 1;
    });
  }

  function checkEmptyState() {
    const rows = document.querySelectorAll('#items-table-body tr');
    const emptyState = document.getElementById('items-empty-state');
    const tableContainer = document.getElementById('items-table-container');

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
    if (document.getElementById('item-badge-count')) document.getElementById('item-badge-count').textContent = stats.total + ' Records';

    if (document.getElementById('stat-fabric-count') && stats.fabricCount !== undefined) {
      document.getElementById('stat-fabric-count').textContent = stats.fabricCount;
    }

    if (document.getElementById('stat-trim-count') && stats.trimsCount !== undefined) {
      document.getElementById('stat-trim-count').textContent = stats.trimsCount;
    }

    if (document.getElementById('stat-low-stock') && stats.lowStockCount !== undefined) {
      const cnt = Number(stats.lowStockCount);
      document.getElementById('stat-low-stock').textContent = cnt + ' Low Stock';
      document.getElementById('stat-low-stock').style.color = cnt > 0 ? '#dc2626' : '#059669';
    }
  }

  // --- Live Table Filter & Pills ---
  function filterItemTable(query) {
    query = (query || '').toLowerCase().trim();
    const rows = document.querySelectorAll('#items-table-body tr');
    rows.forEach(row => {
      const text = row.innerText.toLowerCase();
      row.style.display = text.includes(query) ? '' : 'none';
    });
  }

  function applyItemFilter(type, btn) {
    document.querySelectorAll('.itm-filter-pill').forEach(el => el.classList.remove('active'));
    if (btn) btn.classList.add('active');

    const rows = document.querySelectorAll('#items-table-body tr.itm-row');
    rows.forEach(row => {
      const status = row.getAttribute('data-status');
      const category = (row.getAttribute('data-category') || '').toLowerCase();
      const isLow = (row.getAttribute('data-lowstock') === '1');

      if (type === 'all') {
        row.style.display = '';
      } else if (type === 'active') {
        row.style.display = (status === 'active') ? '' : 'none';
      } else if (type === 'fabric') {
        row.style.display = (category.includes('fabric')) ? '' : 'none';
      } else if (type === 'yarn') {
        row.style.display = (category.includes('yarn') || category.includes('thread')) ? '' : 'none';
      } else if (type === 'trims') {
        row.style.display = (category.includes('trim') || category.includes('packaging')) ? '' : 'none';
      } else if (type === 'lowstock') {
        row.style.display = isLow ? '' : 'none';
      }
    });
  }

  // Close modals on clicking backdrop
  window.addEventListener('click', function(e) {
    const itmModal = document.getElementById('item-modal');
    const delModal = document.getElementById('delete-item-modal');
    if (e.target === itmModal) closeItemModal();
    if (e.target === delModal) closeDeleteModal();
  });
</script>
@endpush
