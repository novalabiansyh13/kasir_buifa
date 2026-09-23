<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\Msmenu;
use App\Services\Master\MenuService;
use DomainException;
use Exception;

class Menu extends BaseController
{
    protected MenuService $menuService;
    protected array $arrbc;

    public function __construct(?MenuService $menuService = null)
    {
        $dataakses = sessionMenu('menu');
        $this->setArrayAccess($dataakses);
        $this->menuService = $menuService ?? new MenuService();
        $this->arrbc = [
            [
                'Master',
                'Menu',
            ]
        ];
    }

    public function index()
    {
        return view('master/menu/v_menu', [
            'title'      => 'Data Master Menu',
            'breadcrumb' => $this->arrbc,
            'akses'      => $this->getArrayAccess(),
            'section'    => 'Master Menu'
        ]);
    }

    public function datatable()
    {
        $this->response->setContentType('application/json');
        $table = Datatables::method([Msmenu::class, 'getMenus'], 'searchable')
            ->make();
        $table->updateRow(function ($db, $no) {
            $idEnc = encrypting($db->menuid);
            $btn_edit = "<button type='button' onclick=\"openModal('Edit Menu', '" . getURL('menu/form/' . $idEnc) . "', {}, 'max-w-lg')\" class='btn btn-soft-warning w-8 h-8 p-0 rounded-lg text-sm inline-flex items-center justify-center me-1.5 shadow-xs' title='Edit Menu'><i class='bi bi-pencil-square'></i></button>";
            $btn_hapus = "<button type='button' class='btn btn-soft-danger w-8 h-8 p-0 rounded-lg text-sm inline-flex items-center justify-center shadow-xs cursor-pointer' onclick=\"modalDelete('Hapus Menu - " . esc($db->menuname) . "', {'link':'" . getURL('menu/delete') . "', 'id':'" . $idEnc . "', 'pagetype':'table'})\" title='Hapus Menu'><i class='bi bi-trash3-fill'></i></button>";
            $iconPreview = "<span class='inline-flex items-center gap-1.5 font-mono text-[11px] px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300'>
                <i class='" . esc($db->icon) . " text-sky-600 dark:text-cyan-400'></i> " . esc($db->icon) . "
            </span>";
            $parentName = !empty($db->parent_name) ? esc($db->parent_name) : "<span class='text-slate-400 text-[11px] font-normal'>Root / Utama</span>";
            $statusBadge = $db->is_active
                ? "<span class='badge badge-soft-success font-bold'>Aktif</span>"
                : "<span class='badge badge-soft-danger font-bold'>Nonaktif</span>";
            return [
                $no,
                "<span class='font-bold text-slate-900 dark:text-white'>" . esc($db->menuname) . "</span>",
                "<span class='font-mono text-xs text-sky-700 dark:text-cyan-400 font-bold'>" . esc($db->url) . "</span>",
                $iconPreview,
                $parentName,
                "<span class='font-mono font-bold text-center block'>" . $db->sequence . "</span>",
                $statusBadge,
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
            $row = $this->menuService->getOne($idDec) ?? [];
        }
        return view('master/menu/v_form', [
            'form_type' => $form_type,
            'row'       => $row
        ]);
    }

    public function addMenu()
    {
        $this->response->setContentType('application/json');
        $menuname = trim($this->getPost('menuname') ?? '');
        $url = trim($this->getPost('url') ?? '');
        $icon = trim($this->getPost('icon') ?? 'bi bi-grid');
        $parentid = $this->getPost('parentid') ?? 0;
        if (!empty($parentid) && !is_numeric($parentid)) {
            $parentid = (int) decrypting($parentid);
        } else {
            $parentid = (int) $parentid;
        }
        $isActive = !empty($this->getPost('is_active'));

        try {
            $this->menuService->store([
                'menuname'  => $menuname,
                'url'       => $url,
                'icon'      => $icon,
                'parentid'  => $parentid,
                'is_active' => $isActive,
            ]);

            return $this->response->setJSON([
                'success'   => true,
                'sukses'    => '1',
                'msg'       => 'Menu baru berhasil ditambahkan.',
                'pesan'     => 'Menu baru berhasil ditambahkan.',
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
                'msg'       => 'Terjadi kesalahan saat menambahkan menu.',
                'pesan'     => 'Terjadi kesalahan saat menambahkan menu.',
                'csrfToken' => csrf_hash(),
            ]);
        }
    }

    public function updateMenu()
    {
        $this->response->setContentType('application/json');
        $id = (int) decrypting($this->getPost('id'));
        $menuname = trim($this->getPost('menuname') ?? '');
        $url = trim($this->getPost('url') ?? '');
        $icon = trim($this->getPost('icon') ?? 'bi bi-grid');
        $parentid = $this->getPost('parentid') ?? 0;
        if (!empty($parentid) && !is_numeric($parentid)) {
            $parentid = (int) decrypting($parentid);
        } else {
            $parentid = (int) $parentid;
        }
        $isActive = !empty($this->getPost('is_active'));

        try {
            $this->menuService->update($id, [
                'menuname'  => $menuname,
                'url'       => $url,
                'icon'      => $icon,
                'parentid'  => $parentid,
                'is_active' => $isActive,
            ]);

            return $this->response->setJSON([
                'success'   => true,
                'sukses'    => '1',
                'msg'       => 'Menu berhasil diperbarui.',
                'pesan'     => 'Menu berhasil diperbarui.',
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
                'msg'       => 'Terjadi kesalahan saat memperbarui menu.',
                'pesan'     => 'Terjadi kesalahan saat memperbarui menu.',
                'csrfToken' => csrf_hash(),
            ]);
        }
    }

    public function deleteMenu()
    {
        $this->response->setContentType('application/json');
        $id = (int) decrypting($this->getPost('id'));

        try {
            $this->menuService->delete($id);

            return $this->response->setJSON([
                'success'   => true,
                'sukses'    => '1',
                'msg'       => 'Menu berhasil dihapus.',
                'pesan'     => 'Menu berhasil dihapus.',
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
                'msg'       => 'Menu gagal dihapus.',
                'pesan'     => 'Menu gagal dihapus.',
                'csrfToken' => csrf_hash(),
            ]);
        }
    }

    public function formSort()
    {
        $menuTree = $this->menuService->getAllMenuTree();
        return view('master/menu/v_sort', [
            'menuTree' => $menuTree
        ]);
    }

    public function saveOrder()
    {
        $this->response->setContentType('application/json');
        $orderData = $this->getPost('order');

        if (empty($orderData)) {
            return $this->response->setJSON([
                'success'   => false,
                'sukses'    => '0',
                'msg'       => 'Struktur urutan menu kosong.',
                'pesan'     => 'Struktur urutan menu kosong.',
                'csrfToken' => csrf_hash(),
            ]);
        }

        $items = json_decode($orderData, true);
        if (!is_array($items)) {
            return $this->response->setJSON([
                'success'   => false,
                'sukses'    => '0',
                'msg'       => 'Format data urutan tidak valid.',
                'pesan'     => 'Format data urutan tidak valid.',
                'csrfToken' => csrf_hash(),
            ]);
        }

        try {
            $this->menuService->saveOrder($items);

            return $this->response->setJSON([
                'success'   => true,
                'sukses'    => '1',
                'msg'       => 'Urutan dan hierarki menu berhasil disimpan.',
                'pesan'     => 'Urutan dan hierarki menu berhasil disimpan.',
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
                'msg'       => 'Gagal menyimpan urutan menu.',
                'pesan'     => 'Gagal menyimpan urutan menu.',
                'csrfToken' => csrf_hash(),
            ]);
        }
    }

    public function getMenu($stmt = '')
    {
        $this->response->setContentType('application/json');
        $search = $this->getPost('searchTerm') ?? '';
        $exceptId = $this->getPost('exceptId') ?? '';
        $excIdInt = null;
        if (!empty($exceptId)) {
            $excIdInt = !is_numeric($exceptId) ? (int) decrypting($exceptId) : (int) $exceptId;
        }

        $get = $this->menuService->getSelectOptions($search, $excIdInt);

        $arr = [];
        $arr[] = [
            'id'   => (empty($stmt) ? encrypting(0) : 0),
            'text' => ''
        ];
        foreach ($get as $g) {
            $arr[] = [
                'id'   => (empty($stmt) ? encrypting($g['menuid']) : $g['menuid']),
                'text' => $g['menuname'] . ' (' . $g['url'] . ')'
            ];
        }

        return $this->response->setJSON([
            'data'      => $arr,
            'csrfToken' => csrf_hash(),
        ]);
    }
}
