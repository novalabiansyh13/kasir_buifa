<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\BarangModel;
use App\Services\Master\BarangService;
use App\Services\Master\CategoryService;
use DomainException;
use Exception;

class Barang extends BaseController
{
    protected BarangService $barangService;
    protected CategoryService $categoryService;
    protected array $arrbc;

    public function __construct(
        ?BarangService $barangService = null,
        ?CategoryService $categoryService = null
    ) {
        $dataakses = sessionMenu('barang');
        $this->setArrayAccess($dataakses);
        $this->barangService = $barangService ?? new BarangService();
        $this->categoryService = $categoryService ?? new CategoryService();
        $this->arrbc = [
            [
                'Master',
                'Produk & Barang',
            ]
        ];
    }

    public function index()
    {
        return view('master/barang/v_barang', [
            'title'      => 'Data Barang',
            'breadcrumb' => $this->arrbc,
            'akses'      => $this->getArrayAccess(),
            'section'    => 'Produk & Barang'
        ]);
    }

    public function datatable()
    {
        $this->response->setContentType('application/json');
        $table = Datatables::method([BarangModel::class, 'getBarang'], 'searchable')
            ->make();
        $table->updateRow(function ($db, $no) {
            $btn_edit = "<button type='button' onclick=\"openModal('Edit Produk', '" . getURL('barang/form/' . encrypting($db->id_barang)) . "', {}, 'max-w-xl')\" class='btn btn-soft-warning w-8 h-8 p-0 rounded-lg text-sm inline-flex items-center justify-center me-1.5 shadow-xs' title='Edit Produk'><i class='bi bi-pencil-square'></i></button>";
            $btn_hapus = "<button type='button' class='btn btn-soft-danger w-8 h-8 p-0 rounded-lg text-sm inline-flex items-center justify-center shadow-xs cursor-pointer' onclick=\"modalDelete('Hapus barang - " . esc($db->nama_barang) . "', {'link':'" . getURL('barang/delete') . "', 'id':'" . encrypting($db->id_barang) . "', 'pagetype':'table'})\" title='Hapus Produk'><i class='bi bi-trash3-fill'></i></button>";

            $cat_name = !empty($db->categoryname) ? esc($db->categoryname) : 'Tanpa Kategori';
            $cat_badge = "<span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-sky-50 dark:bg-sky-950/50 text-sky-700 dark:text-cyan-300 border border-sky-200/80 dark:border-sky-800/60 shadow-2xs'>{$cat_name}</span>";

            return [
                $no,
                "<span class='font-bold text-slate-900 dark:text-white'>" . esc($db->nama_barang) . "</span>",
                $cat_badge,
                "<span class='font-mono text-slate-600 dark:text-slate-400'>" . idr($db->harga_beli) . "</span>",
                "<span class='font-mono font-bold text-slate-900 dark:text-white'>" . idr($db->harga_jual) . "</span>",
                "<span class='badge " . ($db->margin >= 0 ? 'badge-soft-success' : 'badge-soft-danger') . " font-mono font-bold'>" . idr($db->margin) . "</span>",
                $btn_edit . " " . $btn_hapus
            ];
        });
        $table->toJson();
    }

    public function forms($id = "")
    {
        $form_type = (empty($id) ? 'add' : 'edit');
        $section = (empty($id) ? 'Tambah Barang' : 'Update Barang');
        $row = [];
        if ($id !== '') {
            $idDec = (int) decrypting($id);
            $row = $this->barangService->getOne($idDec) ?? [];
        }
        array_push($this->arrbc[0], 'Form');
        return view('master/barang/v_form', [
            'form_type'  => $form_type,
            'section'    => $section,
            'title'      => 'Data Barang',
            'breadcrumb' => $this->arrbc,
            'categories' => $this->categoryService->getAll(),
            'row'        => $row
        ]);
    }

