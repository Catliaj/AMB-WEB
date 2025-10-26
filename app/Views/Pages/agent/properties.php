<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Assigned Properties</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= base_url("bootstrap5/css/bootstrap.min.css")?>" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url("assets/styles/agenStyle.css")?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
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
document.addEventListener("DOMContentLoaded", function () {
  const container = document.getElementById('propertyContainer');
  const filterSelect = document.getElementById('filterSelect');
  let houses = [];
  let currentEditId = null;
  let currentModal = null;

 
  function loadHouses() {
    $.ajax({
      url: "/users/agentproperties",
      type: "GET",
      dataType: "json",
      success: function (data) {
        houses = data;
        renderHouses(houses);
      },
      error: function (err) {
        console.error("Error loading houses:", err);
        container.innerHTML = '<p class="text-danger text-center">Failed to load properties.</p>';
      }
    });
  }

  function renderHouses(list) {
  container.innerHTML = '';

  list.forEach((h, i) => {
    const delay = i * 0.1;
    const carouselId = `carousel${h.PropertyID}`;

    const carouselItems = (h.Images || []).map((img, index) => `
      <div class="carousel-item ${index === 0 ? 'active' : ''}">
        <img src="${img}" class="d-block w-100 rounded" style="height:180px; object-fit:cover;">
      </div>
    `).join('');

    const card = `
      <div class="col-md-3 col-sm-6">
        <div class="card p-4 shadow-sm animate__animated animate__fadeInUp" style="animation-delay:${delay}s;">
          <div id="${carouselId}" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
              ${carouselItems}
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
              <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
              <span class="carousel-control-next-icon"></span>
            </button>
          </div>
          <h6 class="fw-bold text-dark mt-3 mb-1">${h.Title}</h6>
          <p class="mb-1 text-muted small">Availability: <span class="${h.New_Status === 'Occupied' ? 'text-danger' : 'text-success'}">${h.New_Status}</span></p>
          <p class="mb-1 text-muted small">Location: ${h.Location || 'Unknown'}</p>
          <div class="d-flex justify-content-between align-items-center">
            <button class="btn btn-sm btn-outline-secondary" onclick="openEditModal(${h.PropertyID})">
              <i class="bi bi-pencil-square"></i> Edit
            </button>
            <button class="btn btn-sm btn-primary">View</button>
          </div>
        </div>
      </div>
    `;
    container.insertAdjacentHTML('beforeend', card);
  });
}
  

  // Filter
  window.filterHouses = function (filter) {
    if (filter === 'all') renderHouses(houses);
    else renderHouses(houses.filter(h => h.New_Status === filter));
  };

  // Edit Modal (same logic you had)
  window.openEditModal = function (id) {
    currentEditId = id;
    const house = houses.find(h => h.PropertyID == id);
    document.getElementById('editHouseName').textContent = house.PropertyName;
    document.getElementById('availabilitySelect').value = house.New_Status || 'Available';
    document.getElementById('currentImagePreview').src = house.PropertyImage 
      ? `data:image/jpeg;base64,${house.PropertyImage}` 
      : 'https://via.placeholder.com/400x250';
    document.getElementById('newImageInput').value = '';
    currentModal = new bootstrap.Modal(document.getElementById('editModal'));
    currentModal.show();
  };

  // Save (update) property status
  window.saveHouseChanges = function () {
    const newStatus = document.getElementById('availabilitySelect').value;
    $.ajax({
      url: "/property/updateStatus",
      type: "POST",
      data: {
        propertyID: currentEditId,
        status: newStatus
      },
      success: function () {
        const house = houses.find(h => h.PropertyID == currentEditId);
        if (house) house.New_Status = newStatus;
        currentModal.hide();
        filterHouses(filterSelect.value);
      }
    });
  };

  // Initial load
  loadHouses();
});
</script>

<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="<?= base_url("bootstrap5/js/bootstrap.bundle.min.js")?>"></script>


  <script src="<?= base_url("bootstrap5/js/bootstrap.bundle.min.js")?>"></script>
</body>
</html>
