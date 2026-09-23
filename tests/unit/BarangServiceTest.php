<?php

namespace Tests\Unit;

use App\Models\BarangModel;
use App\Services\Master\BarangService;
use CodeIgniter\Test\CIUnitTestCase;
use DomainException;

/**
 * @internal
 */
final class BarangServiceTest extends CIUnitTestCase
{
    public function testStoreThrowsExceptionWhenFieldsEmpty(): void
    {
        $service = new BarangService();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Nama barang dan harga wajib diisi.');
        $service->store([
            'nama_barang' => '',
            'harga_beli'  => 0,
            'harga_jual'  => 0,
            'categoryid'  => 1,
        ]);
    }

    public function testStoreThrowsExceptionWhenCategoryEmpty(): void
    {
        $service = new BarangService();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Silakan pilih kategori produk.');
        $service->store([
            'nama_barang' => 'Kopi Kapal Api',
            'harga_beli'  => 1500,
            'harga_jual'  => 2000,
            'categoryid'  => 0,
        ]);
    }

    public function testUpdateThrowsExceptionWhenIdInvalid(): void
    {
        $service = new BarangService();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Data barang belum lengkap.');
        $service->update(0, [
            'nama_barang' => 'Kopi',
            'harga_beli'  => 1500,
            'harga_jual'  => 2000,
            'categoryid'  => 1,
        ]);
    }

    public function testDeleteThrowsExceptionWhenIdInvalid(): void
    {
        $service = new BarangService();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('ID barang tidak valid.');
        $service->delete(0);
    }
}
