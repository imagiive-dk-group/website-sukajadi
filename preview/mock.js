/**
 * Tiruan server untuk pratinjau statis: keranjang (disimpan di sessionStorage) dan pencarian.
 * Di WordPress sungguhan, file ini tidak dipakai.
 */
(() => {
	const P = window.PREVIEW || { items: {}, search: [] };
	const KEY = 'yumnia-preview-cart';
	const load = () => { try { return JSON.parse(sessionStorage.getItem(KEY)) || []; } catch (e) { return []; } };
	const save = (c) => { try { sessionStorage.setItem(KEY, JSON.stringify(c)); } catch (e) { /* abaikan */ } };
	const rp = (n) => 'Rp' + Math.round(n).toLocaleString('id-ID');
	const svg = (d) => `<svg class="icon" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${d}</svg>`;
	const mono = '<svg class="monogram drawer__empty-mark" viewBox="0 0 100 120" aria-hidden="true"><path d="M22 16C33 3 67 3 78 16" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round"/><path d="M50 68C32 62 19 44 22 16" fill="none" stroke="currentColor" stroke-width="8" stroke-linecap="round"/><path d="M50 68C68 62 81 44 78 16" fill="none" stroke="currentColor" stroke-width="8" stroke-linecap="round"/><path d="M50 68V114" fill="none" stroke="currentColor" stroke-width="8" stroke-linecap="round"/></svg>';

	const render = () => {
		const cart = load();
		const count = cart.reduce((a, b) => a + b.qty, 0);
		if (!cart.length) {
			return { count, drawer: `<div class="drawer__content js-drawer-content"><div class="drawer__empty">${mono}<p class="drawer__empty-title">Keranjangmu masih kosong</p><p class="drawer__empty-text">Yuk, lihat koleksi hijab dan sandal dari Sukajadi.</p><a class="btn" href="katalog.html"><span class="btn__label">Mulai belanja</span></a></div></div>` };
		}
		let subtotal = 0;
		const rows = cart.map((c, i) => {
			const it = P.items[c.id];
			subtotal += it.price * c.qty;
			return `<li class="mini-item" data-key="${c.key}" style="--i:${i}"><a class="mini-item__img" href="${it.url}" tabindex="-1"><img src="${it.img}" alt=""></a><div class="mini-item__info"><a class="mini-item__name" href="${it.url}">${it.name}</a>${it.meta ? `<p class="mini-item__meta">${it.meta}</p>` : ''}<div class="mini-item__row"><div class="qty" data-key="${c.key}" data-max="${it.max}"><button type="button" class="qty__btn" data-step="-1" aria-label="Kurangi">${svg('<path d="M5 12h14"/>')}</button><span class="qty__val">${c.qty}</span><button type="button" class="qty__btn" data-step="1" aria-label="Tambah" ${c.qty >= it.max ? 'disabled' : ''}>${svg('<path d="M12 5v14M5 12h14"/>')}</button></div><span class="mini-item__price">${rp(it.price * c.qty)}</span></div></div><button type="button" class="mini-item__remove" data-remove="${c.key}" aria-label="Hapus">${svg('<path d="M6 6l12 12M18 6 6 18"/>')}</button></li>`;
		}).join('');
		const foot = `<div class="drawer__foot"><div class="drawer__total"><span>Subtotal</span><strong>${rp(subtotal)}</strong></div><p class="drawer__note">${svg('<path d="M3 6h11v10H3zM14 10h4l3 3v3h-7"/><circle cx="7" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/>')}Ongkir dihitung admin dan dikonfirmasi lewat WhatsApp.</p><a class="btn btn--block btn--wa" href="terima-kasih.html"><span class="btn__label">Lanjut pesan</span></a><a class="drawer__link" href="#">Lihat keranjang</a></div>`;
		return { count, drawer: `<div class="drawer__content js-drawer-content"><ul class="mini-cart">${rows}</ul>${foot}</div>` };
	};
	const reply = (data, ms = 380) => new Promise((r) => setTimeout(() => r(new Response(JSON.stringify(data), { headers: { 'Content-Type': 'application/json' } })), ms));

	const original = window.fetch.bind(window);
	window.fetch = (url, opts = {}) => {
		const u = String(url);
		if (u.includes('wc-ajax=')) {
			const ep = u.split('wc-ajax=')[1];
			const body = new URLSearchParams(opts.body || '');
			let cart = load();
			let added = 0;
			if (ep === 'yumnia_add') {
				for (let i = 0; body.has(`items[${i}][id]`); i++) {
					const id = body.get(`items[${i}][id]`);
					const qty = parseInt(body.get(`items[${i}][qty]`) || '1', 10);
					const it = P.items[id];
					if (!it) continue;
					const found = cart.find((c) => String(c.id) === String(id));
					if (found) found.qty = Math.min(it.max, found.qty + qty);
					else cart.push({ key: 'k' + id, id, qty: Math.min(it.max, qty) });
					added++;
				}
			} else if (ep === 'yumnia_qty') {
				const key = body.get('key');
				const qty = parseInt(body.get('qty') || '0', 10);
				cart = qty > 0 ? cart.map((c) => (c.key === key ? Object.assign(c, { qty }) : c)) : cart.filter((c) => c.key !== key);
			}
			save(cart);
			return reply({ ok: ep !== 'yumnia_add' || added > 0, added, errors: [], fragments: render() });
		}
		if (u.indexOf('store-api/products') === 0) {
			const q = (new URL(u, location.href).searchParams.get('search') || '').toLowerCase();
			return reply(P.search.filter((p) => p.name.toLowerCase().includes(q)), 150);
		}
		return original(url, opts);
	};

	document.addEventListener('DOMContentLoaded', () => {
		if (!load().length) return;
		const f = render();
		document.querySelectorAll('.js-cart-count').forEach((el) => { el.textContent = f.count; el.setAttribute('data-count', f.count); });
		const old = document.querySelector('.js-drawer-content');
		if (old) { const t = document.createElement('template'); t.innerHTML = f.drawer; old.replaceWith(t.content.firstElementChild); }
	});
})();
