<?php

namespace App\Models;

use CodeIgniter\Model;

class Msmenu extends Model
{
    protected $table = 'msmenu as a';
    public function __construct()
    {
        $this->db = db_connect();
        $this->builder = $this->db->table($this->table);
    }

    public function getMenusByUser($userid)
    {
        if (empty($userid) || !is_numeric($userid)) {
            return $this->getAllMenus();
        }
        return $this->builder
            ->select('a.*')
            ->join('msaccessmenu as b', 'b.menuid = a.menuid')
            ->where('b.userid', (int) $userid)
            ->where('a.is_active', true)
            ->orderBy('a.sequence', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getAllMenus()
    {
        return $this->builder
            ->select('a.*')
            ->where('a.is_active', true)
            ->orderBy('a.sequence', 'ASC')
            ->get()
            ->getResultArray();
    }
}
