        </div><!-- /col-md-10 -->

    </div><!-- /row -->
</div><!-- /container-fluid -->

<footer class="text-center text-muted py-3 mt-4" style="font-size:.8rem;border-top:1px solid #dee2e6;">
    &copy; <?= date('Y') ?> Kasir Pintar Bu Ifa &mdash; Dibuat dengan <i class="bi bi-heart-fill text-danger"></i>
</footer>

<!-- ── Library JS global ───────────────────────────────────────────────────── -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= getURL('js/jquery.min.js') ?>"></script>
<script src="<?= getURL('js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= getURL('js/select2.min.js') ?>"></script>
<script src="<?= getURL('js/editor.js') ?>"></script>

<!-- Token CSRF global (terenkripsi) -->
<input type="hidden" id="csrf_token" value="<?= base_encode(csrf_hash()) ?>">

<script>
/* ══════════════════════════════════════════════════════════════════════════
   Helper Global — Kasir Pintar Bu Ifa (pola HRS)
══════════════════════════════════════════════════════════════════════════ */

var tbl = null;
var tbl_rekap = null;

// ── Notifikasi (Bootstrap toast) ─────────────────────────────────────────────
function showToast(type, msg) {
    var color = (type == 'success') ? 'success' : 'danger';
    var el = $('<div class="toast align-items-center text-bg-' + color + ' border-0 position-fixed top-0 end-0 m-3" role="alert">' +
        '<div class="d-flex">' +
        '<div class="toast-body">' + msg + '</div>' +
        '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>' +
        '</div></div>');
    $('body').append(el);
    var toast = new bootstrap.Toast(el[0], { delay: 3000 });
    toast.show();
    el.on('hidden.bs.toast', function() { el.remove(); });
}
function showNotif(type, msg) { showToast(type, msg); }
function showSuccess(msg) { showToast('success', msg); }
function showError(msg) { showToast('error', msg); }

// ── Navigasi & modal ─────────────────────────────────────────────────────────
function toPage(url, blank) {
    if (blank) {
        window.open(url, '_blank');
    } else {
        window.location.href = url;
    }
}

function close_modal(id) {
    var el = document.getElementById(id);
    if (el) {
        var modal = bootstrap.Modal.getInstance(el);
        if (modal) modal.hide();
    }
}

function modalDelete(title, datas) {
    if (!confirm(title + "\n\nData akan dihapus permanen!")) return;
    $.ajax({
        type: 'post',
        url: datas.link,
        dataType: 'json',
        data: {
            id: datas.id,
            "<?= csrf_token() ?>": decrypter($("#csrf_token").val())
        },
        success: function(response) {
            $("#csrf_token").val(encrypter(response.csrfToken));
            showNotif((response.sukses == 1 ? 'success' : 'error'), response.pesan);
            if (response.sukses == 1) {
                if (tbl !== null) {
                    tbl.ajax.reload();
                } else if (tbl_rekap !== null) {
                    tbl_rekap.ajax.reload();
                }
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            showError(thrownError);
        }
    })
}

// ── Select2 ──────────────────────────────────────────────────────────────────
function generateSelect2(element, dparent, link, placeholder, width, minimumResultsForSearch, allowClear, datas, ismultiple) {
    if (dparent == undefined) dparent = '';
    if (link == undefined) link = '';
    if (placeholder == undefined) placeholder = '';
    if (width == undefined) width = '';
    if (minimumResultsForSearch == undefined) minimumResultsForSearch = 0;
    if (allowClear == undefined) allowClear = true;
    if (datas == undefined) datas = {};
    if (ismultiple == undefined) ismultiple = false;

    var setting_select = {
        allowClear: allowClear,
        multiple: ismultiple,
    };
    if (dparent != '') {
        setting_select.dropdownParent = $(dparent)
    }
    if (placeholder == '') {
        placeholder = 'Choose data';
    }
    setting_select.placeholder = placeholder;
    if (width != '') {
        setting_select.width = width;
    }
    if (minimumResultsForSearch != 0) {
        setting_select.minimumResultsForSearch = minimumResultsForSearch;
    }
    if (link != '') {
        setting_select.ajax = {
            url: link,
            type: "post",
            dataType: "json",
            delay: 250,
            data: function(params) {
                datas.searchTerm = params.term;
                datas["<?= csrf_token() ?>"] = decrypter($("#csrf_token").val());
                return datas;
            },
            processResults: function(response) {
                $("#csrf_token").val(encrypter(response.csrfToken));
                return { results: response.data };
            },
            cache: true,
        };
    }
    $(element).select2(setting_select);
    $(element).on('select2:open', function() {
        $(this).select2('focus');
    });
}

// ── Datatable server-side default ────────────────────────────────────────────
function generateDatatable(selector, options) {
    if (options == undefined) options = {};

    var targetUrl = '';
    if (options.url) {
        targetUrl = options.url;
        delete options.url;
    } else if (options.ajax && options.ajax.url) {
        targetUrl = options.ajax.url;
    } else {
        var uri = '<?= uri_string() ?>';
        if (uri === '' || uri === '/') {
            targetUrl = '<?= getURL('kasir/table') ?>';
        } else {
            targetUrl = '<?= getURL() ?>' + uri.replace(/\/+$/, '') + '/table';
        }
    }

    var defaultOpts = {
        serverSide: true,
        destroy: true,
        autoWidth: false,
        order: [[1, 'asc']],
        ajax: {
            url: targetUrl,
            type: 'post',
            dataType: 'json',
            data: function(param) {
                param["<?= csrf_token() ?>"] = decrypter($("#csrf_token").val());
                return param;
            },
            dataSrc: function(json) {
                if (json.tambahan && json.tambahan.grandtotal !== undefined) {
                    $("#text-grandtotal").text(json.tambahan.grandtotal);
                }
                $("#csrf_token").val(encrypter(json.csrfToken));
                return json.data;
            }
        }
    };

    return $(selector).DataTable($.extend(true, {}, defaultOpts, options));
}

$(document).ready(function() {
    if ($('.table-master').length) {
        tbl = generateDatatable('.table-master', {
            ajax: {
                url: '<?= getURL('barang/table') ?>'
            }
        });
    }
});

// ── Format angka ─────────────────────────────────────────────────────────────
function formatRupiah(n) {
    return 'Rp ' + parseFloat(n).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
}

function toNumeric(n) {
    n = String(n).replace(/[^0-9.,]/g, '');
    n = n.replace(/\./g, '').replace(',', '.');
    return parseFloat(n) || 0;
}

function price_keyup(el) {
    $(el).on('keyup', function() {
        $(this).val(formatRupiah($(this).val()));
    });
}

function exp_number(el) {
    $(el).on('focus', function() {
        this.select();
    });
}
</script>
