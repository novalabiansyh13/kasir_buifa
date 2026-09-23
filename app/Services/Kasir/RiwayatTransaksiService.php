<?php

namespace App\Services\Kasir;

use App\Models\TransaksiModel;

class RiwayatTransaksiService
{
    protected TransaksiModel $transaksiModel;
    protected $db;

    public function __construct(?TransaksiModel $transaksiModel = null, $db = null)
    {
        $this->transaksiModel = $transaksiModel ?? new TransaksiModel();
        $this->db = $db;
    }

    protected function getDb()
    {
        if ($this->db === null) {
            $this->db = db_connect();
        }
        return $this->db;
    }

    /**
     * Membersihkan dan memvalidasi rentang tanggal.
     * Jika format tidak valid atau kosong, gunakan tanggal hari ini.
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array [startDate, endDate]
     */
    public function sanitizeDateRange(?string $startDate, ?string $endDate): array
    {
        $start = !empty($startDate) && strtotime($startDate) ? date('Y-m-d', strtotime($startDate)) : date('Y-m-d');
        $end = !empty($endDate) && strtotime($endDate) ? date('Y-m-d', strtotime($endDate)) : date('Y-m-d');

        if ($start > $end) {
            $temp = $start;
            $start = $end;
            $end = $temp;
        }

        return [$start, $end];
    }

    /**
     * Mengambil data metrik ringkasan omzet, margin, dan jumlah transaksi.
     *
     * @param string $startDate
     * @param string $endDate
     * @return array ['total_penjualan' => float, 'total_margin' => float, 'total_transaksi' => int]
     */
    public function getRingkasan(string $startDate, string $endDate): array
    {
        [$start, $end] = $this->sanitizeDateRange($startDate, $endDate);
        $data = $this->transaksiModel->getRingkasan($start, $end);
        $topProducts = $this->transaksiModel->getTopProducts($start, $end, 3);

        return [
            'total_penjualan' => (float) ($data['total_penjualan'] ?? 0),
            'total_margin'    => (float) ($data['total_margin'] ?? 0),
            'total_transaksi' => (int) ($data['total_transaksi'] ?? 0),
            'top_products'    => $topProducts,
        ];
    }

    /**
     * Mengambil detail satu transaksi beserta baris barang yang dibeli.
     *
     * @param int $idTransaksi
     * @return array|null
     */
    public function getDetailTransaksi(int $idTransaksi): ?array
    {
        if ($idTransaksi <= 0) {
            return null;
        }

        $transaksi = $this->transaksiModel->getOne($idTransaksi);
        if (!$transaksi) {
            return null;
        }

        $items = $this->getDb()->table('detail_transaksi dt')
            ->select('dt.*, b.nama_barang')
            ->join('barang b', 'b.id_barang = dt.id_barang', 'left')
            ->where('dt.id_transaksi', $idTransaksi)
            ->get()
            ->getResultArray();

        return [
            'transaksi' => $transaksi,
            'items'     => $items,
        ];
    }
}
