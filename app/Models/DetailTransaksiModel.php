<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailTransaksiModel extends Model
{
    protected $table         = 'detail_transaksi';
    protected $primaryKey    = 'id_detail';
    protected $useAutoIncrement = true;
    protected $returnType    = 'array';

    protected $allowedFields = [
        'id_transaksi',
        'id_barang',
        'jumlah',
        'harga_jual_satuan',
        'margin_satuan',
        'subtotal_harga',
        'subtotal_margin',
    ];

    protected $useTimestamps = false;
}
