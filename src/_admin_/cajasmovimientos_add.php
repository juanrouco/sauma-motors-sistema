<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CAJA_LIST) && $currentUser->IdPerfil != Perfil::Tesorero && ($currentUser->IdUsuario != 29) &&  !($currentUser->IdPerfil == Perfil::Vendedor && ($currentUser->IdUsuario == 24 || $currentUser->IdUsuario == 10)))
	Session::NoPerm();

/* obtiene datos del formulario */
$IdCajaDetalle		= intval($_REQUEST['IdCajaDetalle']);
$IdTipoMovimiento	= intval($_REQUEST['IdTipoMovimiento']);
$Importe			= strval($_REQUEST['Importe']);
$Comentarios		= strval($_REQUEST['Comentarios']);
$IdCajaDestino		= intval($_REQUEST['IdCajaDestino']);
$IdCajaOrigen		= intval($_REQUEST['IdCajaOrigen']);
$IdConcepto			= intval($_REQUEST['IdConcepto']);
$IdUsuario			= intval($_REQUEST['IdUsuario']);
$IdUsuario2			= intval($_REQUEST['IdUsuario2']);
$Submit				= (isset($_REQUEST['Submitted']));

/* declaramos e instanciamos variables necesarias */
$err				= 0;
$oCajaMovimiento 	= new CajaMovimiento();
$oCajasMovimientos	= new CajasMovimientos();
$oCajasDetalles		= new CajasDetalles();
$oUsuarios			= new Usuarios();

if (!$oCajaDetalle = $oCajasDetalles->GetById($IdCajaDetalle))
{
	header("Location: cajas_detalle.php" . $strParams);
	exit();
}

$arrCajasDetalle = $oCajasDetalles->GetAll();
$arrUsuarios = $oUsuarios->GetAll();
$arrUsuariosEspecial = $oUsuarios->GetAll(array('Especial' => 1));

/* definimos cadena a mandar por get */
$strParams = (strlen($_SERVER['QUERY_STRING']) > 0) ? '?' . $_SERVER['QUERY_STRING'] : '';

