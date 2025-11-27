
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
      // Build fetch URL: include mode query param when page indicates reservations mode
      let fetchUrl = myBookingsUrl;
      try {
        const mode = window.bookingsMode || null;
        if (mode) {
          // only append when URL doesn't already include the mode param
          const urlObj = new URL(fetchUrl, window.location.origin);
          if (!urlObj.searchParams.has('mode')) {
            urlObj.searchParams.set('mode', mode === 'reservations' ? 'reservations' : 'bookings');
            fetchUrl = urlObj.toString();
          } else {
            fetchUrl = urlObj.toString();
          }
        }
      } catch (e) {
        // fallback: append simple query string
        if (window.bookingsMode === 'reservations' && fetchUrl.indexOf('?') === -1) fetchUrl += '?mode=reservations';
        else if (window.bookingsMode === 'reservations') fetchUrl += '&mode=reservations';
      }

      const res = await fetch(fetchUrl, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
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
            const reason = String(b.Reason || b.reason || '').toLowerCase();
            // Exclude any bookings that are viewing-purpose (even if status is 'pending')
            if (reason.includes('view')) return false;
            // include scheduled bookings intended for reservation
            return s === 'pending' || s === 'confirmed' || s === 'reserved' || s === 'scheduled';
          });
        } else {
          // bookings page shows cancelled, viewing, scheduled, or other non-active statuses
          // include pending bookings that were created for "viewing" purpose
          filtered = data.filter(b => {
            const s = String(b.BookingStatus || b.status || '').toLowerCase();
            const reason = String(b.Reason || b.reason || '').toLowerCase();
            if (s === 'pending' && reason.includes('view')) return true;
            // show scheduled bookings here too
            return s === 'cancelled' || s === 'rejected' || s === 'viewing' || s === 'completed' || s === 'scheduled' || s === 'scheduled';
          });
        }
      }

      // Update reservation counters when on reservations page
      try {
        if (window.bookingsMode === 'reservations') {
          const totalCount = Array.isArray(filtered) ? filtered.length : 0;
          const scheduledCount = Array.isArray(filtered) ? filtered.filter(x => String((x.BookingStatus||x.status||'')).toLowerCase() === 'scheduled').length : 0;
          const pendingCount = Array.isArray(filtered) ? filtered.filter(x => String((x.BookingStatus||x.status||'')).toLowerCase() === 'pending').length : 0;
          const totalEl = document.getElementById('totalReservationsCount');
          const scheduledEl = document.getElementById('scheduledReservationsCount');
          const pendingEl = document.getElementById('pendingReservationsCount');
          if (totalEl) totalEl.textContent = String(totalCount);
          if (scheduledEl) scheduledEl.textContent = String(scheduledCount);
          if (pendingEl) pendingEl.textContent = String(pendingCount);
        }
        // Update booking counters when on bookings page
        if (window.bookingsMode === 'bookings') {
          const totalCountB = Array.isArray(filtered) ? filtered.length : 0;
          const confirmedCountB = Array.isArray(filtered) ? filtered.filter(x => String((x.BookingStatus||x.status||'')).toLowerCase() === 'scheduled').length : 0;
          // Upcoming: treat confirmed/scheduled as upcoming; user requested upcoming to reflect confirmed
          const upcomingCountB = confirmedCountB;
          const totalElB = document.getElementById('totalBookingsCount');
          const confirmedElB = document.getElementById('confirmedBookingsCount');
          const upcomingElB = document.getElementById('upcomingBookingsCount');
          if (totalElB) totalElB.textContent = String(totalCountB);
          if (confirmedElB) confirmedElB.textContent = String(confirmedCountB);
          if (upcomingElB) upcomingElB.textContent = String(upcomingCountB);
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
      // attach listeners for view property buttons (open property details modal)
      container.querySelectorAll('.btn-view-property').forEach(btn => btn.addEventListener('click', async (ev) => {
        ev.stopPropagation();
        const propId = btn.dataset.propertyId || btn.getAttribute('data-property-id');
        if (!propId) return;
        try {
          const res = await fetch((window.propertiesViewUrl || '/index.php/properties/view') + '/' + encodeURIComponent(propId), { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
          if (!res.ok) throw new Error('Failed to load property');
          const property = await res.json();
          // reuse client.js's modal updater if available
          if (window.updatePropertyModal) {
            window.updatePropertyModal(property);
            const modalEl = document.getElementById('propertyDetailsModal');
            if (modalEl) new bootstrap.Modal(modalEl).show();
          } else if (window.openPropertyDetails) {
            window.openPropertyDetails(propId);
          } else {
            // fallback: navigate to property view page
            window.location.href = '/properties/view/' + encodeURIComponent(propId);
          }
        } catch (err) {
          console.error('Failed to open property details', err);
          if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', text: 'Unable to load property details.' });
        }
      }));
      container.querySelectorAll('.btn-cancel-booking').forEach(btn => btn.addEventListener('click', onCancelBooking));
      container.querySelectorAll('.btn-confirm-contract').forEach(btn => btn.addEventListener('click', onConfirmContract));
    } catch (err) {
      console.error('loadMyBookings error', err);
      container.innerHTML = `<div class="text-center text-danger py-4">Unable to load bookings. Please refresh the page.</div>`;
    }
  }

  function renderBookingCard(b) {
    // Show booking date if available
    const date = b.bookingDate ? new Date(b.bookingDate).toLocaleDateString() : '—';
    const status = b.BookingStatus || b.status || 'Pending';
    // Display label: treat 'Confirmed' as 'Scheduled' for clients
    const _s = String(status || '').toLowerCase();
    const displayStatus = _s === 'confirmed' ? 'Scheduled' : (status ? String(status).charAt(0).toUpperCase() + String(status).slice(1) : 'Pending');
    const img = (b.Images && b.Images[0]) ? b.Images[0] : (b.Image ? b.Image : 'uploads/properties/no-image.jpg');
    const badgeClass = statusClass(status);
    const notes = b.Notes || b.Notes === 0 ? escapeHtml(b.Notes) : '';
    const reason = b.Reason ? `Purpose: ${escapeHtml(b.Reason)}` : '';

    // Build a safe absolute image URL (preserve absolute URLs from backend)
    let imgUrl = '';
    try {
      if (/^https?:\/\//i.test(img)) imgUrl = img;
      else imgUrl = (window.location.origin.replace(/\/$/, '') + '/' + String(img).replace(/^\/+/, ''));
    } catch (e) {
      imgUrl = img;
    }

    return `
      <div class="card mb-3">
        <div class="row g-0 align-items-center">
          <div class="col-auto" style="width:140px;">
            <img src="${escapeHtml(imgUrl)}" class="img-fluid rounded-start" style="height:100%; object-fit:cover;" alt="Property image">
          </div>
          <div class="col">
            <div class="card-body">
              <h6 class="mb-1">${escapeHtml(b.PropertyTitle || b.Title || 'Property')}</h6>
              <p class="text-muted small mb-1">${escapeHtml(b.PropertyLocation || b.Location || '')}</p>
              <div class="mb-1">
                <span class="badge ${badgeClass}">${escapeHtml(displayStatus)}</span>
                <span class="ms-2 text-muted small">Date: ${escapeHtml(date)}</span>
              </div>
              <p class="small text-muted mb-0">${notes ? notes : ''}</p>
              ${ (b.Rating !== undefined && b.Rating !== null) ? `<div class="small text-warning mt-1">Rating: ${escapeHtml(String(b.Rating))} &#9733;</div>` : '' }
            </div>
          </div>
          <div class="col-auto pe-3">
                <div class="d-flex flex-column gap-2">
                  <button class="btn btn-sm btn-outline-primary btn-view-booking" data-id="${escapeHtml(b.bookingID)}">Details</button>
                  <button class="btn btn-sm btn-outline-secondary btn-view-property" data-property-id="${escapeHtml(b.PropertyID || b.property_id || '')}">View Property</button>
                </div>
          </div>
        </div>
      </div>
    `;
  }

  function statusClass(status) {
    const s = String(status || '').toLowerCase();
    if (s === 'confirmed' || s === 'scheduled') return 'bg-success text-white';
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
      // Try a dedicated endpoint that returns a single booking by id
      const detailUrl = '/bookings/' + encodeURIComponent(id);
      const res = await fetch(detailUrl, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      if (res.ok) {
        const booking = await res.json();
        populateBookingModal(booking);
        const modalEl = document.getElementById('bookingDetailModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
        return;
      }

      // Fallback: re-fetch full list and find selected one (older behaviour)
      const listRes = await fetch(myBookingsUrl, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      if (!listRes.ok) throw new Error('Failed to load booking');
      const list = await listRes.json();
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
  // helper to detect if a reservation/booking has a contract file
  const detectHasContract = (obj) => {
    if (!obj) return false;
    // common direct fields
    const direct = obj.ContractFile || obj.contract_file || obj.contractPath || obj.Contract_Path || obj.ContractFilePath || obj.contract_file_path || obj.contractFile || obj.contractFilePath || obj.contractPDF || obj.contractpdf || obj.contractPdf;
    if (direct) return true;

    // check common arrays that might contain attachments
    const listKeys = ['Files', 'files', 'attachments', 'Attachments', 'Documents', 'documents'];
    for (const k of listKeys) {
      const list = obj[k];
      if (Array.isArray(list) && list.length) {
        for (const item of list) {
          const name = (item && (item.filename || item.name || item.path || item.url)) ? (item.filename || item.name || item.path || item.url) : String(item);
          if (/contract|agreement|signed/i.test(String(name))) return true;
        }
      }
    }

    // scan string values for a likely contract/pdf url or filename
    for (const v of Object.values(obj)) {
      if (typeof v === 'string' && /\.pdf$/i.test(v) && /contract|agreement|signed|filled/i.test(v)) return true;
    }

    return false;
  };
  // helper: safely set textContent if element exists
  const setText = (id, value) => {
    const el = document.getElementById(id);
    if (!el) return;
    el.textContent = value ?? '';
  };

  // basic fields
  setText('bookingModalTitle', 'Booking #' + (b.bookingID || ''));
  setText('bookingModalPropertyTitle', b.PropertyTitle || b.Title || 'Property');
  setText('bookingModalLocation', b.PropertyLocation || b.Location || '');

  const status = b.BookingStatus || b.status || 'Pending';
  // Map confirmed -> Scheduled for UI label
  const statusDisplay = String(status || '').toLowerCase() === 'confirmed' ? 'Scheduled' : (status ? String(status).charAt(0).toUpperCase() + String(status).slice(1) : 'Pending');
  const statusEl = document.getElementById('bookingModalStatus');
  if (statusEl) {
    statusEl.textContent = statusDisplay;
    statusEl.className = 'badge ' + statusClass(status);
  }

  // image and price
  const img = (b.Images && b.Images[0]) ? b.Images[0] : (b.Image ? b.Image : 'uploads/properties/no-image.jpg');
  const imgEl = document.getElementById('bookingModalImage');
  if (imgEl) imgEl.src = img;

  setText('bookingModalPrice', b.PropertyPrice ? `₱${Number(b.PropertyPrice).toLocaleString()}` : (b.Price ? `₱${Number(b.Price).toLocaleString()}` : '—'));
  setText('bookingModalNotes', b.Notes || b.Reason || 'No notes provided.');

  // Booking date/time: accept various server keys and format for display
  const rawDate = b.booking_date ?? b.BookingDate ?? b.scheduled_at ?? b.scheduledAt ?? b.date ?? null;
  const dateEl = document.getElementById('bookingModalDate');
  if (dateEl) {
    if (rawDate) {
      // Try to parse ISO-like strings, otherwise show raw
      let formatted = rawDate;
      try {
        const d = new Date(rawDate);
        if (!isNaN(d.getTime())) {
          formatted = d.toLocaleString();
        }
      } catch (e) {
        // leave formatted as raw
      }
      dateEl.textContent = String(formatted);
    } else {
      dateEl.textContent = '—';
    }
  }

  // Property details: support when booking JSON includes property fields or nested property object
  const prop = b.Property ?? b.property ?? b.property_details ?? {};
  const beds = b.PropertyBedrooms ?? b.Bedrooms ?? b.beds ?? prop.Bedrooms ?? prop.beds ?? '—';
  const baths = b.PropertyBathrooms ?? b.Bathrooms ?? b.baths ?? prop.Bathrooms ?? prop.baths ?? '—';
  const size = b.PropertySize ?? b.Size ?? b.size ?? prop.Size ?? prop.sqft ?? prop.sqft ?? '—';
  const parking = b.PropertyParking ?? b.Parking ?? b.parking_spaces ?? b.parking ?? prop.Parking_Spaces ?? prop.parking_spaces ?? '—';
  const corp = b.Corporation ?? b.corporation ?? prop.Corporation ?? prop.corporation ?? '—';
  const desc = b.PropertyDescription ?? b.Description ?? b.description ?? prop.Description ?? prop.description ?? '';

  setText('bookingModalBeds', beds !== null && beds !== undefined ? String(beds) : '—');
  setText('bookingModalBaths', baths !== null && baths !== undefined ? String(baths) : '—');
  setText('bookingModalSize', size !== null && size !== undefined ? String(size) : '—');
  setText('bookingModalParking', parking !== null && parking !== undefined ? String(parking) : '—');
  setText('bookingModalPropertyType', b.Property_Type !== null && b.Property_Type !== undefined ? String(b.Property_Type) : '—');
  setText('bookingModalCorporation', corp !== null && corp !== undefined ? String(corp) : '—');
  const descEl = document.getElementById('bookingModalDescription');
  if (descEl) descEl.textContent = desc || '—';

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
  if (cancelBtn) {
    const s = String(status).toLowerCase();
    // hide cancel button when status is 'scheduled'
    if (s === 'scheduled') {
      cancelBtn.style.display = 'none';
      cancelBtn.onclick = null;
    } else if (['pending','confirmed','viewing'].includes(s)) {
      cancelBtn.style.display = '';
      cancelBtn.dataset.id = b.bookingID;
      cancelBtn.onclick = onCancelBooking;
    } else {
      cancelBtn.style.display = 'none';
      cancelBtn.onclick = null;
    }
  }

  // Show Confirm Contract button when booking is confirmed
  const confirmBtn = document.getElementById('modalConfirmContractBtn');
  if (confirmBtn) {
    const s = String(status).toLowerCase();
    // Use robust detector to see if a contract file exists
    const hasContractFile = detectHasContract(b);
    if (hasContractFile) {
      confirmBtn.style.display = 'none';
      confirmBtn.onclick = null;
    } else if (['confirmed','scheduled'].includes(s)) {
      confirmBtn.style.display = '';
      // set dataset for use by handler
      confirmBtn.dataset.id = b.bookingID ?? '';
      // price fallback
      const price = b.PropertyPrice ?? b.Price ?? b.property_price ?? (b.Property && (b.Property.Price || b.Property.price)) ?? 0;
      confirmBtn.dataset.price = String(price || 0);
      // attach click handler that calls existing onConfirmContract with a small wrapper
      confirmBtn.onclick = (ev) => {
        try { onConfirmContract({ currentTarget: confirmBtn }); } catch (err) { console.error(err); }
      };
    } else {
      confirmBtn.style.display = 'none';
      confirmBtn.onclick = null;
    }
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

  // Confirm contract flow (client-side)
  async function onConfirmContract(e) {
    const bookingID = e.currentTarget?.dataset?.id;
    const price = Number(e.currentTarget?.dataset?.price || 0);
    if (!bookingID) return;

    // locate modal and scope DOM queries to it (fixes duplicate IDs across modals)
    const modalEl = document.getElementById('confirmContractModal');
    if (!modalEl) return;
    const modal = new bootstrap.Modal(modalEl);

    // scoped elements inside confirm modal
    const priceEl = modalEl.querySelector('#contractPropertyPrice');
    const monthlyEl = modalEl.querySelector('#contractMonthly');
    const errorsElScoped = modalEl.querySelector('#contractErrors');
    const ageEl = modalEl.querySelector('#contractClientAge');

    // populate modal
    if (priceEl) priceEl.textContent = price ? `₱${Number(price).toLocaleString()}` : '—';
    if (monthlyEl) monthlyEl.textContent = '—';
    if (errorsElScoped) errorsElScoped.style.display = 'none';
    if (ageEl) ageEl.textContent = '…';

    // helper: fetch user's birthdate and compute age (returns number or null)
    async function getClientAge(userId) {
      if (!userId) return null;
      try {
        // Prefer a dedicated getAge endpoint if the view exposes it.
        let ageUrl = null;
        if (window.getAgeUrlBase) {
          ageUrl = window.getAgeUrlBase;
        } else if (window.getUserUrlBase && String(window.getUserUrlBase).includes('getUser')) {
          try { ageUrl = String(window.getUserUrlBase).replace(/getUser\/?$/, 'getAge'); } catch(e) { ageUrl = null; }
        }
        if (!ageUrl) ageUrl = '/index.php/users/getAge';

        const res = await fetch(`${ageUrl}/${encodeURIComponent(userId)}`, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (!res.ok) return null;
        let json = null;
        try { json = await res.json(); } catch(e) { return null; }
        // server returns { age: number, birthdate: 'YYYY-MM-DD' }
        if (json && (typeof json.age === 'number' || !isNaN(Number(json.age)))) {
          return Number(json.age);
        }

        // fallback: attempt to compute from returned birthdate if provided
        const birth = json?.birthdate ?? json?.Birthdate ?? null;
        if (!birth) return null;
        const dob = new Date(birth);
        if (isNaN(dob.getTime())) return null;
        const now = new Date();
        let ageCalc = now.getFullYear() - dob.getFullYear();
        const m = now.getMonth() - dob.getMonth();
        if (m < 0 || (m === 0 && now.getDate() < dob.getDate())) ageCalc--;
        return ageCalc;
      } catch (err) {
        console.warn('getClientAge error', err);
        return null;
      }
    }

    // fetch age once and compute live when mode changes
    let age = null;
    const uid = window.currentUserId;
    age = await getClientAge(uid);
    if (ageEl) ageEl.textContent = age !== null ? String(age) : '—';

    // attach compute handler to radios inside the confirm modal only
    const radios = Array.from(modalEl.querySelectorAll('input[name="contractMode"]'));
    function compute() {
      const sel = radios.find(r => r.checked)?.value || null;
      if (errorsElScoped) errorsElScoped.style.display = 'none';
      if (!sel) {
        if (monthlyEl) monthlyEl.textContent = '—';
        return;
      }

      if (sel === 'full') {
        if (monthlyEl) monthlyEl.textContent = `₱${Number(price).toLocaleString()}`;
        return;
      }

      const maxYears = sel === 'pagibig' ? 60 : (sel === 'banko' ? 30 : 0);
      if (age === null) {
        if (errorsElScoped) { errorsElScoped.textContent = 'Unable to determine age. Please update your profile birthdate.'; errorsElScoped.style.display = ''; }
        if (monthlyEl) monthlyEl.textContent = '—';
        return;
      }

      const years = maxYears - Number(age);
      if (years <= 0) {
        if (errorsElScoped) { errorsElScoped.textContent = `Not eligible for ${sel} (age exceeds maximum).`; errorsElScoped.style.display = ''; }
        if (monthlyEl) monthlyEl.textContent = '—';
        return;
      }

      const months = years * 12;
      if (months <= 0) {
        if (monthlyEl) monthlyEl.textContent = '—';
        return;
      }

      const perMonth = Number(price) / months;
      // format number with grouping
      const perText = `₱${perMonth.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')}`;
      if (monthlyEl) monthlyEl.textContent = perText;
    }

    radios.forEach(r => r.addEventListener('change', compute));
    // run compute once to initialize displayed value if a radio is pre-selected
    compute();

    // Confirm button handler (client-side only for now) scoped to modal
    const confirmBtn = modalEl.querySelector('#confirmContractBtn');
    const onConfirm = async () => {
      const sel = radios.find(r => r.checked)?.value || null;
      if (!sel) {
        if (errorsElScoped) { errorsElScoped.textContent = 'Please choose a payment mode.'; errorsElScoped.style.display = ''; }
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
          if (errorsElScoped) { errorsElScoped.textContent = 'Not eligible for this loan mode.'; errorsElScoped.style.display = ''; }
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

        // detach handler to avoid duplicates
        if (confirmBtn) confirmBtn.removeEventListener('click', onConfirm);
        radios.forEach(r => r.removeEventListener('change', compute));

        // Show signature modal only after confirm modal is fully hidden to avoid backdrop overlap
        const signModalEl = document.getElementById('signContractModal');
        const openSignModal = async () => {
          try {
            if (!signModalEl) return;
            // set booking/reservation ids for sign handler
            const resInput = document.getElementById('signContractReservationId');
            const bookInput = document.getElementById('signContractBookingId');
            if (resInput) resInput.value = '';
            if (bookInput) bookInput.value = bookingID;

            // Defer SignaturePad initialization until modal is visible so sizing is correct
            let signaturePad = null;
            const signModal = new bootstrap.Modal(signModalEl);

            // One-time handler to init canvas after modal is shown
            const onShown = () => {
              try {
                const canvas = signModalEl.querySelector('#signaturePad') || document.getElementById('signaturePad');
                if (!canvas) return;

                // Ensure canvas is sized to its CSS layout size using devicePixelRatio
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const w = Math.max(300, canvas.clientWidth || 600);
                const h = Math.max(120, canvas.clientHeight || 200);
                canvas.width = Math.floor(w * ratio);
                canvas.height = Math.floor(h * ratio);
                const ctx = canvas.getContext('2d');
                ctx.setTransform(ratio, 0, 0, ratio, 0, 0);

                signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgba(255,255,255,0)' });

                const clearBtn = signModalEl.querySelector('#clearSignatureBtn') || document.getElementById('clearSignatureBtn');
                if (clearBtn) clearBtn.onclick = () => signaturePad.clear();

                // prepare sign button handler (replace to avoid double binds)
                const signBtn = signModalEl.querySelector('#signContractBtn') || document.getElementById('signContractBtn');
                if (signBtn) {
                  const newBtn = signBtn.cloneNode(true);
                  signBtn.parentNode.replaceChild(newBtn, signBtn);
                  newBtn.addEventListener('click', async () => {
                    const errorsEl = signModalEl.querySelector('#signContractErrors') || document.getElementById('signContractErrors');
                    if (errorsEl) { errorsEl.style.display = 'none'; errorsEl.textContent = ''; }
                    if (!signaturePad || signaturePad.isEmpty()) {
                      if (errorsEl) { errorsEl.textContent = 'Please provide your signature before continuing.'; errorsEl.style.display = ''; }
                      return;
                    }

                    const sigData = signaturePad.toDataURL();
                    try {
                      const params = new URLSearchParams();
                      params.append('booking_id', bookingID);
                      params.append('signature', sigData);
                      if (window.csrfName && window.csrfHash) params.append(window.csrfName, window.csrfHash);

                      const r = await fetch(window.signContractUrl || '/index.php/users/signContract', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: params.toString()
                      });

                      const resp = await r.json().catch(()=>null);
                      if (!r.ok || resp?.error) throw new Error(resp?.error || 'Failed to sign/ generate contract');

                      if (resp?.pdf_url) try { window.open(resp.pdf_url, '_blank'); } catch (e) {}

                      Swal.fire({ icon: 'success', title: 'Contract Submitted', text: 'Contract generated and submitted. Awaiting admin confirmation.' });
                      bootstrap.Modal.getInstance(signModalEl)?.hide();
                      loadMyBookings();
                    } catch (err) {
                      console.error('Sign contract failed', err);
                      if (errorsEl) { errorsEl.textContent = err.message || 'Unable to generate contract.'; errorsEl.style.display = ''; }
                    }
                  });
                }

                // remove this handler after initialization
                signModalEl.removeEventListener('shown.bs.modal', onShown);
              } catch (err) {
                console.warn('SignaturePad init failed', err);
              }
            };

            // cleanup on hide
            const onHiddenSign = () => {
              try {
                // reset canvas size placeholder so next show re-inits fresh
                const c = signModalEl.querySelector('#signaturePad') || document.getElementById('signaturePad');
                if (c) { c.width = 0; c.height = 0; const ctx = c.getContext && c.getContext('2d'); if (ctx) ctx.clearRect(0,0,c.width,c.height); }
                signModalEl.removeEventListener('hidden.bs.modal', onHiddenSign);
              } catch (e) { /* ignore */ }
            };

            signModalEl.addEventListener('shown.bs.modal', onShown);
            signModalEl.addEventListener('hidden.bs.modal', onHiddenSign);

            signModal.show();
          } catch (err) {
            console.warn('Opening sign modal failed', err);
            Swal.fire({ icon: 'success', title: 'Contract Proposal Sent', html: `<p>Mode: <strong>${sel}</strong></p><p>Monthly: <strong>₱${perMonth.toFixed(2)}</strong></p>` });
          }
        };

        // If confirm modal is visible, wait for it to hide to avoid backdrop overlap
        try {
          const onHiddenConfirm = () => {
            modalEl.removeEventListener('hidden.bs.modal', onHiddenConfirm);
            openSignModal();
          };
          modalEl.addEventListener('hidden.bs.modal', onHiddenConfirm);
          modal.hide();
        } catch (e) {
          // fallback: hide then open
          modal.hide();
          setTimeout(openSignModal, 200);
        }
      } catch (err) {
        console.error('Persist contract failed', err);
        Swal.fire({ icon: 'error', title: 'Failed', text: err.message || 'Unable to save contract proposal.' });
      }
    };

    if (confirmBtn) confirmBtn.addEventListener('click', onConfirm);

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