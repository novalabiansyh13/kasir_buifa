<?php

namespace App\Services\Auth;

use App\Models\Msuser;
use DomainException;

class AuthService
{
    protected ?Msuser $msuser = null;

    public function __construct(?Msuser $msuser = null)
    {
        $this->msuser = $msuser;
    }

    protected function getUserModel(): Msuser
    {
        if ($this->msuser === null) {
            $this->msuser = new Msuser();
        }
        return $this->msuser;
    }

    /**
     * Memvalidasi kredensial login pengguna.
     *
     * @param string $username
     * @param string $password
     * @return array Data pengguna terverifikasi
     * @throws DomainException
     */
    public function authenticate(string $username, string $password): array
    {
        $username = trim($username);
        $password = trim($password);

        if (empty($username) || empty($password)) {
            throw new DomainException('Username dan password wajib diisi.');
        }

        $user = $this->getUserModel()->getByUsername($username);
        if (!$user) {
            throw new DomainException('Username atau password salah.');
        }

        if (isset($user['is_active']) && !$user['is_active']) {
            throw new DomainException('Akun Anda dinonaktifkan. Silakan hubungi Administrator.');
        }

        $passwordValid = password_verify($password, $user['password']) || ($password === $user['password']);
        if (!$passwordValid) {
            throw new DomainException('Username atau password salah.');
        }

        return $user;
    }

    /**
     * Menyimpan data sesi login terenkripsi.
     *
     * @param array $user
     * @return void
     */
    public function loginSession(array $user): void
    {
        setSession('userid', $user['userid']);
        setSession('username', $user['username']);
        setSession('fullname', $user['fullname']);
        setSession('roleid', $user['roleid'] ?? 1);
        setSession('role', $user['rolename'] ?? $user['role'] ?? 'Administrator');
        setSession('photo', $user['photo'] ?? '');
    }

    /**
     * Mengakhiri sesi pengguna.
     *
     * @return void
     */
    public function logout(): void
    {
        destroySession();
    }
}
