# Backend codebase reference (CodeIgniter 4 POS)

Deep, factual reference for AI agents and developers. Last verified: 2026-09-23.
If you modify code that alters any architecture, models, routes, or services documented here, update this file in the same change.
Operational instructions and boundaries: [`AGENTS.md`](./AGENTS.md).

> Staleness rule. If this file is older than 4 weeks, verify schema and routes against `app/Config/Routes.php` and `app/Database/Migrations/` before trusting it.

## 1. Stack

| Layer | Technology | Details |
|---|---|---|
| Framework | CodeIgniter 4.7 | PHP 8.2+ supported, running on PHP 8.5 |
| Web server | Apache (XAMPP) | Document root points to project root, public entry via `public/index.php` |
| Database | PostgreSQL | Database: `kasir_toko`, schema: `public`, port: 5432, driver: `Postgre` |
| Frontend styles | Tailwind CSS | Utility classes compiled to `public/css/tailwind.css` |
| UI icons | Bootstrap Icons | SVG and webfont icons (`bi bi-*`) |
| Client scripts | JavaScript | Vanilla JS, jQuery, DataTables, Chart.js (Omzet dashboard) |
| Authentication | Session-based | Encrypted session values via AES-256-CBC and Base62 |
| Pattern | Controller -> Service -> Model | Thin controllers, domain service orchestration, dedicated models |

## 2. Architecture (how things connect)

```text
HTTP Request (Browser Client)
  → app/Config/Routes.php (Route mapping and filter assignment)
  → app/Filters/ (CheckAccess for auth verification, IsNotLogin for guest redirects)
  → app/Controllers/ (Thin HTTP dispatcher)
      → Request input sanitization (trim, cast, getPost, getGet, getFile)
      → Form format and presence validation
      → Service Layer (app/Services/ - business rules, calculations, DB transactions)
          → Database Transactions ($db->transBegin(), $db->transCommit(), $db->transRollback())
          → Models (app/Models/ - query builders, schema bindings, Datatables source)
          → PostgreSQL Database (kasir_toko)
      → Response generation (primary_helper.php: respondAndDie() or $this->response->setJSON())
  → HTTP JSON Response (with fresh csrfToken) or rendered HTML View to Client
```

## 3. Service pattern specifications

The codebase is migrating toward a strict three-tier architecture: Controller -> Service -> Model.

### Tier separation rules

1. **Controller tier (`app/Controllers/`)**
   - Dispatches HTTP traffic only.
   - Extracts inputs via `$this->request->getPost()`, `$this->request->getGet()`, or `$this->request->getFile()`.
   - Validates input format, presence, and scalar boundaries.
   - Converts encrypted string IDs to integer IDs via `decrypting()`.
   - Delegates business workflows to domain services in `app/Services/`.
   - Catches `DomainException` for client validation messages and `Exception` for system failures.
   - Returns responses via `respondAndDie($status, $msg)` for AJAX or `view($template, $data)` for pages.
   - Never contains SQL queries, query builder calls, margin math, or transaction methods.

2. **Service tier (`app/Services/`)**
   - Coordinates domain logic and preserves business rules.
   - Accepts only primitive types and sanitized data arrays. Never accepts `$this->request` or `RequestInterface`.
   - Controls database transactions via `$this->db->transBegin()`, `$this->db->transCommit()`, and `$this->db->transRollback()`.
   - Validates domain constraints such as product stock availability, uniqueness, or referential integrity.
   - Handles physical asset storage such as uploaded receipts or profile photos.
   - Throws `DomainException` with human-readable error messages on invariant failures.
   - Returns pure data types (arrays, booleans, objects, integers) to callers.
   - Never calls `echo`, `print`, `header()`, `die`, `exit`, or `respondAndDie()`.

3. **Model tier (`app/Models/`)**
   - Manages database access for a single table.
   - Configures table names, primary keys, and searchable columns.
   - Exposes CRUD helper methods (`store`, `edit`, `destroy`, `getOne`).
   - Generates database query builder instances for server-side Datatables.
   - Never manages user sessions, multi-step business transactions, or file uploads.

### Domain service structure and refactoring roadmap

