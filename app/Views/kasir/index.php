<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="row g-3">

    <!-- ══════════════════════════════════════════════════════════════════════
         KOLOM KIRI: Form Input Transaksi
    ═══════════════════════════════════════════════════════════════════════ -->
    <div class="col-lg-5 col-xl-4">

        <div class="card h-100">
            <div class="card-header bg-success text-white d-flex align-items-center gap-2">
                <i class="bi bi-cart-plus fs-5"></i>
                <span>Input Transaksi</span>
            </div>
            <div class="card-body">

                <!-- Pilih Barang -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pilih Barang</label>
                    <select id="selectBarang" class="form-select">
                        <option value="">— Pilih barang —</option>
                        <?php foreach ($barang_list as $b): ?>
                            <option value="<?= $b['id_barang'] ?>"
                                    data-nama="<?= esc($b['nama_barang']) ?>"
                                    data-harga="<?= $b['harga_jual'] ?>"
                                    data-margin="<?= $b['margin'] ?>">
                                <?= esc($b['nama_barang']) ?>
                                (Rp <?= number_format($b['harga_jual'], 0, ',', '.') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-7">
                        <label class="form-label fw-semibold">Harga Jual (Rp)</label>
                        <input type="number" id="inputHarga" class="form-control" placeholder="0" min="0" readonly>
                    </div>
                    <div class="col-5">
                        <label class="form-label fw-semibold">Jumlah</label>
                        <input type="number" id="inputJumlah" class="form-control" value="1" min="1">
                    </div>
                </div>

                <button type="button" id="btnTambahItem" class="btn btn-success w-100 mb-3">
                    <i class="bi bi-plus-circle me-1"></i>Tambah ke Keranjang
                </button>

                <hr>

                <!-- Keranjang Belanja -->
                <h6 class="fw-bold mb-2"><i class="bi bi-cart3 me-1"></i>Keranjang</h6>

                <div id="cartEmpty" class="text-muted text-center py-3 small">
                    Keranjang kosong. Pilih barang di atas.
                </div>

                <table class="table table-sm table-hover mb-2 d-none" id="cartTable">
                    <thead class="table-light">
                        <tr>
                            <th>Barang</th>
                            <th class="text-end">Harga</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="cartBody"></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end">Total Bayar:</td>
                            <td class="text-end text-success" id="cartTotal">Rp 0</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Form Submit -->
                <form id="formTransaksi" action="<?= base_url('/kasir/simpan') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="items" id="hiddenItems">
                    <button type="submit" id="btnSimpan" class="btn btn-primary w-100 disabled">
                        <i class="bi bi-save2 me-1"></i>Simpan Transaksi
                    </button>
                </form>

            </div><!-- /card-body -->
        </div><!-- /card -->

    </div><!-- /col-lg-5 -->

    <!-- ══════════════════════════════════════════════════════════════════════
         KOLOM KANAN: Rekap Harian
    ═══════════════════════════════════════════════════════════════════════ -->
    <div class="col-lg-7 col-xl-8">

        <!-- Ringkasan Hari Ini -->
        <div class="row g-3 mb-3">
            <div class="col-sm-6">
                <div class="card text-center border-0 bg-success text-white">
                    <div class="card-body py-3">
                        <div class="small mb-1 opacity-75"><i class="bi bi-cash-coin me-1"></i>Total Penjualan Hari Ini</div>
                        <div class="fs-4 fw-bold" id="summaryPenjualan">
                            Rp <?= number_format($ringkasan['total_penjualan'], 0, ',', '.') ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card text-center border-0 bg-warning">
                    <div class="card-body py-3">
                        <div class="small mb-1 text-dark opacity-75"><i class="bi bi-graph-up-arrow me-1"></i>Total Margin / Untung Hari Ini</div>
                        <div class="fs-4 fw-bold text-dark" id="summaryMargin">
                            Rp <?= number_format($ringkasan['total_margin'], 0, ',', '.') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Rekap Transaksi Harian -->
        <div class="card">
            <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                <span><i class="bi bi-journal-text me-1"></i>Rekap Transaksi Hari Ini</span>
                <button class="btn btn-sm btn-outline-light" id="btnRefresh" title="Muat ulang data">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0" id="tabelRekap">
                        <thead class="table-dark">
                            <tr>
                                <th style="min-width:70px">Jam</th>
                                <th>Detail Barang</th>
                                <th class="text-end" style="min-width:130px">Total Bayar</th>
                                <th class="text-end" style="min-width:110px">Margin</th>
                            </tr>
                        </thead>
                        <tbody id="rekapBody">
                            <?php if (empty($transaksi)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                        Belum ada transaksi hari ini.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($transaksi as $t): ?>
                                    <tr>
                                        <td><span class="badge bg-secondary"><?= esc($t['jam']) ?></span></td>
                                        <td><?= esc($t['detail_barang']) ?></td>
                                        <td class="text-end fw-semibold text-success">
                                            Rp <?= number_format($t['total_bayar'], 0, ',', '.') ?>
                                        </td>
                                        <td class="text-end text-warning fw-semibold">
                                            Rp <?= number_format($t['total_margin'], 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="summary-row">
                            <tr>
                                <td colspan="2" class="text-end">
                                    <i class="bi bi-calculator me-1"></i>TOTAL HARI INI:
                                </td>
                                <td class="text-end text-success" id="footerPenjualan">
                                    Rp <?= number_format($ringkasan['total_penjualan'], 0, ',', '.') ?>
                                </td>
                                <td class="text-end text-warning" id="footerMargin">
                                    Rp <?= number_format($ringkasan['total_margin'], 0, ',', '.') ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div><!-- /table-responsive -->
            </div><!-- /card-body -->
        </div><!-- /card -->

    </div><!-- /col-lg-7 -->

</div><!-- /row -->

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script>
/* ─────────────────────────────────────────────────────────────────────────────
   Kasir – Client-side cart logic
───────────────────────────────────────────────────────────────────────────── */

const cart = [];          // [{id_barang, nama, harga, margin, jumlah}]

const selectBarang  = document.getElementById('selectBarang');
const inputHarga    = document.getElementById('inputHarga');
const inputJumlah   = document.getElementById('inputJumlah');
const btnTambah     = document.getElementById('btnTambahItem');
const cartTable     = document.getElementById('cartTable');
const cartEmpty     = document.getElementById('cartEmpty');
const cartBody      = document.getElementById('cartBody');
const cartTotalEl   = document.getElementById('cartTotal');
const hiddenItems   = document.getElementById('hiddenItems');
const btnSimpan     = document.getElementById('btnSimpan');
const btnRefresh    = document.getElementById('btnRefresh');

// ── Auto-fill harga saat barang dipilih ──────────────────────────────────────
selectBarang.addEventListener('change', function () {
    const opt = this.selectedOptions[0];
    if (opt && opt.value) {
        inputHarga.value = opt.dataset.harga;
    } else {
        inputHarga.value = '';
    }
});

// ── Tambah item ke keranjang ──────────────────────────────────────────────────
btnTambah.addEventListener('click', function () {
    const opt = selectBarang.selectedOptions[0];
    if (!opt || !opt.value) {
        alert('Pilih barang terlebih dahulu.');
        return;
    }

    const jumlah = parseInt(inputJumlah.value, 10);
    if (isNaN(jumlah) || jumlah < 1) {
        alert('Jumlah harus minimal 1.');
        return;
    }

    const idBarang = parseInt(opt.value, 10);
    const existing = cart.find(c => c.id_barang === idBarang);

    if (existing) {
        existing.jumlah += jumlah;
    } else {
        cart.push({
            id_barang : idBarang,
            nama      : opt.dataset.nama,
            harga     : parseFloat(opt.dataset.harga),
            margin    : parseFloat(opt.dataset.margin),
            jumlah    : jumlah,
        });
    }

    renderCart();

    // Reset pilihan
    selectBarang.value = '';
    inputHarga.value   = '';
    inputJumlah.value  = 1;
});

// ── Render tabel keranjang ────────────────────────────────────────────────────
function renderCart() {
    cartBody.innerHTML = '';
    let total = 0;

    cart.forEach((item, idx) => {
        const subtotal = item.harga * item.jumlah;
        total += subtotal;

        const tr = document.createElement('tr');
        tr.className = 'cart-item';
        tr.innerHTML = `
            <td>${escHtml(item.nama)}</td>
            <td class="text-end">${formatRp(item.harga)}</td>
            <td class="text-center">
                <input type="number" class="form-control form-control-sm text-center"
                       style="width:60px;display:inline-block"
                       value="${item.jumlah}" min="1"
                       onchange="updateJumlah(${idx}, this.value)">
            </td>
            <td class="text-end fw-semibold">${formatRp(subtotal)}</td>
            <td>
                <button class="btn btn-sm btn-outline-danger p-0 px-1"
                        onclick="hapusItem(${idx})" title="Hapus">
                    <i class="bi bi-trash3"></i>
                </button>
            </td>`;
        cartBody.appendChild(tr);
    });

    cartTotalEl.textContent = formatRp(total);
    hiddenItems.value = JSON.stringify(cart.map(c => ({ id_barang: c.id_barang, jumlah: c.jumlah })));

    if (cart.length > 0) {
        cartTable.classList.remove('d-none');
        cartEmpty.classList.add('d-none');
        btnSimpan.classList.remove('disabled');
    } else {
        cartTable.classList.add('d-none');
        cartEmpty.classList.remove('d-none');
        btnSimpan.classList.add('disabled');
    }
}

function updateJumlah(idx, val) {
    const n = parseInt(val, 10);
    if (n >= 1) {
        cart[idx].jumlah = n;
        renderCart();
    }
}

function hapusItem(idx) {
    cart.splice(idx, 1);
    renderCart();
}

// ── Konfirmasi simpan ─────────────────────────────────────────────────────────
document.getElementById('formTransaksi').addEventListener('submit', function (e) {
    if (cart.length === 0) { e.preventDefault(); return; }
    if (!confirm('Simpan transaksi ini?')) e.preventDefault();
});

// ── Refresh rekap via AJAX ────────────────────────────────────────────────────
btnRefresh.addEventListener('click', muatRekap);

function muatRekap() {
    btnRefresh.disabled = true;
    btnRefresh.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    fetch('<?= base_url('/kasir/rekap') ?>')
        .then(r => r.json())
        .then(data => {
            renderRekap(data.transaksi);
            updateSummary(data.ringkasan);
        })
        .catch(() => alert('Gagal memuat rekap.'))
        .finally(() => {
            btnRefresh.disabled = false;
            btnRefresh.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
        });
}

function renderRekap(rows) {
    const tbody = document.getElementById('rekapBody');
    if (!rows || rows.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-4">
            <i class="bi bi-inbox fs-4 d-block mb-1"></i>Belum ada transaksi hari ini.</td></tr>`;
        return;
    }
    tbody.innerHTML = rows.map(t => `
        <tr>
            <td><span class="badge bg-secondary">${escHtml(t.jam)}</span></td>
            <td>${escHtml(t.detail_barang)}</td>
            <td class="text-end fw-semibold text-success">${formatRp(t.total_bayar)}</td>
            <td class="text-end text-warning fw-semibold">${formatRp(t.total_margin)}</td>
        </tr>`).join('');
}

function updateSummary(r) {
    const penjualan = parseFloat(r.total_penjualan) || 0;
    const margin    = parseFloat(r.total_margin)    || 0;
    document.getElementById('summaryPenjualan').textContent = formatRp(penjualan);
    document.getElementById('summaryMargin').textContent    = formatRp(margin);
    document.getElementById('footerPenjualan').textContent  = formatRp(penjualan);
    document.getElementById('footerMargin').textContent     = formatRp(margin);
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function formatRp(n) {
    return 'Rp ' + parseFloat(n).toLocaleString('id-ID', { minimumFractionDigits: 0 });
}
function escHtml(s) {
    return String(s).replace(/[&<>"']/g, m =>
        ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m]));
}
</script>
<?= $this->endSection() ?>
