<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFotoDanSatuanToBarang extends Migration
{
    public function up(): void
    {
        $this->db->query("
            ALTER TABLE barang 
            ADD COLUMN IF NOT EXISTS foto VARCHAR(255) DEFAULT NULL,
            ADD COLUMN IF NOT EXISTS satuan VARCHAR(20) DEFAULT 'pcs' NOT NULL
        ");
    }

    public function down(): void
    {
        $this->db->query("
            ALTER TABLE barang 
            DROP COLUMN IF EXISTS foto,
            DROP COLUMN IF EXISTS satuan
        ");
    }
}