| Domain | Service file | Status | Primary responsibilities |
|---|---|---|---|
| Auth | `app/Services/Auth/AuthService.php` | Completed | Verify credentials, configure encrypted session, manage logout |
| Auth | `app/Services/Auth/ProfileService.php` | Completed | Validate username uniqueness, hash passwords, store profile photo |
| Master | `app/Services/Master/BarangService.php` | Completed | Product CRUD, compute margin (`harga_jual - harga_beli`), category lookup |
| Master | `app/Services/Master/CategoryService.php` | Completed | Category CRUD, foreign key dependency checks before deletion |
| Master | `app/Services/Master/MenuService.php` | Completed | Menu hierarchy, sequence reordering, role access assignment |
| Master | `app/Services/Master/UserService.php` | Completed | User CRUD, role association, password hashing, avatar handling |
| Master | `app/Services/Master/UsergroupService.php` | Completed | Role CRUD, menu permission tree assignment |
| Kasir | `app/Services/Kasir/KasirService.php` | Completed | Validate cart items, calculate subtotals and margins, persist transaction header and detail batch |
| Kasir | `app/Services/Kasir/RiwayatTransaksiService.php` | Completed | Date range sanitization, transaction summary metrics, top products aggregation |
| Dashboard | `app/Services/Dashboard/OmzetService.php` | Completed | Annual turnover calculation, monthly chart generation, category sales rankings |

## 4. Project structure

