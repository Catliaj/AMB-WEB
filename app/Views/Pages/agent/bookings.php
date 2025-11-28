<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agent Bookings - ABM Property</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= base_url("bootstrap5/css/bootstrap.min.css")?>" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  
  <style>
    /* CSS VARIABLES */
    :root {
      --primary: #469541;
      --primary-hover: #357a34;
      --secondary: #000000;
      --accent: #2d9fa8;
      --light-bg: #d3f0ff;
      --light-accent: #c8f5d2;
      --bg-color: #ffffff;
      --text-color: #333333;
      --text-muted: #666666;
      --card-bg: #ffffff;
      --border-color: rgba(0, 0, 0, 0.1);
      --shadow: rgba(0, 0, 0, 0.1);
      --table-header-bg: #f8f9fa;
      --table-header-color: #495057;
      --table-row-hover: rgba(0, 0, 0, 0.03);
      --table-border: #dee2e6;
      --table-bg: #ffffff;
    }

    html[data-theme="dark"] {
      --primary: #5ab34f;
      --primary-hover: #469541;
      --secondary: #c4aee3;
      --accent: #3eb3bd;
      --light-bg: #252e42;
      --light-accent: #2d4038;
      --bg-color: #1a1f2e;
      --text-color: #e9ecef;
      --text-muted: #adb5bd;
      --card-bg: #252a3a;
      --border-color: rgba(255, 255, 255, 0.1);
      --shadow: rgba(0, 0, 0, 0.3);
      --table-header-bg: #2d3447;
      --table-header-color: #e9ecef;
      --table-row-hover: rgba(255, 255, 255, 0.05);
      --table-border: #3a4255;
      --table-bg: #252a3a;
    }

    body {
      background-color: var(--bg-color);
      color: var(--text-color);
      transition: background-color 0.3s, color 0.3s;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    /* NAVIGATION */
    .navbar {
      background: linear-gradient(120deg, #d3f0ff 0%, #c8f5d2 100%);
      padding: 10px 50px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
      position: sticky;
      top: 0;
      z-index: 1030;
      border: none !important;
      height: 100px;
    }

    html[data-theme="dark"] .navbar {
      background: linear-gradient(120deg, #252e42 0%, #2d4038 100%);
    }

    .logo-text {
      color: var(--secondary);
      font-size: 20px;
      font-weight: 500;
      letter-spacing: 3px;
    }

    html[data-theme="dark"] .logo-text {
      color: var(--primary);
    }

    .nav-link-custom {
      color: var(--text-color) !important;
      padding: 0.5rem 1rem;
      border-radius: 0.5rem;
      font-size: 18px;
      font-weight: 500;
      transition: all 0.2s;
    }

    .nav-link-custom:hover,
    .nav-link-custom.active {
      color: var(--primary) !important;
    }

    .main-links .nav-item {
      margin: 0 1.5rem;
    }

    #themeToggle {
      background: none;
      border: none;
      cursor: pointer;
      padding: 0.5rem;
    }

    #themeToggle i {
      color: var(--text-color);
      transition: color 0.3s;
    }

    #themeToggle:hover i {
      color: var(--primary);
    }

    /* MAIN CONTENT */
    .main-content {
      padding: 2rem;
      min-height: calc(100vh - 100px);
    }

    /* CARDS */
    .card {
      background-color: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      box-shadow: 0 2px 10px var(--shadow);
    }

    /* TABLE STYLING - FIXED FOR THEMES */
    .table-container {
      border-radius: 8px;
      overflow: hidden;
      border: 1px solid var(--table-border);
      background-color: var(--table-bg);
    }

    .table {
      color: var(--text-color) !important;
      background-color: var(--table-bg) !important;
      margin-bottom: 0;
      border-color: var(--table-border) !important;
    }

    .table thead {
      background-color: var(--table-header-bg) !important;
    }

    .table thead th {
      background-color: var(--table-header-bg) !important;
      color: var(--table-header-color) !important;
      border-color: var(--table-border) !important;
      font-weight: 600;
      padding: 1rem;
      vertical-align: middle;
      border-bottom: 2px solid var(--table-border) !important;
    }

    .table tbody tr {
      border-color: var(--table-border) !important;
      background-color: var(--table-bg) !important;
      transition: background-color 0.2s ease;
    }

    .table tbody tr:hover {
      background-color: var(--table-row-hover) !important;
    }

    .table tbody td {
      color: var(--text-color) !important;
      border-color: var(--table-border) !important;
      padding: 1rem;
      vertical-align: middle;
      background-color: transparent !important;
    }

    /* Ensure all table text follows theme */
    .table th,
    .table td {
      color: var(--text-color) !important;
    }

    /* BUTTONS */
    .btn-primary {
      background-color: var(--primary) !important;
      border-color: var(--primary) !important;
      color: white !important;
    }

    .btn-primary:hover {
      background-color: var(--primary-hover) !important;
      border-color: var(--primary-hover) !important;
    }

    .btn-outline-primary {
      color: var(--primary) !important;
      border-color: var(--primary) !important;
      background-color: transparent;
    }

    .btn-outline-primary:hover {
      background-color: var(--primary) !important;
      color: white !important;
    }

    .btn-outline-success {
      color: #198754 !important;
      border-color: #198754 !important;
      background-color: transparent;
    }

    .btn-outline-success:hover {
      background-color: #198754 !important;
      color: white !important;
    }

    .btn-outline-danger {
      color: #dc3545 !important;
      border-color: #dc3545 !important;
      background-color: transparent;
    }

    .btn-outline-danger:hover {
      background-color: #dc3545 !important;
      color: white !important;
    }

    /* TEXT */
    h1, h2, h3, h4, h5, h6, p, strong, span {
      color: var(--text-color);
    }

    .text-muted {
      color: var(--text-muted) !important;
    }

    /* BADGES */
    .badge {
      padding: 0.4rem 0.75rem;
      border-radius: 0.5rem;
      font-size: 0.85rem;
      font-weight: 600;
    }

    .badge.bg-warning {
      color: #000 !important;
    }

    .badge.bg-success,
    .badge.bg-danger,
    .badge.bg-secondary {
      color: #fff !important;
    }

    /* MODAL */
    .modal-content {
      background-color: var(--card-bg);
      color: var(--text-color);
      border: 1px solid var(--border-color);
    }

    .modal-header {
      background-color: var(--light-bg);
      border-bottom: 1px solid var(--border-color);
    }

    .modal-body {
      background-color: var(--card-bg);
    }

    /* SWEETALERT2 DARK MODE */
    html[data-theme="dark"] .swal2-popup {
      background-color: var(--card-bg);
      color: var(--text-color);
    }

    html[data-theme="dark"] .swal2-title,
    html[data-theme="dark"] .swal2-html-container {
      color: var(--text-color);
    }

    html[data-theme="dark"] .swal2-input {
      background-color: var(--bg-color);
      color: var(--text-color);
      border: 1px solid var(--border-color);
    }

    /* RESPONSIVE */
    @media (max-width: 992px) {
      .navbar {
        padding: 10px 20px;
        height: auto;
      }
      .main-links .nav-item {
        margin: 0;
      }
      .nav-link-custom {
        text-align: left;
        padding-left: 1.8rem;
      }
      .main-content {
        padding: 1rem;
      }
    }

    /* Force override Bootstrap table colors */
    .table > :not(caption) > * > * {
      background-color: transparent !important;
    }
  </style>
