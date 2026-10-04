<?php

use Tuupola\Base62;

function getURL($param = "")
{
    return base_url($param);
}

function sessionMenu($link = '')
{
    return getAllAccess();
}

function getSession($key)
{
    return decrypting(session()->get($key . '-hrs-session'));
}

function setSession($key, $value)
{
    return session()->set($key . '-hrs-session', encrypting($value));
}

function removeSession($key)
{
    return session()->remove($key . '-hrs-session');
}

function destroySession()
{
    return session()->destroy();
}

function isLoggedIn()
{
    $userid = getSession('userid');
    return !empty($userid);
}

function getUserPhoto()
{
    $photo = getSession('photo');
    if (!empty($photo) && file_exists(FCPATH . 'uploads/profile/' . $photo)) {
        return base_url('public/uploads/profile/' . $photo);
    }
    return base_url('public/images/default-avatar.png');
}

function encode($val)
{
    return json_encode($val);
}

function decode($val)
{
    return json_decode($val);
}

function encrypting($teks = '')
{
    if ($teks == '') {
        return '';
    }
    $enkripsi = \Config\Services::encrypter();
    $base62 = new Base62;
    try {
        $result = $base62->encode($enkripsi->encrypt("$teks"));
    } catch (Exception $e) {
        $result = $teks;
    }
    return $result;
}

function decrypting($teks = '')
{
    if ($teks == '' || $teks === null) {
        return '';
    }
    if (is_numeric($teks)) {
        return $teks;
    }
    $enkripsi = \Config\Services::encrypter();
    $base62 = new Base62;
    try {
        return $enkripsi->decrypt($base62->decode("$teks"));
    } catch (\Throwable $e) {
        $decodedBase64 = base64_decode(strtr($teks, '-_', '+/'));
        if ($decodedBase64 !== false) {
            return $enkripsi->decrypt($decodedBase64);
        }
        return $teks;
    }
}

function base_encode($text)
{
    $txt = $text;
    for ($n = 0; $n < 6; $n++) {
        $txt = base64_encode($txt);
    }
    return $txt;
}

function base_decode($text)
{
    $txt = $text;
    for ($n = 0; $n < 6; $n++) {
        $txt = base64_decode($txt);
    }
    return $txt;
}

function formatDate($format, $date = '')
{
    if (empty($date)) {
        $date = date('Y-m-d H:i:s');
    }
    $dt = new DateTime($date);
    return $dt->format($format);
}

function formatNumber($nomor, $decimal_separator = '.', $thousand_separator = ',', $decimal = 0)
{
    $exp_no = explode($decimal_separator, $nomor);
    $text = number_format($exp_no[0], $decimal, $decimal_separator, $thousand_separator);
    if (count($exp_no) > 1) {
        if ($exp_no[1] > 0) {
            $trims = rtrim($exp_no[1], 0);
            $text .= $decimal_separator . $trims;
        }
    }
    return $text;
}

function idr($number)
{
    return 'Rp ' . number_format((float) $number, 0, ',', '.');
}

function idrHTML($number)
{
    return '<span>' . idr($number) . '</span>';
}

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

// Akses Component
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

function getCurrentUsername()
{
    $u = getSession('username');
    return !empty($u) ? $u : 'system';
}

// Dynamic Sidebar Menu Generator
function generateSidebarMenus()
{
    $roleid = getSession('roleid');
    $menuModel = new \App\Models\Msmenu();
    if (empty($roleid) || !is_numeric($roleid)) {
        $userid = getSession('userid');
        if (!empty($userid) && is_numeric($userid)) {
            $userModel = new \App\Models\Msuser();
            $u = $userModel->getOne($userid);
            $roleid = $u['roleid'] ?? 1;
        } else {
            return $menuModel->getAllMenus();
        }
    }
    return $menuModel->getMenusByRoleTree((int) $roleid);
}

function validateDeleteData(array $tables)
{
    $global = new \App\Models\Globalmodel();
    return $global->validateData($tables);
}

function validateData(array $tables)
{
    $global = new \App\Models\Globalmodel();
    return $global->validateData($tables);
}

if (!function_exists('getProductImage')) {
    function getProductImage(?string $foto, ?int $categoryid = null): string
    {
        if (!empty($foto)) {
            $filePath = FCPATH . 'uploads/produk/' . $foto;
            if (file_exists($filePath)) {
                return base_url('public/uploads/produk/' . $foto);
            }
        }

        $categoryMap = [
            1  => 'cat-sembako',
            2  => 'cat-snack',
            3  => 'cat-alat-tulis',
            4  => 'cat-bumbu-dapur',
            5  => 'cat-minuman',
            6  => 'cat-makanan',
            7  => 'cat-obat',
            8  => 'cat-perawatan',
            9  => 'cat-rumah-tangga',
            10 => 'cat-mainan',
        ];

        if ($categoryid !== null && isset($categoryMap[$categoryid])) {
            $catBase = $categoryMap[$categoryid];
            $svgPath = FCPATH . 'images/categories/' . $catBase . '.svg';
            if (file_exists($svgPath)) {
                return base_url('public/images/categories/' . $catBase . '.svg');
            }
        }

        return base_url('public/images/default-product.svg');
    }
}

if (!function_exists('formatRupiah')) {
    function formatRupiah($nominal = 0): string
    {
        return idr($nominal);
    }
}
