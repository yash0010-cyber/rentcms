<section class="container py-5">
    <h2 class="fw-bold mb-4">Tenant Dashboard</h2>
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <p class="mb-2">Browse new listings or review your current bookings.</p>
            <a class="btn btn-outline-primary" href="<?= htmlspecialchars($baseUrl); ?>/properties">Find a Property</a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <strong>My Bookings</strong>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Property</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?= htmlspecialchars($booking['title']); ?></td>
                            <td><?= htmlspecialchars($booking['check_in_date']); ?></td>
                            <td><?= htmlspecialchars($booking['check_out_date']); ?></td>
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
