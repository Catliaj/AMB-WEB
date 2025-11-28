<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Clients - ABM Property</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= base_url('bootstrap5/css/bootstrap.min.css') ?>" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  
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
    }

    html[data-theme="dark"] {
      --primary: #5ab34f;
      --primary-hover: #469541;
      --secondary: #c4aee3;
      --accent: #3eb3bd;
      --light-bg: #252e42;
      --light-accent: #2d4038;
      --bg-color: #1a1f2e;
      --text-color: #f0f0f0;
      --text-muted: #a0a0a0;
      --card-bg: #252a3a;
      --border-color: rgba(255, 255, 255, 0.1);
      --shadow: rgba(0, 0, 0, 0.3);
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

    .card-header {
      background-color: var(--light-bg);
      border-bottom: 1px solid var(--border-color);
      border-radius: 16px 16px 0 0 !important;
    }

    html[data-theme="dark"] .card-header {
      background-color: var(--light-bg);
    }

    /* CLIENT LIST */
    .client-list-scroll { 
      max-height: 60vh; 
      overflow-y: auto;
      overflow-x: hidden;
    }

    .client-list-scroll::-webkit-scrollbar {
      width: 6px;
    }

    .client-list-scroll::-webkit-scrollbar-track {
      background: transparent;
    }

    .client-list-scroll::-webkit-scrollbar-thumb {
      background: var(--text-muted);
      border-radius: 10px;
    }

    .client-item { 
      display: flex; 
      align-items: center; 
      gap: 12px; 
      padding: 12px; 
      border-bottom: 1px solid var(--border-color);
      cursor: pointer;
      transition: background-color 0.2s;
      border-radius: 8px;
      margin-bottom: 0.5rem;
    }

    .client-item:hover { 
      background-color: var(--light-bg);
    }

    .client-photo { 
      width: 48px; 
      height: 48px; 
      object-fit: cover;
      border: 2px solid var(--border-color);
    }

    .client-name { 
      font-weight: 600; 
      color: var(--text-color);
    }

    .client-detail-photo { 
      width: 120px; 
      height: 120px; 
      object-fit: cover;
      border: 3px solid var(--border-color);
    }

    /* BUTTONS */
    .btn-primary {
      background-color: var(--primary) !important;
      border-color: var(--primary) !important;
    }

    .btn-primary:hover {
      background-color: var(--primary-hover) !important;
    }

    .btn-outline-primary {
      color: var(--primary) !important;
      border-color: var(--primary) !important;
    }

    .btn-outline-primary:hover {
      background-color: var(--primary) !important;
      color: white !important;
    }

    .btn-outline-secondary {
      color: var(--text-color) !important;
      border-color: var(--border-color) !important;
    }

    .btn-outline-secondary:hover {
      background-color: var(--text-muted) !important;
      color: white !important;
    }

    /* TEXT */
    h1, h2, h3, h4, h5, h6, p, strong {
      color: var(--text-color);
    }

    .text-muted {
      color: var(--text-muted) !important;
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

    html[data-theme="dark"] .modal-header {
      background-color: var(--light-bg);
    }

    .modal-body {
      background-color: var(--card-bg);
    }

    .modal-footer {
      background-color: var(--card-bg);
      border-top: 1px solid var(--border-color);
    }

    /* BADGES */
    .badge {
      background-color: var(--text-muted) !important;
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
          <li class="nav-item"><a class="nav-link nav-link-custom active" href="/users/agentclients">Clients</a></li>
          <li class="nav-item"><a class="nav-link nav-link-custom" href="/users/agentbookings">Bookings</a></li>
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
    <div class="container">
      <h3 class="fw-bold mb-4">Manage Clients</h3>
      
      <div class="row g-4 animate__animated animate__fadeInUp">
        
        <!-- Client List (Left Side) -->
        <div class="col-md-6">
          <div class="card client-list">
            <div class="card-header p-4">
              <h4 class="fw-semibold mb-2">My Assigned Clients</h4>
              <p class="text-muted mb-0">Click a client to view complete details.</p>
            </div>
            <div class="card-body p-4 client-list-scroll" id="clientsList"></div>
          </div>
        </div>

        <!-- Client Details (Right Side) -->
        <div class="col-md-6">
          <div class="card p-4 client-details">
            <h4 class="fw-semibold mb-3">Client Details</h4>
            <div id="clientDetails" class="placeholder-text">
              <p class="text-muted text-center py-5">Select a client from the list to view their details.</p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- ID Modal -->
  <div class="modal fade" id="idModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-semibold">Valid ID</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center">
          <img id="idImage" src="" alt="Valid ID" class="img-fluid rounded shadow-sm">
        </div>
      </div>
    </div>
  </div>

  <!-- Documents Modal -->
  <div class="modal fade" id="documentsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-semibold">Client Documents</h5>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
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

  // Client Management
  const clients = <?= json_encode($clients) ?>;
  const listContainer = document.getElementById("clientsList");
  const detailsDiv = document.getElementById("clientDetails");

  function escapeHtml(s){
    if (s === null || s === undefined) return '';
    return String(s).replace(/[&<>"']/g, function(ch){
      return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch]);
    });
  }

  clients.forEach((c, i) => {
    const clientItem = document.createElement("div");
    clientItem.classList.add("client-item", "animate__animated", "animate__fadeInUp");

    const fullName = [c.FirstName, c.MiddleName, c.LastName].filter(Boolean).join(' ');

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

    let detailPhoto = '<?= base_url('uploads/properties/no-image.jpg') ?>';
    if (c.Image && c.Image.trim() !== '') {
      const folder = (c.employmentStatus && c.employmentStatus.toLowerCase() === 'ofw') ? 'ofw' : 'locallyemployed';
      detailPhoto = '<?= base_url('') ?>' + 'uploads/' + folder + '/' + c.Image;
    }
    
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

    const viewDocsBtn = document.getElementById('viewDocsBtn');
    if (viewDocsBtn) viewDocsBtn.addEventListener('click', () => openDocumentsById(c.UserID || c.UserId || c.userID));

    (async () => {
      try {
        const res = await fetch('/users/clientBookings/' + encodeURIComponent(c.UserID || c.UserId), { 
          credentials: 'same-origin', 
          headers: { 'X-Requested-With': 'XMLHttpRequest' } 
        });
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
    const defaultImage = "<?= base_url('uploads/properties/no-image.jpg') ?>";
    document.getElementById("idImage").src = src && src.trim() !== "" ? src : defaultImage;
    new bootstrap.Modal(document.getElementById("idModal")).show();
  }

  async function openDocumentsById(userId) {
    const body = document.getElementById('documentsModalBody');
    if (!body) return;
    if (!userId) { 
      body.innerHTML = '<div class="text-muted small">No user selected.</div>'; 
      new bootstrap.Modal(document.getElementById('documentsModal')).show(); 
      return; 
    }

    try {
      body.innerHTML = '<div class="text-muted small">Loading documents…</div>';
      const res = await fetch('/users/clientDocuments/' + encodeURIComponent(userId), { 
        credentials: 'same-origin', 
        headers: { 'X-Requested-With': 'XMLHttpRequest' } 
      });
      if (!res.ok) {
        body.innerHTML = '<div class="text-danger small">Failed to load documents.</div>';
        new bootstrap.Modal(document.getElementById('documentsModal')).show();
        return;
      }
      const data = await res.json();
      const rows = [];

      const push = (title, url) => { 
        if (!url) return; 
        rows.push({ title, url, isImage: /\.(png|jpe?g|gif)$/i.test(url) }); 
      };

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