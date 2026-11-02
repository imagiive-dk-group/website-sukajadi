// Gerak saat digulir: pemunculan foto/teks, jeda berurutan dalam daftar,
// penghitung angka, dan pita foto yang berhenti saat tidak terlihat.
// Semua elemen tetap terlihat bila skrip ini gagal (lihat .js di app.css).

const STEP = 70; // jeda antarkartu (ms)
const MAX_DELAY = 420; // batas total jeda supaya daftar panjang tidak lambat

const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');

function prepareStaggers(root = document) {
    root.querySelectorAll('[data-stagger]').forEach((list) => {
        const kind = list.dataset.stagger === 'photo' ? 'photo' : 'rise';
        [...list.children].forEach((child, i) => {
            if (!child.dataset.reveal) {
                child.dataset.reveal = kind;
            }
            child.style.setProperty('--d', `${Math.min(i * STEP, MAX_DELAY)}ms`);
        });
    });

    // Tirai foto mengikuti radius sudut elemennya sendiri.
    root.querySelectorAll('[data-reveal="photo"]').forEach((el) => {
        const r = getComputedStyle(el).borderTopLeftRadius;
        if (r && r !== '0px') {
            el.style.setProperty('--r', r);
        }
    });
}

function reveal(el) {
    if (el.classList.contains('is-in')) {
        return;
    }
    el.classList.add('is-in');

    // Lepas clip-path setelah tirai terbuka agar hover & bayangan normal kembali.
    const done = () => el.classList.add('is-done');
    if (reduced.matches || el.dataset.reveal !== 'photo') {
        done();
    } else {
        const delay = parseFloat(el.style.getPropertyValue('--d')) || 0;
        setTimeout(done, delay + 1300);
    }
}

function countUp(el) {
    const target = parseInt(el.dataset.count, 10);
    if (!Number.isFinite(target) || target <= 0 || reduced.matches) {
        return;
    }
    const duration = 900;
    const start = performance.now();
    const fmt = new Intl.NumberFormat(document.documentElement.lang || 'id');
    const tick = (now) => {
        const t = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - t, 4);
        el.textContent = fmt.format(Math.round(target * eased));
        if (t < 1) {
            requestAnimationFrame(tick);
        }
    };
    el.textContent = '0';
    requestAnimationFrame(tick);
}

export function initMotion() {
    prepareStaggers();

    const items = document.querySelectorAll('[data-reveal]');
    const counters = document.querySelectorAll('[data-count]');

    if (!('IntersectionObserver' in window)) {
        items.forEach(reveal);
        return;
    }

    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }
            const el = entry.target;
            if (el.dataset.count !== undefined) {
                countUp(el);
            } else {
                reveal(el);
            }
            io.unobserve(el);
        });
    // threshold 0: foto bertirai (clip-path) selalu melaporkan rasio 0 sebelum tersingkap.
    }, { rootMargin: '0px 0px -10% 0px', threshold: 0 });

    items.forEach((el) => io.observe(el));
    counters.forEach((el) => io.observe(el));

    // Jaring pengaman: apa pun yang belum muncul setelah 4 detik di atas layar ditampilkan.
    setTimeout(() => {
        items.forEach((el) => {
            if (el.getBoundingClientRect().top < window.innerHeight) {
                reveal(el);
            }
        });
    }, 4000);

    // Pita foto: berhenti saat di luar layar atau tab disembunyikan.
    const marquees = document.querySelectorAll('.marquee');
    if (marquees.length) {
        const mo = new IntersectionObserver((entries) => {
            entries.forEach((e) => e.target.classList.toggle('is-offscreen', !e.isIntersecting));
        });
        marquees.forEach((m) => mo.observe(m));
    }

    // Video latar beranda: hemat baterai saat tidak terlihat.
    const video = document.querySelector('.hero-media video');
    if (video) {
        new IntersectionObserver(([e]) => (e.isIntersecting ? video.play().catch(() => {}) : video.pause()))
            .observe(video);
    }
}
