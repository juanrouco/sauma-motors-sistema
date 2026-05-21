<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_TARE_LIST))
	Session::NoPerm();

/* obtenemos datos enviados */
$IdOrdenTrabajo			= intval($_REQUEST['IdOrdenTrabajo']);
$Action 				= strval($_REQUEST['MainAction']);
$tareasSeleccionados	= strval($_REQUEST['tareasSeleccionados']);
$Id						= intval($_REQUEST['Id']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaramos e instanciamos variables necesarias */
$err 							= 0;
$oTareasTrabajo					= new TareasTrabajo();
$oOrdenesTrabajo		 		= new OrdenesTrabajo();
$oOrdenesTrabajoTareas			= new OrdenesTrabajoTareas();
$oOrdenesTrabajoTareasArticulos	= new OrdenesTrabajoTareasArticulos();
$oTareasTrabajoArticulos 		= new TareasTrabajoArticulos();
$oOrdenTrabajoHitos				= new OrdenTrabajoHitos();
$oArticulos 					= new Articulos();
$oIvas 							= new Ivas();
$oTiposCosto					= new TiposCosto();
$oModelos						= new Modelos();
$oUsuarios						= new Usuarios();

/* definimos cadena a mandar por get */
$strParams = (strlen($_SERVER['QUERY_STRING']) > 0) ? '?' . $_SERVER['QUERY_STRING'] : '';

/* obtiene los datos del curso */
if (!$oOrdenTrabajo = $oOrdenesTrabajo->GetById($IdOrdenTrabajo))
{
	header('Location: ordenestrabajo.php' . $strParams);
	exit;
}

function sum_the_time($time1, $time2) {
  $times = array($time1, $time2);
  $seconds = 0;
  foreach ($times as $time)
  {
    list($hour,$minute,$second) = explode(':', $time);
    $seconds += $hour*3600;
    $seconds += $minute*60;
    $seconds += $second;
  }
  $hours = floor($seconds/3600);
  $seconds -= $hours*3600;
  $minutes  = floor($seconds/60);
  $seconds -= $minutes*60;
  $hours = str_pad($hours, 2, "0", STR_PAD_LEFT);
  $minutes = str_pad($minutes, 2, "0", STR_PAD_LEFT);
  $seconds = str_pad($seconds, 2, "0", STR_PAD_LEFT);
  return "{$hours}:{$minutes}:{$seconds}";
}

$arrOrdenTrabajoHitos = $oOrdenTrabajoHitos->GetByIdOrdenTrabajo($oOrdenTrabajo->IdOrdenTrabajo);
$hitoIniciado = null;
IncludeSUGGEST();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

</script>
</head>
<body>

	<form name="frmData" id="frmData" method="post">
		<input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
		<input type="hidden" name="MainAction" id="MainAction" />
		<input type="hidden" name="IdOrdenTrabajo" id="IdOrdenTrabajo" value="<?=$IdOrdenTrabajo?>" />
		<input type="hidden" name="Id" id="Id" />
		<input type="hidden" name="Submitted" id="Submitted" value="1" />
		<input type="hidden" name="tareasSeleccionados" id="tareasSeleccionados" value="" />
		
		<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td width="20" height="40" class="TituloRubro">&nbsp;</td>
							<td height="40"><span class="tituloPagina">Orden de Trabajo N&deg; <?= $IdOrdenTrabajo ?> - Resumen Horas de Trabajo</span></td>
						</tr>
					</table>			
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>			
			<?php 
			if ($arrOrdenTrabajoHitos != NULL)
			{ 
			?>			
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
						<tr class="bordeGrisFondo">							
							<td width="250" height="25"><div id="margen"><strong>Mec&aacute;nico</strong></div></td>
							<td width="200" height="25"><div id="margen"><strong>Tarea</strong></div></td>
							<td width="125" height="25"><div id="margen"><strong>Inicio</strong></div></td>
							<td width="125" height="25"><div id="margen"><strong>Fin</strong></div></td>
							<td width="75" height="25"><div id="margen"><strong>Estado</strong></div></td>
							<td width="75"><div id="margen"><strong>Parcial</strong></div></td>
						</tr>
						<?php
						$tiempoAcumulado = '00:00:00';
						foreach ($arrOrdenTrabajoHitos as $oOrdenTrabajoHito) 
						{ 
							$oUsuario = $oUsuarios->GetById($oOrdenTrabajoHito->IdUsuario);
							$oOrdenTrabajoTarea = $oOrdenesTrabajoTareas->GetByIdIncrement($oOrdenTrabajoHito->IdOrdenTrabajoTarea);
							
							$tiempo = 0;
							
							$FechaFin = CambiarFechaHora($oOrdenTrabajoHito->FechaHoraFin);
							if ($oOrdenTrabajoHito->TipoHito == OrdenTrabajoHito::Iniciar)
								$FechaFin = '';
							else
							{
								list($hours,$mins,$secs) = explode(':', $oOrdenTrabajoHito->Tiempo);
								$tiempo = mktime($hours,$mins,$secs);
							}
						?>
						<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
							<td width="250"><div id="margen"><?=$oUsuario->Apellido?>, <?= $oUsuario->Nombre ?></div></td>
							<td width="200" height="25"><div id="margen"><?= $oOrdenTrabajoTarea->Titulo ?></div></td>	
							<td width="125"><div id="margen"><?= CambiarFechaHora($oOrdenTrabajoHito->FechaHora) ?></div></td>
							<td width="125"><div id="margen"><?= $FechaFin ?></div></td>
							<td width="75" height="25"><div id="margen"><?= $oOrdenTrabajoHito->GetTipoHito() ?></div></td>	
							<td width="75" height="25"><div id="margen"><?= $oOrdenTrabajoHito->Tiempo ?></div></td>	
						</tr>
						<tr>
							<td colspan="5">
								<div align="center">
									<table width="100%"  border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
										</tr>
									</table>
								</div>
							</td>
						</tr>
						<?php 
							$tiempoAcumulado = sum_the_time($tiempoAcumulado, $oOrdenTrabajoHito->Tiempo);
						} 
						?> 
						<tr class="bordeGrisFondo">							
							<td colspan="5" align="right" height="25"><div id="margen"><strong>Total:&nbsp;</strong></div></td>
							<td width="75"><div id="margen"><strong><?= $tiempoAcumulado ?></strong></div></td>
						</tr>
					</table>
				</td>
			</tr>
			<?php 
			} 
			else 
			{ 
			?>
			<tr height="20">
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td><div align="center"> <img src="images/iconos/alerta.gif" border="0"> </div></td>
						</tr>
						<tr>
							<td><div align="center"><strong>No hay ninguna relaci&oacute;n establecida.</strong></div></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>		  
			<?php
			}
			?>
			<tr>
				<td>&nbsp;</td>
			</tr>
			
			<tr>
				<td>
					<table width="100%" border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30">
								<div align="right">
									<label>
										<input name="button" type="button" class="botonBasico" id="button" onclick="javascript: window.location.href = 'ordenestrabajo_detail.php<?=$strParams?>';" value="Volver a ordenes de trabajo" />
									</label>
								</div>
							</td>
							<td width="10" height="30">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</form>
	<div id="modal-popup" style="display:none">
	</div>
</body>
</html>