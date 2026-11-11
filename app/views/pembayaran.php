<?php
$activeNav = 'tiket';
$base = '../../';

require_once __DIR__ . '/../../includes/data.php';
require_once __DIR__ . '/../../includes/koneksi.php';
require_once __DIR__ . '/../../app/models/Tiket.php';
require_once __DIR__ . '/../../app/models/PembayaranRepository.php';
require_once __DIR__ . '/../../app/models/PembayaranGatewayRepository.php';
require_once __DIR__ . '/../../app/controllers/MidtransController.php';
require_once __DIR__ . '/../../includes/storage.php';
require_once __DIR__ . '/../../includes/midtrans.php';

$emptyText = 'kosong, masih kurang';

// Halaman ini khusus peserta yang sudah login.
$sesi = $_SESSION['user'] ?? null;
if (!is_array($sesi) || empty($sesi['id_pengguna'])) {
    header('Location: ' . $base . $routes['login']);
    exit;
}

if (is_admin_role($sesi['role'] ?? '')) {
    header('Location: ' . $base . $routes['admin-dashboard']);
    exit;
}

$idPengguna = (int) $sesi['id_pengguna'];
$idTiket = (int) ($_GET['id_tiket'] ?? $_POST['id_tiket'] ?? 0);
$idEventFallback = (int) ($_GET['id'] ?? 0);

$tiketRepo = isset($pdo) && $pdo instanceof PDO ? new Tiket($pdo) : null;
$bayarRepo = isset($pdo) && $pdo instanceof PDO ? new PembayaranRepository($pdo) : null;

$tiket = $tiketRepo !== null && $idTiket > 0
    ? $tiketRepo->findForUser($idTiket, $idPengguna)
    : false;
$tiketFound = $tiket !== false && !empty($tiket);

$existing = $tiketFound && $bayarRepo !== null
    ? $bayarRepo->latestForTicket($idTiket)
    : false;

$notice = '';
$error = '';

$midtransController = isset($pdo) && $pdo instanceof PDO ? new MidtransController($pdo) : null;
$gatewayRepo = isset($pdo) && $pdo instanceof PDO ? new PembayaranGatewayRepository($pdo) : null;
$midtransReady = $midtransController !== null && $midtransController->gatewayReady();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = (string) ($_POST['aksi'] ?? 'manual');

    if (!$tiketFound) {
        $error = 'Transaksi tidak ditemukan. Daftar event terlebih dahulu.';
    } elseif (!csrf_valid($_POST['csrf_token'] ?? null)) {
        $error = 'Sesi keamanan kedaluwarsa. Muat ulang halaman lalu coba lagi.';
    } elseif (str_starts_with(strtolower((string) ($tiket['status_bayar'] ?? '')), 'lunas')) {
        $error = 'Pembayaran tiket ini sudah lunas.';
    } elseif ($aksi === 'midtrans') {
        $hasil = $midtransController !== null
            ? $midtransController->start($idTiket, $idPengguna, is_array($sesi) ? $sesi : [])
            : ['success' => false, 'message' => 'Koneksi database gagal.'];

        if (!$hasil['success']) {
            $error = $hasil['message'];
        } else {
            header('Location: pembayaran.php?id_tiket=' . $idTiket . '&bayar=' . rawurlencode((string) ($hasil['order_id'] ?? '')));
            exit;
        }
    } else {
        $metodeKey = (string) ($_POST['metode'] ?? '');
        $daftarMetode = [
            'bank'   => 'Transfer Bank Terbuka',
            'wallet' => 'Sistem Dompet Digital (E-Wallet)',
            'qris'   => 'Pembayaran Cepat QRIS Mandiri',
        ];

        if (!isset($daftarMetode[$metodeKey])) {
            $error = 'Pilih metode pembayaran terlebih dahulu.';
        } else {
            $upload = storage_upload_bukti($_FILES['bukti_bayar'] ?? [], $idTiket);

            if (!$upload['success']) {
                $error = $upload['message'];
            } elseif ($bayarRepo === null) {
                $error = 'Koneksi database gagal. Bukti belum tersimpan.';
            } else {
                try {
                    $idPembayaran = $bayarRepo->createAndMarkPending(
                        $idTiket,
                        (string) $upload['path'],
                        $daftarMetode[$metodeKey]
                    );

                    header('Location: konfirmasi-pembayaran.php?id=' . $idPembayaran);
                    exit;
                } catch (Throwable $e) {
                    error_log('EventIn simpan pembayaran error: ' . $e->getMessage());
                    $error = 'Bukti sudah terunggah, tetapi data pembayaran gagal disimpan. Coba lagi.';
                }
            }
        }
    }
}

