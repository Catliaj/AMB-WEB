<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agent Dashboard - ABM Property</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= base_url("bootstrap5/css/bootstrap.min.css")?>" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  
  <style>
    /* ========== CSS VARIABLES ========== */
    :root {
      --primary: #469541;
      --primary-hover: #357a34;
      --secondary: #000000;
      --accent: #2d9fa8;
      --light-bg: #d3f0ff;
      --light-accent: #c8f5d2;
      --bg-color: #ffffff;
      --text-color: #333333;
      --text-muted: #666666;
      --card-bg: #ffffff;
      --border-color: rgba(0, 0, 0, 0.1);
      --shadow: rgba(0, 0, 0, 0.1);
    }

    html[data-theme="dark"] {
      --primary: #5ab34f;
      --primary-hover: #469541;
      --secondary: #c4aee3;
      --accent: #3eb3bd;
      --light-bg: #252e42;
      --light-accent: #2d4038;
      --bg-color: #1a1f2e;
      --text-color: #f0f0f0;
      --text-muted: #a0a0a0;
      --card-bg: #252a3a;
      --border-color: rgba(255, 255, 255, 0.1);
      --shadow: rgba(0, 0, 0, 0.3);
    }

    /* ========== BODY ========== */
    body {
      background-color: var(--bg-color);
      color: var(--text-color);
      transition: background-color 0.3s, color 0.3s;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      margin: 0;
      padding: 0;
    }

    /* ========== NAVIGATION ========== */
    .navbar {
      background: linear-gradient(120deg, #d3f0ff 0%, #c8f5d2 100%);
      padding: 10px 50px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
      position: sticky;
      top: 0;
      z-index: 1030;
      transition: all 0.3s ease;
      border: none !important;
      height: 100px;
    }

    html[data-theme="dark"] .navbar {
      background: linear-gradient(120deg, #252e42 0%, #2d4038 100%);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }

    .logo-text {
      color: var(--secondary);
      font-size: 20px;
      font-weight: 500;
      letter-spacing: 3px;
      transition: color 0.3s ease;
    }

    html[data-theme="dark"] .logo-text {
      color: var(--primary);
    }

    .navbar-brand:hover .logo-text {
      color: var(--primary);
    }

    .nav-link-custom {
      color: var(--text-color) !important;
      transition: all 0.2s ease;
      padding: 0.5rem 1rem;
      border-radius: 0.5rem;
      font-size: 18px;
      font-weight: 500;
    }

    .nav-link-custom:hover {
      color: var(--primary) !important;
      background-color: transparent !important;
    }

    .nav-link-custom.active {
      color: var(--primary) !important;
      background-color: transparent !important;
    }

    .main-links .nav-item {
      margin: 0 1.5rem;
    }

    #themeToggle {
      background: none;
      border: none;
      cursor: pointer;
      padding: 0.5rem;
      transition: transform 0.3s ease;
    }

    #themeToggle:hover {
      transform: scale(1.1);
    }

    #themeToggle i {
      color: var(--text-color);
      transition: color 0.3s ease;
    }

    #themeToggle:hover i {
      color: var(--primary);
    }

    /* ========== MAIN CONTENT ========== */
    .main-content {
      padding: 2rem;
      min-height: calc(100vh - 100px);
    }

    /* ========== CARDS ========== */
    .card {
      background-color: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      box-shadow: 0 2px 10px var(--shadow);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 15px var(--shadow);
    }

    /* ========== STAT CARDS ========== */
    .stat-card {
      background-color: var(--card-bg);
      border-radius: 16px;
      padding: 1.5rem;
      border: 1px solid var(--border-color);
      box-shadow: 0 2px 10px var(--shadow);
      height: 100%;
    }

    .stat-icon {
      width: 3rem;
      height: 3rem;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
    }

    .stat-icon.success {
      background-color: rgba(25, 135, 84, 0.1);
      color: #198754;
    }

    .stat-icon.primary {
      background-color: rgba(70, 149, 65, 0.1);
      color: var(--primary);
    }

    /* Uniform card heights */
    .uniform-card {
      height: 100%;
    }

    /* ========== BUTTONS ========== */
    .btn-primary {
      background-color: var(--primary) !important;
      border-color: var(--primary) !important;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      background-color: var(--primary-hover) !important;
      border-color: var(--primary-hover) !important;
      transform: translateY(-2px);
    }

    .btn-outline-primary {
      color: var(--primary) !important;
      border-color: var(--primary) !important;
    }

    .btn-outline-primary:hover {
      background-color: var(--primary) !important;
      border-color: var(--primary) !important;
      color: white !important;
    }

    /* ========== TEXT COLORS ========== */
    .text-muted {
      color: var(--text-muted) !important;
    }

    .text-success {
      color: #198754 !important;
    }

    .text-primary {
      color: var(--primary) !important;
    }
    
    .text-dark {
      color: var(--text-color) !important;
    }
    
    /* Make all text follow theme */
    h1, h2, h3, h4, h5, h6, p, span, div {
      color: var(--text-color);
    }
    
    .fw-bold, .fw-semibold {
      color: var(--text-color) !important;
    }

    /* ========== CLIENT LIST ========== */
    .client-list-item {
      padding: 0.75rem;
      border-bottom: 1px solid var(--border-color);
      transition: background-color 0.2s ease;
      border-radius: 8px;
      margin-bottom: 0.5rem;
    }

    .client-list-item:hover {
      background-color: var(--light-bg);
    }

    .client-list-item img {
      border: 2px solid var(--border-color);
    }

    /* ========== PROPERTY IMAGE ========== */
    .property-preview-img {
      border-radius: 12px;
      box-shadow: 0 4px 12px var(--shadow);
      transition: transform 0.3s ease;
    }

    .property-preview-img:hover {
      transform: scale(1.02);
    }

    /* ========== SCROLLBAR ========== */
    ::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }

    ::-webkit-scrollbar-track {
      background: var(--bg-color);
    }

    ::-webkit-scrollbar-thumb {
      background: var(--text-muted);
      border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: var(--primary);
    }

    /* ========== RESPONSIVE ========== */
    @media (max-width: 992px) {
      .navbar {
        padding: 10px 20px;
        height: auto;
      }

      .main-links .nav-item {
        margin: 0;
        width: 100%;
      }

      .nav-link-custom {
        text-align: left;
        width: 100%;
        padding-left: 1.8rem;
      }

      .main-content {
        padding: 1rem;
      }
    }

    /* ========== CHART CONTAINER ========== */
    .chart-container {
      position: relative;
      height: 300px;
    }
    /* Chart.js theme fixes */
