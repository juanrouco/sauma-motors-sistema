<?php

class ModeloImagen
{
	public $IdImagen;
	public $IdModelo;
	public $Imagen;
	public $Epigrafe;
	
	public function ParseFromArray(array $arr)
	{
		$this->IdImagen 	= stripslashes($arr['IdImagen']);
		$this->IdModelo 	= stripslashes($arr['IdModelo']);
		$this->Imagen 		= stripslashes($arr['Imagen']);
		$this->Epigrafe 	= stripslashes($arr['Epigrafe']);
	}
}

?>