</head>

<body>
  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center" href="/users/agentHomepage">
        <img src="<?= base_url('assets/img/amb_logo.png') ?>" alt="AMB Logo" height="50" class="me-2">
        <span class="logo-text">PROPERTY</span>
      </a>

      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mx-lg-auto text-center main-links">
          <li class="nav-item"><a class="nav-link nav-link-custom" href="/users/agentHomepage">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link nav-link-custom" href="/users/agentclients">Clients</a></li>
          <li class="nav-item"><a class="nav-link nav-link-custom active" href="/users/agentbookings">Bookings</a></li>
          <li class="nav-item"><a class="nav-link nav-link-custom" href="/users/agentproperties">Properties</a></li>
          <li class="nav-item"><a class="nav-link nav-link-custom" href="/users/agentchat">Chat</a></li>
          <li class="nav-item d-lg-none"><a class="nav-link nav-link-custom" href="/users/agentprofile">Profile</a></li>
        </ul>

        <ul class="navbar-nav align-items-center d-none d-lg-flex">
          <li class="nav-item"><a class="nav-link nav-link-custom" href="/users/agentprofile"><i class="bi bi-person me-2"></i></a></li>
          <li class="nav-item ms-2">
            <button class="btn btn-link p-2" id="themeToggle" onclick="toggleTheme()">
              <i class="bi bi-sun-fill fs-5" id="themeIcon"></i>
            </button>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="main-content">
    <div class="container-fluid">
      <div class="card p-4 animate__animated animate__fadeInUp">
        <h3 class="fw-bold mb-2">Manage Bookings</h3>
        <p class="text-muted mb-4">View and update all bookings below.</p>

        <div class="table-container">
          <div class="table-responsive">
            <table class="table align-middle text-center">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Property</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="bookingTable">
                <?php if (!empty($bookings)): ?>
                  <?php foreach($bookings as $b): ?>
                    <?php
                      $bookingId = $b['bookingID'] ?? $b['BookingID'] ?? $b['id'] ?? $b['booking_id'] ?? '';
                      $bookingDateRaw = $b['bookingDate'] ?? $b['booking_date'] ?? $b['date'] ?? '';
                      $bookingDate = $bookingDateRaw ? date('Y-m-d', strtotime($bookingDateRaw)) : '';

                      $statusText = $b['BookingStatus'] ?? $b['status'] ?? 'Pending';
                      $statusLower = strtolower(trim($statusText));
                      $isPending = ($statusLower === 'pending');
                      $isScheduled = ($statusLower === 'scheduled');
                      $isRejected = ($statusLower === 'rejected');
                      $isCancelled = ($statusLower === 'cancelled');

                      $statusClass = 'bg-secondary';
                      if ($isPending) $statusClass = 'bg-warning';
                      if ($isScheduled) $statusClass = 'bg-success';
                      if ($isRejected) $statusClass = 'bg-danger';
                      if ($isCancelled) $statusClass = 'bg-danger';

                      $hasId = !empty($bookingId) && trim((string)$bookingId) !== '';
                    ?>
                    <tr
                      data-booking-id="<?= esc($bookingId) ?>"
                      data-client-name="<?= esc($b['ClientName'] ?? '') ?>"
                      data-client-email="<?= esc($b['ClientEmail'] ?? '') ?>"
                      data-property-title="<?= esc($b['PropertyTitle'] ?? '') ?>"
                      data-booking-date="<?= esc($bookingDate) ?>">
                      <td><?= esc($b['ClientName'] ?? '—') ?></td>
                      <td><?= esc($b['ClientEmail'] ?? '—') ?></td>
                      <td><?= esc($b['PropertyTitle'] ?? '—') ?></td>
                      <td><?= esc($bookingDate ?: '—') ?></td>
                      <td>
                        <span class="badge <?= $statusClass ?>"><?= esc($statusText) ?></span>
                      </td>
                      <td>

                        <?php if ($hasId && $isPending): ?>
                          <button class="btn btn-sm btn-outline-success me-1 btn-approve" type="button" title="Confirm">
                            <i class="bi bi-check-circle"></i>
                          </button>
                          <button class="btn btn-sm btn-outline-danger btn-disapprove" type="button" title="Reject">
                            <i class="bi bi-x-circle"></i>
                          </button>
                        <?php else: ?>
                          <button class="btn btn-sm btn-outline-primary me-1 btn-view" type="button" title="View">
                            <i class="bi bi-eye"></i>
                          </button>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6" class="text-center text-muted py-4">No bookings found.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- View Modal -->
  <div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-semibold">Booking Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p><strong>Name:</strong> <span id="modalName"></span></p>
          <p><strong>Email:</strong> <span id="modalEmail"></span></p>
          <p><strong>Property:</strong> <span id="modalProperty"></span></p>
          <p><strong>Date:</strong> <span id="modalDate"></span></p>
          <div id="modalExtra"></div>
        </div>
      </div>
    </div>
  </div>

  <script src="<?= base_url("bootstrap5/js/bootstrap.bundle.min.js")?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
  // Theme Toggle
  function toggleTheme() {
    const html = document.documentElement;
    const themeIcon = document.getElementById('themeIcon');
    const currentTheme = html.getAttribute('data-theme');
    
    if (currentTheme === 'light') {
      html.setAttribute('data-theme', 'dark');
      themeIcon.classList.remove('bi-sun-fill');
      themeIcon.classList.add('bi-moon-fill');
      localStorage.setItem('theme', 'dark');
    } else {
      html.setAttribute('data-theme', 'light');
      themeIcon.classList.remove('bi-moon-fill');
      themeIcon.classList.add('bi-sun-fill');
      localStorage.setItem('theme', 'light');
    }
  }

  // Load saved theme
  document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    const themeIcon = document.getElementById('themeIcon');
    document.documentElement.setAttribute('data-theme', savedTheme);
    
    if (savedTheme === 'dark') {
      themeIcon.classList.remove('bi-sun-fill');
      themeIcon.classList.add('bi-moon-fill');
    }
  });

  // Rest of your JavaScript code remains the same...
  window.updateBookingStatusUrl = <?= json_encode(site_url("users/updateBookingStatus")) ?>;
  window.getBookingUrl = <?= json_encode(site_url("users/getBooking")) ?>;

  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2500,
    timerProgressBar: true
  });

  function escapeHtml(s) {
    if (!s && s !== 0) return '';
    return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]);
  }

  async function showViewModalFromRow(tr) {
    if (!tr) return;
    const bookingId = tr.dataset.bookingId || '';
    const name = tr.dataset.clientName || '—';
    const email = tr.dataset.clientEmail || '—';
    const property = tr.dataset.propertyTitle || '—';
    const date = tr.dataset.bookingDate || '—';

    document.getElementById('modalName').innerText = name;
    document.getElementById('modalEmail').innerText = email;
    document.getElementById('modalProperty').innerText = property;
    document.getElementById('modalDate').innerText = date;
    document.getElementById('modalExtra').innerHTML = '';

    if (bookingId && window.getBookingUrl) {
      const urlGet = window.getBookingUrl.replace(/\/$/, '') + '/' + encodeURIComponent(bookingId);
      try {
        const r = await fetch(urlGet, { credentials: 'same-origin' });
        if (r.ok) {
          const data = await r.json();
          const b = data?.booking ?? data;
          if (b) {
            document.getElementById('modalName').innerText = b.ClientName ?? b.client_name ?? name;
            document.getElementById('modalEmail').innerText = b.ClientEmail ?? b.client_email ?? email;
            document.getElementById('modalProperty').innerText = b.PropertyTitle ?? b.property_title ?? property;
            document.getElementById('modalDate').innerText = b.bookingDate ? new Date(b.bookingDate).toLocaleDateString() : date;
            let html = '';
            if (b.notes) html += `<p><strong>Notes:</strong> ${escapeHtml(b.notes)}</p>`;
            if (b.phone) html += `<p><strong>Phone:</strong> ${escapeHtml(b.phone)}</p>`;
            document.getElementById('modalExtra').innerHTML = html;
          } else {
            Toast.fire({ icon: 'info', title: 'No additional data' });
          }
        } else {
          Toast.fire({ icon: 'info', title: 'Additional details not available' });
        }
      } catch (err) {
        console.error('Failed to fetch booking details', err);
        Toast.fire({ icon: 'error', title: 'Failed to load details' });
      }
    } else if (!bookingId) {
      Toast.fire({ icon: 'info', title: 'Showing basic info only (no booking id)' });
    }

    new bootstrap.Modal(document.getElementById('viewModal')).show();
  }

  async function approveBooking(btn) {
    const tr = btn.closest('tr');
    if (!tr) return;
    const bookingId = tr.dataset.bookingId;
    if (!bookingId) { 
      Swal.fire({ icon: 'error', title: 'Missing ID', text: 'This booking has no id; cannot update.' }); 
      return; 
    }

    const result = await Swal.fire({
      title: 'Approve booking?',
      text: 'Are you sure you want to approve this booking?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Yes, approve',
      cancelButtonText: 'Cancel'
    });
    if (!result.isConfirmed) return;

    toggleRowButtons(tr, true);
    Swal.fire({ title: 'Updating...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    try {
      const payload = { booking_id: bookingId, status: 'scheduled' };
      if (window.updateBookingStatusUrl) {
        const res = await fetch(window.updateBookingStatusUrl, {
          method: 'POST',
          credentials: 'same-origin',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const json = await res.json().catch(()=>null);
        if (!res.ok || !(json && (json.success || json.updated))) {
          throw new Error(json?.error || ('Server returned ' + res.status));
        }
      }
      applyStatusToRow(tr, 'Scheduled');
      Swal.close();
      Toast.fire({ icon: 'success', title: 'Booking approved' });
    } catch (err) {
      Swal.close();
      console.error(err);
      Swal.fire({ icon: 'error', title: 'Failed', text: 'Could not confirm booking. ' + (err.message || '') });
    } finally {
      toggleRowButtons(tr, false);
    }
  }

  async function disapproveBooking(btn) {
    const tr = btn.closest('tr');
    if (!tr) return;
    const bookingId = tr.dataset.bookingId;
    if (!bookingId) { 
      Swal.fire({ icon: 'error', title: 'Missing ID', text: 'This booking has no id; cannot update.' }); 
      return; 
    }

    const { value: reason, isConfirmed } = await Swal.fire({
      title: 'Reject booking?',
      text: 'Provide an optional reason for rejection (leave blank to skip).',
      input: 'text',
      inputPlaceholder: 'Reason (optional)',
      showCancelButton: true,
      confirmButtonText: 'Reject',
      cancelButtonText: 'Cancel'
    });
    if (!isConfirmed) return;

    toggleRowButtons(tr, true);
    Swal.fire({ title: 'Updating...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    try {
      const payload = { booking_id: bookingId, status: 'rejected', reason: reason ?? null };
      if (window.updateBookingStatusUrl) {
        const res = await fetch(window.updateBookingStatusUrl, {
          method: 'POST',
          credentials: 'same-origin',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const json = await res.json().catch(()=>null);
        if (!res.ok || !(json && (json.success || json.updated))) {
          throw new Error(json?.error || ('Server returned ' + res.status));
        }
      }
      applyStatusToRow(tr, 'Rejected');
      Swal.close();
      Toast.fire({ icon: 'success', title: 'Booking rejected' });
    } catch (err) {
      Swal.close();
      console.error(err);
      Swal.fire({ icon: 'error', title: 'Failed', text: 'Could not reject booking. ' + (err.message || '') });
    } finally {
      toggleRowButtons(tr, false);
    }
  }

  function applyStatusToRow(tr, statusText) {
    if (!tr) return;
    const statusCell = tr.querySelector('td:nth-child(5)');
    if (!statusCell) return;
    let cls = 'bg-secondary';
    const st = String(statusText).toLowerCase();
    if (st === 'pending') cls = 'bg-warning';
    if (st === 'scheduled') cls = 'bg-success';
    if (st === 'rejected') cls = 'bg-danger';
    if (st === 'cancelled') cls = 'bg-danger';
    statusCell.innerHTML = `<span class="badge ${cls}">${escapeHtml(statusText)}</span>`;

    const actionsCell = tr.querySelector('td:nth-child(6)');
    if (actionsCell) {
      if (st === 'pending') {
        // keep buttons as-is
      } else {
        actionsCell.innerHTML = '<button class="btn btn-sm btn-outline-primary btn-view" type="button" title="View"><i class="bi bi-eye"></i></button>';
      }
    }
  }

  function toggleRowButtons(tr, disabled) {
    if (!tr) return;
    const buttons = tr.querySelectorAll('button');
    buttons.forEach(b => b.disabled = !!disabled);
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-view').forEach(btn => {
      btn.addEventListener('click', () => { 
        const tr = btn.closest('tr'); 
        showViewModalFromRow(tr); 
      });
    });
    document.querySelectorAll('.btn-approve').forEach(btn => {
      btn.addEventListener('click', () => approveBooking(btn));
    });
    document.querySelectorAll('.btn-disapprove').forEach(btn => {
      btn.addEventListener('click', () => disapproveBooking(btn));
    });
  });
  </script>
</body>
</html>