.chartjs-render-monitor {
    color: var(--text-color) !important;
}

canvas[class*="chartjs"] {
    color: var(--text-color) !important;
}

/* Force chart text colors */
.chartjs-tooltip {
    color: var(--text-color) !important;
}

.chartjs-tooltip-key {
    background: var(--text-color) !important;
}
  </style>
</head>

<body>
  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
      <!-- Logo -->
      <a class="navbar-brand d-flex align-items-center" href="/users/agentHomepage">
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
        <!-- Center nav links -->
        <ul class="navbar-nav mx-lg-auto text-center text-lg-start main-links">
          <li class="nav-item">
            <a class="nav-link nav-link-custom active" href="/users/agentHomepage">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link nav-link-custom" href="/users/agentclients">Clients</a>
          </li>
          <li class="nav-item">
            <a class="nav-link nav-link-custom" href="/users/agentbookings">Bookings</a>
          </li>
          <li class="nav-item">
            <a class="nav-link nav-link-custom" href="/users/agentproperties">Properties</a>
          </li>
          <li class="nav-item">
            <a class="nav-link nav-link-custom" href="/users/agentchat">Chat</a>
          </li>
          <li class="nav-item d-lg-none">
            <a class="nav-link nav-link-custom" href="/users/agentprofile">Profile</a>
          </li>
        </ul>

        <!-- Right-aligned (desktop only) -->
        <ul class="navbar-nav align-items-center d-none d-lg-flex">
          <li class="nav-item">
            <a class="nav-link nav-link-custom" href="/users/agentprofile">
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

  <!-- Main Content -->
  <div class="main-content">
    <div class="container-fluid">
      <div class="row g-4">

        <!-- Overall Views -->
        <div class="col-md-6 col-lg-3">
          <div class="stat-card animate__animated animate__fadeIn">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h6 class="text-muted mb-2">Overall Views</h6>
                <div class="fs-4 fw-bold text-success"><?= $getTotalViewsByAgent?></div>
              </div>
              <div class="stat-icon success">
                <i class="bi bi-eye-fill"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Clients -->
        <div class="col-md-6 col-lg-3">
          <div class="stat-card animate__animated animate__fadeIn animate__delay-1s">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h6 class="text-muted mb-2">Total Clients</h6>
                <div class="fs-4 fw-bold text-primary"><?= $totalClientHandle ?></div>
              </div>
              <div class="stat-icon primary">
                <i class="bi bi-people-fill"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Most Viewed Property -->
        <div class="col-md-12 col-lg-6">
          <div class="card p-4 animate__animated animate__fadeIn animate__delay-2s uniform-card">
            <h6 class="text-muted mb-3 fw-semibold">Most Viewed Property</h6>
            <?php if (!empty($mostViewed)): ?>
              <div class="d-flex align-items-center gap-4">
                <img src="<?= base_url('uploads/properties/' . ($mostViewed['PropertyImage'] ?? 'no-image.jpg')) ?>" 
                     alt="<?= $mostViewed['Title'] ?? 'No Image' ?>" 
                     class="property-preview-img" 
                     width="160" 
                     height="100"
                     style="object-fit: cover;">
                <div>
                  <p class="mb-1 fw-bold fs-6"><?= esc($mostViewed['Title']) ?></p>
                  <p class="text-muted small mb-2">
                    <?= esc($mostViewed['Location']) ?> • 
                    <?= esc($mostViewed['Bedrooms'] ?? 0) ?> Bedrooms • 
                    <?= esc($mostViewed['Parking_Spaces'] ?? 0) ?> Garage
                  </p>
                  <p class="text-success mb-0 fw-semibold">
                    <i class="bi bi-eye-fill"></i> <?= esc($mostViewed['total_views'] ?? 0) ?> views
                  </p>
                </div>
              </div>
            <?php else: ?>
              <p class="text-muted">No properties assigned yet.</p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Property Views Chart -->
        <div class="col-md-12 col-lg-9">
          <div class="card p-4 animate__animated animate__fadeInUp uniform-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="text-muted fw-semibold mb-0">Property Views</h6>
            </div>
            <div class="chart-container">
              <canvas id="propertyChart"></canvas>
            </div>
          </div>
        </div>

        <!-- Clients List -->
        <div class="col-md-12 col-lg-3">
          <div class="card p-3 animate__animated animate__fadeInRight uniform-card">
            <h6 class="fw-bold mb-3">Recent Clients</h6>
            <div id="clientList" style="max-height: 400px; overflow-y: auto;"></div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  
  <script>
    // ========== THEME TOGGLE ==========
    function toggleTheme() {
      const html = document.documentElement;
      const themeIcon = document.getElementById('themeIcon');
      const currentTheme = html.getAttribute('data-theme');
      
      if (currentTheme === 'light') {
        html.setAttribute('data-theme', 'dark');
        themeIcon.classList.remove('bi-sun-fill');
        themeIcon.classList.add('bi-moon-fill');
        localStorage.setItem('theme', 'dark');
      } else {
        html.setAttribute('data-theme', 'light');
        themeIcon.classList.remove('bi-moon-fill');
        themeIcon.classList.add('bi-sun-fill');
        localStorage.setItem('theme', 'light');
      }
      
      // Update chart colors when theme changes
      if (window.propertyChart) {
        const colors = getChartColors();
        window.propertyChart.options.scales.y.ticks.color = colors.textColor;
        window.propertyChart.options.scales.y.grid.color = colors.gridColor;
        window.propertyChart.options.scales.x.ticks.color = colors.textColor;
        window.propertyChart.update();
      }
    }

    // Load saved theme
    document.addEventListener('DOMContentLoaded', function() {
      const savedTheme = localStorage.getItem('theme') || 'light';
      const themeIcon = document.getElementById('themeIcon');
      document.documentElement.setAttribute('data-theme', savedTheme);
      
      if (savedTheme === 'dark') {
        themeIcon.classList.remove('bi-sun-fill');
        themeIcon.classList.add('bi-moon-fill');
      }
    });

    // ========== CHART ==========
    function getChartColors() {
      const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
      return {
        textColor: isDark ? '#f0f0f0' : '#333333',
        gridColor: isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.05)',
        borderColor: '#469541'
      };
    }

    function createChart() {
      const colors = getChartColors();
      const ctx = document.getElementById('propertyChart');
      
      window.propertyChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
          datasets: [{
            label: "Property Views 2025",
            data: [12,25,40,55,70,60,75,80,95,120,110,130],
            borderColor: colors.borderColor,
            backgroundColor: "rgba(70, 149, 65, 0.1)",
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointHoverRadius: 6
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { 
            legend: { 
              display: false 
            }
          },
          scales: { 
            y: { 
              beginAtZero: true,
              ticks: {
                color: colors.textColor
              },
              grid: {
                color: colors.gridColor
              }
            },
            x: {
              ticks: {
                color: colors.textColor
              },
              grid: {
                display: false
              }
            }
          }
        }
      });
    }

    createChart();

    // ========== CLIENT LIST ==========
    const clients = <?= json_encode($clients) ?>;
    const clientList = document.getElementById("clientList");

    let html = '';
    clients.forEach(c => {
      const imgSrc = c.img && c.img.trim() !== '' ? c.img : '<?= base_url('uploads/properties/no-image.jpg') ?>';
      const fullName = c.name || (c.FirstName + ' ' + (c.MiddleName ? c.MiddleName + ' ' : '') + c.LastName) || 'No Name';

      html += `
        <div class="client-list-item">
          <div class="d-flex align-items-center gap-3">
            <img src="${imgSrc}" class="rounded-circle" width="45" height="45" style="object-fit: cover;">
            <div class="flex-grow-1">
              <p class="mb-0 fw-semibold" style="font-size: 0.95rem;">${fullName}</p>
              <p class="text-muted small mb-0">${c.email || 'No Email'}</p>
            </div>
          </div>
        </div>
      `;
    });

    

    clientList.innerHTML = html;
  </script>
</body>
</html>