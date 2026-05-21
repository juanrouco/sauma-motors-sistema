# Architecture

> **Heredado de Aspen** — el patrón arquitectónico aplica tal cual. Los nombres de archivos/clases mencionados también, salvo que el rubro requiera distintas entidades.

## Overview

Legacy three-tier PHP 5.6 DMS (Dealer Management System) for automotive dealerships. Monolithic architecture with no framework — custom-built routing, data access, and session management.

## Architectural Pattern

**Server-rendered MVC-like** with AJAX augmentation:
- **View**: PHP pages in `_admin_/` generating HTML directly
- **Controller**: `xml/xmlhelper.php` dispatches AJAX commands to modules
- **Model**: Entity classes + Data Access classes in `library/`

No formal MVC framework. Pages mix presentation and logic. AJAX modules provide cleaner separation.

## Layers

### 1. Entry Points

| Entry Point | Path | Purpose |
|-------------|------|---------|
| Main redirect | `index.php` | Redirects to `/_admin_/` |
| Admin panel | `_admin_/index.php` | Login page, main UI |
| AJAX dispatcher | `xml/xmlhelper.php` | Routes AJAX requests to module commands |
| Web services | `webservice/` | External API endpoints (usados) |
| PDF generation | `pdf.php` | PDF output endpoint |

### 2. Presentation Layer (`_admin_/`)

~1036 PHP files. Each entity follows the ABM (Alta-Baja-Modificacion) pattern:
- `{entidades}.php` — List/grid with filtering and pagination
- `{entidades}_add.php` — Create form
- `{entidades}_mod.php` — Edit form
- `{entidades}_del.php` — Delete confirmation
- `{entidades}_detail.php` — Read-only view (optional)
- `{entidades}_exportar.php` — Excel export (optional)
- `{entidades}_pdf.php` — PDF generation (optional)

Pages include `inc_library.php` which initializes session, autoloader, permissions, and company data.

### 3. AJAX Module Layer (`modules/`)

56 module files. Each module maps to an entity and exposes commands:

```php
// modules/clientes.php
class ModuleClientes {
    public function BuscarPorCuit($params) { ... }
    public function OtroComando($params) { ... }
}
```

Dispatcher (`xmlhelper.php`) loads module dynamically via `Modules::LoadModule($ModuleName)`, validates command exists, collects `$_REQUEST` params, and calls the method. Response is XML (or HTML if `$_REQUEST['html'] == '1'`).

### 4. Data Access Layer (`library/`)

528 files. Two-class pattern per entity:

- **Entity class** (`class.{entidad}.php`): Plain data object with public properties, constructor initializes defaults
- **Repository class** (`class.{entidades}.php`): Extends `DBAccess`, provides `GetAll()`, `GetById()`, `Insert()`, `Update()`, `Delete()`

### 5. Infrastructure Classes

| Class | File | Role |
|-------|------|------|
| `DB` | `class.db.php` | Raw MySQL connection, query execution |
| `DBAccess` | `class.dbaccess.php` | Base class for repositories, wraps DB |
| `Session` | `class.session.php` | Authentication, session management, permission checks |
| `Modules` | `class.modules.php` | Dynamic module loading for AJAX |
| `Filtro` | `class.filtro.php` | SQL WHERE clause builder for list pages |
| `Utiles` | `class.utiles.php` | Utility functions |
| `Config` | `class.config.php` | Database credentials, system constants |

## Data Flow

### Page Request Flow
```
Browser → Apache → _admin_/{page}.php
  → require inc_library.php
    → __autoload() loads classes from library/
    → Session::Initialize()
    → Session::GetCurrentUser()
    → Permission check
  → Instantiate repository class (e.g., new Clientes())
  → Execute query (e.g., GetAll($filtro))
  → Render HTML with data
```

### AJAX Request Flow
```
Browser → SendXMLRequest(module, command, callback, params)
  → POST to xml/xmlhelper.php
    → Session::Initialize()
    → Modules::LoadModule($ModuleName)
    → Validate command exists (method_exists)
    → Collect $_REQUEST params
    → Call $Module->$CommandName($Params)
    → Return XML response
  → JavaScript callback processes response
```

## Authentication & Authorization

- Session-based authentication via `Session::Login()` / `Session::Logout()`
- Permission definitions in `inc_perms.php`
- Permission checks via `Session::CheckPerm()` in pages
- AJAX endpoint has auth check commented out (security concern)

## Key Subsystems

### Electronic Invoicing (AFIP)
- `facturaelectronica/` — Certificates, request/response templates
- Integration with Argentina's AFIP tax authority for electronic invoicing
- Uses SOAP/XML for communication

### Scheduler
- `scheduller/` — Client-side scheduling library (DHTMLX-based)
- Used for service appointment management

### Ford Integration
- `agenda_ford/` — Integration with Ford's dealer systems
- WSO2 WSF PHP for SOAP web services

### Web Services
- `webservice/` — Exposes endpoints for used vehicle management
- `usados.php`, `usados_liberar.php`, `usados_pisar.php`

## Autoloading

Custom `__autoload()` in `inc_library.php`:
```php
function __autoload($ClassName) {
    $ClassFile = 'class.' . strtolower($ClassName) . '.php';
    $Path = '../library/';
    if (file_exists($Path . $ClassFile))
        require_once($Path . $ClassFile);
}
```

All class files must follow naming convention `class.{lowercasename}.php` in `library/`.