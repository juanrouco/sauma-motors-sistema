# Coding Conventions

> **Heredado de Aspen** — las convenciones de naming, estilo, error handling y JS aplican igual en Sauma. El código base es el mismo.

**Analysis Date:** 2026-03-25

## Naming Patterns

**Files:**
- Class files: `class.{entityname}.php` (all lowercase, singular for entity definition, plural for data access)
- Examples: `class.acreedor.php`, `class.acreedores.php`, `class.db.php`
- Admin pages: `{entities}.php`, `{entities}_add.php`, `{entities}_mod.php`, `{entities}_del.php`
- Modules: `{name}.php` in `modules/`

**Functions:**
- Global utility functions: PascalCase with uppercase first letter (`FormatDate`, `CantidadDiasPasados`, `IsEmail`)
- Class methods: PascalCase with uppercase first letter (`GetAll`, `GetById`, `ParseFilter`, `Insert`)
- Helper functions with underscores: snake_case with leading underscore (`_parseDate`, `_urlencode`)

**Variables:**
- Class properties: PascalCase (`$IdAcreedor`, `$RazonSocial`, `$DomicilioCalle`)
- Local variables: camelCase (`$err`, `$oAcreedor`, `$arrPaises`, `$strParams`)
- Prefix conventions: `$o` for objects, `$arr` for arrays, `$str` for strings, `$i`/`$n` for integers
- Boolean variables prefixed with `Is` or `b`: `$Submit`, `$Filter`

**Types:**
- Class names: PascalCase (`Acreedor`, `Acreedores`, `DBAccess`)
- Database table names: `TB_{EntityPluralName}` in PascalCase (`TB_Acreedores`, `TB_Clientes`, `TB_Ventas`)
- Primary keys: `Id{EntitySingularName}` (`IdAcreedor`, `IdCliente`)
- Constants in classes: UPPERCASE (`const LoginError = 0`, `const PersonaFisica = 1`)

## Code Style

**Formatting:**
- No automated formatter or linter configured
- Indentation: Tabs (appears to be mixed in legacy code)
- Brace style: Opening brace on same line (Allman style in some places)
- Line breaks: DOS line endings in some files, Unix in others

**Spacing:**
- Assignment operators aligned with tabs in declarations (see aligned `=` in `_admin_/acreedores_add.php`)
- Spaces around operators in conditionals
- No trailing semicolon enforcement

**File Structure:**
- All files start with `<?php` opening tag
- Include statements at top: `require_once()` for dependencies
- Class definition follows includes
- Methods ordered: constructor first, then getters/selectors, then setters, then delete

## Import Organization

**Order:**
1. Core framework includes (`class.dbaccess.php`, `class.db.php`)
2. Related entity classes (`class.acreedor.php`, `class.personatipos.php`)
3. Filter/pagination helpers (`class.filter.php`, `class.page.php`)
4. Interfaces at end if implemented

**Example from `library/class.acreedores.php`:**
```php
<?php
require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.acreedor.php');
require_once('class.personatipos.php');
require_once('class.tiposdocumento.php');
require_once('class.tiposiva.php');
require_once('class.profesiones.php');
require_once('class.estadosciviles.php');
require_once('class.usuarios.php');
require_once('class.filter.php');
require_once('class.page.php');

class Acreedores extends DBAccess implements IFilterable
```

**Autoloading:**
- Magic `__autoload()` function in `inc_library.php` automatically loads classes
- Converts class name to lowercase file path: `ClassName` → `../library/class.classname.php`

## Error Handling

**Patterns:**
- **Return-based errors**: Methods return `false` on failure, entity objects/arrays on success
- **Exceptions**: Limited use, only in critical paths (`class.db.php` throws exceptions on SQL errors)
- **Error flags**: Bitmask pattern in forms (see `_admin_/acreedores_add.php`):
  ```php
  $err = 0;
  if ($NumeroInscripcion == '')
      $err |= 1;  // Set bit 0
  if ($IdTipoPersona == '')
      $err |= 2;  // Set bit 1
  if ($RazonSocial == '')
      $err |= 4;  // Set bit 2
  ```

**Error Class:**
- Hardcoded error messages in `library/class.error.php` as constants
- HTML-formatted error strings with color styling

**Exception Usage:**
- Thrown only in critical database operations: `throw new Exception("Error SQL: " . message)`
- Try-catch blocks in integration modules (`class.padronesarba.php`, `class.clientessicop.php`)

## Logging

**Framework:** PHP built-in logging, no external framework

**Patterns:**
- Database logging via `LogsDB` class in `library/class.logsdb.php`
- Logs table: `TB_LogsDB` with fields: `Idlog`, `Fecha`, `IdUsuario`, `Accion`, `Tabla`, `Id`, `EstadoPrevio`, `EstadoActual`
- Log entries record: timestamp, user ID, action type, affected table, record ID, before/after state
- Activity tracking for all CRUD operations on critical entities

## Comments

**When to Comment:**
- Function comments: Only on complex utilities (date parsing, format conversion)
- Inline comments: Used to explain business logic and special cases
- Section comments: Headers like `/* declaracion de variables */` to mark logical blocks
- No JSDoc/PHPDoc standard enforced; comments are informal

