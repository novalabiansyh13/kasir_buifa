<?php

namespace App\Services\Master;

use App\Models\Msuser;
use DomainException;
use Exception;

class UserService
{
    protected ?Msuser $msuser = null;
    protected $db = null;

    public function __construct(?Msuser $msuser = null, $db = null)
    {
        $this->msuser = $msuser;
        $this->db = $db;
    }

    protected function getUserModel(): Msuser
    {
        if ($this->msuser === null) {
            $this->msuser = new Msuser();
        }
        return $this->msuser;
    }

    protected function getDb()
    {
        if ($this->db === null) {
            $this->db = db_connect();
        }
        return $this->db;
    }

    /**
     * Menyimpan user baru.
     *
     * @param array $cleanData
     * @param mixed $filePhoto
     * @return int
     * @throws DomainException|Exception
     */
    public function store(array $cleanData, $filePhoto = null): int
    {
        $username = trim($cleanData['username'] ?? '');
        $fullname = trim($cleanData['fullname'] ?? '');
        $password = trim($cleanData['password'] ?? '');
        $roleid = (int) ($cleanData['roleid'] ?? 2);
        $isActive = !empty($cleanData['is_active']);

        if (empty($username) || empty($fullname) || empty($password)) {
            throw new DomainException('Username, nama lengkap, dan password wajib diisi.');
        }

        $userModel = $this->getUserModel();
        $existing = $userModel->getByUsername($username);
        if (!empty($existing)) {
            throw new DomainException('Username sudah digunakan, silakan pilih username lain.');
        }

        $data = [
            'username'  => $username,
            'fullname'  => $fullname,
            'password'  => password_hash($password, PASSWORD_DEFAULT),
            'roleid'    => $roleid,
            'is_active' => $isActive,
        ];

        if ($filePhoto && $filePhoto->isValid() && !$filePhoto->hasMoved()) {
            $newName = $filePhoto->getRandomName();
            $uploadPath = FCPATH . 'uploads/profile';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $filePhoto->move($uploadPath, $newName);
            $data['photo'] = $newName;
        }

        $db = $this->getDb();
        $db->transBegin();
        try {
            $userModel->store($data);
            $newId = (int) $db->insertID();
            $db->transCommit();
            return $newId;
        } catch (Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Memperbarui data pengguna.
     *
     * @param int $id
     * @param array $cleanData
     * @param mixed $filePhoto
     * @return bool
     * @throws DomainException|Exception
     */
    public function update(int $id, array $cleanData, $filePhoto = null): bool
    {
        $fullname = trim($cleanData['fullname'] ?? '');
        $roleid = (int) ($cleanData['roleid'] ?? 2);
        $isActive = !empty($cleanData['is_active']);
        $password = trim($cleanData['password'] ?? '');

        if ($id <= 0 || empty($fullname)) {
            throw new DomainException('Data user belum lengkap.');
        }

        $data = [
            'fullname'  => $fullname,
            'roleid'    => $roleid,
            'is_active' => $isActive,
        ];

        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($filePhoto && $filePhoto->isValid() && !$filePhoto->hasMoved()) {
            $newName = $filePhoto->getRandomName();
            $uploadPath = FCPATH . 'uploads/profile';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $filePhoto->move($uploadPath, $newName);
            $data['photo'] = $newName;
        }

        $db = $this->getDb();
        $db->transBegin();
        try {
            $this->getUserModel()->edit($data, $id);
            $db->transCommit();
            return true;
        } catch (Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Menghapus user dengan validasi proteksi akun admin utama dan akun aktif sendiri.
     *
     * @param int $id
     * @param int $currentUserId
     * @return bool
     * @throws DomainException|Exception
     */
    public function delete(int $id, int $currentUserId): bool
    {
        if ($id <= 0) {
            throw new DomainException('ID user tidak valid.');
        }

        if ($id === 1) {
            throw new DomainException('User Administrator utama (ID 1) tidak boleh dihapus.');
        }

        if ($id === $currentUserId) {
            throw new DomainException('Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        $db = $this->getDb();
        $db->transBegin();
        try {
            $this->getUserModel()->destroy($id);
            $db->transCommit();
            return true;
        } catch (Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Memperbarui role pengguna.
     *
     * @param int $id
     * @param int $roleId
     * @return bool
     * @throws DomainException|Exception
     */
    public function setRole(int $id, int $roleId): bool
    {
        if ($id <= 0 || $roleId <= 0) {
            throw new DomainException('Data pengguna atau role tidak valid.');
        }

        $db = $this->getDb();
        $db->transBegin();
        try {
            $this->getUserModel()->setRole($id, $roleId);
            $db->transCommit();
            return true;
        } catch (Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Mengambil satu data pengguna.
     */
    public function getOne(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }
        return $this->getUserModel()->getOne($id);
    }
}
