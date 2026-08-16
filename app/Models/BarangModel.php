<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $table = 'barang as a';

    protected $primaryKey = 'id_barang';

    protected $allowedFields = [
        'nama_barang',
        'harga_beli',
        'harga_jual',
        'margin',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->db = db_connect();
        $this->builder = $this->db->table($this->table);
    }

    // Kolom yang bisa di-search datatable (null = tidak bisa dicari)
    public function searchable()
    {
        return [
            null,               // No
            'a.nama_barang',    // Nama Barang
            'a.harga_beli',     // Harga Beli
            'a.harga_jual',     // Harga Jual
            'a.margin',         // Margin
            null,               // Action
        ];
    }

    // Query untuk datatable (return BUILDER, bukan hasil)
    public function getBarang()
    {
        return $this->builder->select('a.*');
    }

    // Ambil satu baris
    public function getOne($id = '')
    {
        $x = $this->builder->select('a.*');
        if ($id != '') {
            $x->where('a.id_barang', $id);
        }
        return $x->get()->getRowArray();
    }

    // Hitung margin otomatis sebelum insert/update
    public function hitungMargin(array &$data)
    {
        if (isset($data['harga_jual'], $data['harga_beli'])) {
            $data['margin'] = (float) $data['harga_jual'] - (float) $data['harga_beli'];
        }
    }

    // CRUD
    public function store($data)
    {
        return $this->builder->insert($data);
    }

    public function edit($data, $id)
    {
        return $this->builder->update($data, ['id_barang' => $id]);
    }

    public function destroy($id)
    {
        return $this->builder->delete(['id_barang' => $id]);
    }

    // Search untuk select2 / autocomplete
    public function getSelect($search = '')
    {
        $cari = strtolower($search);
        return $this->builder
            ->select('a.id_barang, a.nama_barang, a.harga_beli, a.harga_jual, a.margin')
            ->where("(lower(a.nama_barang) like '%" . $cari . "%')", null, false)
            ->limit(15)
            ->orderBy('a.nama_barang')
            ->get()
            ->getResultArray();
    }
}
