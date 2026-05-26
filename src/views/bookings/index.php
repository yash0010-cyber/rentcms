<section class="container py-5">
    <h2 class="fw-bold mb-4">Bookings</h2>
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Property</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Status</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?= htmlspecialchars($booking['title']); ?></td>
                            <td><?= htmlspecialchars($booking['check_in_date']); ?></td>
                            <td><?= htmlspecialchars($booking['check_out_date']); ?></td>
                            <td><?= htmlspecialchars($booking['status']); ?></td>
                            <td>$<?= number_format((float) $booking['total_price'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No bookings yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
