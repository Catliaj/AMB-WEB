<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Manage Users | Admin Dashboard</title>

  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <link rel="stylesheet" href="<?= base_url('assets/styles/admin-style.css')?>">

  <style>
    body {
      font-family: 'Montserrat', sans-serif;
      margin: 0;
      display: flex;
      background: var(--bg);
      color: var(--text);
    }

    .actions {
      display: flex;
      gap: 8px;
      justify-content: center;
    }

    .icon-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 30px;
      height: 30px;
      border-radius: 8px;
      border: none;
      background: var(--card);
      color: var(--text);
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .icon-btn:hover {
      background: var(--hover-overlay);
      transform: scale(1.05);

    }

    .icon-btn i svg {
  width: 36px !important;       /* Force icon size */
  height: 36px !important;
  stroke-width: 3.0 !important; /* Thicker stroke for clarity */
}
    

  



    .filters {
      margin: 20px 0;
      display: flex;
      gap: 10px;
      align-items: center;
    }

    .filters input, 
    .filters select {
      padding: 8px 12px;
      border-radius: 6px;
      border: 1px solid var(--divider);
      background: var(--card);
      color: var(--text);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: var(--card);
      border-radius: 10px;
      overflow: hidden;
    }

    th, td {
      padding: 12px 10px;
      text-align: left;
      border-bottom: 1px solid var(--divider);
    }

    th {
      background: var(--hover-overlay);
      font-weight: 600;
    }

    .layout {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 20px;
      align-items: start;
    }

    .right-panel {
      background: var(--card);
      border-radius: 10px;
      padding: 20px;
      text-align: center;
    }

    .profile-pic img {
      width: 100px;
      border-radius: 50%;
      margin-bottom: 10px;
    }

    .toggle {
      cursor: pointer;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 13px;
      color: white;
      background: #f87171;
      transition: 0.2s;
    }

    .toggle.active {
      background: #34d399;
    }

    #userTable th, #userTable td {
      padding: 9.6px 8px;
      font-size: 10.4px;
    }
  </style>

  <script>
    (function(){
      try {
        var t = localStorage.getItem('adm_theme_pref');
        if (t === 'light') document.documentElement.setAttribute('data-theme','light');
        else if (t === 'dark') document.documentElement.removeAttribute('data-theme');
        else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) document.documentElement.setAttribute('data-theme','light');
      } catch(e){}
    })();
  </script>
</head>

<body>
    <aside class="sidebar">
     <img src="<?= base_url('assets/img/amb_logo.png')?>" alt="AMB Logo">
    <nav class="nav">
      <a href="/admin/adminHomepage" ><i data-lucide="layout-dashboard"></i> Dashboard</a>
      <a href="/admin/manageUsers" class="active"><i data-lucide="users"></i> Manage Users</a>
      <a href="/admin/ManageProperties"><i data-lucide="home"></i> Manage Properties</a>
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
  <section class="main" style="padding:20px;width:100%;">
    <header style="display:flex;justify-content:space-between;align-items:center;">
      <h1><i data-lucide="users"></i> Manage Users</h1>
      <button class="btn primary" id="addUserBtn" style="padding:8px 14px;border:none;border-radius:8px;background:#2563eb;color:white;cursor:pointer;">
        <i data-lucide="user-plus"></i> Add Agent
      </button>
    </header>

    <div class="filters">
      <input type="text" id="searchInput" placeholder="Search users...">
      <select id="roleFilter">
        <option value="">All Roles</option>
        <option>Admin</option>
        <option>Agent</option>
        <option>Client</option>
      </select>
    </div>

    <div class="layout">
      <div>
        <table id="userTable">
          <thead>
            <tr>
              <th>UserID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $user): ?>
              <tr>

                <td><?= esc($user['UserID']) ?></td>
                <td><?= esc($user['FirstName'] . ' ' . $user['LastName']) ?></td>
                <td><?= esc($user['Email']) ?></td>
                <td><?= esc($user['Role']) ?></td>

                <td>
                  <span class="toggle <?= $user['status'] === 'Online' ? 'active' : '' ?>">
                    <?= esc($user['status']) ?>
                  </span>
                </td>

                <td class="actions">
                  <button class="icon-btn" title="Edit"><i data-lucide="edit-3"></i></button>
                  <button class="icon-btn danger" title="Delete"><i data-lucide="trash-2"></i></button>
                  <button class="icon-btn" title="View"><i data-lucide="eye"></i></button>
                </td>

              </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <aside class="right-panel" id="userDetails">
        <div class="profile-pic"><img src="https://via.placeholder.com/100" alt="Profile"></div>
        <h3>Select a user to view details</h3>
      </aside>
    </div>
  </section>


