<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Properties | Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
   <link rel="stylesheet" href="<?= base_url('bootstrap5/css/bootstrap.min.css')?>">
  <link rel="stylesheet" href="<?= base_url('assets/styles/admin-style.css')?>">
  <link rel="stylesheet" href="<?= base_url('client/clientstyle.css')?>">
  <style>
    :root {
      --bg: #f6f8fb;
      --card: #ffffff;
      --text: #1f2937;
      --muted: #6b7280;
      --accent1: #4e9eff;
      --accent2: #2a405a;
      --accent3: #68b76b;
      --shadow: 0 6px 18px rgba(20,25,30,0.06);
      --divider: #e6e9ef;
      --hover-overlay: rgba(0,0,0,0.04);
    }
    /* Sidebar and table header overrides to use the light palette */
    .sidebar { background: linear-gradient(120deg, #d3f0ff 0%, #c8f5d2 100%); border-color: var(--divider); }
    html[data-theme="dark"] .sidebar { background: linear-gradient(120deg, #252e42 0%, #2d4038 100%); }
    .nav a { color: var(--text); }
    thead { background: var(--th-bg, var(--card)); }
    th { color: var(--th-text, var(--text)); }
    .filters-bar { background: white; padding: 10px; border-radius: 8px; }
  </style>

  <style>
    /* smaller preview images */


    #multiPreview {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 10px;
      max-height: 150px;
      overflow-y: auto;
      border: 1px solid var(--border-color, #ccc);
      padding: 6px;
      border-radius: 8px;
    }
    p{
      color: white;
    }

    #multiPreview div {
      position: relative;
      display: inline-block;
    }

    #multiPreview img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 6px;
      border: 1px solid #ddd;
    }

    #multiPreview .remove-img {
      position: absolute;
      top: -6px;
      right: -6px;
      background: rgba(0,0,0,0.6);
      color: white;
      border: none;
      border-radius: 50%;
      width: 18px;
      height: 18px;
      cursor: pointer;
      line-height: 16px;
      font-size: 14px;
    }


    label{
      color: white;
    }
    #newDescription {
      resize: vertical; 
      overflow-y: auto;
      max-height: 200px; 
    }

    #newDescription {
      overflow-y: scroll;
    }

    .image-preview-container {
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    height: 300px;
    background: #f8f9fa;
    border-radius: 10px;
    overflow: hidden;
  }

  .image-preview-container img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
  }

  .carousel-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0, 0, 0, 0.4);
    color: white;
    border: none;
    font-size: 24px;
    padding: 10px 15px;
    cursor: pointer;
    border-radius: 50%;
  }

  .carousel-btn:hover {
    background: rgba(0, 0, 0, 0.6);
  }

  .prev-btn {
    left: 10px;
  }

  .next-btn {
    right: 10px;
  }
/* Universal Modal Styling */
.modal {
  display: none;
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.6);
  justify-content: center;
  align-items: flex-start; /* start at top for better scroll on small screens */
  overflow-y: auto; /* 🔥 allows scroll when content is taller than viewport */
  z-index: 9999;
  padding: 30px 15px; /* adds spacing on smaller screens */
}

/* Show modal when active */
.modal.active {
  display: flex;
}

/* Modal Content Styling */
.modal-content {
  background: #222;
  padding: 25px;
  border-radius: 10px;
  width: 90%;
  max-width: 800px;
  color: white;
  overflow-y: auto; /* 🔥 scroll inside modal if needed */
  max-height: 90vh; /* prevent overflow off screen */
  box-shadow: 0 10px 25px rgba(0,0,0,0.5);
  position: relative;
}






  </style>

  <script>
    (function(){
      try {
        var t = localStorage.getItem('adm_theme_pref');
        if (t === 'light') document.documentElement.setAttribute('data-theme','light');
        else if (t === 'dark') document.documentElement.removeAttribute('data-theme');
        else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) 
          document.documentElement.setAttribute('data-theme','light');
      } catch(e){}
    })();
  </script>
