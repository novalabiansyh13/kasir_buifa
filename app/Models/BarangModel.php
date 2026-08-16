<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $table         = 'barang';
    protected $primaryKey    = 'id_barang';
    protected $useAutoIncrement = true;
    protected $returnType    = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'nama_barang',
        'harga_beli',
        'harga_jual',
        'margin',
    ];

    protected $useTimestamps  = false;  // kolom timestamp dikelola manual oleh DB

    protected $validationRules = [
        'nama_barang' => 'required|min_length[2]|max_length[100]',
        'harga_beli'  => 'required|numeric|greater_than[0]',
        'harga_jual'  => 'required|numeric|greater_than[0]',
    ];

    protected $validationMessages = [
        'nama_barang' => [
            'required'   => 'Nama barang wajib diisi.',
            'min_length' => 'Nama barang minimal 2 karakter.',
            'max_length' => 'Nama barang maksimal 100 karakter.',
        ],
        'harga_beli' => [
            'required'      => 'Harga beli wajib diisi.',
            'numeric'       => 'Harga beli harus berupa angka.',
            'greater_than'  => 'Harga beli harus lebih dari 0.',
        ],
        'harga_jual' => [
            'required'      => 'Harga jual wajib diisi.',
            'numeric'       => 'Harga jual harus berupa angka.',
            'greater_than'  => 'Harga jual harus lebih dari 0.',
        ],
    ];

    /**
     * Hitung dan set margin otomatis sebelum insert/update.
     */
    public function hitungMargin(array &$data): void
    {
        if (isset($data['harga_jual'], $data['harga_beli'])) {
            $data['margin'] = (float) $data['harga_jual'] - (float) $data['harga_beli'];
        }
    }

    /**
     * Kembalikan semua barang diurutkan nama.
     */
    public function getAll(): array
    {
        return $this->orderBy('nama_barang', 'ASC')->findAll();
    }
}
