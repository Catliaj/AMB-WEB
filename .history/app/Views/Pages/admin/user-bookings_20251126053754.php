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
  
      // Initialize Bootstrap modals
      const viewBookingModal = new bootstrap.Modal(document.getElementById('viewBookingModal'));
      const editBookingModal = new bootstrap.Modal(document.getElementById('editBookingModal'));
      const deleteBookingModal = new bootstrap.Modal(document.getElementById('deleteBookingModal'));
  
      let deleteBookingID = null;
  
      // View booking - fetch and display booking details in modal
      function viewBooking(id) {
         const booking = bookings.find(b => b.bookingID == id);
         if (booking) {
           document.getElementById('viewBookingID').textContent = booking.bookingID;
           document.getElementById('viewClientName').textContent = booking.ClientName;
           document.getElementById('viewPropertyTitle').textContent = booking.PropertyTitle;
           document.getElementById('viewBookingDate').textContent = new Date(booking.BookingDate).toLocaleDateString();
           document.getElementById('viewStatus').textContent = booking.Status;
           viewBookingModal.show();
         }
      }
  
      // Edit booking - show modal to change status
      function editBooking(id) {
         const booking = bookings.find(b => b.bookingID == id);
         if (booking) {
           document.getElementById('editBookingID').value = booking.bookingID;
           document.getElementById('editStatus').value = booking.Status;
           editBookingModal.show();
         }
      }
  
      // Delete booking - show confirmation modal
      function deleteBooking(id) {
         deleteBookingID = id;
         deleteBookingModal.show();
      }


    const tableBody = document.querySelector('#bookingTable tbody');
    const searchInput = document.getElementById('searchInput');
    const filterStatus = document.getElementById('filterStatus');

    const bookings = <?= json_encode($booking) ?>;

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

    // Edit booking form submit - update status via AJAX
    document.getElementById('editBookingForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const bookingID = document.getElementById('editBookingID').value;
      const newStatus = document.getElementById('editStatus').value;
      fetch('<?= base_url("/admin/update-booking-status") ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ bookingID: bookingID, Status: newStatus })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          const booking = bookings.find(b => b.bookingID == bookingID);
          if (booking) booking.Status = newStatus;
          renderBookings();
          editBookingModal.hide();
        } else {
          alert('Failed to update booking');
        }
      })
      .catch(err => console.error('Error updating booking:', err));
    });

    // Confirm delete booking - send DELETE request
    document.getElementById('confirmDeleteBooking').addEventListener('click', () => {
      if (!deleteBookingID) return;
      fetch('<?= base_url("/admin/delete-booking") ?>/' + deleteBookingID, {
        method: 'DELETE'
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          const index = bookings.findIndex(b => b.bookingID == deleteBookingID);
          if (index !== -1) bookings.splice(index, 1);
          renderBookings();
          deleteBookingModal.hide();
          deleteBookingID = null;
        } else {
          alert('Failed to delete booking');
        }
      })
      .catch(err => console.error('Error deleting booking:', err));
    });

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
</body>
</html>
