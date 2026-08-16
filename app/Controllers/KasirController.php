<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\TransaksiModel;
use App\Models\DetailTransaksiModel;

class KasirController extends BaseController
{
    protected BarangModel $barangModel;
    protected TransaksiModel $transaksiModel;
    protected DetailTransaksiModel $detailModel;

    public function __construct()
    {
        $this->barangModel    = new BarangModel();
        $this->transaksiModel = new TransaksiModel();
        $this->detailModel    = new DetailTransaksiModel();
    }

    // ─── Dashboard utama ─────────────────────────────────────────────────────

    public function index(): string
    {
        $data = [
            'title'      => 'Kasir Pintar Bu Ifa',
            'barang_list' => $this->barangModel->getAll(),
            'transaksi'  => $this->transaksiModel->getHariIni(),
            'ringkasan'  => $this->transaksiModel->getRingkasanHariIni(),
        ];

        return view('kasir/index', $data);
    }

    // ─── Simpan transaksi (POST) ──────────────────────────────────────────────

    public function simpan()
    {
        $items = $this->request->getPost('items');   // JSON string dari JS

        if (empty($items)) {
            return redirect()->to('/kasir')->with('error', 'Keranjang kosong.');
        }

        $items = json_decode($items, true);

        if (empty($items) || ! is_array($items)) {
            return redirect()->to('/kasir')->with('error', 'Data item tidak valid.');
        }

        $totalBayar  = 0;
        $totalMargin = 0;
        $detailRows  = [];

        foreach ($items as $item) {
            $barang = $this->barangModel->find((int) $item['id_barang']);
            if (! $barang) {
                continue;
            }

            $jumlah          = max(1, (int) $item['jumlah']);
            $hargaJual       = (float) $barang['harga_jual'];
            $marginSatuan    = (float) $barang['margin'];
            $subtotalHarga   = $hargaJual * $jumlah;
            $subtotalMargin  = $marginSatuan * $jumlah;

            $totalBayar  += $subtotalHarga;
            $totalMargin += $subtotalMargin;

            $detailRows[] = [
                'id_barang'        => $barang['id_barang'],
                'jumlah'           => $jumlah,
                'harga_jual_satuan' => $hargaJual,
                'margin_satuan'    => $marginSatuan,
                'subtotal_harga'   => $subtotalHarga,
                'subtotal_margin'  => $subtotalMargin,
            ];
        }

        if (empty($detailRows)) {
            return redirect()->to('/kasir')->with('error', 'Tidak ada item yang valid.');
        }

        // Insert header transaksi
        $idTransaksi = $this->transaksiModel->insert([
            'total_bayar'  => $totalBayar,
            'total_margin' => $totalMargin,
        ]);

        // Insert detail satu per satu
        foreach ($detailRows as &$row) {
            $row['id_transaksi'] = $idTransaksi;
        }
        unset($row);

        $this->detailModel->insertBatch($detailRows);

        return redirect()->to('/kasir')->with('success', 'Transaksi berhasil disimpan!');
    }

    // ─── AJAX: rekap hari ini (JSON) ─────────────────────────────────────────

    public function rekap()
    {
        return $this->response->setJSON([
            'transaksi' => $this->transaksiModel->getHariIni(),
            'ringkasan' => $this->transaksiModel->getRingkasanHariIni(),
        ]);
    }

    // ─── API: daftar barang (JSON) ────────────────────────────────────────────

    public function apiBarang()
    {
        return $this->response->setJSON($this->barangModel->getAll());
    }

    public function apiBarangDetail(int $id)
    {
        $barang = $this->barangModel->find($id);
        if (! $barang) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Barang tidak ditemukan.']);
        }
        return $this->response->setJSON($barang);
    }
}
