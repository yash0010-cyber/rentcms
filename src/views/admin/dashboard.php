<section class="container py-5">
    <h2 class="fw-bold mb-4">Admin Dashboard</h2>
    <div class="row g-3">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3">
                <h6 class="text-muted">Users</h6>
                <h3 class="fw-bold"><?= (int) ($stats['users'] ?? 0); ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3">
                <h6 class="text-muted">Properties</h6>
                <h3 class="fw-bold"><?= (int) ($stats['properties'] ?? 0); ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3">
                <h6 class="text-muted">Bookings</h6>
                <h3 class="fw-bold"><?= (int) ($stats['bookings'] ?? 0); ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3">
                <h6 class="text-muted">Members</h6>
                <h3 class="fw-bold"><?= (int) ($stats['members'] ?? 0); ?></h3>
            </div>
        </div>
    </div>
    <div class="mt-4 d-flex gap-3 flex-wrap">
        <a class="btn btn-outline-primary" href="<?= htmlspecialchars($baseUrl); ?>/admin/users">View Users</a>
        <a class="btn btn-outline-primary" href="<?= htmlspecialchars($baseUrl); ?>/admin/properties">View Properties</a>
        <a class="btn btn-outline-primary" href="<?= htmlspecialchars($baseUrl); ?>/admin/bookings">View Bookings</a>
        <a class="btn btn-outline-primary" href="<?= htmlspecialchars($baseUrl); ?>/admin/members">View Members</a>
    </div>
</section>
