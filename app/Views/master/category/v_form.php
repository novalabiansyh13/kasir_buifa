<form id="formCategory" class="space-y-4">
    <input type="hidden" name="id" value="<?= (($form_type == 'edit') ? encrypting($row['categoryid']) : '') ?>">
    <div>
        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5 required" for="categoryname">Nama Kategori</label>
        <input type="text" name="categoryname" id="categoryname" class="form-control text-xs" value="<?= (($form_type == 'edit') ? esc($row['categoryname']) : '') ?>" placeholder="Contoh: Sembako, Snack, Minuman..." required autofocus>
    </div>
    <input type="hidden" id="csrf_token_cat" name="<?= csrf_token() ?>">
    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-700/80">
        <button type="button" onclick="closeModal()" class="btn btn-soft-secondary text-xs px-4 py-2 font-bold">
            Batal
        </button>
        <button type="submit" id="btnSaveCategory" class="btn btn-primary text-xs px-5 py-2 font-bold flex items-center gap-1.5 shadow-xs">
            <i class="bi bi-save2-fill"></i> <?= ($form_type == 'edit' ? 'Simpan Perubahan' : 'Simpan Kategori') ?>
        </button>
    </div>
</form>
<script>
$('#formCategory').submit(function(e) {
    e.preventDefault();
    var form_type = "<?= $form_type ?>";
    var link = (form_type == 'edit') ? "<?= getURL('category/update') ?>" : "<?= getURL('category/add') ?>";
    var btn = $('#btnSaveCategory');
    var old_html = btn.html();
    btn.html('<i class="bi bi-arrow-repeat animate-spin"></i> Menyimpan...').attr('disabled', 'disabled');
    var csrf = decrypter($("#csrf_token").val());
    $("#csrf_token_cat").val(csrf);
    var data = $('#formCategory').serialize();
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
            $("#csrf_token_cat").val('');

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
