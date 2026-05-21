<?php

require_once('../inc_library_includes.php');

$oUsuario = Session::GetCurrentUser();

$FechaDesde = date('d-m-Y', intval($_REQUEST['start']));
$FechaHasta = date('d-m-Y', intval($_REQUEST['end']));

$oTareas		 	= new Tareas();
$oTareaSeguimientos	= new TareaSeguimientos();
$oPresupuestos		= new Presupuestos();
$oClientes			= new Clientes();
$oModelos			= new Modelos();

$filter = array();
$filter['FechaInicioDesde'] = $FechaDesde;
$filter['FechaInicioHasta'] = $FechaHasta;
$filter['IdUsuarioTo'] 		= $oUsuario->IdUsuario;
$arrTareas = $oTareas->GetAll($filter);

$filter['SeguimientoRealizado']	= 0;
$arrTareasSeguimientos = $oTareaSeguimientos->GetAll($filter);

$filter	= array();
if ($oUsuario->IdPerfil != 1)
{
	if ($oUsuario->IdUsuario == 2 || $oUsuario->IdUsuario == 5)
		$filter['arrIdUsuario'] = array(2, 5);
	else
		$filter['IdUsuario'] = $oUsuario->IdUsuario;
}
$filter['FechaVencimientoDesde'] 	= $FechaDesde;
$filter['FechaVencimientoHasta'] 	= $FechaHasta;
$filter['IdEstado'] 				= PresupuestoEstados::Pendiente;
$arrPresupuestos = $oPresupuestos->GetAll($filter);

$tareas = array();

foreach ($arrTareas as $oTarea)
{
	//if ($oOrdenTrabajo->Estado ==)
	
	$fechaFin = new DateTime($oTarea->FechaInicio . ' ' . $oTarea->Hora);
	$fechaFin->modify("+15 minutes");
	
	$tareas[] = array(
			'id' => 'T' . $oTarea->IdTarea,
			'title' => $oTarea->Nombre,
			'start' => $oTarea->FechaInicio . ' ' . $oTarea->Hora,
			'end' => $fechaFin->format('Y-m-d H:i:s'),
			'allDay' => false,
			'url' => "tareas_descripcion.php?IdTarea=" . $oTarea->IdTarea,
			'color' => TareaEstados::GetOnlyColorById($oTarea->IdEstado),
			'borderColor' => '#ffffff'
	);
}

foreach ($arrTareasSeguimientos as $oSegumiento)
{
	//if ($oOrdenTrabajo->Estado ==)
	
	$fechaFin = new DateTime($oSegumiento->Fecha);
	$fechaFin->modify("+15 minutes");
	$oPresupuesto	= $oPresupuestos->GetById($oSegumiento->IdTarea);
	
	$tareas[] = array(
			'id' => 'P' . $oPresupuesto->IdPresupuesto,
			'title' => SeguimientoEstados::GetById($oSegumiento->IdAccion),
			'start' => $oSegumiento->Fecha,
			'end' => $fechaFin->format('Y-m-d H:i:s'),
			'allDay' => false,
			'url' => "tareas_descripcion.php?IdPresupuesto=" . $oPresupuesto->IdPresupuesto,
			'color' => $oSegumiento->SeguimientoRealizado ? PresupuestoEstados::GetOnlyColorById(PresupuestoEstados::Ganado) : PresupuestoEstados::GetOnlyColorById(PresupuestoEstados::Perdido),
			'borderColor' => '#ffffff'
	);
}

$count = 0;
foreach ($arrPresupuestos as $oPresupuesto)
{
	//if ($oOrdenTrabajo->Estado ==)
	
	$fechaFin = new DateTime($oPresupuesto->FechaVencimiento . ' ' . (9 + $count + 1) . ':00');
	$oCliente = $oClientes->GetById($oPresupuesto->IdCliente);
	$oModelo = $oModelos->GetById($oPresupuesto->IdModelo);
	
	$tareas[] = array(
			'id' => 'P' . $oPresupuesto->IdPresupuesto,
			'title' => $oCliente->RazonSocial . ' - ' . $oModelo->DenominacionComercial,
			'start' => $oPresupuesto->FechaVencimiento . ' ' . (9 + $count) . ':00',
			'end' => $fechaFin->format('Y-m-d H:i:s'),
			'allDay' => false,
			'url' => "presupuestos_detail.php?IdPresupuesto=" . $oPresupuesto->IdPresupuesto,
			'color' => PresupuestoEstados::GetOnlyColorById($oPresupuesto->IdEstado),
			'borderColor' => '#ffffff'
	);
	$count++;
}
	

echo json_encode($tareas);

?>
