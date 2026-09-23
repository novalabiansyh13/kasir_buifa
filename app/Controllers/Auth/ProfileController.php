<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Services\Auth\ProfileService;
use Exception;

class ProfileController extends BaseController
{
    protected $profileService;

    public function __construct()
    {
        $this->profileService = new ProfileService();
    }

    public function update()
    {
        $userid = (int) getSession('userid');
        if (empty($userid)) {
            return respondAndDie(false, 'Sesi tidak valid.');
        }

        // Tangkap dan bersihkan input
        $fullname = trim((string) $this->getPost('fullname'));
        $username = trim((string) $this->getPost('username'));
        $password = (string) $this->getPost('password');

        // Validasi kelayakan format input di controller
        if (empty($fullname) || empty($username)) {
            return respondAndDie(false, 'Nama lengkap dan username tidak boleh kosong.');
        }

        $cleanData = [
            'fullname' => $fullname,
            'username' => $username,
            'password' => $password,
        ];

        $filePhoto = $this->request->getFile('photo');

        try {
            $this->profileService->updateProfile($userid, $cleanData, $filePhoto);
            return respondAndDie(true, 'Profil berhasil diperbarui!');
        } catch (Exception $e) {
            return respondAndDie(false, $e->getMessage());
        }
    }
}

