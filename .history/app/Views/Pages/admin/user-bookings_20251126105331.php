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

   function viewBooking(id) {
      alert('View booking details for ID: ' + id);
      // Implement view logic here
   }

   function editBooking(id) {
      alert('Edit booking for ID: ' + id);
      // Implement edit logic here
   }

   function deleteBooking(id) {
      if (confirm('Are you sure you want to delete this booking?')) {
        alert('Delete booking ID: ' + id);
        // Implement delete logic here
      }
   }


    const tableBody = document.querySelector('#bookingTable tbody');
    const searchInput = document.getElementById('searchInput');
    const filterStatus = document.getElementById('filterStatus');

    const bookings = <?= json_encode($booking) ?>;

    // Initialize Bootstrap modals for view, edit, and delete actions
    const viewModal = new bootstrap.Modal(document.getElementById('viewBookingModal'));
    const editModal = new bootstrap.Modal(document.getElementById('editBookingModal'));
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteBookingModal'));

    // Store current booking id for edit and delete actions
    let currentBookingId = null;

    function renderBookings(data = bookings) {
        tableBody.innerHTML = "";
        if (data.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No bookings found.</td></tr>';
            return;
        }
        data.forEach(b => {
            const row = document.createElement("tr");
            const statusClass = b.Status === 'Approved' ? 'success' : b.Status === 'Rejected' ? 'danger' : 'warning text-dark';
            const formattedDate = new Date(b.BookingDate).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
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

    // Function to open view booking modal with booking details
    function viewBooking(id) {
        const booking = bookings.find(b => b.bookingID === id);
        if (!booking) return;
        document.getElementById('viewBookingDetails').innerText = JSON.stringify(booking, null, 2);
        viewModal.show();
    }

    // Function to open edit booking modal, prefill with booking details
    function editBooking(id) {
        const booking = bookings.find(b => b.bookingID === id);
        if (!booking) return;
        currentBookingId = id;
        document.getElementById('editBookingId').value = booking.bookingID;
        document.getElementById('editClientName').value = booking.ClientName;
        document.getElementById('editPropertyTitle').value = booking.PropertyTitle;
        document.getElementById('editBookingDate').value = new Date(booking.BookingDate).toISOString().slice(0, 10);
        document.getElementById('editStatus').value = booking.Status;
        editModal.show();
    }

    // Function to open delete confirmation modal
    function deleteBooking(id) {
        currentBookingId = id;
        deleteModal.show();
    }

    // Submit handler for edit booking form - for demo just alerts changes
    document.getElementById('editBookingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        // Here would be AJAX or form submit logic
        alert('Submit edit for booking ID: ' + currentBookingId);
        editModal.hide();
    });

    // Confirm delete booking handler - for demo just alerts deletion
    document.getElementById('confirmDeleteBookingBtn').addEventListener('click', function () {
        alert('Confirmed delete for booking ID: ' + currentBookingId);
        deleteModal.hide();
    });

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

    searchInput.addEventListener('input', applyFilters);
    filterStatus.addEventListener('change', applyFilters);
    renderBookings();

  </script>

  <!-- Modals for Booking Actions -->
  <div class="modal fade" id="viewBookingModal" tabindex="-1" aria-labelledby="viewBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewBookingModalLabel">View Booking Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <pre id="viewBookingDetails" style="white-space: pre-wrap; word-wrap: break-word;"></pre>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editBookingModal" tabindex="-1" aria-labelledby="editBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editBookingModalLabel">Edit Booking</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="editBookingForm">
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
</body>
</html>
