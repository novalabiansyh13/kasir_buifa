<form id="formAccessMenu" class="space-y-4">
    <input type="hidden" name="roleid" value="<?= $roleidEnc ?>">
    <div class="flex items-center justify-between p-3 rounded-xl bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800/60">
        <div>
            <span class="text-xs text-slate-500 dark:text-slate-400">Mengatur Hak Akses Untuk:</span>
            <div class="font-bold text-sm text-sky-800 dark:text-cyan-300"><?= esc($role['rolename']) ?></div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" id="btnCheckAll" class="text-[11px] font-bold text-sky-700 dark:text-cyan-400 hover:underline">
                Pilih Semua
            </button>
            <span class="text-slate-300 dark:text-slate-600">&bull;</span>
            <button type="button" id="btnUncheckAll" class="text-[11px] font-bold text-slate-500 dark:text-slate-400 hover:underline">
                Kosongkan
            </button>
        </div>
    </div>
    <div class="space-y-2 max-h-[50vh] overflow-y-auto pr-1">
        <?php if (!empty($menuTree)): ?>
            <?php foreach ($menuTree as $m): ?>
                <?php $isParentChecked = in_array((int)$m['menuid'], $selectedMenuIds); ?>
                <div class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/80 shadow-2xs space-y-2">
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="menus[]" value="<?= encrypting($m['menuid']) ?>" class="menu-checkbox form-checkbox w-4 h-4 text-sky-600 rounded border-slate-300 dark:border-slate-600 focus:ring-sky-500" <?= ($isParentChecked ? 'checked' : '') ?>>
                        <span class="flex items-center gap-2 text-xs font-bold text-slate-900 dark:text-white">
                            <i class="<?= esc($m['icon']) ?> text-sky-600 dark:text-cyan-400 text-sm"></i>
                            <?= esc($m['menuname']) ?>
                        </span>
                        <span class="text-[11px] text-slate-400 font-mono ms-auto"><?= esc($m['url']) ?></span>
                    </label>
                    <?php if (!empty($m['children'])): ?>
                        <div class="ms-7 ps-3 border-l-2 border-slate-100 dark:border-slate-700 space-y-1.5 pt-1">
                            <?php foreach ($m['children'] as $child): ?>
                                <?php $isChildChecked = in_array((int)$child['menuid'], $selectedMenuIds); ?>
                                <label class="flex items-center gap-2.5 cursor-pointer select-none py-0.5">
                                    <input type="checkbox" name="menus[]" value="<?= encrypting($child['menuid']) ?>" class="menu-checkbox form-checkbox w-3.5 h-3.5 text-sky-600 rounded border-slate-300 dark:border-slate-600 focus:ring-sky-500" <?= ($isChildChecked ? 'checked' : '') ?>>
                                    <span class="text-xs text-slate-700 dark:text-slate-300 font-medium flex items-center gap-1.5">
                                        <i class="<?= esc($child['icon']) ?> text-slate-400 text-xs"></i>
                                        <?= esc($child['menuname']) ?>
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-mono ms-auto"><?= esc($child['url']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-6 text-xs text-slate-400">Tidak ada menu yang terdaftar.</div>
        <?php endif; ?>
    </div>

    <input type="hidden" id="csrf_token_acc" name="<?= csrf_token() ?>">
    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-700/80">
        <button type="button" onclick="closeModal()" class="btn btn-soft-secondary text-xs px-4 py-2 font-bold">
            Tutup
        </button>
        <button type="submit" id="btnSaveAccess" class="btn btn-primary text-xs px-5 py-2 font-bold flex items-center gap-1.5 shadow-xs">
            <i class="bi bi-shield-check"></i> Simpan Hak Akses
        </button>
    </div>
</form>
<script>
$('#btnCheckAll').on('click', function() {
    $('.menu-checkbox').prop('checked', true);
});
$('#btnUncheckAll').on('click', function() {
    $('.menu-checkbox').prop('checked', false);
});

$('#formAccessMenu').submit(function(e) {
    e.preventDefault();
    var btn = $('#btnSaveAccess');
    var old_html = btn.html();
    btn.html('<i class="bi bi-arrow-repeat animate-spin"></i> Menyimpan...').attr('disabled', 'disabled');
    
    var csrf = decrypter($("#csrf_token").val());
    $("#csrf_token_acc").val(csrf);
    
    var data = $('#formAccessMenu').serialize();
    $.ajax({
        type: 'post',
        url: "<?= getURL('usergroup/saveaccess') ?>",
        data: data,
        dataType: 'json',
        success: function(response) {
            btn.html(old_html).removeAttr('disabled');
            if (response.csrfToken) {
                $("#csrf_token").val(encrypter(response.csrfToken));
            }
            $("#csrf_token_acc").val('');

            if (response.sukses == 1) {
                closeModal();
                showNotif('success', response.pesan);
            } else {
                showError(response.pesan || 'Gagal menyimpan hak akses.');
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
