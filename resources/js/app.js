import './bootstrap';

import Alpine from 'alpinejs';
import flatpickr from 'flatpickr';

window.Alpine = Alpine;

Alpine.start();

document.querySelectorAll('[data-datepicker]').forEach((el) => {
    flatpickr(el, {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd M Y',
        allowInput: true,
    });
});
