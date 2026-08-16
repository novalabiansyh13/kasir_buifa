<form id="formMenu" class="space-y-4">
    <input type="hidden" name="id" value="<?= (($form_type == 'edit') ? encrypting($row['menuid']) : '') ?>">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5 required" for="menuname">Nama Menu</label>
            <input type="text" name="menuname" id="menuname" class="form-control text-xs" value="<?= (($form_type == 'edit') ? esc($row['menuname']) : '') ?>" placeholder="Contoh: Laporan Penjualan" required autofocus>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5 required" for="url">URL / Route</label>
            <input type="text" name="url" id="url" class="form-control text-xs font-mono" value="<?= (($form_type == 'edit') ? esc($row['url']) : '') ?>" placeholder="laporan" required>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5" for="icon">Icon</label>
            <div class="relative">
                <input type="text" name="icon" id="inputMenuIcon" class="form-control text-xs font-mono ps-9" value="<?= (($form_type == 'edit') ? esc($row['icon']) : 'bi bi-grid') ?>" placeholder="bi bi-grid">
                <i id="previewIcon" class="<?= (($form_type == 'edit') ? esc($row['icon']) : 'bi bi-grid') ?> absolute left-3 top-2.5 text-sky-600 dark:text-cyan-400 text-sm"></i>
            </div>
            <span class="text-[10px] text-slate-400 mt-1 block">Contoh: bi bi-box-seam, bi bi-tags, bi bi-people</span>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5" for="parentid">Parent Menu</label>
            <select name="parentid" id="parentid" class="w-full"></select>
        </div>
    </div>
    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Status Menu Aktif</span>
            <span class="text-[11px] text-slate-400">Menu nonaktif tidak akan ditampilkan di sidebar.</span>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" name="is_active" value="1" class="sr-only peer" <?= (($form_type == 'add' || ($form_type == 'edit' && $row['is_active'])) ? 'checked' : '') ?>>
            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-slate-600 peer-checked:bg-sky-600"></div>
        </label>
    </div>
    <input type="hidden" id="csrf_token_menu" name="<?= csrf_token() ?>">
    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-700/80">
        <button type="button" onclick="closeModal()" class="btn btn-soft-secondary text-xs px-4 py-2 font-bold">
            Batal
        </button>
        <button type="submit" id="btnSaveMenu" class="btn btn-primary text-xs px-5 py-2 font-bold flex items-center gap-1.5 shadow-xs">
            <i class="bi bi-save2-fill"></i> <?= ($form_type == 'edit' ? 'Simpan Perubahan' : 'Simpan Menu') ?>
        </button>
    </div>
</form>
<script>
$(document).ready(function() {
    var currentMenuId = "<?= ($form_type == 'edit' && !empty($row['menuid'])) ? encrypting($row['menuid']) : '' ?>";
    generateSelect2('#parentid', '#globalModal', '<?= getURL('menu/getmenu') ?>', 'Pilih Parent Menu...', '100%', 0, true, { exceptId: currentMenuId });
    <?php if ($form_type == 'edit'): ?>
        <?php if (!empty($row['parentid']) && (int)$row['parentid'] > 0): ?>
            var opt = new Option("<?= esc($row['parent_name'] ?? 'Parent Menu') ?>", "<?= encrypting($row['parentid']) ?>", true, true);
            $('#parentid').append(opt).trigger('change');
        <?php else: ?>
            var opt = new Option("kosong (menu utama)", "<?= encrypting(0) ?>", true, true);
            $('#parentid').append(opt).trigger('change');
        <?php endif; ?>
    <?php endif; ?>
});

$('#inputMenuIcon').on('input', function() {
    var val = $(this).val().trim() || 'bi bi-grid';
    $('#previewIcon').attr('class', val + ' absolute left-3 top-2.5 text-sky-600 dark:text-cyan-400 text-sm');
});

$('#formMenu').submit(function(e) {
    e.preventDefault();
    var form_type = "<?= $form_type ?>";
    var link = (form_type == 'edit') ? "<?= getURL('menu/update') ?>" : "<?= getURL('menu/add') ?>";
    var btn = $('#btnSaveMenu');
    var old_html = btn.html();
    btn.html('<i class="bi bi-arrow-repeat animate-spin"></i> Menyimpan...').attr('disabled', 'disabled');
    var csrf = decrypter($("#csrf_token").val());
    $("#csrf_token_menu").val(csrf);
    var data = $('#formMenu').serialize();
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
            $("#csrf_token_menu").val('');

            if (response.sukses == 1) {
                closeModal();
                showNotif('success', response.pesan);
                if (typeof table !== 'undefined') {
                    table.ajax.reload(null, false);
                } else if ($('.table-master').length) {
                    $('.table-master').DataTable().ajax.reload(null, false);
                }
            } else {
                showError(response.pesan || 'Gagal menyimpan menu.');
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
