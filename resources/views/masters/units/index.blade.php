@extends('layouts.app')

@section('title', 'Unit Master - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Masters Management</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Unit Master</span></div>
@endsection

@push('styles')
<style>
  /* Unit Master Modern UI Styles */
  .uom-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 16px;
  }

  .uom-stat-card {
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

  .uom-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.08);
    border-color: var(--slate-300);
  }

  .uom-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--stat-accent, var(--primary-500));
  }

  .uom-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .uom-stat-info {
    flex: 1;
    min-width: 0;
  }

  .uom-stat-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--slate-500);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 4px;
  }

  .uom-stat-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--slate-900);
    line-height: 1.2;
    display: flex;
    align-items: baseline;
    gap: 8px;
    flex-wrap: wrap;
  }

  .uom-main-card {
    background: #ffffff;
    border-radius: 18px;
    border: 1px solid var(--slate-200);
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    overflow: hidden;
  }

  .uom-card-header {
    padding: 22px 24px;
    border-bottom: 1px solid var(--slate-100);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    background: #ffffff;
  }

  .uom-toolbar {
    padding: 16px 24px;
    background: #f8fafc;
    border-bottom: 1px solid var(--slate-200);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    flex-wrap: wrap;
  }

  .uom-search-wrap {
    position: relative;
    flex: 1;
    min-width: 260px;
    max-width: 400px;
  }

  .uom-search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--slate-400);
    pointer-events: none;
  }

  .uom-search-input {
    width: 100%;
    padding: 9px 14px 9px 38px;
    border: 1px solid var(--slate-300);
    border-radius: 10px;
    font-size: 0.85rem;
    background: #ffffff;
    color: var(--slate-800);
    transition: all 0.2s ease;
  }

  .uom-search-input:focus {
    outline: none;
    border-color: var(--primary-500);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
  }

  .uom-filter-pills {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
  }

  .uom-filter-pill {
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

  .uom-filter-pill:hover {
    border-color: var(--slate-400);
    color: var(--slate-900);
  }

  .uom-filter-pill.active {
    background: var(--primary-600);
    color: #ffffff;
    border-color: var(--primary-600);
    box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
  }

  /* Empty State Modern Box */
  .uom-empty-box {
    padding: 60px 24px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
  }

  .uom-empty-icon-ring {
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

  .uom-empty-features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    max-width: 780px;
    width: 100%;
    margin-top: 32px;
    text-align: left;
  }

  .uom-feature-card {
    background: #ffffff;
    border: 1px solid var(--slate-200);
    border-radius: 12px;
    padding: 16px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
  }

  .uom-avatar {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, #6366f1, #4f46e5);
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
  .uom-status-select {
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

  .uom-status-select:focus {
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
  }

  .uom-status-select.active {
    background-color: #ecfdf5;
    color: #059669;
    border-color: #a7f3d0;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23059669' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  }

  .uom-status-select.inactive {
    background-color: #f1f5f9;
    color: #64748b;
    border-color: #cbd5e1;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  }

  /* Modal Styling */
  #unit-modal, #delete-unit-modal {
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

  .uom-modal-box {
    background: #ffffff;
    border-radius: 20px;
    width: 100%;
    max-width: 620px;
    padding: 26px;
    max-height: 92vh;
    overflow-y: auto;
    box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.3);
    border: 1px solid var(--slate-200);
    position: relative;
    animation: uomModalFade 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  }

  @keyframes uomModalFade {
    from { opacity: 0; transform: scale(0.96) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
  }

  .uom-modal-section-title {
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

  .uom-modal-section-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--slate-200);
  }

  .uom-row-transition {
    transition: all 0.3s ease;
  }
</style>
@endpush

@section('content')
<div style="display:flex; flex-direction:column; gap:22px;">

  <!-- Top KPI Dynamic Metrics Row -->
  <div class="uom-stats-grid">
    
    <!-- 1. Total Units Defined -->
    <div class="uom-stat-card" style="--stat-accent: #2563eb;">
      <div class="uom-stat-icon" style="background:#eff6ff; color:#2563eb;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/>
        </svg>
      </div>
      <div class="uom-stat-info">
        <div class="uom-stat-label">Total Units Defined</div>
        <div class="uom-stat-value">
          <span id="stat-total-count">{{ $stats['total'] }}</span>
          <span id="stat-active-badge" style="font-size:0.75rem; font-weight:700; color:#059669; background:#ecfdf5; padding:2px 8px; border-radius:12px; border:1px solid #a7f3d0;">
            <span id="stat-active-count">{{ $stats['active'] }}</span> Active
          </span>
        </div>
      </div>
    </div>

    <!-- 2. Base Primary Units -->
    <div class="uom-stat-card" style="--stat-accent: #059669;">
      <div class="uom-stat-icon" style="background:#ecfdf5; color:#059669;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/>
        </svg>
      </div>
      <div class="uom-stat-info">
        <div class="uom-stat-label">Base Primary Units</div>
        <div class="uom-stat-value">
          <span id="stat-base-count">{{ $stats['baseUnits'] }}</span>
          <span style="font-size:0.75rem; font-weight:600; color:var(--slate-500);">Independent</span>
        </div>
      </div>
    </div>

    <!-- 3. Derived Sub-Units -->
    <div class="uom-stat-card" style="--stat-accent: #8b5cf6;">
      <div class="uom-stat-icon" style="background:#faf5ff; color:#7c3aed;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M16 3h5v5"/><path d="M8 21H3v-5"/><path d="M21 3l-7 7"/><path d="M3 21l7-7"/>
        </svg>
      </div>
      <div class="uom-stat-info">
        <div class="uom-stat-label">Derived Sub-Units</div>
        <div class="uom-stat-value">
          <span id="stat-derived-count">{{ $stats['derivedUnits'] }}</span>
          <span style="font-size:0.75rem; font-weight:600; color:var(--slate-500);">Converted</span>
        </div>
      </div>
    </div>

    <!-- 4. Conversion Standards Health -->
    <div class="uom-stat-card" style="--stat-accent: #0284c7;">
      <div class="uom-stat-icon" style="background:#e0f2fe; color:#0284c7;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
      </div>
      <div class="uom-stat-info">
        <div class="uom-stat-label">UOM Compliance</div>
        <div class="uom-stat-value">
          <span id="stat-health-percent">{{ $stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100) : 100 }}%</span>
          <span style="font-size:0.75rem; font-weight:600; color:var(--slate-500);">Standardized</span>
        </div>
      </div>
    </div>

  </div>

  <!-- Main Card Container -->
  <div class="uom-main-card">
    
    <!-- Action Header -->
    <div class="uom-card-header">
      <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:40px; height:40px; border-radius:10px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/>
          </svg>
        </div>
        <div>
          <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900); display:flex; align-items:center; gap:8px;">
            Unit of Measurement (UOM) Master
            <span class="badge badge-primary" id="unit-badge-count" style="font-size:0.75rem; padding:2px 8px; border-radius:12px;">{{ count($units) }} Records</span>
          </h3>
          <p style="margin:3px 0 0; font-size:0.8rem; color:var(--slate-500);">Meters, Rolls, Kilograms, Pieces, Cones, Dozen & Gross conversion factors</p>
        </div>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('units-table', 'Unit_Master.csv')" style="display:inline-flex; align-items:center; gap:6px; font-weight:600;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
          </svg>
          Export CSV
        </button>

        <button class="btn btn-primary btn-sm" onclick="openUnitModal()" style="display:inline-flex; align-items:center; gap:6px; font-weight:700; background:linear-gradient(135deg, #2563eb, #1d4ed8); box-shadow:0 4px 10px rgba(37,99,235,0.25); cursor:pointer;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
          </svg>
          Add Unit
        </button>
      </div>
    </div>

    <!-- Quick Filter Bar -->
    <div class="uom-toolbar">
      <div class="uom-search-wrap">
        <svg class="uom-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"/>
          <line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" id="unit-filter-input" class="uom-search-input" placeholder="Search unit name, code, symbol, description..." onkeyup="filterUnitTable(this.value)">
      </div>

      <div class="uom-filter-pills">
        <button type="button" class="uom-filter-pill active" id="pill-all" onclick="applyUnitFilter('all', this)">All (<span id="pill-count-all">{{ $stats['total'] }}</span>)</button>
        <button type="button" class="uom-filter-pill" id="pill-active" onclick="applyUnitFilter('active', this)">Active (<span id="pill-count-active">{{ $stats['active'] }}</span>)</button>
        <button type="button" class="uom-filter-pill" onclick="applyUnitFilter('base', this)">Base Primary Units</button>
        <button type="button" class="uom-filter-pill" onclick="applyUnitFilter('derived', this)">Derived Sub-Units</button>
        <button type="button" class="uom-filter-pill" onclick="applyUnitFilter('inactive', this)">Inactive</button>
      </div>
    </div>

    <!-- Empty State Box (Toggled if 0 records) -->
    <div id="units-empty-state" class="uom-empty-box" style="{{ $units->isEmpty() ? 'display:flex;' : 'display:none;' }}">
      <div class="uom-empty-icon-ring">
        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/>
        </svg>
      </div>

      <h3 style="margin:0 0 8px; font-size:1.25rem; font-weight:800; color:var(--slate-900);">No Measurement Units Configured Yet</h3>
      <p style="margin:0 0 20px; font-size:0.875rem; color:var(--slate-500); max-width:480px; line-height:1.5;">
        Define base measurement units (Meters, Kilograms, Pieces) and derived sub-units (Rolls, Cones, Dozen) with conversion multipliers.
      </p>

      <button type="button" class="btn btn-primary" onclick="openUnitModal()" style="display:inline-flex; align-items:center; gap:8px; padding:10px 22px; font-size:0.9rem; font-weight:700; border-radius:10px; background:linear-gradient(135deg, #2563eb, #1d4ed8); box-shadow:0 6px 16px rgba(37,99,235,0.25); cursor:pointer;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"/>
          <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Add Your First Unit
      </button>

      <!-- Feature cards -->
      <div class="uom-empty-features">
        <div class="uom-feature-card">
          <div style="width:34px; height:34px; border-radius:8px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
          </div>
          <div>
            <div style="font-weight:700; font-size:0.825rem; color:var(--slate-800);">Base & Sub-Unit Hierarchy</div>
            <div style="font-size:0.75rem; color:var(--slate-500); margin-top:2px;">Parent base units with multi-level conversion factors</div>
          </div>
        </div>

        <div class="uom-feature-card">
          <div style="width:34px; height:34px; border-radius:8px; background:#faf5ff; color:#9333ea; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 3h5v5"/><path d="M8 21H3v-5"/><path d="M21 3l-7 7"/><path d="M3 21l7-7"/></svg>
          </div>
          <div>
            <div style="font-weight:700; font-size:0.825rem; color:var(--slate-800);">Automatic Inventory Math</div>
            <div style="font-size:0.75rem; color:var(--slate-500); margin-top:2px;">Purchasing in rolls & issuing in meters seamlessly</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Data Table Container -->
    <div class="table-responsive" id="units-table-container" style="{{ $units->isEmpty() ? 'display:none;' : '' }}">
      <table class="data-table" id="units-table">
        <thead>
          <tr>
            <th style="width: 50px; text-align: center;">#</th>
            <th>Unit Name & Code</th>
            <th style="text-align: center;">Symbol</th>
            <th>UOM Type</th>
            <th>Parent Base Unit</th>
            <th>Conversion Multiplier</th>
            <th style="text-align: center;">Decimals</th>
            <th style="text-align: center; width: 130px;">Status</th>
            <th style="text-align: center; width: 100px;">Actions</th>
          </tr>
        </thead>
        <tbody id="units-table-body">
          @foreach($units as $index => $u)
            @php
              $isDerived = !empty($u->parent_id);
              $conversionText = '1.0000 (Base)';
              if ($isDerived && $u->conversion_factor && $u->parent) {
                $conversionText = '1 ' . $u->code . ' = ' . $u->conversion_factor . ' ' . ($u->parent->symbol ?: $u->parent->code);
              }
            @endphp
            <tr class="uom-row uom-row-transition" id="unit-row-{{ $u->id }}" data-id="{{ $u->id }}" data-type="{{ $isDerived ? 'derived' : 'base' }}" data-status="{{ strtolower($u->status ?? 'active') }}">
              <td class="row-index" style="text-align: center; font-weight: 600; color: var(--slate-400);">{{ $index + 1 }}</td>
              <td>
                <div style="display:flex; align-items:center; gap:10px;">
                  <div class="uom-avatar" id="avatar-{{ $u->id }}">
                    {{ strtoupper(substr($u->name, 0, 2)) }}
                  </div>
                  <div>
                    <div class="unit-name-val" style="font-weight:700; color:var(--slate-900); font-size:0.9rem;">{{ $u->name }}</div>
                    <div class="unit-code-val" style="font-size:0.75rem; color:var(--slate-500); font-family:monospace;">{{ $u->code }}</div>
                  </div>
                </div>
              </td>
              <td class="unit-symbol-val" style="text-align: center; font-family:var(--font-mono); font-weight:700; color:var(--primary-700);">
                {{ $u->symbol ?: '—' }}
              </td>
              <td class="unit-type-cell">
                @if($isDerived)
                  <span class="badge badge-purple" style="font-size:0.75rem; font-weight:600; padding:4px 8px; border-radius:8px;">Derived Sub-Unit</span>
                @else
                  <span class="badge badge-primary" style="font-size:0.75rem; font-weight:600; padding:4px 8px; border-radius:8px;">Base Primary Unit</span>
                @endif
              </td>
              <td class="unit-parent-val" style="font-weight:600; color:var(--slate-700);">
                {{ $u->parent ? $u->parent->name : '— (Self Base)' }}
              </td>
              <td class="unit-factor-val" style="font-weight:600; color:var(--slate-800); font-family:var(--font-mono); font-size:0.85rem;">
                {{ $conversionText }}
              </td>
              <td class="unit-decimals-val" style="text-align: center; font-weight:600; color:var(--slate-600);">
                {{ $u->decimal_places ?? 2 }} Places
              </td>
              <td style="text-align: center;">
                <!-- Direct Table Status Changer -->
                <select class="uom-status-select {{ strtolower($u->status ?? 'active') }}" onchange="changeUnitStatus({{ $u->id }}, this.value, this)" title="Click to change status">
                  <option value="Active" {{ strtolower($u->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                  <option value="Inactive" {{ strtolower($u->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
              </td>
              <td style="text-align: center;">
                <div style="display:inline-flex; align-items:center; gap:6px;">
                  <button type="button" class="btn btn-secondary btn-icon edit-btn-{{ $u->id }}" onclick='editUnit(@json($u))' title="Edit Unit" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                  </button>
                  <button type="button" class="btn btn-danger btn-icon" onclick="confirmDeleteUnit({{ $u->id }}, '{{ addslashes($u->name) }}')" title="Delete Unit" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
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

<!-- Unit Modal (Add & Edit Form via AJAX) -->
<div id="unit-modal" style="display:none;">
  <div class="uom-modal-box">
    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--slate-100); padding-bottom:16px; margin-bottom:18px;">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:38px; height:38px; border-radius:10px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/>
          </svg>
        </div>
        <div>
          <h3 id="umodal-title" style="margin:0; font-size:1.1rem; font-weight:800; color:var(--slate-900);">Add New Unit</h3>
          <p style="margin:2px 0 0; font-size:0.75rem; color:var(--slate-500);">Configure standard measurement unit & conversion multiplier</p>
        </div>
      </div>
      <button type="button" onclick="closeUnitModal()" style="background:none; border:none; color:var(--slate-400); cursor:pointer; font-size:1.4rem; line-height:1; padding:4px;">
        &times;
      </button>
    </div>

    <form id="unit-form" onsubmit="saveUnitAjax(event)">
      @csrf
      <input type="hidden" name="id" id="unit-id" value="">

      <!-- Basic Identification -->
      <div class="uom-modal-section-title">Unit Identification</div>
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:14px; margin-bottom:12px;">
        <div class="form-group" style="margin:0;">
          <label class="form-label required">Unit Full Name</label>
          <input type="text" name="name" id="unit_name" class="form-control" required placeholder="e.g. Meters, Kilograms, Rolls, Pieces">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Unit Code / Abbreviation</label>
          <input type="text" name="code" id="unit_code" class="form-control" placeholder="e.g. MTR, KG, ROLL, PCS" style="text-transform:uppercase;">
        </div>
      </div>

      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:14px; margin-bottom:12px;">
        <div class="form-group" style="margin:0;">
          <label class="form-label">Display Symbol</label>
          <input type="text" name="symbol" id="unit_symbol" class="form-control" placeholder="e.g. m, kg, rl, pcs">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Decimal Places Precision</label>
          <input type="number" name="decimal_places" id="unit_decimals" class="form-control" value="2" min="0" max="4">
        </div>
      </div>

      <!-- Hierarchy & Conversion Logic (Exact logic preserved) -->
      <div class="uom-modal-section-title">Conversion & Base Unit Hierarchy</div>
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:14px; margin-bottom:12px;">
        <div class="form-group" style="margin:0;">
          <label class="form-label">Parent Base Unit</label>
          <select name="parent_id" id="unit_parent" class="form-control" onchange="toggleConversionFactorInput(this.value)">
            <option value="">None (This is an Independent Base Unit)</option>
            @foreach($parentUnits as $pu)
              <option value="{{ $pu->id }}">{{ $pu->name }} ({{ $pu->code }})</option>
            @endforeach
          </select>
        </div>
        <div class="form-group" id="factor-group" style="margin:0;">
          <label class="form-label">Conversion Multiplier</label>
          <input type="number" step="0.0001" name="conversion_factor" id="unit_factor" class="form-control" placeholder="e.g. 1 Roll = 100 Meters">
          <small style="font-size:0.7rem; color:var(--slate-500); display:block; margin-top:3px;">How many parent units equal 1 of this unit</small>
        </div>
      </div>

      <div class="form-group" style="margin-bottom:20px;">
        <label class="form-label">Description / Usage Notes</label>
        <textarea name="description" id="unit_desc" class="form-control" rows="2" placeholder="Fabric rolls, packaging cartons, stitch thread cones..."></textarea>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid var(--slate-100); padding-top:16px;">
        <button type="button" class="btn btn-secondary" onclick="closeUnitModal()">Cancel</button>
        <button type="submit" id="save-uom-btn" class="btn btn-primary" style="background:linear-gradient(135deg, #2563eb, #1d4ed8); font-weight:700;">Save Unit</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Confirmation Modal (via AJAX) -->
<div id="delete-unit-modal" style="display:none;">
  <div class="uom-modal-box" style="max-width:440px; text-align:center;">
    <div style="width:52px; height:52px; border-radius:50%; background:#fef2f2; color:#dc2626; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
      <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
    </div>
    <h3 style="margin:0 0 8px; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Delete Unit</h3>
    <p style="margin:0 0 20px; font-size:0.875rem; color:var(--slate-500); line-height:1.4;">
      Are you sure you want to delete <strong id="delete-unit-name" style="color:var(--slate-800);">this unit</strong>? Any attached sub-units will become standalone base units.
    </p>

    <input type="hidden" id="delete-unit-id" value="">
    <div style="display:flex; justify-content:center; gap:10px;">
      <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
      <button type="button" id="confirm-delete-btn" class="btn btn-danger" onclick="deleteUnitAjax()" style="font-weight:700;">Yes, Delete Unit</button>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  const CSRF_TOKEN = '{{ csrf_token() }}';

  function toggleConversionFactorInput(parentId) {
    const factorInput = document.getElementById('unit_factor');
    if (!parentId) {
      if (factorInput) factorInput.value = '';
    }
  }

  // --- Modal Open / Close ---
  function openUnitModal() {
    const modal = document.getElementById('unit-modal');
    if (!modal) return;
    
    document.getElementById('unit-form').reset();
    document.getElementById('unit-id').value = '';
    document.getElementById('umodal-title').textContent = 'Add New Unit';
    document.getElementById('save-uom-btn').textContent = 'Save Unit';
    document.getElementById('save-uom-btn').disabled = false;
    document.getElementById('unit_decimals').value = '2';
    
    modal.style.display = 'flex';
  }

  function closeUnitModal() {
    const modal = document.getElementById('unit-modal');
    if (modal) modal.style.display = 'none';
  }

  function editUnit(u) {
    const modal = document.getElementById('unit-modal');
    if (!modal) return;

    document.getElementById('umodal-title').textContent = 'Edit Unit: ' + (u.code || u.name);
    document.getElementById('save-uom-btn').textContent = 'Update Unit';
    document.getElementById('save-uom-btn').disabled = false;
    document.getElementById('unit-id').value = u.id;

    document.getElementById('unit_name').value = u.name || '';
    document.getElementById('unit_code').value = u.code || '';
    document.getElementById('unit_symbol').value = u.symbol || '';
    document.getElementById('unit_decimals').value = u.decimal_places ?? 2;
    document.getElementById('unit_parent').value = u.parent_id || '';
    document.getElementById('unit_factor').value = u.conversion_factor || '';
    document.getElementById('unit_desc').value = u.description || '';

    modal.style.display = 'flex';
  }

  function confirmDeleteUnit(id, name) {
    const modal = document.getElementById('delete-unit-modal');
    if (!modal) return;
    
    document.getElementById('delete-unit-name').textContent = name;
    document.getElementById('delete-unit-id').value = id;
    document.getElementById('confirm-delete-btn').textContent = 'Yes, Delete Unit';
    document.getElementById('confirm-delete-btn').disabled = false;
    modal.style.display = 'flex';
  }

  function closeDeleteModal() {
    const modal = document.getElementById('delete-unit-modal');
    if (modal) modal.style.display = 'none';
  }

  // --- 1. STATUS CHANGE THROUGH TABLE VIA AJAX ---
  function changeUnitStatus(id, newStatus, selectEl) {
    const prevClass = selectEl.className;
    selectEl.style.opacity = '0.5';

    fetch('/masters/units/' + id, {
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
        selectEl.className = 'uom-status-select ' + newStatus.toLowerCase();
        
        const row = document.getElementById('unit-row-' + id);
        if (row) row.setAttribute('data-status', newStatus.toLowerCase());

        if (data.stats) updateKpiStats(data.stats);

        UI.showToast('Status Updated', 'Unit status set to ' + newStatus, 'success');
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
  function saveUnitAjax(e) {
    e.preventDefault();

    const saveBtn = document.getElementById('save-uom-btn');
    const uId = document.getElementById('unit-id').value;
    const isEdit = Boolean(uId);

    const payload = {
      name: document.getElementById('unit_name').value.trim(),
      code: document.getElementById('unit_code').value.trim(),
      symbol: document.getElementById('unit_symbol').value.trim(),
      decimal_places: parseInt(document.getElementById('unit_decimals').value) || 2,
      parent_id: document.getElementById('unit_parent').value || null,
      conversion_factor: parseFloat(document.getElementById('unit_factor').value) || null,
      description: document.getElementById('unit_desc').value.trim()
    };

    if (!payload.name) {
      UI.showToast('Validation Error', 'Unit Name is required', 'error');
      return;
    }

    saveBtn.disabled = true;
    saveBtn.textContent = isEdit ? 'Updating...' : 'Saving...';

    const url = isEdit ? ('/masters/units/' + uId) : "{{ route('masters.units.store') }}";
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
      saveBtn.textContent = isEdit ? 'Update Unit' : 'Save Unit';

      if (data.success && data.unit) {
        closeUnitModal();
        UI.showToast(isEdit ? 'Unit Updated' : 'Unit Created', data.message, 'success');

        if (isEdit) {
          updateTableRow(data.unit);
        } else {
          prependTableRow(data.unit);
        }

        if (data.stats) updateKpiStats(data.stats);
        if (data.parentUnits) updateParentDropdown(data.parentUnits);
      } else {
        const msg = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message || 'Validation error');
        UI.showToast('Error', msg, 'error');
      }
    })
    .catch(err => {
      saveBtn.disabled = false;
      saveBtn.textContent = isEdit ? 'Update Unit' : 'Save Unit';
      UI.showToast('Error', 'Failed to save unit details', 'error');
    });
  }

  // --- 3. DELETE USING AJAX ---
  function deleteUnitAjax() {
    const id = document.getElementById('delete-unit-id').value;
    if (!id) return;

    const delBtn = document.getElementById('confirm-delete-btn');
    delBtn.disabled = true;
    delBtn.textContent = 'Deleting...';

    fetch('/masters/units/' + id, {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': CSRF_TOKEN
      }
    })
    .then(res => res.json())
    .then(data => {
      delBtn.disabled = false;
      delBtn.textContent = 'Yes, Delete Unit';
      closeDeleteModal();

      if (data.success) {
        UI.showToast('Unit Deleted', data.message || 'Unit removed successfully', 'warning');
        
        const row = document.getElementById('unit-row-' + id);
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
        if (data.parentUnits) updateParentDropdown(data.parentUnits);
      } else {
        UI.showToast('Error', data.message || 'Could not delete unit', 'error');
      }
    })
    .catch(err => {
      delBtn.disabled = false;
      delBtn.textContent = 'Yes, Delete Unit';
      UI.showToast('Error', 'Failed to delete unit', 'error');
    });
  }

  function updateParentDropdown(parentUnits) {
    const select = document.getElementById('unit_parent');
    if (!select) return;
    const currentVal = select.value;

    select.innerHTML = '<option value="">None (This is an Independent Base Unit)</option>';
    parentUnits.forEach(pu => {
      const opt = document.createElement('option');
      opt.value = pu.id;
      opt.textContent = `${pu.name} (${pu.code})`;
      if (String(pu.id) === String(currentVal)) opt.selected = true;
      select.appendChild(opt);
    });
  }

  // --- Dynamic Table DOM Manipulation ---
  function prependTableRow(u) {
    const tbody = document.getElementById('units-table-body');
    const tableContainer = document.getElementById('units-table-container');
    const emptyState = document.getElementById('units-empty-state');

    if (emptyState) emptyState.style.display = 'none';
    if (tableContainer) tableContainer.style.display = '';

    const tr = document.createElement('tr');
    tr.className = 'uom-row uom-row-transition';
    tr.id = 'unit-row-' + u.id;
    tr.setAttribute('data-id', u.id);
    const isDerived = Boolean(u.parent_id);
    tr.setAttribute('data-type', isDerived ? 'derived' : 'base');
    tr.setAttribute('data-status', (u.status || 'active').toLowerCase());

    const initials = (u.name || 'UN').substring(0, 2).toUpperCase();
    const statusVal = (u.status || 'Active');
    const statusLower = statusVal.toLowerCase();

    let conversionText = '1.0000 (Base)';
    if (isDerived && u.conversion_factor && u.parent) {
      conversionText = '1 ' + u.code + ' = ' + u.conversion_factor + ' ' + (u.parent.symbol || u.parent.code);
    }

    tr.innerHTML = `
      <td class="row-index" style="text-align: center; font-weight: 600; color: var(--slate-400);">1</td>
      <td>
        <div style="display:flex; align-items:center; gap:10px;">
          <div class="uom-avatar" id="avatar-${u.id}">${initials}</div>
          <div>
            <div class="unit-name-val" style="font-weight:700; color:var(--slate-900); font-size:0.9rem;">${u.name}</div>
            <div class="unit-code-val" style="font-size:0.75rem; color:var(--slate-500); font-family:monospace;">${u.code}</div>
          </div>
        </div>
      </td>
      <td class="unit-symbol-val" style="text-align: center; font-family:var(--font-mono); font-weight:700; color:var(--primary-700);">
        ${u.symbol || '—'}
      </td>
      <td class="unit-type-cell">
        ${isDerived ? '<span class="badge badge-purple" style="font-size:0.75rem; font-weight:600; padding:4px 8px; border-radius:8px;">Derived Sub-Unit</span>' : '<span class="badge badge-primary" style="font-size:0.75rem; font-weight:600; padding:4px 8px; border-radius:8px;">Base Primary Unit</span>'}
      </td>
      <td class="unit-parent-val" style="font-weight:600; color:var(--slate-700);">
        ${u.parent ? u.parent.name : '— (Self Base)'}
      </td>
      <td class="unit-factor-val" style="font-weight:600; color:var(--slate-800); font-family:var(--font-mono); font-size:0.85rem;">
        ${conversionText}
      </td>
      <td class="unit-decimals-val" style="text-align: center; font-weight:600; color:var(--slate-600);">
        ${u.decimal_places ?? 2} Places
      </td>
      <td style="text-align: center;">
        <select class="uom-status-select ${statusLower}" onchange="changeUnitStatus(${u.id}, this.value, this)" title="Click to change status">
          <option value="Active" ${statusLower === 'active' ? 'selected' : ''}>Active</option>
          <option value="Inactive" ${statusLower === 'inactive' ? 'selected' : ''}>Inactive</option>
        </select>
      </td>
      <td style="text-align: center;">
        <div style="display:inline-flex; align-items:center; gap:6px;">
          <button type="button" class="btn btn-secondary btn-icon edit-btn-${u.id}" title="Edit Unit" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
              <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
          </button>
          <button type="button" class="btn btn-danger btn-icon" onclick="confirmDeleteUnit(${u.id}, '${(u.name || '').replace(/'/g, "\\'")}')" title="Delete Unit" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="3 6 5 6 21 6"/>
              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
            </svg>
          </button>
        </div>
      </td>
    `;

    const editBtn = tr.querySelector(`.edit-btn-${u.id}`);
    if (editBtn) {
      editBtn.onclick = () => editUnit(u);
    }

    tbody.insertBefore(tr, tbody.firstChild);
    reindexRows();
  }

  function updateTableRow(u) {
    const row = document.getElementById('unit-row-' + u.id);
    if (!row) return;

    row.querySelector('.unit-name-val').textContent = u.name;
    row.querySelector('.unit-code-val').textContent = u.code;
    row.querySelector('.unit-symbol-val').textContent = u.symbol || '—';
    
    const isDerived = Boolean(u.parent_id);
    row.setAttribute('data-type', isDerived ? 'derived' : 'base');

    const typeCell = row.querySelector('.unit-type-cell');
    if (typeCell) {
      typeCell.innerHTML = isDerived 
        ? '<span class="badge badge-purple" style="font-size:0.75rem; font-weight:600; padding:4px 8px; border-radius:8px;">Derived Sub-Unit</span>' 
        : '<span class="badge badge-primary" style="font-size:0.75rem; font-weight:600; padding:4px 8px; border-radius:8px;">Base Primary Unit</span>';
    }

    const parentCell = row.querySelector('.unit-parent-val');
    if (parentCell) {
      parentCell.textContent = u.parent ? u.parent.name : '— (Self Base)';
    }

    const factorCell = row.querySelector('.unit-factor-val');
    if (factorCell) {
      let conversionText = '1.0000 (Base)';
      if (isDerived && u.conversion_factor && u.parent) {
        conversionText = '1 ' + u.code + ' = ' + u.conversion_factor + ' ' + (u.parent.symbol || u.parent.code);
      }
      factorCell.textContent = conversionText;
    }

    const decimalsCell = row.querySelector('.unit-decimals-val');
    if (decimalsCell) {
      decimalsCell.textContent = (u.decimal_places ?? 2) + ' Places';
    }

    const initials = (u.name || 'UN').substring(0, 2).toUpperCase();
    const avatar = document.getElementById('avatar-' + u.id);
    if (avatar) avatar.textContent = initials;

    const editBtn = row.querySelector('.btn-secondary');
    if (editBtn) {
      editBtn.onclick = () => editUnit(u);
    }
  }

  function reindexRows() {
    const rows = document.querySelectorAll('#units-table-body tr');
    rows.forEach((row, i) => {
      const idxCell = row.querySelector('.row-index');
      if (idxCell) idxCell.textContent = i + 1;
    });
  }

  function checkEmptyState() {
    const rows = document.querySelectorAll('#units-table-body tr');
    const emptyState = document.getElementById('units-empty-state');
    const tableContainer = document.getElementById('units-table-container');

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
    if (document.getElementById('unit-badge-count')) document.getElementById('unit-badge-count').textContent = stats.total + ' Records';

    if (document.getElementById('stat-base-count') && stats.baseUnits !== undefined) {
      document.getElementById('stat-base-count').textContent = stats.baseUnits;
    }

    if (document.getElementById('stat-derived-count') && stats.derivedUnits !== undefined) {
      document.getElementById('stat-derived-count').textContent = stats.derivedUnits;
    }

    if (document.getElementById('stat-health-percent') && stats.total !== undefined) {
      const pct = stats.total > 0 ? Math.round((stats.active / stats.total) * 100) : 100;
      document.getElementById('stat-health-percent').textContent = pct + '%';
    }
  }

  // --- Live Table Filter & Pills ---
  function filterUnitTable(query) {
    query = (query || '').toLowerCase().trim();
    const rows = document.querySelectorAll('#units-table-body tr');
    rows.forEach(row => {
      const text = row.innerText.toLowerCase();
      row.style.display = text.includes(query) ? '' : 'none';
    });
  }

  function applyUnitFilter(type, btn) {
    document.querySelectorAll('.uom-filter-pill').forEach(el => el.classList.remove('active'));
    if (btn) btn.classList.add('active');

    const rows = document.querySelectorAll('#units-table-body tr.uom-row');
    rows.forEach(row => {
      const status = row.getAttribute('data-status');
      const uType = row.getAttribute('data-type');

      if (type === 'all') {
        row.style.display = '';
      } else if (type === 'active') {
        row.style.display = (status === 'active') ? '' : 'none';
      } else if (type === 'base') {
        row.style.display = (uType === 'base') ? '' : 'none';
      } else if (type === 'derived') {
        row.style.display = (uType === 'derived') ? '' : 'none';
      } else if (type === 'inactive') {
        row.style.display = (status === 'inactive') ? '' : 'none';
      }
    });
  }

  // Close modals on clicking backdrop
  window.addEventListener('click', function(e) {
    const uModal = document.getElementById('unit-modal');
    const delModal = document.getElementById('delete-unit-modal');
    if (e.target === uModal) closeUnitModal();
    if (e.target === delModal) closeDeleteModal();
  });
</script>
@endpush
