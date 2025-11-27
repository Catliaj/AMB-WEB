<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Database Diagnostic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Booking Database Diagnostic</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <h5>Errors:</h5>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($warnings)): ?>
            <div class="alert alert-warning">
                <h5>Warnings:</h5>
                <ul>
                    <?php foreach ($warnings as $warning): ?>
                        <li><?= esc($warning) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (empty($errors) && empty($warnings)): ?>
            <div class="alert alert-success">
                <h5>✓ All checks passed!</h5>
                <p>The booking table structure appears to be correct.</p>
            </div>
        <?php endif; ?>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5>Table Structure</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Column</th>
                            <th>Type</th>
                            <th>Null</th>
                            <th>Default</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($columns as $col): ?>
                            <tr class="<?= strtolower($col['Field']) === 'status' ? 'table-info' : '' ?>">
                                <td><strong><?= esc($col['Field']) ?></strong></td>
                                <td><?= esc($col['Type']) ?></td>
                                <td><?= esc($col['Null']) ?></td>
                                <td><?= esc($col['Default'] ?? 'NULL') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5>Update Test</h5>
            </div>
            <div class="card-body">
                <p><strong>Result:</strong> <?= esc($testResult) ?></p>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5>Booking Statistics</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats as $stat): ?>
                            <tr>
                                <td><?= esc($stat['Status']) ?></td>
                                <td><?= esc($stat['count']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5>Recent Bookings</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>UserID</th>
                            <th>PropertyID</th>
                            <th>Status</th>
                            <th>Booking Date</th>
                            <th>Updated At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentBookings as $booking): ?>
                            <tr>
                                <td><?= esc($booking['bookingID']) ?></td>
                                <td><?= esc($booking['UserID']) ?></td>
                                <td><?= esc($booking['PropertyID']) ?></td>
                                <td><span class="badge bg-<?= strtolower($booking['Status']) === 'scheduled' ? 'success' : (strtolower($booking['Status']) === 'pending' ? 'warning' : 'secondary') ?>"><?= esc($booking['Status']) ?></span></td>
                                <td><?= esc($booking['BookingDate'] ?? 'N/A') ?></td>
                                <td><?= esc($booking['updated_at'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5>Fix Database</h5>
            </div>
            <div class="card-body">
                <p>Click the button below to automatically fix common database issues:</p>
                <button id="fixBtn" class="btn btn-primary">Apply Fixes</button>
                <div id="fixResult" class="mt-3"></div>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="<?= base_url() ?>" class="btn btn-secondary">Back to Home</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('fixBtn').addEventListener('click', async function() {
            const btn = this;
            const resultDiv = document.getElementById('fixResult');
            btn.disabled = true;
            btn.textContent = 'Applying fixes...';
            resultDiv.innerHTML = '<div class="alert alert-info">Applying fixes...</div>';
            
            try {
                const response = await fetch('<?= site_url('diagnostic/fixBooking') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    resultDiv.innerHTML = '<div class="alert alert-success"><h6>Fixes Applied:</h6><ul>' + 
                        data.fixes.map(f => '<li>' + f + '</li>').join('') + 
                        '</ul></div>';
                    setTimeout(() => location.reload(), 2000);
                } else {
                    resultDiv.innerHTML = '<div class="alert alert-danger">Error: ' + (data.error || 'Unknown error') + '</div>';
                }
            } catch (error) {
                resultDiv.innerHTML = '<div class="alert alert-danger">Error: ' + error.message + '</div>';
            } finally {
                btn.disabled = false;
                btn.textContent = 'Apply Fixes';
            }
        });
    </script>
</body>
</html>

