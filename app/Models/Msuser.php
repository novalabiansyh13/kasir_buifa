<?php

namespace App\Models;

use CodeIgniter\Model;

class Msuser extends Model
{
    protected $table = 'msuser as a';
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
            'a.fullname',
            'a.username',
            'b.rolename',
            'a.is_active',
            null,
        ];
    }

    public function getUsers()
    {
        return $this->builder
            ->select('a.*, b.rolename')
            ->join('msrole as b', 'b.roleid = a.roleid', 'left')
            ->orderBy('a.userid', 'ASC');
    }

    public function getByUsername($username)
    {
        return $this->builder
            ->select('a.*, b.rolename')
            ->join('msrole as b', 'b.roleid = a.roleid', 'left')
            ->where('a.username', $username)
            ->get()
            ->getRowArray();
    }

    public function getOne($userid)
    {
        if (empty($userid) || !is_numeric($userid)) {
            return null;
        }
        return $this->builder
            ->select('a.*, b.rolename')
            ->join('msrole as b', 'b.roleid = a.roleid', 'left')
            ->where('a.userid', (int) $userid)
            ->get()
            ->getRowArray();
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
        return $this->builder->update($data, ['userid' => (int) $id]);
    }

    public function destroy($id)
    {
        return $this->builder->delete(['userid' => (int) $id]);
    }

    public function setRole($userid, $roleid)
    {
        return $this->edit(['roleid' => (int) $roleid], $userid);
    }
}
