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
