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

    public function searchable()
    {
        return [
            null,
            'a.categoryname',
            'a.createddate',
            'a.createdby',
            null,
        ];
    }

    public function getCategory()
    {
        return $this->builder->select('a.*')->orderBy('a.categoryid', 'ASC');
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
        $cari = strtolower(trim($search));
        $x = $this->builder->select('a.categoryid, a.categoryname');
        if ($cari !== '') {
            $x->where("(lower(a.categoryname) like '%" . $cari . "%')", null, false);
        }
        return $x->orderBy('a.categoryid', 'ASC')->get()->getResultArray();
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
        return $this->builder->update($data, ['categoryid' => $id]);
    }

    public function destroy($id)
    {
        return $this->builder->delete(['categoryid' => $id]);
    }
}
