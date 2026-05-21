# External Integrations

> **Heredado de Aspen — VERIFICAR ANTES DE USAR** — esta página fue copiada del DMS de Aspen. Las credenciales, URLs y endpoints (AFIP, Ford Agenda, GUDB, WSpooler, SMTP) son de Aspen y NO aplican a Sauma. Sauma puede tener su propio set de integraciones (verificar acturaelectronica/ y class.config.php) o ninguna.

**Analysis Date:** 2026-03-25

## APIs & External Services

**AFIP (Argentine Tax Authority):**
- Electronic Invoice Service (Factura Electrónica)
  - SDK/Client: NuSOAP (custom SOAP wrapper)
  - Implementation: `library/class.facturaelectronica.php`
  - Auth: X.509 certificate-based (client certificate)
  - Endpoints (dual):
    - Testing: `https://wsaahomo.afip.gov.ar/ws/services/LoginCms`
    - Production: `https://wsaa.afip.gov.ar/ws/services/LoginCms`
  - Facturation: `https://wswhomo.afip.gov.ar/wsfev1/service.asmx?WSDL` (testing)
  - Facturation: `https://servicios1.afip.gov.ar/wsfev1/service.asmx?WSDL` (production)
  - Configuration: `library/class.configuracionfactura.php`
  - Credentials:
    - CUIT: `30712121870` (ASPEN MOTORS S.A.)
    - Certificates: `aspen_nuevo.crt`, `aspen_nuevo.key` (production)
    - Certificates: `aspen_prueba.crt`, `aspenprueba.key` (testing)
  - Mode: Toggle via `ConfiguracionFactura::Testing`

**Ford Agenda (Service Scheduling):**
- Service appointment synchronization
  - SDK/Client: Custom OAuth2 + SOAP integration
  - Implementation: `library/class.turnosagendaws.php`, `library/class.turnoagendaws.php`
  - Auth: OAuth2 (Azure AD)
  - Token endpoint: `https://login.microsoftonline.com/azureford.onmicrosoft.com/oauth2/v2.0/token`
  - Client ID (production): `87d83120-37ab-412b-99a4-79d6279285fb`
  - Client ID (QA): `beb9e6a4-2302-4841-ab9b-a5a5193db8ea`
  - Secrets stored in: `library/class.config.php.example` (lines 57-60)
  - Testing flag: `Config::AgendaFordTesting` (line 55)
  - SOAP certificates: Located in `agenda_ford/wse-php-master/examples/cert/`
  - Database storage: `TB_TurnosAgendaWS` (appointment imports)

**GUDB (Global Used Vehicle Database):**
- Vehicle service recommendations and technical data
  - Implementation: `library/class.codigosgudb.php`, `library/class.gudbserviceappreport.php`
  - Configuration in `library/class.config.php`:
    - `CodigoPais`: 'ARG'
    - `DealerPAyACode`: '00244' (Campana location)
    - `DealerPAyACodePilar`: '60834' (Pilar location)
    - `GUDBVendorID`: 'CROS01'
  - Database tables: `TB_CodigosGUDB`, `TB_RubrosGUDB`

## Data Storage

**Databases:**
- MySQL 5.7
  - Connection: `library/class.db.php`
  - Host: `db` (Docker) or configurable
  - Port: 3307 (Docker mapped), 3306 (internal)
  - User: `root` (Docker) or `root`
  - Database name: `benelli_com_ar`
  - Character set: `latin1` (case-insensitive, `lower_case_table_names=1`)
  - Client: mysqli extension (PHP 5.6)
  - ORM/Access Pattern: Custom DBAccess base class in `library/class.dbaccess.php`
  - Key tables:
    - `TB_TurnosAgendaWS` - Ford Agenda service appointments
    - `TB_CodigosGUDB` - GUDB service codes
    - `TB_FacturasElectronicas` - Generated invoices (logged)
    - `TB_Clientes` - Customer records
    - `TB_Articulos` - Inventory items
    - `TB_OrdenesTrabajos` - Work orders
    - `TB_Ventas` - Sales transactions

**File Storage:**
- Local filesystem only
  - Generated documents: `_recursos/comprobantes/` (invoices/receipts)
  - Uploaded resources: `_recursos/` (various file types)
  - Temporary uploads: `_recursos/` (user uploads)

