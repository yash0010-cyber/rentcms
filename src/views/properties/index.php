<section class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Available Listings</h2>
        <a class="btn btn-outline-primary" href="<?= htmlspecialchars($baseUrl); ?>/">Back to Home</a>
    </div>

    <form class="row g-2 mb-4" method="get" action="<?= htmlspecialchars($baseUrl); ?>/properties">
        <div class="col-md-3">
            <select class="form-select" name="city">
                <option value="">City</option>
                <?php foreach (Property::getCityOptions() as $city): ?>
                    <option value="<?= htmlspecialchars($city); ?>" <?= ($filters['city'] ?? '') === $city ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($city); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" name="category">
                <option value="">Category</option>
                <?php foreach (Property::getCategories() as $category): ?>
                    <option value="<?= htmlspecialchars($category); ?>" <?= ($filters['category'] ?? '') === $category ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($category); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" name="deal_type">
                <option value="">Deal Type</option>
                <?php foreach (Property::getDealTypes() as $dealType): ?>
                    <option value="<?= htmlspecialchars($dealType); ?>" <?= ($filters['deal_type'] ?? '') === $dealType ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($dealType); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary w-100" type="submit">Filter</button>
        </div>
    </form>

    <div class="row g-4">
        <?php foreach ($properties as $property): ?>
            <div class="col-md-4">
                <div class="card listing-card h-100 shadow-sm border-0">
                    <div class="position-relative">
                        <img src="<?= htmlspecialchars($property['image']); ?>" class="card-img-top" alt="Property image">
                        <span class="price-badge"><?= htmlspecialchars($property['price_label']); ?></span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-primary mb-2">
                            <?= htmlspecialchars($property['title']); ?>
                        </h5>
                        <p class="card-text text-muted mb-3">
                            <i class="bi bi-geo-alt-fill text-success me-1"></i>
                            <?= htmlspecialchars($property['address']); ?>
                        </p>
                        <div class="d-flex flex-wrap gap-3 text-muted small mb-3">
                            <span><?= htmlspecialchars($property['deal_type']); ?></span>
                            <span><?= htmlspecialchars($property['category']); ?></span>
                            <span>Bedrooms: <?= htmlspecialchars((string) $property['bedrooms']); ?></span>
                        </div>
                        <a class="btn btn-outline-primary w-100" href="<?= htmlspecialchars($baseUrl); ?>/properties/view?id=<?= (int) $property['id']; ?>">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
