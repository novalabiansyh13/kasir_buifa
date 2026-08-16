/**
 * Template UI Controller (HyperUI Architecture Pattern)
 * Mengelola Theme Switcher, Responsive Sidebar, Profile Dropdowns, dan Modals
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ── 1. THEME CONTROLLER (Dark / Light Mode) ──────────────────────────────
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

    // Inisialisasi tema awal
    const savedTheme = localStorage.getItem('theme') || 'dark';
    applyTheme(savedTheme === 'dark');

    window.toggleTheme = function() {
        const isDark = document.documentElement.classList.contains('dark');
        const newTheme = isDark ? 'light' : 'dark';
        localStorage.setItem('theme', newTheme);
        applyTheme(newTheme === 'dark');
    };

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.toggleTheme();
        });
    }

    // ── 2. SIDEBAR CONTROLLER ────────────────────────────────────────────────
    const mobileToggleBtn = document.getElementById('mobile-toggle-btn');
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');

    if (mobileToggleBtn && sidebar) {
        mobileToggleBtn.addEventListener('click', function(e) {
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

    // ── 3. PROFILE DROPDOWN CONTROLLER ───────────────────────────────────────
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

    // ── 4. MODAL CONTROLLER ──────────────────────────────────────────────────
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
