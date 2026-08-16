<form id="formUser" class="space-y-4" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= (($form_type == 'edit') ? encrypting($row['userid']) : '') ?>">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5 required" for="username">Username</label>
            <input type="text" name="username" id="username" class="form-control text-xs" value="<?= (($form_type == 'edit') ? esc($row['username']) : '') ?>" placeholder="kasir1" required <?= ($form_type == 'edit' ? 'readonly' : 'autofocus') ?>>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5 required" for="fullname">Nama Lengkap</label>
            <input type="text" name="fullname" id="fullname" class="form-control text-xs" value="<?= (($form_type == 'edit') ? esc($row['fullname']) : '') ?>" placeholder="Nama Kasir / Admin" required>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5 <?= ($form_type == 'add' ? 'required' : '') ?>" for="password">
                Password <?= ($form_type == 'edit' ? '<span class="text-[10px] font-normal text-slate-400">(Opsional jika ganti)</span>' : '') ?>
            </label>
            <input type="password" name="password" id="password" class="form-control text-xs" placeholder="••••••••" <?= ($form_type == 'add' ? 'required' : '') ?>>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5 required" for="roleid">Role User Group</label>
            <select name="roleid" id="roleid" class="w-full" required></select>
        </div>
    </div>
    <div>
        <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 mb-1.5" for="photo">Foto Profil <span class="text-[10px] font-normal text-slate-400">(Opsional, Max 2MB JPG/PNG)</span></label>
        <input type="file" name="photo" id="photo" accept="image/*" class="form-control text-xs file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
    </div>
    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Status Akun Aktif</span>
            <span class="text-[11px] text-slate-400">User nonaktif tidak dapat login ke sistem.</span>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" name="is_active" value="1" class="sr-only peer" <?= (($form_type == 'add' || ($form_type == 'edit' && $row['is_active'])) ? 'checked' : '') ?>>
            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-slate-600 peer-checked:bg-sky-600"></div>
        </label>
    </div>
    <input type="hidden" id="csrf_token_user" name="<?= csrf_token() ?>">
    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-700/80">
        <button type="button" onclick="closeModal()" class="btn btn-soft-secondary text-xs px-4 py-2 font-bold">
            Batal
        </button>
        <button type="submit" id="btnSaveUser" class="btn btn-primary text-xs px-5 py-2 font-bold flex items-center gap-1.5 shadow-xs">
            <i class="bi bi-save2-fill"></i> <?= ($form_type == 'edit' ? 'Simpan Perubahan' : 'Simpan User') ?>
        </button>
    </div>
</form>
<script>
$(document).ready(function() {
    generateSelect2('#roleid', '#globalModal', '<?= getURL('usergroup/getrole') ?>', 'Pilih Role User...', '100%', 0, false, {});
    <?php if ($form_type == 'edit' && !empty($row['roleid'])): ?>
        var opt = new Option("<?= esc($row['rolename'] ?? 'Role') ?>", "<?= encrypting($row['roleid']) ?>", true, true);
        $('#roleid').append(opt).trigger('change');
    <?php endif; ?>
});

$('#formUser').submit(function(e) {
    e.preventDefault();
    var form_type = "<?= $form_type ?>";
    var link = (form_type == 'edit') ? "<?= getURL('user/update') ?>" : "<?= getURL('user/add') ?>";
    var btn = $('#btnSaveUser');
    var old_html = btn.html();
    btn.html('<i class="bi bi-arrow-repeat animate-spin"></i> Menyimpan...').attr('disabled', 'disabled');
    var csrf = decrypter($("#csrf_token").val());
    $("#csrf_token_user").val(csrf);
    
    var formData = new FormData(this);
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
            $("#csrf_token_user").val('');

            if (response.sukses == 1) {
                closeModal();
                showNotif('success', response.pesan);
                if (typeof table !== 'undefined') {
                    table.ajax.reload(null, false);
                } else if ($('.table-master').length) {
                    $('.table-master').DataTable().ajax.reload(null, false);
                }
            } else {
                showError(response.pesan || 'Gagal menyimpan data user.');
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
