document.addEventListener('DOMContentLoaded', function() {
    // ── Dark / Light Mode Switcher ───────────────────────────────────────────
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

    // Inisialisasi tema saat load
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

    // ── Mobile Sidebar Toggle ────────────────────────────────────────────────
    const toggleBtn = document.getElementById('mobile-toggle-btn') || document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar') || document.getElementById('main-sidebar');
    const backdrop = document.getElementById('sidebar-backdrop') || document.getElementById('sidebar-overlay');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('-translate-x-full');
            if (backdrop) {
                backdrop.classList.toggle('opacity-0');
                backdrop.classList.toggle('pointer-events-none');
            }
        });
    }

    if (backdrop && sidebar) {
        backdrop.addEventListener('click', function() {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('opacity-0');
            backdrop.classList.add('pointer-events-none');
        });
    }

    // ── Profile Dropdown Toggle ──────────────────────────────────────────────
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

    // ── Modal Edit Profile Helper ────────────────────────────────────────────
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

    // ── Modal Logout Helper ──────────────────────────────────────────────────
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
