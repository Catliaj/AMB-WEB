<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Profile - ABM</title>

    <link href="<?= base_url("bootstrap5/css/bootstrap.min.css")?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url("assets/styles/clientstyle.css")?>">
</head>

<body>
    <!-- Navigation -->
    <body class="site-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light sticky-top custom-nav" id="mainNav">
                <div class="container-fluid">
                    <!-- Logo -->
                    <a class="navbar-brand d-flex align-items-center" href="homepage.html">
                        <img src="<?= base_url('assets/img/amb_logo.png') ?>" alt="AMB Logo" height="50" class="me-2">
                        <span class="logo-text">PROPERTY</span>
                    </a>

                    <!-- Hamburger Button -->
                    <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- Collapsible Content -->
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <!-- Center nav links (desktop), vertical in mobile -->
                        <ul class="navbar-nav mx-lg-auto text-center text-lg-start main-links">
                            <li class="nav-item">
                                <a class="nav-link nav-link-custom" href="/users/clientHomepage">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-custom" href="/users/clientbrowse">Browse Properties</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-custom" href="/users/clientbookings">My Bookings</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-custom" href="/users/clientreservations">Reservations</a>
                            </li>
                            <li class="nav-item d-lg-none">
                                <a class="nav-link nav-link-custom" href="/users/clientprofile">Profile</a>
                            </li>
                        </ul>

                        <!-- Right-aligned (desktop only) -->
                        <ul class="navbar-nav align-items-center d-none d-lg-flex">
                            <li class="nav-item">
                                <a class="nav-link nav-link-custom" href="/users/clientprofile">
                                    <i class="bi bi-person me-2"></i>
                                </a>
                            </li>
                            <li class="nav-item ms-2">
                                <button class="btn btn-link p-2" id="themeToggle" onclick="toggleTheme()">
                                    <i class="bi bi-sun-fill fs-5" id="themeIcon"></i>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
    </body>

    <main id="mainContent" class="page-bg-muted py-4">
        <div class="container">

            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <?php
                            $first = $user['FirstName'] ?? '';
                            $last = $user['LastName'] ?? '';
                            $initials = strtoupper(trim(($first ? $first[0] : '') . ($last ? $last[0] : '')));
                            // Build profile image path based on employmentStatus and stored filename
                            if (!empty($user['Image'])) {
                                $folder = (isset($user['employmentStatus']) && strtolower($user['employmentStatus']) === 'ofw') ? 'ofw' : 'locallyemployed';
                                $profileImage = base_url('uploads/' . $folder . '/' . $user['Image']);
                            } else {
                                $profileImage = base_url('uploads/profiles/default-profile.jpg');
                            }
                        ?>
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
                            <div class="d-flex gap-2 flex-wrap align-items-center">
                                <input type="file" id="profilePhotoInput" name="profilePhoto" accept="image/*" style="display:none">
                                <button class="btn btn-primary btn-sm" id="btnChangePhoto">Change Photo</button>
                                <button class="btn btn-outline-secondary btn-sm" id="btnRemovePhoto">Remove Photo</button>
                                <div id="photoUploadStatus" style="margin-left:8px; font-size:0.9rem; color:#666"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <ul class="nav nav-pills mb-0" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="profile-tab" data-bs-toggle="pill" data-bs-target="#profile"
                            type="button">Profile</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="security-tab" data-bs-toggle="pill" data-bs-target="#security"
                            type="button">Security</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="notifications-tab" data-bs-toggle="pill"
                            data-bs-target="#notifications" type="button">Notifications</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="preferences-tab" data-bs-toggle="pill" data-bs-target="#preferences"
                            type="button">Preferences</button>
                    </li>
                </ul>
                
                <button class="btn btn-danger d-flex align-items-center gap-2">
                    <i class="bi bi-box-arrow-right"></i>
                    <a class="nav-link nav-link-custom" href="/admin/logout"><span>Logout</span></a>
                    
                </button>
            </div>

            <div class="tab-content" id="profileTabsContent">
                <!-- Profile Tab -->
                <div class="tab-pane fade show active" id="profile" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                                    <h3 class="h5 fw-medium mb-4">Personal Information</h3>
                                    <form id="profileForm" method="post" action="<?= site_url('users/updateProfile') ?>" enctype="multipart/form-data">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="full_name" id="full_name_input" value="<?= esc(trim(($user['FirstName'] ?? '') . ' ' . ($user['LastName'] ?? ''))) ?>">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">First Name</label>
                                                <div class="input-icon-wrapper">
                                                    <i class="bi bi-person input-icon"></i>
                                                    <input id="inputFirstName" type="text" class="form-control input-with-icon" value="<?= esc($user['FirstName'] ?? '') ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Last Name</label>
                                                <div class="input-icon-wrapper">
                                                    <i class="bi bi-person input-icon"></i>
                                                    <input id="inputLastName" type="text" class="form-control input-with-icon" value="<?= esc($user['LastName'] ?? '') ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email</label>
                                                <div class="input-icon-wrapper">
                                                    <i class="bi bi-envelope input-icon"></i>
                                                    <input id="inputEmail" name="email" type="email" class="form-control input-with-icon" value="<?= esc($user['Email'] ?? '') ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Phone Number</label>
                                                <div class="input-icon-wrapper">
                                                    <i class="bi bi-telephone input-icon"></i>
                                                    <input id="inputPhone" name="phone" type="tel" class="form-control input-with-icon" value="<?= esc($user['phoneNumber'] ?? '') ?>">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Address</label>
                                                <div class="input-icon-wrapper">
                                                    <i class="bi bi-geo-alt input-icon"></i>
                                                    <input id="inputAddress" type="text" class="form-control input-with-icon" value="<?= esc($user['Address'] ?? '') ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4 d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-outline-secondary" id="btnCancelProfile">Cancel</button>
                                            <button type="button" class="btn btn-primary" id="btnSaveProfile">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                    </div>
                </div>

                <!-- Security Tab -->
                <div class="tab-pane fade" id="security" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="h5 fw-medium mb-4">Security Settings</h3>
                            <div id="securityAlert"></div>
                            <form id="changePasswordForm">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Current Password</label>
                                        <div class="input-icon-wrapper">
                                            <i class="bi bi-lock input-icon"></i>
                                            <input name="current_password" id="current_password" type="password" class="form-control input-with-icon" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">New Password</label>
                                        <div class="input-icon-wrapper">
                                            <i class="bi bi-lock input-icon"></i>
                                            <input name="new_password" id="new_password" type="password" class="form-control input-with-icon" required>
                                            <div class="form-text">At least 8 characters, include letters and numbers.</div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Confirm New Password</label>
                                        <div class="input-icon-wrapper">
                                            <i class="bi bi-lock input-icon"></i>
                                            <input name="confirm_password" id="confirm_password" type="password" class="form-control input-with-icon" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary" id="changePasswordBtn">Update Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Notifications Tab -->
                <div class="tab-pane fade" id="notifications" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="h5 fw-medium mb-4">Notification Preferences</h3>
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-bell text-primary me-3 mt-1"></i>
                                        <div>
                                            <p class="mb-1">Email Notifications</p>
                                            <small class="text-muted">Receive email updates about new properties and
                                                bookings</small>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" checked>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-heart text-primary me-3 mt-1"></i>
                                        <div>
                                            <p class="mb-1">Favorite Properties</p>
                                            <small class="text-muted">Get notified when your favorited properties have
                                                updates</small>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" checked>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-3">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-credit-card text-primary me-3 mt-1"></i>
                                        <div>
                                            <p class="mb-1">Price Drop Alerts</p>
                                            <small class="text-muted">Receive alerts when property prices drop</small>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preferences Tab -->
                <div class="tab-pane fade" id="preferences" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="h5 fw-medium mb-4">Display Preferences</h3>
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                    <div>
                                        <p class="mb-1">Dark Mode</p>
                                        <small class="text-muted">Enable dark theme across the application</small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="themeSwitch"
                                            onchange="toggleTheme()">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                    <div>
                                        <p class="mb-1">Show Sold Properties</p>
                                        <small class="text-muted">Display properties that have been sold</small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-3">
                                    <div>
                                        <p class="mb-1">Marketing Emails</p>
                                        <small class="text-muted">Receive promotional emails and special offers</small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" checked>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Chat Floating Action Button -->
    <a href="/users/chat" class="chat-fab" id="chatButton">
        <i class="bi bi-chat-dots-fill fs-4"></i>
    </a>


    <script src="<?= base_url("bootstrap5/js/bootstrap.bundle.min.js")?>"></script>
    <script src="<?= base_url("assets/js/client.js")?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    (function(){
        // Profile Save / Cancel with SweetAlert2
        const profileForm = document.getElementById('profileForm');
        const btnSave = document.getElementById('btnSaveProfile');
        const btnCancel = document.getElementById('btnCancelProfile');
        const firstInput = document.getElementById('inputFirstName');
        const lastInput = document.getElementById('inputLastName');
        const emailInput = document.getElementById('inputEmail');
        const phoneInput = document.getElementById('inputPhone');
        const addressInput = document.getElementById('inputAddress');
        const fullNameHidden = document.getElementById('full_name_input');

        const initialProfile = {
            first: firstInput ? firstInput.value : '',
            last: lastInput ? lastInput.value : '',
            email: emailInput ? emailInput.value : '',
            phone: phoneInput ? phoneInput.value : '',
            address: addressInput ? addressInput.value : ''
        };

        function hasProfileChanged(){
            return (firstInput && firstInput.value !== initialProfile.first)
                || (lastInput && lastInput.value !== initialProfile.last)
                || (emailInput && emailInput.value !== initialProfile.email)
                || (phoneInput && phoneInput.value !== initialProfile.phone)
                || (addressInput && addressInput.value !== initialProfile.address);
        }

        if (btnSave && profileForm) {
            btnSave.addEventListener('click', function(e){
                e.preventDefault();
                if (!hasProfileChanged()) {
                    Swal.fire({icon: 'info', title: 'No changes', text: 'There are no changes to save.'});
                    return;
                }

                Swal.fire({
                    title: 'Save changes?',
                    text: 'Are you sure you want to save the changes to your profile?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, save',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // set hidden full_name and submit form normally to allow server redirect & flashdata
                        if (fullNameHidden && firstInput) {
                            fullNameHidden.value = (firstInput.value || '') + (lastInput && lastInput.value ? (' ' + lastInput.value) : '');
                        }
                        profileForm.submit();
                    }
                });
            });
        }

        if (btnCancel) {
            btnCancel.addEventListener('click', function(e){
                e.preventDefault();
                if (!hasProfileChanged()) {
                    // nothing changed, simply inform
                    Swal.fire({icon: 'info', title: 'Nothing to discard', text: 'No changes to discard.'});
                    return;
                }
                Swal.fire({
                    title: 'Discard changes?',
                    text: 'Any unsaved changes will be lost.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, discard',
                    cancelButtonText: 'Continue editing'
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (firstInput) firstInput.value = initialProfile.first;
                        if (lastInput) lastInput.value = initialProfile.last;
                        if (emailInput) emailInput.value = initialProfile.email;
                        if (phoneInput) phoneInput.value = initialProfile.phone;
                        if (addressInput) addressInput.value = initialProfile.address;
                        Swal.fire({icon: 'success', title: 'Changes discarded', timer: 1200, showConfirmButton: false});
                    }
                });
            });
        }

        // end profile handlers

    
        const form = document.getElementById('changePasswordForm');
        const alertHolder = document.getElementById('securityAlert');
        const btn = document.getElementById('changePasswordBtn');

        function showAlert(message, type = 'danger'){
            alertHolder.innerHTML = `<div class="alert alert-${type} alert-dismissible" role="alert">${message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>`;
        }
    
        // Photo upload/remove handlers
        (function(){
            const btnChange = document.getElementById('btnChangePhoto');
            const btnRemove = document.getElementById('btnRemovePhoto');
            const fileInput = document.getElementById('profilePhotoInput');
            const statusEl = document.getElementById('photoUploadStatus');
            const avatarImg = document.querySelector('.avatar-large-img');
            const avatarPlaceholder = document.querySelector('.avatar-large');

            function setStatus(msg, isError) {
                if (!statusEl) return; statusEl.textContent = msg || ''; statusEl.style.color = isError ? '#c00' : '#666';
            }

            if (btnChange && fileInput) {
                btnChange.addEventListener('click', (e) => { e.preventDefault(); fileInput.click(); });
                fileInput.addEventListener('change', async (ev) => {
                    const f = fileInput.files && fileInput.files[0];
                    if (!f) return;
                    setStatus('Uploading...');
                    const fd = new FormData();
                    fd.append('profilePhoto', f);
                    // include CSRF if available
                    if (window.csrfName && window.csrfHash) fd.append(window.csrfName, window.csrfHash);
                    try {
                        const res = await fetch('<?= site_url('users/upload-profile-photo') ?>', { method: 'POST', body: fd, credentials: 'same-origin' });
                        const json = await res.json();
                        if (!res.ok || json.status !== 'success') {
                            setStatus(json.message || 'Upload failed', true);
                            return;
                        }
                        setStatus('Uploaded', false);
                        // update UI
                        if (avatarImg) {
                            avatarImg.src = json.url || '<?= base_url('uploads/profiles/default-profile.jpg') ?>';
                            if (avatarPlaceholder) avatarPlaceholder.style.display = 'none';
                        } else if (avatarPlaceholder) {
                            avatarPlaceholder.style.display = 'none';
                            // create img
                            const img = document.createElement('img'); img.src = json.url; img.className = 'avatar-large-img rounded-circle'; img.style.width='96px'; img.style.height='96px'; img.style.objectFit='cover';
                            avatarPlaceholder.parentElement.insertBefore(img, avatarPlaceholder);
                        }
                    } catch (err) {
                        console.error('Upload error', err);
                        setStatus('Network error', true);
                    }
                });
            }

            if (btnRemove) {
                btnRemove.addEventListener('click', async (e) => {
                    e.preventDefault();
                    if (!confirm('Remove profile photo?')) return;
                    setStatus('Removing...');
                    try {
                        const res = await fetch('<?= site_url('users/remove-profile-photo') ?>', { method: 'POST', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                        const json = await res.json();
                        if (!res.ok || json.status !== 'success') {
                            setStatus(json.message || 'Remove failed', true);
                            return;
                        }
                        setStatus('Removed', false);
                        // update UI: show default placeholder
                        if (avatarImg) {
                            avatarImg.src = json.url || '<?= base_url('uploads/profiles/default-profile.jpg') ?>';
                        }
                    } catch (err) {
                        console.error('Remove error', err);
                        setStatus('Network error', true);
                    }
                });
            }
        })();

        form && form.addEventListener('submit', async function(e){
            e.preventDefault();
            alertHolder.innerHTML = '';
            const current = document.getElementById('current_password').value.trim();
            const nw = document.getElementById('new_password').value.trim();
            const confirm = document.getElementById('confirm_password').value.trim();

            if (!current || !nw || !confirm) { showAlert('All fields are required.', 'warning'); return; }
            if (nw !== confirm) { showAlert('Passwords do not match.', 'warning'); return; }
            if (nw.length < 8 || !/[A-Za-z]/.test(nw) || !/[0-9]/.test(nw)) {
                showAlert('Password must be at least 8 characters and include letters and numbers.', 'warning');
                return;
            }

            btn.disabled = true;
            const payload = { current_password: current, new_password: nw, confirm_password: confirm };

            try {
                const res = await fetch('<?= base_url('/users/change-password') ?>', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const json = await res.json();
                if (res.ok && json.status === 'success'){
                    showAlert(json.message || 'Password updated.', 'success');
                    form.reset();
                } else {
                    showAlert(json.message || 'Failed to update password.','danger');
                }
            } catch (err) {
                showAlert('Network error. Try again.','danger');
            } finally {
                btn.disabled = false;
            }
        });
    })();
    </script>
    </div>
</body>

</html>