```text
kasir-bu-ifa/
├── app/
│   ├── Commands/
│   ├── Common.php
│   ├── Config/
│   │   ├── Database.php                               # PostgreSQL connection configuration
│   │   ├── Filters.php                                # Filter aliases: auth, noauth, checkAccess, isLogin
│   │   ├── Routes.php                                 # Central route collection
│   │   └── Services.php                               # Framework services registry
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── LoginController.php                    # Login page and authentication handling
│   │   │   └── ProfileController.php                  # User profile view and update endpoint
│   │   ├── BaseController.php                         # Base controller providing getPost, getGet, and access arrays
│   │   ├── Dashboard/
│   │   │   └── OmzetTahun.php                         # Annual omzet dashboard and AJAX data source
│   │   ├── Home.php                                   # Default fallback controller
│   │   ├── Kasir/
│   │   │   ├── Kasir.php                              # POS terminal view and transaction saving
│   │   │   └── Riwayat.php                            # Transaction history table and modal detail
│   │   └── Master/
│   │       ├── Barang.php                             # Product catalog CRUD, modal forms, Datatable source
│   │       ├── Category.php                           # Product category CRUD and dependency verification
│   │       ├── Menu.php                               # Menu management, nested sorting, sequence persistence
│   │       ├── User.php                               # User management and role assignment
│   │       └── Usergroup.php                          # Role management and permission matrix
│   ├── Database/
│   │   └── Migrations/
│   │       └── 2024-01-01-000001_CreateKasirTables.php# PostgreSQL DDL for barang, transaksi, detail_transaksi
│   ├── Filters/
│   │   ├── CheckAccess.php                            # Session check filter; redirects unauthenticated visitors
│   │   ├── IsLogin.php                                # Duplicate alias for CheckAccess
│   │   └── IsNotLogin.php                             # Guest filter; redirects authenticated users to /kasir
│   ├── Helpers/
│   │   ├── Datatables/
│   │   │   └── Datatables.php                         # Server-side Datatables builder for CI4
│   │   └── primary_helper.php                         # Session, Base62 encryption, formatters, respondAndDie
│   ├── Models/
│   │   ├── BarangModel.php                            # barang table query builder, searchable columns, CRUD
│   │   ├── CategoryModel.php                          # mscategory table access and select options
│   │   ├── DashboardModel.php                         # Aggregates annual turnover, monthly trends, category ranking
│   │   ├── DetailTransaksiModel.php                   # detail_transaksi table batch insertion
│   │   ├── Globalmodel.php                            # Referential constraint checks across multiple tables
│   │   ├── Msmenu.php                                 # msmenu hierarchy tree, active flags, sequence updates
│   │   ├── Msrole.php                                 # msrole table queries and role-menu permission map
│   │   ├── Msuser.php                                 # msuser table access, credentials, role joins
│   │   └── TransaksiModel.php                         # transaksi header queries, date range filters, summary KPI
│   ├── Services/
│   │   ├── Auth/
│   │   │   ├── AuthService.php                        # Credential verification, encrypted session, logout
│   │   │   └── ProfileService.php                     # Profile updates, password hashing, avatar storage
│   │   ├── Dashboard/
│   │   │   └── OmzetService.php                       # Annual turnover calculation, monthly chart, category rankings
│   │   ├── Kasir/
│   │   │   ├── KasirService.php                       # Cart validation, total/margin calculation, transaction persistence
│   │   │   └── RiwayatTransaksiService.php            # Transaction summary calculations, detail queries, date filtering
│   │   └── Master/
│   │       ├── BarangService.php                      # Product CRUD, margin calculation, transaction dependency validation
│   │       ├── CategoryService.php                    # Category CRUD, product dependency validation
│   │       ├── MenuService.php                        # Menu hierarchy, sequence reordering, role access assignment
│   │       ├── UserService.php                        # User CRUD, role association, password hashing, avatar handling
│   │       └── UsergroupService.php                   # Role CRUD, menu permission tree assignment
│   └── Views/
│       ├── auth/
│       │   └── v_login.php                            # Authentication view
│       ├── dashboard/
│       │   └── v_omzettahun.php                       # Annual turnover dashboard view with Chart.js
│       ├── kasir/
│       │   └── v_kasir.php                            # Interactive POS cash register interface
│       ├── master/
│       │   ├── barang/                                # Product catalog views and modal templates
│       │   ├── category/                              # Category views and modal templates
│       │   ├── menu/                                  # Navigation menu views, modal forms, nestable order
│       │   ├── user/                                  # User accounts and role selector views
│       │   └── usergroup/                             # Roles and permission checkbox matrices
│       ├── riwayat/
│       │   └── v_riwayat.php                          # Transaction log view and transaction detail modal
│       └── template/
│           ├── v_header.php                           # Head tags, meta, CSS imports
│           ├── v_navbar.php                           # Top navbar, user avatar, profile link
│           ├── v_sidebar.php                          # Dynamic sidebar generated by menu permissions
│           └── v_footer.php                           # Scripts, global modals, AJAX error handlers
├── docs/
│   └── sql/
│       └── 01_auth_and_menu.sql                       # PostgreSQL schema for msuser, msmenu, msaccessmenu
├── public/
│   ├── css/
│   │   ├── tailwind.css                               # Compiled Tailwind stylesheet
│   │   └── template.css                               # Custom application layout styles
│   ├── images/                                        # Static image assets and default avatars
│   ├── uploads/
│   │   └── profile/                                   # Uploaded user avatar images
│   └── index.php                                      # Public entry point for HTTP requests
└── tests/
    ├── database/
    │   └── ExampleDatabaseTest.php                    # Database test sample
    ├── session/
    │   └── ExampleSessionTest.php                     # Session test case
    └── unit/
        ├── AuthServiceTest.php                        # Unit tests for credential authentication and session
        ├── BarangServiceTest.php                      # Unit tests for product CRUD, margins, and dependencies
        ├── CategoryServiceTest.php                    # Unit tests for category CRUD and dependencies
        ├── HealthTest.php                             # Baseline CI4 framework health check
        ├── KasirServiceTest.php                       # Unit tests for POS shopping cart and transactions
        ├── MenuServiceTest.php                        # Unit tests for menu CRUD, sequence, and sorting
        ├── MsuserTest.php                             # Unit assertions for user model
        ├── OmzetServiceTest.php                       # Unit tests for dashboard turnover and rankings
        ├── ProfileServiceTest.php                     # Unit test for ProfileService logic
        ├── RiwayatTransaksiServiceTest.php            # Unit test for transaction date sanitization and details
        ├── UsergroupServiceTest.php                   # Unit tests for role CRUD and access permissions
        └── UserServiceTest.php                        # Unit tests for user CRUD and protections
```

## 5. Key database entities and relations

Database name: `kasir_toko` on PostgreSQL.

### Authentication and access control tables

