<?php

namespace App\Controllers\Kasir;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\BarangModel;
use App\Models\DetailTransaksiModel;
use App\Models\TransaksiModel;
use Exception;

class Kasir extends BaseController
{
    function __construct()
    {
        $dataakses = sessionMenu('kasir');
        $this->setArrayAccess($dataakses);
        $this->barang = new BarangModel();
        $this->transaksi = new TransaksiModel();
        $this->detail = new DetailTransaksiModel();
        $this->arrbc = [
            [
                'Kasir',
                'Dashboard',
            ]
        ];
    }

    function index()
    {
        return view('kasir/v_kasir', [
            'title' => 'Kasir Pintar Bu Ifa',
            'breadcrumb' => $this->arrbc,
            'akses' => $this->getArrayAccess(),
            'section' => 'Kasir / Dashboard'
        ]);
    }

    public function datatable()
    {
        $this->response->setContentType('application/json');
        $ringkasan = $this->transaksi->getRingkasanHariIni();
        $table = Datatables::method([TransaksiModel::class, 'getRekap'], 'searchable')
            ->make();
        $table->updateRow(function ($db, $no) {
            return [
                $no,
                "<span class='badge bg-secondary'>" . $db->jam . "</span>",
                $db->detail_barang,
                "<span class='fw-semibold text-success'>" . idr($db->total_bayar) . "</span>",
                "<span class='fw-semibold text-warning'>" . idr($db->total_margin) . "</span>",
            ];
        });
        $table->toJson([
            'total_penjualan' => (float) $ringkasan['total_penjualan'],
            'total_margin' => (float) $ringkasan['total_margin'],
        ]);
    }

    function simpan()
    {
        $items = $this->getPost('items');
        $res = array();
        $this->response->setContentType('application/json');
        $this->db->transBegin();
        try {
            if (empty($items)) {
                throw new Exception("Keranjang kosong.");
            }
            $items = json_decode($items, true);
            if (empty($items) || !is_array($items)) {
                throw new Exception("Data item tidak valid.");
            }
            $totalBayar = 0;
            $totalMargin = 0;
            $detailRows = [];
            foreach ($items as $item) {
                $barang = $this->barang->getOne(decrypting($item['id_barang']));
                if (empty($barang)) {
                    continue;
                }
                $jumlah = max(1, (int) $item['jumlah']);
                $hargaJual = (float) $barang['harga_jual'];
                $marginSatuan = (float) $barang['margin'];
                $totalBayar += $hargaJual * $jumlah;
                $totalMargin += $marginSatuan * $jumlah;
                $detailRows[] = [
                    'id_barang' => $barang['id_barang'],
                    'jumlah' => $jumlah,
                    'harga_jual_satuan' => $hargaJual,
                    'margin_satuan' => $marginSatuan,
                    'subtotal_harga' => $hargaJual * $jumlah,
                    'subtotal_margin' => $marginSatuan * $jumlah,
                ];
            }
            if (empty($detailRows)) {
                throw new Exception("Tidak ada item yang valid.");
            }
            $this->transaksi->store([
                'total_bayar' => $totalBayar,
                'total_margin' => $totalMargin,
            ]);
            $idTransaksi = $this->db->insertID();
            foreach ($detailRows as &$row) {
                $row['id_transaksi'] = $idTransaksi;
            }
            unset($row);
            $this->detail->storeBatch($detailRows);
            $res = [
                'pesan' => 'Transaksi berhasil disimpan!',
                'sukses' => '1',
                'trace' => db_connect()->error(),
            ];
            $this->db->transCommit();
        } catch (Exception $e) {
            $res = [
                'sukses' => '0',
                'pesan' => $e->getMessage(),
                'traceString' => $e->getTraceAsString(),
            ];
            $this->db->transRollback();
        }
        $this->db->transComplete();
        $res['csrfToken'] = csrf_hash();
        echo json_encode($res);
    }
}
