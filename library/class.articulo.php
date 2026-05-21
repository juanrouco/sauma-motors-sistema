<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.articulostocks.php');

class Articulo
{
	public $IdArticulo;
	public $Codigo;
	public $Descripcion;
	public $Reemplazo;
	public $PrecioCompra;
	public $PrecioLista;
	public $PrecioOferta;
	public $PrecioTerceros;
	public $IdProveedor;
	public $UnidadVenta;
	public $ClasePieza;
	public $StockMaximo;
	public $StockMinimo;	
	public $Utilidad;
	public $IdIva;
	public $DescCod;
	public $CodDes;
	public $Stocks;
	
	const PathCsvImportBack	= '../_recursos/articulos/';

	public function __construct()
	{
		$this->Stocks = Array();
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdArticulo 			= $arr['IdArticulo'];
		$this->Codigo 				= stripslashes($arr['Codigo']);
		$this->Descripcion 			= htmlentities(stripslashes($arr['Descripcion']));
		$this->Reemplazo 			= stripslashes($arr['Reemplazo']);
		$this->PrecioCompra			= $arr['PrecioCompra'];
		$this->PrecioLista			= $arr['PrecioLista'];
		$this->PrecioOferta			= $arr['PrecioOferta'];
		$this->PrecioTerceros		= $arr['PrecioTerceros'];
		$this->IdProveedor			= $arr['IdProveedor'];
		$this->UnidadVenta			= $arr['UnidadVenta'];
		$this->ClasePieza 			= stripslashes($arr['ClasePieza']);
		$this->StockMaximo			= $arr['StockMaximo'];
		$this->StockMinimo			= $arr['StockMinimo'];
		$this->Utilidad 			= $arr['Utilidad'];	
		$this->IdIva				= $arr['IdIva'];
		$this->DescCod				= $arr['DescCod'];
		$this->CodDes				= $arr['CodDes'];
		$this->LoadStocks();
	}
	
	private function LoadStocks()
	{
		$ArticuloStocks = new ArticuloStocks();
		$this->Stocks = $ArticuloStocks->GetAllByArticulo($this);
	}
	
	public function StockTotal()
	{
		$cantidad = 0;
		foreach($this->Stocks as $stock)
		{
			$cantidad += $stock->StockActual;
		}
		return $cantidad;
	}	
}

?>