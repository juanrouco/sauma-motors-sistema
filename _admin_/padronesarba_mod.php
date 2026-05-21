<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();


/* obtiene datos enviados */
$IdPadronArba	= 693815;
$Percepcion	= floatval($_REQUEST['Percepcion']);
$Retencion	= floatval($_REQUEST['Retencion']);
$Submit	= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err		= 0;
$oPadronesArba	= new PadronesArba();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
if (!$oPadronArba = $oPadronesArba->GetById($IdPadronArba))
{
	header('Location: padronesarba_mod.php' . $strParams);
	exit;
}

if ($Submit)
{
	/* validaciones... */
	if ($Percepcion == 0)
		$err |= 1;
	if ($Retencion == 0)
		$err |= 2;
	
	/* si no hay ningun error... */	
	if ($err == 0)
	{
		$oPadronArba->Percepcion	= $Percepcion;
		$oPadronArba->Retencion 	= $Retencion;
		
		$oPadronArba = $oPadronesArba->Update($oPadronArba);

		header("Location: padronesarba_mod.php" . $strParams);
		exit();
	}
}
else
{
	$Percepcion	= $oPadronArba->Percepcion;
	$Retencion = $oPadronArba->Retencion;
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Padron Arba - Modificar GM</span></td>
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
					<input type="hidden" name="IdPadronArba" id="IdPadronArba" value="<?=$IdPadronArba?>" />
                    
					<table width="70%"  border="0" align="center" cellpadding="0" cellspacing="0">
				  		<tr>
							<td class="bordeGris">
								<table  border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td height="20">&nbsp;</td>
										<td height="20">&nbsp;</td>
									</tr>
                                    <tr>
										<td><div align="right">Retencion:</div></td>
										<td>
                                        	<div align="left">
												<input type="text" name="Retencion" id="Retencion" class="camporFormularioSimple" maxlength="16" onkeyup="javascript: StrToUpper(this.id);" value="<?=$Retencion?>" />
												<span style="color:#FF0000;">&nbsp;(*)</span>										
                                          	</div>
                                     	</td>
									</tr>
                                	<tr>
                                    	<td height="20">&nbsp;</td>
                                        <td height="20"><?php if ($err & 1) { ?><li class="error">Ingrese el coeficiente de retencion</li><?php } ?></td>
                                    </tr>
									<tr>
										<td><div align="right">Percepcion:</div></td>
										<td>
                                        	<div align="left">
												<input type="text" name="Percepcion" id="Percepcion" class="camporFormularioSimple" maxlength="128" onkeyup="javascript: StrToUpper(this.id);" value="<?=$Percepcion?>" />
												<span style="color:#FF0000;">&nbsp;(*)</span>										
                                          	</div>
                                     	</td>
									</tr>
                                	<tr>
										<td height="20">&nbsp;</td>
										<td height="20" align="left"><?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese el coeficiente de percepcion</li><?php } ?></td>
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