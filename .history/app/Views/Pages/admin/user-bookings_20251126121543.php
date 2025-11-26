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
    #bookingTable .action-btn {
      width: 30px !important;
      height: 30px !important;
      padding: 0 !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
    }
    #bookingTable .action-btn i {
      width: 20px !important;
      height: 20px !important;
    }
    .table-container {
      width: 100%;
      overflow-x: auto;
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
      background-color: var(--bg);
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
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </main>


  <script>
   lucide.createIcons();

   let currentBookingId = null;

   function openModal(modalId) {
     document.getElementById(modalId).style.display = 'flex';
   }

   function closeModal(modalId) {
     document.getElementById(modalId).style.display = 'none';
   }

   // Close modal when clicking outside
   window.onclick = function(event) {
     if (event.target.classList.contains('modal')) {
       event.target.style.display = 'none';
     }
   }

   function viewBooking(id) {
     const booking = bookings.find(b => b.bookingID == id);
     if (booking) {
       const formattedDate = new Date(booking.BookingDate).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
       document.getElementById('viewModalBody').innerHTML = `
         <p><strong>Booking ID:</strong> ${booking.bookingID}</p>
         <p><strong>Client Name:</strong> ${booking.ClientName}</p>
         <p><strong>Property:</strong> ${booking.PropertyTitle}</p>
         <p><strong>Date:</strong> ${formattedDate}</p>
         <p><strong>Status:</strong> ${booking.Status}</p>
       `;
       openModal('viewModal');
     }
   }

   function editBooking(id) {
     const booking = bookings.find(b => b.bookingID == id);
     if (booking) {
       currentBookingId = id;
       document.getElementById('editStatus').value = booking.Status;
       openModal('editModal');
     }
   }

   function saveEdit() {
     // Implement save logic here, e.g., AJAX to update
     alert('Booking updated for ID: ' + currentBookingId);
     closeModal('editModal');
     // Reload or update table
   }

   function deleteBooking(id) {
     currentBookingId = id;
     openModal('deleteModal');
   }

   function confirmDelete() {
     // Implement delete logic here
     alert('Booking deleted for ID: ' + currentBookingId);
     closeModal('deleteModal');
     // Reload or update table
   }

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
                <td class="actions">
                    <button class="action-btn" onclick="viewBooking(${b.bookingID})">
                        <i data-lucide="eye"></i>
                    </button>
                    <button class="action-btn" onclick="editBooking(${b.bookingID})">
                        <i data-lucide="edit-2"></i>
                    </button>
                    <button class="action-btn danger" onclick="deleteBooking(${b.bookingID})">
                        <i data-lucide="trash-2"></i>
                    </button>
                </td>
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

  <!-- Modals -->
  <div id="viewModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Booking Details</h2>
        <span class="close" onclick="closeModal('viewModal')">&times;</span>
      </div>
      <div class="modal-body" id="viewModalBody">
        <!-- Booking details will be populated here -->
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeModal('viewModal')">Close</button>
      </div>
    </div>
  </div>

  <div id="editModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Edit Booking</h2>
        <span class="close" onclick="closeModal('editModal')">&times;</span>
      </div>
      <div class="modal-body">
        <form id="editForm">
          <div class="form-group">
            <label for="editStatus">Status:</label>
            <select id="editStatus" name="status">
              <option value="Pending">Pending</option>
              <option value="Approved">Approved</option>
              <option value="Rejected">Rejected</option>
            </select>
          </div>
          <!-- Add other fields as needed -->
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeModal('editModal')">Cancel</button>
        <button class="btn btn-primary" onclick="saveEdit()">Save</button>
      </div>
    </div>
  </div>

  <div id="deleteModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Confirm Deletion</h2>
        <span class="close" onclick="closeModal('deleteModal')">&times;</span>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this booking?</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" onclick="closeModal('deleteModal')">Cancel</button>
        <button class="btn btn-danger" onclick="confirmDelete()">Delete</button>
      </div>
    </div>
  </div>

  <script src="<?= base_url('assets/js/theme-toggle.js')?>"></script>
</body>
</html>
