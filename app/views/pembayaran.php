<?php
$activeNav = 'tiket';
$base = '../../';

require __DIR__ . '/../../includes/data.php';

// Gunakan ?mode=midtrans di URL jika ingin melihat tampilan Midtrans (tanpa upload bukti & tanpa QR manual)
$isMidtrans = isset($_GET['mode']) && $_GET['mode'] === 'midtrans';

$peserta  = $pembayaranPeserta;
$ringkasan = $ringkasanPesanan;

// Anggap pengguna sudah login (auth asli belum tersambung) agar navbar menampilkan profil.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = [
        'id_pengguna' => 1,
        'nama'        => $pembayaranPeserta['nama'],
        'email'       => '',
        'role'        => $pembayaranPeserta['role'],
    ];
}
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
<body data-midtrans="<?= $isMidtrans ? 'true' : 'false' ?>">

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

    <!-- Grid 2 Kolom -->
    <form action="" method="post" enctype="multipart/form-data" class="checkout-grid">
        <!-- Kolom Kiri: Metode Pembayaran & Upload Bukti -->
        <div class="checkout-left">
            <section class="checkout-card">
                <h2 class="checkout-card-title">Pilih Metode Pembayaran</h2>

                <div class="method-list">
                    <!-- Opsi 1: Transfer Bank Terbuka -->
                    <label class="method-option is-selected" data-method="bank">
                        <div class="method-header">
                            <div class="method-info">
                                <span class="method-badge">Bank</span>
                                <span class="method-name">Transfer Bank Terbuka</span>
                            </div>
                            <input type="radio" name="metode" value="bank" class="method-radio" checked>
                        </div>
                    </label>

                    <!-- Opsi 2: E-Wallet -->
                    <label class="method-option" data-method="wallet">
                        <div class="method-header">
                            <div class="method-info">
                                <span class="method-badge">Wallet</span>
                                <span class="method-name">Sistem Dompet Digital (E-Wallet)</span>
                            </div>
                            <input type="radio" name="metode" value="wallet" class="method-radio">
                        </div>
                    </label>

                    <!-- Opsi 3: QRIS Mandiri -->
                    <label class="method-option" data-method="qris">
                        <div class="method-header">
                            <div class="method-info">
                                <span class="method-badge">QR</span>
                                <span class="method-name">Pembayaran Cepat QRIS Mandiri</span>
                            </div>
                            <input type="radio" name="metode" value="qris" class="method-radio">
                        </div>

                        <?php if (!$isMidtrans): ?>
                            <!-- Kotak QR Code (Hanya muncul saat opsi QRIS dipilih pada mode non-midtrans) -->
                            <div class="qris-preview" id="qris-box" hidden>
                                <div class="qris-code" aria-label="Kode QRIS">
                                    <!-- Ilustrasi SVG QR Code -->
                                    <e?= $ikonQrKecil ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </label>
                </div>
            </section>

            <?php if (!$isMidtrans): ?>
                <!-- Card Upload Bukti Pembayaran (Hanya di mode non-midtrans) -->
                <section class="checkout-card">
                    <h2 class="checkout-card-title">Upload Bukti Pembayaran</h2>
                    <label for="bukti_bayar" class="file-input-box">
                        <span id="file-label-text">Upload File</span>
                        <input type="file" id="bukti_bayar" name="bukti_bayar" accept="image/*,.pdf" hidden>
                    </label>
                </section>
            <?php endif; ?>
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

                <button type="submit" class="btn btn-primary btn-block">Bayar Sekarang</button>
            </section>
        </aside>
    </form>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<script src="../../assets/js/main.js"></script>
</body>
</html>