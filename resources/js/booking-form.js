// Formulir booking multi-langkah (Alpine.js).
// Validasi akhir tetap dilakukan di server (StoreBookingRequest).
export default (config) => ({
    step: config.startStep || 1,
    type: config.preset.type || 'package',
    packageId: config.preset.package_id ? String(config.preset.package_id) : '',
    tierId: config.preset.package_tier_id ? String(config.preset.package_tier_id) : '',
    attractionIds: (config.preset.attraction_ids || []).map(String),
    partnerId: config.preset.partner_id ? String(config.preset.partner_id) : '',
    offerId: config.preset.partner_offer_id ? String(config.preset.partner_offer_id) : '',
    nights: config.preset.nights || 1,
    date: config.preset.visit_date || '',
    pax: config.preset.pax || '',
    addons: (config.preset.addons || []).map(String),
    availability: { state: 'idle', message: '' },
    clientError: '',
    timer: null,

    init() {
        if (this.packageId && !this.tierId) {
            this.tierId = String(this.tiers[0]?.id || '');
        }
        if (this.partnerId && !this.offerId) {
            this.offerId = String(this.offers[0]?.id || '');
        }
        if (!this.pax && (this.selectedTier || this.type === 'custom' || this.selectedOffer)) {
            this.pax = this.minPax;
        }
        this.$watch('partnerId', () => {
            const ids = this.offers.map((o) => String(o.id));
            if (!ids.includes(this.offerId)) this.offerId = ids[0] || '';
        });
        this.$watch('offerId', () => {
            if (this.selectedOffer && this.paxNumber < this.selectedOffer.min_qty) this.pax = this.selectedOffer.min_qty;
        });
        this.$watch('type', () => this.check());
        this.$watch('packageId', () => {
            const ids = this.tiers.map((t) => String(t.id));
            if (!ids.includes(this.tierId)) this.tierId = ids[0] || '';
            this.check();
        });
        this.$watch('date', () => this.check());
        this.$watch('pax', () => this.check());
        if (this.date) this.check();
    },

    get selectedPackage() {
        return config.packages.find((p) => String(p.id) === this.packageId) || null;
    },
    get tiers() {
        return this.selectedPackage ? this.selectedPackage.tiers : [];
    },
    get selectedTier() {
        return this.tiers.find((t) => String(t.id) === this.tierId) || null;
    },
    get selectedPartner() {
        return config.partners.find((p) => String(p.id) === this.partnerId) || null;
    },
    get offers() {
        return this.selectedPartner ? this.selectedPartner.offers : [];
    },
    get selectedOffer() {
        return this.offers.find((o) => String(o.id) === this.offerId) || null;
    },
    get isPerNight() {
        return this.type === 'partner' && this.selectedOffer?.unit === 'per_night';
    },
    get nightsNumber() {
        return this.isPerNight ? Math.max(1, parseInt(this.nights, 10) || 1) : 1;
    },
    get qtyLabel() {
        if (this.type === 'partner' && this.selectedOffer) return this.selectedOffer.qty_label;
        return config.labels.pax;
    },
    get selectedAttractions() {
        return config.attractions.filter((a) => this.attractionIds.includes(String(a.id)));
    },
    get pricePerPax() {
        if (this.type === 'partner') {
            return this.selectedOffer ? this.selectedOffer.price : 0;
        }
        if (this.type === 'custom') {
            return config.customBase + this.selectedAttractions.reduce((sum, a) => sum + a.price, 0);
        }
        return this.selectedTier ? this.selectedTier.price : 0;
    },
    get minPax() {
        if (this.type === 'partner') return this.selectedOffer ? this.selectedOffer.min_qty : 1;
        if (this.type === 'custom') return config.customMinPax;
        return this.selectedTier ? this.selectedTier.min_pax : 1;
    },
    get paxNumber() {
        return Math.max(0, parseInt(this.pax, 10) || 0);
    },
    get subtotal() {
        return this.pricePerPax * this.paxNumber * this.nightsNumber;
    },
    get addonLines() {
        if (this.type === 'partner') return [];
        return config.addons
            .filter((a) => this.addons.includes(String(a.id)))
            .map((a) => ({ ...a, subtotal: a.unit === 'per_group' ? a.price : a.price * this.paxNumber }));
    },
    get addonsTotal() {
        return this.addonLines.reduce((sum, a) => sum + a.subtotal, 0);
    },
    get total() {
        return this.subtotal + this.addonsTotal;
    },
    get packageLabel() {
        if (this.type === 'partner') {
            if (!this.selectedPartner || !this.selectedOffer) return '';
            return this.selectedPartner.name + ' — ' + this.selectedOffer.name + (this.isPerNight ? ' (' + this.nightsNumber + ' ' + config.labels.nights + ')' : '');
        }
        if (this.type === 'custom') {
            return this.selectedAttractions.map((a) => a.name).join(', ');
        }
        if (!this.selectedPackage) return '';
        return this.selectedPackage.name + (this.tiers.length > 1 && this.selectedTier ? ' — ' + this.selectedTier.name : '');
    },

    money(n) {
        return window.rupiah(n);
    },

    check() {
        clearTimeout(this.timer);
        if (!this.date) {
            this.availability = { state: 'idle', message: '' };
            return;
        }
        this.availability = { state: 'checking', message: config.labels.checking };
        this.timer = setTimeout(async () => {
            const params = new URLSearchParams({ date: this.date, pax: this.paxNumber, locale: config.locale });
            if (this.type === 'package' && this.packageId) params.set('package_id', this.packageId);
            if (this.type === 'partner') params.set('partner', 1);
            try {
                const res = await fetch(config.availabilityUrl + '?' + params.toString(), { headers: { Accept: 'application/json' } });
                const data = await res.json();
                this.availability = { state: data.ok ? 'ok' : 'error', message: data.message };
            } catch (e) {
                this.availability = { state: 'idle', message: '' };
            }
        }, 350);
    },

    validateStep(step) {
        this.clientError = '';
        if (step === 1) {
            if (this.type === 'package' && !this.selectedTier) return (this.clientError = config.labels.choosePackage), false;
            if (this.type === 'custom' && this.selectedAttractions.length === 0) return (this.clientError = config.labels.chooseActivity), false;
            if (this.type === 'partner' && !this.selectedOffer) return (this.clientError = config.labels.chooseOffer), false;
        }
        if (step === 2) {
            if (!this.date) return (this.clientError = config.labels.chooseDate), false;
            if (this.paxNumber < this.minPax) return (this.clientError = config.labels.minPax.replace(':count', this.minPax)), false;
            if (this.availability.state === 'error') return (this.clientError = this.availability.message), false;
        }
        if (step === 3) {
            const form = this.$root.querySelector('form');
            const fields = ['customer_name', 'phone', 'group_type'].map((n) => form.elements[n]);
            for (const field of fields) {
                if (field && !field.checkValidity()) {
                    field.reportValidity();
                    return false;
                }
            }
        }
        return true;
    },

    next() {
        if (this.validateStep(this.step)) {
            this.step = Math.min(4, this.step + 1);
            this.scrollTop();
        }
    },
    prev() {
        this.clientError = '';
        this.step = Math.max(1, this.step - 1);
        this.scrollTop();
    },
    goTo(step) {
        if (step < this.step) {
            this.step = step;
            this.scrollTop();
        }
    },
    scrollTop() {
        this.$nextTick(() => this.$refs.top?.scrollIntoView({ behavior: 'smooth', block: 'start' }));
    },
});
