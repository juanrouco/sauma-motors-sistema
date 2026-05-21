<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_FACU_UPDATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdFactura			= intval($_REQUEST['IdFactura']);
$Submit				= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err				= 0;
$oFacturaVarias		= new FacturaVarias();
$oComprobantes		= new Comprobantes();
$oClientes			= new Clientes();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
if (!$oFacturaVaria = $oFacturaVarias->GetById($IdFactura))
{
	header("Location: facturavarias.php" . $strParams);
	exit;
}

/* verificamos si existe el comprobante */
if (!$oComprobante = $oComprobantes->GetById($oFacturaVaria->IdComprobante))
{
	header("Location: facturavarias.php" . $strParams);
	exit;
}

/* verificamos si existe el comprobante */
if (!$oCliente = $oClientes->GetById($oComprobante->IdCliente))
{
	header("Location: facturavarias.php" . $strParams);
	exit;
}

/* si el fomulario fue enviado */
if ($Submit)
{
	if ($err == 0)
	{	
		$oFacturaVarias->Delete($IdFactura);
		$oComprobantes->Delete($oComprobante->IdComprobante);
		
		header("Location: facturavarias.php" . $strParams);
		exit;
	}
}


/* incluimkos funcion para armar suggest */
IncludeSUGGEST();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script type="text/javascript">


</script>

</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	<tr>
		<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
				<tr>
					<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
					<td height="40"><span class="tituloPagina">Administraci&oacute;n de Facturas - Eliminar</span></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td valign="top">&nbsp;</td>
	</tr>
	<tr>
		<td>
			<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
						<input type="hidden" name="Submitted" id="Submitted" value="1" />
						<input type="hidden" name="IdFactura" id="IdFactura" value="<?=$IdFactura?>" />
						<input type="hidden" name="IdComprobante" id="IdComprobante" value="<?=$IdComprobante?>" />
		  	<div align="center">
				<table width="60%"  border="0" align="center" cellpadding="4" cellspacing="0">
					<tr>
						<td class="bordeGris">
							<table  border="0" align="center" cellpadding="0" cellspacing="0">
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td><div align="center"><strong>&iquest;Esta seguro que desea eliminar la siguiente factura?</strong></div></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td><div align="center" class="campoEliminar"><?= $oCliente->RazonSocial ?></div></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
						  	</table>						
                      	</td>
					</tr>
				</table>
                <table width="60%" border="0" cellspacing="0" cellpadding="0">
                  	<tr>
                    	<td height="1"><div align="center"></div></td>
                  	</tr>
                </table>
          		<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
					
						<tr>
						  	<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar">
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" value="Cancelar" onClick="javascript: window.location.href = 'facturavarias.php<?=$strParams?>';">
								</div>
							</td>
						</tr>
					
				</table>
			</div>
			</form>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
</table>

</body>
</html>