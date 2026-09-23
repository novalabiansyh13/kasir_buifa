# Backend agent instructions (CodeIgniter 4 POS)

## Project context
Point of sale system for Kasir Bu Ifa.
CodeIgniter 4.7, PHP 8.2+, PostgreSQL (database name: `kasir_toko`, schema: `public`). Session authentication uses Base62 encrypted keys. Timezone is Asia/Jakarta.

## Documentation map
- `CODEBASE.md`, deep technical reference for architecture, service pattern, database schema, models, services, and route declarations. Read at session start.
- `app/Config/Routes.php`, web and AJAX route definitions.
- `app/Services/`, business logic layer and transaction boundary.
- `app/Helpers/primary_helper.php`, encryption, session, formatters, and response utilities.

## Commands cheatsheet
```bash
composer install                                                          # Install PHP dependencies
php spark serve                                                           # Start local development server
php vendor/phpunit/phpunit/phpunit tests/unit tests/session --no-coverage # Run automated test suite
php spark migrate                                                         # Run pending database migrations
php spark routes                                                          # Display registered application routes
```

## Critical rules (read first)
1. NEVER run git commit, git push, or generate commits automatically without an explicit user request.
2. NEVER write business logic, calculations, or direct database queries inside Controllers. Delegate all domain operations to `app/Services/`.
3. NEVER manage database transactions inside Controllers. Service methods control transaction lifecycles using `$db->transBegin()`, `$db->transCommit()`, and `$db->transRollback()`.
4. NEVER pass raw HTTP request objects such as `$this->request`, `$_POST`, or `$_GET` to Service methods. Controllers sanitize all inputs and pass clean arrays or scalar values.
5. NEVER return HTTP responses, call `respondAndDie()`, print output, or terminate execution inside Services. Services return plain PHP types or throw exceptions on failure.
6. ALWAYS throw `DomainException` or `Exception` in Services when business rules fail. Controllers catch exceptions and convert them into HTTP JSON responses.
7. ALWAYS use `encrypting()` and `decrypting()` from `primary_helper.php` for primary keys passed to or received from frontend views.
8. NEVER edit files inside `vendor/`.
9. Update `CODEBASE.md` whenever models, services, database tables, or routes change.

## Service pattern architecture
The application enforces a three-tier architecture across all features: Controller -> Service -> Model.

### Controller tier (`app/Controllers/`)
The controller acts strictly as an HTTP dispatcher.
- Accepts incoming HTTP input from `$this->request->getPost()`, `$this->request->getGet()`, or `$this->request->getFile()`.
- Validates data format, presence, and data types.
- Cleans and casts input values.
- Calls the corresponding Service method, passing clean arrays and primitive types.
- Catches `DomainException` or `Exception` thrown by the Service.
- Formats and outputs the HTTP response using `respondAndDie()` or `$this->response->setJSON()`.

### Service tier (`app/Services/`)
The service layer coordinates domain workflows and enforces system invariants.
- Receives clean data arrays or scalar values via method signatures.
- Coordinates multiple models to complete single business operations.
- Encapsulates database transactions. Starts transactions before mutations and rolls back when errors occur.
- Validates business rules such as stock sufficiency, price calculations, and record uniqueness.
- Handles server file operations such as moving uploaded receipts or avatars.
- Throws `DomainException` when domain validation fails or `Exception` for system errors.
- Returns plain arrays, objects, integers, or booleans to the controller.

### Model tier (`app/Models/`)
The model represents single database tables.
- Defines table schemas, primary keys, and allowed fields.
- Provides standard CRUD methods (`store`, `edit`, `destroy`, `getOne`).
- Provides reusable query builder instances for server-side Datatables.
- Avoids business logic, multi-table side effects, session access, and file operations.

## Non-default conventions (things you would get wrong)
- Controller response format. AJAX endpoints return standard JSON envelopes with boolean `success`, string message `msg`, and fresh token `csrfToken` (`csrf_hash()`).
- Session encryption. User sessions encrypt values through `encrypting()` with a `-hrs-session` suffix. Read via `getSession($key)` and write via `setSession($key, $value)`.
- Foreign key pre-checks. Deletions check child table dependencies using `validateDeleteData($tables)` and `Globalmodel::validateData()` before deleting parent records.
- Datatables integration. Server-side tables utilize `App\Helpers\Datatables\Datatables::method([Model::class, 'methodName'], 'searchable')->make()`.
- User authentication and roles. Role checking relies on `app/Filters/CheckAccess.php` and role permissions in `msaccessmenu`. Unauthenticated requests redirect to `login`.

## Git workflow
- Working branch: inspect active branch using `git status` or `git branch`.
- NEVER run `git commit`, `git push`, or create commits automatically unless explicitly requested by the user. Leave changes unstaged or staged for review.
- Always run the test suite and confirm zero failures before declaring work complete.

## Definition of done
A task is done when:
1. Domain logic resides in `app/Services/` and isolates controllers from direct database mutations.
2. Controller handles HTTP input sanitization, error interception, and standard JSON or view responses.
3. Database mutations involving multiple steps execute inside managed transactions.
4. `php vendor/phpunit/phpunit/phpunit tests/unit tests/session --no-coverage` passes with zero failures.
5. `CODEBASE.md` is updated whenever models, services, schema, or routes change.
