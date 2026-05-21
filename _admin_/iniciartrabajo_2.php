<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 


$IdUsuario			= intval($_REQUEST['NumeroMecanico']);

$oOrdenTrabajo		= new OrdenTrabajo();
$oOrdenesTrabajo	= new OrdenesTrabajo();
$oEstadosOrden		= new EstadosOrden();
$oTallerUnidades	= new TallerUnidades();
$oUsuarios			= new Usuarios();
$oClientes			= new Clientes();
$oMarcas			= new Marcas();
$oOrdenesTrabajoTareas			= new OrdenesTrabajoTareas();
$oOrdenesTrabajoTareasArticulos	= new OrdenesTrabajoTareasArticulos();
$oTareasTrabajoArticulos 		= new TareasTrabajoArticulos();
$oOrdenTrabajoHitos 			= new OrdenTrabajoHitos();
$oOrdenTrabajoComentarios		= new OrdenTrabajoComentarios();
$oCompras						= new Compras();
$oArticulos						= new Articulos();	
$oCuponesDescuento				= new CuponesDescuento();

$strParams = '?' . $_SERVER['QUERY_STRING'];

if (!$oUsuario = $oUsuarios->GetById($IdUsuario))
{
	header("Location: iniciartrabajo.php?Mensaje=6");
	exit();
}


IncludeSUGGEST();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>
<script type="text/javascript">
$j(document).ready(function() {
	$j('#NumeroOrdenTrabajo').focus();
	$j('#NumeroOrdenTrabajo').keyup(function(e) {
		if ($j('#NumeroOrdenTrabajo').val().length == 9) {
			$j('#frmData').submit();
			return false;
		}
	});
});
</script>

</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
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
    	<td valign="top">&nbsp;</td>
  	</tr>	
  	<tr>
    	<td>
			<div align="center">
				<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td><p align="center" class="tituloPagina"><strong>Mec&aacute;nico seleccionado: <?= $oUsuario->Nombre ?> <?= $oUsuario->Apellido ?></strong></p></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td><p align="center" class="tituloPagina">Por favor, escanee el c&oacute;digo de barras de la Orden de Trabajo para continuar.</p></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td height="30">
							<form id="frmData" action="iniciartrabajo_3.php" method="post">
							<input type="hidden" name="IdUsuario" id="IdUsuario" value="<?= $IdUsuario ?>" />
							<p align="center" class="pNegroBold10">
								<input type="text" name="NumeroOrdenTrabajo" id="NumeroOrdenTrabajo" />
								</p>
							</form>
						</td>
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