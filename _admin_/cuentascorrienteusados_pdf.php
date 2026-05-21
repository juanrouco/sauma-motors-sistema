<?php

require_once('../inc_library.php'); 
require_once('../library/mpdf/mpdf.php');
ob_clean();

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_VENT_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$IdMinuta	= intval($_REQUEST['IdMinuta']);
$Submit		= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$oMinutas 			= new MinutasUsados();
$oClientes 			= new Clientes();
$oUsuarios 			= new Usuarios();
$oUsados 			= new Usados();
$oPagos				= new Pagos();

/* verifica si existe el registro a modificar */
if (!$oMinuta = $oMinutas->GetById($IdMinuta))
{	
	header("Location: cuentascorrienteusados.php" . $strParams);
	exit();
}

$arrMinutasFinanciacion = $oMinuta->GetMinutasFinanciacion();

/* obtenemos los datos de la unidad */
if (!$oUsado = $oUsados->GetById($oMinuta->IdUsado))
	exit();

/* obtenemos los datos del vendedor */
if (!$oUsuario = $oUsuarios->GetById($oMinuta->IdUsuario))
	exit();

/* obtenemos los datos del cliente */
if (!$oCliente = $oClientes->GetById($oMinuta->IdCliente))
	exit();

$cliente = $oCliente->RazonSocial;
/* obtenemos informacion del condominio en caso de que existiera */
if ($oClienteCondominio = $oClientes->GetById($oGestoria->IdClienteCondominio))
	$cliente .= ' / ' . $oClienteCondominio->RazonSocial;

$oClienteReventa = $oClientes->GetById($oMinuta->IdClienteReventa);

/* obtenemos informacion del usado en caso de que existiera */
if ($oMinuta->EntregaUsado) 
{
	$arrUsados = $oUsados->GetAllByIdMinutaUsado($oMinuta->IdMinuta);
	
	$oUsadoTomado = $arrUsados[0];
	if (count($arrUsados) > 1)
		$oUsado2 = $arrUsados[1];
}

$FinanciacionCapital = 0;
if ($arrMinutasFinanciacion && count($arrMinutasFinanciacion) > 0)
{
		$Financiacion = true;
	foreach ($arrMinutasFinanciacion as $oMinutaFinanciacion)
		$FinanciacionCapital += $oMinutaFinanciacion->Importe;
}

$arrPagos = $oPagos->GetByIdMinutaUsado($oMinuta->IdMinuta);

$PrecioVenta			= $oMinuta->PrecioVenta;
$Gastos					= $oMinuta->Gastos;
$GastosPatentamiento	= $oMinuta->GastosPatentamiento;
$GastosOtorgamiento		= $oMinuta->GastosOtorgamiento;
$DepositoGarantia		= $oMinuta->DepositoGarantia;
$GastosPrenda			= $oMinuta->GastosPrenda;
$Circular				= $oMinuta->Circular;
$Anticipo				= $oMinuta->Anticipo;
//$FinanciacionCapital	= $oMinuta->FinanciacionCapital;
//$Financiacion			= (($oMinuta->FinanciacionCapital != '') && ($oMinuta->FinanciacionCapital != '0')) ? '1' : '0';
$UsadoValuacion			= $oUsadoTomado->Valuacion;
$UsadoValuacion2		= 0;
$UsadoValuacion2		= $oUsado2->Valuacion;

$EntregaUsado			= $oMinuta->EntregaUsado;

/* creamos el objeto para manipular el .pdf */
$oMpdf = new mPDF();
$oMpdf->watermarkText = '';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<style>
body {
	background-color: #FFFFFF;
}
td {
	font-size: 14px; 
	color: #000000; 
	font-family: Arial, Helvetica, sans-serif;
}
.texto20 {
	font-size: 20px; 
	color: #000000; 
	font-family: Arial, Helvetica, sans-serif;
	font-weight:bold;
}
.bordeBottom {
	border-bottom: 2px solid #000000;
}
.textoPie {
	font-size: 11px; 
	color: #000000; 
	font-family: Arial, Helvetica, sans-serif;
}
</style>

</head>
<body>

