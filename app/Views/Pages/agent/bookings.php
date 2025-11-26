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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
  <div class="container-fluid mt-5 pt-4">
    <div class="card p-4 border-0 shadow-sm animate__animated animate__fadeInUp">
      <h4 class="fw-semibold mb-2">Manage Bookings</h4>
      <p class="text-muted mb-3">View and update all bookings below.</p>

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
                <?php
                  // booking id fallbacks - adjust if your model returns a different key
                  $bookingId = $b['bookingID'] ?? $b['BookingID'] ?? $b['id'] ?? $b['booking_id'] ?? '';

                  // bookingDate
                  $bookingDateRaw = $b['bookingDate'] ?? $b['booking_date'] ?? $b['date'] ?? '';
                  $bookingDate = $bookingDateRaw ? date('Y-m-d', strtotime($bookingDateRaw)) : '';

                  // status normalization
                  $statusText = $b['BookingStatus'] ?? $b['status'] ?? 'Pending';
                  $statusLower = strtolower(trim($statusText));
                  $isPending = ($statusLower === 'pending');
                  $isConfirmed = ($statusLower === 'confirmed');
                  $isRejected = ($statusLower === 'rejected');
                  $isCancelled = ($statusLower === 'cancelled');

                  // badge class mapping
                  $statusClass = 'bg-secondary text-white';
                  if ($isPending) $statusClass = 'bg-warning text-dark';
                  if ($isConfirmed) $statusClass = 'bg-success text-white';
                  if ($isRejected) $statusClass = 'bg-danger text-white';
                  if ($isCancelled) $statusClass = 'bg-danger text-white';

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
                    <button class="btn btn-sm btn-outline-primary me-1 btn-view" type="button" title="View">
                      <i class="bi bi-eye"></i>
                    </button>

                    

                    <?php if ($hasId && $isPending): ?>
                      <!-- Only pending bookings have Approve / Reject -->
                      <button class="btn btn-sm btn-outline-success me-1 btn-approve" type="button" title="Confirm">
                        <i class="bi bi-check-circle"></i>
                      </button>
                      <button class="btn btn-sm btn-outline-danger btn-disapprove" type="button" title="Reject">
                        <i class="bi bi-x-circle"></i>
                      </button>
                    <?php else: ?>
                      <!-- Finalized bookings: only view -->
                      <span class="text-muted small"></span>
                    <?php endif; ?>
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

  <!-- View Modal -->
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
          <div id="modalExtra"></div>
        </div>
      </div>
    </div>
  </div>

  

  <!-- Scripts -->
  <script src="<?= base_url("bootstrap5/js/bootstrap.bundle.min.js")?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
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
      if (!bookingId) { Swal.fire({ icon: 'error', title: 'Missing ID', text: 'This booking has no id; cannot update.' }); return; }

      const result = await Swal.fire({
        title: 'Confirm booking?',
        text: 'Are you sure you want to confirm this booking?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, confirm',
        cancelButtonText: 'Cancel'
      });
      if (!result.isConfirmed) return;

      toggleRowButtons(tr, true);
      Swal.fire({ title: 'Updating...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

      try {
        const payload = { booking_id: bookingId, status: 'confirmed' }; // send lowercase; controller normalizes
        if (window.updateBookingStatusUrl) {
          const res = await fetch(window.updateBookingStatusUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
          });
          const json = await res.json().catch(()=>null);
          if (!res.ok || !(json && (json.success || json.updated))) throw new Error(json?.error || ('Server returned ' + res.status));
        }
        applyStatusToRow(tr, 'Confirmed');
        Swal.close();
        Toast.fire({ icon: 'success', title: 'Booking confirmed' });
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
      if (!bookingId) { Swal.fire({ icon: 'error', title: 'Missing ID', text: 'This booking has no id; cannot update.' }); return; }

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
          if (!res.ok || !(json && (json.success || json.updated))) throw new Error(json?.error || ('Server returned ' + res.status));
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
      let cls = 'bg-secondary text-white';
      const st = String(statusText).toLowerCase();
      if (st === 'pending') cls = 'bg-warning text-dark';
      if (st === 'confirmed') cls = 'bg-success text-white';
      if (st === 'rejected') cls = 'bg-danger text-white';
      if (st === 'cancelled') cls = 'bg-danger text-white';
      statusCell.innerHTML = `<span class="badge ${cls}">${escapeHtml(statusText)}</span>`;

      // Adjust actions after status change: if no longer pending, replace action buttons with "Finalized"
      const actionsCell = tr.querySelector('td:nth-child(6)');
      if (actionsCell) {
        if (st === 'pending') {
          // keep buttons as-is
        } else {
          actionsCell.innerHTML = '<span class="text-muted small"></span>';
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
        btn.addEventListener('click', () => { const tr = btn.closest('tr'); showViewModalFromRow(tr); });
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