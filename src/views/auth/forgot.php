<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="mb-3">Forgot Password</h3>
                    <form method="post" action="<?= htmlspecialchars($baseUrl); ?>/forgot-password">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Validator::generateCSRFToken()); ?>">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
                    </form>
                    <div class="mt-3">
                        <a href="<?= htmlspecialchars($baseUrl); ?>/login">Back to login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
