<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class FacturaPostVentaDetalle extends DBAccess 
{
	public $IdFacturaPostVentaDetalle;
	public $IdFacturaPostVenta;
	public $IdArticulo;
	public $IdOrdenTrabajoTarea;
	public $Detalle;
	public $ImporteIva10;
	public $ImporteIva21;
	public $ImporteUnidad;
	public $ImporteNeto;
	public $Cantidad;
	public $ImporteBruto;
	

	public function __construc()
	{
		$this->IdFacturaPostVentaDetalle	= '';
		$this->IdFacturaPostVenta		 	= '';
		$this->IdArticulo				 	= '';
		$this->IdOrdenTrabajoTarea		 	= '';
		$this->Detalle 						= '';
		$this->ImporteIva10				 	= '';
		$this->ImporteIva21			 		= '';
		$this->ImporteUnidad		 		= '';
		$this->ImporteNeto			 		= '';
		$this->Cantidad				 		= '';
		$this->ImporteBruto			 		= '';
	}

	
	public function ParseFromArray(array $arr)
	{
		$this->IdFacturaPostVentaDetalle	= $arr['IdFacturaPostVentaDetalle'];
		$this->IdFacturaPostVenta		 	= $arr['IdFacturaPostVenta'];
		$this->IdArticulo				 	= $arr['IdArticulo'];
		$this->IdOrdenTrabajoTarea		 	= $arr['IdOrdenTrabajoTarea'];
		$this->Detalle 						= stripslashes($arr['Detalle']);
		$this->ImporteIva10				 	= $arr['ImporteIva10'];
		$this->ImporteIva21 				= $arr['ImporteIva21'];
		$this->ImporteUnidad 				= $arr['ImporteUnidad'];
		$this->ImporteNeto	 				= $arr['ImporteNeto'];
		$this->Cantidad		 				= $arr['Cantidad'];
		$this->ImporteBruto	 				= $arr['ImporteBruto'];
	}
}

?>
