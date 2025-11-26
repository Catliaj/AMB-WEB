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



        <!-- Main Content -->
        <main id="mainContent">
            <!-- Hero Section -->
            <section class="hero">
                <div class="hero-content">
                    <h2>WELCOME TO AMB CORPORATION</h2>
                    <h1>Find Your Dream Home</h1>
                    <p>Your all-in-one real estate solution, where trusted brokers guide you every step of the way. 
                        Discover modern and stunning neighborhoods designed for comfort, convenience, and vibrant living. 
                        With a seamless buying journey and personalized support, we help turn your dream home into a reality—making the process as enjoyable as the outcome.</p>
                    <div class="search-box">
                        <input type="text" placeholder="Search property...">
                        <button><i class="bi bi-search"></i></button>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="<?= base_url('assets/img/house.png') ?>" alt="Dream Home">

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
                        <a class="btn btn-primary" href="/users/clientbrowse">View All</a>
                    </div>

                    <div class="row g-4">
                        <?php if (!empty($topViewed) && is_array($topViewed)): ?>
                            <?php foreach ($topViewed as $prop): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="property-card" onclick="window.location='/property/view/<?= esc($prop['PropertyID']) ?>'">
                                        <div class="property-image">
                                            <img src="<?= esc($prop['PropertyImage'] ? base_url('uploads/properties/' . $prop['PropertyImage']) : base_url('uploads/properties/no-image.jpg')) ?>"
                                                alt="<?= esc($prop['Title']) ?>" loading="lazy">
                                            <button class="property-favorite" onclick="event.stopPropagation(); toggleFavorite(<?= intval($prop['PropertyID']) ?>)">
                                                <i class="bi bi-heart fs-5"></i>
                                            </button>
                                            <span class="property-type-badge">For Sale</span>
                                        </div>
                                        <div class="card-body">
                                            <h3 class="h5 mb-2"><?= esc($prop['Title']) ?></h3>
                                            <div class="d-flex align-items-center mb-3 text-muted">
                                                <i class="bi bi-geo-alt text-accent me-2"></i>
                                                <small><?= esc($prop['Location']) ?></small>
                                            </div>
                                            <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-house-door me-1 text-muted"></i>
                                                    <small class="text-muted"><?= esc($prop['Bedrooms'] ?? '-') ?></small>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-droplet me-1 text-muted"></i>
                                                    <small class="text-muted"><?= esc($prop['Bathrooms'] ?? '-') ?></small>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-cash-stack me-1 text-muted"></i>
                                                    <small class="text-muted"><?= esc(number_format($prop['Price'] ?? 0)) ?></small>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="property-price">PHP <?= esc(number_format($prop['Price'] ?? 0)) ?></span>
                                                <button class="btn btn-primary btn-sm" onclick="event.stopPropagation(); window.location='/property/view/<?= esc($prop['PropertyID']) ?>'">View Details</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Fallback: keep the three static featured cards if no data -->
                            <div class="col-md-6 col-lg-4">
                                <div class="property-card" onclick="alert('Property details coming soon!')">
                                    <div class="property-image">
                                        <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080"
                                            alt="Modern Family House" loading="lazy">
                                        <button class="property-favorite" onclick="event.stopPropagation(); toggleFavorite(1)">
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
                                            <button class="btn btn-primary btn-sm" onclick="event.stopPropagation(); alert('View details coming soon!')">View Details</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- second fallback card -->
                            <div class="col-md-6 col-lg-4">
                                <div class="property-card" onclick="alert('Property details coming soon!')">
                                    <div class="property-image">
                                        <img src="https://images.unsplash.com/photo-1715985160020-d8cd6fdc8ba9?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080"
                                            alt="Luxury Penthouse" loading="lazy">
                                        <button class="property-favorite" onclick="event.stopPropagation(); toggleFavorite(2)">
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
                                            <button class="btn btn-primary btn-sm" onclick="event.stopPropagation(); alert('View details coming soon!')">View Details</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- third fallback card -->
                            <div class="col-md-6 col-lg-4">
                                <div class="property-card" onclick="alert('Property details coming soon!')">
                                    <div class="property-image">
                                        <img src="https://images.unsplash.com/photo-1591268193431-c86baf208255?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&q=80&w=1080"
                                            alt="Garden Villa Estate" loading="lazy">
                                        <button class="property-favorite" onclick="event.stopPropagation(); toggleFavorite(3)">
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
                                            <button class="btn btn-primary btn-sm" onclick="event.stopPropagation(); alert('View details coming soon!')">View Details</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div> <!-- row -->
                </div>
            </section>
        </main>

        <!-- Chat Floating Action Button -->
    <a href="/users/chat" class="chat-fab" id="chatButton">
        <i class="bi bi-chat-dots-fill fs-4"></i>
    </a>


        <script src="<?= base_url("bootstrap5/js/bootstrap.bundle.min.js")?>"></script>
        <script src="<?= base_url("assets/js/client.js")?>"></script>
        <script>
            function toggleHeroImage() {
    const hero = document.querySelector('.hero');
    const heroImage = document.querySelector('.hero-image');

    const isMaximized = window.outerWidth >= screen.availWidth && window.outerHeight >= screen.availHeight * 0.92;

    if (isMaximized) {
        heroImage.style.display = 'block';
        hero.classList.remove('center-content');
    } else {
        heroImage.style.display = 'none';
        hero.classList.add('center-content');
    }
}

window.addEventListener('load', toggleHeroImage);
window.addEventListener('resize', toggleHeroImage);

        </script>
    </div>
</body>

</html>
