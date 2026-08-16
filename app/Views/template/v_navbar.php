<?php
    $bcItems = [];
    if (!empty($breadcrumb)) {
        if (is_array($breadcrumb)) {
            foreach ($breadcrumb as $b) {
                if (is_array($b)) {
                    foreach ($b as $subB) {
                        if (!empty($subB)) $bcItems[] = $subB;
                    }
                } elseif (!empty($b)) {
                    $bcItems[] = $b;
                }
            }
        } elseif (is_string($breadcrumb)) {
            $bcItems[] = $breadcrumb;
        }
    }
?>
<header class="sticky top-2 z-40 mx-2 sm:mx-4 my-2 bg-white/95 dark:bg-slate-800/95 backdrop-blur-md border border-slate-200/80 dark:border-slate-700 rounded-[14px] shadow-sm py-2 px-3 md:px-4 flex items-center justify-between transition-colors duration-200">
    <div class="flex items-center gap-3">
        <button id="mobile-toggle-btn" type="button" class="md:hidden p-2 rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60 text-lg leading-none focus:outline-none transition-colors cursor-pointer" title="Buka Menu">
            <i class="bi bi-list text-xl"></i>
        </button>
        <div class="flex flex-col justify-center">
            <h1 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white leading-tight mb-0.5">
                <?= esc($section ?? (!empty($bcItems) ? end($bcItems) : 'Dashboard')) ?>
            </h1>
            <nav class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 leading-none">
                <a href="<?= base_url('kasir') ?>" class="text-sky-600 dark:text-cyan-400 hover:underline flex items-center gap-1 no-underline font-semibold" title="Home">
                    <i class="bi bi-house-door-fill text-xs"></i>
                    <span>Home</span>
                </a>
                <?php if (!empty($bcItems)): ?>
                    <?php foreach ($bcItems as $index => $item): ?>
                        <?php $isLast = ($index === count($bcItems) - 1); ?>
                        <span class="text-slate-300 dark:text-slate-600 text-[10px]">&gt;</span>
                        <span class="<?= ($isLast ? 'text-slate-800 dark:text-slate-200 font-semibold' : 'text-slate-500 dark:text-slate-400') ?>">
                            <?= esc($item) ?>
                        </span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </nav>
        </div>
    </div>
    
    <div class="flex items-center gap-2.5">
        <button id="theme-toggle" type="button" class="btn btn-soft-secondary p-2 rounded-xl text-base leading-none cursor-pointer" title="Ganti Mode (Dark / Light)">
            <i id="theme-icon-sun" class="bi bi-sun-fill text-amber-400"></i>
            <i id="theme-icon-moon" class="bi bi-moon-stars-fill text-slate-600"></i>
        </button>
        <div class="relative">
            <button id="profile-menu-btn" type="button" class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700/60 transition-all focus:outline-none cursor-pointer">
                <img src="<?= getUserPhoto() ?>" alt="User Avatar" class="w-8 h-8 rounded-full object-cover border border-slate-300 dark:border-slate-600 shadow-xs">
                <div class="hidden md:block text-left leading-tight">
                    <span class="block text-xs font-bold text-slate-900 dark:text-white"><?= esc(getSession('fullname') ?: 'Bu Ifa') ?></span>
                    <span class="block text-[10px] text-slate-500 dark:text-slate-400 font-medium"><?= esc(getSession('role') ?: 'Kasir') ?></span>
                </div>
                <i class="bi bi-chevron-down text-xs text-slate-400"></i>
            </button>
            <div id="profile-dropdown" class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-[14px] shadow-xl py-2 z-50 hidden transition-all">
                <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-700">
                    <p class="text-xs font-bold text-slate-900 dark:text-white mb-0"><?= esc(getSession('fullname') ?: 'Bu Ifa') ?></p>
                    <p class="text-[11px] text-slate-400 mb-0">@<?= esc(getSession('username') ?: 'buifa') ?></p>
                </div>
                <div class="py-1">
                    <button type="button" onclick="openEditProfileModal()" class="w-full text-left px-4 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-sky-600 dark:hover:text-cyan-400 flex items-center gap-2 transition-colors cursor-pointer">
                        <i class="bi bi-person-gear text-base text-slate-400"></i> Edit Profile
                    </button>
                    <button type="button" onclick="openLogoutModal()" class="w-full text-left px-4 py-2 text-xs text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/10 flex items-center gap-2 transition-colors focus:outline-none cursor-pointer">
                        <i class="bi bi-box-arrow-right text-base text-rose-500 dark:text-rose-400"></i> Logout
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>
