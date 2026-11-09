// Interaksi umum EventIn (JavaScript biasa, tanpa library).

// 1. Filter kategori pada kartu event (landing)
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
                var match = filter === 'semua'
                    || card.getAttribute('data-category') === filter.toLowerCase();
                card.hidden = !match;
            });
        });
    });
})();

// 2. Dropdown menu akun (Edit Profil & Logout)
(function () {
    var btn = document.querySelector('.user-menu-btn');
    var menu = document.querySelector('.user-menu-dropdown');

    if (!btn || !menu) {
        return;
    }

    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        var open = menu.hidden;
        menu.hidden = !open;
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    document.addEventListener('click', function () {
        menu.hidden = true;
        btn.setAttribute('aria-expanded', 'false');
    });
})();

// 3. Pilih metode pembayaran & tampilkan QRIS (halaman pembayaran)
(function () {
    var options = document.querySelectorAll('.method-option');
    var qrisBox = document.getElementById('qris-box');

    if (!options.length) {
        return;
    }

    options.forEach(function (option) {
        var radio = option.querySelector('.method-radio');

        radio.addEventListener('change', function () {
            options.forEach(function (el) {
                el.classList.remove('is-selected');
            });

            if (radio.checked) {
                option.classList.add('is-selected');
            }

            if (qrisBox) {
                qrisBox.hidden = option.getAttribute('data-method') !== 'qris';
            }
        });
    });
})();

// 4. Preview nama file bukti pembayaran (halaman pembayaran)
(function () {
    var fileInput = document.getElementById('bukti_bayar');
    var fileLabel = document.getElementById('file-label-text');

    if (!fileInput || !fileLabel) {
        return;
    }

    fileInput.addEventListener('change', function () {
        fileLabel.textContent = fileInput.files.length > 0
            ? fileInput.files[0].name
            : 'Upload File';
    });
})();

// 5. Tiket saya: buka/tutup detail & salin kode tiket
(function () {
    document.querySelectorAll('[data-toggle-ticket]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var cardId = btn.getAttribute('data-toggle-ticket');
            var card = document.getElementById(cardId);

            if (!card) {
                return;
            }

            var detail = card.querySelector('.ticket-detail');
            var textSpan = btn.querySelector('.toggle-text');
            var chevronSpan = btn.querySelector('.toggle-chevron');
            var isHidden = detail.hasAttribute('hidden');

            if (isHidden) {
                detail.removeAttribute('hidden');
                card.classList.add('is-open');
                textSpan.textContent = 'Sembunyikan Detail';
                chevronSpan.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 15 6-6 6 6"></path></svg>';
            } else {
                detail.setAttribute('hidden', '');
                card.classList.remove('is-open');
                textSpan.textContent = 'Lihat Detail';
                chevronSpan.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg>';
            }
        });
    });

    document.querySelectorAll('[data-copy]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var code = btn.getAttribute('data-copy');

            if (navigator.clipboard) {
                navigator.clipboard.writeText(code);
                var original = btn.innerHTML;
                btn.innerHTML = '&#10003;';
                setTimeout(function () { btn.innerHTML = original; }, 1200);
            }
        });
    });
})();

// 6. Daftar tiket: filter pencarian, kategori, & tab status
(function () {
    var cards = document.querySelectorAll('.ticket-card[data-nama]');
    var search = document.querySelector('[data-ticket-search]');
    var cat = document.querySelector('[data-ticket-cat]');
    var tabs = document.querySelectorAll('[data-status-tab]');

    if (!cards.length) {
        return;
    }

    var status = document.querySelector('[data-status-tab].is-active');

    function applyFilter() {
        var q = search ? search.value.trim().toLowerCase() : '';
        var kategori = cat ? cat.value : '';
        var stat = status ? status.getAttribute('data-status-tab') : '';

        cards.forEach(function (card) {
            var matchNama = q === '' || card.getAttribute('data-nama').indexOf(q) !== -1;
            var matchKat = kategori === '' || card.getAttribute('data-kategori') === kategori;
            var matchStatus = stat === '' || card.getAttribute('data-status') === stat;
            card.hidden = !(matchNama && matchKat && matchStatus);
        });
    }

    if (search) {
        search.addEventListener('input', applyFilter);
    }
    if (cat) {
        cat.addEventListener('change', applyFilter);
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t) { t.classList.remove('is-active'); });
            tab.classList.add('is-active');
            status = tab;
            applyFilter();
        });
    });

    applyFilter();
})();
