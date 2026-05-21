<?php

require_once('../library/class.articulo.php');
require_once('../library/class.articulos.php');
require_once('../library/class.pedidosrepuestos.php');
require_once('../library/class.pedidosrepuestosdetalles.php');


class ModuleArticulos
{
	function GetById(array $array)
	{
		$Articulos = new Articulos();

		return $Articulos->GetById($array['IdArticulo']);
	}
	
	function GetByCodigo(array $array)
	{
		$Articulos = new Articulos();

		return $Articulos->GetByCodigo($array['Codigo']);
	}
	
	function GetAll(array $array)
	{
		$Articulos = new Articulos();
		$oPage 	= new Page($array['CurrentPage']);
		
		$filter = array();		
		$filter['Descripcion'] = $array['Filter_Descripcion'];
		
		return $Articulos->GetAll($filter, $oPage);
	}
	
	function GetPedidosRepuestos(array $array)
	{
		$oArticulos					= new Articulos();
		$oPedidosRepuestos 			= new PedidosRepuestos();
		$oPedidosRepuestosDetalles	= new PedidosRepuestosDetalles();
			
		$IdArticulo = $array['IdArticulo'];
		
		$oArticulo = $oArticulos->GetById($IdArticulo);
		
		$arrPedidosRepuestosDetalles = $oPedidosRepuestosDetalles->GetAllPendientesByArticulo($oArticulo);
		$result = array('Resultado' => '');
		
		if ($arrPedidosRepuestosDetalles)
		{
			$strResult = "El repuesto ingresado es solicitado por los siguientes pedidos de repuestos:";
			
			foreach ($arrPedidosRepuestosDetalles as $oPedidoRepuestoDetalle)
			{
				$oPedidoRepuesto = $oPedidosRepuestos->GetById($oPedidoRepuestoDetalle->IdPedidoRepuesto);
				
				$strResult.= "*- Pedido Nro. " . $oPedidoRepuestoDetalle->IdPedidoRepuesto;
				if ($oPedidoRepuesto->IdOrdenTrabajo)
					$strResult.= " / OT: " . $oPedidoRepuesto->IdOrdenTrabajo . ' / Dominio: ' . $oPedidoRepuesto->Dominio;
				else
					$strResult.= " / Pedido para stock";
			}
			$result = array('Resultado' => $strResult);
		}
		
		return $result;
	}
}

?>