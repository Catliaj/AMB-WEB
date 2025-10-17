<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard</title>
 
  <link rel="stylesheet" href="<?= base_url('assets/styles/admin-style.css')?>">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script defer src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script defer src="https://unpkg.com/lucide@latest"></script>
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

<body class="dashboard-page" style="background:var(--bg);">
  <div id="loadingOverlay" class="loading-overlay" role="status" aria-hidden="false">
    <div class="loader" aria-hidden="true">
      <div class="spinner"></div>
    </div>
  </div>


  <aside class="sidebar">
    <img src="amb_logo.png" alt="AMB Logo">
    <nav class="nav">
      <a href="/admin/adminHomepage" class="active"><i data-lucide="layout-dashboard"></i> Dashboard</a>
      <a href="/admin/manageUsers"><i data-lucide="users"></i> Manage Users</a>
      <a href="/admin/ManageProperties"><i data-lucide="home"></i> Manage Properties</a>
      <a href="/admin/userBookings"><i data-lucide="calendar"></i> User Bookings</a>
      <a href="/admin/viewChats"><i data-lucide="message-circle"></i> View Chats</a>
      <a href="/admin/Reports"><i data-lucide="bar-chart-2"></i> Generate Reports</a>
    </nav>
    <div class="profile-box">
      <div class="profile-avatar">A</div>
      <div class="profile-info">
        <!-- set the email here-->
        <strong><?= session('FirstName') . ' ' . session('LastName'); ?></strong>
        <span><?= session('inputEmail'); ?></span>
      </div>
    </div>
  </aside>

  <section class="main">

    <header>
      <div class="left-header">
        <button id="toggleSidebar" class="btn"><i data-lucide="menu"></i></button>
        <h1><i data-lucide="activity"></i> Admin Dashboard</h1>
      </div>
      <div class="controls">
        <button class="btn primary" id="logoutBtn"><i data-lucide="log-out"></i> Logout</button>
      </div>
    </header>

    <div class="content">
      <div class="cards">
        <div class="card"><h2>Total Users</h2><div class="value" id="totalUsers"></div></div>
        <div class="card"><h2>Total Properties</h2><div class="value" id="totalProperties">0</div></div>
        <div class="card"><h2>Total Bookings</h2><div class="value" id="totalBookings">0</div></div>
        <div class="card"><h2>Active Chats</h2><div class="value" id="activeChats">0</div></div>

        <div class="card"><h2>New signups (24h)</h2><div class="value" id="newSignups">12</div></div>
        <div class="card"><h2>Pending bookings</h2><div class="value" id="pendingBookings">0</div></div>
        <div class="card"><h2>Agent assignments</h2><div class="value" id="agentAssignments">0</div></div>
      </div>

      <div class="main-row">
          <div class="chart-card chart-top">
            <h3><path d="M7 9h2M7 13h2M11 9h2M11 13h2M15 9h2M15 13h2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button><i data-lucide="line-chart"></i> Booking Trend</h3>
            <div class="chart-container resizable" data-chart="bookingChart"><canvas id="bookingChart"></canvas>
              <path d="M16 16l4 4M11 16l9 0M6 16l10 0" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
            </div>
          </div>
          <div class="chart-card chart-bottom">
            <h3><path d="M7 9h2M7 13h2M11 9h2M11 13h2M15 9h2M15 13h2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button><i data-lucide="pie-chart"></i> Property Distribution</h3>
            <div class="chart-container resizable" data-chart="propertyChart"><canvas id="propertyChart"></canvas>
              <path d="M16 16l4 4M11 16l9 0M6 16l10 0" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
            </div>
          </div>
        </div>



      </div>
    </div>
  </section>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      if (window.lucide) try { lucide.createIcons(); } catch (e) { console.warn('lucide init', e); }

      const toggle = document.getElementById("toggleSidebar");
      if (toggle) {
        const updateAria = () => {
          const isDesktop = window.matchMedia('(min-width:701px)').matches;
          if (isDesktop) {
            const expanded = !document.body.classList.contains('sidebar-collapsed');
            toggle.setAttribute('aria-expanded', expanded.toString());
          } else {
            const sidebar = document.querySelector('.sidebar');
            const expanded = !!(sidebar && sidebar.classList.contains('show'));
            toggle.setAttribute('aria-expanded', expanded.toString());
          }
        };
        updateAria();

        toggle.addEventListener('click', () => {
          if (window.matchMedia('(min-width:701px)').matches) {
            document.body.classList.toggle('sidebar-collapsed');
            updateAria();
          } else {
            const sidebar = document.querySelector('.sidebar');
            const visible = sidebar?.classList.toggle('show');
            toggle.setAttribute('aria-expanded', (!!visible).toString());
          }
        });

        window.addEventListener('resize', updateAria);
      }

      function animateValue(id, start, end, duration) {
        const obj = document.getElementById(id);
        if (!obj) return;
        let startTimestamp = null;
        const step = (timestamp) => {
          if (!startTimestamp) startTimestamp = timestamp;
          const progress = Math.min((timestamp - startTimestamp) / duration, 1);
          obj.textContent = Math.floor(progress * (end - start) + start);
          if (progress < 1) window.requestAnimationFrame(step);
        };
        window.requestAnimationFrame(step);
      }
      animateValue("totalUsers", 0, <?= esc($totalUsers)?>, 1500);
      animateValue("totalProperties", 0, <?= esc($totalProperties)?>, 1500);
      animateValue("totalBookings", 0, <?= esc($totalBookings)?>, 1500);
      animateValue("activeChats", 0, 14, 1500);
      animateValue("newSignups", 0, 12, 1200);
      animateValue("pendingBookings", 0, <?= esc($pendingBookings)?>, 1200);
      animateValue("agentAssignments", 0, 4, 1200);

      try {
        if (window.Chart) {
          const propertyCanvas = document.getElementById("propertyChart");
          const bookingCanvas = document.getElementById("bookingChart");
          if (propertyCanvas && bookingCanvas) {
            propertyCanvas.style.width = '100%'; propertyCanvas.style.height = '100%';
            bookingCanvas.style.width = '100%'; bookingCanvas.style.height = '100%';
            const propertyCtx = propertyCanvas.getContext("2d");
            const bookingCtx = bookingCanvas.getContext("2d");

            const css = getComputedStyle(document.documentElement);
            const cAccent1 = css.getPropertyValue('--accent1').trim() || '#438f41';
            const cAccent2 = css.getPropertyValue('--accent2').trim() || '#2a405a';
            const cAccent3 = css.getPropertyValue('--accent3').trim() || '#68b76b';
            const accent1Semi = hexToRgba(cAccent1, 0.12);

            new Chart(propertyCtx, {
              type: "doughnut",
              data: {
                labels: ["Available","Reserved","Sold"],
                datasets: [{ data:[<?=esc($availableData)?>,<?=esc($reservedData)?>,<?=esc($soldData)?>], backgroundColor:[cAccent1, cAccent2, cAccent3], borderWidth:0 }]
              },
              options: { responsive:false, maintainAspectRatio:false, plugins:{ legend:{ labels:{ color: css.getPropertyValue('--text').trim() || '#ccc', font:{ size:12 } }, position:"bottom" } } }
            });

            new Chart(bookingCtx, {
              type: "line",
              data: {
                labels:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
                datasets:[{ label:"Bookings", data: <?=esc($BookingData)?>, tension:0.3, borderWidth:2, borderColor:cAccent1, backgroundColor:accent1Semi, fill:true }]
              },
              options: { responsive:false, maintainAspectRatio:false, scales:{ x:{ ticks:{ color:"#999" }, grid:{ color:"#333" } }, y:{ ticks:{ color:"#999" }, grid:{ color:"#333" } } }, plugins:{ legend:{ display:false } } }
            });

            function hexToRgba(hex, alpha){
              try{
                hex = hex.replace('#','').trim();
                if (hex.length === 3) hex = hex.split('').map(h=>h+h).join('');
                const bigint = parseInt(hex, 16);
                const r = (bigint >> 16) & 255;
                const g = (bigint >> 8) & 255;
                const b = bigint & 255;
                return `rgba(${r},${g},${b},${alpha})`;
              }catch(e){ return `rgba(67,143,65,${alpha})`; }
            }
          }
        }
      } catch (e) { console.warn('Chart init error', e); }

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

              let startY = 0, startX = 0, startH = 0, startW = 0, active = false;

              const onPointerDown = (e) => {
                e.preventDefault();
                active = true;
                startY = (e.touches ? e.touches[0].clientY : e.clientY);
                startX = (e.touches ? e.touches[0].clientX : e.clientX);
                const card = container.closest('.chart-card') || container;
                startH = card.clientHeight;
                startW = card.clientWidth;
                document.addEventListener('pointermove', onPointerMove);
                document.addEventListener('pointerup', onPointerUp);
                document.addEventListener('touchmove', onPointerMove, { passive:false });
                document.addEventListener('touchend', onPointerUp);
              };

              const onPointerMove = (e) => {
                if (!active) return;
                e.preventDefault();
                const y = (e.touches ? e.touches[0].clientY : e.clientY);
                const x = (e.touches ? e.touches[0].clientX : e.clientX);
                const dy = y - startY;
                const dx = x - startX;
                const newH = Math.max(120, startH + dy);
                const minW = 200;
                const chartsArea = container.closest('.charts')?.getBoundingClientRect() || { width: window.innerWidth };
                const maxW = Math.max(minW, chartsArea.width - 24);
                const newW = Math.min(maxW, Math.max(minW, startW + dx));

                const cardEl = container.closest('.chart-card') || container;
                cardEl.style.height = newH + 'px';
                cardEl.style.width = newW + 'px';

                const chartId = container.dataset.chart;
                const chart = charts[chartId];
                if (chart && typeof chart.resize === 'function') chart.resize();
              };

              const onPointerUp = () => {
                active = false;
                document.removeEventListener('pointermove', onPointerMove);
                document.removeEventListener('pointerup', onPointerUp);
                document.removeEventListener('touchmove', onPointerMove);
                document.removeEventListener('touchend', onPointerUp);
              };

              handle.addEventListener('pointerdown', onPointerDown);
              handle.addEventListener('touchstart', onPointerDown, { passive:false });

              handle.addEventListener('keydown', (ev) => {
                const chartId = container.dataset.chart;
                const chart = charts[chartId];
                const step = ev.shiftKey ? 40 : 12;
                const cardEl = container.closest('.chart-card') || container;
                const chartsArea = container.closest('.charts')?.getBoundingClientRect() || { width: window.innerWidth };
                const maxW = Math.max(200, chartsArea.width - 24);
                if (ev.key === 'ArrowUp') {
                  ev.preventDefault();
                  const h = Math.max(120, cardEl.clientHeight - step);
                  cardEl.style.height = h + 'px';
                  if (chart && typeof chart.resize === 'function') chart.resize();
                } else if (ev.key === 'ArrowDown') {
                  ev.preventDefault();
                  const h = cardEl.clientHeight + step;
                  cardEl.style.height = h + 'px';
                  if (chart && typeof chart.resize === 'function') chart.resize();
                } else if (ev.key === 'ArrowLeft') {
                  ev.preventDefault();
                  const w = Math.max(200, cardEl.clientWidth - step);
                  cardEl.style.width = w + 'px';
                  if (chart && typeof chart.resize === 'function') chart.resize();
                } else if (ev.key === 'ArrowRight') {
                  ev.preventDefault();
                  const w = Math.min(maxW, cardEl.clientWidth + step);
                  cardEl.style.width = w + 'px';
                  if (chart && typeof chart.resize === 'function') chart.resize();
                }
              });
            }

            document.querySelectorAll('.chart-container.resizable').forEach(makeResizer);
            window.addEventListener('resize', () => {
              document.querySelectorAll('canvas[id]').forEach(c => {
                try { const inst = Chart.getChart(c); if (inst) charts[c.id] = inst; } catch(e) {}
              });
            });
          } catch (e) { console.warn('resizer init', e); }
        })();

              (function(){
                try {
                  const container = document.querySelector('.charts');
                  if (!container) return;
                  let dragEl = null;
                  let placeholder = null;

                  function createPlaceholder(h, w){
                    const el = document.createElement('div');
                    el.className = 'placeholder';
                    el.style.height = (h || 220) + 'px';
                    el.style.minWidth = (w || 280) + 'px';
                    return el;
                  }

                  container.querySelectorAll('.chart-card').forEach(card => {
                    card.setAttribute('draggable','true');
                    card.addEventListener('dragstart', (e) => {
                      dragEl = card;
                      card.classList.add('dragging');
                      placeholder = createPlaceholder(card.clientHeight, card.clientWidth);
                      e.dataTransfer.effectAllowed = 'move';
                    });
                    card.addEventListener('dragend', () => {
                      if (dragEl) dragEl.classList.remove('dragging');
                      dragEl = null;
                      if (placeholder && placeholder.parentElement) placeholder.parentElement.removeChild(placeholder);
                      placeholder = null;
                    });
                  });

                  container.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    const after = Array.from(container.children).find(ch => {
                      if (ch === dragEl || ch.classList.contains('placeholder')) return false;
                      const rect = ch.getBoundingClientRect();
                      return e.clientX < rect.left + rect.width / 2;
                    });
                    if (!placeholder) placeholder = createPlaceholder();
                    if (after) container.insertBefore(placeholder, after);
                    else container.appendChild(placeholder);
                  });

                  container.addEventListener('drop', (e) => {
                    e.preventDefault();
                    if (!dragEl) return;
                    if (placeholder && placeholder.parentElement) container.insertBefore(dragEl, placeholder);
                    if (placeholder && placeholder.parentElement) placeholder.parentElement.removeChild(placeholder);
                    placeholder = null;
                  });
                } catch (e) { console.warn('drag init', e); }
              })();

      const popups = { notifBtn: "notifPopup", settingsBtn: "settingsPopup", profileBtn: "profilePopup" };
      for (const [btnId, popupId] of Object.entries(popups)) {
        const btn = document.getElementById(btnId);
        const popup = document.getElementById(popupId);
        if (!btn || !popup) continue;
        btn.addEventListener('click', (e) => { e.stopPropagation(); for (const p of Object.values(popups)) document.getElementById(p)?.classList.remove('active'); popup.classList.toggle('active'); });
      }
      document.addEventListener('click', () => { for (const p of Object.values(popups)) document.getElementById(p)?.classList.remove('active'); });

      const logout = document.getElementById('logoutBtn');
      const logoutModal = document.getElementById('logoutModal');
      const confirmLogout = document.getElementById('confirmLogout');
      const cancelLogout = document.getElementById('cancelLogout');

      if (logout && logoutModal) {
        const openLogoutModal = (e) => { if (e) e.preventDefault(); logoutModal.style.display = 'flex'; logoutModal.setAttribute('aria-hidden','false'); logoutModal.classList.add('active'); };
        const closeLogoutModal = () => { logoutModal.style.display = 'none'; logoutModal.setAttribute('aria-hidden','true'); logoutModal.classList.remove('active'); };

        logout.addEventListener('click', openLogoutModal);
        if (cancelLogout) cancelLogout.addEventListener('click', closeLogoutModal);
        if (logoutModal) logoutModal.addEventListener('click', (e) => { if (e.target === logoutModal) closeLogoutModal(); });
        if (confirmLogout) confirmLogout.addEventListener('click', () => {
          closeLogoutModal();
          window.location.href = '/admin/logout';
        }, 300);
      }
      function hideLoader() {
        const ol = document.getElementById('loadingOverlay');
        if (!ol) return;
        ol.classList.add('hidden');
        ol.setAttribute('aria-hidden','true');
        setTimeout(() => { ol.parentElement && ol.parentElement.removeChild(ol); }, 600);
      }

      requestAnimationFrame(() => { setTimeout(hideLoader, 80); });
    });
  </script>
  
  <div class="modal" id="logoutModal" aria-hidden="true" role="dialog" aria-labelledby="logoutTitle">
    <div class="modal-content">
      <h2 id="logoutTitle"><i data-lucide="log-out"></i> Confirm Logout</h2>
      <div align="center" style="color:var(--muted);">Are you sure you want to logout?</div>
      <div class="modal-actions">
        <button class="btn" id="cancelLogout"><i data-lucide="x"></i> Cancel</button>
        <button onclick="closeTab()" class="btn danger" id="confirmLogout"><i data-lucide="log-out"></i> Logout</button>
      </div>
    </div>
  </div>
  <div class="modal" id="logsModal" aria-hidden="true">
    <div class="modal-content">
      <h2><i data-lucide="list"></i> Activity Logs</h2>
      <div id="logsContent" style="max-height:320px;overflow:auto;padding:8px 0;color:var(--muted)"></div>
      <div class="modal-actions">
        <button class="btn" id="clearLogs"><i data-lucide="trash-2"></i> Clear</button>
        <button class="btn primary" id="closeLogs"><i data-lucide="x"></i> Close</button>
      </div>
    </div>
  </div>

  <script>
    (function(){
      try {
        const sampleLogs = [
          '2025-10-15 10:32 — New booking created (BOOK001)',
          '2025-10-15 09:10 — Agent assigned to property PROP234',
          '2025-10-14 18:03 — User jane@company.com registered'
        ];
        const logsBtn = document.getElementById('viewLogsBtn');
        const logsModal = document.getElementById('logsModal');
        const logsContent = document.getElementById('logsContent');
        const closeLogs = document.getElementById('closeLogs');
        const clearLogs = document.getElementById('clearLogs');

        function openModal(modal){ modal.style.display = 'flex'; modal.setAttribute('aria-hidden','false'); }
        function closeModal(modal){ modal.style.display = 'none'; modal.setAttribute('aria-hidden','true'); }

        if (logsBtn && logsModal) {
          logsBtn.addEventListener('click', () => {
            logsContent.innerHTML = sampleLogs.map(l => `<div style="padding:6px 0;border-bottom:1px solid var(--divider)">${l}</div>`).join('');
            openModal(logsModal);
          });
        }
        if (closeLogs) closeLogs.addEventListener('click', () => closeModal(logsModal));
        if (clearLogs) clearLogs.addEventListener('click', () => { logsContent.innerHTML = '<div style="padding:6px;color:var(--muted)">No logs</div>'; });
        window.addEventListener('click', (e) => { if (e.target === logsModal) closeModal(logsModal); });
      } catch (e) { console.warn('logs modal', e); }
    })();
  </script>
  <script defer src="theme-toggle.js"></script>
  <script>
    (function(){
      try {
        const container = document.querySelector('.charts');
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
      } catch(e){ console.warn('dashboard drag init', e); }
    })();
  </script>
  <script>
    function closeTab() {
      window.close();
    }
  </script>
</body>
</html>
