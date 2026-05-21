<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

Session::ForceLogin();

if (!Session::CheckPerm(PERM_TARE_UPDATE))
	Session::NoPerm();

$Submit					= (isset($_REQUEST['Submitted']));

$err				= 0;
$oTareasTrabajo		= new TareasTrabajo();
$oModelos			= new Modelos();
$oTiposCosto		= new TiposCosto();
$oCostosManoObra	= new CostosManoObra();
$oCodigosTrabajo	= new CodigosTrabajo();
$oModelosPV			= new ModelosPV();
$oServices			= new Services();

$strParams = '?' . $_SERVER['QUERY_STRING'];

$filter = array();
$filter['Disponible'] = '1';

$arrModelos = $oModelosPV->GetAll($filter);
$arrServices = $oServices->GetAll($filter);

if ($Submit)
{
	/* si no hay errores... */
	if ($err == 0)
	{
		foreach ($arrModelos as $oModeloPV)
		{
			foreach ($arrServices as $oService)
			{
				$create = false;
				if (!$oTareaTrabajo = $oTareasTrabajo->GetByIdModeloPVIdService($oModeloPV->IdModeloPV, $oService->IdService))
				{
					$create = true;
					$oTareaTrabajo = new TareaTrabajo();
				}
				
				$Importe = $_REQUEST['Precio_' . $oModeloPV->IdModeloPV . '_' . $oService->IdService];
				$Importe	= str_replace(",", ".", $Importe);
				$oTareaTrabajo->IdModeloPV		= $oModeloPV->IdModeloPV;
				$oTareaTrabajo->AnioDesde		= 0;
				$oTareaTrabajo->AnioHasta		= 0;
				$oTareaTrabajo->Titulo			= $oService->Nombre;
				$oTareaTrabajo->Descripcion		= $oService->Nombre;
				$oTareaTrabajo->HorasEstimadas	= 1;
				$oTareaTrabajo->Importe			= $Importe;
				$oTareaTrabajo->IdTipoCosto		= TipoCosto::CostoFijo;
				$oTareaTrabajo->IdService		= $oService->IdService;
				
				if ($create)
					$oTareasTrabajo->Create($oTareaTrabajo);
				else
					$oTareasTrabajo->Update($oTareaTrabajo);
			}
		}
		

		header("Location: tareastrabajo.php" . $strParams);
		exit();
	}
}

IncludeSUGGEST();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

	

</script>

</head>
<body>

<table width="98%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de tareas de taller - Cargar Service</span></td>
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
					<input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
                 	
					<table width="90%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>&nbsp;</td>
									</tr>
                                    <tr>
                                        <td>
                                            <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td valign="top">
                                                        <table width="100%" class="bordeGris" border="0" align="center" cellpadding="0" cellspacing="0">                                                           
                                                            <tr class="bordeGrisFondo">
																<td height="25"><div id="margen"><strong>Modelo</strong></div></td>
																<?php
																foreach ($arrServices as $oService)
																{
																?>
																<td height="25"><div align="center" id="margen"><strong><?= $oService->Nombre ?></strong></div></td>
																<?php
																}
																?>
                                                            </tr>
															<?php
															foreach ($arrModelos as $oModeloPV)
															{
															?>
                                                            <tr  onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                                                                <td width="450" height="20"><div id="margen"><strong><?= $oModeloPV->Modelo ?></strong></div></td>
															<?php
																foreach ($arrServices as $oService)
																{
																	$oTareaTrabajo = $oTareasTrabajo->GetByIdModeloPVIdService($oModeloPV->IdModeloPV, $oService->IdService);
																	if (!$oTareaTrabajo)
																		$Precio = 0;
																	else
																		$Precio = $oTareaTrabajo->Importe;
															?>
																<td><div id="margen"><input class="camporFormularioChico" type="text" id="Precio_<?= $oModeloPV->IdModeloPV ?>_<?= $oService->IdService ?>" name="Precio_<?= $oModeloPV->IdModeloPV ?>_<?= $oService->IdService ?>" value="<?= $Precio ?>" style="width: 45px" /></div></td>
															<?php
																}
															?>
                                                            </tr>
															<tr>
																<td colspan="11">
																	<div align="center">
																		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
																			<tr>
																				<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
																			</tr>
																		</table>
																	</div>
																</td>
															</tr>
															<?php
															}
															?>
                                                            
                                                        </table>
                                                    </td>
                                                </tr>
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
							<td><div align="center"></div></td>
						</tr>
					</table>
					<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'tareastrabajo.php<?=$strParams?>';" value="Cancelar" />
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


<div id="modal-popup" style="display:none">
</div>
<div class="modal"><!-- Place at bottom of page --></div>
</body>
</html>