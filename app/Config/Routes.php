<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$this->auth   = ['filter' => 'checkAccess'];
$this->noauth = ['filter' => 'isNotLogin'];
$this->akses  = ['filter' => 'checkAccess'];

$routes->setDefaultNamespace('App\Controllers');
$routes->setTranslateURIDashes(false);
$routes->set404Override();

$routes->get('/', 'Auth\LoginController::index', $this->noauth);
$routes->group('login', function ($routes) {
    $routes->get('/', 'Auth\LoginController::index', $this->noauth);
    $routes->add('process', 'Auth\LoginController::process', $this->noauth);
});
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

// usergroup
$routes->group('usergroup', function ($routes) {
    $routes->add('', 'master\Usergroup::index', $this->akses);
    $routes->add('table', 'master\Usergroup::datatable', $this->akses);
    $routes->add('form', 'master\Usergroup::forms', $this->akses);
    $routes->add('form/(:any)', 'master\Usergroup::forms/$1', $this->akses);
    $routes->add('add', 'master\Usergroup::addRole', $this->akses);
    $routes->add('update', 'master\Usergroup::updateRole', $this->akses);
    $routes->add('delete', 'master\Usergroup::deleteRole', $this->akses);
    $routes->add('access/(:any)', 'master\Usergroup::formAccess/$1', $this->akses);
    $routes->add('saveaccess', 'master\Usergroup::saveAccess', $this->akses);
    $routes->add('getrole', 'master\Usergroup::getRole', $this->auth);
});

// user
$routes->group('user', function ($routes) {
    $routes->add('', 'master\User::index', $this->akses);
    $routes->add('table', 'master\User::datatable', $this->akses);
    $routes->add('form', 'master\User::forms', $this->akses);
    $routes->add('form/(:any)', 'master\User::forms/$1', $this->akses);
    $routes->add('add', 'master\User::addUser', $this->akses);
    $routes->add('update', 'master\User::updateUser', $this->akses);
    $routes->add('delete', 'master\User::deleteUser', $this->akses);
    $routes->add('role/(:any)', 'master\User::formRole/$1', $this->akses);
    $routes->add('saverole', 'master\User::saveRole', $this->akses);
});

// menu
$routes->group('menu', function ($routes) {
    $routes->add('', 'master\Menu::index', $this->akses);
    $routes->add('table', 'master\Menu::datatable', $this->akses);
    $routes->add('form', 'master\Menu::forms', $this->akses);
    $routes->add('form/(:any)', 'master\Menu::forms/$1', $this->akses);
    $routes->add('add', 'master\Menu::addMenu', $this->akses);
    $routes->add('update', 'master\Menu::updateMenu', $this->akses);
    $routes->add('delete', 'master\Menu::deleteMenu', $this->akses);
    $routes->add('sort', 'master\Menu::formSort', $this->akses);
    $routes->add('saveorder', 'master\Menu::saveOrder', $this->akses);
    $routes->add('getmenu', 'master\Menu::getMenu', $this->auth);
});

// kasir
$routes->group('kasir', function ($routes) {
    $routes->add('', 'kasir\Kasir::index', $this->akses);
    $routes->add('table', 'kasir\Riwayat::datatable', $this->akses);
    $routes->add('simpan', 'kasir\Kasir::simpan', $this->akses);
});

// riwayat transaksi
$routes->group('riwayat', function ($routes) {
    $routes->add('', 'kasir\Riwayat::index', $this->akses);
    $routes->add('table', 'kasir\Riwayat::datatable', $this->akses);
    $routes->add('detail/(:any)', 'kasir\Riwayat::detail/$1', $this->akses);
});

// dashboard omzet
$routes->group('omzetboard', function ($routes) {
    $routes->add('', 'Dashboard\OmzetTahun::index', $this->akses);
    $routes->add('getdata', 'Dashboard\OmzetTahun::getData', $this->akses);
});

$routes->add('logout', 'Auth\LoginController::logout', $this->auth);
