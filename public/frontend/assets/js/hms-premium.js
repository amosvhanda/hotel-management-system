/* Mopane Heights — booking helpers: smarter date pairs and instant room filters. No dependencies beyond jQuery UI (already loaded). */
(function () {
    'use strict';

    /* ---------- Dates: check-out follows check-in ---------- */
    function initDates($) {
        if (!$ || !$.datepicker) return;
        var fmt = 'yy-mm-dd';

        function nights($form) {
            var a = $form.find('input[name=check_in]').val();
            var b = $form.find('input[name=check_out]').val();
            var $note = $form.find('.hms-nights');
            if (!a || !b) { $note.remove(); return; }
            var d1 = $.datepicker.parseDate(fmt, a), d2 = $.datepicker.parseDate(fmt, b);
            var n = Math.round((d2 - d1) / 86400000);
            if (!$note.length) { $note = $('<small class="hms-nights" aria-live="polite"></small>').insertAfter($form.find('input[name=check_out]')); }
            $note.text(n > 0 ? n + (n === 1 ? ' night' : ' nights') : '');
        }

        $(document).on('change', 'input[name=check_in].dt_picker', function () {
            var $in = $(this), $form = $in.closest('form');
            var $out = $form.find('input[name=check_out]');
            if (!$out.length || !$in.val()) return;
            var d = $.datepicker.parseDate(fmt, $in.val());
            var next = new Date(d.getTime()); next.setDate(next.getDate() + 1);
            $out.datepicker('option', 'minDate', next);
            var cur = $out.val() ? $.datepicker.parseDate(fmt, $out.val()) : null;
            if (!cur || cur <= d) { $out.datepicker('setDate', next); }
            nights($form);
            setTimeout(function () { $out.datepicker('show'); }, 80);
        });
        $(document).on('change', 'input[name=check_out].dt_picker', function () { nights($(this).closest('form')); });
    }

    /* ---------- Room filters: the form is a normal GET form; this only makes it feel instant ---------- */
    function initFilters() {
        var form = document.getElementById('hmsFilters');
        if (!form) return;

        var price = form.querySelector('input[type=range]');
        var out = document.getElementById('fPriceOut');
        var sample = form.getAttribute('data-money-sample') || '$1,000';
        function money(n) { return sample.replace(/1[,\s.]?000/, Math.round(n).toLocaleString('en-US')); }

        form.classList.add('is-enhanced');   // hides the "Show rooms" button; keyboard users can still press Enter
        var sending = false;
        function send() {
            if (sending) return;
            sending = true;
            form.classList.add('is-loading');
            if (form.requestSubmit) { form.requestSubmit(); } else { form.submit(); }
        }

        if (price && out) {
            price.addEventListener('input', function () {
                var max = parseFloat(price.getAttribute('data-max'));
                out.textContent = parseFloat(price.value) >= max ? 'any' : 'up to ' + money(price.value);
            });
        }

        form.addEventListener('change', function (e) {
            var t = e.target;
            if (t.name === 'check_in') return;                       // wait for check-out
            if (t.name === 'check_out') {
                var inEl = form.querySelector('[name=check_in]');
                if (!t.value || !inEl || !inEl.value) return;       // both dates or nothing
            }
            send();
        });

        window.addEventListener('pageshow', function () { sending = false; form.classList.remove('is-loading'); });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initDates(window.jQuery);
        initFilters();
    });
})();
