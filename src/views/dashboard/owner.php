<section class="container py-5">
    <h2 class="fw-bold mb-4">Owner Dashboard</h2>
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-3">
                <h6 class="text-muted">My Properties</h6>
                <h3 class="fw-bold"><?= count($properties); ?></h3>
                <a class="btn btn-sm btn-outline-primary" href="<?= htmlspecialchars($baseUrl); ?>/owner/properties">Manage Properties</a>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-3">
                <h6 class="text-muted">Bookings</h6>
                <h3 class="fw-bold"><?= count($bookings); ?></h3>
                <a class="btn btn-sm btn-outline-primary" href="<?= htmlspecialchars($baseUrl); ?>/bookings">View Bookings</a>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <strong>Recent Bookings</strong>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Property</th>
                        <th>Tenant</th>
                        <th>Check-in</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?= htmlspecialchars($booking['title']); ?></td>
                            <td><?= htmlspecialchars($booking['tenant_name'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($booking['check_in_date']); ?></td>
                            <td><?= htmlspecialchars($booking['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No bookings yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
