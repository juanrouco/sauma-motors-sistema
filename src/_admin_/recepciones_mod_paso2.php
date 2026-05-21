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
$Observaciones			= strval($_REQUEST['Observaciones']);
$Action					= strval($_REQUEST['MainAction']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err					= 0;
$oUnidades				= new Unidades();
$oModelos				= new Modelos();
$oPlanillasRecepcion	= new PlanillasRecepcion();

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
	/* actualizamos las observciones del recepcion */
	$oPlanillaRecepcion->Observaciones = $Observaciones;
	
	$oPlanillasRecepcion->Update($oPlanillaRecepcion);
	
	/* procesamos la accion requerida */
	switch ($Action)
	{
		case 'Add':
		
			/* obtenemos los datos del detalle a agregar */
			$NumeroVin 		= strval($_REQUEST['NumeroVin']);
			$IdUnidad 		= intval($_REQUEST['IdUnidad']);
			$VehiculoModelo	= strval($_REQUEST['VehiculoModelo']);
			$CodigoLlaves	= strval($_REQUEST['CodigoLlaves']);

			/* validaciones... */
			if ($IdUnidad == '')
				$err |= 1;
			elseif (($oUnidad = $oUnidades->GetById($IdUnidad)) && ($oUnidad->IdPlanillaRecepcion != ''))
				$err |= 2;
			/*if ($CodigoLlaves == '')
				$err |= 4;*/
		
			/* si no hay errores... */
			if ($err == 0)
			{
				$oUnidad->IdPlanillaRecepcion 	= $IdPlanillaRecepcion;
				$oUnidad->CodigoLlaves			= $CodigoLlaves;
				
				$oUnidad = $oUnidades->Update($oUnidad);

				$Operation = Operaciones::Create;
				$Status = (($oUnidad) ? true : false);
				
				/* seteamos variables en null */
				$NumeroVin 		= '';
				$IdUnidad 		= '';
				$VehiculoModelo = '';
				$CodigoLlaves 	= '';
			}
		
			break;

		case 'Delete':

			/* obtenemos el detalle a eliminar */
			$IdUnidad = intval($_REQUEST['Id']);

			/* si no hay errores... */
			if ($oUnidad = $oUnidades->GetById($IdUnidad))
			{
				$oUnidad->IdPlanillaRecepcion 	= '';
				$oUnidad->CodigoLlaves			= '';
				$oUnidad->IdUbicacion			= Ubicacion::Transito;

				$oUnidad = $oUnidades->Update($oUnidad);

				$Operation = Operaciones::Delete;
				$Status = (($oUnidad) ? true : false);
			}

			break;

		case 'Next':

			/* verificamos y actualizamos numero carta porte y codigo llaves de los registros existentes */
			foreach ($arrData as $oUnidad)
			{
				$CodigoLlaves = strval($_REQUEST['CodigoLlaves_' . $oUnidad->IdUnidad]);

				$oUnidad->CodigoLlaves = $CodigoLlaves;
				
				$oUnidad = $oUnidades->Update($oUnidad);
			}	
		
			header('Location: recepciones_mod_paso3.php' . $strParams);
			exit;
		
			break;

		default:
			break;
	}
}

/* obtenemos todos las unidades de la recepcion */
$arrData = $oPlanillaRecepcion->GetAllUnidades();

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

function FilterNumeroVin(IdUnidad, NumeroVin)
{	
	if ((IdUnidad == '') && (NumeroVin == ''))
	{
		Get('NumeroVin').value 		= '';
		Get('IdUnidad').value 		= '';
		Get('VehiculoModelo').value = '';
	}

	var oUnidad = GetUnidad(IdUnidad);
	if (!(oUnidad))
		return;

	var oModelo = GetModelo(oUnidad.IdModelo);
	if (!(oModelo))
		return;

	Get('NumeroVin').value 		= oUnidad.NumeroVin;
	Get('IdUnidad').value 		= oUnidad.IdUnidad;
	Get('VehiculoModelo').value = oModelo.DenominacionModelo;
}

function Add()
{
	var frmData 	= Get('frmData');
	var MainAction 	= Get('MainAction');
				
	if (frmData == undefined)
		return false;

	MainAction.value = 'Add';	
	frmData.submit();
	
	return true;
}

function Delete(IdUnidad)
{
	var frmData 	= Get('frmData');
	var MainAction 	= Get('MainAction');
	var IdField 	= Get('Id');
					
	if (frmData == undefined)
		return false;

	if (confirm('¿Desea realmente eliminar el registro?'))
	{
		MainAction.value = 'Delete';	
		IdField.value = IdUnidad;	
		frmData.submit();
	}
	
	return true;
}

function Next()
{
	var frmData 	= Get('frmData');
	var MainAction 	= Get('MainAction');
				
	if (frmData == undefined)
		return false;

	MainAction.value = 'Next';	
	frmData.submit();
	
	return true;
}

</script>

</head>
<body>

