<?php

namespace App\Services\Dashboard;

use App\Models\DashboardModel;

class OmzetService
{
    protected ?DashboardModel $dashboardModel = null;

    public function __construct(?DashboardModel $dashboardModel = null)
    {
        $this->dashboardModel = $dashboardModel;
    }

    protected function getDashboardModel(): DashboardModel
    {
        if ($this->dashboardModel === null) {
            $this->dashboardModel = new DashboardModel();
        }
        return $this->dashboardModel;
    }

    /**
     * Mengambil daftar tahun transaksi yang tersedia.
     *
     * @return array
     */
    public function getAvailableYears(): array
    {
        return $this->getDashboardModel()->getAvailableYears();
    }

    /**
     * Mengambil agregasi KPI tahunan, grafik tren bulanan, dan klasemen kategori.
     *
     * @param int|null $year
     * @return array ['kpi' => array, 'chart' => array, 'klasemen' => array, 'tahun' => int]
     */
    public function getDashboardData(?int $year = null): array
    {
        $targetYear = ($year !== null && $year > 0) ? $year : (int) date('Y');
        $model = $this->getDashboardModel();

        return [
            'tahun'    => $targetYear,
            'kpi'      => $model->getKpi($targetYear),
            'chart'    => $model->getChartMonthly($targetYear),
            'klasemen' => $model->getKlasemenKategori($targetYear),
        ];
    }
}
