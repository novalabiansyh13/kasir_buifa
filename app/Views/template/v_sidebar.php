<div id="sidebar-backdrop" class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs opacity-0 pointer-events-none transition-opacity duration-300 md:hidden" aria-hidden="true"></div>
<aside id="sidebar" class="group/sidebar fixed inset-y-0 left-0 z-50 flex flex-col w-60 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 -translate-x-full md:translate-x-0 transition-transform md:transition-all duration-300 ease-in-out shadow-2xl md:shadow-lg rounded-r-[18px] border-r border-slate-200/60 dark:border-slate-700" aria-label="Enterprise Navigation">
    <div class="p-3.5 border-b border-slate-100 dark:border-slate-700/60 flex items-center justify-between relative group">
        <a href="<?= base_url('kasir') ?>" class="flex items-center gap-2.5 overflow-hidden no-underline">
            <div class="w-9 h-9 rounded-xl bg-[#0284c7] text-white flex items-center justify-center font-bold shadow-xs shrink-0 text-base">
                <i class="bi bi-shop-window"></i>
            </div>
            <div class="leading-tight overflow-hidden">
                <div class="font-bold text-slate-900 dark:text-white text-xs truncate flex items-center gap-1.5">
                    <span>Smart Cashier</span>
                    <span class="px-1.5 py-0.2 bg-sky-500/15 text-sky-600 dark:text-cyan-400 text-[9px] font-bold rounded-full"> Live POS</span>
                </div>
                <span class="text-[11px] text-slate-400 dark:text-slate-500 truncate block font-normal">Modern POS Suite</span>
            </div>
        </a>
    </div>
    <div class="flex-1 overflow-y-auto px-3 py-3 space-y-3 font-normal custom-scrollbar">
        <?php 
            $userMenus = generateSidebarMenus(); 
            $uri = service('uri');
            $currentSegment = $uri->getSegment(1) ?? 'kasir';
        ?>
        <div>
            <div class="px-3 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">
                MAIN / OVERVIEW
            </div>
            <nav class="space-y-1">
                <?php if (!empty($userMenus)): ?>
                    <?php foreach ($userMenus as $menu): ?>
                        <?php 
                            $isActive = strtolower($currentSegment) === strtolower($menu['url']);
                            $activeClass = $isActive 
                                ? 'bg-sky-50 text-sky-800 dark:bg-sky-950/50 dark:text-cyan-300 font-bold border-l-4 border-[#0284c7]' 
                                : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700/60 hover:text-slate-900 dark:hover:text-white font-medium';
                        ?>
                        <a href="<?= base_url($menu['url']) ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-[10px] text-xs transition-all no-underline <?= $activeClass ?>">
                            <i class="<?= esc($menu['icon']) ?> text-base <?= $isActive ? 'text-[#0284c7] dark:text-cyan-400' : 'text-slate-400' ?>"></i>
                            <span class="truncate"><?= esc($menu['menuname']) ?></span>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </nav>
        </div>
    </div>
    <div class="p-3 border-t border-slate-100 dark:border-slate-700/60 flex items-center gap-2">
        <button type="button" onclick="openLogoutModal()" class="btn btn-soft-danger w-full py-2.5 px-3 flex items-center justify-center gap-2 font-bold text-xs">
            <i class="bi bi-box-arrow-right text-base"></i>
            <span>Logout</span>
        </button>
    </div>
</aside>
