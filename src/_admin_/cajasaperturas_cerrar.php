<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CAJA_CREATE))
	Session::NoPerm();
	
$oUsuario = Session::GetCurrentUser();

/* obtiene datos del formulario */
$IdCajaDetalle		= CajaDetalle::CajaChica;
$IdTipoApertura		= 2;
$IdUsuario			= $oUsuario->IdUsuario;
if (date('H') <= 14)
	$IdTurno = 1;
else
	$IdTurno = 2;
	
$Importe100			= floatval($_REQUEST['Importe100']);
$Importe1002		= floatval($_REQUEST['Importe1002']);
$Importe50			= floatval($_REQUEST['Importe50']);
$Importe20			= floatval($_REQUEST['Importe20']);
$Importe10			= floatval($_REQUEST['Importe10']);
$Importe5			= floatval($_REQUEST['Importe5']);
$Importe2			= floatval($_REQUEST['Importe2']);
$Importe0			= floatval($_REQUEST['Importe0']);
$Importe			= floatval($_REQUEST['Importe']);
$Intento			= intval($_REQUEST['Intento']);
$Submit				= (isset($_REQUEST['Submitted']));

/* declaramos e instanciamos variables necesarias */
$err				= 0;
$oCajasDetalles		= new CajasDetalles();
$oCajasAperturas	= new CajasAperturas();
$oCajasMovimientos	= new CajasMovimientos();

if (!$oCajaDetalle = $oCajasDetalles->GetById($IdCajaDetalle))
{
	header("Location: cajas.php" . $strParams);
	exit();
}

/* definimos cadena a mandar por get */
$strParams = (strlen($_SERVER['QUERY_STRING']) > 0) ? '?' . $_SERVER['QUERY_STRING'] : '';

