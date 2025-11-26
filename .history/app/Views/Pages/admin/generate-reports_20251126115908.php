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

  <link rel="stylesheet" href="<?= base_url('assets/styles/admin-style.css')?>">

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
      <a href="/admin/userBookings"><i data-lucide="calendar"></i> User Bookings</a>
      <a href="/admin/viewChats"><i data-lucide="message-circle"></i> View Chats</a>
      <a href="/admin/Reports" class="active"><i data-lucide="bar-chart-2"></i> Generate Reports</a>
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
      <div class="controls">
        <button class="btn primary" id="generateReportBtn"><i data-lucide="refresh-ccw"></i></button>
        <button class="btn" id="exportPdf"><i data-lucide="file-down"></i></button>
        <button class="btn" id="exportExcel"><i data-lucide="file-spreadsheet"></i></button>
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

    <div class="summary-cards">
      <div class="summary-card">
        <h2>Total Sales</h2>
        <div class="value" id="totalSales">₱1,250,000</div>
      </div>
      <div class="summary-card">
        <h2>Total Properties</h2>
        <div class="value" id="totalProperties">128</div>
      </div>
      <div class="summary-card">
        <h2>Active Users</h2>
        <div class="value" id="activeUsers">86</div>
      </div>
    </div>

    <div class="report-layout">
      <div class="report-preview">
        <h3 style="margin-bottom:10px;">Report Preview</h3>
        <table id="reportTable">
          <thead>
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

    const reportData = [
      { id: "REP001", property: "Modern Condo", agent: "Agent A", status: "Approved", sales: 150000, date: "2025-10-05" },
      { id: "REP002", property: "Family Home", agent: "Agent B", status: "Pending", sales: 90000, date: "2025-09-28" },
      { id: "REP003", property: "Luxury Villa", agent: "Agent C", status: "Approved", sales: 240000, date: "2025-09-15" },
      { id: "REP004", property: "Apartment Block", agent: "Agent A", status: "Rejected", sales: 0, date: "2025-08-12" }
    ];

    function populateTable(data) {
      const tbody = document.getElementById("reportTableBody");
      tbody.innerHTML = "";
      data.forEach(row => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${row.id}</td>
          <td>${row.property}</td>
          <td>${row.agent}</td>
          <td>${row.status}</td>
          <td>₱${row.sales.toLocaleString()}</td>
          <td>${row.date}</td>
        `;
        tbody.appendChild(tr);
      });
    }

    function filterReports() {
      const property = document.getElementById("propertyFilter").value;
      const agent = document.getElementById("agentFilter").value;
      const status = document.getElementById("bookingStatus").value;
      const start = document.getElementById("startDate").value;
      const end = document.getElementById("endDate").value;

      return reportData.filter(r => {
        const matchProperty = property === "All Properties" || r.property.includes(property);
        const matchAgent = agent === "All Agents" || r.agent === agent;
        const matchStatus = status === "All Status" || r.status === status;
        const matchDate = (!start || r.date >= start) && (!end || r.date <= end);
        return matchProperty && matchAgent && matchStatus && matchDate;
      });
    }

    function updateCharts(filtered) {
      const salesByType = { Apartment: 0, Condo: 0, House: 0 };
      filtered.forEach(r => {
        if (r.property.includes("Condo")) salesByType.Condo += r.sales;
        else if (r.property.includes("Apartment")) salesByType.Apartment += r.sales;
        else salesByType.House += r.sales;
      });
      propertySalesChart.data.datasets[0].data = Object.values(salesByType);
      propertySalesChart.update();

      const agentPerf = { "Agent A": 0, "Agent B": 0, "Agent C": 0 };
      filtered.forEach(r => agentPerf[r.agent] += r.sales);
      agentPerformanceChart.data.datasets[0].data = Object.values(agentPerf);
      agentPerformanceChart.update();
    }

    document.getElementById("generateReportBtn").addEventListener("click", () => {
      const filtered = filterReports();
      populateTable(filtered);
      updateCharts(filtered);
      alert("Report updated based on filters!");
    });

    document.getElementById("exportExcel").addEventListener("click", () => {
      const wb = XLSX.utils.book_new();
      const ws = XLSX.utils.table_to_sheet(document.getElementById("reportTable"));
      XLSX.utils.book_append_sheet(wb, ws, "Reports");
      XLSX.writeFile(wb, "Reports.xlsx");
    });

    document.getElementById("exportPdf").addEventListener("click", () => {
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF();
      doc.text("Reports Export", 10, 10);
      doc.autoTable({ html: "#reportTable", startY: 20 });
      doc.save("Reports.pdf");
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
    const bookingTrendsChart = new Chart(bookingTrendsCanvas, {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
        datasets: [{ label: 'Bookings', data: [30, 42, 38, 50, 62, 70, 78], borderColor: chartColors.accent1, borderWidth: 2, tension: 0.3 }]
      },
      options: { responsive:false, maintainAspectRatio:false, plugins: { legend: { display: false } }, scales: { x: { ticks: { color: chartColors.muted } }, y: { ticks: { color: chartColors.muted } } } }
    });

    const propertySalesCanvas = document.getElementById('propertySalesChart');
    propertySalesCanvas.style.width = '100%'; propertySalesCanvas.style.height = '100%';
    const propertySalesChart = new Chart(propertySalesCanvas, {
      type: 'bar',
      data: {
        labels: ['Apartment', 'Condo', 'House'],
        datasets: [{ label: 'Sales', data: [320000, 210000, 420000], backgroundColor: [chartColors.accent1, chartColors.accent2, chartColors.accent3] }]
      },
      options: { responsive:false, maintainAspectRatio:false, plugins: { legend: { display: false } }, scales: { x: { ticks: { color: chartColors.muted } }, y: { ticks: { color: chartColors.muted } } } }
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

    populateTable(reportData);
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
  <script defer src="<?=base_url("assets/js/theme-toggle.js")?>"></script>
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

    // Sync report-preview height with last chart height
    (function(){
      try {
        const lastChart = document.querySelector('.chart-section .chart-card:last-child');
        const reportPreview = document.querySelector('.report-preview');
        if (!lastChart || !reportPreview) return;

        const syncHeight = () => {
          reportPreview.style.height = lastChart.offsetHeight + 'px';
        };

        syncHeight();
        window.addEventListener('resize', syncHeight);

        // Observe changes to last chart height
        const resizeObserver = new ResizeObserver(syncHeight);
        resizeObserver.observe(lastChart);
      } catch(e){ console.warn('height sync init', e); }
    })();
  </script>
</body>
</html>
