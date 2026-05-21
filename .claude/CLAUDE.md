# CLAUDE.md - Sauma Motors (DMS de Motos)

## Descripción General

Sistema de gestión para concesionaria de **motos** (Sauma Motors). Construido sobre la misma base que el DMS legacy de Aspen Motors (sistema para concesionarias automotrices), adaptado al rubro de motos. Gestiona ventas, órdenes de trabajo, facturación, inventario y reportes.

Base de datos del proyecto: `benelli_com_ar` (Benelli es la marca de motos comercializada).

> **Origen:** la arquitectura, convenciones de código y patrones de este proyecto provienen del DMS de Aspen (`aspen-sistema`). Las skills, estructura de capas y reglas de stack legacy son **idénticas**. El layout del repo también: el código vive bajo `src/`. Las integraciones externas (AFIP, etc.) pueden no estar todas presentes o tener distintas credenciales que en Aspen.

## Stack Tecnológico

- **PHP**: 5.6 (sin Composer, includes manuales con `require_once`)
- **MySQL**: 5.7
- **JavaScript**: jQuery (operador `$j`) + Prototype.js (operador `$`)
- **Servidor**: Apache con mod_rewrite
- **Docker**: Entorno de desarrollo containerizado

## Entorno Docker

```bash
docker compose up -d

# Contenedores:
# - sauma_web (PHP 5.6 + Apache)  - puerto 8080  -> http://localhost:8080
# - sauma_db  (MySQL 5.7)          - puerto 3308  (mapeado a 3306 interno)
# - sauma_pma (phpMyAdmin)         - puerto 8081  -> http://localhost:8081

docker exec -it sauma_db mysql -uroot benelli_com_ar
docker exec -it sauma_web bash
```

**Credenciales MySQL (desarrollo):** usuario `root`, sin contraseña, base `benelli_com_ar`.

El mount del compose es `./src:/var/www/html` — el código de aplicación se sirve desde `src/`.

## Estructura de Directorios

```
/
├── Dockerfile
├── docker-compose.yml
├── .claude/                    # Documentación y skills para Claude
├── .planning/                  # Análisis del codebase
├── src/                        # Código de la aplicación (servido por Apache)
│   ├── index.php               # Redirige a _admin_/index.php
│   ├── index.html              # Fallback de redirect
│   ├── inc_library.php         # Include principal: autoloader, sesión, permisos
│   ├── inc_library_includes.php
│   ├── inc_perms.php           # Definición de permisos
│   ├── pdf.php                 # Endpoint de PDFs
│   ├── _admin_/                # Panel de administración (ABMs)
│   ├── _recursos/              # Archivos subidos y recursos generados
│   ├── library/                # Clases PHP (entidades y acceso a datos)
│   ├── modules/                # Módulos para xmlhelper (acciones AJAX)
│   ├── xml/                    # xmlhelper.php - endpoint AJAX
│   ├── api/                    # Endpoints adicionales (si aplica)
│   ├── css/, js/, images/, imagenes/
│   ├── facturaelectronica/     # Facturación electrónica AFIP (si está habilitada)
│   ├── PEAR/                   # PEAR PHP library
│   ├── thumbnail/              # Image thumbnail library
│   └── sql/
│       └── init/               # *.sql que se ejecutan al crear la BD por Docker
```

## Convenciones de Naming

| Componente       | Convención                    | Ejemplo                     |
|------------------|-------------------------------|-----------------------------|
| Archivo entidad  | singular, minúsculas          | `class.acreedor.php`        |
| Archivo datos    | plural, minúsculas            | `class.acreedores.php`      |
| Clase entidad    | PascalCase singular           | `Acreedor`                  |
| Clase datos      | PascalCase plural             | `Acreedores`                |
| Tabla BD         | Prefijo `TB_`, PascalCase     | `TB_Acreedores`             |
| Primary Key      | `Id{Singular}`                | `IdAcreedor`                |
| Foreign Key      | `FK_{Tabla}_{Campo}`          | `FK_Acreedores_IdPais`      |

## Clases Importantes

| Clase      | Archivo                          | Descripción                              |
|------------|----------------------------------|------------------------------------------|
| `Config`   | `src/library/class.config.php`   | Configuración del sistema (DB, URLs)     |
| `DB`       | `src/library/class.db.php`       | Conexión y queries a base de datos       |
| `DBAccess` | `src/library/class.dbaccess.php` | Clase base para acceso a datos           |
| `Session`  | `src/library/class.session.php`  | Manejo de sesiones y autenticación       |
| `Filtro`   | `src/library/class.filtro.php`   | Constructor de filtros para queries      |
| `Utiles`   | `src/library/class.misc.php` o `class.utiles.php` | Funciones utilitarias |

## Conexión a Base de Datos

Configurada en [src/library/class.config.php](../src/library/class.config.php):
- host: `db` (nombre del servicio Docker)
- user: `root`
- pass: *(vacía)*
- database: `benelli_com_ar`
- port: `NULL`

## Convenciones SQL

- **Tablas**: `TB_{NombreEnPlural}` (ej: `TB_Clientes`, `TB_OrdenesTrabajos`)
- **Primary Keys**: `Id{NombreEnSingular}` (ej: `IdCliente`)
- **Charset**: `latin1` (cuidado con caracteres especiales)
- **Engine**: `InnoDB`

## Migraciones SQL

Para inicializar la BD desde un dump:
1. Copiar el archivo `.sql` a [src/sql/init/](../src/sql/init/)
2. Recrear el contenedor de BD: `docker compose down -v; docker compose up -d`

O bien importar manualmente vía phpMyAdmin en http://localhost:8081 (usuario `root`, sin contraseña).

Para aplicar cambios incrementales:
```bash
docker exec -i sauma_db mysql -uroot benelli_com_ar < ruta/al/script.sql
```

## Diferencias clave con Aspen

| Aspecto | Aspen | Sauma Motors |
|---------|-------|--------------|
| Rubro | Autos | Motos |
| Layout código | `src/...` | `src/...` (igual) |
| Container PHP | `php56_app` | `sauma_web` |
| Container DB | `php56_db` | `sauma_db` |
| Container PMA | `php56_pma` | `sauma_pma` |
| DB nombre | `legacy_db` | `benelli_com_ar` |
| DB user/pass | `root`/`root` | `root`/`` (vacía) |
| Puerto MySQL host | 3307 | 3308 |
| Integraciones | AFIP + Ford Agenda + GUDB | A verificar (`facturaelectronica/` existe; agenda Ford no aplica) |

## Skills disponibles

Las skills en [skills/](skills/) son las heredadas del DMS de Aspen y se aplican tal cual en este proyecto:

- **legacy-caveats** — Reglas críticas del stack legacy (PHP 5.6, latin1, $j, etc.)
- **create-entity** — Genera entidad + acceso a datos + tabla SQL
- **create-abm-pages** — Genera páginas ABM (alta/baja/modificación)
- **create-ajax-module** — Crea módulo AJAX para xmlhelper
- **crear-reporte** — Crea página de reporte con filtros y exportación
- **refactor-reporte** — Audita y refactoriza un reporte existente
