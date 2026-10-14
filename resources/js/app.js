import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import GLightbox from 'glightbox';
import bookingForm from './booking-form';
import customPackage from './custom-package';

window.rupiah = (n) => 'Rp' + Math.round(Number(n) || 0).toLocaleString('id-ID');

window.Alpine = Alpine;
Alpine.plugin(collapse);
Alpine.data('bookingForm', bookingForm);
Alpine.data('customPackage', customPackage);
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('.glightbox')) {
        GLightbox({ selector: '.glightbox', touchNavigation: true, loop: true });
    }
});

