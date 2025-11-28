<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Clients</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= base_url('bootstrap5/css/bootstrap.min.css') ?>" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/styles/agenStyle.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <style>
    .client-list-scroll { max-height: 60vh; overflow: auto; }
    .client-item { display:flex; align-items:center; gap:12px; padding:10px; border-bottom:1px solid #f1f1f1; cursor:pointer; }
    .client-item:hover { background:#fbfbfb; }
    .client-photo { width:48px; height:48px; object-fit:cover; }
    .client-name { font-weight:600; color:#333; }
    .client-detail-photo { width:120px; height:120px; object-fit:cover; }
    .client-details .btn { min-width:120px; }
  </style>
</head>

<body>
  <!-- 🔹 Navbar -->
  <nav class="navbar navbar-light fixed-top shadow-sm bg-white border-bottom">
    <div class="container-fluid d-flex justify-content-between align-items-center px-4">
      <h3 class="mb-0 text-secondary fw-semibold">Manage Clients</h3>
      <ul class="nav nav-tabs border-0 flex-nowrap">
        <li class="nav-item"><a class="nav-link" href="/users/agentHomepage">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link active" href="/users/agentclients">Clients</a></li>
        <li class="nav-item"><a class="nav-link" href="/users/agentbookings">Bookings</a></li>
        <li class="nav-item"><a class="nav-link" href="/users/agentproperties">Properties</a></li>
        <li class="nav-item"><a class="nav-link" href="/users/agentchat">Chat</a></li>
      </ul>
      <a href="/users/agentprofile"> <button class="btn btn-outline-primary btn-sm"> Profile</button></a>
    </div>
  </nav>

  <!-- 🔹 Page Content -->
  <div class="container mt-5 pt-5">
    <div class="row g-4 animate__animated animate__fadeInUp">
      
      <!-- 🟩 Client List (Left Side) -->
      <div class="col-md-6">
        <div class="card p-0 shadow-sm client-list">
          <!-- Fixed Header -->
          <div class="card-header bg-white border-bottom p-4">
            <h4 class="fw-semibold mb-2">My Assigned Clients</h4>
            <p class="text-muted mb-0">Click a client to view complete details.</p>
          </div>
          <!-- Scrollable Body -->
          <div class="card-body p-4 client-list-scroll" id="clientsList"></div>
        </div>
      </div>

      <!-- 🟦 Client Details (Right Side) -->
      <div class="col-md-6">
        <div class="card p-4 shadow-sm client-details">
          <h4 class="fw-semibold mb-3">Client Details</h4>
          <div id="clientDetails" class="placeholder-text">
            Select a client from the list to view their details.
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- 🔹 ID Modal -->
  <div class="modal fade" id="idModal" tabindex="-1" aria-labelledby="idModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow-sm">
        <div class="modal-header bg-light">
          <h5 class="modal-title fw-semibold" id="idModalLabel">Valid ID</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center">
          <img id="idImage" src="" alt="Valid ID" class="img-fluid rounded shadow-sm">
        </div>
      </div>
    </div>
  </div>

  <!-- 🔹 Documents Modal -->
  <div class="modal fade" id="documentsModal" tabindex="-1" aria-labelledby="documentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow-sm">
        <div class="modal-header bg-light">
          <h5 class="modal-title fw-semibold" id="documentsModalLabel">Client Documents</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="documentsModalBody">
          <div class="text-muted small">No documents available for this client.</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- 🔹 Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
     const clients = <?= json_encode($clients) ?>;

    const listContainer = document.getElementById("clientsList");
      const detailsDiv = document.getElementById("clientDetails");

    // Small helper to safely escape HTML in injected strings
    function escapeHtml(s){
      if (s === null || s === undefined) return '';
      return String(s).replace(/[&<>"']/g, function(ch){
        return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch]);
      });
    }

    clients.forEach((c, i) => {
    const clientItem = document.createElement("div");
    clientItem.classList.add("client-item", "animate__animated", "animate__fadeInUp");

    // Combine first + middle + last names
    const fullName = [c.FirstName, c.MiddleName, c.LastName].filter(Boolean).join(' ');


    //ayosin toh pag may data na picture
    // Build photo URL from stored filename + employmentStatus when available
    let photoSrc = '<?= base_url('uploads/properties/no-image.jpg') ?>';
    if (c.Image && c.Image.trim() !== '') {
      const folder = (c.employmentStatus && c.employmentStatus.toLowerCase() === 'ofw') ? 'ofw' : 'locallyemployed';
      photoSrc = '<?= base_url('') ?>' + 'uploads/' + folder + '/' + c.Image;
    }
    clientItem.innerHTML = `
      <img src="${photoSrc}" class="client-photo rounded-circle">
    <span class="client-name">${fullName}</span>
    `;

    clientItem.onclick = () => showClient(i);
    listContainer.appendChild(clientItem);
  });

  function showClient(i) {
    const c = clients[i];
    const fullName = [c.FirstName, c.MiddleName, c.LastName].filter(Boolean).join(' ');

    // Build detail photo URL
    let detailPhoto = '<?= base_url('uploads/properties/no-image.jpg') ?>';
    if (c.Image && c.Image.trim() !== '') {
      const folder = (c.employmentStatus && c.employmentStatus.toLowerCase() === 'ofw') ? 'ofw' : 'locallyemployed';
      detailPhoto = '<?= base_url('') ?>' + 'uploads/' + folder + '/' + c.Image;
    }
    // Render details
    detailsDiv.innerHTML = `
      <div class="animate__animated animate__fadeIn">
        <div class="text-center mb-3">
         <img src="${detailPhoto}" 
          class="client-detail-photo rounded-circle shadow-sm mb-2" 
          alt="${c.FirstName}">

          <h5 class="fw-bold">${fullName}</h5>
        </div>
        <p><strong>Email:</strong> ${c.Email}</p>
        <p><strong>Phone:</strong> ${c.phoneNumber}</p>
        <p><strong>Birthday:</strong> ${c.Birthdate}</p>
        <div class="d-flex justify-content-center gap-2 my-3">
          <button class="btn btn-sm btn-outline-primary" id="viewDocsBtn">View Documents</button>
        </div>
        <hr />
        <div id="clientBookingsContainer">
          <div class="text-muted small">Loading booking history...</div>
        </div>
      </div>
    `;

    // attach view documents handler (fetch documents for the specific user)
    const viewDocsBtn = document.getElementById('viewDocsBtn');
    if (viewDocsBtn) viewDocsBtn.addEventListener('click', () => openDocumentsById(c.UserID || c.UserId || c.userID || c.UserId));

    // fetch booking history for this client (agent-only endpoint)
    (async () => {
      try {
        const res = await fetch('/users/clientBookings/' + encodeURIComponent(c.UserID || c.UserId || c.UserId), { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const container = document.getElementById('clientBookingsContainer');
        if (!res.ok) {
          container.innerHTML = '<div class="text-danger small">Could not load booking history.</div>';
          return;
        }
        const data = await res.json();
        if (!Array.isArray(data) || data.length === 0) {
          container.innerHTML = '<div class="text-muted small">No bookings found for this client.</div>';
          return;
        }

        const rows = data.map(b => {
          const when = b.bookingDate ? new Date(b.bookingDate).toLocaleString() : '—';
          const status = b.BookingStatus || b.status || b.statusName || 'Pending';
          const title = b.PropertyTitle || b.Title || 'Property';
          return `<div class="mb-2">
                    <div><strong>${escapeHtml(title)}</strong></div>
                    <div class="small text-muted">Date: ${escapeHtml(when)} • Status: <span class="badge bg-secondary">${escapeHtml(status)}</span></div>
                    <div class="mt-1 small">Notes: ${escapeHtml(b.Notes || b.booking_notes || '')}</div>
                  </div>`;
        }).join('');

        container.innerHTML = rows;
      } catch (err) {
        console.error(err);
        const container = document.getElementById('clientBookingsContainer');
        if (container) container.innerHTML = '<div class="text-danger small">Failed to load bookings.</div>';
      }
    })();
  }

    function viewID(src) {
      const defaultImage = "<?= base_url('uploads/properties/no-image.jpg') ?>"; // default fallback
      document.getElementById("idImage").src = src && src.trim() !== "" ? src : defaultImage;
      new bootstrap.Modal(document.getElementById("idModal")).show();
    }

    // Fetch and show documents for a specific user id
    async function openDocumentsById(userId) {
      const body = document.getElementById('documentsModalBody');
      if (!body) return;
      if (!userId) { body.innerHTML = '<div class="text-muted small">No user selected.</div>'; new bootstrap.Modal(document.getElementById('documentsModal')).show(); return; }

      try {
        body.innerHTML = '<div class="text-muted small">Loading documents…</div>';
        const res = await fetch('/users/clientDocuments/' + encodeURIComponent(userId), { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (!res.ok) {
          body.innerHTML = '<div class="text-danger small">Failed to load documents.</div>';
          new bootstrap.Modal(document.getElementById('documentsModal')).show();
          return;
        }
        const data = await res.json();
        const rows = [];

        const push = (title, url) => { if (!url) return; rows.push({ title, url, isImage: /\.(png|jpe?g|gif)$/i.test(url) }); };


        if (data.govIdImage) push('Government ID', data.govIdImage);
        if (Array.isArray(data.local)) data.local.forEach(d => push(d.key.replace(/_/g,' '), d.url));
        if (Array.isArray(data.ofw)) data.ofw.forEach(d => push(d.key.replace(/_/g,' '), d.url));

        if (rows.length === 0) {
          body.innerHTML = '<div class="text-muted small">No documents available for this client.</div>';
        } else {
          body.innerHTML = rows.map(r => {
            if (r.isImage) return `<div class="mb-3 text-center"><div class="fw-semibold mb-1">${escapeHtml(r.title)}</div><img src="${escapeHtml(r.url)}" class="img-fluid rounded shadow-sm" style="max-height:300px;" alt="${escapeHtml(r.title)}"></div>`;
            return `<div class="mb-2"><div class="fw-semibold">${escapeHtml(r.title)}</div><div><a href="${escapeHtml(r.url)}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary mt-1">Open / Download</a></div></div>`;
          }).join('');
        }

        new bootstrap.Modal(document.getElementById('documentsModal')).show();
      } catch (err) {
        console.error('openDocumentsById error', err);
        body.innerHTML = '<div class="text-danger small">Error loading documents.</div>';
        new bootstrap.Modal(document.getElementById('documentsModal')).show();
      }
    }

  </script>
</body>
</html>
