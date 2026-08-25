<?= $this->include('template/v_header') ?>
<?= $this->include('template/v_appbar') ?>
<div class="space-y-4">
    <div class="card p-4 bg-gradient-to-r from-primary-700 via-primary to-primary-600 text-white flex flex-col sm:flex-row items-center justify-between gap-3 border border-primary-600 shadow-md rounded-[14px]">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/20 text-white flex items-center justify-center text-xl shrink-0">
                <i class="bi bi-receipt-cutoff"></i>
            </div>
            <div>
                <div class="font-bold text-xs text-white flex items-center gap-2">
                    <span>Terminal POS &bull; Kasir Utama</span>
                    <span class="badge bg-white/20 text-white text-[10px] py-0.5 px-2 rounded-full font-mono">Kasir: Online</span>
                </div>
                <span class="text-[11px] text-white/90">Sistem Transaksi Kasir Instan & Manajemen Penjualan Toko</span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold px-3 py-1.5 rounded-xl bg-white/20 text-white flex items-center gap-1.5">
                <i class="bi bi-person-check-fill text-cyan-200"></i> <?= esc(getSession('fullname')) ?>
            </span>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        <div class="lg:col-span-5 space-y-4">
            <div class="card shadow-sm border border-slate-200/80 dark:border-slate-700 rounded-[14px] overflow-hidden bg-white dark:bg-slate-800">
                <div class="bg-[#0284c7] text-white flex items-center justify-between px-4 py-3 font-bold text-xs sm:text-sm">
                    <span class="flex items-center gap-2">
                        <i class="bi bi-cart-plus-fill text-base"></i> Input Transaksi Kasir
                    </span>
                    <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded-full font-normal">Form Transaksi Kasir</span>
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
                    <div class="grid grid-cols-12 gap-2">
                        <div class="col-span-7">
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5">Harga Jual (Rp)</label>
                            <input type="number" id="inputHarga" class="form-control text-xs font-mono bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300" placeholder="0" min="0" readonly>
                        </div>
                        <div class="col-span-5">
                            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5">Jumlah (Qty)</label>
                            <input type="number" id="inputJumlah" class="form-control text-xs text-center font-bold" value="1" min="1">
                        </div>
                    </div>
                    <button type="button" id="btnTambahItem" class="btn btn-primary w-full flex items-center justify-center gap-1.5 py-2.5 text-xs font-bold shadow-xs">
                        <i class="bi bi-plus-circle-fill text-sm"></i> Tambah ke Keranjang
                    </button>
                    <div class="border-t border-slate-200 dark:border-slate-700 my-2"></div>
                    <div class="flex items-center justify-between">
                        <h6 class="font-bold text-xs text-slate-900 dark:text-white flex items-center gap-1.5 mb-0">
                            <i class="bi bi-bag-check-fill text-sky-600 dark:text-cyan-400 text-sm"></i> Keranjang Belanja
                        </h6>
                        <span id="cartCountBadge" class="badge badge-soft-primary badge-xs">0 Item</span>
                    </div>
                    <div id="cartEmpty" class="text-slate-500 dark:text-slate-400 text-center py-6 text-xs bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-dashed border-slate-200 dark:border-slate-700">
                        Keranjang masih kosong. Pilih barang di atas lalu klik Tambah.
                    </div>
                    <div id="cartContainer" class="hidden overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700">
                        <table class="w-full text-xs text-left" id="cartTable">
                            <thead class="bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 uppercase text-[10px] font-bold border-b border-slate-200 dark:border-slate-700">
                                <tr>
                                    <th class="py-2.5 px-3">Barang</th>
                                    <th class="py-2.5 px-3 text-center">Harga</th>
                                    <th class="py-2.5 px-3 text-center">Qty</th>
                                    <th class="py-2.5 px-3 text-center">Subtotal</th>
                                    <th class="py-2.5 px-2 text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="cartBody" class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-900 dark:text-slate-100"></tbody>
                            <tfoot class="bg-slate-50 dark:bg-slate-900 font-bold border-t border-slate-200 dark:border-slate-700">
                                <tr>
                                    <td colspan="3" class="py-2.5 px-3 text-right text-slate-700 dark:text-slate-300">Total Bayar:</td>
                                    <td class="py-2.5 px-3 text-right text-sky-600 dark:text-cyan-400 text-sm font-bold font-mono" id="cartTotal">Rp 0</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <form id="formTransaksi" class="pt-2">
                        <input type="hidden" name="items" id="hiddenItems">
                        <input type="hidden" id="csrf_token_form" name="<?= csrf_token() ?>">
                        <button type="submit" id="btnSimpan" class="btn btn-primary w-full py-3 text-xs font-bold flex items-center justify-center gap-2 shadow-md opacity-50 cursor-not-allowed" disabled>
                            <i class="bi bi-wallet2 text-base"></i> Simpan Transaksi
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-7 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="card p-4 shadow-sm border border-slate-200/80 dark:border-slate-700 rounded-[14px] bg-white dark:bg-slate-800 flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">TOTAL PENJUALAN HARI INI</span>
                        <span class="badge badge-soft-primary badge-xs">Live Kasir</span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white font-mono tracking-tight my-1" id="summaryPenjualan">Rp 0</div>
                    <span class="text-[10px] text-slate-500 dark:text-slate-400">Total omset kotor transaksi hari ini</span>
                </div>
                <div class="card p-4 shadow-sm border border-slate-200/80 dark:border-slate-700 rounded-[14px] bg-white dark:bg-slate-800 flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">TOTAL MARGIN / UNTUNG</span>
                        <span class="badge badge-soft-warning badge-xs">Profit Laba</span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-amber-600 dark:text-amber-400 font-mono tracking-tight my-1" id="summaryMargin">Rp 0</div>
                    <span class="text-[10px] text-slate-500 dark:text-slate-400">Total laba keuntungan (Jual &minus; Beli)</span>
                </div>
            </div>
            <div class="card shadow-sm border border-slate-200/80 dark:border-slate-700 rounded-[14px] overflow-hidden bg-white dark:bg-slate-800">
                <div class="bg-[#0284c7] text-white flex flex-col sm:flex-row sm:items-center justify-between px-4 py-3 gap-2.5 font-bold text-xs sm:text-sm">
                    <span class="flex items-center gap-2">
                        <i class="bi bi-journal-text text-base"></i> Rekap Transaksi
                    </span>
                    <div class="flex items-center gap-2">
                        <div class="relative flex items-center">
                            <i class="bi bi-calendar3 absolute left-2.5 text-slate-400 text-xs pointer-events-none z-10"></i>
                            <input type="text" id="filter-daterange" class="form-control input-daterange font-semibold cursor-pointer w-[195px] sm:w-[205px] bg-white/95 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-xs text-slate-800 dark:text-slate-200" readonly title="Filter Rentang Tanggal" />
                        </div>
                        <button type="button" id="btnRefresh" class="btn btn-soft-secondary btn-sm flex items-center gap-1 text-xs" title="Reset filter ke hari ini & Refresh">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>

                <div class="p-4 overflow-x-auto">
                    <table class="w-full text-xs text-left text-slate-800 dark:text-slate-200 border-collapse table-rekap" id="tabelRekap" style="width: 100%;">
                        <thead class="bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 text-xs font-bold border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="py-3 px-3 text-center">No</th>
                                <th class="py-3 px-3 text-center">Tanggal</th>
                                <th class="py-3 px-3 text-center">Jam</th>
                                <th class="py-3 px-3 text-center">Detail Barang</th>
                                <th class="py-3 px-3 text-center">Total Bayar</th>
                                <th class="py-3 px-3 text-center">Margin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-slate-900 dark:text-slate-100"></tbody>
                        <tfoot class="bg-slate-50 dark:bg-slate-900 font-bold border-t border-slate-200 dark:border-slate-700">
                            <tr>
                                <td colspan="4" class="py-3 px-3 text-start text-slate-700 dark:text-slate-300"><i class="bi bi-calculator me-1"></i>TOTAL:</td>
                                <td class="py-3 px-3 text-center text-sky-600 dark:text-cyan-400 font-bold text-sm font-mono" id="footerPenjualan">Rp 0</td>
                                <td class="py-3 px-3 text-center text-amber-600 dark:text-amber-400 font-bold text-sm font-mono" id="footerMargin">Rp 0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('template/v_footer') ?>
