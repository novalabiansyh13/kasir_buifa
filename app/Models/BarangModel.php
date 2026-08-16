<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $table = 'barang as a';
    public function __construct()
    {
        parent::__construct();
        $this->db = db_connect();
        $this->builder = $this->db->table($this->table);
    }

    public function searchable()
    {
        return [
            null,
            'a.nama_barang',
            'b.categoryname',
            'a.harga_beli',
            'a.harga_jual',
            'a.margin',
            null,
        ];
    }

    public function getBarang()
    {
        return $this->builder
            ->select('a.*, b.categoryname')
            ->join('mscategory as b', 'b.categoryid = a.categoryid', 'left');
    }

    public function getOne($id = '')
    {
        $x = $this->builder
            ->select('a.*, b.categoryname')
            ->join('mscategory as b', 'b.categoryid = a.categoryid', 'left');
        if ($id != '') {
            $x->where('a.id_barang', $id);
        }
        return $x->get()->getRowArray();
    }

    public function hitungMargin(array &$data)
    {
        if (isset($data['harga_jual'], $data['harga_beli'])) {
            $data['margin'] = (float) $data['harga_jual'] - (float) $data['harga_beli'];
        }
    }

    public function store($data)
    {
        $username = getCurrentUsername();
        $data['createdby'] = $username;
        $data['createddate'] = date('Y-m-d H:i:s');
        $data['updatedby'] = $username;
        $data['updateddate'] = date('Y-m-d H:i:s');
        return $this->builder->insert($data);
    }

    public function edit($data, $id)
    {
        $data['updatedby'] = getCurrentUsername();
        $data['updateddate'] = date('Y-m-d H:i:s');
        return $this->builder->update($data, ['id_barang' => $id]);
    }

    public function destroy($id)
    {
        return $this->builder->delete(['id_barang' => $id]);
    }

    public function getSelect($search = '', $categoryid = '')
    {
        $cari = strtolower(trim($search));
        $x = $this->builder
            ->select('a.id_barang, a.nama_barang, a.harga_beli, a.harga_jual, a.margin, a.categoryid, b.categoryname')
            ->join('mscategory as b', 'b.categoryid = a.categoryid', 'left');

        if (!empty($categoryid)) {
            $x->where('a.categoryid', $categoryid);
        }
        if ($cari !== '') {
            $x->where("(lower(a.nama_barang) like '%" . $cari . "%' or lower(b.categoryname) like '%" . $cari . "%')", null, false);
        }
        return $x->limit(25)
            ->orderBy('a.nama_barang', 'ASC')
            ->get()
            ->getResultArray();
    }
}
