/* ==========================================================================
   INTERACTIVE SVG CHARTS ENGINE - PRODUCTION, DONUT & STOCK VISUALIZERS
   GarmentERP
   ========================================================================== */

const ERPCharts = {
  // 1. Production Overview 7-Day Multi-Bar Chart
  renderProductionChart(containerId) {
    const el = document.getElementById(containerId);
    if (!el) return;

    const days = ["07 Aug", "08 Aug", "09 Aug", "10 Aug", "11 Aug", "12 Aug", "13 Aug"];
    const series = [
      { label: "Orders", values: [4200, 3800, 5200, 4800, 5000, 6100, 5000], color: "#3b82f6" },
      { label: "Production", values: [3900, 3500, 4900, 4600, 4850, 5800, 4850], color: "#8b5cf6" },
      { label: "Completed", values: [3600, 3400, 4600, 4400, 4650, 5400, 4550], color: "#10b981" },
      { label: "Dispatched", values: [3200, 3000, 4100, 4000, 4200, 4800, 4200], color: "#f59e0b" }
    ];

    const maxVal = 7000;
    const chartHeight = 180;
    const chartWidth = 540;

    let barsHtml = "";
    const groupWidth = chartWidth / days.length;

    days.forEach((day, dIdx) => {
      const groupX = dIdx * groupWidth + 30;
      series.forEach((s, sIdx) => {
        const barH = (s.values[dIdx] / maxVal) * chartHeight;
        const barY = chartHeight - barH + 20;
        const barX = groupX + (sIdx * 10);
        barsHtml += `
          <rect x="${barX}" y="${barY}" width="8" height="${barH}" rx="3" fill="${s.color}" opacity="0.9">
            <title>${day} - ${s.label}: ${s.values[dIdx].toLocaleString('en-IN')} units</title>
          </rect>
        `;
      });

      barsHtml += `
        <text x="${groupX + 18}" y="${chartHeight + 38}" font-size="11" fill="#64748b" text-anchor="middle" font-family="var(--font-sans)">${day}</text>
      `;
    });

    el.innerHTML = `
      <div style="width:100%; height:100%; display:flex; flex-direction:column; justify-content:space-between;">
        <svg viewBox="0 0 580 230" style="width:100%; height:220px; overflow:visible;">
          <!-- Grid Lines -->
          <line x1="20" y1="20" x2="570" y2="20" stroke="#f1f5f9" stroke-width="1" />
          <line x1="20" y1="80" x2="570" y2="80" stroke="#f1f5f9" stroke-width="1" />
          <line x1="20" y1="140" x2="570" y2="140" stroke="#f1f5f9" stroke-width="1" />
          <line x1="20" y1="200" x2="570" y2="200" stroke="#e2e8f0" stroke-width="1" />
          
          <text x="15" y="24" font-size="9" fill="#94a3b8" text-anchor="end">7K</text>
          <text x="15" y="84" font-size="9" fill="#94a3b8" text-anchor="end">5K</text>
          <text x="15" y="144" font-size="9" fill="#94a3b8" text-anchor="end">2.5K</text>
          <text x="15" y="204" font-size="9" fill="#94a3b8" text-anchor="end">0</text>

          ${barsHtml}
        </svg>
        <div style="display:flex; justify-content:center; gap:16px; margin-top:8px; font-size:0.75rem;">
          ${series.map(s => `
            <div style="display:flex; align-items:center; gap:6px;">
              <span style="width:10px; height:10px; background:${s.color}; border-radius:2px;"></span>
              <span style="color:var(--slate-600); font-weight:600;">${s.label}</span>
            </div>
          `).join('')}
        </div>
      </div>
    `;
  },

  // 2. Order Status Donut Chart
  renderOrderStatusDonut(containerId) {
    const el = document.getElementById(containerId);
    if (!el) return;

    const segments = [
      { label: "In Production", count: 42, color: "#3b82f6" },
      { label: "Pending", count: 18, color: "#f59e0b" },
      { label: "Ready", count: 26, color: "#10b981" },
      { label: "Dispatched", count: 38, color: "#8b5cf6" },
      { label: "Cancelled", count: 4, color: "#ef4444" }
    ];

    const total = segments.reduce((acc, s) => acc + s.count, 0);
    const radius = 65;
    const circumference = 2 * Math.PI * radius;
    let accumulatedAngle = 0;

    let circlesSvg = "";
    segments.forEach(s => {
      const strokeDasharray = `${(s.count / total) * circumference} ${circumference}`;
      const strokeDashoffset = -accumulatedAngle;
      accumulatedAngle += (s.count / total) * circumference;

      circlesSvg += `
        <circle cx="90" cy="90" r="${radius}" fill="transparent"
          stroke="${s.color}" stroke-width="24"
          stroke-dasharray="${strokeDasharray}"
          stroke-dashoffset="${strokeDashoffset}"
          stroke-linecap="round"
          style="transition: all 0.5s ease;">
          <title>${s.label}: ${s.count} orders (${Math.round((s.count/total)*100)}%)</title>
        </circle>
      `;
    });

    el.innerHTML = `
      <div style="display:flex; align-items:center; justify-content:space-around; width:100%; height:100%;">
        <div style="position:relative; width:180px; height:180px;">
          <svg viewBox="0 0 180 180" style="transform: rotate(-90deg); width:180px; height:180px;">
            <circle cx="90" cy="90" r="${radius}" fill="transparent" stroke="#f1f5f9" stroke-width="24" />
            ${circlesSvg}
          </svg>
          <div style="position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center;">
            <span style="font-size:1.5rem; font-weight:800; color:var(--slate-900); line-height:1;">${total}</span>
            <span style="font-size:0.7rem; color:var(--slate-500); font-weight:600; text-transform:uppercase; margin-top:2px;">Orders</span>
          </div>
        </div>

        <div style="display:flex; flex-direction:column; gap:8px;">
          ${segments.map(s => `
            <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; font-size:0.775rem;">
              <div style="display:flex; align-items:center; gap:6px;">
                <span style="width:8px; height:8px; border-radius:50%; background:${s.color};"></span>
                <span style="color:var(--slate-700); font-weight:500;">${s.label}</span>
              </div>
              <span style="font-weight:700; color:var(--slate-900);">${s.count}</span>
            </div>
          `).join('')}
        </div>
      </div>
    `;
  },

  // 3. Stock Distribution Visualizer
  renderStockOverview(containerId) {
    const el = document.getElementById(containerId);
    if (!el) return;

    const stats = ERPState.getDashboardStats() || {};
    const rawStock = Number(stats.rawStock) || 0;
    const wipStock = Number(stats.wipStock) || 0;
    const finishedStock = Number(stats.finishedStock) || 0;
    const total = rawStock + wipStock + finishedStock || 1;

    const rawPct = Math.round((rawStock / total) * 100);
    const wipPct = Math.round((wipStock / total) * 100);
    const finPct = Math.round((finishedStock / total) * 100);

    el.innerHTML = `
      <div style="display:flex; flex-direction:column; gap:16px; width:100%;">
        <!-- Multi-segment progress bar -->
        <div style="height:14px; border-radius:var(--radius-full); background:var(--slate-200); display:flex; overflow:hidden;">
          <div style="width:${rawPct}%; background:#3b82f6;" title="Raw Materials: ${rawStock.toLocaleString('en-IN')} units"></div>
          <div style="width:${wipPct}%; background:#f59e0b;" title="WIP Stock: ${wipStock.toLocaleString('en-IN')} units"></div>
          <div style="width:${finPct}%; background:#10b981;" title="Finished Goods: ${finishedStock.toLocaleString('en-IN')} units"></div>
        </div>

        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:12px;">
          <div style="padding:12px; background:var(--primary-50); border:1px solid var(--primary-100); border-radius:var(--radius-md);">
            <div style="font-size:0.725rem; font-weight:700; color:var(--primary-700); text-transform:uppercase;">Raw Material</div>
            <div style="font-size:1.15rem; font-weight:800; color:var(--primary-900); margin-top:2px;">${rawStock.toLocaleString('en-IN')} <span style="font-size:0.7rem; font-weight:normal;">units</span></div>
            <div style="font-size:0.7rem; color:var(--primary-600); margin-top:2px;">${rawPct}% of total inventory</div>
          </div>

          <div style="padding:12px; background:var(--warning-50); border:1px solid #fde68a; border-radius:var(--radius-md);">
            <div style="font-size:0.725rem; font-weight:700; color:var(--warning-700); text-transform:uppercase;">Work In Progress</div>
            <div style="font-size:1.15rem; font-weight:800; color:var(--warning-900); margin-top:2px;">${wipStock.toLocaleString('en-IN')} <span style="font-size:0.7rem; font-weight:normal;">units</span></div>
            <div style="font-size:0.7rem; color:var(--warning-600); margin-top:2px;">${wipPct}% in active job work</div>
          </div>

          <div style="padding:12px; background:var(--success-50); border:1px solid #bbf7d0; border-radius:var(--radius-md);">
            <div style="font-size:0.725rem; font-weight:700; color:var(--success-700); text-transform:uppercase;">Finished Goods</div>
            <div style="font-size:1.15rem; font-weight:800; color:var(--success-900); margin-top:2px;">${finishedStock.toLocaleString('en-IN')} <span style="font-size:0.7rem; font-weight:normal;">units</span></div>
            <div style="font-size:0.7rem; color:var(--success-600); margin-top:2px;">${finPct}% ready for shipping</div>
          </div>
        </div>
      </div>
    `;
  }
};
