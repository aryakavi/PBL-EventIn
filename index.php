
<?php

require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/koneksi.php';
require __DIR__ . '/app/models/Event.php';
   
$eventModel = new Event($pdo);
$eventsDb = $eventModel->getAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EventIn - Temukan Event Kampus Terbaik</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700;800&family=Geist+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
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
                <a class="btn btn-white" href="<?= e($routes['register']) ?>">Daftar Organisasi</a>
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
                        <small>PEMILIK TIKET</small>
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
                <a class="btn btn-primary" href="<?= e($routes['detail']) ?>">Lihat Detail Acara</a>
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
    <section class="section popular">
        <div class="section-header">
            <h2>hi Event Terpopuler</h2>
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
            <?php foreach ($eventsDb as $ev):
                $img = event_image($ev['id_event']); ?>
                <article class="event-card" data-category="<?= e($ev['nama_kategori']) ?>">
                    <div class="event-cover cover-<?= $ev['id_event'] ?>"
                         <?php if ($img): ?>style="background-image: url('<?= e($img) ?>');"<?php endif; ?>>
                        <span class="event-badge"><?= e(strtoupper($ev['nama_kategori'])) ?></span>
                    </div>
                    <div class="event-body">
                        <h3><?= e($ev['nama_event']) ?></h3>
                        <div class="event-meta">
                            <p>Tanggal: <?= e( date('d M Y : H:i', strtotime($ev['tanggal_mulai']))) ?></p>
                            <p>Lokasi: <?= e($ev['lokasi']) ?></p>
                        </div>
                        <div class="event-footer">
                            <span class="price">Rp.<?= e($ev['harga']) ?></span>
                            <a class="btn btn-primary" href="<?= e($routes['detail']) ?>">Daftar</a>
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
            <a class="btn btn-primary" href="<?= e($routes['register']) ?>">Daftar Sebagai Penyelenggara</a>
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
    <section class="section faq">
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
        <p>Daftarkan diri secara gratis hari ini dan mulailah mengeksplorasi ribuan peluang berharga di sekitarmu.</p>
        <a class="btn btn-primary" href="<?= e($routes['register']) ?>">Buat Akun Sekarang</a>
    </section>

</main>

<?php require __DIR__ . '/includes/footer.php'; ?>

<script src="assets/js/main.js"></script>
</body>
</html>
