# Structure

> **Heredado de Aspen** — la estructura general aplica, pero adaptada al hecho de que en Sauma el código vive en la **raíz** del repo (no bajo `src/`). Los conteos de archivos son aproximados de Aspen y pueden diferir en Sauma; verificar con `git ls-files | wc -l` por carpeta.

## Directory Layout

```
/
├── Dockerfile                    # PHP 5.6 + Apache image
├── docker-compose.yml            # Services: sauma_web, sauma_db, sauma_pma
├── .claude/                      # Documentación y skills para Claude
├── .planning/                    # Análisis del codebase (estos archivos)
├── index.php                     # Redirige a /_admin_/index.php
├── inc_library.php           # Main include: autoloader, session, permissions
├── inc_library_includes.php  # Additional includes
├── inc_perms.php             # Permission definitions
├── pdf.php                   # PDF generation endpoint
├── test.php                  # Test/debug file
├── testmysql.php             # MySQL connectivity test
│
├── _admin_/                  # Admin panel (ABM pages)
    │   ├── index.php             # Login page
    │   ├── access_denied.php     # Access denied page
    │   ├── {entidades}.php       # List pages
    │   ├── {entidades}_add.php   # Create forms
    │   ├── {entidades}_mod.php   # Edit forms
    │   ├── {entidades}_del.php   # Delete confirmations
    │   ├── {entidades}_detail.php    # Detail views
    │   ├── {entidades}_exportar.php  # Excel exports
    │   └── {entidades}_pdf.php       # PDF generation
    │
    ├── library/                  # PHP classes — ~528 files
    │   ├── class.{entidad}.php   # Entity classes (singular)
    │   ├── class.{entidades}.php # Data access classes (plural)
    │   ├── class.db.php          # Database connection
    │   ├── class.dbaccess.php    # Base data access class
    │   ├── class.session.php     # Session/auth management
    │   ├── class.modules.php     # AJAX module loader
    │   ├── class.config.php      # System configuration
    │   ├── class.filtro.php      # SQL filter builder
    │   ├── class.utiles.php      # Utility functions
    │   ├── PHPExcel/             # Excel generation library
    │   ├── WSpooler/             # Print spooler
    │   ├── barcodegen/           # Barcode generation
    │   ├── calendar/             # Calendar widget
    │   ├── mail/                 # PHPMailer library
    │   ├── mpdf/                 # mPDF library
    │   └── backupcompras/        # Purchase backup utilities
    │
    ├── modules/                  # AJAX modules — 56 files
    │   ├── {entidad}.php         # Module per entity
    │   └── ...                   # Each exposes commands for xmlhelper
    │
    ├── xml/                      # AJAX endpoint
    │   └── xmlhelper.php         # Central AJAX dispatcher
    │
    ├── js/                       # JavaScript files
    │   ├── jquery-1.7.0.js       # jQuery (aliased as $j)
    │   ├── effects.js            # Prototype.js effects
    │   ├── formcheck.js          # Form validation
    │   ├── fullcalendar.min.js   # Calendar component
    │   ├── chart.min.js          # Chart.js
    │   └── jquery.*.js           # jQuery plugins
    │
    ├── css/                      # Stylesheets
    │
    ├── images/                   # UI images
    ├── imagenes/                 # Additional images (Spanish naming)
    │
    ├── _recursos/                # Uploaded files and generated resources
    │   ├── comprobantes/         # Invoice/receipt files
    │   └── ...                   # Other uploaded content
    │
    ├── facturaelectronica/       # AFIP electronic invoicing
    │   ├── *.crt, *.key          # SSL certificates
    │   ├── plantillas/           # Invoice templates
    │   └── request.xml           # SOAP request template
    │
    ├── webservice/               # External web service endpoints
    │   ├── config.php            # WS configuration
    │   ├── usados.php            # Used vehicles API
    │   ├── usados_liberar.php    # Release used vehicle
    │   └── usados_pisar.php      # Override used vehicle
    │
    ├── agenda_ford/              # Ford dealer integration
    │   └── wsf-master/           # WSO2 WSF PHP library
    │
    ├── scheduller/               # DHTMLX scheduler
    │   ├── scheduler.js          # Scheduling widget
    │   └── lib/                  # Scheduler library
    │
    ├── mobile/                   # Mobile-specific pages
    │
    ├── sql/                      # SQL migrations
    │   └── cambios-basedatos.sql # Database change scripts
    │
    ├── PEAR/                     # PEAR PHP library
    ├── thumbnail/                # Image thumbnail library
    └── well-known/               # Well-known URI handlers
```

## File Counts

| Directory | Files | Description |
|-----------|-------|-------------|
| `_admin_/` | ~1036 | Admin panel pages |
| `library/` | ~528 | PHP classes and libraries |
| `modules/` | 56 | AJAX modules |
| `js/` | ~20+ | JavaScript files |

## Naming Conventions

### PHP Files
- Entity class: `class.{entidad}.php` (singular, lowercase) — e.g., `class.cliente.php`
- Data access: `class.{entidades}.php` (plural, lowercase) — e.g., `class.clientes.php`
- Admin pages: `{entidades}.php`, `{entidades}_add.php`, etc.
- Modules: `{entidad_o_entidades}.php` in `modules/`

### Classes
- Entity: PascalCase singular — `Cliente`, `OrdenTrabajo`
- Data access: PascalCase plural — `Clientes`, `OrdenesTrabajos`
- Module: `Module{Name}` — `ModuleClientes`

### Database
- Tables: `TB_{PascalCasePlural}` — `TB_Clientes`, `TB_OrdenesTrabajos`
- Primary keys: `Id{Singular}` — `IdCliente`, `IdOrdenTrabajo`
- Foreign keys: `FK_{Table}_{Column}`

## Key Entities (by admin page count)

Major entities with full ABM + exports + reports:
- **Articulos** (inventory/parts) — list, add, mod, del, export, reports, stock management
- **Unidades** (vehicles) — full CRUD + complex workflows
- **OrdenesTrabajos** (work orders) — full lifecycle management
- **Comprobantes** (invoices/receipts) — billing and fiscal documents
- **Clientes** (customers) — CRM functionality
- **Cajas** (cash registers) — financial management
- **Alquileres** (rentals) — rental management
- **Usados** (used vehicles) — used car management