</head>

<body>
  <aside class="sidebar">
     <img src="<?= base_url('assets/img/amb_logo.png')?>" alt="AMB Logo">
    <nav class="nav">
      <a href="/admin/adminHomepage" ><i data-lucide="layout-dashboard"></i> Dashboard</a>
      <a href="/admin/manageUsers"><i data-lucide="users"></i> Manage Users</a>
      <a href="/admin/ManageProperties" class="active" style="background: linear-gradient(90deg, #428d46ff, #2376d4ff);"><i data-lucide="home"></i> Manage Properties</a>
      <!-- User Bookings removed -->
      <!-- View Chats removed for privacy -->
      <a href="/admin/Reports"><i data-lucide="bar-chart-2"></i> Generate Reports</a>
    </nav>

    <div class="profile-box">
      <div class="profile-avatar">A</div>
       <div class="profile-info">
        <strong><?= session('FirstName') . ' ' . session('LastName'); ?></strong>
        <span><?= session('inputEmail'); ?></span>
      </div>
    </div>
  </aside>

  <main class="main">
    <header>
      <div class="left-header">
        <button id="toggleSidebar" class="btn"><i data-lucide="menu"></i></button>
        <h1><i data-lucide="home"></i> Manage Properties</h1>
      </div>
      <button class="btn" id="btnAddProperty"><i data-lucide="plus-circle"></i> Add Property</button>
    </header>

    <div class="filters-bar" style="background: white; padding: 10px; border-radius: 8px;">
      <input type="text" id="searchInput" placeholder="Search all columns..." style="background: white; color: black;">
      <select id="filterStatus" style="background: white; color: black;">
        <option value="">Status</option>
        <option value="Available">Available</option>
        <option value="Reserved">Reserved</option>
        <option value="Sold">Sold</option>
      </select>
      <select id="filterType" style="background: white; color: black;">
        <option value="">Type</option>
        <option value="Apartment">Apartment</option>
        <option value="Condo">Condo</option>
        <option value="House">House</option>
      </select>
    <select id="filterAgent" style="background: white; color: black;">
      <option value="">All Agents</option>
      <?php foreach ($agents as $agent): ?>
        <option value="<?= esc($agent['full_name']); ?>"><?= esc($agent['full_name']); ?></option>
      <?php endforeach; ?>
      <option value="Unassigned">Unassigned</option>
    </select>

    </div>

    <div class="table-container">
      <table id="propertyTable">
        <thead style="background: white;">
          <tr>
            <th>Property ID</th>
            <th>Title</th>
            <th>Type</th>
            <th>Price</th>
            <th>Location</th>
            <th>Status</th>
            <th>Agent</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php
          use App\Models\UsersModel;

          $userModel = new UsersModel();

          foreach ($properties as $property):
              $getAgentName = $userModel->getNameByID($property['agent_assigned']);
          ?>
              <tr>
                  <td><?= esc($property['PropertyID']); ?></td>
                  <td><?= esc($property['Title']); ?></td>
                  <td><?= esc($property['Property_Type']); ?></td>
                  <td>₱<?= esc(number_format($property['Price'], 2)); ?></td>
                  <td><?= esc($property['Location']); ?></td>
                  <td><?= esc($property['New_Status'] ?? 'N/A'); ?></td>
                  <td><?= esc($getAgentName ?? 'Unassigned'); ?></td>
                  <td class="actions">
                      <button class="action-btn" onclick="viewProperty(<?= $property['PropertyID']; ?>)">
                          <i data-lucide="eye"></i>
                      </button>
                      <button class="action-btn" onclick="editProperty(<?= $property['PropertyID']; ?>)">
                          <i data-lucide="edit-2"></i>
                      </button>
                      <button class="btn btn-danger btn-sm" onclick="openDeleteModal(<?= $property['PropertyID'] ?>)">
                        <i class="bi bi-trash"></i>
                      </button>

                  </td>
              </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>

 <div class="modal" id="viewModal">
  <div class="modal-content">
    <h2>Property Details</h2>

    <!-- Image Carousel -->
    <div class="image-preview-container position-relative" id="viewImageGallery">
      <button type="button" class="carousel-btn prev-btn" onclick="prevImage()">&#10094;</button>
      <img id="currentImage" src="" alt="Property Image" class="img-fluid rounded shadow">
      <button type="button" class="carousel-btn next-btn" onclick="nextImage()">&#10095;</button>
    </div>

    <div class="mt-3">
      <p><strong>ID:</strong> <span id="viewID"></span></p>
      <p><strong>Title:</strong> <span id="viewTitle"></span></p>
      <p><strong>Type:</strong> <span id="viewType"></span></p>
      <p><strong>Price:</strong> <span id="viewPrice"></span></p>
      <p><strong>Location:</strong> <span id="viewLocation"></span></p>
      <p><strong>Status:</strong> <span id="viewStatus"></span></p>
      <p><strong>Agent:</strong> <span id="viewAgent"></span></p>
    </div>

    <div class="actions mt-3">
      <button class="btn cancel" onclick="closeModal('viewModal')">Close</button>
    </div>
  </div>
