<?php

namespace App\Controllers\Kasir;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\DetailTransaksiModel;
use App\Models\TransaksiModel;
use App\Services\Kasir\RiwayatTransaksiService;

class Riwayat extends BaseController
{
    protected RiwayatTransaksiService $service;
    protected TransaksiModel $transaksiModel;
    protected DetailTransaksiModel $detailModel;
    protected array $arrbc;

    public function __construct(?RiwayatTransaksiService $service = null)
    {
        $dataakses = sessionMenu('riwayat');
        $this->setArrayAccess($dataakses);
        $this->service = $service ?? new RiwayatTransaksiService();
        $this->transaksiModel = new TransaksiModel();
        $this->detailModel = new DetailTransaksiModel();
        $this->arrbc = [
            [
                'Transaction',
                'Riwayat Transaksi',
            ]
        ];
    }

    public function index()
    {
        return view('riwayat/v_riwayat', [
            'title'      => 'Riwayat Transaksi &bull; Kasir Pintar',
            'breadcrumb' => $this->arrbc,
            'akses'      => $this->getArrayAccess(),
            'section'    => 'Transaction',
        ]);
    }

    public function datatable()
    {
        $this->response->setContentType('application/json');
        $rawStart = $this->getPost('start_date');
        $rawEnd = $this->getPost('end_date');

        [$startDate, $endDate] = $this->service->sanitizeDateRange($rawStart, $rawEnd);
        $ringkasan = $this->service->getRingkasan($startDate, $endDate);

        $table = Datatables::method([TransaksiModel::class, 'getRekap'], 'searchable')
            ->setParams([$startDate, $endDate])
            ->make();

        $table->updateRow(function ($db, $no) {
            return [
                $no,
                "<span class='px-2 py-0.5 rounded-md font-mono text-[11px] font-bold bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 inline-flex items-center gap-1'><i class='bi bi-calendar3 text-[10px] text-sky-600 dark:text-cyan-400'></i>" . $db->tanggal . "</span>",
                "<span class='px-2 py-0.5 rounded-md font-mono text-[11px] font-bold bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 inline-flex items-center gap-1'><i class='bi bi-clock text-[10px] text-amber-500'></i>" . $db->jam . "</span>",
                "<span class='font-medium text-slate-800 dark:text-slate-200'>" . esc($db->detail_barang) . "</span>",
                "<span class='font-bold font-mono text-sky-600 dark:text-cyan-400'>" . idr($db->total_bayar) . "</span>",
                "<span class='font-bold font-mono text-amber-600 dark:text-amber-400'>" . idr($db->total_margin) . "</span>",
            ];
        });

        $table->toJson([
            'total_penjualan' => $ringkasan['total_penjualan'],
            'total_margin'    => $ringkasan['total_margin'],
            'total_transaksi' => $ringkasan['total_transaksi'],
            'top_products'    => $ringkasan['top_products'] ?? [],
        ]);
    }

    public function detail($id = null)
    {
        $this->response->setContentType('application/json');
        if (empty($id)) {
            return $this->response->setJSON([
                'success'   => false,
                'sukses'    => '0',
                'msg'       => 'ID transaksi tidak valid.',
                'pesan'     => 'ID transaksi tidak valid.',
                'csrfToken' => csrf_hash(),
            ]);
        }

        $idTransaksi = is_numeric($id) ? (int)$id : (int) decrypting($id);
        $data = $this->service->getDetailTransaksi($idTransaksi);
        if (!$data) {
            return $this->response->setJSON([
                'success'   => false,
                'sukses'    => '0',
                'msg'       => 'Data transaksi tidak ditemukan.',
                'pesan'     => 'Data transaksi tidak ditemukan.',
                'csrfToken' => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'success'   => true,
            'sukses'    => '1',
            'msg'       => 'Data transaksi ditemukan',
            'pesan'     => 'Data transaksi ditemukan',
            'transaksi' => $data['transaksi'],
            'items'     => $data['items'],
            'csrfToken' => csrf_hash(),
        ]);
    }
}
