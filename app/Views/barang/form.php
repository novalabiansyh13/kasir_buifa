<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">

        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="<?= base_url('/barang') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h4 class="fw-bold mb-0"><?= esc($title) ?></h4>
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
            <div class="card-body">

                <?php
                    $isEdit     = ($barang !== null);
                    $action     = $isEdit
                        ? base_url('/barang/update/' . $barang['id_barang'])
                        : base_url('/barang/simpan');
                    $namaBrg    = old('nama_barang', $isEdit ? $barang['nama_barang'] : '');
                    $hargaBeli  = old('harga_beli',  $isEdit ? $barang['harga_beli']  : '');
                    $hargaJual  = old('harga_jual',  $isEdit ? $barang['harga_jual']  : '');
                    $marginCalc = ($isEdit && $hargaJual !== '' && $hargaBeli !== '')
                        ? ((float)$hargaJual - (float)$hargaBeli)
                        : null;
                ?>

                <form action="<?= $action ?>" method="post" id="formBarang">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="nama_barang">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_barang" id="nama_barang"
                               class="form-control <?= session('errors.nama_barang') ? 'is-invalid' : '' ?>"
                               value="<?= esc($namaBrg) ?>" placeholder="Contoh: Gula Pasir 1 kg" required>
                        <?php if (session('errors.nama_barang')): ?>
                            <div class="invalid-feedback"><?= session('errors.nama_barang') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold" for="harga_beli">Harga Beli (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="harga_beli" id="harga_beli"
                                   class="form-control <?= session('errors.harga_beli') ? 'is-invalid' : '' ?>"
                                   value="<?= esc($hargaBeli) ?>" placeholder="0" min="1" step="any" required>
                            <?php if (session('errors.harga_beli')): ?>
                                <div class="invalid-feedback"><?= session('errors.harga_beli') ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold" for="harga_jual">Harga Jual (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="harga_jual" id="harga_jual"
                                   class="form-control <?= session('errors.harga_jual') ? 'is-invalid' : '' ?>"
                                   value="<?= esc($hargaJual) ?>" placeholder="0" min="1" step="any" required>
                            <?php if (session('errors.harga_jual')): ?>
                                <div class="invalid-feedback"><?= session('errors.harga_jual') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Preview Margin -->
                    <div class="alert alert-info py-2 d-flex align-items-center gap-2" id="previewMargin">
                        <i class="bi bi-info-circle"></i>
                        Margin otomatis:
                        <strong id="marginVal">
                            <?= $marginCalc !== null ? 'Rp ' . number_format($marginCalc, 0, ',', '.') : '—' ?>
                        </strong>
                        <small class="text-muted ms-1">(Harga Jual − Harga Beli)</small>
                    </div>

                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-save2 me-1"></i><?= $isEdit ? 'Simpan Perubahan' : 'Tambah Barang' ?>
                    </button>
                </form>

            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script>
const beli = document.getElementById('harga_beli');
const jual = document.getElementById('harga_jual');
const marginEl = document.getElementById('marginVal');

function hitungMargin() {
    const b = parseFloat(beli.value) || 0;
    const j = parseFloat(jual.value) || 0;
    const m = j - b;
    marginEl.textContent = 'Rp ' + m.toLocaleString('id-ID');
    marginEl.className   = m >= 0 ? 'text-success' : 'text-danger';
}

beli.addEventListener('input', hitungMargin);
jual.addEventListener('input', hitungMargin);
</script>
<?= $this->endSection() ?>