</div>

 <div class="modal" id="addModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content shadow-lg rounded-4 p-3 modal-animate" >
      
      <h2 id="modalTitle" class="mb-2 text-center">Add / Edit Property</h2>
            
      <form id="propertyForm" action="<?= base_url('admin/property/store-property') ?>" method="POST" enctype="multipart/form-data">

        <?php if (function_exists('csrf_field')): ?>
          <?= csrf_field() ?>
        <?php else: ?>
          <input type="hidden" name="<?= esc(csrf_token()) ?>" value="<?= esc(csrf_hash()) ?>">
        <?php endif; ?>


        <input type="hidden" id="userID" name="UserID" value="<?= esc(session()->get('UserID')); ?>">
        <input type="hidden" id="PropertyID" name="PropertyID" value="">

        <div class="row g-3">
          <!-- Title -->
          <div class="col-md-6">
            <label class="form-label ">Title</label>
            <input type="text" id="newTitle" name="Title" class="form-control" placeholder="Title" required>
          </div>

          <!-- Property Type -->
          <div class="col-md-6">
            <label class="form-label">Property Type</label>
            <select id="newType" name="Property_Type" class="form-select" required>
              <option value="">Select Type</option>
              <option value="Apartment">Apartment</option>
              <option value="Condo">Condo</option>
              <option value="House">House</option>
            </select>
          </div>

          <!-- Description -->
          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea id="newDescription" name="Description" class="form-control" placeholder="Enter property description" rows="3" required></textarea>
          </div>

          <!-- Price -->
          <div class="col-md-6">
            <label class="form-label">Price (₱)</label>
            <input type="number" id="newPrice" name="Price" class="form-control" placeholder="Price" min="0" step="0.01" required>
          </div>

          <!-- Location -->
          <div class="col-md-6">
            <label class="form-label">Location</label>
            <input type="text" id="newLocation" name="Location" class="form-control" placeholder="Location" required>
          </div>

          <!-- Size -->
          <div class="col-md-4">
            <label class="form-label">Size (sqm)</label>
            <input type="number" id="newSize" name="Size" class="form-control" placeholder="Property Size (sqm)" min="0" step="0.1">
          </div>

          <!-- Bedrooms -->
          <div class="col-md-4">
            <label class="form-label">Bedrooms</label>
            <input type="number" id="newBedrooms" name="Bedrooms" class="form-control" placeholder="No. of Bedrooms" min="0">
          </div>

          <!-- Bathrooms -->
          <div class="col-md-4">
            <label class="form-label">Bathrooms</label>
            <input type="number" id="newBathrooms" name="Bathrooms" class="form-control" placeholder="No. of Bathrooms" min="0">
          </div>

          <!-- Parking Spaces -->
          <div class="col-md-4">
            <label class="form-label">Parking Spaces</label>
            <input type="number" id="newParking" name="Parking_Spaces" class="form-control" placeholder="No. of Parking Spaces" min="0">
          </div>

          <!-- Corporation -->
          <div class="col-md-8">
            <label class="form-label">Corporation</label>
            <select id="newCorporation" name="Corporation" class="form-select" required>
              <option value="">Select Corporation</option>
              <option value="BellaVita">BellaVita</option>
              <option value="RCD">RCD</option>
            </select>
          </div>

          <!-- Assign Agent -->
          <div class="col-md-12">
            <label class="form-label">Assign Agent</label>
            <select id="newAgent" name="agent_assigned" class="form-select">
              <option value="">Select Agent</option>
              <?php foreach ($agentss as $agent): ?>
                <option value="<?= esc($agent['UserID']); ?>">
                  <?= esc($agent['FirstName'] . ' ' . $agent['LastName']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Property Images -->
          <div class="col-md-12">
            <label class="form-label">Property Images</label>
            <input type="file" id="newImages" name="images[]" class="form-control" accept="image/*" multiple>
            <div id="multiPreview" class="image-preview-container mt-2"></div>
          </div>
        </div>

        <!-- Buttons -->
        <div class="actions d-flex justify-content-end gap-2 mt-4">
          <button class="btn btn-secondary cancel" type="button" id="cancelAdd">Cancel</button>
          <button class="btn btn-primary" type="submit" id="saveProperty">Save</button>
        </div>

      </form>
    </div>
  </div>
</div>


<!-- Delete Confirmation Modal -->
<div class="modal" id="deleteModal">
  <div class="modal-content">
    <h2>Delete Property</h2>
    <p>Are you sure you want to delete this property?</p>
    <div class="modal-actions">
      <button class="btn danger" id="confirmDeleteBtn">Delete</button>
      <button class="btn cancel" onclick="closeModal('deleteModal')">Cancel</button>
    </div>
  </div>
</div>



<script>
  lucide.createIcons();

  const tableBody = document.querySelector("#propertyTable tbody");
  const addModal = document.getElementById("addModal");
  const viewModal = document.getElementById("viewModal");
  const multiPreview = document.getElementById("multiPreview");
  const newImages = document.getElementById("newImages");

  let imageFiles = [];

  const properties = <?= json_encode($properties) ?>;

let deletePropertyID = null;

function openDeleteModal(id) {
  deletePropertyID = id;
  showModal('deleteModal');
}

document.getElementById("confirmDeleteBtn").addEventListener("click", () => {
  if (!deletePropertyID) return;

  fetch("<?= base_url('admin/property/delete-property') ?>/" + deletePropertyID, {
  method: "DELETE"
})

    .then((res) => res.json())
    .then((data) => {
      alert(data.message || "Property deleted successfully!");
      hideModal('deleteModal');

      // Remove the deleted property from table
      const index = properties.findIndex(p => p.PropertyID == deletePropertyID);
      if (index !== -1) {
        properties.splice(index, 1);
        renderTable();
      }

      deletePropertyID = null;
    })
    .catch((err) => {
      console.error("Error deleting property:", err);
    
    });
});



  
  function renderTable(data = properties) {
    tableBody.innerHTML = "";
    data.forEach((p) => {
      const row = document.createElement("tr");
      row.innerHTML = `
        <td>${p.PropertyID}</td>
        <td>${p.Title}</td>
        <td>${p.Property_Type}</td>
        <td>₱${Number(p.Price).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
        <td>${p.Location}</td>
        <td>${p.New_Status ?? "N/A"}</td>
        <td>${p.agent_name ?? "Unassigned"}</td>
        <td class="actions">
          <button class="action-btn" onclick="viewProperty(${p.PropertyID})">
            <i data-lucide='eye'></i>
          </button>
          <button class="action-btn" onclick="editProperty(${p.PropertyID})">
            <i data-lucide='edit-2'></i>
          </button>
          <button class="action-btn danger" onclick="openDeleteModal(${p.PropertyID})">
            <i data-lucide='trash-2'></i>
          </button>

        </td>
      `;
      tableBody.appendChild(row);
    });
    lucide.createIcons();
  }



  const searchInput = document.getElementById("searchInput");
  const filterStatus = document.getElementById("filterStatus");
  const filterType = document.getElementById("filterType");
  const filterAgent = document.getElementById("filterAgent");

  function applyFilters() {
    const searchText = searchInput.value.toLowerCase();
    const statusVal = filterStatus.value;
    const typeVal = filterType.value;
    const agentVal = filterAgent.value;

    const filtered = properties.filter(p => {
      const matchesSearch = Object.values(p)
        .join(" ")
        .toLowerCase()
        .includes(searchText);
      const matchesStatus = !statusVal || (p.New_Status && p.New_Status === statusVal);
      const matchesType = !typeVal || (p.Property_Type && p.Property_Type === typeVal);
      const matchesAgent = !agentVal || (p.agent_name && p.agent_name === agentVal);
      return matchesSearch && matchesStatus && matchesType && matchesAgent;
    });

    renderTable(filtered);
  }


    
    const btnAddProperty = document.getElementById("btnAddProperty");
    btnAddProperty.addEventListener("click", () => {
      addModal.classList.add("active");
      imageFiles = [];
      multiPreview.innerHTML = "";
    });

 
    document.getElementById("cancelAdd").addEventListener("click", () => {
      addModal.classList.remove("active");
    });
     
    newImages.addEventListener("change", function () {
      multiPreview.innerHTML = "";
      imageFiles = Array.from(this.files);
      imageFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function (e) {
          const imgDiv = document.createElement("div");
          const img = document.createElement("img");
          img.src = e.target.result;
          const removeBtn = document.createElement("button");
          removeBtn.textContent = "×";
          removeBtn.classList.add("remove-img");
          removeBtn.onclick = function () {
            imageFiles.splice(index, 1);
            newImages.value = "";
            imgDiv.remove();
          };
          imgDiv.appendChild(img);
          imgDiv.appendChild(removeBtn);
          multiPreview.appendChild(imgDiv);
        };
        reader.readAsDataURL(file);
      });
    });
    
  searchInput.addEventListener("input", applyFilters);
  filterStatus.addEventListener("change", applyFilters);
  filterType.addEventListener("change", applyFilters);
  filterAgent.addEventListener("change", applyFilters);

  renderTable();
