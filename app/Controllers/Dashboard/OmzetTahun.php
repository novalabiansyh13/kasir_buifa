<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\DashboardModel;

class OmzetTahun extends BaseController
{
    protected $dashboardModel;
    protected $arrbc;
    function __construct()
    {
        $dataakses = sessionMenu('omzetboard');
        $this->setArrayAccess($dataakses);
        $this->dashboardModel = new DashboardModel();
        $this->arrbc = [
            [
                'Dashboard',
                'Omzet Tahunan',
            ]
        ];
    }

    function index()
    {
        $years = $this->dashboardModel->getAvailableYears();
        $currentYear = (int) date('Y');
        return view('dashboard/v_omzettahun', [
            'title' => 'Dashboard Omzet & Klasemen - Kasir Bu Ifa',
            'breadcrumb' => $this->arrbc,
            'akses' => $this->getArrayAccess(),
            'section' => 'Omzetboard',
            'years' => $years,
            'defaultYear' => $currentYear,
        ]);
    }

    public function getData()
    {
        $this->response->setContentType('application/json');
        $tahun = (int) ($this->getPost('tahun') ?: date('Y'));
        $kpi = $this->dashboardModel->getKpi($tahun);
        $chart = $this->dashboardModel->getChartMonthly($tahun);
        $klasemen = $this->dashboardModel->getKlasemenKategori($tahun);
        return $this->response->setJSON([
            'sukses' => '1',
            'tahun' => $tahun,
            'kpi' => $kpi,
            'chart' => $chart,
            'klasemen' => $klasemen,
            'csrfToken' => csrf_hash(),
        ]);
    }
}
