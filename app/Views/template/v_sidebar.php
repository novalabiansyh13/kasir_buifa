<div id="sidebar-backdrop" class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs opacity-0 pointer-events-none transition-opacity duration-300 md:hidden" aria-hidden="true"></div>
<aside id="sidebar" class="group/sidebar fixed inset-y-0 left-0 z-50 flex flex-col w-60 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 -translate-x-full md:translate-x-0 shadow-2xl md:shadow-lg rounded-r-[18px] border-r border-slate-200/60 dark:border-slate-700" aria-label="Enterprise Navigation">
    <div class="p-3.5 border-b border-slate-100 dark:border-slate-700/60 flex items-center justify-between relative group sidebar-brand">
        <a href="<?= base_url('kasir') ?>" class="flex items-center gap-2.5 overflow-hidden no-underline">
            <div class="w-9 h-9 rounded-xl bg-[#0284c7] text-white flex items-center justify-center font-bold shadow-xs shrink-0 text-base">
                <i class="bi bi-shop-window"></i>
            </div>
            <div class="leading-tight overflow-hidden sidebar-text">
                <div class="font-bold text-slate-900 dark:text-white text-xs truncate flex items-center gap-1.5">
                    <span>Smart Cashier</span>
                    <span class="px-1.5 py-0.2 bg-sky-500/15 text-sky-600 dark:text-cyan-400 text-[9px] font-bold rounded-full">Live POS</span>
                </div>
                <span class="text-[11px] text-slate-400 dark:text-slate-500 truncate block font-normal">Modern POS Suite</span>
            </div>
        </a>
        <button type="button" id="sidebar-close-btn" class="md:hidden p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
            <i class="bi bi-x-lg text-sm"></i>
        </button>
    </div>
    <button id="sidebar-toggle-btn" type="button" class="hidden md:flex absolute -right-3.5 top-5 z-50 w-7 h-7 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:text-sky-600 dark:hover:text-cyan-400 rounded-full shadow-md items-center justify-center cursor-pointer transition-transform duration-200 hover:scale-110 focus:outline-none" title="Buka / Tutup Sidebar">
        <i id="sidebar-chevron-icon" class="bi bi-chevron-left text-xs transition-transform duration-300"></i>
    </button>
    <div class="flex-1 overflow-y-auto px-3 py-3 space-y-3 font-normal custom-scrollbar sidebar-menu-wrapper">
        <?php 
            $userMenus = generateSidebarMenus(); 
            $uri = service('uri');
            $currentSegment = $uri->getSegment(1) ?? 'kasir';
        ?>
        <div>
            <div class="px-3 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2 sidebar-header-text">
                MAIN / OVERVIEW
            </div>
            <nav class="space-y-1">
                <?php if (!empty($userMenus)): ?>
                    <?php foreach ($userMenus as $menu): ?>
                        <?php if (!empty($menu['children'])): ?>
                            <?php 
                                $isChildActive = in_array(strtolower($currentSegment), array_map('strtolower', array_column($menu['children'], 'url')));
                                $collapseId = 'submenu-' . $menu['menuid'];
                            ?>
                            <div class="group-flyout relative">
                                <button type="button" 
                                    onclick="toggleSubmenu('<?= $collapseId ?>', this)" 
                                    class="sidebar-item-btn w-full flex items-center justify-between px-3 py-2.5 rounded-[10px] text-xs font-semibold transition-all text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60 cursor-pointer select-none <?= ($isChildActive ? 'bg-sky-50/70 dark:bg-sky-950/40 text-sky-800 dark:text-cyan-300' : '') ?>"
                                    title="<?= esc($menu['menuname']) ?>">
                                    <div class="flex items-center gap-2.5">
                                        <i class="<?= esc($menu['icon']) ?> text-base text-sky-600 dark:text-cyan-400 shrink-0"></i>
                                        <span class="truncate sidebar-text"><?= esc($menu['menuname']) ?></span>
                                    </div>
                                    <i class="bi bi-chevron-down text-xs transition-transform duration-200 submenu-chevron <?= ($isChildActive ? 'rotate-180' : '') ?>"></i>
                                </button>
                                <div id="<?= $collapseId ?>" class="submenu-inline ms-3 ps-2.5 border-l-2 border-slate-200 dark:border-slate-700/80 space-y-1 transition-all duration-200 <?= ($isChildActive ? '' : 'hidden') ?>">
                                    <?php foreach ($menu['children'] as $child): ?>
                                        <?php 
                                            $isSubActive = strtolower($currentSegment) === strtolower($child['url']);
                                            $subActiveClass = $isSubActive 
                                                ? 'bg-sky-50 text-sky-800 dark:bg-sky-950/50 dark:text-cyan-300 font-bold border-l-2 border-sky-600' 
                                                : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/60 hover:text-slate-900 dark:hover:text-white font-medium';
                                        ?>
                                        <a href="<?= base_url($child['url']) ?>" class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-xs transition-all no-underline <?= $subActiveClass ?>">
                                            <i class="<?= esc($child['icon']) ?> text-sm <?= $isSubActive ? 'text-sky-600 dark:text-cyan-400' : 'text-slate-400' ?> shrink-0"></i>
                                            <span class="truncate"><?= esc($child['menuname']) ?></span>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                                <div class="flyout-submenu absolute left-[56px] -top-1 ps-4 w-60 z-50">
                                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-2xl rounded-xl p-2.5">
                                        <div class="px-2.5 py-1.5 text-xs font-bold text-sky-800 dark:text-cyan-300 border-b border-slate-100 dark:border-slate-700 mb-1.5 flex items-center gap-2">
                                            <i class="<?= esc($menu['icon']) ?> text-sm"></i>
                                            <span class="truncate"><?= esc($menu['menuname']) ?></span>
                                        </div>
                                        <div class="space-y-1">
                                            <?php foreach ($menu['children'] as $child): ?>
                                                <?php 
                                                    $isSubActive = strtolower($currentSegment) === strtolower($child['url']);
                                                    $subActiveClass = $isSubActive 
                                                        ? 'bg-sky-50 text-sky-800 dark:bg-sky-950/50 dark:text-cyan-300 font-bold border-l-2 border-sky-600' 
                                                        : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/60 hover:text-slate-900 dark:hover:text-white font-medium';
                                                ?>
                                                <a href="<?= base_url($child['url']) ?>" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs transition-all no-underline <?= $subActiveClass ?>">
                                                    <i class="<?= esc($child['icon']) ?> text-xs <?= $isSubActive ? 'text-sky-600 dark:text-cyan-400' : 'text-slate-400' ?>"></i>
                                                    <span class="truncate"><?= esc($child['menuname']) ?></span>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php 
                                $isActive = strtolower($currentSegment) === strtolower($menu['url']);
                                $activeClass = $isActive 
                                    ? 'bg-sky-50 text-sky-800 dark:bg-sky-950/50 dark:text-cyan-300 font-bold border-l-4 border-[#0284c7]' 
                                    : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/60 hover:text-slate-900 dark:hover:text-white font-medium';
                            ?>
                            <div class="group-tooltip relative">
                                <a href="<?= base_url($menu['url']) ?>" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-[10px] text-xs transition-all no-underline <?= $activeClass ?>" title="<?= esc($menu['menuname']) ?>">
                                    <i class="<?= esc($menu['icon']) ?> text-base <?= $isActive ? 'text-[#0284c7] dark:text-cyan-400' : 'text-slate-400' ?> shrink-0"></i>
                                    <span class="truncate sidebar-text"><?= esc($menu['menuname']) ?></span>
                                </a>
                                <div class="flyout-tooltip absolute left-[68px] top-1/2 -translate-y-1/2 px-2.5 py-1 bg-slate-900 text-white text-[11px] font-semibold rounded-md shadow-xl whitespace-nowrap z-50 pointer-events-none">
                                    <?= esc($menu['menuname']) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </nav>
        </div>
    </div>
    <div class="p-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center gap-2">
        <button type="button" onclick="openLogoutModal()" class="sidebar-item-btn btn btn-soft-danger w-full py-2.5 px-3 flex items-center justify-center gap-2 font-bold text-xs" title="Logout">
            <i class="bi bi-box-arrow-right text-base shrink-0"></i>
            <span class="sidebar-text">Logout</span>
        </button>
    </div>
</aside>
