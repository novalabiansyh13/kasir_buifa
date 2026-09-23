<?php

namespace Tests\Unit;

use App\Models\Msmenu;
use App\Services\Master\MenuService;
use CodeIgniter\Test\CIUnitTestCase;
use DomainException;

/**
 * @internal
 */
final class MenuServiceTest extends CIUnitTestCase
{
    public function testStoreThrowsExceptionWhenNameOrUrlEmpty(): void
    {
        $service = new MenuService();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Nama menu dan URL wajib diisi.');
        $service->store([
            'menuname' => '',
            'url'      => '',
        ]);
    }

    public function testUpdateThrowsExceptionWhenIdInvalid(): void
    {
        $service = new MenuService();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Data menu belum lengkap.');
        $service->update(0, [
            'menuname' => 'Master Menu',
            'url'      => 'master/menu',
        ]);
    }

    public function testDeleteThrowsExceptionWhenIdInvalid(): void
    {
        $service = new MenuService();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('ID menu tidak valid.');
        $service->delete(0);
    }

    public function testSaveOrderThrowsExceptionWhenItemsEmpty(): void
    {
        $service = new MenuService();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Struktur urutan menu kosong.');
        $service->saveOrder([]);
    }
}
