<?php
require_once('../inc_library_includes.php');

$IdAcreedor 			= intval($_REQUEST['IdAcreedor']);
$Cuotas	 				= intval($_REQUEST['Cuotas']);
$FinanciacionCapital	= floatval($_REQUEST['FinanciacionCapital']);

$oAcreedoresCuotas 	= new AcreedoresCuotas();
$oAcreedores	 	= new Acreedores();

$oAcreedor = $oAcreedores->GetById($IdAcreedor);

$oAcreedorCuota = $oAcreedoresCuotas->GetAllByAcreedorAndCuota($oAcreedor, $Cuotas);

if ($oAcreedorCuota)
{
	$MontoAAutorizar = $FinanciacionCapital * $oAcreedorCuota->Interes * $oAcreedorCuota->Coeficiente;
	$CuotaAPagar = $MontoAAutorizar / $oAcreedorCuota->Cuotas;
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td height="25" width="75%"><strong>Capital a Financiar:</strong></td>
		<td>$<?= number_format($FinanciacionCapital, 2, ',', '.') ?></td>
	</tr>
	<tr>
		<td height="25"><strong>Acreedor:</strong></td>
		<td><?= $oAcreedor->RazonSocial ?></td>
	</tr>
	<tr>
		<td height="25"><strong>Cuotas:</strong></td>
		<td><?= $Cuotas ?></td>
	</tr>
	<tr>
		<td height="25"><strong>Valor a Autorizar:</strong></td>
		<td>$<?= number_format($MontoAAutorizar, 2, ',', '.') ?></td>
	</tr>
	<tr>
		<td height="25"><strong>Valor Cuotas:</strong></td>
		<td>$<?= number_format($CuotaAPagar, 2, ',', '.') ?></td>
	</tr>
</table>
<?php
}
else
{
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td height="25" width="75%"><strong>No se encontraron opciones para el plazo de financiaci&oacute;n</strong></td>
	</tr>
</table>
<?php
}
?>