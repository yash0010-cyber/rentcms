<section class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">My Properties</h2>
        <a class="btn btn-primary" href="<?= htmlspecialchars($baseUrl); ?>/owner/properties/create">Add House</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>City</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($properties as $property): ?>
                        <tr>
                            <td><?= htmlspecialchars($property['title']); ?></td>
                            <td><?= htmlspecialchars($property['city']); ?></td>
                            <td>$<?= number_format((float) $property['price_per_month'], 0); ?></td>
                            <td><?= htmlspecialchars($property['status']); ?></td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary" href="<?= htmlspecialchars($baseUrl); ?>/owner/properties/edit?id=<?= (int) $property['id']; ?>">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($properties)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No properties yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
