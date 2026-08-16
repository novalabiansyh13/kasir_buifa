<?php

namespace App\Models;

use CodeIgniter\Model;

class Msuser extends Model
{
    protected $table = 'msuser as a';
    public function __construct()
    {
        $this->db = db_connect();
        $this->builder = $this->db->table($this->table);
    }

    public function getByUsername($username)
    {
        return $this->builder
            ->select('a.*')
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
            ->select('a.*')
            ->where('a.userid', (int) $userid)
            ->get()
            ->getRowArray();
    }

    public function updateUser($data, $userid)
    {
        if (empty($userid) || !is_numeric($userid)) {
            return false;
        }
        return $this->builder->update($data, ['userid' => (int) $userid]);
    }
}
