<?php

namespace Tests\Unit;

use App\Models\Msrole;
use App\Services\Master\UsergroupService;
use CodeIgniter\Test\CIUnitTestCase;
use DomainException;

/**
 * @internal
 */
final class UsergroupServiceTest extends CIUnitTestCase
{
    public function testStoreThrowsExceptionWhenRolenameEmpty(): void
    {
        $service = new UsergroupService();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Nama role/user group tidak boleh kosong.');
        $service->store(['rolename' => '']);
    }

    public function testDeleteThrowsExceptionWhenDeletingAdminRole(): void
    {
        $service = new UsergroupService();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('User Group Administrator utama tidak boleh dihapus.');
        $service->delete(1);
    }

    public function testDeleteThrowsExceptionWhenIdInvalid(): void
    {
        $service = new UsergroupService();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('ID User Group tidak valid.');
        $service->delete(0);
    }
}
