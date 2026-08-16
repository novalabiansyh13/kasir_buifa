<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Login - Kasir Bu Ifa') ?></title>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            if (savedTheme === 'light') {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
            } else {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            }
        })();
    </script>
    <link rel="stylesheet" href="<?= base_url('public/css/template.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/css/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/css/sweetalert2.min.css') ?>">
    <script src="<?= base_url('public/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('public/js/sweetalert2.all.min.js') ?>"></script>
    <style>
        .swal2-popup.hyperui-swal {
            background: #111c30 !important;
            border: 1px solid #1e293b !important;
            border-radius: 1rem !important;
            color: #f8fafc !important;
        }
        html.light .swal2-popup.hyperui-swal {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            color: #0f172a !important;
        }
    </style>
</head>
<body class="bg-slate-100 dark:bg-[#0a0f1d] min-h-screen flex items-center justify-center p-4 font-sans text-slate-800 dark:text-slate-100 transition-colors duration-200 selection:bg-sky-500 selection:text-white relative">
    <div class="fixed top-4 right-4 z-50">
        <button id="theme-toggle" type="button" class="btn btn-soft-secondary p-2.5 rounded-xl text-base leading-none cursor-pointer shadow-sm" title="Ganti Mode (Dark / Light)">
            <i id="theme-icon-sun" class="bi bi-sun-fill text-amber-400"></i>
            <i id="theme-icon-moon" class="bi bi-moon-stars-fill text-slate-600"></i>
        </button>
    </div>
    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-white dark:bg-[#111c30] border border-slate-200/80 dark:border-[#1e293b] text-[#0284c7] dark:text-cyan-400 rounded-2xl shadow-md dark:shadow-xl mb-3 transition-colors duration-200">
                <i class="bi bi-shop-window text-2xl"></i>
            </div>
            <h1 class="text-xl font-extrabold tracking-wide text-slate-900 dark:text-white">Smart Cashier <span class="text-[#0284c7] dark:text-cyan-400">Bu Ifa</span></h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Sistem Aplikasi Kasir Digital &bull; Enterprise Cashier System</p>
        </div>
        <div class="bg-white dark:bg-[#111c30] border border-slate-200/80 dark:border-[#1e293b] rounded-2xl p-6 sm:p-8 shadow-xl dark:shadow-2xl transition-colors duration-200">
            <h2 class="text-base font-bold text-slate-900 dark:text-white mb-5 flex items-center gap-2">
                <i class="bi bi-shield-lock text-[#0284c7] dark:text-cyan-400"></i> Masuk Akun Kasir
            </h2>
            <form id="form-login" autocomplete="off" class="space-y-4">
                <?= csrf_field() ?>
                <div>
                    <label for="username" class="block text-slate-700 dark:text-slate-300 text-xs font-semibold mb-1.5">Username</label>
                    <div class="relative">
                        <i class="bi bi-person text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 text-sm"></i>
                        <input type="text" id="username" name="username" class="w-full bg-slate-50 dark:bg-[#0b1322] border border-slate-300 dark:border-[#1e293b] focus:border-[#0284c7] dark:focus:border-cyan-500 text-slate-900 dark:text-white rounded-xl py-2.5 pl-10 pr-4 text-xs outline-none transition-all placeholder-slate-400 dark:placeholder-slate-500" placeholder="ex: @supernova" required autofocus>
                    </div>
                </div>
                <div>
                    <label for="password" class="block text-slate-700 dark:text-slate-300 text-xs font-semibold mb-1.5">Password</label>
                    <div class="relative">
                        <i class="bi bi-key text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 text-sm"></i>
                        <input type="password" id="password" name="password" class="w-full bg-slate-50 dark:bg-[#0b1322] border border-slate-300 dark:border-[#1e293b] focus:border-[#0284c7] dark:focus:border-cyan-500 text-slate-900 dark:text-white rounded-xl py-2.5 pl-10 pr-10 text-xs outline-none transition-all placeholder-slate-400 dark:placeholder-slate-500" placeholder="********" required>
                        <button type="button" id="btn-toggle-pwd" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-sm focus:outline-none cursor-pointer">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" id="btn-submit" class="w-full bg-[#0284c7] hover:bg-[#0369a1] text-white font-bold py-3 px-4 rounded-xl text-xs shadow-lg shadow-sky-600/20 dark:shadow-cyan-950/50 transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                    <i class="bi bi-box-arrow-in-right text-base"></i> Masuk Aplikasi
                </button>
            </form>
        </div>
        <div class="text-center mt-6 text-[11px] text-slate-400 dark:text-slate-500">
            Copyright &copy; 2026 &bull; SuperNova POS Architecture
        </div>
    </div>
    <script>
        const themeToggleBtn = document.getElementById('theme-toggle');
        const sunIcon = document.getElementById('theme-icon-sun');
        const moonIcon = document.getElementById('theme-icon-moon');
        function applyTheme(isDark) {
            if (isDark) {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
                if (sunIcon) sunIcon.style.display = 'inline-block';
                if (moonIcon) moonIcon.style.display = 'none';
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
                if (sunIcon) sunIcon.style.display = 'none';
                if (moonIcon) moonIcon.style.display = 'inline-block';
            }
        }
        const savedTheme = localStorage.getItem('theme') || 'dark';
        applyTheme(savedTheme === 'dark');
        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const isDark = document.documentElement.classList.contains('dark');
                const newTheme = isDark ? 'light' : 'dark';
                localStorage.setItem('theme', newTheme);
                applyTheme(newTheme === 'dark');
            });
        }

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
            customClass: { popup: 'hyperui-swal' },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        $(document).ready(function() {
            $('#btn-toggle-pwd').on('click', function() {
                const pwdInput = $('#password');
                const icon = $(this).find('i');
                if (pwdInput.attr('type') === 'password') {
                    pwdInput.attr('type', 'text');
                    icon.removeClass('bi-eye').addClass('bi-eye-slash');
                } else {
                    pwdInput.attr('type', 'password');
                    icon.removeClass('bi-eye-slash').addClass('bi-eye');
                }
            });

            $('#form-login').on('submit', function(e) {
                e.preventDefault();
                const btn = $('#btn-submit');
                const originalHtml = btn.html();

                btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat animate-spin text-base"></i> Memproses...');

                $.ajax({
                    url: '<?= base_url('login/process') ?>',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            Toast.fire({
                                icon: 'success',
                                title: 'Login Berhasil!',
                                text: res.msg
                            });
                            setTimeout(function() {
                                window.location.href = res.redirect || '<?= base_url('kasir') ?>';
                            }, 1000);
                        } else {
                            btn.prop('disabled', false).html(originalHtml);
                            Toast.fire({
                                icon: 'error',
                                title: 'Gagal Login',
                                text: res.msg
                            });
                        }
                    },
                    error: function() {
                        btn.prop('disabled', false).html(originalHtml);
                        Toast.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Gagal terhubung ke server.'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
