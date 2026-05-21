<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_ORDE_CREATE))
	Session::NoPerm();

/* obtenemos datos enviados */
$PageGaleria		= intval($_REQUEST['PageGaleria']);
$PageGaleriaSize 	= intval($_REQUEST['PageGaleriaSize']);
$Imagen 			= $_FILES['Imagen'];
$Action 			= strval($_REQUEST['MainAction']);
$Id					= intval($_REQUEST['Id']);
$IdOrdenTrabajo		= intval($_REQUEST['IdOrdenTrabajo']);
$url 				= strval($_REQUEST['url']);

/* declaramos e instanciamos variables necesarias */
$err					= 0;
$errUpload				= 0;
$arrData				= array();
$oImage 				= new Image();
$oOrdenesTrabajo		= new OrdenesTrabajo();
$oOrdenTrabajoImagen 	= new OrdenTrabajoImagen();
$oOrdenTrabajoImagenes 	= new OrdenTrabajoImagenes();
$oTallerUnidades		= new TallerUnidades();
$oPage 					= new Page($Page, $PageGaleriaSize);

/* arma cadena de parametros */
$strParams = (strlen($_SERVER['QUERY_STRING']) > 0) ? '?' . $_SERVER['QUERY_STRING'] : '';
$strParams.= '&PageGaleria=' 		. $PageGaleria;
$strParams.= '&PageGaleriaSize=' 	. $PageGaleriaSize;

if (!$oOrdenTrabajo = $oOrdenesTrabajo->GetById($IdOrdenTrabajo))
{
	header('Location: ordenestrabajo.php' . $strParams);
	exit;
}

$Paginado 	= Pageable::PrintPaginator($oPage, $oOrdenTrabajoImagenes->GetCountRows($oOrdenTrabajo->IdOrdenTrabajo));
$arrData 	= $oOrdenTrabajoImagenes->GetAllByOrdenTrabajo($oOrdenTrabajo, $oPage);

/* ejecuta la accion solicitada... */
switch ($Action)
{
	case 'Add':
		
		/* obtiene el epigrafe a agregar */	
		$Epigrafe = $_REQUEST['Epigrafe'];
		if (!$Orden = intval($_REQUEST['Orden']))
			$Orden = 0;
		
		
		
		if ($Imagen['name'] == '')
			$err |= 8;
		
		/* si no hay errores... */
		if ($err == 0)
		{
			if ($Imagen['error'] != 1)
			{
				$oUpload = new Image
				(
					$Imagen['name'],
					$Imagen['tmp_name'],
					$Imagen['size'],
					$Imagen['type'],
					array(OrdenTrabajo::PathImageBig, OrdenTrabajo::PathImageThumb),
					array('jpg', 'jpeg', 'gif', 'png'),
					array(710, 120),
					array(710, 120),
					array('Resize', 'Adaptive'),
					100
				);
		
				if ($oUpload->UploadImage())
				{
					$oOrdenTrabajoImagen->IdOrdenTrabajo	= $IdOrdenTrabajo;
					$oOrdenTrabajoImagen->Imagen 			= $oUpload->GetNombre();
					$oOrdenTrabajoImagen->Epigrafe 			= $Epigrafe;
					$oOrdenTrabajoImagen->Orden				= $Orden;
					
					$oOrdenTrabajoImagen = $oOrdenTrabajoImagenes->Create($oOrdenTrabajoImagen);

					/* determinamos el resultado de la operacion para informar al usuario */
					$Operation = Operaciones::Create;
					$Status = (($oOrdenTrabajoImagen) ? true : false);
				}
			}
		}
		
		break;
		
	case 'DeleteMultiple':
	
		if (sizeof($arrData) != 0)
		{		
			$Delete = true;
		
			foreach ($arrData as $oImagen)
			{				
				if (isset($_REQUEST['Eliminar_' . $oImagen->IdImagen]))
				{
					if (!($oOrdenTrabajoImagenes->Delete($oImagen->IdImagen)))
						$Delete = false;
				}
			}
			
			$Operation = Operaciones::Delete;
			$Status = (($Delete) ? true : false);
		}
		
		break;
		
	case 'Edit':
		if (sizeof($arrData) != 0)
		{
			foreach ($arrData as $oImagen)
			{				
				$Orden = intval($_REQUEST['Orden_' . $oImagen->IdImagen]);
				$oImagen->Orden = $Orden;
				$oOrdenTrabajoImagenes->Update($oImagen);						
			}
		}
		break;
		
	case 'Back':
	
		header('Location: ordenestrabajo.php' . $strParams);
		exit;
		
		break;
		
	default:
		break;
}