<div class="modal fade" id="addAgentModal" tabindex="-1" aria-labelledby="addAgentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-3 bg-dark">
      
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="addAgentModalLabel">
          <i data-lucide="user-plus"></i> Add New Agent
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form action="<?= base_url('/admin/store-agent') ?>" method="post">
        <div class="modal-body">
          <div class="mb-3">
            <label for="FirstName" class="form-label">First Name</label>
            <input type="text" class="form-control" name="FirstName" id="FirstName" required>
          </div>

          <div class="mb-3">
            <label for="LastName" class="form-label">Middle Name</label>
            <input type="text" class="form-control" name="MiddleName" id="LastName" required>
          </div>

          <div class="mb-3">
            <label for="LastName" class="form-label">Last Name</label>
            <input type="text" class="form-control" name="LastName" id="LastName" required>
          </div>

          <div class="mb-3">
            <label for="LastName" class="form-label">Birthdate</label>
            <input type="date" class="form-control" name="Birthdate" id="Birthdate" required>
          </div>

          <div class="mb-3">
            <label for="LastName" class="form-label">Phone Number</label>
            <input type="number" class="form-control" name="PhoneNumber" id="PhoneNumber" required>
          </div>

          <div class="mb-3">
            <label for="Email" class="form-label">Email</label>
            <input type="email" class="form-control" name="Email" id="Email" required>
          </div>

          <div class="mb-3">
            <label for="Password" class="form-label">Password</label>
            <input type="password" class="form-control" name="Password" id="Password" required>
          </div>

          <input type="hidden" name="Role" value="Agent">
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Agent</button>
        </div>
      </form>

    </div>
  </div>
</div>


  <script src="https://unpkg.com/lucide@latest"></script>
<!-- Bootstrap JS (make sure this is included before your script) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
  lucide.createIcons({ 
    attrs: { width: 22, height: 22, strokeWidth: 2 } 
  });

  // Get elements
  const addBtn = document.getElementById('addUserBtn');
  const userTable = document.querySelector('#userTable tbody');
  const userDetails = document.getElementById('userDetails');
  const searchInput = document.getElementById('searchInput');
  const roleFilter = document.getElementById('roleFilter');

  // Initialize Bootstrap modal
  const addAgentModal = new bootstrap.Modal(document.getElementById('addAgentModal'));

  const users = <?= json_encode($users) ?>;
  let editingIndex = null;

  // Function to render user table
  function renderTable(search = '', role = '') {
    userTable.innerHTML = '';
    search = search.toLowerCase();

    const filtered = users.filter(u => {
      const fullName = (u.FirstName + ' ' + u.LastName).toLowerCase();
      const matchesSearch = fullName.includes(search) || u.Email.toLowerCase().includes(search);
      const matchesRole = !role || u.Role === role;
      return matchesSearch && matchesRole;
    });

    filtered.forEach((u, i) => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${u.UserID}</td>
        <td>${u.FirstName} ${u.LastName}</td>
        <td>${u.Email}</td>
        <td>${u.Role}</td>
        <td>
          <span class="toggle ${u.status === 'Online' ? 'active' : ''}" data-index="${i}">
            ${u.status}
          </span>
        </td>
        <td class="actions">
          <button class="icon-btn" data-edit="${i}" title="Edit"><i data-lucide="edit-3"></i></button>
          <button class="icon-btn danger" data-del="${i}" title="Delete"><i data-lucide="trash-2"></i></button>
          <button class="icon-btn" data-view="${i}" title="View"><i data-lucide="eye"></i></button>
        </td>
      `;
      userTable.appendChild(tr);
    });

    lucide.createIcons();
  }

  // Add Agent button (opens Bootstrap modal)
  addBtn.onclick = () => {
    addAgentModal.show();
  };

  // Table interactions
  userTable.addEventListener('click', e => {
    const btn = e.target.closest('button');
    const toggle = e.target.closest('.toggle');
    if (!btn && !toggle) return;

    // Toggle online/offline
    if (toggle) {
      const i = toggle.dataset.index;
      users[i].status = users[i].status === 'Online' ? 'Offline' : 'Online';
      renderTable(searchInput.value, roleFilter.value);
    }

    // Delete user
    if (btn?.dataset.del !== undefined) {
      if (confirm('Delete this user?')) {
        users.splice(btn.dataset.del, 1);
        renderTable(searchInput.value, roleFilter.value);
      }
    }

    // Edit user (for now opens modal prefilled — optional to connect to PHP later)
    if (btn?.dataset.edit !== undefined) {
      editingIndex = btn.dataset.edit;
      const u = users[editingIndex];
      document.getElementById('FirstName').value = u.FirstName;
      document.getElementById('LastName').value = u.LastName;
      document.getElementById('Email').value = u.Email;
      document.getElementById('Role').value = u.Role;
      addAgentModal.show();
    }

    // View user details
    if (btn?.dataset.view !== undefined) {
      const u = users[btn.dataset.view];
      userDetails.innerHTML = `
        <div class="profile-pic">
          <img src="https://via.placeholder.com/100" alt="Profile">
        </div>
        <h3>${u.FirstName} ${u.LastName}</h3>
        <p style="color:var(--muted);">${u.Role} • ${u.Email}</p>
        <p>Status: <strong>${u.status}</strong></p>`;
    }
  });

  // Filter search and roles
  searchInput.addEventListener('input', () => renderTable(searchInput.value, roleFilter.value));
  roleFilter.addEventListener('change', () => renderTable(searchInput.value, roleFilter.value));

  renderTable();
});
</script>




</body>
</html>
