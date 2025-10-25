<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ABM HomePage</title>

    <!-- Bootstrap CSS -->
    <link href="<?= base_url("bootstrap5/css/bootstrap.min.css")?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url("assets/styles/clientstyle.css")?>">
</head>

<body>
    <div class="site-wrapper">
        <!-- Navigation -->

        <body class="home">
            <nav class="navbar navbar-expand-lg navbar-light sticky-top custom-nav" id="mainNav">
                <div class="container-fluid">
                    <!-- Logo -->
                    <a class="navbar-brand d-flex align-items-center" href="homepage.html">
                        <img src="images/amb_logo.png" alt="AMB Logo" height="40" class="me-2">
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
                            <li class="nav-item">
                                <a class="nav-link nav-link-custom" href="/users/chat">Chats</a>
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



        <!-- Main Content -->
        <main id="mainContent">
            <!-- Hero Section -->
            <section class="hero">
                <div class="hero-content">
                    <h2>WELCOME TO AMB CORPORATION</h2>
                    <h1>Find Your Dream Home</h1>
                    <p>Your all-in-one solution, where trusted brokers, modern, stunning neighborhoods, and a seamless
                        buying journey come together to turn your dream home into reality.</p>
                    <div class="search-box">
                        <input type="text" placeholder="Search property...">
                        <button><i class="bi bi-search"></i></button>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="images/house.png" alt="Dream Home">
                </div>
            </section>

            <!-- Second Section -->
            <section class="second-section">
                <div class="modern-home-image">
                    <img src="https://images.unsplash.com/photo-1613490493576-7fde63acd811?w=600&h=400&fit=crop"
                        alt="Modern Home">
                </div>

                <div class="content-right">
                    <h1>Find Your Dream Home</h1>
                    <p class="subtitle">
                        Explore our curated selection of exquisite properties meticulously tailored to your unique dream
                        home.
                    </p>

                    <h2 class="section-subtitle">We Help You To Find Your Dream Home</h2>
                    <p class="section-description">
                        From cozy suburban retreats to sleek urban dwellings, our dedicated team guides you through
                        every step of the journey,
                        ensuring your dream home becomes a reality.
                    </p>

                    <div class="stats">
                        <div class="stat-item">
                            <h3>8K+</h3>
                            <p>Properties Available</p>
                        </div>
                        <div class="stat-item">
                            <h3>6K+</h3>
                            <p>Happy Sold</p>
                        </div>
                        <div class="stat-item">
                            <h3>2K+</h3>
                            <p>Satisfied Agents</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Why Choose Us -->
            <section class="why-choose">
                <h2>Why Choose Us</h2>
                <p style="margin-bottom: 0;">Elevating Your Home Buying Experience with Expertise, Integrity,</p>
                <p style="margin-bottom: 30px;">and Unmatched Personalized Service</p>

                <div class="features">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <h3>Expert Guidance</h3>
                        <p>Benefit from our team's seasoned expertise for a smooth buying journey.</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <h3>Personalized Service</h3>
                        <p>Your dream home journey begins with us. Our services are tailored to your unique needs,
                            making your journey stress-free.</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-clipboard-check"></i>
                        </div>
                        <h3>Transparent Process</h3>
                        <p>Stay informed with our clear and honest approach to buying and selling.</p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-headset"></i>
                        </div>
                        <h3>Exceptional Support</h3>
                        <p>Benefit from our unwavering commitment to exceptional service, offering peace of mind with
                            our responsive and attentive support team.</p>
                    </div>
                </div>
            </section>

            <!-- Featured Properties -->
            <section class="py-5 bg-light">
                <div class="container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="h3 fw-medium mb-1">Featured Properties</h2>
                            <p class="text-muted mb-0">Handpicked properties just for you</p>
                        </div>
                        <a class="btn btn-primary" href="browse.html">View All</a>
                    </div>

                    <div class="row g-4">
                        <!-- We'll display a few featured cards statically or let JS handle favorites; keep cards simple -->
                        <div class="col-md-6 col-lg-4">
                            <div class="property-card" onclick="alert('Property details coming soon!')">
                                <div class="property-image">
                                    <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080"
                                        alt="Modern Family House" loading="lazy">
                                    <button class="property-favorite"
                                        onclick="event.stopPropagation(); toggleFavorite(1)">
                                        <i class="bi bi-heart fs-5"></i>
                                    </button>
                                    <span class="property-type-badge">For Sale</span>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 mb-2">Modern Family House</h3>
                                    <div class="d-flex align-items-center mb-3 text-muted">
                                        <i class="bi bi-geo-alt text-accent me-2"></i>
                                        <small>Beverly Hills, CA</small>
                                    </div>
                                    <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-house-door me-1 text-muted"></i>
                                            <small class="text-muted">4</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-droplet me-1 text-muted"></i>
                                            <small class="text-muted">3</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-arrows-fullscreen me-1 text-muted"></i>
                                            <small class="text-muted">3200 sqft</small>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="property-price">$2,500,000</span>
                                        <button class="btn btn-primary btn-sm"
                                            onclick="event.stopPropagation(); alert('View details coming soon!')">View
                                            Details</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Add two more featured cards (static) -->
                        <div class="col-md-6 col-lg-4">
                            <div class="property-card" onclick="alert('Property details coming soon!')">
                                <div class="property-image">
                                    <img src="https://images.unsplash.com/photo-1715985160020-d8cd6fdc8ba9?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080"
                                        alt="Luxury Penthouse" loading="lazy">
                                    <button class="property-favorite"
                                        onclick="event.stopPropagation(); toggleFavorite(2)">
                                        <i class="bi bi-heart fs-5"></i>
                                    </button>
                                    <span class="property-type-badge">For Rent</span>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 mb-2">Luxury Penthouse</h3>
                                    <div class="d-flex align-items-center mb-3 text-muted">
                                        <i class="bi bi-geo-alt text-accent me-2"></i>
                                        <small>Manhattan, NY</small>
                                    </div>
                                    <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-house-door me-1 text-muted"></i>
                                            <small class="text-muted">3</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-droplet me-1 text-muted"></i>
                                            <small class="text-muted">2</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-arrows-fullscreen me-1 text-muted"></i>
                                            <small class="text-muted">2400 sqft</small>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="property-price">$12,000/mo</span>
                                        <button class="btn btn-primary btn-sm"
                                            onclick="event.stopPropagation(); alert('View details coming soon!')">View
                                            Details</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="property-card" onclick="alert('Property details coming soon!')">
                                <div class="property-image">
                                    <img src="https://images.unsplash.com/photo-1591268193431-c86baf208255?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080"
                                        alt="Garden Villa Estate" loading="lazy">
                                    <button class="property-favorite"
                                        onclick="event.stopPropagation(); toggleFavorite(3)">
                                        <i class="bi bi-heart fs-5"></i>
                                    </button>
                                    <span class="property-type-badge">For Sale</span>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 mb-2">Garden Villa Estate</h3>
                                    <div class="d-flex align-items-center mb-3 text-muted">
                                        <i class="bi bi-geo-alt text-accent me-2"></i>
                                        <small>Miami, FL</small>
                                    </div>
                                    <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-house-door me-1 text-muted"></i>
                                            <small class="text-muted">5</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-droplet me-1 text-muted"></i>
                                            <small class="text-muted">4</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-arrows-fullscreen me-1 text-muted"></i>
                                            <small class="text-muted">4800 sqft</small>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="property-price">$4,200,000</span>
                                        <button class="btn btn-primary btn-sm"
                                            onclick="event.stopPropagation(); alert('View details coming soon!')">View
                                            Details</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- row -->
                </div>
            </section>
        </main>

        <!-- Floating Chat -->
        <div class="chat-window" id="chatWindow">
            <div class="chat-header">
                <div class="d-flex align-items-center">
                    <div class="chat-avatar">
                        <i class="bi bi-chat-dots-fill text-white"></i>
                    </div>
                    <div class="ms-3">
                        <p class="mb-0 fw-medium text-white small">Support Team</p>
                        <div class="d-flex align-items-center">
                            <span class="status-dot"></span>
                            <span class="text-white small ms-1" style="opacity: 0.9;">Online</span>
                        </div>
                    </div>
                </div>
                <button class="btn-close btn-close-white" onclick="toggleChat()"></button>
            </div>
            <div class="chat-messages" id="chatMessages"></div>
            <div class="chat-input-area">
                <input type="text" class="form-control" id="chatInput" placeholder="Type your message..."
                    onkeypress="handleChatKeyPress(event)">
                <button class="btn btn-primary" onclick="sendMessage()">
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>
            <button class="btn-close btn-close-white" onclick="toggleChat()"></button>
        </div>

        <!-- Chat Button -->
        <button class="chat-fab" id="chatButton" onclick="toggleChat()">
            <i class="bi bi-chat-dots-fill fs-4"></i>
        </button>

        <script src="<?= base_url("bootstrap5/js/bootstrap.bundle.min.js")?>"></script>
        <script src="<?= base_url("assets/js/client.js")?>"></script>
    </div>
</body>

</html>
