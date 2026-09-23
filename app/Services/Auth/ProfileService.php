<?php

namespace App\Services\Auth;

use App\Models\Msuser;
use DomainException;
use Exception;

class ProfileService
{
    protected $msuser;
    protected $db;

    public function __construct(?Msuser $msuser = null)
    {
        $this->msuser = $msuser ?? new Msuser();
        $this->db = db_connect();
    }

    /**
     * Memperbarui profil pengguna dengan data yang sudah bersih.
     *
     * @param int $userid
     * @param array $cleanData ['fullname' => string, 'username' => string, 'password' => string]
     * @param mixed $filePhoto UploadedFile atau null
     * @return bool
     * @throws DomainException|Exception
     */
    public function updateProfile(int $userid, array $cleanData, $filePhoto = null): bool
    {
        // Aturan bisnis: username harus unik
        $existing = $this->msuser->getByUsername($cleanData['username']);
        if ($existing && (int) $existing['userid'] !== $userid) {
            throw new DomainException('Username sudah digunakan oleh akun lain.');
        }

        $dataUpdate = [
            'fullname'   => $cleanData['fullname'],
            'username'   => $cleanData['username'],
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if (!empty($cleanData['password'])) {
            $dataUpdate['password'] = password_hash($cleanData['password'], PASSWORD_DEFAULT);
        }

        // Penanganan upload foto profil
        if ($filePhoto && $filePhoto->isValid() && !$filePhoto->hasMoved()) {
            $newName = $filePhoto->getRandomName();
            $uploadPath = FCPATH . 'uploads/profile';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $filePhoto->move($uploadPath, $newName);
            $dataUpdate['photo'] = $newName;
            setSession('photo', $newName);
        }

        $this->db->transBegin();
        try {
            $this->msuser->edit($dataUpdate, $userid);
            $this->db->transCommit();

            setSession('fullname', $cleanData['fullname']);
            setSession('username', $cleanData['username']);
            return true;
        } catch (Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }
}