</script>

<script>
// Modal helper animations and functions
function showModal(id) {
  const modal = document.getElementById(id);
  if (!modal) return;
  modal.classList.add('active');
  const content = modal.querySelector('.modal-content');
  if (content) {
    content.style.transform = 'translateY(-10px) scale(0.98)';
    content.style.opacity = '0';
    requestAnimationFrame(() => {
      content.style.transition = 'transform 260ms ease, opacity 200ms ease';
      content.style.transform = 'translateY(0) scale(1)';
      content.style.opacity = '1';
    });
  }
}

function hideModal(id) {
  const modal = document.getElementById(id);
  if (!modal) return;
  const content = modal.querySelector('.modal-content');
  if (content) {
    content.style.transform = 'translateY(-10px) scale(0.98)';
    content.style.opacity = '0';
    setTimeout(() => modal.classList.remove('active'), 220);
  } else {
    modal.classList.remove('active');
  }
}

function closeModal(id) { hideModal(id); }

// Edit property: fetch data and populate addModal form
function editProperty(propertyID) {
  fetch(`/admin/getProperty/${propertyID}`)
    .then(res => res.json())
    .then(data => {
      if (!data) return console.error('No data received');
      // Populate form fields
      const fieldsToPopulate = ['newTitle','newType','newDescription','newPrice','newLocation','newSize','newBedrooms','newBathrooms','newParking','newCorporation','newAgent'];
      const mapping = {
        newTitle: data.Title || '',
        newType: data.Property_Type || '',
        newDescription: data.Description || '',
        newPrice: data.Price || '',
        newLocation: data.Location || '',
        newSize: data.Size || '',
        newBedrooms: data.Bedrooms || '',
        newBathrooms: data.Bathrooms || '',
        newParking: data.Parking_Spaces || '',
        newCorporation: data.Corporation || '',
        newAgent: data.agent_assigned || ''
      };

      fieldsToPopulate.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        el.value = mapping[id] || '';
        // make text black when editing so it's visible
        el.style.color = '#000';
      });
      // set hidden PropertyID so server knows to update
      document.getElementById('PropertyID').value = propertyID;
      // open modal
      // Open modal
      showModal('addModal');
    })
    .catch(err => console.error('Error fetching property for edit:', err));
}

