# Template: Archivo de exportacion CSV/XLS

**Destino:** `_admin_/{entidad}_reporte_exportar.php`

## Placeholders a reemplazar

| Placeholder | Reemplazar por | Ejemplo |
|-------------|---------------|---------|
| `MiEntidades` | Clase de acceso a datos (plural) | `Articulos` |
| `ExportReporteCsv` | Metodo de exportacion de la clase | `ExportReporteCsv` |
| `FilterXxx` | Campos de filtro (mismos que el reporte) | `FilterIdProveedor` |

## Codigo

```php
<?php
// _admin_/{entidad}_reporte_exportar.php
chdir(dirname(__FILE__));
require_once('../inc_library.php');

/* armamos el filtro con validacion (mismos campos que el reporte) */
$filter = array();
$filter['FechaDesde']  = isset($_REQUEST['FilterFechaDesde']) ? $_REQUEST['FilterFechaDesde'] : '';
$filter['FechaHasta']  = isset($_REQUEST['FilterFechaHasta']) ? $_REQUEST['FilterFechaHasta'] : '';
$filter['IdUbicacion'] = isset($_REQUEST['FilterIdUbicacion']) ? intval($_REQUEST['FilterIdUbicacion']) : 0;
// ... mismos filtros que el reporte principal

$oEntidad = new MiEntidades();
$oEntidad->ExportReporteCsv($filter);
exit();
?>
```

## Notas

- Los filtros deben ser exactamente los mismos que recibe `$strParams` en el reporte principal.
- El metodo `ExportReporteCsv` esta en la clase de acceso a datos (`library/class.{entidades}.php`). Si no existe, hay que crearlo.
- El metodo tipicamente envia headers HTTP para forzar descarga y escribe CSV/XLS al output.
- Usar `chdir(dirname(__FILE__))` al inicio para que los `require_once` relativos funcionen cuando se ejecuta por CLI.