/* si el formulario fue enviado */
if ($Submit)
{
	$Importe = floatval(str_replace(',', '.', $Importe));
	/* validaciones... */
	if (!$Importe)
		$err |= 1;
	elseif ($Intento < 3)
	{
		$oCajaDetalle = $oCajasDetalles->GetById($IdCajaDetalle);
		if (abs($oCajaDetalle->Total - $Importe) > 0.001)
			$err |= 2;
		$Intento++;
	}

	/* si no hay errores... */
	if ($err == 0)
	{
		$oCajaApertura	= new CajaApertura();
		$oCajaApertura->IdUsuario 		= $IdUsuario;
		$oCajaApertura->IdTipoApertura	= $IdTipoApertura;
		$oCajaApertura->IdTurno			= $IdTurno;
		$oCajaApertura->TotalRendir		= $oCajaDetalle->Total;
		$oCajaApertura->TotalReal		= $Importe;
		$oCajaApertura->Diferencia		= $Importe - $oCajaDetalle->Total;
		$oCajaApertura->Fecha			= date('d-m-Y H:i:s');
		$oCajaApertura->IdCajaDetalle	= $IdCajaDetalle;
		
		$oCajaApertura = $oCajasAperturas->Create($oCajaApertura);
		
		$oCajaMovimiento = new CajaMovimiento();
		$oCajaMovimiento->IdTipoMovimiento		= TiposMovimientosCaja::Cierre;
		$oCajaMovimiento->Total 				= $oCajaApertura->Diferencia;
		$oCajaMovimiento->Fecha					= date('Y-m-d H:i:s');
		$oCajaMovimiento->IdCajaDetalle 		= $IdCajaDetalle;

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

		header("Location: cajas.php" . $strParams);
		exit();
	}
}
else
{
	$Intento = 1;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<link type="text/css" rel="stylesheet" href="../library/calendar/calendar.css" />
<script language="javascript" src="../library/calendar/calendar_us.js"></script>


<script type="text/javascript">
function ActualizarTotal()
{
	var Importe100 = $j('#Importe100').val() == '' ? 0 : parseFloat($j('#Importe100').val()) * 100;
	var Importe1002 = $j('#Importe1002').val() == '' ? 0 : parseFloat($j('#Importe1002').val()) * 100;
	var Importe50 = $j('#Importe50').val() == '' ? 0 : parseFloat($j('#Importe50').val()) * 50;
	var Importe20 = $j('#Importe20').val() == '' ? 0 : parseFloat($j('#Importe20').val()) * 20;
	var Importe10 = $j('#Importe10').val() == '' ? 0 : parseFloat($j('#Importe10').val()) * 10;
	var Importe5 = $j('#Importe5').val() == '' ? 0 : parseFloat($j('#Importe5').val()) * 5;
	var Importe2 = $j('#Importe2').val() == '' ? 0 : parseFloat($j('#Importe2').val()) * 2;
	var Importe0 = $j('#Importe0').val() == '' ? 0 : parseFloat($j('#Importe0').val());
	var total = Importe100 + Importe1002 + Importe50 + Importe20 + Importe10 + Importe5 + Importe2 + Importe0;
	$j('#Importe').val(total.toFixed(2));
}
</script>

</head>
<body>

<form name="frmData" id="frmData" method="post" action="<?=$strParams?>" >
    <input type="hidden" name="Submitted" id="Submitted" value="1" />
    <input type="hidden" name="IdCajaDetalle" id="IdCajaDetalle" value="<?= $IdCajaDetalle ?>" />
    <input type="hidden" name="Intento" id="Intento" value="<?= $Intento ?>" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloRubro">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Cierre de Caja <?= $oCajaDetalle->Nombre ?></span></td>
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
                                        <td width="45%">&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">Importe:</div></td>
                                        <td>
                                            <div align="left">
                                                <input type="text" name="Importe" id="Importe" class="camporFormularioChico" readonly="true" maxlength="16" value="<?=$Importe;?>" />
                                                <span style="color:#FF0000;">&nbsp;(*)</span>	
                                            </div>									
                                        </td>
                                    </tr>
                                    <?php if ($err & 1) { ?>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td><li style="color:#FF0000;">Ingrese un importe</li></td>
                                    </tr>
                               		<?php } ?>
                                    <?php if ($err & 2) { ?>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td><li style="color:#FF0000;">El importe ingresado es diferente al valor esperado en la caja<br> Por favor reintente.</li></td>
                                    </tr>
                               		<?php } ?>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
									<tr>
                                        <td><div align="right">Reserva:</div></td>
                                        <td>
                                            <div align="left">
                                                <input type="text" name="Importe1002" id="Importe1002" class="camporFormularioChico" onkeyup="javascript: ActualizarTotal()" maxlength="16" value="<?=$Importe1002 == 0 ? '' : $Importe1002;?>" />
                                            </div>									
                                        </td>
                                    </tr>
									<tr>
                                        <td><div align="right">$100:</div></td>
                                        <td>
                                            <div align="left">
                                                <input type="text" name="Importe100" id="Importe100" class="camporFormularioChico" onkeyup="javascript: ActualizarTotal()" maxlength="16" value="<?=$Importe100 == 0 ? '' : $Importe100;?>" />
                                            </div>									
                                        </td>
                                    </tr>
									<tr>
                                        <td><div align="right">$50:</div></td>
                                        <td>
                                            <div align="left">
                                                <input type="text" name="Importe50" id="Importe50" class="camporFormularioChico" onkeyup="javascript: ActualizarTotal()" maxlength="16" value="<?=$Importe50 == 0 ? '' : $Importe50;?>" />
                                            </div>									
                                        </td>
                                    </tr>
									<tr>
                                        <td><div align="right">$20:</div></td>
                                        <td>
                                            <div align="left">
                                                <input type="text" name="Importe20" id="Importe20" class="camporFormularioChico" onkeyup="javascript: ActualizarTotal()" maxlength="16" value="<?=$Importe20 == 0 ? '' : $Importe20;?>" />	
                                            </div>									
                                        </td>
                                    </tr>
									<tr>
                                        <td><div align="right">$10:</div></td>
                                        <td>
                                            <div align="left">
                                                <input type="text" name="Importe10" id="Importe10" class="camporFormularioChico" onkeyup="javascript: ActualizarTotal()" maxlength="16" value="<?=$Importe10 == 0 ? '' : $Importe10;?>" />	
                                            </div>									
                                        </td>
                                    </tr>
									<tr>
                                        <td><div align="right">$5:</div></td>
                                        <td>
                                            <div align="left">
                                                <input type="text" name="Importe5" id="Importe5" class="camporFormularioChico" onkeyup="javascript: ActualizarTotal()" maxlength="16" value="<?=$Importe5 == 0 ? '' : $Importe5;?>" />	
                                            </div>									
                                        </td>
                                    </tr>
									<tr>
                                        <td><div align="right">$2:</div></td>
                                        <td>
                                            <div align="left">
                                                <input type="text" name="Importe2" id="Importe2" class="camporFormularioChico" onkeyup="javascript: ActualizarTotal()" maxlength="16" value="<?=$Importe2 == 0 ? '' : $Importe2;?>" />	
                                            </div>									
                                        </td>
                                    </tr>
									<tr>
                                        <td><div align="right">Monedas:</div></td>
                                        <td>
                                            <div align="left">
                                                <input type="text" name="Importe0" id="Importe0" class="camporFormularioChico" onkeyup="javascript: ActualizarTotal()" maxlength="16" value="<?=$Importe0 == 0 ? '' : $Importe0;?>" />	
                                            </div>									
                                        </td>
                                    </tr>
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
                                    <input type="button" name="btnCancelar" id="btnCancelar" class="botonBasico" onclick="javascript: window.location.href = 'cajas.php<?=$strParams?>';" value="Cancelar" />
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

</body>
</html>