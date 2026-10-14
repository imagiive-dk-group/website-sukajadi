// Formulir booking multi-langkah (Alpine.js).
// Validasi akhir tetap dilakukan di server (StoreBookingRequest).
export default (config) => ({
    step: config.startStep || 1,
    type: config.preset.type || 'package',
    packageId: config.preset.package_id ? String(config.preset.package_id) : '',
    tierId: config.preset.package_tier_id ? String(config.preset.package_tier_id) : '',
    attractionIds: (config.preset.attraction_ids || []).map(String),
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
        if (!this.pax && (this.selectedTier || this.type === 'custom')) {
            this.pax = this.minPax;
        }
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
    get selectedAttractions() {
        return config.attractions.filter((a) => this.attractionIds.includes(String(a.id)));
    },
    get pricePerPax() {
        if (this.type === 'custom') {
            return config.customBase + this.selectedAttractions.reduce((sum, a) => sum + a.price, 0);
        }
        return this.selectedTier ? this.selectedTier.price : 0;
    },
    get minPax() {
        if (this.type === 'custom') return config.customMinPax;
        return this.selectedTier ? this.selectedTier.min_pax : 1;
    },
    get paxNumber() {
        return Math.max(0, parseInt(this.pax, 10) || 0);
    },
    get subtotal() {
        return this.pricePerPax * this.paxNumber;
    },
    get addonLines() {
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
