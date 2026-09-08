@extends('layouts.app')

@section('title', 'ERP Dashboard - GarmentERP')

@section('content')
  {{-- The App router (App.init / App.renderContent) will mount the active module and dynamic UI here --}}
  <div id="erp-view-container">
    @if (session('success'))
      <div style="margin-bottom: 20px; padding: 14px 18px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 10px; color: #065f46; display: flex; align-items: center; gap: 10px; font-size: 0.9rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <span>{{ session('success') }}</span>
      </div>
    @endif
  </div>
@endsection
