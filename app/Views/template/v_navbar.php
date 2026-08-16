<!-- ── Top Navbar ──────────────────────────────────────────────────────────── -->
<nav class="navbar navbar-expand-md navbar-dark" style="background:var(--brand-primary);">
    <div class="container-fluid px-3">
        <a class="navbar-brand" href="<?= getURL('/') ?>">
            <i class="bi bi-shop-window me-1"></i>Kasir Pintar <span>Bu Ifa</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTop">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarTop">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= getURL('/') ?>">
                        <i class="bi bi-receipt me-1"></i>Kasir
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= getURL('/barang') ?>">
                        <i class="bi bi-box-seam me-1"></i>Barang
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
