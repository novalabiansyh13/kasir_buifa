<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiModel extends Model
{
    protected $table = 'transaksi as a';
    public function __construct()
    {
        parent::__construct();
        $this->db = db_connect();
        $this->builder = $this->db->table($this->table);
    }

    public function searchable()
    {
        return [
            null,
            'a.tanggal',
            'a.jam',
            'a.detail_barang',
            'a.total_bayar',
            'a.total_margin',
        ];
    }

    public function getRekap($startDate = null, $endDate = null)
    {
        $startDate = !empty($startDate) ? $startDate : date('Y-m-d');
        $endDate = !empty($endDate) ? $endDate : date('Y-m-d');
        $sub = $this->db->table('transaksi t')
            ->select("t.id_transaksi, TO_CHAR(t.tanggal_transaksi, 'DD/MM/YYYY') as tanggal, TO_CHAR(t.tanggal_transaksi, 'HH24:MI') as jam, t.total_bayar, t.total_margin, COALESCE(STRING_AGG(b.nama_barang || ' x' || dt.jumlah, ', '), '-') as detail_barang")
            ->join('detail_transaksi dt', 'dt.id_transaksi = t.id_transaksi', 'left')
            ->join('barang b', 'b.id_barang = dt.id_barang', 'left')
            ->where("DATE(t.tanggal_transaksi) >=", $startDate)
            ->where("DATE(t.tanggal_transaksi) <=", $endDate)
            ->groupBy('t.id_transaksi, t.tanggal_transaksi, t.total_bayar, t.total_margin')
            ->orderBy('t.tanggal_transaksi', 'DESC');

        return $this->db->table('(' . $sub->getCompiledSelect() . ') as a');
    }

    public function getRingkasan($startDate = null, $endDate = null)
    {
        $startDate = !empty($startDate) ? $startDate : date('Y-m-d');
        $endDate = !empty($endDate) ? $endDate : date('Y-m-d');
        $row = $this->db->query(
            "SELECT COALESCE(SUM(total_bayar), 0) AS total_penjualan,
                    COALESCE(SUM(total_margin), 0) AS total_margin
             FROM transaksi
             WHERE DATE(tanggal_transaksi) >= '$startDate' AND DATE(tanggal_transaksi) <= '$endDate'"
        )->getRowArray();

        return $row ?? ['total_penjualan' => 0, 'total_margin' => 0];
    }

    public function store($data)
    {
        return $this->builder->insert($data);
    }

    public function edit($data, $id)
    {
        return $this->builder->update($data, ['id_transaksi' => $id]);
    }

    public function destroy($id)
    {
        return $this->builder->delete(['id_transaksi' => $id]);
    }
}
