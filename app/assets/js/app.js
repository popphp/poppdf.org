import '../css/app.css';

/**
 * Color mode.
 *
 * The initial state is applied by an inline script in head.phtml before first
 * paint, so there is no flash. This only owns the toggle and persistence.
 * The key is shared with the docs site so the choice survives crossing between
 * the two - do not rename it.
 */
const THEME_KEY = 'poppdf-theme';

function toggleTheme() {
    const root = document.documentElement;
    const isDark = root.getAttribute('data-theme') === 'dark';

    if (isDark) {
        root.removeAttribute('data-theme');
    } else {
        root.setAttribute('data-theme', 'dark');
    }

    try {
        localStorage.setItem(THEME_KEY, isDark ? 'light' : 'dark');
    } catch (e) {
        // Private mode, or storage blocked. The toggle still works for this page.
    }
}

/**
 * Copy buttons.
 *
 * A copy root is either a code window (copy the <pre>) or a terminal chip
 * (copy the command span, skipping the `$` prompt). Each button keeps its own
 * restore timer, so two clicked in quick succession both revert.
 */
const COPY_LABEL = 'copy';
const COPIED_LABEL = 'copied';
const RESTORE_MS = 1400;

function textToCopy(root) {
    const pre = root.querySelector('pre');

    if (pre) {
        return pre.innerText.trim();
    }

    const command = root.querySelector('[data-copy-text]');

    return command ? command.textContent.trim() : '';
}

function writeToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).catch(() => legacyCopy(text));
        return;
    }

    legacyCopy(text);
}

function legacyCopy(text) {
    const field = document.createElement('textarea');

    field.value = text;
    field.setAttribute('readonly', '');
    field.style.position = 'fixed';
    field.style.opacity = '0';
    document.body.appendChild(field);
    field.select();

    try {
        document.execCommand('copy');
    } catch (e) {
        // Nothing else to try.
    }

    document.body.removeChild(field);
}

function copyFrom(button) {
    const root = button.closest('[data-copy-root]');
    const label = button.querySelector('[data-copy-label]');

    if (root === null) {
        return;
    }

    writeToClipboard(textToCopy(root));

    if (label === null) {
        return;
    }

    label.textContent = COPIED_LABEL;
    clearTimeout(button.copyTimer);
    button.copyTimer = setTimeout(() => {
        label.textContent = COPY_LABEL;
    }, RESTORE_MS);
}

document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
    button.addEventListener('click', toggleTheme);
});

document.querySelectorAll('[data-copy-root] .copy-btn').forEach((button) => {
    button.addEventListener('click', () => copyFrom(button));
});
