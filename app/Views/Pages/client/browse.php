<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Browse Properties - ABM</title>

    <!-- Bootstrap & Icons -->
    <link href="<?= base_url("bootstrap5/css/bootstrap.min.css") ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url("assets/styles/clientstyle.css") ?>">
</head>


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

        <div class="filters-card mb-4">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-sliders text-primary me-2"></i>
                <span class="fw-medium">Filters</span>
            </div>

            <div class="row g-3">
                <div class="col-md-6 col-lg-3">
                    <div class="input-icon-wrapper">
                        <i class="bi bi-search input-icon"></i>
                        <input type="text" id="searchFilter" class="form-control input-with-icon themed-search"
                            placeholder="Search location..." oninput="applyFilters()">
                    </div>

                </div>
                <div class="col-md-6 col-lg-3">
                    <select id="typeFilter" class="form-select" onchange="applyFilters()">
                        <option value="all">All Types</option>
                        <option value="For Sale">For Sale</option>
                        <option value="For Rent">For Rent</option>
                        <option value="all">Liked</option>
                    </select>
                </div>
                <div class="col-md-6 col-lg-3">
                    <select id="bedroomsFilter" class="form-select" onchange="applyFilters()">
                        <option value="all">Any Bedrooms</option>
                        <option value="2">2 Bedrooms</option>
                        <option value="3">3 Bedrooms</option>
                        <option value="4">4 Bedrooms</option>
                        <option value="5">5+ Bedrooms</option>
                    </select>
                </div>
                <div class="col-md-6 col-lg-3">
                    <select id="priceFilter" class="form-select" onchange="applyFilters()">
                        <option value="all">All Prices</option>
                        <option value="0-500k">$0 - $500k</option>
                        <option value="500k-1m">$500k - $1M</option>
                        <option value="1m-5m">$1M - $5M</option>
                        <option value="5m+">$5M+</option>
                    </select>
                </div>
            </div>
        </div>

        <div id="propertiesContainer">
            <!-- JS will inject property cards here via renderPropertiesGrid() -->
        </div>
    </div>
</main>

<!-- Property Details Modal -->
<div class="modal fade" id="propertyDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content property-modal-content">
            <div class="modal-body p-0 position-relative">

                <!-- Image Area with Overlayed Details -->
                <div class="property-modal-image-wrapper position-relative">
                    <img id="modalPropertyImage" class="property-details-image" alt="Property Image">

                    <!-- Heart & Badge -->
                    <button class="property-favorite" id="modalFavoriteBtn" onclick="toggleFavoriteFromModal(event)">
                        <i class="bi bi-heart fs-5"></i>
                    </button>
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
                            <button class="btn btn-outline-primary" id="modalChatBtn">
                                <i class="bi bi-chat-dots-fill me-1"></i>Chat Agent
                            </button>
                            <button class="btn btn-primary" id="modalBookBtn">
                                <i class="bi bi-calendar-check me-2"></i>Book Property
                            </button>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>



