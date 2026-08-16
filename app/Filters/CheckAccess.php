<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CheckAccess implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper(['primary']);
        if (!isLoggedIn()) {
            if ($request->isAJAX()) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'msg' => 'Sesi Anda telah berakhir, silakan login kembali.',
                    'redirect' => base_url('login')
                ]);
                exit;
            }
            return redirect()->to(base_url('login'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // 
    }
}
