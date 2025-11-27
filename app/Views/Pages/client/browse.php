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
    <style>
        /* ========================================
   PROFESSIONAL FILTER SIDEBAR
======================================== */

/* Filter Toggle Button (Mobile Only) */
.filter-toggle-btn {
    position: fixed;
    bottom: 30px;
    left: 1rem;
    z-index: 1030;
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    color: white;
    border: none;
    border-radius: 50px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    box-shadow: 0 4px 20px rgba(70, 149, 65, 0.3);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.filter-toggle-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(70, 149, 65, 0.4);
}

.filter-toggle-btn i {
    font-size: 1.2rem;
}

/* Hide on desktop */
@media (min-width: 992px) {
    .filter-toggle-btn {
        display: none;
    }
}

/* Filter Sidebar Container */
.filter-sidebar {
    background-color: var(--card-bg);
    border-right: 1px solid var(--border-color);
    width: 360px !important;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
}

html[data-theme="dark"] .filter-sidebar {
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
}

/* Desktop: Fixed sidebar */
@media (min-width: 992px) {
    .filter-sidebar {
        position: sticky;
        top: 120px;
        height: auto;
        max-height: calc(100vh - 140px);
        overflow-y: auto;
        border: none;
        border-right: 1px solid var(--border-color);
    }
    
    /* Hide scrollbar */
    .filter-sidebar::-webkit-scrollbar {
        width: 6px;
    }
    
    .filter-sidebar::-webkit-scrollbar-track {
        background: transparent;
    }
    
    .filter-sidebar::-webkit-scrollbar-thumb {
        background: var(--text-muted);
        border-radius: 10px;
    }
}

/* Sidebar Content */
.filter-sidebar-content {
    padding: 1.5rem;
}

.offcanvas-header {
    border-bottom: 1px solid var(--border-color);
    padding: 1.25rem 1.5rem;
}

.offcanvas-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-color);
}

/* Filter Group */
.filter-group {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid var(--border-color);
}

.filter-group:last-of-type {
    border-bottom: none;
}

/* Filter Label */
.filter-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--text-color);
    margin-bottom: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-label i {
    color: var(--primary);
    font-size: 1.1rem;
}

/* Search Input Wrapper */
.search-input-wrapper {
    position: relative;
}

.search-input-wrapper .search-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    pointer-events: none;
    font-size: 1rem;
}

/* Filter Input */
.filter-input {
    width: 100%;
    padding: 0.75rem 2.5rem 0.75rem 1rem;
    border: 1.5px solid var(--border-color);
    border-radius: 8px;
    background-color: var(--bg-color);
    color: var(--text-color);
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.filter-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(70, 149, 65, 0.1);
}

.filter-input::placeholder {
    color: var(--text-muted);
}

/* Filter Chips */
.filter-options-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.filter-chip {
    padding: 0.75rem 1.25rem;
    border: 1.5px solid var(--border-color);
    border-radius: 50px;
    background-color: var(--bg-color);
    color: var(--text-color);
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
    text-align: center;
    flex: 0 0 auto; /* Don't grow or shrink */
    width: auto; /* Let content determine width */
    min-height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.filter-chip:hover {
    border-color: var(--primary);
    background-color: var(--light-accent);
    transform: translateY(-2px);
}

.filter-chip.active {
    background-color: var(--primary);
    color: white;
    border-color: var(--primary);
}

html[data-theme="dark"] .filter-chip.active {
    background-color: var(--primary);
    color: white;
}

/* Price Range Container */
.price-range-container {
    width: 100%;
}

.price-inputs {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.price-input-group {
    position: relative;
    flex: 1;
}

.price-input-group .currency {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-weight: 600;
}

.price-input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.25rem;
    border: 1.5px solid var(--border-color);
    border-radius: 8px;
    background-color: var(--bg-color);
    color: var(--text-color);
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.price-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(70, 149, 65, 0.1);
}

.price-separator {
    color: var(--text-muted);
    font-weight: 600;
}

/* Filter Actions */
.filter-actions {
    display: flex;
    gap: 0.75rem;
    padding-top: 1.5rem;
    margin-top: 1.5rem;
    border-top: 1px solid var(--border-color);
}

.btn-filter-clear,
.btn-filter-apply {
    flex: 1;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.95rem;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-filter-clear {
    background-color: transparent;
    color: var(--text-color);
    border: 1.5px solid var(--border-color);
}

.btn-filter-clear:hover {
    background-color: var(--light-bg);
    border-color: var(--text-muted);
}

.btn-filter-apply {
    background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    color: white;
}

.btn-filter-apply:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(70, 149, 65, 0.3);
}

/* Content Area with Sidebar */
.content-with-sidebar {
    flex: 1;
    padding: 2rem;
    min-height: calc(100vh - 120px);
}

@media (min-width: 992px) {
    main#mainContent {
        display: flex;
        gap: 0;
        padding: 0 !important;
    }
    
    main#mainContent > .container {
        max-width: 100%;
        padding: 0;
        margin: 0;
        display: flex;
        width: 100%;
    }
}

