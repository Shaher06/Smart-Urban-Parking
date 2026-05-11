    </main>
    <footer class="landing-footer mt-auto">
        <div class="container py-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2 fw-semibold">
                        <span class="landing-mark"><i class="bi bi-p-circle-fill"></i></span>
                        <span><?= APP_NAME ?></span>
                    </div>
                    <div class="text-white-50 small mt-1">
                        Smart Urban Parking Management System — university project demo.
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-wrap justify-content-md-end gap-3 small">
                        <a class="link-light text-decoration-none" href="<?= page_url('home') ?>">Home</a>
                        <a class="link-light text-decoration-none" href="<?= page_url('home') ?>#features">Features</a>
                        <a class="link-light text-decoration-none" href="<?= page_url('home') ?>#how">How it works</a>
                        <a class="link-light text-decoration-none" href="<?= page_url('login') ?>">Login</a>
                        <a class="link-light text-decoration-none" href="<?= page_url('register') ?>">Register</a>
                    </div>
                </div>
            </div>
            <hr class="border-white border-opacity-10 my-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 small text-white-50">
                <div>&copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.</div>
                <div></div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset_url('js/main.js') ?>"></script>
</body>
</html>

