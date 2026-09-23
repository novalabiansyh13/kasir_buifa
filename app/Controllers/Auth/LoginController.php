<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Services\Auth\AuthService;
use DomainException;
use Exception;

class LoginController extends BaseController
{
    protected AuthService $authService;

    public function __construct(?AuthService $authService = null)
    {
        $this->authService = $authService ?? new AuthService();
    }

    public function index()
    {
        return view('auth/v_login', [
            'title' => 'Login - Kasir Pintar Bu Ifa',
        ]);
    }

    public function process()
    {
        $username = trim($this->getPost('username') ?? '');
        $password = trim($this->getPost('password') ?? '');

        try {
            $user = $this->authService->authenticate($username, $password);
            $this->authService->loginSession($user);

            return $this->response->setJSON([
                'success'   => true,
                'sukses'    => '1',
                'msg'       => 'Login berhasil! Pengalihan halaman...',
                'pesan'     => 'Login berhasil! Pengalihan halaman...',
                'redirect'  => base_url('kasir'),
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
                'msg'       => 'Terjadi kesalahan sistem saat proses login.',
                'pesan'     => 'Terjadi kesalahan sistem saat proses login.',
                'csrfToken' => csrf_hash(),
            ]);
        }
    }

    public function logout()
    {
        $this->authService->logout();
        return redirect()->to(base_url('login'));
    }
}
