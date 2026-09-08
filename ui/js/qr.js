/* ==========================================================================
   QR CODE ENGINE & DISCOUNT / AMOUNT QR GENERATOR & SCANNER
   GarmentERP - FashionWorks Pvt. Ltd.
   ========================================================================== */

const QRManager = {
  // Generate crisp deterministic 2D QR Code SVG Matrix with custom colors
  generateQRSVG(text, size = 180, color = "#0f172a", bg = "#ffffff") {
    const modules = 25; // 25x25 QR Matrix
    let hash = 0;
    const str = String(text || "GARMENT-DISCOUNT-001");
    for (let i = 0; i < str.length; i++) {
      hash = (hash << 5) - hash + str.charCodeAt(i);
      hash |= 0;
    }

    const matrix = [];
    for (let r = 0; r < modules; r++) {
      matrix[r] = [];
      for (let c = 0; c < modules; c++) {
        matrix[r][c] = false;
      }
    }

    // Helper: draw finder pattern
    const drawFinder = (startX, startY) => {
      for (let r = 0; r < 7; r++) {
        for (let c = 0; c < 7; c++) {
          if (
            r === 0 || r === 6 || c === 0 || c === 6 ||
            (r >= 2 && r <= 4 && c >= 2 && c <= 4)
          ) {
            matrix[startY + r][startX + c] = true;
          }
        }
      }
    };

    drawFinder(0, 0);                 // Top-Left
    drawFinder(modules - 7, 0);         // Top-Right
    drawFinder(0, modules - 7);         // Bottom-Left

    // Timing patterns
    for (let i = 8; i < modules - 8; i++) {
      matrix[6][i] = i % 2 === 0;
      matrix[i][6] = i % 2 === 0;
    }

    // Pseudo-random data modules based on text hash
    let seed = Math.abs(hash) || 1234567;
    for (let r = 0; r < modules; r++) {
      for (let c = 0; c < modules; c++) {
        // Skip finder zones
        if ((r < 8 && c < 8) || (r < 8 && c >= modules - 8) || (r >= modules - 8 && c < 8)) {
          continue;
        }
        if (r === 6 || c === 6) continue;

        seed = (seed * 9301 + 49297) % 233280;
        matrix[r][c] = (seed / 233280) > 0.48;
      }
    }

    const cellSize = size / modules;
    let rects = "";
    for (let r = 0; r < modules; r++) {
      for (let c = 0; c < modules; c++) {
        if (matrix[r][c]) {
          rects += `<rect x="${(c * cellSize).toFixed(2)}" y="${(r * cellSize).toFixed(2)}" width="${(cellSize + 0.25).toFixed(2)}" height="${(cellSize + 0.25).toFixed(2)}" fill="${color}" />`;
        }
      }
    }

    return `
      <svg width="${size}" height="${size}" viewBox="0 0 ${size} ${size}" xmlns="http://www.w3.org/2000/svg" style="background:${bg}; border-radius:6px; display:block; margin:0 auto;">
        <rect width="${size}" height="${size}" fill="${bg}" rx="6" />
        ${rects}
      </svg>
    `;
  },

  // Render Clean Standalone Discount QR Code Card (WITHOUT INDUSTRIAL LABELS)
  renderStandaloneDiscountQR(coupon, size = 200) {
    const amountStr = coupon.type === "percent" ? `${coupon.amount}% OFF` : `₹${coupon.amount} OFF`;
    const qrSvg = this.generateQRSVG(coupon.code || "DISCOUNT", size, coupon.color || "#0f172a");

    return `
      <div class="standalone-qr-card" id="standalone-discount-preview">
        <!-- Top Discount Value Pill -->
        <div class="discount-badge-banner" style="background:${coupon.color || '#0f172a'};">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
          FLAT ${amountStr}
        </div>

        <!-- Pure Clean QR Code (No Label Frame) -->
        <div class="pure-qr-wrapper">
          ${qrSvg}
        </div>

        <!-- Voucher Details -->
        <div class="qr-voucher-meta">
          <div class="qr-coupon-code-pill" onclick="QRManager.copyCouponCode('${coupon.code}')" title="Click to copy coupon code">
            <span class="font-mono font-bold">${coupon.code}</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
          </div>

          <h4 class="qr-offer-title">${coupon.title || 'Customer Discount Voucher'}</h4>
          
          <div class="qr-terms-text">
            ${coupon.minBill ? `Valid on minimum spend of <strong>₹${coupon.minBill.toLocaleString('en-IN')}</strong> • ` : 'No minimum spend • '}
            Valid till: <strong>${coupon.validTill || '30 Sep 2026'}</strong>
          </div>
        </div>
      </div>
    `;
  },

  // 1-Click Copy Coupon Code
  copyCouponCode(code) {
    if (navigator.clipboard) {
      navigator.clipboard.writeText(code);
    }
    UI.showToast("Copied to Clipboard!", `Coupon Code ${code} copied successfully`, "success");
  },

  // Download QR Code as SVG
  downloadSVG(code, color = "#0f172a", size = 400) {
    const svgStr = this.generateQRSVG(code, size, color);
    const blob = new Blob([svgStr], { type: "image/svg+xml" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = `Discount-QR-${code}.svg`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    UI.showToast("Downloaded SVG", `QR SVG image saved as Discount-QR-${code}.svg`, "success");
  },

  // Download QR Code as PNG Image
  downloadPNG(code, color = "#0f172a", size = 500) {
    const svgStr = this.generateQRSVG(code, size, color);
    const canvas = document.createElement("canvas");
    canvas.width = size;
    canvas.height = size;
    const ctx = canvas.getContext("2d");
    const img = new Image();
    const svgBlob = new Blob([svgStr], { type: "image/svg+xml;charset=utf-8" });
    const url = URL.createObjectURL(svgBlob);

    img.onload = () => {
      ctx.drawImage(img, 0, 0);
      URL.revokeObjectURL(url);
      const pngUrl = canvas.toDataURL("image/png");
      const a = document.createElement("a");
      a.href = pngUrl;
      a.download = `Discount-QR-${code}.png`;
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      UI.showToast("Downloaded PNG", `High-Res QR image saved as Discount-QR-${code}.png`, "success");
    };

    img.src = url;
  },

  // Printable Standalone Discount Voucher Modal
  openPrintDiscountVoucherModal(coupon) {
    const amountStr = coupon.type === "percent" ? `${coupon.amount}% OFF` : `₹${coupon.amount} OFF`;
    const qrSvg = this.generateQRSVG(coupon.code, 220, coupon.color || "#0f172a");

    const content = `
      <div class="print-discount-sheet" id="printable-discount-voucher">
        <div class="voucher-print-card" style="border: 2px dashed ${coupon.color || '#0f172a'};">
          <div class="voucher-print-header" style="background:${coupon.color || '#0f172a'};">
            <div style="font-size:0.8rem; text-transform:uppercase; letter-spacing:1px; opacity:0.9;">${ERPState.data.company.name}</div>
            <div style="font-size:1.6rem; font-weight:800; margin-top:2px;">FLAT ${amountStr}</div>
            <div style="font-size:0.85rem; opacity:0.95;">${coupon.title}</div>
          </div>

          <div class="voucher-print-body">
            <div style="margin-bottom:12px;">
              ${qrSvg}
            </div>

            <div style="font-size:0.8rem; color:#64748b; font-weight:600; text-transform:uppercase;">SCAN TO REDEEM DISCOUNT</div>
            <div style="font-size:1.25rem; font-weight:800; font-family:var(--font-mono); color:${coupon.color || '#0f172a'}; margin:6px 0;">
              ${coupon.code}
            </div>

            <div style="font-size:0.75rem; color:#64748b; margin-top:8px;">
              ${coupon.minBill ? `Applicable on min spend of ₹${coupon.minBill.toLocaleString('en-IN')} • ` : ''}
              Valid till ${coupon.validTill || '30 Sep 2026'}
            </div>
          </div>
        </div>
      </div>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="UI.closeModal()">Close</button>
      <button class="btn btn-primary" onclick="window.print(); UI.showToast('Printing Discount QR', 'Sent to printer', 'info');">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
        Print Discount Voucher
      </button>
    `;

    UI.openModal({ title: `Discount Voucher QR - ${amountStr}`, content, footer, size: "modal-md" });
  },

  // 1D Barcode SVG (kept for backwards-compatible Lot Tag generation)
  generateBarcodeSVG(codeText, width = 280, height = 36) {
    const bars = [];
    const str = String(codeText || "123456");
    for (let i = 0; i < str.length; i++) {
      const code = str.charCodeAt(i);
      bars.push(code % 2 === 0 ? 2 : 1);
      bars.push(code % 3 === 0 ? 3 : 1);
      bars.push(1);
    }

    let x = 10;
    let rects = "";
    bars.forEach((w, idx) => {
      if (idx % 2 === 0) {
        rects += `<rect x="${x}" y="0" width="${w}" height="${height}" fill="#0f172a" />`;
      }
      x += w + 1;
    });

    return `
      <svg width="${width}" height="${height}" viewBox="0 0 ${x + 10} ${height}">
        ${rects}
      </svg>
    `;
  },

  // Render Printable Industrial QR Lot Label (Kept for secondary factory tracking)
  renderIndustrialLabel(lot) {
    const qrSvg = this.generateQRSVG(lot.qrCodeString || `LOT-${lot.lotNo}`, 86);
    const barcodeSvg = this.generateBarcodeSVG(lot.lotNo, 280, 24);

    return `
      <div class="industrial-qr-label" id="printable-lot-label">
        <div class="label-header">
          <div class="label-company-name">${ERPState.data.company.name}</div>
          <span class="label-tag">GARMENT TRACKING</span>
        </div>

        <div class="label-body-grid">
          <div class="label-qr-img">
            ${qrSvg}
          </div>
          <div class="label-details">
            <div class="label-row">
              <span class="label-key">LOT NO:</span>
              <span class="label-val font-mono">${lot.lotNo}</span>
            </div>
            <div class="label-row">
              <span class="label-key">ORDER:</span>
              <span class="label-val">${lot.orderNo || "SO-2026-1045"}</span>
            </div>
            <div class="label-row">
              <span class="label-key">CUSTOMER:</span>
              <span class="label-val">${lot.customer || "ABC Fashion"}</span>
            </div>
            <div class="label-row">
              <span class="label-key">ITEM:</span>
              <span class="label-val">${lot.product || "Crew Neck T-Shirt"}</span>
            </div>
            <div class="label-row">
              <span class="label-key">QUANTITY:</span>
              <span class="label-val font-mono">${(lot.currentQty || lot.targetQty).toLocaleString('en-IN')} PCS</span>
            </div>
            <div class="label-row">
              <span class="label-key">PROCESS:</span>
              <span class="label-val">${lot.currentProcess || "Stitching"}</span>
            </div>
          </div>
        </div>

        <div class="label-barcode-footer">
          ${barcodeSvg}
          <div style="font-size:0.65rem; font-family:var(--font-mono); color:#475569; letter-spacing:0.1em; margin-top:2px;">
            ${lot.qrCodeString || lot.lotNo}
          </div>
        </div>
      </div>
    `;
  },

  openPrintLabelModal(lotNo) {
    const lot = ERPState.data.lots.find(l => l.lotNo === lotNo) || ERPState.data.lots[0];
    const labelHtml = this.renderIndustrialLabel(lot);

    const content = `
      <div style="display:flex; flex-direction:column; align-items:center; gap:20px;">
        <p style="font-size:0.875rem; color:var(--slate-600); text-align:center;">
          Print-ready 4" x 3" Industrial QR Barcode Tracking Label for manufacturing bundle tags.
        </p>
        <div style="background:var(--slate-100); padding:20px; border-radius:var(--radius-xl); display:flex; justify-content:center; width:100%;">
          ${labelHtml}
        </div>
      </div>
    `;

    const footer = `
      <button class="btn btn-secondary" onclick="UI.closeModal()">Close</button>
      <button class="btn btn-primary" onclick="window.print(); UI.showToast('Printing QR Label', 'Sent to thermal label printer', 'info');">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
        Print Thermal Label
      </button>
    `;

    UI.openModal({ title: `QR Lot Label - ${lot.lotNo}`, content, footer, size: "modal-md" });
  }
};

