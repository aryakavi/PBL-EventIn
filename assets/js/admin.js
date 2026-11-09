// Interaksi dashboard admin EventIn (JavaScript biasa, tanpa library).

// Dropdown menu akun pada navbar (Dashboard, Edit Profil, Logout).
(function () {
    var btn = document.querySelector('.user-menu-btn');
    var menu = document.querySelector('.user-menu-dropdown');

    if (!btn || !menu) {
        return;
    }

    function close() {
        menu.hidden = true;
        btn.setAttribute('aria-expanded', 'false');
    }

    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        var open = menu.hidden;
        menu.hidden = !open;
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    menu.addEventListener('click', function (e) {
        e.stopPropagation();
    });

    document.addEventListener('click', close);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            close();
        }
    });
})();

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
