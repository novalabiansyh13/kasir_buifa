<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="bi bi-box-seam me-2"></i>Manajemen Barang</h4>
    <a href="<?= base_url('/barang/tambah') ?>" class="btn btn-success">
        <i class="bi bi-plus-circle me-1"></i>Tambah Barang
    </a>
</div>

<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach (session('errors') as $e): ?>
                <li><?= esc($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nama Barang</th>
                        <th class="text-end">Harga Beli</th>
                        <th class="text-end">Harga Jual</th>
                        <th class="text-end">Margin</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($barang)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                Belum ada data barang.
                                <a href="<?= base_url('/barang/tambah') ?>">Tambah sekarang.</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($barang as $i => $b): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td class="fw-semibold"><?= esc($b['nama_barang']) ?></td>
                                <td class="text-end">Rp <?= number_format($b['harga_beli'], 0, ',', '.') ?></td>
                                <td class="text-end text-success fw-semibold">
                                    Rp <?= number_format($b['harga_jual'], 0, ',', '.') ?>
                                </td>
                                <td class="text-end">
                                    <span class="badge <?= $b['margin'] >= 0 ? 'bg-success' : 'bg-danger' ?>">
                                        Rp <?= number_format($b['margin'], 0, ',', '.') ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('/barang/edit/' . $b['id_barang']) ?>"
                                       class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="<?= base_url('/barang/hapus/' . $b['id_barang']) ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Hapus barang ini?')">
                                        <i class="bi bi-trash3"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