<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
    <input type="hidden" name="Id" id="Id" value="" />
    <input type="hidden" name="MainAction" id="MainAction" value="" />
    <input type="hidden" name="Submitted" id="Submitted" value="1" />
    <input type="hidden" name="IdPlanillaRecepcion" id="IdPlanillaRecepcion" value="<?=$IdPlanillaRecepcion?>" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de PlanillasRecepcion - Agregar y Modificar Unidades</span></td>
                    </tr>
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
                        <td>&nbsp;</td>
                        <td width="124">&nbsp;</td>
                        <td width="636">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="8">&nbsp;</td>
                        <td height="25"><strong>N&uacute;mero Recepcion: </strong></td>
                        <td height="25"><?=$oPlanillaRecepcion->IdPlanillaRecepcion?></td>
                    </tr>
                    <tr>
                        <td width="8">&nbsp;</td>
                        <td height="25"><strong>N&uacute;mero Carta Porte: </strong></td>
                        <td height="25"><?=$oPlanillaRecepcion->NumeroCartaPorte?></td>
                    </tr>
                    <tr>
                        <td width="8">&nbsp;</td>
                        <td height="25"><strong>Fecha Recepcion: </strong></td>
                        <td height="25"><?=CambiarFecha($oPlanillaRecepcion->FechaRecepcion)?></td>
                    </tr>
                    <tr>
                        <td width="8">&nbsp;</td>
                        <td height="25"><strong>Observaciones: </strong></td>
                        <td height="25">
                        	<textarea name="Observaciones" id="Observaciones" class="camporFormularioMultiline"><?=$Observaciones?></textarea>
                        </td>
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
        <tr>
            <td>
                <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                    	<td>
                            <table border="0" align="left" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>&nbsp;</td>
                                    <td><div id="margen" align="left">N&uacute;mero Vin:</div></td>
                                    <td><div id="margen" align="left">Interno:</div></td>
                                    <td><div id="margen" align="left">Veh&iacute;culo Modelo:</div></td>
                                    <td><div id="margen" align="left">C&oacute;digo Llaves:</div></td>
                                    <td><div id="margen" align="left">&nbsp;</div></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>
                                        <div align="left">
                                            <input type="text" name="NumeroVin" id="NumeroVin" class="camporFormularioMedianoI" maxlength="128" value="<?=$NumeroVin?>" onkeyup="javascript: StrToUpper(this.id);" autocomplete="off" />
                                            <script language="">
                                            SUGGESTRequest('Unidades', 'GetAllTransito', 'NumeroVin', 'FilterNumeroVin', 'IdUnidad', 'NumeroVin', 'FilterNumeroVin', null);
                                            </script>
                                        </div>
                                    </td>
                                    <td>
                                        <div align="left">
                                            <input type="text" name="IdUnidad" id="IdUnidad" class="camporFormularioChicoSuggest" maxlength="5" value="<?=$IdUnidad?>" readonly="readonly" />
                                        </div>
                                    </td>
                                    <td>
                                        <div align="left">
                                            <input type="text" name="VehiculoModelo" id="VehiculoModelo" class="camporFormularioMedianoDisabled" maxlength="128" value="<?=$VehiculoModelo?>" readonly="readonly" />
                                        </div>
                                    </td>
                                    <td>
                                        <div align="left">
                                            <input type="text" name="CodigoLlaves" id="CodigoLlaves" class="camporFormularioMedianoI" maxlength="128" value="<?=$CodigoLlaves?>" onkeyup="javascript: StrToUpper(this.id);" />
                                        </div>
                                    </td>
                                    <td><input type="button" name="btnAgregar" value="Agregar" class="botonBasico" onClick="javascript: Add();" /></td>
                                </tr>
                                
                            <?php if (($err != 0)) { ?>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td align="left"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese nro. vin</li><?php } if ($err & 2) { ?><li style="color:#FF0000;">La unidad ya existe cargada en el recepcion</li><?php } ?></td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td align="left"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese cod. llaves</li><?php } ?></td>
                                    <td>&nbsp;</td>
                                </tr>
                            <?php } ?>
                                
                            </table>
                      	</td>
                    </tr>
                    <tr>
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
                        <td width="120" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Nro. Interno</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>N&uacute;mero Vin</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Denominaci&oacute;n Modelo</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>C&oacute;digo Llaves</strong></div></td>
                        <td width="83" class="bordeGrisTitulo"><div align="center"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php foreach ($arrData as $oUnidad) { ?>
                    <?php $oModelo = $oModelos->GetById($oUnidad->IdModelo); ?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="120" height="25"><div id="margen"><?=$oUnidad->IdUnidad?></div></td>
                        <td width="120" height="25"><div id="margen"><?=$oUnidad->NumeroVin?></div></td>
                        <td width="258" height="25"><div id="margen"><?=$oModelo->DenominacionComercial?></div></td>
                        <td width="262" height="25"><div id="margen"><input type="text" name="CodigoLlaves_<?=$oUnidad->IdUnidad?>" id="CodigoLlaves_<?=$oUnidad->IdUnidad?>" class="camporFormularioMedianoI" maxlength="128" value="<?=$oUnidad->CodigoLlaves?>" onkeyup="javascript: StrToUpper(this.id);" /></div></td>
                        <td width="83" height="25">
                            <div align="center"> 
                                <a href="javascript: void(0);" onclick="Delete('<?=$oUnidad->IdUnidad?>');"><img src="images/iconos/del.gif" border="0" /></a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">
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
                            	<input type="button" name="btnSiguiente" class="botonBasico" id="btnSiguiente" value="Siguiente" onClick="javascript: Next();" />
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    
    <?php } else { ?>  
    
        <tr>
            <td>
                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td><div align="center"> <img src="images/iconos/alerta.gif" border="0"> </div></td>
                    </tr>
                    <tr>
                        <td><div align="center"><strong>La recepci&oacute;n no posee unidades cargadas.</strong></div></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
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