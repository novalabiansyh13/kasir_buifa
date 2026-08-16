<?php

namespace App\Controllers;

use App\Models\BarangModel;

class BarangController extends BaseController
{
    protected BarangModel $barangModel;

    public function __construct()
    {
        $this->barangModel = new BarangModel();
    }

    // ─── Daftar Barang ────────────────────────────────────────────────────────

    public function index(): string
    {
        return view('barang/index', [
            'title'  => 'Data Barang',
            'barang' => $this->barangModel->getAll(),
        ]);
    }

    // ─── Form Tambah Barang ───────────────────────────────────────────────────

    public function tambah(): string
    {
        return view('barang/form', [
            'title'  => 'Tambah Barang',
            'barang' => null,
        ]);
    }

    // ─── Simpan Barang Baru (POST) ────────────────────────────────────────────

    public function simpan()
    {
        $rules = [
            'nama_barang' => 'required|min_length[2]|max_length[100]',
            'harga_beli'  => 'required|numeric|greater_than[0]',
            'harga_jual'  => 'required|numeric|greater_than[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama_barang' => $this->request->getPost('nama_barang'),
            'harga_beli'  => (float) $this->request->getPost('harga_beli'),
            'harga_jual'  => (float) $this->request->getPost('harga_jual'),
        ];

        $this->barangModel->hitungMargin($data);
        $this->barangModel->insert($data);

        return redirect()->to('/barang')->with('success', 'Barang berhasil ditambahkan!');
    }

    // ─── Form Edit Barang ─────────────────────────────────────────────────────

    public function edit(int $id)
    {
        $barang = $this->barangModel->find($id);
        if (! $barang) {
            return redirect()->to('/barang')->with('error', 'Barang tidak ditemukan.');
        }

        return view('barang/form', [
            'title'  => 'Edit Barang',
            'barang' => $barang,
        ]);
    }

    // ─── Update Barang (POST) ─────────────────────────────────────────────────

    public function update(int $id)
    {
        $barang = $this->barangModel->find($id);
        if (! $barang) {
            return redirect()->to('/barang')->with('error', 'Barang tidak ditemukan.');
        }

        $rules = [
            'nama_barang' => 'required|min_length[2]|max_length[100]',
            'harga_beli'  => 'required|numeric|greater_than[0]',
            'harga_jual'  => 'required|numeric|greater_than[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama_barang' => $this->request->getPost('nama_barang'),
            'harga_beli'  => (float) $this->request->getPost('harga_beli'),
            'harga_jual'  => (float) $this->request->getPost('harga_jual'),
        ];

        $this->barangModel->hitungMargin($data);
        $this->barangModel->update($id, $data);

        return redirect()->to('/barang')->with('success', 'Barang berhasil diperbarui!');
    }

    // ─── Hapus Barang ─────────────────────────────────────────────────────────

    public function hapus(int $id)
    {
        $barang = $this->barangModel->find($id);
        if (! $barang) {
            return redirect()->to('/barang')->with('error', 'Barang tidak ditemukan.');
        }

        $this->barangModel->delete($id);
        return redirect()->to('/barang')->with('success', 'Barang berhasil dihapus.');
    }
}
