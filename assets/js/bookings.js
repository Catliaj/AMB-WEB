(() => {
  // Ensure globals from the view are available: myBookingsUrl, bookingCancelUrl, getUserUrlBase, csrfName, csrfHash
  if (!window.myBookingsUrl) return console.warn('myBookingsUrl not defined');

  const listEl = document.getElementById('bookingsList');
  const modalEl = document.getElementById('bookingDetailModal');
  const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
  let bookingsCache = [];

  async function loadBookings(){
    if (!listEl) return;
    listEl.innerHTML = '<div class="text-center py-4">Loading bookings...</div>';
    try{
      const res = await fetch(window.myBookingsUrl, { credentials: 'same-origin', headers: { 'X-Requested-With':'XMLHttpRequest' } });
      if (!res.ok) throw new Error('Failed to load bookings: ' + res.status);
      const data = await res.json();
      bookingsCache = Array.isArray(data) ? data : [];
      renderList(bookingsCache);
    }catch(err){
      console.error(err);
      listEl.innerHTML = '<div class="text-danger text-center py-4">Failed to load bookings.</div>';
    }
  }

  function renderList(bookings){
    if (!listEl) return;
    if (!bookings || !bookings.length){
      listEl.innerHTML = '<div class="card"><div class="card-body text-center text-muted">No bookings found.</div></div>';
      return;
    }

    const rows = bookings.map(b => {
      const date = b.bookingDate ? moment(b.bookingDate).format('LLL') : '—';
      const status = b.BookingStatus || b.status || 'Pending';
      const title = b.PropertyTitle || b.Title || 'Property';
      const price = b.PropertyPrice || b.Price || 0;
      const img = (b.Images && b.Images[0]) ? ((typeof b.Images[0] === 'object') ? b.Images[0].url : b.Images[0]) : '/uploads/properties/no-image.jpg';
      return `
        <div class="card mb-3">
          <div class="row g-0">
            <div class="col-md-3 d-flex align-items-center">
              <img src="${escapeHtml(img)}" class="img-fluid rounded-start" style="height:120px;object-fit:cover;width:100%">
            </div>
            <div class="col-md-7">
              <div class="card-body">
                <h5 class="card-title mb-1">${escapeHtml(title)}</h5>
                <p class="card-text small text-muted mb-1">${escapeHtml(b.PropertyLocation || '')}</p>
                <p class="card-text small mb-1">Date: ${escapeHtml(date)}</p>
                <p class="card-text small">Status: <span class="badge bg-secondary">${escapeHtml(status)}</span></p>
              </div>
            </div>
            <div class="col-md-2 d-flex align-items-center justify-content-center">
              <div class="d-grid gap-2">
                <button class="btn btn-sm btn-outline-primary" data-action="view" data-id="${b.bookingID}">Details</button>
                <button class="btn btn-sm btn-outline-danger" data-action="cancel" data-id="${b.bookingID}">Cancel</button>
              </div>
            </div>
          </div>
        </div>
      `;
    }).join('');

    listEl.innerHTML = rows;
    listEl.querySelectorAll('button[data-action="view"]').forEach(b => b.addEventListener('click', ev => openDetails(ev.target.dataset.id)));
    listEl.querySelectorAll('button[data-action="cancel"]').forEach(b => b.addEventListener('click', ev => confirmCancel(ev.target.dataset.id)));
  }

  function escapeHtml(s){ if (s===null||s===undefined) return ''; return String(s).replace(/[&<>"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch])); }

  function findBooking(id){ return bookingsCache.find(x => String(x.bookingID) === String(id) || String(x.bookingId) === String(id)); }

  function openDetails(id){
    const b = findBooking(id);
    if (!b) return Swal.fire({ icon:'error', title:'Not found', text:'Booking not available' });

    document.getElementById('bookingModalTitle').innerText = b.PropertyTitle || b.Title || 'Booking Details';
    const imgEl = document.getElementById('bookingModalImage');
    const imgSrc = (b.Images && b.Images[0]) ? ((typeof b.Images[0] === 'object') ? b.Images[0].url : b.Images[0]) : '/uploads/properties/no-image.jpg';
    if (imgEl) imgEl.src = imgSrc;

    document.getElementById('bookingModalPropertyTitle').innerText = b.PropertyTitle || b.Title || '';
    document.getElementById('bookingModalLocation').innerText = b.PropertyLocation || b.Location || '';
    document.getElementById('bookingModalStatus').innerText = b.BookingStatus || b.status || 'Pending';
    document.getElementById('bookingModalDate').innerText = b.bookingDate ? moment(b.bookingDate).format('LLL') : '';
    document.getElementById('bookingModalAgent').innerText = b.AgentName || b.agent_name || '—';
    document.getElementById('bookingModalAgentPhone').innerText = b.ClientPhone || b.AgentPhone || b.agent_phone || '';
    document.getElementById('bookingModalAgentEmail').innerText = b.ClientEmail || b.AgentEmail || b.agent_email || '';
    document.getElementById('bookingModalPrice').innerText = (b.PropertyPrice || b.Price) ? '₱ ' + Number(b.PropertyPrice || b.Price).toLocaleString() : '—';
    document.getElementById('bookingModalNotes').innerText = b.Notes || b.booking_notes || b.Notes || 'No notes provided.';
    document.getElementById('bookingModalAgentId').value = b.AgentID || b.agent_id || '';

    // History: try to show rating if present + reason
    const histEl = document.getElementById('bookingModalHistory');
    let histHtml = '';
    if (b.Rating) histHtml += `<div><strong>Rating:</strong> ${escapeHtml(String(b.Rating))}</div>`;
    if (b.Reason) histHtml += `<div><strong>Purpose:</strong> ${escapeHtml(b.Reason)}</div>`;
    if (b.updated_at) histHtml += `<div><strong>Last Updated:</strong> ${escapeHtml(moment(b.updated_at).format('LLL'))}</div>`;
    if (!histHtml) histHtml = '<div class="text-muted">No additional history available.</div>';
    if (histEl) histEl.innerHTML = histHtml;

    // Contact / Open property buttons
    const contactBtn = document.getElementById('modalContactAgentBtn');
    const openPropBtn = document.getElementById('modalViewPropertyBtn');
    if (contactBtn) contactBtn.onclick = () => {
      const email = b.AgentEmail || b.agent_email || b.ClientEmail || '';
      if (email) window.location.href = 'mailto:' + email;
      else Swal.fire({ icon:'info', title:'No contact', text:'Agent contact not available.' });
    };
    if (openPropBtn) openPropBtn.onclick = () => {
      const pid = b.PropertyID || b.propertyID || b.PropertyId;
      if (pid) window.location.href = '/property/view/' + pid; else Swal.fire({ icon:'info', title:'No property', text:'Property link not available.' });
    };

    // Cancel button in modal
    const modalCancel = document.getElementById('modalCancelBookingBtn');
    if (modalCancel) modalCancel.onclick = () => confirmCancel(b.bookingID || b.bookingId || b.bookingID);

    modal?.show();
  }

  function confirmCancel(id){
    const b = findBooking(id);
    if (!b) return Swal.fire({ icon:'error', title:'Not found' });
    Swal.fire({ title: 'Cancel booking?', text: 'This will cancel your booking.', icon: 'warning', showCancelButton: true }).then(res => {
      if (res.isConfirmed) doCancel(id);
    });
  }

  async function doCancel(id){
    try{
      Swal.fire({ title: 'Cancelling...', allowOutsideClick:false, didOpen: () => Swal.showLoading() });
      const fd = new FormData();
      fd.append('booking_id', id);
      fd.append('status', 'Cancelled');
      if (window.csrfName && window.csrfHash) fd.append(window.csrfName, window.csrfHash);

      const res = await fetch(window.bookingCancelUrl, { method: 'POST', credentials: 'same-origin', body: fd });
      const j = await res.json().catch(()=>null);
      if (!res.ok || !j || j.error) throw new Error(j?.error || 'Failed');
      Swal.close();
      Swal.fire({ icon:'success', title:'Cancelled' });
      await loadBookings();
      modal?.hide();
    }catch(err){
      console.error(err);
      Swal.close();
      Swal.fire({ icon:'error', title:'Failed', text: err.message || 'Cancellation failed' });
    }
  }

  // init
  document.addEventListener('DOMContentLoaded', loadBookings);

})();