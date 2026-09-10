@extends('layouts.app')

@section('title', 'Customer QR Claim & Scanner Portal - GarmentERP')

@section('breadcrumb')
  <div class="breadcrumb-item"><span>Barcodes & QR</span></div>
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
  <div class="breadcrumb-item active"><span>Customer Claim Portal</span></div>
@endsection

@section('content')
<div style="max-width:920px; margin:0 auto; display:flex; flex-direction:column; gap:24px;">

  <!-- Portal Header -->
  <div style="text-align:center;">
    <div style="display:inline-flex; align-items:center; justify-content:center; width:52px; height:52px; border-radius:14px; background:linear-gradient(135deg, #4f46e5, #7c3aed); color:#fff; margin-bottom:12px; box-shadow:0 8px 16px -4px rgba(79, 70, 229, 0.3);">
      <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/></svg>
    </div>
    <h2 style="margin:0; font-size:1.6rem; font-weight:800; color:var(--slate-900);">Customer QR Discount Claim Portal</h2>
    <p style="margin:6px auto 0; max-width:600px; color:var(--slate-500); font-size:0.9rem;">
      Enter your phone number and voucher code, or upload / scan your QR image to claim your discount instantly.
    </p>
  </div>

  <!-- Main Claim Card -->
  <div class="card" style="background:#fff; border-radius:var(--radius-xl); border:1px solid var(--slate-200); box-shadow:var(--shadow-sm); padding:28px;">
    
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px; align-items:start;">
      
      <!-- Left Column: Input Form -->
      <div>
        <h3 style="font-size:1.05rem; font-weight:700; margin-top:0; margin-bottom:16px; color:var(--slate-800); display:flex; align-items:center; gap:8px;">
          <span>1. Customer Verification</span>
        </h3>

        <form id="claim-verification-form" onsubmit="handleClaimSubmit(event)">
          <!-- Customer Phone Number -->
          <div class="form-group" style="margin-bottom:14px;">
            <label class="form-label" style="font-weight:700;">Customer Mobile / Phone <span style="color:red;">*</span></label>
            <div style="position:relative;">
              <input type="tel" id="claim_phone" class="form-control" required placeholder="e.g. 9876543210" style="padding-left:42px; font-weight:600; font-size:0.95rem;">
              <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); font-weight:700; color:var(--slate-400); font-size:0.85rem;">+91</span>
            </div>
            <small style="color:var(--slate-500); font-size:0.75rem;">Single-use discount is tied to this phone number.</small>
          </div>

          <!-- Voucher Unique Code -->
          <div class="form-group" style="margin-bottom:14px;">
            <label class="form-label" style="font-weight:700;">Voucher Unique Code <span style="color:red;">*</span></label>
            <input type="text" id="claim_code" class="form-control" required placeholder="e.g. FASHION-15-X7K" value="{{ $code }}" style="font-family:monospace; text-transform:uppercase; font-size:1rem; font-weight:700; color:#4f46e5; letter-spacing:1px;" oninput="onCodeInput()">
          </div>

          <!-- Purchase Bill Amount -->
          <div class="form-group" style="margin-bottom:20px;">
            <label class="form-label" style="font-weight:700;">Order Bill Amount (₹)</label>
            <input type="number" id="claim_bill" class="form-control" value="3500.00" step="0.01" style="font-weight:700; font-size:1rem;" oninput="calculateRedemptionPreview()">
          </div>

          <div style="display:flex; flex-direction:column; gap:10px;">
            <button type="submit" id="redeem-btn" class="btn btn-primary" style="width:100%; padding:12px; font-weight:700; font-size:1rem; display:inline-flex; align-items:center; justify-content:center; gap:8px;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              Verify & Redeem Single-Use Discount
            </button>
          </div>
        </form>
      </div>

      <!-- Right Column: QR Image Upload & Scanner -->
      <div>
        <h3 style="font-size:1.05rem; font-weight:700; margin-top:0; margin-bottom:16px; color:var(--slate-800); display:flex; align-items:center; gap:8px;">
          <span>2. Scan / Upload QR Voucher</span>
        </h3>

        <!-- Drag & Drop / File Upload Box -->
        <div id="drop-zone" style="border:2px dashed #cbd5e1; border-radius:14px; padding:24px 16px; text-align:center; background:#f8fafc; cursor:pointer; transition:all 0.2s;" onclick="document.getElementById('qr-file-input').click()">
          <input type="file" id="qr-file-input" accept="image/*" style="display:none;" onchange="handleQrFileUpload(event)">
          
          <div style="width:48px; height:48px; border-radius:12px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; margin:0 auto 10px;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
          </div>
          <div style="font-weight:700; color:var(--slate-800); font-size:0.925rem;">Upload QR Voucher Image</div>
          <div style="font-size:0.8rem; color:var(--slate-500); margin-top:4px;">Drag & drop image here or click to browse</div>
        </div>

        <!-- Camera Scanner Launcher -->
        <div style="margin-top:14px; text-align:center;">
          <button type="button" class="btn btn-secondary btn-sm" onclick="startCameraScanner()" style="width:100%; display:inline-flex; align-items:center; justify-content:center; gap:6px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
            Launch Camera Scanner
          </button>
        </div>

        <!-- Live Camera Video Element (Hidden by default) -->
        <div id="camera-container" style="display:none; margin-top:14px; position:relative; border-radius:12px; overflow:hidden; border:2px solid #4f46e5;">
          <video id="camera-preview" style="width:100%; height:200px; object-fit:cover; background:#000;"></video>
          <canvas id="qr-canvas" style="display:none;"></canvas>
          <button type="button" onclick="stopCameraScanner()" class="btn btn-danger btn-xs" style="position:absolute; top:8px; right:8px;">Close Camera</button>
        </div>

      </div>

    </div>

    <!-- Live Calculation & Response Status Banner -->
    <div id="redemption-result-card" style="display:none; margin-top:24px; border-radius:14px; padding:20px; transition:all 0.3s;">
      <!-- Populated via JavaScript on validation/redemption -->
    </div>

  </div>

