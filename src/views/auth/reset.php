<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="mb-3">Reset Password</h3>
                    <form method="post" action="<?= htmlspecialchars($baseUrl); ?>/reset-password">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Validator::generateCSRFToken()); ?>">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? ''); ?>">
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                    </form>
                    <div class="mt-3">
                        <a href="<?= htmlspecialchars($baseUrl); ?>/login">Back to login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
