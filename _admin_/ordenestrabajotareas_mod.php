<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

Session::ForceLogin();

if (!Session::CheckPerm(PERM_TARE_UPDATE))
	Session::NoPerm();

$IdOrdenTrabajoTarea	= intval($_REQUEST['IdOrdenTrabajoTarea']);
$Titulo					= strval($_REQUEST['Titulo']);
$Tarea					= strval($_REQUEST['Tarea']);
$IdTipoVenta			= intval($_REQUEST['IdTipoVenta']);
$IdCategoria			= intval($_REQUEST['IdCategoria']);
$IdVendedor				= intval($_REQUEST['IdVendedor']);
$Importe				= floatval($_REQUEST['Importe']);
$HorasEstimadas			= floatval($_REQUEST['HorasEstimadas']);
$IdCodigoTrabajo		= intval($_REQUEST['IdCodigoTrabajo']);
$CodigoTrabajo			= strval($_REQUEST['CodigoTrabajo']);
$Descripcion			= strval($_REQUEST['Descripcion']);
$Submit					= (isset($_REQUEST['Submitted']));

$err					= 0;
$oOrdenesTrabajoTareas	= new OrdenesTrabajoTareas();
$oOrdenesTrabajo		= new OrdenesTrabajo();
$oCodigosTrabajo		= new CodigosTrabajo();
$oUsuarios				= new Usuarios();

$strParams = '?' . $_SERVER['QUERY_STRING'];

if (!$oOrdenTrabajoTarea	= $oOrdenesTrabajoTareas->GetByIdIncrement($IdOrdenTrabajoTarea))
{
	header('Location: ordenestrabajotareas.php' . $strParams);
	exit;
}

$oOrdenTrabajo = $oOrdenesTrabajo->GetById($oOrdenTrabajoTarea->IdOrdenTrabajo);

if ($Submit)
{
	if ($Importe == '' && $Importe != 0)
		$err |= 1;
	if ($Titulo == '')
		$err |= 2;
	/*if ($Tarea == '')
		$err |= 4;*/
	
	/* si no hay errores... */
	if ($err == 0)
	{
		$Importe	= str_replace(",", ".", $Importe);
			
		$oOrdenTrabajoTarea->HorasEstimadas	= $HorasEstimadas;
		$oOrdenTrabajoTarea->Titulo			= $Titulo;
		$oOrdenTrabajoTarea->Tarea			= $Tarea;
		$oOrdenTrabajoTarea->Importe		= $Importe;
		$oOrdenTrabajoTarea->IdTipoVenta	= $IdTipoVenta;
		$oOrdenTrabajoTarea->IdCategoria	= $IdCategoria;
		$oOrdenTrabajoTarea->IdCodigoTrabajo= $IdCodigoTrabajo;
		$oOrdenTrabajoTarea->Descripcion	= $Descripcion;
		$oOrdenTrabajoTarea->IdVendedor		= $IdVendedor;
		
		$oOrdenTrabajoTarea = $oOrdenesTrabajoTareas->Update($oOrdenTrabajoTarea);

		header('Location: ordenestrabajotareas.php' . $strParams);
		exit();
	}
}
else
{
	if ($oTareaTrabajo->Modelo)
	{		
		$oModelo = $oModelos->GetByCodigoComercial($oTareaTrabajo->Modelo);	
		$Modelo	= $oModelo->DenominacionComercial;
		$IdModelo = $oModelo->IdModelo;
	}
	
	$IdCodigoTrabajo		= $oOrdenTrabajoTarea->IdCodigoTrabajo;
		
	$oCodigoTrabajo 		= $oCodigosTrabajo->GetById($IdCodigoTrabajo);
	$CodigoTrabajo			= $oCodigoTrabajo->Descripcion;
	
	$Importe				= $oOrdenTrabajoTarea->Importe;
	$Titulo					= $oOrdenTrabajoTarea->Titulo;
	$Tarea					= $oOrdenTrabajoTarea->Tarea;
	$Descripcion			= $oOrdenTrabajoTarea->Descripcion;
	$HorasEstimadas			= $oOrdenTrabajoTarea->HorasEstimadas ? $oOrdenTrabajoTarea->HorasEstimadas : '0';
	$IdTipoVenta 			= $oOrdenTrabajoTarea->IdTipoVenta;
	$IdCategoria 			= $oOrdenTrabajoTarea->IdCategoria;
	$IdVendedor 			= $oOrdenTrabajoTarea->IdVendedor;
}
$filterUsuarios = array();
$filterUsuarios['IdPerfil'] = 10;
$arrUsuarios = $oUsuarios->GetAll($filterUsuarios);

IncludeSUGGEST();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

