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
                                    <svg viewBox="0 0 120 120" width="128" height="128" fill="#0f172a">
                                        <path d="M10 10h36v36H10V10zm6 6v24h24V16H16zm6 6h12v12H22V22zm52-12h36v36H74V10zm6 6v24h24V16H80zm6 6h12v12H86V22zM10 74h36v36H10V74zm6 6v24h24V80H16zm6 6h12v12H22V86zm32-76h8v16h-8V10zm12 8h6v14h-6V18zm-12 20h18v8H54v-8zm-44 20h16v8H10v-8zm24 0h32v8H34v-8zm40 0h12v8H74v-8zm20 0h16v8H94v-8zM54 74h12v12H54V74zm20 0h8v20h-8V74zm16 0h20v8H90v-8zm-36 20h16v16H54V94zm24 8h16v8H78v-8zm20-16h12v24H98V86z"/>
                                    </svg>
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

<script src="../../assets/js/main.js?v=<?= e(asset_version('assets/js/main.js')) ?>"></script>
</body>
</html>