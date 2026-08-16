<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'mscategory as a';
    public function __construct()
    {
        parent::__construct();
        $this->db = db_connect();
        $this->builder = $this->db->table($this->table);
    }

    public function getAll()
    {
        return $this->builder->select('a.*')->orderBy('a.categoryid', 'ASC')->get()->getResultArray();
    }

    public function getOne($id = '')
    {
        return $this->builder->select('a.*')->where('a.categoryid', $id)->get()->getRowArray();
    }

    public function getSelect($search = '')
    {
        $cari = strtolower($search);
        return $this->builder
            ->select('a.categoryid, a.categoryname')
            ->where("(lower(a.categoryname) like '%" . $cari . "%')", null, false)
            ->orderBy('a.categoryid', 'ASC')
            ->get()
            ->getResultArray();
    }
}
