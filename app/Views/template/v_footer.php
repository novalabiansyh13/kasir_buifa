            </main>
            <footer class="mt-4 py-4 px-6 border-t border-slate-200 dark:border-slate-800 bg-transparent text-center text-xs text-slate-500 dark:text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-2 shrink-0">
                <span>&copy; 2026 Smart Cashier. All Right Reserved.</span>
                <span class="text-[11px] text-slate-400 dark:text-slate-500">SuperNova &bull; <span class="text-sky-600 dark:text-cyan-400 font-mono font-bold">v1.0.0</span></span>
            </footer>
        </div>
    </div>

    <!-- Modal Confirm Logout -->
    <div id="modal-logout" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm hidden items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 w-full max-w-sm shadow-2xl overflow-hidden text-slate-800 dark:text-slate-200 text-center p-6 space-y-4">
            <div class="w-14 h-14 bg-rose-50 dark:bg-rose-500/10 border border-rose-100 dark:border-rose-500/20 text-rose-500 dark:text-rose-400 rounded-2xl flex items-center justify-center mx-auto text-2xl">
                <i class="bi bi-box-arrow-right"></i>
            </div>
            <div>
                <h3 class="font-bold text-base text-slate-900 dark:text-white mb-1">Logout</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Apakah Anda yakin ingin mengakhiri sesi kasir dan keluar dari aplikasi?</p>
            </div>
            <div class="flex items-center justify-center gap-3 pt-2">
                <button type="button" onclick="closeLogoutModal()" class="w-1/2 py-2.5 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 border border-slate-200 dark:border-slate-600 transition-colors cursor-pointer">Batal</button>
                <a href="<?= base_url('logout') ?>" class="w-1/2 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-md transition-all no-underline inline-flex items-center justify-center cursor-pointer">Ya, Logout</a>
            </div>
        </div>
    </div>

    <!-- Modal Edit Profile -->
    <div id="modal-edit-profile" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm hidden items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 w-full max-w-md shadow-2xl overflow-hidden text-slate-800 dark:text-slate-200">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between bg-slate-50 dark:bg-slate-900">
                <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2 mb-0">
                    <i class="bi bi-person-gear text-sky-600 dark:text-cyan-400 text-base"></i> Edit Profile & Password
                </h3>
                <button type="button" onclick="closeEditProfileModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg leading-none focus:outline-none cursor-pointer">&times;</button>
            </div>

            <form id="form-edit-profile" enctype="multipart/form-data" class="p-6 space-y-4">
                <?= csrf_field() ?>
                <div class="flex items-center gap-4 p-3 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-200 dark:border-slate-700">
                    <img src="<?= getUserPhoto() ?>" alt="Current Avatar" class="w-12 h-12 rounded-full object-cover border border-slate-200 dark:border-slate-600 shadow-sm">
                    <div>
                        <p class="text-xs font-bold text-slate-800 dark:text-white mb-0"><?= esc(getSession('fullname') ?: 'Bu Ifa') ?></p>
                        <p class="text-[11px] text-slate-400 mb-0">Ubah foto profil di bawah</p>
                    </div>
                </div>
                <div>
                    <label for="profile_fullname" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap</label>
                    <input type="text" id="profile_fullname" name="fullname" value="<?= esc(getSession('fullname') ?: 'Bu Ifa') ?>" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 focus:border-sky-500 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white outline-none" required>
                </div>
                <div>
                    <label for="profile_username" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Username</label>
                    <input type="text" id="profile_username" name="username" value="<?= esc(getSession('username') ?: 'buifa') ?>" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 focus:border-sky-500 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white outline-none" required>
                </div>
                <div>
                    <label for="profile_password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Password Baru <span class="text-slate-400 font-normal">(Kosongkan jika tidak diubah)</span></label>
                    <input type="password" id="profile_password" name="password" placeholder="Masukkan password baru..." class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 focus:border-sky-500 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white outline-none">
                </div>
                <div>
                    <label for="profile_photo" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Foto Profil Baru (PNG / JPG)</label>
                    <input type="file" id="profile_photo" name="photo" accept="image/*" class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-sky-500/10 file:text-sky-600 dark:file:text-cyan-400 hover:file:bg-sky-500/20">
                </div>
                <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-700">
                    <button type="button" onclick="closeEditProfileModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-white transition-colors cursor-pointer">Batal</button>
                    <button type="submit" id="btn-save-profile" class="px-4 py-2 rounded-xl text-xs font-bold bg-sky-600 hover:bg-sky-700 text-white shadow-md transition-all cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    <input type="hidden" id="csrf_token" value="<?= base_encode(csrf_hash()) ?>">
    <script>
    var tbl = null;
    var tbl_rekap = null;
    function encrypter(text) {
        var txt = text;
        for (var i = 0; i < 6; i++) {
            txt = btoa(txt);
        }
        return txt;
    }

    function decrypter(text) {
        var txt = text;
        for (var i = 0; i < 6; i++) {
            txt = atob(txt);
        }
        return txt;
    }

    function formatRupiah(number) {
        var n = parseFloat(number) || 0;
        return 'Rp ' + n.toLocaleString('id-ID');
    }

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true,
        customClass: {
            popup: 'hyperui-swal'
        },
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    function showToast(type, msg) {
        Toast.fire({
            icon: (type === 'success' ? 'success' : 'error'),
            title: msg
        });
    }
    function showNotif(type, msg) { showToast(type, msg); }
    function showSuccess(msg) { showToast('success', msg); }
    function showError(msg) { showToast('error', msg); }

    function toPage(url, blank) {
        if (blank) {
            window.open(url, '_blank');
        } else {
            window.location.href = url;
        }
    }

    // Modal Delete Helper
    function modalDelete(title, datas) {
        Swal.fire({
            title: title,
            text: "Data akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="bi bi-trash-fill"></i> Ya, Hapus Data',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'hyperui-swal',
                title: 'hyperui-swal-title',
                htmlContainer: 'hyperui-swal-text'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'post',
                    url: datas.link,
                    dataType: 'json',
                    data: {
                        id: datas.id,
                        "<?= csrf_token() ?>": decrypter($("#csrf_token").val())
                    },
                    success: function(response) {
                        if (response.csrfToken) {
                            $("#csrf_token").val(encrypter(response.csrfToken));
                        }
                        showNotif((response.sukses == 1 ? 'success' : 'error'), response.pesan);
                        if (response.sukses == 1) {
                            if (typeof tbl !== 'undefined' && tbl !== null) {
                                tbl.ajax.reload();
                            } else if (typeof tbl_rekap !== 'undefined' && tbl_rekap !== null) {
                                tbl_rekap.ajax.reload();
                            }
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        showError(thrownError || 'Terjadi kesalahan server.');
                    }
                });
            }
        });
    }

    // Select2 Helper
    function generateSelect2(element, dparent, link, placeholder, width, minimumResultsForSearch, allowClear, datas, ismultiple) {
        if (dparent == undefined) dparent = '';
        if (link == undefined) link = '';
        if (placeholder == undefined) placeholder = 'Pilih data';
        if (width == undefined) width = '100%';
        if (minimumResultsForSearch == undefined) minimumResultsForSearch = 0;
        if (allowClear == undefined) allowClear = true;
        if (datas == undefined) datas = {};
        if (ismultiple == undefined) ismultiple = false;

        var setting_select = {
            allowClear: allowClear,
            multiple: ismultiple,
            placeholder: placeholder,
            width: width
        };
        if (dparent !== '') {
            setting_select.dropdownParent = $(dparent);
        }
        if (minimumResultsForSearch !== 0) {
            setting_select.minimumResultsForSearch = minimumResultsForSearch;
        }
        if (link !== '') {
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
                    if (response.csrfToken) {
                        $("#csrf_token").val(encrypter(response.csrfToken));
                    }
                    return { results: response.data };
                },
                cache: true,
            };
        }
        $(element).select2(setting_select);
    }

    // DataTable Master Default Helper
    $(document).ready(function() {
        if ($('.table-master').length > 0) {
            var uri = '<?= uri_string() ?>';
            var targetUrl = '<?= getURL() ?>' + uri.replace(/\/+$/, '') + '/table';
            var colDefs = [];
            $('.table-master thead th').each(function(idx) {
                var thClass = $(this).attr('class') || '';
                var alignClass = '';
                if (thClass.includes('text-center')) alignClass += ' text-center';
                if (thClass.includes('text-end') || thClass.includes('text-right')) alignClass += ' text-end';
                if (thClass.includes('text-start') || thClass.includes('text-left')) alignClass += ' text-start';
                if (alignClass.trim() !== '') {
                    colDefs.push({ targets: idx, className: alignClass.trim() });
                }
            });
            tbl = $('.table-master').DataTable({
                serverSide: true,
                destroy: true,
                autoWidth: false,
                columnDefs: colDefs,
                ajax: {
                    url: targetUrl,
                    type: 'post',
                    dataType: 'json',
                    data: function(param) {
                        param["<?= csrf_token() ?>"] = decrypter($("#csrf_token").val());
                        return param;
                    },
                    dataSrc: function(json) {
                        if (json.csrfToken) {
                            $("#csrf_token").val(encrypter(json.csrfToken));
                        }
                        return json.data || [];
                    }
                }
            });
        }
        
        $('#form-edit-profile').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append("<?= csrf_token() ?>", decrypter($("#csrf_token").val()));

            const btn = $('#btn-save-profile');
            const originalText = btn.text();
            btn.prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: '<?= base_url('profile/update') ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(res) {
                    btn.prop('disabled', false).text(originalText);
                    if (res.csrfToken) {
                        $("#csrf_token").val(encrypter(res.csrfToken));
                    }
                    if (res.success) {
                        closeEditProfileModal();
                        Swal.fire({
                            icon: 'success',
                            title: 'Profil Diperbarui',
                            text: res.msg,
                            timer: 1500,
                            showConfirmButton: false,
                            customClass: { popup: 'hyperui-swal', title: 'hyperui-swal-title', htmlContainer: 'hyperui-swal-text' }
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.msg,
                            confirmButtonColor: '#e11d48',
                            customClass: { popup: 'hyperui-swal', title: 'hyperui-swal-title', htmlContainer: 'hyperui-swal-text' }
                        });
                    }
                },
                error: function() {
                    btn.prop('disabled', false).text(originalText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan Server',
                        text: 'Gagal memperbarui profil.',
                        confirmButtonColor: '#e11d48',
                        customClass: { popup: 'hyperui-swal', title: 'hyperui-swal-title', htmlContainer: 'hyperui-swal-text' }
                    });
                }
            });
        });
    });
    </script>
</body>
</html>
