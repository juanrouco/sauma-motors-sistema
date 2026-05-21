<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.pedidosmayoristadetalles.php');
require_once('class.pagosmayorista.php');
require_once('class.minutas.php');

class PedidoMayorista
{
	public $IdPedidoMayorista;
	public $IdCliente;
	public $FechaPedidoMayorista;
	public $Observaciones;
	public $IdEstado;
	
	
	public function __construct()
	{
		$this->IdPedidoMayorista 	= '';
		$this->IdCliente			= '';
		$this->FechaPedidoMayorista = '';
		$this->Observaciones 		= '';
		$this->IdEstado 			= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdPedidoMayorista 	= $arr['IdPedidoMayorista'];
		$this->IdCliente			= $arr['IdCliente'];
		$this->FechaPedidoMayorista = $arr['FechaPedidoMayorista'];
		$this->Observaciones 		= $arr['Observaciones'];
		$this->IdEstado 			= $arr['IdEstado'];
	}
	
	
	public function GetAllDetalles()
	{
		$PedidosMayoristaDetalles = new PedidosMayoristaDetalles();
		
		return $PedidosMayoristaDetalles->GetAllByPedidoMayorista($this);
	}
	
	public function GetCostoTotal()
	{
		$arrPedidosMayoristaDetalles = $this->GetAllDetalles();
		$oMinutas = new Minutas();
		
		$Total = 0;
		foreach ($arrPedidosMayoristaDetalles as $oPedidoMayoristaDetalle)
		{
			$oMinuta = $oMinutas->GetById($oPedidoMayoristaDetalle->IdMinuta);
			$Total += $oMinuta->GetCostoTotal();
		}
		return $Total;
	}
	
	public function GetTotalAsignadoLibre()
	{
		$oPagosMayorista = new PagosMayorista();
		
		$arrPagosMayorista = $oPagosMayorista->GetByIdPedidoMayorista($this->IdPedidoMayorista);
		$Total = 0;
		foreach ($arrPagosMayorista as $oPagoMayorista)
		{	
			$Total += $oPagoMayorista->Importe - $oPagoMayorista->ImporteAsignado;
		}
		
		return $Total;
	}
	
	public function GetTotalAcreditado()
	{
		$arrPedidosMayoristaDetalles = $this->GetAllDetalles();
		$oMinutas = new Minutas();
		
		$Total = 0;
		foreach ($arrPedidosMayoristaDetalles as $oPedidoMayoristaDetalle)
		{
			$oMinuta = $oMinutas->GetById($oPedidoMayoristaDetalle->IdMinuta);
			$Total += $oMinuta->GetTotalAcreditado();
		}
		$Total += $this->GetTotalAsignadoLibre();
		return $Total;
	}
	
	public function GetTotalPendiente()
	{
		$arrPedidosMayoristaDetalles = $this->GetAllDetalles();
		$oMinutas = new Minutas();
		
		$Total = 0;
		foreach ($arrPedidosMayoristaDetalles as $oPedidoMayoristaDetalle)
		{
			$oMinuta = $oMinutas->GetById($oPedidoMayoristaDetalle->IdMinuta);
			$Total += $oMinuta->GetTotalPendiente();
		}
		$Total -= $this->GetTotalAsignadoLibre();
		return $Total;
	}
	
	public function GetCountUnidades()
	{
		return count($this->GetAllDetalles());
	}
}

?>