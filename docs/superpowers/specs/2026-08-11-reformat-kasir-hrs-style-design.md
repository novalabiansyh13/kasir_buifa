# Desain: Reformat Kode & Server-Side Datatable/Select2 Sesuai Gaya HRS

**Tanggal:** 2026-08-11
**Project:** kasir-bu-ifa (CodeIgniter 4.7, PHP 8.2, PostgreSQL)
**Acuan:** `docs/HRS-DEV-GUIDE.md`

## 1. Tujuan

Merombak struktur dan gaya kode project `kasir-bu-ifa` agar konsisten dengan panduan
HRS (`docs/HRS-DEV-GUIDE.md`), **tanpa mengubah tampilan web** (desain hijau Bootstrap
saat ini dipertahankan), dengan perubahan fungsional:

1. Semua tabel berbasis DB diubah menjadi **DataTables server-side** (mesin
   `app/Helpers/Datatables/`).
2. Dropdown diubah menjadi **Select2** (AJAX) memakai fungsi global `generateSelect2`.
3. Fungsi global `generateSelect2` dan **datatable default** (`generateDatatable`)
   didefinisikan di `app/Views/template/v_footer.php`.

## 2. Keputusan Lingkup (hasil klarifikasi)

- **Minimal tanpa auth**: tidak ada filter login, `sessionMenu()`, dan pengecekan akses
  nyata. `sessionMenu()` mengembalikan akses all-true agar pola view tetap mengikuti guide
  (`$akses["COMPO_" . COMADD]` dll). Konstanta `COMVIEW..COMSPECIAL` tetap didefinisikan.
- **Asset lokal**: jQuery, DataTables, Select2 diunduh ke `public/js` & `public/css`.
  Bootstrap 5 tetap via CDN (tidak mengubah tampilan).
- **CSRF pola enkripsi HRS penuh**: `base_encode/base_decode` (base64 6x),
  `editor.js` (`encrypter/decrypter`), hidden `#csrf_token` di `v_footer`, token baru
  di-update dari tiap response JSON. Filter CSRF CI4 diaktifkan global.
- **Mesin Datatables**: memakai file asli yang sudah ditempel di
  `app/Helpers/Datatables/`. Helper lain (`primary_helper.php`, `Globalmodel.php`)
  dibuat sendiri menyesuaikan project ini.
- **Pendekatan**: Struktur HRS penuh (template include, subfolder controller,
  route group, helper auto-load) tanpa auth.

## 3. Struktur File

```
app/
├── Config/
│   ├── Autoload.php        → $helpers = ['primary_helper']
│   ├── Constants.php       → + COMVIEW=1, COMADD=2, COMEDIT=3, COMDELETE=4,
│   │                          COMUPLOAD=5, COMDOWNLOAD=6, COMSPECIAL=7
│   ├── Encryption.php      → set $key (hex 32 byte) untuk encrypting/decrypting
│   ├── Filters.php         → aktifkan filter global 'csrf' (before)
│   └── Routes.php          → $this->akses=[], $this->auth=[];
│                             group('barang') + group('kasir')
├── Controllers/
│   ├── BaseController.php  → initController set $this->db;
│   │                          getPost/getGet; setArrayAccess/getArrayAccess
│   ├── Home.php            → dibiarkan
│   ├── Master/Barang.php   → namespace App\Controllers\Master
│   └── Kasir/Kasir.php     → namespace App\Controllers\Kasir
├── Models/
│   ├── BarangModel.php     → pola builder + searchable()
│   ├── TransaksiModel.php  → getRekap() (builder subquery) + getRingkasanHariIni()
│   ├── DetailTransaksiModel.php
│   └── Globalmodel.php     → validateData() untuk validateDeleteData()
├── Helpers/
│   ├── primary_helper.php  → auto-load global
│   └── Datatables/         → (sudah ada, tidak diubah)
└── Views/
    ├── template/           → v_header, v_import, v_sidebar, v_navbar, v_appbar,
    │                          v_footer
    ├── master/barang/      → v_barang.php, v_form.php
    └── kasir/              → v_kasir.php
public/
├── js/    → jquery.min.js, jquery.dataTables.min.js, select2.min.js, editor.js
└── css/   → dataTables.dataTables.min.css, select2.min.css
```

