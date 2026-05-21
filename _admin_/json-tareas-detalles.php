<?php
require_once('../inc_library_includes.php');
header('Content-type: application/json');
$IdTarea = intval($_REQUEST['IdTarea']);

$oTareas		 	= new Tareas();
$oEstadosOrden 		= new EstadosOrden();
$oClientes	 		= new Clientes();

$oTarea		 	= $oTareas->GetById($IdTarea);
$oCliente		= $oClientes->GetById($oTarea->IdCliente);
$oEstadoOrden 	= $oEstadosOrden->GetById($oOrdenTrabajo->IdEstadoOrden);
	
$tareas[] = array(
	'Nombre' => $oTarea->Nombre,
	'FechaInicio' => CambiarFechaHora($oTarea->FechaInicio . ' '. $oTarea->Hora),
	'FechaFin' => CambiarFechaHora($oTarea->FechaFin),
	'Cliente' => array(
		'RazonSocial' => $oCliente ? $oCliente->RazonSocial : 'NO POSEE',
		'Telefono' => $oCliente ? (($oCliente->TelefonoCodigoArea ? $oCliente->TelefonoCodigoArea . '-' : '') . $oCliente->Telefono ) : '',
		'Email' => $oCliente ? $oCliente->Email : ''
	),
	'Estado' => TareaEstados::GetById($oTarea->IdEstado),
	'IdTarea' => $oTarea->IdTarea,
	'Descripcion' => $oTarea->Descripcion
);

echo json_encode($tareas);

?>
