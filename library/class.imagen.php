<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Imagen
{
	const PathImageBigBack	= '../_recursos/imagenes/big/';
	const PathImageBigFront	= '_recursos/imagenes/big/';
	const PathImageThumbBack	= '../_recursos/imagenes/thumb/';
	const PathImageThumbFront	= '_recursos/imagenes/thumb/';

	public $IdImagen;
	public $Imagen;
	public $Url;

	
	public function __construct()
	{
		$this->IdImagen	= '';
		$this->Imagen 	= '';
		$this->Url		= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdImagen	= $arr['IdImagen'];
		$this->Imagen 	= $arr['Imagen'];
		$this->Url		= $arr['Url'];
	}
}

?>