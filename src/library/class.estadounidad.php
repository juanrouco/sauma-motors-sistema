<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class EstadoUnidad
{
	public $IdEstado;
	public $Codigo;
	public $Nombre;
	public $Color;
	public $Predeterminado;
	
	const Stock 			= 1;
	const Reservado 		= 2;
	const Facturado 		= 3;
	const Referido 			= 4;
	const Plan 				= 5;
	const VentasEspeciales 	= 6;
	const Entregado 		= 7;
	const PreVenta			= 10;
	const PreVentaReservado	= 2;
	const VentaEmpleados	= 20;
	const Vendido			= 21;
	const StockPreventa		= 24;
	const ListoFacturar		= 30;

	
	public function __construct()
	{
		$this->IdEstado 		= '';
		$this->Codigo			= '';
		$this->Nombre 			= '';
		$this->Color 			= '';
		$this->Predeterminado 	= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdEstado 		= $arr['IdEstado'];
		$this->Codigo			= $arr['Codigo'];
		$this->Nombre 			= stripslashes($arr['Nombre']);
		$this->Color 			= stripslashes($arr['Color']);
		$this->Predeterminado	= $arr['Predeterminado'];
	}
	
	
	public function CanDelete()
	{
		if ($this->GetAllUnidades())
			return false;
		
		return true;
	}
	
	
	public function GetAllUnidades()
	{
		$Unidades = new Unidades();
		
		return $Unidades->GetAllByEstado($this);
	}
}

?>