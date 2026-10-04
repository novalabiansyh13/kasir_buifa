<?php

namespace App\Controllers\Kasir;

use App\Controllers\BaseController;
use App\Services\Kasir\KasirService;
use App\Services\Master\CategoryService;
use DomainException;
use Exception;

class Kasir extends BaseController
{
    protected KasirService $kasirService;
    protected CategoryService $categoryService;
    protected array $arrbc;

    public function __construct(?KasirService $kasirService = null, ?CategoryService $categoryService = null)
    {
        $dataakses = sessionMenu('kasir');
        $this->setArrayAccess($dataakses);
        $this->kasirService = $kasirService ?? new KasirService();
        $this->categoryService = $categoryService ?? new CategoryService();
        $this->arrbc = [
            [
                'Transaction',
                'Kasir',
            ]
        ];
    }

    public function index()
    {
        $categories = $this->categoryService->getAll();

        return view('kasir/v_kasir', [
            'title'      => 'Kasir Pintar Bu Ifa &bull; Terminal POS',
            'breadcrumb' => $this->arrbc,
            'akses'      => $this->getArrayAccess(),
            'section'    => 'Transaction',
            'categories' => $categories,
        ]);
    }

    public function products()
    {
        $this->response->setContentType('application/json');

        $search = $this->request->getVar('search') ?? '';
        $categoryId = $this->request->getVar('categoryid') ?? $this->request->getVar('category_id') ?? '';
        $page = (int) ($this->request->getVar('page') ?? 1);
        $perPage = (int) ($this->request->getVar('per_page') ?? 12);

        $catIdInt = null;
        if (!empty($categoryId)) {
            $catIdInt = !is_numeric($categoryId) ? (int) decrypting($categoryId) : (int) $categoryId;
        }

        try {
            $result = $this->kasirService->getProductsCatalog(
                is_string($search) ? trim($search) : '',
                $catIdInt,
                $page,
                $perPage
            );

            return $this->response->setJSON([
                'success'    => true,
                'data'       => $result['data'],
                'pagination' => [
                    'total'       => $result['total'],
                    'page'        => $result['page'],
                    'per_page'    => $result['per_page'],
                    'total_pages' => $result['total_pages'],
                ],
                'csrfToken'  => csrf_hash(),
            ]);
        } catch (Exception $e) {
            return $this->response->setJSON([
                'success'    => false,
                'msg'        => 'Gagal memuat katalog produk.',
                'data'       => [],
                'pagination' => [
                    'total'       => 0,
                    'page'        => 1,
                    'per_page'    => $perPage,
                    'total_pages' => 1,
                ],
                'csrfToken'  => csrf_hash(),
            ]);
        }
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
