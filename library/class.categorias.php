<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Categorias
{
	const Taller	 		= 1;
	const ChapaYPintura	 	= 2;
	const PreEntrega	 	= 3;
	const Accesorios	 	= 4;
	const Mantenimiento	 	= 5;
	
	public static function GetAll()
	{
		$arr = array(
			array(
				'IdCategoria' => Categorias::Taller,
				'Nombre' => 'Reparaci&oacute;n',
				'NombreColumna' => 'Taller'
			),
			array(
				'IdCategoria' => Categorias::Mantenimiento, 
				'Nombre' => 'Mantenimiento',
				'NombreColumna' => 'Mantenimiento'
			),
			array(
				'IdCategoria' => Categorias::PreEntrega, 
				'Nombre' => 'PreEntrega',
				'NombreColumna' => 'PreEntrega'
			),
			array(
				'IdCategoria' => Categorias::Accesorios, 
				'Nombre' => 'Accesorios',
				'NombreColumna' => 'Accesorios'
			),
				array(
				'IdCategoria' => Categorias::ChapaYPintura,
				'Nombre' => 'Chapa y Pintura',
				'NombreColumna' => 'Chapa'
			)
		);
		return $arr;
	}
	
	public static function GetById($IdCategoria)
	{
		foreach (Categorias::GetAll() as $oCategoria)
		{
			if ($IdCategoria == $oCategoria['IdCategoria'])
				return $oCategoria;
		}
		return false;
	}
}

?>