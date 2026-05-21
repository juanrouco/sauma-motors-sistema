# Template: Pagina de reporte

**Destino:** `_admin_/{entidad}_reporte.php`

## Placeholders a reemplazar

| Placeholder | Reemplazar por | Ejemplo |
|-------------|---------------|---------|
| `PERM_XXXX_LIST` | Permiso real de `inc_perms.php` | `PERM_ARTI_LIST` |
| `MiEntidades` | Clase de acceso a datos (plural) | `StockMovimientos` |
| `MiReporte` | Titulo visible del reporte | `Reporte de Movimientos de Stock` |
| `mireporte_exportar.php` | Nombre del archivo de exportacion | `stockmovimientos_exportar.php` |
| `FilterXxx` / `$filter['Xxx']` | Campos de filtro reales del reporte | `FilterModelo`, `$filter['Modelo']` |
| `GetReporte($filter)` | Metodo real de consulta | `GetAllReporte($filter)` |
| Columnas del header y filas | Campos reales de la entidad | `$row->CostoTotal`, `$row->Codigo` |

## Cabecera PHP

```php
<?php
require_once('../inc_library.php');

/* autenticacion */
Session::ForceLogin();

/* permisos */
if (!Session::CheckPerm(PERM_XXXX_LIST))
	Session::NoPerm();

/* paginacion (si aplica) */
$Page = (isset($_REQUEST['Page'])) ? intval($_REQUEST['Page']) : 0;

/* armamos el filtro con validacion */
$filter = array();
$filter['FechaDesde']  = isset($_REQUEST['FilterFechaDesde']) ? $_REQUEST['FilterFechaDesde'] : '';
$filter['FechaHasta']  = isset($_REQUEST['FilterFechaHasta']) ? $_REQUEST['FilterFechaHasta'] : '';
$filter['IdUbicacion'] = isset($_REQUEST['FilterIdUbicacion']) ? intval($_REQUEST['FilterIdUbicacion']) : 0;
// ... mas filtros segun necesidad. Usar intval() para IDs numericos.

/* valores por defecto para fechas */
if ($filter['FechaHasta'] == '')
	$filter['FechaHasta'] = date('d-m-Y');

if ($filter['FechaDesde'] == '')
	$filter['FechaDesde'] = date('d-m-Y', strtotime("-7 days"));

/* instanciar SOLO las clases que se usan */
$MiEntidad   = new MiEntidades();
$Ubicaciones = new Ubicaciones();

/* obtener datos para combos del filtro */
$arrUbicaciones = $Ubicaciones->GetAll(array('Postventa' => 1));

/* ejecutar consulta del reporte */
$arrResultados = $MiEntidad->GetReporte($filter);
// O para totales: $oTotales = $MiEntidad->GetTotalReporte($filter);

/* armar parametros para link de exportacion */
$strParams = '';
$strParams.= '?FilterFechaDesde='  . $filter['FechaDesde'];
$strParams.= '&FilterFechaHasta='  . $filter['FechaHasta'];
$strParams.= '&FilterIdUbicacion=' . $filter['IdUbicacion'];

/* calculos derivados — SIEMPRE proteger division por cero */
// $Rentabilidad = ($Compra != 0) ? ($Total / $Compra * 100 - 100) : 0;
?>
```

## HTML completo

