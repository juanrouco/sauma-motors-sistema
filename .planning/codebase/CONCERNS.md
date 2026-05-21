# Concerns

> **Heredado de Aspen** — los riesgos del stack legacy (PHP 5.6 EOL, sin prepared statements, charset latin1, jQuery 1.7) aplican igual en Sauma. Las menciones específicas a integraciones (AFIP, etc.) verificar si aplican.

## Security

### SQL Injection (Critical)
No prepared statements anywhere in the codebase. All SQL queries use string concatenation:
```php
// src/library/class.acreedores.php - typical pattern
$sql = "SELECT * FROM TB_Acreedores WHERE IdAcreedor = $id";
```
User input flows from `$_REQUEST` through modules to queries without parameterization. The `DBAccess` base class and `DB` class provide no escaping helpers.

### AJAX Endpoint Auth Bypass (skip, non-important)
`src/xml/xmlhelper.php` has the authentication check **commented out** (lines 25-36):
```php
/*
if (!Session::GetCurrentUser()) {
    // ... return error
    exit;
}
*/
```
Any unauthenticated request can execute module commands.

### Hardcoded Credentials
- Database credentials in `src/library/class.config.php` (root/root)
- AFIP certificates and keys stored in `src/facturaelectronica/` (`.crt`, `.key` files committed to repo)

### Information Disclosure
- `test.php` and `testmysql.php` exposed in production
- Error handler in `xmlhelper.php` prints raw error strings to output
- `error_reporting(E_ERROR | E_PARSE)` still shows parse errors

## Technical Debt

### PHP 5.6 End of Life
PHP 5.6 reached EOL in December 2018. No security patches since then. Upgrade blocked by:
- Use of deprecated `__autoload()` (replaced by `spl_autoload_register()` in PHP 7)
- Potential use of `mysql_*` functions (removed in PHP 7)
- `register_globals` handling in `src/inc_library.php` (removed in PHP 5.4 but code still checks for it)
- Third-party libraries (PHPExcel, old PHPMailer 2.3, WSpooler, PEAR) may not be PHP 7 compatible

### No Dependency Management
- No Composer — all libraries manually copied into `src/library/`
- PHPExcel (abandoned, replaced by PhpSpreadsheet)
- PHPMailer 2.3 (from ~2007, current version is 6.x)
- WSpooler (print spooler, likely deprecated)
- PEAR library bundled in `PEAR/`

### Charset Issues
- Database uses `latin1` charset, not UTF-8
- XML responses use `iso-8859-1` encoding
- Risk of data corruption with special characters (accents, etc.)

### No Code Organization
- 528 files flat in `src/library/` with no subdirectories/namespaces
- Entity and repository classes mixed with utility classes and third-party libraries
- No PSR standards followed

## Performance

### Database Connection Management
- Each `DBAccess` subclass potentially opens its own connection
- No connection pooling
- No persistent connections configured

### N+1 Query Patterns
Repository classes typically load related entities one at a time in loops rather than using JOINs or batch queries.

### No Caching
- No application-level caching (no Redis, Memcached, or file cache)
- No query result caching
- Company data (`DatosEmpresa`) loaded on every page request via `src/inc_library.php`

### Unbounded File Storage
- `src/_recursos/` grows without limits (uploaded files, generated invoices)
- No cleanup mechanism for old files

## Fragile Areas

### AFIP Electronic Invoicing
- `src/facturaelectronica/` — Certificates have expiration dates, renewal is manual
- SOAP-based integration is brittle (XML request/response templates)
- Financial/legal compliance — errors here have regulatory consequences

### Work Order Workflow
- Complex state transitions without a formal state machine
- Status changes scattered across multiple files
- No validation that transitions are valid

### Inventory/Stock Management
- Stock adjustments in `src/library/class.articulostock*.php`
- No transaction protection for stock movements
- Multiple stock-related classes suggest complex/fragile logic: `articulostock`, `articulostockhistorico`, `articulostocks`, `articulostocks2`

### Cash Register Operations
- Multiple related classes: `caja`, `cajaapertura`, `cajadetalle`, `cajamovimiento`, `cajamovimientopago`, `cajagestoria`
- Financial calculations without proper decimal handling
- No audit trail beyond database records

## Scaling Limits

- **Single server architecture** — No horizontal scaling capability
- **Session-based auth** — Cannot scale to multiple app servers without sticky sessions
- **File-based uploads** — Stored on local filesystem, not object storage
- **No background jobs** — Everything runs synchronously in request cycle
- **MySQL 5.7** — Adequate but no read replicas or sharding

## Dependencies at Risk

| Dependency | Status | Risk |
|------------|--------|------|
| PHP 5.6 | EOL since 2018 | No security patches, blocks modern tools |
| PHPExcel | Abandoned | Replaced by PhpSpreadsheet, no updates |
| PHPMailer 2.3 | Severely outdated | Known vulnerabilities, current is 6.x |
| WSpooler | Likely deprecated | Windows print spooler integration |
| PEAR | Legacy | Mostly replaced by Composer packages |
| jQuery 1.7 | Very outdated | Known XSS vulnerabilities, current is 3.x |
| Prototype.js | Effectively abandoned | No updates, conflicts with modern JS |

## Known Bugs (from code inspection)

1. **Possible typo in DB class** — References to `$this->oConn` vs `$this-oConn` (missing `>`) would cause silent failures
2. **Error handler duplication** — `xmlhelper.php` error handler prints error string 3 times (lines 78-80)
3. **Register globals cleanup** — `src/inc_library.php` unsets `$_SESSION` variables which could break session handling if `register_globals` were somehow enabled