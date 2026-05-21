<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_REMI_DELETE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdRemito	= intval($_REQUEST['IdRemito']);
$Submit		= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err			= 0;
$oRemitos		= new Remitos();
$oComprobantes	= new Comprobantes();
$oMinutas		= new Minutas();
$oUnidades		= new Unidades();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
if (!$oRemito = $oRemitos->GetById($IdRemito))
{
	header("Location: remitos.php" . $strParams);
	exit;
}

/* verificamos si existe el comprobante */
if (!$oComprobante = $oComprobantes->GetById($oRemito->IdComprobante))
{
	header("Location: remitos.php" . $strParams);
	exit;
}

/* obtenemos los datos de la venta */
if (!$oMinuta = $oMinutas->GetById($oRemito->IdMinuta))
{	
	header("Location: remitos.php" . $strParams);
	exit();
}

/* obtenemos los datos de la unidad */
if (!$oUnidad = $oUnidades->GetById($oMinuta->IdUnidad))
{	
	header("Location: remitos.php" . $strParams);
	exit();
}

/* si el fomulario fue enviado */
if ($Submit)
{
	/* actualizamos el estado del comprobante */
	$oComprobante->IdEstado = ComprobanteEstados::Anulado;
	
	if ($oComprobante = $oComprobantes->Update($oComprobante))
	{
		/* actualizamos el estado de la unidad, volviendo a estado facturado */
		$oUnidad->IdEstado = EstadoUnidad::Facturado;
		
		$oUnidades->Update($oUnidad);
	}

	header("Location: remitos.php" . $strParams);
	exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	<tr>
		<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
				<tr>
					<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
					<td height="40"><span class="tituloPagina">Administraci&oacute;n de Remitos - Anular</span></td>
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
				<table width="60%"  border="0" align="center" cellpadding="4" cellspacing="0">
					<tr>
						<td class="bordeGris">
							<table  border="0" align="center" cellpadding="0" cellspacing="0">
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td><div align="center"><strong>&iquest;Esta seguro que desea anular el siguiente remito?</strong></div></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td><div align="center" class="campoEliminar"><?=$oComprobante->Prefijo . ' - ' . $oComprobante->Numero?></div></td>
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
					<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
						<input type="hidden" name="Submitted" id="Submitted" value="1" />
						<input type="hidden" name="IdRemito" id="IdRemito" value="<?=$IdRemito?>" />
						<tr>
						  	<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar">
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" value="Cancelar" onClick="javascript: window.location.href = 'remitos.php<?=$strParams?>';">
								</div>
							</td>
						</tr>
					</form>
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