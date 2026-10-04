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

    public function testStoreWithCustomSatuan(): void
    {
        $mockModel = $this->createMock(BarangModel::class);
        $mockModel->expects($this->once())
            ->method('store')
            ->with($this->callback(function ($data) {
                return isset($data['satuan'])
                    && $data['satuan'] === 'renteng'
                    && $data['nama_barang'] === 'Kopi Sachet'
                    && $data['margin'] == 500;
            }))
            ->willReturn(true);

        $mockDb = $this->createMock(\CodeIgniter\Database\BaseConnection::class);
        $mockDb->method('insertID')->willReturn(101);

        $service = new BarangService($mockModel, $mockDb);
        $id = $service->store([
            'categoryid'  => 1,
            'nama_barang' => 'Kopi Sachet',
            'harga_beli'  => 1000,
            'harga_jual'  => 1500,
            'satuan'      => 'renteng',
        ]);
        $this->assertSame(101, $id);
    }

    public function testStoreDefaultsSatuanToPcsWhenEmpty(): void
    {
        $mockModel = $this->createMock(BarangModel::class);
        $mockModel->expects($this->once())
            ->method('store')
            ->with($this->callback(function ($data) {
                return isset($data['satuan']) && $data['satuan'] === 'pcs';
            }))
            ->willReturn(true);

        $mockDb = $this->createMock(\CodeIgniter\Database\BaseConnection::class);
        $mockDb->method('insertID')->willReturn(102);

        $service = new BarangService($mockModel, $mockDb);
        $id = $service->store([
            'categoryid'  => 1,
            'nama_barang' => 'Gula Pasir',
            'harga_beli'  => 12000,
            'harga_jual'  => 14000,
            'satuan'      => '',
        ]);
        $this->assertSame(102, $id);
    }

    public function testDeleteRemovesPhysicalPhotoFileIfPresent(): void
    {
        $photoName = 'test_delete_prod_' . uniqid() . '.webp';
        $photoPath = FCPATH . 'uploads/produk/' . $photoName;
        if (!is_dir(dirname($photoPath))) {
            mkdir(dirname($photoPath), 0755, true);
        }
        file_put_contents($photoPath, 'dummy image data');
        $this->assertFileExists($photoPath);

        $mockModel = $this->createMock(BarangModel::class);
        $mockModel->method('getOne')->willReturn([
            'id_barang'   => 55,
            'nama_barang' => 'Barang Hapus',
            'foto'        => $photoName,
        ]);
        $mockModel->expects($this->once())->method('destroy')->with(55)->willReturn(true);

        $mockDb = $this->createMock(\CodeIgniter\Database\BaseConnection::class);

        $service = new BarangService($mockModel, $mockDb);
        $result = $service->delete(55);

        $this->assertTrue($result);
        $this->assertFileDoesNotExist($photoPath);
    }
}
