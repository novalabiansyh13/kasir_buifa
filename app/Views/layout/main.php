<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Kasir Pintar Bu Ifa') ?> – Kasir Pintar Bu Ifa</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --brand-primary: #198754;
            --brand-dark:    #145c38;
        }
        body {
            background: #f4f6f9;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        .navbar-brand { font-weight: 700; letter-spacing: .5px; }
        .navbar-brand span { color: #ffc107; }

        /* Sidebar */
        .sidebar {
            min-height: calc(100vh - 56px);
            background: #fff;
            border-right: 1px solid #dee2e6;
        }
        .sidebar .nav-link {
            color: #495057;
            border-radius: 8px;
            margin-bottom: 2px;
        }
        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            background: var(--brand-primary);
            color: #fff;
        }
        .sidebar .nav-link .bi { margin-right: 8px; }

        /* Cards */
        .card { border: none; box-shadow: 0 1px 4px rgba(0,0,0,.08); border-radius: 12px; }
        .card-header { border-radius: 12px 12px 0 0 !important; font-weight: 600; }

        /* Rupiah badge */
        .rp-badge { font-size: .75rem; opacity: .7; }

        /* Sticky summary row */
        tfoot.summary-row tr td { font-weight: 700; background: #e9f7ef; }

        /* Cart item animation */
        .cart-item { animation: fadeIn .2s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; } }

        /* Mobile adjustments */
        @media (max-width: 767.98px) {
            .sidebar { min-height: auto; border-right: none; border-bottom: 1px solid #dee2e6; }
            .sidebar .nav { flex-direction: row; flex-wrap: wrap; gap: 4px; }
        }
    </style>
    <?= $this->renderSection('head_extra') ?>
</head>
<body>

<!-- ── Top Navbar ──────────────────────────────────────────────────────────── -->
<nav class="navbar navbar-expand-md navbar-dark" style="background:var(--brand-primary);">
    <div class="container-fluid px-3">
        <a class="navbar-brand" href="<?= base_url('/') ?>">
            <i class="bi bi-shop-window me-1"></i>Kasir Pintar <span>Bu Ifa</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTop">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarTop">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('/') ?>">
                        <i class="bi bi-receipt me-1"></i>Kasir
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('/barang') ?>">
                        <i class="bi bi-box-seam me-1"></i>Barang
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- ── Main Layout ─────────────────────────────────────────────────────────── -->
<div class="container-fluid">
    <div class="row">

        <!-- Sidebar (desktop) -->
        <div class="col-md-2 sidebar d-none d-md-block py-3 px-2">
            <nav class="nav flex-column">
                <a href="<?= base_url('/') ?>"
                   class="nav-link <?= (current_url() === base_url('/') || current_url() === base_url('/kasir')) ? 'active' : '' ?>">
                    <i class="bi bi-receipt-cutoff"></i>Kasir / Dashboard
                </a>
                <a href="<?= base_url('/barang') ?>"
                   class="nav-link <?= (strpos(current_url(), base_url('/barang')) !== false) ? 'active' : '' ?>">
                    <i class="bi bi-box-seam"></i>Manajemen Barang
                </a>
            </nav>
        </div>

        <!-- Content -->
        <div class="col-md-10 py-3 px-3">

            <!-- Flash messages -->
            <?php if (session()->has('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-1"></i><?= session('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (session()->has('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i><?= session('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </div>

    </div><!-- /row -->
</div><!-- /container-fluid -->

<footer class="text-center text-muted py-3 mt-4" style="font-size:.8rem;border-top:1px solid #dee2e6;">
    &copy; <?= date('Y') ?> Kasir Pintar Bu Ifa &mdash; Dibuat dengan <i class="bi bi-heart-fill text-danger"></i>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
