<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\Msmenu;
use App\Models\Msrole;
use Exception;

class Usergroup extends BaseController
{
    function __construct()
    {
        $dataakses = sessionMenu('usergroup');
        $this->setArrayAccess($dataakses);
        $this->role = new Msrole();
        $this->menu = new Msmenu();
        $this->arrbc = [
            [
                'Master',
                'User Group',
            ]
        ];
    }

    function index()
    {
        return view('master/usergroup/v_usergroup', [
            'title' => 'Data User Group & Hak Akses',
            'breadcrumb' => $this->arrbc,
            'akses' => $this->getArrayAccess(),
            'section' => 'Master User Group'
        ]);
    }

    public function datatable()
    {
        $this->response->setContentType('application/json');
        $table = Datatables::method([Msrole::class, 'getRoles'], 'searchable')
            ->make();
        $table->updateRow(function ($db, $no) {
            $idEnc = encrypting($db->roleid);
            $btn_access = "<button type='button' onclick=\"openModal('Setting Hak Akses - " . esc($db->rolename) . "', '" . getURL('usergroup/access/' . $idEnc) . "', {}, 'max-w-xl')\" class='btn btn-soft-primary w-8 h-8 p-0 rounded-lg text-sm inline-flex items-center justify-center me-1.5 shadow-xs' title='Setting Akses Menu'><i class='bi bi-shield-lock-fill'></i></button>";
            $btn_edit = "<button type='button' onclick=\"openModal('Edit User Group', '" . getURL('usergroup/form/' . $idEnc) . "', {}, 'max-w-md')\" class='btn btn-soft-warning w-8 h-8 p-0 rounded-lg text-sm inline-flex items-center justify-center me-1.5 shadow-xs' title='Edit User Group'><i class='bi bi-pencil-square'></i></button>";
            $btn_hapus = "<button type='button' class='btn btn-soft-danger w-8 h-8 p-0 rounded-lg text-sm inline-flex items-center justify-center shadow-xs cursor-pointer' onclick=\"modalDelete('Hapus User Group - " . esc($db->rolename) . "', {'link':'" . getURL('usergroup/delete') . "', 'id':'" . $idEnc . "', 'pagetype':'table'})\" title='Hapus User Group'><i class='bi bi-trash3-fill'></i></button>";
            $createdDate = !empty($db->createddate) ? formatDate('d M Y H:i', $db->createddate) : '-';
            $createdBy = !empty($db->createdby) ? esc($db->createdby) : 'system';
            return [
                $no,
                "<span class='font-bold text-slate-900 dark:text-white'>" . esc($db->rolename) . "</span>",
                "<span class='text-xs font-mono text-slate-600 dark:text-slate-400'>" . $createdDate . "</span>",
                "<span class='badge badge-soft-secondary font-mono text-[11px]'>" . $createdBy . "</span>",
                $btn_access . " " . $btn_edit . " " . $btn_hapus
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
            $row = $this->role->getOne($id);
        }
        return view('master/usergroup/v_form', [
            'form_type' => $form_type,
            'row' => $row
        ]);
    }

    function addRole()
    {
        $rolename = trim($this->getPost('rolename') ?? '');
        $res = [];
        $this->response->setContentType('application/json');
        $this->db->transBegin();
        try {
            if (empty($rolename)) {
                throw new Exception("Nama role/user group tidak boleh kosong.");
            }
            $data = [
                'rolename' => $rolename,
            ];
            $this->role->store($data);
            $res = [
                'sukses' => '1',
                'pesan' => 'User Group baru berhasil ditambahkan.',
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

    function updateRole()
    {
        $id = decrypting($this->getPost('id'));
        $rolename = trim($this->getPost('rolename') ?? '');
        $res = [];
        $this->response->setContentType('application/json');
        $this->db->transBegin();
        try {
            if (empty($id) || empty($rolename)) {
                throw new Exception("Data user group belum lengkap.");
            }
            $data = [
                'rolename' => $rolename,
            ];
            $this->role->edit($data, $id);
            $res = [
                'sukses' => '1',
                'pesan' => 'User Group berhasil diperbarui.',
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

    function deleteRole()
    {
        $id = decrypting($this->getPost('id'));
        $res = [];
        $this->response->setContentType('application/json');
        $this->db->transBegin();
        try {
            if (empty($id)) {
                throw new Exception("ID User Group tidak valid.");
            }
            if ((int)$id === 1) {
                throw new Exception("User Group Administrator utama tidak boleh dihapus.");
            }
            $tables = [
                ['table' => 'msuser', 'column' => 'roleid', 'value' => $id, 'alias' => 'User / Pengguna'],
            ];
            $getvalidate = validateDeleteData($tables);
            if (!empty($getvalidate)) {
                $aliases = array_unique(array_column($getvalidate, 'alias'));
                $msg = "<div>User Group tidak dapat dihapus karena masih digunakan oleh:</div>";
                $msg .= "<ul style='margin: 0; padding-left: 20px;'>";
                foreach ($aliases as $alias) {
                    $msg .= "<li>" . $alias . "</li>";
                }
                $msg .= "</ul>";
                throw new Exception($msg);
            }
            $this->role->destroy($id);
            $this->db->table('msaccessmenu')->where('roleid', (int) $id)->delete();
            $res['sukses'] = '1';
            $res['pesan'] = 'User Group berhasil dihapus.';
            $this->db->transCommit();
        } catch (Exception $e) {
            $res = [
                'sukses' => '0',
                'pesan' => (!empty($e->getMessage())) ? $e->getMessage() : "User Group gagal dihapus."
            ];
            $this->db->transRollback();
        }
        $this->db->transComplete();
        $res['csrfToken'] = csrf_hash();
        echo json_encode($res);
    }

    public function formAccess($roleidEnc = "")
    {
        $roleid = decrypting($roleidEnc);
        $role = $this->role->getOne($roleid);
        if (empty($role)) {
            echo "<div class='p-4 text-rose-500 text-xs font-bold'>User Group tidak ditemukan.</div>";
            return;
        }

        $allMenuTree = $this->menu->getAllMenuTree();
        $currentAccess = $this->role->getAccessMenu($roleid);
        $selectedMenuIds = array_column($currentAccess, 'menuid');

        return view('master/usergroup/v_access', [
            'role' => $role,
            'roleidEnc' => $roleidEnc,
            'menuTree' => $allMenuTree,
            'selectedMenuIds' => $selectedMenuIds
        ]);
    }

    public function saveAccess()
    {
        $roleid = decrypting($this->getPost('roleid'));
        $menus = $this->getPost('menus') ?? [];
        $res = [];
        $this->response->setContentType('application/json');
        $this->db->transBegin();
        try {
            if (empty($roleid)) {
                throw new Exception("Role ID tidak valid.");
            }
            $this->role->saveAccessMenu($roleid, $menus);
            $res = [
                'sukses' => '1',
                'pesan' => 'Hak akses menu berhasil disimpan.',
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

    public function getRole($stmt = '')
    {
        $this->response->setContentType('application/json');
        $search = $this->getPost('searchTerm') ?? '';
        $builder = $this->role->builder->select('a.roleid, a.rolename');
        if (!empty($search)) {
            $cari = strtolower(trim($search));
            $builder->where("lower(a.rolename) like '%" . $cari . "%'", null, false);
        }
        $get = $builder->orderBy('a.roleid', 'ASC')->get()->getResultArray();

        $arr = [];
        foreach ($get as $g) {
            $arr[] = [
                'id' => (empty($stmt) ? encrypting($g['roleid']) : $g['roleid']),
                'text' => $g['rolename']
            ];
        }
        echo encode([
            'data' => $arr,
            'csrfToken' => csrf_hash(),
            'trace' => db_connect()->error(),
        ]);
    }
}
