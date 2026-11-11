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

Di Render, isi variabel yang sama lewat dashboard. `render.yaml` sudah
mendaftarkan nama variabelnya.

## Supabase Storage

Bucket `image_event` harus ada dan publik untuk membaca file. Untuk upload
dengan service role key, RLS Storage dilewati. Kalau memakai anon/publishable
key, tambahkan policy `INSERT` pada `storage.objects` untuk bucket tersebut.

## Catatan database

Tabel `tiket` dan `pembayaran` harus punya default identity/serial pada
`id_tiket` dan `id_pembayaran`. Status pembayaran disimpan di
`tiket.status_bayar`, bukan di tabel `pembayaran`.