<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Admin Login') - GarmentERP</title>
  <meta name="description" content="GarmentERP - Manufacturing & Supply Chain Management Portal">

  <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Application Stylesheets -->
  <link rel="stylesheet" href="{{ asset('css/main.css') }}">
  <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
  
  <style>
    body.auth-page {
      background: radial-gradient(circle at 10% 20%, rgba(30, 41, 59, 0.04) 0%, rgba(15, 23, 42, 0.08) 90%), #f8fafc;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
      font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .auth-container {
      width: 100%;
      max-width: 460px;
    }
    .auth-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 16px;
      box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
      padding: 36px 32px;
      position: relative;
      overflow: hidden;
    }
    .auth-card::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #2563eb, #3b82f6, #06b6d4);
    }
    .auth-brand {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      margin-bottom: 28px;
    }
    .auth-brand .logo-icon {
      width: 52px;
      height: 52px;
      background: linear-gradient(135deg, #1e293b, #0f172a);
      color: #38bdf8;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      font-weight: 800;
      box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.2);
      margin-bottom: 12px;
    }
    .auth-brand h1 {
      font-size: 1.5rem;
      font-weight: 700;
      color: #0f172a;
      margin: 0;
      letter-spacing: -0.02em;
    }
    .auth-brand p {
      font-size: 0.875rem;
      color: #64748b;
      margin-top: 6px;
      margin-bottom: 0;
    }
    .demo-box {
      margin-top: 24px;
      padding: 14px;
      background: #f8fafc;
      border: 1px dashed #cbd5e1;
      border-radius: 10px;
      font-size: 0.8rem;
      color: #475569;
    }
    .demo-pill {
      display: inline-block;
      background: #e2e8f0;
      color: #0f172a;
      padding: 2px 6px;
      border-radius: 4px;
      font-family: 'JetBrains Mono', monospace;
      font-size: 0.75rem;
      font-weight: 600;
      cursor: pointer;
    }
    .demo-pill:hover {
      background: #cbd5e1;
    }
    .alert {
      padding: 12px 16px;
      border-radius: 8px;
      font-size: 0.85rem;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .alert-danger {
      background: #fef2f2;
      border: 1px solid #fecaca;
      color: #991b1b;
    }
    .alert-success {
      background: #f0fdf4;
      border: 1px solid #bbf7d0;
      color: #166534;
    }
    .alert-info {
      background: #f0f9ff;
      border: 1px solid #bae6fd;
      color: #075985;
    }
  </style>
</head>
<body class="auth-page">
  <div class="auth-container">
    @yield('content')
  </div>

  @yield('scripts')
</body>
</html>
