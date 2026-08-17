import './bootstrap';
import * as bootstrap from 'bootstrap';
import AOS from 'aos';

/* ---------------- Loading Screen ---------------- */
window.addEventListener('load', () => {
    const loader = document.getElementById('loader');
    if (loader) {
        setTimeout(() => loader.classList.add('hidden'), 400);
    }
    initCounters();
});

/* ---------------- AOS ---------------- */
AOS.init({
    duration: 800,
    once: true,
    offset: 80,
    easing: 'ease-out-cubic',
});

/* ---------------- Navbar Scroll ---------------- */
const navbar = document.querySelector('.navbar-mk');
const onScroll = () => {
    if (navbar) navbar.classList.toggle('scrolled', window.scrollY > 30);
    const btn = document.getElementById('backToTop');
    if (btn) btn.classList.toggle('show', window.scrollY > 500);
};
window.addEventListener('scroll', onScroll, { passive: true });
onScroll();

/* ---------------- Back to Top ---------------- */
document.getElementById('backToTop')?.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

/* ---------------- Smooth scroll untuk anchor ---------------- */
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener('click', function (e) {
        const id = this.getAttribute('href');
        if (id.length > 1) {
            const el = document.querySelector(id);
            if (el) {
                e.preventDefault();
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    });
});

/* ---------------- Typing Effect ---------------- */
function initTyping() {
    const el = document.getElementById('typingText');
    if (!el) return;
    const words = JSON.parse(el.dataset.words || '[]');
    let wordIdx = 0, charIdx = 0, deleting = false;
    const type = () => {
        const word = words[wordIdx];
        if (!word) return;
        el.textContent = word.slice(0, charIdx);
        let delay = deleting ? 45 : 85;
        if (!deleting && charIdx === word.length) { delay = 1800; deleting = true; }
        else if (deleting && charIdx === 0) { deleting = false; wordIdx = (wordIdx + 1) % words.length; delay = 350; }
        charIdx += deleting ? -1 : 1;
        setTimeout(type, delay);
    };
    type();
}
document.addEventListener('DOMContentLoaded', initTyping);

/* ---------------- Counter Animation ---------------- */
function initCounters() {
    document.querySelectorAll('[data-counter]').forEach((el) => {
        const target = parseInt(el.dataset.counter, 10) || 0;
        const suffix = el.dataset.suffix || '';
        const duration = 2000;
        const start = performance.now();
        const step = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.floor(eased * target).toLocaleString('id-ID') + suffix;
            if (progress < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    });
}

/* ---------------- Dark Mode ---------------- */
const themeToggle = document.getElementById('themeToggle');
function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('mk-theme', theme);
    const icon = themeToggle?.querySelector('i');
    if (icon) icon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
}
themeToggle?.addEventListener('click', () => {
    const current = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    applyTheme(current);
});
(function initTheme() {
    const saved = localStorage.getItem('mk-theme');
    if (saved) applyTheme(saved);
})();

/* ---------------- Countdown Event ---------------- */
function initCountdown() {
    document.querySelectorAll('[data-countdown]').forEach((box) => {
        const target = new Date(box.dataset.countdown).getTime();
        if (isNaN(target)) return;
        const update = () => {
            const diff = target - Date.now();
            if (diff <= 0) return;
            const d = Math.floor(diff / 86400000);
            const h = Math.floor((diff % 86400000) / 3600000);
            const m = Math.floor((diff % 3600000) / 60000);
            const s = Math.floor((diff % 60000) / 1000);
            const set = (cls, v) => {
                const el = box.querySelector(cls);
                if (el) el.textContent = String(v).padStart(2, '0');
            };
            set('.cd-d', d); set('.cd-h', h); set('.cd-m', m); set('.cd-s', s);
        };
        update();
        setInterval(update, 1000);
    });
}
document.addEventListener('DOMContentLoaded', initCountdown);

/* ---------------- Toast Helper ---------------- */
window.mkToast = function (message, type = 'success') {
    const container = document.getElementById('toastContainer');
    if (!container) return;
    const icons = { success: 'bi-check-circle-fill text-success', error: 'bi-x-circle-fill text-danger', info: 'bi-info-circle-fill text-primary' };
    const div = document.createElement('div');
    div.className = 'toast align-items-center show mb-2';
    div.style.background = 'var(--mk-card-bg)';
    div.style.color = 'var(--mk-text)';
    div.innerHTML = `
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2">
                <i class="bi ${icons[type] || icons.info}"></i>
                <span>${message}</span>
            </div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>`;
    container.appendChild(div);
    setTimeout(() => { div.remove(); }, 4000);
};

/* ---------------- CSRF helper untuk AJAX ---------------- */
window.mkFetch = async function (url, options = {}) {
    const opts = { ...options, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content, 'Accept': 'application/json', ...(options.headers || {}) } };
    if (opts.body instanceof FormData) {
        // FormData: biarkan browser menentukan Content-Type (multipart/form-data)
        delete opts.headers['Content-Type'];
    } else if (opts.body && typeof opts.body === 'object') {
        opts.body = JSON.stringify(opts.body);
        opts.headers['Content-Type'] = 'application/json';
    } else if (typeof opts.body === 'string') {
        // String body dari pemanggil berupa JSON (mis. JSON.stringify({...}))
        opts.headers['Content-Type'] = 'application/json';
    }
    const res = await fetch(url, opts);
    const data = await res.json().catch(() => ({}));
    if (!res.ok) throw new Error(data.message || 'Terjadi kesalahan');
    return data;
};

/* ---------------- PWA: Service Worker ---------------- */
if ('serviceWorker' in navigator && (location.protocol === 'https:' || ['localhost', '127.0.0.1'].includes(location.hostname))) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}

/* ---------------- Toggle Password (tombol mata) ---------------- */
document.addEventListener('click', (e) => {
    const btn = e.target.closest('.toggle-password');
    if (!btn) return;
    const input = btn.closest('.input-group')?.querySelector('input[type="password"], input[type="text"]');
    if (!input) return;
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    const icon = btn.querySelector('i');
    if (icon) icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    btn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
});

window.bootstrap = bootstrap;
