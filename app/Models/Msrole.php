<?php

namespace App\Models;

use CodeIgniter\Model;

class Msrole extends Model
{
    protected $table = 'msrole as a';
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
            'a.rolename',
            'a.createddate',
            'a.createdby',
            null,
        ];
    }

    public function getRoles()
    {
        return $this->builder->select('a.*')->orderBy('a.roleid', 'ASC');
    }

    public function getAll()
    {
        return $this->builder->select('a.*')->orderBy('a.roleid', 'ASC')->get()->getResultArray();
    }

    public function getOne($id = '')
    {
        return $this->builder->select('a.*')->where('a.roleid', $id)->get()->getRowArray();
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
        return $this->builder->update($data, ['roleid' => $id]);
    }

    public function destroy($id)
    {
        return $this->builder->delete(['roleid' => $id]);
    }

    public function getAccessMenu($roleid)
    {
        return $this->db->table('msaccessmenu')
            ->select('menuid')
            ->where('roleid', (int) $roleid)
            ->get()
            ->getResultArray();
    }

    public function saveAccessMenu($roleid, array $menuIds)
    {
        $this->db->table('msaccessmenu')->where('roleid', (int) $roleid)->delete();
        $username = getCurrentUsername();
        $rows = [];
        foreach ($menuIds as $mid) {
            $mid = is_numeric($mid) ? (int)$mid : (int)decrypting($mid);
            if ($mid > 0) {
                $rows[] = [
                    'roleid' => (int) $roleid,
                    'menuid' => $mid,
                    'createdby' => $username,
                    'createddate' => date('Y-m-d H:i:s'),
                ];
            }
        }
        if (!empty($rows)) {
            $this->db->table('msaccessmenu')->insertBatch($rows);
        }
        return true;
    }
}
