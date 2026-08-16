<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$this->auth   = ['filter' => 'checkAccess'];
$this->noauth = ['filter' => 'isNotLogin'];
$this->akses  = ['filter' => 'checkAccess'];

$routes->get('login', 'Auth\LoginController::index', $this->noauth);
$routes->post('login/process', 'Auth\LoginController::process', $this->noauth);
$routes->get('logout', 'Auth\LoginController::logout', $this->auth);
$routes->post('profile/update', 'Auth\ProfileController::update', $this->auth);

//barang
$routes->group('barang', function ($routes) {
    $routes->add('', 'master\Barang::index', $this->akses);
    $routes->add('table', 'master\Barang::datatable', $this->akses);
    $routes->add('form', 'master\Barang::forms', $this->akses);
    $routes->add('form/(:any)', 'master\Barang::forms/$1', $this->akses);
    $routes->add('add', 'master\Barang::addBarang', $this->akses);
    $routes->add('update', 'master\Barang::updateBarang',  $this->akses);
    $routes->add('delete', 'master\Barang::deleteBarang',  $this->akses);
    $routes->add('getbarang', 'master\Barang::getBarang',     $this->auth);
    $routes->add('getcategory', 'master\Barang::getCategory',   $this->auth);
});

// category
$routes->group('category', function ($routes) {
    $routes->add('', 'master\Category::index', $this->akses);
    $routes->add('table', 'master\Category::datatable', $this->akses);
    $routes->add('form', 'master\Category::forms', $this->akses);
    $routes->add('form/(:any)', 'master\Category::forms/$1', $this->akses);
    $routes->add('add', 'master\Category::addCategory', $this->akses);
    $routes->add('update', 'master\Category::updateCategory', $this->akses);
    $routes->add('delete', 'master\Category::deleteCategory', $this->akses);
});

// kasir
$routes->group('kasir', function ($routes) {
    $routes->add('', 'kasir\Kasir::index', $this->akses);
    $routes->add('table', 'kasir\Kasir::datatable', $this->akses);
    $routes->add('simpan', 'kasir\Kasir::simpan', $this->akses);
});
