<form id="formBarang" class="space-y-4" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= (($form_type == 'edit') ? encrypting($row['id_barang']) : '') ?>">
    
    <!-- Upload Foto Produk -->
    <div>
        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5">Foto Produk <span class="text-[10px] font-normal text-slate-400 dark:text-slate-500">(Opsional, otomatis WebP maks 400x400)</span></label>
        <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl">
            <div class="w-16 h-16 rounded-xl bg-slate-200 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 overflow-hidden shrink-0 flex items-center justify-center relative group">
                <?php 
                    $currentPhoto = !empty($row['foto']) ? getProductImage($row['foto'], $row['categoryid'] ?? null) : getProductImage(null, $row['categoryid'] ?? null);
                ?>
                <img id="previewFoto" src="<?= $currentPhoto ?>" alt="Preview" class="w-full h-full object-cover">
            </div>
            <div class="space-y-1.5 flex-1 min-w-0">
                <input type="file" name="foto" id="inputFoto" accept="image/png, image/jpeg, image/jpg, image/webp" class="hidden">
                <div class="flex items-center gap-2">
                    <button type="button" onclick="$('#inputFoto').click()" class="btn btn-soft-primary btn-xs flex items-center gap-1 font-bold">
                        <i class="bi bi-camera-fill"></i> Pilih Foto
                    </button>
                    <button type="button" id="btnHapusFoto" onclick="resetFoto()" class="btn btn-soft-danger btn-xs font-bold hidden">
                        <i class="bi bi-x-circle"></i> Batal
                    </button>
                </div>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 mb-0">Format: PNG, JPG, atau WebP (Maksimal 2 MB).</p>
            </div>
        </div>
    </div>

    <!-- Kategori Produk -->
    <div>
        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5 required" for="categoryid">Kategori Produk</label>
        <select name="categoryid" id="categoryid" class="w-full" required></select>
    </div>

    <!-- Nama Produk & Satuan -->
    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
        <div class="sm:col-span-8">
            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5 required" for="nama_barang">Nama Produk</label>
            <input type="text" name="nama_barang" id="nama_barang" class="form-control text-xs" value="<?= (($form_type == 'edit') ? esc($row['nama_barang']) : '') ?>" placeholder="Contoh: Minyak Goreng 1L" required autofocus>
        </div>
        <div class="sm:col-span-4">
            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5 required" for="satuanSelect">Satuan</label>
            <?php
                $presets = ['pcs', 'kg', 'gram', 'renteng', 'sachet', 'botol', 'pack', 'dus', 'butir', 'kaleng', 'liter'];
                $currentSatuan = !empty($row['satuan']) ? strtolower($row['satuan']) : 'pcs';
                $isCustom = !in_array($currentSatuan, $presets);
            ?>
            <select id="satuanSelect" class="form-control text-xs font-semibold cursor-pointer">
                <?php foreach ($presets as $p): ?>
                    <option value="<?= $p ?>" <?= (!$isCustom && $currentSatuan === $p) ? 'selected' : '' ?>><?= $p ?></option>
                <?php endforeach; ?>
                <option value="custom" <?= $isCustom ? 'selected' : '' ?>>Lainnya (Kustom)...</option>
            </select>
        </div>
    </div>

    <!-- Input Satuan Kustom -->
    <div id="containerCustomSatuan" class="<?= $isCustom ? '' : 'hidden' ?> p-2.5 bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800/60 rounded-xl space-y-1">
        <label class="block text-[11px] font-bold text-sky-800 dark:text-sky-300" for="inputCustomSatuan">Ketik Nama Satuan Kustom:</label>
        <input type="text" id="inputCustomSatuan" class="form-control text-xs" placeholder="Contoh: ikat, slop, cup" value="<?= $isCustom ? esc($currentSatuan) : '' ?>">
    </div>
    <input type="hidden" name="satuan" id="satuanHidden" value="<?= esc($currentSatuan) ?>">

    <!-- Harga Beli & Harga Jual -->
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

    <!-- Margin Laba Preview -->
    <div class="p-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl flex items-center gap-3 text-xs" id="previewMargin">
        <i class="bi bi-info-circle-fill text-sky-600 dark:text-cyan-400 text-base shrink-0"></i>
        <div>
            <span class="text-slate-600 dark:text-slate-400 font-medium">Margin laba: </span>
            <strong id="marginVal" class="text-sky-600 dark:text-cyan-400 font-bold font-mono text-sm">
                <?= (($form_type == 'edit') ? idr($row['margin']) : '—') ?>
            </strong>
            <span class="text-slate-400 text-[10px] block">(Harga Jual &minus; Harga Beli)</span>
        </div>
    </div>

    <input type="hidden" id="csrf_token_form" name="<?= csrf_token() ?>">
    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-700/80">
        <button type="button" onclick="closeModal()" class="btn btn-soft-secondary text-xs px-4 py-2 font-bold">
            Batal
        </button>
        <button type="submit" id="btn-subs" class="btn btn-primary text-xs px-5 py-2 font-bold flex items-center gap-1.5 shadow-xs">
            <i class="bi bi-save2-fill"></i> <?= ($form_type == 'edit' ? 'Simpan Perubahan' : 'Simpan Produk') ?>
        </button>
    </div>
