<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_RECE_UPDATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdPlanillaRecepcion	= intval($_REQUEST['IdPlanillaRecepcion']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err					= 0;
$oPlanillasRecepcion	= new PlanillasRecepcion();
$oUnidades				= new Unidades();
$oModelos				= new Modelos();
$oUbicaciones			= new Ubicaciones();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verificamos si existe el recepcion */
if (!$oPlanillaRecepcion = $oPlanillasRecepcion->GetById($IdPlanillaRecepcion))
{	
	header("Location: recepciones.php" . $strParams);
	exit();
}

/* obtenemos todos las unidades de la recepcion */
$arrData = $oPlanillaRecepcion->GetAllUnidades();

/* si el formulario fue enviado... */
if ($Submit)
{
	/* actualizamos el estado del recepcion */
	$oPlanillaRecepcion->IdEstado = RecepcionEstados::Aprobado;
	
	$oPlanillasRecepcion->Update($oPlanillaRecepcion);
	
	/* actualizamos la ubicacion de cada unidad de la recepcion, pasando a PILAR */
	foreach ($arrData as $oUnidad)
	{
		$oUnidad->IdUbicacion = $_REQUEST['IdUbicacion_' . $oUnidad->IdUnidad];
		
		/* actualizamos el registro */		
		$oUnidades->Update($oUnidad);		
	}	

	header('Location: recepciones.php' . $strParams);
	exit;
}


$arrUbicaciones = $oUbicaciones->GetAll();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include('include/head.inc.php'); ?>
</head>
<body>

<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
    <input type="hidden" name="Submitted" id="Submitted" value="1" />
    <input type="hidden" name="IdPlanillaRecepcion" id="IdPlanillaRecepcion" value="<?=$IdPlanillaRecepcion?>" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Recepciones - Aprobar Modificaci&oacute;n</span></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td>&nbsp;</td>
                        <td width="126">&nbsp;</td>
                        <td width="647">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="9">&nbsp;</td>
                        <td height="25"><strong>N&uacute;mero Recepcion: </strong></td>
                        <td height="25"><?=$oPlanillaRecepcion->IdPlanillaRecepcion?></td>
                    </tr>
                    <tr>
                        <td width="9">&nbsp;</td>
                        <td height="25"><strong>N&uacute;mero Carta Porte: </strong></td>
                        <td height="25"><?=$oPlanillaRecepcion->NumeroCartaPorte?></td>
                    </tr>
                    <tr>
                        <td width="9">&nbsp;</td>
                        <td height="25"><strong>Fecha Recepcion: </strong></td>
                        <td height="25"><?=CambiarFecha($oPlanillaRecepcion->FechaRecepcion)?></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td height="25"><strong>Observaciones: </strong></td>
                        <td height="25"><?=$oPlanillaRecepcion->Observaciones?></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </table>		
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
    
    <?php if ($arrData != NULL) { ?>
            
        <tr>
            <td>
                <table width="90%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                    <tr class="bordeGrisFondo">
                        <td width="100" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Nro. Interno</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>N&uacute;mero Vin</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Denominaci&oacute;n Modelo</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>C&oacute;digo Llaves</strong></div></td>
						<td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Ubicaci&oacute;n</strong></div></td>
                    </tr>
          
                <?php foreach ($arrData as $oUnidad) { ?>
                    <?php $oModelo = $oModelos->GetById($oUnidad->IdModelo); ?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="100" height="25"><div id="margen"><?=$oUnidad->IdUnidad?></div></td>
                        <td width="130" height="25"><div id="margen"><?=$oUnidad->NumeroVin?></div></td>
                        <td width="316" height="25"><div id="margen"><?=$oModelo->DenominacionComercial?></div></td>
                        <td width="317" height="25"><div id="margen"><?=$oUnidad->CodigoLlaves?></div></td>
						<td width="317" height="25">
							<div id="margen">
								<select id="IdUbicacion_<?= $oUnidad->IdUnidad ?>" name="IdUbicacion_<?= $oUnidad->IdUnidad ?>" class="camporFormularioSimple">
									<?php
										foreach ($arrUbicaciones as $oUbicacion)
										{
											if ($oUbicacion->IdUbicacion != Ubicacion::Transito)
											{
												$selected = '';
												if ($oUbicacion->IdUbicacion == $oUnidad->IdUbicacion)
													$selected = "selected='selected'";
									?>
									<option value="<?= $oUbicacion->IdUbicacion ?>" <?= $selected ?>><?= $oUbicacion->Nombre ?></option>
									<?php
											}
										}
									?>
								</select>
							</div>
						</td>
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
          
                <?php } ?>      
                
                </table>
          	</td>
      	</tr>
        <tr>
        	<td>&nbsp;</td>
        </tr>
        <tr>
        	<td>
                <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td height="30">
                            <div align="center">
                            	<input type="submit" name="btnConfirmar" class="botonBasico" id="btnConfirmar" value="Confirmar" />
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    
    <?php } ?>  
    
        <tr>
        	<td>&nbsp;</td>
        </tr>
    </table>
</form>

</body>
</html>