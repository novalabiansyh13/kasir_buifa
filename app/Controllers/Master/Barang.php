<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\BarangModel;
use Exception;

class Barang extends BaseController
{
    function __construct()
    {
        $dataakses = sessionMenu('barang');
        $this->setArrayAccess($dataakses);
        $this->barang = new BarangModel();
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
            $btn_edit = "<a href='" . getURL('barang/form/' . encrypting($db->id_barang)) . "' class='btn btn-sm btn-outline-primary me-1'><i class='bi bi-pencil-square'></i></a>";
            $btn_hapus = "<button type='button' class='btn btn-sm btn-outline-danger' onclick=\"modalDelete('Hapus barang - " . $db->nama_barang . "', {'link':'" . getURL('barang/delete') . "', 'id':'" . encrypting($db->id_barang) . "', 'pagetype':'table'})\"><i class='bi bi-trash3'></i></button>";
            return [
                $no,
                $db->nama_barang,
                idr($db->harga_beli),
                idr($db->harga_jual),
                "<span class='badge " . ($db->margin >= 0 ? 'bg-success' : 'bg-danger') . "'>" . idr($db->margin) . "</span>",
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
            'row' => $row
        ]);
    }

    function addBarang()
    {
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
            $data = [
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
            $data = [
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
        $search = $this->getPost('searchTerm');
        $get = $this->barang->getSelect($search);
        $arr = [];
        foreach ($get as $g) {
            $arr[] = [
                'id' => (empty($stmt) ? encrypting($g['id_barang']) : $g['id_barang']),
                'text' => $g['nama_barang'],
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
}