$Paginado 	= Pageable::PrintPaginator($oPage, $oOrdenTrabajoImagenes->GetCountRows($oOrdenTrabajo->IdOrdenTrabajo), true);
$arrData 	= $oOrdenTrabajoImagenes->GetAllByOrdenTrabajo($oOrdenTrabajo, $oPage);

$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<script language="javascript">

function SetPage(Page)
{
	var frmData = Get('frmData');
	
	if (frmData == undefined)
		return false;

	frmData.PageGaleria.value = Page;		
	frmData.submit();
}

function SetPageSize(PageSize)
{
	var frmData = Get('frmData');
	
	if (frmData == undefined)
		return false;

	if (frmData.PageGaleriaSize == undefined)
		return false;

	frmData.PageGaleriaSize.value = PageSize;
	frmData.submit();
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

function RefreshEpigrafe(IdImagen)
{
	var lblEpigrafe = Get('lblEpigrafe_' + IdImagen);
	var Epigrafe	= Get('Epigrafe_' + IdImagen);

	if (lblEpigrafe == undefined)
		return false;

	if (Epigrafe == undefined)
		return false;

	lblEpigrafe.innerText = Epigrafe.value;
	
	return true;
}

function Edit(IdImagen)
{
	var frmData 	= Get('frmData');
	var Epigrafe 	= Get('Epigrafe_' + IdImagen);	
	var arr 		= new Array();
	var obj;
					
	if (frmData == undefined)
		return false;
	
	if (Epigrafe == undefined)
		return false;
				
	arr['IdImagen'] = IdImagen;
				
	obj = SendXMLRequest('OrdenTrabajoImagenes', 'GetById', null, arr);
	if (obj.Status.Id != 0)
	{
		alert(obj.Status.Description);
		return;
	}
	
	oImagen 			= obj.Response;
	oImagen.Epigrafe 	= (Epigrafe.value != '') ? Epigrafe.value : oImagen.Epigrafe;	

	arr['IdImagen'] = oImagen.IdImagen;
	arr['Epigrafe'] = oImagen.Epigrafe;	

	obj = SendXMLRequest('OrdenTrabajoImagenes', 'Update', null, arr);
	if (obj.Status.Id != 0)
	{
		alert(obj.Status.Description);
		ShowResult(false);
		return;
	}
			
	if (Epigrafe.value != '')
		RefreshEpigrafe(IdImagen);
			
	Epigrafe.value = '';
	
	ShowResult(true);	
	
	return true;	
}

function EditOrden()
{
	var frmData 	= Get('frmData');
	var MainAction 	= Get('MainAction');
					
	if (frmData == undefined)
		return false;
	MainAction.value = 'Edit';	
	frmData.submit();
}

function DeleteMultiple()
{
	var frmData 	= Get('frmData');
	var MainAction 	= Get('MainAction');
					
	if (frmData == undefined)
		return false;

	MainAction.value = 'DeleteMultiple';
	frmData.submit();
	return true;
}

function Back()
{
	var frmData 	= Get('frmData');
	var MainAction 	= Get('MainAction');
					
	if (frmData == undefined)
		return false;

	MainAction.value = 'Back';
	frmData.submit();
	return true;
}

function ShowAddImage()
{
	HideSection('ShownAddImage');
	ShowSection('AddImageMain');
}

function HideAddImage()
{
	ShowSection('ShownAddImage');
	HideSection('AddImageMain');
}			

function ShowResult(Status)
{
	if (Status)
	{
		Get('tdOperationImagenResult').innerHTML = '<div style="border: 2px solid #00CC00; padding: 5px; background:#CCFFCC;"><img src="images/iconos/check.gif" border="0" /><strong style="padding-left: 10px"><label id="lblOperationResult"> Registro modificado correctamente.</label></strong></div>'
	}
	else
	{
		Get('tdOperationImagenResult').innerHTML = '<div style="border: 2px solid #FF0000; padding: 5px; background:#FFCC99;"><img src="images/iconos/permisos.gif" border="0" /><strong style="padding-left: 10px"><label id="lblOperationResult"> Ocurrio un error al intentar modificar el registro.</label></strong></div>'
	}

	ShowSection('trOperationImagenResult');
	
	setTimeout("HideSection('trOperationImagenResult');", 5000);
	
	return true;
}

</script>

<?php include('include/head.inc.php'); ?>

</head>

<body>

<form name="frmData" id="frmData" method="post" enctype="multipart/form-data">
	<input type="hidden" name="PageGaleria" id="PageGaleria" value="<?=$PageGaleria?>" />
	<input type="hidden" name="PageGaleriaSize" id="PageGaleriaSize" value="<?=$PageGaleriaSize?>" />
	<input type="hidden" name="MainAction" id="MainAction" />
	<input type="hidden" name="Id" id="Id" />
    <input type="hidden" name="IdOrdenTrabajo" id="IdOrdenTrabajo" value="<?=$IdOrdenTrabajo?>" />

	<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
					<tr>
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
						<td height="40"><span class="tituloPagina">Galer&iacute;a de im&aacute;genes de la orden de trabajo</span></td>
					  </tr>
				</table>
            </td>
		</tr>

       	<?php //echo Operaciones::PrintResult($Operation, $Status); ?>

		<tr id="trOperationImagenResult" style="display:none;"><td id="tdOperationImagenResult"></td></tr>
        
		<tr>
		  	<td valign="top">&nbsp;</td>
	  	</tr>
		<tr>
			<td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td><div align="left"><strong>Carga de im&aacute;genes:</strong></div></td>
                  <td><div align="right"><a href="ordenestrabajo.php<?=$strParams?>" class="linkMenu">[Volver a ordenes de trabajo]</a></div></td>
                </tr>
              </table></td>
		</tr>
		<tr>
		  <td valign="top">&nbsp;</td>
	  	</tr>		
		<tr>
			<td>
				<div align="center">				
				<div id="AddImageMain">
				<div id="AddImage">				
					<table width="100%"  border="0" align="center" cellpadding="5" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td>
								<table border="0" cellspacing="0" cellpadding="0">
                                  	<tr>
                                        <td class="tituloMenu"><div align="right">Imagen:</div></td>
                                        <td><input type="file" name="Imagen" id="Imagen" class="camporFormularioSimple" />
                                          <span style="color:#FF0000;">&nbsp;(*)</span></td>
                                        <td width="20">&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Ep&iacute;grafe:</div></td>
                                        <td><input type="text" name="Epigrafe" id="Epigrafe" class="camporFormularioSimple" /></td>
                                        <td width="20">&nbsp;</td>
										<td class="tituloMenu"><div align="right">Orden:</div></td>
                                        <td><input type="text" name="Orden" id="Orden" class="camporFormularioSimple" style="width:50px" /></td>
                                        <td width="20">&nbsp;</td>
                                        <td><input type="button" name="btnAceptar" class="botonBasico" value="Aceptar" onclick="javascript: Add();" /></td>
                                  	</tr>
                                  	<tr>
                                        <td class="tituloMenu">&nbsp;</td>
                                        <td>La imagen no puede superar los 2MB.</td>
                                        <td width="20">&nbsp;</td>
                                        <td class="tituloMenu">&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td width="20">&nbsp;</td>
                                        <td>&nbsp;</td>
                                  	</tr>
                            <?php if ($err & 8) { ?>
                                  	<tr>
                                        <td class="tituloMenu">&nbsp;</td>
                                        <td><li style="color:#FF0000">Seleccione una imagen</li></td>
                                        <td width="20">&nbsp;</td>
                                        <td class="tituloMenu">&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td width="20">&nbsp;</td>
                                        <td>&nbsp;</td>
                                  	</tr>
							<?php } ?>
							<?php if ((is_object($oUpload)) && ($oUpload->strError != '')) { ?>
									<tr>
                                        <td class="tituloMenu">&nbsp;</td>
                                        <td><li style="color:#FF0000;"><?=$oUpload->strError?></li></td>
                                        <td width="20">&nbsp;</td>
                                        <td class="tituloMenu">&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td width="20">&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
							<?php } ?>
                         		</table>						  
                       		</td>
						</tr>
					</table>       			
				</div>
				</div>				
				</div>			
         	</td>
		</tr>
		<tr>
			<td align="center">

				<?php if (sizeof($arrData) != 0) { ?>

         		<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
					<tr>
					  	<td>&nbsp;</td>
				  	</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
                        <td align="right" colspan="3">
                            <div>
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td><div align="left"><strong>Im&aacute;genes disponibles en la orden de trabajo <?=CambiarFecha($oOrdenTrabajo->Fecha)?> - <?= $oTallerUnidad->Modelo ?>:</strong></div></td>
                                        <td><div align="right"><? print ($Paginado) ?></div></td>
                                    </tr>
                                </table>
                            </div>
                        </td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					
            		<tr>
						<?php $Cont = 1; ?>
						<?php foreach ($arrData as $oImagen) { ?>
						<td align="center" valign="top" nowrap="nowrap" height="100" class="bordeGris">
							<table width="100%" height="100%" align="center" border="0">
						  		<tr>
                                	<td>&nbsp;</td>
                                </tr>
                                <tr valign="top">
									<td align="center"><a href="<?=OrdenTrabajo::PathImageBig . $oImagen->Imagen?>" target="_blank"><img src="<?=OrdenTrabajo::PathImageThumb . $oImagen->Imagen?>" border="0" width="150" height="150" /></a></td>
							  	</tr>
								<tr valign="top">
								  <td align="center"><label id="lblEpigrafe_<?=$oImagen->IdImagen?>"><?=$oImagen->Epigrafe?></label></td>
							  	</tr>
								<tr align="center" valign="top" height="80">
									<td>
						 				<table>         
											<tr>
												<td><b>Orden:</b></td>
												<td>&nbsp;</td>
											</tr>
											<tr>
												<td>
													<input type="text" name="Orden_<?=$oImagen->IdImagen?>" id="Orden_<?=$oImagen->IdImagen?>"  class="camporFormularioSimple" style="width:120px" maxlength="255" value="<?=$oImagen->Orden?>" />
												</td>
											</tr>
                                            <tr>
												<td><b>Ep&iacute;grafe: </b></td>
                                                <td>&nbsp;</td>
                                            </tr>
											<tr>
												<td>
													<input type="text" name="Epigrafe_<?=$oImagen->IdImagen?>" id="Epigrafe_<?=$oImagen->IdImagen?>" class="camporFormularioSimple" style="width:120px" maxlength="255" />												</td>
												<td>
													<input type="button" class="botonBasico" onclick="javascript: Edit('<?=$oImagen->IdImagen?>');" value="Modificar" />												</td>
											</tr>
											<tr>												
												<td>
													<table>
														<tr>
															<td><b>Eliminar imagen</b></td>
															<td>
																<input type="checkbox" name="Eliminar_<?=$oImagen->IdImagen?>" id="Eliminar_<?=$oImagen->IdImagen?>" class="radio" value="1" />                    										</td>
														</tr>
													</table>
                                                </td>
                                                <td>&nbsp;</td>
											</tr>
										</table>								  
                                 	</td>
							  	</tr>
							</table>					  
                     	</td>
						
              	<?php if (($Cont % 3) == 0) { ?>
                
            		</tr>
					<tr>
						
				<?php } $Cont ++; } ?>
					</tr>
				
					<tr>
						<td>&nbsp;</td>
					</tr>	
					<tr>
						<td align="right" colspan="3"><div><? print ($Paginado) ?></div></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td height="30" valign="middle" class="bordeGrisFondo" colspan="3">
							<table width="95%" align="center" cellspacing="0" cellpadding="0" border="0">
								<tr>
									<td align="center">
										<input type="button" name="BorrarMultiple"  class="botonBasico" onclick="javascript: DeleteMultiple();" value="Borrar imágenes seleccionadas" />										
										<input type="button" name="BorrarMultiple"  class="botonBasico" onclick="javascript: EditOrden();" value="Actualizar Orden" />
										<!--<input type="button" name="ModificarMultiple"  class="botonBasico" onclick="javascript: EditMultiple();" value="Modificar Todos" />-->
                                        </td>
								</tr>
						  	</table>						
                      	</td>
					</tr>
          		</table>

        		<?php } else { ?>
<br>
        		<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
          			<tr>
            			<td>&nbsp;</td>
          			</tr>
          			<tr>
            			<td>
							<div align="center">
								<img src="images/iconos/alerta.gif" border="0" />				
                         	</div>						
                     	</td>
          			</tr>
          			<tr>
            			<td>
							<div align="center"><strong>El registro no posee im&aacute;genes asociadas.</strong></div>						
                      	</td>
          			</tr>
          			<tr>
            			<td>&nbsp;</td>
          			</tr>
       		  	</table>

		  <?php } ?>			
          
          	</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
	</table>
</form>

</body>
</html>