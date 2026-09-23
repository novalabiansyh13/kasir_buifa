<?php

namespace App\Services\Master;

use App\Models\Msrole;
use DomainException;
use Exception;

class UsergroupService
{
    protected ?Msrole $msrole = null;
    protected $db = null;

    public function __construct(?Msrole $msrole = null, $db = null)
    {
        $this->msrole = $msrole;
        $this->db = $db;
    }

    protected function getRoleModel(): Msrole
    {
        if ($this->msrole === null) {
            $this->msrole = new Msrole();
        }
        return $this->msrole;
    }

    protected function getDb()
    {
        if ($this->db === null) {
            $this->db = db_connect();
        }
        return $this->db;
    }

    /**
     * Menyimpan user group / role baru.
     *
     * @param array $cleanData
     * @return int
     * @throws DomainException|Exception
     */
    public function store(array $cleanData): int
    {
        $rolename = trim($cleanData['rolename'] ?? '');
        if (empty($rolename)) {
            throw new DomainException('Nama role/user group tidak boleh kosong.');
        }

        $db = $this->getDb();
        $db->transBegin();
        try {
            $this->getRoleModel()->store(['rolename' => $rolename]);
            $newId = (int) $db->insertID();
            $db->transCommit();
            return $newId;
        } catch (Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Memperbarui data user group / role.
     *
     * @param int $id
     * @param array $cleanData
     * @return bool
     * @throws DomainException|Exception
     */
    public function update(int $id, array $cleanData): bool
    {
        $rolename = trim($cleanData['rolename'] ?? '');
        if ($id <= 0 || empty($rolename)) {
            throw new DomainException('Data user group belum lengkap.');
        }

        $db = $this->getDb();
        $db->transBegin();
        try {
            $this->getRoleModel()->edit(['rolename' => $rolename], $id);
            $db->transCommit();
            return true;
        } catch (Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Menghapus user group setelah validasi proteksi role admin dan user aktif.
     *
     * @param int $id
     * @return bool
     * @throws DomainException|Exception
     */
    public function delete(int $id): bool
    {
        if ($id <= 0) {
            throw new DomainException('ID User Group tidak valid.');
        }

        if ($id === 1) {
            throw new DomainException('User Group Administrator utama tidak boleh dihapus.');
        }

        $tables = [
            ['table' => 'msuser', 'column' => 'roleid', 'value' => $id, 'alias' => 'User / Pengguna'],
        ];
        $getvalidate = validateDeleteData($tables);
        if (!empty($getvalidate)) {
            $aliases = array_unique(array_column($getvalidate, 'alias'));
            $msg = '<div>User Group tidak dapat dihapus karena masih digunakan oleh:</div>';
            $msg .= "<ul style='margin: 0; padding-left: 20px;'>";
            foreach ($aliases as $alias) {
                $msg .= '<li>' . $alias . '</li>';
            }
            $msg .= '</ul>';
            throw new DomainException($msg);
        }

        $db = $this->getDb();
        $db->transBegin();
        try {
            $this->getRoleModel()->destroy($id);
            $db->table('msaccessmenu')->where('roleid', $id)->delete();
            $db->transCommit();
            return true;
        } catch (Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Menyimpan matriks hak akses menu untuk suatu role.
     *
     * @param int $roleId
     * @param array $menus
     * @return bool
     * @throws DomainException|Exception
     */
    public function saveAccess(int $roleId, array $menus): bool
    {
        if ($roleId <= 0) {
            throw new DomainException('Role ID tidak valid.');
        }

        $db = $this->getDb();
        $db->transBegin();
        try {
            $this->getRoleModel()->saveAccessMenu($roleId, $menus);
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
        return $this->getRoleModel()->getOne($id);
    }

    public function getAll(): array
    {
        return $this->getRoleModel()->getAll();
    }

    public function getAccessMenu(int $roleId): array
    {
        if ($roleId <= 0) {
            return [];
        }
        return $this->getRoleModel()->getAccessMenu($roleId);
    }

    public function getSelectOptions(?string $search = ''): array
    {
        $cari = strtolower(trim($search ?? ''));
        $builder = $this->getRoleModel()->builder->select('a.roleid, a.rolename');
        if ($cari !== '') {
            $builder->where("lower(a.rolename) like '%" . $cari . "%'", null, false);
        }
        return $builder->orderBy('a.roleid', 'ASC')->get()->getResultArray();
    }
}
