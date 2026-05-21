<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_RECE_UPDATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdPlanillaRecepcion	= intval($_REQUEST['IdPlanillaRecepcion']);
$NumeroCartaPorte		= strval($_REQUEST['NumeroCartaPorte']);
$FechaRecepcion			= strval($_REQUEST['FechaRecepcion']);
$Observaciones			= strval($_REQUEST['Observaciones']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err					= 0;
$oPlanillasRecepcion	= new PlanillasRecepcion();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
if (!$oPlanillaRecepcion = $oPlanillasRecepcion->GetById($IdPlanillaRecepcion))
{	
	header("Location: recepciones.php" . $strParams);
	exit();
}

/* si el formulario fue enviado... */
if ($Submit)
{
	/* validaciones... */
	if ($NumeroCartaPorte == '')
		$err |= 1;
	elseif (($NumeroCartaPorte != $oPlanillaRecepcion->NumeroCartaPorte) && 
			($oPlanillasRecepcion->GetByNumeroCartaPorte($NumeroCartaPorte)))
		$err |= 2;
	if ($FechaRecepcion == '')
		$err |= 4;
		
	/* si no hay errores... */
	if ($err == 0)
	{
		$oPlanillaRecepcion->NumeroCartaPorte	= $NumeroCartaPorte;
		$oPlanillaRecepcion->FechaRecepcion		= $FechaRecepcion;
		$oPlanillaRecepcion->Observaciones		= $Observaciones;
		$oPlanillaRecepcion->IdEstado			= RecepcionEstados::Pendiente;

		if ($oPlanillaRecepcion = $oPlanillasRecepcion->Update($oPlanillaRecepcion))
		{
			/* enviamos el id generado */
			$strParams.= '&IdPlanillaRecepcion=' . $oPlanillaRecepcion->IdPlanillaRecepcion;
			
			header('Location: recepciones_mod_paso2.php' . $strParams);
			exit;
		}
	}
}
else
{
	$NumeroCartaPorte	= $oPlanillaRecepcion->NumeroCartaPorte;
	$FechaRecepcion		= CambiarFecha($oPlanillaRecepcion->FechaRecepcion);
	$Observaciones		= $oPlanillaRecepcion->Observaciones;
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Recepciones - Modificar</span></td>
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
					<input type="hidden" name="IdPlanillaRecepcion" id="IdPlanillaRecepcion" value="<?=$IdPlanillaRecepcion?>" />
                    
					<table width="80%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table  border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td><div align="right">N&uacute;mero Recepci&oacute;n:</div></td>
										<td>
											<input type="text" class="camporFormularioChico" maxlength="128" value="<?=$IdPlanillaRecepcion?>" disabled="disabled" />
										</td>
									</tr>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td><div align="right">N&uacute;mero Carta Porte:</div></td>
										<td>
											<input type="text" name="NumeroCartaPorte" id="NumeroCartaPorte" class="camporFormularioSimple" maxlength="128" value="<?=$NumeroCartaPorte?>" onkeyup="javascript: StrToUpper(this.id);" />
											<span style="color:#FF0000;">&nbsp;(*)</span>
										</td>
									</tr>
								
                                	<tr>
										<td>&nbsp;</td>
										<td align="left"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese el nro. de carta porte</li><?php } if ($err & 2) { ?><li style="color:#FF0000;">Ya existe registrado el nro. de carta porte</li><?php } ?></td>
                                    </tr>
									<tr>
										<td><div align="right">Fecha de Recepcion:</div></td>
                                        <td>
                                            <div align="left">
                                                <input name="FechaRecepcion" type="text" class="camporFormularioChico" id="FechaRecepcion" value="<?=$FechaRecepcion?>" size="12" maxlength="12" />
                                                <script language="javascript">
                                                new tcal({'formname': 'frmData', 'controlname': 'FechaRecepcion'});
                                                </script>
                                            </div>
                                        </td>
									</tr>
                                	<tr>
										<td>&nbsp;</td>
										<td align="left"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese la fecha del recepcion</li><?php } ?></td>
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
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'recepciones.php<?=$strParams?>';" value="Cancelar" />
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