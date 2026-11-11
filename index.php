<?php

$activeNav = 'home';
$base = '';

require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/koneksi.php';
require __DIR__ . '/app/models/SiteRepository.php';
require __DIR__ . '/app/models/Event.php';
require __DIR__ . '/app/controllers/EventController.php';

$emptyText = 'kosong, masih kurang';

$siteRepo = isset($pdo) && $pdo instanceof PDO ? new SiteRepository($pdo) : null;
$totals   = $siteRepo !== null
    ? $siteRepo->totals()
    : ['event' => 0, 'peserta' => 0, 'organisasi' => 0];

$stats = [
    [number_format($totals['event'], 0, ',', '.'), 'Event Terdaftar'],
    [number_format($totals['peserta'], 0, ',', '.'), 'Peserta Aktif'],
    [number_format($totals['organisasi'], 0, ',', '.'), 'Organisasi Kampus'],
];

$eventModel = $pdo instanceof PDO ? new Event($pdo) : null;
$eventController = $eventModel !== null ? new EventController($eventModel) : null;
$eventsDb = $eventController !== null ? $eventController->index() : [];

/* Kategori filter diambil dari tabel kategori_event. */
$categories = ['semua' => 'Semua Kategori'];
$daftarOrganisasi = $siteRepo !== null ? $siteRepo->organisasi() : [];

foreach ($siteRepo !== null ? $siteRepo->kategori() : [] as $k) {
    $categories[$k['nama_kategori']] = $k['nama_kategori'];
}

/* Mitra = UKM yang terdaftar di database. */
$partners = [];
foreach ($daftarOrganisasi as $u) {
    $partners[] = $u['singkatan'] !== '' ? $u['singkatan'] : $u['nama_ukm'];
}
if (empty($partners)) {
    $partners = [$emptyText];
}

/* Organisasi terdaftar beserta jumlah event yang dijalankan. */
$orgs = [];
foreach ($daftarOrganisasi as $u) {
    $orgs[] = [
        $u['nama_ukm'] !== '' ? $u['nama_ukm'] : $emptyText,
        (int) $u['jumlah_event'] . ' event terselenggara',
    ];
}
if (empty($orgs)) {
    $orgs = [[$emptyText, $emptyText]];
}

/* Sorotan minggu ini = event dengan pendaftar terbanyak. */
$sorotan = $siteRepo !== null ? $siteRepo->sorotan() : [];

$featured = [
    'ticket_title' => ($sorotan['nama_event'] ?? '') !== '' ? $sorotan['nama_event'] : $emptyText,
    'ticket_meta'  => !empty($sorotan['tanggal_mulai'])
        ? date('d M Y, H:i', strtotime($sorotan['tanggal_mulai'])) . ' WIB | ' . ($sorotan['lokasi'] ?? '')
        : $emptyText,
    'ticket_owner' => ($sorotan['nama_ukm'] ?? '') !== ''
        ? $sorotan['nama_ukm']
        : (($sorotan['singkatan'] ?? '') !== '' ? $sorotan['singkatan'] : $emptyText),
    'title'        => ($sorotan['nama_event'] ?? '') !== '' ? $sorotan['nama_event'] : $emptyText,
    'desc'         => ($sorotan['deskripsi'] ?? '') !== '' ? $sorotan['deskripsi'] : $emptyText,
    'perks'        => [
        'Sertifikat resmi terintegrasi SKPI',
        'Akses penuh ke seluruh sesi kegiatan',
        'Konsumsi dan goodie bag peserta',
    ],
];

$featuredEventId = (int) ($sorotan['id_event'] ?? 0);
if ($featuredEventId === 0 && !empty($eventsDb[0]['id_event'])) {
    $featuredEventId = (int) $eventsDb[0]['id_event'];
}

?>


<!DOCTYPE html>
<html lang="id">

<head>
<?php
$pageTitle  = 'Temukan Event Kampus Terbaik';
$assetBase  = 'assets';
$pageStyles = ['style.css'];
require __DIR__ . '/includes/head.php';
?>
</head>

