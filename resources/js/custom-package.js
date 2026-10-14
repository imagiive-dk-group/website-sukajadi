// Kalkulator paket custom: pilih aktivitas → estimasi harga → lanjut ke booking.
export default (config) => ({
    selected: [],
    pax: config.minPax,

    get items() {
        return config.attractions.filter((a) => this.selected.includes(String(a.id)));
    },
    get perPax() {
        return config.basePrice + this.items.reduce((sum, a) => sum + a.price, 0);
    },
    get paxNumber() {
        return Math.max(0, parseInt(this.pax, 10) || 0);
    },
    get total() {
        return this.perPax * this.paxNumber;
    },
    get canContinue() {
        return this.items.length > 0 && this.paxNumber >= config.minPax;
    },
    get bookingUrl() {
        const params = new URLSearchParams({ custom: 1, pax: this.paxNumber });
        this.items.forEach((a) => params.append('items[]', a.id));
        return config.bookingUrl + '?' + params.toString();
    },
    money(n) {
        return window.rupiah(n);
    },
});
