<?php
require __DIR__ . '/../../../includes/admin/data.php';
$halamanAktif = 'event';

$idEvent = (int) ($_GET['id'] ?? 0);
$detail  = $adminRepo !== null ? $adminRepo->event($idEvent) : [];
$peserta = $adminRepo !== null && $detail ? $adminRepo->eventTickets($idEvent) : [];

$tampil = static function ($nilai) use ($emptyText): string {
    $teks = trim((string) $nilai);

    return $teks === '' ? $emptyText : $teks;
};

$tanggal = static function (?string $nilai, string $format = 'd M Y, H:i'): string {
    if ($nilai === null || $nilai === '') {
        return '';
    }

    $time = strtotime($nilai);

    return $time === false ? '' : date($format, $time);
};

$statusEvent = $emptyText;
if (!empty($detail['tanggal_selesai']) && strtotime((string) $detail['tanggal_selesai']) < time()) {
    $statusEvent = 'Selesai';
} elseif ($detail) {
    $statusEvent = 'Aktif';
}

$badgeStatus = $statusEvent === 'Aktif' ? 'badge-info' : ($statusEvent === 'Selesai' ? 'badge-neutral' : 'badge-warning');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<?php
$pageTitle  = 'Detail Event';
$assetBase  = '../../../assets';
$pageStyles = ['admin.css'];
require __DIR__ . '/../../../includes/head.php';
?>
</head>
<body>

<?php include __DIR__ . '/../../../includes/admin/navbar.php'; ?>

<div class="layout">
    <?php include __DIR__ . '/../../../includes/admin/sidebar.php'; ?>

    <main class="content">
        <div class="page-head">
            <a class="back-link" href="event.php">&larr; Kembali ke Kelola Event</a>
            <h1>Detail Event</h1>
            <p>Pratinjau data event yang tersimpan di database.</p>
        </div>

        <section class="form-card">
            <div class="form-card-header detail-title-row">
                <div>
                    <h2><?= e($tampil($detail['nama_event'] ?? '')) ?></h2>
                    <p><?= e($tampil($detail['nama_kategori'] ?? '')) ?></p>
                </div>
                <span class="badge <?= $badgeStatus ?>"><?= e($statusEvent) ?></span>
            </div>

            <div class="detail-body">
                <div class="proof-col">
                    <span class="proof-title">Banner Event</span>
                    <div class="proof-preview">
                        <span class="proof-icon">&#128444;</span>
                        <span><?= e($tampil($detail['poster'] ?? '')) ?></span>
                    </div>
                    <span class="proof-filename"><?= e($tampil($detail['poster'] ?? '')) ?></span>
                </div>

                <div class="info-col">
                    <div class="info-item">
                        <span class="info-label">PENYELENGGARA</span>
                        <strong class="info-value"><?= e($tampil($detail['nama_ukm'] ?? '')) ?></strong>
                    </div>
                    <div class="info-item">
                        <span class="info-label">TANGGAL MULAI</span>
                        <span class="info-text"><?= e($tampil($tanggal($detail['tanggal_mulai'] ?? null))) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">TANGGAL SELESAI</span>
                        <span class="info-text"><?= e($tampil($tanggal($detail['tanggal_selesai'] ?? null))) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">LOKASI</span>
                        <span class="info-text"><?= e($tampil($detail['lokasi'] ?? '')) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">KUOTA / PESERTA</span>
                        <span class="info-text">
                            <?= e($tampil((string) ($detail['kuota'] ?? ''))) ?> kuota &middot;
                            <?= (int) ($detail['jumlah_peserta'] ?? 0) ?> peserta terdaftar
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">HARGA TIKET</span>
                        <span class="info-text"><?= e(format_rupiah($detail['harga'] ?? 0)) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">NARAHUBUNG</span>
                        <span class="info-text"><?= e($tampil($detail['pembuat_nama'] ?? '')) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">EMAIL</span>
                        <span class="info-text"><?= e($tampil($detail['pembuat_email'] ?? '')) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">WHATSAPP</span>
                        <span class="info-text"><?= e($tampil($detail['pembuat_hp'] ?? '')) ?></span>
                    </div>
                </div>
            </div>

            <div class="detail-description">
                <span class="info-label">DESKRIPSI EVENT</span>
                <p><?= nl2br(e($tampil($detail['deskripsi'] ?? ''))) ?></p>
            </div>

            <div class="form-actions">
                <a class="btn btn-outline" href="event.php">Kembali</a>
                <a class="btn btn-primary" href="event-edit.php?id=<?= (int) $idEvent ?>">Edit Event</a>
            </div>
        </section>

        <section class="form-card">
            <div class="form-card-header">
                <h2>Daftar Peserta</h2>
                <p>Data tiket peserta yang terdaftar pada event ini.</p>
            </div>

            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Nama</th>
                            <th scope="col">Email</th>
                            <th scope="col">Kode QR</th>
                            <th scope="col">Nominal</th>
                            <th scope="col">Status Bayar</th>
                            <th scope="col">Check-in</th>
                            <th scope="col">Sertifikat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($peserta)): ?>
                            <tr>
                                <td colspan="7" class="empty-state"><?= e($emptyText) ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($peserta as $t): ?>
                            <?php
                            $statusBayar = (string) ($t['status_bayar'] ?? '');
                            $badgeBayar  = match (strtolower($statusBayar)) {
                                'lunas' => 'badge-success',
                                'menunggu' => 'badge-warning',
                                default => 'badge-danger',
                            };
                            ?>
                            <tr>
                                <td><strong><?= e($tampil($t['nama'] ?? '')) ?></strong></td>
                                <td class="mono-email"><?= e($tampil($t['email'] ?? '')) ?></td>
                                <td class="mono-id"><?= e($tampil($t['kode_qr'] ?? '')) ?></td>
                                <td><?= e(format_rupiah($t['nominal'] ?? 0)) ?></td>
                                <td><span class="badge <?= $badgeBayar ?>"><?= e($tampil($statusBayar)) ?></span></td>
                                <td class="muted"><?= e($tampil($tanggal($t['waktu_checkin'] ?? null))) ?></td>
                                <td class="muted"><?= e($tampil($t['status_sertifikat'] ?? '')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<div class="sidebar-overlay" data-sidebar-close></div>

<script src="../../../assets/js/admin.js?v=<?= e(asset_version('assets/js/admin.js')) ?>"></script>
</body>
</html>