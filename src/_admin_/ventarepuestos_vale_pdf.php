<?php 
require_once('../inc_library.php');
require_once('../library/mpdf/mpdf.php');

ob_clean();

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdCompra = intval($_REQUEST['IdCompra']);

$oCompras				= new Compras();
$oComprobantes 			= new Comprobantes();
$oClientes 				= new Clientes();
$oTiposIva 				= new TiposIva();
$oLocalidades 			= new Localidades();
$oPartidos 				= new Partidos();
$oProvincias 			= new Provincias();
$oPaises 				= new Paises();
$oArticulos				= new Articulos();
$oTallerUnidades		= new TallerUnidades();
$oOrdenesTrabajo		= new OrdenesTrabajo();
$oColores				= new Colores();
$oOrdenesTrabajoTareas	= new OrdenesTrabajoTareas();

if (!$oCompra = $oCompras->GetById($IdCompra))
	exit();
$oCompra->LoadAllDetalles();

$oOrdenTrabajo = $oOrdenesTrabajo->GetById($oCompra->IdOrdenTrabajo);

if ($oCompra->IdCliente)
{
	if (!$oCliente = $oClientes->GetById($oCompra->IdCliente))
	{	
		header("Location: ventarepuestos.php" . $strParams);
		exit();
	}
}
else
{
	$oTallerUnidad = $oTallerUnidades->GetById($oCompra->IdTallerUnidad);
	if (!$oCliente = $oClientes->GetById($oTallerUnidad->IdCliente))
	{	
		header("Location: ventarepuestos.php" . $strParams);
		exit();
	}
}

if (!$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva))
	exit();

$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);

$oPartido = $oPartidos->GetById($oCliente->DomicilioIdPartido);

$oProvincia = $oProvincias->GetById($oCliente->DomicilioIdProvincia);

$oColor = $oColores->GetById($oTallerUnidad->IdColor);

/* creamos el objeto para manipular el .pdf */
$oMpdf = new mPDF();
if ($oCompra->IdTipoMovimiento == TipoMovimiento::Devolucion)
	$oMpdf->SetWatermarkText('DEVOLUCION');
else
	$oMpdf->watermarkText = '';