File lama yang diganti/dihapus: `app/Controllers/BarangController.php`,
`app/Controllers/KasirController.php`, `app/Views/barang/index.php`,
`app/Views/barang/form.php`, `app/Views/kasir/index.php`, `app/Views/layout/main.php`.

## 4. Template Global

Isi `app/Views/layout/main.php` didistribusikan ke template (tampilan sama):

- **`v_import.php`** — semua link CSS & JS global: Bootstrap CDN (css+js),
  `css/dataTables.dataTables.min.css`, `css/select2.min.css`,
  `js/jquery.min.js`, `js/jquery.dataTables.min.js`, `js/select2.min.js`,
  `js/editor.js`.
- **`v_header.php`** — DOCTYPE, `<head>` (include `v_import`), buka `<body>`,
  include `v_navbar` + `v_sidebar`, buka `<div class="container-fluid">` +
  `<div class="row">` + kolom konten.
- **`v_navbar.php`** — navbar atas hijau (brand, toggler, menu Kasir/Barang).
- **`v_sidebar.php`** — sidebar desktop (`col-md-2`, menu Kasir/Dashboard +
  Manajemen Barang, active state).
- **`v_appbar.php`** — bar atas versi mobile (`d-md-none`): judul halaman
  (`$title`/`$section`) + tombol toggle.
- **`v_footer.php`** — tutup container, footer, `<script>` global + hidden
  `#csrf_token` + seluruh fungsi global JS.

Alur include per halaman:

```php
<?= $this->include('template/v_header') ?>
<?= $this->include('template/v_appbar') ?>
<div class="main-content content margin-t-4">…</div>
<?= $this->include('template/v_footer') ?>
```

## 5. `v_footer.php` — Fungsi Global JS

- Hidden token: `<input type="hidden" id="csrf_token" value="<?= base_encode(csrf_hash()) ?>">`.
- `generateSelect2(element, dparent='', link='', placeholder='', width='',
  minimumResultsForSearch=0, allowClear=true, datas={}, ismultiple=false)` — pola guide
  Bab 9: AJAX POST ke endpoint, `data` kirim `searchTerm` + CSRF, `processResults`
  update token & return `{results: data}`.
- `generateDatatable(selector, options={})` — inisialisasi datatable server-side default:
  `serverSide:true`, `destroy:true`, `autoWidth:false`, ajax ke `current_url(true)/table`
  (POST + CSRF), `dataSrc` update `#csrf_token` + baca `tambahan.grandtotal` ke
  `#text-grandtotal`.
- Auto-init: `var tbl = generateDatatable('.table-master')` (dipakai `modalDelete`
  untuk `tbl.ajax.reload()`).
- `showNotif(type,msg)`, `showSuccess(msg)`, `showError(msg)`, `toPage(url,blank)`,
  `close_modal(id)`, `modalDelete(title,datas)` (AJAX POST ke `link` + `id`, reload tbl),
  `modalForm(title,size,link,datas)`, `formatRupiah`, `toNumeric`, `price_keyup`,
  `exp_number`.

## 6. Config

- `Autoload.php`: `public $helpers = ['primary_helper'];`.
- `Constants.php`: tambah konstanta `COMVIEW..COMSPECIAL`.
- `Encryption.php`: set `$key` (hex 32 byte).
- `Filters.php`: globals `before` tambah `'csrf'`.
- `Routes.php`: `$this->auth = []; $this->akses = [];` lalu group route (pola guide,
  tanpa filter):

```php
$routes->group('barang', function ($routes) {
    $routes->add('',              'master\Barang::index',        $this->akses);
    $routes->add('table',         'master\Barang::datatable',    $this->akses);
    $routes->add('form',          'master\Barang::forms',        $this->akses);
    $routes->add('form/(:any)',   'master\Barang::forms/$1',     $this->akses);
    $routes->add('add',           'master\Barang::addBarang',    $this->akses);
    $routes->add('update',        'master\Barang::updateBarang', $this->akses);
    $routes->add('delete',        'master\Barang::deleteBarang', $this->akses);
    $routes->add('getbarang',     'master\Barang::getBarang',    $this->auth);
});
$routes->group('kasir', function ($routes) {
    $routes->add('',              'kasir\Kasir::index',   $this->akses);
    $routes->add('table',         'kasir\Kasir::datatable', $this->akses);
    $routes->add('simpan',        'kasir\Kasir::simpan',  $this->akses);
});
```

