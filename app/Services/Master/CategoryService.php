<?php

namespace App\Services\Master;

use App\Models\CategoryModel;
use DomainException;
use Exception;

class CategoryService
{
    protected ?CategoryModel $category = null;
    protected $db = null;

    public function __construct(?CategoryModel $category = null, $db = null)
    {
        $this->category = $category;
        $this->db = $db;
    }

    protected function getCategoryModel(): CategoryModel
    {
        if ($this->category === null) {
            $this->category = new CategoryModel();
        }
        return $this->category;
    }

    protected function getDb()
    {
        if ($this->db === null) {
            $this->db = db_connect();
        }
        return $this->db;
    }

    /**
     * Menyimpan kategori produk baru.
     *
     * @param array $cleanData ['categoryname' => string]
     * @return int
     * @throws DomainException|Exception
     */
    public function store(array $cleanData): int
    {
        $name = trim($cleanData['categoryname'] ?? '');
        if (empty($name)) {
            throw new DomainException('Nama kategori tidak boleh kosong.');
        }

        $db = $this->getDb();
        $db->transBegin();
        try {
            $this->getCategoryModel()->store(['categoryname' => $name]);
            $newId = (int) $db->insertID();
            $db->transCommit();
            return $newId;
        } catch (Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Memperbarui nama kategori produk.
     *
     * @param int $id
     * @param array $cleanData ['categoryname' => string]
     * @return bool
     * @throws DomainException|Exception
     */
    public function update(int $id, array $cleanData): bool
    {
        $name = trim($cleanData['categoryname'] ?? '');
        if ($id <= 0 || empty($name)) {
            throw new DomainException('Data kategori belum lengkap.');
        }

        $db = $this->getDb();
        $db->transBegin();
        try {
            $this->getCategoryModel()->edit(['categoryname' => $name], $id);
            $db->transCommit();
            return true;
        } catch (Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Menghapus kategori setelah memeriksa dependensi barang.
     *
     * @param int $id
     * @return bool
     * @throws DomainException|Exception
     */
    public function delete(int $id): bool
    {
        if ($id <= 0) {
            throw new DomainException('ID kategori tidak valid.');
        }

        $tables = [
            ['table' => 'barang', 'column' => 'categoryid', 'value' => $id, 'alias' => 'Produk / Barang'],
        ];
        $getvalidate = validateDeleteData($tables);
        if (!empty($getvalidate)) {
            $aliases = array_unique(array_column($getvalidate, 'alias'));
            $msg = '<div>Kategori tidak dapat dihapus karena masih digunakan di:</div>';
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
            $this->getCategoryModel()->destroy($id);
            $db->transCommit();
            return true;
        } catch (Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Mengambil satu data kategori berdasarkan ID.
     */
    public function getOne(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }
        return $this->getCategoryModel()->getOne($id);
    }

    /**
     * Mengambil seluruh data kategori.
     */
    public function getAll(): array
    {
        return $this->getCategoryModel()->getAll();
    }

    /**
     * Mengambil daftar opsi select kategori.
     */
    public function getSelectOptions(?string $search = ''): array
    {
        return $this->getCategoryModel()->getSelect($search ?? '');
    }
}
