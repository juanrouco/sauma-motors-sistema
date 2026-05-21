<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class OrigenesCliente
{
	const Existente			= 1;
	const MercadoLibre		= 2;
	const OLX				= 3;
	const Diario		 	= 4;
	const TV				= 5;
	const Radio				= 6;
	const Salon				= 7;
	const Facebook		 	= 8;
	const Instagram		 	= 9;
	const Twitter		 	= 10;
	const Referido 			= 11;
	
	public static function GetAll()
	{
		$arr = array(
			array('IdOrigenCliente' => OrigenesCliente::Existente, 'Nombre' => 'Cliente Existente'),
			array('IdOrigenCliente' => OrigenesCliente::MercadoLibre, 'Nombre' => 'Mercado Libre'),
			array('IdOrigenCliente' => OrigenesCliente::OLX, 'Nombre' => 'OLX'),
			array('IdOrigenCliente' => OrigenesCliente::Diario, 'Nombre' => 'Diario'),
			array('IdOrigenCliente' => OrigenesCliente::TV, 'Nombre' => 'TV'),
			array('IdOrigenCliente' => OrigenesCliente::Radio, 'Nombre' => 'Radio'),
			array('IdOrigenCliente' => OrigenesCliente::Salon, 'Nombre' => 'Sal&oacute;n'),
			array('IdOrigenCliente' => OrigenesCliente::Facebook, 'Nombre' => 'Facebook'),
			array('IdOrigenCliente' => OrigenesCliente::Instagram, 'Nombre' => 'Instagram'),
			array('IdOrigenCliente' => OrigenesCliente::Twitter, 'Nombre' => 'Twitter'),
			array('IdOrigenCliente' => OrigenesCliente::Referido, 'Nombre' => 'Referido')
		);
		return $arr;
	}
	
	public static function GetById($IdOrigenesCliente)
	{
		foreach (OrigenesCliente::GetAll() as $oOrigenesCliente)
		{
			if ($IdOrigenesCliente == $oOrigenesCliente['IdOrigenCliente'])
				return $oOrigenesCliente['Nombre'];
		}
		return false;
	}
}

?>