<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class SectoresPostVenta
{
	const Mostrador			= 1;
	const Taller		 	= 2;
	const ChapaYPintura	 	= 3;
	const Preentrega	 	= 4;
	const Stock			 	= 5;
	const Zarate			= 6;
	
	public static function GetAll()
	{
		$arr = array(
			array('IdSectorPostVenta' => SectoresPostVenta::Taller, 'Nombre' => 'Taller'),
			array('IdSectorPostVenta' => SectoresPostVenta::ChapaYPintura, 'Nombre' => 'Chapa y Pintura'),
			array('IdSectorPostVenta' => SectoresPostVenta::Preentrega, 'Nombre' => 'Pre-entrega'),
			array('IdSectorPostVenta' => SectoresPostVenta::Mostrador, 'Nombre' => 'Mostrador'),
			array('IdSectorPostVenta' => SectoresPostVenta::Stock, 'Nombre' => 'Stock'),
			array('IdSectorPostVenta' => SectoresPostVenta::Zarate, 'Nombre' => 'Zarate')
		);
		return $arr;
	}
	
	public static function GetById($IdSectorPostVenta)
	{
		foreach (SectoresPostVenta::GetAll() as $oSectorPostVenta)
		{
			if ($IdSectorPostVenta == $oSectorPostVenta['IdSectorPostVenta'])
				return $oSectorPostVenta;
		}
		return false;
	}
}

?>