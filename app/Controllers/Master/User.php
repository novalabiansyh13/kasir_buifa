<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\Msrole;
use App\Models\Msuser;
use Exception;

class User extends BaseController
{
    function __construct()
    {
        $dataakses = sessionMenu('user');
        $this->setArrayAccess($dataakses);
        $this->user = new Msuser();
        $this->role = new Msrole();
        $this->arrbc = [
            [
                'Master',
                'User',
            ]
        ];
    }

    function index()
    {
        return view('master/user/v_user', [
            'title' => 'Data User Pengguna',
            'breadcrumb' => $this->arrbc,
            'akses' => $this->getArrayAccess(),
            'section' => 'Master User'
        ]);
    }

    public function datatable()
    {
        $this->response->setContentType('application/json');
        $table = Datatables::method([Msuser::class, 'getUsers'], 'searchable')
            ->make();
        $table->updateRow(function ($db, $no) {
            $idEnc = encrypting($db->userid);
            $btn_role = "<button type='button' onclick=\"openModal('Setting Role User - " . esc($db->username) . "', '" . getURL('user/role/' . $idEnc) . "', {}, 'max-w-sm')\" class='btn btn-soft-primary w-8 h-8 p-0 rounded-lg text-sm inline-flex items-center justify-center me-1.5 shadow-xs' title='Ubah Role User'><i class='bi bi-person-gear'></i></button>";
            $btn_edit = "<button type='button' onclick=\"openModal('Edit User', '" . getURL('user/form/' . $idEnc) . "', {}, 'max-w-lg')\" class='btn btn-soft-warning w-8 h-8 p-0 rounded-lg text-sm inline-flex items-center justify-center me-1.5 shadow-xs' title='Edit User'><i class='bi bi-pencil-square'></i></button>";
            $btn_hapus = "<button type='button' class='btn btn-soft-danger w-8 h-8 p-0 rounded-lg text-sm inline-flex items-center justify-center shadow-xs cursor-pointer' onclick=\"modalDelete('Hapus User - " . esc($db->username) . "', {'link':'" . getURL('user/delete') . "', 'id':'" . $idEnc . "', 'pagetype':'table'})\" title='Hapus User'><i class='bi bi-trash3-fill'></i></button>";
            $photoUrl = !empty($db->photo) && file_exists(FCPATH . 'uploads/profile/' . $db->photo)
                ? base_url('public/uploads/profile/' . $db->photo)
                : base_url('public/images/default-avatar.png');
            $avatarHtml = "<div class='w-8 h-8 rounded-full overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shrink-0 inline-flex items-center justify-center shadow-2xs'>
                <img src='{$photoUrl}' alt='" . esc($db->username) . "' class='w-full h-full object-cover'>
            </div>";
            $roleName = !empty($db->rolename) ? esc($db->rolename) : 'Kasir';
            $roleBadge = "<span class='badge " . ((int)$db->roleid === 1 ? 'badge-soft-primary' : 'badge-soft-secondary') . " font-bold'>" . $roleName . "</span>";
            $statusBadge = $db->is_active
                ? "<span class='badge badge-soft-success font-bold'>Aktif</span>"
                : "<span class='badge badge-soft-danger font-bold'>Nonaktif</span>";
            return [
                $no,
                $avatarHtml,
                "<span class='font-mono font-bold text-slate-900 dark:text-white'>" . esc($db->username) . "</span>",
                "<span class='font-medium text-slate-800 dark:text-slate-200'>" . esc($db->fullname) . "</span>",
                $roleBadge,
                $statusBadge,
                $btn_role . " " . $btn_edit . " " . $btn_hapus
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
            $row = $this->user->getOne($id);
        }
        $roles = $this->role->getAll();
        return view('master/user/v_form', [
            'form_type' => $form_type,
            'row' => $row,
            'roles' => $roles
        ]);
    }

    function addUser()
    {
        $username = trim($this->getPost('username') ?? '');
        $fullname = trim($this->getPost('fullname') ?? '');
        $password = trim($this->getPost('password') ?? '');
        $roleid = $this->getPost('roleid') ?? 2;
        if (!empty($roleid) && !is_numeric($roleid)) {
            $roleid = (int) decrypting($roleid);
        } else {
            $roleid = (int) $roleid;
        }
        $is_active = $this->getPost('is_active') ? true : false;
        $res = [];
        $this->response->setContentType('application/json');
        $this->db->transBegin();
        try {
            if (empty($username) || empty($fullname) || empty($password)) {
                throw new Exception("Username, nama lengkap, dan password wajib diisi.");
            }
            $existing = $this->user->getByUsername($username);
            if (!empty($existing)) {
                throw new Exception("Username sudah digunakan, silakan pilih username lain.");
            }
            $data = [
                'username' => $username,
                'fullname' => $fullname,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'roleid' => (int) $roleid,
                'is_active' => $is_active,
            ];

            $filePhoto = $this->request->getFile('photo');
            if ($filePhoto && $filePhoto->isValid() && !$filePhoto->hasMoved()) {
                $newName = $filePhoto->getRandomName();
                $filePhoto->move(FCPATH . 'uploads/profile', $newName);
                $data['photo'] = $newName;
            }

            $this->user->store($data);
            $res = [
                'sukses' => '1',
                'pesan' => 'User baru berhasil ditambahkan.',
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

    function updateUser()
    {
        $id = decrypting($this->getPost('id'));
        $fullname = trim($this->getPost('fullname') ?? '');
        $password = trim($this->getPost('password') ?? '');
        $roleid = $this->getPost('roleid') ?? 2;
        if (!empty($roleid) && !is_numeric($roleid)) {
            $roleid = (int) decrypting($roleid);
        } else {
            $roleid = (int) $roleid;
        }
        $is_active = $this->getPost('is_active') ? true : false;

        $res = [];
        $this->response->setContentType('application/json');
        $this->db->transBegin();
        try {
            if (empty($id) || empty($fullname)) {
                throw new Exception("Data user belum lengkap.");
            }

            $data = [
                'fullname' => $fullname,
                'roleid' => (int) $roleid,
                'is_active' => $is_active,
            ];

            if (!empty($password)) {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            // Handle photo upload
            $filePhoto = $this->request->getFile('photo');
            if ($filePhoto && $filePhoto->isValid() && !$filePhoto->hasMoved()) {
                $newName = $filePhoto->getRandomName();
                $filePhoto->move(FCPATH . 'uploads/profile', $newName);
                $data['photo'] = $newName;
            }

            $this->user->edit($data, $id);
            $res = [
                'sukses' => '1',
                'pesan' => 'Data user berhasil diperbarui.',
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

    function deleteUser()
    {
        $id = decrypting($this->getPost('id'));
        $res = [];
        $this->response->setContentType('application/json');
        $this->db->transBegin();
        try {
            if (empty($id)) {
                throw new Exception("ID user tidak valid.");
            }
            if ((int)$id === 1) {
                throw new Exception("User Administrator utama (ID 1) tidak boleh dihapus.");
            }
            $currentUserId = getSession('userid');
            if ((int)$id === (int)$currentUserId) {
                throw new Exception("Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.");
            }
            $this->user->destroy($id);
            $res['sukses'] = '1';
            $res['pesan'] = 'User berhasil dihapus.';
            $this->db->transCommit();
        } catch (Exception $e) {
            $res = [
                'sukses' => '0',
                'pesan' => (!empty($e->getMessage())) ? $e->getMessage() : "User gagal dihapus."
            ];
            $this->db->transRollback();
        }
        $this->db->transComplete();
        $res['csrfToken'] = csrf_hash();
        echo json_encode($res);
    }

    public function formRole($id = "")
    {
        if (empty($id)) {
            return "ID User tidak valid.";
        }
        $idDec = decrypting($id);
        $user = $this->user->getOne($idDec);
        if (!$user) {
            return "Data user tidak ditemukan.";
        }
        $roles = $this->role->getAll();
        return view('master/user/v_form_role', [
            'idEnc' => $id,
            'row' => $user,
            'roles' => $roles
        ]);
    }

    public function saveRole()
    {
        $id = decrypting($this->getPost('id'));
        $roleid = $this->getPost('roleid');
        if (!empty($roleid) && !is_numeric($roleid)) {
            $roleid = (int) decrypting($roleid);
        } else {
            $roleid = (int) $roleid;
        }
        $res = [];
        $this->response->setContentType('application/json');
        $this->db->transBegin();
        try {
            if (empty($id) || empty($roleid)) {
                throw new Exception("Data pengguna atau role tidak valid.");
            }
            $this->user->setRole($id, $roleid);
            $res = [
                'sukses' => '1',
                'pesan' => 'Role user berhasil diperbarui.',
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
}
