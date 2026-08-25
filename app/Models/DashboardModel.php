<?php

namespace App\Models;
use CodeIgniter\Model;

class DashboardModel extends Model
{
    protected $db;
    public function __construct()
    {
        parent::__construct();
        $this->db = db_connect();
    }

    public function getAvailableYears()
    {
        $rows = $this->db->query(
            "SELECT DISTINCT EXTRACT(YEAR FROM tanggal_transaksi)::int AS tahun
             FROM transaksi
             WHERE tanggal_transaksi IS NOT NULL
             ORDER BY tahun DESC"
        )->getResultArray();
        $years = array_column($rows, 'tahun');
        $currentYear = (int) date('Y');
        if (!in_array($currentYear, $years)) {
            array_unshift($years, $currentYear);
        }
        return $years;
    }

    public function getKpi($tahun)
    {
        $tahun = (int) $tahun;
        $rowRingkasan = $this->db->query(
            "SELECT COALESCE(SUM(total_bayar), 0) AS total_omzet,
                    COALESCE(SUM(total_margin), 0) AS total_margin,
                    COUNT(id_transaksi) AS total_transaksi
             FROM transaksi
             WHERE EXTRACT(YEAR FROM tanggal_transaksi) = ?",
            [$tahun]
        )->getRowArray();

        $totalOmzet = (float) ($rowRingkasan['total_omzet'] ?? 0);
        $totalMargin = (float) ($rowRingkasan['total_margin'] ?? 0);
        $totalTransaksi = (int) ($rowRingkasan['total_transaksi'] ?? 0);
        $topKategoriRow = $this->db->query(
            "SELECT c.categoryname,
                    COALESCE(SUM(dt.subtotal_harga), 0) AS omzet_kategori
             FROM detail_transaksi dt
             JOIN transaksi t ON t.id_transaksi = dt.id_transaksi
             JOIN barang b ON b.id_barang = dt.id_barang
             JOIN mscategory c ON c.categoryid = b.categoryid
             WHERE EXTRACT(YEAR FROM t.tanggal_transaksi) = ?
             GROUP BY c.categoryid, c.categoryname
             ORDER BY omzet_kategori DESC
             LIMIT 1",
            [$tahun]
        )->getRowArray();

        $topKategoriNama = $topKategoriRow['categoryname'] ?? '-';
        $topKategoriOmzet = (float) ($topKategoriRow['omzet_kategori'] ?? 0);
        $topKategoriPersen = $totalOmzet > 0 ? round(($topKategoriOmzet / $totalOmzet) * 100, 1) : 0;
        return [
            'total_omzet' => $totalOmzet,
            'total_margin' => $totalMargin,
            'total_transaksi' => $totalTransaksi,
            'top_kategori_nama' => $topKategoriNama,
            'top_kategori_omzet' => $topKategoriOmzet,
            'top_kategori_persen' => $topKategoriPersen,
        ];
    }

    public function getChartMonthly($tahun)
    {
        $tahun = (int) $tahun;
        $tahunLalu = $tahun - 1;
        $rowsCurrent = $this->db->query(
            "SELECT EXTRACT(MONTH FROM tanggal_transaksi)::int AS bulan,
                    COALESCE(SUM(total_bayar), 0) AS omzet,
                    COALESCE(SUM(total_margin), 0) AS margin
             FROM transaksi
             WHERE EXTRACT(YEAR FROM tanggal_transaksi) = ?
             GROUP BY bulan
             ORDER BY bulan ASC",
            [$tahun]
        )->getResultArray();

        $rowsPrev = $this->db->query(
            "SELECT EXTRACT(MONTH FROM tanggal_transaksi)::int AS bulan,
                    COALESCE(SUM(total_bayar), 0) AS omzet,
                    COALESCE(SUM(total_margin), 0) AS margin
             FROM transaksi
             WHERE EXTRACT(YEAR FROM tanggal_transaksi) = ?
             GROUP BY bulan
             ORDER BY bulan ASC",
            [$tahunLalu]
        )->getResultArray();

        $currentMap = array_column($rowsCurrent, 'omzet', 'bulan');
        $currentMarginMap = array_column($rowsCurrent, 'margin', 'bulan');
        $prevMap = array_column($rowsPrev, 'omzet', 'bulan');
        $prevMarginMap = array_column($rowsPrev, 'margin', 'bulan');
        $currentSeries = [];
        $currentMarginSeries = [];
        $prevSeries = [];
        $prevMarginSeries = [];
        $hasPrevData = false;
        for ($m = 1; $m <= 12; $m++) {
            $valCurr = isset($currentMap[$m]) ? (float) $currentMap[$m] : 0;
            $valMarginCurr = isset($currentMarginMap[$m]) ? (float) $currentMarginMap[$m] : 0;
            $valPrev = isset($prevMap[$m]) ? (float) $prevMap[$m] : 0;
            $valMarginPrev = isset($prevMarginMap[$m]) ? (float) $prevMarginMap[$m] : 0;
            $currentSeries[] = $valCurr;
            $currentMarginSeries[] = $valMarginCurr;
            $prevSeries[] = $valPrev;
            $prevMarginSeries[] = $valMarginPrev;
            if ($valPrev > 0) {
                $hasPrevData = true;
            }
        }
        return [
            'tahun_current' => $tahun,
            'tahun_prev' => $tahunLalu,
            'has_prev_data' => $hasPrevData,
            'series_current' => $currentSeries,
            'series_margin_current' => $currentMarginSeries,
            'series_prev' => $prevSeries,
            'series_margin_prev' => $prevMarginSeries,
        ];
    }