$oMpdf->showWatermarkText = true;

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
	font-size: 20px; 
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
<?php
for ($i = 0; $i < 2; $i++)
{
?>
<table width="800" border="0" cellspacing="0" cellpadding="0" align="center">	
  	<tr>
    	<td width="800">
			<div style="width:100%" align="center">				
				<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
				<?php
				if ($i == 1)
				{
				?>
				<tr>
					<td align="center">&nbsp;</td>
				</tr> 
				<tr>
					<td align="center">&nbsp;</td>
				</tr> 
				<?php
				}
				?>
					<tr>
						<td width="800">
							<table width="800" border="0" cellpadding="0" cellspacing="0">
								<tr>
	                                <td width="800">
										<table width="800" border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td width="200">
													<img src="images/logo_tolosa.jpg" width="200" />
												</td>
												<td width="400" align="center">
													<div style="width:400px" align="center"><span style="width: 100%; align:center" class="texto20">VALE DE MATERIALES</span></div>
												</td>
												<td width="200">
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
                    	<td align="right"><strong>N&deg; VALE:</strong> <?= $oCompra->NumeroVale ?></td>
                    </tr>					
					<tr>
                    	<td align="right"><?= $i == 0 ? 'ORIGINAL' : 'DUPLICADO' ?></td>
                    </tr>
					<tr>
						<td>
							<table width="800" border="0" cellpadding="0" cellspacing="0" align="center">
								<tr>
									<td width="400"><div align="center"><strong>FECHA: </strong><em><?=CambiarFecha($oCompra->FechaCarga)?></em></div></td>
									<td width="400"><div align="center"><strong>ORDEN DE TRABAJO N&deg;: </strong><em><?=$oOrdenTrabajo->IdOrdenTrabajo?></em></div></td>
								</tr>
								<tr>
									<td width="400"><div align="center"><strong>MODELO: </strong><em><?= $oTallerUnidad->Modelo?></em></div></td>
									<td width="400"><div align="center"><strong>CHASIS N&deg;: </strong><em><?=$oTallerUnidad->NumeroVin?></em></div></td>
								</tr>
								<tr>
									<td width="400"><div align="center"><strong>PATENTE: </strong><em><?= $oTallerUnidad->Dominio?></em></div></td>
									<td width="400"><div align="center"><strong>COLOR: </strong><em><?=$oColor->Nombre?></em></div></td>
								</tr>
							</table>
						</td>
					</tr>
                    <tr>
                    	<td align="center" style="border-bottom: 1px solid #000"><div align="center"><strong>REPUESTOS</strong></div></td>
                    </tr>
                    <tr height="10">
                    	<td height="10"></td>
                    </tr>					
					<tr>
						<td width="800" align="center">
								<table width="800" align="center" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td width="200" class="bordeNegro bordeNegroTop" align="center"><strong>CODIGO</strong></td>
										<td width="400" class="bordeNegro bordeNegroTop" align="center"><strong>DESCRIPCION</strong></td>
										<td width="200" class="bordeNegro bordeNegroTop bordeNegroRight" align="center"><strong>CANTIDAD</strong></td>									
										<td width="200" class="bordeNegro bordeNegroTop bordeNegroRight" align="center"><strong>CARGO</strong></td>									
									</tr>
									<?php 
									$count = 0;
									if ($oCompra->CompraDetalles != NULL)
									{
										foreach ($oCompra->CompraDetalles as $oCompraDetalle) 
										{
											$oArticulo = $oArticulos->GetById($oCompraDetalle->IdArticulo);
											$oOrdenTrabajoTarea = $oOrdenesTrabajoTareas->GetByIdIncrement($oCompra->IdOrdenTrabajoTarea);
											
											$oTipoVenta = TipoVenta::GetByIdOrdenTrabajo($oOrdenTrabajoTarea->IdTipoVenta);
									?>
									<tr>
										<td width="200" class="bordeNegro Item" align="center"><?=$oArticulo->Codigo?></td>
										<td width="400" class="bordeNegro Item" align="center"><?=$oArticulo->Descripcion?></td>
										<td width="200" class="bordeNegro Item" align="center"><?= $oCompra->IdTipoMovimiento == TipoMovimiento::Devolucion ? '-' : '' ?><?= $oCompraDetalle->Cantidad ?></td>
										<td width="200" class="bordeNegro Item bordeNegroRight" align="center"><?= $oTipoVenta['Nombre'] ?></td>
									</tr>
									<?php
											$count++;
										}
										for ($j = $count; $j < 13; $j++)
										{
									?>
									<tr>
										<td width="200" class="bordeNegro Item" align="center">&nbsp;</td>
										<td width="400" class="bordeNegro Item" align="center">&nbsp;</td>
										<td width="200" class="bordeNegro Item" align="center">&nbsp;</td>
										<td width="200" class="bordeNegro Item bordeNegroRight" align="center">&nbsp;</td>
									</tr>
									<?php
										}
									}
									?>
								</table>
						</td>
					</tr>					
					
					<tr>
						<td height="70"></td>
					</tr>
					<tr>
						<td>
							<table width="800" border="0" cellpadding="0" cellspacing="0" align="center">
								<tr>
									<td width="400" align="center"><strong>FIRMA MECANICO</strong></td>
									<td width="400" align="center"><strong>FIRMA MECANICO</strong></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
    		</div>
		</td>
  	</tr>
	<?php
	if ($i == 0 && $oCompra->IdTipoMovimiento != TipoMovimiento::Devolucion)
	{
	?>
  	<tr>
		<td align="center" style="border-bottom: 1px solid #000">&nbsp;</td>
	</tr> 
	<?php
	}
	?>	
</table>
<?php
	if ($oCompra->IdTipoMovimiento == TipoMovimiento::Devolucion && $i == 0)
	{
?>
	<pagebreak />
<?php
	}
}
?>
</body>
</html>
<?php

$Contenido = ob_get_contents();
ob_end_clean();

$oMpdf->SetJS('this.print();');
$oMpdf->WriteHTML($Contenido);
$oMpdf->Output('vale.pdf', 'I'); 

?>