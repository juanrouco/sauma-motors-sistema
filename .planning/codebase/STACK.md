# Technology Stack

> **Heredado de Aspen** — el stack tecnológico (PHP 5.6, MySQL 5.7, jQuery + Prototype, mPDF, PHPExcel, etc.) es idéntico. Las menciones a WSpooler/AFIP/Ford son específicas de Aspen y deben verificarse para Sauma.

**Analysis Date:** 2026-03-25

## Languages

**Primary:**
- PHP 5.6 - Legacy DMS system, server-side logic and page rendering
- JavaScript (jQuery + Prototype.js) - Client-side UI interactions
- SQL (MySQL) - Data queries and schema definitions
- HTML/CSS - Frontend markup and styling

**Secondary:**
- Bash - Docker initialization and system scripts

## Runtime

**Environment:**
- PHP 5.6 (containerized via Docker, image: `php:5.6-apache`)
- Apache 2.x with mod_rewrite enabled
- MySQL 5.7 (container image: `mysql:5.7`)

**Package Manager:**
- No Composer - uses manual `require_once` includes
- PEAR packages for mail and socket operations
- Lockfile: Not applicable (no dependency manager)

## Frameworks

**Core:**
- Custom MVC-like framework (in-house implementation)
  - Entry point: `index.php`
  - Library base: `library/` with class files
  - Admin pages: `_admin_/` (CRUD interfaces)

**Frontend:**
- jQuery 1.6.2, 1.7.0 - DOM manipulation and AJAX (uses `$j` operator)
- Prototype.js - Element selection and utilities (uses `$` operator)
- jQuery UI 1.8.20, 1.10.2 custom builds - Widgets
- Full Calendar - Event scheduling UI

**PDF Generation:**
- mPDF (v5.x) - HTML-to-PDF conversion
- mPDF_old (legacy version) - Fallback PDF generation
- FPDF - Lightweight PDF generation

**Excel/Data Export:**
- PHPExcel - Excel file generation and parsing
- Custom XLS export classes in `library/excel_export/`

**Build/Dev:**
- Docker - Containerized environment
- docker-compose 3.8 - Service orchestration

## Key Dependencies

**Critical:**
- WSpooler (IFDRIVERS) - Print spooler integration for fiscal printers
  - Location: `library/WSpooler/`
  - Purpose: Network communication with fiscal devices
- NuSOAP - SOAP client for web services
  - Location: `library/class.nusoap.php`
  - Purpose: AFIP integration and other WS calls

**Infrastructure:**
- PEAR/Mail - Email transmission via SMTP
  - Location: `library/mail/`
  - Configuration: Gmail SMTP (hardcoded in config)
- phpseclib - SSH/SFTP and cryptography
  - Location: `library/phpseclib/`
  - Purpose: Secure file transfers and encryption
- phmagick - ImageMagick wrapper
  - Location: `library/phmagick/`
  - Purpose: Image manipulation
- Barcode generation - Custom barcode library
  - Location: `library/barcodegen/`

**Data Processing:**
- PHPExcel - Excel read/write
  - Location: `library/PHPExcel/`
- Excel export utility - Custom export classes
  - Location: `library/excel_export/`
- Excel reader - Custom Excel parsing
  - Location: `library/excel_reader/`

## Configuration

**Environment:**
- Configuration stored in `library/class.config.php` (generated from `class.config.php.example`)
- No `.env` file support - all config is hardcoded PHP constants
- Critical configs:
  - Database credentials (host, user, pass, database name)
  - SMTP settings (Gmail credentials)
  - AFIP fiscal invoice URLs and certificates
  - Ford Agenda OAuth credentials
  - WSpooler host/port for fiscal printer

**Build:**
- `Dockerfile` - PHP 5.6 + Apache with extensions (gd, mbstring, mysql, mysqli, pdo_mysql, xdebug)
- `docker-compose.yml` - Multi-service orchestration (PHP app, MySQL, phpMyAdmin)
- `php.ini` - Customized PHP configuration for legacy compatibility

## PHP Extensions Installed

**Database:**
- mysql (deprecated, legacy)
- mysqli (current)
- pdo_mysql

**Image Processing:**
- gd (with freetype, jpeg support)

**Text Processing:**
- mbstring (multi-byte string handling)

**Utilities:**
- sqlite3 (file-based database)
- zip (archive handling)

**Debugging:**
- xdebug 2.5.5 (compatible with PHP 5.6)

## Platform Requirements

**Development:**
- Docker and Docker Compose
- Ports available: 8080 (PHP app), 3307 (MySQL), 8081 (phpMyAdmin)
- 2+ GB RAM recommended for MySQL container

**Production:**
- Apache 2.x web server
- PHP 5.6 with all extensions listed above
- MySQL 5.7+ with `lower_case_table_names=1` and `ONLY_FULL_GROUP_BY` disabled
- Network access to:
  - AFIP web services (fiscal invoicing)
  - Ford Agenda OAuth endpoints
  - Gmail SMTP (email sending)
  - Fiscal printer (WSpooler host on local network)

**Network Configuration:**
- Fiscal printer: `192.168.20.48:1000` (WSpooler protocol)
- AFIP Testing: `wsaahomo.afip.gov.ar`
- AFIP Production: `servicios1.afip.gov.ar`
- Ford Agenda: `login.microsoftonline.com` (OAuth)

---

*Stack analysis: 2026-03-25*