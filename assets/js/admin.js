// Interaksi dashboard admin EventIn (JavaScript biasa, tanpa library).

// Toggle sidebar (hamburger) pada tampilan mobile.
(function () {
  var sidebar = document.getElementById("sidebar");
  var toggle = document.querySelector(".navbar-toggle");
  var overlay = document.querySelector(".sidebar-overlay");

  if (!sidebar || !toggle || !overlay) {
    return;
  }

  function setOpen(open) {
    sidebar.classList.toggle("is-open", open);
    overlay.classList.toggle("is-open", open);
    toggle.setAttribute("aria-expanded", open ? "true" : "false");
    toggle.setAttribute("aria-label", open ? "Tutup menu" : "Buka menu");
  }

  toggle.addEventListener("click", function () {
    setOpen(!sidebar.classList.contains("is-open"));
  });

  overlay.addEventListener("click", function () {
    setOpen(false);
  });

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      setOpen(false);
    }
  });

  sidebar.querySelectorAll(".menu-item").forEach(function (link) {
    link.addEventListener("click", function () {
      setOpen(false);
    });
  });
})();

// Dropdown menu akun (Edit Profil & Logout)
(function () {
  var btn = document.querySelector(".user-menu-btn");
  var menu = document.querySelector(".user-menu-dropdown");

  if (!btn || !menu) {
    return;
  }

  btn.addEventListener("click", function (e) {
    e.stopPropagation();
    var open = menu.hidden;
    menu.hidden = !open;
    btn.setAttribute("aria-expanded", open ? "true" : "false");
  });

  document.addEventListener("click", function () {
    menu.hidden = true;
    btn.setAttribute("aria-expanded", "false");
  });
})();

// QR Code Scanner
(function () {
  var reader = document.getElementById("reader");

  if (!reader || typeof Html5Qrcode === "undefined") {
    return;
  }

  var scanner = new Html5Qrcode("reader");

  Html5Qrcode.getCameras()
    .then(function (cameras) {
      if (cameras.length === 0) {
        alert("Kamera tidak ditemukan.");
        return;
      }

      console.log("Daftar kamera:", cameras);

      // Prioritaskan kamera belakang.
      var kamera = cameras.find(function (camera) {
        return /back|rear|environment|belakang/i.test(camera.label);
      });

      // Jika tidak ditemukan, pilih kamera fisik pertama.
      if (!kamera) {
        kamera = cameras.find(function (camera) {
          return !/virtual|obs/i.test(camera.label);
        });
      }

      if (!kamera) {
        alert("Kamera tidak ditemukan.");
        return;
      }

      console.log("Kamera dipilih:", kamera);

      scanner
        .start(
          kamera.id,
          {
            fps: 10,
          },
          function (decodedText) {
            console.log("QR terbaca:", decodedText);

            scanner.stop().catch(function (error) {
              console.error("Gagal menghentikan scanner:", error);
            });
          },
          function (errorMessage) {},
        )
        .catch(function (error) {
          console.error("Kamera gagal dibuka:", error);
          alert("Gagal membuka kamera: " + error);
        });
    })
    .catch(function (error) {
      console.error("Gagal mendeteksi kamera:", error);
      alert("Gagal mendeteksi kamera:", error);
    });
})();