<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content booking-modal-content">
            <div class="modal-body p-0 position-relative">

                <!-- Close Button -->
                <button type="button" class="btn-close booking-modal-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>

                <!-- Image Header with Overlay -->
                <div class="booking-modal-header">
    <img id="bookingPropertyImage" src="" alt="Property Image" class="booking-header-image">
    
    <!-- Navigation Arrows -->
    <button class="modal-nav prev" onclick="navigateBookingImage(-1)">
        <i class="bi bi-chevron-left"></i>
    </button>
    <button class="modal-nav next" onclick="navigateBookingImage(1)">
        <i class="bi bi-chevron-right"></i>
    </button>
    
    <div class="booking-header-overlay">
                        <span class="booking-property-type" id="bookingPropertyType"></span>
                        <h4 class="booking-property-title" id="bookingPropertyTitle"></h4>
                        <p class="booking-property-location mb-0">
                            <i class="bi bi-geo-alt"></i>
                            <span id="bookingPropertyLocation"></span>
                        </p>
                    </div>
                </div>

                <!-- Booking Form -->
                <div class="booking-modal-body">

                    <!-- Property Details Grid -->
                    <div class="booking-property-details mb-4">
                        <h5 class="booking-section-title">Property Details</h5>
                        <div class="row g-3">
                            <!-- Price -->
                            <div class="col-6 col-md-3">
                                <div class="detail-item">
                                    <i class="bi bi-currency-dollar text-primary"></i>
                                    <div>
                                        <small class="text-muted d-block">Price</small>
                                        <strong id="bookingPropertyPrice"></strong>
                                    </div>
                                </div>
                            </div>
                            <!-- Bedrooms -->
                            <div class="col-6 col-md-3">
                                <div class="detail-item">
                                    <i class="bi bi-house-door text-primary"></i>
                                    <div>
                                        <small class="text-muted d-block">Bedrooms</small>
                                        <strong id="bookingPropertyBedrooms"></strong>
                                    </div>
                                </div>
                            </div>
                            <!-- Agent (aligned vertically with Bedrooms) -->
                            <div class="col-6 col-md-3">
                                <div class="detail-item">
                                    <i class="bi bi-person-badge text-primary"></i>
                                    <div>
                                        <small class="text-muted d-block">Agent</small>
                                        <strong id="bookingPropertyAgent"></strong>
                                    </div>
                                </div>
                            </div>
                            <!-- Bathrooms -->
                            <div class="col-6 col-md-3">
                                <div class="detail-item">
                                    <i class="bi bi-droplet text-primary"></i>
                                    <div>
                                        <small class="text-muted d-block">Bathrooms</small>
                                        <strong id="bookingPropertyBathrooms"></strong>
                                    </div>
                                </div>
                            </div>
                            <!-- Corporation (aligned vertically with Bathrooms) -->
                            <div class="col-6 col-md-3">
                                <div class="detail-item">
                                    <i class="bi bi-building text-primary"></i>
                                    <div>
                                        <small class="text-muted d-block">Corporation</small>
                                        <strong id="bookingPropertyCorporation"></strong>
                                    </div>
                                </div>
                            </div>
                            <!-- Size -->
                            <div class="col-6 col-md-3">
                                <div class="detail-item">
                                    <i class="bi bi-arrows-fullscreen text-primary"></i>
                                    <div>
                                        <small class="text-muted d-block">Size</small>
                                        <strong id="bookingPropertySize"></strong>
                                    </div>
                                </div>
                            </div>
                            <!-- Parking -->
                            <div class="col-6 col-md-3">
                                <div class="detail-item">
                                    <i class="bi bi-p-square text-primary"></i>
                                    <div>
                                        <small class="text-muted d-block">Parking</small>
                                        <strong id="bookingPropertyParking"></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="booking-description mb-4">
                        <h5 class="booking-section-title">Description</h5>
                        <p class="text-muted" id="bookingPropertyDescription"></p>
                    </div>

                    <!-- Booking Form -->
                    <div class="booking-form-section">
                        <h5 class="booking-section-title">Schedule Your Visit</h5>
                        <form id="propertyBookingForm">
                            <input type="hidden" id="bookingPropertyId" name="property_id">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="bookingDate" class="form-label">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        Booking Date
                                    </label>
                                    <input type="date" class="form-control" id="bookingDate" name="booking_date"
                                        required>
                                </div>

                                <div class="col-md-6">
                                    <label for="bookingPurpose" class="form-label">
                                        <i class="bi bi-clipboard-check me-1"></i>
                                        Purpose
                                    </label>
                                    <select class="form-select" id="bookingPurpose" name="booking_purpose" required>
                                        <option value="" selected disabled>Select purpose...</option>
                                        <option value="Viewing">Viewing</option>
                                        <option value="Reserve">Reserve</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label for="bookingNotes" class="form-label">
                                        <i class="bi bi-chat-left-text me-1"></i>
                                        Additional Notes (Optional)
                                    </label>
                                    <textarea class="form-control" id="bookingNotes" name="booking_notes" rows="3"
                                        placeholder="Any special requests or questions..."></textarea>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="booking-actions mt-4">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    <i class="bi bi-x-lg me-2"></i>Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-calendar-check me-2"></i>Confirm Booking
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chat Floating Action Button -->
    <a href="/users/chat" class="chat-fab" id="chatButton">
        <i class="bi bi-chat-dots-fill fs-4"></i>
    </a>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const propertiesUrl = "<?= base_url('/properties/all') ?>";
    const propertiesViewUrl = "<?= base_url('/properties/view') ?>";

</script>


<script src="<?= base_url("bootstrap5/js/bootstrap.bundle.min.js") ?>"></script>
<script src="<?= base_url("assets/js/client.js") ?>"></script>

</div>
</body>

</html>