-- Tabel transaksi gateway pembayaran (Midtrans).
--
-- CATATAN: tabel ini SUDAH ADA di Supabase project EventIn dengan struktur
-- di bawah. File ini hanya dokumentasi + jaring pengaman kalau project
-- perlu dibuat ulang. Jalankan manual di Supabase SQL Editor; aplikasi
-- tidak pernah membuat tabel sendiri.

create table if not exists public.pembayaran_gateway (
    id_pembayaran_gateway serial primary key,
    id_tiket              integer       not null
                          references public.tiket(id_tiket) on delete cascade,
    order_id              varchar(50)   not null,
    gross_amount          numeric       not null,
    transaction_status    varchar(30)   not null default 'pending',
    raw_response          jsonb,
    created_at            timestamp     not null default current_timestamp,
    paid_at               timestamp
);

create unique index if not exists pembayaran_gateway_order_id_key
    on public.pembayaran_gateway (order_id);

create index if not exists pembayaran_gateway_id_tiket_idx
    on public.pembayaran_gateway (id_tiket);

alter table public.pembayaran_gateway enable row level security;

-- Tanpa policy: akses hanya lewat koneksi database server (pooler),
-- sama seperti sembilan tabel lain di project ini.
