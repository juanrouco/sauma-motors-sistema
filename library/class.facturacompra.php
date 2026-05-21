<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class FacturaCompra
{
	public $IdFacturaCompra;
	public $IdComprobanteTipo;
	public $IdProveedor;
	public $Cuit;
	public $Numero;
	public $Fecha;
	public $ImporteNeto;
	public $Iva10;
	public $Iva21;
	public $Iva27;
	public $PercepcionIva;
	public $PercepcionIB;
	public $PercepcionGanancias;
	public $NoGrabados;
	public $ImpuestoInterno;
	public $ImpuestoInternoD;
	public $Total;
	public $IdTipoCargo;
	public $IdConcepto;
	public $Reportado;
	public $IdUnidad;
	public $IdPeriodo;
	
	public function __construct()
	{
		$this->IdFacturaCompra 		= '';
		$this->IdComprobanteTipo	= '';
		$this->IdProveedor 			= '';
		$this->Cuit 				= '';
		$this->Numero	 			= '';
		$this->Fecha		 		= '';
		$this->ImporteNeto	 		= '';
		$this->Iva10		 		= '';
		$this->Iva21		 		= '';
		$this->Iva27		 		= '';
		$this->PercepcionIva 		= '';
		$this->PercepcionIB	 		= '';
		$this->PercepcionGanancias	= '';
		$this->NoGrabados	 		= '';
		$this->ImpuestoInterno 		= '';
		$this->ImpuestoInternoD		= '';
		$this->Total		 		= '';
		$this->IdTipoCargo			= '';
		$this->IdConcepto			= '';
		$this->Reportado			= '';
		$this->IdUnidad				= '';
		$this->IdPeriodo			= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdFacturaCompra 		= $arr['IdFacturaCompra'];
		$this->IdComprobanteTipo	= $arr['IdComprobanteTipo'];
		$this->IdProveedor 			= $arr['IdProveedor'];		
		$this->Cuit		 			= $arr['Cuit'];
		$this->Numero	 			= $arr['Numero'];
		$this->Fecha		 		= $arr['Fecha'];
		$this->ImporteNeto	 		= $arr['ImporteNeto'];
		$this->Iva10		 		= $arr['Iva10'];
		$this->Iva21		 		= $arr['Iva21'];
		$this->Iva27		 		= $arr['Iva27'];
		$this->PercepcionIva 		= $arr['PercepcionIva'];
		$this->PercepcionIB	 		= $arr['PercepcionIB'];
		$this->PercepcionGanancias	= $arr['PercepcionGanancias'];
		$this->NoGrabados	 		= $arr['NoGrabados'];
		$this->ImpuestoInterno 		= $arr['ImpuestoInterno'];
		$this->ImpuestoInternoD		= $arr['ImpuestoInternoD'];
		$this->Total		 		= $arr['Total'];
		$this->IdTipoCargo	 		= $arr['IdTipoCargo'];
		$this->IdConcepto			= $arr['IdConcepto'];
		$this->Reportado			= $arr['Reportado'];
		$this->IdUnidad				= $arr['IdUnidad'];
		$this->IdPeriodo			= $arr['IdPeriodo'];
	}
}

?>