## 7. `primary_helper.php` (auto-load)

`getURL`, `base_encode`, `base_decode`, `encrypting`, `decrypting`, `formatDate`,
`formatNumber`, `idr`, `idrHTML`, `respondAndDie`, `sessionMenu`, `getAllAccess`,
`validateDeleteData`.

- `sessionMenu($link)` → tanpa session; return array akses `COMPO_1..COMPO_7` = true.
- `validateDeleteData($tables)` → pakai `Globalmodel::validateData()` (query UNION
  cek referensi FK) seperti guide Bab 10.4.

## 8. BaseController

Pola guide:

```php
abstract class BaseController extends Controller
{
    protected $db;
    protected $akses;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->db = db_connect();
    }

    function getPost($key, $cadangan = '') { … }
    function getGet($key, $cadangan = '') { … }
    function setArrayAccess($dataakses) { $this->akses = $dataakses; }
    function getArrayAccess() { return $this->akses; }
}
```

## 9. Controller

### `Master/Barang.php`

- `__construct()`: `$this->setArrayAccess(sessionMenu('barang')); $this->barang = new BarangModel();`
  + breadcrumb `[['Master','Barang']]`.
- `index()`: render `master/barang/v_barang` dengan `title`, `breadcrumb`, `akses`,
  `section`.
- `datatable()`: `Datatables::method([BarangModel::class,'getBarang'],'searchable')
  ->make()->updateRow(...)->toJson();` — updateRow: No, nama, harga beli (idr),
  harga jual (idr), margin (badge), tombol edit (`toPage`) + hapus (`modalDelete`).
- `forms($id='')`: add/edit, render `master/barang/v_form`.
- `addBarang()` / `updateBarang()`: validasi (throw Exception), transaksi DB,
  response JSON `{pesan, sukses, trace, csrfToken}`.
- `deleteBarang()`: decrypt id, `validateDeleteData` cek `detail_transaksi`, hapus,
  response JSON.
- `getBarang($stmt='')`: endpoint select2 → `{data:[{id:encrypting(id), text, harga, margin}], csrfToken}`.

### `Kasir/Kasir.php`

- `__construct()`: `$this->setArrayAccess(sessionMenu('kasir'));` instansiasi
  `BarangModel`, `TransaksiModel`, `DetailTransaksiModel`.
- `index()`: render `kasir/v_kasir` (title, akses).
- `datatable()`: `Datatables::method([TransaksiModel::class,'getRekap'],'searchable')
  ->make()->updateRow(...)->toJson($ringkasan)` — `$ringkasan` = `getRingkasanHariIni()`
  untuk update kartu summary.
- `simpan()`: logika transaksi (post `items` JSON), transaksi DB, response JSON
  `{sukses, pesan, csrfToken}`.

## 10. Model

### `BarangModel`

- `$table = 'barang as a'`, `$builder` di `__construct`.
- `searchable()`: `[null, 'a.nama_barang', 'a.harga_beli', 'a.harga_jual', 'a.margin', null]`.
- `getBarang()`: return builder (`select a.*, orderBy nama`).
- `getOne($id='')`, `store($data)`, `edit($data,$id)`, `destroy($id)`.
- `getSelect($search='')`: search `lower(nama_barang) like`, limit 15 → array
  `{id_barang, nama_barang, harga_beli, harga_jual, margin}`.

### `TransaksiModel`

- `$table = 'transaksi as a'`.
- `getRekap()`: return builder dari subquery agar alias (`jam`, `detail_barang`)
  dapat di-search/order:

```php
$sub = $this->db->table('transaksi t')
    ->select("t.id_transaksi, TO_CHAR(t.tanggal_transaksi,'HH24:MI') as jam,
             t.total_bayar, t.total_margin,
             COALESCE(STRING_AGG(b.nama_barang || ' x' || dt.jumlah, ', '), '-') as detail_barang")
    ->join('detail_transaksi dt', 'dt.id_transaksi = t.id_transaksi', 'left')
    ->join('barang b', 'b.id_barang = dt.id_barang', 'left')
    ->where("DATE(t.tanggal_transaksi)", 'CURRENT_DATE', false)
    ->groupBy('t.id_transaksi, t.tanggal_transaksi, t.total_bayar, t.total_margin');
return $this->db->table('(' . $sub->getCompiledSelect() . ') as a');
```

