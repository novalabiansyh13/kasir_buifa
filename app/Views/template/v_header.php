<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Kasir Pintar Bu Ifa') ?></title>
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
    <?= $this->include('template/v_import') ?>
</head>
<body class="min-h-screen leading-[1.4] text-slate-800 dark:text-slate-100 font-sans antialiased bg-slate-100 dark:bg-slate-900 transition-colors duration-200">
    <div id="app-shell" class="flex min-h-screen relative">
        <?= $this->include('template/v_sidebar') ?>
        <div id="main-wrapper" class="flex-1 flex flex-col min-w-0 pl-0 md:pl-60 transition-all duration-300">
            <?= $this->include('template/v_navbar') ?>
            <main class="flex-1 p-3 sm:p-4 w-full mx-auto space-y-4">
                <?php if (session()->has('success')): ?>
                    <div class="alert alert-soft-success flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-2.5">
                            <i class="bx bx-check-circle text-lg"></i>
                            <span class="font-medium text-xs"><?= session('success') ?></span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 dark:text-emerald-400 hover:opacity-75 text-base leading-none focus:outline-none">&times;</button>
                    </div>
                <?php endif; ?>
                <?php if (session()->has('error')): ?>
                    <div class="alert alert-soft-danger flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-2.5">
                            <i class="bx bx-error-circle text-lg"></i>
                            <span class="font-medium text-xs"><?= session('error') ?></span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 dark:text-rose-400 hover:opacity-75 text-base leading-none focus:outline-none">&times;</button>
                    </div>
                <?php endif; ?>
