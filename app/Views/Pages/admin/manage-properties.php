<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Properties | Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="<?= base_url('assets/styles/admin-style.css')?>">

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
    <img src="amb_logo.png" alt="AMB Logo">
    <nav class="nav">
      <a href="/admin/adminHomepage" ><i data-lucide="layout-dashboard"></i> Dashboard</a>
      <a href="/admin/manageUsers"><i data-lucide="users"></i> Manage Users</a>
      <a href="/admin/ManageProperties" class="active"><i data-lucide="home"></i> Manage Properties</a>
      <a href="/admin/userBookings"><i data-lucide="calendar"></i> User Bookings</a>
      <a href="/admin/viewChats"><i data-lucide="message-circle"></i> View Chats</a>
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

    <div class="filters-bar">
      <input type="text" id="searchInput" placeholder="Search all columns...">
      <select id="filterStatus">
        <option value="">Status</option>
        <option value="Available">Available</option>
        <option value="Reserved">Reserved</option>
        <option value="Sold">Sold</option>
      </select>
      <select id="filterType">
        <option value="">Type</option>
        <option value="Apartment">Apartment</option>
        <option value="Condo">Condo</option>
        <option value="House">House</option>
      </select>
    <select id="filterAgent">
  <option value="">All Agents</option>
  <?php foreach ($agents as $agent): ?>
    <option value="<?= esc($agent['full_name']); ?>"><?= esc($agent['full_name']); ?></option>
  <?php endforeach; ?>
  <option value="Unassigned">Unassigned</option>
</select>

    </div>

    <div class="table-container">
      <table id="propertyTable">
        <thead>
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
          <!-- Dynamic rows will be inserted here -->
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
            <button class="action-btn danger" onclick="openDeleteModal(<?= $property['PropertyID']; ?>)">
                <i data-lucide="trash-2"></i>
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
      <div id="viewImageGallery" class="image-preview-container"></div>
      <p><strong>ID:</strong> <span id="viewID"></span></p>
      <p><strong>Title:</strong> <span id="viewTitle"></span></p>
      <p><strong>Type:</strong> <span id="viewType"></span></p>
      <p><strong>Price:</strong> <span id="viewPrice"></span></p>
      <p><strong>Location:</strong> <span id="viewLocation"></span></p>
      <p><strong>Status:</strong> <span id="viewStatus"></span></p>
      <p><strong>Agent:</strong> <span id="viewAgent"></span></p>
      <button class="btn cancel" onclick="closeModal('viewModal')">Close</button>
    </div>
  </div>

  <div class="modal" id="addModal">
    <div class="modal-content">
      <h2 id="modalTitle">Add New Property</h2>
      <input type="text" id="newTitle" placeholder="Title" required>
      <select id="newType">
        <option value="">Select Type</option>
        <option value="Apartment">Apartment</option>
        <option value="Condo">Condo</option>
        <option value="House">House</option>
      </select>
      <input type="text" id="newPrice" placeholder="Price (₱)" required>
      <input type="text" id="newLocation" placeholder="Location" required>

      <label for="newImages" class="img-label">Property Images</label>
      <input type="file" id="newImages" accept="image/*" multiple>
      <div class="image-preview-container" id="multiPreview"></div>

      <select id="newStatus">
        <option value="Available">Available</option>
        <option value="Reserved">Reserved</option>
        <option value="Sold">Sold</option>
      </select>
      <select id="newAgent">
        <option value="Agent A">Agent A</option>
        <option value="Agent B">Agent B</option>
      </select>

      <div class="actions">
        <button class="btn cancel" id="cancelAdd">Cancel</button>
        <button class="btn" id="saveProperty">Save</button>
      </div>
    </div>
  </div>

  <div class="modal" id="deleteModal">
    <div class="modal-content">
      <h2><i data-lucide="trash-2"></i> Confirm Deletion</h2>
      <div align="center" style="color:var(--muted);">
        Are you sure you want to delete this item? <br>This action cannot be undone.
      </div>
      <div class="modal-actions">
        <button class="btn" id="cancelDelete"><i data-lucide="x"></i> Cancel</button>
        <button class="btn danger" id="confirmDelete"><i data-lucide="trash-2"></i> Delete</button>
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

  // 🟢 Embed PHP properties into JavaScript as JSON
  const properties = <?= json_encode($properties) ?>;

  // 🟢 Utility to render table rows dynamically
  
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

    

  // 🟢 View Property Modal
  window.viewProperty = function (id) {
    const p = properties.find(prop => prop.PropertyID == id);
    if (!p) return;

    document.getElementById("viewID").textContent = p.PropertyID;
    document.getElementById("viewTitle").textContent = p.Title;
    document.getElementById("viewType").textContent = p.Property_Type;
    document.getElementById("viewPrice").textContent = "₱" + Number(p.Price).toLocaleString();
    document.getElementById("viewLocation").textContent = p.Location;
    document.getElementById("viewStatus").textContent = p.New_Status ?? "N/A";
    document.getElementById("viewAgent").textContent = p.agent_name ?? "Unassigned";

    viewModal.classList.add("active");
  };

  function closeModal(id) {
    document.getElementById(id).classList.remove("active");
  }

  // 🟢 Filters
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

  searchInput.addEventListener("input", applyFilters);
  filterStatus.addEventListener("change", applyFilters);
  filterType.addEventListener("change", applyFilters);
  filterAgent.addEventListener("change", applyFilters);

  // 🟢 Initialize
  renderTable();
</script>


</body>
</html>
