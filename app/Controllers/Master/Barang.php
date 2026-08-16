<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\BarangModel;
use App\Models\CategoryModel;
use Exception;

class Barang extends BaseController
{
    function __construct()
    {
        $dataakses = sessionMenu('barang');
        $this->setArrayAccess($dataakses);
        $this->barang = new BarangModel();
        $this->category = new CategoryModel();
        $this->arrbc = [
            [
                'Master',
                'Barang',
            ]
        ];
    }

    function index()
    {
        return view('master/barang/v_barang', [
            'title' => 'Data Barang',
            'breadcrumb' => $this->arrbc,
            'akses' => $this->getArrayAccess(),
            'section' => 'Master Barang'
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
        if ($id != '') {
            $id = decrypting($id);
            $row = $this->barang->getOne($id);
        }
        array_push($this->arrbc[0], 'Form');
        return view('master/barang/v_form', [
            'form_type' => $form_type,
            'section' => $section,
            'title' => 'Data Barang',
            'breadcrumb' => $this->arrbc,
            'categories' => $this->category->getAll(),
            'row' => $row
        ]);
    }

    function addBarang()
    {
        $categoryid = $this->getPost('categoryid');
        if (!empty($categoryid) && !is_numeric($categoryid)) {
            $categoryid = decrypting($categoryid);
        }
        $nama_barang = $this->getPost('nama_barang');
        $harga_beli = $this->getPost('harga_beli');
        $harga_jual = $this->getPost('harga_jual');
        $res = array();
        $this->response->setContentType('application/json');
        $this->db->transBegin();
        try {
            if (empty($nama_barang) || empty($harga_beli) || empty($harga_jual)) {
                throw new Exception("Nama barang / harga masih kosong");
            }
            if (empty($categoryid)) {
                throw new Exception("Silakan pilih kategori produk");
            }
            $data = [
                'categoryid' => (int) $categoryid,
                'nama_barang' => $nama_barang,
                'harga_beli' => (float) $harga_beli,
                'harga_jual' => (float) $harga_jual,
            ];
            $this->barang->hitungMargin($data);
            $this->barang->store($data);
            $res = [
                'pesan' => 'Barang baru ditambahkan',
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

    function updateBarang()
    {
        $id = decrypting($this->getPost('id'));
        $categoryid = $this->getPost('categoryid');
        if (!empty($categoryid) && !is_numeric($categoryid)) {
            $categoryid = decrypting($categoryid);
        }
        $nama_barang = $this->getPost('nama_barang');
        $harga_beli = $this->getPost('harga_beli');
        $harga_jual = $this->getPost('harga_jual');
        $res = array();
        $this->response->setContentType('application/json');
        $this->db->transBegin();
        try {
            if (empty($id) || empty($nama_barang) || empty($harga_beli) || empty($harga_jual)) {
                throw new Exception("Data barang belum lengkap");
            }
            if (empty($categoryid)) {
                throw new Exception("Silakan pilih kategori produk");
            }
            $data = [
                'categoryid' => (int) $categoryid,
                'nama_barang' => $nama_barang,
                'harga_beli' => (float) $harga_beli,
                'harga_jual' => (float) $harga_jual,
            ];
            $this->barang->hitungMargin($data);
            $this->barang->edit($data, $id);
            $res = [
                'pesan' => 'Update barang berhasil',
                'sukses' => '1',
                'trace' => db_connect()->error(),
            ];
            $this->db->transCommit();
        } catch (Exception $e) {
            $res = [
                'sukses' => '0',
                'pesan' => $e->getMessage(),
                'traceString' => $e->getTraceAsString()
            ];
            $this->db->transRollback();
        }
        $this->db->transComplete();
        $res['csrfToken'] = csrf_hash();
        echo json_encode($res);
    }

    function deleteBarang()
    {
        $id = decrypting($this->getPost('id'));
        $res = array();
        $this->response->setContentType('application/json');
        $this->db->transBegin();
        try {
            if (empty($id)) {
                throw new Exception("ID barang tidak valid");
            }
            $tables = [
                ['table' => 'detail_transaksi', 'column' => 'id_barang', 'value' => $id, 'alias' => 'Transaksi'],
            ];
            $getvalidate = validateDeleteData($tables);
            if (!empty($getvalidate)) {
                $aliases = array_unique(array_column($getvalidate, 'alias'));
                $msg = "<div>Data sedang digunakan di :</div>";
                $msg .= "<ul style='margin: 0; padding-left: 20px;'>";
                foreach ($aliases as $alias) {
                    $msg .= "<li>" . $alias . "</li>";
                }
                $msg .= "</ul>";
                throw new Exception($msg);
            }
            $this->barang->destroy($id);
            $res['sukses'] = '1';
            $res['pesan'] = 'Barang berhasil dihapus';
            $this->db->transCommit();
        } catch (Exception $e) {
            $res = [
                'sukses' => '0',
                'pesan' => (!empty($e->getMessage())) ? $e->getMessage() : "Data gagal dihapus"
            ];
            $this->db->transRollback();
        }
        $this->db->transComplete();
        $res['csrfToken'] = csrf_hash();
        echo json_encode($res);
    }

    function getBarang($stmt = '')
    {
        $this->response->setContentType('application/json');
        $search = $this->getPost('searchTerm') ?? '';
        $categoryid = $this->getPost('categoryid') ?? '';
        if (!empty($categoryid) && !is_numeric($categoryid)) {
            $categoryid = decrypting($categoryid);
        }
        $get = $this->barang->getSelect($search, $categoryid);
        $arr = [];
        foreach ($get as $g) {
            $catLabel = !empty($g['categoryname']) ? ' [' . $g['categoryname'] . ']' : '';
            $arr[] = [
                'id' => (empty($stmt) ? encrypting($g['id_barang']) : $g['id_barang']),
                'text' => $g['nama_barang'] . $catLabel,
                'nama_barang' => $g['nama_barang'],
                'categoryname' => $g['categoryname'] ?? '',
                'categoryid' => $g['categoryid'] ?? '',
                'harga' => (float) $g['harga_jual'],
                'margin' => (float) $g['margin'],
            ];
        }
        echo encode([
            'data' => $arr,
            'csrfToken' => csrf_hash(),
            'trace' => db_connect()->error(),
        ]);
    }

    function getCategory($stmt = '')
    {
        $this->response->setContentType('application/json');
        $search = $this->getPost('searchTerm') ?? '';
        $get = $this->category->getSelect($search);
        $arr = [];
        foreach ($get as $g) {
            $arr[] = [
                'id' => (empty($stmt) ? encrypting($g['categoryid']) : $g['categoryid']),
                'text' => $g['categoryname'],
            ];
        }
        echo encode([
            'data' => $arr,
            'csrfToken' => csrf_hash(),
            'trace' => db_connect()->error(),
        ]);
    }
}
