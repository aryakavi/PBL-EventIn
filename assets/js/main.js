// Interaksi landing page EventIn (JavaScript biasa, tanpa library).

// 1. Toggle tampilkan/sembunyikan kata sandi
(function () {
    var toggles = document.querySelectorAll('[data-toggle-password]');

    toggles.forEach(function (toggle) {
        toggle.addEventListener('click', function () {
            var input = document.getElementById(toggle.getAttribute('data-toggle-password'));

            if (!input) {
                return;
            }

            var show = input.type === 'password';

            input.type = show ? 'text' : 'password';
            toggle.classList.toggle('is-visible', show);
            toggle.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        });
    });
})();

// 2. Filter kategori pada kartu event
(function () {
    var chips = document.querySelectorAll('.chip');
    var cards = document.querySelectorAll('.event-card');

    chips.forEach(function (chip) {
        chip.addEventListener('click', function () {
            var filter = chip.getAttribute('data-filter');

            chips.forEach(function (c) {
                c.classList.toggle('is-active', c === chip);
            });

            cards.forEach(function (card) {
                var match = filter === 'semua' || card.getAttribute('data-category') === filter;
                card.hidden = !match;
            });
        });
    });
})();
