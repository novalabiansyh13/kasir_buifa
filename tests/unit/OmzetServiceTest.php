<?php

namespace Tests\Unit;

use App\Models\DashboardModel;
use App\Services\Dashboard\OmzetService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class OmzetServiceTest extends CIUnitTestCase
{
    public function testGetDashboardDataReturnsAggregatedStructure(): void
    {
        $mockModel = $this->createMock(DashboardModel::class);
        $mockModel->method('getKpi')->with(2026)->willReturn([
            'total_omzet'     => 12000000.0,
            'total_transaksi' => 150,
            'total_margin'    => 3500000.0,
        ]);
        $mockModel->method('getChartMonthly')->with(2026)->willReturn([
            ['bulan' => 1, 'omzet' => 1000000.0, 'margin' => 300000.0],
        ]);
        $mockModel->method('getKlasemenKategori')->with(2026)->willReturn([
            ['categoryname' => 'Makanan', 'total_penjualan' => 8000000.0],
        ]);

        $service = new OmzetService($mockModel);
        $result = $service->getDashboardData(2026);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('kpi', $result);
        $this->assertArrayHasKey('chart', $result);
        $this->assertArrayHasKey('klasemen', $result);
        $this->assertSame(12000000.0, $result['kpi']['total_omzet']);
    }

    public function testGetAvailableYearsReturnsList(): void
    {
        $mockModel = $this->createMock(DashboardModel::class);
        $mockModel->method('getAvailableYears')->willReturn([2024, 2025, 2026]);

        $service = new OmzetService($mockModel);
        $years = $service->getAvailableYears();

        $this->assertSame([2024, 2025, 2026], $years);
    }
}
