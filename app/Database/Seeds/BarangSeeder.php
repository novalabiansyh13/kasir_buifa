<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seed data awal: contoh barang sembako.
 * Jalankan dengan: php spark db:seed BarangSeeder
 */
class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $barang = [
            ['nama_barang' => 'Beras 5 kg',       'harga_beli' =>  65000, 'harga_jual' =>  72000],
            ['nama_barang' => 'Gula Pasir 1 kg',   'harga_beli' =>  14500, 'harga_jual' =>  17000],
            ['nama_barang' => 'Minyak Goreng 1 L', 'harga_beli' =>  14000, 'harga_jual' =>  16500],
            ['nama_barang' => 'Tepung Terigu 1 kg','harga_beli' =>   9000, 'harga_jual' =>  11000],
            ['nama_barang' => 'Mie Instan Goreng', 'harga_beli' =>   2700, 'harga_jual' =>   3500],
            ['nama_barang' => 'Kecap Manis 135 ml','harga_beli' =>   5500, 'harga_jual' =>   7000],
            ['nama_barang' => 'Garam Halus 250 g', 'harga_beli' =>   2000, 'harga_jual' =>   3000],
            ['nama_barang' => 'Telur Ayam (butir)','harga_beli' =>   2200, 'harga_jual' =>   2800],
            ['nama_barang' => 'Sabun Mandi',        'harga_beli' =>   3000, 'harga_jual' =>   4000],
            ['nama_barang' => 'Sampo Sachet',       'harga_beli' =>    800, 'harga_jual' =>   1500],
        ];

        foreach ($barang as &$b) {
            $b['margin'] = $b['harga_jual'] - $b['harga_beli'];
        }
        unset($b);

        $this->db->table('barang')->insertBatch($barang);
        echo "BarangSeeder: " . count($barang) . " barang berhasil ditambahkan.\n";
    }
}
