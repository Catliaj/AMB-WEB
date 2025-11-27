<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>User Bookings | Admin Dashboard</title>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="<?= base_url('assets/styles/admin-style.css')?>">

  <style>
    #bookingTable th, #bookingTable td {
      padding: 9.6px 12.8px;
      font-size: 10.4px;
    }
    .table-container {
      width: 100%;
      overflow: auto;
      max-height: 600px;
    }

    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.7);
      justify-content: center;
      align-items: center;
      animation: fadeIn 0.3s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    .modal-content {
      background-color: var(--card);
      color: var(--text);
      padding: 0;
      border-radius: 12px;
      width: 90%;
      max-width: 500px;
      box-shadow: var(--shadow);
      border: 1px solid var(--divider);
      animation: slideIn 0.3s ease-out;
    }
    @keyframes slideIn {
      from { transform: translateY(-50px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 20px 15px 20px;
      border-bottom: 1px solid var(--divider);
      background: linear-gradient(90deg, var(--accent1), var(--accent2));
      color: #fff;
      border-radius: 12px 12px 0 0;
    }
    .modal-header h2 {
      margin: 0;
      font-size: 1.4em;
      font-weight: 600;
    }
    .close {
      cursor: pointer;
      font-size: 1.8em;
      color: #fff;
      transition: color 0.2s;
    }
    .close:hover {
      color: #ccc;
    }
    .modal-body {
      padding: 20px;
      line-height: 1.6;
    }
    .modal-body p {
      margin: 8px 0;
    }
    .form-group {
      margin-bottom: 15px;
    }
    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: 500;
      color: var(--text);
    }
    .form-group select {
      width: 100%;
      padding: 8px 12px;
      border: 1px solid var(--divider);
      border-radius: 6px;
      background-color: var(--card);
      color: var(--text);
      font-size: 0.9em;
      outline: none;
    }
    .form-group select:focus {
      border-color: var(--accent2);
    }
    .modal-footer {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      padding: 15px 20px 20px 20px;
      border-top: 1px solid var(--divider);
      background-color: var(--panel);
    }
    .btn {
      padding: 10px 20px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 0.9em;
      font-weight: 500;
      transition: all 0.2s;
      font-family: inherit;
    }
    .btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .btn-primary {
      background: linear-gradient(90deg, var(--accent1), var(--accent2));
      color: #fff;
    }
    .btn-danger {
      background: var(--accent3);
      color: #fff;
    }
    .btn-secondary {
      background: var(--panel);
      color: var(--text);
      border: 1px solid var(--divider);
    }
  </style>

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

  <main class="main">
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
  </main>


  <script>
   lucide.createIcons();

    const tableBody = document.querySelector('#bookingTable tbody');
    const searchInput = document.getElementById('searchInput');
    const filterStatus = document.getElementById('filterStatus');

    const bookings = <?= json_encode($booking) ?>;

    // Function to render bookings
    function renderBookings(bookingsToRender) {
        tableBody.innerHTML = '';
        bookingsToRender.forEach(b => {
            const statusClass = b.Status === 'Approved' ? 'success' : b.Status === 'Rejected' ? 'danger' : 'warning text-dark';
            const formattedDate = new Date(b.BookingDate).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${b.bookingID}</td>
                <td>${b.ClientName}</td>
                <td>${b.PropertyTitle}</td>
                <td>${formattedDate}</td>
                <td><span class="badge bg-${statusClass}">${b.Status}</span></td>
            `;
            tableBody.appendChild(row);
        });
        lucide.createIcons();
    }

    // Initial render
    renderBookings(bookings);

    function applyFilters() {
        const searchText = searchInput.value.toLowerCase();
        const statusVal = filterStatus.value;

        const filtered = bookings.filter(b => {
            const matchesSearch = Object.values(b).join(" ").toLowerCase().includes(searchText);
            const matchesStatus = !statusVal || b.Status === statusVal;
            return matchesSearch && matchesStatus;
        });

        renderBookings(filtered);
    }

    // Event listeners for filters
    searchInput.addEventListener('input', applyFilters);
    filterStatus.addEventListener('change', applyFilters);
  </script>

  <script src="<?= base_url('assets/js/theme-toggle.js')?>"></script>
</body>
</html>
