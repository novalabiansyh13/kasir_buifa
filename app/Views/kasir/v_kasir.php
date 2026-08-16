<?= $this->include('template/v_header') ?>
<?= $this->include('template/v_appbar') ?>
<div class="main-content content margin-t-4">
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

                <!-- Pilih Barang (Select2) -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pilih Barang</label>
                    <select id="selectBarang" class="form-select">
                        <option value="">— Pilih barang —</option>
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
                <form id="formTransaksi" style="padding-inline: 0px;">
                    <input type="hidden" name="items" id="hiddenItems">
                    <input type="hidden" id="csrf_token_form" name="<?= csrf_token() ?>">
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
                        <div class="fs-4 fw-bold" id="summaryPenjualan">Rp 0</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card text-center border-0 bg-warning">
                    <div class="card-body py-3">
                        <div class="small mb-1 text-dark opacity-75"><i class="bi bi-graph-up-arrow me-1"></i>Total Margin / Untung Hari Ini</div>
                        <div class="fs-4 fw-bold text-dark" id="summaryMargin">Rp 0</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Rekap Transaksi Harian (server-side datatable) -->
        <div class="card">
            <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                <span><i class="bi bi-journal-text me-1"></i>Rekap Transaksi Hari Ini</span>
                <button class="btn btn-sm btn-outline-light" id="btnRefresh" title="Muat ulang data">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0 table-rekap" id="tabelRekap" style="width: 100%;">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th style="min-width:70px">Jam</th>
                                <th>Detail Barang</th>
                                <th class="text-end" style="min-width:130px">Total Bayar</th>
                                <th class="text-end" style="min-width:110px">Margin</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot class="summary-row">
                            <tr>
                                <td colspan="3" class="text-end">
                                    <i class="bi bi-calculator me-1"></i>TOTAL HARI INI:
                                </td>
                                <td class="text-end text-success" id="footerPenjualan">Rp 0</td>
                                <td class="text-end text-warning" id="footerMargin">Rp 0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div><!-- /table-responsive -->
            </div><!-- /card-body -->
        </div><!-- /card -->

    </div><!-- /col-lg-7 -->

</div><!-- /row -->
</div>
<?= $this->include('template/v_footer') ?>
<script>
/* ─────────────────────────────────────────────────────────────────────────────
   Kasir – client-side cart logic + rekap datatable
──────────────────────────────────────────────────────────────────────────── */

var cart = [];

