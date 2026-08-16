<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<!-- DataTables -->
<link href="<?= getURL('css/dataTables.dataTables.min.css') ?>" rel="stylesheet">
<!-- Select2 -->
<link href="<?= getURL('css/select2.min.css') ?>" rel="stylesheet">
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
