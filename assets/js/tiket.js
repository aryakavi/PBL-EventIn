// Interaksi halaman "Tiket Saya" (JavaScript biasa, tanpa framework).

// 1. Render QR code tiket dari kode_qr di database
(function () {
    var targets = document.querySelectorAll('[data-qr]');

    targets.forEach(function (target) {
        var kode = target.getAttribute('data-qr');

        if (!kode) {
            return;
        }

        if (typeof QRCode === 'undefined') {
            target.textContent = kode;
            return;
        }

        new QRCode(target, {
            text: kode,
            width: 144,
            height: 144,
            correctLevel: QRCode.CorrectLevel.M
        });
    });
})();

// 2. Unduh e-tiket lewat dialog cetak browser (hanya kartu yang dipilih)
(function () {
    var tombol = document.querySelectorAll('[data-print-tiket]');

    tombol.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var item = btn.closest('.tiket-item');

            document.querySelectorAll('.tiket-item.is-printing').forEach(function (el) {
                el.classList.remove('is-printing');
            });

            if (item) {
                item.open = true;
                item.classList.add('is-printing');
            }

            window.print();
        });
    });

    window.addEventListener('afterprint', function () {
        document.querySelectorAll('.tiket-item.is-printing').forEach(function (el) {
            el.classList.remove('is-printing');
        });
    });
})();

// 3. Submit pencarian saat menekan Enter
(function () {
    var input = document.querySelector('.tiket-search input');

    if (!input) {
        return;
    }

    input.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            input.form.submit();
        }
    });
})();
