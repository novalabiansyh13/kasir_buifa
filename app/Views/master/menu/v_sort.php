<div class="space-y-4">
    <div class="p-3.5 rounded-xl bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800/60 flex items-start gap-2.5 text-xs text-sky-800 dark:text-cyan-300">
        <i class="bi bi-info-circle-fill text-base shrink-0 mt-0.5"></i>
        <div class="space-y-1">
            <div class="font-bold">Panduan Mengatur Urutan & Submenu:</div>
            <div>&bull; <strong>Ubah Urutan:</strong> Drag item menu ke atas atau ke bawah.</div>
            <div>&bull; <strong>Jadikan Submenu:</strong> Drag item lalu geser ke arah <strong>kanan</strong> di bawah menu target (otomatis menjadi anak/submenu).</div>
            <div>&bull; <strong>Keluarkan dari Submenu:</strong> Drag item submenu lalu geser kembali ke arah <strong>kiri</strong>.</div>
        </div>
    </div>
    <div class="dd" id="nestableMenu">
        <ol class="dd-list">
            <?php if (!empty($menuTree)): ?>
                <?php foreach ($menuTree as $item): ?>
                    <li class="dd-item" data-id="<?= $item['menuid'] ?>">
                        <div class="dd-handle">
                            <div class="flex items-center gap-2.5">
                                <i class="bi bi-grip-vertical text-slate-400"></i>
                                <i class="<?= esc($item['icon']) ?> text-sky-600 dark:text-cyan-400"></i>
                                <span class="font-bold text-xs text-slate-900 dark:text-white"><?= esc($item['menuname']) ?></span>
                            </div>
                            <span class="text-[11px] font-mono text-slate-400"><?= esc($item['url']) ?></span>
                        </div>
                        <?php if (!empty($item['children'])): ?>
                            <ol class="dd-list">
                                <?php foreach ($item['children'] as $child): ?>
                                    <li class="dd-item" data-id="<?= $child['menuid'] ?>">
                                        <div class="dd-handle">
                                            <div class="flex items-center gap-2.5">
                                                <i class="bi bi-grip-vertical text-slate-400"></i>
                                                <i class="<?= esc($child['icon']) ?> text-slate-500"></i>
                                                <span class="font-medium text-xs text-slate-800 dark:text-slate-200"><?= esc($child['menuname']) ?></span>
                                            </div>
                                            <span class="text-[10px] font-mono text-slate-400"><?= esc($child['url']) ?></span>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-6 text-xs text-slate-400">Tidak ada menu untuk diurutkan.</div>
            <?php endif; ?>
        </ol>
    </div>
    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-700/80">
        <button type="button" onclick="closeModal()" class="btn btn-soft-secondary text-xs px-4 py-2 font-bold">
            Batal
        </button>
        <button type="button" id="btnSaveSortMenu" class="btn btn-primary text-xs px-5 py-2 font-bold flex items-center gap-1.5 shadow-xs">
            <i class="bi bi-check2-circle"></i> Simpan Urutan
        </button>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#nestableMenu').nestable({
        maxDepth: 2
    });
    $('#btnSaveSortMenu').on('click', function() {
        var serialized = $('#nestableMenu').nestable('serialize');
        var btn = $(this);
        var old_html = btn.html();
        btn.html('<i class="bi bi-arrow-repeat animate-spin"></i> Menyimpan...').attr('disabled', 'disabled');
        var postData = {
            order: JSON.stringify(serialized)
        };
        postData["<?= csrf_token() ?>"] = decrypter($("#csrf_token").val());
        $.ajax({
            type: 'post',
            url: "<?= getURL('menu/saveorder') ?>",
            data: postData,
            dataType: 'json',
            success: function(response) {
                btn.html(old_html).removeAttr('disabled');
                if (response.csrfToken) {
                    $("#csrf_token").val(encrypter(response.csrfToken));
                }
                if (response.sukses == 1) {
                    closeModal();
                    showNotif('success', response.pesan);
                    if (typeof table !== 'undefined') {
                        table.ajax.reload(null, false);
                    } else if ($('.table-master').length) {
                        $('.table-master').DataTable().ajax.reload(null, false);
                    }
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    showError(response.pesan || 'Gagal menyimpan urutan menu.');
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                btn.html(old_html).removeAttr('disabled');
                showError(thrownError || 'Terjadi kesalahan sistem.');
            }
        });
    });
});
</script>
