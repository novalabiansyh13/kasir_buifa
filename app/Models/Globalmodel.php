<?php

namespace App\Models;

use CodeIgniter\Model;

class Globalmodel extends Model
{
    // Cek apakah value masih direferensikan oleh tabel lain (UNION query)
    public static function validateData(array $tables)
    {
        $db = db_connect();
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