/* Mobile Adjustments */
@media (max-width: 991px) {
    .filter-sidebar {
        width: 85vw !important;
        max-width: 400px;
    }
    
    .content-with-sidebar {
        padding: 1rem;
    }
    
    .filter-options-grid {
        grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
    }
}

/* Smooth Animations */
.filter-chip,
.filter-input,
.price-input {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.filter-sidebar .offcanvas-body {
    transition: transform 0.3s ease-in-out;
}

/* Results Count Badge */
.results-count {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background-color: var(--light-accent);
    color: var(--primary);
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

html[data-theme="dark"] .results-count {
    background-color: var(--light-bg);
}
    </style>
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

<main id="mainContent" class="page-bg-muted">
    <div class="container-fluid p-0">
        <div class="d-flex">
            <!-- Filter Toggle Button (Mobile) -->
            <button class="filter-toggle-btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterSidebar">
                <i class="bi bi-sliders"></i>
                <span>Filters</span>
            </button>

            <!-- Desktop Sidebar + Mobile Offcanvas -->
            <div class="offcanvas-lg offcanvas-start filter-sidebar" tabindex="-1" id="filterSidebar">
                <div class="offcanvas-header d-lg-none">
                    <h5 class="offcanvas-title">Filters</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                </div>
                
                <div class="offcanvas-body">
                    <div class="filter-sidebar-content">
                        <!-- Search Filter -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="bi bi-search"></i>
                                Search Location
                            </label>
                            <div class="search-input-wrapper">
                                <input type="text" 
                                       id="searchFilter" 
                                       class="filter-input" 
                                       placeholder="Enter city, area, or landmark..."
                                       oninput="applyFilters()">
                                <i class="bi bi-geo-alt search-icon"></i>
                            </div>
                        </div>

                        <!-- Property Type Filter -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="bi bi-house-door"></i>
                                Property Type
                            </label>
                            <div class="filter-options-grid">
                                <button class="filter-chip active" data-filter="type" data-value="all" onclick="selectChip(this, 'type')">
                                    All Types
                                </button>
                                <button class="filter-chip" data-filter="type" data-value="Apartment" onclick="selectChip(this, 'type')">
                                    Apartment
                                </button>
                                <button class="filter-chip" data-filter="type" data-value="House" onclick="selectChip(this, 'type')">
                                    House
                                </button>
                                <button class="filter-chip" data-filter="type" data-value="Condo" onclick="selectChip(this, 'type')">
                                    Condo
                                </button>
                            </div>
                        </div>

                        <!-- Bedrooms Filter -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="bi bi-door-open"></i>
                                Bedrooms
                            </label>
                            <div class="filter-options-grid">
                                <button class="filter-chip active" data-filter="bedrooms" data-value="all" onclick="selectChip(this, 'bedrooms')">
                                    Any
                                </button>
                                <button class="filter-chip" data-filter="bedrooms" data-value="1" onclick="selectChip(this, 'bedrooms')">
                                    1
                                </button>
                                <button class="filter-chip" data-filter="bedrooms" data-value="2" onclick="selectChip(this, 'bedrooms')">
                                    2
                                </button>
                                <button class="filter-chip" data-filter="bedrooms" data-value="3" onclick="selectChip(this, 'bedrooms')">
                                    3
                                </button>
                                <button class="filter-chip" data-filter="bedrooms" data-value="4" onclick="selectChip(this, 'bedrooms')">
                                    4
                                </button>
                                <button class="filter-chip" data-filter="bedrooms" data-value="5+" onclick="selectChip(this, 'bedrooms')">
                                    5+
                                </button>
                            </div>
                        </div>

                        <!-- Bathrooms Filter -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="bi bi-droplet"></i>
                                Bathrooms
                            </label>
                            <div class="filter-options-grid">
                                <button class="filter-chip active" data-filter="bathrooms" data-value="all" onclick="selectChip(this, 'bathrooms')">
                                    Any
                                </button>
                                <button class="filter-chip" data-filter="bathrooms" data-value="1" onclick="selectChip(this, 'bathrooms')">
                                    1
                                </button>
                                <button class="filter-chip" data-filter="bathrooms" data-value="2" onclick="selectChip(this, 'bathrooms')">
                                    2
                                </button>
                                <button class="filter-chip" data-filter="bathrooms" data-value="3+" onclick="selectChip(this, 'bathrooms')">
                                    3+
                                </button>
                            </div>
                        </div>

                        <!-- Price Range Filter -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="bi bi-currency-dollar"></i>
                                Price Range
                            </label>
                            <div class="price-range-container">
                                <div class="price-inputs">
                                    <div class="price-input-group">
                                        <span class="currency">₱</span>
                                        <input type="text" 
                                               id="minPrice" 
                                               class="price-input" 
                                               placeholder="Min"
                                               oninput="formatPriceInput(this); applyFilters()">
                                    </div>
                                    <span class="price-separator">—</span>
                                    <div class="price-input-group">
                                        <span class="currency">₱</span>
                                        <input type="text" 
                                               id="maxPrice" 
                                               class="price-input" 
                                               placeholder="Max"
                                               oninput="formatPriceInput(this); applyFilters()">
                                    </div>
                                </div>
                                
                                <!-- Quick Price Filters -->
                                <div class="filter-options-grid mt-3">
                                    <button class="filter-chip" onclick="setQuickPrice(0, 500000)">
                                        Under 500K
                                    </button>
                                    <button class="filter-chip" onclick="setQuickPrice(500000, 1000000)">
                                        500K - 1M
                                    </button>
                                    <button class="filter-chip" onclick="setQuickPrice(1000000, 5000000)">
                                        1M - 5M
                                    </button>
                                    <button class="filter-chip" onclick="setQuickPrice(5000000, null)">
                                        5M+
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Size Filter -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="bi bi-arrows-fullscreen"></i>
                                Property Size (sqft)
                            </label>
                            <div class="price-inputs">
                                <input type="number" 
                                       id="minSize" 
                                       class="filter-input" 
                                       placeholder="Min sqft"
                                       style="padding: 0.75rem 1rem;"
                                       oninput="applyFilters()">
                                <span class="price-separator">—</span>
                                <input type="number" 
                                       id="maxSize" 
                                       class="filter-input" 
                                       placeholder="Max sqft"
                                       style="padding: 0.75rem 1rem;"
                                       oninput="applyFilters()">
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="filter-actions">
                            <button class="btn-filter-clear" onclick="clearAllFilters()">
                                <i class="bi bi-x-circle"></i>
                                Clear All
                            </button>
                            <button class="btn-filter-apply d-lg-none" data-bs-dismiss="offcanvas">
                                <i class="bi bi-check-circle"></i>
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="content-with-sidebar">
                <div id="propertiesContainer">
                    <!-- Properties will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Property Details Modal -->
<div class="modal fade" id="propertyDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content property-modal-content">
            <div class="modal-body p-0 position-relative">

                <!-- Close button (visible) -->
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>

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
                            <button class="btn btn-primary" id="modalChatBtn">
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
                                        <strong id="bookingPropertyAgent">—</strong>
                                        
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
                                        Preferred Date
                                    </label>
                                    <input type="date" id="bookingDate" name="booking_date" class="form-control">
                                    <div class="form-text small">Optional — agent may reschedule.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="bookingTime" class="form-label">
                                        <i class="bi bi-clock me-1"></i>
                                        Preferred Time
                                    </label>
                                    <div class="d-flex gap-2">
                                        <input type="text" id="bookingTime" name="booking_time" class="form-control" placeholder="hh:mm" aria-label="Time (hh:mm)">
                                        <select id="bookingTimeAmpm" name="booking_time_ampm" class="form-select" aria-label="AM or PM">
                                            <option value="AM">AM</option>
                                            <option value="PM">PM</option>
                                        </select>
                                    </div>
                                    <div class="form-text small">Optional — enter hour and minutes (e.g. 10:30).</div>
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
  // Use site_url so index.php (index_page) is included when needed
  window.getUserUrlBase    = <?= json_encode(site_url('users/getUser')) ?>;   // used as `${window.getUserUrlBase}/{id}`
    window.getAgeUrlBase     = <?= json_encode(site_url('users/getAge')) ?>;
  window.propertiesUrl     = <?= json_encode(site_url('properties/all')) ?>;
  window.propertiesViewUrl = <?= json_encode(site_url('properties/view')) ?>; // used as `${propertiesViewUrl}/{id}`
  window.bookingCreateUrl  = <?= json_encode(site_url('bookings/create')) ?>;
  window.bookingCancelUrl  = <?= json_encode(site_url('bookings/cancel')) ?>;
  window.myBookingsUrl     = <?= json_encode(site_url('bookings/mine')) ?>;
  // If CSRF is enabled in CI:
  window.csrfName = <?= json_encode(csrf_token()) ?>;
  window.csrfHash = <?= json_encode(csrf_hash()) ?>;
</script>



<script src="<?= base_url("bootstrap5/js/bootstrap.bundle.min.js") ?>"></script>
<script src="<?= base_url("assets/js/client.js") ?>"></script>

</div>
</body>

</html>