// Ensure cancel buttons close modals
  document.getElementById('cancelAdd').addEventListener('click', () => {
    // reset PropertyID and input text colors when canceling
    const propIdEl = document.getElementById('PropertyID');
    if (propIdEl) propIdEl.value = '';
    ['newTitle','newType','newDescription','newPrice','newLocation','newSize','newBedrooms','newBathrooms','newParking','newCorporation','newAgent'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.style.color = '';
    });
    hideModal('addModal');
  });

// viewProperty should open view modal with animation (ensure it uses showModal)
const originalViewProperty = window.viewProperty;
window.viewProperty = function(propID) {
  // call existing implementation (which fetches and opens modal)
  originalViewProperty(propID);
  // ensure animation
  setTimeout(() => showModal('viewModal'), 80);
}

</script>

<script>
let currentImages = [];
let currentImageIndex = 0;

function viewProperty(propertyID) {
  fetch(`/admin/getProperty/${propertyID}`)
    .then(res => res.json())
    .then(data => {
      if (!data) return console.error('No data received');

      // Fill details
      document.getElementById('viewID').textContent = data.PropertyID;
      document.getElementById('viewTitle').textContent = data.Title;
      document.getElementById('viewType').textContent = data.Property_Type;
      document.getElementById('viewPrice').textContent = '₱' + parseFloat(data.Price).toLocaleString();
      document.getElementById('viewLocation').textContent = data.Location;
      document.getElementById('viewStatus').textContent = data.New_Status || 'N/A';
      document.getElementById('viewAgent').textContent = data.AgentName || 'Unassigned';

      // Images
      currentImages = Array.isArray(data.images) ? data.images : [];
      currentImageIndex = 0;
      updateImageDisplay();

      document.getElementById('viewModal').classList.add('active');
    })
    .catch(err => console.error('Error fetching property:', err));
}

function updateImageDisplay() {
  const imgEl = document.getElementById('currentImage');
  
  imgEl.src = currentImages.length ? currentImages[currentImageIndex] : '/uploads/properties/no-image.jpg';
}

function nextImage() {
  if (!currentImages.length) return;
  currentImageIndex = (currentImageIndex + 1) % currentImages.length;
  updateImageDisplay();
}

function prevImage() {
  if (!currentImages.length) return;
  currentImageIndex = (currentImageIndex - 1 + currentImages.length) % currentImages.length;
  updateImageDisplay();
}

function closeModal(modalId) {
  // Delegate to animated hide helper for consistent behavior
  try { hideModal(modalId); } catch(e) { document.getElementById(modalId).classList.remove('active'); }
}







</script>


<script src="<?= base_url('bootstrap5/js/bootstrap.min.js')?>"> </script>

</body>
</html>
