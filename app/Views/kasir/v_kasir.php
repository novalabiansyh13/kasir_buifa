<?= $this->include('template/v_header') ?>
<?= $this->include('template/v_appbar') ?>

<div class="space-y-4">
    <!-- Header Banner -->
    <div class="card p-4 bg-gradient-to-r from-primary-700 via-primary to-primary-600 text-white flex flex-col sm:flex-row items-center justify-between gap-3 border border-primary-600 shadow-md rounded-[14px]">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/20 text-white flex items-center justify-center text-xl shrink-0">
                <i class="bi bi-receipt-cutoff"></i>
            </div>
            <div>
                <div class="font-bold text-xs text-white flex items-center gap-2">
                    <span>Terminal POS &bull; Kasir Penjualan</span>
                    <span class="badge bg-white/20 text-white text-[10px] py-0.5 px-2 rounded-full font-mono">Kasir: Online</span>
                </div>
                <span class="text-[11px] text-white/90">Sistem Input Transaksi Kasir Instan &amp; Penjualan Toko</span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= getURL('riwayat') ?>" class="btn btn-sm bg-white/20 hover:bg-white/30 text-white border border-white/30 rounded-xl flex items-center gap-1.5 transition-all text-xs font-bold no-underline">
                <i class="bi bi-clock-history"></i> Buka Riwayat Transaksi
            </a>
            <span class="text-xs font-semibold px-3 py-1.5 rounded-xl bg-white/20 text-white flex items-center gap-1.5">
                <i class="bi bi-person-check-fill text-cyan-200"></i> <?= esc(getSession('fullname')) ?>
            </span>
        </div>
    </div>

    <!-- POS Layout: Grid 12 Kolom -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
        <!-- Kolom Kiri: Input Produk -->
        <div class="lg:col-span-5 space-y-4">
            <div class="card shadow-sm border border-slate-200/80 dark:border-slate-700 rounded-[14px] overflow-hidden bg-white dark:bg-slate-800">
                <div class="bg-[#0284c7] text-white flex items-center justify-between px-4 py-3 font-bold text-xs sm:text-sm">
                    <span class="flex items-center gap-2">
                        <i class="bi bi-cart-plus-fill text-base"></i> Input Barang Belanja
                    </span>
                    <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded-full font-normal">Form Transaksi</span>
                </div>
                <div class="p-4 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5">
                            Filter Kategori <span class="text-[10px] font-normal text-slate-400 dark:text-slate-500">(Opsional)</span>
                        </label>
                        <select id="selectCategory" class="w-full">
                            <option value=""></option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5">Pilih Barang / Produk</label>
                        <select id="selectBarang" class="w-full">
                            <option value=""></option>
                        </select>
                    </div>

                    <div class="grid grid-cols-12 gap-3">
                        <div class="col-span-7">
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5">Harga Jual Satuan</label>
                            <input type="number" id="inputHarga" class="form-control text-xs font-mono bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300" placeholder="Rp 0" min="0" readonly>
                        </div>
                        <div class="col-span-5">
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5">Jumlah (Qty)</label>
                            <div class="flex items-center">
                                <button type="button" onclick="ubahQtyInput(-1)" class="btn btn-soft-secondary px-2.5 py-1 rounded-r-none text-xs font-bold">-</button>
                                <input type="number" id="inputJumlah" class="form-control text-xs text-center font-bold rounded-none border-x-0" value="1" min="1">
                                <button type="button" onclick="ubahQtyInput(1)" class="btn btn-soft-secondary px-2.5 py-1 rounded-l-none text-xs font-bold">+</button>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 flex items-center justify-between text-xs">
                        <span class="font-medium text-slate-500 dark:text-slate-400">Subtotal Produk:</span>
                        <span id="previewSubtotal" class="font-bold font-mono text-sm text-sky-600 dark:text-cyan-400">Rp 0</span>
                    </div>

                    <button type="button" id="btnTambahItem" class="btn btn-primary w-full flex items-center justify-center gap-1.5 py-2.5 text-xs font-bold shadow-xs">
                        <i class="bi bi-plus-circle-fill text-sm"></i> Tambah ke Keranjang
                    </button>
                </div>
            </div>

            <!-- Kartu Akses Cepat Riwayat -->
            <div class="card p-4 shadow-sm border border-slate-200/80 dark:border-slate-700 rounded-[14px] bg-white dark:bg-slate-800 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Riwayat Transaksi Toko</span>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400">Cek rekap penjualan, nota, dan margin laba</span>
                </div>
                <a href="<?= getURL('riwayat') ?>" class="btn btn-sm btn-soft-primary flex items-center gap-1 text-xs font-bold no-underline">
                    <i class="bi bi-arrow-right-circle-fill"></i> Buka
                </a>
            </div>
        </div>

        <!-- Kolom Kanan: Keranjang Belanja & Pembayaran -->
        <div class="lg:col-span-7 space-y-4">
            <div class="card shadow-sm border border-slate-200/80 dark:border-slate-700 rounded-[14px] overflow-hidden bg-white dark:bg-slate-800">
                <div class="bg-slate-900 text-white flex items-center justify-between px-4 py-3 font-bold text-xs sm:text-sm">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-bag-check-fill text-sky-400 text-base"></i>
                        <span>Keranjang Belanja</span>
                        <span id="cartCountBadge" class="badge badge-soft-primary badge-xs">0 Item</span>
                    </div>
                    <button type="button" onclick="kosongkanKeranjang()" class="text-[11px] text-rose-400 hover:text-rose-300 font-normal flex items-center gap-1 cursor-pointer">
                        <i class="bi bi-trash"></i> Kosongkan
                    </button>
                </div>

                <div class="p-4 space-y-4">
                    <!-- Placeholder Keranjang Kosong -->
                    <div id="cartEmpty" class="text-slate-500 dark:text-slate-400 text-center py-10 text-xs bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-dashed border-slate-200 dark:border-slate-700">
                        <i class="bi bi-cart-x text-3xl text-slate-400 dark:text-slate-600 block mb-2"></i>
                        <p class="font-medium text-slate-700 dark:text-slate-300 mb-0.5">Keranjang Belanja Masih Kosong</p>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Pilih barang di panel kiri lalu klik Tambah ke Keranjang.</p>
                    </div>

                    <!-- Tabel Item Keranjang -->
                    <div id="cartContainer" class="hidden overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
                        <table class="w-full text-xs text-left" id="cartTable">
                            <thead class="bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 uppercase text-[10px] font-bold border-b border-slate-200 dark:border-slate-700">
                                <tr>
                                    <th class="py-2.5 px-3">Nama Barang</th>
                                    <th class="py-2.5 px-3 text-right">Harga</th>
                                    <th class="py-2.5 px-3 text-center w-24">Qty</th>
                                    <th class="py-2.5 px-3 text-right">Subtotal</th>
                                    <th class="py-2.5 px-2 text-center w-10"></th>
                                </tr>
                            </thead>
                            <tbody id="cartBody" class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-900 dark:text-slate-100"></tbody>
                        </table>
                    </div>

                    <!-- Section Pembayaran Kasir -->
                    <div class="pt-2 border-t border-slate-200 dark:border-slate-700 space-y-3">
                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-900/60">
                            <div>
                                <span class="text-xs font-bold text-sky-800 dark:text-sky-300 block">TOTAL TAGIHAN:</span>
                                <span class="text-[10px] text-sky-600 dark:text-sky-400">Total belanja pelanggan</span>
                            </div>
                            <span class="text-2xl sm:text-3xl font-extrabold font-mono text-sky-600 dark:text-cyan-400" id="cartTotal">Rp 0</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5">Uang Diterima (Tunai)</label>
                                <div class="relative flex items-center">
                                    <span class="absolute left-3 text-xs font-bold text-slate-400 pointer-events-none">Rp</span>
                                    <input type="number" id="inputBayar" class="form-control text-xs font-mono font-bold ps-9 text-slate-900 dark:text-white" placeholder="0" min="0">
                                </div>
                                <div class="flex flex-wrap gap-1 mt-1.5">
                                    <button type="button" onclick="setNominalBayar('pas')" class="btn btn-soft-secondary py-0.5 px-2 text-[10px] rounded-md">Uang Pas</button>
                                    <button type="button" onclick="setNominalBayar(10000)" class="btn btn-soft-secondary py-0.5 px-2 text-[10px] rounded-md">10.000</button>
                                    <button type="button" onclick="setNominalBayar(20000)" class="btn btn-soft-secondary py-0.5 px-2 text-[10px] rounded-md">20.000</button>
                                    <button type="button" onclick="setNominalBayar(50000)" class="btn btn-soft-secondary py-0.5 px-2 text-[10px] rounded-md">50.000</button>
                                    <button type="button" onclick="setNominalBayar(100000)" class="btn btn-soft-secondary py-0.5 px-2 text-[10px] rounded-md">100.000</button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5">Kembalian</label>
                                <div class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 flex items-center justify-between h-[38px]">
                                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Kembali:</span>
                                    <span id="labelKembalian" class="font-mono font-bold text-sm text-emerald-600 dark:text-emerald-400">Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Form Submit Transaksi -->
                        <form id="formTransaksi" class="pt-2">
                            <input type="hidden" name="items" id="hiddenItems">
                            <input type="hidden" id="csrf_token_form" name="<?= csrf_token() ?>">
                            <button type="submit" id="btnSimpan" class="btn btn-primary w-full py-3 text-xs font-bold flex items-center justify-center gap-2 shadow-md opacity-50 cursor-not-allowed" disabled>
                                <i class="bi bi-wallet2 text-base"></i> Selesaikan &amp; Simpan Transaksi
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('template/v_footer') ?>