**Comment Style:**
- Block comments: `/* text */` for section headers
- Inline comments: `// text` for single-line notes
- Spanish and English mixed throughout codebase

**Example from `_admin_/acreedores_add.php`:**
```php
/* obtiene datos del formulario */
$IdTipoPersona = intval($_REQUEST['IdTipoPersona']);

/* validaciones... */
if ($NumeroInscripcion == '')
    $err |= 1;

/* si el formulario fue enviado */
if ($Submit) { ... }
```

## Function Design

**Size:** No strict limit enforced, ranges from 50-1000+ lines in data access classes

**Parameters:**
- Entity methods accept arrays as parameters (filter arrays, parameter collections)
- Rarely use type hints (PHP 5.6 legacy limitation)
- Optional parameters with default values: `function GetAll(array $filter = NULL, Page $oPage = NULL)`

**Return Values:**
- Consistent return pattern: `false` on error, arrays/objects on success
- Query methods return result objects that support iteration: `while ($oRow = $oRes->GetRow())`
- Collection methods return PHP arrays of entity objects

**Example from `library/class.acreedores.php`:**
```php
public function GetAll(array $filter = NULL, Page $oPage = NULL)
{
    $sql = "SELECT *";
    $sql .= " FROM TB_Acreedores";
    $sql .= ($filter) ? $this->ParseFilter($filter) : "";
    $sql .= " ORDER BY RazonSocial";
    $sql .= ($oPage) ? Pageable::ParsePage($oPage) : "";

    if (!($oRes = $this->GetQuery($sql)))
        return false;

    $arr = array();
    while ($oRow = $oRes->GetRow()) {
        $oAcreedor = new Acreedor();
        $oAcreedor->ParseFromArray($oRow);
        array_push($arr, $oAcreedor);
        $oRes->MoveNext();
    }
    return $arr;
}
```

## Module Design

**Exports:**
- Module classes export public methods that become AJAX commands
- Methods follow naming pattern: `CommandName()` with PascalCase
- Each method receives a parameters array from the HTTP request
- Return values converted to XML by xmlhelper

**Barrel Files:**
- No barrel files (index files) used
- Direct class imports required

**Module Example from `modules/acreedores.php`:**
```php
<?php
require_once('../library/class.acreedores.php');

class ModuleAcreedores
{
    function GetAll(array $array)
    {
        $Acreedores = new Acreedores();
        $filter = array();
        $filter['RazonSocial'] = $array['FilterRazonSocial'];
        $filter['Email'] = $array['FilterEmail'];
        $filter['ClaveFiscalNumero'] = $array['FilterFiscalNumero'];
        $filter['IdTipoPersona'] = $array['FilterIdTipoPersona'];

        return $Acreedores->GetAll($filter, NULL);
    }

    function GetById(array $array)
    {
        $Acreedores = new Acreedores();
        return $Acreedores->GetById($array['IdAcreedor']);
    }
}
?>
```

## JavaScript Conventions

**Libraries:**
- jQuery (version 1.6.2, `js/jquery-1.6.2.js`)
- Prototype.js (older version, `js/prototype.js` or mootools)
- CRITICAL: jQuery uses `$j` alias to avoid conflicts with Prototype's `$`

**jQuery:**
- All jQuery selections prefixed with `$j`
- Use `$j('#elementId').val()`, `$j('#btn').click()`, `$j.ajax()`
- Never use `$()` for jQuery - reserved for Prototype

**Prototype/Mootools:**
- Uses native `$()` selector
- Element manipulation: `Element.show()`, `Element.hide()`, `Element.observe()`
- Event handling: `object.observe('event', handler)`

**Example from `_admin_/acreedores.php`:**
```javascript
function SetPage(Page)
{
    var frmData = Get('frmData');  // Get() is a helper function

    if (frmData == undefined)
        return false;

    frmData.Page.value = Page;     // Direct property access
    frmData.submit();               // Standard DOM method
}

function Filtrar()
{
    var frmData = Get('frmData');

    if (frmData == undefined)
        return false;

    frmData.Page.value = 0;
    frmData.submit();
}
```

## Request/Response Handling

**Form Submission:**
- Direct POST/GET without REST conventions
- Form data collected via `$_REQUEST['fieldname']`
- Type casting applied immediately: `intval()`, `strval()`, `isset()`
- Submitted flag: `$Submit = (isset($_REQUEST['Submitted']))`

**AJAX Requests:**
- Endpoint: `xml/xmlhelper.php`
- Request parameters: `?module=ModuleName&command=CommandName&param1=value1`
- Response format: XML by default, HTML if `?html=1` param passed
- Request function: `SendXMLRequest(module, command, callback, params)`

**Session/Security:**
- All pages check authentication: `Session::ForceLogin()`
- Permission checks: `if (!Session::CheckPerm(PERM_PERMISSION_CODE)) Session::NoPerm()`
- Permissions defined as constants in `inc_perms.php`

---

*Convention analysis: 2026-03-25*