<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class TipoVenta
{
	const Mostrador			= 0;
	const OrdenReparacion 	= 2;
	const VentaInterna 		= 1;
	const Garantia		 	= 3;
	const PreEntrega	 	= 4;
	const ChapaYPintura	 	= 5;
	const Accesorios	 	= 6;
	const DaniosYFaltantes 	= 7;
	
	public static function GetAll()
	{
		$arr = array(
			array('IdTipoVenta' => TipoVenta::Mostrador, 'Nombre' => 'Venta en Mostrador'),
			array('IdTipoVenta' => TipoVenta::OrdenReparacion, 'Nombre' => 'Cliente'),
			array('IdTipoVenta' => TipoVenta::VentaInterna, 'Nombre' => 'Cargo Interno'),
			array('IdTipoVenta' => TipoVenta::Garantia, 'Nombre' => 'Garant&iacute;a'),
			array('IdTipoVenta' => TipoVenta::ChapaYPintura, 'Nombre' => 'Chapa y Pintura'),
			array('IdTipoVenta' => TipoVenta::Accesorios, 'Nombre' => 'Accesorios'),
			array('IdTipoVenta' => TipoVenta::DaniosYFaltantes, 'Nombre' => 'Da&ntilde;os y Faltantes')
		);
		return $arr;
	}
	
	public static function GetAllVenta()
	{
		$arr = array(
			array('IdTipoVenta' => TipoVenta::Mostrador, 'Nombre' => 'Venta en Mostrador'),
			array('IdTipoVenta' => TipoVenta::OrdenReparacion, 'Nombre' => 'Orden de Trabajo')
		);
		return $arr;
	}
	
	public static function GetAllGM()
	{
		$arr = array(
			array('IdTipoVenta' => TipoVenta::Garantia, 'Nombre' => 'Garant&iacute;a'),
			array('IdTipoVenta' => TipoVenta::DaniosYFaltantes, 'Nombre' => 'Da&ntilde;os y Faltantes')
		);
		return $arr;
	}
	
	public static function GetAllOrdenTrabajo()
	{
		$arr = array(
			array('IdTipoVenta' => TipoVenta::OrdenReparacion, 'Nombre' => 'Cliente'),
			array('IdTipoVenta' => TipoVenta::VentaInterna, 'Nombre' => 'Cargo Interno'),
			array('IdTipoVenta' => TipoVenta::Garantia, 'Nombre' => 'Garant&iacute;a'),
			array('IdTipoVenta' => TipoVenta::DaniosYFaltantes, 'Nombre' => 'Da&ntilde;os y Faltantes')/*,
			array('IdTipoVenta' => TipoVenta::PreEntrega, 'Nombre' => 'Preentrega'),
			array('IdTipoVenta' => TipoVenta::ChapaYPintura, 'Nombre' => 'Chapa y Pintura'),
			array('IdTipoVenta' => TipoVenta::Accesorios, 'Nombre' => 'Accesorios')*/
		);
		return $arr;
	}
	
	public static function GetAllPedidosRepuestos()
	{
		$arr = array(
			array('IdTipoVenta' => TipoVenta::OrdenReparacion, 'Nombre' => 'Cliente'),
			array('IdTipoVenta' => TipoVenta::Garantia, 'Nombre' => 'Garant&iacute;a'),
			array('IdTipoVenta' => TipoVenta::VentaInterna, 'Nombre' => 'Interno')
		);
		return $arr;
	}
	
	public static function GetById($IdTipoVenta)
	{
		foreach (TipoVenta::GetAll() as $oTipoVenta)
		{
			if ($IdTipoVenta == $oTipoVenta['IdTipoVenta'])
				return $oTipoVenta;
		}
		return false;
	}
	
	public static function GetByIdOrdenTrabajo($IdTipoVenta)
	{
		foreach (TipoVenta::GetAllOrdenTrabajo() as $oTipoVenta)
		{
			if ($IdTipoVenta == $oTipoVenta['IdTipoVenta'])
				return $oTipoVenta;
		}
		return false;
	}
}

?>