<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Assigned Properties</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <link href="<?= base_url("bootstrap5/css/bootstrap.min.css")?>" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url("assets/styles/agenStyle.css")?>">
</head>

<body>
  <!-- ✅ Navbar (copied from dashboard) -->
  <nav class="navbar navbar-light fixed-top shadow-sm bg-white border-bottom">
    <div class="container-fluid d-flex justify-content-between align-items-center px-4">
      <h3 class="mb-0 text-secondary fw-semibold">Agent Dashboard</h3>

      <ul class="nav nav-tabs border-0 flex-nowrap">
        <li class="nav-item"><a class="nav-link" href="/users/agentHomepage">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="/users/agentclients">Clients</a></li>
        <li class="nav-item"><a class="nav-link" href="/users/agentbookings">Bookings</a></li>
        <li class="nav-item"><a class="nav-link active" href="/users/agentprofile">Properties</a></li>
      </ul>

      <button class="btn btn-outline-primary btn-sm">View Profile</button>
    </div>
  </nav>
<br>
  <div class="container-fluid mt-5 pt-4">
    <div class="d-flex justify-content-between align-items-center mb-3"></div>
      <h4 class="fw-bold text-secondary">Assigned Properties</h4><br>
      <div>
        <button class="btn btn-success btn-sm" onclick="filterHouses('all')">Filter By</button>
        <select id="filterSelect" class="form-select d-inline-block ms-2" style="width: auto;" onchange="filterHouses(this.value)">
          <option value="all">All</option>
          <option value="Available">Available</option>
          <option value="Occupied">Occupied</option>
        </select>
      </div>
    </div>
<br>
    <div class="row g-4" id="propertyContainer">
      <!-- Cards dynamically displayed -->
    </div>
  </div>

  <!-- Edit Property Modal -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-sm">
        <div class="modal-header bg-light">
          <h5 class="modal-title fw-semibold">Edit Property Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <p id="editHouseName" class="fw-bold mb-2"></p>

          <div class="text-center mb-3">
            <img id="currentImagePreview" src="" alt="House Image"
                 class="rounded shadow-sm"
                 style="width: 100%; max-width: 300px; height: 180px; object-fit: cover;">
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Replace Image</label>
            <input type="file" id="newImageInput" class="form-control" accept="image/*">
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Availability</label>
            <select id="availabilitySelect" class="form-select">
              <option value="Available">Available</option>
              <option value="Occupied">Occupied</option>
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary btn-sm" onclick="saveHouseChanges()">Save Changes</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    const houses = [
      { id: 1, name: 'HOUSE 1', location: 'Location', floor: '40 sq m', lot: '40 sq m', bed: 1, status: 'Available', img: 'https://via.placeholder.com/400x250' },
      { id: 2, name: 'HOUSE 2', location: 'Location', floor: '40 sq m', lot: '40 sq m', bed: 1, status: 'Available', img: 'https://via.placeholder.com/400x250' },
      { id: 3, name: 'HOUSE 3', location: 'Location', floor: '40 sq m', lot: '40 sq m', bed: 1, status: 'Occupied', img: 'https://via.placeholder.com/400x250' },
      { id: 4, name: 'HOUSE 4', location: 'Location', floor: '40 sq m', lot: '40 sq m', bed: 1, status: 'Available', img: 'https://via.placeholder.com/400x250' },
      { id: 5, name: 'HOUSE 5', location: 'Location', floor: '40 sq m', lot: '40 sq m', bed: 1, status: 'Occupied', img: 'https://via.placeholder.com/400x250' },
      { id: 6, name: 'HOUSE 6', location: 'Location', floor: '40 sq m', lot: '40 sq m', bed: 1, status: 'Available', img: 'https://via.placeholder.com/400x250' },
      { id: 7, name: 'HOUSE 7', location: 'Location', floor: '40 sq m', lot: '40 sq m', bed: 1, status: 'Available', img: 'https://via.placeholder.com/400x250' },
      { id: 8, name: 'HOUSE 8', location: 'Location', floor: '40 sq m', lot: '40 sq m', bed: 1, status: 'Occupied', img: 'https://via.placeholder.com/400x250' }
    ];

    const container = document.getElementById('propertyContainer');
    let currentEditId = null;
    let currentModal = null;

    function renderHouses(list) {
      container.innerHTML = '';
      list.forEach((h, i) => {
        const delay = i * 0.1;
        const card = `
          <div class="col-md-3 col-sm-6">
            <div class="card p-4 shadow-sm animate__animated animate__fadeInUp" style="animation-delay: ${delay}s;">
              <img src="${h.img}" alt="${h.name}" class="rounded mb-3" style="width:100%; height:180px; object-fit:cover;">
              <h6 class="fw-bold text-dark mb-1">${h.name}</h6>
              <p class="mb-1 text-muted small">Location: ${h.location}</p>
              <p class="mb-1 text-muted small">Availability: <span class="${h.status === 'Occupied' ? 'text-danger' : 'text-success'}">${h.status}</span></p>
              <p class="mb-1 text-muted small">Floor: ${h.floor}</p>
              <p class="mb-1 text-muted small">Lot Area: ${h.lot}</p>
              <p class="mb-2 text-muted small">Bedrooms: ${h.bed}</p>
              <div class="d-flex justify-content-between align-items-center">
                <button class="btn btn-sm btn-outline-secondary" onclick="openEditModal(${h.id})">
                  <i class="bi bi-pencil-square"></i> Edit
                </button>
                <button class="btn btn-sm btn-primary">View</button>
              </div>
            </div>
          </div>`;
        container.insertAdjacentHTML('beforeend', card);
      });
    }

    function filterHouses(filter) {
      if (filter === 'all') renderHouses(houses);
      else renderHouses(houses.filter(h => h.status === filter));
    }

    function openEditModal(id) {
      currentEditId = id;
      const house = houses.find(h => h.id === id);
      document.getElementById('editHouseName').textContent = house.name;
      document.getElementById('availabilitySelect').value = house.status;
      document.getElementById('currentImagePreview').src = house.img;
      document.getElementById('newImageInput').value = '';
      currentModal = new bootstrap.Modal(document.getElementById('editModal'));
      currentModal.show();
    }

    function saveHouseChanges() {
      const newStatus = document.getElementById('availabilitySelect').value;
      const newImageInput = document.getElementById('newImageInput');
      const house = houses.find(h => h.id === currentEditId);
      house.status = newStatus;
      if (newImageInput.files && newImageInput.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
          house.img = e.target.result;
          renderHousesAfterSave();
        };
        reader.readAsDataURL(newImageInput.files[0]);
      } else {
        renderHousesAfterSave();
      }
    }

    function renderHousesAfterSave() {
      currentModal.hide();
      const filter = document.getElementById('filterSelect').value;
      filterHouses(filter);
    }

    renderHouses(houses);
  </script>

  <script src="<?= base_url("bootstrap5/js/bootstrap.bundle.min.js")?>"></script>
</body>
</html>
