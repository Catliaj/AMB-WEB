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
                        <img src="<?= base_url('assets/img/AMB_logo.png') ?>" alt="AMB Logo" height="50" class="me-2">
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
                                <a class="nav-link nav-link-custom" href="/users/clientreservations">Reservations</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-custom" href="/users/clientbookings">My Bookings</a>
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
                            $profileImage = !empty($user['Image'])
                                ? base_url('uploads/profiles/' . $user['Image'])
                                : base_url('uploads/profiles/default-profile.jpg');
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
                            <div class="d-flex gap-2 flex-wrap">
                                <button class="btn btn-primary btn-sm">Change Photo</button>
                                <button class="btn btn-outline-secondary btn-sm">Remove Photo</button>
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
                
                <button class="btn btn-outline-danger d-flex align-items-center gap-2">
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
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">First Name</label>
                                    <div class="input-icon-wrapper">
                                        <i class="bi bi-person input-icon"></i>
                                        <input type="text" class="form-control input-with-icon" value="<?= esc($user['FirstName'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Last Name</label>
                                    <div class="input-icon-wrapper">
                                        <i class="bi bi-person input-icon"></i>
                                        <input type="text" class="form-control input-with-icon" value="<?= esc($user['LastName'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <div class="input-icon-wrapper">
                                        <i class="bi bi-envelope input-icon"></i>
                                        <input type="email" class="form-control input-with-icon"
                                            value="<?= esc($user['Email'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <div class="input-icon-wrapper">
                                        <i class="bi bi-telephone input-icon"></i>
                                        <input type="tel" class="form-control input-with-icon"
                                            value="<?= esc($user['phoneNumber'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Address</label>
                                    <div class="input-icon-wrapper">
                                        <i class="bi bi-geo-alt input-icon"></i>
                                        <input type="text" class="form-control input-with-icon"
                                            value="<?= esc($user['Address'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 d-flex justify-content-end gap-2">
                                <button class="btn btn-outline-secondary">Cancel</button>
                                <button class="btn btn-primary">Save Changes</button>
                            </div>
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
    <script>
    (function(){
        const form = document.getElementById('changePasswordForm');
        const alertHolder = document.getElementById('securityAlert');
        const btn = document.getElementById('changePasswordBtn');

        function showAlert(message, type = 'danger'){
            alertHolder.innerHTML = `<div class="alert alert-${type} alert-dismissible" role="alert">${message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>`;
        }

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