**Caching:**
- None detected - no Redis or Memcached integration
- In-memory caching via AFIP Token (FacturaElectronica class)
  - Token file: `ta.xml` (credentials cache)

## Authentication & Identity

**Auth Provider:**
- Custom session-based authentication
  - Implementation: `library/class.session.php`
  - Session storage: PHP native `$_SESSION`
  - Login mechanism: `Session::Login($usuario, $password)` against `TB_Usuarios`
  - Current user: `Session::GetCurrentUser()` or `$_SESSION['IdUsuario']`

**OAuth2 (External - Ford Agenda only):**
- Azure AD OAuth2 flow
  - Endpoints configured in `Config::AgendaFordLoginUrl`
  - Client credentials in `library/class.config.php`
  - Token refresh: Handled during appointment sync

## Monitoring & Observability

**Error Tracking:**
- None detected
- Errors logged to file system via xdebug or PHP error log

**Logs:**
- PHP error log: `/var/log/php/` (Docker volume mount)
- Database logging: `TB_LogsDB` table for query auditing
- Electronic invoice logs: `TB_LogFacturaElectronica`, `TB_LogsFacturaElectronica`
- WSpooler printer logs: Not exposed (handled by printer device)

**Debug Mode:**
- xdebug 2.5.5 installed in Docker (can be enabled for remote debugging)
- NuSOAP debug level: Configurable via `$GLOBALS['_transient']['static']['nusoap_base']['globalDebugLevel']`

## CI/CD & Deployment

**Hosting:**
- Docker containerized (development)
- Apache 2.x on Linux (production assumed)
- No cloud provider specified (on-premises likely)

**CI Pipeline:**
- None detected in codebase
- Manual Docker build: `docker-compose up -d`

**Deployment Process:**
- Manual via Docker Compose
- Database migrations: SQL scripts in `sql/cambios-basedatos.sql`
- No automated testing pipeline detected

## Environment Configuration

**Required env vars:**
- None - PHP configuration uses class constants instead of environment variables
- All sensitive data hardcoded in `library/class.config.php` (security risk)

**Config Location:**
- `library/class.config.php` (main configuration)
- `library/class.configuracionfactura.php` (AFIP invoice setup)
- Docker environment: `docker-compose.yml` (MySQL root password: `root`)

**Critical Configs (All Hardcoded):**
- SMTP credentials: `Config::SMTPServer`, `Config::SMTPUser`, `Config::SMTPPassword`
- AFIP certificates: Path in `ConfiguracionFactura`
- Ford Agenda OAuth: `Config::AgendaFordClientId`, `Config::AgendaFordClientSecret`
- Database connection: `Database_Host`, `Database_User`, `Database_Pass`
- WSpooler printer: `192.168.20.48:1000`

## Webhooks & Callbacks

**Incoming:**
- None detected
- Scheduled imports only (Ford Agenda, GUDB)

**Outgoing:**
- Email notifications via SMTP
  - SMTP configuration: `Config::SMTPServer` (Gmail)
  - Recipients: Hardcoded email lists in config
    - `Config::EmailsRepuestos` - Parts department
    - `Config::EmailsRepuestosPilar` - Pilar branch
    - `Config::CorreoAdministrador` - Admin email
- Fiscal printer output via WSpooler protocol (network socket)

## External Print Devices

**Fiscal Printers:**
- Device: Connected via WSpooler (IFDRIVERS)
- Connection: TCP socket `192.168.20.48:1000`
- Implementation: `library/WSpooler/WSpooler.php`
- Used by: `library/class.generadordocumentos.php` and related generators
- Commands: Custom ASCII protocol with separators (fiscal receipt format)
- Supported operations:
  - Customer data (@SetCustomerData)
  - Receipt opening/closing
  - Item printing
  - Total/subtotal calculation
  - VAT calculations
  - Perception amounts

## Data Exchange Formats

**Invoice Generation:**
- Electronic: AFIP XML format (via NuSOAP)
- Print: Custom ASCII format for fiscal devices
- PDF: HTML-to-PDF via mPDF

**Appointment Sync:**
- Ford Agenda: SOAP XML format
- Local storage: SQL (TB_TurnosAgendaWS)

**Excel Export:**
- PHPExcel format (XLSX/XLS)
- Custom CSV export via `library/excel_export/`

---

*Integration audit: 2026-03-25*