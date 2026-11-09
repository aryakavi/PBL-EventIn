<?php
// Peta ikon SVG EventIn. Dipakai lewat helper icon('nama').
// Ikon disimpan sebagai isi <svg> (path/shape), dibungkus otomatis oleh icon().

$ikon = [
    // Umum
    'user'          => '<circle cx="12" cy="8" r="4"></circle><path d="M5 21a7 7 0 0 1 14 0"></path>',
    'card'          => '<rect x="3" y="5" width="18" height="14" rx="2"></rect><circle cx="8" cy="11" r="2"></circle><path d="M5.5 16a2.5 2.5 0 0 1 5 0M14 10h4M14 14h4"></path>',
    'email'         => '<rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 6 9-6"></path>',
    'phone'         => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.4 2.1L8.1 10a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.6 1.9Z"></path>',
    'lock'          => '<circle cx="8" cy="15" r="4"></circle><path d="m11 12 9-9M17 6l2 2M14 9l2 2"></path>',
    'eye'           => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path><circle cx="12" cy="12" r="3"></circle>',
    'arrow-right'   => '<path d="M5 12h14M13 6l6 6-6 6"></path>',
    'building'      => '<path d="M3 21h18M5 21V5a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v16"></path><rect x="8" y="8" width="3" height="3" rx=".5"></rect><rect x="13" y="8" width="3" height="3" rx=".5"></rect><rect x="8" y="14" width="3" height="3" rx=".5"></rect><rect x="13" y="14" width="3" height="3" rx=".5"></rect>',
    'calendar'      => '<rect x="3" y="4" width="18" height="17" rx="2"></rect><path d="M16 2v4M8 2v4M3 10h18"></path>',
    'stat-users'    => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path>',
    'note'          => '<rect x="4" y="3" width="16" height="18" rx="2"></rect><path d="M9 8h1M14 8h1M9 12h1M14 12h1M9 16h1M14 16h1"></path>',
    'search'        => '<circle cx="11" cy="11" r="7"></circle><path d="m21 21-4.3-4.3"></path>',
    'pin'           => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle>',
    'refresh'       => '<path d="M21 12a9 9 0 1 1-3-6.7L21 8"></path><path d="M21 3v5h-5"></path>',
    'clipboard'     => '<rect x="9" y="9" width="11" height="11" rx="2"></rect><path d="M5 15V5a2 2 0 0 1 2-2h10"></path>',
    'check'         => '<path d="m5 12 4 4 10-10"></path>',
    'download'      => '<path d="M12 3v12"></path><path d="m7 10 5 5 5-5"></path><path d="M5 21h14"></path>',
    'map'           => '<path d="m9 4-6 2v14l6-2 6 2 6-2V4l-6 2-6-2Z"></path><path d="M9 4v14M15 6v14"></path>',
    'seat'          => '<path d="M6 19V6a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13"></path><path d="M4 19h16M9 8h6M9 12h6"></path>',
    'shield-check'  => '<path d="m9 12 2 2 4-4"></path><path d="M12 3a9 9 0 1 0 9 9"></path>',
    'chevron-up'    => '<path d="m6 15 6-6 6 6"></path>',
    'chevron-down'  => '<path d="m6 9 6 6 6-6"></path>',
    'times'         => '<path d="M18 6 6 18M6 6l12 12"></path>',
    'clock'         => '<circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path>',
    // Konfirmasi pembayaran
    'chat'          => '<path d="M21 12a8 8 0 0 1-11.5 7.2L4 21l1.8-5.5A8 8 0 1 1 21 12Z"></path>',
    'ticket'        => '<path d="M3 8a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2a2 2 0 0 0 0-4Z"></path><path d="M14 6v12"></path>',
    'shield'        => '<path d="M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6Z"></path><path d="m9 12 2 2 4-4"></path>',
    'headset'       => '<path d="M4 14v-2a8 8 0 0 1 16 0v2"></path><rect x="2" y="14" width="4" height="6" rx="1"></rect><rect x="18" y="14" width="4" height="6" rx="1"></rect><path d="M20 20a3 3 0 0 1-3 2h-3"></path>',
    // Admin
    'edit'          => '<path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"></path>',
    'eye-open'      => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path><circle cx="12" cy="12" r="3"></circle>',
    'trash'         => '<path d="M3 6h18"></path><path d="M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path><path d="M10 11v6M14 11v6"></path>',
    'file'          => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"></path><path d="M14 2v6h6"></path>',
    'menu'          => '<path d="M3 6h18M3 12h18M3 18h18"></path>',
];

// Ikon QR (atribut khusus, disimpan utuh)
$ikonQr = '<svg viewBox="0 0 120 120" width="136" height="136" fill="#0f172a" aria-label="QR Code Tiket">'
    . '<path d="M10 10h34v34H10V10zm6 6v22h22V16H16zm6 6h10v10H22V22zm54-12h34v34H76V10zm6 6v22h22V16H82zm6 6h10v10H88V22zM10 76h34v34H10V76zm6 6v22h22V82H16zm6 6h10v10H22V88zm30-78h8v14h-8V10zm12 10h6v14h-6V20zM52 44h16v8H52v-8zM16 54h16v8H16v-8zm22 0h12v8H38v-8zm38 0h14v8H76v-8zm20 0h14v8H96v-8zM52 76h12v12H52V76zm22 0h8v18h-8V76zm14 0h22v8H88v-8zM52 96h16v14H52V96zm22 6h16v8H74v-8zm22-12h14v20H96V90z"/>'
    . '<rect x="54" y="54" width="12" height="12" rx="2" fill="#f97316"/></svg>';

// Ikon QR kecil (bayar) tanpa aria-label khusus
$ikonQrKecil = '<svg viewBox="0 0 120 120" width="128" height="128" fill="#0f172a">'
    . '<path d="M10 10h36v36H10V10zm6 6v24h24V16H16zm6 6h12v12H22V22zm52-12h36v36H74V10zm6 6v24h24V16H80zm6 6h12v12H86V22zM10 74h36v36H10V74zm6 6v24h24V80H16zm6 6h12v12H22V86zm32-76h8v16h-8V10zm12 8h6v14h-6V18zm-12 20h18v8H54v-8zm-44 20h16v8H10v-8zm24 0h32v8H34v-8zm40 0h12v8H74v-8zm20 0h16v8H94v-8zM54 74h12v12H54V74zm20 0h8v20h-8V74zm16 0h20v8H90v-8zm-36 20h16v16H54V94zm24 8h16v8H78v-8zm20-16h12v24H98V86z"/></svg>';

if (!function_exists('icon')) {
    /** Bungkus path ikon dengan <svg> standar. */
    function icon(string $nama, string $kelas = ''): string
    {
        global $ikon;
        $path = $ikon[$nama] ?? '';
        $attr = $kelas !== '' ? ' class="' . e($kelas) . '"' : '';
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"' . $attr . '>' . $path . '</svg>';
    }
}
