<?php

namespace Tests\Unit;

use App\Models\CategoryModel;
use App\Services\Master\CategoryService;
use CodeIgniter\Test\CIUnitTestCase;
use DomainException;

/**
 * @internal
 */
final class CategoryServiceTest extends CIUnitTestCase
{
    public function testStoreThrowsExceptionWhenNameEmpty(): void
    {
        $service = new CategoryService();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Nama kategori tidak boleh kosong.');
        $service->store(['categoryname' => '']);
    }

    public function testUpdateThrowsExceptionWhenIdOrNameInvalid(): void
    {
        $service = new CategoryService();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Data kategori belum lengkap.');
        $service->update(0, ['categoryname' => 'Minuman']);
    }

    public function testDeleteThrowsExceptionWhenIdInvalid(): void
    {
        $service = new CategoryService();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('ID kategori tidak valid.');
        $service->delete(0);
    }
}