</div>

@push('scripts')
<script>
  let validatedVoucher = null;
  let videoStream = null;
  let animationFrameId = null;

  function onCodeInput() {
    const code = document.getElementById('claim_code').value.trim();
    if (code.length >= 6) {
      checkVoucherStatus(code);
    }
  }

  async function checkVoucherStatus(code) {
    try {
      const res = await fetch('{{ route('qr.validate') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        },
        body: JSON.stringify({ voucher_code: code })
      });

      const data = await res.json();
      if (data.success) {
        validatedVoucher = data.voucher;
        calculateRedemptionPreview();
      } else {
        showResultBanner(false, data.message || 'Voucher cannot be redeemed.');
      }
    } catch(err) {
      console.error(err);
    }
  }

  function calculateRedemptionPreview() {
    if (!validatedVoucher) return;
    const bill = parseFloat(document.getElementById('claim_bill').value) || 0;
    let disc = 0;

    if (validatedVoucher.discount_type === 'Percentage') {
      disc = (bill * parseFloat(validatedVoucher.discount_percent)) / 100;
      if (validatedVoucher.max_discount_cap && disc > validatedVoucher.max_discount_cap) {
        disc = parseFloat(validatedVoucher.max_discount_cap);
      }
    } else {
      disc = parseFloat(validatedVoucher.discount_amount || validatedVoucher.discount_percent || 500);
    }

    const finalPay = Math.max(0, bill - disc);

    showResultBanner(true, `
      <div style="display:flex; align-items:center; gap:12px;">
        <div style="width:40px; height:40px; border-radius:50%; background:#ecfdf5; color:#059669; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div>
          <div style="font-weight:800; font-size:1.1rem; color:#065f46;">Valid Single-Use Voucher: ${validatedVoucher.voucher_code}</div>
          <div style="font-size:0.85rem; color:#047857;">Original Bill: ₹${bill.toFixed(2)} | Discount Applied: -₹${disc.toFixed(2)} | <strong>Final Payable: ₹${finalPay.toFixed(2)}</strong></div>
        </div>
      </div>
    `);
  }

  async function handleClaimSubmit(e) {
    e.preventDefault();
    const phone = document.getElementById('claim_phone').value.trim();
    const code = document.getElementById('claim_code').value.trim();
    const bill = document.getElementById('claim_bill').value.trim();

    if (!phone || !code) {
      alert('Please provide both phone number and voucher code.');
      return;
    }

    const btn = document.getElementById('redeem-btn');
    btn.disabled = true;
    btn.innerText = 'Processing Claim...';

    try {
      const res = await fetch('{{ route('qr.redeem') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          voucher_code: code,
          customer_phone: phone,
          order_bill: bill
        })
      });

      const data = await res.json();
      btn.disabled = false;
      btn.innerText = 'Verify & Redeem Single-Use Discount';

      if (data.success) {
        showResultBanner(true, `
          <div style="background:#ecfdf5; border:2px solid #059669; border-radius:12px; padding:18px; color:#065f46;">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              <h4 style="margin:0; font-size:1.15rem; font-weight:800;">🎉 Discount Claim Approved & Expired!</h4>
            </div>
            <p style="margin:4px 0 12px; font-size:0.9rem;">
              Claim Receipt: <strong>${data.claim_id}</strong> | Phone: <strong>${data.phone}</strong> | Saved: <strong>₹${Number(data.discount_value).toFixed(2)}</strong>
            </p>
            <div style="font-size:0.8rem; background:rgba(0,0,0,0.05); padding:8px 12px; border-radius:8px;">
              🔒 <strong>Single-Use Security Policy:</strong> This voucher code is now permanently redeemed and locked. It cannot be used again.
            </div>
          </div>
        `);
      } else {
        showResultBanner(false, data.message || 'Redemption failed.');
      }
    } catch(err) {
      btn.disabled = false;
      btn.innerText = 'Verify & Redeem Single-Use Discount';
      showResultBanner(false, 'Network error while contacting redemption server.');
    }
  }

  function showResultBanner(isSuccess, htmlContent) {
    const card = document.getElementById('redemption-result-card');
    card.style.display = 'block';
    if (isSuccess) {
      card.style.background = '#ecfdf5';
      card.style.border = '1px solid #a7f3d0';
      card.style.color = '#065f46';
    } else {
      card.style.background = '#fef2f2';
      card.style.border = '1px solid #fecaca';
      card.style.color = '#991b1b';
    }
    card.innerHTML = typeof htmlContent === 'string' && htmlContent.startsWith('<') ? htmlContent : `<div style="font-weight:700;">${htmlContent}</div>`;
  }

  // QR Image File Decoder using jsQR
  function handleQrFileUpload(e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function() {
      const img = new Image();
      img.onload = function() {
        const canvas = document.createElement('canvas');
        canvas.width = img.width;
        canvas.height = img.height;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0);
        const imgData = ctx.getImageData(0, 0, img.width, img.height);

        if (typeof jsQR !== 'undefined') {
          const qrCode = jsQR(imgData.data, imgData.width, imgData.height);
          if (qrCode && qrCode.data) {
            let extractedCode = qrCode.data;
            try {
              const parsed = JSON.parse(qrCode.data);
              if (parsed.code) extractedCode = parsed.code;
            } catch(ex) {}

            document.getElementById('claim_code').value = extractedCode;
            checkVoucherStatus(extractedCode);
            UI.showToast('QR Scanned', `Decoded voucher: ${extractedCode}`, 'success');
          } else {
            alert('Could not decode a valid QR barcode from this image. Please try a clearer picture or type the code.');
          }
        } else {
          // Fallback code fill
          document.getElementById('claim_code').value = 'FASHION-15-CLAIM';
          checkVoucherStatus('FASHION-15-CLAIM');
        }
      };
      img.src = reader.result;
    };
    reader.readAsDataURL(file);
  }

  // Web Camera Live Scanner
  function startCameraScanner() {
    const container = document.getElementById('camera-container');
    const video = document.getElementById('camera-preview');
    container.style.display = 'block';

    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
      .then(stream => {
        videoStream = stream;
        video.srcObject = stream;
        video.setAttribute('playsinline', true);
        video.play();
        animationFrameId = requestAnimationFrame(scanCameraFrame);
      })
      .catch(err => {
        alert('Camera access denied or unavailable: ' + err.message);
        container.style.display = 'none';
      });
  }

  function stopCameraScanner() {
    if (videoStream) {
      videoStream.getTracks().forEach(t => t.stop());
      videoStream = null;
    }
    if (animationFrameId) cancelAnimationFrame(animationFrameId);
    document.getElementById('camera-container').style.display = 'none';
  }

  function scanCameraFrame() {
    const video = document.getElementById('camera-preview');
    const canvas = document.getElementById('qr-canvas');
    if (video.readyState === video.HAVE_ENOUGH_DATA) {
      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      const ctx = canvas.getContext('2d');
      ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
      const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);

      if (typeof jsQR !== 'undefined') {
        const qrCode = jsQR(imgData.data, imgData.width, imgData.height);
        if (qrCode && qrCode.data) {
          stopCameraScanner();
          let code = qrCode.data;
          try {
            const parsed = JSON.parse(qrCode.data);
            if (parsed.code) code = parsed.code;
          } catch(e) {}
          document.getElementById('claim_code').value = code;
          checkVoucherStatus(code);
          UI.showToast('QR Code Scanned', code, 'success');
          return;
        }
      }
    }
    animationFrameId = requestAnimationFrame(scanCameraFrame);
  }

  document.addEventListener('DOMContentLoaded', () => {
    const code = document.getElementById('claim_code').value;
    if (code) checkVoucherStatus(code);
  });
</script>
@endpush
@endsection
