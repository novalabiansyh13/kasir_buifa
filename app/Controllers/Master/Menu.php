<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\Msmenu;
use Exception;

class Menu extends BaseController
{
    function __construct()
    {
        $dataakses = sessionMenu('menu');
        $this->setArrayAccess($dataakses);
        $this->menu = new Msmenu();
        $this->arrbc = [
            [
                'Master',
                'Menu',
            ]
        ];
    }

    function index()
    {
        return view('master/menu/v_menu', [
            'title' => 'Data Master Menu',
            'breadcrumb' => $this->arrbc,
            'akses' => $this->getArrayAccess(),
            'section' => 'Master Menu'
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
        if ($id != '') {
            $id = decrypting($id);
            $row = $this->menu->getOne($id);
        }
        return view('master/menu/v_form', [
            'form_type' => $form_type,
            'row' => $row
        ]);
    }

    function addMenu()
    {
        $menuname = trim($this->getPost('menuname') ?? '');
        $url = trim($this->getPost('url') ?? '');
        $icon = trim($this->getPost('icon') ?? 'bi bi-grid');
        $parentid = $this->getPost('parentid') ?? 0;
        if (!empty($parentid) && !is_numeric($parentid)) {
            $parentid = (int) decrypting($parentid);
        } else {
            $parentid = (int) $parentid;
        }
        $is_active = $this->getPost('is_active') ? true : false;
        $res = [];
        $this->response->setContentType('application/json');
        $this->db->transBegin();
        try {
            if (empty($menuname) || empty($url)) {
                throw new Exception("Nama menu dan URL wajib diisi.");
            }
            $maxSeq = $this->menu->builder->selectMax('sequence')->get()->getRowArray();
            $nextSeq = ((int) ($maxSeq['sequence'] ?? 0)) + 1;
            $data = [
                'menuname' => $menuname,
                'url' => $url,
                'icon' => $icon,
                'parentid' => $parentid,
                'sequence' => $nextSeq,
                'is_active' => $is_active,
            ];
            $this->menu->store($data);
            $newMenuId = $this->db->insertID();
            $this->db->table('msaccessmenu')->insert([
                'roleid' => 1,
                'menuid' => $newMenuId,
                'createdby' => getCurrentUsername(),
                'createddate' => date('Y-m-d H:i:s')
            ]);

            $res = [
                'sukses' => '1',
                'pesan' => 'Menu baru berhasil ditambahkan.',
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

    function updateMenu()
    {
        $id = decrypting($this->getPost('id'));
        $menuname = trim($this->getPost('menuname') ?? '');
        $url = trim($this->getPost('url') ?? '');
        $icon = trim($this->getPost('icon') ?? 'bi bi-grid');
        $parentid = $this->getPost('parentid') ?? 0;
        if (!empty($parentid) && !is_numeric($parentid)) {
            $parentid = (int) decrypting($parentid);
        } else {
            $parentid = (int) $parentid;
        }
        $is_active = $this->getPost('is_active') ? true : false;
        $res = [];
        $this->response->setContentType('application/json');
        $this->db->transBegin();
        try {
            if (empty($id) || empty($menuname) || empty($url)) {
                throw new Exception("Data menu belum lengkap.");
            }
            $data = [
                'menuname' => $menuname,
                'url' => $url,
                'icon' => $icon,
                'parentid' => $parentid,
                'is_active' => $is_active,
            ];
            $this->menu->edit($data, $id);
            $res = [
                'sukses' => '1',
                'pesan' => 'Menu berhasil diperbarui.',
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

    function deleteMenu()
    {
        $id = decrypting($this->getPost('id'));
        $res = [];
        $this->response->setContentType('application/json');
        $this->db->transBegin();
        try {
            if (empty($id)) {
                throw new Exception("ID menu tidak valid.");
            }
            $this->menu->destroy($id);
            $this->db->table('msaccessmenu')->where('menuid', (int) $id)->delete();
            $res['sukses'] = '1';
            $res['pesan'] = 'Menu berhasil dihapus.';
            $this->db->transCommit();
        } catch (Exception $e) {
            $res = [
                'sukses' => '0',
                'pesan' => (!empty($e->getMessage())) ? $e->getMessage() : "Menu gagal dihapus."
            ];
            $this->db->transRollback();
        }
        $this->db->transComplete();
        $res['csrfToken'] = csrf_hash();
        echo json_encode($res);
    }

    public function formSort()
    {
        $menuTree = $this->menu->getAllMenuTree();
        return view('master/menu/v_sort', [
            'menuTree' => $menuTree
        ]);
    }

    public function saveOrder()
    {
        $orderData = $this->getPost('order');
        $res = [];
        $this->response->setContentType('application/json');
        $this->db->transBegin();
        try {
            if (empty($orderData)) {
                throw new Exception("Struktur urutan menu kosong.");
            }
            $items = json_decode($orderData, true);
            if (!is_array($items)) {
                throw new Exception("Format data urutan tidak valid.");
            }
            $seq = 1;
            $this->menu->saveOrderRecursive($items, 0, $seq);
            $res = [
                'sukses' => '1',
                'pesan' => 'Urutan dan hierarki menu berhasil disimpan.',
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

    public function getMenu($stmt = '')
    {
        $this->response->setContentType('application/json');
        $search = $this->getPost('searchTerm') ?? '';
        $exceptId = $this->getPost('exceptId') ?? '';
        if (!empty($exceptId) && !is_numeric($exceptId)) {
            $exceptId = decrypting($exceptId);
        }

        $builder = $this->menu->builder->select('a.menuid, a.menuname, a.url, a.icon');
        if (!empty($exceptId) && is_numeric($exceptId)) {
            $builder->where('a.menuid !=', (int) $exceptId);
        }
        if (!empty($search)) {
            $cari = strtolower(trim($search));
            $builder->where("(lower(a.menuname) like '%" . $cari . "%' or lower(a.url) like '%" . $cari . "%')", null, false);
        }
        $get = $builder->orderBy('a.sequence', 'ASC')->get()->getResultArray();

        $arr = [];
        $arr[] = [
            'id' => (empty($stmt) ? encrypting(0) : 0),
            'text' => ''
        ];
        foreach ($get as $g) {
            $arr[] = [
                'id' => (empty($stmt) ? encrypting($g['menuid']) : $g['menuid']),
                'text' => $g['menuname'] . ' (' . $g['url'] . ')'
            ];
        }
        echo encode([
            'data' => $arr,
            'csrfToken' => csrf_hash(),
            'trace' => db_connect()->error(),
        ]);
    }
}
