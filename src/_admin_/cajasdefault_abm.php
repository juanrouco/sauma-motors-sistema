<?php

require_once('../inc_library.php'); 
require_once('../library/class.tipopago.php'); 

Session::ForceLogin();

if (!Session::CheckPerm(PERM_CAJADEFT_CREATE))
	Session::NoPerm();

$strParams					= '?' . $_SERVER['QUERY_STRING'];
$aceptarURL					= "cajasdefault_listado.php";
$cancelarURL				= "cajasdefault_listado.php";
$Submit						= (isset($_REQUEST['Submitted']));

$IdTipoPago					= intval($_REQUEST['IdTipoPago']);
$IdUbicacion				= intval($_REQUEST['IdUbicacion']);
$IdTipoPagoSeleccionado		= intval($_REQUEST['IdTipoPagoSeleccionado']);
$IdUbicacionSeleccionada 	= intval($_REQUEST['IdUbicacionSeleccionada']);
$IdCajaAdministracion		= intval($_REQUEST['IdCajaAdministracion']);
$IdCajaTaller				= intval($_REQUEST['IdCajaTaller']);
$IdCajaRepuestos			= intval($_REQUEST['IdCajaRepuestos']);

$err					= 0;
$oCajaDefault			= new CajaDetalleDefault();
$oCajasDefault			= new CajasDetallesDefault();
$oUbicaciones			= new Ubicaciones();
$oCajasDetalles			= new CajasDetalles();

$arrTiposPago			= array();
$arrUbicaciones			= array();
$arrCajasDetalle		= array();
$arrTiposPago			= TipoPago::GetAll();
$arrUbicaciones			= $oUbicaciones->GetAll();
$arrCajasDetalle		= $oCajasDetalles->GetAll();

$header = nuevaCajaDefault() ? "Cajas por Default - Crear" : "Cajas por Default - Modificar";

function nuevaCajaDefault()
{
	return (strval($_REQUEST['IdTipoPago']) == '' || strval($_REQUEST['IdUbicacion']) == '');
}

