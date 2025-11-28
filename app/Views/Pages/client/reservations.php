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
    
    <!-- Enhanced Booking Styles -->
    <style>
        /* Fix for modal property titles to follow theme */
#bookingModalPropertyTitle,
#modalPropertyTitle {
    color: var(--text-color) !important;
}

/* Ensure all modal text follows theme */
.modal-enhanced .modal-body,
.modal-enhanced .modal-content {
    color: var(--text-color);
}

.modal-enhanced .text-muted {
    color: var(--text-muted) !important;
}
        /* Enhanced Booking Card Styles */
        .booking-card-enhanced {
            background-color: var(--card-bg);
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 6px 20px var(--shadow);
            border: 1px solid var(--border-color);
            margin-bottom: 1.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .booking-card-enhanced:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px var(--shadow);
        }

        .booking-card-enhanced::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            z-index: 1;
        }

        .booking-image-container {
            position: relative;
            overflow: hidden;
            height: 200px;
        }

        .booking-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .booking-card-enhanced:hover .booking-image {
            transform: scale(1.05);
        }

        .booking-status-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            z-index: 2;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.4rem 0.8rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .booking-card-body {
            padding: 1.75rem;
            background-color: var(--card-bg);
        }

        .booking-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-color);
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .booking-location {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .booking-meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background-color: var(--light-bg);
            border-radius: 0.75rem;
        }

        .meta-item {
            text-align: center;
        }

        .meta-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .meta-value {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .booking-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .booking-btn {
            flex: 1;
            min-width: 120px;
            padding: 0.6rem 1rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            border: none;
            text-align: center;
        }

        /* Enhanced Modal Alignment */
        .modal-enhanced {
            backdrop-filter: blur(8px);
        }

        .modal-enhanced .modal-content {
            border: none;
            border-radius: 1.25rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
            background-color: var(--card-bg);
            overflow: hidden;
        }

        .modal-enhanced .modal-header {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            border-bottom: none;
            padding: 1.5rem 2rem;
        }

        .modal-enhanced .modal-title {
            font-weight: 700;
            font-size: 1.25rem;
        }

        .modal-enhanced .modal-body {
            padding: 2rem;
        }

        .modal-enhanced .btn-close {
            filter: invert(1);
            opacity: 0.8;
        }

        .modal-enhanced .btn-close:hover {
            opacity: 1;
        }

        /* Stats Cards Enhancement */
        .stat-card {
            background: linear-gradient(135deg, var(--card-bg), var(--light-bg));
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            padding: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px var(--shadow);
        }

        .stat-icon {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        /* Contract Modal Enhancement */
        .contract-option-card {
            border: 2px solid var(--border-color);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: var(--card-bg);
        }

        .contract-option-card:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px var(--shadow);
        }

        .contract-option-card.selected {
            border-color: var(--primary);
            background-color: var(--light-accent);
        }

        .payment-calculation-box {
            background: linear-gradient(135deg, var(--light-bg), var(--card-bg));
            border-radius: 1rem;
            padding: 1.5rem;
            border-left: 4px solid var(--primary);
        }

        /* Signature Pad Enhancement */
        .signature-container {
            border: 2px dashed var(--border-color);
            border-radius: 1rem;
            padding: 1rem;
            background-color: var(--bg-color);
        }

        /* Empty State Styling */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--text-muted);
        }
        
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            opacity: 0.5;
        }
        
        .empty-state-title {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .empty-state-description {
            max-width: 500px;
            margin: 0 auto 1.5rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            /* Target the specific button container structure from client.js */
            #bookingsList .col-auto .d-flex.flex-column {
                flex-direction: row !important;
                justify-content: center !important;
                align-items: center !important;
                gap: 0.5rem !important;
            }
            
            /* Make buttons equal width and horizontal */
            #bookingsList .col-auto .d-flex button {
                flex: 1 !important;
                min-width: 110px !important;
                max-width: 150px !important;
                margin-bottom: 15px;
            }
            
            /* Adjust the parent column to center buttons */
            #bookingsList .col-auto {
                width: 100% !important;
                padding: 0 1rem !important;
                margin-top: 1rem;
            }
            .booking-meta-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
            }

            .booking-actions {
                flex-direction: column;
            }

            .booking-btn {
                min-width: 100%;
            }

            .modal-enhanced .modal-body {
                padding: 1.5rem;
            }

            .stat-card {
                padding: 1.25rem;
            }
        }

        @media (max-width: 576px) {
            .booking-card-body {
                padding: 1.25rem;
            }

            .booking-meta-grid {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }

            .modal-enhanced .modal-header {
                padding: 1.25rem 1.5rem;
            }
        }

        /* Animation for modal entrance */
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-enhanced.show .modal-dialog {
            animation: modalSlideIn 0.3s ease-out;
        }

        /* Loading states */
        .loading-skeleton {
            background: linear-gradient(90deg, var(--border-color) 25%, var(--light-bg) 50%, var(--border-color) 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }
            100% {
                background-position: -200% 0;
            }
        }
    </style>
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
                    <!-- Confirm Contract Modal (used by bookings.js) -->
                    <div class="modal fade modal-enhanced" id="confirmContractModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Select Payment / Propose Contract</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                            <label class="form-label"><strong>Property Price</strong></label>
                                            <div id="contractPropertyPrice" class="fs-5">—</div>
                                    </div>
                                    <div class="mb-3">
                                            <label class="form-label"><strong>Client Age</strong></label>
                                            <div id="contractClientAge" class="fs-5">—</div>
                                    </div>
                                    <div class="mb-3">
                                            <label class="form-label"><strong>Payment Mode</strong></label>
                                            <div>
                                                    <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="contractMode" id="modePagibig_confirm_res" value="pagibig">
                                                            <label class="form-check-label" for="modePagibig_confirm_res">Pagibig (max 60 years)</label>
                                                    </div>
                                                    <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="contractMode" id="modeBanko_confirm_res" value="banko">
                                                            <label class="form-check-label" for="modeBanko_confirm_res">Bank (max 30 years)</label>
                                                    </div>
                                                    <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="contractMode" id="modeFull_confirm_res" value="full">
                                                            <label class="form-check-label" for="modeFull_confirm_res">Full Payment</label>
                                                    </div>
                                            </div>
                                    </div>
                                    <div class="mb-3">
                                            <label class="form-label"><strong>Payment Calculation</strong></label>
                                            <div id="contractMonthly" class="payment-calculation-box">—</div>
                                    </div>
                                    <div id="contractErrors" class="text-danger small mb-2" style="display:none;"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" id="confirmContractBtn" class="btn btn-primary">Send Proposal</button>
                                </div>
                            </div>
                        </div>
                    </div>
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
                    <div class="card stat-card">
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
                    <div class="card stat-card">
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
                    <div class="card stat-card">
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
                
                <!-- Empty State (shown when no reservations) -->
                <div id="emptyBookingsState" class="empty-state" style="display: none;">
                    <div class="empty-state-icon">
                        <i class="bi bi-calendar-x"></i>
                    </div>
                    <h3 class="empty-state-title">No Reservations Found</h3>
                    <p class="empty-state-description">
                        You haven't made any property reservations yet. Browse our properties and make your first reservation.
                    </p>
                    <a href="/users/clientbrowse" class="btn btn-primary">
                        <i class="bi bi-house me-1"></i> Browse Properties
                    </a>
                </div>
            </div>

        </div>
    </main>

    <!-- Property Details Modal (copied exactly from bookings.php) -->
    <div class="modal fade modal-enhanced" id="propertyDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content property-modal-content">
                <div class="modal-body p-0 position-relative">

                    <!-- Close button (visible) -->
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>

                    <!-- Image Area with Overlayed Details -->
                    <div class="property-modal-image-wrapper position-relative">
                        <img id="modalPropertyImage" class="property-details-image" alt="Property Image">

                       
                        <span class="property-type-badge" id="modalPropertyType"></span>

                        <!-- Navigation Arrows -->
                        <button class="modal-nav prev" onclick="navigateProperty(-1)">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button class="modal-nav next" onclick="navigateProperty(1)">
                            <i class="bi bi-chevron-right"></i>
                        </button>

                        <!-- Overlay Details -->
                        <div class="property-modal-details-overlay">
                            <!-- Property Title -->
                            <h5 id="modalPropertyTitle" class="mb-1"></h5>

                            <!-- Price directly under title -->
                            <div class="property-price text-primary fw-bold mb-2">
                                ₱<span id="modalPropertyPrice"></span>
                            </div>

                            <!-- Location -->
                            <div class="location mb-2">
                                <i class="bi bi-geo-alt"></i>
                                <span id="modalPropertyLocation"></span>
                            </div>

                            <!-- House stats -->
                            <div class="house-stats mb-3">
                                <div><i class="bi bi-house-door"></i><span id="modalPropertyBeds"></span></div>
                                <div><i class="bi bi-droplet"></i><span id="modalPropertyBaths"></span></div>
                                <div><i class="bi bi-arrows-fullscreen"></i><span id="modalPropertySqft"></span></div>
                            </div>

                            <!-- Action buttons -->
                            <div class="price-book">
                                <button class="btn btn-primary" id="modalChatBtn">
                                    <i class="bi bi-chat-dots-fill me-1"></i>Chat Agent
                                </button>
                               
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Booking Detail Modal -->
    <div class="modal fade modal-enhanced" id="bookingDetailModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="bookingModalTitle">Reservation Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-5">
                <div id="bookingImageWrapper" class="mb-3 text-center">
                  <img id="bookingModalImage" src="<?= base_url('uploads/properties/no-image.jpg') ?>" class="img-fluid rounded" alt="Property image" style="max-height:300px; object-fit:cover;">
                </div>
                <div class="d-flex justify-content-center gap-2">
                  <button id="modalContactAgentBtn" class="btn btn-primary btn-sm"><i class="bi bi-chat-dots"></i> Contact Agent</button>
                </div>
              </div>

              <div class="col-md-7">
                <h5 id="bookingModalPropertyTitle" class="mb-1">Property title</h5>
                <p id="bookingModalLocation" class="text-muted small mb-2">Location</p>

                <div class="mb-2">
                  <span id="bookingModalStatus" class="badge bg-secondary">Status</span>
                  <span class="ms-2 text-muted" id="bookingModalDate">Date</span>
                </div>

                            <div id="bookingModalMeta" class="mb-2 small text-muted">
                                <div class="mb-1"><strong>Agent:</strong> <span id="bookingModalAgent">—</span></div>
                                <div id="bookingModalAgentContacts" class="small text-muted mb-2">
                                    <div><i class="bi bi-telephone me-1"></i><span id="bookingModalAgentPhone">—</span></div>
                                    <div><i class="bi bi-envelope me-1"></i><span id="bookingModalAgentEmail">—</span></div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-6"><small class="text-muted d-block">Price</small><strong id="bookingModalPrice">—</strong></div>
                                    <div class="col-6"><small class="text-muted d-block">Date & Time</small><span id="bookingModalDate">—</span></div>
                                    <div class="col-6"><small class="text-muted d-block">Bedrooms</small><span id="bookingModalBeds">—</span></div>
                                    <div class="col-6"><small class="text-muted d-block">Bathrooms</small><span id="bookingModalBaths">—</span></div>
                                    <div class="col-6"><small class="text-muted d-block">Size</small><span id="bookingModalSize">—</span></div>
                                    <div class="col-6"><small class="text-muted d-block">Parking</small><span id="bookingModalParking">—</span></div>
                                    <div class="col-6"><small class="text-muted d-block">Property Type</small><span id="bookingModalPropertyType">—</span></div>
                                    <div class="col-12"><small class="text-muted d-block">Corporation</small><span id="bookingModalCorporation">—</span></div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <h6 class="mb-1">Property Description</h6>
                                <p id="bookingModalDescription" class="small text-muted mb-0">—</p>
                            </div>

                        <div class="mt-3 d-flex justify-content-end">
                            <!-- Hidden agent id (if available) -->
                            <input type="hidden" id="bookingModalAgentId" value="">
                            <button id="modalSelectPaymentBtn" class="btn btn-info btn-sm me-2" style="display:none">Select Payment</button>
                            <button id="modalConfirmContractBtn" class="btn btn-primary btn-sm me-2" style="display:none">Confirm Contract</button>
                            <button id="modalCancelBookingBtn" class="btn btn-danger btn-sm me-2">Cancel Booking</button>
                            
                            <button id="modalCloseBtn" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
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

