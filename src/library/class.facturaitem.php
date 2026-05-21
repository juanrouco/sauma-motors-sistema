<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.articulos.php');
require_once('class.ivas.php');

class FacturaItem extends DBAccess 
{
	public $IdFacturaItem;
	public $IdFactura;
	public $Descripcion;	
	public $Cantidad;	
	public $ImporteNeto;	
	public $ImporteBruto;
	public $IdIva;
	public $IvaAlicuota;
	public $Iva21;
	public $Iva10;
	public $IdArticulo;
	public $IdTarea;
	public $Interes;

	public function __construct()
	{
		$this->IdFacturaItem		= '';
		$this->IdFactura			= '';
		$this->Descripcion			= '';
		$this->Cantidad 			= '';		
		$this->ImporteNeto		 	= '';
		$this->ImporteBruto		 	= '';
		$this->IdIva			 	= '';
		$this->IvaAlicuota		 	= '';
		$this->Iva21			 	= '';
		$this->Iva10			 	= '';
		$this->IdArticulo		 	= '';
		$this->IdTarea			 	= '';
		$this->Interes			 	= '';
	}

	
	public function ParseFromArray(array $arr)
	{
		$this->IdFacturaItem		= $arr['IdFacturaItem'];
		$this->IdFactura			= $arr['IdFactura'];
		$this->Descripcion	 		= $arr['Descripcion'];
		$this->Cantidad 			= $arr['Cantidad'];
		$this->ImporteNeto		 	= $arr['ImporteNeto'];
		$this->ImporteBruto 		= $arr['ImporteBruto'];
		$this->IdIva		 		= $arr['IdIva'];
		$this->IvaAlicuota	 		= $arr['IvaAlicuota'];
		$this->Iva21		 		= $arr['Iva21'];
		$this->Iva10		 		= $arr['Iva10'];
		$this->IdArticulo	 		= $arr['IdArticulo'];
		$this->IdTarea		 		= $arr['IdTarea'];
		$this->Interes		 		= $arr['Interes'];
	}
}

?>
