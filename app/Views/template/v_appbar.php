<!-- Appbar (mobile) -->
<div class="d-md-none appbar px-3 py-2 d-flex align-items-center justify-content-between" style="background:var(--brand-primary);">
    <span class="text-white fw-semibold">
        <i class="bi bi-shop-window me-1"></i><?= esc($title ?? 'Kasir Pintar Bu Ifa') ?>
    </span>
    <?php if (isset($section)): ?>
        <span class="text-white-50 small"><?= esc($section) ?></span>
    <?php endif; ?>
</div>
