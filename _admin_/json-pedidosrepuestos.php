<?php

require_once('../inc_library_includes.php');
header('Content-type: application/json; charset=utf8');
$IdPedidoRepuesto = intval($_REQUEST['IdPedidoRepuesto']);

$oPedidosRepuestos 	= new PedidosRepuestos();
$oEstadosOrden 		= new EstadosOrden();
$oTallerUnidades	= new TallerUnidades();
$oClientes	 		= new Clientes();
$oArticulos			= new Articulos();
$oOrdenTrabajoComentarios = new OrdenTrabajoComentarios();

$oPedidoRepuesto = $oPedidosRepuestos->GetById($IdPedidoRepuesto);
$oSector = SectoresPostVenta::GetById($oPedidoRepuesto->IdSector);
$oModalidad = Modalidades::GetById($oPedidoRepuesto->IdModalidad);

$arrDetalles = $oPedidoRepuesto->GetAllDetalles();
$str = '';
foreach ($arrDetalles as $oDetalle)
{	
	$oArticulo = $oArticulos->GetById($oDetalle->IdArticulo);
	$str.= ' - ' . utf8_encode('[' . $oArticulo->Codigo . '] ' . $oArticulo->Descripcion);
}
	
$ordenes[] = array(
	'IdPedidoRepuesto' => $oPedidoRepuesto->IdPedidoRepuesto,
	'Fecha' => CambiarFecha($oPedidoRepuesto->Fecha),
	'Sector' => $oSector['Nombre'],
	'IdOrdenTrabajo' => $oPedidoRepuesto->IdOrdenTrabajo ? $oPedidoRepuesto->IdOrdenTrabajo : '',
	'Dominio' => $oPedidoRepuesto->Dominio,
	'Modalidad' => $oModalidad['Nombre'],
	'Aprobado' => $oPedidoRepuesto->Aprobado ? 'SI' : 'NO',
	'Pedido' => $oPedidoRepuesto->Pedido() ? 'SI' : 'NO',
	'Recibido' => $oPedidoRepuesto->Recibido() ? 'SI' : 'NO',
	'Vencido' => $oPedidoRepuesto->Vencido() ? 'SI' : 'NO',
	'Costo' => $oPedidoRepuesto->Costo(),
	'Repuestos' => $str
);

echo json_encode($ordenes);

?>
