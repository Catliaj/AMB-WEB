(function(){
  const THEME_KEY = 'adm_theme_pref';

  function applyTheme(theme) {
    const isLight = theme === 'light';
    document.documentElement.setAttribute('data-theme', isLight ? 'light' : 'dark');
  }

  function getSavedTheme() {
    try {
      return localStorage.getItem(THEME_KEY);
    } catch {
      return null;
    }
  }

  function saveTheme(theme) {
    try {
      localStorage.setItem(THEME_KEY, theme);
    } catch {}
  }

  function updateBtn(theme) {
    const btn = document.getElementById('themeToggleBtn');
    if (!btn) return;

    const isLight = theme === 'light';
    btn.textContent = isLight ? '🌙' : '☀️';
    btn.title = isLight ? 'Switch to dark mode' : 'Switch to light mode';
    btn.classList.toggle('active', isLight);
  }

  function injectToggle() {
    if (document.getElementById('themeToggleBtn')) return;

    const btn = document.createElement('button');
    btn.id = 'themeToggleBtn';
    btn.setAttribute('aria-label', 'Toggle theme');
    btn.style.cssText = `
      position: fixed;
      right: 18px;
      bottom: 18px;
      z-index: 9999;
      border-radius: 999px;
      border: none;
      padding: 10px 12px;
      font-size: 16px;
      cursor: pointer;
      box-shadow: 0 6px 18px rgba(0,0,0,0.12);
      background: var(--card);
      color: var(--text);
      transition: transform 120ms ease, background 200ms ease, color 200ms ease;
    `;
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      window.admTheme.toggle();
    });

    document.body.appendChild(btn);
  }

  window.admTheme = {
    toggle() {
      const current = getSavedTheme() || (document.documentElement.getAttribute('data-theme') === 'light' ? 'light' : 'dark');
      const next = current === 'light' ? 'dark' : 'light';
      applyTheme(next);
      saveTheme(next);
      updateBtn(next);
    },
    set(theme) {
      applyTheme(theme);
      saveTheme(theme);
      updateBtn(theme);
    }
  };

  document.addEventListener('DOMContentLoaded', () => {
    const saved = getSavedTheme();
    const prefersLight = window.matchMedia?.('(prefers-color-scheme: light)').matches;
    const initial = saved || (prefersLight ? 'light' : 'dark');

    applyTheme(initial);
    injectToggle();
    updateBtn(initial);
  });
})();
