import './bootstrap';

const pad2 = (value) => String(value).padStart(2, '0');

const updateUtcClock = () => {
    const now = new Date();
    const hh = pad2(now.getUTCHours());
    const mm = pad2(now.getUTCMinutes());
    const ss = pad2(now.getUTCSeconds());
    const time = `${hh}:${mm}:${ss}`;

    document.querySelectorAll('[data-utc-time]').forEach((el) => {
        el.textContent = time;
    });
};

if (document.querySelector('[data-utc-clock]')) {
    updateUtcClock();
    setInterval(updateUtcClock, 1000);
}

document.querySelectorAll('[data-sidebar-group]').forEach((group) => {
    const toggle = group.querySelector('[data-sidebar-toggle]');
    if (!toggle) {
        return;
    }

    toggle.setAttribute('aria-expanded', group.classList.contains('open') ? 'true' : 'false');

    toggle.addEventListener('click', () => {
        const isOpen = group.classList.toggle('open');
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
});
