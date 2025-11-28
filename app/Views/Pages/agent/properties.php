<!doctype html>
<html lang="en" data-theme="light">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Assigned Properties - ABM Property</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  
  <style>

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
    }

    .nav-link-custom:hover,
    .nav-link-custom.active {
      color: var(--primary) !important;
    }

    .main-links .nav-item {
      margin: 0 1.5rem;
    }

    /* MAIN CONTENT */
    .main-content {
      padding: 2rem;
      min-height: calc(100vh - 100px);
    }

    /* CARDS */
    .card, .property-card {
      background-color: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      box-shadow: 0 2px 10px var(--shadow);
      transition: transform 0.2s ease;
    }

    .property-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 4px 15px var(--shadow);
    }

    .property-image { 
      height: 180px; 
      object-fit: cover; 
      width: 100%; 
      border-radius: 12px;
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

    /* BADGES */
    .badge-status { 
      padding: 0.35em 0.6em; 
      border-radius: 0.375rem; 
      font-size: 0.8rem; 
    }

    /* FORMS */
    .form-control, .form-select {
      background-color: var(--card-bg);
      border: 1px solid var(--border-color);
      color: var(--text-color);
    }

    .form-control:focus, .form-select:focus {
      background-color: var(--card-bg);
      border-color: var(--primary);
      color: var(--text-color);
    }

    /* MODAL */
    .modal-content {
      background-color: var(--card-bg);
      color: var(--text-color);
    }

    .modal-header {
      background-color: var(--light-bg);
      border-bottom: 1px solid var(--border-color);
    }

    /* TEXT */
    h1, h2, h3, h4, h5, h6, p {
      color: var(--text-color);
    }

    .text-muted {
      color: var(--text-muted) !important;
    }

    /* IMAGE THUMBNAILS */
    .img-thumb-wrap { 
      width: 120px; 
      position: relative; 
    }
    
    .img-thumb-wrap .remove-new { 
      position: absolute; 
      top: 4px; 
      right: 4px; 
      z-index: 5; 
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
        <img src="<?= base_url('assets/img/AMB_logo.png') ?>" alt="AMB Logo" height="50" class="me-2">
        <span class="logo-text">PROPERTY</span>
      </a>

      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mx-lg-auto text-center main-links">
          <li class="nav-item"><a class="nav-link nav-link-custom" href="/users/agentHomepage">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link nav-link-custom" href="/users/agentclients">Clients</a></li>
          <li class="nav-item"><a class="nav-link nav-link-custom" href="/users/agentbookings">Bookings</a></li>
          <li class="nav-item"><a class="nav-link nav-link-custom active" href="/users/agentproperties">Properties</a></li>
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
  <main class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="fw-bold">Assigned Properties</h3>
      <div class="d-flex gap-2">
        <select id="filterSelect" class="form-select form-select-sm" style="width:auto">
          <option value="all">All</option>
          <option value="Available">Available</option>
          <option value="Occupied">Occupied</option>
          <option value="Pending">Pending</option>
          <option value="Cancelled">Cancelled</option>
        </select>
        <button id="refreshBtn" class="btn btn-outline-secondary btn-sm">Refresh</button>
      </div>
    </div>

    <div id="propertyContainer" class="row g-4"></div>
  </main>

  <!-- Edit Modal -->
  <div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-semibold">Edit Property</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="editForm">
            <input type="hidden" id="editPropertyID" name="propertyID" />
            <div class="row">
              <div class="col-md-6 text-center">
                <div id="currentImagesContainer" class="mb-3 d-flex flex-wrap gap-2 justify-content-center"></div>
                <div class="mb-2 small text-muted">Existing images. Click remove to mark for deletion.</div>
                <div class="mb-3">
                  <label class="form-label fw-semibold">Add / Replace Images</label>
                  <input type="file" id="newImagesInput" name="images[]" class="form-control" accept="image/*" multiple>
                  <div id="newImagesPreview" class="mt-2 d-flex flex-wrap gap-2"></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label fw-semibold">Title</label>
                  <input type="text" id="editTitle" name="title" class="form-control">
                </div>
                <div class="mb-3">
                  <label class="form-label fw-semibold">Availability</label>
                  <select id="availabilitySelect" name="status" class="form-select">
                    <option value="Available">Available</option>
                    <option value="Occupied">Occupied</option>
                    <option value="Pending">Pending</option>
                    <option value="Cancelled">Cancelled</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-semibold">Location</label>
                  <input type="text" id="editLocation" name="location" class="form-control">
                </div>
                <div class="mb-3">
                  <label class="form-label fw-semibold">Price</label>
                  <input type="number" id="editPrice" name="price" class="form-control" step="0.01">
                </div>
                <div class="mb-3">
                  <label class="form-label fw-semibold">Description</label>
                  <textarea id="editDescription" name="description" class="form-control" rows="4"></textarea>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary btn-sm" id="saveChangesBtn">Save Changes</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View Modal -->
  <div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-semibold" id="viewTitle">Property</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="viewBody"></div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
          <button id="editFromViewBtn" class="btn btn-primary btn-sm">Edit</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    const apiListUrl = <?= json_encode(site_url('users/agentproperties')) ?>;
    const updateUrl = <?= json_encode(site_url('property/updateStatus')) ?>;
    const uploadsBase = <?= json_encode(base_url('uploads/properties/')) ?>;
  </script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

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

  document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    const themeIcon = document.getElementById('themeIcon');
    document.documentElement.setAttribute('data-theme', savedTheme);
    
    if (savedTheme === 'dark') {
      themeIcon.classList.remove('bi-sun-fill');
      themeIcon.classList.add('bi-moon-fill');
    }
  });

  // Property Management Code (keeping your existing logic)
  let properties = [];
  let currentEditProperty = null;
  let imagesMarkedForDeletion = [];
  let newSelectedFiles = [];

  document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('refreshBtn').addEventListener('click', loadProperties);
    document.getElementById('filterSelect').addEventListener('change', applyFilter);
    document.getElementById('saveChangesBtn').addEventListener('click', savePropertyChanges);

    const newImgInput = document.getElementById('newImagesInput');
    if (newImgInput) {
      newImgInput.addEventListener('change', (ev) => {
        const files = Array.from(ev.target.files || []);
        files.forEach(f => {
          const key = `${f.name}_${f.size}`;
          const exists = newSelectedFiles.some(x => `${x.name}_${x.size}` === key);
          if (!exists) newSelectedFiles.push(f);
        });
        newImgInput.value = '';
        renderNewFilesPreview();
      });
    }

    loadProperties();
  });

  function renderNewFilesPreview() {
    const preview = document.getElementById('newImagesPreview');
    preview.innerHTML = '';
    newSelectedFiles.forEach((f, i) => {
      const url = URL.createObjectURL(f);
      const wrapper = document.createElement('div');
      wrapper.className = 'img-thumb-wrap img-thumbnail d-inline-block me-2 mb-2';
      wrapper.style.width = '120px';
      wrapper.innerHTML = `
        <img src="${escapeHtml(url)}" style="width:120px;height:80px;object-fit:cover;display:block;border-radius:4px;">
        <button type="button" class="btn btn-sm btn-danger remove-new" data-new-index="${i}">×</button>
      `;
      preview.appendChild(wrapper);
    });
    preview.querySelectorAll('button[data-new-index]').forEach(b => {
      b.addEventListener('click', () => {
        const idx = Number(b.getAttribute('data-new-index'));
        if (!isNaN(idx)) {
          newSelectedFiles.splice(idx, 1);
          renderNewFilesPreview();
        }
      });
    });
  }

  async function loadProperties() {
    const container = document.getElementById('propertyContainer');
    container.innerHTML = '<div class="text-center py-5">Loading...</div>';
    try {
      const res = await fetch(apiListUrl, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      if (!res.ok) throw new Error('Failed to fetch properties');
      const data = await res.json();
      properties = Array.isArray(data) ? data : (data.properties ?? data);
      if (!Array.isArray(properties)) properties = [];

      properties = properties.map(p => {
        if (!p.Images || !Array.isArray(p.Images)) {
          if (p.Image) {
            const src = (p.Image && String(p.Image).startsWith('http')) ? p.Image : (uploadsBase + (p.Image || 'no-image.jpg'));
            p.Images = [{ id: null, filename: (p.Image || null), url: src }];
          } else {
            p.Images = [{ id: null, filename: 'no-image.jpg', url: uploadsBase + 'no-image.jpg' }];
          }
        } else {
          p.Images = p.Images.map(img => {
            if (!img) return { id: null, filename: 'no-image.jpg', url: uploadsBase + 'no-image.jpg' };
            if (typeof img === 'object' && img.url) return img;
            return { id: null, filename: String(img).split('/').pop(), url: img };
          });

          const seen = new Set();
          p.Images = p.Images.filter(img => {
            if (!img) return false;
            const key = img.id || img.filename?.toLowerCase() || img.url?.toLowerCase();
            if (seen.has(key)) return false;
            seen.add(key);
            return true;
          });
        }

        p.Title = p.Title ?? p.PropertyTitle ?? 'Untitled';
        p.Location = p.Location ?? p.PropertyLocation ?? '';
        p.Price = p.Price ?? 0;
        p.New_Status = p.New_Status ?? p.status ?? 'Available';
        return p;
      });

      renderList(properties);
    } catch (err) {
      console.error(err);
      container.innerHTML = '<div class="text-danger text-center py-4">Failed to load properties.</div>';
    }
  }

  function renderList(list) {
    const container = document.getElementById('propertyContainer');
    container.innerHTML = '';
    if (!list.length) {
      container.innerHTML = '<div class="text-center text-muted py-4">No properties assigned.</div>';
      return;
    }

    list.forEach((p, idx) => {
      const firstImg = p.Images?.[0] || { url: uploadsBase + 'no-image.jpg' };
      const img = (typeof firstImg === 'object') ? firstImg.url : firstImg;
      const badgeClass = String(p.New_Status || '').toLowerCase() === 'occupied' ? 'bg-danger' : 'bg-success';
      const col = document.createElement('div');
      col.className = 'col-md-3 col-sm-6';
      col.innerHTML = `
        <div class="card property-card p-3 animate__animated animate__fadeInUp" style="animation-delay:${idx*0.06}s">
          <img src="${escapeHtml(img)}" alt="${escapeHtml(p.Title)}" class="property-image mb-3">
          <h6 class="fw-bold mb-1">${escapeHtml(p.Title)}</h6>
          <p class="mb-1 text-muted small">Status: <span class="badge-status ${badgeClass} text-white">${escapeHtml(p.New_Status)}</span></p>
          <p class="mb-2 text-muted small">${escapeHtml(p.Location)}</p>
          <div class="d-flex justify-content-between">
            <button class="btn btn-sm btn-outline-secondary" data-action="edit" data-id="${p.PropertyID}"><i class="bi bi-pencil"></i> Edit</button>
            <button class="btn btn-sm btn-primary" data-action="view" data-id="${p.PropertyID}"><i class="bi bi-eye"></i> View</button>
          </div>
        </div>
      `;
      container.appendChild(col);
    });

    container.querySelectorAll('button[data-action="edit"]').forEach(btn => {
      btn.addEventListener('click', () => openEditModal(btn.dataset.id));
    });
    container.querySelectorAll('button[data-action="view"]').forEach(btn => {
      btn.addEventListener('click', () => openViewModal(btn.dataset.id));
    });
  }

  function applyFilter() {
    const filter = document.getElementById('filterSelect').value;
    if (filter === 'all') renderList(properties);
    else renderList(properties.filter(p => p.New_Status === filter));
  }

  function openEditModal(propertyID) {
    const p = properties.find(x => String(x.PropertyID) === String(propertyID));
    if (!p) return Swal.fire({ icon: 'error', title: 'Not found' });

    currentEditProperty = p;
    document.getElementById('editPropertyID').value = p.PropertyID;
    document.getElementById('editTitle').value = p.Title ?? '';
    document.getElementById('editLocation').value = p.Location ?? '';
    document.getElementById('editPrice').value = p.Price ?? '';
    document.getElementById('editDescription').value = p.Description ?? '';
    document.getElementById('availabilitySelect').value = p.New_Status ?? 'Available';

    imagesMarkedForDeletion = [];
    const container = document.getElementById('currentImagesContainer');
    container.innerHTML = '';

    const imgs = Array.isArray(p.Images) ? p.Images : [{ url: uploadsBase + 'no-image.jpg' }];
    const seen = new Set();
    imgs.forEach(el => {
      if (!el) return;
      const key = el.id || el.filename?.toLowerCase() || el.url?.toLowerCase();
      if (seen.has(key)) return;
      seen.add(key);

      const src = typeof el === 'object' ? el.url : el;
      const wrapper = document.createElement('div');
      wrapper.className = 'position-relative';
      wrapper.style.width = '120px';
      wrapper.innerHTML = `
        <img src="${escapeHtml(src)}" class="img-thumbnail" style="width:120px;height:80px;object-fit:cover;"> 
        <button type="button" class="btn btn-sm btn-danger position-absolute" style="top:4px;right:4px;" data-image-id="${el.id ?? ''}" data-image-name="${escapeHtml(el.filename)}">Remove</button>
      `;
      container.appendChild(wrapper);
    });

    newSelectedFiles = [];
    document.getElementById('newImagesPreview').innerHTML = '';
    document.getElementById('newImagesInput').value = '';

    container.querySelectorAll('button[data-image-name]').forEach(b => {
      b.addEventListener('click', () => {
        const id = b.getAttribute('data-image-id');
        const name = b.getAttribute('data-image-name');
        const wrapper = b.closest('div.position-relative');
        if (wrapper) wrapper.remove();
        if (id && id !== '') imagesMarkedForDeletion.push(id);
        else if (name) imagesMarkedForDeletion.push(name);
      });
    });

    new bootstrap.Modal(document.getElementById('editModal')).show();
  }

  async function savePropertyChanges() {
    if (!currentEditProperty) return;
    const form = document.getElementById('editForm');
    const formData = new FormData(form);

    newSelectedFiles.forEach(f => formData.append('images[]', f));
    imagesMarkedForDeletion.forEach(d => formData.append('deleteImages[]', d));

    try {
      Swal.fire({ title: 'Saving...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
      const res = await fetch(updateUrl, { method: 'POST', credentials: 'same-origin', body: formData });
      const json = await res.json().catch(() => null);
      if (!res.ok || !(json?.success || json?.updated)) {
        throw new Error(json?.error || 'Update failed');
      }

      await loadProperties();
      Swal.close();
      Swal.fire({ icon: 'success', title: 'Saved' });
      bootstrap.Modal.getInstance(document.getElementById('editModal'))?.hide();

      newSelectedFiles = [];
      imagesMarkedForDeletion = [];
    } catch (err) {
      console.error(err);
      Swal.close();
      Swal.fire({ icon: 'error', title: 'Failed', text: err.message });
    }
  }

  function openViewModal(propertyID) {
    const p = properties.find(x => String(x.PropertyID) === String(propertyID));
    if (!p) return Swal.fire({ icon: 'error', title: 'Not found' });

    document.getElementById('viewTitle').innerText = p.Title || 'Property';
    const body = document.getElementById('viewBody');
    const imgs = (p.Images || []).map(el => {
      const src = typeof el === 'object' ? el.url : el;
      return `<div class="col-md-6 mb-3"><img src="${escapeHtml(src)}" class="img-fluid rounded"></div>`;
    }).join('');
    body.innerHTML = `
      <div class="row">
        <div class="col-lg-6">
          <div class="row">${imgs}</div>
        </div>
        <div class="col-lg-6">
          <h4 class="mb-1">${escapeHtml(p.Title)}</h4>
          <p class="text-muted small mb-2">${escapeHtml(p.Location)}</p>
          <p><strong>₱ ${Number(p.Price).toLocaleString()}</strong></p>
          <p>${escapeHtml(p.Description || 'No description')}</p>
        </div>
      </div>
    `;
    const modal = new bootstrap.Modal(document.getElementById('viewModal'));
    document.getElementById('editFromViewBtn').onclick = () => {
      modal.hide();
      openEditModal(propertyID);
    };
    modal.show();
  }

  function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str).replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[s]);
  }
  </script>
</body>
</html>