<body>

    <?php require __DIR__ . '/includes/navbar.php'; ?>

    <main>

        <!-- Hero -->
        <section class="hero">
            <div class="hero-text">
                <span class="hero-tag">SISTEM INTEGRASI KAMPUS</span>
                <h1>Temukan Event Kampus Terbaik &amp; Berjejaring Lebih Luas</h1>
                <p>Pusat penemuan kegiatan akademik, seminar nasional, kompetisi mahasiswa, dan workshop sertifikasi profesional terpadu dalam satu platform digital eksklusif.</p>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="<?= e($routes['explore']) ?>">Jelajah Sekarang</a>
                    <a class="btn btn-white" href="<?= e($routes['register-admin']) ?>">Daftar Organisasi</a>
                </div>
            </div>
            <div class="hero-stats">
                <?php foreach ($stats as [$value, $label]): ?>
                    <div class="stat-card">
                        <strong><?= e($value) ?></strong>
                        <span><?= e($label) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Sorotan minggu ini -->
        <section class="section featured">
            <div class="section-header">
                <h2>Sorotan Minggu Ini</h2>
                <p>Event pilihan dengan antusiasme pendaftar tertinggi</p>
                <hr>
            </div>
            <div class="featured-body">
                <div class="ticket-mockup">
                    <div class="ticket-top">
                        <span class="ticket-label">TIKET ELEKTRONIK RESMI</span>
                        <span class="ticket-badge">E</span>
                    </div>
                    <hr class="ticket-line">
                    <div class="ticket-info">
                        <h3><?= e($featured['ticket_title']) ?></h3>
                        <p><?= e($featured['ticket_meta']) ?></p>
                    </div>
                    <div class="ticket-bottom">
                        <div>
                            <small>PENYELENGGARA</small>
                            <strong><?= e($featured['ticket_owner']) ?></strong>
                        </div>
                        <span class="ticket-qr">QR</span>
                    </div>
                </div>

                <div class="featured-detail">
                    <h3><?= e($featured['title']) ?></h3>
                    <p><?= e($featured['desc']) ?></p>
                    <ul class="perks">
                        <?php foreach ($featured['perks'] as $perk): ?>
                            <li>&#10003; <?= e($perk) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a class="btn btn-primary" href="<?= e($routes['detail'] . ($featuredEventId > 0 ? '?id=' . $featuredEventId : '')) ?>">Lihat Detail Acara</a>
                </div>
            </div>
        </section>

        <!-- Mitra -->
        <section class="partner-logos" id="partners">
            <p>BEKERJASAMA DENGAN MITRA TERPERCAYA</p>
            <div class="partner-row">
                <?php foreach ($partners as $name): ?>
                    <div class="partner-box"><?= e($name) ?></div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Event terpopuler -->
        <section class="section popular" id="explore">
            <div class="section-header">
                <h2>Event Terpopuler</h2>
                <p>Pilih kategori yang paling sesuai dengan minat akademik dan karir Anda</p>
                <hr>
            </div>

            <div class="chips" role="tablist" aria-label="Filter kategori">
                <?php foreach ($categories as $slug => $label): ?>
                    <button type="button"
                        class="chip<?= $slug === 'semua' ? ' is-active' : '' ?>"
                        data-filter="<?= e($slug) ?>"><?= e($label) ?></button>
                <?php endforeach; ?>
            </div>

            <div class="event-grid" id="event-grid">
                <?php if (empty($eventsDb)): ?>
                    <p class="event-empty"><?= e($emptyText) ?></p>
                <?php endif; ?>
                <?php foreach ($eventsDb as $ev):
                    $img = event_image((int) $ev['id_event']);
                    $detailHref = $routes['detail'] . '?id=' . (int) $ev['id_event'];
                    $namaKategori = (string) ($ev['nama_kategori'] ?? ''); ?>
                    <article class="event-card"
                        data-category="<?= e(strtolower(str_replace(' ', '-', $namaKategori))) ?>">
                        <a class="event-cover cover-<?= (int) $ev['id_event'] ?>"
                            href="<?= e($detailHref) ?>"
                            aria-label="Lihat detail <?= e($ev['nama_event']) ?>"
                            <?php if ($img): ?>style="background-image: url('<?= e($img) ?>');" <?php endif; ?>>
                            <span class="event-badge"><?= e(strtoupper($namaKategori !== '' ? $namaKategori : $emptyText)) ?></span>
                        </a>
                        <div class="event-body">
                            <h3><a href="<?= e($detailHref) ?>"><?= e($ev['nama_event'] !== '' ? $ev['nama_event'] : $emptyText) ?></a></h3>
                            <div class="event-meta">
                                <p>Tanggal: <?= e(!empty($ev['tanggal_mulai']) ? date('d M Y : H:i', strtotime($ev['tanggal_mulai'])) : $emptyText) ?></p>
                                <p>Lokasi: <?= e(($ev['lokasi'] ?? '') !== '' ? $ev['lokasi'] : $emptyText) ?></p>
                            </div>
                            <div class="event-footer">
                                <span class="price"><?= e((float) $ev['harga'] > 0 ? format_rupiah($ev['harga']) : 'Gratis') ?></span>
                                <a class="btn btn-primary" href="<?= e($detailHref) ?>">Daftar</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Cara kerja -->
        <section class="section how">
            <div class="section-header">
                <h2>Bagaimana EventIn Bekerja?</h2>
                <p>Empat langkah mudah memulai perjalanan pengembangan diri Anda</p>
                <hr>
            </div>
            <div class="steps">
                <?php foreach ($steps as $i => [$num, $title, $desc]): ?>
                    <div class="step">
                        <div class="step-content">
                            <span class="step-num"><?= e($num) ?></span>
                            <h4><?= e($title) ?></h4>
                            <p><?= e($desc) ?></p>
                        </div>
                        <?php if ($i < count($steps) - 1): ?>
                            <span class="step-line" aria-hidden="true"></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Untuk penyelenggara -->
        <section class="organizers">
            <div class="organizers-head">
                <h2>Solusi Terbaik Bagi Penyelenggara Event</h2>
                <p>Kelola pendaftaran, transaksi pembayaran, absensi digital, hingga pengiriman sertifikat secara instan dan efisien.</p>
            </div>
            <div class="feature-grid">
                <?php foreach ($features as [$code, $title, $desc]): ?>
                    <div class="feature-card">
                        <span class="feature-icon"><?= e($code) ?></span>
                        <h4><?= e($title) ?></h4>
                        <p><?= e($desc) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="organizers-cta">
                <a class="btn btn-primary" href="<?= e($routes['register-admin']) ?>">Daftar Sebagai Penyelenggara</a>
            </div>
        </section>

        <!-- Organisasi terdaftar -->
        <section class="section orgs">
            <div class="section-header">
                <h2>Organisasi &amp; Himpunan Terdaftar</h2>
                <p>Bekerjasama langsung dengan Unit Kegiatan Mahasiswa dari berbagai kampus terkemuka</p>
                <hr>
            </div>
            <div class="org-grid">
                <?php foreach ($orgs as [$name, $members]): ?>
                    <div class="org-card">
                        <span class="org-logo">LOGO</span>
                        <div>
                            <strong><?= e($name) ?></strong>
                            <span><?= e($members) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- FAQ -->
        <section class="section faq" id="faq">
            <div class="section-header">
                <h2>Pertanyaan Sering Diajukan</h2>
                <p>Temukan jawaban cepat atas pertanyaan mendasar seputar platform</p>
                <hr>
            </div>
            <div class="faq-list">
                <?php foreach ($faqs as [$q, $a]): ?>
                    <details class="faq-item">
                        <summary>
                            <span><?= e($q) ?></span>
                            <span class="faq-icon" aria-hidden="true">v</span>
                        </summary>
                        <p><?= e($a) ?></p>
                    </details>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- CTA akhir -->
        <section class="final-cta">
            <h2>Siap Meningkatkan Pengalaman Akademik Anda?</h2>
            <p>Daftarkan diri secara gratis hari ini dan mulailah mengeksplorasi berbagai peluang kegiatan kampus di sekitarmu.</p>
            <a class="btn btn-primary" href="<?= e($routes['register']) ?>">Buat Akun Sekarang</a>
        </section>

    </main>

    <?php require __DIR__ . '/includes/footer.php'; ?>

    <script src="assets/js/main.js?v=<?= e(asset_version('assets/js/main.js')) ?>"></script>
</body>

</html>