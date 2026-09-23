<?php

namespace Tests\Unit;

use App\Models\Msuser;
use App\Services\Master\UserService;
use CodeIgniter\Test\CIUnitTestCase;
use DomainException;

/**
 * @internal
 */
final class UserServiceTest extends CIUnitTestCase
{
    public function testStoreThrowsExceptionWhenFieldsEmpty(): void
    {
        $service = new UserService();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Username, nama lengkap, dan password wajib diisi.');
        $service->store([
            'username' => '',
            'fullname' => '',
            'password' => '',
        ]);
    }

    public function testStoreThrowsExceptionWhenUsernameAlreadyTaken(): void
    {
        $mockModel = $this->createMock(Msuser::class);
        $mockModel->method('getByUsername')->with('admin')->willReturn(['userid' => 1, 'username' => 'admin']);

        $service = new UserService($mockModel);
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Username sudah digunakan, silakan pilih username lain.');
        $service->store([
            'username' => 'admin',
            'fullname' => 'Administrator',
            'password' => 'admin123',
        ]);
    }

    public function testDeleteThrowsExceptionWhenDeletingAdmin(): void
    {
        $service = new UserService();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('User Administrator utama (ID 1) tidak boleh dihapus.');
        $service->delete(1, 2);
    }

    public function testDeleteThrowsExceptionWhenDeletingSelf(): void
    {
        $service = new UserService();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        $service->delete(3, 3);
    }
}
