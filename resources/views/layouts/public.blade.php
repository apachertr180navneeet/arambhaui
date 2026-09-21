<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Customer Voucher Portal') - {{ $companyName ?? 'GarmentERP' }}</title>
  <meta name="description" content="Customer QR voucher verification and instant discount redemption portal.">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="base-url" content="{{ url('/') }}">

  <!-- Base URL & AJAX Interceptor for dynamic subfolder / server deployment -->
  <script>
    window.APP_URL = "{{ rtrim(url('/'), '/') }}";
    window.API_BASE_URL = window.APP_URL;
    window.COMPANY_SETTINGS = {!! json_encode($companySettings ?? []) !!};
    window.COMPANY_NAME = {!! json_encode($companyName ?? 'GarmentERP') !!};

    // Helper to resolve relative routes to the full application base URL
    window.apiUrl = function(path) {
      if (!path) return window.APP_URL;
      if (/^https?:\/\//i.test(path) || path.startsWith('blob:') || path.startsWith('data:')) {
        return path;
      }
      const base = window.APP_URL.replace(/\/+$/, '');
      const cleanPath = path.toString().replace(/^\/+/, '');
      return base + '/' + cleanPath;
    };

    // Global fetch interceptor: automatically prepends base URL to root-relative paths
    (function() {
      const originalFetch = window.fetch;
      window.fetch = function(resource, init) {
        if (typeof resource === 'string') {
          if (resource.startsWith('/') && !resource.startsWith('//')) {
            const parser = document.createElement('a');
            parser.href = window.APP_URL;
            const basePath = parser.pathname.replace(/\/+$/, '');
            if (basePath && basePath !== '/' && !resource.startsWith(basePath + '/') && resource !== basePath) {
              resource = basePath + (resource.startsWith('/') ? resource : '/' + resource);
            }
          }
        } else if (resource instanceof Request) {
          try {
            const urlStr = resource.url;
            const parser = document.createElement('a');
            parser.href = window.APP_URL;
            const basePath = parser.pathname.replace(/\/+$/, '');
            if (basePath && basePath !== '/') {
              const reqUrl = new URL(urlStr, window.location.origin);
              if (!reqUrl.pathname.startsWith(basePath + '/') && reqUrl.pathname !== basePath) {
                const newUrl = reqUrl.origin + basePath + reqUrl.pathname + reqUrl.search;
                resource = new Request(newUrl, resource);
              }
            }
          } catch (e) {}
        }
        return originalFetch.call(this, resource, init);
      };
    })();
  </script>

  <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Application Stylesheets -->
  <link rel="stylesheet" href="{{ asset('css/main.css') }}">
  <link rel="stylesheet" href="{{ asset('css/forms.css') }}">
  <link rel="stylesheet" href="{{ asset('css/qr.css') }}">

  <!-- jsQR library for browser camera/file QR decoding -->
  <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>

  <!-- QRCode.js library for dynamic UPI QR generation -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

  <!-- SweetAlert2 library -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body.public-layout {
      margin: 0;
      padding: 0;
      min-height: 100vh;
      background: radial-gradient(circle at 10% 20%, rgba(30, 41, 59, 0.04) 0%, rgba(15, 23, 42, 0.08) 90%), #f8fafc;
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      color: #1e293b;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .public-navbar {
      background: #ffffff;
      border-bottom: 1px solid #e2e8f0;
      padding: 14px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
    }

    .public-brand {
      display: flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
      color: #0f172a;
    }

    .public-brand-logo {
      width: 38px;
      height: 38px;
      background: linear-gradient(135deg, #4f46e5, #7c3aed);
      color: #ffffff;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 1.15rem;
      box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.25);
    }

    .public-brand-title {
      font-size: 1.1rem;
      font-weight: 800;
      letter-spacing: -0.02em;
      line-height: 1.2;
    }

    .public-brand-badge {
      display: inline-block;
      font-size: 0.65rem;
      font-weight: 700;
      background: #eff6ff;
      color: #2563eb;
      padding: 2px 6px;
      border-radius: 4px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-left: 6px;
    }

    .public-brand-sub {
      font-size: 0.75rem;
      color: #64748b;
      font-weight: 500;
    }

    .public-main-container {
      flex: 1;
      width: 100%;
      max-width: 1000px;
      margin: 0 auto;
      padding: 32px 20px;
      box-sizing: border-box;
    }

    .public-footer {
      text-align: center;
      padding: 20px 24px;
      font-size: 0.8rem;
      color: #94a3b8;
      border-top: 1px solid #f1f5f9;
      background: #ffffff;
    }

    @media (max-width: 640px) {
      .public-navbar {
        padding: 12px 16px;
      }
      .public-main-container {
        padding: 20px 14px;
      }
    }
  </style>

  @stack('styles')
</head>
<body class="public-layout">



  <!-- Main Content Container -->
  <main class="public-main-container">
    @yield('content')
  </main>

  <!-- Clean Public Footer -->
  <footer class="public-footer">
    <div>&copy; {{ date('Y') }} {{ $companyName ?? 'GarmentERP' }} &bull; Single-Use Secure QR Voucher System</div>
  </footer>

  <!-- Core UI Components (Toasts, Modals) -->
  <script src="{{ asset('js/components.js') }}"></script>

  <script>
    // Global SweetAlert Toast Configuration
    window.Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
      }
    });

    @if (session('success'))
      Toast.fire({
        icon: 'success',
        title: {!! json_encode(session('success')) !!}
      });
    @endif

    @if (session('error'))
      Toast.fire({
        icon: 'error',
        title: {!! json_encode(session('error')) !!}
      });
    @endif
  </script>

  @stack('scripts')
</body>
</html>
