<?php

require_once('class.db.php');
require_once('class.dbaccess.php');


class ArticuloStock
{
	public $IdArticuloStock;
	public $IdArticulo;
	public $IdUbicacion;
	public $Ubicacion;
	public $StockInicial;
	public $StockActual;	

	public function __construct()
	{
		
	}	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdArticuloStock 		= $arr['IdArticuloStock'];
		$this->IdArticulo			= $arr['IdArticulo'];
		$this->IdUbicacion			= $arr['IdUbicacion'];
		$this->Ubicacion 			= stripslashes($arr['Ubicacion']);
		$this->StockInicial			= $arr['StockInicial'];
		$this->StockActual			= $arr['StockActual'];		
	}
	
	public function AumentarStock($cantidad)
	{
		$this->StockActual += $cantidad;
	}
	
	public function DisminuirStock($cantidad)
	{
		$this->StockActual -= $cantidad;
	}
	
	public function StockMovido()
	{
		return ($this->StockInicial - $this->StockActual);
	}
}

?>