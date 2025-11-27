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
    .img-thumb-wrap { width:120px; position:relative; }
    .img-thumb-wrap .remove-new { position:absolute; top:4px; right:4px; z-index:5; }
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
                <div id="currentImagesContainer" class="mb-3 d-flex flex-wrap gap-2 justify-content-center">
                  <!-- thumbnails inserted here -->
                </div>
                <div class="mb-2 small-hint text-muted">Existing images. Click remove to mark for deletion (changes saved when you press Save).</div>
                <div class="mb-3">
                  <label class="form-label fw-semibold">Add / Replace Images</label>
                  <input type="file" id="newImagesInput" name="images[]" class="form-control" accept="image/*" multiple>
                  <div id="newImagesPreview" class="mt-2 d-flex flex-wrap gap-2"></div>
                  <div class="small-hint mt-1">You can add multiple images. New files are uploaded alongside existing ones.</div>
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
    const updateUrl = <?= json_encode(site_url('property/updateStatus')) ?>;  // endpoint to accept FormData (propertyID, status, images[], deleteImages[], ...)
    const uploadsBase = <?= json_encode(base_url('uploads/properties/')) ?>;
  </script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="<?= base_url("bootstrap5/js/bootstrap.bundle.min.js") ?>"></script>

  <script>
  // State
  let properties = [];
  let currentEditProperty = null;
  let imagesMarkedForDeletion = []; // for existing images (ids or filenames)
  let newSelectedFiles = []; // managed list of files the user added in the modal

  document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('refreshBtn').addEventListener('click', loadProperties);
    document.getElementById('filterSelect').addEventListener('change', applyFilter);
    document.getElementById('saveChangesBtn').addEventListener('click', savePropertyChanges);

    // Manage newly selected files using newSelectedFiles to avoid duplicates and double-append
    const newImgInput = document.getElementById('newImagesInput');
    if (newImgInput) {
      newImgInput.addEventListener('change', (ev) => {
        const files = Array.from(ev.target.files || []);
        // Add only files not already present (compare by name + size)
        files.forEach(f => {
          const key = `${f.name}_${f.size}`;
          const exists = newSelectedFiles.some(x => `${x.name}_${x.size}` === key);
          if (!exists) newSelectedFiles.push(f);
        });
        // clear the input to allow re-selecting same files later if user removed them
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
    // attach listeners
    preview.querySelectorAll('button[data-new-index]').forEach(b => {
      b.addEventListener('click', () => {
        const idx = Number(b.getAttribute('data-new-index'));
        if (!isNaN(idx)) {
          // revoke objectURL for the removed file if needed (optional)
          try { URL.revokeObjectURL(newSelectedFiles[idx] && newSelectedFiles[idx].preview); } catch(e){}
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
      if (!res.ok) throw new Error('Failed to fetch properties: ' + res.status);
      const data = await res.json();
      properties = Array.isArray(data) ? data : (data.properties ?? data);
      if (!Array.isArray(properties)) properties = [];

      // normalize and dedupe images
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
            if (typeof img === 'object' && img.url) return { id: img.id ?? null, filename: img.filename ?? null, url: img.url };
            const filename = (function(u){ try { const parsed = new URL(u, location.origin); const parts = parsed.pathname.split('/'); return decodeURIComponent(parts.pop() || parsed.pathname); } catch(e){ return String(u).split('/').pop().split('?')[0]; } })(img);
            return { id: null, filename: filename, url: img };
          });

          // Deduplicate images by id (preferred) or filename/url (case-insensitive)
          const seen = new Set();
          p.Images = p.Images.filter(img => {
            if (!img) return false;
            const key = (img.id && img.id !== null) ? String(img.id) : (img.filename ? String(img.filename).toLowerCase() : String(img.url).toLowerCase());
            if (seen.has(key)) return false;
            seen.add(key);
            return true;
          });
        }

        p.Title = p.Title ?? p.PropertyTitle ?? p.title ?? 'Untitled';
        p.Location = p.Location ?? p.PropertyLocation ?? p.location ?? '';
        p.Price = p.Price ?? p.price ?? 0;
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
      const firstImg = (p.Images && p.Images[0]) ? p.Images[0] : { url: uploadsBase + 'no-image.jpg' };
      const img = (typeof firstImg === 'object') ? firstImg.url : firstImg;
      const badgeClass = (String(p.New_Status || '').toLowerCase() === 'occupied') ? 'bg-danger' : 'bg-success';
      const col = document.createElement('div');
      col.className = 'col-md-3 col-sm-6';
      col.innerHTML = `
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

    // Existing images: render thumbnails and allow marking for deletion
    imagesMarkedForDeletion = [];
    const container = document.getElementById('currentImagesContainer');
    container.innerHTML = '';

    const imgs = Array.isArray(p.Images) ? p.Images : (p.Images ? [p.Images] : [{ url: uploadsBase + 'no-image.jpg' }]);

    // Deduplicate before rendering (safety)
    const seen = new Set();
    imgs.forEach(el => {
      if (!el) return;
      const id = (typeof el === 'object') ? (el.id ?? null) : null;
      const filename = (typeof el === 'object') ? (el.filename ?? null) : (function(u){ try { const parsed = new URL(u, location.origin); const parts = parsed.pathname.split('/'); return decodeURIComponent(parts.pop() || parsed.pathname); } catch(e){ return String(u).split('/').pop().split('?')[0]; } })(el);
      const key = (id && id !== null) ? String(id) : (filename ? String(filename).toLowerCase() : (typeof el === 'object' ? String(el.url).toLowerCase() : String(el).toLowerCase()));
      if (seen.has(key)) return;
      seen.add(key);

      const src = (typeof el === 'object') ? el.url : el;
      const wrapper = document.createElement('div');
      wrapper.className = 'position-relative';
      wrapper.style.width = '120px';
      wrapper.innerHTML = `
        <img src="${escapeHtml(src)}" class="img-thumbnail" style="width:120px;height:80px;object-fit:cover;"> 
        <button type="button" class="btn btn-sm btn-danger position-absolute" style="top:4px;right:4px;" data-image-id="${id ?? ''}" data-image-name="${escapeHtml(filename)}">Remove</button>
      `;
      container.appendChild(wrapper);
    });

    // reset new files and preview when opening
    newSelectedFiles = [];
    document.getElementById('newImagesPreview').innerHTML = '';
    document.getElementById('newImagesInput').value = '';

    // attach remove listeners: remove thumbnail immediately and record id/name
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

    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
  }

  async function savePropertyChanges() {
    if (!currentEditProperty) return;
    const form = document.getElementById('editForm');
    const formData = new FormData(form);

    // Append only our managed newSelectedFiles (prevents duplicates / double-appends)
    newSelectedFiles.forEach(f => formData.append('images[]', f));

    // Send delete entries as repeated fields so most backends (PHP) receive arrays
    imagesMarkedForDeletion.forEach(d => formData.append('deleteImages[]', d));

    try {
      Swal.fire({ title: 'Saving...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
      const res = await fetch(updateUrl, {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
      });
      const json = await res.json().catch(() => null);
      if (!res.ok || !(json && (json.success || json.updated))) {
        throw new Error(json?.error || 'Server returned ' + res.status);
      }

      // Do NOT attempt to merge images on the client side (this often causes duplication).
      // Instead refresh authoritative data from server.
      await loadProperties();

      Swal.close();
      Swal.fire({ icon: 'success', title: 'Saved' });
      bootstrap.Modal.getInstance(document.getElementById('editModal'))?.hide();

      // reset newSelectedFiles and deletion list
      newSelectedFiles = [];
      imagesMarkedForDeletion = [];
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
    const imgs = (p.Images || []).map(el => {
      const src = (typeof el === 'object') ? el.url : el;
      return `<div class="col-md-6 mb-3"><img src="${escapeHtml(src)}" class="img-fluid rounded"></div>`;
    }).join('');
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