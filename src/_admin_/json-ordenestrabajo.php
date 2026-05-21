<?php
require_once('../inc_library_includes.php');
header('Content-type: application/json; charset=utf8');
$IdOrdenTrabajo = intval($_REQUEST['IdOrdenTrabajo']);

$oOrdenesTrabajo 			= new OrdenesTrabajo();
$oEstadosOrden 		= new EstadosOrden();
$oTallerUnidades	= new TallerUnidades();
$oClientes	 		= new Clientes();
$oOrdenTrabajoComentarios = new OrdenTrabajoComentarios();

$oOrdenTrabajo 	= $oOrdenesTrabajo->GetById($IdOrdenTrabajo);
$oTallerUnidad 	= $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
$oCliente		= $oClientes->GetById($oTallerUnidad->IdCliente);
$oEstadoOrden 	= $oEstadosOrden->GetById($oOrdenTrabajo->IdEstadoOrden);
$arrTareas 		= $oOrdenTrabajo->GetAllTareas();

$Estado = $oEstadoOrden->Nombre;
if ($oOrdenTrabajo->IdEstadoOrden == EstadoOrden::Rechazado)
{
	$oComentarioRechazo = $oOrdenTrabajoComentarios->GetRechazoByIdOrdenTrabajo($oOrdenTrabajo->IdOrdenTrabajo);
	$oTipoRechazo = TiposRechazos::GetById($oComentarioRechazo->IdTipoRechazo);
	$Estado.= ' - ' . $oTipoRechazo['Nombre'] . ': ' . $oComentarioRechazo->Comentarios;
}

$strTareas = '';
foreach ($arrTareas as $oTarea)
{
	$strTareas.= ' - ' . utf8_encode($oTarea->Titulo);
}
	
$ordenes[] = array(
	'IdOrdenTrabajo' => $oOrdenTrabajo->IdOrdenTrabajo,
	'FechaInicio' => CambiarFechaHora($oOrdenTrabajo->FechaInicio),
	'FechaFin' => CambiarFechaHora($oOrdenTrabajo->FechaFin),
	'Cliente' => array(
		'RazonSocial' => utf8_decode($oCliente->RazonSocial),
		'Telefono' => ($oCliente->TelefonoCodigoArea ? $oCliente->TelefonoCodigoArea . '-' : '') . $oCliente->Telefono,
		'Email' =>$oCliente->Email
	),
	'Estado' => $Estado,
	'Modelo' => $oTallerUnidad->Modelo,
	'Tipo' => $oOrdenTrabajo->Bahia ? 'BAHIA' : 'REGULAR',
	'Tareas' => $strTareas
);

echo json_encode($ordenes);

?>
