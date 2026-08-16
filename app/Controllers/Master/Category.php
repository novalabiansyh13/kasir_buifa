<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\CategoryModel;
use Exception;

class Category extends BaseController
{
    function __construct()
    {
        $dataakses = sessionMenu('category');
        $this->setArrayAccess($dataakses);
        $this->category = new CategoryModel();
        $this->arrbc = [
            [
                'Master',
                'Kategori',
            ]
        ];
    }

    function index()
    {
        return view('master/category/v_category', [
            'title' => 'Data Kategori Produk',
            'breadcrumb' => $this->arrbc,
            'akses' => $this->getArrayAccess(),
            'section' => 'Kategori Produk'
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
        if ($id != '') {
            $id = decrypting($id);
            $row = $this->category->getOne($id);
        }
        return view('master/category/v_form', [
            'form_type' => $form_type,
            'row' => $row
        ]);
    }

    function addCategory()
    {
        $categoryname = trim($this->getPost('categoryname') ?? '');
        $res = [];
        $this->response->setContentType('application/json');
        $this->db->transBegin();
        try {
            if (empty($categoryname)) {
                throw new Exception("Nama kategori tidak boleh kosong.");
            }
            $data = [
                'categoryname' => $categoryname,
            ];
            $this->category->store($data);
            $res = [
                'sukses' => '1',
                'pesan' => 'Kategori baru berhasil ditambahkan.',
            ];
            $this->db->transCommit();
        } catch (Exception $e) {
            $res = [
                'sukses' => '0',
                'pesan' => $e->getMessage(),
            ];
            $this->db->transRollback();
        }
        $this->db->transComplete();
        $res['csrfToken'] = csrf_hash();
        echo json_encode($res);
    }

    function updateCategory()
    {
        $id = decrypting($this->getPost('id'));
        $categoryname = trim($this->getPost('categoryname') ?? '');
        $res = [];
        $this->response->setContentType('application/json');
        $this->db->transBegin();
        try {
            if (empty($id) || empty($categoryname)) {
                throw new Exception("Data kategori belum lengkap.");
            }
            $data = [
                'categoryname' => $categoryname,
            ];
            $this->category->edit($data, $id);
            $res = [
                'sukses' => '1',
                'pesan' => 'Kategori berhasil diperbarui.',
            ];
            $this->db->transCommit();
        } catch (Exception $e) {
            $res = [
                'sukses' => '0',
                'pesan' => $e->getMessage(),
            ];
            $this->db->transRollback();
        }
        $this->db->transComplete();
        $res['csrfToken'] = csrf_hash();
        echo json_encode($res);
    }

    function deleteCategory()
    {
        $id = decrypting($this->getPost('id'));
        $res = [];
        $this->response->setContentType('application/json');
        $this->db->transBegin();
        try {
            if (empty($id)) {
                throw new Exception("ID kategori tidak valid.");
            }
            $tables = [
                ['table' => 'barang', 'column' => 'categoryid', 'value' => $id, 'alias' => 'Produk / Barang'],
            ];
            $getvalidate = validateDeleteData($tables);
            if (!empty($getvalidate)) {
                $aliases = array_unique(array_column($getvalidate, 'alias'));
                $msg = "<div>Kategori tidak dapat dihapus karena masih digunakan di:</div>";
                $msg .= "<ul style='margin: 0; padding-left: 20px;'>";
                foreach ($aliases as $alias) {
                    $msg .= "<li>" . $alias . "</li>";
                }
                $msg .= "</ul>";
                throw new Exception($msg);
            }
            $this->category->destroy($id);
            $res['sukses'] = '1';
            $res['pesan'] = 'Kategori berhasil dihapus.';
            $this->db->transCommit();
        } catch (Exception $e) {
            $res = [
                'sukses' => '0',
                'pesan' => (!empty($e->getMessage())) ? $e->getMessage() : "Kategori gagal dihapus."
            ];
            $this->db->transRollback();
        }
        $this->db->transComplete();
        $res['csrfToken'] = csrf_hash();
        echo json_encode($res);
    }
}