<!-- Payment Selection Modal -->
<div class="modal fade modal-enhanced" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Payment Method</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="paymentModalBookingId" value="">
                <input type="hidden" id="paymentModalReservationId" value="">
                <input type="hidden" id="paymentModalPropertyPrice" value="">
                <input type="hidden" id="paymentModalClientAge" value="">
                <input type="hidden" id="paymentModalMonthlyPayment" value="">
                <input type="hidden" id="paymentModalMode" value="">
                
                <div class="mb-3">
                    <label class="form-label"><strong>Property Price</strong></label>
                    <div id="contractPropertyPrice" class="fs-5">—</div>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Client Age</strong></label>
                    <div id="contractClientAge" class="fs-5">—</div>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Payment Mode</strong></label>
                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="contractMode" id="modePagibig" value="pagibig">
                            <label class="form-check-label" for="modePagibig">Pagibig (max 60 years)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="contractMode" id="modeBanko" value="banko">
                            <label class="form-check-label" for="modeBanko">Bank (max 30 years)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="contractMode" id="modeFull" value="full">
                            <label class="form-check-label" for="modeFull">Full Payment</label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Payment Calculation</strong></label>
                    <div id="contractMonthly" class="payment-calculation-box">—</div>
                </div>

                <div id="contractErrors" class="text-danger small mb-2" style="display:none;"></div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="confirmPaymentBtn" class="btn btn-primary">Confirm Payment</button>
            </div>
        </div>
    </div>
