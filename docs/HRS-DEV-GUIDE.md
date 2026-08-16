# Panduan Development Project HRS

> Dokumentasi resmi untuk mengembangkan / me-recreate project **HRS** dengan gaya kode
> yang konsisten: **clean, rapi, dan efisien**.
>
> Dokumen ini adalah **spec & panduan gaya** — bukan dokumentasi fitur. Tujuannya agar
> siapapun yang menambah / menulis ulang kode menghasilkan kode yang identik polanya
> dengan codebase HRS saat ini.

---

## Daftar Isi

1. [Pengenalan Project](#1-pengenalan-project)
2. [Struktur Direktori](#2-struktur-direktori)
3. [Arsitektur MVC & Alur Request](#3-arsitektur-mvc--alur-request)
4. [Gaya Kode (Code Style)](#4-gaya-kode-code-style)
5. [Session, Helper & Utility Global](#5-session-helper--utility-global)
6. [Template Global (Layout)](#6-template-global-layout)
7. [Routes](#7-routes)
8. [Datatable](#8-datatable)
9. [Select2 & AJAX Endpoint](#9-select2--ajax-endpoint)
10. [Form & Submit (AJAX + CSRF)](#10-form--submit-ajax--csrf)
11. [Tutorial: Membuat Module Baru](#11-tutorial-membuat-module-baru)
12. [Checklist Kepatuhan (Style Checklist)](#12-checklist-kepatuhan-style-checklist)

---

## 1. Pengenalan Project

### 1.1 Stack Teknologi

| Komponen             | Teknologi                                                                     |
| -------------------- | ----------------------------------------------------------------------------- |
| Backend              | PHP 8.0+ dengan framework **CodeIgniter 4.6.0** (`codeigniter4/framework`)     |
| Database             | **PostgreSQL** (`DBDriver = Postgre`, default db `hrs`)                        |
| Frontend             | jQuery, Bootstrap, BoxIcons, CSS custom                                        |
| Tabel UI             | jQuery DataTables (server-side)                                                |
| Dropdown/Autocomplete| Select2, Typeahead (jQuery)                                                    |
| Realtime / Notif     | Node.js (`server.js`) — Express + Socket.IO + `pg`, via `env('urlNode')`        |
| Export               | PHPSpreadsheet (`phpoffice/phpspreadsheet`)                                    |
| Session              | `session()` bawaan CI4 + pola encrypt/decrypt (lihat [Bab 5](#5-session-helper--utility-global)) |

### 1.2 Tujuan Dokumen

Dokumen ini menjawab pertanyaan: *"Bagaimana cara menulis kode agar sama persis dengan
project HRS?"* — mencakup penamaan, struktur, pola Controller/Model/View, penggunaan
template global, routes, datatable, select2, dan aturan keamanan (akses + CSRF).

---

## 2. Struktur Direktori

### 2.1 Pohon Direktori Utama

```
HRS/
├── app/                          # Seluruh logika aplikasi
│   ├── Config/                   # Konfigurasi framework (Routes, Database, Filters, dll)
│   ├── Controllers/
│   │   ├── BaseController.php    # Base untuk semua controller
│   │   ├── Home.php
│   │   ├── Servers.php
│   │   ├── auth/                 # LoginController, Personal
│   │   ├── master/               # Semua CRUD master data
│   │   ├── approval/             # Approval / Workflow
│   │   ├── ess/                  # Employee Self-Service
│   │   ├── report/               # Laporan & export
│   │   ├── guestbook/            # Guest book
│   │   ├── iclock/               # Integrasi mesin absen (fingerprint)
│   │   └── View/                 # Controller khusus render halaman/dashboard
│   ├── Database/                 # (placeholder migrasi/seed)
│   ├── Filters/                  # CheckAccess, IsLogin, IsNotLogin
│   ├── Helpers/
│   │   ├── primary_helper.php    # ✅ Helper global utama (auto-loaded)
│   │   ├── Approval/             # Helper approval
│   │   ├── Datatables/           # ✅ Mesin server-side datatable
│   │   ├── Leavetime/            # Helper history cuti
│   │   └── Privileges/           # Helper akses user
│   ├── Language/                 # File bahasa framework
│   ├── Libraries/                # Library custom (jika ada)
│   ├── Models/                   # Semua model (satu file per tabel)
│   └── Views/
│       ├── template/             # ✅ Template global (header, footer, dsb)
│       ├── global/               # Partial reusable (approval, export)
│       ├── login/                # Halaman login & forgot password
│       ├── master/<modul>/       # View per modul master
│       ├── ess/<modul>/          # View per modul ESS
│       ├── report/<modul>/       # View per modul report
│       ├── allapproval/          # View approval
│       └── home/                 # View dashboard
├── public/                       # Aset publik (js, css, images)
│   ├── index.php
│   ├── js/  (jquery, dataTables, select2, editor.js, dll)
│   ├── css/ (template.css, mystyle.css, dll)
│   └── ...
├── writable/                     # File upload / cache / logs
├── .env                          # Konfigurasi environment (jangan di-commit)
├── server.js                     # Node.js socket server
├── package.json                  # Dependency Node.js
└── composer.json                 # Dependency PHP
```

### 2.2 Konvensi Penamaan Folder

| Folder        | Konvensi                                                                                 |
| ------------- | ---------------------------------------------------------------------------------------- |
| `Controllers` | Sub-folder **lowercase**: `master/`, `ess/`, `report/`, `auth/`, `approval/`, `guestbook/`, `iclock/`, `View/` |
| `Models`      | **Flat** (tanpa sub-folder), satu file per tabel                                         |
| `Views`       | Sub-folder **lowercase** per modul: `master/department/`                                  |
| `Helpers`     | Sub-folder **StudlyCase**: `Datatables/`, `Approval/`, `Leavetime/`, `Privileges/`        |

### 2.3 Konvensi Penamaan File

| Jenis        | Pola                       | Contoh                                 |
| ------------ | -------------------------- | -------------------------------------- |
| Controller   | `StudlyCase.php`           | `Department.php`, `LoginController.php`|
| Model        | `Ms<Table>` / `<Nama>.php` | `Msdepartment.php`, `Workflowhd.php`   |
| View halaman | `v_<nama>.php`             | `v_department.php`, `v_form.php`       |
| Template     | `v_<nama>.php`             | `v_header.php`, `v_footer.php`         |
| Helper       | `<Kelas>.php`              | `Datatables.php`, `DriversMethod.php`  |

---

## 3. Arsitektur MVC & Alur Request

### 3.1 Alur Request

```
Browser
  └─ GET/POST /department
       ├─ app/Config/Routes.php        → kelompok route + filter
       │     └─ $this->akses (filter checkAccess)
       ├─ app/Filters/CheckAccess.php  → cek session userid (redirect ke login jika kosong)
       ├─ app/Controllers/master/Department.php
       │     └─ index() → view('master/department/v_department', [...])
       ├─ app/Models/Msdepartment.php  → query database (dipanggil saat datatable/simpan)
       ├─ app/Views/template/*.php     → layout global
       └─ app/Views/master/department/v_department.php → isi halaman
```

### 3.2 Tanggung Jawab Layer

| Layer          | Tanggung Jawab                                                                                       |
| -------------- | ---------------------------------------------------------------------------------------------------- |
| **Routes**     | Mendefinisikan URL → controller::method + filter akses                                                 |
| **Controller** | Orchestrasi: set akses, load model, validasi input, panggil model, kirim response (view / JSON)       |
| **Model**      | Semua query DB, join, filter, search (datatable), dan CRUD                                            |
| **View**       | Tampilan HTML + JS (hanya render, tidak ada logika query)                                             |
| **Helper**     | Fungsi global reuse (session, enkripsi, format, datatable, notifikasi)                                |

> **Aturan utama:** *Thin Controller, Rich Model.* Controller TIDAK boleh berisi query
> SQL panjang / builder; semua query dipindah ke Model. Controller hanya memanggil method
> model dan menyusun response.

### 3.3 BaseController

Semua controller mewarisi `app/Controllers/BaseController.php`. Base ini menyediakan:

- `initController()` — otomatis set `$this->db = db_connect()`.
- `getPost($key, $cadangan = '')` — baca POST aman, default saat kosong.
- `getGet($key, $cadangan = '')` — baca GET aman.
- `setArrayAccess($array)` / `getArrayAccess()` — menyimpan & membaca hak akses menu.

```php
namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $db;

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
}
```

---

## 4. Gaya Kode (Code Style)

### 4.1 PHP — Aturan Umum

1. **File PHP**: dibuka dengan `<?php`, **tanpa closing tag** `?>` di akhir file.
2. **Identasi**: 4 spasi (bukan tab).
3. **Line length**: tidak diatur keras, namun utamakan kode yang mudah dibaca (max ~120 karakter).
4. **String**: gunakan `'single quote'` untuk string statis; `"double quote"` hanya jika
   ada interpolasi variabel / karakter escape.
5. **Namespace** wajib: `namespace App\Controllers\Master;` dst.
6. **Class**: `StudlyCase` (misal `class Department`).
7. **Method & fungsi**: `camelCase` (misal `addDepartment()`, `getDepartment()`).
8. **Variabel**: `camelCase` (misal `$departname`, `$arrbc`).
9. **Konstanta**: `UPPER_SNAKE` (misal `COMADD`, `SEND_BELL`).
10. **Curly braces**: brace di baris baru (Allman style) untuk class & method;
    kontrol flow juga memakai brace di baris baru (konsisten di codebase).
11. **`use` statements**: satu per baris, diurutkan, ditulis setelah namespace.
12. **Deklarasi properti** class: `protected $table = '...'` (lihat pola model).

### 4.2 Contoh Penulisan PHP (Mengikuti Codebase)

```php
<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\Msdepartment;
use Exception;

class Department extends BaseController
{
    function __construct()
    {
        $dataakses = sessionMenu('department');
        $this->setArrayAccess($dataakses);
        $this->department = new Msdepartment();
        $this->arrbc = [
            [
                'Setting',
                'Department',
            ]
        ];
    }

    function index()
    {
        return view('master/department/v_department', [
            'title' => 'Department',
            'breadcrumb' => $this->arrbc,
            'akses' => $this->getArrayAccess(),
            'section' => 'Setting Department'
        ]);
    }
}
```

### 4.3 Model — Pola Standar

Setiap model mengikuti pola konsisten berikut:

```php
<?php

namespace App\Models;

use CodeIgniter\Model;

class Msdepartment extends Model
{
    protected $table = 'msdepartment as a';

    public function __construct()
    {
        $this->db = db_connect();
        $this->builder = $this->db->table($this->table);
    }

    // Kolom yang bisa di-search datatable (null = tidak bisa dicari)
    public function searchable()
    {
        return [
            null,
            "a.departname",
            "b.companyname",
            null
        ];
    }

    // Query untuk datatable (return BUILDER, bukan hasil)
    public function getDepartment()
    {
        return $this->builder
            ->select('a.*, b.companyid, b.companyname')
            ->join('mscompany as b', 'b.companyid = a.companyid')
            ->where('a.companyid', getSession('companyid'));
    }

    // Ambil satu baris
    public function getOne($departid = '')
    {
        $x = $this->builder
            ->select('a.*, b.companyid, b.companyname')
            ->join('mscompany as b', 'b.companyid = a.companyid');
        if ($departid != '') {
            $x->where('a.departid', $departid);
        }
        return $x->get()->getRowArray();
    }

    // CRUD
    public function store($data)
    {
        return $this->builder->insert($data);
    }

    public function edit($data, $id)
    {
        return $this->builder->update($data, ['departid' => $id]);
    }

    public function destroy($id)
    {
        return $this->builder->delete(['departid' => $id]);
    }

    // Search untuk select2 / autocomplete
    public function getSelect($search = '', $companyid = '', $branchid = '')
    {
        $cari = strtolower($search);
        $x = $this->builder
            ->where("(lower(departname) like '%" . $cari . "%')", null, false)
            ->limit(15);
        if (!empty($companyid)) {
            $x->where("a.companyid", decrypting($companyid));
        } else {
            $x->where('a.companyid', getSession('companyid'));
        }
        return $x->orderBy('departname')->get()->getResultArray();
    }
}
```

**Poin penting model:**

- `$table` selalu pakai alias: `'namatabel as a'`.
- Join memakai alias huruf: `join('mscompany as b', 'b.companyid = a.companyid')`.
- Filter tenant (perusahaan) selalu memakai `getSession('companyid')`.
- Method query datatable **return Builder** (`$this->builder->select(...)`) — TIDAK
  memanggil `->get()` agar mesin datatable bisa menambahkan search/limit/order.
- `searchable()` return array; indeks `null` = kolom (No, Action) tidak dicari.
- Pencarian pakai `lower(...) like '%...%'` dan parameter di-escape dengan `null, false`.

### 4.4 Controller — Pola Standar

Struktur controller CRUD selalu:

```
__construct()          → sessionMenu + setArrayAccess + instansiasi model + breadcrumb
index()                → render halaman list
datatable()            → response JSON untuk DataTables
forms($id = "")        → render halaman form (add/edit)
addX()                 → proses simpan (POST)
updateX()              → proses ubah (POST)
deleteX()              → proses hapus (POST)
getX()                 → endpoint select2 (GET/POST) → { data: [{id,text}], csrfToken }
```

Penamaan method action mengikuti **modul**: `addDepartment`, `updateDepartment`,
`deleteDepartment`, atau `addData`/`updateData`/`deleteData` untuk modul generik.

### 4.5 View — Aturan Penulisan

1. Setiap halaman diawali include template:
   ```php
   <?= $this->include('template/v_header') ?>
   <?= $this->include('template/v_appbar') ?>
   ```
2. Isi halaman dibungkus: `<div class="main-content content margin-t-4">`.
3. Diakhiri include template footer, lalu **block `<script>`** untuk JS halaman:
   ```php
   <?= $this->include('template/v_footer') ?>
   <script>
       // JS halaman di sini
   </script>
   ```
4. Short echo tags (`<?= ... ?>`) wajib — jangan pakai `<?php echo ... ?>` di view.
5. Variabel yang biasa dipakai di view: `$title`, `$section`, `$breadcrumb`, `$row`,
   `$form_type`, `$akses`.
6. Indentasi view 4 spasi.

---

## 5. Session, Helper & Utility Global

### 5.1 Helper Auto-Load

`app/Config/Autoload.php` memuat `primary_helper.php` secara global:

```php
public $helpers = ['primary_helper'];
```

Artinya semua fungsi di `app/Helpers/primary_helper.php` tersedia di seluruh controller,
model, dan view tanpa `helper()` manual.

### 5.2 Session — Pola Enkripsi

Session HRS memakai suffix `-hrs-session` dan nilainya di-enkripsi:

```php
// SET — simpan
setSession('userid', $data['userid']);

// GET — baca
getSession('userid');          // return nilai plain

// Hapus
removeSession('key');
destroySession();
```

Implementasi di `primary_helper.php`:

```php
function setSession($key, $value)
{
    return session()->set($key . '-hrs-session', encrypting($value));
}

function getSession($key)
{
    return decrypting(session()->get($key . '-hrs-session'));
}
```

**Session standar yang sering dipakai:**
`userid`, `empname`, `name`, `photo`, `companyid`, `companyname`, `branchid`,
`branchname`, `departid`, `departname`, `areaid`, `areaname`, `menuid`.

### 5.3 Enkripsi & Decrypt ID

ID sensitif (PK) di-enkripsi sebelum dikirim ke view/URL, lalu di-decrypt di controller:

```php
// View / URL
<?= getURL('department/form/' . encrypting($db->departid)) ?>

// Controller
$departid = decrypting($this->getPost('id'));
```

```php
function encrypting($teks = '')   // CI Encrypter + Base62 encode
function decrypting($teks = '')   // Base62 decode + CI Decrypter
```

### 5.4 Helper Penting Lainnya

| Fungsi                               | Fungsi                                                                      |
| ------------------------------------ | --------------------------------------------------------------------------- |
| `getURL($param = '')`                | `base_url($param)`                                                          |
| `getSession / setSession`            | baca/tulis session terenkripsi                                              |
| `formatDate($format, $date = '')`    | format tanggal (`Y-m-d H:i:s`, dll)                                         |
| `formatNumber`, `idr`, `idrHTML`     | format angka / rupiah                                                       |
| `encrypting / decrypting`            | enkripsi/dekripsi ID                                                        |
| `base_encode / base_decode`          | encode base64 6x (dipakai token CSRF di halaman)                            |
| `respondAndDie($status, $msg)`       | echo JSON `{success,msg,csrfToken}` lalu `die`                              |
| `send_notif(...)`                    | kirim notifikasi via Socket.IO + simpan ke `stnotif`                        |
| `validateDeleteData($tables)`        | cek referensi FK sebelum hapus (lihat [Bab 10](#10-form--submit-ajax--csrf))|
| `insert_history(...)`                | simpan log perubahan ke `msloghistory`                                      |
| `sessionMenu($link)`                 | set `menuid` dari URL menu + return `getAllAccess()`                        |
| `getAllAccess()`                     | array akses `COMPO_1..COMPO_7` (true/false)                                 |

### 5.5 Konstanta Global (`app/Config/Constants.php`)

```php
defined('BASE')         || define('BASE', $base);      // dynamic base URL
defined('COMVIEW')      || define('COMVIEW', 1);
defined('COMADD')       || define('COMADD', 2);
defined('COMEDIT')      || define('COMEDIT', 3);
defined('COMDELETE')    || define('COMDELETE', 4);
defined('COMUPLOAD')    || define('COMUPLOAD', 5);
defined('COMDOWNLOAD')  || define('COMDOWNLOAD', 6);
defined('COMSPECIAL')   || define('COMSPECIAL', 7);
defined('SEND_BELL')    || define('SEND_BELL', 'send_bell_hrs');
defined('BELL_SOCKET')  || define('BELL_SOCKET', 'bell_socket_hrs');
```

Konstanta `COMPO_*` dipakai untuk **validasi akses per tombol** (lihat Bab 4.4 & 10).

---

## 6. Template Global (Layout)

### 6.1 Daftar File Template (`app/Views/template/`)

| File              | Fungsi                                                                                    |
| ----------------- | ----------------------------------------------------------------------------------------- |
| `v_header.php`    | DOCTYPE, `<head>`, include `v_import`, sidebar + navbar, buka container                   |
| `v_import.php`    | **Semua** CSS & JS global (kunci agar library konsisten)                                  |
| `v_sidebar.php`   | Sidebar kiri (menu dinamis dari `generateSidebar()`)                                      |
| `v_navbar.php`    | Navbar atas (breadcrumb, notifikasi, switcher company)                                    |
| `v_appbar.php`    | Appbar (versi mobile / halaman ESS) dengan tombol back & search                           |
| `v_tab.php`       | Tab navigation (jika variabel `$tabs` di-set)                                             |
| `v_footer.php`    | Tutup container + **seluruh JS global** (notifikasi, modal, datatable, select2, form builder) |
| `v_subsidebar.php`| Sub-menu sidebar (versi expand)                                                           |
| `v_sideopen.php`  | Sub-menu sidebar (versi shrink)                                                           |

### 6.2 Alur Include Template

`v_header.php` menyusun head + sidebar + navbar, lalu membuka `<div class="global-containers">`,
`<div class="content">`, dan `<div class="container-fluid p-x-y">`. Setiap halaman
meletakkan isi di antara header dan footer:

```php
<?= $this->include('template/v_header') ?>
<?= $this->include('template/v_appbar') ?>
<div class="main-content content margin-t-4">
    <!-- isi halaman -->
</div>
<?= $this->include('template/v_footer') ?>
```

### 6.3 `v_import.php` — Asset Global

**JANGAN** menambah library CSS/JS satu-per-halaman. Semua library global sudah
dideklarasikan di `v_import.php` sehingga otomatis tersedia di semua halaman:

**CSS:** `template.css`, `modal.css`, `mystyle.css`, `boxicons.css`, `jquery-ui.css`,
`select2.min.css`, `notyf.css`, `microtip.css`, `apexcharts.css`, `daterangepicker.css`,
`cropper.css`, `typeahead.css`, `jquery.nestable.min.css`, `accordion.css`,
`dataTables.dataTables.min.css`, `fixedColumns.dataTables.min.css`.

**JS:** `jquery.js`, `jquery.dataTables.min.js`, `dataTables.fixedColumns.min.js`,
`typeahead.min.js`, `jquery-ui.js`, `notyf.js`, `editor.js`, `ckeditor/build/ckeditor.js`,
`xlsx.full.min.js`, `bootstrap.js`, `select2.min.js`, `apexcharts.js`, `socket.io.min.js`,
`sweetalert2.min.js`, `moment.min.js`, `daterangepicker.min.js`, `cropper.js`, `moment.js`,
`jquery.nestable.min.js`, `accordion.js`, `chart.js`, `chartDatalabels.min.js`,
`fullcalendar.js`, `popper.min.js`, `tippy-bundle.umd.min.js`.

> **Catatan kunci:** `editor.js` berisi fungsi JS `encrypter()` / `decrypter()` (base64 6x)
> yang dipakai seluruh halaman untuk mengelola token CSRF. Jangan hapus file ini.

### 6.4 `v_footer.php` — JS Global

File ini memuat **semua helper JS** yang wajib dipakai untuk pola HRS:

- `showSuccess(msg)`, `showError(msg)`, `showNotif(type, msg)` → Notyf.
- `toPage(url, blank)` → pindah halaman.
- `close_modal(id)` → tutup modal bootstrap.
- `modalDelete(title, datas)` → buka modal konfirmasi hapus; saat konfirmasi ditekan
  otomatis `$.ajax` ke `link` dengan `id`, lalu `tbl.ajax.reload()` (jika `pagetype='table'`).
- `modalForm(title, size, link, datas)` / `modalFormTwo(...)` → buka modal berisi form
  (response JSON `{ view: '...' }`).
- `modalglobal(id, link, title, btn, size)` → buka modal detail.
- `modalRelease(title, type, custommessage)` → konfirmasi release/unrelease.
- `generateSelect2(...)` → **helper select2 utama** (lihat [Bab 9](#9-select2--ajax-endpoint)).
- `load_notification()`, `read_notif(elem)` → notifikasi socket.
- `showSlideForm(title, link, type, data)` → form slide-up.
- `generateTypeahead(input, url, hiddenId, ...)` → autocomplete typeahead.
- `encrypter/decrypter` (di `editor.js`) untuk token CSRF.
- Inisialisasi global DataTables:
  - `var tbl = $('.table-master').DataTable({ serverSide: true, ... })` → dipakai otomatis
    untuk tabel di halaman ber-class `table-master`. Endpoint = `current_url()/table`.
  - `tbl_history = $('.tbl-history')...` → tabel riwayat log.
- Helper angka: `formatRupiah`, `toNumeric`, `price_keyup`, `exp_number` (JS).

**Token CSRF global** disimpan di hidden input:

```html
<input type="hidden" id="csrf_token" value="<?= base_encode(csrf_hash()) ?>">
```

Semua AJAX memakai pola:

```js
data: { <?= csrf_token() ?>: decrypter($("#csrf_token").val()) }
...
success: function(res) { $("#csrf_token").val(encrypter(res.csrfToken)); }
```

### 6.5 Variabel yang Dipakai Template

| Variabel     | Dipakai di                         | Keterangan                            |
| ------------ | ---------------------------------- | ------------------------------------- |
| `$title`     | `v_header`, `v_appbar`, `v_navbar` | Wajib ada                             |
| `$section`   | `v_navbar` (judul breadcrumb)      | Wajib ada (kecuali `$nobc` di-set)    |
| `$breadcrumb`| `v_navbar`                         | Array of array, contoh `[['Setting','Department']]` |
| `$search`    | `v_appbar`                         | Opsional; memunculkan tombol add & search di appbar |
| `$tabs`      | `v_tab`                            | Opsional; tab navigation              |
| `$nobc`      | `v_navbar`                         | Opsional; menyembunyikan breadcrumb   |
| `$akses`     | View halaman                       | Array akses `COMPO_*` untuk cek tombol|

---

## 7. Routes

### 7.1 Struktur `app/Config/Routes.php`

Semua route didefinisikan di `app/Config/Routes.php` dengan tiga filter bawaan:

```php
$routes = Services::routes();
$this->auth   = ['filter' => 'auth'];          // wajib login
$this->noauth = ['filter' => 'noauth'];        // halaman publik (login, forgot pass)
$this->akses  = ['filter' => 'checkAccess'];   // wajib login + cek akses menu
```

Mapping filter di `app/Config/Filters.php`:

| Alias           | Kelas              | Fungsi                                             |
| --------------- | ------------------ | -------------------------------------------------- |
| `auth`          | `IsNotLogin`       | Redirect ke `/login` jika session kosong           |
| `noauth`        | `IsLogin`          | (untuk halaman yang hanya boleh diakses saat belum login) |
| `checkAccess`   | `CheckAccess`      | Cek login + cek hak akses view (`COMVIEW`)         |

### 7.2 Pola Route per Modul CRUD

Route CRUD memakai `$routes->group()` dengan pola tetap:

```php
$routes->group('department', function ($routes) {
    $routes->add('',              'master\Department::index',            $this->akses);
    $routes->add('table',         'master\Department::datatable',        $this->akses);
    $routes->add('form',          'master\Department::forms',            $this->akses);
    $routes->add('form/(:any)',   'master\Department::forms/$1',         $this->akses);
    $routes->add('add',           'master\Department::addDepartment',    $this->akses);
    $routes->add('update',        'master\Department::updateDepartment', $this->akses);
    $routes->add('delete',        'master\Department::deleteDepartment', $this->akses);
    $routes->add('getdepartment', 'master\Department::getDepartment',    $this->auth);
});
```

**Aturan:**
- **Endpoint tabel** selalu `table` → method `datatable` (dipakai `current_url()/table`
  oleh `v_footer.php`).
- **Endpoint select2** memakai prefix `get...` (contoh `getdepartment`, `getselect`,
  `getemployee`) dan biasanya filter `$this->auth`.
- Route `form/(:any)` menerima parameter terenkripsi: `forms/$1`.
- Segmen URL route **sama persis** dengan argumen `sessionMenu('...')` di controller
  (contoh `department` ↔ `sessionMenu('department')`).
- Route non-CRUD (upload, import, tab, detail) ditambah sebagai baris `$routes->add(...)`
  di dalam group yang sama.

### 7.3 Menambah Route Baru

1. Tentukan nama segmen (misal `division`) — harus unik.
2. Tambahkan group di `Routes.php`:
   ```php
   $routes->group('division', function ($routes) {
       $routes->add('',            'master\Division::index', $this->akses);
       $routes->add('table',       'master\Division::datatable', $this->akses);
       $routes->add('form',        'master\Division::forms', $this->akses);
       $routes->add('form/(:any)', 'master\Division::forms/$1', $this->akses);
       $routes->add('add',         'master\Division::addData', $this->akses);
       $routes->add('update',      'master\Division::updateData', $this->akses);
       $routes->add('delete',      'master\Division::deleteData', $this->akses);
       $routes->add('getselect',   'master\Division::getSelect', $this->auth);
   });
   ```
3. Di controller, `sessionMenu('division')` otomatis memetakan hak akses menu.

---

## 8. Datatable

### 8.1 Arsitektur Server-Side Datatable

Mesin datatable ada di `app/Helpers/Datatables/`. Alur kerjanya:

1. **Frontend** (`v_footer.php`) menginisialisasi `tbl = $('.table-master').DataTable({...})`
   dengan `serverSide: true` dan endpoint `current_url()/table`.
2. **Controller** method `datatable()` memanggil `Datatables::method(...)` lalu `make()`
   untuk menjalankan query builder dari Model, menambahkan search/limit/order.
3. **Response** `toJson()` mengembalikan JSON `{ draw, recordsFiltered, recordsTotal,
   data, csrfToken, tambahan }`.
4. `dataSrc` di frontend membaca `json.data` dan memperbarui `csrfToken`.

### 8.2 Bagian Backend

**Controller:**

```php
public function datatable()
{
    $table = Datatables::method([Msdepartment::class, 'getDepartment'], 'searchable')
        ->make();
    $table->updateRow(function ($db, $no) {
        $akses = $this->getArrayAccess();
        $btn_edit = (!$akses["COMPO_" . COMEDIT] ? '-' : "<button onclick=\"toPage('" . getURL('department/form/' . encrypting($db->departid)) . "')\"  class='btn btn-sm btn-warning'><i class='bx bx-edit-alt'></i></button>");
        $btn_hapus = (!$akses["COMPO_" . COMDELETE] ? '-' : "<button type='button' class='btn btn-sm btn-danger' onclick=\"modalDelete('Delete department - " . $db->departname . "', {'link':'" . getURL('department/delete') . "', 'id':'" . encrypting($db->departid) . "', 'pagetype':'table'})\"><i class='bx bx-trash'></i></button>");
        return [
            $no,
            $db->departname,
            $db->companyname,
            $btn_edit . " " . $btn_hapus
        ];
    });
    $table->toJson();
}
```

**Model** — query datatable return **builder** dan punya method `searchable()`:

```php
public function searchable()
{
    return [
        null,              // kolom No (tidak dicari)
        "a.departname",    // kolom DB untuk kolom 2
        "b.companyname",   // kolom DB untuk kolom 3
        null               // kolom Action (tidak dicari)
    ];
}

public function getDepartment()
{
    return $this->builder
        ->select('a.*, b.companyid, b.companyname')
        ->join('mscompany as b', 'b.companyid = a.companyid')
        ->where('a.companyid', getSession('companyid'));
}
```

### 8.3 API `Datatables::method()`

```php
Datatables::method($callable, $dbcolumns = null)
```

- `$callable`: `[Model::class, 'methodQuery']` (query builder) **atau**
  `[Model::class, 'methodTable']` dengan `$dbcolumns` = nama method `searchable`.
- `->make()` → jalankan query (filter search + order + limit) dan hitung total.
- `->updateRow(callable)` → transform tiap baris. Param `$db` (object row), `$no` (nomor urut).
- `->updateMultipleRow(callable)` → satu baris menghasilkan banyak baris.
- `->prependRow($data)` / `->appendRow($data)` → tambah baris di awal/akhir.
- `->setParams(...)` → parameter tambahan untuk method query.
- `->toJson($tambahan = [])` → output JSON + `csrfToken` (+ data `tambahan` jika perlu).

> Perhatikan: untuk datatable **tanpa searchable method** (misal tabel tab/detail), panggil
> `Datatables::method([Model::class, 'getTable'])` tanpa argumen kedua (contoh pada
> `Msfamilydata`, `Msemployeestatus`, dll).

### 8.4 Bagian Frontend

**Inisialisasi global** di `v_footer.php`:

```js
var tbl = $('.table-master').DataTable({
    serverSide: true,
    destroy: true,
    autoWidth: false,
    ajax: {
        url: '<?= current_url(true) ?>/table',
        type: 'post',
        dataType: 'json',
        data: function(param) {
            param["<?= csrf_token() ?>"] = decrypter($("#csrf_token").val());
            return param;
        },
        "deferRender": true,
        dataSrc: function(json) {
            let gt = json.tambahan.grandtotal;
            if (gt != undefined) {
                $("#text-grandtotal").text(gt);
            }
            $("#csrf_token").val(encrypter(json.csrfToken));
            return json.data
        }
    }
})
```

**View halaman** hanya butuh tabel ber-class `table-master` dengan kolom yang
berurutan sesuai `updateRow`:

```html
<table class="table table-bordered table-master fs-7" style="width: 100%;">
    <thead>
        <tr>
            <td class="tableheader">No</td>
            <td class="tableheader">Name</td>
            <td class="tableheader">Company</td>
            <td class="tableheader">Action</td>
        </tr>
    </thead>
    <tbody></tbody>
</table>
```

> Jika halaman punya lebih dari satu tabel (tabel tab/detail), beri class berbeda
> (misal `table-mastered`, `table-detail`) dan inisialisasi manual di script halaman,
> lalu refresh dengan `tblX.ajax.reload()`.

---

## 9. Select2 & AJAX Endpoint

### 9.1 Helper Global `generateSelect2`

Definisi lengkap di `v_footer.php`:

```js
function generateSelect2(
    element,                 // selector, misal '#leaveType'
    dparent = '',            // dropdownParent (misal '#modaldetail') jika di dalam modal
    link = '',               // endpoint AJAX (POST) yang mengembalikan {data, csrfToken}
    placeholder = '',        // default 'Choose data'
    width = '',              // misal '100%'
    minimumResultsForSearch = 0,
    allowClear = true,
    datas = {},              // data tambahan yang dikirim (misal {textcat: 'Leave Type'})
    ismultiple = false,
) {
    setting_select = {
        allowClear: allowClear,
        multiple: ismultiple,
    };
    if (dparent != '') {
        setting_select.dropdownParent = $(dparent)
    }
    if (placeholder == '') {
        placeholder = 'Choose data';
    }
    setting_select.placeholder = placeholder;
    if (width != '') {
        setting_select.width = width;
    }
    if (minimumResultsForSearch != 0) {
        setting_select.minimumResultsForSearch = minimumResultsForSearch;
    }
    if (link != '') {
        setting_select.ajax = {
            url: link,
            type: "post",
            dataType: "json",
            delay: 250,
            data: function(params) {
                datas.searchTerm = params.term;
                datas.<?= csrf_token() ?> = decrypter($("#csrf_token").val());
                return datas;
            },
            processResults: function(response) {
                $("#csrf_token").val(encrypter(response.csrfToken));
                return { results: response.data };
            },
            cache: true,
        };
    }
    $(element).select2(setting_select);
    $(element).on('select2:open', function() {
        $(this).select2('focus');
    });
}
```

### 9.2 Endpoint Server (Controller)

Controller menyediakan method `getX` yang membaca `searchTerm` dan mengembalikan
array `[{id, text}]`:

```php
function getDepartment($stmt = '')
{
    $search = $this->getPost('searchTerm');
    $companyid = $this->getPost('companyid');
    $isall = $this->getPost('isallowedall');
    $branchid = $this->getPost('branchid');
    $get = $this->department->getSelect($search, $companyid, $branchid);
    $arr = [];
    if ($isall == 't' && getSession('branchid') == '0') {
        $arr[] = ['id' => 0, 'text' => 'ALL'];
    }
    foreach ($get as $g) {
        $arr[] = [
            'id' => (empty($stmt) ? encrypting($g['departid']) : $g['departid']),
            'text' => $g['departname']
        ];
    }
    echo encode([
        'data' => $arr,
        'csrfToken' => csrf_hash(),
        'trace' => db_connect()->error(),
    ]);
}
```

> **Pola umum:** `id` berisi `encrypting(...)` dari PK (kecuali parameter `$stmt`
> menandakan pemakaian internal yang butuh id polos). Respons selalu menyertakan
> `csrfToken`.

### 9.3 Pemakaian di View

```js
$(document).ready(function() {
    generateSelect2("#leaveType", "", '<?= getURL('settingtype/gettype') ?>', 'Select Leave Type', '100%', 0, true, {
        textcat: "Leave Type",
    });
});
```

**Pola filter list (multiple select2 pada halaman index):** inisialisasi tiap dropdown,
lalu baca nilai lewat `.select2('data')` saat tombol filter ditekan:

```js
let data_area = $('#area').select2('data');
let data_branch = $('#branch').select2('data');
// dst, lalu kirim sebagai parameter ajax reload
```

**Pola validasi/opsi dinamis saat select2 berubah:**

```js
$("#leaveType").on('change', function() {
    let data = $(this).select2('data');
    if (data[0].text == 'Sakit' || data[0].text == 'Cuti') {
        $("#fromHour").val('00:00');
        $("#fromHour").attr('readonly', 'true');
    } else {
        $("#fromHour").removeAttr('readonly');
    }
});
```

### 9.4 Select2 di Dalam Modal

Saat select2 berada di dalam modal, wajib set `dropdownParent` agar dropdown tidak
terpotong oleh `overflow` modal:

```js
generateSelect2('#branch', '#modaldetail', '<?= getURL('branch/getselect') ?>', 'Choose Branch', '100%', 0, true, {});
```

### 9.5 Alternatif: `$.fn.initSelect2` (Form Builder)

Untuk elemen dengan attribute `data-*`, tersedia `FormsBuilder.initSelect2`:

```js
$('select').initSelect2({
    selector: 'select',
    url: '<?= getURL('department/getdepartment') ?>',
    placeholder: 'Choose department',
    data: (params) => ({ searchTerm: params.term }),
});
```

---

## 10. Form & Submit (AJAX + CSRF)

### 10.1 Pola Form di View

Contoh `v_form.php` (Department):

```html
<form id="form-department" style="padding-inline: 0px;">
    <input type="hidden" name="id" value="<?= (($form_type == 'edit') ? encrypting($row['departid']) : '') ?>">
    <div class="form-group">
        <label>Department Name<span class="text-danger">*</span> :</label>
        <input type="text" class="form-input fs-7" name="departname" value="<?= (($form_type == 'edit') ? $row['departname'] : '') ?>" placeholder="ex: Finance" required>
    </div>
    <input type="hidden" id="csrf_token_form" name="<?= csrf_token() ?>">
</form>
```

### 10.2 Submit via AJAX

```js
$('#form-department').submit(function(e) {
    e.preventDefault();
    let form_type = "<?= $form_type ?>"
    let link = "<?= getURL('department/add') ?>"
    if (form_type == 'edit') {
        link = "<?= getURL('department/update') ?>"
    }
    // load button
    $('#btn-subs').html('<i class="bx bx-loader bx-spin"></i>');
    $('#btn-subs').attr('disabled', 'disabled');
    $("button.btn").attr('disabled', 'disabled');

    // isi token CSRF form dari token global
    let csrf = decrypter($("#csrf_token").val());
    $("#csrf_token_form").val(csrf);

    let data = $('#form-department').serialize();
    $.ajax({
        type: 'post',
        url: link,
        data: data,
        dataType: 'json',
        success: function(response) {
            // restore button
            $('#btn-subs').html(old_html);
            $('#btn-subs').removeAttr('disabled');
            $("button.btn").removeAttr('disabled');

            // perbarui token CSRF global
            $("#csrf_token").val(encrypter(response.csrfToken));
            $("#csrf_token_form").val('');

            let pesan = response.pesan;
            let notif = 'success';
            if (response.sukses != 1) notif = 'error';
            showNotif(notif, pesan);

            if (response.sukses == 1) {
                window.location.href = "<?= getURL('department') ?>"
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            // restore button + showError
            showError(thrownError + ", please contact administrator for the further");
        }
    })
    return false;
})
```

### 10.3 Pola Controller (Simpan/Ubah)

```php
function addDepartment()
{
    $departname = $this->getPost('departname');
    $res = array();
    $akses = $this->getArrayAccess();
    if (!$akses["COMPO_" . COMADD]) {
        throw new Exception("You don't have access to add");
    }
    $this->db->transBegin();
    try {
        if (empty($departname)) {
            throw new Exception("Department name masih kosong");
        }
        $this->department->store([
            'departname' => $departname,
            'companyid' => getSession('companyid'),
            'createdby' => getSession('userid'),
            'createddate' => formatDate('Y-m-d H:i:s'),
            'updatedby' => getSession('userid'),
            'updateddate' => formatDate('Y-m-d H:i:s')
        ]);
        $res = [
            'pesan' => 'Department baru ditambahkan',
            'sukses' => '1',
            'trace' => db_connect()->error(),
        ];
        $this->db->transCommit();
    } catch (Exception $e) {
        $res = [
            'sukses' => '0',
            'pesan' => $e->getMessage(),
            'traceString' => $e->getTraceAsString(),
        ];
        $this->db->transRollback();
    }
    $this->db->transComplete();
    $res['csrfToken'] = csrf_hash();
    echo json_encode($res);
}
```

**Aturan wajib untuk method simpan/ubah/hapus:**

1. **Cek akses** dengan `$this->getArrayAccess()["COMPO_" . COMADD/COMEDIT/COMDELETE]`.
2. **Transaksi DB** selalu dibungkus `transBegin()` / `transCommit()` / `transRollback()`
   + `transComplete()`.
3. **Validasi** lempar `throw new Exception("pesan")` → ditangkap catch → `sukses=0`.
4. **Response JSON** selalu menyertakan `csrfToken` baru:
   `$res['csrfToken'] = csrf_hash();`
5. `createdby/createddate` & `updatedby/updateddate` selalu diisi saat insert.

### 10.4 Pola Delete + Validasi FK

```php
function deleteDepartment()
{
    $departid = decrypting($this->getPost('id'));
    $res = array();
    $akses = $this->getArrayAccess();
    if (!$akses["COMPO_" . COMDELETE]) {
        throw new Exception();
    }
    $this->db->transBegin();
    try {
        $tables = [
            ['table' => 'msannouncementdt', 'column' => 'departid', 'value' => $departid, 'alias' => 'Announcement Detail'],
            ['table' => 'msemployee', 'column' => 'departid', 'value' => $departid, 'alias' => 'Employee'],
            // dst...
        ];
        $getvalidate = validateDeleteData($tables);
        if (!empty($getvalidate)) {
            $aliases = array_unique(array_column($getvalidate, 'alias'));
            $msg = "<div>Data sedang digunakan di :</div>";
            $msg .= "<ul style='margin: 0; padding-left: 20px;'>";
            foreach ($aliases as $alias) {
                $msg .= "<li>" . $alias . "</li>";
            }
            $msg .= "</ul>";
            throw new Exception($msg);
        }
        $this->department->destroy($departid);
        $res['sukses'] = '1';
        $this->db->transCommit();
    } catch (Exception $e) {
        $res = [
            'sukses' => '0',
            'pesan' => (!empty($e->getMessage())) ? $e->getMessage() : "Data gagal dihapus"
        ];
        $this->db->transRollback();
    }
    $this->db->transComplete();
    $res['csrfToken'] = csrf_hash();
    echo json_encode($res);
}
```

`validateDeleteData()` memakai `Globalmodel::validateData()` yang menjalankan UNION query
untuk mengecek apakah `value` masih direferensikan tabel lain, lalu mengembalikan daftar
`alias` yang menggunakannya.

### 10.5 Form dengan File Upload

Pola `leavetime/v_form.php` — gunakan `FormData` + `processData:false`/`contentType:false`:

```js
let data = new FormData(this);
data.append('document', $('#document').prop('files')[0]);
$.ajax({
    type: 'post',
    url: '<?= getURL('leavetime/create') ?>',
    data: data,
    dataType: "json",
    processData: false,
    contentType: false,
    success: function(response) { /* pola sama seperti di atas */ }
});
```

---

## 11. Tutorial: Membuat Module Baru

Tutorial ini membuat modul **Division** (Master Data → Division) dengan CRUD penuh,
mengikuti seluruh konvensi yang sudah dijelaskan.

### 11.1 Ringkasan Langkah

| Langkah | File yang Dibuat / Diubah                          | Isi                              |
| ------- | -------------------------------------------------- | -------------------------------- |
| 1       | DB: tabel `msdivision`                             | `divisionid, divisionname, isactive, companyid, createdby, createddate, updatedby, updateddate` |
| 2       | `app/Models/Msdivision.php`                        | Model CRUD + searchable + select |
| 3       | `app/Controllers/master/Division.php`              | Controller CRUD                  |
| 4       | `app/Config/Routes.php` (tambah group `division`)  | Route CRUD                      |
| 5       | `app/Views/master/division/v_division.php`         | Halaman list + datatable         |
| 6       | `app/Views/master/division/v_form.php`             | Halaman form add/edit            |
| 7       | Test manual                                       | Add → Edit → Delete → Filter     |

### 11.2 Langkah 1 — Model `Msdivision.php`

```php
<?php

namespace App\Models;

use CodeIgniter\Model;

class Msdivision extends Model
{
    protected $table = 'msdivision as a';

    public function __construct()
    {
        $this->db = db_connect();
        $this->builder = $this->db->table($this->table);
    }

    public function searchable()
    {
        return [
            null,               // No
            "a.divisionname",   // Division Name
            "b.companyname",    // Company
            "a.isactive",       // Status
            null                // Action
        ];
    }

    public function getTable()
    {
        return $this->builder
            ->select('a.*, b.companyname')
            ->join('mscompany as b', 'b.companyid = a.companyid')
            ->where('a.companyid', getSession('companyid'));
    }

    public function getOne($divisionid = '')
    {
        $x = $this->builder->select('a.*, b.companyname')
            ->join('mscompany as b', 'b.companyid = a.companyid');
        if ($divisionid != '') {
            $x->where('a.divisionid', $divisionid);
        }
        return $x->get()->getRowArray();
    }

    public function store($data)
    {
        return $this->builder->insert($data);
    }

    public function edit($data, $id)
    {
        return $this->builder->update($data, ['divisionid' => $id]);
    }

    public function destroy($id)
    {
        return $this->builder->delete(['divisionid' => $id]);
    }

    public function getSelect($search = '', $companyid = '')
    {
        $cari = strtolower($search);
        $x = $this->builder
            ->where("(lower(divisionname) like '%" . $cari . "%')", null, false)
            ->limit(15);
        if (!empty($companyid)) {
            $x->where("a.companyid", decrypting($companyid));
        } else {
            $x->where('a.companyid', getSession('companyid'));
        }
        return $x->orderBy('divisionname')->get()->getResultArray();
    }
}
```

### 11.3 Langkah 2 — Controller `Division.php`

```php
<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Helpers\Datatables\Datatables;
use App\Models\Msdivision;
use Exception;

class Division extends BaseController
{
    function __construct()
    {
        $dataakses = sessionMenu('division');
        $this->setArrayAccess($dataakses);
        $this->division = new Msdivision();
        $this->arrbc = [
            [
                'Master',
                'Division',
            ]
        ];
    }

    function index()
    {
        return view('master/division/v_division', [
            'title' => 'Division',
            'breadcrumb' => $this->arrbc,
            'akses' => $this->getArrayAccess(),
            'section' => 'Master Division'
        ]);
    }

    public function datatable()
    {
        $table = Datatables::method([Msdivision::class, 'getTable'], 'searchable')
            ->make();
        $table->updateRow(function ($db, $no) {
            $akses = $this->getArrayAccess();
            $btn_edit = (!$akses["COMPO_" . COMEDIT] ? '-' : "<button onclick=\"toPage('" . getURL('division/form/' . encrypting($db->divisionid)) . "')\"  class='btn btn-sm btn-warning'><i class='bx bx-edit-alt'></i></button>");
            $btn_hapus = (!$akses["COMPO_" . COMDELETE] ? '-' : "<button type='button' class='btn btn-sm btn-danger' onclick=\"modalDelete('Delete division - " . $db->divisionname . "', {'link':'" . getURL('division/delete') . "', 'id':'" . encrypting($db->divisionid) . "', 'pagetype':'table'})\"><i class='bx bx-trash'></i></button>");
            $btn_status = ($db->isactive == 't'
                ? "<button type='button' class='btn btn-sm btn-outline-success' onclick=\"updateStatus('" . encrypting($db->divisionid) . "', 'f')\">Aktif</button>"
                : "<button type='button' class='btn btn-sm btn-outline-secondary' onclick=\"updateStatus('" . encrypting($db->divisionid) . "', 't')\">Nonaktif</button>");
            return [
                $no,
                $db->divisionname,
                $db->companyname,
                $btn_status,
                $btn_edit . " " . $btn_hapus
            ];
        });
        $table->toJson();
    }

    public function forms($divisionid = "")
    {
        $form_type = (empty($divisionid) ? 'add' : 'edit');
        $section = (empty($divisionid) ? 'Add Division' : 'Update Division');
        $acc = (empty($divisionid) ? 2 : 3);
        $row = [];
        if ($divisionid != '') {
            $divisionid = decrypting($divisionid);
            $row = $this->division->getOne($divisionid);
        }
        $akses = $this->getArrayAccess();
        if (!$akses['COMPO_' . $acc]) {
            return redirect()->to(getURL('division'));
        }
        array_push($this->arrbc[0], 'Form');
        return view('master/division/v_form', [
            'form_type' => $form_type,
            'section' => $section,
            'title' => 'Division',
            'breadcrumb' => $this->arrbc,
            'row' => $row,
            'divisionid' => $divisionid
        ]);
    }

    function addData()
    {
        $divisionname = $this->getPost('divisionname');
        $res = array();
        $akses = $this->getArrayAccess();
        if (!$akses["COMPO_" . COMADD]) {
            throw new Exception("You don't have access to add");
        }
        $this->db->transBegin();
        try {
            if (empty($divisionname)) {
                throw new Exception("Division name masih kosong");
            }
            $this->division->store([
                'divisionname' => $divisionname,
                'isactive' => 't',
                'companyid' => getSession('companyid'),
                'createdby' => getSession('userid'),
                'createddate' => formatDate('Y-m-d H:i:s'),
                'updatedby' => getSession('userid'),
                'updateddate' => formatDate('Y-m-d H:i:s')
            ]);
            $res = [
                'pesan' => 'Division baru ditambahkan',
                'sukses' => '1',
                'trace' => db_connect()->error(),
            ];
            $this->db->transCommit();
        } catch (Exception $e) {
            $res = [
                'sukses' => '0',
                'pesan' => $e->getMessage(),
                'traceString' => $e->getTraceAsString(),
            ];
            $this->db->transRollback();
        }
        $this->db->transComplete();
        $res['csrfToken'] = csrf_hash();
        echo json_encode($res);
    }

    function updateData()
    {
        $id = decrypting($this->getPost('id'));
        $divisionname = $this->getPost('divisionname');
        $res = array();
        $akses = $this->getArrayAccess();
        if (!$akses["COMPO_" . COMEDIT]) {
            throw new Exception("You don't have access to update");
        }
        $this->db->transBegin();
        try {
            if (empty($divisionname)) {
                throw new Exception("Division name masih kosong");
            }
            $data = [
                'divisionname' => $divisionname,
                'updatedby' => getSession('userid'),
                'updateddate' => formatDate('Y-m-d H:i:s')
            ];
            $this->division->edit($data, $id);
            $res = [
                'pesan' => 'Update Division berhasil',
                'sukses' => '1',
                'trace' => db_connect()->error(),
            ];
            $this->db->transCommit();
        } catch (Exception $e) {
            $res = [
                'sukses' => '0',
                'pesan' => $e->getMessage(),
                'traceString' => $e->getTraceAsString()
            ];
            $this->db->transRollback();
        }
        $this->db->transComplete();
        $res['csrfToken'] = csrf_hash();
        echo json_encode($res);
    }

    function deleteData()
    {
        $divisionid = decrypting($this->getPost('id'));
        $res = array();
        $akses = $this->getArrayAccess();
        if (!$akses["COMPO_" . COMDELETE]) {
            throw new Exception();
        }
        $this->db->transBegin();
        try {
            $tables = [
                ['table' => 'msemployee', 'column' => 'divisionid', 'value' => $divisionid, 'alias' => 'Employee'],
                ['table' => 'msteamdt', 'column' => 'divisionid', 'value' => $divisionid, 'alias' => 'Team Detail'],
            ];
            $getvalidate = validateDeleteData($tables);
            if (!empty($getvalidate)) {
                $aliases = array_unique(array_column($getvalidate, 'alias'));
                $msg = "<div>Data sedang digunakan di :</div>";
                $msg .= "<ul style='margin: 0; padding-left: 20px;'>";
                foreach ($aliases as $alias) {
                    $msg .= "<li>" . $alias . "</li>";
                }
                $msg .= "</ul>";
                throw new Exception($msg);
            }
            $this->division->destroy($divisionid);
            $res['sukses'] = '1';
            $this->db->transCommit();
        } catch (Exception $e) {
            $res = [
                'sukses' => '0',
                'pesan' => (!empty($e->getMessage())) ? $e->getMessage() : "Data gagal dihapus"
            ];
            $this->db->transRollback();
        }
        $this->db->transComplete();
        $res['csrfToken'] = csrf_hash();
        echo json_encode($res);
    }

    function updateStatus()
    {
        $id = decrypting($this->getPost('id'));
        $isactive = $this->getPost('isactive');
        $res = array();
        $akses = $this->getArrayAccess();
        if (!$akses["COMPO_" . COMEDIT]) {
            throw new Exception("You don't have access to update");
        }
        $this->db->transBegin();
        try {
            $this->division->edit([
                'isactive' => $isactive,
                'updatedby' => getSession('userid'),
                'updateddate' => formatDate('Y-m-d H:i:s')
            ], $id);
            $res['sukses'] = '1';
            $res['pesan'] = 'Status berhasil diubah';
            $this->db->transCommit();
        } catch (Exception $e) {
            $res = [
                'sukses' => '0',
                'pesan' => $e->getMessage()
            ];
            $this->db->transRollback();
        }
        $this->db->transComplete();
        $res['csrfToken'] = csrf_hash();
        echo json_encode($res);
    }

    function getDivision($stmt = '')
    {
        $search = $this->getPost('searchTerm');
        $companyid = $this->getPost('companyid');
        $get = $this->division->getSelect($search, $companyid);
        $arr = [];
        foreach ($get as $g) {
            $arr[] = [
                'id' => (empty($stmt) ? encrypting($g['divisionid']) : $g['divisionid']),
                'text' => $g['divisionname']
            ];
        }
        echo encode([
            'data' => $arr,
            'csrfToken' => csrf_hash(),
            'trace' => db_connect()->error(),
        ]);
    }
}
```

### 11.4 Langkah 3 — Routes

```php
$routes->group('division', function ($routes) {
    $routes->add('',              'master\Division::index', $this->akses);
    $routes->add('table',         'master\Division::datatable', $this->akses);
    $routes->add('form',          'master\Division::forms', $this->akses);
    $routes->add('form/(:any)',   'master\Division::forms/$1', $this->akses);
    $routes->add('add',           'master\Division::addData', $this->akses);
    $routes->add('update',        'master\Division::updateData', $this->akses);
    $routes->add('delete',        'master\Division::deleteData', $this->akses);
    $routes->add('updateStatus',  'master\Division::updateStatus', $this->akses);
    $routes->add('getdivision',   'master\Division::getDivision', $this->auth);
});
```

### 11.5 Langkah 4 — View List `v_division.php`

```php
<?= $this->include('template/v_header') ?>
<?= $this->include('template/v_appbar') ?>
<div class="main-content content margin-t-4">
    <div class="card rounded shadow-sm w-100 p-x">
        <div class="card-header dflex align-center justify-end">
            <?php if ($akses["COMPO_" . COMADD]) : ?>
                <button class="btn btn-primary dflex align-center" onclick="return toPage('<?= getURL('division/form') ?>')">
                    <i class="bx bx-plus margin-r-2"></i>
                    <span class="fw-normal fs-7">Add New</span>
                </button>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <div class="table-responsive margin-t-14p">
                <table class="table table-bordered table-master fs-7" style="width: 100%;">
                    <thead>
                        <tr>
                            <td class="tableheader">No</td>
                            <td class="tableheader">Division</td>
                            <td class="tableheader">Company</td>
                            <td class="tableheader">Status</td>
                            <td class="tableheader">Action</td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->include('template/v_footer') ?>
<script>
    function updateStatus(id, isactive) {
        $.ajax({
            type: 'post',
            url: '<?= getURL('division/updateStatus') ?>',
            dataType: 'json',
            data: {
                id: id,
                isactive: isactive,
                <?= csrf_token() ?>: decrypter($("#csrf_token").val())
            },
            success: function(response) {
                $("#csrf_token").val(encrypter(response.csrfToken));
                showNotif((response.sukses == 1 ? 'success' : 'error'), response.pesan);
                if (response.sukses == 1) {
                    tbl.ajax.reload();
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                showError(thrownError);
            }
        })
    }
</script>
```

### 11.6 Langkah 5 — View Form `v_form.php`

```php
<?= $this->include('template/v_header') ?>
<?= $this->include('template/v_appbar') ?>
<div class="main-content content margin-t-4">
    <div class="card p-x-2 p-y-2 shadow-sm w-100 rounded margin-t-18p">
        <div class="card-header dflex justify-end">
            <button onclick="return toPage('<?= getURL('division') ?>')" class="btn btn-danger margin-r-2 dflex align-center p-x"><i class='bx bx-left-arrow-alt margin-r-2'></i> Back</button>
            <button type="button" id="btn-subs" onclick="return subs()" class="btn btn-primary dflex align-center p-x"><i class="bx bx-save margin-r-2"></i><?= ($form_type == 'edit' ? 'Update' : 'Save') ?></button>
        </div>
        <div class="card-body margin-t-14p" id="tab-form">
            <form id="form-division" style="padding-inline: 0px;">
                <input type="hidden" name="id" value="<?= (($form_type == 'edit') ? encrypting($row['divisionid']) : '') ?>">
                <div class="form-group">
                    <label>Division Name<span class="text-danger">*</span> :</label>
                    <input type="text" class="form-input fs-7" name="divisionname" value="<?= (($form_type == 'edit') ? $row['divisionname'] : '') ?>" placeholder="ex: Finance" required>
                </div>
                <input type="hidden" id="csrf_token_form" name="<?= csrf_token() ?>">
            </form>
        </div>
    </div>
</div>
<?= $this->include('template/v_footer') ?>
<script>
    function subs() {
        $('#form-division').trigger('submit');
    }

    $('#form-division').submit(function(e) {
        e.preventDefault();
        let form_type = "<?= $form_type ?>"
        let link = "<?= getURL('division/add') ?>"
        if (form_type == 'edit') {
            link = "<?= getURL('division/update') ?>"
        }
        let old_html = $('#btn-subs').html();
        $('#btn-subs').html('<i class="bx bx-loader bx-spin"></i>');
        $('#btn-subs').attr('disabled', 'disabled');
        $("button.btn").attr('disabled', 'disabled');
        let csrf = decrypter($("#csrf_token").val());
        $("#csrf_token_form").val(csrf);
        let data = $('#form-division').serialize();
        $.ajax({
            type: 'post',
            url: link,
            data: data,
            dataType: 'json',
            success: function(response) {
                $('#btn-subs').html(old_html);
                $('#btn-subs').removeAttr('disabled');
                $("button.btn").removeAttr('disabled');
                $("#csrf_token").val(encrypter(response.csrfToken));
                $("#csrf_token_form").val('');
                let pesan = response.pesan;
                let notif = 'success';
                if (response.sukses != 1) {
                    notif = 'error';
                }
                if (response.pesan != undefined) {
                    pesan = response.pesan;
                }
                showNotif(notif, pesan);
                if (response.sukses == 1) {
                    window.location.href = "<?= getURL('division') ?>"
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                $('#btn-subs').html(old_html);
                $('#btn-subs').removeAttr('disabled');
                $("button.btn").removeAttr('disabled');
                showError(thrownError + ", please contact administrator for the further");
            }
        })
        return false;
    })
</script>
```

### 11.7 Langkah 6 — Test

1. Login sebagai user dengan akses menu Division.
2. Cek halaman list → datatable tampil dengan No, Division, Company, Status, Action.
3. Tombol Add New → form → simpan → kembali ke list → data muncul.
4. Klik edit → ganti nama → Update → data berubah.
5. Klik delete → modal konfirmasi → data terhapus (tabel referensi memblokir jika dipakai).
6. Endpoint select2 `division/getdivision` mengembalikan `{data:[{id,text}],csrfToken}`.

---

## 12. Checklist Kepatuhan (Style Checklist)

Sebelum menyelesaikan fitur / modul, pastikan semua poin berikut terpenuhi:

### PHP / Struktur
- [ ] File controller di folder yang benar (`master/`, `ess/`, `report/`, dll) dengan namespace sesuai.
- [ ] Class `StudlyCase`, method `camelCase`, variabel `camelCase`.
- [ ] Controller extends `BaseController`; `$this->db` dipakai dari `initController`.
- [ ] `__construct()` memanggil `sessionMenu('<segmen route>')` + `setArrayAccess(...)`.
- [ ] Semua query ada di Model, bukan controller.
- [ ] Model pakai `$table = 'nama as a'`, join alias huruf, filter `getSession('companyid')`.
- [ ] Model datatable return **builder**, dan method `searchable()` ada jika perlu search.

### Routes
- [ ] Route group mengikuti pola: `''`, `table`, `form`, `form/(:any)`, `add`, `update`, `delete`.
- [ ] Endpoint select2 pakai prefix `get...` + filter `$this->auth`.
- [ ] Segmen URL route == argumen `sessionMenu(...)`.

### View
- [ ] Diawali `v_header` + `v_appbar`, isi di dalam `main-content`, diakhiri `v_footer` + `<script>`.
- [ ] Pakai short echo `<?= ... ?>`, tidak pakai `<?php echo`.
- [ ] Tabel list ber-class `table-master` (otomatis di-handle `tbl` global).
- [ ] Tombol berdasarkan akses: `<?php if ($akses["COMPO_" . COMADD]) : ?>`.
- [ ] Tidak menambah CSS/JS library baru di halaman (semua via `v_import.php`).

### AJAX & Keamanan
- [ ] Setiap AJAX POST menyertakan token CSRF (`decrypter($("#csrf_token").val())`).
- [ ] Setiap response menyertakan `csrfToken` baru dan di-update via `encrypter(res.csrfToken)`.
- [ ] Simpan/ubah/hapus memakai `transBegin`/`transCommit`/`transRollback`/`transComplete`.
- [ ] Setiap action simpan/ubah/hapus cek `$akses["COMPO_" . ...]`.
- [ ] ID sensitif dikirim terenkripsi (`encrypting`) dan di-decrypt di controller (`decrypting`).
- [ ] Delete memakai `validateDeleteData()` untuk cek referensi FK.

### Select2
- [ ] Pakai `generateSelect2(...)` global (jangan inisialisasi select2 manual).
- [ ] Select2 di dalam modal set `dropdownParent` ke id modal.
- [ ] Endpoint select2 mengembalikan `{ data: [{id, text}], csrfToken }`.

### Konsistensi Umum
- [ ] Pesan notifikasi: bahasa Indonesia, singkat dan jelas.
- [ ] Indentasi 4 spasi, PHP tanpa closing tag `?>`.
- [ ] Tidak ada kode yang di-comment-out / debug `console.log` (kecuali memang sengaja).
- [ ] Tidak ada perubahan di file yang tidak terkait fitur.
