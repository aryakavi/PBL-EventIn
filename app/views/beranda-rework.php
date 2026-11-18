<?php
$base      = '../../';
$activeNav = 'home';
$navAnchors = [
    'home'     => '#beranda',
    'explore'  => '#jelajah',
    'partners' => '#mitra',
];

require __DIR__ . '/../../includes/data.php';
?>
<!DOCTYPE html>
<html lang="id" class="smooth-scroll">
<head>
<?php
$pageTitle  = 'Platform Manajemen & Penemuan Event Kampus';
$assetBase  = '../../assets';
$pageStyles = ['style.css'];
require __DIR__ . '/../../includes/head.php';
?>
</head>
<body>

<!-- ================= NAVBAR LANDING PAGE ================= -->
<?php require __DIR__ . '/../../includes/navbar.php'; ?>

<!-- ================= SECTION 1: BERANDA ================= -->
<section id="beranda" class="scroll-section">
    <!-- Hero Utama -->
    <div class="landing-hero">
        <div class="landing-container hero-grid">
            <div class="hero-content">
                <h1>Temukan Event Kampus Terbaik &amp; Berjejaring Lebih Luas</h1>
                <p>Pusat penemuan kegiatan akademik, seminar nasional, kompetisi mahasiswa, dan workshop sertifikasi profesional terpadu dalam satu platform digital eksklusif.</p>
                <div class="hero-buttons">
                    <a href="#jelajah" class="btn btn-primary">Jelajah Sekarang</a>
                    <a href="#mitra" class="btn btn-white">Daftar Organisasi</a>
                </div>
            </div>

            <div class="hero-stats">
                <?php foreach ($statistikHero as $st): ?>
                    <div class="hero-stat-card">
                        <strong><?= e($st['angka']) ?></strong>
                        <span><?= e($st['label']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Sorotan Minggu Ini -->
    <div class="landing-container highlight-wrap">
        <div class="landing-section-head">
            <h2>Sorotan Minggu Ini</h2>
            <p>Event pilihan dengan antusiasme pendaftar tertinggi</p>
        </div>

        <div class="highlight-grid">
            <!-- Kartu Visual Tiket -->
            <div class="ticket-mockup"> 
                <div class="mockup-top">
                    <span class="mockup-label">TIKET ELEKTRONIK RESMI</span>
                    <span class="mockup-badge">E</span>
                </div>
                <div class="mockup-middle">
                    <h3><?= e($featured['ticket_title']) ?></h3>
                    <p><?= e($featured['ticket_meta']) ?></p>
                </div>
                <div class="mockup-bottom">
                    <div>
                        <span class="mockup-owner-label">PEMILIK TIKET</span>
                        <strong class="mockup-owner-name"><?= e($featured['ticket_owner']) ?></strong>
                    </div>
                    <span class="mockup-qr">QR</span>
                </div>
            </div>

            <!-- Deskripsi Sorotan -->
            <div class="highlight-info">
                <h3><?= e($featured['title']) ?></h3>
                <p><?= e($featured['desc']) ?></p>
                <ul class="highlight-perks">
                    <?php foreach ($featured['perks'] as $perk): ?>
                        <li>&#10003; <?= e($perk) ?></li>
                    <?php endforeach; ?>
                </ul>
                <a href="detail-event.php" class="btn btn-primary">Lihat Detail Acara</a>
            </div>
        </div>
    </div>
</section>

<!-- ================= SECTION 2: JELAJAH EVENT ================= -->
<section id="jelajah" class="scroll-section">
    <div class="landing-container explore-wrap">
        <h2 class="explore-heading">Jelajahi Berbagai Event Kampus</h2>

        <!-- Bar Pencarian & Filter -->
        <form class="explore-filter-bar" onsubmit="return false;">
            <input type="text" id="explore-search" class="filter-search" placeholder="Cari judul event, organisasi, atau kata kunci..">
            <select class="filter-select">
                <option value="">Kategori</option>
                <option value="SEMINAR">Seminar</option>
                <option value="WORKSHOP">Workshop</option>
                <option value="KOMPETISI">Kompetisi</option>
                <option value="WEBINAR">Webinar</option>
            </select>
            <select class="filter-select">
                <option value="">Lokasi</option>
                <option value="Gedung C">Aula Utama Gedung C</option>
                <option value="Rektorat">Gedung Rektorat</option>
            </select>
            <select class="filter-select">
                <option value="">Tanggal</option>
                <option value="maret">Maret 2026</option>
                <option value="april">April 2026</option>
            </select>
            <select class="filter-select">
                <option value="">Harga</option>
                <option value="berbayar">Berbayar</option>
                <option value="gratis">Gratis</option>
            </select>
            <button type="submit" class="btn btn-primary">Terapkan</button>
        </form>

        <!-- Pill Kategori -->
        <div class="category-pills" role="tablist">
            <button type="button" class="cat-pill is-active" data-filter-cat="ALL">Semua Kategori</button>
            <button type="button" class="cat-pill" data-filter-cat="SEMINAR">Seminar</button>
            <button type="button" class="cat-pill" data-filter-cat="WORKSHOP">Workshop</button>
            <button type="button" class="cat-pill" data-filter-cat="KOMPETISI">Kompetisi</button>
            <button type="button" class="cat-pill" data-filter-cat="WEBINAR">Webinar</button>
        </div>

        <!-- Grid 9 Kartu Event -->
        <div class="event-cards-grid" id="event-grid">
            <?php foreach ($katalogEvent as $ev): ?>
                <article class="catalog-card" data-category="<?= e($ev['kategori']) ?>">
                    <div class="catalog-thumb">
                        <img src="<?= e($ev['gambar']) ?>" alt="Judul Event Eksklusif Ke-<?= (int)$ev['no'] ?>">
                    </div>
                    <div class="catalog-body">
                        <span class="cat-tag tag-<?= e($ev['warna']) ?>"><?= e($ev['kategori']) ?></span>
                        <h3 class="catalog-title">Judul Event Eksklusif Ke-<?= (int)$ev['no'] ?> Kampus Utama</h3>
                        <div class="catalog-meta">
                            <span>Tanggal: 24 Maret 2026</span>
                            <span>Lokasi: Aula Utama Gedung C</span>
                        </div>
                        <div class="catalog-footer">
                            <strong class="catalog-price">Rp 45.000</strong>
                            <a href="detail-event.php" class="btn btn-primary btn-sm-card">Daftar</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ================= SECTION 3: MITRA ================= -->
<section id="mitra" class="scroll-section">
    <!-- Hero Kemitraan (Dark Navy) -->
    <div class="partner-hero">
        <div class="landing-container partner-hero-grid">
            <div class="partner-hero-left">
                <h2>Publikasikan Event Kampus &amp; Jangkau Ribuan Mahasiswa Potensial</h2>
                <p>Solusi terintegrasi untuk BEM, Himpunan Mahasiswa (HMP), UKM, dan mitra industri untuk mengelola registrasi tiket, presensi QR code, hingga distribusi e-sertifikat otomatis.</p>

                <div class="hero-buttons">
                    <a href="index.php" class="btn btn-primary">Ajukan Jadi Mitra Event &rarr;</a>
                    <a href="#cara-kerja" class="btn btn-dark-outline">&#128214; Pelajari Panduan &amp; Syarat</a>
                </div>

                <div class="partner-mini-stats">
                    <div>
                        <strong>120+</strong>
                        <span>Organisasi &amp; UKM Aktif</span>
                    </div>
                    <div>
                        <strong>45K+</strong>
                        <span>Tiket Terdistribusi</span>
                    </div>
                    <div>
                        <strong>99.8%</strong>
                        <span>Verifikasi QR Sukses</span>
                    </div>
                </div>
            </div>

            <!-- Kartu Pratinjau Portal Penyelenggara -->
            <div class="partner-preview-card">
                <div class="preview-card-head">
                    <div class="preview-org">
                        <span class="preview-org-icon">&#9638;</span>
                        <div>
                            <strong>Portal Penyelenggara</strong>
                            <span>HMTI Polinema (Terverifikasi)</span>
                        </div>
                    </div>
                    <span class="live-scanner-pill">Live Scanner</span>
                </div>

                <div class="preview-event-box">
                    <div class="preview-event-top">
                        <div>
                            <span class="preview-mini-label">EVENT MENDATANG</span>
                            <h4>National Tech Summit 2026</h4>
                            <p>Auditorium Utama Lt. 8 &bull; <strong>320/400 Kuota</strong></p>
                        </div>
                        <span class="preview-qr-box">QR</span>
                    </div>

                    <div class="preview-feed-row">
                        <span><i class="feed-dot"></i> Budi Gunawan (2141720088)</span>
                        <strong class="feed-tag">PRESENSI VALID</strong>
                    </div>
                    <div class="preview-feed-row">
                        <span><i class="feed-dot"></i> Aryakavi (2141720092)</span>
                        <strong class="feed-tag">SKPI TERINTEGRASI</strong>
                    </div>
                </div>

                <div class="preview-card-foot">
                    <span>Status Kemitraan: <strong>Aktif Resmi</strong></span>
                    <strong class="free-hassle">100% Bebas Ribet &#10004;</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Bar Organisasi & Himpunan Mahasiswa -->
    <div class="org-logos-band">
        <div class="landing-container">
            <p class="org-band-title">BEKERJASAMA DENGAN ORGANISASI &amp; HIMPUNAN MAHASISWA TERKEMUKA</p>
            <div class="org-logos-grid">
                <?php foreach ($daftarOrganisasi as $org): ?>
                    <div class="org-logo-box"><?= e($org) ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Fitur Lengkap Penyelenggara -->
    <div class="landing-container features-wrap">
        <span class="section-eyebrow">&#10004; FITUR LENGKAP PENYELENGGARA</span>
        <h2 class="features-heading">Semua yang Anda Butuhkan untuk Menyukseskan Event Kampus</h2>
        <p class="features-sub">Otomatisasi seluruh alur operasional acara tanpa form manual Google Spreadsheet dan tanpa kendala sertifikat ganda.</p>

        <div class="features-grid">
            <?php foreach ($fiturMitra as $f): ?>
                <article class="feature-card">
                    <span class="feature-icon icon-<?= e($f['warna']) ?>"><?= $f['ikon'] ?></span>
                    <h3><?= e($f['judul']) ?></h3>
                    <p><?= e($f['deskripsi']) ?></p>
                    <span class="feature-foot"><?= e($f['tag']) ?></span>
                </article>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Alur Pendaftaran Mudah (5 Langkah) -->
    <div class="steps-band" id="cara-kerja">
        <div class="landing-container">
            <span class="steps-eyebrow">ALUR PENDAFTARAN MUDAH</span>
            <h2 class="steps-heading">Bagaimana Cara Kerja Menjadi Mitra?</h2>
            <p class="steps-sub">Hanya butuh 4 langkah mudah untuk mulai mempublikasikan dan mengelola acara kampus Anda secara profesional.</p>

            <div class="steps-grid">
                <?php foreach ($langkahMitra as $l): ?>
                    <div class="step-card">
                        <span class="step-num<?= $l['no'] === 1 ? ' is-first' : '' ?>"><?= (int)$l['no'] ?></span>
                        <h3><?= e($l['judul']) ?></h3>
                        <p><?= e($l['teks']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="steps-cta">
                <a href="index.php" class="btn btn-primary">Mulai Registrasi Organisasi &rsaquo;</a>
            </div>
        </div>
    </div>

    <!-- Pertanyaan Sering Diajukan (FAQ) -->
    <div class="landing-container faq-wrap">
        <div class="landing-section-head">
            <h2>Pertanyaan Sering Diajukan</h2>
            <p>Temukan jawaban cepat atas pertanyaan mendasar seputar platform</p>
        </div>

        <div class="faq-list">
            <?php foreach ($daftarFaqBeranda as $faq): ?>
                <div class="faq-item">
                    <button type="button" class="faq-question" aria-expanded="false">
                        <span><?= e($faq['tanya']) ?></span>
                        <span class="faq-chevron">&#9662;</span>
                    </button>
                    <div class="faq-answer" hidden>
                        <p><?= e($faq['jawab']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Banner CTA Oranye Bawah -->
    <div class="cta-orange-banner">
        <div class="landing-container">
            <h2>Siap Mengadakan Event Kampus Terbaik Anda?</h2>
            <p>Bergabunglah dengan ratusan pengurus organisasi mahasiswa yang telah menyederhanakan registrasi, presensi, dan sertifikasi bersama EventIn.</p>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<script src="../../assets/js/main.js"></script>
</body>
</html>
