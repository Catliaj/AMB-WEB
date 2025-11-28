<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Profile - ABM Property</title>

  <link href="<?= base_url("bootstrap5/css/bootstrap.min.css")?>" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  
  <style>
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
      --heading-color: #333333;
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
      --heading-color: #f0f0f0;
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
      transition: all 0.2s;
    }

    .nav-link-custom:hover,
    .nav-link-custom.active {
      color: var(--primary) !important;
    }

    .main-links .nav-item {
      margin: 0 1.5rem;
    }

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

    /* MAIN CONTENT */
    .main-content {
      padding: 2rem;
      min-height: calc(100vh - 100px);
    }

    /* CARDS */
    .card {
      background-color: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      box-shadow: 0 2px 10px var(--shadow);
    }

    /* HEADINGS - FIXED FOR THEME */
    h1, h2, h3, h4, h5, h6,
    .h1, .h2, .h3, .h4, .h5, .h6 {
      color: var(--heading-color) !important;
    }

    /* Specific targeting for profile headings */
    .card-body h3,
    .card-body .h5,
    .h5.fw-medium,
    .card-body h5 {
      color: var(--heading-color) !important;
    }

    /* Force all heading elements to follow theme */
    h1, h2, h3, h4, h5, h6,
    .h1, .h2, .h3, .h4, .h5, .h6,
    .card-title,
    .card-header,
    .modal-title,
    .nav-pills .nav-link {
      color: var(--heading-color);
    }

    /* AVATAR */
    .avatar-large {
      width: 96px;
      height: 96px;
      border-radius: 50%;
      background-color: var(--primary);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      font-weight: 500;
    }

    .avatar-large-img {
      border: 3px solid var(--border-color);
    }

    /* NAV PILLS */
    .nav-pills .nav-link {
      color: var(--text-color);
      border-radius: 8px;
      transition: all 0.2s;
    }

    .nav-pills .nav-link:hover {
      background-color: var(--light-bg);
    }

    .nav-pills .nav-link.active {
      background-color: var(--primary) !important;
      color: white !important;
    }

    /* FORMS */
    .form-control,
    .form-select {
      background-color: var(--card-bg);
      border: 1px solid var(--border-color);
      color: var(--text-color);
    }

    .form-control:focus,
    .form-select:focus {
      background-color: var(--card-bg);
      border-color: var(--primary);
      color: var(--text-color);
      box-shadow: 0 0 0 0.2rem rgba(70, 149, 65, 0.15);
    }

    .form-label {
      color: var(--text-color);
      font-weight: 500;
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

    .btn-outline-secondary {
      color: var(--text-color) !important;
      border-color: var(--border-color) !important;
    }

    .btn-outline-secondary:hover {
      background-color: var(--text-muted) !important;
      color: white !important;
    }

    .btn-danger {
      background-color: #dc3545 !important;
      border-color: #dc3545 !important;
    }

    /* TEXT - ENHANCED THEME SUPPORT */
    h1, h2, h3, h4, h5, h6, p, span, label, strong, div {
      color: var(--text-color);
    }

    .text-muted {
      color: var(--text-muted) !important;
    }

    /* Specific targeting for user name and email */
    .card-body .h5.mb-1,
    .card-body h3.h5,
    .card-body h5 {
      color: var(--heading-color) !important;
    }

    /* Ensure all text in card bodies follows theme */
    .card-body * {
      color: var(--text-color);
    }

    .card-body h1,
    .card-body h2,
    .card-body h3,
    .card-body h4,
    .card-body h5,
    .card-body h6 {
      color: var(--heading-color) !important;
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
          <li class="nav-item"><a class="nav-link nav-link-custom" href="/users/agentproperties">Properties</a></li>
          <li class="nav-item"><a class="nav-link nav-link-custom" href="/users/agentchat">Chat</a></li>
          <li class="nav-item d-lg-none"><a class="nav-link nav-link-custom active" href="/users/agentprofile">Profile</a></li>
        </ul>

        <ul class="navbar-nav align-items-center d-none d-lg-flex">
          <li class="nav-item"><a class="nav-link nav-link-custom active" href="/users/agentprofile"><i class="bi bi-person me-2"></i></a></li>
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
    <div class="container">
      <?php
        $user = isset($user) ? $user : [];
        $first = $user['FirstName'] ?? $user['first_name'] ?? '';
        $last = $user['LastName'] ?? $user['last_name'] ?? '';
        $initials = strtoupper(trim(($first ? $first[0] : '') . ($last ? $last[0] : '')));
        if (!empty($user['Image'])) {
          $profileImage = base_url('uploads/profiles/' . $user['Image']);
        } else {
          $profileImage = base_url('uploads/profiles/default-profile.jpg');
        }
      ?>

      <div class="card mb-4 animate__animated animate__fadeInUp">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-md-auto text-center text-md-start mb-3 mb-md-0">
              <?php if (!empty($user['Image'])): ?>
                <img src="<?= esc($profileImage) ?>" alt="Profile" class="avatar-large-img rounded-circle" style="width:96px;height:96px;object-fit:cover;" onerror="this.onerror=null;this.src='<?= base_url('uploads/profiles/default-profile.jpg') ?>'">
              <?php else: ?>
                <div class="avatar-large mx-auto mx-md-0"><?= esc($initials ?: 'U') ?></div>
              <?php endif; ?>
            </div>
            <div class="col-md">
              <h3 class="h5 mb-1"><?= esc(trim($first . ' ' . $last) ?: 'User') ?></h3>
              <p class="text-muted small mb-3"><?= esc($user['Email'] ?? session()->get('inputEmail') ?? '') ?></p>
              <div class="d-flex gap-2 flex-wrap">
                <label class="btn btn-primary btn-sm">Change Photo
                  <input id="avatarInput" type="file" name="avatar" accept="image/*" hidden>
                </label>
                <button id="removePhotoBtn" class="btn btn-outline-secondary btn-sm">Remove Photo</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <ul class="nav nav-pills mb-0" id="profileTabs" role="tablist">
          <li class="nav-item" role="presentation"><button class="nav-link active" id="profile-tab" data-bs-toggle="pill" data-bs-target="#profile" type="button">Profile</button></li>
          <li class="nav-item" role="presentation"><button class="nav-link" id="security-tab" data-bs-toggle="pill" data-bs-target="#security" type="button">Security</button></li>
          <li class="nav-item" role="presentation"><button class="nav-link" id="notifications-tab" data-bs-toggle="pill" data-bs-target="#notifications" type="button">Notifications</button></li>
          <li class="nav-item" role="presentation"><button class="nav-link" id="preferences-tab" data-bs-toggle="pill" data-bs-target="#preferences" type="button">Preferences</button></li>
        </ul>
        <a class="btn btn-danger d-flex align-items-center gap-2" href="/admin/logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
      </div>

      <div class="tab-content" id="profileTabsContent">
        <div class="tab-pane fade show active" id="profile" role="tabpanel">
          <div class="card">
            <div class="card-body">
              <h3 class="h5 fw-medium mb-4">Personal Information</h3>
              <form id="agentProfileForm" method="post" action="<?= esc($saveUrl ?? '/index.php/users/updateProfile') ?>" enctype="multipart/form-data">
                <?php if (function_exists('csrf_field')) echo csrf_field(); ?>
                <input type="hidden" name="remove_avatar" id="removeAvatarInput" value="0">
                <input type="hidden" name="full_name" id="agent_full_name" value="<?= esc(trim($first . ' ' . $last)) ?>">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input id="inputFirstNameAgent" type="text" class="form-control" name="first_name" value="<?= esc($first) ?>">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input id="inputLastNameAgent" type="text" class="form-control" name="last_name" value="<?= esc($last) ?>">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input id="inputEmailAgent" type="email" class="form-control" name="email" value="<?= esc($user['Email'] ?? '') ?>">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Phone Number</label>
                    <input id="inputPhoneAgent" type="tel" class="form-control" name="phone" value="<?= esc($user['phoneNumber'] ?? '') ?>">
                  </div>
                </div>
                <div class="mt-4 d-flex justify-content-end gap-2">
                  <button type="button" class="btn btn-outline-secondary" id="btnCancelProfileAgent">Cancel</button>
                  <button type="button" class="btn btn-primary" id="btnSaveProfileAgent">Save Changes</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="tab-pane fade" id="security" role="tabpanel">
          <div class="card">
            <div class="card-body">
              <h3 class="h5 fw-medium mb-4">Security Settings</h3>
              <form id="changePasswordForm" method="post" action="<?= esc('/users/change-password') ?>">
                <?php if (function_exists('csrf_field')) echo csrf_field(); ?>
                <div class="row g-3">
                  <div class="col-12">
                    <label class="form-label">Current Password</label>
                    <input name="current_password" id="current_password" type="password" class="form-control" required>
                  </div>
                  <div class="col-12">
                    <label class="form-label">New Password</label>
                    <input name="new_password" id="new_password" type="password" class="form-control" required>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Confirm New Password</label>
                    <input name="confirm_password" id="confirm_password" type="password" class="form-control" required>
                  </div>
                </div>
                <div class="mt-4 d-flex justify-content-end">
                  <button class="btn btn-primary">Change Password</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="tab-pane fade" id="notifications" role="tabpanel">
          <div class="card">
            <div class="card-body">
              <p class="text-muted">Notification settings coming soon.</p>
            </div>
          </div>
        </div>
        
        <div class="tab-pane fade" id="preferences" role="tabpanel">
          <div class="card">
            <div class="card-body">
              <p class="text-muted">Preferences coming soon.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="<?= base_url("bootstrap5/js/bootstrap.bundle.min.js")?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
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

  // Load saved theme
  document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    const themeIcon = document.getElementById('themeIcon');
    document.documentElement.setAttribute('data-theme', savedTheme);
    
    if (savedTheme === 'dark') {
      themeIcon.classList.remove('bi-sun-fill');
      themeIcon.classList.add('bi-moon-fill');
    }
  });

  // Profile Photo Management
  (function(){
    const avatarInput = document.getElementById('avatarInput');
    const removeBtn = document.getElementById('removePhotoBtn');
    const removeInput = document.getElementById('removeAvatarInput');
    const avatarImg = document.querySelector('.avatar-large-img') || document.querySelector('.avatar-large');

    if (avatarInput && avatarImg) {
      avatarInput.addEventListener('change', (e)=>{
        const f = avatarInput.files && avatarInput.files[0];
        if (!f) return;
        const reader = new FileReader();
        reader.onload = function(ev){
          try {
            if (avatarImg.tagName && avatarImg.tagName.toLowerCase() === 'img') {
              avatarImg.src = ev.target.result;
            } else {
              avatarImg.style.backgroundImage = `url(${ev.target.result})`;
              avatarImg.style.backgroundSize = 'cover';
              avatarImg.textContent = '';
            }
            if (removeInput) removeInput.value = '0';
          } catch(e){/* ignore */}
        };
        reader.readAsDataURL(f);
      });
    }

    if (removeBtn) {
      removeBtn.addEventListener('click', ()=>{
        if (!confirm('Remove profile photo?')) return;
        if (avatarImg) {
          if (avatarImg.tagName && avatarImg.tagName.toLowerCase() === 'img') {
            avatarImg.src = '<?= base_url('uploads/profiles/default-profile.jpg') ?>';
          } else {
            avatarImg.style.backgroundImage = '';
            avatarImg.textContent = '<?= esc($initials ?: 'U') ?>';
          }
        }
        if (removeInput) removeInput.value = '1';
        if (avatarInput) avatarInput.value = '';
      });
    }
  })();
  
  // Save / Cancel with SweetAlert for agent profile
  (function(){
    const form = document.getElementById('agentProfileForm');
    const btnSave = document.getElementById('btnSaveProfileAgent');
    const btnCancel = document.getElementById('btnCancelProfileAgent');
    const first = document.getElementById('inputFirstNameAgent');
    const last = document.getElementById('inputLastNameAgent');
    const email = document.getElementById('inputEmailAgent');
    const phone = document.getElementById('inputPhoneAgent');
    const fullHidden = document.getElementById('agent_full_name');

    const initial = {
      first: first ? first.value : '',
      last: last ? last.value : '',
      email: email ? email.value : '',
      phone: phone ? phone.value : ''
    };

    function changed(){
      return (first && first.value !== initial.first)
        || (last && last.value !== initial.last)
        || (email && email.value !== initial.email)
        || (phone && phone.value !== initial.phone);
    }

    if (btnSave && form) {
      btnSave.addEventListener('click', function(e){
        e.preventDefault();
        if (!changed()) {
          Swal.fire({icon:'info', title:'No changes', text:'There are no changes to save.'});
          return;
        }
        Swal.fire({
          title: 'Save changes?',
          text: 'Are you sure you want to save the changes to your profile?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Yes, save',
          cancelButtonText: 'Cancel'
        }).then((res)=>{
          if (res.isConfirmed) {
            if (fullHidden && first) fullHidden.value = (first.value || '') + (last && last.value ? (' ' + last.value) : '');
            form.submit();
          }
        });
      });
    }

    if (btnCancel) {
      btnCancel.addEventListener('click', function(e){
        e.preventDefault();
        if (!changed()) {
          Swal.fire({icon:'info', title:'Nothing to discard', text:'No changes to discard.'});
          return;
        }
        Swal.fire({
          title: 'Discard changes?',
          text: 'Any unsaved changes will be lost.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, discard',
          cancelButtonText: 'Continue editing'
        }).then((res)=>{
          if (res.isConfirmed) {
            if (first) first.value = initial.first;
            if (last) last.value = initial.last;
            if (email) email.value = initial.email;
            if (phone) phone.value = initial.phone;
            Swal.fire({icon:'success', title:'Changes discarded', timer:1200, showConfirmButton:false});
          }
        });
      });
    }

  })();
  </script>
</body>
</html>