<script>
var cart = [];
var currentTotalBelanja = 0;

function initSelectBarang(catId = '') {
    if ($('#selectBarang').hasClass("select2-hidden-accessible")) {
        $('#selectBarang').select2('destroy');
    }
    $('#selectBarang').empty().append('<option value=""></option>');
    generateSelect2('#selectBarang', '', '<?= getURL('barang/getbarang') ?>', 'Cari nama barang...', '100%', 0, true, { categoryid: catId });
}

function updateSubtotalPreview() {
    var harga = parseFloat($('#inputHarga').val()) || 0;
    var qty = parseInt($('#inputJumlah').val()) || 1;
    $('#previewSubtotal').text(formatRupiah(harga * qty));
}

function ubahQtyInput(delta) {
    var val = parseInt($('#inputJumlah').val()) || 1;
    val = Math.max(1, val + delta);
    $('#inputJumlah').val(val);
    updateSubtotalPreview();
}

$(document).ready(function() {
    generateSelect2('#selectCategory', '', '<?= getURL('barang/getcategory') ?>', 'Semua Kategori (Filter)...', '100%', 0, true, {});
    initSelectBarang();

    $('#selectCategory').on('change', function() {
        var catId = $(this).val() || '';
        $('#inputHarga').val('');
        $('#inputJumlah').val('1');
        updateSubtotalPreview();
        initSelectBarang(catId);
    });

    $('#selectBarang').on('change', function() {
        var data = $(this).select2('data');
        if (data && data.length > 0 && data[0].harga !== undefined) {
            $('#inputHarga').val(data[0].harga);
        } else {
            $('#inputHarga').val('');
        }
        updateSubtotalPreview();
    });

    $('#inputJumlah').on('input change', function() {
        updateSubtotalPreview();
    });

    $('#inputBayar').on('input change', function() {
        hitungKembalian();
    });

    $('#btnTambahItem').on('click', function() {
        tambahItem();
    });

    $('#formTransaksi').on('submit', function(e) {
        e.preventDefault();
        if (cart.length === 0) {
            showError('Keranjang belanja masih kosong.');
            return;
        }
        var bayar = parseFloat($('#inputBayar').val()) || 0;
        if (bayar > 0 && bayar < currentTotalBelanja) {
            showError('Uang yang diterima kurang dari total belanja.');
            return;
        }

        Swal.fire({
            title: 'Simpan Transaksi Kasir?',
            text: 'Total: ' + formatRupiah(currentTotalBelanja) + '. Pastikan pembayaran sudah sesuai.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0284c7',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="bi bi-check-circle-fill"></i> Ya, Simpan Transaksi',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'hyperui-swal',
                title: 'hyperui-swal-title',
                htmlContainer: 'hyperui-swal-text'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                prosesSimpanTransaksi();
            }
        });
    });

    function prosesSimpanTransaksi() {
        var csrf = decrypter($("#csrf_token").val());
        $("#csrf_token_form").val(csrf);
        var btn = $('#btnSimpan');
        var old_html = btn.html();
        btn.html('<i class="bi bi-arrow-repeat animate-spin text-base"></i> Menyimpan...').attr('disabled', 'disabled');

        $.ajax({
            type: 'post',
            url: '<?= getURL('kasir/simpan') ?>',
            data: $('#formTransaksi').serialize(),
            dataType: 'json',
            success: function(response) {
                btn.html(old_html);
                if (response.csrfToken) {
                    $("#csrf_token").val(encrypter(response.csrfToken));
                }
                $("#csrf_token_form").val('');
                if (response.sukses == 1) {
                    cart = [];
                    $('#inputBayar').val('');
                    renderCart();
                    showSuccess(response.pesan || 'Transaksi berhasil disimpan!');
                } else {
                    btn.removeAttr('disabled');
                    showError(response.pesan || 'Gagal menyimpan transaksi.');
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                btn.html(old_html).removeAttr('disabled');
                showError(thrownError || 'Terjadi kesalahan koneksi.');
            }
        });
    }
});

