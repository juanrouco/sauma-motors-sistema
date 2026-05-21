<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.proveedores.php');

class Cheque extends DBAccess 
{
	public $IdCheque;
	public $Fecha;
	public $NumeroCheque;
	public $Banco;
	public $FechaEmision;
	public $FechaDeposito;
	public $Importe;
	public $Observaciones;
	public $IdProveedor;
	public $IdFacturaCompra;
	public $Pago;
	public $NumeroFactura;
	
	public function __construct()
	{
		$this->IdCheque				= '';
		$this->Fecha				= '';
		$this->NumeroCheque			= '';
		$this->Banco				= '';
		$this->FechaEmision 		= '';
		$this->FechaDeposito		= '';
		$this->Importe 				= '';
		$this->Observaciones 		= '';
		$this->IdProveedor 			= '';
		$this->IdFacturaCompra 		= '';
		$this->Pago			 		= '';
		$this->NumeroFactura	 	= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdCheque				= $arr['IdCheque'];
		$this->Fecha				= $arr['Fecha'];
		$this->NumeroCheque			= $arr['NumeroCheque'];
		$this->Banco				= $arr['Banco'];
		$this->FechaEmision 		= $arr['FechaEmision'];
		$this->FechaDeposito		= $arr['FechaDeposito'];
		$this->Importe 				= $arr['Importe'];
		$this->Observaciones 		= $arr['Observaciones'];
		$this->IdProveedor 			= $arr['IdProveedor'];
		$this->IdFacturaCompra 		= $arr['IdFacturaCompra'];
		$this->Pago			 		= $arr['Pago'];
		$this->NumeroFactura	 	= $arr['NumeroFactura'];
	}
	
	public function GetProveedor()
	{
		$oProveedores = new Proveedores();
		return $oProveedores->GetById($this->IdProveedor);
	}
}
?>