    public function addBarang()
    {
        $this->response->setContentType('application/json');
        $categoryid = $this->getPost('categoryid');
        if (!empty($categoryid) && !is_numeric($categoryid)) {
            $categoryid = (int) decrypting($categoryid);
        } else {
            $categoryid = (int) $categoryid;
        }

        $nama_barang = $this->getPost('nama_barang');
        $harga_beli = $this->getPost('harga_beli');
        $harga_jual = $this->getPost('harga_jual');

        try {
            $this->barangService->store([
                'categoryid'  => $categoryid,
                'nama_barang' => $nama_barang,
                'harga_beli'  => $harga_beli,
                'harga_jual'  => $harga_jual,
            ]);

            return $this->response->setJSON([
                'success'   => true,
                'sukses'    => '1',
                'msg'       => 'Barang baru berhasil ditambahkan.',
                'pesan'     => 'Barang baru berhasil ditambahkan.',
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
                'msg'       => 'Terjadi kesalahan saat menambahkan barang.',
                'pesan'     => 'Terjadi kesalahan saat menambahkan barang.',
                'csrfToken' => csrf_hash(),
            ]);
        }
    }

    public function updateBarang()
    {
        $this->response->setContentType('application/json');
        $id = (int) decrypting($this->getPost('id'));
        $categoryid = $this->getPost('categoryid');
        if (!empty($categoryid) && !is_numeric($categoryid)) {
            $categoryid = (int) decrypting($categoryid);
        } else {
            $categoryid = (int) $categoryid;
        }

        $nama_barang = $this->getPost('nama_barang');
        $harga_beli = $this->getPost('harga_beli');
        $harga_jual = $this->getPost('harga_jual');

        try {
            $this->barangService->update($id, [
                'categoryid'  => $categoryid,
                'nama_barang' => $nama_barang,
                'harga_beli'  => $harga_beli,
                'harga_jual'  => $harga_jual,
            ]);

            return $this->response->setJSON([
                'success'   => true,
                'sukses'    => '1',
                'msg'       => 'Update barang berhasil.',
                'pesan'     => 'Update barang berhasil.',
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
                'msg'       => 'Terjadi kesalahan saat memperbarui barang.',
                'pesan'     => 'Terjadi kesalahan saat memperbarui barang.',
                'csrfToken' => csrf_hash(),
            ]);
        }
    }

    public function deleteBarang()
    {
        $this->response->setContentType('application/json');
        $id = (int) decrypting($this->getPost('id'));

        try {
            $this->barangService->delete($id);

            return $this->response->setJSON([
                'success'   => true,
                'sukses'    => '1',
                'msg'       => 'Barang berhasil dihapus.',
                'pesan'     => 'Barang berhasil dihapus.',
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
                'msg'       => 'Data gagal dihapus.',
                'pesan'     => 'Data gagal dihapus.',
                'csrfToken' => csrf_hash(),
            ]);
        }
    }

    public function getBarang($stmt = '')
    {
        $this->response->setContentType('application/json');
        $search = $this->getPost('searchTerm') ?? '';
        $categoryid = $this->getPost('categoryid') ?? '';
        $catIdInt = null;
        if (!empty($categoryid)) {
            $catIdInt = !is_numeric($categoryid) ? (int) decrypting($categoryid) : (int) $categoryid;
        }

        $get = $this->barangService->getSelectOptions($search, $catIdInt);
        $arr = [];
        foreach ($get as $g) {
            $catLabel = !empty($g['categoryname']) ? ' [' . $g['categoryname'] . ']' : '';
            $arr[] = [
                'id'           => (empty($stmt) ? encrypting($g['id_barang']) : $g['id_barang']),
                'text'         => $g['nama_barang'] . $catLabel,
                'nama_barang'  => $g['nama_barang'],
                'categoryname' => $g['categoryname'] ?? '',
                'categoryid'   => $g['categoryid'] ?? '',
                'harga'        => (float) $g['harga_jual'],
                'margin'       => (float) $g['margin'],
            ];
        }

        return $this->response->setJSON([
            'data'      => $arr,
            'csrfToken' => csrf_hash(),
        ]);
    }

    public function getCategory($stmt = '')
    {
        $this->response->setContentType('application/json');
        $search = $this->getPost('searchTerm') ?? '';
        $get = $this->categoryService->getSelectOptions($search);
        $arr = [];
        foreach ($get as $g) {
            $arr[] = [
                'id'   => (empty($stmt) ? encrypting($g['categoryid']) : $g['categoryid']),
                'text' => $g['categoryname'],
            ];
        }

        return $this->response->setJSON([
            'data'      => $arr,
            'csrfToken' => csrf_hash(),
        ]);
    }
}
