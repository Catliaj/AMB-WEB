<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>User Bookings | Admin Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="<?= base_url('assets/styles/admin-style.css')?>">

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


<body>
  <aside class="sidebar">
     <img src="<?= base_url('assets/img/amb_logo.png')?>" alt="AMB Logo">
    <nav class="nav">
      <a href="/admin/adminHomepage"><i data-lucide="layout-dashboard"></i> Dashboard</a>
      <a href="/admin/manageUsers"><i data-lucide="users"></i> Manage Users</a>
      <a href="/admin/ManageProperties"><i data-lucide="home"></i> Manage Properties</a>
      <a href="/admin/userBookings" class="active"><i data-lucide="calendar"></i> User Bookings</a>
      <!-- View Chats removed for privacy -->
      <a href="/admin/Reports"><i data-lucide="bar-chart-2"></i> Generate Reports</a>
    </nav>

    <div class="profile-box">
      <div class="profile-avatar">A</div>
       <div class="profile-info">
        <strong><?= session('FirstName') . ' ' . session('LastName'); ?></strong>
        <span><?= session('inputEmail'); ?></span>
      </div>
    </div>
  </aside>

  <section class="main">
    <header>
      <div class="left-header">
        <button id="toggleSidebar" class="btn"><i data-lucide="menu"></i></button>
        <h1><i data-lucide="calendar"></i> User Bookings</h1>
      </div>
    </header>

    <div class="filters">
      <input type="text" id="searchInput" placeholder="Search by client or property...">
      <select id="filterStatus">
        <option value="">Filter by Status</option>
        <option value="Pending">Pending</option>
        <option value="Approved">Approved</option>
        <option value="Rejected">Rejected</option>
      </select>
      <select id="sortDate" aria-label="Sort by date">
        <option value="desc">Newest first</option>
        <option value="asc">Oldest first</option>
      </select>
    </div>

    <div class="main-row">
      <div class="table-container">
        <table id="bookingTable">
          <thead>
            <tr>
              <th>BookingID</th>
              <th>Property</th>
              <th>Client Name</th>
              
              <th>Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
              <?php if (!empty($booking)): ?>
        <?php foreach ($booking as $b): ?>
          <tr>
            <td><?= esc($b['bookingID']) ?></td>
            <td><?= esc($b['PropertyTitle']) ?></td>
            <td><?= esc($b['ClientName']) ?></td>
            <td><?= esc(date('M d, Y', strtotime($b['BookingDate']))) ?></td>
            <td>
              <?php if ($b['Status'] === 'Approved'): ?>
                <span class="badge bg-success">Approved</span>
              <?php elseif ($b['Status'] === 'Rejected'): ?>
                <span class="badge bg-danger">Rejected</span>
              <?php else: ?>
                <span class="badge bg-warning text-dark">Pending</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="5" class="text-center text-muted py-4">
            No bookings found.
          </td>
        </tr>
      <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <script>
    lucide.createIcons();

    // bookings will be populated from server-rendered rows first (if present), otherwise try JSON endpoint
    let bookings = [];

    (function loadBookings(){
      // Parse server-rendered rows immediately if available
      const serverRows = Array.from(document.querySelectorAll('#bookingTable tbody tr'));
      let parsed = [];
      if (serverRows.length) {
        parsed = serverRows.map(tr => {
          const cells = tr.querySelectorAll('td');
          return {
            id: (cells[0] && cells[0].textContent) ? cells[0].textContent.trim() : '',
            property: (cells[1] && cells[1].textContent) ? cells[1].textContent.trim() : '',
            client: (cells[2] && cells[2].textContent) ? cells[2].textContent.trim() : '',
            date: (cells[3] && cells[3].textContent) ? cells[3].textContent.trim() : '',
            status: (cells[4] && cells[4].textContent) ? cells[4].textContent.trim() : ''
          };
        }).filter(r => {
          // Filter out placeholder "No bookings found" rows where the first cell contains that text
          return !(r.id && r.id.toLowerCase().includes('no bookings found'));
        });
      }

      if (parsed.length) {
        bookings = parsed;
        renderBookings();
        return; // server data present, skip JSON fetch
      }

      // No server rows found — try to fetch JSON from API endpoint
      fetch('/admin/userBookings')
        .then(res => {
          if (!res.ok) throw new Error('Network response was not ok');
          return res.json();
        })
        .then(data => {
          if (Array.isArray(data) && data.length) {
            bookings = data.map(b => ({
              id: b.BookingID ?? b.bookingID ?? b.id ?? '',
              client: b.clients ?? b.clientName ?? (((b.FirstName || '') + ' ' + (b.LastName || '')).trim()) ?? '',
              property: b.title ?? b.PropertyTitle ?? b.property ?? '',
              date: b.BookingDate ?? b.bookingDate ?? b.date ?? '',
              status: b.Status ?? b.status ?? ''
            }));
          }
        })
        .catch(err => {
          console.warn('Could not load bookings JSON from server:', err);
        })
        .finally(() => {
          renderBookings();
        });
    })();

    const tableBody = document.querySelector('#bookingTable tbody');
    let searchInput = document.getElementById('searchInput');
    let filterStatus = document.getElementById('filterStatus');

    function initBookingUI() {
      searchInput = document.getElementById('searchInput');
      filterStatus = document.getElementById('filterStatus');
      if (searchInput) searchInput.addEventListener('input', renderBookings);
      if (filterStatus) filterStatus.addEventListener('change', renderBookings);
      const sortDate = document.getElementById('sortDate');
      if (sortDate) sortDate.addEventListener('change', renderBookings);
    }

    // initialize UI handlers now and again on DOMContentLoaded as a fallback
    initBookingUI();
    document.addEventListener('DOMContentLoaded', () => { initBookingUI(); renderBookings(); });

    // renderBookings will (re)build the table from `bookings` and attach click handlers
    function renderBookings() {
      const q = (searchInput.value || '').trim().toLowerCase();
      const statusVal = filterStatus.value;
      tableBody.innerHTML = '';
      const sortVal = (document.getElementById('sortDate') && document.getElementById('sortDate').value) ? document.getElementById('sortDate').value : 'desc';

      let filtered = bookings.filter(b => {
        const matchQ = !q || ((b.client||'').toLowerCase().includes(q) || (b.property||'').toLowerCase().includes(q));
        const matchStatus = !statusVal || statusVal === '' || (b.status || '') === statusVal;
        return matchQ && matchStatus;
      });

      // sort by booking date (expecting formats like YYYY-MM-DD or Mmm dd, YYYY); try to parse gracefully
      filtered.sort((a,b) => {
        const pa = Date.parse(a.date) || 0;
        const pb = Date.parse(b.date) || 0;
        return sortVal === 'asc' ? pa - pb : pb - pa;
      });

      if (!filtered.length) {
        tableBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No bookings found.</td></tr>';
        return;
      }

      filtered.forEach((b, idx) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `\n          <td>${b.id || ''}</td>\n          <td>${b.property || ''}</td>\n          <td>${b.client || ''}</td>\n          <td>${b.date || ''}</td>\n          <td>${b.status || ''}</td>\n        `;
        tr.style.cursor = 'pointer';
        tr.addEventListener('click', () => openBookingDetail(b));
        tableBody.appendChild(tr);
      });
    }

    // Booking detail modal logic
    function openBookingDetail(b) {
      const modal = document.getElementById('bookingDetailModal');
      if (!modal) return;
      const body = modal.querySelector('#bookingDetailBody');

      // Try to fetch full details from server using booking id
      if (b && b.id) {
        fetch('/admin/booking/' + encodeURIComponent(b.id))
          .then(res => {
            if (!res.ok) throw new Error('Could not fetch booking details');
            return res.json();
          })
          .then(data => {
            body.innerHTML = `
              <table style="width:100%; border-collapse: collapse;">
                <tr><td style="padding:8px; font-weight:600; width:140px;">Booking ID</td><td style="padding:8px;">${data.bookingID || data.bookingId || b.id || '—'}</td></tr>
                <tr><td style="padding:8px; font-weight:600;">Client</td><td style="padding:8px;">${data.ClientName || data.Client || b.client || '—'}</td></tr>
                <tr><td style="padding:8px; font-weight:600;">Client Email</td><td style="padding:8px;">${data.ClientEmail || '—'}</td></tr>
                <tr><td style="padding:8px; font-weight:600;">Client Phone</td><td style="padding:8px;">${data.ClientPhone || '—'}</td></tr>
                <tr><td style="padding:8px; font-weight:600;">Property</td><td style="padding:8px;">${data.PropertyTitle || data.Title || b.property || '—'}</td></tr>
                <tr><td style="padding:8px; font-weight:600;">Property Location</td><td style="padding:8px;">${data.PropertyLocation || data.Location || '—'}</td></tr>
                <tr><td style="padding:8px; font-weight:600;">Date</td><td style="padding:8px;">${data.bookingDate || data.BookingDate || b.date || '—'}</td></tr>
                <tr><td style="padding:8px; font-weight:600;">Status</td><td style="padding:8px;">${data.status || data.BookingStatus || b.status || '—'}</td></tr>
                <tr><td style="padding:8px; font-weight:600;">Notes</td><td style="padding:8px;">${data.Notes || data.Reason || '—'}</td></tr>
              </table>
            `;
            modal.classList.add('open');
            modal.setAttribute('aria-hidden', 'false');
          })
          .catch(err => {
            // fallback to showing basic info if fetch fails
            body.innerHTML = `
              <table style="width:100%; border-collapse: collapse;">
                <tr><td style="padding:8px; font-weight:600; width:140px;">Booking ID</td><td style="padding:8px;">${b.id || '—'}</td></tr>
                <tr><td style="padding:8px; font-weight:600;">Client</td><td style="padding:8px;">${b.client || '—'}</td></tr>
                <tr><td style="padding:8px; font-weight:600;">Property</td><td style="padding:8px;">${b.property || '—'}</td></tr>
                <tr><td style="padding:8px; font-weight:600;">Date</td><td style="padding:8px;">${b.date || '—'}</td></tr>
                <tr><td style="padding:8px; font-weight:600;">Status</td><td style="padding:8px;">${b.status || '—'}</td></tr>
              </table>
            `;
            modal.classList.add('open');
            modal.setAttribute('aria-hidden', 'false');
          });
        return;
      }

      // No id available — render whatever we have
      body.innerHTML = `
        <table style="width:100%; border-collapse: collapse;">
          <tr><td style="padding:8px; font-weight:600; width:140px;">Booking ID</td><td style="padding:8px;">${b.id || '—'}</td></tr>
          <tr><td style="padding:8px; font-weight:600;">Client</td><td style="padding:8px;">${b.client || '—'}</td></tr>
          <tr><td style="padding:8px; font-weight:600;">Property</td><td style="padding:8px;">${b.property || '—'}</td></tr>
          <tr><td style="padding:8px; font-weight:600;">Date</td><td style="padding:8px;">${b.date || '—'}</td></tr>
          <tr><td style="padding:8px; font-weight:600;">Status</td><td style="padding:8px;">${b.status || '—'}</td></tr>
        </table>
      `;
      modal.classList.add('open');
      modal.setAttribute('aria-hidden', 'false');
    }

    document.addEventListener('click', (ev) => {
      const modal = document.getElementById('bookingDetailModal');
      if (!modal) return;
      if (ev.target.matches('#bookingDetailModal .close-modal') || ev.target.matches('#bookingDetailModal .modal-close-btn')) {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
      }
      // click outside content closes
      if (ev.target.id === 'bookingDetailModal') {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
      }
    });

    // Initialize render once DOM is ready in case fetch resolved earlier
    document.addEventListener('DOMContentLoaded', () => { renderBookings(); });

    const ctx = document.getElementById('bookingChart').getContext('2d');
    (function(){
      const css = getComputedStyle(document.documentElement);
      const cAccent1 = css.getPropertyValue('--accent1').trim() || '#438f41';
      const accent1Semi = (function(hex, a){ try{ hex=hex.replace('#',''); if(hex.length===3) hex=hex.split('').map(h=>h+h).join(''); const n=parseInt(hex,16); return `rgba(${(n>>16)&255},${(n>>8)&255},${n&255},${a})`; }catch(e){return `rgba(67,143,65,${a})`;}})(cAccent1, 0.12);

      new Chart(ctx, {
        type: 'line',
        data: {
          labels: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct", "Nov","Dec"],
          datasets: [{
            label: "Bookings",
            data: [8,12,9,15,18,22,20,24,28,32],
            tension: 0.3,
            borderWidth: 2,
            borderColor: cAccent1,
            backgroundColor: accent1Semi,
            fill: true
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            x: { ticks: { color: "#999" }, grid: { color: "#333" } },
            y: { ticks: { color: "#999" }, grid: { color: "#333" } }
          },
          plugins: { legend: { display: false } }
        }
      });
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
  
  <!-- Booking detail modal -->
  <div id="bookingDetailModal" class="simple-modal" aria-hidden="true">
    <div class="simple-modal-content modal-dark">
      <button class="close-modal" aria-label="Close">&times;</button>
      <h3>Booking Details</h3>
      <div id="bookingDetailBody"></div>
      <div style="margin-top:12px; text-align:right;"><button class="btn modal-close-btn">Close</button></div>
    </div>
  </div>
</body>
</html>
