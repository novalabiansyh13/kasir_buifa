<?= $this->include('template/v_header') ?>
<?= $this->include('template/v_appbar') ?>
<div class="main-content content margin-t-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0"><i class="bi bi-box-seam me-2"></i>Manajemen Barang</h4>
        <?php if ($akses["COMPO_" . COMADD]) : ?>
            <a href="<?= getURL('barang/form') ?>" class="btn btn-success">
                <i class="bi bi-plus-circle me-1"></i>Tambah Barang
            </a>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-master" style="width: 100%;">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th class="text-end">Harga Beli</th>
                            <th class="text-end">Harga Jual</th>
                            <th class="text-end">Margin</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->include('template/v_footer') ?>
