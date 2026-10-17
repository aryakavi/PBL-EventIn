// Interaksi landing page EventIn (JavaScript biasa, tanpa library).

// 1. Filter kategori pada kartu event
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