function FilterCodigoTrabajo(IdCodigoTrabajo, CodigoTrabajo) {
	$j('#IdCodigoTrabajo').val(IdCodigoTrabajo);
	$j('#CodigoTrabajo').val(CodigoTrabajo);
	
	$j('#modal-popup').dialog('close');
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

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de tareas de la orden de trabajo N&deg; <?= $oOrdenTrabajo->IdOrdenTrabajo ?> - Modificar</span></td>
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
                    <input type="hidden" name="IdOrdenTrabajoTarea" id="IdOrdenTrabajoTarea" value="<?=$IdOrdenTrabajoTarea?>" />
                 	
					<table width="90%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>&nbsp;</td>
									</tr>
                                    <tr>
                                        <td>
                                            <table border="0" align="center" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td valign="top">
                                                        <table border="0" align="center" cellpadding="0" cellspacing="0">                                                           
                                                            <tr>
																<td><div align="right">Novedad del Cliente:</div></td>
																<td>
																	<div align="left">
																		<input type="text" name="Titulo" id="Titulo" class="camporFormularioSimple" maxlength="255" value="<?=$Titulo?>" />
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20"><?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese el titulo de la tarea</li><?php } ?><?php if ($err & 64) { ?><li style="color:#FF0000;">ya existe registrado el n&uacute;mero de motor</li><?php } ?></td>
                                                            </tr>  
															 <tr>
																<td><div align="right">Tipo Cargo:</div></td>
																<td>
																	<div align="left">
																		<select name="IdTipoVenta" id="IdTipoVenta" class="camporFormularioSimple">
																		<?php
																		foreach (TipoVenta::GetAllOrdenTrabajo() as $oTipoVenta)
																		{
																			$selected = ($oTipoVenta['IdTipoVenta'] == $IdTipoVenta) ? 'selected="selected"' : '';																			
																		?>
																			<option value="<?= $oTipoVenta['IdTipoVenta'] ?>" <?= $selected ?>><?= $oTipoVenta['Nombre'] ?></option>
																		<?php
																		}
																		?>
																		</select>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20">&nbsp;</td>
                                                            </tr>
															 <tr>
																<td><div align="right">Categor&iacute;a:</div></td>
																<td>
																	<div align="left">
																		<select name="IdCategoria" id="IdCategoria" class="camporFormularioSimple">
																		<?php
																		foreach (Categorias::GetAll() as $oCategoria)
																		{
																			$selected = ($oCategoria['IdCategoria'] == $IdCategoria) ? 'selected="selected"' : '';																			
																		?>
																			<option value="<?= $oCategoria['IdCategoria'] ?>" <?= $selected ?>><?= $oCategoria['Nombre'] ?></option>
																		<?php
																		}
																		?>
																		</select>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20">&nbsp;</td>
                                                            </tr>
															 <tr>
																<td><div align="right">Vendedor:</div></td>
																<td>
																	<div align="left">
																		<select name="IdVendedor" id="IdVendedor" class="camporFormularioSimple">
																			<option value="">Indistinto</option>
																		<?php
																		foreach ($arrUsuarios as $oUsuario)
																		{
																			$selected = ($oUsuario->IdUsuario == $IdVendedor) ? 'selected="selected"' : '';																			
																		?>
																			<option value="<?= $oUsuario->IdUsuario ?>" <?= $selected ?>><?= $oUsuario->Nombre . ' ' . $oUsuario->Apellido ?></option>
																		<?php
																		}
																		?>
																		</select>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20">&nbsp;</td>
                                                            </tr>
															<tr>
																<td><div align="right">Codigo de Trabajo:</div></td>
                                                                <td>
																	<div align="left">
																		<input type="text" id="CodigoTrabajo" name="CodigoTrabajo" value="<?= $CodigoTrabajo ?>" class="camporFormularioSimpleDisabled" style="width: 225px" readonly="readonly" />
																		<a id="buscar-codigos" href="#"><img src="images/iconos/lupa.jpg" alt="Buscar" title="Buscar" class="buscar" style="margin-bottom: -6px" /></a>
																		<input type="hidden" id="IdCodigoTrabajo" name="IdCodigoTrabajo" value="<?= $IdCodigoTrabajo ?>" />
																		<span style="color:#FF0000;">&nbsp;</span>
																	</div>																				
																</td>                                                                            
                                                            </tr>   
															<tr>
                                                                <td height="20">&nbsp;</td>
                                                            </tr>
                                                            <tr>
                                                                <td><div align="right">Diagnostico:</div></td>
																<td>
																	<div align="left">
																		<textarea name="Descripcion" id="Descripcion" class="camporFormularioSimple" style="height: 75px" onkeyup="javascript: StrToUpper(this.id);"><?=$Descripcion?></textarea>
																	</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20">&nbsp;</td>
                                                            </tr>                                                      
                                                            <tr>
																<td><div align="right">Tarea Realizada:</div></td>
																<td>
																	<div align="left">
																		<input type="text" name="Tarea" id="Tarea" class="camporFormularioSimple" maxlength="255" value="<?=$Tarea?>" />
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td height="20"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese la tarea realizada</li><?php } ?></td>
                                                            </tr>
															<tr>
																<td><div align="right">Importe:</div></td>
																<td>
																	<div align="left">
																		<input type="text" name="Importe" id="Importe" class="camporFormularioSimple" value="<?=$Importe?>" />
																		<span style="color:#FF0000;">&nbsp;(*)</span>
																	</div>
																</td>
															</tr>
                                                            <tr class="tr_costoFijo">
                                                                <td height="20"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese un importe</li><?php } ?></td>
                                                            </tr>
															<tr>
																<td><div align="right">Tiempo Estimado (Hs.):</div></td>                                                                        
                                                                <td>
																	<div align="left">
																		<input type="text" name="HorasEstimadas" id="HorasEstimadas" class="camporFormularioChico" value="<?= $HorasEstimadas ?>" />
																	</div>																				
																</td>
															</tr>
                                                            <tr>
                                                                <td height="20"><?php if ($err & 2) { ?><li style="color:#FF0000;">Seleccione el tiempo estimado</li><?php } ?><?php if ($err & 64) { ?><li style="color:#FF0000;">ya existe registrado el n&uacute;mero de motor</li><?php } ?></td>
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
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'ordenestrabajotareas.php<?=$strParams?>';" value="Cancelar" />
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