<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
    	<td>
        	<div align="center">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                    <tr>
                        <td>
                            <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
                                <tr>
	                                <td width="31%">&nbsp;</td>
                                    <td width="45%">&nbsp;</td>
	                                <td width="24%" align="right"><div align="right">FECHA: <?=CambiarFecha($oMinuta->FechaMinuta)?></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
                                <tr>
                                	<td align="center"><div align="center"><img src="images/logo_tolosa.jpg" width="250" height="50" /></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="30" align="center">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr align="center">
                                    <td align="center"><div align="center"><span class="texto20">CUENTA CORRIENTE USADO N&deg; <?= $oUsado->IdUsado ?></span></div></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<tr>
                    	<td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="59%"><strong>N&deg; INTERNO: </strong><?=utf8_encode($oUsado->IdUsado)?></td>
                                    <td width="41%"><strong>MODELO: </strong><?=utf8_encode($oUsado->Modelo)?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="59%"><strong>CLIENTE: </strong><?=utf8_encode($cliente)?></td>
                                	<td width="5%">&nbsp;</td>
                                    <td width="36%"><strong>VENDEDOR: </strong><?=utf8_encode($oUsuario->Nombra . ' ' . $oUsuario->Apellido)?></td>
                                </tr>
                            </table>
                        </td>
                    </tr><tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="59%"><strong>FECHA VENTA: </strong><?=CambiarFecha($oMinuta->FechaMinuta)?></td>
                                    <td width="41%">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                    	<td>&nbsp;</td>
                    </tr>
                    <tr>
                    	<td align="center"><div align="center"><strong>DETALLE CUENTA CORRIENTE</strong></div></td>
                    </tr>
                    <tr>
                    	<td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <table width="70%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                	<td width="37%">PRECIO DE VENTA</td>
                                    <td width="14%">&nbsp;</td>
                                	<td width="49%">$ <?=number_format($PrecioVenta)?></td>
                                </tr>
								<tr>
                                	<td width="37%"><?= utf8_encode('SEÑA') ?></td>
                                    <td width="14%">&nbsp;</td>
                                	<td width="49%"> - $ <?=number_format($DepositoGarantia)?></td>
                                </tr>
								<?php
								if ($EntregaUsado)
								{
								?>
                                <tr>
                                	<td>USADO</td>
                                    <td>&nbsp;</td>
                                	<td> - $ <?=number_format($UsadoValuacion + $UsadoValuacion2)?></td>
                                </tr>
                                <tr>
                                	<td>ARREGLOS USADO</td>
                                    <td>&nbsp;</td>
                                	<td>$ <?=number_format($oUsadoTomado->Arreglos + $oUsado2->Arreglos)?></td>
                                </tr>
								<?php
								}
								if ($Financiacion)
								{
								?>
								<tr>
                                	<td>CAPITAL A FINANCIAR</td>
                                    <td>&nbsp;</td>
                                	<td>- $ <?=number_format($FinanciacionCapital)?></td>
                                </tr>
								<?php
								}
								?>
								<tr>
                                    <td>GASTOS OTORGAMIENTO</td>
                                    <td>&nbsp;</td>
                                    <td>$ <?=number_format($GastosOtorgamiento)?></td>
                                </tr>
								<tr>
                                    <td>GASTOS GESTORIA</td>
                                    <td>&nbsp;</td>
                                    <td>$ <?=number_format($Anticipo)?></td>
                                </tr>
                                <tr>
                                    <td width="37%">OTROS GASTOS</td>
                                    <td>&nbsp;</td>
                                    <td width="49%">$ <?=number_format($Gastos)?></td>
                                </tr>
								<tr>
                                    <td>PAGOS</td>
                                    <td>&nbsp;</td>
                                    <td>- $ <?=number_format($oMinuta->GetTotalPagos())?></td>
                                </tr>
								<tr>
                                    <td><strong>SALDO A PAGAR</strong></td>
                                    <td>&nbsp;</td>
                                    <td>$ <?=number_format($oMinuta->GetTotalAAbonar())?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					
					<?php
					if ($arrPagos)
					{
					?>
					<tr>
                    	<td>&nbsp;</td>
                    </tr>
                    <tr>
                    	<td align="center"><div align="center"><strong>PAGOS REALIZADOS</strong></div></td>
                    </tr>
                    <tr>
                    	<td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <table align="center" width="100%" border="0" cellpadding="0" cellspacing="0">
                               <tr>
                                    <td width="200" align="center"><strong>FECHA</strong></td>
									<td width="200" align="center"><strong>IMPORTE</strong></td>
									<td width="200" align="center"><strong>TIPO PAGO</strong></td>
                                </tr>
								<?php
								foreach ($arrPagos as $oPago)
								{
								?>
								<tr>
                                    <td width="200" align="center"><?=CambiarFecha($oPago->Fecha)?></td>
									<td width="200" align="center">$ <?= number_format($oPago->Importe, 2, ',', '.') ?></td>
									<td width="200" align="center"><?= TipoPago::GetById($oPago->IdTipoPago) ?></td>
                                </tr>
								<?php
								}
								?>
                                
								
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
                        <td>&nbsp;</td>
                    </tr>
                </table>
          	</div>
       	</td>
   	</tr>
</table>

</body>
</html>

<?php

$Contenido = ob_get_contents();
ob_end_clean();

$oMpdf->WriteHTML($Contenido);
$oMpdf->Output('cuenta corriente.pdf', 'D'); 

?>