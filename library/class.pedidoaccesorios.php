<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.pedidosaccesoriositems.php');

class PedidoAccesorios
{
	public $IdPedido;
	public $IdMinuta;
	public $IdMinutaUsado;
	public $Fecha;
	public $Accesorios;


	public function __construct()
	{
		$this->IdPedido			= '';
		$this->IdMinuta			= '';
		$this->IdMinutaUsado	= '';
		$this->Fecha			= '';
		$this->Accesorios		= '';
	}

	
	public function ParseFromArray(array $arr)
	{
		$this->IdPedido			= $arr['IdPedido'];
		$this->IdMinuta			= $arr['IdMinuta'];
		$this->IdMinutaUsado	= $arr['IdMinutaUsado'];
		$this->Fecha			= $arr['Fecha'];
		$this->Accesorios		= stripslashes($arr['Accesorios']);
	}
	
	public function GetAllItems()
	{
		$oPedidosAccesoriosItems = new PedidosAccesoriosItems();
		
		return $oPedidosAccesoriosItems->GetAllByPedidoAccesorio($this);
	}
	
	public function GetCosto()
	{
		$oPedidosAccesoriosItems = new PedidosAccesoriosItems();
		
		$arrItems =  $oPedidosAccesoriosItems->GetAllByPedidoAccesorio($this);
		
		$Total = 0;
		
		if ($arrItems);
		{
			foreach ($arrItems as $Item)
			{
				$Total+= $Item->Importe * 1.21;
			}
		}
		
		return $Total;
	}
}

?>