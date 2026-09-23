<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\Msuser;
use App\Services\Master\UserService;
use App\Services\Master\UsergroupService;
use DomainException;
use Exception;

class User extends BaseController
{
    protected UserService $userService;
    protected UsergroupService $usergroupService;
    protected array $arrbc;

    public function __construct(
        ?UserService $userService = null,
        ?UsergroupService $usergroupService = null
    ) {
        $dataakses = sessionMenu('user');
        $this->setArrayAccess($dataakses);
        $this->userService = $userService ?? new UserService();
        $this->usergroupService = $usergroupService ?? new UsergroupService();
        $this->arrbc = [
            [
                'Master',
                'User',
            ]
        ];
    }

    public function index()
    {
        return view('master/user/v_user', [
            'title'      => 'Data User Pengguna',
            'breadcrumb' => $this->arrbc,
            'akses'      => $this->getArrayAccess(),
            'section'    => 'Master User'
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
        if ($id !== '') {
            $idDec = (int) decrypting($id);
            $row = $this->userService->getOne($idDec) ?? [];
        }
        $roles = $this->usergroupService->getAll();
        return view('master/user/v_form', [
            'form_type' => $form_type,
            'row'       => $row,
            'roles'     => $roles
        ]);
    }

    public function addUser()
    {
        $this->response->setContentType('application/json');
        $username = trim($this->getPost('username') ?? '');
        $fullname = trim($this->getPost('fullname') ?? '');
        $password = trim($this->getPost('password') ?? '');
        $roleid = $this->getPost('roleid') ?? 2;
        if (!empty($roleid) && !is_numeric($roleid)) {
            $roleid = (int) decrypting($roleid);
        } else {
            $roleid = (int) $roleid;
        }
        $isActive = !empty($this->getPost('is_active'));
        $filePhoto = $this->request->getFile('photo');

        try {
            $this->userService->store([
                'username'  => $username,
                'fullname'  => $fullname,
                'password'  => $password,
                'roleid'    => $roleid,
                'is_active' => $isActive,
            ], $filePhoto);

            return $this->response->setJSON([
                'success'   => true,
                'sukses'    => '1',
                'msg'       => 'User baru berhasil ditambahkan.',
                'pesan'     => 'User baru berhasil ditambahkan.',
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
                'msg'       => 'Terjadi kesalahan saat menambahkan user.',
                'pesan'     => 'Terjadi kesalahan saat menambahkan user.',
                'csrfToken' => csrf_hash(),
            ]);
        }
    }

    public function updateUser()
    {
        $this->response->setContentType('application/json');
        $id = (int) decrypting($this->getPost('id'));
        $fullname = trim($this->getPost('fullname') ?? '');
        $password = trim($this->getPost('password') ?? '');
        $roleid = $this->getPost('roleid') ?? 2;
        if (!empty($roleid) && !is_numeric($roleid)) {
            $roleid = (int) decrypting($roleid);
        } else {
            $roleid = (int) $roleid;
        }
        $isActive = !empty($this->getPost('is_active'));
        $filePhoto = $this->request->getFile('photo');

        try {
            $this->userService->update($id, [
                'fullname'  => $fullname,
                'password'  => $password,
                'roleid'    => $roleid,
                'is_active' => $isActive,
            ], $filePhoto);

            return $this->response->setJSON([
                'success'   => true,
                'sukses'    => '1',
                'msg'       => 'Data user berhasil diperbarui.',
                'pesan'     => 'Data user berhasil diperbarui.',
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
                'msg'       => 'Terjadi kesalahan saat memperbarui data user.',
                'pesan'     => 'Terjadi kesalahan saat memperbarui data user.',
                'csrfToken' => csrf_hash(),
            ]);
        }
    }

    public function deleteUser()
    {
        $this->response->setContentType('application/json');
        $id = (int) decrypting($this->getPost('id'));
        $currentUserId = (int) getSession('userid');

        try {
            $this->userService->delete($id, $currentUserId);

            return $this->response->setJSON([
                'success'   => true,
                'sukses'    => '1',
                'msg'       => 'User berhasil dihapus.',
                'pesan'     => 'User berhasil dihapus.',
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
                'msg'       => 'User gagal dihapus.',
                'pesan'     => 'User gagal dihapus.',
                'csrfToken' => csrf_hash(),
            ]);
        }
    }

    public function formRole($id = "")
    {
        if (empty($id)) {
            return "ID User tidak valid.";
        }
        $idDec = (int) decrypting($id);
        $user = $this->userService->getOne($idDec);
        if (!$user) {
            return "Data user tidak ditemukan.";
        }
        $roles = $this->usergroupService->getAll();
        return view('master/user/v_form_role', [
            'idEnc' => $id,
            'row'   => $user,
            'roles' => $roles
        ]);
    }

    public function saveRole()
    {
        $this->response->setContentType('application/json');
        $id = (int) decrypting($this->getPost('id'));
        $roleid = $this->getPost('roleid');
        if (!empty($roleid) && !is_numeric($roleid)) {
            $roleid = (int) decrypting($roleid);
        } else {
            $roleid = (int) $roleid;
        }

        try {
            $this->userService->setRole($id, $roleid);

            return $this->response->setJSON([
                'success'   => true,
                'sukses'    => '1',
                'msg'       => 'Role user berhasil diperbarui.',
                'pesan'     => 'Role user berhasil diperbarui.',
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
                'msg'       => 'Gagal memperbarui role user.',
                'pesan'     => 'Gagal memperbarui role user.',
                'csrfToken' => csrf_hash(),
            ]);
        }
    }
}
