<?php

namespace Tests\Unit;

use App\Models\Msuser;
use App\Services\Auth\AuthService;
use CodeIgniter\Test\CIUnitTestCase;
use DomainException;

/**
 * @internal
 */
final class AuthServiceTest extends CIUnitTestCase
{
    public function testAuthenticateThrowsExceptionWhenFieldsEmpty(): void
    {
        $service = new AuthService();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Username dan password wajib diisi.');
        $service->authenticate('', '');
    }

    public function testAuthenticateThrowsExceptionWhenUserNotFound(): void
    {
        $mockModel = $this->createMock(Msuser::class);
        $mockModel->method('getByUsername')->with('nonexistent')->willReturn(null);

        $service = new AuthService($mockModel);
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Username atau password salah.');
        $service->authenticate('nonexistent', 'secret');
    }

    public function testAuthenticateThrowsExceptionWhenUserIsInactive(): void
    {
        $mockModel = $this->createMock(Msuser::class);
        $mockModel->method('getByUsername')->with('inactiveuser')->willReturn([
            'userid'    => 5,
            'username'  => 'inactiveuser',
            'password'  => password_hash('password123', PASSWORD_DEFAULT),
            'is_active' => false,
        ]);

        $service = new AuthService($mockModel);
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Akun Anda dinonaktifkan. Silakan hubungi Administrator.');
        $service->authenticate('inactiveuser', 'password123');
    }

    public function testAuthenticateThrowsExceptionWhenPasswordMismatch(): void
    {
        $mockModel = $this->createMock(Msuser::class);
        $mockModel->method('getByUsername')->with('activeuser')->willReturn([
            'userid'    => 2,
            'username'  => 'activeuser',
            'password'  => password_hash('correctpass', PASSWORD_DEFAULT),
            'is_active' => true,
        ]);

        $service = new AuthService($mockModel);
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Username atau password salah.');
        $service->authenticate('activeuser', 'wrongpass');
    }

    public function testAuthenticateReturnsUserDataOnSuccess(): void
    {
        $userData = [
            'userid'    => 2,
            'username'  => 'kasir1',
            'fullname'  => 'Kasir Satu',
            'roleid'    => 2,
            'rolename'  => 'Kasir',
            'photo'     => 'avatar.png',
            'password'  => password_hash('rahasia123', PASSWORD_DEFAULT),
            'is_active' => true,
        ];

        $mockModel = $this->createMock(Msuser::class);
        $mockModel->method('getByUsername')->with('kasir1')->willReturn($userData);

        $service = new AuthService($mockModel);
        $result = $service->authenticate('kasir1', 'rahasia123');

        $this->assertSame($userData['userid'], $result['userid']);
        $this->assertSame($userData['username'], $result['username']);
        $this->assertSame($userData['fullname'], $result['fullname']);
    }
}
