/**
 * Yumnia — interaksi & animasi.
 * Tanpa library: IntersectionObserver, Web Animations API, View Transitions.
 */
(() => {
	'use strict';

	const D = document;
	const H = D.documentElement;
	const B = D.body;
	const Y = window.YUMNIA || { i18n: {} };
	const t = (k, fb) => (Y.i18n && Y.i18n[k]) || fb || k;
	const $ = (s, c = D) => c.querySelector(s);
	const $$ = (s, c = D) => Array.from(c.querySelectorAll(s));
	const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
	const fine = matchMedia('(hover: hover) and (pointer: fine)').matches;
	const rupiah = (n) => 'Rp' + Math.round(Number(n) || 0).toLocaleString('id-ID');
	const clamp = (v, a, b) => Math.min(b, Math.max(a, v));
	const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c]);
	const json = (el, attr) => { try { return JSON.parse(el.getAttribute(attr)); } catch (e) { return null; } };

	/* ------------------------------------------------------------ pecah kata */
	function split(el) {
		if (el.querySelector('.w')) return;
		let i = 0;
		const walk = (node) => {
			Array.from(node.childNodes).forEach((child) => {
				if (child.nodeType === 3) {
					const parts = child.textContent.split(/(\s+)/);
					const frag = D.createDocumentFragment();
					parts.forEach((p) => {
						if (!p) return;
						if (/^\s+$/.test(p)) { frag.appendChild(D.createTextNode(' ')); return; }
						const w = D.createElement('span');
						w.className = 'w';
						const inner = D.createElement('span');
						inner.className = 'w__i';
						inner.style.setProperty('--i', i++);
						inner.textContent = p;
						w.appendChild(inner);
						frag.appendChild(w);
					});
					child.replaceWith(frag);
				} else if (child.nodeType === 1 && !child.classList.contains('w')) {
					walk(child);
				}
			});
		};
		walk(el);
	}
	$$('[data-split]').forEach(split);

	/* ------------------------------------------------------------ muncul saat digulir */
	const countUp = (el) => {
		const m = el.textContent.trim().match(/^(\d+)(.*)$/);
		if (!m || reduced) return;
		const end = parseInt(m[1], 10);
		const suffix = m[2];
		const start = performance.now();
		const dur = 1600;
		const step = (now) => {
			const p = clamp((now - start) / dur, 0, 1);
			const e = 1 - Math.pow(1 - p, 4);
			el.textContent = Math.round(end * e) + suffix;
			if (p < 1) requestAnimationFrame(step);
		};
		requestAnimationFrame(step);
	};
	const revealTargets = () => $$('[data-reveal]:not(.is-in), [data-split]:not([data-split="now"]):not(.is-in), .card:not(.is-in), [data-count]:not(.is-counted)');
	// Elemen yang terpotong penuh (clip-path) tidak dilaporkan IntersectionObserver,
	// jadi untuk efek "clip" & "arch" yang diamati adalah elemen induknya.
	const proxied = new Map();
	let io = null;
	const reveal = (el) => {
		el.classList.add('is-in');
		if (el.hasAttribute('data-count') && !el.classList.contains('is-counted')) {
			el.classList.add('is-counted');
			countUp(el);
		}
	};
	if ('IntersectionObserver' in window) {
		io = new IntersectionObserver((entries) => {
			entries.forEach((en) => {
				if (!en.isIntersecting) return;
				const list = proxied.get(en.target);
				if (list) { list.forEach(reveal); proxied.delete(en.target); } else { reveal(en.target); }
				io.unobserve(en.target);
			});
		}, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 });
	}
	const observe = (root = D) => {
		const list = root === D ? revealTargets() : $$('[data-reveal], [data-split]:not([data-split="now"]), .card', root);
		list.forEach((el) => {
			if (!io) { reveal(el); return; }
			const mode = el.getAttribute('data-reveal');
			if ((mode === 'clip' || mode === 'arch') && el.parentElement) {
				const host = el.parentElement;
				if (!proxied.has(host)) { proxied.set(host, []); io.observe(host); }
				proxied.get(host).push(el);
			} else {
				io.observe(el);
			}
		});
	};
	observe();

	/* ------------------------------------------------------------ intro + hero */
	const hero = $('[data-hero]');
	const startPage = () => {
		H.classList.remove('has-intro');
		if (hero) hero.classList.add('is-in');
		$$('[data-split="now"]').forEach((el, n) => setTimeout(() => el.classList.add('is-in'), hero ? 120 : 60 + n * 60));
	};
	if (H.classList.contains('has-intro') && hero && !reduced) {
		try { sessionStorage.setItem('yumnia-intro', '1'); } catch (e) { /* abaikan */ }
		const c = D.createElement('div');
		c.className = 'curtain';
		c.setAttribute('aria-hidden', 'true');
		const mark = $('.brand__mark') || $('.monogram');
		c.innerHTML = (mark ? mark.outerHTML.replace('brand__mark', '') : '') + '<span class="curtain__word">YUMNIA</span>';
		const inner = D.createElement('div');
		inner.style.cssText = 'display:grid;justify-items:center';
		while (c.firstChild) inner.appendChild(c.firstChild);
		c.appendChild(inner);
		B.appendChild(c);
		H.classList.remove('has-intro');
		setTimeout(() => { c.classList.add('is-leaving'); setTimeout(startPage, 250); }, 1250);
		setTimeout(() => c.remove(), 2600);
	} else {
		requestAnimationFrame(startPage);
	}

	/* ------------------------------------------------------------ header & gulir */
	const header = $('[data-header]');
	let lastY = window.scrollY;
	let ticking = false;
	let heroH = hero ? hero.offsetHeight : 0;
	const band = $('[data-marquee] .band__track');
	let bandAnim = null;
	let velocity = 0;
	const heroMedia = hero ? $('[data-hero-media]', hero) : null;
	const heroContent = hero ? $('.hero__content', hero) : null;

	const onScroll = () => {
		const y = window.scrollY;
		const dy = y - lastY;
		if (header) {
			header.classList.toggle('is-solid', !hero || y > heroH - header.offsetHeight - 10);
			if (!H.classList.contains('is-locked')) {
				if (y > 420 && dy > 6) header.classList.add('is-hidden');
				else if (dy < -6 || y < 200) header.classList.remove('is-hidden');
			}
			H.style.setProperty('--hdr-offset', header.classList.contains('is-hidden') ? '0px' : header.offsetHeight + 'px');
		}
		if (heroMedia && !reduced && y < heroH) {
			heroMedia.style.transform = `translate3d(0, ${y * 0.28}px, 0)`;
			if (heroContent) heroContent.style.opacity = String(clamp(1 - y / (heroH * 0.75), 0, 1));
		}
		B.classList.toggle('is-scrolled', y > (hero ? heroH * 0.6 : 0));
		velocity = clamp(dy, -60, 60);
		lastY = y;
		ticking = false;
	};
	window.addEventListener('scroll', () => {
		if (!ticking) { ticking = true; requestAnimationFrame(onScroll); }
	}, { passive: true });
	window.addEventListener('resize', () => { heroH = hero ? hero.offsetHeight : 0; }, { passive: true });
	onScroll();

	// Pita berjalan: makin cepat (dan berbalik arah) mengikuti gulir.
	if (band && !reduced && band.getAnimations) {
		bandAnim = band.getAnimations()[0] || null;
		let rate = 1;
		const loop = () => {
			const target = (velocity < -1 ? -1 : 1) * (1 + Math.abs(velocity) * 0.09);
			rate += (target - rate) * 0.08;
			velocity *= 0.9;
			if (bandAnim) bandAnim.playbackRate = rate;
			requestAnimationFrame(loop);
		};
		if (bandAnim) requestAnimationFrame(loop);
	}

	/* ------------------------------------------------------------ tombol magnet & kursor */
	if (fine && !reduced) {
		$$('[data-magnetic]').forEach((el) => {
			const label = $('.btn__label', el);
			el.addEventListener('pointermove', (e) => {
				const r = el.getBoundingClientRect();
				const x = e.clientX - r.left - r.width / 2;
				const y = e.clientY - r.top - r.height / 2;
				el.style.transform = `translate(${x * 0.22}px, ${y * 0.32}px)`;
				if (label) label.style.transform = `translate(${x * 0.1}px, ${y * 0.12}px)`;
			});
			el.addEventListener('pointerleave', () => {
				el.style.transform = '';
				if (label) label.style.transform = '';
			});
		});

		const cursor = $('.cursor');
		const cLabel = cursor && $('.cursor__label', cursor);
		if (cursor) {
			let mx = -100, my = -100, cx = -100, cy = -100;
			D.addEventListener('pointermove', (e) => {
				mx = e.clientX; my = e.clientY;
				const target = e.target.closest && e.target.closest('[data-cursor]');
				if (target && !e.target.closest('.card__add, .swatch')) {
					cLabel.textContent = target.getAttribute('data-cursor');
					cursor.classList.add('is-active');
				} else {
					cursor.classList.remove('is-active');
				}
			}, { passive: true });
			const follow = () => {
				cx += (mx - cx) * 0.2; cy += (my - cy) * 0.2;
				cursor.style.transform = `translate3d(${cx}px, ${cy}px, 0)`;
				requestAnimationFrame(follow);
			};
			follow();
		}
	}

	/* ------------------------------------------------------------ notifikasi kecil */
	const toastBox = $('[data-toasts]');
	const toast = (msg, type = 'ok') => {
		if (!toastBox) return;
		const el = D.createElement('div');
		el.className = 'toast' + (type === 'error' ? ' toast--error' : '');
		el.innerHTML = (type === 'error' ? '' : '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>') + '<span></span>';
		el.querySelector('span').textContent = msg;
		toastBox.appendChild(el);
		setTimeout(() => { el.classList.add('is-out'); setTimeout(() => el.remove(), 450); }, 2600);
	};

	/* ------------------------------------------------------------ panel (menu, cari, keranjang, filter, sheet) */
	const scrim = $('[data-scrim]');
	let current = null;
	let lastTrigger = null;
	const focusables = (el) => $$('a[href], button:not([disabled]), input:not([type="hidden"]):not([disabled]), select, textarea, [tabindex]:not([tabindex="-1"])', el).filter((n) => n.offsetParent !== null || n === D.activeElement);

	const openPanel = (name, trigger) => {
		const panel = $(`[data-panel="${name}"]`);
		if (!panel) return;
		if (current && current !== panel) closePanel(true);
		current = panel;
		lastTrigger = trigger || D.activeElement;
		$$(`[data-open="${name}"]`).forEach((b) => b.setAttribute('aria-expanded', 'true'));
		panel.hidden = false;
		if (scrim && name !== 'menu') { scrim.hidden = false; }
		H.classList.add('is-locked');
		if (header) header.classList.remove('is-hidden');
		void panel.offsetWidth;
		panel.classList.add('is-open');
		if (scrim && name !== 'menu') scrim.classList.add('is-open');
		setTimeout(() => {
			const first = name === 'search' ? $('[data-search-input]', panel) : focusables(panel)[0];
			if (first) first.focus({ preventScroll: true });
		}, 80);
	};
	const closePanel = (instant) => {
		const panel = current;
		if (!panel) return;
		current = null;
		const name = panel.getAttribute('data-panel');
		$$(`[data-open="${name}"]`).forEach((b) => b.setAttribute('aria-expanded', 'false'));
		panel.classList.remove('is-open');
		if (scrim) scrim.classList.remove('is-open');
		const done = () => {
			if (!panel.classList.contains('is-open')) panel.hidden = true;
			if (scrim && !current) scrim.hidden = true;
		};
		if (instant || reduced) done(); else setTimeout(done, 700);
		if (!current) H.classList.remove('is-locked');
		if (lastTrigger && lastTrigger.focus && !instant) lastTrigger.focus({ preventScroll: true });
	};
	D.addEventListener('click', (e) => {
		const opener = e.target.closest('[data-open]');
		if (opener) { e.preventDefault(); openPanel(opener.getAttribute('data-open'), opener); return; }
		if (e.target.closest('[data-close]') || e.target === scrim) { closePanel(); return; }
		if (current && current.getAttribute('data-panel') === 'menu' && e.target.closest('.menu-big a')) closePanel(true);
	});
	D.addEventListener('keydown', (e) => {
		if (!current) return;
		if (e.key === 'Escape') { closePanel(); return; }
		if (e.key === 'Tab') {
			const f = focusables(current);
			if (!f.length) return;
			const first = f[0], last = f[f.length - 1];
			if (e.shiftKey && D.activeElement === first) { e.preventDefault(); last.focus(); }
			else if (!e.shiftKey && D.activeElement === last) { e.preventDefault(); first.focus(); }
		}
	});

	/* ------------------------------------------------------------ pencarian langsung */
	const sInput = $('[data-search-input]');
	const sResults = $('[data-search-results]');
	const sSuggest = $('[data-search-suggest]');
	if (sInput && sResults && Y.storeApi) {
		let timer = null, ctrl = null;
		const render = (items, q) => {
			if (!items.length) {
				sResults.innerHTML = `<p class="search-results__empty">${esc(t('noResults'))}</p>`;
				return;
			}
			sResults.innerHTML = items.map((p, i) => {
				const img = p.images && p.images[0] ? (p.images[0].thumbnail || p.images[0].src) : '';
				const minor = p.prices ? Number(p.prices.currency_minor_unit || 0) : 0;
				const price = p.prices ? Number(p.prices.price) / Math.pow(10, minor) : 0;
				return `<a class="result" style="--i:${i}" href="${esc(p.permalink)}"><span class="result__img">${img ? `<img src="${esc(img)}" alt="" loading="lazy">` : ''}</span><span class="result__name">${p.name}</span><span class="result__price">${rupiah(price)}</span></a>`;
			}).join('') + `<a class="link-arrow search-results__all" href="${esc(Y.search + encodeURIComponent(q))}">${esc(t('seeAll'))}</a>`;
		};
		const run = (q) => {
			if (ctrl) ctrl.abort();
			if (q.length < 2) { sResults.innerHTML = ''; if (sSuggest) sSuggest.hidden = false; return; }
			if (sSuggest) sSuggest.hidden = true;
			ctrl = new AbortController();
			fetch(`${Y.storeApi}?search=${encodeURIComponent(q)}&per_page=8`, { signal: ctrl.signal, credentials: 'same-origin' })
				.then((r) => r.json())
				.then((items) => render(Array.isArray(items) ? items : [], q))
				.catch(() => {});
		};
		sInput.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(() => run(sInput.value.trim()), 220); });
		$$('[data-search-term]').forEach((b) => b.addEventListener('click', () => { sInput.value = b.getAttribute('data-search-term'); run(sInput.value); sInput.focus(); }));
	}

	/* ------------------------------------------------------------ keranjang */
	const ajax = (endpoint, data) => {
		const body = new URLSearchParams();
		body.append('nonce', Y.nonce || '');
		Object.keys(data || {}).forEach((k) => {
			const v = data[k];
			if (Array.isArray(v)) {
				v.forEach((row, i) => Object.keys(row).forEach((rk) => body.append(`${k}[${i}][${rk}]`, row[rk])));
			} else body.append(k, v);
		});
		return fetch(Y.wcAjax.replace('%%endpoint%%', endpoint), { method: 'POST', body, credentials: 'same-origin' }).then((r) => r.json());
	};
	const applyFragments = (f) => {
		if (!f) return;
		$$('.js-cart-count').forEach((el) => { el.textContent = f.count; el.setAttribute('data-count', f.count); });
		const old = $('.js-drawer-content');
		if (old && f.drawer) {
			const tpl = D.createElement('template');
			tpl.innerHTML = f.drawer.trim();
			old.replaceWith(tpl.content.firstElementChild);
		}
	};
	const bumpCart = () => {
		$$('.cart-btn').forEach((b) => { b.classList.remove('is-bump'); void b.offsetWidth; b.classList.add('is-bump'); });
	};
	const fly = (img) => {
		const target = $('.cart-btn');
		if (!img || !target || reduced || !img.animate) return Promise.resolve();
		const a = img.getBoundingClientRect();
		const b = target.getBoundingClientRect();
		if (!a.width) return Promise.resolve();
		const clone = img.cloneNode();
		clone.removeAttribute('srcset');
		clone.src = img.currentSrc || img.src;
		clone.className = 'fly';
		Object.assign(clone.style, { left: a.left + 'px', top: a.top + 'px', width: a.width + 'px', height: a.height + 'px' });
		B.appendChild(clone);
		const dx = b.left + b.width / 2 - (a.left + a.width / 2);
		const dy = b.top + b.height / 2 - (a.top + a.height / 2);
		const anim = clone.animate([
			{ transform: 'translate(0,0) scale(1)', opacity: 1, borderRadius: '12px' },
			{ transform: `translate(${dx * 0.55}px, ${dy * 0.35 - 90}px) scale(0.45)`, opacity: 1, offset: 0.55, borderRadius: '40px' },
			{ transform: `translate(${dx}px, ${dy}px) scale(0.06)`, opacity: 0.3, borderRadius: '50%' },
		], { duration: 850, easing: 'cubic-bezier(.65,0,.35,1)' });
		return anim.finished.then(() => clone.remove()).catch(() => clone.remove());
	};
	const addItems = (items, opts = {}) => {
		const btn = opts.btn;
		if (btn) btn.classList.add('is-loading');
		return ajax('yumnia_add', { items }).then((res) => {
			if (btn) btn.classList.remove('is-loading');
			if (res.errors && res.errors.length) res.errors.forEach((m) => toast(m, 'error'));
			if (!res.ok) return false;
			return fly(opts.img).then(() => {
				applyFragments(res.fragments);
				bumpCart();
				toast(items.length > 1 ? t('addedSet') : t('added'));
				if (btn) { btn.classList.add('is-done'); setTimeout(() => btn.classList.remove('is-done'), 1400); }
				if (opts.openDrawer !== false) setTimeout(() => openPanel('cart'), 220);
				return true;
			});
		}).catch(() => {
			if (btn) btn.classList.remove('is-loading');
			toast(t('error'), 'error');
			return false;
		});
	};

	const drawer = $('[data-panel="cart"]');
	if (drawer) {
		drawer.addEventListener('click', (e) => {
			const step = e.target.closest('.qty [data-step]');
			const rem = e.target.closest('[data-remove]');
			if (!step && !rem) return;
			let key, qty;
			if (rem) {
				key = rem.getAttribute('data-remove');
				qty = 0;
				const li = rem.closest('.mini-item');
				if (li) li.classList.add('is-removing');
			} else {
				const box = step.closest('.qty');
				const val = $('.qty__val', box);
				key = box.getAttribute('data-key');
				qty = clamp(parseInt(val.textContent, 10) + parseInt(step.getAttribute('data-step'), 10), 0, parseInt(box.getAttribute('data-max') || '99', 10));
				val.textContent = qty;
				if (qty === 0) { const li = box.closest('.mini-item'); if (li) li.classList.add('is-removing'); }
			}
			drawer.classList.add('is-busy');
			ajax('yumnia_qty', { key, qty }).then((res) => {
				setTimeout(() => {
					applyFragments(res.fragments);
					drawer.classList.remove('is-busy');
				}, qty === 0 ? 380 : 0);
				(res.errors || []).forEach((m) => toast(m, 'error'));
			}).catch(() => { drawer.classList.remove('is-busy'); toast(t('error'), 'error'); });
		});
	}
	// Halaman dibuka lagi dari tombol kembali: segarkan isi keranjang.
	window.addEventListener('pageshow', (e) => {
		if (e.persisted && Y.wcAjax) ajax('yumnia_fragments', {}).then((r) => applyFragments(r.fragments)).catch(() => {});
	});

	/* ------------------------------------------------------------ logika varian */
	const matches = (v, sel) => Object.keys(sel).every((k) => !sel[k] || !v.a[k] || v.a[k] === sel[k]);
	const resolve = (p, sel) => {
		if (!p.attrs || !p.attrs.length) return p.stock ? { id: p.id, p: p.price, s: true, q: p.q, img: '' } : null;
		if (!p.attrs.every((a) => sel[a.k])) return null;
		return p.vars.find((v) => matches(v, sel)) || null;
	};
	const optionAvailable = (p, sel, key, slug) => {
		const test = Object.assign({}, sel, { [key]: slug });
		return p.vars.some((v) => v.s && matches(v, test));
	};
	const colorImage = (p, slug) => {
		const v = p.vars.find((x) => x.a.attribute_pa_warna === slug && x.img);
		return v ? v : null;
	};

	/* ------------------------------------------------------------ sheet pilih varian (tambah cepat & set) */
	const sheet = $('[data-panel="sheet"]');
	const sheetBody = sheet && $('[data-sheet-body]', sheet);
	const sheetBtn = sheet && $('[data-sheet-submit]', sheet);
	let sheetState = null;

	const pickMarkup = (p, idx) => {
		const groups = (p.attrs || []).map((a) => {
			const isColor = a.t === 'pa_warna';
			const opts = a.o.map((o) => `<label class="opt${isColor ? ' opt--swatch' : ''}"><input type="radio" name="p${idx}-${esc(a.k)}" data-key="${esc(a.k)}" value="${esc(o.s)}">${isColor ? `<span class="opt__swatch" style="--c:${esc(o.h)}"></span><span class="opt__name">${esc(o.n)}</span>` : `<span class="opt__text">${esc(o.n)}</span>`}</label>`).join('');
			return `<fieldset class="option" data-attr="${esc(a.k)}"><legend class="option__legend"><span>${esc(a.l)}: <strong data-attr-value>—</strong></span></legend><div class="option__list">${opts}</div></fieldset>`;
		}).join('');
		return `<div class="pick" data-pick="${idx}"><img class="pick__img" src="${esc(p.img)}" alt="" width="72" height="90"><div><p class="pick__name">${esc(p.name)}</p><p class="pick__price" data-pick-price>${rupiah(p.price)}</p>${groups}<p class="pick__stock" data-pick-stock></p></div></div>`;
	};
	const refreshPick = (p, root, sel) => {
		(p.attrs || []).forEach((a) => {
			$$(`input[data-key="${a.k}"]`, root).forEach((inp) => {
				const ok = optionAvailable(p, sel, a.k, inp.value);
				inp.closest('.opt').classList.toggle('is-unavailable', !ok);
				inp.checked = sel[a.k] === inp.value;
			});
			const chosen = a.o.find((o) => o.s === sel[a.k]);
			const label = $(`[data-attr="${a.k}"] [data-attr-value]`, root);
			if (label) label.textContent = chosen ? chosen.n : '—';
		});
		const v = resolve(p, sel);
		const price = $('[data-pick-price]', root);
		const stock = $('[data-pick-stock]', root);
		if (price) price.textContent = rupiah(v ? v.p : p.price);
		if (stock) stock.textContent = v ? (v.s ? (v.q !== null && v.q !== undefined && v.q <= 3 ? t('lowStock').replace('%d', v.q) : t('inStock')) : t('soldOut')) : '';
		const img = $('.pick__img', root);
		if (img && sel.attribute_pa_warna) {
			const ci = colorImage(p, sel.attribute_pa_warna);
			if (ci && ci.img) img.src = ci.img;
		}
		return v && v.s ? v : null;
	};
	const updateSheet = () => {
		if (!sheetState) return;
		let ready = true;
		sheetState.items.forEach((it, i) => {
			it.v = refreshPick(it.p, $(`[data-pick="${i}"]`, sheetBody), it.sel);
			if (!it.v) ready = false;
		});
		sheetBtn.disabled = !ready;
	};
	const openSheet = (products, opts = {}) => {
		if (!sheet) return;
		sheetState = { items: products.map((p) => ({ p, sel: Object.assign({}, p.pre || {}), v: null })), img: opts.img, set: !!opts.set };
		sheetState.items.forEach((it) => {
			(it.p.attrs || []).forEach((a) => {
				if (a.o.length === 1) it.sel[a.k] = a.o[0].s;
				if (it.sel[a.k] && !optionAvailable(it.p, it.sel, a.k, it.sel[a.k])) delete it.sel[a.k];
				if (a.t === 'pa_warna' && !it.sel[a.k]) {
					const first = a.o.find((o) => optionAvailable(it.p, it.sel, a.k, o.s));
					if (first) it.sel[a.k] = first.s;
				}
			});
		});
		$('.sheet__title', sheet).textContent = opts.set ? t('pickSet', 'Ambil set ini') : t('pick', 'Pilih varian');
		$('.btn__label', sheetBtn).textContent = opts.set ? t('addAll') : t('add');
		sheetBody.innerHTML = sheetState.items.map((it, i) => pickMarkup(it.p, i)).join('');
		updateSheet();
		openPanel('sheet', opts.trigger);
	};
	if (sheet) {
		sheetBody.addEventListener('change', (e) => {
			const inp = e.target.closest('input[data-key]');
			if (!inp || !sheetState) return;
			const idx = parseInt(inp.closest('[data-pick]').getAttribute('data-pick'), 10);
			sheetState.items[idx].sel[inp.getAttribute('data-key')] = inp.value;
			updateSheet();
		});
		sheetBtn.addEventListener('click', () => {
			if (!sheetState) return;
			const items = sheetState.items.filter((it) => it.v).map((it) => ({ id: it.v.id, qty: 1 }));
			if (!items.length) return;
			const img = sheetState.img;
			closePanel(true);
			addItems(items, { img });
		});
	}

	/* ------------------------------------------------------------ kartu produk */
	D.addEventListener('click', (e) => {
		const quick = e.target.closest('[data-quick-add]');
		if (quick) {
			e.preventDefault();
			const card = quick.closest('[data-card]');
			const p = json(card, 'data-product');
			const img = $('.card__img--main', card);
			if (!p) return;
			if (!p.attrs || !p.attrs.length) { addItems([{ id: p.id, qty: 1 }], { img, btn: quick }); return; }
			const active = $('.swatch.is-active', card);
			if (active) p.pre = { attribute_pa_warna: active.getAttribute('data-swatch') };
			openSheet([p], { img, trigger: quick });
			return;
		}
		const setBtn = e.target.closest('[data-add-set]');
		if (setBtn) {
			const look = setBtn.closest('[data-set]');
			const list = look && json(look, 'data-set');
			if (list && list.length) openSheet(list, { set: true, img: $('.look__img', look), trigger: setBtn });
			return;
		}
		const sw = e.target.closest('.swatch');
		if (sw) { e.preventDefault(); swapCard(sw); return; }
		const link = e.target.closest('.card__link, .card__title a');
		if (link && 'CSSViewTransitionRule' in window && !reduced) {
			const card = link.closest('.card');
			const img = card && $('.card__img--main', card);
			if (img) img.style.viewTransitionName = 'pdp-hero';
		}
	});
	const swapCard = (sw) => {
		const card = sw.closest('[data-card]');
		const img = card && $('.card__img--main', card);
		const src = sw.getAttribute('data-img');
		if (!img || !src || img.getAttribute('src') === src) return;
		$$('.swatch', card).forEach((s) => s.classList.toggle('is-active', s === sw));
		img.removeAttribute('srcset');
		img.classList.remove('is-swapping');
		void img.offsetWidth;
		img.src = src;
		img.classList.add('is-swapping');
	};
	if (fine) {
		D.addEventListener('pointerover', (e) => {
			const sw = e.target.closest && e.target.closest('.swatch');
			if (sw) swapCard(sw);
		});
	}
	// Bersihkan nama transisi bila kembali lewat bfcache.
	window.addEventListener('pageshow', () => $$('.card__img--main').forEach((i) => { i.style.viewTransitionName = ''; }));

	/* ------------------------------------------------------------ rel produk */
	$$('[data-rail]').forEach((rail) => {
		const section = rail.closest('section') || rail.parentElement;
		const bar = $('[data-rail-bar]', section);
		const prev = $('[data-rail-prev]', section);
		const next = $('[data-rail-next]', section);
		const update = () => {
			const max = rail.scrollWidth - rail.clientWidth;
			const p = max > 0 ? rail.scrollLeft / max : 0;
			const vis = rail.scrollWidth ? rail.clientWidth / rail.scrollWidth : 1;
			if (bar) { bar.style.width = (vis * 100) + '%'; bar.style.transform = `translateX(${p * (1 / vis - 1) * 100}%)`; }
			if (prev) prev.disabled = rail.scrollLeft < 4;
			if (next) next.disabled = rail.scrollLeft > max - 4;
		};
		rail.addEventListener('scroll', () => requestAnimationFrame(update), { passive: true });
		window.addEventListener('resize', update, { passive: true });
		update();
		const by = (dir) => rail.scrollBy({ left: dir * rail.clientWidth * 0.75, behavior: reduced ? 'auto' : 'smooth' });
		if (prev) prev.addEventListener('click', () => by(-1));
		if (next) next.addEventListener('click', () => by(1));

		// Seret dengan mouse.
		let down = false, startX = 0, startL = 0, moved = false;
		rail.addEventListener('pointerdown', (e) => {
			if (e.pointerType !== 'mouse' || e.button !== 0 || e.target.closest('button')) return;
			down = true; moved = false; startX = e.clientX; startL = rail.scrollLeft;
		});
		window.addEventListener('pointermove', (e) => {
			if (!down) return;
			const dx = e.clientX - startX;
			if (!moved && Math.abs(dx) > 6) { moved = true; rail.classList.add('is-dragging'); }
			if (moved) rail.scrollLeft = startL - dx;
		});
		window.addEventListener('pointerup', () => {
			if (!down) return;
			down = false;
			if (moved) {
				setTimeout(() => rail.classList.remove('is-dragging'), 30);
			}
		});
		rail.addEventListener('click', (e) => { if (moved) { e.preventDefault(); e.stopPropagation(); moved = false; } }, true);
	});

	/* ------------------------------------------------------------ padu-padan (tab di beranda) */
	const lookTabs = $('[data-looks-tabs]');
	if (lookTabs) {
		lookTabs.addEventListener('click', (e) => {
			const tab = e.target.closest('[data-look-tab]');
			if (!tab || tab.classList.contains('is-active')) return;
			const idx = tab.getAttribute('data-look-tab');
			$$('[data-look-tab]', lookTabs).forEach((b) => { const on = b === tab; b.classList.toggle('is-active', on); b.setAttribute('aria-selected', on ? 'true' : 'false'); });
			$$('[data-look]').forEach((s) => {
				const on = s.getAttribute('data-look') === idx;
				s.hidden = !on;
				s.classList.toggle('is-active', on);
				if (on) {
					s.classList.remove('is-entering');
					void s.offsetWidth;
					s.classList.add('is-entering');
					$$('[data-reveal], [data-split]', s).forEach((el) => { el.classList.remove('is-in'); void el.offsetWidth; requestAnimationFrame(() => el.classList.add('is-in')); });
				}
			});
		});
	}

	/* ------------------------------------------------------------ akordeon halus */
	const animateDetails = (details) => {
		const summary = $('summary', details);
		if (!summary || details.dataset.anim) return;
		details.dataset.anim = '1';
		let body = details.querySelector(':scope > .acc__body');
		if (!body) {
			body = D.createElement('div');
			body.className = 'details-body';
			Array.from(details.childNodes).forEach((n) => { if (n !== summary) body.appendChild(n); });
			details.appendChild(body);
		}
		body.style.overflow = 'hidden';
		summary.addEventListener('click', (e) => {
			if (reduced || !body.animate) return;
			e.preventDefault();
			if (details.open) {
				const h = body.offsetHeight;
				body.animate([{ height: h + 'px', opacity: 1 }, { height: '0px', opacity: 0 }], { duration: 420, easing: 'cubic-bezier(.65,0,.35,1)' }).finished.then(() => { details.open = false; });
				details.classList.add('is-closing');
				setTimeout(() => details.classList.remove('is-closing'), 420);
			} else {
				details.open = true;
				const h = body.offsetHeight;
				body.animate([{ height: '0px', opacity: 0 }, { height: h + 'px', opacity: 1 }], { duration: 520, easing: 'cubic-bezier(.22,1,.36,1)' });
			}
		});
	};
	$$('details.acc, .wp-block-details').forEach(animateDetails);

	/* ------------------------------------------------------------ detail produk */
	const pdp = $('[data-pdp]');
	if (pdp) {
		const p = json(pdp, 'data-product');
		const form = $('[data-pdp-form]', pdp);
		const addBtn = $('[data-add-btn]', pdp);
		const addLabel = addBtn && $('.btn__label', addBtn);
		const stockEl = $('[data-stock]', pdp);
		const varInput = $('[data-variation-id]', pdp);
		const qtyInput = $('.qty__input', pdp);
		const track = $('[data-gallery-track]', pdp);
		const items = track ? $$('.gallery__item', track) : [];
		const bar = $('[data-pdp-bar]');
		const barImg = $('[data-pdp-bar-img]');
		const sel = {};
		let currentVar = null;

		const goTo = (i) => {
			const item = items[i];
			if (!item) return;
			if (getComputedStyle(track).display === 'flex') {
				track.scrollTo({ left: item.offsetLeft - track.offsetLeft, behavior: reduced ? 'auto' : 'smooth' });
			} else if (i > 0) {
				const r = item.getBoundingClientRect();
				if (r.top < 0 || r.bottom > window.innerHeight) item.scrollIntoView({ behavior: reduced ? 'auto' : 'smooth', block: 'center' });
				item.classList.remove('is-flash'); void item.offsetWidth; item.classList.add('is-flash');
			}
		};
		if (track) {
			const count = $('[data-gallery-index]', pdp);
			const dots = $$('[data-gallery-go]', pdp);
			track.addEventListener('scroll', () => {
				const i = Math.round(track.scrollLeft / Math.max(1, track.clientWidth));
				if (count) count.textContent = i + 1;
				dots.forEach((d, n) => d.classList.toggle('is-active', n === i));
			}, { passive: true });
			dots.forEach((d) => d.addEventListener('click', () => goTo(parseInt(d.getAttribute('data-gallery-go'), 10))));

			// Lihat foto besar.
			track.addEventListener('click', (e) => {
				const img = e.target.closest('.gallery__img');
				if (!img) return;
				const start = items.indexOf(img.closest('.gallery__item'));
				const lb = D.createElement('div');
				lb.className = 'lightbox';
				lb.setAttribute('role', 'dialog');
				lb.setAttribute('aria-modal', 'true');
				lb.innerHTML = items.map((it) => { const im = $('img', it); return `<img src="${esc(im.getAttribute('data-full') || im.currentSrc || im.src)}" alt="${esc(im.alt)}">`; }).join('');
				const close = D.createElement('button');
				close.className = 'icon-btn lightbox__close';
				close.setAttribute('aria-label', 'Tutup');
				close.innerHTML = '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg>';
				B.appendChild(lb);
				B.appendChild(close);
				H.classList.add('is-locked');
				lb.scrollLeft = start * lb.clientWidth;
				requestAnimationFrame(() => lb.classList.add('is-open'));
				close.focus();
				const shut = () => { lb.remove(); close.remove(); H.classList.remove('is-locked'); D.removeEventListener('keydown', onKey); img.focus && img.focus(); };
				const onKey = (ev) => { if (ev.key === 'Escape') shut(); };
				close.addEventListener('click', shut);
				D.addEventListener('keydown', onKey);
			});
		}

		const update = () => {
			if (!p) return;
			(p.attrs || []).forEach((a) => {
				$$(`input[name="${a.k}"]`, form).forEach((inp) => {
					inp.closest('.opt').classList.toggle('is-unavailable', !optionAvailable(p, sel, a.k, inp.value));
				});
				const chosen = a.o.find((o) => o.s === sel[a.k]);
				const lbl = $(`[data-attr="${a.k}"] [data-attr-value]`, form);
				if (lbl) lbl.textContent = chosen ? chosen.n : '—';
			});
			const v = resolve(p, sel);
			currentVar = v;
			if (varInput) varInput.value = v ? v.id : 0;
			$$('[data-price]').forEach((el) => { if (v) el.textContent = rupiah(v.p); });
			if (stockEl) {
				stockEl.classList.remove('is-ok', 'is-low', 'is-out');
				if (!p.attrs.length) {
					stockEl.classList.add(p.stock ? 'is-ok' : 'is-out');
				} else if (!v) {
					stockEl.textContent = t('choose');
				} else if (!v.s) {
					stockEl.textContent = t('soldOut'); stockEl.classList.add('is-out');
				} else if (v.q !== null && v.q !== undefined && v.q <= 3) {
					stockEl.textContent = t('lowStock').replace('%d', v.q); stockEl.classList.add('is-low');
				} else {
					stockEl.textContent = t('inStock'); stockEl.classList.add('is-ok');
				}
			}
			if (addBtn && p.stock) {
				const ok = !p.attrs.length || (v && v.s);
				addBtn.setAttribute('aria-disabled', ok ? 'false' : 'true');
				if (addLabel) addLabel.textContent = ok ? t('add') : (v && !v.s ? t('soldOut') : t('choose'));
			}
			if (qtyInput) {
				const max = v && v.q ? v.q : 99;
				qtyInput.max = max;
				if (parseInt(qtyInput.value, 10) > max) qtyInput.value = max;
			}
		};

		if (form && p) {
			$$('input[type="radio"]', form).forEach((inp) => { if (inp.checked) sel[inp.name] = inp.value; });
			// Pilihkan warna pertama yang tersedia supaya pembeli tinggal memilih ukuran.
			const colorAttr = (p.attrs || []).find((a) => a.t === 'pa_warna');
			if (colorAttr && !sel[colorAttr.k]) {
				const first = colorAttr.o.find((o) => optionAvailable(p, sel, colorAttr.k, o.s));
				const inp = first && $(`input[name="${colorAttr.k}"][value="${first.s}"]`, form);
				if (inp) { inp.checked = true; sel[colorAttr.k] = first.s; }
			}
			form.addEventListener('change', (e) => {
				const inp = e.target;
				if (inp.type !== 'radio') return;
				sel[inp.name] = inp.value;
				update();
				if (inp.name === 'attribute_pa_warna') {
					const v = colorImage(p, inp.value);
					const idx = v ? items.findIndex((it) => parseInt(it.getAttribute('data-image-id'), 10) === v.gi) : 0;
					goTo(idx >= 0 ? idx : 0);
					if (barImg && v && v.img) barImg.src = v.img;
				}
			});
			$$('[data-qty-input] [data-step]', form).forEach((b) => b.addEventListener('click', () => {
				const max = parseInt(qtyInput.max || '99', 10);
				qtyInput.value = clamp((parseInt(qtyInput.value, 10) || 1) + parseInt(b.getAttribute('data-step'), 10), 1, max);
			}));
			form.addEventListener('submit', (e) => {
				if (!Y.wcAjax) return;
				e.preventDefault();
				const v = resolve(p, sel);
				if (p.attrs.length && !v) {
					const missing = p.attrs.find((a) => !sel[a.k]);
					const fs = missing && $(`[data-attr="${missing.k}"]`, form);
					if (fs && fs.animate) fs.animate([{ transform: 'translateX(0)' }, { transform: 'translateX(-8px)' }, { transform: 'translateX(8px)' }, { transform: 'translateX(0)' }], { duration: 360 });
					toast(missing && missing.t === 'pa_warna' ? t('chooseColor') : t('chooseSize'), 'error');
					return;
				}
				if (v && !v.s) { toast(t('soldOut'), 'error'); return; }
				const heroImg = $('.gallery__item .gallery__img', pdp);
				addItems([{ id: v ? v.id : p.id, qty: parseInt(qtyInput ? qtyInput.value : 1, 10) || 1 }], { img: heroImg, btn: addBtn });
			});
			update();
		}

		$$('[data-open-details]', pdp).forEach((b) => b.addEventListener('click', () => {
			const d = $(`[data-details="${b.getAttribute('data-open-details')}"]`, pdp);
			if (!d) return;
			if (!d.open) $('summary', d).click();
			setTimeout(() => d.scrollIntoView({ behavior: reduced ? 'auto' : 'smooth', block: 'center' }), 120);
		}));

		// Bilah beli di HP saat tombol utama tidak terlihat.
		if (bar && addBtn && 'IntersectionObserver' in window) {
			new IntersectionObserver(([en]) => {
				const show = !en.isIntersecting && en.boundingClientRect.top < 0;
				bar.classList.toggle('is-visible', show);
				bar.setAttribute('aria-hidden', show ? 'false' : 'true');
				$('[data-pdp-bar-btn]', bar).tabIndex = show ? 0 : -1;
				B.classList.toggle('has-pdp-bar', show && window.innerWidth < 1000);
			}).observe(addBtn);
			$('[data-pdp-bar-btn]', bar).addEventListener('click', () => {
				if (!p.attrs.length || currentVar) { form.requestSubmit ? form.requestSubmit() : form.submit(); return; }
				form.scrollIntoView({ behavior: reduced ? 'auto' : 'smooth', block: 'center' });
				toast(t('choose'), 'error');
			});
		}
	}

	/* ------------------------------------------------------------ katalog */
	$$('[data-autosubmit]').forEach((s) => s.addEventListener('change', () => s.form.submit()));

	/* ------------------------------------------------------------ setelah pesan → WhatsApp */
	const thanks = $('[data-thanks]');
	if (thanks) {
		const url = thanks.getAttribute('data-wa');
		const key = 'yumnia-wa-' + thanks.getAttribute('data-order');
		const num = $('[data-countdown]', thanks);
		let sent = false;
		try { sent = !!localStorage.getItem(key); } catch (e) { /* abaikan */ }
		if (sent) {
			const txt = $('[data-countdown-text]', thanks);
			if (txt) txt.textContent = 'Sudah mengirim pesan ke admin? Kalau belum, tekan tombol di bawah.';
		} else if (url) {
			let n = parseInt(num ? num.textContent : '4', 10);
			const tick = setInterval(() => {
				n -= 1;
				if (num) num.textContent = Math.max(0, n);
				if (n <= 0) {
					clearInterval(tick);
					try { localStorage.setItem(key, '1'); } catch (e) { /* abaikan */ }
					window.location.href = url;
				}
			}, 1000);
			const btn = $('[data-wa-btn]', thanks);
			if (btn) btn.addEventListener('click', () => { clearInterval(tick); try { localStorage.setItem(key, '1'); } catch (e) { /* abaikan */ } });
		}
	}

	/* ------------------------------------------------------------ lain-lain */
	$$('[data-to-top]').forEach((b) => b.addEventListener('click', () => window.scrollTo({ top: 0, behavior: reduced ? 'auto' : 'smooth' })));
	D.addEventListener('click', (e) => {
		const a = e.target.closest('a[href^="#"]');
		if (!a || a.getAttribute('href').length < 2) return;
		const target = D.getElementById(a.getAttribute('href').slice(1));
		if (!target) return;
		e.preventDefault();
		target.scrollIntoView({ behavior: reduced ? 'auto' : 'smooth', block: 'start' });
		history.replaceState(null, '', a.getAttribute('href'));
	});
})();
