<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Services\Dashboard\OmzetService;

class OmzetTahun extends BaseController
{
    protected OmzetService $omzetService;
    protected array $arrbc;

    public function __construct(?OmzetService $omzetService = null)
    {
        $dataakses = sessionMenu('omzetboard');
        $this->setArrayAccess($dataakses);
        $this->omzetService = $omzetService ?? new OmzetService();
        $this->arrbc = [
            [
                'Dashboard',
                'Omzet Tahunan',
            ]
        ];
    }

    public function index()
    {
        $years = $this->omzetService->getAvailableYears();
        $currentYear = (int) date('Y');

        return view('dashboard/v_omzettahun', [
            'title'       => 'Dashboard Omzet & Klasemen - Kasir Bu Ifa',
            'breadcrumb'  => $this->arrbc,
            'akses'       => $this->getArrayAccess(),
            'section'     => 'Omzetboard',
            'years'       => $years,
            'defaultYear' => $currentYear,
        ]);
    }

    public function getData()
    {
        $this->response->setContentType('application/json');
        $tahun = (int) ($this->getPost('tahun') ?: date('Y'));
        $data = $this->omzetService->getDashboardData($tahun);

        return $this->response->setJSON([
            'success'   => true,
            'sukses'    => '1',
            'msg'       => 'Data dashboard berhasil dimuat.',
            'pesan'     => 'Data dashboard berhasil dimuat.',
            'tahun'     => $data['tahun'],
            'kpi'       => $data['kpi'],
            'chart'     => $data['chart'],
            'klasemen'  => $data['klasemen'],
            'csrfToken' => csrf_hash(),
        ]);
    }
}
