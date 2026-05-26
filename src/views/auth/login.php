<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="mb-3">Login</h3>
                    <form method="post" action="<?= htmlspecialchars($baseUrl); ?>/login">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Validator::generateCSRFToken()); ?>">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    <div class="mt-3 d-flex justify-content-between">
                        <a href="<?= htmlspecialchars($baseUrl); ?>/register">Create an account</a>
                        <a href="<?= htmlspecialchars($baseUrl); ?>/forgot-password">Forgot password?</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
