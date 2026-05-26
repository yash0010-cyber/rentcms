<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="mb-3">Admin Login</h3>
                    <form method="post" action="<?= htmlspecialchars($baseUrl); ?>/admin/login">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Validator::generateCSRFToken()); ?>">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-dark w-100">Login as Admin</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
