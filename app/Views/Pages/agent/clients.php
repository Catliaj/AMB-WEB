<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agent Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>
<body>
  <nav class="navbar navbar-light fixed-top shadow-sm bg-white border-bottom">
    <div class="container-fluid d-flex justify-content-between align-items-center px-4">
      <div class="d-flex align-items-center">
        <h3 class="mb-0 text-secondary fw-semibold me-4">Agent</h3>
      </div>

      <ul class="nav nav-tabs">
        <li class="nav-item"><a class="nav-link" href="dashboard.html">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link active" href="clients.html">Clients</a></li>
        <li class="nav-item"><a class="nav-link" href="bookings.html">Bookings</a></li>
        <li class="nav-item"><a class="nav-link" href="properties.html">Properties</a></li>
      </ul>

      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-primary btn-sm">View Profile</button>
      </div>
    </div>
  </nav>
          <br><br>

  <!-- Animated Page Content -->
  <div class="frame-content mt-5 p-4 animate__animated animate__fadeInUp">
    <div class="card p-4 border-0 shadow-sm">
      <h4>Manage Clients</h4>
      <p class="text-muted">Add, edit, or remove clients.</p>
      <button class="btn btn-success">Add Client</button>
    </div>
  </div>

  <!-- Animation CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</body>
</html>
