<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_IPRE_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$filter	= ReceiveArray($_REQUEST['filter']);
$Action	= strval($_REQUEST['MainAction']);
$Submit	= (isset($_REQUEST['Submitted']));

/* armamos el filtro en caso de que no este armado */
if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter	= array();
	$filter['NumeroVin'] = trim($_REQUEST['FilterNumeroVin']);
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

/* asigamos filtro de estado */
$filter['IdEstado'] = EstadoUnidad::Reservado;
$filter['FechaRetiroNull'] = '1';

/* declaracion de variables */
$oUnidades				= new Unidades();
$oModelos				= new Modelos();
$oColores				= new Colores();
$oMinutas				= new Minutas();
$oClientes				= new Clientes();
$oOrdenesSalida 		= new OrdenesSalida();
$oPlanillasRecepcion 	= new PlanillasRecepcion();

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?filter=' . SendArray($filter);

$arrData = $oUnidades->GetAll($filter);

/* si el formulario fue enviado... */
if ($Submit)
{
	switch ($Action)
	{
		case 'UpdateAll':

			foreach ($arrData as $oUnidad)
			{
				$FechaRetiro = $_REQUEST['FechaRetiro_' . $oUnidad->IdUnidad];
				
				if ($FechaRetiro != '')
				{
					$oUnidad->FechaRetiro = $FechaRetiro;
					
					$oUnidades->Update($oUnidad);
				}
			}
			
			$arrData = $oUnidades->GetAll($filter);

			break;
	}
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

function Filtrar()
{
	var frmData = Get('frmData');
	
	if (frmData == undefined)
		return false;

	frmData.submit();
}

function ClearFilter()
{	
	window.location.href = 'planillaspreentrega.php';
}								

function ShowFilter()
{
	HideSection('ShownFilter');
	ShowSection('HiddenFilter');
	ShowSection('FilterMain');
}

function HideFilter()
{
	ShowSection('ShownFilter');
	HideSection('HiddenFilter');
	HideSection('FilterMain');
}

function SetNumeroVin(IdUnidad, NumeroVin)
{	
	Get('FilterNumeroVin').value = NumeroVin;
}

function UpdateAll()
{
	var frmData = Get('frmData');
	var Action = Get('MainAction');
	
	if (frmData == undefined)
		return false;
		
	Action.value = 'UpdateAll';
	frmData.submit();
	return true;
}

</script>

</head>
<body>

<form name="frmData" id="frmData" method="post" onSubmit="Filtrar();">
    <input type="hidden" name="filtroActivo" id="filtroActivo" value="1" />
    <input type="hidden" name="Submitted" id="Submitted" value="1" />
    <input type="hidden" name="MainAction" id="MainAction" value="" />

    <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Informes de Preentrega</span></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td height="30" valign="top">
                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="80%" height="40">
                            <table border="0" align="right" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="30"><div align="center"><img src="images/iconos/pdf.png" alt="Exportar PDF" border="0"></div></td>
                                    <td><a href="planillaspreentrega_pdf.php<?=$strParams?>">Generar PDF</a></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
            	</table>
          	</td>
        </tr>
        <tr>
            <td height="30" valign="top" align="center">
                <!-- Aca van los filtros -->				
                <div id="ShownFilter" class="bordeGrisFondo" style="<?=$filterMostrar;?> padding-top: 10px; padding-bottom: 10px;" align="center">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                        <tr>
                            <td>[+] <a href="#bottom" class="linkMenu" onClick="javascript: ShowFilter();"> <b> Mostrar b&uacute;squeda y filtros</b></a></td>
                            <td><div align="right"><a href="#" onClick="javascript: ClearFilter();" class="linkMenu">[Volver al listado general]</a></div></td>
                        </tr>
                    </table>
                </div>
                <div id="HiddenFilter" style="<?=$filterStyle;?> padding-top: 10px; padding-bottom: 10px;" class="bordeGrisFondo" align="center">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                        <tr>
                            <td>[-] <a href="#bottom" class="linkMenu" onClick="javascript: HideFilter();"> <b>Ocultar b&uacute;squeda y filtros</b></a></td>
                            <td><div align="right"><a href="#" onClick="javascript: ClearFilter();" class="linkMenu">[Volver al listado general]</a></div></td>
                        </tr>
                    </table>
                </div>
                <div align="center">
                    <table width="90%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td height="1"><div align="center"></div></td>
                        </tr>
                    </table>
                </div>
                <div id="FilterMain" style="<?=$filterStyle;?>" class="">
                <div id="Filter" align="center">		
                    <table border="0" class="bordeGrisFondo" align="center" cellpadding="2" cellspacing="2" width="100%">
                        <tr>
                            <td class="tituloMenu">
                                <table border="0" cellspacing="0" cellpadding="0">
                                    <tr>                              
                                        <td class="tituloMenu"><div align="right">N&uacute;mero Vin:</div></td>
                                        <td>
                                        	<input name="FilterNumeroVin" id="FilterNumeroVin" type="text" class="camporFormularioMediano" value="<?=$filter['NumeroVin']?>" maxlength="128" onkeyup="javascript: StrToUpper(this.id);">
                                            <script language="">
                                            SUGGESTRequest('Unidades', 'GetAll', 'FilterNumeroVin', 'SetNumeroVin', 'IdUnidad', 'NumeroVin', 'FilterNumeroVin', null);
                                            </script>
                                      	</td>
                                        <td>&nbsp;</td>
                                        <td><input type="submit" name="button" id="button" class="botonBasico" value="Buscar"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
                </div>				
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
    
    <?php if ($arrData != NULL) { ?>
            
        <tr>
            <td>
                <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
                    <tr>
                        <td><div align="right"><input type="button" name="btnUpdateAll" value="Modifcar Fechas" class="botonBasico" onclick="javascript: UpdateAll();" /></div></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                <table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                    <tr class="bordeGrisFondo">
                        <td width="88" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Nro. Interno</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Modelo</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Color</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Llave</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>N&uacute;mero Vin</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cliente</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Fecha Retiro</strong></div></td>
                    </tr>
          
                <?php foreach ($arrData as $oUnidad) { ?>
                    <?php $oModelo = $oModelos->GetById($oUnidad->IdModelo); ?>
                    <?php $oColor = $oColores->GetById($oUnidad->IdColor); ?>
                    <?php $oMinuta = $oMinutas->GetByUnidad($oUnidad); ?>
                    <?php $oCliente = $oClientes->GetById($oMinuta->IdCliente); ?>
					<?php $oPlanillaRecepcion = $oPlanillasRecepcion->GetById($oUnidad->IdPlanillaRecepcion); ?>
					<?php $CodigoLlaves = ($oPlanillaRecepcion->IdEstado == RecepcionEstados::Aprobado) ? $oUnidad->CodigoLlaves : ''; ?>
                    <?php 
						if ($oUnidad->FechaRetiro == '')
						{
							/* determinamos como fecha de compra a la fecha de pasado mañana */
							$FechaRetiro = date("Y-m-d", strtotime(date("Y-m-d") . " + 2 days"));
							$FechaRetiro = CambiarFecha($FechaRetiro);
						}
						else
						{
							$FechaRetiro = CambiarFecha($oUnidad->FechaRetiro);
						}

						/* si la unidad posee una orden de salida asociada, entonces la salteamos */
						if ($oOrdenesSalida->GetByIdMinuta($oMinuta->IdMinuta))
							continue;
					?>
          
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="88" height="25"><div id="margen" align="center"><?=$oUnidad->IdUnidad?></div></td>
                        <td width="168" height="25"><div id="margen"><?=$oModelo->DenominacionComercial?></div></td>
                        <td width="142" height="25"><div id="margen"><?=$oColor->Nombre?></div></td>
                        <td width="79" height="25"><div id="margen"><?=$oCodigoLlaves?></div></td>
                        <td width="84" height="25"><div id="margen"><?=$oUnidad->NumeroVin?></div></td>
                        <td width="125" height="25"><div id="margen"><?=$oCliente->RazonSocial?></div></td>
                        <td width="145" height="25">
                        	<div id="margen" align="center">
                                <input name="FechaRetiro_<?=$oUnidad->IdUnidad?>" type="text" class="camporFormularioChico" id="FechaRetiro_<?=$oUnidad->IdUnidad?>" value="<?=$FechaRetiro?>" size="12" maxlength="12" />
                                <script language="javascript">
                                new tcal({'formname': 'frmData', 'controlname': 'FechaRetiro_<?=$oUnidad->IdUnidad?>'});
                                </script>
                         	</div>
                     	</td>
                    </tr>
                    <tr>
                        <td colspan="8">
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
                <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
                    <tr>
                        <td><div align="right"><input type="button" name="btnUpdateAll" value="Modifcar Fechas" class="botonBasico" onclick="javascript: UpdateAll();" /></div></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
        	<td>&nbsp;</td>
        </tr>
    
    <?php } else { ?>  
    
        <tr>
            <td>
                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
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