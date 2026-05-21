# Testing

> **Heredado de Aspen** — el estado de testing (no hay testing automático) y el approach manual aplica igual en Sauma.

## Current State

**No automated testing framework exists.** This is a legacy PHP 5.6 codebase with zero automated tests.

## What Exists

### Debug/Test Files
- `test.php` — Manual test/debug file
- `testmysql.php` — MySQL connectivity verification
- `src/library/barcodegen/test_1D.php` — Barcode generation test page
- `src/library/mail/tests/` — PHPMailer library tests (third-party, not project tests)

These are manual verification pages, not automated test suites.

### No Test Infrastructure
- No PHPUnit or any testing framework
- No Composer (so no `vendor/bin/phpunit`)
- No test configuration files
- No CI/CD pipeline
- No test directories or conventions

## Testing Approach

All testing is **manual**:
- Developers test by navigating the admin panel
- Database queries tested via phpMyAdmin (port 8081)
- AJAX modules tested via browser developer tools
- No regression testing capability

## Critical Areas Needing Tests

### High Priority
1. **Financial operations** — Invoice generation, payment processing, cash register operations (`src/library/class.comprobantes*.php`, `src/library/class.cajas*.php`)
2. **AFIP electronic invoicing** — Tax authority integration, certificate handling (`src/facturaelectronica/`)
3. **Inventory management** — Stock adjustments, transfers, minimum stock alerts (`src/library/class.articulostock*.php`)
4. **Data access layer** — SQL query correctness in repository classes

### Medium Priority
5. **Work order lifecycle** — Status transitions, task assignments (`src/library/class.ordentrabajo*.php`)
6. **Customer management** — CUIT validation, duplicate detection (`src/modules/clientes.php`)
7. **Permission system** — Access control enforcement (`src/inc_perms.php`, `Session::CheckPerm()`)

### Constraints
- PHP 5.6 limits modern testing tools (PHPUnit 5.x is the last compatible version)
- No dependency injection — classes instantiate dependencies directly
- Global state via `$_SESSION` and `$_REQUEST` makes unit testing difficult
- Database coupling — all repository classes require live MySQL connection