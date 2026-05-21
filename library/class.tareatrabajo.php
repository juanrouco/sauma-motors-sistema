<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.tareastrabajoarticulos.php');
require_once('class.articulos.php');
require_once('class.ivas.php');
require_once('class.costosmanoobra.php');
require_once('class.costosmanoobra.php');
require_once('class.tipocosto.php');

class TareaTrabajo
{
	public $IdTareaTrabajo;
	public $Modelo;
	public $AnioDesde;
	public $AnioHasta;
	public $Importe;
	public $Titulo;
	public $Descripcion;
	public $HorasEstimadas;
	public $TotalImporte;
	public $IdTipoCosto;
	public $IdCodigoTrabajo;
	public $IdModeloPV;
	
	public function __construct()
	{
		$this->IdTareaTrabajo 		= '';
		$this->Modelo				= '';
		$this->AnioDesde 			= '';
		$this->AnioHasta 			= '';
		$this->Importe			 	= '';
		$this->Titulo			 	= '';
		$this->Descripcion			= '';
		$this->HorasEstimadas 		= '';
		$this->IdTipoCosto 			= '';
		$this->IdCodigoTrabajo		= '';
		$this->IdModeloPV			= '';
		$this->IdService			= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdTareaTrabajo 		= $arr['IdTareaTrabajo'];
		$this->Modelo				= $arr['Modelo'];
		$this->AnioDesde 			= $arr['AnioDesde'];
		$this->AnioHasta			= $arr['AnioHasta'];
		$this->Importe		 		= $arr['Importe'];
		$this->Titulo 				= $arr['Titulo'];
		$this->Descripcion			= $arr['Descripcion'];
		$this->HorasEstimadas 		= $arr['HorasEstimadas'];
		$this->IdTipoCosto			= $arr['IdTipoCosto'];
		$this->IdCodigoTrabajo		= $arr['IdCodigoTrabajo'];
		$this->IdModeloPV			= $arr['IdModeloPV'];
		$this->IdService			= $arr['IdService'];
	}
	
	public function ImporteRepuestos()
	{
		$oTareasTrabajoArticulos = new TareasTrabajoArticulos();
		$oArticulos = new Articulos();
		$oIvas = new Ivas();
		
		$arrRelacionados = $oTareasTrabajoArticulos->GetAllByTareaTrabajo($this);
		
		$total = 0;
		foreach ($arrRelacionados as $oRelacion)
		{
			$oArticulo = $oArticulos->GetById($oRelacion->IdArticulo);
			$oIva = $oIvas->GetById($oArticulo->IdIva);
			$total += $oArticulo->PrecioLista * (1 + $oIva->Alicuota) * $oRelacion->Cantidad;
		}
		return number_format($total, 2);
	}
	
	public function ImporteManoObra()
	{
		$oCostosManoObra = new CostosManoObra();
		return ($this->HorasEstimadas * $oCostosManoObra->GetLast());
	}
	
	public function ImporteTotal()
	{
		if ($this->IdTipoCosto == TipoCosto::CostoFijo)
			return $this->Importe;		
		return $this->ImporteRepuestos() + ($this->ImporteManoObra());
	}
}

?>