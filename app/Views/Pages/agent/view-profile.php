<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Profile - AMB</title>

  <link href="<?= base_url("bootstrap5/css/bootstrap.min.css")?>" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= base_url("assets/styles/clientstyle.css")?>">
</head>

<body class="site-wrapper">

  <main id="mainContent" class="page-bg-muted py-4">
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

      <div class="card mb-4">
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
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input type="text" class="form-control" name="first_name" value="<?= esc($first) ?>">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" class="form-control" name="last_name" value="<?= esc($last) ?>">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?= esc($user['Email'] ?? '') ?>">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" name="phone" value="<?= esc($user['phoneNumber'] ?? '') ?>">
                  </div>
                </div>
                <div class="mt-4 d-flex justify-content-end gap-2">
                  <a class="btn btn-outline-secondary" href="/users/agentHomepage">Cancel</a>
                  <button type="submit" class="btn btn-primary">Save Changes</button>
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

        <div class="tab-pane fade" id="notifications" role="tabpanel"><div class="card"><div class="card-body">Notification settings coming soon.</div></div></div>
        <div class="tab-pane fade" id="preferences" role="tabpanel"><div class="card"><div class="card-body">Preferences coming soon.</div></div></div>
      </div>
    </div>
  </main>

  <script src="<?= base_url("bootstrap5/js/bootstrap.bundle.min.js")?>"></script>
  <script>
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
  </script>
</body>
</html>
