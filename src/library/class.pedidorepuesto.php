<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.pedidosrepuestosdetalles.php');

class PedidoRepuesto
{
	
	public $IdPedidoRepuesto;
	public $Fecha;
	public $IdUsuario;
	public $IdSector;
	public $IdOrdenTrabajo;
	public $Dominio;
	public $IdModalidad;
	public $Aprobado;
	public $IdUsuarioAprobado;
	public $FechaVencimiento;
	public $FechaAprobado;
	public $FechaPedido;
	public $IdUsuarioGenerador;
	public $IdUsuarioPedido;
	
	
	public function __construct()
	{
		$this->IdPedidoRepuesto 	= '';
		$this->Fecha				= '';
		$this->IdUsuario 			= '';
		$this->IdSector 			= '';
		$this->IdOrdenTrabajo 		= '';
		$this->Dominio		 		= '';
		$this->IdModalidad	 		= '';
		$this->Aprobado		 		= '';
		$this->IdUsuarioAprobado	= '';
		$this->FechaVencimiento		= '';
		$this->FechaAprobado		= '';
		$this->FechaPedido			= '';
		$this->IdUsuarioGenerador	= '';
		$this->IdUsuarioPedido		= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdPedidoRepuesto 	= $arr['IdPedidoRepuesto'];
		$this->Fecha				= $arr['Fecha'];
		$this->IdUsuario 			= $arr['IdUsuario'];
		$this->IdSector 			= $arr['IdSector'];
		$this->IdOrdenTrabajo 		= $arr['IdOrdenTrabajo'];
		$this->Dominio		 		= $arr['Dominio'];
		$this->IdModalidad		 	= $arr['IdModalidad'];
		$this->Aprobado			 	= $arr['Aprobado'];
		$this->IdUsuarioAprobado 	= $arr['IdUsuarioAprobado'];
		$this->FechaVencimiento 	= $arr['FechaVencimiento'];
		$this->FechaAprobado 		= $arr['FechaAprobado'];
		$this->FechaPedido		 	= $arr['FechaPedido'];
		$this->IdUsuarioGenerador 	= $arr['IdUsuarioGenerador'];
		$this->IdUsuarioPedido		 = $arr['IdUsuarioPedido'];
	}
	
	public function GetAllDetalles()
	{
		$oPedidosRepuestosDetalles = new PedidosRepuestosDetalles();
		
		return $oPedidosRepuestosDetalles->GetAllByPedidoRepuesto($this);
	}
	
	public function Pedido()
	{
		$arrPedidosRepuestosDetalles = $this->GetAllDetalles();
		
		foreach ($arrPedidosRepuestosDetalles as $oPedidoRepuestoDetalle)
		{
			if ($oPedidoRepuestoDetalle->FechaPedido)
				return true;
		}
		return false;
	}
	
	public function Recibido()
	{
		$arrPedidosRepuestosDetalles = $this->GetAllDetalles();
		
		foreach ($arrPedidosRepuestosDetalles as $oPedidoRepuestoDetalle)
		{
			if (!$oPedidoRepuestoDetalle->Recibido)
				return false;
		}
		return true;
	}
	
	public function Parcial()
	{
		$arrPedidosRepuestosDetalles = $this->GetAllDetalles();
		$resultado = '<span style="color:green">SI</span>';
		$count = 0;
		foreach ($arrPedidosRepuestosDetalles as $oPedidoRepuestoDetalle)
		{
			if (!$oPedidoRepuestoDetalle->Recibido)
				$count++;
		}
		if ($count == count($arrPedidosRepuestosDetalles))
			return false;
		elseif ($count > 0)
			return true;
		return false;
	}
	
	public function RecibidoTexto()
	{
		$arrPedidosRepuestosDetalles = $this->GetAllDetalles();
		$resultado = '<span style="color:green">SI</span>';
		$count = 0;
		foreach ($arrPedidosRepuestosDetalles as $oPedidoRepuestoDetalle)
		{
			if (!$oPedidoRepuestoDetalle->Recibido)
				$count++;
		}
		if ($count == count($arrPedidosRepuestosDetalles))
			$resultado = '<span style="color:red">NO</span>';
		elseif ($count > 0)
			$resultado = '<span style="color:red">PARCIAL</span>';
			
		return $resultado;
	}
	
	public function Vencido()
	{
		$arrPedidosRepuestosDetalles = $this->GetAllDetalles();
		
		foreach ($arrPedidosRepuestosDetalles as $oPedidoRepuestoDetalle)
		{
			if ($oPedidoRepuestoDetalle->FechaVencimiento && $oPedidoRepuestoDetalle->FechaVencimiento < date('Y-m-d H:i:s') && !$oPedidoRepuestoDetalle->Recibido)
				return true;
		}
		return false;
	}
	
	public function FechaVencido()
	{
		$arrPedidosRepuestosDetalles = $this->GetAllDetalles();
		$FechaVencido = date('Y-m-d H:i:s');
		
		foreach ($arrPedidosRepuestosDetalles as $oPedidoRepuestoDetalle)
		{
			if ($oPedidoRepuestoDetalle->FechaVencimiento < $FechaVencido && !$oPedidoRepuestoDetalle->Recibido)
				$FechaVencido = $oPedidoRepuestoDetalle->FechaVencimiento;
		}
		return $FechaVencido;
	}
	
	public function Costo()
	{
		$arrPedidosRepuestosDetalles = $this->GetAllDetalles();
		
		$Costo = 0;
		foreach ($arrPedidosRepuestosDetalles as $oPedidoRepuestoDetalle)
		{
			$Costo += $oPedidoRepuestoDetalle->Precio * $oPedidoRepuestoDetalle->Cantidad;
		}
		return $Costo;
	}
}

?>