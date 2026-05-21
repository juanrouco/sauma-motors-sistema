<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_COMP_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdPlanillaCompra	= intval($_REQUEST['IdPlanillaCompra']);
$Action				= strval($_REQUEST['MainAction']);
$Submit				= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err				= 0;
$oUnidades			= new Unidades();
$oModelos			= new Modelos();
$oPlanillasCompra	= new PlanillasCompra();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verificamos si existe el compra */
if (!$oPlanillaCompra = $oPlanillasCompra->GetById($IdPlanillaCompra))
{	
	header("Location: compras.php" . $strParams);
	exit();
}

/* si el formulario fue enviado... */
if ($Submit)
{
	/* procesamos la accion requerida */
	switch ($Action)
	{
		case 'Add':
		
			/* obtenemos los datos del detalle a agregar */
			$NumeroVin 				= strval($_REQUEST['NumeroVin']);
			$IdUnidad 				= intval($_REQUEST['IdUnidad']);
			$NumeroFacturaCompra	= strval($_REQUEST['NumeroFacturaCompra']);
			$ImporteCompraNeto		= floatval($_REQUEST['ImporteCompraNeto']);

			/* validaciones... */
			if ($IdUnidad == '')
				$err |= 1;
			elseif (($oUnidad = $oUnidades->GetById($IdUnidad)) && ($oUnidad->IdPlanillaCompra != ''))
				$err |= 2;
			if ($ImporteCompraNeto == '')
				$err |= 4;
		
			/* si no hay errores... */
			if ($err == 0)
			{
				$oUnidad->IdPlanillaCompra 	= $IdPlanillaCompra;
				$oUnidad->ImporteCompraNeto	= $ImporteCompraNeto;
				
				/* actualizamos el registro */
				$oUnidad = $oUnidades->Update($oUnidad);

				$Operation = Operaciones::Create;
				$Status = (($oUnidad) ? true : false);

				/* seteamos variables en null */
				$NumeroVin 				= '';
				$IdUnidad 				= '';
				$NumeroFacturaCompra 	= '';
				$ImporteCompraNeto 		= '';
			}
		
			break;

		case 'Delete':

			/* obtenemos el detalle a eliminar */
			$IdUnidad = intval($_REQUEST['Id']);

			/* si no hay errores... */
			if ($oUnidad = $oUnidades->GetById($IdUnidad))
			{
				$oUnidad->IdPlanillaRecepcion 	= '';
				$oUnidad->ImporteCompraNeto		= '';

				$oUnidad = $oUnidades->Update($oUnidad);

				$Operation = Operaciones::Delete;
				$Status = (($oUnidad) ? true : false);
			}

			break;

		case 'Finish':
		
			header('Location: compras.php' . $strParams);
			exit;
		
			break;

		default:
			break;
	}
}

/* obtenemos todas las unidades de la compra */
$arrData = $oPlanillaCompra->GetAllUnidades();

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
		Get('NumeroVin').value 				= '';
		Get('IdUnidad').value 				= '';
		Get('NumeroFacturaCompra').value 	= '';
	}

	var oUnidad = GetUnidad(IdUnidad);
	if (!(oUnidad))
		return;

	Get('NumeroVin').value 				= oUnidad.NumeroVin;
	Get('IdUnidad').value 				= oUnidad.IdUnidad;
	Get('NumeroFacturaCompra').value 	= oUnidad.NumeroFacturaCompra;
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

function Finish()
{
	var frmData 	= Get('frmData');
	var MainAction 	= Get('MainAction');
				
	if (frmData == undefined)
		return false;

	MainAction.value = 'Finish';	
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
    <input type="hidden" name="IdPlanillaCompra" id="IdPlanillaCompra" value="<?=$IdPlanillaCompra?>" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Datos de PlanillasCompra - Agregar Unidades</span></td>
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
                        <td width="103">&nbsp;</td>
                        <td width="730">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="10">&nbsp;</td>
                        <td height="25"><strong>N&uacute;mero Carga: </strong></td>
                        <td height="25"><?=$oPlanillaCompra->IdPlanillaCompra?></td>
                    </tr>
                    <tr>
                        <td width="10">&nbsp;</td>
                        <td height="25"><strong>Fecha Carga: </strong></td>
                        <td height="25"><?=CambiarFecha($oPlanillaCompra->FechaCarga)?></td>
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
                                    <td><div id="margen" align="left">Nro. Factura:</div></td>
                                    <td><div id="margen" align="left">Importe Compra Neto:</div></td>
                                    <td><div id="margen" align="left">&nbsp;</div></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>
                                        <div align="left">
                                            <input type="text" name="NumeroVin" id="NumeroVin" class="camporFormularioMedianoI" maxlength="128" value="<?=$NumeroVin?>" onkeyup="javascript: StrToUpper(this.id);" autocomplete="off" />
                                            <script language="">
                                            SUGGESTRequest('Unidades', 'GetAll', 'NumeroVin', 'FilterNumeroVin', 'IdUnidad', 'NumeroVin', 'FilterNumeroVin', null);
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
                                            <input type="text" name="NumeroFacturaCompra" id="NumeroFacturaCompra" class="camporFormularioMedianoDisabled" maxlength="128" value="<?=$NumeroFacturaCompra?>" readonly="readonly" />
                                        </div>
                                    </td>
                                    <td>
                                        <div align="left">
                                            <input type="text" name="ImporteCompraNeto" id="ImporteCompraNeto" class="camporFormularioMedianoI" maxlength="128" value="<?=$ImporteCompraNeto?>" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" />
                                        </div>
                                    </td>
                                    <td><input type="button" name="btnAgregar" value="Agregar" class="botonBasico" onClick="javascript: Add();" /></td>
                                </tr>
                                
                            <?php if (($err != 0)) { ?>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td align="left"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese nro. vin</li><?php } if ($err & 2) { ?>
                                    <li style="color:#FF0000;">La unidad ya existe cargada</li><?php } ?></td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td align="left"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese importe de compra</li><?php } ?></td>
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
                        <td width="100" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Nro. Interno</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>N&uacute;mero Vin</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>N&uacute;mero Factura</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Importe Compra Neto</strong></div></td>
                        <td width="90" class="bordeGrisTitulo"><div align="center"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php foreach ($arrData as $oUnidad) { ?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="100" height="25"><div id="margen"><?=$oUnidad->IdUnidad?></div></td>
                        <td width="130" height="25"><div id="margen"><?=$oUnidad->NumeroVin?></div></td>
                        <td width="316" height="25"><div id="margen"><?=$oUnidad->NumeroFacturaCompra?></div></td>
                        <td width="317" height="25"><div id="margen" align="center"><?='$ ' . number_format($oUnidad->ImporteCompraNeto, 2)?></div></td>
                        <td width="90" height="25">
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
                            	<input type="button" name="btnSiguiente" class="botonBasico" id="btnSiguiente" value="Finzalizar" onClick="javascript: Finish();" />
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
                        <td><div align="center"><strong>La carga no posee unidades cargadas.</strong></div></td>
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