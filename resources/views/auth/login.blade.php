@extends('layouts.auth')

@section('title', 'Admin Sign In')

@section('content')
<div class="auth-card">
  <div class="auth-brand">
    <div class="logo-icon">G</div>
    <h1>GarmentERP</h1>
    <p>Sign in to access your Manufacturing & Supply Chain Portal</p>
  </div>

  {{-- Session Flash Alerts --}}
  @if (session('success'))
    <div class="alert alert-success">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  @if (session('info'))
    <div class="alert alert-info">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
      <span>{{ session('info') }}</span>
    </div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <div>
        @foreach ($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    </div>
  @endif

  <form method="POST" action="{{ route('login.submit') }}" id="login-form">
    @csrf

    <div class="form-group" style="margin-bottom: 16px;">
      <label class="form-label" for="email" style="font-weight:600; font-size:0.875rem; color:#334155; margin-bottom:6px; display:block;">Email Address</label>
      <input 
        type="email" 
        id="email" 
        name="email" 
        class="form-control" 
        placeholder="admin@garmenterp.com" 
        value="{{ old('email', 'admin@garmenterp.com') }}" 
        required 
        autofocus
        style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.9rem;"
      >
    </div>

    <div class="form-group" style="margin-bottom: 18px;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
        <label class="form-label" for="password" style="font-weight:600; font-size:0.875rem; color:#334155; margin-bottom:0;">Password</label>
        <span style="font-size:0.75rem; color:#64748b;">Default: admin123</span>
      </div>
      <div style="position:relative;">
        <input 
          type="password" 
          id="password" 
          name="password" 
          class="form-control font-mono" 
          placeholder="••••••••" 
          value="admin123" 
          required
          style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.9rem; letter-spacing: 0.05em;"
        >
      </div>
    </div>

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; font-size: 0.85rem;">
      <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: #475569; user-select: none;">
        <input type="checkbox" name="remember" id="remember" value="1" checked style="accent-color: #2563eb; width:16px; height:16px;">
        <span>Remember my session</span>
      </label>
    </div>

    <button 
      type="submit" 
      class="btn btn-primary w-full" 
      style="width:100%; padding:12px; background:#2563eb; color:#fff; border:none; border-radius:8px; font-size:0.95rem; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; transition: background 0.2s;"
      onmouseover="this.style.background='#1d4ed8'" 
      onmouseout="this.style.background='#2563eb'"
    >
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
      <span>Sign In to Admin Portal</span>
    </button>
  </form>

  <div class="demo-box">
    <div style="font-weight: 600; margin-bottom: 8px; color: #1e293b;">Default Admin Credentials:</div>
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 4px;">
      <span>Email:</span>
      <span class="demo-pill" onclick="fillCreds('admin@garmenterp.com', 'admin123')">admin@garmenterp.com</span>
    </div>
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <span>Password:</span>
      <span class="demo-pill" onclick="fillCreds('admin@garmenterp.com', 'admin123')">admin123</span>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  function fillCreds(email, pass) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = pass;
  }
</script>
@endsection
