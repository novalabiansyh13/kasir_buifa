<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Filter placeholder (project tanpa login; pola tetap mengikuti panduan HRS)
$this->auth   = [];
$this->noauth = [];
$this->akses  = [];

// ── Root → Kasir ─────────────────────────────────────────────────────────────
$routes->get('/', 'kasir\Kasir::index');

// ── Barang (Master) ──────────────────────────────────────────────────────────
$routes->group('barang', function ($routes) {
    $routes->add('',              'master\Barang::index',         $this->akses);
    $routes->add('table',         'master\Barang::datatable',     $this->akses);
    $routes->add('form',          'master\Barang::forms',         $this->akses);
    $routes->add('form/(:any)',   'master\Barang::forms/$1',      $this->akses);
    $routes->add('add',           'master\Barang::addBarang',     $this->akses);
    $routes->add('update',        'master\Barang::updateBarang',  $this->akses);
    $routes->add('delete',        'master\Barang::deleteBarang',  $this->akses);
    $routes->add('getbarang',     'master\Barang::getBarang',     $this->auth);
});

// ── Kasir ────────────────────────────────────────────────────────────────────
$routes->group('kasir', function ($routes) {
    $routes->add('',              'kasir\Kasir::index',           $this->akses);
    $routes->add('table',         'kasir\Kasir::datatable',       $this->akses);
    $routes->add('simpan',        'kasir\Kasir::simpan',          $this->akses);
});