```html
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php include('include/head.inc.php'); ?>
<script language="javascript">
$j(document).ready(function() {
	// JavaScript del reporte (modales, etc.)
});
</script>
</head>

<body>
<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
	<!-- Titulo -->
	<tr>
		<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
				<tr>
					<td width="20" height="40" class="TituloRubro">&nbsp;</td>
					<td height="40"><span class="tituloPagina">MiReporte</span></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>

	<!-- Link exportar (si aplica, eliminar si no hay exportacion) -->
	<tr>
		<td height="30" valign="top">
			<table border="0" align="right" cellpadding="0" cellspacing="0">
				<tr>
					<td width="30"><div align="center"><img src="images/iconos/icono_csv.gif" alt="Exportar XLS" border="0"></div></td>
					<td><a href="mireporte_exportar.php<?= htmlspecialchars($strParams) ?>">Exportar XLS</a></td>
				</tr>
			</table>
		</td>
	</tr>

	<!-- Formulario de filtro -->
	<tr>
		<td height="30" valign="top">
			<form name="frmData" id="frmData" method="post">
				<input type="hidden" name="Page" id="Page" value="<?= htmlspecialchars($Page) ?>" />
				<div id="FilterMain">
				<div id="Filter">
					<table border="0" class="bordeGrisFondo" align="left" cellpadding="2" cellspacing="2" width="100%">
						<tr>
							<td width="20" height="40" class="TituloRubro">&nbsp;</td>
							<td><span class="tituloPagina">Filtro</span></td>
						</tr>
						<tr>
							<td width="20" height="40" class="TituloRubro">&nbsp;</td>
							<td class="tituloMenu">
								<table border="0" align="left" cellpadding="0" cellspacing="0">
									<tr>
										<td class="tituloMenu">Fecha Desde:</td>
										<td width="270">
											<input type="text" name="FilterFechaDesde" id="FilterFechaDesde"
												class="camporFormularioSuggest"
												value="<?= htmlspecialchars($filter['FechaDesde']) ?>" />
											<script type="text/javascript">
												new tcal({'formname': 'frmData', 'controlname': 'FilterFechaDesde'});
											</script>
										</td>
										<td class="tituloMenu">Fecha Hasta:</td>
										<td width="270">
											<input type="text" name="FilterFechaHasta" id="FilterFechaHasta"
												class="camporFormularioSuggest"
												value="<?= htmlspecialchars($filter['FechaHasta']) ?>" />
											<script type="text/javascript">
												new tcal({'formname': 'frmData', 'controlname': 'FilterFechaHasta'});
											</script>
										</td>
									</tr>
									<tr>
										<td class="tituloMenu">Ubicaci&oacute;n:</td>
										<td width="270">
											<select name="FilterIdUbicacion" id="FilterIdUbicacion" class="camporFormularioSuggest">
												<option value="">Indistinto</option>
												<?php if ($arrUbicaciones): ?>
													<?php foreach ($arrUbicaciones as $oUbicacion): ?>
														<option value="<?= $oUbicacion->IdUbicacion ?>"
															<?= ($oUbicacion->IdUbicacion == $filter['IdUbicacion']) ? "selected='selected'" : "" ?>>
															<?= $oUbicacion->Nombre ?>
														</option>
													<?php endforeach; ?>
												<?php endif; ?>
											</select>
										</td>
										<!-- Agregar mas campos de filtro aqui -->
										<td valign="middle">
											<input type="submit" name="button" class="botonBasico" value="Buscar" />
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div>
				</div>
			</form>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>

	<!-- Tabla de resultados -->
	<tr>
		<td>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
				<!-- Header -->
				<tr class="bordeGrisFondo">
					<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Columna 1</strong></div></td>
					<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Columna 2</strong></div></td>
					<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>$ Monto</strong></div></td>
				</tr>

				<!-- Filas de datos -->
				<?php foreach ($arrResultados as $row): ?>
				<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
					<td height="25"><div id="margen"><?= htmlspecialchars($row->Campo1) ?></div></td>
					<td height="25"><div id="margen"><?= htmlspecialchars($row->Campo2) ?></div></td>
					<td height="25"><div id="margen">$<?= number_format($row->Monto, 2, ',', '.') ?></div></td>
				</tr>
				<tr>
					<td colspan="3"><div align="center">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr><td height="1" background="images/linea_punteada.gif"></td></tr>
						</table>
					</div></td>
				</tr>
				<?php endforeach; ?>

				<!-- Fila de totales -->
				<tr bgColor='#f3f3f3'>
					<td colspan="2" height="25"><div id="margen"><strong>TOTAL</strong></div></td>
					<td height="25"><div id="margen">$<?= number_format($Total, 2, ',', '.') ?></div></td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<!-- Modal popup (si el reporte tiene detalles expandibles) -->
<div id="modal-popup" style="display:none"></div>
<div class="modal"><!-- Place at bottom of page --></div>

</body>
</html>
```

## Notas

- El `colspan` del separador y del TOTAL debe coincidir con la cantidad de columnas del header.
- Si el reporte no tiene exportacion, eliminar el bloque "Link exportar" y la variable `$strParams`.
- Si el reporte no tiene paginacion, eliminar `$Page` y el hidden `Page`.
- Para reportes con multiples secciones de totales (como `stockmovimientos_totales_reporte.php`), usar un array de datos e iterar con `foreach` en vez de repetir bloques HTML.