- **`msuser`**: System user records.
  - Columns: `userid` (SERIAL, PK), `username` (VARCHAR 50, UNIQUE), `password` (VARCHAR 255, bcrypt hash), `fullname` (VARCHAR 100), `photo` (VARCHAR 255, nullable), `roleid` (INT, FK to `msrole.roleid`), `is_active` (BOOLEAN, default true), `createddate` (TIMESTAMP), `createdby` (VARCHAR), `updateddate` (TIMESTAMP), `updatedby` (VARCHAR).
- **`msrole`**: User authorization groups.
  - Columns: `roleid` (SERIAL, PK), `rolename` (VARCHAR 100), `createddate` (TIMESTAMP), `createdby` (VARCHAR), `updateddate` (TIMESTAMP), `updatedby` (VARCHAR).
- **`msmenu`**: Application navigation items.
  - Columns: `menuid` (SERIAL, PK), `menuname` (VARCHAR 100), `url` (VARCHAR 100, UNIQUE), `icon` (VARCHAR 50), `parentid` (INT, default 0 for root items), `sequence` (INT, ordering index), `is_active` (BOOLEAN, default true), `createddate` (TIMESTAMP), `createdby` (VARCHAR), `updateddate` (TIMESTAMP), `updatedby` (VARCHAR).
- **`msaccessmenu`**: Junction mapping roles to visible menus.
  - Columns: `accessid` (SERIAL, PK), `roleid` (INT, FK to `msrole.roleid` ON DELETE CASCADE), `menuid` (INT, FK to `msmenu.menuid` ON DELETE CASCADE), `createddate` (TIMESTAMP), `createdby` (VARCHAR). Unique constraint on `(roleid, menuid)`.

### Catalog and transaction tables

- **`mscategory`**: Product category groups.
  - Columns: `categoryid` (SERIAL, PK), `categoryname` (VARCHAR 100), `createddate` (TIMESTAMP), `createdby` (VARCHAR), `updateddate` (TIMESTAMP), `updatedby` (VARCHAR).
- **`barang`**: Product catalog items.
  - Columns: `id_barang` (SERIAL, PK), `nama_barang` (VARCHAR 100), `categoryid` (INT, FK to `mscategory.categoryid`), `harga_beli` (NUMERIC 12,2), `harga_jual` (NUMERIC 12,2), `margin` (NUMERIC 12,2, computed as `harga_jual - harga_beli`), `createddate` (TIMESTAMP), `createdby` (VARCHAR), `updateddate` (TIMESTAMP), `updatedby` (VARCHAR).
- **`transaksi`**: Sales transaction header records.
  - Columns: `id_transaksi` (SERIAL, PK), `tanggal_transaksi` (TIMESTAMP DEFAULT CURRENT_TIMESTAMP), `total_bayar` (NUMERIC 12,2), `total_margin` (NUMERIC 12,2).
- **`detail_transaksi`**: Individual line items for sales transactions.
  - Columns: `id_detail` (SERIAL, PK), `id_transaksi` (INT, FK to `transaksi.id_transaksi` ON DELETE CASCADE), `id_barang` (INT, FK to `barang.id_barang`), `jumlah` (INT, CHECK `jumlah > 0`), `harga_jual_satuan` (NUMERIC 12,2), `margin_satuan` (NUMERIC 12,2), `subtotal_harga` (NUMERIC 12,2), `subtotal_margin` (NUMERIC 12,2).

## 6. Route map

All route definitions reside in `app/Config/Routes.php`.

### Authentication routes
- `GET /` : Redirects guest to login via filter `isNotLogin`.
- `GET /login` : Renders the login form view via `Auth\LoginController::index`.
- `POST /login/process` : Validates credentials and creates session via `Auth\LoginController::process`.
- `GET|POST /logout` : Terminates session and redirects to login via `Auth\LoginController::logout`.
- `POST /profile/update` : Updates user profile via `Auth\ProfileController::update` (filter: `checkAccess`).

