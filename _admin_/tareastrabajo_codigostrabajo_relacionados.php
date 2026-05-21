<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_TARE_UPDATE))
	Session::NoPerm();

/* obtenemos datos enviados */
$IdTareaTrabajo			= intval($_REQUEST['IdTareaTrabajo']);
$Action 				= strval($_REQUEST['MainAction']);
$codigosSeleccionados	= strval($_REQUEST['codigosSeleccionados']);
$Id						= intval($_REQUEST['Id']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaramos e instanciamos variables necesarias */
$err 							= 0;
$oCodigosTrabajo 				= new CodigosTrabajo();
$oTareasTrabajo		 			= new TareasTrabajo();
$oTareasTrabajoCodigosTrabajo	= new TareasTrabajoCodigosTrabajo();
$Ivas							= new Ivas();
$oModelos			 			= new Modelos();

/* definimos cadena a mandar por get */
$strParams = (strlen($_SERVER['QUERY_STRING']) > 0) ? '?' . $_SERVER['QUERY_STRING'] : '';

/* obtiene los datos del curso */
if (!$oTareaTrabajo = $oTareasTrabajo->GetById($IdTareaTrabajo))
{
	header('Location: tareastrabajo.php' . $strParams);
	exit;
}

if ($oTareaTrabajo->Modelo)
{
	$oModelo = $oModelos->GetByCodigoComercial($oTareaTrabajo->Modelo);
}

/* si el formulario fue enviado */
if ($Submit)
{
	switch ($Action)
	{
		case 'Add':
			$Ids = split(',', $codigosSeleccionados);
			$oTareasTrabajoCodigosTrabajo->Begin();
			try
			{
				foreach ($Ids as $Id)
				{
					$oCodigoTrabajo 							= $oCodigosTrabajo->GetById($Id);
					$update = true;
					if (!$oTareaTrabajoCodigoTrabajo = $oTareasTrabajoCodigosTrabajo->GetById($IdTareaTrabajo, $Id))
					{
						$oTareaTrabajoCodigoTrabajo				= new TareaTrabajoCodigoTrabajo();
						$update = false;
					}
					
					$oTareaTrabajoCodigoTrabajo->IdTareaTrabajo = $oTareaTrabajo->IdTareaTrabajo;
					$oTareaTrabajoCodigoTrabajo->IdCodigoTrabajo = $oCodigoTrabajo->IdCodigoTrabajo;
					if (!$update)
						$oTareasTrabajoCodigosTrabajo->Create($oTareaTrabajoCodigoTrabajo);
					else
						$oTareasTrabajoCodigosTrabajo->Update($oTareaTrabajoCodigoTrabajo);
				}
				$oTareasTrabajoCodigosTrabajo->Commit();
			}
			catch (Exception $ex)
			{
				$oTareasTrabajoCodigosTrabajo->Rollback();
			}
		
			break;
			
		case 'Delete':
			$oTareasTrabajoCodigosTrabajo->Delete($oTareaTrabajo->IdTareaTrabajo, $Id);
			break;
			
		default:
			break;
	}
}
$arrIvas 				= $Ivas->GetAll();
$arrCodigosTrabajo 		= $oTareasTrabajoCodigosTrabajo->GetAllByTareaTrabajo($oTareaTrabajo);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script type="text/javascript" src="../library/html_editor/tiny_mce.js"></script>
<script type="text/javascript" src="../library/html_editor/editor_html.js"></script>

<script language="javascript">

function validar(busqueda)
{
	if (($j('#Codigo').val() == '' && $j('#Descripcion').val() == '')) {
		$j('.error_3').show();
		return false;
	} else {
		$j('.error_3').hide();
		if ($j('#codigosSeleccionados').val() == '' && !busqueda) {
			$j('.error_4').show();
			return false;
		} else {
			$j('.error_4').hide();
		}
	}

	return true;
}

function realizarBusqueda(page) {
	if (validar(true)) {
		var urlAjax = 'codigostrabajo_buscar_popup.php?FilterCodigo=' + $j('#Codigo').val() + '&FilterDescripcion=' + $j('#Descripcion').val() + '&Page=' + page;
		$j('body').addClass("loading"); 
		$j.ajax(urlAjax,{
			success: function(data) {
				$j('#modal-popup').html(data);	
				$j('body').removeClass("loading"); 
				$j('.agregar').click(function() {
					var IdCodigoTrabajo = $j(this).attr('id').split('_')[1];							
					AgregarCodigoTrabajo(IdCodigoTrabajo);
				});						
				
				$j('#modal-popup').dialog({
					closeOnEscape: true,
					title: 'Codigos de Trabajo encontrados',
					width: 700,
					height: 550,
					modal: true
				});
			}
		});				
	}
}

function SetPage(page)
{
	realizarBusqueda(page);
}

function FilterCodigoTrabajo(IdCodigoTrabajo, CodigoTrabajo) {
	AgregarCodigoTrabajo(IdCodigoTrabajo);
	
	$j('#modal-popup').dialog('close');
}

function AgregarCodigoTrabajo(IdCodigoTrabajo) {
	if (IdCodigoTrabajo != '' && IdCodigoTrabajo != null && IdCodigoTrabajo != undefined) {
		if ($j('#id_' + IdCodigoTrabajo).length == 0) {
			var codigosSeleccionados = $j('#codigosSeleccionados').val();
			if (codigosSeleccionados != '')
				codigosSeleccionados += ',';
			codigosSeleccionados += IdCodigoTrabajo;
			$j('#codigosSeleccionados').val(codigosSeleccionados);
			
			$j('#modal-popup').dialog('close');
			oCodigoTrabajo = GetCodigoTrabajo(IdCodigoTrabajo);
			
			if (oCodigoTrabajo.UnidadVenta)
				cantidad *= oCodigoTrabajo.UnidadVenta;

				var row = "";
			row += "<tr id=\"row_" + oCodigoTrabajo.IdCodigoTrabajo + "\" onMouseOver=\"bgColor='#f3f3f3'\" onMouseOut=\"bgColor=''\">";
			row += "	<td height=\"25\">";
			row += "		<div id=\"margen\">" + oCodigoTrabajo.GLC_LbrOp + "</div>";
			row += "		<input type=\"hidden\" id=\"id_" + oCodigoTrabajo.IdCodigoTrabajo + "\" name=\"id_" + oCodigoTrabajo.IdCodigoTrabajo + "\" value=\"" + oCodigoTrabajo.IdCodigoTrabajo + "\" />";
			row += "	</td>";
			row += "	<td height=\"25\">" + oCodigoTrabajo.Descripcion + "</td>";
			row += "</tr>";
			$j('#CodigoTrabajos').append(row);
		} else {				
			$j('#modal-popup').dialog('close');
		}
	}
}

function Add()
{
	if (validar(false)) {
		var frmData 	= Get('frmData');
		var MainAction 	= Get('MainAction');
						
		if (frmData == undefined)
			return false;

		MainAction.value = 'Add';
		frmData.submit();
		return true;
	}
	return false;
}

function Delete(Id)
{
	var frmData 	= Get('frmData');
	var MainAction 	= Get('MainAction');
	var IdField 	= Get('Id');
					
	if (frmData == undefined)
		return false;

	MainAction.value = 'Delete';
	IdField.value = Id;
	frmData.submit();
	return true;
}

function ShowAddRelacion()
{
	HideSection('ShownAddRelacion');
	ShowSection('AddRelacionMain');
}

function HideAddRelacion()
{
	ShowSection('ShownAddRelacion');
	HideSection('AddRelacionMain');
}

$j(document).ready(function() {
	$j('#buscar-codigos').click(function(e) {
		e.preventDefault();
		
		RealizarBusquedaPopup('codigostrabajo_buscar_popup.php', {}, 'C&oacute;digos de Trabajo');
	});
});

</script>
</head>
<body>

	<form name="frmData" id="frmData" method="post">
		<input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
		<input type="hidden" name="MainAction" id="MainAction" />
		<input type="hidden" name="IdTareaTrabajo" id="IdTareaTrabajo" value="<?=$IdTareaTrabajo?>" />
		<input type="hidden" name="Id" id="Id" />
		<input type="hidden" name="Submitted" id="Submitted" value="1" />
		<input type="hidden" name="codigosSeleccionados" id="codigosSeleccionados" value="" />
		
		<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td width="20" height="40" class="TituloRubro">&nbsp;</td>
							<td height="40"><span class="tituloPagina">Relacionar Codigo de Trabajo</span></td>
						</tr>
					</table>			
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>							
							<td>
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td><strong>Relaci&oacute;n de codigos de trabajo para la tarea:</strong></td>
									</tr>
									<tr>
										<td>
											<span class="tituloCategoriaMenu">
												<strong>
													<?= $oModelo ? $oModelo->DenominacionComercial . ' - ' : ''?><?=$oTareaTrabajo->Titulo?>
												</strong>
											</span>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<div align="center">
						<div id="ShownAddRelacion" class="bordeGrisFondo" style="padding: 5px"><font>[+]<a href="#bottom" class = "linkMenu" onclick="javascript: ShowAddRelacion();"><b> A&ntilde;adir Relaci&oacute;n</b></a></font></div>
						<div id="AddRelacionMain" style="display: none;">
							<div id="AddRelacion">	
								<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>
											<div class="titulo-seccion">Seleccione los codigos:</div>
											<div style="clear:both; height: 0px">&nbsp;</div>
											<table width="100%" border="0" cellpadding="5" cellspacing="0">
												<tr>
													<td class="bordeGris">
														<table width="100%" border="0" cellpadding="0" cellspacing="0">		<tr>
																<td>&nbsp;</td>
															</tr>													
															<tr>
																<td>
																	<table cellpadding="0" cellspacing="0" border="0">
																		<tr>																	
																			<td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
																			<td><a id="buscar-codigos" href="#">Agregar C&oacute;digo</a></td>
																		</tr>
																	</table>
																</td>
																
															</tr>	
															<tr>
																<td>&nbsp;</td>
															</tr>
															<tr>
																<td align="center">
																	<table id="CodigoTrabajos" class="bordeGris" border="0" align="center" cellpadding="0" cellspacing="0" width="715">
																		<tr class="bordeGrisFondo">
																			<td width="150" height="25" class="bordeGrisTitulo"><div id="margen"><strong>C&oacute;digo</strong></div></td>
																			<td width="150" height="25" class="bordeGrisTitulo"><strong>Descripci&oacute;n</strong></td>					
																			
																		</tr>													
																	</table>
																</td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td><div align="center"></div></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td>
											<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
												<tr>
													<td height="30">
														<div align="center">
															<input type="button" name="btnAceptar" class="botonBasico" value="Aceptar" onclick="javascript: Add();" />
															<input type="button" name="btnCancelar" class="botonBasico" onclick="javascript: HideAddRelacion();" value="Cancelar" />
														</div>
														<div align="center"></div>
													</td>
												</tr>
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
			if ($arrCodigosTrabajo != NULL)
			{ 
			?>			
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr height="20">
				<td><span class="tituloCategoriaMenu">Codigos relacionados:</span></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
						<tr class="bordeGrisFondo">							
							<td width="150" height="25"><div id="margen"><strong>C&oacute;digo</strong></div></td>
							<td width="300" height="25"><div id="margen"><strong>Descirpci&oacute;n</strong></div></td>                       
							<td width="100"><div align="center"><strong>Acciones</strong></div></td>
						</tr>
						<?php 
						foreach ($arrCodigosTrabajo as $oRelacion) 
						{ 
							$oCodigoTrabajo = $oCodigosTrabajo->GetById($oRelacion->IdCodigoTrabajo);
						?>
						<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
							<td width="150"><div id="margen"><?=$oCodigoTrabajo->GLC_LbrOp?></div></td>
							<td><div id="margen"><?=$oCodigoTrabajo->Descripcion?></div></td>              
							<td>
								<div align="center">
									<a href="#bottom" onClick="javascript: Delete(<?=$oRelacion->IdCodigoTrabajo?>)">
										<img src="images/iconos/del.gif" alt="Eliminar" border="0" />
									</a>
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="4">
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
			<?php 
			} 
			else 
			{ 
			?>
			<tr height="20">
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td><div align="center"> <img src="images/iconos/alerta.gif" border="0"> </div></td>
						</tr>
						<tr>
							<td><div align="center"><strong>No hay ninguna relaci&oacute;n establecida.</strong></div></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>		  
			<?php
			}
			?>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<table width="100%" border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30">
								<div align="right">
									<label>
										<input name="button" type="button" class="botonBasico" id="button" onclick="javascript: window.location.href = 'tareastrabajo.php<?=$strParams?>';" value="Volver a tareas de trabajo" />
									</label>
								</div>
							</td>
							<td width="10" height="30">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</form>
	<div id="modal-popup" style="display:none">
	</div>
</body>
</html>