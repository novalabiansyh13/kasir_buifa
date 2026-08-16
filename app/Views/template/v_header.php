<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Kasir Pintar Bu Ifa') ?> – Kasir Pintar Bu Ifa</title>
    <?= $this->include('template/v_import') ?>
</head>
<body>

<?= $this->include('template/v_navbar') ?>

<div class="container-fluid">
    <div class="row">

        <?= $this->include('template/v_sidebar') ?>

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