if ($Submit)
{
	if (nuevaCajaDefault() && $oCajasDefault->GetById($IdTipoPagoSeleccionado, $IdUbicacionSeleccionada))
		$err |= 1;
	
	if ($err == 0)
	{	
		$oCajaDefault->IdCajaAdministracion = $IdCajaAdministracion;
		$oCajaDefault->IdCajaTaller 		= $IdCajaTaller;
		$oCajaDefault->IdCajaRepuestos 		= $IdCajaRepuestos;
		
		if (nuevaCajaDefault())
		{
			$oCajaDefault->IdTipoPago 			= $IdTipoPagoSeleccionado;
			$oCajaDefault->IdUbicacion 			= $IdUbicacionSeleccionada;
			if ($oCajaDefault = $oCajasDefault->Create($oCajaDefault))
			{
				header('Location:' . $aceptarURL);
				exit;
			}
			else
				$err |= 32;
		} 
		else 
		{
			$oCajaDefault->IdTipoPago 			= $IdTipoPago;
			$oCajaDefault->IdUbicacion 			= $IdUbicacion;
			if ($oCajaDefault = $oCajasDefault->Update($oCajaDefault))
			{
				header('Location:' . $aceptarURL);
				exit;
			}
			else
				$err |= 64;
		}
	}
}
else
{
	if (!nuevaCajaDefault() && ($oCajaDefault = $oCajasDefault->GetById(strval($IdTipoPago), strval($IdUbicacion))))
	{
		$IdTipoPagoSeleccionado	= $oCajaDefault->IdTipoPago;
		$IdUbicacionSeleccionada = $oCajaDefault->IdUbicacion;
		$IdCajaAdministracion	= $oCajaDefault->IdCajaAdministracion;
		$IdCajaTaller			= $oCajaDefault->IdCajaTaller;
		$IdCajaRepuestos		= $oCajaDefault->IdCajaRepuestos;
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script type="text/javascript">


function validar(busqueda)
{
	return true;
}

function SetPage(page)
{
	realizarBusqueda(page);
}

$j(document).ready(function() {
	
	if (<?= $Submit ? 1 : 0?>) {
		if (<?= $err & 1?>)
			$j('#ErrorSubmit').html('<li style="color:#FF0000;">Ya existe un registro para el Tipo de Pago y la Sucursal seleccionados</li>');
		if (<?= $err & 32?>)
			$j('#ErrorSubmit').html('<li style="color:#FF0000;">Error al crear el registro</li>');
		if (<?= $err & 64?>)
			$j('#ErrorSubmit').html('<li style="color:#FF0000;">Error al modificar el registro</li>');
	}
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
        			<td height="40"><span class="tituloPagina"><?= $header ?></span></td>
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
                 	
					<table width="70%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td id="ErrorSubmit" height="20" colspan="2"></td>
									</tr>
									<tr>
										<td><div id="margen" align="right" class="tituloMenu">Tipo de Pago:</div></td>
										<td><div id="margen">
											<select id="IdTipoPagoSeleccionado" name="IdTipoPagoSeleccionado" class="<?= !nuevaCajaDefault() ? "camporFormularioSimpleDisabled" : "camporFormularioSimple" ?>" <?= !nuevaCajaDefault() ? "disabled" : "" ?>>
												<?php foreach($arrTiposPago as $rowTipoPago) { 
													$selected = $rowTipoPago['IdTipoPago'] == $IdTipoPago ||  $rowTipoPago['IdTipoPago'] == $IdTipoPagoSeleccionado ? 'selected="selected"' : '';
												?> <option value="<?=$rowTipoPago['IdTipoPago']?>" <?=$selected?>><?=$rowTipoPago['Descripcion']?></option>
												<?php } ?>
											</select>
											<span style="color:#FF0000;">&nbsp;(*)</span>
										</div></td>
									</tr>															
									<tr>											
										<td></td>
										<td id="ErrorTipoPago" height="20"></td>
									</tr>
									<tr>
										<td><div id="margen" align="right" class="tituloMenu">Sucursal:</div></td>
										<td><div id="margen">
											<select id="IdUbicacionSeleccionada" name="IdUbicacionSeleccionada" class="<?= !nuevaCajaDefault() ? "camporFormularioSimpleDisabled" : "camporFormularioSimple" ?>" <?= !nuevaCajaDefault() ? "disabled" : "" ?>>
												<?php foreach($arrUbicaciones as $oUbicacion) { 
													$selected = $oUbicacion->IdUbicacion == $IdUbicacion || $oUbicacion->IdUbicacion == $IdUbicacionSeleccionada ? 'selected="selected"' : '';
												?> <option value="<?=$oUbicacion->IdUbicacion?>" <?=$selected?>><?=$oUbicacion->Nombre?></option>
												<?php } ?>
											</select>
											<span style="color:#FF0000;">&nbsp;(*)</span>
										</div></td>
									</tr>														
									<tr>											
										<td></td>
										<td id="ErrorSucursal" height="20"></td>
									</tr>
									<tr>
										<td><div id="margen" align="right" class="tituloMenu">Caja Administraci&oacute;n:</div></td>
										<td><div id="margen">
											<select id="IdCajaAdministracion" name="IdCajaAdministracion" class="camporFormularioSimple">
												<?php foreach($arrCajasDetalle as $oCajaDetalle) { 
													$selected = $oCajaDetalle->IdCajaDetalle == $IdCajaAdministracion ? 'selected="selected"' : '';
												?> <option value="<?=$oCajaDetalle->IdCajaDetalle?>" <?=$selected?>><?=$oCajaDetalle->Nombre?></option>
												<?php } ?>
											</select>
											<span style="color:#FF0000;">&nbsp;(*)</span>
										</div></td>
									</tr>														
									<tr>											
										<td></td>
										<td height="20"></td>
									</tr>
									<tr>
										<td><div id="margen" align="right" class="tituloMenu">Caja Taller:</div></td>
										<td><div id="margen">
											<select id="IdCajaTaller" name="IdCajaTaller" class="camporFormularioSimple">
												<?php foreach($arrCajasDetalle as $oCajaDetalle) { 
													$selected = $oCajaDetalle->IdCajaDetalle == $IdCajaTaller ? 'selected="selected"' : '';
												?> <option value="<?=$oCajaDetalle->IdCajaDetalle?>" <?=$selected?>><?=$oCajaDetalle->Nombre?></option>
												<?php } ?>
											</select>
											<span style="color:#FF0000;">&nbsp;(*)</span>
										</div></td>
									</tr>														
									<tr>											
										<td></td>
										<td height="20"></td>
									</tr>
									<tr>
										<td><div id="margen" align="right" class="tituloMenu">Caja Repuestos:</div></td>
										<td><div id="margen">
											<select id="IdCajaRepuestos" name="IdCajaRepuestos" class="camporFormularioSimple">
												<?php foreach($arrCajasDetalle as $oCajaDetalle) { 
													$selected = $oCajaDetalle->IdCajaDetalle == $IdCajaRepuestos ? 'selected="selected"' : '';
												?> <option value="<?=$oCajaDetalle->IdCajaDetalle?>" <?=$selected?>><?=$oCajaDetalle->Nombre?></option>
												<?php } ?>
											</select>
											<span style="color:#FF0000;">&nbsp;(*)</span>
										</div></td>
									</tr>		
									<tr>
										<td height="20" colspan="2"><div align="right">&nbsp;</div></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td><div align="center"></div></td>
						</tr>
					</table>
					<table width="70%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = '<?=$cancelarURL?>';" value="Cancelar" />
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

<div class="modal"><!-- Place at bottom of page --></div>
</body>
</html>										