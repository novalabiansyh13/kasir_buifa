<?php

namespace App\Controllers\Kasir;

use App\Controllers\BaseController;
use App\Services\Kasir\KasirService;
use DomainException;
use Exception;

class Kasir extends BaseController
{
    protected KasirService $kasirService;
    protected array $arrbc;

    public function __construct(?KasirService $kasirService = null)
    {
        $dataakses = sessionMenu('kasir');
        $this->setArrayAccess($dataakses);
        $this->kasirService = $kasirService ?? new KasirService();
        $this->arrbc = [
            [
                'Transaction',
                'Kasir',
            ]
        ];
    }

    public function index()
    {
        return view('kasir/v_kasir', [
            'title'      => 'Kasir Pintar Bu Ifa &bull; Terminal POS',
            'breadcrumb' => $this->arrbc,
            'akses'      => $this->getArrayAccess(),
            'section'    => 'Transaction',
        ]);
    }

    public function simpan()
    {
        $this->response->setContentType('application/json');
        $rawItems = $this->getPost('items');

        if (empty($rawItems)) {
            return $this->response->setJSON([
                'success'   => false,
                'sukses'    => '0',
                'msg'       => 'Keranjang belanja masih kosong.',
                'pesan'     => 'Keranjang belanja masih kosong.',
                'csrfToken' => csrf_hash(),
            ]);
        }

        $items = is_array($rawItems) ? $rawItems : json_decode($rawItems, true);
        if (!is_array($items)) {
            return $this->response->setJSON([
                'success'   => false,
                'sukses'    => '0',
                'msg'       => 'Format data item transaksi tidak valid.',
                'pesan'     => 'Format data item transaksi tidak valid.',
                'csrfToken' => csrf_hash(),
            ]);
        }

        try {
            $result = $this->kasirService->simpanTransaksi($items);

            return $this->response->setJSON([
                'success'   => true,
                'sukses'    => '1',
                'msg'       => 'Transaksi kasir berhasil disimpan!',
                'pesan'     => 'Transaksi kasir berhasil disimpan!',
                'data'      => $result,
                'csrfToken' => csrf_hash(),
            ]);
        } catch (DomainException $e) {
            return $this->response->setJSON([
                'success'   => false,
                'sukses'    => '0',
                'msg'       => $e->getMessage(),
                'pesan'     => $e->getMessage(),
                'csrfToken' => csrf_hash(),
            ]);
        } catch (Exception $e) {
            return $this->response->setJSON([
                'success'   => false,
                'sukses'    => '0',
                'msg'       => 'Terjadi kesalahan sistem saat menyimpan transaksi.',
                'pesan'     => 'Terjadi kesalahan sistem saat menyimpan transaksi.',
                'csrfToken' => csrf_hash(),
            ]);
        }
    }
}
