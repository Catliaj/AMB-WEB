<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>My Reservations - ABM</title>

    <link href="<?= base_url("bootstrap5/css/bootstrap.min.css")?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url("assets/styles/clientstyle.css")?>">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
                                <a class="nav-link nav-link-custom" href="/users/clientbookings">My Bookings</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-link-custom active" href="/users/clientreservations">Reservations</a>
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
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon me-3">
                                    <i class="bi bi-calendar-check"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">Total Reservations</p>
                                    <h3 id="totalReservationsCount" class="h4 mb-0">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon me-3">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">Scheduled</p>
                                    <h3 id="scheduledReservationsCount" class="h4 mb-0">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon me-3">
                                    <i class="bi bi-clock"></i>
                                </div>
                                <div>
                                    <p class="text-muted small mb-0">Pending</p>
                                    <h3 id="pendingReservationsCount" class="h4 mb-0">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="bookingsList">
                <!-- Reservations will be rendered here by bookings.js (mode: reservations) -->
            </div>

        </div>
    </main>

<div class="modal fade" id="bookingDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bookingModalTitle">Booking Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-5">
            <div id="bookingImageWrapper" class="mb-3 text-center">
              <img id="bookingModalImage" src="<?= base_url('uploads/properties/no-image.jpg') ?>" class="img-fluid rounded" alt="Property image" style="max-height:300px; object-fit:cover;">
            </div>
            <div class="d-flex justify-content-center gap-2">
                            <button id="modalContactAgentBtn" class="btn btn-outline-primary btn-sm"><i class="bi bi-chat-dots"></i> Contact Agent</button>
                            <button id="modalDetailsBtn" class="btn btn-outline-secondary btn-sm"><i class="bi bi-info-circle"></i> Details</button>
            </div>
          </div>

          <div class="col-md-7">
            <h5 id="bookingModalPropertyTitle" class="mb-1">Property title</h5>
            <p id="bookingModalLocation" class="text-muted small mb-2">Location</p>

                        <div class="mb-2">
                            <span id="bookingModalStatus" class="badge bg-secondary">Status</span>
                        </div>

            <div id="bookingModalMeta" class="mb-2 small text-muted">
              <div><strong>Agent:</strong> <span id="bookingModalAgent">—</span></div>
              <div id="bookingModalAgentContacts" class="small text-muted">
                <div><i class="bi bi-telephone me-1"></i><span id="bookingModalAgentPhone">—</span></div>
                <div><i class="bi bi-envelope me-1"></i><span id="bookingModalAgentEmail">—</span></div>
              </div>
              <div><strong>Price:</strong> <span id="bookingModalPrice">—</span></div>
            </div>

            
            </div>

            <div class="mt-3">
              <!-- Hidden agent id (if available) -->
              <input type="hidden" id="bookingModalAgentId" value="">
              <button id="modalCancelBookingBtn" class="btn btn-danger btn-sm me-2">Cancel Booking</button>
              <button id="modalCloseBtn" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
        </div>

        <div id="bookingModalHistory" class="mt-3 small text-muted">
          <!-- optional status history/other details will be injected here -->
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Confirm Contract Modal (client) -->
<div class="modal fade" id="confirmContractModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Contract</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label"><strong>Property Price</strong></label>
                    <div id="contractPropertyPrice">—</div>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Client Age</strong></label>
                    <div id="contractClientAge">—</div>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Payment Mode</strong></label>
                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="contractMode" id="modePagibig" value="pagibig">
                            <label class="form-check-label" for="modePagibig">Pagibig (max age 60)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="contractMode" id="modeBanko" value="banko">
                            <label class="form-check-label" for="modeBanko">Bank (max age 30)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="contractMode" id="modeFull" value="full">
                            <label class="form-check-label" for="modeFull">Full Payment</label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Computed Monthly</strong></label>
                    <div id="contractMonthly">—</div>
                </div>

                <div id="contractErrors" class="text-danger small mb-2" style="display:none;"></div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="confirmContractBtn" class="btn btn-primary">Confirm Contract</button>
            </div>
        </div>
    </div>
</div>

    <!-- Chat Floating Action Button -->
    <a href="/users/chat" class="chat-fab" id="chatButton">
        <i class="bi bi-chat-dots-fill fs-4"></i>
    </a>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= base_url("bootstrap5/js/bootstrap.bundle.min.js")?>"></script>
    <script src="<?= base_url("assets/js/client.js")?>"></script>
    <script src="<?= base_url("assets/js/bookings.js")?>"></script>
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/locale/en-gb.js"></script>
    <script>
        window.bookingCreateUrl = <?= json_encode(site_url('bookings/create')) ?>;
        window.myBookingsUrl = <?= json_encode(site_url('bookings/mine')) ?>;
        window.bookingCancelUrl = <?= json_encode(site_url('bookings/cancel')) ?>;
          window.getUserUrlBase = <?= json_encode(site_url('users/getUser')) ?>;
                // expose current user id for client-side age lookup
                window.currentUserId = <?= json_encode($currentUserId ?? null) ?>;
        window.csrfName = <?= json_encode(csrf_token()) ?>;
        window.csrfHash = <?= json_encode(csrf_hash()) ?>;
    // Reservations page: show pending/confirmed/reserved
    window.bookingsMode = 'reservations';
    </script>
    </div>
</body>

</html>
