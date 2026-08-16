<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\Msuser;

class LoginController extends BaseController
{
    protected $msuser;
    public function __construct()
    {
        $this->msuser = new Msuser();
    }

    public function index()
    {
        return view('auth/v_login', [
            'title' => 'Login - Kasir Pintar Bu Ifa',
        ]);
    }

    public function process()
    {
        $username = trim($this->getPost('username'));
        $password = trim($this->getPost('password'));
        if (empty($username) || empty($password)) {
            return respondAndDie(false, 'Username dan password wajib diisi.');
        }
        $user = $this->msuser->getByUsername($username);
        if (!$user) {
            return respondAndDie(false, 'Username atau password salah.');
        }
        $passwordValid = password_verify($password, $user['password']) || ($password === $user['password']);
        if (!$passwordValid) {
            return respondAndDie(false, 'Username atau password salah.');
        }
        // Set Session login
        setSession('userid', $user['userid']);
        setSession('username', $user['username']);
        setSession('fullname', $user['fullname']);
        setSession('roleid', $user['roleid'] ?? 1);
        setSession('role', $user['rolename'] ?? $user['role'] ?? 'Administrator');
        setSession('photo', $user['photo'] ?? '');
        return respondAndDie(true, 'Login berhasil! Pengalihan halaman...');
    }

    public function logout()
    {
        destroySession();
        return redirect()->to(base_url('login'));
    }
}
