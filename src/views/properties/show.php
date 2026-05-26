<section class="container py-5">
    <div class="row g-4">
        <div class="col-lg-7">
            <img src="<?= htmlspecialchars($property['image']); ?>" class="img-fluid rounded shadow-sm" alt="Property image">
        </div>
        <div class="col-lg-5">
            <h2 class="fw-bold mb-2"><?= htmlspecialchars($property['title']); ?></h2>
            <p class="text-muted mb-2">
                <i class="bi bi-geo-alt-fill text-success me-1"></i>
                <?= htmlspecialchars($property['address']); ?>
            </p>
            <p class="mb-3"><?= htmlspecialchars($property['description'] ?: 'No description provided.'); ?></p>
            <div class="d-flex flex-wrap gap-3 text-muted mb-3">
                <span><strong>Type:</strong> <?= htmlspecialchars($property['property_type'] ?: 'Home'); ?></span>
                <span><strong>Bedrooms:</strong> <?= htmlspecialchars((string) $property['bedrooms']); ?></span>
                <span><strong>Bathrooms:</strong> <?= htmlspecialchars((string) $property['bathrooms']); ?></span>
            </div>
            <div class="bg-light p-3 rounded mb-3">
                <h4 class="mb-1">$<?= number_format((float) $property['price_per_month'], 0); ?> / month</h4>
                <p class="text-muted mb-0">Owner: <?= htmlspecialchars($property['owner_name'] ?? ''); ?></p>
            </div>

            <?php if (Auth::isTenant()): ?>
                <form method="post" action="<?= htmlspecialchars($baseUrl); ?>/bookings/create" class="border rounded p-3">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Validator::generateCSRFToken()); ?>">
                    <input type="hidden" name="property_id" value="<?= (int) $property['id']; ?>">
                    <div class="mb-2">
                        <label class="form-label">Check-in</label>
                        <input type="date" class="form-control" name="check_in_date" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Check-out</label>
                        <input type="date" class="form-control" name="check_out_date" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Request Booking</button>
                </form>
            <?php else: ?>
                <div class="alert alert-info">Login as a tenant to book this property.</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-lg-6">
            <h4 class="mb-3">Ratings & Reviews</h4>
            <?php if (empty($ratings)): ?>
                <p class="text-muted">No reviews yet.</p>
            <?php else: ?>
                <?php foreach ($ratings as $rating): ?>
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between">
                            <strong><?= htmlspecialchars($rating['full_name']); ?></strong>
                            <span class="text-warning"><?= str_repeat('★', (int) $rating['rating']); ?></span>
                        </div>
                        <p class="mb-0 text-muted"><?= htmlspecialchars($rating['review']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="col-lg-6">
            <h4 class="mb-3">Leave a Rating</h4>
            <?php if (Auth::isTenant()): ?>
                <form method="post" action="<?= htmlspecialchars($baseUrl); ?>/ratings/create" class="border rounded p-3">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Validator::generateCSRFToken()); ?>">
                    <input type="hidden" name="property_id" value="<?= (int) $property['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label">Rating (1-5)</label>
                        <select class="form-select" name="rating">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <option value="<?= $i; ?>"><?= $i; ?> Stars</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Review</label>
                        <textarea class="form-control" name="review" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-outline-primary">Submit Rating</button>
                </form>
            <?php else: ?>
                <div class="alert alert-info">Login as a tenant to leave a rating.</div>
            <?php endif; ?>
        </div>
    </div>
</section>
