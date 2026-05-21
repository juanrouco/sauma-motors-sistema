<?php

class OrdenTrabajoImagen
{
	public $IdImagen;
	public $IdOrdenTrabajo;
	public $Imagen;
	public $Epigrafe;
	public $Orden;
	
	public function ParseFromArray(array $arr)
	{
		$this->IdImagen 	= stripslashes($arr['IdImagen']);
		$this->IdOrdenTrabajo 	= stripslashes($arr['IdOrdenTrabajo']);
		$this->Imagen 		= stripslashes($arr['Imagen']);
		$this->Epigrafe 	= stripslashes($arr['Epigrafe']);
		$this->Orden	 	= stripslashes($arr['Orden']);
	}
}

?>