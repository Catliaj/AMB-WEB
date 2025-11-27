<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Generate Reports | Admin Dashboard</title>

  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

  <link rel="stylesheet" href="<?= base_url('assets/styles/admin-style.css')?>">
  <link rel="stylesheet" href="<?= base_url('client/clientstyle.css')?>">
  <style>
    :root {
      --bg: #f6f8fb;
      --card: #ffffff;
      --text: #1f2937;
      --muted: #6b7280;
      --accent1: #4e9eff;
      --accent2: #2a405a;
      --accent3: #68b76b;
      --shadow: 0 6px 18px rgba(20,25,30,0.06);
      --divider: #e6e9ef;
      --hover-overlay: rgba(0,0,0,0.04);
    }
    /* Sidebar and table header overrides to use the light palette */
    .sidebar { background: linear-gradient(120deg, #d3f0ff 0%, #c8f5d2 100%); border-color: var(--divider); }
    html[data-theme="dark"] .sidebar { background: linear-gradient(120deg, #252e42 0%, #2d4038 100%); }
    .nav a { color: var(--text); }
    thead { background: var(--th-bg, var(--card)); }
    th { color: var(--th-text, var(--text)); }
  </style>
  
</head>

  <script>
    (function(){
      try {
        var t = localStorage.getItem('adm_theme_pref');
        if (t === 'light') document.documentElement.setAttribute('data-theme','light');
        else if (t === 'dark') document.documentElement.removeAttribute('data-theme');
        else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) document.documentElement.setAttribute('data-theme','light');
      } catch(e){}
    })();
  </script>

</head>

<body>
  <aside class="sidebar">
     <img src="<?= base_url('assets/img/amb_logo.png')?>" alt="AMB Logo">
    <nav class="nav">
      <a href="/admin/adminHomepage"><i data-lucide="layout-dashboard"></i> Dashboard</a>
      <a href="/admin/manageUsers"><i data-lucide="users"></i> Manage Users</a>
      <a href="/admin/ManageProperties"><i data-lucide="home"></i> Manage Properties</a>
      <!-- User Bookings removed -->
      <!-- View Chats removed for privacy -->
      <a href="/admin/Reports" class="active" style="background: linear-gradient(90deg, #2e7d32, #1565c0);"><i data-lucide="bar-chart-2"></i> Generate Reports</a>
    </nav>

    <div class="profile-box">
      <div class="profile-avatar">A</div>
      <div class="profile-info">
        <strong><?= session('FirstName') . ' ' . session('LastName'); ?></strong>
        <span><?= session('inputEmail'); ?></span>
      </div>
    </div>
  </aside>

  <main class="main">
    <header>
      <div class="left-header">
        <button id="toggleSidebar" class="btn"><i data-lucide="menu"></i></button>
        <h1><i data-lucide="bar-chart-2"></i> Generate Reports</h1>
      </div>
    </header>

    <div class="filters">
      <input type="date" id="startDate">
      <input type="date" id="endDate">
      <select id="propertyFilter">
        <option>All Properties</option>
        <option>Apartment</option>
        <option>Condo</option>
        <option>House</option>
      </select>
      <select id="agentFilter">
        <option>All Agents</option>
        <option>Agent A</option>
        <option>Agent B</option>
        <option>Agent C</option>
      </select>
      <select id="bookingStatus">
        <option>All Status</option>
        <option>Pending</option>
        <option>Approved</option>
        <option>Rejected</option>
      </select>
    </div>
<div class="header-actions" style="display: flex; gap: 10px;">
          <button class="btn" id="generateReportBtn" style="background: linear-gradient(90deg, #2e7d32, #1565c0); color: white; border: none;"><i data-lucide="refresh-ccw"></i> Generate</button>
          <button class="btn" id="exportPdf" style="background: linear-gradient(90deg, #2e7d32, #1565c0); color: white; border: none;"><i data-lucide="file-down"></i> PDF</button>
          <button class="btn" id="exportExcel" style="background: linear-gradient(90deg, #2e7d32, #1565c0); color: white; border: none;"><i data-lucide="file-spreadsheet"></i> Excel</button>
        </div>

    <section class="summary-wrapper">
      <h2>Summary</h2>
      <p class="summary-note">Overview of key metrics for the selected filters and date range.</p>
      <div class="summary-cards">
        <div class="summary-card">
          <h2><span class="icon"><i data-lucide="dollar-sign"></i></span> Total Sales</h2>
          <div class="value" id="totalSales">₱1,250,000</div>
          <p class="stat-desc">Sum of all confirmed sales in the selected period.</p>
        </div>
        <div class="summary-card">
          <h2><span class="icon"><i data-lucide="home"></i></span> Total Properties</h2>
          <div class="value" id="totalProperties">128</div>
          <p class="stat-desc">Number of properties available or listed in the selected filters.</p>
        </div>
        <div class="summary-card">
          <h2><span class="icon"><i data-lucide="users"></i></span> Active Users</h2>
          <div class="value" id="activeUsers">86</div>
          <p class="stat-desc">Users who have an active booking or activity within the date range.</p>
        </div>
      </div>
    </section>

    <div class="report-layout">
      <div class="report-preview">
        <h3>Report Preview</h3>
        <table id="reportTable">
          <thead style="background: white;">
            <tr>
              <th>Report ID</th>
              <th>Property</th>
              <th>Agent</th>
              <th>Status</th>
              <th>Sales</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody id="reportTableBody"></tbody>
        </table>
      </div>

      <div class="chart-section">
          <div class="chart-card">
            <h3><button class="drag-handle" aria-label="Drag to reorder" title="Drag to reorder"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M7 9h2M7 13h2M11 9h2M11 13h2M15 9h2M15 13h2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button><i data-lucide="trending-up"></i> Booking Rates Over Time</h3>
            <div class="chart-container resizable" data-chart="bookingTrendsChart">
              <canvas id="bookingTrendsChart"></canvas>
              <button class="resizer" aria-label="Resize chart" title="Resize"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M16 16l4 4M11 16l9 0M6 16l10 0" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
            </div>
          </div>
          <div class="chart-card">
            <h3><button class="drag-handle" aria-label="Drag to reorder" title="Drag to reorder"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M7 9h2M7 13h2M11 9h2M11 13h2M15 9h2M15 13h2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button><i data-lucide="home"></i> Property Sales by Type</h3>
            <div class="chart-container resizable" data-chart="propertySalesChart">
              <canvas id="propertySalesChart"></canvas>
              <button class="resizer" aria-label="Resize chart" title="Resize"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M16 16l4 4M11 16l9 0M6 16l10 0" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
            </div>
          </div>
          <div class="chart-card">
            <h3><button class="drag-handle" aria-label="Drag to reorder" title="Drag to reorder"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M7 9h2M7 13h2M11 9h2M11 13h2M15 9h2M15 13h2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button><i data-lucide="user-check"></i> Agent Performance Summary</h3>
            <div class="chart-container resizable" data-chart="agentPerformanceChart">
              <canvas id="agentPerformanceChart"></canvas>
              <button class="resizer" aria-label="Resize chart" title="Resize"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M16 16l4 4M11 16l9 0M6 16l10 0" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
            </div>
          </div>
      </div>
    </div>
  </main>







  <script>
    lucide.createIcons();

    let reportData = [];
    const REPORTS_DATA_URL = '<?= base_url('admin/reports/data') ?>';
    const REPORTS_CSV_URL = '<?= base_url('admin/reports/export.csv') ?>';
    const REPORTS_PDF_URL = '<?= base_url('admin/reports/export.pdf') ?>';

    function populateTable(data) {
      const tbody = document.getElementById("reportTableBody");
      tbody.innerHTML = "";
      data.forEach(row => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${row.bookingID ?? ''}</td>
          <td>${row.PropertyTitle ?? ''}</td>
          <td>${row.AgentName ?? ''}</td>
          <td>${row.BookingStatus ?? ''}</td>
          <td>₱${(Number(row.Sales) || 0).toLocaleString()}</td>
          <td>${row.bookingDate ?? ''}</td>
        `;
        tbody.appendChild(tr);
      });
    }

    function updateSummaryMetrics(filtered) {
      // Total sales: strictly include rows where status is 'Confirmed' and reason is 'Reserved'.
      const total = filtered.reduce((sum, r) => {
        const status = String(r.BookingStatus || r.Status || '').toLowerCase();
        const reason = String(r.Reason || '').toLowerCase();
        const include = (status === 'confirmed' && reason === 'reserved');
        return sum + (include ? (Number(r.Sales) || 0) : 0);
      }, 0);
      const totalEl = document.getElementById('totalSales');
      if (totalEl) totalEl.textContent = '₱' + total.toLocaleString();

      // Total unique properties
      const props = new Set(filtered.map(r => r.PropertyTitle || r.PropertyID || ''));
      const propEl = document.getElementById('totalProperties');
      if (propEl) propEl.textContent = props.size;

      // Active users — derive from unique agents involved in filtered results
      const agents = new Set(filtered.map(r => r.AgentName || ''));
      const actEl = document.getElementById('activeUsers');
      if (actEl) actEl.textContent = agents.size;
    }

    function filterReports() {
      const property = document.getElementById("propertyFilter").value;
      const agent = document.getElementById("agentFilter").value;
      const status = document.getElementById("bookingStatus").value;
      const start = document.getElementById("startDate").value;
      const end = document.getElementById("endDate").value;

      return reportData.filter(r => {
        const matchProperty = property === "All Properties" || (r.PropertyTitle && r.PropertyTitle.includes(property)) || (r.Property_Type && r.Property_Type.includes(property));
        const matchAgent = agent === "All Agents" || (r.AgentName && r.AgentName === agent);
        const matchStatus = status === "All Status" || (r.BookingStatus && r.BookingStatus === status);
        const matchDate = (!start || (r.bookingDate && r.bookingDate >= start)) && (!end || (r.bookingDate && r.bookingDate <= end));
        return matchProperty && matchAgent && matchStatus && matchDate;
      });
    }

    function updateCharts(filtered) {
      const salesByType = { Apartment: 0, Condo: 0, House: 0 };
      filtered.forEach(r => {
        const title = (r.PropertyTitle || '').toLowerCase();
        const sales = Number(r.Sales) || 0;
        if (title.includes('condo')) salesByType.Condo += sales;
        else if (title.includes('apartment')) salesByType.Apartment += sales;
        else salesByType.House += sales;
      });
      // Convert aggregated sales into scatter data points with x indices matching propertyTypeLabels
      const salesPoints = [
        { x: 0, y: salesByType.Apartment },
        { x: 1, y: salesByType.Condo },
        { x: 2, y: salesByType.House }
      ];
      propertySalesChart.data.datasets[0].data = salesPoints;
      propertySalesChart.update();

      // Agent performance: build dynamic labels and data
      const agentMap = {};
      filtered.forEach(r => {
        const name = r.AgentName || 'Unassigned';
        const sales = Number(r.Sales) || 0;
        agentMap[name] = (agentMap[name] || 0) + sales;
      });
      const labels = Object.keys(agentMap);
      const data = Object.values(agentMap);
      agentPerformanceChart.data.labels = labels.length ? labels : ['No Data'];
      agentPerformanceChart.data.datasets[0].data = data.length ? data : [0];
      agentPerformanceChart.update();
    }

    // Compute monthly booking counts (Jan..Dec) from filtered report rows and update bookingTrendsChart
    function updateBookingTrends(filtered) {
      const counts = Array(12).fill(0);
      const currentYear = new Date().getFullYear();
      filtered.forEach(r => {
        const dstr = r.bookingDate || r.BookingDate || r.date || '';
        if (!dstr) return;
        const dt = new Date(dstr);
        if (isNaN(dt)) {
          // try parsing as YYYY-MM-DD HH:MM:SS
          const parsed = Date.parse(dstr);
          if (isNaN(parsed)) return;
          dt = new Date(parsed);
        }
        if (dt.getFullYear() === currentYear) {
          counts[dt.getMonth()] += 1;
        }
      });
      bookingTrendsChart.data.labels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
      bookingTrendsChart.data.datasets[0].data = counts;
      bookingTrendsChart.update();
    }

    async function fetchReports() {
      const qs = buildFilterQuery();
      const url = REPORTS_DATA_URL + (qs ? ('?' + qs) : '');
      try {
        const res = await fetch(url, { credentials: 'same-origin' });
        if (!res.ok) throw new Error('Failed to fetch reports');
        const data = await res.json();
        reportData = data;
        const filtered = filterReports();
            populateTable(filtered);
            updateCharts(filtered);
            updateBookingTrends(filtered);
            updateSummaryMetrics(filtered);
      } catch (err) {
        console.error('reports fetch error', err);
        alert('Failed to load reports.');
      }
    }

    document.getElementById("generateReportBtn").addEventListener("click", fetchReports);

    function buildFilterQuery() {
      const params = new URLSearchParams();
      const start = document.getElementById("startDate").value;
      const end = document.getElementById("endDate").value;
      const property = document.getElementById("propertyFilter").value;
      const agent = document.getElementById("agentFilter").value;
      const status = document.getElementById("bookingStatus").value;
      if (start) params.set('startDate', start);
      if (end) params.set('endDate', end);
      if (property) params.set('property', property);
      if (agent) params.set('agent', agent);
      if (status) params.set('status', status);
      return params.toString();
    }

    document.getElementById("exportExcel").addEventListener("click", () => {
      // Call server-side CSV generator and trigger download
      const qs = buildFilterQuery();
      const url = REPORTS_CSV_URL + (qs ? ('?' + qs) : '');
      window.open(url, '_blank');
    });

    document.getElementById("exportPdf").addEventListener("click", () => {
      const qs = buildFilterQuery();
      const url = REPORTS_PDF_URL + (qs ? ('?' + qs) : '');
      window.open(url, '_blank');
    });

    const chartColors = {
      accent1: getComputedStyle(document.documentElement).getPropertyValue('--accent1').trim() || '#4e9eff',
      accent2: getComputedStyle(document.documentElement).getPropertyValue('--accent2').trim() || '#4effa1',
      accent3: getComputedStyle(document.documentElement).getPropertyValue('--accent3').trim() || '#ffb84e',
      text: '#fff',
      muted: '#aaa'
    };

    const bookingTrendsCanvas = document.getElementById('bookingTrendsChart');
    bookingTrendsCanvas.style.width = '100%'; bookingTrendsCanvas.style.height = '100%';
    // Initialize bookings-over-time chart with 12 months (counts start at 0)
    const bookingMonthLabels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const bookingTrendsChart = new Chart(bookingTrendsCanvas, {
      type: 'line',
      data: {
        labels: bookingMonthLabels,
        datasets: [{ label: 'Bookings', data: Array(12).fill(0), borderColor: chartColors.accent1, borderWidth: 2, tension: 0.3, fill: false }]
      },
      options: { responsive:false, maintainAspectRatio:false, plugins: { legend: { display: false } }, scales: { x: { ticks: { color: chartColors.muted } }, y: { ticks: { color: chartColors.muted }, beginAtZero: true } } }
    });

    const propertySalesCanvas = document.getElementById('propertySalesChart');
    propertySalesCanvas.style.width = '100%'; propertySalesCanvas.style.height = '100%';
    // Use a scatter plot for Property Sales by Type. We map types to x positions 0..2
    const propertyTypeLabels = ['Apartment', 'Condo', 'House'];
    const propertySalesChart = new Chart(propertySalesCanvas, {
      type: 'scatter',
      data: {
        datasets: [{
          label: 'Sales',
          data: [
            { x: 0, y: 320000 },
            { x: 1, y: 210000 },
            { x: 2, y: 420000 }
          ],
          backgroundColor: [chartColors.accent1, chartColors.accent2, chartColors.accent3],
          pointRadius: 8
        }]
      },
      options: {
        responsive: false,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: {
            type: 'linear',
            min: 0,
            max: propertyTypeLabels.length - 1,
            ticks: {
              stepSize: 1,
              callback: function(value) { return propertyTypeLabels[value] ?? value; },
              color: chartColors.muted
            }
          },
          y: { ticks: { color: chartColors.muted } }
        }
      }
    });

    const agentPerformanceCanvas = document.getElementById('agentPerformanceChart');
    agentPerformanceCanvas.style.width = '100%'; agentPerformanceCanvas.style.height = '100%';
    const agentPerformanceChart = new Chart(agentPerformanceCanvas, {
      type: 'doughnut',
      data: {
        labels: ['Agent A', 'Agent B', 'Agent C'],
        datasets: [{ data: [45, 30, 25], backgroundColor: [chartColors.accent1, chartColors.accent2, chartColors.accent3], borderWidth: 0 }]
      },
      options: { responsive:false, maintainAspectRatio:false, plugins: { legend: { labels: { color: chartColors.text } } } }
    });

    // initial load
    fetchReports();
    (function(){
      try {
        const charts = {};
        document.querySelectorAll('canvas[id]').forEach(c => {
          const cid = c.id;
          let inst = null;
          try { inst = Chart.getChart(c); } catch(e) { inst = c.__chartjs || null; }
          if (inst) charts[cid] = inst;
        });

        function makeResizer(container){
          const handle = container.querySelector('.resizer');
          if (!handle) return;
          handle.setAttribute('tabindex','0');
          let startY=0, startX=0, startH=0, startW=0, active=false;
          const onPointerDown = (e) => {
            e.preventDefault(); active = true;
            startY = (e.touches ? e.touches[0].clientY : e.clientY);
            startX = (e.touches ? e.touches[0].clientX : e.clientX);
            const card = container.closest('.chart-card') || container;
            startH = card.clientHeight; startW = card.clientWidth;
            document.addEventListener('pointermove', onPointerMove);
            document.addEventListener('pointerup', onPointerUp);
            document.addEventListener('touchmove', onPointerMove, { passive:false });
            document.addEventListener('touchend', onPointerUp);
          };
          const onPointerMove = (e) => {
            if (!active) return; e.preventDefault();
            const y = (e.touches ? e.touches[0].clientY : e.clientY);
            const x = (e.touches ? e.touches[0].clientX : e.clientX);
            const dy = y - startY; const dx = x - startX;
            const newH = Math.max(120, startH + dy); const minW = 200;
            const chartsArea = container.closest('.chart-section')?.getBoundingClientRect() || { width: window.innerWidth };
            const maxW = Math.max(minW, chartsArea.width - 24);
            const newW = Math.min(maxW, Math.max(minW, startW + dx));
            const cardEl = container.closest('.chart-card') || container;
            cardEl.style.height = newH + 'px'; cardEl.style.width = newW + 'px';
            const chartId = container.dataset.chart; const chart = charts[chartId];
            if (chart && typeof chart.resize === 'function') chart.resize();
          };
          const onPointerUp = () => { active = false; document.removeEventListener('pointermove', onPointerMove); document.removeEventListener('pointerup', onPointerUp); document.removeEventListener('touchmove', onPointerMove); document.removeEventListener('touchend', onPointerUp); };
          handle.addEventListener('pointerdown', onPointerDown); handle.addEventListener('touchstart', onPointerDown, { passive:false });
          handle.addEventListener('keydown', (ev) => {
            const chartId = container.dataset.chart; const chart = charts[chartId]; const step = ev.shiftKey ? 40 : 12; const cardEl = container.closest('.chart-card') || container; const chartsArea = container.closest('.chart-section')?.getBoundingClientRect() || { width: window.innerWidth }; const maxW = Math.max(200, chartsArea.width - 24);
            if (ev.key === 'ArrowUp') { ev.preventDefault(); const h = Math.max(120, cardEl.clientHeight - step); cardEl.style.height = h + 'px'; if (chart && typeof chart.resize === 'function') chart.resize(); }
            else if (ev.key === 'ArrowDown') { ev.preventDefault(); const h = cardEl.clientHeight + step; cardEl.style.height = h + 'px'; if (chart && typeof chart.resize === 'function') chart.resize(); }
            else if (ev.key === 'ArrowLeft') { ev.preventDefault(); const w = Math.max(200, cardEl.clientWidth - step); cardEl.style.width = w + 'px'; if (chart && typeof chart.resize === 'function') chart.resize(); }
            else if (ev.key === 'ArrowRight') { ev.preventDefault(); const w = Math.min(maxW, cardEl.clientWidth + step); cardEl.style.width = w + 'px'; if (chart && typeof chart.resize === 'function') chart.resize(); }
          });
        }

        document.querySelectorAll('.chart-container.resizable').forEach(makeResizer);
        window.addEventListener('resize', () => { document.querySelectorAll('canvas[id]').forEach(c => { try { const inst = Chart.getChart(c); if (inst) charts[c.id] = inst; } catch(e){} }); });
      } catch(e) { console.warn('reports resizer', e); }
    })();
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      if (window.lucide) try { lucide.createIcons(); } catch(e){}
      const t = document.getElementById('toggleSidebar');
      if (t) {
        const updateAria = () => {
          const isDesktop = window.matchMedia('(min-width:701px)').matches;
          if (isDesktop) {
            const expanded = !document.body.classList.contains('sidebar-collapsed');
            t.setAttribute('aria-expanded', expanded.toString());
          } else {
            const sidebar = document.querySelector('.sidebar');
            const expanded = !!(sidebar && sidebar.classList.contains('show'));
            t.setAttribute('aria-expanded', expanded.toString());
          }
        };
        updateAria();
        t.addEventListener('click', () => {
          if (window.matchMedia('(min-width:701px)').matches) {
            document.body.classList.toggle('sidebar-collapsed');
            updateAria();
          } else {
            const sidebar = document.querySelector('.sidebar');
            const visible = sidebar?.classList.toggle('show');
            t.setAttribute('aria-expanded', (!!visible).toString());
          }
        });
        window.addEventListener('resize', updateAria);
      }
    });
  </script>
  <script defer src="theme-toggle.js"></script>
  <script>
    // Drag & drop reordering for report charts
    (function(){
      try {
        const container = document.querySelector('.chart-section') || document.querySelector('.charts');
        if (!container) return;
        let dragEl = null; let placeholder = null;
        function createPlaceholder(h,w){ const el = document.createElement('div'); el.className='placeholder'; el.style.height=(h||220)+'px'; el.style.minWidth=(w||280)+'px'; return el; }
        container.querySelectorAll('.chart-card').forEach(card => {
          card.setAttribute('draggable','true');
          card.addEventListener('dragstart', (e)=>{ dragEl = card; card.classList.add('dragging'); placeholder = createPlaceholder(card.clientHeight, card.clientWidth); e.dataTransfer.effectAllowed='move'; });
          card.addEventListener('dragend', ()=>{ if (dragEl) dragEl.classList.remove('dragging'); dragEl=null; if (placeholder && placeholder.parentElement) placeholder.parentElement.removeChild(placeholder); placeholder=null; });
        });
        container.addEventListener('dragover', (e)=>{ e.preventDefault(); const after = Array.from(container.children).find(ch=>{ if (ch===dragEl||ch.classList.contains('placeholder')) return false; const rect = ch.getBoundingClientRect(); return e.clientX < rect.left + rect.width/2; }); if (!placeholder) placeholder=createPlaceholder(); if (after) container.insertBefore(placeholder, after); else container.appendChild(placeholder); });
        container.addEventListener('drop', (e)=>{ e.preventDefault(); if (!dragEl) return; if (placeholder && placeholder.parentElement) container.insertBefore(dragEl, placeholder); if (placeholder && placeholder.parentElement) placeholder.parentElement.removeChild(placeholder); placeholder=null; });
      } catch(e){ console.warn('reports drag init', e); }
    })();
  </script>
</body>
</html>
