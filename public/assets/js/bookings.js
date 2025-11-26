
(function () {
  const myBookingsUrl = window.myBookingsUrl || '/index.php/bookings/mine';
  const bookingCreateUrl = window.bookingCreateUrl || '/index.php/bookings/create';
  const csrfName = window.csrfName || null;
  const csrfHash = window.csrfHash || null;

  // render bookings list into #bookingsList
  async function loadMyBookings() {
    const container = document.getElementById('bookingsList');
    if (!container) return;

    container.innerHTML = `<div class="text-center py-4">Loading your bookings…</div>`;

    try {
      const res = await fetch(myBookingsUrl, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      if (!res.ok) throw new Error('Failed to load bookings: ' + res.status);
      const data = await res.json();

      if (!Array.isArray(data) || data.length === 0) {
        container.innerHTML = '<div class="text-center text-muted py-4">You have no bookings yet.<br><small>Book a property from the listing to see it here.</small></div>';
        return;
      }

      container.innerHTML = data.map(renderBookingCard).join('');
      // attach listeners for detail buttons
      container.querySelectorAll('.btn-view-booking').forEach(btn => btn.addEventListener('click', onViewBooking));
      container.querySelectorAll('.btn-cancel-booking').forEach(btn => btn.addEventListener('click', onCancelBooking));
    } catch (err) {
      console.error('loadMyBookings error', err);
      container.innerHTML = `<div class="text-center text-danger py-4">Unable to load bookings. Please refresh the page.</div>`;
    }
  }

  function renderBookingCard(b) {
    const date = b.bookingDate ? new Date(b.bookingDate).toLocaleDateString() : '—';
    const status = b.BookingStatus || b.status || 'Pending';
    const img = (b.Images && b.Images[0]) ? b.Images[0] : (b.Image ? b.Image : 'uploads/properties/no-image.jpg');
    const badgeClass = statusClass(status);
    const notes = b.Notes || b.Notes === 0 ? escapeHtml(b.Notes) : '';

    return `
      <div class="card mb-3">
        <div class="row g-0 align-items-center">
          <div class="col-auto" style="width:140px;">
            <img src="${escapeHtml(img)}" class="img-fluid rounded-start" style="height:100%; object-fit:cover;" alt="Property image">
          </div>
          <div class="col">
            <div class="card-body">
              <h6 class="mb-1">${escapeHtml(b.PropertyTitle || b.Title || 'Property')}</h6>
              <p class="text-muted small mb-1">${escapeHtml(b.PropertyLocation || b.Location || '')}</p>
              <div class="mb-1">
                <span class="badge ${badgeClass}">${escapeHtml(status)}</span>
                <span class="ms-2 small text-muted">${escapeHtml(date)}</span>
              </div>
              <p class="small text-muted mb-0">${notes ? notes : ''}</p>
              ${ (b.Rating !== undefined && b.Rating !== null) ? `<div class="small text-warning mt-1">Rating: ${escapeHtml(String(b.Rating))} &#9733;</div>` : '' }
            </div>
          </div>
          <div class="col-auto pe-3">
            <div class="d-flex flex-column gap-2">
              <button class="btn btn-sm btn-outline-primary btn-view-booking" data-id="${b.bookingID}">Details</button>
              ${ (String(status).toLowerCase() === 'pending') ? `<button class="btn btn-sm btn-danger btn-cancel-booking" data-id="${b.bookingID}">Cancel</button>` : '' }
            </div>
          </div>
        </div>
      </div>
    `;
  }

  function statusClass(status) {
    const s = String(status || '').toLowerCase();
    if (s === 'confirmed' || s === 'confirmed') return 'bg-success text-white';
    if (s === 'pending') return 'bg-warning text-dark';
    if (s === 'rejected' || s === 'cancelled') return 'bg-danger text-white';
    return 'bg-secondary text-white';
  }

  // event handlers
  async function onViewBooking(e) {
    const id = e.currentTarget.dataset.id;
    if (!id) return;
    // Best: fetch fresh booking details (if endpoint exists). We'll rely on the list data for now:
    try {
      // If you have an endpoint /bookings/{id} use it; otherwise find the element's card data from DOM or reload full list and find booking.
      // Simpler: re-fetch all bookings and find selected one
      const res = await fetch(myBookingsUrl, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      if (!res.ok) throw new Error('Failed to load booking');
      const list = await res.json();
      const booking = list.find(b => String(b.bookingID) === String(id));
      if (!booking) throw new Error('Booking not found');

      populateBookingModal(booking);
      const modalEl = document.getElementById('bookingDetailModal');
      const modal = new bootstrap.Modal(modalEl);
      modal.show();
    } catch (err) {
      console.error('Failed to show booking', err);
      Swal.fire({ icon: 'error', title: 'Error', text: 'Unable to load booking details.' });
    }
  }
function populateBookingModal(b) {
  // basic fields
  document.getElementById('bookingModalTitle').textContent = 'Booking #' + (b.bookingID || '');
  document.getElementById('bookingModalPropertyTitle').textContent = b.PropertyTitle || b.Title || 'Property';
  document.getElementById('bookingModalLocation').textContent = b.PropertyLocation || b.Location || '';
  document.getElementById('bookingModalDate').textContent = (b.bookingDate ? new Date(b.bookingDate).toLocaleString() : '—');

  const status = b.BookingStatus || b.status || 'Pending';
  const statusEl = document.getElementById('bookingModalStatus');
  statusEl.textContent = status;
  statusEl.className = 'badge ' + statusClass(status);

  // image and price
  const img = (b.Images && b.Images[0]) ? b.Images[0] : (b.Image ? b.Image : 'uploads/properties/no-image.jpg');
  const imgEl = document.getElementById('bookingModalImage');
  if (imgEl) imgEl.src = img;

  document.getElementById('bookingModalPrice').textContent = b.PropertyPrice ? `₱${Number(b.PropertyPrice).toLocaleString()}` : (b.Price ? `₱${Number(b.Price).toLocaleString()}` : '—');
  document.getElementById('bookingModalNotes').textContent = b.Notes || b.Reason || 'No notes provided.';

  // --- AGENT INFO: prefer server-provided fields if present ---
  // server keys: agent_id, agent_name, agent_phone, agent_email
  // booking payload keys used previously: assigned_agent, agent_name
  const agentId = b.agent_id ?? b.assigned_agent ?? b.Agent_Assigned ?? null;
  const agentName = b.agent_name ?? b.assigned_agent_name ?? b.assigned_agent ?? null;
  const agentPhone = b.agent_phone ?? b.agent_contact ?? b.agentPhone ?? '';
  const agentEmail = b.agent_email ?? b.agent_contact_email ?? b.agentEmail ?? '';

  // populate agent fields in modal
  const agentEl = document.getElementById('bookingModalAgent');
  if (agentEl) agentEl.textContent = agentName || (agentId ? String(agentId) : 'Unassigned');

  const phoneEl = document.getElementById('bookingModalAgentPhone');
  if (phoneEl) {
    if (agentPhone) {
      // clickable tel link
      phoneEl.innerHTML = `<a href="tel:${escapeHtml(agentPhone)}" class="text-decoration-none">${escapeHtml(agentPhone)}</a>`;
    } else {
      phoneEl.textContent = '—';
    }
  }

  const emailEl = document.getElementById('bookingModalAgentEmail');
  if (emailEl) {
    if (agentEmail) {
      emailEl.innerHTML = `<a href="mailto:${escapeHtml(agentEmail)}" class="text-decoration-none">${escapeHtml(agentEmail)}</a>`;
    } else {
      emailEl.textContent = '—';
    }
  }

  // store agent id in a hidden input for actions
  const agentIdInput = document.getElementById('bookingModalAgentId');
  if (agentIdInput) agentIdInput.value = agentId ?? '';

  // show/hide cancel button depending on status
  const cancelBtn = document.getElementById('modalCancelBookingBtn');
  if (String(status).toLowerCase() === 'pending') {
    cancelBtn.style.display = '';
    cancelBtn.dataset.id = b.bookingID;
    cancelBtn.onclick = onCancelBooking;
  } else {
    cancelBtn.style.display = 'none';
    cancelBtn.onclick = null;
  }

  // contact agent button action:
  const contactBtn = document.getElementById('modalContactAgentBtn');
  if (contactBtn) {
    contactBtn.onclick = () => {
      // prefer starting a chat with agent id if available
      if (agentId) {
        // passes agent + property to chat; adjust query params to your chat implementation
        window.location.href = `/users/chat?agent=${encodeURIComponent(agentId)}&property=${encodeURIComponent(b.PropertyID || b.property_id || '')}`;
        return;
      }
      // otherwise prefer mailto if email available
      if (agentEmail) {
        window.location.href = `mailto:${encodeURIComponent(agentEmail)}?subject=${encodeURIComponent('Inquiry about ' + (b.PropertyTitle || 'property'))}`;
        return;
      }
      // fallback: open general chat page
      window.location.href = '/users/chat';
    };
  }

  // view property button
  const viewPropBtn = document.getElementById('modalViewPropertyBtn');
  if (viewPropBtn) {
    viewPropBtn.onclick = () => {
      if (b.PropertyID) {
        window.location.href = '/properties/view/' + encodeURIComponent(b.PropertyID);
      } else {
        bootstrap.Modal.getInstance(document.getElementById('bookingDetailModal'))?.hide();
        window.location.href = '/users/clientbrowse';
      }
    };
  }
  // Populate rating in modal (if provided by server)
  const ratingEl = document.getElementById('bookingModalRating');
  if (ratingEl) {
    if (b.Rating !== undefined && b.Rating !== null && b.Rating !== '') {
      ratingEl.textContent = String(b.Rating) + ' ★';
    } else {
      ratingEl.textContent = '—';
    }
  }
}

  async function onCancelBooking(e) {
    const bookingID = e.currentTarget?.dataset?.id || e?.currentTarget?.getAttribute('data-id');
    if (!bookingID) return;

    const confirmed = await Swal.fire({
      title: 'Cancel booking?',
      text: 'Are you sure you want to cancel this booking? This action cannot be undone.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, cancel',
      cancelButtonText: 'Keep booking'
    });

    if (!confirmed.isConfirmed) return;

    // POST cancel to server. Create route /bookings/cancel that accepts booking_id and sets status to 'Cancelled'
    try {
      const payload = new URLSearchParams();
        payload.append('booking_id', bookingID);
        payload.append('status', 'Cancelled');
        if (window.csrfName && window.csrfHash) payload.append(window.csrfName, window.csrfHash);

        const res = await fetch(window.bookingCancelUrl || '/index.php/bookings/cancel', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: payload.toString()
        });

      const json = await res.json().catch(()=>null);
      if (!res.ok || json?.error) throw new Error(json?.error || 'Failed to cancel');

      Swal.fire({ icon: 'success', title: 'Cancelled', text: 'Your booking was cancelled.' });
      // refresh list and close modal
      loadMyBookings();
      bootstrap.Modal.getInstance(document.getElementById('bookingDetailModal'))?.hide();
    } catch (err) {
      console.error('Cancel booking failed', err);
      Swal.fire({ icon: 'error', title: 'Failed', text: err.message || 'Unable to cancel booking.' });
    }
  }

  // utility
  function escapeHtml(s) {
    if (s === undefined || s === null) return '';
    return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c]);
  }

  // initialize on DOM ready
  document.addEventListener('DOMContentLoaded', () => {
    loadMyBookings();
  });

  // expose for manual refresh
  window.loadMyBookings = loadMyBookings;
})();