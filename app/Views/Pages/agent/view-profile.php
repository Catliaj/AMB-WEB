<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/styles/agentStyle.css')?>">
</head>
<body style="background: linear-gradient(to right, #e8f5e9, #e3f2fd); min-height: 100vh;">

  <div class="container mt-5">
    <div class="card shadow-lg p-4 mx-auto" style="max-width: 600px; border-radius: 15px;">
      <div class="text-center mb-4">
        <img src="https://randomuser.me/api/portraits/men/45.jpg" class="rounded-circle" width="120" height="120" alt="Admin">
        <h4 class="mt-3">John Smith</h4>
        <p class="text-muted">Administrator</p>
      </div>

      <form>
        <div class="mb-3">
          <label class="form-label fw-bold">Full Name</label>
          <input type="text" class="form-control" value="John Smith">
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Email</label>
          <input type="email" class="form-control" value="johnsmith@agencydomain.com">
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Phone Number</label>
          <input type="text" class="form-control" value="+63 912 345 6789">
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Password</label>
          <input type="password" class="form-control" placeholder="Enter new password">
        </div>

        <div class="d-flex justify-content-between">
          <a href="index.html" class="btn btn-outline-secondary">Back to Dashboard</a>
          <button type="submit" class="btn btn-success">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
