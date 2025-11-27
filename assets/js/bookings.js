(() => {
  // Ensure globals from the view are available: myBookingsUrl, bookingCancelUrl, getUserUrlBase, csrfName, csrfHash
  if (!window.myBookingsUrl) return console.warn('myBookingsUrl not defined');

  const listEl = document.getElementById('bookingsList');
  const modalEl = document.getElementById('bookingDetailModal');
  const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
  let bookingsCache = [];
  const mode = window.bookingsMode || 'bookings'; // 'bookings' or 'reservations'

  async function loadBookings(){
    if (!listEl) return;
    listEl.innerHTML = '<div class="text-center py-4">Loading bookings...</div>';
    try{
      let url = window.myBookingsUrl;
      if (mode === 'reservations') {
        url = window.reservationsUrl || window.myBookingsUrl; // Use reservations endpoint if available
      } else {
        // For bookings mode, filter to show only non-reserved bookings
        url = window.myBookingsUrl;
      }
      const res = await fetch(url, { credentials: 'same-origin', headers: { 'X-Requested-With':'XMLHttpRequest' } });
      if (!res.ok) throw new Error('Failed to load bookings: ' + res.status);
      const data = await res.json();
      bookingsCache = Array.isArray(data) ? data : [];
      
      // Debug: log the data to see what we're getting
      console.log('Loaded bookings/reservations:', bookingsCache.length, 'items');
      if (bookingsCache.length > 0) {
        console.log('First item:', bookingsCache[0]);
      }
      
      // Filter based on mode
      if (mode === 'reservations') {
        // For reservations, use the reservations endpoint which returns reservations with booking data
        // No additional filtering needed as the endpoint already returns only reservations
      } else {
        // Show bookings that are not in reservations (Pending, Scheduled, Rejected, Cancelled)
        // Filter out any that have reservation IDs, but include all statuses
        bookingsCache = bookingsCache.filter(b => {
          // Don't show if it has a reservation ID (it's in reservations)
          if (b.reservationID || b.ReservationID) return false;
          // Show all bookings: Pending, Scheduled, Rejected, Cancelled
          return true;
        });
      }
      
      renderList(bookingsCache);
    }catch(err){
      console.error(err);
      listEl.innerHTML = '<div class="text-danger text-center py-4">Failed to load bookings.</div>';
    }
  }

  function getStatusBadgeClass(status) {
    const st = String(status || '').toLowerCase();
    if (st === 'pending') return 'bg-warning text-dark';
    if (st === 'scheduled') return 'bg-success text-white';
    if (st === 'rejected') return 'bg-danger text-white';
    if (st === 'cancelled') return 'bg-secondary text-white';
    return 'bg-secondary text-white';
  }

  function renderList(bookings){
    if (!listEl) return;
    if (!bookings || !bookings.length){
      listEl.innerHTML = '<div class="card"><div class="card-body text-center text-muted">No ' + (mode === 'reservations' ? 'reservations' : 'bookings') + ' found.</div></div>';
      return;
    }

    const rows = bookings.map(b => {
      const date = b.bookingDate ? moment(b.bookingDate).format('LLL') : '—';
      // Try multiple possible field names for status
      const status = b.BookingStatus || b.status || b.Status || 'Pending';
      const title = b.PropertyTitle || b.Title || 'Property';
      const price = b.PropertyPrice || b.Price || 0;
      const img = (b.Images && b.Images[0]) ? ((typeof b.Images[0] === 'object') ? b.Images[0].url : b.Images[0]) : '/uploads/properties/no-image.jpg';
      const statusLower = String(status).toLowerCase();
      const badgeClass = getStatusBadgeClass(status);
      
      // Debug log for scheduled bookings
      if (statusLower === 'scheduled') {
        console.log('Found scheduled booking:', b.bookingID, status);
      }
      
      // Determine which buttons to show based on status and mode
      let actionButtons = '';
      if (mode === 'bookings') {
        // Bookings page: show buttons based on status
        if (statusLower === 'pending') {
          actionButtons = `
            <button class="btn btn-sm btn-outline-primary" data-action="view" data-id="${b.bookingID}">Details</button>
          `;
        } else if (statusLower === 'scheduled') {
          actionButtons = `
            <button class="btn btn-sm btn-outline-primary" data-action="view" data-id="${b.bookingID}">Details</button>
            <button class="btn btn-sm btn-outline-danger" data-action="cancel" data-id="${b.bookingID}">Cancel</button>
            <button class="btn btn-sm btn-success" data-action="reserve" data-id="${b.bookingID}">Reserve</button>
          `;
        } else if (statusLower === 'rejected' || statusLower === 'cancelled') {
          actionButtons = `
            <button class="btn btn-sm btn-outline-primary" data-action="view" data-id="${b.bookingID}">Details</button>
          `;
        } else {
          actionButtons = `
            <button class="btn btn-sm btn-outline-primary" data-action="view" data-id="${b.bookingID}">Details</button>
            <button class="btn btn-sm btn-outline-danger" data-action="cancel" data-id="${b.bookingID}">Cancel</button>
          `;
        }
      } else {
        // Reservations page
        const reservationId = b.reservationID || b.ReservationID;
        if (reservationId) {
          actionButtons = `
            <button class="btn btn-sm btn-outline-primary" data-action="view" data-id="${b.bookingID}" data-reservation-id="${reservationId}">Details</button>
            <button class="btn btn-sm btn-outline-danger" data-action="cancel-reservation" data-id="${b.bookingID}" data-reservation-id="${reservationId}">Cancel</button>
            <button class="btn btn-sm btn-info" data-action="select-payment" data-id="${b.bookingID}" data-reservation-id="${reservationId}" data-price="${price}">Select Payment</button>
          `;
        } else if (statusLower === 'scheduled') {
          // Scheduled booking that can be reserved
          actionButtons = `
            <button class="btn btn-sm btn-outline-primary" data-action="view" data-id="${b.bookingID}">Details</button>
            <button class="btn btn-sm btn-success" data-action="reserve" data-id="${b.bookingID}">Reserve</button>
          `;
        } else {
          actionButtons = `
            <button class="btn btn-sm btn-outline-primary" data-action="view" data-id="${b.bookingID}">Details</button>
          `;
        }
      }

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
                <p class="card-text small">Status: <span class="badge ${badgeClass}">${escapeHtml(status)}</span></p>
              </div>
            </div>
            <div class="col-md-2 d-flex align-items-center justify-content-center">
              <div class="d-grid gap-2">
                ${actionButtons}
              </div>
            </div>
          </div>
        </div>
      `;
    }).join('');

    listEl.innerHTML = rows;
    
    // Attach event listeners
    listEl.querySelectorAll('button[data-action="view"]').forEach(b => b.addEventListener('click', ev => openDetails(ev.target.dataset.id)));
    listEl.querySelectorAll('button[data-action="cancel"]').forEach(b => b.addEventListener('click', ev => confirmCancel(ev.target.dataset.id)));
    listEl.querySelectorAll('button[data-action="reserve"]').forEach(b => b.addEventListener('click', ev => confirmReserve(ev.target.dataset.id)));
    listEl.querySelectorAll('button[data-action="cancel-reservation"]').forEach(b => b.addEventListener('click', ev => confirmCancelReservation(ev.target.dataset.id, ev.target.dataset.reservationId)));
    listEl.querySelectorAll('button[data-action="select-payment"]').forEach(b => b.addEventListener('click', ev => openPaymentModal(ev.target.dataset.id, ev.target.dataset.reservationId, ev.target.dataset.price)));
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
    // Try multiple possible field names for status
    const status = b.BookingStatus || b.status || b.Status || 'Pending';
    document.getElementById('bookingModalStatus').innerText = status;
    document.getElementById('bookingModalStatus').className = 'badge ' + getStatusBadgeClass(status);
    document.getElementById('bookingModalDate').innerText = b.bookingDate ? moment(b.bookingDate).format('LLL') : '';
    document.getElementById('bookingModalAgent').innerText = b.AgentName || b.agent_name || '—';
    document.getElementById('bookingModalAgentPhone').innerText = b.ClientPhone || b.AgentPhone || b.agent_phone || '';
    document.getElementById('bookingModalAgentEmail').innerText = b.ClientEmail || b.AgentEmail || b.agent_email || '';
    document.getElementById('bookingModalPrice').innerText = (b.PropertyPrice || b.Price) ? '₱ ' + Number(b.PropertyPrice || b.Price).toLocaleString() : '—';
    if (document.getElementById('bookingModalNotes')) {
      document.getElementById('bookingModalNotes').innerText = b.Notes || b.booking_notes || b.Notes || 'No notes provided.';
    }
    document.getElementById('bookingModalAgentId').value = b.AgentID || b.agent_id || '';

    // Update property details
    if (document.getElementById('bookingModalBeds')) document.getElementById('bookingModalBeds').innerText = b.PropertyBedrooms || b.Bedrooms || '—';
    if (document.getElementById('bookingModalBaths')) document.getElementById('bookingModalBaths').innerText = b.PropertyBathrooms || b.Bathrooms || '—';
    if (document.getElementById('bookingModalSize')) document.getElementById('bookingModalSize').innerText = b.PropertySize || b.Size || '—';
    if (document.getElementById('bookingModalParking')) document.getElementById('bookingModalParking').innerText = b.PropertyParking || b.Parking_Spaces || '—';
    if (document.getElementById('bookingModalPropertyType')) document.getElementById('bookingModalPropertyType').innerText = b.Property_Type || '—';
    if (document.getElementById('bookingModalCorporation')) document.getElementById('bookingModalCorporation').innerText = b.Corporation || '—';
    if (document.getElementById('bookingModalDescription')) document.getElementById('bookingModalDescription').innerText = b.PropertyDescription || b.Description || '—';

    // History: try to show rating if present + reason
    const histEl = document.getElementById('bookingModalHistory');
    if (histEl) {
      let histHtml = '';
      if (b.Rating) histHtml += `<div><strong>Rating:</strong> ${escapeHtml(String(b.Rating))}</div>`;
      if (b.Reason) histHtml += `<div><strong>Purpose:</strong> ${escapeHtml(b.Reason)}</div>`;
      if (b.updated_at) histHtml += `<div><strong>Last Updated:</strong> ${escapeHtml(moment(b.updated_at).format('LLL'))}</div>`;
      if (!histHtml) histHtml = '<div class="text-muted">No additional history available.</div>';
      histEl.innerHTML = histHtml;
    }

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

    // Update modal buttons based on status
    const statusLower = String(status).toLowerCase();
    const modalCancel = document.getElementById('modalCancelBookingBtn');
    const modalReserve = document.getElementById('modalReserveBtn');
    const modalSelectPayment = document.getElementById('modalSelectPaymentBtn');
    
    if (modalCancel) {
      if (statusLower === 'scheduled' && mode === 'bookings') {
        modalCancel.style.display = 'inline-block';
        modalCancel.onclick = () => confirmCancel(b.bookingID || b.bookingId || b.bookingID);
      } else if (statusLower === 'rejected' || statusLower === 'cancelled') {
        modalCancel.style.display = 'none';
      } else {
        modalCancel.style.display = 'inline-block';
        modalCancel.onclick = () => confirmCancel(b.bookingID || b.bookingId || b.bookingID);
      }
    }
    
    if (modalReserve) {
      if (statusLower === 'scheduled' && mode === 'bookings') {
        modalReserve.style.display = 'inline-block';
        modalReserve.onclick = () => confirmReserve(b.bookingID || b.bookingId || b.bookingID);
      } else {
        modalReserve.style.display = 'none';
      }
    }
    
    if (modalSelectPayment && (b.reservationID || b.ReservationID)) {
      modalSelectPayment.style.display = 'inline-block';
      modalSelectPayment.onclick = () => openPaymentModal(b.bookingID || b.bookingId || b.bookingID, b.reservationID || b.ReservationID, b.PropertyPrice || b.Price);
    } else if (modalSelectPayment) {
      modalSelectPayment.style.display = 'none';
    }

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

  async function confirmReserve(id) {
    const b = findBooking(id);
    if (!b) return Swal.fire({ icon:'error', title:'Not found' });
    
    const result = await Swal.fire({
      title: 'Reserve this property?',
      text: 'This will move your booking to reservations.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Yes, reserve',
      cancelButtonText: 'Cancel'
    });
    
    if (!result.isConfirmed) return;
    
    try {
      Swal.fire({ title: 'Reserving...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
      const fd = new FormData();
      fd.append('booking_id', id);
      if (window.csrfName && window.csrfHash) fd.append(window.csrfName, window.csrfHash);
      
      const res = await fetch(window.reserveUrl || '/users/reserve', { method: 'POST', credentials: 'same-origin', body: fd });
      const j = await res.json().catch(() => null);
      if (!res.ok || !j || j.error) throw new Error(j?.error || 'Failed to reserve');
      
      Swal.close();
      Swal.fire({ icon: 'success', title: 'Reserved', text: 'Your booking has been moved to reservations.' });
      await loadBookings();
      modal?.hide();
    } catch (err) {
      console.error(err);
      Swal.close();
      Swal.fire({ icon: 'error', title: 'Failed', text: err.message || 'Reservation failed' });
    }
  }

  async function confirmCancelReservation(bookingId, reservationId) {
    const result = await Swal.fire({
      title: 'Cancel reservation?',
      text: 'This will cancel your reservation and move it back to bookings.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, cancel',
      cancelButtonText: 'No'
    });
    
    if (!result.isConfirmed) return;
    
    try {
      Swal.fire({ title: 'Cancelling...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
      const fd = new FormData();
      fd.append('booking_id', bookingId);
      fd.append('reservation_id', reservationId);
      fd.append('status', 'Cancelled');
      if (window.csrfName && window.csrfHash) fd.append(window.csrfName, window.csrfHash);
      
      const res = await fetch(window.cancelReservationUrl || window.bookingCancelUrl, { method: 'POST', credentials: 'same-origin', body: fd });
      const j = await res.json().catch(() => null);
      if (!res.ok || !j || j.error) throw new Error(j?.error || 'Failed to cancel reservation');
      
      Swal.close();
      Swal.fire({ icon: 'success', title: 'Cancelled', text: 'Reservation cancelled and moved back to bookings.' });
      await loadBookings();
      modal?.hide();
    } catch (err) {
      console.error(err);
      Swal.close();
      Swal.fire({ icon: 'error', title: 'Failed', text: err.message || 'Cancellation failed' });
    }
  }

  async function openPaymentModal(bookingId, reservationId, propertyPrice) {
    // Fetch user's birthdate to calculate age
    try {
      const userRes = await fetch(`${window.getUserUrlBase}/${window.currentUserId || ''}`, { credentials: 'same-origin' });
      const userData = await userRes.json().catch(() => null);
      
      if (!userData || !userData.Birthdate) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Could not fetch user birthdate. Please update your profile.' });
        return;
      }
      
      // Calculate age
      const birthdate = new Date(userData.Birthdate);
      const today = new Date();
      let age = today.getFullYear() - birthdate.getFullYear();
      const monthDiff = today.getMonth() - birthdate.getMonth();
      if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthdate.getDate())) {
        age--;
      }
      
      // Open payment modal
      document.getElementById('contractPropertyPrice').innerText = '₱ ' + Number(propertyPrice).toLocaleString();
      document.getElementById('contractClientAge').innerText = age + ' years';
      document.getElementById('contractMonthly').innerText = '—';
      
      // Reset radio buttons
      document.querySelectorAll('input[name="contractMode"]').forEach(rb => rb.checked = false);
      
      // Store booking and reservation IDs
      document.getElementById('paymentModalBookingId').value = bookingId;
      document.getElementById('paymentModalReservationId').value = reservationId;
      document.getElementById('paymentModalPropertyPrice').value = propertyPrice;
      document.getElementById('paymentModalClientAge').value = age;
      
      // Add event listeners to radio buttons for calculation
      document.querySelectorAll('input[name="contractMode"]').forEach(rb => {
        rb.onchange = () => calculatePayment(age, propertyPrice, rb.value);
      });
      
      const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
      paymentModal.show();
    } catch (err) {
      console.error(err);
      Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load payment information.' });
    }
  }

  function calculatePayment(age, propertyPrice, mode) {
    let monthlyPayment = 0;
    let calculationText = '';
    
    if (mode === 'full') {
      // Full payment: property price (no monthly calculation)
      monthlyPayment = propertyPrice;
      calculationText = `Full Payment: ₱ ${Number(propertyPrice).toLocaleString()}`;
    } else {
      // Loan calculation: use full maximum term
      // Formula: years = maxYears, months = years * 12, monthly = propertyPrice / months
      const maxYears = mode === 'pagibig' ? 60 : 30;
      const years = maxYears; // Use full loan term
      const months = years * 12;
      monthlyPayment = propertyPrice / months;
      calculationText = `Age: ${age} years | Loan Term: ${years} years (${months} months) | Monthly Payment: ₱ ${Number(monthlyPayment).toFixed(2).toLocaleString()}`;
    }
    
    document.getElementById('contractMonthly').innerText = calculationText;
    document.getElementById('paymentModalMonthlyPayment').value = monthlyPayment;
    document.getElementById('paymentModalMode').value = mode;
  }

  // Payment confirmation handler
  document.addEventListener('DOMContentLoaded', () => {
    loadBookings();
    
    // Payment modal confirm button
    const confirmPaymentBtn = document.getElementById('confirmPaymentBtn');
    if (confirmPaymentBtn) {
      confirmPaymentBtn.onclick = async () => {
        const bookingId = document.getElementById('paymentModalBookingId').value;
        const reservationId = document.getElementById('paymentModalReservationId').value;
        const propertyPrice = document.getElementById('paymentModalPropertyPrice').value;
        const mode = document.getElementById('paymentModalMode').value;
        
        if (!mode) {
          Swal.fire({ icon: 'warning', title: 'Please select a payment mode' });
          return;
        }
        
        try {
          Swal.fire({ title: 'Saving payment...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
          const fd = new FormData();
          fd.append('reservation_id', reservationId);
          fd.append('mode', mode);
          fd.append('property_price', propertyPrice);
          if (window.csrfName && window.csrfHash) fd.append(window.csrfName, window.csrfHash);
          
          const res = await fetch(window.selectPaymentUrl || '/users/selectPayment', { method: 'POST', credentials: 'same-origin', body: fd });
          const j = await res.json().catch(() => null);
          if (!res.ok || !j || j.error) throw new Error(j?.error || 'Failed to save payment');
          
          Swal.close();
          bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
          
          // Open signature modal
          document.getElementById('signContractReservationId').value = reservationId;
          document.getElementById('signContractBookingId').value = bookingId;
          initSignaturePad();
          const signModal = new bootstrap.Modal(document.getElementById('signContractModal'));
          signModal.show();
        } catch (err) {
          console.error(err);
          Swal.close();
          Swal.fire({ icon: 'error', title: 'Failed', text: err.message || 'Failed to save payment' });
        }
      };
    }
    
    // Signature pad initialization
    let signaturePad = null;
    function initSignaturePad() {
      const canvas = document.getElementById('signaturePad');
      if (!canvas) return;
      
      if (signaturePad) {
        signaturePad.clear();
        return;
      }
      
      signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(255, 255, 255)',
        penColor: 'rgb(0, 0, 0)'
      });
      
      // Clear button
      const clearBtn = document.getElementById('clearSignatureBtn');
      if (clearBtn) {
        clearBtn.onclick = () => signaturePad.clear();
      }
    }
    
    // Sign contract button
    const signContractBtn = document.getElementById('signContractBtn');
    if (signContractBtn) {
      signContractBtn.onclick = async () => {
        if (!signaturePad || signaturePad.isEmpty()) {
          Swal.fire({ icon: 'warning', title: 'Please provide your signature' });
          return;
        }
        
        const reservationId = document.getElementById('signContractReservationId').value;
        const signatureData = signaturePad.toDataURL('image/png');
        
        try {
          Swal.fire({ title: 'Signing contract...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
          const fd = new FormData();
          fd.append('reservation_id', reservationId);
          fd.append('signature', signatureData);
          if (window.csrfName && window.csrfHash) fd.append(window.csrfName, window.csrfHash);
          
          const res = await fetch(window.signContractUrl || '/users/signContract', { method: 'POST', credentials: 'same-origin', body: fd });
          const j = await res.json().catch(() => null);
          if (!res.ok || !j || j.error) throw new Error(j?.error || 'Failed to sign contract');
          
          Swal.close();
          bootstrap.Modal.getInstance(document.getElementById('signContractModal')).hide();
          Swal.fire({ icon: 'success', title: 'Contract Signed', text: 'Your contract has been signed and generated successfully!' });
          await loadBookings();
        } catch (err) {
          console.error(err);
          Swal.close();
          Swal.fire({ icon: 'error', title: 'Failed', text: err.message || 'Failed to sign contract' });
        }
      };
    }
  });

})();