/* --- Transaksi Midtrans yang masih berjalan --- */
$orderIdAktif  = (string) ($_GET['bayar'] ?? '');
$snapToken     = '';
$snapRedirect  = '';
$gatewayRow    = false;

if ($gatewayRepo !== null && $tiketFound) {
    $gatewayRow = $orderIdAktif !== ''
        ? $gatewayRepo->findByOrderId($orderIdAktif)
        : $gatewayRepo->latestForTicket($idTiket);

    if ($gatewayRow !== false && $orderIdAktif !== '' && (int) ($gatewayRow['id_tiket'] ?? 0) !== $idTiket) {
        $gatewayRow = false;
    }
}

if ($gatewayRow !== false && $orderIdAktif !== '') {
    $raw = $gatewayRow['raw_response'] ?? null;
    $raw = is_string($raw) ? json_decode($raw, true) : (is_array($raw) ? $raw : []);
    $raw = is_array($raw) ? $raw : [];

    $snapToken    = (string) ($raw['token'] ?? '');
    $snapRedirect = (string) ($raw['redirect_url'] ?? '');
}

if (($_GET['midtrans'] ?? '') === 'selesai') {
    $notice = 'Pembayaran diproses. Status tiket akan diperbarui otomatis setelah Midtrans mengirim notifikasi.';
}

$statusBayar = strtolower((string) ($tiket['status_bayar'] ?? ''));
$isLunas = str_starts_with($statusBayar, 'lunas');
$isMenunggu = str_starts_with($statusBayar, 'menunggu');
$isBatal = str_starts_with($statusBayar, 'batal') || str_starts_with($statusBayar, 'ditolak');

$labelStatus = match (true) {
    $isLunas    => 'Lunas / Terverifikasi',
    $isMenunggu => 'Menunggu Verifikasi Admin',
    $isBatal    => 'Gagal / Ditolak',
    default     => 'Belum Bayar',
};

$buktiPath = (string) ($existing['bukti_pembayaran'] ?? '');
$buktiUrl = $buktiPath !== '' ? storage_public_url($buktiPath) : '';

$harga = $tiketFound
    ? (float) ($tiket['nominal'] ?? $tiket['harga'] ?? 0)
    : 0.0;

$ringkasan = [
    'judul'    => $tiketFound && ($tiket['nama_event'] ?? '') !== '' ? $tiket['nama_event'] : $emptyText,
    'tiket'    => '1x Tiket ' . ($tiketFound && ($tiket['nama_kategori'] ?? '') !== '' ? $tiket['nama_kategori'] : $emptyText),
    'subtotal' => format_rupiah($harga),
    'admin'    => format_rupiah(0),
    'total'    => format_rupiah($harga),
];

