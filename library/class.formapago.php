<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.planescuotas.php');

class FormaPago
{
	public $IdFormaPago;
	public $Nombre;
	public $Disponible;
	
	const Efectivo 		= 1;
	const Visa 			= 2;
	const AMEX 			= 3;
	const Cheque 		= 4;
	const Transf 		= 5;
	const MercadoPago 	= 6;
	
	public function __construct()
	{
		$this->IdFormaPago 	= '';
		$this->Nombre 		= '';
		$this->Disponible 	= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdFormaPago 	= $arr['IdFormaPago'];
		$this->Nombre 		= stripslashes($arr['Nombre']);
		$this->Disponible	= $arr['Disponible'];
	}

	
	public function GetAllPlanesCuotas()
	{
		$oPlanesCuotas = new PlanesCuotas();
		
		return $oPlanesCuotas->GetAllByFormaPago($this);
	}	
}

?>