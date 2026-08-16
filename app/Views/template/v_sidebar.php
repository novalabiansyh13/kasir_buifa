<!-- Sidebar (desktop) -->
<div class="col-md-2 sidebar d-none d-md-block py-3 px-2">
    <nav class="nav flex-column">
        <a href="<?= getURL('/') ?>"
           class="nav-link <?= (current_url() === getURL('/') || current_url() === getURL('/kasir')) ? 'active' : '' ?>">
            <i class="bi bi-receipt-cutoff"></i>Kasir / Dashboard
        </a>
        <a href="<?= getURL('/barang') ?>"
           class="nav-link <?= (strpos(current_url(), getURL('/barang')) !== false) ? 'active' : '' ?>">
            <i class="bi bi-box-seam"></i>Manajemen Barang
        </a>
    </nav>
</div>