<script>
var cart = [];

function initSelectBarang(catId = '') {
    if ($('#selectBarang').hasClass("select2-hidden-accessible")) {
        $('#selectBarang').select2('destroy');
    }
    $('#selectBarang').empty().append('<option value=""></option>');
    generateSelect2('#selectBarang', '', '<?= getURL('barang/getbarang') ?>', 'Cari nama barang...', '100%', 0, true, { categoryid: catId });
}

$(document).ready(function() {
    generateSelect2('#selectCategory', '', '<?= getURL('barang/getcategory') ?>', 'Semua Kategori (Filter)...', '100%', 0, true, {});
    initSelectBarang();

    $('#selectCategory').on('change', function() {
        var catId = $(this).val() || '';
        $('#inputHarga').val('');
        $('#inputJumlah').val('1');
        initSelectBarang(catId);
    });

    $('#selectBarang').on('change', function() {
        var data = $(this).select2('data');
        if (data && data.length > 0 && data[0].harga !== undefined) {
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
            showError('Keranjang belanja masih kosong.');
            return;
        }
        Swal.fire({
            title: 'Simpan Transaksi Kasir?',
            text: 'Pastikan seluruh item keranjang dan jumlah sudah sesuai.',
            icon: 'question',
            showCancelButton: true,
            showDenyButton: false,
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
                    renderCart();
                    showSuccess(response.pesan || 'Transaksi berhasil disimpan!');
                    if (tbl_rekap !== null) {
                        tbl_rekap.ajax.reload();
                    }
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

    $('#filter-daterange').daterangepicker({
        startDate: moment(),
        endDate: moment(),
        linkedCalendars: false,
        showCustomRangeLabel: false,
        alwaysShowCalendars: true,
        opens: 'left',
        locale: {
            format: 'DD/MM/YYYY',
            separator: ' - ',
            applyLabel: 'Terapkan',
            cancelLabel: 'Batal',
            daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
        }
    });

    $('#filter-daterange').on('apply.daterangepicker', function(ev, picker) {
        if (tbl_rekap) tbl_rekap.ajax.reload();
    });

    tbl_rekap = $('#tabelRekap').DataTable({
        serverSide: true,
        destroy: true,
        autoWidth: false,
        ajax: {
            url: '<?= getURL('kasir/table') ?>',
            type: 'post',
            dataType: 'json',
            data: function(param) {
                param["<?= csrf_token() ?>"] = decrypter($("#csrf_token").val());
                var drp = $('#filter-daterange').data('daterangepicker');
                if (drp) {
                    param.start_date = drp.startDate.format('YYYY-MM-DD');
                    param.end_date = drp.endDate.format('YYYY-MM-DD');
                }
                return param;
            },
            dataSrc: function(json) {
                if (json.csrfToken) {
                    $("#csrf_token").val(encrypter(json.csrfToken));
                }
                var totalPenjualan = 0;
                var totalMargin = 0;
                if (json.tambahan) {
                    totalPenjualan = json.tambahan.total_penjualan || 0;
                    totalMargin = json.tambahan.total_margin || 0;
                }
                $('#summaryPenjualan').text(formatRupiah(totalPenjualan));
                $('#summaryMargin').text(formatRupiah(totalMargin));
                $('#footerPenjualan').text(formatRupiah(totalPenjualan));
                $('#footerMargin').text(formatRupiah(totalMargin));
                return json.data || [];
            }
        },
        columns: [
            { data: 0, className: 'text-center' },
            { data: 1, className: 'text-center' },
            { data: 2, className: 'text-center' },
            { data: 3, className: 'text-start' },
            { data: 4, className: 'text-center font-mono' },
            { data: 5, className: 'text-center font-mono' }
        ]
    });

    $('#btnRefresh').on('click', function() {
        var drp = $('#filter-daterange').data('daterangepicker');
        if (drp) {
            drp.setStartDate(moment());
            drp.setEndDate(moment());
        }
        if (tbl_rekap) {
            tbl_rekap.ajax.reload();
        }
    });
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
                <input type="number" min="1" value="${item.qty}" onchange="updateQty(${index}, this.value)" class="form-control text-xs text-center py-0.5 px-1 w-14 inline-block font-bold">
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
    $('#cartTotal').text(formatRupiah(total));
    $('#hiddenItems').val(JSON.stringify(cart));
    $('#cartCountBadge').text(totalQty + ' Item');
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
</script>