/* si el formulario fue enviado */
if ($Submit)
{
	$Importe = floatval(str_replace(',', '.', $Importe));
	/* validaciones... */
	if (!$IdTipoMovimiento)
		$err |= 1;
	if (!$Importe)
		$err |= 2;
	if ($IdTipoMovimiento == TiposMovimientosCaja::TransferenciaCaja && !$IdCajaDestino)
		$err |= 4;
	if ($IdTipoMovimiento == TiposMovimientosCaja::TransferenciaCaja && $Comentarios == '')
		$err |= 64;
	if (($IdTipoMovimiento == TiposMovimientosCaja::Egreso || $IdTipoMovimiento == TiposMovimientosCaja::EgresosRemesas || $IdTipoMovimiento == TiposMovimientosCaja::TransferenciaCaja || $IdTipoMovimiento == TiposMovimientosCaja::Gastos) && $oCajaDetalle->IdCajaDetalle != 9  && $oCajaDetalle->Total < $Importe)
		$err |= 8;
	if (($IdTipoMovimiento == TiposMovimientosCaja::Egreso || $IdTipoMovimiento == TiposMovimientosCaja::EgresosRemesas || $IdTipoMovimiento == TiposMovimientosCaja::Gastos) && !$IdConcepto)
		$err |= 16;
	elseif (!($IdTipoMovimiento == TiposMovimientosCaja::Egreso || $IdTipoMovimiento == TiposMovimientosCaja::EgresosRemesas || $IdTipoMovimiento == TiposMovimientosCaja::Gastos))
		$IdConcepto = '';
	if ($IdConcepto == ConceptosCajas::Sueldos)
		$IdUsuario = $IdUsuario2;
	if ($IdConcepto == ConceptosCajas::Sueldos && !$IdUsuario)
		$err |= 32;

	/* si no hay errores... */
	if ($err == 0)
	{
		$oCajaMovimiento->IdTipoMovimiento		= $IdTipoMovimiento;
		if ($IdTipoMovimiento != TiposMovimientosCaja::MovimientoCajaEntrada)
			$Importe = $Importe * -1;
		$oCajaMovimiento->Total 				= $Importe;
		$oCajaMovimiento->Comentarios 			= $Comentarios;
		$oCajaMovimiento->Fecha					= date('Y-m-d H:i:s');
		$oCajaMovimiento->IdCajaDetalle 		= $IdCajaDetalle;
		$oCajaMovimiento->IdConcepto	 		= $IdConcepto;
		$oCajaMovimiento->IdUsuario	 			= $IdUsuario;
		
		if ($IdTipoMovimiento == TiposMovimientosCaja::TransferenciaCaja)
		{
			$oCajaMovimiento->IdCajaOrigen			= $IdCajaDetalle;
			$oCajaMovimiento->IdCajaDestino			= $IdCajaDestino;
		}

		/* crea el usuario */
		$oCajaMovimiento = $oCajasMovimientos->Create($oCajaMovimiento);
		
		$oCajasDetalles = new CajasDetalles();
		$oCajaDetalle = $oCajasDetalles->GetById($oCajaMovimiento->IdCajaDetalle);
		$oCajaDetalle->FechaUltimoMovimiento = date('Y-m-d H:i:s');
		$oCajaDetalle->Total += $oCajaMovimiento->Total;
		$oCajasDetalles->Update($oCajaDetalle);
				
		$oCajas = new Cajas();
		$oCaja = $oCajas->GetById(1);
		$oCaja->TotalRendir += $oCajaMovimiento->Total;
		$oCaja->TotalDetalles += $oCajaMovimiento->Total;
		$oCajas->Update($oCaja);
		
		if ($IdTipoMovimiento == TiposMovimientosCaja::TransferenciaCaja)
		{
			$oCajaMovimiento = new CajaMovimiento();
			$oCajaMovimiento->IdTipoMovimiento		= $IdTipoMovimiento;
			$Importe = $Importe * -1;
			$oCajaMovimiento->Total 				= $Importe;
			$oCajaMovimiento->Comentarios 			= $Comentarios;
			$oCajaMovimiento->Fecha					= date('Y-m-d H:i:s');
			$oCajaMovimiento->IdCajaDetalle 		= $IdCajaDestino;
			$oCajaMovimiento->IdCajaOrigen			= $IdCajaDestino;
			$oCajaMovimiento->IdCajaDestino			= $IdCajaDetalle;
	
			/* crea el usuario */
			$oCajaMovimiento = $oCajasMovimientos->Create($oCajaMovimiento);
			
			$oCajasDetalles = new CajasDetalles();
			$oCajaDetalle = $oCajasDetalles->GetById($oCajaMovimiento->IdCajaDetalle);
			$oCajaDetalle->FechaUltimoMovimiento = date('Y-m-d H:i:s');
			$oCajaDetalle->Total += $oCajaMovimiento->Total;
			$oCajasDetalles->Update($oCajaDetalle);
					
			$oCajas = new Cajas();
			$oCaja = $oCajas->GetById(1);
			$oCaja->TotalRendir += $oCajaMovimiento->Total;
			$oCaja->TotalDetalles += $oCajaMovimiento->Total;
			$oCajas->Update($oCaja);
		}


		header("Location: cajas_detalle.php" . $strParams);
		exit();
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<link type="text/css" rel="stylesheet" href="../library/calendar/calendar.css" />
<script language="javascript" src="../library/calendar/calendar_us.js"></script>


<script language="javascript">

function ValidarCajaDestino(value)
{
	if (value == '<?= TiposMovimientosCaja::TransferenciaCaja ?>')
		ShowSection('tr_CajaDestino');
	else
		HideSection('tr_CajaDestino');
	
	if (value == '<?= TiposMovimientosCaja::Egreso ?>' || value == '<?= TiposMovimientosCaja::EgresosRemesas ?>' || value == '<?= TiposMovimientosCaja::Gastos ?>')
		ShowSection('trConcepto');
	else
	{
		HideSection('trConcepto');
		
		HideSection('trConceptoError');
	}
	
	if (value == '<?= TiposMovimientosCaja::Ingreso ?>')
		ShowSection('trUsuarioEspecial');
	else
		HideSection('trUsuarioEspecial');
	
}

function ValidarConcepto(value)
{
	if (value == '<?= ConceptosCajas::Sueldos ?>')
	{
		ShowSection('trUsuario');
	}
	else
	{
		HideSection('trUsuario');
		
		HideSection('trUsuarioError');
	}
	
	if (value != '' && value != '0' && value != '<?= ConceptosCajas::Sueldos ?>')
	{
		ShowSection('trUsuarioEspecial');
	}
	else
	{
		HideSection('trUsuarioEspecial');
	}
}

</script>

</head>
<body>

<form name="frmData" id="frmData" method="post" action="<?=$strParams?>" >
    <input type="hidden" name="Submitted" id="Submitted" value="1" />
    <input type="hidden" name="IdCajaDetalle" id="IdCajaDetalle" value="<?= $IdCajaDetalle ?>" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloRubro">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Caja <?= $oCajaDetalle->Nombre ?> - Agregar Movimiento</span></td>
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
                    <table width="75%"  border="0" align="center" cellpadding="5" cellspacing="0">
                        <tr>
                            <td class="bordeGris">
                                <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">Tipo Movimiento:</div></td>
                                        <td>
                                            <div align="left">
                                                <select name="IdTipoMovimiento" id="IdTipoMovimiento" class="camporFormularioSimple" onchange="ValidarCajaDestino(this.value);">
                                                	<option value="">Seleccione el tipo de movimiento</option>
                                                	<?php
                                                	foreach (TiposMovimientosCaja::GetAllEditable() as $oTipoMovimiento)
                                                	{
                                                		$selected = '';
                                                		if ($oTipoMovimiento['IdTipo'] == $IdTipoMovimiento)
                                                			$selected = 'selected="selected"';
                                                	?>
                                                	<option value="<?= $oTipoMovimiento['IdTipo'] ?>" <?= $selected ?>><?= $oTipoMovimiento['Descripcion'] ?></option>
                                                	<?php
                                                	}
                                                	?>
                                                </select>
                                                <span style="color:#FF0000;">&nbsp;(*)</span>										
                                            </div>
                                        </td>
                                    </tr>
                               		<?php if ($err & 1) { ?>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td><li style="color:#FF0000;">Seleccione el tipo de movimiento</li></td>
                                    </tr>
                               		<?php } ?>
                                    <tr id="trConcepto" style="display: none">
                                        <td><div align="right">Concepto:</div></td>
                                        <td>
                                            <div align="left">
                                                <select name="IdConcepto" id="IdConcepto" class="camporFormularioSimple" onchange="ValidarConcepto(this.value);">
                                                	<option value="">Seleccione el concepto</option>
                                                	<?php
                                                	foreach (ConceptosCajas::GetAll() as $oConcepto)
                                                	{
                                                		$selected = '';
                                                		if ($oConcepto['IdConcepto'] == $IdConcepto)
                                                			$selected = 'selected="selected"';
                                                	?>
                                                	<option value="<?= $oConcepto['IdConcepto'] ?>" <?= $selected ?>><?= $oConcepto['Descripcion'] ?></option>
                                                	<?php
                                                	}
                                                	?>
                                                </select>
                                                <span style="color:#FF0000;">&nbsp;(*)</span>										
                                            </div>
                                        </td>
                                    </tr>
                               		<?php if ($err & 16) { ?>
                                    <tr id="trConceptoError">
                                        <td>&nbsp;</td>
                                        <td><li style="color:#FF0000;">Seleccione el concepto</li></td>
                                    </tr>
                               		<?php } ?>
                                    <tr id="trUsuario" style="display: none">
                                        <td><div align="right">Usuario:</div></td>
                                        <td>
                                            <div align="left">
                                                <select name="IdUsuario2" id="IdUsuario2" class="camporFormularioSimple">
                                                	<option value="">Seleccione el usuario</option>
                                                	<?php
                                                	foreach ($arrUsuarios as $oUsuario)
                                                	{
                                                		$selected = '';
                                                		if ($oUsuario->IdUsuario == $IdUsuario2)
                                                			$selected = 'selected="selected"';
                                                	?>
                                                	<option value="<?= $oUsuario->IdUsuario ?>" <?= $selected ?>><?= $oUsuario->Nombre . ' ' . $oUsuario->Apellido ?></option>
                                                	<?php
                                                	}
                                                	?>
                                                </select>
                                                <span style="color:#FF0000;">&nbsp;(*)</span>										
                                            </div>
                                        </td>
                                    </tr>
                                    <tr id="trUsuarioEspecial" style="display: none">
                                        <td><div align="right">Usuario:</div></td>
                                        <td>
                                            <div align="left">
                                                <select name="IdUsuario" id="IdUsuario" class="camporFormularioSimple">
                                                	<option value="">No Aplica</option>
                                                	<?php
                                                	foreach ($arrUsuariosEspecial as $oUsuario)
                                                	{
                                                		$selected = '';
                                                		if ($oUsuario->IdUsuario == $IdUsuario)
                                                			$selected = 'selected="selected"';
                                                	?>
                                                	<option value="<?= $oUsuario->IdUsuario ?>" <?= $selected ?>><?= $oUsuario->Nombre . ' ' . $oUsuario->Apellido ?></option>
                                                	<?php
                                                	}
                                                	?>
                                                </select>
                                                <span style="color:#FF0000;">&nbsp;(*)</span>										
                                            </div>
                                        </td>
                                    </tr>
                               		<?php if ($err & 32) { ?>
                                    <tr id="trUsuarioError">
                                        <td>&nbsp;</td>
                                        <td><li style="color:#FF0000;">Seleccione el usuario</li></td>
                                    </tr>
                               		<?php } ?>
                               
                                    <tr>
                                        <td><div align="right">Importe:</div></td>
                                        <td>
                                            <div align="left">
                                                <input type="text" name="Importe" id="Importe" class="camporFormularioChico" maxlength="16" value="<?=$Importe;?>" />
                                                <span style="color:#FF0000;">&nbsp;(*)</span>	
                                            </div>									
                                        </td>
                                    </tr>
                                    <?php if ($err & 2) { ?>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td><li style="color:#FF0000;">Ingrese un importe</li></td>
                                    </tr>
                               		<?php } ?>
                                    <?php if ($err & 8) { ?>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td><li style="color:#FF0000;">Esta intentando sacar más plata que la disponible</li></td>
                                    </tr>
                               		<?php } ?>
                                    <tr id="tr_CajaDestino" style="<?= TiposMovimientosCaja::TransferenciaCaja == $IdTipoMovimiento ? '' : 'display: none;' ?>">
                                        <td><div align="right">Caja de Destino:</div></td>
                                        <td>
                                            <div align="left">
                                                <select name="IdCajaDestino" id="IdCajaDestino" class="camporFormularioSimple">
                                                	<option value="">Seleccione la caja destino</option>
                                                	<?php
                                                	foreach ($arrCajasDetalle as $oCajaDetalle)
                                                	{
                                                		if ($oCajaDetalle->IdCajaDetalle != $IdCajaDetalle && $oCajaDetalle->IdCajaDetalle != 2 && (true || $oCajaDetalle->IdCajaDetalle == CajaDetalle::BancoPatagonia || $oCajaDetalle->IdCajaDetalle == CajaDetalle::BancoGalicia || $oCajaDetalle->IdCajaDetalle == CajaDetalle::BancoHonda))
                                                		{
	                                                		$selected = '';
	                                                		if ($oCajaDetalle->IdCajaDetalle == $IdCajaDestino)
	                                                			$selected = 'selected="selected"';
                                                	?>
                                                	<option value="<?= $oCajaDetalle->IdCajaDetalle ?>" <?= $selected ?>><?= $oCajaDetalle->Nombre ?></option>
                                                	<?php
                                                		}
                                                	}
                                                	?>
                                                </select>
                                                <span style="color:#FF0000;">&nbsp;(*)</span>										
                                            </div>					
                                        </td>
                                    </tr>
                               		<?php if ($err & 4) { ?>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td><li style="color:#FF0000;">Seleccione la caja de destino</li></td>
                                    </tr>
                               		<?php } ?>
                               		                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                               
                                    <tr>
                                        <td><div align="right">Comentarios:</div></td>
                                        <td>
                                            <div align="left">
                                                <textarea name="Comentarios" id="Comentarios" class="camporFormularioSimple" style="height:75px"><?=$Comentarios;?></textarea>
                                            </div>									
                                        </td>
                                    </tr>
                               		<?php if ($err & 64) { ?>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td><li style="color:#FF0000;">Ingrese un comentario</li></td>
                                    </tr>
                               		<?php } ?>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                </table>						
                            </td>
                        </tr>
                    </table>
                    <table width="75%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td height="1"><div align="center"></div></td>
                        </tr>
                    </table>
                    <table width="75%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                        <tr>
                            <td height="30">
                                <div align="center">
                                    <input type="submit" name="btnAceptar" id="btnAceptar" class="botonBasico" value="Aceptar" />
                                    <input type="button" name="btnCancelar" id="btnCancelar" class="botonBasico" onclick="javascript: window.location.href = 'cajas_detalle.php<?=$strParams?>';" value="Cancelar" />
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
    </table>
</form>
<script type="text/javascript">
ValidarCajaDestino('<?= $IdTipoMovimiento ?>');
ValidarConcepto('<?= $IdConcepto ?>');
</script>
</body>
</html>