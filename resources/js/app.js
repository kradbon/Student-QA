import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const themeToggle = document.querySelector('[data-theme-toggle]');
    const themeKey = 'student-qa-theme';
    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)');

    const savedTheme = () => {
        try {
            const theme = localStorage.getItem(themeKey);
            return theme === 'dark' || theme === 'light' ? theme : null;
        } catch {
            return null;
        }
    };

    const applyTheme = (theme, persist = false) => {
        document.documentElement.dataset.theme = theme;
        themeToggle?.setAttribute('aria-pressed', String(theme === 'dark'));

        if (persist) {
            try {
                localStorage.setItem(themeKey, theme);
            } catch {
                // The theme still works for this page when storage is unavailable.
            }
        }
    };

    applyTheme(savedTheme() || (systemTheme.matches ? 'dark' : 'light'));
    themeToggle?.addEventListener('click', () => {
        applyTheme(document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark', true);
    });
    systemTheme.addEventListener?.('change', (event) => {
        if (!savedTheme()) applyTheme(event.matches ? 'dark' : 'light');
    });

    const title = document.querySelector('[data-similar-title]');
    const body = document.querySelector('[data-similar-body]');
    const results = document.querySelector('[data-similar-results]');

    if (!title || !results) return;

    let timer;
    const search = async () => {
        const query = `${title.value} ${body?.value ?? ''}`.trim();
        if (query.length < 8) {
            results.replaceChildren();
            return;
        }

        try {
            results.setAttribute('aria-busy', 'true');
            results.replaceChildren();
            const loading = document.createElement('p');
            loading.className = 'text-sm text-slate-500';
            loading.textContent = results.dataset.loading;
            results.append(loading);
            const endpoint = new URL(results.dataset.endpoint, window.location.origin);
            endpoint.searchParams.set('q', query);
            const response = await fetch(endpoint, {
                headers: { Accept: 'application/json' },
            });
            const data = await response.json();
            results.replaceChildren();
            if (!data.data.length) return;

            const heading = document.createElement('p');
            heading.className = 'font-semibold';
            heading.textContent = results.dataset.label;
            results.append(heading);
            data.data.forEach((question) => {
                const link = document.createElement('a');
                link.className = 'block rounded-xl border border-slate-200 p-3 hover:border-orange-400';
                link.href = question.url;
                link.textContent = question.title;
                results.append(link);
            });
        } catch {
            results.replaceChildren();
        } finally {
            results.removeAttribute('aria-busy');
        }
    };

    [title, body].filter(Boolean).forEach((field) => field.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(search, 350);
    }));
});
