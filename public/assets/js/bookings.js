
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

      // page mode: 'reservations' => show pending/confirmed (reservations)
      // 'bookings' => show cancelled or viewing (default client bookings page)
      const mode = window.bookingsMode || 'bookings';
      let filtered = data;
      if (Array.isArray(data)) {
        if (mode === 'reservations') {
          filtered = data.filter(b => {
            const s = String(b.BookingStatus || b.status || '').toLowerCase();
            return s === 'pending' || s === 'confirmed' || s === 'reserved';
          });
        } else {
          // bookings page shows cancelled or viewing (or other non-active statuses)
          filtered = data.filter(b => {
            const s = String(b.BookingStatus || b.status || '').toLowerCase();
            return s === 'cancelled' || s === 'rejected' || s === 'viewing' || s === 'completed';
          });
        }
      }

      // Update reservation counters when on reservations page
      try {
        if (window.bookingsMode === 'reservations') {
          const totalCount = Array.isArray(filtered) ? filtered.length : 0;
          const scheduledCount = Array.isArray(filtered) ? filtered.filter(x => String((x.BookingStatus||x.status||'')).toLowerCase() === 'confirmed').length : 0;
          const pendingCount = Array.isArray(filtered) ? filtered.filter(x => String((x.BookingStatus||x.status||'')).toLowerCase() === 'pending').length : 0;
          const totalEl = document.getElementById('totalReservationsCount');
          const scheduledEl = document.getElementById('scheduledReservationsCount');
          const pendingEl = document.getElementById('pendingReservationsCount');
          if (totalEl) totalEl.textContent = String(totalCount);
          if (scheduledEl) scheduledEl.textContent = String(scheduledCount);
          if (pendingEl) pendingEl.textContent = String(pendingCount);
        }
      } catch (e) {
        console.warn('Failed to update reservation counters', e);
      }

      if (!Array.isArray(filtered) || filtered.length === 0) {
        const emptyMsg = (window.bookingsMode === 'reservations')
          ? '<div class="text-center text-muted py-4">You have no reservations (pending or confirmed).</div>'
          : '<div class="text-center text-muted py-4">You have no bookings to display.</div>';
        container.innerHTML = emptyMsg;
        return;
      }

      container.innerHTML = filtered.map(renderBookingCard).join('');
      // attach listeners for detail buttons
      container.querySelectorAll('.btn-view-booking').forEach(btn => btn.addEventListener('click', onViewBooking));
      container.querySelectorAll('.btn-cancel-booking').forEach(btn => btn.addEventListener('click', onCancelBooking));
      container.querySelectorAll('.btn-confirm-contract').forEach(btn => btn.addEventListener('click', onConfirmContract));
    } catch (err) {
      console.error('loadMyBookings error', err);
      container.innerHTML = `<div class="text-center text-danger py-4">Unable to load bookings. Please refresh the page.</div>`;
    }
  }

  function renderBookingCard(b) {
    // Clients cannot assign booking dates; do not display booking date on client view
    const date = '—';
    const status = b.BookingStatus || b.status || 'Pending';
    // Display label: treat 'Confirmed' as 'Scheduled' for clients
    const _s = String(status || '').toLowerCase();
    const displayStatus = _s === 'confirmed' ? 'Scheduled' : (status ? String(status).charAt(0).toUpperCase() + String(status).slice(1) : 'Pending');
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
                <span class="badge ${badgeClass}">${escapeHtml(displayStatus)}</span>
              </div>
              <p class="small text-muted mb-0">${notes ? notes : ''}</p>
            </div>
          </div>
          <div class="col-auto pe-3">
                <div class="d-flex flex-column gap-2">
                  <button class="btn btn-sm btn-outline-primary btn-view-booking" data-id="${b.bookingID}">Details</button>
                  ${ (['pending','confirmed'].includes(String(status).toLowerCase())) ? `<button class="btn btn-sm btn-danger btn-cancel-booking" data-id="${b.bookingID}">Cancel</button>` : '' }
                  ${ (String(status).toLowerCase() === 'confirmed') ? `<button class="btn btn-sm btn-success btn-confirm-contract" data-id="${b.bookingID}" data-price="${b.PropertyPrice || b.Price || 0}">Confirm Contract</button>` : '' }
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

  const status = b.BookingStatus || b.status || 'Pending';
  // Map confirmed -> Scheduled for UI label
  const statusDisplay = String(status || '').toLowerCase() === 'confirmed' ? 'Scheduled' : (status ? String(status).charAt(0).toUpperCase() + String(status).slice(1) : 'Pending');
  const statusEl = document.getElementById('bookingModalStatus');
  statusEl.textContent = statusDisplay;
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
  if (['pending','confirmed'].includes(String(status).toLowerCase())) {
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

  // Confirm contract flow (client-side)
  async function onConfirmContract(e) {
    const bookingID = e.currentTarget?.dataset?.id;
    const price = Number(e.currentTarget?.dataset?.price || 0);
    if (!bookingID) return;

    // populate modal
    document.getElementById('contractPropertyPrice').textContent = price ? `₱${Number(price).toLocaleString()}` : '—';
    document.getElementById('contractMonthly').textContent = '—';
    document.getElementById('contractErrors').style.display = 'none';
    document.getElementById('contractClientAge').textContent = '…';

    // open modal
    const modalEl = document.getElementById('confirmContractModal');
    const modal = new bootstrap.Modal(modalEl);

    // fetch client age using currentUserId and getUserUrlBase
    let age = null;
    try {
      const uid = window.currentUserId;
      if (uid && window.getUserUrlBase) {
        const res = await fetch(`${window.getUserUrlBase}/${encodeURIComponent(uid)}`, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (res.ok) {
          const u = await res.json();
          const birth = u.Birthdate ?? u.birthdate ?? u.BirthDate ?? u.Birthdate ?? null;
          if (birth) {
            const dob = new Date(birth);
            const now = new Date();
            age = now.getFullYear() - dob.getFullYear();
            const m = now.getMonth() - dob.getMonth();
            if (m < 0 || (m === 0 && now.getDate() < dob.getDate())) age--;
          }
        }
      }
    } catch (err) {
      console.warn('Failed to fetch user for age', err);
    }

    document.getElementById('contractClientAge').textContent = age !== null ? String(age) : '—';

    // attach compute handler
    const radios = Array.from(document.querySelectorAll('input[name="contractMode"]'));
    function compute() {
      const sel = radios.find(r => r.checked)?.value || null;
      const errorsEl = document.getElementById('contractErrors');
      errorsEl.style.display = 'none';
      if (!sel) {
        document.getElementById('contractMonthly').textContent = '—';
        return;
      }

      if (sel === 'full') {
        document.getElementById('contractMonthly').textContent = `₱${Number(price).toLocaleString()}`;
        return;
      }

      const maxYears = sel === 'pagibig' ? 60 : (sel === 'banko' ? 30 : 0);
      if (age === null) {
        errorsEl.textContent = 'Unable to determine age. Please update your profile birthdate.';
        errorsEl.style.display = '';
        return;
      }

      const years = maxYears - Number(age);
      if (years <= 0) {
        errorsEl.textContent = `Not eligible for ${sel} (age exceeds maximum).`;
        errorsEl.style.display = '';
        document.getElementById('contractMonthly').textContent = '—';
        return;
      }

      const months = years * 12;
      if (months <= 0) {
        document.getElementById('contractMonthly').textContent = '—';
        return;
      }

      const perMonth = Number(price) / months;
      document.getElementById('contractMonthly').textContent = `₱${perMonth.toFixed(2).toLocaleString ? Number(perMonth.toFixed(2)).toLocaleString() : perMonth.toFixed(2)}`;
    }

    radios.forEach(r => r.addEventListener('change', compute));

    // Confirm button handler (client-side only for now)
    const confirmBtn = document.getElementById('confirmContractBtn');
    const onConfirm = async () => {
      const sel = radios.find(r => r.checked)?.value || null;
      if (!sel) {
        const errorsEl = document.getElementById('contractErrors');
        errorsEl.textContent = 'Please choose a payment mode.';
        errorsEl.style.display = '';
        return;
      }

      // compute final per-month again
      const maxYears = sel === 'pagibig' ? 60 : (sel === 'banko' ? 30 : 0);
      let perMonth = null;
      if (sel === 'full') {
        perMonth = Number(price);
      } else {
        const years = maxYears - Number(age || 0);
        if (years <= 0) {
          const errorsEl = document.getElementById('contractErrors');
          errorsEl.textContent = 'Not eligible for this loan mode.';
          errorsEl.style.display = '';
          return;
        }
        perMonth = Number(price) / (years * 12);
      }

      // Persist proposal to server so agent can review/confirm
      try {
        const params = new URLSearchParams();
        params.append('booking_id', bookingID);
        params.append('mode', sel);
        params.append('monthly', perMonth.toFixed(2));
        if (window.csrfName && window.csrfHash) params.append(window.csrfName, window.csrfHash);

        const res = await fetch('/index.php/bookings/proposeContract', {
          method: 'POST',
          credentials: 'same-origin',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: params.toString()
        });

        const json = await res.json().catch(()=>null);
        if (!res.ok || json?.error) throw new Error(json?.error || 'Failed to persist contract');

        Swal.fire({
          icon: 'success',
          title: 'Contract Proposal Sent',
          html: `<p>Mode: <strong>${sel}</strong></p><p>Monthly: <strong>₱${perMonth.toFixed(2)}</strong></p>`
        });

        // detach handler to avoid duplicates
        confirmBtn.removeEventListener('click', onConfirm);
        radios.forEach(r => r.removeEventListener('change', compute));
        modal.hide();
        // refresh bookings to show updated state if any
        loadMyBookings();
      } catch (err) {
        console.error('Persist contract failed', err);
        Swal.fire({ icon: 'error', title: 'Failed', text: err.message || 'Unable to save contract proposal.' });
      }
    };

    confirmBtn.addEventListener('click', onConfirm);

    modal.show();
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