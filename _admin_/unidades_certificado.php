<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_UNID_UPDATE))
	Session::NoPerm();

/* obtenemos datos enviados */
$PageGaleria		= intval($_REQUEST['PageGaleria']);
$PageGaleriaSize 	= intval($_REQUEST['PageGaleriaSize']);
$Archivo 			= $_FILES['Archivo'];
$Action 			= strval($_REQUEST['MainAction']);
$Id					= intval($_REQUEST['Id']);
$IdUnidad			= intval($_REQUEST['IdUnidad']);
$Nombre				= strval($_REQUEST['Nombre']);

/* declaramos e instanciamos variables necesarias */
$err					= 0;
$errUpload				= 0;
$arrData				= array();
$oImage 				= new Image();
$oUnidades				= new Unidades();
$oUnidadesArchivos 		= new UnidadesArchivos();
$oUnidadArchivo 		= new UnidadArchivo();
$oPage 					= new Page($Page, $PageGaleriaSize);

/* arma cadena de parametros */
$strParams = (strlen($_SERVER['QUERY_STRING']) > 0) ? '?' . $_SERVER['QUERY_STRING'] : '';
$strParams.= '&PageGaleria=' 		. $PageGaleria;
$strParams.= '&PageGaleriaSize=' 	. $PageGaleriaSize;

if (!$oUnidad = $oUnidades->GetById($IdUnidad))
{
	header('Location: unidades.php' . $strParams);
	exit;
}

$oUnidadArchivo 	= $oUnidadesArchivos->GetCertificadoByUnidad($oUnidad);

/* ejecuta la accion solicitada... */
switch ($Action)
{
	case 'Add':
		
		/* obtiene el epigrafe a agregar */	
		$Nombre = $_REQUEST['Nombre'];
		
		$Certificado = 1;
		
		if ($Archivo['name'] == '')
			$err |= 8;
		
		/* si no hay errores... */
		if ($err == 0)
		{
			if ($Archivo['error'] != 1)
			{
				$oUpload = new Up
				(
					$Archivo['name'],
					$Archivo['tmp_name'],
					$Archivo['size'],
					$Archivo['type'],
					Unidad::PathFile
				);
		
				if ($oUpload->UploadFile())
				{
					$oUnidadArchivo = new UnidadArchivo();
					$oUnidadArchivo->IdUnidad		= $IdUnidad;
					$oUnidadArchivo->Archivo		= $oUpload->GetNombre();
					$oUnidadArchivo->Nombre			= $Nombre;
					$oUnidadArchivo->Certificado	= $Certificado;
					
					if ($oUnidadArchivo = $oUnidadesArchivos->Create($oUnidadArchivo))
					{
						$oUnidad->Certificado = 1;
						$oUnidades->Update($oUnidad);
					}

					/* determinamos el resultado de la operacion para informar al usuario */
					$Operation = Operaciones::Create;
					$Status = (($oUnidadArchivo) ? true : false);
				}
			}
		}
		
		break;
		
	case 'Delete':
	
		if ($oUnidadArchivo)
		{		
			$Delete = true;
		
			if (!($oUnidadesArchivos->Delete($oUnidadArchivo->IdUnidadArchivo)))
				$Delete = false;
			
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
	
		header('Location: unidades.php' . $strParams);
		exit;
		
		break;
		
	default:
		break;
}

$oUnidadArchivo 	= $oUnidadesArchivos->GetCertificadoByUnidad($oUnidad);

$oUnidad = $oUnidades->GetById($IdUnidad);

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

	MainAction.value = 'Delete';
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
    <input type="hidden" name="IdUnidad" id="IdUnidad" value="<?=$IdUnidad?>" />

	<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
					<tr>
						<td width="20" height="40" class="TituloRubro">&nbsp;</td>
						<td height="40"><span class="tituloPagina">Factura de la unidad</span></td>
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
                  <td><div align="left"><strong>Carga de Factura:</strong></div></td>
                  <td><div align="right"><a href="unidades.php<?=$strParams?>" class="linkMenu">[Volver a unidades]</a></div></td>
                </tr>
              </table></td>
		</tr>
		<tr>
		  <td valign="top">&nbsp;</td>
	  	</tr>
		<?php if (!$oUnidadArchivo) { ?>
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
                                        <td class="tituloMenu"><div align="right">Factura:</div></td>
                                        <td><input type="file" name="Archivo" id="Archivo" class="camporFormularioSimple" />
                                          <span style="color:#FF0000;">&nbsp;(*)</span></td>
                                        <td width="20">&nbsp;</td>
                                        <td class="tituloMenu"><div align="right">Nombre:</div></td>
                                        <td><input type="text" name="Nombre" id="Nombre" class="camporFormularioSimple" /></td>
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
                                        <td><li style="color:#FF0000">Seleccione un archivo</li></td>
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
		<?php
		}
		else
		{
		?>
		<tr>
			<td align="center">

				

         		<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
					<tr>
					  	<td>&nbsp;</td>
				  	</tr>
            		<tr>
						<td align="center" valign="top" nowrap="nowrap" height="100" class="bordeGris">
							<table width="100%" height="100%" align="center" border="0">
						  		<tr>
                                	<td>&nbsp;</td>
                                </tr>
                                <tr valign="top">
									<td align="center"><a href="<?=Unidad::PathFile . $oUnidadArchivo->Archivo?>" target="_blank">Descargar Certificado</a></td>
							  	</tr>
								<tr valign="top">
								  <td align="center"><label id="lblEpigrafe_<?=$oUnidadArchivo->IdUnidadArchivo?>"><?=$oUnidadArchivo->Nombre?></label></td>
							  	</tr>
								<tr align="center" valign="top" height="80">
									<td>
						 				<table>
											<tr>												
												<td>
													<table>
														<tr>
															<td><input type="button" name="BorrarMultiple"  class="botonBasico" onclick="javascript: DeleteMultiple();" value="Borrar Certificado" /></td>
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