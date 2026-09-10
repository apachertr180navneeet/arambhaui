@extends('layouts.app')

@section('title', 'Job Worker Master - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Masters Management</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Job Worker Master</span></div>
@endsection

@push('styles')
<style>
  /* Job Worker Master Modern UI Styles */
  .jw-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 16px;
  }

  .jw-stat-card {
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

  .jw-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.08);
    border-color: var(--slate-300);
  }

  .jw-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--stat-accent, var(--primary-500));
  }

  .jw-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .jw-stat-info {
    flex: 1;
    min-width: 0;
  }

  .jw-stat-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--slate-500);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 4px;
  }

  .jw-stat-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--slate-900);
    line-height: 1.2;
    display: flex;
    align-items: baseline;
    gap: 8px;
    flex-wrap: wrap;
  }

  .jw-main-card {
    background: #ffffff;
    border-radius: 18px;
    border: 1px solid var(--slate-200);
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    overflow: hidden;
  }

  .jw-card-header {
    padding: 22px 24px;
    border-bottom: 1px solid var(--slate-100);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    background: #ffffff;
  }

  .jw-toolbar {
    padding: 16px 24px;
    background: #f8fafc;
    border-bottom: 1px solid var(--slate-200);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    flex-wrap: wrap;
  }

  .jw-search-wrap {
    position: relative;
    flex: 1;
    min-width: 260px;
    max-width: 400px;
  }

  .jw-search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--slate-400);
    pointer-events: none;
  }

  .jw-search-input {
    width: 100%;
    padding: 9px 14px 9px 38px;
    border: 1px solid var(--slate-300);
    border-radius: 10px;
    font-size: 0.85rem;
    background: #ffffff;
    color: var(--slate-800);
    transition: all 0.2s ease;
  }

  .jw-search-input:focus {
    outline: none;
    border-color: var(--primary-500);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
  }

  .jw-filter-pills {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
  }

  .jw-filter-pill {
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

  .jw-filter-pill:hover {
    border-color: var(--slate-400);
    color: var(--slate-900);
  }

  .jw-filter-pill.active {
    background: var(--primary-600);
    color: #ffffff;
    border-color: var(--primary-600);
    box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
  }

  /* Empty State Modern Box */
  .jw-empty-box {
    padding: 60px 24px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
  }

  .jw-empty-icon-ring {
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

  .jw-empty-features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    max-width: 780px;
    width: 100%;
    margin-top: 32px;
    text-align: left;
  }

  .jw-feature-card {
    background: #ffffff;
    border: 1px solid var(--slate-200);
    border-radius: 12px;
    padding: 16px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
  }

  .jw-avatar {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, #059669, #047857);
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
  .jw-status-select {
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

  .jw-status-select:focus {
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
  }

  .jw-status-select.active {
    background-color: #ecfdf5;
    color: #059669;
    border-color: #a7f3d0;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23059669' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  }

  .jw-status-select.blocked {
    background-color: #fef2f2;
    color: #dc2626;
    border-color: #fecaca;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23dc2626' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  }

  .jw-status-select.inactive {
    background-color: #f1f5f9;
    color: #64748b;
    border-color: #cbd5e1;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  }

  /* Modal Styling */
  #jobworker-modal, #delete-jobworker-modal {
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

  .jw-modal-box {
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
    animation: jwModalFade 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  }

  @keyframes jwModalFade {
    from { opacity: 0; transform: scale(0.96) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
  }

  .jw-modal-section-title {
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

  .jw-modal-section-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--slate-200);
  }

  .jw-row-transition {
    transition: all 0.3s ease;
  }
</style>
@endpush

@section('content')
<div style="display:flex; flex-direction:column; gap:22px;">

  <!-- Top KPI Dynamic Metrics Row -->
  <div class="jw-stats-grid">
    
    <!-- 1. Total Job Workers -->
    <div class="jw-stat-card" style="--stat-accent: #2563eb;">
      <div class="jw-stat-icon" style="background:#eff6ff; color:#2563eb;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
      </div>
      <div class="jw-stat-info">
        <div class="jw-stat-label">Registered Job Workers</div>
        <div class="jw-stat-value">
          <span id="stat-total-count">{{ $stats['total'] }}</span>
          <span id="stat-active-badge" style="font-size:0.75rem; font-weight:700; color:#059669; background:#ecfdf5; padding:2px 8px; border-radius:12px; border:1px solid #a7f3d0;">
            <span id="stat-active-count">{{ $stats['active'] }}</span> Active
          </span>
        </div>
      </div>
    </div>

    <!-- 2. Daily Production Capacity -->
    <div class="jw-stat-card" style="--stat-accent: #059669;">
      <div class="jw-stat-icon" style="background:#ecfdf5; color:#059669;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
      </div>
      <div class="jw-stat-info">
        <div class="jw-stat-label">Total Daily Capacity</div>
        <div class="jw-stat-value">
          <span id="stat-capacity-count">{{ number_format($stats['totalCapacity']) }}</span>
          <span style="font-size:0.75rem; font-weight:600; color:var(--slate-500);">Pcs / Day</span>
        </div>
      </div>
    </div>

    <!-- 3. Stitching & Sewing Units -->
    <div class="jw-stat-card" style="--stat-accent: #8b5cf6;">
      <div class="jw-stat-icon" style="background:#faf5ff; color:#7c3aed;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>
        </svg>
      </div>
      <div class="jw-stat-info">
        <div class="jw-stat-label">Stitching Units</div>
        <div class="jw-stat-value">
          <span id="stat-stitching-count">{{ $stats['stitchingCount'] }}</span>
          <span style="font-size:0.75rem; font-weight:600; color:var(--slate-500);">Sewing Lines</span>
        </div>
      </div>
    </div>

    <!-- 4. Total Outstanding Balances -->
    <div class="jw-stat-card" style="--stat-accent: #ef4444;">
      <div class="jw-stat-icon" style="background:#fef2f2; color:#dc2626;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="1" x2="12" y2="23"/>
          <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
        </svg>
      </div>
      <div class="jw-stat-info">
        <div class="jw-stat-label">Total Outstanding Dues</div>
        <div class="jw-stat-value" id="stat-total-outstanding" style="color:{{ $stats['totalOutstanding'] > 0 ? '#dc2626' : '#059669' }};">
          ₹{{ number_format($stats['totalOutstanding'], 2) }}
        </div>
      </div>
    </div>

  </div>

  <!-- Main Card Container -->
  <div class="jw-main-card">
    
    <!-- Action Header -->
    <div class="jw-card-header">
      <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:40px; height:40px; border-radius:10px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
          </svg>
        </div>
        <div>
          <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:var(--slate-900); display:flex; align-items:center; gap:8px;">
            Job Worker Directory
            <span class="badge badge-primary" id="jobworker-badge-count" style="font-size:0.75rem; padding:2px 8px; border-radius:12px;">{{ count($jobworkers) }} Records</span>
          </h3>
          <p style="margin:3px 0 0; font-size:0.8rem; color:var(--slate-500);">Stitching units, cutting masters, embroidery, printing & washing contractors</p>
        </div>
      </div>

      <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <button class="btn btn-secondary btn-sm" onclick="UI.exportTableToCSV('jobworkers-table', 'JobWorker_Master.csv')" style="display:inline-flex; align-items:center; gap:6px; font-weight:600;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
          </svg>
          Export CSV
        </button>

        <button class="btn btn-primary btn-sm" onclick="openJobWorkerModal()" style="display:inline-flex; align-items:center; gap:6px; font-weight:700; background:linear-gradient(135deg, #2563eb, #1d4ed8); box-shadow:0 4px 10px rgba(37,99,235,0.25); cursor:pointer;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
          </svg>
          Add Job Worker
        </button>
      </div>
    </div>

    <!-- Quick Filter Bar -->
    <div class="jw-toolbar">
      <div class="jw-search-wrap">
        <svg class="jw-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"/>
          <line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" id="jw-filter-input" class="jw-search-input" placeholder="Search worker, skill, phone, city..." onkeyup="filterJobWorkerTable(this.value)">
      </div>

      <div class="jw-filter-pills">
        <button type="button" class="jw-filter-pill active" id="pill-all" onclick="applyJobWorkerFilter('all', this)">All (<span id="pill-count-all">{{ $stats['total'] }}</span>)</button>
        <button type="button" class="jw-filter-pill" id="pill-active" onclick="applyJobWorkerFilter('active', this)">Active (<span id="pill-count-active">{{ $stats['active'] }}</span>)</button>
        <button type="button" class="jw-filter-pill" onclick="applyJobWorkerFilter('stitching', this)">Stitching</button>
        <button type="button" class="jw-filter-pill" onclick="applyJobWorkerFilter('cutting', this)">Cutting</button>
        <button type="button" class="jw-filter-pill" onclick="applyJobWorkerFilter('embroidery', this)">Embroidery</button>
        <button type="button" class="jw-filter-pill" onclick="applyJobWorkerFilter('washing', this)">Washing / Finishing</button>
        <button type="button" class="jw-filter-pill" onclick="applyJobWorkerFilter('blocked', this)">Blocked / Inactive</button>
      </div>
    </div>

    <!-- Empty State Box (Toggled if 0 records) -->
    <div id="jobworkers-empty-state" class="jw-empty-box" style="{{ $jobworkers->isEmpty() ? 'display:flex;' : 'display:none;' }}">
      <div class="jw-empty-icon-ring">
        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
        </svg>
      </div>

      <h3 style="margin:0 0 8px; font-size:1.25rem; font-weight:800; color:var(--slate-900);">No Job Workers in Directory Yet</h3>
      <p style="margin:0 0 20px; font-size:0.875rem; color:var(--slate-500); max-width:480px; line-height:1.5;">
        Register manufacturing contractors, cutting masters, sewing units, and embroidery specialists to manage job orders and piece rates.
      </p>

      <button type="button" class="btn btn-primary" onclick="openJobWorkerModal()" style="display:inline-flex; align-items:center; gap:8px; padding:10px 22px; font-size:0.9rem; font-weight:700; border-radius:10px; background:linear-gradient(135deg, #2563eb, #1d4ed8); box-shadow:0 6px 16px rgba(37,99,235,0.25); cursor:pointer;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"/>
          <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Add Your First Job Worker
      </button>

      <!-- Feature cards -->
      <div class="jw-empty-features">
        <div class="jw-feature-card">
          <div style="width:34px; height:34px; border-radius:8px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
          </div>
          <div>
            <div style="font-weight:700; font-size:0.825rem; color:var(--slate-800);">Piece Rates & Capacity</div>
            <div style="font-size:0.75rem; color:var(--slate-500); margin-top:2px;">Track standard unit rates, output limits, and daily work allocation</div>
          </div>
        </div>

        <div class="jw-feature-card">
          <div style="width:34px; height:34px; border-radius:8px; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          </div>
          <div>
            <div style="font-weight:700; font-size:0.825rem; color:var(--slate-800);">Job Work Process Tracking</div>
            <div style="font-size:0.75rem; color:var(--slate-500); margin-top:2px;">Issue cutting & stitching challans with live progress tracking</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Data Table Container -->
    <div class="table-responsive" id="jobworkers-table-container" style="{{ $jobworkers->isEmpty() ? 'display:none;' : '' }}">
      <table class="data-table" id="jobworkers-table">
        <thead>
          <tr>
            <th style="width: 50px; text-align: center;">#</th>
            <th>Worker / Unit Name & Code</th>
            <th>Specialization / Skill</th>
            <th style="text-align: right;">Rate / Pc</th>
            <th style="text-align: center;">Daily Capacity</th>
            <th>Contact & Phone</th>
            <th>Workshop Location</th>
            <th style="text-align: right;">Outstanding</th>
            <th style="text-align: center; width: 130px;">Status</th>
            <th style="text-align: center; width: 100px;">Actions</th>
          </tr>
        </thead>
        <tbody id="jobworkers-table-body">
          @foreach($jobworkers as $index => $jw)
            @php
              $skillLower = strtolower($jw->skill_type ?? '');
              $skillBadge = 'badge-info';
              if (str_contains($skillLower, 'stitch')) $skillBadge = 'badge-primary';
              elseif (str_contains($skillLower, 'cut')) $skillBadge = 'badge-warning';
              elseif (str_contains($skillLower, 'embroid')) $skillBadge = 'badge-purple';
              elseif (str_contains($skillLower, 'wash') || str_contains($skillLower, 'finish')) $skillBadge = 'badge-success';
              elseif (str_contains($skillLower, 'print')) $skillBadge = 'badge-cyan';
            @endphp
            <tr class="jw-row jw-row-transition" id="jobworker-row-{{ $jw->id }}" data-id="{{ $jw->id }}" data-skill="{{ $skillLower }}" data-status="{{ strtolower($jw->status ?? 'active') }}" data-balance="{{ $jw->outstanding ?? 0 }}">
              <td class="row-index" style="text-align: center; font-weight: 600; color: var(--slate-400);">{{ $index + 1 }}</td>
              <td>
                <div style="display:flex; align-items:center; gap:10px;">
                  <div class="jw-avatar" id="avatar-{{ $jw->id }}">
                    {{ strtoupper(substr($jw->name, 0, 2)) }}
                  </div>
                  <div>
                    <div class="jw-name-val" style="font-weight:700; color:var(--slate-900); font-size:0.9rem;">{{ $jw->name }}</div>
                    <div class="jw-code-val" style="font-size:0.75rem; color:var(--slate-500); font-family:monospace;">{{ $jw->code ?? ('JW-'.str_pad($jw->id, 3, '0', STR_PAD_LEFT)) }}</div>
                  </div>
                </div>
              </td>
              <td class="jw-skill-cell">
                <span class="badge {{ $skillBadge }}" style="font-size:0.75rem; font-weight:600; padding:4px 8px; border-radius:8px;">
                  {{ $jw->skill_type }}
                </span>
              </td>
              <td class="jw-rate-val" style="text-align: right; font-weight: 700; color: #059669;">
                ₹{{ number_format($jw->rate_per_piece, 2) }}
              </td>
              <td class="jw-capacity-val" style="text-align: center; font-weight: 600; color: var(--slate-700);">
                {{ number_format($jw->daily_capacity) }} pcs
              </td>
              <td>
                <div class="jw-contact-val" style="font-weight:600; color:var(--slate-800); font-size:0.85rem;">{{ $jw->contact_person ?: $jw->name }}</div>
                <div class="jw-phone-val" style="font-size:0.75rem; color:var(--slate-500);">{{ $jw->phone }}</div>
              </td>
              <td>
                <div class="jw-city-val" style="font-weight:600; color:var(--slate-700); font-size:0.85rem;">{{ $jw->city ?: ($jw->address ? \Illuminate\Support\Str::limit($jw->address, 25) : '—') }}</div>
                <div class="jw-state-val" style="font-size:0.75rem; color:var(--slate-500);">{{ $jw->state ?: '—' }}</div>
              </td>
              <td class="jw-outstanding-val" style="text-align: right; font-weight: 700; color: {{ ($jw->outstanding ?? 0) > 0 ? '#dc2626' : '#059669' }};">
                ₹{{ number_format($jw->outstanding ?? 0, 2) }}
              </td>
              <td style="text-align: center;">
                <!-- Direct Table Status Changer -->
                <select class="jw-status-select {{ strtolower($jw->status ?? 'active') }}" onchange="changeJobWorkerStatus({{ $jw->id }}, this.value, this)" title="Click to change status">
                  <option value="Active" {{ strtolower($jw->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                  <option value="Inactive" {{ strtolower($jw->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                  <option value="Blocked" {{ strtolower($jw->status ?? '') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                </select>
              </td>
              <td style="text-align: center;">
                <div style="display:inline-flex; align-items:center; gap:6px;">
                  <button type="button" class="btn btn-secondary btn-icon edit-btn-{{ $jw->id }}" onclick='editJobWorker(@json($jw))' title="Edit Job Worker" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                  </button>
                  <button type="button" class="btn btn-danger btn-icon" onclick="confirmDeleteJobWorker({{ $jw->id }}, '{{ addslashes($jw->name) }}')" title="Delete Job Worker" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
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

<!-- Job Worker Modal (Add & Edit Form via AJAX) -->
<div id="jobworker-modal" style="display:none;">
  <div class="jw-modal-box">
    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--slate-100); padding-bottom:16px; margin-bottom:18px;">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:38px; height:38px; border-radius:10px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
          </svg>
        </div>
        <div>
          <h3 id="jwmodal-title" style="margin:0; font-size:1.1rem; font-weight:800; color:var(--slate-900);">Add New Job Worker</h3>
          <p style="margin:2px 0 0; font-size:0.75rem; color:var(--slate-500);">Complete job worker & manufacturing contractor profile</p>
        </div>
      </div>
      <button type="button" onclick="closeJobWorkerModal()" style="background:none; border:none; color:var(--slate-400); cursor:pointer; font-size:1.4rem; line-height:1; padding:4px;">
        &times;
      </button>
    </div>

    <form id="jobworker-form" onsubmit="saveJobWorkerAjax(event)">
      @csrf
      <input type="hidden" name="id" id="jobworker-id" value="">

      <!-- Basic Details -->
      <div class="jw-modal-section-title">General Information</div>
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:14px; margin-bottom:12px;">
        <div class="form-group" style="margin:0;">
          <label class="form-label required">Worker / Unit Name</label>
          <input type="text" name="name" id="jw_name" class="form-control" required placeholder="e.g. Raj Stitching Works">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Job Worker Code</label>
          <input type="text" name="code" id="jw_code" class="form-control" placeholder="Auto-generated if empty (e.g. JW-001)">
        </div>
      </div>

      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:14px; margin-bottom:12px;">
        <div class="form-group" style="margin:0;">
          <label class="form-label required">Specialization / Process</label>
          <select name="skill_type" id="jw_skill" class="form-control" required>
            <option value="Stitching">Stitching / Sewing</option>
            <option value="Cutting">Fabric Cutting</option>
            <option value="Embroidery">Computer Embroidery</option>
            <option value="Printing">Screen / Digital Printing</option>
            <option value="Washing & Finishing">Washing & Finishing</option>
            <option value="Ironing & Packing">Ironing & Packing</option>
            <option value="Button & Eyelet">Button & Eyelet Hole</option>
          </select>
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Contact Person</label>
          <input type="text" name="contact_person" id="jw_contact" class="form-control" placeholder="e.g. Master Rajesh">
        </div>
      </div>

      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:14px; margin-bottom:12px;">
        <div class="form-group" style="margin:0;">
          <label class="form-label required">Phone / Mobile</label>
          <input type="text" name="phone" id="jw_phone" class="form-control" required placeholder="e.g. 9845067890">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" id="jw_email" class="form-control" placeholder="e.g. raj.stitching@gmail.com">
        </div>
      </div>

      <!-- Financial & Rates -->
      <div class="jw-modal-section-title">Rates & Production Capacity</div>
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:14px; margin-bottom:12px;">
        <div class="form-group" style="margin:0;">
          <label class="form-label required">Default Rate / Piece (₹)</label>
          <input type="number" step="0.01" name="rate_per_piece" id="jw_rate" class="form-control" required placeholder="45.00" value="45.00">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Daily Capacity (Pcs)</label>
          <input type="number" name="daily_capacity" id="jw_capacity" class="form-control" placeholder="500" value="500">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Opening Outstanding (₹)</label>
          <input type="number" step="0.01" name="outstanding" id="jw_outstanding" class="form-control" placeholder="0.00" value="0.00">
        </div>
      </div>

      <!-- Workshop Address -->
      <div class="jw-modal-section-title">Workshop Location</div>
      <div class="form-group" style="margin-bottom:12px;">
        <label class="form-label">Workshop Address</label>
        <textarea name="address" id="jw_address" class="form-control" rows="2" placeholder="Gala / Unit address, industrial estate..."></textarea>
      </div>

      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap:14px; margin-bottom:20px;">
        <div class="form-group" style="margin:0;">
          <label class="form-label">City</label>
          <input type="text" name="city" id="jw_city" class="form-control" placeholder="e.g. Tirupur">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">State</label>
          <input type="text" name="state" id="jw_state" class="form-control" placeholder="e.g. Tamil Nadu">
        </div>
        <div class="form-group" style="margin:0;">
          <label class="form-label">Pincode</label>
          <input type="text" name="pincode" id="jw_pincode" class="form-control" placeholder="e.g. 641602">
        </div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid var(--slate-100); padding-top:16px;">
        <button type="button" class="btn btn-secondary" onclick="closeJobWorkerModal()">Cancel</button>
        <button type="submit" id="save-jw-btn" class="btn btn-primary" style="background:linear-gradient(135deg, #2563eb, #1d4ed8); font-weight:700;">Save Job Worker</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Confirmation Modal (via AJAX) -->
<div id="delete-jobworker-modal" style="display:none;">
  <div class="jw-modal-box" style="max-width:440px; text-align:center;">
    <div style="width:52px; height:52px; border-radius:50%; background:#fef2f2; color:#dc2626; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
      <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
    </div>
    <h3 style="margin:0 0 8px; font-size:1.15rem; font-weight:800; color:var(--slate-900);">Delete Job Worker</h3>
    <p style="margin:0 0 20px; font-size:0.875rem; color:var(--slate-500); line-height:1.4;">
      Are you sure you want to delete <strong id="delete-jw-name" style="color:var(--slate-800);">this job worker</strong>? This action cannot be undone.
    </p>

    <input type="hidden" id="delete-jw-id" value="">
    <div style="display:flex; justify-content:center; gap:10px;">
      <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
      <button type="button" id="confirm-delete-btn" class="btn btn-danger" onclick="deleteJobWorkerAjax()" style="font-weight:700;">Yes, Delete Job Worker</button>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  const CSRF_TOKEN = '{{ csrf_token() }}';

  function getSkillBadgeClass(skill) {
    const s = (skill || '').toLowerCase();
    if (s.includes('stitch')) return 'badge-primary';
    if (s.includes('cut')) return 'badge-warning';
    if (s.includes('embroid')) return 'badge-purple';
    if (s.includes('wash') || s.includes('finish')) return 'badge-success';
    if (s.includes('print')) return 'badge-cyan';
    return 'badge-info';
  }

  // --- Modal Open / Close ---
  function openJobWorkerModal() {
    const modal = document.getElementById('jobworker-modal');
    if (!modal) return;
    
    document.getElementById('jobworker-form').reset();
    document.getElementById('jobworker-id').value = '';
    document.getElementById('jwmodal-title').textContent = 'Add New Job Worker';
    document.getElementById('save-jw-btn').textContent = 'Save Job Worker';
    document.getElementById('save-jw-btn').disabled = false;
    document.getElementById('jw_rate').value = '45.00';
    document.getElementById('jw_capacity').value = '500';
    document.getElementById('jw_outstanding').value = '0.00';
    
    modal.style.display = 'flex';
  }

  function closeJobWorkerModal() {
    const modal = document.getElementById('jobworker-modal');
    if (modal) modal.style.display = 'none';
  }

  function editJobWorker(jw) {
    const modal = document.getElementById('jobworker-modal');
    if (!modal) return;

    document.getElementById('jwmodal-title').textContent = 'Edit Job Worker';
    document.getElementById('save-jw-btn').textContent = 'Update Job Worker';
    document.getElementById('save-jw-btn').disabled = false;
    document.getElementById('jobworker-id').value = jw.id;

    document.getElementById('jw_name').value = jw.name || '';
    document.getElementById('jw_code').value = jw.code || '';
    document.getElementById('jw_skill').value = jw.skill_type || 'Stitching';
    document.getElementById('jw_contact').value = jw.contact_person || '';
    document.getElementById('jw_phone').value = jw.phone || '';
    document.getElementById('jw_email').value = jw.email || '';
    document.getElementById('jw_rate').value = jw.rate_per_piece || 45.00;
    document.getElementById('jw_capacity').value = jw.daily_capacity || 500;
    document.getElementById('jw_outstanding').value = jw.outstanding || '0.00';
    document.getElementById('jw_address').value = jw.address || '';
    document.getElementById('jw_city').value = jw.city || '';
    document.getElementById('jw_state').value = jw.state || '';
    document.getElementById('jw_pincode').value = jw.pincode || '';

    modal.style.display = 'flex';
  }

  function confirmDeleteJobWorker(id, name) {
    const modal = document.getElementById('delete-jobworker-modal');
    if (!modal) return;
    
    document.getElementById('delete-jw-name').textContent = name;
    document.getElementById('delete-jw-id').value = id;
    document.getElementById('confirm-delete-btn').textContent = 'Yes, Delete Job Worker';
    document.getElementById('confirm-delete-btn').disabled = false;
    modal.style.display = 'flex';
  }

  function closeDeleteModal() {
    const modal = document.getElementById('delete-jobworker-modal');
    if (modal) modal.style.display = 'none';
  }

  // --- 1. STATUS CHANGE THROUGH TABLE VIA AJAX ---
  function changeJobWorkerStatus(id, newStatus, selectEl) {
    const prevClass = selectEl.className;
    selectEl.style.opacity = '0.5';

    fetch('/masters/jobworkers/' + id, {
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
        selectEl.className = 'jw-status-select ' + newStatus.toLowerCase();
        
        const row = document.getElementById('jobworker-row-' + id);
        if (row) row.setAttribute('data-status', newStatus.toLowerCase());

        if (data.stats) updateKpiStats(data.stats);

        UI.showToast('Status Updated', 'Job worker status set to ' + newStatus, 'success');
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
  function saveJobWorkerAjax(e) {
    e.preventDefault();

    const saveBtn = document.getElementById('save-jw-btn');
    const jwId = document.getElementById('jobworker-id').value;
    const isEdit = Boolean(jwId);

    const payload = {
      name: document.getElementById('jw_name').value.trim(),
      code: document.getElementById('jw_code').value.trim(),
      skill_type: document.getElementById('jw_skill').value,
      contact_person: document.getElementById('jw_contact').value.trim(),
      phone: document.getElementById('jw_phone').value.trim(),
      email: document.getElementById('jw_email').value.trim(),
      rate_per_piece: parseFloat(document.getElementById('jw_rate').value) || 0,
      daily_capacity: parseInt(document.getElementById('jw_capacity').value) || 0,
      outstanding: parseFloat(document.getElementById('jw_outstanding').value) || 0,
      address: document.getElementById('jw_address').value.trim(),
      city: document.getElementById('jw_city').value.trim(),
      state: document.getElementById('jw_state').value.trim(),
      pincode: document.getElementById('jw_pincode').value.trim()
    };

    if (!payload.name) {
      UI.showToast('Validation Error', 'Worker Name is required', 'error');
      return;
    }
    if (!payload.phone) {
      UI.showToast('Validation Error', 'Phone Number is required', 'error');
      return;
    }

    saveBtn.disabled = true;
    saveBtn.textContent = isEdit ? 'Updating...' : 'Saving...';

    const url = isEdit ? ('/masters/jobworkers/' + jwId) : "{{ route('masters.jobworkers.store') }}";
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
      saveBtn.textContent = isEdit ? 'Update Job Worker' : 'Save Job Worker';

      if (data.success && data.job_worker) {
        closeJobWorkerModal();
        UI.showToast(isEdit ? 'Job Worker Updated' : 'Job Worker Created', data.message, 'success');

        if (isEdit) {
          updateTableRow(data.job_worker);
        } else {
          prependTableRow(data.job_worker);
        }

        if (data.stats) updateKpiStats(data.stats);
      } else {
        const msg = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message || 'Validation error');
        UI.showToast('Error', msg, 'error');
      }
    })
    .catch(err => {
      saveBtn.disabled = false;
      saveBtn.textContent = isEdit ? 'Update Job Worker' : 'Save Job Worker';
      UI.showToast('Error', 'Failed to save job worker details', 'error');
    });
  }

  // --- 3. DELETE USING AJAX ---
  function deleteJobWorkerAjax() {
    const id = document.getElementById('delete-jw-id').value;
    if (!id) return;

    const delBtn = document.getElementById('confirm-delete-btn');
    delBtn.disabled = true;
    delBtn.textContent = 'Deleting...';

    fetch('/masters/jobworkers/' + id, {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': CSRF_TOKEN
      }
    })
    .then(res => res.json())
    .then(data => {
      delBtn.disabled = false;
      delBtn.textContent = 'Yes, Delete Job Worker';
      closeDeleteModal();

      if (data.success) {
        UI.showToast('Job Worker Deleted', data.message || 'Job Worker removed successfully', 'warning');
        
        const row = document.getElementById('jobworker-row-' + id);
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
        UI.showToast('Error', data.message || 'Could not delete job worker', 'error');
      }
    })
    .catch(err => {
      delBtn.disabled = false;
      delBtn.textContent = 'Yes, Delete Job Worker';
      UI.showToast('Error', 'Failed to delete job worker', 'error');
    });
  }

  // --- Dynamic Table DOM Manipulation ---
  function prependTableRow(jw) {
    const tbody = document.getElementById('jobworkers-table-body');
    const tableContainer = document.getElementById('jobworkers-table-container');
    const emptyState = document.getElementById('jobworkers-empty-state');

    if (emptyState) emptyState.style.display = 'none';
    if (tableContainer) tableContainer.style.display = '';

    const tr = document.createElement('tr');
    tr.className = 'jw-row jw-row-transition';
    tr.id = 'jobworker-row-' + jw.id;
    tr.setAttribute('data-id', jw.id);
    tr.setAttribute('data-skill', (jw.skill_type || '').toLowerCase());
    tr.setAttribute('data-status', (jw.status || 'active').toLowerCase());
    tr.setAttribute('data-balance', jw.outstanding || 0);

    const initials = (jw.name || 'JW').substring(0, 2).toUpperCase();
    const badgeClass = getSkillBadgeClass(jw.skill_type);
    const statusVal = (jw.status || 'Active');
    const statusLower = statusVal.toLowerCase();

    tr.innerHTML = `
      <td class="row-index" style="text-align: center; font-weight: 600; color: var(--slate-400);">1</td>
      <td>
        <div style="display:flex; align-items:center; gap:10px;">
          <div class="jw-avatar" id="avatar-${jw.id}">${initials}</div>
          <div>
            <div class="jw-name-val" style="font-weight:700; color:var(--slate-900); font-size:0.9rem;">${jw.name}</div>
            <div class="jw-code-val" style="font-size:0.75rem; color:var(--slate-500); font-family:monospace;">${jw.code || ('JW-' + String(jw.id).padStart(3, '0'))}</div>
          </div>
        </div>
      </td>
      <td class="jw-skill-cell">
        <span class="badge ${badgeClass}" style="font-size:0.75rem; font-weight:600; padding:4px 8px; border-radius:8px;">
          ${jw.skill_type || 'Stitching'}
        </span>
      </td>
      <td class="jw-rate-val" style="text-align: right; font-weight: 700; color: #059669;">
        ₹${Number(jw.rate_per_piece || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
      </td>
      <td class="jw-capacity-val" style="text-align: center; font-weight: 600; color: var(--slate-700);">
        ${Number(jw.daily_capacity || 0).toLocaleString()} pcs
      </td>
      <td>
        <div class="jw-contact-val" style="font-weight:600; color:var(--slate-800); font-size:0.85rem;">${jw.contact_person || jw.name}</div>
        <div class="jw-phone-val" style="font-size:0.75rem; color:var(--slate-500);">${jw.phone || '—'}</div>
      </td>
      <td>
        <div class="jw-city-val" style="font-weight:600; color:var(--slate-700); font-size:0.85rem;">${jw.city || (jw.address ? jw.address.substring(0, 25) : '—')}</div>
        <div class="jw-state-val" style="font-size:0.75rem; color:var(--slate-500);">${jw.state || '—'}</div>
      </td>
      <td class="jw-outstanding-val" style="text-align: right; font-weight: 700; color: ${Number(jw.outstanding || 0) > 0 ? '#dc2626' : '#059669'};">
        ₹${Number(jw.outstanding || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
      </td>
      <td style="text-align: center;">
        <select class="jw-status-select ${statusLower}" onchange="changeJobWorkerStatus(${jw.id}, this.value, this)" title="Click to change status">
          <option value="Active" ${statusLower === 'active' ? 'selected' : ''}>Active</option>
          <option value="Inactive" ${statusLower === 'inactive' ? 'selected' : ''}>Inactive</option>
          <option value="Blocked" ${statusLower === 'blocked' ? 'selected' : ''}>Blocked</option>
        </select>
      </td>
      <td style="text-align: center;">
        <div style="display:inline-flex; align-items:center; gap:6px;">
          <button type="button" class="btn btn-secondary btn-icon edit-btn-${jw.id}" title="Edit Job Worker" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
              <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
          </button>
          <button type="button" class="btn btn-danger btn-icon" onclick="confirmDeleteJobWorker(${jw.id}, '${(jw.name || '').replace(/'/g, "\\'")}')" title="Delete Job Worker" style="width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="3 6 5 6 21 6"/>
              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
            </svg>
          </button>
        </div>
      </td>
    `;

    const editBtn = tr.querySelector(`.edit-btn-${jw.id}`);
    if (editBtn) {
      editBtn.onclick = () => editJobWorker(jw);
    }

    tbody.insertBefore(tr, tbody.firstChild);
    reindexRows();
  }

  function updateTableRow(jw) {
    const row = document.getElementById('jobworker-row-' + jw.id);
    if (!row) return;

    row.querySelector('.jw-name-val').textContent = jw.name;
    row.querySelector('.jw-code-val').textContent = jw.code || ('JW-' + String(jw.id).padStart(3, '0'));
    row.querySelector('.jw-contact-val').textContent = jw.contact_person || jw.name;
    row.querySelector('.jw-phone-val').textContent = jw.phone || '—';
    row.querySelector('.jw-city-val').textContent = jw.city || (jw.address ? jw.address.substring(0, 25) : '—');
    row.querySelector('.jw-state-val').textContent = jw.state || '—';
    
    const initials = (jw.name || 'JW').substring(0, 2).toUpperCase();
    const avatar = document.getElementById('avatar-' + jw.id);
    if (avatar) avatar.textContent = initials;

    const skillCell = row.querySelector('.jw-skill-cell');
    if (skillCell) {
      const badgeClass = getSkillBadgeClass(jw.skill_type);
      skillCell.innerHTML = `<span class="badge ${badgeClass}" style="font-size:0.75rem; font-weight:600; padding:4px 8px; border-radius:8px;">${jw.skill_type || 'Stitching'}</span>`;
    }

    const rateCell = row.querySelector('.jw-rate-val');
    if (rateCell) {
      rateCell.textContent = '₹' + Number(jw.rate_per_piece || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    const capCell = row.querySelector('.jw-capacity-val');
    if (capCell) {
      capCell.textContent = Number(jw.daily_capacity || 0).toLocaleString() + ' pcs';
    }

    const outCell = row.querySelector('.jw-outstanding-val');
    if (outCell) {
      const outVal = Number(jw.outstanding || 0);
      outCell.textContent = '₹' + outVal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
      outCell.style.color = outVal > 0 ? '#dc2626' : '#059669';
    }

    const editBtn = row.querySelector('.btn-secondary');
    if (editBtn) {
      editBtn.onclick = () => editJobWorker(jw);
    }
  }

  function reindexRows() {
    const rows = document.querySelectorAll('#jobworkers-table-body tr');
    rows.forEach((row, i) => {
      const idxCell = row.querySelector('.row-index');
      if (idxCell) idxCell.textContent = i + 1;
    });
  }

  function checkEmptyState() {
    const rows = document.querySelectorAll('#jobworkers-table-body tr');
    const emptyState = document.getElementById('jobworkers-empty-state');
    const tableContainer = document.getElementById('jobworkers-table-container');

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
    if (document.getElementById('jobworker-badge-count')) document.getElementById('jobworker-badge-count').textContent = stats.total + ' Records';

    if (document.getElementById('stat-capacity-count') && stats.totalCapacity !== undefined) {
      document.getElementById('stat-capacity-count').textContent = Number(stats.totalCapacity).toLocaleString();
    }

    if (document.getElementById('stat-stitching-count') && stats.stitchingCount !== undefined) {
      document.getElementById('stat-stitching-count').textContent = stats.stitchingCount;
    }

    if (document.getElementById('stat-total-outstanding') && stats.totalOutstanding !== undefined) {
      const val = Number(stats.totalOutstanding);
      document.getElementById('stat-total-outstanding').textContent = '₹' + val.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
      document.getElementById('stat-total-outstanding').style.color = val > 0 ? '#dc2626' : '#059669';
    }
  }

  // --- Live Table Filter & Pills ---
  function filterJobWorkerTable(query) {
    query = (query || '').toLowerCase().trim();
    const rows = document.querySelectorAll('#jobworkers-table-body tr');
    rows.forEach(row => {
      const text = row.innerText.toLowerCase();
      row.style.display = text.includes(query) ? '' : 'none';
    });
  }

  function applyJobWorkerFilter(type, btn) {
    document.querySelectorAll('.jw-filter-pill').forEach(el => el.classList.remove('active'));
    if (btn) btn.classList.add('active');

    const rows = document.querySelectorAll('#jobworkers-table-body tr.jw-row');
    rows.forEach(row => {
      const status = row.getAttribute('data-status');
      const skill = (row.getAttribute('data-skill') || '').toLowerCase();
      const balance = parseFloat(row.getAttribute('data-balance') || 0);

      if (type === 'all') {
        row.style.display = '';
      } else if (type === 'active') {
        row.style.display = (status === 'active') ? '' : 'none';
      } else if (type === 'stitching') {
        row.style.display = skill.includes('stitch') ? '' : 'none';
      } else if (type === 'cutting') {
        row.style.display = skill.includes('cut') ? '' : 'none';
      } else if (type === 'embroidery') {
        row.style.display = skill.includes('embroid') ? '' : 'none';
      } else if (type === 'washing') {
        row.style.display = (skill.includes('wash') || skill.includes('finish')) ? '' : 'none';
      } else if (type === 'blocked') {
        row.style.display = (status === 'blocked' || status === 'inactive') ? '' : 'none';
      }
    });
  }

  // Close modals on clicking backdrop
  window.addEventListener('click', function(e) {
    const jwModal = document.getElementById('jobworker-modal');
    const delModal = document.getElementById('delete-jobworker-modal');
    if (e.target === jwModal) closeJobWorkerModal();
    if (e.target === delModal) closeDeleteModal();
  });
</script>
@endpush
