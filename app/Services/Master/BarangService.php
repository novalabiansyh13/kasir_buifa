<?php

namespace App\Services\Master;

use App\Models\BarangModel;
use DomainException;
use Exception;

class BarangService
{
    protected ?BarangModel $barang = null;
    protected $db = null;

    public function __construct(?BarangModel $barang = null, $db = null)
    {
        $this->barang = $barang;
        $this->db = $db;
    }

    protected function getBarangModel(): BarangModel
    {
        if ($this->barang === null) {
            $this->barang = new BarangModel();
        }
        return $this->barang;
    }

    protected function getDb()
    {
        if ($this->db === null) {
            $this->db = db_connect();
        }
        return $this->db;
    }

    /**
     * Menyimpan produk baru dengan margin terhitung.
     *
     * @param array $cleanData
     * @return int
     * @throws DomainException|Exception
     */
    public function store(array $cleanData): int
    {
        $namaBarang = trim($cleanData['nama_barang'] ?? '');
        $hargaBeli = (float) ($cleanData['harga_beli'] ?? 0);
        $hargaJual = (float) ($cleanData['harga_jual'] ?? 0);
        $categoryId = (int) ($cleanData['categoryid'] ?? 0);

        if (empty($namaBarang) || $hargaBeli <= 0 || $hargaJual <= 0) {
            throw new DomainException('Nama barang dan harga wajib diisi.');
        }

        if ($categoryId <= 0) {
            throw new DomainException('Silakan pilih kategori produk.');
        }

        $margin = $hargaJual - $hargaBeli;

        $db = $this->getDb();
        $db->transBegin();
        try {
            $this->getBarangModel()->store([
                'categoryid'  => $categoryId,
                'nama_barang' => $namaBarang,
                'harga_beli'  => $hargaBeli,
                'harga_jual'  => $hargaJual,
                'margin'      => $margin,
            ]);

            $newId = (int) $db->insertID();
            $db->transCommit();
            return $newId;
        } catch (Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Memperbarui data produk.
     *
     * @param int $id
     * @param array $cleanData
     * @return bool
     * @throws DomainException|Exception
     */
    public function update(int $id, array $cleanData): bool
    {
        $namaBarang = trim($cleanData['nama_barang'] ?? '');
        $hargaBeli = (float) ($cleanData['harga_beli'] ?? 0);
        $hargaJual = (float) ($cleanData['harga_jual'] ?? 0);
        $categoryId = (int) ($cleanData['categoryid'] ?? 0);

        if ($id <= 0 || empty($namaBarang) || $hargaBeli <= 0 || $hargaJual <= 0) {
            throw new DomainException('Data barang belum lengkap.');
        }

        if ($categoryId <= 0) {
            throw new DomainException('Silakan pilih kategori produk.');
        }

        $margin = $hargaJual - $hargaBeli;

        $db = $this->getDb();
        $db->transBegin();
        try {
            $this->getBarangModel()->edit([
                'categoryid'  => $categoryId,
                'nama_barang' => $namaBarang,
                'harga_beli'  => $hargaBeli,
                'harga_jual'  => $hargaJual,
                'margin'      => $margin,
            ], $id);

            $db->transCommit();
            return true;
        } catch (Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Menghapus produk setelah memeriksa dependensi transaksi.
     *
     * @param int $id
     * @return bool
     * @throws DomainException|Exception
     */
    public function delete(int $id): bool
    {
        if ($id <= 0) {
            throw new DomainException('ID barang tidak valid.');
        }

        $tables = [
            ['table' => 'detail_transaksi', 'column' => 'id_barang', 'value' => $id, 'alias' => 'Transaksi'],
        ];
        $getvalidate = validateDeleteData($tables);
        if (!empty($getvalidate)) {
            $aliases = array_unique(array_column($getvalidate, 'alias'));
            $msg = '<div>Data sedang digunakan di :</div>';
            $msg .= "<ul style='margin: 0; padding-left: 20px;'>";
            foreach ($aliases as $alias) {
                $msg .= '<li>' . $alias . '</li>';
            }
            $msg .= '</ul>';
            throw new DomainException($msg);
        }

        $db = $this->getDb();
        $db->transBegin();
        try {
            $this->getBarangModel()->destroy($id);
            $db->transCommit();
            return true;
        } catch (Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Mengambil satu data produk berdasarkan ID.
     */
    public function getOne(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }
        return $this->getBarangModel()->getOne($id);
    }

    /**
     * Mengambil daftar opsi select produk.
     */
    public function getSelectOptions(?string $search = '', ?int $categoryId = null): array
    {
        return $this->getBarangModel()->getSelect($search ?? '', $categoryId ? (string) $categoryId : '');
    }
}
