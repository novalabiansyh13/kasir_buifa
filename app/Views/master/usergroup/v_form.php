<form id="formUsergroup" class="space-y-4">
    <input type="hidden" name="id" value="<?= (($form_type == 'edit') ? encrypting($row['roleid']) : '') ?>">
    <div>
        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5 required" for="rolename">Nama User Group / Role</label>
        <input type="text" name="rolename" id="rolename" class="form-control text-xs" value="<?= (($form_type == 'edit') ? esc($row['rolename']) : '') ?>" placeholder="Contoh: Administrator, Kasir, Supervisor..." required autofocus>
    </div>
    <input type="hidden" id="csrf_token_ug" name="<?= csrf_token() ?>">
    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-700/80">
        <button type="button" onclick="closeModal()" class="btn btn-soft-secondary text-xs px-4 py-2 font-bold">
            Batal
        </button>
        <button type="submit" id="btnSaveUsergroup" class="btn btn-primary text-xs px-5 py-2 font-bold flex items-center gap-1.5 shadow-xs">
            <i class="bi bi-save2-fill"></i> <?= ($form_type == 'edit' ? 'Simpan Perubahan' : 'Simpan User Group') ?>
        </button>
    </div>
</form>
<script>
$('#formUsergroup').submit(function(e) {
    e.preventDefault();
    var form_type = "<?= $form_type ?>";
    var link = (form_type == 'edit') ? "<?= getURL('usergroup/update') ?>" : "<?= getURL('usergroup/add') ?>";
    var btn = $('#btnSaveUsergroup');
    var old_html = btn.html();
    btn.html('<i class="bi bi-arrow-repeat animate-spin"></i> Menyimpan...').attr('disabled', 'disabled');
    var csrf = decrypter($("#csrf_token").val());
    $("#csrf_token_ug").val(csrf);
    var data = $('#formUsergroup').serialize();
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
            $("#csrf_token_ug").val('');

            if (response.sukses == 1) {
                closeModal();
                showNotif('success', response.pesan);
                if (typeof table !== 'undefined') {
                    table.ajax.reload(null, false);
                } else if ($('.table-master').length) {
                    $('.table-master').DataTable().ajax.reload(null, false);
                }
            } else {
                showError(response.pesan || 'Gagal menyimpan data.');
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
