<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>My Bookings - ABM</title>

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
                                    <p class="text-muted small mb-0">Total Bookings</p>
                                    <h3 class="h4 mb-0">4</h3>
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
                                    <p class="text-muted small mb-0">Confirmed</p>
                                    <h3 class="h4 mb-0">2</h3>
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
                                    <p class="text-muted small mb-0">Upcoming</p>
                                    <h3 class="h4 mb-0">2</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="bookingsList">
                <!-- Bookings are rendered via client.js from bookingsData -->
            </div>
        </div>
    </main>

    <!-- Chat -->
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
    </div>

    <button class="chat-fab" id="chatButton" onclick="toggleChat()">
        <i class="bi bi-chat-dots-fill fs-4"></i>
    </button>

    <script src="<?= base_url("bootstrap5/js/bootstrap.bundle.min.js")?>"></script>
    <script src="<?= base_url("assets/js/client.js")?>"></script>
    </div>
</body>

</html>
