<form id="formSetRole" class="space-y-4">
    <input type="hidden" name="id" value="<?= $idEnc ?>">
    <div class="p-3 rounded-xl bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800/60">
        <span class="text-xs text-slate-500 dark:text-slate-400">Pengguna:</span>
        <div class="font-bold text-sm text-sky-800 dark:text-cyan-300"><?= esc($row['fullname']) ?> <span class="text-xs font-normal text-slate-500 font-mono">(@<?= esc($row['username']) ?>)</span></div>
    </div>
    <div>
        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5 required" for="set_roleid">Pilih User Group / Role</label>
        <select name="roleid" id="set_roleid" class="w-full" required>
            <?php if (!empty($roles)): ?>
                <?php foreach ($roles as $r): ?>
                    <option value="<?= $r['roleid'] ?>" <?= ((int)$row['roleid'] === (int)$r['roleid'] ? 'selected' : '') ?>>
                        <?= esc($r['rolename']) ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
    <input type="hidden" id="csrf_token_srole" name="<?= csrf_token() ?>">
    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-700/80">
        <button type="button" onclick="closeModal()" class="btn btn-soft-secondary text-xs px-4 py-2 font-bold">
            Batal
        </button>
        <button type="submit" id="btnSaveSetRole" class="btn btn-primary text-xs px-5 py-2 font-bold flex items-center gap-1.5 shadow-xs">
            <i class="bi bi-save2-fill"></i> Simpan Role
        </button>
    </div>
</form>
<script>
$(document).ready(function() {
    generateSelect2('#set_roleid', '#globalModal', '', 'Pilih Role User...', '100%', 0, false, {});
});
$('#formSetRole').submit(function(e) {
    e.preventDefault();
    var btn = $('#btnSaveSetRole');
    var old_html = btn.html();
    btn.html('<i class="bi bi-arrow-repeat animate-spin"></i> Menyimpan...').attr('disabled', 'disabled');
    var csrf = decrypter($("#csrf_token").val());
    $("#csrf_token_srole").val(csrf);
    var data = $('#formSetRole').serialize();
    $.ajax({
        type: 'post',
        url: "<?= getURL('user/saverole') ?>",
        data: data,
        dataType: 'json',
        success: function(response) {
            btn.html(old_html).removeAttr('disabled');
            if (response.csrfToken) {
                $("#csrf_token").val(encrypter(response.csrfToken));
            }
            $("#csrf_token_srole").val('');

            if (response.sukses == 1) {
                closeModal();
                showNotif('success', response.pesan);
                if (typeof table !== 'undefined') {
                    table.ajax.reload(null, false);
                } else if ($('.table-master').length) {
                    $('.table-master').DataTable().ajax.reload(null, false);
                }
            } else {
                showError(response.pesan || 'Gagal menyimpan role user.');
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
