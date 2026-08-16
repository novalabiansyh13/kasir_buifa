<?php

namespace App\Models;

use CodeIgniter\Model;

class Msmenu extends Model
{
    protected $table = 'msmenu as a';

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
            'a.menuname',
            'a.url',
            'a.icon',
            'p.menuname',
            'a.sequence',
            'a.is_active',
            null,
        ];
    }

    public function getMenus()
    {
        return $this->builder
            ->select('a.*, p.menuname as parent_name')
            ->join('msmenu as p', 'p.menuid = a.parentid', 'left')
            ->orderBy('a.sequence', 'ASC');
    }

    public function getOne($id = '')
    {
        return $this->builder
            ->select('a.*, p.menuname as parent_name')
            ->join('msmenu as p', 'p.menuid = a.parentid', 'left')
            ->where('a.menuid', (int) $id)
            ->get()
            ->getRowArray();
    }

    public function getMenusByRole($roleid)
    {
        if (empty($roleid) || !is_numeric($roleid)) {
            return $this->getAllMenus();
        }
        return $this->builder
            ->select('a.*')
            ->join('msaccessmenu as b', 'b.menuid = a.menuid')
            ->where('b.roleid', (int) $roleid)
            ->where('a.is_active', true)
            ->orderBy('a.sequence', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getMenusByRoleTree($roleid)
    {
        $menus = $this->getMenusByRole($roleid);
        $tree = [];
        $children = [];
        foreach ($menus as $m) {
            $parentId = (int) $m['parentid'];
            if ($parentId === 0) {
                $tree[$m['menuid']] = $m;
                $tree[$m['menuid']]['children'] = [];
            } else {
                $children[$parentId][] = $m;
            }
        }
        foreach ($children as $parentId => $subItems) {
            if (isset($tree[$parentId])) {
                $tree[$parentId]['children'] = $subItems;
            } else {
                foreach ($subItems as $sub) {
                    $tree[$sub['menuid']] = $sub;
                    $tree[$sub['menuid']]['children'] = [];
                }
            }
        }
        return array_values($tree);
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

    public function getAllMenuTree()
    {
        $all = $this->builder
            ->select('a.*')
            ->orderBy('a.sequence', 'ASC')
            ->get()
            ->getResultArray();

        // Build hierarchy tree
        $tree = [];
        $children = [];
        foreach ($all as $item) {
            $parentId = (int) $item['parentid'];
            if ($parentId === 0) {
                $tree[$item['menuid']] = $item;
                $tree[$item['menuid']]['children'] = [];
            } else {
                $children[$parentId][] = $item;
            }
        }
        foreach ($children as $parentId => $subItems) {
            if (isset($tree[$parentId])) {
                $tree[$parentId]['children'] = $subItems;
            } else {
                // If parent is missing, treat as root
                foreach ($subItems as $sub) {
                    $tree[$sub['menuid']] = $sub;
                    $tree[$sub['menuid']]['children'] = [];
                }
            }
        }
        return array_values($tree);
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
        return $this->builder->update($data, ['menuid' => (int) $id]);
    }

    public function destroy($id)
    {
        return $this->builder->delete(['menuid' => (int) $id]);
    }

    public function saveOrderRecursive(array $items, $parentId = 0, &$sequence = 1)
    {
        $username = getCurrentUsername();
        $now = date('Y-m-d H:i:s');
        foreach ($items as $item) {
            $id = $item['id'] ?? 0;
            if ($id > 0) {
                $this->db->table('msmenu')->where('menuid', $id)->update([
                    'parentid' => (int) $parentId,
                    'sequence' => (int) $sequence,
                    'updatedby' => $username,
                    'updateddate' => $now,
                ]);
                $sequence++;
                if (!empty($item['children']) && is_array($item['children'])) {
                    $this->saveOrderRecursive($item['children'], $id, $sequence);
                }
            }
        }
        return true;
    }
}
