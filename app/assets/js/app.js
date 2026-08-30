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
 * Mobile nav.
 *
 * Below 700px the link row becomes a dropdown panel behind the hamburger.
 * aria-expanded on the button is the single source of truth: the CSS swaps the
 * burger/close icons off it, and this reads it back rather than keeping its own
 * flag. Closes on link click, Escape, a click outside, and on resize back to
 * the desktop layout - otherwise the panel's .is-open would linger as a stray
 * flex column once the media query stops applying.
 */
const NAV_BREAKPOINT = 700;

function navIsOpen(button) {
    return button.getAttribute('aria-expanded') === 'true';
}

function setNav(button, panel, open) {
    button.setAttribute('aria-expanded', open ? 'true' : 'false');
    panel.classList.toggle('is-open', open);
}

const navToggle = document.querySelector('[data-nav-toggle]');
const navPanel = navToggle ? document.getElementById(navToggle.getAttribute('aria-controls')) : null;

if (navToggle !== null && navPanel !== null) {
    navToggle.addEventListener('click', (event) => {
        event.stopPropagation();
        setNav(navToggle, navPanel, !navIsOpen(navToggle));
    });

    navPanel.addEventListener('click', (event) => {
        if (event.target.closest('a')) {
            setNav(navToggle, navPanel, false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && navIsOpen(navToggle)) {
            setNav(navToggle, navPanel, false);
            navToggle.focus();
        }
    });

    document.addEventListener('click', (event) => {
        if (navIsOpen(navToggle) && !navPanel.contains(event.target)) {
            setNav(navToggle, navPanel, false);
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > NAV_BREAKPOINT && navIsOpen(navToggle)) {
            setNav(navToggle, navPanel, false);
        }
    });
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
