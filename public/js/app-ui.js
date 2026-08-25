document.addEventListener('DOMContentLoaded', function() {
    // Dark / Light Mode Switcher
    const themeBtn = document.getElementById('theme-toggle') || document.getElementById('theme-toggle-btn');
    const sunIcon = document.getElementById('theme-icon-sun');
    const moonIcon = document.getElementById('theme-icon-moon');
    function applyThemeUI(isDark) {
        if (isDark) {
            document.documentElement.classList.add('dark');
            document.documentElement.classList.remove('light');
            if (sunIcon) sunIcon.classList.remove('hidden');
            if (moonIcon) moonIcon.classList.add('hidden');
        } else {
            document.documentElement.classList.remove('dark');
            document.documentElement.classList.add('light');
            if (sunIcon) sunIcon.classList.add('hidden');
            if (moonIcon) moonIcon.classList.remove('hidden');
        }
    }

    const currentTheme = localStorage.getItem('theme') || 'dark';
    applyThemeUI(currentTheme === 'dark');
    window.toggleTheme = function() {
        const isCurrentlyDark = document.documentElement.classList.contains('dark');
        const newTheme = isCurrentlyDark ? 'light' : 'dark';
        localStorage.setItem('theme', newTheme);
        applyThemeUI(newTheme === 'dark');
    };

    if (themeBtn) {
        themeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.toggleTheme();
        });
    }

    const sidebarToggleBtn = document.getElementById('sidebar-toggle-btn');
    const mobileToggleBtn = document.getElementById('mobile-toggle-btn');
    const desktopReopenBtn = document.getElementById('desktop-sidebar-reopen-btn');
    const sidebarCloseBtn = document.getElementById('sidebar-close-btn');
    const sidebarChevronIcon = document.getElementById('sidebar-chevron-icon');
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    const mainWrapper = document.getElementById('main-wrapper');
    function isMobile() {
        return window.innerWidth < 768;
    }

    function applyDesktopSidebarState(isCollapsed) {
        if (!sidebar || !mainWrapper) return;
        if (isCollapsed) {
            sidebar.classList.add('sidebar-collapsed');
            mainWrapper.classList.remove('md:pl-60');
            mainWrapper.classList.add('md:pl-[72px]');
            if (sidebarChevronIcon) {
                sidebarChevronIcon.classList.remove('bi-chevron-left');
                sidebarChevronIcon.classList.add('bi-chevron-right');
            }
        } else {
            sidebar.classList.remove('sidebar-collapsed');
            mainWrapper.classList.remove('md:pl-[72px]');
            mainWrapper.classList.add('md:pl-60');
            if (sidebarChevronIcon) {
                sidebarChevronIcon.classList.remove('bi-chevron-right');
                sidebarChevronIcon.classList.add('bi-chevron-left');
            }
        }
    }

    if (!isMobile()) {
        const isCollapsed = localStorage.getItem('sidebar_collapsed') === '1';
        applyDesktopSidebarState(isCollapsed);
    }

    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (!isMobile()) {
                const isCurrentlyCollapsed = sidebar.classList.contains('sidebar-collapsed');
                const newCollapsed = !isCurrentlyCollapsed;
                localStorage.setItem('sidebar_collapsed', newCollapsed ? '1' : '0');
                applyDesktopSidebarState(newCollapsed);
            }
        });
    }

    if (mobileToggleBtn && sidebar) {
        mobileToggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.remove('-translate-x-full');
            if (backdrop) {
                backdrop.classList.remove('opacity-0', 'pointer-events-none');
            }
        });
    }

    if (sidebarCloseBtn && sidebar) {
        sidebarCloseBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.add('-translate-x-full');
            if (backdrop) {
                backdrop.classList.add('opacity-0', 'pointer-events-none');
            }
        });
    }

    if (backdrop && sidebar) {
        backdrop.addEventListener('click', function() {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('opacity-0', 'pointer-events-none');
        });
    }

    window.addEventListener('resize', function() {
        if (!isMobile()) {
            if (backdrop) {
                backdrop.classList.add('opacity-0', 'pointer-events-none');
            }
            const isCollapsed = localStorage.getItem('sidebar_collapsed') === '1';
            applyDesktopSidebarState(isCollapsed);
        } else {
            if (mainWrapper) {
                mainWrapper.classList.remove('md:pl-60', 'md:pl-[72px]');
            }
        }
    });

    window.toggleSubmenu = function(id, btn) {
        if (!isMobile() && sidebar && sidebar.classList.contains('sidebar-collapsed')) {
            return;
        }
        const target = document.getElementById(id);
        if (!target) return;
        const chevron = btn.querySelector('.submenu-chevron');
        const isHidden = target.classList.contains('hidden');
        if (isHidden) {
            target.classList.remove('hidden');
            if (chevron) chevron.classList.add('rotate-180');
        } else {
            target.classList.add('hidden');
            if (chevron) chevron.classList.remove('rotate-180');
        }
    };

    const profileBtn = document.getElementById('profile-menu-btn');
    const profileDropdown = document.getElementById('profile-dropdown');
    if (profileBtn && profileDropdown) {
        profileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', function(e) {
            if (!profileDropdown.contains(e.target) && !profileBtn.contains(e.target)) {
                profileDropdown.classList.add('hidden');
            }
        });
    }

    window.openEditProfileModal = function() {
        const modal = document.getElementById('modal-edit-profile');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        if (profileDropdown) profileDropdown.classList.add('hidden');
    };

    window.closeEditProfileModal = function() {
        const modal = document.getElementById('modal-edit-profile');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    };

    window.openLogoutModal = function() {
        const modal = document.getElementById('modal-logout');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        if (profileDropdown) profileDropdown.classList.add('hidden');
    };

    window.closeLogoutModal = function() {
        const modal = document.getElementById('modal-logout');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    };
});
