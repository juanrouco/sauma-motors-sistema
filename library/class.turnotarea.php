<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.articulos.php');
require_once('class.ivas.php');

class TurnoTarea
{
	const IdEstadoActivo = 1;
	const IdEstadoFinalizado = 2;
	const IdEstadoReabierto = 3;

	public $IdTurnoTarea;
	public $IdTurno;
	public $Importe;
	public $Titulo;
	public $Descripcion;
	public $HorasEstimadas;
	public $IdTareaTrabajo;
	public $IdTipoVenta;
	public $IdEstado;
	public $IdCodigoTrabajo;
	public $IdFacturaCompra;
	
	public function __construct()
	{
		$this->IdTurnoTarea 		= '';
		$this->IdTurno				= '';
		$this->Importe			 	= '';
		$this->Titulo			 	= '';
		$this->Descripcion			= '';
		$this->HorasEstimadas 		= '';	
		$this->IdTareaTrabajo		= '';
		$this->IdTipoVenta			= '';
		$this->IdEstado				= '';
		$this->IdCodigoTrabajo		= '';
		$this->IdFacturaCompra		= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdTurnoTarea			= $arr['IdTurnoTarea'];
		$this->IdTurno				= $arr['IdTurno'];
		$this->Importe		 		= $arr['Importe'];
		$this->Titulo 				= $arr['Titulo'];
		$this->Descripcion			= $arr['Descripcion'];
		$this->HorasEstimadas 		= $arr['HorasEstimadas'];
		$this->IdTareaTrabajo		= $arr['IdTareaTrabajo'];
		$this->IdTipoVenta			= $arr['IdTipoVenta'];
		$this->IdEstado				= $arr['IdEstado'];
		$this->IdCodigoTrabajo		= $arr['IdCodigoTrabajo'];
		$this->IdFacturaCompra		= $arr['IdFacturaCompra'];
	}
	
	public function GetAllArticulos()
	{
		$oTurnosTareasArticulos = new TurnosTareasArticulos();		
		
		return $oTurnosTareasArticulos->GetAllByTurnoTarea($this);
	}
	
	public function ImporteRepuestos()
	{
		$arrRelacionados = $this->GetAllArticulos();
		
		$total = 0;
		foreach ($arrRelacionados as $oRelacion)
		{			
			$total += $oRelacion->PrecioTotal;
		}
		return number_format($total, 2);
	}
	
	public function ImporteSinRepuestos()
	{
		$arrRelacionados = $this->GetAllArticulos();
		
		$total = 0;
		foreach ($arrRelacionados as $oRelacion)
		{			
			$total += $oRelacion->PrecioTotal;
		}
		return $this->Importe - $total;
	}
}

?>