# PBL-EventIn

Github repo for PBL EventIn

## Alur pembayaran

1. Peserta klik **Daftar Sekarang** di halaman detail event.
2. Sistem membuat satu baris di tabel `tiket` dengan status `Belum Bayar`
   (event gratis langsung `Lunas`).
3. Peserta diarahkan ke `app/views/pembayaran.php?id_tiket=<id>`.
4. Bukti pembayaran diunggah ke Supabase Storage bucket `image_event`,
   folder `bukti_pembayaran/`.
5. Sistem insert baris `pembayaran` dan mengubah status tiket menjadi `Menunggu`.
6. Admin membuka `app/views/admin/pembayaran.php`:
   - tab **Menunggu** untuk transaksi yang belum diverifikasi,
   - tab **Belum Upload** untuk tiket berbayar tanpa bukti,
   - tombol **Lunas**, **Gagal**, dan **Kembalikan ke Menunggu** untuk
     membuat skenario status kapan saja.

## Konfigurasi

Salin `.env.example` menjadi `.env` lalu isi:

- `DB_*` — kredensial database Supabase (connection pooler).
- `SUPABASE_URL` — `https://<project-ref>.supabase.co`.
- `SUPABASE_SERVICE_ROLE_KEY` — service role key, hanya di server.
- `SUPABASE_BUCKET` — default `image_event`.
- `SUPABASE_BUKTI_FOLDER` — default `bukti_pembayaran`.
- `MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`, `MIDTRANS_IS_PRODUCTION`,
  `MIDTRANS_FINISH_URL` — untuk pembayaran online, lihat bagian di bawah.

Di Render, isi variabel yang sama lewat dashboard. `render.yaml` sudah
mendaftarkan nama variabelnya.

## Supabase Storage

Upload ditangani kelas `Storage` di `app/models/Storage.php`. Kelas ini
memvalidasi tipe file (JPG, PNG, WEBP, PDF), batas ukuran, dan memakai
timeout 45 detik. Kredensial selalu dibaca dari `app_config()`, tidak ditulis
di dalam kelas. View memakai pembungkus `storage_upload_bukti()` dan
`storage_public_url()` di `includes/storage.php`.

Bucket `image_event` harus ada dan publik untuk membaca file. Untuk upload
dengan service role key, RLS Storage dilewati. Kalau memakai anon/publishable
key, tambahkan policy `INSERT` pada `storage.objects` untuk bucket tersebut.

## Pembayaran online (Midtrans Snap)

Selain transfer manual, peserta bisa membayar lewat Midtrans. Semua metode
(VA, e-wallet, QRIS, kartu) ditampilkan oleh Snap, jadi menambah metode cukup
mengaktifkannya di dashboard Midtrans tanpa mengubah kode.

Alur:

1. Peserta menekan **Bayar Online (Midtrans)** di `app/views/pembayaran.php`.
2. `MidtransController::start()` membuat transaksi Snap dan menyimpan baris
   `pembayaran_gateway` dengan status `pending`.
3. Halaman memuat `snap.js` dan membuka popup pembayaran.
4. Midtrans mengirim notifikasi ke `midtrans-notification.php`.
5. Notifikasi diverifikasi dengan `SHA512(order_id + status_code +
   gross_amount + ServerKey)`, nominal dicocokkan dengan `tiket.nominal`,
   lalu `tiket.status_bayar` diperbarui.
6. Peserta kembali ke `midtrans-finish.php`, yang mengecek ulang status ke
   API Midtrans sebelum mengarahkan ke halaman pembayaran.

Konfigurasi tambahan di `.env`:

- `MIDTRANS_SERVER_KEY` — server key, hanya di server. Tanpa ini tombol
  pembayaran online disembunyikan.
- `MIDTRANS_CLIENT_KEY` — client key, boleh tampil di browser.
- `MIDTRANS_IS_PRODUCTION` — `false` untuk sandbox.
- `MIDTRANS_FINISH_URL` — opsional. Kosongkan untuk memakai
  `https://<host>/midtrans-finish.php`.

Set **Payment Notification URL** di Midtrans Dashboard > Settings >
Configuration ke `https://<domain>/midtrans-notification.php`. URL harus
publik dan HTTPS; localhost tidak bisa, gunakan ngrok atau cloudflared.

Struktur tabel `pembayaran_gateway` ada di `database/midtrans.sql`.

## Catatan database

Tabel `tiket` dan `pembayaran` harus punya default identity/serial pada
`id_tiket` dan `id_pembayaran`. Status pembayaran disimpan di
`tiket.status_bayar`, bukan di tabel `pembayaran`.