<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_TARE_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$Costo	= floatval($_REQUEST['Costo']);
$Submit	= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err				= 0;
$oCostosManoObra	= new CostosManoObra();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];


if ($Submit)
{
	/* validaciones... */
	if ($Costo == '')
		$err |= 1;
		
	/* si no hay ningun error... */	
	if ($err == 0)
	{
		$Costo = $oCostosManoObra->Update($Costo);
		$Operation = Operaciones::Update;
		$Status = (($Costo) ? true : false);
	}
}
else
{
	$Costo	= $oCostosManoObra->GetLast();
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
        			<td width="20" height="40" class="TituloRubro">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Costo de Mano de Obra - Modificar</span></td>
      			</tr>
    		</table>
		</td>
  	</tr>
  	<tr>
    	<td valign="top">&nbsp;</td>
  	</tr>
	<?php echo Operaciones::PrintResult($Operation, $Status); ?>
	<tr>
    	<td valign="top">&nbsp;</td>
  	</tr>
  	<tr>
    	<td>
			<div align="center">
				<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
					<input type="hidden" name="Submitted" id="Submitted" value="1" />
                    
					<table width="70%"  border="0" align="center" cellpadding="0" cellspacing="0">
				  		<tr>
							<td class="bordeGris">
								<table  border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
									</tr>
                                    <tr>
										<td><div align="right">Codigo:</div></td>
										<td>
                                        	<div align="left">
												$<input type="text" name="Costo" id="Costo" class="camporFormularioSimple" maxlength="16" onkeyup="javascript: StrToUpper(this.id);" value="<?=$Costo?>" />
												<span style="color:#FF0000;">&nbsp;(*)</span>										
                                          	</div>
                                     	</td>
									</tr>
                                	<tr>
                                    	<td height="20">&nbsp;</td>
                                        <td height="20"><?php if ($err & 1) { ?><li class="error">Ingrese el costo de la mano de obra</li><?php } ?></td>
                                    </tr>									
								</table>							
                          	</td>
						</tr>
					</table>
	            	<table width="70%" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td height="1"><div align="center"></div></td>
                      </tr>
                    </table>
      				<table width="70%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'ordenestrabajo.php<?=$strParams?>';" value="Cancelar" />
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