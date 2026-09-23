<?php

namespace App\Services\Kasir;

use App\Models\BarangModel;
use App\Models\DetailTransaksiModel;
use App\Models\TransaksiModel;
use DomainException;
use Exception;

class KasirService
{
    protected ?BarangModel $barang = null;
    protected ?TransaksiModel $transaksi = null;
    protected ?DetailTransaksiModel $detail = null;
    protected $db = null;

    public function __construct(
        ?BarangModel $barang = null,
        ?TransaksiModel $transaksi = null,
        ?DetailTransaksiModel $detail = null,
        $db = null
    ) {
        $this->barang = $barang;
        $this->transaksi = $transaksi;
        $this->detail = $detail;
        $this->db = $db;
    }

    protected function getBarangModel(): BarangModel
    {
        if ($this->barang === null) {
            $this->barang = new BarangModel();
        }
        return $this->barang;
    }

    protected function getTransaksiModel(): TransaksiModel
    {
        if ($this->transaksi === null) {
            $this->transaksi = new TransaksiModel();
        }
        return $this->transaksi;
    }

    protected function getDetailModel(): DetailTransaksiModel
    {
        if ($this->detail === null) {
            $this->detail = new DetailTransaksiModel();
        }
        return $this->detail;
    }

    protected function getDb()
    {
        if ($this->db === null) {
            $this->db = db_connect();
        }
        return $this->db;
    }

    /**
     * Memproses keranjang belanja dan menyimpan data transaksi beserta detailnya.
     *
     * @param array $items
     * @return array ['id_transaksi' => int, 'total_bayar' => float, 'total_margin' => float]
     * @throws DomainException|Exception
     */
    public function simpanTransaksi(array $items): array
    {
        if (empty($items)) {
            throw new DomainException('Keranjang belanja masih kosong.');
        }

        $totalBayar = 0;
        $totalMargin = 0;
        $detailRows = [];
        $barangModel = $this->getBarangModel();

        foreach ($items as $item) {
            $idItem = !empty($item['id_barang']) ? $item['id_barang'] : ($item['id'] ?? null);
            if (empty($idItem)) {
                continue;
            }
            $rawId = is_numeric($idItem) ? (int) $idItem : decrypting($idItem);
            $barang = $barangModel->getOne($rawId);
            if (empty($barang)) {
                continue;
            }

            $jumlah = max(1, (int) ($item['jumlah'] ?? $item['qty'] ?? 1));
            $hargaJual = (float) $barang['harga_jual'];
            $marginSatuan = (float) $barang['margin'];

            $subtotalHarga = $hargaJual * $jumlah;
            $subtotalMargin = $marginSatuan * $jumlah;

            $totalBayar += $subtotalHarga;
            $totalMargin += $subtotalMargin;

            $detailRows[] = [
                'id_barang'         => (int) $barang['id_barang'],
                'jumlah'            => $jumlah,
                'harga_jual_satuan' => $hargaJual,
                'margin_satuan'     => $marginSatuan,
                'subtotal_harga'    => $subtotalHarga,
                'subtotal_margin'   => $subtotalMargin,
            ];
        }

        if (empty($detailRows)) {
            throw new DomainException('Tidak ada produk valid yang ditemukan di keranjang.');
        }

        $db = $this->getDb();
        $db->transBegin();
        try {
            $this->getTransaksiModel()->store([
                'total_bayar'  => $totalBayar,
                'total_margin' => $totalMargin,
            ]);

            $idTransaksi = (int) $db->insertID();

            foreach ($detailRows as &$row) {
                $row['id_transaksi'] = $idTransaksi;
            }
            unset($row);

            $this->getDetailModel()->storeBatch($detailRows);
            $db->transCommit();

            return [
                'id_transaksi' => $idTransaksi,
                'total_bayar'  => $totalBayar,
                'total_margin' => $totalMargin,
            ];
        } catch (Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }
}