- `searchable()`: `[null, 'a.jam', 'a.detail_barang', 'a.total_bayar', 'a.total_margin']`.
- `getRingkasanHariIni()`: SUM total_bayar/total_margin (untuk `tambahan` datatable).
- `store($data)`: insert header.

### `DetailTransaksiModel`

- `$table = 'detail_transaksi as a'`; `storeBatch($rows)` → `insertBatch`.

### `Globalmodel`

- `validateData($tables)` → UNION query cek apakah `value` direferensikan tabel lain,
  return daftar `alias`.

## 11. View

### `master/barang/v_barang.php`

- Include `v_header` + `v_appbar`; tombol "Add New" (jika `$akses["COMPO_" . COMADD]`).
- Tabel `class="table table-bordered table-master fs-7"` kolom: No, Nama Barang,
  Harga Beli, Harga Jual, Margin, Action; `<tbody></tbody>` diisi datatable.
- Include `v_footer`; tidak perlu script khusus (datatable global `.table-master`
  otomatis di-handle `generateDatatable`).

### `master/barang/v_form.php`

- Include `v_header` + `v_appbar`; kartu form (UI sama seperti sekarang).
- Hidden `id` (encrypting saat edit), `#csrf_token_form`.
- Submit via AJAX (pola guide Bab 10): `barang/add` atau `barang/update`,
  update token, `showNotif`, redirect ke list saat sukses.

### `kasir/v_kasir.php`

- Kolom kiri (form input transaksi) tetap: `#selectBarang` memakai
  `generateSelect2('#selectBarang', '', '<?= getURL('barang/getbarang') ?>', 'Pilih barang', '100%', ...)`.
  Data harga/margin dibaca dari `.select2('data')` hasil endpoint (id, text, harga, margin).
  Keranjang client-side (dipindah ke jQuery).
- Kolom kanan (rekap): tabel `class="table table-bordered table-rekap fs-7"` kolom
  No, Jam, Detail Barang, Total Bayar, Margin + tfoot ringkasan. Inisialisasi manual
  `generateDatatable('.table-rekap', {custom dataSrc})` untuk update kartu
  `summaryPenjualan`/`summaryMargin`/`footerPenjualan`/`footerMargin` dari
  `json.tambahan`.
- Tombol refresh → `tbl_rekap.ajax.reload()`.

## 12. Aset

- Unduh ke `public/js`: `jquery.min.js`, `jquery.dataTables.min.js`, `select2.min.js`.
- Unduh ke `public/css`: `dataTables.dataTables.min.css`, `select2.min.css`.
- Buat `public/js/editor.js`: `encrypter(text)` = `btoa` 6x, `decrypter(text)` = `atob` 6x
  (invers dari `base_encode`/`base_decode` PHP).

## 13. Alur Data & Penanganan Error

- **Datatable**: frontend POST `columns/search/order` + CSRF → controller
  `Datatables::method(...)->make()->updateRow(...)->toJson($tambahan)` →
  `{draw, recordsFiltered, recordsTotal, data, csrfToken, tambahan}`.
- **Form/hapus**: validasi `throw new Exception` → `sukses=0` + pesan →
  `showNotif('error', pesan)`; simpan pakai `transBegin/transCommit/transRollback/transComplete`.
- **CSRF**: token `base_encode(csrf_hash())` di `v_footer`; dikirim polos setelah
  `decrypter(...)`; tiap response membawa `csrfToken` baru → di-update via `encrypter(...)`.

## 14. Pengujian

- `spark routes` / buka halaman: `/`, `/barang`, `/barang/form`, `/barang/form/<id>`.
- Barang: datatable muncul (No, Nama, Harga Beli, Harga Jual, Margin, Action);
  add → edit → delete (terblokir jika dipakai transaksi) → search/order/sort/page.
- Kasir: select2 pilih barang (AJAX), tambah keranjang, simpan transaksi, rekap
  datatable muncul + kartu ringkasan ter-update dari `tambahan`.
- Cek token CSRF ter-update pada tiap AJAX (tidak ada 419/CSRF error).
- `php spark` lint manual; tidak ada `console.log` / kode comment-out.

## 15. Di Luar Lingkup

- Login/autentikasi, filter `checkAccess`/`auth`, session multi-company (`companyid`).
- Perubahan tampilan web (desain hijau saat ini dipertahankan).
- Fitur baru di luar datatable/select2/reformat.
