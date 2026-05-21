<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_MINP_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$Fecha				= strval($_REQUEST['Fecha']);
$MontoDisponible	= strval($_REQUEST['MontoDisponible']);
$Submit				= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err					= 0;
$oMinutaPago 	= new MinutaPago();
$oMinutasPago	= new MinutasPago();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* si el formulario fue enviado... */
if ($Submit)
{
	/* validaciones... */
	if ($Fecha == '')
		$err |= 1;
	if ($MontoDisponible == '')
		$err |= 2;
		
	/* si no hay errores... */
	if ($err == 0)
	{
		$MontoDisponible = str_replace(',', '.', $MontoDisponible);
		$oMinutaPago->Fecha				= $Fecha;
		$oMinutaPago->MontoDisponible	= $MontoDisponible;
		$oMinutaPago->IdEstado			= RecepcionEstados::Pendiente;

		if ($oMinutaPago = $oMinutasPago->Create($oMinutaPago))
		{
			/* enviamos el id generado */
			$strParams.= '&IdMinutaPago=' . $oMinutaPago->IdMinutaPago;
			
			header('Location: minutaspago_seleccion_unidades.php' . $strParams);
			exit;
		}
	}
}
else
{
	/* determinamos como fecha de recepcion a la fecha de ayer */
	$Fecha = date("Y-m-d");
	$Fecha = CambiarFecha($Fecha);
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Minutas de Pago - Agregar</span></td>
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
				<form name="frmData" id="frmData" method="post" enctype="multipart/form-data" action="minutaspago_add_paso1.php<?=$strParams?>">
					<input type="hidden" name="Submitted" id="Submitted" value="1" />
					<input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
                    
					<table width="80%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table  border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td><div align="right">Fecha:</div></td>
                                        <td>
                                            <div align="left">
                                                <input name="Fecha" type="text" class="camporFormularioChico" id="Fecha" value="<?=$Fecha?>" size="12" maxlength="12" />
                                                <script language="javascript">
                                                new tcal({'formname': 'frmData', 'controlname': 'Fecha'});
                                                </script>
                                            </div>
                                        </td>
									</tr>
								
                                	<tr>
										<td>&nbsp;</td>
										<td align="left"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese la fecha de la minuta de pago</li><?php } ?></td>
                                    </tr>
									<tr>
										<td><div align="right">Monto Disponible:</div></td>
                                        <td>
                                            <div align="left">
                                                <input name="MontoDisponible" type="text" class="camporFormularioMediano" id="MontoDisponible" value="<?=$MontoDisponible?>" />
                                            </div>
                                        </td>
									</tr>
								
                                	<tr>
										<td>&nbsp;</td>
										<td align="left"><?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese el monto disponible</li><?php } ?></td>
                                    </tr>
								</table>
							</td>
						</tr>
						<tr>
							<td><div align="center"></div></td>
						</tr>
					</table>
					<table width="80%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30">
								<div align="center">
									<input type="submit" name="btnSiguiente" class="botonBasico" id="btnSiguiente" value="Siguiente" />
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'minutaspago.php<?=$strParams?>';" value="Cancelar" />
								</div>
							</td>
						</tr>
					</table>
				</form>
    		</div>
		</td>
  	</tr>
  	<tr>
    	<td>&nbsp;</td>
  	</tr>
</table>

</body>
</html>