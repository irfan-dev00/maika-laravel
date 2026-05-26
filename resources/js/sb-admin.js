const SIDEBAR_TOGGLE_KEY = 'sb|sidebar-toggle';
const MOBILE_BREAKPOINT = 992;

const isMobile = () => window.innerWidth < MOBILE_BREAKPOINT;

const setStoredSidebarToggle = (toggled) => {
    // Jangan simpan state di mobile (selalu mulai tertutup di mobile)
    if (isMobile()) return;
    localStorage.setItem(SIDEBAR_TOGGLE_KEY, toggled ? 'true' : 'false');
};

const getStoredSidebarToggle = () => {
    if (isMobile()) return false; // mobile: selalu tertutup saat load
    return localStorage.getItem(SIDEBAR_TOGGLE_KEY) === 'true';
};

const setSidebarToggled = (toggled) => {
    document.body.classList.toggle('sb-sidenav-toggled', toggled);
    setStoredSidebarToggle(toggled);
};

const ensureBackdrop = () => {
    let backdrop = document.querySelector('.sb-sidenav-backdrop');
    if (!backdrop) {
        backdrop = document.createElement('div');
        backdrop.className = 'sb-sidenav-backdrop';
        backdrop.addEventListener('click', () => setSidebarToggled(false));
        document.body.appendChild(backdrop);
    }
    return backdrop;
};

const syncThemeNav = (theme) => {
    const topnav = document.querySelector('[data-sb-topnav]');
    const sidenav = document.querySelector('[data-sb-sidenav]');

    if (topnav) {
        topnav.classList.toggle('navbar-dark', theme === 'dark');
        topnav.classList.toggle('bg-dark', theme === 'dark');
        topnav.classList.toggle('navbar-light', theme !== 'dark');
        topnav.classList.toggle('bg-light', theme !== 'dark');
    }

    if (sidenav) {
        sidenav.classList.toggle('sb-sidenav-dark', theme === 'dark');
        sidenav.classList.toggle('sb-sidenav-light', theme !== 'dark');
    }
};

document.addEventListener('DOMContentLoaded', () => {
    ensureBackdrop();
    setSidebarToggled(getStoredSidebarToggle());

    const toggle = document.getElementById('sidebarToggle');
    if (toggle) {
        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            setSidebarToggled(!document.body.classList.contains('sb-sidenav-toggled'));
        });
    }

    // Auto-close sidebar saat klik nav-link di mobile
    document.querySelectorAll('#layoutSidenav_nav .nav-link').forEach((link) => {
        link.addEventListener('click', () => {
            if (isMobile()) setSidebarToggled(false);
        });
    });

    // Reset state saat resize melewati breakpoint
    let lastIsMobile = isMobile();
    window.addEventListener('resize', () => {
        const nowMobile = isMobile();
        if (nowMobile !== lastIsMobile) {
            lastIsMobile = nowMobile;
            // Saat berubah mode, tutup sidebar agar konsisten
            setSidebarToggled(false);
        }
    });

    const theme = document.documentElement.getAttribute('data-bs-theme') || 'light';
    syncThemeNav(theme);

    window.addEventListener('theme:changed', (e) => {
        syncThemeNav(e.detail?.theme || 'light');
    });
});

