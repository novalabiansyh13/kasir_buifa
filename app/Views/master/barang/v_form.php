<?= $this->include('template/v_header') ?>
<?= $this->include('template/v_appbar') ?>
<div class="main-content content margin-t-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">

            <div class="d-flex align-items-center gap-2 mb-3">
                <a href="<?= getURL('barang') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <h4 class="fw-bold mb-0"><?= ($form_type == 'edit' ? 'Edit Barang' : 'Tambah Barang') ?></h4>
            </div>

            <div class="card">
                <div class="card-body">

                    <form id="formBarang" style="padding-inline: 0px;">
                        <input type="hidden" name="id" value="<?= (($form_type == 'edit') ? encrypting($row['id_barang']) : '') ?>">

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="nama_barang">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" name="nama_barang" id="nama_barang"
                                   class="form-control"
                                   value="<?= (($form_type == 'edit') ? esc($row['nama_barang']) : '') ?>"
                                   placeholder="Contoh: Gula Pasir 1 kg" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold" for="harga_beli">Harga Beli (Rp) <span class="text-danger">*</span></label>
                                <input type="number" name="harga_beli" id="harga_beli"
                                       class="form-control"
                                       value="<?= (($form_type == 'edit') ? esc($row['harga_beli']) : '') ?>"
                                       placeholder="0" min="1" step="any" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold" for="harga_jual">Harga Jual (Rp) <span class="text-danger">*</span></label>
                                <input type="number" name="harga_jual" id="harga_jual"
                                       class="form-control"
                                       value="<?= (($form_type == 'edit') ? esc($row['harga_jual']) : '') ?>"
                                       placeholder="0" min="1" step="any" required>
                            </div>
                        </div>

                        <!-- Preview Margin -->
                        <div class="alert alert-info py-2 d-flex align-items-center gap-2" id="previewMargin">
                            <i class="bi bi-info-circle"></i>
                            Margin otomatis:
                            <strong id="marginVal">
                                <?= (($form_type == 'edit') ? idr($row['margin']) : '—') ?>
                            </strong>
                            <small class="text-muted ms-1">(Harga Jual − Harga Beli)</small>
                        </div>

                        <input type="hidden" id="csrf_token_form" name="<?= csrf_token() ?>">

                        <button type="submit" id="btn-subs" class="btn btn-success w-100">
                            <i class="bi bi-save2 me-1"></i><?= ($form_type == 'edit' ? 'Simpan Perubahan' : 'Tambah Barang') ?>
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
<?= $this->include('template/v_footer') ?>
<script>
    $('#harga_beli, #harga_jual').on('input', function() {
        var b = parseFloat($('#harga_beli').val()) || 0;
        var j = parseFloat($('#harga_jual').val()) || 0;
        var m = j - b;
        $('#marginVal').text(formatRupiah(m));
        $('#marginVal').attr('class', m >= 0 ? 'text-success' : 'text-danger');
    });

    $('#formBarang').submit(function(e) {
        e.preventDefault();
        var form_type = "<?= $form_type ?>"
        var link = "<?= getURL('barang/add') ?>"
        if (form_type == 'edit') {
            link = "<?= getURL('barang/update') ?>"
        }
        var old_html = $('#btn-subs').html();
        $('#btn-subs').html('<span class="spinner-border spinner-border-sm me-1"></span>');
        $('#btn-subs').attr('disabled', 'disabled');
        $("button.btn").attr('disabled', 'disabled');

        var csrf = decrypter($("#csrf_token").val());
        $("#csrf_token_form").val(csrf);

        var data = $('#formBarang').serialize();
        $.ajax({
            type: 'post',
            url: link,
            data: data,
            dataType: 'json',
            success: function(response) {
                $('#btn-subs').html(old_html);
                $('#btn-subs').removeAttr('disabled');
                $("button.btn").removeAttr('disabled');
                $("#csrf_token").val(encrypter(response.csrfToken));
                $("#csrf_token_form").val('');
                var notif = 'success';
                if (response.sukses != 1) notif = 'error';
                showNotif(notif, response.pesan);
                if (response.sukses == 1) {
                    window.location.href = "<?= getURL('barang') ?>"
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                $('#btn-subs').html(old_html);
                $('#btn-subs').removeAttr('disabled');
                $("button.btn").removeAttr('disabled');
                showError(thrownError + ", please contact administrator for the further");
            }
        })
        return false;
    })
</script>
