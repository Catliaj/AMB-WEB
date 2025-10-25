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
      <a href="/admin/viewChats"><i data-lucide="message-circle"></i> View Chats</a>
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
    </div>

    <div class="main-row">
      <div class="table-container">
        <table id="bookingTable">
          <thead>
            <tr>
              <th>BookingID</th>
              <th>Client Name</th>
              <th>Property</th>
              <th>Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            

          </tbody>
        </table>
      </div>
    </div>
  </section>

  <script>
    lucide.createIcons();

    // bookings will be populated from server; fallback to sample if fetch fails
    let bookings = [
      { id: "BOOK001", client: "John Santos", property: "Modern Studio Apartment", date: "2025-10-03", status: "Pending" },
      { id: "BOOK002", client: "Maria Cruz", property: "Elegant Condo Unit", date: "2025-09-27", status: "Approved" },
      { id: "BOOK003", client: "Carlos Dela Peña", property: "2BR Family House", date: "2025-10-05", status: "Rejected" },
    ];

    // Try to load bookings from backend JSON endpoint. The backend should return an array
    // of objects with fields: id, client (full name), property, date (YYYY-MM-DD), status
    (function loadBookings(){
      fetch('/admin/bookings/json')
        .then(res => {
          if (!res.ok) throw new Error('Network response was not ok');
          return res.json();
        })
        .then(data => {
          if (Array.isArray(data) && data.length) {
            // Map backend fields to expected front-end shape if necessary
            bookings = data.map(b => ({
              id: b.BookingID ?? b.bookingID ?? b.id,
              client: b.clients ?? (b.clientName ?? (b.client ?? `${b.FirstName || ''} ${b.LastName || ''}`).trim()),
              property: b.title ?? b.PropertyTitle ?? b.property ?? b.Property ?? '—',
              date: b.BookingDate ?? b.bookingDate ?? b.date,
              status: b.Status ?? b.status
            }));
          }
        })
        .catch(err => {
          // Keep the sample bookings if fetch fails; log for debugging
          console.warn('Could not load bookings from server, using sample data:', err);
        })
        .finally(() => renderBookings());
    })();

    const tableBody = document.querySelector('#bookingTable tbody');
    const searchInput = document.getElementById('searchInput');
    const filterStatus = document.getElementById('filterStatus');

    function renderBookings() {
      tableBody.innerHTML = '';
      const query = searchInput.value.toLowerCase();
      const filter = filterStatus.value;

      bookings.filter(b =>
        (!filter || b.status === filter) &&
        (b.client.toLowerCase().includes(query) || b.property.toLowerCase().includes(query))
      ).forEach((b, i) => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${b.id}</td>
          <td>${b.client}</td>
          <td>${b.property}</td>
          <td>${b.date}</td>
          <td>${b.status}</td>
          
        `;
        tableBody.appendChild(row);
      });
      lucide.createIcons();
    }

    searchInput.addEventListener('input', renderBookings);
    filterStatus.addEventListener('change', renderBookings);
    renderBookings();

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
  <script defer src="theme-toggle.js"></script>
</body>
</html>
