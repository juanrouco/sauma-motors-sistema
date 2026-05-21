<?php
require_once('../inc_library.php');

$oOrdenTrabajoHitos = new OrdenTrabajoHitos();
$oOrdenTrabajoHitos->ActualizarEstados();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php include('include/head.inc.php'); ?>
<script type="text/javascript">
$j(document).ready(function() {
	$j('#NumeroMecanico').focus();
	$j('#NumeroMecanico').keyup(function(e) {
		if ($j('#NumeroMecanico').val().length == 9) {
			$j('#frmData').submit();
			return false;
		}
	});
	var mensaje = '';
	<?php
	if ($_REQUEST['Mensaje'] == '1')
	{
	?>
		mensaje = 'Atención: La Orden de Trabajo no pudo ser encontrada.\nPor favor reintente.';
	<?php
	} elseif ($_REQUEST['Mensaje'] == '2')
	{
	?>
		mensaje = 'Atención: La Orden de Trabajo no se encuentra disponible para comenzar a trabajar.';
	<?php
	} elseif ($_REQUEST['Mensaje'] == '3')
	{
	?>
		mensaje = 'Atención: La Orden de Trabajo no se encuentra asignada al mecanico.';
	<?php
	} elseif ($_REQUEST['Mensaje'] == '4')
	{
	?>
		mensaje = 'Atención: La Orden de Trabajo ha sido iniciada.';
	<?php
	} elseif ($_REQUEST['Mensaje'] == '5')
	{
	?>
		mensaje = 'Atención: La Orden de Trabajo ha sido detenida.';
	<?php
	} elseif ($_REQUEST['Mensaje'] == '6')
	{
	?>
		mensaje = 'Atención: Mecánico no encontrado.';
	<?php
	} elseif ($_REQUEST['Mensaje'] == '7')
	{
	?>
		mensaje = 'Atención: El mecánico tiene tareas abiertas, para comenzar una nueva debe finalizar las anteriores.';
	<?php
	}
	?>
	if (mensaje != '')
	{
		$j('<div></div>').appendTo('body').html('<div><h6>' + mensaje + '</h6></div>').dialog({
			modal: true, title: 'Alerta', zIndex: 10000, autoOpen: true,
			width: 'auto', resizable: false,
			buttons: {
				Aceptar: function () {
					$j(this).dialog("close");
				}
			},
			close: function (event, ui) {
				$j(this).remove();
				$j('#NumeroMecanico').focus();
			}
		});
	}
	
});
</script>
</head>
<body>
<table width="100%" height="68"  border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td><div align="center"></div></td>
	</tr>
</table>
<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="bordeGris">
	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Fichado de Tareas</span></td>
      			</tr>
    		</table>
		</td>
  	</tr>
	<tr>
		<td><p align="center">&nbsp;</p></td>
	</tr>
	<tr>
		<td>
			<div align="center">
				<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
				<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><p align="center" class="tituloPagina">Por favor, escanee el c&oacute;digo de barras del mec&aacute;nico para continuar.</p></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>
						
						<form id="frmData" action="iniciartrabajo_2.php" method="post">
						<p align="center" class="pNegroBold10">
							<input type="text" name="NumeroMecanico" id="NumeroMecanico" />
							</p>
						</form>
						
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
				</tr>
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