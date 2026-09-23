<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\Msrole;
use App\Services\Master\MenuService;
use App\Services\Master\UsergroupService;
use DomainException;
use Exception;

class Usergroup extends BaseController
{
    protected UsergroupService $usergroupService;
    protected MenuService $menuService;
    protected array $arrbc;

    public function __construct(
        ?UsergroupService $usergroupService = null,
        ?MenuService $menuService = null
    ) {
        $dataakses = sessionMenu('usergroup');
        $this->setArrayAccess($dataakses);
        $this->usergroupService = $usergroupService ?? new UsergroupService();
        $this->menuService = $menuService ?? new MenuService();
        $this->arrbc = [
            [
                'Master',
                'User Group',
            ]
        ];
    }

    public function index()
    {
        return view('master/usergroup/v_usergroup', [
            'title'      => 'Data User Group & Hak Akses',
            'breadcrumb' => $this->arrbc,
            'akses'      => $this->getArrayAccess(),
            'section'    => 'Master User Group'
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
        if ($id !== '') {
            $idDec = (int) decrypting($id);
            $row = $this->usergroupService->getOne($idDec) ?? [];
        }
        return view('master/usergroup/v_form', [
            'form_type' => $form_type,
            'row'       => $row
        ]);
    }

    public function addRole()
    {
        $this->response->setContentType('application/json');
        $rolename = trim($this->getPost('rolename') ?? '');

        try {
            $this->usergroupService->store(['rolename' => $rolename]);

            return $this->response->setJSON([
                'success'   => true,
                'sukses'    => '1',
                'msg'       => 'User Group baru berhasil ditambahkan.',
                'pesan'     => 'User Group baru berhasil ditambahkan.',
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
                'msg'       => 'Terjadi kesalahan saat menambahkan user group.',
                'pesan'     => 'Terjadi kesalahan saat menambahkan user group.',
                'csrfToken' => csrf_hash(),
            ]);
        }
    }

    public function updateRole()
    {
        $this->response->setContentType('application/json');
        $id = (int) decrypting($this->getPost('id'));
        $rolename = trim($this->getPost('rolename') ?? '');

        try {
            $this->usergroupService->update($id, ['rolename' => $rolename]);

            return $this->response->setJSON([
                'success'   => true,
                'sukses'    => '1',
                'msg'       => 'User Group berhasil diperbarui.',
                'pesan'     => 'User Group berhasil diperbarui.',
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
                'msg'       => 'Terjadi kesalahan saat memperbarui user group.',
                'pesan'     => 'Terjadi kesalahan saat memperbarui user group.',
                'csrfToken' => csrf_hash(),
            ]);
        }
    }

    public function deleteRole()
    {
        $this->response->setContentType('application/json');
        $id = (int) decrypting($this->getPost('id'));

        try {
            $this->usergroupService->delete($id);

            return $this->response->setJSON([
                'success'   => true,
                'sukses'    => '1',
                'msg'       => 'User Group berhasil dihapus.',
                'pesan'     => 'User Group berhasil dihapus.',
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
                'msg'       => 'User Group gagal dihapus.',
                'pesan'     => 'User Group gagal dihapus.',
                'csrfToken' => csrf_hash(),
            ]);
        }
    }

    public function formAccess($roleidEnc = "")
    {
        $roleid = (int) decrypting($roleidEnc);
        $role = $this->usergroupService->getOne($roleid);
        if (empty($role)) {
            echo "<div class='p-4 text-rose-500 text-xs font-bold'>User Group tidak ditemukan.</div>";
            return;
        }

        $allMenuTree = $this->menuService->getAllMenuTree();
        $currentAccess = $this->usergroupService->getAccessMenu($roleid);
        $selectedMenuIds = array_column($currentAccess, 'menuid');

        return view('master/usergroup/v_access', [
            'role'            => $role,
            'roleidEnc'       => $roleidEnc,
            'menuTree'        => $allMenuTree,
            'selectedMenuIds' => $selectedMenuIds
        ]);
    }

    public function saveAccess()
    {
        $this->response->setContentType('application/json');
        $roleid = (int) decrypting($this->getPost('roleid'));
        $menus = $this->getPost('menus') ?? [];

        try {
            $this->usergroupService->saveAccess($roleid, $menus);

            return $this->response->setJSON([
                'success'   => true,
                'sukses'    => '1',
                'msg'       => 'Hak akses menu berhasil disimpan.',
                'pesan'     => 'Hak akses menu berhasil disimpan.',
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
                'msg'       => 'Gagal menyimpan hak akses menu.',
                'pesan'     => 'Gagal menyimpan hak akses menu.',
                'csrfToken' => csrf_hash(),
            ]);
        }
    }

    public function getRole($stmt = '')
    {
        $this->response->setContentType('application/json');
        $search = $this->getPost('searchTerm') ?? '';
        $get = $this->usergroupService->getSelectOptions($search);

        $arr = [];
        foreach ($get as $g) {
            $arr[] = [
                'id'   => (empty($stmt) ? encrypting($g['roleid']) : $g['roleid']),
                'text' => $g['rolename']
            ];
        }

        return $this->response->setJSON([
            'data'      => $arr,
            'csrfToken' => csrf_hash(),
        ]);
    }
}
