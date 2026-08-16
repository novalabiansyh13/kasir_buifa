<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\Msuser;

class ProfileController extends BaseController
{
    protected $msuser;

    public function __construct()
    {
        $this->msuser = new Msuser();
    }

    public function update()
    {
        $userid = getSession('userid');
        if (empty($userid)) {
            return respondAndDie(false, 'Sesi tidak valid.');
        }
        $fullname = trim($this->getPost('fullname'));
        $username = trim($this->getPost('username'));
        $password = trim($this->getPost('password'));
        if (empty($fullname) || empty($username)) {
            return respondAndDie(false, 'Nama lengkap dan username tidak boleh kosong.');
        }
        // Cek username unik
        $existing = $this->msuser->getByUsername($username);
        if ($existing && $existing['userid'] != $userid) {
            return respondAndDie(false, 'Username sudah digunakan oleh akun lain.');
        }
        $dataUpdate = [
            'fullname'   => $fullname,
            'username'   => $username,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        if (!empty($password)) {
            $dataUpdate['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $filePhoto = $this->request->getFile('photo');
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
        $this->msuser->updateUser($dataUpdate, $userid);
        setSession('fullname', $fullname);
        setSession('username', $username);
        return respondAndDie(true, 'Profil berhasil diperbarui!');
    }
}
