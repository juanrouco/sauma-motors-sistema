<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CUPON_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$Numero				= strval($_REQUEST['Numero']);
$Descuento			= floatval($_REQUEST['Descuento']);
$Submit				= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err				= 0;
$oCuponesDescuento	= new CuponesDescuento();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

if ($Submit)
{
	/* validaciones... */
	if ($Numero == '')
		$err |= 1;
	if ($Descuento == '')
		$err |= 2;
	if ($Descuento > 100)
		$err |= 4;
	if ($oCuponesDescuento->GetByNumero($Numero))
		$err |= 8;

	/* verificamos que no se solapen numeros */
	if ($err == 0)
	{
		$oCuponDescuento = new CuponDescuento();
		$oCuponDescuento->Numero 	= $Numero;
		$oCuponDescuento->IdEstado 	= ComprobanteEstados::Libre;
		$oCuponDescuento->Descuento = $Descuento;
		
		if ($oCuponesDescuento->Create($oCuponDescuento))
		{
			header("Location: cuponesdescuento.php" . $strParams);
			exit();
		}
	}
}
else
{
	$Numero	= uniqid('');;
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Cupones de Descuento - Agregar</span></td>
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
				<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
					<input type="hidden" name="Submitted" id="Submitted" value="1" />
                    
					<table width="80%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table  border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
									</tr>
									<tr>
										<td><div align="right">N&uacute;mero:</div></td>
										<td>
											<input type="text" name="Numero" id="Numero" class="camporFormularioMediano" maxlength="8" onkeyup="javascript: StrToUpper(this.id);" value="<?=$Numero?>" />
											<span style="color:#FF0000;">&nbsp;(*)</span>
										</td>
									</tr>
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20" align="left" colspan="2">
											<?php 
											if ($err & 1)
											{ 
											?>
											<li style="color:#FF0000;">Ingrese el n&uacute;mero</li>
											<?php 
											}
											if ($err & 8) 
											{ 
											?>
											<li style="color:#FF0000;">El n&uacute;mero ya ha sido utilizado para otro cup&oacute;n</li>
											<?php 
											} 
											?>
										</td>
                                    </tr>
									<tr>
										<td><div align="right">Descuento:</div></td>
										<td>
											<input type="text" name="Descuento" id="Descuento" class="camporFormularioMediano" maxlength="5" onkeyup="javascript: StrToUpper(this.id);" value="<?=$Descuento?>" />%
											<span style="color:#FF0000;">&nbsp;(*)</span>
										</td>
									</tr>
									<tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20" align="left" colspan="2">
											<?php 
											if ($err & 2)
											{ 
											?>
											<li style="color:#FF0000;">Ingrese el descuento</li>
											<?php 
											}
											if ($err & 4) 
											{ 
											?>
											<li style="color:#FF0000;">El descuento ingresado debe estar entre 0 y 100%</li>
											<?php 
											} 
											?>
										</td>
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
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'cuponesdescuento.php<?=$strParams?>';" value="Cancelar" />
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