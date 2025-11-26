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
      width: 38px;
      height: 38px;
      border-radius: 10px;
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
  width: 28px !important;       /* Force icon size */
  height: 28px !important;
  stroke-width: 2.2 !important; /* Thicker stroke for clarity */
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
            <label for="MiddleName" class="form-label">Middle Name</label>
            <input type="text" class="form-control" name="MiddleName" id="MiddleName">
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
          <input type="hidden" id="editingUserId" name="UserID" value="">
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" id="reactivateBtn" class="btn btn-success" style="display:none;margin-right:auto;">Reactivate</button>
          <button type="submit" class="btn btn-primary">Add Agent</button>
        </div>
      </form>

    </div>
  </div>
</div>

    <!-- Choose Action Modal -->
    <div class="modal fade" id="chooseActionModal" tabindex="-1" aria-labelledby="chooseActionLabel" aria-hidden="true">
      <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content modal-dark">
          <div class="modal-header">
            <h5 class="modal-title" id="chooseActionLabel">Choose an action</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center">
            <p>Select what you want to do with this user:</p>
            <div class="d-flex gap-2 justify-content-center">
              <button id="actionDeactivate" class="btn btn-warning">Deactivate User</button>
              <button id="actionDelete" class="btn btn-danger">Delete User</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirm Action Modal -->
    <div class="modal fade" id="confirmActionModal" tabindex="-1" aria-labelledby="confirmActionLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-dark">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmActionLabel">Confirm</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p id="confirmActionText"></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" id="confirmActionBtn" class="btn btn-primary">Confirm</button>
          </div>
        </div>
      </div>
    </div>

          <!-- View User Modal -->
          <div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
              <div class="modal-content modal-dark">
                <div class="modal-header">
                  <h5 class="modal-title" id="viewUserLabel">User Details</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="row">
                    <div class="col-md-6">
                      <dl class="row">
                        <dt class="col-sm-4">User ID</dt>
                        <dd class="col-sm-8" id="vu_userid"></dd>

                        <dt class="col-sm-4">First Name</dt>
                        <dd class="col-sm-8" id="vu_first"></dd>

                        <dt class="col-sm-4">Middle Name</dt>
                        <dd class="col-sm-8" id="vu_middle"></dd>

                        <dt class="col-sm-4">Last Name</dt>
                        <dd class="col-sm-8" id="vu_last"></dd>

                        <dt class="col-sm-4">Birthdate</dt>
                        <dd class="col-sm-8" id="vu_bday"></dd>
                      </dl>
                    </div>
                    <div class="col-md-6">
                      <dl class="row">
                        <dt class="col-sm-4">Role</dt>
                        <dd class="col-sm-8" id="vu_role"></dd>

                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8" id="vu_status"></dd>

                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8" id="vu_email"></dd>

                        <dt class="col-sm-4">Phone</dt>
                        <dd class="col-sm-8" id="vu_phone"></dd>
                      </dl>
                    </div>
                  </div>
                  <hr>
                  <div class="d-flex justify-content-between align-items-center">
                    <h6>Submitted Documents</h6>
                    <button id="vu_viewDocs" class="btn btn-outline-primary btn-sm">View Documents</button>
                  </div>
                  <div id="vu_docs_list" class="mt-3"></div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>

          <!-- View Documents Modal -->
          <div class="modal fade" id="viewDocumentsModal" tabindex="-1" aria-labelledby="viewDocumentsLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="viewDocumentsLabel">Documents</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="docsModalBody">
                  <!-- documents will be injected here -->
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>


  <script src="https://unpkg.com/lucide@latest"></script>
