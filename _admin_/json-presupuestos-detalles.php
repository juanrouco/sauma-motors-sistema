<?php
require_once('../inc_library_includes.php');
header('Content-type: application/json');
$IdPresupuesto = intval($_REQUEST['IdPresupuesto']);

$oPresupuestos		 	= new Presupuestos();
$oModelos		 		= new Modelos();
$oClientes	 			= new Clientes();

$oPresupuesto	= $oPresupuestos->GetById($IdPresupuesto);
$oCliente		= $oClientes->GetById($oPresupuesto->IdCliente);
$oModelo	 	= $oModelos->GetById($oPresupuesto->IdModelo);
	
$tareas[] = array(
	'FechaVencimiento' => CambiarFecha($oPresupuesto->FechaVencimiento),
	'Cliente' => array(
		'RazonSocial' => $oCliente ? $oCliente->RazonSocial : 'NO POSEE',
		'Telefono' => $oCliente ? (($oCliente->TelefonoCodigoArea ? $oCliente->TelefonoCodigoArea . '-' : '') . $oCliente->Telefono ) : '',
		'Email' => $oCliente ? $oCliente->Email : ''
	),
	'Modelo' => array(
		'DenominacionComercial' => $oModelo->DenominacionComercial
	),
	'Estado' => PresupuestoEstados::GetById($oPresupuesto->IdEstado),
	'IdPresupuesto' => $oPresupuesto->IdPresupuesto,
	'Observaciones' => $oPresupuesto->Observaciones ? $oPresupuesto->Observaciones : ''
);

echo json_encode($tareas);

?>
