<?php
require_once('../inc_library.php'); 
require_once('../library/mpdf/mpdf.php');
ob_clean();

Session::ForceLogin();

if (!Session::CheckPerm(PERM_TURNO_LIST))
	Session::NoPerm();

$Bahia			 				= $_REQUEST['Bahia'];
$FechaInicioDesde 				= $_REQUEST['FilterFechaDesde'];
$FechaInicioHasta 				= $_REQUEST['FilterFechaHasta'];

$oOrdenTrabajo					= new OrdenTrabajo();
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
$oTurnosComentarios				= new TurnosComentarios();

$strParams = '?' . $_SERVER['QUERY_STRING'];

$filter['FechaInicioDesde'] = $FechaInicioDesde;
if ($FechaInicioHasta)
	$filter['FechaInicioHasta'] = $FechaInicioHasta . ' 23:59';
$filter['Bahia'] 			= $Bahia;
$filter['IdEstadoOrden']	= EstadoOrden::Presupuesto;

$arrOrdenesTrabajo = $oTurnos->GetAll($filter);

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
													<div align="center"><span style="text-align:center" class="texto20">Turnos de Taller de la Fecha <?= $FechaInicioDesde ? $FechaInicioDesde : date('d/m/Y') ?></span></div>
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
                    	<td align="left" width="15%" style="border-top: 1px solid #000; border-bottom: 1px solid #000"><div align="left"><strong>Fecha</strong></div></td>
                    	<td align="left" width="10%" style="border-top: 1px solid #000; border-bottom: 1px solid #000"><div align="left"><strong>Hora</strong></div></td>
                    	<td align="left" width="50%" style="border-top: 1px solid #000; border-bottom: 1px solid #000"><div align="left"><strong>Cliente / Operaciones</strong></div></td>
                    	<td align="left" width="25%" style="border-top: 1px solid #000; border-bottom: 1px solid #000"><div align="left"><strong>Telefonos</strong></div></td>
                    </tr>
					<?php
					if ($arrOrdenesTrabajo)
					{
					
						$count = 0;
						foreach ($arrOrdenesTrabajo as $oOrdenTrabajo)
						{
							$HoraInicio		= date_format(new DateTime($oOrdenTrabajo->FechaInicio), 'H');
							$MinutoInicio	= date_format(new DateTime($oOrdenTrabajo->FechaInicio), 'i');
							$oTallerUnidad 	= $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
							$oUsuario		= $oUsuarios->GetById($oOrdenTrabajo->IdUsuarioAsignado);
							$oCliente		= $oClientes->GetById($oTallerUnidad->IdCliente);
							$arrOrdenesTrabajoTareas = $oTurnosTareas->GetAllByTurno($oOrdenTrabajo);
							$arrTurnosComentarios = $oTurnosComentarios->GetByIdTurno($oOrdenTrabajo->IdTurno);
					?>
                    <tr>
                    	<td align="left" valign="top" width="15%"><div align="left"><?=str_replace('-', '/', CambiarFecha($oOrdenTrabajo->FechaInicio)) ?></div></td>
                    	<td align="left" valign="top" width="10%"><div align="left"><strong><?= $HoraInicio ?>:<?= $MinutoInicio ?></strong></div></td>
                    	<td align="left" valign="top" width="50%">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td colspan="2"><div align="left"><strong><?= utf8_encode($oCliente->RazonSocial) ?></strong></td>
								</tr>
								<tr>
									<td colspan="2"><div align="left">Modelo: <?= $oTallerUnidad->Modelo ?></div></td>
								</tr>
								<tr>
									<td colspan="2"><div align="left">Chasis: <?= $oTallerUnidad->NumeroVin ?></div></td>
								</tr>
								<tr>
									<td colspan="2"><div align="left">Patente: <?= utf8_encode($oTallerUnidad->Dominio) ?></div></td>
								</tr>
								<tr>
									<td colspan="2"><div align="left">Mecanico:</div></td>
								</tr>
								<?php
								if ($arrOrdenesTrabajoTareas)
								{
									foreach ($arrOrdenesTrabajoTareas as $oOrdenTrabajoTarea)
									{
								?>
								<tr>
									<td colspan="2"><div align="left"><?= utf8_encode($oOrdenTrabajoTarea->Titulo) ?></div></td>
								</tr>
								<?php
									}
								}
								?>
								
							</table>
						</td>
                    	<td  valign="top" align="left" width="25%">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td>
										<div align="left"><?=($oCliente->TelefonoCodigoArea ? $oCliente->TelefonoCodigoArea . ' - ' : '') . $oCliente->Telefono?></div>
									</td>
								</tr>
								<?php
								if ($oCliente->Fax)
								{
								?>
								<tr>
									<td>
										<div align="left"><?=($oCliente->FaxCodigoArea ? $oCliente->FaxCodigoArea . ' - ' : '') . $oCliente->Fax ?></div>
									</td>
								</tr>
								<?php
								}
								?>
								<tr>
									<td>
										<div align="left"><?= utf8_encode($oCliente->Email) ?></div>
									</td>
								</tr>
							</table>
						</td>
                    </tr>
					<tr>
						<td colspan="4"><div align="left">Comentarios:</div></td>
					</tr>
					<?php
					$Count = 1;
					if ($oTurno->Comentarios && trim($oTurno->Comentarios) != '')
					{
					?>
					<tr>
						<td colspan="4"><div align="left"><?= $Count ?>- <?= utf8_encode($oTurno->Comentarios) ?></div></td>
					</tr>
					<?php
						$Count++;
					}
					if ($arrTurnosComentarios)
					{
						foreach ($arrTurnosComentarios as $oTurnoComentario) 
						{
							if ($oTurnoComentario->Comentarios != '')
							{
					?>
					<tr>
						<td colspan="4"><div align="left"><?= $Count ?>- <?= utf8_encode($oTurnoComentario->Comentarios) ?></div></td>
					</tr>
					<?php
								$Count++;
							}
						}
					}
					?>
					<tr  style="border-bottom: 1px solid #000">
						<td style="border-bottom: 1px solid #000">&nbsp;</td>
						<td style="border-bottom: 1px solid #000">&nbsp;</td>
						<td style="border-bottom: 1px solid #000">&nbsp;</td>
						<td style="border-bottom: 1px solid #000">&nbsp;</td>
					</tr>
					<?php
							$count++;
							if ($count != 0 && $count % 5 == 0)
							{
								$count = 0;
					?>
					
					
				</table>
			</div>
		</td>
  	</tr>
  	<tr>
    	<td>&nbsp;</td>
  	</tr>
</table>
<table width="794" border="0" cellspacing="0" cellpadding="0" align="center">	
  	<tr>
    	<td>
			<div align="center">		
				<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">	
					<?php
							}
						}
					}
					?>
					
				</table>
    		</div>
		</td>
  	</tr>
  	<tr>
    	<td>&nbsp;</td>
  	</tr>
</table>

</body>
</html>
<?php

$Contenido = ob_get_contents();
ob_end_clean();

$oMpdf->WriteHTML($Contenido);
$oMpdf->Output('Turnos ' . date('d-m-Y') . '.pdf', 'D'); 

?>