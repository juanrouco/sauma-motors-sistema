# Structure

> **Heredado de Aspen** — la estructura general aplica tal cual: el código vive bajo `src/`. Los conteos de archivos son aproximados de Aspen y pueden diferir en Sauma; verificar con `git ls-files src/<carpeta> | wc -l` si interesa el valor real.

## Directory Layout

```
/
├── Dockerfile                    # PHP 5.6 + Apache image
├── docker-compose.yml            # Services: sauma_web, sauma_db, sauma_pma
├── .claude/                      # Documentación y skills para Claude
├── .planning/                    # Análisis del codebase (estos archivos)
└── src/                          # Application source (mounted as /var/www/html)
    ├── index.php                 # Redirige a /_admin_/index.php
    ├── index.html                # Fallback de redirect (meta refresh)
    ├── inc_library.php           # Main include: autoloader, session, permissions
    ├── inc_library_includes.php  # Additional includes
    ├── inc_perms.php             # Permission definitions
    ├── pdf.php                   # PDF generation endpoint
    ├── test.php                  # Test/debug file
    ├── testmysql.php             # MySQL connectivity test
    │
    ├── _admin_/                  # Admin panel (ABM pages)
    │   ├── index.php             # Login page
    │   ├── access_denied.php
    │   ├── {entidades}.php       # List pages
    │   ├── {entidades}_add.php   # Create forms
    │   ├── {entidades}_mod.php   # Edit forms
    │   ├── {entidades}_del.php   # Delete confirmations
    │   ├── {entidades}_detail.php
    │   ├── {entidades}_exportar.php
    │   └── {entidades}_pdf.php
    │
    ├── library/                  # PHP classes
    │   ├── class.{entidad}.php   # Entity classes (singular)
    │   ├── class.{entidades}.php # Data access classes (plural)
    │   ├── class.db.php
    │   ├── class.dbaccess.php
    │   ├── class.session.php
    │   ├── class.modules.php
    │   ├── class.config.php
    │   ├── class.filtro.php
    │   └── ... (subcarpetas para PHPExcel, mPDF, mail, etc., según existan)
    │
    ├── modules/                  # AJAX modules (xmlhelper dispatch targets)
    ├── xml/                      # xmlhelper.php — central AJAX endpoint
    ├── api/                      # Endpoints adicionales (si aplica)
    │
    ├── js/                       # JavaScript (jQuery + Prototype.js)
    ├── css/                      # Stylesheets
    ├── images/                   # UI images
    ├── imagenes/                 # Additional images (Spanish naming)
    │
    ├── _recursos/                # Uploaded files and generated resources
    ├── facturaelectronica/       # AFIP electronic invoicing (si aplica)
    ├── PEAR/                     # PEAR PHP library
    ├── thumbnail/                # Image thumbnail library
    │
    └── sql/
        ├── init/                 # *.sql ejecutados al crear la BD por Docker
        └── ... (scripts de migración, log.log, mysql-bu.xml)
```

## Naming Conventions

### PHP Files
- Entity class: `class.{entidad}.php` (singular, lowercase) — e.g., `class.cliente.php`
- Data access: `class.{entidades}.php` (plural, lowercase) — e.g., `class.clientes.php`
- Admin pages: `{entidades}.php`, `{entidades}_add.php`, etc.
- Modules: `{entidad_o_entidades}.php` en `src/modules/`

### Classes
- Entity: PascalCase singular — `Cliente`, `OrdenTrabajo`
- Data access: PascalCase plural — `Clientes`, `OrdenesTrabajos`
- Module: `Module{Name}` — `ModuleClientes`

### Database
- Tables: `TB_{PascalCasePlural}` — `TB_Clientes`, `TB_OrdenesTrabajos`
- Primary keys: `Id{Singular}` — `IdCliente`, `IdOrdenTrabajo`
- Foreign keys: `FK_{Table}_{Column}`

## Key Entities (probables, según Aspen — verificar en Sauma)

Entidades típicas del DMS legacy con ABM completo + exports + reportes:

- **Articulos** (repuestos/inventario) — list, add, mod, del, export, reportes, stock
- **Unidades** (vehículos / motos) — CRUD + workflows
- **OrdenesTrabajos** — ciclo de vida de órdenes de servicio
- **Comprobantes** (facturas/recibos) — facturación y documentos fiscales
- **Clientes** — CRM
- **Cajas** — gestión financiera
- **Usados** — vehículos usados
