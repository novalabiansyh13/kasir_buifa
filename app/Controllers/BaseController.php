<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $db;

    protected $akses;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->db = db_connect();
    }

    function getPost($key, $cadangan = '')
    {
        $post = $this->request->getPost($key);
        $hasil = $cadangan;
        if ($post != null && $post != '') {
            $hasil = $this->request->getPost($key);
        }
        return $hasil;
    }

    function getGet($key, $cadangan = '')
    {
        $get = $this->request->getGet($key);
        $hasil = $cadangan;
        if ($get != null && $get != '') {
            $hasil = $this->request->getGet($key);
        }
        return $hasil;
    }

    function setArrayAccess($dataakses)
    {
        $this->akses = $dataakses;
    }

    function getArrayAccess()
    {
        return $this->akses;
    }
}
