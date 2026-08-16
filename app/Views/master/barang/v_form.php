<?= $this->include('template/v_header') ?>
<?= $this->include('template/v_appbar') ?>
<div class="max-w-xl mx-auto space-y-4">
    <div class="flex items-center gap-3">
        <a href="<?= getURL('barang') ?>" class="btn btn-soft-secondary p-2 rounded-xl" title="Kembali">
            <i class="bi bi-arrow-left text-base"></i>
        </a>
        <div>
            <h2 class="font-bold text-sm sm:text-base text-slate-900 dark:text-white mb-0"><?= ($form_type == 'edit' ? 'Edit Data Produk' : 'Tambah Produk Baru') ?></h2>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-0">Master Data &bull; Katalog Toko</p>
        </div>
    </div>
    <div class="card p-6 shadow-sm border border-slate-200/80 dark:border-slate-700 rounded-[14px] bg-white dark:bg-slate-800 space-y-4">
        <form id="formBarang" class="space-y-4">
            <input type="hidden" name="id" value="<?= (($form_type == 'edit') ? encrypting($row['id_barang']) : '') ?>">
            <div>
                <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5 required" for="categoryid">Kategori Produk</label>
                <select name="categoryid" id="categoryid" class="w-full" required></select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5 required" for="nama_barang">Nama Produk</label>
                <input type="text" name="nama_barang" id="nama_barang" class="form-control text-xs" value="<?= (($form_type == 'edit') ? esc($row['nama_barang']) : '') ?>" placeholder="Contoh: Minyak goreng 1L" required>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5 required" for="harga_beli">Harga Beli (Rp)</label>
                    <input type="number" name="harga_beli" id="harga_beli" class="form-control text-xs font-mono" value="<?= (($form_type == 'edit') ? esc($row['harga_beli']) : '') ?>" placeholder="0" min="1" step="any" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5 required" for="harga_jual">Harga Jual (Rp)</label>
                    <input type="number" name="harga_jual" id="harga_jual" class="form-control text-xs font-mono" value="<?= (($form_type == 'edit') ? esc($row['harga_jual']) : '') ?>" placeholder="0" min="1" step="any" required>
                </div>
            </div>
            <div class="p-3.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl flex items-center gap-3 text-xs" id="previewMargin">
                <i class="bi bi-info-circle-fill text-sky-600 dark:text-cyan-400 text-lg shrink-0"></i>
                <div>
                    <span class="text-slate-600 dark:text-slate-400 font-medium">Margin laba otomatis: </span>
                    <strong id="marginVal" class="text-sky-600 dark:text-cyan-400 font-bold font-mono text-sm">
                        <?= (($form_type == 'edit') ? idr($row['margin']) : '—') ?>
                    </strong>
                    <span class="text-slate-400 text-[10px] block">(Harga Jual &minus; Harga Beli)</span>
                </div>
            </div>
            <input type="hidden" id="csrf_token_form" name="<?= csrf_token() ?>">
            <button type="submit" id="btn-subs" class="btn btn-primary w-full py-3 text-xs font-bold flex items-center justify-center gap-2 shadow-md">
                <i class="bi bi-save2-fill text-sm"></i> <?= ($form_type == 'edit' ? 'Simpan Perubahan Produk' : 'Simpan Produk Baru') ?>
            </button>
        </form>
    </div>
</div>
<?= $this->include('template/v_footer') ?>
<script>
    $(document).ready(function() {
        generateSelect2('#categoryid', '', '<?= getURL('barang/getcategory') ?>', 'Pilih Kategori Produk...', '100%', 0, false, {});
        <?php if ($form_type == 'edit' && !empty($row['categoryid'])): ?>
            var opt = new Option("<?= esc($row['categoryname'] ?? '') ?>", "<?= encrypting($row['categoryid']) ?>", true, true);
            $('#categoryid').append(opt).trigger('change');
        <?php endif; ?>
    });

    $('#harga_beli, #harga_jual').on('input', function() {
        var b = parseFloat($('#harga_beli').val()) || 0;
        var j = parseFloat($('#harga_jual').val()) || 0;
        var m = j - b;
        $('#marginVal').text(formatRupiah(m));
        $('#marginVal').attr('class', m >= 0 ? 'text-sky-600 dark:text-cyan-400 font-bold font-mono text-sm' : 'text-rose-600 dark:text-rose-400 font-bold font-mono text-sm');
    });

    $('#formBarang').submit(function(e) {
        e.preventDefault();
        var form_type = "<?= $form_type ?>"
        var link = "<?= getURL('barang/add') ?>"
        if (form_type == 'edit') {
            link = "<?= getURL('barang/update') ?>"
        }
        var btn = $('#btn-subs');
        var old_html = btn.html();
        btn.html('<i class="bi bi-arrow-repeat animate-spin text-base"></i> Menyimpan...').attr('disabled', 'disabled');
        var csrf = decrypter($("#csrf_token").val());
        $("#csrf_token_form").val(csrf);
        var data = $('#formBarang').serialize();
        $.ajax({
            type: 'post',
            url: link,
            data: data,
            dataType: 'json',
            success: function(response) {
                btn.html(old_html).removeAttr('disabled');
                if (response.csrfToken) {
                    $("#csrf_token").val(encrypter(response.csrfToken));
                }
                $("#csrf_token_form").val('');

                var notif = (response.sukses == 1 ? 'success' : 'error');
                showNotif(notif, response.pesan);
                if (response.sukses == 1) {
                    setTimeout(function() {
                        window.location.href = "<?= getURL('barang') ?>";
                    }, 1000);
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                btn.html(old_html).removeAttr('disabled');
                showError(thrownError || 'Terjadi kesalahan sistem.');
            }
        });
        return false;
    });
</script>
