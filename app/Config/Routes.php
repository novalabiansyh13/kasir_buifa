<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ── Dashboard / Kasir ────────────────────────────────────────────────────────
$routes->get('/',              'KasirController::index');
$routes->get('/kasir',         'KasirController::index');
$routes->post('/kasir/simpan', 'KasirController::simpan');
$routes->get('/kasir/rekap',   'KasirController::rekap');       // JSON endpoint (AJAX)

// ── Manajemen Barang ──────────────────────────────────────────────────────────
$routes->get('/barang',              'BarangController::index');
$routes->get('/barang/tambah',       'BarangController::tambah');
$routes->post('/barang/simpan',      'BarangController::simpan');
$routes->get('/barang/edit/(:num)',  'BarangController::edit/$1');
$routes->post('/barang/update/(:num)', 'BarangController::update/$1');
$routes->get('/barang/hapus/(:num)', 'BarangController::hapus/$1');

// ── API helpers ───────────────────────────────────────────────────────────────
$routes->get('/api/barang',           'KasirController::apiBarang');      // list barang (JSON)
$routes->get('/api/barang/(:num)',     'KasirController::apiBarangDetail/$1'); // single barang (JSON)