function tambahItem() {
    var selectData = $('#selectBarang').select2('data');
    if (!selectData || selectData.length === 0 || !selectData[0].id) {
        showError('Pilih barang terlebih dahulu!');
        return;
    }
    var item = selectData[0];
    var qty = parseInt($('#inputJumlah').val()) || 1;
    var harga = parseFloat(item.harga) || 0;
    if (qty <= 0) {
        showError('Jumlah qty minimal 1.');
        return;
    }
    var existingIndex = cart.findIndex(c => c.id === item.id);
    if (existingIndex > -1) {
        cart[existingIndex].qty += qty;
        cart[existingIndex].jumlah += qty;
        cart[existingIndex].subtotal = cart[existingIndex].qty * cart[existingIndex].harga;
    } else {
        cart.push({
            id_barang: item.id,
            id: item.id,
            text: item.text,
            harga: harga,
            jumlah: qty,
            qty: qty,
            subtotal: qty * harga
        });
    }
    $('#selectBarang').val(null).trigger('change');
    $('#inputHarga').val('');
    $('#inputJumlah').val('1');
    updateSubtotalPreview();
    renderCart();
}

function renderCart() {
    var tbody = $('#cartBody');
    tbody.empty();
    var total = 0;
    var totalQty = 0;
    if (cart.length === 0) {
        $('#cartEmpty').removeClass('hidden');
        $('#cartContainer').addClass('hidden');
        $('#btnSimpan').addClass('opacity-50 cursor-not-allowed').prop('disabled', true);
        $('#cartCountBadge').text('0 Item');
        $('#cartTotal').text('Rp 0');
        currentTotalBelanja = 0;
        hitungKembalian();
        return;
    }
    $('#cartEmpty').addClass('hidden');
    $('#cartContainer').removeClass('hidden');
    $('#btnSimpan').removeClass('opacity-50 cursor-not-allowed').prop('disabled', false);

    cart.forEach(function(item, index) {
        total += item.subtotal;
        totalQty += item.qty;
        var row = `<tr>
            <td class="py-2.5 px-3 font-semibold text-slate-900 dark:text-white">${item.text}</td>
            <td class="py-2.5 px-3 text-right text-slate-600 dark:text-slate-400 font-mono">${formatRupiah(item.harga)}</td>
            <td class="py-2.5 px-3 text-center">
                <div class="inline-flex items-center">
                    <button type="button" onclick="updateQty(${index}, ${item.qty - 1})" class="btn btn-soft-secondary py-0.5 px-1.5 rounded-r-none text-xs font-bold">-</button>
                    <input type="number" min="1" value="${item.qty}" onchange="updateQty(${index}, this.value)" class="form-control text-xs text-center py-0.5 px-1 w-12 font-bold rounded-none border-x-0">
                    <button type="button" onclick="updateQty(${index}, ${item.qty + 1})" class="btn btn-soft-secondary py-0.5 px-1.5 rounded-l-none text-xs font-bold">+</button>
                </div>
            </td>
            <td class="py-2.5 px-3 text-right text-sky-600 dark:text-cyan-400 font-bold font-mono">${formatRupiah(item.subtotal)}</td>
            <td class="py-2.5 px-2 text-center">
                <button type="button" onclick="hapusItem(${index})" class="btn btn-soft-danger p-1 text-xs" title="Hapus item">
                    <i class="bi bi-trash-fill"></i>
                </button>
            </td>
        </tr>`;
        tbody.append(row);
    });

    currentTotalBelanja = total;
    $('#cartTotal').text(formatRupiah(total));
    $('#hiddenItems').val(JSON.stringify(cart));
    $('#cartCountBadge').text(totalQty + ' Item');
    hitungKembalian();
}

