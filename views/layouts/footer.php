    </main>
    <footer class="py-4 bg-dark text-white mt-auto">
        <div class="container">
            <div class="row py-3">
                <div class="col-md-4 mb-3 mb-md-0">
                    <h5 class="mb-3 text-light">About</h5>
                    <p class="text-muted small mb-0">A simple, clean auction platform helping auction houses run their own timed online auctions without paying expensive third-party fees.</p>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <h5 class="mb-3 text-light">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?= BASE_URL ?>" class="text-decoration-none text-muted small">Home</a></li>
                        <li><a href="<?= BASE_URL ?>auctions" class="text-decoration-none text-muted small">Auctions</a></li>
                        <li><a href="<?= BASE_URL ?>how-to-bid" class="text-decoration-none text-muted small">How to Bid</a></li>
                        <li><a href="<?= BASE_URL ?>register" class="text-decoration-none text-muted small">Register</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3 text-light">Contact</h5>
                    <ul class="list-unstyled text-muted small">
                        <li class="mb-1"><i class="bi bi-envelope me-2"></i> info@titancode.pl</li>
                        <li class="mb-1"><i class="bi bi-telephone me-2"></i> +48 511 118 916</li>
                        <li><i class="bi bi-geo-alt me-2"></i> Warsaw, Poland</li>
                    </ul>
                </div>
            </div>
            <hr class="my-3 bg-secondary">
            <div class="row align-items-center">
                <div class="col-md-6 small">
                    <p class="mb-md-0">&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end small">
                    <p class="mb-0">
                        <span class="ms-2">Created by <i class="fas fa-code"></i> <a href="http://titancode.pl/" target="_blank" class="text-white text-decoration-none">TitanCode</a></span>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Scripts -->
    <script src="<?= BASE_URL ?>public/js/script.js"></script>
</body>
</html> 