### Master Barang routes (filter: `checkAccess`)
- `GET /barang` : Product catalog index page (`master\Barang::index`).
- `POST /barang/table` : Server-side Datatables JSON endpoint (`master\Barang::datatable`).
- `GET|POST /barang/form` : Modal form view for new product (`master\Barang::forms`).
- `GET|POST /barang/form/(:any)` : Modal form view for editing product by encrypted ID (`master\Barang::forms/$1`).
- `POST /barang/add` : Endpoint to insert a product (`master\Barang::addBarang`).
- `POST /barang/update` : Endpoint to update a product (`master\Barang::updateBarang`).
- `POST /barang/delete` : Endpoint to delete a product (`master\Barang::deleteBarang`).
- `GET|POST /barang/getbarang` : Async select options for products (`master\Barang::getBarang`).
- `GET|POST /barang/getcategory` : Async select options for categories (`master\Barang::getCategory`).

### Master Category routes (filter: `checkAccess`)
- `GET /category` : Category index page (`master\Category::index`).
- `POST /category/table` : Server-side Datatables JSON endpoint (`master\Category::datatable`).
- `GET|POST /category/form` : Modal form view for new category (`master\Category::forms`).
- `GET|POST /category/form/(:any)` : Modal form view for editing category by encrypted ID (`master\Category::forms/$1`).
- `POST /category/add` : Endpoint to insert category (`master\Category::addCategory`).
- `POST /category/update` : Endpoint to update category (`master\Category::updateCategory`).
- `POST /category/delete` : Endpoint to delete category after checking product dependencies (`master\Category::deleteCategory`).

### Master Usergroup routes (filter: `checkAccess`)
- `GET /usergroup` : Roles index page (`master\Usergroup::index`).
- `POST /usergroup/table` : Server-side Datatables JSON endpoint (`master\Usergroup::datatable`).
- `GET|POST /usergroup/form` : Modal form view for new role (`master\Usergroup::forms`).
- `GET|POST /usergroup/form/(:any)` : Modal form view for editing role (`master\Usergroup::forms/$1`).
- `POST /usergroup/add` : Endpoint to insert role (`master\Usergroup::addRole`).
- `POST /usergroup/update` : Endpoint to update role (`master\Usergroup::updateRole`).
- `POST /usergroup/delete` : Endpoint to delete role (`master\Usergroup::deleteRole`).
- `GET|POST /usergroup/access/(:any)` : Permission assignment modal for role ID (`master\Usergroup::formAccess/$1`).
- `POST /usergroup/saveaccess` : Persists menu access assignments (`master\Usergroup::saveAccess`).
- `GET|POST /usergroup/getrole` : Async select options for roles (`master\Usergroup::getRole`).

### Master User routes (filter: `checkAccess`)
- `GET /user` : Users index page (`master\User::index`).
- `POST /user/table` : Server-side Datatables JSON endpoint (`master\User::datatable`).
- `GET|POST /user/form` : Modal form view for new user (`master\User::forms`).
- `GET|POST /user/form/(:any)` : Modal form view for editing user (`master\User::forms/$1`).
- `POST /user/add` : Endpoint to insert user with hashed password (`master\User::addUser`).
- `POST /user/update` : Endpoint to update user data (`master\User::updateUser`).
- `POST /user/delete` : Endpoint to delete user account (`master\User::deleteUser`).
- `GET|POST /user/role/(:any)` : Modal to modify user role assignment (`master\User::formRole/$1`).
- `POST /user/saverole` : Persists updated role assignment (`master\User::saveRole`).

### Master Menu routes (filter: `checkAccess`)
- `GET /menu` : Menu management index page (`master\Menu::index`).
- `POST /menu/table` : Server-side Datatables JSON endpoint (`master\Menu::datatable`).
- `GET|POST /menu/form` : Modal form view for new menu (`master\Menu::forms`).
- `GET|POST /menu/form/(:any)` : Modal form view for editing menu (`master\Menu::forms/$1`).
- `POST /menu/add` : Endpoint to insert menu (`master\Menu::addMenu`).
- `POST /menu/update` : Endpoint to update menu (`master\Menu::updateMenu`).
- `POST /menu/delete` : Endpoint to delete menu (`master\Menu::deleteMenu`).
- `GET|POST /menu/sort` : Drag and drop nestable menu reordering view (`master\Menu::formSort`).
- `POST /menu/saveorder` : Persists hierarchy and sequence index (`master\Menu::saveOrder`).
- `GET|POST /menu/getmenu` : Async select options for parent menus (`master\Menu::getMenu`).

