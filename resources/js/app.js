require('./bootstrap');

require('bootstrap');

require('./sb-admin');

const getPreferredTheme = () => {
    const storedTheme = localStorage.getItem('theme');
    if (storedTheme === 'light' || storedTheme === 'dark') {
        return storedTheme;
    }
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
};

const setTheme = (theme) => {
    document.documentElement.setAttribute('data-bs-theme', theme);
    localStorage.setItem('theme', theme);
    window.dispatchEvent(new CustomEvent('theme:changed', { detail: { theme } }));
};

const syncThemeToggleLabel = (theme) => {
    const el = document.querySelector('[data-theme-toggle]');
    if (!el) {
        return;
    }
    el.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
    el.setAttribute('data-current-theme', theme);
    el.setAttribute('title', theme === 'dark' ? 'Switch ke Light' : 'Switch ke Dark');
    // Ikon + label (label disembunyikan di mobile via CSS)
    const icon = theme === 'dark' ? 'fa-sun' : 'fa-moon';
    const label = theme === 'dark' ? 'Light' : 'Dark';
    el.innerHTML = `<i class="fa-solid ${icon}"></i><span class="d-none d-sm-inline ms-1">${label}</span>`;
};

document.addEventListener('DOMContentLoaded', () => {
    const initialTheme = getPreferredTheme();
    document.documentElement.setAttribute('data-bs-theme', initialTheme);
    syncThemeToggleLabel(initialTheme);

    const toggle = document.querySelector('[data-theme-toggle]');
    if (toggle) {
        toggle.addEventListener('click', () => {
            const next = (document.documentElement.getAttribute('data-bs-theme') || 'light') === 'dark' ? 'light' : 'dark';
            setTheme(next);
            syncThemeToggleLabel(next);
        });
    }

    window.addEventListener('theme:changed', (e) => {
        syncThemeToggleLabel(e.detail?.theme);
    });
});
