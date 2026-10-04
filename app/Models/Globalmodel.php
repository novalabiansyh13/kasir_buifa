<?php

namespace App\Models;

use CodeIgniter\Model;

class Globalmodel extends Model
{
    protected $DBGroup = 'default';

    public static function validateData(array $tables)
    {
        $db = db_connect('default');
        $parts = [];
        foreach ($tables as $t) {
            $alias = $db->escape($t['alias']);
            $val = $db->escape($t['value']);
            $parts[] = "SELECT {$alias} as alias, {$val}::text as valueid
                        FROM {$t['table']}
                        WHERE {$t['column']} = {$val}";
        }
        return $db->query(implode(' UNION ALL ', $parts))->getResultArray();
    }
}