$(document).ready(function() {

    // ── Select2 pilih barang (AJAX) ─────────────────────────────────────────
    generateSelect2('#selectBarang', '', '<?= getURL('barang/getbarang') ?>', 'Pilih barang', '100%', 5, true, {});

    $('#selectBarang').on('change', function() {
        var data = $(this).select2('data');
        if (data.length > 0 && data[0].harga !== undefined) {
            $('#inputHarga').val(data[0].harga);
        } else {
            $('#inputHarga').val('');
        }
    });

    $('#btnTambahItem').on('click', function() {
        tambahItem();
    });

    $('#formTransaksi').on('submit', function(e) {
        e.preventDefault();
        if (cart.length === 0) {
            showError('Keranjang masih kosong.');
            return;
        }
        if (!confirm('Simpan transaksi ini?')) return;

        var csrf = decrypter($("#csrf_token").val());
        $("#csrf_token_form").val(csrf);

        var old_html = $('#btnSimpan').html();
        $('#btnSimpan').html('<span class="spinner-border spinner-border-sm me-1"></span>');
        $('#btnSimpan').attr('disabled', 'disabled');

        $.ajax({
            type: 'post',
            url: '<?= getURL('kasir/simpan') ?>',
            data: $('#formTransaksi').serialize(),
            dataType: 'json',
            success: function(response) {
                $('#btnSimpan').html(old_html);
                $('#btnSimpan').removeAttr('disabled');
                $("#csrf_token").val(encrypter(response.csrfToken));
                $("#csrf_token_form").val('');
                showNotif((response.sukses == 1 ? 'success' : 'error'), response.pesan);
                if (response.sukses == 1) {
                    cart.length = 0;
                    renderCart();
                    $('#inputHarga').val('');
                    $('#inputJumlah').val(1);
                    if (tbl_rekap !== null) tbl_rekap.ajax.reload();
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                $('#btnSimpan').html(old_html);
                $('#btnSimpan').removeAttr('disabled');
                showError(thrownError + ", please contact administrator for the further");
            }
        });
    });

    $('#btnRefresh').on('click', function() {
        if (tbl_rekap !== null) tbl_rekap.ajax.reload();
    });

    // ── Rekap datatable (custom dataSrc update kartu ringkasan) ─────────────
    if ($('.table-rekap').length) {
        tbl_rekap = generateDatatable('.table-rekap', {
            order: [[1, 'desc']],
            ajax: {
                url: '<?= getURL('kasir/table') ?>',
                dataSrc: function(json) {
                    if (json.tambahan) {
                        var pj = json.tambahan.total_penjualan;
                        var mg = json.tambahan.total_margin;
                        if (pj !== undefined) {
                            $("#summaryPenjualan").text(formatRupiah(pj));
                            $("#footerPenjualan").text(formatRupiah(pj));
                        }
                        if (mg !== undefined) {
                            $("#summaryMargin").text(formatRupiah(mg));
                            $("#footerMargin").text(formatRupiah(mg));
                        }
                    }
                    $("#csrf_token").val(encrypter(json.csrfToken));
                    return json.data;
                }
            }
        });
    }
});

function tambahItem() {
    var data = $('#selectBarang').select2('data');
    if (data.length === 0 || !data[0].id) {
        showError('Pilih barang terlebih dahulu.');
        return;
    }
    var jumlah = parseInt($('#inputJumlah').val(), 10);
    if (isNaN(jumlah) || jumlah < 1) {
        showError('Jumlah harus minimal 1.');
        return;
    }
    var item = data[0];
    var existing = cart.find(function(c) { return c.id_barang === item.id; });
    if (existing) {
        existing.jumlah += jumlah;
    } else {
        cart.push({
            id_barang: item.id,
            nama: item.text,
            harga: parseFloat(item.harga),
            margin: parseFloat(item.margin),
            jumlah: jumlah
        });
    }
    renderCart();
    $('#selectBarang').val(null).trigger('change');
    $('#inputHarga').val('');
    $('#inputJumlah').val(1);
}

function renderCart() {
    $('#cartBody').empty();
    var total = 0;
    cart.forEach(function(item, idx) {
        var subtotal = item.harga * item.jumlah;
        total += subtotal;
        $('#cartBody').append(
            '<tr class="cart-item">' +
            '<td>' + escHtml(item.nama) + '</td>' +
            '<td class="text-end">' + formatRupiah(item.harga) + '</td>' +
            '<td class="text-center">' +
            '<input type="number" class="form-control form-control-sm text-center" style="width:60px;display:inline-block" value="' + item.jumlah + '" min="1" data-idx="' + idx + '">' +
            '</td>' +
            '<td class="text-end fw-semibold">' + formatRupiah(subtotal) + '</td>' +
            '<td><button class="btn btn-sm btn-outline-danger p-0 px-1" data-hapus="' + idx + '" title="Hapus"><i class="bi bi-trash3"></i></button></td>' +
            '</tr>'
        );
    });
    $('#cartTotal').text(formatRupiah(total));
    $('#hiddenItems').val(JSON.stringify(cart.map(function(c) { return { id_barang: c.id_barang, jumlah: c.jumlah }; })));
    if (cart.length > 0) {
        $('#cartTable').removeClass('d-none');
        $('#cartEmpty').addClass('d-none');
        $('#btnSimpan').removeClass('disabled');
    } else {
        $('#cartTable').addClass('d-none');
        $('#cartEmpty').removeClass('d-none');
        $('#btnSimpan').addClass('disabled');
    }
}

$(document).on('change', '#cartBody input[type=number]', function() {
    var idx = $(this).data('idx');
    var n = parseInt($(this).val(), 10);
    if (n >= 1 && cart[idx]) {
        cart[idx].jumlah = n;
        renderCart();
    }
});

$(document).on('click', '#cartBody button[data-hapus]', function() {
    var idx = $(this).data('hapus');
    cart.splice(idx, 1);
    renderCart();
});

function escHtml(s) {
    return String(s).replace(/[&<>"']/g, function(m) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m];
    });
}
</script>
