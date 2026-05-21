<?php
require_once('../inc_library_includes.php');
header('Content-type: application/json; charset=utf8');
$IdTurno = intval($_REQUEST['IdTurno']);

$oTurnos 			= new Turnos();
$oEstadosOrden 		= new EstadosOrden();
$oTallerUnidades	= new TallerUnidades();
$oClientes	 		= new Clientes();
$oTurnosComentarios = new TurnosComentarios();

$oTurno 		= $oTurnos->GetById($IdTurno);
$oTallerUnidad 	= $oTallerUnidades->GetById($oTurno->IdTallerUnidad);
$oCliente		= $oClientes->GetById($oTallerUnidad->IdCliente);
$oEstadoOrden 	= $oEstadosOrden->GetById($oTurno->IdEstadoOrden);
$arrTareas 		= $oTurno->GetAllTareas();

$Estado = $oEstadoOrden->Nombre;
if ($oTurno->IdEstadoOrden == EstadoOrden::Rechazado)
{
	$oComentarioRechazo = $oTurnosComentarios->GetRechazoByIdTurno($oTurno->IdTurno);
	$oTipoRechazo = TiposRechazos::GetById($oComentarioRechazo->IdTipoRechazo);
	$Estado.= ' - ' . $oTipoRechazo['Nombre'] . ': ' . $oComentarioRechazo->Comentarios;
}

$strTareas = '';
foreach ($arrTareas as $oTarea)
{
	$strTareas.= ' - ' . utf8_encode($oTarea->Titulo);
}
	
$ordenes[] = array(
	'IdTurno' => $oTurno->IdTurno,
	'FechaInicio' => CambiarFechaHora($oTurno->FechaInicio),
	'FechaFin' => CambiarFechaHora($oTurno->FechaFin),
	'Cliente' => array(
		'RazonSocial' => utf8_decode($oCliente->RazonSocial),
		'Telefono' => ($oCliente->TelefonoCodigoArea ? $oCliente->TelefonoCodigoArea . '-' : '') . $oCliente->Telefono,
		'Email' =>$oCliente->Email
	),
	'Estado' => $Estado,
	'Modelo' => $oTallerUnidad->Modelo,
	'Tipo' => $oTurno->Bahia ? 'BAHIA' : 'REGULAR',
	'Reconfirmado' => $oTurno->Reconfirmado ? 'SI' : 'NO',
	'Remis' => $oTurno->Remis ? 'SI' : 'NO',
	'Tareas' => $strTareas
);

echo json_encode($ordenes);

?>
