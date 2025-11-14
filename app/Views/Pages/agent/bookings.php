<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agent Bookings</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= base_url("bootstrap5/css/bootstrap.min.css")?>" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url("assets/styles/agenStyle.css")?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>
<body>
  <nav class="navbar navbar-light fixed-top shadow-sm bg-white border-bottom">
    <div class="container-fluid d-flex justify-content-between align-items-center px-4">
      <h3 class="mb-0 text-secondary fw-semibold">Agent Bookings</h3>
      <ul class="nav nav-tabs border-0 flex-nowrap">
        <li class="nav-item"><a class="nav-link" href="/users/agentHomepage">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="/users/agentclients">Clients</a></li>
        <li class="nav-item"><a class="nav-link active" href="/users/agentbookings">Bookings</a></li>
        <li class="nav-item"><a class="nav-link" href="/users/agentproperties">Properties</a></li>
        <li class="nav-item"><a class="nav-link" href="/users/agentchat">Chat</a></li>
      </ul>
      <a href="/users/agentprofile"> <button class="btn btn-outline-primary btn-sm"> Profile</button></a>
    </div>
  </nav>
<br>
  <!-- ✅ Main Content -->
  <div class="container-fluid mt-5 pt-4">
    <div class="card p-4 border-0 shadow-sm animate__animated animate__fadeInUp">
      <h4 class="fw-semibold mb-2">Manage Bookings</h4>
      <p class="text-muted mb-3">View and update all bookings below.</p>

      <!-- Bookings Table -->
      <div class="table-responsive">
        <table class="table align-middle text-center">
          <thead class="table-light">
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
                <tr>
                  <td><?= esc($b['ClientName']) ?></td>
                  <td><?= esc($b['ClientEmail']) ?></td>
                  <td><?= esc($b['PropertyTitle']) ?></td>
                  <td><?= date('Y-m-d', strtotime($b['bookingDate'])) ?></td>
                  <td>
                    <?php
                      $statusClass = 'bg-warning text-dark';
                      if($b['BookingStatus'] === 'Approved') $statusClass = 'bg-success';
                      if($b['BookingStatus'] === 'Disapproved') $statusClass = 'bg-danger';
                    ?>
                    <span class="badge <?= $statusClass ?>"><?= esc($b['BookingStatus']) ?></span>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-outline-primary me-1"
                            onclick="viewBooking('<?= esc($b['ClientName']) ?>', '<?= esc($b['ClientEmail']) ?>', '<?= esc($b['PropertyTitle']) ?>', '<?= date('Y-m-d', strtotime($b['bookingDate'])) ?>')">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-success me-1" onclick="approveBooking(this)">
                      <i class="bi bi-check-circle"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="disapproveBooking(this)">
                      <i class="bi bi-x-circle"></i>
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="text-center text-muted">No bookings found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>

  <!-- ✅ View Modal -->
  <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content rounded-4">
        <div class="modal-header">
          <h5 class="modal-title fw-semibold" id="viewModalLabel">Booking Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p><strong>Name:</strong> <span id="modalName"></span></p>
          <p><strong>Email:</strong> <span id="modalEmail"></span></p>
          <p><strong>Property:</strong> <span id="modalProperty"></span></p>
          <p><strong>Date:</strong> <span id="modalDate"></span></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
 <script src="<?= base_url("bootstrap5/js/bootstrap.bundle.min.js")?>"></script>
  <script>
    // View modal
    function viewBooking(name, email, property, date) {
      document.getElementById('modalName').innerText = name;
      document.getElementById('modalEmail').innerText = email;
      document.getElementById('modalProperty').innerText = property;
      document.getElementById('modalDate').innerText = date;
      new bootstrap.Modal(document.getElementById('viewModal')).show();
    }

    // Approve
    function approveBooking(btn) {
      if (confirm("Are you sure you want to approve this booking?")) {
        const row = btn.closest('tr');
        const statusCell = row.querySelector('td:nth-child(5)');
        statusCell.innerHTML = '<span class="badge bg-success">Approved</span>';
      }
    }

    // Disapprove
    function disapproveBooking(btn) {
      if (confirm("Are you sure you want to disapprove this booking?")) {
        const row = btn.closest('tr');
        const statusCell = row.querySelector('td:nth-child(5)');
        statusCell.innerHTML = '<span class="badge bg-danger">Disapproved</span>';
      }
    }
  </script>
</body>
</html>
