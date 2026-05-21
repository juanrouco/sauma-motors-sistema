<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class TipoMovimiento
{
	const Venta			= 1;
	const Devolucion 	= 2;
	
	public static function GetAll()
	{
		$arr = array(
			array('IdTipoVenta' => TipoMovimiento::Venta, 'Nombre' => 'Venta'),
			array('IdTipoVenta' => TipoMovimiento::Devolucion, 'Nombre' => 'Devolucion')
		);
		return $arr;
	}
	
	
	
	public static function GetById($IdTipoMovimiento)
	{
		foreach (TipoMovimiento::GetAll() as $oTipoMovimiento)
		{
			if ($IdTipoMovimiento == $oTipoMovimiento['IdTipoMovimiento'])
				return $oTipoMovimiento;
		}
		return false;
	}
}

?>