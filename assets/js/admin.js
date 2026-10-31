// Interaksi dashboard admin EventIn (JavaScript biasa, tanpa library).

// Toggle sidebar (hamburger) pada tampilan mobile.
(function () {
    var sidebar = document.getElementById('sidebar');
    var toggle = document.querySelector('.navbar-toggle');
    var overlay = document.querySelector('.sidebar-overlay');

    if (!sidebar || !toggle || !overlay) {
        return;
    }

    function setOpen(open) {
        sidebar.classList.toggle('is-open', open);
        overlay.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        toggle.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
    }

    toggle.addEventListener('click', function () {
        setOpen(!sidebar.classList.contains('is-open'));
    });

    overlay.addEventListener('click', function () {
        setOpen(false);
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            setOpen(false);
        }
    });

    sidebar.querySelectorAll('.menu-item').forEach(function (link) {
        link.addEventListener('click', function () {
            setOpen(false);
        });
    });
})();