<!-- Bootstrap JS (make sure this is included before your script) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
  lucide.createIcons({ 
    attrs: { width: 22, height: 22, strokeWidth: 2 } 
  });

  const BASE_URL = '<?= base_url() ?>';

  // Get elements
  const addBtn = document.getElementById('addUserBtn');
  const userTable = document.querySelector('#userTable tbody');
  const userDetails = document.getElementById('userDetails');
  const searchInput = document.getElementById('searchInput');
  const roleFilter = document.getElementById('roleFilter');

  // Initialize Bootstrap modal (single instance)
  const addAgentModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('addAgentModal'));

  window.users = <?= json_encode($users) ?>;
  const users = window.users;
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
          ${u.status === 'Deactivated' ?
            `<button class="icon-btn success" data-reactivate="${i}" title="Reactivate"><i data-lucide="refresh-ccw"></i></button>` :
            `<button class="icon-btn danger" data-del="${i}" title="Delete"><i data-lucide="trash-2"></i></button>`
          }
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

    // Delete user -> open choose-action modal
    if (btn?.dataset.del !== undefined) {
      const modalEl = document.getElementById('chooseActionModal');
      modalEl.dataset.selectedIndex = btn.dataset.del;
      modalEl.dataset.selectedId = users[btn.dataset.del].UserID;
      bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }

    // Reactivate quick button in table (for deactivated users)
    if (btn?.dataset.reactivate !== undefined) {
      const idx = Number(btn.dataset.reactivate);
      const userId = users[idx].UserID;
      Swal.fire({
        title: 'Reactivate user?',
        text: 'This will restore the user to active status.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Reactivate',
        cancelButtonText: 'Cancel'
      }).then(async (result) => {
        if (!result.isConfirmed) return;
        try {
          const res = await fetch(`${BASE_URL}/admin/user/reactivate/${userId}`, { method: 'POST' });
          const j = await res.json();
          if (res.ok && j.status === 'success') {
            users[idx].status = 'Offline';
            Swal.fire({ icon: 'success', title: 'Reactivated', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500 });
            const ev = new Event('input'); document.getElementById('searchInput').dispatchEvent(ev);
          } else {
            Swal.fire({ icon: 'error', title: 'Error', text: j.message || 'Failed to reactivate' });
          }
        } catch (err) {
          console.error(err);
          Swal.fire({ icon: 'error', title: 'Error', text: 'Network error' });
        }
      });
    }

    // Edit user: populate modal and mark as editing
    if (btn?.dataset.edit !== undefined) {
      const idx = btn.dataset.edit;
      const u = users[idx];
      const modalEl = document.getElementById('addAgentModal');
      modalEl.dataset.selectedIndex = idx;
      modalEl.dataset.selectedId = u.UserID;
      // populate fields
      document.getElementById('FirstName').value = u.FirstName || '';
      document.getElementById('MiddleName').value = u.MiddleName || '';
      document.getElementById('LastName').value = u.LastName || '';
      document.getElementById('Birthdate').value = u.Birthdate || '';
      document.getElementById('PhoneNumber').value = u.PhoneNumber || '';
      document.getElementById('Email').value = u.Email || '';
      document.getElementById('Password').value = '';
      // UI hints
      document.getElementById('addAgentModalLabel').textContent = 'Edit User';
      document.querySelector('#addAgentModal .btn-primary').textContent = 'Save Changes';
      // show reactivate button if user is deactivated
      const reactivateBtn = document.getElementById('reactivateBtn');
      if (u.status === 'Deactivated') {
        reactivateBtn.style.display = 'inline-block';
      } else {
        reactivateBtn.style.display = 'none';
      }
      addAgentModal.show();
    }

    // View user details -> open view modal
    if (btn?.dataset.view !== undefined) {
      const idx = Number(btn.dataset.view);
      const u = users[idx];
      // Populate modal fields
      document.getElementById('vu_userid').textContent = u.UserID || '';
      document.getElementById('vu_first').textContent = u.FirstName || '';
      document.getElementById('vu_middle').textContent = u.MiddleName || '';
      document.getElementById('vu_last').textContent = u.LastName || '';
      document.getElementById('vu_bday').textContent = u.Birthdate || '';
      document.getElementById('vu_role').textContent = u.Role || '';
      document.getElementById('vu_status').textContent = u.status || '';
      document.getElementById('vu_email').textContent = u.Email || '';
      document.getElementById('vu_phone').textContent = u.PhoneNumber || '';

      // Populate documents list (inline) and prepare view documents modal
      const docsList = document.getElementById('vu_docs_list');
      docsList.innerHTML = '';
      const docs = u.documents || u.docs || null;
      if (docs && Array.isArray(docs) && docs.length) {
        const ul = document.createElement('ul');
        ul.className = 'list-group';
        docs.forEach(d => {
          const li = document.createElement('li');
          li.className = 'list-group-item d-flex justify-content-between align-items-center';
          const name = d.name || d.filename || (typeof d === 'string' ? d : 'Document');
          li.innerHTML = `<span>${name}</span><a href="${d.url || d.file || '#'}" target="_blank" class="btn btn-sm btn-outline-primary">Open</a>`;
          ul.appendChild(li);
        });
        docsList.appendChild(ul);
      } else {
        docsList.innerHTML = '<p class="text-muted">No documents uploaded</p>';
      }

      // Wire View Documents button
      const vdBtn = document.getElementById('vu_viewDocs');
      vdBtn.onclick = () => {
        const docsModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('viewDocumentsModal'));
        const body = document.getElementById('docsModalBody');
        if (docs && Array.isArray(docs) && docs.length) {
          body.innerHTML = '';
          docs.forEach(d => {
            const row = document.createElement('div');
            row.className = 'd-flex justify-content-between align-items-center mb-2';
            const name = d.name || d.filename || (typeof d === 'string' ? d : 'Document');
            const href = d.url || d.file || '#';
            row.innerHTML = `<div>${name}</div><div><a href="${href}" target="_blank" class="btn btn-sm btn-primary me-2">Open</a></div>`;
            body.appendChild(row);
          });
        } else {
          body.innerHTML = '<p class="text-muted">No documents available for this user.</p>';
        }
        docsModal.show();
      };

      // Show the view modal
      bootstrap.Modal.getOrCreateInstance(document.getElementById('viewUserModal')).show();
    }
  });

  // Filter search and roles
  searchInput.addEventListener('input', () => renderTable(searchInput.value, roleFilter.value));
  roleFilter.addEventListener('change', () => renderTable(searchInput.value, roleFilter.value));

  renderTable();
});
</script>

