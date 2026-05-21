<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.articulos.php');
require_once('class.ivas.php');

class CompraDetalle extends DBAccess 
{
	public $IdCompraDetalle;
	public $IdCompra;
	public $IdArticulo;
	public $ImporteUnidad;	
	public $Cantidad;	
	public $ImporteCompraNeto;	
	public $PrecioCompra;
	public $CantidadTotal;

	public function __construc()
	{
		$this->IdCompraDetalle		= '';
		$this->IdCompra				= '';
		$this->IdArticulo 			= '';
		$this->ImporteUnidad		= '';
		$this->Cantidad 			= '';		
		$this->ImporteCompraNeto 	= '';
		$this->PrecioCompra		 	= '';
		$this->CantidadTotal	 	= '';
	}

	
	public function ParseFromArray(array $arr)
	{
		$this->IdCompraDetalle		= $arr['IdCompraDetalle'];
		$this->IdCompra				= $arr['IdCompra'];
		$this->IdArticulo 			= $arr['IdArticulo'];
		$this->ImporteUnidad 		= $arr['ImporteUnidad'];
		$this->Cantidad 			= $arr['Cantidad'];
		$this->ImporteCompraNeto 	= $arr['ImporteCompraNeto'];
		$this->PrecioCompra 		= $arr['PrecioCompra'];
		$this->CantidadTotal 		= $arr['CantidadTotal'];
	}
	
	public function GetSubtotalSinIva()
	{
		$oArticulos = new Articulos();
		$oIvas = new Ivas();		
		$oArticulo 	= $oArticulos->GetById($this->IdArticulo);
		$oIva		= $oIvas->GetById($oArticulo->IdIva);
		$div 		= $oIva->Alicuota + 1;
		return number_format((($this->ImporteCompraNeto / $div) / $this->Cantidad), 2);		
	}
	
	public function GetSubtotal()
	{
		$oArticulos = new Articulos();
		$oIvas = new Ivas();		
		$oArticulo 	= $oArticulos->GetById($this->IdArticulo);
		$oIva		= $oIvas->GetById($oArticulo->IdIva);
		$div 		= $oIva->Alicuota + 1;
		return number_format(($this->ImporteCompraNeto / $div), 2);		
	}
	
	public function GetSubtotalIva($IdIva)
	{
		$oArticulos = new Articulos();
		$oIvas = new Ivas();
		$oIva = $oIvas->GetById($IdIva);	
		$oArticulo 	= $oArticulos->GetById($this->IdArticulo);
		if ($oIva->IdIva == $oArticulo->IdIva)
		{			
			$div 		= $oIva->Alicuota + 1;
			return number_format(($this->ImporteCompraNeto - ($this->ImporteCompraNeto / $div)), 2);			
		}
		else
			return 0;
	}
	
	public function GetTotalIva()
	{
		$oArticulos = new Articulos();
		$oIvas = new Ivas();
		
		$oArticulo 	= $oArticulos->GetById($this->IdArticulo);
		$oIva = $oIvas->GetById($oArticulo->IdIva);
		
		$div 		= $oIva->Alicuota + 1;
		return number_format(($this->ImporteCompraNeto - ($this->ImporteCompraNeto / $div)), 2);
	}
	
	public function GetUnitarioIva($IdIva)
	{
		$oArticulos = new Articulos();
		$oIvas = new Ivas();
		$oIva = $oIvas->GetById($IdIva);	
		$oArticulo 	= $oArticulos->GetById($this->IdArticulo);
		if ($oIva->IdIva == $oArticulo->IdIva)
		{			
			$div 		= $oIva->Alicuota + 1;
			return number_format(($this->ImporteUnidad - ($this->ImporteUnidad / $div)), 2);			
		}
		else
			return 0;
	}
}

?>