### Kasir & POS terminal routes (filter: `checkAccess`)
- `GET /kasir` : Main cash register terminal page (`kasir\Kasir::index`).
- `POST /kasir/table` : Recent transactions preview datatable (`kasir\Riwayat::datatable`).
- `POST /kasir/simpan` : Processes shopping cart payload and persists sales record (`kasir\Kasir::simpan`).

### Riwayat Transaksi routes (filter: `checkAccess`)
- `GET /riwayat` : Transaction history index page with date filtering (`kasir\Riwayat::index`).
- `POST /riwayat/table` : Server-side Datatables listing with date bounds (`kasir\Riwayat::datatable`).
- `GET|POST /riwayat/detail/(:any)` : Modal view for transaction line items by encrypted ID (`kasir\Riwayat::detail/$1`).

### Dashboard Omzet routes (filter: `checkAccess`)
- `GET /omzetboard` : Annual turnover dashboard with chart and rankings (`Dashboard\OmzetTahun::index`).
- `POST /omzetboard/getdata` : Aggregated KPI and chart series data for target year (`Dashboard\OmzetTahun::getData`).

## 7. Core helpers and utilities

Located in `app/Helpers/primary_helper.php`. Loaded automatically or via `helper(['primary'])`.

### Cryptography and obfuscation
- `encrypting(string|int $teks): string` : Encrypts plaintext via CI Encrypter (AES-256-CBC) and encodes output to URL-safe alphanumeric strings via Tuupola Base62.
- `decrypting(string $teks): string|int` : Decodes Base62 and decrypts ciphertext back to plaintext or original integer. Handles fallback base64 decoding when needed.

### Session management
- `getSession(string $key): mixed` : Reads and automatically decrypts session value stored under `{$key}-hrs-session`.
- `setSession(string $key, mixed $value): void` : Encrypts value and stores it into session under `{$key}-hrs-session`.
- `removeSession(string $key): void` : Removes encrypted session entry.
- `destroySession(): void` : Destroys active user session.
- `isLoggedIn(): bool` : Verifies presence of decrypted `userid` in session.

### Formatting and display
- `idr(float|int $number): string` : Formats numbers into Indonesian Rupiah currency string (e.g. `Rp 25.000`).
- `idrHTML(float|int $number): string` : Wraps formatted Rupiah inside a span tag.
- `formatDate(string $format, ?string $date): string` : Formats DateTime string into target layout. Defaults to current timestamp when empty.

### AJAX response helper
- `respondAndDie(bool $status, string $msg): void` : Sets application/json header, outputs standard envelope containing `success`, `msg`, and fresh `csrfToken`, then calls `die;`.

### Menu and navigation generator
- `generateSidebarMenus(): array` : Resolves active role from encrypted session, queries `msmenu` through `Msmenu::getMenusByRoleTree()`, and returns ordered hierarchical menu structures.

### Referential integrity validation
- `validateDeleteData(array $tables): array` : Inspects child tables via `Globalmodel::validateData()` before deleting parent records. Prevents foreign key constraint violations and returns blocking record details.

## 8. Response and error envelope standards

All AJAX responses return standard JSON envelopes with a fresh CSRF token.

### Standard success response
```json
{
  "success": true,
  "msg": "Data berhasil disimpan.",
  "csrfToken": "4a7b9c1d2e8f..."
}
```

### Standard validation or domain failure
```json
{
  "success": false,
  "msg": "Stok barang tidak mencukupi untuk transaksi ini.",
  "csrfToken": "4a7b9c1d2e8f..."
}
```

### Session expiration response
Triggered by `app/Filters/CheckAccess.php` when unauthenticated AJAX calls occur:
```json
{
  "success": false,
  "msg": "Sesi Anda telah berakhir, silakan login kembali.",
  "redirect": "http://localhost/kasir-bu-ifa/login"
}
```

### Server-side Datatables response
Generated by `App\Helpers\Datatables\Datatables`:
```json
{
  "draw": 1,
  "recordsTotal": 120,
  "recordsFiltered": 120,
  "data": [
    [1, "Indomie Goreng", "Rp 3.500", "Rp 4.000", "Rp 500", "<button>...</button>"]
  ]
}
```
