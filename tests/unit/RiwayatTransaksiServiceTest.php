<?php

namespace Tests\Unit;

use App\Models\TransaksiModel;
use App\Services\Kasir\RiwayatTransaksiService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class RiwayatTransaksiServiceTest extends CIUnitTestCase
{
    public function testSanitizeDateRangeNormal(): void
    {
        $mock = $this->createMock(TransaksiModel::class);
        $service = new RiwayatTransaksiService($mock);
        [$start, $end] = $service->sanitizeDateRange('2026-09-01', '2026-09-05');

        $this->assertSame('2026-09-01', $start);
        $this->assertSame('2026-09-05', $end);
    }

    public function testSanitizeDateRangeReversed(): void
    {
        $mock = $this->createMock(TransaksiModel::class);
        $service = new RiwayatTransaksiService($mock);
        [$start, $end] = $service->sanitizeDateRange('2026-09-10', '2026-09-01');

        $this->assertSame('2026-09-01', $start);
        $this->assertSame('2026-09-10', $end);
    }

    public function testSanitizeDateRangeNullDefaultsToToday(): void
    {
        $mock = $this->createMock(TransaksiModel::class);
        $service = new RiwayatTransaksiService($mock);
        [$start, $end] = $service->sanitizeDateRange(null, null);

        $today = date('Y-m-d');
        $this->assertSame($today, $start);
        $this->assertSame($today, $end);
    }

    public function testGetRingkasanReturnsProperStructure(): void
    {
        $mockModel = $this->createMock(TransaksiModel::class);
        $mockModel->method('getRingkasan')
            ->willReturn([
                'total_penjualan' => '150000.00',
                'total_margin'    => '35000.00',
                'total_transaksi' => '5',
            ]);
        $mockModel->method('getTopProducts')
            ->willReturn([
                ['nama_barang' => 'Minyak Goreng 1L', 'total_qty' => '10'],
                ['nama_barang' => 'Beras 5kg', 'total_qty' => '7'],
                ['nama_barang' => 'Gula Pasir 1kg', 'total_qty' => '4'],
            ]);

        $service = new RiwayatTransaksiService($mockModel);
        $result = $service->getRingkasan('2026-09-01', '2026-09-05');

        $this->assertIsArray($result);
        $this->assertSame(150000.0, $result['total_penjualan']);
        $this->assertSame(35000.0, $result['total_margin']);
        $this->assertSame(5, $result['total_transaksi']);
        $this->assertCount(3, $result['top_products']);
        $this->assertSame('Minyak Goreng 1L', $result['top_products'][0]['nama_barang']);
    }

    public function testGetDetailTransaksiReturnsNullWhenNotFound(): void
    {
        $mockModel = $this->createMock(TransaksiModel::class);
        $mockModel->method('getOne')->with(999)->willReturn(null);

        $service = new RiwayatTransaksiService($mockModel);
        $result = $service->getDetailTransaksi(999);

        $this->assertNull($result);
    }
}
