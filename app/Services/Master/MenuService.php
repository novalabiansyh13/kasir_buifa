<?php

namespace App\Services\Master;

use App\Models\Msmenu;
use DomainException;
use Exception;

class MenuService
{
    protected ?Msmenu $msmenu = null;
    protected $db = null;

    public function __construct(?Msmenu $msmenu = null, $db = null)
    {
        $this->msmenu = $msmenu;
        $this->db = $db;
    }

    protected function getMenuModel(): Msmenu
    {
        if ($this->msmenu === null) {
            $this->msmenu = new Msmenu();
        }
        return $this->msmenu;
    }

    protected function getDb()
    {
        if ($this->db === null) {
            $this->db = db_connect();
        }
        return $this->db;
    }

    /**
     * Menyimpan menu baru beserta hak akses role Administrator.
     *
     * @param array $cleanData
     * @return int
     * @throws DomainException|Exception
     */
    public function store(array $cleanData): int
    {
        $menuname = trim($cleanData['menuname'] ?? '');
        $url = trim($cleanData['url'] ?? '');
        $icon = trim($cleanData['icon'] ?? 'bi bi-grid');
        $parentid = (int) ($cleanData['parentid'] ?? 0);
        $isActive = !empty($cleanData['is_active']);

        if (empty($menuname) || empty($url)) {
            throw new DomainException('Nama menu dan URL wajib diisi.');
        }

        $menuModel = $this->getMenuModel();
        $maxSeq = $menuModel->builder->selectMax('sequence')->get()->getRowArray();
        $nextSeq = ((int) ($maxSeq['sequence'] ?? 0)) + 1;

        $data = [
            'menuname'  => $menuname,
            'url'       => $url,
            'icon'      => $icon,
            'parentid'  => $parentid,
            'sequence'  => $nextSeq,
            'is_active' => $isActive,
        ];

        $db = $this->getDb();
        $db->transBegin();
        try {
            $menuModel->store($data);
            $newMenuId = (int) $db->insertID();

            $db->table('msaccessmenu')->insert([
                'roleid'      => 1,
                'menuid'      => $newMenuId,
                'createdby'   => getCurrentUsername(),
                'createddate' => date('Y-m-d H:i:s'),
            ]);

            $db->transCommit();
            return $newMenuId;
        } catch (Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Memperbarui data menu.
     *
     * @param int $id
     * @param array $cleanData
     * @return bool
     * @throws DomainException|Exception
     */
    public function update(int $id, array $cleanData): bool
    {
        $menuname = trim($cleanData['menuname'] ?? '');
        $url = trim($cleanData['url'] ?? '');
        $icon = trim($cleanData['icon'] ?? 'bi bi-grid');
        $parentid = (int) ($cleanData['parentid'] ?? 0);
        $isActive = !empty($cleanData['is_active']);

        if ($id <= 0 || empty($menuname) || empty($url)) {
            throw new DomainException('Data menu belum lengkap.');
        }

        $data = [
            'menuname'  => $menuname,
            'url'       => $url,
            'icon'      => $icon,
            'parentid'  => $parentid,
            'is_active' => $isActive,
        ];

        $db = $this->getDb();
        $db->transBegin();
        try {
            $this->getMenuModel()->edit($data, $id);
            $db->transCommit();
            return true;
        } catch (Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Menghapus menu beserta hak akses yang tertaut.
     *
     * @param int $id
     * @return bool
     * @throws DomainException|Exception
     */
    public function delete(int $id): bool
    {
        if ($id <= 0) {
            throw new DomainException('ID menu tidak valid.');
        }

        $db = $this->getDb();
        $db->transBegin();
        try {
            $this->getMenuModel()->destroy($id);
            $db->table('msaccessmenu')->where('menuid', $id)->delete();
            $db->transCommit();
            return true;
        } catch (Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Menyimpan hierarki dan urutan menu secara rekursif.
     *
     * @param array $items
     * @return bool
     * @throws DomainException|Exception
     */
    public function saveOrder(array $items): bool
    {
        if (empty($items)) {
            throw new DomainException('Struktur urutan menu kosong.');
        }

        $db = $this->getDb();
        $db->transBegin();
        try {
            $seq = 1;
            $this->getMenuModel()->saveOrderRecursive($items, 0, $seq);
            $db->transCommit();
            return true;
        } catch (Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    public function getOne(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }
        return $this->getMenuModel()->getOne($id);
    }

    public function getAllMenuTree(): array
    {
        return $this->getMenuModel()->getAllMenuTree();
    }

    public function getSelectOptions(?string $search = '', ?int $exceptId = null): array
    {
        $builder = $this->getMenuModel()->builder->select('a.menuid, a.menuname, a.url, a.icon');
        if ($exceptId !== null && $exceptId > 0) {
            $builder->where('a.menuid !=', $exceptId);
        }
        $cari = strtolower(trim($search ?? ''));
        if ($cari !== '') {
            $builder->where("(lower(a.menuname) like '%" . $cari . "%' or lower(a.url) like '%" . $cari . "%')", null, false);
        }
        return $builder->orderBy('a.sequence', 'ASC')->get()->getResultArray();
    }
}