function updateQty(index, val) {
    var qty = parseInt(val) || 1;
    if (qty < 1) {
        hapusItem(index);
        return;
    }
    cart[index].qty = qty;
    cart[index].jumlah = qty;
    cart[index].subtotal = qty * cart[index].harga;
    renderCart();
}

function hapusItem(index) {
    cart.splice(index, 1);
    renderCart();
}

function kosongkanKeranjang() {
    if (cart.length === 0) return;
    Swal.fire({
        title: 'Kosongkan Keranjang?',
        text: 'Seluruh item belanja di keranjang akan dihapus.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Kosongkan',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'hyperui-swal',
            title: 'hyperui-swal-title',
            htmlContainer: 'hyperui-swal-text'
        }
    }).then((res) => {
        if (res.isConfirmed) {
            cart = [];
            $('#inputBayar').val('');
            renderCart();
        }
    });
}

function setNominalBayar(nominal) {
    if (nominal === 'pas') {
        $('#inputBayar').val(currentTotalBelanja);
    } else {
        $('#inputBayar').val(nominal);
    }
    hitungKembalian();
}

function hitungKembalian() {
    var bayar = parseFloat($('#inputBayar').val()) || 0;
    var kembalian = bayar - currentTotalBelanja;
    var label = $('#labelKembalian');

    if (bayar === 0 || currentTotalBelanja === 0) {
        label.text('Rp 0').removeClass('text-rose-500 text-emerald-600 dark:text-emerald-400').addClass('text-slate-500 dark:text-slate-400');
    } else if (kembalian < 0) {
        label.text('Kurang ' + formatRupiah(Math.abs(kembalian))).removeClass('text-emerald-600 dark:text-emerald-400 text-slate-500').addClass('text-rose-500');
    } else {
        label.text(formatRupiah(kembalian)).removeClass('text-rose-500 text-slate-500').addClass('text-emerald-600 dark:text-emerald-400');
    }
}
</script>
