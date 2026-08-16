<?php

use CodeIgniter\Encryption\Encryption;

if (!function_exists('getURL')) {
    function getURL($param = '')
    {
        return base_url($param);
    }
}

if (!function_exists('encode')) {
    function encode($data)
    {
        return json_encode($data);
    }
}

// ─── Enkripsi / Decrypt ID ───────────────────────────────────────────────────

if (!function_exists('encrypting')) {
    function encrypting($teks = '')
    {
        if ($teks == '') {
            return '';
        }
        $encrypter = service('encrypter');
        return base64_encode($encrypter->encrypt($teks));
    }
}

if (!function_exists('decrypting')) {
    function decrypting($teks = '')
    {
        if ($teks == '') {
            return '';
        }
        try {
            $encrypter = service('encrypter');
            return $encrypter->decrypt(base64_decode($teks));
        } catch (\Throwable $e) {
            return '';
        }
    }
}

// ─── Token CSRF (base64 6x) ──────────────────────────────────────────────────

if (!function_exists('base_encode')) {
    function base_encode($teks = '')
    {
        for ($i = 0; $i < 6; $i++) {
            $teks = base64_encode($teks);
        }
        return $teks;
    }
}

if (!function_exists('base_decode')) {
    function base_decode($teks = '')
    {
        for ($i = 0; $i < 6; $i++) {
            $teks = base64_decode($teks);
        }
        return $teks;
    }
}

// ─── Format ──────────────────────────────────────────────────────────────────

if (!function_exists('formatDate')) {
    function formatDate($format, $date = '')
    {
        if (empty($date)) {
            $date = date('Y-m-d H:i:s');
        }
        $dt = new DateTime($date);
        return $dt->format($format);
    }
}

if (!function_exists('formatNumber')) {
    function formatNumber($number, $decimal = 0)
    {
        return number_format((float) $number, $decimal, ',', '.');
    }
}

if (!function_exists('idr')) {
    function idr($number)
    {
        return 'Rp ' . number_format((float) $number, 0, ',', '.');
    }
}

if (!function_exists('idrHTML')) {
    function idrHTML($number)
    {
        return '<span>' . idr($number) . '</span>';
    }
}

// ─── Response ────────────────────────────────────────────────────────────────

if (!function_exists('respondAndDie')) {
    function respondAndDie($status, $msg)
    {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $status,
            'msg' => $msg,
            'csrfToken' => csrf_hash(),
        ]);
        die;
    }
}

// ─── Session & Akses (tanpa auth) ────────────────────────────────────────────

if (!function_exists('getAllAccess')) {
    function getAllAccess()
    {
        return [
            'COMPO_1' => true,
            'COMPO_2' => true,
            'COMPO_3' => true,
            'COMPO_4' => true,
            'COMPO_5' => true,
            'COMPO_6' => true,
            'COMPO_7' => true,
        ];
    }
}

if (!function_exists('sessionMenu')) {
    function sessionMenu($link = '')
    {
        return getAllAccess();
    }
}

// ─── Validasi Hapus (referensi FK) ───────────────────────────────────────────

if (!function_exists('validateDeleteData')) {
    function validateDeleteData($tables)
    {
        return \App\Models\Globalmodel::validateData($tables);
    }
}
