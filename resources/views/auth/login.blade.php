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
      <div style="position:relative; display:flex; align-items:center;">
        <input 
          type="password" 
          id="password" 
          name="password" 
          class="form-control font-mono" 
          placeholder="••••••••" 
          value="admin123" 
          required
          style="width:100%; padding:10px 40px 10px 14px; border:1px solid #cbd5e1; border-radius:8px; font-size:0.9rem; letter-spacing: 0.05em;"
        >
        <button 
          type="button" 
          id="togglePassword" 
          onclick="togglePasswordVisibility()" 
          title="Show / Hide Password" 
          aria-label="Show or hide password"
          style="position:absolute; right:10px; background:none; border:none; color:#64748b; cursor:pointer; padding:4px; display:flex; align-items:center; justify-content:center;"
        >
          <svg id="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </button>
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
      id="submit-btn"
      class="btn btn-primary w-full" 
      style="width:100%; padding:12px; background:#2563eb; color:#fff; border:none; border-radius:8px; font-size:0.95rem; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; transition: background 0.2s;"
      onmouseover="this.style.background='#1d4ed8'" 
      onmouseout="this.style.background='#2563eb'"
    >
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
      <span>Sign In to Admin Portal</span>
    </button>
  </form>

  <div class="demo-box" style="margin-top:20px; padding:14px; background:#f8fafc; border:1px dashed #cbd5e1; border-radius:10px; font-size:0.82rem; color:#475569;">
    <div style="font-weight: 600; margin-bottom: 8px; color: #1e293b; display:flex; justify-content:space-between; align-items:center;">
      <span>Quick Demo Accounts:</span>
      <span style="font-size:0.75rem; color:#64748b;">Click to autofill</span>
    </div>
    <div style="display:flex; flex-direction:column; gap:6px;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
        <span>Admin:</span>
        <button type="button" class="demo-pill" onclick="fillCreds('admin@garmenterp.com', 'admin123')">admin@garmenterp.com</button>
      </div>
      <div style="display:flex; justify-content:space-between; align-items:center;">
        <span>Supervisor:</span>
        <button type="button" class="demo-pill" onclick="fillCreds('supervisor@garmenterp.com', 'admin123')">supervisor@garmenterp.com</button>
      </div>
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

  function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
    } else {
      passwordInput.type = 'password';
      eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
    }
  }

  document.getElementById('login-form')?.addEventListener('submit', function() {
    const btn = document.getElementById('submit-btn');
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<svg class="animate-spin" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg><span>Signing in...</span>';
    }
  });
</script>
@endsection

