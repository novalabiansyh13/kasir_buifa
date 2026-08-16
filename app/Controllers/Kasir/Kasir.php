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
                'Dashboard',
                'Kasir',
            ]
        ];
    }

    function index()
    {
        return view('kasir/v_kasir', [
            'title' => 'Kasir Pintar Bu Ifa',
            'breadcrumb' => $this->arrbc,
            'akses' => $this->getArrayAccess(),
            'section' => 'Kasir'
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
                "<span class='px-2 py-0.5 rounded-md font-mono text-[11px] font-bold bg-slate-100 dark:bg-[#0b1322] border border-slate-200 dark:border-[#1e293b] text-slate-700 dark:text-slate-300'>" . $db->jam . "</span>",
                "<span class='font-medium text-slate-800 dark:text-slate-200'>" . esc($db->detail_barang) . "</span>",
                "<span class='font-bold font-mono text-sky-600 dark:text-cyan-400'>" . idr($db->total_bayar) . "</span>",
                "<span class='font-bold font-mono text-amber-600 dark:text-amber-400'>" . idr($db->total_margin) . "</span>",
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
                throw new Exception("Keranjang belanja masih kosong.");
            }
            $items = json_decode($items, true);
            if (empty($items) || !is_array($items)) {
                throw new Exception("Format data item transaksi tidak valid.");
            }
            $totalBayar = 0;
            $totalMargin = 0;
            $detailRows = [];
            foreach ($items as $item) {
                $idItem = !empty($item['id_barang']) ? $item['id_barang'] : ($item['id'] ?? null);
                if (empty($idItem)) {
                    continue;
                }
                $rawId = is_numeric($idItem) ? (int)$idItem : decrypting($idItem);
                $barang = $this->barang->getOne($rawId);
                if (empty($barang)) {
                    continue;
                }
                $jumlah = max(1, (int) ($item['jumlah'] ?? $item['qty'] ?? 1));
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
                throw new Exception("Tidak ada produk valid yang ditemukan di keranjang.");
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
                'pesan' => 'Transaksi kasir berhasil disimpan!',
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
