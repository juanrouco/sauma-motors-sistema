<?php
require_once('../inc_library.php'); 
require_once('../library/mpdf/mpdf.php');
ob_clean();

Session::ForceLogin();

$Garantia			 			= '1';
$FechaCargaDesde 				= $_REQUEST['FilterFechaCargaDesde'];
$FechaCargaHasta 				= $_REQUEST['FilterFechaCargaHasta'];

$oCompras						= new Compras();
$oTurnos						= new Turnos();
$oEstadosOrden					= new EstadosOrden();
$oTallerUnidades				= new TallerUnidades();
$oUsuarios						= new Usuarios();
$oClientes						= new Clientes();
$oMarcas						= new Marcas();
$oTurnosTareas					= new TurnosTareas();
$oTurnosTareasArticulos			= new TurnosTareasArticulos();
$oTareasTrabajoArticulos 		= new TareasTrabajoArticulos();
$oArticulos						= new Articulos();
$oTiposDocumento 				= new TiposDocumento();
$oLocalidades					= new Localidades();
$oOrdenTrabajoComentarios		= new OrdenTrabajoComentarios();

$strParams = '?' . $_SERVER['QUERY_STRING'];

$filter['FechaCargaDesde'] = $FechaCargaDesde;
if ($FechaCargaHasta)
	$filter['FechaCargaHasta'] = $FechaCargaHasta . ' 23:59';
$filter['IdTipoVenta'] 	= TipoVenta::Garantia;

$arrCompras = $oCompras->GetAllReporte($filter);


/* creamos el objeto para manipular el .pdf */
$oMpdf = new mPDF();
$oMpdf->watermarkText = '';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style>
body {
	background-color: #FFFFFF;
}
table {
	border-collapse: collapse;
	width: 100%;
}
td {
	font-size: 14px; 
	color: #000000; 
	font-family: Arial, Helvetica, sans-serif;
}
td.bordeNegro {
	border-bottom: 1px solid #000000;
	border-left: 1px solid #000000;
}
td.bordeNegroBottom {
	border-bottom: 1px solid #000000;
}
td.bordeNegroTop {
	border-top: 1px solid #000000;
}
td.bordeNegroLeft {
	border-left: 1px solid #000000;
}
td.bordeNegroRight {
	border-right: 1px solid #000000;
}
td.Item {	
	font-size: 12px; 
}
.texto20 {
	font-size: 15; 
	color: #000000; 
	font-family: Arial, Helvetica, sans-serif;
	font-weight:bold;
}
.bordeBottom {
	border-bottom: 2px solid #000000;
}
.textoPie {
	font-size: 11px; 
	color: #000000; 
	font-family: Arial, Helvetica, sans-serif;
}
</style>
</head>
<body>

<table width="794" border="0" cellspacing="0" cellpadding="0" align="center">	
  	<tr>
    	<td>
			<div align="center">				
				<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
					<tr>
						<td colspan="4">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
	                                <td width="50%" align="left">
										<table width="100%" border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td><div align="left"><img src="images/logo_tolosa.jpg" width="250" height="50" /></div></td>
											</tr>
										</table>
									</td>
	                                <td width="50%" align="left">
										<table width="100%" border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td><div align="right">&nbsp;</div></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
	                                <td align="center" colspan="2" width="100%">
										<table width="100%" border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td align="center">
													<div align="center"><span style="text-align:center" class="texto20">LISTADO DE VALES DE TALLER CON CARGO G <?= $FechaCargaDesde ? $FechaCargaDesde : date('d/m/Y') ?> - <?= $FechaCargaHasta ? $FechaCargaHasta : date('d/m/Y') ?></span></div>
												</td>
											</tr>
										</table>
									</td>
                                </tr>
							</table>
						</td>
					</tr>					
					 <tr>
                    	<td colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                    	<td align="left" width="15%" style="border-top: 1px solid #000; border-bottom: 1px solid #000"><div align="left"><strong>Orden Taller</strong></div></td>
                    	<td align="left" width="15%" style="border-top: 1px solid #000; border-bottom: 1px solid #000"><div align="left"><strong>Fecha</strong></div></td>
                    	<td align="left" width="15%" style="border-top: 1px solid #000; border-bottom: 1px solid #000"><div align="left"><strong>Nro Pieza</strong></div></td>
                    	<td align="left" width="40%" style="border-top: 1px solid #000; border-bottom: 1px solid #000"><div align="left"><strong>Descripci&oacute;n</strong></div></td>
                    	<td align="left" width="15%" style="border-top: 1px solid #000; border-bottom: 1px solid #000"><div align="left"><strong>Cantidad</strong></div></td>
                    </tr>
					<?php
					if ($arrCompras)
					{
					
						$count = 0;
						foreach ($arrCompras as $oCompra)
						{
							$oCompra->LoadAllDetalles();
							foreach ($oCompra->CompraDetalles as $oCompraDetalle)
							{
								$oArticulo = $oArticulos->GetById($oCompraDetalle->IdArticulo);
					?>
                    <tr>
                    	<td align="left" valign="top" width="15%"><div align="left"><?= $oCompra->IdOrdenTrabajo ?></div></td>
                    	<td align="left" valign="top" width="15%"><div align="left"><?=str_replace('-', '/', CambiarFecha($oCompra->FechaCarga)) ?></div></td>
                    	<td align="left" valign="top" width="15%"><div align="left"><?= $oArticulo->Codigo ?></div></td>
                    	<td align="left" valign="top" width="40%"><div align="left"><?= $oArticulo->Descripcion ?></div></td>
                    	<td align="left" valign="top" width="15%"><div align="left"><?= $oCompraDetalle->Cantidad ?></div></td>
                    	
                    </tr>
					<?php
								$count++;
								if ($count % 30 == 0)
								{
					?>
					</table>
    		</div>
		</td>
  	</tr>
</table>
<pagebreak />
<table width="794" border="0" cellspacing="0" cellpadding="0" align="center">	
  	<tr>
    	<td>
			<div align="center">				
				<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
					
                    <tr>
                    	<td align="left" width="15%" style="border-top: 1px solid #000; border-bottom: 1px solid #000"><div align="left"><strong>Orden Taller</strong></div></td>
                    	<td align="left" width="15%" style="border-top: 1px solid #000; border-bottom: 1px solid #000"><div align="left"><strong>Fecha</strong></div></td>
                    	<td align="left" width="15%" style="border-top: 1px solid #000; border-bottom: 1px solid #000"><div align="left"><strong>Nro Pieza</strong></div></td>
                    	<td align="left" width="40%" style="border-top: 1px solid #000; border-bottom: 1px solid #000"><div align="left"><strong>Descripci&oacute;n</strong></div></td>
                    	<td align="left" width="15%" style="border-top: 1px solid #000; border-bottom: 1px solid #000"><div align="left"><strong>Cantidad</strong></div></td>
                    </tr>
					<?php
								}
							}
						}
					}
					?>
					
				</table>
    		</div>
		</td>
  	</tr>
</table>

</body>
</html>
<?php

$Contenido = ob_get_contents();
ob_end_clean();

$oMpdf->WriteHTML($Contenido);
$oMpdf->Output('Vales Garantia.pdf', 'D'); 

?>