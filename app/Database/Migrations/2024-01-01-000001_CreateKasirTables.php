<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKasirTables extends Migration
{
    public function up(): void
    {
        // Pakai raw SQL agar kompatibel 100% dengan PostgreSQL
        $this->db->query("
            CREATE TABLE IF NOT EXISTS barang (
                id_barang   SERIAL PRIMARY KEY,
                nama_barang VARCHAR(100) NOT NULL,
                harga_beli  NUMERIC(12, 2) NOT NULL,
                harga_jual  NUMERIC(12, 2) NOT NULL,
                margin      NUMERIC(12, 2),
                created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS transaksi (
                id_transaksi       SERIAL PRIMARY KEY,
                tanggal_transaksi  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                total_bayar        NUMERIC(12, 2) DEFAULT 0,
                total_margin       NUMERIC(12, 2) DEFAULT 0
            )
        ");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS detail_transaksi (
                id_detail          SERIAL PRIMARY KEY,
                id_transaksi       INT REFERENCES transaksi(id_transaksi) ON DELETE CASCADE,
                id_barang          INT REFERENCES barang(id_barang),
                jumlah             INT NOT NULL CHECK (jumlah > 0),
                harga_jual_satuan  NUMERIC(12, 2) NOT NULL,
                margin_satuan      NUMERIC(12, 2) NOT NULL,
                subtotal_harga     NUMERIC(12, 2),
                subtotal_margin    NUMERIC(12, 2)
            )
        ");
    }

    public function down(): void
    {
        $this->db->query('DROP TABLE IF EXISTS detail_transaksi');
        $this->db->query('DROP TABLE IF EXISTS transaksi');
        $this->db->query('DROP TABLE IF EXISTS barang');
    }
}