    public function getKlasemenKategori($tahun)
    {
        $tahun = (int) $tahun;

        // Ambil transaksi paling akhir di tahun tersebut
        $latestTx = $this->db->query(
            "SELECT id_transaksi 
             FROM transaksi 
             WHERE EXTRACT(YEAR FROM tanggal_transaksi) = ? 
             ORDER BY tanggal_transaksi DESC, id_transaksi DESC 
             LIMIT 1",
            [$tahun]
        )->getRowArray();
        $latestId = $latestTx['id_transaksi'] ?? 0;

        // Peringkat saat ini (Current Rank)
        $currentRows = $this->db->query(
            "SELECT c.categoryid, c.categoryname,
                    COALESCE(SUM(dt.subtotal_harga), 0) AS total_omzet,
                    COALESCE(SUM(dt.jumlah), 0) AS total_qty
             FROM detail_transaksi dt
             JOIN transaksi t ON t.id_transaksi = dt.id_transaksi
             JOIN barang b ON b.id_barang = dt.id_barang
             JOIN mscategory c ON c.categoryid = b.categoryid
             WHERE EXTRACT(YEAR FROM t.tanggal_transaksi) = ?
             GROUP BY c.categoryid, c.categoryname
             ORDER BY total_omzet DESC",
            [$tahun]
        )->getResultArray();
        if (empty($currentRows)) {
            return [];
        }

        // Peringkat sebelum transaksi terakhir (Previous Rank)
        $prevRankMap = [];
        if ($latestId > 0) {
            $prevRows = $this->db->query(
                "SELECT c.categoryid,
                        COALESCE(SUM(dt.subtotal_harga), 0) AS total_omzet
                 FROM detail_transaksi dt
                 JOIN transaksi t ON t.id_transaksi = dt.id_transaksi
                 JOIN barang b ON b.id_barang = dt.id_barang
                 JOIN mscategory c ON c.categoryid = b.categoryid
                 WHERE EXTRACT(YEAR FROM t.tanggal_transaksi) = ?
                   AND t.id_transaksi != ?
                 GROUP BY c.categoryid
                 ORDER BY total_omzet DESC",
                [$tahun, $latestId]
            )->getResultArray();
            $r = 1;
            foreach ($prevRows as $p) {
                $prevRankMap[$p['categoryid']] = $r++;
            }
        }

        $topTotalOmzet = (float) ($currentRows[0]['total_omzet'] ?? 1);
        if ($topTotalOmzet <= 0) $topTotalOmzet = 1;
        $klasemen = [];
        $currRank = 1;
        foreach ($currentRows as $row) {
            $catId = $row['categoryid'];
            $omzet = (float) $row['total_omzet'];
            $qty = (int) $row['total_qty'];
            $status = 'same';
            $diff = 0;
            if (isset($prevRankMap[$catId])) {
                $oldRank = $prevRankMap[$catId];
                if ($currRank < $oldRank) {
                    $status = 'up';
                    $diff = $oldRank - $currRank;
                } elseif ($currRank > $oldRank) {
                    $status = 'down';
                    $diff = $currRank - $oldRank;
                } else {
                    $status = 'same';
                    $diff = 0;
                }
            } else {
                $status = 'up';
                $diff = 1;
            }
            $persenDariTop = round(($omzet / $topTotalOmzet) * 100, 1);
            $klasemen[] = [
                'rank' => $currRank,
                'categoryid' => $catId,
                'categoryname' => $row['categoryname'],
                'total_omzet' => $omzet,
                'total_qty' => $qty,
                'status' => $status,
                'diff' => $diff,
                'persen_bar' => $persenDariTop,
            ];
            $currRank++;
            if ($currRank > 5) {
                break;
            }
        }
        return $klasemen;
    }
}