</form>

<script>
var originalPhotoSrc = '<?= $currentPhoto ?>';

function resetFoto() {
    $('#inputFoto').val('');
    $('#previewFoto').attr('src', originalPhotoSrc);
    $('#btnHapusFoto').addClass('hidden');
}

$(document).ready(function() {
    generateSelect2('#categoryid', '#globalModal', '<?= getURL('barang/getcategory') ?>', 'Pilih Kategori Produk...', '100%', 0, false, {});
    <?php if ($form_type == 'edit' && !empty($row['categoryid'])): ?>
        var opt = new Option("<?= esc($row['categoryname'] ?? '') ?>", "<?= encrypting($row['categoryid']) ?>", true, true);
        $('#categoryid').append(opt).trigger('change');
    <?php endif; ?>

    // Preview foto yang dipilih
    $('#inputFoto').on('change', function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#previewFoto').attr('src', e.target.result);
                $('#btnHapusFoto').removeClass('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    // Pengaturan satuan
    $('#satuanSelect').on('change', function() {
        var val = $(this).val();
        if (val === 'custom') {
            $('#containerCustomSatuan').removeClass('hidden');
            $('#inputCustomSatuan').focus();
            $('#satuanHidden').val($('#inputCustomSatuan').val().trim() || 'pcs');
        } else {
            $('#containerCustomSatuan').addClass('hidden');
            $('#satuanHidden').val(val);
        }
    });

    $('#inputCustomSatuan').on('input', function() {
        var customVal = $(this).val().trim();
        $('#satuanHidden').val(customVal || 'pcs');
    });
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
    var form_type = "<?= $form_type ?>";
    var link = (form_type == 'edit') ? "<?= getURL('barang/update') ?>" : "<?= getURL('barang/add') ?>";
    var btn = $('#btn-subs');
    var old_html = btn.html();
    btn.html('<i class="bi bi-arrow-repeat animate-spin"></i> Menyimpan...').attr('disabled', 'disabled');
    
    var csrf = decrypter($("#csrf_token").val());
    $("#csrf_token_form").val(csrf);
    
    var formData = new FormData(this);
    formData.set('<?= csrf_token() ?>', csrf);

    $.ajax({
        type: 'post',
        url: link,
        data: formData,
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function(response) {
            btn.html(old_html).removeAttr('disabled');
            if (response.csrfToken) {
                $("#csrf_token").val(encrypter(response.csrfToken));
            }
            $("#csrf_token_form").val('');

            if (response.sukses == 1 || response.success === true) {
                closeModal();
                showNotif('success', response.pesan || response.msg);
                if (typeof table !== 'undefined') {
                    table.ajax.reload(null, false);
                } else if ($('.table-master').length) {
                    $('.table-master').DataTable().ajax.reload(null, false);
                }
            } else {
                showError(response.pesan || response.msg || 'Gagal menyimpan produk.');
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
