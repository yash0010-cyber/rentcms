<section class="container py-5">
    <h2 class="fw-bold mb-4">Properties</h2>
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>City</th>
                        <th>Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($properties as $property): ?>
                        <tr>
                            <td><?= htmlspecialchars($property['title']); ?></td>
                            <td><?= htmlspecialchars($property['address']); ?></td>
                            <td><?= htmlspecialchars($property['price_label']); ?></td>
                            <td><?= htmlspecialchars($property['deal_type']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($properties)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No properties found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
