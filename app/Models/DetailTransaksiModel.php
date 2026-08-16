<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailTransaksiModel extends Model
{
    protected $table = 'detail_transaksi as a';

    protected $primaryKey = 'id_detail';

    protected $allowedFields = [
        'id_transaksi',
        'id_barang',
        'jumlah',
        'harga_jual_satuan',
        'margin_satuan',
        'subtotal_harga',
        'subtotal_margin',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->db = db_connect();
        $this->builder = $this->db->table($this->table);
    }

    public function storeBatch($rows)
    {
        return $this->builder->insertBatch($rows);
    }
}
