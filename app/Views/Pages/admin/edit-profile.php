<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Edit Profile | Admin Dashboard</title>

  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

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

    .icon-btn.danger {
      color: #f87171;
    }

    .icon-btn.view {
      background-color: green;
      color: white;
    }

    .icon-btn.edit {
      background-color: blue;
      color: white;
    }

    .icon-btn svg {
      width: 20px;
      height: 20px;
    }

    /* Use Bootstrap's modal styles — avoid overriding global .modal rules */
    .modal-content {
      background: var(--card);
      padding: 20px;
      border-radius: 12px;
      display: flex;
      flex-direction: column;
      gap: 10px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }

    .modal-actions {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 10px;
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
    <aside class="sidebar" style="display:flex;flex-direction:column;justify-content:space-between;">
     <img src="<?= base_url('assets/img/amb_logo.png')?>" alt="AMB Logo">
    <nav class="nav">
      <a href="/admin/adminHomepage"><i data-lucide="layout-dashboard"></i> Dashboard</a>
      <a href="/admin/manageUsers"><i data-lucide="users"></i> Manage Users</a>
      <a href="/admin/ManageProperties"><i data-lucide="home"></i> Manage Properties</a>
      <!-- User Bookings removed -->
      <!-- View Chats removed for privacy -->
      <a href="/admin/Reports"><i data-lucide="bar-chart-2"></i> Generate Reports</a>
      <a href="/admin/editProfile" class="active" style="background: linear-gradient(90deg, #2e7d32, #1565c0);"><i data-lucide="user"></i> Edit Profile</a>
    </nav>

    <a href="<?= base_url('/admin/editProfile') ?>" class="profile-box" style="text-decoration:none;color:inherit;display:block;margin-top:10px;">
      <div class="profile-avatar">A</div>
       <div class="profile-info">
        <strong><?= session('FirstName') . ' ' . session('LastName'); ?></strong>
        <span><?= session('inputEmail'); ?></span>
      </div>
    </a>
  </aside>
  <section class="main" style="padding:20px;width:100%;">
    <header style="display:flex;justify-content:space-between;align-items:center;">
      <h1><i data-lucide="user"></i> Edit Profile</h1>
    </header>

    <div style="max-width: 600px; margin: 0 auto; background: var(--card); padding: 20px; border-radius: 10px; box-shadow: var(--shadow);">
      <form action="<?= base_url('/admin/updateProfile') ?>" method="post">
        <div class="mb-3">
          <label for="FirstName" class="form-label">First Name</label>
          <input type="text" class="form-control" name="FirstName" id="FirstName" value="<?= esc($user['FirstName'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
          <label for="MiddleName" class="form-label">Middle Name</label>
          <input type="text" class="form-control" name="MiddleName" id="MiddleName" value="<?= esc($user['MiddleName'] ?? '') ?>">
        </div>

        <div class="mb-3">
          <label for="LastName" class="form-label">Last Name</label>
          <input type="text" class="form-control" name="LastName" id="LastName" value="<?= esc($user['LastName'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
          <label for="Birthdate" class="form-label">Birthdate</label>
          <input type="date" class="form-control" name="Birthdate" id="Birthdate" value="<?= esc($user['Birthdate'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
          <label for="PhoneNumber" class="form-label">Phone Number</label>
          <input type="text" class="form-control" name="PhoneNumber" id="PhoneNumber" value="<?= esc($user['phoneNumber'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
          <label for="Email" class="form-label">Email</label>
          <input type="email" class="form-control" name="Email" id="Email" value="<?= esc($user['Email'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
          <label for="Password" class="form-label">New Password (leave empty to keep current)</label>
          <div class="input-group">
            <input type="password" class="form-control" name="Password" id="Password">
            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
              <i data-lucide="eye"></i>
            </button>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
          <a href="/admin/adminHomepage" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-success">Save Changes</button>
        </div>
      </form>
    </div>
  </section>

  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      lucide.createIcons();

      // Toggle password visibility
      const togglePasswordBtn = document.getElementById('togglePassword');
      const passwordInput = document.getElementById('Password');
      togglePasswordBtn.addEventListener('click', () => {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        const icon = togglePasswordBtn.querySelector('i');
        icon.setAttribute('data-lucide', type === 'password' ? 'eye' : 'eye-off');
        lucide.createIcons();
      });
    });
  </script>
</body>
</html>