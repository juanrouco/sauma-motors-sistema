<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.cajasmovimientos.php');

class CajaDetalle extends DBAccess 
{
	const CajaChica		= 1;
	const CajaCheques	= 2;
	const BancoGalicia	= 17;
	const BancoPatagonia= 16;
	const BancoHonda	= 5;
	const MercadoPago	= 19;
	const TodoPago		= 18;
	const CajaTaller	= 20;
	const CajaRepuestos	= 21;
	
	public $IdCajaDetalle;
	public $IdCaja;
	public $Nombre;
	public $FechaUltimoMovimiento;
	public $Total;
	
	public function ParseFromArray(array $arr)
	{
		$this->IdCajaDetalle			= $arr['IdCajaDetalle'];
		$this->IdCaja					= $arr['IdCaja'];
		$this->Nombre					= $arr['Nombre'];
		$this->FechaUltimoMovimiento	= $arr['FechaUltimoMovimiento'];
		$this->Total					= $arr['Total'];	
	}	
	
	public function GetAllMovimientos()
	{
		$oCajasMovimientos = new CajasMovimientos();
		
		return $oCajasMovimientos->GetAllByIdCajaDetalle($this->IdCajaDetalle);
	}
}

?>