<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agent Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= base_url("bootstrap5/css/bootstrap.min.css")?>" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url("assets/styles/agenStyle.css")?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>
<body>
 
  <nav class="navbar navbar-light fixed-top shadow-sm bg-white border-bottom">
    <div class="container-fluid d-flex justify-content-between align-items-center px-4">
      <h3 class="mb-0 text-secondary fw-semibold">Agent Dashboard</h3>
      <ul class="nav nav-tabs border-0 flex-nowrap">
        <li class="nav-item"><a class="nav-link active" href="/users/agentHomepage">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="/users/agentclients">Clients</a></li>
        <li class="nav-item"><a class="nav-link" href="/users/agentbookings">Bookings</a></li>
        <li class="nav-item"><a class="nav-link" href="/users/agentproperties">Properties</a></li>
        <li class="nav-item"><a class="nav-link" href="/users/agentchat">Chat</a></li>
      </ul>
      <a href="/users/agentprofile"> <button class="btn btn-outline-primary btn-sm"> Profile</button></a>
    </div>
  </nav>
<br>

  <div class="container-fluid mt-5 pt-4">
    <div class="row g-3">

      <!-- Overall Views -->
      <div class="col-md-3 col-lg-3">
        <div class="card p-3 shadow-sm animate__animated animate__fadeIn h-100">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="text-muted mb-1">Overall Views</h6>
              <div class="fs-5 fw-bold text-success"><?= $getTotalViewsByAgent?></div>
            </div>
            <div class="bg-light rounded-circle p-2">
              <i class="bi bi-eye-fill text-success fs-4"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Total Clients (smaller layout) -->
      <div class="col-md-3 col-lg-3">
        <div class="card p-3 shadow-sm animate__animated animate__fadeIn animate__delay-1s h-100">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="text-muted mb-1">Total Clients</h6>
              <div class="fs-5 fw-bold text-primary"> <?= $totalClientHandle ?></div>
            </div>
            <div class="bg-light rounded-circle p-2">
              <i class="bi bi-people-fill text-primary fs-4"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Most Viewed Property -->
      <div class="col-md-6 col-lg-6">
        <div class="card p-4 shadow-sm animate__animated animate__fadeIn animate__delay-2s h-100">
          <h6 class="text-muted mb-3">Most Viewed Property</h6>
          <?php if (!empty($mostViewed)): ?>
            <div class="d-flex align-items-center gap-4">
              <img src="<?= base_url('uploads/properties/' . ($mostViewed['PropertyImage'] ?? 'no-image.jpg')) ?>" 
     alt="<?= $mostViewed['Title'] ?? 'No Image' ?>" 
     class="rounded shadow-sm" width="160" height="100">


              <div>
                <p class="mb-1 fw-bold text-dark fs-6"><?= esc($mostViewed['Title']) ?></p>
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


      <!-- Property Views Chart (wide) -->
      <div class="col-md-9">
        <div class="card p-4 shadow-sm animate__animated animate__fadeInUp h-100">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="text-muted">Property Views</h6>
          </div>
          <canvas id="propertyChart"></canvas>
        </div>
      </div>

      <!-- Clients List -->
      <div class="col-md-3">
        <div class="card p-3 shadow-sm h-100 animate__animated animate__fadeInRight">
          <h6 class="fw-bold mb-3">Clients</h6>
          <div id="clientList"></div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script>
    // --- Chart ---
    const ctx = document.getElementById('propertyChart');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
        datasets: [{
          label: "Property Views 2025",
          data: [12,25,40,55,70,60,75,80,95,120,110,130],
          borderColor: "#198754",
          backgroundColor: "rgba(25,135,84,0.2)",
          fill: true,
          tension: 0.3
        }]
      },
      options: {
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
      }
    });

   
    const clients = <?= json_encode($clients) ?>;
    const clientList = document.getElementById("clientList");

    let html = '';
    clients.forEach(c => {
        // Fallback image if no client photo aayosin ko to pag may data
        const imgSrc = c.img && c.img.trim() !== '' ? c.img : '<?= base_url('uploads/properties/no-image.jpg') ?>';


        const fullName = c.name || (c.FirstName + ' ' + (c.MiddleName ? c.MiddleName + ' ' : '') + c.LastName) || 'No Name';

        html += `
            <div class="d-flex align-items-center gap-3 border-bottom py-2">
                <img src="${imgSrc}" class="rounded-circle" width="40" height="40">
                <div>
                    <p class="mb-0 fw-bold">${fullName}</p>
                    <p class="text-muted small mb-0">${c.email || 'No Email'}</p>
                </div>
            </div>
        `;
    });

clientList.innerHTML = html;
  </script>
</body>
</html>