$peserta = (string) ($sesi['nama'] ?? '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<?php
$pageTitle  = 'Pembayaran Tiket';
$assetBase  = '../../assets';
$pageStyles = ['style.css'];
require __DIR__ . '/../../includes/head.php';
?>
</head>
<body>

<!-- Navbar Peserta -->
<?php require __DIR__ . '/../../includes/navbar.php'; ?>

<!-- Konten Utama Pembayaran -->
<main class="payment-container">
    <!-- Stepper -->
    <div class="stepper">
        <div class="step-item is-active">
            <span class="step-circle">1</span>
            <span class="step-label">Pembayaran</span>
        </div>
        <div class="pay-step-line"></div>
        <div class="step-item">
            <span class="step-circle">2</span>
            <span class="step-label">Konfirmasi</span>
        </div>
    </div>

    <?php if ($error !== ''): ?>
        <div class="profil-alert profil-alert--error" role="alert"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if ($notice !== ''): ?>
        <div class="profil-alert profil-alert--success" role="status"><?= e($notice) ?></div>
    <?php endif; ?>

    <?php if (!$tiketFound): ?>
        <div class="profil-alert profil-alert--error" role="alert">
            Transaksi tidak ditemukan. Silakan daftar event terlebih dahulu.
            <?php if ($idEventFallback > 0): ?>
                <a href="event-detail.php?id=<?= (int) $idEventFallback ?>">Kembali ke detail event</a>.
            <?php else: ?>
                <a href="<?= e($base . $routes['explore']) ?>">Jelajah event</a>.
            <?php endif; ?>
        </div>
    <?php else: ?>

        <?php if ($buktiUrl !== ''): ?>
            <div class="profil-alert <?= $isLunas ? 'profil-alert--success' : ($isBatal ? 'profil-alert--error' : '') ?>" role="status">
                Status pembayaran: <strong><?= e($labelStatus) ?></strong>.
                <a href="<?= e($buktiUrl) ?>" target="_blank" rel="noopener">Lihat bukti terakhir</a>.
            </div>
        <?php endif; ?>

        <!-- Grid 2 Kolom -->
        <form action="" method="post" enctype="multipart/form-data" class="checkout-grid">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="id_tiket" value="<?= (int) $idTiket ?>">

            <!-- Kolom Kiri: Metode Pembayaran & Upload Bukti -->
            <div class="checkout-left">
                <section class="checkout-card">
                    <h2 class="checkout-card-title">Pilih Metode Pembayaran</h2>

                    <div class="method-list">
                        <label class="method-option is-selected" data-method="bank">
                            <div class="method-header">
                                <div class="method-info">
                                    <span class="method-badge">Bank</span>
                                    <span class="method-name">Transfer Bank Terbuka</span>
                                </div>
                                <input type="radio" name="metode" value="bank" class="method-radio" checked>
                            </div>
                        </label>

                        <label class="method-option" data-method="wallet">
                            <div class="method-header">
                                <div class="method-info">
                                    <span class="method-badge">Wallet</span>
                                    <span class="method-name">Sistem Dompet Digital (E-Wallet)</span>
                                </div>
                                <input type="radio" name="metode" value="wallet" class="method-radio">
                            </div>
                        </label>

                        <label class="method-option" data-method="qris">
                            <div class="method-header">
                                <div class="method-info">
                                    <span class="method-badge">QR</span>
                                    <span class="method-name">Pembayaran Cepat QRIS Mandiri</span>
                                </div>
                                <input type="radio" name="metode" value="qris" class="method-radio">
                            </div>

                            <div class="qris-preview" id="qris-box" hidden>
                                <div class="qris-code" aria-label="Kode QRIS">
                                    <svg viewBox="0 0 120 120" width="128" height="128" fill="#0f172a">
                                        <path d="M10 10h36v36H10V10zm6 6v24h24V16H16zm6 6h12v12H22V22zm52-12h36v36H74V10zm6 6v24h24V16H80zm6 6h12v12H86V22zM10 74h36v36H10V74zm6 6v24h24V80H16zm6 6h12v12H22V86zm32-76h8v16h-8V10zm12 8h6v14h-6V18zm-12 20h18v8H54v-8zm-44 20h16v8H10v-8zm24 0h32v8H34v-8zm40 0h12v8H74v-8zm20 0h16v8H94v-8zM54 74h12v12H54V74zm20 0h8v20h-8V74zm16 0h20v8H90v-8zm-36 20h16v16H54V94zm24 8h16v8H78v-8zm20-16h12v24H98V86z"/>
                                    </svg>
                                </div>
                            </div>
                        </label>
                    </div>
                </section>

                <section class="checkout-card">
                    <h2 class="checkout-card-title">Upload Bukti Pembayaran</h2>
                    <label for="bukti_bayar" class="file-input-box">
                        <span id="file-label-text">Upload File</span>
                        <input type="file" id="bukti_bayar" name="bukti_bayar" accept="image/jpeg,image/png,image/webp,application/pdf" hidden>
                    </label>
                    <p class="upload-hint">Format JPG, PNG, WEBP, atau PDF. Maksimal 5 MB.</p>
                    <?php if ($buktiUrl !== ''): ?>
                        <p class="upload-hint">Bukti sebelumnya: <a href="<?= e($buktiUrl) ?>" target="_blank" rel="noopener">buka file</a></p>
                    <?php endif; ?>
                </section>
            </div>

            <!-- Kolom Kanan: Ringkasan Pemesanan -->
            <aside class="checkout-right">
                <section class="checkout-card summary-card">
                    <h2 class="checkout-card-title">Ringkasan Pemesanan</h2>

                    <div class="summary-event">
                        <span class="summary-sublabel">DETAIL TENTANG ACARA</span>
                        <strong class="summary-event-title"><?= e($ringkasan['judul']) ?></strong>
                        <span class="summary-ticket-type"><?= e($ringkasan['tiket']) ?></span>
                    </div>

                    <p class="payment-status-line">Status: <strong><?= e($labelStatus) ?></strong></p>

                    <hr class="summary-divider">

                    <div class="summary-rows">
                        <div class="summary-row">
                            <span class="muted">Subtotal Tiket</span>
                            <span class="summary-price"><?= e($ringkasan['subtotal']) ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="muted">Biaya Administrasi</span>
                            <span class="summary-price"><?= e($ringkasan['admin']) ?></span>
                        </div>
                    </div>

                    <hr class="summary-divider">

                    <div class="summary-total">
                        <strong>Total Pembayaran</strong>
                        <strong class="total-amount"><?= e($ringkasan['total']) ?></strong>
                    </div>

                    <?php if ($isLunas): ?>
                        <a href="tiket-saya.php" class="btn btn-primary btn-block">Lihat Tiket Saya</a>
                    <?php elseif ($midtransReady): ?>
                        <button type="submit" name="aksi" value="midtrans" class="btn btn-primary btn-block" formnovalidate>
                            Bayar Online (Midtrans)
                        </button>
                        <p class="upload-hint">
                            Virtual account, e-wallet, QRIS, dan kartu. Status tiket diperbarui otomatis
                            setelah pembayaran berhasil.
                        </p>
                        <button type="submit" name="aksi" value="manual" class="btn btn-outline btn-block">
                            <?= $buktiUrl !== '' ? 'Kirim Ulang Bukti Transfer' : 'Upload Bukti Transfer Manual' ?>
                        </button>
                    <?php else: ?>
                        <button type="submit" name="aksi" value="manual" class="btn btn-primary btn-block">
                            <?= $buktiUrl !== '' ? 'Kirim Ulang Bukti' : 'Bayar Sekarang' ?>
                        </button>
                    <?php endif; ?>
                </section>
            </aside>
        </form>
    <?php endif; ?>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<script src="../../assets/js/main.js?v=<?= e(asset_version('assets/js/main.js')) ?>"></script>

<?php if ($snapToken !== '' && !$isLunas): ?>
    <script src="<?= e(MidtransClient::snapScriptUrl()) ?>" data-client-key="<?= e(MidtransClient::clientKey()) ?>"></script>
    <script>
    (function () {
        var tujuan = 'pembayaran.php?id_tiket=<?= (int) $idTiket ?>';

        if (!window.snap) {
            return;
        }

        window.snap.pay(<?= json_encode($snapToken, JSON_UNESCAPED_SLASHES) ?>, {
            onSuccess: function () { window.location.href = tujuan + '&midtrans=selesai'; },
            onPending: function () { window.location.href = tujuan + '&midtrans=selesai'; },
            onError: function () { window.location.href = tujuan + '&midtrans=selesai'; },
            onClose: function () { window.location.href = tujuan; }
        });
    })();
    </script>
<?php endif; ?>
</body>
</html>