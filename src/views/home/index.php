<section class="hero-section">
    <div class="container">
        <div class="hero-search shadow-sm">
            <form class="row g-2" method="get" action="<?= htmlspecialchars($baseUrl); ?>/properties">
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
                    <button class="btn btn-primary w-100 search-btn" type="submit">
                        <i class="bi bi-search me-1"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="listing-section bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Popular Listing</h2>
            <button class="btn btn-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#mapModal">
                <i class="bi bi-geo-alt-fill text-success me-1"></i> Show on Map
            </button>
        </div>
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
                            <div class="d-flex flex-wrap gap-3 text-muted small">
                                <span><?= htmlspecialchars($property['deal_type']); ?></span>
                                <span><?= htmlspecialchars($property['category']); ?></span>
                                <span>Bedrooms: <?= htmlspecialchars((string) $property['bedrooms']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<div class="modal fade" id="mapModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Listings Map</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="map-placeholder d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <i class="bi bi-geo-alt-fill display-4 text-primary"></i>
                        <p class="mt-3">Map integration goes here. Connect your preferred map service when ready.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
