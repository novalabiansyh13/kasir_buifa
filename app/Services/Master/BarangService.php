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
     * Memproses berkas foto produk, mengubah ukuran maks 400x400 px, dan mengonversi ke WebP.
     *
     * @param mixed $filePhoto
     * @param string|null $oldPhoto
     * @return string|null
     * @throws DomainException
     */
    public function processPhoto($filePhoto, ?string $oldPhoto = null): ?string
    {
        if ($filePhoto === null) {
            return null;
        }

        if (method_exists($filePhoto, 'getError')) {
            if ($filePhoto->getError() === UPLOAD_ERR_NO_FILE) {
                return null;
            }
            if ($filePhoto->getError() !== UPLOAD_ERR_OK) {
                throw new DomainException('Terjadi kesalahan saat mengunggah foto produk.');
            }
        }

        if (method_exists($filePhoto, 'hasMoved') && $filePhoto->hasMoved()) {
            return null;
        }

        $validMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $mime = $filePhoto->getMimeType();
        if (!in_array($mime, $validMimes, true)) {
            throw new DomainException('Format foto harus berupa JPG, PNG, atau WebP.');
        }

        if ($filePhoto->getSize() > 2 * 1024 * 1024) {
            throw new DomainException('Ukuran foto produk maksimal 2 MB.');
        }

        $uploadDir = FCPATH . 'uploads/produk/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $tempPath = $filePhoto->getTempName();
        $sourceData = file_get_contents($tempPath);
        if (!$sourceData) {
            throw new DomainException('Gagal membaca berkas foto yang diunggah.');
        }

        $sourceImg = @imagecreatefromstring($sourceData);
        if (!$sourceImg) {
            throw new DomainException('Format berkas gambar tidak valid.');
        }

        $origW = imagesx($sourceImg);
        $origH = imagesy($sourceImg);
        $maxDim = 400;

        if ($origW > $maxDim || $origH > $maxDim) {
            if ($origW >= $origH) {
                $newW = $maxDim;
                $newH = (int) round($origH * ($maxDim / $origW));
            } else {
                $newH = $maxDim;
                $newW = (int) round($origW * ($maxDim / $origH));
            }
        } else {
            $newW = $origW;
            $newH = $origH;
        }

        $targetImg = imagecreatetruecolor($newW, $newH);
        imagealphablending($targetImg, false);
        imagesavealpha($targetImg, true);

        imagecopyresampled($targetImg, $sourceImg, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

        $newFileName = 'prod_' . uniqid() . '_' . bin2hex(random_bytes(4)) . '.webp';
        $destination = $uploadDir . $newFileName;

        if (!imagewebp($targetImg, $destination, 80)) {
            imagedestroy($sourceImg);
            imagedestroy($targetImg);
            throw new DomainException('Gagal mengompresi dan menyimpan foto produk ke format WebP.');
        }

        imagedestroy($sourceImg);
        imagedestroy($targetImg);

        if (!empty($oldPhoto)) {
            $oldPath = $uploadDir . $oldPhoto;
            if (file_exists($oldPath) && is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        return $newFileName;
    }

    /**
     * Menyimpan produk baru dengan margin terhitung.
     *
     * @param array $cleanData
     * @param mixed $filePhoto
     * @return int
     * @throws DomainException|Exception
     */
    public function store(array $cleanData, $filePhoto = null): int
    {
        $namaBarang = trim($cleanData['nama_barang'] ?? '');
        $hargaBeli = (float) ($cleanData['harga_beli'] ?? 0);
        $hargaJual = (float) ($cleanData['harga_jual'] ?? 0);
        $categoryId = (int) ($cleanData['categoryid'] ?? 0);
        $satuan = trim($cleanData['satuan'] ?? '');
        if (empty($satuan)) {
            $satuan = 'pcs';
        }

        if (empty($namaBarang) || $hargaBeli <= 0 || $hargaJual <= 0) {
            throw new DomainException('Nama barang dan harga wajib diisi.');
        }

        if ($categoryId <= 0) {
            throw new DomainException('Silakan pilih kategori produk.');
        }

        $foto = $this->processPhoto($filePhoto);
        $margin = $hargaJual - $hargaBeli;

        $insertData = [
            'categoryid'  => $categoryId,
            'nama_barang' => $namaBarang,
            'harga_beli'  => $hargaBeli,
            'harga_jual'  => $hargaJual,
            'margin'      => $margin,
            'satuan'      => $satuan,
        ];
        if (!empty($foto)) {
            $insertData['foto'] = $foto;
        }

        $db = $this->getDb();
        $db->transBegin();
        try {
            $this->getBarangModel()->store($insertData);

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
     * @param mixed $filePhoto
     * @return bool
     * @throws DomainException|Exception
     */
    public function update(int $id, array $cleanData, $filePhoto = null): bool
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

        $barangLama = $this->getOne($id);
        $oldPhoto = $barangLama['foto'] ?? null;
        $foto = $this->processPhoto($filePhoto, $oldPhoto);

        $satuan = trim($cleanData['satuan'] ?? '');
        if (empty($satuan)) {
            $satuan = $barangLama['satuan'] ?? 'pcs';
        }

        $margin = $hargaJual - $hargaBeli;

        $updateData = [
            'categoryid'  => $categoryId,
            'nama_barang' => $namaBarang,
            'harga_beli'  => $hargaBeli,
            'harga_jual'  => $hargaJual,
            'margin'      => $margin,
            'satuan'      => $satuan,
        ];
        if (!empty($foto)) {
            $updateData['foto'] = $foto;
        }

        $db = $this->getDb();
        $db->transBegin();
        try {
            $this->getBarangModel()->edit($updateData, $id);

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

        $barangLama = $this->getOne($id);

        $db = $this->getDb();
        $db->transBegin();
        try {
            $this->getBarangModel()->destroy($id);
            $db->transCommit();

            if (!empty($barangLama['foto'])) {
                $photoPath = FCPATH . 'uploads/produk/' . $barangLama['foto'];
                if (file_exists($photoPath) && is_file($photoPath)) {
                    @unlink($photoPath);
                }
            }

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
