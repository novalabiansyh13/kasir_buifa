<?php

namespace Tests\Unit;

use App\Models\BarangModel;
use App\Models\DetailTransaksiModel;
use App\Models\TransaksiModel;
use App\Services\Kasir\KasirService;
use CodeIgniter\Test\CIUnitTestCase;
use DomainException;

/**
 * @internal
 */
final class KasirServiceTest extends CIUnitTestCase
{
    public function testSimpanTransaksiThrowsExceptionWhenItemsEmpty(): void
    {
        $service = new KasirService();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Keranjang belanja masih kosong.');
        $service->simpanTransaksi([]);
    }

    public function testSimpanTransaksiThrowsExceptionWhenNoValidProducts(): void
    {
        $mockBarang = $this->createMock(BarangModel::class);
        $mockBarang->method('getOne')->willReturn(null);

        $service = new KasirService($mockBarang);
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Tidak ada produk valid yang ditemukan di keranjang.');
        $service->simpanTransaksi([['id_barang' => 9999, 'jumlah' => 1]]);
    }

    public function testSimpanTransaksiSuccessWithMocks(): void
    {
        $mockBarang = $this->createMock(BarangModel::class);
        $mockBarang->method('getOne')->with(1)->willReturn([
            'id_barang'  => 1,
            'nama_barang'=> 'Indomie Goreng',
            'harga_jual' => 3500.0,
            'margin'     => 500.0,
        ]);

        $mockTransaksi = $this->createMock(TransaksiModel::class);
        $mockTransaksi->expects($this->once())
            ->method('store')
            ->with([
                'total_bayar'  => 7000.0,
                'total_margin' => 1000.0,
            ]);

        $mockDetail = $this->createMock(DetailTransaksiModel::class);
        $mockDetail->expects($this->once())
            ->method('storeBatch')
            ->with([
                [
                    'id_barang'         => 1,
                    'jumlah'            => 2,
                    'harga_jual_satuan' => 3500.0,
                    'margin_satuan'     => 500.0,
                    'subtotal_harga'    => 7000.0,
                    'subtotal_margin'   => 1000.0,
                    'id_transaksi'      => 42,
                ],
            ]);

        $mockDb = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['transBegin', 'transCommit', 'transRollback', 'insertID'])
            ->getMock();
        $mockDb->expects($this->once())->method('transBegin');
        $mockDb->expects($this->once())->method('transCommit');
        $mockDb->expects($this->once())->method('insertID')->willReturn(42);

        $service = new KasirService($mockBarang, $mockTransaksi, $mockDetail, $mockDb);
        $res = $service->simpanTransaksi([['id_barang' => 1, 'jumlah' => 2]]);

        $this->assertSame(42, $res['id_transaksi']);
        $this->assertSame(7000.0, $res['total_bayar']);
        $this->assertSame(1000.0, $res['total_margin']);
    }
}