<script>
// Action modal logic
document.addEventListener('DOMContentLoaded', () => {
  const BASE_URL = '<?= base_url() ?>';
  const chooseActionEl = document.getElementById('chooseActionModal');
  const confirmActionEl = document.getElementById('confirmActionModal');
  const chooseActionModal = bootstrap.Modal.getOrCreateInstance(chooseActionEl);
  const confirmActionModal = bootstrap.Modal.getOrCreateInstance(confirmActionEl);

  let selectedUserIndex = null;
  let selectedUserId = null;
  let selectedAction = null; // 'deactivate' | 'delete'

  // When the choose-action modal is shown, read the selected user info from dataset
  chooseActionEl.addEventListener('show.bs.modal', () => {
    selectedUserIndex = chooseActionEl.dataset.selectedIndex ? Number(chooseActionEl.dataset.selectedIndex) : null;
    selectedUserId = chooseActionEl.dataset.selectedId || null;
  });

  const btnDeactivate = document.getElementById('actionDeactivate');
  const btnDelete = document.getElementById('actionDelete');
  const confirmText = document.getElementById('confirmActionText');
  const confirmBtn = document.getElementById('confirmActionBtn');

  btnDeactivate.addEventListener('click', () => {
    selectedAction = 'deactivate';
    confirmText.textContent = 'Are you sure you want to deactivate this user? This will set their status to Deactivated.';
    chooseActionModal.hide();
    confirmActionModal.show();
  });

  btnDelete.addEventListener('click', () => {
    selectedAction = 'delete';
    confirmText.textContent = 'Are you sure you want to permanently delete this user? This action cannot be undone.';
    chooseActionModal.hide();
    confirmActionModal.show();
  });

  confirmBtn.addEventListener('click', async () => {
    if (selectedUserIndex === null || !selectedUserId || !selectedAction) return;

    try {
      if (selectedAction === 'deactivate') {
        const res = await fetch(`${BASE_URL}/admin/user/deactivate/${selectedUserId}`, { method: 'POST' });
        const j = await res.json();
        if (res.ok && j.status === 'success') {
          // update local array if present
          if (window.users && window.users[selectedUserIndex]) {
            window.users[selectedUserIndex].status = 'Deactivated';
          }
          // If this view has a users variable in the local scope, update it too
          if (typeof users !== 'undefined' && users[selectedUserIndex]) {
            users[selectedUserIndex].status = 'Deactivated';
            // re-render table
            const ev = new Event('input');
            document.getElementById('searchInput').dispatchEvent(ev);
          }
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: j.message || 'Failed to deactivate user' });
        }
      } else if (selectedAction === 'delete') {
        const res = await fetch(`${BASE_URL}/admin/user/delete/${selectedUserId}`, { method: 'DELETE' });
        const j = await res.json();
        if (res.ok && j.status === 'success') {
          if (typeof users !== 'undefined') {
            users.splice(selectedUserIndex, 1);
            const ev = new Event('input');
            document.getElementById('searchInput').dispatchEvent(ev);
          }
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: j.message || 'Failed to delete user' });
        }
      }
    } catch (err) {
      console.error(err);
      Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred. Check console for details.' });
    } finally {
      selectedUserIndex = null;
      selectedUserId = null;
      selectedAction = null;
      confirmActionModal.hide();
      // show success toast for actions (handled below)
      if (typeof Swal !== 'undefined') {
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Action completed', showConfirmButton: false, timer: 2000 });
      }
    }
  });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const BASE_URL = '<?= base_url() ?>';
  const modalEl = document.getElementById('addAgentModal');
  const reactivateBtn = document.getElementById('reactivateBtn');
  const addAgentModal = bootstrap.Modal.getOrCreateInstance(modalEl);
  const form = modalEl.querySelector('form');

  // When showing the modal, decide if it's edit mode
  modalEl.addEventListener('show.bs.modal', () => {
    const selectedId = modalEl.dataset.selectedId || null;
    const selectedIndex = modalEl.dataset.selectedIndex;
    if (!selectedId) {
      // new agent -> clear fields
      form.reset();
      document.getElementById('addAgentModalLabel').textContent = 'Add New Agent';
      document.querySelector('#addAgentModal .btn-primary').textContent = 'Add Agent';
      reactivateBtn.style.display = 'none';
    } else {
      // editing -> populate editingUserId hidden
      document.getElementById('editingUserId').value = selectedId;
    }
  });

  // Reactivate handler
  reactivateBtn.addEventListener('click', async () => {
    const selectedId = modalEl.dataset.selectedId;
    const selectedIndex = Number(modalEl.dataset.selectedIndex);
    if (!selectedId) return;
    try {
      const res = await fetch(`${BASE_URL}/admin/user/reactivate/${selectedId}`, { method: 'POST' });
      const j = await res.json();
      if (res.ok && j.status === 'success') {
        if (window.users && window.users[selectedIndex]) {
          window.users[selectedIndex].status = 'Offline';
        }
        Swal.fire({ icon: 'success', title: 'Reactivated', text: j.message || 'User reactivated' });
        addAgentModal.hide();
        const ev = new Event('input'); document.getElementById('searchInput').dispatchEvent(ev);
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: j.message || 'Failed to reactivate user' });
      }
    } catch (err) {
      console.error(err);
      Swal.fire({ icon: 'error', title: 'Error', text: 'Network error' });
    }
  });

  // Intercept form submit only for edit mode (update)
  form.addEventListener('submit', async (e) => {
    const selectedId = modalEl.dataset.selectedId || null;
    if (!selectedId) return; // allow normal submit for new agent
    e.preventDefault();
    const fd = new FormData(form);
    try {
      const res = await fetch(`${BASE_URL}/admin/user/update/${selectedId}`, { method: 'POST', body: fd });
      const j = await res.json();
      if (res.ok && j.status === 'success') {
        // update local user data
        const idx = Number(modalEl.dataset.selectedIndex);
        if (window.users && window.users[idx]) {
          window.users[idx].FirstName = fd.get('FirstName');
          window.users[idx].MiddleName = fd.get('MiddleName');
          window.users[idx].LastName = fd.get('LastName');
          window.users[idx].Birthdate = fd.get('Birthdate');
          window.users[idx].PhoneNumber = fd.get('PhoneNumber');
          window.users[idx].Email = fd.get('Email');
        }
        Swal.fire({ icon: 'success', title: 'Saved', text: j.message || 'User updated' });
        addAgentModal.hide();
        const ev = new Event('input'); document.getElementById('searchInput').dispatchEvent(ev);
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: j.message || 'Failed to update user' });
      }
    } catch (err) {
      console.error(err);
      Swal.fire({ icon: 'error', title: 'Error', text: 'Network error' });
    }
  });
});
</script>




</body>
</html>
