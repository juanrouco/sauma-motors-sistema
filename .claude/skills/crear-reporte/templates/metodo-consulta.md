# Template: Metodo de consulta en clase de acceso a datos

**Destino:** Agregar metodos a `src/library/class.{entidades}.php`

Se necesitan tipicamente 2 metodos: uno para obtener totales/resumen y otro para parsear el filtro (si la clase no tiene `ParseFilter` propio).

## Placeholders a reemplazar

| Placeholder | Reemplazar por | Ejemplo |
|-------------|---------------|---------|
| `TB_MiTabla` | Tabla principal del reporte | `TB_StockMovimientos` |
| `TB_OtraTabla` | Tablas relacionadas via JOIN | `TB_Articulos` |
| `t`, `o` | Alias de tablas | `ms`, `a` |
| Campos del SELECT | Columnas reales a sumar/contar | `SUM(cd.ImporteCompraNeto)` |
| Campos del stdClass | Propiedades del objeto resultado | `$oTotal->CostoTotalFord` |
| Condiciones de ParseFilter | Filtros especificos de la entidad | `AND t.IdProveedor = ...` |

## Metodo GetTotalReporte

```php
public function GetTotalReporte(array $filter = NULL)
{
	$oTotal = new stdClass();
	$oTotal->CantidadTotal = 0;
	$oTotal->CostoTotal = 0;
	// ... inicializar TODOS los campos en 0

	$sql = " SELECT SUM(t.Cantidad) AS CantidadTotal,";
	$sql.= " SUM(t.Monto) AS CostoTotal";
	$sql.= " FROM TB_MiTabla t";
	$sql.= " INNER JOIN TB_OtraTabla o ON t.IdOtra = o.IdOtra";
	// Agregar mas JOINs segun necesidad

	// Para excluir registros referenciados por notas de credito,
	// usar LEFT JOIN (NO usar NOT IN que es ~20% mas lento):
	// $sql.= " LEFT JOIN TB_NotasCredito nc ON f.IdComprobante = nc.IdFactura";
	// Luego en el WHERE: " AND nc.IdFactura IS NULL"

	$sql.= " WHERE 1=1";

	if ($filter)
		$sql.= " " . $this->ParseFilter($filter);

	if (!($oRes = $this->GetQuery($sql)))
		return false;

	if (!$oRow = $oRes->GetRow())
		return false;

	$oTotal->CantidadTotal = $oRow['CantidadTotal'];
	$oTotal->CostoTotal = $oRow['CostoTotal'];
	// ... mapear todos los campos

	return $oTotal;
}
```

## Metodo ParseFilter

Solo crear si la clase no tiene un `ParseFilter` propio. Si ya existe, agregar los campos nuevos ahi.

```php
public function ParseFilter(array $filter)
{
	$sql = '';

	if ($filter['FechaDesde'] != null && $filter['FechaDesde'] != "")
		$sql.= " AND t.Fecha >= " . DB::Date($filter['FechaDesde']);

	if ($filter['FechaHasta'] != null && $filter['FechaHasta'] != "")
		$sql.= " AND t.Fecha <= " . DB::Date($filter['FechaHasta']);

	if ($filter['IdUbicacion'] != null && $filter['IdUbicacion'] != "")
		$sql.= " AND t.IdUbicacion = " . DB::Number($filter['IdUbicacion']);

	// Para campos de texto (busqueda parcial):
	// if ($filter['Modelo'] != null && $filter['Modelo'] != "")
	//     $sql.= " AND t.Modelo LIKE '%" . DB::StringUnquoted($filter['Modelo']) . "%'";

	return $sql;
}
```

## Notas

- Usar `DB::Number()` para IDs y valores numericos, `DB::Date()` para fechas, `DB::String()` para strings con comillas, `DB::StringUnquoted()` para strings dentro de LIKE.
- Inicializar el stdClass con TODOS los campos en 0 antes del query. Si el query falla o no devuelve filas, el objeto sigue siendo usable.
- El patron `WHERE 1=1` seguido de `AND ...` en ParseFilter simplifica la construccion dinamica del filtro (no hay que manejar si es el primer AND o no).
- Si el reporte necesita listar registros individuales (no solo totales), crear un metodo `GetAllReporte($filter, $oPage)` que devuelve un array de objetos en vez de un stdClass con sumas.
