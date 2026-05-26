<?php
$isEdit = !empty($property);
$action = $isEdit ? $baseUrl . '/owner/properties/edit' : $baseUrl . '/owner/properties/create';
?>
<section class="container py-5">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h3 class="mb-3"><?= $isEdit ? 'Edit Property' : 'Add Property'; ?></h3>
            <form method="post" action="<?= htmlspecialchars($action); ?>" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Validator::generateCSRFToken()); ?>">
                <?php if ($isEdit): ?>
                    <input type="hidden" name="id" value="<?= (int) $property['id']; ?>">
                <?php endif; ?>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($property['title'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Property Type</label>
                        <input type="text" class="form-control" name="property_type" value="<?= htmlspecialchars($property['property_type'] ?? ''); ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($property['description'] ?? ''); ?></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" class="form-control" name="address" value="<?= htmlspecialchars($property['address'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">City</label>
                        <input type="text" class="form-control" name="city" value="<?= htmlspecialchars($property['city'] ?? ''); ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">State</label>
                        <input type="text" class="form-control" name="state" value="<?= htmlspecialchars($property['state'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Country</label>
                        <input type="text" class="form-control" name="country" value="<?= htmlspecialchars($property['country'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Postal Code</label>
                        <input type="text" class="form-control" name="postal_code" value="<?= htmlspecialchars($property['postal_code'] ?? ''); ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Price per Month</label>
                        <input type="number" class="form-control" name="price_per_month" value="<?= htmlspecialchars((string) ($property['price_per_month'] ?? '')); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Bedrooms</label>
                        <input type="number" class="form-control" name="bedrooms" value="<?= htmlspecialchars((string) ($property['bedrooms'] ?? 1)); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Bathrooms</label>
                        <input type="number" class="form-control" name="bathrooms" value="<?= htmlspecialchars((string) ($property['bathrooms'] ?? 1)); ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Square Feet</label>
                        <input type="number" class="form-control" name="square_feet" value="<?= htmlspecialchars((string) ($property['square_feet'] ?? '')); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Featured Image</label>
                        <input type="file" class="form-control" name="featured_image">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Amenities</label>
                    <textarea class="form-control" name="amenities" rows="2"><?= htmlspecialchars($property['amenities'] ?? ''); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save Property</button>
                <a href="<?= htmlspecialchars($baseUrl); ?>/owner/properties" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
</section>
