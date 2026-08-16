<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiModel extends Model
{
    protected $table         = 'transaksi';
    protected $primaryKey    = 'id_transaksi';
    protected $useAutoIncrement = true;
    protected $returnType    = 'array';

    protected $allowedFields = [
        'tanggal_transaksi',
        'total_bayar',
        'total_margin',
    ];

    protected $useTimestamps = false;

    /**
     * Ambil semua transaksi hari ini beserta detail barang-nya.
     */
    public function getHariIni(): array
    {
        $db = \Config\Database::connect();

        return $db->query(
            "SELECT
                t.id_transaksi,
                TO_CHAR(t.tanggal_transaksi, 'HH24:MI') AS jam,
                t.total_bayar,
                t.total_margin,
                COALESCE(
                    STRING_AGG(b.nama_barang || ' x' || dt.jumlah, ', '),
                    '-'
                ) AS detail_barang
             FROM transaksi t
             LEFT JOIN detail_transaksi dt ON dt.id_transaksi = t.id_transaksi
             LEFT JOIN barang b            ON b.id_barang     = dt.id_barang
             WHERE DATE(t.tanggal_transaksi) = CURRENT_DATE
             GROUP BY t.id_transaksi, t.tanggal_transaksi, t.total_bayar, t.total_margin
             ORDER BY t.tanggal_transaksi DESC"
        )->getResultArray();
    }

    /**
     * Ringkasan akumulasi hari ini.
     */
    public function getRingkasanHariIni(): array
    {
        $db = \Config\Database::connect();

        $row = $db->query(
            "SELECT
                COALESCE(SUM(total_bayar),  0) AS total_penjualan,
                COALESCE(SUM(total_margin), 0) AS total_margin
             FROM transaksi
             WHERE DATE(tanggal_transaksi) = CURRENT_DATE"
        )->getRowArray();

        return $row ?? ['total_penjualan' => 0, 'total_margin' => 0];
    }
}
