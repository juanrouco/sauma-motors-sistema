<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.articulos.php');
require_once('class.ivas.php');
require_once('class.compras.php');

class OrdenTrabajoTarea
{
	const IdEstadoActivo = 1;
	const IdEstadoFinalizado = 2;
	const IdEstadoReabierto = 3;

	public $IdOrdenTrabajoTarea;
	public $IdOrdenTrabajo;
	public $Importe;
	public $Titulo;
	public $Descripcion;
	public $HorasEstimadas;
	public $IdTareaTrabajo;
	public $IdTipoVenta;
	public $IdEstado;
	public $IdCodigoTrabajo;
	public $IdFacturaCompra;
	public $Tarea;
	public $IdCategoria;
	public $Agrupar;
	public $TotalMO;
	public $TotalRepuestos;
	public $IdVendedor;
	public $IdProveedor;
	public $CostoTotal;
	public $Terceros;
	
	public function __construct()
	{
		$this->IdOrdenTrabajoTarea 	= '';
		$this->IdOrdenTrabajo		= '';
		$this->Importe			 	= '';
		$this->Titulo			 	= '';
		$this->Descripcion			= '';
		$this->HorasEstimadas 		= '';	
		$this->IdTareaTrabajo		= '';
		$this->IdTipoVenta			= '';
		$this->IdEstado				= '';
		$this->IdCodigoTrabajo		= '';
		$this->IdFacturaCompra		= '';
		$this->Tarea				= '';
		$this->IdCategoria			= '';
		$this->Agrupar				= '';
		$this->TotalMO				= '';
		$this->TotalRepuestos		= '';
		$this->IdVendedor			= '';
		$this->IdProveedor			= '';
		$this->CostoTotal			= '';
		$this->Terceros				= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdOrdenTrabajoTarea	= $arr['IdOrdenTrabajoTarea'];
		$this->IdOrdenTrabajo		= $arr['IdOrdenTrabajo'];
		$this->Importe		 		= $arr['Importe'];
		$this->Titulo 				= $arr['Titulo'];
		$this->Descripcion			= $arr['Descripcion'];
		$this->HorasEstimadas 		= $arr['HorasEstimadas'];
		$this->IdTareaTrabajo		= $arr['IdTareaTrabajo'];
		$this->IdTipoVenta			= $arr['IdTipoVenta'];
		$this->IdEstado				= $arr['IdEstado'];
		$this->IdCodigoTrabajo		= $arr['IdCodigoTrabajo'];
		$this->IdFacturaCompra		= $arr['IdFacturaCompra'];
		$this->Tarea				= $arr['Tarea'];
		$this->IdCategoria			= $arr['IdCategoria'];
		$this->Agrupar				= $arr['Agrupar'];
		$this->TotalMO				= $arr['TotalMO'];
		$this->TotalRepuestos		= $arr['TotalRepuestos'];
		$this->IdVendedor			= $arr['IdVendedor'];
		$this->IdProveedor			= $arr['IdProveedor'];
		$this->CostoTotal			= $arr['CostoTotal'];
		$this->Terceros				= $arr['Terceros'];
	}
	
	public function GetAllArticulos()
	{
			
		$oOrdenesTrabajoTareasArticulos = new OrdenesTrabajoTareasArticulos();		
		
		return $oOrdenesTrabajoTareasArticulos->GetAllByOrdenTrabajoTarea($this);
	}
	
	public function CantidadRepuestosAsignados()
	{		
		$oCompras = new Compras();		
		
		$arrCompras = $oCompras->GetByOrdenTrabajoTarea($this);
		
		$total = 0;
		foreach ($arrCompras as $oCompra)
		{			
			if ($oCompra->IdTipoMovimiento == TipoMovimiento::Devolucion)
				$total -= $oCompra->CantidadRepuestos();
			else
				$total += $oCompra->CantidadRepuestos();
		}
		return $total;
	}
	
	public function ImporteRepuestos()
	{
		if ($this->Agrupar)
			return $this->TotalRepuestos;
			
		$oOrdenesTrabajoTareasArticulos = new OrdenesTrabajoTareasArticulos();		
		
		$arrRelacionados = $oOrdenesTrabajoTareasArticulos->GetAllByOrdenTrabajoTarea($this);
		
		$total = 0;
		foreach ($arrRelacionados as $oRelacion)
		{			
			$total += $oRelacion->PrecioTotal;
		}
		return number_format($total, 2);
	}
	
	public function ImporteSinRepuestos()
	{
		if ($this->Agrupar)
			return $this->TotalMO;
			
		$ImporteSinRepuestos = $this->Importe;
		$ImporteSinRepuestos-= $this->ImporteRepuestosReal();
		
		return $ImporteSinRepuestos;
	}
	
	public function ImporteRepuestosReal()
	{
		if ($this->Agrupar)
			return $this->TotalRepuestos;
			
		$oCompras = new Compras();		
		
		$arrCompras = $oCompras->GetByOrdenTrabajoTarea($this);
		
		$total = 0;
		foreach ($arrCompras as $oCompra)
		{			
			$oCompra->LoadAllDetalles();
			if ($oCompra->IdTipoMovimiento == TipoMovimiento::Devolucion)
				$total -= $oCompra->Total();
			else
				$total += $oCompra->Total();
		}
		return $total;
	}
	
	public function CostoRepuestosReal()
	{
		$oCompras = new Compras();		
		
		$arrCompras = $oCompras->GetByOrdenTrabajoTarea($this);
		
		$total = 0;
		foreach ($arrCompras as $oCompra)
		{			
			$oCompra->LoadAllDetalles();
			
			if ($oCompra->IdTipoMovimiento == TipoMovimiento::Devolucion)
				$total -= $oCompra->Costo();
			else
				$total += $oCompra->Costo();
		}
		return $total;
	}
	
	public function ImporteTotalReal()
	{
		return $this->ImporteRepuestosReal() + $this->ImporteSinRepuestos();
	}
}

?>