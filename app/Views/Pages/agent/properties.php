<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Assigned Properties</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url("assets/styles/agenStyle.css")?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <style>
    .property-card .card-body { padding: 1rem; }
    .property-image { height: 180px; object-fit: cover; width:100%; border-radius:6px; }
    .small-hint { font-size: .85rem; color: #6c757d; }
    .badge-status { padding: .35em .6em; border-radius: .375rem; font-size:.8rem; }
  </style>
</head>
<body>
  <nav class="navbar navbar-light fixed-top shadow-sm bg-white border-bottom">
    <div class="container-fluid d-flex justify-content-between align-items-center px-4">
      <h3 class="mb-0 text-secondary fw-semibold">Assigned Properties</h3>
      <ul class="nav nav-tabs border-0 flex-nowrap">
        <li class="nav-item"><a class="nav-link" href="/users/agentHomepage">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="/users/agentclients">Clients</a></li>
        <li class="nav-item"><a class="nav-link" href="/users/agentbookings">Bookings</a></li>
        <li class="nav-item"><a class="nav-link active" href="/users/agentproperties">Properties</a></li>
        <li class="nav-item"><a class="nav-link" href="/users/agentchat">Chat</a></li>
      </ul>
      <a href="/users/agentprofile" class="btn btn-outline-primary btn-sm">View Profile</a>
    </div>
  </nav>

  <main class="container-fluid mt-5 pt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      

      <div class="d-flex align-items-center gap-2 mt-3">
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

    <div id="propertyContainer" class="row g-4">
      <!-- Cards dynamically inserted here -->
    </div>
  </main>

  <!-- Edit Modal (popup for editing) -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 shadow-sm">
        <div class="modal-header bg-light">
          <h5 class="modal-title fw-semibold">Edit Property</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <form id="editForm">
            <input type="hidden" id="editPropertyID" name="propertyID" />
            <div class="row">
              <div class="col-md-6 text-center">
                <img id="currentImagePreview" src="<?= base_url('uploads/properties/no-image.jpg') ?>" alt="House Image"
                     class="rounded shadow-sm mb-3" style="width:100%; max-width:360px; height:220px; object-fit:cover;">
                <div class="mb-3">
                  <label class="form-label fw-semibold">Replace Image</label>
                  <input type="file" id="newImageInput" name="image" class="form-control" accept="image/*">
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

  <!-- View Modal (popup for viewing details) -->
  <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content border-0 shadow-sm">
        <div class="modal-header bg-light">
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

  <!-- endpoints exposed from PHP (site_url ensures correct base/CI index.php) -->
  <script>
    const apiListUrl = <?= json_encode(site_url('users/agentproperties')) ?>; // should return JSON for AJAX requests
    const updateUrl = <?= json_encode(site_url('property/updateStatus')) ?>;  // endpoint to accept FormData (propertyID, status, image, ...)
    const uploadsBase = <?= json_encode(base_url('uploads/properties/')) ?>;
  </script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="<?= base_url("bootstrap5/js/bootstrap.bundle.min.js") ?>"></script>

  <script>
  let properties = [];
  let currentEditProperty = null;

  document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('refreshBtn').addEventListener('click', loadProperties);
    document.getElementById('filterSelect').addEventListener('change', applyFilter);
    document.getElementById('saveChangesBtn').addEventListener('click', savePropertyChanges);
    loadProperties();
  });

  async function loadProperties() {
    const container = document.getElementById('propertyContainer');
    container.innerHTML = '<div class="text-center py-5">Loading...</div>';
    try {
      const res = await fetch(apiListUrl, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      if (!res.ok) throw new Error('Failed to fetch properties: ' + res.status);
      const data = await res.json();
      properties = Array.isArray(data) ? data : (data.properties ?? data);
      if (!Array.isArray(properties)) properties = [];
      // normalize each property for our front-end keys
      properties = properties.map(p => {
        // Images: if your DB returns a single Image column named Image, convert it.
        if (!p.Images || !Array.isArray(p.Images)) {
          if (p.Image) {
            p.Images = [ p.Image.startsWith('http') ? p.Image : (uploadsBase + (p.Image || 'no-image.jpg')) ];
          } else {
            p.Images = [ uploadsBase + 'no-image.jpg' ];
          }
        }
        p.Title = p.Title ?? p.PropertyTitle ?? p.title ?? 'Untitled';
        p.Location = p.Location ?? p.PropertyLocation ?? p.location ?? '';
        p.Price = p.Price ?? p.price ?? 0;
        // status: prefer New_Status from joins, fallback to status
        p.New_Status = p.New_Status ?? p.status ?? 'Available';
        return p;
      });
      renderList(properties);
    } catch (err) {
      console.error(err);
      document.getElementById('propertyContainer').innerHTML = '<div class="text-danger text-center py-4">Failed to load properties.</div>';
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
      const img = (p.Images && p.Images[0]) ? p.Images[0] : (uploadsBase + 'no-image.jpg');
      const badgeClass = (String(p.New_Status || '').toLowerCase() === 'occupied') ? 'bg-danger' : 'bg-success';
      const div = document.createElement('div');
      div.className = 'col-md-3 col-sm-6';
      div.innerHTML = `
        <div class="card property-card p-3 shadow-sm animate__animated animate__fadeInUp" style="animation-delay:${idx*0.06}s">
          <img src="${escapeHtml(img)}" alt="${escapeHtml(p.Title || 'Property')}" class="property-image mb-3 rounded">
          <h6 class="fw-bold mb-1">${escapeHtml(p.Title || 'Untitled')}</h6>
          <p class="mb-1 text-muted small">Availability: <span class="badge-status ${badgeClass} text-white">${escapeHtml(p.New_Status || '')}</span></p>
          <p class="mb-2 text-muted small">Location: ${escapeHtml(p.Location || 'Unknown')}</p>
          <div class="d-flex justify-content-between align-items-center">
            <button class="btn btn-sm btn-outline-secondary" data-action="edit" data-id="${p.PropertyID}"><i class="bi bi-pencil-square"></i> Edit</button>
            <button class="btn btn-sm btn-primary" data-action="view" data-id="${p.PropertyID}"><i class="bi bi-eye"></i> View</button>
          </div>
        </div>
      `;
      container.appendChild(div);
    });

    // attach delegated listeners
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
    else renderList(properties.filter(p => (p.New_Status || '').toString() === filter));
  }

  function openEditModal(propertyID) {
    const p = properties.find(x => String(x.PropertyID) === String(propertyID));
    if (!p) return Swal.fire({ icon: 'error', title: 'Not found', text: 'Property data not available.' });

    currentEditProperty = p;
    document.getElementById('editPropertyID').value = p.PropertyID;
    document.getElementById('editTitle').value = p.Title ?? '';
    document.getElementById('editLocation').value = p.Location ?? '';
    document.getElementById('editPrice').value = p.Price ?? '';
    document.getElementById('editDescription').value = p.Description ?? '';
    document.getElementById('availabilitySelect').value = p.New_Status ?? p.status ?? 'Available';
    document.getElementById('currentImagePreview').src = (p.Images && p.Images[0]) ? p.Images[0] : (uploadsBase + 'no-image.jpg');
    document.getElementById('newImageInput').value = '';
    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
  }

  async function savePropertyChanges() {
    if (!currentEditProperty) return;
    const form = document.getElementById('editForm');
    const formData = new FormData(form);
    const imageFile = document.getElementById('newImageInput').files[0];
    if (imageFile) formData.append('image', imageFile);

    try {
      Swal.fire({ title: 'Saving...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
      const res = await fetch(updateUrl, {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
      });
      const json = await res.json().catch(()=>null);
      if (!res.ok || !(json && (json.success || json.updated))) {
        throw new Error(json?.error || 'Server returned ' + res.status);
      }

      // Update local copy and UI
      const p = properties.find(x => String(x.PropertyID) === String(currentEditProperty.PropertyID));
      if (p) {
        p.Title = formData.get('title') || p.Title;
        p.Location = formData.get('location') || p.Location;
        p.Price = formData.get('price') || p.Price;
        p.Description = formData.get('description') || p.Description;
        p.New_Status = formData.get('status') || p.New_Status;
        if (json.imageUrl) {
          // server returned url for uploaded image
          p.Images = [ json.imageUrl ];
        } else if (imageFile) {
          // optional: show a local preview by creating an object URL
          p.Images = [ URL.createObjectURL(imageFile) ];
        }
      }

      Swal.close();
      Swal.fire({ icon: 'success', title: 'Saved' });
      bootstrap.Modal.getInstance(document.getElementById('editModal'))?.hide();
      applyFilter();
    } catch (err) {
      console.error(err);
      Swal.close();
      Swal.fire({ icon: 'error', title: 'Failed', text: err.message || 'Update failed' });
    }
  }

  function openViewModal(propertyID) {
    const p = properties.find(x => String(x.PropertyID) === String(propertyID));
    if (!p) return Swal.fire({ icon: 'error', title: 'Not found', text: 'Property data not available.' });

    document.getElementById('viewTitle').innerText = p.Title || 'Property';
    const body = document.getElementById('viewBody');
    const imgs = (p.Images || []).map(src => `<div class="col-md-6 mb-3"><img src="${escapeHtml(src)}" class="img-fluid rounded"></div>`).join('');
    body.innerHTML = `
      <div class="row">
        <div class="col-lg-6">
          <div class="row">${imgs}</div>
        </div>
        <div class="col-lg-6">
          <h4 class="mb-1">${escapeHtml(p.Title || '')}</h4>
          <p class="text-muted small mb-2">Location: ${escapeHtml(p.Location || '')}</p>
          <p><strong>₱ ${Number(p.Price || 0).toLocaleString()}</strong></p>
          <p>${escapeHtml(p.Description || 'No description')}</p>
          <p class="small text-muted">Property ID: ${escapeHtml(p.PropertyID)}</p>
        </div>
      </div>
    `;
    const modal = new bootstrap.Modal(document.getElementById('viewModal'));
    // "Edit" button in view modal opens edit modal for same property
    const editBtn = document.getElementById('editFromViewBtn');
    editBtn.onclick = () => {
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