</div>

<!-- Sign Contract Modal -->
<div class="modal fade modal-enhanced" id="signContractModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sign Contract</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="signContractReservationId" value="">
                <input type="hidden" id="signContractBookingId" value="">
                
                <div class="mb-3">
                    <label class="form-label"><strong>Please sign below:</strong></label>
                    <div class="signature-container">
                        <canvas id="signaturePad" style="border: 1px solid #ddd; cursor: crosshair; width:100%; height:200px; touch-action: none;"></canvas>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="clearSignatureBtn">Clear</button>
                </div>
                
                <div id="signContractErrors" class="text-danger small mb-2" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="signContractBtn" class="btn btn-primary">Sign & Generate Contract</button>
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
    <!-- Load moment before booking scripts that depend on it -->
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/locale/en-gb.js"></script>
    <script src="<?= base_url("assets/js/client.js")?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.9/dist/signature_pad.umd.min.js"></script>
    <script src="<?= base_url("assets/js/bookings.js")?>"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        window.bookingCreateUrl = <?= json_encode(site_url('bookings/create')) ?>;
        window.myBookingsUrl = <?= json_encode(site_url('bookings/mine')) ?>;
        window.reservationsUrl = <?= json_encode(site_url('bookings/reservations')) ?>;
        window.bookingCancelUrl = <?= json_encode(site_url('bookings/cancel')) ?>;
        window.reserveUrl = <?= json_encode(site_url('users/reserve')) ?>;
        window.selectPaymentUrl = <?= json_encode(site_url('users/selectPayment')) ?>;
        window.signContractUrl = <?= json_encode(site_url('users/signContract')) ?>;
        window.getUserUrlBase = <?= json_encode(site_url('users/getUser')) ?>;
        window.getAgeUrlBase = <?= json_encode(site_url('users/getAge')) ?>;
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