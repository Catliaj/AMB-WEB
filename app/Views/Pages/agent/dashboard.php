<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agent Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/styles/agentStyle.css')?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>
<body>

  <div class="container-fluid">
  
  <div class="row">
    
    <!-- ✅ NAVBAR -->
    <div class="col-12">
      <nav class="navbar navbar-light fixed-top shadow-sm bg-white border-bottom">
        <div class="container-fluid d-flex justify-content-between align-items-center px-4">
          <div class="d-flex align-items-center">
            <h3 class="mb-0 text-secondary fw-semibold me-4">Agent</h3>
          </div>

          <ul class="nav nav-tabs border-0 flex-nowrap" id="dashboardTabs">
            <li class="nav-item"><a class="nav-link active" href="dashboard.html">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="clients.html">Clients</a></li>
            <li class="nav-item"><a class="nav-link" href="bookings.html">Bookings</a></li>
            <li class="nav-item"><a class="nav-link" href="properties.html">Properties</a></li>
          </ul>

          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-primary btn-sm">View Profile</button>
          </div>
        </div>
      </nav>
    </div>
  </div>
            <br><br>
    <!-- ✅ MAIN CONTENT -->
    <div class="container-fluid mt-5 pt-4">
      <div class="row g-3">
        <!-- Total Sales -->
        <div class="col-md-6">
          <div class="card p-4 shadow-sm animate__animated animate__fadeIn">
            <h6 class="text-muted mb-2">Total Sales</h6>
            <div class="fs-4 fw-bold text-dark">$45,678.90</div>
            <small class="text-success">+20% month over month</small>
          </div>
        </div>

        <!-- Total Clients -->
        <div class="col-md-6">
          <div class="card p-4 shadow-sm animate__animated animate__fadeIn animate__delay-1s">
            <h6 class="text-muted mb-2">Total Clients</h6>
            <div class="fs-4 fw-bold text-primary">405</div>
            <small class="text-success">+33% month over month</small>
          </div>
        </div>

        <!-- Property Views Chart -->
        <div class="col-md-8">
          <div class="card p-4 shadow-sm animate__animated animate__fadeInUp">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <h6 class="text-muted">Property Views</h6>

    <!-- 👇 Replace old dropdown with these two -->
    <div class="d-flex align-items-center gap-2">
      <!-- Year selector -->
      <div class="btn-group">
        <button id="yearBtn" class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">2024</button>
        <ul class="dropdown-menu" id="yearMenu">
          <li><button class="dropdown-item" data-year="2023">2023</button></li>
          <li><button class="dropdown-item" data-year="2024">2024</button></li>
          <li><button class="dropdown-item" data-year="2025">2025</button></li>
        </ul>
      </div>

      <!-- Chart type selector -->
      <div class="btn-group">
        <button id="typeBtn" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Line</button>
        <ul class="dropdown-menu" id="typeMenu">
          <li><button class="dropdown-item" data-type="line">Line</button></li>
          <li><button class="dropdown-item" data-type="pie">Pie</button></li>
        </ul>
      </div>
    </div>
  </div>

            <canvas id="propertyChart"></canvas>
          </div>
        </div>

        <!-- Clients List -->
        <div class="col-md-4">
          <div class="card p-3 shadow-sm h-100 animate__animated animate__fadeInRight">
            <h6 class="fw-bold mb-3">Clients</h6>
            <input type="text" id="searchClient" class="form-control form-control-sm mb-2" placeholder="Search client...">
            <div class="client-list" id="clientList">
              <div class="client-item" data-name="Helena" data-email="helena@figmasfakedomain.net" data-img="https://randomuser.me/api/portraits/women/44.jpg">
                <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Helena">
                <div><p class="fw-bold mb-0">Helena</p><p class="text-muted mb-0 small">helena@figmasfakedomain.net</p></div>
              </div>
              <div class="client-item" data-name="Daniel" data-email="daniel@figmasfakedomain.net" data-img="https://randomuser.me/api/portraits/men/21.jpg">
                <img src="https://randomuser.me/api/portraits/men/21.jpg" alt="Daniel">
                <div><p class="fw-bold mb-0">Daniel</p><p class="text-muted mb-0 small">daniel@figmasfakedomain.net</p></div>
              </div>
              <div class="client-item" data-name="Sophie" data-email="sophie@figmasfakedomain.net" data-img="https://randomuser.me/api/portraits/women/66.jpg">
                <img src="https://randomuser.me/api/portraits/women/66.jpg" alt="Sophie">
                <div><p class="fw-bold mb-0">Sophie</p><p class="text-muted mb-0 small">sophie@figmasfakedomain.net</p></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ✅ CLIENT MODAL -->
    <div class="modal fade" id="clientModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3">
          <div class="modal-header border-0">
            <h5 class="modal-title fw-bold">Client Profile</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body text-center">
            <img id="modalImg" class="rounded-circle mb-3" width="100">
            <h5 id="modalName"></h5>
            <p id="modalEmail" class="text-muted"></p>
            <a href="chat.html" class="btn btn-primary">View Chat</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ✅ SCRIPTS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
  // === Temporary data for each year (12 values for months) ===
  const yearlyData = {
    2023: [100, 150, 130, 170, 190, 210, 250, 230, 260, 280, 300, 320],
    2024: [120, 140, 160, 180, 200, 240, 270, 260, 290, 310, 330, 360],
    2025: [150, 170, 190, 220, 240, 260, 300, 310, 340, 370, 400, 420]
  };

  const monthLabels = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];

  const canvas = document.getElementById('propertyChart');
  let chartInstance = null;
  let currentYear = '2024';
  let currentType = 'line';

  // Helper: generate colors array for pie chart
  function generateColors(n) {
    const base = [
      '#198754','#0d6efd','#fd7e14','#6f42c1','#dc3545',
      '#20c997','#6610f2','#0dcaf0','#ffc107','#adb5bd',
      '#0b5ed7','#198754'
    ];
    // repeat/truncate to n
    return Array.from({length: n}, (_, i) => base[i % base.length]);
  }

  // Create chart of given type and year
  function createChart(type = 'line', year = '2024') {
    // destroy existing instance
    if (chartInstance) {
      chartInstance.destroy();
      chartInstance = null;
    }

    const data = yearlyData[year];

    if (type === 'line') {
      chartInstance = new Chart(canvas, {
        type: 'line',
        data: {
          labels: monthLabels,
          datasets: [{
            label: `Property Views ${year}`,
            data: data,
            fill: true,
            borderColor: "#198754",
            backgroundColor: "rgba(25,135,84,0.12)",
            tension: 0.3,
            pointRadius: 4,
            pointBackgroundColor: "#198754"
          }]
        },
        options: {
          responsive: true,
          animation: { duration: 800, easing: 'easeInOutCubic' },
          plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
          scales: { y: { beginAtZero: true, ticks: { stepSize: 50 } } }
        }
      });
    } else if (type === 'pie') {
      // For pie we can show 12 slices (one per month)
      chartInstance = new Chart(canvas, {
        type: 'pie',
        data: {
          labels: monthLabels,
          datasets: [{
            label: `Property Views ${year}`,
            data: data,
            backgroundColor: generateColors(data.length),
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          animation: { duration: 800, easing: 'easeInOutCubic' },
          plugins: { legend: { position: 'right' } }
        }
      });
    }
  }

  // Fade helper to smooth switching
  function animateChartSwitch(newType, newYear) {
    // short fade out
    canvas.style.transition = 'opacity 250ms ease';
    canvas.style.opacity = 0;
    setTimeout(() => {
      createChart(newType, newYear);
      // fade in
      canvas.style.opacity = 1;
    }, 300);
  }

  // Initialize with defaults
  createChart(currentType, currentYear);

  // Year menu interaction
  document.getElementById('yearMenu').querySelectorAll('[data-year]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const y = e.currentTarget.getAttribute('data-year');
      currentYear = y;
      document.getElementById('yearBtn').textContent = y;
      animateChartSwitch(currentType, currentYear);
    });
  });

  // Chart type menu interaction
  document.getElementById('typeMenu').querySelectorAll('[data-type]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const t = e.currentTarget.getAttribute('data-type');
      currentType = t;
      document.getElementById('typeBtn').textContent = t.charAt(0).toUpperCase() + t.slice(1);
      animateChartSwitch(currentType, currentYear);
    });
  });

  // Client search filter (kept as before)
  document.getElementById("searchClient")?.addEventListener("keyup", function () {
    const searchValue = this.value.toLowerCase();
    document.querySelectorAll(".client-item").forEach(item => {
      const name = item.querySelector(".fw-bold").textContent.toLowerCase();
      item.style.display = name.includes(searchValue) ? "flex" : "none";
    });
  });

  // Client modal popup (kept as before)
  document.querySelectorAll(".client-item").forEach(client => {
    client.addEventListener("click", function () {
      document.getElementById("modalName").textContent = this.dataset.name;
      document.getElementById("modalEmail").textContent = this.dataset.email;
      document.getElementById("modalImg").src = this.dataset.img;
      new bootstrap.Modal(document.getElementById("clientModal")).show();
    });
  });
</script>


</body>
</html>
