<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\CategoryModel;
use App\Services\Master\CategoryService;
use DomainException;
use Exception;

class Category extends BaseController
{
    protected CategoryService $categoryService;
    protected array $arrbc;

    public function __construct(?CategoryService $categoryService = null)
    {
        $dataakses = sessionMenu('category');
        $this->setArrayAccess($dataakses);
        $this->categoryService = $categoryService ?? new CategoryService();
        $this->arrbc = [
            [
                'Master',
                'Kategori',
            ]
        ];
    }

    public function index()
    {
        return view('master/category/v_category', [
            'title'      => 'Data Kategori Produk',
            'breadcrumb' => $this->arrbc,
            'akses'      => $this->getArrayAccess(),
            'section'    => 'Kategori Produk'
        ]);
    }

    public function datatable()
    {
        $this->response->setContentType('application/json');
        $table = Datatables::method([CategoryModel::class, 'getCategory'], 'searchable')
            ->make();
        $table->updateRow(function ($db, $no) {
            $idEnc = encrypting($db->categoryid);
            $btn_edit = "<button type='button' onclick=\"openModal('Edit Kategori', '" . getURL('category/form/' . $idEnc) . "', {}, 'max-w-md')\" class='btn btn-soft-warning w-8 h-8 p-0 rounded-lg text-sm inline-flex items-center justify-center me-1.5 shadow-xs' title='Edit Kategori'><i class='bi bi-pencil-square'></i></button>";
            $btn_hapus = "<button type='button' class='btn btn-soft-danger w-8 h-8 p-0 rounded-lg text-sm inline-flex items-center justify-center shadow-xs cursor-pointer' onclick=\"modalDelete('Hapus kategori - " . esc($db->categoryname) . "', {'link':'" . getURL('category/delete') . "', 'id':'" . $idEnc . "', 'pagetype':'table'})\" title='Hapus Kategori'><i class='bi bi-trash3-fill'></i></button>";

            $createdDate = !empty($db->createddate) ? formatDate('d M Y H:i', $db->createddate) : '-';
            $createdBy = !empty($db->createdby) ? esc($db->createdby) : 'system';

            return [
                $no,
                "<span class='font-bold text-slate-900 dark:text-white'>" . esc($db->categoryname) . "</span>",
                "<span class='text-xs font-mono text-slate-600 dark:text-slate-400'>" . $createdDate . "</span>",
                "<span class='badge badge-soft-secondary font-mono text-[11px]'>" . $createdBy . "</span>",
                $btn_edit . " " . $btn_hapus
            ];
        });
        $table->toJson();
    }

    public function forms($id = "")
    {
        $form_type = (empty($id) ? 'add' : 'edit');
        $row = [];
        if ($id !== '') {
            $idDec = (int) decrypting($id);
            $row = $this->categoryService->getOne($idDec) ?? [];
        }
        return view('master/category/v_form', [
            'form_type' => $form_type,
            'row'       => $row
        ]);
    }

    public function addCategory()
    {
        $this->response->setContentType('application/json');
        $categoryname = trim($this->getPost('categoryname') ?? '');

        try {
            $this->categoryService->store(['categoryname' => $categoryname]);

            return $this->response->setJSON([
                'success'   => true,
                'sukses'    => '1',
                'msg'       => 'Kategori baru berhasil ditambahkan.',
                'pesan'     => 'Kategori baru berhasil ditambahkan.',
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
                'msg'       => 'Terjadi kesalahan saat menambahkan kategori.',
                'pesan'     => 'Terjadi kesalahan saat menambahkan kategori.',
                'csrfToken' => csrf_hash(),
            ]);
        }
    }

    public function updateCategory()
    {
        $this->response->setContentType('application/json');
        $id = (int) decrypting($this->getPost('id'));
        $categoryname = trim($this->getPost('categoryname') ?? '');

        try {
            $this->categoryService->update($id, ['categoryname' => $categoryname]);

            return $this->response->setJSON([
                'success'   => true,
                'sukses'    => '1',
                'msg'       => 'Kategori berhasil diperbarui.',
                'pesan'     => 'Kategori berhasil diperbarui.',
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
                'msg'       => 'Terjadi kesalahan saat memperbarui kategori.',
                'pesan'     => 'Terjadi kesalahan saat memperbarui kategori.',
                'csrfToken' => csrf_hash(),
            ]);
        }
    }

    public function deleteCategory()
    {
        $this->response->setContentType('application/json');
        $id = (int) decrypting($this->getPost('id'));

        try {
            $this->categoryService->delete($id);

            return $this->response->setJSON([
                'success'   => true,
                'sukses'    => '1',
                'msg'       => 'Kategori berhasil dihapus.',
                'pesan'     => 'Kategori berhasil dihapus.',
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
                'msg'       => 'Kategori gagal dihapus.',
                'pesan'     => 'Kategori gagal dihapus.',
                'csrfToken' => csrf_hash(),
            ]);
        }
    }
}
